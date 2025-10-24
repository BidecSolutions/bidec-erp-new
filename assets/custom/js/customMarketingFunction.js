window.onload=function () {
    var pageType = $('#pageType').val();
    if(pageType == 0){
        filterVoucherList();
    }else if(pageType == 1){
        viewDataFilterOneParameter();
    }else if(pageType == 2){
        viewDataFilterTwoParameter();
    }
}


var baseUrl = $('#baseUrl').val();


function viewRangeWiseDataFilter() {
    var fromDate = $('#fromDate').val();
    var toDate = $('#toDate').val();
    // Parse the entries
    var startDate = Date.parse(fromDate);
    var endDate = Date.parse(toDate);
    // Make sure they are valid
    if (isNaN(startDate)) {
        alert("The start date provided is not valid, please enter a valid date.");
        return false;
    }
    if (isNaN(endDate)) {
        alert("The end date provided is not valid, please enter a valid date.");
        return false;
    }
    // Check the date range, 86400000 is the number of milliseconds in one day
    var difference = (endDate - startDate) / (86400000 * 7);
    if (difference < 0) {
        alert("The start date must come before the end date.");
        return false;
    }
    filterVoucherList();
}
function filterVoucherList(){
    var fromDate = $('#fromDate').val();
    var toDate = $('#toDate').val();
    var functionName = $('#functionName').val();
    var tbodyId = $('#tbodyId').val();
    var filterType = $('#filterType').val();
    //alert(tbodyId);
    var m = $('#m').val();
    var url = ''+baseUrl+'/'+functionName+'';
    //alert(url); return false;
    $('#'+tbodyId+'').html('<tr><td colspan="15"><div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div></div></td><tr>');
    if(filterType == 'StockRequirementList'){
        var fromDate = $('#fromDate').val();
        var toDate = $('#toDate').val();
        var selectFinishGoodsId = $('#selectFinishGoodsId').val();
        var selectFinishGoods = $('#selectFinishGoodsTwo').val();
        var selectVoucherStatus = $('#selectVoucherStatus').val();
        $.getJSON(url, {fromDate: fromDate, toDate: toDate, m: m,selectFinishGoods:selectFinishGoods,selectFinishGoodsId:selectFinishGoodsId,selectVoucherStatus:selectVoucherStatus}, function (result) {
            $.each(result, function (i, field) {
                $('#' + tbodyId + '').html('' + field + '');
            });
        })
    }

}

function deleteCompanyMarketingTwoTableRecords(m,voucherStatus,rowStatus,columnValue,columnOne,columnTwo,columnThree,tableOne,tableTwo){
    $.ajax({
        url: ''+baseUrl+'/md/deleteCompanyMarketingTwoTableRecords',
        type: "GET",
        data: {m:m,voucherStatus:voucherStatus,rowStatus:rowStatus,columnValue:columnValue,columnOne:columnOne,columnTwo:columnTwo,columnThree:columnThree,tableOne:tableOne,tableTwo:tableTwo},
        success:function(data) {
            filterVoucherList();
        }
    });
}

function repostCompanyMarketingTwoTableRecords(m,voucherStatus,rowStatus,columnValue,columnOne,columnTwo,columnThree,tableOne,tableTwo){
    $.ajax({
        url: ''+baseUrl+'/md/repostCompanyMarketingTwoTableRecords',
        type: "GET",
        data: {m:m,voucherStatus:voucherStatus,rowStatus:rowStatus,columnValue:columnValue,columnOne:columnOne,columnTwo:columnTwo,columnThree:columnThree,tableOne:tableOne,tableTwo:tableTwo},
        success:function(data) {
            filterVoucherList();
        }
    });
}

$("#selectSubDepartmentTwo").change(function() {
    var selectSubDepartmentTwo = $('#selectSubDepartment option[value="' + $('#selectSubDepartmentTwo').val() + '"]').data('id');
    $('#selectSubDepartmentId').val(selectSubDepartmentTwo);
}).change();

$("#selectEmployeeTwo").change(function() {
    var selectEmployeeTwo = $('#selectEmployee option[value="' + $('#selectEmployeeTwo').val() + '"]').data('id');
    $('#selectEmployeeId').val(selectEmployeeTwo);
}).change();

$("#selectFinishGoodsTwo").change(function() {
    var selectFinishGoodsTwo = $('#selectFinishGoods option[value="' + $('#selectFinishGoodsTwo').val() + '"]').data('id');
    $('#selectFinishGoodsId').val(selectFinishGoodsTwo);
}).change();