<?php

namespace App\Http\Controllers\Store\Report;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Fara;
use App\Models\Subitem;
use CommonFacades;
use Input;
use DB;

class StockReportController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','MultiDB']);
    }
    public function stockReportLocationWiseView()
    {
        $subItems = new Subitem();
        $subItems = $subItems->status()->approved()->get();

        return view('Store.Report.stockReportLocationWiseView', compact('subItems'));
    }
    // public function stockReportSingleLocationView(Request $request, $location_id)
    // {
    //     $toDate = $request->toDate ?? '2022-01-01'; //date("Y-m-d", mktime(0, 0, 0, date("m"), 0));
    //     // dd($toDate);
        // $subItems = new Fara();
        // $subItems = $subItems->where('location_id', $location_id)
        // ->status()
        // ->where('date', '<=', $toDate)
        // ->groupBy('sub_ic_id', 'location_id', 'main_ic_id')
        // ->get();

    //     return view('Store.Report.stockReportSingleLocationView', compact('subItems'));
    // }

    public function stockReportSingleLocationView(Request $request, $location_id)
    {
        $toDate = $request->input('toDate', '2025-01-01');
        $m = CommonFacades::getSessionCompanyId();
        $subItems = new Fara();
        $subItems = $subItems->where('location_id', $location_id)
            ->status()
            ->where('company_id', getSessionCompanyId())
            ->where('date', '<=', $toDate)
            ->groupBy('sub_ic_id', 'location_id')
            ->paginate(250); // Using pagination with 10 items per page
    
        if ($request->ajax()) {
            return view('Store.Report.stockReportSingleLocationViewPartial', compact('subItems'))->render();
        }
    
        return view('Store.Report.stockReportSingleLocationViewNew', compact('subItems', 'toDate', 'location_id'));
    }
    
    public function get_stock_location_wise(Request $request){
        // dd($request);
        $subItems = (new Fara)->newQuery();
        if($request->CategoryId != null)            
            $subItems->where('main_ic_id', $request->CategoryId);
        if($request->locationId != null)        
            $subItems->where('location_id', $request->locationId);
        if($request->subItemId != null)        
            $subItems->where('sub_ic_id', $request->subItemId);
        $subItemData = $subItems->status()->groupBy('sub_ic_id', 'location_id', 'main_ic_id')->get();

        return view('Store.Report.Ajax.filterStockReportLocationWise', compact('subItemData'));

    }
    
    public function stockReportInOutView()
    {
        $subItems = new Subitem();
        $subItems = $subItems->status()->approved()->get();
        return view('Store.Report.stockReportInOutView', compact('subItems'));
    }
    public function get_stock_in_out_wise(Request $request){
        // dd($request);
        $toDate =  date('Y-m-t');

        $subItems = (new Fara)->newQuery();
        if($request->subItemId != null)            
            $subItems->where('sub_ic_id', $request->subItemId);
        if($request->locationId != null)        
            $subItems->where('location_id', $request->locationId);

        $subItemData = $subItems->status()->orderBy('date', 'DESC')->get();
        // dd($subItemData, $request->fromDate);
        return view('Store.Report.Ajax.filterStockReportInOutWise', compact('subItemData'));

    }
}
