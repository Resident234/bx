$(function () {

    (function () {

        var pageIsLoading = false;
        var lastPoint = 0;
        var paginationContainerForTrigger = $('.js-pagination-container');
        var link = paginationContainerForTrigger.find('.ajax-link');

        $('body').on('click', '.ajax-link', function (e) {
            e.preventDefault();
            ajaxLoadContent($(this));
        });

        $(document).scroll(function () {
            var targetPos = paginationContainerForTrigger.offset().top;
            var winHeight = $(window).height();
            var winScrollTop = $(document).scrollTop();
            var scrollToElem = targetPos - winHeight
            if ((typeof(pageIsLoading) != 'undefined') && pageIsLoading) {
                return false;
            }
            if (lastPoint > winScrollTop) {
                lastPoint = winScrollTop;
                return false;
            }
            lastPoint = winScrollTop;
            if (winScrollTop >= (scrollToElem + ($(window).height() / 3))) {
                /** когда кнопка окажется на трети видимой части экрана,
                 * считая снизу, то сработает догрузка */
                if ($('.ajax-link').length) {
                    ajaxLoadContent(link);
                }
            }

        });

        function ajaxLoadContent($link){
            var link = $link;
            var strTitleNormal = link.data("title-normal");
            var strTitleLoading = link.data("title-loading");

            pageIsLoading = true;
            $(".js-preloader-Wait").show();
            link.html(strTitleLoading);

            $.get(link.attr('href'), function (data) {

                $(".js-preloader-Wait").hide();
                link.html(strTitleNormal);
                pageIsLoading = false;

                var response = $(data);
                $('.ajax-list').append(response.find('.ajax-list').html());
                if (response.find('.ajax-link').length) {
                    link.attr('href', response.find('.ajax-link').attr('href'));
                }
                else {
                    link.remove();
                }
            });
        }

    })();


});