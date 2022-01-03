(function ($) {

    "use strict";

    var fullHeight = function () {

        $('.js-fullheight').css('height', $(window).height());
        $(window).resize(function () {
            $('.js-fullheight').css('height', $(window).height());
        });

    };
    fullHeight();

    $('#sidebarCollapse').on('click', function () {
        $('#sidebar').toggleClass('active');
    });

    $('#visualsidebarCollapse').on('click', function () {
        if (($('#sidebar')).hasClass('visualActive')) {
            $("#graphs").css('margin-left', '0');
            $("#graphs").css('width', '85%');
        } else {
            $("#graphs").css('margin-left', '3%');
            $("#graphs").css('width', '100%');
        }
        $('#sidebar').toggleClass('visualActive');

    });

})(jQuery);
