<?php
$accType = Auth::user()->acc_type;
$currentDate = date('Y-m-d');
$m;

?>
    

<script src="{{ URL::asset('assets/select2/select2.full.min.js') }}"></script>
<link href="{{ URL::asset('assets/select2/select2.css') }}" rel="stylesheet">

<div class="">
    <div class="boking-wrp dp_sdw">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="well">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <span class="subHeadingLabelClass"> Edit Checklist 
                            </span>
                        </div>
                    </div>
                    <div class="lineHeight">&nbsp;</div>

                    <?php echo Form::open(['url' => 'production/updateChecklist?m=' . $m . '', 'id' => 'addPurchaseOrderDetail']); ?>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="pageType" value="<?php echo Input::get('pageType'); ?>">
                    <input type="hidden" name="parentCode" value="<?php echo Input::get('parentCode'); ?>">
                    
                    <input type="hidden" value="{{$dataChecklist->id}}" name="check_id" >
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="panel">
                            <div class="panel-body">
                                <div class="lineHeight">&nbsp;</div>
                                {{-- <div class="loadPurchaseOrderDetailSection"></div> --}}
                                <div>
                                    <div class="row">

                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                            <label class="sf-label">Checklist Name.</label>
                                            <span class="rflabelsteric"><strong>*</strong></span>
                                            <input type="text" class="form-control requiredField" name="checklistName"
                                                id="checklistName" placeholder="Invoice/Quotation No."
                                                value="{{$dataChecklist->checklist_name}}" />
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                            <label class="sf-label">Model.</label>
                                            <span class="rflabelsteric"><strong>*</strong></span>
                                                <select name="recipe_id[]" id="recipe_id" class="form-control select2" multiple>
                                                    @php
                                                    $model_specific_check = json_decode($dataChecklist->recipe_id, true);
                                                    if (empty($model_specific_check)) {
                                                        $model_specific_check = [];
                                                    }
                                                    @endphp                                     
                                                    @foreach ($recipes as $recipe)
                                                        <option {{ in_array($recipe->id, $model_specific_check) ? 'selected' : '' }} value="{{ $recipe->id }}">{{ $recipe->subitem->sub_ic ?? '' }}</option>
                                                    @endforeach
                                                </select>
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                            <label class="sf-label">Stage</label>
                                            <span class="rflabelsteric"><strong>*</strong></span>
                                                <select name="stageid" id="stageid" class="form-control">                                                    
                                                   @foreach($dataStages as  $item)
                                                     <option {{ $dataChecklist->stage_id == $item->id ? 'selected' : '' }}  value="{{$item->id}}">{{$item->stage_name}}</option>  
                                                   @endforeach
                                                </select>
                                        </div>

                                    </div>
                                    <div class="lineHeight">&nbsp;</div>

                                    <div class="row">

                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">
                                            {{ Form::submit('Submit', ['class' => 'btn btn-success', 'id' => '']) }}
                                            <button type="reset" id="reset" class="btn btn-primary">Clear
                                                Form</button>
                                        </div>
                                    </div>



                                </div>
                            </div>
                        </div>
                    </div>
                    <?php echo Form::close(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<script>
    $('.select2').select2();       
</script>
