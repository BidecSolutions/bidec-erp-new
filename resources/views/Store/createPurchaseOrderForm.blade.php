<?php
    $accType = Auth::user()->acc_type;
    $currentDate = date('Y-m-d');
    $m;

?>
@extends('layouts.default')

@section('content')
    <script src="{{ URL::asset('assets/select2/select2.full.min.js') }}"></script>
    <link href="{{ URL::asset('assets/select2/select2.css') }}" rel="stylesheet">
    
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
                            <?php echo Form::open(array('url' => 'stad/addPurchaseOrderDetail?m='.$m.'','id'=>'addPurchaseOrderDetail'));?>
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="pageType" value="<?php echo Input::get('pageType')?>">
                            <input type="hidden" name="parentCode" value="<?php echo Input::get('parentCode')?>">
                            <input type="hidden" name="tax_acc_code" value="<?php echo Input::get('parentCode')?>">   
                            <input type="hidden" name="po_type" value="pr">                         
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="panel">
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                <label class="sf-label">Purchae Request Detail</label>
                                                <span class="rflabelsteric"><strong>*</strong></span>
                                                <select class="form-control requiredField" required name="purchase_request_no" id="purchase_request_no" onchange="loadPurchaseOrderDetailByPRNo()">
                                                    <option value="">Select Purchae Request Detail</option>
                                                    <?php foreach($PurchaseRequestDatas as $row){?>
                                                        <option value="<?php echo $row->purchase_request_no.'<*>'.$row->purchase_request_date?>"><?php echo 'PR No => &nbsp;&nbsp;&nbsp;'.$row->purchase_request_no.'&nbsp;, PR Date => &nbsp;&nbsp;&nbsp;'.CommonFacades::changeDateFormat($row->purchase_request_date).' , Created By => &nbsp;&nbsp;&nbsp;'.$row->username.' , Location => &nbsp;&nbsp;&nbsp;'.$row->location_name .' , Department / Sub Department => &nbsp;&nbsp;&nbsp;'.$row->department_name.' / '.$row->sub_department_name?></option>
                                                    <?php }?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="lineHeight">&nbsp;</div>
                                        <div class="loadPurchaseOrderDetailSection"></div>
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
            calculateGrandTotalAmount();
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
            //console.log(tax_percent, tax_acc_code);
            var makePercentage = (tax_percent*totalAmount) / 100;
            var tax_percent_amount = makePercentage;
            totalAmount = parseFloat(totalAmount) + parseFloat(tax_percent_amount);
            console.log(totalAmount, tax_percent_amount);
            //alert(makePercentage);
            $('#tax_amount').html(makePercentage);        
            $('#tax_percent').val(tax_percent);
            $('#sales_tax_acc_code').val(tax_acc_code);
            $('#net_amount').html(totalAmount);
            $('#grand_amount').html(totalAmount);
            calculateGrandTotalAmount();
        }
        function calculateGrandTotalAmount(){
            var netAmount = $('#net_amount').text();
            var poDiscountAmount = $('#po_discount').val();
            var grandTotal = Number(netAmount) - Number(poDiscountAmount);
            $('#grand_amount').html(grandTotal);
        }
    </script>
    
@endsection