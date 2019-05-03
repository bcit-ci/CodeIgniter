"use strict";
// Class definition

var KTDatatableHtmlTableDemo = function() {
    // Private functions

    // demo initializer
    var demo = function() {

        var datatable = $('.kt-datatable').KTDatatable({
            data: {
                saveState: {
                    cookie: false
                },
            },
            layout: {
                scroll: true,
                height: 550,
                footer: false
            },
            search: {
                input: $('#generalSearch'),
            },
            columns: [
                {
                    field: 'DepositPaid',
                    type: 'number',
                },
                {
                    field: 'OrderDate',
                    type: 'date',
                    format: 'YYYY-MM-DD',
                }, {
                    field: 'Status',
                    title: 'Status',
                    autoHide: false,
                    // callback function support for column rendering
                    template: function(row) {
                        var status = {
                            1: {
                                'title': 'Pending',
                                'class': 'kt-badge--brand'
                            },
                            2: {
                                'title': 'Delivered',
                                'class': ' kt-badge--metal'
                            },
                            3: {
                                'title': 'Canceled',
                                'class': ' kt-badge--primary'
                            },
                            4: {
                                'title': 'Success',
                                'class': ' kt-badge--success'
                            },
                            5: {
                                'title': 'Info',
                                'class': ' kt-badge--info'
                            },
                            6: {
                                'title': 'Danger',
                                'class': ' kt-badge--danger'
                            },
                            7: {
                                'title': 'Warning',
                                'class': ' kt-badge--warning'
                            },
                        };
                        return '<span class="kt-badge ' + status[row.Status].class + ' kt-badge--inline kt-badge--pill">' + status[row.Status].title + '</span>';
                    },
                }, {
                    field: 'Type',
                    title: 'Type',
                    autoHide: false,
                    // callback function support for column rendering
                    template: function(row) {
                        var status = {
                            1: {
                                'title': 'Online',
                                'state': 'danger'
                            },
                            2: {
                                'title': 'Retail',
                                'state': 'primary'
                            },
                            3: {
                                'title': 'Direct',
                                'state': 'accent'
                            },
                        };
                        return '<span class="kt-badge kt-badge--' + status[row.Type].state + ' kt-badge--dot"></span>&nbsp;<span class="kt-font-bold kt-font-' + status[row.Type].state + '">' + status[row.Type].title + '</span>';
                    },
                },
            ],
        });

        $('#kt_form_status').on('change', function() {
            datatable.search($(this).val().toLowerCase(), 'status');
        });

        $('#kt_form_type').on('change', function() {
            datatable.search($(this).val().toLowerCase(), 'type');
        });

        $('#kt_form_status,#kt_form_type').selectpicker();
    };

    return {
        // Public functions
        init: function() {
            // init dmeo
            demo();
        }
    };
}();

jQuery(document).ready(function() {
    KTDatatableHtmlTableDemo.init();
});