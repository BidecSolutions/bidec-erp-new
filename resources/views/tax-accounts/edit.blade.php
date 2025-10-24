@php
    use App\Helpers\CommonHelper;
@endphp
@extends('layouts.layouts')
@section('content')
    <div class="well_N">
        <div class="boking-wrp dp_sdw">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    {{ CommonHelper::displayPageTitle('Edit Tax Account Detail') }}
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 text-right">
                    <a href="{{ route('tax-accounts.index') }}" class="btn btn-success btn-xs">+ View List</a>
                </div>
            </div>
            <div class="row">
                <form method="POST" action="{{ route('tax-accounts.update', $taxAccount->id) }}">
                    @csrf
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="row">
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <label>Account Name</label>
                                <input type="hidden" name="taxAccount_acc_id" id="taxAccount_acc_id" value="{{$taxAccount->acc_id}}" />
                                <input type="hidden" name="old_acc_id" id="old_acc_id" value="{{$taxAccount->parent_code}}" />
                                <select name="acc_id" id="acc_id" class="form-control select2">
                                    @foreach ($chartOfAccountList as $coalRow)
                                        <option value="{{ $coalRow->code }}"
                                            {{ $taxAccount->parent_code == $coalRow->code ? 'selected' : '' }}>
                                            {{ $coalRow->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <label>Name</label>
                                <input type="text" name="name"
                                    class="form-control @error('name') border border-danger @enderror" id="name"
                                    value="{{ old('name', $taxAccount->name) }}" />
                                @error('name')
                                    <div class="text-sm text-danger text-red-600">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <label>Percent Value</label>
                                <input type="text" name="percent_value"
                                    class="form-control @error('percent_value') border border-danger @enderror" id="percent_value"
                                    value="{{ old('percent_value', $taxAccount->percent_value) }}" />
                                @error('percent_value')
                                    <div class="text-sm text-danger text-red-600">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="lineHeight">&nbsp;</div>
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">
                                <button type="reset" id="reset" class="btn btn-primary">Clear Form</button>
                                <button type="submit" class="btn btn-sm btn-success">Update</button>
                            </div>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection
