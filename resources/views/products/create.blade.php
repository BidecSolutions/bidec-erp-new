@php
    use App\Helpers\CommonHelper;
@endphp

@extends('layouts.layouts')

@section('content')
    <style>
    .error-message {
        color: red;
        font-size: 13px;
        position: absolute;
        margin-top: 2px;
    }
    .form-group {
        position: relative; 
    }
    </style>
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
                <div class="row form-input pb-4">
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
                                            onclick="showFormModelForDataInsert({url: 'categories/create', type: 'model',optionName:'categories',columnId:'category_id'})">
                                            Category
                                        </label>
                                        <select name="category_id" id="category_id" required class="form-control select2 category_id">
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="brand_id"
                                            onclick="showFormModelForDataInsert({url: 'brands/create', type: 'model',optionName:'brands',columnId:'brand_id'})">
                                            Brand
                                        </label>
                                        <select name="brand_id" id="brand_id" required class="form-control select2 brand_id">
                                            @foreach ($brands as $brand)
                                                <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                                                    {{ $brand->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                </div>
                <div class="row form-input pb-4">
                                <div class="col-lg-4" id="barcode">
                                    <div class="form-group">
                                        <label for="barcode">Barcode</label>
                                        <input type="text" id="barcode" name="barcode" value="{{ old('barcode') }}"
                                            placeholder="Barcode" class="form-control">
                                    </div>
                                </div>

                                <div class="col-lg-4" id= "size_id">
                                    <div class="form-group">
                                        <label for="name">Size</label>
                                    <select name="size_id" id="size_id" class="select2 form-control variant_size_id">
                                        @foreach ($sizes as $size)
                                            <option value="{{ $size->id }}">{{ $size->name }}</option>
                                        @endforeach
                                    </select>
                                    </div>
                                </div>

                                <div class="col-lg-4" id ="sell_price">
                                    <div class="form-group">
                                        <label for="sell_price">Sell Price</label>
                                        <input type="number" id="sell_price" name="sell_price" value="{{ old('sell_price') }}"
                                            placeholder="Sell Price" class="form-control">
                                    </div>
                                </div>
                </div>
                <div class="row form-input pb-4">
                        @php
                            $imageFields = ['product' => 'Product Image', 'icon' => 'Icon', 'cover_image' => 'Cover Image'];
                        @endphp

                        @foreach ($imageFields as $field => $label)
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="{{ $field }}">{{ $label }}</label>
                                <input type="file" id="{{ $field }}" name="{{ $field }}" class="form-control">
                                </div>
                            </div>
                        @endforeach

                            <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="description">Description</label>
                                        <input type="text" id="description" name="description" value="{{ old('description') }}"
                                            placeholder="Description" class="form-control">
                                    </div>
                            </div>

                             <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="name">Product Type</label>
                                    <select name="product_type" id="product_type" class="select2 form-control ">
                                        <option value="2">Raw Product</option>    
                                        <option value="1">Finish Product</option>
                                    </select>
                                    </div>
                                </div>

                            <div class="col-lg-4" style="line-height: 5;">
                                <div class="form-group">
                                    <label for="name">Has Variant</label>
                                    <input type="checkbox" id="has_variant" name="has_variant" value="1">
                                </div>
                            </div>
                </div>

                <hr>
                <div class="row" id="variant-detail">
                    <div class="col-lg-12">
                        <label><strong>Variant Details</strong></label>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-condensed" id="variant-table">
                                <thead>
                                    <tr>
                                        <th class="text-center"
                                            onclick="showFormModelForDataInsert({url: 'sizes/create', type: 'model',optionName:'sizes',columnId:'variant_size_id'})">
                                            Size Name
                                        </th>
                                        <th class="text-center">Sell Amount</th>
                                        <th class="text-center">Variant Image</th>
                                        <th class="text-center">Variant Barcode</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <select name="variant_size_id[]" id="variant_size_id" class="select2 form-control variant_size_id">
                                                @foreach ($sizes as $size)
                                                    <option value="{{ $size->id }}">{{ $size->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                        <input type="number" name="variant_amount[]" id="variant_amount" class="form-control" value="1" min="1" />
                                        </td>
                                        <td>
                                            <input type="file" name="variant_image[]" id="variant_image" class="form-control" />
                                        </td>
                                        <td>
                                            <input type="text" name="variant_barcode[]" id="variant_barcode" class="form-control" value="0" />
                                        </td>
                                        <td class="text-center">---</td>
                                    </tr>
                                </tbody>
                            </table>
                            <button type="button" class="btn btn-primary btn-sm" id="add-row">Add Row</button>
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
    $(document).ready(function() {
        const allowedTypes = ['image/jpeg', 'image/png'];
        $('input[type="file"]').on('change', function() {
            const fileInput = $(this);
            const file = this.files[0];
            const errorSpan = fileInput.siblings('.error-message');

            errorSpan.remove();

            if (file && !allowedTypes.includes(file.type)) {
                fileInput.val('');
                fileInput.after('<span class="error-message" style="color:red; font-size:13px;">Only JPG or PNG images are allowed.</span>');
            }
        });
            $('form').on('submit', function(e) {
                let isValid = true;
                $('.error-message').remove(); 

                $('input[type="file"]').each(function() {
                    const file = this.files[0];
                    const fileInput = $(this);

                    if (file && !allowedTypes.includes(file.type)) {
                        fileInput.after('<span class="error-message" style="color:red; font-size:13px;">Only JPG or PNG images are allowed.</span>');
                        isValid = false;
                    }
                });

                if (!isValid) e.preventDefault();
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const table = document.querySelector('#variant-table tbody');

            document.querySelector('#add-row').addEventListener('click', function() {
                console.log("here");
                const newRow = document.createElement('tr');
                newRow.innerHTML = `
                <td>
                    <select name="variant_size_id[]" class="form-control variant_size_id new-select2">
                        <option value="">Select Size</option>
                        @foreach ($sizes as $size)
                            <option value="{{ $size->id }}">{{ $size->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td><input type="number" name="variant_amount[]" class="form-control" required /></td>
                <td><input type="file" name="variant_image[]" class="form-control" /></td>
                <td><input type="text" name="variant_barcode[]" class="form-control" /></td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm remove-row">Remove</button>
                </td>
                `;
                table.appendChild(newRow);
                $('.new-select2').select2().removeClass('new-select2');
            });

            // Use event delegation to remove rows
            table.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-row')) {
                    e.target.closest('tr').remove();
                }
            });
        });
        $(document).ready(function() {
            $('#variant-detail').hide();

            $('#has_variant').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#sell_price, #barcode, #size_id').hide();
                    $('#variant-detail').slideDown();
                } else {
                    $('#sell_price, #barcode, #size_id').show();
                    $('#variant-detail').slideUp();
                }
            });
        });
    </script>
@endsection
