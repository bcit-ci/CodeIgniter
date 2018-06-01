<?php

class Inflector_helper_test extends CI_TestCase {

	public function set_up()
	{
		$this->helper('inflector');
	}

	public function test_singular()
	{
		$strs = array(
			'tellies'      => 'telly',
			'smellies'     => 'smelly',
			'abjectnesses' => 'abjectness',
			'smells'       => 'smell',
			'equipment'    => 'equipment'
		);

		foreach ($strs as $str => $expect)
		{
			$this->assertEquals($expect, singular($str));
		}
	}

	// --------------------------------------------------------------------

	public function test_plural()
	{
		$strs = array(
			'telly'      => 'tellies',
			'smelly'     => 'smellies',
			'abjectness' => 'abjectnesses', // ref : https://en.wiktionary.org/wiki/abjectnesses
			'smell'      => 'smells',
			'witch'      => 'witches',
			'equipment'  => 'equipment'
		);

		foreach ($strs as $str => $expect)
		{
			$this->assertEquals($expect, plural($str));
		}
	}

	// --------------------------------------------------------------------

	public function test_camelize()
	{
		$strs = array(
			'this is the string'	=> 'thisIsTheString',
			'this is another one'   => 'thisIsAnotherOne',
			'i-am-playing-a-trick'  => 'i-am-playing-a-trick',
			'what_do_you_think-yo?' => 'whatDoYouThink-yo?',
		);

		foreach ($strs as $str => $expect)
		{
			$this->assertEquals($expect, camelize($str));
		}
	}

	// --------------------------------------------------------------------

	public function test_underscore()
	{
		$strs = array(
			'this is the string'    => 'this_is_the_string',
			'this is another one'   => 'this_is_another_one',
			'i-am-playing-a-trick'  => 'i-am-playing-a-trick',
			'what_do_you_think-yo?' => 'what_do_you_think-yo?',
		);

		foreach ($strs as $str => $expect)
		{
			$this->assertEquals($expect, underscore($str));
		}
	}

	// --------------------------------------------------------------------

	public function test_humanize()
	{
		$strs = array(
			'this_is_the_string'    => 'This Is The String',
			'this_is_another_one'   => 'This Is Another One',
			'i-am-playing-a-trick'  => 'I-am-playing-a-trick',
			'what_do_you_think-yo?' => 'What Do You Think-yo?',
		);

		foreach ($strs as $str => $expect)
		{
			$this->assertEquals($expect, humanize($str));
		}
	}

	// --------------------------------------------------------------------

	public function test_ordinal_format()
	{
		$strs = array(
			1                => '1st',
			2                => '2nd',
			4                => '4th',
			11               => '11th',
			12               => '12th',
			13               => '13th',
			'something else' => 'something else',
		);

		foreach ($strs as $str => $expect)
		{
			$this->assertEquals($expect, ordinal_format($str));
		}
	}
}