<?php
$accType = Auth::user()->acc_type;
$currentDate = date('Y-m-d');

if($accType == 'client'){
    $m = getSessionCompanyId();
}else{
    // $m = Auth::user()->company_id;
    $m = getSessionCompanyId();
}
use App\Helpers\PurchaseHelper;
use App\Helpers\CommonHelper;

function clean($string) {
   $string = str_replace(' ', '', $string); // Replaces all spaces with hyphens.
   $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.

   return preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
}
$all_items = Cache::remember('get_all_subitem_by_demand_type_'.$m.'', '320',function(){
    $all_items = '';
    foreach (CommonHelper::get_all_subitem_by_demand_type(1) as $key => $value){
        
        $uom_name = $value->uomData->uom_name ?? 'pcs';
        $all_items .= '<option value="'.$value->id.'" data-uom="'.$uom_name.'">';
        $item_code = $value->item_code ?? '';
        $item_name = $value->sub_ic . '-' . $item_code;
        $all_items .= clean($item_name);
        $all_items .= '</option>';
        
    };
    return $all_items;
});
?>
@extends('layouts.default')

@section('content')
    @include('select2')
    @include('modal')
    <div class="container-fluid"> 
        <div class="well_N">
        <div class="dp_sdw">    
            <div class="panel">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12" style="display: none;">
                            {{-- @include('Purchase.'.$accType.'purchaseMenu') --}}
                        </div>
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <span class="subHeadingLabelClass">Stock Transfer Form</span>
                                    </div>
                                </div>
                                <div class="lineHeight">&nbsp;</div>
                                <div class="row">
                                    <?php echo Form::open(array('url' => 'store/addStockTransfer?m='.$m.'','id'=>'addPurchaseReturnDetail','class'=>'stop'));?>
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <input type="hidden" name="pageType" value="<?php echo $_GET['pageType']?>">
                                    <input type="hidden" name="parentCode" value="<?php echo $_GET['parentCode']?>">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

                                                <div class="row">
                                                    <?php $uniq=PurchaseHelper::get_unique_no_transfer(date('y'),date('m')) ?>
                                                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                                        <label for="">Transfer No</label>
                                                        <input type="text" id="tr_no" name="tr_date" value="{{strtoupper($uniq)}}" class="form-control requiredField" readonly>
                                                    </div>
                                                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                                        <label for="">Transfer Date</label>
                                                        <input type="date" class="form-control requiredField" id="tr_date" name="tr_date" value="<?php echo date('Y-m-d')?>">
                                                    </div>
                                                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                                                        <label for="">Remarks</label>
                                                        <textarea type="text" name="description" id="description" class="form-control requiredField"></textarea>
                                                    </div>
                                                </div>
                                                <div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">&nbsp;</div></div>
                                                <div class="row">
                                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                        <div class="table-responsive">
                                                            <table class="table table-bordered sf-table-list">
                                                                <thead>
                                                                <tr class="text-center">
                                                                    <th colspan="5" class="text-center">Stock Transfer Detail</th>
                                                                    <th colspan="1" class="text-center">
                                                                        <button type="button" class="btn btn-xs btn-primary" id="BtnAddMore" onclick="AddMoreRows()">Add More</button>
                                                                    </th>
                                                                    <th class="text-center">
                                                                        <span class="badge badge-success" id="span">1</span>
                                                                    </th>
                                                                </tr>
                                                                <tr>
                                                                    <th class="text-center" style="width: 30%">Item Name</th>
                                                                    <th class="text-center" style="width: 180px;">Location From</th>
                                                                    <th class="text-center">In Stock Qty</th>
                                                                    <th class="text-center">Transfer Qty</th>
                                                                    <th style="display: none" class="text-center">Rate</th>
                                                                    <th style="display: none" class="text-center">Amount</th>
                                                                    <th class="text-center" style="width: 180px;">Location To</th>
                                                                    <th style="" class="text-center">Desc</th>
                                                                    <th class="text-center">-</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody id="AppendHtml">
                                                                <tr class="text-center AutoNo">
                                                                    <td>                                                                        
                                                                        <select class="form-control select2" name="item_id[]" id="item_id1">
                                                                            <option>Select Product</option>
                                                                            {!! $all_items !!}
                                                                        </select>
                                                                    </td>
                                                                    <td>
                                                                        <select onchange="get_stock(this.id,'1')" name="warehouse_from[]" id="warehouse_from1" class="form-control requiredField" style="width: 180px;">
                                                                            {!! SelectListFacades::getLocationList($m,1,0) !!}
                                                                        </select>
                                                                        
                                                                    </td>

                                                                    <td>
                                                                        <input type="text" name="in_stock_qty[]" id="in_stock_qty1" class="form-control requiredField" readonly>
                                                                    </td>
                                                                    <td>
                                                                        <input onkeyup="check_qty(this.id,1)" type="text" name="qty[]" id="qty1" class="form-control requiredField SendQty" step="any" min>
                                                                    </td>
                                                                    <td style="display: none">
                                                                        <input  readonly type="number" name="rate[]" id="rate1" class="form-control">
                                                                    </td>
                                                                    <td style="display: none">
                                                                        <input readonly type="number" name="amount[]" id="amount1" class="form-control">
                                                                    </td>
                                                                    <td>
                                                                        <select name="warehouse_to[]" id="warehouse_to1" class="form-control requiredField" style="width: 180px;">
                                                                            {!! SelectListFacades::getLocationList($m,1,0) !!}
                                                                        </select>
                                                                    </td>
                                                                    <td><textarea name="des[]"></textarea></td>
                                                                    <td>-</td>
                                                                </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>

                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">

                                            <button type="submit" id="" class="btn btn-success">Submit</button>
                                            <button type="reset" id="reset" class="btn btn-danger">Clear Form</button>
                                        </div>
                                    </div>
                                    <?php echo Form::close();?>
                                </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>    
    <script>
        var Counter = 1;
        function AddMoreRows()
        {
            Counter++;
            $('#AppendHtml').append('<tr class="text-center AutoNo" id="RemoveRow'+Counter+'">' +
                    '<td>' +
                    '<select class="form-control select2" name="item_id[]" id="item_id'+Counter+'">'+
                    '{!! $all_items !!}'+
                    '</select>'+
                    '</td>' +
                    '<td>' +
                    '<select onchange="get_stock(this.id,'+Counter+')" name="warehouse_from[]" id="warehouse_from'+Counter+'" class="form-control" style="width: 180px;">' +
                    // '<option value="">Select Warehouse</option>'+
                    '{!! SelectListFacades::getLocationList($m,1,0) !!}'+
                    
                    '</select>' +                    
                    '</td>' +
                    '<td>' +
                    '<input type="text" name="in_stock_qty[]" id="in_stock_qty'+Counter+'" class="form-control requiredField" readonly>' +
                    '</td>' +
                    '<td>' +
                    '<input onkeyup="check_qty(this.id,'+Counter+')" type="text" name="qty[]" id="qty'+Counter+'" class="form-control requiredField SendQty">' +
                    '</td>' +
                    '<td style="display: none">' +
                    '<input readonly type="number" name="rate[]" id="rate'+Counter+'" class="form-control">' +
                    '</td>' +
                    '<td style="display: none">' +
                    '<input readonly type="number" name="amount[]" id="amount'+Counter+'" class="form-control">' +
                    '</td>' +
                    '<td>' +
                    '<select name="warehouse_to[]" id="warehouse_to'+Counter+'" class="form-control requiredField" style="width: 180px;">' +
                    // '<option value="">Select Warehouse</option>'+
                    '{!! SelectListFacades::getLocationList($m,1,0) !!}'+
                   
                    '</select>' +
                    '</td>' +
                    '<td><textarea name="des[]"></textarea></td>'+
                    '<td>' +
                    '<button type="button" class="btn btn-xs btn-danger" id="BtnRemove'+Counter+'" onclick="RemoveRows('+Counter+')">-</button>' +
                    '</td>'+
                    '</tr>');
                    $('.select2').select2();
            var AutoNo = $(".AutoNo").length;
            $('#span').text(AutoNo);


            $('.sam_jass').bind("enterKey",function(e){


                $('#items').modal('show');
                e.preventDefault();

            });
            $('.sam_jass').keyup(function(e){
                if(e.keyCode == 13)
                {
                    selected_id=this.id;
                    $(this).trigger("enterKey");
                    e.preventDefault();

                }

            });
            $('.SendQtyy').on('keypress', function (event) {
                var regex = new RegExp("^[a-zA-Z0-9]+$");
                var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
                if (!regex.test(key)) {
                    event.preventDefault();
                    return false;
                }
            });
        }
        $('.SendQtyy').on('keypress', function (event) {
            var regex = new RegExp("^[a-zA-Z0-9]+$");
            var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
            if (!regex.test(key)) {
                event.preventDefault();
                return false;
            }
        });

        function RemoveRows(Rows)
        {
            $('#RemoveRow'+Rows).remove();
            var AutoNo = $(".AutoNo").length;
            $('#span').text(AutoNo);
        }
        $(document).ready(function(){
            $('#supplier').select2();
        });

        function getGrnNoBySupplier() {

            $('.loadGoodsReceiptNoteDetailSection').html('');
            var supplier_id=$('#supplier').val();
            $.ajax({
                url: '<?php echo url('/')?>/pmfal/getGrnNoBySupplier',
                type: "GET",
                data: { supplier_id:supplier_id},
                success:function(data)
                {
                    $('#grn_no').html(data);
                    $('#grn_no').select2();
                }
            });
        }

        function loadGoodsReceiptNoteDetailByGrnNo(){


            var GrnNo = $('#grn_no').val();
            var m = '<?php echo getSessionCompanyId() ?>';
            if(GrnNo == ''){
                alert('Please Select Purchase Request No');
                $('.loadGoodsReceiptNoteDetailSection').html('');
            }else{
                $('.loadGoodsReceiptNoteDetailSection').html('<div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div></div>');
                $.ajax({
                    url: '<?php echo url('/')?>/pmfal/makeFormGoodsReceiptNoteDetailByGrnNo',
                    type: "GET",
                    data: { GrnNo:GrnNo,m:m},
                    success:function(data) {
                        $('.loadGoodsReceiptNoteDetailSection').html(data);
                    }
                });
            }
        }


        $( "form" ).submit(function( event ) {
            var validate=validatee();
           
            if (validate==true)
            {

            }
            else
            {
                return false;
            }

        });
        $('.stop').on('keyup keypress', function(e) {
            var keyCode = e.keyCode || e.which;
            if (keyCode === 13) {
                e.preventDefault();
                return false;
            }
        });
        function validatee()
        {
            var validate=true;
            $( ".amount" ).each(function() {
                var id=this.id;
                if($('#'+id).prop("checked") == true)
                {

                    id=id.replace('enable_disable_','');
                    var amount=$('#return_qty_'+id).val();

                    if (amount <= 0 || amount=='')
                    {
                        $('#return_qty_'+id).css('border', '3px solid red');

                        validate=false;
                    }
                    else
                    {
                        $('#return_qty_'+id).css('border', '');

                        if ($('#Remarks').val()=='')
                        {
                            $('#Remarks').css('border', '3px solid red');

                            validate=false;
                        }
                    }



                }

            });
            return validate;
        }


        $('.sam_jass').bind("enterKey",function(e){


            $('#items').modal('show');
            e.preventDefault();

        });
        $('.sam_jass').keyup(function(e){
            if(e.keyCode == 13)
            {
                selected_id=this.id;
                $(this).trigger("enterKey");
                e.preventDefault();

            }

        });


        function get_stock(warehouse,number)
        {
            $('#in_stock_qty'+number).val(0);

            $('#warehouse_to'+number+'').val('');
            $('#warehouse_to'+number+' option').prop('disabled', false);
            var warehouse=$('#'+warehouse).val();
            var item=$('#item_id'+number).val();

            $.ajax({
                url: '<?php echo url('/')?>/pdc/get_stock_location_wise',
                type: "GET",
                data: {warehouse:warehouse,item:item},
                success:function(data)
                {   
                    console.log(data);
                    $('#in_stock_qty'+number).val(data);           
                    $('#warehouse_to'+number+' option[value="'+warehouse+'"]').prop('disabled', true)
                    check_qty('qty'+number,number);
                }
            });

        }


        function get_stock_qty(warehouse,number)
        {


            var warehouse=$('#warehouse_from'+number).val();
            var item=$('#sub_'+number).val();
            var batch_code=$('#batch_code'+number).val();


            $.ajax({
                url: '<?php echo url('/')?>/pdc/get_stock_location_wise?batch_code='+batch_code,
                type: "GET",
                data: {warehouse:warehouse,item:item},
                success:function(data)
                {

                    //   $('#batch_code'+number).html(data);

                    data=data.split('/');
                    $('#in_stock_qty'+number).val(data[0]);
                       $('#rate'+number).val(data[1]);
                    //     var amount=data[0]*data[1];
                    //     $('#net_amount'+number).val(amount);
                    if (data[0]==0)
                    {
                        $("#"+item).css("background-color", "red");
                    }
                    else
                    {
                        $("#"+item).css("background-color", "");
                    }

                }
            });

        }
        function check_qty(id,number)
        {
            var qty=parseFloat($('#'+id).val());
            var instock=parseFloat($('#in_stock_qty'+number).val());

            if (qty>instock)
            {
                alert('Transferd QTY can not greater than actual qty');
                $('#'+id).val(0);
                $('#amount'+number).val(0);
            }
            else
            {
                var rate = parseFloat( $('#rate'+number).val());
                var total=(qty*rate).toFixed(2)
                $('#amount'+number).val(total);
            }
        }


        $(function() {



            $(".btn-success").click(function(e){
                var purchaseRequest = new Array();
                var val;
                //$("input[name='demandsSection[]']").each(function(){
                purchaseRequest.push($(this).val());
                //});
                var _token = $("input[name='_token']").val();
                for (val of purchaseRequest) {
                    jqueryValidationCustom();
                    if(validate == 0){

                        vala = 0;
                        var flag = false;
                        $('.SendQty').each(function(){
                            vala = parseFloat($(this).val());
                            if(vala == 0)
                            {
                                alert('Please Enter Correct Transfer Qty....!');
                                $(this).css('border-color','red');
                                flag = true;
                                return false;
                            }
                            else{
                                $(this).css('border-color','#ccc');
                            }
                        });
                        if(flag == true)
                        {return false;}

                    }
                    else
                    {
                        return false;
                    }
                }

            });
        });
    </script>    
    <script type="text/javascript">
        $('.select2').select2();
    </script>

@endsection