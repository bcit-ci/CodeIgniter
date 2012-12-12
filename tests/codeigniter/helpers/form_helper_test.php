<?php

class Form_helper_test extends CI_TestCase
{
	public function set_up()
	{
		$this->helper('form');
	}

	// ------------------------------------------------------------------------

	public function test_form_hidden()
	{
		$expected = <<<EOH

<input type="hidden" name="username" value="johndoe" />

EOH;

		$this->assertEquals($expected, form_hidden('username', 'johndoe'));
	}

	// ------------------------------------------------------------------------

	public function test_form_input()
	{
		$expected = <<<EOH
<input type="text" name="username" value="johndoe" id="username" maxlength="100" size="50" style="width:50%"  />

EOH;

		$data = array(
			'name'        => 'username',
			'id'          => 'username',
			'value'       => 'johndoe',
			'maxlength'   => '100',
			'size'        => '50',
			'style'       => 'width:50%',
		);

		$this->assertEquals($expected, form_input($data));
	}

	// ------------------------------------------------------------------------

	public function test_form_password()
	{
		$expected = <<<EOH
<input type="password" name="password" value=""  />

EOH;

		$this->assertEquals($expected, form_password('password'));
	}

	// ------------------------------------------------------------------------

	public function test_form_upload()
	{
		$expected = <<<EOH
<input type="file" name="attachment" value=""  />

EOH;

		$this->assertEquals($expected, form_upload('attachment'));
	}

	// ------------------------------------------------------------------------

	public function test_form_textarea()
	{
		$expected = <<<EOH
<textarea name="notes" cols="40" rows="10" >Notes</textarea>

EOH;

		$this->assertEquals($expected, form_textarea('notes', 'Notes'));
	}

	// ------------------------------------------------------------------------

	public function test_form_dropdown()
	{
		$expected = <<<EOH
<select name="shirts">
<option value="small">Small Shirt</option>
<option value="med">Medium Shirt</option>
<option value="large" selected="selected">Large Shirt</option>
<option value="xlarge">Extra Large Shirt</option>
</select>

EOH;

		$options = array(
			'small'		=> 'Small Shirt',
			'med'		=> 'Medium Shirt',
			'large'		=> 'Large Shirt',
			'xlarge'	=> 'Extra Large Shirt',
		);

		$this->assertEquals($expected, form_dropdown('shirts', $options, 'large'));

		$expected = <<<EOH
<select name="shirts" multiple="multiple">
<option value="small" selected="selected">Small Shirt</option>
<option value="med">Medium Shirt</option>
<option value="large" selected="selected">Large Shirt</option>
<option value="xlarge">Extra Large Shirt</option>
</select>

EOH;

		$shirts_on_sale = array('small', 'large');

		$this->assertEquals($expected, form_dropdown('shirts', $options, $shirts_on_sale));

		$options = array(
			'Swedish Cars' => array(
				'volvo'	=> 'Volvo',
				'saab'	=> 'Saab'
			),
			'German Cars' => array(
				'mercedes'	=> 'Mercedes',
				'audi'		=> 'Audi'
			)
		);

		$expected = <<<EOH
<select name="cars" multiple="multiple">
<optgroup label="Swedish Cars">
<option value="volvo" selected="selected">Volvo</option>
<option value="saab">Saab</option>
</optgroup>
<optgroup label="German Cars">
<option value="mercedes">Mercedes</option>
<option value="audi" selected="selected">Audi</option>
</optgroup>
</select>

EOH;

		$this->assertEquals($expected, form_dropdown('cars', $options, array('volvo', 'audi')));
	}

	// ------------------------------------------------------------------------

	public function test_form_multiselect()
	{
		$expected = <<<EOH
<select name="shirts[]"  multiple="multiple">
<option value="small">Small Shirt</option>
<option value="med" selected="selected">Medium Shirt</option>
<option value="large" selected="selected">Large Shirt</option>
<option value="xlarge">Extra Large Shirt</option>
</select>

EOH;

		$options = array(
			'small'		=> 'Small Shirt',
			'med'		=> 'Medium Shirt',
			'large'		=> 'Large Shirt',
			'xlarge'	=> 'Extra Large Shirt',
		);

		$this->assertEquals($expected, form_multiselect('shirts[]', $options, array('med', 'large')));
	}

	// ------------------------------------------------------------------------

	public function test_form_fieldset()
	{
		$expected = <<<EOH
<fieldset>
<legend>Address Information</legend>

EOH;

		$this->assertEquals($expected, form_fieldset('Address Information'));
	}

	// ------------------------------------------------------------------------

	public function test_form_fieldset_close()
	{
		$expected = <<<EOH
</fieldset></div></div>
EOH;

		$this->assertEquals($expected, form_fieldset_close('</div></div>'));
	}

	// ------------------------------------------------------------------------

	public function test_form_checkbox()
	{
		$expected = <<<EOH
<input type="checkbox" name="newsletter" value="accept" checked="checked"  />

EOH;

		$this->assertEquals($expected, form_checkbox('newsletter', 'accept', TRUE));
	}

	// ------------------------------------------------------------------------

	public function test_form_radio()
	{
		$expected = <<<EOH
<input type="radio" name="newsletter" value="accept" checked="checked"  />

EOH;

		$this->assertEquals($expected, form_radio('newsletter', 'accept', TRUE));
	}

	// ------------------------------------------------------------------------

	public function test_form_submit()
	{
		$expected = <<<EOH
<input type="submit" name="mysubmit" value="Submit Post!"  />

EOH;

		$this->assertEquals($expected, form_submit('mysubmit', 'Submit Post!'));
	}

	// ------------------------------------------------------------------------

	public function test_form_label()
	{
		$expected = <<<EOH
<label for="username">What is your Name</label>
EOH;

		$this->assertEquals($expected, form_label('What is your Name', 'username'));
	}

	// ------------------------------------------------------------------------

	public function test_form_reset()
	{
		$expected = <<<EOH
<input type="reset" name="myreset" value="Reset"  />

EOH;

		$this->assertEquals($expected, form_reset('myreset', 'Reset'));
	}

	// ------------------------------------------------------------------------

	public function test_form_button()
	{
		$expected = <<<EOH
<button name="name" type="button" >content</button>

EOH;

		$this->assertEquals($expected, form_button('name', 'content'));
	}

	// ------------------------------------------------------------------------

	public function test_form_close()
	{
		$expected = <<<EOH
</form></div></div>
EOH;

		$this->assertEquals($expected, form_close('</div></div>'));
	}

	// ------------------------------------------------------------------------

	public function test_form_prep()
	{
		$this->assertEquals(
			'Here is a string containing &quot;quoted&quot; text.',
			form_prep('Here is a string containing "quoted" text.')
		);

		$this->assertEquals(
			'Here is a string containing a &lt;tag&gt;.',
			form_prep('Here is a string containing a <tag>.', TRUE)
		);
	}

}

/* End of file form_helper_test.php */