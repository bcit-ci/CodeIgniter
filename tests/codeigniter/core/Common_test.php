<?php

class Common_test extends CI_TestCase {

	// ------------------------------------------------------------------------

	public function test_is_php()
	{
		$this->assertEquals(TRUE, is_php('1.2.0'));
		$this->assertEquals(FALSE, is_php('9999.9.9'));
	}

	// ------------------------------------------------------------------------

	public function test_stringify_attributes()
	{
		$this->assertEquals(' class="foo" id="bar"', _stringify_attributes(array('class' => 'foo', 'id' => 'bar')));

		$atts = new Stdclass;
		$atts->class = 'foo';
		$atts->id = 'bar';
		$this->assertEquals(' class="foo" id="bar"', _stringify_attributes($atts));

		$atts = new Stdclass;
		$this->assertEquals('', _stringify_attributes($atts));

		$this->assertEquals(' class="foo" id="bar"', _stringify_attributes('class="foo" id="bar"'));

		$this->assertEquals('', _stringify_attributes(array()));
	}

	// ------------------------------------------------------------------------

	public function test_stringify_js_attributes()
	{
		$this->assertEquals('width=800,height=600', _stringify_attributes(array('width' => '800', 'height' => '600'), TRUE));

		$atts = new Stdclass;
		$atts->width = 800;
		$atts->height = 600;
		$this->assertEquals('width=800,height=600', _stringify_attributes($atts, TRUE));
	}

}