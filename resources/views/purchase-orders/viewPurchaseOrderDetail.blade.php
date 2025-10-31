@php
    use App\Helpers\CommonHelper;
    $counter = 1;
    $data = [
        'type' => 1,
        'id' => $purchaseOrder->id,
        'status' => $purchaseOrder->status,
        'voucher_type_status' => $purchaseOrder->po_status,
    ];
@endphp

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">
        <?php echo CommonHelper::displayPrintButtonInBlade('PrintPurchaseOrderDetail', '', '1'); ?>
        {{ CommonHelper::getButtonsforPurchaseOrdersAndGoodReceiptNoteVouchers($data) }}
    </div>
</div>

<div class="lineHeight">&nbsp;</div>

<div class="well">
    <div class="row" id="PrintPurchaseOrderDetail">
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
                                <th>PO No.</th>
                                <td>{{ $purchaseOrder->po_no }}</td>
                            </tr>
                            <tr>
                                <th>Order Date</th>
                                <td>{{ $purchaseOrder->po_date }}</td>
                            </tr>
                            <tr>
                                <th>Supplier</th>
                                <td>{{ $purchaseOrder->supplier ?? 'No supplier specified' }}</td>
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
                                <th>Quotation No.</th>
                                <td>{{ $purchaseOrder->invoice_quotation_no }}</td>
                            </tr>
                            <tr>
                                <th>Quotation Date</th>
                                <td>{{ $purchaseOrder->quotation_date }}</td>
                            </tr>
                            <tr>
                                <th>Payment Type</th>
 <td>{{ $purchaseOrder->payment_type_name ?? 'N/A' }}</td>


                            </tr>
                            <tr>
                                <th>Payment Rate</th>
                                <td>{{ $purchaseOrder->payment_type_rate }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <label>Description</label>
            <p>{{ $purchaseOrder->po_note }}</p>
        </div>

        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-condensed">
                    <thead>
                        <tr>
                            <th class="text-center">Product Name</th>
                            <th class="text-center">Quantity</th>
                            <th class="text-center">Unit Price</th>
                            <th class="text-center">Sub Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalAmount = 0;
                        @endphp
                        @foreach ($purchaseOrderDetails as $key => $row)
                            @php
                                $totalAmount += $row->sub_total;
                            @endphp
                            <tr>
                                <td>
                                    {{ $row->product_name ?? 'N/A' }} -
                                    {{ $row->size_name ?? 'N/A' }} -
                                    {{ isset($row->product_variant_amount) ? number_format($row->product_variant_amount, 2) : '0.00' }}
                                </td>
                                <td class="text-center">{{ $row->qty ?? 0 }}</td>
                                <td class="text-right">
                                    {{ isset($row->unit_price) ? number_format($row->unit_price, 2) : '0.00' }}
                                </td>
                                <td class="text-right">
                                    {{ isset($row->sub_total) ? number_format($row->sub_total, 2) : '0.00' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3">Total</th>
                            <th class="text-right">
                                {{number_format($totalAmount,2)}}
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>


