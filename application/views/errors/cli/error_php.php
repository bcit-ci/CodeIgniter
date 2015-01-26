<?php
/**
 * CodeIgniter - An open source application development framework for PHP
 *
 * @package	CodeIgniter
 * @author	CodeIgniter Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (http://ellislab.com/)
 * @copyright	Copyright (c) 2014 - 2015, British Columbia Institute of Technology (http://bcit.ca/)
 * @license	http://opensource.org/licenses/MIT	MIT License
 * @link	http://codeigniter.com
 * @since	Version 3.0.0
 * @filesource
 */
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