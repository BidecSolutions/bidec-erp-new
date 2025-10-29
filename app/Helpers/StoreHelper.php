<?php
namespace App\Helpers;
use DB;
use Config;
use CommonFacades;
use App\Models\Category;
use App\Models\Subitem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;

class StoreHelper
{
    public static function homePageURL()
    {
        return url('/');
    }

    public static function calculateExpenseAmountItemWise($id,$poNo){
        $queryOne = DB::table('expense_claim_voucher_datas AS ecvd')
            ->select('ecv.id', 'ecv.ev_no', DB::raw('SUM(ecvd.expense_amount) AS total_amount'))
            ->join('expense_claim_vouchers AS ecv', 'ecv.id', '=', 'ecvd.master_id')
            ->where('ecv.po_id',$id)
            ->groupBy('ecvd.master_id')->first();
        $queryTwo = DB::table('purchase_order_data AS pod')
            ->select('po.id', 'po.purchase_order_no', DB::raw('COUNT(pod.id) as totalRecord'))
            ->join('purchase_order AS po', 'po.purchase_order_no', '=', 'pod.purchase_order_no')
            ->where('po.purchase_order_no',$poNo)
            ->groupBy('pod.purchase_order_no')->first();
        if(empty($queryOne)){
            return 0;
        }
        return $expenseAmount = $queryOne->total_amount / $queryTwo->totalRecord;
        
    }

    public static function displayMaterialRequestVoucherListButton($param1,$param2,$param3,$param4,$param5,$param6,$param7,$param8,$param9,$param10,$param11, $param12, $issued, $total){
        $tableOne = $param8;
        $tableTwo = $param9;
        if($param3 == 1){
            $dropdownList = '';

            // dd($sc_status, 'dadsa', $param2);
            if ( $issued == $total && singlePermission(getSessionCompanyId(), Auth::user()->id, Input::get('parentCode'), 'right_edit', Auth::user()->acc_type)) {                
                $dropdownList .= '<li><a onclick="showMasterTableEditModel(\''.$param10.'\',\''.$param4.'\',\''.$param11.'\',\''.$param1.'\')"><span class="glyphicon glyphicon-edit"></span> Edit</a></li>';
            }
            if ($param2 != 2 && singlePermission(getSessionCompanyId(), Auth::user()->id, Input::get('parentCode'), 'right_delete', Auth::user()->acc_type)) {
                $dropdownList .= '<li><a onclick="deleteCompanyMaterialTwoTableRecords(\''.$param1.'\',\''.$param2.'\',\''.$param3.'\',\''.$param4.'\',\''.$param5.'\',\''.$param6.'\',\''.$param7.'\',\''.$param8.'\',\''.$param9.'\')"><span class="glyphicon glyphicon-trash"></span> Delete</a></li>';                
            }
            return $dropdownList; 
        }else if($param3 == 2 && $param2 != 2){
            return '<li><a onclick="repostCompanyMaterialTwoTableRecords(\''.$param1.'\',\''.$param2.'\',\''.$param3.'\',\''.$param4.'\',\''.$param5.'\',\''.$param6.'\',\''.$param7.'\',\''.$param8.'\',\''.$param9.'\')"><span class="glyphicon glyphicon-edit"></span> Restore</a></li>';
        }
    }

    public static function reversePurchaseOrderDetailBeforeApproval($param1,$param2,$param3){
		$checkPurchaseOrderStatus = DB::table('purchase_order')->where('purchase_order_no','=',$param2)->first();
		$a = '';
		if($checkPurchaseOrderStatus->purchase_order_status == '1'){
            
            if(singlePermission(getSessionCompanyId(), Auth::user()->id, Input::get('parentCode'), 'right_delete', Auth::user()->acc_type)){ 

			    $a .= '<a class="btn btn-xs btn-danger" onclick="reversePurchaseOrderDetailBeforeApproval(\''.$param1.'\',\''.$param2.'\',\''.$param3.'\')">Delete Purchase Order Detail Before Approval</a>';
            }
            // else{
			//     // $a .= '<a disabled class="btn btn-xs btn-danger">Delete Purchase Order Detail Before Approval</a>';

            // }

		}else{
			$a = '';
		}
		return $a;
    }
    
    public static function displayItemWiseLastPurchaseRate($param1,$param2,$param3){
		$locationList = DB::table('location')->where('status','=','1')->get();
		$a = '<div class="row">';
        $b = 0;
        foreach($locationList as $row){
            $b += 2;
            $displayItemWiseLastPurchaseRate = DB::table('grn_data')
                ->select('grn_data.po_date','goods_receipt_note.supplier_id','goods_receipt_note.supplier_location_id','grn_data.grn_date','grn_data.rate')
                ->join('goods_receipt_note', 'grn_data.grn_no', '=', 'goods_receipt_note.grn_no')
                ->where('grn_data.category_id', '=', $param2)
				->where('grn_data.sub_item_id', '=', $param3)
				->where('grn_data.location_id', '=', $row->id)
                ->where('grn_data.status', '=', '1')
				->orderBy('grn_data.id', 'desc')
				->first();
            
            if(empty($displayItemWiseLastPurchaseRate)){
                // $a .= '<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">Last Purchase Not Available</div>';
			}else{
				$a .= '<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">Location Name : '.$row->location_name.' <br /> Purchase Order Date : '.CommonHelper::changeDateFormat($displayItemWiseLastPurchaseRate->po_date).' <br /> Supplier Name : '.CommonHelper::getCompanyDatabaseTableValueById($param1,'supplier','name',$displayItemWiseLastPurchaseRate->supplier_id).'  <br /> Unit Price : '.$displayItemWiseLastPurchaseRate->rate.' </div>';
            }
            if($b == 12){
                $b = 0;
                $a .= '</div><div class="row">';
            }
		}
		$a .= '</div>';
		
		return $a;
	}

    public static function getPurchaseOrderDataDetailAgainstPurchaseOrderNo($m,$purchaseRequestNo,$purchaseOrderNo,$locationId){
		$getPurchaseRequestDataDetail = DB::table('purchase_request_data')
		    ->select('category.main_ic','subitem.sub_ic','purchase_request_data.qty','purchase_request_data.id')
			->join('category', 'purchase_request_data.category_id', '=', 'category.id')
            ->join('subitem', 'purchase_request_data.sub_item_id', '=', 'subitem.id')
            ->join('purchase_order_data', 'purchase_request_data.id', '=', 'purchase_order_data.purchase_request_data_record_id')
            ->where('purchase_request_data.purchase_request_no','=',$purchaseRequestNo)
            ->where('purchase_order_data.purchase_order_no','=',$purchaseOrderNo)
			->where('purchase_request_data.purchase_request_status','=','2')
			->get();
		$data = '<table class="table table-bordered customTable"><thead><tr><th class="text-center">Category Name</th><th class="text-center">Item Name</th><th class="text-center">P.R. Qty.</th><th class="text-center">P.O. Detail</th></tr></thead><tbody>';
		foreach($getPurchaseRequestDataDetail as $row){
			$getPurchaseOrderDataDetail = DB::table('purchase_order_data')
				->select('purchase_order_data.location_id','purchase_order_data.purchase_order_qty','purchase_order_data.unit_price','purchase_order_data.privious_unit_price','purchase_order_data.sub_total','purchase_order_data.purchase_order_status','purchase_order_data.grn_status')
				->where('purchase_order_data.purchase_request_data_record_id','=',$row->id)
				->get();
			$data.='<tr><td>'.$row->main_ic.'</td><td>'.$row->sub_ic.'</td><td class="text-center">'.$row->qty.'</td><td><table class="table table-bordered table-striped table-sm sf-table-list"><thead><tr><th class="text-center">Unit Price</th><th class="text-center">P.O.Qty.</th><th class="text-center">Sub Total Amount</th></tr></thead><tbody>';
			foreach($getPurchaseOrderDataDetail as $row1){
				$data.='<tr><td class="text-right">'.number_format($row1->unit_price).'</td><td class="text-center">'.$row1->purchase_order_qty.'</td><td class="text-right">'.number_format($row1->sub_total).'</td></tr>';
			}
			$data.='</tbody></table></td></tr>';
		}
		$data.='</tbody></table>';
		return $data;
    }
    
    public static function reversePurchaseOrderDetailAfterApproval($param1,$param2,$param3){
        $countGRNDataDetail = DB::table('grn_data')->where('po_no','=',$param2)->get();
        $countPVsDetail = DB::table('pvs')->where('po_no','=',$param2)->get();
        $countMultiPOPVsDetail = DB::table('pvs_multiple_po_grn_datas')->where('po_grn_no','=',$param2)->get();
		$a = '';
		if(count($countGRNDataDetail) == '0' && count($countPVsDetail) == '0' && count($countMultiPOPVsDetail) == 0){
            if(singlePermission(getSessionCompanyId(), Auth::user()->id, Input::get('parentCode'), 'right_reverse', Auth::user()->acc_type)){
			    $a .= '<a class="btn btn-xs btn-danger" onclick="reversePurchaseOrderDetailAfterApproval(\''.$param1.'\',\''.$param2.'\',\''.$param3.'\')">Reverse Purchase Order Detail After Approval</a>';                
            }else{
			    $a .= '<a disabled class="btn btn-xs btn-danger">Reverse Purchase Order Detail After Approval</a>';
            }
		}else{
			$a = '';
		}
		return $a;
    }
    
    public static function addUserActivityLog($param1,$param2,$param3,$param4,$param5,$param6,$param7,$param8,$param9){
		
		$data1['activity_type'] = $param3;
		$data1['option_name'] = $param2;
		$data1['description'] = $param9;
		$data1['user_name'] = $param5;
		$data1['user_id'] = $param6;
		$data1['activity'] = $param4;
		$data1['date'] = $param8;
		$data1['time'] = $param7;
		DB::table($param1)->insert($data1);
	}

    public static function makeSubTotalAmountPurchaseOrder($param1,$param2){
        CommonFacades::companyDatabaseConnection($param1);
            $makeSubTotalAmountPurchaseOrder = DB::table('purchase_order_data')
                ->where('purchase_order_no', '=', $param2)
                ->sum('sub_total');
        CommonFacades::reconnectMasterDatabase();
        return $makeSubTotalAmountPurchaseOrder;
    }

    public static function makeSubTotalWithPercentageAmountPurchaseOrder($param1,$param2){
        CommonFacades::companyDatabaseConnection($param1);
        $makeSubTotalAmountPurchaseOrder = DB::table('purchase_order_data')
            ->where('purchase_order_no', '=', $param2)
            ->sum('sub_total');

        $makePercentAmountPurchaseOrder = DB::table('purchase_order_data')
            ->where('purchase_order_no', '=', $param2)
            ->sum('sub_total_with_persent');
        CommonFacades::reconnectMasterDatabase();

        return $makeSubTotalAmountPurchaseOrder + $makePercentAmountPurchaseOrder;
    }


    public static function checkVoucherStatus($param1,$param2)
    {
        if ($param1 == 1 && $param2 == 1) {
            return 'Pending';
        } else if ($param2 == 2) {
            return 'Deleted';
        } else if ($param1 == 2 && $param2 == 1) {
            return 'Approve';
        }
    }

    public static function priviousPurchaseOrderQtyThisPurchaseRequest($param1,$param2,$param3,$param4,$param5){
        CommonFacades::companyDatabaseConnection($param1);
            if($param5 == 0) {

                $priviousSendPurchaseOrderQty = DB::table("purchase_order_data")
                    ->select(DB::raw("SUM(purchase_order_qty) as purchase_order_qty"))
                    ->where(['category_id' => $param3, 'sub_item_id' => $param4, 'status' => '1'])
                    ->where('purchase_request_no' ,'=', $param2)
                    ->groupBy(DB::raw("sub_item_id"))
                    ->get();
            }else{
                $priviousSendPurchaseOrderQty = DB::table("purchase_order_data")
                    ->select(DB::raw("SUM(purchase_order_qty) as purchase_order_qty"))
                    ->where(['category_id' => $param3, 'sub_item_id' => $param4, 'status' => '1'])
                    ->where('purchase_request_no' ,'=', $param2)
                    ->where('id' ,'!=', $param5)
                    ->groupBy(DB::raw("sub_item_id"))
                    ->get();
            }
        CommonFacades::reconnectMasterDatabase();
        $totalPriviousSendPurchaseOrderQty = 0;
        foreach ($priviousSendPurchaseOrderQty as $row){
            $totalPriviousSendPurchaseOrderQty += $row->purchase_order_qty;
        }
        return $totalPriviousSendPurchaseOrderQty;

    }

    public static function getTotalPurchaseRequestQtyItemWise($param1,$param2,$param3,$param4){
        CommonFacades::companyDatabaseConnection($param1);
        $purchaseRequestQty = DB::table("purchase_request_data")
            ->select(DB::raw("SUM(qty) as qty"))
            ->where(['category_id' => $param3,'sub_item_id' => $param4,'status' => '1'])
            ->groupBy(DB::raw("sub_item_id"))
            ->get();
        CommonFacades::reconnectMasterDatabase();
        $totalPurchaseRequestQty = 0;
        foreach ($purchaseRequestQty as $row){
            $totalPurchaseRequestQty += $row->qty;
        }
        return $totalPurchaseRequestQty;
    }



    public static function checkItemWiseCurrentBalanceQty($param1,$param2,$param3,$param4,$param5){
        //return $param1.'----'.$param2.'----'.$param3.'<br />';
        CommonFacades::companyDatabaseConnection($param1);
        $openingBalance = DB::selectOne('select `qty` from `fara` where `action` = 1 and `status` = 1 and `main_ic_id` = '.$param2.' and `sub_ic_id` = '.$param3.' ')->qty;
        $purchaseBalance = '';
        //$sendBalance = DB::selectOne('select `qty` from `fara` where `action` = 2 and `status` = 1 and `main_ic_id` = '.$param2.' and `sub_ic_id` = '.$param3.' ');
        $sendBalance = DB::table("fara")
            ->select(DB::raw("SUM(qty) as qty"))
            ->where(['main_ic_id' => $param2,'sub_ic_id' => $param3,'action' => '2'])
            ->where('date','<=',$param5)
            ->groupBy(DB::raw("sub_ic_id"))
            ->get();
        $returnBalance = DB::table("fara")
            ->select(DB::raw("SUM(qty) as qty"))
            ->where(['main_ic_id' => $param2,'sub_ic_id' => $param3,'action' => '4'])
            ->where('date','<=',$param5)
            ->groupBy(DB::raw("sub_ic_id"))
            ->get();
        $purchaseBalance = DB::table("fara")
            ->select(DB::raw("SUM(qty) as qty"))
            ->where(['main_ic_id' => $param2,'sub_ic_id' => $param3])
			->whereIn('action',array(3,8))
            ->where('date','<=',$param5)
            ->groupBy(DB::raw("sub_ic_id"))
            ->get();

        $cashSaleBalance = DB::table("fara")
            ->select(DB::raw("SUM(qty) as qty"))
            ->where(['main_ic_id' => $param2,'sub_ic_id' => $param3,'action' => '5'])
            ->where('date','<=',$param5)
            ->groupBy(DB::raw("sub_ic_id"))
            ->get();

        $creditSaleBalance = DB::table("fara")
            ->select(DB::raw("SUM(qty) as qty"))
            ->where(['main_ic_id' => $param2,'sub_ic_id' => $param3,'action' => '6'])
            ->where('date','<=',$param5)
            ->groupBy(DB::raw("sub_ic_id"))
            ->get();
        CommonFacades::reconnectMasterDatabase();
        $totalSendBalance = 0;
        foreach ($sendBalance as $row){
            $totalSendBalance += $row->qty;
        }
        $totalReturnBalance = 0;
        foreach ($returnBalance as $row){
            $totalReturnBalance += $row->qty;
        }
        $totalPurchaseBalance = 0;
        foreach ($purchaseBalance as $row){
            $totalPurchaseBalance += $row->qty;
        }

        $totalCashSaleBalance = 0;
        foreach ($cashSaleBalance as $row){
            $totalCashSaleBalance += $row->qty;
        }

        $totalCreditSaleBalance = 0;
        foreach ($creditSaleBalance as $row){
            $totalCreditSaleBalance += $row->qty;
        }
        $currentBalanceInStore = $openingBalance + $totalPurchaseBalance + $totalReturnBalance - $totalSendBalance  - $totalCashSaleBalance  - $totalCreditSaleBalance;

        return $currentBalanceInStore;
    }

    public static function issueQtyItemWiseDetail($param1,$param2,$param3,$param4,$param5){
        CommonFacades::companyDatabaseConnection($param1);
        $data = DB::table("store_challan_data")
            ->select(DB::raw("SUM(issue_qty) as issue_qty"))
            ->where(['category_id' => $param2,'sub_item_id' => $param3])
            ->where([$param5 => $param4])
            ->groupBy(DB::raw("sub_item_id"))
            ->get();
        CommonFacades::reconnectMasterDatabase();
        $totalQty = 0;
        foreach ($data as $row){
            $totalQty += $row->issue_qty;
        }
        return $totalQty;
    }

    public static function demandAndRemainingQtyItemWise($param1,$param2,$param3,$param4,$param5){
        static::issueQtyItemWiseDetail($param1,$param2,$param3,$param4,$param5);
        return 'Demand and Remaining Qty Item Wise';
    }

    public static function displayApproveDeleteRepostButtonPurchaseOrder($param1,$param2,$param3,$param4,$param5,$param6,$param7,$param8,$param9){
        if($param3 == 1 && $param2 == 1){
    ?>
            <?php
            if(singlePermission(getSessionCompanyId(), Auth::user()->id, Input::get('parentCode'), 'right_approve', Auth::user()->acc_type)){ 
            ?>
            <button class="delete-modal btn btn-xs btn-primary btn-xs" data-dismiss="modal" aria-hidden="true" onclick="approvePurchaseOrder('<?php echo $param1 ?>','<?php echo $param2;?>','<?php echo $param3 ?>','<?php echo $param4 ?>','<?php echo $param5 ?>','<?php echo $param6 ?>','<?php echo $param7 ?>','<?php echo $param8;?>','<?php echo $param9;?>')">
                <span class="glyphicon glyphicon-ok"></span> Approve Voucher
            </button>
            <?php } else{ 
                ?>
            <button disabled class="delete-modal btn btn-xs btn-primary btn-xs">
                <span class="glyphicon glyphicon-ok"></span> Approve Voucher
            </button>
            <?php } 
                ?>

    <?php
        }else if($param3 == 2 && $param2 == 1){
    ?>
            <?php
            if(singlePermission(getSessionCompanyId(), Auth::user()->id, Input::get('parentCode'), 'right_repost', Auth::user()->acc_type)){ 
            ?>
            <button class="delete-modal btn btn-xs btn-warning btn-xs" data-dismiss="modal" aria-hidden="true" onclick="repostCompanyStoreTwoTableRecords('<?php echo $param1 ?>','<?php echo $param2;?>','<?php echo $param3 ?>','<?php echo $param4 ?>','<?php echo $param5 ?>','<?php echo $param6 ?>','<?php echo $param7 ?>','<?php echo $param8;?>','<?php echo $param9;?>')">
                <span class="glyphicon glyphicon-edit"></span> Repost Voucher
            </button>
            <?php } else{ 
                ?>
            <button disabled class="delete-modal btn btn-xs btn-warning btn-xs">
                <span class="glyphicon glyphicon-edit"></span> Repost Voucher
            </button>
            <?php } 
                ?>
    <?php
        }
    }

    public static function displayApproveDeleteRepostButtonPurchaseRequestSale($param1,$param2,$param3,$param4,$param5,$param6,$param7,$param8,$param9){
        if($param3 == 1 && $param2 == 1){
            ?>
            <button class="delete-modal btn btn-xs btn-primary btn-xs" data-dismiss="modal" aria-hidden="true" onclick="approvePurchaseRequestSale('<?php echo $param1 ?>','<?php echo $param2;?>','<?php echo $param3 ?>','<?php echo $param4 ?>','<?php echo $param5 ?>','<?php echo $param6 ?>','<?php echo $param7 ?>','<?php echo $param8;?>','<?php echo $param9;?>')">
                <span class="glyphicon glyphicon-ok"></span> Approve Voucher
            </button>

            <?php /*?><button class="delete-modal btn btn-xs btn-danger btn-xs" data-dismiss="modal" aria-hidden="true" onclick="deleteCompanyPurchaseTwoTableRecords('<?php echo $param1 ?>','<?php echo $param2;?>','<?php echo $param3 ?>','<?php echo $param4 ?>','<?php echo $param5 ?>','<?php echo $param6 ?>','<?php echo $param7 ?>','<?php echo $param8;?>','<?php echo $param9;?>')">
                <span class="glyphicon glyphicon-trash"></span> Delete Voucher
            </button><?php */?>
            <?php
        }else if($param3 == 2 && $param2 == 1){
            ?>
            <button class="delete-modal btn btn-xs btn-warning btn-xs" data-dismiss="modal" aria-hidden="true" onclick="repostCompanyPurchaseTwoTableRecords('<?php echo $param1 ?>','<?php echo $param2;?>','<?php echo $param3 ?>','<?php echo $param4 ?>','<?php echo $param5 ?>','<?php echo $param6 ?>','<?php echo $param7 ?>','<?php echo $param8;?>','<?php echo $param9;?>')">
                <span class="glyphicon glyphicon-edit"></span> Repost Voucher
            </button>
            <?php
        }
    }

    public static function displayApproveDeleteRepostButtonTwoTable($param1,$param2,$param3,$param4,$param5,$param6,$param7,$param8,$param9){
        $param1.' - '.$param2.' - '.$param3.' - '.$param4.' - '.$param5.' - '.$param6.' - '.$param7.' - '.$param8.' - '.$param9;
        if($param3 == 1 && $param2 == 1){
            ?>
            <button class="delete-modal btn btn-xs btn-primary btn-xs" data-dismiss="modal" aria-hidden="true" onclick="approveCompanyPurchaseTwoTableRecords('<?php echo $param1 ?>','<?php echo $param2;?>','<?php echo $param3 ?>','<?php echo $param4 ?>','<?php echo $param5 ?>','<?php echo $param6 ?>','<?php echo $param7 ?>','<?php echo $param8;?>','<?php echo $param9;?>')">
                <span class="glyphicon glyphicon-ok"></span> Approve Voucher
            </button>

            <button class="delete-modal btn btn-xs btn-danger btn-xs" data-dismiss="modal" aria-hidden="true" onclick="deleteCompanyPurchaseTwoTableRecords('<?php echo $param1 ?>','<?php echo $param2;?>','<?php echo $param3 ?>','<?php echo $param4 ?>','<?php echo $param5 ?>','<?php echo $param6 ?>','<?php echo $param7 ?>','<?php echo $param8;?>','<?php echo $param9;?>')">
                <span class="glyphicon glyphicon-trash"></span> Delete Voucher
            </button>
            <?php
        }else if($param3 == 2 && $param2 == 1){
            ?>
            <button class="delete-modal btn btn-xs btn-warning btn-xs" data-dismiss="modal" aria-hidden="true" onclick="repostCompanyPurchaseTwoTableRecords('<?php echo $param1 ?>','<?php echo $param2;?>','<?php echo $param3 ?>','<?php echo $param4 ?>','<?php echo $param5 ?>','<?php echo $param6 ?>','<?php echo $param7 ?>','<?php echo $param8;?>','<?php echo $param9;?>')">
                <span class="glyphicon glyphicon-edit"></span> Repost Voucher
            </button>
            <?php
        }
    }

    public static function displayStoreChallanButton($param1,$param2,$param3,$param4,$param5){
        if($param4 == '1'){
            $paramOne = "store/editStoreChallanVoucherForm";
            $paramTwo = $param5;
            $paramThree = "Store Challan Voucher Edit Detail Form";
            $paramFour = $param1;
            return '<button class="edit-modal btn btn-xs btn-info" onclick="showMasterTableEditModel(\''.$paramOne.'\',\''.$paramTwo.'\',\''.$paramThree.'\',\''.$paramFour.'\')"><span class="glyphicon glyphicon-edit"> P</span></button>';
        }
    }

    public static function displayPurchaseOrderButton($param1,$param2,$param3,$param4,$param5){
        if($param3 == 1 && $param2 == 1) {
            $paramOned = "store/editPurchaseOrderVoucherForm";
            $paramTwod = $param5;
            $paramThreed = "Purchase Order Voucher Edit Detail Form";
            $paramFourd = $param1;
            return '<button class="edit-modal btn btn-xs btn-info" onclick="showMasterTableEditModel(\'' . $paramOned . '\',\'' . $paramTwod . '\',\'' . $paramThreed . '\',\'' . $paramFourd . '\')"><span class="glyphicon glyphicon-edit"> P</span></button>';
        }else{

        }
    }

    public static function displayStoreChallanReturnButton($param1,$param2,$param3,$param4,$param5,$param6,$param7,$param8,$param9,$param10,$param11,$param12,$param13)
    {
        if ($param3 == 1) {
            return '<button class="edit-modal btn btn-xs btn-info" onclick="showMasterTableEditModel(\'' . $param6 . '\',\'' . $param4 . '\',\'' . $param7 . '\',\'' . $param1 . '\')"><span class="glyphicon glyphicon-edit"> P</span></button> <button class="delete-modal btn btn-xs btn-danger btn-xs" onclick="deleteCompanyStoreThreeTableRecords(\'' . $param1 . '\',\'' . $param2 . '\',\'' . $param3 . '\',\'' . $param4 . '\',\'' . $param5 . '\',\'' . $param8 . '\',\'' . $param9 . '\',\'' . $param10 . '\',\'' . $param11 . '\',\'' . $param12 . '\')"><span class="glyphicon glyphicon-trash"> A</span></button>';
        }else{
            return '<button class="delete-modal btn btn-xs btn-warning btn-xs" onclick="repostCompanyStoreThreeTableRecords(\'' . $param1 . '\',\'' . $param2 . '\',\'' . $param3 . '\',\'' . $param4 . '\',\'' . $param5 . '\',\'' . $param8 . '\',\'' . $param9 . '\',\'' . $param10 . '\',\'' . $param11 . '\',\'' . $param12 . '\',\'' . $param13 . '\')"><span class="glyphicon glyphicon-edit"> P</span></button>';
        }
    }

    public static function getDemandQtyByDemandNo($param1,$param2,$param3,$param4,$param5){
        CommonFacades::companyDatabaseConnection($param1);
        $data = DB::table("demand_data")
            ->select(DB::raw("SUM(qty) as qty"))
            ->where(['category_id' => $param2,'sub_item_id' => $param3])
            ->where([$param5 => $param4])
            ->groupBy(DB::raw("sub_item_id"))
            ->get();
        CommonFacades::reconnectMasterDatabase();
        $totalQty = 0;
        foreach ($data as $row){
            $totalQty += $row->qty;
        }
        return $totalQty;
    }

    public static function getReturnQtyByStoreChallanNo($param1,$param2,$param3,$param4,$param5){
        CommonFacades::companyDatabaseConnection($param1);
        $data = DB::table("store_challan_return_data")
            ->select(DB::raw("SUM(return_qty) as return_qty"))
            ->where(['category_id' => $param2,'sub_item_id' => $param3])
            ->where([$param5 => $param4])
            ->where('status','=',1)
            ->groupBy(DB::raw("sub_item_id"))
            ->get();
        CommonFacades::reconnectMasterDatabase();
        $totalQty = 0;
        foreach ($data as $row){
            $totalQty += $row->return_qty;
        }
        return $totalQty;
    }

    public static function getReturnQtyByDemandNo($param1,$param2,$param3,$param4,$param5){
        CommonFacades::companyDatabaseConnection($param1);
        $data = DB::table("store_challan_data")
            ->select(DB::raw("store_challan_no"))
            ->where(['category_id' => $param2,'sub_item_id' => $param3])
            ->where([$param5 => $param4])
            ->where('status','=',1)
            ->get();
        $totalQty = 0;
        foreach ($data as $row){
            $dataOne = DB::table("store_challan_return_data")
                ->select(DB::raw("SUM(return_qty) as return_qty"))
                ->where(['category_id' => $param2,'sub_item_id' => $param3])
                ->where(['store_challan_no' => $row->store_challan_no])
                ->where('status','=',1)
                ->groupBy(DB::raw("sub_item_id"))
                ->get();
            foreach ($dataOne as $rowOne){
                $totalQty += $rowOne->return_qty;
            }
        }
        CommonFacades::reconnectMasterDatabase();


        return $totalQty;
    }

    public static function itemWiseCreatedStoreChallan($param1,$param2,$param3,$param4,$param5){
        CommonFacades::companyDatabaseConnection($param1);
            $data = DB::table('store_challan_data')
                ->select('store_challan_no','store_challan_date','issue_qty')
                ->where(['category_id' => $param3,'sub_item_id' => $param4,'demand_no' => $param2])
                ->where('store_challan_no','!=',$param5)
                ->get();
        CommonFacades::reconnectMasterDatabase();
        foreach ($data as $row){
            echo $row->store_challan_no.'  ---  ';
            echo CommonFacades::changeDateFormat($row->store_challan_date).'  ---  ';
            echo $row->issue_qty;
            echo '<br />';
        }
        if($data->isEmpty()){
            echo 'Not Found!';
        }
    }
}
?>