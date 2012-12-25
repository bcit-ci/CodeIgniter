<?php

/**
 * Wraps a text at the given line length
 * <pre>
 *  * value : the text to wrap
 *  * length : maximum line length
 *  * break : the character(s) to use to break the line
 *  * cut : if true, the line is cut at the exact length instead of breaking at the nearest space
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
function Dwoo_Plugin_wordwrap_compile(Dwoo_Compiler $compiler, $value, $length=80, $break="\n", $cut=false)
{
	return 'wordwrap('.$value.','.$length.','.$break.','.$cut.')';
}
