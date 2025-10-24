var baseUrl = $('#baseUrl').val();
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
    var functionName = $('#functionName').val();
    var tbodyId = $('#tbodyId').val();
    var filterType = $('#filterType').val();
    //alert(tbodyId);
    var m = $('#m').val();
    var url = ''+baseUrl+'/'+functionName+'';
    $('#'+tbodyId+'').html('<tr><td colspan="25"><div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div></div></td><tr>');
    if(filterType == 'bomList'){
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
    }else if(filterType == 'productionRequestList'){
        var fromDate = $('#fromDate').val();
        var toDate = $('#toDate').val();
        var selectFinishGoodsBulkId = $('#selectFinishGoodsBulkId').val();
        var selectFinishGoodsBulk = $('#selectFinishGoodsBulkTwo').val();
        var selectVoucherStatus = $('#selectVoucherStatus').val();
        $.getJSON(url, {fromDate: fromDate, toDate: toDate, m: m,selectFinishGoodsBulk:selectFinishGoodsBulk,selectFinishGoodsBulkId:selectFinishGoodsBulkId,selectVoucherStatus:selectVoucherStatus}, function (result) {
            $.each(result, function (i, field) {
                $('#' + tbodyId + '').html('' + field + '');
            });
        })
    }else if(filterType == 'StockRequirementList'){
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
    }else if(filterType == 'RMDRVoucherList'){
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
    }else if(filterType == 'PMDRVoucherList'){
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
    }else if(filterType == 'ARVoucherList'){
        var fromDate = $('#fromDate').val();
        var toDate = $('#toDate').val();
        var selectFinishGoodsId = $('#selectFinishGoodsId').val();
        var selectFinishGoods = $('#selectFinishGoodsTwo').val();
        var selectVoucherStatus = $('#selectVoucherStatus').val();
        var selectRequestStatus = $('#selectRequestStatus').val();
        $.getJSON(url, {fromDate: fromDate, toDate: toDate, m: m,selectFinishGoods:selectFinishGoods,selectFinishGoodsId:selectFinishGoodsId,selectVoucherStatus:selectVoucherStatus,selectRequestStatus:selectRequestStatus}, function (result) {
            $.each(result, function (i, field) {
                $('#' + tbodyId + '').html('' + field + '');
            });
        })
    }else if(filterType == 'ARDVoucherList'){
        var fromDate = $('#fromDate').val();
        var toDate = $('#toDate').val();
        var selectFinishGoodsId = $('#selectFinishGoodsId').val();
        var selectFinishGoods = $('#selectFinishGoodsTwo').val();
        var selectVoucherStatus = $('#selectVoucherStatus').val();
        var selectRequestStatus = $('#selectRequestStatus').val();
        $.getJSON(url, {fromDate: fromDate, toDate: toDate, m: m,selectFinishGoods:selectFinishGoods,selectFinishGoodsId:selectFinishGoodsId,selectVoucherStatus:selectVoucherStatus,selectRequestStatus:selectRequestStatus}, function (result) {
            $.each(result, function (i, field) {
                $('#' + tbodyId + '').html('' + field + '');
            });
        })
    }else if(filterType == 2){

    }else if(filterType == 3){

    }
}

function deleteCompanyProductionTwoTableRecords(m,voucherStatus,rowStatus,columnValue,columnOne,columnTwo,columnThree,tableOne,tableTwo){
    $.ajax({
        url: ''+baseUrl+'/prd/deleteCompanyProductionTwoTableRecords',
        type: "GET",
        data: {m:m,voucherStatus:voucherStatus,rowStatus:rowStatus,columnValue:columnValue,columnOne:columnOne,columnTwo:columnTwo,columnThree:columnThree,tableOne:tableOne,tableTwo:tableTwo},
        success:function(data) {
            filterVoucherList();
        }
    });
}

function repostCompanyProductionTwoTableRecords(m,voucherStatus,rowStatus,columnValue,columnOne,columnTwo,columnThree,tableOne,tableTwo){
    $.ajax({
        url: ''+baseUrl+'/prd/repostCompanyProductionTwoTableRecords',
        type: "GET",
        data: {m:m,voucherStatus:voucherStatus,rowStatus:rowStatus,columnValue:columnValue,columnOne:columnOne,columnTwo:columnTwo,columnThree:columnThree,tableOne:tableOne,tableTwo:tableTwo},
        success:function(data) {
            filterVoucherList();
        }
    });
}

function deleteBomDetail(param1,param2,param3){
    var functionName = 'prd/deleteBomDetail';
    $.ajax({
        url: ''+baseUrl+'/'+functionName+'',
        type: "GET",
        data: {companyId:param1,bomNo:param2},
        success:function(data) {
            filterVoucherList();
            alert(data);
        }
    });
}

function approveBomDetail(param1,param2,param3){
    var functionName = 'prd/approveBomDetail';
    $.ajax({
        url: ''+baseUrl+'/'+functionName+'',
        type: "GET",
        data: {companyId:param1,bomNo:param2},
        success:function(data) {
            filterVoucherList();
            alert(data);
        }
    });
}



function restoredBomDetail(param1,param2,param3){
    var functionName = 'prd/restoredBomDetail';
    $.ajax({
        url: ''+baseUrl+'/'+functionName+'',
        type: "GET",
        data: {companyId:param1,bomNo:param2,finishGoodBulkId:param3},
        success:function(data) {
            filterVoucherList();
            alert(data);
        }
    });
}




$("#selectFinishGoodsTwo").change(function() {
    var selectFinishGoodsTwo = $('#selectFinishGoods option[value="' + $('#selectFinishGoodsTwo').val() + '"]').data('id');
    $('#selectFinishGoodsId').val(selectFinishGoodsTwo);
}).change();

$("#selectFinishGoodsBulkTwo").change(function() {
    var selectFinishGoodsBulkTwo = $('#selectFinishGoodsBulk option[value="' + $('#selectFinishGoodsBulkTwo').val() + '"]').data('id');
    $('#selectFinishGoodsBulkId').val(selectFinishGoodsBulkTwo);
}).change();

