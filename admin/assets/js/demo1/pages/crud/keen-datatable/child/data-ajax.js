'use strict';
// Class definition

var KTDatatableChildRemoteDataDemo = function () {
    // Private functions

    // demo initializer
    var demo = function () {

        var datatable = $('.kt_datatable').KTDatatable({
            // datasource definition
            data: {
                type: 'remote',
                source: {
                    read: {
                        url: 'https://keenthemes.com/keen/themes/themes/keen/dist/preview/inc/api/datatables/demos/customers.php',
                    },
                },
                pageSize: 10, // display 20 records per page
                serverPaging: true,
                serverFiltering: false,
                serverSorting: true,
            },

            // layout definition
            layout: {
                theme: 'default',
                scroll: false,
                height: null,
                footer: false,
            },

            // column sorting
            sortable: true,

            pagination: true,

            detail: {
                title: 'Load sub table',
                content: subTableInit,
            },

            search: {
                input: $('#generalSearch'),
            },

            // columns definition
            columns: [
                {
                    field: 'RecordID',
                    title: '',
                    sortable: false,
                    width: 30,
                    textAlign: 'center',
                }, {
                    field: 'checkbox',
                    title: '',
                    template: '{{RecordID}}',
                    sortable: false,
                    width: 20,
                    textAlign: 'center',
                    selector: {class: 'kt-checkbox--solid'},
                }, {
                    field: 'FirstName',
                    title: 'First Name',
                    sortable: 'asc',
                }, {
                    field: 'LastName',
                    title: 'Last Name',
                }, {
                    field: 'Company',
                    title: 'Company',
                }, {
                    field: 'Email',
                    title: 'Email',
                }, {
                    field: 'Phone',
                    title: 'Phone',
                }, {
                    field: 'Status',
                    title: 'Status',
                    // callback function support for column rendering
                    template: function (row) {
                        var status = {
                            1: {'title': 'Pending', 'class': 'kt-badge--brand'},
                            2: {'title': 'Delivered', 'class': ' kt-badge--metal'},
                            3: {'title': 'Canceled', 'class': ' kt-badge--primary'},
                            4: {'title': 'Success', 'class': ' kt-badge--success'},
                            5: {'title': 'Info', 'class': ' kt-badge--info'},
                            6: {'title': 'Danger', 'class': ' kt-badge--danger'},
                            7: {'title': 'Warning', 'class': ' kt-badge--warning'},
                        };
                        return '<span class="kt-badge ' + status[row.Status].class + ' kt-badge--inline kt-badge--pill">' + status[row.Status].title + '</span>';
                    },
                }, {
                    field: 'Type',
                    title: 'Type',
                    autoHide: false,
                    // callback function support for column rendering
                    template: function (row) {
                        var status = {
                            1: {'title': 'Online', 'state': 'danger'},
                            2: {'title': 'Retail', 'state': 'primary'},
                            3: {'title': 'Direct', 'state': 'accent'},
                        };
                        return '<span class="kt-badge kt-badge--' + status[row.Type].state + ' kt-badge--dot"></span>&nbsp;<span class="kt-font-bold kt-font-' + status[row.Type].state +
                            '">' +
                            status[row.Type].title + '</span>';
                    },
                }, {
                    field: 'Actions',
                    width: 110,
                    title: 'Actions',
                    sortable: false,
                    overflow: 'visible',
                    autoHide: false,
                    template: function () {
                        return '\
		                  <div class="dropdown">\
		                      <a href="javascript:;" class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="dropdown">\
		                          <i class="la la-ellipsis-h"></i>\
		                      </a>\
		                      <div class="dropdown-menu dropdown-menu-right">\
		                          <a class="dropdown-item" href="#"><i class="la la-edit"></i> Edit Details</a>\
		                          <a class="dropdown-item" href="#"><i class="la la-leaf"></i> Update Status</a>\
		                          <a class="dropdown-item" href="#"><i class="la la-print"></i> Generate Report</a>\
		                      </div>\
		                  </div>\
		                  <a href="javascript:;" class="btn btn-sm btn-clean btn-icon btn-icon-md" title="Edit details">\
		                      <i class="la la-edit"></i>\
		                  </a>\
		                  <a href="javascript:;" class="btn btn-sm btn-clean btn-icon btn-icon-md" title="Delete">\
		                      <i class="la la-trash"></i>\
		                  </a>\
		              ';
                    },
                }],
        });

        $('#kt_form_status').on('change', function () {
            datatable.search($(this).val().toLowerCase(), 'status');
        });

        $('#kt_form_type').on('change', function () {
            datatable.search($(this).val().toLowerCase(), 'type');
        });

        $('#kt_form_status,#kt_form_type').selectpicker();

        function subTableInit(e) {
            $('<div/>').attr('id', 'child_data_ajax_' + e.data.RecordID).appendTo(e.detailCell).KTDatatable({
                data: {
                    type: 'remote',
                    source: {
                        read: {
                            url: 'https://keenthemes.com/keen/themes/themes/keen/dist/preview/inc/api/datatables/demos/orders.php',
                            headers: {'x-my-custom-header': 'some value', 'x-test-header': 'the value'},
                            params: {
                                // custom query params
                                query: {
                                    generalSearch: '',
                                    CustomerID: e.data.RecordID,
                                },
                            },
                        },
                    },
                    pageSize: 10,
                    serverPaging: true,
                    serverFiltering: false,
                    serverSorting: true,
                },

                // layout definition
                layout: {
                    theme: 'default',
                    scroll: true,
                    height: 300,
                    footer: false,

                    // enable/disable datatable spinner.
                    spinner: {
                        type: 1,
                        theme: 'default',
                    },
                },

                sortable: true,

                // columns definition
                columns: [
                    {
                        field: 'RecordID',
                        title: '#',
                        sortable: false,
                        width: 30,
                    }, {
                        field: 'OrderID',
                        title: 'Order ID',
                        template: function (row) {
                            return '<span>' + row.OrderID + ' - ' + row.ShipCountry + '</span>';
                        },
                    }, {
                        field: 'ShipCountry',
                        title: 'Country',
                        width: 100,
                    }, {
                        field: 'ShipAddress',
                        title: 'Ship Address',
                    }, {
                        field: 'ShipName',
                        title: 'Ship Name',
                    }, {
                        field: 'TotalPayment',
                        title: 'Payment',
                        type: 'number',
                    }, {
                        field: 'Status',
                        title: 'Status',
                        // callback function support for column rendering
                        template: function (row) {
                            var status = {
                                1: {'title': 'Pending', 'class': 'kt-badge--brand'},
                                2: {'title': 'Delivered', 'class': ' kt-badge--metal'},
                                3: {'title': 'Canceled', 'class': ' kt-badge--primary'},
                                4: {'title': 'Success', 'class': ' kt-badge--success'},
                                5: {'title': 'Info', 'class': ' kt-badge--info'},
                                6: {'title': 'Danger', 'class': ' kt-badge--danger'},
                                7: {'title': 'Warning', 'class': ' kt-badge--warning'},
                            };
                            return '<span class="kt-badge ' + status[row.Status].class + ' kt-badge--inline kt-badge--pill">' + status[row.Status].title + '</span>';
                        },
                    }, {
                        field: 'Type',
                        title: 'Type',
                        autoHide: false,
                        // callback function support for column rendering
                        template: function (row) {
                            var status = {
                                1: {'title': 'Online', 'state': 'danger'},
                                2: {'title': 'Retail', 'state': 'primary'},
                                3: {'title': 'Direct', 'state': 'accent'},
                            };
                            return '<span class="kt-badge kt-badge--' + status[row.Type].state + ' kt-badge--dot"></span>&nbsp;<span class="kt-font-bold kt-font-' +
                                status[row.Type].state + '">' +
                                status[row.Type].title + '</span>';
                        },
                    }
                ],
            });
        }
    };

    return {
        // Public functions
        init: function () {
            // init dmeo
            demo();
        },
    };
}();

jQuery(document).ready(function () {
    KTDatatableChildRemoteDataDemo.init();
});