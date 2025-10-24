<div class="row">
    <div class="col-lg-12">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-condensed">
                <thead>
                    <tr>
                        <th class="text-center">S.No</th>
                        <th class="text-center">JV No</th>
                        <th class="text-center">Order No</th>
                        <th class="text-center">Product Name</th>
                        <th class="text-center">Variant</th>
                        <th class="text-center">Customer</th>
                        <th class="text-center">Account Name</th>
                        <th class="text-center">Sale Qty</th>
                        <th class="text-center">Unit Price</th>
                        <th class="text-center">Amount</th>
                        <th class="text-center">Sale Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($salesData as $key => $sale)
                        <tr>
                            <td class="text-center">{{ $key + 1 }}</td>
                            <td>{{ $sale->jv_no ?? '-' }}</td>
                            <td>{{ $sale->order_no ?? '-' }}</td>
                            <td>{{ $sale->product_name }}</td>
                            <td>{{ $sale->size_name }}</td>
                            <td>{{ $sale->customer_name ?? '-' }}</td>
                            <td>{{ $sale->account_name ?? '-' }}</td>
                            <td class="text-center">{{ round($sale->sale_qty) }}</td>
                            <td class="text-center">{{ round($sale->unit_price) }}</td>
                            <td class="text-center">{{ round($sale->amount) }}</td>
                            <td class="text-center">{{ $sale->order_date }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center">No sales transactions found</td>
                        </tr>
                    @endforelse
                    {{-- Total Amount Row (FIXED) --}}
                    @if(!empty($salesData))
                                        @php
                                            $totalAmount = 0;
                                            foreach ($salesData as $sale) {
                                                // Access amount as an object property, not an array
                                                $totalAmount += round($sale->amount);
                                            }
                                        @endphp
                                        <tr>
                                            <td colspan="9" class="text-right"><strong>Total Amount:</strong></td>
                                            <td class="text-center">{{ $totalAmount }}</td>
                                            <td></td>
                                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>