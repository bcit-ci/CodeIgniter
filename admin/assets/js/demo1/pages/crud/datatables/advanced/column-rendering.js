"use strict";
var KTDatatablesAdvancedColumnRendering = function() {

	var initTable1 = function() {
		var table = $('#kt_table_1');

		// begin first table
		table.DataTable({
			responsive: true,
			paging: true,
			columnDefs: [
				{
					targets: 0,
					title: 'Artist',
					render: function(data, type, full, meta) {
						var number = KTUtil.getRandomInt(1, 14);
						var user_img = '100_' + number + '.jpg';

						var output;
						if (number > 8) {
							output = `
                                <div class="kt-user-card-v2">
                                    <div class="kt-user-card-v2__pic">
                                        <img src="https://keenthemes.com/keen/preview/assets/media/users/` + user_img + `" class="kt-img-rounded kt-marginless" alt="photo">
                                    </div>
                                    <div class="kt-user-card-v2__details">
                                        <span class="kt-user-card-v2__name">` + full[2] + `</span>
                                        <a href="#" class="kt-user-card-v2__email kt-link">` + full[3] + `</a>
                                    </div>
                                </div>`;
						}
						else {
							var stateNo = KTUtil.getRandomInt(0, 7);
							var states = [
								'success',
								'brand',
								'danger',
								'accent',
								'warning',
								'metal',
								'primary',
								'info'];
							var state = states[stateNo];

							output = `
                                <div class="kt-user-card-v2">
                                    <div class="kt-user-card-v2__pic">
                                        <div class="kt-badge kt-badge--xl kt-badge--` + state + `"><span>` + full[2].substring(0, 1) + `</div>
                                    </div>
                                    <div class="kt-user-card-v2__details">
                                        <span class="kt-user-card-v2__name">` + full[2] + `</span>
                                        <a href="#" class="kt-user-card-v2__email kt-link">` + full[3] + `</a>
                                    </div>
                                </div>`;
						}

						return output;
					},
				},
				{
					targets: 1,
					render: function(data, type, full, meta) {
						return '<a class="kt-link" target="_blank" href="' + data + '">' + data + '</a>';
					},
				},
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
                                <a class="dropdown-item" href="#"><i class="la la-edit"></i> Approve</a>
                                <a class="dropdown-item" href="#"><i class="la la-leaf"></i> Review</a>
                                <a class="dropdown-item" href="#"><i class="la la-print"></i> Trash</a>
                            </div>
                        </span>
                        <a href="#" class="btn btn-sm btn-clean btn-icon btn-icon-md" title="View">
                          <i class="la la-edit"></i>
                        </a>`;
					},
				},
				{
					targets: 4,
					render: function(data, type, full, meta) {
						var status = {
							1: {'title': 'Approved', 'class': ' kt-badge--success'},
							2: {'title': 'Trashed', 'class': ' kt-badge--danger'},
							3: {'title': 'New', 'class': 'kt-badge--metal'},
							4: {'title': 'Review', 'class': 'kt-badge--brand'},
						};
						if (typeof status[data] === 'undefined') {
							return data;
						}
						return '<span class="kt-badge ' + status[data].class + ' kt-badge--inline kt-badge--pill">' + status[data].title + '</span>';
					},
				},
				{
					targets: 5,
					render: function(data, type, full, meta) {
						var status = {
							1: {'title': 'Progs', 'state': 'danger'},
							2: {'title': 'Waitn', 'state': 'primary'},
							3: {'title': 'Done', 'state': 'accent'},
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
	KTDatatablesAdvancedColumnRendering.init();
});