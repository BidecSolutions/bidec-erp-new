@php
    use App\Helpers\CommonHelper;
@endphp
<div class="row">
    @foreach ($products as $index => $product)
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card product-card text-center shadow-sm p-3">
                <div class="card-body">
                    <!-- Product Image -->
                    <div class="product-image mb-3">
                        {!! CommonHelper::display_document_two($product['product_image'] ?? 'assets/img/no_image.png') !!}
                    </div>

                    <!-- Product Category -->
                    <p class="product-category text-muted">
                        {{ $product['category_name'] ?? 'Category' }}</p>

                    <!-- Product Name -->
                    <h5 class="card-title font-weight-bold">{{ $product['name'] }}</h5>

                    <!-- Product Price (Default Variant) -->
                    <p class="product-price font-weight-bold text-primary">
                        ${{ number_format($product['variants'][0]['amount'] ?? 0, 2) }}
                    </p>



                    <!-- Variant Buttons -->
                    <div class="variant-buttons mt-3">
                        @foreach ($product['variants'] as $variant)
                            <button class="btn btn-outline-primary btn-sm variant-button add-to-cart mt-1 add-to-cart"
                                data-product-id="{{ $product['id'] }}" data-variant-id="{{ $variant['id'] }}"
                                data-barcode="{{ $variant['variant_barcode'] }}"
                                data-name="{{ $product['name'] }} ({{ $variant['size_name'] }})"
                                data-price="{{ $variant['amount'] }}">
                                {{ $variant['size_name'] }} -
                                ${{ number_format($variant['amount'], 2) }}
                            </button>
                        @endforeach
                    </div>

                </div>
            </div>
        </div>
    @endforeach
</div>
