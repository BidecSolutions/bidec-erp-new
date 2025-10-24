<?php

namespace App\Http\Controllers;
use App\Helpers\CommonHelper;
use App\Models\Paras;
use Illuminate\Http\Request;
use Session;

use App\Repositories\Interfaces\ParaRepositoryInterface;

class ParaController extends Controller
{
    private $paraRepository;

    public function __construct(ParaRepositoryInterface $paraRepository)
    {
        $this->paraRepository = $paraRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->ajax()){
            $paras =  $this->paraRepository->allParas($request->all());

            return view('paras.indexAjax', compact('paras'));
        }
        return view('paras.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('paras.create');
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
            'para_name' => 'required|string|max:255'
        ]);

        $this->paraRepository->storePara($data);

        return redirect()->route('paras.index')->with('message', 'Para Created Successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Paras  $para
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        echo $id;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Paras  $para
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $para = $this->paraRepository->findPara($id);

        return view('paras.edit', compact('para'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Paras  $para
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'para_name' => 'required|string|max:255'
        ]);

        $this->paraRepository->updatePara($data, $id);

        return redirect()->route('paras.index')->with('message', 'Para Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Paras  $para
     * @return \Illuminate\Http\Response
     */
    public function destroy($id){
        $this->paraRepository->changeParaStatus($id,2);
        return response()->json(['success' => 'Inactive Successfully!']);
    }

    public function changeInactiveToActiveRecord($id){
        $this->paraRepository->changeParaStatus($id,1);
        return response()->json(['success' => 'Active Successfully!']);
    }

    public function createParaDetailForm(){
        $paraList = Paras::select('paras.id','paras.para_name')
            ->selectRaw('MAX(pod.id) as podId')
            ->selectRaw('MAX(pod.total_lines_in_para) as total_lines_in_para')
            ->selectRaw('MAX(pod.estimated_completion_days) as estimated_completion_days')
            ->selectRaw('MAX(pod.excelent) as excelent')
            ->selectRaw('MAX(pod.good) as good')
            ->selectRaw('MAX(pod.average) as average')
            ->selectRaw('MAX(pod.company_id) as company_id')
            ->leftJoin('para_other_details as pod', function($join) {
                $join->on('paras.id', '=', 'pod.para_id')
                    ->where('pod.company_id', '=', Session::get('company_id'))->where('pod.company_location_id', '=', Session::get('company_location_id'));
            })
            ->groupBy('paras.id','paras.para_name')
            ->get();
        return view('paras.createParaDetailForm',compact('paraList'));
    }

    public function addOtherParaDetail(Request $request){
        $this->paraRepository->storeAddOtherParaDetail($request->all());

        return redirect()->route('viewParasOtherDetailList')->with('message', 'Para Created Successfully');
    }

    public function viewParasOtherDetailList(){
        $parasOtherDetails =  $this->paraRepository->allParasOtherDetails();
        //return response()->json(['success' => 'Deleted Successfully!']);
        return view('paras.viewParasOtherDetailList', compact('parasOtherDetails'));
    }
}
