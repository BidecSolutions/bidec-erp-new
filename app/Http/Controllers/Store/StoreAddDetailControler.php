<?php

namespace App\Http\Controllers\Store;
use App\Http\Controllers\Controller;

use App\Models\Account;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderData;
use App\Models\SubDepartment;
use App\Models\Supplier;
use App\PurchaseOrderExpenseData;
use Input;
use Auth;
use DB;
use Config;
use Redirect;
use Session;
use StoreFacades;
use CommonFacades;
use App\Helpers\CommonHelper;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

class StoreAddDetailControler extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth']);

    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function addMaterialRequestDetail(request $request){

        date_default_timezone_set("Asia/Karachi");
        $companyId = Session::get('company_id');
        $companyLocationId = Session::get('company_location_id');
        try{

          $materialRequestsSection = request('materialRequestsSection');
            foreach ($materialRequestsSection as $row) {
                $DepartmentAndSubDepartmentDetail = explode('<*>',request('sub_department_id_' . $row . ''));
                $material_request_date = strip_tags(date("Y-m-d", strtotime(request('material_request_date_' . $row . ''))));
                $description = strip_tags(request('description_' . $row . ''));
                $sub_department_id = strip_tags($DepartmentAndSubDepartmentDetail[0]);
                $departmentId = $DepartmentAndSubDepartmentDetail[0];
                // $location_id = strip_tags(request('location_id_'.$row.''));
                $projectId = strip_tags(request('project_id_'.$row.''));
              
                $recipeId = strip_tags(request('recipe_id'));
                $productionProcessId = request('productionProcessId');

                $no_of_qty = strip_tags(request('no_of_qty_'.$row.''));
                $materialRequestDataSection = request('materialRequestDataSection');
                $str = DB::selectOne("select max(convert(substr(`material_request_no`,3,length(substr(`material_request_no`,3))-4),signed integer)) reg from `material_request` where substr(`material_request_no`,-4,2) = " . date('m') . " and substr(`material_request_no`,-2,2) = " . date('y') . "")->reg;
                $material_request_no = 'MR' . ($str + 1) . date('my');


                $data1['material_request_no'] = $material_request_no;
                $data1['material_request_date'] = $material_request_date;
                $data1['description'] = $description;
                $data1['department_id'] = $departmentId;
                $data1['sub_department_id'] = $sub_department_id;
                $data1['project_id'] = request('project_id') ?: null;
                $data1['recipe_id'] = request('recipe_id') ?: null;
                $data1['productionProcessId'] = request('productionProcessId') ?: null;
                $data1['no_of_qty'] = request('no_of_qty_'.$row.'') ?: 0;
                $data1['date'] = date("Y-m-d");
                $data1['time'] = date("H:i:s");
                $data1['user_id'] = Auth::user()->id;
                $data1['username'] = Auth::user()->name;
                $data1['status'] = 1;
                $data1['material_request_status'] = 2;
                $data1['approve_username'] = Auth::user()->name;
                $data1['approve_date'] = date("Y-m-d");
                $data1['approve_time'] = date("H:i:s");
                $data1['approve_user_id'] = Auth::user()->id;
                $data1['company_id'] = $companyId;
                $data1['location_id'] = $companyLocationId;
                $data1['accounting_year'] = Session::get('accountYear') ?: date('Y');
            
       
                

                DB::table('material_request')->insert($data1);
                foreach ($materialRequestDataSection as $key => $row2) {
                    // $category_id = strip_tags(request('category_id_' . $row . '_' . $row2 . ''));
                    $product_id = request('product_id');
                    // $sub_item_id = strip_tags(request('sub_item_id_' . $row . '_' . $row2 . ''));
                    // $qty = strip_tags(request('qty_' . $row . '_' . $row2 . ''));
                    $qty = request('qty');
                    // $subDescription = strip_tags(request('sub_description_' . $row . '_' . $row2 . ''));
                    $subDescription = request('sub_description');
                    
                    // dd($category_id,$sub_item_id,$qty,$subDescription);

                    $data2['material_request_no'] = $material_request_no;
                    $data2['material_request_date'] = $material_request_date;
                    $data2['product_id'] = $product_id[$key];
                    $data2['required_date'] = date("Y-m-d");
                    $data2['qty'] = $qty[$key];
                    $data2['sub_description'] = $subDescription[$key];
                    $data2['approx_cost'] = 0;
                    $data2['date'] = date("Y-m-d");
                    $data2['time'] = date("H:i:s");
                    $data2['user_id'] = Auth::user()->id;
                    $data2['username'] = Auth::user()->name;
                    $data2['status'] = 1;
                    $data2['material_request_status'] = 2;
                    $data2['approve_username'] = Auth::user()->name;
                    $data2['company_id'] = $companyId;
                    $data2['accounting_year'] = Session::get('accountYear') ?: date('Y');
                    DB::table('material_request_data')->insert($data2);

                }
            }
            $msg = 'Add New Material Request. Material Request No => '.$material_request_no.' and Material Request Date => '.$material_request_date. ' created by '.Auth::user()->name;
            return redirect()->to('store/viewMaterialRequestList?pageType='.request('pageType').'&&parentCode='.request('parentCode').'#SFR')->with('success','Add Material Request Successfully!');
        }catch (\Exception $e) {
            return redirect()->back()->with('error','Oops! There might be a issue '. $e->getMessage());
        }
    }
    public function updateInvoicePO(Request $request)
    {
        //dd($request->all());
        DB::table('purchase_order')
            ->where('id', $request->id)
            ->update(['qoutation_no' => $request->qoutation_no]);
            return redirect()->back();
    }

    public function updateStages(Request $request)
    {
       // dd($request->all());
        DB::table('production_stages')
            ->where('id', $request->id)
            ->update(['stage_name' => $request->qoutation_no]);
            return redirect()->back();
    }
    public function updateChecklist(Request $request)
    {
       //dd($request->all());
        DB::table('production_checklists')
            ->where('id', $request->check_id)
            ->update([
                      'checklist_name' => $request->checklistName, 
                      'stage_id' => $request->stageid
                    ]);
            return redirect()->back();
    }
    public function StagesDelete($id)
    {
       // dd($id);
        DB::table('production_stages')
            ->where('id', $id)
            ->update(['status' => 0]);
            return redirect()->back();
    }
    public function updatePurchaseOrderDetail(Request $request)
    {
        $requestData = $request->all();
        
        // dd($request, $requestData);
        
        date_default_timezone_set("Asia/Karachi");
        try {
            $m = getSessionCompanyId();
            $seletedSupplierRow = Input::get('seletedPurchaseRequestRow');
            $supplierIds = array();
            $saleTaxAccCode = Account::where('code', Input::get('sales_tax_acc_code'))->first();
            foreach($seletedSupplierRow as $row) {
                // $supplierIds[] = Input::get('supplier_id_' . $row . '');
                if(isset($requestData['supplier_id_' . $row ])){                    
                    $supplierIds[] = $requestData['supplier_id_' . $row ];
                }
            }
            // dd($seletedSupplierRow, $supplierIds);

            $newSupplierIds = array();
      

            
                foreach($supplierIds as $supplierIdItems) {
                    foreach($newSupplierIds as $newSupplierIdItems) {
                        if($supplierIdItems == $newSupplierIdItems) {
                            continue 2;
                        }
                    }
                    $newSupplierIds[] = $supplierIdItems;
                }
                // $tempReturn = "";
                $allNewSuppliersCount = PurchaseOrderData::where('purchase_order_no', $request->poNo)->whereIn('supplier_id', $newSupplierIds)->count();
                // dd($allNewSuppliersCount, $newSupplierIds, $request->poNo);
                // foreach ($newSupplierIds as $key => $row1){
                    
                //     $supplierId = $row1;                    
                //     $purchaseOrderModel = PurchaseOrder::where('purchase_order_no', $request->poNo)->where('supplier_id', $supplierId);
                //     // dd($purchaseOrderModel->get(), $key);
                //     if($purchaseOrderModel->count() >= 1 && $allNewSuppliersCount > 0 ){
                        
                //         $tempReturn .= 'the purchase order exist'. $request->poNo . 'the current PO will update -----| ';
                //         // $purchaseOrderNo = $request->poNo;
                       
                //         // dd($newSupplierIds, $request, $requestData, $purchaseOrderModel->get());
                //         // $purchaseOrderModel->update($data1);
                //     }else{
                //         // dd($request->poNo, $purchaseOrderModel->count(), $allNewSuppliersCount, $supplierId, $newSupplierIds) ;
                //         $tempReturn .= 'the purchase order does not exist the current PO will insert ----- |';
                //         // if (count($newSupplierIds) <= 1) {
                //         //     dd('check');
                //         // }
                //         // dd('out', $purchaseOrderModel->count(), $supplierId);
                        
                //         if ($allNewSuppliersCount == 0) {
                //             $tempReturn .= 'the purchase order'. $request->poNo . 'suppliers is changed in all items ----- ';
                //             // PurchaseOrder::where('purchase_order_no', $request->poNo)->delete();
                //         }
                        
                //     }

                //     $seletedPurchaseRequestRow = Input::get('seletedPurchaseRequestRow');
                //     // dd($seletedPurchaseRequestRow);
                //     foreach($seletedPurchaseRequestRow as $key2 => $row){
                //         $supplierIdTwo = Input::get('supplier_id_'.$row.'');
                        
                //             $purchaseOrderDataModel = PurchaseOrderData::where('id', $row)->where('supplier_id', $supplierIdTwo);
                //             // dd($purchaseOrderDataModel->get()->toArray(), $row, $supplierIdTwo);
                //             if($purchaseOrderDataModel->count() >= 1 ){
                //                 // dd($key, $purchaseOrderModel);
                //                 $skipOrNot = Input::get('option_'.$row);                                
                //                 // dd($key2, $skipOrNot, 1);
                //                 if ($skipOrNot == '2') {
                //                     $tempReturn .= 'The purchase order data'. ' ' . 'exist but skipped  ----- ';  
                //                     // dd($key2, $skipOrNot, 1);                                  
                                    
                //                 } else {
                //                     if ($supplierIdTwo == $row1) {
                                        
                //                         $tempReturn .= $skipOrNot;
                //                         $tempReturn .= 'the purchase order data'. ' ' . 'exist will update  ----- ';  
                //                     }
                //                     // dd('in1', $skipOrNot, $row, $key2, $newSupplierIds, $seletedPurchaseRequestRow);
                //                 }
                //             }else{
                //                 $skipOrNot = Input::get('option_'.$row); 
                //                 if ($skipOrNot == '2') {
                //                     $tempReturn .= 'TEST SKIP';  
                //                     // dd($key2, $skipOrNot, 1);                                  
                                    
                //                 }                               
                //                 $tempReturn .= $skipOrNot;
                //                 $tempReturn .= 'the purchase order data does not exist will update new PO data';  
                //                 // // dd($skipOrNot, 2);
                //                 // if ($skipOrNot == 2) {
                //                 //     // dd($skipOrNot, 2);                                                                          
                //                 // } else {
                //                 //     // dd('in');
                                    
                //                 // }
                //             }
                        
                //     }                    
                // }
                // dd($tempReturn);
                // dd($newSupplierIds, $request->poNo, $requestData, $allNewSuppliersCount->count());
                //return $newSupplierIds;
                $newPurchaseOrderCreated = false;
                $temp = '';
                foreach ($newSupplierIds as $key => $row1){
                    
                        $supplierId = $row1;
                        $debitAccId = CommonFacades::getAccountIdByMasterTable($m,$supplierId,'supplier');
                        $str = DB::selectOne("select max(convert(substr(`purchase_order_no`,3,length(substr(`purchase_order_no`,3))-4),signed integer)) reg from `purchase_order` where substr(`purchase_order_no`,-4,2) = ".date('m')." and substr(`purchase_order_no`,-2,2) = ".date('y')."")->reg;
                        $purchaseOrderNo = 'PO'.($str+1).date('my').'';
                        $prNo = Input::get('prNo');
                        $prDate = Input::get('prDate');
                        $subDepartmentId = Input::get('subDepartmentId');
                        $locationId = Input::get('locationId');
                        $departmentId = Input::get('departmentId');
                        $projectId = Input::get('projectId');
                        $po_date = date("Y-m-d", strtotime(Input::get('po_date')));
                        $delivery_place = Input::get('delivery_place');
                        $qoutation_no = Input::get('qoutation_no');
                        $qoutation_date = Input::get('qoutation_date');
                        $main_description = Input::get('main_description');
                        $initialEmailAddress = Input::get('initialEmailAddress');
                        $purchaseOrderVoucherType = Input::get('purchase_order_voucher_type');
                        $paymentType = Input::get('paymentType');
                        $payment_type_rate = Input::get('payment_type_rate');
                        $po_note = Input::get('po_note');
                        $po_discount = Input::get('po_discount');
                        $termCondition = Input::get('termCondition');
                        
                        // if($key == 1){
                        //     $purchaseOrderModel = PurchaseOrder::where('purchase_order_no', $request->poNo)->where('supplier_id', $supplierId);
                        //     // dd($purchaseOrderModel->count(), $key);
                        // }
                        $purchaseOrderModel = PurchaseOrder::where('purchase_order_no', $request->poNo)->where('supplier_id', $supplierId);
                        // dd($purchaseOrderModel->get(), $key);
                        if($purchaseOrderModel->count() >= 1 && $allNewSuppliersCount > 0 ){
                            // $purchaseOrderNo = $request->poNo;
                            $data1['purchase_order_no'] = $request->poNo;
                            $data1['supplier_id'] = $supplierId;
                            $data1['description'] = $main_description;
                            $data1['username'] = Auth::user()->name;
                            $data1['user_id'] = Auth::user()->id;
                            $data1['date'] = date("Y-m-d");
                            $data1['time'] = date("H:i:s");
                            $data1['delivery_place'] = $delivery_place;
                            $data1['qoutation_no'] = $qoutation_no;
                            $data1['paymentType'] = $paymentType;
                            $data1['term_and_condition'] = $termCondition;
                            $data1['payment_type_rate'] = $payment_type_rate;
                            $data1['po_note'] = $po_note;
                            $data1['custom_tax_percent'] = Input::get('tax_percent', 0);
                            $data1['tax_type'] = Input::get('tax_type');
                            $data1['account_id'] = $saleTaxAccCode->id ?? 0;
                            $data1['po_discount'] = $po_discount ?? 0;
                            
                            // dd($newSupplierIds, $request, $requestData, $purchaseOrderModel->get());
                            $purchaseOrderModel->update($data1);
                            $newPurchaseOrderCreated = false;
                        }else{
                            // if (count($newSupplierIds) <= 1) {
                            //     dd('check');
                            // }
                            // dd('out', $purchaseOrderModel->count(), $supplierId);
                            $data1['purchase_order_no'] = $purchaseOrderNo;
                            $data1['purchase_order_date'] = $po_date;
                            $data1['purchase_request_no'] = $prNo;
                            $data1['purchase_request_date'] = $prDate;
                            $data1['delivery_place'] = $delivery_place;
                            $data1['qoutation_no'] = $qoutation_no;
                            $data1['qoutation_date'] = $qoutation_date;
                            if($purchaseOrderVoucherType == 2){
                                $data1['voucher_type'] = '2';
                            }else{
                                $data1['voucher_type'] = '1';
                            }
                            $data1['location_id'] = $locationId;
                            $data1['department_id'] = $departmentId;
                            $data1['sub_department_id'] = $subDepartmentId;
                            $data1['supplier_id'] = $supplierId;
                            $data1['project_id'] = $projectId;
                            $data1['description'] = $main_description;
                            $data1['username'] = Auth::user()->name;
                            $data1['user_id'] = Auth::user()->id;
                            $data1['date'] = date("Y-m-d");
                            $data1['time'] = date("H:i:s");
                            $data1['purchase_order_status'] = 1;
                            $data1['company_id'] = $m;
                            $data1['accounting_year']     	 = Session::get('accountYear');
                            $data1['paymentType'] = $paymentType;
                            $data1['payment_type_rate'] = $payment_type_rate;
                            $data1['po_note'] = $po_note; 
                            $data1['custom_tax_percent'] = Input::get('tax_percent', 0);
                            $data1['account_id'] = $saleTaxAccCode->id ?? 0;
                            $data1['po_discount'] = $po_discount ?? 0;                   
                            if ($allNewSuppliersCount == 0) {
                                $data1['purchase_order_no'] = $request->poNo;
                                PurchaseOrder::where('purchase_order_no', $request->poNo)->update($data1);
                                $newPurchaseOrderCreated = false;
                            }else{
                                // dd('in dsadsads');
                                DB::table('purchase_order')->insert($data1);
                                $newPurchaseOrderCreated = true;
                            }
                        }

                        $seletedPurchaseRequestRow = Input::get('seletedPurchaseRequestRow');
                        // dd($seletedPurchaseRequestRow);
                        foreach($seletedPurchaseRequestRow as $key2 => $row){
                            $supplierIdTwo = Input::get('supplier_id_'.$row.'');
                            $purchaseOrderDataModel = PurchaseOrderData::where('id', $row)->where('supplier_id', $supplierIdTwo);
                            $skipOrNot = Input::get('option_'.$row);
                                
                            $purchaseOrderDataId = (int)Input::get('purchaseOrderDataId_'.$row);
                            $categoryId = Input::get('categoryId_'.$row.'');
                            $subItemId = Input::get('subItemId_'.$row.'');
                            // dd($key2, $skipOrNot, 1);
                            if ($skipOrNot == '2') {  
                                // dd($key2, $skipOrNot, 1);                                  
                                DB::table('purchase_order_data')->where('id', $purchaseOrderDataId)->delete();
                                DB::table('purchase_request_data')
                                ->where('category_id', $categoryId)
                                ->where('sub_item_id', $subItemId)
                                ->where('purchase_request_no', $prNo)
                                ->where('id',$row)
                                ->update(['purchase_order_status' => "1"]);
                            } else {
                                // dd('in1', $skipOrNot, $row, $key2, $newSupplierIds, $seletedPurchaseRequestRow);
                                $saleTaxHeadCheckbox = Input::get('sale_tax_head_checkbox_'.$row.'');
                                $saleTaxHead = Input::get('sale_tax_head_'.$row.'');
                                $qoutation_no = Input::get('qoutation_no_'.$row.'');
                                $qoutation_date = Input::get('qoutation_date_'.$row.'');
                                $delivery_days = Input::get('delivery_days_'.$row.'');
                                $payment_terms = Input::get('payment_terms_'.$row.'');
                                
                                $supplierIdTwo = Input::get('supplier_id_'.$row.'');
                                $remaining_purchase_order_qty = Input::get('remaining_purchase_order_qty_'.$row.'');
                                $unit = Input::get('unit_'.$row.'');
                                $sub_total_with_persent = Input::get('sub_total_with_persent_'.$row.'');
                                
                                $data2['category_id'] = $categoryId;
                                $data2['sub_item_id'] = $subItemId;
                                $data2['qoutation_no'] = $qoutation_no;
                                $data2['qoutation_date'] = $qoutation_date;
                                $data2['supplier_id'] = $supplierIdTwo;
                                $data2['delivery_days'] = $delivery_days;
                                $data2['payment_terms'] = $payment_terms;
                                $data2['username'] = Auth::user()->name;
                                $data2['user_id'] = Auth::user()->id;
                                $data2['date'] = date("Y-m-d");
                                $data2['time'] = date("H:i:s");
                                $data2['company_id'] = $m;
                                $locationTwo = Input::get('location_id_'.$row.'_1');
                                $purchaseOrderQty = Input::get('purchase_order_qty_'.$row.'_1');
                                $unitPriceTwo = Input::get('unit_price_'.$row.'_1');
                                $subTotal = Input::get('sub_total_'.$row.'_1');
                                
                                
                                $data2['purchase_order_qty'] = $purchaseOrderQty;
                                $data2['unit_price'] = number_format($unitPriceTwo*$payment_type_rate,3, '.', '');
                                $data2['sub_total'] = number_format($subTotal*$payment_type_rate,3, '.', '');
                                $data2['location_id'] = $locationTwo;

                                if($purchaseOrderDataModel->count() >= 1 ){
                                    // dd($key, $purchaseOrderModel);                                
                                        if ($supplierIdTwo == $row1) { 
                                            $data2['purchase_order_no'] = $request->poNo;
                                            // $temp .= $key. $key2.'isme same supllier or po hoga'. $data2['purchase_order_no']. "\r\n";                                                                          
                                            DB::table('purchase_order_data')->where('id', $purchaseOrderDataId)->update($data2);
                                        }
                                        // dd($key, $purchaseOrderNo, $data2,DB::table('purchase_order_data')->where('id', $purchaseOrderDataId)->get());
                                        // dd(DB::table('purchase_order_data')->where('id', $purchaseOrderDataId)->update($data2));
                                    
                                }else{
                                    // dd('in', $key, $key2, $row);
                                    if ($allNewSuppliersCount == 0) {
                                        // dd($key, $key2, 'in1 test');
                                        $data2['purchase_order_no'] = $request->poNo;
                                        // $temp .= 'sare alag hain same PO update hoga'. $data2['purchase_order_no']. "\r\n";
                                    } else {
                                        //  dd($key, $key2, 'in2 test');
                                        $data2['purchase_order_no'] = $purchaseOrderNo;
                                        // $temp .= $key. $key2. 'isme alag supllier or po hoga' . $data2['purchase_order_no']. "\r\n";
                                                            
                                    }
                                    if ($supplierIdTwo == $row1) { 
                                        // $temp .= 'adding item to new PO'. $data2['purchase_order_no']. "\r\n";
                                        DB::table('purchase_order_data')->where('id', $purchaseOrderDataId)->update($data2);
                                    }
                                    
                                }
                            }
                        }
                        
                    }
                    // dd(nl2br($temp));
                    // $msg = 'Add New Purchase Order. Purchase Request No => '.$prNo.' and Purchase Order No => '.$purchaseOrderNo.' and Purchase Order Date => '.$po_date.'';
                    // CommonFacades::addEmailNotificationDetailOptionWise($m,'purchase_order_notification_setting',1,$locationId,'Purcahse Order','Add Purcahse Order',$msg,$initialEmailAddress);
                
           
                return redirect()->to('store/viewPurchaseOrderList?pageType='.Input::get('pageType').'&&parentCode='.Input::get('parentCode').'#SFR')->with('success','Add Purchase Order Successfully!');
        } catch (\Exception $e) {
            // dd($e->getMessage(), $e);
            return redirect()->back()->with('error','Oops! There might be a issue '. $e->getMessage());
        }
    }

    public function addPurchaseOrderDetailDirect(Request $request){
        try{
            //dd($request->all());
            //Start Purchase Request Section
                $m = Input::get('m');
                $DepartmentAndSubDepartmentDetail = explode('<*>',Input::get('sub_department_id_1'));
                $purchase_request_date = date("Y-m-d", strtotime(strip_tags(Input::get('po_date'))));
                $purchase_request_type = 1;
                $description = strip_tags(Input::get('main_description'));
                $sub_department_id = strip_tags($DepartmentAndSubDepartmentDetail[0]);
                $departmentId = $DepartmentAndSubDepartmentDetail[1];
                $location_id = strip_tags(Input::get('locationId'));
                $projectId = strip_tags(Input::get('projectId'));
                $purchaseRequestDataSection = Input::get('seletedPurchaseRequestRow');

                $purchase_request_type = 1;
                $str = DB::selectOne("select max(convert(substr(`purchase_request_no`,3,length(substr(`purchase_request_no`,3))-4),signed integer)) reg from `purchase_request` where substr(`purchase_request_no`,-4,2) = " . date('m') . " and substr(`purchase_request_no`,-2,2) = " . date('y') . "")->reg;
                $purchase_request_no = 'PR' . ($str + 1) . date('my');

                $data1['purchase_request_no'] = $purchase_request_no;
                $data1['purchase_request_date'] = $purchase_request_date;
                $data1['purchase_request_type'] = $purchase_request_type;
                $data1['description'] = $description;
                $data1['department_id'] = $departmentId;
                $data1['sub_department_id'] = $sub_department_id;
                $data1['project_id'] = $projectId;
                $data1['location_id'] = $location_id;
                $data1['date'] = date("Y-m-d");
                $data1['time'] = date("H:i:s");
                $data1['user_id'] = Auth::user()->id;
                $data1['username'] = Auth::user()->name;
                $data1['status'] = 1;
                $data1['purchase_request_status'] = 2;
                $data1['approve_date'] = date("Y-m-d");
                $data1['approve_time'] = date("H:i:s");
                $data1['approve_user_id'] = Auth::user()->id;
                $data1['approve_username'] = Auth::user()->name;
                $data1['company_id'] = $m;
                $data1['accounting_year']     	 = Session::get('accountYear');

                DB::table('purchase_request')->insert($data1);
                foreach ($purchaseRequestDataSection as $row2) {
                    $category_id = strip_tags(Input::get('categoryId_' . $row2 . '_1'));
                    $sub_item_id = strip_tags(Input::get('subItemId_' . $row2 . '_1'));
                    $qty = strip_tags(Input::get('purchase_order_qty_' . $row2 . '_1'));
                    $required_date_data = $purchase_request_date;


                    $data2['purchase_request_no'] = $purchase_request_no;
                    $data2['purchase_request_date'] = $purchase_request_date;
                    $data2['category_id'] = $category_id;
                    $data2['sub_item_id'] = $sub_item_id;
                    $data2['required_date'] = $required_date_data;
                    $data2['qty'] = $qty;
                    $data2['approx_cost'] = 0;
                    $data2['date'] = date("Y-m-d");
                    $data2['time'] = date("H:i:s");
                    $data2['user_id'] = Auth::user()->id;
                    $data2['username'] = Auth::user()->name;
                    $data2['status'] = 1;
                    $data2['purchase_request_status'] = 2;
                    $data2['company_id'] = $m;
                    $data2['approve_username'] = Auth::user()->name;
                    $data2['accounting_year']     	 = Session::get('accountYear');

                    DB::table('purchase_request_data')->insert($data2);
                    
                }
            //End Purchase Request Section


            //Start Purchase Order Section
                $seletedSupplierRow = Input::get('seletedPurchaseRequestRow');
                $newSupplierIds = array();
                $saleTaxAccCode = Account::where('code', Input::get('sales_tax_acc_code'))->first();
                // dd($saleTaxAccCode, Input::get('tax_percent'));
                foreach($seletedSupplierRow as $row) {
                    $newSupplierIds[] = Input::get('supplier_id_' . $row . '_1');
                }
                foreach ($newSupplierIds as $row1){
                    
                    $supplierId = $row1;
                    // dd($supplierId, $newSupplierIds, $seletedSupplierRow);
                    $debitAccId = CommonFacades::getAccountIdByMasterTable($m,$supplierId,'supplier');
                    $str = DB::selectOne("select max(convert(substr(`purchase_order_no`,3,length(substr(`purchase_order_no`,3))-4),signed integer)) reg from `purchase_order` where substr(`purchase_order_no`,-4,2) = ".date('m')." and substr(`purchase_order_no`,-2,2) = ".date('y')."")->reg;
                    $purchaseOrderNo = 'PO'.($str+1).date('my').'';
                    $prNo = $purchase_request_no;
                    $prDate = date("Y-m-d", strtotime(Input::get('po_date')));                
                    $locationId = Input::get('locationId');                
                    $projectId = Input::get('projectId');
                    $po_date = date("Y-m-d", strtotime(Input::get('po_date')));
                    $delivery_place = Input::get('delivery_place');
                    $qoutation_no = Input::get('qoutation_no');
                    $main_description = Input::get('main_description');
                    $initialEmailAddress = '-';Input::get('initialEmailAddress');
                    $purchaseOrderVoucherType = 1;
                    $paymentType = Input::get('paymentType');
                    $payment_type_rate = Input::get('payment_type_rate');
                    $po_note = Input::get('po_note');
                    $poType = Input::get('po_type');
                    $data3['purchase_order_no'] = $purchaseOrderNo;
                    $data3['purchase_order_date'] = $po_date;
                    $data3['purchase_request_no'] = $prNo;
                    $data3['purchase_request_date'] = $prDate;
                    $data3['delivery_place'] = $delivery_place;
                    $data3['qoutation_no'] = $qoutation_no;
                    $data3['term_and_condition'] = Input::get('termCondition');
                    $data3['paymentType'] = $paymentType;
                    $data3['payment_type_rate'] = $payment_type_rate;
                    $data3['po_note'] = $po_note;
                    $data3['custom_tax_percent'] = Input::get('tax_percent', 0);
                    $data3['tax_type'] = Input::get('tax_type');
                    $data3['account_id'] = $saleTaxAccCode->id ?? 0;


                    if($purchaseOrderVoucherType == 2){
                        $data3['voucher_type'] = '2';
                    }else{
                        $data3['voucher_type'] = '1';
                    }
                    $data3['location_id'] = $locationId;
                    $data3['department_id'] = $departmentId;
                    $data3['sub_department_id'] = $sub_department_id;
                    $data3['supplier_id'] = $supplierId;
                    $data3['project_id'] = $projectId;
                    $data3['description'] = $main_description;
                    $data3['username'] = Auth::user()->name;
                    $data3['user_id'] = Auth::user()->id;
                    $data3['date'] = date("Y-m-d");
                    $data3['time'] = date("H:i:s");
                    $data3['purchase_order_status'] = 1;
                    $data3['po_type'] = $poType;
                    $data3['company_id'] = $m;
                    $data3['accounting_year']     	 = Session::get('accountYear');
                    $po_id = DB::table('purchase_order')->insertGetId($data3);

                    $seletedPurchaseRequestRow = Input::get('seletedPurchaseRequestRow');
                    foreach($seletedPurchaseRequestRow as $row){

                        

                        $saleTaxHeadCheckbox = Input::get('sale_tax_head_checkbox_'.$row.'_1');
                        $saleTaxHead = Input::get('sale_tax_head_'.$row.'_1');
                        $qoutation_no = Input::get('qoutation_no_'.$row.'_1');
                        $qoutation_date = Input::get('qoutation_date_'.$row.'_1');
                        $delivery_days = Input::get('delivery_days_'.$row.'_1', 30);
                        $payment_terms = Input::get('payment_terms_'.$row.'_1');
                        
                        $categoryId = Input::get('categoryId_'.$row.'_1');
                        $subItemId = Input::get('subItemId_'.$row.'_1');
                        $purchaseOrderQty = Input::get('purchase_order_qty_'.$row.'_1');

                        $getPurchaseRequestDataDetail = DB::table('purchase_request_data')
                            ->where('purchase_request_no',$prNo)
                            ->where('category_id',$categoryId)
                            ->where('sub_item_id',$subItemId)
                            ->where('qty',$purchaseOrderQty)
                            ->first();
                        //dd($getPurchaseRequestDataDetail);
                        
                        $supplierIdTwo = Input::get('supplier_id_'.$row.'_1');
                        $remaining_purchase_order_qty = Input::get('remaining_purchase_order_qty_'.$row.'_1');
                        $unit = Input::get('unit_'.$row.'_1');
                        $sub_total_with_persent = Input::get('sub_total_with_persent_'.$row.'_1');
                        $data4['purchase_order_no'] = $purchaseOrderNo;
                        $data4['purchase_order_date'] = $po_date;
                        $data4['purchase_request_no'] = $prNo;
                        $data4['purchase_request_data_record_id'] = $getPurchaseRequestDataDetail->id;
                        $data4['purchase_request_date'] = $prDate;
                        if($saleTaxHeadCheckbox == '1'){
                            $data4['sale_tax_status'] = $saleTaxHeadCheckbox;
                            $data4['sale_tax_head'] = $saleTaxHead;
                            $data4['unit'] = $unit;
                            $data4['sub_total_with_persent'] = $sub_total_with_persent;
                        }else if($saleTaxHeadCheckbox == '2'){
                            $data4['sale_tax_status'] = $saleTaxHeadCheckbox;
                            $data4['sale_tax_head'] = '0';
                            $data4['unit'] = '0';
                            $data4['sub_total_with_persent'] = '0';
                        }
                        $data4['category_id'] = $categoryId;
                        $data4['sub_item_id'] = $subItemId;
                        $data4['qoutation_no'] = $qoutation_no;
                        $data4['qoutation_date'] = $qoutation_date;
                        $data4['supplier_id'] = $supplierId;
                        $data4['delivery_days'] = $delivery_days;
                        $data4['payment_terms'] = $payment_terms ?? '-';
                        $data4['username'] = Auth::user()->name;
                        $data4['user_id'] = Auth::user()->id;
                        $data4['date'] = date("Y-m-d");
                        $data4['time'] = date("H:i:s");
                        $data4['purchase_order_status'] = 1;
                        $data4['accounting_year']     	 = Session::get('accountYear');
                        $data4['company_id'] = $m;
                        $locationTwo = $locationId;
                        
                        $unitPriceTwo = Input::get('unit_price_'.$row.'_1');
                        $subTotal = Input::get('sub_total_'.$row.'_1');
                        $data4['purchase_order_qty'] = $purchaseOrderQty;
                        $data4['unit_price'] = number_format($unitPriceTwo*$payment_type_rate,3, '.', '');
                        $data4['sub_total'] = number_format($subTotal*$payment_type_rate,3, '.', '');
                        $data4['location_id'] = $locationTwo;
                        if($supplierIdTwo == $row1) {
                            DB::table('purchase_order_data')->insert($data4);
                            $previousQty = CommonFacades::checkPriviousReceiveQtyPurchaseRequestWise($categoryId, $subItemId, $prNo);
                            $purchaseRequestQty = Input::get('purchase_request_qty_'.$row.'_1');
                            $overAllQunatity = $purchaseRequestQty+$previousQty;
                            $demandQty = $purchaseOrderQty+$previousQty;
                                    DB::table('purchase_request_data')
                                        ->where('category_id', $categoryId)
                                        ->where('sub_item_id', $subItemId)
                                        ->where('purchase_request_no', $prNo)
                                        ->where('id',$row)
                                        ->update(['purchase_order_status' => "2"]);
                        }
                    }
                }
                //dd($supplierIds);
            //End Purchaes Order Section
            return redirect()->to('store/viewPurchaseOrderList?pageType='.Input::get('pageType').'&&parentCode='.Input::get('parentCode').'#SFR')->with('success','Add Purchase Order Successfully!');
        }catch (\Exception $e) {
            dd($e->getMessage(), $e->getTraceAsString());
            return redirect()->back()->with('error','Oops! There might be a issue '. $e->getMessage());
        }
    }

    public function addPurchaseOrderDetail(){
        // dd(Input::all());       
        date_default_timezone_set("Asia/Karachi");
        try{
            $m = Input::get('m');
            $seletedSupplierRow = Input::get('seletedPurchaseRequestRow');
            $supplierIds = array();
            $saleTaxAccCode = Account::where('code', Input::get('sales_tax_acc_code'))->first();
            // dd($saleTaxAccCode, Input::get('tax_percent'));
            foreach($seletedSupplierRow as $row) {
                $supplierIds[] = Input::get('supplier_id_' . $row . '');
            }
            // dd($supplierIds);
            $newSupplierIds = array();
            foreach($supplierIds as $supplierIdItems) {
                foreach($newSupplierIds as $newSupplierIdItems) {
                    if($supplierIdItems == $newSupplierIdItems) {
                        continue 2;
                    }
                }
                $newSupplierIds[] = $supplierIdItems;
            }
            //return $newSupplierIds;
            foreach ($newSupplierIds as $row1){

                if (Input::get('po_type') == 'direct') {
                    $DepartmentAndSubDepartmentDetail = explode('<*>',Input::get('sub_department_id_1'));
                    // dd($DepartmentAndSubDepartmentDetail);
                    $subDepartmentId = strip_tags($DepartmentAndSubDepartmentDetail[0]);
                    $departmentId = $DepartmentAndSubDepartmentDetail[1];
                }else{
                    $subDepartmentId = Input::get('subDepartmentId');
                    $departmentId = Input::get('departmentId');
                }
                $supplierId = $row1;
                // dd($supplierId, $newSupplierIds, $seletedSupplierRow);
                $debitAccId = CommonFacades::getAccountIdByMasterTable($m,$supplierId,'supplier');
                $str = DB::selectOne("select max(convert(substr(`purchase_order_no`,3,length(substr(`purchase_order_no`,3))-4),signed integer)) reg from `purchase_order` where substr(`purchase_order_no`,-4,2) = ".date('m')." and substr(`purchase_order_no`,-2,2) = ".date('y')."")->reg;
                $purchaseOrderNo = 'PO'.($str+1).date('my').'';
                $prNo = Input::get('prNo', '');
                $prDate = Input::get('prDate', '0000-00-00');                
                $locationId = Input::get('locationId');                
                $projectId = Input::get('projectId');
                $po_date = date("Y-m-d", strtotime(Input::get('po_date')));
                $delivery_place = Input::get('delivery_place');
                $qoutation_no = Input::get('qoutation_no');
                $qoutation_date = Input::get('qoutation_date');
                $main_description = Input::get('main_description');
                $initialEmailAddress = Input::get('initialEmailAddress');
                $purchaseOrderVoucherType = Input::get('purchase_order_voucher_type');
                $paymentType = Input::get('paymentType');
                $payment_type_rate = Input::get('payment_type_rate');
                $poDiscount = Input::get('po_discount');
                $po_note = Input::get('po_note');
                $poType = Input::get('po_type');
                $data1['purchase_order_no'] = $purchaseOrderNo;
                $data1['purchase_order_date'] = $po_date;
                $data1['purchase_request_no'] = $prNo;
                $data1['purchase_request_date'] = $prDate;
                $data1['delivery_place'] = $delivery_place;
                $data1['qoutation_no'] = $qoutation_no;
                $data1['qoutation_date'] = $qoutation_date;
                $data1['term_and_condition'] = Input::get('termCondition');
                $data1['paymentType'] = $paymentType;
                $data1['payment_type_rate'] = $payment_type_rate;
                $data1['po_note'] = $po_note;
                $data1['custom_tax_percent'] = Input::get('tax_percent', 0);
                $data1['tax_type'] = Input::get('tax_type');
                $data1['account_id'] = $saleTaxAccCode->id ?? 0;
                $data1['po_discount'] = $poDiscount ?? 0;


                if($purchaseOrderVoucherType == 2){
                    $data1['voucher_type'] = '2';
                }else{
                    $data1['voucher_type'] = '1';
                }
                $data1['location_id'] = $locationId;
                $data1['department_id'] = $departmentId;
                $data1['sub_department_id'] = $subDepartmentId;
                $data1['supplier_id'] = $supplierId;
                $data1['project_id'] = $projectId;
                $data1['description'] = $main_description;
                $data1['username'] = Auth::user()->name;
                $data1['user_id'] = Auth::user()->id;
                $data1['date'] = date("Y-m-d");
                $data1['time'] = date("H:i:s");
                $data1['purchase_order_status'] = 1;
                $data1['po_type'] = $poType;
                $data1['company_id'] = $m;
                $data1['accounting_year']     	 = Session::get('accountYear');
                $po_id = DB::table('purchase_order')->insertGetId($data1);

                $seletedPurchaseRequestRow = Input::get('seletedPurchaseRequestRow');
                foreach($seletedPurchaseRequestRow as $row){
                    $saleTaxHeadCheckbox = Input::get('sale_tax_head_checkbox_'.$row.'');
                    $saleTaxHead = Input::get('sale_tax_head_'.$row.'');
                    $qoutation_no = Input::get('qoutation_no_'.$row.'');
                    $qoutation_date = Input::get('qoutation_date_'.$row.'');
                    $delivery_days = Input::get('delivery_days_'.$row.'', 30);
                    $payment_terms = Input::get('payment_terms_'.$row.'');
                    if ($poType == "direct") {                        
                        $categoryId = 0;
                        $subItemId = 0;
                        $data2['item_name'] = Input::get('subItemId_'.$row.'');
                    } else {
                        $categoryId = Input::get('categoryId_'.$row.'');
                        $subItemId = Input::get('subItemId_'.$row.'');                        
                    }
                    
                    $supplierIdTwo = Input::get('supplier_id_'.$row.'');
                    $remaining_purchase_order_qty = Input::get('remaining_purchase_order_qty_'.$row.'');
                    $unit = Input::get('unit_'.$row.'');
                    $sub_total_with_persent = Input::get('sub_total_with_persent_'.$row.'');
                    $data2['purchase_order_no'] = $purchaseOrderNo;
                    $data2['purchase_order_date'] = $po_date;
                    $data2['purchase_request_no'] = $prNo;
                    $data2['purchase_request_data_record_id'] = $row;
                    $data2['purchase_request_date'] = $prDate;
                    if($saleTaxHeadCheckbox == '1'){
                        $data2['sale_tax_status'] = $saleTaxHeadCheckbox;
                        $data2['sale_tax_head'] = $saleTaxHead;
                        $data2['unit'] = $unit;
                        $data2['sub_total_with_persent'] = $sub_total_with_persent;
                    }else if($saleTaxHeadCheckbox == '2'){
                        $data2['sale_tax_status'] = $saleTaxHeadCheckbox;
                        $data2['sale_tax_head'] = '0';
                        $data2['unit'] = '0';
                        $data2['sub_total_with_persent'] = '0';
                    }
                    $data2['category_id'] = $categoryId;
                    $data2['sub_item_id'] = $subItemId;
                    $data2['qoutation_no'] = $qoutation_no;
                    $data2['qoutation_date'] = $qoutation_date;
                    $data2['supplier_id'] = $supplierId;
                    $data2['delivery_days'] = $delivery_days;
                    $data2['payment_terms'] = $payment_terms;
                    $data2['username'] = Auth::user()->name;
                    $data2['user_id'] = Auth::user()->id;
                    $data2['date'] = date("Y-m-d");
                    $data2['time'] = date("H:i:s");
                    $data2['purchase_order_status'] = 1;
                    $data2['accounting_year']     	 = Session::get('accountYear');
                    $data2['company_id'] = $m;
                    if ($poType == 'direct') {
                        $locationTwo = $locationId;
                    }else{
                        $locationTwo = Input::get('location_id_'.$row.'_1');
                    }
                    
                    $purchaseOrderQty = Input::get('purchase_order_qty_'.$row.'_1');
                    $unitPriceTwo = Input::get('unit_price_'.$row.'_1');
                    $subTotal = Input::get('sub_total_'.$row.'_1');
                    $data2['purchase_order_qty'] = $purchaseOrderQty;
                    $data2['unit_price'] = number_format($unitPriceTwo*$payment_type_rate,3, '.', '');
                    $data2['sub_total'] = number_format($subTotal*$payment_type_rate,3, '.', '');
                    $data2['location_id'] = $locationTwo;
                    if($supplierIdTwo == $row1) {
                        DB::table('purchase_order_data')->insert($data2);
                        $previousQty = CommonFacades::checkPriviousReceiveQtyPurchaseRequestWise($categoryId, $subItemId, $prNo);
                        $purchaseRequestQty = Input::get('purchase_request_qty_'.$row.'_1');
                        // echo $overAllQunatity = $previousQty;
                        // echo '<br />';
                        // echo $purchaseRequestQty;
                        // echo '<br />';
                        // echo ($purchaseRequestQty+$previousQty);
                        $overAllQunatity = $purchaseRequestQty+$previousQty;
                        $demandQty = $purchaseOrderQty+$previousQty;
                        // dd($previousQty, $purchaseRequestQty, $purchaseOrderQty, $overAllQunatity);
                        if (Input::get('po_type') == 'pr') {                            
                            if ($demandQty >= $overAllQunatity) {       
                                // dd('in', $overAllQunatity, $purchaseOrderQty);                 
                                DB::table('purchase_request_data')
                                    ->where('category_id', $categoryId)
                                    ->where('sub_item_id', $subItemId)
                                    ->where('purchase_request_no', $prNo)
                                    ->where('id',$row)
                                    ->update(['purchase_order_status' => "2"]);
                            }
                        }
                        // dd('in2', $overAllQunatity, $purchaseOrderQty);
                    }
                }
                $expense_amounts = Input::get('expenseArray');
                if (input::get('expense_added')) {
                    foreach ($expense_amounts as $key => $row) {
                        $expenseAmount = Input::get('expense_amount_'.$row.'');
                        $expense_head_id = Input::get('expense_head_id_'.$row.'');
                        $expenseData = PurchaseOrderExpenseData::create([
                            'purchase_order_id' => $po_id,
                            'purchase_order_no' => $purchaseOrderNo,
                            'expense_head_id' => $expense_head_id,
                            'expense_amount' => $expenseAmount
                        ]);
                    }
                }
                $msg = 'Add New Purchase Order. Purchase Request No => '.$prNo.' and Purchase Order No => '.$purchaseOrderNo.' and Purchase Order Date => '.$po_date. ' created by '.Auth::user()->name;
            }
            return redirect()->to('store/viewPurchaseOrderList?pageType='.request('pageType').'&&parentCode='.request('parentCode').'#SFR')->with('success','Add Purchase Order Successfully!');
        }catch (\Exception $e) {
            dd($e->getMessage(), $e->getTraceAsString());
            return redirect()->back()->with('error','Oops! There might be a issue '. $e->getMessage());
        }
    }

    //ERP New

    public function addStoreChallanDetail(){
        // dd(Input::all());
        date_default_timezone_set("Asia/Karachi");
        try{
            $m = CommonFacades::getSessionCompanyId();
            $str = DB::selectOne("select max(convert(substr(`store_challan_no`,3,length(substr(`store_challan_no`,3))-4),signed integer)) reg from `store_challan` where substr(`store_challan_no`,-4,2) = ".date('m')." and substr(`store_challan_no`,-2,2) = ".date('y')."")->reg;
            $storeChallanNo = 'SC'.($str+1).date('my');
            $mrNo = Input::get('mrNo');
            $mrDate = Input::get('mrDate');
            $subDepartmentId = Input::get('subDepartmentId');
            $fromSubDepartmentId = Input::get('from_sub_department_id');
            $locationId = Input::get('locationId');
            $projectId = Input::get('projectId');
            $store_challan_date = date("Y-m-d", strtotime(Input::get('store_challan_date')));
            $departmentId = Input::get('departmentId');
            // $fromDepartmentId = SubDepartment::where('id', $fromSubDepartmentId)->first()->department_id;            
            $pageType = Input::get('pageType');
            $parentCode = Input::get('parentCode');
            $main_description = Input::get('main_description');
            $initialEmailAddress = Input::get('initialEmailAddress');

            $data1['material_request_no']       = $mrNo;
            $data1['material_request_date']     = $mrDate;
            $data1['sub_department_id']         = $subDepartmentId;
            $data1['location_id']               = $locationId;
            $data1['project_id']                = $projectId;
            $data1['department_id']             = $departmentId;
            $data1['company_id']                = $m;
            $data1['warehouse_from_id']         = Input::get('warehouse_from_id');
            $data1['warehouse_to_id']           = $locationId;
            $data1['from_sub_department_id']    = $fromSubDepartmentId;
            $data1['purpose']                   = Input::get('challan_purpose');

            $data1['store_challan_no']          = $storeChallanNo;
            $data1['store_challan_date']        = $store_challan_date;
            
            $data1['description']               = $main_description;
            $data1['user_id'] 		 	        = Auth::user()->id;
            $data1['username'] 		 	        = Auth::user()->name;
            $data1['date']                      = date("Y-m-d");
            $data1['time']                      = date("H:i:s");
            $data1['store_challan_status']      = 1;
            $data1['accounting_year']     	    = Session::get('accountYear');

            DB::table('store_challan')->insert($data1);

            $storeChallanData = Input::get('storeChallanData');
            foreach ($storeChallanData as $row) {
                
                $categoryId = Input::get('category_id_'. $row.'');
                $subItemId = Input::get('sub_item_id_'. $row.'');
                $issueStatus = Input::get('issue_status_'. $row.'');
                $remainingStoreChallanQty = Input::get('remaining_store_challan_qty_'.$row.'');
                $storeChallanQty = Input::get('store_challan_qty_'.$row.'');
                $subDescription = Input::get('sub_description_'.$row.'');
                $item_type = Input::get('item_type_'.$row.'');
                if($issueStatus == 1){
                    $data2['store_challan_no'] = $storeChallanNo;
                    $data2['store_challan_date'] = $store_challan_date;
                    $data2['material_request_data_id'] = $row;
                    $data2['category_id'] = $categoryId;
                    $data2['sub_item_id'] = $subItemId;
                    $data2['item_type'] = $item_type;
                    $data2['issue_qty'] = $storeChallanQty;
                    $data2['username'] = Auth::user()->name;
                    $data2['user_id'] = Auth::user()->id;
                    $data2['date'] = date("Y-m-d");
                    $data2['time'] = date("H:i:s");
                    $data2['store_challan_status'] = 1;
                    $data2['accounting_year']     	    = Session::get('accountYear');
                    $data2['company_id']                = $m;
                    $data2['sub_description'] = $subDescription;

                    DB::table('store_challan_data')->insert($data2);
                    if($remainingStoreChallanQty == $storeChallanQty){
                        DB::table('material_request_data')->where('id',$row)->update(['store_challan_status' => 2]);
                    }
                }
            }
            $checkRemainingMaterialRequestData = DB::table('material_request_data')->where('material_request_no',$mrNo)->where('store_challan_status','2')->count();
            if($checkRemainingMaterialRequestData == 0){
                DB::table('material_request')->where('material_request_no',$mrNo)->update(['store_challan_status' => 2]);
            }
            return redirect()->to('store/viewStoreChallanList?pageType='.Input::get('pageType').'&&parentCode='.Input::get('parentCode').'#SFR')->with('success','Add Store Challan Successfully!');
        }catch (\Exception $e) {
            return redirect()->back()->with('error','Oops! There might be a issue '. $e->getMessage());
        }
    }

    


    public function addPurchaseRequestSaleDetail(){
        date_default_timezone_set("Asia/Karachi");
        $m = CommonFacades::getSessionCompanyId();
        CommonFacades::companyDatabaseConnection(CommonFacades::getSessionCompanyId());
        $str = DB::selectOne("select max(convert(substr(`purchase_request_no`,3,length(substr(`purchase_request_no`,3))-4),signed integer)) reg from `purchase_request` where substr(`purchase_request_no`,-4,2) = ".date('m')." and substr(`purchase_request_no`,-2,2) = ".date('y')."")->reg;
        $purchaseRequestNo = 'pr'.($str+1).date('my');
        $slip_no = Input::get('slip_no');
        $purchase_request_date = Input::get('purchase_request_date');
        $departmentId = Input::get('departmentId');
        $supplier_id = Input::get('supplier_id');
        $pageType = Input::get('pageType');
        $parentCode = Input::get('parentCode');
        $main_description = Input::get('main_description');
        $mainDemandType = Input::get('demandType');

        $data1['slip_no'] = $slip_no;
        $data1['purchase_request_no'] = $purchaseRequestNo;
        $data1['purchase_request_date'] = $purchase_request_date;
        $data1['demand_type'] = $mainDemandType;
        $data1['sub_department_id'] = $departmentId;
        $data1['supplier_id'] = $supplier_id;
        $data1['description'] = $main_description;
        $data1['username'] 		 	= Auth::user()->name;
        $data1['date'] = date("Y-m-d");
        $data1['time'] = date("H:i:s");
        $data1['purchase_request_status'] = 1;

        DB::table('purchase_request')->insert($data1);

        $seletedPurchaseRequestSaleRow = Input::get('seletedPurchaseRequestSaleRow');
        foreach ($seletedPurchaseRequestSaleRow as $row) {
            $demandNo = Input::get('demandNo_' . $row . '');
            $demandDate = Input::get('demandDate_' . $row . '');
            $demandType = Input::get('demandType_' . $row . '');
            $demandSendType = Input::get('demandSendType_' . $row . '');
            $categoryId = Input::get('categoryId_' . $row . '');
            $subItemId = Input::get('subItemId_' . $row . '');
            $purchase_request_qty = Input::get('purchase_request_qty_' . $row . '');
            $purchase_request_rate = Input::get('purchase_request_rate_' . $row . '');

            $purchase_request_sub_total = $purchase_request_qty*$purchase_request_rate;

            $data2['purchase_request_no'] = $purchaseRequestNo;
            $data2['purchase_request_date'] = $purchase_request_date;
            $data2['demand_no'] = $demandNo;
            $data2['demand_date'] = $demandDate;
            $data2['demand_type'] = $demandType;
            $data2['demand_send_type'] = $demandSendType;
            $data2['category_id'] = $categoryId;
            $data2['sub_item_id'] = $subItemId;
            $data2['purchase_request_qty'] = $purchase_request_qty;
            $data2['rate'] = $purchase_request_rate;
            $data2['sub_total'] = $purchase_request_sub_total;
            $data2['username'] = Auth::user()->name;
            $data2['date'] = date("Y-m-d");
            $data2['time'] = date("H:i:s");
            $data2['purchase_request_status'] = 1;

            DB::table('purchase_request_data')->insert($data2);
        }
        CommonFacades::reconnectMasterDatabase();
        Session::flash('dataInsert','successfully saved.');
        return Redirect::to('store/viewPurchaseRequestSaleList?pageType='.Input::get('pageType').'&&parentCode='.Input::get('parentCode').'#SFR');
    }


    public function addStoreChallanReturnDetail(){
        //dd(Input::all());
        date_default_timezone_set("Asia/Karachi");
        $m = CommonFacades::getSessionCompanyId();
        CommonFacades::companyDatabaseConnection(CommonFacades::getSessionCompanyId());
        $str = DB::selectOne("select max(convert(substr(`store_challan_return_no`,4,length(substr(`store_challan_return_no`,4))-4),signed integer)) reg from `store_challan_return` where substr(`store_challan_return_no`,-4,2) = ".date('m')." and substr(`store_challan_return_no`,-2,2) = ".date('y')."")->reg;
        $storeChallanReturnNo = 'SCR'.($str+1).date('my');
        $slip_no = Input::get('slip_no', rand(10, 1000));
        $store_challan_return_date = Input::get('store_challan_return_date');
        $departmentId = Input::get('departmentId');
        $subDepartmentId = Input::get('subDepartmentId');
        $locationId = Input::get('locationId');
        $projectId = Input::get('projectId');
        $store_challan_date = date("Y-m-d", strtotime(Input::get('store_challan_date')));
        $departmentId = Input::get('departmentId');
        $pageType = Input::get('pageType');
        $parentCode = Input::get('parentCode');
        $main_description = Input::get('main_description');

        $data1['slip_no'] = $slip_no;
        $data1['store_challan_return_no'] = $storeChallanReturnNo;
        $data1['store_challan_return_date'] = $store_challan_return_date;
        $data1['sub_department_id'] = $departmentId;
        $data1['description'] = $main_description;
        $data1['username'] 		 	= Auth::user()->name;
        $data1['approve_username'] = Auth::user()->name;
        $data1['date'] = date("Y-m-d");
        $data1['time'] = date("H:i:s");
        $data1['store_challan_return_status'] = 2;
        $data1['location_id']               = $locationId;
        $data1['project_id']                = $projectId;
        $data1['department_id']             = $departmentId;
        $data1['company_id']                = $m;
        $data1['sub_department_id']         = $subDepartmentId;

        DB::table('store_challan_return')->insert($data1);

        $seletedStoreChallanReturnRow = Input::get('seletedStoreChallanReturnRow');
        foreach ($seletedStoreChallanReturnRow as $row) {
            // dd(Input::get('issue_status_' . $row . ''), $row);
            if (Input::get('issue_status_'.$row.'') == 1) {
                # code...
                $storeChallanNo = Input::get('storeChallanNo_'.$row.'');
                $storeChallanDate = Input::get('storeChallanDate_'.$row.'');
                $categoryId = Input::get('categoryId_'.$row.'');
                $subItemId = Input::get('subItemId_'.$row.'');
                $return_qty = Input::get('return_qty_'.$row.'');
                $storeChallanIssueQty = Input::get('storeChallanIssueQty_'.$row.'');
                $remaining_store_challan_qty = Input::get('remaining_store_challan_qty_'.$row.'');

                $data2['store_challan_data_id'] = $row;
                $data2['store_challan_return_no'] = $storeChallanReturnNo;
                $data2['store_challan_return_date'] = $store_challan_return_date;
                $data2['store_challan_no'] = $storeChallanNo;
                $data2['store_challan_date'] = $storeChallanDate;
                $data2['category_id'] = $categoryId;
                $data2['sub_item_id'] = $subItemId;
                $data2['return_qty'] = $return_qty;
                $data2['username'] = Auth::user()->name;
                $data2['approve_username'] = Auth::user()->name;
                $data2['date'] = date("Y-m-d");
                $data2['time'] = date("H:i:s");
                $data2['store_challan_return_status'] = 2;
    
                DB::table('store_challan_return_data')->insert($data2);
                
                $data3['store_challan_data_id'] = $row;
                $data3['scr_no'] = $storeChallanReturnNo;
                $data3['scr_date'] = $store_challan_return_date;
                $data3['sc_no'] = $storeChallanNo;
                $data3['sc_date'] = $storeChallanDate;
                $data3['main_ic_id'] = $categoryId;
                $data3['sub_ic_id'] = $subItemId;
                $data3['qty'] = $return_qty;
                $data3['value'] = 0;
                $data3['username'] = Auth::user()->name;
                $data3['date'] = date("Y-m-d");
                $data3['time'] = date("H:i:s");
                $data3['action'] = 4;
                $data3['location_id'] = Input::get('warehouse_from_id_' . $row, 0);
                $data3['warehouse_from_id'] = Input::get('warehouse_to_id_' . $row, 0);
                $data3['warehouse_to_id'] = Input::get('warehouse_from_id_' . $row, 0);            
                $data3['purpose'] = Input::get('purpose_' . $row, 0);
                $data3['company_id'] = $m;
                DB::table('fara')->insert($data3);
            }
        }
        // dd($data1, $data2, $data3, Input::all());
        CommonFacades::reconnectMasterDatabase();       
        Session::flash('success','successfully saved.');
        return Redirect::to('store/viewStoreChallanReturnList?pageType='.Input::get('pageType').'&&parentCode='.Input::get('parentCode').'#SFR');
    }

}

