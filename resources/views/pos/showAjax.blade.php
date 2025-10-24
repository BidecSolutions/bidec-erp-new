@php
    use App\Helpers\CommonHelper;
    $data = [
        'type' => 3, // Assuming POS type identifier
        'id' => $orderDetails['order']->id,
        'status' => $orderDetails['order']->status,
        'voucher_type_status' => $orderDetails['order']->status,
    ];
@endphp

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">
        <?php echo CommonHelper::displayPrintButtonInBlade('PrintPOSOrderDetail', '', '1'); ?>
    </div>
</div>

<div class="lineHeight">&nbsp;</div>

<div class="well">
    <div class="row" id="PrintPOSOrderDetail">
        <style>
            .floatLeft { width: 45%; float: left; }
            .floatRight { width: 45%; float: right; }
            .total-row { font-weight: bold; background: #f5f5f5; }
            .voucher-section { margin-top: 20px; border-top: 2px solid #ddd; padding-top: 15px; }
        </style>

        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="floatLeft">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <tbody>
                            <tr>
                                <th>Order No</th>
                                <td>{{ $orderDetails['order']->order_no }}</td>
                            </tr>
                            <tr>
                                <th>Order Date</th>
                                <td>{{ $orderDetails['order']->order_date }}</td>
                            </tr>
                            <tr>
                                <th>Customer</th>
                                <td>{{ $orderDetails['order']->customer_name }}</td>
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
                                <th>Payment Type</th>
                                <td>{{ $orderDetails['order']->payment_type == 1 ? 'Cash' : 'Bank' }}</td>
                            </tr>
                            <tr>
                                <th>Total Amount</th>
                                <td>{{ number_format($orderDetails['order']->total_amount, 2) }}</td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>{{ $orderDetails['order']->status == 1 ? 'Completed' : 'Pending' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-condensed">
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Product</th>
                            <th class="text-center">Variant</th>
                            <th class="text-center">Price</th>
                            <th class="text-center">Qty</th>
                            <th class="text-center">Discount</th>
                            <th class="text-center">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orderDetails['items'] as $key => $item)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>{{ $item->product_name }}</td>
                            <td class="text-center">
                                @if($item->variant_id)
                                    {{ $item->size_name ?? 'N/A' }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-right">{{ number_format($item->price, 2) }}</td>
                            <td class="text-center">{{ $item->qty }}</td>
                            <td class="text-right">{{ number_format($item->discount, 2) }}</td>
                            <td class="text-right">
                                {{ number_format(($item->price * $item->qty) - $item->discount, 2) }}
                            </td>
                        </tr>
                        @endforeach
                        <tr class="total-row">
                            <td colspan="6" class="text-right">Grand Total</td>
                            <td class="text-right">
                                {{ number_format($orderDetails['order']->total_amount, 2) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        @if($orderDetails['journal_voucher'] || $orderDetails['receipt'])
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 voucher-section">
            <div class="row">
                @if($orderDetails['journal_voucher'])
                <div class="col-md-6">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th>Journal Voucher No</th>
                                    <td>{{ $orderDetails['journal_voucher']->jv_no }}</td>
                                </tr>
                                <tr>
                                    <th>JV Date</th>
                                    <td>{{ $orderDetails['journal_voucher']->jv_date }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>{{ $orderDetails['journal_voucher']->jv_status == 2 ? 'Approved' : 'Pending' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                @if($orderDetails['receipt'])
                <div class="col-md-6">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th>Receipt No</th>
                                    <td>{{ $orderDetails['receipt']->rv_no }}</td>
                                </tr>
                                <tr>
                                    <th>Receipt Date</th>
                                    <td>{{ $orderDetails['receipt']->rv_date }}</td>
                                </tr>
                                <tr>
                                    <th>Payment Amount</th>
                                    <td>{{ number_format($orderDetails['order']->payment_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Change Amount</th>
                                    <td>{{ number_format($orderDetails['order']->payment_amount - $orderDetails['order']->total_amount , 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>