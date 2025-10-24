<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use Illuminate\Http\Request;

use App\Repositories\Interfaces\ClassRepositoryInterface;

class ClassController extends Controller
{
    private $classRepository;

    public function __construct(ClassRepositoryInterface $classRepository)
    {
        $this->classRepository = $classRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->ajax()){
            $classes =  $this->classRepository->allClasses($request->all());

            return view('classes.indexAjax', compact('classes'));
        }
        return view('classes.index');

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('classes.create');
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
            'class_name' => 'required|string|max:255',
            'class_no' => 'required',
            'fee_amount' => 'required'
        ]);

        $this->classRepository->storeClass($data);

        return redirect()->route('classes.index')->with('message', 'Class Created Successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Class  $class
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Class  $class
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $class = $this->classRepository->findClass($id);
        return view('classes.edit', compact('class'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Class  $class
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'class_name' => 'required|string|max:255',
            'class_no' => 'required'
        ]);

        $this->classRepository->updateClass($data, $id);

        return redirect()->route('classes.index')->with('message', 'Class Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Class  $class
     * @return \Illuminate\Http\Response
     */
    public function destroy($id){
        $this->classRepository->changeClassStatus($id,2);
        return response()->json(['success' => 'Inactive Successfully!']);
    }

    public function changeInactiveToActiveRecord($id){
        $this->classRepository->changeClassStatus($id,1);
        return response()->json(['success' => 'Active Successfully!']);
    }
}
