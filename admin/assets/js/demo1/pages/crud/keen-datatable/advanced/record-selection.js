"use strict";
// Class definition

var KTDatatableRecordSelectionDemo = function() {
    // Private functions

    var options = {
        // datasource definition
        data: {
            type: 'remote',
            source: {
                read: {
                    url: 'https://keenthemes.com/keen/themes/themes/keen/dist/preview/inc/api/datatables/demos/default.php',
                },
            },
            pageSize: 10,
            serverPaging: true,
            serverFiltering: true,
            serverSorting: true,
        },

        // layout definition
        layout: {
            theme: 'default', // datatable theme
            class: '', // custom wrapper class
            scroll: true, // enable/disable datatable scroll both horizontal and
            // vertical when needed.
            height: 350, // datatable's body's fixed height
            footer: false // display/hide footer
        },

        // column sorting
        sortable: true,

        pagination: true,

        // columns definition

        columns: [{
            field: 'employee_id',
            title: '#',
            sortable: false,
            width: 20,
            selector: {
                class: 'kt-checkbox--solid'
            },
            textAlign: 'center',
        }, {
            field: 'id',
            title: 'Employee ID',
            template: '{{employee_id}}',
        }, {
            field: 'name',
            title: 'Name',
            template: function(row) {
                return row.first_name + ' ' + row.last_name;
            },
        }, {
            field: 'phone',
            title: 'Phone',
        }, {
            field: 'hire_date',
            title: 'Hire Date',
            type: 'date',
            format: 'MM/DD/YYYY',
        }, {
            field: 'status',
            title: 'Status',
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
                return '<span class="kt-badge ' + status[row.status].class +
                    ' kt-badge--inline kt-badge--pill">' + status[row.status].title +
                    '</span>';
            },
        }, {
            field: 'type',
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
                return '<span class="kt-badge kt-badge--' + status[row.type].state +
                    ' kt-badge--dot"></span>&nbsp;<span class="kt-font-bold kt-font-' +
                    status[row.type].state + '">' + status[row.type].title + '</span>';
            },
        }, {
            field: 'Actions',
            title: 'Actions',
            sortable: false,
            width: 110,
            overflow: 'visible',
	        autoHide: false,
            template: function() {
	            return '\
                    <div class="dropdown">\
                        <a href="javascript:;" class="btn btn-sm btn-clean btn-icon btn-icon-sm" data-toggle="dropdown">\
                            <i class="flaticon2-settings"></i>\
                        </a>\
                        <div class="dropdown-menu dropdown-menu-right">\
                            <a class="dropdown-item" href="#"><i class="la la-edit"></i> Edit Details</a>\
                            <a class="dropdown-item" href="#"><i class="la la-leaf"></i> Update Status</a>\
                            <a class="dropdown-item" href="#"><i class="la la-print"></i> Generate Report</a>\
                        </div>\
                    </div>\
                    <a href="javascript:;" class="btn btn-sm btn-clean btn-icon btn-icon-sm" title="Edit details">\
                        <i class="flaticon2-file"></i>\
                    </a>\
                    <a href="javascript:;" class="btn btn-sm btn-clean btn-icon btn-icon-sm" title="Delete">\
                        <i class="flaticon2-delete"></i>\
                    </a>\
                ';
            },
        }],
    };

    // basic demo
    var localSelectorDemo = function() {

        options.search = {
            input: $('#generalSearch'),
        };

        var datatable = $('#local_record_selection').KTDatatable(options);

        $('#kt_form_status').on('change', function() {
            datatable.search($(this).val().toLowerCase(), 'status');
        });

        $('#kt_form_type').on('change', function() {
            datatable.search($(this).val().toLowerCase(), 'type');
        });

        $('#kt_form_status,#kt_form_type').selectpicker();

        datatable.on(
            'kt-datatable--on-check kt-datatable--on-uncheck kt-datatable--on-layout-updated',
            function(e) {
                var checkedNodes = datatable.rows('.kt-datatable__row--active').nodes();
                var count = checkedNodes.length;
                $('#kt_datatable_selected_number').html(count);
                if (count > 0) {
                    $('#kt_datatable_group_action_form').collapse('show');
                } else {
                    $('#kt_datatable_group_action_form').collapse('hide');
                }
            });

        $('#kt_modal_fetch_id').on('show.bs.modal', function(e) {
            var ids = datatable.rows('.kt-datatable__row--active').
            nodes().
            find('.kt-checkbox--single > [type="checkbox"]').
            map(function(i, chk) {
                return $(chk).val();
            });
            var c = document.createDocumentFragment();
            for (var i = 0; i < ids.length; i++) {
                var li = document.createElement('li');
                li.setAttribute('data-id', ids[i]);
                li.innerHTML = 'Selected record ID: ' + ids[i];
                c.appendChild(li);
            }
            $(e.target).find('.kt_datatable_selected_ids').append(c);
        }).on('hide.bs.modal', function(e) {
            $(e.target).find('.kt_datatable_selected_ids').empty();
        });

    };

    var serverSelectorDemo = function() {

        // enable extension
        options.extensions = {
            checkbox: {},
        };
        options.search = {
            input: $('#generalSearch1'),
        };

        var datatable = $('#server_record_selection').KTDatatable(options);

        $('#kt_form_status1').on('change', function() {
            datatable.search($(this).val().toLowerCase(), 'status');
        });

        $('#kt_form_type1').on('change', function() {
            datatable.search($(this).val().toLowerCase(), 'type');
        });

        $('#kt_form_status1,#kt_form_type1').selectpicker();

        datatable.on(
            'kt-datatable--on-click-checkbox kt-datatable--on-layout-updated',
            function(e) {
                // datatable.checkbox() access to extension methods
                var ids = datatable.checkbox().getSelectedId();
                var count = ids.length;
                $('#kt_datatable_selected_number1').html(count);
                if (count > 0) {
                    $('#kt_datatable_group_action_form1').collapse('show');
                } else {
                    $('#kt_datatable_group_action_form1').collapse('hide');
                }
            });

        $('#kt_modal_fetch_id_server').on('show.bs.modal', function(e) {
            var ids = datatable.checkbox().getSelectedId();
            var c = document.createDocumentFragment();
            for (var i = 0; i < ids.length; i++) {
                var li = document.createElement('li');
                li.setAttribute('data-id', ids[i]);
                li.innerHTML = 'Selected record ID: ' + ids[i];
                c.appendChild(li);
            }
            $(e.target).find('.kt_datatable_selected_ids').append(c);
        }).on('hide.bs.modal', function(e) {
            $(e.target).find('.kt_datatable_selected_ids').empty();
        });

    };

    return {
        // public functions
        init: function() {
            localSelectorDemo();
            serverSelectorDemo();
        }
    };
}();

jQuery(document).ready(function() {
    KTDatatableRecordSelectionDemo.init();
});