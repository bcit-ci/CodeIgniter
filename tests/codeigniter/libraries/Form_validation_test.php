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
        
        $this->ci_instance_var('load', $ldr);
        $this->helper('form');
              
    }
    
    public function test___construct()
    {
        $this->form_validation = new CI_Form_validation();

        $this->assertNotNull($this->form_validation);
    }
    
    public function test__construct_rules() 
    {
        
    }
    
    public function test_
    
}
