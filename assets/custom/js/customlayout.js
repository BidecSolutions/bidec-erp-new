$(document).ready(function(){
    
$(".Navclose").click(function(){
    $(".sidenavnr").toggleClass("Navactive");
    $("body").toggleClass("full_with");
   
  });



$(".tmenu-list").hover(function () {
      $(this).toggleClass("active");
    });
$(".settingListSb").click(function () {
      $(this).toggleClass("active");
    });

$(".settingListSb-subItem").click(function () {
      $(this).toggleClass("active");
     
    });

$(".dd").click(function () {
      $(this).find('.collapsee').slideToggle();

    });
$(".sm-bx").click(function () {
       $(".sidenavnr").removeClass("Navactive");
    $("body").removeClass("full_with");

    });


 var $myGroup = $('#myGroup');
$myGroup.on('show.bs.collapse','.collapse', function() {
    $myGroup.find('.collapse.in').collapse('hide');
});



// end
});

