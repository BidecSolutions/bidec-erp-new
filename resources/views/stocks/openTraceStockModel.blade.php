<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <label>Product Detail</label>
        <select name="filter_product_variant_id_main" id="filter_product_variant_id_main" class="form-control" onchange="loadTraceStockDetail()">
            @foreach($products as $product)
                <optgroup label="{{ $product['name'] }}">
                    @foreach($product['variants'] as $variant)
                        <option value="{{ $variant['id'] }}">
                            {{ $variant['size_name'] }} - {{ number_format($variant['amount'], 2) }}
                        </option>
                    @endforeach
                </optgroup>
            @endforeach
        </select>
    </div>
</div>
<div class="lineHeight">&nbsp;</div>
<div class="well">
    <div class="row" id="loadTraceStockDetail">
        
    </div>
</div>
<script>
    function loadTraceStockDetail() {
        var filter_product_variant_id = $('#filter_product_variant_id_main').val();

        // Check if filter_product_variant_id is null or empty
        if (!filter_product_variant_id) {
            alert('Error: Product Variant ID is required!');
            return; // Stop function execution
        }

        var pageType = true;
        var parentCode = true;

        $.ajax({
            url: '<?php echo url('/'); ?>/stocks/loadTraceStockDetail',
            type: "GET",
            data: {
                pageType: pageType,
                parentCode: parentCode,
                filter_product_variant_id: filter_product_variant_id
            },
            beforeSend: function() {
                jQuery('#loadTraceStockDetail').html('<div class="loader"></div>');
            },
            success: function(data) {
                setTimeout(function() {
                    jQuery('#loadTraceStockDetail').html(data);
                }, 1000);
            },
            error: function() {
                alert('Error: Unable to load stock details.');
            }
        });
    }

    loadTraceStockDetail();
</script>