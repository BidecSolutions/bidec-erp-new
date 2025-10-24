<?php
namespace App\Http\Controllers;
use App\Models\Head;
use Illuminate\Http\Request;
use App\Http\Requests\HeadRequest;
use App\Repositories\Interfaces\HeadRepositoryInterface;
class HeadController extends Controller
{
    private $headRepository;

    public function __construct(HeadRepositoryInterface $headRepository)
    {
        $this->headRepository = $headRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->ajax()){
            $heads =  $this->headRepository->allHeads($request->all());

            return view('heads.indexAjax', compact('heads'));
        }
        return view('heads.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('heads.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(HeadRequest $request)
    {
        $this->headRepository->storeHead($request->all());

        return redirect()->route('heads.index')->with('message', 'Head Created Successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Head  $head
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Head  $head
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $head = $this->headRepository->findHead($id);

        return view('heads.edit', compact('head'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Head  $head
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'head_name' => 'required|string|max:255'
        ]);

        $this->headRepository->updateHead($data, $id);

        return redirect()->route('heads.index')->with('message', 'Head Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Head  $head
     * @return \Illuminate\Http\Response
     */
    public function destroy($id){
        $this->headRepository->changeHeadStatus($id,2);
        return response()->json(['success' => 'Inactive Successfully!']);
    }

    public function changeInactiveToActiveRecord($id){
        $this->headRepository->changeHeadStatus($id,1);
        return response()->json(['success' => 'Active Successfully!']);
    }
}
