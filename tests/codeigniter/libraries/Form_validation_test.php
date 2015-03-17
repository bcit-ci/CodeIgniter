<?php

class Form_validation_test extends CI_TestCase {
    
    public function set_up()
    {          
        // Create a mock loader since load->helper() looks in the wrong directories for unit tests,
        // We'll use CI_TestCase->helper() instead
        $ldr = $this->getMock('CI_Loader', array('helper'));
        // At current, CI_Form_Validation only calls load->helper("form")
        // Assert this so that if that changes this fails fast
        $ldr->expects($this->once())
                ->method('helper')
                ->with($this->equalTo('form'));
        
        // Same applies for lang
        $lang = $this->getMock('CI_Lang', array('load'));
        
        // Setup CI_Input
        // Set server variable to GET as default, since this will leave unset in STDIN env
        $_SERVER['REQUEST_METHOD'] = 'GET';

        // Set config for Input class
        $this->ci_set_config('allow_get_array',	TRUE);
        $this->ci_set_config('global_xss_filtering', FALSE);
        $this->ci_set_config('csrf_protection', FALSE);

        $security = new Mock_Core_Security();

        $this->ci_set_config('charset', 'UTF-8');
        $utf8 = new Mock_Core_Utf8();

        $inp = new Mock_Core_Input($security, $utf8);
        
        $this->ci_instance_var('lang', $lang);
        $this->ci_instance_var('load', $ldr);
        $this->ci_instance_var('input', $inp);
        
        $this->lang('form_validation');
        $this->helper('form');
    }
    
    public function test___construct()
    {
        $this->form_validation = new CI_Form_validation();

        $this->assertNotNull($this->form_validation);
    }
    
    public function test_rules_valid() 
    {
        $this->form_validation = new CI_Form_validation();
        
        $valid_posts = array(
            'required' => array('required',' !'),
            'matches[sample]' => 'sample',
            'differs[sample]' => 'differ',
            'min_length[4]' => array('is_more_than_4', '1234', '   1'),
            'max_length[8]' => array('less_8', '12345678'),
            'exact_length[5]' => '12345',
            'greater_than[-5]' => array('-4','0','123124451'),
            'greater_than_equal_to[8]' => array('8', '99'),
            'less_than[0]' => '-1',
            'less_than_equal_to[5]' => array('-5', '5'),
            'in_list[red,blue,green]' => array('red', 'blue','green'),
            'alpha' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHLIJKLMNOPQRSTUVWXYZ',
            'alpha_numeric' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHLIJKLMNOPQRSTUVWXYZ0123456789',
            'alpha_numeric_spaces' => ' abcdefghijklmnopqrstuvwxyzABCDEFGHLIJKLMNOPQRSTUVWXYZ0123456789',
            'alpha_dash' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHLIJKLMNOPQRSTUVWXYZ0123456789-_',
            'numeric' => '0123456789',
            'integer' => array('0', '-1231', '987234'),
            'decimal' => array('0.123', '1.0'),
            'is_natural' => '0',
            'is_natural_no_zero' => '1',
            'valid_url' => array('www.codeigniter.com','http://codeigniter.eu'),
            'valid_email' => 'email@sample.com',
            'valid_emails' => '1@sample.com,2@sample.com',
            'valid_ip[ipv4]' => '127.0.0.1',
            'valid_ip[ipv6]' => '2001:0db8:85a3:0000:0000:8a2e:0370:7334',
            'valid_base64' => 'string'
            );
          
        // Loop through each rule and test
        foreach ($valid_posts as $rule => $value) {
            // Reset $_POST
            $_POST = array();
            
            if (is_array($value)) {
                foreach($value as $item) {
//                    printf("%s => %s\n", $rule, $item);
                    $this->form_validation->set_rules('field', 'name', $rule);
                    $_POST['field'] = $item;
                    $this->assertTrue($this->form_validation->run());
                    $this->form_validation->reset_validation();
                }
            }
            else {
//                printf("%s => %s\n", $rule, $value);
                $this->form_validation->set_rules('field', 'name', $rule);
                $_POST['field'] = $value;
                $this->assertTrue($this->form_validation->run());
                $this->form_validation->reset_validation();
            }
        }
    }
    
    public function test_rules_invalid()
    {
        $this->form_validation = new CI_Form_validation();
        
        $invalid_posts = array(
            'required' => array('',' '),
            'matches[sample]' => 'Sample',
            'differ[sample]' => 'sample',
            'min_length[4]' => array('123', ''),
            'max_length[8]' => array('more_than_8', '12345678 '),
            'exact_length[5]' => ' 12345',
            'greater_than[-5]' => array('-5, -12415'),
            'greater_than_equal_to[8]' => array('7', '0'),
            'less_than[0]' => '0',
            'less_than_equal_to[5]' => array('6', '98234'),
            'in_list[red,blue,green]' => array(' red', 'Blue','failz'),
            'alpha' => array('*', ' a', '1'),
            'alpha_numeric' => array('a1 ', '*', '1-'),
            'alpha_numeric_spaces' => array('a1*', '  ~'),
            'alpha_dash' => array('a b', '*'),
            'numeric' => array('a', ''),
            'integer' => array('0.123', '1a', ''),
            'decimal' => array('1', 'a',''),
            'is_natural' => array('1.2','aA',''),
            'is_natural_no_zero' => array('0','1.2',''),
            'valid_url' => array('codeigniter.com','nosite', ''),
            'valid_email' => '@sample.com',
            'valid_emails' => '@sample.com,2@sample.com,validemail@email.ca',
            'valid_ip[ipv4]' => '257.0.0.1',
            'valid_ip[ipv6]' => 'A001:0db8:85a3:0000:0000:8a2e:0370:7334',
            );
          
        // Loop through each rule and test
        foreach ($invalid_posts as $rule => $value) {
            // Reset $_POST
            $_POST = array();
            
            if (is_array($value)) {
                foreach($value as $item) {
                    printf("%s => %s\n", $rule, $item);
                    $this->form_validation->set_rules('field', 'name', $rule);
                    $_POST['field'] = $item;
                    $this->assertFalse($this->form_validation->run());
                    $this->form_validation->reset_validation();
                }
            }
            else {
                printf("%s => %s\n", $rule, $value);
                $this->form_validation->set_rules('field', 'name', $rule);
                $_POST['field'] = $value;
                $this->assertFalse($this->form_validation->run());
                $this->form_validation->reset_validation();
            }
        }
    }
   
}
