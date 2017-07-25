<!DOCTYPE html>
<html lang="en">  
<head>
<title>Profile | <?php echo $user['first_name']; ?></title>
<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
<link href="<?php echo base_url(); ?>assets/css/style.css?as3" rel='stylesheet' type='text/css' />
<!--load jquery-->
<script type="text/javascript" src="//code.jquery.com/jquery-2.1.1.min.js"></script>
<script type="text/javascript" src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
</head>
<body>
<?php $this->view('shared/header'); ?>
<div class="container">    
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
<?php $this->view('shared/footer'); ?>
</body>
</html>