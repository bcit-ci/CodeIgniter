<?php

/**
 * Loads sub-templates contained in an external file
 * <pre>
 *  * file : the resource name of the file to load
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
function Dwoo_Plugin_load_templates_compile(Dwoo_Compiler $compiler, $file)
{
	$file = substr($file, 1, -1);

	if ($file === '') {
		return;
	}

	if (preg_match('#^([a-z]{2,}):(.*)$#i', $file, $m)) {
		// resource:identifier given, extract them
		$resource = $m[1];
		$identifier = $m[2];
	} else {
		// get the current template's resource
		$resource = $compiler->getDwoo()->getTemplate()->getResourceName();
		$identifier = $file;
	}

	$tpl = $compiler->getDwoo()->templateFactory($resource, $identifier);

	if ($tpl === null) {
		throw new Dwoo_Compilation_Exception($compiler, 'Load Templates : Resource "'.$resource.':'.$identifier.'" not found.');
	} elseif ($tpl === false) {
		throw new Dwoo_Compilation_Exception($compiler, 'Load Templates : Resource "'.$resource.'" does not support includes.');
	}

	$cmp = clone $compiler;
	$cmp->compile($compiler->getDwoo(), $tpl);
	foreach ($cmp->getTemplatePlugins() as $template=>$args) {
		$compiler->addTemplatePlugin($template, $args['params'], $args['uuid'], $args['body']);
	}
	foreach ($cmp->getUsedPlugins() as $plugin=>$type) {
		$compiler->addUsedPlugin($plugin, $type);
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