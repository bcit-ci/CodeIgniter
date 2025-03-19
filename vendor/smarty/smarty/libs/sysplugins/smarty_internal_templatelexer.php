<?php
/*
 * This file is part of Smarty.
 *
 * (c) 2015 Uwe Tews
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Smarty_Internal_Templatelexer
 * This is the template file lexer.
 * It is generated from the smarty_internal_templatelexer.plex file
 *
 *
 * @author Uwe Tews <uwe.tews@googlemail.com>
 */
class Smarty_Internal_Templatelexer
{
    /**
     * Source
     *
     * @var string
     */
    public $data;

    /**
     * byte counter
     *
     * @var int
     */
    public $counter;

    /**
     * token number
     *
     * @var int
     */
    public $token;

    /**
     * token value
     *
     * @var string
     */
    public $value;

    /**
     * current line
     *
     * @var int
     */
    public $line;

    /**
     * tag start line
     *
     * @var
     */
    public $taglineno;

    /**
     * php code type
     *
     * @var string
     */
    public $phpType = '';

    /**
     * escaped left delimiter
     *
     * @var string
     */
    public $ldel = '';

    /**
     * escaped left delimiter length
     *
     * @var int
     */
    public $ldel_length = 0;

    /**
     * escaped right delimiter
     *
     * @var string
     */
    public $rdel = '';

    /**
     * escaped right delimiter length
     *
     * @var int
     */
    public $rdel_length = 0;

    /**
     * state number
     *
     * @var int
     */
    public $state = 1;

    /**
     * Smarty object
     *
     * @var Smarty
     */
    public $smarty = null;

    /**
     * compiler object
     *
     * @var Smarty_Internal_TemplateCompilerBase
     */
    public $compiler = null;

    /**
     * literal tag nesting level
     *
     * @var int
     */
    private $literal_cnt = 0;

    /**
     * PHP start tag string
     *
     * @var string
     */

    /**
     * trace file
     *
     * @var resource
     */
    public $yyTraceFILE;

    /**
     * trace prompt
     *
     * @var string
     */
    public $yyTracePrompt;

    /**
     * XML flag true while processing xml
     *
     * @var bool
     */
    public $is_xml = false;

    /**
     * state names
     *
     * @var array
     */
    public $state_name = array(1 => 'TEXT', 2 => 'TAG', 3 => 'TAGBODY', 4 => 'LITERAL', 5 => 'DOUBLEQUOTEDSTRING',);

    /**
     * storage for assembled token patterns
     *
     * @var string
     */
    private $yy_global_pattern1 = null;

    private $yy_global_pattern2 = null;

    private $yy_global_pattern3 = null;

    private $yy_global_pattern4 = null;

    private $yy_global_pattern5 = null;

    /**
     * token names
     *
     * @var array
     */
    public $smarty_token_names = array(        // Text for parser error messages
                                               'NOT'         => '(!,not)', 'OPENP' => '(', 'CLOSEP' => ')',
                                               'OPENB'       => '[', 'CLOSEB' => ']', 'PTR' => '->', 'APTR' => '=>',
                                               'EQUAL'       => '=', 'NUMBER' => 'number', 'UNIMATH' => '+" , "-',
                                               'MATH'        => '*" , "/" , "%', 'INCDEC' => '++" , "--',
                                               'SPACE'       => ' ', 'DOLLAR' => '$', 'SEMICOLON' => ';',
                                               'COLON'       => ':', 'DOUBLECOLON' => '::', 'AT' => '@', 'HATCH' => '#',
                                               'QUOTE'       => '"', 'BACKTICK' => '`', 'VERT' => '"|" modifier',
                                               'DOT'         => '.', 'COMMA' => '","', 'QMARK' => '"?"',
                                               'ID'          => 'id, name', 'TEXT' => 'text',
                                               'LDELSLASH'   => '{/..} closing tag', 'LDEL' => '{...} Smarty tag',
                                               'COMMENT'     => 'comment', 'AS' => 'as', 'TO' => 'to',
                                               'PHP'         => '"<?php", "<%", "{php}" tag',
                                               'LOGOP'       => '"<", "==" ... logical operator',
                                               'TLOGOP'      => '"lt", "eq" ... logical operator; "is div by" ... if condition',
                                               'SCOND'       => '"is even" ... if condition',);

    /**
     * constructor
     *
     * @param   string                             $data template source
     * @param Smarty_Internal_TemplateCompilerBase $compiler
     */
    function __construct($data, Smarty_Internal_TemplateCompilerBase $compiler)
    {
        $this->data = $data;
        $this->counter = 0;
        if (preg_match('~^\xEF\xBB\xBF~i', $this->data, $match)) {
            $this->counter += strlen($match[ 0 ]);
        }
        $this->line = 1;
        $this->smarty = $compiler->smarty;
        $this->compiler = $compiler;
        $this->ldel = preg_quote($this->smarty->left_delimiter, '~');
        $this->ldel_length = strlen($this->smarty->left_delimiter);
        $this->rdel = preg_quote($this->smarty->right_delimiter, '~');
        $this->rdel_length = strlen($this->smarty->right_delimiter);
        $this->smarty_token_names[ 'LDEL' ] = $this->smarty->left_delimiter;
        $this->smarty_token_names[ 'RDEL' ] = $this->smarty->right_delimiter;
    }

    public function PrintTrace()
    {
        $this->yyTraceFILE = fopen('php://output', 'w');
        $this->yyTracePrompt = '<br>';
    }

    /*
     * Check if this tag is autoliteral
     */
    public function isAutoLiteral()
    {
        return $this->smarty->auto_literal && isset($this->value[ $this->ldel_length ]) ?
            strpos(" \n\t\r", $this->value[ $this->ldel_length ]) !== false : false;
    }

    private $_yy_state = 1;

    private $_yy_stack = array();

    public function yylex()
    {
        return $this->{'yylex' . $this->_yy_state}();
    }

    public function yypushstate($state)
    {
        if ($this->yyTraceFILE) {
            fprintf($this->yyTraceFILE, "%sState push %s\n", $this->yyTracePrompt,
                    isset($this->state_name[ $this->_yy_state ]) ? $this->state_name[ $this->_yy_state ] :
                        $this->_yy_state);
        }
        array_push($this->_yy_stack, $this->_yy_state);
        $this->_yy_state = $state;
        if ($this->yyTraceFILE) {
            fprintf($this->yyTraceFILE, "%snew State %s\n", $this->yyTracePrompt,
                    isset($this->state_name[ $this->_yy_state ]) ? $this->state_name[ $this->_yy_state ] :
                        $this->_yy_state);
        }
    }

    public function yypopstate()
    {
        if ($this->yyTraceFILE) {
            fprintf($this->yyTraceFILE, "%sState pop %s\n", $this->yyTracePrompt,
                    isset($this->state_name[ $this->_yy_state ]) ? $this->state_name[ $this->_yy_state ] :
                        $this->_yy_state);
        }
        $this->_yy_state = array_pop($this->_yy_stack);
        if ($this->yyTraceFILE) {
            fprintf($this->yyTraceFILE, "%snew State %s\n", $this->yyTracePrompt,
                    isset($this->state_name[ $this->_yy_state ]) ? $this->state_name[ $this->_yy_state ] :
                        $this->_yy_state);
        }
    }

    public function yybegin($state)
    {
        $this->_yy_state = $state;
        if ($this->yyTraceFILE) {
            fprintf($this->yyTraceFILE, "%sState set %s\n", $this->yyTracePrompt,
                    isset($this->state_name[ $this->_yy_state ]) ? $this->state_name[ $this->_yy_state ] :
                        $this->_yy_state);
        }
    }

    public function yylex1()
    {
        if (!isset($this->yy_global_pattern1)) {
            $this->yy_global_pattern1 =
                "/\G([{][}])|\G(" . $this->ldel . "[*])|\G((" . $this->ldel . "\\s*php(.*?)" . $this->rdel . ")|(" .
                $this->ldel . "\\s*[\/]php" . $this->rdel . "))|\G(" . $this->ldel . "\\s*literal\\s*" . $this->rdel .
                ")|\G(" . $this->ldel . "\\s*)|\G(\\s*" . $this->rdel .
                ")|\G((<[?]((php\\s+|=)|\\s+))|(<[%])|(<[?]xml\\s+)|(<script\\s+language\\s*=\\s*[\"']?\\s*php\\s*[\"']?\\s*>)|([?][>])|([%][>]))|\G([\S\s])/isS";
        }
        if ($this->counter >= strlen($this->data)) {
            return false; // end of input
        }

        do {
            if (preg_match($this->yy_global_pattern1, $this->data, $yymatches, null, $this->counter)) {
                $yysubmatches = $yymatches;
                if (strlen($yysubmatches[ 0 ]) < 200) {
                    $yymatches = preg_grep("/(.|\s)+/", $yysubmatches);
                } else {
                    $yymatches = array_filter($yymatches, 'strlen');
                }
                if (empty($yymatches)) {
                    throw new Exception('Error: lexing failed because a rule matched' . ' an empty string.  Input "' .
                                        substr($this->data, $this->counter, 5) . '... state TEXT');
                }
                next($yymatches); // skip global match
                $this->token = key($yymatches); // token number
                $this->value = current($yymatches); // token value
                $r = $this->{'yy_r1_' . $this->token}();
                if ($r === null) {
                    $this->counter += strlen($this->value);
                    $this->line += substr_count($this->value, "\n");
                    // accept this token
                    return true;
                } elseif ($r === true) {
                    // we have changed state
                    // process this token in the new state
                    return $this->yylex();
                } elseif ($r === false) {
                    $this->counter += strlen($this->value);
                    $this->line += substr_count($this->value, "\n");
                    if ($this->counter >= strlen($this->data)) {
                        return false; // end of input
                    }
                    // skip this token
                    continue;
                }
            } else {
                throw new Exception('Unexpected input at line' . $this->line . ': ' . $this->data[ $this->counter ]);
            }
            break;
        } while (true);
    } // end function

    const TEXT = 1;

    function yy_r1_1()
    {

        $this->token = Smarty_Internal_Templateparser::TP_TEXT;
    }

    function yy_r1_2()
    {

        preg_match("~[*]{$this->rdel}~", $this->data, $match, PREG_OFFSET_CAPTURE, $this->counter);
        if (isset($match[ 0 ][ 1 ])) {
            $to = $match[ 0 ][ 1 ] + strlen($match[ 0 ][ 0 ]);
        } else {
            $this->compiler->trigger_template_error("missing or misspelled comment closing tag '*{$this->smarty->right_delimiter}'");
        }
        $this->value = substr($this->data, $this->counter, $to - $this->counter);
        return false;
    }

    function yy_r1_3()
    {

        $obj = new Smarty_Internal_Compile_Private_Php();
        $obj->parsePhp($this);
    }

    function yy_r1_7()
    {

        if ($this->smarty->auto_literal && isset($this->value[ $this->ldel_length ]) ?
            strpos(" \n\t\r", $this->value[ $this->ldel_length ]) !== false : false
        ) {
            $this->token = Smarty_Internal_Templateparser::TP_TEXT;
        } else {
            $this->token = Smarty_Internal_Templateparser::TP_LITERALSTART;
            $this->yypushstate(self::LITERAL);
        }
    }

    function yy_r1_8()
    {

        if ($this->smarty->auto_literal && isset($this->value[ $this->ldel_length ]) ?
            strpos(" \n\t\r", $this->value[ $this->ldel_length ]) !== false : false
        ) {
            $this->token = Smarty_Internal_Templateparser::TP_TEXT;
        } else {
            $this->yypushstate(self::TAG);
            return true;
        }
    }

    function yy_r1_9()
    {

        $this->token = Smarty_Internal_Templateparser::TP_TEXT;
    }

    function yy_r1_10()
    {

        $obj = new Smarty_Internal_Compile_Private_Php();
        $obj->parsePhp($this);
    }

    function yy_r1_19()
    {

        $to = strlen($this->data);
        preg_match("~($this->ldel)|(<[?]((php\s+|=)|\s+))|(<[%])|(<[?]xml\s+)|(<script\s+language\s*=\s*[\"']?\s*php\s*[\"']?\s*>)|([?][>])|([%][>])~i",
                   $this->data, $match, PREG_OFFSET_CAPTURE, $this->counter);
        if (isset($match[ 0 ][ 1 ])) {
            $to = $match[ 0 ][ 1 ];
        }
        $this->value = substr($this->data, $this->counter, $to - $this->counter);
        $this->token = Smarty_Internal_Templateparser::TP_TEXT;
    }

    public function yylex2()
    {
        if (!isset($this->yy_global_pattern2)) {
            $this->yy_global_pattern2 =
                "/\G(" . $this->ldel . "\\s*(if|elseif|else if|while)\\s+)|\G(" . $this->ldel . "\\s*for\\s+)|\G(" .
                $this->ldel . "\\s*foreach(?![^\s]))|\G(" . $this->ldel . "\\s*setfilter\\s+)|\G(" . $this->ldel .
                "\\s*[0-9]*[a-zA-Z_]\\w*(\\s+nocache)?\\s*" . $this->rdel . ")|\G(" . $this->ldel .
                "\\s*[\/](?:(?!block)[0-9]*[a-zA-Z_]\\w*)\\s*" . $this->rdel . ")|\G(" . $this->ldel .
                "\\s*[$][0-9]*[a-zA-Z_]\\w*(\\s+nocache)?\\s*" . $this->rdel . ")|\G(" . $this->ldel . "\\s*[\/])|\G(" .
                $this->ldel . "\\s*)/isS";
        }
        if ($this->counter >= strlen($this->data)) {
            return false; // end of input
        }

        do {
            if (preg_match($this->yy_global_pattern2, $this->data, $yymatches, null, $this->counter)) {
                $yysubmatches = $yymatches;
                if (strlen($yysubmatches[ 0 ]) < 200) {
                    $yymatches = preg_grep("/(.|\s)+/", $yysubmatches);
                } else {
                    $yymatches = array_filter($yymatches, 'strlen');
                }
                if (empty($yymatches)) {
                    throw new Exception('Error: lexing failed because a rule matched' . ' an empty string.  Input "' .
                                        substr($this->data, $this->counter, 5) . '... state TAG');
                }
                next($yymatches); // skip global match
                $this->token = key($yymatches); // token number
                $this->value = current($yymatches); // token value
                $r = $this->{'yy_r2_' . $this->token}();
                if ($r === null) {
                    $this->counter += strlen($this->value);
                    $this->line += substr_count($this->value, "\n");
                    // accept this token
                    return true;
                } elseif ($r === true) {
                    // we have changed state
                    // process this token in the new state
                    return $this->yylex();
                } elseif ($r === false) {
                    $this->counter += strlen($this->value);
                    $this->line += substr_count($this->value, "\n");
                    if ($this->counter >= strlen($this->data)) {
                        return false; // end of input
                    }
                    // skip this token
                    continue;
                }
            } else {
                throw new Exception('Unexpected input at line' . $this->line . ': ' . $this->data[ $this->counter ]);
            }
            break;
        } while (true);
    } // end function

    const TAG = 2;

    function yy_r2_1()
    {

        $this->token = Smarty_Internal_Templateparser::TP_LDELIF;
        $this->yybegin(self::TAGBODY);
        $this->taglineno = $this->line;
    }

    function yy_r2_3()
    {

        $this->token = Smarty_Internal_Templateparser::TP_LDELFOR;
        $this->yybegin(self::TAGBODY);
        $this->taglineno = $this->line;
    }

    function yy_r2_4()
    {

        $this->token = Smarty_Internal_Templateparser::TP_LDELFOREACH;
        $this->yybegin(self::TAGBODY);
        $this->taglineno = $this->line;
    }

    function yy_r2_5()
    {

        $this->token = Smarty_Internal_Templateparser::TP_LDELSETFILTER;
        $this->yybegin(self::TAGBODY);
        $this->taglineno = $this->line;
    }

    function yy_r2_6()
    {

        $this->yypopstate();
        $this->token = Smarty_Internal_Templateparser::TP_SIMPLETAG;
        $this->taglineno = $this->line;
    }

    function yy_r2_8()
    {

        $this->yypopstate();
        $this->token = Smarty_Internal_Templateparser::TP_CLOSETAG;
        $this->taglineno = $this->line;
    }

    function yy_r2_9()
    {

        if ($this->_yy_stack[ count($this->_yy_stack) - 1 ] == self::TEXT) {
            $this->yypopstate();
            $this->token = Smarty_Internal_Templateparser::TP_SIMPELOUTPUT;
            $this->taglineno = $this->line;
        } else {
            $this->value = $this->smarty->left_delimiter;
            $this->token = Smarty_Internal_Templateparser::TP_LDEL;
            $this->yybegin(self::TAGBODY);
            $this->taglineno = $this->line;
        }
    }

    function yy_r2_11()
    {

        $this->token = Smarty_Internal_Templateparser::TP_LDELSLASH;
        $this->yybegin(self::TAGBODY);
        $this->taglineno = $this->line;
    }

    function yy_r2_12()
    {

        $this->token = Smarty_Internal_Templateparser::TP_LDEL;
        $this->yybegin(self::TAGBODY);
        $this->taglineno = $this->line;
    }

    public function yylex3()
    {
        if (!isset($this->yy_global_pattern3)) {
            $this->yy_global_pattern3 = "/\G(\\s*" . $this->rdel . ")|\G(" . $this->ldel .
                "\\s*)|\G([\"])|\G('[^'\\\\]*(?:\\\\.[^'\\\\]*)*')|\G([$]smarty\\.block\\.(child|parent))|\G([$][0-9]*[a-zA-Z_]\\w*)|\G([$])|\G(\\s+is\\s+in\\s+)|\G(\\s+as\\s+)|\G(\\s+to\\s+)|\G(\\s+step\\s+)|\G(\\s+instanceof\\s+)|\G(\\s*(([!=][=]{1,2})|([<][=>]?)|([>][=]?)|[&|]{2})\\s*)|\G(\\s+(eq|ne|neq|gt|ge|gte|lt|le|lte|mod|and|or|xor|(is\\s+(not\\s+)?(odd|even|div)\\s+by))\\s+)|\G(\\s+is\\s+(not\\s+)?(odd|even))|\G(([!]\\s*)|(not\\s+))|\G([(](int(eger)?|bool(ean)?|float|double|real|string|binary|array|object)[)]\\s*)|\G(\\s*[(]\\s*)|\G(\\s*[)])|\G(\\[\\s*)|\G(\\s*\\])|\G(\\s*[-][>]\\s*)|\G(\\s*[=][>]\\s*)|\G(\\s*[=]\\s*)|\G(([+]|[-]){2})|\G(\\s*([+]|[-])\\s*)|\G(\\s*([*]{1,2}|[%\/^&]|[<>]{2})\\s*)|\G([@])|\G([#])|\G(\\s+[0-9]*[a-zA-Z_][a-zA-Z0-9_\-:]*\\s*[=]\\s*)|\G(([0-9]*[a-zA-Z_]\\w*)?(\\\\[0-9]*[a-zA-Z_]\\w*)+)|\G([0-9]*[a-zA-Z_]\\w*)|\G(\\d+)|\G([`])|\G([|])|\G([.])|\G(\\s*[,]\\s*)|\G(\\s*[;]\\s*)|\G([:]{2})|\G(\\s*[:]\\s*)|\G(\\s*[?]\\s*)|\G(0[xX][0-9a-fA-F]+)|\G(\\s+)|\G([\S\s])/isS";
        }
        if ($this->counter >= strlen($this->data)) {
            return false; // end of input
        }

        do {
            if (preg_match($this->yy_global_pattern3, $this->data, $yymatches, null, $this->counter)) {
                $yysubmatches = $yymatches;
                if (strlen($yysubmatches[ 0 ]) < 200) {
                    $yymatches = preg_grep("/(.|\s)+/", $yysubmatches);
                } else {
                    $yymatches = array_filter($yymatches, 'strlen');
                }
                if (empty($yymatches)) {
                    throw new Exception('Error: lexing failed because a rule matched' . ' an empty string.  Input "' .
                                        substr($this->data, $this->counter, 5) . '... state TAGBODY');
                }
                next($yymatches); // skip global match
                $this->token = key($yymatches); // token number
                $this->value = current($yymatches); // token value
                $r = $this->{'yy_r3_' . $this->token}();
                if ($r === null) {
                    $this->counter += strlen($this->value);
                    $this->line += substr_count($this->value, "\n");
                    // accept this token
                    return true;
                } elseif ($r === true) {
                    // we have changed state
                    // process this token in the new state
                    return $this->yylex();
                } elseif ($r === false) {
                    $this->counter += strlen($this->value);
                    $this->line += substr_count($this->value, "\n");
                    if ($this->counter >= strlen($this->data)) {
                        return false; // end of input
                    }
                    // skip this token
                    continue;
                }
            } else {
                throw new Exception('Unexpected input at line' . $this->line . ': ' . $this->data[ $this->counter ]);
            }
            break;
        } while (true);
    } // end function

    const TAGBODY = 3;

    function yy_r3_1()
    {

        $this->token = Smarty_Internal_Templateparser::TP_RDEL;
        $this->yypopstate();
    }

    function yy_r3_2()
    {

        if ($this->smarty->auto_literal && isset($this->value[ $this->ldel_length ]) ?
            strpos(" \n\t\r", $this->value[ $this->ldel_length ]) !== false : false
        ) {
            $this->token = Smarty_Internal_Templateparser::TP_TEXT;
        } else {
            $this->yypushstate(self::TAG);
            return true;
        }
    }

    function yy_r3_3()
    {

        $this->token = Smarty_Internal_Templateparser::TP_QUOTE;
        $this->yypushstate(self::DOUBLEQUOTEDSTRING);
    }

    function yy_r3_4()
    {

        $this->token = Smarty_Internal_Templateparser::TP_SINGLEQUOTESTRING;
    }

    function yy_r3_5()
    {

        $this->token = Smarty_Internal_Templateparser::TP_SMARTYBLOCKCHILDPARENT;
        $this->taglineno = $this->line;
    }

    function yy_r3_7()
    {

        $this->token = Smarty_Internal_Templateparser::TP_DOLLARID;
    }

    function yy_r3_8()
    {

        $this->token = Smarty_Internal_Templateparser::TP_DOLLAR;
    }

    function yy_r3_9()
    {

        $this->token = Smarty_Internal_Templateparser::TP_ISIN;
    }

    function yy_r3_10()
    {

        $this->token = Smarty_Internal_Templateparser::TP_AS;
    }

    function yy_r3_11()
    {

        $this->token = Smarty_Internal_Templateparser::TP_TO;
    }

    function yy_r3_12()
    {

        $this->token = Smarty_Internal_Templateparser::TP_STEP;
    }

    function yy_r3_13()
    {

        $this->token = Smarty_Internal_Templateparser::TP_INSTANCEOF;
    }

    function yy_r3_14()
    {

        $this->token = Smarty_Internal_Templateparser::TP_LOGOP;
    }

    function yy_r3_19()
    {

        $this->token = Smarty_Internal_Templateparser::TP_TLOGOP;
    }

    function yy_r3_24()
    {

        $this->token = Smarty_Internal_Templateparser::TP_SINGLECOND;
    }

    function yy_r3_27()
    {

        $this->token = Smarty_Internal_Templateparser::TP_NOT;
    }

    function yy_r3_30()
    {

        $this->token = Smarty_Internal_Templateparser::TP_TYPECAST;
    }

    function yy_r3_34()
    {

        $this->token = Smarty_Internal_Templateparser::TP_OPENP;
    }

    function yy_r3_35()
    {

        $this->token = Smarty_Internal_Templateparser::TP_CLOSEP;
    }

    function yy_r3_36()
    {

        $this->token = Smarty_Internal_Templateparser::TP_OPENB;
    }

    function yy_r3_37()
    {

        $this->token = Smarty_Internal_Templateparser::TP_CLOSEB;
    }

    function yy_r3_38()
    {

        $this->token = Smarty_Internal_Templateparser::TP_PTR;
    }

    function yy_r3_39()
    {

        $this->token = Smarty_Internal_Templateparser::TP_APTR;
    }

    function yy_r3_40()
    {

        $this->token = Smarty_Internal_Templateparser::TP_EQUAL;
    }

    function yy_r3_41()
    {

        $this->token = Smarty_Internal_Templateparser::TP_INCDEC;
    }

    function yy_r3_43()
    {

        $this->token = Smarty_Internal_Templateparser::TP_UNIMATH;
    }

    function yy_r3_45()
    {

        $this->token = Smarty_Internal_Templateparser::TP_MATH;
    }

    function yy_r3_47()
    {

        $this->token = Smarty_Internal_Templateparser::TP_AT;
    }

    function yy_r3_48()
    {

        $this->token = Smarty_Internal_Templateparser::TP_HATCH;
    }

    function yy_r3_49()
    {

        // resolve conflicts with shorttag and right_delimiter starting with '='
        if (substr($this->data, $this->counter + strlen($this->value) - 1, $this->rdel_length) ==
            $this->smarty->right_delimiter
        ) {
            preg_match("~\s+~", $this->value, $match);
            $this->value = $match[ 0 ];
            $this->token = Smarty_Internal_Templateparser::TP_SPACE;
        } else {
            $this->token = Smarty_Internal_Templateparser::TP_ATTR;
        }
    }

    function yy_r3_50()
    {

        $this->token = Smarty_Internal_Templateparser::TP_NAMESPACE;
    }

    function yy_r3_53()
    {

        $this->token = Smarty_Internal_Templateparser::TP_ID;
    }

    function yy_r3_54()
    {

        $this->token = Smarty_Internal_Templateparser::TP_INTEGER;
    }

    function yy_r3_55()
    {

        $this->token = Smarty_Internal_Templateparser::TP_BACKTICK;
        $this->yypopstate();
    }

    function yy_r3_56()
    {

        $this->token = Smarty_Internal_Templateparser::TP_VERT;
    }

    function yy_r3_57()
    {

        $this->token = Smarty_Internal_Templateparser::TP_DOT;
    }

    function yy_r3_58()
    {

        $this->token = Smarty_Internal_Templateparser::TP_COMMA;
    }

    function yy_r3_59()
    {

        $this->token = Smarty_Internal_Templateparser::TP_SEMICOLON;
    }

    function yy_r3_60()
    {

        $this->token = Smarty_Internal_Templateparser::TP_DOUBLECOLON;
    }

    function yy_r3_61()
    {

        $this->token = Smarty_Internal_Templateparser::TP_COLON;
    }

    function yy_r3_62()
    {

        $this->token = Smarty_Internal_Templateparser::TP_QMARK;
    }

    function yy_r3_63()
    {

        $this->token = Smarty_Internal_Templateparser::TP_HEX;
    }

    function yy_r3_64()
    {

        $this->token = Smarty_Internal_Templateparser::TP_SPACE;
    }

    function yy_r3_65()
    {

        $this->token = Smarty_Internal_Templateparser::TP_TEXT;
    }

    public function yylex4()
    {
        if (!isset($this->yy_global_pattern4)) {
            $this->yy_global_pattern4 =
                "/\G(" . $this->ldel . "\\s*literal\\s*" . $this->rdel . ")|\G(" . $this->ldel . "\\s*[\/]literal\\s*" .
                $this->rdel . ")|\G([\S\s])/isS";
        }
        if ($this->counter >= strlen($this->data)) {
            return false; // end of input
        }

        do {
            if (preg_match($this->yy_global_pattern4, $this->data, $yymatches, null, $this->counter)) {
                $yysubmatches = $yymatches;
                if (strlen($yysubmatches[ 0 ]) < 200) {
                    $yymatches = preg_grep("/(.|\s)+/", $yysubmatches);
                } else {
                    $yymatches = array_filter($yymatches, 'strlen');
                }
                if (empty($yymatches)) {
                    throw new Exception('Error: lexing failed because a rule matched' . ' an empty string.  Input "' .
                                        substr($this->data, $this->counter, 5) . '... state LITERAL');
                }
                next($yymatches); // skip global match
                $this->token = key($yymatches); // token number
                $this->value = current($yymatches); // token value
                $r = $this->{'yy_r4_' . $this->token}();
                if ($r === null) {
                    $this->counter += strlen($this->value);
                    $this->line += substr_count($this->value, "\n");
                    // accept this token
                    return true;
                } elseif ($r === true) {
                    // we have changed state
                    // process this token in the new state
                    return $this->yylex();
                } elseif ($r === false) {
                    $this->counter += strlen($this->value);
                    $this->line += substr_count($this->value, "\n");
                    if ($this->counter >= strlen($this->data)) {
                        return false; // end of input
                    }
                    // skip this token
                    continue;
                }
            } else {
                throw new Exception('Unexpected input at line' . $this->line . ': ' . $this->data[ $this->counter ]);
            }
            break;
        } while (true);
    } // end function

    const LITERAL = 4;

    function yy_r4_1()
    {

        $this->literal_cnt ++;
        $this->token = Smarty_Internal_Templateparser::TP_LITERAL;
    }

    function yy_r4_2()
    {

        if ($this->literal_cnt) {
            $this->literal_cnt --;
            $this->token = Smarty_Internal_Templateparser::TP_LITERAL;
        } else {
            $this->token = Smarty_Internal_Templateparser::TP_LITERALEND;
            $this->yypopstate();
        }
    }

    function yy_r4_3()
    {

        $to = strlen($this->data);
        preg_match("~{$this->ldel}[/]?literal{$this->rdel}~i", $this->data, $match, PREG_OFFSET_CAPTURE,
                   $this->counter);
        if (isset($match[ 0 ][ 1 ])) {
            $to = $match[ 0 ][ 1 ];
        } else {
            $this->compiler->trigger_template_error("missing or misspelled literal closing tag");
        }
        $this->value = substr($this->data, $this->counter, $to - $this->counter);
        $this->token = Smarty_Internal_Templateparser::TP_LITERAL;
    }

    public function yylex5()
    {
        if (!isset($this->yy_global_pattern5)) {
            $this->yy_global_pattern5 =
                "/\G(" . $this->ldel . "\\s*literal\\s*" . $this->rdel . ")|\G(" . $this->ldel . "\\s*[\/]literal\\s*" .
                $this->rdel . ")|\G(" . $this->ldel . "\\s*[\/])|\G(" . $this->ldel . "\\s*[0-9]*[a-zA-Z_]\\w*)|\G(" .
                $this->ldel .
                "\\s*)|\G([\"])|\G([`][$])|\G([$][0-9]*[a-zA-Z_]\\w*)|\G([$])|\G(([^\"\\\\]*?)((?:\\\\.[^\"\\\\]*?)*?)(?=(" .
                $this->ldel . "|\\$|`\\$|\")))|\G([\S\s])/isS";
        }
        if ($this->counter >= strlen($this->data)) {
            return false; // end of input
        }

        do {
            if (preg_match($this->yy_global_pattern5, $this->data, $yymatches, null, $this->counter)) {
                $yysubmatches = $yymatches;
                if (strlen($yysubmatches[ 0 ]) < 200) {
                    $yymatches = preg_grep("/(.|\s)+/", $yysubmatches);
                } else {
                    $yymatches = array_filter($yymatches, 'strlen');
                }
                if (empty($yymatches)) {
                    throw new Exception('Error: lexing failed because a rule matched' . ' an empty string.  Input "' .
                                        substr($this->data, $this->counter, 5) . '... state DOUBLEQUOTEDSTRING');
                }
                next($yymatches); // skip global match
                $this->token = key($yymatches); // token number
                $this->value = current($yymatches); // token value
                $r = $this->{'yy_r5_' . $this->token}();
                if ($r === null) {
                    $this->counter += strlen($this->value);
                    $this->line += substr_count($this->value, "\n");
                    // accept this token
                    return true;
                } elseif ($r === true) {
                    // we have changed state
                    // process this token in the new state
                    return $this->yylex();
                } elseif ($r === false) {
                    $this->counter += strlen($this->value);
                    $this->line += substr_count($this->value, "\n");
                    if ($this->counter >= strlen($this->data)) {
                        return false; // end of input
                    }
                    // skip this token
                    continue;
                }
            } else {
                throw new Exception('Unexpected input at line' . $this->line . ': ' . $this->data[ $this->counter ]);
            }
            break;
        } while (true);
    } // end function

    const DOUBLEQUOTEDSTRING = 5;

    function yy_r5_1()
    {

        $this->token = Smarty_Internal_Templateparser::TP_TEXT;
    }

    function yy_r5_2()
    {

        $this->token = Smarty_Internal_Templateparser::TP_TEXT;
    }

    function yy_r5_3()
    {

        if ($this->smarty->auto_literal && isset($this->value[ $this->ldel_length ]) ?
            strpos(" \n\t\r", $this->value[ $this->ldel_length ]) !== false : false
        ) {
            $this->token = Smarty_Internal_Templateparser::TP_TEXT;
        } else {
            $this->yypushstate(self::TAG);
            return true;
        }
    }

    function yy_r5_4()
    {

        if ($this->smarty->auto_literal && isset($this->value[ $this->ldel_length ]) ?
            strpos(" \n\t\r", $this->value[ $this->ldel_length ]) !== false : false
        ) {
            $this->token = Smarty_Internal_Templateparser::TP_TEXT;
        } else {
            $this->yypushstate(self::TAG);
            return true;
        }
    }

    function yy_r5_5()
    {

        if ($this->smarty->auto_literal && isset($this->value[ $this->ldel_length ]) ?
            strpos(" \n\t\r", $this->value[ $this->ldel_length ]) !== false : false
        ) {
            $this->token = Smarty_Internal_Templateparser::TP_TEXT;
        } else {
            $this->token = Smarty_Internal_Templateparser::TP_LDEL;
            $this->taglineno = $this->line;
            $this->yypushstate(self::TAGBODY);
        }
    }

    function yy_r5_6()
    {

        $this->token = Smarty_Internal_Templateparser::TP_QUOTE;
        $this->yypopstate();
    }

    function yy_r5_7()
    {

        $this->token = Smarty_Internal_Templateparser::TP_BACKTICK;
        $this->value = substr($this->value, 0, - 1);
        $this->yypushstate(self::TAGBODY);
        $this->taglineno = $this->line;
    }

    function yy_r5_8()
    {

        $this->token = Smarty_Internal_Templateparser::TP_DOLLARID;
    }

    function yy_r5_9()
    {

        $this->token = Smarty_Internal_Templateparser::TP_TEXT;
    }

    function yy_r5_10()
    {

        $this->token = Smarty_Internal_Templateparser::TP_TEXT;
    }

    function yy_r5_14()
    {

        $to = strlen($this->data);
        $this->value = substr($this->data, $this->counter, $to - $this->counter);
        $this->token = Smarty_Internal_Templateparser::TP_TEXT;
    }

}

     