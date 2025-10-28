<?php
    $accType = Auth::user()->acc_type;
    $currentDate = date('Y-m-d');
    $m = getSessionCompanyId();
    use App\Helpers\PurchaseHelper;
    use App\Helpers\CommonHelper;

    function clean($string) {
        $string = str_replace(' ', '', $string); // Replaces all spaces with hyphens.
        $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
        return preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
    }
    $all_items = Cache::remember('get_fg_subitem_by_demand_type_'.$m.'', '320',function(){
        $all_items = '';
        foreach (CommonHelper::get_all_subitem_by_demand_type(3) as $key => $value){
            $uom_name = $value->uomData->uom_name ?? 'pcs';
            $all_items .= '<option value="'.$value->id.'" data-uom="'.$uom_name.'">';
            $item_code = $value->item_code ?? '';
            $item_name = $value->sub_ic . '-' . $item_code;
            $all_items .= clean($item_name);
            $all_items .= '</option>';
        }
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
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <span class="subHeadingLabelClass">Stock Transfer Form</span>
                    </div>
                </div>
                <div class="lineHeight">&nbsp;</div>
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <?php echo Form::open(array('url' => 'store/addStockTransferTwo?m='.$m.'','id'=>'addPurchaseReturnDetail','class'=>'stop'));?>
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="pageType" value="<?php echo $_GET['pageType']?>">
                            <input type="hidden" name="parentCode" value="<?php echo $_GET['parentCode']?>">
                            <input type="hidden" name="voucher_type" value="2">
                            <div class="row">
                                <?php $uniq=PurchaseHelper::get_unique_no_transfer(date('y'),date('m')) ?>
                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                    <label for="">Transfer No</label>
                                    <input type="text" id="tr_no" name="tr_no" value="{{strtoupper($uniq)}}" class="form-control requiredField" readonly>
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
                            <div class="lineHeight">&nbsp;</div>
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
                                                    <th class="text-center" rowspan="2">Item Name</th>
                                                    <th class="text-center" colspan="3" style="width: 40%">IOT Detail</th>                                                                  
                                                    <th style="display: none" rowspan="2" class="text-center">Rate</th>
                                                    <th style="display: none" rowspan="2" class="text-center">Amount</th>
                                                    <th class="text-center" rowspan="2" style="width: 150px;">Location To</th>
                                                    <th style="" rowspan="2" class="text-center">Desc</th>
                                                    <th class="text-center" rowspan="2">Action</th>
                                                </tr>
                                                <tr>
                                                    <th class="text-center" style="width: 20%">IOT No</th>
                                                    <th class="text-center" style="width: 10%">Location From</th>
                                                    <th class="text-center" style="width: 10%">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="AppendHtml">
                                                <tr class="text-center AutoNo">
                                                    <td> 
                                                        <input type="hidden" name="trasnferArray[]" id="trasnferArray[]" value="1" />                                                                       
                                                        <select class="form-control select2 item_id" name="item_id_1" id="item_id_1" onchange="loadIOTDetail(1)">
                                                            <option>Select Product</option>
                                                            {!! $all_items !!}
                                                        </select>
                                                    </td>
                                                    <td colspan="3">
                                                        <table class="table table-bordered table-striped table-condensed">
                                                            <tbody id="sub_detail_1">
                                                                <tr>
                                                                    <td  style="width: 20%">
                                                                        <input type="hidden" name="trasnferDataArray_1[]" id="trasnferDataArray_1[]" value="1" />                                                                    
                                                                        <select class="form-control select2 iot_1" name="iot_1_1" id="iot_1_1" onchange="getLocationDetail(1,1)">
                                                                            <option>IOT</option>
                                                                        </select>
                                                                    </td>
                                                                    <td style="width: 10%">
                                                                        <input type="hidden" class="warehouse_from_1_1" name="warehouse_from_1_1" id="warehouse_from_1_1">
                                                                        <input type="hidden" class="fara_row_id_1_1" name="fara_row_id_1_1" id="fara_row_id_1_1">
                                                                        <span class="warehouse_from_text_1_1" id="warehouse_from_text_1_1"></span>
                                                                    </td>

                                                                    <td style="width: 10%">-</td>
                                                                </tr>
                                                            </tbody>
                                                            <tfoot>
                                                                <tr>
                                                                    <input type="hidden" name="counter_1" id="counter_1" value="1" />
                                                                    <td colspan="3" class="text-right"><input type="button" value="Add More Row" class="btn btn-xs btn-primary" onclick="addMoreRowSubDetailRow('1')" /></td>
                                                                </tr>
                                                            </tfoot>
                                                        </table>
                                                    </td>
                                                    <td style="display: none">
                                                        <input  readonly type="number" name="rate_1" id="rate_1" class="form-control">
                                                    </td>
                                                    <td style="display: none">
                                                        <input readonly type="number" name="amount_1" id="amount_1" class="form-control">
                                                    </td>
                                                    <td><select name="warehouse_to_1" id="warehouse_to_1" class="form-control requiredField" style="width: 180px;">{!! SelectListFacades::getLocationList($m,1,0) !!}</select></td>
                                                    <td><textarea name="des_1" id="des_1"></textarea></td>
                                                    <td>-</td>
                                                </tr>
                                            </tbody>
                                        </table>
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
    <script>
        $("select").select2();
        function addMoreRowSubDetailRow(id){
            var oldCounter = $('#counter_'+id+'').val();
            var newCounter = parseInt(oldCounter) + parseInt(1);
            let data = '<tr><td style="width: 20%"><input type="hidden" name="trasnferDataArray_'+id+'[]" id="trasnferDataArray_'+id+'[]" value="'+newCounter+'" /><select class="form-control select2 iot_'+id+'" name="iot_'+id+'_'+newCounter+'" id="iot_'+id+'_'+newCounter+'" onchange="getLocationDetail('+id+','+newCounter+')"><option>IOT</option></select></td>'+
                '<td style="width: 10%">'+
                '<input type="hidden" class="warehouse_from_'+id+'_'+newCounter+'" name="warehouse_from_'+id+'_'+newCounter+'" id="warehouse_from_'+id+'_'+newCounter+'"><input type="hidden" class="fara_row_id_'+id+'_'+newCounter+'" name="fara_row_id_'+id+'_'+newCounter+'" id="fara_row_id_'+id+'_'+newCounter+'"><span class="warehouse_from_text_'+id+'_'+newCounter+'" id="warehouse_from_text_'+id+'_'+newCounter+'"></span></td>'+
                '<td style="width: 10%">-</td></tr>';
            $('#sub_detail_'+id+'').append(data);
            $('#counter_'+id+'').val(newCounter);
            //$("select").select2();
            loadIOTDetail(id);
            $("select").select2();
        }
        var Counter = 1;
        function AddMoreRows()
        {
            Counter++;
            $('#AppendHtml').append('<tr class="text-center AutoNo">'+
                '<td><input type="hidden" name="trasnferArray[]" id="trasnferArray[]" value="'+Counter+'" /><select class="form-control select2 item_id" onchange="loadIOTDetail('+Counter+')" name="item_id_'+Counter+'" id="item_id_'+Counter+'"><option>Select Product</option>{!! $all_items !!}</select></td>'+
                '<td colspan="3">'+
                '<table class="table table-bordered table-striped table-condensed"><tbody id="sub_detail_'+Counter+'"><tr><td><input type="hidden" name="trasnferDataArray_'+Counter+'[]" id="trasnferDataArray_'+Counter+'[]" value="1" /><select class="form-control select2 iot_'+Counter+'" name="iot_'+Counter+'_1" id="iot_'+Counter+'_1" onchange="getLocationDetail('+Counter+',1)"><option>IOT</option></select></td>'+
                '<td><input type="hidden" class="warehouse_from_'+Counter+'_1" name="warehouse_from_'+Counter+'_1" id="warehouse_from_'+Counter+'_1"><input type="hidden" class="fara_row_id_'+Counter+'_1" name="fara_row_id_'+Counter+'_1" id="fara_row_id_'+Counter+'_1"><span class="warehouse_from_text_'+Counter+'_1" id="warehouse_from_text_'+Counter+'_1"></span></td><td>-</td>'+
                '</tr></tbody><tfoot><tr><td colspan="3" class="text-right"><input type="hidden" name="counter_'+Counter+'" id="counter_'+Counter+'" value="1" /><input type="button" value="Add More Row" class="btn btn-xs btn-primary" onclick="addMoreRowSubDetailRow('+Counter+')" /></td></tr></tfoot></table></td>'+
                '<td style="display: none"><input  readonly type="number" name="rate_'+Counter+'" id="rate_'+Counter+'" class="form-control"></td>'+
                '<td style="display: none"><input readonly type="number" name="amount_'+Counter+'" id="amount_'+Counter+'" class="form-control"></td>'+
                '<td><select name="warehouse_to_'+Counter+'" id="warehouse_to_'+Counter+'" class="form-control requiredField" style="width: 180px;">{!! SelectListFacades::getLocationList($m,1,0) !!}</select></td>'+
                '<td><textarea name="des_'+Counter+'" id="des_'+Counter+'"></textarea></td><td>-</td></tr>');
                $("select").select2();
            
            // var AutoNo = $(".AutoNo").length;
            // $('#span').text(AutoNo);


            // $('.sam_jass').bind("enterKey",function(e){


            //     $('#items').modal('show');
            //     e.preventDefault();

            // });
            // $('.sam_jass').keyup(function(e){
            //     if(e.keyCode == 13)
            //     {
            //         selected_id=this.id;
            //         $(this).trigger("enterKey");
            //         e.preventDefault();

            //     }

            // });
            // $('.SendQtyy').on('keypress', function (event) {
            //     var regex = new RegExp("^[a-zA-Z0-9]+$");
            //     var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
            //     if (!regex.test(key)) {
            //         event.preventDefault();
            //         return false;
            //     }
            // });
        }
        function loadIOTDetail(id){
            var itemId = $('#item_id_'+id+'').val();
            $.ajax({
                url: "{{ route('stock.transfer.iot-products') }}",
                type: "GET",
                data: {item:itemId},
                success:function(res){
                    html = '';                    
                    res.forEach(data => { 
                        html += `<option data-faraId='${data.id}' data-locationName='${data.location_name}' data-locationId='${data.location_id}' value='${data.iot}'>${data.iot}</option>`
                    });
                    $('.iot_'+id+'').append(html);                    
                }
            });
        }
        function getLocationDetail(id,counter){
            var iot = $('#iot_'+id+'_'+counter+'').val();
            
            var optionSelected = $("#iot_"+id+'_'+counter+" option:selected");
            var location = optionSelected.data('locationid');
            var location_name = optionSelected.data('locationname');
            optionSelected.closest('tr').find('.warehouse_from_'+id+'_'+counter+'').val(location); 
            optionSelected.closest('tr').find('.fara_row_id_'+id+'_'+counter+'').val(optionSelected.data('faraid')); 
            optionSelected.closest('tr').find('.warehouse_from_text_'+id+'_'+counter+'').html(location_name);
        }
    </script>
@endsection