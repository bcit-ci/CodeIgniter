#################
Calendaring Class
#################

The Calendar class enables you to dynamically create calendars. Your
calendars can be formatted through the use of a calendar template,
allowing 100% control over every aspect of its design. In addition, you
can pass data to your calendar cells.

.. contents::
  :local:

.. raw:: html

  <div class="custom-index container"></div>

***************************
Using the Calendaring Class
***************************

Initializing the Class
======================

Like most other classes in CodeIgniter, the Calendar class is
initialized in your controller using the $this->load->library function::

	$this->load->library('calendar');

Once loaded, the Calendar object will be available using::

	$this->calendar

Displaying a Calendar
=====================

Here is a very simple example showing how you can display a calendar::

	$this->load->library('calendar');
	echo $this->calendar->generate();

The above code will generate a calendar for the current month/year based
on your server time. To show a calendar for a specific month and year
you will pass this information to the calendar generating function::

	$this->load->library('calendar');
	echo $this->calendar->generate(2006, 6);

The above code will generate a calendar showing the month of June in
2006. The first parameter specifies the year, the second parameter
specifies the month.

Passing Data to your Calendar Cells
===================================

To add data to your calendar cells involves creating an associative
array in which the keys correspond to the days you wish to populate and
the array value contains the data. The array is passed to the third
parameter of the calendar generating function. Consider this example::

	$this->load->library('calendar');

	$data = array(
		3  => 'http://example.com/news/article/2006/06/03/',
		7  => 'http://example.com/news/article/2006/06/07/',
		13 => 'http://example.com/news/article/2006/06/13/',
		26 => 'http://example.com/news/article/2006/06/26/'
	);

	echo $this->calendar->generate(2006, 6, $data);

Using the above example, day numbers 3, 7, 13, and 26 will become links
pointing to the URLs you've provided.

.. note:: By default it is assumed that your array will contain links.
	In the section that explains the calendar template below you'll see how
	you can customize how data passed to your cells is handled so you can
	pass different types of information.

Setting Display Preferences
===========================

There are seven preferences you can set to control various aspects of
the calendar. Preferences are set by passing an array of preferences in
the second parameter of the loading function. Here is an example::

	$prefs = array(
		'start_day'    => 'saturday',
		'month_type'   => 'long',
		'day_type'     => 'short'
	);

	$this->load->library('calendar', $prefs);

	echo $this->calendar->generate();

The above code would start the calendar on saturday, use the "long"
month heading, and the "short" day names. More information regarding
preferences below.

======================  =================  ============================================  ===================================================================
Preference              Default            Options                                       Description
======================  =================  ============================================  ===================================================================
**template**           	None               None                                          A string or array containing your calendar template.
											   See the template section below.
**local_time**        	time()             None                                          A Unix timestamp corresponding to the current time.
**start_day**           sunday             Any week day (sunday, monday, tuesday, etc.)  Sets the day of the week the calendar should start on.
**month_type**          long               long, short                                   Determines what version of the month name to use in the header.
											   long = January, short = Jan.
**day_type**            abr                long, short, abr                              Determines what version of the weekday names to use in
											   the column headers. long = Sunday, short = Sun, abr = Su.
**show_next_prev**      FALSE              TRUE/FALSE (boolean)                          Determines whether to display links allowing you to toggle
											   to next/previous months. See information on this feature below.
**next_prev_url**       controller/method  A URL                                         Sets the basepath used in the next/previous calendar links.
**show_other_days**     FALSE              TRUE/FALSE (boolean)                          Determines whether to display days of other months that share the
											   first or last week of the calendar month.
======================  =================  ============================================  ===================================================================


Showing Next/Previous Month Links
=================================

To allow your calendar to dynamically increment/decrement via the
next/previous links requires that you set up your calendar code similar
to this example::

	$prefs = array(
		'show_next_prev'  => TRUE,
		'next_prev_url'   => 'http://example.com/index.php/calendar/show/'
	);

	$this->load->library('calendar', $prefs);

	echo $this->calendar->generate($this->uri->segment(3), $this->uri->segment(4));

You'll notice a few things about the above example:

-  You must set the "show_next_prev" to TRUE.
-  You must supply the URL to the controller containing your calendar in
   the "next_prev_url" preference. If you don't, it will be set to the current
   *controller/method*.
-  You must supply the "year" and "month" to the calendar generating
   function via the URI segments where they appear (Note: The calendar
   class automatically adds the year/month to the base URL you
   provide.).

Creating a Calendar Template
============================

By creating a calendar template you have 100% control over the design of
your calendar. Using the string method, each component of your calendar
will be placed within a pair of pseudo-variables as shown here::

	$prefs['template'] = '

		{table_open}<table border="0" cellpadding="0" cellspacing="0">{/table_open}

		{heading_row_start}<tr>{/heading_row_start}

		{heading_previous_cell}<th><a href="{previous_url}">&lt;&lt;</a></th>{/heading_previous_cell}
		{heading_title_cell}<th colspan="{colspan}">{heading}</th>{/heading_title_cell}
		{heading_next_cell}<th><a href="{next_url}">&gt;&gt;</a></th>{/heading_next_cell}

		{heading_row_end}</tr>{/heading_row_end}

		{week_row_start}<tr>{/week_row_start}
		{week_day_cell}<td>{week_day}</td>{/week_day_cell}
		{week_row_end}</tr>{/week_row_end}

		{cal_row_start}<tr>{/cal_row_start}
		{cal_cell_start}<td>{/cal_cell_start}
		{cal_cell_start_today}<td>{/cal_cell_start_today}
		{cal_cell_start_other}<td class="other-month">{/cal_cell_start_other}

		{cal_cell_content}<a href="{content}">{day}</a>{/cal_cell_content}
		{cal_cell_content_today}<div class="highlight"><a href="{content}">{day}</a></div>{/cal_cell_content_today}

		{cal_cell_no_content}{day}{/cal_cell_no_content}
		{cal_cell_no_content_today}<div class="highlight">{day}</div>{/cal_cell_no_content_today}

		{cal_cell_blank}&nbsp;{/cal_cell_blank}

		{cal_cell_other}{day}{/cal_cel_other}

		{cal_cell_end}</td>{/cal_cell_end}
		{cal_cell_end_today}</td>{/cal_cell_end_today}
		{cal_cell_end_other}</td>{/cal_cell_end_other}
		{cal_row_end}</tr>{/cal_row_end}

		{table_close}</table>{/table_close}
	';

	$this->load->library('calendar', $prefs);

	echo $this->calendar->generate();

Using the array method, you will pass `key => value` pairs. You can pass as
many or as few values as you'd like. Omitted keys will use the default values
inherited in the calendar class.

Example::

	$prefs['template'] = array(
		'table_open'           => '<table class="calendar">',
		'cal_cell_start'       => '<td class="day">',
		'cal_cell_start_today' => '<td class="today">'
	);
    
	$this->load->library('calendar', $prefs);
    
	echo $this->calendar->generate();

***************
Class Reference
***************

.. php:class:: CI_Calendar

	.. php:method:: initialize([$config = array()])

		:param	array	$config: Configuration parameters
		:returns:	CI_Calendar instance (method chaining)
		:rtype:	CI_Calendar

		Initializes the Calendaring preferences. Accepts an associative array as input, containing display preferences.

	.. php:method:: generate([$year = ''[, $month = ''[, $data = array()]]])

		:param	int	$year: Year
		:param	int	$month: Month
		:param	array	$data: Data to be shown in the calendar cells
		:returns:	HTML-formatted calendar
		:rtype:	string

		Generate the calendar.


	.. php:method:: get_month_name($month)

		:param	int	$month: Month
		:returns:	Month name
		:rtype:	string

		Generates a textual month name based on the numeric month provided.

	.. php:method:: get_day_names($day_type = '')

		:param	string	$day_type: 'long', 'short', or 'abr'
		:returns:	Array of day names
		:rtype:	array

		Returns an array of day names (Sunday, Monday, etc.) based on the type
		provided. Options: long, short, abr. If no ``$day_type`` is provided (or
		if an invalid type is provided) this method will return the "abbreviated"
		style.

	.. php:method:: adjust_date($month, $year)

		:param	int	$month: Month
		:param	int	$year: Year
		:returns:	An associative array containing month and year
		:rtype:	array

		This method makes sure that you have a valid month/year. For example, if
		you submit 13 as the month, the year will increment and the month will
		become January::

			print_r($this->calendar->adjust_date(13, 2014));

		outputs::

			Array
			(    
				[month] => '01'
				[year] => '2015'
			)

	.. php:method:: get_total_days($month, $year)

		:param	int	$month: Month
		:param	int	$year: Year
		:returns:	Count of days in the specified month
		:rtype:	int

		Total days in a given month::

			echo $this->calendar->get_total_days(2, 2012);
			// 29

		.. note:: This method is an alias for :doc:`Date Helper
			<../helpers/date_helper>` function :php:func:`days_in_month()`.

	.. php:method:: default_template()

		:returns:	An array of template values
		:rtype:	array

		Sets the default template. This method is used when you have not created
		your own template.


	.. php:method:: parse_template()

		:returns:	CI_Calendar instance (method chaining)
		:rtype:	CI_Calendar

		Harvests the data within the template ``{pseudo-variables}`` used to
		display the calendar.
