<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;

use App\Http\Requests;
use Illuminate\Http\Request;
use Input;
use Auth;
use DB;
use Config;
use Redirect;
use Session;
use StoreFacades;
use CommonFacades;
use Cache;

class StoreEditDetailControler extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'MultiDB']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function updateStoreChallanDetailandApprove(Request $request)
    {
        // dd($request->all(), Input::get('warehouse_to_id'));
        //receiver_user_id
        if ($request->store_challan_status == 1) {
            date_default_timezone_set("Asia/Karachi");
            $m = CommonFacades::getSessionCompanyId();
            $pageType = Input::get('pageType');
            $parentCode = Input::get('parentCode');
            $StoreChallanNo = Input::get('StoreChallanNo');
            $StoreChallanDate = Input::get('StoreChallanDate');
            $storeChallanDataRow = Input::get('storeChallanDataRow');
            $locationId = Input::get('locationId');
            $warehouseFrom = Input::get('warehouse_from_id');
            $warehouseTo = Input::get('warehouse_to_id');
            $receiver_user_id = Input::get('receiver_user_id');
            //$receiver_username = Input::get('receiver_username');
            //receiver_user_id
            foreach ($storeChallanDataRow as $row) {
                $receiveQty = Input::get('receive_qty_' . $row . '');
                $categoryId = Input::get('category_id_' . $row . '');
                $subItemId = Input::get('sub_item_id_' . $row . '');
                // dd($receiveQty, $subItemId);
                $updateTwoDetails = array(
                    'store_challan_status' => 2,
                    'approve_username' => Auth::user()->name,
                    'approve_date' => date("Y-m-d"),
                    'approve_time' => date("H:i:s"),
                    'receive_qty' => $receiveQty
                );

                DB::table('store_challan_data')->where('store_challan_no', $StoreChallanNo)->where('id', $row)->update($updateTwoDetails);

                $fData['sc_no'] = $StoreChallanNo;
                $fData['sc_date'] = $StoreChallanDate;
                $fData['location_id'] = $warehouseFrom;
                $fData['warehouse_from_id'] = $warehouseFrom ?? 0;
                $fData['warehouse_to_id'] = $warehouseTo ?? 0;
                $fData['main_ic_id'] = $categoryId;
                $fData['sub_ic_id'] = $subItemId;
                $fData['qty'] = $receiveQty;
                $fData['action'] = 2;
                $fData['purpose'] = Input::get('purpose', 0);
                $fData['username'] = Auth::user()->name;
                $fData['approve_username'] = Auth::user()->name;
                $fData['date'] = date("Y-m-d");
                $fData['time'] = date("H:i:s");
                $fData['company_id'] = $m;
                $fData['accounting_year'] = Session::get('accountYear');

                DB::table('fara')->insert($fData);
                $fData['action'] = 11;
                $fData['location_id'] = $warehouseTo;
                DB::table('fara')->insert($fData);
                $updateDetails = array(
                    'store_challan_status' => 2,
                    'approve_username' => Auth::user()->name,
                    'approve_date' => date("Y-m-d"),
                    'approve_time' => date("H:i:s"),
                    'approve_user_id' => Auth::user()->id,
                    'receiver_user_id' => $receiver_user_id,
                    // 'receiver_username' => $receiver_username,
                );

                DB::table('store_challan')->where('store_challan_no', $StoreChallanNo)->update($updateDetails);
                // Cache::forget('cacheZViewInventory_' . $m . '');
                // Cache::rememberForever('cacheZViewInventory_' . $m . '', function () use ($m) {
                //     return DB::table('z_view_inventory')->where('company_id', '=', $m)->get();
                // });
            }
        } else {
            //  $temp = DB::table('store_challan')->where('store_challan_no', Input::get('StoreChallanNo'))->first();
            // if($temp->receiver_user_id == Auth::user()->id){                
            $StoreChallanNo = Input::get('StoreChallanNo');
            // $fData['receiver_user_id'] = Auth::user()->id;
            $fData['receiver_username'] = Auth::user()->name;
            $fData['receiver_date'] = date("Y-m-d");
            $fData['receiver_time'] = date("H:i:s");
            $fData['store_challan_status'] = 4;
            DB::table('store_challan')->where('store_challan_no', $StoreChallanNo)->update($fData);
            // }else{
            //     return redirect()->back()->with('error', 'Login user is not the listed receiver.');
            // }
        }
    }

    public function updateMaterialRequestDetailandApprove()
    {
        date_default_timezone_set("Asia/Karachi");
        $m = CommonFacades::getSessionCompanyId();
        $materialRequest_no = Input::get('MaterialRequestNo');
        $materialRequest_date = Input::get('MaterialRequestDate');
        $initialEmailAddress = Input::get('initialEmailAddress');
        $location_id = Input::get('location_id');
        $mrVoucherStatus = Input::get('mrVoucherStatus');
        $mrVoucherRemarks = Input::get('mrVoucherRemarks');

        if ($mrVoucherStatus == 3) {
            $subject = 'Reject Material Request';
            $updateDetails = array(
                'material_request_status' => 3,
                'additional_remarks' => $mrVoucherRemarks
            );

            $updateTwoDetails = array(
                'material_request_status' => 3
            );
        } else {
            $subject = 'Approve Material Request';
            $updateDetails = array(
                'material_request_status' => 2,
                'approve_username' => Auth::user()->name,
                'approve_date' => date("Y-m-d"),
                'approve_time' => date("H:i:s"),
                'approve_user_id' => Auth::user()->id,
                'additional_remarks' => $mrVoucherRemarks
            );

            $updateTwoDetails = array(
                'material_request_status' => 2,
                'approve_username' => Auth::user()->name
            );
        }

        DB::table('material_request')
            ->where('material_request_no', $materialRequest_no)
            ->update($updateDetails);

        DB::table('material_request_data')->where('material_request_no', $materialRequest_no)->update($updateTwoDetails);

        $msg = $subject . '. Material Request No => ' . $materialRequest_no . ' and Material Request Date => ' . $materialRequest_date . ' created by ' . Auth::user()->name;
        CommonFacades::addEmailNotificationDetailOptionWise($m, 'material_request_notification_setting', $mrVoucherStatus, $location_id, 'Material Request', $subject, $msg, $initialEmailAddress);

        echo 'Done';
    }

    public function editStoreChallanVoucherDetail()
    {
        $m = CommonFacades::getSessionCompanyId();
        CommonFacades::companyDatabaseConnection(CommonFacades::getSessionCompanyId());
        $storeChallanNo = Input::get('store_challan_no');
        $slip_no = Input::get('slip_no');
        $store_challan_date = Input::get('store_challan_date');
        $sub_department_id = Input::get('departmentId');
        $pageType = Input::get('pageType');
        $parentCode = Input::get('parentCode');
        $main_description = Input::get('description');
        DB::table('fara')->where('sc_no', $storeChallanNo)->delete();


        $data1['slip_no'] = $slip_no;
        $data1['store_challan_date'] = $store_challan_date;
        $data1['description'] = $main_description;
        $data1['date'] = date("Y-m-d");
        $data1['time'] = date("H:i:s");

        DB::table('store_challan')->where('store_challan_no', '=', $storeChallanNo)->update($data1);
        $seletedStoreChallanRow = Input::get('storeChallanDataSection');
        foreach ($seletedStoreChallanRow as $row) {
            $recordId = Input::get('recordId_' . $row . '');
            $demandNo = Input::get('demandNo_' . $row . '');
            $demandDate = Input::get('demandDate_' . $row . '');
            $categoryId = Input::get('categoryId_' . $row . '');
            $subItemId = Input::get('subItemId_' . $row . '');
            $issue_qty = Input::get('issue_qty_' . $row . '');
            $demandAndRemainingQty = Input::get('demandAndRemainingQty_' . $row . '');

            $data2['store_challan_date'] = $store_challan_date;
            $data2['demand_date'] = $demandDate;
            $data2['issue_qty'] = $issue_qty;
            $data2['date'] = date("Y-m-d");
            $data2['time'] = date("H:i:s");

            DB::table('store_challan_data')->where('store_challan_no', '=', $storeChallanNo)->where('id', '=', $recordId)->update($data2);
            if ($issue_qty == $demandAndRemainingQty) {
                DB::table('demand_data')
                    ->where('category_id', $categoryId)
                    ->where('sub_item_id', $subItemId)
                    ->where('demand_no', $demandNo)
                    ->update(['store_challan_status' => "2"]);
            }

            $data3['sc_no'] = $storeChallanNo;
            $data3['sc_date'] = $store_challan_date;
            $data3['demand_no'] = $demandNo;
            $data3['demand_date'] = $demandDate;
            $data3['main_ic_id'] = $categoryId;
            $data3['sub_ic_id'] = $subItemId;
            $data3['qty'] = $issue_qty;
            $data3['value'] = 0;
            $data3['username'] = Auth::user()->name;
            $data3['date'] = date("Y-m-d");
            $data3['time'] = date("H:i:s");
            $data3['action'] = 2;
            $data3['status'] = 1;
            $data3['company_id'] = $m;
            DB::table('fara')->insert($data3);
        }
        CommonFacades::reconnectMasterDatabase();
        Session::flash('dataEdit', 'successfully Update.');
        return Redirect::to('store/viewStoreChallanList?pageType=' . Input::get('pageType') . '&&parentCode=' . Input::get('parentCode') . '#SFR');
    }

    public function editPurchaseOrderVoucherDetail()
    {
        $m = CommonFacades::getSessionCompanyId();
        $pageType = Input::get('pageType');
        $parentCode = Input::get('parentCode');
        CommonFacades::companyDatabaseConnection(CommonFacades::getSessionCompanyId());
        $purchase_order_no = Input::get('purchase_order_no');
        DB::table('purchase_order')->where('purchase_order_no', $purchase_order_no)->delete();
        DB::table('purchase_order_data')->where('purchase_order_no', $purchase_order_no)->delete();

        $seletedSupplierRow = Input::get('purchaseOrderDataSection');
        $supplierIds = array();
        foreach ($seletedSupplierRow as $row) {
            $supplierIds[] = Input::get('supplier_id_' . $row . '');
        }
        $newSupplierIds = array();
        foreach ($supplierIds as $supplierIdItems) {
            foreach ($newSupplierIds as $newSupplierIdItems) {
                if ($supplierIdItems == $newSupplierIdItems) {
                    continue 2;
                }
            }
            $newSupplierIds[] = $supplierIdItems;
        }
        $newSupplierIds;

        foreach ($newSupplierIds as $row1) {
            if ($newSupplierIds[0] == $row1) {
                $NpurchaseOrderNo = $purchase_order_no;
            } else {
                $str = DB::selectOne("select max(convert(substr(`purchase_order_no`,3,length(substr(`purchase_order_no`,3))-4),signed integer)) reg from `purchase_order` where substr(`purchase_order_no`,-4,2) = " . date('m') . " and substr(`purchase_order_no`,-2,2) = " . date('y') . "")->reg;
                $NpurchaseOrderNo = 'po' . ($str + 1) . date('my');
            }
            $NpurchaseOrderNo;

            $pageType = Input::get('pageType');
            $parentCode = Input::get('parentCode');
            $purchase_request_no = Input::get('prNo');
            $purchase_request_date = Input::get('prDate');
            $po_date = Input::get('po_date');
            $delivery_place = Input::get('delivery_place');
            $supplier_id = $row1;


            $subDepartmentId = Input::get('subDepartmentId');
            $main_description = Input::get('main_description');


            $data1['purchase_order_no'] = $NpurchaseOrderNo;
            $data1['purchase_order_date'] = $po_date;
            $data1['purchase_request_no'] = $purchase_request_no;
            $data1['purchase_request_date'] = $purchase_request_date;
            $data1['delivery_place'] = $delivery_place;
            $data1['sub_department_id'] = $subDepartmentId;
            $data1['supplier_id'] = $supplier_id;
            $data1['description'] = $main_description;
            $data1['username'] = Auth::user()->name;
            $data1['user_id'] = Auth::user()->id;
            $data1['date'] = date("Y-m-d");
            $data1['time'] = date("H:i:s");
            $data1['purchase_order_status'] = 1;
            $data1['accounting_year']          = Session::get('accountYear');

            DB::table('purchase_order')->insert($data1);

            $seletedPurchaseRequestRow = Input::get('purchaseOrderDataSection');
            //return $seletedPurchaseRequestRow;
            foreach ($seletedPurchaseRequestRow as $row2) {
                $saleTaxHead = Input::get('sale_tax_head_' . $row2 . '');
                $checkSupplierId = Input::get('supplier_id_' . $row2 . '');
                $qoutation_no = Input::get('qoutation_no_' . $row2 . '');
                $qoutation_date = Input::get('qoutation_date_' . $row2 . '');
                $delivery_days = Input::get('delivery_days_' . $row2 . '');
                $payment_terms = Input::get('payment_terms_' . $row2 . '');
                $categoryId = Input::get('categoryId_' . $row2 . '');
                $subItemId = Input::get('subItemId_' . $row2 . '');
                $remaining_purchase_order_qty = Input::get('remaining_purchase_order_qty_' . $row2 . '');
                $unit = Input::get('unit_' . $row2 . '');
                $purchase_order_qty = Input::get('purchase_order_qty_' . $row2 . '');
                $unit_price = Input::get('unit_price_' . $row2 . '');
                $sub_total = Input::get('sub_total_' . $row2 . '');
                $sub_total_with_persent = Input::get('sub_total_with_persent_' . $row2 . '');

                $data2['purchase_order_no'] = $NpurchaseOrderNo;
                $data2['purchase_order_date'] = $po_date;
                $data2['purchase_request_no'] = $purchase_request_no;
                $data2['purchase_request_date'] = $purchase_request_date;
                $data2['category_id'] = $categoryId;
                $data2['sub_item_id'] = $subItemId;
                $data2['sale_tax_head'] = $saleTaxHead;
                $data2['qoutation_no'] = $qoutation_no;
                $data2['qoutation_date'] = $qoutation_date;
                $data2['supplier_id'] = $checkSupplierId;
                $data2['delivery_days'] = $delivery_days;
                $data2['payment_terms'] = $payment_terms;
                $data2['unit'] = $unit;
                $data2['purchase_order_qty'] = $purchase_order_qty;
                $data2['unit_price'] = $unit_price;
                $data2['sub_total'] = $sub_total;
                $data2['sub_total_with_persent'] = $sub_total_with_persent;
                $data2['username'] = Auth::user()->name;
                $data2['user_id'] = Auth::user()->id;
                $data2['date'] = date("Y-m-d");
                $data2['time'] = date("H:i:s");
                $data2['purchase_order_status'] = 1;
                $data2['accounting_year']          = Session::get('accountYear');
                if ($supplier_id == $checkSupplierId) {
                    DB::table('purchase_order_data')->insert($data2);
                    if ($remaining_purchase_order_qty <= $purchase_order_qty) {
                        DB::table('purchase_request_data')
                            ->where('category_id', $categoryId)
                            ->where('sub_item_id', $subItemId)
                            ->where('purchase_request_no', $purchase_request_no)
                            ->update(['purchase_order_status' => "2"]);
                    }
                }
            }
        }

        CommonFacades::reconnectMasterDatabase();
        Session::flash('dataEdit', 'successfully Update.');
        return Redirect::to('store/viewPurchaseOrderList?pageType=' . Input::get('pageType') . '&&parentCode=' . Input::get('parentCode') . '#SFR');
    }
    public function updateTaxPurchaseOrderVoucherDetail(Request $request)
    {
        $m = CommonFacades::getSessionCompanyId();
        $parentCode = $request->parentCode ?? '';
        $pageType = $request->pageType ?? '';
        $accountId = '';
        $purchase_order_no = Input::get('purchase_order_no');


        $purchase_order_data = DB::table('purchase_order_data')
            ->where('purchase_order_no', $purchase_order_no)
            ->where('company_id', $m)
            ->get();
        $data1['custom_tax_percent'] = $request->taxPercentage ?? '';
        $data1['account_id'] = $request->account_id ?? '';
        DB::table('purchase_order')
            ->where('purchase_order_no', $purchase_order_no)
            ->where('company_id', $m)
            ->update($data1);
        DB::table('purchase_order_data')
            ->where('purchase_order_no', $purchase_order_no)
            ->where('company_id', $m)
            ->update($data1);
        // dd(count($purchase_order_data), $purchase_order_no);

        Session::flash('dataEdit', 'successfully Update.');
        return Redirect::to('store/viewPurchaseOrderList?pageType=' . $pageType . '&&parentCode=' . $parentCode . '#SFR');
    }
}
