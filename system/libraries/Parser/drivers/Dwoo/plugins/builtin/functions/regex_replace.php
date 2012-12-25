<?php

/**
 * Replaces the search string by the replace string using regular expressions
 * <pre>
 *  * value : the string to search into
 *  * search : the string to search for, must be a complete regular expression including delimiters
 *  * replace : the string to use as a replacement, must be a complete regular expression including delimiters
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
function Dwoo_Plugin_regex_replace(Dwoo $dwoo, $value, $search, $replace)
{
	$search = (array) $search;
	$cnt = count($search);

	for ($i = 0; $i < $cnt; $i++) {
		// Credits for this to Monte Ohrt who made smarty's regex_replace modifier
		if (($pos = strpos($search[$i], "\0")) !== false) {
			$search[$i] = substr($search[$i], 0, $pos);
		}

		if (preg_match('#[a-z\s]+$#is', $search[$i], $m) && (strpos($m[0], 'e') !== false)) {
			$search[$i] = substr($search[$i], 0, -strlen($m[0])) . str_replace(array('e', ' '), '', $m[0]);
		}
	}

	return preg_replace($search, $replace, $value);
}