<?php
    $accType = Auth::user()->acc_type;
    $m = Session::get('company_id');
    $formDateValue = date('Y-m-d');
?>
@extends('layouts.layouts')

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
                                <span class="subHeadingLabelClass">Create Material Request Form</span>
                            </div>
                        </div>
                        <div class="lineHeight">&nbsp;</div>
                        <div class="row">
                            <?php echo Form::open(array('url' => 'stad/addMaterialRequestDetail?m='.$m.'','id'=>'addMaterialRequestDetail'));?>
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="productionProcessId" id="productionProcessId" value="0" />
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="panel">
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                <input type="hidden" name="materialRequestsSection[]" class="form-control requiredField" id="materialRequestsSection" value="1" />
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                <div class="row">
                                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                        <label class="sf-label">Material Request Date.</label>
                                                        <span class="rflabelsteric"><strong>*</strong></span>
                                                        <input type="text" class="form-control requiredField fromDateDatePicker" readonly name="material_request_date_1" id="material_request_date_1" value="<?php echo $formDateValue ?>" />
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                        <label class="sf-label">Requested Department / Sub Department</label>
                                                        <span class="rflabelsteric"><strong>*</strong></span>
                                                        <select class="form-control requiredField" name="sub_department_id_1" id="sub_department_id_1">
                                                            <option value="">Select Department</option>
                                                            @foreach($departments as $key => $y)
                                                                <option value="{{ $y->id}}">{{ $y->department_name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                <div class="row">
                                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                        <label class="sf-label">Remarks</label>
                                                        <span class="rflabelsteric"><strong>*</strong></span>
                                                        <textarea name="description_1" id="description_1" rows="6" cols="50" style="resize:none;" class="form-control requiredField"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="lineHeight">&nbsp;</div>
                                        <div class="well">
                                            <div class="panel">
                                                <div class="panel-body">
                                                    <div class="row">
                                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                            <div id="itemList" class="table-responsive">
                                                                <table id="buildyourform" class="table table-bordered">
                                                                    <thead>
                                                                    <tr>
                                                                        <th class="text-center col-sm-3">Category <span class="rflabelsteric"><strong>*</strong></span></th>
                                                                        <th class="text-center col-sm-3">Sub Item <span class="rflabelsteric"><strong>*</strong></span></th>
                                                                        <th class="text-center" style="width:150px;">Qty in Unit <span class="rflabelsteric"><strong>*</strong></span></th>
                                                                        <th class="text-center" style="width:150px;">Description</th>
                                                                        <th class="text-center" style="width:100px;">Action</th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody class="addMoreMaterialRequestsDetailRows_1" id="addMoreMaterialRequestsDetailRows_1">
                                                                    <input type="hidden" name="materialRequestDataSection[]" class="form-control requiredField materialRequestDataSection_1" id="materialRequestDataSection_1" value="1" />
                                                                    <tr>
                                                                        <td>
                                                                            <select name="category_id[]" id="category_id_1_1" onchange="subItemListLoadDepandentCategoryId(this.id,this.value)" class="form-control requiredField">
                                                                                @foreach($departments as $key => $y)
                                                                                    <option value="{{ $y->id}}">{{ $y->department_name}}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </td>
                                                                        <td>
                                                                            <select name="sub_item_id[]" id="sub_item_id_1_1" class="form-control requiredField">
                                                                            </select>
                                                                        </td>
                                                                        <td>
                                                                            <input type="number" name="qty[]" id="qty_1_1" step="0.0001" class="form-control requiredField" />
                                                                        </td>
                                                                        <td>
                                                                            <input type="text" name="sub_description[]" id="sub_description_1_1" value="-" class="form-control requiredField" />
                                                                        </td>
                                                                        <td class="text-center">---</td>
                                                                    </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">
                                                <input type="button" class="btn btn-sm btn-primary" onclick="addMoreMaterialRequestsDetailRows('1')" value="Add More Material Request's Rows" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="materialRequestsSection"></div>
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">
                                    {{ Form::submit('Submit', ['class' => 'btn btn-success btnSubmit']) }}
                                    <button type="reset" id="reset" class="btn btn-primary">Clear Form</button>
                                    <input type="button" style="display: none;" class="btn btn-sm btn-primary addMoreMaterialRequests" value="Add More Material Material's Section" />
                                </div>
                            </div>
                            <?php echo Form::close();?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(function () {
            $("select").select2();
        });
        $(document).ready(function() {
            var d = 1;
            $('.addMoreMaterialRequests').click(function (e){
                e.preventDefault();
                d++;
                var m = '<?php echo $m;?>';
                $.ajax({
                    url: '<?php echo url('/')?>/stmfal/makeFormMaterialRequestVoucher',
                    type: "GET",
                    data: { id:d,m:m},
                    success:function(data) {
                        $('.materialRequestsSection').append('<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="MaterialRequests_'+d+'"><a href="#" onclick="removeMaterialRequestsSection('+d+')" class="btn btn-xs btn-danger">Remove</a><div class="lineHeight">&nbsp;</div><div class="panel"><div class="panel-body">'+data+'</div></div></div>');
                    }
                });
            });

            $(".btn-success").click(function(e){
                var materialRequests = new Array();
                var val;
                $("input[name='materialRequestsSection[]']").each(function(){
                    materialRequests.push($(this).val());
                });
                var _token = $("input[name='_token']").val();
                for (val of materialRequests) {
                    jqueryValidationCustom();
                    if(validate == 0){
                        //alert(response);
                        $(".btnSubmit").val('Sending, please wait...');
                        setTimeout(function(){
                            $(".btnSubmit").prop("type", "button");
                        },50);
                    }else{
                        return false;
                    }
                }

            });



            $('#recipe_id').on('change', function(){
                var id = $(this).val();
                var no_of_qty = $('#no_of_qty_1').val();
                $.ajax({
                    url: '{{ route('recipe.material.items') }}',
                    type: "GET",
                    data: { id:id,no_of_qty:no_of_qty },
                    success:function(data) {
                        $('#itemList').html(data);
                    }
                }); 
            })

        });
        function loadRecipeDetail(){
            var id = $('#recipe_id').val();
            var no_of_qty = $('#no_of_qty_1').val();
            $.ajax({
                url: '{{ route('recipe.material.items') }}',
                type: "GET",
                data: { id:id,no_of_qty:no_of_qty },
                success:function(data) {
                    $('#itemList').html(data);
                }
            });
        }
        var x = 1;
        function addMoreMaterialRequestsDetailRows(id){
            console.log($('.materialRequestDataSection_1:last').val());
            if ($('.addMoreMaterialRequestsDetailRows_'+id+':last').val()) {
                x = $('.addMoreMaterialRequestsDetailRows_'+id+':last').val()
            }
            x++;
            var m = '<?php echo $m;?>';
            $.ajax({
                url: '<?php echo url('/')?>/stmfal/addMoreMaterialRequestsDetailRows',
                type: "GET",
                data: { counter:x,id:id,m:m},
                success:function(data) {
                    //alert(data);
                    $('.addMoreMaterialRequestsDetailRows_'+id+'').append(data);
                }
            });
        }

        function removeMaterialRequestsRows(id,counter){
            var elem = document.getElementById('removeMaterialRequestsRows_'+id+'_'+counter+'');
            elem.parentNode.removeChild(elem);
        }
        function removeMaterialRequestsSection(id){
            var elem = document.getElementById('MaterialRequests_'+id+'');
            elem.parentNode.removeChild(elem);
        }

        function subItemListLoadDepandentCategoryId(id,value) {
            var arr = id.split('_');
            var m = '<?php echo $m;?>';
            $.ajax({
                url: '<?php echo url('/')?>/pmfal/subItemListLoadDepandentCategoryId',
                type: "GET",
                data: { id:id,m:m,value:value},
                success:function(data) {
                    $('#sub_item_id_'+arr[2]+'_'+arr[3]+'').html(data);
                }
            });
        }

        
    </script>
@endsection