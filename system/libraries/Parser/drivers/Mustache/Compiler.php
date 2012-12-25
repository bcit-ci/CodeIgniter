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
 * Mustache Compiler class.
 *
 * This class is responsible for turning a Mustache token parse tree into normal PHP source code.
 */
class Mustache_Compiler
{

    private $sections;
    private $source;
    private $indentNextLine;
    private $customEscape;
    private $charset;

    /**
     * Compile a Mustache token parse tree into PHP source code.
     *
     * @param string $source       Mustache Template source code
     * @param string $tree         Parse tree of Mustache tokens
     * @param string $name         Mustache Template class name
     * @param bool   $customEscape (default: false)
     * @param string $charset      (default: 'UTF-8')
     *
     * @return string Generated PHP source code
     */
    public function compile($source, array $tree, $name, $customEscape = false, $charset = 'UTF-8')
    {
        $this->sections       = array();
        $this->source         = $source;
        $this->indentNextLine = true;
        $this->customEscape   = $customEscape;
        $this->charset        = $charset;

        return $this->writeCode($tree, $name);
    }

    /**
     * Helper function for walking the Mustache token parse tree.
     *
     * @throws InvalidArgumentException upon encountering unknown token types.
     *
     * @param array $tree  Parse tree of Mustache tokens
     * @param int   $level (default: 0)
     *
     * @return string Generated PHP source code
     */
    private function walk(array $tree, $level = 0)
    {
        $code = '';
        $level++;
        foreach ($tree as $node) {
            switch ($node[Mustache_Tokenizer::TYPE]) {
                case Mustache_Tokenizer::T_SECTION:
                    $code .= $this->section(
                        $node[Mustache_Tokenizer::NODES],
                        $node[Mustache_Tokenizer::NAME],
                        $node[Mustache_Tokenizer::INDEX],
                        $node[Mustache_Tokenizer::END],
                        $node[Mustache_Tokenizer::OTAG],
                        $node[Mustache_Tokenizer::CTAG],
                        $level
                    );
                    break;

                case Mustache_Tokenizer::T_INVERTED:
                    $code .= $this->invertedSection(
                        $node[Mustache_Tokenizer::NODES],
                        $node[Mustache_Tokenizer::NAME],
                        $level
                    );
                    break;

                case Mustache_Tokenizer::T_PARTIAL:
                case Mustache_Tokenizer::T_PARTIAL_2:
                    $code .= $this->partial(
                        $node[Mustache_Tokenizer::NAME],
                        isset($node[Mustache_Tokenizer::INDENT]) ? $node[Mustache_Tokenizer::INDENT] : '',
                        $level
                    );
                    break;

                case Mustache_Tokenizer::T_UNESCAPED:
                case Mustache_Tokenizer::T_UNESCAPED_2:
                    $code .= $this->variable($node[Mustache_Tokenizer::NAME], false, $level);
                    break;

                case Mustache_Tokenizer::T_COMMENT:
                    break;

                case Mustache_Tokenizer::T_ESCAPED:
                    $code .= $this->variable($node[Mustache_Tokenizer::NAME], true, $level);
                    break;

                case Mustache_Tokenizer::T_TEXT:
                    $code .= $this->text($node[Mustache_Tokenizer::VALUE], $level);
                    break;

                default:
                    throw new InvalidArgumentException('Unknown node type: '.json_encode($node));
            }
        }

        return $code;
    }

    const KLASS = '<?php

        class %s extends Mustache_Template
        {
            public function renderInternal(Mustache_Context $context, $indent = \'\', $escape = false)
            {
                $buffer = \'\';
        %s

                if ($escape) {
                    return %s;
                } else {
                    return $buffer;
                }
            }
        %s
        }';

    /**
     * Generate Mustache Template class PHP source.
     *
     * @param array  $tree Parse tree of Mustache tokens
     * @param string $name Mustache Template class name
     *
     * @return string Generated PHP source code
     */
    private function writeCode($tree, $name)
    {
        $code     = $this->walk($tree);
        $sections = implode("\n", $this->sections);

        return sprintf($this->prepare(self::KLASS, 0, false), $name, $code, $this->getEscape('$buffer'), $sections);
    }

    const SECTION_CALL = '
        // %s section
        $buffer .= $this->section%s($context, $indent, $context->%s(%s));
    ';

    const SECTION = '
        private function section%s(Mustache_Context $context, $indent, $value) {
            $buffer = \'\';
            if (!is_string($value) && is_callable($value)) {
                $source = %s;
                $buffer .= $this->mustache
                    ->loadLambda((string) call_user_func($value, $source)%s)
                    ->renderInternal($context, $indent);
            } elseif (!empty($value)) {
                $values = $this->isIterable($value) ? $value : array($value);
                foreach ($values as $value) {
                    $context->push($value);%s
                    $context->pop();
                }
            }

            return $buffer;
        }';

    /**
     * Generate Mustache Template section PHP source.
     *
     * @param array  $nodes Array of child tokens
     * @param string $id    Section name
     * @param int    $start Section start offset
     * @param int    $end   Section end offset
     * @param string $otag  Current Mustache opening tag
     * @param string $ctag  Current Mustache closing tag
     * @param int    $level
     *
     * @return string Generated section PHP source code
     */
    private function section($nodes, $id, $start, $end, $otag, $ctag, $level)
    {
        $method = $this->getFindMethod($id);
        $id     = var_export($id, true);
        $source = var_export(substr($this->source, $start, $end - $start), true);

        if ($otag !== '{{' || $ctag !== '}}') {
            $delims = ', '.var_export(sprintf('{{= %s %s =}}', $otag, $ctag), true);
        } else {
            $delims = '';
        }

        $key    = ucfirst(md5($delims."\n".$source));

        if (!isset($this->sections[$key])) {
            $this->sections[$key] = sprintf($this->prepare(self::SECTION), $key, $source, $delims, $this->walk($nodes, 2));
        }

        return sprintf($this->prepare(self::SECTION_CALL, $level), $id, $key, $method, $id);
    }

    const INVERTED_SECTION = '
        // %s inverted section
        $value = $context->%s(%s);
        if (empty($value)) {
            %s
        }';

    /**
     * Generate Mustache Template inverted section PHP source.
     *
     * @param array  $nodes Array of child tokens
     * @param string $id    Section name
     * @param int    $level
     *
     * @return string Generated inverted section PHP source code
     */
    private function invertedSection($nodes, $id, $level)
    {
        $method = $this->getFindMethod($id);
        $id     = var_export($id, true);

        return sprintf($this->prepare(self::INVERTED_SECTION, $level), $id, $method, $id, $this->walk($nodes, $level));
    }

    const PARTIAL = '
        if ($partial = $this->mustache->loadPartial(%s)) {
            $buffer .= $partial->renderInternal($context, %s);
        }
    ';

    /**
     * Generate Mustache Template partial call PHP source.
     *
     * @param string $id     Partial name
     * @param string $indent Whitespace indent to apply to partial
     * @param int    $level
     *
     * @return string Generated partial call PHP source code
     */
    private function partial($id, $indent, $level)
    {
        return sprintf(
            $this->prepare(self::PARTIAL, $level),
            var_export($id, true),
            var_export($indent, true)
        );
    }

    const VARIABLE = '
        $value = $context->%s(%s);
        if (!is_string($value) && is_callable($value)) {
            $value = $this->mustache
                ->loadLambda((string) call_user_func($value))
                ->renderInternal($context, $indent);
        }
        $buffer .= %s%s;
    ';

    /**
     * Generate Mustache Template variable interpolation PHP source.
     *
     * @param string  $id     Variable name
     * @param boolean $escape Escape the variable value for output?
     * @param int     $level
     *
     * @return string Generated variable interpolation PHP source
     */
    private function variable($id, $escape, $level)
    {
        $method = $this->getFindMethod($id);
        $id     = ($method !== 'last') ? var_export($id, true) : '';
        $value  = $escape ? $this->getEscape() : '$value';

        return sprintf($this->prepare(self::VARIABLE, $level), $method, $id, $this->flushIndent(), $value);
    }

    const LINE = '$buffer .= "\n";';
    const TEXT = '$buffer .= %s%s;';

    /**
     * Generate Mustache Template output Buffer call PHP source.
     *
     * @param string $text
     * @param int    $level
     *
     * @return string Generated output Buffer call PHP source
     */
    private function text($text, $level)
    {
        if ($text === "\n") {
            $this->indentNextLine = true;

            return $this->prepare(self::LINE, $level);
        } else {
            return sprintf($this->prepare(self::TEXT, $level), $this->flushIndent(), var_export($text, true));
        }
    }

    /**
     * Prepare PHP source code snippet for output.
     *
     * @param string  $text
     * @param int     $bonus          Additional indent level (default: 0)
     * @param boolean $prependNewline Prepend a newline to the snippet? (default: true)
     *
     * @return string PHP source code snippet
     */
    private function prepare($text, $bonus = 0, $prependNewline = true)
    {
        $text = ($prependNewline ? "\n" : '').trim($text);
        if ($prependNewline) {
            $bonus++;
        }

        return preg_replace("/\n( {8})?/", "\n".str_repeat(" ", $bonus * 4), $text);
    }

    const DEFAULT_ESCAPE = 'htmlspecialchars(%s, ENT_COMPAT, %s)';
    const CUSTOM_ESCAPE  = 'call_user_func($this->mustache->getEscape(), %s)';

    /**
     * Get the current escaper.
     *
     * @param string $value (default: '$value')
     *
     * @return string Either a custom callback, or an inline call to `htmlspecialchars`
     */
    private function getEscape($value = '$value')
    {
        if ($this->customEscape) {
            return sprintf(self::CUSTOM_ESCAPE, $value);
        } else {
            return sprintf(self::DEFAULT_ESCAPE, $value, var_export($this->charset, true));
        }
    }

    /**
     * Select the appropriate Context `find` method for a given $id.
     *
     * The return value will be one of `find`, `findDot` or `last`.
     *
     * @see Mustache_Context::find
     * @see Mustache_Context::findDot
     * @see Mustache_Context::last
     *
     * @param string $id Variable name
     *
     * @return string `find` method name
     */
    private function getFindMethod($id)
    {
        if ($id === '.') {
            return 'last';
        } elseif (strpos($id, '.') === false) {
            return 'find';
        } else {
            return 'findDot';
        }
    }

    const LINE_INDENT = '$indent . ';

    /**
     * Get the current $indent prefix to write to the buffer.
     *
     * @return string "$indent . " or ""
     */
    private function flushIndent()
    {
        if ($this->indentNextLine) {
            $this->indentNextLine = false;

            return self::LINE_INDENT;
        } else {
            return '';
        }
    }
}
