
"use strict";
// Class definition

var KTRepeaterDemo = function () {
    
    // Private functions
    var demo = function () {
        $('.kt-repeater').each(function(){
            $(this).repeater({
                show: function () {
                    $(this).slideDown();
                },
                // Enable the option below to have a 2-step remove button
                /*
                hide: function (deleteElement) {
                    if(confirm('Are you sure you want to delete this element?')) {
                        $(this).slideUp(deleteElement);
                    }
                },
                */
                isFirstItemUndeletable: true
            });
        });
    }

    return {
        // public functions
        init: function() {
            demo(); 
        }
    };
}();

jQuery(document).ready(function() {    
    KTRepeaterDemo.init();
});