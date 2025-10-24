@php
    use App\Helpers\CommonHelper;
    $counter = 1;
    $data = [
        'type' => 1,
        'id' => $purchaseOrder->id,
        'status' => $purchaseOrder->status,
        'voucher_type_status' => $purchaseOrder->status,
    ];
@endphp

<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title">Purchase Order Details</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-lg-12 text-right">
                <?php echo CommonHelper::displayPrintButtonInBlade('PrintPurchaseOrderDetail', '', '1'); ?>
                <!-- {{ CommonHelper::getButtonsforPaymentAndReceiptVouchers($data) }} -->
            </div>
        </div>

        <div class="lineHeight">&nbsp;</div>

        <div class="well">
            <div class="row" id="PrintPurchaseOrderDetail">
                <div class="col-lg-12">
                    <div class="floatLeft">
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
                                    <tr>
                                        <th>Delivery Place</th>
                                        <td>{{ $purchaseOrder->delivery_place }}</td>
                                    </tr>
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
                                        <td>{{ $purchaseOrder->paymentType }}</td>
                                    </tr>
                                    <tr>
                                        <th>Payment Rate</th>
                                        <td>{{ $purchaseOrder->payment_type_rate }}</td>
                                    </tr>
                                    <tr>
                                        <th>Notes</th>
                                        <td>{{ $purchaseOrder->po_note }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-12">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-condensed">
                            <thead>
                                <tr>
                                    <th class="text-center">S.No</th>
                                    <th class="text-center">Product Name</th>
                                    <th class="text-center">Quantity</th>
                                    <th class="text-center">Unit Price</th>
                                    <th class="text-center">Sub Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($purchaseOrderDetails as $key => $row)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td>{{ $row->productVariant->size_name ?? 'N/A' }} -
                                            {{ isset($row->productVariant->amount) ? number_format($row->productVariant->amount, 2) : '0.00' }}
                                        </td>
                                        <td class="text-right">{{ $row->qty ?? 0 }}</td>
                                        <td class="text-right">
                                            {{ isset($row->unit_price) ? number_format($row->unit_price, 2) : '0.00' }}
                                        </td>
                                        <td class="text-right">
                                            {{ isset($row->sub_total) ? number_format($row->sub_total, 2) : '0.00' }}
                                        </td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
    </div>
</div>
