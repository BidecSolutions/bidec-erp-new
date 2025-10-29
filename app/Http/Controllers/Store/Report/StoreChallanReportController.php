<?php

namespace App\Http\Controllers\Store\Report;

use App\Helpers\CommonFacades;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use App\Exports\ExportStoreChallanItemWiseReport;
use App\Exports\ExportStockTransferItemAndTypeWiseReport;
use Maatwebsite\Excel\Facades\Excel;

class StoreChallanReportController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','MultiDB']);
    }
    public function storeChallanItemWiseReport(Request $request)
    {
        // dd('in StoreChallanReportController');
        // $checkPermission =  CommonFacades::checkUserPermissionForSingleOption(CommonFacades::getSessionCompanyId(),Auth::user()->id,Auth::user()->emp_id,$_GET['pageType'],$_GET['parentCode'],'Store',Auth::user()->acc_type);
        // if($checkPermission != 1) {
        //     return view('dontPermissionForPage');
        // }
        
        
        return view('Store.Report.storeChallanItemWiseReport');
    }

    public function filterStoreChallanItemWiseReport()
    {
        // dd('dsada');
        $fromDate = date("Y-m-d", strtotime(Input::get('fromDate')));
        $toDate = date("Y-m-d", strtotime(Input::get('toDate')));
        $m = CommonFacades::getSessionCompanyId();

        $selectVoucherStatus = Input::get('selectVoucherStatus');
        $startRecordNo = Input::get('startRecordNo');
        $endRecordNo = Input::get('endRecordNo');
        $filterLocationId = Input::get('filterLocationId');
        $filterSubDepartmentId = Input::get('filterSubDepartmentId');
        
        $filterPojectId = Input::get('filterProjectId');

        if($selectVoucherStatus == 0){
            $statusContion = '';
        }else if($selectVoucherStatus == 1){
            $statusContion = ' and store_challan.status = 1 and store_challan.store_challan_status = 1';
        }else if($selectVoucherStatus == 2){
            $statusContion = ' and store_challan.status = 1 and store_challan.store_challan_status = 2';
        }else if($selectVoucherStatus == 3){
            $statusContion = ' and store_challan.status = 2';
        }

        $params['fromDate'] = $fromDate;
        $params['toDate'] = $toDate;
        $params['selectVoucherStatus'] = $selectVoucherStatus;

        $countStoreChallanVoucherList = DB::connection('tenant')->select("select 
            store_challan_data.id,
            store_challan_data.accounting_year,
            store_challan_data.company_id,
            store_challan.material_request_no,
            store_challan.material_request_date,
            store_challan_data.store_challan_no,
            store_challan_data.store_challan_date,
            store_challan_data.issue_qty,
            store_challan_data.receive_qty,
            store_challan.description,
            store_challan_data.store_challan_status,
            store_challan_data.status,
            store_challan_data.date,
            store_challan_data.time,
            store_challan.username,
            store_challan_data.user_id,
            store_challan_data.approve_username,
            store_challan_data.approve_date,
            store_challan_data.approve_time,
            store_challan_data.delete_username,
            store_challan.location_id,
            store_challan.project_id,
            store_challan.department_id,
            store_challan.sub_department_id,
            subitem.sub_ic
        from store_challan_data
        INNER JOIN store_challan ON store_challan_data.store_challan_no = store_challan.store_challan_no
        INNER JOIN subitem ON store_challan_data.sub_item_id = subitem.id
        where store_challan.company_id = ".$m." and 
        store_challan.accounting_year = ".Session::get('accountYear')." and 
        store_challan.store_challan_date between '".$fromDate."' and '".$toDate."'".$statusContion." order by store_challan.id desc");
        
        $filterStoreChallanVoucherList = DB::connection('tenant')->select("select 
            store_challan_data.id,
            store_challan_data.accounting_year,
            store_challan_data.company_id,
            store_challan.material_request_no,
            store_challan.material_request_date,
            store_challan_data.store_challan_no,
            store_challan_data.store_challan_date,
            store_challan_data.issue_qty,
            store_challan_data.receive_qty,
            store_challan.description,
            store_challan_data.store_challan_status,
            store_challan_data.status,
            store_challan_data.date,
            store_challan_data.time,
            store_challan.username,
            store_challan_data.user_id,
            store_challan_data.approve_username,
            store_challan_data.approve_date,
            store_challan_data.approve_time,
            store_challan_data.delete_username,
            store_challan.location_id,
            store_challan.project_id,
            store_challan.department_id,
            store_challan.sub_department_id,
            subitem.sub_ic
        from store_challan_data
        INNER JOIN store_challan ON store_challan_data.store_challan_no = store_challan.store_challan_no
        INNER JOIN subitem ON store_challan_data.sub_item_id = subitem.id
        where store_challan.company_id = ".$m." and 
        store_challan.accounting_year = ".Session::get('accountYear')." and 
        store_challan.store_challan_date between '".$fromDate."' and '".$toDate."'".$statusContion." order by store_challan.id desc LIMIT  ".$startRecordNo.",".$endRecordNo."");
        // dd($filterStoreChallanVoucherList);
        return view('Store.Report.Ajax.filterStoreChallanVoucherList',compact('filterStoreChallanVoucherList','countStoreChallanVoucherList','params'));
    }

    public function ExportStoreChallanItemWiseReport(Request $request){
        $export = new ExportStoreChallanItemWiseReport($request->fromDate, $request->toDate, $request->selectVoucherStatus);
        return Excel::download($export, 'ExportStoreChallanItemWiseReport.xlsx');
    }

    public function ExportStockTransferItemAndTypeWiseReport(Request $request){
        $export = new ExportStockTransferItemAndTypeWiseReport($request->fromDate, $request->toDate, $request->voucherType, $request->recordType);
        return Excel::download($export, 'ExportStockTransferItemAndTypeWiseReport.xlsx');
    }

    

    public function stockTransferItemAndTypeWiseReport(Request $request){
        return view('Store.Report.stockTransferItemAndTypeWiseReport');
    }

    public function filterStockTransferItemAndTypeWiseReport(Request $request){
        $fromDate = date("Y-m-d", strtotime(Input::get('fromDate')));
        $toDate = date("Y-m-d", strtotime(Input::get('toDate')));
        $m = CommonFacades::getSessionCompanyId();
        $voucherType = Input::get('voucherType');
        $startRecordNo = Input::get('startRecordNo');
        $endRecordNo = Input::get('endRecordNo');
        $recordType = Input::get('recordType');

        

        $params['fromDate'] = $fromDate;
        $params['toDate'] = $toDate;
        $params['voucherType'] = $voucherType;
        $params['recordType'] = $recordType;

        if($recordType == 1){
            $baseTableCondition = 'stock_transfers as st';
            $joiningTableCondition = 'INNER JOIN stock_transfer_datas as std ON std.master_id = st.id';
            $groupByCondition = ' group by st.tr_no';

        }else{
            $baseTableCondition = 'stock_transfer_datas as std';
            $joiningTableCondition = 'INNER JOIN stock_transfers as st ON st.id = std.master_id';
            $groupByCondition = '';
        }

        if($voucherType == 0){
            $statusContion = '';
        }else if($voucherType == 1){
            $statusContion = ' and st.type = 1';
        }else if($voucherType == 2){
            $statusContion = ' and st.type = 2';
        }

        $countStockTransferVoucherList = DB::connection('tenant')->select("select 
            st.id as stock_transfer_id,
            st.tr_no,
            st.tr_date,
            st.description,
            st.type,
            std.desc,
            std.item_id,
            std.iot,
            std.warehouse_from,
            std.warehouse_to,
            std.batch_code,
            std.qty,
            std.rate,
            std.amount,
            subitem.sub_ic
        from ".$baseTableCondition."
        ".$joiningTableCondition."
        INNER JOIN subitem ON std.item_id = subitem.id
        where st.company_id = ".$m." and 
        st.tr_date between '".$fromDate."' and '".$toDate."'".$statusContion."".$groupByCondition."");

        $filterStockTransferVoucherList = DB::connection('tenant')->select("select 
            st.id as stock_transfer_id,
            st.tr_no,
            st.tr_date,
            st.description,
            st.type,
            std.desc,
            std.item_id,
            std.iot,
            std.warehouse_from,
            std.warehouse_to,
            std.batch_code,
            std.qty,
            std.rate,
            std.amount,
            subitem.sub_ic
        from ".$baseTableCondition."
        ".$joiningTableCondition."
        INNER JOIN subitem ON std.item_id = subitem.id
        where st.company_id = ".$m." and 
        st.tr_date between '".$fromDate."' and '".$toDate."'".$statusContion."".$groupByCondition." order by st.id desc LIMIT  ".$startRecordNo.",".$endRecordNo."");

        return view('Store.Report.Ajax.filterStockTransferItemAndTypeWiseReport',compact('countStockTransferVoucherList','filterStockTransferVoucherList','params'));
    }
}
