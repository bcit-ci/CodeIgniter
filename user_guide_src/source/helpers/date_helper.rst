###########
Date Helper
###########

The Date Helper file contains functions that help you work with dates.

.. contents:: Page Contents

Loading this Helper
===================

This helper is loaded using the following code

::

	$this->load->helper('date');

The following functions are available:

now()
=====

Returns the current time as a Unix timestamp, referenced either to your
server's local time or GMT, based on the "time reference" setting in
your config file. If you do not intend to set your master time reference
to GMT (which you'll typically do if you run a site that lets each user
set their own timezone settings) there is no benefit to using this
function over PHP's time() function.

.. php:method:: now()

mdate()
=======

This function is identical to PHPs `date() <http://www.php.net/date>`_
function, except that it lets you use MySQL style date codes, where each
code letter is preceded with a percent sign: %Y %m %d etc.

The benefit of doing dates this way is that you don't have to worry
about escaping any characters that are not date codes, as you would
normally have to do with the date() function. Example

.. php:method:: mdate($datestr = '', $time = '')

	:param string 	$datestr: Date String
	:param integer 	$time: time
	:returns: integer


::

	$datestring = "Year: %Y Month: %m Day: %d - %h:%i %a";
	$time = time();
	echo mdate($datestring, $time);

If a timestamp is not included in the second parameter the current time
will be used.

standard_date()
===============

Lets you generate a date string in one of several standardized formats.
Example

.. php:method:: standard_date($fmt = 'DATE_RFC822', $time = '')

	:param string 	$fmt: the chosen format
	:param string 	$time: Unix timestamp
	:returns: string

::

	$format = 'DATE_RFC822';
	$time = time();
	echo standard_date($format, $time);

The first parameter must contain the format, the second parameter must
contain the date as a Unix timestamp.

Supported formats:

+----------------+------------------------+-----------------------------------+
| Constant       | Description            | Example                           |
+================+========================+===================================+
| DATE_ATOM      | Atom                   | 2005-08-15T16:13:03+0000          |
+----------------+------------------------+-----------------------------------+
| DATE_COOKIE    | HTTP Cookies           | Sun, 14 Aug 2005 16:13:03 UTC     |
+----------------+------------------------+-----------------------------------+
| DATE_ISO8601   | ISO-8601               | 2005-08-14T16:13:03+00:00         |
+----------------+------------------------+-----------------------------------+
| DATE_RFC822    | RFC 822                | Sun, 14 Aug 05 16:13:03 UTC       |
+----------------+------------------------+-----------------------------------+
| DATE_RFC850    | RFC 850                | Sunday, 14-Aug-05 16:13:03 UTC    |
+----------------+------------------------+-----------------------------------+
| DATE_RFC1036   | RFC 1036               | Sunday, 14-Aug-05 16:13:03 UTC    |
+----------------+------------------------+-----------------------------------+
| DATE_RFC1123   | RFC 1123               | Sun, 14 Aug 2005 16:13:03 UTC     |
+----------------+------------------------+-----------------------------------+
| DATE_RFC2822   | RFC 2822               | Sun, 14 Aug 2005 16:13:03 +0000   |
+----------------+------------------------+-----------------------------------+
| DATE_RSS       | RSS                    | Sun, 14 Aug 2005 16:13:03 UTC     |
+----------------+------------------------+-----------------------------------+
| DATE_W3C       | W3C                    | 2005-08-14T16:13:03+0000          |
+----------------+------------------------+-----------------------------------+


local_to_gmt()
==============

Takes a Unix timestamp as input and returns it as GMT. 

.. php:method:: local_to_gmt($time = '')

	:param integer 	$time: Unix timestamp
	:returns: string

Example:

::

	$now = time();
	$gmt = local_to_gmt($now);

gmt_to_local()
==============

Takes a Unix timestamp (referenced to GMT) as input, and converts it to
a localized timestamp based on the timezone and Daylight Saving time
submitted.

.. php:method:: gmt_to_local($time = '', $timezone = 'UTC', $dst = FALSE)

	:param integer 	$time: Unix timestamp
	:param string 	$timezone: timezone
	:param boolean 	$dst: whether DST is active
	:returns: integer

Example

::

	$timestamp = '1140153693';
	$timezone  = 'UM8';
	$daylight_saving = TRUE;
	echo gmt_to_local($timestamp, $timezone, $daylight_saving);


.. note:: For a list of timezones see the reference at the bottom of this page.


mysql_to_unix()
===============

Takes a MySQL Timestamp as input and returns it as Unix. 

.. php:method:: mysql_to_unix($time = '')

	:param integer 	$time: Unix timestamp
	:returns: integer

Example

::

	$mysql = '20061124092345';
	$unix = mysql_to_unix($mysql);

unix_to_human()
===============

Takes a Unix timestamp as input and returns it in a human readable
format with this prototype

.. php:method:: unix_to_human($time = '', $seconds = FALSE, $fmt = 'us')

	:param integer 	$time: Unix timestamp
	:param boolean 	$seconds: whether to show seconds
	:param string 	$fmt: format: us or euro
	:returns: integer

Example

::

	YYYY-MM-DD HH:MM:SS AM/PM

This can be useful if you need to display a date in a form field for
submission.

The time can be formatted with or without seconds, and it can be set to
European or US format. If only the timestamp is submitted it will return
the time without seconds formatted for the U.S. Examples

::

	$now = time();
	echo unix_to_human($now); // U.S. time, no seconds
	echo unix_to_human($now, TRUE, 'us'); // U.S. time with seconds
	echo unix_to_human($now, TRUE, 'eu'); // Euro time with seconds

human_to_unix()
===============

The opposite of the above function. Takes a "human" time as input and
returns it as Unix. This function is useful if you accept "human"
formatted dates submitted via a form. Returns FALSE (boolean) if the
date string passed to it is not formatted as indicated above. 

.. php:method:: human_to_unix($datestr = '')

	:param integer 	$datestr: Date String
	:returns: integer

Example:

::

	$now = time();
	$human = unix_to_human($now);
	$unix = human_to_unix($human);

nice_date()
===========

This function can take a number poorly-formed date formats and convert
them into something useful. It also accepts well-formed dates.

The function will return a Unix timestamp by default. You can,
optionally, pass a format string (the same type as the PHP date function
accepts) as the second parameter. 

.. php:method:: nice_date($bad_date = '', $format = FALSE) 

	:param integer 	$bad_date: The terribly formatted date-like string
	:param string 	$format: Date format to return (same as php date function)
	:returns: string

Example

::

	$bad_time = 199605  // Should Produce: 1996-05-01
	$better_time = nice_date($bad_time,'Y-m-d');
	$bad_time = 9-11-2001 // Should Produce: 2001-09-11
	$better_time = nice_date($human,'Y-m-d');

timespan()
==========

Formats a unix timestamp so that is appears similar to this

::

	1 Year, 10 Months, 2 Weeks, 5 Days, 10 Hours, 16 Minutes

The first parameter must contain a Unix timestamp. The second parameter
must contain a timestamp that is greater that the first timestamp. If
the second parameter empty, the current time will be used. The third 
parameter is optional and limits the number of time units to display. 
The most common purpose for this function is to show how much time has 
elapsed from some point in time in the past to now. 

.. php:method:: timespan($seconds = 1, $time = '', $units = '')

	:param integer 	$seconds: a number of seconds
	:param string 	$time: Unix timestamp
	:param integer 	$units: a number of time units to display
	:returns: string

Example

::

	$post_date = '1079621429';
	$now = time();
	$units = 2;
	echo timespan($post_date, $now, $units);

.. note:: The text generated by this function is found in the following language
	file: language/<your_lang>/date_lang.php

days_in_month()
===============

Returns the number of days in a given month/year. Takes leap years into
account. 

.. php:method:: days_in_month($month = 0, $year = '')

	:param integer 	$month: a numeric month
	:param integer 	$year: a numeric year
	:returns: integer

Example

::

	echo days_in_month(06, 2005);

If the second parameter is empty, the current year will be used.

timezones()
===========

Takes a timezone reference (for a list of valid timezones, see the
"Timezone Reference" below) and returns the number of hours offset from
UTC.

.. php:method:: timezones($tz = '')

	:param string 	$tz: a numeric timezone
	:returns: string

Example

::

	echo timezones('UM5');


This function is useful when used with `timezone_menu()`.

timezone_menu()
===============

Generates a pull-down menu of timezones, like this one:


.. raw:: html

	<form action="#">
		<select name="timezones">
			<option value='UM12'>(UTC - 12:00) Enitwetok, Kwajalien</option>
			<option value='UM11'>(UTC - 11:00) Nome, Midway Island, Samoa</option>
			<option value='UM10'>(UTC - 10:00) Hawaii</option>
			<option value='UM9'>(UTC - 9:00) Alaska</option>
			<option value='UM8'>(UTC - 8:00) Pacific Time</option>
			<option value='UM7'>(UTC - 7:00) Mountain Time</option>
			<option value='UM6'>(UTC - 6:00) Central Time, Mexico City</option>
			<option value='UM5'>(UTC - 5:00) Eastern Time, Bogota, Lima, Quito</option>
			<option value='UM4'>(UTC - 4:00) Atlantic Time, Caracas, La Paz</option>
			<option value='UM25'>(UTC - 3:30) Newfoundland</option>
			<option value='UM3'>(UTC - 3:00) Brazil, Buenos Aires, Georgetown, Falkland Is.</option>
			<option value='UM2'>(UTC - 2:00) Mid-Atlantic, Ascention Is., St Helena</option>
			<option value='UM1'>(UTC - 1:00) Azores, Cape Verde Islands</option>
			<option value='UTC' selected='selected'>(UTC) Casablanca, Dublin, Edinburgh, London, Lisbon, Monrovia</option>
			<option value='UP1'>(UTC + 1:00) Berlin, Brussels, Copenhagen, Madrid, Paris, Rome</option>
			<option value='UP2'>(UTC + 2:00) Kaliningrad, South Africa, Warsaw</option>
			<option value='UP3'>(UTC + 3:00) Baghdad, Riyadh, Moscow, Nairobi</option>
			<option value='UP25'>(UTC + 3:30) Tehran</option>
			<option value='UP4'>(UTC + 4:00) Adu Dhabi, Baku, Muscat, Tbilisi</option>
			<option value='UP35'>(UTC + 4:30) Kabul</option>
			<option value='UP5'>(UTC + 5:00) Islamabad, Karachi, Tashkent</option>
			<option value='UP45'>(UTC + 5:30) Bombay, Calcutta, Madras, New Delhi</option>
			<option value='UP6'>(UTC + 6:00) Almaty, Colomba, Dhaka</option>
			<option value='UP7'>(UTC + 7:00) Bangkok, Hanoi, Jakarta</option>
			<option value='UP8'>(UTC + 8:00) Beijing, Hong Kong, Perth, Singapore, Taipei</option>
			<option value='UP9'>(UTC + 9:00) Osaka, Sapporo, Seoul, Tokyo, Yakutsk</option>
			<option value='UP85'>(UTC + 9:30) Adelaide, Darwin</option>
			<option value='UP10'>(UTC + 10:00) Melbourne, Papua New Guinea, Sydney, Vladivostok</option>
			<option value='UP11'>(UTC + 11:00) Magadan, New Caledonia, Solomon Islands</option>
			<option value='UP12'>(UTC + 12:00) Auckland, Wellington, Fiji, Marshall Island</option>
		</select>
	</form>


This menu is useful if you run a membership site in which your users are
allowed to set their local timezone value.

The first parameter lets you set the "selected" state of the menu. For
example, to set Pacific time as the default you will do this

.. php:method:: timezone_menu($default = 'UTC', $class = "", $name = 'timezones')

	:param string 	$default: timezone
	:param string	$class: classname
	:param string	$name: menu name
	:returns: string

Example: 

::

	echo timezone_menu('UM8');

Please see the timezone reference below to see the values of this menu.

The second parameter lets you set a CSS class name for the menu.

.. note:: The text contained in the menu is found in the following
	language file: `language/<your_lang>/date_lang.php`


Timezone Reference
==================

The following table indicates each timezone and its location.

+------------+----------------------------------------------------------------+
| Time Zone  | Location                                                       |
+============+================================================================+
| UM12       | (UTC - 12:00) Enitwetok, Kwajalien                             |
+------------+----------------------------------------------------------------+
| UM11       | (UTC - 11:00) Nome, Midway Island, Samoa                       |
+------------+----------------------------------------------------------------+
| UM10       | (UTC - 10:00) Hawaii                                           |
+------------+----------------------------------------------------------------+
| UM9        | (UTC - 9:00) Alaska                                            |
+------------+----------------------------------------------------------------+
| UM8        | (UTC - 8:00) Pacific Time                                      |
+------------+----------------------------------------------------------------+
| UM7        | (UTC - 7:00) Mountain Time                                     |
+------------+----------------------------------------------------------------+
| UM6        | (UTC - 6:00) Central Time, Mexico City                         |
+------------+----------------------------------------------------------------+
| UM5        | (UTC - 5:00) Eastern Time, Bogota, Lima, Quito                 |
+------------+----------------------------------------------------------------+
| UM4        | (UTC - 4:00) Atlantic Time, Caracas, La Paz                    |
+------------+----------------------------------------------------------------+
| UM25       | (UTC - 3:30) Newfoundland                                      |
+------------+----------------------------------------------------------------+
| UM3        | (UTC - 3:00) Brazil, Buenos Aires, Georgetown, Falkland Is.    |
+------------+----------------------------------------------------------------+
| UM2        | (UTC - 2:00) Mid-Atlantic, Ascention Is., St Helena            |
+------------+----------------------------------------------------------------+
| UM1        | (UTC - 1:00) Azores, Cape Verde Islands                        |
+------------+----------------------------------------------------------------+
| UTC        | (UTC) Casablanca, Dublin, Edinburgh, London, Lisbon, Monrovia  |
+------------+----------------------------------------------------------------+
| UP1        | (UTC + 1:00) Berlin, Brussels, Copenhagen, Madrid, Paris, Rome |
+------------+----------------------------------------------------------------+
| UP2        | (UTC + 2:00) Kaliningrad, South Africa, Warsaw                 |
+------------+----------------------------------------------------------------+
| UP3        | (UTC + 3:00) Baghdad, Riyadh, Moscow, Nairobi                  |
+------------+----------------------------------------------------------------+
| UP25       | (UTC + 3:30) Tehran                                            |
+------------+----------------------------------------------------------------+
| UP4        | (UTC + 4:00) Adu Dhabi, Baku, Muscat, Tbilisi                  |
+------------+----------------------------------------------------------------+
| UP35       | (UTC + 4:30) Kabul                                             |
+------------+----------------------------------------------------------------+
| UP5        | (UTC + 5:00) Islamabad, Karachi, Tashkent                      |
+------------+----------------------------------------------------------------+
| UP45       | (UTC + 5:30) Bombay, Calcutta, Madras, New Delhi               |
+------------+----------------------------------------------------------------+
| UP6        | (UTC + 6:00) Almaty, Colomba, Dhaka                            |
+------------+----------------------------------------------------------------+
| UP7        | (UTC + 7:00) Bangkok, Hanoi, Jakarta                           |
+------------+----------------------------------------------------------------+
| UP8        | (UTC + 8:00) Beijing, Hong Kong, Perth, Singapore, Taipei      |
+------------+----------------------------------------------------------------+
| UP9        | (UTC + 9:00) Osaka, Sapporo, Seoul, Tokyo, Yakutsk             |
+------------+----------------------------------------------------------------+
| UP85       | (UTC + 9:30) Adelaide, Darwin                                  |
+------------+----------------------------------------------------------------+
| UP10       | (UTC + 10:00) Melbourne, Papua New Guinea, Sydney, Vladivostok |
+------------+----------------------------------------------------------------+
| UP11       | (UTC + 11:00) Magadan, New Caledonia, Solomon Islands          |
+------------+----------------------------------------------------------------+
| UP12       | (UTC + 12:00) Auckland, Wellington, Fiji, Marshall Island      |
+------------+----------------------------------------------------------------+
