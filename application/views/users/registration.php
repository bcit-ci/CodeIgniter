<!DOCTYPE html>
<html lang="en">  
<head>
<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
<link href="<?php echo base_url(); ?>assets/css/style.css?7678" rel='stylesheet' type='text/css' />
</head>
<body>
<?php $this->view('shared/header'); ?>
<div class="container">
	<div class="row">
		<div class="col-xs-12 col-sm-8 col-md-6 col-sm-offset-2 col-md-offset-3">
			<div class="row">
				<div class="col-lg-12">												
					<form role="form" action="" method="post">
					<fieldset>
					<h2>User Registration</h2>
					<hr class="colorgraph">
						<div class="form-group">
							<input type="text" class="form-control input-lg" name="first_name" placeholder="First Name" required="" value="<?php echo !empty($user['first_name'])?$user['first_name']:''; ?>">
						  <?php echo form_error('first_name','<span class="help-block">','</span>'); ?>
						</div>
						<div class="form-group">
							<input type="text" class="form-control input-lg" name="last_name" placeholder="Last Name" required="" value="<?php echo !empty($user['last_name'])?$user['last_name']:''; ?>">
						  <?php echo form_error('last_name','<span class="help-block">','</span>'); ?>
						</div>
						<div class="form-group">
							<input type="email" class="form-control input-lg" name="email" placeholder="Email" required="" value="<?php echo !empty($user['email'])?$user['email']:''; ?>">
						  <?php echo form_error('email','<span class="help-block">','</span>'); ?>
						</div>
						<div class="form-group">
						  <input type="password" class="form-control input-lg" name="password" placeholder="Password" required="">
						  <?php echo form_error('password','<span class="help-block">','</span>'); ?>
						</div>
						<div class="form-group">
						  <input type="password" class="form-control input-lg" name="conf_password" placeholder="Confirm password" required="">
						  <?php echo form_error('conf_password','<span class="help-block">','</span>'); ?>
						</div>
						<div class="form-group">
							<input type="text" class="form-control input-lg" name="address" placeholder="Address" required="" value="<?php echo !empty($user['address'])?$user['address']:''; ?>">
						  <?php echo form_error('address','<span class="help-block">','</span>'); ?>
						</div>
						<div class="form-group">
							<input type="text" class="form-control input-lg" name="country" placeholder="Country" required="" value="<?php echo !empty($user['country'])?$user['country']:''; ?>">
						  <?php echo form_error('country','<span class="help-block">','</span>'); ?>
						</div>
						<div class="form-group">
							<input type="text" class="form-control input-lg" name="state" placeholder="State" required="" value="<?php echo !empty($user['state'])?$user['state']:''; ?>">
						  <?php echo form_error('state','<span class="help-block">','</span>'); ?>
						</div>
						<div class="form-group">
							<input type="text" class="form-control input-lg" name="city" placeholder="City" required="" value="<?php echo !empty($user['city'])?$user['city']:''; ?>">
						  <?php echo form_error('city','<span class="help-block">','</span>'); ?>
						</div>
						<div class="form-group">
							<input type="text" class="form-control input-lg" name="zipcode" placeholder="Zipcode" required="" value="<?php echo !empty($user['zipcode'])?$user['zipcode']:''; ?>">
						  <?php echo form_error('zipcode','<span class="help-block">','</span>'); ?>
						</div>
						<div class="form-group">
							<input type="text" class="form-control input-lg" name="phone" placeholder="Phone" value="<?php echo !empty($user['phone'])?$user['phone']:''; ?>">
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
						<hr class="colorgraph">
						<div class="form-group">
							<input type="submit" name="regisSubmit" class="btn btn-lg btn-success btn-block" value="Register"/>
						</div>
						</fieldset>
					</form>
					<p class="footInfo">Already have an account? <a href="<?php echo base_url(); ?>login">Login here</a></p>					
				</div>
			</div>
				
			
		</div>
	</div>
</div>
<?php $this->view('shared/footer'); ?>
</body>
</html>