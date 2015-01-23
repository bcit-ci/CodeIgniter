<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

An uncaught Exception was encountered

Type: <?php echo get_class($exception); ?>
Message: <?php echo $message; ?>
Filename: <?php echo $exception->getFile(); ?>
Line Number: <?php echo $exception->getLine(); ?>

<?php if (defined('SHOW_DEBUG_BACKTRACE') && SHOW_DEBUG_BACKTRACE === TRUE): ?>

Backtrace:
	<?php foreach ($exception->getTrace() as $error): ?>
		<?php if (isset($error['file']) && strpos($error['file'], realpath(BASEPATH)) !== 0): ?>

	File: <?php echo $error['file']; ?>
	Line: <?php echo $error['line']; ?>
	Function: <?php echo $error['function']; ?>

		<?php endif ?>

	<?php endforeach ?>
<?php endif ?>