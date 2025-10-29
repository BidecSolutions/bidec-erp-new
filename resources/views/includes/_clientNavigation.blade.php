@php
    use App\Helpers\CommonHelper;

    // Menu type mapping (consider moving this to a config or helper file if reused)
    $menuType = [
        '1' => 'User',
        '2' => 'Purchase',
        '3' => 'Sales',
        '4' => 'Store',
        '5' => 'Finance',
        '6' => 'Setting',
        '7' => 'Reports',
        '8' => 'Dashboard',
        '9' => 'HR',
        '10' => 'General Option'
    ];

    // Fetch menu types and submenus in one go
    //$getMenuTypesTwo = DB::table('menus')->select('menu_type', 'menu_icon')->distinct()->get();
    //$jsonString = DB::table('sub_menus')->where('status', 1)->get()->toJson();

    // Cache menu types and submenus to reduce DB queries
    $cacheKeyMenuTypes = 'menu_types_with_icons';
    $getMenuTypesTwo = Cache::remember($cacheKeyMenuTypes, 60, function () {
        return DB::table('menus')->select('menu_type', 'menu_icon')->distinct()->get();
    });

    $cacheKeySubMenus = 'active_sub_menus';
    $jsonString = Cache::remember($cacheKeySubMenus, 60, function () {
        return DB::table('sub_menus')->where('status', 1)->get()->toJson();
    });
@endphp

<style>
    .dd.active > a {
        background: #f0f0f0;
        border-left: 4px solid #007bff;
    }

    .mmastermnu .active a {
        color: #007bff !important;
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
        <img class="logo_m" src="<?php echo url('/assets/img/logo.webp')?>">
        
        <div class="o_f">
            <a href="#" class="closebtn theme-f-clr Navclose" ><i class="far fa-dot-circle"></i></a>
        </div>
    </div>
    <ul class="m_list " id="myGroup">
        <?php
            $SubQueryMenu = DB::Connection('mysql')->table('menus')->select('menu_type','menu_icon')->distinct()->get();
            foreach($SubQueryMenu as $sqmRow){
                $linkName = $menuType[$sqmRow->menu_type] ?? null;
                $newCounter = 0;
        ?>
                <li class="mainOption_<?php echo $sqmRow->menu_type?>">
                    <div class="sm-bx">
                        <button class="btn settingListSb theme-bg" data-toggle="collapse" data-target="#masterSetting<?php echo $sqmRow->menu_type?>" ><span><i class="<?php echo $sqmRow->menu_icon?>"></i></span> <p><?php echo $linkName?></p></button>
                        <?php 
                            $mainMenuId = $sqmRow->menu_type;
                            $cacheKeySubMenu = 'sub_menus_for_' . $sqmRow->menu_type;
                            $SubQuerySubMenus = Cache::remember($cacheKeySubMenu, 60, function () use ($sqmRow) {
                                return DB::Connection('mysql')->table('menus')->where('menu_type', $sqmRow->menu_type)->get();
                            });
                            //$SubQuerySubMenus = DB::Connection('mysql')->table('menus')->where('menu_type',$sqmRow->menu_type)->get();
                            $count = 1;
                            $getMenuDetailId = DB::table('sub_menus')
                                ->join('menus', 'sub_menus.menu_id', '=', 'menus.id') // Corrected join
                                ->where('sub_menus.url', Route::currentRouteName()) // Ensure column reference
                                ->select('sub_menus.*', 'menus.menu_type') // Select needed fields
                                ->first();
                                $menuTypeClass = '';
                                if(!empty($getMenuDetailId)){
                                    if ($getMenuDetailId->menu_type != $sqmRow->menu_type) {
                                        $menuTypeClass = 'collapse';
                                    }
                                }else{
                                    $menuTypeClass = 'collapse';
                                }
                                
                        ?>
                        <div id="masterSetting<?php echo $sqmRow->menu_type?>" class="{{$menuTypeClass}} pmastermnu">
                            <ul class="list-unstyled">
                            @foreach($SubQuerySubMenus as $sqsmRow)
                                @php
                                    $menuId = $sqsmRow->id;
                                    $subMenus = json_decode($jsonString, true);
                                    $specificRecords = array_filter($subMenus, fn($subMenu) => $subMenu['menu_id'] == $menuId && $subMenu['sub_menu_type'] == 1);
                                    $urls = array_column($specificRecords, 'url');
                                    $hasActiveChild = collect($specificRecords)->contains(fn($record) => Route::currentRouteName() === $record['url']);
                                @endphp
                                @if (Auth::user()->email !== 'ushahfaisalranta@gmail.com' && Auth::user()->acc_type !== 'owner')
                                    @canany($urls)
                                        <li class="dd {{ $hasActiveChild ? 'active' : '' }}"><a href="#" class="settingListSb-subItem" data-toggle="collapsee" data-target="#masterSetting1-<?= $count ?>">{{$sqsmRow->menu_name}}</a>
                                            <div id="masterSetting1-<?= $count ?>" class="collapsee smastermnu">
                                                <ul class="list-unstyled">
                                                    <?php
                                                        $sqsmRowParentCode = $sqsmRow->id;
                                                        $mainSubMenus = DB::Connection('mysql')->table('sub_menus')->where('menu_id',$sqsmRow->id)->where('sub_menu_type',1)->get();
                                                        foreach($mainSubMenus as $msmRow1){
                                                            $routeUrl = $msmRow1->url;
                                                            $isActive = Route::currentRouteName() === $routeUrl;
                                                    ?>
                                                            @can($routeUrl)
                                                                <li class="{{ $isActive ? 'active' : '' }}">
                                                                    <span><i class="fal fa-circle-notch"></i></span>
                                                                    <a href="{{ route($routeUrl) }}" class="{{ $isActive ? 'active' : '' }}">
                                                                        {{ $msmRow1->sub_menu_name }}
                                                                    </a>
                                                                </li>
                                                                @php
                                                                    $newCounter++;
                                                                @endphp
                                                            @endcan
                                                    <?php 
                                                        } 
                                                    ?>
                                                </ul>
                                            </div>
                                        </li>
                                    @endcanany
                                @else
                                    <li class="dd {{ $hasActiveChild ? 'active' : '' }}"><a href="#" class="settingListSb-subItem" data-toggle="collapsee" data-target="#masterSetting1-<?= $count ?>">{{$sqsmRow->menu_name}}</a>
                                        <div id="masterSetting1-<?= $count ?>" class="collapsee smastermnu">
                                            <ul class="list-unstyled">
                                                <?php
                                                    $sqsmRowParentCode = $sqsmRow->id;
                                                    $mainSubMenus = DB::Connection('mysql')->table('sub_menus')->where('menu_id',$sqsmRow->id)->where('sub_menu_type',1)->get();
                                                    foreach($mainSubMenus as $msmRow1){
                                                        $routeUrl = $msmRow1->url;
                                                        $isActive = Route::currentRouteName() === $routeUrl;
                                                ?>
                                                        <li class="{{ $isActive ? 'active' : '' }}">
                                                            <span><i class="fal fa-circle-notch"></i></span>
                                                            <a href="{{ route($routeUrl) }}" class="{{ $isActive ? 'active' : '' }}">
                                                                {{ $msmRow1->sub_menu_name }}
                                                            </a>
                                                        </li>
                                                <?php 
                                                    } 
                                                ?>
                                            </ul>
                                        </div>
                                    </li>
                                @endif
                                <?php 
                                    $count ++;
                                ?>
                            @endforeach
                            @if (Auth::user()->email !== 'ushahfaisalranta@gmail.com' && Auth::user()->acc_type !== 'owner')
                                <script>
                                    removeMainOption('<?php echo $sqmRow->menu_type?>','<?php echo $newCounter?>');
                                </script>
                            @endif
                            </ul>
                        </div>
                    </div>
                </li>
        <?php
            }
        ?>
        <!-- All Company START--->
       
        <li class="dropdown">
            <div class="sm-bx">     
                <button class="btn settingListSb theme-bg" data-toggle="modal" data-target="#companyListModel"><span><i class=""></i></span> <p>Company List</p></button>                                   
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
                <ul class="nav navbar-nav navbar-right">
                    <li class="dropdown user-name-drop">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">{{ substr('A', 0, 1) }}</a>
                        <div class="account-information dropdown-menu dropdown-menu-right">
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
                        Company Name: <strong>{{ Session::get('company_name') }}</strong> ||
                        Campus Name: <strong>{{ Session::get('company_location_name') }}</strong>
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
