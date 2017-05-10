<?php

class Typography_test extends CI_TestCase {

	public function set_up()
	{
		$this->type = new CI_Typography();
		$this->ci_instance('type', $this->type);
	}

	// --------------------------------------------------------------------

	/**
	 * Tests the format_characters() function.
	 *
	 * this can and should grow.
	 */
	public function test_format_characters()
	{
		$strs = array(
			'"double quotes"' 				=> '&#8220;double quotes&#8221;',
			'"testing" in "theory" that is' => '&#8220;testing&#8221; in &#8220;theory&#8221; that is',
			"Here's what I'm" 				=> 'Here&#8217;s what I&#8217;m',
			'&' 							=> '&amp;',
			'&amp;' 						=> '&amp;',
			'&nbsp;'						=> '&nbsp;',
			'--'							=> '&#8212;',
			'foo...'						=> 'foo&#8230;',
			'foo..'							=> 'foo..',
			'foo...bar.'					=> 'foo&#8230;bar.',
			'test.  new'					=> 'test.&nbsp; new',
		);

		foreach ($strs as $str => $expected)
		{
			$this->assertEquals($expected, $this->type->format_characters($str));
		}
	}

	// --------------------------------------------------------------------

	public function test_nl2br_except_pre()
	{
		$str = <<<EOH
Hello, I'm a happy string with some new lines.  

I like to skip.

Jump

and sing.

<pre>
I am inside a pre tag.  Please don't mess with me.

k?
</pre>

That's my story and I'm sticking to it.

The End.
EOH;

		$expected = <<<EOH
Hello, I'm a happy string with some new lines.  <br />
<br />
I like to skip.<br />
<br />
Jump<br />
<br />
and sing.<br />
<br />
<pre>
I am inside a pre tag.  Please don't mess with me.

k?
</pre><br />
<br />
That's my story and I'm sticking to it.<br />
<br />
The End.
EOH;

		$this->assertEquals($expected, $this->type->nl2br_except_pre($str));
	}

	// --------------------------------------------------------------------

	public function test_auto_typography()
	{
		$this->_blank_string();
		$this->_standardize_new_lines();
		$this->_reduce_linebreaks();
		$this->_remove_comments();
		$this->_protect_pre();
		$this->_no_opening_block();
		$this->_protect_braced_quotes();
	}

	// --------------------------------------------------------------------

	private function _blank_string()
	{
		// Test blank string
		$this->assertEquals('', $this->type->auto_typography(''));
	}

	// --------------------------------------------------------------------

	private function _standardize_new_lines()
	{
		$strs = array(
			"My string\rhas return characters"	=> "<p>My string<br />\nhas return characters</p>",
			'This one does not!' 				=> '<p>This one does not!</p>'
		);

		foreach ($strs as $str => $expect)
		{
			$this->assertEquals($expect, $this->type->auto_typography($str));
		}
	}

	// --------------------------------------------------------------------

	private function _reduce_linebreaks()
	{
		$str = "This has way too many linebreaks.\n\n\n\nSee?";
		$expect = "<p>This has way too many linebreaks.</p>\n\n<p>See?</p>";

		$this->assertEquals($expect, $this->type->auto_typography($str, TRUE));
	}

	// --------------------------------------------------------------------

	private function _remove_comments()
	{
		$str = '<!-- I can haz comments? -->  But no!';
		$expect = '<p><!-- I can haz comments? -->&nbsp; But no!</p>';

		$this->assertEquals($expect, $this->type->auto_typography($str));
	}

	// --------------------------------------------------------------------

	private function _protect_pre()
	{
		$str = '<p>My Sentence</p><pre>var_dump($this);</pre>';
		$expect = '<p>My Sentence</p><pre>var_dump($this);</pre>';

		$this->assertEquals($expect, $this->type->auto_typography($str));
	}

	// --------------------------------------------------------------------

	private function _no_opening_block()
	{
		$str = 'My Sentence<pre>var_dump($this);</pre>';
		$expect = '<p>My Sentence</p><pre>var_dump($this);</pre>';

		$this->assertEquals($expect, $this->type->auto_typography($str));
	}

	// --------------------------------------------------------------------

	public function _protect_braced_quotes()
	{
		$this->type->protect_braced_quotes = TRUE;

		$str = 'Test {parse="foobar"}';
		$expect = '<p>Test {parse="foobar"}</p>';

		$this->assertEquals($expect, $this->type->auto_typography($str));

		$this->type->protect_braced_quotes = FALSE;

		$str = 'Test {parse="foobar"}';
		$expect = '<p>Test {parse=&#8220;foobar&#8221;}</p>';

		$this->assertEquals($expect, $this->type->auto_typography($str));
	}

}