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

}