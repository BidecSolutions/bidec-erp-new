@php
    use App\Helpers\CommonHelper;
    $counter = 1;
    $data = [
        'type' => 4, // Adjust the type as per your voucher system
        'id' => $returnSaleDetail->id,
        'status' => $returnSaleDetail->status,
        'voucher_type_status' => $returnSaleDetail->return_sale_status,
    ];
@endphp

<div class="row">
    <div class="col-lg-12 text-right">
        {!! CommonHelper::displayPrintButtonInBlade('PrintReturnSaleDetail', '', '1') !!}
        {{ CommonHelper::getButtonsforReturnSale($data) }}

    </div>
</div>

<div class="lineHeight">&nbsp;</div>

<div class="well">
    <div class="row" id="PrintReturnSaleDetail">
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

        <div class="col-lg-12">
            <div class="floatLeft">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <tbody>
                            <tr>
                                <th>Return Sale No.</th>
                                <td>{{ $returnSaleDetail->return_sale_no }}</td>
                            </tr>
                            <tr>
                                <th>Return Date</th>
                                <td>{{ $returnSaleDetail->return_sale_date }}</td>
                            </tr>
                            <tr>
                                <th>Order No.</th>
                                <td>{{ $returnSaleDetail->original_order_no ?? 'N/A' }}</td>
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
                                <th>Customer</th>
                                <td>{{ $returnSaleDetail->customer_name ?? 'No customer specified' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-12">
            <label>Reason</label>
            <p>{{ $returnSaleDetail->reason }}</p>
        </div>

        <div class="col-lg-12">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-condensed">
                    <thead>
                        <tr>
                            <th class="text-center">Product Name - Size</th>
                            <th class="text-center">Return Quantity</th>
                            <th class="text-center">Unit Price</th>
                            <th class="text-center">Amount</th>
                            <th class="text-center">Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $totalAmount = 0; @endphp
                        @foreach ($returnSaleItems as $item)

                            @php
                                $lineAmount = $item->unit_price * $item->return_qty;
                                $totalAmount += $lineAmount;
                            @endphp
                            <tr>
                                <td class="text-center">
                                    {{ $item->product_name ?? 'N/A' }} - {{ $item->size_name ?? 'N/A' }}
                                </td>
                                <td class="text-center">{{ $item->return_qty }}</td>
                                <td class="text-center">{{ number_format($item->unit_price, 2) }}</td>
                                <td class="text-center">{{ number_format($lineAmount, 2) }}</td>
                                <td class="text-center">{{ $item->remarks ?? '' }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="3" class="text-right"><strong>Total Amount</strong></td>
                            <td class="text-center"><strong>{{ number_format($totalAmount, 2) }}</strong></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
