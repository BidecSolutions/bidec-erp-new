@php
use App\Helpers\CommonHelper;
@endphp
<div class="well_N">
    <div class="boking-wrp dp_sdw">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                {{ CommonHelper::displayPageTitle('Add New Category') }}
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 text-right">
                <a href="{{ route('categories.index') }}" class="btn btn-success btn-xs">+ View List</a>
            </div>
        </div>
        <form method="POST" action="{{ route('categories.store') }}" enctype="multipart/form-data" id="categoryForm">
            @csrf
            <input type="hidden" name="pageOptionType" id="pageOptionType" value="{{$pageOptionType}}" />
            <input type="hidden" name="columnId" id="columnId" value="{{$columnId}}" />
            <div class="row justify-content-center form-input pb-4">
                <div class="col-lg-4">
                    <label>Account Name</label>
                    <select name="acc_id" id="acc_id" class="form-control select2">
                        @foreach($chartOfAccountList as $coalRow)
                        <option value="{{$coalRow->code}}">{{$coalRow->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-4 hidden">
                    <div class="form-group">
                        <label for="parent_id">Parent Category</label>
                        <select name="parent_id" id="parent_id" required class="form-control select2">
                            <option value="0">No Parent</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @foreach ($category->childCategories as $childCategory)
                            <option value="{{ $childCategory->id }}">
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
                        <input type="text" id="name" name="name" required placeholder="Name" class="required form-control">
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="form-group">
                        <label for="order_number">Ordering Number</label>
                        <input type="number" id="order_number" name="order_number" required placeholder="Order Number" class="required form-control">
                    </div>
                </div>

                @php
                $imageFields = ['banner' => 'Banner', 'icon' => 'Icon', 'cover_image' => 'Cover Image'];
                @endphp

                @foreach($imageFields as $field => $label)
                <div class="col-lg-4 hidden">
                    <div class="form-group">
                        <label class="form-label" for="{{ $field }}">{{ $label }}</label>
                        <input type="file" id="{{ $field }}" name="{{ $field }}" class="form-control">
                    </div>
                </div>
                @endforeach
            </div>
            <div class="row">
                <div class="col-lg-12 text-right">
                    <button type="reset" id="reset" class="btn btn-primary">Clear Form</button>
                    @if($pageOptionType == 'normal')
                    <button type="submit" class="btn btn-sm btn-success">Submit</button>
                    @else
                    <button type="button" class="btn btn-sm btn-success" onclick="submitForm('categoryForm')">Submit</button>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>
<script>
    // AJAX Form Submission Function
    function submitForm(formId) {
        // Create a new FormData object to gather the form data
        var formData = new FormData(document.getElementById(formId));
        var columnId = '<?php echo $columnId ?>';

        // Perform the AJAX request
        $.ajax({
            url: $('#' + formId).attr('action'), // Get the form action URL
            type: 'POST',
            data: formData,
            processData: false, // Do not process FormData into a query string
            contentType: false, // Let FormData set the content type
            success: function(response) {
                var option = '<option value="' + response.data.id + '">' + response.data.name + '</option>';
                $('.' + columnId).append(option);
                $('#showFormModelForDataInsert').modal('toggle');
            },
            error: function(xhr, status, error) {
                // Handle the error response
                alert('Error: ' + xhr.responseText);
            }
        });
    }
</script>