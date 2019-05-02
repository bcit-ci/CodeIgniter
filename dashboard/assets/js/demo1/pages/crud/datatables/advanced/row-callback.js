"use strict";
var KTDatatablesAdvancedColumnVisibility = function() {

	var initTable1 = function() {
		var table = $('#kt_table_1');

		// begin first table
		table.DataTable({
			responsive: true,
			createdRow: function(row, data, index) {
				var cell = $('td', row).eq(6);
				if (data[6].replace(/[\$,]/g, '') * 1 > 400000 && data[6].replace(/[\$,]/g, '') * 1 < 600000) {
					cell.addClass('highlight').css({'font-weight': 'bold', color: '#716aca'}).attr('title', 'Over $400,000 and below $600,000');
				}
				if (data[6].replace(/[\$,]/g, '') * 1 > 600000) {
					cell.addClass('highlight').css({'font-weight': 'bold', color: '#f4516c'}).attr('title', 'Over $600,000');
				}
				cell.html(KTUtil.numberString(data[6]));
			},
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
	KTDatatablesAdvancedColumnVisibility.init();
});