<?php
use App\Models\Fara;
use App\Models\Category;
$m = CommonFacades::getSessionCompanyId();
$data ='';
//$paramOne = $_GET['paramOne'];
$paramTwo = $_GET['paramTwo'];
$categoryId = $_GET['categoryId'];
$subItemId = $_GET['subItemId'];
$filterDate = $paramTwo;
if($categoryId == 0){
    $categodyTitle = 'All Categories';
}else{
    $categodyTitle = CommonFacades::getCompanyDatabaseTableValueById($m,'category','main_ic',$categoryId);
}

if($subItemId == 0){
    $subItemTitle = 'All Sub Items';
}else{
    $subItemTitle = CommonFacades::getCompanyDatabaseTableValueById($m,'subitem','sub_ic',$subItemId);
}

CommonFacades::companyDatabaseConnection(CommonFacades::getSessionCompanyId());

if($paramTwo != '' and $subItemId != 0){
    $dateConditionTwo = "and fara.date <= '".$paramTwo."'";
    $dateCondition = "and `date` <= '".$paramTwo."'";
    $subItemConditionOne = "and `sub_ic_id` = ".$subItemId."";
    $subItemConditionTwo = "and subitem.id = ".$subItemId."";
    $mainItemCategoryId = "and `id` = ".$categoryId."";

}else if($paramTwo != ''){
    $dateConditionTwo = "and fara.date <= '".$paramTwo."'";
    $subItemConditionTwo = "";
    $subItemConditionOne = "";
    $dateCondition = "and `date` <= '".$paramTwo."'";
    $mainItemCategoryId = "";

}else if($subItemNameId != 0){
    $dateConditionTwo = "";
    $subItemConditionTwo = "and subitem.id = ".$subItemId."";
    $subItemConditionOne = "and `sub_ic_id` = ".$subItemId."";
    $dateCondition = "";
    $mainQueryForStock = "";
    $mainItemCategoryId = "and `id` = ".$categoryId."";

}else{
    $dateConditionTwo = "";
    $subItemConditionTwo = "";
    $subItemConditionOne = "";
    $dateCondition = "";
    $mainQueryForStock = "";
    $mainItemCategoryId = "";

}
$Categorys = DB::select("select `id`,`main_ic` from `category` where `status` = '1' ".$mainItemCategoryId."");
CommonFacades::reconnectMasterDatabase();
$counterOne = 1;
$data.='<div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center" style="font-size: 15px !important; font-style: inherit; font-family: -webkit-body; font-weight: bold;">Filter By : (Category Name => '.$categodyTitle.')&nbsp;&nbsp;,&nbsp;&nbsp;(Item Name => '.$subItemTitle.')&nbsp;&nbsp;,&nbsp;&nbsp;(As On Date => '.CommonFacades::changeDateFormat($filterDate).')</div></div>';
foreach ($Categorys as $row1){
    $data.='<div class="panel"><div class="panel panel-body"><div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><strong>'.$row1->main_ic.'</strong></div><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';
    CommonFacades::companyDatabaseConnection(CommonFacades::getSessionCompanyId());
    $subinfo = DB::select("SELECT subitem.id,subitem.sub_ic,subitem.main_ic_id,
		sum(fara.qty) as qty,sum(fara.value) as value,fara.supp_id,
		round(sum(fara.value) / sum(fara.qty),2) as cpu
		FROM subitem
		INNER JOIN fara
		ON subitem.id=fara.sub_ic_id
		where subitem.main_ic_id = ".$row1->id." ".$subItemConditionTwo."
		AND fara.status = '1' ".$dateConditionTwo." AND fara.action = 1 group by sub_ic ORDER BY sub_ic");
    CommonFacades::reconnectMasterDatabase();
    $data.='<div class="table-responsive"><table class="table table-bordered sf-table-list"><thead><th class="text-center">S.No</th><th class="text-center">Sub Item Name</th><th class="text-center">Opening Qty.</th><th class="text-center">Purchase Qty</th><th class="text-center">Total Qty.</th><th class="text-center">Issue Qty</th><th class="text-center">Cash Sale Qty</th><th class="text-center">Credit Sale Qty</th><th class="text-center">Return Qty.</th><th class="text-center">Current Balance.</th><th class="text-center hidden-print">Action</th></thead><tbody>';
    $counterTwo = 1;
    foreach ($subinfo as $row2){
        $paramOne = "stdc/viewStockInventorySummaryDetail";
        $paramTwo = "View Item Wise Summary Detail";
        $paramThree = $row2->main_ic_id;
        $paramFour = $row2->id;
        $paramFive = $row2->sub_ic;
        $totalPurchaseQty = CommonFacades::getAllPurchaseQtyItemWise($m,$row1->id,$row2->id,$paramTwo,$filterDate);
        $overAllTotalQty = $totalPurchaseQty + $row2->qty;
        $data.='<tr><td class="text-center">'.$counterTwo++.'</td><td>'.$row2->sub_ic.'</td><td class="text-center">'.$row2->qty.'</td><td class="text-center">'.$totalPurchaseQty.'</td><td class="text-center">'.$overAllTotalQty.'</td><td class="text-center">'.CommonFacades::getAllIssueQtyItemWise($m,$row1->id,$row2->id,$paramTwo,$filterDate).'</td><td class="text-center">'.CommonFacades::getAllCashSaleQtyItemWise($m,$row1->id,$row2->id,$paramTwo,$filterDate).'</td><td class="text-center">'.CommonFacades::getAllCreditSaleQtyItemWise($m,$row1->id,$row2->id,$paramTwo,$filterDate).'</td><td class="text-center">'.CommonFacades::getAllStoreChallanReturQtyItemWise($m,$row1->id,$row2->id,$paramTwo,$filterDate).'</td><td class="text-center">'.StoreFacades::checkItemWiseCurrentBalanceQty($m,$row1->id,$row2->id,$paramTwo,$filterDate).'</td><td class="text-center hidden-print">';
        $data.='<a onclick="showDetailModelSixParamerter(\''.$paramOne.'\',\''.$paramTwo.'\',\''.$paramThree.'\',\''.$paramFour.'\',\''.$paramFive.'\',\''.$filterDate.'\')" class="btn btn-xs btn-success"><span class="glyphicon glyphicon-eye-open"></span></a>';
        $data.='</td></tr>';
    }
    $data.='</tbody></table></div>';
    $data.='</div></div></div></div>';
}
echo json_encode(array('data' => $data));
?>