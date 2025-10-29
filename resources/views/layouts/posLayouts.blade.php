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
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <script src="{{ URL::asset('assets/js/jquery-1.10.2.js') }}"></script>
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
  <div class="container-fluid  text-white py-2 position-fixed start-0 w-100" style="background-color: #030351ff !important">
  <div class="row align-items-center text-center">
    
  <div class="col-md-1">
  <button style="margin-top:2%" class="btn btn-outline-light btn-sm rounded-circle">
        <i class="fa-solid fa-arrow-left"></i>
      </button>
    </div>
  <div class="col-md-3">
    <h5 style="color:  #ffffffff" class="mb-0 fw-bold text-uppercase">Muhammad Murad - ( Sale Agent )</h5>
    </div>
    <!-- Left: Date -->
     <div class="col-md-4">
    <h5 style="color:  #ffffffff" class="mb-0 fw-bold text-uppercase">Date :  27th Oct 2025 |  Time : 9:45pm </h5>
    </div>
    
    <!-- Center: Title -->
   <div class="col-md-3">
    <h5 style="color:  #ffffffff" class="mb-0 fw-bold text-uppercase">Order # - (ORD - 543810 )</h5>
    </div>

     <div class="col-md-1">
   <button type="button" class="btn btn-sm btn-danger" style="margin-top:2%" >   <i class="fa-solid fa-power-off"></i></button></div>
  </div>
</div>

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

    @include('includes._footer')
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

        $('body').on('click', '#inactive-record', function() {
            var userURL = $(this).data('url');
            //alert(userURL);
            //return 'Testing';

            var trObj = $(this);
            if (confirm("Are you sure you want to remove this?") == true) {
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
                        get_ajax_data();
                    }
                });
            }
        });
        $('body').on('click', '#active-record', function() {
            var userURL = $(this).data('url');
            var trObj = $(this);
            if (confirm("Are you sure you want to remove this?") == true) {
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
                        get_ajax_data();
                    }
                });
            }
        });


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
                            alert(data.catchError);
                            return;
                        }
                        alert(data.success);
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
    </script>
</body>

</html>
