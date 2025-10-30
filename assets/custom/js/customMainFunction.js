// // Wait until the document is fully loaded
// $(document).ready(function () {
//   // Apply Select2 to all elements with the 'select2' class
//   $('.select2').select2({
//     placeholder: "Select an option",
//     allowClear: true,
//     width: '100%' // Ensures dropdown fits the container width
//   });
// });

//Start Print
function printView(param1, param2, param3) {
  $(".qrCodeDiv").removeClass("hidden");
  $("div").removeClass("well");
  $(".pdfClass").removeClass("hidden");
  $(".pdfClass").removeClass("hidden");
  $('.table-responsive').removeClass('overflowscroll');
  $('.table-responsive').removeClass('wrapper');
  $('.d-print-inline-block').removeClass('d-none d-print-inline-block');
  var printContents = document.getElementById(param1).innerHTML;
  var originalContents = document.body.innerHTML;
  document.body.innerHTML = printContents;
  window.print();
  document.body.innerHTML = originalContents;
  //if(param3 == 1){
  location.reload();
  //}
}

function printViewTwo(param1, param2, param3) {
  $(".qrCodeDiv").removeClass("hidden");
  $("div").removeClass("well");
  var printContents = document.getElementById(param1).innerHTML;
  var originalContents = document.body.innerHTML;
  document.body.innerHTML = printContents;
  window.print();
  document.body.innerHTML = originalContents;

  $("#showDetailModelOneParamerter").modal("toggle");
}

function ExportToExcelTwo(name, id) {
  var table = $('#' + id + '');
  $(table).table2excel({
    // exclude CSS class
    exclude: '.noExl',
    name: name,
    filename: '' + name + '_' + $.now(),//do not include extension
    fileext: '.xls', // file extension
    preserveColors: true,
    exclude_links: true,
    sheetName: '' + name + '_'
  });
}

function changeAccountingYear() {
  var changeAYearValue = $("#changeAYear").val();
  var changeAYearText = $("#changeAYear option:selected").text();
  var changeAYearTextSplit = changeAYearText.split(" <--> ");
  var fromDateOld = changeAYearTextSplit[0];
  var toDateOld = changeAYearTextSplit[1];

  var fromDate = fromDateOld
    .split("-")
    .reverse()
    .join("-");
  var toDate = toDateOld
    .split("-")
    .reverse()
    .join("-");
  var baseUrl = $("#url").val();

  $.ajax({
    url: "" + baseUrl + "/updateAccountingYearInSession",
    method: "GET",
    data: {
      fromDate: fromDate,
      toDate: toDate,
      changeAYearValue: changeAYearValue
    },
    error: function () {
      alert("error");
    },
    success: function (response) {
      location.reload();
    }
  });
}

function updateStartEndField() {
  $("#startRecordNo").val("0");
  $("#endRecordNo").val("30");
}

var validate = 0;
function jqueryValidationCustom() {
  var requiredField = document.getElementsByClassName("requiredField");
  // console.log(requiredField);
  for (i = 0; i < requiredField.length; i++) {
    var rf = requiredField[i].id;
    // console.log(rf);
    var checkType = requiredField[i].type;
    console.log(checkType);
    if ($("#" + rf).val() == "") {
      $("#" + rf).css("border-color", "red !important");
      $("#" + rf).focus();
      // validate = 1;

      if ($("#" + rf).attr('disabled')) {
        validate = 0
      } else {
        validate = 1;
      }
      alert(rf + ' ' + 'Required');
      return false;
    } else {
      $("#submit-btn-abc").prop("disabled", false);
      $("#" + rf).css("border-color", "#ccc");
      // console.log($("#" + rf).attr('disabled'));
      validate = 0;
      console.log('in2');
    }
  }
  return validate;
}

function dashboardPrint(param1, param2, param3) {
  var printedVaues = [];
  var printContents1;
  var originalContents;
  var chaljao = [];
  $(".qrCodeDiv").removeClass("hidden");
  $("div").removeClass("well");
  $(".collectionRefund").removeClass("col-lg-6 col-md-6 col-sm-6 col-xs-6");
  $(".collectionRefund").addClass("col-lg-5 col-md-5 col-sm-5 col-xs-5");

  $(".bookingFunction").removeClass("col-lg-12 col-md-12 col-sm-12 col-xs-12");
  $(".bookingFunction").addClass("col-lg-3 col-md-3 col-sm-3 col-xs-3");

  $(".bookingOrder").removeClass("col-lg-6 col-md-6 col-sm-6 col-xs-12");
  $(".bookingOrder").addClass("col-lg-6 col-md-6 col-sm-6 col-xs-6");

  $(".bookingCollection").removeClass("col-lg-3 col-md-3 col-sm-3 col-xs-12");
  $(".bookingCollection").addClass("col-lg-3 col-md-3 col-sm-3 col-xs-3");

  $(".bookingRefund").removeClass("col-lg-3 col-md-3 col-sm-3 col-xs-12");
  $(".bookingRefund").addClass("col-lg-3 col-md-3 col-sm-3 col-xs-3");

  var printDate = document.getElementById("printDateCheck").innerHTML;
  for (i = 0; i < param1.length; i++) {
    printedVaues.push(param1[i]);
  }
  var originalContents = document.body.innerHTML;
  document.body.innerHTML = printDate + getElementById(printedVaues);
  window.print();
  document.body.innerHTML = originalContents;
  //if(param3 == 1){
  location.reload();
  //}

  function getElementById(ids) {
    var testing = [];
    var idList = ids.join(" ");
    var strArray = idList.split(" ");
    var results = [],
      item;
    for (var i = 0; i < strArray.length; i++) {
      //alert(strArray[i]);
      item = document.getElementById(strArray[i]).innerHTML;
      console.log(item);
      chaljao.push(item);
    }

    return chaljao;
  }
}

function singleDateWisePrint(param1, param2, param3, param4) {
  var checkedValues = [];
  var unCheckedValues = [];
  var printContents1;
  var originalContents;
  var chaljao = [];

  for (i = 0; i < param2.length; i++) {
    checkedValues.push(param2[i]);
    var finalCheckValues = checkedValues.join(" ");
    $("#" + finalCheckValues).addClass("visible-print");
  }
  for (i = 0; i < param3.length; i++) {
    unCheckedValues.push(param3[i]);
    var finalUnCheckValues = unCheckedValues.join(" ");
    $("#" + param3[i]).addClass("hidden-print");
  }

  var printContents = document.getElementById(param1).innerHTML;
  var originalContents = document.body.innerHTML;
  document.body.innerHTML = printContents;
  window.print();
  document.body.innerHTML = originalContents;
  //if(param3 == 1){
  location.reload();
  //}
}

//End Print

//Start Generate PDF
function generateCSVFile(tableId, fileName) {
  $('.hidden-print').remove();
  $('.hideExportTwo').remove();
  $("#" + tableId + "").tableHTMLExport({ type: 'csv', filename: '' + fileName + '.csv' });
  location.reload();
}
function generatePDFFile(tableId, fileName) {
  // Ensure to remove any hidden elements and show necessary elements
  $('.hidden-print').remove();
  $(".pdfClass").removeClass("hidden");

  // Get the table element
  var table = document.getElementById(tableId);

  // Check if the table exists
  if (!table) {
    console.error("Table with ID '" + tableId + "' not found.");
    return;
  }

  // Calculate the dimensions of the table
  var width = table.offsetWidth;
  var height = table.offsetHeight;

  // Use dom-to-image to convert to PNG
  domtoimage.toPng(table, {
    bgcolor: '#ffffff'
  }).then(function (dataUrl) {
    var img = new Image();
    img.src = dataUrl;

    img.onload = function () {
      // Create a new jsPDF instance
      var pdf = new jsPDF('l', 'pt', [img.width, img.height]);

      // Add the image to the PDF
      pdf.addImage(img, 'PNG', 0, 0, img.width, img.height);

      // Save the PDF
      pdf.save(fileName + ".pdf");

      // Optional: Refresh the page
      location.reload();
    };
  }).catch(function (error) {
    console.error('Error generating PDF:', error);
  });
}
//End Generate PDF

//Start Export
function exportView(param1, param2, $param3) {
  $("#" + param1 + "").tableToCSV();
}

jQuery.fn.tableToCSV = function () {
  var clean_text = function (text) {
    text = text.replace(/"/g, '""');
    return '"' + text + '"';
  };

  $(this).each(function () {
    var table = $(this);
    var caption = $(this)
      .find("caption")
      .text();
    var title = [];
    var rows = [];

    $(this)
      .find("tr")
      .each(function () {
        var data = [];
        $(this)
          .find("th")
          .each(function () {
            var text = clean_text($(this).text());
            title.push(text);
          });
        $(this)
          .find("td")
          .each(function () {
            var text = clean_text($(this).text());
            data.push(text);
          });
        data = data.join(",");
        rows.push(data);
      });
    title = title.join(",");
    rows = rows.join("\n");

    var csv = title + rows;
    var uri = "data:text/csv;charset=utf-8," + encodeURIComponent(csv);
    var download_link = document.createElement("a");
    download_link.href = uri;
    var ts = new Date().getTime();
    if (caption == "") {
      download_link.download = ts + ".csv";
    } else {
      download_link.download = caption + "-" + ts + ".csv";
    }
    document.body.appendChild(download_link);
    download_link.click();
    document.body.removeChild(download_link);
  });
};

//End Export

//Start Datalist Filter Remove

jQuery(function ($) {
  function tog(v) {
    return v ? "addClass" : "removeClass";
  }
  $(document)
    .on("input", ".clearable", function () {
      $(this)[tog(this.value)]("x");
    })
    .on("mousemove", ".x", function (e) {
      $(this)[
        tog(
          this.offsetWidth - 18 < e.clientX - this.getBoundingClientRect().left
        )
      ]("onX");
    })
    .on("click", ".onX", function () {
      $(".clearable").val("");
      $(this)
        .removeClass("x onX")
        .val("");
    });
});

//End Datalist Filter Remove

//Start Reports Function
function adminRangeFilter(value) {
  var rangeFilterValue = $("#adminRangeFilter").val();
  if (rangeFilterValue == 1) {
    $("#startDate").removeAttr("readonly", "readonly");
    $("#endDate").removeAttr("readonly", "readonly");
    $("#btnDate").removeAttr("disabled", "disabled");

    $("#startMonth").attr("readonly", "readonly");
    $("#endMonth").attr("readonly", "readonly");
    $("#btnMonth").attr("disabled", "disabled");

    $("#startYear").attr("disabled", "disabled");
    $("#endYear").attr("disabled", "disabled");
    $("#btnYear").attr("disabled", "disabled");
  } else if (rangeFilterValue == 2) {
    $("#startDate").attr("readonly", "readonly");
    $("#endDate").attr("readonly", "readonly");
    $("#btnDate").attr("disabled", "disabled");

    $("#startMonth").removeAttr("readonly", "readonly");
    $("#endMonth").removeAttr("readonly", "readonly");
    $("#btnMonth").removeAttr("disabled", "disabled");

    $("#startYear").attr("disabled", "disabled");
    $("#endYear").attr("disabled", "disabled");
    $("#btnYear").attr("disabled", "disabled");
  } else if (rangeFilterValue == 3) {
    $("#startDate").attr("readonly", "readonly");
    $("#endDate").attr("readonly", "readonly");
    $("#btnDate").attr("disabled", "disabled");

    $("#startMonth").attr("readonly", "readonly");
    $("#endMonth").attr("readonly", "readonly");
    $("#btnMonth").attr("disabled", "disabled");

    $("#startYear").removeAttr("disabled", "disabled");
    $("#endYear").removeAttr("disabled", "disabled");
    $("#btnYear").removeAttr("disabled", "disabled");
  }
}


function deleteRowMasterTable(m, id, tableName) {
  var baseUrl = $("#url").val();
  $.ajax({
    url: "" + baseUrl + "/masd/deleteMasterTableRecord",
    type: "GET",
    data: {
      id: id,
      tableName: tableName,
      m: m
    },
    success: function (data) {
      $("#mainFunctionPage").hide();
      $("#functionAjaxResponse").html(data);
      $("#showMasterTableEditModel").modal("hide");
      location.reload();
    }
  });
}
function repostMasterTableRecords(m, id, tableName) {
  var baseUrl = $("#url").val();
  $.ajax({
    url: "" + baseUrl + "/masd/repostMasterTableRecord",
    type: "GET",
    data: {
      id: id,
      tableName: tableName,
      m: m
    },
    success: function (data) {
      $("#mainFunctionPage").hide();
      $("#functionAjaxResponse").html(data);
      $("#showMasterTableEditModel").modal("hide");
      location.reload();
    }
  });
}

function uniqueValueInTable(fieldId, tableName, dbType) {
  alert(fieldId + " - " + tableName + " - " + dbType);
}
//Start Purchase Sale Invoice Functions
function approvePurchaseSaleInvoiceVoucher(type, id, status, voucherTypeStatus) {
  var baseUrl = $("#url").val();
  if (type == 1) {
    var url = 'purchase-invoice/approvePurchaseInvoiceVoucher';
  }  else {
    var url = 'sale-invoice/approveSaleInvoiceVoucher';
  }
  $.ajax({
    url: url,  // URL of the Blade view (e.g., 'sections/_form')
    type: 'post',
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    data: { id: id, status: status, voucherTypeStatus: voucherTypeStatus },
    success: function (response) {
      if (response == 'Done') {
        $("#showDetailModelOneParamerter").modal("toggle");
        get_ajax_data();
      }
    }
  });
}

function reversePurchaseSaleInvoiceVoucher(type, id, status, voucherTypeStatus) {
  var baseUrl = $("#url").val();
  if (type == 1) {
    var url = 'purchase-invoice/reversePurchaseInvoiceVoucher';
  }  else {
    var url = 'sale-invoice/reverseSaleInvoiceVoucher';
  }
  $.ajax({
    url: url,  // URL of the Blade view (e.g., 'sections/_form')
    type: 'post',
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    data: { id: id, status: status, voucherTypeStatus: voucherTypeStatus },
    success: function (response) {
      if (response == 'Done') {
        $("#showDetailModelOneParamerter").modal("toggle");
        get_ajax_data();
      }
    }
  });
}
//End Purchase Sale Invoice Functions

//Start Finance Functions
function deleteFinanceVoucher(type, id, status, voucherTypeStatus) {
  var baseUrl = $("#url").val();
  if (type == 1) {
    var url = 'payments/deletePaymentVoucher';
  } else if (type == 3) {
    var url = 'journalvouchers/deleteJournalVoucher';
  } else {
    var url = 'receipts/deleteReceiptVoucher';
  }
  $.ajax({
    url: url,  // URL of the Blade view (e.g., 'sections/_form')
    type: 'post',
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    data: { id: id, status: status, voucherTypeStatus: voucherTypeStatus },
    success: function (response) {
      if (response == 'Done') {
        $("#showDetailModelOneParamerter").modal("toggle");
        get_ajax_data();
      }
    }
  });
}
function approveFinanceVoucher(type, id, status, voucherTypeStatus) {
  var baseUrl = $("#url").val();
  if (type == 1) {
    var url = 'payments/approvePaymentVoucher';
  } else if (type == 3) {
    var url = 'journalvouchers/approveJournalVoucher';
  } else {
    var url = 'receipts/approveReceiptVoucher';
  }
  $.ajax({
    url: url,  // URL of the Blade view (e.g., 'sections/_form')
    type: 'post',
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    data: { id: id, status: status, voucherTypeStatus: voucherTypeStatus },
    success: function (response) {
      if (response == 'Done') {
        $("#showDetailModelOneParamerter").modal("toggle");
        get_ajax_data();
      }
    }
  });
}

function rejectFinanceVoucher(type, id, status, voucherTypeStatus) {
  if (type == 1) {
    var url = 'payments/paymentVoucherRejectAndRepost';
  } else if (type == 3) {
    var url = 'journalvouchers/journalVoucherRejectAndRepost';
  } else {
    var url = 'receipts/receiptVoucherRejectAndRepost';
  }
  $.ajax({
    url: url,  // URL of the Blade view (e.g., 'sections/_form')
    type: 'post',
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    data: { id: id, status: status, voucherTypeStatus: voucherTypeStatus, value: 3 },
    success: function (response) {
      if (response == 'Done') {
        $("#showDetailModelOneParamerter").modal("toggle");
        get_ajax_data();
      }
    }
  });
}

function repostFinanceVoucher(type, id, status, voucherTypeStatus) {
  if (type == 1) {
    var url = 'payments/paymentVoucherRejectAndRepost';
  } else if (type == 3) {
    var url = 'journalvouchers/journalVoucherRejectAndRepost';
  } else {
    var url = 'receipts/receiptVoucherRejectAndRepost';
  }
  $.ajax({
    url: url,  // URL of the Blade view (e.g., 'sections/_form')
    type: 'post',
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    data: { id: id, status: status, voucherTypeStatus: voucherTypeStatus, value: 1 },
    success: function (response) {
      if (response == 'Done') {
        $("#showDetailModelOneParamerter").modal("toggle");
        get_ajax_data();
      }
    }
  });
}

function inactiveFinanceVoucher(type, id, status, voucherTypeStatus) {
  if (type == 1) {
    var url = 'payments/paymentVoucherActiveAndInactive';
  } else if (type == 3) {
    var url = 'journalvouchers/journalVoucherActiveAndInactive';
  } else {
    var url = 'receipts/receiptVoucherActiveAndInactive';
  }
  $.ajax({
    url: url,  // URL of the Blade view (e.g., 'sections/_form')
    type: 'post',
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    data: { id: id, status: status, voucherTypeStatus: voucherTypeStatus, value: 2 },
    success: function (response) {
      if (response == 'Done') {
        $("#showDetailModelOneParamerter").modal("toggle");
        get_ajax_data();
      }
    }
  });
}

function activeFinanceVoucher(type, id, status, voucherTypeStatus) {
  if (type == 1) {
    var url = 'payments/paymentVoucherActiveAndInactive';
  } else if (type == 3) {
    var url = 'journalvouchers/journalVoucherActiveAndInactive';
  } else {
    var url = 'receipts/receiptVoucherActiveAndInactive';
  }
  $.ajax({
    url: url,  // URL of the Blade view (e.g., 'sections/_form')
    type: 'post',
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    data: { id: id, status: status, voucherTypeStatus: voucherTypeStatus, value: 1 },
    success: function (response) {
      if (response == 'Done') {
        $("#showDetailModelOneParamerter").modal("toggle");
        get_ajax_data();
      }
    }
  });
}
function reverseFinanceVoucher(type, id, status, voucherTypeStatus) {
  var baseUrl = $("#url").val();
  if (type == 1) {
    var url = ''+baseUrl+'/finance/payments/reversePaymentVoucher';
  } else if (type == 3) {
    var url = ''+baseUrl+'/finance/journalvouchers/reverseJournalVoucher';
  } else {
    var url = ''+baseUrl+'/finance/receipts/reverseReceiptVoucher';
  }
  $.ajax({
    url: url,  // URL of the Blade view (e.g., 'sections/_form')
    type: 'post',
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    data: { id: id, status: status, voucherTypeStatus: voucherTypeStatus },
    success: function (response) {
      if (response == 'Done') {
        $("#showDetailModelOneParamerter").modal("toggle");
        get_ajax_data();
      }
    }
  });
}
//End Finance Functions

//Start Purchase Functions

function approvePurchaseVoucher(type, id, status, voucherTypeStatus) {
  if (type == 1) {
    var url = 'purchase-orders/approvePurchaseOrderVoucher';
  } else {
    var url = 'good-receipt-notes/approveGoodReceiptNoteVoucher';
  }
  $.ajax({
    url: url,  // URL of the Blade view (e.g., 'sections/_form')
    type: 'post',
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    data: { id: id, status: status, voucherTypeStatus: voucherTypeStatus },
    success: function (response) {
      if (response == 'Done') {
        $("#showDetailModelOneParamerter").modal("toggle");
        get_ajax_data();
      }
    }
  });
}

function rejectPurchaseVoucher(type, id, status, voucherTypeStatus) {
  if (type == 1) {
    var url = 'purchase-orders/purchaseOrderVoucherRejectAndRepost';
  } else {
    var url = 'good-receipt-notes/goodReceiptNoteVoucherRejectAndRepost';
  }
  $.ajax({
    url: url,  // URL of the Blade view (e.g., 'sections/_form')
    type: 'post',
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    data: { id: id, status: status, voucherTypeStatus: voucherTypeStatus, value: 3 },
    success: function (response) {
      if (response == 'Done') {
        $("#showDetailModelOneParamerter").modal("toggle");
        get_ajax_data();
      }
    }
  });
}

function repostPurchaseVoucher(type, id, status, voucherTypeStatus) {
  if (type == 1) {
    var url = 'purchase-orders/purchaseOrderVoucherRejectAndRepost';
  } else {
    var url = 'good-receipt-notes/goodReceiptNoteVoucherRejectAndRepost';
  }
  $.ajax({
    url: url,  // URL of the Blade view (e.g., 'sections/_form')
    type: 'post',
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    data: { id: id, status: status, voucherTypeStatus: voucherTypeStatus, value: 1 },
    success: function (response) {
      if (response == 'Done') {
        $("#showDetailModelOneParamerter").modal("toggle");
        get_ajax_data();
      }
    }
  });
}

function inactivePurchaseVoucher(type, id, status, voucherTypeStatus) {
  if (type == 1) {
    var url = 'purchase-orders/purchaseOrderVoucherActiveAndInactive';
  } else {
    var url = 'good-receipt-notes/goodReceiptNoteVoucherActiveAndInactive';
  }
  $.ajax({
    url: url,  // URL of the Blade view (e.g., 'sections/_form')
    type: 'post',
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    data: { id: id, status: status, voucherTypeStatus: voucherTypeStatus, value: 2 },
    success: function (response) {
      if (response == 'Done') {
        $("#showDetailModelOneParamerter").modal("toggle");
        get_ajax_data();
      }
    }
  });
}

function activePurchaseVoucher(type, id, status, voucherTypeStatus) {
  if (type == 1) {
    var url = 'purchase-orders/purchaseOrderVoucherActiveAndInactive';
  } else {
    var url = 'good-receipt-notes/goodReceiptNoteVoucherActiveAndInactive';
  }
  $.ajax({
    url: url,  // URL of the Blade view (e.g., 'sections/_form')
    type: 'post',
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    data: { id: id, status: status, voucherTypeStatus: voucherTypeStatus, value: 1 },
    success: function (response) {
      if (response == 'Done') {
        $("#showDetailModelOneParamerter").modal("toggle");
        get_ajax_data();
      }
    }
  });
}

// material request and store challan

function approveMaterialVoucher(type, id, status, voucherTypeStatus) {
  if (type == 1) {
    var url = 'material-requests/approveMaterialRequestVoucher';
  } else {
    var url = 'good-receipt-notes/approveGoodReceiptNoteVoucher';
  }
  $.ajax({
    url: url,  // URL of the Blade view (e.g., 'sections/_form')
    type: 'post',
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    data: { id: id, status: status, voucherTypeStatus: voucherTypeStatus },
    success: function (response) {
      if (response == 'Done') {
        $("#showDetailModelOneParamerter").modal("toggle");
        get_ajax_data();
      }
    }
  });
}
function rejectMaterialVoucher(type, id, status, voucherTypeStatus) {
  if (type == 1) {
    var url = 'material-requests/materialRequestVoucherRejectAndRepost';
  } else {
    var url = 'good-receipt-notes/goodReceiptNoteVoucherRejectAndRepost';
  }
  $.ajax({
    url: url,  // URL of the Blade view (e.g., 'sections/_form')
    type: 'post',
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    data: { id: id, status: status, voucherTypeStatus: voucherTypeStatus, value: 3 },
    success: function (response) {
      if (response == 'Done') {
        $("#showDetailModelOneParamerter").modal("toggle");
        get_ajax_data();
      }
    }
  });
}
function repostMaterialVoucher(type, id, status, voucherTypeStatus) {
  if (type == 1) {
    var url = 'material-requests/materialRequestVoucherRejectAndRepost';
  } else {
    var url = 'good-receipt-notes/goodReceiptNoteVoucherRejectAndRepost';
  }
  $.ajax({
    url: url,  // URL of the Blade view (e.g., 'sections/_form')
    type: 'post',
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    data: { id: id, status: status, voucherTypeStatus: voucherTypeStatus, value: 1 },
    success: function (response) {
      if (response == 'Done') {
        $("#showDetailModelOneParamerter").modal("toggle");
        get_ajax_data();
      }
    }
  });
}

function inactiveMaterialVoucher(type, id, status, voucherTypeStatus) {
  if (type == 1) {
    var url = 'material-requests/materialRequestVoucherActiveAndInactive';
  } else {
    var url = 'good-receipt-notes/goodReceiptNoteVoucherActiveAndInactive';
  }
  $.ajax({
    url: url,  // URL of the Blade view (e.g., 'sections/_form')
    type: 'post',
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    data: { id: id, status: status, voucherTypeStatus: voucherTypeStatus, value: 2 },
    success: function (response) {
      if (response == 'Done') {
        $("#showDetailModelOneParamerter").modal("toggle");
        get_ajax_data();
      }
    }
  });
}

function activeMaterialVoucher(type, id, status, voucherTypeStatus) {
  if (type == 1) {
    var url = 'material-requests/materialRequestVoucherActiveAndInactive';
  } else {
    var url = 'good-receipt-notes/goodReceiptNoteVoucherActiveAndInactive';
  }
  $.ajax({
    url: url,  // URL of the Blade view (e.g., 'sections/_form')
    type: 'post',
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    data: { id: id, status: status, voucherTypeStatus: voucherTypeStatus, value: 1 },
    success: function (response) {
      if (response == 'Done') {
        $("#showDetailModelOneParamerter").modal("toggle");
        get_ajax_data();
      }
    }
  });
}
//End Purchase Functions


//Start Store Functions

//Start Sale Functions

function approveDirectSaleInvoiceVoucher(type, id, status, voucherTypeStatus) {
  if (type == 1) {
    var url = 'direct-sale-invoices/approveDirectSaleInvoiceVoucher';
  }
  $.ajax({
    url: url,  // URL of the Blade view (e.g., 'sections/_form')
    type: 'post',
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    data: { id: id, status: status, voucherTypeStatus: voucherTypeStatus },
    success: function (response) {
      if (response == 'Done') {
        $("#showDetailModelOneParamerter").modal("toggle");
        get_ajax_data();
      }
    }
  });
}

function rejectDirectSaleInvoiceVoucher(type, id, status, voucherTypeStatus) {
  if (type == 1) {
    var url = 'direct-sale-invoices/directSaleInvoiceVoucherRejectAndRepost';
  }
  $.ajax({
    url: url,  // URL of the Blade view (e.g., 'sections/_form')
    type: 'post',
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    data: { id: id, status: status, voucherTypeStatus: voucherTypeStatus, value: 3 },
    success: function (response) {
      if (response == 'Done') {
        $("#showDetailModelOneParamerter").modal("toggle");
        get_ajax_data();
      }
    }
  });
}

function repostDirectSaleInvoiceVoucher(type, id, status, voucherTypeStatus) {
  if (type == 1) {
    var url = 'direct-sale-invoices/directSaleInvoiceVoucherRejectAndRepost';
  }
  $.ajax({
    url: url,  // URL of the Blade view (e.g., 'sections/_form')
    type: 'post',
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    data: { id: id, status: status, voucherTypeStatus: voucherTypeStatus, value: 1 },
    success: function (response) {
      if (response == 'Done') {
        $("#showDetailModelOneParamerter").modal("toggle");
        get_ajax_data();
      }
    }
  });
}

function inactiveDirectSaleInvoiceVoucher(type, id, status, voucherTypeStatus) {
  if (type == 1) {
    var url = 'direct-sale-invoices/directSaleInvoiceVoucherActiveAndInactive';
  }
  $.ajax({
    url: url,  // URL of the Blade view (e.g., 'sections/_form')
    type: 'post',
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    data: { id: id, status: status, voucherTypeStatus: voucherTypeStatus, value: 2 },
    success: function (response) {
      if (response == 'Done') {
        $("#showDetailModelOneParamerter").modal("toggle");
        get_ajax_data();
      }
    }
  });
}

function activeDirectSaleInvoiceVoucher(type, id, status, voucherTypeStatus) {
  if (type == 1) {
    var url = 'direct-sale-invoices/directSaleInvoiceVoucherActiveAndInactive';
  }
  $.ajax({
    url: url,  // URL of the Blade view (e.g., 'sections/_form')
    type: 'post',
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    data: { id: id, status: status, voucherTypeStatus: voucherTypeStatus, value: 1 },
    success: function (response) {
      if (response == 'Done') {
        $("#showDetailModelOneParamerter").modal("toggle");
        get_ajax_data();
      }
    }
  });
}

//End Sale Functions

function approveStoreVoucher(type, id, status, voucherTypeStatus) {
  if (type == 1) {
    var url = 'transfer-notes/approveTransferNoteVoucher';
  }
  $.ajax({
    url: url,  // URL of the Blade view (e.g., 'sections/_form')
    type: 'post',
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    data: { id: id, status: status, voucherTypeStatus: voucherTypeStatus },
    success: function (response) {
      if (response == 'Done') {
        $("#showDetailModelOneParamerter").modal("toggle");
        get_ajax_data();
      }
    }
  });
}

function rejectStoreVoucher(type, id, status, voucherTypeStatus) {
  if (type == 1) {
    var url = 'transfer-notes/transferNoteVoucherRejectAndRepost';
  }
  $.ajax({
    url: url,  // URL of the Blade view (e.g., 'sections/_form')
    type: 'post',
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    data: { id: id, status: status, voucherTypeStatus: voucherTypeStatus, value: 3 },
    success: function (response) {
      if (response == 'Done') {
        $("#showDetailModelOneParamerter").modal("toggle");
        get_ajax_data();
      }
    }
  });
}

function repostStoreVoucher(type, id, status, voucherTypeStatus) {
  if (type == 1) {
    var url = 'transfer-notes/transferNoteVoucherRejectAndRepost';
  }
  $.ajax({
    url: url,  // URL of the Blade view (e.g., 'sections/_form')
    type: 'post',
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    data: { id: id, status: status, voucherTypeStatus: voucherTypeStatus, value: 1 },
    success: function (response) {
      if (response == 'Done') {
        $("#showDetailModelOneParamerter").modal("toggle");
        get_ajax_data();
      }
    }
  });
}

function inactiveStoreVoucher(type, id, status, voucherTypeStatus) {
  if (type == 1) {
    var url = 'transfer-notes/transferNoteVoucherActiveAndInactive';
  }
  $.ajax({
    url: url,  // URL of the Blade view (e.g., 'sections/_form')
    type: 'post',
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    data: { id: id, status: status, voucherTypeStatus: voucherTypeStatus, value: 2 },
    success: function (response) {
      if (response == 'Done') {
        $("#showDetailModelOneParamerter").modal("toggle");
        get_ajax_data();
      }
    }
  });
}

function activeStoreVoucher(type, id, status, voucherTypeStatus) {
  if (type == 1) {
    var url = 'transfer-notes/transferNoteVoucherActiveAndInactive';
  }
  $.ajax({
    url: url,  // URL of the Blade view (e.g., 'sections/_form')
    type: 'post',
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    data: { id: id, status: status, voucherTypeStatus: voucherTypeStatus, value: 1 },
    success: function (response) {
      if (response == 'Done') {
        $("#showDetailModelOneParamerter").modal("toggle");
        get_ajax_data();
      }
    }
  });
}


function inactiveReturnVoucher(id) {
  
  var url = `return-good-receipt-notes/destroy/${id}`;
  $.ajax({
    url: url, 
    type: 'post',
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    data: { id: id },
    success: function (response) {
        $("#showDetailModelOneParamerter").modal("toggle");
        get_ajax_data();
    }
  });
}
function activeReturnVoucher(id) {
  
  var url = `return-good-receipt-notes/status/${id}`;
  $.ajax({
    url: url, 
    type: 'post',
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    data: { id: id },
    success: function (response) {
        $("#showDetailModelOneParamerter").modal("toggle");
        get_ajax_data();
    }
  });
}
function rejectReturnVoucher(id) {
  var url = `return-good-receipt-notes/returnGoodReceiptNoteVoucherReject/${id}`;
  $.ajax({
    url: url, 
    type: 'post',
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    data: { id: id },
    success: function (response) {
        $("#showDetailModelOneParamerter").modal("toggle");
        get_ajax_data();
    }
  });
}
function repostReturnVoucher(id) {
  var url = `return-good-receipt-notes/returnGoodReceiptNoteVoucherRepost/${id}`;
  $.ajax({
    url: url, 
    type: 'post',
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    data: { id: id },
    success: function (response) {
        $("#showDetailModelOneParamerter").modal("toggle");
        get_ajax_data();
    }
  });
}
function approveReturnVoucher(id) {
  var url = `return-good-receipt-notes/approveReturnGoodReceiptNoteVoucher`;
  $.ajax({
    url: url, 
    type: 'post',
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    data: { id: id },
    success: function (response) {
        $("#showDetailModelOneParamerter").modal("toggle");
        get_ajax_data();
    }
  });
}


//End Store Functions

function get_ajax_data() {
  var form = $('#list_data');
  var actionUrl = form.attr('action');
  $('#data').html('<tr><td colspan="100"><div class="preloader-list"><div class="preloader-list-logo"></div></div></td></tr>');
  var pages = $('#pages').val();
  $.ajax({
    type: "get",
    url: actionUrl,
    data: form.serialize(), // serializes the form's elements.
    async: true,
    cache: false,
    success: function (data) {
      $('#data').html(data);
      //$('select').select2();
    }
  });
}

function get_ajax_data_two(tableId, columns) {
  var form = $('#list_data');
  var actionUrl = form.attr('action');
  var tableElement = $(`#${tableId}`);

  // Destroy the existing DataTable instance if it exists
  if ($.fn.dataTable.isDataTable(tableElement)) {
    tableElement.DataTable().destroy();
    tableElement.empty(); // Clear table content
  }
  tableElement.DataTable({
    processing: true,
    serverSide: true,
    ajax: {
      url: actionUrl,
      type: 'GET',
      data: function (d) {
        // Serialize form data and include it in the request
        var formData = form.serializeArray();
        $.each(formData, function (i, field) {
          d[field.name] = field.value;
        });
      },
      dataSrc: 'data' // Specify where the data is in the server response
    },
    columns: columns
  });
}




function inactiveReturnSale(id) {
  
  var url = `sales-return/destroy/${id}`;
  $.ajax({
    url: url, 
    type: 'post',
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    data: { id: id },
    success: function (response) {
        $("#showDetailModelOneParamerter").modal("toggle");
        get_ajax_data();
    }
  });
}
function activeReturnSale(id) {
  
  var url = `sales-return/status/${id}`;
  $.ajax({
    url: url, 
    type: 'post',
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    data: { id: id },
    success: function (response) {
        $("#showDetailModelOneParamerter").modal("toggle");
        get_ajax_data();
    }
  });
}
function rejectReturnSale(id) {
  var url = `sales-return/returnSaleReject/${id}`;
  $.ajax({
    url: url, 
    type: 'post',
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    data: { id: id },
    success: function (response) {
        $("#showDetailModelOneParamerter").modal("toggle");
        get_ajax_data();
    }
  });
}
function repostReturnSale(id) {
  var url = `sales-return/returnSaleRepost/${id}`;
  $.ajax({
    url: url, 
    type: 'post',
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    data: { id: id },
    success: function (response) {
        $("#showDetailModelOneParamerter").modal("toggle");
        get_ajax_data();
    }
  });
}
function approveReturnSale(id) {
  var url = `sales-return/returnSaleApprove/${id}`;
  $.ajax({
    url: url, 
    type: 'post',
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    data: { id: id },
    success: function (response) {
        $("#showDetailModelOneParamerter").modal("toggle");
        get_ajax_data();
    }
  });
}
//End Reports Function
