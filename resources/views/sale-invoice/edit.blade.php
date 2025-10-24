@php
    use App\Helpers\CommonHelper;
@endphp

@extends('layouts.layouts')

@section('content')
<div class="well_N">
    <div class="boking-wrp dp_sdw">
        {{-- Page Title & Action Button --}}
        <div class="row mb-3">
            <div class="col-md-6">
                {{ CommonHelper::displayPageTitle('Edit Sale Invoice') }}
            </div>
            <div class="col-md-6 text-right">
                <a href="{{ route('sale-invoice.index') }}" class="btn btn-success btn-xs">
                    View List
                </a>
            </div>
        </div>

        {{-- Sale Invoice Edit Form --}}
        <form method="POST" action="{{ route('sale-invoice.update', $saleInvoice->id) }}">
            @csrf
            @method('POST')

            <div class="row">
                {{-- Sale Invoice Date --}}
                <div class="col-md-3 mb-3">
                    <label for="si_date">Sale Invoice Date <span class="text-danger">*</span></label>
                    <input type="date"
                           id="si_date"
                           name="si_date"
                           class="form-control @error('si_date') is-invalid @enderror"
                           value="{{ old('si_date', $saleInvoice->invoice_date) }}">
                    @error('si_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Slip No --}}
                <div class="col-md-3 mb-3">
                    <label for="slip_no">Slip No <span class="text-danger">*</span></label>
                    <input type="text"
                           id="slip_no"
                           name="slip_no"
                           class="form-control @error('slip_no') is-invalid @enderror"
                           value="{{ old('slip_no', $saleInvoice->slip_no) }}">
                    @error('slip_no')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                {{-- Description --}}
                <div class="col-md-12 mb-3">
                    <label for="description">Description <span class="text-danger">*</span></label>
                    <input type="text"
                           id="description"
                           name="description"
                           class="form-control @error('description') is-invalid @enderror"
                           value="{{ old('description', $saleInvoice->description) }}">
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                {{-- Customer --}}
                <div class="col-md-4 mb-3">
                    <label for="customer_id">Customer Name <span class="text-danger">*</span></label>
                    <select id="customer_id"
                            name="customer_id"
                            class="form-control select2 @error('customer_id') is-invalid @enderror">
                        <option value="">Select Customer Name</option>
                        @foreach($customers as $cRow)
                            <option value="{{ $cRow['id'] }}" 
                                {{ old('customer_id', $saleInvoice->customer_id) == $cRow['id'] ? 'selected' : '' }}>
                                {{ $cRow['name'] }}
                            </option>
                        @endforeach
                    </select>
                    @error('customer_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Credit Account --}}
                <div class="col-md-4 mb-3">
                    <label for="credit_account_id">Credit Account <span class="text-danger">*</span></label>
                    <select id="credit_account_id"
                            name="credit_account_id"
                            class="form-control select2 @error('credit_account_id') is-invalid @enderror">
                        @if(count($saleInvoiceSetting) != 0)
                            @foreach($saleInvoiceSetting as $row)
                                <option value="{{ $row->id }}" 
                                    {{ old('credit_account_id', $saleInvoice->credit_account_id) == $row->id ? 'selected' : '' }}>
                                    {{ $row->code }} — {{ $row->name }}
                                </option>
                            @endforeach
                        @else
                            <option value="">Select Parent Code</option>
                            @foreach(CommonHelper::get_all_chart_of_account(1) as $row)
                                <option value="{{ $row->id }}" 
                                    {{ old('credit_account_id', $saleInvoice->credit_account_id) == $row->id ? 'selected' : '' }}>
                                    {{ $row->code }} — {{ $row->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    @error('credit_account_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Transaction Amount --}}
                <div class="col-md-4 mb-3">
                    <label for="amount">Transaction Amount <span class="text-danger">*</span></label>
                    <input type="number"
                           id="amount"
                           name="amount"
                           class="form-control @error('amount') is-invalid @enderror"
                           value="{{ old('amount', $saleInvoice->amount) }}">
                    @error('amount')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="row mt-3">
                <div class="col-md-12 text-right">
                    <a href="{{ route('sale-invoice.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-success btn-sm">Update</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function () {
        $('.select2').select2();
    });
</script>
@endsection
