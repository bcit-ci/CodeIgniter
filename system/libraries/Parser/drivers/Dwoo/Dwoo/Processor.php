<?php

/**
 * base class for processors
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
abstract class Dwoo_Processor
{
	/**
	 * the compiler instance that runs this processor
	 *
	 * @var Dwoo
	 */
	protected $compiler;

	/**
	 * constructor, if you override it, call parent::__construct($dwoo); or assign
	 * the dwoo instance yourself if you need it
	 *
	 * @param Dwoo $dwoo the dwoo instance that runs this plugin
	 */
	public function __construct(Dwoo_Compiler $compiler)
	{
		$this->compiler = $compiler;
	}

	/**
	 * processes the input and returns it filtered
	 *
	 * @param string $input the template to process
	 * @return string
	 */
	abstract public function process($input);
}
