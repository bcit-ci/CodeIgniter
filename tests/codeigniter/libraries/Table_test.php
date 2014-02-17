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

		$template = array('a' => 'b');

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
			array(
				array('data' => 'name'),
				array('data' => 'color'),
				array('data' => 'size')
			),
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
			array(
				array('data' => 'your'),
				array('data' => 'pony'),
				array('data' => 'stinks')
			),
			$this->table->rows[1]
		);
	}

	// Uility Methods
	// --------------------------------------------------------------------

	public function test_prep_args()
	{
		$expected = array(
			array('data' => 'name'),
			array('data' => 'color'),
			array('data' => 'size')
		);

		$this->assertEquals(
			$expected,
			$this->table->prep_args(array('name', 'color', 'size'))
		);

		// with cell attributes
		// need to add that new argument row to our expected outcome
		$expected[] = array('data' => 'weight', 'class' => 'awesome');

		$this->assertEquals(
			$expected,
			$this->table->prep_args(array('name', 'color', 'size', array('data' => 'weight', 'class' => 'awesome')))
		);
	}

	public function test_default_template_keys()
	{
		$keys = array(
			'table_open',
			'thead_open', 'thead_close',
			'heading_row_start', 'heading_row_end', 'heading_cell_start', 'heading_cell_end',
			'tbody_open', 'tbody_close',
			'row_start', 'row_end', 'cell_start', 'cell_end',
			'row_alt_start', 'row_alt_end', 'cell_alt_start', 'cell_alt_end',
			'table_close'
		);

		foreach ($keys as $key)
		{
			$this->assertArrayHasKey($key, $this->table->default_template());
		}
	}

	public function test_compile_template()
	{
		$this->assertFalse($this->table->set_template('invalid_junk'));

		// non default key
		$this->table->set_template(array('nonsense' => 'foo'));
		$this->table->compile_template();

		$this->assertArrayHasKey('nonsense', $this->table->template);
		$this->assertEquals('foo', $this->table->template['nonsense']);

		// override default
		$this->table->set_template(array('table_close' => '</table junk>'));
		$this->table->compile_template();

		$this->assertArrayHasKey('table_close', $this->table->template);
		$this->assertEquals('</table junk>', $this->table->template['table_close']);
	}

	public function test_make_columns()
	{
		// Test bogus parameters
		$this->assertFalse($this->table->make_columns('invalid_junk'));
		$this->assertFalse($this->table->make_columns(array()));
		$this->assertFalse($this->table->make_columns(array('one', 'two'), '2.5'));

		// Now on to the actual column creation

		$five_values = array(
			'Laura', 'Red', '15',
			'Katie', 'Blue'
		);

		// No column count - no changes to the array
		$this->assertEquals(
			$five_values,
			$this->table->make_columns($five_values)
		);

		// Column count of 3 leaves us with one &nbsp;
		$this->assertEquals(
			array(
				array('Laura', 'Red', '15'),
				array('Katie', 'Blue', '&nbsp;')
			),
			$this->table->make_columns($five_values, 3)
		);
	}

	public function test_clear()
	{
		$this->table->set_heading('Name', 'Color', 'Size');

		// Make columns changes auto_heading
		$rows = $this->table->make_columns(array(
			'Laura', 'Red', '15',
			'Katie', 'Blue'
		), 3);

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
		$data = array(
			array('name', 'color', 'number'),
			array('Laura', 'Red', '22'),
			array('Katie', 'Blue')
		);

		$this->table->auto_heading = FALSE;
		$this->table->set_from_array($data);
		$this->assertEmpty($this->table->heading);

		$this->table->clear();

		$this->table->set_from_array($data);
		$this->assertEquals(count($this->table->rows), 2);

		$expected = array(
			array('data' => 'name'),
			array('data' => 'color'),
			array('data' => 'number')
		);

		$this->assertEquals($expected, $this->table->heading);

		$expected = array(
			array('data' => 'Katie'),
			array('data' => 'Blue'),
		);

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

		$expected = array(
			array('data' => 'name'),
			array('data' => 'email')
		);

		$this->assertEquals($expected, $this->table->heading);

		$expected = array(
			'name' => array('data' => 'Foo Bar'),
			'email' => array('data' => 'foo@bar.com'),
		);

		$this->assertEquals($expected, $this->table->rows[1]);
	}

	public function test_generate()
	{
		// Prepare the data
		$data = array(
			array('Name', 'Color', 'Size'),
			array('Fred', 'Blue', 'Small'),
			array('Mary', 'Red', 'Large'),
			array('John', 'Green', 'Medium')
		);

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
		return array('name', 'email');
	}

	public function result_array()
	{
		return array(
			array('name' => 'John Doe', 'email' => 'john@doe.com'),
			array('name' => 'Foo Bar', 'email' => 'foo@bar.com')
		);
	}
}