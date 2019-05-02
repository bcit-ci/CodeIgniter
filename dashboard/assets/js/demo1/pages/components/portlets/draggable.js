"use strict";
var KTPortletDraggable = function () {

    return {
        //main function to initiate the module
        init: function () {
            $("#kt_sortable_portlets").sortable({
                connectWith: ".kt-portlet__head",
                items: ".kt-portlet",
                opacity: 0.8,
                handle : '.kt-portlet__head',
                coneHelperSize: true,
                placeholder: 'kt-portlet--sortable-placeholder',
                forcePlaceholderSize: true,
                tolerance: "pointer",
                helper: "clone",
                cancel: ".kt-portlet--sortable-empty", // cancel dragging if portlet is in fullscreen mode
                revert: 250, // animation in milliseconds
                update: function(b, c) {
                    if (c.item.prev().hasClass("kt-portlet--sortable-empty")) {
                        c.item.prev().before(c.item);
                    }                    
                }
            });
        }
    };
}();

jQuery(document).ready(function() {
    KTPortletDraggable.init();
});