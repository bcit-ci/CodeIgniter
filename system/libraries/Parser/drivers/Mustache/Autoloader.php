<?php

/*
 * This file is part of Mustache.php.
 *
 * (c) 2012 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Mustache class autoloader.
 */
class Mustache_Autoloader
{

    private $baseDir;

    /**
     * Autoloader constructor.
     *
     * @param string $baseDir Mustache library base directory (default: dirname(__FILE__).'/..')
     */
    public function __construct($baseDir = null)
    {
        if ($baseDir === null) {
            $this->baseDir = dirname(__FILE__).'/..';
        } else {
            $this->baseDir = rtrim($baseDir, '/');
        }
    }

    /**
     * Register a new instance as an SPL autoloader.
     *
     * @param string $baseDir Mustache library base directory (default: dirname(__FILE__).'/..')
     *
     * @return Mustache_Autoloader Registered Autoloader instance
     */
    public static function register($baseDir = null)
    {
        $loader = new self($baseDir);
        spl_autoload_register(array($loader, 'autoload'));

        return $loader;
    }

    /**
     * Autoload Mustache classes.
     *
     * @param string $class
     */
    public function autoload($class)
    {
        if ($class[0] === '\\') {
            $class = substr($class, 1);
        }

        if (strpos($class, 'Mustache') !== 0) {
            return;
        }

        $file = sprintf('%s/%s.php', $this->baseDir, str_replace('_', '/', $class));
        if (is_file($file)) {
            require $file;
        }
    }
}
