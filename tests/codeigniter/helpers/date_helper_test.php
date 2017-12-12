<?php

class Date_helper_test extends CI_TestCase {

	public function set_up()
	{
		$this->helper('date');
		$this->time = time();
	}

	// ------------------------------------------------------------------------

	public function test_nice_date()
	{
		$this->assertEquals('2016-11-01', nice_date('201611',   'Y-m-d'));
		$this->assertEquals('2016-11-23', nice_date('20161123', 'Y-m-d'));
	}

	// ------------------------------------------------------------------------

	public function test_now_local()
	{
		/*

		// This stub job, is simply to cater $config['time_reference']
		$config = $this->getMockBuilder('CI_Config')->getMock();
		$config->expects($this->any())
			   ->method('item')
			   ->will($this->returnValue('local'));

		// Add the stub to our test instance
		$this->ci_instance_var('config', $config);

		*/

		$this->ci_set_config('time_reference', 'local');

		$this->assertEquals(time(), now());
	}

	// ------------------------------------------------------------------------

	public function test_now_utc()
	{
		/*

		// This stub job, is simply to cater $config['time_reference']
		$config = $this->getMockBuilder('CI_Config')->getMock();
		$config->expects($this->any())
			   ->method('item')
			   ->will($this->returnValue('UTC'));

		// Add the stub to our stdClass
		$this->ci_instance_var('config', $config);

		*/

		$this->assertEquals(
			mktime(gmdate('G'), gmdate('i'), gmdate('s'), gmdate('n'), gmdate('j'), gmdate('Y')),
			now('UTC')
		);
	}

	// ------------------------------------------------------------------------

	public function test_mdate()
	{
		$this->assertEquals(
			date('Y-m-d - h:i a', $this->time),
			mdate('%Y-%m-%d - %h:%i %a', $this->time)
		);
	}

	// ------------------------------------------------------------------------

	public function test_timespan()
	{
		$this->ci_vfs_clone('system/language/english/date_lang.php');

		$loader_cls = $this->ci_core_class('load');
		$this->ci_instance_var('load', new $loader_cls);

		$lang_cls = $this->ci_core_class('lang');
		$this->ci_instance_var('lang', new $lang_cls);

		$this->assertEquals('1 Second', timespan(time(), time()+1));
		$this->assertEquals('1 Minute', timespan(time(), time()+60));
		$this->assertEquals('1 Hour', timespan(time(), time()+3600));
		$this->assertEquals('2 Hours', timespan(time(), time()+7200));
	}

	// ------------------------------------------------------------------------

	public function test_days_in_month()
	{
		$this->assertEquals(30, days_in_month(06, 2005));
		$this->assertEquals(28, days_in_month(02, 2011));
		$this->assertEquals(29, days_in_month(02, 2012));
	}

	// ------------------------------------------------------------------------

	public function test_local_to_gmt()
	{
		$this->assertEquals(
			mktime(
				gmdate('G', $this->time), gmdate('i', $this->time), gmdate('s', $this->time),
				gmdate('n', $this->time), gmdate('j', $this->time), gmdate('Y', $this->time)
			),
			local_to_gmt($this->time)
		);
	}

	// ------------------------------------------------------------------------

	public function test_gmt_to_local()
	{
		$this->assertEquals(1140128493, gmt_to_local('1140153693', 'UM8', TRUE));
	}

	// ------------------------------------------------------------------------

	public function test_mysql_to_unix()
	{
		$this->assertEquals($this->time, mysql_to_unix(date('Y-m-d H:i:s', $this->time)));
	}

	// ------------------------------------------------------------------------

	public function test_unix_to_human()
	{
		$this->assertEquals(date('Y-m-d h:i A', $this->time), unix_to_human($this->time));
		$this->assertEquals(date('Y-m-d h:i:s A', $this->time), unix_to_human($this->time, TRUE, 'us'));
		$this->assertEquals(date('Y-m-d H:i:s', $this->time), unix_to_human($this->time, TRUE, 'eu'));
	}

	// ------------------------------------------------------------------------

	public function test_human_to_unix()
	{
		$date = '2000-12-31 10:00:00 PM';
		$this->assertEquals(strtotime($date), human_to_unix($date));
		$this->assertFalse(human_to_unix());
	}

	// ------------------------------------------------------------------------

	public function test_timezones()
	{
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

		foreach ($zones AS $test => $expected)
		{
			$this->assertEquals($expected, timezones($test));
		}

		$this->assertArrayHasKey('UP3', timezones());
		$this->assertEquals(0, timezones('non_existent'));
	}

	// ------------------------------------------------------------------------

	public function test_date_range()
	{
		$dates = array(
			'29-01-2012', '30-01-2012', '31-01-2012',
			'01-02-2012', '02-02-2012', '03-02-2012',
			'04-02-2012', '05-02-2012', '06-02-2012',
			'07-02-2012', '08-02-2012', '09-02-2012',
			'10-02-2012', '11-02-2012', '12-02-2012',
			'13-02-2012', '14-02-2012', '15-02-2012',
			'16-02-2012', '17-02-2012', '18-02-2012',
			'19-02-2012', '20-02-2012', '21-02-2012',
			'22-02-2012', '23-02-2012', '24-02-2012',
			'25-02-2012', '26-02-2012', '27-02-2012',
			'28-02-2012', '29-02-2012', '01-03-2012'
		);

		$this->assertEquals($dates, date_range(mktime(12, 0, 0, 1, 29, 2012), mktime(12, 0, 0, 3, 1, 2012), TRUE, 'd-m-Y'));
		array_pop($dates);
		$this->assertEquals($dates, date_range(mktime(12, 0, 0, 1, 29, 2012), 31, FALSE, 'd-m-Y'));
	}

}
