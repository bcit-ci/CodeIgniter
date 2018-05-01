<?php

class Asset_helper_test extends CI_TestCase {

	public function set_up()
	{
		$this->helper('asset');
	}

	/**
	* @runInSeparateProcess
	*/
	public function test_asset_version()
	{
		$this->ci_set_config('asset_version', 1);
		$this->ci_set_config('base_url', 'http://localhost/');

		$this->assertEquals(
			'http://localhost/assets/css/bootstrap.css?v=1',
			asset_version('assets/css/bootstrap.css')
		);
	}
}
