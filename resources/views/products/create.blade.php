@php
    use App\Helpers\CommonHelper;
@endphp

@extends('layouts.layouts')

@section('content')
    <div class="well_N">
        <div class="boking-wrp dp_sdw">
            <div class="row">
                <div class="col-lg-6">
                    {{ CommonHelper::displayPageTitle('Add New Product') }}
                </div>
                <div class="col-lg-6 text-right">
                    <a href="{{ route('products.index') }}" class="btn btn-success btn-xs">+ View List</a>
                </div>
            </div>
            <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="row justify-content-center form-input pb-4">
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" required
                                placeholder="Name" class="required form-control">
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="category_id"
                                onclick="showFormModelForDataInsert({url: 'categories/create', type: 'model',optionName:'categories',columnId:'category_id'})">Category</label>
                            <select name="category_id" id="category_id" required class="form-control select2 category_id">
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="brand_id"
                                onclick="showFormModelForDataInsert({url: 'brands/create', type: 'model',optionName:'brands',columnId:'brand_id'})">Brand</label>
                            <select name="brand_id" id="brand_id" required class="form-control select2 brand_id">
                                @foreach ($brands as $brand)
                                    <option value="{{ $brand->id }}"
                                        {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                                        {{ $brand->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6 hidden">
                        <div class="form-group">
                            <label for="order_number">Ordering Number</label>
                            <input type="number" id="order_number" name="order_number" value="{{ old('order_number') }}"
                                placeholder="Order Number" class="form-control">
                        </div>
                    </div>

                    @php
                        $imageFields = ['product' => 'Product Image', 'icon' => 'Icon', 'cover_image' => 'Cover Image'];
                    @endphp

                    @foreach ($imageFields as $field => $label)
                        <div class="col-lg-6 hidden">
                            <div class="form-group">
                                <label class="form-label" for="{{ $field }}">{{ $label }}</label>
                                <input type="file" id="{{ $field }}" name="{{ $field }}"
                                    class="form-control">
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <label>Variant Details</label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-condensed" id="variant-table">
                                <thead>
                                    <tr>
                                        <th class="text-center"
                                            onclick="showFormModelForDataInsert({url: 'sizes/create', type: 'model',optionName:'sizes',columnId:'variant_size_id'})">
                                            Size Name</th>
                                        <th class="text-center">Sell Amount</th>
                                        <th class="text-center">Variant Image</th>
                                        <th class="text-center">Variant Barcode</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <select name="variant_size_id[]" id="variant_size_id"
                                                class="select2 form-control variant_size_id">
                                                @foreach ($sizes as $size)
                                                    <option value="{{ $size->id }}">{{ $size->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" name="variant_amount[]" id="variant_amount"
                                                class="form-control" value="0" />
                                        </td>
                                        <td>
                                            <input type="file" name="variant_image[]" id="variant_image"
                                                class="form-control" />
                                        </td>
                                        <td>
                                            <input type="text" name="variant_barcode[]" id="variant_barcode"
                                                class="form-control" value="0" />
                                        </td>
                                        <td class="text-center">
                                            ---
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <!-- <button type="button" class="btn btn-primary btn-sm" id="add-row">Add Row</button> -->
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-lg-12 text-right">
                        <button type="reset" id="reset" class="btn btn-primary">Clear Form</button>
                        <button type="submit" class="btn btn-success">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const table = document.querySelector('#variant-table tbody');

            // Initialize select2 only for existing elements

            document.querySelector('#add-row').addEventListener('click', function() {
                const newRow = `
        <tr>
            <td>
                <select name="variant_size_id[]" class="form-control variant_size_id new-select2">
                    <option value="">Select Size</option>
                    @foreach ($sizes as $size)
                        <option value="{{ $size->id }}">{{ $size->name }}</option>
                    @endforeach
                </select>
            </td>
            <td><input type="number" name="variant_amount[]" class="form-control" /></td>
            <td><input type="file" name="variant_image[]" class="form-control" /></td>
            <td><input type="text" name="variant_barcode[]" class="form-control" /></td>
            <td class="text-center">
                <button type="button" class="btn btn-danger btn-sm remove-row">Remove</button>
            </td>
        </tr>
        `;
                table.insertAdjacentHTML('beforeend', newRow);

                // Initialize select2 only for new elements with the class 'new-select2'
                $('.new-select2').select2().removeClass('new-select2');
            });

            table.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-row')) {
                    e.target.closest('tr').remove();
                }
            });
        });
    </script>
@endsection
