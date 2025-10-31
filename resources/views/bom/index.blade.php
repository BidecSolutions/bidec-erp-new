@php
    use App\Helpers\CommonHelper;
@endphp
@extends('layouts.layouts')
@section('content')
<div class="well_N">
    <div class="boking-wrp dp_sdw">
        <div class="row">
            <div class="col-lg-6">
                {{ CommonHelper::displayPageTitle('Edit BOM') }}
            </div>
            <div class="col-lg-6 text-right">
                <a href="{{ route('bom.index') }}" class="btn btn-success btn-xs">+ View List</a>
            </div>
        </div>

        <form method="POST" action="{{ route('bom.update', $bom->id) }}">
            @csrf
            @method('PUT')
            <div class="panel-body">
                <div class="row mb-3">
                    <div class="col-lg-3">
                        <label class="sf-label">BOM Date <span class="text-danger">*</span></label>
                        <input type="date" name="bom_date" value="{{ old('bom_date', $bom->bom_date) }}" class="form-control" required>
                    </div>
                    <div class="col-lg-3">
                        <label class="sf-label">BOM No</label>
                        <input type="text" readonly value="{{ $bom->bom_no }}" class="form-control">
                    </div>
                    <div class="col-lg-6">
                        <label class="sf-label">Remarks</label>
                        <textarea name="remarks" rows="2" class="form-control">{{ old('remarks', $bom->remarks) }}</textarea>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-lg-4">
                        <label class="sf-label">Finish Product <span class="text-danger">*</span></label>
                        <select name="finish_product_id" class="form-control" required>
                            <option value="">Select Finish Product</option>
                            @foreach ($finishProducts as $product)
                                <optgroup label="{{ $product['name'] }}">
                                    @foreach ($product['variants'] as $variant)
                                        <option value="{{ $variant['id'] }}" 
                                            {{ $variant['id'] == $bom->finish_product_id ? 'selected' : '' }}>
                                            {{ $variant['size_name'] ?? '' }} - {{ $product['name'] }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-lg-12">
                        <h5>Raw Material Details</h5>
                        <table class="table table-bordered" id="rawProductTable">
                            <thead>
                                <tr>
                                    <th class="text-center">Raw Product</th>
                                    <th class="text-center">Quantity</th>
                                    <th class="text-center">Unit</th>
                                    <th class="text-center">Cost</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($bomItems as $index => $item)
                                    <tr id="row_{{ $index }}">
                                        <td>
                                            <select name="items[{{ $index }}][variant_id]" class="form-control">
                                                <option value="">Select Raw Product</option>
                                                @foreach ($rawProducts as $product)
                                                    <optgroup label="{{ $product['name'] }}">
                                                        @foreach ($product['variants'] as $variant)
                                                            <option value="{{ $variant['id'] }}"
                                                                {{ $variant['id'] == $item->variant_id ? 'selected' : '' }}>
                                                                {{ $variant['size_name'] ?? '' }} - {{ $product['name'] }}
                                                            </option>
                                                        @endforeach
                                                    </optgroup>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><input type="number" name="items[{{ $index }}][qty]" value="{{ $item->qty }}" class="form-control"></td>
                                        <td><input type="text" name="items[{{ $index }}][unit]" value="{{ $item->unit }}" class="form-control"></td>
                                        <td><input type="number" name="items[{{ $index }}][cost]" value="{{ $item->cost }}" step="0.01" class="form-control"></td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-danger btn-sm" onclick="removeRow({{ $index }})">Remove</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <button type="button" class="btn btn-primary btn-sm" onclick="addMoreRows()">+ Add Row</button>
                    </div>
                </div>

                <div class="text-right mt-3">
                    <button type="submit" class="btn btn-success btn-sm">Update</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('script')
<script>
    let rowCounter = {{ count($bomItems) }};

    function addMoreRows() {
        let newIndex = ++rowCounter;
        let rowHtml = `
            <tr id="row_${newIndex}">
                <td>
                    <select name="items[${newIndex}][variant_id]" class="form-control">
                        <option value="">Select Raw Product</option>
                        @foreach ($rawProducts as $product)
                            <optgroup label="{{ $product['name'] }}">
                                @foreach ($product['variants'] as $variant)
                                    <option value="{{ $variant['id'] }}">{{ $variant['size_name'] ?? '' }} - {{ $product['name'] }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </td>
                <td><input type="number" name="items[${newIndex}][qty]" class="form-control"></td>
                <td><input type="text" name="items[${newIndex}][unit]" class="form-control"></td>
                <td><input type="number" name="items[${newIndex}][cost]" step="0.01" class="form-control"></td>
                <td class="text-center"><button type="button" class="btn btn-danger btn-sm" onclick="removeRow(${newIndex})">Remove</button></td>
            </tr>`;
        $('#rawProductTable tbody').append(rowHtml);
    }

    function removeRow(id) {
        $('#row_' + id).remove();
    }
</script>
@endsection
