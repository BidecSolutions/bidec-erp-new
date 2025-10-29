<?php
$id = $_GET['id'];
$m = $_GET['m'];
$parentCode = $_GET['parentCode'];
$currentDate = date('Y-m-d');
$pageType = $_GET['pageType'];
$parentCode = $_GET['parentCode'];
$getOverAllPaidAmount = 0;
$overAllTotalAmount = 0;
//foreach ($getPurchaseOrderDetail as $row) {
	$eUsername = $getPurchaseOrderDetail->username;
	$eDate = CommonFacades::changeDateFormat($getPurchaseOrderDetail->date);
	if($getPurchaseOrderDetail->purchase_order_status == '2'){
		$aUsername = $getPurchaseOrderDetail->approve_username;
		$aDate = CommonFacades::changeDateFormat($getPurchaseOrderDetail->approve_date);
	}else{
		$aUsername = '-';
		$aDate = '-';
	}
?>

<div style="line-height:5px;">&nbsp;</div>
<div class="row" id="printPurchaseOrderVoucherDetail">
	<link href="{{ URL::asset('assets/css/bootstrap.css') }}" rel="stylesheet" media="print" />
	<style>
		.floatLeft {
			width: 30%;
			float: left;
		}
		
		.floatRight {
			width: 35%;
			float: right;
		}
		.tableBorder {
			border: 3px solid black !important;
		}
		.borderBottom {
			border-bottom: 3px solid black;
		}
		.borderTop {
			border-top: 3px solid black;
		}
		.tableBorder > thead > tr > th, .tableBorder > tbody > tr > th, .tableBorder > tfoot > tr > th, .tableBorder > thead > tr > td, .tableBorder > tbody > tr > td, .tableBorder > tfoot > tr > td {
			border: 1px solid #000;
		}
		th,td {
			font-size: 11px !important;
		}
		.rowBorderBottom {
			border-bottom: inset; font-weight: bold;
		}
		@media print{
			.tableBorder > thead > tr > th, .tableBorder > tbody > tr > th, .tableBorder > tfoot > tr > th, .tableBorder > thead > tr > td, .tableBorder > tbody > tr > td, .tableBorder > tfoot > tr > td {
				border: 1px solid #000;
			}
			th,td {
				font-size: 11px !important;
			}
			.tableBorder {
				border: 3px solid black !important;
			}
			.borderBottom {
				border-bottom: 3px solid black;
			}
			.borderTop {
				border-top: 3px solid black;
			}
			.rowBorderBottom {
				border-bottom: inset; font-weight: bold;
			}
		}
	</style>
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="well">
		
           <div class="row">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center borderBottom borderTop quotationHeading">Edit Tax Purchase Order Voucher</div>
			</div>
			<div style="line-height:5px;">&nbsp;</div>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="floatLeft">
                        <table class="table table-bordered table-condensed tableMargin tableBorder">
                            <tbody>
								<tr>
									<td style="width:50%;">Purchase Order No.</td>
									<td style="width:50%;"><?php echo $getPurchaseOrderDetail->purchase_order_no;?></td>
								</tr>
								<tr>
									<td>Purchase Order Date</td>
									<td><?php echo CommonFacades::changeDateFormat($getPurchaseOrderDetail->purchase_order_date);?></td>
								</tr>
								<tr>
									<td>Location</td>
									<td><?php echo $getPurchaseOrderDetail->location_name;?></td>
								</tr>
                                <tr>
									<td>Supplier Name</td>
									<td><?php echo $getPurchaseOrderDetail->name;?></td>
								</tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="floatRight">
                        <table class="table table-bordered table-condensed tableMargin tableBorder">
                            <tbody>
								<tr>
									<td style="width:50%;">Purchase Request No.</td>
									<td style="width:50%;"><?php echo $getPurchaseOrderDetail->purchase_request_no;?></td>
								</tr>
								<tr>
									<td>Purchase Request Date</td>
									<td><?php echo CommonFacades::changeDateFormat($getPurchaseOrderDetail->purchase_request_date);?></td>
								</tr>
								<tr>
									<td>Department</td>
									<td><?php echo $getPurchaseOrderDetail->department_name;?></td>
								</tr>
                                <tr>
									<td>Sub Department</td>
									<td><?php echo $getPurchaseOrderDetail->sub_department_name;?></td>
								</tr>
                            </tbody>
                        </table>
                    </div>

                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <p><?php echo $getPurchaseOrderDetail->description?></p>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="table-responsive">
                        <table class="table table-bordered table-condensed tableMargin tableBorder">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width:50px;">S.No</th>
                                    <th class="text-center">Category</th>
                                    <th class="text-center">Item Code</th>
                                    <th class="text-center">Item Name</th>
                                    <th class="text-center">Unit</th>
                                    <th class="text-center">Grn Detail</th>
                                    <th class="text-center">Quantity</th>
                                    <th class="text-center">Unit Price</th>
                                    <th class="text-center">Sub Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $counter = 1;
                                    foreach ($getPurchaseOrderDataDetail as $row1){
                                        $overAllTotalAmount += $row1->sub_total;
                                ?>
                                        <tr>
                                            <td class="text-center"><?php echo $counter++;?></td>
                                            <td><?php echo $row1->main_ic?></td>
                                            <td><?php echo $row1->item_code;?></td>
                                            <td><?php echo $row1->sub_ic;?></td>
                                            <td><?php echo $row1->uom_name;?></td>
                                            <td>
                                                <?php
                                                    $grnDetail = DB::table('grn_data')->where('po_no','=',$id)->where('category_id','=',$row1->category_id)->where('sub_item_id','=',$row1->sub_item_id)->where('status','=','1')->get();
                                                    $grnCounter = 0;
                                                    foreach ($grnDetail as $grnRow) {
                                                        $grnCounter++;
                                                        echo  'GRN No => '.$grnRow->grn_no.' => GRN Date => '.CommonFacades::changeDateFormat($grnRow->grn_date).' <br />';
                                                    }
                                                ?>
                                            </td>
                                            <td class="text-center"><?php echo $row1->purchase_order_qty;?></td>
                                            <td class="text-center"><?php echo $row1->unit_price;?></td>
                                            <td class="text-right"><?php echo number_format($row1->sub_total);?></td>
                                        </tr>
                                        <?php echo StoreFacades::displayItemWiseLastPurchaseRate($m,$row1->category_id,$row1->sub_item_id);?>
                                <?php
                                    }
                                ?>
								<tr>
									<td colspan="9"><br /> <?php //echo CommonFacades::number_to_word( $overAllTotalAmount ); ?></td>
								</tr>
								<input type="hidden" readonly name="hiddenAmmount" id="hiddenAmmount" value="<?php echo round($overAllTotalAmount);?>" onkeyup="NumToWord(this.value,'divDisplayWords')"; />
								<tr>
									<td colspan="3"><strong>Amount in words</strong></td>
									<td colspan="6"><strong id="divDisplayWords"></strong></td>
								</tr>
								<tr>
									<td colspan="8" class="text-right"><strong>Total</strong> <?php //echo CommonFacades::number_to_word( $overAllTotalAmount ); ?></td>
									<td class="text-right"><strong><?php echo number_format($overAllTotalAmount);?></strong></td>
								</tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
			<div style="line-height:5px;">&nbsp;</div>
            <div class="row">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<?php 
						if($getPurchaseOrderDetail->voucher_type == 2){
					?>
						<div class="row">
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
								<div class="floatLeft">
									<table  class="table table-bordered table-condensed tableMargin tableBorder">
										<tbody>
											<tr>
												<th style="width:40%;">PV No.</th>
												<td style="width:60%;"><?php echo $getPaymentVoucherDetail->pv_no;?></td>
											</tr>
											<tr>
												<th style="width:40%;">Slip No.</th>
												<td style="width:60%;"><?php echo $getPaymentVoucherDetail->slip_no;?></td>
											</tr>
											<tr>
												<th>PV Date</th>
												<td><?php echo CommonFacades::changeDateFormatTwo($getPaymentVoucherDetail->pv_date);?></td>
											</tr>
										</tbody>
									</table>
								</div>
								<div class="floatRight">
									<table  class="table table-bordered table-condensed tableMargin tableBorder">
										<tbody>
											<tr>
												<th style="width:40%;">Cheque No.</th>
												<td style="width:60%;"><?php echo $getPaymentVoucherDetail->cheque_no;?></td>
											</tr>
											<tr>
												<th style="width:40%;">Cheque Date.</th>
												<td style="width:60%;"><?php echo CommonFacades::changeDateFormatTwo($getPaymentVoucherDetail->cheque_date);?></td>
											</tr>
											<tr>
												<th>Region Name</th>
												<td><?php echo $regionName;?></td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
							
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
								<div class="table-responsive">
									<table  class="table table-bordered table-condensed tableMargin tableBorder">
										<thead>
											<tr>
												<th class="text-center" style="width:50px;">S.No</th>
												<th class="text-center">Account</th>
												<th class="text-center">Description</th>
												<th class="text-center" style="width:150px;">Debit</th>
												<th class="text-center" style="width:150px;">Credit</th>
											</tr>
										</thead>
										<tbody>
											<?php
												$getPaymentVoucherDataDetail = DB::table('pv_data')->where('pv_no','=',$getPaymentVoucherDetail->pv_no)->get();
												$pvCounter = 1;
												$getOverAllDebitAmount = 0;
												$getOverAllCreditAmount = 0;
												foreach ($getPaymentVoucherDataDetail as $pvRow) {
											?>
												<tr>
													<td class="text-center"><?php echo $pvCounter++;?></td>
													<td><?php  echo FinanceFacades::getAccountNameByAccId($pvRow->acc_id,$m);?></td>
													<td><?php echo $pvRow->description?></td>
													<td class="debit_amount text-right">
														<?php
														if($pvRow->debit_credit == 1){
															$getOverAllCreditAmount += $pvRow->amount;
															echo number_format($pvRow->amount,2);
														}else{}
														?>
													</td>
													<td class="credit_amount text-right">
														<?php
														if($pvRow->debit_credit == 0){
															$getOverAllDebitAmount += $pvRow->amount;
															echo number_format($pvRow->amount,2);
														}else{}
														?>
													</td>
												</tr>
											<?php
												}
											?>
											<tr class="sf-table-total">
												<td colspan="3">
													<label for="field-1" class="sf-label"><b>Total</b></label>
												</td>
												<?php 
													$getOverAllPaidAmount+=$getOverAllDebitAmount;
												?>
												<td class="text-right"><b><?php echo number_format($getOverAllCreditAmount,2);?></b></td>
												<td class="text-right"><b><?php echo number_format($getOverAllDebitAmount,2);?></b></td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
							<div style="line-height:8px;">&nbsp;</div>
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
								<div class="row">
									<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
										<div class="table-responsive">
											<table  class="table table-bordered table-condensed tableMargin tableBorder">
												<thead>
												<tr>
													<th>Description</th>
													<th colspan="5"><?php echo $getPaymentVoucherDetail->description;?></th>
												</tr>
												
												</thead>
											</table>
										</div>
									</div>
								</div>
							</div>
						</div>
					<?php
						}
					?>
				</div>
			</div>
			<div style="line-height:5px;">&nbsp;</div>
			<div class="row">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6"></div>
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">						
						<div class="">
							<form action="{{ route('update.tax.purchase.voucher') }}" method="post">
								{{ csrf_field() }}
								<input type="hidden" name="pageType" value="{{ Input::get('pageType') }}">
								<input type="hidden" name="parentCode" value="{{ Input::get('parentCode') }}">
								<input type="hidden" name="purchase_order_no" value="{{ $getPurchaseOrderDetail->purchase_order_no }}">
								<div class="form-group col-lg-4 col-md-4">
									<select {{ $getPurchaseOrderDetail->purchase_order_status == 2 ? 'disabled' : '' }} class="form-control requiredField" name="account_id" id="account_id">
										<option value="">Select Account</option>
										@foreach($accountList as $key => $y)
											<option value="<?php echo $y->id.'<*>'.$y->code?>">{{ $y->code .' ---- '. $y->name}}</option>
										@endforeach
									</select>
								</div>								
								<div class="form-group col-lg-3 col-md-3">
									<input {{ $getPurchaseOrderDetail->purchase_order_status == 2 ? 'disabled' : '' }} class="form-control" type="text" name="taxPercentage" id="taxPercentage" value="{{ $getPurchaseOrderDetail->custom_tax_percent ?? '' }}">
								</div>
								<div class="form-group col-lg-3 col-md-3">
									<input {{ $getPurchaseOrderDetail->purchase_order_status == 2 ? 'disabled' : '' }} class="form-control" type="text" name="taxValue" id="taxValue" value="{{ percentage($getPurchaseOrderDetail->custom_tax_percent, $overAllTotalAmount) }}">
								</div>
								@if ($getPurchaseOrderDetail->purchase_order_status == 1)																								
									<button>submit</button>
								@endif
							</form>
						</div>
						<div class="">
							<table  class="table table-bordered table-condensed tableMargin tableBorder">
								<tbody>
									<tr>
										<th>Sub Total Amount</th>
										<td class="text-right"><?php echo number_format($overAllTotalAmount);?></td>
									</tr>
									<tr>
										<th>Paid Amount</th>
										<td class="text-right"><?php echo number_format($getOverAllPaidAmount);?></td>
									</tr>
									<tr>
										<th>Remaining Amount</th>
										<td class="text-right"><?php echo number_format($overAllTotalAmount-$getOverAllPaidAmount);?></td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
					<div class="panel panelBorder">
						<div class="text-center" style="font-weight: bold;">Entered By</div>
						<div style="border-bottom: inset;"></div>
						<div class="panel-body">
							<div class="row">
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 rowBorderBottom">
									Sign:
								</div>
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 rowBorderBottom">
									Name:&nbsp;&nbsp;&nbsp;<?php echo $eUsername;?>
								</div>
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 rowBorderBottom">
									Date:&nbsp;&nbsp;&nbsp;<?php echo $eDate;?>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
					<div class="panel panelBorder">
						<div class="text-center" style="font-weight: bold;">Reviewed By</div>
						<div style="border-bottom: inset;"></div>
						<div class="panel-body">
							<div class="row">
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 rowBorderBottom">
									Sign:
								</div>
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 rowBorderBottom">
									Name:&nbsp;&nbsp;&nbsp;<?php echo $aUsername;?>
								</div>
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 rowBorderBottom">
									Date:&nbsp;&nbsp;&nbsp;<?php echo $aDate;?>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
					<div class="panel panelBorder">
						<div class="text-center" style="font-weight: bold;">Approved By</div>
						<div style="border-bottom: inset;"></div>
						<div class="panel-body">
							<div class="row">
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 rowBorderBottom">
									Sign:
								</div>
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 rowBorderBottom">
									Name:
								</div>
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 rowBorderBottom">
									Date:
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
        </div>
    </div>
    <?php //}?>
</div>
<script>
    
	var subTotal = {{ $overAllTotalAmount }};
	
	$('#taxPercentage').keyup(function() {
		var taxPercentage = $('#taxPercentage').val();
		var taxValue = $('#taxValue').val();
		var value = taxPercentage/100;
		taxValue = subTotal * value;
		$('#taxValue').val(taxValue);
		console.log('changed', value, taxValue, subTotal, taxPercentage);

	})
	
</script>