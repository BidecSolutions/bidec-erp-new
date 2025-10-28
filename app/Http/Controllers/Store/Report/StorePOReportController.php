<?php

namespace App\Http\Controllers\Store\Report;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use App\Exports\ExportPurchaseOrderItemWise;
use Maatwebsite\Excel\Facades\Excel;

class StorePOReportController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','MultiDB']);
    }
    public function purchaseOrderItemWiseReport(Request $request)
    {
        if($request->ajax()){
            
            $fromDate = $request->fromDate ? date('Y-m-d', strtotime($request->fromDate)) : date("Y-m-d", mktime(0, 0, 0, date("m"), 1));
            $toDate = $request->toDate ? date('Y-m-d', strtotime($request->toDate)) : date("Y-m-d", mktime(0, 0, 0, date("m"), 0));
            $m = getSessionCompanyId();
            $filterSupplierId = $request->filterSupplierId ?? '';
            $invoiceNo = $request->invoiceNumber ?? '';
            $selectVoucherStatus = $request->selectVoucherStatus ?? '0';
            $startRecordNo = Input::get('startRecordNo');
		    $endRecordNo = Input::get('endRecordNo');

            $params['fromDate'] = $fromDate;
            $params['toDate'] = $toDate;
            $params['m'] = $m;
            $params['filterSupplierId'] = $filterSupplierId;
            $params['invoiceNo'] = $invoiceNo;
            $params['selectVoucherStatus'] = $selectVoucherStatus;
            $params['startRecordNo'] = $startRecordNo;
            $params['endRecordNo'] = $endRecordNo;

            if($selectVoucherStatus == '0'){
                $statusCondition = '';
                $purchaseOrderCondition = '';
            }else if($selectVoucherStatus == '1'){
                $statusCondition = ' and purchase_order_data.status = 1';
                $purchaseOrderCondition = ' and purchase_order_data.purchase_order_status = 1';
            }else if($selectVoucherStatus == '2'){
                $statusCondition = ' and purchase_order_data.status = 1';
                $purchaseOrderCondition = ' and purchase_order_data.purchase_order_status = 2';
            }else if($selectVoucherStatus == '3'){
                $statusCondition = ' and purchase_order_data.status = 2';
                $purchaseOrderCondition = '';
            }
            
            if(!empty($filterSupplierId)){
                $filterSupplierIdCondition = ' and purchase_order_data.supplier_id = '.$filterSupplierId.'';
            }else{
                $filterSupplierIdCondition = '';
            }
            if(!empty($invoiceNo)){
                $invoiceNoCondition = " and purchase_order.qoutation_no LIKE '".$invoiceNo."'";
            }else{
                $invoiceNoCondition = '';
            }

            $filterPurchaseOrderVoucherList = DB::connection('tenant')->select("SELECT 
                purchase_order.id,
                subitem.sub_ic,
                subitem.item_code,
                purchase_order_data.purchase_order_no,
                purchase_order_data.purchase_order_date,
                purchase_order_data.purchase_request_no,
                purchase_order_data.purchase_request_date,
                purchase_order_data.purchase_order_qty,
                purchase_request_data.qty as pr_qty,
                purchase_order_data.unit_price,
                purchase_order_data.sub_total,
                purchase_order.qoutation_no,
                purchase_order.purchase_order_status,
                purchase_order.status,
                purchase_order.custom_tax_percent,
                location.location_name,
                supplier.name,
                department_name,
                sub_department_name,
                project_name
                FROM purchase_order_data

                INNER JOIN purchase_order ON purchase_order_data.purchase_order_no = purchase_order.purchase_order_no
                LEFT JOIN purchase_request_data ON purchase_order_data.purchase_request_data_record_id = purchase_request_data.id
                INNER JOIN sub_department ON purchase_order.sub_department_id = sub_department.id
                INNER JOIN department ON purchase_order.department_id = department.id 
                INNER JOIN location ON purchase_order.location_id = location.id
                INNER JOIN project ON purchase_order.project_id = project.id
                INNER JOIN supplier ON purchase_order_data.supplier_id = supplier.id
                LEFT JOIN subitem ON purchase_order_data.sub_item_id = subitem.id
                WHERE purchase_order_data.purchase_order_date BETWEEN '".$fromDate."' AND  '".$toDate."' ". $statusCondition ." ". $filterSupplierIdCondition ." ". $invoiceNoCondition ." order by purchase_order.id LIMIT ".$startRecordNo.",".$endRecordNo."");

            $countFilterPurchaseOrderVoucherList = DB::connection('tenant')->select("SELECT 
                purchase_order.id,
                subitem.sub_ic,
                subitem.item_code,
                purchase_order_data.purchase_order_no,
                purchase_order_data.purchase_order_date,
                purchase_order_data.purchase_request_no,
                purchase_order_data.purchase_request_date,
                purchase_order_data.purchase_order_qty,
                purchase_request_data.qty as pr_qty,
                purchase_order_data.unit_price,
                purchase_order_data.sub_total,
                purchase_order.qoutation_no,
                purchase_order.purchase_order_status,
                purchase_order.status,
                purchase_order.custom_tax_percent,
                location.location_name,
                supplier.name,
                department_name,
                sub_department_name,
                project_name
                FROM purchase_order_data

                INNER JOIN purchase_order ON purchase_order_data.purchase_order_no = purchase_order.purchase_order_no
                LEFT JOIN purchase_request_data ON purchase_order_data.purchase_request_data_record_id = purchase_request_data.id
                INNER JOIN sub_department ON purchase_order.sub_department_id = sub_department.id
                INNER JOIN department ON purchase_order.department_id = department.id 
                INNER JOIN location ON purchase_order.location_id = location.id
                INNER JOIN project ON purchase_order.project_id = project.id
                INNER JOIN supplier ON purchase_order_data.supplier_id = supplier.id
                LEFT JOIN subitem ON purchase_order_data.sub_item_id = subitem.id
                WHERE purchase_order_data.purchase_order_date BETWEEN '".$fromDate."' AND  '".$toDate."' ". $statusCondition ." ". $filterSupplierIdCondition ." ". $invoiceNoCondition ." order by purchase_order.id ");
            return view('Store.Report.purchaseOrderItemWiseListAjax', compact('filterPurchaseOrderVoucherList','countFilterPurchaseOrderVoucherList','params'));    
        }
        return view('Store.Report.purchaseOrderItemWiseList');
    }


    // public function invoiceSubmissionReport(Request $request)
    // {
    //     return view('Store.Report.InvoiceSubmissionReport');
    // }

    public function invoiceSubmissionReport(Request $request){
        if($request->ajax()){
            $locationId = $request->input('filterLocationId');
            $subDepartmentId = $request->input('filterSubDepartmentId');
            $projectId = $request->input('filterProjectId');
            $supplierId = $request->input('filterSupplierId');
            $fromDate = date("Y-m-d", strtotime($request->get('fromDate')));
            $toDate = date("Y-m-d", strtotime($request->get('toDate')));
            $startRecordNo = Input::get('startRecordNo');
		    $endRecordNo = Input::get('endRecordNo');
            $payment_status = Input::get('payment_status');
            

            $params['fromDate'] = $fromDate;
            $params['toDate'] = $toDate;
            $params['supplierId'] = $supplierId;
            $params['locationId'] = $locationId;
            $params['subDepartmentId'] = $subDepartmentId;
            $params['projectId'] = $projectId;
            $params['startRecordNo'] = $startRecordNo;
            $params['endRecordNo'] = $endRecordNo;
            $params['payment_status'] = $payment_status;

            $subquery = DB::table('payment_data_against_po')
                ->select('po_id', DB::raw('MAX(id) as max_id'))
                ->groupBy('po_id');

                $query = DB::table('purchase_order as po')
                ->select('po.id', 'po.purchase_order_no','pdap.remarks','pdap.payment_status', 'po.purchase_order_date', 'po.purchase_request_no', 'po.purchase_request_date', 'po.qoutation_no', 'po.qoutation_date', 'po.payment_type_rate', 'po.paymentType', 'po.custom_tax_percent','po.invoice_type','po.batch_no','po.finance_status','po.submited_date', DB::raw('SUM(pdap.amount) as paidAmount'), 's.name as supplierName', 'p.project_name', DB::raw('SUM(pod.sub_total) as invoiceAmount'))
                ->leftJoin(DB::raw("({$subquery->toSql()}) as pdap_max"), function($join) {
                    $join->on('pdap_max.po_id', '=', 'po.id');
                })
                ->leftJoin('payment_data_against_po as pdap', function($join) {
                    $join->on('pdap_max.po_id', '=', 'pdap.po_id');
                    $join->on('pdap_max.max_id', '=', 'pdap.id');
                })
                ->join('supplier as s', 'po.supplier_id', '=', 's.id')
                ->join('project as p', 'po.project_id', '=', 'p.id')
                ->join('purchase_order_data as pod', 'po.purchase_order_no', '=', 'pod.purchase_order_no')
                ->when($locationId != '', function ($q) use ($locationId) {
                    return $q->where('po.location_id','=',$locationId);
                })
                ->when($subDepartmentId != '', function ($q) use ($subDepartmentId) {
                    return $q->where('po.sub_department_id','=',$subDepartmentId);
                })
                ->when($projectId != '', function ($q) use ($projectId) {
                    return $q->where('po.project_id','=',$projectId);
                })
                ->when($supplierId != '', function ($q) use ($supplierId) {
                    return $q->where('po.supplier_id','=',$supplierId);
                })
                ->whereBetween('po.purchase_order_date', [$fromDate, $toDate])
                ->groupBy('po.id')
                ->get();
            return view('Store.Report.InvoiceSubmissionReportAjax',compact('query','params'));    
        }
        return view('Store.Report.InvoiceSubmissionReport');
    }



    public function exportPurchaseOrderItemWise(Request $request){
        $export = new ExportPurchaseOrderItemWise($request->fromDate, $request->toDate, $request->filterSupplierId);
        return Excel::download($export, 'exportPurchaseOrderItemWise.xlsx');
    }
}
