<?php

namespace App\Http\Controllers;

use App\Models\ClassTimings;
use Illuminate\Http\Request;

use App\Repositories\Interfaces\ClassTimingRepositoryInterface;

class ClassTimingController extends Controller
{
    private $classTimingRepository;

    public function __construct(ClassTimingRepositoryInterface $classTimingRepository)
    {
        $this->classTimingRepository = $classTimingRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->ajax()){
            $classTimings =  $this->classTimingRepository->allClassTimings($request->all());

            return view('classtimings.indexAjax', compact('classTimings'));
        }
        return view('classtimings.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('classtimings.create');
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
            'name' => 'required|string|max:255'
        ]);

        $this->classTimingRepository->storeClassTiming($data);

        return redirect()->route('classtimings.index')->with('message', 'Class Timing Created Successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ClassTimings  $classTiming
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ClassTimings  $classTiming
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $classTiming = $this->classTimingRepository->findClassTiming($id);

        return view('classtimings.edit', compact('classTiming'));
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
        $request->validate([
            'class_name' => 'required|string|max:255',
            'class_no' => 'required',
            'teacher_id' => 'required'
        ]);

        $this->classTimingRepository->updateClassTiming($request->all(), $id);

        return redirect()->route('classtimings.index')->with('message', 'Class Timing Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ClassTimings  $classTiming
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->classTimingRepository->destroyClassTiming($id);
        return response()->json(['success' => 'Deleted Successfully!']);
        //return redirect()->route('classtimings.index')->with('status', 'Class Timing Delete Successfully');
    }
}
