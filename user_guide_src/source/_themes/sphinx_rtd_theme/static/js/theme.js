$(document).ready(function () {
    // Shift nav in mobile when clicking the menu.
    $(document).on('click', "[data-toggle='wy-nav-top']", function () {
        $("[data-toggle='wy-nav-shift']").toggleClass("shift");
        $("[data-toggle='rst-versions']").toggleClass("shift");
    });
    // Close menu when you click a link.
    $(document).on('click', ".wy-menu-vertical .current ul li a", function () {
        $("[data-toggle='wy-nav-shift']").removeClass("shift");
        $("[data-toggle='rst-versions']").toggleClass("shift");
    });
    $(document).on('click', "[data-toggle='rst-current-version']", function () {
        $("[data-toggle='rst-versions']").toggleClass("shift-up");
    });
    // Make tables responsive
    $("table.docutils:not(.field-list)").wrap("<div class='wy-table-responsive'></div>");
    // ---
    // START DOC MODIFICATION BY RUFNEX
    // v1.0 04.02.2015
    // Add ToogleButton to get FullWidth-View by Johannes Gamperl codeigniter.de

    $('#openToc').click(function () {
        $('#nav').slideToggle();
    });
    $('#closeMe').toggle(
        function ()
        {
            setCookie('ciNav', true, 365);
            $('#nav2').show();
            $('#topMenu').remove();
            $('body').css({background: 'none'});
            $('.wy-nav-content-wrap').css({background: 'none', 'margin-left': 0});
            $('.wy-breadcrumbs').append('<div style="float:right;"><div style="float:left;" id="topMenu">' + $('.wy-form').parent().html() + '</div></div>');
            $('.wy-nav-side').toggle();
        },
        function ()
        {
            setCookie('ciNav', false, 365);
            $('#topMenu').remove();
            $('#nav').hide();
            $('#nav2').hide();
            $('body').css({background: '#edf0f2;'});
            $('.wy-nav-content-wrap').css({background: 'none repeat scroll 0 0 #fcfcfc;', 'margin-left': '300px'});
            $('.wy-nav-side').show();
        }
    );
    if (getCookie('ciNav') == 'true')
    {
        $('#closeMe').trigger('click');
        //$('#nav').slideToggle();
    }
    // END MODIFICATION ---
});

// Rufnex Cookie functions
function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    var expires = "expires=" + d.toGMTString();
    document.cookie = cname + "=" + cvalue + "; " + expires;
}
function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ')
            c = c.substring(1);
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return false;
}
// End

window.SphinxRtdTheme = (function (jquery) {
    var stickyNav = (function () {
        var navBar,
                win,
                stickyNavCssClass = 'stickynav',
                applyStickNav = function () {
                    if (navBar.height() <= win.height()) {
                        navBar.addClass(stickyNavCssClass);
                    } else {
                        navBar.removeClass(stickyNavCssClass);
                    }
                },
                enable = function () {
                    applyStickNav();
                    win.on('resize', applyStickNav);
                },
                init = function () {
                    navBar = jquery('nav.wy-nav-side:first');
                    win = jquery(window);
                };
        jquery(init);
        return {
            enable: enable
        };
    }());
    return {
        StickyNav: stickyNav
    };
}($));
