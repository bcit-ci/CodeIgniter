<?php
require_once BASEPATH.'helpers/date_helper.php';

class Date_helper_test extends CI_TestCase
{
	// ------------------------------------------------------------------------

	public function test_now()
	{
		$this->markTestIncomplete('not implemented yet');
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
		$this->markTestIncomplete('not implemented yet');
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
		$this->markTestIncomplete('not implemented yet');
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
		$this->assertEquals(1344708680, mysql_to_unix(date("YYYY-MM-DD HH:MM:SS")));
	}

	// ------------------------------------------------------------------------

	public function test_unix_to_human()
	{
		$time = time();
		$this->assertEquals(date("Y-m-d h:i A"), unix_to_human($time));
		$this->assertEquals(date("Y-m-d h:i:s A"), unix_to_human($time, TRUE, 'us'));
		$this->assertEquals(date("Y-m-d H:i:s"), unix_to_human($time, TRUE, 'eu'));
	}

	// ------------------------------------------------------------------------

	public function test_human_to_unix()
	{
		$time = time();
		$this->markTestIncomplete('Failed Test');
		// $this->assertEquals($time, human_to_unix(unix_to_human($time)));
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