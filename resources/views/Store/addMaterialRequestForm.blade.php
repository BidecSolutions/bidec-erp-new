@extends('layouts.layouts')

@section('content')
<link href="{{ asset('assets/select2/select2.css') }}" rel="stylesheet">
<script src="{{ asset('assets/select2/select2.full.min.js') }}"></script>

<div class="well_N">
    <div class="boking-wrp dp_sdw">
        <div class="row">
            <div class="col-lg-12">
                <div class="well">
                    <span class="subHeadingLabelClass">Create Material Request Form</span>
                    <div class="lineHeight">&nbsp;</div>

                    {{ Form::open(['url' => 'stad/addMaterialRequestDetail?m='.Session::get('company_id'), 'id' => 'addMaterialRequestDetail']) }}
                    @csrf

                    {{-- Basic Info Section --}}
                    <div class="panel">
                        <div class="panel-body">
                            <input type="hidden" name="materialRequestsSection[]" value="1">

                            <div class="row">
                                <div class="col-lg-6">
                                    <label>Material Request Date <span class="rflabelsteric">*</span></label>
                                    <input type="date" class="form-control requiredField" name="material_request_date_1" value="{{ now()->format('Y-m-d') }}">
                                </div>
                                <div class="col-lg-6">
                                    <label>Requested Department / Sub Department <span class="rflabelsteric">*</span></label>
                                    <select name="sub_department_id_1" class="form-control requiredField select2">
                                        <option value="">Select Department</option>
                                        @foreach($departments as $dept)
                                            <option value="{{ $dept['id'] }}">{{ $dept['department_name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-lg-12">
                                    <label>Remarks <span class="rflabelsteric">*</span></label>
                                    <textarea name="description_1" rows="4" class="form-control requiredField"></textarea>
                                </div>
                            </div>

                            {{-- Table Section --}}
                            <div class="lineHeight">&nbsp;</div>
                            <div class="well">
                                <div class="panel">
                                    <div class="panel-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Product/Variant *</th>
                                                        <th style="width:150px;">Qty in Unit *</th>
                                                        <th style="width:150px;">Description</th>
                                                        <th style="width:100px;">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="addMoreMaterialRequestsDetailRows_1">
                                                    {{-- Default row --}}
                                                    @include('Store.partials.material_request_row', ['products' => $products, 'id' => 1, 'counter' => 1])
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="text-right">
                                            <button type="button" class="btn btn-sm btn-primary" onclick="addMoreMaterialRequestsDetailRows(1)">
                                                Add More Material Request's Rows
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Submit Section --}}
                            <div class="text-right">
                                <button type="submit" class="btn btn-success btnSubmit">Submit</button>
                                <button type="reset" class="btn btn-primary">Clear Form</button>
                            </div>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- JS --}}
<script>
$(document).ready(function() {
    $("select").select2();
});

let rowCounter = 1;
function addMoreMaterialRequestsDetailRows(id) {
    rowCounter++;
    const m = "{{ Session::get('company_id') }}";
    $.ajax({
        url: "{{ url('stmfal/addMoreMaterialRequestsDetailRows') }}",
        type: "GET",
        data: { counter: rowCounter, id: id, m: m },
        success: function(data) {
            $("#addMoreMaterialRequestsDetailRows_" + id).append(data);
        }
    });
}

function removeMaterialRequestsRows(id, counter) {
    $("#removeMaterialRequestsRows_" + id + "_" + counter).remove();
}
</script>
@endsection
