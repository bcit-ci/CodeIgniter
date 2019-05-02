/// <reference path="../../../toastr.js" />
/// <reference path="../qunit/qunit.js" />
(function () {
    var iconClasses = {
        error: 'toast-error',
        info: 'toast-info',
        success: 'toast-success',
        warning: 'toast-warning'
    };
    var positionClasses = {
        topRight: 'toast-top-right',
        bottomRight: 'toast-bottom-right',
        bottomLeft: 'toast-bottom-left',
        topLeft: 'toast-top-left',
        topCenter: 'toast-top-center',
        bottomCenter: 'toast-bottom-center'
    };
    var sampleMsg = 'I don\'t think they really exist';
    var sampleTitle = 'TEST';
    var selectors = {
        container: 'div#toast-container',
        toastInfo: 'div#toast-container > div.toast-info',
        toastWarning: 'div#toast-container > div.toast-success',
        toastError: 'div#toast-container > div.toast-error',
        toastSuccess: 'div#toast-container > div.toast-success'
    };

    toastr.options = {
        timeOut: 2000,
        extendedTimeOut: 0,
        fadeOut: 0,
        fadeIn: 0,
        showDuration: 0,
        hideDuration: 0,
        debug: false
    };

    var delay = toastr.options.timeOut + 500;

    // 'Clears' must go first
    module('clear');
    asyncTest('clear - show 3 toasts, clear the 2nd', 1, function () {
        //Arrange
        var $toast = [];
        $toast[0] = toastr.info(sampleMsg, sampleTitle + '-1');
        $toast[1] = toastr.info(sampleMsg, sampleTitle + '-2');
        $toast[2] = toastr.info(sampleMsg, sampleTitle + '-3');
        var $container = toastr.getContainer();
        //Act
        toastr.clear($toast[1]);
        //Assert
        setTimeout(function () {
            ok($container && $container.children().length === 2);
            //Teardown
            resetContainer();
            start();
        }, 1000);
    });
    asyncTest('clear - show 3 toasts, clear all 3, 0 left', 1, function () {
        //Arrange
        var $toast = [];
        $toast[0] = toastr.info(sampleMsg, sampleTitle + '-1');
        $toast[1] = toastr.info(sampleMsg, sampleTitle + '-2');
        $toast[2] = toastr.info(sampleMsg, sampleTitle + '-3');
        var $container = toastr.getContainer();
        //Act
        toastr.clear();
        //Assert
        setTimeout(function () {
            ok($container && $container.children().length === 0);
            //Teardown
            resetContainer();
            start();
        }, delay);
    });
    test('clear - after clear with force option toast with focus disappears', 1, function () {
        //Arrange
        var $toast;
        var msg = sampleMsg + '<br/><br/><button type="button">Clear</button>';
        //Act
        $toast = toastr.info(msg, sampleTitle + '-1');
        $toast.find('button').focus();
        toastr.clear($toast, { force: true });
        var $container = toastr.getContainer();
        //Assert
        ok($container && $container.children().length === 0, 'Focused toast after a clear with force is not visible');
        //Teardown
        resetContainer();
    });
    asyncTest('clear and show - show 2 toasts, clear both, then show 1 more', 2, function () {
        //Arrange
        var $toast = [];
        $toast[0] = toastr.info(sampleMsg, sampleTitle + '-1');
        $toast[1] = toastr.info(sampleMsg, sampleTitle + '-2');
        var $container = toastr.getContainer();
        toastr.clear();
        //Act
        setTimeout(function () {
            $toast[2] = toastr.info(sampleMsg, sampleTitle + '-3-Visible');
            //Assert
            equal($toast[2].find('div.toast-title').html(), sampleTitle + '-3-Visible', 'Finds toast after a clear');
            ok($toast[2].is(':visible'), 'Toast after a clear is visible');
            //Teardown
            resetContainer();
            start();
        }, delay);
    });
    asyncTest('clear and show - clear removes toast container', 2, function () {
        //Arrange
        var $toast = [];
        $toast[0] = toastr.info(sampleMsg, sampleTitle + '-1');
        $toast[1] = toastr.info(sampleMsg, sampleTitle + '-2');
        var $container = toastr.getContainer();
        toastr.clear();
        //Act
        setTimeout(function () {
            //Assert
            equal($(selectors.container).length, 0, 'Toast container does not exist');
            ok(!$toast[1].is(':visible'), 'Toast after a clear is visible');
            //Teardown
            resetContainer();
            start();
        }, delay);
    });
    asyncTest('clear and show - after clear new toast creates container', 1, function () {
        //Arrange
        var $toast = [];
        $toast[0] = toastr.info(sampleMsg, sampleTitle + '-1');
        $toast[1] = toastr.info(sampleMsg, sampleTitle + '-2');
        var $container = toastr.getContainer();
        toastr.clear();
        //Act
        setTimeout(function () {
            $toast[2] = toastr.info(sampleMsg, sampleTitle + '-3-Visible');
            //Assert
            equal($(selectors.container).find('div.toast-title').html(), sampleTitle + '-3-Visible', 'Finds toast after a clear'); //Teardown
            resetContainer();
            start();
        }, delay);
    });
    asyncTest('clear and show - clear toast after hover', 1, function () {
        //Arrange
        var $toast = toastr.info(sampleMsg, sampleTitle);
        var $container = toastr.getContainer();
        $toast.trigger("mouseout");
        //Act
        setTimeout(function () {
            //Assert
            ok($container.find('div.toast-title').length === 0, 'Toast clears after a mouse hover'); //Teardown
            resetContainer();
            start();
        }, 500);
    });
    asyncTest('clear and show - do not clear toast after hover', 1, function () {
        //Arrange
        var $toast = toastr.info(sampleMsg, sampleTitle, { closeOnHover: false });
        var $container = toastr.getContainer();
        $toast.trigger("mouseout");
        //Act
        setTimeout(function () {
            //Assert
            ok($container.find('div.toast-title').length === 1, 'Toast does not clear after a mouse hover'); //Teardown
            resetContainer();
            start();
        }, 500);
    });
    test('clear and show - after clear all toasts new toast still appears', 1, function () {
        //Arrange
        var $toast = [];
        //Act
        $toast[0] = toastr.info(sampleMsg, sampleTitle + '-1');
        $toast[1] = toastr.info(sampleMsg, sampleTitle + '-2');
        toastr.clear();
        $toast[2] = toastr.info(sampleMsg, sampleTitle + '-3-Visible');
        //Assert
        ok($toast[2].is(':visible'), 'Toast after a clear is visible');
        //Teardown
        resetContainer();
    });
    module('info');
    test('info - pass title and message', 3, function () {
        //Arrange
        //Act
        var $toast = toastr.info(sampleMsg, sampleTitle);
        //Assert
        equal($toast.find('div.toast-title').html(), sampleTitle, 'Sets title');
        equal($toast.find('div.toast-message').html(), sampleMsg, 'Sets message');
        ok($toast.hasClass(iconClasses.info), 'Sets info icon');
        //Teardown
        $toast.remove();
        clearContainerChildren();
    });
    test('info - pass message, but no title', 3, function () {
        //Arrange
        //Act
        var $toast = toastr.info(sampleMsg);
        //Assert
        equal($toast.find('div.toast-title').length, 0, 'Sets null title');
        equal($toast.find('div.toast-message').html(), sampleMsg, 'Sets message');
        ok($toast.hasClass(iconClasses.info), 'Sets info icon');
        //Teardown
        $toast.remove();
        clearContainerChildren();
    });
    test('info - pass no message nor title', 3, function () {
        //Arrange
        //Act
        var $toast = toastr.info(); //Assert
        equal($toast.find('div.toast-title').length, 0, 'Sets null title');
        equal($toast.find('div.toast-message').html(), null, 'Sets message');
        ok($toast.hasClass(iconClasses.info), 'Sets info icon');
        //Teardown
        $toast.remove();
        clearContainerChildren();
    });
    module('warning');
    test('warning - pass message and title', 3, function () {
        //Arrange
        //Act
        var $toast = toastr.warning(sampleMsg, sampleTitle);
        //Assert
        equal($toast.find('div.toast-title').html(), sampleTitle, 'Sets title');
        equal($toast.find('div.toast-message').html(), sampleMsg, 'Sets message');
        ok($toast.hasClass(iconClasses.warning), 'Sets warning icon');
        //Teardown
        $toast.remove();
        clearContainerChildren();
    });
    test('warning - pass message, but no title', 3, function () {
        //Arrange
        //Act
        var $toast = toastr.warning(sampleMsg);
        //Assert
        equal($toast.find('div.toast-title').length, 0, 'Sets empty title');
        equal($toast.find('div.toast-message').html(), sampleMsg, 'Sets message');
        ok($toast.hasClass(iconClasses.warning), 'Sets warning icon');
        //Teardown
        $toast.remove();
        clearContainerChildren();
    });
    test('warning - no message nor title', 3, function () {
        //Arrange
        //Act
        var $toast = toastr.warning('');
        //Assert
        equal($toast.find('div.toast-title').length, 0, 'Sets null title');
        equal($toast.find('div.toast-message').length, 0, 'Sets empty message');
        ok($toast.hasClass(iconClasses.warning), 'Sets warning icon');
        //Teardown
        $toast.remove();
        clearContainerChildren();
    });
    module('error');
    test('error - pass message and title', 3, function () {
        //Arrange
        //Act
        var $toast = toastr.error(sampleMsg, sampleTitle);
        //Assert
        equal($toast.find('div.toast-title').html(), sampleTitle, 'Sets title');
        equal($toast.find('div.toast-message').html(), sampleMsg, 'Sets message');
        ok($toast.hasClass(iconClasses.error), 'Sets error icon');
        //Teardown
        $toast.remove();
        clearContainerChildren();
    });
    test('error - pass message, but no title', 3, function () {
        //Arrange
        //Act
        var $toast = toastr.error(sampleMsg); //Assert
        equal($toast.find('div.toast-title').length, 0, 'Sets empty title');
        equal($toast.find('div.toast-message').html(), sampleMsg, 'Sets message');
        ok($toast.hasClass(iconClasses.error), 'Sets error icon');
        //Teardown
        $toast.remove();
        clearContainerChildren();
    });
    test('error - no message nor title', 3, function () {
        //Arrange
        //Act
        var $toast = toastr.error('');
        //Assert
        equal($toast.find('div.toast-title').length, 0, 'Sets empty title');
        equal($toast.find('div.toast-message').length, 0, 'Sets empty message');
        ok($toast.hasClass(iconClasses.error), 'Sets error icon');
        //Teardown
        $toast.remove();
        clearContainerChildren();
    });
    module('success');
    test('success - pass message and title', 3, function () {
        //Arrange
        //Act
        var $toast = toastr.success(sampleMsg, sampleTitle);
        //Assert
        equal($toast.find('div.toast-title').html(), sampleTitle, 'Sets title');
        equal($toast.find('div.toast-message').html(), sampleMsg, 'Sets message');
        ok($toast.hasClass(iconClasses.success), 'Sets success icon');
        //Teardown
        $toast.remove();
        clearContainerChildren();
    });
    test('success - pass message, but no title', 3, function () {
        //Arrange
        //Act
        var $toast = toastr.success(sampleMsg);
        //Assert
        equal($toast.find('div.toast-title').length, 0, 'Sets empty title');
        equal($toast.find('div.toast-message').html(), sampleMsg, 'Sets message');
        ok($toast.hasClass(iconClasses.success), 'Sets success icon');
        //Teardown
        $toast.remove();
        clearContainerChildren();
    });
    test('success - no message nor title', 3, function () {
        //Arrange
        //Act
        var $toast = toastr.success('');
        //Assert
        equal($toast.find('div.toast-title').length, 0, 'Sets null title');
        equal($toast.find('div.toast-message').length, 0, 'Sets empty message');
        ok($toast.hasClass(iconClasses.success), 'Sets success icon'); //Teardown
        $toast.remove();
        clearContainerChildren();
    });


    module('escape html', {
        teardown: function () {
            toastr.options.escapeHtml = false;
        }
    });
    test('info - escape html', 2, function () {
        //Arrange
        toastr.options.escapeHtml = true;
        //Act
        var $toast = toastr.info('html <strong>message</strong>', 'html <u>title</u>');
        //Assert
        equal($toast.find('div.toast-title').html(), 'html &lt;u&gt;title&lt;/u&gt;', 'Title is escaped');
        equal($toast.find('div.toast-message').html(), 'html &lt;strong&gt;message&lt;/strong&gt;', 'Message is escaped');
        //Teardown
        $toast.remove();
        clearContainerChildren();
    });
    test('warning - escape html', 2, function () {
        //Arrange
        toastr.options.escapeHtml = true;
        //Act
        var $toast = toastr.warning('html <strong>message</strong>', 'html <u>title</u>');
        //Assert
        equal($toast.find('div.toast-title').html(), 'html &lt;u&gt;title&lt;/u&gt;', 'Title is escaped');
        equal($toast.find('div.toast-message').html(), 'html &lt;strong&gt;message&lt;/strong&gt;', 'Message is escaped');
        //Teardown
        $toast.remove();
        clearContainerChildren();
    });
    test('error - escape html', 2, function () {
        //Arrange
        toastr.options.escapeHtml = true;
        //Act
        var $toast = toastr.error('html <strong>message</strong>', 'html <u>title</u>');
        //Assert
        equal($toast.find('div.toast-title').html(), 'html &lt;u&gt;title&lt;/u&gt;', 'Title is escaped');
        equal($toast.find('div.toast-message').html(), 'html &lt;strong&gt;message&lt;/strong&gt;', 'Message is escaped');
        //Teardown
        $toast.remove();
        clearContainerChildren();
    });
    test('success - escape html', 2, function () {
        //Arrange
        toastr.options.escapeHtml = true;
        //Act
        var $toast = toastr.success('html <strong>message</strong>', 'html <u>title</u>');
        //Assert
        equal($toast.find('div.toast-title').html(), 'html &lt;u&gt;title&lt;/u&gt;', 'Title is escaped');
        equal($toast.find('div.toast-message').html(), 'html &lt;strong&gt;message&lt;/strong&gt;', 'Message is escaped');
        //Teardown
        $toast.remove();
        clearContainerChildren();
    });

    module('closeButton', {
        teardown: function () {
            toastr.options.closeButton = false;
        }
    });
    test('close button disabled', 1, function () {
        //Arrange
        toastr.options.closeButton = false;
        //Act
        var $toast = toastr.success('');
        //Assert
        equal($toast.find('button.toast-close-button').length, 0, 'close button should not exist with closeButton=false');
        //Teardown
        $toast.remove();
        clearContainerChildren();
    });
    test('close button enabled', 1, function () {
        //Arrange
        toastr.options.closeButton = true;
        //Act
        var $toast = toastr.success('');
        //Assert
        equal($toast.find('button.toast-close-button').length, 1, 'close button should exist with closeButton=true');
        //Teardown
        $toast.remove();
        clearContainerChildren();
    });
    test('close button has type=button', 1, function () {
        //Arrange
        toastr.options.closeButton = true;
        //Act
        var $toast = toastr.success('');
        //Assert
        equal($toast.find('button[type="button"].toast-close-button').length, 1, 'close button should have type=button');
        //Teardown
        $toast.remove();
        clearContainerChildren();
    });
    asyncTest('close button duration', 1, function () {
        //Arrange
        toastr.options.closeButton = true;
        toastr.options.closeDuration = 0;
        toastr.options.hideDuration = 2000;
        var $container = toastr.getContainer();
        //Act
        var $toast = toastr.success('');
        $toast.find('button.toast-close-button').click();
        setTimeout(function () {
            //Assert
            ok($container && $container.children().length === 0, 'close button should support own hide animation');
            //Teardown
            toastr.options.hideDuration = 0;
            resetContainer();
            start();
        }, 500);
    });

    module('progressBar', {
        teardown: function () {
            toastr.options.progressBar = false;
        }
    });
    test('progress bar disabled', 1, function () {
        //Arrange
        toastr.options.progressBar = false;
        //Act
        var $toast = toastr.success('');
        //Assert
        equal($toast.find('div.toast-progress').length, 0, 'progress bar should not exist with progressBar=false');
        //Teardown
        $toast.remove();
        clearContainerChildren();
    });
    test('progress bar enabled', 1, function () {
        //Arrange
        toastr.options.progressBar = true;
        //Act
        var $toast = toastr.success('');
        //Assert
        equal($toast.find('div.toast-progress').length, 1, 'progress bar should exist with progressBar=true');
        //Teardown
        $toast.remove();
        clearContainerChildren();
    });

    module('rtl', {
        teardown: function () {
            toastr.options.rtl = false;
        }
    });
    test('toastr is ltr by default', 1, function () {
        //Arrange
        //Act
        //Assert
        toastr.subscribe(function(response) {
            equal(response.options.rtl, false, 'ltr by default (i.e. rtl=false)');
        });
        var $toast = toastr.success('');
        //Teardown
        toastr.subscribe(null);
        $toast.remove();
        clearContainerChildren();
    });
    test('ltr toastr does not have .rtl class', 1, function () {
        //Arrange
        //Act
        var $toast = toastr.success('');
        //Assert
        ok($toast.hasClass('rtl') === false, 'ltr div container does not have .rtl class');
        //Teardown
        $toast.remove();
        clearContainerChildren();
    });
    test('rtl toastr has .rtl class', 1, function () {
        //Arrange
        toastr.options.rtl = true;
        //Act
        var $toast = toastr.success('');
        //Assert
        ok($toast.hasClass('rtl'), 'rtl div container has .rtl class');
        //Teardown
        $toast.remove();
        clearContainerChildren();
    });

    module('accessibility');
    test('toastr success has aria polite',1,function() {
        // Arrange
        var $toast = toastr.success('');

        // Act
        ok($toast.attr('aria-live')==='polite', 'success toast has aria-live of polite');

        // Teardown
        $toast.remove();
        clearContainerChildren();
    });
    test('toastr info has aria polite',1,function() {
        // Arrange
        var $toast = toastr.info('');

        // Act
        ok($toast.attr('aria-live')==='polite', 'info toast has aria-live of polite');

        // Teardown
        $toast.remove();
        clearContainerChildren();
    });
    test('toastr warning has aria assertive',1,function() {
        // Arrange
        var $toast = toastr.warning('');

        // Act
        ok($toast.attr('aria-live')==='assertive', 'warning toast has aria-live of assertive');

        // Teardown
        $toast.remove();
        clearContainerChildren();
    });
    test('toastr error has aria assertive',1,function() {
        // Arrange
        var $toast = toastr.error('');

        // Act
        ok($toast.attr('aria-live')==='assertive', 'error toast has aria-live of assertive');

        // Teardown
        $toast.remove();
        clearContainerChildren();
    });

    module('event', {
        teardown: function () {
            toastr.options.closeButton = false;
            toastr.options.hideDuration = 0;
        }
    });
    asyncTest('event - onShown is executed', 1, function () {
        // Arrange
        var run = false;
        var onShown = function () { run = true; };
        toastr.options.onShown = onShown;
        // Act
        var $toast = toastr.success(sampleMsg, sampleTitle);
        setTimeout(function () {
            // Assert
            ok(run);
            //Teardown
            $toast.remove();
            clearContainerChildren();
            start();
        }, delay);
    });

    asyncTest('event - onHidden is executed', 1, function () {
        //Arrange
        var run = false;
        var onHidden = function () { run = true; };
        toastr.options.onHidden = onHidden;
        toastr.options.timeOut = 1;
        //Act
        var $toast = toastr.success(sampleMsg, sampleTitle);
        setTimeout(function () {
            // Assert
            ok(run); //Teardown
            $toast.remove();
            clearContainerChildren();
            start();
        }, delay);
    });

    asyncTest('event - onShown and onHidden are both executed', 2, function () {
        //Arrange
        var onShowRun = false;
        var onHideRun = false;
        var onShow = function () { onShowRun = true; };
        var onHide = function () { onHideRun = true; };
        toastr.options.onShown = onShow;
        toastr.options.onHidden = onHide;
        toastr.options.timeOut = 1;
        //Act
        var $toast = toastr.success(sampleMsg, sampleTitle);
        setTimeout(function () {
            // Assert
            ok(onShowRun);
            ok(onHideRun);
            //Teardown
            $toast.remove();
            clearContainerChildren();
            start();
        }, delay);
    });

    asyncTest('event - onCloseClick is executed', 1, function () {
        //Arrange
        var run = false;
        toastr.options.closeButton = true;
        toastr.options.closeDuration = 0;
        toastr.options.hideDuration = 2000;
        var onCloseClick = function () { run = true; };
        toastr.options.onCloseClick = onCloseClick;
        toastr.options.timeOut = 1;
        //Act
        var $toast = toastr.success(sampleMsg, sampleTitle);
        $toast.find('button.toast-close-button').click();
        setTimeout(function () {
            // Assert
            ok(run); //Teardown
            $toast.remove();
            clearContainerChildren();
            start();
        }, delay);
    });

    test('event - message appears when no show or hide method functions provided', 1, function () {
        //Arrange
        //Act
        var $toast = toastr.success(sampleMsg, sampleTitle);
        //Assert
        ok($toast.hasClass(iconClasses.success), 'Sets success icon');
        //Teardown
        $toast.remove();
        clearContainerChildren();
    });

    test('event - prevent duplicate sequential toasts.', 1, function(){
        toastr.options.preventDuplicates = true;

        var $toast = [];
        $toast[0] = toastr.info(sampleMsg, sampleTitle);
        $toast[1] = toastr.info(sampleMsg, sampleTitle);
        $toast[2] = toastr.info(sampleMsg + " 1", sampleTitle);
        $toast[3] = toastr.info(sampleMsg, sampleTitle);
        var $container = toastr.getContainer();

        ok($container && $container.children().length === 3);

        clearContainerChildren();
    });

    test('event - prevent duplicate sequential toasts, but allow previous after clear.', 1, function(){
        toastr.options.preventDuplicates = true;

        var $toast = [];
        $toast[0] = toastr.info(sampleMsg, sampleTitle);
        $toast[1] = toastr.info(sampleMsg, sampleTitle);
        clearContainerChildren();
        $toast[3] = toastr.info(sampleMsg, sampleTitle);
        var $container = toastr.getContainer();

        ok($container && $container.children().length === 1);

        clearContainerChildren();
    });

    test('event - allow duplicate sequential toasts.', 1, function(){
        toastr.options.preventDuplicates = false;

        var $toast = [];
        $toast[0] = toastr.info(sampleMsg, sampleTitle);
        $toast[1] = toastr.info(sampleMsg, sampleTitle);
        $toast[1] = toastr.info(sampleMsg, sampleTitle);
        var $container = toastr.getContainer();

        ok($container && $container.children().length === 3);

        clearContainerChildren();
    });

    test('event - allow preventDuplicates option to be overridden.', 1, function() {
        var $toast = [];

        $toast[0] = toastr.info(sampleMsg, sampleTitle, {
            preventDuplicates: true
        });
        $toast[1] = toastr.info(sampleMsg, sampleTitle, {
            preventDuplicates: true
        });
        $toast[2] = toastr.info(sampleMsg, sampleTitle);
        var $container = toastr.getContainer();

        ok($container && $container.children().length === 2);
        clearContainerChildren();
    });

    module('subscription');
    asyncTest('subscribe - triggers 2 visible and 2 hidden response notifications while clicking on a toast', 1, function () {
        //Arrange
        var $toast = [];
        var expectedReponses = [];
        //Act
        toastr.subscribe(function(response) {
          if(response.options.testId) {
            expectedReponses.push(response);
          }
        })

        $toast[0] = toastr.info(sampleMsg, sampleTitle, {testId : 1});
        $toast[1] = toastr.info(sampleMsg, sampleTitle, {testId : 2});

        $toast[1].click()

        setTimeout(function () {
            // Assert
            ok(expectedReponses.length === 4);
            //Teardown
            clearContainerChildren();
            toastr.subscribe(null);
            start();
        }, delay);
    });

    module('order of appearance');
    test('Newest toast on top', 1, function () {
        //Arrange
        resetContainer();
        toastr.options.newestOnTop = true;
        //Act
        var $first = toastr.success("First toast");
        var $second = toastr.success("Second toast");
        //Assert
        var containerHtml = toastr.getContainer().html();
        ok(containerHtml.indexOf("First toast") > containerHtml.indexOf("Second toast"), 'Newest toast is on top');
        //Teardown
        $first.remove();
        $second.remove();
        resetContainer();
    });

    test('Oldest toast on top', 1, function () {
        //Arrange
        resetContainer();
        toastr.options.newestOnTop = false;
        //Act
        var $first = toastr.success("First toast");
        var $second = toastr.success("Second toast");
        //Assert
        var containerHtml = toastr.getContainer().html();
        ok(containerHtml.indexOf("First toast") < containerHtml.indexOf("Second toast"), 'Oldest toast is on top');
        //Teardown
        $first.remove();
        $second.remove();
        resetContainer();
    });

    // These must go last
    module('positioning');
    test('Container - position top-right', 1, function () {
        //Arrange
        resetContainer();
        toastr.options.positionClass = positionClasses.topRight;
        //Act
        var $toast = toastr.success(sampleMsg);
        var $container = toastr.getContainer();
        //Assert
        ok($container.hasClass(positionClasses.topRight), 'Has position top right');
        //Teardown
        $toast.remove();
        resetContainer();
    });
    test('Container - position bottom-right', 1, function () {
        //Arrange
        resetContainer();
        toastr.options.positionClass = positionClasses.bottomRight;
        //Act
        var $toast = toastr.success(sampleMsg);
        var $container = toastr.getContainer();
        //Assert
        ok($container.hasClass(positionClasses.bottomRight), 'Has position bottom right');
        //Teardown
        $toast.remove();
        resetContainer();
    });
    test('Container - position bottom-left', 1, function () {
        //Arrange
        resetContainer();
        //$(selectors.container).remove()
        toastr.options.positionClass = positionClasses.bottomLeft;
        //Act
        var $toast = toastr.success(sampleMsg);
        var $container = toastr.getContainer();
        //Assert
        ok($container.hasClass(positionClasses.bottomLeft), 'Has position bottom left');
        //Teardown
        $toast.remove();
        resetContainer();
    });
    test('Container - position top-left', 1, function () {
        //Arrange
        resetContainer();
        toastr.options.positionClass = positionClasses.topLeft;
        //Act
        var $toast = toastr.success(sampleMsg);
        var $container = toastr.getContainer();
        //Assert
        ok($container.hasClass(positionClasses.topLeft), 'Has position top left');
        //Teardown
        $toast.remove();
        resetContainer();
    });
    test('Container - position top-center', 1, function () {
        //Arrange
        resetContainer();
        toastr.options.positionClass = positionClasses.topCenter;
        //Act
        var $toast = toastr.success(sampleMsg);
        var $container = toastr.getContainer();
        //Assert
        ok($container.hasClass(positionClasses.topCenter), 'Has position top center');
        //Teardown
        $toast.remove();
        resetContainer();
    });
    test('Container - position bottom-center', 1, function () {
        //Arrange
        resetContainer();
        toastr.options.positionClass = positionClasses.bottomCenter;
        //Act
        var $toast = toastr.success(sampleMsg);
        var $container = toastr.getContainer();
        //Assert
        ok($container.hasClass(positionClasses.bottomCenter), 'Has position bottom center');
        //Teardown
        $toast.remove();
        resetContainer();
    });

    function resetContainer() {
        var $container = toastr.getContainer();
        if ($container) {
            $container.remove();
        }
        $(selectors.container).remove();
        clearContainerChildren();
    }

    function clearContainerChildren() {
        toastr.clear();
    }

})();
