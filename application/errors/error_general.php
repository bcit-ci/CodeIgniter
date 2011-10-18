<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Error</title>
<style type="text/css">

::selection{ background-color: #E13300; color: white; }
::moz-selection{ background-color: #E13300; color: white; }
::webkit-selection{ background-color: #E13300; color: white; }

body {
	background-color: #fff;
	margin: 40px;
	font: 13px/20px normal Helvetica, Arial, sans-serif;
	color: #4F5155;
}

h1 {
	color: #444;
	background-color: transparent;
	border-bottom: 1px solid #D0D0D0;
	font-size: 19px;
	font-weight: normal;
	margin: 0 0 14px 0;
	padding: 14px 15px 10px 15px;
}

#container {
	margin: 10px;
	border: 1px solid #D0D0D0;
	box-shadow: 0 0 8px #D0D0D0;
	-webkit-box-shadow: 0 0 8px #D0D0D0;
	-moz-box-shadow: 0 0 8px #D0D0D0;
}

p {
	margin: 12px 15px 12px 15px;
}
p.footer{
		text-align: right;
		font-size: 11px;
		border-top: 1px solid #DDD;
		line-height: 32px;
		padding: 0 10px 0 10px;
		margin: 20px 0 0 0;
}
</style>
</head>
<body>
	<div id="container">
		<h1><?php echo $heading; ?></h1>
		<?php echo $message; ?>
		<?php echo  (ENVIRONMENT == 'development') ?  '<p class="footer">CodeIgniter Version <strong>' . CI_VERSION . '</strong></p>' : '' ?>
	</div>
</body>
</html>