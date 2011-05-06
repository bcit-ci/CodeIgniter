<?php

require_once(BASEPATH.'helpers/xml_helper.php');

class Xml_helper_test extends CI_TestCase
{
	
	public function test_xml_convert()
	{
		$this->assertEquals('&lt;tag&gt;my &amp; test &#45; &lt;/tag&gt;', xml_convert('<tag>my & test - </tag>'));
	}
	
}