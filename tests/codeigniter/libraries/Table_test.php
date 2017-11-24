<?php

class Table_test extends CI_TestCase {

	public function set_up()
	{
		$this->table = new Mock_Libraries_Table();
		$this->ci_instance_var('table', $this->table);
	}

	// Setter Methods
	// --------------------------------------------------------------------

	public function test_set_template()
	{
		$this->assertFalse($this->table->set_template('not an array'));

		$template = ['a' => 'b'];

		$this->table->set_template($template);
		$this->assertEquals($template, $this->table->template);
	}

	public function test_set_empty()
	{
		$this->table->set_empty('nada');
		$this->assertEquals('nada', $this->table->empty_cells);
	}

	public function test_set_caption()
	{
		$this->table->set_caption('awesome cap');
		$this->assertEquals('awesome cap', $this->table->caption);
	}

	/*
	 * @depends	test_prep_args
	 */
	public function test_set_heading()
	{
		// uses _prep_args internally, so we'll just do a quick
		// check to verify that func_get_args and prep_args are
		// being called.

		$this->table->set_heading('name', 'color', 'size');

		$this->assertEquals(
			[
				['data' => 'name'],
				['data' => 'color'],
				['data' => 'size']
			],
			$this->table->heading
		);
	}

	/*
	 * @depends	test_prep_args
	 */
	public function test_add_row()
	{
		// uses _prep_args internally, so we'll just do a quick
		// check to verify that func_get_args and prep_args are
		// being called.

		$this->table->add_row('my', 'pony', 'sings');
		$this->table->add_row('your', 'pony', 'stinks');
		$this->table->add_row('my pony', '>', 'your pony');

		$this->assertEquals(count($this->table->rows), 3);

		$this->assertEquals(
			[
				['data' => 'your'],
				['data' => 'pony'],
				['data' => 'stinks']
			],
			$this->table->rows[1]
		);
	}

	// Uility Methods
	// --------------------------------------------------------------------

	public function test_prep_args()
	{
		$expected = [
			['data' => 'name'],
			['data' => 'color'],
			['data' => 'size']
		];

		$this->assertEquals(
			$expected,
			$this->table->prep_args(['name', 'color', 'size'])
		);

		// with cell attributes
		// need to add that new argument row to our expected outcome
		$expected[] = ['data' => 'weight', 'class' => 'awesome'];

		$this->assertEquals(
			$expected,
			$this->table->prep_args(['name', 'color', 'size', ['data' => 'weight', 'class' => 'awesome']])
		);
	}

	public function test_default_template_keys()
	{
		$keys = [
			'table_open',
			'thead_open', 'thead_close',
			'heading_row_start', 'heading_row_end', 'heading_cell_start', 'heading_cell_end',
			'tbody_open', 'tbody_close',
			'row_start', 'row_end', 'cell_start', 'cell_end',
			'row_alt_start', 'row_alt_end', 'cell_alt_start', 'cell_alt_end',
			'table_close'
		];

		foreach ($keys as $key)
		{
			$this->assertArrayHasKey($key, $this->table->default_template());
		}
	}

	public function test_compile_template()
	{
		$this->assertFalse($this->table->set_template('invalid_junk'));

		// non default key
		$this->table->set_template(['nonsense' => 'foo']);
		$this->table->compile_template();

		$this->assertArrayHasKey('nonsense', $this->table->template);
		$this->assertEquals('foo', $this->table->template['nonsense']);

		// override default
		$this->table->set_template(['table_close' => '</table junk>']);
		$this->table->compile_template();

		$this->assertArrayHasKey('table_close', $this->table->template);
		$this->assertEquals('</table junk>', $this->table->template['table_close']);
	}

	public function test_make_columns()
	{
		// Test bogus parameters
		$this->assertFalse($this->table->make_columns('invalid_junk'));
		$this->assertFalse($this->table->make_columns([]));
		$this->assertFalse($this->table->make_columns(['one', 'two'], '2.5'));

		// Now on to the actual column creation

		$five_values = [
			'Laura', 'Red', '15',
			'Katie', 'Blue'
		];

		// No column count - no changes to the array
		$this->assertEquals(
			$five_values,
			$this->table->make_columns($five_values)
		);

		// Column count of 3 leaves us with one &nbsp;
		$this->assertEquals(
			[
				['Laura', 'Red', '15'],
				['Katie', 'Blue', '&nbsp;']
			],
			$this->table->make_columns($five_values, 3)
		);
	}

	public function test_clear()
	{
		$this->table->set_heading('Name', 'Color', 'Size');

		// Make columns changes auto_heading
		$rows = $this->table->make_columns([
			'Laura', 'Red', '15',
			'Katie', 'Blue'
		], 3);

		foreach ($rows as $row)
		{
			$this->table->add_row($row);
		}

		$this->assertFalse($this->table->auto_heading);
		$this->assertEquals(count($this->table->heading), 3);
		$this->assertEquals(count($this->table->rows), 2);

		$this->table->clear();

		$this->assertTrue($this->table->auto_heading);
		$this->assertEmpty($this->table->heading);
		$this->assertEmpty($this->table->rows);
	}

	public function test_set_from_array()
	{
		$data = [
			['name', 'color', 'number'],
			['Laura', 'Red', '22'],
			['Katie', 'Blue']
		];

		$this->table->auto_heading = FALSE;
		$this->table->set_from_array($data);
		$this->assertEmpty($this->table->heading);

		$this->table->clear();

		$this->table->set_from_array($data);
		$this->assertEquals(count($this->table->rows), 2);

		$expected = [
			['data' => 'name'],
			['data' => 'color'],
			['data' => 'number']
		];

		$this->assertEquals($expected, $this->table->heading);

		$expected = [
			['data' => 'Katie'],
			['data' => 'Blue'],
		];

		$this->assertEquals($expected, $this->table->rows[1]);
	}

	public function test_set_from_object()
	{
		// This needs to be passed by reference to CI_DB_result::__construct()
		$dummy = new stdClass();
		$dummy->conn_id = NULL;
		$dummy->result_id = NULL;

		$db_result = new DB_result_dummy($dummy);

		$this->table->set_from_db_result($db_result);

		$expected = [
			['data' => 'name'],
			['data' => 'email']
		];

		$this->assertEquals($expected, $this->table->heading);

		$expected = [
			'name' => ['data' => 'Foo Bar'],
			'email' => ['data' => 'foo@bar.com'],
		];

		$this->assertEquals($expected, $this->table->rows[1]);
	}

	public function test_generate()
	{
		// Prepare the data
		$data = [
			['Name', 'Color', 'Size'],
			['Fred', 'Blue', 'Small'],
			['Mary', 'Red', 'Large'],
			['John', 'Green', 'Medium']
		];

		$table = $this->table->generate($data);

		// Test the table header
		$this->assertTrue(strpos($table, '<th>Name</th>') !== FALSE);
		$this->assertTrue(strpos($table, '<th>Color</th>') !== FALSE);
		$this->assertTrue(strpos($table, '<th>Size</th>') !== FALSE);

		// Test the first entry
		$this->assertTrue(strpos($table, '<td>Fred</td>') !== FALSE);
		$this->assertTrue(strpos($table, '<td>Blue</td>') !== FALSE);
		$this->assertTrue(strpos($table, '<td>Small</td>') !== FALSE);
	}

}

// We need this for the _set_from_db_result() test
class DB_result_dummy extends CI_DB_result
{
	public function list_fields()
	{
		return ['name', 'email'];
	}

	public function result_array()
	{
		return [
			['name' => 'John Doe', 'email' => 'john@doe.com'],
			['name' => 'Foo Bar', 'email' => 'foo@bar.com']
		];
	}
}