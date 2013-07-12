<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Hook class
 */
class Hooks {

    /**
     * Capture the output in cli mode
     * @return type
     */
    public function captureOutput()
    {
        $this->CI =& get_instance();
        $output = $this->CI->output->get_output();
        if (PHP_SAPI != 'cli') {
            echo $output;
        }
    }

}