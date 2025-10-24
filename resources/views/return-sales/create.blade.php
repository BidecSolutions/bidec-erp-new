@extends('layouts.layouts')

@section('content')
<div class="well_N">
    <div class="boking-wrp dp_sdw">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                {!! \App\Helpers\CommonHelper::displayPageTitle('Create Sale Return') !!}
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 text-right">
                <a href="{{ route('sales-return.index') }}" class="btn btn-success btn-xs">+ View List</a>
            </div>
        </div>

        <form method="POST" action="{{ route('sales-return.store') }}">
            @csrf
            <div class="form-group mt-3">
                <label for="order_id">Select Order</label>
                <select name="order_id_customer_id" id="order_id" class="form-control select2" onchange="loadOrderDetails(this.value)">
                    <option value="">-- Select Order --</option>
                    @foreach($saleReturns as $order)
                        <option value="{{ $order->cart_id }}<>{{$order->customer_id }}">Order# {{ $order->order_no }} | Customer: {{ $order->customer_name }}</option>
                    @endforeach
                </select>
            </div>

            <div id="order-details-section" class="mt-4">
                {{-- Loaded via AJAX when an order is selected --}}
            </div>

            <div class="form-group mt-4">
                <label>Reason for Return</label>
                <textarea name="reason" class="form-control" rows="3"></textarea>
            </div>

            <div class="form-group mt-3">
                <button type="submit" class="btn btn-primary">Submit Sale Return</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('script')
<script>
    function loadOrderDetails(cartId) {
        if (!cartId) {
            $('#order-details-section').html('');
            return;
        }

        $('#order-details-section').html('<div class="text-center mt-3"><div class="loader"></div></div>');

        $.ajax({
            url: '{{ url('/sales-return/load-order-details') }}',
            type: 'GET',
            data: { cart_id: cartId },
            success: function(response) {
                $('#order-details-section').html(response.html);
            },
            error: function() {
                alert('Failed to load order details.');
                $('#order-details-section').html('');
            }
        });
    }
</script>
<script>
    // Toggle the return quantity input based on checkbox
    function toggleReturnQtyInput(checkbox, itemId) {
        const returnQtyInput = document.getElementById('return_qty_' + itemId);
        if (checkbox.checked) {
            returnQtyInput.disabled = false;
            returnQtyInput.value = 0;  // Reset value when checkbox is checked
        } else {
            returnQtyInput.disabled = true;
            returnQtyInput.value = 0;  // Set to 0 when checkbox is unchecked
        }
    }
</script>

@endsection
