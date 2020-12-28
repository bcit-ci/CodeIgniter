<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2019, British Columbia Institute of Technology
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
 * @copyright	Copyright (c) 2014 - 2019, British Columbia Institute of Technology (https://bcit.ca/)
 * @license	https://opensource.org/licenses/MIT	MIT License
 * @link	https://codeigniter.com
 * @since	Version 1.0.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CodeIgniter Date Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/userguide3/helpers/date_helper.html
 */

// ------------------------------------------------------------------------

if ( ! function_exists('now'))
{
	/**
	 * Get "now" time
	 *
	 * Returns time() based on the timezone parameter or on the
	 * "time_reference" setting
	 *
	 * @param	string
	 * @return	int
	 */
	function now($timezone = NULL)
	{
		if (empty($timezone))
		{
			$timezone = config_item('time_reference');
		}

		if ($timezone === 'local' OR $timezone === date_default_timezone_get())
		{
			return time();
		}

		$datetime = new DateTime('now', new DateTimeZone($timezone));
		sscanf($datetime->format('j-n-Y G:i:s'), '%d-%d-%d %d:%d:%d', $day, $month, $year, $hour, $minute, $second);

		return mktime($hour, $minute, $second, $month, $day, $year);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('mdate'))
{
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
	 * @param	string
	 * @param	int
	 * @return	int
	 */
	function mdate($datestr = '', $time = '')
	{
		if ($datestr === '')
		{
			return '';
		}
		elseif (empty($time))
		{
			$time = now();
		}

		$datestr = str_replace(
			'%\\',
			'',
			preg_replace('/([a-z]+?){1}/i', '\\\\\\1', $datestr)
		);

		return date($datestr, $time);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('timespan'))
{
	/**
	 * Timespan
	 *
	 * Returns a span of seconds in this format:
	 *	10 days 14 hours 36 minutes 47 seconds
	 *
	 * @param	int	a number of seconds
	 * @param	int	Unix timestamp
	 * @param	int	a number of display units
	 * @return	string
	 */
	function timespan($seconds = 1, $time = '', $units = 7)
	{
		$CI =& get_instance();
		$CI->lang->load('date');

		is_numeric($seconds) OR $seconds = 1;
		is_numeric($time) OR $time = time();
		is_numeric($units) OR $units = 7;

		$seconds = ($time <= $seconds) ? 1 : $time - $seconds;

		$str = array();
		$years = floor($seconds / 31557600);

		if ($years > 0)
		{
			$str[] = $years.' '.$CI->lang->line($years > 1 ? 'date_years' : 'date_year');
		}

		$seconds -= $years * 31557600;
		$months = floor($seconds / 2629743);

		if (count($str) < $units && ($years > 0 OR $months > 0))
		{
			if ($months > 0)
			{
				$str[] = $months.' '.$CI->lang->line($months > 1 ? 'date_months' : 'date_month');
			}

			$seconds -= $months * 2629743;
		}

		$weeks = floor($seconds / 604800);

		if (count($str) < $units && ($years > 0 OR $months > 0 OR $weeks > 0))
		{
			if ($weeks > 0)
			{
				$str[] = $weeks.' '.$CI->lang->line($weeks > 1 ? 'date_weeks' : 'date_week');
			}

			$seconds -= $weeks * 604800;
		}

		$days = floor($seconds / 86400);

		if (count($str) < $units && ($months > 0 OR $weeks > 0 OR $days > 0))
		{
			if ($days > 0)
			{
				$str[] = $days.' '.$CI->lang->line($days > 1 ? 'date_days' : 'date_day');
			}

			$seconds -= $days * 86400;
		}

		$hours = floor($seconds / 3600);

		if (count($str) < $units && ($days > 0 OR $hours > 0))
		{
			if ($hours > 0)
			{
				$str[] = $hours.' '.$CI->lang->line($hours > 1 ? 'date_hours' : 'date_hour');
			}

			$seconds -= $hours * 3600;
		}

		$minutes = floor($seconds / 60);

		if (count($str) < $units && ($days > 0 OR $hours > 0 OR $minutes > 0))
		{
			if ($minutes > 0)
			{
				$str[] = $minutes.' '.$CI->lang->line($minutes > 1 ? 'date_minutes' : 'date_minute');
			}

			$seconds -= $minutes * 60;
		}

		if (count($str) === 0)
		{
			$str[] = $seconds.' '.$CI->lang->line($seconds > 1 ? 'date_seconds' : 'date_second');
		}

		return implode(', ', $str);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('days_in_month'))
{
	/**
	 * Number of days in a month
	 *
	 * Takes a month/year as input and returns the number of days
	 * for the given month/year. Takes leap years into consideration.
	 *
	 * @param	int	a numeric month
	 * @param	int	a numeric year
	 * @return	int
	 */
	function days_in_month($month = 0, $year = '')
	{
		if ($month < 1 OR $month > 12)
		{
			return 0;
		}
		elseif ( ! is_numeric($year) OR strlen($year) !== 4)
		{
			$year = date('Y');
		}

		if (defined('CAL_GREGORIAN'))
		{
			return cal_days_in_month(CAL_GREGORIAN, $month, $year);
		}

		if ($year >= 1970)
		{
			return (int) date('t', mktime(12, 0, 0, $month, 1, $year));
		}

		if ($month == 2)
		{
			if ($year % 400 === 0 OR ($year % 4 === 0 && $year % 100 !== 0))
			{
				return 29;
			}
		}

		$days_in_month	= array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
		return $days_in_month[$month - 1];
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('local_to_gmt'))
{
	/**
	 * Converts a local Unix timestamp to GMT
	 *
	 * @param	int	Unix timestamp
	 * @return	int
	 */
	function local_to_gmt($time = '')
	{
		if ($time === '')
		{
			$time = time();
		}

		return mktime(
			gmdate('G', $time),
			gmdate('i', $time),
			gmdate('s', $time),
			gmdate('n', $time),
			gmdate('j', $time),
			gmdate('Y', $time)
		);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('gmt_to_local'))
{
	/**
	 * Converts GMT time to a localized value
	 *
	 * Takes a Unix timestamp (in GMT) as input, and returns
	 * at the local value based on the timezone and DST setting
	 * submitted
	 *
	 * @param	int	Unix timestamp
	 * @param	string	timezone
	 * @param	bool	whether DST is active
	 * @return	int
	 */
	function gmt_to_local($time = '', $timezone = 'UTC', $dst = FALSE)
	{
		if ($time === '')
		{
			return now();
		}

		$time += timezones($timezone) * 3600;

		return ($dst === TRUE) ? $time + 3600 : $time;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('mysql_to_unix'))
{
	/**
	 * Converts a MySQL Timestamp to Unix
	 *
	 * @param	int	MySQL timestamp YYYY-MM-DD HH:MM:SS
	 * @return	int	Unix timstamp
	 */
	function mysql_to_unix($time = '')
	{
		// We'll remove certain characters for backward compatibility
		// since the formatting changed with MySQL 4.1
		// YYYY-MM-DD HH:MM:SS

		$time = str_replace(array('-', ':', ' '), '', $time);

		// YYYYMMDDHHMMSS
		return mktime(
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

if ( ! function_exists('unix_to_human'))
{
	/**
	 * Unix to "Human"
	 *
	 * Formats Unix timestamp to the following prototype: 2006-08-21 11:35 PM
	 *
	 * @param	int	Unix timestamp
	 * @param	bool	whether to show seconds
	 * @param	string	format: us or euro
	 * @return	string
	 */
	function unix_to_human($time = '', $seconds = FALSE, $fmt = 'us')
	{
		$r = date('Y', $time).'-'.date('m', $time).'-'.date('d', $time).' ';

		if ($fmt === 'us')
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

		if ($fmt === 'us')
		{
			return $r.' '.date('A', $time);
		}

		return $r;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('human_to_unix'))
{
	/**
	 * Convert "human" date to GMT
	 *
	 * Reverses the above process
	 *
	 * @param	string	format: us or euro
	 * @return	int
	 */
	function human_to_unix($datestr = '')
	{
		if ($datestr === '')
		{
			return FALSE;
		}

		$datestr = preg_replace('/\040+/', ' ', trim($datestr));

		if ( ! preg_match('/^(\d{2}|\d{4})\-[0-9]{1,2}\-[0-9]{1,2}\s[0-9]{1,2}:[0-9]{1,2}(?::[0-9]{1,2})?(?:\s[AP]M)?$/i', $datestr))
		{
			return FALSE;
		}

		sscanf($datestr, '%d-%d-%d %s %s', $year, $month, $day, $time, $ampm);
		sscanf($time, '%d:%d:%d', $hour, $min, $sec);
		isset($sec) OR $sec = 0;

		if (isset($ampm))
		{
			$ampm = strtolower($ampm);

			if ($ampm[0] === 'p' && $hour < 12)
			{
				$hour += 12;
			}
			elseif ($ampm[0] === 'a' && $hour === 12)
			{
				$hour = 0;
			}
		}

		return mktime($hour, $min, $sec, $month, $day, $year);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('nice_date'))
{
	/**
	 * Turns many "reasonably-date-like" strings into something
	 * that is actually useful. This only works for dates after unix epoch.
	 *
	 * @deprecated	3.1.3	Use DateTime::createFromFormat($input_format, $input)->format($output_format);
	 * @param	string	The terribly formatted date-like string
	 * @param	string	Date format to return (same as php date function)
	 * @return	string
	 */
	function nice_date($bad_date = '', $format = FALSE)
	{
		if (empty($bad_date))
		{
			return 'Unknown';
		}
		elseif (empty($format))
		{
			$format = 'U';
		}

		// Date like: YYYYMM
		if (preg_match('/^\d{6}$/i', $bad_date))
		{
			if (in_array(substr($bad_date, 0, 2), array('19', '20')))
			{
				$year  = substr($bad_date, 0, 4);
				$month = substr($bad_date, 4, 2);
			}
			else
			{
				$month  = substr($bad_date, 0, 2);
				$year   = substr($bad_date, 2, 4);
			}

			return date($format, strtotime($year.'-'.$month.'-01'));
		}

		// Date Like: YYYYMMDD
		if (preg_match('/^\d{8}$/i', $bad_date, $matches))
		{
			return DateTime::createFromFormat('Ymd', $bad_date)->format($format);
		}

		// Date Like: MM-DD-YYYY __or__ M-D-YYYY (or anything in between)
		if (preg_match('/^(\d{1,2})-(\d{1,2})-(\d{4})$/i', $bad_date, $matches))
		{
			return date($format, strtotime($matches[3].'-'.$matches[1].'-'.$matches[2]));
		}

		// Any other kind of string, when converted into UNIX time,
		// produces "0 seconds after epoc..." is probably bad...
		// return "Invalid Date".
		if (date('U', strtotime($bad_date)) === '0')
		{
			return 'Invalid Date';
		}

		// It's probably a valid-ish date format already
		return date($format, strtotime($bad_date));
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('timezone_menu'))
{
	/**
	 * Timezone Menu
	 *
	 * Generates a drop-down menu of timezones.
	 *
	 * @param	string	timezone
	 * @param	string	classname
	 * @param	string	menu name
	 * @param	mixed	attributes
	 * @return	string
	 */
	function timezone_menu($default = 'UTC', $class = '', $name = 'timezones', $attributes = '')
	{
		$CI =& get_instance();
		$CI->lang->load('date');

		$default = ($default === 'GMT') ? 'UTC' : $default;

		$menu = '<select name="'.$name.'"';

		if ($class !== '')
		{
			$menu .= ' class="'.$class.'"';
		}

		$menu .= _stringify_attributes($attributes).">\n";

		foreach (timezones() as $key => $val)
		{
			$selected = ($default === $key) ? ' selected="selected"' : '';
			$menu .= '<option value="'.$key.'"'.$selected.'>'.$CI->lang->line($key)."</option>\n";
		}

		return $menu.'</select>';
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('timezones'))
{
	/**
	 * Timezones
	 *
	 * Returns an array of timezones. This is a helper function
	 * for various other ones in this library
	 *
	 * @param	string	timezone
	 * @return	string
	 */
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

		if ($tz === '')
		{
			return $zones;
		}

		return isset($zones[$tz]) ? $zones[$tz] : 0;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('date_range'))
{
	/**
	 * Date range
	 *
	 * Returns a list of dates within a specified period.
	 *
	 * @param	int	unix_start	UNIX timestamp of period start date
	 * @param	int	unix_end|days	UNIX timestamp of period end date
	 *					or interval in days.
	 * @param	mixed	is_unix		Specifies whether the second parameter
	 *					is a UNIX timestamp or a day interval
	 *					 - TRUE or 'unix' for a timestamp
	 *					 - FALSE or 'days' for an interval
	 * @param	string  date_format	Output date format, same as in date()
	 * @return	array
	 */
	function date_range($unix_start = '', $mixed = '', $is_unix = TRUE, $format = 'Y-m-d')
	{
		if ($unix_start == '' OR $mixed == '' OR $format == '')
		{
			return FALSE;
		}

		$is_unix = ! ( ! $is_unix OR $is_unix === 'days');

		// Validate input and try strtotime() on invalid timestamps/intervals, just in case
		if ( ( ! ctype_digit((string) $unix_start) && ($unix_start = @strtotime($unix_start)) === FALSE)
			OR ( ! ctype_digit((string) $mixed) && ($is_unix === FALSE OR ($mixed = @strtotime($mixed)) === FALSE))
			OR ($is_unix === TRUE && $mixed < $unix_start))
		{
			return FALSE;
		}

		if ($is_unix && ($unix_start == $mixed OR date($format, $unix_start) === date($format, $mixed)))
		{
			return array(date($format, $unix_start));
		}

		$range = array();

		$from = new DateTime();
		$from->setTimestamp($unix_start);

		if ($is_unix)
		{
			$arg = new DateTime();
			$arg->setTimestamp($mixed);
		}
		else
		{
			$arg = (int) $mixed;
		}

		$period = new DatePeriod($from, new DateInterval('P1D'), $arg);
		foreach ($period as $date)
		{
			$range[] = $date->format($format);
		}

		/* If a period end date was passed to the DatePeriod constructor, it might not
		 * be in our results. Not sure if this is a bug or it's just possible because
		 * the end date might actually be less than 24 hours away from the previously
		 * generated DateTime object, but either way - we have to append it manually.
		 */
		if ( ! is_int($arg) && $range[count($range) - 1] !== $arg->format($format))
		{
			$range[] = $arg->format($format);
		}

		return $range;
	}
}
