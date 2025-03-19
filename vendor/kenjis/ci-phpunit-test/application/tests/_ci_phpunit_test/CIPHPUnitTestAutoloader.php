<?php
/**
 * Part of ci-phpunit-test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

class CIPHPUnitTestAutoloader
{
	private $alias = [
		'MonkeyPatch',
		'ReflectionHelper',
	];

	/**
	 * @var directories to search file
	 */
	private $dirs = [];

	/**
	 * @var CIPHPUnitTestFileCache
	 */
	private $cache;

	/**
	 * @param CIPHPUnitTestFileCache $cache
	 * @param array $dirs directories to search file
	 */
	public function __construct(
		CIPHPUnitTestFileCache $cache = null,
		array $dirs = null
	)
	{
		$this->cache = $cache;
		if ($dirs === null)
		{
			$this->dirs = [
				APPPATH.'models',
				APPPATH.'libraries',
				APPPATH.'controllers',
				APPPATH.'modules',
			];
		}
		else
		{
			$this->dirs = $dirs;
		}
	}

	public function load($class)
	{
		if ($this->cache)
		{
			if ($this->loadFromCache($class))
			{
				return;
			}
		}

		$this->loadCIPHPUnitTestAliasClass($class);
		$this->loadCIPHPUnitTestClass($class);
		$this->loadApplicationClass($class);
	}

	protected function loadCIPHPUnitTestAliasClass($class)
	{
		if (in_array($class, $this->alias))
		{
			$dir = __DIR__ . '/alias';
			$this->loadClassFile($dir, $class);
		}
	}

	protected function loadCIPHPUnitTestClass($class)
	{
		if (substr($class, 0, 13) !== 'CIPHPUnitTest')
		{
			return;
		}

		if (substr($class, -9) !== 'Exception')
		{
			$dir = __DIR__;
			$this->loadClassFile($dir, $class);
		}
		else
		{
			$dir = __DIR__ . '/exceptions';
			$this->loadClassFile($dir, $class);
		}
	}

	protected function loadApplicationClass($class)
	{
		foreach ($this->dirs as $dir)
		{
			if ( ! is_dir($dir))
			{
				continue;
			}

			if ($this->loadClassFile($dir, $class))
			{
				return true;
			}

			$iterator = new \RecursiveIteratorIterator(
				new \RecursiveDirectoryIterator(
					$dir, \RecursiveDirectoryIterator::SKIP_DOTS
				),
				\RecursiveIteratorIterator::SELF_FIRST
			);

			foreach ($iterator as $file)
			{
				if ($file->isDir())
				{
					if ($this->loadClassFile($file, $class))
					{
						return true;
					}
				}
			}
		}
	}

	protected function loadClassFile($dir, $class)
	{
		$class_file = $dir . '/' . $class . '.php';
		if (file_exists($class_file))
		{
			require $class_file;
			if ($this->cache)
			{
				$this->cache[$class] = $class_file;
			}
			return true;
		}
		
		return false;
	}

	protected function loadFromCache($class)
	{
		if ($filename = $this->cache[$class])
		{
			if (is_readable($filename))
			{
				require $filename;
				return true;
			}
			else
			{
				unset($this->cache[$class]);
			}
		}
		
		return false;
	}
}
