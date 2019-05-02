(function ($) {
    "use strict";
    var count = 1;

    setToltip();
    fixForFooterNoContent();
    fixForBlogThumbnailSize();
    fixTeamLayout();
    imageSliderSettings();
    textSliderSettings();
    newsBackgroundImages();
    skillsFill();
    portfolioItemContentLoadOnClick();
    fixForMenu();
    singlePostStickyInfo();
    slowScroll();
    logoClickFix();
    placeholderShowHide();
    fitVideo();
    firstSectionActiveFix();
    setMenu();
   // SendMail();

    //Show-Hide header sidebar
    $('#toggle').on('click', multiClickFunctionStop);

    $(window).on('load', function () {
        isotopeSetUp();
        setUpParallax();
        hashFix();
        $('.doc-loader').fadeOut(600);
    });

    $(window).on('resize', function () {
        fixForBlogThumbnailSize();
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

    function setToltip() {
        $(".tooltip").tipper({
            direction: "top",
            follow: true
        });
    }

    function fixForFooterNoContent() {
        if (($('.footer-content').html().replace(/\s/g, '') == '') || ($('.footer-content').html().replace(/\s/g, '') == '<divclass="footer-logo-divider"></div><divclass="footer-social-divider"></div>'))
        {
            $('.footer').addClass('hidden');
        }
    }

    function fixForBlogThumbnailSize() {
        $('.blog-holder .blog-item-holder.has-post-thumbnail').each(function () {
            if ($(this).find('.post-thumbnail').height() > ($(this).find('.entry-holder').innerHeight() + 80)) {
                $(this).addClass('is-smaller');
                $(this).find('.post-thumbnail img').height($(this).find('.entry-holder').innerHeight() + 80);
            }
        });
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
            })
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

    function setUpParallax() {
        $('[data-jarallax-element]').jarallax({
            speed: 0.2
        });
    }

    function destroyParallax() {
        $('[data-jarallax-element]').jarallax('destroy');
    }

    function fixTeamLayout() {
        if ($(window).width() < 1000) {
            $('.member-right').each(function () {
                if (!$(this).hasClass('small-screen')) {
                    $(this).addClass('small-screen').removeClass('big-screen').find('img').insertBefore($(this).find('.member-info'));
                }
            });
        } else
        {
            $('.member-right').each(function () {
                if (!$(this).hasClass('big-screen')) {
                    $(this).addClass('big-screen').removeClass('small-screen').find('.member-info').insertBefore($(this).find('img'));
                }
            });
        }
    }

    function newsBackgroundImages() {
        $(".latest-posts-background-featured-image-holder").each(function () {
            $(this).css('background-image', 'url(' + ($(this).data("background-image") + ')'));
        });
    }

    function portfolioItemContentLoadOnClick() {
        $('.ajax-portfolio').on('click', function (e) {
            e.preventDefault();
            var portfolioItemID = $(this).data('id');
            $(this).addClass('animate-plus');
            if ($("#pcw-" + portfolioItemID).length) //Check if is allready loaded
            {
                $('html, body').animate({scrollTop: $('#portfolio-wrapper').offset().top}, 400);
                setTimeout(function () {
                    $('#portfolio-grid, .more-posts-portfolio-holder').addClass('hide');
                    setTimeout(function () {
                        $("#pcw-" + portfolioItemID).addClass('show');
                        $('.portfolio-load-content-holder').addClass('show');
                        $('.ajax-portfolio').removeClass('animate-plus');
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
                $('html, body').animate({scrollTop: $('#portfolio-wrapper').offset().top}, 400);
                setTimeout(function () {
                    $("#pcw-" + portfolioItemID).imagesLoaded(function () {
                        skillsFill();
                        imageSliderSettings();
                        $(".site-content").fitVids(); //Fit Video
                        $('#portfolio-grid, .more-posts-portfolio-holder').addClass('hide');
                        setTimeout(function () {
                            $("#pcw-" + portfolioItemID).addClass('show');
                            $('.portfolio-load-content-holder').addClass('show');
                            $('.ajax-portfolio').removeClass('animate-plus');
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
                                $('html, body').animate({scrollTop: $('#p-item-' + portfolioReturnItemID).offset().top}, 400);
                            }, 500);
                        });
                    });
                }, 500);
            }
        });
        return false;
    }

    function skillsFill() {
        $(".skill-fill").each(function () {
            $(this).width($(this).data("fill"));
        });
    }

    function fixForMenu() {
        $(".header-holder").sticky({topSpacing: 0});
    }

    function singlePostStickyInfo() {
        $(".single-post .entry-info").stick_in_parent({offset_top: 120, parent: ".single-content-wrapper", spacer: ".sticky-spacer"});
    }

    function slowScroll() {
        $('#header-main-menu ul li a[href^="#"], a.button, a.button-dot, .slow-scroll').on("click", function (e) {
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
    }

    function logoClickFix() {
        $('.header-logo').on("click", function (e) {
            if ($(".page-template-onepage").length) {
                e.preventDefault();
                $('html, body').animate({scrollTop: 0}, 1500);
            }
        });
    }

    function placeholderShowHide() {
        $('input, textarea').on("focus", function () {
            $(this).data('placeholder', $(this).attr('placeholder'));
            $(this).attr('placeholder', '');
        });
        $('input, textarea').on("blur", function () {
            $(this).attr('placeholder', $(this).data('placeholder'));
        });
    }

    function fitVideo() {
        $(".site-content, .portfolio-item-wrapper").fitVids({ignore: '.wp-block-embed__wrapper'});
    }

    function hashFix() {
        var hash = location.hash;
        if ((hash != '') && ($(hash).length))
        {
            $('html, body').animate({scrollTop: $(hash).offset().top - 77}, 1);
        }
    }

    function firstSectionActiveFix() {
        $(window).scrollTop(1);
        $(window).scrollTop(0);
    }

    function setMenu() {
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
    }


})(jQuery);