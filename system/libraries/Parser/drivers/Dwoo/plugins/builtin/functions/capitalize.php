<?php

/**
 * Capitalizes the first letter of each word
 * <pre>
 *  * value : the string to capitalize
 *  * numwords : if true, the words containing numbers are capitalized as well
 * </pre>
 * This software is provided 'as-is', without any express or implied warranty.
 * In no event will the authors be held liable for any damages arising from the use of this software.
 *
 * @author     Jordi Boggiano <j.boggiano@seld.be>
 * @copyright  Copyright (c) 2008, Jordi Boggiano
 * @license    http://dwoo.org/LICENSE   Modified BSD License
 * @link       http://dwoo.org/
 * @version    1.1.0
 * @date       2009-07-18
 * @package    Dwoo
 */
function Dwoo_Plugin_capitalize(Dwoo $dwoo, $value, $numwords=false)
{
	if ($numwords || preg_match('#^[^0-9]+$#',$value))
	{
		return mb_convert_case((string) $value,MB_CASE_TITLE, $dwoo->getCharset());
	} else {
		$bits = explode(' ', (string) $value);
		$out = '';
		while (list(,$v) = each($bits)) {
			if (preg_match('#^[^0-9]+$#', $v)) {
				$out .=	' '.mb_convert_case($v, MB_CASE_TITLE, $dwoo->getCharset());
			} else {
				$out .=	' '.$v;
			}
		}

		return substr($out, 1);
	}
}
