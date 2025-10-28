<?php
    $accType = Auth::user()->acc_type;
    $currentDate = date('Y-m-d');
    $m = getSessionCompanyId();

    $expenseAccountHtml = '';
    foreach ($accounts as $key => $y) {        
        $expenseAccountHtml .= '<option value="'.$y->id.'">'. $y->code .' ---- '. str_replace("'", "", $y->name).'</option>';
    }
?>
@extends('layouts.default')

@section('content')
    <script src="{{ URL::asset('assets/select2/select2.full.min.js') }}"></script>
    <link href="{{ URL::asset('assets/select2/select2.css') }}" rel="stylesheet">
    <script>
        function updateOverAllDebitAmount(){
            var sum = 0;
            $("input[class *= 'yesSubTotalAmount']").each(function(){
                sum += +$(this).val();
            });
            $('#pv_debit_amount').val(sum);
        }
        
        function calculateTotalTaxHeadAmount(){
            var sum = 0;
            $("input[class *= 'yesTaxHeadAmount']").each(function(){
                sum += +$(this).val();
            });
            $('#overAllTaxAmount').val(sum);
        }
        
        function optionEnableAndDisableTaxHeadSlap(paramOne){
            var taxHeadOption = $('#tax_head_option_'+paramOne+'').val();
            if(taxHeadOption == 1){
                $('#tax_head_amount_'+paramOne+'').addClass('yesTaxHeadAmount');
                $('#tax_head_amount_'+paramOne+'').prop("readonly", false);
            }else{
                $('#tax_head_amount_'+paramOne+'').removeClass('yesTaxHeadAmount');
                $('#tax_head_amount_'+paramOne+'').val('0');
                $('#tax_head_amount_'+paramOne+'').prop("readonly", true);
            }
            calculateTotalTaxHeadAmount();
        }
        
        function optionEnableAndDisablePurchaseOrderRequestRegionWise(paramOne,paramTwo,paramThree){
            var generatePurchaseOrderType = $('#generate_purchase_order_type_'+paramOne+'_'+paramThree+'').val();
            var countYesValue = $('#countYesValue_'+paramOne+'').val();
            if(generatePurchaseOrderType == 1){
                $('#countYesValue_'+paramOne+'').val(parseInt(countYesValue) + parseInt('1'));
                $('#generate_purchase_order_type_'+paramOne+'_'+paramThree+'').addClass('yesOption_'+paramOne+'');
                $('#purchase_order_qty_'+paramOne+'_'+paramThree+'').val('1');
                $('#unit_price_'+paramOne+'_'+paramThree+'').val('1');
                $('#sub_total_'+paramOne+'_'+paramThree+'').val('1');
                $('#sub_total_with_persent_'+paramOne+'_'+paramThree+'').val('0');
            }else{
                $('#countYesValue_'+paramOne+'').val(parseInt(countYesValue) - parseInt('1'));
                $('#generate_purchase_order_type_'+paramOne+'_'+paramThree+'').removeClass('yesOption_'+paramOne+'');
                $('#purchase_order_qty_'+paramOne+'_'+paramThree+'').val('0');
                $('#unit_price_'+paramOne+'_'+paramThree+'').val('0');
                $('#sub_total_'+paramOne+'_'+paramThree+'').val('0');
                $('#sub_total_with_persent_'+paramOne+'_'+paramThree+'').val('0');
            }
            var countYesValueTwo = $('#countYesValue_'+paramOne+'').val();
            if(countYesValueTwo == 1){
                $('.yesOption_'+paramOne+'').prop("disabled", true);
                updateOverAllDebitAmount();
            }else{
                $('.yesOption_'+paramOne+'').prop("disabled", false);
                updateOverAllDebitAmount();
            }
        }
        
        
        
        function calculateTaxHeadPercentageAndAmount(paramOne,paramTwo){
            var pvDebitAmount = $('#pv_debit_amount').val();
            if(pvDebitAmount == '0'){
                alert('Something Wrong!');
            }else if(pvDebitAmount == ''){
                alert('Something Wrong!');
            }else{
                var taxHeadPercentage = $('#tax_head_percentage_'+paramOne+'').val();
                var taxHeadAmount = $('#tax_head_amount_'+paramOne+'').val();
                if(paramTwo == 1){
                    //Convert our percentage value into a decimal.
                    var percentInDecimal = parseInt(taxHeadPercentage) / 100;
                    //Get the result.
                    var percentAmount = percentInDecimal * pvDebitAmount;
                    //Print it out - Result is 232.
                    $('#tax_head_amount_'+paramOne+'').val(percentAmount);
                }else if(paramTwo == 2){
                    //Convert our percentage value into a decimal.
                    var percentInDecimal = parseInt(taxHeadAmount) / pvDebitAmount;
                    //Get the result.
                    var percent = percentInDecimal * 100;
                    //Print it out - Result is 232.
                    $('#tax_head_percentage_'+paramOne+'').val(percent);
                }
            }
        }
    </script>

    <div class="well_N">
	    <div class="boking-wrp dp_sdw">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="well">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <span class="subHeadingLabelClass">Create Purchase Order Form</span>
                            </div>
                        </div>
                        <div class="lineHeight">&nbsp;</div>
                        <div class="row">
                            <?php echo Form::open(array('url' => 'stad/addPurchaseOrderDetailDirect?m='.$m.'','id'=>'addPurchaseOrderDetailDirect'));?>
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="pageType" value="<?php echo Input::get('pageType')?>">
                            <input type="hidden" name="parentCode" value="<?php echo Input::get('parentCode')?>">
                            <input type="hidden" name="tax_acc_code" value="<?php echo Input::get('parentCode')?>">  
                            <input type="hidden" name="po_type" value="direct">                          
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="panel">
                                    <div class="panel-body">
                                        
                                        <div class="lineHeight">&nbsp;</div>
                                        <div class="">
                                            <div>                                                
                                                <input type="hidden" id="tax_percent" name="tax_percent" value="">
                                                <input type="hidden" id="sales_tax_acc_code" name="sales_tax_acc_code" value="">
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                                    <label class="sf-label">Location</label>
                                                    <span class="rflabelsteric"><strong>*</strong></span>                                                    
                                                    <span class="rflabelsteric"><strong>*</strong></span>
                                                    <select name="locationId" id="locationId" class="form-control requiredField" required>
                                                        <?php echo SelectListFacades::getLocationList($m,1,0);?>
                                                    </select>
                                                </div>
                                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                                    <label class="sf-label">P.O Date.</label>
                                                    <span class="rflabelsteric"><strong>*</strong></span>
                                                    <input type="text" class="form-control requiredField purchaseRequestDataPicker" name="po_date" id="po_date" readonly value="<?php echo $formDateValue ?>" />
                                                </div>
                                                <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
                                                    <label class="sf-label">Department / Sub Department</label>
                                                    <span class="rflabelsteric"><strong>*</strong></span>
                                                    <select class="form-control requiredField" name="sub_department_id_1" id="sub_department_id_1">
                                                        <option value="">Select Department</option>
                                                        @foreach($departments as $key => $y)
                                                            <optgroup label="{{ $y->department_name}}" value="{{ $y->id}}">
                                                                <?php
                                                                    $departmentId = $y->id;
                                                                    $subdepartments = Cache::rememberForever('cacheSubDepartment_'.$m.'',function() use ($m){
                                                                        return DB::select("select * from sub_department where company_id = ".$m."");
                                                                    });
                                                                ?>
                                                                @foreach($subdepartments as $key2 => $y2)
                                                                    <?php
                                                                        if($y2->department_id == $departmentId){ 
                                                                    ?>
                                                                            <option value="{{ $y2->id.'<*>'.$y->id}}">{{ $y2->sub_department_name}}</option>
                                                                    <?php
                                                                        }
                                                                    ?>
                                                                @endforeach
                                                            </optgroup>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                                    <label class="sf-label">Project</label>
                                                    <span class="rflabelsteric"><strong>*</strong></span>
                                                    <select name="projectId" id="projectId" class="form-control requiredField" required>
                                                        <?php echo SelectListFacades::getProjectList($m,1,0);?>
                                                    </select>
                                                </div>
                                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                                    <label class="sf-label">Delivery place</label>
                                                    <span class="rflabelsteric"><strong>*</strong></span>
                                                    <input type="text" class="form-control" name="delivery_place" id="delivery_place" placeholder="Delivery Place" value="Factory" />
                                                </div>
                                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                                    <label class="sf-label">Invoice/Quotation No.</label>
                                                    <span class="rflabelsteric"><strong>*</strong></span>
                                                    <input type="text" class="form-control requiredField" name="qoutation_no" id="qoutation_no" placeholder="Invoice/Quotation No." value="" />
                                                </div>
                                                <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
                                                    <label class="sf-label">Remarks</label>
                                                    <span class="rflabelsteric"><strong>*</strong></span>
                                                    <textarea name="main_description" id="main_description" rows="2" cols="50" style="resize:none;" class="form-control">-</textarea>
                                                </div>
                                                {{-- <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
                                                    <label class="sf-label">Term & Condition</label>
                                                    <span class="rflabelsteric"><strong>*</strong></span>
                                                    <textarea  rows="2" cols="50" style="resize:none;" class="form-control">-Purchase Order should not be accepted if any alterations have been made to the date,quantity,rate, description or name of the Supplier.
                                                         Payment will be made  in advance In same account tital as per invoice mentioned. Defective material shall not be accepted & will be replaced at vendor cost
                                                    </textarea>
                                                </div> --}}
                                                <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
                                                    <label class="sf-label">TOS Advance %</label>        
                                                    {{-- <input class="form-control" id="termCondition" name="termCondition" type="number" /> --}}
                                                    <select class="form-control" name="termCondition" id="termCondition">
                                                        <option value="0">0</option>
                                                        <option value="10">10</option>
                                                        <option value="20">20</option>
                                                        <option value="30">30</option>
                                                        <option value="40">40</option>
                                                        <option value="50">50</option>
                                                        <option value="60">60</option>
                                                        <option value="70">70</option>
                                                        <option value="80">80</option>
                                                        <option value="90">90</option>
                                                        <option value="100">100</option>
                                                    </select>
                                                </div>
                                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                                    <?php  $paymentTypeList = DB::connection('tenant')->table('payment_type')->get(); ?>
                                                    <label class="sf-label">Payment Type</label>
                                                    <select class="form-control" name="paymentTypeTwo" id="paymentTypeTwo" onchange="touglePurchaseOrderPaymentRate()">
                                                    @foreach($paymentTypeList as $ptlRow)
                                                        <option value="{{$ptlRow->id}}<*>{{$ptlRow->conversion_rate_type}}<*>{{$ptlRow->conversion_rate}}" >{{$ptlRow->payment_type_name}}</option>
                                                    @endforeach
                                                    </select>
                                                    <input type="hidden" name="paymentType" id="paymentType" value="" />
                                                </div>
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                    <label class="sf-label">Payment Type Rate</label>
                                                    <input type="number" readonly name="payment_type_rate" id="payment_type_rate" step="0.001" value="1" class="form-control" />
                                                </div>
                                            </div>
                                            <div class="lineHeight">&nbsp;</div>
                                            <div class="row">
                                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                    <label class="sf-label">Note</label>
                                                    <span class="rflabelsteric"><strong>*</strong></span>
                                                    <textarea name="po_note" id="po_note" rows="2" cols="50" style="resize:none;" class="form-control">-</textarea>
                                                </div>
                                            </div>
                                            <div class="lineHeight">&nbsp;</div>
                                            <div class="row">
                                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered sf-table-list" id="purchaseOrderTable">
                                                            <input type="hidden" name="totalGPRDDRow" id="totalGPRDDRow" value="1" />                                                            
                                                                    
                                                                    <thead>
                                                                        <tr>
                                                                            <th class="text-center hidden"><label><input type="checkbox" name="sale_tax_head_checkbox_1" id="sale_tax_head_checkbox_1" value="2" onchange="saleTaxEnableAndDisable('1')"> Sales Tax Head</label></th>
                                                                            <th class="text-center hidden">Tax Unit</th>
                                                                            <th class="text-center">Category</th>
                                                                            <th colspan="2" class="text-center">Item Code / Item Name</th>
                                                                            <th class="text-center">Supplier Name</th>
                                                                            
                                                                            <th class="text-center" colspan="2">Qoutation Date</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody class="displayRowPRow_1">
                                                                        <tr>
                                                                            <td class="text-center hidden">
                                                                                <input type="hidden" name="seletedPurchaseRequestRow[]" readonly id="seletedPurchaseRequestRow" value="1" class="form-control" />
                                                                                <input type="number" class="form-control requiredField" name="delivery_days_1_1" id="delivery_days_1_1" value="30" />
                                                                                <input type="number" class="form-control requiredField" placeholder="Payment Terms Days" name="payment_terms_1_1" id="payment_terms_1_1" value="45" />
                                                                                
                                                                                <select name="sale_tax_head_1" id="sale_tax_head_1" class="form-control">
                                                                                    <?php echo SelectListFacades::getChartOfAccountList($m,1,0,0);?>
                                                                                </select>
                                                                            </td>
                                                                            <td class="text-center hidden">
                                                                                <input type="number" name="unit_1_1" id="unit_1_1" step="0.00001" placeholder="Type Unit Percent" class="form-control requiredField" onkeyup="makesubtotalamount(1,0)" value="0">
                                                                            </td>
                                                                            <td>
                                                                                <select name="categoryId_1_1" id="categoryId_1_1" onchange="subItemListLoadDepandentCategoryId(this.id,this.value)" class="form-control requiredField">
                                                                                    <?php echo PurchaseFacades::categoryList($m,'0');?>
                                                                                </select>
                                                                            </td>
                                                                            <td colspan="2">
                                                                                <select name="subItemId_1_1" id="subItemId_1_1" class="form-control requiredField">
                                                                                </select>
                                                                                <!-- <input type="text" name="subItemId_1" id="subItemId_1" placeholder="Type Item Name" class="form-control requiredField"> -->
                                                                            </td>
                                                                            <td>
                                                                                <select onchange="makesubtotalamount(1,1)" class="form-control requiredField" name="supplier_id_1_1" id="supplier_id_1_1">
                                                                                    <?php echo SelectListFacades::getSupplierList($m,1,0);?>
                                                                                </select>
                                                                            </td>
                                                                            
                                                                            <td colspan="2">
                                                                                <input type="date" class="form-control requiredField" name="qoutation_date_1_1" id="qoutation_date_1_1" value="<?php echo date('Y-m-d') ?>" placeholder="Qoutation No" />
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                    <thead>
                                                                        <tr>
                                                                            <th class="text-center">Invoice/Quotation No.</th>
                                                                            <th class="text-center">Purchase Order Qty./Previous Purchased Qty.</th>
                                                                            <th class="text-center">Unit Price</th>
                                                                            <th class="text-center">Sub Total <span class="rflabelsteric"><strong>*</strong></span></th>
                                                                            <th class="text-center hidden" colspan="2">Sub Total With % <span class="rflabelsteric"><strong>*</strong></span></th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody class="displayRowPRow_1">
                                                                        <tr>
                                                                            <td>
                                                                                <input type="text" class="form-control requiredField" name="qoutation_no_1_1" id="qoutation_no_1_1" value="-" placeholder="Qoutation No" />
                                                                            </td>
                                                                            <td class="text-center" style="display: flex; justify-content: space-around;">
                                            
                                                                                <input type="number" style="width: 45%;" name="purchase_order_qty_1_1" id="purchase_order_qty_1_1" step="0.00001" placeholder="Type Purchase Order Qty" class="form-control requiredField" onchange="makesubtotalamount(1,1)" max="<?php //echo $remainingPurchaseOrderQty;?>" value="">                                                                                
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <input type="number" name="unit_price_1_1" id="unit_price_1_1" step="0.00001" placeholder="Type Unit Price" class="form-control requiredField unit_price" onkeyup="makesubtotalamount(1,1)" value="">
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <input type="number" readonly name="sub_total_1_1" id="sub_total_1_1" step="0.00001" placeholder="Type Sub Total" class="form-control requiredField yesSubTotalAmount" value="">
                                                                            </td>
                                                                            <td class="text-center hidden" colspan="2">
                                                                                <input type="number" readonly name="sub_total_with_persent_1_1" id="sub_total_with_persent_1_1" step="0.00001" placeholder="Type Sub Total With Persent" class="form-control" value="">
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                                                                                        
                                                                    <tr>
                                                                        <td colspan="10">&nbsp;</td>                                                                        
                                                                    </tr>                                                           
                                                        </table>
                                                        <div colspan="1">
                                                            <input type="button" class="btn btn-sm btn-primary" onclick="addMorePurchaseOrdersDetailRows('1')" value="Add More Rows" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 hidden">
                                                    <label>Select Purchase Order Voucher Type</label>
                                                    <select name="purchase_order_voucher_type" id="purchase_order_voucher_type" class="form-control" onchange="optionEnableAndDisablePurchaseOrderVoucherTypeAmountField()">
                                                        <option value="1">Without Payment</option>
                                                        <option value="2">Paid Amount Advanced</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="table-responsive">
                                                            <div class="hidden">
                                                                <label for="">Expense</label>
                                                                <input type="checkbox" name="expense_added" id="expense_added">
                                                            </div>
                                                            <table id="expenseTable" class="table table-bordered d-none">
                                                                <thead>
                                                                <tr>
                                                                    <th class="text-center">Sr No<span class="rflabelsteric"><strong>*</strong></span></th>
                                                                    <th class="text-center">Expense Type<span class="rflabelsteric"><strong>*</strong></span></th>
                                                                    <th class="text-center">Amount<span class="rflabelsteric"><strong>*</strong></span></th>
                                                                    <th class="text-center">
                                                                        <button type="button" class="btn btn-sm btn-primary" id="BtnAddMore" onclick="AddMoreRowsExpense()">Add More</button>
                                                                    </th>
                                                                </tr>
                                                                </thead>
                                                                <tbody id="AppendExpenseHtml">
                                                                    <tr class="text-center">
                                                                        <td>1</td>
                                                                        <td>
                                                                            <select style="width: 150px;" name="expense_head_id[]" id="expense_head_id1">
                                                                                <option value="">Select Head</option>
                                                                                {!! $expenseAccountHtml !!}
                                                                            </select>
                                                                        </td>
                                                                        <td>
                                                                            <input type="number" class="form-control text-right GetAmount" id="expense_amount1" name="expense_amount[]" placeholder="Expense Amount" step="any" onkeyup="expensetotal()">
                                                                        </td>
                                                                        <td> - - - </td>
                                                                    </tr>
                                                                </tbody>
                                                                <tbody>
                                                                    <tr>
                                                                        <td colspan="2"><strong style="font-size: 20px">TOTAL</strong></td>
                                                                        <td class="text-right"><strong style="font-size: 20px" id="TotalExpenseAmount"></strong></td>
                                                                        <td style="background-color: darkgray"></td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right pb-2 d-flex justify-content-end" style="display: flex; justify-content:end;">    
                                                            <select onchange="calculateTaxAmount()" name="sales_tax_id" id="sales_tax_id">
                                                                <option value="0,0">No Tax</option>
                                                                @if (getSessionCompanyId() == 1)                                                                    
                                                                    <option value="2-1-1-1-13-1,18">GST 18% Payable</option>                                                                                                                                    
                                                                @endif
                                                                @if (getSessionCompanyId() == 2)                                                                    
                                                                    <option value="2-1-1-1-13-1,5">VAT 5% Payable</option>     
                                                                    <option value="2-1-1-1-13-2,13">13% Payable</option>                                                                                                                                    
                                                                    <option value="2-1-1-1-13-3,18">18% Payable</option>                                                                                                                                   
                                                                @endif                                                                
                                                                <option value="2-1-1-1-13-2,17">GST 17% Payable</option>
                                                                <option value="2-1-1-1-13-3,16">PRA Tax Output (16%)</option>
                                                                <option value="2-1-1-1-13-4,10">SRB Output 10% Payable</option>            
                                                                <option value="2-1-1-1-13-6,13">GST 13% payable</option>            
                                                                <option value="2-1-1-1-13-7,10">GST 10% payable</option>            
                                                                <option value="2-1-1-1-13-8,manual">GST Manual</option>           
                                                            </select>
                                                            <input class="form-control" type="hidden" name="tax_type"  id="tax_type" />
                                                            <input onKeyup="calculateTaxAmount()" class="form-control" style="width:120px;" type="hidden" name="manual_tax" value="0" id="manual_tax" />
                                                        </div>
                                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right pb-2 d-flex justify-content-end" style="display: flex; justify-content:end;">
                                                            <span>
                                                                <h3>
                                                                    Total : 
                                                                </h3>
                                                                
                                                            </span>
                                                            <span>
                                                                <h3 id="total_amount" style="padding-left: 5px;">
                                                                    0
                                                                </h3>
                                                            </span>
                                                        </div>
                                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right pb-2 d-flex justify-content-end" style="display: flex; justify-content:end;">
                                                            <span>
                                                                <h3>
                                                                    Tax Amount : 
                                                                </h3>
                                                                
                                                            </span>
                                                            <span>
                                                                <h3 id="tax_amount" style="padding-left: 5px;">
                                                                    0
                                                                </h3>
                                                            </span>
                                                        </div>
                                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right pb-2 d-flex justify-content-end" style="display: flex; justify-content:end;">
                                                            <span>
                                                                <h3>
                                                                    Grand Total : 
                                                                </h3>
                                                                
                                                            </span>
                                                            <span>
                                                                <h3 id="grand_amount" style="padding-left: 5px;">
                                                                    0
                                                                </h3>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    
                                                </div>
                                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">
                                                    {{ Form::submit('Submit', ['class' => 'btn btn-success btn-add-success btnSubmit','id' => 'submit-btn-abc']) }}
                                                    <button type="reset" id="reset" class="btn btn-primary">Clear Form</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php echo Form::close();?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('custom-js-end')
    <script>
        $(document).on('keyup', '.unit_price', function(){
            var sum = 0; 
            $('.yesSubTotalAmount').each(function() {
                var isDisabled = $(this).prop('disabled');
                console.log(isDisabled);
                if(!isDisabled){
                    sum += Number($(this).val());
                }
            }); 
            $('#total_amount').html(sum);
            calculateTaxAmount();
            //console.log(sum);
        });
        $(function () {                       
            $("select").select2();
        });
        function loadPurchaseOrderDetailByPRNo(){
            var prNo = $('#purchase_request_no').val();
            var m = '<?php echo $m?>';
            var pageType = '<?php echo Input::get('pageType')?>';
            var parentCode = '<?php echo Input::get('parentCode')?>';
            if(prNo == ''){
                alert('Please Select Purchase Request No');
                $('.loadPurchaseOrderDetailSection').html('');
            }else{
                $('.loadPurchaseOrderDetailSection').html('<div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div></div>');
                $.ajax({
                    url: '<?php echo url('/')?>/stmfal/makeFormPurchaseOrderDetailByPRNo',
                    type: "GET",
                    data: { prNo:prNo,pageType:pageType,parentCode:parentCode},
                    success:function(data) {
                        $('.loadPurchaseOrderDetailSection').html(data);
                        //disableInputFormDateAccountYear();
                        $('#submit-btn-abc').prop('disabled', false);
                    }
                });
            }
        }
        function saleTaxEnableAndDisable(paramOne){
            //alert(paramOne);
            if($('input[name="sale_tax_head_checkbox_'+paramOne+'"]').prop("checked") == true){
                //alert("Checkbox is checked.");
                $('#sale_tax_head_checkbox_'+paramOne+'').val('1');
                $('#unit_'+paramOne+'').prop('disabled', false);
                $('#sale_tax_head_'+paramOne+'').prop('disabled', false);
                
                //document.getElementById("name").disabled = true;
            }
            else if($('input[name="sale_tax_head_checkbox_'+paramOne+'"]').prop("checked") == false){
                //alert("Checkbox is unchecked.");
                $('#sale_tax_head_checkbox_'+paramOne+'').val('2');
                $('#unit_'+paramOne+'').val('0');
                $('#sub_total_with_persent_'+paramOne+'').val('0');
                $('#unit_'+paramOne+'').prop('disabled', true);
                $('#sale_tax_head_'+paramOne+'').prop('disabled', true);
            }
        }

        function validatePurchaseOrderQtyAgainstPurchaseRequest(paramOne,paramTwo,paramThree){
            var purchaseOrderQty = $('#purchase_order_qty_'+paramOne+'_'+paramTwo+'').val();
            var purchaseRequestQty = paramThree;
            if(parseInt(purchaseOrderQty) > parseInt(purchaseRequestQty)){
            alert('Something Went Wrong! Your Purchase Order Qty is Greater than Remaining Purchase Request Qty...');
                $('#purchase_order_qty_'+paramOne+'_'+paramTwo+'').val(purchaseRequestQty);
                return false;
            }
        }

        function touglePurchaseOrderPaymentRate(){
            var paymentTypeTwo = $('#paymentTypeTwo').val();
            const paymentTypeSplit = paymentTypeTwo.split('<*>');
            var paymentType = $('#paymentType').val(paymentTypeSplit[0]);
            var conversionRateType = paymentTypeSplit[1];
            if(conversionRateType == 2){
                $('#payment_type_rate').removeAttr('readonly');
                $('#payment_type_rate').val(paymentTypeSplit[2]);   
            }else{
                $('#payment_type_rate').val(paymentTypeSplit[2]);
                $('#payment_type_rate').attr('readonly','readonly');
            }
        }

        function calculateTaxAmount(){            
            var totalAmount = $('#total_amount').text();
            var tax_input = $('#sales_tax_id').val();
            var tax_input_array = tax_input.split(',');
            var tax_acc_code = tax_input_array[0];
            var tax_percent = tax_input_array[1];
            if (tax_percent == 'manual' && tax_acc_code=='2-1-1-1-13-8') {
                $('#manual_tax').attr({'type': 'text'});
                $('#tax_type').val('manual');
                tax_percent=$('#manual_tax').val();
            } else {
                $('#manual_tax').val(0);
                $('#tax_type').val('');
                $('#manual_tax').attr({'type': 'hidden'});
            }
            //console.log('calculateTaxAmount');
            var makePercentage = (tax_percent*totalAmount) / 100;
            var tax_percent_amount = makePercentage;
            //alert(makePercentage);
            $('#tax_amount').html(makePercentage);        
            $('#tax_percent').val(tax_percent);
            $('#sales_tax_acc_code').val(tax_acc_code);
            $('#grand_amount').html(parseFloat(totalAmount)+parseFloat(makePercentage));
        }
    </script>
    <script type="text/javascript">
        $(function () {
            $("select").select2();
            $('#expense_added').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#expenseTable').removeClass('d-none');
                } else {
                    $('#expenseTable').addClass('d-none');            
                }
            });
        });
        var counter = 1;
        function AddMoreRowsExpense(){
            counter++;
            $('#AppendExpenseHtml').append('<tr class="text-center AutoNo" id="RemoveRow'+counter+'">' +
                '<td>'+
                counter +
                '</td>'+
                '<td>'+
                '<select style="width: 150px;" name="expense_head_id[]" id="expense_head_id'+counter+'">'+
                '<option value="">Select Head</option>'+
                '{!! $expenseAccountHtml !!}'+
                '</select>'+
                '</td>'+
                '<td>'+
                '<input type="number" class="form-control text-right requiredField GetAmount" id="expense_amount'+counter+'" name="expense_amount[]" placeholder="Expense Amount" step="any" onkeyup="expensetotal()">'+
                '</td>'+
                '<td>'+
                '----'+
                '</td>'+
                '</tr>'
                );       
                $("select").select2(); 
                
        }
        function expensetotal(){
            var total = 0;
            $('.GetAmount').each(function(index, element,) {
            var value = $(element).val();
            total += parseInt(value);
            });
            $('#TotalExpenseAmount').html(total);
        }
        function optionEnableAndDisbaleBankDetail(){
            var pvVoucherType = $('#pv_voucher_type').val();
            if(pvVoucherType == '1'){
                $('#pv_cheque_no').removeClass('requiredField');
                $('#pv_cheque_date').removeClass('requiredField');
                
                $('#pv_cheque_no').prop("disabled", true);
                $('#pv_cheque_date').prop("disabled", true);
                
                $("#pvPaymentVoucherTaxHeadOption :input").attr("disabled", true);
                $("#pvPaymentVoucherTaxHeadOption select").removeClass("requiredField");
                $("#pvPaymentVoucherTaxHeadOption input").removeClass("requiredField");
                
            }else{
                $('#pv_cheque_no').addClass('requiredField');
                $('#pv_cheque_date').addClass('requiredField');
                
                $('#pv_cheque_no').prop("disabled", false);
                $('#pv_cheque_date').prop("disabled", false);
                
                $("#pvPaymentVoucherTaxHeadOption :input").attr("disabled", false);
                $("#pvPaymentVoucherTaxHeadOption select").addClass("requiredField");
                $("#pvPaymentVoucherTaxHeadOption input").addClass("requiredField");
                
                
            }
        }
        function optionEnableAndDisablePurchaseOrderVoucherTypeAmountField(){
            var purchaseOrderVoucherType = $('#purchase_order_voucher_type').val();
            if(purchaseOrderVoucherType == 1){
                $("#pvPaymentVoucherOption :input").attr("disabled", true);
                $("#pvPaymentVoucherOption select").removeClass("requiredField");
                $("#pvPaymentVoucherOption input").removeClass("requiredField");
            }else{
                $("#pvPaymentVoucherOption :input").attr("disabled", false);
                $("#pvPaymentVoucherOption select").addClass("requiredField");
                $("#pvPaymentVoucherOption input").addClass("requiredField");
                optionEnableAndDisbaleBankDetail();
            }
        }
        optionEnableAndDisablePurchaseOrderVoucherTypeAmountField();
        optionEnableAndDisbaleBankDetail();
        function makesubtotalamount(param1,param2){
            var unit = $('#unit_'+param1+'').val();
            var purchase_order_qty = $('#purchase_order_qty_'+param1+'_'+param2+'').val();
            var unit_price = $('#unit_price_'+param1+'_'+param2+'').val();
            console.log(unit, purchase_order_qty, unit_price, 'test');
            if(unit !== '' && purchase_order_qty !== '' && unit_price !== ''){
                //alert('Done');
                var subTotal = purchase_order_qty * unit_price;
                var sub_total_with_persent = parseInt(subTotal) * parseInt(unit) / parseInt('100');
                $('#sub_total_'+param1+'_'+param2+'').val(subTotal);
                
                $('#sub_total_with_persent_'+param1+'_'+param2+'').val(sub_total_with_persent);
                updateOverAllDebitAmount();
                //overalltotalsection();
            }else{
                //alert('Empty Value');
                $('#sub_total_'+param1+'_'+param2+'').val('');
                $('#sub_total_with_persent_'+param1+'_'+param2+'').val('');
                updateOverAllDebitAmount();
                //overalltotalsection();
            }        
        }
    
        $(".btn-add-success").click(function(e){
            var seletedPurchaseRequestRow = new Array();
            var val;
            $("input[name='seletedPurchaseRequestRow[]']").each(function(){
                seletedPurchaseRequestRow.push($(this).val());
            });
            var _token = $("input[name='_token']").val();
            for (val of seletedPurchaseRequestRow) {
                jqueryValidationCustom();
                console.log(validate);
                if(validate == 0){
                    $(".btnSubmit").val('Sending, please wait...');
                    $('.mainOption').prop("disabled", false);
                    setTimeout(function(){
                        $(".btnSubmit").prop("type", "button");
                    },50);
                }else{
                    return false;
                }
            }
    
        });
        $(document).ready(function() {
            var sum = 0; 
                $('.yesSubTotalAmount').each(function() {
                    var isDisabled = $(this).prop('disabled');
                    console.log(isDisabled);
                    if(!isDisabled){
                        sum += Number($(this).val());
                    }
                }); 
                $('#total_amount').html(sum);
                calculateTaxAmount();
                //console.log(sum);
            var startAccountYear = $("#startAccountYearDMYFormat").val();
            var endAccountYear = $("#endAccountYearDMYFormat").val();
            $(".fromDateDatePicker").datepicker({
                showAnim: "slideDown",
                dateFormat: "dd-mm-yy",
                maxDate: endAccountYear,
                minDate: startAccountYear
            });
        });
        function subItemListLoadDepandentCategoryId(id,value) {
            var arr = id.split('_');
            var m = '<?php echo $m;?>';
            $.ajax({
                url: '<?php echo url('/')?>/pmfal/subItemListLoadDepandentCategoryId',
                type: "GET",
                data: { id:id,m:m,value:value},
                success:function(data) {
                    $('#subItemId_'+arr[1]+'_'+arr[2]).html(data);
                }
            });
        }
        var x = 1;
        function addMorePurchaseOrdersDetailRows(id){
            x++;
            var m = '<?php echo $m;?>';
            $.ajax({
                url: '<?php echo url('/')?>/pmfal/addMorePurchaseOrdersDetailRows',
                type: "GET",
                data: { counter:x,id:id,m:m},
                success:function(data) {
                    //alert(data);
                    $('#purchaseOrderTable').append(data);
                }
            });
        }
    </script>
    <script type="text/javascript">
        saleTaxEnableAndDisable('1');
        function optionDisableAndEnable(paramOne){
            var optionValue = $('#option_'+paramOne+'').val();
            var totalGPRDDRow = $('#totalGPRDDRow').val();
            
            if(optionValue == 2){
                if(totalGPRDDRow == 1){
                    alert('Something Wrong! Atleast one item in Purchase Order...');
                    return false;
                }else{
                    var totalGPRDDRow = parseInt(totalGPRDDRow) - parseInt(1);
                    $('.displayRowPRow_'+paramOne+' :input').prop("disabled", true);
                    $('#option_'+paramOne+'').removeClass("optionPurchaseSkip");
                    $('.displayRowPRow_'+paramOne+' :input').removeClass("requiredField");
                    
                }
            }else{
                var totalGPRDDRow = parseInt(totalGPRDDRow) + parseInt(1);
                $('.displayRowPRow_'+paramOne+' :input').prop("disabled", false);
                $('#option_'+paramOne+'').addClass("optionPurchaseSkip");
                $('.displayRowPRow_'+paramOne+' :input').addClass("requiredField");
            }
            $('#totalGPRDDRow').val(totalGPRDDRow);
            $('#option_'+paramOne+'').prop("disabled", false);
            var totalGPRDDRowTwo = $('#totalGPRDDRow').val();
            if(totalGPRDDRowTwo == 1){
                $('.optionPurchaseSkip').prop("disabled", true); 
            }else{
                $('.optionPurchaseSkip').prop("disabled", false);
            }
            var sum = 0;
            $('.yesSubTotalAmount').each(function() {
                var isDisabled = $(this).prop('disabled');
                console.log(isDisabled);
                if(!isDisabled){
                    sum += Number($(this).val());
                }
            }); 
            $('#total_amount').html(sum);
        }
    </script>
    
@endsection