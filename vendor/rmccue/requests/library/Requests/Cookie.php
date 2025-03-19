<?php
/**
 * Cookie storage object
 *
 * @package Requests
 * @subpackage Cookies
 */

/**
 * Cookie storage object
 *
 * @package Requests
 * @subpackage Cookies
 */
class Requests_Cookie {
	/**
	 * 
	 * @var string
	 */
	public $name;

	/**
	 * @var string
	 */
	public $value;

	/**
	 * Cookie attributes
	 * 
	 * Valid keys are (currently) path, domain, expires, max-age, secure and
	 * httponly.
	 *
	 * @var array
	 */
	public $attributes = array();

	/**
	 * Create a new cookie object
	 *
	 * @param string $name
	 * @param string $value
	 * @param array $attributes Associative array of attribute data
	 */
	public function __construct($name, $value, $attributes = array()) {
		$this->name = $name;
		$this->value = $value;
		$this->attributes = $attributes;
	}

	/**
	 * Format a cookie for a Cookie header
	 *
	 * This is used when sending cookies to a server.
	 *
	 * @return string Cookie formatted for Cookie header
	 */
	public function formatForHeader() {
		return sprintf('%s=%s', $this->name, $this->value);
	}

	/**
	 * Format a cookie for a Set-Cookie header
	 *
	 * This is used when sending cookies to clients. This isn't really
	 * applicable to client-side usage, but might be handy for debugging.
	 *
	 * @return string Cookie formatted for Set-Cookie header
	 */
	public function formatForSetCookie() {
		$header_value = $this->formatForHeader();
		if (!empty($this->attributes)) {
			$parts = array();
			foreach ($this->attributes as $key => $value) {
				// Ignore non-associative attributes
				if (is_numeric($key)) {
					$parts[] = $value;
				}
				else {
					$parts[] = sprintf('%s=%s', $key, $value);
				}
			}

			$header_value .= '; ' . implode('; ', $parts);
		}
		return $header_value;
	}

	/**
	 * Get the cookie value
	 *
	 * Attributes and other data can be accessed via methods.
	 */
	public function __toString() {
		return $this->value;
	}

	/**
	 * Parse a cookie string into a cookie object
	 *
	 * Based on Mozilla's parsing code in Firefox and related projects, which
	 * is an intentional deviation from RFC 2109 and RFC 2616. RFC 6265
	 * specifies some of this handling, but not in a thorough manner.
	 *
	 * @param string Cookie header value (from a Set-Cookie header)
	 * @return Requests_Cookie Parsed cookie object
	 */
	public static function parse($string, $name = '') {
		$parts = explode(';', $string);
		$kvparts = array_shift($parts);

		if (!empty($name)) {
			$value = $string;
		}
		elseif (strpos($kvparts, '=') === false) {
			// Some sites might only have a value without the equals separator.
			// Deviate from RFC 6265 and pretend it was actually a blank name
			// (`=foo`)
			//
			// https://bugzilla.mozilla.org/show_bug.cgi?id=169091
			$name = '';
			$value = $kvparts;
		}
		else {
			list($name, $value) = explode('=', $kvparts, 2);
		}
		$name = trim($name);
		$value = trim($value);

		// Attribute key are handled case-insensitively
		$attributes = new Requests_Utility_CaseInsensitiveDictionary();

		if (!empty($parts)) {
			foreach ($parts as $part) {
				if (strpos($part, '=') === false) {
					$part_key = $part;
					$part_value = true;
				}
				else {
					list($part_key, $part_value) = explode('=', $part, 2);
					$part_value = trim($part_value);
				}

				$part_key = trim($part_key);
				$attributes[$part_key] = $part_value;
			}
		}

		return new Requests_Cookie($name, $value, $attributes);
	}

	/**
	 * Parse all Set-Cookie headers from request headers
	 *
	 * @param Requests_Response_Headers $headers
	 * @return array
	 */
	public static function parseFromHeaders(Requests_Response_Headers $headers) {
		$cookie_headers = $headers->getValues('Set-Cookie');
		if (empty($cookie_headers)) {
			return array();
		}

		$cookies = array();
		foreach ($cookie_headers as $header) {
			$parsed = self::parse($header);
			$cookies[$parsed->name] = $parsed;
		}

		return $cookies;
	}
}
