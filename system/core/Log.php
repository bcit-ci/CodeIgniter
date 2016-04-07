<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2016, British Columbia Institute of Technology
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package	CodeIgniter
 * @author	EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (https://ellislab.com/)
 * @copyright	Copyright (c) 2014 - 2016, British Columbia Institute of Technology (http://bcit.ca/)
 * @license	http://opensource.org/licenses/MIT	MIT License
 * @link	https://codeigniter.com
 * @since	Version 1.0.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Logging Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Logging
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/user_guide/general/errors.html
 */
class CI_Log {

	/**
	 * Path to save log files
	 *
	 * @var string
	 */
	// 日志路径
	protected $_log_path;

	/**
	 * File permissions
	 *
	 * @var	int
	 */
	//权限
	protected $_file_permissions = 0644;

	/**
	 * Level of logging
	 *
	 * @var int
	 */
	/* 记录日志临界值，默认记录error信息
	 * 0 = Disables logging, Error logging TURNED OFF 禁用log
	 * 1 = Error Messages (including PHP errors)      记录error信息
	 * 2 = Debug Messages							  记录debug信息
     * 3 = Informational Messages					  记录info信息
	 * 4 = All Messages								  记录所有信息
	 * */
	protected $_threshold = 1;

	/**
	 * Array of threshold levels to log
	 *
	 * @var array
	 */
	//临界值数组
	protected $_threshold_array = array();

	/**
	 * Format of timestamp for log files
	 *
	 * @var string
	 */
	//日期格式
	protected $_date_fmt = 'Y-m-d H:i:s';

	/**
	 * Filename extension
	 *
	 * @var	string
	 */
	//文件后缀
	protected $_file_ext;

	/**
	 * Whether or not the logger can write to the log files
	 *
	 * @var bool
	 */
	//日志文件可否写入
	protected $_enabled = TRUE;

	/**
	 * Predefined logging levels
	 *
	 * @var array
	 */
	//日志级别
	protected $_levels = array('ERROR' => 1, 'DEBUG' => 2, 'INFO' => 3, 'ALL' => 4);

	// --------------------------------------------------------------------

	/**
	 * Class constructor
	 *
	 * @return	void
	 */
	public function __construct()
	{
		//获取主配置文件
		$config =& get_config();

		//设置日志路径
		$this->_log_path = ($config['log_path'] !== '') ? $config['log_path'] : APPPATH.'logs/';
		//设置日志后缀
		$this->_file_ext = (isset($config['log_file_extension']) && $config['log_file_extension'] !== '')
			? ltrim($config['log_file_extension'], '.') : 'php';

		//判断是否存在，不存在，则创建
		file_exists($this->_log_path) OR mkdir($this->_log_path, 0755, TRUE);

		//判断文件日志是否可以写入
		if ( ! is_dir($this->_log_path) OR ! is_really_writable($this->_log_path))
		{
			$this->_enabled = FALSE;
		}

		//获取主配置文件阀值被初始化类临界值和临界值数组
		if (is_numeric($config['log_threshold']))
		{
			$this->_threshold = (int) $config['log_threshold'];
		}
		elseif (is_array($config['log_threshold']))
		{
			$this->_threshold = 0;
			$this->_threshold_array = array_flip($config['log_threshold']);//交换数组中key-val
		}

		//日志时间格式
		if ( ! empty($config['log_date_format']))
		{
			$this->_date_fmt = $config['log_date_format'];
		}

		//日志文件权限
		if ( ! empty($config['log_file_permissions']) && is_int($config['log_file_permissions']))
		{
			$this->_file_permissions = $config['log_file_permissions'];
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Write Log File
	 *
	 * Generally this function will be called using the global log_message() function
	 *
	 * @param	string	$level 	The error level: 'error', 'debug' or 'info'
	 * @param	string	$msg 	The error message
	 * @return	bool
	 */
	// 记录信息到log
	public function write_log($level, $msg)
	{
		//当前日志不能写入
		if ($this->_enabled === FALSE)
		{
			return FALSE;
		}

		//msg级别
		$level = strtoupper($level);

		//是否在设置改级别，而且该级别大于阀值，则记录该msg
		if (( ! isset($this->_levels[$level]) OR ($this->_levels[$level] > $this->_threshold))
			&& ! isset($this->_threshold_array[$this->_levels[$level]]))
		{
			return FALSE;
		}

		//日志记录
		$filepath = $this->_log_path.'log-'.date('Y-m-d').'.'.$this->_file_ext;
		//消息
		$message = '';

		//不存在日志文件
		if ( ! file_exists($filepath))
		{
			$newfile = TRUE;
			// Only add protection to php files
			//php文件，追加一句php语句
			if ($this->_file_ext === 'php')
			{
				$message .= "<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>\n\n";
			}
		}

		//以追加，二进制方式打开文件，如果文件不存在，则尝试创建文件
		if ( ! $fp = @fopen($filepath, 'ab'))
		{
			//创建文件，或者打开文件失败
			return FALSE;
		}

		// Instantiating DateTime with microseconds appended to initial date is needed for proper support of this format
		//安装指定格式获取时间
		if (strpos($this->_date_fmt, 'u') !== FALSE)
		{
			$microtime_full = microtime(TRUE); //返回当前 Unix 时间戳和微秒数，带true参数，返回float类型
			$microtime_short = sprintf("%06d", ($microtime_full - floor($microtime_full)) * 1000000); //微妙数,转化为6位长度整数，不够用0填充； %d 为十进制整数；0 填充位字符， 6 长度
			$date = new DateTime(date('Y-m-d H:i:s.'.$microtime_short, $microtime_full));
			$date = $date->format($this->_date_fmt);
		}
		else
		{
			$date = date($this->_date_fmt);
		}

		//拼接内容
		$message .= $this->_format_line($level, $date, $msg);

		//通过排他锁锁定日志文件，防止多个进程同时操作文件
		flock($fp, LOCK_EX);

		//??? 为什么用for写入?
		for ($written = 0, $length = strlen($message); $written < $length; $written += $result)
		{
			if (($result = fwrite($fp, substr($message, $written))) === FALSE)
			{
				break;
			}
		}

		//写入完毕，释放锁
		flock($fp, LOCK_UN);
		//关闭文件
		fclose($fp);

		if (isset($newfile) && $newfile === TRUE)
		{
			//新文件，设置权限
			chmod($filepath, $this->_file_permissions);
		}

		//返回写入字符串长度
		return is_int($result);
	}

	// --------------------------------------------------------------------

	/**
	 * Format the log line.
	 *
	 * This is for extensibility of log formatting
	 * If you want to change the log format, extend the CI_Log class and override this method
	 *
	 * @param	string	$level 	The error level
	 * @param	string	$date 	Formatted date string
	 * @param	string	$msg 	The log message
	 * @return	string	Formatted log line with a new line character '\n' at the end
	 */
	protected function _format_line($level, $date, $message)
	{
		return $level.' - '.$date.' --> '.$message."\n";
	}
}
