<?php

class Calendar_test extends CI_TestCase {

	public function set_up()
	{
		// Required for get_total_days()
		$this->ci_instance_var('load', $this->getMockBuilder('CI_Loader')->setMethods(array('helper'))->getMock());

		$lang = $this->getMockBuilder('CI_Lang')->setMethods(array('load', 'line'))->getMock();
		$lang->expects($this->any())->method('line')->will($this->returnValue(FALSE));
		$this->ci_instance_var('lang', $lang);

		$this->calendar = new CI_Calendar();
	}

	// --------------------------------------------------------------------

	public function test_initialize()
	{
		$this->calendar->initialize(array(
			'month_type'	=>	'short',
			'start_day'	=>	'monday'
		));
		$this->assertEquals('short', $this->calendar->month_type);
		$this->assertEquals('monday', $this->calendar->start_day);
	}

	// --------------------------------------------------------------------

	public function test_generate()
	{
		$no_events = '<table border="0" cellpadding="4" cellspacing="0">

<tr>
<th colspan="7">September&nbsp;2011</th>

</tr>

<tr>
<td>Su</td><td>Mo</td><td>Tu</td><td>We</td><td>Th</td><td>Fr</td><td>Sa</td>
</tr>

<tr>
<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>1</td><td>2</td><td>3</td>
</tr>

<tr>
<td>4</td><td>5</td><td>6</td><td>7</td><td>8</td><td>9</td><td>10</td>
</tr>

<tr>
<td>11</td><td>12</td><td>13</td><td>14</td><td>15</td><td>16</td><td>17</td>
</tr>

<tr>
<td>18</td><td>19</td><td>20</td><td>21</td><td>22</td><td>23</td><td>24</td>
</tr>

<tr>
<td>25</td><td>26</td><td>27</td><td>28</td><td>29</td><td>30</td><td>&nbsp;</td>
</tr>

</table>';

		$this->assertEquals($no_events, $this->calendar->generate(2011, 9));

		$data = array(
			3  => 'http://example.com/news/article/2006/03/',
			7  => 'http://example.com/news/article/2006/07/',
			13 => 'http://example.com/news/article/2006/13/',
			26 => 'http://example.com/news/article/2006/26/'
		);

		$events = '<table border="0" cellpadding="4" cellspacing="0">

<tr>
<th colspan="7">September&nbsp;2011</th>

</tr>

<tr>
<td>Su</td><td>Mo</td><td>Tu</td><td>We</td><td>Th</td><td>Fr</td><td>Sa</td>
</tr>

<tr>
<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>1</td><td>2</td><td><a href="http://example.com/news/article/2006/03/">3</a></td>
</tr>

<tr>
<td>4</td><td>5</td><td>6</td><td><a href="http://example.com/news/article/2006/07/">7</a></td><td>8</td><td>9</td><td>10</td>
</tr>

<tr>
<td>11</td><td>12</td><td><a href="http://example.com/news/article/2006/13/">13</a></td><td>14</td><td>15</td><td>16</td><td>17</td>
</tr>

<tr>
<td>18</td><td>19</td><td>20</td><td>21</td><td>22</td><td>23</td><td>24</td>
</tr>

<tr>
<td>25</td><td><a href="http://example.com/news/article/2006/26/">26</a></td><td>27</td><td>28</td><td>29</td><td>30</td><td>&nbsp;</td>
</tr>

</table>';

		$this->assertEquals($events, $this->calendar->generate(2011, 9, $data));
	}

	// --------------------------------------------------------------------

	public function test_get_month_name()
	{
		$this->calendar->month_type = NULL;
		$this->assertEquals('January', $this->calendar->get_month_name('01'));

		$this->calendar->month_type = 'short';
		$this->assertEquals('Jan', $this->calendar->get_month_name('01'));
	}

	// --------------------------------------------------------------------

	public function test_get_day_names()
	{
		$this->assertEquals(array(
			'Sunday',
			'Monday',
			'Tuesday',
			'Wednesday',
			'Thursday',
			'Friday',
			'Saturday'
		), $this->calendar->get_day_names('long'));

		$this->assertEquals(array(
			'Sun',
			'Mon',
			'Tue',
			'Wed',
			'Thu',
			'Fri',
			'Sat'
		), $this->calendar->get_day_names('short'));

		$this->calendar->day_type = NULL;

		$this->assertEquals(array(
			'Su',
			'Mo',
			'Tu',
			'We',
			'Th',
			'Fr',
			'Sa'
		), $this->calendar->get_day_names());
	}

	// --------------------------------------------------------------------

	public function test_adjust_date()
	{
		$this->assertEquals(array('month' => 8, 'year' => 2012), $this->calendar->adjust_date(8, 2012));
		$this->assertEquals(array('month' => 1, 'year' => 2013), $this->calendar->adjust_date(13, 2012));
	}

	// --------------------------------------------------------------------

	public function test_get_total_days()
	{
		$this->assertEquals(0, $this->calendar->get_total_days(13, 2012));

		$this->assertEquals(31, $this->calendar->get_total_days(1, 2012));
		$this->assertEquals(28, $this->calendar->get_total_days(2, 2011));
		$this->assertEquals(29, $this->calendar->get_total_days(2, 2012));
		$this->assertEquals(31, $this->calendar->get_total_days(3, 2012));
		$this->assertEquals(30, $this->calendar->get_total_days(4, 2012));
		$this->assertEquals(31, $this->calendar->get_total_days(5, 2012));
		$this->assertEquals(30, $this->calendar->get_total_days(6, 2012));
		$this->assertEquals(31, $this->calendar->get_total_days(7, 2012));
		$this->assertEquals(31, $this->calendar->get_total_days(8, 2012));
		$this->assertEquals(30, $this->calendar->get_total_days(9, 2012));
		$this->assertEquals(31, $this->calendar->get_total_days(10, 2012));
		$this->assertEquals(30, $this->calendar->get_total_days(11, 2012));
		$this->assertEquals(31, $this->calendar->get_total_days(12, 2012));
	}

	// --------------------------------------------------------------------

	public function test_default_template()
	{
		$array = array(
			'table_open'			=> '<table border="0" cellpadding="4" cellspacing="0">',
			'heading_row_start'		=> '<tr>',
			'heading_previous_cell'		=> '<th><a href="{previous_url}">&lt;&lt;</a></th>',
			'heading_title_cell'		=> '<th colspan="{colspan}">{heading}</th>',
			'heading_next_cell'		=> '<th><a href="{next_url}">&gt;&gt;</a></th>',
			'heading_row_end'		=> '</tr>',
			'week_row_start'		=> '<tr>',
			'week_day_cell'			=> '<td>{week_day}</td>',
			'week_row_end'			=> '</tr>',
			'cal_row_start'			=> '<tr>',
			'cal_cell_start'		=> '<td>',
			'cal_cell_start_today'		=> '<td>',
			'cal_cell_content'		=> '<a href="{content}">{day}</a>',
			'cal_cell_content_today'	=> '<a href="{content}"><strong>{day}</strong></a>',
			'cal_cell_no_content'		=> '{day}',
			'cal_cell_no_content_today'	=> '<strong>{day}</strong>',
			'cal_cell_blank'		=> '&nbsp;',
			'cal_cell_end'			=> '</td>',
			'cal_cell_end_today'		=> '</td>',
			'cal_row_end'			=> '</tr>',
			'table_close'			=> '</table>',
			'cal_cell_start_other'		=> '<td style="color: #666;">',
			'cal_cell_other'		=> '{day}',
			'cal_cell_end_other'		=> '</td>'
		);

		$this->assertEquals($array, $this->calendar->default_template());
	}

}
