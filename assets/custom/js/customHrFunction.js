window.onload=function () {
    var pageType = $('#pageType').val();
    if(pageType == 0){
        filterVoucherList();
    }else if(pageType == 1){
        viewDataFilterOneParameter();
    }else if(pageType == 2){
        viewDataFilterTwoParameter();
    }else if(pageType == 'fCUSAAEL'){
        viewFilterCreateUpdateSalaryAndAllowancesEmployeeList();
    }else if(pageType == 'fEAMWRDASDW'){
        filterEmployeeAttendanceMonthWiseReportDepartmentAndSubDepartmentWise();
    }
}


var baseUrl = $('#baseUrl').val();

function filterEmployeeAttendanceMonthWiseReportDepartmentAndSubDepartmentWise(){
    var functionName = $('#functionName').val();
    var tbodyId = $('#tbodyId').val();
    var m = $('#m').val();
    var url = ''+baseUrl+'/'+functionName+'';
    var pageType1 = $('#pageType1').val();
    var parentCode = $('#parentCode').val();
    var selectMainDepartmentId = $('#main_department_id').val();
    var selectSubDepartmentId = $('#sub_department_id').val();
    var monthYear = $('#monthYear').val();
    if(monthYear == ''){
        alert('Please Select Month Year!');
    }else if(selectMainDepartmentId == ''){
        alert('Please Select Main Department');
    }else{
        $('#'+tbodyId+'').html('<div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div></div>');
        $.ajax({
            url: '' + baseUrl + '/' + functionName + '',
            method: 'GET',
            data: {
                m: m,pageType1:pageType1,parentCode:parentCode,
                selectMainDepartmentId:selectMainDepartmentId,
                selectSubDepartmentId:selectSubDepartmentId,
                monthYear:monthYear
            },
            success: function (response) {
                $('#' + tbodyId + '').html(response);
            }
        });
    }
}

function viewFilterCreateUpdateSalaryAndAllowancesEmployeeList(){
    var functionName = $('#functionName').val();
    var tbodyId = $('#tbodyId').val();
    var m = $('#m').val();
    var url = ''+baseUrl+'/'+functionName+'';
    var pageType1 = $('#pageType1').val();
    var parentCode = $('#parentCode').val();
    //alert(pageType);
    //return false;
    $('#'+tbodyId+'').html('<tr><td colspan="15"><div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div></div></td><tr>');
    var selectEmployeeId = $('#selectEmployeeId').val();
    var selectEmployee = $('#selectEmployeeTwo').val();
    var selectSubDepartmentId = $('#selectSubDepartmentId').val();
    var selectSubDepartment = $('#selectSubDepartmentTwo').val();

    $.ajax({
        url: '' + baseUrl + '/' + functionName + '',
        method: 'GET',
        data: {
            m: m,pageType1:pageType1,parentCode:parentCode,selectEmployee:selectEmployee,selectEmployeeId:selectEmployeeId,selectSubDepartment:selectSubDepartment,selectSubDepartmentId:selectSubDepartmentId
        },
        success: function (response) {
            $('#' + tbodyId + '').html(response);
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
    var filterType = $('#filterType').val();
    //alert(tbodyId);
    var m = $('#m').val();
    var url = ''+baseUrl+'/'+functionName+'';
    //alert(url); return false;
    $('#'+tbodyId+'').html('<tr><td colspan="25"><div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div></div></td><tr>');
    if(filterType == 1){
        var selectSubDepartmentId = $('#selectSubDepartmentId').val();
        var selectSubDepartment = $('#selectSubDepartmentTwo').val();
        var selectVoucherStatus = $('#selectVoucherStatus').val();
        $.getJSON(url, {fromDate: fromDate, toDate: toDate, m: m,selectSubDepartment:selectSubDepartment,selectSubDepartmentId:selectSubDepartmentId,selectVoucherStatus:selectVoucherStatus}, function (result) {
            $.each(result, function (i, field) {
                $('#' + tbodyId + '').html('' + field + '');
            });
        })
    }else if(filterType == 2){
        var selectSubDepartmentId = $('#selectSubDepartmentId').val();
        var selectSubDepartment = $('#selectSubDepartmentTwo').val();
        var selectSupplierId = $('#selectSupplierId').val();
        var selectSupplier = $('#selectSupplierTwo').val();
        var selectVoucherStatus = $('#selectVoucherStatus').val();
        $.getJSON(url, {fromDate: fromDate, toDate: toDate, m: m,selectSupplier:selectSupplier,selectSupplierId:selectSupplierId,selectSubDepartment:selectSubDepartment,selectSubDepartmentId:selectSubDepartmentId,selectVoucherStatus:selectVoucherStatus}, function (result) {
            $.each(result, function (i, field) {
                $('#' + tbodyId + '').html('' + field + '');
            });
        })
    }else if(filterType == 3){
        var selectBranchId = $('#selectBranchId').val();
        var selectBranch = $('#selectBranchTwo').val();
        var selectVoucherStatus = $('#selectVoucherStatus').val();
        $.getJSON(url, {fromDate: fromDate, toDate: toDate, m: m,selectBranch:selectBranch,selectBranchId:selectBranchId,selectVoucherStatus:selectVoucherStatus}, function (result) {
            $.each(result, function (i, field) {
                $('#' + tbodyId + '').html('' + field + '');
            });
        })
    }else if(filterType == 'EmployeeList'){
        var selectEmployeeId = $('#selectEmployeeId').val();
        var selectEmployee = $('#selectEmployeeTwo').val();
        var selectSubDepartmentId = $('#selectSubDepartmentId').val();
        var selectSubDepartment = $('#selectSubDepartmentTwo').val();
        var selectEmployeeGradingStatus = $('#selectEmployeeGradingStatus').val();
        var selectColumnSortStatus = $('#selectColumnSortStatus').val();
        var selectVoucherStatus = $('#selectVoucherStatus').val();
        $.getJSON(url, {fromDate: fromDate, toDate: toDate, m: m,selectEmployee: selectEmployee,
            selectEmployeeId: selectEmployeeId,selectSubDepartment:selectSubDepartment,selectSubDepartmentId:selectSubDepartmentId,selectEmployeeGradingStatus:selectEmployeeGradingStatus,selectVoucherStatus:selectVoucherStatus,selectColumnSortStatus:selectColumnSortStatus}, function (result) {
            $.each(result, function (i, field) {
                $('#' + tbodyId + '').html('' + field + '');
            });
        })
    }else if(filterType == 'LeftEmployeeList'){
        var selectEmployeeId = $('#selectEmployeeId').val();
        var selectEmployee = $('#selectEmployeeTwo').val();
        var selectSubDepartmentId = $('#selectSubDepartmentId').val();
        var selectSubDepartment = $('#selectSubDepartmentTwo').val();
        var selectEmployeeGradingStatus = $('#selectEmployeeGradingStatus').val();
        var selectVoucherStatus = $('#selectVoucherStatus').val();
        $.getJSON(url, {fromDate: fromDate, toDate: toDate, m: m,selectEmployee: selectEmployee,
            selectEmployeeId: selectEmployeeId,selectSubDepartment:selectSubDepartment,selectSubDepartmentId:selectSubDepartmentId,selectEmployeeGradingStatus:selectEmployeeGradingStatus,selectVoucherStatus:selectVoucherStatus}, function (result) {
            $.each(result, function (i, field) {
                $('#' + tbodyId + '').html('' + field + '');
            });
        })
    }else if(filterType == 'EmployeeListDepartmentWise'){
        var selectMainDepartmentId = $('#main_department_id').val();
        var selectSubDepartmentId = $('#sub_department_id').val();
        $.getJSON(url, {m: m,
            selectMainDepartmentId: selectMainDepartmentId,selectSubDepartmentId:selectSubDepartmentId}, function (result) {
            $.each(result, function (i, field) {
                $('#' + tbodyId + '').html('' + field + '');
            });
        })
    }else if(filterType == 'EmployeeStrengthList'){
        var selectMainDepartmentId = $('#main_department_id').val();
        var selectSubDepartmentId = $('#sub_department_id').val();
        $.getJSON(url, {m: m,
            selectMainDepartmentId: selectMainDepartmentId,selectSubDepartmentId:selectSubDepartmentId}, function (result) {
            $.each(result, function (i, field) {
                $('#' + tbodyId + '').html('' + field + '');
            });
        })
    }else if(filterType == 'WorkingHoursPolicList'){
        var fromDate = $('#fromDate').val();
        var toDate = $('#toDate').val();
        var selectVoucherStatus = $('#selectVoucherStatus').val();
        $.getJSON(url, {fromDate: fromDate, toDate: toDate, m: m,selectVoucherStatus:selectVoucherStatus}, function (result) {
            $.each(result, function (i, field) {
                $('#' + tbodyId + '').html('' + field + '');
            });
        })
    }else if(filterType == 'EmployeeAttendanceList'){
        var fromDate = $('#fromDate').val();
        var toDate = $('#toDate').val();
        var selectEmployeeId = $('#selectEmployeeId').val();
        var selectEmployee = $('#selectEmployeeTwo').val();
        var selectSubDepartmentId = $('#selectSubDepartmentId').val();
        var selectSubDepartment = $('#selectSubDepartmentTwo').val();
        var attendanceStatus = $('#attendance_status').val();
        if(attendanceStatus == 3){
            var lateValue = $('#late_value').val();
            $.getJSON(url, {
                fromDate: fromDate,
                toDate: toDate,
                m: m,
                attendanceStatus: attendanceStatus,
                selectEmployee: selectEmployee,
                selectEmployeeId: selectEmployeeId,
                selectSubDepartment: selectSubDepartment,
                selectSubDepartmentId: selectSubDepartmentId,
                lateValue: lateValue
            }, function (result) {
                $.each(result, function (i, field) {
                    $('#' + tbodyId + '').html('' + field + '');
                });
            })
    }else {
            $.getJSON(url, {
                fromDate: fromDate,
                toDate: toDate,
                m: m,
                attendanceStatus: attendanceStatus,
                selectEmployee: selectEmployee,
                selectEmployeeId: selectEmployeeId,
                selectSubDepartment: selectSubDepartment,
                selectSubDepartmentId: selectSubDepartmentId
            }, function (result) {
                $.each(result, function (i, field) {
                    $('#' + tbodyId + '').html('' + field + '');
                });
            })
        }
    }else if(filterType == 'UsersLoginTimePeriodAndRolePermissionList'){
        var fromDate = $('#fromDate').val();
        var toDate = $('#toDate').val();
        var selectVoucherStatus = $('#selectVoucherStatus').val();
        $.getJSON(url, {fromDate: fromDate, toDate: toDate, m: m,selectVoucherStatus:selectVoucherStatus}, function (result) {
            $.each(result, function (i, field) {
                $('#' + tbodyId + '').html('' + field + '');
            });
        })
    }else if(filterType == 'UsersOptionPermissionList'){
        var fromDate = $('#fromDate').val();
        var toDate = $('#toDate').val();
        var selectVoucherStatus = $('#selectVoucherStatus').val();
        $.getJSON(url, {fromDate: fromDate, toDate: toDate, m: m,selectVoucherStatus:selectVoucherStatus}, function (result) {
            $.each(result, function (i, field) {
                $('#' + tbodyId + '').html('' + field + '');
            });
        })
    }else if(filterType == 'EmployeeAttendanceListMonthWiseSummary'){
        var monthYear = $('#monthYear').val();
        var selectEmployeeId = $('#selectEmployeeId').val();
        var selectEmployee = $('#selectEmployeeTwo').val();
        var selectSubDepartmentId = $('#selectSubDepartmentId').val();
        var selectSubDepartment = $('#selectSubDepartmentTwo').val();
        $.getJSON(url, {monthYear: monthYear, selectEmployeeId: selectEmployeeId, m: m,selectEmployee:selectEmployee,selectSubDepartmentId:selectSubDepartmentId,selectSubDepartment:selectSubDepartment}, function (result) {
            $.each(result, function (i, field) {
                $('#' + tbodyId + '').html('' + field + '');
            });
        })
    }else if(filterType == 'ReturnAbleGatePassList'){
        var fromDate = $('#fromDate').val();
        var toDate = $('#toDate').val();
        var selectVoucherStatus = $('#selectVoucherStatus').val();
        $.getJSON(url, {fromDate: fromDate, toDate: toDate, m: m,selectVoucherStatus:selectVoucherStatus}, function (result) {
            $.each(result, function (i, field) {
                $('#' + tbodyId + '').html('' + field + '');
            });
        })
    }else if(filterType == 'EmployeeAllowanceList'){
        var fromDate = $('#fromDate').val();
        var toDate = $('#toDate').val();
        var selectEmployeeId = $('#selectEmployeeId').val();
        var selectEmployee = $('#selectEmployeeTwo').val();
        var selectSubDepartmentId = $('#selectSubDepartmentId').val();
        var selectSubDepartment = $('#selectSubDepartmentTwo').val();
        var selectVoucherStatus = $('#selectVoucherStatus').val();
        $.getJSON(url, {fromDate: fromDate, toDate: toDate, m: m,selectVoucherStatus:selectVoucherStatus,selectEmployee:selectEmployee,selectEmployeeId:selectEmployeeId,selectSubDepartment:selectSubDepartment,selectSubDepartmentId:selectSubDepartmentId}, function (result) {
            $.each(result, function (i, field) {
                $('#' + tbodyId + '').html('' + field + '');
            });
        })
    }else if(filterType == 'EmployeeLeaveApplicationList'){
        var fromDate = $('#fromDate').val();
        var toDate = $('#toDate').val();
        var selectEmployeeId = $('#selectEmployeeId').val();
        var selectEmployee = $('#selectEmployeeTwo').val();
        var selectSubDepartmentId = $('#selectSubDepartmentId').val();
        var selectSubDepartment = $('#selectSubDepartmentTwo').val();
        var selectLeaveType = $('#selectLeaveType').val();
        var selectLeaveDayType = $('#selectLeaveDayType').val();
        
        $.getJSON(url, {fromDate: fromDate, toDate: toDate, m: m,selectLeaveType:selectLeaveType,selectLeaveDayType:selectLeaveDayType,selectEmployee:selectEmployee,selectEmployeeId:selectEmployeeId,selectSubDepartment:selectSubDepartment,selectSubDepartmentId:selectSubDepartmentId}, function (result) {
            $.each(result, function (i, field) {
                $('#' + tbodyId + '').html('' + field + '');
            });
        })
    }else if(filterType == 'AdvanceSalaryList'){
        var fromDate = $('#fromDate').val();
        var toDate = $('#toDate').val();
        var selectEmployeeId = $('#selectEmployeeId').val();
        var selectEmployee = $('#selectEmployeeTwo').val();
        var selectSubDepartmentId = $('#selectSubDepartmentId').val();
        var selectSubDepartment = $('#selectSubDepartmentTwo').val();
        var selectVoucherStatus = $('#selectVoucherStatus').val();
        $.getJSON(url, {fromDate: fromDate, toDate: toDate, m: m,selectVoucherStatus:selectVoucherStatus,selectEmployee:selectEmployee,selectEmployeeId:selectEmployeeId,selectSubDepartment:selectSubDepartment,selectSubDepartmentId:selectSubDepartmentId}, function (result) {
            $.each(result, function (i, field) {
                $('#' + tbodyId + '').html('' + field + '');
            });
        })
    }else if(filterType == 'ReceiveAdvanceSalaryList'){
        var fromDate = $('#fromDate').val();
        var toDate = $('#toDate').val();
        var selectEmployeeId = $('#selectEmployeeId').val();
        var selectEmployee = $('#selectEmployeeTwo').val();
        var selectSubDepartmentId = $('#selectSubDepartmentId').val();
        var selectSubDepartment = $('#selectSubDepartmentTwo').val();
        var selectVoucherStatus = $('#selectVoucherStatus').val();
        $.getJSON(url, {fromDate: fromDate, toDate: toDate, m: m,selectVoucherStatus:selectVoucherStatus,selectEmployee:selectEmployee,selectEmployeeId:selectEmployeeId,selectSubDepartment:selectSubDepartment,selectSubDepartmentId:selectSubDepartmentId}, function (result) {
            $.each(result, function (i, field) {
                $('#' + tbodyId + '').html('' + field + '');
            });
        })
    }else if(filterType == 'ReceiveLoanList'){
        var fromDate = $('#fromDate').val();
        var toDate = $('#toDate').val();
        var selectEmployeeId = $('#selectEmployeeId').val();
        var selectEmployee = $('#selectEmployeeTwo').val();
        var selectSubDepartmentId = $('#selectSubDepartmentId').val();
        var selectSubDepartment = $('#selectSubDepartmentTwo').val();
        var selectVoucherStatus = $('#selectVoucherStatus').val();
        $.getJSON(url, {fromDate: fromDate, toDate: toDate, m: m,selectVoucherStatus:selectVoucherStatus,selectEmployee:selectEmployee,selectEmployeeId:selectEmployeeId,selectSubDepartment:selectSubDepartment,selectSubDepartmentId:selectSubDepartmentId}, function (result) {
            $.each(result, function (i, field) {
                $('#' + tbodyId + '').html('' + field + '');
            });
        })
    }else if(filterType == 'LoanRequestList'){
        var fromDate = $('#fromDate').val();
        var toDate = $('#toDate').val();
        var selectEmployeeId = $('#selectEmployeeId').val();
        var selectEmployee = $('#selectEmployeeTwo').val();
        var selectSubDepartmentId = $('#selectSubDepartmentId').val();
        var selectSubDepartment = $('#selectSubDepartmentTwo').val();
        var selectVoucherStatus = $('#selectVoucherStatus').val();
        $.getJSON(url, {fromDate: fromDate, toDate: toDate, m: m,selectVoucherStatus:selectVoucherStatus,selectEmployee:selectEmployee,selectEmployeeId:selectEmployeeId,selectSubDepartment:selectSubDepartment,selectSubDepartmentId:selectSubDepartmentId}, function (result) {
            $.each(result, function (i, field) {
                $('#' + tbodyId + '').html('' + field + '');
            });
        })
    }else if(filterType == 'filterViewUpdateSalaryAndAllowancesList'){
        var fromDate = $('#fromDate').val();
        var toDate = $('#toDate').val();
        var selectEmployeeId = $('#selectEmployeeId').val();
        var selectEmployee = $('#selectEmployeeTwo').val();
        var selectSubDepartmentId = $('#selectSubDepartmentId').val();
        var selectSubDepartment = $('#selectSubDepartmentTwo').val();
        var selectVoucherStatus = $('#selectVoucherStatus').val();
        $.getJSON(url, {fromDate: fromDate, toDate: toDate, m: m,selectVoucherStatus:selectVoucherStatus,selectEmployee:selectEmployee,selectEmployeeId:selectEmployeeId,selectSubDepartment:selectSubDepartment,selectSubDepartmentId:selectSubDepartmentId}, function (result) {
            $.each(result, function (i, field) {
                $('#' + tbodyId + '').html('' + field + '');
            });
        })
    }else if(filterType == 'filterViewSalaryMonthlyDeductionList'){
        var monthYear = $('#month_year').val();
        if(monthYear == ''){
            alert('Please Select Month and Year');
        }else {
            var fromDate = $('#fromDate').val();
            var toDate = $('#toDate').val();
            var selectEmployeeId = $('#selectEmployeeId').val();
            var selectEmployee = $('#selectEmployeeTwo').val();
            var selectSubDepartmentId = $('#selectSubDepartmentId').val();
            var selectSubDepartment = $('#selectSubDepartmentTwo').val();
            var selectVoucherStatus = $('#selectVoucherStatus').val();

            $.getJSON(url, {
                fromDate: fromDate,
                toDate: toDate,
                monthYear: monthYear,
                m: m,
                selectVoucherStatus: selectVoucherStatus,
                selectEmployee: selectEmployee,
                selectEmployeeId: selectEmployeeId,
                selectSubDepartment: selectSubDepartment,
                selectSubDepartmentId: selectSubDepartmentId
            }, function (result) {
                $.each(result, function (i, field) {
                    $('#' + tbodyId + '').html('' + field + '');
                });
            })
        }
    }else if(filterType == 'UsersLoginTimePeriodAndRolePermissionList'){
		
        $.getJSON(url, {m: m}, function (result) {
            $.each(result, function (i, field) {
                $('#' + tbodyId + '').html('' + field + '');
            });
        })
    }

}






function rejectAdvanceSalaryWithPaySlip(companyId,recordId,tableName,column){
    var companyId;
    var recordId;
    var tableName;
    var column;
    var functionName = 'cdOne/rejectAdvanceSalaryWithPaySlip';

    $.ajax({
        url: ''+baseUrl+'/'+functionName+'',
        type: "GET",
        data: {companyId:companyId,recordId:recordId,tableName:tableName,column:column},
        success:function(data) {
            location.reload();
        }
    });

}


function approveAdvanceSalaryWithPaySlip(companyId,recordId,emp_id){
    var companyId;
    var recordId;
    var emp_id;
    var functionName = 'cdOne/approveAdvanceSalaryWithPaySlip';
    $.ajax({
        url: ''+baseUrl+'/'+functionName+'',
        type: "GET",
        data: {companyId:companyId,recordId:recordId,emp_id:emp_id},
        success:function(data) {
            location.reload();
        }
    });

}
function deleteAdvanceSalaryWithPaySlip(companyId,recordId,tableName){
    var companyId;
    var recordId;
    var tableName;
    var functionName = 'cdOne/deleteAdvanceSalaryWithPaySlip';

    $.ajax({
        url: ''+baseUrl+'/'+functionName+'',
        type: "GET",
        data: {companyId:companyId,recordId:recordId,tableName:tableName},
        success:function(data) {
            location.reload();
        }
    });

}

function approveLoanRequest(companyId,recordId) {
    var companyId;
    var recordId;

    var functionName = 'cdOne/approveLoanRequest';
    $.ajax({
        url: ''+baseUrl+'/'+functionName+'',
        type: "GET",
        data: {companyId:companyId,recordId:recordId},
        success:function(data) {
            location.reload();
        }
    });

}

function rejectLoanRequest(companyId,recordId) {
    var companyId;
    var recordId;

    var functionName = 'cdOne/rejectLoanRequest';
    $.ajax({
        url: ''+baseUrl+'/'+functionName+'',
        type: "GET",
        data: {companyId:companyId,recordId:recordId},
        success:function(data) {
            location.reload();
        }
    });

}

function deleteLoanRequest(companyId,recordId){
    var companyId;
    var recordId;

    var functionName = 'cdOne/deleteLoanRequest';
    $.ajax({
        url: ''+baseUrl+'/'+functionName+'',
        type: "GET",
        data: {companyId:companyId,recordId:recordId},
        success:function(data) {
            location.reload();
        }
    });
}

function deleteLeaveApplicationData(companyId,recordId)
{
    var companyId;
    var recordId;
    var functionName = 'cdOne/deleteLeaveApplicationDetail';

    if(confirm('Do you Want To Delete Leave Application ?')){
        $.ajax({
            url: ''+baseUrl+'/'+functionName+'',
            type: "GET",
            data: {companyId:companyId,recordId:recordId},
            success:function(data) {
                location.reload();
            }
        });
    }
}


function approveAndRejectRequestHiring(companyId,recordId,approval_status)
{
    var functionName = 'cdOne/approveAndRejectRequestHiring';
    $.ajax({
        url: ''+baseUrl+'/'+functionName+'',
        type: "GET",
        data: {companyId:companyId,recordId:recordId,approval_status:approval_status},
        success:function(data) {
            location.reload();
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