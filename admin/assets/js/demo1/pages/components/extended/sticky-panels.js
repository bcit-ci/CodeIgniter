"use strict";
// Class definition
// Based on:  https://github.com/rgalus/sticky-js

var KTStickyPanelsDemo = function () {
    
    // Private functions

    // Basic demo
    var demo1 = function () {        
        if (KTLayout.onAsideToggle) {
            var sticky = new Sticky('.sticky');

            KTLayout.onAsideToggle(function() {
                setTimeout(function() {
                    sticky.update(); // update sticky positions on aside toggle                    
                }, 500);                
            });   
        }             
    }

    return {
        // public functions
        init: function() {
            demo1(); 
        }
    };
}();

jQuery(document).ready(function() {    
    KTStickyPanelsDemo.init();
});