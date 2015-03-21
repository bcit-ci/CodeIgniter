<?php

class Form_validation_test extends CI_TestCase {

	public function set_up()
	{
		$_SERVER['REQUEST_METHOD'] = 'POST';

		// Create a mock loader since load->helper() looks in the wrong directories for unit tests,
		// We'll use CI_TestCase->helper() instead
		$loader = $this->getMock('CI_Loader', array('helper'));
		// At current, CI_Form_Validation only calls load->helper("form")
		// Assert this so that if that changes this fails fast
		$loader->method('helper')
				->with($this->equalTo('form'));
		// Same applies for lang
		$lang = $this->getMock('CI_Lang', array('load'));

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

	public function test___construct()
	{
		$form_validation = new CI_Form_validation();
		$this->assertNotNull($form_validation);
	}	

	public function test_rule_required()
	{
		$this->assertTrue($this->run_rule('required', ' someValue'));

		$this->assertFalse($this->run_rule('required', ''));
		$this->assertFalse($this->run_rule('required', ' '));
	}

	public function test_rule_matches()
	{
		// Empty input should pass any rule unless required is also specified
		$_POST['to_match'] = 'sample';
		$this->assertTrue($this->run_rule('matches[to_match]', '', FALSE));
		$_POST['to_match'] = 'sample';
		$this->assertTrue($this->run_rule('matches[to_match]', 'sample', FALSE));

		$_POST['to_match'] = 'sample';
		$this->assertFalse($this->run_rule('matches[to_match]', 'Sample', FALSE));
		$_POST['to_match'] = 'sample';
		$this->assertFalse($this->run_rule('matches[to_match]', ' sample', FALSE));
	}

	public function test_rule_differs()
	{
		// Empty input should pass any rule unless required is also specified
		$_POST['to_differ'] = 'sample';
		$this->assertTrue($this->run_rule('differs[to_differ]', '', FALSE));
		$_POST['to_differ'] = 'sample';
		$this->assertTrue($this->run_rule('differs[to_differ]', 'Sample', FALSE));
		$_POST['to_differ'] = 'sample';
		$this->assertTrue($this->run_rule('differs[to_differ]', ' sample', FALSE));

		$_POST['to_differ'] = 'sample';
		$this->assertFalse($this->run_rule('differs[to_differ]', 'sample', FALSE));
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
		// Empty input should pass any rule unless required is also specified
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
		$this->assertTrue($this->form_validation->valid_url('http://codeigniter.eu'));
				
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
		$data = array();		
		$this->form_validation->reset_validation();
		$this->form_validation->set_data($data);
		$this->form_validation->set_rules('field', 'label', 'required');
		$this->assertFalse($this->form_validation->run());		
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

	public function run_rule($rule, $test_value, $reset_post = TRUE)
	{
//        $this->markTestSkipped('Not designed to be a unit test');
		$this->form_validation->reset_validation();
		if ($reset_post === TRUE)
		{
			$_POST = array();
		}

		$this->form_validation->set_rules('field', 'name', $rule);
		$_POST['field'] = $test_value;
		return $this->form_validation->run();
	}

}
