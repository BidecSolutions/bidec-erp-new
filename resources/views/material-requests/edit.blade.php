@php
    use App\Helpers\CommonHelper;
@endphp
@extends('layouts.layouts')
@section('content')
<div class="well_N">
    <div class="boking-wrp dp_sdw">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                {{ CommonHelper::displayPageTitle('Edit Material Request') }}
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 text-right">
                <a href="{{ route('purchase-orders.index') }}" class="btn btn-success btn-xs">+ View List</a>
            </div>
        </div>
        <div class="row">
            <form method="POST" action="{{ route('material-requests.update', $materialRequest->id) }}">
                @csrf
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <label class="sf-label">M.R Date <span class="rflabelsteric">*</span></label>
                                <input type="date" value="{{ old('mr_date', $materialRequest->material_request_date) }}" name="mr_date" class="form-control requiredField" />
                            </div>

                            <div class="col-lg-3">
                                <label class="sf-label">Department</label>
                                <select class="form-control select2" name="department_id" id="department_id">
                                    @foreach ($departments as $dRow)
                                        <option value="{{ $dRow['id'] }}" {{ $dRow['id'] == $materialRequest->department_id ? 'selected' : '' }}>
                                            {{ $dRow['department_name'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-lg-6">
                                <label class="sf-label">Remarks <span class="rflabelsteric">*</span></label>
                                <textarea name="main_description" rows="2" class="form-control" style="resize:none;">{{ old('main_description', $materialRequest->main_description) }}</textarea>
                            </div>
                        </div>

                        <div class="lineHeight">&nbsp;</div>

                        <div class="table-responsive">
                            <table class="table table-bordered sf-table-list" id="materialRequestTable">
                                <thead>
                                    <tr>
                                        <th class="text-center">Product</th>
                                        <th class="text-center">Qty</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($materialRequestData as $index => $data)
                                        <tr id="row_{{ $index }}">
                                            <td>
                                                <select name="mrDataArray[{{ $index }}][product_id]" class="form-control select2">
                                                    <option value="">Select Product Detail</option>
                                                    @foreach($products as $product)
                                                        <optgroup label="{{ $product['name'] }}">
                                                            @foreach($product['variants'] as $variant)
                                                                <option value="{{ $variant['id'] }}" {{ $data->product_variant_id == $variant['id'] ? 'selected' : '' }}>
                                                                    {{ $variant['size_name'] }} - {{ number_format($variant['amount'], 2) }}
                                                                </option>
                                                            @endforeach
                                                        </optgroup>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" name="mrDataArray[{{ $index }}][qty]" value="{{ $data->qty }}" class="form-control" min="1" />
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-danger btn-sm" onclick="removeMaterialRequestRow({{ $index }})">Remove</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <div>
                                <input type="button" class="btn btn-sm btn-primary" onclick="addMoreMaterialRequestsDetailRows()" value="Add More Rows" />
                            </div>
                        </div>

                        <div class="lineHeight">&nbsp;</div>

                        <div class="text-right">
                            <button type="reset" id="reset" class="btn btn-primary">Clear Form</button>
                            <button type="submit" class="btn btn-sm btn-success">Submit</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let rowCounter = {{ count($materialRequestData) }};

function addMoreMaterialRequestsDetailRows() {
    const newRow = `
        <tr id="row_${rowCounter}">
            <td>
                <select name="mrDataArray[${rowCounter}][product_id]" class="form-control select2">
                    <option value="">Select Product Detail</option>
                    @foreach($products as $product)
                        <optgroup label="{{ $product['name'] }}">
                            @foreach($product['variants'] as $variant)
                                <option value="{{ $variant['id'] }}">
                                    {{ $variant['size_name'] }} - {{ number_format($variant['amount'], 2) }}
                                </option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="number" name="mrDataArray[${rowCounter}][qty]" value="" class="form-control" min="1" />
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-danger btn-sm" onclick="removeMaterialRequestRow(${rowCounter})">Remove</button>
            </td>
        </tr>`;
    
    $('#materialRequestTable tbody').append(newRow);
    $('.select2').select2();
    rowCounter++;
}

function removeMaterialRequestRow(rowId) {
    $(`#row_${rowId}`).remove();
}

// Prevent duplicate variants
$('form').on('submit', function (e) {
    const selected = [];
    let duplicate = false;

    $('select[name^="mrDataArray"]').each(function () {
        const value = $(this).val();
        if (value && selected.includes(value)) {
            duplicate = true;
            return false;
        }
        selected.push(value);
    });

    if (duplicate) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Duplicate Product',
            text: 'A product cannot be added more than once in the same Material Request.',
            confirmButtonColor: '#d33'
        });
    }
});
</script>
@endsection
