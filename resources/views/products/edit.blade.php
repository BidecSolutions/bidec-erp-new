@php
    use App\Helpers\CommonHelper;
@endphp

@extends('layouts.layouts')

@section('content')
    <div class="well_N">
        <div class="boking-wrp dp_sdw">
            <div class="row">
                <div class="col-lg-6">
                    {{ CommonHelper::displayPageTitle('Edit Product') }}
                </div>
                <div class="col-lg-6 text-right">
                    <a href="{{ route('products.index') }}" class="btn btn-success btn-xs">+ View List</a>
                </div>
            </div>
            <form method="POST" action="{{ route('products.update', $product->id) }}" enctype="multipart/form-data">
                @csrf
                <div class="row justify-content-center form-input pb-4">
                    <div class="col-lg-6">
                        <label>Account Name</label>
                        <input type="hidden" name="old_acc_id" id="old_acc_id" value="{{$product->acc_id}}" />
                        <input type="hidden" name="old_parent_code" id="old_parent_code" value="{{$product->parent_code}}" /> 
                        <select name="acc_id" id="acc_id" class="form-control select2">
                            @foreach($chartOfAccountList as $coalRow)
                            <option value="{{$coalRow->code}}" {{ $product->parent_code == $coalRow->code ? 'selected' : '' }}>{{$coalRow->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}"
                                required placeholder="Name" class="required form-control">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="category_id"
                                onclick="showFormModelForDataInsert({url: 'categories/create', type: 'model',optionName:'categories',columnId:'category_id'})">Category</label>
                            <select name="category_id" id="category_id" required class="form-control select2 category_id">
                                <option value="">Select Category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="brand_id"
                                onclick="showFormModelForDataInsert({url: 'brands/create', type: 'model',optionName:'brands',columnId:'brand_id'})">Brand</label>
                            <select name="brand_id" id="brand_id" required class="form-control select2 brand_id">
                                <option value="">Select Brand</option>
                                @foreach ($brands as $brand)
                                    <option value="{{ $brand->id }}"
                                        {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>
                                        {{ $brand->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6 hidden">
                        <div class="form-group">
                            <label for="order_number">Ordering Number</label>
                            <input type="number" id="order_number" name="order_number"
                                value="{{ old('order_number', $product->order_number) }}" required
                                placeholder="Order Number" class="required form-control">
                        </div>
                    </div>

                    @php
                        $imageFields = [
                            'product' => ['label' => 'Product Image', 'value' => $product->product_image],
                            'icon' => ['label' => 'Icon', 'value' => $product->icon_image],
                            'cover_image' => ['label' => 'Cover Image', 'value' => $product->cover_image],
                        ];
                    @endphp

                    @foreach ($imageFields as $field => $data)
                        <div class="col-lg-6 hidden">
                            <div class="form-group">
                                <label class="form-label" for="{{ $field }}">{{ $data['label'] }}</label>
                                <input type="file" id="{{ $field }}" name="{{ $field }}"
                                    class="form-control">
                                @if ($data['value'])
                                    <img src="{{ asset($data['value']) }}" alt="{{ $data['label'] }}" width="100"
                                        class="mt-2">
                                @endif
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
                                    @foreach ($variants as $index => $variant)
                                        <tr>
                                            <td>
                                                <select name="variant_size_id[]" id="variant_size_id"
                                                    class="form-control variant_size_id select2">
                                                    <option value="">Select Size</option>
                                                    @foreach ($sizes as $size)
                                                        <option value="{{ $size->id }}"
                                                            {{ $variant->size_id == $size->id ? 'selected' : '' }}>
                                                            {{ $size->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" name="variant_amount[]" id="variant_amount"
                                                    class="form-control" value="{{ $variant->amount }}" />
                                            </td>
                                            <td>
                                                <input type="file" name="variant_image[]" id="variant_image"
                                                    class="form-control" />
                                                @if ($variant->variant_image)
                                                    <img src="{{ asset($variant->variant_image) }}" alt="Variant Image"
                                                        width="50" class="mt-2">
                                                @endif
                                            </td>
                                            <td>
                                                <input type="text" name="variant_barcode[]" id="variant_barcode"
                                                    class="form-control" value="{{ $variant->variant_barcode }}" />
                                            </td>
                                            <td class="text-center">
                                                <button type="button"
                                                    class="btn btn-danger btn-sm remove-row">Remove</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <button type="button" class="btn btn-primary btn-sm" id="add-row">Add Row</button>
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-lg-12 text-right">
                        <button type="reset" id="reset" class="btn btn-primary">Clear Form</button>
                        <button type="submit" class="btn btn-success">Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const table = document.querySelector('#variant-table tbody');

            document.querySelector('#add-row').addEventListener('click', function() {
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
    </script>
@endsection
