<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Welcome to CodeIgniter</title>

    <style type="text/css">

	    @font-face {
	        font-family: 'amblebold_italic';
	        src: url('https://cdn.rawgit.com/bcit-ci/codeigniter-website/develop/assets/fonts/Amble-BoldItalic-webfont.eot');
	        src: url('https://cdn.rawgit.com/bcit-ci/codeigniter-website/develop/assets/fonts/Amble-BoldItalic-webfont.eot?#iefix') format('embedded-opentype'),
	            url('https://cdn.rawgit.com/bcit-ci/codeigniter-website/develop/assets/fonts/Amble-BoldItalic-webfont.woff') format('woff'),
	            url('https://cdn.rawgit.com/bcit-ci/codeigniter-website/develop/assets/fonts/Amble-BoldItalic-webfont.ttf') format('truetype'),
	            url('https://cdn.rawgit.com/bcit-ci/codeigniter-website/develop/assets/fonts/Amble-BoldItalic-webfont.svg#amblebold_italic') format('svg');
	        font-weight: normal;
	        font-style: normal;
	    }

	    ::selection { background-color: #F07746; color: white; }
	    ::-moz-selection { background-color: #F07746; color: white; }

	    body {
	        background-color: #FFF;
	        margin: 40px;
	        font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
		    font-size: 16px;
		    line-height: 1.5;
	        font-weight: normal;
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
	        color: #FFF;
	        background-color: #DD4814;
	        border-bottom: 1px solid #D0D0D0;
	        font-size: 19px;
	        font-weight: normal;
	        margin: 0 0 14px 0;
	        padding: 5px 10px;
	        line-height: 40px;
	        font-size: 26px;
	        font-family: 'amblebold_italic', 'Helvetica Neue', Helvetica,Arial, sans-serif;
	    }

	    h1 img {
	        display: inline-block;
	        float:left;
	        padding-right:8px;
	    }

	    code {
	        font-family: Consolas, Monaco, Courier New, Courier, monospace;
	        font-size: 13px;
	        background-color: #F5F5F5;
	        border: 1px solid #E3E3E3;
	        border-radius: 4px;
	        color: #002166;
	        display: block;
	        margin: 14px 0 14px 0;
	        padding: 12px 10px 12px 10px;
	    }

	    #body {
	        margin: 0 15px 0 15px;
	    }
		p {
			 margin: 0 0 10px;
			 padding:0;
		}
	    p.footer {
	        text-align: right;
	        font-size: 11px;
	        border-top: 1px solid #D0D0D0;
	        line-height: 32px;
	        padding: 0 10px 0 10px;
	        margin: 20px 0 0 0;
	        background:#8BA8AF;
	        color:#FFF;

	    }

	    #container {
	        margin: 10px;
	        border: 1px solid #D0D0D0;
	        box-shadow: 0 0 8px #D0D0D0;
			border-radius: 4px;
	    }
	    #wrapper {
			margin:0 auto;
			max-width: 1024px;
	    }
    </style>
</head>

<body>
	<div id="wrapper">
		<div id="container">
		    <h1>
		        <img class="logo" src="https://cdn.rawgit.com/bcit-ci/codeigniter-website/develop/assets/images/ci-logo-white.png" />
		        Welcome to CodeIgniter!
		    </h1>

		    <div id="body">
		        <p>The page you are looking at is being generated dynamically by CodeIgniter.</p>

		        <p>If you would like to edit this page you'll find it located at:</p>
		        <code>application/views/welcome_message.php</code>

		        <p>The corresponding controller for this page is found at:</p>
		        <code>application/controllers/Welcome.php</code>

		        <p>If you are exploring CodeIgniter for the very first time, you should start by reading the
		           <a href="user_guide/">User Guide</a>.
		       </p>
		    </div>

		    <p class="footer">Page rendered in <strong>{elapsed_time}</strong> seconds.
		    	<?php echo  (ENVIRONMENT === 'development') ?  'CodeIgniter Version <strong>' . CI_VERSION . '</strong>' : '' ?>
			</p>
		</div>
	</div>
</body>
</html>
