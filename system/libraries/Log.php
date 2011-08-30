<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2011, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Logging Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Logging
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/general/errors.html
 */
class CI_Log {

	protected $_log_path;
	protected $_threshold		= 1;
	protected $_threshold_max	= 0;
	protected $_threshold_array	= array();
	protected $_date_fmt		= 'Y-m-d H:i:s';
	protected $_enabled			= TRUE;
	protected $_levels			= array('ERROR' => '1', 'DEBUG' => '2',  'INFO' => '3', 'ALL' => '4');

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$CI =& get_instance();

		$log_path = $CI->config->item('log_path');
		$this->_log_path = ($log_path != '') ? $log_path : APPPATH.'logs/';

		if ( ! is_dir($this->_log_path) OR ! $CI->is_really_writable($this->_log_path))
		{
			$this->_enabled = FALSE;
		}

		$threshold = $CI->config->item('log_threshold');
		if (is_numeric($threshold))
		{
			$this->_threshold = $threshold;
		}
		elseif (is_array($config['log_threshold']))
		{
			$this->_threshold = $this->_threshold_max;
			$this->_threshold_array = array_flip($config['log_threshold']);
		}

		$format = $CI->config->item('log_date_format');
		if ($format != '')
		{
			$this->_date_fmt = $format;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Write Log File
	 *
	 * Generally this function will be called using the global log_message() function
	 *
	 * @param	string	the error level
	 * @param	string	the error message
	 * @param	bool	whether the error is a native PHP error
	 * @return	bool
	 */
	public function write_log($level = 'error', $msg, $php_error = FALSE)
	{
		if ($this->_enabled === FALSE)
		{
			return FALSE;
		}

		$level = strtoupper($level);

		if ( ! isset($this->_levels[$level]) OR ($this->_levels[$level] > $this->_threshold))
		{
			if (empty($this->_threshold_array) OR ! isset($this->_threshold_array[$this->_levels[$level]]))
			{
				return FALSE;
			}
		}


		$filepath = $this->_log_path.'log-'.date('Y-m-d').'.php';
		$message  = '';

		if ( ! file_exists($filepath))
		{
			$message .= "<"."?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?".">\n\n";
		}

		if ( ! $fp = @fopen($filepath, FOPEN_WRITE_CREATE))
		{
			return FALSE;
		}

		$message .= $level.' '.(($level == 'INFO') ? ' -' : '-').' '.date($this->_date_fmt). ' --> '.$msg."\n";

		flock($fp, LOCK_EX);
		fwrite($fp, $message);
		flock($fp, LOCK_UN);
		fclose($fp);

		@chmod($filepath, FILE_WRITE_MODE);
		return TRUE;
	}

}
// END Log Class

/* End of file Log.php */
/* Location: ./system/libraries/Log.php */
