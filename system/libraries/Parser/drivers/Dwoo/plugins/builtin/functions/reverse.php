<?php

/**
 * Reverses a string or an array
 * <pre>
 *  * value : the string or array to reverse
 *  * preserve_keys : if value is an array and this is true, then the array keys are left intact
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
function Dwoo_Plugin_reverse(Dwoo $dwoo, $value, $preserve_keys=false)
{
	if (is_array($value)) {
		return array_reverse($value, $preserve_keys);
	} elseif(($charset=$dwoo->getCharset()) === 'iso-8859-1') {
		return strrev((string) $value);
	} else {
	    $strlen = mb_strlen($value);
	    $out = '';
	    while ($strlen--) {
	        $out .= mb_substr($value, $strlen, 1, $charset);
	    }
		return $out;
	}
}
