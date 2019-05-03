"use strict";
// Class definition

var KTClipboardDemo = function () {
    
    // Private functions
    var demos = function () {
        // basic example
        new ClipboardJS('[data-clipboard=true]').on('success', function(e) {
            e.clearSelection();
            alert('Copied!');
        });
    }

    return {
        // public functions
        init: function() {
            demos(); 
        }
    };
}();

jQuery(document).ready(function() {    
    KTClipboardDemo.init();
});