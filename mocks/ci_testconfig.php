<?php

class CI_TestConfig extends CI_Config
{
    public $config = array();
    public $_config_paths = array(APPPATH);
    public $loaded = array();

    public function item($key, $index = '')
    {
        return isset($this->config[$key]) ? $this->config[$key] : false;
    }

    public function load($file = '', $use_sections = false, $fail_gracefully = false)
    {
        $this->loaded[] = $file;
        return true;
    }
}
