<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
        <meta name="description" content="" />
        <meta name="author" content="" />
	    <meta name="csrf-token" content="{{ csrf_token() }}" />
        <!--[if IE]>
            <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
            <![endif]-->
        <title>Bidec Accounts</title>
        <!-- BOOTSTRAP CORE STYLE  -->
        <link href="{{ URL::asset('assets/css/bootstrap.css') }}" rel="stylesheet" />
        <!-- FONT AWESOME STYLE  -->
        <link href="{{ URL::asset('assets/css/font-awesome.css') }}" rel="stylesheet" />
        <!-- CUSTOM STYLE  -->
        <link href="{{ URL::asset('assets/css/main.css') }}" rel="stylesheet" />
        <!-- GOOGLE FONT -->
        <link href="{{ URL::asset('assets/css/fa.css') }}" rel='stylesheet' type='text/css' />
        <link href="{{ URL::asset('assets/css/arrows.css') }}" rel='stylesheet' type='text/css' />
        <script src="{{ URL::asset('assets/js/jquery.min.js') }}"></script>
        <script src="{{ URL::asset('assets/js/jquery-1.10.2.js') }}"></script>
        <!-- BOOTSTRAP SCRIPTS  -->
        <script src="{{ URL::asset('assets/js/bootstrap.js') }}"></script>
        <link href="{{ URL::asset('assets/css/font-awesome.min.css') }}" rel="stylesheet">
        <link href="{{ URL::asset('assets/css/style.css') }}" rel="stylesheet" />
    </head>
    <body>
        <!-- LOGO HEADER END-->
        <!-- Services Section -->
        <input type="hidden" id="url" value="<?php echo url('/') ?>">
        <!-- MENU SECTION END-->
        <div class="modal fade" id="showForgotPasswordModel">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header btn-primary">
                        <div class="row">
                            <div class="col-md-8 col-sm-1 col-xs-12 text-center">
                                <span class="modalTitle subHeadingLabelClass"></span>
                            </div>
                            <div class="col-md-4 col-sm-1 col-xs-12 text-right">
                                <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal" aria-hidden="true">Close</button>
                            </div>
                        </div>
                    </div>
                    <div  class="modal-body"></div>
                    <div class="modal-footer theme-bg btn-primary">
                        <div class="row">
                            <div class="text-center ">
                                &copy; <?php echo date('Y')?> Innovative-net.com |<a href="http://www.innovative-net.com/" target="_blank"  > Designed by : innovative-net.com</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
            function showForgotPasswordModel(url,id,modalName){
                $.ajax({
                    url: '<?php echo url('/')?>'+url+'',
                    type: "GET",
                    data: {id:id},
                    success:function(data) {
                        jQuery('#showForgotPasswordModel').modal('show', {backdrop: 'false'});
                        //jQuery('#showMasterTableEditModel').modal('show', {backdrop: 'true'});
                        jQuery('#showForgotPasswordModel .modalTitle').html(modalName);
                        jQuery('#showForgotPasswordModel .modal-body').html('<div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div></div>');
                        setTimeout(function(){
                            jQuery('#showForgotPasswordModel .modal-body').html(data);
                        },1000);
                    }
                });
            }
        </script>