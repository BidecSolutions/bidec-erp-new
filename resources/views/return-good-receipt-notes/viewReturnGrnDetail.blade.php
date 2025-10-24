@php
    use App\Helpers\CommonHelper;
    $counter = 1;
    $data = [
        'type' => 3, // Type value for Return GRN (adjust as needed)
        'id' => $returnGrnDetail->id,
        'status' => $returnGrnDetail->status,
        'voucher_type_status' => $returnGrnDetail->return_grn_status,
    ];
@endphp

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">
        <?php echo CommonHelper::displayPrintButtonInBlade('PrintReturnGrnDetail', '', '1'); ?>
        {{ CommonHelper::getButtonsforReturnGoodReceiptNoteVouchers($data) }}
    </div>
</div>

<div class="lineHeight">&nbsp;</div>

<div class="well">
    <div class="row" id="PrintReturnGrnDetail">
        <style>
            .floatLeft {
                width: 45%;
                float: left;
            }

            .floatRight {
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
                                <th>Return G.R.N. No.</th>
                                <td>{{ $returnGrnDetail->return_grn_no }}</td>
                            </tr>
                            <tr>
                                <th>Return Date</th>
                                <td>{{ $returnGrnDetail->return_date }}</td>
                            </tr>
                            <tr>
                                <th>Original GRN No.</th>
                                <td>{{ $returnGrnDetail->original_grn_no ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Original GRN Date</th>
                                <td>{{ $returnGrnDetail->original_grn_date ?? 'N/A' }}</td>
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
                                <td>{{ $returnGrnDetail->supplier_name ?? 'No supplier specified' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <label>Reason</label>
            <p>{{ $returnGrnDetail->reason }}</p>
        </div>

        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-condensed">
                    <thead>
                        <tr>
                            <th class="text-center">Product Name  -  Variant Name</th>
                            <th class="text-center">Purchase Order Detail</th>
                            <th class="text-center">Return Quantity</th>
                            <th class="text-center">Unit Price</th>
                            <th class="text-center">Amount</th>
                            <th class="text-center">Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalAmount = 0;
                        @endphp
                        @foreach ($returnGrnDataDetails as $key => $row)
                                                @php
                                                    $lineAmount = $row->po_unit_price * $row->return_qty;
                                                    $totalAmount += $lineAmount;
                                                @endphp
                                                <tr>
                                                    <td>
                                                        {{ $row->product_name ?? 'N/A' }} -
                                                        {{ $row->size_name ?? 'N/A' }}
                                                    </td>
                                                    <td>{{ $row->po_no }} - {{ $row->po_date }}</td>
                                                    <td class="text-center">{{ $row->return_qty }}</td>
                                                    <td class="text-center">{{ number_format($row->po_unit_price, 2) }}</td>
                                                    <td class="text-center">{{ number_format($lineAmount, 2) }}</td>
                                                    <td>{{ $row->remarks ?? '' }}</td>
                                                </tr>
                        @endforeach
                        <tr>
                            <td colspan="4" class="text-right"><strong>Total Amount</strong></td>
                            <td class="text-center"><strong>{{ number_format($totalAmount, 2) }}</strong></td>
                            <td></td>
                        </tr>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>