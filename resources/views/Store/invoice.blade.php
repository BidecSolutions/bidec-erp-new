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
                            <span class="subHeadingLabelClass"> Edit Invoice Purchase Order
                                {{ $purchaseOrderData->purchase_order_no ?? '' }}</span>
                        </div>
                    </div>
                    <div class="lineHeight">&nbsp;</div>

                    <?php echo Form::open(['url' => 'stad/updateInvoicePO?m=' . $m . '', 'id' => 'addPurchaseOrderDetail']); ?>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="pageType" value="<?php echo Input::get('pageType'); ?>">
                    <input type="hidden" name="parentCode" value="<?php echo Input::get('parentCode'); ?>">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="panel">
                            <div class="panel-body">
                                <div class="lineHeight">&nbsp;</div>
                                {{-- <div class="loadPurchaseOrderDetailSection"></div> --}}
                                <div>
                                    <input hidden name="id" id="id" value="<?php echo $purchaseOrderData->id; ?>">
                                    <div class="row">

                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                            <label class="sf-label">Invoice/Quotation No.</label>
                                            <span class="rflabelsteric"><strong>*</strong></span>
                                            <input type="text" class="form-control requiredField" name="qoutation_no"
                                                id="qoutation_no" placeholder="Invoice/Quotation No."
                                                value="<?php echo $purchaseOrderData->qoutation_no; ?>" />
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
