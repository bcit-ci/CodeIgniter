<?php

/**
 * Concatenates any number of variables or strings fed into it
 * <pre>
 *  * rest : two or more strings that will be merged into one
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
function Dwoo_Plugin_cat_compile(Dwoo_Compiler $compiler, $value, array $rest)
{
	return '('.$value.').('.implode(').(', $rest).')';
}
