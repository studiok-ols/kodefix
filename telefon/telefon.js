jQuery( document ).ready(function($) {


    $("#F_form").on('submit',function(e){

        var request = $('#F_letters').val();
        var reg = /^[a-z ]+$/i;

        $('.telefon-response').removeClass('done error');

        if(!reg.test(request)) {
            $('.telefon-response').addClass('error');
            $('.telefon-response').html('Tylko litery alfabetu łacińskiego oraz spacja');
            return false;
        }

        request = request.replaceAll(' ','-');

        htmlRequest = $.ajax({
            url: "/wp-json/wl/v1/posts/" + request,
            type: "get"
        });

        htmlRequest.done(function (response, textStatus, jqXHR){
            $('.telefon-response').html(response);
            $('.telefon-response').addClass('done');
        });

        htmlRequest.error(function (request, status, error) {

            $('.telefon-response').html(request.responseText);
            $('.telefon-response').addClass('error');
        });

        return false;

    });

    $('.telefon-response').click(function(){
        $('.telefon-response').removeClass('done error');
        $('.telefon-response').html();
    })

});

