@php
    use App\Helpers\CommonHelper;
    $data = array(
        'type' => 1,
        'id' => $purchaseInvoiceDetail->id,
        'status' => $purchaseInvoiceDetail->status,
        'voucher_status' => $purchaseInvoiceDetail->voucher_status
    );
@endphp
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        {{CommonHelper::getButtonsforPurchaseAndSaleInvoiceVouchers($data)}}
    </div>
</div>
<div class="container my-5" id="PrintPurchaseInvoiceDetail">
    <div class="card shadow-lg border-0 rounded-4">
        <!-- Header -->
        <div class="card-header bg-dark text-white text-center py-4">
            <h3 class="mb-1 fw-bold text-uppercase">Purchase Invoice</h3>
            <small class="text-light">#{{ $purchaseInvoiceDetail->invoice_no }}</small>
        </div>
        <!-- Body -->
        <div class="card-body p-4">
            <div class="row mb-4">
                <!-- Invoice Info -->
                <div class="col-md-6">
                    <h6 class="fw-bold text-secondary">Invoice Information</h6>
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th class="text-muted" width="40%">Invoice Type:</th>
                            <td>{{ $purchaseInvoiceDetail->invoice_type }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">Slip No:</th>
                            <td>{{ $purchaseInvoiceDetail->slip_no }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">Amount:</th>
                            <td><span class="fw-bold text-success">
                                {{ number_format($purchaseInvoiceDetail->amount, 2) }}
                            </span></td>
                        </tr>
                        <tr>
                            <th class="text-muted">Description:</th>
                            <td>{{ $purchaseInvoiceDetail->description }}</td>
                        </tr>
                    </table>
                </div>

                <!-- Company Info -->
                <div class="col-md-6">
                    <h6 class="fw-bold text-secondary">Company & Supplier</h6>
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th class="text-muted" width="40%">Supplier:</th>
                            <td>{{ $purchaseInvoiceDetail->supplier_name }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">Company:</th>
                            <td>{{ $purchaseInvoiceDetail->company_name }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">Location:</th>
                            <td>{{ $purchaseInvoiceDetail->company_location_name }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">JV No / Date:</th>
                            <td>
                                {{ $purchaseInvoiceDetail->jv_no }} 
                                ({{ CommonHelper::changeDateformat($purchaseInvoiceDetail->jv_date) }})
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <hr>

            <!-- Signature Section -->
            <div class="row mt-5">
                <div class="col-md-6 text-start">
                    <p class="fw-bold mb-5">__________________________</p>
                    <small class="text-muted">Authorized Signature</small>
                </div>
                <div class="col-md-6 text-end">
                    <p class="fw-bold mb-5">__________________________</p>
                    <small class="text-muted">Company Stamp</small>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <!-- <div class="card-footer bg-light text-end">
            <button onclick="window.print()" class="btn btn-dark btn-sm">
                <i class="fa fa-print"></i> Print Invoice
            </button>
        </div> -->
    </div>
</div>
