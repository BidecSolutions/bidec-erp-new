<?php
    use App\Helpers\CommonHelper;
    use App\Helpers\ReuseableCode;
    $export = true;
    $m = getSessionCompanyId();
    $currentMonthEndDate =  isset($_GET['toDate']) ? $_GET['toDate'] :date('Y-m-t');
    use App\Helpers\PurchaseHelper; 
?>
<table id="stockReportLocationWiseView" class="table table-bordered table-responsive">
    <thead>
        <th class="text-center">S.No</th>
        <th class="text-center">Category</th>
        <th class="text-center">Item Code</th>
        <th class="text-center">Item Name</th>
        <th class="text-center">UOM</th>
        <th class="text-center">Location</th>
        <th class="text-center">In Stock</th>
        <th class="text-center">Reorder Level</th>
    </thead>
    <tbody id="">
        @foreach($subItems as $item)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>{{ $item->category->main_ic ?? 'None' }}</td>
                <td class="text-center">{{ $item->subItem->item_code ?? 'None' }}</td>
                <td>{{ $item->subItem->sub_ic ?? 'None' }}</td>
                <td class="text-center">{{ $item->subItem->uomData->uom_name ?? 'None' }}</td>
                <td class="text-center">{{ $item->location->location_name ?? 'None' }}</td>                
                <td class="text-center">{{ $item->stockLocationWiseSum() ?? 'None' }}</td>
                <td class="text-center">{{ CommonHelper::getReorderLevelItemAndLocationWise($item->location_id, $item->sub_ic_id) }}</td>                
            </tr>
        @endforeach
    </tbody>
</table>

<div class="text-center">
    {{ $subItems->appends(['toDate' => request()->toDate])->links() }}
</div>
