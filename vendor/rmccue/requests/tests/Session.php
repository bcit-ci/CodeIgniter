<?php

class RequestsTest_Session extends PHPUnit_Framework_TestCase {
	public function testPropertyUsage() {
		$headers = array(
			'X-TestHeader' => 'testing',
			'X-TestHeader2' => 'requests-test'
		);
		$data = array(
			'testdata' => 'value1',
			'test2' => 'value2',
			'test3' => array(
				'foo' => 'bar',
				'abc' => 'xyz'
			)
		);
		$options = array(
			'testoption' => 'test',
			'foo' => 'bar'
		);

		$session = new Requests_Session('http://example.com/', $headers, $data, $options);
		$this->assertEquals('http://example.com/', $session->url);
		$this->assertEquals($headers, $session->headers);
		$this->assertEquals($data, $session->data);
		$this->assertEquals($options['testoption'], $session->options['testoption']);

		// Test via property access
		$this->assertEquals($options['testoption'], $session->testoption);

		// Test setting new property
		$session->newoption = 'foobar';
		$options['newoption'] = 'foobar';
		$this->assertEquals($options['newoption'], $session->options['newoption']);

		// Test unsetting property
		unset($session->newoption);
		$this->assertFalse(isset($session->newoption));

		// Update property
		$session->testoption = 'foobar';
		$options['testoption'] = 'foobar';
		$this->assertEquals($options['testoption'], $session->testoption);

		// Test getting invalid property
		$this->assertNull($session->invalidoption);
	}

	public function testURLResolution() {
		$session = new Requests_Session('http://httpbin.org/');

		// Set the cookies up
		$response = $session->get('/get');
		$this->assertTrue($response->success);
		$this->assertEquals('http://httpbin.org/get', $response->url);

		$data = json_decode($response->body, true);
		$this->assertNotNull($data);
		$this->assertArrayHasKey('url', $data);
		$this->assertEquals('http://httpbin.org/get', $data['url']);
	}

	public function testSharedCookies() {
		$session = new Requests_Session('http://httpbin.org/');

		$options = array(
			'follow_redirects' => false
		);
		$response = $session->get('/cookies/set?requests-testcookie=testvalue', array(), $options);
		$this->assertEquals(302, $response->status_code);

		// Check the cookies
		$response = $session->get('/cookies');
		$this->assertTrue($response->success);

		// Check the response
		$data = json_decode($response->body, true);
		$this->assertNotNull($data);
		$this->assertArrayHasKey('cookies', $data);

		$cookies = array(
			'requests-testcookie' => 'testvalue'
		);
		$this->assertEquals($cookies, $data['cookies']);
	}
}
