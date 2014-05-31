<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.2.4 or newer
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Open Software License version 3.0
 *
 * This source file is subject to the Open Software License (OSL 3.0) that is
 * bundled with this package in the files license.txt / license.rst.  It is
 * also available through the world wide web at this URL:
 * http://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to obtain it
 * through the world wide web, please send an email to
 * licensing@ellislab.com so we can send you a copy immediately.
 *
 * @package		CodeIgniter
 * @author		Andrey Andreev
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (http://ellislab.com/)
 * @license		http://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * @link		http://codeigniter.com
 * @since		Version 3.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CodeIgniter Session Files Driver
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Sessions
 * @author		Andrey Andreev
 * @link		http://codeigniter.com/user_guide/libraries/sessions.html
 */
class CI_Session_files_driver extends CI_Session_driver implements SessionHandlerInterface {

	/**
	 * Save path
	 *
	 * @var	string
	 */
	protected $_save_path;

	/**
	 * File handle
	 *
	 * @var	resource
	 */
	protected $_file_handle;

	/**
	 * File name
	 *
	 * @var	resource
	 */
	protected $_file_path;

	/**
	 * File new flag
	 *
	 * @var	bool
	 */
	protected $_file_new;

	// ------------------------------------------------------------------------

	/**
	 * Class constructor
	 *
	 * @param	array	$params	Configuration parameters
	 * @return	void
	 */
	public function __construct(&$params)
	{
		parent::__construct($params);

		if (isset($this->_save_path))
		{
			$this->_save_path = rtrim($this->_save_path, '/\\');
			ini_set('session.save_path', $this->_save_path);
		}
		else
		{
			$this->_save_path = rtrim(ini_get('session.save_path'), '/\\');
		}
	}

	// ------------------------------------------------------------------------

	public function open($save_path, $name)
	{
		if ( ! is_dir($save_path) && ! mkdir($save_path, 0700, TRUE))
		{
			log_message('error', "Session: Configured save path '".$this->_save_path."' is not a directory, doesn't exist or cannot be created.");
			return FALSE;
		}

		$this->_save_path = $save_path;
		$this->_file_path = $this->_save_path.DIRECTORY_SEPARATOR
			.$name // we'll use the session cookie name as a prefix to avoid collisions
			.($this->_match_ip ? md5($_SERVER['REMOTE_ADDR']) : '');

		return TRUE;
	}

	// ------------------------------------------------------------------------

	public function read($session_id)
	{
		// This might seem weird, but PHP 5.6 introduces session_reset(),
		// which re-reads session data
		if ($this->_file_handle === NULL)
		{
			$this->_file_path .= $session_id;

			// Just using fopen() with 'c+b' mode would be perfect, but it is only
			// available since PHP 5.2.6 and we have to set permissions for new files,
			// so we'd have to hack around this ...
			if (($this->_file_new = ! file_exists($this->_file_path)) === TRUE)
			{
				if (($this->_file_handle = fopen($this->_file_path, 'w+b')) === FALSE)
				{
					log_message('error', "Session: File '".$this->_file_path."' doesn't exist and cannot be created.");
					return FALSE;
				}
			}
			elseif (($this->_file_handle = fopen($this->_file_path, 'r+b')) === FALSE)
			{
				log_message('error', "Session: Unable to open file '".$this->_file_path."'.");
				return FALSE;
			}

			if (flock($this->_file_handle, LOCK_EX) === FALSE)
			{
				log_message('error', "Session: Unable to obtain lock for file '".$this->_file_path."'.");
				fclose($this->_file_handle);
				$this->_file_handle = NULL;
				return FALSE;
			}

			if ($this->_file_new)
			{
				chmod($this->_file_path, 0600);
				$this->_fingerprint = md5('');
				return '';
			}
		}
		else
		{
			rewind($this->_file_handle);
		}

		$session_data = '';
		for ($read = 0, $length = filesize($this->_file_path); $read < $length; $read += strlen($buffer))
		{
			if (($buffer = fread($this->_file_handle, $length - $read)) === FALSE)
			{
				break;
			}

			$session_data .= $buffer;
		}

		$this->_fingerprint = md5($session_data);
		return $session_data;
	}

	public function write($session_id, $session_data)
	{
		if ( ! is_resource($this->_file_handle))
		{
			return FALSE;
		}
		elseif ($this->_fingerprint === md5($session_data))
		{
			return ($this->_file_new)
				? TRUE
				: touch($this->_file_path);
		}

		if ( ! $this->_file_new)
		{
			ftruncate($this->_file_handle, 0);
			rewind($this->_file_handle);
		}

		for ($written = 0, $length = strlen($session_data); $written < $length; $written += $result)
		{
			if (($result = fwrite($this->_file_handle, substr($session_data, $written))) === FALSE)
			{
				break;
			}
		}

		if ( ! is_int($result))
		{
			$this->_fingerprint = md5(substr($session_data, 0, $written));
			log_message('error', 'Session: Unable to write data.');
			return FALSE;
		}

		$this->_fingerprint = md5($session_data);
		return TRUE;
	}

	// ------------------------------------------------------------------------

	public function close()
	{
		if (is_resource($this->_file_handle))
		{
			flock($this->_file_handle, LOCK_UN);
			fclose($this->_file_handle);

			$this->_file_handle = $this->_file_new = NULL;
			return TRUE;
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	public function destroy($session_id)
	{
		if ($this->close())
		{
			return unlink($this->_file_path) && $this->_cookie_destroy();
		}
		elseif ($this->_file_path !== NULL)
		{
			clearstatcache();
			return file_exists($this->_file_path)
				? (unlink($this->_file_path) && $this->_cookie_destroy())
				: TRUE;
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	public function gc($maxlifetime)
	{
		if ( ! is_dir($this->_save_path) OR ($files = scandir($this->_save_path)) === FALSE)
		{
			log_message('debug', "Session: Garbage collector couldn't list files under directory '".$this->_save_path."'.");
			return FALSE;
		}

		$ts = time() - $maxlifetime;

		foreach ($files as $file)
		{
			// If the filename doesn't match this pattern, it's either not a session file or is not ours
			if ( ! preg_match('/(?:[0-9a-f]{32})?[0-9a-f]{40}$/i', $file)
				OR ! is_file($this->_save_path.DIRECTORY_SEPARATOR.$file)
				OR ($mtime = filemtime($file)) === FALSE
				OR $mtime > $ts)
			{
				continue;
			}

			unlink($this->_save_path.DIRECTORY_SEPARATOR.$file);
		}

		return TRUE;
	}

}

/* End of file Session_files_driver.php */
/* Location: ./system/libraries/Session/drivers/Session_files_driver.php */