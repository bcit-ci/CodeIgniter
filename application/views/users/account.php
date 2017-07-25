<!DOCTYPE html>
<html lang="en">  
<head>
<title>Profile | <?php echo $user['first_name']; ?></title>
<link href="<?php echo base_url(); ?>assets/css/style.css" rel='stylesheet' type='text/css' />
</head>
<body>
<div class="container">
    <h2>User Account</h2> | <span><a href="<?php echo base_url(); ?>event">Create Event</a></span> | <span><a href="<?php echo base_url(); ?>logout">Logout</a></span>
    <h3>Welcome <?php echo $user['first_name']; ?>!</h3>
    <div class="account-info">
        <p><b>Name: </b><?php echo $user['first_name']." ".$user['last_name']; ?></p>
        <p><b>Email: </b><?php echo $user['email']; ?></p>
		<p><b>Gender: </b><?php echo $user['gender']; ?></p>
		<p><b>Address: </b><?php echo $user['address']; ?></p>
		<p><b>Country: </b><?php echo $user['country']; ?></p>
		<p><b>State: </b><?php echo $user['state']; ?></p>
		<p><b>City: </b><?php echo $user['city']; ?></p>
		<p><b>Zipcode: </b><?php echo $user['zipcode']; ?></p>
		<p><b>Gender: </b><?php echo $user['gender']; ?></p>
        <p><b>Created: </b><?php echo $user['created']; ?></p>        
    </div>
</div>
</body>
</html>