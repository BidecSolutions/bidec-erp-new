
<?php
    use App\Helpers\CommonHelper;
    use App\Helpers\ReuseableCode;
    $export = true;
    $m = getSessionCompanyId();
    $currentMonthEndDate =  isset($_GET['toDate']) ? $_GET['toDate'] :date('Y-m-t');
    use App\Helpers\PurchaseHelper; 
?>
@extends('layouts.default')
@section('content')
    @include('select2')
    <style>
        element.style {
            width: 183px;
        }
    </style>
    <div class="container-fluid">
    <div class="well_N">
        <div class="dp_sdw">
            <div id="stockReportLocationWiseViewDiv">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="panel">
                            <div class="panel-body">
                                <?php echo CommonHelper::headerPrintSectionInPrintView($m);?>
                                <?php //echo CommonHelper::displayPrintButtonInBlade('filterBookDayList','HrefHide','1');?>
                                <?php //echo CommonHelper::displayExportButton('EmpExitInterviewList','','1')?>
                                <form method="get">
                                <div class="row">
                                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                        <label>To Date</label>
                                        <input type="Date" name="toDate" id="toDate" value="{{ $currentMonthEndDate }}" class="form-control">
                                    </div>
                                    <div class="col-lg-1 col-md-1 col-sm-1 col-xs-12 text-right">
                                        <input type="submit" value="View Filter Data" class="btn btn-sm btn-danger"
                                            style="margin-top: 32px;" />
                                    </div>
                                </div>
                                </form>
                                <div id="filterSubItemList">
                                    
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
                                            <th class="text-center">Reorder Level</th>
                                        </thead>
                                        <tbody id="">
                                            <?php
                                                $counter=1;
                                            ?>  
                                            @foreach($subItems as $item)
                                                <tr>
                                                    <td class="text-center">{{$counter++}}</td>                
                                                    <td>{{ $item->category->main_ic ?? 'None' }}</td>
                                                    <td class="text-center">{{ $item->subItem->item_code ?? 'None' }}</td>
                                                    <td>{{ $item->subItem->sub_ic ?? 'None' }}</td>
                                                    <td class="text-center">{{ $item->subItem->uomData->uom_name ?? 'None' }}</td>
                                                    <td class="text-center">{{ $item->location->location_name ?? 'None' }}</td>                
                                                    <td class="text-center">{{ $item->stockLocationWiseSum() ?? 'None' }}</td>
                                                    <td class="text-center"><?php echo CommonHelper::getReorderLevelItemAndLocationWise($item->location_id,$item->sub_ic_id)?></td>                
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>   
    </div>
    <script src="{{ URL::asset('assets/custom/js/exportToExcelXlsx.js') }}"></script>
    <script !src="">
        function ExportToExcel(type, fn, dl) {
            var elt = document.getElementById('data');
            var wb = XLSX.utils.table_to_book(elt, { sheet: "sheet1" });
            return dl ?
                    XLSX.write(wb, { bookType: type, bookSST: true, type: 'base64' }) :
                    XLSX.writeFile(wb, fn || ('Stock Report <?php echo date('d-m-Y')?>.' + (type || 'xlsx')));
        }
        $(document).ready(function() {
            $('.select2').select2();
        });
        function viewSubItemList(){
            $('#filterSubItemList').html('<tr><td colspan="100"><div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center"><div class="loader"></div></div></div></div></td><tr>');
            var CategoryId = $('#filterCategoryId').val();
            var locationId = $('#location_id').val();
            var subItemId = $('#subitem_id').val();
            alert(locationId);
            var m = '<?php echo $m;?>';
                
            const uri = '{{ route('inventory.report.stock-locationwise') }}'
                    
            $.ajax({
                url: uri,
                type: "GET",
                data:{m:m, CategoryId:CategoryId, locationId: locationId, subItemId: subItemId},
                success:function(data) {
                    setTimeout(function(){
                        $('#filterSubItemList').html(data);
                    },1000);
                }
            });
            
        }
    </script>
@endsection