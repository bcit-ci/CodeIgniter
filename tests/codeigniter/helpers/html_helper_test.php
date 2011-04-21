<?php

require_once(BASEPATH.'helpers/html_helper.php');

class Html_helper_test extends CI_TestCase
{
	public function testHeading()
	{
		$this->assertEquals('<h1>foobar</h1>', heading('foobar'));
	}

	// ------------------------------------------------------------------------
	
	public function testUl()
	{
		$expect = <<<EOH
<ul>
  <li>foo</li>
  <li>bar</li>
</ul>

EOH;

		$expect = ltrim($expect);

		$list = array('foo', 'bar');
		
		$this->assertEquals($expect, ul($list));


		$expect = <<<EOH
<ul class="test">
  <li>foo</li>
  <li>bar</li>
</ul>

EOH;

		$expect = ltrim($expect);

		$list = array('foo', 'bar');

		$this->assertEquals($expect, ul($list, ' class="test"'));

		$this->assertEquals($expect, ul($list, array('class' => 'test')));
	}
	
	// ------------------------------------------------------------------------

	public function testNBS()
	{
		$this->assertEquals('&nbsp;&nbsp;&nbsp;', nbs(3));
	}

	// ------------------------------------------------------------------------
	
	public function testMeta()
	{
		$this->assertEquals("<meta name=\"test\" content=\"foo\" />\n", meta('test', 'foo'));
		
		$expect = "<meta name=\"foo\" content=\"\" />\n";
		
		$this->assertEquals($expect, meta(array('name' => 'foo')));
		
	}
	
}