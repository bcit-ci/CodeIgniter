<?php

class Language_helper_test extends CI_TestCase {

	public function test_lang()
	{
		$this->helper('language');
		$lang = $this->getMock('CI_Lang', array('line'));
		$lang->expects($this->any())->method('line')->will($this->returnValue(FALSE));
		$this->ci_instance_var('lang', $lang);

		$this->assertFalse(lang(1));
		$this->assertEquals('<label for="foo" class="bar"></label>', lang(1, 'foo', array('class' => 'bar')));
	}

}