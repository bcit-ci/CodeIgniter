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

namespace Doctrine\Common\Reflection;

use Doctrine\Common\Annotations\TokenParser;
use ReflectionException;

/**
 * Parses a file for namespaces/use/class declarations.
 *
 * @author Karoly Negyesi <karoly@negyesi.net>
 */
class StaticReflectionParser implements ReflectionProviderInterface
{
    /**
     * The fully qualified class name.
     *
     * @var string
     */
    protected $className;

    /**
     * The short class name.
     *
     * @var string
     */
    protected $shortClassName;

    /**
     * Whether the caller only wants class annotations.
     *
     * @var boolean.
     */
    protected $classAnnotationOptimize;

    /**
     * Whether the parser has run.
     *
     * @var boolean
     */
    protected $parsed = false;

    /**
     * The namespace of the class.
     *
     * @var string
     */
    protected $namespace = '';

    /**
     * The use statements of the class.
     *
     * @var array
     */
    protected $useStatements = [];

    /**
     * The docComment of the class.
     *
     * @var string
     */
    protected $docComment = [
        'class' => '',
        'property' => [],
        'method' => []
    ];

    /**
     * The name of the class this class extends, if any.
     *
     * @var string
     */
    protected $parentClassName = '';

    /**
     * The parent PSR-0 Parser.
     *
     * @var \Doctrine\Common\Reflection\StaticReflectionParser
     */
    protected $parentStaticReflectionParser;

    /**
     * Parses a class residing in a PSR-0 hierarchy.
     *
     * @param string               $className               The full, namespaced class name.
     * @param ClassFinderInterface $finder                  A ClassFinder object which finds the class.
     * @param boolean              $classAnnotationOptimize Only retrieve the class docComment.
     *                                                      Presumes there is only one statement per line.
     */
    public function __construct($className, $finder, $classAnnotationOptimize = false)
    {
        $this->className = ltrim($className, '\\');
        $lastNsPos = strrpos($this->className, '\\');

        if ($lastNsPos !== false) {
            $this->namespace = substr($this->className, 0, $lastNsPos);
            $this->shortClassName = substr($this->className, $lastNsPos + 1);
        } else {
            $this->shortClassName = $this->className;
        }

        $this->finder = $finder;
        $this->classAnnotationOptimize = $classAnnotationOptimize;
    }

    /**
     * @return void
     */
    protected function parse()
    {
        if ($this->parsed || !$fileName = $this->finder->findFile($this->className)) {
            return;
        }
        $this->parsed = true;
        $contents = file_get_contents($fileName);
        if ($this->classAnnotationOptimize) {
            if (preg_match("/\A.*^\s*((abstract|final)\s+)?class\s+{$this->shortClassName}\s+/sm", $contents, $matches)) {
                $contents = $matches[0];
            }
        }
        $tokenParser = new TokenParser($contents);
        $docComment = '';
        while ($token = $tokenParser->next(false)) {
            if (is_array($token)) {
                switch ($token[0]) {
                    case T_USE:
                        $this->useStatements = array_merge($this->useStatements, $tokenParser->parseUseStatement());
                        break;
                    case T_DOC_COMMENT:
                        $docComment = $token[1];
                        break;
                    case T_CLASS:
                        $this->docComment['class'] = $docComment;
                        $docComment = '';
                        break;
                    case T_VAR:
                    case T_PRIVATE:
                    case T_PROTECTED:
                    case T_PUBLIC:
                        $token = $tokenParser->next();
                        if ($token[0] === T_VARIABLE) {
                            $propertyName = substr($token[1], 1);
                            $this->docComment['property'][$propertyName] = $docComment;
                            continue 2;
                        }
                        if ($token[0] !== T_FUNCTION) {
                            // For example, it can be T_FINAL.
                            continue 2;
                        }
                        // No break.
                    case T_FUNCTION:
                        // The next string after function is the name, but
                        // there can be & before the function name so find the
                        // string.
                        while (($token = $tokenParser->next()) && $token[0] !== T_STRING);
                        $methodName = $token[1];
                        $this->docComment['method'][$methodName] = $docComment;
                        $docComment = '';
                        break;
                    case T_EXTENDS:
                        $this->parentClassName = $tokenParser->parseClass();
                        $nsPos = strpos($this->parentClassName, '\\');
                        $fullySpecified = false;
                        if ($nsPos === 0) {
                            $fullySpecified = true;
                        } else {
                            if ($nsPos) {
                                $prefix = strtolower(substr($this->parentClassName, 0, $nsPos));
                                $postfix = substr($this->parentClassName, $nsPos);
                            } else {
                                $prefix = strtolower($this->parentClassName);
                                $postfix = '';
                            }
                            foreach ($this->useStatements as $alias => $use) {
                                if ($alias == $prefix) {
                                    $this->parentClassName = '\\' . $use . $postfix;
                                    $fullySpecified = true;
                              }
                            }
                        }
                        if (!$fullySpecified) {
                            $this->parentClassName = '\\' . $this->namespace . '\\' . $this->parentClassName;
                        }
                        break;
                }
            }
        }
    }

    /**
     * @return StaticReflectionParser
     */
    protected function getParentStaticReflectionParser()
    {
        if (empty($this->parentStaticReflectionParser)) {
            $this->parentStaticReflectionParser = new static($this->parentClassName, $this->finder);
        }

        return $this->parentStaticReflectionParser;
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * @return string
     */
    public function getNamespaceName()
    {
        return $this->namespace;
    }

    /**
     * {@inheritDoc}
     */
    public function getReflectionClass()
    {
        return new StaticReflectionClass($this);
    }

    /**
     * {@inheritDoc}
     */
    public function getReflectionMethod($methodName)
    {
        return new StaticReflectionMethod($this, $methodName);
    }

    /**
     * {@inheritDoc}
     */
    public function getReflectionProperty($propertyName)
    {
        return new StaticReflectionProperty($this, $propertyName);
    }

    /**
     * Gets the use statements from this file.
     *
     * @return array
     */
    public function getUseStatements()
    {
        $this->parse();

        return $this->useStatements;
    }

    /**
     * Gets the doc comment.
     *
     * @param string $type The type: 'class', 'property' or 'method'.
     * @param string $name The name of the property or method, not needed for 'class'.
     *
     * @return string The doc comment, empty string if none.
     */
    public function getDocComment($type = 'class', $name = '')
    {
        $this->parse();

        return $name ? $this->docComment[$type][$name] : $this->docComment[$type];
    }

    /**
     * Gets the PSR-0 parser for the declaring class.
     *
     * @param string $type The type: 'property' or 'method'.
     * @param string $name The name of the property or method.
     *
     * @return StaticReflectionParser A static reflection parser for the declaring class.
     *
     * @throws ReflectionException
     */
    public function getStaticReflectionParserForDeclaringClass($type, $name)
    {
        $this->parse();
        if (isset($this->docComment[$type][$name])) {
            return $this;
        }
        if (!empty($this->parentClassName)) {
            return $this->getParentStaticReflectionParser()->getStaticReflectionParserForDeclaringClass($type, $name);
        }
        throw new ReflectionException('Invalid ' . $type . ' "' . $name . '"');
    }
}
