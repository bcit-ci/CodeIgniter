<?php

class RequestsTests_Encoding extends PHPUnit_Framework_TestCase {
	protected static function mapData($type, $data) {
		$real_data = array();
		foreach ($data as $value) {
			$key = $type . ': ' . $value[0];
			$real_data[$key] = $value;
		}
		return $real_data;
	}

	public static function gzipData() {
		return array(
			array(
				'foobar',
				"\x1f\x8b\x08\x00\x00\x00\x00\x00\x00\x03\x4b\xcb\xcf\x4f\x4a"
				. "\x2c\x02\x00\x95\x1f\xf6\x9e\x06\x00\x00\x00",
			),
			array(
				'Requests for PHP',
				"\x1f\x8b\x08\x00\x00\x00\x00\x00\x00\x03\x0b\x4a\x2d\x2c\x4d"
				. "\x2d\x2e\x29\x56\x48\xcb\x2f\x52\x08\xf0\x08\x00\x00\x58\x35"
				. "\x18\x17\x10\x00\x00\x00",
			),
		);
	}

	public static function deflateData() {
		return array(
			array(
				'foobar',
				"\x1f\x8b\x08\x00\x00\x00\x00\x00\x00\x03\x78\x9c\x4b\xcb\xcf"
				. "\x4f\x4a\x2c\x02\x00\x08\xab\x02\x7a"
			),
			array(
				'Requests for PHP',
				"\x1f\x8b\x08\x00\x00\x00\x00\x00\x00\x03\x78\x9c\x0b\x4a\x2d"
				. "\x2c\x4d\x2d\x2e\x29\x56\x48\xcb\x2f\x52\x08\xf0\x08\x00\x00"
				. "\x34\x68\x05\xcc"
			)
		);
	}
	public static function deflateWithoutHeadersData() {
		return array(
			array(
				'foobar',
				"\x78\x9c\x4b\xcb\xcf\x4f\x4a\x2c\x02\x00\x08\xab\x02\x7a"
			),
			array(
				'Requests for PHP',
				"\x78\x9c\x0b\x4a\x2d\x2c\x4d\x2d\x2e\x29\x56\x48\xcb\x2f\x52"
				. "\x08\xf0\x08\x00\x00\x34\x68\x05\xcc"
			)
		);
	}

	public static function encodedData() {
		$datasets = array();
		$datasets['gzip'] = self::gzipData();
		$datasets['deflate'] = self::deflateData();
		$datasets['deflate without zlib headers'] = self::deflateWithoutHeadersData();

		$data = array();
		foreach ($datasets as $key => $set) {
			$real_set = self::mapData($key, $set);
			$data = array_merge($data, $real_set);
		}
		return $data;
	}

	/**
	 * @dataProvider encodedData
	 */
	public function testDecompress($original, $encoded) {
		$decoded = Requests::decompress($encoded);
		$this->assertEquals($original, $decoded);
	}

	/**
	 * @dataProvider encodedData
	 */
	public function testCompatibleInflate($original, $encoded) {
		$decoded = Requests::compatible_gzinflate($encoded);
		$this->assertEquals($original, $decoded);
	}

	protected function bin2hex($field) {
		$field = bin2hex($field);
		$field = chunk_split($field,2,"\\x");
		$field = "\\x" . substr($field,0,-2);
		return $field;
	}
}
