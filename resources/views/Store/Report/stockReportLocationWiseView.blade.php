
<?php
use App\Helpers\CommonHelper;
use App\Helpers\ReuseableCode;
$export = true;
$m = getSessionCompanyId();
?>
<?php use App\Helpers\PurchaseHelper; ?>
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
             <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">
                <?php echo CommonHelper::displayPrintButtonInBlade('stockReportLocationWiseViewDiv','','1');?>
                <?php echo CommonFacades::displayExportButton('stockReportLocationWiseView', '', '1'); ?>
            </div>

            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                <div class="form-group ">
                    <label for="email">Category</label>                
                    <select name="filterCategoryId" id="filterCategoryId" class="form-control select2">
                        <?php echo PurchaseFacades::categoryList($m,'0');?>
                    </select>
                </div>
            </div>

            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                <div class="form-group ">
                    <label for="email">Location</label>
                    <select name="location_id" id="location_id" class="form-control select2" required>
                        <?php echo SelectListFacades::getLocationList($m,1,0);?>
                    </select>
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                <div class="form-group ">
                    <label for="email">Items</label>
                    <select name="subitem_id" id="subitem_id" class="form-control select2" required>
                        <option value="">Select Item</option>
                        @foreach ($subItems as $item)
                            <option value="{{ $item->id }}">{{ $item->item_code.' <--> '.$item->sub_ic }}</option>                            
                        @endforeach
                    </select>
                </div>
            </div>

        </div>


        <div class="row">

            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                <div>&nbsp;</div>
                <button onclick="viewSubItemList()" type="button" class="btn btn-sm btn-primary" style="margin: 5px 0px 0px px;" onclick="BookDayList();">Submit</button>
                </div>

            </div>

        </div>






            <div id="stockReportLocationWiseViewDiv">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="panel">
                            <div class="panel-body">
                                <?php //echo CommonHelper::headerPrintSectionInPrintView($m);?>
                                <?php //echo CommonHelper::displayPrintButtonInBlade('filterBookDayList','HrefHide','1');?>
                                <?php //echo CommonHelper::displayExportButton('EmpExitInterviewList','','1')?>
                                <div id="filterSubItemList"></div>

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
    </script>
<script>
    $(document).ready(function() {
        $('.select2').select2();
    });
    function viewSubItemList(){
        $('#filterSubItemList').html('<tr><td colspan="100"><div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center"><div class="loader"></div></div></div></div></td><tr>');
        var CategoryId = $('#filterCategoryId').val();
        var locationId = $('#location_id').val();
        var subItemId = $('#subitem_id').val();
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