/* Проверка на корректность введенных данных */

jQuery("#contactForm").validator().on("submit", function (event) {
    if (event.isDefaultPrevented()) {
        // в случае некорректно заполненной формы
        formError();
        submitMSG(false, "Пожалуйста, проверьте корректность введенных данных!");
    } else {
        // если все поля заполнены праильно
        event.preventDefault();
        submitForm();
    }
});


//Формирование и отправка формы на php
function submitForm(){
    // Формирование переменных на основе полученной формы
    var date = jQuery("#date").val();       //Дата
    var captcha = jQuery("#text-captcha").val();    //Капча

    jQuery.ajax({
        type: "POST",       //Передача данных на сервер методом POST
        url: "php/index.php",    //URL PHP файла для дальнейшей обработки данных
        data: "date=" + date + "&captcha=" + captcha,   //Данные для отправки на сервер
        success : function(text){   
            //в случае успешного завершения запроса...
            jQuery( "#messages" ).replaceWith( jQuery("#messages").html(text) );
        }
    });
}

function formError(){
    jQuery("#contactForm").removeClass().addClass('shake animated').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){
        jQuery(this).removeClass();
    });
}

function submitMSG(valid, msg){
    if(valid){
        var msgClasses = "h3 text-center tada animated text-success";
        jQuery( "#contactForm" ).replaceWith( jQuery("#contactForm").addClass(msgClasses).text(msg) );
    } else {
        var msgClasses = "h3 text-center text-danger";
        jQuery("#msgSubmit").removeClass().addClass(msgClasses).text(msg);
    }
}