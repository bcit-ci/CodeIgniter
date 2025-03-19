<?php
/**
 * Part of ci-phpunit-test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

class Installer
{
    const TEST_FOLDER = 'tests';

    public static function install($app = 'application')
    {
        self::recursiveCopy(
            dirname(__FILE__) . '/application/tests',
            $app . '/' . static::TEST_FOLDER
        );
        self::fixPath($app);
    }

    /**
     * Fix paths in Bootstrap.php
     */
    private static function fixPath($app = 'application')
    {
        $file = $app . '/' . static::TEST_FOLDER . '/Bootstrap.php';
        $contents = file_get_contents($file);
        
        if (! file_exists('system')) {
            if (file_exists('vendor/codeigniter/framework/system')) {
                $contents = str_replace(
                    '$system_path = \'../../system\';',
                    '$system_path = \'../../vendor/codeigniter/framework/system\';',
                    $contents
                );
            } else {
                throw new Exception('Can\'t find "system" folder.');
            }
        }
        
        if (! file_exists('index.php')) {
            if (file_exists('public/index.php')) {
                // CodeIgniter 3.0.6 and after
                $contents = str_replace(
                    "define('FCPATH', realpath(dirname(__FILE__).'/../..').DIRECTORY_SEPARATOR);",
                    "define('FCPATH', realpath(dirname(__FILE__).'/../../public').DIRECTORY_SEPARATOR);",
                    $contents
                );
                // CodeIgniter 3.0.5 and before
                $contents = str_replace(
                    "define('FCPATH', realpath(dirname(__FILE__).'/../..').'/');",
                    "define('FCPATH', realpath(dirname(__FILE__).'/../../public').'/');",
                    $contents
                );
            } elseif (file_exists($app . '/public/index.php')) {
                // CodeIgniter 3.0.6 and after
                $contents = str_replace(
                    "define('FCPATH', realpath(dirname(__FILE__).'/../..').DIRECTORY_SEPARATOR);",
                    "define('FCPATH', realpath(dirname(__FILE__).'/../public').DIRECTORY_SEPARATOR);",
                    $contents
                );
                // CodeIgniter 3.0.5 and before
                $contents = str_replace(
                    "define('FCPATH', realpath(dirname(__FILE__).'/../..').'/');",
                    "define('FCPATH', realpath(dirname(__FILE__).'/../public').'/');",
                    $contents
                );
                if ($app != 'application') {
                    $contents = str_replace(
                        "\$application_folder = '../../application';",
                        "\$application_folder = '../../{$app}';",
                        $contents
                    );
                }
            } else {
                throw new Exception('Can\'t find "index.php".');
            }
        }
        
        file_put_contents($file, $contents);
    }

    public static function update($app = 'application')
    {
        $target_dir = $app . '/' . static::TEST_FOLDER . '/_ci_phpunit_test';
        self::recursiveUnlink($target_dir);
        self::recursiveCopy(
            dirname(__FILE__) . '/application/tests/_ci_phpunit_test',
            $target_dir
        );
    }

    /**
     * Recursive Copy
     *
     * @param string $src
     * @param string $dst
     */
    private static function recursiveCopy($src, $dst)
    {
        @mkdir($dst, 0755);
        
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($src, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );
        
        foreach ($iterator as $file) {
            if ($file->isDir()) {
                @mkdir($dst . '/' . $iterator->getSubPathName());
            } else {
                $success = copy($file, $dst . '/' . $iterator->getSubPathName());
                if ($success) {
                    echo 'copied: ' . $dst . '/' . $iterator->getSubPathName() . PHP_EOL;
                }
            }
        }
    }

    /**
     * Recursive Unlink
     *
     * @param string $dir
     */
    private static function recursiveUnlink($dir)
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        
        foreach ($iterator as $file) {
            if ($file->isDir()) {
                rmdir($file);
            } else {
                unlink($file);
            }
        }
        
        rmdir($dir);
    }
}
