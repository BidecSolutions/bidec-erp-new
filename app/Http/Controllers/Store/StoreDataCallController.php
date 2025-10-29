<?php

namespace App\Http\Controllers\Store;
use App\Http\Controllers\Controller;

use App\Http\Requests;
use Illuminate\Http\Request;
use StoreFacades;
use CommonFacades;
use Auth;
use DB;
use Config;
use Session;
use Input;
use App\Models\Supplier;
use App\Models\Category;
use App\Models\Subitem;
use App\Models\UOM;
use App\Models\StoreChallan;
use App\Models\StoreChallanData;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestData;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderData;
use App\Models\Recipe;
use App\Models\RecipeData;
use App\Models\StoreChallanReturn;
use Cache;
use App\Models\StoreChallanReturnData;
use App\PurchaseOrderExpenseData;

class StoreDataCallController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth','MultiDB']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function viewAdvanceSearchFilterAgainstPurchaseOrder(Request $request){
    
        $paramDetail = $request->id;
        $m = $request->m;
        $explodeParamDetail = explode('<*>',$paramDetail);
        $columnName = $explodeParamDetail[0];
        $columnValue = $explodeParamDetail[1];

        if($columnName == 'purchase_request_date' || $columnName == 'purchase_order_date'){
            $filterCondition = " purchase_order.".$columnName." = '".$columnValue."'";
        }else if($columnName == 'supplier_id'){
            $filterCondition = " purchase_order.".$columnName." = ".$columnValue."";
        }else{
            $filterCondition = " purchase_order.".$columnName. " like '%".$columnValue."%'";
        }

        $filterPurchaseOrderVoucherList = DB::select("SELECT 
            purchase_order.id,purchase_order.payment_voucher_status,purchase_order.sub_department_id,
            purchase_order.purchase_order_type,purchase_order.voucher_type,purchase_order.region_id,
            purchase_order.department_id,purchase_order.accounting_year,purchase_order.purchase_order_no,purchase_order.purchase_order_date,
            purchase_order.purchase_request_no,purchase_order.purchase_request_date,purchase_order.delivery_days,
            purchase_order.delivery_place,purchase_order.payment_terms,purchase_order.qoutation_no,purchase_order.qoutation_date,
            purchase_order.supplier_id,purchase_order.location_id,purchase_order.description,purchase_order.purchase_order_status,
            purchase_order.status,purchase_order.date,purchase_order.time,purchase_order.username,purchase_order.user_id,
            purchase_order.approve_username,purchase_order.approve_date,purchase_order.approve_time,purchase_order.delete_username,purchase_order.voucher_remarks,location.location_name,name,department_name,sub_department_name,project_name,
            (SELECT count(purchase_order_data.id) FROM purchase_order_data WHERE purchase_order_data.purchase_order_no = purchase_order.purchase_order_no and purchase_order_data.grn_status = 1) as totalRemainingItemNotGeneratedGRN,
            (SELECT count(purchase_order_data.id) FROM purchase_order_data WHERE purchase_order_data.purchase_order_no = purchase_order.purchase_order_no) as totalItemsPurchaseOrderData
            FROM purchase_order

            INNER JOIN sub_department ON purchase_order.sub_department_id = sub_department.id
            INNER JOIN department ON purchase_order.department_id = department.id 
            INNER JOIN location ON purchase_order.location_id = location.id
            INNER JOIN project ON purchase_order.project_id = project.id
            INNER JOIN supplier ON purchase_order.supplier_id = supplier.id
            WHERE ".$filterCondition." order by purchase_order.id desc");
		return view('Store.AjaxPages.viewAdvanceSearchFilterAgainstPurchaseOrder',compact('filterPurchaseOrderVoucherList'));
	}
    public function viewAdvanceSearchFilterAgainstMaterialRequest(Request $request){

        $paramDetail = $request->id;
        $m = $request->m;
        $explodeParamDetail = explode('<*>',$paramDetail);
        $columnName = $explodeParamDetail[0];
        $columnValue = $explodeParamDetail[1];

        if(isset($columnName)){
            $filterCondition = " and material_request.".$columnName." like '%".$columnValue."%'";
        }else{
            $filterCondition = "";
        }
        // dd($filterCondition);
        $filterMaterialRequestList = DB::select("SELECT 
           material_request.id,
            material_request.accounting_year,
            material_request.company_id,
            material_request.location_id,
            material_request.project_id,
            material_request.material_request_no,
            material_request.material_request_date,
            material_request.required_date,
            material_request.department_id,
            material_request.sub_department_id,
            material_request.description,
            material_request.material_request_status,
            material_request.additional_remarks,
            material_request.status,
            material_request.date,
            material_request.time,
            material_request.username,
            material_request.user_id,
            material_request.approve_username,
            material_request.approve_date,
            material_request.approve_time,
            material_request.approve_user_id,
            material_request.delete_username,
            material_request.delete_date,
            material_request.delete_time,
            material_request.delete_user_id,
            material_request.store_challan_status,
            (SELECT count(material_request_data.id) FROM material_request_data WHERE material_request_data.material_request_no = material_request.material_request_no and material_request_data.store_challan_status = 1) as totalRemainingItemNotGeneratedSC,
            (SELECT count(material_request_data.id) FROM material_request_data WHERE material_request_data.material_request_no = material_request.material_request_no) as totalRequestItems
        from material_request 
        where material_request.company_id = ".$m." and 
        material_request.accounting_year = ".Session::get('accountYear')."
        ".$filterCondition." order by material_request.id desc");;
        // dd($filterMaterialRequestList);
		return view('Store.AjaxPages.viewAdvanceSearchFilterAgainstMaterialRequest',compact('filterMaterialRequestList'));
	}

    public function loadPurchaseOrderDetail(Request $request){
        echo $poId = $request->input('poId');
        $purchaseOrderList = DB::connection('tenant')->table('purchase_order')->select('id','purchase_order_no','purchase_order_date')->where('status',1)->get();
        foreach($purchaseOrderList as $polRow){
        ?>
            <option value="<?php echo $polRow->id;?>" <?php if($poId == $polRow->id){echo 'selected';}?>><?php echo $polRow->purchase_order_no.' - '.$polRow->purchase_order_date?></option>
        <?php
        }
    }



    public function viewAdvanceSearchFilterAgainstStoreChallan(Request $request){
       //dd($request->all());
        $paramDetail = $request->id;
        $m = $request->m;
        $explodeParamDetail = explode('<*>',$paramDetail);
        $columnName = $explodeParamDetail[0];
        $columnValue = $explodeParamDetail[1];
       //dd($columnName);
        if($columnName == 'material_request_no'){
            //dd($columnName);
            $filterStoreChallanVoucherList = DB::table('store_challan')->where('store_challan_no', $columnValue)->get();      
            return view('Store.AjaxPages.viewAdvanceSearchFilterAgainstStoreChallan',compact('filterStoreChallanVoucherList'));
        }else if($columnName == 'MR_no'){
            $filterStoreChallanVoucherList = DB::table('store_challan')->where('material_request_no', $columnValue)->get();
            return view('Store.AjaxPages.viewAdvanceSearchFilterAgainstStoreChallan',compact('filterStoreChallanVoucherList'));
        }
        else if($columnName == 'material_request_date'){
            $filterStoreChallanVoucherList = DB::table('store_challan')->where('store_challan_date', $columnValue)->get();
            return view('Store.AjaxPages.viewAdvanceSearchFilterAgainstStoreChallan',compact('filterStoreChallanVoucherList'));
        }
        else if($columnName == 'username'){
            $filterStoreChallanVoucherList = DB::table('store_challan')->where('username', $columnValue)->get();
            return view('Store.AjaxPages.viewAdvanceSearchFilterAgainstStoreChallan',compact('filterStoreChallanVoucherList'));
        }
        else{
            $filterCondition = " purchase_order.".$columnName. " like '%".$columnValue."%'";
        }
        
      
	}
    public function filterStoreChallanVoucherList(){
        $fromDate = date("Y-m-d", strtotime(Input::get('fromDate')));
        $toDate = date("Y-m-d", strtotime(Input::get('toDate')));
        $m = CommonFacades::getSessionCompanyId();

        $selectVoucherStatus = Input::get('selectVoucherStatus');
        $filterLocationId = Input::get('filterLocationId');
        $filterSubDepartmentId = Input::get('filterSubDepartmentId');
        $startRecordNo = Input::get('startRecordNo');
        $endRecordNo = Input::get('endRecordNo');
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

        if(empty($filterLocationId)){
            $locationCondition = '';
        }else{
            $locationCondition = ' and store_challan.location_id = '.$filterLocationId.'';
        }

        if(empty($filterSubDepartmentId)){
            $subDepartmentCondition = '';
        }else{
            $subDepartmentCondition = ' and store_challan.sub_department_id = '.$filterSubDepartmentId.'';
        }

        if(empty($filterPojectId)){
            $filterProjectCondition = '';
        }else{
            $filterProjectCondition = ' and store_challan.project_id = '.$filterPojectId.'';
        }
        
        $countStoreChallanVoucherList = DB::select("select 
            store_challan.id,
            store_challan.accounting_year,
            store_challan.company_id,
            store_challan.store_challan_no,
            store_challan.store_challan_date,
            store_challan.description,
            store_challan.store_challan_status,
            store_challan.status,
            store_challan.date,
            store_challan.time,
            store_challan.username,
            store_challan.user_id,
            store_challan.approve_username,
            store_challan.delete_username,
            store_challan.location_id,
            store_challan.project_id,
            store_challan.department_id,
            store_challan.sub_department_id
            from store_challan 
        where store_challan.company_id = ".$m." and 
        store_challan.accounting_year = ".Session::get('accountYear')." and 
        store_challan.store_challan_date between '".$fromDate."' and '".$toDate."'".$statusContion."".$locationCondition."".$subDepartmentCondition."".$filterProjectCondition."");
        
        $filterStoreChallanVoucherList = DB::select("select 
            store_challan.id,
            store_challan.accounting_year,
            store_challan.company_id,
            store_challan.material_request_no,
            store_challan.material_request_date,
            store_challan.store_challan_no,
            store_challan.store_challan_date,
            store_challan.description,
            store_challan.store_challan_status,
            store_challan.status,
            store_challan.date,
            store_challan.time,
            store_challan.username,
            store_challan.user_id,
            store_challan.approve_username,
            store_challan.approve_date,
            store_challan.approve_time,
            store_challan.delete_username,
            store_challan.location_id,
            store_challan.project_id,
            store_challan.department_id,
            store_challan.sub_department_id,
            store_challan.receiver_username,
            store_challan.receiver_date,
            store_challan.receiver_time
        from store_challan 
        where store_challan.company_id = ".$m." and 
        store_challan.accounting_year = ".Session::get('accountYear')." and 
        store_challan.store_challan_date between '".$fromDate."' and '".$toDate."'".$statusContion."".$locationCondition."".$subDepartmentCondition."".$filterProjectCondition." order by store_challan.id desc LIMIT ".$startRecordNo.",".$endRecordNo."");
        
        return view('Store.AjaxPages.filterStoreChallanVoucherList',compact('filterStoreChallanVoucherList','countStoreChallanVoucherList'));
    }

    public function filterMaterialRequestVoucherList(){
        $fromDate = date("Y-m-d", strtotime(Input::get('fromDate')));
        $toDate = date("Y-m-d", strtotime(Input::get('toDate')));
        $m = CommonFacades::getSessionCompanyId();

        $selectVoucherStatus = Input::get('selectVoucherStatus');
        $filterLocationId = Input::get('filterLocationId');
        $filterSubDepartmentId = Input::get('filterSubDepartmentId');
        $startRecordNo = Input::get('startRecordNo');
        $endRecordNo = Input::get('endRecordNo');
        $filterPojectId = Input::get('filterProjectId');

        if($selectVoucherStatus == 0){
            $statusContion = '';
        }else if($selectVoucherStatus == 1){
            $statusContion = ' and material_request.status = 1 and material_request.material_request_status = 1';
        }else if($selectVoucherStatus == 2){
            $statusContion = ' and material_request.status = 1 and material_request.material_request_status = 2';
        }else if($selectVoucherStatus == 3){
            $statusContion = ' and material_request.status = 2';
        }else if($selectVoucherStatus == 4){
            $statusContion = ' HAVING (SELECT count(material_request_data.id) FROM material_request_data WHERE material_request_data.material_request_no = material_request.material_request_no and material_request_data.store_challan_status = 1) = 0';
        }

        if(empty($filterLocationId)){
            $locationCondition = '';
        }else{
            $locationCondition = ' and material_request.location_id = '.$filterLocationId.'';
        }

        if(empty($filterSubDepartmentId)){
            $subDepartmentCondition = '';
        }else{
            $subDepartmentCondition = ' and material_request.sub_department_id = '.$filterSubDepartmentId.'';
        }

        if(empty($filterPojectId)){
            $filterProjectCondition = '';
        }else{
            $filterProjectCondition = ' and material_request.project_id = '.$filterPojectId.'';
        }
        
        $countMaterialRequestList = DB::select("select 
            material_request.id,
            material_request.accounting_year,
            material_request.company_id,
            material_request.location_id,
            material_request.project_id,
            material_request.material_request_no,
            material_request.material_request_date,
            material_request.required_date,
            material_request.department_id,
            material_request.sub_department_id,
            material_request.description,
            material_request.material_request_status,
            material_request.additional_remarks,
            material_request.status,
            material_request.date,
            material_request.time,
            material_request.username,
            material_request.user_id,
            material_request.approve_username,
            material_request.approve_date,
            material_request.approve_time,
            material_request.approve_user_id,
            material_request.delete_username,
            material_request.delete_date,
            material_request.delete_time,
            material_request.delete_user_id,
            material_request.store_challan_status
        from material_request 
        where material_request.company_id = ".$m." and 
        material_request.accounting_year = ".Session::get('accountYear')." and 
        material_request.material_request_date between '".$fromDate."' and '".$toDate."'".$statusContion."".$locationCondition."".$subDepartmentCondition."".$filterProjectCondition."");
        
        $filterMaterialRequestList = DB::select("select 
            material_request.id,
            material_request.accounting_year,
            material_request.company_id,
            material_request.location_id,
            material_request.project_id,
            material_request.material_request_no,
            material_request.material_request_date,
            material_request.required_date,
            material_request.department_id,
            material_request.sub_department_id,
            material_request.description,
            material_request.material_request_status,
            material_request.additional_remarks,
            material_request.status,
            material_request.date,
            material_request.time,
            material_request.username,
            material_request.user_id,
            material_request.approve_username,
            material_request.approve_date,
            material_request.approve_time,
            material_request.approve_user_id,
            material_request.delete_username,
            material_request.delete_date,
            material_request.delete_time,
            material_request.delete_user_id,
            material_request.store_challan_status,
            (SELECT count(material_request_data.id) FROM material_request_data WHERE material_request_data.material_request_no = material_request.material_request_no and material_request_data.store_challan_status = 1) as totalRemainingItemNotGeneratedSC,
            (SELECT count(material_request_data.id) FROM material_request_data WHERE material_request_data.material_request_no = material_request.material_request_no) as totalRequestItems
        from material_request 
        where material_request.company_id = ".$m." and 
        material_request.accounting_year = ".Session::get('accountYear')." and 
        material_request.material_request_date between '".$fromDate."' and '".$toDate."'".$statusContion."".$locationCondition."".$subDepartmentCondition."".$filterProjectCondition." order by material_request.id desc LIMIT ".$startRecordNo.",".$endRecordNo."");
        // dd($filterMaterialRequestList);
        return view('Store.AjaxPages.filterMaterialRequestVoucherList',compact('filterMaterialRequestList','countMaterialRequestList'));
    }

    public function viewStoreChallanVoucherDetail(){
        $id = Input::get('id');
        $getStoreChallanDetail = DB::selectOne("select 
            store_challan.id,
            store_challan.accounting_year,
            store_challan.material_request_no,
            store_challan.material_request_date,
            store_challan.location_id,
            store_challan.project_id,
            store_challan.department_id,
            store_challan.company_id,
            store_challan.store_challan_no,
            store_challan.store_challan_date,
            store_challan.sub_department_id,
            store_challan.warehouse_from_id,
            store_challan.warehouse_to_id,
            store_challan.from_sub_department_id,
            store_challan.description,
            store_challan.store_challan_status,
            store_challan.status,
            store_challan.purpose,
            store_challan.date,
            store_challan.time,
            store_challan.username,
            store_challan.user_id,
            store_challan.approve_username,
            store_challan.receiver_user_id,
            store_challan.approve_date,
            store_challan.approve_time,
            store_challan.delete_username,
            sub_department.sub_department_name,
            location.location_name,
            department.department_name,
            project.project_name
        from store_challan 
        INNER JOIN sub_department ON store_challan.sub_department_id = sub_department.id
        INNER JOIN department ON sub_department.department_id = department.id 
        INNER JOIN location ON store_challan.location_id = location.id
        INNER JOIN project ON store_challan.project_id = project.id 
        where store_challan.store_challan_no = '".$id."'");
        $getStoreChallanDataDetail = DB::select("select
            store_challan_data.id,
            store_challan_data.accounting_year,
            store_challan_data.company_id,
            store_challan_data.material_request_data_id,
            store_challan_data.store_challan_no,
            store_challan_data.store_challan_date,
            store_challan_data.issue_qty,
            store_challan_data.item_type,
            store_challan_data.store_challan_status,
            store_challan_data.status,
            store_challan_data.date,
            store_challan_data.time,
            store_challan_data.username,
            store_challan_data.user_id,
            store_challan_data.approve_username,
            store_challan_data.approve_date,
            store_challan_data.approve_time,
            store_challan_data.delete_username,
            store_challan_data.category_id,
            store_challan_data.sub_item_id,
            category.main_ic,
            subitem.item_code,
            subitem.sub_ic,
            uom.uom_name,
            material_request_data.qty
        from store_challan_data
        INNER JOIN material_request_data ON store_challan_data.material_request_data_id = material_request_data.id
        INNER JOIN category ON store_challan_data.category_id = category.id
        INNER JOIN subitem ON store_challan_data.sub_item_id = subitem.id
        INNER JOIN uom ON subitem.uom = uom.id
        where store_challan_data.store_challan_no = '".$id."'");
        //dd($getStoreChallanDetail);
        return view('Store.AjaxPages.viewStoreChallanVoucherDetail',compact('getStoreChallanDetail','getStoreChallanDataDetail'));
    }
     public function editMaterialRequestVoucherForm(Request $request){
       
        $itemID = $request->id;
        $m = CommonFacades::getSessionCompanyId();
        $departments = Cache::rememberForever('cacheDepartment_'.$m.'',function() use ($m){
            return DB::select("select * from department where company_id = ".$m."");
        });
        $id = Input::get('id');
        $getMaterialRequestDetail = DB::selectOne("select 
            material_request.id,
            material_request.accounting_year,
            material_request.company_id,
            material_request.location_id,
            material_request.material_request_no,
            material_request.material_request_date,
            material_request.required_date,
            material_request.description,
            material_request.material_request_status,
            material_request.status,
            material_request.date,
            material_request.time,
            material_request.username,
            material_request.user_id,
            material_request.approve_username,
            material_request.approve_date,
            material_request.delete_username,
            material_request.additional_remarks,
            material_request.project_id,
            sub_department.sub_department_name,
            location.location_name,
            department.department_name,
            project.project_name,
            material_request.sub_department_id,
            material_request.department_id
        from material_request 
        INNER JOIN sub_department ON material_request.sub_department_id = sub_department.id
        INNER JOIN department ON sub_department.department_id = department.id 
        INNER JOIN location ON material_request.location_id = location.id
        INNER JOIN project ON material_request.project_id = project.id 
        where material_request.material_request_no = '".$id."'");
        $getMaterialRequestDataDetail = DB::select("select
            material_request_data.id,
            material_request_data.accounting_year,
            material_request_data.company_id,
            material_request_data.material_request_no,
            material_request_data.material_request_date,
            material_request_data.required_date,
            material_request_data.qty,
            material_request_data.approx_cost,
            material_request_data.approx_sub_total,
            material_request_data.sub_description,
            material_request_data.material_request_status,
            material_request_data.store_challan_status,
            material_request_data.status,
            material_request_data.date,
            material_request_data.time,
            material_request_data.username,
            material_request_data.user_id,
            material_request_data.approve_username,
            material_request_data.delete_username,
            material_request_data.category_id,
            material_request_data.sub_item_id,
            material_request_data.uom_id,
            category.main_ic,
            subitem.item_code,
            subitem.sub_ic,
            uom.uom_name,
            (select sum(issue_qty) from store_challan_data where material_request_data.id = store_challan_data.material_request_data_id and store_challan_data.status = 1) as issueQty
        from material_request_data
        INNER JOIN category ON material_request_data.category_id = category.id
        INNER JOIN subitem ON material_request_data.sub_item_id = subitem.id
        INNER JOIN uom ON subitem.uom = uom.id
        where material_request_data.material_request_no = '".$id."'");
        // dd($getMaterialRequestDataDetail, $getMaterialRequestDetail);
        return view('Store.AjaxPages.editMaterialRequestVoucherForm',compact('departments','id','itemID','getMaterialRequestDetail','getMaterialRequestDataDetail'));
    }
    public function viewMaterialRequestVoucherDetail(){
        $id = Input::get('id');
        $getMaterialRequestDetail = DB::selectOne("select 
            material_request.id,
            material_request.accounting_year,
            material_request.company_id,
            material_request.location_id,
            material_request.material_request_no,
            material_request.material_request_date,
            material_request.required_date,
            material_request.description,
            material_request.material_request_status,
            material_request.status,
            material_request.date,
            material_request.time,
            material_request.username,
            material_request.user_id,
            material_request.approve_username,
            material_request.approve_date,
            material_request.delete_username,
            material_request.additional_remarks,
            sub_department.sub_department_name,
            location.location_name,
            department.department_name,
            project.project_name
        from material_request 
        INNER JOIN sub_department ON material_request.sub_department_id = sub_department.id
        INNER JOIN department ON sub_department.department_id = department.id 
        INNER JOIN location ON material_request.location_id = location.id
        INNER JOIN project ON material_request.project_id = project.id 
        where material_request.material_request_no = '".$id."'");
        $getMaterialRequestDataDetail = DB::select("select
            material_request_data.id,
            material_request_data.accounting_year,
            material_request_data.company_id,
            material_request_data.material_request_no,
            material_request_data.material_request_date,
            material_request_data.required_date,
            material_request_data.qty,
            material_request_data.approx_cost,
            material_request_data.approx_sub_total,
            material_request_data.sub_description,
            material_request_data.material_request_status,
            material_request_data.store_challan_status,
            material_request_data.status,
            material_request_data.date,
            material_request_data.time,
            material_request_data.username,
            material_request_data.user_id,
            material_request_data.approve_username,
            material_request_data.delete_username,
            material_request_data.category_id,
            material_request_data.sub_item_id,
            material_request_data.uom_id,
            category.main_ic,
            subitem.item_code,
            subitem.sub_ic,
            uom.uom_name,
            (select sum(issue_qty) from store_challan_data where material_request_data.id = store_challan_data.material_request_data_id and store_challan_data.status = 1) as issueQty
        from material_request_data
        INNER JOIN category ON material_request_data.category_id = category.id
        INNER JOIN subitem ON material_request_data.sub_item_id = subitem.id
        INNER JOIN uom ON subitem.uom = uom.id
        where material_request_data.material_request_no = '".$id."'");
       // dd($getMaterialRequestDataDetail);
        return view('Store.AjaxPages.viewMaterialRequestVoucherDetail',compact('getMaterialRequestDetail','getMaterialRequestDataDetail'));
    }

    public function filterPurchaseOrderVoucherList(){

        $fromDate = date("Y-m-d", strtotime(Input::get('fromDate')));
        $toDate = date("Y-m-d", strtotime(Input::get('toDate')));
        $m = Input::get('m');
        $filterLocationId = Input::get('filterLocationId');
		$filterSubDepartmentId = Input::get('filterSubDepartmentId');
        $filterSupplierId = Input::get('filterSupplierId');
        $selectVoucherStatus = Input::get('selectVoucherStatus');
        $startRecordNo = Input::get('startRecordNo');
        $endRecordNo = Input::get('endRecordNo');
        $filterPojectId = Input::get('filterProjectId');
        $selectQuotationStatus = Input::get('selectQuotationStatus'); 
		//$getRegionPrivilagesArray = CommonHelper::getRegionPrivilagesArray($m,Auth::user()->emr_no);
		
		if($selectVoucherStatus == '0'){
			$statusCondition = '';
			$purchaseOrderCondition = '';
		}else if($selectVoucherStatus == '1'){
			$statusCondition = ' and purchase_order.status = 1';
			$purchaseOrderCondition = ' and purchase_order.purchase_order_status = 1';
		}else if($selectVoucherStatus == '2'){
			$statusCondition = ' and purchase_order.status = 1';
			$purchaseOrderCondition = ' and purchase_order.purchase_order_status = 2';
		}else if($selectVoucherStatus == '3'){
			$statusCondition = ' and purchase_order.status = 2';
			$purchaseOrderCondition = '';
		}else if($selectVoucherStatus == '4'){
			$statusCondition = ' HAVING (SELECT count(purchase_order_data.id) FROM purchase_order_data WHERE purchase_order_data.purchase_order_no = purchase_order.purchase_order_no and purchase_order_data.grn_status = 1) = 0';			
            $purchaseOrderCondition = '';
		}

        if($selectQuotationStatus == 0){
            $filterQuotationStatusCondition = '';    
        }else{
            $filterQuotationStatusCondition = ' and purchase_request.quotation_status = '.$selectQuotationStatus.'';
        }
		
		if(!empty($filterSubDepartmentId)){
			$filterSubDepartmentIdCondition = ' and purchase_order.sub_department_id = '.$filterSubDepartmentId.'';
		}else{
			$filterSubDepartmentIdCondition = '';
		}
		
		if(!empty($filterSupplierId)){
			$filterSupplierIdCondition = ' and purchase_order.supplier_id = '.$filterSupplierId.'';
		}else{
			$filterSupplierIdCondition = '';
        }
        if(empty($filterLocationId)){
            $filterLocationIdCondition = '';
        }else{
            $filterLocationIdCondition = ' and purchase_order.location_id = '.$filterLocationId.'';
        }

        if(empty($filterPojectId)){
            $filterProjectCondition = '';
        }else{
            $filterProjectCondition = ' and purchase_order.project_id = '.$filterPojectId.'';
        }
		
        $countPurchaseOrderVoucherList = DB::select("SELECT 
            purchase_order.id,purchase_order.payment_voucher_status,purchase_order.sub_department_id,
            purchase_order.purchase_order_type,purchase_order.voucher_type,purchase_order.region_id,
            purchase_order.department_id,purchase_order.accounting_year,purchase_order.purchase_order_no,purchase_order.purchase_order_date,
            purchase_order.purchase_request_no,purchase_order.purchase_request_date,purchase_order.delivery_days,
            purchase_order.delivery_place,purchase_order.payment_terms,purchase_order.qoutation_no,purchase_order.qoutation_date,
            purchase_order.supplier_id,purchase_order.location_id,purchase_order.description,purchase_order.purchase_order_status,
            purchase_order.status,purchase_order.date,purchase_order.time,purchase_order.username,purchase_order.user_id,
            purchase_order.approve_username,purchase_order.approve_date,purchase_order.approve_time,purchase_order.delete_username,purchase_order.voucher_remarks,location.location_name,name,department_name,sub_department_name,project_name 
            FROM purchase_order
            INNER Join purchase_request on purchase_order.purchase_request_no = purchase_request.purchase_request_no
            INNER JOIN sub_department ON purchase_order.sub_department_id = sub_department.id
            INNER JOIN department ON purchase_order.department_id = department.id 
            INNER JOIN location ON purchase_order.location_id = location.id
            INNER JOIN project ON purchase_order.project_id = project.id
            INNER JOIN supplier ON purchase_order.supplier_id = supplier.id 
            WHERE purchase_order.purchase_order_date BETWEEN '".$fromDate."' AND  '".$toDate."'".$filterSubDepartmentIdCondition."".$purchaseOrderCondition."".$statusCondition."".$filterSupplierIdCondition."".$filterLocationIdCondition."".$filterProjectCondition."".$filterQuotationStatusCondition."");
        
        $filterPurchaseOrderVoucherList = DB::select("SELECT 
            purchase_order.id,purchase_order.payment_voucher_status,purchase_order.sub_department_id,
            purchase_order.purchase_order_type,purchase_order.voucher_type,purchase_order.region_id,
            purchase_order.department_id,purchase_order.accounting_year,purchase_order.purchase_order_no,purchase_order.purchase_order_date,
            purchase_order.purchase_request_no,purchase_order.purchase_request_date,purchase_order.delivery_days,
            purchase_order.delivery_place,purchase_order.payment_terms,purchase_order.qoutation_no,purchase_order.qoutation_date,
            purchase_order.supplier_id,purchase_order.location_id,purchase_order.description,purchase_order.purchase_order_status,
            purchase_order.status,purchase_order.date,purchase_order.time,purchase_order.username,purchase_order.user_id,purchase_order.po_type,
            purchase_order.approve_username,purchase_order.approve_date,purchase_order.approve_time,purchase_order.delete_username,purchase_order.voucher_remarks,location.location_name,name,department_name,sub_department_name,project_name,
            (SELECT count(purchase_order_data.id) FROM purchase_order_data WHERE purchase_order_data.purchase_order_no = purchase_order.purchase_order_no and purchase_order_data.grn_status = 1) as totalRemainingItemNotGeneratedGRN,
            (SELECT count(purchase_order_data.id) FROM purchase_order_data WHERE purchase_order_data.purchase_order_no = purchase_order.purchase_order_no) as totalItemsPurchaseOrderData
            FROM purchase_order

            INNER Join purchase_request on purchase_order.purchase_request_no = purchase_request.purchase_request_no
            INNER JOIN sub_department ON purchase_order.sub_department_id = sub_department.id
            INNER JOIN department ON purchase_order.department_id = department.id 
            INNER JOIN location ON purchase_order.location_id = location.id
            INNER JOIN project ON purchase_order.project_id = project.id
            INNER JOIN supplier ON purchase_order.supplier_id = supplier.id
            WHERE purchase_order.purchase_order_date BETWEEN '".$fromDate."' AND  '".$toDate."'".$filterSubDepartmentIdCondition."".$purchaseOrderCondition."".$statusCondition."".$filterSupplierIdCondition."".$filterLocationIdCondition."".$filterProjectCondition."".$filterQuotationStatusCondition."  order by purchase_order.id desc LIMIT ".$startRecordNo.",".$endRecordNo."");
		// dd($filterPurchaseOrderVoucherList, $countPurchaseOrderVoucherList);
        return view('Store.AjaxPages.filterPurchaseOrderVoucherList',compact('filterPurchaseOrderVoucherList','countPurchaseOrderVoucherList'));
    }
    
    public function viewPurchaseOrderVoucherDetail(){
        $poNo = Input::get('id');
        $getPurchaseOrderDetail = DB::selectOne("
        select 
            purchase_order.id,
            purchase_order.company_id,
            purchase_order.payment_voucher_status,
            purchase_order.purchase_order_type,
            purchase_order.voucher_type,
            purchase_order.region_id,
            purchase_order.department_id,
            purchase_order.sub_department_id,
            purchase_order.accounting_year,
            purchase_order.purchase_order_no,
            purchase_order.purchase_order_date,
            purchase_order.purchase_request_no,
            purchase_order.purchase_request_date,
            purchase_order.delivery_days,
            purchase_order.delivery_place,
            purchase_order.payment_terms,
            purchase_order.qoutation_no,
            purchase_order.qoutation_date,
            purchase_order.supplier_id,
            purchase_order.location_id,
            purchase_order.term_and_condition,
            purchase_order.description,
            purchase_order.purchase_order_status,
            purchase_order.status,
            purchase_order.date,
            purchase_order.time,
            purchase_order.username,
            purchase_order.user_id,
            purchase_order.approve_user_id,
            purchase_order.approve_username,
            purchase_order.approve_date,
            purchase_order.approve_time,
            purchase_order.delete_username,
            purchase_order.po_note,
            purchase_order.paymentType,
            purchase_order.payment_type_rate,
            purchase_order.custom_tax_percent,
            purchase_order.account_id,
            purchase_order.po_type,
            purchase_order.po_discount,
            supplier.name,
            supplier.physical_address,
            supplier.mobile_no,
            supplier.ntn_no,
            supplier.bank_name,
            supplier.account_no,
            supplier.account_title,
            location.location_name,
            project.project_name,
            department.department_name,
            sub_department.sub_department_name
        from 
            purchase_order,
            supplier,
            location,
            project,
            department,
            sub_department
        where 
            purchase_order.supplier_id = supplier.id and 
            purchase_order.location_id = location.id and 
            purchase_order.project_id = project.id and 
            purchase_order.department_id = department.id and 
            purchase_order.sub_department_id = sub_department.id and 
            purchase_order_no = '".$poNo."'
        ");        
        $getPurchaseOrderDataDetail = DB::select("
        select
            purchase_order_data.id,
            purchase_order_data.accounting_year,
            purchase_order_data.company_id,
            purchase_order_data.purchase_order_no,
            purchase_order_data.purchase_order_date,
            purchase_order_data.purchase_request_no,
            purchase_order_data.purchase_request_data_record_id,
            purchase_order_data.purchase_request_date,
            purchase_order_data.category_id,
            purchase_order_data.sub_item_id,
            purchase_order_data.location_id,
            purchase_order_data.supplier_id,
            purchase_order_data.supplier_location_id,
            purchase_order_data.sale_tax_head,
            purchase_order_data.sale_tax_status,
            purchase_order_data.qoutation_no,
            purchase_order_data.qoutation_date,
            purchase_order_data.delivery_days,
            purchase_order_data.payment_terms,
            purchase_order_data.greaterthan_valuation,
            purchase_order_data.lessthan_valuation,
            purchase_order_data.unit,
            purchase_order_data.purchase_order_qty,
            purchase_order_data.unit_price,
            purchase_order_data.privious_unit_price,
            purchase_order_data.sub_total,
            purchase_order_data.sub_total_with_persent,
            purchase_order_data.user_id,
            purchase_order_data.purchase_order_status,
            purchase_order_data.grn_status,
            purchase_order_data.status,
            purchase_order_data.date,
            purchase_order_data.time,
            purchase_order_data.username,
            purchase_order_data.approve_username,
            purchase_order_data.approve_date,
            purchase_order_data.approve_time,
            purchase_order_data.delete_username,
            purchase_order_data.item_name,
            category.main_ic,
            subitem.item_code,
            subitem.sub_ic,
            uom.uom_name,
            purchase_order_data.custom_tax_percent
        from
            purchase_order_data
            left join category on purchase_order_data.category_id = category.id
            left join subitem on purchase_order_data.sub_item_id = subitem.id
            left join uom on subitem.uom = uom.id
        where
            purchase_order_data.purchase_order_no = '".$poNo."'
        ");
        $expenseVoucherDetail = PurchaseOrderExpenseData::where('purchase_order_no', $poNo)->get();
        // dd(count($expenseVoucherDetail));
        // dd($getPurchaseOrderDataDetail);
        $getPaymentVoucherDetail = DB::table('pvs')->where('po_no','=',$poNo)->first();
        $expense_claim_vouchers = DB::table('expense_claim_vouchers AS ecv')
            ->select('ecv.*', DB::raw('SUM(ecvd.expense_amount) AS total_amount'), 's.name', 'p.project_name', 'sd.sub_department_name', 'd.department_name', 'l.location_name')
            ->join('expense_claim_voucher_datas AS ecvd', 'ecv.id', '=', 'ecvd.master_id')
            ->leftJoin('supplier AS s', 'ecv.supplier_id', '=', 's.id')
            ->leftJoin('project AS p', 'ecv.project_id', '=', 'p.id')
            ->leftJoin('location AS l', 'ecv.location_id', '=', 'l.id')
            ->leftJoin('department AS d', 'ecv.department_id', '=', 'd.id')
            ->leftJoin('sub_department AS sd', 'ecv.sub_department_id', '=', 'sd.id')
            ->where('ecv.po_id',$getPurchaseOrderDetail->id)
            ->groupBy('ecv.id')
            ->get();
        
        return view('Store.AjaxPages.viewPurchaseOrderVoucherDetail',compact('getPurchaseOrderDetail','getPurchaseOrderDataDetail','getPaymentVoucherDetail', 'expenseVoucherDetail','expense_claim_vouchers'));
    }
    public function viewPurchaseOrderVoucherTaxDetail(){
        $poNo = Input::get('id');
        $getPurchaseOrderDetail = DB::selectOne("
        select 
            purchase_order.id,
            purchase_order.company_id,
            purchase_order.payment_voucher_status,
            purchase_order.purchase_order_type,
            purchase_order.voucher_type,
            purchase_order.region_id,
            purchase_order.department_id,
            purchase_order.sub_department_id,
            purchase_order.accounting_year,
            purchase_order.purchase_order_no,
            purchase_order.purchase_order_date,
            purchase_order.purchase_request_no,
            purchase_order.purchase_request_date,
            purchase_order.delivery_days,
            purchase_order.delivery_place,
            purchase_order.payment_terms,
            purchase_order.qoutation_no,
            purchase_order.qoutation_date,
            purchase_order.supplier_id,
            purchase_order.location_id,
            purchase_order.description,
            purchase_order.purchase_order_status,
            purchase_order.status,
            purchase_order.date,
            purchase_order.time,
            purchase_order.username,
            purchase_order.user_id,
            purchase_order.approve_user_id,
            purchase_order.approve_username,
            purchase_order.approve_date,
            purchase_order.approve_time,
            purchase_order.delete_username,
            supplier.name,
            location.location_name,
            department.department_name,
            sub_department.sub_department_name,
            purchase_order.custom_tax_percent,
            purchase_order.account_id
        from 
            purchase_order,
            supplier,
            location,
            department,
            sub_department
        where 
            purchase_order.supplier_id = supplier.id and 
            purchase_order.location_id = location.id and 
            purchase_order.department_id = department.id and 
            purchase_order.sub_department_id = sub_department.id and 
            purchase_order_no = '".$poNo."'
        ");
        $getPurchaseOrderDataDetail = DB::select("
        select
            purchase_order_data.id,
            purchase_order_data.accounting_year,
            purchase_order_data.company_id,
            purchase_order_data.purchase_order_no,
            purchase_order_data.purchase_order_date,
            purchase_order_data.purchase_request_no,
            purchase_order_data.purchase_request_data_record_id,
            purchase_order_data.purchase_request_date,
            purchase_order_data.category_id,
            purchase_order_data.sub_item_id,
            purchase_order_data.location_id,
            purchase_order_data.supplier_id,
            purchase_order_data.supplier_location_id,
            purchase_order_data.sale_tax_head,
            purchase_order_data.sale_tax_status,
            purchase_order_data.qoutation_no,
            purchase_order_data.qoutation_date,
            purchase_order_data.delivery_days,
            purchase_order_data.payment_terms,
            purchase_order_data.greaterthan_valuation,
            purchase_order_data.lessthan_valuation,
            purchase_order_data.unit,
            purchase_order_data.purchase_order_qty,
            purchase_order_data.unit_price,
            purchase_order_data.privious_unit_price,
            purchase_order_data.sub_total,
            purchase_order_data.sub_total_with_persent,
            purchase_order_data.user_id,
            purchase_order_data.purchase_order_status,
            purchase_order_data.grn_status,
            purchase_order_data.status,
            purchase_order_data.date,
            purchase_order_data.time,
            purchase_order_data.username,
            purchase_order_data.approve_username,
            purchase_order_data.approve_date,
            purchase_order_data.approve_time,
            purchase_order_data.delete_username,
            category.main_ic,
            subitem.item_code,
            subitem.sub_ic,
            uom.uom_name
        from
            purchase_order_data,category,subitem,uom
        where
            purchase_order_data.category_id = category.id and
            purchase_order_data.sub_item_id = subitem.id and
            subitem.uom = uom.id and 
            purchase_order_data.purchase_order_no = '".$poNo."'
        ");
        $getPaymentVoucherDetail = DB::table('pvs')->where('po_no','=',$poNo)->first();
        $m = CommonFacades::getSessionCompanyId();
		CommonFacades::companyDatabaseConnection($m);
        $accountList = DB::table('accounts')->orderBy('level1', 'ASC')
            ->orderBy('level2', 'ASC')
            ->orderBy('level3', 'ASC')
            ->orderBy('level4', 'ASC')
            ->orderBy('level5', 'ASC')
            ->orderBy('level6', 'ASC')
            ->orderBy('level7', 'ASC')
            ->get();
        CommonFacades::reconnectMasterDatabase();
        // dd($getPurchaseOrderDetail);
        return view('Store.AjaxPages.viewPurchaseOrderVoucherTaxDetail',compact('accountList', 'getPurchaseOrderDetail','getPurchaseOrderDataDetail','getPaymentVoucherDetail'));
    }   

    //ERP New

    public function filterPurchaseRequestVoucherList(){
        $fromDate = $_GET['fromDate'];
        $toDate = $_GET['toDate'];
        $m = CommonFacades::getSessionCompanyId();
        $counter = 1;
        $selectVoucherStatus = $_GET['selectVoucherStatus'];
        $selectSubDepartment = $_GET['selectSubDepartment'];
        $selectSubDepartmentId = $_GET['selectSubDepartmentId'];

        CommonFacades::companyDatabaseConnection($m);
        if($selectVoucherStatus == '0' && empty($selectSubDepartmentId)){
            $purchaseRequestDetail = PurchaseRequest::whereBetween('purchase_request_date',[$fromDate,$toDate])->get();
        }else if($selectVoucherStatus == '0' && !empty($selectSubDepartmentId)){
            $purchaseRequestDetail = PurchaseRequest::whereBetween('purchase_request_date',[$fromDate,$toDate])->whereIn('status', array(1, 2))->where('sub_department_id','=',$selectSubDepartmentId)->get();
        }else if($selectVoucherStatus == '1' && !empty($selectSubDepartmentId)){
            $purchaseRequestDetail = PurchaseRequest::whereBetween('purchase_request_date',[$fromDate,$toDate])->where('status','=','1')->where('purchase_request_status','=','1')->where('sub_department_id','=',$selectSubDepartmentId)->get();
        }else if($selectVoucherStatus == '2' && !empty($selectSubDepartmentId)){
            $purchaseRequestDetail = PurchaseRequest::whereBetween('purchase_request_date',[$fromDate,$toDate])->where('status','=','1')->where('purchase_request_status','=','2')->where('sub_department_id','=',$selectSubDepartmentId)->get();
        }else if($selectVoucherStatus == '3' && !empty($selectSubDepartmentId)){
            $purchaseRequestDetail = PurchaseRequest::whereBetween('purchase_request_date',[$fromDate,$toDate])->where('status','=','2')->where('sub_department_id','=',$selectSubDepartmentId)->get();
        }else if($selectVoucherStatus == '1' && empty($selectSubDepartmentId)){
            $purchaseRequestDetail = PurchaseRequest::whereBetween('purchase_request_date',[$fromDate,$toDate])->where('status','=','1')->where('purchase_request_status','=','1')->get();
        }else if($selectVoucherStatus == '2' && empty($selectSubDepartmentId)){
            $purchaseRequestDetail = PurchaseRequest::whereBetween('purchase_request_date',[$fromDate,$toDate])->where('status','=','1')->where('purchase_request_status','=','2')->get();
        }else if($selectVoucherStatus == '3' && empty($selectSubDepartmentId)){
            $purchaseRequestDetail = PurchaseRequest::whereBetween('purchase_request_date',[$fromDate,$toDate])->where('status','=','2')->get();
        }
        CommonFacades::reconnectMasterDatabase();
        return view('Store.AjaxPages.filterPurchaseRequestVoucherList',compact('purchaseRequestDetail'));
    }

    public function viewPurchaseRequestVoucherDetail(){
        return view('Store.AjaxPages.viewPurchaseRequestVoucherDetail');
    }

    public function filterApproveDemandListandCreateStoreChallan(){
        return view('Store.AjaxPages.filterApproveDemandListandCreateStoreChallan');
    }

    public function createStoreChallanDetailForm(Request $request){
        return view('Store.createStoreChallanDetailForm',compact('request'));
    }

    

    

    public function filterApprovePurchaseRequestListandCreatePurchaseOrder(){
        return view('Store.AjaxPages.filterApprovePurchaseRequestListandCreatePurchaseOrder');
    }

    public function filterApproveDemandListandCreatePurchaseRequestSale(){
        return view('Store.AjaxPages.filterApproveDemandListandCreatePurchaseRequestSale');
    }

    public function createPurchaseOrderDetailForm(Request $request){
        $m = CommonFacades::getSessionCompanyId();
        CommonFacades::companyDatabaseConnection($m);
        $supplierList = Supplier::select('name','id','acc_id')->where('status','=','1')->get();
        CommonFacades::reconnectMasterDatabase();
        return view('Store.createPurchaseOrderDetailForm',compact('request','supplierList'));
    }

    public function createPurchaseOrderSaleDetailForm(Request $request){
        $m = CommonFacades::getSessionCompanyId();
        CommonFacades::companyDatabaseConnection($m);
        $supplierList = Supplier::select('name','id','acc_id')->where('status','=','1')->get();
        CommonFacades::reconnectMasterDatabase();
        return view('Store.createPurchaseRequestSaleDetailForm',compact('request','supplierList'));
    }

    


    public function filterPurchaseOrderSaleVoucherList(){

        $fromDate = $_GET['fromDate'];
        $toDate = $_GET['toDate'];
        $m = CommonFacades::getSessionCompanyId();
        $selectVoucherStatus = $_GET['selectVoucherStatus'];
        $selectSubDepartment = $_GET['selectSubDepartment'];
        $selectSubDepartmentId = $_GET['selectSubDepartmentId'];
        $selectSupplier = $_GET['selectSupplier'];
        $selectSupplierId = $_GET['selectSupplierId'];
        CommonFacades::companyDatabaseConnection($m);
        if($selectVoucherStatus == '0' && empty($selectSubDepartmentId) && empty($selectSupplierId)){
            //return 'One';
            $purchaseRequestSaleDetail = PurchaseRequest::whereBetween('purchase_request_date',[$fromDate,$toDate])->where('demand_type','=','2')->get();
        }else if($selectVoucherStatus == '0' && !empty($selectSubDepartmentId) && empty($selectSupplierId)){
            //return 'Two';
            $purchaseRequestSaleDetail = PurchaseRequest::whereBetween('purchase_request_date',[$fromDate,$toDate])->where('demand_type','=','2')->whereIn('status', array(1, 2))->where('sub_department_id','=',$selectSubDepartmentId)->get();
        }else if($selectVoucherStatus == '0' && empty($selectSubDepartmentId) && !empty($selectSupplierId)){
            //return 'Three';
            $purchaseRequestSaleDetail = PurchaseRequest::whereBetween('purchase_request_date',[$fromDate,$toDate])->where('demand_type','=','2')->whereIn('status', array(1, 2))->where('supplier_id','=',$selectSupplierId)->get();
        }else if($selectVoucherStatus == '1' && !empty($selectSubDepartmentId) && empty($selectSupplierId)){
            //return 'Four';
            $purchaseRequestSaleDetail = PurchaseRequest::whereBetween('purchase_request_date',[$fromDate,$toDate])->where('demand_type','=','2')->where('status','=','1')->where('purchase_request_status','=','1')->where('sub_department_id','=',$selectSubDepartmentId)->get();
        }else if($selectVoucherStatus == '2' && !empty($selectSubDepartmentId) && empty($selectSupplierId)){
            //return 'Five';
            $purchaseRequestSaleDetail = PurchaseRequest::whereBetween('purchase_request_date',[$fromDate,$toDate])->where('demand_type','=','2')->where('status','=','1')->where('purchase_request_status','=','2')->where('sub_department_id','=',$selectSubDepartmentId)->get();
        }else if($selectVoucherStatus == '3' && !empty($selectSubDepartmentId) && empty($selectSupplierId)){
            //return 'Six';
            $purchaseRequestSaleDetail = PurchaseRequest::whereBetween('purchase_request_date',[$fromDate,$toDate])->where('demand_type','=','2')->where('status','=','2')->where('sub_department_id','=',$selectSubDepartmentId)->get();
        }else if($selectVoucherStatus == '1' && empty($selectSubDepartmentId) && empty($selectSupplierId)){
            //return 'Seven';
            $purchaseRequestSaleDetail = PurchaseRequest::whereBetween('purchase_request_date',[$fromDate,$toDate])->where('demand_type','=','2')->where('status','=','1')->where('purchase_request_status','=','1')->get();
        }else if($selectVoucherStatus == '2' && empty($selectSubDepartmentId) && empty($selectSupplierId)){
            //return 'Eight';
            $purchaseRequestSaleDetail = PurchaseRequest::whereBetween('purchase_request_date',[$fromDate,$toDate])->where('demand_type','=','2')->where('status','=','1')->where('purchase_request_status','=','2')->get();
        }else if($selectVoucherStatus == '3' && empty($selectSubDepartmentId) && empty($selectSupplierId)){
            //return 'Nine';
            $purchaseRequestSaleDetail = PurchaseRequest::whereBetween('purchase_request_date',[$fromDate,$toDate])->where('demand_type','=','2')->where('status','=','2')->get();
        }else if($selectVoucherStatus == '1' && empty($selectSubDepartmentId) && !empty($selectSupplierId)){
            //return 'Ten';
            $purchaseRequestSaleDetail = PurchaseRequest::whereBetween('purchase_request_date',[$fromDate,$toDate])->where('demand_type','=','2')->where('status','=','1')->where('purchase_request_status','=','1')->where('supplier_id','=',$selectSupplierId)->get();
        }else if($selectVoucherStatus == '2' && empty($selectSubDepartmentId) && !empty($selectSupplierId)){
            //return 'Eleven';
            $purchaseRequestSaleDetail = PurchaseRequest::whereBetween('purchase_request_date',[$fromDate,$toDate])->where('demand_type','=','2')->where('status','=','1')->where('purchase_request_status','=','2')->where('supplier_id','=',$selectSupplierId)->get();
        }else if($selectVoucherStatus == '3' && empty($selectSubDepartmentId) && !empty($selectSupplierId)){
            //return 'Twelve';
            $purchaseRequestSaleDetail = PurchaseRequest::whereBetween('purchase_request_date',[$fromDate,$toDate])->where('demand_type','=','2')->where('status','=','2')->where('supplier_id','=',$selectSupplierId)->get();
        }else if($selectVoucherStatus == '0' && !empty($selectSubDepartmentId) && !empty($selectSupplierId)){
            //return 'Thirteen';
            $purchaseRequestSaleDetail = PurchaseRequest::whereBetween('purchase_request_date',[$fromDate,$toDate])->where('demand_type','=','2')->where('supplier_id','=',$selectSupplierId)->where('sub_department_id','=',$selectSubDepartmentId)->get();
        }else if($selectVoucherStatus == '1' && !empty($selectSubDepartmentId) && !empty($selectSupplierId)){
            //return 'Fourteen';
            $purchaseRequestSaleDetail = PurchaseRequest::whereBetween('purchase_request_date',[$fromDate,$toDate])->where('demand_type','=','2')->where('status','=','1')->where('purchase_request_status','=','1')->where('supplier_id','=',$selectSupplierId)->where('sub_department_id','=',$selectSubDepartmentId)->get();
        }else if($selectVoucherStatus == '2' && !empty($selectSubDepartmentId) && !empty($selectSupplierId)){
            //return 'Fifteen';
            $purchaseRequestSaleDetail = PurchaseRequest::whereBetween('purchase_request_date',[$fromDate,$toDate])->where('demand_type','=','2')->where('status','=','1')->where('purchase_request_status','=','2')->where('supplier_id','=',$selectSupplierId)->where('sub_department_id','=',$selectSubDepartmentId)->get();
        }else if($selectVoucherStatus == '3' && !empty($selectSubDepartmentId) && !empty($selectSupplierId)){
            //return 'Sixteen';
            $purchaseRequestSaleDetail = PurchaseRequest::whereBetween('purchase_request_date',[$fromDate,$toDate])->where('demand_type','=','2')->where('status','=','2')->where('supplier_id','=',$selectSupplierId)->where('sub_department_id','=',$selectSubDepartmentId)->get();
        }
        CommonFacades::reconnectMasterDatabase();
        return view('Store.AjaxPages.filterPurchaseOrderSaleVoucherList',compact('purchaseRequestSaleDetail'));
    }

    

    public function viewPurchaseOrderSaleVoucherDetail(){
        return view('Store.AjaxPages.viewPurchaseOrderSaleVoucherDetail');
    }

    public function filterApproveStoreChallanandCreateStoreChallanReturn(){
        return view('Store.AjaxPages.filterApproveStoreChallanandCreateStoreChallanReturn');
    }

    public function createStoreChallanReturnDetailForm(Request $request){
        return view('Store.createStoreChallanReturnDetailForm',compact('request'));
    }

    public function filterStoreChallanReturnList(){
        $fromDate = date("Y-m-d", strtotime(Input::get('fromDate')));
        $toDate = date("Y-m-d", strtotime(Input::get('toDate')));
        $m = CommonFacades::getSessionCompanyId();

        $startRecordNo = Input::get('startRecordNo');
        $endRecordNo = Input::get('endRecordNo');
        
        $storeChallanReturnDetail = DB::connection('tenant')->table('store_challan_return')->whereBetween('store_challan_return_date',[$fromDate,$toDate])->get();
        //echo json_encode(array('data' => $storeChallanReturnDetail));
        return view('Store.AjaxPages.filterStoreChallanReturnList',compact('storeChallanReturnDetail'));
    }
    public function viewStoreChallanReturnDetail(){
        return view('Store.AjaxPages.viewStoreChallanReturnDetail');
    }

    public function filterViewDateWiseStockInventoryReport(){
        return view('Store.AjaxPages.filterViewDateWiseStockInventoryReport');
    }

    public function viewStockInventorySummaryDetail(){
        $m = CommonFacades::getSessionCompanyId();
        $categoryIcId = $_GET['pOne'];
        $subIcId = $_GET['pTwo'];
        $filterDate = $_GET['pFour'];
        CommonFacades::companyDatabaseConnection($m);
        $itemOpeningQty = DB::table('fara')->where('main_ic_id','=',$categoryIcId)->where('sub_ic_id','=',$subIcId)->where('date','<=',$filterDate)->where('action','=',1)->first();
        $itemPurchaseData = DB::table('fara')->where('main_ic_id','=',$categoryIcId)->where('sub_ic_id','=',$subIcId)->where('date','<=',$filterDate)->whereIn('action',array(3,8))->get();
        $itemIssueData = DB::table('fara')->where('main_ic_id','=',$categoryIcId)->where('sub_ic_id','=',$subIcId)->where('date','<=',$filterDate)->where('action','=',9)->get(); 
		$itemReceivedData = DB::table("material_issuance_data")->select('received_qty','material_issuance_no','material_issuance_date')->where(['category_id' => $categoryIcId,'sub_item_id' => $subIcId])->where('material_issuance_date','<=',$filterDate)->where('received_qty','!=','0')->get();
		CommonFacades::reconnectMasterDatabase();
        return view('Store.AjaxPages.viewStockInventorySummaryDetail',compact('itemOpeningQty','itemPurchaseData','itemIssueData','itemReceivedData'));
    }

    public function getRecipeMaterialItems(Request $request){
        // dd($request);
        $no_of_qty = $request->no_of_qty;
        $recipe = Recipe::where('id', $request->id)->first();        
        $recipeData = RecipeData::where('recipe_id', $recipe->id)->get();
        // dd($recipeData);
        return view('Store.AjaxPages.viewMaterialRecipeList', compact('recipeData','no_of_qty'));
    }




}