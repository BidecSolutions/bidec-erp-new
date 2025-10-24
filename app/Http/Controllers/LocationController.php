<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanyLocations;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Log;
use Storage;
use Session;

use App\Repositories\Interfaces\LocationRepositoryInterface;
use Illuminate\Support\Facades\DB;

class LocationController extends Controller
{
    private $locationRepository;

    public function __construct(LocationRepositoryInterface $locationRepository)
    {
        $this->locationRepository = $locationRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $locations = $this->locationRepository->allLocations($request->all());
            return view('locations.indexAjax', compact('locations'));
        }
        return view('locations.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        return view('locations.create');
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
            'email' => 'required|unique:company_locations|max:255',
            'location_code' => 'required',
            'name' => 'required|string|max:255',
            'address' => 'required',
            'phone_no' => 'required',
            'company_id' => 'required|integer|exists:companies,id'
        ]);

        $companyLocation = CompanyLocations::create([
            'email' => $data['email'],
            'location_code' => $data['location_code'],
            'name' => $data['name'],
            'address' => $data['address'],
            'phone_no' => $data['phone_no'],
            'company_id' => $data['company_id'],
        ]);
        return redirect()->route('locations.index')->with('message', 'Campus Created Successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Company  $country
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
        $location = $this->locationRepository->findLocation($id);

        return view('locations.edit', compact('location'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Company  $country
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'email' => 'required|unique:company_locations,email,' . $id . '|max:255',
            'location_code' => 'required',
            'name' => 'required|string|max:255',
            'address' => 'required',
            'phone_no' => 'required',
        ]);

        $companyLocation = CompanyLocations::findOrFail($id);

        $companyLocation->update([
            'email' => $data['email'],
            'location_code' => $data['location_code'],
            'name' => $data['name'],
            'address' => $data['address'],
            'phone_no' => $data['phone_no'],
        ]);

        return redirect()->route('locations.index')->with('message', 'Campus Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Country  $country
     * @return \Illuminate\Http\Response
     */
    public function status($id)
    {
        $this->locationRepository->changeLocationStatus($id, 2);
        return response()->json(['success' => 'Active Successfully!']);
    }

    public function changeInactiveToActiveRecord($id)
    {
        $this->locationRepository->changeLocationStatus($id, 1);
        return response()->json(['success' => 'Active Successfully!']);
    }

    public function loadCompanies(Request $request)
    {
        $filterMadrasaMainScreenId = $request->input('filterMadrasaMainScreenId');
        $accType = $request->input('accType');
        if (empty($filterMadrasaMainScreenId)) {
            if ($accType == 'client') {
                $companiesList = DB::Connection('mysql')->table('companies')->select(['name', 'id', 'dbName', 'company_code', 'registration_no'])->where('status', '=', '1')->get();
            } else if ($accType == 'superadmin' || $accType == 'superuser') {
                $checkCompanyId = Auth::user()->company_id;
                $a = explode("<*>", $checkCompanyId);
                $companiesList = DB::Connection('mysql')->table('companies')->select(['name', 'id', 'dbName', 'company_code', 'registration_no'])->where('status', '=', '1')->whereIn('id', $a)->get();
            }
        } else {
            $companiesList = DB::Connection('mysql')->table('companies')->select(['name', 'id', 'dbName', 'company_code', 'registration_no'])->where('status', '=', '1')->where('id', $filterMadrasaMainScreenId)->get();
        }
        $data = '<ul class="ban-list">';
        foreach ($companiesList as $cRow1) {
            // Sanitize and encode URL parameters
            $companyId = urlencode($cRow1->id);
            $companyName = urlencode($cRow1->name);
            $companyCode = urlencode($cRow1->company_code);
            $url = url("set_user_db_id?company_id={$companyId}&company_name={$companyName}&company_code={$companyCode}");

            // Append list item
            $data .= '<li><div class="banq-box">';
            $data .= '<a href="' . $url . '">';
            $data .= '<span class="companyLetr theme-bg theme-f-m">' . substr($cRow1->name, 0, 1) . '</span>';
            $data .= '<h3 class="item-model-company theme-f-m">' . $cRow1->name . '</h3>';
            $data .= '<h3 class="item-model-company theme-f-m">' . $cRow1->registration_no . '</h3>';
            $data .= '</a></div></li>';
        }
        $data .= '</ul>';
        echo $data;
    }
}
