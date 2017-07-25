<!DOCTYPE html>
<html lang="en">  
<head>
<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
<link href="<?php echo base_url(); ?>assets/css/style.css?7678" rel='stylesheet' type='text/css' />
<!--load jquery-->
<script type="text/javascript" src="//code.jquery.com/jquery-2.1.1.min.js"></script>
<script type="text/javascript">
$(function(){
    $('.button-checkbox').each(function(){
    	var $widget = $(this),
			$button = $widget.find('button'),
			$checkbox = $widget.find('input:checkbox'),
			color = $button.data('color'),
			settings = {
					on: {
						icon: 'glyphicon glyphicon-check'
					},
					off: {
						icon: 'glyphicon glyphicon-unchecked'
					}
			};

		$button.on('click', function () {
			$checkbox.prop('checked', !$checkbox.is(':checked'));
			$checkbox.triggerHandler('change');
			updateDisplay();
		});

		$checkbox.on('change', function () {
			updateDisplay();
		});

		function updateDisplay() {
			var isChecked = $checkbox.is(':checked');
			// Set the button's state
			$button.data('state', (isChecked) ? "on" : "off");

			// Set the button's icon
			$button.find('.state-icon')
				.removeClass()
				.addClass('state-icon ' + settings[$button.data('state')].icon);

			// Update the button's color
			if (isChecked) {
				$button
					.removeClass('btn-default')
					.addClass('btn-' + color + ' active');
			} 
            else 
            { 
                $button
					.removeClass('btn-' + color + ' active')
					.addClass('btn-default');
			}
		}
		function init() {
			updateDisplay();
			// Inject the icon if applicable
			if ($button.find('.state-icon').length == 0) {
				$button.prepend('<i class="state-icon ' + settings[$button.data('state')].icon + '"></i> ');
			}
		}
		init();
	});
});
</script>
</head>
<body>
<?php $this->view('shared/header'); ?>
<div class="container">    
	<div class="row" style="margin-top:60px">
		<div class="col-xs-12 col-sm-8 col-md-6 col-sm-offset-2 col-md-offset-3">
			<?php
			if(!empty($success_msg)){
				echo '<p class="statusMsg">'.$success_msg.'</p>';
			}elseif(!empty($error_msg)){
				echo '<p class="statusMsg">'.$error_msg.'</p>';
			}
			?>
			<form role="form" action="" method="post">
				<fieldset>
					<h2>Please Sign In</h2>
					<hr class="colorgraph">
					<div class="form-group">
						<input type="email" name="email" id="email" class="form-control input-lg" placeholder="Email Address">
						<?php echo form_error('email','<span class="help-block">','</span>'); ?>
					</div>
					<div class="form-group">
						<input type="password" name="password" id="password" class="form-control input-lg" placeholder="Password">
						<?php echo form_error('password','<span class="help-block">','</span>'); ?>
					</div>
					<span class="button-checkbox">
						<button type="button" class="btn" data-color="info">Remember Me</button>
						<input type="checkbox" name="remember_me" id="remember_me" checked="checked" class="hidden">
						<a href="forgot-password" class="btn btn-link pull-right">Forgot Password?</a>
					</span>
					<hr class="colorgraph">
					<div class="row">
						<div class="col-xs-6 col-sm-6 col-md-6">
							<input type="submit" class="btn btn-lg btn-success btn-block" name="loginSubmit" value="Sign In">
						</div>
						<div class="col-xs-6 col-sm-6 col-md-6">
							<a href="register" class="btn btn-lg btn-primary btn-block">Register</a>
						</div>
					</div>
				</fieldset>
			</form>
		</div>
	</div>
</div> 
</body>
</html>