<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license. For more information, see
 * <http://www.doctrine-project.org>.
 */

namespace Doctrine\Common\Annotations;

/**
 * AnnotationRegistry.
 */
final class AnnotationRegistry
{
    /**
     * A map of namespaces to use for autoloading purposes based on a PSR-0 convention.
     *
     * Contains the namespace as key and an array of directories as value. If the value is NULL
     * the include path is used for checking for the corresponding file.
     *
     * This autoloading mechanism does not utilize the PHP autoloading but implements autoloading on its own.
     *
     * @var array
     */
    static private $autoloadNamespaces = array();

    /**
     * A map of autoloader callables.
     *
     * @var array
     */
    static private $loaders = array();

    /**
     * @return void
     */
    static public function reset()
    {
        self::$autoloadNamespaces = array();
        self::$loaders = array();
    }

    /**
     * Registers file.
     *
     * @param string $file
     *
     * @return void
     */
    static public function registerFile($file)
    {
        require_once $file;
    }

    /**
     * Adds a namespace with one or many directories to look for files or null for the include path.
     *
     * Loading of this namespaces will be done with a PSR-0 namespace loading algorithm.
     *
     * @param string            $namespace
     * @param string|array|null $dirs
     *
     * @return void
     */
    static public function registerAutoloadNamespace($namespace, $dirs = null)
    {
        self::$autoloadNamespaces[$namespace] = $dirs;
    }

    /**
     * Registers multiple namespaces.
     *
     * Loading of this namespaces will be done with a PSR-0 namespace loading algorithm.
     *
     * @param array $namespaces
     *
     * @return void
     */
    static public function registerAutoloadNamespaces(array $namespaces)
    {
        self::$autoloadNamespaces = array_merge(self::$autoloadNamespaces, $namespaces);
    }

    /**
     * Registers an autoloading callable for annotations, much like spl_autoload_register().
     *
     * NOTE: These class loaders HAVE to be silent when a class was not found!
     * IMPORTANT: Loaders have to return true if they loaded a class that could contain the searched annotation class.
     *
     * @param callable $callable
     *
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    static public function registerLoader($callable)
    {
        if (!is_callable($callable)) {
            throw new \InvalidArgumentException("A callable is expected in AnnotationRegistry::registerLoader().");
        }
        self::$loaders[] = $callable;
    }

    /**
     * Autoloads an annotation class silently.
     *
     * @param string $class
     *
     * @return boolean
     */
    static public function loadAnnotationClass($class)
    {
        foreach (self::$autoloadNamespaces AS $namespace => $dirs) {
            if (strpos($class, $namespace) === 0) {
                $file = str_replace("\\", DIRECTORY_SEPARATOR, $class) . ".php";
                if ($dirs === null) {
                    if ($path = stream_resolve_include_path($file)) {
                        require $path;
                        return true;
                    }
                } else {
                    foreach((array)$dirs AS $dir) {
                        if (is_file($dir . DIRECTORY_SEPARATOR . $file)) {
                            require $dir . DIRECTORY_SEPARATOR . $file;
                            return true;
                        }
                    }
                }
            }
        }

        foreach (self::$loaders AS $loader) {
            if (call_user_func($loader, $class) === true) {
                return true;
            }
        }
        return false;
    }
}
