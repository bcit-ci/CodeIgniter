<?php

/**
 * Replaces all white-space characters with the given string
 * <pre>
 *  * value : the text to process
 *  * with : the replacement string, note that any number of consecutive white-space characters will be replaced by a single replacement string
 * </pre>
 * Example :
 *
 * <code>
 * {"a    b  c		d
 *
 * e"|whitespace}
 *
 * results in : a b c d e
 * </code>
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
function Dwoo_Plugin_whitespace_compile(Dwoo_Compiler $compiler, $value, $with=' ')
{
	return "preg_replace('#\s+#'.(strcasecmp(\$this->charset, 'utf-8')===0?'u':''), $with, $value)";
}
