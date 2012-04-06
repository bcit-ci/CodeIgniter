<?php

class Date_helper_test extends CI_TestCase {

	public function set_up()
	{
		$this->helper('date');
	}

	// ------------------------------------------------------------------------

	public function test_now_local()
	{
		// This stub job, is simply to cater $config['time_reference']
		$config = $this->getMock('CI_Config');
		$config->expects($this->any())
			   ->method('item')
			   ->will($this->returnValue('local'));
		
		// Add the stub to our test instance
		$this->ci_instance_var('config', $config);

		$expected = time();
		$test = now();
		$this->assertEquals($expected, $test);
	}

	// ------------------------------------------------------------------------

	public function test_now_gmt()
	{
		// This stub job, is simply to cater $config['time_reference']
		$config = $this->getMock('CI_Config');
		$config->expects($this->any())
			   ->method('item')
			   ->will($this->returnValue('gmt'));
		
		// Add the stub to our stdClass
		$this->ci_instance_var('config', $config);

		$t = time();
		$expected = mktime(gmdate("H", $t), gmdate("i", $t), gmdate("s", $t), gmdate("m", $t), gmdate("d", $t), gmdate("Y", $t));
		$test = now();
		$this->assertEquals($expected, $test);
	}

	// ------------------------------------------------------------------------

	public function test_mdate()
	{
		$time = time();
		$expected = date("Y-m-d - h:i a", $time);
		$test = mdate("%Y-%m-%d - %h:%i %a", $time);
		$this->assertEquals($expected, $test);
	}

	// ------------------------------------------------------------------------

	public function test_standard_date_rfc822()
	{
		$time = time();
		$format = 'DATE_RFC822';
		$expected = date("D, d M y H:i:s O", $time);
		$this->assertEquals($expected, standard_date($format, $time));
	}

	// ------------------------------------------------------------------------

	public function test_standard_date_atom()
	{
		$time = time();
		$format = 'DATE_ATOM';
		$expected = date("Y-m-d\TH:i:sO", $time);
		$this->assertEquals($expected, standard_date($format, $time));
	}

	// ------------------------------------------------------------------------

	public function test_standard_date_cookie()
	{
		$time = time();
		$format = 'DATE_COOKIE';
		$expected = date("l, d-M-y H:i:s \U\T\C", $time);
		$this->assertEquals($expected, standard_date($format, $time));
	}

	// ------------------------------------------------------------------------

	public function test_standard_date_iso8601()
	{
		$time = time();
		$format = 'DATE_ISO8601';
		$expected = date("Y-m-d\TH:i:sO", $time);
		$this->assertEquals($expected, standard_date($format, $time));
	}

	// ------------------------------------------------------------------------

	public function test_standard_date_rfc850()
	{
		$time = time();
		$format = 'DATE_RFC850';
		$expected = date("l, d-M-y H:i:s \U\T\C", $time);
		$this->assertEquals($expected, standard_date($format, $time));
	}

	// ------------------------------------------------------------------------

	public function test_standard_date_rfc1036()
	{
		$time = time();
		$format = 'DATE_RFC1036';
		$expected = date("D, d M y H:i:s O", $time);
		$this->assertEquals($expected, standard_date($format, $time));
	}

	// ------------------------------------------------------------------------

	public function test_standard_date_rfc1123()
	{
		$time = time();
		$format = 'DATE_RFC1123';
		$expected = date("D, d M Y H:i:s O", $time);
		$this->assertEquals($expected, standard_date($format, $time));
	}

	// ------------------------------------------------------------------------

	public function test_standard_date_rfc2822()
	{
		$time = time();
		$format = 'DATE_RFC2822';
		$expected = date("D, d M Y H:i:s O", $time);
		$this->assertEquals($expected, standard_date($format, $time));
	}

	// ------------------------------------------------------------------------

	public function test_standard_date_rss()
	{
		$time = time();
		$format = 'DATE_RSS';
		$expected = date("D, d M Y H:i:s O", $time);
		$this->assertEquals($expected, standard_date($format, $time));
	}

	// ------------------------------------------------------------------------

	public function test_standard_date_w3c()
	{
		$time = time();
		$format = 'DATE_W3C';
		$expected = date("Y-m-d\TH:i:sO", $time);
		$this->assertEquals($expected, standard_date($format, $time));
	}

	// ------------------------------------------------------------------------

	public function test_timespan()
	{
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
		$t = time();
		$expected = mktime(gmdate("H", $t), gmdate("i", $t), gmdate("s", $t), gmdate("m", $t), gmdate("d", $t), gmdate("Y", $t));
		$this->assertEquals($expected, local_to_gmt($t));
	}

	// ------------------------------------------------------------------------

	public function test_gmt_to_local()
	{
		$timestamp = '1140153693';
		$timezone = 'UM8';
		$daylight_saving = TRUE;

		$this->assertEquals(1140128493, gmt_to_local($timestamp, $timezone, $daylight_saving));
	}

	// ------------------------------------------------------------------------

	public function test_mysql_to_unix()
	{
		$time = time();
		$this->assertEquals($time, 
				mysql_to_unix(date("Y-m-d H:i:s", $time)));
	}

	// ------------------------------------------------------------------------

	public function test_unix_to_human()
	{
		$time = time();
		$this->assertEquals(date("Y-m-d h:i A", $time), unix_to_human($time));
		$this->assertEquals(date("Y-m-d h:i:s A", $time), unix_to_human($time, TRUE, 'us'));
		$this->assertEquals(date("Y-m-d H:i:s", $time), unix_to_human($time, TRUE, 'eu'));
	}

	// ------------------------------------------------------------------------

	public function test_human_to_unix()
	{
		$date = '2000-12-31 10:00:00 PM';
		$expected = strtotime($date);
		$this->assertEquals($expected, human_to_unix($date));
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
		$this->assertEquals(0, timezones('non_existant'));
	}
}

/* End of file date_helper_test.php */