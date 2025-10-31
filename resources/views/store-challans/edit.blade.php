@php
    use App\Helpers\CommonHelper;
@endphp

@extends('layouts.layouts')

@section('content')
    <div class="well_N">
        <div class="boking-wrp dp_sdw">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    {{ CommonHelper::displayPageTitle('Edit Good Receipt Note') }}
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 text-right">
                    <a href="{{ route('store-challans.index') }}" class="btn btn-success btn-xs">+ View List</a>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <label>Department Detail</label>
                    <input type="hidden" id="store_challan_id" value="{{$storeChallan->id }}">
                    <select name="department_id" id="department_id" class="form-control select2"
                        onchange="loadPurchaseOrderDetailUsingSupplierId()">
                        <option value="">Select Supplier</option>
                        @foreach ($getDepartmentList as $gdList)
                            <option value="{{ $gdList->id }}"
                                {{ $storeChallan->department_id == $gdList->id ? 'selected' : '' }}>
                                {{ $gdList->department_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="lineHeight">&nbsp;</div>
            <div class="loadGoodsReceiptNoteDetailSection mt-3">
                {{-- Load existing purchase order details here --}}
                {!! $purchaseOrderHtml ?? '' !!}
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            loadPurchaseOrderDetailForEdit();
        });

        function loadPurchaseOrderDetailForEdit() {
            console.log("hhhhh");
            var departmentId = $('#department_id').val();
            var storeChallanId = $('#store_challan_id').val(); // Get GRN ID from hidden input

            if (departmentId === '' || storeChallanId === '') {
                alert('Department ID and Store Challan Id ID are required.');
                $('.loadGoodsReceiptNoteDetailSection').html('');
                return;
            }

            $('.loadGoodsReceiptNoteDetailSection').html(
                '<div class="row"><div class="col-lg-12 text-center"><div class="loader"></div></div></div>'
            );

            $.ajax({
                url: "{{ route('getMaterialRequestsForEdit') }}", // Updated route
                type: "GET",
                data: {
                    deparmentId: departmentId,
                    storeChallanId: storeChallanId
                },
                success: function(data) {
                    if (data.success) {
                        $('.loadGoodsReceiptNoteDetailSection').html(data.html);
                    } else {
                        alert(data.message || 'No purchase orders found.');
                        $('.loadGoodsReceiptNoteDetailSection').html('');
                    }
                },
                error: function() {
                    alert('Failed to fetch purchase orders. Please try again.');
                    $('.loadGoodsReceiptNoteDetailSection').html('');
                }
            });
        }



        function hideDetails() {
            document.getElementById('purchaseOrderDetails').style.display = 'none';
        }

        function processSelectedOrder() {
            const selectedOrderId = $('input[name="selected_order"]:checked').val();
            if (!selectedOrderId) {
                alert('Please select a purchase order to proceed.');
                return;
            }

            // Example action: Log selected order ID or make an AJAX request
            console.log('Selected Order ID:', selectedOrderId);

            // You can replace this with an AJAX call or other functionality
            $.ajax({
                url: '/store-challans/processPurchaseOrder',
                type: 'POST',
                data: {
                    orderId: selectedOrderId,
                    _token: '{{ csrf_token() }}' // Include CSRF token for POST requests
                },
                success: function(response) {
                    alert('Purchase order processed successfully!');
                    console.log(response);
                },
                error: function() {
                    alert('Failed to process the purchase order. Please try again.');
                }
            });
        }
    </script>
@endsection
