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
	$('#location-is-online-button').click(function() {
		$('#event_address').val('This is Online event');
		$('.event_address_fieldset').hide();
		return false;
	});
	$('#js-location-cant-find').click(function() {
		$('#event_address').val('Address');
		$('.event_address_fieldset').show();
	});
	$('.event_address_fieldset').hide();
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
							<?php if(isset($error)) echo $error;?>
							<?php echo validation_errors(); ?>				
							<form action="" method="post" name="event_registration" enctype="multipart/form-data">
								<div class="form-group">
									<input type="text" class="form-control" name="event_title" placeholder="Title" required value="<?php echo !empty($event['event_title'])?$event['event_title']:''; ?>">
								  <?php echo form_error('event_title','<span class="help-block">','</span>'); ?>
								</div>
								<div class="form-group">
									<textarea class="form-control" rows="5" cols="5" name="event_description" placeholder="Description" required><?php echo !empty($event['event_description'])?$event['event_description']:''; ?></textarea>
								</div>
								<div class="form-group">
									<input type="text" class="form-control" name="event_address" id="event_address" placeholder="Address" required value="<?php echo !empty($event['event_address'])?$event['event_address']:''; ?>">
								</div>
								
																	
									<div class="form-group event_address_fieldset">
										<input type="text" class="form-control" name="event_city" id="event_city" placeholder="City" value="<?php echo !empty($event['event_city'])?$event['event_city']:''; ?>">
									</div>
									<div class="form-group event_address_fieldset">
										<input type="text" class="form-control" name="event_state" id="event_state" placeholder="State" value="<?php echo !empty($event['event_state'])?$event['event_state']:''; ?>">
									</div>
									<div class="form-group event_address_fieldset">
										<input type="text" class="form-control" name="event_zipcode" id="event_zipcode" placeholder="Zipcode" value="<?php echo !empty($event['event_zipcode'])?$event['event_zipcode']:''; ?>">
									</div>
								
									<ul class="bullet-list-ico bullet-list-ico--compact text-body-small">
        								<li>
											<a href="javascript:void(0);" class="js-location-is-online" id="location-is-online-button">
												<i class="ico-computer ico--small"></i>
												Online event
											</a>
										</li>
										<li>
											<a href="javascript:void(0);" class="js-location-cant-find" id="js-location-cant-find">
												<i class="ico-location ico--small"></i>
													Enter Address
											</a>
										</li>
									</ul>
									
								<div class="form-group">
									<select class="form-control" name="event_type" id="event_type" required>
										<option value="" selected="selected">Select the type of event</option>
										<option value="19">Appearance or Signing</option>
										<option value="17">Attraction</option>
										<option value="18">Camp, Trip, or Retreat</option>
										<option value="9">Class, Training, or Workshop</option>
										<option value="6">Concert or Performance</option>
										<option value="1">Conference</option>
										<option value="4">Convention</option>
										<option value="8">Dinner or Gala</option>
										<option value="5">Festival or Fair</option>
										<option value="14">Game or Competition</option>
										<option value="10">Meeting or Networking Event</option>
										<option value="100">Other</option>
										<option value="11">Party or Social Gathering</option>
										<option value="15">Race or Endurance Event</option>
										<option value="12">Rally</option>
										<option value="7">Screening</option>
										<option value="2">Seminar or Talk</option>
										<option value="16">Tour</option>
										<option value="13">Tournament</option>
										<option value="3">Tradeshow, Consumer Show, or Expo</option>
									</select>
									<?php echo form_error('event_type','<span class="help-block">','</span>'); ?>
								</div>																
								<div class="form-group">
									<input type="text" class="form-control" name="event_contact" placeholder="Event Contact" required value="<?php echo !empty($event['event_contact'])?$event['event_contact']:''; ?>">
								  <?php echo form_error('event_contact','<span class="help-block">','</span>'); ?>
								</div>								
								<div class="form-group">
									<input type="text" class="form-control date" name="event_starttime" id="event_starttime" placeholder="Event Start Time" required value="<?php echo !empty($event['event_starttime'])?$event['event_starttime']:''; ?>">									
									<?php echo form_error('event_starttime','<span class="help-block">','</span>'); ?>
								</div>
								<div class="form-group">
									<input type="text" class="form-control date" name="event_endtime" id="event_endtime" placeholder="Event End Time" required value="<?php echo !empty($event['event_endtime'])?$event['event_endtime']:''; ?>">
									<?php echo form_error('event_endtime','<span class="help-block">','</span>'); ?>
								</div>								
								<div class="form-group">
									<input type="file" class="form-control" name="event_image" placeholder="Event Image" required>
								  <?php echo form_error('event_image','<span class="help-block">','</span>'); ?>
								</div>
								<div class="form-group">
									<select class="form-control" name="event_privacy" id="event_privacy" required>
										<option value="">Event Visibility</option>
										<option value="Public">Public</option>
										<option value="Private">Private</option>
									</select>
									<?php echo form_error('event_privacy','<span class="help-block">','</span>'); ?>
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