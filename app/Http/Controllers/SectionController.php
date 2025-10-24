<?php

namespace App\Http\Controllers;

use App\Helpers\CommonHelper;
use App\Models\Employee;
use App\Models\Section;
use App\Models\Subject;
use DB;
use Illuminate\Http\Request;

use App\Repositories\Interfaces\SectionRepositoryInterface;
use Illuminate\Support\Facades\Log;
use Session;

class SectionController extends Controller
{
    private $sectionRepository;

    public function __construct(SectionRepositoryInterface $sectionRepository)
    {
        $this->sectionRepository = $sectionRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $sections = $this->sectionRepository->allSections($request->all());

            return view('sections.indexAjax', compact('sections'));
        }
        return view('sections.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('sections.create');
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
            'class_id' => 'required|integer|exists:classes,id',
            'section_name' => 'required|string|max:255',
            'fee_amount' => 'required|numeric'
        ]);

        $this->sectionRepository->storeSection($data);

        return redirect()->route('sections.index')->with('message', 'Section Created Successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Section  $section
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Section  $section
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $section = $this->sectionRepository->findSection($id);

        return view('sections.edit', compact('section'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Section  $section
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'class_id' => 'required|integer|exists:classes,id',
            'section_name' => 'required|string|max:255',
            'fee_amount' => 'required|numeric'
        ]);

        $this->sectionRepository->updateSection($data, $id);

        return redirect()->route('sections.index')->with('message', 'Section Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Section  $section
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->sectionRepository->changeSectionStatus($id, 2);
        return response()->json(['success' => 'Inactive Successfully!']);
    }

    public function changeInactiveToActiveRecord($id)
    {
        $this->sectionRepository->changeSectionStatus($id, 1);
        return response()->json(['success' => 'Active Successfully!']);
    }








}
