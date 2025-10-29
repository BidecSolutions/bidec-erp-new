<?php
$accType = Auth::user()->acc_type;
$m;
$currentDate = date('Y-m-d');
?>

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="well">
            <div class="row">
                <div class="row">
                    <?php echo Form::open(['url' => 'store/UpdateMaterialRequestVoucherForm?m=' . $m . '', 'id' => 'purchaseRequestVoucherForm']); ?>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="pageType" value="<?php echo $_GET['pageType']; ?>">
                    <input type="hidden" name="parentCode" value="<?php echo $_GET['parentCode']; ?>">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="panel">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <input type="hidden" name="materialRequestsSection[]"
                                            class="form-control requiredField" id="materialRequestsSection"
                                            value="1" />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                        <div class="row">
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                <label class="sf-label">Material Request Date. {{$itemID}}</label>
                                                <input  type="text" hidden  name="main_ID" value="{{$id}}"/>
                                                <span class="rflabelsteric"><strong>*</strong></span>
                                                <input type="text"
                                                    class="form-control requiredField fromDateDatePicker" readonly
                                                    name="material_request_date_1" id="material_request_date_1"
                                                    value="<?php echo $formDateValue; ?>" />
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                <label>Location :</label>
                                                <span class="rflabelsteric"><strong>*</strong></span>
                                                <select name="location_id_1" id="location_id_1"
                                                    class="form-control requiredField" required>
                                                    <?php echo SelectListFacades::getLocationList($m, 1,  $getMaterialRequestDetail->location_id); ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                <label class="sf-label">Requested Department / Sub Department</label>
                                                <span class="rflabelsteric"><strong>*</strong></span>
                                                <select class="form-control requiredField" name="department_id_1" id="department_id_1">
                                                    
                                                    @foreach ($departments as $key => $y)
                                                        <optgroup label="{{ $y->department_name }}"
                                                            value="{{ $y->id }}">
                                                            <?php
                                                            $departmentId = $y->id;
                                                            $subdepartments = Cache::rememberForever('cacheSubDepartment_' . $m . '', function () use ($m) {
                                                                return DB::select('select * from sub_department where company_id = ' . $m . '');
                                                            });
                                                            ?>
                                                            @foreach ($subdepartments as $key2 => $y2)
                                                                <?php
                                                                    if($y2->department_id == $departmentId){ 
                                                                        if($getMaterialRequestDetail->department_id == $y2->department_id && $getMaterialRequestDetail->sub_department_id == $y2->id){
                                                                            $selected = 'selected';
                                                                        }else{
                                                                            $selected = '';
                                                                        }
                                                                ?>
                                                                <option {{ $selected }}  value="{{ $y2->id . '<*>' . $y->id }}">
                                                                    {{ $y2->sub_department_name }}</option>
                                                                <?php
                                                                    }
                                                                ?>
                                                            @endforeach
                                                        </optgroup>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                <label>Project :</label>
                                                <span class="rflabelsteric"><strong>*</strong></span>
                                                <select name="project_id_1" id="project_id_1"
                                                    class="form-control requiredField" required>
                                                    <?php echo SelectListFacades::getProjectList($m, 1, $getMaterialRequestDetail->project_id); ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                        <div class="row">
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                <label class="sf-label">Remarks</label>
                                                <span class="rflabelsteric"><strong>*</strong></span>
                                                <textarea name="description_1" id="description_1" rows="6" cols="50" style="resize:none;"
                                                    class="form-control requiredField">{{$getMaterialRequestDetail->description}}</textarea>
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
                                                    <div class="table-responsive">
                                                        <table id="buildyourform" class="table table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th class="text-center col-sm-3">Category <span
                                                                            class="rflabelsteric"><strong>*</strong></span>
                                                                    </th>
                                                                    <th class="text-center col-sm-3">Sub Item <span
                                                                            class="rflabelsteric"><strong>*</strong></span>
                                                                    </th>
                                                                    <th class="text-center" style="width:150px;">Qty in
                                                                        Unit <span
                                                                            class="rflabelsteric"><strong>*</strong></span>
                                                                    </th>
                                                                    <th class="text-center" style="width:150px;">
                                                                        Description</th>
                                                                    <th class="text-center" style="width:100px;">Action
                                                                    </th>
                                                                </tr>
                                                            </thead>
                                                           
                                                            <tbody class="addMoreMaterialRequestsDetailRows_1"
                                                            id="addMoreMaterialRequestsDetailRows_1">
                                                            @foreach($getMaterialRequestDataDetail as $key => $row)
                                                                <input type="hidden"
                                                                    name="materialRequestDataSection[]"
                                                                    class="form-control requiredField"
                                                                    id="materialRequestDataSection_{{ $key+1 }}" value="{{ $row->id }}" />
                                                                <tr>
                                                                    <td>
                                                                        <select name="category_id[]"
                                                                            id="category_id_1_{{ $key+1 }}"
                                                                            onchange="subItemListLoadDepandentCategoryId(this.id,this.value)"
                                                                            class="form-control requiredField">
                                                                            <?php echo PurchaseFacades::categoryList($m, $row->category_id); ?>
                                                                        </select>
                                                                    </td>
                                                                    <td>
                                                                        <select name="sub_item_id[]"
                                                                            id="sub_item_id_1_{{ $key+1 }}" value="{{$row->sub_ic}}"
                                                                            class="form-control requiredField">
                                                                            <option selected value="{{$row->sub_item_id}}">{{ CommonFacades::get_subitem_name($row->sub_item_id) }}</option>
                                                                        </select>
                                                                    </td>
                                                                    <td>
                                                                        <input type="number" name="qty[]"
                                                                            id="qty_1_{{ $key+1 }}" step="0.0001" value="{{$row->qty}}"
                                                                            class="form-control requiredField" />
                                                                    </td>
                                                                    <td>
                                                                        <input type="text"
                                                                            name="sub_description[]"
                                                                            id="sub_description_1_{{ $key+1 }}" value="{{$row->sub_description}}"
                                                                            class="form-control requiredField" />
                                                                    </td>
                                                                    <td class="text-center">---</td>
                                                                </tr>
                                                                @endforeach
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
                                        <input type="button" class="btn btn-sm btn-primary"
                                            onclick="addMoreMaterialRequestsDetailRows('1')"
                                            value="Add More Material Request's Rows" />
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
                            <input type="button" style="display: none;"
                                class="btn btn-sm btn-primary addMoreMaterialRequests"
                                value="Add More Material Material's Section" />
                        </div>
                    </div>
                    <?php echo Form::close(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(function() {
        $("select").select2();
    });
    $(document).ready(function() {
        var d = 1;
        $('.addMoreMaterialRequests').click(function(e) {
            e.preventDefault();
            d++;
            var m = '<?php echo $m; ?>';
            $.ajax({
                url: '<?php echo url('/'); ?>/stmfal/makeFormMaterialRequestVoucher',
                type: "GET",
                data: {
                    id: d,
                    m: m
                },
                success: function(data) {
                    $('.materialRequestsSection').append(
                        '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="MaterialRequests_' +
                        d + '"><a href="#" onclick="removeMaterialRequestsSection(' +
                        d +
                        ')" class="btn btn-xs btn-danger">Remove</a><div class="lineHeight">&nbsp;</div><div class="panel"><div class="panel-body">' +
                        data + '</div></div></div>');
                }
            });
        });

        $(".btn-success").click(function(e) {
            var materialRequests = new Array();
            var val;
            $("input[name='materialRequestsSection[]']").each(function() {
                materialRequests.push($(this).val());
            });
            var _token = $("input[name='_token']").val();
            for (val of materialRequests) {
                jqueryValidationCustom();
                if (validate == 0) {
                    //alert(response);
                    $(".btnSubmit").val('Sending, please wait...');
                    setTimeout(function() {
                        $(".btnSubmit").prop("type", "button");
                    }, 50);
                } else {
                    return false;
                }
            }

        });
    });
    var x = {{ count($getMaterialRequestDataDetail) }};

    function addMoreMaterialRequestsDetailRows(id) {
        x++;
        var m = '<?php echo $m; ?>';
        $.ajax({
            url: '<?php echo url('/'); ?>/stmfal/addMoreMaterialRequestsDetailRows',
            type: "GET",
            data: {
                counter: x,
                id: id,
                m: m
            },
            success: function(data) {
                //alert(data);
                $('.addMoreMaterialRequestsDetailRows_' + id + '').append(data);
            }
        });
    }

    function removeMaterialRequestsRows(id, counter) {
        var elem = document.getElementById('removeMaterialRequestsRows_' + id + '_' + counter + '');
        elem.parentNode.removeChild(elem);
    }

    function removeMaterialRequestsSection(id) {
        var elem = document.getElementById('MaterialRequests_' + id + '');
        elem.parentNode.removeChild(elem);
    }

    function subItemListLoadDepandentCategoryId(id, value) {
        var arr = id.split('_');
        var m = '<?php echo $m; ?>';
        $.ajax({
            url: '<?php echo url('/'); ?>/pmfal/subItemListLoadDepandentCategoryId',
            type: "GET",
            data: {
                id: id,
                m: m,
                value: value
            },
            success: function(data) {
                $('#sub_item_id_' + arr[2] + '_' + arr[3] + '').html(data);
            }
        });
    }

    function formSubmitOne(e) {

        var postData = $('#purchaseRequestVoucherForm').serializeArray();
        var formURL = $('#purchaseRequestVoucherForm').attr("action");
        $.ajax({
            url: formURL,
            type: "POST",
            data: postData,
            success: function(data) {
                $('#showMasterTableEditModel').modal('toggle');
                filterVoucherList();
            }
        });
    }
</script>
