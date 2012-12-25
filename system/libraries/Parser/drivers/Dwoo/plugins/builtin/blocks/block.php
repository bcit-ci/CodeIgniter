<?php

/**
 * This is used only when rendering a template that has blocks but is not extending anything, 
 * it doesn't do anything by itself and should not be used outside of template inheritance context,
 * see {@link http://wiki.dwoo.org/index.php/TemplateInheritance} to read more about it.
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
class Dwoo_Plugin_block extends Dwoo_Block_Plugin implements Dwoo_ICompilable_Block
{
	public function init($name='')
	{
	}

	public static function preProcessing(Dwoo_Compiler $compiler, array $params, $prepend, $append, $type)
	{
		return '';
	}

	public static function postProcessing(Dwoo_Compiler $compiler, array $params, $prepend, $append, $content)
	{
		return $content;
	}
}
