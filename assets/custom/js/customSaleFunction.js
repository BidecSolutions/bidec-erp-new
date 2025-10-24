window.onload=function () {
    var pageType = $('#pageType').val();
    if(pageType == 0){
        filterVoucherList();
    }else if(pageType == 1){
        viewDataFilterOneParameter();
    }
}
var baseUrl = $('#baseUrl').val();

function viewDataFilterOneParameter() {
    var paramOne = $('#paramOne').val();
    var functionName = $('#functionName').val();
    var divId = $('#divId').val();
    var fromDate = $('#fromDate').val();
    var toDate = $('#toDate').val();
    var parentCode = $('#parentCode').val();
    var m = $('#m').val();

    $('#'+divId+'').html('<div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div></div>');
    $.ajax({
        url: ''+baseUrl+'/'+functionName+'',
        method:'GET',
        data:{fromDate:fromDate,toDate:toDate,m:m,parentCode:parentCode,paramOne:paramOne},
        error: function(){
            alert('error');
        },
        success: function(response){
            setTimeout(function(){
                $('#'+divId+'').html(response);
            },1000);
        }
    });
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
    var fromDate = $('#fromDate').val();
    var toDate = $('#toDate').val();
    var functionName = $('#functionName').val();
    var tbodyId = $('#tbodyId').val();
    //alert(tbodyId);
    var m = $('#m').val();
    var filterType = $('#filterType').val();
    var url = ''+baseUrl+'/'+functionName+'';
    $('#'+tbodyId+'').html('<tr><td colspan="50"><div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div></div></td><tr>');
    if(filterType == 1) {
        $.getJSON(url, {fromDate: fromDate, toDate: toDate, m: m}, function (result) {
            $.each(result, function (i, field) {
                $('#' + tbodyId + '').html('' + field + '');
            });
        })
    }else if(filterType == 2){
        var selectSubItemId = $('#selectSubItemId').val();
        var selectSubItem = $('#selectSubItemTwo').val();
        var selectCustomerId = $('#selectCustomerId').val();
        var selectCustomer = $('#selectCustomerTwo').val();
        var selectVoucherStatus = $('#selectVoucherStatus').val();
        $.getJSON(url, {fromDate: fromDate, toDate: toDate, m: m,selectCustomer:selectCustomer,selectCustomerId:selectCustomerId,selectSubItem:selectSubItem,selectSubItemId:selectSubItemId,selectVoucherStatus:selectVoucherStatus}, function (result) {
            $.each(result, function (i, field) {
                $('#' + tbodyId + '').html('' + field + '');
            });
        })
    }

}

function creditSaleVoucherApprove(m,invoiceNo) {
    $.ajax({
        url: ''+baseUrl+'/sa/creditSaleVoucherApprove',
        type: "GET",
        data: {m:m,invoiceNo:invoiceNo},
        success:function(data) {
            filterVoucherList();
        }
    });
}

function deleteCashSaleVoucher(param1,param2,param3,param4) {
    var m = param1;
    var invoiceNo = param2;
    var journalVouhcer = param3;
    var receiptVoucher = param4;
    var agree = confirm("Are you sure you want to delete this Voucher?");
    if(agree){
        $.ajax({
            url: '' + baseUrl + '/sa/cashSaleVoucherDelete',
            type: "GET",
            data: {m: m, invoiceNo: invoiceNo, journalVouhcer: journalVouhcer, receiptVoucher: receiptVoucher},
            success: function (data) {
                filterVoucherList();
            }
        });
    }else{
        return false;
    }
}


function repostCashSaleVoucher(param1,param2,param3,param4) {
    var m = param1;
    var invoiceNo = param2;
    var journalVouhcer = param3;
    var receiptVoucher = param4;
    var agree = confirm("Are you sure you want to repost this Voucher?");
    if(agree){
        $.ajax({
            url: '' + baseUrl + '/sa/cashSaleVoucherRepost',
            type: "GET",
            data: {m: m, invoiceNo: invoiceNo, journalVouhcer: journalVouhcer, receiptVoucher: receiptVoucher},
            success: function (data) {
                filterVoucherList();
            }
        });
    }else{
        return false;
    }
}

$("#selectSubItemTwo").change(function() {
    var selectSubItemTwo = $('#selectSubItem option[value="' + $('#selectSubItemTwo').val() + '"]').data('id');
    $('#selectSubItemId').val(selectSubItemTwo);
}).change();

$("#selectCustomerTwo").change(function() {
    var selectCustomerTwo = $('#selectCustomer option[value="' + $('#selectCustomerTwo').val() + '"]').data('id');
    $('#selectCustomerId').val(selectCustomerTwo);
}).change();