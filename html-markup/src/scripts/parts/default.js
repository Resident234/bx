$(function () {
    $('body').on('click', '.ajax-link', function (e) {
        e.preventDefault();
        $(".js-preloader-Wait").show();
        var link = $(this);
        $.get(link.attr('href'), function (data) {
            $(".js-preloader-Wait").hide();
            var response = $(data);
            $('.ajax-list').append(response.find('.ajax-list').html());
            if (response.find('.ajax-link').length) {
                link.attr('href', response.find('.ajax-link').attr('href'));
            }
            else {
                link.remove();
            }
        });
    });

});