<?php

/**
 * interface for dwoo plugin loaders
 *
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
interface Dwoo_ILoader
{
	/**
	 * loads a plugin file
	 *
	 * the second parameter is used to avoid permanent rehashing when using php functions,
	 * however this means that if you have add a plugin that overrides a php function you have
	 * to delete the classpath.cache file(s) by hand to force a rehash of the plugins
	 *
	 * @param string $class the plugin name, without the Dwoo_Plugin_ prefix
	 * @param bool $forceRehash if true, the class path caches will be rebuilt if the plugin is not found, in case it has just been added, defaults to true
	 */
	public function loadPlugin($class, $forceRehash = true);
}
