"use strict";
//
// Bootstrap Tooltips Demo
//




var KTBootstrapTooltipsDemo = function () {    
    //Private functions

    // Demo 1
    var demo1 = function () {
		$('[data-toggle="tooltip"]').tooltip();
    }

    return {
        // Public functions
        init: function() {
            demo1();
        }
    };
}();

jQuery(document).ready(function() {    
    KTBootstrapTooltipsDemo.init();
});