<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en">

	<head>
		<meta charset="utf-8" />
		<title>Upriselive</title>
		<meta name="description" content="Upriselive">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />

		<script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.16/webfont.js"></script>
		<script>
			WebFont.load({
				google: {
					"families": ["Poppins:300,400,500,600,700"]
				},
				active: function() {
					sessionStorage.fonts = true;
				}
			});
		</script>
		
		<link href="<?php echo base_url() . 'admin/assets/vendors/custom/datatables/datatables.bundle.css' ?>" rel="stylesheet" type="text/css" />
		<link href="<?php echo base_url() . 'admin/assets/vendors/general/perfect-scrollbar/css/perfect-scrollbar.css' ?>" rel="stylesheet" type="text/css" />
		<link href="<?php echo base_url() . 'admin/assets/vendors/general/tether/dist/css/tether.css" rel="stylesheet' ?>" type="text/css" />
		<link href="<?php echo base_url() . 'admin/assets/vendors/general/bootstrap-datepicker/dist/css/bootstrap-datepicker3.css' ?>" rel="stylesheet" type="text/css" />
		<link href="<?php echo base_url() . 'admin/assets/vendors/general/bootstrap-datetime-picker/css/bootstrap-datetimepicker.css' ?>" rel="stylesheet" type="text/css" />
		<link href="<?php echo base_url() . 'admin/assets/vendors/general/bootstrap-timepicker/css/bootstrap-timepicker.css' ?>" rel="stylesheet" type="text/css" />
		<link href="<?php echo base_url() . 'admin/assets/vendors/general/bootstrap-daterangepicker/daterangepicker.css' ?>" rel="stylesheet" type="text/css" />
		<link href="<?php echo base_url() . 'admin/assets/vendors/general/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.css' ?>" rel="stylesheet" type="text/css" />
		<link href="<?php echo base_url() . 'admin/assets/vendors/general/bootstrap-select/dist/css/bootstrap-select.css' ?>" rel="stylesheet" type="text/css" />
		<link href="<?php echo base_url() . 'admin/assets/vendors/general/owl.carousel/dist/assets/owl.carousel.css' ?>" rel="stylesheet" type="text/css" />
		<link href="<?php echo base_url() . 'admin/assets/vendors/general/owl.carousel/dist/assets/owl.theme.default.css' ?>" rel="stylesheet" type="text/css" />
		<link href="<?php echo base_url() . 'admin/assets/vendors/general/dropzone/dist/dropzone.css" rel="stylesheet' ?>" type="text/css" />
		<link href="<?php echo base_url() . 'admin/assets/vendors/general/summernote/dist/summernote.css' ?>" rel="stylesheet" type="text/css" />
		<link href="<?php echo base_url() . 'admin/assets/vendors/general/bootstrap-markdown/css/bootstrap-markdown.min.css' ?>" rel="stylesheet" type="text/css" />
		<link href="<?php echo base_url() . 'admin/assets/vendors/general/animate.css/animate.css' ?>" rel="stylesheet" type="text/css" />
		<link href="<?php echo base_url() . 'admin/assets/vendors/general/toastr/build/toastr.css' ?>" rel="stylesheet" type="text/css" />
		<link href="<?php echo base_url() . 'admin/assets/vendors/general/morris.js/morris.css' ?>" rel="stylesheet" type="text/css" />
		<link href="<?php echo base_url() . 'admin/assets/vendors/general/sweetalert2/dist/sweetalert2.css' ?>" rel="stylesheet" type="text/css" />
		<link href="<?php echo base_url() . 'admin/assets/vendors/general/socicon/css/socicon.css' ?>" rel="stylesheet" type="text/css" />
		<link href="<?php echo base_url() . 'admin/assets/vendors/custom/vendors/line-awesome/css/line-awesome.css' ?>" rel="stylesheet" type="text/css" />
		<link href="<?php echo base_url() . 'admin/assets/vendors/custom/vendors/flaticon/flaticon.css' ?>" rel="stylesheet" type="text/css" />
		<link href="<?php echo base_url() . 'admin/assets/vendors/custom/vendors/flaticon2/flaticon.css' ?>" rel="stylesheet" type="text/css" />
		<link href="<?php echo base_url() . 'admin/assets/vendors/general/@fortawesome/fontawesome-free/css/all.min.css' ?>" rel="stylesheet" type="text/css" />
		<link href="<?php echo base_url() . 'admin/assets/css/demo1/style.bundle.css' ?>" rel="stylesheet" type="text/css" />
		<link href="<?php echo base_url() . 'admin/assets/css/demo1/skins/header/base/light.css' ?>" rel="stylesheet" type="text/css" />
		<link href="<?php echo base_url() . 'admin/assets/css/demo1/skins/header/menu/light.css' ?>" rel="stylesheet" type="text/css" />
		<link href="<?php echo base_url() . 'admin/assets/css/demo1/skins/brand/navy.css' ?>" rel="stylesheet" type="text/css" />
		<link href="<?php echo base_url() . 'admin/assets/css/demo1/skins/aside/navy.css' ?>" rel="stylesheet" type="text/css" />
		<link href="<?php echo base_url() . 'admin/mad.css' ?>" rel="stylesheet" type="text/css" />
		<link rel="shortcut icon" href="<?php echo base_url() . 'admin/images/favicon.png' ?>" />
	</head>
	
	<body class="kt-quick-panel--right kt-demo-panel--right kt-offcanvas-panel--right kt-header--fixed kt-header-mobile--fixed kt-subheader--enabled kt-subheader--transparent kt-aside--enabled kt-aside--fixed kt-page--loading">

		<div id="kt_header_mobile" class="kt-header-mobile  kt-header-mobile--fixed ">
			<div class="kt-header-mobile__logo">
				<a href="#">
					<img alt="Logo" src="<?php echo base_url() . 'admin/images/logo-inverse.png' ?>" />
				</a>
			</div>
			<div class="kt-header-mobile__toolbar">
				<button class="kt-header-mobile__toolbar-toggler kt-header-mobile__toolbar-toggler--left" id="kt_aside_mobile_toggler"><span></span></button>
				<button class="kt-header-mobile__toolbar-toggler" id="kt_header_mobile_toggler"><span></span></button>
				<button class="kt-header-mobile__toolbar-topbar-toggler" id="kt_header_mobile_topbar_toggler"><i class="flaticon-more"></i></button>
			</div>
		</div>
		<div class="kt-grid kt-grid--hor kt-grid--root">
			<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--ver kt-page">
				<button class="kt-aside-close " id="kt_aside_close_btn"><i class="la la-close"></i></button>
				<div class="kt-aside  kt-aside--fixed  kt-grid__item kt-grid kt-grid--desktop kt-grid--hor-desktop" id="kt_aside">
					<div class="kt-aside__brand kt-grid__item " id="kt_aside_brand">
						<div class="kt-aside__brand-logo">
							<a href="#">
								<img alt="Logo" src="<?php echo base_url() . 'admin/images/logo-inverse.png' ?>" />
							</a>
						</div>
						<div class="kt-aside__brand-tools">
							<button class="kt-aside__brand-aside-toggler kt-aside__brand-aside-toggler--left" id="kt_aside_toggler"><span></span></button>
						</div>
					</div>
					<div class="kt-aside-menu-wrapper kt-grid__item kt-grid__item--fluid" id="kt_aside_menu_wrapper">
						<div id="kt_aside_menu" class="kt-aside-menu " data-ktmenu-vertical="1" data-ktmenu-scroll="1" data-ktmenu-dropdown-timeout="500">
							<ul class="kt-menu__nav ">
								<li class="kt-menu__section ">
									<h4 class="kt-menu__section-text">Filter by</h4>
									<i class="kt-menu__section-icon flaticon-more-v2"></i>
								</li>
								<li class="kt-menu__item " aria-haspopup="true"><a href="#" class="kt-menu__link "><i class="kt-menu__link-icon flaticon2-graphic"></i><span class="kt-menu__link-text">All</span></a></li>
								<li class="kt-menu__item " aria-haspopup="true"><a href="#" class="kt-menu__link "><i class="kt-menu__link-icon flaticon2-graph-1"></i><span class="kt-menu__link-text">Pending</span></a></li>
								<li class="kt-menu__item " aria-haspopup="true"><a href="#" class="kt-menu__link "><i class="kt-menu__link-icon flaticon2-rocket-2"></i><span class="kt-menu__link-text">Approved</span></a></li>
								<li class="kt-menu__item " aria-haspopup="true"><a href="#" class="kt-menu__link "><i class="kt-menu__link-icon flaticon2-attention"></i><span class="kt-menu__link-text">Trashed</span></a></li>
								<li class="kt-menu__section ">
									<h4 class="kt-menu__section-text">Video Progress</h4>
									<i class="kt-menu__section-icon flaticon-more-v2"></i>
								</li>
								<li class="kt-menu__item " aria-haspopup="true"><a href="#" class="kt-menu__link "><i class="kt-menu__link-icon flaticon2-graphic"></i><span class="kt-menu__link-text">All</span></a></li>
								<li class="kt-menu__item " aria-haspopup="true"><a href="#" class="kt-menu__link "><i class="kt-menu__link-icon flaticon2-laptop"></i><span class="kt-menu__link-text">Done</span></a></li>
								<li class="kt-menu__item " aria-haspopup="true"><a href="#" class="kt-menu__link "><i class="kt-menu__link-icon flaticon2-browser-2"></i><span class="kt-menu__link-text">In Progress</span></a></li>
								<li class="kt-menu__item " aria-haspopup="true"><a href="#" class="kt-menu__link "><i class="kt-menu__link-icon flaticon2-open-text-book"></i><span class="kt-menu__link-text">Waiting List</span></a></li>
							</ul>
						</div>
					</div>
				</div>
				<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-wrapper" id="kt_wrapper">
					<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor">
						<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
							<div class="kt-portlet kt-portlet--mobile">
								<div class="kt-portlet__head">
									<div class="kt-portlet__head-label">
										<h3 class="kt-portlet__head-title"><span>All</span> Artist List</h3>
									</div>
								</div>
								<div class="kt-portlet__body">
									<table class="table table-striped- table-bordered table-hover table-checkable" id="kt_table_1">
										<thead>
											<tr>
												<th>Record ID</th>
												<th>Song Link</th>
												<th>City/State</th>
												<th>Phone Number</th>
												<th>Status</th>
												<th>Video</th>
												<th>Actions</th>
											</tr>
										</thead>
										<tbody>
										<?php foreach($requests as $request){ ?>
											<tr>
												<td><?php echo $request["id"] ?></td>
												<td><?php echo $request["link"] ?></td>
												<td><?php echo $request["name"] ?></td>
												<td><?php echo $request["email"] ?></td>
												<td><?php echo $request["approval_status"] ?></td>
												<td><?php echo $request["review_status"] ?></td>
												<td nowrap></td>
											</tr>
										<?php } ?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
					<div class="kt-footer kt-grid__item kt-grid kt-grid--desktop kt-grid--ver-desktop">
						<div class="kt-footer__copyright"><a href="#" target="_blank" class="kt-link">© Upriselive 2019. All rights reserved.</a>
						</div>
						<div class="kt-footer__menu">
							<a href="#" target="_blank" class="kt-footer__menu-link kt-link">About</a>
							<a href="#" target="_blank" class="kt-footer__menu-link kt-link">Team</a>
							<a href="#" target="_blank" class="kt-footer__menu-link kt-link">Contact</a>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div id="kt_quick_panel" class="kt-offcanvas-panel">
			<div class="kt-offcanvas-panel__nav">
				<ul class="nav nav-pills" role="tablist">
					<li class="nav-item active">
						<a class="nav-link active" data-toggle="tab" href="#kt_quick_panel_tab_notifications" role="tab">Notifications</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" data-toggle="tab" href="#kt_quick_panel_tab_actions" role="tab">Actions</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" data-toggle="tab" href="#kt_quick_panel_tab_settings" role="tab">Settings</a>
					</li>
				</ul>
				<button class="kt-offcanvas-panel__close" id="kt_quick_panel_close_btn"><i class="flaticon2-delete"></i></button>
			</div>
		</div>
		<div id="kt_scrolltop" class="kt-scrolltop">
			<i class="la la-arrow-up"></i>
		</div>
		<script>
			var KTAppOptions = {
				"colors": {
					"state": {
						"brand": "#5d78ff",
						"metal": "#c4c5d6",
						"light": "#ffffff",
						"accent": "#00c5dc",
						"primary": "#5867dd",
						"success": "#34bfa3",
						"info": "#36a3f7",
						"warning": "#ffb822",
						"danger": "#fd3995",
						"focus": "#9816f4"
					},
					"base": {
						"label": [
							"#c5cbe3",
							"#a1a8c3",
							"#3d4465",
							"#3e4466"
						],
						"shape": [
							"#f0f3ff",
							"#d9dffa",
							"#afb4d4",
							"#646c9a"
						]
					}
				}
			};
		</script>
		<script src="<?php echo base_url() . 'admin/assets/vendors/general/jquery/dist/jquery.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url() . 'admin/assets/vendors/general/popper.js/dist/umd/popper.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url() . 'admin/assets/vendors/general/bootstrap/dist/js/bootstrap.min.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url() . 'admin/assets/vendors/general/js-cookie/src/js.cookie.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url() . 'admin/assets/vendors/general/moment/min/moment.min.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url() . 'admin/assets/vendors/general/tooltip.js/dist/umd/tooltip.min.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url() . 'admin/assets/vendors/general/perfect-scrollbar/dist/perfect-scrollbar.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url() . 'admin/assets/vendors/general/sticky-js/dist/sticky.min.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url() . 'admin/assets/vendors/general/wnumb/wNumb.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url() . 'admin/assets/vendors/general/jquery-form/dist/jquery.form.min.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url() . 'admin/assets/vendors/general/block-ui/jquery.blockUI.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url() . 'admin/assets/vendors/general/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url() . 'admin/assets/vendors/custom/js/vendors/bootstrap-datepicker.init.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url() . 'admin/assets/vendors/general/bootstrap-datetime-picker/js/bootstrap-datetimepicker.min.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url() . 'admin/assets/vendors/general/bootstrap-timepicker/js/bootstrap-timepicker.min.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url() . 'admin/assets/vendors/custom/js/vendors/bootstrap-timepicker.init.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url() . 'admin/assets/vendors/general/bootstrap-daterangepicker/daterangepicker.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url() . 'admin/assets/vendors/general/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url() . 'admin/assets/vendors/general/bootstrap-maxlength/src/bootstrap-maxlength.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url() . 'admin/assets/vendors/custom/vendors/bootstrap-multiselectsplitter/bootstrap-multiselectsplitter.min.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url() . 'admin/assets/vendors/general/bootstrap-select/dist/js/bootstrap-select.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url() . 'admin/assets/vendors/general/typeahead.js/dist/typeahead.bundle.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url() . 'admin/assets/vendors/general/handlebars/dist/handlebars.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url() . 'admin/assets/vendors/general/inputmask/dist/jquery.inputmask.bundle.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url() . 'admin/assets/vendors/general/inputmask/dist/inputmask/inputmask.date.extensions.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url() . 'admin/assets/vendors/general/inputmask/dist/inputmask/inputmask.numeric.extensions.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url() . 'admin/assets/vendors/general/nouislider/distribute/nouislider.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url() . 'admin/assets/vendors/general/owl.carousel/dist/owl.carousel.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url() . 'admin/assets/vendors/general/autosize/dist/autosize.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url() . 'admin/assets/vendors/general/clipboard/dist/clipboard.min.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url() . 'admin/assets/vendors/general/dropzone/dist/dropzone.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url() . 'admin/assets/vendors/general/summernote/dist/summernote.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url() . 'admin/assets/vendors/general/markdown/lib/markdown.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url() . 'admin/assets/vendors/general/bootstrap-markdown/js/bootstrap-markdown.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url() . 'admin/assets/vendors/custom/js/vendors/bootstrap-markdown.init.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url() . 'admin/assets/vendors/general/jquery-validation/dist/jquery.validate.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url() . 'admin/assets/vendors/general/jquery-validation/dist/additional-methods.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url() . 'admin/assets/vendors/custom/js/vendors/jquery-validation.init.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url() . 'admin/assets/vendors/general/toastr/build/toastr.min.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url() . 'admin/assets/vendors/general/raphael/raphael.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url() . 'admin/assets/vendors/general/morris.js/morris.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url() . 'admin/assets/vendors/general/chart.js/dist/Chart.bundle.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url() . 'admin/assets/vendors/custom/vendors/bootstrap-session-timeout/dist/bootstrap-session-timeout.min.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url() . 'admin/assets/vendors/custom/vendors/jquery-idletimer/idle-timer.min.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url() . 'admin/assets/vendors/general/waypoints/lib/jquery.waypoints.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url() . 'admin/assets/vendors/general/counterup/jquery.counterup.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url() . 'admin/assets/vendors/general/es6-promise-polyfill/promise.min.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url() . 'admin/assets/vendors/general/sweetalert2/dist/sweetalert2.min.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url() . 'admin/assets/vendors/custom/js/vendors/sweetalert2.init.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url() . 'admin/assets/vendors/general/jquery.repeater/src/lib.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url() . 'admin/assets/vendors/general/jquery.repeater/src/jquery.input.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url() . 'admin/assets/vendors/general/jquery.repeater/src/repeater.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url() . 'admin/assets/vendors/general/dompurify/dist/purify.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url() . 'admin/assets/js/demo1/scripts.bundle.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url() . 'admin/assets/vendors/custom/datatables/datatables.bundle.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url() . 'admin/assets/js/demo1/pages/crud/datatables/advanced/column-rendering.js' ?>" type="text/javascript"></script>
	</body>
</html>