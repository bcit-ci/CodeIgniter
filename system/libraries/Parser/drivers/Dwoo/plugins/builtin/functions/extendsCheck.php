<?php

/**
 * Checks whether an extended file has been modified, and if so recompiles the current template. This is for internal use only, do not use.
 *
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
function Dwoo_Plugin_extendsCheck_compile(Dwoo_Compiler $compiler, $file)
{
	preg_match('#^["\']([a-z]{2,}):(.*?)["\']$#i', $file, $m);
	$resource = $m[1];
	$identifier = $m[2];

	$tpl = $compiler->getDwoo()->templateFactory($resource, $identifier);

	if ($tpl === null) {
		throw new Dwoo_Compilation_Exception($compiler, 'Load Templates : Resource "'.$resource.':'.$identifier.'" not found.');
	} elseif ($tpl === false) {
		throw new Dwoo_Compilation_Exception($compiler, 'Load Templates : Resource "'.$resource.'" does not support includes.');
	}


	$out = '\'\';// checking for modification in '.$resource.':'.$identifier."\r\n";
	
	$modCheck = $tpl->getIsModifiedCode();
	
	if ($modCheck) {
		$out .= 'if (!('.$modCheck.')) { ob_end_clean(); return false; }';
	} else {
		$out .= 'try {
	$tpl = $this->templateFactory("'.$resource.'", "'.$identifier.'");
} catch (Dwoo_Exception $e) {
	$this->triggerError(\'Load Templates : Resource <em>'.$resource.'</em> was not added to Dwoo, can not extend <em>'.$identifier.'</em>\', E_USER_WARNING);
}
if ($tpl === null)
	$this->triggerError(\'Load Templates : Resource "'.$resource.':'.$identifier.'" was not found.\', E_USER_WARNING);
elseif ($tpl === false)
	$this->triggerError(\'Load Templates : Resource "'.$resource.'" does not support extends.\', E_USER_WARNING);
if ($tpl->getUid() != "'.$tpl->getUid().'") { ob_end_clean(); return false; }';
	}
	
	return $out;
}
