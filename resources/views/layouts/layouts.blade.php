@php
    use App\Helpers\CommonHelper;
    $loginCnic = Session::get('login_cnic');
@endphp
<!DOCTYPE html>
<html>

<head>
    <title>Bidec Accounts</title>
    <!-- BOOTSTRAP CORE STYLE  -->
    <link href="{{ URL::asset('assets/css/bootstrap.css') }}" rel="stylesheet" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- FONT AWESOME STYLE  -->
    <!--<link href="{{ URL::asset('assets/css/font-awesome.css') }}" rel="stylesheet" />-->
    <!-- CUSTOM STYLE  -->
    <link href="{{ URL::asset('assets/css/style.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('assets/css/main.css') }}" rel="stylesheet" />
    <!-- GOOGLE FONT -->
    <link href="{{ URL::asset('assets/css/fa.css') }}" rel='stylesheet' type='text/css' />
    <link href="{{ URL::asset('assets/css/arrows.css') }}" rel='stylesheet' type='text/css' />

    <script src="{{ URL::asset('assets/js/jquery-1.10.2.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/js/bootstrap-select.min.js"></script> -->
    <!-- BOOTSTRAP SCRIPTS  -->
    <script src="{{ URL::asset('assets/js/bootstrap.js') }}"></script>
    <link href="{{ URL::asset('assets/css/font-awesome.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/MegaMenu/demo.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/MegaMenu/webslidemenu.css') }}" rel="stylesheet">
    <!-- Toastr CSS -->
    <!-- <link rel="stylesheet" href="{{ asset('css/app.css') }}"> -->
    <!-- Laravel Mix compiled CSS -->
    <link rel="stylesheet" href="{{ asset('node_modules/toastr/build/toastr.min.css') }}">
    <!-- Toastr JS -->
    <!-- <script src="{{ asset('js/app.js') }}"></script>  -->
    <!-- Laravel Mix compiled JS -->
    <script src="{{ asset('node_modules/toastr/build/toastr.min.js') }}"></script>
    <!-- Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet">


    <!-- custom-css-end -->
    <style>
        .d-flex {
            display: flex;
        }

        .justify-center {
            justify-content: center;
        }

        .d-none {
            display: none;
        }

        .modal-body {
            position: relative;
            padding: 15px;
            overflow-y: auto;
            height: 550px;
        }
    </style>
    <!-- notification css-->
    <style>
        ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .notification-drop {
            font-family: 'Ubuntu', sans-serif;
            color: #444;
        }

        .notification-drop .item {
            padding: 10px;
            font-size: 18px;
            position: relative;
            border-bottom: 1px solid #ddd;
        }

        .notification-drop .item:hover {
            cursor: pointer;
        }

        .notification-drop .item i {
            margin-left: 10px;
        }

        .notification-drop .item ul {
            display: none;
            position: absolute;
            top: 100%;
            background: #fff;
            left: -250px;
            right: 0;
            z-index: 1;
            border-top: 1px solid #ddd;
            width: 353px;
            ;
        }

        .notification-drop .item ul li {
            font-size: 12px;
            padding: 15px 0 15px 10px;
        }

        .notification-drop .item ul li:hover {
            background: #ddd;
            color: rgba(0, 0, 0, 0.8);
        }

        @media screen and (min-width: 500px) {
            .notification-drop {
                display: flex;
                justify-content: flex-end;
            }

            .notification-drop .item {
                border: none;
            }
        }



        .notification-bell {
            font-size: 20px;
        }

        .btn__badge {
            background: #FF5D5D;
            color: white;
            font-size: 12px;
            position: absolute;
            top: 0;
            right: 0px;
            padding: 3px 10px;
            border-radius: 50%;
        }

        .pulse-button {
            box-shadow: 0 0 0 0 rgba(255, 0, 0, 0.5);
            -webkit-animation: pulse 1.5s infinite;
        }

        .pulse-button:hover {
            -webkit-animation: none;
        }

        @-webkit-keyframes pulse {
            0% {
                -moz-transform: scale(0.9);
                -ms-transform: scale(0.9);
                -webkit-transform: scale(0.9);
                transform: scale(0.9);
            }

            70% {
                -moz-transform: scale(1);
                -ms-transform: scale(1);
                -webkit-transform: scale(1);
                transform: scale(1);
                box-shadow: 0 0 0 50px rgba(255, 0, 0, 0);
            }

            100% {
                -moz-transform: scale(0.9);
                -ms-transform: scale(0.9);
                -webkit-transform: scale(0.9);
                transform: scale(0.9);
                box-shadow: 0 0 0 0 rgba(255, 0, 0, 0);
            }
        }

        .notification-text {
            font-size: 14px;
            font-weight: bold;
        }

        .notification-text span {
            float: right;
        }

        iframe#\:1\.container {
            display: none;
        }

        .VIpgJd-ZVi9od-l4eHX-hSRGPd {
            display: none;
        }
    </style>
    @yield('custom-css-end')

    <!-- custom-css-end -->

    <script src="{{ URL::asset('assets/MegaMenu/webslidemenu.js') }}"></script>

    <script src="{{ URL::asset('assets/custom/js/jquery.cookie.min.js') }}"></script>

    <link rel="stylesheet" id="changeStyle" type="text/css" href="{{ URL::asset('assets/custom/css/color-one.css') }}">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="{{ URL::asset('assets/custom/js/multi-select-js-library.js') }}"></script>
    <link href="{{ URL::asset('assets/custom/css/multi-select-css-library.css') }}" rel="stylesheet">
    <script src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/3/jquery.inputmask.bundle.js"></script>
    <script src="{{ URL::asset('assets/custom/js/customlayout.js') }}"></script>
    <script src="{{ URL::asset('assets/custom/js/customMainFunction.js') }}"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css"
        href="https://cdn.datatables.net/fixedheader/3.1.9/css/fixedHeader.dataTables.min.css">
    <script src="{{ URL::asset('assets/select2/select2.full.min.js') }}"></script>
    <link href="{{ URL::asset('assets/select2/select2.css') }}" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.4.1/jspdf.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dom-to-image/2.6.0/dom-to-image.min.js"
        integrity="sha256-c9vxcXyAG4paArQG3xk6DjyW/9aHxai2ef9RpMWO44A=" crossorigin="anonymous"></script>
    <script src="{{ URL::asset('assets/tableHTMLExport.js') }}"></script>
</head>

<body>
    @if (empty($loginCnic))
        @include('includes._clientNavigation')
    @else
        @include('includes._parentNavigation')
    @endif

    <div class="container-fluid">
        @yield('content')
    </div>
    @if (empty($loginCnic))
        <script>
            function filterSchoolMainScrreen() {
                var filterSchoolMainScreenId = $('#filter_school_main_screen').val();
                var accType = '{{ Auth::user()->acc_type }}';
                $.ajax({
                    type: "GET",
                    url: '/loadCompanies',
                    data: {
                        filterSchoolMainScreenId: filterSchoolMainScreenId,
                        accType: accType
                    }, // Serialize form data
                    async: true,
                    cache: false,
                    success: function(data) {
                        $('#mainScreenFilterSchool').html(data); // Update table content
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching data:', error);
                    }
                });
            }
        </script>
        <?php
        $accType = Auth::user()->acc_type;
        ?>
        @if (Route::current()->getName() == 'companies.create')
            :
            <div id="companyListModel" class="modal fade in" role="dialog" aria-hidden="false">
            @elseif(Route::current()->getName() == 'companies.index')
                <div id="companyListModel" class="modal fade in" role="dialog" aria-hidden="false">
                @elseif(Route::current()->getName() == 'locations.create')
                    <div id="companyListModel" class="modal fade in" role="dialog" aria-hidden="false">
                    @elseif(Route::current()->getName() == 'locations.index')
                        <div id="companyListModel" class="modal fade in" role="dialog" aria-hidden="false">
                        @elseif(Session::get('company_id') == '' && Session::get('company_location_id') == '')
                            <div id="companyListModel" class="modal fade in" role="dialog" aria-hidden="false"
                                style="display: block;">
                            @else
                                <div id="companyListModel" class="modal fade in" role="dialog" aria-hidden="false">
        @endif
        <div class="modal-dialog modalWidth dply">
            <!-- Modal content-->
            <div class="model-n modal-content">

                <div class="modal-body">
                    <div class="mdel-bx">
                        <div class="model-logo"><img style="width:15%;" src="{{ CommonHelper::displaySchoolLogo() }}">
                            <h4 class="modal-title">Select The Company</h4>
                        </div>
                        <div class="row">
                            <div class="row">
                                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                                    <?php 
                                                                if ($accType == 'client') {
                                                            ?>
                                    <a class="btn btn-xs btn-primary" href="{{ url('companies/create') }}">Add New
                                        Company</a>
                                    <a class="btn btn-xs btn-success" href="{{ url('companies') }}">Company List</a>
                                    <a class="btn btn-xs btn-primary" href="{{ url('locations/create') }}">Add
                                        Company Location</a>
                                    <a class="btn btn-xs btn-success" href="{{ url('locations') }}">Company Location
                                        List</a>
                                    <?php }else if($accType == 'owner'){?>

                                    <a class="btn btn-xs btn-primary" href="{{ url('locations/create') }}">Add
                                        Company Location</a>
                                    <a class="btn btn-xs btn-success" href="{{ url('locations') }}">Company Location
                                        List</a>
                                    <?php 
                                                                } else {
                                                            ?>
                                    &nbsp;
                                    <?php    
                                                                }
                                                            ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div id="loadMainSection"></div>
                                </div>
                            </div>
                            <a href="{{ url('/signout') }}" class="btn-b">Sign Out</a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        </div>

    @endif
    <div class="modal fade" id="showDetailModelOneParamerter">
        <div class=" model-outer">
            <div class="modal-content ">
                <div class="loadm-bx ">
                    <div class="model-logo">
                        <img class="logo_mm" src="{{ CommonHelper::displaySchoolLogo() }}">
                        <h4><span class="modalTitle"></span></h4>
                        <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal"><i
                                class="fal fa-times"></i></button>
                    </div>
                    <div class="modal-body"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="showFormModelForDataInsert">
        <div class=" model-outer">
            <div class="modal-content ">
                <div class="loadm-bx ">
                    <div class="model-logo">
                        <img class="logo_mm" src="{{ CommonHelper::displaySchoolLogo() }}">
                        <h4><span class="modalTitle"></span></h4>
                        <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal"><i
                                class="fal fa-times"></i></button>
                    </div>
                    <div class="modal-body"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="showFormModelForCheckStock">
        <div class=" model-outer">
            <div class="modal-content ">
                <div class="loadm-bx ">
                    <div class="model-logo">
                        <img class="logo_mm" src="{{ CommonHelper::displaySchoolLogo() }}">
                        <h4><span class="modalTitle"></span></h4>
                        <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal"><i
                                class="fal fa-times"></i></button>
                    </div>
                    <div class="modal-body"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openTraceStockModel() {
            var pageType = true;
            var parentCode = true;
            var modalName = 'Trace Stock';
            $.ajax({
                url: '<?php echo url('/'); ?>/stocks/openTraceStockModel',
                type: "GET",
                data: {
                    pageType: pageType,
                    parentCode: parentCode
                },
                success: function(data) {
                    jQuery('#showFormModelForCheckStock').modal('show', {
                        backdrop: 'false'
                    });
                    //jQuery('#showMasterTableEditModel').modal('show', {backdrop: 'true'});
                    jQuery('#showFormModelForCheckStock .modalTitle').html(modalName);
                    jQuery('#showFormModelForCheckStock .modal-body').html(
                        '<div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div></div>'
                    );
                    setTimeout(function() {
                        jQuery('#showFormModelForCheckStock .modal-body').html(data);
                    }, 1000);
                }
            });
        }

        function loadCompanies() {
            var accType = '<?php echo $accType; ?>';
            $.ajax({
                url: '{{ url('/loadCompanies') }}', // Route to handle fetching Companies
                type: 'GET',
                data: {
                    filterSchoolMainScreenId: '',
                    accType: accType
                },
                success: function(response) {
                    $('#loadMainSection').html(response);
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching Companies:', error);
                }
            });
        }
        loadCompanies();

        function loadLocations(companyId) {
            var accType = '<?php echo $accType; ?>';
            $.ajax({
                url: '{{ url('/loadLocations') }}', // Route to handle fetching locations
                type: 'GET',
                data: {
                    company_id: companyId,
                    accType: accType
                },
                success: function(response) {
                    $('#loadMainSection').html(response);
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching locations:', error);
                }
            });
        }

        function setCampusAndRedirect(companyId, campusId, companyName, companyCode) {
            const href =
                `{{ url('set_user_db_id') }}?company_id=${companyId}&company_name=${companyName}&company_code=${companyCode}&company_location_id=${campusId}`;
            window.location.href = href; // Redirect with the complete URL
        }
    </script>
    <script>
        function showDetailModelOneParamerter(url, id, modalName) {
            var pageType = true;
            var parentCode = true;
            $.ajax({
                url: '<?php echo url('/'); ?>/' + url + '',
                type: "GET",
                data: {
                    id: id,
                    pageType: pageType,
                    parentCode: parentCode
                },
                success: function(data) {
                    jQuery('#showDetailModelOneParamerter').modal('show', {
                        backdrop: 'false'
                    });
                    //jQuery('#showMasterTableEditModel').modal('show', {backdrop: 'true'});
                    jQuery('#showDetailModelOneParamerter .modalTitle').html(modalName);
                    jQuery('#showDetailModelOneParamerter .modal-body').html(
                        '<div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div></div>'
                    );
                    setTimeout(function() {
                        jQuery('#showDetailModelOneParamerter .modal-body').html(data);
                    }, 1000);
                }
            });
        }

        function showFormModelForDataInsert(param) {
            var url = param.url; // 'brands.create'
            var type = param.type; // 'model'
            var optionName = param.optionName; // 'brands'
            var modalName = '-';
            var columnId = param.columnId;
            $.ajax({
                url: '<?php echo url('/'); ?>/' + url + '',
                type: "GET",
                data: {
                    type: type,
                    optionName: optionName,
                    columnId: columnId
                },
                success: function(data) {
                    jQuery('#showFormModelForDataInsert').modal('show', {
                        backdrop: 'false'
                    });
                    //jQuery('#showMasterTableEditModel').modal('show', {backdrop: 'true'});
                    jQuery('#showFormModelForDataInsert .modalTitle').html(modalName);
                    jQuery('#showFormModelForDataInsert .modal-body').html(
                        '<div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div></div>'
                    );

                    setTimeout(function() {
                        jQuery('#showFormModelForDataInsert .modal-body').html(data);
                        $('.modal-body > div').removeClass('well_N');
                    }, 1000);
                }
            });
        }

        // $('body').on('click', '#inactive-record', function() {
        //     var userURL = $(this).data('url');
        //     //alert(userURL);
        //     //return 'Testing';

        //     var trObj = $(this);
        //     if (confirm("Are you sure you want to remove this?") == true) {
        //         $.ajax({
        //             url: userURL,
        //             type: 'POST',
        //             dataType: 'json',
        //             data: {
        //                 "_token": "{{ csrf_token() }}",
        //             },
        //             success: function(data) {
        //                 if (typeof(data.success) == 'undefined') {
        //                     alert(data.catchError);
        //                     return;
        //                 }
        //                 alert(data.success);
        //                 $("#filter-button").click();
        //                 get_ajax_data();
        //             }
        //         });
        //     }
        // });
  // 🟢 ACTIVATE RECORD
// 🟢 ACTIVATE RECORD
$('body').on('click', '#active-record', function () {
    var userURL = $(this).data('url');

    Swal.fire({
        title: "Activate Record?",
        text: "Are you sure you want to activate this record?",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#28a745",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Yes, Activate"
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: userURL,
                type: 'POST',
                dataType: 'json',
                data: { "_token": "{{ csrf_token() }}" },
                success: function (data) {
                    if (!data.success) {
                        Swal.fire("Error", data.catchError || "Something went wrong!", "error");
                        return;
                    }

                    Swal.fire({
                        icon: "success",
                        title: "Activated!",
                        text: data.success,
                        timer: 1500,
                        showConfirmButton: false
                    });

                    // Refresh according to filter
                    get_ajax_data();
                },
                error: function () {
                    Swal.fire("Error", "Something went wrong!", "error");
                }
            });
        }
    });
});


// 🔴 INACTIVATE RECORD
$('body').on('click', '#inactive-record', function () {
    var userURL = $(this).data('url');

    Swal.fire({
        title: "Deactivate Record?",
        text: "Are you sure you want to deactivate this record?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Yes, Deactivate"
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: userURL,
                type: 'POST',
                dataType: 'json',
                data: { "_token": "{{ csrf_token() }}" },
                success: function (data) {
                    if (!data.success) {
                        Swal.fire("Error", data.catchError || "Something went wrong!", "error");
                        return;
                    }

                    Swal.fire({
                        icon: "success",
                        title: "Deactivated!",
                        text: data.success,
                        timer: 1500,
                        showConfirmButton: false
                    });

                    // ✅ Reapply filter and reload table
                    get_ajax_data();
                },
                error: function () {
                    Swal.fire("Error", "Something went wrong!", "error");
                }
            });
        }
    });
});


        // $('body').on('click', '#active-record', function() {
        //     var userURL = $(this).data('url');
        //     var trObj = $(this);
        //     if (confirm("Are you sure you want to remove this?") == true) {
        //         $.ajax({
        //             url: userURL,
        //             type: 'POST',
        //             dataType: 'json',
        //             data: {
        //                 "_token": "{{ csrf_token() }}",
        //             },
        //             success: function(data) {
        //                 if (typeof(data.success) == 'undefined') {
        //                     alert(data.catchError);
        //                     return;
        //                 }
        //                 alert(data.success);
        //                 $("#filter-button").click();
        //                 get_ajax_data();
        //             }
        //         });
        //     }
        // });


        $('body').on('click', '#unsuspended-record', function() {
            var userURL = $(this).data('url');
            //alert(userURL);
            //return 'Testing';

            var trObj = $(this);
            if (confirm("Are you sure you want to unsuspended this?") == true) {
                $.ajax({
                    url: userURL,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(data) {
                        if (typeof(data.success) == 'undefined') {
                          Swal.fire("Error!", data.catchError, "error");

                            return;
                        }
                  Swal.fire("Success!", data.success, "success");

                        $("#filter-button").click();
                    }
                });
            }
        });
        $('body').on('click', '#suspend-record', function() {
            var userURL = $(this).data('url');
            var trObj = $(this);
            if (confirm("Are you sure you want to suspend this?") == true) {
                $.ajax({
                    url: userURL,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(data) {
                        if (typeof(data.success) == 'undefined') {
                            alert(data.catchError);
                            return;
                        }
                        alert(data.success);
                        $("#filter-button").click();
                    }
                });
            }
        });
    </script>
    <link href="{{ URL::asset('assets/custom/css/customMain.css') }}" rel="stylesheet">
    <script>
        $('.select2').select2();
    </script>
    @yield('custom-js-end')
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/fixedheader/3.1.9/js/dataTables.fixedHeader.min.js"></script>
    <script src="//cdn.rawgit.com/rainabba/jquery-table2excel/1.1.0/dist/jquery.table2excel.min.js"></script>
    <!-- jQuery (required for Select2) -->
    
    <!-- Select2 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @yield('script')
    <script>
        // Display Toastr notifications
        @if (session('success_two'))
            toastr.success("{{ session('success') }}", 'Success');
        @endif

        // @if (session('error'))
        //     toastr.error("{{ session('error') }}", 'Error');
        // @endif



        @if (session('success'))
            Swal.fire({
                title: "Success!",
                text: "{{ session('success') }}",
                icon: "success",
                confirmButtonText: "OK"
            });
        @endif

        @if (session('error'))
            Swal.fire({
                title: "Error!",
                text: "{{ session('error') }}",
                icon: "error",
                confirmButtonText: "OK"
            });
        @endif

        @if (session('info'))
            toastr.info("{{ session('info') }}", 'Info');
        @endif

        @if (session('warning'))
            toastr.warning("{{ session('warning') }}", 'Warning');
        @endif

        function sum(id){
        var sum_amount = 0;
        var sum_amount2 = 0;
        $("input[class *= 'd_amount_"+id+"']").each(function(){
            sum_amount += +$(this).val();
        });
        //alert(sum_amount);
        $('#d_t_amount_'+id+'').val(parseFloat(sum_amount.toFixed(3)));

        $("input[class *= 'c_amount_"+id+"']").each(function(){
            sum_amount2 += +$(this).val();
        });
        $('#c_t_amount_'+id+'').val(parseFloat(sum_amount2.toFixed(3)));
        if ($('#d_t_amount_'+id+'').val() != $('#c_t_amount_'+id+'').val()){
            $('.btnSubmit').prop('disabled', true);
            $(".btnSubmit").val('Debit and Credit is not Match');
            $('#d_t_amount_'+id+'').css('background-color','#C00');
            $('#d_t_amount_'+id+'').css('color','#fff');
            $('#c_t_amount_'+id+'').css('background-color','#C00');
            $('#c_t_amount_'+id+'').css('color','#fff');
        }else{
            $('.btnSubmit').prop('disabled', false);
            $(".btnSubmit").val('Submit');
            $('#d_t_amount_'+id+'').removeAttr('style');
            $('#c_t_amount_'+id+'').removeAttr('style');

        }
        toWords(id);
    }

    var th = ['','Thousand','Million', 'Billion','Trillion'];
    var dg = ['Zero','One','Two','Three','Four', 'Five','Six','Seven','Eight','Nine'];
    var tn = ['Ten','Eleven','Twelve','Thirteen', 'Fourteen','Fifteen','Sixteen', 'Seventeen','Eighteen','Nineteen'];
    var tw = ['Twenty','Thirty','Forty','Fifty', 'Sixty','Seventy','Eighty','Ninety'];
    function toWords(id) {
        s = $('#d_t_amount_'+id+'').val();
        s = s.toString();
        s = s.replace(/[\, ]/g,'');
        if (s != parseFloat(s)) return 'not a number';
        var x = s.indexOf('.');
        if (x == -1)
            x = s.length;
        if (x > 15)
            return 'too big';
        var n = s.split('');
        var str = '';
        var sk = 0;
        for (var i=0;   i < x;  i++) {
            if ((x-i)%3==2) {
                if (n[i] == '1') {
                    str += tn[Number(n[i+1])] + ' ';
                    i++;
                    sk=1;
                } else if (n[i]!=0) {
                    str += tw[n[i]-2] + ' ';
                    sk=1;
                }
            } else if (n[i]!=0) { // 0235
                str += dg[n[i]] +' ';
                if ((x-i)%3==0) str += 'hundred ';
                sk=1;
            }
            if ((x-i)%3==1) {
                if (sk)
                    str += th[(x-i-1)/3] + ' ';
                sk=0;
            }
        }

        if (x != s.length) {
            var y = s.length;
            str += 'point ';
            for (var i=x+1; i<y; i++)
                str += dg[n[i]] +' ';
        }
        result = str.replace(/\s+/g,' ')+'Only';
        //$('#rupees').val(result);
    };

    function addSeparatorsNF(nStr, inD, outD, sep){
        nStr += '';
        var dpos = nStr.indexOf(inD);
        var nStrEnd = '';
        if (dpos != -1) {
            nStrEnd = outD + nStr.substring(dpos + 1, nStr.length);
            nStr = nStr.substring(0, dpos);
        }
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(nStr)) {
            nStr = nStr.replace(rgx, '$1' + sep + '$2');
        }
        return nStr + nStrEnd;
    }

    function mainDisable(disable,enable){
        if ($('#'+disable).val() == ""){
            $('#'+disable).attr('readonly','readonly');
            $('#'+disable).removeAttr('required','required');
            $('#'+disable).removeClass("requiredField");
            $('#'+disable).val("");
            $('#'+enable).removeAttr('readonly');
            $('#'+enable).attr('required','required');
            $('#'+enable).addClass("requiredField");
        }
    }
    </script>
</body>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    @if (session('success'))
        Swal.fire({
            title: "Success!",
            text: "{{ session('success') }}",
            icon: "success",
            confirmButtonText: "OK"
        });
    @endif

    @if (session('error'))
        Swal.fire({
            title: "Error!",
            text: "{{ session('error') }}",
            icon: "error",
            confirmButtonText: "OK"
        });
    @endif

    @if (session('warning'))
        Swal.fire({
            title: "Warning!",
            text: "{{ session('warning') }}",
            icon: "warning",
            confirmButtonText: "OK"
        });
    @endif

    @if (session('info'))
        Swal.fire({
            title: "Info!",
            text: "{{ session('info') }}",
            icon: "info",
            confirmButtonText: "OK"
        });
    @endif
</script>

</html>
