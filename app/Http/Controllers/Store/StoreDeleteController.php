<?php

namespace App\Http\Controllers\Store;
use App\Http\Controllers\Controller;
//namespace App\Http\Controllers\Auth
//use Auth;
//use App\User;
use App\Http\Requests;
use Illuminate\Http\Request;
use DB;
use Config;
use Redirect;
use Session;
use Auth;
use Input;
use CommonFacades;
use PurchaseFacades;
use FinanceFacades;
use Illuminate\Support\Facades\Log;
use StoreFacades;


class StoreDeleteController extends Controller
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

    public function reverseStoreChallanReturnVoucher(Request $request){
        $voucherNo = $request->get('voucherNo');

        DB::connection('tenant')->table('store_challan_return')->where('store_challan_return_no',$voucherNo)->delete();
        DB::connection('tenant')->table('store_challan_return_data')->where('store_challan_return_no',$voucherNo)->delete();
        DB::connection('tenant')->table('fara')->where('scr_no',$voucherNo)->delete();

    }

    public function deleteCompanyMaterialTwoTableRecords(){
        $m = CommonFacades::getSessionCompanyId();
        $voucherStatus = $_GET['voucherStatus'];
        $rowStatus = $_GET['rowStatus'];
        $columnValue = $_GET['columnValue'];
        $columnOne = $_GET['columnOne'];
        $columnTwo = $_GET['columnTwo'];
        $columnThree = $_GET['columnThree'];
        $tableOne = $_GET['tableOne'];
        $tableTwo = $_GET['tableTwo'];

        $tableOneDetail = DB::table($tableOne)->where($columnOne, $columnValue)->first();

        $initialEmailAddress = CommonFacades::voucherInitialEmailAddress($tableOneDetail->user_id);


        $updateDetails = array(
            $columnThree => 2,
            'delete_username' => Auth::user()->name
        );

        DB::table($tableOne)
            ->where($columnOne, $columnValue)
            ->update($updateDetails);

        DB::table($tableTwo)
            ->where($columnOne, $columnValue)
            ->update($updateDetails);
        if($tableOne == 'material_request'){
            $msg = 'Delete Material Request. Material Request No => '.$columnValue.'';
            CommonFacades::addEmailNotificationDetailOptionWise($m,'material_request_notification_setting',5,$tableOneDetail->location_id,'Material Request','Delete Material Request',$msg,$initialEmailAddress);
        }
        activity()
            ->causedBy(auth()->user())
            ->withProperties(['mr_no' => $columnValue])
            ->log($msg);

    }

    public function repostCompanyMaterialTwoTableRecords(){
        $m = CommonFacades::getSessionCompanyId();
        $voucherStatus = $_GET['voucherStatus'];
        $rowStatus = $_GET['rowStatus'];
        $columnValue = $_GET['columnValue'];
        $columnOne = $_GET['columnOne'];
        $columnTwo = $_GET['columnTwo'];
        $columnThree = $_GET['columnThree'];
        $tableOne = $_GET['tableOne'];
        $tableTwo = $_GET['tableTwo'];

        $tableOneDetail = DB::table($tableOne)->where($columnOne, $columnValue)->first();

        $initialEmailAddress = CommonFacades::voucherInitialEmailAddress($tableOneDetail->user_id);


        $updateDetails = array(
            $columnThree => 1,
            'delete_username' => ''
        );

        DB::table($tableOne)
            ->where($columnOne, $columnValue)
            ->update($updateDetails);

        DB::table($tableTwo)
            ->where($columnOne, $columnValue)
            ->update($updateDetails);

        if($tableOne == 'material_request'){
            $msg = 'Restore Material Request. Material Request No => '.$columnValue.'';
            CommonFacades::addEmailNotificationDetailOptionWise($m,'material_request_notification_setting',7,$tableOneDetail->location_id,'Material Request','Restore Material Request',$msg,$initialEmailAddress);
        }
        activity()
            ->causedBy(auth()->user())
            ->withProperties(['mr_no' => $columnValue])
            ->log($msg);
    }

    public function reversePurchaseOrderDetailAfterApproval(){
		date_default_timezone_set("Asia/Karachi");
		$m = Input::get('m');
		$poNo = Input::get('poNo');
		$pageType = Input::get('pageType');
		$parentCode = Input::get('parentCode');
        $prNo = Input::get('prNo');
        $initialEmailAddress = Input::get('initialEmailAddress');
        $purchaseOrderDate = Input::get('purchaseOrderDate');
        $poVoucherRemarks = Input::get('poVoucherRemarks');
        $locationId = Input::get('locationId');
		$urlPath = request()->segment(1).'/'.request()->segment(2);
		
        $updatePurchaseOrderDetails = array(
            'purchase_order_status' => '1',
            'approve_user_id' => '',
            'approve_username' => '',
            'approve_date' => '0000-00-00',
            'approve_time' => '00:00:00',
            'voucher_remarks' => $poVoucherRemarks
        );
        
        $updatePurchaseOrderDataDetails = array(
            'purchase_order_status' => '1',
            'approve_username' => '',
            'approve_date' => '0000-00-00',
            'approve_time' => '00:00:00',
            'grn_status' => '1'
        );

        DB::table('purchase_order')
            ->where('purchase_order_no', $poNo)
            ->update($updatePurchaseOrderDetails);
        DB::table('purchase_order_data')
            ->where('purchase_order_no', $poNo)
            ->update($updatePurchaseOrderDataDetails);

        $msg = 'Reverse After Approvel Purchase Order. Purchase Request No => '.$prNo.' and Purchase Order No => '.$poNo.' and Purchase Order Date => '.$purchaseOrderDate.' and Additional Remarks => '.$poVoucherRemarks.'';
        CommonFacades::addEmailNotificationDetailOptionWise($m,'purchase_order_notification_setting',6,$locationId,'Purcahse Order','Reverse After Approvel Purchase Order',$msg,$initialEmailAddress);
            
        $mData = 'Done';
		//}
		return $mData;
	}
	
	public function reversePurchaseOrderDetailBeforeApproval(){
		date_default_timezone_set("Asia/Karachi");
		$m = Input::get('m');
		$poNo = Input::get('poNo');
		$prNo = Input::get('prNo');
		$pageType = Input::get('pageType');
        $parentCode = Input::get('parentCode');
        $initialEmailAddress = Input::get('initialEmailAddress');
        $purchaseOrderDate = Input::get('purchaseOrderDate');
        $poVoucherRemarks = Input::get('poVoucherRemarks');
        $locationId = Input::get('locationId');
		$urlPath = request()->segment(1).'/'.request()->segment(2);
		/*$cgsmPrivileges = CommonHelper::checkGlobalSubMenuPrivileges($urlPath,Auth::user()->emr_no,$pageType,$parentCode);
		if($cgsmPrivileges == 0){
			$mData = '<div style="text-align:center"><h1>You have Insufficient Privileges to access this page !</h1></div><div style="text-align:center"><h1><a href="">Go Back</a></h1></div>';
		}else{*/
			$purcahseRequestDataIdArray = DB::table('purchase_order_data')->select('purchase_request_data_record_id')->where('purchase_order_no','=',$poNo)->get();
			foreach($purcahseRequestDataIdArray as $row){
				$updatePurchaseRequestDataDetails = array(
					'purchase_order_status' => '1'
				);

				DB::table('purchase_request_data')
					->where('id', $row->purchase_request_data_record_id)
					->update($updatePurchaseRequestDataDetails);
			}
			DB::table('purchase_order')
				->where('purchase_order_no', $poNo)
				->delete();
			DB::table('purchase_order_data')
				->where('purchase_order_no', $poNo)
				->delete();
            $msg = 'Reverse Before Approvel Purchase Order. Purchase Request No => '.$prNo.' and Purchase Order No => '.$poNo.' and Purchase Order Date => '.$purchaseOrderDate.' and Additional Remarks => '.$poVoucherRemarks.'';
            CommonFacades::addEmailNotificationDetailOptionWise($m,'purchase_order_notification_setting',6,$locationId,'Purcahse Order','Reverse Before Approvel Purchase Order',$msg,$initialEmailAddress);	
            Log::info($msg);
            activity()
            ->causedBy(auth()->user())
            ->withProperties(['po_no' => $poNo])
            ->log($msg);
			$mData = 'Done';
		//}
		return $mData;
	}

    public function deleteCompanyStoreThreeTableRecords(){
        $m = CommonFacades::getSessionCompanyId();
        CommonFacades::companyDatabaseConnection($m);
        $voucherStatus = $_GET['voucherStatus'];
        $rowStatus = $_GET['rowStatus'];
        $columnValue = $_GET['columnValue'];
        $columnOne = $_GET['columnOne'];
        $columnTwo = $_GET['columnTwo'];
        $columnThree = $_GET['columnThree'];
        $tableOne = $_GET['tableOne'];
        $tableTwo = $_GET['tableTwo'];
        $tableThree = $_GET['tableThree'];


        $updateDetails = array(
            $columnTwo => 2,
            'delete_username' => Auth::user()->name
        );

        DB::table($tableOne)
            ->where($columnOne, $columnValue)
            ->update($updateDetails);

        DB::table($tableTwo)
            ->where($columnOne, $columnValue)
            ->update($updateDetails);

        DB::table($tableThree)
            ->where($columnThree, $columnValue)
            ->delete();


        CommonFacades::reconnectMasterDatabase();
        //Session::flash('dataApprove','successfully approve.');
    }

    public function repostCompanyStoreThreeTableRecords(){
        $m = CommonFacades::getSessionCompanyId();
        CommonFacades::companyDatabaseConnection($m);
        $voucherStatus = $_GET['voucherStatus'];
        $rowStatus = $_GET['rowStatus'];
        $columnValue = $_GET['columnValue'];
        $columnOne = $_GET['columnOne'];
        $columnTwo = $_GET['columnTwo'];
        $columnThree = $_GET['columnThree'];
        $columnFour = $_GET['columnFour'];
        $tableOne = $_GET['tableOne'];
        $tableTwo = $_GET['tableTwo'];
        $tableThree = $_GET['tableThree'];


        $updateDetails = array(
            $columnTwo => 1,
            'delete_username' => ''
        );

        DB::table($tableOne)
            ->where($columnOne, $columnValue)
            ->update($updateDetails);

        DB::table($tableTwo)
            ->where($columnOne, $columnValue)
            ->update($updateDetails);

        $secondTableRecord = DB::table($tableTwo)->where($columnOne, $columnValue)->where('status','=', '1')->get();
        //return print($secondTableRecord);
        foreach ($secondTableRecord as $row){
            if($columnThree == 'demand_no'){
                $action = '3';
                $qty = $row->qty;
                $mcOne = '';
                $mcTwo = '';
                $mcThree = '';
                $mcFour = '';
            }else if($columnThree == 'sc_no'){
                $action = '2';
                $qty = $row->issue_qty;
                $mcOne = '';
                $mcTwo = '';
                $mcThree = '';
                $mcFour = '';
            }else if($columnThree == 'scr_no'){
                $action = '4';
                $qty = $row->return_qty;
                $data[$columnThree] = $row->store_challan_return_no;
                $data[$columnFour] = $row->store_challan_return_date;
                $data['sc_no'] = $row->store_challan_no;
                $data['sc_date'] = $row->store_challan_date;

            }
            $data['main_ic_id'] = $row->category_id;
            $data['sub_ic_id'] = $row->sub_item_id;
            $data['main_ic_id'] = $row->category_id;
            $data['sub_ic_id'] = $row->sub_item_id;
            $data['qty'] = $qty;
            $data['action'] = $action;
            $data['status'] = 1;
            $data['username'] = Auth::user()->name;
            $data['date'] = date("Y-m-d");
            $data['time'] = date("H:i:s");
            $data['company_id'] = $m;
            DB::table($tableThree)->insert($data);
        }

        CommonFacades::reconnectMasterDatabase();
    }

    public function deleteCompanyStoreTwoTableRecords(){

        $m = CommonFacades::getSessionCompanyId();
        CommonFacades::companyDatabaseConnection($m);
        $voucherStatus = $_GET['voucherStatus'];
        $rowStatus = $_GET['rowStatus'];
        $columnValue = $_GET['columnValue'];
        $columnOne = $_GET['columnOne'];
        $columnTwo = $_GET['columnTwo'];
        $columnThree = $_GET['columnThree'];
        $tableOne = $_GET['tableOne'];
        $tableTwo = $_GET['tableTwo'];


        $updateDetails = array(
            $columnThree => 2,
            'delete_username' => Auth::user()->name
        );

        DB::table($tableOne)
            ->where($columnOne, $columnValue)
            ->update($updateDetails);

        DB::table($tableTwo)
            ->where($columnOne, $columnValue)
            ->update($updateDetails);


        CommonFacades::reconnectMasterDatabase();
        Session::flash('dataDelete','successfully delete.');

    }

    public function repostCompanyStoreTwoTableRecords(){
        $m = CommonFacades::getSessionCompanyId();
        CommonFacades::companyDatabaseConnection($m);
        $voucherStatus = $_GET['voucherStatus'];
        $rowStatus = $_GET['rowStatus'];
        $columnValue = $_GET['columnValue'];
        $columnOne = $_GET['columnOne'];
        $columnTwo = $_GET['columnTwo'];
        $columnThree = $_GET['columnThree'];
        $tableOne = $_GET['tableOne'];
        $tableTwo = $_GET['tableTwo'];


        $updateDetails = array(
            $columnThree => 1,
            'delete_username' => ''
        );

        DB::table($tableOne)
            ->where($columnOne, $columnValue)
            ->update($updateDetails);

        DB::table($tableTwo)
            ->where($columnOne, $columnValue)
            ->update($updateDetails);

        Session::flash('dataRepost','successfully repost.');
        CommonFacades::reconnectMasterDatabase();
    }

    public function approvePurchaseOrder(){
		date_default_timezone_set("Asia/Karachi");
        $m = Input::get('m');
		$pageType = Input::get('pageType');
		$parentCode = Input::get('parentCode');
		$urlPath = request()->segment(1).'/'.request()->segment(2);
		/*$cgsmPrivileges = CommonHelper::checkGlobalSubMenuPrivileges($urlPath,Auth::user()->emr_no,$pageType,$parentCode);
		if($cgsmPrivileges == 0){
			$mData = '<div style="text-align:center"><h1>You have Insufficient Privileges to access this page !</h1></div><div style="text-align:center"><h1><a href="">Go Back</a></h1></div>';
		}else{*/
            
            $voucherStatus = Input::get('voucherStatus');
			$rowStatus = Input::get('rowStatus');
			$columnValue = Input::get('columnValue');
			$columnOne = Input::get('columnOne');
			$columnTwo = Input::get('columnTwo');
			$columnThree = Input::get('columnThree');
			$tableOne = Input::get('tableOne');
            $tableTwo = Input::get('tableTwo');
            $initialEmailAddress = Input::get('initialEmailAddress');
            $purchaseOrderDate = Input::get('purchaseOrderDate');
            $poVoucherRemarks = Input::get('poVoucherRemarks');
            $locationId = Input::get('locationId');
            $getPurchaseOrderVoucherDetail = DB::table('purchase_order')->where('purchase_order_no','=',$columnValue)->first();
            $prNo = $getPurchaseOrderVoucherDetail->purchase_request_no;
            $updateDetailsOne = array(
                $columnTwo => 2,
                'approve_user_id' => Auth::user()->id,
				'approve_username' => Auth::user()->name,
				'approve_date' => date("Y-m-d"),
                'approve_time' => date("H:i:s"),
                'voucher_remarks' => $poVoucherRemarks
			);
            
            $updateDetails = array(
                $columnTwo => 2,
                'approve_username' => Auth::user()->name,
				'approve_date' => date("Y-m-d"),
				'approve_time' => date("H:i:s")
			);
			
			$updatePVDetail = array(
				'pv_status' => 2,
				'approve_username' => Auth::user()->name,
				'approve_date' => date("Y-m-d"),
				'approve_time' => date("H:i:s")
			);
			
			$updatePVDataDetail = array(
				'pv_status' => 2,
				'approve_username' => Auth::user()->name
			);
			
			
			
			DB::table($tableOne)
				->where($columnOne, $columnValue)
				->update($updateDetailsOne);

			DB::table($tableTwo)
				->where($columnOne, $columnValue)
				->update($updateDetails);
				
			if($getPurchaseOrderVoucherDetail->voucher_type == 2){
				$pvDetail = DB::table('pvs')->where('po_no','=',$columnValue)->first();
				DB::table('pvs')
					->where('pv_no', $pvDetail->pv_no)
					->update($updatePVDetail);

				DB::table('pv_data')
					->where('pv_no', $pvDetail->pv_no)
					->update($updatePVDataDetail);
					
				$pvDataDetail = DB::table('pv_data')->where('pv_no','=',$pvDetail->pv_no)->where('pv_status','=','2')->get();	
					
				foreach ($pvDataDetail as $row) {
					$vouceherType = 2;
					$voucherNo = $row->pv_no;
					$voucherDate = $row->pv_date;
					$data['acc_id'] = $row->acc_id;
					$data['acc_code'] = FinanceFacades::getAccountCodeByAccId($row->acc_id,$m);
					$data['region_id'] = $pvDetail->region_id;
					$data['particulars'] = $row->description;
					$data['opening_bal'] = '0';
					$data['debit_credit'] = $row->debit_credit;
					$data['amount'] = $row->amount;
					$data['voucher_no'] = $voucherNo;
					$data['voucher_type'] = $vouceherType;
					$data['v_date'] = $voucherDate;
					$data['date'] = date("Y-m-d");
					$data['time'] = date("H:i:s");
					$data['accounting_year']     	 = Session::get('accountYear');
					$data['username'] = Auth::user()->name;

					DB::table('transactions')->insert($data);
				}
			}
			
            $msg = 'Approve Purchase Order. Purchase Request No => '.$prNo.' and Purchase Order No => '.$columnValue.' and Purchase Order Date => '.$purchaseOrderDate.' and Additional Remarks => '.$poVoucherRemarks.'';
            CommonFacades::addEmailNotificationDetailOptionWise($m,'purchase_order_notification_setting',4,$locationId,'Purcahse Order','Approve Purcahse Order',$msg,$initialEmailAddress);
            
            $mData = 'Done';
		//}
		return $mData;
    }

    public function approvePurchaseRequestSale(){
        $m = CommonFacades::getSessionCompanyId();
        CommonFacades::companyDatabaseConnection($m);
        $voucherStatus = $_GET['voucherStatus'];
        $rowStatus = $_GET['rowStatus'];
        $columnValue = $_GET['columnValue'];
        $columnOne = $_GET['columnOne'];
        $columnTwo = $_GET['columnTwo'];
        $columnThree = $_GET['columnThree'];
        $tableOne = $_GET['tableOne'];
        $tableTwo = $_GET['tableTwo'];
        $updateDetails = array(
            $columnTwo => 2,
            'approve_username' => Auth::user()->name
        );
        DB::table($tableOne)
            ->where($columnOne, $columnValue)
            ->update($updateDetails);

        DB::table($tableTwo)
            ->where($columnOne, $columnValue)
            ->update($updateDetails);

        $firstTableRecord = DB::table($tableOne)->where($columnOne, $columnValue)->where('status','=', '1')->first();
        $secondTableRecord = DB::table($tableTwo)->where($columnOne, $columnValue)->where('status','=', '1')->get();
        $supplierId = $firstTableRecord->supplier_id;
        CommonFacades::reconnectMasterDatabase();
        $supplierAccId = CommonFacades::getAccountIdByMasterTable($m,$firstTableRecord->supplier_id,'supplier');
        CommonFacades::companyDatabaseConnection($m);
        $description = $firstTableRecord->description;
        $slipNo = $firstTableRecord->slip_no;
        $prNo = $columnValue;
        $prDate = $firstTableRecord->purchase_request_date;
        $str = DB::selectOne("select max(convert(substr(`jv_no`,3,length(substr(`jv_no`,3))-4),signed integer)) reg from `jvs` where substr(`jv_no`,-4,2) = ".date('m')." and substr(`jv_no`,-2,2) = ".date('y')."")->reg;
        $jv_no = 'jv'.($str+1).date('my');

        $data1['jv_date']   	    = date('Y-m-d');
        $data1['jv_no']   		    = $jv_no;
        $data1['pr_date']   	    = $prDate;
        $data1['pr_no']   		    = $prNo;
        $data1['slip_no']   	    = $slipNo;
        $data1['voucherType'] 	    = 2;
        $data1['description']       = '('.$description.') * (Purchase Request No => '.$prNo.') * (Purchase Request Date => '.$prDate.') * (Slip No => '.$slipNo.')';
        $data1['jv_status']  	    = 2;
        $data1['username'] 		    = Auth::user()->name;
        $data1['approve_username'] 	= Auth::user()->name;
        $data1['date'] 			    = date('Y-m-d');
        $data1['time'] 			    = date('H:i:s');
        DB::table('jvs')->insert($data1);
        $overAllSubTotal = 0;
        $jvDataDescription = '';
        foreach ($secondTableRecord as $row){
            $demandNo = $row->demand_no;
            $demandDate = $row->demand_date;
            $categoryId = $row->category_id;
            $subItemId = $row->sub_item_id;
            $qty = $row->purchase_request_qty;
            $rate = $row->rate;
            $subTotal = $row->sub_total;
            $overAllSubTotal += $subTotal;
            $jvDataDescription = '(Demand No => '.$demandNo.')*(Demand Date => '.$demandDate.')';
            CommonFacades::reconnectMasterDatabase();
            $subItemAccId = CommonFacades::getAccountIdByMasterTable($m,$row->sub_item_id,'subitem');
            CommonFacades::companyDatabaseConnection($m);
            $data2['debit_credit'] = 1;
            $data2['amount'] = $subTotal;
            $data2['jv_no']   		= $jv_no;
            $data2['jv_date']   	= date('Y-m-d');
            $data2['acc_id'] 		= $subItemAccId;
            $data2['description']   = $jvDataDescription;
            $data2['jv_status']   	= 2;
            $data2['username'] 		= Auth::user()->name;
            $data2['status']  		= 1;
            $data2['date'] 			= date('Y-m-d');
            $data2['time'] 			= date('H:i:s');

            DB::table('jv_data')->insert($data2);

        }

        $data3['debit_credit'] = 0;
        $data3['amount'] = $overAllSubTotal;
        $data3['jv_no']   		= $jv_no;
        $data3['jv_date']   	= date('Y-m-d');
        $data3['acc_id'] 		= $supplierAccId;
        $data3['description']   = $jvDataDescription;
        $data3['jv_status']   	= 2;
        $data3['username'] 		= Auth::user()->name;
        $data3['approve_username'] 		= Auth::user()->name;
        $data3['status']  		= 1;
        $data3['date'] 			= date('Y-m-d');
        $data3['time'] 			= date('H:i:s');

        DB::table('jv_data')->insert($data3);

        $updateDemandDetails = array(
            'purchase_request_status' => 2
        );

        DB::table('demand_data')
            ->where('demand_no', $demandNo)
            ->where('demand_status', '2')
            ->where('status', '1')
            ->update($updateDemandDetails);

        $jvDataRecord = DB::table('jv_data')->where('jv_no', $jv_no)->where('status','=', '1')->get();
        foreach ($jvDataRecord as $row2) {
            $vouceherType = 3;
            $voucherNo = $row2->jv_no;
            $voucherDate = $row2->jv_date;
            CommonFacades::reconnectMasterDatabase();
            $accCode = FinanceFacades::getAccountCodeByAccId($row2->acc_id,$m);
            CommonFacades::companyDatabaseConnection($m);

            $data4['acc_id'] = $row2->acc_id;
            $data4['acc_code'] = $accCode;
            $data4['particulars'] = $row2->description;
            $data4['opening_bal'] = '0';
            $data4['debit_credit'] = $row2->debit_credit;
            $data4['amount'] = $row2->amount;
            $data4['voucher_no'] = $voucherNo;
            $data4['voucher_type'] = $vouceherType;
            $data4['v_date'] = $voucherDate;
            $data4['date'] = date("Y-m-d");
            $data4['time'] = date("H:i:s");
            $data4['username'] = Auth::user()->name;

            DB::table('transactions')->insert($data4);
        }


        CommonFacades::reconnectMasterDatabase();
    }

}
