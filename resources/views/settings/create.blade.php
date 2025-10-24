@php
    use App\Helpers\CommonHelper;
@endphp
@extends('layouts.layouts')
@section('content')
<script src="https://cdn.ckeditor.com/ckeditor5/36.0.1/classic/ckeditor.js"></script>
<div class="well_N">
	<div class="boking-wrp dp_sdw">
	    <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                {{CommonHelper::displayPageTitle('Add New Settings')}}
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 text-right">
                <a href="{{ route('settings.index') }}" class="btn btn-success btn-xs"><span></span> View List</a>
            </div>
        </div>
        <form method="POST" action="{{ route('settings.store') }}">
            @csrf
            <input type="hidden" name="company_id" id="company_id" value="{{Session::get('company_id')}}" />
            <input type="hidden" name="company_location_id" id="company_location_id" value="{{Session::get('company_location_id')}}" />
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <label>Fee Voucher Footer Description</label>
                    <textarea name="fee_voucher_footer_description" id="fee_voucher_footer_description"></textarea>
                </div>
            </div>
            <div class="lineHeight">&nbsp;</div>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">
                    <button type="reset" id="reset" class="btn btn-primary">Clear Form</button>
                    <button type="submit" class="btn btn-sm btn-success">Submit</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
@section('script')
    <script>
        // Initialize CKEditor
        ClassicEditor
            .create(document.querySelector('#fee_voucher_footer_description'))
            .catch(error => {
                console.error(error);
            });
    </script>
@endsection