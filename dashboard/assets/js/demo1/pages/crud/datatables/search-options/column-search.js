"use strict";
var KTDatatablesSearchOptionsColumnSearch = function() {

	$.fn.dataTable.Api.register('column().title()', function() {
		return $(this.header()).text().trim();
	});

	var initTable1 = function() {

		// begin first table
		var table = $('#kt_table_1').DataTable({
			responsive: true,

			// Pagination settings
			dom: `<'row'<'col-sm-12'tr>>
			<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 dataTables_pager'lp>>`,
			// read more: https://datatables.net/examples/basic_init/dom.html

			lengthMenu: [5, 10, 25, 50],

			pageLength: 10,

			language: {
				'lengthMenu': 'Display _MENU_',
			},

			searchDelay: 500,
			processing: true,
			serverSide: true,
			ajax: {
				url: 'https://keenthemes.com/keen/themes/themes/keen/dist/preview/inc/api/datatables/demos/server.php',
				type: 'POST',
				data: {
					// parameters for custom backend script demo
					columnsDef: [
						'RecordID', 'OrderID', 'Country', 'ShipCity', 'CompanyAgent',
						'ShipDate', 'Status', 'Type', 'Actions',],
				},
			},
			columns: [
				{data: 'RecordID'},
				{data: 'OrderID'},
				{data: 'Country'},
				{data: 'ShipCity'},
				{data: 'CompanyAgent'},
				{data: 'ShipDate'},
				{data: 'Status'},
				{data: 'Type'},
				{data: 'Actions', responsivePriority: -1},
			],
			initComplete: function() {
				var thisTable = this;
				var rowFilter = $('<tr class="filter"></tr>').appendTo($(table.table().header()));

				this.api().columns().every(function() {
					var column = this;
					var input;

					switch (column.title()) {
						case 'Record ID':
						case 'Order ID':
						case 'Ship City':
						case 'Company Agent':
							input = $(`<input type="text" class="form-control form-control-sm form-filter kt-input" data-col-index="` + column.index() + `"/>`);
							break;

						case 'Country':
							input = $(`<select class="form-control form-control-sm form-filter kt-input" title="Select" data-col-index="` + column.index() + `">
										<option value="">Select</option></select>`);
							column.data().unique().sort().each(function(d, j) {
								$(input).append('<option value="' + d + '">' + d + '</option>');
							});
							break;

						case 'Status':
							var status = {
								1: {'title': 'Pending', 'class': 'kt-badge--brand'},
								2: {'title': 'Delivered', 'class': ' kt-badge--metal'},
								3: {'title': 'Canceled', 'class': ' kt-badge--primary'},
								4: {'title': 'Success', 'class': ' kt-badge--success'},
								5: {'title': 'Info', 'class': ' kt-badge--info'},
								6: {'title': 'Danger', 'class': ' kt-badge--danger'},
								7: {'title': 'Warning', 'class': ' kt-badge--warning'},
							};
							input = $(`<select class="form-control form-control-sm form-filter kt-input" title="Select" data-col-index="` + column.index() + `">
										<option value="">Select</option></select>`);
							column.data().unique().sort().each(function(d, j) {
								$(input).append('<option value="' + d + '">' + status[d].title + '</option>');
							});
							break;

						case 'Type':
							var status = {
								1: {'title': 'Online', 'state': 'danger'},
								2: {'title': 'Retail', 'state': 'primary'},
								3: {'title': 'Direct', 'state': 'accent'},
							};
							input = $(`<select class="form-control form-control-sm form-filter kt-input" title="Select" data-col-index="` + column.index() + `">
										<option value="">Select</option></select>`);
							column.data().unique().sort().each(function(d, j) {
								$(input).append('<option value="' + d + '">' + status[d].title + '</option>');
							});
							break;

						case 'Ship Date':
							input = $(`
							<div class="input-group date">
								<input type="text" class="form-control form-control-sm kt-input" readonly placeholder="From" id="m_datepicker_1"
								 data-col-index="` + column.index() + `"/>
								<div class="input-group-append">
									<span class="input-group-text"><i class="la la-calendar-o glyphicon-th"></i></span>
								</div>
							</div>
							<div class="input-group date">
								<input type="text" class="form-control form-control-sm kt-input" readonly placeholder="To" id="m_datepicker_2"
								 data-col-index="` + column.index() + `"/>
								<div class="input-group-append">
									<span class="input-group-text"><i class="la la-calendar-o glyphicon-th"></i></span>
								</div>
							</div>`);
							break;

						case 'Actions':
							var search = $(`<button class="btn btn-brand kt-btn btn-sm kt-btn--icon">
							  <span>
							    <i class="la la-search"></i>
							    <span>Search</span>
							  </span>
							</button>`);

							var reset = $(`<button class="btn btn-secondary kt-btn btn-sm kt-btn--icon">
							  <span>
							    <i class="la la-close"></i>
							    <span>Reset</span>
							  </span>
							</button>`);

							$('<th>').append(search).append(reset).appendTo(rowFilter);

							$(search).on('click', function(e) {
								e.preventDefault();
								var params = {};
								$(rowFilter).find('.kt-input').each(function() {
									var i = $(this).data('col-index');
									if (params[i]) {
										params[i] += '|' + $(this).val();
									}
									else {
										params[i] = $(this).val();
									}
								});
								$.each(params, function(i, val) {
									// apply search params to datatable
									table.column(i).search(val ? val : '', false, false);
								});
								table.table().draw();
							});

							$(reset).on('click', function(e) {
								e.preventDefault();
								$(rowFilter).find('.kt-input').each(function(i) {
									$(this).val('');
									table.column($(this).data('col-index')).search('', false, false);
								});
								table.table().draw();
							});
							break;
					}

					if (column.title() !== 'Actions') {
						$(input).appendTo($('<th>').appendTo(rowFilter));
					}
				});

				 // hide search column for responsive table
				 var hideSearchColumnResponsive = function () {
           thisTable.api().columns().every(function () {
	           var column = this
	           if(column.responsiveHidden()) {
		           $(rowFilter).find('th').eq(column.index()).show();
	           } else {
		           $(rowFilter).find('th').eq(column.index()).hide();
	           }
           })
         };

				// init on datatable load
				hideSearchColumnResponsive();
				// recheck on window resize
				window.onresize = hideSearchColumnResponsive;

				$('#kt_datepicker_1,#kt_datepicker_2').datepicker();
			},
			columnDefs: [
				{
					targets: -1,
					title: 'Actions',
					orderable: false,
					render: function(data, type, full, meta) {
						return `
                        <span class="dropdown">
                            <a href="#" class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="true">
                              <i class="la la-ellipsis-h"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="#"><i class="la la-edit"></i> Edit Details</a>
                                <a class="dropdown-item" href="#"><i class="la la-leaf"></i> Update Status</a>
                                <a class="dropdown-item" href="#"><i class="la la-print"></i> Generate Report</a>
                            </div>
                        </span>
                        <a href="#" class="btn btn-sm btn-clean btn-icon btn-icon-md" title="View">
                          <i class="la la-edit"></i>
                        </a>`;
					},
				},
				{
					targets: 5,
					width: '150px',
				},
				{
					targets: 6,
					render: function(data, type, full, meta) {
						var status = {
							1: {'title': 'Pending', 'class': 'kt-badge--brand'},
							2: {'title': 'Delivered', 'class': ' kt-badge--metal'},
							3: {'title': 'Canceled', 'class': ' kt-badge--primary'},
							4: {'title': 'Success', 'class': ' kt-badge--success'},
							5: {'title': 'Info', 'class': ' kt-badge--info'},
							6: {'title': 'Danger', 'class': ' kt-badge--danger'},
							7: {'title': 'Warning', 'class': ' kt-badge--warning'},
						};
						if (typeof status[data] === 'undefined') {
							return data;
						}
						return '<span class="kt-badge ' + status[data].class + ' kt-badge--inline kt-badge--pill">' + status[data].title + '</span>';
					},
				},
				{
					targets: 7,
					render: function(data, type, full, meta) {
						var status = {
							1: {'title': 'Online', 'state': 'danger'},
							2: {'title': 'Retail', 'state': 'primary'},
							3: {'title': 'Direct', 'state': 'accent'},
						};
						if (typeof status[data] === 'undefined') {
							return data;
						}
						return '<span class="kt-badge kt-badge--' + status[data].state + ' kt-badge--dot"></span>&nbsp;' +
							'<span class="kt-font-bold kt-font-' + status[data].state + '">' + status[data].title + '</span>';
					},
				},
			],
		});

	};

	return {

		//main function to initiate the module
		init: function() {
			initTable1();
		}
	};
}();

jQuery(document).ready(function() {
	KTDatatablesSearchOptionsColumnSearch.init();
});