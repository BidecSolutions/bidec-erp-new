@php
    use App\Helpers\CommonHelper;
@endphp

<style>
    .dd.active > a {
        background: #f0f0f0;
        border-left: 4px solid #205b60;
    }

    .mmastermnu .active a {
        color: #205b60 !important;
        font-weight: bold !important;
    }

    .mmastermnu .active a:before {
        content: "•";
        margin-right: 8px;
        color: #205b60;
    }

    .mmastermnu.show {
        display: block;
    }

    .smastermnu .active a {
        color: #205b60 !important;
        font-weight: bold !important;
    }

    .smastermnu .active a:before {
        content: "•";
        margin-right: 8px;
        color: #205b60;
    }

    .smastermnu.show {
        display: block;
    }
    .headerwrap {
        margin: 1.3rem auto 0px;
        border-radius: 0.428rem;
        z-index: 12;
        box-shadow: 0 4px 24px 0 rgb(34 41 47 / 10%);
    }
    .well_N {
        position: relative;
        transition: 300ms ease all;
        backface-visibility: hidden;
        min-height: calc(100% - 3.35rem);
        /* padding:calc(2rem + 4.45rem + 1.3rem) 0.6rem 0; */
        /* background: antiquewhite; */
    }
</style>
<div class="container-fluid">
    <div class="headerwrap">
        <nav class="navbar erp-menus">
            <div class="navbar-header">
                <button class="navbar-toggle" type="button" data-toggle="collapse" data-target=".js-navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </div>
            <div class="collapse navbar-collapse js-navbar-collapse">
                <ul class="nav navbar-nav">
                    <li class="dropdown user-name-drop">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">{{ substr('A', 0, 1) }}</a>
                        <div class="account-information dropdown-menu">
                            <div class="account-inner">
                                <div class="title">
                                    <span>{{ substr('A', 0, 1) }}</span>
                                </div>
                                <div class="main-heading">
                                    <h5>{{ 'A' }}</h5>
                                    <p>{{ 'POS Management System' }}</p>
                                    <ul class="list-unstyled" id="nav">
                                        @foreach (range(1, 7) as $i)
                                            <li><a href="#" rel="{{ url("/assets/css/color-$i.css") }}"><div class="color-{{ $i }}"></div></a></li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            <div class="account-footer">
                                <a href="{{ route('change_password') }}" class="btn link-accounts sign_out">Change Password</a>
                                <a href="{{ url('/signout') }}" class="btn link-accounts sign_out">Sign out</a>
                            </div>
                        </div>
                    </li>
                </ul>
                <div style="text-align: center">
                    <h4 style="color: black;">
                        Company Name: <strong>{{ Session::get('company_name') }}</strong> || Location Name: <strong>{{ Session::get('company_location_name') }}</strong> &nbsp;&nbsp;&nbsp;&nbsp;<span class="btn btn-xs btn-primary" onclick="openTraceStockModel()">Check Stock</span> &nbsp;&nbsp;<a class="btn btn-xs btn-primary" href="{{url('/')}}">Dashboard</a>
                    </h4>
                </div>
            </div>
        </nav>
    </div>
</div>

<!-- Hidden field for base URL -->
<input type="hidden" id="url" value="{{ url('/') }}">

<!-- Script Section -->
<script type="text/javascript">
    // Cookie management utilities
    var Cookie = {
        // Keep the existing cookie methods as is if needed
    };

    $(document).ready(function () {
        // Keep the document-ready code as is if necessary
    });
    
</script>
