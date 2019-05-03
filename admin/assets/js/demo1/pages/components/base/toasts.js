"use strict";
//
// Bootstrap Toasts Demos
//




var KTBootstrapToastsDemo = function () {    
    //Private functions

    // Demo 1
    var demo1 = function () {
        // Move element and append to body
        $('#kt_toast_1').appendTo($('body'));

        // Init toast
        $('#kt_toast_1').toast({
            delay: 4000
        });

        // Handle toggle
		$('#kt_toast_toggle_1').click(function() {
            $('#kt_toast_1').toast('show');
        });
    }

    // Demo 2
    var demo2 = function () {
		// Move element and append to body
        $('#kt_toast_2').appendTo($('body'));

        // Init toast
        $('#kt_toast_2').toast({
            delay: 4000
        });

        // Handle toggle
		$('#kt_toast_toggle_2').click(function() {
            $('#kt_toast_2').toast('show');
        });
    }

    // Demo 3
    var demo3 = function () {
		// Move element and append to body
        $('#kt_toast_3').appendTo($('body'));

        // Init toast
        $('#kt_toast_3').toast({
            delay: 14000
        });

        // Handle toggle
		$('#kt_toast_toggle_3').click(function() {
            $('#kt_toast_3').toast('show');
        });
    }

    // Demo 4
    var demo4 = function () {
		// Move element and append to body
        $('#kt_toast_4').appendTo($('body'));

        // Init toast
        $('#kt_toast_4').toast({
            delay: 4000
        });

        // Handle toggle
		$('#kt_toast_toggle_4').click(function() {
            $('#kt_toast_4').toast('show');
        });
    }

    // Demo 5
    var demo5 = function () {
		// Move element and append to body
        $('#kt_toast_5').appendTo($('body'));

        // Init toast
        $('#kt_toast_5').toast({
            delay: 4000
        });

        // Handle toggle
		$('#kt_toast_toggle_5').click(function() {
            $('#kt_toast_5').toast('show');
        });
    }

    // Demo 6
    var demo6 = function () {
		// Move element and append to body
        $('#kt_toast_6').appendTo($('body'));

        // Init toast
        $('#kt_toast_6').toast({
            delay: 4000
        });

        // Handle toggle
		$('#kt_toast_toggle_6').click(function() {
            $('#kt_toast_6').toast('show');
        });
    }

    return {
        // Public functions
        init: function() {
            demo1();
            demo2();
            demo3();
            demo4();
            demo5();
            demo6();
        }
    };
}();

jQuery(document).ready(function() {    
    KTBootstrapToastsDemo.init();
});