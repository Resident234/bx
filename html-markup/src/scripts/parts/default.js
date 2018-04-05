$(function () {
    $('body').on('click', '.ajax-link', function (e) {
        e.preventDefault();
        var link = $(this);
        var strTitleNormal = link.data("title-normal");
        var strTitleLoading = link.data("title-loading");

        $(".js-preloader-Wait").show();
        link.html(strTitleLoading);

        $.get(link.attr('href'), function (data) {

            $(".js-preloader-Wait").hide();
            link.html(strTitleNormal);

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