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

namespace Doctrine\Common\Persistence\Mapping\Driver;

use Doctrine\Common\Persistence\Mapping\MappingException;

/**
 * The Symfony File Locator makes a simplifying assumptions compared
 * to the DefaultFileLocator. By assuming paths only contain entities of a certain
 * namespace the mapping files consists of the short classname only.
 *
 * @author  Fabien Potencier <fabien@symfony.com>
 * @author  Benjamin Eberlei <kontakt@beberlei.de>
 * @license MIT
 */
class SymfonyFileLocator implements FileLocator
{
    /**
     * The paths where to look for mapping files.
     *
     * @var array
     */
    protected $paths = [];

    /**
     * A map of mapping directory path to namespace prefix used to expand class shortnames.
     *
     * @var array
     */
    protected $prefixes = [];

    /**
     * File extension that is searched for.
     *
     * @var string|null
     */
    protected $fileExtension;

    /**
     * Represents PHP namespace delimiters when looking for files
     *
     * @var string
     */
    private $nsSeparator;

    /**
     * Constructor.
     *
     * @param array       $prefixes
     * @param string|null $fileExtension
     * @param string      $nsSeparator String which would be used when converting FQCN to filename and vice versa. Should not be empty
     */
    public function __construct(array $prefixes, $fileExtension = null, $nsSeparator = '.')
    {
        $this->addNamespacePrefixes($prefixes);
        $this->fileExtension = $fileExtension;

        if (empty($nsSeparator)) {
            throw new \InvalidArgumentException('Namespace separator should not be empty');
        }

        $this->nsSeparator = (string) $nsSeparator;
    }

    /**
     * Adds Namespace Prefixes.
     *
     * @param array $prefixes
     *
     * @return void
     */
    public function addNamespacePrefixes(array $prefixes)
    {
        $this->prefixes = array_merge($this->prefixes, $prefixes);
        $this->paths = array_merge($this->paths, array_keys($prefixes));
    }

    /**
     * Gets Namespace Prefixes.
     *
     * @return array
     */
    public function getNamespacePrefixes()
    {
        return $this->prefixes;
    }

    /**
     * {@inheritDoc}
     */
    public function getPaths()
    {
        return $this->paths;
    }

    /**
     * {@inheritDoc}
     */
    public function getFileExtension()
    {
        return $this->fileExtension;
    }

    /**
     * Sets the file extension used to look for mapping files under.
     *
     * @param string $fileExtension The file extension to set.
     *
     * @return void
     */
    public function setFileExtension($fileExtension)
    {
        $this->fileExtension = $fileExtension;
    }

    /**
     * {@inheritDoc}
     */
    public function fileExists($className)
    {
        $defaultFileName = str_replace('\\', $this->nsSeparator, $className).$this->fileExtension;
        foreach ($this->paths as $path) {
            if (!isset($this->prefixes[$path])) {
                // global namespace class
                if (is_file($path.DIRECTORY_SEPARATOR.$defaultFileName)) {
                    return true;
                }

                continue;
            }

            $prefix = $this->prefixes[$path];

            if (0 !== strpos($className, $prefix.'\\')) {
                continue;
            }

            $filename = $path.'/'.strtr(substr($className, strlen($prefix)+1), '\\', $this->nsSeparator).$this->fileExtension;
            if (is_file($filename)) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function getAllClassNames($globalBasename = null)
    {
        $classes = [];

        if ($this->paths) {
            foreach ((array) $this->paths as $path) {
                if (!is_dir($path)) {
                    throw MappingException::fileMappingDriversRequireConfiguredDirectoryPath($path);
                }

                $iterator = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($path),
                    \RecursiveIteratorIterator::LEAVES_ONLY
                );

                foreach ($iterator as $file) {
                    $fileName = $file->getBasename($this->fileExtension);

                    if ($fileName == $file->getBasename() || $fileName == $globalBasename) {
                        continue;
                    }

                    // NOTE: All files found here means classes are not transient!
                    if (isset($this->prefixes[$path])) {

                        // Calculate namespace suffix for given prefix as a relative path from basepath to file path
                        $nsSuffix = strtr(
                            substr(realpath($file->getPath()), strlen(realpath($path))),
                            $this->nsSeparator,
                            '\\'
                        );

                        $classes[] = $this->prefixes[$path] . str_replace(DIRECTORY_SEPARATOR, '\\', $nsSuffix) . '\\' .str_replace($this->nsSeparator, '\\', $fileName);
                    } else {
                        $classes[] = str_replace($this->nsSeparator, '\\', $fileName);
                    }
                }
            }
        }

        return $classes;
    }

    /**
     * {@inheritDoc}
     */
    public function findMappingFile($className)
    {
        $defaultFileName = str_replace('\\', $this->nsSeparator, $className).$this->fileExtension;
        foreach ($this->paths as $path) {
            if (!isset($this->prefixes[$path])) {
                if (is_file($path.DIRECTORY_SEPARATOR.$defaultFileName)) {
                    return $path.DIRECTORY_SEPARATOR.$defaultFileName;
                }

                continue;
            }

            $prefix = $this->prefixes[$path];

            if (0 !== strpos($className, $prefix.'\\')) {
                continue;
            }

            $filename = $path.'/'.strtr(substr($className, strlen($prefix)+1), '\\', $this->nsSeparator ).$this->fileExtension;
            if (is_file($filename)) {
                return $filename;
            }
        }

        throw MappingException::mappingFileNotFound($className, substr($className, strrpos($className, '\\') + 1).$this->fileExtension);
    }
}
