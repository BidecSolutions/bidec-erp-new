@php
use App\Helpers\CommonHelper;
@endphp
@extends('layouts.layouts')
@section('content')
<div class="well_N">
    <div class="boking-wrp dp_sdw">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                {{ CommonHelper::displayPageTitle('Edit Category Detail') }}
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 text-right">
                <a href="{{ route('categories.index') }}" class="btn btn-success btn-xs">+ View List</a>
            </div>
        </div>
        <div class="row">
            <form method="POST" action="{{ route('categories.update', $category->id) }}" enctype="multipart/form-data"
                id="categoryForm">
                @csrf
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="row justify-content-center form-input pb-4">
                        <input type="hidden" name="category_acc_id" id="category_acc_id" value="{{ $category->acc_id }}" />
                        

                        @if ($category->children_count != 0)
                            <input type="hidden" name="old_acc_id" id="old_acc_id" value="{{ $category->acc_id }}" />
                            <input type="hidden" name="acc_id" id="acc_id" value="{{ $category->acc_id }}" />
                        @else
                            <input type="hidden" name="old_acc_id" id="old_acc_id" value="{{ $category->parent_code }}" />
                            <div class="col-lg-4">
                                <label>Account Name ({{ $category->children_count }})</label>
                                <select name="acc_id" id="acc_id" class="form-control select2">
                                    @foreach ($chartOfAccountList as $coalRow)
                                        <option value="{{ $coalRow->code }}"
                                            {{ $category->parent_code == $coalRow->code ? 'selected' : '' }}>
                                            {{ $coalRow->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="col-lg-4 hidden">
                            <div class="form-group">
                                <label for="parent_id">Parent Category</label>
                                <select name="parent_id" id="parent_id" required class="form-control select2">
                                    <!-- Default option if no parent is selected -->
                                    <option value="0" {{ $category->parent_id == 0 ? 'selected' : '' }}>No Parent
                                    </option>

                                    <!-- Loop through the categories to show parent categories and their children -->
                                    @foreach ($categories as $parentCategory)
                                    <!-- Show the parent category -->
                                    <option value="{{ $parentCategory->id }}"
                                        {{ $category->parent_id == $parentCategory->id ? 'selected' : '' }}>
                                        {{ $parentCategory->name }}
                                    </option>

                                    <!-- Loop through the child categories and show them with indentation -->
                                    @foreach ($parentCategory->childCategories as $childCategory)
                                    <option value="{{ $childCategory->id }}"
                                        {{ $category->parent_id == $childCategory->id ? 'selected' : '' }}>
                                        {{ str_repeat('--', $childCategory->level) . ' ' . $childCategory->name }}
                                    </option>
                                    @endforeach
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" id="name" name="name"
                                    value="{{ old('name', $category->name) }}" required placeholder="Name"
                                    class="required form-control">
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="order_number">Ordering Number</label>
                                <input type="number" id="order_number" name="order_number"
                                    value="{{ old('order_number', $category->order_number) }}" required
                                    placeholder="Order Number" class="required form-control">
                            </div>
                        </div>

                        @php
                        $imageFields = ['banner_image' => 'Banner', 'icon_image' => 'Icon', 'cover_image' => 'Cover Image'];
                        @endphp

                        @foreach ($imageFields as $field => $label)
                        <div class="col-lg-4 hidden">
                            <div class="form-group">
                                <label class="form-label" for="{{ $field }}">{{ $label }}</label>
                                <input type="file" id="{{ $field }}" name="{{ $field }}"
                                    class="form-control">
                                @if ($category->$field)
                                <p>Current {{ $label }}: <a
                                        href="{{ asset('storage/' . $category->$field) }}" target="_blank">View</a>
                                </p>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="row">
                        <div class="col-lg-12 text-right">
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