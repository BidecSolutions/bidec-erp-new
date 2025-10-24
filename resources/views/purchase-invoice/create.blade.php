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
                {{ CommonHelper::displayPageTitle('Add New Purchase Invoice') }}
            </div>
            <div class="col-md-6 text-right">
                <a href="{{ route('purchase-invoice.index') }}" class="btn btn-success btn-xs">
                    View List
                </a>
            </div>
        </div>

        {{-- Purchase Invoice Form --}}
        <form method="POST" action="{{ route('purchase-invoice.store') }}">
            @csrf

            <div class="row">
                {{-- Purchase Invoice Date --}}
                <div class="col-md-3 mb-3">
                    <label for="pi_date">Purchase Invoice Date <span class="text-danger">*</span></label>
                    <input type="date"
                           id="pi_date"
                           name="pi_date"
                           class="form-control @error('pi_date') is-invalid @enderror"
                           value="{{ old('pi_date', date('Y-m-d')) }}">
                    @error('pi_date')
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
                           value="{{ old('slip_no') ?? '-' }}">
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
                           value="{{ old('description') }}">
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                {{-- Supplier --}}
                <div class="col-md-4 mb-3">
                    <label for="supplier_id">Supplier Name <span class="text-danger">*</span></label>
                    <select id="supplier_id"
                            name="supplier_id"
                            class="form-control select2 @error('supplier_id') is-invalid @enderror">
                        <option value="">Select Supplier Name</option>
                        @foreach($suppliers as $sRow)
                            <option value="{{ $sRow['id'] }}" {{ old('supplier_id') == $sRow['id'] ? 'selected' : '' }}>
                                {{ $sRow['name'] }}
                            </option>
                        @endforeach
                    </select>
                    @error('supplier_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Debit Account --}}
                <div class="col-md-4 mb-3">
                    <label for="debit_account_id">Debit Account <span class="text-danger">*</span></label>
                    <select id="debit_account_id"
                            name="debit_account_id"
                            class="form-control select2 @error('debit_account_id') is-invalid @enderror">
                        
                        @if(count($purchaseInvoiceSetting) != 0)
                            @foreach($purchaseInvoiceSetting as $row)
                                <option value="{{ $row->id }}" {{ old('debit_account_id') == $row->id ? 'selected' : '' }}>
                                    {{ $row->code }} — {{ $row->name }}
                                </option>
                            @endforeach
                        @else
                            <option value="">Select Parent Code</option>
                            @foreach(CommonHelper::get_all_chart_of_account(1) as $row)
                                <option value="{{ $row->id }}" {{ old('debit_account_id') == $row->id ? 'selected' : '' }}>
                                    {{ $row->code }} — {{ $row->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    @error('debit_account_id')
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
                           value="{{ old('amount') }}">
                    @error('amount')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="row mt-3">
                <div class="col-md-12 text-right">
                    <button type="reset" class="btn btn-primary">Clear Form</button>
                    <button type="submit" class="btn btn-success btn-sm">Submit</button>
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
