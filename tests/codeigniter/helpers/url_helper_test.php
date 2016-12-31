<?php

class Url_helper_test extends CI_TestCase {

	public function set_up()
	{
		$this->helper('url');
	}

	/**
	 * @runInSeparateProcess
	 */
	public function test_url_title()
	{
		define('UTF8_ENABLED', FALSE);

		$words = array(
			'foo bar /' 	=> 'foo-bar',
			'\  testing 12' => 'testing-12'
		);

		foreach ($words as $in => $out)
		{
			$this->assertEquals($out, url_title($in, 'dash', TRUE));
		}
	}

	// --------------------------------------------------------------------

	/**
	 * @runInSeparateProcess
	 */
	public function test_url_title_extra_dashes()
	{
		define('UTF8_ENABLED', FALSE);

		$words = array(
			'_foo bar_' 	=> 'foo_bar',
			'_What\'s wrong with CSS?_' => 'Whats_wrong_with_CSS'
		);

		foreach ($words as $in => $out)
		{
			$this->assertEquals($out, url_title($in, 'underscore'));
		}
	}

	// --------------------------------------------------------------------

	public function test_prep_url()
	{
		$this->assertEquals('http://codeigniter.com', prep_url('codeigniter.com'));
		$this->assertEquals('http://www.codeigniter.com', prep_url('www.codeigniter.com'));
	}

	// --------------------------------------------------------------------

	public function test_auto_link_url()
	{
		$strings = array(
			'www.codeigniter.com test' => '<a href="http://www.codeigniter.com">www.codeigniter.com</a> test',
			'This is my noreply@codeigniter.com test' => 'This is my noreply@codeigniter.com test',
			'<br />www.google.com' => '<br /><a href="http://www.google.com">www.google.com</a>',
			'Download CodeIgniter at www.codeigniter.com. Period test.' => 'Download CodeIgniter at <a href="http://www.codeigniter.com">www.codeigniter.com</a>. Period test.',
			'Download CodeIgniter at www.codeigniter.com, comma test' => 'Download CodeIgniter at <a href="http://www.codeigniter.com">www.codeigniter.com</a>, comma test',
			'This one: ://codeigniter.com must not break this one: http://codeigniter.com' => 'This one: <a href="://codeigniter.com">://codeigniter.com</a> must not break this one: <a href="http://codeigniter.com">http://codeigniter.com</a>'
		);

		foreach ($strings as $in => $out)
		{
			$this->assertEquals($out, auto_link($in, 'url'));
		}
	}

	// --------------------------------------------------------------------

	public function test_pull_675()
	{
		$strings = array(
			'<br />www.google.com' => '<br /><a href="http://www.google.com">www.google.com</a>',
		);

		foreach ($strings as $in => $out)
		{
			$this->assertEquals($out, auto_link($in, 'url'));
		}
	}

}
