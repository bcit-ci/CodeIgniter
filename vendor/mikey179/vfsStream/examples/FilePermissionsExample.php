<?php
/**
 * This file is part of vfsStream.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  org\bovigo\vfs
 */
namespace org\bovigo\vfs\example;
/**
 * Example showing correct file permission support introduced with 0.7.0.
 */
class FilePermissionsExample
{
    /**
     * reads configuration from given config file
     *
     * @param  mixed   $config
     * @param  string  $configFile
     */
    public function writeConfig($config, $configFile)
    {
        @file_put_contents($configFile, serialize($config));
    }

    // more methods here
}
?>