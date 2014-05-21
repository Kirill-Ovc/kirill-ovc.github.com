

function map_initialize() {
    var mapOptions = {
        center: new google.maps.LatLng(55.137641, 61.404955),
        zoom: 16,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    var map = new google.maps.Map(document.getElementById("map_canvas"),
        mapOptions);

    var address = new google.maps.LatLng(55.136540, 61.403266);
    //var address1 = new google.maps.LatLng(55.137569, 61.400345);
    var icon1 = new google.maps.MarkerImage("img/map_marker.png",
        new google.maps.Size(30, 45),
        new google.maps.Point(0,0),
        new google.maps.Point(15, 45)
    );
    var marker1 = new google.maps.Marker({
        position: address,
        icon: icon1,
        map: map,
        title:"ул. Доватора 1В"
    });
    marker1.setMap(map);

    // draw a path to the marker

    var planCoordinates = [
        new google.maps.LatLng(55.137820, 61.402846),
        new google.maps.LatLng(55.137432, 61.402939),
        new google.maps.LatLng(55.136483, 61.402907)
    ];
    var path = new google.maps.Polyline({
        geodesic: true,
        path: planCoordinates,
        strokeColor: "#ffffff",
        strokeOpacity: 0.8,
        strokeWeight: 5
    });

    path.setMap(map);
}

function closePopup()
{
    $('#popup_modal_form, #popup_modal_bg_layer, .form_submitted_layer_container').hide();
    $('div.appointment_form .form_content').show();
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

/* begin calculator functions */
// массивы со значениями цен и времени
var price_array = new Array(
    //Малолитражные | Средний класс | Бизнес класс | Кроссоверы | Джипы, пикапы
    ["200", "250", "320", "400", "500"], //Бесконтактная мойка кузова
    ["100", "120", "140", "160", "180"], //Экспресс мойка
    ["300", "350", "500", "550", "650"], //Наномойка
    ["130", "130", "150", "150", "180"], //Пылесос салона
    ["130", "130", "150", "150", "180"], //Влажная уборка
    ["635", "705", "830", "930", "1120"] //Комплексная мойка
);
var time_array = new Array(
    ["30", "30", "30", "30", "30", "30"], //Бесконтактная мойка кузова
    ["10", "10", "10", "10", "10", "10"], //Экспресс мойка
    ["30", "30", "30", "30", "30", "30"], //Наномойка
    ["30", "30", "30", "30", "30", "30"], //Пылесос салона
    ["30", "30", "30", "30", "30", "30"], //Влажная уборка
    ["50", "50", "50", "50", "50", "50"] //Комплексная мойка
);

function change_price_list(){
    var car_type_id = 1;
    var car_type_id = $('.choose_car_type .car_type_list div.active').attr('car_type_id');
    var num_of_services = $('.choose_service .service_list li[service_id]').length;
    for (var i = 0; i < num_of_services; i++)
    {
        var service_id = i + 1;
        var price = price_array[service_id - 1][car_type_id - 1];
        var time = time_array[service_id - 1][car_type_id - 1];
        $('.choose_service .service_list li[service_id = "'+ service_id+'" ]').attr('prive_value',price);
        $('.choose_service .service_list li[service_id = "'+ service_id+'" ]').attr('time_value',time);
        $('.choose_service .service_list li[service_id = "'+ service_id+'" ] .service_price').html("от " + price + " рублей");
    }
}

function calculate_price() {
    var price = 0;
    var time = 0;
    $('.choose_service .service_list li.active').each(function() {
            price += parseInt($(this).attr('prive_value'), 10);
        });
    $('.choose_service .service_list li.active').each(function() {
        time += parseInt($(this).attr('time_value'), 10);
    });
    $("input#wash_result_price").val(price+ " рублей") ;
    $("input#wash_result_time").val(time+ " минут") ;
}

/* end calculator functions */



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

    change_price_list();
    calculate_price();

    // calculator selection of car type
    $('.choose_car_type .car_item').click(function(){
        $('.choose_car_type .car_item').removeClass('active');
        $(this).addClass('active');
        change_price_list();
        calculate_price();
    });

    // calculator selection of service type
    $('.choose_service .service_list li').click(function(){
        //$('.choose_service .service_list li').toggleClass('active');
        $(this).toggleClass('active');
        calculate_price();
    });


});
