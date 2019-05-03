"use strict";
var defaults = {
	"language": {
		"paginate": {
			"first": '<i class="la la-angle-double-left"></i>',
			"last": '<i class="la la-angle-double-right"></i>',
			"next": '<i class="la la-angle-right"></i>',
			"previous": '<i class="la la-angle-left"></i>'
		}
	}
};

if (KTUtil.isRTL()) {
	defaults = {
		"language": {
			"paginate": {
				"first": '<i class="la la-angle-double-right"></i>',
				"last": '<i class="la la-angle-double-left"></i>',
				"next": '<i class="la la-angle-left"></i>',
				"previous": '<i class="la la-angle-right"></i>'
			}
		}
	}
}

$.extend(true, $.fn.dataTable.defaults, defaults);

// fix dropdown overflow inside datatable
KTApp.initAbsoluteDropdown($('body'));