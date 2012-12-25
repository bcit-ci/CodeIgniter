<?php

namespace Rain\Tpl;

/**
 * Basic Rain tpl exception.
 */
class Exception extends \Exception {

    /**
     * Path of template file with error.
     */
    protected $templateFile = '';

    /**
     * Handles path of template file with error.
     *
     * @param string | null $templateFile
     * @return \Rain\Tpl_Exception | string
     */  	
    public function templateFile($templateFile){
        if(is_null($templateFile))
            return $this->templateFile;

        $this->templateFile = (string) $templateFile;
        return $this;
    }
}

// -- end
