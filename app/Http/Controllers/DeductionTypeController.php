<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\DB;
use Session;
class DeductionTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->ajax()){
            $filterStatus = $request->input('filterStatus');
            $deductionTypes =  DB::table('deduction_type')
                ->when($filterStatus != '', function ($q) use ($filterStatus){
                    return $q->where('status','=',$filterStatus);
                })
                ->where('company_id',Session::get('company_id'))
                ->get();

            return view('deduction-type.indexAjax', compact('deductionTypes'));
        }
        return view('deduction-type.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('deduction-type.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'deduction_name' => 'required|string|max:255'
        ]);
        $data['created_date'] = date('Y-m-d');
        $data['status'] = 1;
        $data['created_by'] = Auth::user()->name;
        $data['company_id'] = Session::get('company_id');
        $data['company_location_id'] = Session::get('company_location_id');
        
        DB::table('deduction_type')->insert($data);
        return redirect()->route('deduction-type.index')->with('success', 'Deduction Type Created Successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Country  $country
     * @return \Illuminate\Http\Response
     */

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Country  $country
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $deductionType = DB::table('deduction_type')->where('id',$id)->first();
        return view('deduction-type.edit', compact('deductionType'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Country  $country
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'deduction_name' => 'required|string|max:255'
        ]);

        DB::table('deduction_type')->where('id',$id)->update($data);

        return redirect()->route('deduction-type.index')->with('message', 'Deduction Type Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Country  $country
     * @return \Illuminate\Http\Response
     */
    public function destroy($id){
        $data['status'] = 2;
        DB::table('deduction_type')->where('id',$id)->update($data);
        return response()->json(['success' => 'Inactive Successfully!']);
    }

    public function changeInactiveToActiveRecord($id){
        $data['status'] = 1;
        DB::table('deduction_type')->where('id',$id)->update($data);
        return response()->json(['success' => 'Active Successfully!']);
    }
}
