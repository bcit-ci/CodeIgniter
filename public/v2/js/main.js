(function ($) {
    "use strict";

    var count = 1;    

    portfolioItemContentLoadOnClick();
    
    fixTeamLayout();
    fixNewsBackgroundImages();
    skillsFill();
    imageSliderSettings();
    textSliderSettings();
    zIndexSectionFix();
    //SendMail();


    //Fix for Menu
    $(".header-holder").sticky({topSpacing: 0});

    //Slow Scroll
    $('#header-main-menu ul li a[href^="#"], a.button, .slow-scroll').on("click", function (e) {
        if ($(this).attr('href') === '#')
        {
            e.preventDefault();
        } else {
            if ($(window).width() < 1024) {
                if (!$(e.target).is('.sub-arrow'))
                {
                    $('html, body').animate({scrollTop: $(this.hash).offset().top - 76}, 1500);
                    $('.menu-holder').removeClass('show');
                    $('#toggle').removeClass('on');
                    return false;
                }
            } else
            {
                $('html, body').animate({scrollTop: $(this.hash).offset().top - 76}, 1500);
                return false;
            }
        }
    });

    //Logo Click Fix
    $('.header-logo').on("click", function (e) {
        if ($(".page-template-onepage").length) {
            e.preventDefault();
            $('html, body').animate({scrollTop: 0}, 1500);
        }
    });

    $('.single-post .num-comments a').on('click', function (e) {
        e.preventDefault();
        $('html, body').animate({scrollTop: $(this.hash).offset().top - 76}, 1500);
        return false;
    });


    //Placeholder show/hide
    $('input, textarea').on("focus", function () {
        $(this).data('placeholder', $(this).attr('placeholder'));
        $(this).attr('placeholder', '');
    });
    $('input, textarea').on("blur", function () {
        $(this).attr('placeholder', $(this).data('placeholder'));
    });

    //Fit Video
    $(".site-content, .portfolio-item-wrapper").fitVids();

    //Set menu
    $('.main-menu').smartmenus({
        subMenusSubOffsetX: 1,
        subMenusSubOffsetY: -8,
        markCurrentTree: true
    });

    var $mainMenu = $('.main-menu').on('click', 'span.sub-arrow', function (e) {
        var obj = $mainMenu.data('smartmenus');
        if (obj.isCollapsible()) {
            var $item = $(this).parent(),
                    $sub = $item.parent().dataSM('sub');
            $sub.dataSM('arrowClicked', true);
        }
    }).bind({
        'beforeshow.smapi': function (e, menu) {
            var obj = $mainMenu.data('smartmenus');
            if (obj.isCollapsible()) {
                var $menu = $(menu);
                if (!$menu.dataSM('arrowClicked')) {
                    return false;
                }
                $menu.removeDataSM('arrowClicked');
            }
        }
    });

    //Show-Hide header sidebar
    $('#toggle').on('click', multiClickFunctionStop);

    $(window).on('load', function () {
        isotopeSetUp();

        //Fix for hash
        var hash = location.hash;
        if ((hash != '') && ($(hash).length))
        {
            $('html, body').animate({scrollTop: $(hash).offset().top - 76}, 1);
            $('html, body').animate({scrollTop: $(hash).offset().top - 76}, 1);
        } else {
            $(window).scrollTop(1);
            $(window).scrollTop(0);
        }


        $('.doc-loader').fadeOut(600);
    });


    $(window).on('resize', function () {
        setActiveMenuItem();
        fixTeamLayout();
    });

    $(window).on('scroll', function () {
        setActiveMenuItem();
    });





//------------------------------------------------------------------------
//Helper Methods -->
//------------------------------------------------------------------------


    function multiClickFunctionStop() {
        $('#toggle').off("click");
        $('#toggle').toggleClass("on");
        if ($('#toggle').hasClass("on"))
        {
            $('.menu-holder').addClass('show');
            $('#toggle').on("click", multiClickFunctionStop);
        } else
        {
            $('.menu-holder').removeClass('show');
            $('#toggle').on("click", multiClickFunctionStop);
        }
    }    

    function is_touch_device() {
        return !!('ontouchstart' in window);
    }

    function setActiveMenuItem() {
        var currentSection = null;
        $('.section').each(function () {
            var element = $(this).attr('id');
            if ($('#' + element).is('*')) {
                if ($(window).scrollTop() >= $('#' + element).offset().top - 115)
                {
                    currentSection = element;
                }
            }
        });
        $('#header-main-menu ul li').removeClass('active').find('a[href*="#' + currentSection + '"]').parent().addClass('active');
    }

    function isotopeSetUp() {
        $('.grid').isotope({
            itemSelector: '.grid-item',
            masonry: {
                columnWidth: '.grid-sizer'
            }
        });
    }

    function imageSliderSettings() {
        $(".image-slider").each(function () {
            var id = $(this).attr('id');
            var auto_value = window[id + '_auto'];                        
            var hover_pause = window[id + '_hover'];
            var speed_value = window[id + '_speed'];
            auto_value = (auto_value === 'true') ? true : false;
            hover_pause = (hover_pause === 'true') ? true : false;
            $('#' + id).owlCarousel({
                loop: true,
                autoHeight: true,
                smartSpeed: 1000,
                autoplay: auto_value,
                autoplayHoverPause: hover_pause,
                autoplayTimeout: speed_value,
                responsiveClass: true,
                items: 1
            });

            $(this).on('mouseleave', function () {
                $(this).trigger('stop.owl.autoplay');
                $(this).trigger('play.owl.autoplay', [auto_value]);
            });
        });
    }

    function textSliderSettings() {
        $(".text-slider").each(function () {
            var id = $(this).attr('id');
            var auto_value = window[id + '_auto'];
            var hover_pause = window[id + '_hover'];
            var speed_value = window[id + '_speed'];
            auto_value = (auto_value === 'true') ? true : false;
            hover_pause = (hover_pause === 'true') ? true : false;
            $('#' + id).owlCarousel({
                loop: true,
                autoHeight: false,
                smartSpeed: 1000,
                autoplay: auto_value,
                autoplayHoverPause: hover_pause,
                autoplayTimeout: speed_value,
                responsiveClass: true,
                dots: false,
                animateIn: 'fadeIn',
                animateOut: 'fadeOut',
                nav: true,
                items: 1
            });
        });
    }    

    function portfolioItemContentLoadOnClick() {
        $('.ajax-portfolio').on('click', function (e) {
            e.preventDefault();
            var portfolioItemID = $(this).data('id');
            $(this).addClass('loading-portfolio-item-content');
            if ($("#pcw-" + portfolioItemID).length) //Check if is allready loaded
            {
                $('html, body').animate({scrollTop: $('#portfolio-wrapper').offset().top - 150}, 400);
                setTimeout(function () {
                    $('#portfolio-grid, .more-posts-portfolio-holder').addClass('hide');
                    setTimeout(function () {
                        $("#pcw-" + portfolioItemID).addClass('show');
                        $('.portfolio-load-content-holder').addClass('show');
                        $('.ajax-portfolio').removeClass('loading-portfolio-item-content');
                        $('#portfolio-grid, .more-posts-portfolio-holder').hide();
                    }, 300);
                }, 500);
            } else {
                loadPortfolioItemContent(portfolioItemID);
            }
        });
    }

    function loadPortfolioItemContent(portfolioItemID) {
        $.ajax({
            url: $('.ajax-portfolio[data-id="' + portfolioItemID + '"]').attr('href'),
            type: 'POST',
            success: function (html) {
                var getPortfolioItemHtml = $(html).find(".portfolio-item-wrapper").html();
                $('.portfolio-load-content-holder').append('<div id="pcw-' + portfolioItemID + '" class="portfolio-content-wrapper">' + getPortfolioItemHtml + '</div>');
                if (!$("#pcw-" + portfolioItemID + " .close-icon").length) {
                    $("#pcw-" + portfolioItemID).prepend('<div class="close-icon"></div>');
                }
                $('html, body').animate({scrollTop: $('#portfolio-wrapper').offset().top - 150}, 400);
                setTimeout(function () {
                    $("#pcw-" + portfolioItemID).imagesLoaded(function () {
                        imageSliderSettings();
                        $(".site-content").fitVids(); //Fit Video
                        $('#portfolio-grid, .more-posts-portfolio-holder').addClass('hide');
                        setTimeout(function () {
                            $("#pcw-" + portfolioItemID).addClass('show');
                            $('.portfolio-load-content-holder').addClass('show');
                            $('.ajax-portfolio').removeClass('loading-portfolio-item-content');
                            $('#portfolio-grid').hide();
                        }, 300);
                        $('.close-icon').on('click', function (e) {
                            var portfolioReturnItemID = $(this).closest('.portfolio-content-wrapper').attr("id").split("-")[1];
                            $('.portfolio-load-content-holder').addClass("viceversa");
                            $('#portfolio-grid, .more-posts-portfolio-holder').css('display', 'block');
                            setTimeout(function () {
                                $('#pcw-' + portfolioReturnItemID).removeClass('show');
                                $('.portfolio-load-content-holder').removeClass('viceversa show');
                                $('#portfolio-grid, .more-posts-portfolio-holder').removeClass('hide');
                            }, 300);
                            setTimeout(function () {
                                $('html, body').animate({scrollTop: $('#p-item-' + portfolioReturnItemID).offset().top - 150}, 400);
                            }, 500);
                        });
                    });
                }, 500);
            }
        });
        return false;
    }

    function fixNewsBackgroundImages() {
        $(".latest-posts-background-featured-image-holder").each(function () {
            $(this).css('background-image', 'url(' + ($(this).data("image") + ')'));
        });
    }

    function fixTeamLayout() {
        if ($(window).width() < 1000) {
            $('.member-right').each(function () {
                if (!$(this).hasClass('small-screen')) {
                    $(this).addClass('small-screen').removeClass('big-screen').find('.member-image-holder').insertBefore($(this).find('.member-info'));
                }
            });
        } else
        {
            $('.member-right').each(function () {
                if (!$(this).hasClass('big-screen')) {
                    $(this).addClass('big-screen').removeClass('small-screen').find('.member-info').insertBefore($(this).find('.member-image-holder'));
                }
            });
        }

    }

    function skillsFill() {
        $(".skill-fill").each(function () {
            $(this).width($(this).data("fill")).height($(this).data("fill"));
        });
    }

    function zIndexSectionFix() {
        var numSection = $(".page-template-onepage .section-wrapper").length + 2;
        $('.page-template-onepage').find('.section-wrapper').each(function () {
            $(this).css('zIndex', numSection);
            numSection--;
        });
    }


    function isValidEmailAddress(emailAddress) {
        var pattern = /^([a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+(\.[a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)*|"((([ \t]*\r\n)?[ \t]+)?([\x01-\x08\x0b\x0c\x0e-\x1f\x7f\x21\x23-\x5b\x5d-\x7e\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|\\[\x01-\x09\x0b\x0c\x0d-\x7f\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))*(([ \t]*\r\n)?[ \t]+)?")@(([a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.)+([a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.?$/i;
        return pattern.test(emailAddress);
    }

    function SendMail() {
        $('.contact-form [type="submit"]').on('click', function () {
            var emailVal = $('#contact-email').val();
            if (isValidEmailAddress(emailVal)) {
                var params = {
                    'action': 'SendMessage',
                    'name': $('#name').val(),
                    'email': $('#contact-email').val(),
                    'subject': $('#subject').val(),
                    'message': $('#message').val()
                };
                $.ajax({
                    type: "POST",
                    url: "php/sendMail.php",
                    data: params,
                    success: function (response) {
                        if (response) {
                            var responseObj = $.parseJSON(response);
                            if (responseObj.ResponseData)
                            {
                                alert(responseObj.ResponseData);
                            }
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        //xhr.status : 404, 303, 501...
                        var error = null;
                        switch (xhr.status)
                        {
                            case "301":
                                error = "Redirection Error!";
                                break;
                            case "307":
                                error = "Error, temporary server redirection!";
                                break;
                            case "400":
                                error = "Bad request!";
                                break;
                            case "404":
                                error = "Page not found!";
                                break;
                            case "500":
                                error = "Server is currently unavailable!";
                                break;
                            default:
                                error = "Unespected error, please try again later.";
                        }
                        if (error) {
                            alert(error);
                        }
                    }
                });
            } else
            {
                alert('Your email is not in valid format');
            }
        });
    }

})(jQuery);