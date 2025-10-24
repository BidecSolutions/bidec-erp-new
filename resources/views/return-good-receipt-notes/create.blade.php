@extends('layouts.layouts')

@section('content')
    <div class="well_N">
        <div class="boking-wrp dp_sdw">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    {!! \App\Helpers\CommonHelper::displayPageTitle('Create Return GRN') !!}
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 text-right">
                    <a href="{{ route('return-good-receipt-notes.index') }}" class="btn btn-success btn-xs">+ View List</a>
                </div>
            </div>

            <form method="POST" action="{{ route('return-good-receipt-notes.store') }}">
                @csrf

                <div class="form-group mt-3">
                    <label for="grn_id">Select Approved GRN</label>
                    <select name="good_receipt_note_id" id="grn_id" class="form-control select2" onchange="loadGRNDetails(this.value)">
                        <option value="">-- Select GRN --</option>
                        @foreach($grns as $grn)
                            <option value="{{ $grn->id }}">GRN# {{ $grn->grn_no }} | Supplier: {{ $grn->supplier_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="grn-details-section" class="mt-4">
                    {{-- Loaded via AJAX when a GRN is selected --}}
                </div>

                <div class="form-group mt-4">
                    <label>Reason for Return</label>
                    <textarea name="reason" class="form-control" rows="3"></textarea>
                </div>

                <div class="form-group mt-3">
                    <button type="submit" class="btn btn-primary">Submit Return GRN</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
<script>
    function loadGRNDetails(grnId) {
        if (!grnId) {
            $('#grn-details-section').html('');
            return;
        }

        $('#grn-details-section').html('<div class="text-center mt-3"><div class="loader"></div></div>');

        $.ajax({
            url: '{{ url('/return-good-receipt-notes/load-grn-details') }}',
            type: 'GET',
            data: {
                grn_id: grnId
            },
            success: function(response) {
                $('#grn-details-section').html(response.html);
            },
            error: function() {
                alert('Failed to load GRN details.');
                $('#grn-details-section').html('');
            }
        });
    }

    function toggleRowInputs(checkbox) {
        const row = checkbox.closest('tr');
        const inputs = row.querySelectorAll('.row-input');
        inputs.forEach(input => {
            input.disabled = !checkbox.checked;
            if (!checkbox.checked) {
                if (input.type === 'number') input.value = 0;
                else if (input.type === 'text') input.value = '';
            }
        });
    }

    // Initialize all rows on load
    document.querySelectorAll('input[name^="include_product"]').forEach(checkbox => {
        toggleRowInputs(checkbox);
    });
</script>
@endsection
