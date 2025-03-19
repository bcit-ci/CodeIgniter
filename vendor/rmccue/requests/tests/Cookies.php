<?php

class RequestsTest_Cookies extends PHPUnit_Framework_TestCase {
	public function testBasicCookie() {
		$cookie = new Requests_Cookie('requests-testcookie', 'testvalue');

		$this->assertEquals('requests-testcookie', $cookie->name);
		$this->assertEquals('testvalue', $cookie->value);
		$this->assertEquals('testvalue', (string) $cookie);

		$this->assertEquals('requests-testcookie=testvalue', $cookie->formatForHeader());
		$this->assertEquals('requests-testcookie=testvalue', $cookie->formatForSetCookie());
	}

	public function testCookieWithAttributes() {
		$attributes = array(
			'httponly',
			'path' => '/'
		);
		$cookie = new Requests_Cookie('requests-testcookie', 'testvalue', $attributes);

		$this->assertEquals('requests-testcookie=testvalue', $cookie->formatForHeader());
		$this->assertEquals('requests-testcookie=testvalue; httponly; path=/', $cookie->formatForSetCookie());
	}

	public function testEmptyCookieName() {
		$cookie = Requests_Cookie::parse('test');
		$this->assertEquals('', $cookie->name);
		$this->assertEquals('test', $cookie->value);
	}

	public function testEmptyAttributes() {
		$cookie = Requests_Cookie::parse('foo=bar; HttpOnly');
		$this->assertTrue($cookie->attributes['httponly']);
	}

	public function testCookieJarSetter() {
		$jar1 = new Requests_Cookie_Jar();
		$jar1['requests-testcookie'] = 'testvalue';

		$jar2 = new Requests_Cookie_Jar(array(
			'requests-testcookie' => 'testvalue',
		));
		$this->assertEquals($jar1, $jar2);
	}

	public function testCookieJarUnsetter() {
		$jar = new Requests_Cookie_Jar();
		$jar['requests-testcookie'] = 'testvalue';

		$this->assertEquals('testvalue', $jar['requests-testcookie']);

		unset($jar['requests-testcookie']);
		$this->assertEmpty($jar['requests-testcookie']);
		$this->assertFalse(isset($jar['requests-testcookie']));
	}

	/**
	 * @expectedException Requests_Exception
	 */
	public function testCookieJarAsList() {
		$cookies = new Requests_Cookie_Jar();
		$cookies[] = 'requests-testcookie1=testvalue1';
	}

	public function testCookieJarIterator() {
		$cookies = array(
			'requests-testcookie1' => 'testvalue1',
			'requests-testcookie2' => 'testvalue2',
		);
		$jar = new Requests_Cookie_Jar($cookies);

		foreach ($jar as $key => $value) {
			$this->assertEquals($cookies[$key], $value);
		}
	}

	public function testReceivingCookies() {
		$options = array(
			'follow_redirects' => false,
		);
		$url = 'http://httpbin.org/cookies/set?requests-testcookie=testvalue';

		$response = Requests::get($url, array(), $options);

		$cookie = $response->cookies['requests-testcookie'];
		$this->assertNotEmpty( $cookie );
		$this->assertEquals( 'testvalue', $cookie->value );
	}

	public function testPersistenceOnRedirect() {
		$options = array(
			'follow_redirects' => true,
		);
		$url = 'http://httpbin.org/cookies/set?requests-testcookie=testvalue';

		$response = Requests::get($url, array(), $options);

		$cookie = $response->cookies['requests-testcookie'];
		$this->assertNotEmpty( $cookie );
		$this->assertEquals( 'testvalue', $cookie->value );
	}

	protected function setCookieRequest($cookies) {
		$options = array(
			'cookies' => $cookies,
		);
		$response = Requests::get('http://httpbin.org/cookies/set', array(), $options);

		$data = json_decode($response->body, true);
		$this->assertInternalType('array', $data);
		$this->assertArrayHasKey('cookies', $data);
		return $data['cookies'];
	}

	public function testSendingCookie() {
		$cookies = array(
			'requests-testcookie1' => 'testvalue1',
		);

		$data = $this->setCookieRequest($cookies);

		$this->assertArrayHasKey('requests-testcookie1', $data);
		$this->assertEquals('testvalue1', $data['requests-testcookie1']);
	}

	public function testSendingCookieWithJar() {
		$cookies = new Requests_Cookie_Jar(array(
			'requests-testcookie1' => 'testvalue1',
		));
		$data = $this->setCookieRequest($cookies);

		$this->assertArrayHasKey('requests-testcookie1', $data);
		$this->assertEquals('testvalue1', $data['requests-testcookie1']);
	}

	public function testSendingMultipleCookies() {
		$cookies = array(
			'requests-testcookie1' => 'testvalue1',
			'requests-testcookie2' => 'testvalue2',
		);
		$data = $this->setCookieRequest($cookies);

		$this->assertArrayHasKey('requests-testcookie1', $data);
		$this->assertEquals('testvalue1', $data['requests-testcookie1']);

		$this->assertArrayHasKey('requests-testcookie2', $data);
		$this->assertEquals('testvalue2', $data['requests-testcookie2']);
	}

	public function testSendingMultipleCookiesWithJar() {
		$cookies = new Requests_Cookie_Jar(array(
			'requests-testcookie1' => 'testvalue1',
			'requests-testcookie2' => 'testvalue2',
		));
		$data = $this->setCookieRequest($cookies);

		$this->assertArrayHasKey('requests-testcookie1', $data);
		$this->assertEquals('testvalue1', $data['requests-testcookie1']);

		$this->assertArrayHasKey('requests-testcookie2', $data);
		$this->assertEquals('testvalue2', $data['requests-testcookie2']);
	}

	public function testSendingPrebakedCookie() {
		$cookies = new Requests_Cookie_Jar(array(
			new Requests_Cookie('requests-testcookie', 'testvalue'),
		));
		$data = $this->setCookieRequest($cookies);

		$this->assertArrayHasKey('requests-testcookie', $data);
		$this->assertEquals('testvalue', $data['requests-testcookie']);
	}
}