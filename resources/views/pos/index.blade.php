@extends('layouts.layouts')
@section('content')
@php
    use App\Helpers\CommonHelper;
    $fromDate = date('Y-m-d', strtotime('-30 days'));
    $toDate = date('Y-m-d', strtotime('+30 days'));
@endphp

<style>
    body {
        background-color: #ffecec;
    }
    .pos-container {
        display: flex;
        gap: 25px;
    }
    .menu-section {
        flex: 3;
        background: #fff;
        border-radius: 20px;
        padding: 25px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.05);
    }
    .bill-section {
        flex: 1.3;
        background: #fff;
        border-radius: 20px;
        padding: 25px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.05);
    }
    .category-scroll {
        display: flex;
        gap: 15px;
        overflow-x: auto;
        padding-bottom: 10px;
    }
    .category-btn {
        background: #fff3f3;
        border: 1px solid transparent;
        border-radius: 10px;
        padding: 10px 20px;
        font-weight: 500;
        cursor: pointer;
        transition: 0.2s;
    }
    .category-btn.active, .category-btn:hover {
        background: #ffb3b3;
        border-color: #ff6666;
    }
    .menu-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }
    .menu-card {
        background: #fff;
        border-radius: 15px;
        overflow: hidden;
        text-align: center;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        transition: 0.2s;
    }
    .menu-card:hover {
        transform: translateY(-4px);
    }
    .menu-card img {
        width: 100%;
        height: 160px;
        object-fit: cover;
    }
    .menu-card .details {
        padding: 12px;
    }
    .menu-card button {
        margin-top: 10px;
        background: #ff6f61;
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 8px 14px;
        font-size: 14px;
        transition: 0.2s;
    }
    .menu-card button:hover {
        background: #ff4b3e;
    }
</style>

<div class="container-fluid mt-4 pos-container">

    <!-- Menu Section -->
    <div class="menu-section">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold">Choose Category</h4>
            <input type="text" class="form-control w-25" placeholder="Search menu..." />
        </div>

        <div class="category-scroll mb-3">
            <div class="category-btn active">Rice Bowl</div>
            <div class="category-btn">Ice Cream</div>
            <div class="category-btn">Coffee</div>
            <div class="category-btn">Dessert</div>
            <div class="category-btn">Snack</div>
        </div>

        <h5 class="fw-bold mt-3">Rice Bowl Menu</h5>

        <div class="menu-grid">
            @foreach(range(1,6) as $i)
                <div class="menu-card">
                    <img src="https://source.unsplash.com/400x300/?rice,bowl,food" alt="Food">
                    <div class="details">
                        <h6>Rice Bowl #{{ $i }}</h6>
                        <p class="text-muted mb-1">$27.09</p>
                        <button>Add to Billing</button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Bill Section -->
    <div class="bill-section">
        <h4 class="fw-bold mb-3">Bills</h4>
        <div class="d-flex flex-column gap-3" id="billItems">
            <div class="d-flex justify-content-between">
                <span>Miso Ramen x2</span>
                <span>$14.18</span>
            </div>
            <div class="d-flex justify-content-between">
                <span>Waffle x2</span>
                <span>$14.18</span>
            </div>
            <div class="d-flex justify-content-between">
                <span>Mocha Ice Cream x2</span>
                <span>$14.18</span>
            </div>
        </div>

        <hr>
        <div class="d-flex justify-content-between">
            <span>Subtotal</span><span>$27.18</span>
        </div>
        <div class="d-flex justify-content-between">
            <span>Discount</span><span>$5.00</span>
        </div>
        <div class="d-flex justify-content-between">
            <span>Tax</span><span>$1.99</span>
        </div>
        <hr>
        <div class="d-flex justify-content-between fw-bold">
            <span>Total</span><span>$20.51</span>
        </div>

        <h5 class="mt-4 fw-bold">Payment Method</h5>
        <div class="d-flex gap-3 mt-2">
            <button class="btn btn-light flex-fill border">Cash</button>
            <button class="btn btn-danger flex-fill">Debit Card</button>
            <button class="btn btn-light flex-fill border">QRIS</button>
        </div>

        <button class="btn btn-success w-100 mt-4">Add to Billing</button>
    </div>

</div>

@endsection

@section('script')
<script>
    $(document).ready(function() {
        get_ajax_data();
    });
</script>
@endsection
