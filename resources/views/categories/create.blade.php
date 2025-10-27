@php
use App\Helpers\CommonHelper;
@endphp

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
                <div class="col-lg-4">
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

<script>

function submitForm(formId) {
    var formData = new FormData(document.getElementById(formId));

    $.ajax({
        url: $('#' + formId).attr('action'),
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
 
            $('#createCategoryModal').modal('hide');
            Swal.fire({
                title: "Success!",
                text: "Category has been created successfully.",
                icon: "success",
                timer: 2000,
                showConfirmButton: false
            });

            if (typeof get_ajax_data === "function") {
                get_ajax_data();
            }
        },
        error: function(xhr) {
            Swal.fire({
                title: "Error!",
                text: "Something went wrong. Please try again.",
                icon: "error"
            });
        }
    });
}
</script>