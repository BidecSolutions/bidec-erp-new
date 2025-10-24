window.onload=function () {
    var pageType = $('#pageType').val();
    if(pageType == 'EBAESMWReport'){
        eventBookedAndExecutedSameMonthWiseReport();
    }else if(pageType == 'YECSReport'){
        yearlyEventCalenderStatusReport();
    }else if(pageType == 'EBOMYAEOMYWReport'){
        eventBookedOtherMonthYearAndExecutedOtherMonthYearWiseReport();
    }else if(pageType == 'FPROMYAEOMYWReport'){
        fullPaymentReceivedOtherMonthYearAndExecutedOtherMonthYearWiseReport();
    }else if(pageType == 'MEReport'){
        viewMonthlyExpenseReport();
    }else if(pageType == 'fVDWSIReport'){
		viewFilterDateWiseStockInventoryReport();
	}else if(pageType == 'CABBReport'){
		viewFilterCashAndBankBookReport();
	}else if(pageType == 'CABBDWReport'){
		viewFilterCashAndBankBookDayWiseReport();
	}else if(pageType == 'fVRABReport'){
		viewRangeWiseDataFilter();
	}else if(pageType == 'fEBRWReport'){
        viewRangeWiseDataFilter();
    }else if(pageType == 'fVCABReport'){
        viewRangeWiseDataFilter();
    }else if(pageType == 'VEReportRangeWise'){
        viewVariableExpenseReportRangeWise();
    }else if(pageType == 'FEReportRangeWise'){
        viewFixedExpenseReportRangeWise();
    }else if(pageType == 'SEReportRangeWise'){
        viewSalaryExpenseReportRangeWise();
    }else if(pageType == 'BCReport'){
        bookingComparisanReport();
    }else if(pageType == 'BCCReport'){
        bookingCollectionComparisanReport();
    }else if(pageType == 'BRCReport'){
        bookingRefundComparisanReport();
    }else if(pageType == 'EPCReport'){
        expensePaymentComparisanReport();
    }else if(pageType == 'SWCCReport'){
        serviceWiseCollectionComparisanReport();
    }else if(pageType == 'SWRCReport'){
        serviceWiseRevenueComparisanReport();
    }else if(pageType == 'fVAPALPEReport'){
        viewRangeWiseDataFilter();
    }else if(pageType == 'ValuationReport'){
        valuationReport();
    }else if(pageType == 'BChartsReport'){
        bookingChartsReport();
    }else if(pageType == 'BCChartsReport'){
        bookingCChartsReport();
    }else if(pageType == 'VECReportRangeWise'){
        viewVariableExpenseComparisanReportRangeWise();
    }else if(pageType == 'FECReportRangeWise'){
        viewFixedExpenseComparisanReportRangeWise();
    }else if(pageType == 'SECReportRangeWise'){
        viewSalaryExpenseComparisanReportRangeWise();
    }else if(pageType == 'FMWSSReportRangeWise'){
		viewMonthWiseSummaryStatementReport();
	}else if(pageType == 'FASSReportRangeWise'){
		viewAccountsStatementSummaryReport();
	}else if(pageType == 'CECReportRangeWise'){
		viewCapitalExpenseComparisanReportRangeWise();
	}else if(pageType == 'ROCEReportRangeWise'){
		viewRefundOfCancelEventsComparisanReportRangeWise();
	}else if(pageType == 'BCECReportRangeWise'){
		viewBankChargesExpenseComparisanReportRangeWise();
	}else if(pageType == 'MICReportRangeWise'){
		viewMarqueeInvestmentsComparisanReportRangeWise();
	}else if(pageType == 'FPAReportRangeWise'){
		viewProfitAccountsReportRangeWise();
	}else if(pageType == 'FEBAReportRangeWise'){
		viewEventBookingAccountsReportRangeWise();
	}else if(pageType == 'FRSSReportRangeWise'){
		viewReconcilationStatementSummaryReport();
	}else if(pageType == 'ECAPRRWReport'){
		eventCancelAndPaymentRefundRangeWiseReport();
	}else if(pageType == 'FVCABBSMRangeWiseReport'){
		filterViewCashAndBankBookSummaryMonthRangeWiseReport();
	}else if(pageType == 'FVMEBAMRangeWiseReport'){
		filterViewMarqueeEventBookingAccountsMonthRangeWiseReport();
	}else if(pageType == 'fVBSAPALReport'){
		viewRangeWiseDataFilter();
	}else if(pageType == 'NBAPAFPMWReport'){
		filterNewBookingAdvancePartAndFinalPaymentMonthWiseReport();
	}else if(pageType == 'fBMSSRWReport'){
		filterBookingMonthlyStatementSummaryRangeWiseReport();
	}else if(pageType == 'CRRAEHWReport'){
		filterCurrentRangeRecoveryAndExpenseHeadWiseReport();
	}else if(pageType == 'ESRAHWReport'){
		filterExpenseSummaryRangeAndHeadWiseReport();
	}
	
	
	
	
}
var baseUrl = $('#baseUrl').val();



function filterViewMarqueeEventBookingAccountsMonthRangeWiseReport(){
	var fromMonth = $('#fromMonth').val();
    var toMonth = $('#toMonth').val();
	
    var functionName = $('#functionName').val();
    var tbodyId = $('#tbodyId').val();
    var filterType = $('#filterType').val();
    var m = $('#m').val();
    
	var url = ''+baseUrl+'/'+functionName+'';
    //alert(url); return false;
    $('#'+tbodyId+'').html('<tr><td colspan="100"><div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div></div></td></tr>');
    
	$.ajax({
        url: ''+baseUrl+'/'+functionName+'',
        method:'GET',
        data:{fromMonth: fromMonth, toMonth: toMonth, m: m},
        error: function(){
            alert('error');
        },
        success: function(response){
            //setTimeout(function(){
                $('#'+tbodyId+'').html(response);
            //},1000);
        }
    });
}

function filterViewCashAndBankBookSummaryMonthRangeWiseReport(){
	var fromMonth = $('#fromMonth').val();
    var toMonth = $('#toMonth').val();
	
    var functionName = $('#functionName').val();
    var tbodyId = $('#tbodyId').val();
    var filterType = $('#filterType').val();
    var m = $('#m').val();
    
	var url = ''+baseUrl+'/'+functionName+'';
    //alert(url); return false;
    $('#'+tbodyId+'').html('<tr><td colspan="100"><div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div></div></td></tr>');
    
	$.ajax({
        url: ''+baseUrl+'/'+functionName+'',
        method:'GET',
        data:{fromMonth: fromMonth, toMonth: toMonth, m: m},
        error: function(){
            alert('error');
        },
        success: function(response){
            //setTimeout(function(){
                $('#'+tbodyId+'').html(response);
            //},1000);
        }
    });
}

function viewAccountsStatementSummaryReport(){
	var filterMonthYear = $('#filterMonthYear').val();
	var functionName = $('#functionName').val();
    var tbodyId = $('#tbodyId').val();
    var filterType = $('#filterType').val();
    var m = $('#m').val();
    
	var url = ''+baseUrl+'/'+functionName+'';
    //alert(url); return false;
    $('#'+tbodyId+'').html('<div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div></div>');
    $.ajax({
        url: ''+baseUrl+'/'+functionName+'',
        method:'GET',
        data:{filterMonthYear: filterMonthYear, m: m},
        error: function(){
            alert('error');
        },
        success: function(response){
            //setTimeout(function(){
                $('#'+tbodyId+'').html(response);
            //},1000);
        }
    });
}

function viewReconcilationStatementSummaryReport(){
	var filterMonthYear = $('#filterMonthYear').val();
	var functionName = $('#functionName').val();
    var tbodyId = $('#tbodyId').val();
    var filterType = $('#filterType').val();
    var m = $('#m').val();
    
	var url = ''+baseUrl+'/'+functionName+'';
    //alert(url); return false;
    $('#'+tbodyId+'').html('<div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div></div>');
    $.ajax({
        url: ''+baseUrl+'/'+functionName+'',
        method:'GET',
        data:{filterMonthYear: filterMonthYear, m: m},
        error: function(){
            alert('error');
        },
        success: function(response){
            //setTimeout(function(){
                $('#'+tbodyId+'').html(response);
            //},1000);
        }
    });
}

function viewMonthWiseSummaryStatementReport(){
	var filterMonthYear = $('#filterMonthYear').val();
	var functionName = $('#functionName').val();
    var tbodyId = $('#tbodyId').val();
    var filterType = $('#filterType').val();
    var m = $('#m').val();
    
	var url = ''+baseUrl+'/'+functionName+'';
    //alert(url); return false;
    $('#'+tbodyId+'').html('<div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div></div>');
    $.ajax({
        url: ''+baseUrl+'/'+functionName+'',
        method:'GET',
        data:{filterMonthYear: filterMonthYear, m: m},
        error: function(){
            alert('error');
        },
        success: function(response){
            //setTimeout(function(){
                $('#'+tbodyId+'').html(response);
            //},1000);
        }
    });
}

function viewVariableExpenseComparisanReportRangeWise(){
	var fromMonth = $('#fromMonth').val();
    var toMonth = $('#toMonth').val();
    var filterExpenseAccountName = $('#filterExpenseAccountName').val();
	var filterExpenseAccountNameText = $('#filterExpenseAccountName option:selected').text();
	
	var functionName = $('#functionName').val();
    var tbodyId = $('#tbodyId').val();
    var filterType = $('#filterType').val();
    var m = $('#m').val();
    
	var url = ''+baseUrl+'/'+functionName+'';
    //alert(url); return false;
    $('#'+tbodyId+'').html('<tr><td colspan="100"><div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div></div></td></tr>');
    $.getJSON(url, {fromMonth: fromMonth, toMonth: toMonth, m: m,filterExpenseAccountName:filterExpenseAccountName,filterExpenseAccountNameText:filterExpenseAccountNameText}, function (result) {
        $.each(result, function (i, field) {
            $('#' + tbodyId + '').html('' + field + '');
        });
    })
}

function viewFixedExpenseComparisanReportRangeWise(){
	var fromMonth = $('#fromMonth').val();
    var toMonth = $('#toMonth').val();
    var filterExpenseAccountName = $('#filterExpenseAccountName').val();
	var filterExpenseAccountNameText = $('#filterExpenseAccountName option:selected').text();
	
	var functionName = $('#functionName').val();
    var tbodyId = $('#tbodyId').val();
    var filterType = $('#filterType').val();
    var m = $('#m').val();
    
	var url = ''+baseUrl+'/'+functionName+'';
    //alert(url); return false;
    $('#'+tbodyId+'').html('<tr><td colspan="100"><div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div></div></td></tr>');
    $.getJSON(url, {fromMonth: fromMonth, toMonth: toMonth, m: m,filterExpenseAccountName:filterExpenseAccountName,filterExpenseAccountNameText:filterExpenseAccountNameText}, function (result) {
        $.each(result, function (i, field) {
            $('#' + tbodyId + '').html('' + field + '');
        });
    })
}

function viewSalaryExpenseComparisanReportRangeWise(){
	var fromMonth = $('#fromMonth').val();
    var toMonth = $('#toMonth').val();
    var filterExpenseAccountName = $('#filterExpenseAccountName').val();
	var filterExpenseAccountNameText = $('#filterExpenseAccountName option:selected').text();
	
	var functionName = $('#functionName').val();
    var tbodyId = $('#tbodyId').val();
    var filterType = $('#filterType').val();
    var m = $('#m').val();
    
	var url = ''+baseUrl+'/'+functionName+'';
    //alert(url); return false;
    $('#'+tbodyId+'').html('<tr><td colspan="100"><div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div></div></td></tr>');
    $.getJSON(url, {fromMonth: fromMonth, toMonth: toMonth, m: m,filterExpenseAccountName:filterExpenseAccountName,filterExpenseAccountNameText:filterExpenseAccountNameText}, function (result) {
        $.each(result, function (i, field) {
            $('#' + tbodyId + '').html('' + field + '');
        });
    })
}

function viewCapitalExpenseComparisanReportRangeWise(){
	var fromMonth = $('#fromMonth').val();
    var toMonth = $('#toMonth').val();
    var functionName = $('#functionName').val();
    var tbodyId = $('#tbodyId').val();
    var filterType = $('#filterType').val();
    var m = $('#m').val();
    
	var url = ''+baseUrl+'/'+functionName+'';
    //alert(url); return false;
    $('#'+tbodyId+'').html('<tr><td colspan="100"><div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div></div></td></tr>');
    $.getJSON(url, {fromMonth: fromMonth, toMonth: toMonth, m: m}, function (result) {
        $.each(result, function (i, field) {
            $('#' + tbodyId + '').html('' + field + '');
        });
    })
}


function viewRefundOfCancelEventsComparisanReportRangeWise(){
	var fromMonth = $('#fromMonth').val();
    var toMonth = $('#toMonth').val();
	
    var filterLawnId = $('#filterLawnId').val();
	var filterLawnIdText = $('#filterLawnId option:selected').text();
	
	var functionName = $('#functionName').val();
    var tbodyId = $('#tbodyId').val();
    var filterType = $('#filterType').val();
    var m = $('#m').val();
    
	var url = ''+baseUrl+'/'+functionName+'';
    //alert(url); return false;
    $('#'+tbodyId+'').html('<tr><td colspan="100"><div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div></div></td></tr>');
    $.getJSON(url, {fromMonth: fromMonth, toMonth: toMonth, m: m,filterLawnId:filterLawnId,filterLawnIdText:filterLawnIdText}, function (result) {
        $.each(result, function (i, field) {
            $('#' + tbodyId + '').html('' + field + '');
        });
    })
}

function viewBankChargesExpenseComparisanReportRangeWise(){
	var fromMonth = $('#fromMonth').val();
    var toMonth = $('#toMonth').val();
	
    var filterAccountId = $('#filterAccountId').val();
	var filterAccountIdText = $('#filterAccountId option:selected').text();
	
	var functionName = $('#functionName').val();
    var tbodyId = $('#tbodyId').val();
    var filterType = $('#filterType').val();
    var m = $('#m').val();
    
	var url = ''+baseUrl+'/'+functionName+'';
    //alert(url); return false;
    $('#'+tbodyId+'').html('<tr><td colspan="100"><div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div></div></td></tr>');
    $.getJSON(url, {fromMonth: fromMonth, toMonth: toMonth, m: m,filterAccountId:filterAccountId,filterAccountIdText:filterAccountIdText}, function (result) {
        $.each(result, function (i, field) {
            $('#' + tbodyId + '').html('' + field + '');
        });
    })
}

function viewMarqueeInvestmentsComparisanReportRangeWise(){
	var fromMonth = $('#fromMonth').val();
    var toMonth = $('#toMonth').val();
	
    var filterDistributionAccountId = $('#filterDistributionAccountId').val();
	var filterDistributionAccountIdText = $('#filterDistributionAccountId option:selected').text();
	
	var functionName = $('#functionName').val();
    var tbodyId = $('#tbodyId').val();
    var filterType = $('#filterType').val();
    var m = $('#m').val();
    
	var url = ''+baseUrl+'/'+functionName+'';
    //alert(url); return false;
    $('#'+tbodyId+'').html('<tr><td colspan="100"><div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div></div></td></tr>');
    $.getJSON(url, {fromMonth: fromMonth, toMonth: toMonth, m: m,filterDistributionAccountId:filterDistributionAccountId,filterDistributionAccountIdText:filterDistributionAccountIdText}, function (result) {
        $.each(result, function (i, field) {
            $('#' + tbodyId + '').html('' + field + '');
        });
    })
}


function viewProfitAccountsReportRangeWise(){
	var fromMonth = $('#fromMonth').val();
    var toMonth = $('#toMonth').val();
	
    var functionName = $('#functionName').val();
    var tbodyId = $('#tbodyId').val();
    var filterType = $('#filterType').val();
    var m = $('#m').val();
    
	var url = ''+baseUrl+'/'+functionName+'';
    //alert(url); return false;
    $('#'+tbodyId+'').html('<tr><td colspan="100"><div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div></div></td></tr>');
    
	$.ajax({
        url: ''+baseUrl+'/'+functionName+'',
        method:'GET',
        data:{fromMonth: fromMonth, toMonth: toMonth, m: m},
        error: function(){
            alert('error');
        },
        success: function(response){
            //setTimeout(function(){
                $('#'+tbodyId+'').html(response);
            //},1000);
        }
    });
}

function viewEventBookingAccountsReportRangeWise(){
	var fromMonth = $('#fromMonth').val();
    var toMonth = $('#toMonth').val();
	
    var functionName = $('#functionName').val();
    var tbodyId = $('#tbodyId').val();
    var filterType = $('#filterType').val();
    var m = $('#m').val();
    
	var url = ''+baseUrl+'/'+functionName+'';
    //alert(url); return false;
    $('#'+tbodyId+'').html('<tr><td colspan="100"><div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div></div></td></tr>');
    
	$.ajax({
        url: ''+baseUrl+'/'+functionName+'',
        method:'GET',
        data:{fromMonth: fromMonth, toMonth: toMonth, m: m},
        error: function(){
            alert('error');
        },
        success: function(response){
            //setTimeout(function(){
                $('#'+tbodyId+'').html(response);
            //},1000);
        }
    });
}








function filterExpenseSummaryRangeAndHeadWiseReport(){
	var fromDates = $('#fromDate').val();
    var toDates = $('#toDate').val();
    
	
    // Parse the entries
		var date = fromDates;
        var fromDate = date.split("-").reverse().join("-");
        var date1 = toDates;
        var toDate = date1.split("-").reverse().join("-");
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
	
	var filterAccountHeadArray=[]; 
	$('select[name="filterAccountHead[]"] option:selected').each(function() {
		filterAccountHeadArray.push($(this).val());
	});
	if(filterAccountHeadArray == ''){
		alert('Something Wrong! Please Select Account Head.');
		return false;
	}

    var functionName = $('#functionName').val();
    var tbodyId = $('#tbodyId').val();
    var filterType = $('#filterType').val();
    var m = $('#m').val();
    
	$('#'+tbodyId+'').html('<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div>');
    $.ajax({
        url: ''+baseUrl+'/'+functionName+'',
        method:'GET',
        data:{fromDate: fromDate, toDate: toDate, m: m,filterAccountHeadArray:filterAccountHeadArray},
        error: function(){
            alert('error');
        },
        success: function(response){
			$('#'+tbodyId+'').html(response);
        }
    });
}


function filterCurrentRangeRecoveryAndExpenseHeadWiseReport(){
	var fromDates = $('#fromDate').val();
    var toDates = $('#toDate').val();
    
	
    // Parse the entries
		var date = fromDates;
        var fromDate = date.split("-").reverse().join("-");
        var date1 = toDates;
        var toDate = date1.split("-").reverse().join("-");
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
	
	var filterAccountHeadArray=[]; 
	$('select[name="filterAccountHead[]"] option:selected').each(function() {
		filterAccountHeadArray.push($(this).val());
	});
	if(filterAccountHeadArray == ''){
		alert('Something Wrong! Please Select Account Head.');
		return false;
	}

    var functionName = $('#functionName').val();
    var tbodyId = $('#tbodyId').val();
    var filterType = $('#filterType').val();
    var m = $('#m').val();
    
	$('#'+tbodyId+'').html('<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div>');
    $.ajax({
        url: ''+baseUrl+'/'+functionName+'',
        method:'GET',
        data:{fromDate: fromDate, toDate: toDate, m: m,filterAccountHeadArray:filterAccountHeadArray},
        error: function(){
            alert('error');
        },
        success: function(response){
			$('#'+tbodyId+'').html(response);
        }
    });
}



function viewVariableExpenseReportRangeWise(){
	var fromDate = $('#fromDate').val();
    var toDate = $('#toDate').val();
    var filterExpenseAccountName = $('#filterExpenseAccountName').val();
	var filterExpenseAccountNameText = $('#filterExpenseAccountName option:selected').text();
	
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

    var functionName = $('#functionName').val();
    var tbodyId = $('#tbodyId').val();
    var filterType = $('#filterType').val();
    var m = $('#m').val();
    
	var url = ''+baseUrl+'/'+functionName+'';
    //alert(url); return false;
    $('#'+tbodyId+'').html('<tr><td colspan="100"><div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div></div></td></tr>');
    $.getJSON(url, {fromDate: fromDate, toDate: toDate, m: m,filterExpenseAccountName:filterExpenseAccountName,filterExpenseAccountNameText:filterExpenseAccountNameText}, function (result) {
        $.each(result, function (i, field) {
            $('#' + tbodyId + '').html('' + field + '');
        });
    })
}



function viewFixedExpenseReportRangeWise(){
	var fromDate = $('#fromDate').val();
    var toDate = $('#toDate').val();
    var filterExpenseAccountName = $('#filterExpenseAccountName').val();
	var filterExpenseAccountNameText = $('#filterExpenseAccountName option:selected').text();
	
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

    var functionName = $('#functionName').val();
    var tbodyId = $('#tbodyId').val();
    var filterType = $('#filterType').val();
    var m = $('#m').val();
    
	var url = ''+baseUrl+'/'+functionName+'';
    //alert(url); return false;
    $('#'+tbodyId+'').html('<tr><td colspan="100"><div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div></div></td></tr>');
    $.getJSON(url, {fromDate: fromDate, toDate: toDate, m: m,filterExpenseAccountName:filterExpenseAccountName,filterExpenseAccountNameText:filterExpenseAccountNameText}, function (result) {
        $.each(result, function (i, field) {
            $('#' + tbodyId + '').html('' + field + '');
        });
    })
}

function viewSalaryExpenseReportRangeWise(){
	var fromDate = $('#fromDate').val();
    var toDate = $('#toDate').val();
    var filterExpenseAccountName = $('#filterExpenseAccountName').val();
	var filterExpenseAccountNameText = $('#filterExpenseAccountName option:selected').text();
	
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

    var functionName = $('#functionName').val();
    var tbodyId = $('#tbodyId').val();
    var filterType = $('#filterType').val();
    var m = $('#m').val();
    
	var url = ''+baseUrl+'/'+functionName+'';
    //alert(url); return false;
    $('#'+tbodyId+'').html('<tr><td colspan="100"><div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div></div></td></tr>');
    $.getJSON(url, {fromDate: fromDate, toDate: toDate, m: m,filterExpenseAccountName:filterExpenseAccountName,filterExpenseAccountNameText:filterExpenseAccountNameText}, function (result) {
        $.each(result, function (i, field) {
            $('#' + tbodyId + '').html('' + field + '');
        });
    })
}

function viewMonthlyExpenseReport(){
    var fromDate = $('#fromDate').val();
    var toDate = $('#toDate').val();
    var filterTypeDate = $('#filterTypeDate').val();
	var filterHeadAccount = $('#filterHeadAccount').val();
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

    var functionName = $('#functionName').val();
    var tbodyId = $('#tbodyId').val();
    var filterType = $('#filterType').val();
    //alert(tbodyId);
    var m = $('#m').val();
    var url = ''+baseUrl+'/'+functionName+'';
    //alert(url); return false;
    $('#'+tbodyId+'').html('<tr><td colspan="100"><div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div></div></td></tr>');
    $.getJSON(url, {fromDate: fromDate, toDate: toDate, m: m,filterTypeDate:filterTypeDate,filterHeadAccount:filterHeadAccount}, function (result) {
        $.each(result, function (i, field) {
            $('#' + tbodyId + '').html('' + field + '');
        });
    })
}

function yearlyEventCalenderStatusReport(){
    var filterLawnId = $('#filterLawnId').val();
    var filterLawnIdText = $('#filterLawnId option:selected').text();
    var filterYear = $('#filterYear').val();
    var filterYearText = $('#filterYear option:selected').text();
    var filterTypeDate = $('#filterTypeDate').val();
    var filterTypeDateText = $('#filterTypeDate option:selected').text();

    var functionName = $('#functionName').val();
    var tbodyId = $('#tbodyId').val();
    var filterType = $('#filterType').val();

    var m = $('#m').val();
    $('#'+tbodyId+'').html('<tr><td colspan="100"><div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div></div></td></tr>');
    $.ajax({
        url: ''+baseUrl+'/'+functionName+'',
        method:'GET',
        data:{filterLawnId: filterLawnId, filterLawnIdText: filterLawnIdText, m: m,filterYear:filterYear,filterYearText:filterYearText,filterTypeDate:filterTypeDate,filterTypeDateText:filterTypeDateText},
        error: function(){
            alert('error');
        },
        success: function(response){
            setTimeout(function(){
                $('#'+tbodyId+'').html(response);
            },1000);
        }
    });
}

function filterBookingMonthlyStatementSummaryRangeWiseReport(){
	var filterLawnId = $('#filterLawnId').val();
    var filterLawnIdText = $('#filterLawnId option:selected').text();

    var filterTiming = $('#filterTiming').val();
    var filterTimingText = $('#filterTiming option:selected').text();
	
	var filterHeadAccount = $('#filterHeadAccount').val();
    var filterHeadAccountText = $('#filterHeadAccount option:selected').text();

    var filterTypeDate = $('#filterTypeDate').val();
	var fromDate = $('#fromDate').val();
	var toDate = $('#toDate').val();
	var dataFilterServiceType = $('#dataFilterServiceType').val();
	
	var functionName = $('#functionName').val();
    var tbodyId = $('#tbodyId').val();
    var filterType = $('#filterType').val();
	//alert(tbodyId);
    var m = $('#m').val();
    var url = ''+baseUrl+'/'+functionName+'';
    //alert(url); return false;
    $('#'+tbodyId+'').html('<div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div></div>');
    $.getJSON(url, {
			filterLawnId: filterLawnId,
			filterLawnIdText: filterLawnIdText,
			filterHeadAccount:filterHeadAccount,
			filterHeadAccountText:filterHeadAccountText,
			m: m,
			filterTiming:filterTiming,
			filterTimingText:filterTimingText,
			filterTypeDate:filterTypeDate,
			fromDate:fromDate,
			toDate:toDate,
			dataFilterServiceType:dataFilterServiceType
		}, function (result) {
        $.each(result, function (i, field) {
            $('#' + tbodyId + '').html('' + field + '');
        });
    })
}

function filterNewBookingAdvancePartAndFinalPaymentMonthWiseReport(){
	var filterLawnId = $('#filterLawnId').val();
    var filterLawnIdText = $('#filterLawnId option:selected').text();

    var filterTiming = $('#filterTiming').val();
    var filterTimingText = $('#filterTiming option:selected').text();
	
	var filterHeadAccount = $('#filterHeadAccount').val();
    var filterHeadAccountText = $('#filterHeadAccount option:selected').text();

    var filterTypeDate = $('#filterTypeDate').val();
	var fromDate = $('#fromDate').val();
	var toDate = $('#toDate').val();
	var dataFilterServiceType = $('#dataFilterServiceType').val();
	var dataFilterServiceTypeForBooking = $('#dataFilterServiceTypeForBooking').val();

    var functionName = $('#functionName').val();
    var tbodyId = $('#tbodyId').val();
    var filterType = $('#filterType').val();
	var pageReportType = $('#pageReportType').val();
    //alert(tbodyId);
    var m = $('#m').val();
    var url = ''+baseUrl+'/'+functionName+'';
    //alert(url); return false;
    $('#'+tbodyId+'').html('<tr><td colspan="100"><div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div></div></td></tr>');
    $.getJSON(url, {
			filterLawnId: filterLawnId,
			filterLawnIdText: filterLawnIdText,
			filterHeadAccount:filterHeadAccount,
			filterHeadAccountText:filterHeadAccountText,
			m: m,
			filterTiming:filterTiming,
			filterTimingText:filterTimingText,
			filterTypeDate:filterTypeDate,
			pageReportType:pageReportType,
			fromDate:fromDate,
			toDate:toDate,
			dataFilterServiceType:dataFilterServiceType,
			dataFilterServiceTypeForBooking:dataFilterServiceTypeForBooking
		}, function (result) {
        $.each(result, function (i, field) {
            $('#' + tbodyId + '').html('' + field + '');
        });
    })
}

function eventBookedAndExecutedSameMonthWiseReport(){
    var filterLawnId = $('#filterLawnId').val();
    var filterLawnIdText = $('#filterLawnId option:selected').text();

    var filterTiming = $('#filterTiming').val();
    var filterTimingText = $('#filterTiming option:selected').text();

    var filterMonthYear = $('#filterMonthYear').val();

    var functionName = $('#functionName').val();
    var tbodyId = $('#tbodyId').val();
    var filterType = $('#filterType').val();
    //alert(tbodyId);
    var m = $('#m').val();
    var url = ''+baseUrl+'/'+functionName+'';
    //alert(url); return false;
    $('#'+tbodyId+'').html('<tr><td colspan="100"><div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div></div></td></tr>');
    $.getJSON(url, {filterLawnId: filterLawnId, filterLawnIdText: filterLawnIdText, m: m,filterTiming:filterTiming,filterTimingText:filterTimingText,filterMonthYear:filterMonthYear}, function (result) {
        $.each(result, function (i, field) {
            $('#' + tbodyId + '').html('' + field + '');
        });
    })


}

function eventCancelAndPaymentRefundRangeWiseReport(){
	var fromDates = $('#fromDate').val();
    var toDates = $('#toDate').val();
    var filterTypeDate = $('#filterTypeDate').val();
	// Parse the entries
		var date=fromDates;
        var fromDate= date.split("-").reverse().join("-");
        var date1=toDates;
        var toDate= date1.split("-").reverse().join("-");
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
	var filterLawnId = $('#filterLawnId').val();
    var filterLawnIdText = $('#filterLawnId option:selected').text();

    var filterTiming = $('#filterTiming').val();
    var filterTimingText = $('#filterTiming option:selected').text();
	
    var functionName = $('#functionName').val();
    var tbodyId = $('#tbodyId').val();
    var filterType = $('#filterType').val();
    //alert(tbodyId);
    var m = $('#m').val();
    var url = ''+baseUrl+'/'+functionName+'';
    //alert(url); return false;
    $('#'+tbodyId+'').html('<tr><td colspan="100"><div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div></div></td></tr>');
    $.getJSON(url, {fromDate: fromDate, toDate: toDate, m: m,filterTypeDate:filterTypeDate,filterTiming:filterTiming,filterTimingText:filterTimingText,filterLawnId:filterLawnId,filterLawnIdText:filterLawnIdText}, function (result) {
        $.each(result, function (i, field) {
            $('#' + tbodyId + '').html('' + field + '');
        });
    })
}



function viewFilterCashAndBankBookReport(){
	var filterBookTypeId = $('#filterBookType').val();
    var filterBookTypeIdText = $('#filterBookType option:selected').text();
	var filterBankBookHeadId = $('#filterBankBookHead').val();
    var filterBankBookHeadIdText = $('#filterBankBookHead option:selected').text();
	var filterCashBookHeadId = $('#filterCashBookHead').val();
    var filterCashBookHeadIdText = $('#filterCashBookHead option:selected').text();
	var functionName = $('#functionName').val();
    var tbodyId = $('#tbodyId').val();
	var fromDates = $('#fromDate').val();
    var toDates = $('#toDate').val();
	if(filterBookTypeId == '2'){
		if(filterBankBookHeadId === null){
			$('#'+tbodyId+'').html('');
			alert('Something Wrong! Please Bank Book Head');
			return false;
		}
	}else if(filterBookTypeId == '1'){
		if(filterCashBookHeadId === null){
			$('#'+tbodyId+'').html('');
			alert('Something Wrong! Please Cash Book Head');
			return false;
		}
	}
	
	
	
	// Parse the entries
		var date = fromDates;
        var fromDate = date.split("-").reverse().join("-");
        var date1 = toDates;
        var toDate = date1.split("-").reverse().join("-");
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
	var m = $('#m').val();
    $('#'+tbodyId+'').html('<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div>');	
	$.ajax({
        url: ''+baseUrl+'/'+functionName+'',
        method:'GET',
        data:{m: m, filterBookTypeId:filterBookTypeId,filterBookTypeIdText:filterBookTypeIdText,filterBankBookHeadId:filterBankBookHeadId,filterBankBookHeadIdText:filterBankBookHeadIdText,filterCashBookHeadId:filterCashBookHeadId,filterCashBookHeadIdText:filterCashBookHeadIdText,fromDate:fromDate,toDate:toDate},
        error: function(){
            alert('error');
        },
        success: function(response){
            setTimeout(function(){
                $('#'+tbodyId+'').html(response);
            },1000);
        }
    });
}

function viewFilterCashAndBankBookDayWiseReport(){
	var filterBookTypeId = $('#filterBookType').val();
    var filterBookTypeIdText = $('#filterBookType option:selected').text();
	
	var filterBankBookHeadId = $('#filterBankBookHead').val();
    var filterBankBookHeadIdText = $('#filterBankBookHead option:selected').text();
	
	var filterCashBookHeadId = $('#filterCashBookHead').val();
    var filterCashBookHeadIdText = $('#filterCashBookHead option:selected').text();
	
	var functionName = $('#functionName').val();
    var tbodyId = $('#tbodyId').val();
	var fromDates = $('#fromDate').val();
    var toDates = $('#toDate').val();
	// Parse the entries
		var date = fromDates;
        var fromDate = date.split("-").reverse().join("-");
        var date1 = toDates;
        var toDate = date1.split("-").reverse().join("-");
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
	if(filterBookTypeId == '2'){
		if(filterBankBookHeadId === null){
			$('#'+tbodyId+'').html('');
			alert('Something Wrong! Please Bank Book Head');
			return false;
		}
	}else if(filterBookTypeId == '1'){
		if(filterCashBookHeadId === null){
			$('#'+tbodyId+'').html('');
			alert('Something Wrong! Please Cash Book Head');
			return false;
		}
	}
	var m = $('#m').val();
    $('#'+tbodyId+'').html('<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div>');	
	$.ajax({
        url: ''+baseUrl+'/'+functionName+'',
        method:'GET',
        data:{m: m, filterBookTypeId:filterBookTypeId,filterBookTypeIdText:filterBookTypeIdText,filterBankBookHeadId:filterBankBookHeadId,filterBankBookHeadIdText:filterBankBookHeadIdText,filterCashBookHeadId:filterCashBookHeadId,filterCashBookHeadIdText:filterCashBookHeadIdText,fromDate:fromDate,toDate:toDate},
        error: function(){
            alert('error');
        },
        success: function(response){
            setTimeout(function(){
                $('#'+tbodyId+'').html(response);
            },1000);
        }
    });
}

function fullPaymentReceivedOtherMonthYearAndExecutedOtherMonthYearWiseReport(){
    var filterLawnId = $('#filterLawnId').val();
    var filterLawnIdText = $('#filterLawnId option:selected').text();

    var filterTiming = $('#filterTiming').val();
    var filterTimingText = $('#filterTiming option:selected').text();

    var filterFromMonthYear = $('#filterFromMonthYear').val();
    var filterToMonthYear = $('#filterToMonthYear').val();
    var filterFromExecuteMonthYear = $('#filterFromExecuteMonthYear').val();
	var filterToExecuteMonthYear = $('#filterToExecuteMonthYear').val();

    var functionName = $('#functionName').val();
    var tbodyId = $('#tbodyId').val();
    var filterType = $('#filterType').val();
    //alert(tbodyId);
    var m = $('#m').val();
    var url = ''+baseUrl+'/'+functionName+'';
    //alert(url); return false;
    $('#'+tbodyId+'').html('<tr><td colspan="100"><div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div></div></td></tr>');
    $.getJSON(url, {filterLawnId: filterLawnId, filterLawnIdText: filterLawnIdText, m: m,filterTiming:filterTiming,filterTimingText:filterTimingText,filterFromMonthYear:filterFromMonthYear,filterToMonthYear:filterToMonthYear,filterFromExecuteMonthYear:filterFromExecuteMonthYear,filterToExecuteMonthYear:filterToExecuteMonthYear}, function (result) {
        $.each(result, function (i, field) {
            $('#' + tbodyId + '').html('' + field + '');
        });
    })
}

function eventBookedOtherMonthYearAndExecutedOtherMonthYearWiseReport(){
    var filterLawnId = $('#filterLawnId').val();
    var filterLawnIdText = $('#filterLawnId option:selected').text();

    var filterTiming = $('#filterTiming').val();
    var filterTimingText = $('#filterTiming option:selected').text();

    var filterFromMonthYear = $('#filterFromMonthYear').val();
    var filterToMonthYear = $('#filterToMonthYear').val();
    var filterExecuteMonthYear = $('#filterExecuteMonthYear').val();

    var functionName = $('#functionName').val();
    var tbodyId = $('#tbodyId').val();
    var filterType = $('#filterType').val();
    //alert(tbodyId);
    var m = $('#m').val();
    var url = ''+baseUrl+'/'+functionName+'';
    //alert(url); return false;
    $('#'+tbodyId+'').html('<tr><td colspan="100"><div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div></div></td></tr>');
    $.getJSON(url, {filterLawnId: filterLawnId, filterLawnIdText: filterLawnIdText, m: m,filterTiming:filterTiming,filterTimingText:filterTimingText,filterFromMonthYear:filterFromMonthYear,filterToMonthYear:filterToMonthYear,filterExecuteMonthYear:filterExecuteMonthYear}, function (result) {
        $.each(result, function (i, field) {
            $('#' + tbodyId + '').html('' + field + '');
        });
    })


}

function viewRangeWiseDataFilter() {
    var fromDates = $('#fromDate').val();
    var toDates = $('#toDate').val();
    var filterType = $('#filterType').val();
	// Parse the entries
		var date=fromDates;
        var fromDate= date.split("-").reverse().join("-");
        var date1=toDates;
        var toDate= date1.split("-").reverse().join("-");
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
	if(filterType == 'fVRABReport'){
		filterViewRefundAgainstBookingReport();
	}else if(filterType == 'fVCABReport'){
		filterViewCollectionAgainstBookingReport();
	}else if(filterType == 'fEBRWReport'){
		viewEventBookingRangewiseReport();
	}else if(filterType == 'fVAPALPEReport'){
        filterViewAverageProfitAndLossPerEventReport();
    }else if(filterType == 'fVBSAPALReport'){
        filterViewBookingServiceAgainstProfitAndLossMonthWiseReport();
    }else if(filterType == 'NBAPAFPMWReport'){
        filterNewBookingAdvancePartAndFinalPaymentMonthWiseReport();
    }else if(filterType == 'fBMSSRWReport'){
		filterBookingMonthlyStatementSummaryRangeWiseReport();
	}
	
	
}

function filterViewRefundAgainstBookingReport(){
	 
    var filterLawnId = $('#filterLawnId').val();
    var filterLawnIdText = $('#filterLawnId option:selected').text();
    var filterTypeDate = $('#filterTypeDate').val();
    var functionName = $('#functionName').val();
    var tbodyId = $('#tbodyId').val();
    var filterType = $('#filterType').val();
    var m = $('#m').val();
    var url = ''+baseUrl+'/'+functionName+'';
	var fromDates = $('#fromDate').val();
    var toDates = $('#toDate').val();
	var eventFilterTypeDate = $('#eventFilterTypeDate').val();
    // Parse the entries
	var date=fromDates;
	var fromDate= date.split("-").reverse().join("-");
	var date1=toDates;
	var toDate= date1.split("-").reverse().join("-");
	var startDate = Date.parse(fromDate);
	var endDate = Date.parse(toDate);
    $('#' + tbodyId + '').html('<tr><td colspan="1000000000"><div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div></div></td><tr>');
    $.ajax({
        url:url,
        type:'GET',
        data:{filterLawnId: filterLawnId, filterLawnIdText: filterLawnIdText, m: m,filterTypeDate:filterTypeDate,fromDate:fromDate,toDate:toDate,eventFilterTypeDate:eventFilterTypeDate},
        success:function(res){
            $('#' + tbodyId + '').html(res);
        }
    });
}

function filterViewBookingServiceAgainstProfitAndLossMonthWiseReport(){
	var filterLawnId = $('#filterLawnId').val();
    var filterLawnIdText = $('#filterLawnId option:selected').text();
	var filterServiceType = $('#filterServiceType').val();
    var functionName = $('#functionName').val();
    var tbodyId = $('#tbodyId').val();
    var filterType = $('#filterType').val();
    var m = $('#m').val();
    var url = ''+baseUrl+'/'+functionName+'';
	var fromDates = $('#fromDate').val();
    var toDates = $('#toDate').val();
	// Parse the entries
	var date = fromDates;
	var fromDate = date.split("-").reverse().join("-");
	var date1 = toDates;
	var toDate= date1.split("-").reverse().join("-");
	var startDate = Date.parse(fromDate);
	var endDate = Date.parse(toDate);
    $('#' + tbodyId + '').html('<tr><td colspan="1000000000"><div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div></div></td><tr>');
    $.ajax({
        url:url,
        type:'GET',
        data:{filterLawnId: filterLawnId, filterLawnIdText: filterLawnIdText, m: m,fromDate:fromDate,toDate:toDate,filterServiceType:filterServiceType},
        success:function(res){
            $('#' + tbodyId + '').html(res);
        }
    });
}

function filterViewCollectionAgainstBookingReport(){
	var functionName = $('#functionName').val();
    var filterLawnId = $('#filterLawnId').val();
    var filterLawnIdText = $('#filterLawnId option:selected').text();
    var tbodyId = $('#tbodyId').val();
    var m = $('#m').val();
    var url = ''+baseUrl+'/'+functionName+'';
	var fromDates = $('#fromDate').val();
    var toDates = $('#toDate').val();
	// Parse the entries
	var date=fromDates;
	var fromDate= date.split("-").reverse().join("-");
	var date1=toDates;
	var toDate= date1.split("-").reverse().join("-");
	var startDate = Date.parse(fromDate);
	var endDate = Date.parse(toDate);
    $('#' + tbodyId + '').html('<tr><td colspan="1000000000"><div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div></div></td><tr>');
    $.ajax({
        url:url,
        type:'GET',
        data:{filterLawnId: filterLawnId, filterLawnIdText: filterLawnIdText, m: m,fromDate:fromDate,toDate:toDate},
        success:function(res){
            $('#' + tbodyId + '').html(res);
        }
    });
}

function viewEventBookingRangewiseReport(){
	 
    var filterLawnId = $('#filterLawnId').val();
    var filterLawnIdText = $('#filterLawnId option:selected').text();
    var filterTypeDate = $('#filterTypeDate').val();
    var functionName = $('#functionName').val();
    var tbodyId = $('#tbodyId').val();
    var filterType = $('#filterType').val();
    var m = $('#m').val();
    var url = ''+baseUrl+'/'+functionName+'';
	var fromDates = $('#fromDate').val();
    var toDates = $('#toDate').val();
	var eventFilterTypeDate = $('#eventFilterTypeDate').val();
    // Parse the entries
	var date = fromDates;
	var fromDate = date.split("-").reverse().join("-");
	var date1 = toDates;
	var toDate = date1.split("-").reverse().join("-");
	var startDate = Date.parse(fromDate);
	var endDate = Date.parse(toDate);
    $('#' + tbodyId + '').html('<tr><td colspan="1000000000"><div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div></div></td><tr>');
    $.ajax({
        url:url,
        type:'GET',
        data:{filterLawnId: filterLawnId, filterLawnIdText: filterLawnIdText, m: m,filterTypeDate:filterTypeDate,fromDate:fromDate,toDate:toDate,eventFilterTypeDate:eventFilterTypeDate},
        success:function(res){
            $('#' + tbodyId + '').html(res);
        }
    });
    
}





function viewFilterDateWiseStockInventoryReport() {
    var paramOne = $('#paramOne').val();
    if(paramOne != '') {
        var res = paramOne.split("^");
        var categoryId = res[0];
        var subItemId = res[1];
    }else{
        var categoryId = 0;
        var subItemId = 0;
    }
    var paramTwo = $('#paramTwo').val();
    var functionName = $('#functionName').val();
    var tbodyId = $('#tbodyId').val();
    //alert(tbodyId);
    var m = $('#m').val();
    var url = ''+baseUrl+'/'+functionName+'';
    $('#'+tbodyId+'').html('<div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div></div>');
    $.getJSON(url, { categoryId:categoryId,subItemId:subItemId,paramTwo:paramTwo,m:m} ,function(result){
        $.each(result, function(i, field){
            $('#'+tbodyId+'').html(''+field+'');
        });
    })
}



//Start Common Function For Reports
function dataFilterDateWise(dateWiseFunction) {

    var fromDate = $('#startDate').val();
    var toDate = $('#endDate').val();
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
    //if (difference <= 1) {
    //alert("The range must be at least seven days apart.");
    //return false;
    //}
    //return true;
    this[dateWiseFunction]();

}

function dataFilterMonthWise(monthWiseFunction) {
    this[monthWiseFunction]();
}

function dataFilterYearWise(yearWiseFunction) {
    this[yearWiseFunction]();
}
//End Common Function For Reports

function dateWiseFilterViewInventoryPerformanceDetail() {
    alert('Date Wise')
}

function monthWiseFilterViewInventoryPerformanceDetail() {
    alert('Month Wise')
}

function yearWiseFilterViewInventoryPerformanceDetail() {
    alert('Year Wise')
}

//Start viewInventoryPerformanceDetailReports
$("#selectSubItemTwo").change(function() {
    var selectSubItemTwo = $('#selectSubItem option[value="' + $('#selectSubItemTwo').val() + '"]').data('id');
    $('#selectSubItemId').val(selectSubItemTwo);
}).change();

function bookingComparisanReport(){
	var filterComparisanType = $('#filterComparisanType').val();
	var filterLawnId = $('#filterLawnId').val();
	var filterLawnIdText = $('#filterLawnId option:selected').text();
	var yearArray=[]; 
	$('select[name="filterYear[]"] option:selected').each(function() {
		yearArray.push($(this).val());
	});
	if(yearArray == ''){
		alert('Something Wrong! Please Select Year.');
		return false;
	}
	
	var monthArray=[]; 
	$('select[name="filterMonth[]"] option:selected').each(function() {
		monthArray.push($(this).val());
	});
	if(monthArray == ''){
		alert('Something Wrong! Please Select Month.');
		return false;
	}
	var functionName = $('#functionName').val();
    var tbodyId = $('#tbodyId').val();
    var m = $('#m').val();
    $('#'+tbodyId+'').html('<div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div></div>');
    $.ajax({
        url: ''+baseUrl+'/'+functionName+'',
        method:'GET',
        data:{
			filterComparisanType: filterComparisanType, 
			filterLawnId: filterLawnId,
			m: m,
			filterLawnIdText:filterLawnIdText,
			yearArray:yearArray,
			monthArray:monthArray
		},
        error: function(){
            alert('error');
        },
        success: function(response){
            setTimeout(function(){
                $('#'+tbodyId+'').html(response);
            },1000);
        }
    });
}

function bookingCollectionComparisanReport(){
	var filterComparisanType = $('#filterComparisanType').val();
	var filterLawnId = $('#filterLawnId').val();
	var filterLawnIdText = $('#filterLawnId option:selected').text();
	var yearArray=[]; 
	$('select[name="filterYear[]"] option:selected').each(function() {
		yearArray.push($(this).val());
	});
	if(yearArray == ''){
		alert('Something Wrong! Please Select Year.');
		return false;
	}
	
	var monthArray=[]; 
	$('select[name="filterMonth[]"] option:selected').each(function() {
		monthArray.push($(this).val());
	});
	if(monthArray == ''){
		alert('Something Wrong! Please Select Month.');
		return false;
	}
	var functionName = $('#functionName').val();
    var tbodyId = $('#tbodyId').val();
    var m = $('#m').val();
    $('#'+tbodyId+'').html('<div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div></div>');
    $.ajax({
        url: ''+baseUrl+'/'+functionName+'',
        method:'GET',
        data:{
			filterComparisanType: filterComparisanType, 
			filterLawnId: filterLawnId,
			m: m,
			filterLawnIdText:filterLawnIdText,
			yearArray:yearArray,
			monthArray:monthArray
		},
        error: function(){
            alert('error');
        },
        success: function(response){
            setTimeout(function(){
                $('#'+tbodyId+'').html(response);
            },1000);
        }
    });
}

function bookingRefundComparisanReport(){
	var filterComparisanType = $('#filterComparisanType').val();
	var filterLawnId = $('#filterLawnId').val();
	var filterLawnIdText = $('#filterLawnId option:selected').text();
	var yearArray=[]; 
	$('select[name="filterYear[]"] option:selected').each(function() {
		yearArray.push($(this).val());
	});
	if(yearArray == ''){
		alert('Something Wrong! Please Select Year.');
		return false;
	}
	
	var monthArray=[]; 
	$('select[name="filterMonth[]"] option:selected').each(function() {
		monthArray.push($(this).val());
	});
	if(monthArray == ''){
		alert('Something Wrong! Please Select Month.');
		return false;
	}
	var functionName = $('#functionName').val();
    var tbodyId = $('#tbodyId').val();
    var m = $('#m').val();
    $('#'+tbodyId+'').html('<div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div></div>');
    $.ajax({
        url: ''+baseUrl+'/'+functionName+'',
        method:'GET',
        data:{
			filterComparisanType: filterComparisanType, 
			filterLawnId: filterLawnId,
			m: m,
			filterLawnIdText:filterLawnIdText,
			yearArray:yearArray,
			monthArray:monthArray
		},
        error: function(){
            alert('error');
        },
        success: function(response){
            setTimeout(function(){
                $('#'+tbodyId+'').html(response);
            },1000);
        }
    });
}

function expensePaymentComparisanReport(){
	var filterComparisanType = $('#filterComparisanType').val();
	var filterAccId = $('#filterAccId').val();
	var filterAccIdText = $('#filterAccId option:selected').text();
	var yearArray=[]; 
	$('select[name="filterYear[]"] option:selected').each(function() {
		yearArray.push($(this).val());
	});
	if(yearArray == ''){
		alert('Something Wrong! Please Select Year.');
		return false;
	}
	
	var monthArray=[]; 
	$('select[name="filterMonth[]"] option:selected').each(function() {
		monthArray.push($(this).val());
	});
	if(monthArray == ''){
		alert('Something Wrong! Please Select Month.');
		return false;
	}
	var functionName = $('#functionName').val();
    var tbodyId = $('#tbodyId').val();
    var m = $('#m').val();
    $('#'+tbodyId+'').html('<div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div></div>');
    $.ajax({
        url: ''+baseUrl+'/'+functionName+'',
        method:'GET',
        data:{
			filterComparisanType: filterComparisanType, 
			filterAccId: filterAccId,
			m: m,
			filterAccIdText:filterAccIdText,
			yearArray:yearArray,
			monthArray:monthArray
		},
        error: function(){
            alert('error');
        },
        success: function(response){
            setTimeout(function(){
                $('#'+tbodyId+'').html(response);
            },1000);
        }
    });
}

function formatNumber(num) {
  return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
}

function serviceWiseCollectionComparisanReport(){
    var filterComparisanType = $('#filterComparisanType').val();
    var filterServiceName = $('#filterServiceName').val();
    var filterServiceNameText = $('#filterServiceName option:selected').text();
    var yearArray=[];
    $('select[name="filterYear[]"] option:selected').each(function() {
        yearArray.push($(this).val());
    });
    if(yearArray == ''){
        alert('Something Wrong! Please Select Year.');
        return false;
    }

    var monthArray=[];
    $('select[name="filterMonth[]"] option:selected').each(function() {
        monthArray.push($(this).val());
    });
    if(monthArray == ''){
        alert('Something Wrong! Please Select Month.');
        return false;
    }
    var functionName = $('#functionName').val();
    var tbodyId = $('#tbodyId').val();
    var m = $('#m').val();
    $('#'+tbodyId+'').html('<div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div></div>');
    $.ajax({
        url: ''+baseUrl+'/'+functionName+'',
        method:'GET',
        data:{
            filterComparisanType: filterComparisanType,
            filterServiceName: filterServiceName,
            m: m,
            filterServiceNameText:filterServiceNameText,
            yearArray:yearArray,
            monthArray:monthArray
        },
        error: function(){
            alert('error');
        },
        success: function(response){
            setTimeout(function(){
                $('#'+tbodyId+'').html(response);
            },1000);
        }
    });
}

function serviceWiseRevenueComparisanReport(){
    var filterComparisanType = $('#filterComparisanType').val();
    var filterServiceName = $('#filterServiceName').val();
    var filterServiceNameText = $('#filterServiceName option:selected').text();
    var yearArray=[];
    $('select[name="filterYear[]"] option:selected').each(function() {
        yearArray.push($(this).val());
    });
    if(yearArray == ''){
        alert('Something Wrong! Please Select Year.');
        return false;
    }

    var monthArray=[];
    $('select[name="filterMonth[]"] option:selected').each(function() {
        monthArray.push($(this).val());
    });
    if(monthArray == ''){
        alert('Something Wrong! Please Select Month.');
        return false;
    }
    var functionName = $('#functionName').val();
    var tbodyId = $('#tbodyId').val();
    var m = $('#m').val();
    $('#'+tbodyId+'').html('<div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div></div>');
    $.ajax({
        url: ''+baseUrl+'/'+functionName+'',
        method:'GET',
        data:{
            filterComparisanType: filterComparisanType,
            filterServiceName: filterServiceName,
            m: m,
            filterServiceNameText:filterServiceNameText,
            yearArray:yearArray,
            monthArray:monthArray
        },
        error: function(){
            alert('error');
        },
        success: function(response){
            setTimeout(function(){
                $('#'+tbodyId+'').html(response);
            },1000);
        }
    });
}

function filterViewAverageProfitAndLossPerEventReport(){
    var functionName = $('#functionName').val();
    var tbodyId = $('#tbodyId').val();
    var m = $('#m').val();
    var url = ''+baseUrl+'/'+functionName+'';
    var fromDates = $('#fromDate').val();
    var toDates = $('#toDate').val();
    // Parse the entries
    var date=fromDates;
    var fromDate= date.split("-").reverse().join("-");
    var date1=toDates;
    var toDate= date1.split("-").reverse().join("-");
    var startDate = Date.parse(fromDate);
    var endDate = Date.parse(toDate);
    $('#' + tbodyId + '').html('<tr><td colspan="1000000000"><div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div></div></td><tr>');
    $.ajax({
        url:url,
        type:'GET',
        data:{m: m,fromDate:fromDate,toDate:toDate},
        success:function(res){
            $('#' + tbodyId + '').html(res);
        }
    });
}

function valuationReport() {
    var filterComparisanType = $('#filterComparisanType').val();
    var yearArray = [];
    $('select[name="filterYear[]"] option:selected').each(function () {
        yearArray.push($(this).val());
    });
    if (yearArray == '') {
        alert('Something Wrong! Please Select Year.');
        return false;
    }

    var monthArray = [];
    $('select[name="filterMonth[]"] option:selected').each(function () {
        monthArray.push($(this).val());
    });
    if (monthArray == '') {
        alert('Something Wrong! Please Select Month.');
        return false;
    }
    var functionName = $('#functionName').val();
    var tbodyId = $('#tbodyId').val();
    var m = $('#m').val();
    $('#' + tbodyId + '').html('<div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div></div>');
    $.ajax({
        url: '' + baseUrl + '/' + functionName + '',
        method: 'GET',
        data: {
            filterComparisanType: filterComparisanType,
            m: m,
            yearArray: yearArray,
            monthArray: monthArray
        },
        error: function () {
            alert('error');
        },
        success: function (response) {
            setTimeout(function () {
                $('#' + tbodyId + '').html(response);
            }, 1000);
        }
    });
}

function bookingCChartsReport(){
	var filterComparisanType = $('#filterComparisanType').val();
	var filterLawnId = $('#filterLawnId').val();
	var filterLawnIdText = $('#filterLawnId option:selected').text();
	var yearArray=[]; 
	$('select[name="filterYear[]"] option:selected').each(function() {
		yearArray.push($(this).val());
	});
	if(yearArray == ''){
		alert('Something Wrong! Please Select Year.');
		return false;
	}
	
	var monthArray=[]; 
	$('select[name="filterMonth[]"] option:selected').each(function() {
		monthArray.push($(this).val());
	});
	if(monthArray == ''){
		alert('Something Wrong! Please Select Month.');
		return false;
	}
	var functionName = $('#functionName').val();
    var tbodyId = $('#tbodyId').val();
    var m = $('#m').val();
    $('#'+tbodyId+'').html('<div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div></div>');
    $.ajax({
        url: ''+baseUrl+'/'+functionName+'',
        method:'GET',
        data:{
			filterComparisanType: filterComparisanType, 
			filterLawnId: filterLawnId,
			m: m,
			filterLawnIdText:filterLawnIdText,
			yearArray:yearArray,
			monthArray:monthArray
		},
        error: function(){
            alert('error');
        },
        success: function(response){
            setTimeout(function(){
                $('#'+tbodyId+'').html(response);
            },1000);
        }
    });
}


function bookingChartsReport(){
	var filterComparisanType = $('#filterComparisanType').val();
	var filterLawnId = $('#filterLawnId').val();
	var filterLawnIdText = $('#filterLawnId option:selected').text();
	var yearArray=[]; 
	$('select[name="filterYear[]"] option:selected').each(function() {
		yearArray.push($(this).val());
	});
	if(yearArray == ''){
		alert('Something Wrong! Please Select Year.');
		return false;
	}
	
	var monthArray=[]; 
	$('select[name="filterMonth[]"] option:selected').each(function() {
		monthArray.push($(this).val());
	});
	if(monthArray == ''){
		alert('Something Wrong! Please Select Month.');
		return false;
	}
	var functionName = $('#functionName').val();
    var tbodyId = $('#tbodyId').val();
    var m = $('#m').val();
    $('#'+tbodyId+'').html('<div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div></div>');
    $.ajax({
        url: ''+baseUrl+'/'+functionName+'',
        method:'GET',
        data:{
			filterComparisanType: filterComparisanType, 
			filterLawnId: filterLawnId,
			m: m,
			filterLawnIdText:filterLawnIdText,
			yearArray:yearArray,
			monthArray:monthArray
		},
        error: function(){
            alert('error');
        },
        success: function(response){
            setTimeout(function(){
                $('#'+tbodyId+'').html(response);
            },1000);
        }
    });
}

//End viewInventoryPerformanceDetailReports



