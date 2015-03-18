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
        $loader->expects($this->once())
                ->method('helper')
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
		$this->assertNotNull($this->form_validation);
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
		// Empty input should pass any rule unless required is also specified
		$this->assertTrue($this->run_rule('min_length[34]', ''));
		$this->assertTrue($this->run_rule('min_length[2]', '12'));
		$this->assertTrue($this->run_rule('min_length[2]', ' 2'));

		$this->assertFalse($this->run_rule('min_length[2]', '1'));
		$this->assertFalse($this->run_rule('min_length[4]|required', ''));
	}

	public function test_rule_max_length()
	{
		// Empty input should pass any rule unless required is also specified
		$this->assertTrue($this->run_rule('max_length[4]', ''));
		$this->assertTrue($this->run_rule('max_length[4]', '1234'));

		$this->assertFalse($this->run_rule('max_length[4]', '12345'));
	}

	public function test_rule_exact_length()
	{
		// Empty input should pass any rule unless required is also specified
		$this->assertTrue($this->run_rule('exact_length[4]', ''));
		$this->assertTrue($this->run_rule('exact_length[4]', '1234'));

		$this->assertFalse($this->run_rule('exact_length[4]', '123'));
		$this->assertFalse($this->run_rule('exact_length[4]', '12345'));
	}

	public function test_rule_greater_than()
	{
		// Empty input should pass any rule unless required is also specified
		$this->assertTrue($this->run_rule('greater_than[-10]', ''));
		$this->assertTrue($this->run_rule('greater_than[-10]', '-9'));

		$this->assertFalse($this->run_rule('greater_than[-10]', '-99'));
		$this->assertFalse($this->run_rule('greater_than[-10]', 'a'));
	}

	public function test_rule_greater_than_equal_to()
	{
		// Empty input should pass any rule unless required is also specified
		$this->assertTrue($this->run_rule('greater_than_equal_to[0]', ''));
		$this->assertTrue($this->run_rule('greater_than_equal_to[0]', '0'));
		$this->assertTrue($this->run_rule('greater_than_equal_to[0]', '1'));

		$this->assertFalse($this->run_rule('greater_than_equal_to[0]', '-1'));
		$this->assertFalse($this->run_rule('greater_than_equal_to[0]', 'a'));
	}

	public function test_rule_less_than()
	{
		// Empty input should pass any rule unless required is also specified
		$this->assertTrue($this->run_rule('less_than[0]', ''));
		$this->assertTrue($this->run_rule('less_than[0]', '-1'));

		$this->assertFalse($this->run_rule('less_than[0]', '0'));
		$this->assertFalse($this->run_rule('less_than[0]', 'a'));
	}

	public function test_rule_less_than_equal_to()
	{
		// Empty input should pass any rule unless required is also specified
		$this->assertTrue($this->run_rule('less_than_equal_to[0]', ''));
		$this->assertTrue($this->run_rule('less_than_equal_to[0]', '-1'));
		$this->assertTrue($this->run_rule('less_than_equal_to[0]', '0'));

		$this->assertFalse($this->run_rule('less_than_equal_to[0]', '1'));
		$this->assertFalse($this->run_rule('less_than_equal_to[0]', 'a'));
	}

	public function test_rule_in_list()
	{
		// Empty input should pass any rule unless required is also specified
		$this->assertTrue($this->run_rule('in_list[red,Blue,123]', ''));
		$this->assertTrue($this->run_rule('in_list[red,Blue,123]', 'red'));
		$this->assertTrue($this->run_rule('in_list[red,Blue,123]', 'Blue'));
		$this->assertTrue($this->run_rule('in_list[red,Blue,123]', '123'));

		$this->assertFalse($this->run_rule('in_list[red,Blue,123]', 'Red'));
		$this->assertFalse($this->run_rule('in_list[red,Blue,123]', 'blue'));
		$this->assertFalse($this->run_rule('in_list[red,Blue,123]', ' red'));
	}

	public function test_rule_alpha()
	{
		// Empty input should pass any rule unless required is also specified
		$this->assertTrue($this->run_rule('alpha', ''));
		$this->assertTrue($this->run_rule('alpha', 'abcdefghijklmnopqrstuvwxyzABCDEFGHLIJKLMNOPQRSTUVWXYZ'));

		$this->assertFalse($this->run_rule('alpha', 'abcdefghijklmnopqrstuvwxyzABCDEFGHLIJKLMNOPQRSTUVWXYZ '));
		$this->assertFalse($this->run_rule('alpha', 'abcdefghijklmnopqrstuvwxyzABCDEFGHLIJKLMNOPQRSTUVWXYZ1'));
		$this->assertFalse($this->run_rule('alpha', 'abcdefghijklmnopqrstuvwxyzABCDEFGHLIJKLMNOPQRSTUVWXYZ*'));
	}

	public function test_rule_alpha_numeric()
	{
		// Empty input should pass any rule unless required is also specified
		$this->assertTrue($this->run_rule('alpha_numeric', ''));
		$this->assertTrue($this->run_rule('alpha_numeric', 'abcdefghijklmnopqrstuvwxyzABCDEFGHLIJKLMNOPQRSTUVWXYZ0123456789'));

		$this->assertFalse($this->run_rule('alpha_numeric', 'abcdefghijklmnopqrstuvwxyzABCDEFGHLIJKLMNOPQRSTUVWXYZ0123456789\ '));
		$this->assertFalse($this->run_rule('alpha_numeric', 'abcdefghijklmnopqrstuvwxyzABCDEFGHLIJKLMNOPQRSTUVWXYZ0123456789_'));
	}

	public function test_rule_alpha_numeric_spaces()
	{
		// Empty input should pass any rule unless required is also specified
		$this->assertTrue($this->run_rule('alpha_numeric_spaces', ''));
		$this->assertTrue($this->run_rule('alpha_numeric_spaces', ' abcdefghijklmnopqrstuvwxyzABCDEFGHLIJKLMNOPQRSTUVWXYZ0123456789'));

		$this->assertFalse($this->run_rule('alpha_numeric_spaces', ' abcdefghijklmnopqrstuvwxyzABCDEFGHLIJKLMNOPQRSTUVWXYZ0123456789_'));
	}

	public function test_rule_alpha_dash()
	{
		// Empty input should pass any rule unless required is also specified
		$this->assertTrue($this->run_rule('alpha_dash', ''));
		$this->assertTrue($this->run_rule('alpha_dash', 'abcdefghijklmnopqrstuvwxyzABCDEFGHLIJKLMNOPQRSTUVWXYZ0123456789-_'));

		$this->assertFalse($this->run_rule('alpha_dash', 'abcdefghijklmnopqrstuvwxyzABCDEFGHLIJKLMNOPQRSTUVWXYZ0123456789-_\ '));
	}

	public function test_rule_numeric()
	{
		// Empty input should pass any rule unless required is also specified
		$this->assertTrue($this->run_rule('numeric', ''));
		$this->assertTrue($this->run_rule('numeric', '0'));
		$this->assertTrue($this->run_rule('numeric', '12314'));
		$this->assertTrue($this->run_rule('numeric', '-42'));

		$this->assertFalse($this->run_rule('numeric', '123a'));
	}

	public function test_rule_integer()
	{
		// Empty input should pass any rule unless required is also specified
		$this->assertTrue($this->run_rule('integer', ''));
		$this->assertTrue($this->run_rule('integer', '0'));
		$this->assertTrue($this->run_rule('integer', '42'));

		$this->assertFalse($this->run_rule('integer', '124a'));
		$this->assertFalse($this->run_rule('integer', '1.9'));
	}

	public function test_rule_decimal()
	{
		// Empty input should pass any rule unless required is also specified
		$this->assertTrue($this->run_rule('decimal', ''));
		$this->assertTrue($this->run_rule('decimal', '1.0'));
		$this->assertTrue($this->run_rule('decimal', '0.98'));

		$this->assertFalse($this->run_rule('decimal', '1.0a'));
		$this->assertFalse($this->run_rule('decimal', '-i'));
	}

	public function test_rule_is_natural()
	{
		// Empty input should pass any rule unless required is also specified
		$this->assertTrue($this->run_rule('is_natural', ''));
		$this->assertTrue($this->run_rule('is_natural', '0'));
		$this->assertTrue($this->run_rule('is_natural', '12'));

		$this->assertFalse($this->run_rule('is_natural', '42a'));
	}

	public function test_rule_is_natural_no_zero()
	{
		// Empty input should pass any rule unless required is also specified
		$this->assertTrue($this->run_rule('is_natural_no_zero', ''));
		$this->assertTrue($this->run_rule('is_natural_no_zero', '42'));

		$this->assertFalse($this->run_rule('is_natural_no_zero', '0'));
		$this->assertFalse($this->run_rule('is_natural_no_zero', '42a'));
	}

	public function test_rule_valid_url()
	{
		// Empty input should pass any rule unless required is also specified
		$this->assertTrue($this->run_rule('valid_url', ''));
		$this->assertTrue($this->run_rule('valid_url', 'www.codeigniter.com'));
		$this->assertTrue($this->run_rule('valid_url', 'http://codeigniter.eu'));

		$this->assertFalse($this->run_rule('valid_url', 'code igniter'));
	}

	public function test_rule_valid_email()
	{
		// Empty input should pass any rule unless required is also specified
		$this->assertTrue($this->run_rule('valid_email', ''));
		$this->assertTrue($this->run_rule('valid_email', 'email@sample.com'));

		$this->assertFalse($this->run_rule('valid_email', '@sample.com'));
	}

	public function test_rule_valid_emails()
	{
		// Empty input should pass any rule unless required is also specified
		$this->assertTrue($this->run_rule('valid_emails', ''));
		$this->assertTrue($this->run_rule('valid_emails', '1@sample.com,2@sample.com'));

		$this->assertFalse($this->run_rule('valid_emails', '@sample.com,2@sample.com,validemail@email.ca'));
	}

	public function test_rule_valid_ip()
	{
		// Empty input should pass any rule unless required is also specified
		$this->assertTrue($this->run_rule('valid_ip', ''));
		$this->assertTrue($this->run_rule('valid_ip', '127.0.0.1'));
		$this->assertTrue($this->run_rule('valid_ip[ipv4]', '127.0.0.1'));
		$this->assertTrue($this->run_rule('valid_ip', '2001:0db8:85a3:0000:0000:8a2e:0370:7334'));
		$this->assertTrue($this->run_rule('valid_ip[ipv6]', '2001:0db8:85a3:0000:0000:8a2e:0370:7334'));

		$this->assertFalse($this->run_rule('valid_ip[ipv4]', '2001:0db8:85a3:0000:0000:8a2e:0370:7334'));
		$this->assertFalse($this->run_rule('valid_ip[ipv6]', '127.0.0.1'));
		$this->assertFalse($this->run_rule('valid_ip', 'H001:0db8:85a3:0000:0000:8a2e:0370:7334'));
		$this->assertFalse($this->run_rule('valid_ip', '127.0.0.259'));
	}

	public function test_rule_valid_base64()
	{
		// Empty input should pass any rule unless required is also specified
		$this->assertTrue($this->run_rule('valid_base64', ''));
		$this->assertTrue($this->run_rule('valid_base64', base64_encode('string')));
		
		$this->assertFalse($this->run_rule('valid_base64', "FA08GG"));
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
