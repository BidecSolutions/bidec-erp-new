@php
    $counter = 1;
    $runningBalance = []; // Store balance per product

    // Loop over each stock transaction (each row from your query)
@endphp

<div class="row">
    <div class="col-lg-12">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-condensed">
                <thead>
                    <tr>
                        <th class="text-center">S.No</th>
                        <th class="text-center">Product Name</th>
                        <th class="text-center">Variant</th>
                        <th class="text-center">Type</th>
                        <th class="text-center">Supplier</th>
                        <th class="text-center">Customer</th>
                        <th class="text-center">To Location</th>
                        <th class="text-center">Date</th>
                        <th class="text-center">Purchase Qty</th>
                        <th class="text-center">Transfer In</th>
                        <th class="text-center">Transfer Out</th>
                        <th class="text-center">Sale Qty</th>
                        <th class="text-center">Return GRN Qty</th>
                        <th class="text-center">Sale Return Qty</th>
                        <th class="text-center">Balance</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stockSummary as $ssRow)
                        @php
                            // Set quantities based on the status
                            $purchaseQty   = ($ssRow->status == 2) ? $ssRow->qty : 0;
                            $transferInQty = ($ssRow->status == 3 && $ssRow->company_location_id == $ssRow->to_company_location_id) ? $ssRow->qty : 0;
                            $transferOutQty= ($ssRow->status == 3) ? $ssRow->qty : 0;
                            $saleQty       = ($ssRow->status == 1) ? $ssRow->qty : 0;
                            $returnGRNQty  = ($ssRow->status == 4) ? $ssRow->qty : 0;
                            $saleReturnQty = ($ssRow->status == 5) ? $ssRow->qty : 0;

                            // Build a unique key for each product variant combination
                            $productKey = $ssRow->product_name . '-' . $ssRow->size_name;

                            // Get the previous running balance, if any
                            $previousBalance = $runningBalance[$productKey] ?? 0;

                            // Calculate the current balance:
                            // - Sales and Return GRN subtract from stock.
                            // - Purchases, Transfer In, and Sale Returns add to stock.
                            $currentBalance = $previousBalance 
                                + $purchaseQty 
                                + $transferInQty 
                                - $transferOutQty 
                                - $saleQty 
                                - $returnGRNQty
                                + $saleReturnQty;
                            
                            $runningBalance[$productKey] = $currentBalance;
                        @endphp
                        <tr>
                            <td class="text-center">{{ $counter++ }}</td>
                            <td>{{ $ssRow->product_name }}</td>
                            <td>{{ $ssRow->size_name }}</td>
                            <td>{{ $ssRow->type }}</td>
                            <td>{{ $ssRow->supplier_name ?? '-' }}</td>
                            <td>{{ $ssRow->customer_name ?? '-' }}</td>
                            <td>{{ $ssRow->company_location_name ?? '-' }}</td>
                            <td class="text-center">
                                @switch($ssRow->status)
                                    @case(1) {{ $ssRow->order_date }} @break
                                    @case(2) {{ $ssRow->grn_date }} @break
                                    @case(3) {{ $ssRow->transfer_note_date }} @break
                                    @case(4) {{ $ssRow->rgrn_date }} @break
                                    @case(5) {{ $ssRow->return_date ?? '-' }} @break
                                    @default -
                                @endswitch
                            </td>
                            <td class="text-center">{{ $purchaseQty }}</td>
                            <td class="text-center">{{ $transferInQty }}</td>
                            <td class="text-center">{{ $transferOutQty }}</td>
                            <td class="text-center">{{ $saleQty }}</td>
                            <td class="text-center">{{ $returnGRNQty }}</td>
                            <td class="text-center">{{ $saleReturnQty }}</td>
                            <td class="text-center">{{ $currentBalance }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="15" class="text-center">No stock transactions found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
