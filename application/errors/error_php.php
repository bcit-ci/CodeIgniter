<div style="border:1px solid #990000;padding-left:20px;margin:0 0 10px 0;">

<h4>A PHP Error was encountered</h4>

<p>Severity: <?php echo $severity; ?></p>
<p>Message:  <?php echo $message; ?></p>
<p>Filename: <?php echo $filepath; ?></p>
<p>Line Number: <?php echo $line; ?></p>

<?php if(defined('SHOW_ERROR_BACKTRACE') && SHOW_ERROR_BACKTRACE === TRUE): ?>
	
	<p>Backtrace: </p>
	<?php foreach(debug_backtrace() as $error): ?>
	
		<?php if(isset($error['file']) &&  ! stristr($error['file'], SYSDIR)): ?>
			<p>
			File: <?php echo $error['file'] ?><br />
			Line: <?php echo $error['line'] ?><br />
			Function: <?php echo $error['function'] ?>
			</p>
		<? endif ?>
	
	<? endforeach ?></p>

<?php endif ?>

</div>