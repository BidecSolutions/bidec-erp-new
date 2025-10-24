@php
    use App\Helpers\CommonHelper;
    $companyLocationId = Session::get('company_location_id');
@endphp
@extends('layouts.layouts')
@section('content')
<div class="well_N">
	<div class="boking-wrp dp_sdw">
	    <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                {{CommonHelper::displayPageTitle('Add New Transfer Note')}}
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 text-right">
                <a href="{{ route('transfer-notes.index') }}" class="btn btn-success btn-xs">+ View List</a>
            </div>
        </div>
        <div class="row">
            <form method="POST" action="{{ route('transfer-notes.store') }}">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    @csrf
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="panel-body">
                                        
                                    <div class="row">
                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                            <label class="sf-label">T.N. Date.</label>
                                            <span class="rflabelsteric"><strong>*</strong></span>
                                            <input type="date" class="form-control requiredField" name="tn_date" id="tn_date" value="{{date('Y-m-d')}}" />
                                        </div>
                                        <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
                                            <label class="sf-label">Description</label>
                                            <span class="rflabelsteric"><strong>*</strong></span>
                                            <textarea name="description" id="description" rows="2" cols="50" style="resize:none;" class="form-control">-</textarea>
                                        </div>
                                    </div>
                                    <div class="lineHeight">&nbsp;</div>
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                            <div class="table-responsive">
                                                <table class="table table-bordered sf-table-list" id="transferNoteTable">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center">Company Locations</th>
                                                            <th class="text-center">Product</th>
                                                            <th class="text-center">Qty.</th>
                                                            <th class="text-center">Remarks</th>
                                                            <th class="text-center">Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr id="row_1">
                                                            <td>
                                                                <select name="locationId_1" id="locationId_1" class="form-control requiredField">
                                                                    <option value="">Select Location</option>
                                                                    @foreach($companyLocations as $clRow)
                                                                        <option 
                                                                            value="{{$clRow->id}}"
                                                                            @if($clRow->id == $companyLocationId) 
                                                                                disabled
                                                                            @endif
                                                                        >{{$clRow->name}}</option>
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <input type="hidden" name="tnDataArray[]" id="tnDataArray" value="1" />
                                                                <select name="productId_1" id="productId_1" class="form-control requiredField">
                                                                    <option value="">Select Product</option>
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
                                                            <td>
                                                                <input type="text" name="remarks_1" id="remarks_1" value="" class="form-control" />
                                                            </td>
                                                            <td class="text-center">
                                                                ---
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <div>
                                                    <input type="button" class="btn btn-sm btn-primary" onclick="addMoreTransferNotesDetailRows()" value="Add More Rows" />
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
            </form 
        </div>
    </div>
</div>
@endsection
@section('script')
    <script>
        var rowCounter = 1; // Keep track of the row numbers
        function addMoreTransferNotesDetailRows() {
            rowCounter++;
            var newRow = `
                <tr id="row_${rowCounter}">
                    <td>
                        <select name="locationId_${rowCounter}" id="locationId_${rowCounter}" class="form-control requiredField">
                            <option value="">Select Location</option>
                            @foreach($companyLocations as $clRow)
                                <option value="{{$clRow->id}}"
                                @if($clRow->id == $companyLocationId) 
                                    disabled
                                @endif
                                >{{$clRow->name}}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input type="hidden" name="tnDataArray[]" id="tnDataArray" value="${rowCounter}" />
                        <select name="productId_${rowCounter}" id="productId_${rowCounter}" class="form-control requiredField">
                            <option value="">Select Product</option>
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
                        <input type="number" name="qty_${rowCounter}" id="qty_${rowCounter}" value="" class="form-control"/>
                    </td>
                    <td>
                        <input type="text" name="remarks_${rowCounter}" id="remarks_${rowCounter}" value="" class="form-control"/>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-danger btn-sm" onclick="removeTransferNoteRow(${rowCounter})">Remove</button>
                    </td>
                </tr>`;
                $('#transferNoteTable tbody').append(newRow);
        }

        function removeTransferNoteRow(rowId) {
            $(`#row_${rowId}`).remove(); // Remove the row with the specified ID
        }
    </script>
@endsection
