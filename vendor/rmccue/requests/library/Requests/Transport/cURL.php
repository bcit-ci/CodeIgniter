<?php
/**
 * cURL HTTP transport
 *
 * @package Requests
 * @subpackage Transport
 */

/**
 * cURL HTTP transport
 *
 * @package Requests
 * @subpackage Transport
 */
class Requests_Transport_cURL implements Requests_Transport {
	/**
	 * Raw HTTP data
	 *
	 * @var string
	 */
	public $headers = '';

	/**
	 * Information on the current request
	 *
	 * @var array cURL information array, see {@see http://php.net/curl_getinfo}
	 */
	public $info;

	/**
	 * Version string
	 *
	 * @var string
	 */
	public $version;

	/**
	 * cURL handle
	 *
	 * @var resource
	 */
	protected $fp;

	/**
	 * Have we finished the headers yet?
	 *
	 * @var boolean
	 */
	protected $done_headers = false;

	/**
	 * If streaming to a file, keep the file pointer
	 *
	 * @var resource
	 */
	protected $stream_handle;

	/**
	 * Constructor
	 */
	public function __construct() {
		$curl = curl_version();
		$this->version = $curl['version'];
		$this->fp = curl_init();

		curl_setopt($this->fp, CURLOPT_HEADER, false);
		curl_setopt($this->fp, CURLOPT_RETURNTRANSFER, 1);
		if (version_compare($this->version, '7.10.5', '>=')) {
			curl_setopt($this->fp, CURLOPT_ENCODING, '');
		}
		if (defined('CURLOPT_PROTOCOLS')) {
			curl_setopt($this->fp, CURLOPT_PROTOCOLS, CURLPROTO_HTTP | CURLPROTO_HTTPS);
		}
		if (defined('CURLOPT_REDIR_PROTOCOLS')) {
			curl_setopt($this->fp, CURLOPT_REDIR_PROTOCOLS, CURLPROTO_HTTP | CURLPROTO_HTTPS);
		}
	}

	/**
	 * Perform a request
	 *
	 * @throws Requests_Exception On a cURL error (`curlerror`)
	 *
	 * @param string $url URL to request
	 * @param array $headers Associative array of request headers
	 * @param string|array $data Data to send either as the POST body, or as parameters in the URL for a GET/HEAD
	 * @param array $options Request options, see {@see Requests::response()} for documentation
	 * @return string Raw HTTP result
	 */
	public function request($url, $headers = array(), $data = array(), $options = array()) {
		$this->setup_handle($url, $headers, $data, $options);

		$options['hooks']->dispatch('curl.before_send', array(&$this->fp));

		if ($options['filename'] !== false) {
			$this->stream_handle = fopen($options['filename'], 'wb');
			curl_setopt($this->fp, CURLOPT_FILE, $this->stream_handle);
		}

		if (isset($options['verify'])) {
			if ($options['verify'] === false) {
				curl_setopt($this->fp, CURLOPT_SSL_VERIFYHOST, 0);
				curl_setopt($this->fp, CURLOPT_SSL_VERIFYPEER, 0);

			} elseif (is_string($options['verify'])) {
				curl_setopt($this->fp, CURLOPT_CAINFO, $options['verify']);
			}
		}

		if (isset($options['verifyname']) && $options['verifyname'] === false) {
			curl_setopt($this->fp, CURLOPT_SSL_VERIFYHOST, 0);
		}

		$response = curl_exec($this->fp);

		$options['hooks']->dispatch('curl.after_send', array(&$fake_headers));

		if (curl_errno($this->fp) === 23 || curl_errno($this->fp) === 61) {
			curl_setopt($this->fp, CURLOPT_ENCODING, 'none');
			$response = curl_exec($this->fp);
		}

		$this->process_response($response, $options);
		curl_close($this->fp);
		return $this->headers;
	}

	/**
	 * Send multiple requests simultaneously
	 *
	 * @param array $requests Request data
	 * @param array $options Global options
	 * @return array Array of Requests_Response objects (may contain Requests_Exception or string responses as well)
	 */
	public function request_multiple($requests, $options) {
		$multihandle = curl_multi_init();
		$subrequests = array();
		$subhandles = array();

		$class = get_class($this);
		foreach ($requests as $id => $request) {
			$subrequests[$id] = new $class();
			$subhandles[$id] = $subrequests[$id]->get_subrequest_handle($request['url'], $request['headers'], $request['data'], $request['options']);
			$request['options']['hooks']->dispatch('curl.before_multi_add', array(&$subhandles[$id]));
			curl_multi_add_handle($multihandle, $subhandles[$id]);
		}

		$completed = 0;
		$responses = array();

		$request['options']['hooks']->dispatch('curl.before_multi_exec', array(&$multihandle));

		do {
			$active = false;

			do {
				$status = curl_multi_exec($multihandle, $active);
			}
			while ($status === CURLM_CALL_MULTI_PERFORM);

			$to_process = array();

			// Read the information as needed
			while ($done = curl_multi_info_read($multihandle)) {
				$key = array_search($done['handle'], $subhandles, true);
				if (!isset($to_process[$key])) {
					$to_process[$key] = $done;
				}
			}

			// Parse the finished requests before we start getting the new ones
			foreach ($to_process as $key => $done) {
				$options = $requests[$key]['options'];
				$responses[$key] = $subrequests[$key]->process_response(curl_multi_getcontent($done['handle']), $options);

				$options['hooks']->dispatch('transport.internal.parse_response', array(&$responses[$key], $requests[$key]));

				curl_multi_remove_handle($multihandle, $done['handle']);
				curl_close($done['handle']);

				if (!is_string($responses[$key])) {
					$options['hooks']->dispatch('multiple.request.complete', array(&$responses[$key], $key));
				}
				$completed++;
			}
		}
		while ($active || $completed < count($subrequests));

		$request['options']['hooks']->dispatch('curl.after_multi_exec', array(&$multihandle));

		curl_multi_close($multihandle);

		return $responses;
	}

	/**
	 * Get the cURL handle for use in a multi-request
	 *
	 * @param string $url URL to request
	 * @param array $headers Associative array of request headers
	 * @param string|array $data Data to send either as the POST body, or as parameters in the URL for a GET/HEAD
	 * @param array $options Request options, see {@see Requests::response()} for documentation
	 * @return resource Subrequest's cURL handle
	 */
	public function &get_subrequest_handle($url, $headers, $data, $options) {
		$this->setup_handle($url, $headers, $data, $options);

		if ($options['filename'] !== false) {
			$this->stream_handle = fopen($options['filename'], 'wb');
			curl_setopt($this->fp, CURLOPT_FILE, $this->stream_handle);
		}

		return $this->fp;
	}

	/**
	 * Setup the cURL handle for the given data
	 *
	 * @param string $url URL to request
	 * @param array $headers Associative array of request headers
	 * @param string|array $data Data to send either as the POST body, or as parameters in the URL for a GET/HEAD
	 * @param array $options Request options, see {@see Requests::response()} for documentation
	 */
	protected function setup_handle($url, $headers, $data, $options) {
		$options['hooks']->dispatch('curl.before_request', array(&$this->fp));

		$headers = Requests::flatten($headers);
		if (in_array($options['type'], array(Requests::HEAD, Requests::GET, Requests::DELETE)) & !empty($data)) {
			$url = self::format_get($url, $data);
		}
		elseif (!empty($data) && !is_string($data)) {
			$data = http_build_query($data, null, '&');
		}

		switch ($options['type']) {
			case Requests::POST:
				curl_setopt($this->fp, CURLOPT_POST, true);
				curl_setopt($this->fp, CURLOPT_POSTFIELDS, $data);
				break;
			case Requests::PATCH:
			case Requests::PUT:
				curl_setopt($this->fp, CURLOPT_CUSTOMREQUEST, $options['type']);
				curl_setopt($this->fp, CURLOPT_POSTFIELDS, $data);
				break;
			case Requests::DELETE:
				curl_setopt($this->fp, CURLOPT_CUSTOMREQUEST, 'DELETE');
				break;
			case Requests::HEAD:
				curl_setopt($this->fp, CURLOPT_NOBODY, true);
				break;
		}

		curl_setopt($this->fp, CURLOPT_URL, $url);
		curl_setopt($this->fp, CURLOPT_TIMEOUT, $options['timeout']);
		curl_setopt($this->fp, CURLOPT_CONNECTTIMEOUT, $options['timeout']);
		curl_setopt($this->fp, CURLOPT_REFERER, $url);
		curl_setopt($this->fp, CURLOPT_USERAGENT, $options['useragent']);
		curl_setopt($this->fp, CURLOPT_HTTPHEADER, $headers);

		if (true === $options['blocking']) {
			curl_setopt($this->fp, CURLOPT_HEADERFUNCTION, array(&$this, 'stream_headers'));
		}
	}

	public function process_response($response, $options) {
		if ($options['blocking'] === false) {
			$fake_headers = '';
			$options['hooks']->dispatch('curl.after_request', array(&$fake_headers));
			return false;
		}
		if ($options['filename'] !== false) {
			fclose($this->stream_handle);
			$this->headers = trim($this->headers);
		}
		else {
			$this->headers .= $response;
		}

		if (curl_errno($this->fp)) {
			throw new Requests_Exception('cURL error ' . curl_errno($this->fp) . ': ' . curl_error($this->fp), 'curlerror', $this->fp);
			return;
		}
		$this->info = curl_getinfo($this->fp);

		$options['hooks']->dispatch('curl.after_request', array(&$this->headers));
		return $this->headers;
	}

	/**
	 * Collect the headers as they are received
	 *
	 * @param resource $handle cURL resource
	 * @param string $headers Header string
	 * @return integer Length of provided header
	 */
	protected function stream_headers($handle, $headers) {
		// Why do we do this? cURL will send both the final response and any
		// interim responses, such as a 100 Continue. We don't need that.
		// (We may want to keep this somewhere just in case)
		if ($this->done_headers) {
			$this->headers = '';
			$this->done_headers = false;
		}
		$this->headers .= $headers;

		if ($headers === "\r\n") {
			$this->done_headers = true;
		}
		return strlen($headers);
	}

	/**
	 * Format a URL given GET data
	 *
	 * @param string $url
	 * @param array|object $data Data to build query using, see {@see http://php.net/http_build_query}
	 * @return string URL with data
	 */
	protected static function format_get($url, $data) {
		if (!empty($data)) {
			$url_parts = parse_url($url);
			if (empty($url_parts['query'])) {
				$query = $url_parts['query'] = '';
			}
			else {
				$query = $url_parts['query'];
			}

			$query .= '&' . http_build_query($data, null, '&');
			$query = trim($query, '&');

			if (empty($url_parts['query'])) {
				$url .= '?' . $query;
			}
			else {
				$url = str_replace($url_parts['query'], $query, $url);
			}
		}
		return $url;
	}

	/**
	 * Whether this transport is valid
	 *
	 * @codeCoverageIgnore
	 * @return boolean True if the transport is valid, false otherwise.
	 */
	public static function test() {
		return (function_exists('curl_init') && function_exists('curl_exec'));
	}
}
