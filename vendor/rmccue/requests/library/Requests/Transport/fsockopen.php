<?php
/**
 * fsockopen HTTP transport
 *
 * @package Requests
 * @subpackage Transport
 */

/**
 * fsockopen HTTP transport
 *
 * @package Requests
 * @subpackage Transport
 */
class Requests_Transport_fsockopen implements Requests_Transport {
	/**
	 * Raw HTTP data
	 *
	 * @var string
	 */
	public $headers = '';

	/**
	 * Stream metadata
	 *
	 * @var array Associative array of properties, see {@see http://php.net/stream_get_meta_data}
	 */
	public $info;

	protected $connect_error = '';

	/**
	 * Perform a request
	 *
	 * @throws Requests_Exception On failure to connect to socket (`fsockopenerror`)
	 * @throws Requests_Exception On socket timeout (`timeout`)
	 *
	 * @param string $url URL to request
	 * @param array $headers Associative array of request headers
	 * @param string|array $data Data to send either as the POST body, or as parameters in the URL for a GET/HEAD
	 * @param array $options Request options, see {@see Requests::response()} for documentation
	 * @return string Raw HTTP result
	 */
	public function request($url, $headers = array(), $data = array(), $options = array()) {
		$options['hooks']->dispatch('fsockopen.before_request');

		$url_parts = parse_url($url);
		$host = $url_parts['host'];
		$context = stream_context_create();
		$verifyname = false;

		// HTTPS support
		if (isset($url_parts['scheme']) && strtolower($url_parts['scheme']) === 'https') {
			$remote_socket = 'ssl://' . $host;
			$url_parts['port'] = 443;

			$context_options = array(
				'verify_peer' => true,
				// 'CN_match' => $host,
				'capture_peer_cert' => true
			);
			$verifyname = true;

			// SNI, if enabled (OpenSSL >=0.9.8j)
			if (defined('OPENSSL_TLSEXT_SERVER_NAME') && OPENSSL_TLSEXT_SERVER_NAME) {
				$context_options['SNI_enabled'] = true;
				if (isset($options['verifyname']) && $options['verifyname'] === false) {
					$context_options['SNI_enabled'] = false;
				}
			}

			if (isset($options['verify'])) {
				if ($options['verify'] === false) {
					$context_options['verify_peer'] = false;
				} elseif (is_string($options['verify'])) {
					$context_options['cafile'] = $options['verify'];
				}
			}

			if (isset($options['verifyname']) && $options['verifyname'] === false) {
				$verifyname = false;
			}

			stream_context_set_option($context, array('ssl' => $context_options));
		}
		else {
			$remote_socket = 'tcp://' . $host;
		}

		$proxy = isset( $options['proxy'] );
		$proxy_auth = $proxy && isset( $options['proxy_username'] ) && isset( $options['proxy_password'] );

		if (!isset($url_parts['port'])) {
			$url_parts['port'] = 80;
		}
		$remote_socket .= ':' . $url_parts['port'];

		set_error_handler(array($this, 'connect_error_handler'), E_WARNING | E_NOTICE);

		$options['hooks']->dispatch('fsockopen.remote_socket', array(&$remote_socket));

		$fp = stream_socket_client($remote_socket, $errno, $errstr, $options['timeout'], STREAM_CLIENT_CONNECT, $context);

		restore_error_handler();

		if ($verifyname) {
			if (!$this->verify_certificate_from_context($host, $context)) {
				throw new Requests_Exception('SSL certificate did not match the requested domain name', 'ssl.no_match');
			}
		}

		if (!$fp) {
			if ($errno === 0) {
				// Connection issue
				throw new Requests_Exception(rtrim($this->connect_error), 'fsockopen.connect_error');
			}
			else {
				throw new Requests_Exception($errstr, 'fsockopenerror');
				return;
			}
		}

		$request_body = '';
		$out = '';
		switch ($options['type']) {
			case Requests::POST:
			case Requests::PUT:
			case Requests::PATCH:
				if (isset($url_parts['path'])) {
					$path = $url_parts['path'];
					if (isset($url_parts['query'])) {
						$path .= '?' . $url_parts['query'];
					}
				}
				else {
					$path = '/';
				}

				$options['hooks']->dispatch( 'fsockopen.remote_host_path', array( &$path, $url ) );
				$out = $options['type'] . " $path HTTP/1.0\r\n";

				if (is_array($data)) {
					$request_body = http_build_query($data, null, '&');
				}
				else {
					$request_body = $data;
				}
				if (empty($headers['Content-Length'])) {
					$headers['Content-Length'] = strlen($request_body);
				}
				if (empty($headers['Content-Type'])) {
					$headers['Content-Type'] = 'application/x-www-form-urlencoded; charset=UTF-8';
				}
				break;
			case Requests::HEAD:
			case Requests::GET:
			case Requests::DELETE:
				$path = self::format_get($url_parts, $data);
				$options['hooks']->dispatch('fsockopen.remote_host_path', array(&$path, $url));
				$out = $options['type'] . " $path HTTP/1.0\r\n";
				break;
		}
		$out .= "Host: {$url_parts['host']}";

		if ($url_parts['port'] !== 80) {
			$out .= ":{$url_parts['port']}";
		}
		$out .= "\r\n";

		$out .= "User-Agent: {$options['useragent']}\r\n";
		$accept_encoding = $this->accept_encoding();
		if (!empty($accept_encoding)) {
			$out .= "Accept-Encoding: $accept_encoding\r\n";
		}

		$headers = Requests::flatten($headers);

		if (!empty($headers)) {
			$out .= implode($headers, "\r\n") . "\r\n";
		}

		$options['hooks']->dispatch('fsockopen.after_headers', array(&$out));

		if (substr($out, -2) !== "\r\n") {
			$out .= "\r\n";
		}

		$out .= "Connection: Close\r\n\r\n" . $request_body;

		$options['hooks']->dispatch('fsockopen.before_send', array(&$out));

		fwrite($fp, $out);
		$options['hooks']->dispatch('fsockopen.after_send', array(&$fake_headers));

		if (!$options['blocking']) {
			fclose($fp);
			$fake_headers = '';
			$options['hooks']->dispatch('fsockopen.after_request', array(&$fake_headers));
			return '';
		}
		stream_set_timeout($fp, $options['timeout']);

		$this->info = stream_get_meta_data($fp);

		$this->headers = '';
		$this->info = stream_get_meta_data($fp);
		if (!$options['filename']) {
			while (!feof($fp)) {
				$this->info = stream_get_meta_data($fp);
				if ($this->info['timed_out']) {
					throw new Requests_Exception('fsocket timed out', 'timeout');
				}

				$this->headers .= fread($fp, 1160);
			}
		}
		else {
			$download = fopen($options['filename'], 'wb');
			$doingbody = false;
			$response = '';
			while (!feof($fp)) {
				$this->info = stream_get_meta_data($fp);
				if ($this->info['timed_out']) {
					throw new Requests_Exception('fsocket timed out', 'timeout');
				}

				$block = fread($fp, 1160);
				if ($doingbody) {
					fwrite($download, $block);
				}
				else {
					$response .= $block;
					if (strpos($response, "\r\n\r\n")) {
						list($this->headers, $block) = explode("\r\n\r\n", $response, 2);
						$doingbody = true;
						fwrite($download, $block);
					}
				}
			}
			fclose($download);
		}
		fclose($fp);

		$options['hooks']->dispatch('fsockopen.after_request', array(&$this->headers));
		return $this->headers;
	}

	/**
	 * Send multiple requests simultaneously
	 *
	 * @param array $requests Request data (array of 'url', 'headers', 'data', 'options') as per {@see Requests_Transport::request}
	 * @param array $options Global options, see {@see Requests::response()} for documentation
	 * @return array Array of Requests_Response objects (may contain Requests_Exception or string responses as well)
	 */
	public function request_multiple($requests, $options) {
		$responses = array();
		$class = get_class($this);
		foreach ($requests as $id => $request) {
			try {
				$handler = new $class();
				$responses[$id] = $handler->request($request['url'], $request['headers'], $request['data'], $request['options']);

				$request['options']['hooks']->dispatch('transport.internal.parse_response', array(&$responses[$id], $request));
			}
			catch (Requests_Exception $e) {
				$responses[$id] = $e;
			}

			if (!is_string($responses[$id])) {
				$request['options']['hooks']->dispatch('multiple.request.complete', array(&$responses[$id], $id));
			}
		}

		return $responses;
	}

	/**
	 * Retrieve the encodings we can accept
	 *
	 * @return string Accept-Encoding header value
	 */
	protected static function accept_encoding() {
		$type = array();
		if (function_exists('gzinflate')) {
			$type[] = 'deflate;q=1.0';
		}

		if (function_exists('gzuncompress')) {
			$type[] = 'compress;q=0.5';
		}

		$type[] = 'gzip;q=0.5';

		return implode(', ', $type);
	}

	/**
	 * Format a URL given GET data
	 *
	 * @param array $url_parts
	 * @param array|object $data Data to build query using, see {@see http://php.net/http_build_query}
	 * @return string URL with data
	 */
	protected static function format_get($url_parts, $data) {
		if (!empty($data)) {
			if (empty($url_parts['query']))
				$url_parts['query'] = '';

			$url_parts['query'] .= '&' . http_build_query($data, null, '&');
			$url_parts['query'] = trim($url_parts['query'], '&');
		}
		if (isset($url_parts['path'])) {
			if (isset($url_parts['query'])) {
				$get = $url_parts['path'] . '?' . $url_parts['query'];
			}
			else {
				$get = $url_parts['path'];
			}
		}
		else {
			$get = '/';
		}
		return $get;
	}

	/**
	 * Error handler for stream_socket_client()
	 *
	 * @param int $errno Error number (e.g. E_WARNING)
	 * @param string $errstr Error message
	 */
	public function connect_error_handler($errno, $errstr) {
		// Double-check we can handle it
		if (($errno & E_WARNING) === 0 && ($errno & E_NOTICE) === 0) {
			// Return false to indicate the default error handler should engage
			return false;
		}

		$this->connect_error .= $errstr . "\n";
		return true;
	}

	/**
	 * Verify the certificate against common name and subject alternative names
	 *
	 * Unfortunately, PHP doesn't check the certificate against the alternative
	 * names, leading things like 'https://www.github.com/' to be invalid.
	 * Instead
	 *
	 * @see http://tools.ietf.org/html/rfc2818#section-3.1 RFC2818, Section 3.1
	 *
	 * @throws Requests_Exception On failure to connect via TLS (`fsockopen.ssl.connect_error`)
	 * @throws Requests_Exception On not obtaining a match for the host (`fsockopen.ssl.no_match`)
	 * @param string $host Host name to verify against
	 * @param resource $context Stream context
	 * @return bool
	 */
	public function verify_certificate_from_context($host, $context) {
		$meta = stream_context_get_options($context);

		// If we don't have SSL options, then we couldn't make the connection at
		// all
		if (empty($meta) || empty($meta['ssl']) || empty($meta['ssl']['peer_certificate'])) {
			throw new Requests_Exception(rtrim($this->connect_error), 'ssl.connect_error');
		}

		$cert = openssl_x509_parse($meta['ssl']['peer_certificate']);

		return Requests_SSL::verify_certificate($host, $cert);
	}

	/**
	 * Whether this transport is valid
	 *
	 * @codeCoverageIgnore
	 * @return boolean True if the transport is valid, false otherwise.
	 */
	public static function test($capabilities = array()) {
		if (!function_exists('fsockopen'))
			return false;

		// If needed, check that streams support SSL
		if (isset( $capabilities['ssl'] ) && $capabilities['ssl']) {
			if (!extension_loaded('openssl') || !function_exists('openssl_x509_parse'))
				return false;

			// Currently broken, thanks to https://github.com/facebook/hhvm/issues/2156
			if (defined('HHVM_VERSION'))
				return false;
		}

		return true;
	}
}
