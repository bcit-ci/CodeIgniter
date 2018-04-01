###########
Date Helper
###########

The Date Helper file contains functions that help you work with dates.

.. contents::
  :local:

.. raw:: html

  <div class="custom-index container"></div>

Loading this Helper
===================

This helper is loaded using the following code::

	$this->load->helper('date');

Available Functions
===================

The following functions are available:


.. php:function:: now([$timezone = NULL])

	:param	string	$timezone: Timezone
	:returns:	UNIX timestamp
	:rtype:	int

	Returns the current time as a UNIX timestamp, referenced either to your server's
	local time or any PHP supported timezone, based on the "time reference" setting
	in your config file. If you do not intend to set your master time reference to
	any other PHP supported timezone (which you'll typically do if you run a site
	that lets each user set their own timezone settings) there is no benefit to using
	this function over PHP's ``time()`` function.
	::

		echo now('Australia/Victoria');

	If a timezone is not provided, it will return ``time()`` based on the
	**time_reference** setting.

.. php:function:: mdate([$datestr = ''[, $time = '']])

	:param	string	$datestr: Date string
	:param	int	$time: UNIX timestamp
	:returns:	MySQL-formatted date
	:rtype:	string

	This function is identical to PHP's `date() <http://php.net/manual/en/function.date.php>`_
	function, except that it lets you use MySQL style date codes, where each
	code letter is preceded with a percent sign, e.g. `%Y %m %d`

	The benefit of doing dates this way is that you don't have to worry
	about escaping any characters that are not date codes, as you would
	normally have to do with the ``date()`` function.

	Example::

		$datestring = 'Year: %Y Month: %m Day: %d - %h:%i %a';
		$time = time();
		echo mdate($datestring, $time);

	If a timestamp is not included in the second parameter the current time
	will be used.

.. php:function:: standard_date([$fmt = 'DATE_RFC822'[, $time = NULL]])

	:param	string	$fmt: Date format
	:param	int	$time: UNIX timestamp
	:returns:	Formatted date or FALSE on invalid format
	:rtype:	string

	Lets you generate a date string in one of several standardized formats.

	Example::

		$format = 'DATE_RFC822';
		$time = time();
		echo standard_date($format, $time);

	.. note:: This function is DEPRECATED. Use the native ``date()`` combined with
		`DateTime's format constants
		<https://secure.php.net/manual/en/class.datetime.php#datetime.constants.types>`_
		instead::

			echo date(DATE_RFC822, time());

	**Supported formats:**

	===============	=======================	======================================
	Constant        Description             Example
	===============	=======================	======================================
	DATE_ATOM       Atom                    2005-08-15T16:13:03+0000
	DATE_COOKIE     HTTP Cookies            Sun, 14 Aug 2005 16:13:03 UTC
	DATE_ISO8601    ISO-8601                2005-08-14T16:13:03+00:00
	DATE_RFC822     RFC 822                 Sun, 14 Aug 05 16:13:03 UTC
	DATE_RFC850     RFC 850                 Sunday, 14-Aug-05 16:13:03 UTC
	DATE_RFC1036    RFC 1036                Sunday, 14-Aug-05 16:13:03 UTC
	DATE_RFC1123    RFC 1123                Sun, 14 Aug 2005 16:13:03 UTC
	DATE_RFC2822    RFC 2822                Sun, 14 Aug 2005 16:13:03 +0000
	DATE_RSS        RSS                     Sun, 14 Aug 2005 16:13:03 UTC
	DATE_W3C        W3C                     2005-08-14T16:13:03+0000
	===============	=======================	======================================

.. php:function:: local_to_gmt([$time = ''])

	:param	int	$time: UNIX timestamp
	:returns:	UNIX timestamp
	:rtype:	int

	Takes a UNIX timestamp as input and returns it as GMT.

	Example::

		$gmt = local_to_gmt(time());

.. php:function:: gmt_to_local([$time = ''[, $timezone = 'UTC'[, $dst = FALSE]]])

	:param	int	$time: UNIX timestamp
	:param	string	$timezone: Timezone
	:param	bool	$dst: Whether DST is active
	:returns:	UNIX timestamp
	:rtype:	int

	Takes a UNIX timestamp (referenced to GMT) as input, and converts it to
	a localized timestamp based on the timezone and Daylight Saving Time
	submitted.

	Example::

		$timestamp = 1140153693;
		$timezone  = 'UM8';
		$daylight_saving = TRUE;
		echo gmt_to_local($timestamp, $timezone, $daylight_saving);


	.. note:: For a list of timezones see the reference at the bottom of this page.

.. php:function:: mysql_to_unix([$time = ''])

	:param	string	$time: MySQL timestamp
	:returns:	UNIX timestamp
	:rtype:	int

	Takes a MySQL Timestamp as input and returns it as a UNIX timestamp.

	Example::

		$unix = mysql_to_unix('20061124092345');

.. php:function:: unix_to_human([$time = ''[, $seconds = FALSE[, $fmt = 'us']]])

	:param	int	$time: UNIX timestamp
	:param	bool	$seconds: Whether to show seconds
	:param	string	$fmt: format (us or euro)
	:returns:	Formatted date
	:rtype:	string

	Takes a UNIX timestamp as input and returns it in a human readable
	format with this prototype::

		YYYY-MM-DD HH:MM:SS AM/PM

	This can be useful if you need to display a date in a form field for
	submission.

	The time can be formatted with or without seconds, and it can be set to
	European or US format. If only the timestamp is submitted it will return
	the time without seconds formatted for the U.S.

	Examples::

		$now = time();
		echo unix_to_human($now); // U.S. time, no seconds
		echo unix_to_human($now, TRUE, 'us'); // U.S. time with seconds
		echo unix_to_human($now, TRUE, 'eu'); // Euro time with seconds

.. php:function:: human_to_unix([$datestr = ''])

	:param	int	$datestr: Date string
	:returns:	UNIX timestamp or FALSE on failure
	:rtype:	int

	The opposite of the :php:func:`unix_to_time()` function. Takes a "human"
	time as input and returns it as a UNIX timestamp. This is useful if you
	accept "human" formatted dates submitted via a form. Returns boolean FALSE
	date string passed to it is not formatted as indicated above.

	Example::

		$now = time();
		$human = unix_to_human($now);
		$unix = human_to_unix($human);

.. php:function:: nice_date([$bad_date = ''[, $format = FALSE]])

	:param	int	$bad_date: The terribly formatted date-like string
	:param	string	$format: Date format to return (same as PHP's ``date()`` function)
	:returns:	Formatted date
	:rtype:	string

	This function can take a number poorly-formed date formats and convert
	them into something useful. It also accepts well-formed dates.

	The function will return a UNIX timestamp by default. You can, optionally,
	pass a format string (the same type as the PHP ``date()`` function accepts)
	as the second parameter.

	Example::

		$bad_date = '199605';
		// Should Produce: 1996-05-01
		$better_date = nice_date($bad_date, 'Y-m-d');

		$bad_date = '9-11-2001';
		// Should Produce: 2001-09-11
		$better_date = nice_date($bad_date, 'Y-m-d');

	.. note:: This function is DEPRECATED. Use PHP's native `DateTime class
		<https://secure.php.net/datetime>`_ instead.

.. php:function:: timespan([$seconds = 1[, $time = ''[, $units = '']]])

	:param	int	$seconds: Number of seconds
	:param	string	$time: UNIX timestamp
	:param	int	$units: Number of time units to display
	:returns:	Formatted time difference
	:rtype:	string

	Formats a UNIX timestamp so that is appears similar to this::

		1 Year, 10 Months, 2 Weeks, 5 Days, 10 Hours, 16 Minutes

	The first parameter must contain a UNIX timestamp.
	The second parameter must contain a timestamp that is greater that the
	first timestamp.
	The thirdparameter is optional and limits the number of time units to display.

	If the second parameter empty, the current time will be used.

	The most common purpose for this function is to show how much time has
	elapsed from some point in time in the past to now.

	Example::

		$post_date = '1079621429';
		$now = time();
		$units = 2;
		echo timespan($post_date, $now, $units);

	.. note:: The text generated by this function is found in the following language
		file: `language/<your_lang>/date_lang.php`

.. php:function:: days_in_month([$month = 0[, $year = '']])

	:param	int	$month: a numeric month
	:param	int	$year: a numeric year
	:returns:	Count of days in the specified month
	:rtype:	int

	Returns the number of days in a given month/year. Takes leap years into
	account.

	Example::

		echo days_in_month(06, 2005);

	If the second parameter is empty, the current year will be used.

	.. note:: This function will alias the native ``cal_days_in_month()``, if
		it is available.

.. php:function:: date_range([$unix_start = ''[, $mixed = ''[, $is_unix = TRUE[, $format = 'Y-m-d']]]])

	:param	int	$unix_start: UNIX timestamp of the range start date
	:param	int	$mixed: UNIX timestamp of the range end date or interval in days
	:param	bool	$is_unix: set to FALSE if $mixed is not a timestamp
	:param	string	$format: Output date format, same as in ``date()``
	:returns:	An array of dates
	:rtype:	array

	Returns a list of dates within a specified period.

	Example::

		$range = date_range('2012-01-01', '2012-01-15');
		echo "First 15 days of 2012:";
		foreach ($range as $date)
		{
			echo $date."\n";
		}

.. php:function:: timezones([$tz = ''])

	:param	string	$tz: A numeric timezone
	:returns:	Hour difference from UTC
	:rtype:	int

	Takes a timezone reference (for a list of valid timezones, see the
	"Timezone Reference" below) and returns the number of hours offset from
	UTC.

	Example::

		echo timezones('UM5');


	This function is useful when used with :php:func:`timezone_menu()`.

.. php:function:: timezone_menu([$default = 'UTC'[, $class = ''[, $name = 'timezones'[, $attributes = '']]]])

	:param	string	$default: Timezone
	:param	string	$class: Class name
	:param	string	$name: Menu name
	:param	mixed	$attributes: HTML attributes
	:returns:	HTML drop down menu with time zones
	:rtype:	string

	Generates a pull-down menu of timezones, like this one:

	.. raw:: html

		<form action="#">
			<select name="timezones">
				<option value='UM12'>(UTC -12:00) Baker/Howland Island</option>
				<option value='UM11'>(UTC -11:00) Samoa Time Zone, Niue</option>
				<option value='UM10'>(UTC -10:00) Hawaii-Aleutian Standard Time, Cook Islands, Tahiti</option>
				<option value='UM95'>(UTC -9:30) Marquesas Islands</option>
				<option value='UM9'>(UTC -9:00) Alaska Standard Time, Gambier Islands</option>
				<option value='UM8'>(UTC -8:00) Pacific Standard Time, Clipperton Island</option>
				<option value='UM7'>(UTC -7:00) Mountain Standard Time</option>
				<option value='UM6'>(UTC -6:00) Central Standard Time</option>
				<option value='UM5'>(UTC -5:00) Eastern Standard Time, Western Caribbean Standard Time</option>
				<option value='UM45'>(UTC -4:30) Venezuelan Standard Time</option>
				<option value='UM4'>(UTC -4:00) Atlantic Standard Time, Eastern Caribbean Standard Time</option>
				<option value='UM35'>(UTC -3:30) Newfoundland Standard Time</option>
				<option value='UM3'>(UTC -3:00) Argentina, Brazil, French Guiana, Uruguay</option>
				<option value='UM2'>(UTC -2:00) South Georgia/South Sandwich Islands</option>
				<option value='UM1'>(UTC -1:00) Azores, Cape Verde Islands</option>
				<option value='UTC' selected='selected'>(UTC) Greenwich Mean Time, Western European Time</option>
				<option value='UP1'>(UTC +1:00) Central European Time, West Africa Time</option>
				<option value='UP2'>(UTC +2:00) Central Africa Time, Eastern European Time, Kaliningrad Time</option>
				<option value='UP3'>(UTC +3:00) Moscow Time, East Africa Time</option>
				<option value='UP35'>(UTC +3:30) Iran Standard Time</option>
				<option value='UP4'>(UTC +4:00) Azerbaijan Standard Time, Samara Time</option>
				<option value='UP45'>(UTC +4:30) Afghanistan</option>
				<option value='UP5'>(UTC +5:00) Pakistan Standard Time, Yekaterinburg Time</option>
				<option value='UP55'>(UTC +5:30) Indian Standard Time, Sri Lanka Time</option>
				<option value='UP575'>(UTC +5:45) Nepal Time</option>
				<option value='UP6'>(UTC +6:00) Bangladesh Standard Time, Bhutan Time, Omsk Time</option>
				<option value='UP65'>(UTC +6:30) Cocos Islands, Myanmar</option>
				<option value='UP7'>(UTC +7:00) Krasnoyarsk Time, Cambodia, Laos, Thailand, Vietnam</option>
				<option value='UP8'>(UTC +8:00) Australian Western Standard Time, Beijing Time, Irkutsk Time</option>
				<option value='UP875'>(UTC +8:45) Australian Central Western Standard Time</option>
				<option value='UP9'>(UTC +9:00) Japan Standard Time, Korea Standard Time, Yakutsk Time</option>
				<option value='UP95'>(UTC +9:30) Australian Central Standard Time</option>
				<option value='UP10'>(UTC +10:00) Australian Eastern Standard Time, Vladivostok Time</option>
				<option value='UP105'>(UTC +10:30) Lord Howe Island</option>
				<option value='UP11'>(UTC +11:00) Srednekolymsk Time, Solomon Islands, Vanuatu</option>
				<option value='UP115'>(UTC +11:30) Norfolk Island</option>
				<option value='UP12'>(UTC +12:00) Fiji, Gilbert Islands, Kamchatka Time, New Zealand Standard Time</option>
				<option value='UP1275'>(UTC +12:45) Chatham Islands Standard Time</option>
				<option value='UP13'>(UTC +13:00) Phoenix Islands Time, Tonga</option>
				<option value='UP14'>(UTC +14:00) Line Islands</option>
			</select>
		</form>


	This menu is useful if you run a membership site in which your users are
	allowed to set their local timezone value.

	The first parameter lets you set the "selected" state of the menu. For
	example, to set Pacific time as the default you will do this::

		echo timezone_menu('UM8');

	Please see the timezone reference below to see the values of this menu.

	The second parameter lets you set a CSS class name for the menu.

	The fourth parameter lets you set one or more attributes on the generated select tag.

	.. note:: The text contained in the menu is found in the following
		language file: `language/<your_lang>/date_lang.php`

Timezone Reference
==================

The following table indicates each timezone and its location.

Note some of the location lists have been abridged for clarity and formatting.

===========     =====================================================================
Time Zone       Location
===========     =====================================================================
UM12            (UTC - 12:00) Baker/Howland Island
UM11            (UTC - 11:00) Samoa Time Zone, Niue
UM10            (UTC - 10:00) Hawaii-Aleutian Standard Time, Cook Islands
UM95            (UTC - 09:30) Marquesas Islands
UM9             (UTC - 09:00) Alaska Standard Time, Gambier Islands
UM8             (UTC - 08:00) Pacific Standard Time, Clipperton Island
UM7             (UTC - 07:00) Mountain Standard Time
UM6             (UTC - 06:00) Central Standard Time
UM5             (UTC - 05:00) Eastern Standard Time, Western Caribbean
UM45            (UTC - 04:30) Venezuelan Standard Time
UM4             (UTC - 04:00) Atlantic Standard Time, Eastern Caribbean
UM35            (UTC - 03:30) Newfoundland Standard Time
UM3             (UTC - 03:00) Argentina, Brazil, French Guiana, Uruguay
UM2             (UTC - 02:00) South Georgia/South Sandwich Islands
UM1             (UTC -1:00) Azores, Cape Verde Islands
UTC             (UTC) Greenwich Mean Time, Western European Time
UP1             (UTC +1:00) Central European Time, West Africa Time
UP2             (UTC +2:00) Central Africa Time, Eastern European Time
UP3             (UTC +3:00) Moscow Time, East Africa Time
UP35            (UTC +3:30) Iran Standard Time
UP4             (UTC +4:00) Azerbaijan Standard Time, Samara Time
UP45            (UTC +4:30) Afghanistan
UP5             (UTC +5:00) Pakistan Standard Time, Yekaterinburg Time
UP55            (UTC +5:30) Indian Standard Time, Sri Lanka Time
UP575           (UTC +5:45) Nepal Time
UP6             (UTC +6:00) Bangladesh Standard Time, Bhutan Time, Omsk Time
UP65            (UTC +6:30) Cocos Islands, Myanmar
UP7             (UTC +7:00) Krasnoyarsk Time, Cambodia, Laos, Thailand, Vietnam
UP8             (UTC +8:00) Australian Western Standard Time, Beijing Time
UP875           (UTC +8:45) Australian Central Western Standard Time
UP9             (UTC +9:00) Japan Standard Time, Korea Standard Time, Yakutsk
UP95            (UTC +9:30) Australian Central Standard Time
UP10            (UTC +10:00) Australian Eastern Standard Time, Vladivostok Time
UP105           (UTC +10:30) Lord Howe Island
UP11            (UTC +11:00) Srednekolymsk Time, Solomon Islands, Vanuatu
UP115           (UTC +11:30) Norfolk Island
UP12            (UTC +12:00) Fiji, Gilbert Islands, Kamchatka, New Zealand
UP1275          (UTC +12:45) Chatham Islands Standard Time
UP13            (UTC +13:00) Phoenix Islands Time, Tonga
UP14            (UTC +14:00) Line Islands
===========	=====================================================================
