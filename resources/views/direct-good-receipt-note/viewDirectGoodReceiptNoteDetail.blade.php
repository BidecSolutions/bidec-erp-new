@php
    use App\Helpers\CommonHelper;

    $data = [
        'type' => 2,
        'id' => $goodReceiptNoteDetail->id,
        'status' => $goodReceiptNoteDetail->status,
        'voucher_type_status' => $goodReceiptNoteDetail->grn_status,
    ];

    // Totals
    $grossAmount = 0;
    foreach ($goodReceiptNoteDataDetails as $row) {
        $grossAmount += $row->po_sub_total;
    }
    $taxAmount = $goodReceiptNoteDetail->tax_amount ?? 0;
    $netAmount = $grossAmount + $taxAmount;
@endphp

<div class="row mb-3">
    <div class="col-lg-12 text-right">
        {!! CommonHelper::displayPrintButtonInBlade('PrintGoodReceiptNoteDetail', '', '1') !!}
        {{ CommonHelper::getButtonsforPurchaseOrdersAndGoodReceiptNoteVouchers($data) }}
    </div>
</div>

<div class="invoice border rounded shadow-sm" id="PrintGoodReceiptNoteDetail">
    <style>
        .invoice {
            background: #fff;
            padding: 30px;
            font-size: 14px;
            color: #000;
        }
        .invoice-header {
            border-bottom: 3px solid #205b60;
            margin-bottom: 20px;
            padding-bottom: 15px;
        }
        .invoice-header h2 {
            margin: 0;
            font-weight: 700;
            color: #205b60;
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
        .terms-box {
            margin-top: 40px;
            font-size: 13px;
        }
        .terms-box h6 {
            font-weight: 600;
            margin-bottom: 10px;
        }
    </style>

    {{-- Header --}}
    <div class="invoice-header d-flex justify-content-between align-items-center">
        <div>
            <h2>Purchase Invoice</h2>
            <small>Goods Receipt Note</small>
        </div>
        <div>
            {{-- Dummy Company Logo --}}
            <img src="https://via.placeholder.com/120x60.png?text=Company+Logo" alt="Company Logo" style="height:60px;">
        </div>
    </div>

    {{-- Company & Supplier Info --}}
    <div class="row mb-4">
        <div class="col-md-6">
            <h6 class="text-primary">Company Info</h6>
            <table class="table table-sm table-borderless company-info">
                <tr><th>Name:</th><td>ABC Corporation Pvt Ltd</td></tr>
                <tr><th>Address:</th><td>123 Business Street, Karachi, Pakistan</td></tr>
                <tr><th>Phone:</th><td>+92 300 1234567</td></tr>
                <tr><th>Email:</th><td>info@abccorp.com</td></tr>
            </table>
        </div>
        <div class="col-md-6">
            <h6 class="text-primary">Supplier Info</h6>
            <table class="table table-sm table-borderless company-info">
                <tr><th>Name:</th><td>{{ $goodReceiptNoteDetail->supplier ?? 'N/A' }}</td></tr>
                <tr><th>Address:</th><td>Supplier Address, City</td></tr>
                <tr><th>Phone:</th><td>+92 300 7654321</td></tr>
                <tr><th>Email:</th><td>supplier@email.com</td></tr>
            </table>
        </div>
    </div>

    {{-- GRN Details --}}
    <div class="row mb-4">
        <div class="col-md-6">
            <h6 class="text-primary">GRN Details</h6>
            <table class="table table-sm table-borderless company-info">
                <tr><th>G.R.N. No:</th><td>{{ $goodReceiptNoteDetail->grn_no }}</td></tr>
                <tr><th>G.R.N. Date:</th><td>{{ $goodReceiptNoteDetail->grn_date }}</td></tr>
                @if(!empty($goodReceiptNoteDetail->quotation_no))
                    <tr><th>Invoice/Quotation:</th><td>{{ $goodReceiptNoteDetail->quotation_no }}</td></tr>
                @endif
            </table>
        </div>
    </div>

    {{-- Description --}}
    @if(!empty($goodReceiptNoteDetail->description) && $goodReceiptNoteDetail->description !== '-')
        <div class="mb-4 description-box">
            <strong>Description:</strong> {{ $goodReceiptNoteDetail->description }}
        </div>
    @endif

    {{-- Items Table --}}
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Product</th>
                    <th>Purchase Order</th>
                    <th>Rate</th>
                    <th>Qty</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($goodReceiptNoteDataDetails as $key => $row)
                    <tr>
                        <td class="text-center">{{ $key+1 }}</td>
                        <td>
                            {{ $row->product_name ?? 'N/A' }} - 
                            {{ $row->size_name ?? 'N/A' }}
                            <small>({{ isset($row->product_variant_amount) ? number_format($row->product_variant_amount,2) : '0.00' }})</small>
                        </td>
                        <td>{{ $row->po_no }} - {{ CommonHelper::changeDateFormat($row->po_date) }}</td>
                        <td class="text-right">{{ number_format($row->po_unit_price ?? 0,2) }}</td>
                        <td class="text-center">{{ $row->receive_qty ?? 0 }}</td>
                        <td class="text-right">{{ number_format($row->po_sub_total ?? 0,2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="totals-row">
                    <th colspan="5">Gross Amount</th>
                    <td>{{ number_format($grossAmount,2) }}</td>
                </tr>
                @if($taxAmount > 0)
                    <tr class="totals-row">
                        <th colspan="5">Tax ({{ $goodReceiptNoteDetail->tax_account_name }})</th>
                        <td>{{ number_format($taxAmount,2) }}</td>
                    </tr>
                    <tr class="totals-row">
                        <th colspan="5">Net Amount</th>
                        <td>{{ number_format($netAmount,2) }}</td>
                    </tr>
                @endif
            </tfoot>
        </table>
    </div>

    {{-- Terms & Conditions --}}
    <div class="terms-box">
        <h6 class="text-primary">Terms & Conditions</h6>
        <ul>
            <li>All goods once sold will not be returned or exchanged.</li>
            <li>Payment is due within 30 days of invoice date.</li>
            <li>Late payments will incur additional charges.</li>
            <li>All disputes are subject to Karachi jurisdiction only.</li>
        </ul>
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
