@extends('layouts.layouts')
@section('content')
<div class="well_N">
    <div class="boking-wrp dp_sdw">
        <div class="row">
            <div class="col-lg-6">{{ \App\Helpers\CommonHelper::displayPageTitle('Add New Purchase Payment Voucher') }}</div>
            <div class="col-lg-6 text-right">
                <a href="{{ route('purchase-payments.index') }}" class="btn btn-success btn-xs">View List</a>
            </div>
        </div>
        <div class="lineHeight">&nbsp;</div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
            </div>
        @endif

        <form method="POST" action="{{ route('purchase-payments.store') }}">
            @csrf

            <div class="row">
                <div class="col-lg-12">
                    <label>
                        <input type="radio" name="entry_option" value="1" onchange="toggleEntryType()"> PO / GRN
                        <input type="radio" name="entry_option" value="2" checked onchange="toggleEntryType()"> Purchase Invoice
                    </label>

                    <!-- Both selects in one row -->
                    <div class="d-flex mt-2" style="gap:10px;">
                        <!-- PO / GRN Select -->
                        <select name="po_id" id="po_id" class="form-control entry-select" onchange="loadPODetails()">
                            <option value="">Select PO</option>
                            @foreach($pendingPOs as $po)
                                <option value="{{ $po->id }}">
                                    {{ $po->po_no }} - {{ \App\Helpers\CommonHelper::changeDateFormat($po->po_date) }} - {{ $po->supplier_name }} - 
                                    {{ $po->grn_no ? $po->grn_no . ' - ' . \App\Helpers\CommonHelper::changeDateFormat($po->grn_date) : 'GRN not created' }}
                                </option>
                            @endforeach
                        </select>

                        <!-- Purchase Invoice Select (Multiple) -->
                        <select name="invoice_ids[]" id="invoice_id" class="form-control entry-select" multiple="multiple" onchange="loadInvoiceDetails()">
                            @foreach($purchaseInvoices as $invoice)
                                <option value="{{ $invoice->id }}">
                                    {{ $invoice->invoice_no }} - {{ \App\Helpers\CommonHelper::changeDateFormat($invoice->invoice_date) }} - {{ $invoice->supplier_name }} - I.A.: {{ $invoice->amount }} - R.A.: {{$invoice->remaining_amount}}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="lineHeight">&nbsp;</div>
            <div class="loadDetailsSection"></div>
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
        $(".entry-select").select2({
            placeholder: "Select options",
            allowClear: true
        });

        // Initialize selects: show PO by default
        toggleEntryType();
    });

    function toggleEntryType() {
        const type = $("input[name='entry_option']:checked").val();
        if(type === '1') {
            $('#po_id').show().prop('required', true).prop('disabled', false);
            $('#invoice_id').hide().prop('required', false).prop('disabled', true).val(null).trigger('change');
        } else {
            $('#invoice_id').show().prop('required', true).prop('disabled', false);
            $('#po_id').hide().prop('required', false).prop('disabled', true).val('').trigger('change');
        }
        $('.loadDetailsSection').html('');
    }

    function loadPODetails() {
        const poId = $('#po_id').val();
        if(!poId) return $('.loadDetailsSection').html('');
        $('.loadDetailsSection').html('<div class="loader"></div>');

        $.get("{{ url('finance/purchase-payments/loadPurchasePaymentVoucherDetailByPONo') }}", { poId })
            .done(data => {
                $('.loadDetailsSection').html(data);
            });
    }

    function loadInvoiceDetails() {
        const invoiceIds = $('#invoice_id').val();
        if(!invoiceIds || invoiceIds.length === 0) return $('.loadDetailsSection').html('');
        $('.loadDetailsSection').html('<div class="loader"></div>');

        $.get("{{ url('finance/purchase-payments/loadPurchasePaymentVoucherDetailByInvoiceId') }}", { 
            invoiceIds: invoiceIds 
        })
        .done(data => {
            $('.loadDetailsSection').html(data);
        });
    }

    function chequeOptionEnableDisable(){
        var voucherType = $('#voucher_type').val();
        if(voucherType == 1){
            $('#chequeDetail').addClass('hidden');
        }else{
            $('#chequeDetail').removeClass('hidden');
        }
    }
</script>
@endsection