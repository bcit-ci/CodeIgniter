<?php

class standard_test extends CI_TestCase {

	public function test_bootstrap()
	{
		if (is_php('5.5'))
		{
			return $this->markTestSkipped('All array functions are already available on PHP 5.5');
		}

		$this->assertTrue(function_exists('array_column'));
	}

	// ------------------------------------------------------------------------

	/**
	 * array_column() test
	 *
	 * Borrowed from PHP's own tests
	 *
	 * @depends	test_bootstrap
	 */
	public function test_array_column()
	{
		// Basic tests

		$input = [
			[
				'id' => 1,
				'first_name' => 'John',
				'last_name' => 'Doe'
			],
			[
				'id' => 2,
				'first_name' => 'Sally',
				'last_name' => 'Smith'
			],
			[
				'id' => 3,
				'first_name' => 'Jane',
				'last_name' => 'Jones'
			]
		];

		// Ensure internal array position doesn't break it
		next($input);

		$this->assertEquals(
			['John', 'Sally', 'Jane'],
			array_column($input, 'first_name')
		);

		$this->assertEquals(
			[1, 2, 3],
			array_column($input, 'id')
		);

		$this->assertEquals(
			[
				1 => 'Doe',
				2 => 'Smith',
				3 => 'Jones'
			],
			array_column($input, 'last_name', 'id')
		);

		$this->assertEquals(
			[
				'John' => 'Doe',
				'Sally' => 'Smith',
				'Jane' => 'Jones'
			],
			array_column($input, 'last_name', 'first_name')
		);

		// Object key search

		$f = new Foo();
		$b = new Bar();

		$this->assertEquals(
			['Doe', 'Smith', 'Jones'],
			array_column($input, $f)
		);

		$this->assertEquals(
			[
				'John' => 'Doe',
				'Sally' => 'Smith',
				'Jane' => 'Jones'
			],
			array_column($input, $f, $b)
		);

		// NULL parameters

		$input = [
			456 => [
				'id' => '3',
				'title' => 'Foo',
				'date' => '2013-03-25'
			],
			457 => [
				'id' => '5',
				'title' => 'Bar',
				'date' => '2012-05-20'
			]
		];

		$this->assertEquals(
			[
				3 => [
					'id' => '3',
					'title' => 'Foo',
					'date' => '2013-03-25'
				],
				5 => [
					'id' => '5',
					'title' => 'Bar',
					'date' => '2012-05-20'
				]
			],
			array_column($input, NULL, 'id')
		);

		$this->assertEquals(
			[
				[
					'id' => '3',
					'title' => 'Foo',
					'date' => '2013-03-25'
				],
				[
					'id' => '5',
					'title' => 'Bar',
					'date' => '2012-05-20'
				]
			],
			array_column($input, NULL, 'foo')
		);

		$this->assertEquals(
			[
				[
					'id' => '3',
					'title' => 'Foo',
					'date' => '2013-03-25'
				],
				[
					'id' => '5',
					'title' => 'Bar',
					'date' => '2012-05-20'
				]
			],
			array_column($input, NULL)
		);

		// Data types

		$fh = fopen(__FILE__, 'r', TRUE);
		$stdClass = new stdClass();
		$input = [
			[
				'id' => 1,
				'value' => $stdClass
			],
			[
				'id' => 2,
				'value' => 34.2345
			],
			[
				'id' => 3,
				'value' => TRUE
			],
			[
				'id' => 4,
				'value' => FALSE
			],
			[
				'id' => 5,
				'value' => NULL
			],
			[
				'id' => 6,
				'value' => 1234
			],
			[
				'id' => 7,
				'value' => 'Foo'
			],
			[
				'id' => 8,
				'value' => $fh
			]
		];

		$this->assertEquals(
			[
				$stdClass,
				34.2345,
				TRUE,
				FALSE,
				NULL,
				1234,
				'Foo',
				$fh
			],
			array_column($input, 'value')
		);

		$this->assertEquals(
			[
				1 => $stdClass,
				2 => 34.2345,
				3 => TRUE,
				4 => FALSE,
				5 => NULL,
				6 => 1234,
				7 => 'Foo',
				8 => $fh
			],
			array_column($input, 'value', 'id')
		);

		// Numeric column keys

		$input = [
			['aaa', '111'],
			['bbb', '222'],
			['ccc', '333', -1 => 'ddd']
		];

		$this->assertEquals(
			['111', '222', '333'],
			array_column($input, 1)
		);

		$this->assertEquals(
			[
				'aaa' => '111',
				'bbb' => '222',
				'ccc' => '333'
			],
			array_column($input, 1, 0)
		);

		$this->assertEquals(
			[
				'aaa' => '111',
				'bbb' => '222',
				'ccc' => '333'
			],
			array_column($input, 1, 0.123)
		);

		$this->assertEquals(
			[
				0 => '111',
				1 => '222',
				'ddd' => '333'
			],
			array_column($input, 1, -1)
		);

		// Non-existing columns

		$this->assertEquals([], array_column($input, 2));
		$this->assertEquals([], array_column($input, 'foo'));
		$this->assertEquals(
			['aaa', 'bbb', 'ccc'],
			array_column($input, 0, 'foo')
		);
		$this->assertEquals([], array_column($input, 3.14));

		// One-dimensional array
		$this->assertEquals([], array_column(['foo', 'bar', 'baz'], 1));

		// Columns not present in all rows

		$input = [
			['a' => 'foo', 'b' => 'bar', 'e' => 'bbb'],
			['a' => 'baz', 'c' => 'qux', 'd' => 'aaa'],
			['a' => 'eee', 'b' => 'fff', 'e' => 'ggg']
		];

		$this->assertEquals(
			['qux'],
			array_column($input, 'c')
		);

		$this->assertEquals(
			['baz' => 'qux'],
			array_column($input, 'c', 'a')
		);

		$this->assertEquals(
			[
				0 => 'foo',
				'aaa' => 'baz',
				1 => 'eee'
			],
			array_column($input, 'a', 'd')
		);

		$this->assertEquals(
			[
				'bbb' => 'foo',
				0 => 'baz',
				'ggg' => 'eee'
			],
			array_column($input, 'a', 'e')
		);

		$this->assertEquals(
			['bar', 'fff'],
			array_column($input, 'b')
		);

		$this->assertEquals(
			[
				'foo' => 'bar',
				'eee' => 'fff'
			],
			array_column($input, 'b', 'a')
		);
	}
}

// ------------------------------------------------------------------------

class Foo {

	public function __toString()
	{
		return 'last_name';
	}
}

class Bar {

	public function __toString()
	{
		return 'first_name';
	}
}
