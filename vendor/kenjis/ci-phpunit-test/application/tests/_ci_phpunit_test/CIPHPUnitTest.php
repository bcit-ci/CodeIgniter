<?php
/**
 * Part of ci-phpunit-test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

class CIPHPUnitTest
{
	private static $loader_class = 'CI_Loader';
	private static $autoload_dirs;

	/**
	 * Initialize CIPHPUnitTest
	 * 
	 * @param array $autoload_dirs directories to search class file for autoloader
	 */
	public static function init(array $autoload_dirs = null)
	{
		// Fix CLI args
		$_server_backup = $_SERVER;
		$_SERVER['argv'] = [
			'index.php',
			'welcome'	// Dummy
		];
		$_SERVER['argc'] = 2;

		self::$autoload_dirs = $autoload_dirs;
		
		$cwd_backup = getcwd();

		// Load autoloader for ci-phpunit-test
		require __DIR__ . '/autoloader.php';

		// Autoloader for PHP-Parser
		// Don't use `require`, because we may have required already
		// in `patcher/bootstrap.php`
		require_once __DIR__ . '/patcher/third_party/PHP-Parser/lib/bootstrap.php';

		require APPPATH . '/tests/TestCase.php';

		// Replace a few Common functions
		require __DIR__ . '/replacing/core/Common.php';
		require BASEPATH . 'core/Common.php';

		// Workaround for missing CodeIgniter's error handler
		// See https://github.com/kenjis/ci-phpunit-test/issues/37
		set_error_handler('_error_handler');

		// Load new functions of CIPHPUnitTest
		require __DIR__ . '/functions.php';
		// Load ci-phpunit-test CI_Loader
		require __DIR__ . '/replacing/core/Loader.php';
		// Load ci-phpunit-test CI_Input
		require __DIR__ . '/replacing/core/Input.php';

		// Change current directroy
		chdir(FCPATH);

		/*
		 * --------------------------------------------------------------------
		 * LOAD THE BOOTSTRAP FILE
		 * --------------------------------------------------------------------
		 *
		 * And away we go...
		 */
		require __DIR__ . '/replacing/core/CodeIgniter.php';

		self::replaceHelpers();

		// Create CodeIgniter instance
		if (! self::wiredesignzHmvcInstalled())
		{
			new CI_Controller();
		}
		else
		{
			new MX_Controller();
		}

		// This code is here, not to cause errors with HMVC
		self::replaceLoader();

		// Restore $_SERVER. We need this for NetBeans
		$_SERVER = $_server_backup;
		
		// Restore cwd to use `Usage: phpunit [options] <directory>`
		chdir($cwd_backup);
	}

	public static function createCodeIgniterInstance()
	{
		if (! self::wiredesignzHmvcInstalled())
		{
			new CI_Controller();
		}
		else
		{
			new CI();
			new MX_Controller();
		}
	}

	public static function wiredesignzHmvcInstalled()
	{
		if (file_exists(APPPATH.'third_party/MX'))
		{
			return true;
		}
		
		return false;
	}

	public static function getAutoloadDirs()
	{
		return self::$autoload_dirs;
	}

	protected static function replaceLoader()
	{
		$my_loader_file = 
			APPPATH . 'core/' . config_item('subclass_prefix') . 'Loader.php';

		if (file_exists($my_loader_file))
		{
			self::$loader_class = config_item('subclass_prefix') . 'Loader';
			if ( ! class_exists(self::$loader_class))
			{
				require $my_loader_file;
			}
		}
		self::loadLoader();
	}

	protected static function replaceHelpers()
	{
		$my_helper_file = APPPATH . 'helpers/' . config_item('subclass_prefix') . 'url_helper.php';
		if (file_exists($my_helper_file))
		{
			require $my_helper_file;
		}
		require __DIR__ . '/replacing/helpers/url_helper.php';
	}

	public static function setPatcherCacheDir($dir = null)
	{
		if ($dir === null)
		{
			$dir = APPPATH . 'tests/_ci_phpunit_test/tmp/cache';
		}

		MonkeyPatchManager::setCacheDir(
			$dir
		);
	}

	public static function loadLoader()
	{
		$loader = new self::$loader_class;
		load_class_instance('Loader', $loader);
	}
}
