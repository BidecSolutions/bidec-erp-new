@php
    use App\Helpers\CommonHelper;

    $data = [
        'type' => 1,
        'id' => $directSaleInvoice->id,
        'status' => $directSaleInvoice->status,
        'voucher_type_status' => $directSaleInvoice->dsi_status,
    ];

    // Totals
    $grossAmount = 0;
    foreach ($directSaleInvoiceDatas as $row) {
        $grossAmount += $row->total_amount;
    }
    $taxAmount = $directSaleInvoice->tax_amount ?? 0;
    $netAmount = $grossAmount + $taxAmount;
@endphp

<div class="row mb-3">
    <div class="col-lg-12 text-right">
        {!! CommonHelper::displayPrintButtonInBlade('PrintDirectSaleInvoiceDetail', '', '1') !!}
        {{ CommonHelper::getButtonsforDirectSaleInvoiceVouchers($data) }}
    </div>
</div>

<div class="invoice border rounded shadow-sm" id="PrintDirectSaleInvoiceDetail">
    <style>
        .invoice {
            background: #fff;
            padding: 30px;
            font-size: 14px;
            color: #000;
        }
        .invoice-header {
            border-bottom: 3px solid #28a745;
            margin-bottom: 20px;
            padding-bottom: 15px;
        }
        .invoice-header h2 {
            margin: 0;
            font-weight: 700;
            color: #28a745;
        }
        .invoice-header small {
            color: #6c757d;
        }
        .company-info {
            font-size: 13px;
            color: #444;
        }
        .table th {
            background: #f8f9fa;
            text-align: center;
            font-weight: 600;
        }
        .table td {
            vertical-align: middle;
        }
        .totals-row th {
            text-align: right;
        }
        .totals-row td {
            text-align: right;
            font-weight: 600;
        }
        .description-box {
            background: #f1f3f5;
            padding: 10px;
            border-radius: 5px;
        }
        .signature-box {
            margin-top: 50px;
        }
        .signature-col {
            width: 30%;
            text-align: center;
            display: inline-block;
            margin: 0 2%;
        }
        .signature-col hr {
            border-top: 2px solid #000;
            margin-bottom: 5px;
        }
    </style>

    {{-- Header --}}
    <div class="invoice-header d-flex justify-content-between align-items-center">
        <div>
            <h2>Sales Invoice</h2>
            <small>Direct Sale Invoice</small>
        </div>
        <div>
            {{-- Dummy Company Logo --}}
            <img src="https://via.placeholder.com/120x60.png?text=Company+Logo" alt="Company Logo" style="height:60px;">
        </div>
    </div>

    {{-- Customer & Invoice Info --}}
    <div class="row mb-4">
        <div class="col-md-6">
            <h6 class="text-success">Invoice Info</h6>
            <table class="table table-sm table-borderless company-info">
                <tr><th>D.S.I. No:</th><td>{{ $directSaleInvoice->dsi_no }}</td></tr>
                <tr><th>D.S.I. Date:</th><td>{{ $directSaleInvoice->dsi_date }}</td></tr>
                <tr><th>Customer:</th><td>{{ $directSaleInvoice->customer ?? 'N/A' }}</td></tr>
            </table>
        </div>
        <div class="col-md-6">
            <h6 class="text-success">Payment Info</h6>
            <table class="table table-sm table-borderless company-info">
                <tr><th>Payment Type:</th><td>{{ $directSaleInvoice->paymentType }}</td></tr>
                <tr><th>Payment Rate:</th><td>{{ number_format($directSaleInvoice->payment_type_rate, 2) }}</td></tr>
                <tr><th>Notes:</th><td>{{ $directSaleInvoice->si_note }}</td></tr>
            </table>
        </div>
    </div>

    {{-- Description --}}
    @if(!empty($directSaleInvoice->main_description) && $directSaleInvoice->main_description !== '-')
        <div class="mb-4 description-box">
            <strong>Main Description:</strong> {{ $directSaleInvoice->main_description }}
        </div>
    @endif

    {{-- Items Table --}}
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Sub Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($directSaleInvoiceDatas as $key => $row)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>
                            {{ $row->product_name ?? 'N/A' }} - 
                            {{ $row->size_name ?? 'N/A' }}
                            <small>({{ isset($row->product_variant_amount) ? number_format($row->product_variant_amount, 2) : '0.00' }})</small>
                        </td>
                        <td class="text-center">{{ $row->qty ?? 0 }}</td>
                        <td class="text-right">{{ isset($row->rate) ? number_format($row->rate, 2) : '0.00' }}</td>
                        <td class="text-right">{{ isset($row->total_amount) ? number_format($row->total_amount, 2) : '0.00' }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="totals-row">
                    <th colspan="4">Gross Amount</th>
                    <td>{{ number_format($grossAmount, 2) }}</td>
                </tr>
                @if($taxAmount > 0)
                    <tr class="totals-row">
                        <th colspan="4">Tax ({{ $directSaleInvoice->tax_account_name }})</th>
                        <td>{{ number_format($taxAmount, 2) }}</td>
                    </tr>
                    <tr class="totals-row">
                        <th colspan="4">Net Amount</th>
                        <td>{{ number_format($netAmount, 2) }}</td>
                    </tr>
                @endif
            </tfoot>
        </table>
    </div>

    {{-- Signatures --}}
    <div class="signature-box text-center">
        <div class="signature-col">
            <hr>
            <p><strong>Prepared By</strong></p>
        </div>
        <div class="signature-col">
            <hr>
            <p><strong>Checked By</strong></p>
        </div>
        <div class="signature-col">
            <hr>
            <p><strong>Approved By</strong></p>
        </div>
    </div>
</div>
