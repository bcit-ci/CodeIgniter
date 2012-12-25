<?php

/**
 * Evaluates the given string as if it was a template
 *
 * Although this plugin is kind of optimized and will
 * not recompile your string each time, it is still not
 * a good practice to use it. If you want to have templates
 * stored in a database or something you should probably use
 * the Dwoo_Template_String class or make another class that
 * extends it
 * <pre>
 *  * var : the string to use as a template
 *  * assign : if set, the output of the template will be saved in this variable instead of being output
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
function Dwoo_Plugin_eval(Dwoo $dwoo, $var, $assign = null)
{
	if ($var == '') {
		return;
	}

	$tpl = new Dwoo_Template_String($var);
	$out = $dwoo->get($tpl, $dwoo->readVar('_parent'));

	if ($assign !== null) {
		$dwoo->assignInScope($out, $assign);
	} else {
		return $out;
	}
}
