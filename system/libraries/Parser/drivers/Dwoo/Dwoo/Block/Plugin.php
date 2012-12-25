<?php

/**
 * base class for block plugins
 *
 * you have to implement the <em>init()</em> method, it will receive the parameters that
 * are in the template code and is called when the block starts
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
abstract class Dwoo_Block_Plugin extends Dwoo_Plugin
{
	/**
	 * stores the contents of the block while it runs
	 *
	 * @var string
	 */
	protected $buffer = '';

	/**
	 * buffers input, override only if necessary
	 *
	 * @var string $input the content that must be buffered
	 */
	public function buffer($input)
	{
		$this->buffer .= $input;
	}

	// initialization code, receives the parameters from {block param1 param2}
	// public function init($arg, $arg, ...);

	/**
	 * called when the block ends, this is most of the time followed right away by a call
	 * of <em>process()</em> but not always, so this should be used to do any shutdown operations on the
	 * block object, if required.
	 */
	public function end()
	{
	}

	/**
	 * called when the block output is required by a parent block
	 *
	 * this must read $this->buffer and return it processed
	 *
	 * @return string
	 */
	public function process()
	{
		return $this->buffer;
	}

	/**
	 * called at compile time to define what the block should output in the compiled template code, happens when the block is declared
	 *
	 * basically this will replace the {block arg arg arg} tag in the template
	 *
	 * @param Dwoo_Compiler $compiler the compiler instance that calls this function
	 * @param array $params an array containing original and compiled parameters
	 * @param string $prepend that is just meant to allow a child class to call
	 * 						  parent::postProcessing($compiler, $params, "foo();") to add a command before the
	 * 						  default commands are executed
	 * @param string $append that is just meant to allow a child class to call
	 * 						 parent::postProcessing($compiler, $params, null, "foo();") to add a command after the
	 * 						 default commands are executed
	 * @param string $type the type is the plugin class name used
	 */
	public static function preProcessing(Dwoo_Compiler $compiler, array $params, $prepend, $append, $type)
	{
		return Dwoo_Compiler::PHP_OPEN.$prepend.'$this->addStack("'.$type.'", array('.Dwoo_Compiler::implode_r($compiler->getCompiledParams($params)).'));'.$append.Dwoo_Compiler::PHP_CLOSE;
	}

	/**
	 * called at compile time to define what the block should output in the compiled template code, happens when the block is ended
	 *
	 * basically this will replace the {/block} tag in the template
	 *
	 * @see preProcessing
	 * @param Dwoo_Compiler $compiler the compiler instance that calls this function
	 * @param array $params an array containing original and compiled parameters, see preProcessing() for more details
	 * @param string $prepend that is just meant to allow a child class to call
	 * 						  parent::postProcessing($compiler, $params, "foo();") to add a command before the
	 * 						  default commands are executed
	 * @param string $append that is just meant to allow a child class to call
	 * 						 parent::postProcessing($compiler, $params, null, "foo();") to add a command after the
	 * 						 default commands are executed
	 * @param string $content the entire content of the block being closed
	 */
	public static function postProcessing(Dwoo_Compiler $compiler, array $params, $prepend, $append, $content)
	{
		return $content . Dwoo_Compiler::PHP_OPEN.$prepend.'$this->delStack();'.$append.Dwoo_Compiler::PHP_CLOSE;
	}
}
