@php
    use App\Helpers\CommonHelper;
@endphp
@extends('layouts.layouts')
@section('content')
<div class="well_N">
	<div class="boking-wrp dp_sdw">
	    <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                {{CommonHelper::displayPageTitle('Add New Material Request')}}
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 text-right">
                <a href="{{ route('material-requests.index') }}" class="btn btn-success btn-xs">+ View List</a>
            </div>
        </div>
        <div class="row">


            <form method="POST" action="{{ route('material-requests.store') }}">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    @csrf
                    
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="panel-body">
                                        
                                    <div class="row">
                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                            <label class="sf-label">M.R Date.</label>
                                            <span class="rflabelsteric"><strong>*</strong></span>
                                            <input type="date" class="form-control requiredField" name="mr_date" id="mr_date" value="{{date('Y-m-d')}}" />
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                            <label class="sf-label">Department</label>
                                            <select class="form-control select2" name="department_id" id="department_id">
                                                <option value="">Select Department</option>
                                                @foreach($departments as $dRow)
                                                    <option value="{{$dRow['id']}}" >{{$dRow['department_name']}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                            <label class="sf-label">Remarks</label>
                                            <span class="rflabelsteric"><strong>*</strong></span>
                                            <textarea name="main_description" id="main_description" rows="2" cols="50" style="resize:none;" class="form-control">-</textarea>
                                        </div>
                                    </div>
                                    <div class="lineHeight">&nbsp;</div>
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                            <div class="table-responsive">
                                                <table class="table table-bordered sf-table-list" id="materialRequestTable">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center">Product</th>
                                                            <th class="text-center">Qty.</th>
                                                            <th class="text-center">Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr id="row_1">
                                                            <td>
                                                                <input type="hidden" name="mrDataArray[]" id="mrDataArray" value="1" />
                                                                <select name="productId_1" id="productId_1" class="form-control requiredField select2">
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
                                                                <input type="number" name="qty_1" id="qty_1" value="" class="form-control" />
                                                            </td>
                                                            <td class="text-center">
                                                                ---
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <div>
                                                    <input type="button" class="btn btn-sm btn-primary" onclick="addMoreMaterialRequestsDetailRows()" value="Add More Rows" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="lineHeight">&nbsp;</div>
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">
                            <button type="reset" id="reset" class="btn btn-primary">Clear Form</button>
                            <button type="submit" class="btn btn-sm btn-success">Submit</button>
                        </div>
                    </div>
                </div>
</form> 
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    var rowCounter = 1; // Keep track of the row numbers
    function addMoreMaterialRequestsDetailRows() {
        rowCounter++;
        var newRow = `
            <tr id="row_${rowCounter}">
                <td>
                    <input type="hidden" name="mrDataArray[]" id="mrDataArray" value="${rowCounter}" />
                    <select name="productId_${rowCounter}" id="productId_${rowCounter}" class="form-control requiredField new-select2">
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
                    <input type="number" name="qty_${rowCounter}" id="qty_${rowCounter}" value="" class="form-control" />
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm" onclick="removeMaterialRequestRow(${rowCounter})">Remove</button>
                </td>
            </tr>`;
        $('#materialRequestTable tbody').append(newRow);
        $('.new-select2').select2();
    }

    function removeMaterialRequestRow(rowId) {
        $(`#row_${rowId}`).remove();
    }

    // Prevent duplicate product variants
    $('form').on('submit', function (e) {
        const selectedProducts = [];
        let hasDuplicate = false;
        $('select[name^="productId_"]').each(function () {
            const value = $(this).val();
            if (value) {
                if (selectedProducts.includes(value)) {
                    hasDuplicate = true;
                    return false;
                }
                selectedProducts.push(value);
            }
        });
        if (hasDuplicate) {
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
