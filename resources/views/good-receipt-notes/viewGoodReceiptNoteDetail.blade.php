@php
    use App\Helpers\CommonHelper;
    $counter = 1;
    $data = [
        'type' => 2,
        'id' => $goodReceiptNoteDetail->id,
        'status' => $goodReceiptNoteDetail->status,
        'voucher_type_status' => $goodReceiptNoteDetail->grn_status,
    ];
@endphp

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">
        <?php echo CommonHelper::displayPrintButtonInBlade('PrintGoodReceiptNoteDetail', '', '1'); ?>
        {{ CommonHelper::getButtonsforPurchaseOrdersAndGoodReceiptNoteVouchers($data) }}
    </div>
</div>

<div class="lineHeight">&nbsp;</div>

<div class="well">
    <div class="row" id="PrintGoodReceiptNoteDetail">
           <div class="col-lg-12 text-center mb-3">
       {!! CommonHelper::displayCompanyInfo() !!}
        </div>
        <style>
            .floatLeft{
                width: 45%;
                float: left;
            }
            .floatRight{
                width: 45%;
                float: right;
            }
        </style>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="floatLeft">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <tbody>
                            <tr>
                                <th>G.R.N. No.</th>
                                <td>{{ $goodReceiptNoteDetail->grn_no }}</td>
                            </tr>
                            <tr>
                                <th>G.R.N. Date</th>
                                <td>{{ $goodReceiptNoteDetail->grn_date }}</td>
                            </tr>
                            
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="floatRight">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <tbody>
                            <tr>
                                <th>Supplier</th>
                                <td>{{ $goodReceiptNoteDetail->supplier ?? 'No supplier specified' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <label>Description</label>
            <p>{{ $goodReceiptNoteDetail->description }}</p>
        </div>

        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-condensed">
                    <thead>
                        <tr>
                            <th class="text-center">Product Name</th>
                            <th class="text-center">Purchase Order Detail</th>
                            <th class="text-center">Quotation No</th>
                            <th class="text-center">Expiry Date</th>
                                 <th class="text-center">Unit Price</th> 
                            <th class="text-center">Receive Quantity</th>
                               <th class="text-center">Sub Total</th> 
                        </tr>
                    </thead>
                     <tbody>
            @php
                $totalAmount = 0;
            @endphp
            @foreach ($goodReceiptNoteDataDetails as $key => $row)
                @php
                    $unitPrice = $row->po_unit_price ?? 0;
                    $receiveQty = $row->receive_qty ?? 0;
                    $subTotal = $unitPrice * $receiveQty;
                    $totalAmount += $subTotal;
                @endphp
                <tr>
                    <td>
                        {{ $row->product_name ?? 'N/A' }} -
                        {{ $row->size_name ?? 'N/A' }} -
                        {{ isset($row->product_variant_amount) ? number_format($row->product_variant_amount, 2) : '0.00' }}
                    </td>
                    <td>{{ $row->po_no }} - {{ CommonHelper::changeDateFormat($row->po_date) }}</td>
                    <td>{{ $row->quotation_no }}</td>
                    <td>{{ CommonHelper::changeDateFormat($row->expiry_date) }}</td>
                    <td class="text-center">{{ number_format($unitPrice, 2) }}</td>
                    <td class="text-center">{{ $receiveQty }}</td>
                    <td class="text-center">{{ number_format($subTotal, 2) }}</td> <!-- Sub Total -->
                </tr>
            @endforeach

            <!-- Total Amount Row -->
            <tr>
                <th colspan="6" class="text-left">Total Amount</th>
                <th class="text-center">{{ number_format($totalAmount, 2) }}</th>
            </tr>
        </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
