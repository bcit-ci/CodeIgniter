<?php
namespace Rain\Tpl;

require_once __DIR__ . '/IPlugin.php';

/**
 * Maintains template plugins and call hook methods.
 */
class PluginContainer
{
	/**
	 * Hook callables sorted by hook name.
	 *
	 * @var array
	 */
	private $hooks = array();

	/**
	 * Registered plugin instances sorted by name.
	 *
	 * @var array
	 */
	private $plugins = array();

	/**
	 * Safe method that will not override plugin of same name.
	 * Instead an exception is thrown.
	 *
	 * @param string $name
	 * @param IPlugin $plugin
	 * @throws \InvalidArgumentException Plugin of same name already exists in container.
	 * @return PluginContainer
	 */
	public function addPlugin($name, IPlugin $plugin) {
		if (isset($this->plugins[(string) $name])) {
			throw new \InvalidArgumentException('Plugin named "' . $name . '" already exists in container');
		}
		return $this->setPlugin($name, $plugin);
	}

	/**
	 * Sets plugin by name. Plugin of same name is replaced when exists.
	 *
	 * @param string $name
	 * @param IPlugin $plugin
	 * @return PluginContainer
	 */
	public function setPlugin($name, IPlugin $plugin) {
		$this->removePlugin($name);
		$this->plugins[(string) $name] = $plugin;

		foreach ((array) $plugin->declareHooks() as $hook => $method) {
			if (is_int($hook)) {
				// numerical key, method has same name as hook
				$hook = $method;
			}
			$callable = array($plugin, $method);
			if (!is_callable($callable)) {
				throw new \InvalidArgumentException(sprintf(
					'Wrong callcable suplied by %s for "%s" hook ',
					get_class($plugin), $hook
				));
			}
			$this->hooks[$hook][] = $callable;
		}
		return $this;
	}

	public function removePlugin($name) {
		$name = (string) $name;
		if (!isset($this->plugins[$name])) {
			return;
		}
		$plugin = $this->plugins[$name];
		unset($this->plugins[$name]);
		// remove all registered callables
		foreach ($this->hooks as $hook => &$callables) {
			foreach ($callables as $i => $callable) {
				if ($callable[0] === $plugin) {
					unset($callables[$i]);
				}
			}
		}
		return $this;
	}

	/**
	 * Passes the context object to registered plugins.
	 *
	 * @param string $hook_name
	 * @param \ArrayAccess $context
	 * @return PluginContainer
	 */
	public function run($hook_name, \ArrayAccess $context ){
		if (!isset($this->hooks[$hook_name])) {
			return $this;
		}
		$context['_hook_name'] = $hook_name;
		foreach( $this->hooks[$hook_name] as $callable ){
			call_user_func($callable, $context);
		}
		return $this;
	}

	/**
	 * Retuns context object that will be passed to plugins.
	 *
	 * @param array $params
	 * @return \ArrayObject
	 */
	public function createContext($params = array())
	{
		return new \ArrayObject((array) $params, \ArrayObject::ARRAY_AS_PROPS);
	}
}
