<?php

/**
 * dwoo compilation exception class
 *
 * This software is provided 'as-is', without any express or implied warranty.
 * In no event will the authors be held liable for any damages arising from the use of this software.
 *
 * @author     Jordi Boggiano <j.boggiano@seld.be>
 * @copyright  Copyright (c) 2008, Jordi Boggiano
 * @license    http://dwoo.org/LICENSE   Modified BSD License
 * @link       http://dwoo.org/
 * @version    1.0.0
 * @date       2008-10-23
 * @package    Dwoo
 */
class Dwoo_Compilation_Exception extends Dwoo_Exception
{
	protected $compiler;
	protected $template;

	public function __construct(Dwoo_Compiler $compiler, $message)
	{
		$this->compiler = $compiler;
		$this->template = $compiler->getDwoo()->getTemplate();
		parent::__construct('Compilation error at line '.$compiler->getLine().' in "'.$this->template->getResourceName().':'.$this->template->getResourceIdentifier().'" : '.$message);
	}

	public function getCompiler()
	{
		return $this->compiler;
	}

	public function getTemplate()
	{
		return $this->template;
	}
}
