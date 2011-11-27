<?php

require_once(BASEPATH.'helpers/url_helper.php');

class Url_helper_test extends CI_TestCase
{
	public function test_url_title()
	{
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

	public function test_url_title_extra_dashes()
	{
		$words = array(
			'_foo bar_' 	=> 'foo_bar',
			'_What\'s wrong with CSS?_' => 'Whats_wrong_with_CSS'
		);

		foreach ($words as $in => $out)
		{
			$this->assertEquals($out, url_title($in, 'underscore'));
		}
	}
}