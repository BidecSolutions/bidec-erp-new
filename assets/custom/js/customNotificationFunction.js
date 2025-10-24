var baseUrl = $('#baseUrl').val();
function displayNotificationModuleWise(paramOne,paramTwo,paramThree,paramFour){
    //alert(paramThree);
    $('#'+paramTwo+'').html('<div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div></div>');
    var functionName = paramFour;
    var m = paramThree;
    var url = ''+baseUrl+'/'+functionName+'';
    var subMenuSelect = $('#'+paramOne+'SubMenuSelectOption').val();
    var subMenuRightSelect = $('#'+paramOne+'SubMenuRightSelectOption').val();
    var notificationDate = $('#'+paramOne+'NotificationDate').val();
    $.getJSON(url, {subMenuSelect: subMenuSelect, subMenuRightSelect: subMenuRightSelect, m: m,notificationDate:notificationDate}, function (result) {
        $.each(result, function (i, field) {
            $('#' + paramTwo + '').html('' + field + '');
        });
    })


}