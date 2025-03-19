<?php

/**
 * IRI test cases
 *
 * Copyright (c) 2008-2010 Geoffrey Sneddon.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * 	* Redistributions of source code must retain the above copyright notice,
 *	  this list of conditions and the following disclaimer.
 *
 * 	* Redistributions in binary form must reproduce the above copyright notice,
 *	  this list of conditions and the following disclaimer in the documentation
 *	  and/or other materials provided with the distribution.
 *
 * 	* Neither the name of the SimplePie Team nor the names of its contributors
 *	  may be used to endorse or promote products derived from this software
 *	  without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDERS AND CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package IRI
 * @author Geoffrey Sneddon
 * @copyright 2008-2010 Geoffrey Sneddon
 * @license http://www.opensource.org/licenses/bsd-license.php
 * @link http://hg.gsnedders.com/iri/
 *
 */

class RequestsTest_IRI extends PHPUnit_Framework_TestCase
{
	public static function rfc3986_tests()
	{
		return array(
			// Normal
			array('g:h', 'g:h'),
			array('g', 'http://a/b/c/g'),
			array('./g', 'http://a/b/c/g'),
			array('g/', 'http://a/b/c/g/'),
			array('/g', 'http://a/g'),
			array('//g', 'http://g'),
			array('?y', 'http://a/b/c/d;p?y'),
			array('g?y', 'http://a/b/c/g?y'),
			array('#s', 'http://a/b/c/d;p?q#s'),
			array('g#s', 'http://a/b/c/g#s'),
			array('g?y#s', 'http://a/b/c/g?y#s'),
			array(';x', 'http://a/b/c/;x'),
			array('g;x', 'http://a/b/c/g;x'),
			array('g;x?y#s', 'http://a/b/c/g;x?y#s'),
			array('', 'http://a/b/c/d;p?q'),
			array('.', 'http://a/b/c/'),
			array('./', 'http://a/b/c/'),
			array('..', 'http://a/b/'),
			array('../', 'http://a/b/'),
			array('../g', 'http://a/b/g'),
			array('../..', 'http://a'),
			array('../../', 'http://a'),
			array('../../g', 'http://a/g'),
			// Abnormal
			array('../../../g', 'http://a/g'),
			array('../../../../g', 'http://a/g'),
			array('/./g', 'http://a/g'),
			array('/../g', 'http://a/g'),
			array('g.', 'http://a/b/c/g.'),
			array('.g', 'http://a/b/c/.g'),
			array('g..', 'http://a/b/c/g..'),
			array('..g', 'http://a/b/c/..g'),
			array('./../g', 'http://a/b/g'),
			array('./g/.', 'http://a/b/c/g/'),
			array('g/./h', 'http://a/b/c/g/h'),
			array('g/../h', 'http://a/b/c/h'),
			array('g;x=1/./y', 'http://a/b/c/g;x=1/y'),
			array('g;x=1/../y', 'http://a/b/c/y'),
			array('g?y/./x', 'http://a/b/c/g?y/./x'),
			array('g?y/../x', 'http://a/b/c/g?y/../x'),
			array('g#s/./x', 'http://a/b/c/g#s/./x'),
			array('g#s/../x', 'http://a/b/c/g#s/../x'),
			array('http:g', 'http:g'),
		);
	}

	/**
	 * @dataProvider rfc3986_tests
	 */
	public function testStringRFC3986($relative, $expected)
	{
		$base = new Requests_IRI('http://a/b/c/d;p?q');
		$this->assertEquals($expected, Requests_IRI::absolutize($base, $relative)->iri);
		$this->assertEquals($expected, (string) Requests_IRI::absolutize($base, $relative));
	}

	/**
	 * @dataProvider rfc3986_tests
	 */
	public function testBothStringRFC3986($relative, $expected)
	{
		$base = 'http://a/b/c/d;p?q';
		$this->assertEquals($expected, Requests_IRI::absolutize($base, $relative)->iri);
		$this->assertEquals($expected, (string) Requests_IRI::absolutize($base, $relative));
	}

	/**
	 * @dataProvider rfc3986_tests
	 */
	public function testObjectRFC3986($relative, $expected)
	{
		$base = new Requests_IRI('http://a/b/c/d;p?q');
		$expected = new Requests_IRI($expected);
		$this->assertEquals($expected, Requests_IRI::absolutize($base, $relative));
	}

	public static function sp_tests()
	{
		return array(
			array('http://a/b/c/d', 'f%0o', 'http://a/b/c/f%250o'),
			array('http://a/b/', 'c', 'http://a/b/c'),
			array('http://a/', 'b', 'http://a/b'),
			array('http://a/', '/b', 'http://a/b'),
			array('http://a/b', 'c', 'http://a/c'),
			array('http://a/b/', "c\x0Ad", 'http://a/b/c%0Ad'),
			array('http://a/b/', "c\x0A\x0B", 'http://a/b/c%0A%0B'),
			array('http://a/b/c', '//0', 'http://0'),
			array('http://a/b/c', '0', 'http://a/b/0'),
			array('http://a/b/c', '?0', 'http://a/b/c?0'),
			array('http://a/b/c', '#0', 'http://a/b/c#0'),
			array('http://0/b/c', 'd', 'http://0/b/d'),
			array('http://a/b/c?0', 'd', 'http://a/b/d'),
			array('http://a/b/c#0', 'd', 'http://a/b/d'),
			array('http://example.com', '//example.net', 'http://example.net'),
			array('http:g', 'a', 'http:a'),
		);
	}

	/**
	 * @dataProvider sp_tests
	 */
	public function testStringSP($base, $relative, $expected)
	{
		$base = new Requests_IRI($base);
		$this->assertEquals($expected, Requests_IRI::absolutize($base, $relative)->iri);
		$this->assertEquals($expected, (string) Requests_IRI::absolutize($base, $relative));
	}

	/**
	 * @dataProvider sp_tests
	 */
	public function testObjectSP($base, $relative, $expected)
	{
		$base = new Requests_IRI($base);
		$expected = new Requests_IRI($expected);
		$this->assertEquals($expected, Requests_IRI::absolutize($base, $relative));
	}

	public static function absolutize_tests()
	{
		return array(
			array('http://example.com/', 'foo/111:bar', 'http://example.com/foo/111:bar'),
			array('http://example.com/#foo', '', 'http://example.com'),
		);
	}

	/**
	 * @dataProvider absolutize_tests
	 */
	public function testAbsolutizeString($base, $relative, $expected)
	{
		$base = new Requests_IRI($base);
		$this->assertEquals($expected, Requests_IRI::absolutize($base, $relative)->iri);
	}

	/**
	 * @dataProvider absolutize_tests
	 */
	public function testAbsolutizeObject($base, $relative, $expected)
	{
		$base = new Requests_IRI($base);
		$expected = new Requests_IRI($expected);
		$this->assertEquals($expected, Requests_IRI::absolutize($base, $relative));
	}

	public static function normalization_tests()
	{
		return array(
			array('example://a/b/c/%7Bfoo%7D', 'example://a/b/c/%7Bfoo%7D'),
			array('eXAMPLE://a/./b/../b/%63/%7bfoo%7d', 'example://a/b/c/%7Bfoo%7D'),
			array('example://%61/', 'example://a/'),
			array('example://%41/', 'example://a/'),
			array('example://A/', 'example://a/'),
			array('example://a/', 'example://a/'),
			array('example://%25A/', 'example://%25a/'),
			array('HTTP://EXAMPLE.com/', 'http://example.com'),
			array('http://example.com/', 'http://example.com'),
			array('http://example.com:', 'http://example.com'),
			array('http://example.com:80', 'http://example.com'),
			array('http://@example.com', 'http://@example.com'),
			array('http://', 'http://'),
			array('http://example.com?', 'http://example.com?'),
			array('http://example.com#', 'http://example.com#'),
			array('https://example.com/', 'https://example.com'),
			array('https://example.com:', 'https://example.com'),
			array('https://@example.com', 'https://@example.com'),
			array('https://example.com?', 'https://example.com?'),
			array('https://example.com#', 'https://example.com#'),
			array('file://localhost/foobar', 'file:/foobar'),
			array('http://[0:0:0:0:0:0:0:1]', 'http://[::1]'),
			array('http://[2001:db8:85a3:0000:0000:8a2e:370:7334]', 'http://[2001:db8:85a3::8a2e:370:7334]'),
			array('http://[0:0:0:0:0:ffff:c0a8:a01]', 'http://[::ffff:c0a8:a01]'),
			array('http://[ffff:0:0:0:0:0:0:0]', 'http://[ffff::]'),
			array('http://[::ffff:192.0.2.128]', 'http://[::ffff:192.0.2.128]'),
			array('http://[invalid]', 'http:'),
			array('http://[0:0:0:0:0:0:0:1]:', 'http://[::1]'),
			array('http://[0:0:0:0:0:0:0:1]:80', 'http://[::1]'),
			array('http://[0:0:0:0:0:0:0:1]:1234', 'http://[::1]:1234'),
			// Punycode decoding helps with normalisation of IRIs, but is not
			// needed for URIs, so we don't really care about it for Requests
			//array('http://xn--tdali-d8a8w.lv', 'http://tūdaliņ.lv'),
			//array('http://t%C5%ABdali%C5%86.lv', 'http://tūdaliņ.lv'),
			array('http://Aa@example.com', 'http://Aa@example.com'),
			array('http://example.com?Aa', 'http://example.com?Aa'),
			array('http://example.com/Aa', 'http://example.com/Aa'),
			array('http://example.com#Aa', 'http://example.com#Aa'),
			array('http://[0:0:0:0:0:0:0:0]', 'http://[::]'),
			array('http:.', 'http:'),
			array('http:..', 'http:'),
			array('http:./', 'http:'),
			array('http:../', 'http:'),
			array('http://example.com/%3A', 'http://example.com/%3A'),
			array('http://example.com/:', 'http://example.com/:'),
			array('http://example.com/%C2', 'http://example.com/%C2'),
			array('http://example.com/%C2a', 'http://example.com/%C2a'),
			array('http://example.com/%C2%00', 'http://example.com/%C2%00'),
			array('http://example.com/%C3%A9', 'http://example.com/é'),
			array('http://example.com/%C3%A9%00', 'http://example.com/é%00'),
			array('http://example.com/%C3%A9cole', 'http://example.com/école'),
			array('http://example.com/%FF', 'http://example.com/%FF'),
			array("http://example.com/\xF3\xB0\x80\x80", 'http://example.com/%F3%B0%80%80'),
			array("http://example.com/\xF3\xB0\x80\x80%00", 'http://example.com/%F3%B0%80%80%00'),
			array("http://example.com/\xF3\xB0\x80\x80a", 'http://example.com/%F3%B0%80%80a'),
			array("http://example.com?\xF3\xB0\x80\x80", "http://example.com?\xF3\xB0\x80\x80"),
			array("http://example.com?\xF3\xB0\x80\x80%00", "http://example.com?\xF3\xB0\x80\x80%00"),
			array("http://example.com?\xF3\xB0\x80\x80a", "http://example.com?\xF3\xB0\x80\x80a"),
			array("http://example.com/\xEE\x80\x80", 'http://example.com/%EE%80%80'),
			array("http://example.com/\xEE\x80\x80%00", 'http://example.com/%EE%80%80%00'),
			array("http://example.com/\xEE\x80\x80a", 'http://example.com/%EE%80%80a'),
			array("http://example.com?\xEE\x80\x80", "http://example.com?\xEE\x80\x80"),
			array("http://example.com?\xEE\x80\x80%00", "http://example.com?\xEE\x80\x80%00"),
			array("http://example.com?\xEE\x80\x80a", "http://example.com?\xEE\x80\x80a"),
			array("http://example.com/\xC2", 'http://example.com/%C2'),
			array("http://example.com/\xC2a", 'http://example.com/%C2a'),
			array("http://example.com/\xC2\x00", 'http://example.com/%C2%00'),
			array("http://example.com/\xC3\xA9", 'http://example.com/é'),
			array("http://example.com/\xC3\xA9\x00", 'http://example.com/é%00'),
			array("http://example.com/\xC3\xA9cole", 'http://example.com/école'),
			array("http://example.com/\xFF", 'http://example.com/%FF'),
			array("http://example.com/\xFF%00", 'http://example.com/%FF%00'),
			array("http://example.com/\xFFa", 'http://example.com/%FFa'),
			array('http://example.com/%61', 'http://example.com/a'),
			array('http://example.com?%26', 'http://example.com?%26'),
			array('http://example.com?%61', 'http://example.com?a'),
			array('///', '///'),
		);
	}

	/**
	 * @dataProvider normalization_tests
	 */
	public function testStringNormalization($input, $output)
	{
		$input = new Requests_IRI($input);
		$this->assertEquals($output, $input->iri);
		$this->assertEquals($output, (string) $input);
	}

	/**
	 * @dataProvider normalization_tests
	 */
	public function testObjectNormalization($input, $output)
	{
		$input = new Requests_IRI($input);
		$output = new Requests_IRI($output);
		$this->assertEquals($output, $input);
	}

	public static function equivalence_tests()
	{
		return array(
			array('http://É.com', 'http://%C3%89.com'),
		);
	}

	/**
	 * @dataProvider equivalence_tests
	 */
	public function testObjectEquivalence($input, $output)
	{
		$input = new Requests_IRI($input);
		$output = new Requests_IRI($output);
		$this->assertEquals($output, $input);
	}

	public static function not_equivalence_tests()
	{
		return array(
			array('http://example.com/foo/bar', 'http://example.com/foo%2Fbar'),
		);
	}

	/**
	 * @dataProvider not_equivalence_tests
	 */
	public function testObjectNotEquivalence($input, $output)
	{
		$input = new Requests_IRI($input);
		$output = new Requests_IRI($output);
		$this->assertNotEquals($output, $input);
	}

	public function testInvalidAbsolutizeBase()
	{
		$this->assertFalse(Requests_IRI::absolutize('://not a URL', '../'));
	}

	public function testInvalidAbsolutizeRelative()
	{
		$this->assertFalse(Requests_IRI::absolutize('http://example.com/', 'http://example.com//not a URL'));
	}

	public function testFullGamut()
	{
		$iri = new Requests_IRI();
		$iri->scheme = 'http';
		$iri->userinfo = 'user:password';
		$iri->host = 'example.com';
		$iri->path = '/test/';
		$iri->fragment = 'test';

		$this->assertEquals('http', $iri->scheme);
		$this->assertEquals('user:password', $iri->userinfo);
		$this->assertEquals('example.com', $iri->host);
		$this->assertEquals(80, $iri->port);
		$this->assertEquals('/test/', $iri->path);
		$this->assertEquals('test', $iri->fragment);
	}

	public function testReadAliased()
	{
		$iri = new Requests_IRI();
		$iri->scheme = 'http';
		$iri->userinfo = 'user:password';
		$iri->host = 'example.com';
		$iri->path = '/test/';
		$iri->fragment = 'test';

		$this->assertEquals('http', $iri->ischeme);
		$this->assertEquals('user:password', $iri->iuserinfo);
		$this->assertEquals('example.com', $iri->ihost);
		$this->assertEquals(80, $iri->iport);
		$this->assertEquals('/test/', $iri->ipath);
		$this->assertEquals('test', $iri->ifragment);
	}

	public function testWriteAliased()
	{
		$iri = new Requests_IRI();
		$iri->scheme = 'http';
		$iri->iuserinfo = 'user:password';
		$iri->ihost = 'example.com';
		$iri->ipath = '/test/';
		$iri->ifragment = 'test';

		$this->assertEquals('http', $iri->scheme);
		$this->assertEquals('user:password', $iri->userinfo);
		$this->assertEquals('example.com', $iri->host);
		$this->assertEquals(80, $iri->port);
		$this->assertEquals('/test/', $iri->path);
		$this->assertEquals('test', $iri->fragment);
	}

	/**
	 * @expectedException PHPUnit_Framework_Error_Notice
	 */
	public function testNonexistantProperty()
	{
		$iri = new Requests_IRI();
		$this->assertFalse(isset($iri->nonexistant_prop));
		$should_fail = $iri->nonexistant_prop;
	}

	public function testBlankHost()
	{
		$iri = new Requests_IRI('http://example.com/a/?b=c#d');
		$iri->host = null;

		$this->assertEquals(null, $iri->host);
		$this->assertEquals('http:/a/?b=c#d', (string) $iri);
	}

	public function testBadPort()
	{
		$iri = new Requests_IRI();
		$iri->port = 'example';

		$this->assertEquals(null, $iri->port);
	}
}
