

function map_initialize() {
    var mapOptions = {
        center: new google.maps.LatLng(55.137581, 61.401160),
        zoom: 16,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    var map = new google.maps.Map(document.getElementById("map_canvas"),
        mapOptions);

    var address1 = new google.maps.LatLng(55.137569, 61.400345);
    var icon1 = new google.maps.MarkerImage("../img/map_marker.png",
        new google.maps.Size(30, 45),
        new google.maps.Point(0,0),
        new google.maps.Point(15, 45)
    );
    var marker1 = new google.maps.Marker({
        position: address1,
        icon: icon1,
        map: map,
        title:"ул. Доватора 1В"
    });
    marker1.setMap(map);
}

function closePopup()
{
    $('#popup_modal_form, #popup_modal_bg_layer, .form_submitted_layer_container').hide();
    $('div.appointment_form .form_content').show();
}

function calculate_price() {
    var price = 0;
    var time = 0;
    price = $('.choose_service .service_list li.active').attr('prive_value');
    time = $('.choose_service .service_list li.active').attr('time_value');
    $("input#wash_result_price").val(price+ " рублей") ;
    $("input#wash_result_time").val(time+ " минут") ;
}

function openPrivacyWindow() {
    url = 'privacy.html';
    var width = 480;
    var height = 500;
    var leftPx = ( screen.availWidth - width ) / 2;
    var topPx = ( screen.availHeight - height ) / 2;
    var params = "width=" +width+ ", height=" +height+ ", resizable=yes, scrollbars=yes, top=" +topPx+ ", left=" +leftPx;
    window.open(url, 'newWindow', params);
}


$( document ).ready(function() {

    if ($("#mymap").length > 0){
        map_initialize();
    }

    $('.open_modal_form').click(function () {
        $('#popup_modal_form, #popup_modal_bg_layer').show();
        $('div.appointment_form .form_content').show();
        return false;
    });

    $('#popup_modal_form .icon_close').click(function () {
        closePopup();
    });

    $('#popup_modal_bg_layer').click(function () {
        closePopup();
    });

    $('.form_submitted_layer_container .icon_close').click(function () {
        $('.form_submitted_layer_container').hide();
        $('div.appointment_form .form_content').show();
    });

    $('form input[name="phone"]').keypress(function(e){
        if (e.which > 0 && // check that key code exists
            e.which != 8 && // allow backspace
            e.which != 32 && // allow space
            //e.which != 45 && // allow dash
            e.which != 43 && // allow +
            !(e.which >= 48 && e.which <= 57) // allow 0-9
            )
        {
            e.preventDefault();
        }
    });

    $('.form_submit').on('click', function(){
        var $name = $(this).parents('form').find('input[name="name"]'),
            $phone = $(this).parents('form').find('input[name="phone"]'),
            $email = $(this).parents('form').find('input[name="email"]');

        $name.removeClass('error');
        $phone.removeClass('error');
        $email.removeClass('error');

        if ($name.length && !$name.val().trim()) { $name.addClass('error'); }
        if ($phone.length && !$phone.val().trim()) { $phone.addClass('error'); }
        else {
            if($phone.length &&  $phone.val().trim().match(/[^+0-9 ]/) ){ $phone.addClass('error'); }
        }
        if ($email.length && !$email.val().trim()) { $email.addClass('error'); }

        if ($name.hasClass('error') || $phone.hasClass('error')|| $email.hasClass('error')) { return false; }
    });

    $('form').on('submit', function(){
        var that = this;
        $.ajax({
            url: this.getAttribute('action'),
            type: this.getAttribute('method'),
            data: $(this).serialize(),
            complete: function(){
                $(that).find('input[type="text"], textarea').val('');
                //$(that).find('input[type="submit"]').hide();
                $('div.appointment_form .form_content').hide();
                $('div.form_submitted_layer_container').show();
            }
        });
        return false;
    });

    calculate_price();
    // selection in calculator
    $('.choose_car_type .car_item').click(function(){
        $('.choose_car_type .car_item').removeClass('active');
        $(this).addClass('active');
    });

    $('.choose_service .service_list li').click(function(){
        $('.choose_service .service_list li').removeClass('active');
        $(this).addClass('active');
        calculate_price();
    });


});
