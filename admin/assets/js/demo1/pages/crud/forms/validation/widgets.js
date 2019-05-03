"use strict";
// Class definition

var KTFormWidgets = function () {
    // Private functions
    var validator;

    var initWidgets = function() {
        // datepicker
        $('#kt_datepicker').datepicker({
            todayHighlight: true,
            templates: {
                leftArrow: '<i class="la la-angle-left"></i>',
                rightArrow: '<i class="la la-angle-right"></i>'
            }
        });

        // datetimepicker
        $('#kt_datetimepicker').datetimepicker({
            pickerPosition: 'bottom-left',
            todayHighlight: true,
            autoclose: true,
            format: 'yyyy.mm.dd hh:ii'
        });

        $('#kt_datetimepicker').change(function() {
            validator.element($(this));
        });

        // timepicker
        $('#kt_timepicker').timepicker({
            minuteStep: 1,
            showSeconds: true,
            showMeridian: true
        });

        // daterangepicker
        $('#kt_daterangepicker').daterangepicker({
            buttonClasses: 'kt-btn btn',
            applyClass: 'btn-primary',
            cancelClass: 'btn-secondary'
        }, function(start, end, label) {
            var input = $('#kt_daterangepicker').find('.form-control');
            
            input.val( start.format('YYYY/MM/DD') + ' / ' + end.format('YYYY/MM/DD'));
            validator.element(input); // validate element
        });

        // bootstrap select
        $('#kt_bootstrap_select').selectpicker();
        $('#kt_bootstrap_select').on('changed.bs.select', function() {
            validator.element($(this)); // validate element
        });

        // typeahead
        var countries = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.whitespace,
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            prefetch: 'https://keenthemes.com/keen/themes/themes/keen/dist/preview/inc/api/typeahead/countries.json'
        });

        $('#kt_typeahead').typeahead(null, {
            name: 'countries',
            source: countries
        });
        $('#kt_typeahead').bind('typeahead:select', function(ev, suggestion) {
            validator.element($('#kt_typeahead')); // validate element
        });
    }
    
    var initValidation = function () {
        validator = $( "#kt_form_1" ).validate({
            // define validation rules
            rules: {
                date: {
                    required: true,
                    date: true 
                },
                daterange: {
                    required: true
                },
                datetime: {
                    required: true
                },
                time: {
                    required: true
                },
                select: {
                    required: true,
                    minlength: 2,
                    maxlength: 4
                },
                typeahead: {
                    required: true
                },
                markdown: {
                    required: true
                }
            },
            
            //display error alert on form submit  
            invalidHandler: function(event, validator) {             
                var alert = $('#kt_form_1_msg');
                alert.parent().removeClass('kt-hide');
                KTUtil.scrollTo("kt_form_2", -200);
            },

            submitHandler: function (form) {
                //form[0].submit(); // submit the form
            }
        });       
    }

    return {
        // public functions
        init: function() {
            initWidgets(); 
            initValidation();
        }
    };
}();

jQuery(document).ready(function() {    
    KTFormWidgets.init();
});