<!DOCTYPE html>
<html lang="en">  
<head>
<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
<link href="<?php echo base_url(); ?>assets/css/style.css?7678" rel='stylesheet' type='text/css' />
</head>
<body>
<div class="container">
	<div class="row">
		<div class="col-md-6 col-md-offset-3">
			<div class="panel panel-login">
				<div class="panel-heading">
					<div class="row">
						<div class="col-xs-12">
							<a href="#" class="active" id="login-form-link">Login</a>
						</div>						
					</div>
					<hr>
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-lg-12">
							<?php
							if(!empty($success_msg)){
								echo '<p class="statusMsg">'.$success_msg.'</p>';
							}elseif(!empty($error_msg)){
								echo '<p class="statusMsg">'.$error_msg.'</p>';
							}
							?>
							<form action="" method="post">
								<div class="form-group has-feedback">
									<input type="email" class="form-control" name="email" placeholder="Email" required="" value="">
									<?php echo form_error('email','<span class="help-block">','</span>'); ?>
								</div>
								<div class="form-group">
								  <input type="password" class="form-control" name="password" placeholder="Password" required="">
								  <?php echo form_error('password','<span class="help-block">','</span>'); ?>
								</div>
								<div class="form-group">
									<input type="submit" name="loginSubmit" class="btn-primary" value="Submit"/>
								</div>
							</form>						
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</body>
</html>