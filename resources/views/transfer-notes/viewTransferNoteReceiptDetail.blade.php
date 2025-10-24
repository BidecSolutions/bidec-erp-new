@php
    use App\Helpers\CommonHelper;
    $counter = 1;
@endphp

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">
        <?php echo CommonHelper::displayPrintButtonInBlade('PrintTransferNoteDetail', '', '1'); ?>
    </div>
</div>

<div class="lineHeight">&nbsp;</div>

<div class="well">
    <div class="row" id="PrintTransferNoteDetail">
        <style>
            .floatLeft {
                width: 45%;
                float: left;
            }
            .floatRight {
                width: 45%;
                float: right;
            }
            .error {
                color: red;
                font-size: 12px;
            }
        </style>
        <?php echo Form::open(array('url' => 'transfer-notes/updateTransferNotesReceiptDetail','id'=>'updateTransferNotesReceiptDetail'));?>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="floatLeft">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <tbody>
                            <tr>
                                <th>Transfer Note No.</th>
                                <td>{{ $transferNote->transfer_note_no }}</td>
                            </tr>
                            <tr>
                                <th>Transfer Note Date</th>
                                <td>{{ $transferNote->transfer_note_date }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <label>Description</label>
            <p>{{ $transferNote->description }}</p>
        </div>

        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-condensed">
                    <thead>
                        <tr>
                            <th class="text-center">Product Detail</th>
                            <th class="text-center">Location Name</th>
                            <th class="text-center">Remarks</th>
                            <th class="text-center">Send Qty</th>
                            <th class="text-center">Receive Qty</th>
                            <th class="text-center">Return Qty</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transferNoteDetails as $key => $row)
                            @php $disabled = ($row->tnd_status == 2 || $row->tnd_status == 3) ? 'disabled' : ''; @endphp
                            <tr>
                                <td>
                                    <input type="hidden" name="idsArray[]" value="{{$row->id}}" />
                                    {{ $row->product_name ?? 'N/A' }} - 
                                    {{ $row->size_name ?? 'N/A' }} - 
                                    {{ isset($row->product_variant_amount) ? number_format($row->product_variant_amount, 2) : '0.00' }}
                                </td>
                                <td class="text-center">{{ $row->company_location_name }}</td>
                                <td class="text-center">{{ $row->remarks }}</td>
                                <td class="text-center send-qty">{{ $row->send_qty ?? 0 }}</td>
                                <td class="text-center">
                                    <input type="number" name="receive_qty_{{$row->id}}" id="receive_qty_{{$row->id}}" 
                                           class="form-control receive-qty_{{$row->id}}"
                                           value="{{ $row->receive_qty ?? 0 }}" 
                                           min="0" onchange="checkQuantities({{$row->id}},{{$row->send_qty}})" {{$disabled}} />
                                </td>
                                <td class="text-center">
                                    <input type="number" name="return_qty_{{$row->id}}" id="return_qty_{{$row->id}}" 
                                           class="form-control return-qty_{{$row->id}}"
                                           value="{{ $row->return_qty ?? 0 }}" 
                                           min="0" onchange="checkQuantities({{$row->id}},{{$row->send_qty}})" {{$disabled}} />
                                </td>
                                <td class="text-center">
                                    @php
                                        $status = $row->tnd_status ?? 0;
                                        echo $status == 1 ? 'Pending' : ($status == 2 ? 'Received' : 'Returned');
                                    @endphp
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @if(count($transferNoteDetails) != 0)
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">
                {{ Form::button('Submit', ['class' => 'btn btn-success btn-success-edit']) }}
            </div>
        @endif
        <?php echo Form::close(); ?>
    </div>
</div>
<script>
    function checkQuantities(id, sendQty) {
        var receiveQty = parseInt($('#receive_qty_' + id).val()) || 0;
        var returnQty = parseInt($('#return_qty_' + id).val()) || 0;
        var totalQty = receiveQty + returnQty;

        if (totalQty > sendQty) {
            alert('The total of received and returned quantities cannot exceed the sent quantity!');
            
            // Adjust the input that triggered the change
            if (receiveQty > sendQty - returnQty) {
                $('#receive_qty_' + id).val(0);
            }
            if (returnQty > sendQty - receiveQty) {
                $('#return_qty_' + id).val(0);
            }
        }

        // Ensure values are not negative
        if (receiveQty < 0) {
            alert('Received quantity cannot be negative!');
            $('#receive_qty_' + id).val(0);
        }
        
        if (returnQty < 0) {
            alert('Returned quantity cannot be negative!');
            $('#return_qty_' + id).val(0);
        }
    }

    $(".btn-success-edit").click(function(e){
        // var locationId = $('#location_id').val();
        // //alert(regionId);
        // if(locationId == null){
        //     alert('Something Wrong! Please Select Location');
        //     return false;
        // }else{
        //     var subItem = new Array();
        //     var val;
        //     subItem.push($(this).val());
        //     var _token = $("input[name='_token']").val();
        //     for (val of subItem) {
        //         jqueryValidationCustom();
        //         if(validate == 0){
        //             //return false;
        //         }else{
        //             return false;
        //         }
        //     }
            formSubmitOne(e);   
        //}
    });


    function formSubmitOne(e){
        var postData = $('#updateTransferNotesReceiptDetail').serializeArray();
        var formURL = $('#updateTransferNotesReceiptDetail').attr("action");
        $.ajax({
            url : formURL,
            type: "POST",
            data : postData,
            success:function(data){
                alert(data);
                //$('#showDetailModelOneParamerter').modal('toggle');
                //get_ajax_data();
            }
        });
    }
</script>
