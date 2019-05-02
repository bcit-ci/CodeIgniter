"use strict";
//
// Bootstrap Popovers Demo
//




var KTBootstrapPopoversDemo = function () {    
    //Private functions

    // Demo 1
    var demo1 = function () {
		$('[data-toggle="popover"]').popover()
    }

    return {
        // Public functions
        init: function() {
            demo1();
        }
    };
}();

jQuery(document).ready(function() {    
    KTBootstrapPopoversDemo.init();
});