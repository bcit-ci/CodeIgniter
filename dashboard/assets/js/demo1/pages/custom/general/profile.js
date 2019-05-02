"use strict";
// Class definition
var KTProfile = function() {

    var initProfileAvatar = function() {
        var avatar = new KTAvatar('kt_profile_avatar');
    }

    var initStatisticCharts = function() {
        // Mini charts
        KTLib.initMiniChart($('#kt_profile_mini_chart_1'), [6, 12, 9,  18, 15, 9, 11, 8], KTApp.getStateColor('danger'), 2);
        KTLib.initMiniChart($('#kt_profile_mini_chart_2'), [8, 13,  10, 14, 12,  10, 11, 14], KTApp.getStateColor('brand'), 2);
        // html markup for the mini charts:  <canvas id="kt_profile_mini_chart_1" width="90" height="50"></canvas>
    }

    return {
        // Init demos
        init: function() {
           initStatisticCharts(); 
           initProfileAvatar();
        }
    };
}();

// Class initialization on page load
jQuery(document).ready(function() {
    KTProfile.init();
});