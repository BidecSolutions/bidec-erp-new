
<h2 style="text-align: center">Stock Summary Report</h2>
<table id="stockReportLocationWiseView" class="table table-bordered table-responsive">

    <thead>
    <th class="text-center">S.No</th>
    <th class="text-center">Category</th>
    <th class="text-center">Item Code</th>
    <th class="text-center">Item Name</th>
    <th class="text-center">UOM</th>
    <th class="text-center">Location</th>
    <th class="text-center">In Stock</th>

    </thead>
    <tbody id="">
    <?php
    $counter=1;
    ?>
    @foreach($subItemData as $item)
        
        <tr>
            <td class="text-center">{{$counter++}}</td>                
            <td class="text-center">{{ $item->category->main_ic ?? 'None' }}</td>
            <td class="text-center">{{ $item->subItem->item_code ?? 'None' }}</td>
            <td class="text-center">{{ $item->subItem->sub_ic ?? 'None' }}</td>
            <td class="text-center">{{ $item->subItem->uomData->uom_name ?? 'None' }}</td>
            <td class="text-center">{{ $item->location->location_name ?? 'None' }}</td>                
            <td class="text-center">{{ $item->stockLocationWiseSum() ?? 'None' }}</td>                
        </tr>
    @endforeach

    </tbody>
</table>


