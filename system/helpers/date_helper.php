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
 * CodeIgniter Date Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/helpers/date_helper.html
 */

// ------------------------------------------------------------------------

/**
 * Get "now" time
 *
 * Returns time() or its GMT equivalent based on the config file preference
 *
 * @access	public
 * @return	integer
 */
if ( ! function_exists('now'))
{
	function now()
	{
		$CI =& get_instance();

		if (strtolower($CI->config->item('time_reference')) == 'gmt')
		{
			$now = time();
			$system_time = mktime(gmdate("H", $now), gmdate("i", $now), gmdate("s", $now), gmdate("m", $now), gmdate("d", $now), gmdate("Y", $now));

			if (strlen($system_time) < 10)
			{
				$system_time = time();
				log_message('error', 'The Date class could not set a proper GMT timestamp so the local time() value was used.');
			}

			return $system_time;
		}
		else
		{
			return time();
		}
	}
}

// ------------------------------------------------------------------------

/**
 * Convert MySQL Style Datecodes
 *
 * This function is identical to PHPs date() function,
 * except that it allows date codes to be formatted using
 * the MySQL style, where each code letter is preceded
 * with a percent sign:  %Y %m %d etc...
 *
 * The benefit of doing dates this way is that you don't
 * have to worry about escaping your text letters that
 * match the date codes.
 *
 * @access	public
 * @param	string
 * @param	integer
 * @return	integer
 */
if ( ! function_exists('mdate'))
{
	function mdate($datestr = '', $time = '')
	{
		if ($datestr == '')
			return '';

		if ($time == '')
			$time = now();

		$datestr = str_replace('%\\', '', preg_replace("/([a-z]+?){1}/i", "\\\\\\1", $datestr));
		return date($datestr, $time);
	}
}

// ------------------------------------------------------------------------

/**
 * Standard Date
 *
 * Returns a date formatted according to the submitted standard.
 *
 * @access	public
 * @param	string	the chosen format
 * @param	integer	Unix timestamp
 * @return	string
 */
if ( ! function_exists('standard_date'))
{
	function standard_date($fmt = 'DATE_RFC822', $time = '')
	{
		$formats = array(
						'DATE_ATOM'		=>	'%Y-%m-%dT%H:%i:%s%Q',
						'DATE_COOKIE'	=>	'%l, %d-%M-%y %H:%i:%s UTC',
						'DATE_ISO8601'	=>	'%Y-%m-%dT%H:%i:%s%Q',
						'DATE_RFC822'	=>	'%D, %d %M %y %H:%i:%s %O',
						'DATE_RFC850'	=>	'%l, %d-%M-%y %H:%m:%i UTC',
						'DATE_RFC1036'	=>	'%D, %d %M %y %H:%i:%s %O',
						'DATE_RFC1123'	=>	'%D, %d %M %Y %H:%i:%s %O',
						'DATE_RSS'		=>	'%D, %d %M %Y %H:%i:%s %O',
						'DATE_W3C'		=>	'%Y-%m-%dT%H:%i:%s%Q'
						);

		if ( ! isset($formats[$fmt]))
		{
			return FALSE;
		}

		return mdate($formats[$fmt], $time);
	}
}

// ------------------------------------------------------------------------

/**
 * Timespan
 *
 * Returns a span of seconds in this format:
 *	10 days 14 hours 36 minutes 47 seconds
 *
 * @access	public
 * @param	integer	a number of seconds
 * @param	integer	Unix timestamp
 * @return	integer
 */
if ( ! function_exists('timespan'))
{
	function timespan($seconds = 1, $time = '')
	{
		$CI =& get_instance();
		$CI->lang->load('date');

		if ( ! is_numeric($seconds))
		{
			$seconds = 1;
		}

		if ( ! is_numeric($time))
		{
			$time = time();
		}

		if ($time <= $seconds)
		{
			$seconds = 1;
		}
		else
		{
			$seconds = $time - $seconds;
		}

		$str = '';
		$years = floor($seconds / 31536000);

		if ($years > 0)
		{
			$str .= $years.' '.$CI->lang->line((($years	> 1) ? 'date_years' : 'date_year')).', ';
		}

		$seconds -= $years * 31536000;
		$months = floor($seconds / 2628000);

		if ($years > 0 OR $months > 0)
		{
			if ($months > 0)
			{
				$str .= $months.' '.$CI->lang->line((($months	> 1) ? 'date_months' : 'date_month')).', ';
			}

			$seconds -= $months * 2628000;
		}

		$weeks = floor($seconds / 604800);

		if ($years > 0 OR $months > 0 OR $weeks > 0)
		{
			if ($weeks > 0)
			{
				$str .= $weeks.' '.$CI->lang->line((($weeks	> 1) ? 'date_weeks' : 'date_week')).', ';
			}

			$seconds -= $weeks * 604800;
		}

		$days = floor($seconds / 86400);

		if ($months > 0 OR $weeks > 0 OR $days > 0)
		{
			if ($days > 0)
			{
				$str .= $days.' '.$CI->lang->line((($days	> 1) ? 'date_days' : 'date_day')).', ';
			}

			$seconds -= $days * 86400;
		}

		$hours = floor($seconds / 3600);

		if ($days > 0 OR $hours > 0)
		{
			if ($hours > 0)
			{
				$str .= $hours.' '.$CI->lang->line((($hours	> 1) ? 'date_hours' : 'date_hour')).', ';
			}

			$seconds -= $hours * 3600;
		}

		$minutes = floor($seconds / 60);

		if ($days > 0 OR $hours > 0 OR $minutes > 0)
		{
			if ($minutes > 0)
			{
				$str .= $minutes.' '.$CI->lang->line((($minutes	> 1) ? 'date_minutes' : 'date_minute')).', ';
			}

			$seconds -= $minutes * 60;
		}

		if ($str == '')
		{
			$str .= $seconds.' '.$CI->lang->line((($seconds	> 1) ? 'date_seconds' : 'date_second')).', ';
		}

		return substr(trim($str), 0, -1);
	}
}

// ------------------------------------------------------------------------

/**
 * Number of days in a month
 *
 * Takes a month/year as input and returns the number of days
 * for the given month/year. Takes leap years into consideration.
 *
 * @access	public
 * @param	integer a numeric month
 * @param	integer	a numeric year
 * @return	integer
 */
if ( ! function_exists('days_in_month'))
{
	function days_in_month($month = 0, $year = '')
	{
		if ($month < 1 OR $month > 12)
		{
			return 0;
		}

		if ( ! is_numeric($year) OR strlen($year) != 4)
		{
			$year = date('Y');
		}

		if ($month == 2)
		{
			if ($year % 400 == 0 OR ($year % 4 == 0 AND $year % 100 != 0))
			{
				return 29;
			}
		}

		$days_in_month	= array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
		return $days_in_month[$month - 1];
	}
}

// ------------------------------------------------------------------------

/**
 * Converts a local Unix timestamp to GMT
 *
 * @access	public
 * @param	integer Unix timestamp
 * @return	integer
 */
if ( ! function_exists('local_to_gmt'))
{
	function local_to_gmt($time = '')
	{
		if ($time == '')
			$time = time();

		return mktime( gmdate("H", $time), gmdate("i", $time), gmdate("s", $time), gmdate("m", $time), gmdate("d", $time), gmdate("Y", $time));
	}
}

// ------------------------------------------------------------------------

/**
 * Converts GMT time to a localized value
 *
 * Takes a Unix timestamp (in GMT) as input, and returns
 * at the local value based on the timezone and DST setting
 * submitted
 *
 * @access	public
 * @param	integer Unix timestamp
 * @param	string	timezone
 * @param	bool	whether DST is active
 * @return	integer
 */
if ( ! function_exists('gmt_to_local'))
{
	function gmt_to_local($time = '', $timezone = 'UTC', $dst = FALSE)
	{
		if ($time == '')
		{
			return now();
		}

		$time += timezones($timezone) * 3600;

		if ($dst == TRUE)
		{
			$time += 3600;
		}

		return $time;
	}
}

// ------------------------------------------------------------------------

/**
 * Converts a MySQL Timestamp to Unix
 *
 * @access	public
 * @param	integer Unix timestamp
 * @return	integer
 */
if ( ! function_exists('mysql_to_unix'))
{
	function mysql_to_unix($time = '')
	{
		// We'll remove certain characters for backward compatibility
		// since the formatting changed with MySQL 4.1
		// YYYY-MM-DD HH:MM:SS

		$time = str_replace('-', '', $time);
		$time = str_replace(':', '', $time);
		$time = str_replace(' ', '', $time);

		// YYYYMMDDHHMMSS
		return  mktime(
						substr($time, 8, 2),
						substr($time, 10, 2),
						substr($time, 12, 2),
						substr($time, 4, 2),
						substr($time, 6, 2),
						substr($time, 0, 4)
						);
	}
}

// ------------------------------------------------------------------------

/**
 * Unix to "Human"
 *
 * Formats Unix timestamp to the following prototype: 2006-08-21 11:35 PM
 *
 * @access	public
 * @param	integer Unix timestamp
 * @param	bool	whether to show seconds
 * @param	string	format: us or euro
 * @return	string
 */
if ( ! function_exists('unix_to_human'))
{
	function unix_to_human($time = '', $seconds = FALSE, $fmt = 'us')
	{
		$r  = date('Y', $time).'-'.date('m', $time).'-'.date('d', $time).' ';

		if ($fmt == 'us')
		{
			$r .= date('h', $time).':'.date('i', $time);
		}
		else
		{
			$r .= date('H', $time).':'.date('i', $time);
		}

		if ($seconds)
		{
			$r .= ':'.date('s', $time);
		}

		if ($fmt == 'us')
		{
			$r .= ' '.date('A', $time);
		}

		return $r;
	}
}

// ------------------------------------------------------------------------

/**
 * Convert "human" date to GMT
 *
 * Reverses the above process
 *
 * @access	public
 * @param	string	format: us or euro
 * @return	integer
 */
if ( ! function_exists('human_to_unix'))
{
	function human_to_unix($datestr = '')
	{
		if ($datestr == '')
		{
			return FALSE;
		}

		$datestr = trim($datestr);
		$datestr = preg_replace("/\040+/", ' ', $datestr);

		if ( ! preg_match('/^[0-9]{2,4}\-[0-9]{1,2}\-[0-9]{1,2}\s[0-9]{1,2}:[0-9]{1,2}(?::[0-9]{1,2})?(?:\s[AP]M)?$/i', $datestr))
		{
			return FALSE;
		}

		$split = explode(' ', $datestr);

		$ex = explode("-", $split['0']);

		$year  = (strlen($ex['0']) == 2) ? '20'.$ex['0'] : $ex['0'];
		$month = (strlen($ex['1']) == 1) ? '0'.$ex['1']  : $ex['1'];
		$day   = (strlen($ex['2']) == 1) ? '0'.$ex['2']  : $ex['2'];

		$ex = explode(":", $split['1']);

		$hour = (strlen($ex['0']) == 1) ? '0'.$ex['0'] : $ex['0'];
		$min  = (strlen($ex['1']) == 1) ? '0'.$ex['1'] : $ex['1'];

		if (isset($ex['2']) && preg_match('/[0-9]{1,2}/', $ex['2']))
		{
			$sec  = (strlen($ex['2']) == 1) ? '0'.$ex['2'] : $ex['2'];
		}
		else
		{
			// Unless specified, seconds get set to zero.
			$sec = '00';
		}

		if (isset($split['2']))
		{
			$ampm = strtolower($split['2']);

			if (substr($ampm, 0, 1) == 'p' AND $hour < 12)
				$hour = $hour + 12;

			if (substr($ampm, 0, 1) == 'a' AND $hour == 12)
				$hour =  '00';

			if (strlen($hour) == 1)
				$hour = '0'.$hour;
		}

		return mktime($hour, $min, $sec, $month, $day, $year);
	}
}

// ------------------------------------------------------------------------

/**
 * Timezone Menu
 *
 * Generates a drop-down menu of timezones.
 *
 * @access	public
 * @param	string	timezone
 * @param	string	classname
 * @param	string	menu name
 * @return	string
 */
if ( ! function_exists('timezone_menu'))
{
	function timezone_menu($default = 'UTC', $class = "", $name = 'timezones')
	{
		$CI =& get_instance();
		$CI->lang->load('date');

		if ($default == 'GMT')
			$default = 'UTC';

		$menu = '<select name="'.$name.'"';

		if ($class != '')
		{
			$menu .= ' class="'.$class.'"';
		}

		$menu .= ">\n";

		foreach (timezones() as $key => $val)
		{
			$selected = ($default == $key) ? " selected='selected'" : '';
			$menu .= "<option value='{$key}'{$selected}>".$CI->lang->line($key)."</option>\n";
		}

		$menu .= "</select>";

		return $menu;
	}
}

// ------------------------------------------------------------------------

/**
 * Timezones
 *
 * Returns an array of timezones.  This is a helper function
 * for various other ones in this library
 *
 * @access	public
 * @param	string	timezone
 * @return	string
 */
if ( ! function_exists('timezones'))
{
	function timezones($tz = '')
	{
		// Note: Don't change the order of these even though
		// some items appear to be in the wrong order

		$zones = array(
						'UM12'		=> -12,
						'UM11'		=> -11,
						'UM10'		=> -10,
						'UM95'		=> -9.5,
						'UM9'		=> -9,
						'UM8'		=> -8,
						'UM7'		=> -7,
						'UM6'		=> -6,
						'UM5'		=> -5,
						'UM45'		=> -4.5,
						'UM4'		=> -4,
						'UM35'		=> -3.5,
						'UM3'		=> -3,
						'UM2'		=> -2,
						'UM1'		=> -1,
						'UTC'		=> 0,
						'UP1'		=> +1,
						'UP2'		=> +2,
						'UP3'		=> +3,
						'UP35'		=> +3.5,
						'UP4'		=> +4,
						'UP45'		=> +4.5,
						'UP5'		=> +5,
						'UP55'		=> +5.5,
						'UP575'		=> +5.75,
						'UP6'		=> +6,
						'UP65'		=> +6.5,
						'UP7'		=> +7,
						'UP8'		=> +8,
						'UP875'		=> +8.75,
						'UP9'		=> +9,
						'UP95'		=> +9.5,
						'UP10'		=> +10,
						'UP105'		=> +10.5,
						'UP11'		=> +11,
						'UP115'		=> +11.5,
						'UP12'		=> +12,
						'UP1275'	=> +12.75,
						'UP13'		=> +13,
						'UP14'		=> +14
					);

		if ($tz == '')
		{
			return $zones;
		}

		if ($tz == 'GMT')
			$tz = 'UTC';

		return ( ! isset($zones[$tz])) ? 0 : $zones[$tz];
	}
}


/* End of file date_helper.php */
/* Location: ./system/helpers/date_helper.php */