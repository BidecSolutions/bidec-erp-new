@php
    $m = 1;
    use App\Helpers\CommonHelper;
    $menuType = array(
        '1' => 'User',
        '9' => 'General Setting',
        '10' => 'General Option',
        '2' => 'Finance',
        '3' => 'Purchase',
        '4' => 'Store',
        '5' => 'Sale',
        '6' => 'HR',
        '7' => 'Reports',
        '8' => 'Dashboard'
        
    );
    // Read the JSON file
    if(Auth::user()->acc_type == 'client'){
        $jsonString = DB::table('sub_menus')->get()->toJson();
    }else{
        $jsonString = DB::table('sub_menus')->where('status',1)->get()->toJson();
    }
    
    $getMenus = DB::table('menus')->orderBy('menu_order_by')->get();
@endphp

<style>
    .dd.active > a {
        background: #f0f0f0;
        border-left: 4px solid #007bff;
    }

    .mmastermnu .active a {
        color: #3c4044ff !important;
        font-weight: bold !important;
    }

    .mmastermnu .active a:before {
        content: "•";
        margin-right: 8px;
        color: #007bff;
    }

    .mmastermnu.show {
        display: block;
    }

    .smastermnu .active a {
        color: #007bff !important;
        font-weight: bold !important;
    }

    .smastermnu .active a:before {
        content: "•";
        margin-right: 8px;
        color: #007bff;
    }

    .smastermnu.show {
        display: block;
    }
    .headerwrap {
        margin: 1.3rem auto 0px;
        border-radius: 0.428rem;
        z-index: 12;
        margin-left: 270px;
        box-shadow: 0 4px 24px 0 rgb(34 41 47 / 10%);
    }
    .well_N {
        position: relative;
        transition: 300ms ease all;
        backface-visibility: hidden;
        min-height: calc(100% - 3.35rem);
        margin-left: 270px;
        /* padding:calc(2rem + 4.45rem + 1.3rem) 0.6rem 0; */
        /* background: antiquewhite; */
    }
</style>

<div id="mySidenav" class="sidenavnr">
    <div class="logo_wrp">
        <img class="logo_m" src="./assets/img/logo.png">

        <div class="o_f">
            <a href="#" class="closebtn theme-f-clr Navclose" ><i class="far fa-dot-circle"></i></a>
        </div>
    </div>
    <ul class="m_list " id="myGroup">
        
        <li>
            <div class="sm-bx">
                <ul>
                    @php
                        $count = 1;
                    @endphp
                    
                    @foreach($getMenus as $gmRow)
                        @php
                            $menuId = $gmRow->id;
                            // Decode the JSON string to an array
                            $subMenus = json_decode($jsonString, true);
                            // Filter the records based on the ID
                            $specificRecords = array_filter($subMenus, function($subMenu) use ($menuId) {
                                return $subMenu['menu_id'] == $menuId && $subMenu['sub_menu_type'] == 1;
                            });
                            $urls = [];
                            foreach ($specificRecords as $value) {
                                $urls[] = $value['url'];
                            }
                        @endphp
                        @if(Auth::user()->email != 'ushahfaisalranta@gmail.com')
                            <h4>Menu Heading</h4>
                            @canany($urls)
                                <li class="dd" style="border-bottom: 1px solid #ccc; padding-top: 4px;">
                                    <a href="#" class="settingListSb-subItem btn theme-bg" data-toggle="collapsee" data-target="#masterSetting1-<?= $count ?>">{{ $gmRow->menu_name }}</a>
                                    <div id="masterSetting1-<?= $count ?>" class="collapsee smastermnu">
                                        <ul class="list-unstyled">
                                            @foreach($specificRecords as $gsmRow)
                                                @php
                                                    $routeUrl = $gsmRow['url'];
                                                @endphp
                                                @can($routeUrl)
                                                    <li>
                                                        <span><i class="fal fa-circle-notch"></i></span>
                                                        <a href="{{route($routeUrl)}}">{{$gsmRow['sub_menu_name']}}</a>
                                                    </li>
                                                @endcan
                                            @endforeach
                                        </ul>
                                    </div>
                                </li>
                            @endcanany
                        @else
                            <li class="dd" style="border-bottom: 1px solid #ccc; padding-top: 4px;">
                                <a href="#" class="settingListSb-subItem btn theme-bg" data-toggle="collapsee" data-target="#masterSetting1-<?= $count ?>">{{ $gmRow->menu_name }}</a>
                                <div id="masterSetting1-<?= $count ?>" class="collapsee smastermnu">
                                    <ul class="list-unstyled">
                                        @foreach($specificRecords as $gsmRow)
                                            @php
                                                $routeUrl = $gsmRow['url'];
                                            @endphp
                                            <li>
                                                <span><i class="fal fa-circle-notch"></i></span>
                                                <a href="{{route($routeUrl)}}">{{$gsmRow['sub_menu_name']}}</a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </div>
        </li>
        <!-- All Company END--->
    </ul>
</div>

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
                    <h4 style="color: black;">Company Name: <strong>{{ Session::get('company_name') }}</strong> || Campus Name: <strong>{{ Session::get('company_location_name') }}</strong> &nbsp;&nbsp;&nbsp;&nbsp;<span class="btn btn-xs btn-primary" onclick="openTraceStockModel()">Check Stock</span> &nbsp;&nbsp;&nbsp;&nbsp;<a class="btn btn-xs btn-primary" href="{{url('pos/create')}}">POS</a></h4>
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
