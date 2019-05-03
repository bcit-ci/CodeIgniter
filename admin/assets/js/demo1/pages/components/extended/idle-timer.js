"use strict";
var KTIdleTimerDemo = function() {

    var demo1 = function() {
        //Define default
        var
            docTimeout = 5000;

        /*
        Handle raised idle/active events
        */
        $(document).on("idle.idleTimer", function(event, elem, obj) {
            $("#docStatus")
                .val(function(i, v) {
                    return v + "Idle @ " + moment().format() + " \n";
                })
                .removeClass("alert-success")
                .addClass("alert-warning")
                .scrollTop($('#docStatus')[0].scrollHeight);
        });
        $(document).on("active.idleTimer", function(event, elem, obj, e) {
            $('#docStatus')
                .val(function(i, v) {
                    return v + "Active [" + e.type + "] [" + e.target.nodeName + "] @ " + moment().format() + " \n";
                })
                .addClass("alert-success")
                .removeClass("alert-warning")
                .scrollTop($('#docStatus')[0].scrollHeight);
        });

        /*
        Handle button events
        */
        $("#btPause").click(function() {
            $(document).idleTimer("pause");
            $('#docStatus')
                .val(function(i, v) {
                    return v + "Paused @ " + moment().format() + " \n";
                })
                .scrollTop($('#docStatus')[0].scrollHeight);
            $(this).blur();
            return false;
        });
        $("#btResume").click(function() {
            $(document).idleTimer("resume");
            $('#docStatus')
                .val(function(i, v) {
                    return v + "Resumed @ " + moment().format() + " \n";
                })
                .scrollTop($('#docStatus')[0].scrollHeight);
            $(this).blur();
            return false;
        });
        $("#btElapsed").click(function() {
            $('#docStatus')
                .val(function(i, v) {
                    return v + "Elapsed (since becoming active): " + $(document).idleTimer("getElapsedTime") + " \n";
                })
                .scrollTop($('#docStatus')[0].scrollHeight);
            $(this).blur();
            return false;
        });
        $("#btDestroy").click(function() {
            $(document).idleTimer("destroy");
            $('#docStatus')
                .val(function(i, v) {
                    return v + "Destroyed: @ " + moment().format() + " \n";
                })
                .removeClass("alert-success")
                .removeClass("alert-warning")
                .scrollTop($('#docStatus')[0].scrollHeight);
            $(this).blur();
            return false;
        });
        $("#btInit").click(function() {
            // for demo purposes show init with just object
            $(document).idleTimer({
                timeout: docTimeout
            });
            $('#docStatus')
                .val(function(i, v) {
                    return v + "Init: @ " + moment().format() + " \n";
                })
                .scrollTop($('#docStatus')[0].scrollHeight);

            //Apply classes for default state
            if ($(document).idleTimer("isIdle")) {
                $('#docStatus')
                    .removeClass("alert-success")
                    .addClass("alert-warning");
            } else {
                $('#docStatus')
                    .addClass("alert-success")
                    .removeClass("alert-warning");
            }
            $(this).blur();
            return false;
        });

        //Clear old statuses
        $('#docStatus').val('');

        //Start timeout, passing no options
        //Same as $.idleTimer(docTimeout, docOptions);
        $(document).idleTimer(docTimeout);

        //For demo purposes, style based on initial state
        if ($(document).idleTimer("isIdle")) {
            $("#docStatus")
                .val(function(i, v) {
                    return v + "Initial Idle State @ " + moment().format() + " \n";
                })
                .removeClass("alert-success")
                .addClass("alert-warning")
                .scrollTop($('#docStatus')[0].scrollHeight);
        } else {
            $('#docStatus')
                .val(function(i, v) {
                    return v + "Initial Active State @ " + moment().format() + " \n";
                })
                .addClass("alert-success")
                .removeClass("alert-warning")
                .scrollTop($('#docStatus')[0].scrollHeight);
        }


        //For demo purposes, display the actual timeout on the page
        $('#docTimeout').text(docTimeout / 1000);

    }

    var demo2 = function() {
        //Define textarea settings
        var
            taTimeout = 3000;

        /*
        Handle raised idle/active events
        */
        $('#elStatus').on("idle.idleTimer", function(event, elem, obj) {
            //If you dont stop propagation it will bubble up to document event handler
            event.stopPropagation();

            $('#elStatus')
                .val(function(i, v) {
                    return v + "Idle @ " + moment().format() + " \n";
                })
                .removeClass("alert-success")
                .addClass("alert-warning")
                .scrollTop($('#elStatus')[0].scrollHeight);

        });
        $('#elStatus').on("active.idleTimer", function(event) {
            //If you dont stop propagation it will bubble up to document event handler
            event.stopPropagation();

            $('#elStatus')
                .val(function(i, v) {
                    return v + "Active @ " + moment().format() + " \n";
                })
                .addClass("alert-success")
                .removeClass("alert-warning")
                .scrollTop($('#elStatus')[0].scrollHeight);
        });

        /*
        Handle button events
        */
        $("#btReset").click(function() {
            $('#elStatus')
                .idleTimer("reset")
                .val(function(i, v) {
                    return v + "Reset @ " + moment().format() + " \n";
                })
                .scrollTop($('#elStatus')[0].scrollHeight);

            //Apply classes for default state
            if ($("#elStatus").idleTimer("isIdle")) {
                $('#elStatus')
                    .removeClass("alert-success")
                    .addClass("alert-warning");
            } else {
                $('#elStatus')
                    .addClass("alert-success")
                    .removeClass("alert-warning");
            }
            $(this).blur();
            return false;
        });
        $("#btRemaining").click(function() {
            $('#elStatus')
                .val(function(i, v) {
                    return v + "Remaining: " + $("#elStatus").idleTimer("getRemainingTime") + " \n";
                })
                .scrollTop($('#elStatus')[0].scrollHeight);
            $(this).blur();
            return false;
        });
        $("#btLastActive").click(function() {
            $('#elStatus')
                .val(function(i, v) {
                    return v + "LastActive: " + $("#elStatus").idleTimer("getLastActiveTime") + " \n";
                })
                .scrollTop($('#elStatus')[0].scrollHeight);
            $(this).blur();
            return false;
        });
        $("#btState").click(function() {
            $('#elStatus')
                .val(function(i, v) {
                    return v + "State: " + ($("#elStatus").idleTimer("isIdle") ? "idle" : "active") + " \n";
                })
                .scrollTop($('#elStatus')[0].scrollHeight);
            $(this).blur();
            return false;
        });

        //Clear value if there was one cached & start time
        $('#elStatus').val('').idleTimer(taTimeout);

        //For demo purposes, show initial state
        if ($("#elStatus").idleTimer("isIdle")) {
            $("#elStatus")
                .val(function(i, v) {
                    return v + "Initial Idle @ " + moment().format() + " \n";
                })
                .removeClass("alert-success")
                .addClass("alert-warning")
                .scrollTop($('#elStatus')[0].scrollHeight);
        } else {
            $('#elStatus')
                .val(function(i, v) {
                    return v + "Initial Active @ " + moment().format() + " \n";
                })
                .addClass("alert-success")
                .removeClass("alert-warning")
                .scrollTop($('#elStatus')[0].scrollHeight);
        }

        // Display the actual timeout on the page
        $('#elTimeout').text(taTimeout / 1000);

    }

    return {
        //main function to initiate the module
        init: function() {
            demo1();
            demo2();
        }
    };

}();

jQuery(document).ready(function() {
    KTIdleTimerDemo.init();
});