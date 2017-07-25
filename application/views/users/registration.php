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
							<a href="#" class="active" id="login-form-link">User Registration</a>
						</div>						
					</div>
					<hr>
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-lg-12">   
							<form action="" method="post">
								<div class="form-group">
									<input type="text" class="form-control" name="first_name" placeholder="First Name" required="" value="<?php echo !empty($user['first_name'])?$user['first_name']:''; ?>">
								  <?php echo form_error('first_name','<span class="help-block">','</span>'); ?>
								</div>
								<div class="form-group">
									<input type="text" class="form-control" name="last_name" placeholder="Last Name" required="" value="<?php echo !empty($user['last_name'])?$user['last_name']:''; ?>">
								  <?php echo form_error('last_name','<span class="help-block">','</span>'); ?>
								</div>
								<div class="form-group">
									<input type="email" class="form-control" name="email" placeholder="Email" required="" value="<?php echo !empty($user['email'])?$user['email']:''; ?>">
								  <?php echo form_error('email','<span class="help-block">','</span>'); ?>
								</div>
								<div class="form-group">
								  <input type="password" class="form-control" name="password" placeholder="Password" required="">
								  <?php echo form_error('password','<span class="help-block">','</span>'); ?>
								</div>
								<div class="form-group">
								  <input type="password" class="form-control" name="conf_password" placeholder="Confirm password" required="">
								  <?php echo form_error('conf_password','<span class="help-block">','</span>'); ?>
								</div>
								<div class="form-group">
									<input type="text" class="form-control" name="address" placeholder="Address" required="" value="<?php echo !empty($user['address'])?$user['address']:''; ?>">
								  <?php echo form_error('address','<span class="help-block">','</span>'); ?>
								</div>
								<div class="form-group">
									<input type="text" class="form-control" name="country" placeholder="Country" required="" value="<?php echo !empty($user['country'])?$user['country']:''; ?>">
								  <?php echo form_error('country','<span class="help-block">','</span>'); ?>
								</div>
								<div class="form-group">
									<input type="text" class="form-control" name="state" placeholder="State" required="" value="<?php echo !empty($user['state'])?$user['state']:''; ?>">
								  <?php echo form_error('state','<span class="help-block">','</span>'); ?>
								</div>
								<div class="form-group">
									<input type="text" class="form-control" name="city" placeholder="City" required="" value="<?php echo !empty($user['city'])?$user['city']:''; ?>">
								  <?php echo form_error('city','<span class="help-block">','</span>'); ?>
								</div>
								<div class="form-group">
									<input type="text" class="form-control" name="zipcode" placeholder="Zipcode" required="" value="<?php echo !empty($user['zipcode'])?$user['zipcode']:''; ?>">
								  <?php echo form_error('zipcode','<span class="help-block">','</span>'); ?>
								</div>
								<div class="form-group">
									<input type="text" class="form-control" name="phone" placeholder="Phone" value="<?php echo !empty($user['phone'])?$user['phone']:''; ?>">
								</div>								
								<div class="form-group">
									<?php
									if(!empty($user['gender']) && $user['gender'] == 'Female'){
										$fcheck = 'checked="checked"';
										$mcheck = '';
									}else{
										$mcheck = 'checked="checked"';
										$fcheck = '';
									}
									?>
									<div class="radio">
										<label>
										<input type="radio" name="gender" value="Male" <?php echo $mcheck; ?>>
										Male
										</label>
									</div>
									<div class="radio">
										<label>
										  <input type="radio" name="gender" value="Female" <?php echo $fcheck; ?>>
										  Female
										</label>
									</div>
								</div>
								<div class="form-group">
									<input type="submit" name="regisSubmit" class="btn-primary" value="Submit"/>
								</div>
							</form>
							<p class="footInfo">Already have an account? <a href="<?php echo base_url(); ?>login">Login here</a></p>              
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</body>
</html>