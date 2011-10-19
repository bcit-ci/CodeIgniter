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
		background-color: #393939;
		font: 13px/20px normal Helvetica, Arial, sans-serif;
		color: #4F5155;
	}

	a {
		color: #e64c1b;
		background-color: transparent;
		font-weight: normal;
	}

	h1 {
		color: #444;
		border-bottom: 1px dotted #d9d9d9;
		padding-bottom:8px;
		font-size: 24px;
		font-weight: normal;
	}

	code {
		font-family: Consolas, Monaco, Courier New, Courier, monospace;
		font-size: 12px;
		background-color: #f5f5f5;
		border: 1px dotted #d8d8d8;
		color: #555;
		display: block;
		margin: 14px 0 14px 0;
		padding: 12px 10px 12px 10px;
	}

	#body{
		background-color:#fff;
		padding:1em 2em;
		border-bottom:6px solid #e64c1b;
	}
	
	#container{
		width:640px;
		margin:5em auto 0 auto;
	}

	#container>img
	{
		margin:0 auto;
		display:block;
		margin-bottom:1.5em;
	}

	#footer
	{
		text-align:right;
		text-transform:uppercase;
		font-size:10px;
		color:#888;
	}

	#footer em
	{
		font-style:normal;
		color:#ccc;
	}
	</style>
</head>
<body>

<div id="container">
	<!-- CodeIgniter Logo as BASE64 Data -->
	<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADAAAAA/CAYAAACmVEtSAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyBpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMC1jMDYwIDYxLjEzNDc3NywgMjAxMC8wMi8xMi0xNzozMjowMCAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNSBXaW5kb3dzIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOkNDNTAyNjQzRTFFNTExRTBBNkMwRjMxM0JEMjM2RTA1IiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOkNDNTAyNjQ0RTFFNTExRTBBNkMwRjMxM0JEMjM2RTA1Ij4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6Q0M1MDI2NDFFMUU1MTFFMEE2QzBGMzEzQkQyMzZFMDUiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6Q0M1MDI2NDJFMUU1MTFFMEE2QzBGMzEzQkQyMzZFMDUiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz7SPhHYAAAJFUlEQVR42sxaCchVRRSe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" alt="CodeIgniter">
	<div id="body">
		<h1><?php echo $heading; ?></h1>
		<?php echo $message; ?>	
	</div>
</div>

</body>
</html>