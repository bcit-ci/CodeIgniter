<?php

class Utf8_test extends CI_TestCase {

	public function set_up()
	{
		$this->utf8 = new Mock_Core_Utf8();
	}

	// --------------------------------------------------------------------

	public function test_convert_to_utf8()
	{
		$this->assertEquals(
			$this->utf8->convert_to_utf8('тест', 'WINDOWS-1251'),
			'С‚РµСЃС‚'
		);
	}

}