<?php
defined('BASEPATH') or exit('No direct script access allowed');
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

		<link href="<?php echo base_url(). 'admin/assets/css/demo1/pages/custom/general/user/login-v2.css' ?>" rel="stylesheet" type="text/css" />
		<link href="<?php echo base_url(). 'admin/assets/vendors/general/perfect-scrollbar/css/perfect-scrollbar.css' ?>" rel="stylesheet" type="text/css" />
		<link href="<?php echo base_url(). 'admin/assets/vendors/general/tether/dist/css/tether.css' ?>" rel="stylesheet" type="text/css" />
		<link href="<?php echo base_url(). 'admin/assets/vendors/general/bootstrap-datepicker/dist/css/bootstrap-datepicker3.css' ?>" rel="stylesheet" type="text/css" />
		<link href="<?php echo base_url(). 'admin/assets/vendors/general/bootstrap-datetime-picker/css/bootstrap-datetimepicker.css' ?>" rel="stylesheet" type="text/css" />
		<link href="<?php echo base_url(). 'admin/assets/vendors/general/bootstrap-timepicker/css/bootstrap-timepicker.css' ?>" rel="stylesheet" type="text/css" />
		<link href="<?php echo base_url(). 'admin/assets/vendors/general/bootstrap-daterangepicker/daterangepicker.css' ?>" rel="stylesheet" type="text/css" />
		<link href="<?php echo base_url(). 'admin/assets/vendors/general/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.css' ?>" rel="stylesheet" type="text/css" />
		<link href="<?php echo base_url(). 'admin/assets/vendors/general/bootstrap-select/dist/css/bootstrap-select.css' ?>" rel="stylesheet" type="text/css" />
		<link href="<?php echo base_url(). 'admin/assets/vendors/general/nouislider/distribute/nouislider.css' ?>" rel="stylesheet" type="text/css" />
		<link href="<?php echo base_url(). 'admin/assets/vendors/general/owl.carousel/dist/assets/owl.carousel.css' ?>" rel="stylesheet" type="text/css" />
		<link href="<?php echo base_url(). 'admin/assets/vendors/general/owl.carousel/dist/assets/owl.theme.default.css' ?>' ?>" rel="stylesheet" type="text/css" />
		<link href="<?php echo base_url(). 'admin/assets/vendors/general/dropzone/dist/dropzone.css' ?>" rel="stylesheet" type="text/css" />
		<link href="<?php echo base_url(). 'admin/assets/vendors/general/summernote/dist/summernote.css' ?>" rel="stylesheet" type="text/css" />
		<link href="<?php echo base_url(). 'admin/assets/vendors/general/bootstrap-markdown/css/bootstrap-markdown.min.css' ?>" rel="stylesheet" type="text/css" />
		<link href="<?php echo base_url(). 'admin/assets/vendors/general/animate.css/animate.css' ?>" rel="stylesheet" type="text/css" />
		<link href="<?php echo base_url(). 'admin/assets/vendors/general/toastr/build/toastr.css' ?>" rel="stylesheet" type="text/css" />
		<link href="<?php echo base_url(). 'admin/assets/vendors/general/morris.js/morris.css' ?>" rel="stylesheet" type="text/css" />
		<link href="<?php echo base_url(). 'admin/assets/vendors/general/sweetalert2/dist/sweetalert2.css' ?>" rel="stylesheet" type="text/css" />
		<link href="<?php echo base_url(). 'admin/assets/vendors/general/socicon/css/socicon.css' ?>" rel="stylesheet" type="text/css" />
		<link href="<?php echo base_url(). 'admin/assets/vendors/custom/vendors/line-awesome/css/line-awesome.css' ?>" rel="stylesheet" type="text/css" />
		<link href="<?php echo base_url(). 'admin/assets/vendors/custom/vendors/flaticon/flaticon.css' ?>" rel="stylesheet" type="text/css" />
		<link href="<?php echo base_url(). 'admin/assets/vendors/custom/vendors/flaticon2/flaticon.css' ?>" rel="stylesheet" type="text/css" />
		<link href="<?php echo base_url(). 'admin/assets/vendors/general/@fortawesome/fontawesome-free/css/all.min.css' ?>" rel="stylesheet" type="text/css" />
		<link href="<?php echo base_url(). 'admin/assets/css/demo1/style.bundle.css' ?>" rel="stylesheet" type="text/css" />
		<link href="<?php echo base_url(). 'admin/assets/css/demo1/skins/header/base/light.css' ?>" rel="stylesheet" type="text/css" />
		<link href="<?php echo base_url(). 'admin/assets/css/demo1/skins/header/menu/light.css' ?>" rel="stylesheet" type="text/css" />
		<link href="<?php echo base_url(). 'admin/assets/css/demo1/skins/brand/navy.css' ?>" rel="stylesheet" type="text/css" />
		<link href="<?php echo base_url(). 'admin/assets/css/demo1/skins/aside/navy.css' ?>" rel="stylesheet" type="text/css" />
		<link href="mad.css" rel="stylesheet" type="text/css" />
		<link rel="shortcut icon" href="<?php echo base_url(). 'admin/images/favicon.png' ?>" />
	</head>

	<body class="kt-login-v2--enabled kt-quick-panel--right kt-demo-panel--right kt-offcanvas-panel--right kt-header--fixed kt-header-mobile--fixed kt-subheader--enabled kt-subheader--transparent kt-aside--enabled kt-aside--fixed kt-page--loading">

		<div class="kt-grid kt-grid--ver kt-grid--root">
			<div class="kt-grid__item   kt-grid__item--fluid kt-grid  kt-grid kt-grid--hor kt-login-v2" id="kt_login_v2">
				<div class="kt-grid__item  kt-grid--hor">
					<div class="kt-login-v2__head">
						<div class="kt-login-v2__logo">
							<a href="#">
								<img src="<?php echo base_url(). 'admin/images/logo.png'?>" alt="" />
							</a>
						</div>
						<div class="kt-login-v2__signup">
							<span>Don't have an account?</span>
							<a href="#" class="kt-link kt-font-brand">Sign Up</a>
						</div>
					</div>
				</div>
				<div class="kt-grid__item  kt-grid  kt-grid--ver  kt-grid__item--fluid">
					<div class="kt-login-v2__body">
						<div class="kt-login-v2__wrapper">
							<div class="kt-login-v2__container">
								<div class="kt-login-v2__title">
									<h3>Sign to Account</h3>
                                </div>
                                <div id="infoMessage"><?php echo $message;?></div>
								<!-- <form class="kt-login-v2__form kt-form" action="login" method="post" autocomplete="off"> -->
                                <?php echo form_open(
    $action = "auth/login",
    $attributes = array("class" => "kt-login-v2__form kt-form",
                                                                "autocomplete" => "off"
                                                    )
);?>

									<div class="form-group">
										<input class="form-control" type="text" placeholder="Username" name="identity" autocomplete="off">
									</div>
									<div class="form-group">
										<input class="form-control" type="password" placeholder="Password" name="password" autocomplete="off">
									</div>
									<div class="kt-login-v2__actions">
										<a href="#" class="kt-link kt-link--brand">
											Forgot Password ?
										</a>
										<button type="submit" class="btn btn-brand btn-elevate btn-pill">Sign In</button>
									</div>
                                <!-- </form> -->
                                <?php echo form_close();?>
								<div class="kt-separator kt-separator--space-lg  kt-separator--border-solid"></div>

								<h3 class="kt-login-v2__desc">Or sign with social account</h3>
								<div class="kt-login-v2__options">
									<a href="#" class="btn btn-facebook btn-pill">
										<i class="fab fa-facebook-f"></i>
										Facebook
									</a>
									<a href="#" class="btn btn-twitter btn-pill">
										<i class="fab fa-twitter"></i>
										Twitter
									</a>
									<a href="#" class="btn btn-google btn-pill">
										<i class="fab fa-google"></i>
										Google
									</a>
								</div>
							</div>
						</div>
						<div class="kt-login-v2__image">
							<img src="<?php echo base_url(). 'admin/assets/media/misc/bg_icon.svg' ?>" alt="">
						</div>
					</div>
				</div>
				<div class="kt-grid__item">
					<div class="kt-login-v2__footer">
						<div class="kt-login-v2__link">
							<a href="#" class="kt-link kt-font-brand">Privacy</a>
							<a href="#" class="kt-link kt-font-brand">Legal</a>
							<a href="#" class="kt-link kt-font-brand">Contact</a>
						</div>
						<div class="kt-login-v2__info">
							<a href="#" class="kt-link">© Upriselive 2019. All rights reserved.</a>
						</div>
					</div>
				</div>
			</div>
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

		<script src="<?php echo base_url(). 'admin/assets/vendors/general/jquery/dist/jquery.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url(). 'admin/assets/vendors/general/popper.js/dist/umd/popper.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url(). 'admin/assets/vendors/general/bootstrap/dist/js/bootstrap.min.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url(). 'admin/assets/vendors/general/js-cookie/src/js.cookie.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url(). 'admin/assets/vendors/general/moment/min/moment.min.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url(). 'admin/assets/vendors/general/tooltip.js/dist/umd/tooltip.min.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url(). 'admin/assets/vendors/general/perfect-scrollbar/dist/perfect-scrollbar.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url(). 'admin/assets/vendors/general/sticky-js/dist/sticky.min.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url(). 'admin/assets/vendors/general/wnumb/wNumb.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url(). 'admin/assets/vendors/general/jquery-form/dist/jquery.form.min.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url(). 'admin/assets/vendors/general/block-ui/jquery.blockUI.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url(). 'admin/assets/vendors/general/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url(). 'admin/assets/vendors/custom/js/vendors/bootstrap-datepicker.init.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url(). 'admin/assets/vendors/general/bootstrap-datetime-picker/js/bootstrap-datetimepicker.min.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url(). 'admin/assets/vendors/general/bootstrap-timepicker/js/bootstrap-timepicker.min.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url(). 'admin/assets/vendors/custom/js/vendors/bootstrap-timepicker.init.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url(). 'admin/assets/vendors/general/bootstrap-daterangepicker/daterangepicker.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url(). 'admin/assets/vendors/general/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url(). 'admin/assets/vendors/general/bootstrap-maxlength/src/bootstrap-maxlength.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url(). 'admin/assets/vendors/custom/vendors/bootstrap-multiselectsplitter/bootstrap-multiselectsplitter.min.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url(). 'admin/assets/vendors/general/bootstrap-select/dist/js/bootstrap-select.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url(). 'admin/assets/vendors/general/typeahead.js/dist/typeahead.bundle.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url(). 'admin/assets/vendors/general/handlebars/dist/handlebars.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url(). 'admin/assets/vendors/general/inputmask/dist/jquery.inputmask.bundle.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url(). 'admin/assets/vendors/general/inputmask/dist/inputmask/inputmask.date.extensions.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url(). 'admin/assets/vendors/general/inputmask/dist/inputmask/inputmask.numeric.extensions.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url(). 'admin/assets/vendors/general/nouislider/distribute/nouislider.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url(). 'admin/assets/vendors/general/owl.carousel/dist/owl.carousel.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url(). 'admin/assets/vendors/general/autosize/dist/autosize.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url(). 'admin/assets/vendors/general/clipboard/dist/clipboard.min.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url(). 'admin/assets/vendors/general/dropzone/dist/dropzone.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url(). 'admin/assets/vendors/general/summernote/dist/summernote.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url(). 'admin/assets/vendors/general/markdown/lib/markdown.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url(). 'admin/assets/vendors/general/bootstrap-markdown/js/bootstrap-markdown.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url(). 'admin/assets/vendors/custom/js/vendors/bootstrap-markdown.init.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url(). 'admin/assets/vendors/general/jquery-validation/dist/jquery.validate.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url(). 'admin/assets/vendors/general/jquery-validation/dist/additional-methods.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url(). 'admin/assets/vendors/custom/js/vendors/jquery-validation.init.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url(). 'admin/assets/vendors/general/toastr/build/toastr.min.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url(). 'admin/assets/vendors/general/raphael/raphael.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url(). 'admin/assets/vendors/general/morris.js/morris.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url(). 'admin/assets/vendors/general/chart.js/dist/Chart.bundle.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url(). 'admin/assets/vendors/custom/vendors/bootstrap-session-timeout/dist/bootstrap-session-timeout.min.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url(). 'admin/assets/vendors/custom/vendors/jquery-idletimer/idle-timer.min.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url(). 'admin/assets/vendors/general/waypoints/lib/jquery.waypoints.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url(). 'admin/assets/vendors/general/counterup/jquery.counterup.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url(). 'admin/assets/vendors/general/es6-promise-polyfill/promise.min.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url(). 'admin/assets/vendors/general/sweetalert2/dist/sweetalert2.min.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url(). 'admin/assets/vendors/custom/js/vendors/sweetalert2.init.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url(). 'admin/assets/vendors/general/jquery.repeater/src/lib.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url(). 'admin/assets/vendors/general/jquery.repeater/src/jquery.input.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url(). 'admin/assets/vendors/general/jquery.repeater/src/repeater.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url(). 'admin/assets/vendors/general/dompurify/dist/purify.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url(). 'admin/assets/js/demo1/scripts.bundle.js' ?>" type="text/javascript"></script>
		<script src="<?php echo base_url(). 'admin/assets/js/demo1/pages/custom/general/login.js' ?>" type="text/javascript"></script>

	</body>
</html>