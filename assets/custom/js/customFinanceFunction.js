var baseUrl = $("#baseUrl").val();
function viewRangeWiseDataFilter() {
  var fromDateOld = $("#fromDate").val();
  var toDateOld = $("#toDate").val();

  var fromDate = fromDateOld
    .split("-")
    .reverse()
    .join("-");
  var toDate = toDateOld
    .split("-")
    .reverse()
    .join("-");

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

function loadChartOfAccountDetailTypeWise(
  paramOne,
  paramTwo,
  pageType,
  parentCode
) {
  var filterType = $("#filterType").val();
  var functionName = $("#functionName").val();
  var tbodyId = $("#tbodyId").val();
  var m = $("#m").val();
  var url = "" + baseUrl + "/" + functionName + "";
  $("#" + tbodyId + "").html(
    '<tr><td colspan="1000000000"><div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div></div></td><tr>'
  );
  $.ajax({
    url: "" + baseUrl + "/" + functionName + "",
    method: "GET",
    data: {
      m: m,
      type: paramTwo,
      pageType: pageType,
      parentCode: parentCode
    },
    success: function(response) {
      $("#" + tbodyId + "").html(response);
    }
  });
}

function filterVoucherList() {
  var fromDate = $("#fromDate").val();
  var toDate = $("#toDate").val();
  var functionName = $("#functionName").val();
  var tbodyId = $("#tbodyId").val();
  var filterType = $("#filterType").val();
  var startRecordNo = $("#startRecordNo").val();
  var endRecordNo = $("#endRecordNo").val();
  var pageType = $("#pageTypeTwo").val();
  var parentCode = $("#parentCode").val();
  var m = $("#m").val();
  var url = "" + baseUrl + "/" + functionName + "";
  $("#" + tbodyId + "").html(
    '<tr><td colspan="50"><div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div></div></td><tr>'
  );

  if (filterType == "purchaseCPVList") {
    var fromDate = $("#fromDate").val();
    var toDate = $("#toDate").val();
    var filterLocationId = $("#filterLocationId").val();
    var filterDepartmentId = $("#filterDepartmentId").val();
    var selectVoucherStatus = $("#selectVoucherStatus").val();
    var filterSupplierId = $("#filterSupplierId").val();
    $.ajax({
      url: "" + baseUrl + "/" + functionName + "",
      method: "GET",
      data: {
        fromDate: fromDate,
        toDate: toDate,
        m: m,
        filterLocationId: filterLocationId,
        filterDepartmentId: filterDepartmentId,
        selectVoucherStatus: selectVoucherStatus,
        filterSupplierId: filterSupplierId,
        startRecordNo: startRecordNo,
        endRecordNo: endRecordNo,
        pageType: pageType,
        parentCode: parentCode
      },
      success: function(response) {
        $("#" + tbodyId + "").html(response);
      }
    });
  } else if(filterType == "journalVouchersLogs"){
    var fromDate = $("#fromDate").val();
    var toDate = $("#toDate").val();
    var selectOptionType = $('#selectOptionType').val();
    $.ajax({
      url: "" + baseUrl + "/" + functionName + "",
      method: "GET",
      data: {
        fromDate: fromDate,
        toDate: toDate,
        m: m,
        selectOptionType: selectOptionType,
        startRecordNo: startRecordNo,
        endRecordNo: endRecordNo,
        pageType: pageType,
        parentCode: parentCode
      },
      success: function(response) {
        $("#" + tbodyId + "").html(response);
      }
    });
  } else if (filterType == "purchaseBPVList") {
    var fromDate = $("#fromDate").val();
    var toDate = $("#toDate").val();
    var filterLocationId = $("#filterLocationId").val();
    var filterDepartmentId = $("#filterDepartmentId").val();
    var selectVoucherStatus = $("#selectVoucherStatus").val();
    var filterSupplierId = $("#filterSupplierId").val();
    $.ajax({
      url: "" + baseUrl + "/" + functionName + "",
      method: "GET",
      data: {
        fromDate: fromDate,
        toDate: toDate,
        m: m,
        filterLocationId: filterLocationId,
        filterDepartmentId: filterDepartmentId,
        selectVoucherStatus: selectVoucherStatus,
        filterSupplierId: filterSupplierId,
        startRecordNo: startRecordNo,
        endRecordNo: endRecordNo,
        pageType: pageType,
        parentCode: parentCode
      },
      success: function(response) {
        $("#" + tbodyId + "").html(response);
      }
    });
  } else if (filterType == "chartOfAccountList") {
    var type = "0";
    var pageType = $("#pageType").val();
    var parentCode = $("#parentCode").val();
    loadChartOfAccountDetailTypeWise(m, type, pageType, parentCode);
  } else if (filterType == "iouVouchers") {
    var fromDate = $("#fromDate").val();
    var toDate = $("#toDate").val();
    $.ajax({
      url: "" + baseUrl + "/" + functionName + "",
      method: "GET",
      data: {
        fromDate: fromDate,
        toDate: toDate,
        m: m
      },
      success: function(response) {
        $("#" + tbodyId + "").html(response);
      }
    });
  } else if (filterType == "billRecordingVoucherList") {
    var fromDate = $("#fromDate").val();
    var toDate = $("#toDate").val();
    var selectRecordType = $("#selectRecordType").val();
    var filterAccountHeadId = $("#filterAccountHeadId").val();
    var selectUsername = $("#selectUsername").val();
    var filterAccountHeadIdText = $(
      "#filterAccountHeadId option:selected"
    ).text();
    $.ajax({
      url: "" + baseUrl + "/" + functionName + "",
      method: "GET",
      data: {
        fromDate: fromDate,
        toDate: toDate,
        m: m,
        selectRecordType: selectRecordType,
        filterAccountHeadId: filterAccountHeadId,
        filterAccountHeadIdText: filterAccountHeadIdText,
        selectUsername: selectUsername
      },
      success: function(response) {
        $("#" + tbodyId + "").html(response);
      }
    });
  } else if (filterType == "paymentVouchers") {
    var fromDate = $("#fromDate").val();
    var toDate = $("#toDate").val();
    var selectVoucherStatus = $("#selectVoucherStatus").val();
    var selectVoucherType = $("#selectVoucherType").val();
    var filterLocationId = $("#filterLocationId").val();
    var filterDepartmentId = $("#filterDepartmentId").val();
    $.ajax({
      url: "" + baseUrl + "/" + functionName + "",
      method: "GET",
      data: {
        fromDate: fromDate,
        toDate: toDate,
        m: m,
        selectVoucherStatus: selectVoucherStatus,
        selectVoucherType: selectVoucherType,
        filterLocationId: filterLocationId,
        filterDepartmentId: filterDepartmentId
      },
      success: function(response) {
        $("#" + tbodyId + "").html(response);
      }
    });
  } else if (filterType == "receiptVouchers") {
    var fromDate = $("#fromDate").val();
    var toDate = $("#toDate").val();
    var selectVoucherStatus = $("#selectVoucherStatus").val();
    var selectVoucherType = $("#selectVoucherType").val();
    var filterLocationId = $("#filterLocationId").val();
    var filterDepartmentId = $("#filterDepartmentId").val();
    $.ajax({
      url: "" + baseUrl + "/" + functionName + "",
      method: "GET",
      data: {
        fromDate: fromDate,
        toDate: toDate,
        m: m,
        selectVoucherStatus: selectVoucherStatus,
        selectVoucherType: selectVoucherType,
        filterLocationId: filterLocationId,
        filterDepartmentId: filterDepartmentId
      },
      success: function(response) {
        $("#" + tbodyId + "").html(response);
      }
    });
  } else if (filterType == "journalVouchers") {
    var fromDate = $("#fromDate").val();
    var toDate = $("#toDate").val();
    var selectVoucherStatus = $("#selectVoucherStatus").val();
    var filterLocationId = $("#filterLocationId").val();
    var filterDepartmentId = $("#filterDepartmentId").val();
    $.ajax({
      url: "" + baseUrl + "/" + functionName + "",
      method: "GET",
      data: {
        fromDate: fromDate,
        toDate: toDate,
        m: m,
        selectVoucherStatus: selectVoucherStatus,
        filterLocationId: filterLocationId,
        filterDepartmentId: filterDepartmentId
      },
      success: function(response) {
        $("#" + tbodyId + "").html(response);
      }
    });
  } else if (filterType == "distributionBankAccountList") {
    $.ajax({
      url: "" + baseUrl + "/" + functionName + "",
      method: "GET",
      data: {
        m: m
      },
      success: function(response) {
        $("#" + tbodyId + "").html(response);
      }
    });
  } else if (filterType == "transferOfFundList") {
    var fromDate = $("#fromDate").val();
    var toDate = $("#toDate").val();
    var filterTranferType = $("#filterTranferType").val();
    var filterTranferTypeNameText = $(
      "#filterTranferType option:selected"
    ).text();
    var selectVoucherStatus = $("#selectVoucherStatus").val();
    var selectVoucherStatusText = $(
      "#selectVoucherStatus option:selected"
    ).text();
    $.ajax({
      url: "" + baseUrl + "/" + functionName + "",
      method: "GET",
      data: {
        fromDate: fromDate,
        toDate: toDate,
        m: m,
        filterTranferType: filterTranferType,
        filterTranferTypeNameText: filterTranferTypeNameText,
        selectVoucherStatus: selectVoucherStatus,
        selectVoucherStatusText: selectVoucherStatusText
      },
      success: function(response) {
        $("#" + tbodyId + "").html(response);
      }
    });
  } else {
    var fromDate = $("#fromDate").val();
    var toDate = $("#toDate").val();
    var selectVoucherStatus = $("#selectVoucherStatus").val();
    var selectAccountHead = $("#selectAccountHeadTwo").val();
    var selectAccountHeadId = $("#selectAccountHeadId").val();
    $.ajax({
      url: "" + baseUrl + "/" + functionName + "",
      method: "GET",
      data: {
        fromDate: fromDate,
        toDate: toDate,
        m: m,
        selectVoucherStatus: selectVoucherStatus,
        selectAccountHead: selectAccountHead,
        selectAccountHeadId: selectAccountHeadId
      },
      success: function(response) {
        $("#" + tbodyId + "").html(response);
      }
    });
  }
}
filterVoucherList();

function deleteCompanyFinanceTwoTableRecords(
  m,
  voucherStatus,
  rowStatus,
  columnValue,
  columnOne,
  columnTwo,
  columnThree,
  tableOne,
  tableTwo
) {
  $.ajax({
    url: "" + baseUrl + "/fd/deleteCompanyFinanceTwoTableRecords",
    type: "GET",
    data: {
      m: m,
      voucherStatus: voucherStatus,
      rowStatus: rowStatus,
      columnValue: columnValue,
      columnOne: columnOne,
      columnTwo: columnTwo,
      columnThree: columnThree,
      tableOne: tableOne,
      tableTwo: tableTwo
    },
    success: function(data) {
      filterVoucherList();
    }
  });
}

function deleteChartOfAccountRecords(
  m,
  voucherStatus,
  rowStatus,
  columnValue,
  columnOne,
  columnTwo,
  columnThree,
  tableOne,
  tableTwo
) {
  $.ajax({
    url: "" + baseUrl + "/fd/deleteChartOfAccountRecords",
    type: "GET",
    data: {
      m: m,
      voucherStatus: voucherStatus,
      rowStatus: rowStatus,
      columnValue: columnValue,
      columnOne: columnOne,
      columnTwo: columnTwo,
      columnThree: columnThree,
      tableOne: tableOne,
      tableTwo: tableTwo
    },
    success: function(data) {
      filterVoucherList();
    }
  });
}

function approveChartOfAccountRecords(
  m,
  voucherStatus,
  rowStatus,
  columnValue,
  columnOne,
  columnTwo,
  columnThree,
  tableOne,
  tableTwo
) {
  $.ajax({
    url: "" + baseUrl + "/fd/approveChartOfAccountRecords",
    type: "GET",
    data: {
      m: m,
      voucherStatus: voucherStatus,
      rowStatus: rowStatus,
      columnValue: columnValue,
      columnOne: columnOne,
      columnTwo: columnTwo,
      columnThree: columnThree,
      tableOne: tableOne,
      tableTwo: tableTwo
    },
    success: function(data) {
      filterVoucherList();
    }
  });
}

function repostChartOfAccountRecords(
  m,
  voucherStatus,
  rowStatus,
  columnValue,
  columnOne,
  columnTwo,
  columnThree,
  tableOne,
  tableTwo
) {
  $.ajax({
    url: "" + baseUrl + "/fd/repostChartOfAccountRecords",
    type: "GET",
    data: {
      m: m,
      voucherStatus: voucherStatus,
      rowStatus: rowStatus,
      columnValue: columnValue,
      columnOne: columnOne,
      columnTwo: columnTwo,
      columnThree: columnThree,
      tableOne: tableOne,
      tableTwo: tableTwo
    },
    success: function(data) {
      filterVoucherList();
    }
  });
}

function deleteDistributionBankAccountRecords(
  m,
  accId,
  rowStatus,
  columnValue,
  columnOne,
  columnTwo,
  columnThree,
  tableOne,
  tableTwo
) {
  var mainTitle = "Distribution";
  $.ajax({
    url: "" + baseUrl + "/fd/deleteDistributionBankAccountRecords",
    type: "GET",
    data: {
      m: m,
      accId: accId,
      rowStatus: rowStatus,
      columnValue: columnValue,
      columnOne: columnOne,
      columnTwo: columnTwo,
      columnThree: columnThree,
      tableOne: tableOne,
      tableTwo: tableTwo,
      mainTitle: mainTitle
    },
    success: function(data) {
      if (data == "duplicate") {
        checkUserPermissionForSingleOptionModel(
          "/bdc/dontPermissionForModal",
          "Delete Details"
        );
      } else {
        filterVoucherList();
      }
    }
  });
}

function repostDistributionBankAccountRecords(
  m,
  accId,
  rowStatus,
  columnValue,
  columnOne,
  columnTwo,
  columnThree,
  tableOne,
  tableTwo
) {
  var mainTitle = "Distribution";
  $.ajax({
    url: "" + baseUrl + "/fd/repostDistributionBankAccountRecords",
    type: "GET",
    data: {
      m: m,
      accId: accId,
      rowStatus: rowStatus,
      columnValue: columnValue,
      columnOne: columnOne,
      columnTwo: columnTwo,
      columnThree: columnThree,
      tableOne: tableOne,
      tableTwo: tableTwo,
      mainTitle: mainTitle
    },
    success: function(data) {
      if (data == "duplicate") {
        checkUserPermissionForSingleOptionModel(
          "/bdc/dontPermissionForModal",
          "Repost Details"
        );
      } else {
        filterVoucherList();
      }
    }
  });
}

function deleteTransferOfFundRecords(m, status, id, jvNo) {
  var mainTitle = "Distribution";
  $.ajax({
    url: "" + baseUrl + "/fd/deleteTransferOfFundRecords",
    type: "GET",
    data: { m: m, status: status, id: id, jvNo: jvNo, mainTitle: mainTitle },
    success: function(data) {
      if (data == "duplicate") {
        checkUserPermissionForSingleOptionModel(
          "/bdc/dontPermissionForModal",
          "delete Details"
        );
      } else {
        filterVoucherList();
      }
    }
  });
}

function repostTransferOfFundRecords(m, status, id, jvNo) {
  var mainTitle = "Distribution";
  $.ajax({
    url: "" + baseUrl + "/fd/repostTransferOfFundRecords",
    type: "GET",
    data: { m: m, status: status, id: id, jvNo: jvNo, mainTitle: mainTitle },
    success: function(data) {
      if (data == "duplicate") {
        checkUserPermissionForSingleOptionModel(
          "/bdc/dontPermissionForModal",
          "Repost Details"
        );
      } else {
        filterVoucherList();
      }
    }
  });
}

function reverseFinanceVoucherAfterApproval(
  m,
  voucherStatus,
  rowStatus,
  columnValue,
  columnOne,
  columnTwo,
  columnThree,
  tableOne,
  tableTwo
) {
  $.ajax({
    url: "" + baseUrl + "/fd/reverseFinanceVoucherAfterApproval",
    type: "GET",
    data: {
      m: m,
      voucherStatus: voucherStatus,
      rowStatus: rowStatus,
      columnValue: columnValue,
      columnOne: columnOne,
      columnTwo: columnTwo,
      columnThree: columnThree,
      tableOne: tableOne,
      tableTwo: tableTwo
    },
    success: function(data) {
      filterVoucherList();
    }
  });
}

function repostCompanyFinanceTwoTableRecords(
  m,
  voucherStatus,
  rowStatus,
  columnValue,
  columnOne,
  columnTwo,
  columnThree,
  tableOne,
  tableTwo
) {
  $.ajax({
    url: "" + baseUrl + "/fd/repostCompanyFinanceTwoTableRecords",
    type: "GET",
    data: {
      m: m,
      voucherStatus: voucherStatus,
      rowStatus: rowStatus,
      columnValue: columnValue,
      columnOne: columnOne,
      columnTwo: columnTwo,
      columnThree: columnThree,
      tableOne: tableOne,
      tableTwo: tableTwo
    },
    success: function(data) {
      filterVoucherList();
    }
  });
}

function approveCompanyFinanceTwoTableRecords(
  m,
  voucherStatus,
  rowStatus,
  columnValue,
  columnOne,
  columnTwo,
  columnThree,
  tableOne,
  tableTwo
) {
  $.ajax({
    url: "" + baseUrl + "/fd/approveCompanyFinanceTwoTableRecords",
    type: "GET",
    data: {
      m: m,
      voucherStatus: voucherStatus,
      rowStatus: rowStatus,
      columnValue: columnValue,
      columnOne: columnOne,
      columnTwo: columnTwo,
      columnThree: columnThree,
      tableOne: tableOne,
      tableTwo: tableTwo
    },
    success: function(data) {
      filterVoucherList();
    }
  });
}

function displayCancelAdvanceSalaryVoucher(
  m,
  voucherStatus,
  rowStatus,
  columnValue,
  columnOne,
  columnTwo,
  columnThree,
  tableOne,
  tableTwo,
  voucherType
) {
  $.ajax({
    url: "" + baseUrl + "/fd/displayCancelAdvanceSalaryVoucher",
    type: "GET",
    data: {
      m: m,
      voucherStatus: voucherStatus,
      rowStatus: rowStatus,
      columnValue: columnValue,
      columnOne: columnOne,
      columnTwo: columnTwo,
      columnThree: columnThree,
      tableOne: tableOne,
      tableTwo: tableTwo,
      voucherType: voucherType
    },
    success: function(data) {
      filterVoucherList();
    }
  });
}

function displayApproveAdvanceSalaryVoucher(
  m,
  voucherStatus,
  rowStatus,
  columnValue,
  columnOne,
  columnTwo,
  columnThree,
  tableOne,
  tableTwo,
  voucherType
) {
  $.ajax({
    url: "" + baseUrl + "/fd/displayApproveAdvanceSalaryVoucher",
    type: "GET",
    data: {
      m: m,
      voucherStatus: voucherStatus,
      rowStatus: rowStatus,
      columnValue: columnValue,
      columnOne: columnOne,
      columnTwo: columnTwo,
      columnThree: columnThree,
      tableOne: tableOne,
      tableTwo: tableTwo,
      voucherType: voucherType
    },
    success: function(data) {
      filterVoucherList();
    }
  });
}

function deleteCompanyFinanceThreeTableRecords(
  m,
  voucherStatus,
  rowStatus,
  columnValue,
  columnOne,
  columnTwo,
  columnThree,
  tableOne,
  tableTwo,
  tableThree
) {
  $.ajax({
    url: "" + baseUrl + "/fd/deleteCompanyFinanceThreeTableRecords",
    type: "GET",
    data: {
      m: m,
      voucherStatus: voucherStatus,
      rowStatus: rowStatus,
      columnValue: columnValue,
      columnOne: columnOne,
      columnTwo: columnTwo,
      columnThree: columnThree,
      tableOne: tableOne,
      tableTwo: tableTwo,
      tableThree: tableThree
    },
    success: function(data) {
      filterVoucherList();
    }
  });
}

function repostCompanyFinanceThreeTableRecords(
  m,
  voucherStatus,
  rowStatus,
  columnValue,
  columnOne,
  columnTwo,
  columnThree,
  tableOne,
  tableTwo,
  tableThree
) {
  $.ajax({
    url: "" + baseUrl + "/fd/repostCompanyFinanceThreeTableRecords",
    type: "GET",
    data: {
      m: m,
      voucherStatus: voucherStatus,
      rowStatus: rowStatus,
      columnValue: columnValue,
      columnOne: columnOne,
      columnTwo: columnTwo,
      columnThree: columnThree,
      tableOne: tableOne,
      tableTwo: tableTwo,
      tableThree: tableThree
    },
    success: function(data) {
      filterVoucherList();
    }
  });
}

$("#selectAccountHeadTwo")
  .change(function() {
    var selectAccountHeadTwo = $(
      '#selectAccountHead option[value="' +
        $("#selectAccountHeadTwo").val() +
        '"]'
    ).data("id");
    $("#selectAccountHeadId").val(selectAccountHeadTwo);
  })
  .change();
