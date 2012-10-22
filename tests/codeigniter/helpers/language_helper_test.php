<?php

class Language_helper_test extends CI_TestCase {

	public function test_lang()
	{
		$this->helper('language');
		$this->ci_instance_var('lang', new Mock_Core_Lang());

		$this->assertFalse(lang(1));
		$this->assertEquals('<label for="foo"></label>', lang(1, 'foo'));
	}

}