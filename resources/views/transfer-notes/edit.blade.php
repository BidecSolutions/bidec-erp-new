@php
    use App\Helpers\CommonHelper;
@endphp
@extends('layouts.layouts')
@section('content')
    <div class="well_N">
        <div class="boking-wrp dp_sdw">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    {{ CommonHelper::displayPageTitle('Edit Transfer Note') }}
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 text-right">
                    <a href="{{ route('transfer-notes.index') }}" class="btn btn-success btn-xs">+ View List</a>
                </div>
            </div>
            <div class="row">
                <form method="POST" action="{{ route('transfer-notes.update', $transferNote->id) }}">
                    @csrf
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                    <label class="sf-label">T.N. Date.</label>
                                    <input type="date" class="form-control" name="tn_date"
                                        value="{{ $transferNote->transfer_note_date }}" required />
                                </div>
                                <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
                                    <label class="sf-label">Description</label>
                                    <textarea name="description" rows="2" class="form-control">{{ $transferNote->description }}</textarea>
                                </div>
                            </div>
                            <div class="lineHeight">&nbsp;</div>
                            <div class="table-responsive">
                                <table class="table table-bordered sf-table-list" id="transferNoteTable">
                                    <thead>
                                        <tr>
                                            <th>Company Locations</th>
                                            <th>Product</th>
                                            <th>Qty.</th>
                                            <th>Remarks</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($transferNoteData as $index => $data)
                                            <tr id="row_{{ $index + 1 }}">
                                                <!-- Hidden fields to help the controller identify this row -->
                                                <input type="hidden" name="tnDataArray[]" value="{{ $index + 1 }}">
                                                <input type="hidden" name="dataId_{{ $index + 1 }}"
                                                    value="{{ $data->id }}">

                                                <td>
                                                    <select name="locationId_{{ $index + 1 }}"
                                                        class="form-control select2" required>
                                                        <option value="">Select Location</option>
                                                        @foreach ($companyLocations as $clRow)
                                                            <option value="{{ $clRow->id }}"
                                                                {{ $data->to_company_location_id == $clRow->id ? 'selected' : '' }}>
                                                                {{ $clRow->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>

                                                </td>
                                                <td>
                                                    <select name="productId_{{ $index + 1 }}" class="form-control select2">
                                                        <option value="">Select Product</option>
                                                        @foreach ($products as $product)
                                                            <optgroup label="{{ $product['name'] }}">
                                                                @foreach ($product['variants'] as $variant)
                                                                    <option value="{{ $variant['id'] }}"
                                                                        {{ $data->product_variant_id == $variant['id'] ? 'selected' : '' }}>
                                                                        {{ $variant['size_name'] }} -
                                                                        {{ number_format($variant['amount'], 2) }}
                                                                    </option>
                                                                @endforeach
                                                            </optgroup>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="number" name="qty[{{ $index + 1 }}]"
                                                        value="{{ $data->send_qty }}" class="form-control" />
                                                </td>
                                                <td>
                                                    <input type="text" name="remarks[{{ $index + 1 }}]"
                                                        value="{{ $data->remarks }}" class="form-control" />
                                                </td>
                                                <!-- Optional remove button for existing rows -->
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-danger btn-sm"
                                                        onclick="removeTransferNoteRow({{ $index + 1 }})">Remove</button>
                                                </td>
                                            </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                            </div>
                            <button type="button" class="btn btn-primary" onclick="addMoreTransferNotesDetailRows()">Add
                                more Row</button>
                        </div>
                        <div class="lineHeight">&nbsp;</div>
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">
                                <button type="reset" id="reset" class="btn btn-primary" onclick="clearForm()">Clear
                                    Form</button>
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
        // Start rowCounter at the number of existing rows
        var rowCounter = {{ count($transferNoteData) }};

        function addMoreTransferNotesDetailRows() {
            rowCounter++;

            // Use backticks for multiline strings:
            var newRow = `
            <tr id="row_${rowCounter}">
                <td>
                    <!-- We must push this row into tnDataArray so controller sees it -->
                    <input type="hidden" name="tnDataArray[]" value="${rowCounter}" />

                    <select name="locationId_${rowCounter}" class="form-control">
                        <option value="">Select Location</option>
                        @foreach ($companyLocations as $clRow)
                            <option value="{{ $clRow->id }}">{{ $clRow->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <select name="productId_${rowCounter}" class="form-control">
                        <option value="">Select Product</option>
                        @foreach ($products as $product)
                            <optgroup label="{{ $product['name'] }}">
                                @foreach ($product['variants'] as $variant)
                                    <option value="{{ $variant['id'] }}">
                                        {{ $variant['size_name'] }} - {{ number_format($variant['amount'], 2) }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input type="number" name="qty_${rowCounter}" class="form-control" />
                </td>
                <td>
                    <input type="text" name="remarks_${rowCounter}" class="form-control" />
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm"
                        onclick="removeTransferNoteRow(${rowCounter})">Remove</button>
                </td>
            </tr>
        `;

            $('#transferNoteTable tbody').append(newRow);
        }

        function removeTransferNoteRow(rowId) {
            // Count rows that have at least one non-empty input field
            var filledRows = $('#transferNoteTable tbody tr').filter(function() {
                return $(this).find('input, select').filter(function() {
                    return $(this).val() !== "";
                }).length > 0;
            }).length;

            // Allow removal if more than one filled row exists
            if (filledRows > 1) {
                $(`#row_${rowId}`).remove();
            } else {
                alert("You must have at least one filled row.");
            }
        }


        function clearForm() {
            $('#transferNoteTable tbody').empty();
            addMoreTransferNotesDetailRows();

        }
        
    </script>
@endsection
