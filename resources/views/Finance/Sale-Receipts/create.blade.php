@php
    use App\Helpers\CommonHelper;
@endphp

@extends('layouts.layouts')
@section('content')
<div class="well_N">
    <div class="boking-wrp dp_sdw">
        <div class="row">
            <div class="col-lg-6">{{ CommonHelper::displayPageTitle('Add New Sale Receipt Voucher') }}</div>
            <div class="col-lg-6 text-right">
                <a href="{{ route('sale-receipts.index') }}" class="btn btn-success btn-xs">View List</a>
            </div>
        </div>

        <div class="lineHeight">&nbsp;</div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
            </div>
        @endif

        <form method="POST" action="{{ route('sale-receipts.store') }}">
            @csrf

            <div class="row">
                <div class="col-lg-12">
                    <label>
                        <input type="radio" name="entry_option" value="1" onchange="toggleSaleEntryType()"> Direct Sale Invoice
                        <input type="radio" name="entry_option" value="2" checked onchange="toggleSaleEntryType()"> Sale Invoice
                    </label>

                    <div class="d-flex mt-2" style="gap:10px;">
                        <!-- Direct Sale Invoice Select -->
                        <select name="dsi_id" id="dsi_id" class="form-control entry-select" onchange="loadDSIDetails()">
                            <option value="">Select Direct Sale Invoice</option>
                            @foreach($getPendingDirectSaleInvoiceReceipts as $dsi)
                                <option value="{{ $dsi->id }}">
                                    {{ $dsi->dsi_no }} - {{ CommonHelper::changeDateFormat($dsi->dsi_date) }}
                                </option>
                            @endforeach
                        </select>

                        <!-- Sale Invoice Select -->
                        <select name="invoice_id" id="invoice_id" class="form-control entry-select" onchange="loadInvoiceDetails()">
                            <option value="">Select Sale Invoice</option>
                            @foreach($saleInvoices as $invoice)
                                <option value="{{ $invoice->id }}">
                                    {{ $invoice->invoice_no }} - {{ CommonHelper::changeDateFormat($invoice->invoice_date) }} - {{ $invoice->customer_name }} - {{ $invoice->amount }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="lineHeight">&nbsp;</div>
            <div class="loadSaleReceiptVoucherDetailSection"></div>
            <div class="lineHeight">&nbsp;</div>

            <div class="row">
                <div class="col-lg-12 text-right">
                    <button type="submit" class="btn btn-success btn-sm">Submit</button>
                </div>
            </div>

        </form>
    </div>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        $(".entry-select").select2();
        toggleSaleEntryType(); // Initialize default entry
    });

    function toggleSaleEntryType() {
        const type = $("input[name='entry_option']:checked").val();

        if(type === '1') { // Direct Sale Invoice
            $('#dsi_id').show().prop('required', true).prop('disabled', false);
            $('#invoice_id').prop('required', false).prop('disabled', true).val('');
        } else { // Sale Invoice
            $('#invoice_id').show().prop('required', true).prop('disabled', false);
            $('#dsi_id').prop('required', false).prop('disabled', true).val('');
        }

        $('.loadSaleReceiptVoucherDetailSection').html('');
        $(".entry-select").trigger('change.select2'); // refresh Select2 state
    }

    function loadDSIDetails() {
        const dsiId = $('#dsi_id').val();
        if(!dsiId) return $('.loadSaleReceiptVoucherDetailSection').html('');

        $('.loadSaleReceiptVoucherDetailSection').html('<div class="loader"></div>');

        $.get("{{ url('finance/sale-receipts/loadSaleReceiptVoucherDetailByDSINO') }}", { dsiId })
            .done(data => {
                $('.loadSaleReceiptVoucherDetailSection').html(data);
                chequeOptionEnableDisable();
            });
    }

    function loadInvoiceDetails() {
        const invoiceId = $('#invoice_id').val();
        if(!invoiceId) return $('.loadSaleReceiptVoucherDetailSection').html('');

        $('.loadSaleReceiptVoucherDetailSection').html('<div class="loader"></div>');

        $.get("{{ url('finance/sale-receipts/loadSaleReceiptVoucherDetailByInvoiceId') }}", { invoiceId })
            .done(data => {
                $('.loadSaleReceiptVoucherDetailSection').html(data);
                chequeOptionEnableDisable();
            });
    }

    function chequeOptionEnableDisable() {
        const voucherType = $('#voucher_type').val();
        if(voucherType == 1){
            $('#chequeDetail').addClass('hidden');
        } else {
            $('#chequeDetail').removeClass('hidden');
        }
    }
</script>
@endsection
