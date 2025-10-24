@php
    $m = 1;
    use App\Helpers\CommonHelper;
    $menuType = array(
        '1' => 'User',
        '2' => 'Finance',
        '3' => 'Purchase',
        '4' => 'Store',
        '5' => 'Sale',
        '6' => 'HR',
        '7' => 'Reports',
        '8' => 'Dashboard',
        '9' => 'General Setting',
        '10' => 'General Option'
    );
    // Read the JSON file
    $jsonString = DB::table('sub_menus')->get()->toJson();
    $getMenus = DB::table('menus')->get();
    
@endphp
<div id="mySidenav" class="sidenavnr">
    <div class="logo_wrp">
        <img class="logo_m" src="{{CommonHelper::displaySchoolLogo()}}">

        <div class="o_f">
            <a href="#" class="closebtn theme-f-clr Navclose" ><i class="far fa-dot-circle"></i></a>
        </div>
    </div>
    <ul class="m_list " id="myGroup">
        
        <li>
            <div class="sm-bx">
                <ul>
                    <li class="dd" style="border-bottom: 1px solid #ccc; padding-top: 4px;">
                        <a href="{{route('parents.dashboard')}}" class="settingListSb-subItem btn theme-bg">Dashboard</a>
                    </li>
                    <li class="dd" style="border-bottom: 1px solid #ccc; padding-top: 4px;">
                        <a href="{{route('parents.comletedParasList')}}" class="settingListSb-subItem btn theme-bg">Completed Paras List</a>
                    </li>
                    <li class="dd" style="border-bottom: 1px solid #ccc; padding-top: 4px;">
                        <a href="{{route('parents.attendance-list')}}" class="settingListSb-subItem btn theme-bg">Attendance List</a>
                    </li>
                    <?php /*?><li class="dd" style="border-bottom: 1px solid #ccc; padding-top: 4px;">
                        <a href="{{route('parents.studentPerformanceList')}}" class="settingListSb-subItem btn theme-bg">Student Performances List</a>
                    </li><?php */?>
                    <li class="dd" style="border-bottom: 1px solid #ccc; padding-top: 4px;">
                        <a href="{{route('parents.viewStudentPerformanceReport')}}" class="settingListSb-subItem btn theme-bg">Student Performance Report</a>
                    </li>
                    <li class="dd" style="border-bottom: 1px solid #ccc; padding-top: 4px;">
                        <a href="{{route('parents.viewMonthlyPerformanceReport')}}" class="settingListSb-subItem btn theme-bg">Monthly Performance Report</a>
                    </li>
                    
                </ul>
            </div>
        </li>
        <!-- All Company END--->
    </ul>
</div>
<div class="container-fluid">
    <div class="headerwrap">
        <nav class="navbar  erp-menus">
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
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo  substr('A',0,1); ?></a>
                        <div class="account-information dropdown-menu">
                            <div class="account-inner">
                                <div class="title">
                                    <span><?php echo  substr('A',0,1); ?></span>
                                </div>
                                <div class="main-heading">

                                    <h5>{{ 'A' }}</h5>
                                    <p><?php echo 'Bidec Accounts'?></p>

                                    <ul class="list-unstyled" id="nav">
                                        <li><a href="#" rel="<?php echo url('/'); ?>/assets/css/color-one.css"><div class="color-one"></div></a></li>
                                        <li><a href="#" rel="<?php echo url('/'); ?>/assets/css/color-two.css"><div class="color-two"></div></a></li>
                                        <li><a href="#" rel="<?php echo url('/'); ?>/assets/css/color-three.css"><div class="color-three"></div></a></li>
                                        <li><a href="#" rel="<?php echo url('/'); ?>/assets/css/color-four.css"><div class="color-four"></div></a></li>
                                        <li><a href="#" rel="<?php echo url('/'); ?>/assets/css/color-five.css"><div class="color-five"></div></a></li>
                                        <li><a href="#" rel="<?php echo url('/'); ?>/assets/css/color-six.css"><div class="color-six"></div></a></li>
                                        <li><a href="#" rel="<?php echo url('/'); ?>/assets/css/color-seven.css"><div class="color-seven"></div></a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="account-footer">

                                <a style="display:none;" href="{{route('change_password')}}" class="btn link-accounts sign_out">Change Password</a>
                                <a href="{{ url('/signout') }}" class="btn link-accounts sign_out">Sign out</a>
                            </div>
                        </div>
                    </li>
                </ul>
                <div style="text-aligh: right">
                    <h3 style="color: green">{{Session::get('company_name')}}</h3>
                    <div class="style-switcher">
                        <a href="#" id="switcher-toggler"><i class="fa fa-cog"></i></a>
                        <h3>Choose Language</h3>
                        <div id="translate"></div>
                        <script src="https://cdn.jsdelivr.net/npm/js-cookie@rc/dist/js.cookie.min.js"></script>
                    </div>
                </div>

            </div>
        <!-- /.nav-collapse -->
    </nav>
</div>
</div>
<!--For Demo Only (End Removable) -->
<input type="hidden" id="url" value="<?php echo url('/') ?>">
<!-- MENU SECTION END-->
<script type="text/javascript">

    // var url = "{{ route('changeLang') }}";

    // $(".changeLang").change(function(){
    //     window.location.href = url + "?lang="+ $(this).val();
    // });
</script>
<script type="text/javascript">
	var Cookie = {
	set: function (name, value, days) {
		var domain, domainParts, date, expires, host;

		if (days) {
			date = new Date();
			date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
			expires = "; expires=" + date.toGMTString();
		} else {
			expires = "";
		}

		host = location.host;
		if (host.split(".").length === 1) {
			// no "." in a domain - it's localhost or something similar
			document.cookie = name + "=" + value + expires + "; path=/";
		} else {
			// Remember the cookie on all subdomains.
			//
			// Start with trying to set cookie to the top domain.
			// (example: if user is on foo.com, try to set
			//  cookie to domain ".com")
			//
			// If the cookie will not be set, it means ".com"
			// is a top level domain and we need to
			// set the cookie to ".foo.com"
			domainParts = host.split(".");
			domainParts.shift();
			domain = "." + domainParts.join(".");

			document.cookie =
				name + "=" + value + expires + "; path=/; domain=" + domain;

			// check if cookie was successfuly set to the given domain
			// (otherwise it was a Top-Level Domain)
			if (Cookie.get(name) == null || Cookie.get(name) != value) {
				// append "." to current domain
				domain = "." + host;
				document.cookie =
					name + "=" + value + expires + "; path=/; domain=" + domain;
			}
		}
	},

	get: function (name) {
		var nameEQ = name + "=";
		var ca = document.cookie.split(";");
		for (var i = 0; i < ca.length; i++) {
			var c = ca[i];
			while (c.charAt(0) == " ") {
				c = c.substring(1, c.length);
			}

			if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
		}
		return null;
	},

	erase: function (name) {
		Cookie.set(name, "", -1);
	}
};

// function googleTranslateElementInit() {
//     let url = new URL(window.location);
//     let lang = url.searchParams.get("lang");
//     if(!lang)
//     {
//     	lang = "en";
//     }
//     // List of languages you want to include (Arabic and English)
//     const includedLanguages = ['ar', 'en','ur'];

//     if (lang && includedLanguages.includes(lang)) {
//         console.log(lang);
//         Cookie.set("googtrans", `/ar/${lang}`, { path: "" });
//         Cookie.set("googtrans", `/ar/${lang}`);
//         Cookie.set("googtrans", `/ar/${lang}`, { path: "", domain: location.host });
//     } else {
//         Cookie.erase("googtrans");
//         Cookies.remove("googtrans", { path: "" });
//     }

//     new google.translate.TranslateElement({
//         pageLanguage: "en",
//         includedLanguages: includedLanguages.join(',')
//     }, "translate");

//     let langSelector = document.querySelector(".goog-te-combo");
//     langSelector.addEventListener("change", function () {
//         let lang = langSelector.value;

//         // Check if the selected language is included in the allowed languages
//         if (includedLanguages.includes(lang)) {
//             var newurl =
//                 window.location.protocol +
//                 "//" +
//                 window.location.host +
//                 window.location.pathname +
//                 "?lang=" +
//                 lang;
//             window.history.pushState({ path: newurl }, "", newurl);
//         } else {
//             // Handle the case where an invalid language is selected (e.g., show an error message)
//         }
//     });
// }

// document.addEventListener("DOMContentLoaded", function () {
//     (function () {
//         Cookie.erase("googtrans");
//         var googleTranslateScript = document.createElement("script");
//         googleTranslateScript.type = "text/javascript";
//         googleTranslateScript.async = true;
//         googleTranslateScript.src =
//             "//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit";
//         (
//             document.getElementsByTagName("head")[0] ||
//             document.getElementsByTagName("body")[0]
//         ).appendChild(googleTranslateScript);
//     })();
// });
$(document).ready(function(){
//     var parentElement = document.querySelector('.skiptranslate.goog-te-gadget');
// var childNodeToRemove = parentElement.childNodes[1];

// // Check if the child node is a text node
// if (childNodeToRemove.nodeType === Node.TEXT_NODE) {
//     parentElement.removeChild(childNodeToRemove);
// }
});
</script>
