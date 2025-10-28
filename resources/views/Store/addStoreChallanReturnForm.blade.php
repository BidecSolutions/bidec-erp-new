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
                                <span class="subHeadingLabelClass">Return Store Challan Form</span>
                            </div>
                        </div>
                        <div class="lineHeight">&nbsp;</div>
                        <div class="row">
                            <?php echo Form::open(array('url' => 'stad/addStoreChallanReturnDetail?m='.$m.'','id'=>'addStoreChallanDetail'));?>
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="pageType" value="<?php echo Input::get('pageType')?>">
                            <input type="hidden" name="parentCode" value="<?php echo Input::get('parentCode')?>">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="panel">
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                <label class="sf-label">Challan Detail</label>
                                                <span class="rflabelsteric"><strong>*</strong></span>
                                                <select class="form-control" required name="material_request_no" id="material_request_no" onchange="loadStoreChallanDetailByMRNo()">
                                                    <option value="">Select Challan To Return</option>
                                                    <?php foreach($challanRequestDatas as $row){?>
                                                        <option value="<?php echo $row->store_challan_no.'<*>'.$row->store_challan_date?>"><?php echo 'Challan No => &nbsp;&nbsp;&nbsp;'.$row->store_challan_no.'&nbsp;, MR Date => &nbsp;&nbsp;&nbsp;'.CommonFacades::changeDateFormat($row->store_challan_date).' , Created By => &nbsp;&nbsp;&nbsp;'.$row->username.' , Location => &nbsp;&nbsp;&nbsp;'.$row->location_name .' , Department / Sub Department => &nbsp;&nbsp;&nbsp;'.$row->department_name.' / '.$row->sub_department_name?></option>
                                                    <?php }?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="lineHeight">&nbsp;</div>
                                        <div class="loadStoreChallanDetailSection"></div>
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
    <script>
        $(function () {
            $("select").select2();
        });
        function loadStoreChallanDetailByMRNo(){
            var mrNo = $('#material_request_no').val();
            var m = '<?php echo $m?>';
            var pageType = '<?php echo Input::get('pageType')?>';
            var parentCode = '<?php echo Input::get('parentCode')?>';
            if(mrNo == ''){
                alert('Please Select Challan No');
                $('.loadStoreChallanDetailSection').html('');
            }else{
                $('.loadStoreChallanDetailSection').html('<div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div></div>');
                $.ajax({
                    url: '<?php echo url('/')?>/stmfal/makeFormStoreChallanReturnByMRNo',
                    type: "GET",
                    data: { mrNo:mrNo,pageType:pageType,parentCode:parentCode},
                    success:function(data) {
                        $('.loadStoreChallanDetailSection').html(data);
                        //disableInputFormDateAccountYear();
                        $('#submit-btn-abc').prop('disabled', false);
                    }
                });
            }
        }
        
    </script>
@endsection