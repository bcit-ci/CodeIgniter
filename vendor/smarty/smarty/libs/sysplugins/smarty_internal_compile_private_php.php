<?php
/**
 * Smarty Internal Plugin Compile PHP Expression
 * Compiles any tag which will output an expression or variable
 *
 * @package    Smarty
 * @subpackage Compiler
 * @author     Uwe Tews
 */

/**
 * Smarty Internal Plugin Compile PHP Expression Class
 *
 * @package    Smarty
 * @subpackage Compiler
 */
class Smarty_Internal_Compile_Private_Php extends Smarty_Internal_CompileBase
{

    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see Smarty_Internal_CompileBase
     */
    public $required_attributes = array('code', 'type');

    /**
     * Compiles code for generating output from any expression
     *
     * @param array                                 $args      array with attributes from parser
     * @param \Smarty_Internal_TemplateCompilerBase $compiler  compiler object
     * @param array                                 $parameter array with compilation parameter
     *
     * @return string
     * @throws \SmartyException
     */
    public function compile($args, Smarty_Internal_TemplateCompilerBase $compiler, $parameter)
    {
        // check and get attributes
        $_attr = $this->getAttributes($compiler, $args);
        $compiler->has_code = false;
        if ($_attr['type'] == 'xml') {
            $compiler->tag_nocache = true;
            $save = $compiler->template->compiled->has_nocache_code;
            $output = addcslashes($_attr['code'], "'\\");
            $compiler->parser->current_buffer->append_subtree($compiler->parser, new Smarty_Internal_ParseTree_Tag($compiler->parser, $compiler->processNocacheCode("<?php echo '" .
                                                                                                                                                                    $output .
                                                                                                                                                                    "';?>", $compiler, true)));
            $compiler->template->compiled->has_nocache_code = $save;
            return '';
        }
        if ($_attr['type'] != 'tag') {
            if ($compiler->php_handling == Smarty::PHP_REMOVE) {
                return '';
            } elseif ($compiler->php_handling == Smarty::PHP_QUOTE) {
                $output = preg_replace_callback('#(<\?(?:php|=)?)|(<%)|(<script\s+language\s*=\s*["\']?\s*php\s*["\']?\s*>)|(\?>)|(%>)|(<\/script>)#i', array($this,
                                                                                                                                                              'quote'), $_attr['code']);
                $compiler->parser->current_buffer->append_subtree($compiler->parser, new Smarty_Internal_ParseTree_Text($output));
                return '';
            } elseif ($compiler->php_handling == Smarty::PHP_PASSTHRU || $_attr['type'] == 'unmatched') {
                $compiler->tag_nocache = true;
                $save = $compiler->template->compiled->has_nocache_code;
                $output = addcslashes($_attr['code'], "'\\");
                $compiler->parser->current_buffer->append_subtree($compiler->parser, new Smarty_Internal_ParseTree_Tag($compiler->parser, $compiler->processNocacheCode("<?php echo '" .
                                                                                                                                                                        $output .
                                                                                                                                                                        "';?>", $compiler, true)));
                $compiler->template->compiled->has_nocache_code = $save;
                return '';
            } elseif ($compiler->php_handling == Smarty::PHP_ALLOW) {
                if (!($compiler->smarty instanceof SmartyBC)) {
                    $compiler->trigger_template_error('$smarty->php_handling PHP_ALLOW not allowed. Use SmartyBC to enable it', null, true);
                }
                $compiler->has_code = true;
                return $_attr['code'];
            } else {
                $compiler->trigger_template_error('Illegal $smarty->php_handling value', null, true);
            }
        } else {
            $compiler->has_code = true;
            if (!($compiler->smarty instanceof SmartyBC)) {
                $compiler->trigger_template_error('{php}{/php} tags not allowed. Use SmartyBC to enable them', null, true);
            }
            $ldel = preg_quote($compiler->smarty->left_delimiter, '#');
            $rdel = preg_quote($compiler->smarty->right_delimiter, '#');
            preg_match("#^({$ldel}php\\s*)((.)*?)({$rdel})#", $_attr['code'], $match);
            if (!empty($match[2])) {
                if ('nocache' == trim($match[2])) {
                    $compiler->tag_nocache = true;
                } else {
                    $compiler->trigger_template_error("illegal value of option flag \"{$match[2]}\"", null, true);
                }
            }
            return preg_replace(array("#^{$ldel}\\s*php\\s*(.)*?{$rdel}#",
                                      "#{$ldel}\\s*/\\s*php\\s*{$rdel}$#"), array('<?php ', '?>'), $_attr['code']);
        }
    }

    /**
     * Lexer code for PHP tags
     *
     * This code has been moved from lexer here fo easier debugging and maintenance
     *
     * @param $lex
     */
    public function parsePhp($lex)
    {
        $lex->token = Smarty_Internal_Templateparser::TP_PHP;
        $close = 0;
        $lex->taglineno = $lex->line;
        $closeTag = '?>';
        if (strpos($lex->value, '<?xml') === 0) {
            $lex->is_xml = true;
            $lex->token = Smarty_Internal_Templateparser::TP_NOCACHE;
            return;
        } elseif (strpos($lex->value, '<?') === 0) {
            $lex->phpType = 'php';
        } elseif (strpos($lex->value, '<%') === 0) {
            $lex->phpType = 'asp';
            $closeTag = '%>';
        } elseif (strpos($lex->value, '%>') === 0) {
            $lex->phpType = 'unmatched';
        } elseif (strpos($lex->value, '?>') === 0) {
            if ($lex->is_xml) {
                $lex->is_xml = false;
                $lex->token = Smarty_Internal_Templateparser::TP_NOCACHE;
                return;
            }
            $lex->phpType = 'unmatched';
        } elseif (strpos($lex->value, '<s') === 0) {
            $lex->phpType = 'script';
            $closeTag = '</script>';
        } elseif (strpos($lex->value, $lex->smarty->left_delimiter) === 0) {
            if ($lex->isAutoLiteral()) {
                $lex->token = Smarty_Internal_Templateparser::TP_TEXT;
                return;
            }
            $closeTag = "{$lex->smarty->left_delimiter}/php{$lex->smarty->right_delimiter}";
            if ($lex->value == $closeTag) {
                $lex->compiler->trigger_template_error("unexpected closing tag '{$closeTag}'");
            }
            $lex->phpType = 'tag';
        }
        if ($lex->phpType == 'unmatched') {
            return;
        }
        if (($lex->phpType == 'php' || $lex->phpType == 'asp') &&
            ($lex->compiler->php_handling == Smarty::PHP_PASSTHRU || $lex->compiler->php_handling == Smarty::PHP_QUOTE)
        ) {
            return;
        }
        $start = $lex->counter + strlen($lex->value);
        $body = true;
        if (preg_match('~' . preg_quote($closeTag, '~') . '~i', $lex->data, $match, PREG_OFFSET_CAPTURE, $start)) {
            $close = $match[0][1];
        } else {
            $lex->compiler->trigger_template_error("missing closing tag '{$closeTag}'");
        }
        while ($body) {
            if (preg_match('~([/][*])|([/][/][^\n]*)|(\'[^\'\\\\]*(?:\\.[^\'\\\\]*)*\')|("[^"\\\\]*(?:\\.[^"\\\\]*)*")~', $lex->data, $match, PREG_OFFSET_CAPTURE, $start)) {
                $value = $match[0][0];
                $from = $pos = $match[0][1];
                if ($pos > $close) {
                    $body = false;
                } else {
                    $start = $pos + strlen($value);
                    $phpCommentStart = $value == '/*';
                    if ($phpCommentStart) {
                        $phpCommentEnd = preg_match('~([*][/])~', $lex->data, $match, PREG_OFFSET_CAPTURE, $start);
                        if ($phpCommentEnd) {
                            $pos2 = $match[0][1];
                            $start = $pos2 + strlen($match[0][0]);
                        }
                    }
                    while ($close > $pos && $close < $start) {
                        if (preg_match('~' . preg_quote($closeTag, '~') .
                                       '~i', $lex->data, $match, PREG_OFFSET_CAPTURE, $from)) {
                            $close = $match[0][1];
                            $from = $close + strlen($match[0][0]);
                        } else {
                            $lex->compiler->trigger_template_error("missing closing tag '{$closeTag}'");
                        }
                    }
                    if ($phpCommentStart && (!$phpCommentEnd || $pos2 > $close)) {
                        $lex->taglineno = $lex->line + substr_count(substr($lex->data, $lex->counter, $start), "\n");
                        $lex->compiler->trigger_template_error("missing PHP comment closing tag '*/'");
                    }
                }
            } else {
                $body = false;
            }
        }
        $lex->value = substr($lex->data, $lex->counter, $close + strlen($closeTag) - $lex->counter);
    }

    /*
     * Call back function for $php_handling = PHP_QUOTE
     *
     */
    /**
     * @param $match
     *
     * @return string
     */
    private function quote($match)
    {
        return htmlspecialchars($match[0], ENT_QUOTES);
    }
}
