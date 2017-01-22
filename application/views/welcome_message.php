<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Welcome to CodeIgniter</title>

	<style type="text/css">

	::selection { background-color: #f07746; color: #fff; }
	::-moz-selection { background-color: #f07746; color: #fff; }

	body {
		background-color: #fff;
		margin: 40px auto;
		max-width: 1024px;
		font: 16px/24px normal "Helvetica Neue",Helvetica,Arial,sans-serif;
		color: #808080;
	}

	a {
		color: #dd4814;
		background-color: transparent;
		font-weight: normal;
		text-decoration: none;
	}

	a:hover {
	   color: #97310e;
	}

	h1 {
		color: #fff;
		background-color: #dd4814;
		border-bottom: 1px solid #d0d0d0;
		font-size: 22px;
		font-weight: bold;
		margin: 0 0 14px 0;
		padding: 5px 10px;
		line-height: 40px;
	}

	h1 img {
		display: block;
	}

	h2 {
		color:#404040;
		margin:0;
		padding:0 0 10px 0;
	}

	code {
		font-family: Consolas, Monaco, Courier New, Courier, monospace;
		font-size: 13px;
		background-color: #f5f5f5;
		border: 1px solid #e3e3e3;
		border-radius: 4px;
		color: #002166;
		display: block;
		margin: 14px 0 14px 0;
		padding: 12px 10px 12px 10px;
	}

	#body {
		margin: 0 15px 0 15px;
		min-height: 96px;
	}

	p {
		 margin: 0 0 10px;
		 padding:0;
	}

	p.footer {
		text-align: right;
		font-size: 12px;
		border-top: 1px solid #d0d0d0;
		line-height: 32px;
		padding: 0 10px 0 10px;
		margin: 20px 0 0 0;
		background:#8ba8af;
		color:#fff;
	}

	#container {
		margin: 10px;
		border: 1px solid #d0d0d0;
		box-shadow: 0 0 8px #d0d0d0;
		border-radius: 4px;
	}
	</style>
</head>
<body>
	<div id="container">
		<h1>
			<img alt="CodeIgniter" src="data:image/png;base64,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"/>
		</h1>

		<div id="body">
			<p>The page you are looking at is being generated dynamically by CodeIgniter.</p>

			<p>If you would like to edit this page you'll find it located at:</p>
			<code>application/views/welcome_message.php</code>

			<p>The corresponding controller for this page is found at:</p>
			<code>application/controllers/Welcome.php</code>

			<p>If you are exploring CodeIgniter for the very first time, you should start by reading the <a href="user_guide/">User Guide</a>.</p>
		</div>

		<p class="footer">Page rendered in <strong>{elapsed_time}</strong> seconds. <?php echo  (ENVIRONMENT === 'development') ?  'CodeIgniter Version <strong>' . CI_VERSION . '</strong>' : '' ?></p>
	</div>
</body>
</html>
