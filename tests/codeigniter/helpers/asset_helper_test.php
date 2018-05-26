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
		$this->ci_set_config('base_url', 'http://localhost/');

		$asset_directory = 'assets_test';

		if (! is_dir($asset_directory))
		{
			mkdir($asset_directory, 0755, true);
		}

		file_put_contents($asset_directory . '/test.css', '');

		$time = filemtime($asset_directory . '/test.css');

		$this->assertEquals(
			'http://localhost/assets_test/test.css?v=' . $time,
			asset_version('assets_test/test.css')
		);

		unlink($asset_directory . '/test.css');

		rmdir($asset_directory);
	}

}
