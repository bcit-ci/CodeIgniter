<?php
/**
 * Smarty Internal Plugin Smarty Template Compiler Base
 * This file contains the basic classes and methods for compiling Smarty templates with lexer/parser
 *
 * @package    Smarty
 * @subpackage Compiler
 * @author     Uwe Tews
 */

/**
 * Class SmartyTemplateCompiler
 *
 * @package    Smarty
 * @subpackage Compiler
 */
class Smarty_Internal_SmartyTemplateCompiler extends Smarty_Internal_TemplateCompilerBase
{
    /**
     * Lexer class name
     *
     * @var string
     */
    public $lexer_class;

    /**
     * Parser class name
     *
     * @var string
     */
    public $parser_class;

    /**
     * array of vars which can be compiled in local scope
     *
     * @var array
     */
    public $local_var = array();

    /**
     * array of callbacks called when the normal compile process of template is finished
     *
     * @var array
     */
    public $postCompileCallbacks = array();

    /**
     * prefix code
     *
     * @var string
     */
    public $prefixCompiledCode = '';

    /**
     * postfix code
     *
     * @var string
     */
    public $postfixCompiledCode = '';

    /**
     * Initialize compiler
     *
     * @param string $lexer_class  class name
     * @param string $parser_class class name
     * @param Smarty $smarty       global instance
     */
    public function __construct($lexer_class, $parser_class, Smarty $smarty)
    {
        parent::__construct($smarty);
        // get required plugins
        $this->lexer_class = $lexer_class;
        $this->parser_class = $parser_class;
    }

    /**
     * method to compile a Smarty template
     *
     * @param  mixed $_content template source
     * @param bool   $isTemplateSource
     *
     * @return bool true if compiling succeeded, false if it failed
     * @throws \SmartyCompilerException
     */
    protected function doCompile($_content, $isTemplateSource = false)
    {
        /* here is where the compiling takes place. Smarty
          tags in the templates are replaces with PHP code,
          then written to compiled files. */
        // init the lexer/parser to compile the template
        $this->parser =
            new $this->parser_class(new $this->lexer_class(str_replace(array("\r\n", "\r"), "\n", $_content), $this),
                                    $this);
        if ($isTemplateSource && $this->template->caching) {
            $this->parser->insertPhpCode("<?php\n\$_smarty_tpl->compiled->nocache_hash = '{$this->nocache_hash}';\n?>\n");
        }
        if (function_exists('mb_internal_encoding') && ((int) ini_get('mbstring.func_overload')) & 2) {
            $mbEncoding = mb_internal_encoding();
            mb_internal_encoding('ASCII');
        } else {
            $mbEncoding = null;
        }

        if ($this->smarty->_parserdebug) {
            $this->parser->PrintTrace();
            $this->parser->lex->PrintTrace();
        }
        // get tokens from lexer and parse them
        while ($this->parser->lex->yylex()) {
            if ($this->smarty->_parserdebug) {
                echo "<pre>Line {$this->parser->lex->line} Parsing  {$this->parser->yyTokenName[$this->parser->lex->token]} Token " .
                    htmlentities($this->parser->lex->value) . "</pre>";
            }
            $this->parser->doParse($this->parser->lex->token, $this->parser->lex->value);
        }

        // finish parsing process
        $this->parser->doParse(0, 0);
        if ($mbEncoding) {
            mb_internal_encoding($mbEncoding);
        }
        // check for unclosed tags
        if (count($this->_tag_stack) > 0) {
            // get stacked info
            list($openTag, $_data) = array_pop($this->_tag_stack);
            $this->trigger_template_error("unclosed {$this->smarty->left_delimiter}" . $openTag .
                                          "{$this->smarty->right_delimiter} tag");
        }
        // call post compile callbacks
        foreach ($this->postCompileCallbacks as $cb) {
            $parameter = $cb;
            $parameter[0] = $this;
            call_user_func_array($cb[0], $parameter);
        }
        // return compiled code
        return $this->prefixCompiledCode . $this->parser->retvalue . $this->postfixCompiledCode;
    }

    /**
     * Register a post compile callback
     * - when the callback is called after template compiling the compiler object will be inserted as first parameter
     *
     * @param callback $callback
     * @param array    $parameter optional parameter array
     * @param string   $key       optional key for callback
     * @param bool     $replace   if true replace existing keyed callback
     */
    public function registerPostCompileCallback($callback, $parameter = array(), $key = null, $replace = false)
    {
        array_unshift($parameter, $callback);
        if (isset($key)) {
            if ($replace || !isset($this->postCompileCallbacks[$key])) {
                $this->postCompileCallbacks[$key] = $parameter;
            }
        } else {
            $this->postCompileCallbacks[] = $parameter;
        }
    }

    /**
     * Remove a post compile callback
     *
     * @param string $key callback key
     */
    public function unregisterPostCompileCallback($key)
    {
        unset($this->postCompileCallbacks[$key]);
    }
}
