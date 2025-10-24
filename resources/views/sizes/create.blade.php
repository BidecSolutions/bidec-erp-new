@php
    use App\Helpers\CommonHelper;
@endphp
<div class="well_N">
	<div class="boking-wrp dp_sdw">
	    <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                {{CommonHelper::displayPageTitle('Add New Size')}}
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 text-right">
                <a href="{{ route('sizes.index') }}" class="btn btn-success btn-xs">+ View List</a>
            </div>
        </div>
        <div class="row">
            <form method="POST" action="{{ route('sizes.store') }}" id="sizeForm">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    @csrf
                    <input type="hidden" name="pageOptionType" id="pageOptionType" value="{{$pageOptionType}}" />
                    <input type="hidden" name="columnId" id="columnId" value="{{$columnId}}" />
                    <div class="row">
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                            <label>Size Name</label>
                            <input type="text" name="name"
                            class="form-control @error('name') border border-danger @enderror"
                            id="name" value="{{old('name')}}" />
                            @error('name')
                                <div class="text-sm text-danger text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="lineHeight">&nbsp;</div>
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">
                            <button type="reset" id="reset" class="btn btn-primary">Clear Form</button>
                            @if($pageOptionType == 'normal')
                                <button type="submit" class="btn btn-sm btn-success">Submit</button>
                            @else
                                <button type="button" class="btn btn-sm btn-success" onclick="submitForm('sizeForm')">Submit</button>
                            @endif
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // AJAX Form Submission Function
    function submitForm(formId) {
        // Create a new FormData object to gather the form data
        var formData = new FormData(document.getElementById(formId));
        var columnId = '<?php echo $columnId?>';
        
        // Perform the AJAX request
        $.ajax({
            url: $('#'+formId+'').attr('action'),  // Get the form action URL
            type: 'POST',
            data: formData,
            processData: false,  // Prevent jQuery from automatically transforming the data
            contentType: false,  // Allow the browser to determine the content type
            success: function(response) {
                var option = '<option value="'+response.data.id+'">'+response.data.name+'</option>';
                $('.'+columnId+'').append(option);
                $('#showFormModelForDataInsert').modal('toggle');
            },
            error: function(xhr, status, error) {
                // Handle the error response
                alert('Error: ' + xhr.responseText);
            }
        });
    }
</script>
