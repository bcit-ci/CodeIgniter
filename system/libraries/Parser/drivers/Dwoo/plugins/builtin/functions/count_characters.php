<?php

/**
 * Counts the characters in a string
 * <pre>
 *  * value : the string to process
 *  * count_spaces : if true, the white-space characters are counted as well
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
function Dwoo_Plugin_count_characters_compile(Dwoo_Compiler $compiler, $value, $count_spaces=false)
{
	if ($count_spaces==='false') {
		return 'preg_match_all(\'#[^\s\pZ]#u\', '.$value.', $tmp)';
	} else {
		return 'mb_strlen('.$value.', $this->charset)';
	}
}
