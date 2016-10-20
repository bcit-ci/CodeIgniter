<?php

class Form_validation_test extends CI_TestCase {

	public function set_up()
	{
		$_SERVER['REQUEST_METHOD'] = 'POST';

		// Create a mock loader since load->helper() looks in the wrong directories for unit tests,
		// We'll use CI_TestCase->helper() instead
		$loader = $this->getMockBuilder('CI_Loader')->setMethods(array('helper'))->getMock();

		// Same applies for lang
		$lang = $this->getMockBuilder('CI_Lang')->setMethods(array('load'))->getMock();

		$this->ci_set_config('charset', 'UTF-8');
		$utf8 = new Mock_Core_Utf8();
		$security = new Mock_Core_Security();
		$input = new Mock_Core_Input($security, $utf8);

		$this->ci_instance_var('lang', $lang);
		$this->ci_instance_var('load', $loader);
		$this->ci_instance_var('input', $input);

		$this->lang('form_validation');
		$this->helper('form');

		$this->form_validation = new CI_Form_validation();
	}

	public function test_empty_array_input()
	{
		$this->assertFalse(
			$this->run_rules(
				array(array('field' => 'foo', 'label' => 'Foo Label', 'rules' => 'required')),
				array('foo' => array())
			)
		);
	}

	public function test_rule_required()
	{
		$rules = array(array('field' => 'foo', 'label' => 'Foo', 'rules' => 'is_numeric'));

		// Empty, not required
		$this->assertTrue($this->run_rules($rules, array('foo' => '')));


		// Not required, but also not empty
		$this->assertTrue($this->run_rules($rules, array('foo' => '123')));
		$this->assertFalse($this->run_rules($rules, array('foo' => 'bar')));

		// Required variations
		$rules[0]['rules'] .= '|required';
		$this->assertTrue($this->run_rules($rules, array('foo' => '123')));
		$this->assertFalse($this->run_rules($rules, array('foo' => '')));
		$this->assertFalse($this->run_rules($rules, array('foo' => ' ')));
		$this->assertFalse($this->run_rules($rules, array('foo' => 'bar')));
	}

	public function test_rule_matches()
	{
		$rules = array(
			array('field' => 'foo', 'label' => 'label', 'rules' => 'required'),
			array('field' => 'bar', 'label' => 'label2', 'rules' => 'matches[foo]')
		);
		$values_base = array('foo' => 'sample');

		$this->assertTrue($this->run_rules($rules, array_merge($values_base, array('bar' => 'sample'))));

		$this->assertFalse($this->run_rules($rules, array_merge($values_base, array('bar' => ''))));
		$this->assertFalse($this->run_rules($rules, array_merge($values_base, array('bar' => 'Sample'))));
		$this->assertFalse($this->run_rules($rules, array_merge($values_base, array('bar' => ' sample'))));
	}

	public function test_rule_differs()
	{
		$rules = array(
			array('field' => 'foo', 'label' => 'label', 'rules' => 'required'),
			array('field' => 'bar', 'label' => 'label2', 'rules' => 'differs[foo]')
		);
		$values_base = array('foo' => 'sample');

		$this->assertTrue($this->run_rules($rules, array_merge($values_base, array('bar' => 'does_not_match'))));
		$this->assertTrue($this->run_rules($rules, array_merge($values_base, array('bar' => 'Sample'))));
		$this->assertTrue($this->run_rules($rules, array_merge($values_base, array('bar' => ' sample'))));

		$this->assertFalse($this->run_rules($rules, array_merge($values_base, array('bar' => 'sample'))));
	}

	public function test_rule_min_length()
	{
		$this->assertTrue($this->form_validation->min_length('12345', '5'));
		$this->assertTrue($this->form_validation->min_length('test', '0'));

		$this->assertFalse($this->form_validation->min_length('123', '4'));
		$this->assertFalse($this->form_validation->min_length('should_fail', 'A'));
		$this->assertFalse($this->form_validation->min_length('', '4'));
	}

	public function test_rule_max_length()
	{
		$this->assertTrue($this->form_validation->max_length('', '4'));
		$this->assertTrue($this->form_validation->max_length('1234', '4'));

		$this->assertFalse($this->form_validation->max_length('12345', '4'));
		$this->assertFalse($this->form_validation->max_length('should_fail', 'A'));
	}

	public function test_rule_exact_length()
	{
		$this->assertTrue($this->form_validation->exact_length('1234', '4'));

		$this->assertFalse($this->form_validation->exact_length('', '3'));
		$this->assertFalse($this->form_validation->exact_length('12345', '4'));
		$this->assertFalse($this->form_validation->exact_length('123', '4'));
		$this->assertFalse($this->form_validation->exact_length('should_fail', 'A'));
	}

	public function test_rule_greater_than()
	{
		$this->assertTrue($this->form_validation->greater_than('-10', '-11'));
		$this->assertTrue($this->form_validation->greater_than('10', '9'));

		$this->assertFalse($this->form_validation->greater_than('10', '10'));
		$this->assertFalse($this->form_validation->greater_than('10', 'a'));
		$this->assertFalse($this->form_validation->greater_than('10a', '10'));
	}

	public function test_rule_greater_than_equal_to()
	{
		$this->assertTrue($this->form_validation->greater_than_equal_to('0', '0'));
		$this->assertTrue($this->form_validation->greater_than_equal_to('1', '0'));

		$this->assertFalse($this->form_validation->greater_than_equal_to('-1', '0'));
		$this->assertFalse($this->form_validation->greater_than_equal_to('10a', '0'));
	}

	public function test_rule_less_than()
	{
		$this->assertTrue($this->form_validation->less_than('4', '5'));
		$this->assertTrue($this->form_validation->less_than('-1', '0'));

		$this->assertFalse($this->form_validation->less_than('4', '4'));
		$this->assertFalse($this->form_validation->less_than('10a', '5'));
	}

	public function test_rule_less_than_equal_to()
	{
		$this->assertTrue($this->form_validation->less_than_equal_to('-1', '0'));
		$this->assertTrue($this->form_validation->less_than_equal_to('-1', '-1'));
		$this->assertTrue($this->form_validation->less_than_equal_to('4', '4'));

		$this->assertFalse($this->form_validation->less_than_equal_to('0', '-1'));
		$this->assertFalse($this->form_validation->less_than_equal_to('10a', '0'));
	}

	public function test_rule_in_list()
	{
		$this->assertTrue($this->form_validation->in_list('red', 'red,Blue,123'));
		$this->assertTrue($this->form_validation->in_list('Blue', 'red,Blue,123'));
		$this->assertTrue($this->form_validation->in_list('123', 'red,Blue,123'));

		$this->assertFalse($this->form_validation->in_list('Red', 'red,Blue,123'));
		$this->assertFalse($this->form_validation->in_list(' red', 'red,Blue,123'));
		$this->assertFalse($this->form_validation->in_list('1234', 'red,Blue,123'));
	}

	public function test_rule_alpha()
	{
		$this->assertTrue($this->form_validation->alpha('abcdefghijklmnopqrstuvwxyzABCDEFGHLIJKLMNOPQRSTUVWXYZ'));

		$this->assertFalse($this->form_validation->alpha('abcdefghijklmnopqrstuvwxyzABCDEFGHLIJKLMNOPQRSTUVWXYZ '));
		$this->assertFalse($this->form_validation->alpha('abcdefghijklmnopqrstuvwxyzABCDEFGHLIJKLMNOPQRSTUVWXYZ1'));
		$this->assertFalse($this->form_validation->alpha('abcdefghijklmnopqrstuvwxyzABCDEFGHLIJKLMNOPQRSTUVWXYZ*'));
	}

	public function test_rule_alpha_numeric()
	{
		$this->assertTrue($this->form_validation->alpha_numeric('abcdefghijklmnopqrstuvwxyzABCDEFGHLIJKLMNOPQRSTUVWXYZ0123456789'));

		$this->assertFalse($this->form_validation->alpha_numeric('abcdefghijklmnopqrstuvwxyzABCDEFGHLIJKLMNOPQRSTUVWXYZ0123456789\ '));
		$this->assertFalse($this->form_validation->alpha_numeric('abcdefghijklmnopqrstuvwxyzABCDEFGHLIJKLMNOPQRSTUVWXYZ0123456789_'));
	}

	public function test_rule_alpha_numeric_spaces()
	{
		$this->assertTrue($this->form_validation->alpha_numeric_spaces(' abcdefghijklmnopqrstuvwxyzABCDEFGHLIJKLMNOPQRSTUVWXYZ0123456789'));

		$this->assertFalse($this->form_validation->alpha_numeric_spaces(' abcdefghijklmnopqrstuvwxyzABCDEFGHLIJKLMNOPQRSTUVWXYZ0123456789_'));
	}

	public function test_rule_alpha_dash()
	{
		$this->assertTrue($this->form_validation->alpha_dash('abcdefghijklmnopqrstuvwxyzABCDEFGHLIJKLMNOPQRSTUVWXYZ0123456789-_'));

		$this->assertFalse($this->form_validation->alpha_dash('abcdefghijklmnopqrstuvwxyzABCDEFGHLIJKLMNOPQRSTUVWXYZ0123456789-_\ '));
	}

	public function test_rule_numeric()
	{
		$this->assertTrue($this->form_validation->numeric('0'));
		$this->assertTrue($this->form_validation->numeric('12314'));
		$this->assertTrue($this->form_validation->numeric('-42'));

		$this->assertFalse($this->form_validation->numeric('123a'));
		$this->assertFalse($this->form_validation->numeric('--1'));
	}

	public function test_rule_integer()
	{
		$this->assertTrue($this->form_validation->integer('0'));
		$this->assertTrue($this->form_validation->integer('42'));
		$this->assertTrue($this->form_validation->integer('-1'));

		$this->assertFalse($this->form_validation->integer('124a'));
		$this->assertFalse($this->form_validation->integer('1.9'));
		$this->assertFalse($this->form_validation->integer('--1'));
	}

	public function test_rule_decimal()
	{
		$this->assertTrue($this->form_validation->decimal('1.0'));
		$this->assertTrue($this->form_validation->decimal('-0.98'));

		$this->assertFalse($this->form_validation->decimal('0'));
		$this->assertFalse($this->form_validation->decimal('1.0a'));
		$this->assertFalse($this->form_validation->decimal('-i'));
		$this->assertFalse($this->form_validation->decimal('--1'));
	}

	public function test_rule_is_natural()
	{
		$this->assertTrue($this->form_validation->is_natural('0'));
		$this->assertTrue($this->form_validation->is_natural('12'));

		$this->assertFalse($this->form_validation->is_natural('42a'));
		$this->assertFalse($this->form_validation->is_natural('-1'));
	}

	public function test_rule_is_natural_no_zero()
	{
		$this->assertTrue($this->form_validation->is_natural_no_zero('42'));

		$this->assertFalse($this->form_validation->is_natural_no_zero('0'));
		$this->assertFalse($this->form_validation->is_natural_no_zero('42a'));
		$this->assertFalse($this->form_validation->is_natural_no_zero('-1'));
	}

	public function test_rule_valid_url()
	{
		$this->assertTrue($this->form_validation->valid_url('www.codeigniter.com'));
		$this->assertTrue($this->form_validation->valid_url('http://codeigniter.com'));

		// https://bugs.php.net/bug.php?id=51192
		$this->assertTrue($this->form_validation->valid_url('http://accept-dashes.tld'));
		$this->assertFalse($this->form_validation->valid_url('http://reject_underscores.tld'));

		// https://github.com/bcit-ci/CodeIgniter/issues/4415
		$this->assertTrue($this->form_validation->valid_url('http://[::1]/ipv6'));

		// URI scheme case-sensitivity: https://github.com/bcit-ci/CodeIgniter/pull/4758
		$this->assertTrue($this->form_validation->valid_url('HtTp://127.0.0.1/'));

		$this->assertFalse($this->form_validation->valid_url('htt://www.codeIgniter.com'));
		$this->assertFalse($this->form_validation->valid_url(''));
		$this->assertFalse($this->form_validation->valid_url('code igniter'));
	}

	public function test_rule_valid_email()
	{
		$this->assertTrue($this->form_validation->valid_email('email@sample.com'));

		$this->assertFalse($this->form_validation->valid_email('valid_email', '@sample.com'));
	}

	public function test_rule_valid_emails()
	{
		$this->assertTrue($this->form_validation->valid_emails('1@sample.com,2@sample.com'));
		$this->assertTrue($this->form_validation->valid_emails('email@sample.com'));

		$this->assertFalse($this->form_validation->valid_emails('valid_email', '@sample.com'));
		$this->assertFalse($this->form_validation->valid_emails('@sample.com,2@sample.com,validemail@email.ca'));
	}

	public function test_rule_valid_ip()
	{
		$this->assertTrue($this->form_validation->valid_ip('127.0.0.1'));
		$this->assertTrue($this->form_validation->valid_ip('127.0.0.1', 'ipv4'));
		$this->assertTrue($this->form_validation->valid_ip('2001:0db8:85a3:0000:0000:8a2e:0370:7334'));
		$this->assertTrue($this->form_validation->valid_ip('2001:0db8:85a3:0000:0000:8a2e:0370:7334', 'ipv6'));

		$this->assertFalse($this->form_validation->valid_ip('2001:0db8:85a3:0000:0000:8a2e:0370:7334', 'ipv4'));
		$this->assertFalse($this->form_validation->valid_ip('127.0.0.1', 'ipv6'));
		$this->assertFalse($this->form_validation->valid_ip('H001:0db8:85a3:0000:0000:8a2e:0370:7334'));
		$this->assertFalse($this->form_validation->valid_ip('127.0.0.259'));
	}

	public function test_rule_valid_base64()
	{
		$this->assertTrue($this->form_validation->valid_base64(base64_encode('string')));

		$this->assertFalse($this->form_validation->valid_base64('FA08GG'));
	}

	public function test_set_data()
	{
		// Reset test environment
		$_POST = array();
		$this->form_validation->reset_validation();
		$data = array('field' => 'some_data');
		$this->form_validation->set_data($data);
		$this->form_validation->set_rules('field', 'label', 'required');
		$this->assertTrue($this->form_validation->run());

		// Test with empty array
		$_POST = array();
		$this->form_validation->reset_validation();
		$data = array('field' => 'some_data');
		$this->form_validation->set_data($data);
		// This should do nothing. Old data will still be used
		$this->form_validation->set_data(array());
		$this->form_validation->set_rules('field', 'label', 'required');
		$this->assertTrue($this->form_validation->run());
	}

	public function test_set_message()
	{
		// Reset test environment
		$_POST = array();
		$this->form_validation->reset_validation();
		$err_message = 'What a terrible error!';
		$rules = array(
			array(
				'field' => 'req_field',
				'label' => 'label',
				'rules' => 'required'
			)
		);
		$errorless_data = array('req_field' => 'some text');
		$erroneous_data = array('req_field' => '');

		$this->form_validation->set_message('required', $err_message);
		$this->form_validation->set_data($erroneous_data);
		$this->form_validation->set_rules($rules);
		$this->form_validation->run();
		$this->assertEquals('<p>'.$err_message.'</p>', $this->form_validation->error('req_field'));

		$this->form_validation->reset_validation();
		$this->form_validation->set_message('required', $err_message);
		$this->form_validation->set_data($errorless_data);
		$this->form_validation->set_rules($rules);
		$this->form_validation->run();
		$this->assertEquals('', $this->form_validation->error('req_field'));
	}

	public function test_set_error_delimiters()
	{
		$this->form_validation->reset_validation();
		$prefix = '<div class="error">';
		$suffix = '</div>';
		$this->form_validation->set_error_delimiters($prefix, $suffix);
		$this->form_validation->set_rules('foo', 'label', 'required');
		$_POST = array('foo' => '');
		$this->form_validation->run();
		$error_msg = $this->form_validation->error('foo');

		$this->assertTrue(strrpos($error_msg, $prefix) === 0);
		$this->assertTrue(strrpos($error_msg, $suffix, -strlen($suffix)) === (strlen($error_msg) - strlen($suffix)));
	}

	public function test_error_array()
	{
		$this->form_validation->reset_validation();
		$error_message = 'What a terrible error!';
		$this->form_validation->set_message('required', $error_message);
		$this->form_validation->set_rules('foo', 'label', 'required');
		$_POST = array('foo' => '');
		$this->form_validation->run();
		$error_array = $this->form_validation->error_array();
		$this->assertEquals($error_message, $error_array['foo']);
	}

	public function test_error_string()
	{
		$this->form_validation->reset_validation();
		$error_message = 'What a terrible error!';
		$prefix_default = '<foo>';
		$suffix_default = '</foo>';
		$prefix_test = '<bar>';
		$suffix_test = '</bar>';
		$this->form_validation->set_error_delimiters($prefix_default, $suffix_default);
		$this->form_validation->set_message('required', $error_message);
		$this->form_validation->set_rules('foo', 'label', 'required');
		$_POST = array('foo' => '');
		$this->form_validation->run();

		$this->assertEquals($prefix_default.$error_message.$suffix_default."\n", $this->form_validation->error_string());
		$this->assertEquals($prefix_test.$error_message.$suffix_default."\n", $this->form_validation->error_string($prefix_test, ''));
		$this->assertEquals($prefix_default.$error_message.$suffix_test."\n", $this->form_validation->error_string('', $suffix_test));
		$this->assertEquals($prefix_test.$error_message.$suffix_test."\n", $this->form_validation->error_string($prefix_test, $suffix_test));

		$this->form_validation->reset_validation();
		$this->form_validation->set_rules('foo', 'label', 'required');
		$_POST = array('foo' => 'bar');
		$this->form_validation->run();
		$this->assertEquals('', $this->form_validation->error_string());
	}

	public function test_run()
	{
		// form_validation->run() is tested in many of the other unit tests
		// This test will only test run(group='') when group is not empty
		$config = array(
			'pass' => array(
				array(
					'field' => 'username',
					'label' => 'user',
					'rules' => 'alpha_numeric'
				)
			),
			'fail' => array(
				array(
					'field' => 'username',
					'label' => 'user',
					'rules' => 'alpha'
				)
			)
		);
		$_POST = array('username' => 'foo42');
		$form_validation = new CI_Form_validation($config);
		$this->assertTrue($form_validation->run('pass'));

		$form_validation = new CI_Form_validation($config);
		$this->assertFalse($form_validation->run('fail'));
	}

	public function test_set_rules_exception()
	{
		$this->setExpectedException('BadMethodCallException');
		$this->form_validation->set_rules('foo', 'bar');
	}

	public function test_has_rule()
	{
		$this->form_validation->reset_validation();
		$this->form_validation->set_rules('foo', 'label', 'required');

		$this->assertTrue($this->form_validation->has_rule('foo'));
		$this->assertFalse($this->form_validation->has_rule('bar'));
	}

	public function test_set_value()
	{
		$this->form_validation->reset_validation();
		$default = 'default';
		$this->form_validation->set_rules('foo', 'label', 'required');
		$this->form_validation->set_rules('bar[]', 'label', 'required');

		// No post data yet: should return the default value provided
		$this->assertEquals($default, $this->form_validation->set_value('foo', $default));
		$_POST = array('foo' => 'foo', 'bar' => array('bar1', 'bar2'));
		$this->form_validation->run();
		$this->assertEquals('foo', $this->form_validation->set_value('foo', $default));
		$this->assertEquals('bar1', $this->form_validation->set_value('bar[]', $default));
		$this->assertEquals('bar2', $this->form_validation->set_value('bar[]', $default));
	}

	public function test_set_select()
	{
		// Test 1: No options selected
		$this->form_validation->reset_validation();
		$_POST = array();
		$this->form_validation->run();

		$this->assertEquals('', $this->form_validation->set_select('select', 'foo'));
		$this->assertEquals(' selected="selected"', $this->form_validation->set_select('select', 'bar', TRUE));

		// Test 2: 1 option selected
		$this->form_validation->reset_validation();
		$this->form_validation->set_rules('select', 'label', 'alpha_numeric');
		$_POST = array('select' => 'foo');
		$this->form_validation->run();

		$this->assertEquals(' selected="selected"', $this->form_validation->set_select('select', 'foo'));
		$this->assertEquals(' selected="selected"', $this->form_validation->set_select('select', 'foo', TRUE));
		$this->assertEquals('', $this->form_validation->set_select('select', 'bar'));
		$this->assertEquals('', $this->form_validation->set_select('select', 'bar', TRUE));

		// Test 3: Multiple options selected
		$this->form_validation->reset_validation();
		$this->form_validation->set_rules('select[]', 'label', 'alpha_numeric');
		$_POST = array('select' => array('foo', 'bar'));
		$this->form_validation->run();

		$this->assertEquals(' selected="selected"', $this->form_validation->set_select('select[]', 'foo'));
		$this->assertEquals(' selected="selected"', $this->form_validation->set_select('select[]', 'foo', TRUE));
		$this->assertEquals(' selected="selected"', $this->form_validation->set_select('select[]', 'bar'));
		$this->assertEquals(' selected="selected"', $this->form_validation->set_select('select[]', 'bar', TRUE));
		$this->assertEquals('', $this->form_validation->set_select('select[]', 'foobar'));
		$this->assertEquals('', $this->form_validation->set_select('select[]', 'foobar', TRUE));
	}

	public function test_set_radio()
	{
		// Test 1: No options selected
		$this->form_validation->reset_validation();
		$_POST = array();
		$this->form_validation->run();

		$this->assertEquals('', $this->form_validation->set_radio('select', 'foo'));
		// Default should only work when no rules are set
		$this->assertEquals(' checked="checked"', $this->form_validation->set_radio('select', 'bar', TRUE));

		// Test 2: 1 option selected
		$this->form_validation->reset_validation();
		$this->form_validation->set_rules('select', 'label', 'alpha_numeric');
		$_POST = array('select' => 'foo');
		$this->form_validation->run();

		$this->assertEquals(' checked="checked"', $this->form_validation->set_radio('select', 'foo'));
		$this->assertEquals(' checked="checked"', $this->form_validation->set_radio('select', 'foo', TRUE));
		$this->assertEquals('', $this->form_validation->set_radio('select', 'bar'));
		$this->assertEquals('', $this->form_validation->set_radio('select', 'bar', TRUE));

		// Test 3: Multiple options checked
		$this->form_validation->reset_validation();
		$this->form_validation->set_rules('select[]', 'label', 'alpha_numeric');
		$_POST = array('select' => array('foo', 'bar'));
		$this->form_validation->run();

		$this->assertEquals(' checked="checked"', $this->form_validation->set_radio('select[]', 'foo'));
		$this->assertEquals(' checked="checked"', $this->form_validation->set_radio('select[]', 'foo', TRUE));
		$this->assertEquals(' checked="checked"', $this->form_validation->set_radio('select[]', 'bar'));
		$this->assertEquals(' checked="checked"', $this->form_validation->set_radio('select[]', 'bar', TRUE));
		$this->assertEquals('', $this->form_validation->set_radio('select[]', 'foobar'));
		$this->assertEquals('', $this->form_validation->set_radio('select[]', 'foobar', TRUE));
	}

	public function test_set_checkbox()
	{
		// Test 1: No options selected
		$this->form_validation->reset_validation();
		$_POST = array();
		$this->form_validation->run();

		$this->assertEquals('', $this->form_validation->set_checkbox('select', 'foo'));
		$this->assertEquals(' checked="checked"', $this->form_validation->set_checkbox('select', 'bar', TRUE));

		// Test 2: 1 option selected
		$this->form_validation->reset_validation();
		$this->form_validation->set_rules('select', 'label', 'alpha_numeric');
		$_POST = array('select' => 'foo');
		$this->form_validation->run();

		$this->assertEquals(' checked="checked"', $this->form_validation->set_checkbox('select', 'foo'));
		$this->assertEquals(' checked="checked"', $this->form_validation->set_checkbox('select', 'foo', TRUE));
		$this->assertEquals('', $this->form_validation->set_checkbox('select', 'bar'));
		$this->assertEquals('', $this->form_validation->set_checkbox('select', 'bar', TRUE));

		// Test 3: Multiple options selected
		$this->form_validation->reset_validation();
		$this->form_validation->set_rules('select[]', 'label', 'alpha_numeric');
		$_POST = array('select' => array('foo', 'bar'));
		$this->form_validation->run();

		$this->assertEquals(' checked="checked"', $this->form_validation->set_checkbox('select[]', 'foo'));
		$this->assertEquals(' checked="checked"', $this->form_validation->set_checkbox('select[]', 'foo', TRUE));
		$this->assertEquals(' checked="checked"', $this->form_validation->set_checkbox('select[]', 'bar'));
		$this->assertEquals(' checked="checked"', $this->form_validation->set_checkbox('select[]', 'bar', TRUE));
		$this->assertEquals('', $this->form_validation->set_checkbox('select[]', 'foobar'));
		$this->assertEquals('', $this->form_validation->set_checkbox('select[]', 'foobar', TRUE));
	}

	public function test_regex_match()
	{
		$regex = '/f[a-zA-Z]+/';
		$this->assertTrue($this->form_validation->regex_match('foo', $regex));
		$this->assertFalse($this->form_validation->regex_match('bar', $regex));
	}

	public function test_prep_for_form()
	{
		$this->form_validation->reset_validation();
		$error_msg_unprepped = '<error =\'foobar\'">';
		$error_msg_prepped = '&lt;error =&#39;foobar&#39;&quot;&gt;';
		$this->form_validation->set_rules('foo', 'label', 'required', array('required' => $error_msg_unprepped));
		$_POST = array('foo' => '');
		$this->form_validation->run();
		$error_arr = $this->form_validation->error_array();

		$this->assertEquals('', $this->form_validation->prep_for_form(''));
		$this->assertEquals(array('foo' => $error_msg_prepped), $this->form_validation->prep_for_form($error_arr));
	}

	public function test_prep_url()
	{
		$this->assertEquals('', $this->form_validation->prep_url(''));
		$this->assertEquals('http://codeigniter.com', $this->form_validation->prep_url('codeigniter.com'));
		$this->assertEquals('https://codeigniter.com', $this->form_validation->prep_url('https://codeigniter.com'));
		$this->assertEquals('http://codeigniter.com', $this->form_validation->prep_url('http://codeigniter.com'));
		$this->assertEquals('http://www.codeigniter.com', $this->form_validation->prep_url('www.codeigniter.com'));
	}

	public function test_encode_php_tags()
	{
		$this->assertEquals("&lt;?php", $this->form_validation->encode_php_tags('<?php'));
		$this->assertEquals('?&gt;', $this->form_validation->encode_php_tags('?>'));
	}

	/**
	 * Run rules
	 *
	 * Helper method to set rules and run them at once, not
	 * an actual test case.
	 */
	public function run_rules($rules, $values)
	{
		$this->form_validation->reset_validation();
		$_POST = array();

		$this->form_validation->set_rules($rules);
		foreach ($values as $field => $value)
		{
			$_POST[$field] = $value;
		}

		return $this->form_validation->run();
	}
}
