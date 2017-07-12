<!DOCTYPE html>
<html lang="en">  
<head>
<title>Event Registration</title>
<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
<link href="<?php echo base_url(); ?>assets/css/style.css?7678" rel='stylesheet' type='text/css' />

<!--load jquery-->
<script type="text/javascript" src="//code.jquery.com/jquery-2.1.1.min.js"></script>

<link type="text/css" rel="stylesheet" href="<?php echo base_url('assets/css/jquery.datetimepicker.css'); ?>" />

<script type="text/javascript" src="<?php echo base_url('assets/js/jquery.datetimepicker.full.js'); ?>"></script>

<!--load jquery ui js file-->

<script type="text/javascript">
$(function() {		
    $("#event_starttime").datetimepicker({
		format: 'Y-m-d H:i:s'
	});
	$("#event_endtime").datetimepicker({
		format: 'Y-m-d H:i:s'
	});
});
</script>
</head>
<body>
<div class="container">
	<div class="row">
		<div class="col-md-6 col-md-offset-3">
			<div class="panel panel-login">
				<div class="panel-heading">
					<div class="row">
						<div class="col-xs-12">
							<a href="#" class="active" id="login-form-link">Event Registration</a>
						</div>						
					</div>
					<hr>
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-lg-12">   
							<form action="" method="post">
								<div class="form-group">
									<input type="text" class="form-control" name="event_title" placeholder="Event Title" required="" value="<?php echo !empty($user['event_title'])?$user['event_title']:''; ?>">
								  <?php echo form_error('event_title','<span class="help-block">','</span>'); ?>
								</div>
								<div class="form-group">
									<input type="text" class="form-control" name="event_description" placeholder="Event Description" required="" value="<?php echo !empty($user['event_description'])?$user['event_description']:''; ?>">
								  <?php echo form_error('event_description','<span class="help-block">','</span>'); ?>
								</div>
								<div class="form-group">
									<input type="text" class="form-control" name="event_type" placeholder="Event Type" required="" value="<?php echo !empty($user['event_type'])?$user['event_type']:''; ?>">
								  <?php echo form_error('event_type','<span class="help-block">','</span>'); ?>
								</div>																
								<div class="form-group">
									<input type="text" class="form-control" name="event_contact" placeholder="Event Contact" required="" value="<?php echo !empty($user['event_contact'])?$user['event_contact']:''; ?>">
								  <?php echo form_error('event_contact','<span class="help-block">','</span>'); ?>
								</div>
								<div class="form-group">
									<input type="text" class="form-control date" name="event_starttime" id="event_starttime" placeholder="Event Start Time" required="" value="<?php echo !empty($user['event_starttime'])?$user['event_starttime']:''; ?>">
								  <?php echo form_error('event_starttime','<span class="help-block">','</span>'); ?>
								</div>
								<div class="form-group">
									<input type="text" class="form-control date" name="event_endtime" id="event_endtime" placeholder="Event End Time" required="" value="<?php echo !empty($user['event_endtime'])?$user['event_endtime']:''; ?>">
								  <?php echo form_error('event_endtime','<span class="help-block">','</span>'); ?>
								</div>								
								<div class="form-group">
									<input type="file" class="form-control" name="event_image" placeholder="Event Image" required="">
								  <?php echo form_error('event_image','<span class="help-block">','</span>'); ?>
								</div>																			
								<div class="form-group">
									<input type="submit" name="eventSubmit" class="btn-primary" value="Submit"/>
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