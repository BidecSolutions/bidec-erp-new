<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;
use Session;
class AllowanceTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->ajax()){
            $filterType = $request->input('filterType');
            $filterStatus = $request->input('filterStatus');
            $allowanceTypes =  DB::table('allowance_type')
                ->when($filterType != '', function ($q) use ($filterType){
                    return $q->where('type','=',$filterType);
                })
                ->when($filterStatus != '', function ($q) use ($filterStatus){
                    return $q->where('status','=',$filterStatus);
                })
                ->where('company_id',Session::get('company_id'))
                ->get();

            return view('allowance-type.indexAjax', compact('allowanceTypes'));
        }
        return view('allowance-type.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('allowance-type.create');
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
            'allowance_name' => 'required|string|max:255',
            'type' => 'required'
        ]);
        $data['created_date'] = date('Y-m-d');
        $data['status'] = 1;
        $data['created_by'] = Auth::user()->name;
        $data['company_id'] = Session::get('company_id');
        $data['company_location_id'] = Session::get('company_location_id');
        
        DB::table('allowance_type')->insert($data);
        return redirect()->route('allowance-type.index')->with('success', 'Allowance Type Created Successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Country  $country
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Country  $country
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $allowanceType = DB::table('allowance_type')->where('id',$id)->first();
        return view('allowance-type.edit', compact('allowanceType'));
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
            'allowance_name' => 'required|string|max:255',
            'type' => 'required'
        ]);
        DB::table('allowance_type')->where('id',$id)->update($data);
        
        return redirect()->route('allowance-type.index')->with('message', 'Allowance Type Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Country  $country
     * @return \Illuminate\Http\Response
     */
    public function destroy($id){
        DB::table('allowance_type')->where('id',$id)->update(['status' => 2]);
        return response()->json(['success' => 'Inactive Successfully!']);
    }

    public function changeInactiveToActiveRecord($id){
        DB::table('allowance_type')->where('id',$id)->update(['status' => 1]);
        return response()->json(['success' => 'Active Successfully!']);
    }
}
