<?php
namespace Rain\Tpl;

/**
 * Interface for a plugin classes for rain template engine.
 * Plugins should at first declare implemented hooks during registration.
 *
 * Example implementation:
 * <code>
 * public function declare_hooks() {
 *   return array('before_parse', 'after_parse' => 'custom_method');
 * }
 * </code>
 *
 * Template will then call the registered method with a context object as parameter.
 * Context object implements \ArrayAccess.
 * It's properties depends on hook api.
 *
 * Method can modify some properties. No return value is expected.
 */
interface IPlugin
{
	/**
	 * Returns a list of hooks that are implemented by the plugin.
	 * This should be an array containing:
	 * - a key/value pair where key is hook name and value is implementing method,
	 * - a value only when hook has same name as method.
	 */
	public function declareHooks();

	/**
	 * Sets plugin options.
	 *
	 * @var array
	 */
	public function setOptions($options);
}
