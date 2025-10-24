window.onload=function () {
    var pageType = $('#pageType').val();
    if(pageType == 0){
        filterVoucherList();
    }else if(pageType == 6){
        filterBookingCalender();
    }else if(pageType == 7){
        filterBookingCalenderMonthWise();
    }else if(pageType == 2){
        viewDataFilterTwoParameter();
    }
}
var baseUrl = $('#baseUrl').val();
function filterBookingCalender(){
    var getYearName = $('#getYearName').val();

    var getLawnName = $('#getLawnName').val();
    var getLawnNameText = $('#getLawnName option:selected').text();

    var getTimingName = $('#getTimingName').val();
    var getTimingNameText = $('#getTimingName option:selected').text();

    var getEventName = $('#getEventName').val();
    var getEventNameText = $('#getEventName option:selected').text();

    var functionName = $('#functionName').val();
    var tbodyId = $('#tbodyId').val();
    var filterType = $('#filterType').val();
	
    //alert(tbodyId);
    var m = $('#m').val();
    var url = ''+baseUrl+'/'+functionName+'';
    //alert(url); return false;
    $('#'+tbodyId+'').html('<div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div></div>');
    $.ajax({
        url: '' + baseUrl + '/' + functionName + '',
        method: 'GET',
        data: {
            getYearName: getYearName,
            getLawnName: getLawnName,
            m: m,
            getTimingName: getTimingName,
            getEventName: getEventName,
            getLawnNameText: getLawnNameText,
            getTimingNameText: getTimingNameText,
            getEventNameText: getEventNameText
        },
        success: function (response) {
            $('#' + tbodyId + '').html(response);
        }
    });
}




function viewRangeWiseDataFilter() {
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
    filterVoucherList();
}


function filterVoucherList(){
	
    var fromDates = $('#fromDate').val();
    var toDates = $('#toDate').val();
	var date=fromDates;
	var fromDate = date.split("-").reverse().join("-");
	var date1=toDates;
	var toDate= date1.split("-").reverse().join("-");
	var startDate = Date.parse(fromDate);
	var endDate = Date.parse(toDate);
	
    var functionName = $('#functionName').val();
    var tbodyId = $('#tbodyId').val();
    var filterType = $('#filterType').val();
	
	var lawn=$('#lawn').val();
	var timming=$('#timming').val();
	var pageType=$('#pageType').val();
	var parentCode=$('#parentCode').val();
    //alert(tbodyId);
    var m = $('#m').val(); 
    var url = ''+baseUrl+'/'+functionName+'';
    //alert(url); return false;
    $('#'+tbodyId+'').html('<tr><td colspan="1000000000"><div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div></div></td><tr>');

   if(filterType == 'bookingList'){
		var filterTypeDate = $('#filterTypeDate').val();
		var getLawnName = $('#getLawnName').val();
		var getLawnNameText = $('#getLawnName option:selected').text();
		var getTimingName = $('#getTimingName').val();
		var getTimingNameText = $('#getTimingName option:selected').text();
        $.getJSON(url, {fromDate: fromDate, toDate: toDate, m: m,getTimingName:getTimingName,getTimingNameText:getTimingNameText,filterTypeDate:filterTypeDate,pageType:pageType,parentCode:parentCode,getLawnName:getLawnName,getLawnNameText:getLawnNameText}, function (result) {
            $.each(result, function (i, field) {
                $('#' + tbodyId + '').html('' + field + '');
            });
        })
    }else if(filterType == 'allBookingTentativeList'){
		var filterTypeDate = $('#filterTypeDate').val();
		var filterTypeBooking = $('#filterTypeBooking').val();
        var selectVoucherStatus = $('#selectVoucherStatus').val();
		var getLawnName = $('#getLawnName').val();
		var getLawnNameText = $('#getLawnName option:selected').text();
        $.getJSON(url, {fromDate: fromDate, toDate: toDate,filterTypeBooking:filterTypeBooking, m: m,selectVoucherStatus:selectVoucherStatus,filterTypeDate:filterTypeDate,pageType:pageType,parentCode:parentCode,getLawnName:getLawnName,getLawnNameText:getLawnNameText}, function (result) {
            $.each(result, function (i, field) {
                $('#' + tbodyId + '').html('' + field + '');
            });
        })
    }else if(filterType == 'filterActivityBookingLogsList'){
		$('#'+tbodyId+'').html('<tr><td colspan="1000000000"><div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div></div></td><tr>');
		var filterTypeDate = $('#filterTypeDate').val();
        var selectVoucherStatus = $('#selectVoucherStatus').val();
		var activity = $('#activity').val();
		var getActivity = $('#activity option:selected').text();
		var getUserName = $('#selectUser').val();
		var getUserNameText = $('#selectUser option:selected').text();
        $.getJSON(url, {fromDate: fromDate,getUserName:getUserName, getUserNameText:getUserNameText,toDate: toDate, m: m,selectVoucherStatus:selectVoucherStatus,filterTypeDate:filterTypeDate,pageType:pageType,parentCode:parentCode,activity:activity,getActivity:getActivity,getLawnNameText:getLawnNameText}, function (result) {
            $.each(result, function (i, field) { 
                $('#' + tbodyId + '').html('' + field + '');  
            });
        })
    }else if(filterType == 'filterActivityBillingLogsList'){
		$('#'+tbodyId+'').html('<tr><td colspan="1000000000"><div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div></div></td><tr>');
		var filterTypeDate = $('#filterTypeDate').val();
        var selectVoucherStatus = $('#selectVoucherStatus').val();
		var activity = $('#activity').val();
		var getActivity = $('#activity option:selected').text();
		var getUserName = $('#selectUser').val();
		var getUserNameText = $('#selectUser option:selected').text();
        $.getJSON(url, {fromDate: fromDate,getUserName:getUserName, getUserNameText:getUserNameText,toDate: toDate, m: m,selectVoucherStatus:selectVoucherStatus,filterTypeDate:filterTypeDate,pageType:pageType,parentCode:parentCode,activity:activity,getActivity:getActivity,getLawnNameText:getLawnNameText}, function (result) {
            $.each(result, function (i, field) { 
                $('#' + tbodyId + '').html('' + field + '');  
            });
        })
    }else if(filterType == 'CustomerNameContactList'){
		
		var filterTypeDate = $('#filterTypeDate').val();
        var selectVoucherStatus = $('#selectVoucherStatus').val();
		var getLawnName = $('#getLawnName').val();
		var getLawnNameText = $('#getLawnName option:selected').text();
		var getRangeOrAll = $('#filterTypeRangeOrAll').val(); 
        $.getJSON(url, {fromDate: fromDate, toDate: toDate,getRangeOrAll:getRangeOrAll, m: m,selectVoucherStatus:selectVoucherStatus,filterTypeDate:filterTypeDate,pageType:pageType,parentCode:parentCode,getLawnName:getLawnName,getLawnNameText:getLawnNameText}, function (result) {
            $.each(result, function (i, field) {
                $('#' + tbodyId + '').html('' + field + '');
            });
        })
    }else if(filterType == 'userWiseBookingList'){
		$('#'+tbodyId+'').html('<div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div></div>');
		var filterTypeDate = $('#filterTypeDate').val();
        var selectVoucherStatus = $('#selectVoucherStatus').val();
		var getLawnName = $('#getLawnName').val();
		var getLawnNameText = $('#getLawnName option:selected').text();
		var getUserName = $('#selectUser').val();
		var getUserNameText = $('#selectUser option:selected').text();
		$.getJSON(url, {fromDate: fromDate, toDate: toDate,getUserName:getUserName,getUserNameText:getUserNameText, m: m,selectVoucherStatus:selectVoucherStatus,filterTypeDate:filterTypeDate,pageType:pageType,parentCode:parentCode,getLawnName:getLawnName,getLawnNameText:getLawnNameText}, function (result) {
            $.each(result, function (i, field) {
                $('#' + tbodyId + '').html('' + field + '');
            });
        })
    }else if(filterType == 'shiftBookingList'){
		var filterTypeDate = $('#filterTypeDate').val();
        var selectVoucherStatus = $('#selectVoucherStatus').val();
		var getLawnName = $('#getLawnName').val();
		var getLawnNameText = $('#getLawnName option:selected').text();
        $.getJSON(url, {fromDate: fromDate, toDate: toDate, m: m,selectVoucherStatus:selectVoucherStatus,filterTypeDate:filterTypeDate,pageType:pageType,parentCode:parentCode,getLawnName:getLawnName,getLawnNameText:getLawnNameText}, function (result) {
            $.each(result, function (i, field) {
                $('#' + tbodyId + '').html('' + field + '');
            });
        })
    }else if(filterType == 'cancelBookingList'){
		var filterTypeDate = $('#filterTypeDate').val();
		var getLawnName = $('#getLawnName').val();
		var getLawnNameText = $('#getLawnName option:selected').text();
		var getTimingName = $('#getTimingName').val();
		var getTimingNameText = $('#getTimingName option:selected').text();
        $.getJSON(url, {fromDate: fromDate, toDate: toDate, m: m,getTimingNameText:getTimingNameText,getTimingName:getTimingName,filterTypeDate:filterTypeDate,getLawnName:getLawnName,getLawnNameText:getLawnNameText,pageType:pageType,parentCode:parentCode}, function (result) {
            $.each(result, function (i, field) {
                $('#' + tbodyId + '').html('' + field + '');
            });
        })
    }else if(filterType == 'tentativeBookingList'){
		var filterTypeDate = $('#filterTypeDate').val();
		$.ajax({
			type:'GET',
			url:url,
			data:{fromDate: fromDate, toDate: toDate, m: m,filterTypeDate:filterTypeDate,timming:timming,lawn:lawn},
			success:function(res){
				$('#' + tbodyId + '').html('' + res + '');
			}	
		});
        }
     else if(filterType == 'walkingCustomerList'){
        var filterTypeDate = $('#filterTypeDate').val();
			$.ajax({
				type:'GET',
				url:url,
				data:{fromDate: fromDate, toDate: toDate, m: m,filterTypeDate:filterTypeDate,timming:timming,lawn:lawn},
				success:function(res){
					$('#' + tbodyId + '').html('' + res + '');
				}	
			});
    }
	else if(filterType == 'bookingListLog'){
		
		var filterTypeLogs = $('#filterTypeLogs').val();
		var filterTypeUser = $('#filterTypeUser').val();
       // alert(filterTypeLogs);
		// alert(filterTypeUser);
        $.getJSON(url, {fromDate: fromDate, toDate: toDate, m: m,filterTypeUser:filterTypeUser,filterTypeLogs:filterTypeLogs,pageType:pageType,parentCode:parentCode}, function (result) {
            $.each(result, function (i, field) {
                $('#' + tbodyId + '').html('' + field + '');
            });
        })
    }else if(filterType == 'filterViewSMSSummaryReportList'){
		$('#'+tbodyId+'').html('<tr><td colspan="1000000000"><div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div></div></td><tr>');
		var filter_type = $('#filter_type').val();
		var getFilter_type = $('#filter_type option:selected').text();
		
		var filterMobileNo = $('#filterMobileNo').val();
		
		
		
		$.getJSON(url, {fromDate: fromDate,toDate: toDate, m: m,pageType:pageType,parentCode:parentCode,filter_type:filter_type,getFilter_type:getFilter_type,filterMobileNo:filterMobileNo}, function (result) {
            $.each(result, function (i, field) { 
                $('#' + tbodyId + '').html('' + field + '');  
            });
        })
	}else if(filterType == 'filterViewEmailSummaryReportList'){
		$('#'+tbodyId+'').html('<tr><td colspan="1000000000"><div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div></div></td><tr>');
		var filter_type = $('#filter_type').val();
		var getFilter_type = $('#filter_type option:selected').text();
		
		var filterEmail = $('#filterEmail').val();
		
		
		$.getJSON(url, {fromDate: fromDate,toDate: toDate, m: m,pageType:pageType,parentCode:parentCode,filter_type:filter_type,getFilter_type:getFilter_type,filterEmail:filterEmail}, function (result) {
            $.each(result, function (i, field) { 
                $('#' + tbodyId + '').html('' + field + '');  
            });
        })
	}
}

function deleteBookingFiveTableRecords(m,columnOne,columnTwo,columnThree,columnValue,jv_no,tableOne,tableTwo,tableThree,tableFour,tableFive,tableSix){
    $.ajax({
        url: ''+baseUrl+'/bd/deleteBookingRecord',
        type: "GET",
        data: {m:m,columnOne:columnOne,columnTwo:columnTwo,columnThree:columnThree,columnValue:columnValue,jv_no:jv_no,tableOne:tableOne,tableTwo:tableTwo,tableThree:tableThree,tableFour:tableFour,tableFive:tableFive,tableSix:tableSix},
        success:function(data) {
            filterVoucherList();
        }
    });
}

function repostBookingFiveTableRecords(m,columnOne,columnTwo,columnThree,columnValue,jv_no,tableOne,tableTwo,tableThree,tableFour,tableFive,tableSix){
    $.ajax({
        url: ''+baseUrl+'/bd/repostBookingRecord',
        type: "GET",
        data: {m:m,columnOne:columnOne,columnTwo:columnTwo,columnThree:columnThree,columnValue:columnValue,jv_no:jv_no,tableOne:tableOne,tableTwo:tableTwo,tableThree:tableThree,tableFour:tableFour,tableFive:tableFive,tableSix:tableSix},
        success:function(data) {
            filterVoucherList();
        }
    });
}

function showAndHideFilterOptionRangeWise(){
	var getBookingTypeOption = $('#getBookingTypeOption').val();
	if(getBookingTypeOption == '1'){
		$('#monthWiseSection').removeClass( "hidden" );
		$('#rangeWiseSection').addClass( "hidden" );
	}else if(getBookingTypeOption == '2'){
		$('#monthWiseSection').addClass( "hidden" );
		$('#rangeWiseSection').removeClass( "hidden" );
	}
}