<?php
/**
 * Smarty Internal Plugin Compile ForeachSection
 * Shared methods for {foreach} {section} tags
 *
 * @package    Smarty
 * @subpackage Compiler
 * @author     Uwe Tews
 */

/**
 * Smarty Internal Plugin Compile ForeachSection Class
 *
 * @package    Smarty
 * @subpackage Compiler
 */
class Smarty_Internal_Compile_Private_ForeachSection extends Smarty_Internal_CompileBase
{

    /**
     * Preg search pattern
     *
     * @var string
     */
    private $propertyPreg = '';

    /**
     * Offsets in preg match result
     *
     * @var array
     */
    private $resultOffsets = array();

    /**
     * Start offset
     *
     * @var int
     */
    private $startOffset = 0;

    /**
     * Name of this tag
     *
     * @var string
     */
    public $tagName = '';

    /**
     * Valid properties of $smarty.xxx variable
     *
     * @var array
     */
    public $nameProperties = array();

    /**
     * {section} tag has no item properties
     *
     * @var array
     */
    public $itemProperties = null;

    /**
     * {section} tag has always name attribute
     *
     * @var bool
     */
    public $isNamed = true;

    /**
     * @var array
     */
    public $matchResults = array();

    /**
     * Scan sources for used tag attributes
     *
     * @param  array                                $attributes
     * @param \Smarty_Internal_TemplateCompilerBase $compiler
     */
    public function scanForProperties($attributes, Smarty_Internal_TemplateCompilerBase $compiler)
    {
        $this->propertyPreg = '~(';
        $this->startOffset = 0;
        $this->resultOffsets = array();
        $this->matchResults = array('named' => array(), 'item' => array());
        if ($this->isNamed) {
            $this->buildPropertyPreg(true, $attributes);
        }
        if (isset($this->itemProperties)) {
            if ($this->isNamed) {
                $this->propertyPreg .= '|';
            }
            $this->buildPropertyPreg(false, $attributes);
        }
        $this->propertyPreg .= ')\W~i';
        // Template source
        $this->matchTemplateSource($compiler);
        // Parent template source
        $this->matchParentTemplateSource($compiler);
        // {block} source
        $this->matchBlockSource($compiler);
    }

    /**
     * Build property preg string
     *
     * @param bool  $named
     * @param array $attributes
     */
    public function buildPropertyPreg($named, $attributes)
    {
        if ($named) {
            $this->resultOffsets['named'] = $this->startOffset + 3;
            $this->propertyPreg .= "([\$]smarty[.]{$this->tagName}[.]{$attributes['name']}[.](";
            $properties = $this->nameProperties;
        } else {
            $this->resultOffsets['item'] = $this->startOffset + 3;
            $this->propertyPreg .= "([\$]{$attributes['item']}[@](";
            $properties = $this->itemProperties;
        }
        $this->startOffset += count($properties) + 2;
        $propName = reset($properties);
        while ($propName) {
            $this->propertyPreg .= "({$propName})";
            $propName = next($properties);
            if ($propName) {
                $this->propertyPreg .= '|';
            }
        }
        $this->propertyPreg .= '))';
    }

    /**
     * Find matches in source string
     *
     * @param string $source
     */
    public function matchProperty($source)
    {
        preg_match_all($this->propertyPreg, $source, $match, PREG_SET_ORDER);
        foreach ($this->resultOffsets as $key => $offset) {
            foreach ($match as $m) {
                if (isset($m[$offset]) && !empty($m[$offset])) {
                    $this->matchResults[$key][strtolower($m[$offset])] = true;
                }
            }
        }
    }

    /**
     * Find matches in template source
     *
     * @param \Smarty_Internal_TemplateCompilerBase $compiler
     */
    public function matchTemplateSource(Smarty_Internal_TemplateCompilerBase $compiler)
    {
        $this->matchProperty($compiler->parser->lex->data);
    }

    /**
     * Find matches in all parent template source
     *
     * @param \Smarty_Internal_TemplateCompilerBase $compiler
     */
    public function matchParentTemplateSource(Smarty_Internal_TemplateCompilerBase $compiler)
    {
        // search parent compiler template source
        $nextCompiler = $compiler;
        while ($nextCompiler !== $nextCompiler->parent_compiler) {
            $nextCompiler = $nextCompiler->parent_compiler;
            if ($compiler !== $nextCompiler) {
                // get template source
                $_content = $nextCompiler->template->source->getContent();
                if ($_content != '') {
                    // run pre filter if required
                    if ((isset($nextCompiler->smarty->autoload_filters['pre']) ||
                            isset($nextCompiler->smarty->registered_filters['pre']))) {
                        $_content = $nextCompiler->smarty->ext->_filter_Handler->runFilter('pre', $_content, $nextCompiler->template);
                    }
                    $this->matchProperty($_content);
                }
            }
        }
    }

    /**
     * Find matches in {block} tag source
     *
     * @param \Smarty_Internal_TemplateCompilerBase $compiler
     */
    public function matchBlockSource(Smarty_Internal_TemplateCompilerBase $compiler)
    {

    }

    /**
     * Compiles code for the {$smarty.foreach.xxx} or {$smarty.section.xxx}tag
     *
     * @param  array                                $args      array with attributes from parser
     * @param \Smarty_Internal_TemplateCompilerBase $compiler  compiler object
     * @param  array                                $parameter array with compilation parameter
     *
     * @return string compiled code
     * @throws \SmartyCompilerException
     */
    public function compileSpecialVariable($args, Smarty_Internal_TemplateCompilerBase $compiler, $parameter)
    {
        $tag = strtolower(trim($parameter[ 0 ], '"\''));
        $name = isset($parameter[ 1 ]) ? $compiler->getId($parameter[ 1 ]) : false;
        if (!$name) {
            $compiler->trigger_template_error("missing or illegal \$smarty.{$tag} name attribute", null, true);
        }
        $property = isset($parameter[ 2 ]) ? strtolower($compiler->getId($parameter[ 2 ])) : false;
        if (!$property || !in_array($property, $this->nameProperties)) {
            $compiler->trigger_template_error("missing or illegal \$smarty.{$tag} property attribute", null, true);
        }
        $tagVar = "'__smarty_{$tag}_{$name}'";
        return "(isset(\$_smarty_tpl->tpl_vars[{$tagVar}]->value['{$property}']) ? \$_smarty_tpl->tpl_vars[{$tagVar}]->value['{$property}'] : null)";
    }
}