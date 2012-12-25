<?php

/**
 * Indents every line of a text by the given amount of characters
 * <pre>
 *  * value : the string to indent
 *  * by : how many characters should be inserted before each line
 *  * char : the character(s) to insert
 * </pre>
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
function Dwoo_Plugin_indent_compile(Dwoo_Compiler $compiler, $value, $by=4, $char=' ')
{
	return "preg_replace('#^#m', '".str_repeat(substr($char, 1, -1), trim($by, '"\''))."', $value)";
}
