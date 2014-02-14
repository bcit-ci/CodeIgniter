<?php

class Model_test extends CI_TestCase {

	private $ci_obj;

	public function set_up()
	{
		$loader = $this->ci_core_class('loader');
		$this->load = new $loader();
		$this->ci_obj = $this->ci_instance();
		$this->ci_set_core_class('model', 'CI_Model');

		$model_code =<<<MODEL
<?php
class Test_model extends CI_Model {

	public \$property = 'foo';

}
MODEL;

		$this->ci_vfs_create('Test_model', $model_code, $this->ci_app_root, 'models');
		$this->load->model('test_model');
	}

	// --------------------------------------------------------------------

	public function test__get()
	{
		$this->assertEquals('foo', $this->ci_obj->test_model->property);

		$this->ci_obj->controller_property = 'bar';
		$this->assertEquals('bar', $this->ci_obj->test_model->controller_property);
	}

}