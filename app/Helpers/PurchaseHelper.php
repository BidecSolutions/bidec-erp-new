<?php
namespace App\Helpers;
use DB;
use Config;
use CommonFacades;
use App\Models\Category;
use App\Models\Subitem;
use Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Session;
class PurchaseHelper
{
    public static function homePageURL()
    {
        return url('/');
    }

    public static function displayReversePurchaseRequestVoucherAfterApproval($m,$purchaseRequestNo){
        $countPurchaseOrderAgainstPR = DB::table('purchase_order')->where('purchase_request_no','=',$purchaseRequestNo)->count();
        $data = '';
        if($countPurchaseOrderAgainstPR == 0){
            if(singlePermission(getSessionCompanyId(), Auth::user()->id, Input::get('parentCode'), 'right_reverse', Auth::user()->acc_type)){ 
                $data = '<input type="text" name="rprVoucherremarks" id="rprVoucherremarks" class="form-control" /><div style="line-height:5px;">&nbsp;</div><button class="btn btn-xs btn-danger" onclick="reversePurchaseRequestAfterApproval()">Reverse</button>';
            }else{
                $data = '<input type="text" class="form-control" /><div style="line-height:5px;">&nbsp;</div><button disabled class="btn btn-xs btn-danger">Reverse</button>';
            }
        }else{
            $data = '<span class="btn btn-xs success">Atleast One Purchase Order Created</span>';
        }
        return $data;
    }

    public static function getPurchaseSummaryCountingTypeWiseForDashboard($m,$locationId,$fromDate,$toDate,$type,$projectId,$subDepartmentId){
        $data = '0';
        $m = CommonFacades::getSessionCompanyId();
        $accountingYear = Session::get('accountYear');
		if($type == 0){
            $filterLocationCondition = ' and location_id = '.$locationId.'';
            if(empty($projectId)){
                $filterProjectCondition = '';    
            }else{
                $filterProjectCondition = ' and project_id = '.$projectId.'';
            }
            if(empty($subDepartmentId)){
                $filterSubDepartmentCondition = '';
            }else{
                $filterSubDepartmentCondition = ' and sub_department_id = '.$subDepartmentId.'';
            }
                
			$getData = DB::select("SELECT 
                id,
                purchase_request_no,
                purchase_request_date from purchase_request WHERE company_id = ".$m."".$filterLocationCondition."".$filterProjectCondition."".$filterSubDepartmentCondition." and accounting_year = ".$accountingYear."  and status = 1 and purchase_request_date BETWEEN '".$fromDate."' and '".$toDate."'");
			$data = count($getData);
		}else if($type == 1){
            $filterLocationCondition = ' and location_id = '.$locationId.'';
            if(empty($projectId)){
                $filterProjectCondition = '';    
            }else{
                $filterProjectCondition = ' and project_id = '.$projectId.'';
            }
            if(empty($subDepartmentId)){
                $filterSubDepartmentCondition = '';
            }else{
                $filterSubDepartmentCondition = ' and sub_department_id = '.$subDepartmentId.'';
            }
			$getData = DB::select("SELECT 
                id,
                purchase_request_no,
                purchase_request_date from purchase_request WHERE company_id = ".$m."".$filterLocationCondition."".$filterProjectCondition."".$filterSubDepartmentCondition." and accounting_year = ".$accountingYear." and status = 1 and purchase_request_status = 1 and purchase_request_date BETWEEN '".$fromDate."' and '".$toDate."'");
			$data = count($getData);
		}else if($type == 2){
            $filterLocationCondition = ' and location_id = '.$locationId.'';
            if(empty($projectId)){
                $filterProjectCondition = '';    
            }else{
                $filterProjectCondition = ' and project_id = '.$projectId.'';
            }
            if(empty($subDepartmentId)){
                $filterSubDepartmentCondition = '';
            }else{
                $filterSubDepartmentCondition = ' and sub_department_id = '.$subDepartmentId.'';
            }
			$getData = DB::select("SELECT 
                id,
                purchase_request_no,
                purchase_request_date from purchase_request WHERE company_id = ".$m."".$filterLocationCondition."".$filterProjectCondition."".$filterSubDepartmentCondition." and accounting_year = ".$accountingYear." and status = 1 and purchase_request_status = 2 and purchase_request_date BETWEEN '".$fromDate."' and '".$toDate."'");
			$data = count($getData);
		}else if($type == 3){
            $filterLocationCondition = ' and purchase_request.location_id = '.$locationId.'';
            if(empty($projectId)){
                $filterProjectCondition = '';    
            }else{
                $filterProjectCondition = ' and purchase_request.project_id = '.$projectId.'';
            }
            if(empty($subDepartmentId)){
                $filterSubDepartmentCondition = '';
            }else{
                $filterSubDepartmentCondition = ' and purchase_request.sub_department_id = '.$subDepartmentId.'';
            }
			$getData = DB::select("SELECT 
                purchase_request_data.purchase_request_no,
                purchase_request_data.purchase_request_date,
                purchase_request.location_id
                from purchase_request_data 
                INNER JOIN purchase_request ON purchase_request_data.purchase_request_no = purchase_request.purchase_request_no 
                WHERE purchase_request_data.company_id = ".$m."".$filterLocationCondition."".$filterProjectCondition."".$filterSubDepartmentCondition." and 
                purchase_request_data.accounting_year = ".$accountingYear." and 
                purchase_request_data.purchase_order_status = 1 and 
                purchase_request_data.purchase_request_status = 2 and 
                purchase_request.purchase_request_date BETWEEN '".$fromDate."' and '".$toDate."'");
			$data = count($getData);
		}else if($type == 4){
            $filterLocationCondition = ' and purchase_order.location_id = '.$locationId.'';
            
            if(empty($projectId)){
                $filterProjectCondition = '';    
            }else{
                $filterProjectCondition = ' and purchase_order.project_id = '.$projectId.'';
            }
            if(empty($subDepartmentId)){
                $filterSubDepartmentCondition = '';
            }else{
                $filterSubDepartmentCondition = ' and purchase_order.sub_department_id = '.$subDepartmentId.'';
            }

			$statusCondition = ' and purchase_order.status = 1';
			$purchaseOrderCondition = ' and purchase_order.purchase_order_status = 1';
			$getData = DB::select("SELECT 
                purchase_order.id,
                purchase_order.purchase_order_no,
                purchase_order.purchase_order_date,
                purchase_order.purchase_request_no,
                purchase_order.purchase_request_date
                FROM `purchase_order`  
                WHERE purchase_order.purchase_order_date BETWEEN '".$fromDate."' AND  '".$toDate."'".$purchaseOrderCondition."".$statusCondition."".$filterLocationCondition."".$filterProjectCondition."".$filterSubDepartmentCondition." and 
                purchase_order.company_id = ".$m." and purchase_order.accounting_year = ".$accountingYear." order by purchase_order.id desc");
			$data = count($getData);
		}else if($type == 5){
            $filterLocationCondition = ' and purchase_order.location_id = '.$locationId.'';
            if(empty($projectId)){
                $filterProjectCondition = '';    
            }else{
                $filterProjectCondition = ' and purchase_order.project_id = '.$projectId.'';
            }
            if(empty($subDepartmentId)){
                $filterSubDepartmentCondition = '';
            }else{
                $filterSubDepartmentCondition = ' and purchase_order.sub_department_id = '.$subDepartmentId.'';
            }
			$statusCondition = ' and purchase_order.status = 1';
			$purchaseOrderCondition = ' and purchase_order.purchase_order_status = 2';
			$getData = DB::select("SELECT 
                purchase_order.id,
                purchase_order.purchase_order_no,
                purchase_order.purchase_order_date,
                purchase_order.purchase_request_no,
                purchase_order.purchase_request_date
                FROM `purchase_order`  
                WHERE purchase_order.purchase_order_date BETWEEN '".$fromDate."' AND  '".$toDate."'".$purchaseOrderCondition."".$statusCondition."".$filterLocationCondition."".$filterProjectCondition."".$filterSubDepartmentCondition." and 
                purchase_order.company_id = ".$m." and 
                purchase_order.accounting_year = ".$accountingYear." order by purchase_order.id desc");
			$data = count($getData);
		}else if($type == 6){
            $filterLocationCondition = ' and purchase_order_data.location_id = '.$locationId.'';
            if(empty($projectId)){
                $filterProjectCondition = '';    
            }else{
                $filterProjectCondition = ' and purchase_order.project_id = '.$projectId.'';
            }
            if(empty($subDepartmentId)){
                $filterSubDepartmentCondition = '';
            }else{
                $filterSubDepartmentCondition = ' and purchase_order.sub_department_id = '.$subDepartmentId.'';
            }
			$statusCondition = ' and purchase_order_data.status = 1';
			$purchaseOrderCondition = ' and purchase_order_data.purchase_order_status = 2';
			$getData = DB::select("SELECT 
                purchase_order_data.accounting_year,
                purchase_order_data.purchase_order_no,
                purchase_order_data.purchase_order_date,
                purchase_order_data.purchase_request_no,
                purchase_order_data.purchase_request_data_record_id,
                purchase_order_data.purchase_request_date
                FROM `purchase_order_data` 
                INNER JOIN purchase_order ON purchase_order.purchase_order_no = purchase_order_data.purchase_order_no
                WHERE purchase_order_data.purchase_order_date BETWEEN '".$fromDate."' AND  '".$toDate."'".$purchaseOrderCondition."".$statusCondition."".$filterLocationCondition."".$filterProjectCondition."".$filterSubDepartmentCondition." and 
                purchase_order_data.company_id = ".$m." and purchase_order_data.accounting_year = ".$accountingYear." and purchase_order_data.grn_status = 1");
			$data = count($getData);
		}else if($type == 7){
            $filterLocationCondition = ' and grn_data.location_id = '.$locationId.'';
            if(empty($projectId)){
                $filterProjectCondition = '';    
            }else{
                $filterProjectCondition = ' and purchase_order.project_id = '.$projectId.'';
            }
            if(empty($subDepartmentId)){
                $filterSubDepartmentCondition = '';
            }else{
                $filterSubDepartmentCondition = ' and grn_data.sub_department_id = '.$subDepartmentId.'';
            }
			$getData = DB::select("SELECT 
                goods_receipt_note.id,
                goods_receipt_note.grn_no,
                goods_receipt_note.grn_date
                FROM `goods_receipt_note` 
                INNER JOIN grn_data ON goods_receipt_note.id = grn_data.grn_id
                INNER JOIN purchase_order ON purchase_order.purchase_order_no = grn_data.po_no 
                where goods_receipt_note.grn_date BETWEEN '".$fromDate."' AND  '".$toDate."'".$filterLocationCondition."".$filterProjectCondition."".$filterSubDepartmentCondition." and goods_receipt_note.company_id = ".$m." and goods_receipt_note.accounting_year = ".$accountingYear." and goods_receipt_note.grn_status = 1 and goods_receipt_note.status = 1");
			$data = count($getData);
		}else if($type == 8){
            $filterLocationCondition = ' and grn_data.location_id = '.$locationId.'';
            if(empty($projectId)){
                $filterProjectCondition = '';    
            }else{
                $filterProjectCondition = ' and purchase_order.project_id = '.$projectId.'';
            }
            if(empty($subDepartmentId)){
                $filterSubDepartmentCondition = '';
            }else{
                $filterSubDepartmentCondition = ' and grn_data.sub_department_id = '.$subDepartmentId.'';
            }
			$getData = DB::select("SELECT 
                goods_receipt_note.id,
                goods_receipt_note.grn_no,
                goods_receipt_note.grn_date
                FROM `goods_receipt_note` 
                INNER JOIN grn_data ON goods_receipt_note.id = grn_data.grn_id
                INNER JOIN purchase_order ON purchase_order.purchase_order_no = grn_data.po_no 
                where goods_receipt_note.grn_date BETWEEN '".$fromDate."' AND  '".$toDate."'".$filterLocationCondition."".$filterProjectCondition."".$filterSubDepartmentCondition." and goods_receipt_note.company_id = ".$m." and goods_receipt_note.accounting_year = ".$accountingYear." and goods_receipt_note.grn_status = 2 and goods_receipt_note.status = 1");
			$data = count($getData);
		}
		return $data;
		
	}

    public static function reverseGoodsReceiptNoteDetailBeforeApproval($m,$grnNo){
		CommonHelper::companyDatabaseConnection($m);
			$countJVSDetail = DB::table('jvs')->where('grn_no','=',$grnNo)->get();
		CommonHelper::reconnectMasterDatabase();
		$a = '';
		if(count($countJVSDetail) == '0'){
            if(singlePermission(getSessionCompanyId(), Auth::user()->id, Input::get('parentCode'), 'right_reverse', Auth::user()->acc_type)){
                $a .= '<a class="btn btn-xs btn-danger" onclick="reverseGoodsReceiptNoteDetailBeforeApproval(\''.$m.'\',\''.$grnNo.'\')">Reverse Goods Receipt Note Detail Before Approval</a>';
            }else{
                $a .= '<a disabled class="btn btn-xs btn-danger">Reverse Goods Receipt Note Detail Before Approval</a>';                
            }
		}else{
			$a = '';
		}
		return $a;
	}
	
	public static function reverseGoodsReceiptNoteDetailAfterApproval($m,$grnNo){
		CommonHelper::companyDatabaseConnection($m);
			$countPVSDetail = DB::table('pvs')->where('grn_no','=',$grnNo)->get();
			$countAPADAGRNDetail = DB::table('advanced_paid_amount_detail_against_grn')->where('grn_no','=',$grnNo)->get();
		CommonHelper::reconnectMasterDatabase();
		$a = '';
		if(count($countPVSDetail) == '0' && count($countAPADAGRNDetail) == 0){
            if(singlePermission(getSessionCompanyId(), Auth::user()->id, Input::get('parentCode'), 'right_reverse', Auth::user()->acc_type)){
			    $a .= '<a class="btn btn-xs btn-danger" onclick="reverseGoodsReceiptNoteDetailAfterApproval(\''.$m.'\',\''.$grnNo.'\')">Reverse Goods Receipt Note Detail After Approval</a>';
            }else{
			    $a .= '<a disabled class="btn btn-xs btn-danger">Reverse Goods Receipt Note Detail After Approval</a>';                
            }
		}else{
			$a = '';
		}
		return $a;
	}

    public static function displayLocationListForTable($m){
        $locationList = DB::table('location')->where('company_id','=',$m)->orderBy('id', 'DESC')->get();
        $data = '';
        foreach($locationList as $llRow){
            // $data.='<th class="text-center">'.$llRow->location_name.'</th>';
            $data.='<th class="text-center"></th>';
        }

        return $data;
    }

    //$getBillRecordingDetail = DB::selectOne("select `bill_recording`.`id`, `bill_recording`.`bill_recording_no`, `bill_recording`.`jv_no`, `bill_recording`.`supplier_id`, `bill_recording`.`bill_recording_acc_id`, `bill_recording`.`supplier_acc_id`, `bill_recording`.`bill_no`, `bill_recording`.`bill_date`, `bill_recording`.`price`, `bill_recording`.`qty`, `bill_recording`.`discount`, `bill_recording`.`total_amount`, `bill_recording`.`description`, `bill_recording`.`date`, `bill_recording`.`time`, `bill_recording`.`username`, `bill_recording`.`status`, `bill_recording`.`bill_recording_status`, `bill_recording`.`bill_record_type`, (SELECT COALESCE(SUM(bill_payments.payment_amount),0) FROM bill_payments WHERE bill_payments.bill_recording_id = bill_recording.id GROUP BY bill_payments.bill_recording_id) as totalPaymentAmount from `bill_recording` where bill_recording.bill_recording_no = '".$billRecordingNo."' order by `bill_recording`.`bill_date` asc");

    public static function displayLocationWiseCurrentBalance($m,$mainIcId,$subIcId){
        $data = '';
        // $getAllItems = Cache::rememberForever('cacheZViewInventory_'.$m.'',function() use ($m){
        //     return DB::table('z_view_inventory')->where('company_id','=',$m)->get();
        // });
        $locationList = DB::table('location')->where('company_id','=',$m)->orderBy('id', 'DESC')->get();
        foreach ($locationList as $key => $location) {
            $makeCurrentBalance = CommonFacades::stockLocationWiseSum($mainIcId, $subIcId, $location->id);
            $data.='<td class="text-center">'.$makeCurrentBalance.'('. CommonFacades::get_location_name($location->id) .')</td>';
            // dd(CommonFacades::stockLocationWiseSum($mainIcId, $subIcId, $location->id));
        }
        // dd('in');
        // foreach($getAllItems as $gaiRow){
        //     if($gaiRow->main_ic_id == $mainIcId && $gaiRow->sub_ic_id == $subIcId){
        //         // dd($gaiRow);
        //         $makeCurrentBalance = $gaiRow->openingQty + $gaiRow->purchaseQty + $gaiRow->stockReceived + $gaiRow->storeChallanReturnQty - $gaiRow->storeChallanQty - $gaiRow->stockTransfer - $gaiRow->technicianIssuance ;
        //         $data.='<td class="text-center">'.$makeCurrentBalance.'('. CommonFacades::get_location_name($gaiRow->location_id) .')</td>';
        
        //     }
        // }
        return $data;
    }
	
	public static function displayMaterialIssuanceVoucherListButton($param1,$param2,$param3,$param4,$param5,$param6){
		$data.='<li><a onclick="showDetailModelTwoParamerter(\''.$paramEditOneMI.'\',\''.$paramTwoEdit.'\',\''.$paramEditThreeMI.'\',\''.$m.'\')"><span class="glyphicon glyphicon-edit"></span> Edit Material Issuance</a></li>';
					$data.='<li><a onclick="showDetailModelTwoParamerter(\''.$paramViewOneMI.'\',\''.$paramTwoEdit.'\',\''.$paramViewThreeMI.'\',\''.$m.'\')"><span class="glyphicon glyphicon-eye-open"></span> View Material Issuance</a></li>';
	}



    public static function checkBomStatusProductWise($param1,$param2){
        CommonFacades::companyDatabaseConnection($param1);
        $checkBomStatusProductWise = DB::table('bom')->where('bom_no','=',$param2)->first();
        CommonFacades::reconnectMasterDatabase();
        return $checkBomStatusProductWise->bom_status;
    }

    public static function getTotalReceivedQuantityByPoNoAndPoDataId($param1,$param2,$param3,$param4){
        CommonFacades::companyDatabaseConnection($param1);
        $getTotalReceivedQuantityByPoNoAndPoDataId = DB::table('grn_data')->where('sub_item_id','=',$param4)->where('po_no','=',$param3)->where('status','=','1')->orWhere('po_data_id','=',$param2)->get();
        CommonFacades::reconnectMasterDatabase();
        $a = 0;
        foreach($getTotalReceivedQuantityByPoNoAndPoDataId as $row){
            $tqrigc = $row->tqrigc + $row->loss_quantity;
            $a += $tqrigc;
        }
        echo $a;
    }


    public static function displayApproveDeleteRepostButtonAdditionalRequestRawMaterialDispensing($param1,$param2,$param3){
        if($param3 == 1){
            ?>
            <a class="delete-modal btn btn-xs btn-primary btn-xs" data-dismiss="modal" aria-hidden="true" onclick="approveCompanyPurchaseAdditionalRequestRawMaterialDispensing('<?php echo $param1 ?>','<?php echo $param2;?>')">
                <span class="glyphicon glyphicon-ok"></span> Approve Additional Request Raw Material Dispensing Voucher
            </a>

            <a class="delete-modal btn btn-xs btn-danger btn-xs" data-dismiss="modal" aria-hidden="true" onclick="deleteCompanyPurchaseAdditionalRequestRawMaterialDispensing('<?php echo $param1 ?>','<?php echo $param2;?>')">
                <span class="glyphicon glyphicon-trash"></span> Delete Additional Request Raw Material Dispensing Voucher
            </a>
        <?php }else if($param3 == 3){?>
            <a class="delete-modal btn btn-xs btn-warning btn-xs" data-dismiss="modal" aria-hidden="true" onclick="repostCompanyPurchaseAdditionalRequestRawMaterialDispensing('<?php echo $param1 ?>','<?php echo $param2;?>')">
                <span class="glyphicon glyphicon-edit"></span> Repost Additional Request Raw Material Dispensing Voucher
            </a>
        <?php }
    }

    public static function displayApproveDeleteRepostButtonRawMaterialDispensing($param1,$param2,$param3,$param4,$param5,$param6,$param7,$param8){
        if($param3 == 1 && $param2 == 1){
            ?>
            <a class="delete-modal btn btn-xs btn-primary btn-xs" data-dismiss="modal" aria-hidden="true" onclick="approveCompanyPurchaseRawMaterialDispensing('<?php echo $param1 ?>','<?php echo $param2;?>','<?php echo $param3 ?>','<?php echo $param4 ?>','<?php echo $param5 ?>','<?php echo $param6 ?>')">
                <span class="glyphicon glyphicon-ok"></span> Approve Raw Material Dispensing Voucher
            </a>

            <a class="delete-modal btn btn-xs btn-danger btn-xs" data-dismiss="modal" aria-hidden="true" onclick="deleteCompanyPurchaseRawMaterialDispensing('<?php echo $param1 ?>','<?php echo $param2;?>','<?php echo $param3 ?>','<?php echo $param4 ?>','<?php echo $param5 ?>','<?php echo $param6 ?>','<?php echo $param7?>','<?php echo $param8?>')">
                <span class="glyphicon glyphicon-trash"></span> Delete Raw Material Dispensing Voucher
            </a>
        <?php }else if($param3 == 2 && $param2 == 1){?>
            <a class="delete-modal btn btn-xs btn-warning btn-xs" data-dismiss="modal" aria-hidden="true" onclick="repostCompanyPurchaseTwoTableRecords('<?php echo $param1 ?>','<?php echo $param2;?>','<?php echo $param3 ?>','<?php echo $param4 ?>','<?php echo $param5 ?>','<?php echo $param6 ?>','<?php echo $param7 ?>','<?php echo $param8;?>','<?php echo $param9;?>')">
                <span class="glyphicon glyphicon-edit"></span> Repost Voucher
            </a>
        <?php }
    }

    public static function displayApproveDeleteRepostButtonPackingMaterialDispensing($param1,$param2,$param3,$param4,$param5,$param6,$param7,$param8){
        if($param3 == 1 && $param2 == 1){
            ?>
            <a class="delete-modal btn btn-xs btn-primary btn-xs" data-dismiss="modal" aria-hidden="true" onclick="approveCompanyPurchasePackingMaterialDispensing('<?php echo $param1 ?>','<?php echo $param2;?>','<?php echo $param3 ?>','<?php echo $param4 ?>','<?php echo $param5 ?>','<?php echo $param6 ?>')">
                <span class="glyphicon glyphicon-ok"></span> Approve Packing MAterial Dispensing Voucher
            </a>
            <a class="delete-modal btn btn-xs btn-danger btn-xs" data-dismiss="modal" aria-hidden="true" onclick="deleteCompanyPurchasePackingMaterialDispensing('<?php echo $param1 ?>','<?php echo $param2;?>','<?php echo $param3 ?>','<?php echo $param4 ?>','<?php echo $param5 ?>','<?php echo $param6 ?>','<?php echo $param7?>','<?php echo $param8?>')">
                <span class="glyphicon glyphicon-trash"></span> Delete Packing Material Dispensing Voucher
            </a>
        <?php }else if($param3 == 2 && $param2 == 1){?>
            <a class="delete-modal btn btn-xs btn-warning btn-xs" data-dismiss="modal" aria-hidden="true" onclick="repostCompanyPurchaseTwoTableRecords('<?php echo $param1 ?>','<?php echo $param2;?>','<?php echo $param3 ?>','<?php echo $param4 ?>','<?php echo $param5 ?>','<?php echo $param6 ?>','<?php echo $param7 ?>','<?php echo $param8;?>','<?php echo $param9;?>')">
                <span class="glyphicon glyphicon-edit"></span> Repost Voucher
            </a>
        <?php }
    }

    public static function totalDispensingLotNoAndDispenseQtyAgainstSubItemIDandProductionRequestDataID($param1,$param2,$param3,$param4,$param5){
        CommonFacades::companyDatabaseConnection($param1);
        if($param5 == 'raw'){
            $assignMaterialDispensingLotNo = DB::table('assign_raw_material_dispensing_lot_no')->where('raw_material_dispensing_data_id','=',$param2)->where('raw_dispensing_no','=',$param3)->get();
        }else if($param5 == 'packing'){
            $assignMaterialDispensingLotNo = DB::table('assign_packing_material_dispensing_lot_no')->where('packing_material_dispensing_data_id','=',$param2)->where('packing_dispensing_no','=',$param3)->get();
        }
        CommonFacades::reconnectMasterDatabase();
        $a = '';
        $b = 0;
        if($param4 == 'lot_no'){
            foreach($assignMaterialDispensingLotNo as $row){
                $a .= '<span>'.$row->$param4.'</span><br />';
            }
            return $a;
        }else{
            foreach($assignMaterialDispensingLotNo as $row){
                $b += $row->$param4;
                $a .= '<span>'.$row->$param4.'</span><br />';
            }
            return $a.'<*>'.$b;
        }

    }

    public static function displayDispensingButtons($param1,$param2,$param3,$param4,$param5,$param6,$param7,$param8,$param9,$param10,$param11){
        CommonFacades::companyDatabaseConnection($param1);
        $checkDispensingDetail = DB::table('production_request_data')->where('id','=',$param4)->where('production_request_no','=',$param2)->where('status','=','1')->first();
        CommonFacades::reconnectMasterDatabase();
        $a = '';
        $param14 = 'pdc/addPackingMaterialDispensingForm';
        $param15 = 'Packing Material Dispensing Form';
        if($checkDispensingDetail->raw_material_issuance_status == 1){
            $param16 = 'pdc/addRawMaterialDispensingForm';
            $param12 = $param2.'<*>'.$param3.'<*>'.$param4.'<*>'.$param5.'<*>'.$param6.'<*>'.$param7.'<*>'.$param8.'<*>'.$param9.'<*>'.$param10.'<*>'.$param11;
            $param13 = 'Raw Material Dispensing Form';
            $a .= '<li><a onclick="showMasterTableEditModel(\''.$param16.'\',\''.$param12.'\',\''.$param13.'\',\''.$param1.'\')"><span class="glyphicon glyphicon-edit"> </span> Add R.M.D.D</a></li>';
        }else{
            $param16 = 'pdc/viewRawMaterialVoucherDetail';
            $param12 = $param11.'<*>'.$param3.'<*>'.$param5.'<*>'.$param6;
            $param13 = 'Raw Material Dispensing Detail';

            $a .= '<li><a onclick="showMasterTableEditModel(\''.$param16.'\',\''.$param12.'\',\''.$param13.'\',\''.$param1.'\')"><span class="glyphicon glyphicon-eye-open"> </span> View R.M.D.D</a></li>';
        }
        //if($checkDispensingDetail->packing_material_issuance_status == 1){
        //$a .= '<li><a onclick="showMasterTableEditModel(\''.$param14.'\',\''.$param12.'\',\''.$param15.'\',\''.$param1.'\')"><span class="glyphicon glyphicon-edit"></span> Add P.M.D.D</a></li>';
        //}

        return $a;
    }

    public static function purchaseItemSummaryDetail($param1,$param2,$param3){
        if($param2 == 1){
            $purchaseItemSummary = DB::table('purchase_order_data')
                ->select(
                    'purchase_order_data.category_id',
                    'purchase_order_data.sub_item_id',
                    'purchase_order_data.supplier_id',
                    'purchase_order_data.unit_price',
                    'purchase_order_data.purchase_order_qty',
                    'purchase_order_data.item_name',
                    'category.main_ic',
                    'subitem.item_code',
                    'subitem.sub_ic'
                )
                ->leftJoin('category', 'purchase_order_data.category_id', '=', 'category.id')
                ->leftJoin('subitem', 'purchase_order_data.sub_item_id', '=', 'subitem.id')
                ->where('purchase_order_data.purchase_order_no', '=', $param3)
                ->where('purchase_order_data.status', '=', '1')
                ->get();
        }else{
            $purchaseItemSummary = DB::table('grn_data')
                ->select(
                    'grn_data.po_date',
                    'grn_data.category_id',
                    'grn_data.sub_item_id',
                    'goods_receipt_note.supplier_id',
                    'goods_receipt_note.supplier_location_id',
                    'grn_data.grn_date',
                    'grn_data.rate',
                    'grn_data.tqrigc',
                    'category.main_ic',
                    'subitem.item_code',
                    'subitem.sub_ic'
                )
                ->join('category', 'grn_data.category_id', '=', 'category.id')
                ->join('subitem', 'grn_data.sub_item_id', '=', 'subitem.id')
                ->join('goods_receipt_note', 'grn_data.grn_no', '=', 'goods_receipt_note.grn_no')
                ->where('grn_data.grn_no', '=', $param3)
                ->where('grn_data.status', '=', '1')
                ->get();
        }
        $data='';
        $data.='<div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="table-responsive"><table class="table table-bordered sf-table-list"><thead><th class="text-center">S.No</th><th class="text-center">Category Name</th><th class="text-center">Item Code</th><th class="text-center">Item Name</th><th class="text-center">Qty.</th><th class="text-center">Unit Price</th><th class="text-center">Sub-Total</th></thead><tbody>';
		$counter = 1;
		foreach($purchaseItemSummary as $row){
			$categoryName = $row->main_ic;
			$itemCode = $row->item_code ?? 0;
            $itemName = $row->sub_ic ?? $row->item_name;
            if($param2 == 1){
                $rate = $row->unit_price;
                $qty = $row->purchase_order_qty;
                $amount = $row->purchase_order_qty*$row->unit_price;
            }else{
                $rate = $row->rate;
                $qty = $row->tqrigc;
                $amount = $row->tqrigc*$row->rate;
            }
			$data.='<tr><td class="text-center">'.$counter++.'</td><td>'.$categoryName.'</td><td>'.$itemCode.'</td><td>'.$itemName.'</td><td class="text-center">'.$qty.'</td><td class="text-center">'.$rate.'</td><td class="text-center">'.number_format($amount).'</td></tr>';
		}
		$data.='</tbody></table></div></div></div>';
		return $data;
    }

    public static function purchaseGoodsReceiptNoteSummaryDetail($param1,$param2){
        $purchaseGoodsReceiptNoteSummaryDetail = DB::table('grn_data')
            ->select(
                'grn_data.po_date',
                'grn_data.category_id',
                'grn_data.sub_item_id',
                'goods_receipt_note.supplier_id',
                'goods_receipt_note.supplier_location_id',
                'grn_data.grn_date',
                'grn_data.rate',
                'grn_data.tqrigc',
                'category.main_ic',
                'subitem.item_code',
                'subitem.sub_ic'
            )
            ->join('category', 'grn_data.category_id', '=', 'category.id')
            ->join('subitem', 'grn_data.sub_item_id', '=', 'subitem.id')
            ->join('goods_receipt_note', 'grn_data.grn_no', '=', 'goods_receipt_note.grn_no')
			->where('grn_data.grn_no', '=', $param2)
			->where('grn_data.status', '=', '1')
			->get();
		$data='';
        $data.='<div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="table-responsive"><table class="table table-bordered sf-table-list"><thead><th class="text-center">S.No</th><th class="text-center">Category Name</th><th class="text-center">Item Code</th><th class="text-center">Item Name</th><th class="text-center">Receive Qty.</th><th class="text-center">Unit Price</th><th class="text-center">Sub-Total</th></thead><tbody>';
		$counter = 1;
		foreach($purchaseGoodsReceiptNoteSummaryDetail as $row){
			$categoryName = $row->main_ic;
			$itemCode = $row->item_code;
			$itemName = $row->sub_ic;
			$data.='<tr><td class="text-center">'.$counter++.'</td><td>'.$categoryName.'</td><td>'.$itemCode.'</td><td>'.$itemName.'</td><td class="text-center">'.$row->tqrigc.'</td><td class="text-center">'.$row->rate.'</td><td class="text-center">'.number_format($row->tqrigc * $row->rate).'</td></tr>';
		}
		$data.='</tbody></table></div></div></div>';
		return $data;
	}

    public static function getOpeningQuantityForSubItem($param1,$param2){
        CommonFacades::companyDatabaseConnection($param1);
        $getOpeningQuantityForSubItem = DB::table('fara')->where('sub_ic_id','=',$param2)->where('status','=','1')->first();
        CommonFacades::reconnectMasterDatabase();

        return $getOpeningQuantityForSubItem->qty;
    }

    public static function getAllProductNameUsingProductionNoAndProductionRequestDataID($param1,$param2,$param3,$param4){

    }

    public static function purchaseOrderNumberByPurchaseRequestNo($param1,$param2){
        $getPurchaseOrderDetail = DB::table('purchase_order')->select('purchase_order_no','purchase_order_date')->where('purchase_request_no','=',$param2)->get();
        $data = '';
        foreach ($getPurchaseOrderDetail as $row) {
            $title = 'View Purchase Order Detail';
            $pageLink = 'pdc/viewPurchaseOrderVoucherDetail';
            $data .= '<span class="btn btn-xs btn-success" onclick="showDetailModelOneParamerter(\''.$pageLink.'\',\''.$row->purchase_order_no.'\',\''.$title.'\')">'.strtoupper($row->purchase_order_no).' - '.CommonHelper::changeDateFormat($row->purchase_order_date).'</span><div class="lineHeight">&nbsp;</div>';
        }
        return $data;
    }

    public static function purchaseOrderNumberByGRNNo($param1,$param2,$param3){
        return $param1.'-'.$param2.'-'.$param3;
        CommonFacades::companyDatabaseConnection($param1);
        $getPurchaseOrderDetail = DB::table('purchase_order')->select('purchase_order_no','purchase_order_date')->where('purchase_request_no','=',$param2)->get();
        $a = '';
        foreach ($getPurchaseOrderDetail as $row) {
            $title = 'View Purchase Order Detail';
            $pageLink = 'pdc/viewPurchaseOrderVoucherDetail';
            $a .= '<a onclick="showDetailModelOneParamerter(\''.$pageLink.'\',\''.$row->purchase_order_no.'\',\''.$title.'\')">PO No =>'.$row->purchase_order_no.' => '.'PO Date =>'.CommonFacades::changeDateFormat($row->purchase_order_date).'</a><br />';
        }
        CommonFacades::reconnectMasterDatabase();
        return $a;
    }

    public static function SystemGeneratedLotNoList($param1,$param2,$param3){
        CommonFacades::companyDatabaseConnection($param1);
        $getLotNoList = DB::table('fara')->select('lot_no')->where('sub_ic_id','=',$param2)->where('lot_no','!=','')->get();
        $a = '';
        foreach ($getLotNoList as $row) {
            $lotNo = $row->lot_no;
            $countLotNoLength = strlen($lotNo);
            if($countLotNoLength == 4){
                $makeLotNo = substr_replace($lotNo, '00' . substr($lotNo, -1), -1);
            }else if($countLotNoLength == 5){
                $makeLotNo = substr_replace($lotNo, '0' . substr($lotNo, -2), -2);
            }else{
                $makeLotNo = $lotNo;
            }
            $selectedLotNo = '';
            if($param3 == $row->lot_no){
                $selectedLotNo = 'selected';
            }
            $a .= '<option value="'.$row->lot_no.'"'.$selectedLotNo.'>'.$makeLotNo.'</option>';
        }
        CommonFacades::reconnectMasterDatabase();
        return $a;
    }

    public static function getSaleTaxHeadUsingPurchaseOrderNo($param1,$param2,$param3,$param4){
        CommonFacades::companyDatabaseConnection($param1);
        $getPurchaseRequestNo = DB::table('purchase_order_data')->where('purchase_order_no','=',$param2)->where('category_id','=',$param3)->where('sub_item_id','=',$param4)->first();
        CommonFacades::reconnectMasterDatabase();
        return $getPurchaseRequestNo->sale_tax_head;
    }

    public static function getPurchaseRequestNoUsingPurchaseOrderNo($param1,$param2,$param3,$param4){
        CommonFacades::companyDatabaseConnection($param1);
        $getPurchaseRequestNo = DB::table('purchase_order_data')->where('purchase_order_no','=',$param2)->where('category_id','=',$param3)->where('sub_item_id','=',$param4)->first();
        CommonFacades::reconnectMasterDatabase();
        return $getPurchaseRequestNo->purchase_request_no;
    }

    public static function getPurchaseRequestDateUsingPurchaseOrderNo($param1,$param2,$param3,$param4){
        CommonFacades::companyDatabaseConnection($param1);
        $getPurchaseRequestDate = DB::table('purchase_order_data')->where('purchase_order_no','=',$param2)->where('category_id','=',$param3)->where('sub_item_id','=',$param4)->first();
        CommonFacades::reconnectMasterDatabase();
        return $getPurchaseRequestDate->purchase_request_date;
    }

    public static function makeSystemGeneratedGrnLotNo($param1,$param2,$param3){
        CommonFacades::companyDatabaseConnection($param1);
        $str = DB::selectOne("select max(convert(substr(`lot_no`,4,length(substr(`lot_no`,4))-4),signed integer)) reg from `grn_data` where substr(`lot_no`,-2,2) = ".date('y')."")->reg;
        $checkStrNumber = $str + 1;
        if ($checkStrNumber >= 1 && $checkStrNumber <= 9) {
            $concateValue = $str.'00';
            if($str == 0){
                $newStrNumber = '001';
            }else {
                $newStrNumber = $concateValue + 1;
            }
            $makeNewGrnLotNo = 'R' . date('y') . ($newStrNumber);
        }else {
            $makeNewGrnLotNo = 'R' . date('y') . ($str);
        }
        //$makeNewGrnLotNo = DB::selectOne('select `lot_no` from `accounts` where `id` = '.$param1.'')->name;
        //'R18001';
        CommonFacades::reconnectMasterDatabase();
        echo $makeNewGrnLotNo;
    }

    public static function categoryList($param1,$param2){
        echo '<option value="">Select Category</option>';
        $categoryList = Cache::rememberForever('cacheCategory_'.$param1.'',function() use ($param1){
            return DB::select("select * from category where company_id = ".$param1."");
        });
        foreach($categoryList as $row){
            $disabled = '';
            if($row->status != 1){
                $disabled = 'disabled';
            } 
            ?>
                <option value="<?php echo $row->id;?>" <?php if($param2 == $row->id){echo 'selected';}?> <?php echo $disabled?>><?php echo $row->main_ic;?></option>
            <?php
        }
    }

    public static function subItemList($param1,$param2,$param3){
        echo '<option value="">Select Item</option>';
        $subItemListTwo = Cache::rememberForever('cacheSubItemItemWise_'.$param3.'_'.$param1.'',function() use ($param1,$param3){
            return DB::select("
                select
                    subitem.id,
                    subitem.accounting_year,
                    subitem.item_code,
                    subitem.unit_of_gram,
                    subitem.sub_ic,
                    subitem.main_ic_id,
                    subitem.acc_id,
                    subitem.reorder_level,
                    subitem.time,
                    subitem.date,
                    subitem.action,
                    subitem.username,
                    subitem.status,
                    subitem.type,
                    subitem.uom,
                    subitem.stockType,
                    subitem.itemType,
                    subitem.company_id,
                    subitem.delete_username,
                    category.main_ic
                from 
                    subitem,category
                where subitem.is_approved = 1 and subitem.main_ic_id = category.id and subitem.company_id = ".$param1." and subitem.main_ic_id = ".$param3."
            ");
        });
        
        foreach($subItemListTwo as $row){
            $disabled = '';
            if($row->status != 1){
                $disabled = 'disabled';
            }
            ?>
            
            <option value="<?php echo $row->id;?>" <?php if($param2 == $row->id){echo 'selected';}?> <?php echo $disabled?>><?php echo $row->item_code.' <--> '.$row->sub_ic;?></option>
            <?php
        }
    }

    public static function checkVoucherStatus($param1,$param2){
        if($param1 == 1 && $param2 == 1){
            return 'Pending';
        }else if($param2 == 2){
            return 'Deleted';
        }else if($param1 == 2 && $param2 == 1){
            return 'Approve';
        }else if($param1 == 3 && $param2 == 1){
            return 'Rejected';
        }else if($param1 == 4 && $param2 == 1){
            return 'Received';
        }
    }

    public static function displayApproveDeleteRepostButtonOneTable(){

    }

    public static function displayApproveDeleteRepostButtonTwoTable($param1,$param2,$param3,$param4,$param5,$param6,$param7,$param8,$param9){
        $param1.' - '.$param2.' - '.$param3.' - '.$param4.' - '.$param5.' - '.$param6.' - '.$param7.' - '.$param8.' - '.$param9;
        if($param3 == 1 && $param2 == 1){
            ?>
            <a class="delete-modal btn btn-xs btn-primary btn-xs" data-dismiss="modal" aria-hidden="true" onclick="approveCompanyPurchaseTwoTableRecords('<?php echo $param1 ?>','<?php echo $param2;?>','<?php echo $param3 ?>','<?php echo $param4 ?>','<?php echo $param5 ?>','<?php echo $param6 ?>','<?php echo $param7 ?>','<?php echo $param8;?>','<?php echo $param9;?>')">
                <span class="glyphicon glyphicon-ok"></span> Approve Voucher
            </a>

            <a class="delete-modal btn btn-xs btn-danger btn-xs" data-dismiss="modal" aria-hidden="true" onclick="deleteCompanyPurchaseTwoTableRecords('<?php echo $param1 ?>','<?php echo $param2;?>','<?php echo $param3 ?>','<?php echo $param4 ?>','<?php echo $param5 ?>','<?php echo $param6 ?>','<?php echo $param7 ?>','<?php echo $param8;?>','<?php echo $param9;?>')">
                <span class="glyphicon glyphicon-trash"></span> Delete Voucher
            </a>
            <?php
        }else if($param3 == 2 && $param2 == 1){
            ?>
            <a class="delete-modal btn btn-xs btn-warning btn-xs" data-dismiss="modal" aria-hidden="true" onclick="repostCompanyPurchaseTwoTableRecords('<?php echo $param1 ?>','<?php echo $param2;?>','<?php echo $param3 ?>','<?php echo $param4 ?>','<?php echo $param5 ?>','<?php echo $param6 ?>','<?php echo $param7 ?>','<?php echo $param8;?>','<?php echo $param9;?>')">
                <span class="glyphicon glyphicon-edit"></span> Repost Voucher
            </a>
            <?php
        }
    }

    public static function displayApproveDeleteRepostButtonGoodsReceiptNote($param1,$param2,$param3,$param4,$param5,$param6,$param7,$param8,$param9){
        $param1.' - '.$param2.' - '.$param3.' - '.$param4.' - '.$param5.' - '.$param6.' - '.$param7.' - '.$param8.' - '.$param9;
        if($param3 == 1 && $param2 == 1){
            ?>
            <?php 
            if(singlePermission(getSessionCompanyId(), Auth::user()->id, Input::get('parentCode'), 'right_approve', Auth::user()->acc_type)){
                ?>
                <a class="delete-modal btn btn-xs btn-primary btn-xs" data-dismiss="modal" aria-hidden="true" onclick="approveCompanyPurchaseGoodsReceiptNote('<?php echo $param1 ?>','<?php echo $param2;?>','<?php echo $param3 ?>','<?php echo $param4 ?>','<?php echo $param5 ?>','<?php echo $param6 ?>','<?php echo $param7 ?>','<?php echo $param8;?>','<?php echo $param9;?>')">
                    <span class="glyphicon glyphicon-ok"></span> Approve Voucher
                </a>
            <?php }else{
                ?>
                <a disabled class="btn btn-xs btn-primary btn-xs">
                    <span class="glyphicon glyphicon-ok"></span> Approve Voucher
                </a>
            <?php } ?>

            <?php /*?><a class="delete-modal btn btn-xs btn-danger btn-xs" data-dismiss="modal" aria-hidden="true" onclick="deleteCompanyPurchaseTwoTableRecords('<?php echo $param1 ?>','<?php echo $param2;?>','<?php echo $param3 ?>','<?php echo $param4 ?>','<?php echo $param5 ?>','<?php echo $param6 ?>','<?php echo $param7 ?>','<?php echo $param8;?>','<?php echo $param9;?>')">
                <span class="glyphicon glyphicon-trash"></span> Delete Voucher
            </a><?php */?>
        <?php }else if($param3 == 2 && $param2 == 1){?>
            <a class="delete-modal btn btn-xs btn-warning btn-xs" data-dismiss="modal" aria-hidden="true" onclick="repostCompanyPurchaseTwoTableRecords('<?php echo $param1 ?>','<?php echo $param2;?>','<?php echo $param3 ?>','<?php echo $param4 ?>','<?php echo $param5 ?>','<?php echo $param6 ?>','<?php echo $param7 ?>','<?php echo $param8;?>','<?php echo $param9;?>')">
                <span class="glyphicon glyphicon-edit"></span> Repost Voucher
            </a>
        <?php }
    }

    public static function displayPurchaseRequestVoucherListButton($param1,$param2,$param3,$param4,$param5,$param6,$param7,$param8,$param9,$param10,$param11){
        $tableOne = $param8;
        $tableTwo = $param9;
        if($param3 == 1 && $param2 != 2){
            $dropdownList = '';
            if(singlePermission(getSessionCompanyId(), Auth::user()->id, Input::get('parentCode'), 'right_edit', Auth::user()->acc_type)){
                $dropdownList .= '<li><a onclick="showMasterTableEditModel(\''.$param10.'\',\''.$param4.'\',\''.$param11.'\',\''.$param1.'\')"><span class="glyphicon glyphicon-edit"></span> Edit</a></li>';
            }
            if(singlePermission(getSessionCompanyId(), Auth::user()->id, Input::get('parentCode'), 'right_delete', Auth::user()->acc_type)){
                $dropdownList .= '<li><a onclick="deleteCompanyPurchaseTwoTableRecords(\''.$param1.'\',\''.$param2.'\',\''.$param3.'\',\''.$param4.'\',\''.$param5.'\',\''.$param6.'\',\''.$param7.'\',\''.$param8.'\',\''.$param9.'\')"><span class="glyphicon glyphicon-trash"></span> Delete</a></li>';
            }
            return $dropdownList;
        }
        if($param3 == 1 && $param2 == 2){
            $dropdownList = '';
            // if(singlePermission(getSessionCompanyId(), Auth::user()->id, Input::get('parentCode'), 'right_edit', Auth::user()->acc_type)){
            //     $dropdownList .= '<li><a onclick="showMasterTableEditModel(\''.$param10.'\',\''.$param4.'\',\''.$param11.'\',\''.$param1.'\')"><span class="glyphicon glyphicon-edit"></span> Edit</a></li>';
            // }
            return $dropdownList;
        }
        else if($param3 == 2 && $param2 != 2){
            return '<li><a onclick="repostCompanyPurchaseTwoTableRecords(\''.$param1.'\',\''.$param2.'\',\''.$param3.'\',\''.$param4.'\',\''.$param5.'\',\''.$param6.'\',\''.$param7.'\',\''.$param8.'\',\''.$param9.'\')"><span class="glyphicon glyphicon-edit"></span> Restore</a></li>';
        }
    }
	
	public static function displayDirectGoodsReceiptNoteVoucherListButton($param1,$param2,$param3,$param4,$param5,$param6,$param7,$param8,$param9,$param10,$param11,$param12){
		$tableOne = $param8;
        $tableTwo = $param9;
        if($param3 == 1){
            return '<li><a onclick="showMasterTableEditModel(\''.$param10.'\',\''.$param4.'\',\''.$param11.'\',\''.$param1.'\')"><span class="glyphicon glyphicon-edit"></span> Edit</a></li><li><a onclick="deleteDirectGoodsReceiptNoteDetail(\''.$param1.'\',\''.$param2.'\',\''.$param3.'\',\''.$param4.'\',\''.$param12.'\')"><span class="glyphicon glyphicon-trash"></span> Delete</a></li>';
        }else if($param3 == 2){
            return '<li><a onclick="restoreDirectGoodsReceiptNoteDetail(\''.$param1.'\',\''.$param2.'\',\''.$param3.'\',\''.$param4.'\',\''.$param12.'\')"><span class="glyphicon glyphicon-edit"></span> Restore</a></li>';
        }
	}

    public static function displayGoodsReceiptNoteVoucherListButton($param1,$param2,$param3,$param4,$param5,$param6,$param7,$param8,$param9,$param10,$param11){
        $tableOne = $param8;
        $tableTwo = $param9;
        if($param3 == 1 && $param2 == 1){
            $dropdownList = '';
            if(singlePermission(getSessionCompanyId(), Auth::user()->id, Input::get('parentCode'), 'right_edit', Auth::user()->acc_type)){
                $dropdownList .= '<li><a onclick="showMasterTableEditModel(\''.$param10.'\',\''.$param4.'\',\''.$param11.'\',\''.$param1.'\')"><span class="glyphicon glyphicon-edit"></span> Edit</a></li>';
            }
            if(singlePermission(getSessionCompanyId(), Auth::user()->id, Input::get('parentCode'), 'right_delete', Auth::user()->acc_type)){
                $dropdownList .= '<li><a onclick="deleteCompanyPurchaseTwoTableRecords(\''.$param1.'\',\''.$param2.'\',\''.$param3.'\',\''.$param4.'\',\''.$param5.'\',\''.$param6.'\',\''.$param7.'\',\''.$param8.'\',\''.$param9.'\')"><span class="glyphicon glyphicon-trash"></span> Delete</a></li>';
            }
            return $dropdownList;
        }else if($param3 == 2 && $param2 == 1){
            return '<li><a onclick="repostCompanyPurchaseTwoTableRecords(\''.$param1.'\',\''.$param2.'\',\''.$param3.'\',\''.$param4.'\',\''.$param5.'\',\''.$param6.'\',\''.$param7.'\',\''.$param8.'\',\''.$param9.'\')"><span class="glyphicon glyphicon-edit"></span> Restore</a></li>';
        }
    }



    public static function changeActionButtons($param1,$param2,$param3,$param4,$param5,$param6,$param7,$param8,$param9,$param10,$param11){
        $tableOne = $param8;
        $tableTwo = $param9;
        ?>
        <?php if($param3 == 1 && $param2 == 1){?>
            <button class="edit-modal btn btn-xs btn-info" onclick="showMasterTableEditModel('<?php echo $param10;?>','<?php echo $param4 ?>','<?php echo $param11 ?>','<?php echo $param1?>')">
                <span class="glyphicon glyphicon-edit"> P</span>
            </button>
            <button class="delete-modal btn btn-xs btn-danger btn-xs" onclick="deleteCompanyPurchaseTwoTableRecords('<?php echo $param1 ?>','<?php echo $param2;?>','<?php echo $param3 ?>','<?php echo $param4 ?>','<?php echo $param5 ?>','<?php echo $param6 ?>','<?php echo $param7 ?>','<?php echo $param8;?>','<?php echo $param9;?>')">
                <span class="glyphicon glyphicon-trash"> P</span>
            </button>
        <?php }else if($param3 == 2 && $param2 == 1){?>
            <button class="delete-modal btn btn-xs btn-warning btn-xs" onclick="repostCompanyPurchaseTwoTableRecords('<?php echo $param1 ?>','<?php echo $param2;?>','<?php echo $param3 ?>','<?php echo $param4 ?>','<?php echo $param5 ?>','<?php echo $param6 ?>','<?php echo $param7 ?>','<?php echo $param8;?>','<?php echo $param9;?>')">
                <span class="glyphicon glyphicon-edit"> P</span>
            </button>
        <?php }?>


        <?php if($param3 != 2 && $param2 == 2){?>
            <button class="edit-modal btn btn-xs btn-info hidden" onclick="showMasterTableEditModel('<?php echo $param8;?>','<?php echo $param4 ?>','<?php echo $param9 ?>','<?php echo $param1?>')">
                <span class="glyphicon glyphicon-edit"> A</span>
            </button>
            <button class="delete-modal btn btn-xs btn-danger btn-xs hidden" onclick="deleteCompanyFinanceThreeTableRecords('<?php echo $param1 ?>','<?php echo $param2;?>','<?php echo $param3 ?>','<?php echo $param4 ?>','<?php echo $param5 ?>','<?php echo $param6 ?>','<?php echo $param7 ?>','<?php echo $tableOne; ?>','<?php echo $tableTwo;?>','transactions')">
                <span class="glyphicon glyphicon-trash"> A</span>
            </button>
        <?php }else if($param3 == 2 && $param2 == 2){?>
            <button class="delete-modal btn btn-xs btn-warning btn-xs hidden" onclick="repostCompanyFinanceThreeTableRecords('<?php echo $param1 ?>','<?php echo $param2;?>','<?php echo $param3 ?>','<?php echo $param4 ?>','<?php echo $param5 ?>','<?php echo $param6 ?>','<?php echo $param7 ?>','<?php echo $tableOne; ?>','<?php echo $tableTwo;?>','transactions')">
                <span class="glyphicon glyphicon-edit"> A</span>
            </button>
        <?php }?>
        <?php
    }


    public static function getCreditAccountHeadNameForInvoice($param1,$param2){
        CommonFacades::companyDatabaseConnection($param2);
        $accountName = DB::selectOne('select `name` from `accounts` where `id` = '.$param1.'')->name;
        CommonFacades::reconnectMasterDatabase();
        return $accountName;
    }

    public static function purchasePaymentVoucherSummaryDetail($param1,$param2,$param3){
        $result = \DB::table("pv_data")
            ->select("pv_data.pv_no","pv_data.amount","pv_data.acc_id","pv_data.debit_credit","pv_data.id","pvs.pv_no","pvs.grn_no","pvs.po_no")
            ->join('pvs','pv_data.pv_no','=','pvs.pv_no')
            ->where(['pvs.'.$param2.'' => $param3,'pvs.status' => '1','pvs.pv_status' => '2','pvs.pv_status' => '2','pv_data.debit_credit' => '0'])
            ->get();
        $data='';
        $data.='<div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="table-responsive"><table class="table table-bordered sf-table-list"><thead><th class="text-center">S.No</th><th class="text-center">PV No</th><th class="text-center">Account Head</th><th class="text-center col-sm-3">Amount</th></thead><tbody>';
        $counter = 1;
        $totalPaymentAmount = 0;
        foreach($result as $row){
            $totalPaymentAmount += $row->amount;
            $data .='<tr><td class="text-center">'.$counter++.'</td>';
            $data .='<td class="text-center">'.$row->pv_no.'</td>';
            $data .='<td class="text-center">'.static::getCreditAccountHeadNameForInvoice($row->acc_id,$param1).'</td>';
            $data .='<td class="text-right">'.$row->amount.'</td></tr>';
        }
        $data.='</tbody></table></div></div><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><input type="hidden" readonly name="totalPaymentAmount" id="totalPaymentAmount" value="'.$totalPaymentAmount.'" class="form-control" /></div></div>';
        return $data;
    }

    public static function monthWisePurchaseActivitySupplierWise($param1,$param2,$param3){
        CommonFacades::companyDatabaseConnection($param1);
        $supplierId = $param2;
        $monthStartDate = date(''.$param3.'-01');
        $monthEndDate   = date(''.$param3.'-t');
        $resultFara = DB::table('fara')
            ->select('grn_no','grn_date','main_ic_id','sub_ic_id','supp_id','qty','price','value','action')
            ->where('supp_id','=',$param2)
            ->whereBetween('date', array($monthStartDate, $monthEndDate))
            ->get();
        CommonFacades::reconnectMasterDatabase();
        ?>
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="table-responsive">
                    <table class="table table-bordered sf-table-list">
                        <thead>
                        <tr>
                            <th class="text-center">S.No</th>
                            <th class="text-center">Category Name</th>
                            <th class="text-center">Item Name</th>
                            <th class="text-center">GRN. No.</th>
                            <th class="text-center">GRN. Date</th>
                            <th class="text-center">Qty.</th>
                            <th class="text-center">Price</th>
                            <th class="text-center">Amount</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $counter = 1;
                        $totalPurchaseAmount = 0;
                        foreach($resultFara as $row){
                            $totalPurchaseAmount += $row->value;
                            ?>
                            <tr>
                                <td class="text-center"><?php echo $counter++;?></td>
                                <td class="text-center"><?php echo CommonFacades::getCompanyDatabaseTableValueById($param1,'category','main_ic',$row->main_ic_id);?></td>
                                <td class="text-center"><?php echo CommonFacades::getCompanyDatabaseTableValueById($param1,'subitem','sub_ic',$row->sub_ic_id);?></td>
                                <td class="text-center"><?php echo $row->grn_no;?></td>
                                <td class="text-center"><?php echo CommonFacades::changeDateFormat($row->grn_date);?></td>
                                <td class="text-center"><?php echo $row->qty;?></td>
                                <td class="text-center"><?php echo $row->value/$row->qty;?></td>
                                <td class="text-right"><?php echo $row->value;?></td>
                            </tr>
                            <?php
                        }
                        ?>
                        <tr>
                            <td colspan="7">Total Amount</td>
                            <td class="text-right"><?php echo $totalPurchaseAmount;?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php
    }

    public static function monthWisePaymentVoucherActivitySupplierWise($param1,$param2,$param3,$param4){
        CommonFacades::companyDatabaseConnection($param1);
        $supplierId = $param2;
        $monthStartDate = date(''.$param3.'-01');
        $monthEndDate   = date(''.$param3.'-t');
        $accId = $param4;
        $result = \DB::table("pv_data")
            ->select("pv_data.pv_no","pv_data.pv_date","pv_data.amount","pv_data.acc_id","pv_data.debit_credit","pv_data.id","pvs.pv_no","pvs.grn_no","pvs.cheque_no","pvs.cheque_date","pvs.voucherType")
            ->join('pvs','pv_data.pv_no','=','pvs.pv_no')
            ->where(['pv_data.acc_id' => $accId,'pvs.status' => '1','pvs.pv_status' => '2'])
            ->whereBetween('pv_data.pv_date', array($monthStartDate, $monthEndDate))
            ->get();
        CommonFacades::reconnectMasterDatabase();
        $data='';
        $data.='<div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="table-responsive"><table class="table table-bordered sf-table-list"><thead><th class="text-center">S.No</th><th class="text-center">PV No</th><th class="text-center">PV Date</th><th class="text-center">Cheque No</th><th class="text-center">Cheque Date</th><th class="text-center col-sm-3">Amount</th></thead><tbody>';
        $counter = 1;
        $totalPaymentAmount = 0;
        foreach($result as $row){
            $totalPaymentAmount += $row->amount;
            $data .='<tr><td class="text-center">'.$counter++.'</td>';
            $data .='<td class="text-center">'.$row->pv_no.'</td>';
            $data .='<td class="text-center">'.CommonFacades::changeDateFormat($row->pv_date).'</td>';
            if($row->voucherType == 4 || $row->voucherType == 2){
                $data .='<td class="text-center">'.$row->cheque_no.'</td>';
                $data .='<td class="text-center">'.CommonFacades::changeDateFormat($row->cheque_date).'</td>';
            }else{
                $data .='<td class="text-center">---</td>';
                $data .='<td class="text-center">---</td>';
            }
            $data .='<td class="text-right">'.$row->amount.'</td></tr>';
        }
        $data.='</tbody></table></div></div><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><input type="hidden" readonly name="totalPaymentAmount" id="totalPaymentAmount" value="'.$totalPaymentAmount.'" class="form-control" /></div></div>';
        return $data;
    }

    public static function getAllProductNameUsingProductionNoAndFGI($param1,$param2,$param3){
        CommonFacades::companyDatabaseConnection($param1);
        if(empty($param3)){
            $getAllProductNameUsingProductionNoAndFGI = DB::table('production_request_data')->select('id','finish_good_bulk_id','pack_size_id','batch_size_id','production_machine_id')->where('production_request_no','=',$param2)->groupBy('id')->get();
        }else{
            $getAllProductNameUsingProductionNoAndFGI = DB::table('production_request_data')->select('id','finish_good_bulk_id','pack_size_id','batch_size_id','production_machine_id')->where('production_request_no','=',$param2)->where('finish_good_bulk_id','=',$param3)->groupBy('id')->get();
        }
        $a = '';

        foreach ($getAllProductNameUsingProductionNoAndFGI as $row) {
            $paramOne = "pdc/viewRawMaterialDetailProductWise";
            $paramTwo = $param2.'<*>'.$row->id.'<*>'.$row->finish_good_bulk_id.'<*>'.$row->pack_size_id.'<*>'.$row->batch_size_id.'<*>'.$row->production_machine_id;
            $paramThree = "View Raw Material and Packing Material Detail Product Wise";
            CommonFacades::reconnectMasterDatabase();
            $packSize = CommonFacades::getMasterTableValueById($param1,'pack_size','pack_size_name',$row->pack_size_id);
            $batchSize = CommonFacades::getMasterTableValueById($param1,'batch_size','batch_size_name',$row->batch_size_id);
            $productName = CommonFacades::getCompanyDatabaseTableValueById($param1,'subitem','sub_ic',$row->finish_good_bulk_id);
            CommonFacades::companyDatabaseConnection($param1);
            $a .= '<a onclick="showDetailModelOneParamerter(\''.$paramOne.'\',\''.$paramTwo.'\',\''.$paramThree.'\')" class="btn btn-xs btn-success">Product Name => '.$productName.' => Batch Size No => '.$batchSize.' => '.'Pack Size No => '.$packSize.'</a><div style="line-height: 5px;">&nbsp;</div>';
        }
        CommonFacades::reconnectMasterDatabase();
        return $a;
    }
    public static function get_unique_no_transfer($year, $month)
    {

        $quotation_no = '';
        $variable = 100;
        sprintf("%'03d", $variable);
        $str = DB::Connection('tenant')->selectOne("select max(convert(substr(`tr_no`,7,length(substr(`tr_no`,3))-3),signed integer)) reg
        from `stock_transfers` where substr(`tr_no`,3,2) = " . $year . " and substr(`tr_no`,5,2) = " . $month . "")->reg;
        $str = $str + 1;
        $str = sprintf("%'03d", $str);
        return  $job_order_no = 'tr' . $year . $month . $str;


    }
    public static function get_unique_no_export($year, $month)
    {

        $quotation_no = '';
        $variable = 100;
        sprintf("%'03d", $variable);
        $str = DB::Connection('tenant')->table('export_units')
        ->select(DB::raw('RIGHT(MAX(CAST(SUBSTRING_INDEX(e_no, "exp", -1) AS UNSIGNED)), 3) AS max_value'))
        ->value('max_value');
        $str = $str + 1;
        $str = sprintf("%'03d", $str);
        return  $job_order_no = 'exp' . $year . $month . $str;
    }
    public static function get_unique_no_export_item($year, $month)
    {

        $quotation_no = '';
        $variable = 100;
        sprintf("%'03d", $variable);
        $str = DB::Connection('tenant')->table('export_items')
        ->select(DB::raw('RIGHT(MAX(CAST(SUBSTRING_INDEX(e_item_no, "eip", -1) AS UNSIGNED)), 3) AS max_value'))
        ->value('max_value');
        $str = $str + 1;
        $str = sprintf("%'03d", $str);
        return  $job_order_no = 'eip' . $year . $month . $str;
    }
    public static function get_unique_no_import($year, $month)
    {

        $quotation_no = '';
        $variable = 100;
        sprintf("%'03d", $variable);
        $str = DB::Connection('tenant')->table('import_units')
        ->select(DB::raw('RIGHT(MAX(CAST(SUBSTRING_INDEX(imp_no, "imp", -1) AS UNSIGNED)), 3) AS max_value'))
        ->value('max_value');
        $str = $str + 1;
        $str = sprintf("%'03d", $str);
        return  $job_order_no = 'imp' . $year . $month . $str;
    }
    public static function get_unique_no_expense_voucher($year, $month)
    {
        $str = DB::connection('tenant')->selectOne("select max(convert(substr(`ev_no`,3,length(substr(`ev_no`,3))-4),signed integer)) reg from `expense_claim_vouchers` where substr(`ev_no`,-4,2) = ".date('m')." and substr(`ev_no`,-2,2) = ".date('y')."")->reg;
		return $job_order_no = 'EV'.($str+1).date('my');
    }
    public static function get_unique_no_trash($year, $month)
    {

        $quotation_no = '';
        $variable = 100;
        sprintf("%'03d", $variable);
        $str = DB::Connection('tenant')->selectOne("select max(convert(substr(`tr_no`,8,length(substr(`tr_no`,3))-3),signed integer)) reg
        from `stock_trashes` where substr(`tr_no`,4,2) = " . $year . " and substr(`tr_no`,6,2) = " . $month . "")->reg;
        $str = $str + 1;
        // dd($str);
        $str = sprintf("%'03d", $str);
        return  $job_order_no = 'TRH' . $year . $month . $str;


    }
}
?>