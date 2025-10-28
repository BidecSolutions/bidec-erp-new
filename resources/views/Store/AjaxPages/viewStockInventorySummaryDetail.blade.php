<?php
$m = CommonFacades::getSessionCompanyId();
$currentDate = date('Y-m-d');
$categoryIcId = $_GET['pOne'];
$subIcId = $_GET['pTwo'];
$filterDate = $_GET['pFour'];
$subIcName = $_GET['pThree'];
?>

<div class="row">
    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
        <label>To Date</label>
        <input type="Date" name="paramTwo" id="paramTwo" max="<?php echo $currentDate;?>"
               onchange="showDetailModelSixParamerter('stdc/viewStockInventorySummaryDetail','View Item Wise Summary Detail','<?php echo $categoryIcId?>','<?php echo $subIcId;?>','<?php echo $subIcName;?>',this.value)" value="<?php echo $filterDate;?>" class="form-control" />
    </div>
    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 text-right">
        <?php echo CommonFacades::displayPrintButtonInBlade('printViewStockInventorySummaryDetail','margin-top: 32px','1');?>
    </div>
</div>
<div style="line-height:5px;">&nbsp;</div>
<div id="viewFilterDateWiseStockInventoryDetail">
    <div class="well" id="printViewStockInventorySummaryDetail">
        <div class="row">
            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-4">
                <label style="border-bottom:2px solid #000 !important;">Printed On Date&nbsp;:&nbsp;</label><label style="border-bottom:2px solid #000 !important;"><?php echo CommonFacades::changeDateFormat($currentDate);?></label>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-5">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center"
                         style="font-size: 30px !important; font-style: inherit;
    								font-family: -webkit-body; font-weight: bold;">
                        <?php echo CommonFacades::getCompanyName($m);?>
                    </div>
                    <br />
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center"
                         style="font-size: 15px !important; font-style: inherit;
    								font-family: -webkit-body; font-weight: bold;">

                        <?php echo 'Filter By : (Item Name => '.$subIcName.')&nbsp;&nbsp;,&nbsp;&nbsp;(As On Date => '.CommonFacades::changeDateFormat($filterDate).')'; ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 text-right">
                <?php $nameOfDay = date('l', strtotime($currentDate)); ?>
                <label style="border-bottom:2px solid #000 !important;">Printed On Day&nbsp;:&nbsp;</label><label style="border-bottom:2px solid #000 !important;"><?php echo '&nbsp;'.$nameOfDay;?></label>

            </div>
        </div>
        <div style="line-height:5px;">&nbsp;</div>
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="panel">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><strong>Purchase Item Detail</strong></div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered sf-table-list">
                                        <thead>
                                        <th class="text-center">S.No</th>
                                        <th class="text-center">Supplier Name</th>
                                        <th class="text-center">G.R.N. No</th>
                                        <th class="text-center">G.R.N. Date</th>
                                        <th class="text-center">Invoice No</th>
                                        <th class="text-center">Qty</th>
                                        <th class="text-center">Amount</th>
                                        <th class="text-center">Current Balance</th>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td class="text-center">1</td>
                                            <td colspan="4" class="text-center">Opening Qty</td>
                                            <td class="text-center"><?php echo $itemOpeningQty->qty;?></td>
                                            <td class="text-right"><?php echo $itemOpeningQty->value;?></td>
                                            <td class="text-center"><?php echo $itemOpeningQty->qty;?></td>
                                        </tr>
                                        <?php
                                        $pcounter = 2;
                                        $currentPurchaseBalance = $itemOpeningQty->qty;
										$totalPurchaseAmountBalance = $itemOpeningQty->value;
                                        foreach ($itemPurchaseData as $row) {
                                        $currentPurchaseBalance += $row->qty;
										$totalPurchaseAmountBalance += $row->value;
                                        ?>
                                        <tr>
                                            <td class="text-center"><?php echo $pcounter++;?></td>
                                            <td><?php echo CommonFacades::getCompanyDatabaseTableValueById($m,'supplier','name',$row->supp_id);?></td>
                                            <td class="text-center"><?php echo $row->grn_no?></td>
                                            <td class="text-center"><?php echo CommonFacades::changeDateFormat($row->grn_date);?></td>
                                            <td class="text-center"><?php echo CommonFacades::getInvoiceNoByGRNNo($m,$row->grn_no)?></td>
                                            <td class="text-center"><?php echo number_format($row->qty,2)?></td>
                                            <td class="text-right"><?php echo number_format($row->value,2)?></td>
                                            <td class="text-center"><?php echo number_format($currentPurchaseBalance,2);?></td>
                                        </tr>
                                        <?php }?>
										<tr>
                                            <th colspan="5" class="text-center">Total</th>
                                            <th class="text-center"><?php echo number_format($currentPurchaseBalance,2);?></th>
                                            <th class="text-right"><?php echo number_format($totalPurchaseAmountBalance,2);?></th>
                                            <th class="text-center">---</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="panel">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><strong>Material Issuance Item Detail</strong></div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered sf-table-list">
                                        <thead>
                                        <th class="text-center">S.No</th>
                                        <th class="text-center">Material Issuance No</th>
                                        <th class="text-center">Material Issuance Date</th>
                                        <th class="text-center">Qty</th>
                                        <th class="text-center">Current Balance</th>
                                        </thead>
                                        <tbody>
                                        <?php
                                        $icounter = 1;
                                        $currentIssueQty = 0;
                                        foreach ($itemIssueData as $row1) {
                                        $currentIssueQty += $row1->qty;
                                        ?>
                                        <tr>
                                            <td class="text-center"><?php echo $icounter++;?></td>
                                            <td class="text-center"><?php echo $row1->material_issuance_no?></td>
                                            <td class="text-center"><?php echo CommonFacades::changeDateFormat($row1->material_issuance_date);?></td>
                                            <td class="text-center"><?php echo $row1->qty?></td>
                                            <td class="text-center"><?php echo $currentIssueQty;?></td>
                                        </tr>
                                        <?php }?>
										<tr>
                                            <th colspan="3" class="text-center">Total</th>
                                            <th class="text-center"><?php echo number_format($currentIssueQty,2);?></th>
                                            <th class="text-center">---</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
		
		<div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="panel">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><strong>Material Received Item Detail</strong></div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered sf-table-list">
                                        <thead>
                                        <th class="text-center">S.No</th>
                                        <th class="text-center">Material Issuance No</th>
                                        <th class="text-center">Material Issuance Date</th>
                                        <th class="text-center">Qty</th>
                                        <th class="text-center">Current Balance</th>
                                        </thead>
                                        <tbody>
                                        <?php
                                        $rcounter = 1;
                                        $currentReceivedQty = 0;
                                        foreach ($itemReceivedData as $row2) {
                                        $currentReceivedQty += $row2->received_qty;
                                        ?>
                                        <tr>
                                            <td class="text-center"><?php echo $rcounter++;?></td>
                                            <td class="text-center"><?php echo $row2->material_issuance_no?></td>
                                            <td class="text-center"><?php echo CommonFacades::changeDateFormat($row2->material_issuance_date);?></td>
                                            <td class="text-center"><?php echo $row2->received_qty?></td>
                                            <td class="text-center"><?php echo $currentReceivedQty;?></td>
                                        </tr>
                                        <?php }?>
										<tr>
                                            <th colspan="3" class="text-center">Total</th>
                                            <th class="text-center"><?php echo number_format($currentReceivedQty,2);?></th>
                                            <th class="text-center">---</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="panel">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                <div class="table-responsive">
                                    <table class="table table-bordered sf-table-list">
                                        <tbody>
                                        <tr>
                                            <th>Opening + Purchase Qty</th>
                                            <td class="text-right"><?php echo number_format($currentPurchaseBalance,2);?></td>
                                        </tr>
                                        <tr>
                                            <th>Issue Qty</th>
                                            <td class="text-right"><?php echo number_format($currentIssueQty,2);?></td>
                                        </tr>
                                        <tr>
                                            <th>Return Qty</th>
                                            <td class="text-right"><?php echo number_format($currentReceivedQty,2);?></td>
                                        </tr>
                                        <tr>
                                            <th>Total Balance</th>
                                            <td class="text-right"><?php echo number_format($currentPurchaseBalance - $currentIssueQty + $currentReceivedQty,2);?></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function viewFilterDateWiseStockInventoryDetail() {
        //alert();
    }
</script>
