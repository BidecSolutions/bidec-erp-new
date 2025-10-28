<?php
$id = $_GET['id'];
$m = getSessionCompanyId();
$parentCode = $_GET['parentCode'];
$currentDate = date('Y-m-d');
$pageType = $_GET['pageType'];
$parentCode = $_GET['parentCode'];
$getOverAllPaidAmount = 0;
$overAllTotalAmount = 0;
//foreach ($getPurchaseOrderDetail as $row) {
$eUsername = $getPurchaseOrderDetail->username;
$eDate = CommonFacades::changeDateFormat($getPurchaseOrderDetail->date);
if ($getPurchaseOrderDetail->purchase_order_status == '2') {
    $aUsername = $getPurchaseOrderDetail->approve_username;
    $aDate = CommonFacades::changeDateFormat($getPurchaseOrderDetail->approve_date);
} else {
    $aUsername = '-';
    $aDate = '-';
}
?>
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">
        <?php
        if ($getPurchaseOrderDetail->purchase_order_status == '1') {
            if ($getPurchaseOrderDetail->status == '1' || $getPurchaseOrderDetail->status == '2') {
                echo StoreFacades::displayApproveDeleteRepostButtonPurchaseOrder($m, $getPurchaseOrderDetail->purchase_order_status, $getPurchaseOrderDetail->status, $getPurchaseOrderDetail->purchase_order_no, 'purchase_order_no', 'purchase_order_status', 'status', 'purchase_order', 'purchase_order_data', $pageType, $parentCode, $getPurchaseOrderDetail->purchase_request_no);
                echo CommonFacades::displayPrintButtonInView('printPurchaseOrderVoucherDetail', '', '1');
                echo StoreFacades::reversePurchaseOrderDetailBeforeApproval($m, $getPurchaseOrderDetail->purchase_order_no, $getPurchaseOrderDetail->purchase_request_no);
                $initialEmailAddress = CommonFacades::voucherInitialEmailAddress($getPurchaseOrderDetail->user_id);
            }
        } else {
            echo CommonFacades::displayPrintButtonInView('printPurchaseOrderVoucherDetail', '', '1');
            echo StoreFacades::reversePurchaseOrderDetailAfterApproval($m, $getPurchaseOrderDetail->purchase_order_no, $getPurchaseOrderDetail->purchase_request_no);
            $initialEmailAddress = CommonFacades::voucherInitialEmailAddress($getPurchaseOrderDetail->approve_user_id);
        }
        ?>
    </div>
</div>
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <input type="hidden" name="initialEmailAddress" id="initialEmailAddress" value="<?php echo $initialEmailAddress; ?>" />
        <input type="hidden" name="purchaseOrderDate" id="purchaseOrderDate" value="<?php echo $getPurchaseOrderDetail->purchase_order_date; ?>" />
        <input type="hidden" name="locationId" id="locationId" value="<?php echo $getPurchaseOrderDetail->location_id; ?>" />
        <label>Remarks</label>
        <textarea name="poVoucherRemarks" id="poVoucherRemarks" class="form-control"><?php if (empty($getPurchaseOrderDetail->voucher_remarks)) {
            echo '-';
        } else {
            echo $getPurchaseOrderDetail->voucher_remarks;
        } ?></textarea>
    </div>
</div>
<div style="line-height:5px;">&nbsp;</div>
<div class="row" id="printPurchaseOrderVoucherDetail">
    <link href="{{ URL::asset('assets/css/bootstrap.css') }}" rel="stylesheet" media="print" />
    <style>
        .floatLeft {
            width: 50%;
            float: left;
        }

        .floatRight {
            width: 45%;
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

        .tableBorder>thead>tr>th,
        .tableBorder>tbody>tr>th,
        .tableBorder>tfoot>tr>th,
        .tableBorder>thead>tr>td,
        .tableBorder>tbody>tr>td,
        .tableBorder>tfoot>tr>td {
            border: 1px solid #000;
        }

        th,
        td {
            font-size: 11px !important;
        }

        .rowBorderBottom {
            border-bottom: inset;
            font-weight: bold;
        }

        @media print {

            .tableBorder>thead>tr>th,
            .tableBorder>tbody>tr>th,
            .tableBorder>tfoot>tr>th,
            .tableBorder>thead>tr>td,
            .tableBorder>tbody>tr>td,
            .tableBorder>tfoot>tr>td {
                border: 1px solid #000;
            }

            th,
            td {
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
                border-bottom: inset;
                font-weight: bold;
            }
        }
    </style>
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="well">
            <?php
            if (empty($getHeaderFooterSettingDetail)) {
                echo CommonFacades::headerPrintSectionInPrintView($m);
            } else {
                echo CommonFacades::headerSettingPrintSectionInPrintView($m, '1');
            }
            ?>
            <div class="row">
                <div
                    class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center borderBottom borderTop quotationHeading">
                    View <b>Purchase Order</b> Voucher Detail</div>
            </div>
            <div style="line-height:5px;">&nbsp;</div>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="floatLeft">
                        <table class="table table-bordered table-condensed tableMargin tableBorder">
                            <tbody style="width: 90%">
                                <tr>
                                    <td style="width:0%;">Purchase Order No.</td>
                                    <td style="width:0%;"><?php echo $getPurchaseOrderDetail->purchase_order_no; ?></td>
                                </tr>
                                <tr>
                                    <td>Purchase Order Date</td>
                                    <td><?php echo CommonFacades::changeDateFormat($getPurchaseOrderDetail->purchase_order_date); ?></td>
                                </tr>
                                <tr>
                                    <td style="width:0%;">Purchase Request No.</td>
                                    @if ($getPurchaseOrderDetail->po_type == 'direct')
                                        <td style="width:0%;">DIRECT</td>                                        
                                    @else                                        
                                        <td style="width:0%;"><?php echo $getPurchaseOrderDetail->purchase_request_no; ?></td>
                                    @endif
                                </tr>
                                <tr>
                                    <td>Purchase Request Date</td>
                                    <td><?php echo CommonFacades::changeDateFormat($getPurchaseOrderDetail->purchase_request_date); ?></td>
                                </tr>
                                <tr>
                                    <td>Invoice/Quotation</td>
                                    <td><?php echo $getPurchaseOrderDetail->qoutation_no; ?></td>
                                </tr>
                                <tr>
                                    <td style="width:0%;">Location</td>
                                    <td style="width:0%;"><?php echo $getPurchaseOrderDetail->location_name; ?></td>
                                </tr>
                                <tr>
                                    <td>Department</td>
                                    <td><?php echo $getPurchaseOrderDetail->department_name; ?></td>
                                </tr>
                                <tr>
                                    <td>Sub Department</td>
                                    <td><?php echo $getPurchaseOrderDetail->sub_department_name; ?></td>
                                </tr>
                                <tr>
                                    <td>Project</td>
                                    <td><?php echo $getPurchaseOrderDetail->project_name; ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="floatRight">
                        <table class="table table-bordered table-condensed tableMargin tableBorder">
                            <tbody>
                                <tr>
                                    <td>Supplier Name</td>
                                    <td> {{ $getPurchaseOrderDetail->name ?? '' }}</td>
                                </tr>
                                <tr>
                                    <td>Supplier Address</td>
                                    <td> {{ $getPurchaseOrderDetail->physical_address ?? '' }}</td>
                                </tr>
                                <tr>
                                    <td>Supplier NTN</td>
                                    <td>{{ $getPurchaseOrderDetail->ntn_no ?? '' }}</td>
                                </tr>
                                <tr>
                                    <td>Supplier Mobile no.</td>
                                    <td>{{ $getPurchaseOrderDetail->mobile_no ?? '' }}</td>
                                </tr>
                                <tr>
                                    <td>Supplier Phone no.</td>
                                    <td>{{ $getPurchaseOrderDetail->phone_no ?? '' }}</td>
                                </tr>
                                <tr>
                                    <td>Bank</td>
                                    <td> {{ $getPurchaseOrderDetail->bank_name ?? '' }}</td>
                                </tr>
                                <tr>
                                    <td>Account Title</td>
                                    <td> {{ $getPurchaseOrderDetail->account_title ?? '' }}</td>
                                </tr>
                                <tr>
                                    <td>Account No.</td>
                                    <td> {{ $getPurchaseOrderDetail->account_no ?? '' }}</td>
                                </tr>

                            </tbody>
                        </table>
                    </div>

                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <p><span>PO Note:</span> <?php echo $getPurchaseOrderDetail->po_note;?></p>
                </div>
                {{-- <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <p>{{ $getPurchaseOrderDetail->description ?? '' }}</p>
                </div> --}}
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
                                    <td class="text-center"><?php echo $counter++; ?></td>
                                    <td><?php echo  $row1->main_ic ?? 0; ?></td>
                                    <td><?php echo $row1->item_code ?? 0; ?></td>
                                    <td><?php echo $row1->sub_ic ?? $row1->item_name; ?></td>
                                    <td><?php echo $row1->uom_name; ?></td>
                                    <td>
                                        <?php
                                        $grnDetail = DB::table('grn_data')
                                            ->where('po_no', '=', $id)
                                            ->where('category_id', '=', $row1->category_id)
                                            ->where('sub_item_id', '=', $row1->sub_item_id)
                                            ->where('status', '=', '1')
                                            ->get();
                                        $grnCounter = 0;
                                        foreach ($grnDetail as $grnRow) {
                                            $grnCounter++;
                                            echo 'GRN No => ' . $grnRow->grn_no . ' => GRN Date => ' . CommonFacades::changeDateFormat($grnRow->grn_date) . ' <br />';
                                        }
                                        ?>
                                    </td>
                                    <td class="text-center"><?php echo $row1->purchase_order_qty; ?></td>
                                    <td class="text-center">
                                        <?php echo $row1->unit_price; ?>
                                        @if ($getPurchaseOrderDetail->paymentType != 1)
                                            ({{ DB::connection('tenant')->table('payment_type')->where('id', $getPurchaseOrderDetail->paymentType)->value('payment_type_name') }} {{ $row1->unit_price / $getPurchaseOrderDetail->payment_type_rate }})
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        <?php echo number_format($row1->sub_total, 0); ?>
                                        @if ($getPurchaseOrderDetail->paymentType != 1)
                                            ({{ DB::connection('tenant')->table('payment_type')->where('id', $getPurchaseOrderDetail->paymentType)->value('payment_type_name') }} {{ $row1->sub_total / $getPurchaseOrderDetail->payment_type_rate }})
                                        @endif
                                    </td>
                                </tr>
                                <tr class="hidden-print">
                                    <td colspan="9">
                                        <?php echo StoreFacades::displayItemWiseLastPurchaseRate($m, $row1->category_id, $row1->sub_item_id); ?>
                                    </td>
                                </tr>
                                <?php
                                    }
                                ?>
                                <tr class="hidden-print">
                                    <td colspan="9"><br /> <?php //echo CommonFacades::number_to_word( $overAllTotalAmount );
                                    ?></td>
                                </tr>
                                <input type="hidden" readonly name="hiddenAmmount" id="hiddenAmmount"
                                    value="<?php echo round($overAllTotalAmount); ?>" onkeyup="NumToWord(this.value,'divDisplayWords')"; />
                                <tr>
                                    <td colspan="3"><strong>Amount in words</strong></td>
                                    <td colspan="6"><strong style="float: right;" id="divDisplayWords"></strong></td>
                                </tr>
                                <tr>
                                    <td colspan="8" class="text-right"><strong>Total</strong> <?php //echo CommonFacades::number_to_word( $overAllTotalAmount );
                                    ?>
                                    </td>
                                    <td class="text-right"><strong><?php echo number_format($overAllTotalAmount,0); ?></strong></td>
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
                                <table class="table table-bordered table-condensed tableMargin tableBorder">
                                    <tbody>
                                        <tr>
                                            <th style="width:40%;">PV No.</th>
                                            <td style="width:60%;"><?php echo $getPaymentVoucherDetail->pv_no; ?></td>
                                        </tr>
                                        <tr>
                                            <th style="width:40%;">Slip No.</th>
                                            <td style="width:60%;"><?php echo $getPaymentVoucherDetail->slip_no; ?></td>
                                        </tr>
                                        <tr>
                                            <th>PV Date</th>
                                            <td><?php echo CommonFacades::changeDateFormatTwo($getPaymentVoucherDetail->pv_date); ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="floatRight">
                                <table class="table table-bordered table-condensed tableMargin tableBorder">
                                    <tbody>
                                        <tr>
                                            <th style="width:40%;">Cheque No.</th>
                                            <td style="width:60%;"><?php echo $getPaymentVoucherDetail->cheque_no; ?></td>
                                        </tr>
                                        <tr>
                                            <th style="width:40%;">Cheque Date.</th>
                                            <td style="width:60%;"><?php echo CommonFacades::changeDateFormatTwo($getPaymentVoucherDetail->cheque_date); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Region Name</th>
                                            <td><?php echo $regionName; ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="table-responsive">
                                <table class="table table-bordered table-condensed tableMargin tableBorder">
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
                                            <td class="text-center"><?php echo $pvCounter++; ?></td>
                                            <td><?php echo FinanceFacades::getAccountNameByAccId($pvRow->acc_id, $m); ?></td>
                                            <td><?php echo $pvRow->description; ?></td>
                                            <td class="debit_amount text-right">
                                                <?php
                                                if ($pvRow->debit_credit == 1) {
                                                    $getOverAllCreditAmount += $pvRow->amount;
                                                    echo number_format($pvRow->amount, 2);
                                                } else {
                                                }
                                                ?>
                                            </td>
                                            <td class="credit_amount text-right">
                                                <?php
                                                if ($pvRow->debit_credit == 0) {
                                                    $getOverAllDebitAmount += $pvRow->amount;
                                                    echo number_format($pvRow->amount, 2);
                                                } else {
                                                }
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
                                            $getOverAllPaidAmount += $getOverAllDebitAmount;
                                            ?>
                                            <td class="text-right"><b><?php echo number_format($getOverAllCreditAmount,0); ?></b></td>
                                            <td class="text-right"><b><?php echo number_format($getOverAllDebitAmount,0); ?></b></td>
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
                                        <table class="table table-bordered table-condensed tableMargin tableBorder">
                                            <thead>
                                                <tr>
                                                    <th>Description</th>
                                                    <th colspan="5"><?php echo $getPaymentVoucherDetail->description; ?></th>
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
                    <div class="floatLeft">
                        {{-- <table class="table table-bordered table-condensed tableMargin tableBorder">
                            <tbody>
								<tr>
									<td>Bank</td>
									<td> {{$getPurchaseOrderDetail->bank_name ?? ''}}</td>
								</tr>
								<tr>
									<td>Account Title</td>
									<td> {{$getPurchaseOrderDetail->account_title ?? ''}}</td>
								</tr>							
								<tr>
									<td>Account No.</td>
									<td> {{$getPurchaseOrderDetail->account_no ?? ''}}</td>
								</tr>							
								
                            </tbody>
                        </table> --}}
                    </div>
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 borderBottom borderTop quotationHeading">Expense Claim Detail</div>
                        <div style="line-height:5px;">&nbsp;</div>
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <table class="table table-bordered table-condensed tableMargin tableBorder">
                                <thead>
                                    <tr>
                                        <th class="text-center">S.No</th>
                                        <th class="text-center">Voucher No</th>
                                        <th class="text-center">Expense Date</th>
                                        <th class="text-center">Supplier Name</th>
                                        <th class="text-center">Project Name</th>
                                        <th class="text-center">Location Name</th>
                                        <th class="text-center">Department Name</th>
                                        <th class="text-center">Sub Department Name</th>
                                        <th class="text-center">Description</th>
                                        <th class="text-center">Expense Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $totalExpenseAmountTwo = 0;
                                        $ecvCounter = 1;
                                    @endphp
                                    @foreach ($expense_claim_vouchers as $ecvRow)
                                        @php
                                            $totalExpenseAmountTwo += $ecvRow->total_amount;
                                        @endphp
                                        <tr>
                                            <td class="text-center">{{$ecvCounter++}}</td>
                                            <td class="text-center">{{ strtoupper($ecvRow->ev_no) }}</td>
                                            <td class="text-center">{{FinanceFacades::changeDateFormat($ecvRow->expense_date)}}</td>
                                            <td>{{$ecvRow->name}}</td>
                                            <td>{{$ecvRow->project_name}}</td>
                                            <td>{{$ecvRow->location_name}}</td>
                                            <td>{{$ecvRow->department_name}}</td>
                                            <td>{{$ecvRow->sub_department_name}}</td>
                                            <td>{{$ecvRow->description}}</td>
                                            <td class="text-right">{{number_format($ecvRow->total_amount,0)}}
                                            @if ($ecvRow->paymentType != 1)
                                            ({{ DB::connection('tenant')->table('payment_type')->where('id', $ecvRow->paymentType)->value('payment_type_name') }} {{ $ecvRow->total_amount / $ecvRow->payment_type_rate }})
                                             @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="9">Total Expense Amount</th>
                                        <th class="text-right">{{number_format($totalExpenseAmountTwo,0)}}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    <div class="floatLeft">
                        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    @if ($getPurchaseOrderDetail->term_and_condition != null  && $getPurchaseOrderDetail->term_and_condition != '0')                            
                                    <ul class="form_control">
                                        <li>Purchase Order should not be accepted if any alterations have been made to the
                                            date,quantity,rate, description or name of the Supplier. </li>
            
                                        <li>Payment will be made {{ $getPurchaseOrderDetail->term_and_condition }}% in advance In
                                            same account title as per invoice mentioned.</li>
                                        <li>Defective material shall not be accepted & will be replaced at vendor cost</li>
                                    </ul>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="floatRight">
                        <table class="table table-bordered table-condensed tableMargin tableBorder">
                            <tbody>
                                <?php 
                                    if($getPurchaseOrderDetail->paymentType == 2){
                                ?>
                                        <tr>
                                            <th colspan="2" class="text-center">This Purchase Order is calculated in Dollar, The Rate is Rs <span><?php echo $getPurchaseOrderDetail->payment_type_rate?></span></th>
                                        </tr>
                                <?php
                                    }
                                ?>
                                <tr>
                                    <th>Total Amount</th>
                                    <td class="text-right"><?php echo number_format($overAllTotalAmount, 2); ?> {{ $getPurchaseOrderDetail->paymentType != 1 ? '('.config('currency.po_currency.'.$getPurchaseOrderDetail->paymentType) . $overAllTotalAmount/$getPurchaseOrderDetail->payment_type_rate . ')' : '' }}</td>
                                </tr>
                                <tr>
                                    <th>Tax Amount</th>
                                    
                                    <td class="text-right">{{ number_format(($getPurchaseOrderDetail->custom_tax_percent*$overAllTotalAmount)/100, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Purchase Order Discount</th>
                                    
                                    <td class="text-right">{{ number_format($getPurchaseOrderDetail->po_discount, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Grand Total Amount</th>
                                    <td class="text-right">{{ number_format($overAllTotalAmount - $getPurchaseOrderDetail->po_discount +($getPurchaseOrderDetail->custom_tax_percent*$overAllTotalAmount)/100, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Paid Amount</th>
                                    <td class="text-right">{{ number_format($getOverAllPaidAmount, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Remaining Amount</th>
                                    <td class="text-right">{{ number_format($overAllTotalAmount - $getPurchaseOrderDetail->po_discount +($getPurchaseOrderDetail->custom_tax_percent*$overAllTotalAmount)/100 - $getOverAllPaidAmount, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Grand Total With Expense</th>
                                    <td class="text-right">{{ number_format($overAllTotalAmount- $getPurchaseOrderDetail->po_discount +($getPurchaseOrderDetail->custom_tax_percent*$overAllTotalAmount)/100 + $totalExpenseAmountTwo, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
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
                                    Name:&nbsp;&nbsp;&nbsp;<?php echo $eUsername; ?>
                                </div>
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 rowBorderBottom">
                                    Date:&nbsp;&nbsp;&nbsp;<?php echo $eDate; ?>
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
                                    Name:&nbsp;&nbsp;&nbsp;<?php echo $aUsername; ?>
                                </div>
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 rowBorderBottom">
                                    Date:&nbsp;&nbsp;&nbsp;<?php echo $aDate; ?>
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
            @if (count($expenseVoucherDetail) > 0)               
            <div class="row hidden-print">
                <div class="col-md-12">
                    <h3>Expense Detail</h3>
                </div>
                <div class="col-md-6">
                    <table class="table table-bordered table-condensed tableMargin tableBorder">
                        <thead>
                            <th>Head</th>
                            <th>Amount</th>
                        </thead>
                        <tbody>
                            @foreach ($expenseVoucherDetail as $item)                                
                                <tr>
                                    <td>{{ $item->accountHead->name ?? '' }}</td>
                                    <td>{{ $item->expense_amount }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>
    </div>
    <?php //}
    ?>
</div>
<script>
    function cancelCompletePurchaseOrderDetail(param1, param2, param3, param4, param5, param6, param7) {
        var pageType = '<?php echo $pageType; ?>';
        var parentCode = '<?php echo $parentCode; ?>';
        var m = '{{ getSessionCompanyId() }}'
        $.ajax({
            url: '<?php echo url('/'); ?>/pd/cancelCompletePurchaseOrderDetail',
            type: "GET",
            data: {
                m: m,
                poNo: param1,
                id: param2,
                categoryId: param3,
                subItemId: param4,
                supplierId: param5,
                purchaseRequestDataId: param6,
                purchaseRequestNo: param7,
                pageType: pageType,
                parentCode: parentCode
            },
            success: function(data) {
                if (data == 'Done') {
                    location.reload();
                } else {
                    alert(removeTags(data));
                }
            }
        });
    }

    function cancelSinglePurchaseOrderItemRow(param1, param2, param3, param4, param5, param6, param7, param8, param9,
        param10) {
        var pageType = '<?php echo $pageType; ?>';
        var parentCode = '<?php echo $parentCode; ?>';
        var m = '{{ getSessionCompanyId() }}'
        $.ajax({
            url: '<?php echo url('/'); ?>/pd/cancelSinglePurchaseOrderItemRow',
            type: "GET",
            data: {
                m: m,
                poNo: param1,
                id: param2,
                categoryId: param3,
                subItemId: param4,
                supplierId: param5,
                purchaseRequestDataId: param6,
                purchaseRequestNo: param7,
                purchaseRequestDate: param8,
                parentCode: param9,
                purchaseOrderDate: param10,
                pageType: pageType,
                parentCode: parentCode
            },
            success: function(data) {
                if (data == 'Done') {
                    $('#modal').modal('toggle');
                    $("#viewPurchaseOrderDetail_" + param1 + "").click();
                } else {
                    alert(removeTags(data));
                }
            }
        });
    }

    function approvePurchaseOrder(m, voucherStatus, rowStatus, columnValue, columnOne, columnTwo, columnThree, tableOne,
        tableTwo) {
        var initialEmailAddress = $('#initialEmailAddress').val();
        var purchaseOrderDate = $('#purchaseOrderDate').val();
        var poVoucherRemarks = $('#poVoucherRemarks').val();
        var locationId = $('#locationId').val();
        $.ajax({
            url: "<?php echo url('/'); ?>/std/approvePurchaseOrder",
            type: "GET",
            data: {
                m: m,
                voucherStatus: voucherStatus,
                rowStatus: rowStatus,
                columnValue: columnValue,
                columnOne: columnOne,
                columnTwo: columnTwo,
                columnThree: columnThree,
                tableOne: tableOne,
                tableTwo: tableTwo,
                initialEmailAddress: initialEmailAddress,
                purchaseOrderDate: purchaseOrderDate,
                poVoucherRemarks: poVoucherRemarks,
                locationId: locationId
            },
            success: function(data) {
                filterVoucherList();
            }
        });
    }
    NumToWord('<?php echo round($overAllTotalAmount); ?>', 'divDisplayWords');

    function NumToWord(numberInput, div) {
        let myDiv = document.querySelector('#' + div);
        let oneToTwenty = ['', 'one ', 'two ', 'three ', 'four ', 'five ', 'six ', 'seven ', 'eight ', 'nine ', 'ten ',
            'eleven ', 'twelve ', 'thirteen ', 'fourteen ', 'fifteen ', 'sixteen ', 'seventeen ', 'eighteen ',
            'nineteen '
        ];
        let tenth = ['', '', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety'];

        if (numberInput.toString().length > 7) return myDiv.innerHTML = 'overlimit';
        console.log(numberInput);
        //let num = ('0000000000'+ numberInput).slice(-10).match(/^(\d{1})(\d{2})(\d{2})(\d{2})(\d{1})(\d{2})$/);
        let num = ('0000000' + numberInput).slice(-7).match(/^(\d{1})(\d{1})(\d{2})(\d{1})(\d{2})$/);
        console.log(num);
        if (!num) return;

        let outputText = num[1] != 0 ? (oneToTwenty[Number(num[1])] ||
            `${tenth[num[1][0]]} ${oneToTwenty[num[1][1]]}`) + ' million ' : '';

        outputText += num[2] != 0 ? (oneToTwenty[Number(num[2])] || `${tenth[num[2][0]]} ${oneToTwenty[num[2][1]]}`) +
            'hundred ' : '';
        outputText += num[3] != 0 ? (oneToTwenty[Number(num[3])] || `${tenth[num[3][0]]} ${oneToTwenty[num[3][1]]}`) +
            ' thousand ' : '';
        outputText += num[4] != 0 ? (oneToTwenty[Number(num[4])] || `${tenth[num[4][0]]} ${oneToTwenty[num[4][1]]}`) +
            'hundred ' : '';
        outputText += num[5] != 0 ? (oneToTwenty[Number(num[5])] || `${tenth[num[5][0]]} ${oneToTwenty[num[5][1]]} `) :
            '';

        myDiv.innerHTML = outputText;
        console.log(outputText);
    }

    function reversePurchaseOrderDetailAfterApproval(paramOne, paramTwo, paramThree) {
        var pageType = '<?php echo $pageType; ?>';
        var parentCode = '<?php echo $parentCode; ?>';
        var initialEmailAddress = $('#initialEmailAddress').val();
        var purchaseOrderDate = $('#purchaseOrderDate').val();
        var poVoucherRemarks = $('#poVoucherRemarks').val();
        var locationId = $('#locationId').val();
        $.ajax({
            url: '<?php echo url('/'); ?>/std/reversePurchaseOrderDetailAfterApproval',
            type: "GET",
            data: {
                m: paramOne,
                poNo: paramTwo,
                pageType: pageType,
                parentCode: parentCode,
                prNo: paramThree,
                initialEmailAddress: initialEmailAddress,
                purchaseOrderDate: purchaseOrderDate,
                poVoucherRemarks: poVoucherRemarks,
                locationId: locationId
            },
            success: function(data) {
                if (data == 'Done') {
                    $('#showDetailModelOneParamerter').modal('toggle');
                    viewRangeWiseDataFilter();
                } else {
                    alert(removeTags(data));
                }
            }
        });
    }


    function reversePurchaseOrderDetailBeforeApproval(paramOne, paramTwo, paramThree) {
        var pageType = '<?php echo $pageType; ?>';
        var parentCode = '<?php echo $parentCode; ?>';
        var initialEmailAddress = $('#initialEmailAddress').val();
        var purchaseOrderDate = $('#purchaseOrderDate').val();
        var poVoucherRemarks = $('#poVoucherRemarks').val();
        var locationId = $('#locationId').val();
        $.ajax({
            url: '<?php echo url('/'); ?>/std/reversePurchaseOrderDetailBeforeApproval',
            type: "GET",
            data: {
                m: paramOne,
                poNo: paramTwo,
                pageType: pageType,
                parentCode: parentCode,
                prNo: paramThree,
                initialEmailAddress: initialEmailAddress,
                purchaseOrderDate: purchaseOrderDate,
                poVoucherRemarks: poVoucherRemarks,
                locationId: locationId
            },
            success: function(data) {
                if (data == 'Done') {
                    $('#showDetailModelOneParamerter').modal('toggle');
                    viewRangeWiseDataFilter();
                } else {
                    alert(removeTags(data));
                }
            }
        });
    }
</script>
