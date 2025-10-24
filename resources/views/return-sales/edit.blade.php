@extends('layouts.layouts')

@section('content')
<div class="well_N">
    <div class="boking-wrp dp_sdw">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                {!! \App\Helpers\CommonHelper::displayPageTitle('Edit Sale Return') !!}
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 text-right">
                <a href="{{ route('sales-return.index') }}" class="btn btn-success btn-xs">+ View List</a>
            </div>
        </div>

        <form method="POST" action="{{ route('sales-return.update') }}">
            @csrf
            <input type="hidden" name="id" class="form-control" value="{{$returnSale->id}}">

            <div class="form-group mt-3">
                <label>Order No</label>
                <input type="text" class="form-control" value="Order# {{ $returnSale->order_no }}" readonly>
            </div>

            <div class="form-group mt-3">
                <label>Customer</label>
                <input type="text" class="form-control" value="{{ $returnSale->customer_name }}" readonly>
            </div>

            <div id="order-details-section" class="mt-4">
                <table class="table table-bordered mt-3">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Product Name</th>
                            <th>Variant Name</th>
                            <th>Ordered Qty</th>
                            <th>Previously Returned</th>
                            <th>Return Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($returnItems as $item)
                            @php
                                $maxReturnable = $item->original_qty - $item->previously_returned_qty + $item->return_qty;
                            @endphp
                            <tr>
                                <td>
                                    <input type="checkbox"
                                           name="include_product[{{ $item->cart_item_id }}]"
                                           value="1"
                                           onchange="toggleReturnQtyInput(this, {{ $item->cart_item_id }})"
                                           checked>
                                </td>
                                <td>{{ $item->product_name }}</td>
                                <td>{{ $item->size_name }}</td>
                                <td>{{ $item->original_qty }}</td>
                                <td>{{ $item->previously_returned_qty }}</td>
                                <td>
                                    <input type="number"
                                           class="form-control return-qty"
                                           name="return_qtys[{{ $item->cart_item_id }}]"
                                           min="0"
                                           max="{{ $maxReturnable }}"
                                           value="{{ $item->return_qty }}"
                                           id="return_qty_{{ $item->cart_item_id }}">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="form-group mt-4">
                <label>Reason for Return</label>
                <textarea name="reason" class="form-control" rows="3">{{ $returnSale->reason }}</textarea>
            </div>

            <div class="form-group mt-3">
                <button type="submit" class="btn btn-primary">Update Sale Return</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('script')
<script>
    function toggleReturnQtyInput(checkbox, itemId) {
        const returnQtyInput = document.getElementById('return_qty_' + itemId);
        if (checkbox.checked) {
            returnQtyInput.disabled = false;
        } else {
            returnQtyInput.disabled = true;
            returnQtyInput.value = 0;
        }
    }
</script>
@endsection
