<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>BIDEC ERP</title>

    <!-- Fonts & Styles -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/css/bootstrap.min.css" crossorigin="anonymous">

    <!-- jQuery -->
    <script src="{{ URL::asset('assets/js/jquery-1.10.2.js') }}"></script>

    <style>
        /* ===== General Styles ===== */
        body {
            font-family: 'Lato', sans-serif;
            margin: 0;
            padding: 0;
            overflow: hidden; /* Prevent scroll until loaded */
        }

        .fa-btn {
            margin-right: 6px;
        }

        /* ===== Loader Overlay ===== */
        #loader {
            position: fixed;
            z-index: 9999;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            transition: opacity 0.6s ease, visibility 0.6s ease;
        }

        /* Spinner Animation */
        .spinner {
            border: 6px solid #f3f3f3;
            border-top: 6px solid #0b5f5a;
            border-radius: 50%;
            width: 70px;
            height: 70px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Optional Text under loader */
        .loader-text {
            margin-top: 15px;
            color: #0b5f5a;
            font-weight: 600;
            font-size: 18px;
            letter-spacing: 1px;
        }

        /* ===== Content Fade-in Effect ===== */
        #app-layout {
            opacity: 0;
            transition: opacity 0.6s ease;
        }

        /* Once loaded */
        body.loaded #app-layout {
            opacity: 1;
        }

        body.loaded #loader {
            opacity: 0;
            visibility: hidden;
        }
    </style>
</head>

<body id="app-layout">
    <!-- ===== Page Loader ===== -->
    <div id="loader">
        <!-- Option 1: Spinner -->
        <div class="spinner"></div>
        <div class="loader-text">Loading...</div>

        <!-- Option 2: Logo Loader (uncomment below and remove spinner if you prefer a logo) -->
        <!--
        <img src="{{ asset('assets/images/bidec-logo.png') }}" alt="BIDEC Logo" width="150" style="margin-bottom: 10px;">
        <div class="loader-text">Loading...</div>
        -->
    </div>

    <!-- ===== Page Content ===== -->
    @yield('content')

    <!-- ===== Loader Script ===== -->
    <script>
        $(window).on('load', function() {
            // Fade out loader & show content
            $('body').addClass('loaded');
            // Enable scroll
            $('body').css('overflow', 'auto');
        });
    </script>
</body>
</html>
