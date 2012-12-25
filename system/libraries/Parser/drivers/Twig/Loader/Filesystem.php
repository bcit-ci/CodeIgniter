<?php

/*
 * This file is part of Twig.
 *
 * (c) 2009 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Loads template from the filesystem.
 *
 * @package    twig
 * @author     Fabien Potencier <fabien@symfony.com>
 */
class Twig_Loader_Filesystem implements Twig_LoaderInterface, Twig_ExistsLoaderInterface
{
    protected $paths;
    protected $cache;

    /**
     * Constructor.
     *
     * @param string|array $paths A path or an array of paths where to look for templates
     */
    public function __construct($paths)
    {
        $this->setPaths($paths);
    }

    /**
     * Returns the paths to the templates.
     *
     * @param string $namespace A path namespace
     *
     * @return array The array of paths where to look for templates
     */
    public function getPaths($namespace = '__main__')
    {
        return isset($this->paths[$namespace]) ? $this->paths[$namespace] : array();
    }

    /**
     * Returns the path namespaces.
     *
     * The "__main__" namespace is always defined.
     *
     * @return array The array of defined namespaces
     */
    public function getNamespaces()
    {
        return array_keys($this->paths);
    }

    /**
     * Sets the paths where templates are stored.
     *
     * @param string|array $paths     A path or an array of paths where to look for templates
     * @param string       $namespace A path namespace
     */
    public function setPaths($paths, $namespace = '__main__')
    {
        if (!is_array($paths)) {
            $paths = array($paths);
        }

        $this->paths[$namespace] = array();
        foreach ($paths as $path) {
            $this->addPath($path, $namespace);
        }
    }

    /**
     * Adds a path where templates are stored.
     *
     * @param string $path      A path where to look for templates
     * @param string $namespace A path name
     *
     * @throws Twig_Error_Loader
     */
    public function addPath($path, $namespace = '__main__')
    {
        // invalidate the cache
        $this->cache = array();

        if (!is_dir($path)) {
            throw new Twig_Error_Loader(sprintf('The "%s" directory does not exist.', $path));
        }

        $this->paths[$namespace][] = rtrim($path, '/\\');
    }

    /**
     * Prepends a path where templates are stored.
     *
     * @param string $path      A path where to look for templates
     * @param string $namespace A path name
     *
     * @throws Twig_Error_Loader
     */
    public function prependPath($path, $namespace = '__main__')
    {
        // invalidate the cache
        $this->cache = array();

        if (!is_dir($path)) {
            throw new Twig_Error_Loader(sprintf('The "%s" directory does not exist.', $path));
        }

        $path = rtrim($path, '/\\');

        if (!isset($this->paths[$namespace])) {
            $this->paths[$namespace][] = $path;
        } else {
            array_unshift($this->paths[$namespace], $path);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getSource($name)
    {
        return file_get_contents($this->findTemplate($name));
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheKey($name)
    {
        return $this->findTemplate($name);
    }

    /**
     * {@inheritdoc}
     */
    public function exists($name)
    {
        $name = (string) $name;
        if (isset($this->cache[$name])) {
            return true;
        }

        try {
            $this->findTemplate($name);

            return true;
        } catch (Twig_Error_Loader $exception) {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isFresh($name, $time)
    {
        return filemtime($this->findTemplate($name)) <= $time;
    }

    protected function findTemplate($name)
    {
        $name = (string) $name;

        // normalize name
        $name = preg_replace('#/{2,}#', '/', strtr($name, '\\', '/'));

        if (isset($this->cache[$name])) {
            return $this->cache[$name];
        }

        $this->validateName($name);

        $namespace = '__main__';
        if (isset($name[0]) && '@' == $name[0]) {
            if (false === $pos = strpos($name, '/')) {
                throw new Twig_Error_Loader(sprintf('Malformed namespaced template name "%s" (expecting "@namespace/template_name").', $name));
            }

            $namespace = substr($name, 1, $pos - 1);

            $name = substr($name, $pos + 1);
        }

        if (!isset($this->paths[$namespace])) {
            throw new Twig_Error_Loader(sprintf('There are no registered paths for namespace "%s".', $namespace));
        }

        foreach ($this->paths[$namespace] as $path) {
            if (is_file($path.'/'.$name)) {
                return $this->cache[$name] = $path.'/'.$name;
            }
        }

        throw new Twig_Error_Loader(sprintf('Unable to find template "%s" (looked into: %s).', $name, implode(', ', $this->paths[$namespace])));
    }

    protected function validateName($name)
    {
        if (false !== strpos($name, "\0")) {
            throw new Twig_Error_Loader('A template name cannot contain NUL bytes.');
        }

        $parts = explode('/', $name);
        $level = 0;
        foreach ($parts as $part) {
            if ('..' === $part) {
                --$level;
            } elseif ('.' !== $part) {
                ++$level;
            }

            if ($level < 0) {
                throw new Twig_Error_Loader(sprintf('Looks like you try to load a template outside configured directories (%s).', $name));
            }
        }
    }
}
