<!DOCTYPE html>
<html lang="en">  
<head>
<title>Event Registration</title>
<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
<link href="<?php echo base_url(); ?>assets/css/style.css?2121" rel='stylesheet' type='text/css' />

<!--load jquery-->
<script type="text/javascript" src="//code.jquery.com/jquery-2.1.1.min.js"></script>

<script type="text/javascript" src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>

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
		$('.event_online_fieldset').show();
		$('.event_address_fieldset').hide();
		$('#event_place').val(1);
		$('.location').prop('required',false);	
		$('.location').val(function() {
			return this.defaultValue;
		});
	});
	$('#js-location-cant-find').click(function() {
		$('.event_online_fieldset').hide();
		$('.event_address_fieldset').show();
		$('#event_place').val(2);
		$('.location').prop('required',true);		
	});
	$('.event_address_fieldset').hide();
});
</script>
</head>
<body>
<?php $this->view('shared/header'); ?>
<!-- ================ INICIA FORMULARIO DE LOGIN ============================================================== -->    


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
								<div class="form-group event_online_fieldset">
									<input type="text" class="form-control" name="event_online" id="event_online" readonly placeholder="This is Online event" value="<?php echo !empty($event['event_online'])?$event['event_online']:''; ?>">
									<ul class="bullet-list-ico bullet-list-ico--compact text-body-small">
										<li>
											<a href="javascript:void(0);" class="js-location-cant-find" id="js-location-cant-find">
												<i class="ico-location ico--small"></i>
													Add a location
											</a>
										</li>
									</ul>
								</div>
								<div class="form-group event_address_fieldset">
									<input type="text" class="form-control location" name="event_venue" id="event_venue" placeholder="Enter the venue's name" value="<?php echo !empty($event['event_venue'])?$event['event_venue']:''; ?>">
								</div>
								<div class="form-group event_address_fieldset">
									<input type="text" class="form-control location" name="event_address" id="event_address" placeholder="Address" value="<?php echo !empty($event['event_address'])?$event['event_address']:''; ?>">
								</div>																								
								<div class="form-group event_address_fieldset">
									<input type="text" class="form-control location" name="event_city" id="event_city" placeholder="City" value="<?php echo !empty($event['event_city'])?$event['event_city']:''; ?>">
								</div>
								<div class="form-group event_address_fieldset">
									<input type="text" class="form-control location" name="event_state" id="event_state" placeholder="State" value="<?php echo !empty($event['event_state'])?$event['event_state']:''; ?>">
								</div>
								<div class="form-group event_address_fieldset">
									<input type="text" class="form-control location" name="event_zipcode" id="event_zipcode" placeholder="Zipcode" value="<?php echo !empty($event['event_zipcode'])?$event['event_zipcode']:''; ?>">
									<ul class="bullet-list-ico bullet-list-ico--compact text-body-small">
										<li>
											<a href="javascript:void(0);" class="js-location-is-online" id="location-is-online-button">
												<i class="ico-computer ico--small"></i>
												Reset location
											</a>
										</li>										
									</ul>
								</div>														
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
									<input type="text" class="form-control" name="event_contact" placeholder="Who's organizing this event?" required value="<?php echo !empty($event['event_contact'])?$event['event_contact']:''; ?>">
									<?php echo form_error('event_contact','<span class="help-block">','</span>'); ?>
								</div>
								<div class="form-group">
									<textarea class="form-control" rows="5" cols="5" name="event_contact_description" placeholder="Organizer Description"><?php echo !empty($event['event_contact_description'])?$event['event_contact_description']:''; ?></textarea>
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
									<div class="g-group js-ticket-summary-footer create-tickets--footer l-pad-vert-2">
										<div>
											<div class="g-cell g-cell-1-1 btn-group btn-group--responsive l-align-center l-pad-bot-1">
												<a href="#" class="js-create-ticket btn btn--ico btn--secondary l-mar-top-1" data-type="free" id="create-ticket-free-button">
													<i class="ico-circle-plus"></i>Free ticket
												</a>											
												<a href="#" class="js-create-ticket btn btn--ico btn--secondary l-mar-top-1" data-type="paid" id="create-ticket-paid-button">
													<i class="ico-circle-plus"></i>Paid ticket
												</a>
												<a href="#" class="js-create-ticket btn btn--ico btn--secondary l-mar-top-1" data-type="donation" id="create-ticket-donation-button">
													<i class="ico-circle-plus"></i>Donation
												</a>
											</div>											
										</div>
									</div>
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
									<input type="hidden" name="event_place" id="event_place" value="1">
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