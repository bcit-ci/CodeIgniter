<?php

/**
 * Returns a variable or a default value if it's empty
 * <pre>
 *  * value : the variable to check
 *  * default : fallback value if the first one is empty
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
function Dwoo_Plugin_default_compile(Dwoo_Compiler $compiler, $value, $default='')
{
	return '(($tmp = '.$value.')===null||$tmp===\'\' ? '.$default.' : $tmp)';
}
