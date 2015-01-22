<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

A PHP Error was encountered

Severity: <?php echo $severity;?>
Message:  <?php echo $message;?>
Filename: <?php echo $filepath;?>
Line Number: <?php echo $line;?>

<?php if (defined('SHOW_DEBUG_BACKTRACE') && SHOW_DEBUG_BACKTRACE === TRUE): ?>

Backtrace:
	<?php foreach (debug_backtrace() as $error): ?>
		<?php if (isset($error['file']) && strpos($error['file'], realpath(BASEPATH)) !== 0): ?>

	File: <?php echo $error['file'];?>
	Line: <?php echo $error['line'];?>
	Function: <?php echo $error['function'];?>

		<?php endif ?>

	<?php endforeach ?>
<?php endif ?>