<?php

class TPC_yyToken implements ArrayAccess
{
    public $string = '';

    public $metadata = array();

    public function __construct($s, $m = array())
    {
        if ($s instanceof TPC_yyToken) {
            $this->string = $s->string;
            $this->metadata = $s->metadata;
        } else {
            $this->string = (string) $s;
            if ($m instanceof TPC_yyToken) {
                $this->metadata = $m->metadata;
            } elseif (is_array($m)) {
                $this->metadata = $m;
            }
        }
    }

    public function __toString()
    {
        return $this->string;
    }

    public function offsetExists($offset)
    {
        return isset($this->metadata[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->metadata[$offset];
    }

    public function offsetSet($offset, $value)
    {
        if ($offset === null) {
            if (isset($value[0])) {
                $x = ($value instanceof TPC_yyToken) ? $value->metadata : $value;
                $this->metadata = array_merge($this->metadata, $x);

                return;
            }
            $offset = count($this->metadata);
        }
        if ($value === null) {
            return;
        }
        if ($value instanceof TPC_yyToken) {
            if ($value->metadata) {
                $this->metadata[$offset] = $value->metadata;
            }
        } elseif ($value) {
            $this->metadata[$offset] = $value;
        }
    }

    public function offsetUnset($offset)
    {
        unset($this->metadata[$offset]);
    }
}

class TPC_yyStackEntry
{
    public $stateno;       /* The state-number */
    public $major;         /* The major token value.  This is the code
                     ** number for the token at this stack level */
    public $minor; /* The user-supplied minor token value.  This
                     ** is the value of the token  */
}

;

#line 12 "../smarty/lexer/smarty_internal_configfileparser.y"

/**
 * Smarty Internal Plugin Configfileparse
 *
 * This is the config file parser.
 * It is generated from the smarty_internal_configfileparser.y file
 *
 * @package    Smarty
 * @subpackage Compiler
 * @author     Uwe Tews
 */
class Smarty_Internal_Configfileparser
{
    #line 25 "../smarty/lexer/smarty_internal_configfileparser.y"

    /**
     * result status
     *
     * @var bool
     */
    public $successful = true;

    /**
     * return value
     *
     * @var mixed
     */
    public $retvalue = 0;

    /**
     * @var
     */
    public $yymajor;

    /**
     * lexer object
     *
     * @var Smarty_Internal_Configfilelexer
     */
    private $lex;

    /**
     * internal error flag
     *
     * @var bool
     */
    private $internalError = false;

    /**
     * compiler object
     *
     * @var Smarty_Internal_Config_File_Compiler
     */
    public $compiler = null;

    /**
     * smarty object
     *
     * @var Smarty
     */
    public $smarty = null;

    /**
     * copy of config_overwrite property
     *
     * @var bool
     */
    private $configOverwrite = false;

    /**
     * copy of config_read_hidden property
     *
     * @var bool
     */
    private $configReadHidden = false;

    /**
     * helper map
     *
     * @var array
     */
    private static $escapes_single = Array('\\' => '\\', '\'' => '\'');

    /**
     * constructor
     *
     * @param Smarty_Internal_Configfilelexer      $lex
     * @param Smarty_Internal_Config_File_Compiler $compiler
     */
    function __construct(Smarty_Internal_Configfilelexer $lex, Smarty_Internal_Config_File_Compiler $compiler)
    {
        // set instance object
        self::instance($this);
        $this->lex = $lex;
        $this->smarty = $compiler->smarty;
        $this->compiler = $compiler;
        $this->configOverwrite = $this->smarty->config_overwrite;
        $this->configReadHidden = $this->smarty->config_read_hidden;
    }

    /**
     * @param null $new_instance
     *
     * @return null
     */
    public static function &instance($new_instance = null)
    {
        static $instance = null;
        if (isset($new_instance) && is_object($new_instance)) {
            $instance = $new_instance;
        }
        return $instance;
    }

    /**
     * parse optional boolean keywords
     *
     * @param string $str
     *
     * @return bool
     */
    private function parse_bool($str)
    {
        $str = strtolower($str);
        if (in_array($str, array('on', 'yes', 'true'))) {
            $res = true;
        } else {
            $res = false;
        }
        return $res;
    }

    /**
     * parse single quoted string
     *  remove outer quotes
     *  unescape inner quotes
     *
     * @param string $qstr
     *
     * @return string
     */
    private static function parse_single_quoted_string($qstr)
    {
        $escaped_string = substr($qstr, 1, strlen($qstr) - 2); //remove outer quotes

        $ss = preg_split('/(\\\\.)/', $escaped_string, - 1, PREG_SPLIT_DELIM_CAPTURE);

        $str = "";
        foreach ($ss as $s) {
            if (strlen($s) === 2 && $s[0] === '\\') {
                if (isset(self::$escapes_single[$s[1]])) {
                    $s = self::$escapes_single[$s[1]];
                }
            }
            $str .= $s;
        }
        return $str;
    }

    /**
     * parse double quoted string
     *
     * @param string $qstr
     *
     * @return string
     */
    private static function parse_double_quoted_string($qstr)
    {
        $inner_str = substr($qstr, 1, strlen($qstr) - 2);
        return stripcslashes($inner_str);
    }

    /**
     * parse triple quoted string
     *
     * @param string $qstr
     *
     * @return string
     */
    private static function parse_tripple_double_quoted_string($qstr)
    {
        return stripcslashes($qstr);
    }

    /**
     * set a config variable in target array
     *
     * @param array $var
     * @param array $target_array
     */
    private function set_var(Array $var, Array &$target_array)
    {
        $key = $var["key"];
        $value = $var["value"];

        if ($this->configOverwrite || !isset($target_array['vars'][$key])) {
            $target_array['vars'][$key] = $value;
        } else {
            settype($target_array['vars'][$key], 'array');
            $target_array['vars'][$key][] = $value;
        }
    }

    /**
     * add config variable to global vars
     *
     * @param array $vars
     */
    private function add_global_vars(Array $vars)
    {
        if (!isset($this->compiler->config_data['vars'])) {
            $this->compiler->config_data['vars'] = Array();
        }
        foreach ($vars as $var) {
            $this->set_var($var, $this->compiler->config_data);
        }
    }

    /**
     * add config variable to section
     *
     * @param string $section_name
     * @param array  $vars
     */
    private function add_section_vars($section_name, Array $vars)
    {
        if (!isset($this->compiler->config_data['sections'][$section_name]['vars'])) {
            $this->compiler->config_data['sections'][$section_name]['vars'] = Array();
        }
        foreach ($vars as $var) {
            $this->set_var($var, $this->compiler->config_data['sections'][$section_name]);
        }
    }

    const TPC_OPENB = 1;

    const TPC_SECTION = 2;

    const TPC_CLOSEB = 3;

    const TPC_DOT = 4;

    const TPC_ID = 5;

    const TPC_EQUAL = 6;

    const TPC_FLOAT = 7;

    const TPC_INT = 8;

    const TPC_BOOL = 9;

    const TPC_SINGLE_QUOTED_STRING = 10;

    const TPC_DOUBLE_QUOTED_STRING = 11;

    const TPC_TRIPPLE_QUOTES = 12;

    const TPC_TRIPPLE_TEXT = 13;

    const TPC_TRIPPLE_QUOTES_END = 14;

    const TPC_NAKED_STRING = 15;

    const TPC_OTHER = 16;

    const TPC_NEWLINE = 17;

    const TPC_COMMENTSTART = 18;

    const YY_NO_ACTION = 60;

    const YY_ACCEPT_ACTION = 59;

    const YY_ERROR_ACTION = 58;

    const YY_SZ_ACTTAB = 38;

    static public $yy_action = array(29, 30, 34, 33, 24, 13, 19, 25, 35, 21, 59, 8, 3, 1, 20, 12, 14, 31, 20, 12, 15,
        17, 23, 18, 27, 26, 4, 5, 6, 32, 2, 11, 28, 22, 16, 9, 7, 10,);

    static public $yy_lookahead = array(7, 8, 9, 10, 11, 12, 5, 27, 15, 16, 20, 21, 23, 23, 17, 18, 13, 14, 17, 18, 15,
        2, 17, 4, 25, 26, 6, 3, 3, 14, 23, 1, 24, 17, 2, 25, 22, 25,);

    const YY_SHIFT_USE_DFLT = - 8;

    const YY_SHIFT_MAX = 19;

    static public $yy_shift_ofst = array(- 8, 1, 1, 1, - 7, - 3, - 3, 30, - 8, - 8, - 8, 19, 5, 3, 15, 16, 24, 25, 32,
        20,);

    const YY_REDUCE_USE_DFLT = - 21;

    const YY_REDUCE_MAX = 10;

    static public $yy_reduce_ofst = array(- 10, - 1, - 1, - 1, - 20, 10, 12, 8, 14, 7, - 11,);

    static public $yyExpectedTokens = array(array(), array(5, 17, 18,), array(5, 17, 18,), array(5, 17, 18,),
        array(7, 8, 9, 10, 11, 12, 15, 16,), array(17, 18,), array(17, 18,), array(1,), array(), array(), array(),
        array(2, 4,), array(15, 17,), array(13, 14,), array(14,), array(17,), array(3,), array(3,), array(2,),
        array(6,), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(),
        array(), array(), array(), array(), array(),);

    static public $yy_default = array(44, 37, 41, 40, 58, 58, 58, 36, 39, 44, 44, 58, 58, 58, 58, 58, 58, 58, 58, 58,
        55, 54, 57, 56, 50, 45, 43, 42, 38, 46, 47, 52, 51, 49, 48, 53,);

    const YYNOCODE = 29;

    const YYSTACKDEPTH = 100;

    const YYNSTATE = 36;

    const YYNRULE = 22;

    const YYERRORSYMBOL = 19;

    const YYERRSYMDT = 'yy0';

    const YYFALLBACK = 0;

    public static $yyFallback = array();

    public function Trace($TraceFILE, $zTracePrompt)
    {
        if (!$TraceFILE) {
            $zTracePrompt = 0;
        } elseif (!$zTracePrompt) {
            $TraceFILE = 0;
        }
        $this->yyTraceFILE = $TraceFILE;
        $this->yyTracePrompt = $zTracePrompt;
    }

    public function PrintTrace()
    {
        $this->yyTraceFILE = fopen('php://output', 'w');
        $this->yyTracePrompt = '<br>';
    }

    public $yyTraceFILE;

    public $yyTracePrompt;

    public $yyidx;                    /* Index of top element in stack */
    public $yyerrcnt;                 /* Shifts left before out of the error */
    public $yystack = array();  /* The parser's stack */

    public $yyTokenName = array('$', 'OPENB', 'SECTION', 'CLOSEB', 'DOT', 'ID', 'EQUAL', 'FLOAT', 'INT', 'BOOL',
        'SINGLE_QUOTED_STRING', 'DOUBLE_QUOTED_STRING', 'TRIPPLE_QUOTES', 'TRIPPLE_TEXT', 'TRIPPLE_QUOTES_END',
        'NAKED_STRING', 'OTHER', 'NEWLINE', 'COMMENTSTART', 'error', 'start', 'global_vars', 'sections', 'var_list',
        'section', 'newline', 'var', 'value',);

    public static $yyRuleName = array('start ::= global_vars sections', 'global_vars ::= var_list',
        'sections ::= sections section', 'sections ::=', 'section ::= OPENB SECTION CLOSEB newline var_list',
        'section ::= OPENB DOT SECTION CLOSEB newline var_list', 'var_list ::= var_list newline',
        'var_list ::= var_list var', 'var_list ::=', 'var ::= ID EQUAL value', 'value ::= FLOAT', 'value ::= INT',
        'value ::= BOOL', 'value ::= SINGLE_QUOTED_STRING', 'value ::= DOUBLE_QUOTED_STRING',
        'value ::= TRIPPLE_QUOTES TRIPPLE_TEXT TRIPPLE_QUOTES_END', 'value ::= TRIPPLE_QUOTES TRIPPLE_QUOTES_END',
        'value ::= NAKED_STRING', 'value ::= OTHER', 'newline ::= NEWLINE', 'newline ::= COMMENTSTART NEWLINE',
        'newline ::= COMMENTSTART NAKED_STRING NEWLINE',);

    public function tokenName($tokenType)
    {
        if ($tokenType === 0) {
            return 'End of Input';
        }
        if ($tokenType > 0 && $tokenType < count($this->yyTokenName)) {
            return $this->yyTokenName[$tokenType];
        } else {
            return "Unknown";
        }
    }

    public static function yy_destructor($yymajor, $yypminor)
    {
        switch ($yymajor) {
            default:
                break;   /* If no destructor action specified: do nothing */
        }
    }

    public function yy_pop_parser_stack()
    {
        if (empty($this->yystack)) {
            return;
        }
        $yytos = array_pop($this->yystack);
        if ($this->yyTraceFILE && $this->yyidx >= 0) {
            fwrite($this->yyTraceFILE, $this->yyTracePrompt . 'Popping ' . $this->yyTokenName[$yytos->major] . "\n");
        }
        $yymajor = $yytos->major;
        self::yy_destructor($yymajor, $yytos->minor);
        $this->yyidx --;

        return $yymajor;
    }

    public function __destruct()
    {
        while ($this->yystack !== Array()) {
            $this->yy_pop_parser_stack();
        }
        if (is_resource($this->yyTraceFILE)) {
            fclose($this->yyTraceFILE);
        }
    }

    public function yy_get_expected_tokens($token)
    {
        static $res3 = array();
        static $res4 = array();
        $state = $this->yystack[$this->yyidx]->stateno;
        $expected = self::$yyExpectedTokens[$state];
        if (isset($res3[$state][$token])) {
            if ($res3[$state][$token]) {
                return $expected;
            }
        } else {
            if ($res3[$state][$token] = in_array($token, self::$yyExpectedTokens[$state], true)) {
                return $expected;
            }
        }
        $stack = $this->yystack;
        $yyidx = $this->yyidx;
        do {
            $yyact = $this->yy_find_shift_action($token);
            if ($yyact >= self::YYNSTATE && $yyact < self::YYNSTATE + self::YYNRULE) {
                // reduce action
                $done = 0;
                do {
                    if ($done ++ == 100) {
                        $this->yyidx = $yyidx;
                        $this->yystack = $stack;
                        // too much recursion prevents proper detection
                        // so give up
                        return array_unique($expected);
                    }
                    $yyruleno = $yyact - self::YYNSTATE;
                    $this->yyidx -= self::$yyRuleInfo[$yyruleno][1];
                    $nextstate = $this->yy_find_reduce_action($this->yystack[$this->yyidx]->stateno, self::$yyRuleInfo[$yyruleno][0]);
                    if (isset(self::$yyExpectedTokens[$nextstate])) {
                        $expected = array_merge($expected, self::$yyExpectedTokens[$nextstate]);
                        if (isset($res4[$nextstate][$token])) {
                            if ($res4[$nextstate][$token]) {
                                $this->yyidx = $yyidx;
                                $this->yystack = $stack;
                                return array_unique($expected);
                            }
                        } else {
                            if ($res4[$nextstate][$token] = in_array($token, self::$yyExpectedTokens[$nextstate], true)) {
                                $this->yyidx = $yyidx;
                                $this->yystack = $stack;
                                return array_unique($expected);
                            }
                        }
                    }
                    if ($nextstate < self::YYNSTATE) {
                        // we need to shift a non-terminal
                        $this->yyidx ++;
                        $x = new TPC_yyStackEntry;
                        $x->stateno = $nextstate;
                        $x->major = self::$yyRuleInfo[$yyruleno][0];
                        $this->yystack[$this->yyidx] = $x;
                        continue 2;
                    } elseif ($nextstate == self::YYNSTATE + self::YYNRULE + 1) {
                        $this->yyidx = $yyidx;
                        $this->yystack = $stack;
                        // the last token was just ignored, we can't accept
                        // by ignoring input, this is in essence ignoring a
                        // syntax error!
                        return array_unique($expected);
                    } elseif ($nextstate === self::YY_NO_ACTION) {
                        $this->yyidx = $yyidx;
                        $this->yystack = $stack;
                        // input accepted, but not shifted (I guess)
                        return $expected;
                    } else {
                        $yyact = $nextstate;
                    }
                } while (true);
            }
            break;
        } while (true);
        $this->yyidx = $yyidx;
        $this->yystack = $stack;

        return array_unique($expected);
    }

    public function yy_is_expected_token($token)
    {
        static $res = array();
        static $res2 = array();
        if ($token === 0) {
            return true; // 0 is not part of this
        }
        $state = $this->yystack[$this->yyidx]->stateno;
        if (isset($res[$state][$token])) {
            if ($res[$state][$token]) {
                return true;
            }
        } else {
            if ($res[$state][$token] = in_array($token, self::$yyExpectedTokens[$state], true)) {
                return true;
            }
        }
        $stack = $this->yystack;
        $yyidx = $this->yyidx;
        do {
            $yyact = $this->yy_find_shift_action($token);
            if ($yyact >= self::YYNSTATE && $yyact < self::YYNSTATE + self::YYNRULE) {
                // reduce action
                $done = 0;
                do {
                    if ($done ++ == 100) {
                        $this->yyidx = $yyidx;
                        $this->yystack = $stack;
                        // too much recursion prevents proper detection
                        // so give up
                        return true;
                    }
                    $yyruleno = $yyact - self::YYNSTATE;
                    $this->yyidx -= self::$yyRuleInfo[$yyruleno][1];
                    $nextstate = $this->yy_find_reduce_action($this->yystack[$this->yyidx]->stateno, self::$yyRuleInfo[$yyruleno][0]);
                    if (isset($res2[$nextstate][$token])) {
                        if ($res2[$nextstate][$token]) {
                            $this->yyidx = $yyidx;
                            $this->yystack = $stack;
                            return true;
                        }
                    } else {
                        if ($res2[$nextstate][$token] = (isset(self::$yyExpectedTokens[$nextstate]) && in_array($token, self::$yyExpectedTokens[$nextstate], true))) {
                            $this->yyidx = $yyidx;
                            $this->yystack = $stack;
                            return true;
                        }
                    }
                    if ($nextstate < self::YYNSTATE) {
                        // we need to shift a non-terminal
                        $this->yyidx ++;
                        $x = new TPC_yyStackEntry;
                        $x->stateno = $nextstate;
                        $x->major = self::$yyRuleInfo[$yyruleno][0];
                        $this->yystack[$this->yyidx] = $x;
                        continue 2;
                    } elseif ($nextstate == self::YYNSTATE + self::YYNRULE + 1) {
                        $this->yyidx = $yyidx;
                        $this->yystack = $stack;
                        if (!$token) {
                            // end of input: this is valid
                            return true;
                        }
                        // the last token was just ignored, we can't accept
                        // by ignoring input, this is in essence ignoring a
                        // syntax error!
                        return false;
                    } elseif ($nextstate === self::YY_NO_ACTION) {
                        $this->yyidx = $yyidx;
                        $this->yystack = $stack;
                        // input accepted, but not shifted (I guess)
                        return true;
                    } else {
                        $yyact = $nextstate;
                    }
                } while (true);
            }
            break;
        } while (true);
        $this->yyidx = $yyidx;
        $this->yystack = $stack;

        return true;
    }

    public function yy_find_shift_action($iLookAhead)
    {
        $stateno = $this->yystack[$this->yyidx]->stateno;

        /* if ($this->yyidx < 0) return self::YY_NO_ACTION;  */
        if (!isset(self::$yy_shift_ofst[$stateno])) {
            // no shift actions
            return self::$yy_default[$stateno];
        }
        $i = self::$yy_shift_ofst[$stateno];
        if ($i === self::YY_SHIFT_USE_DFLT) {
            return self::$yy_default[$stateno];
        }
        if ($iLookAhead == self::YYNOCODE) {
            return self::YY_NO_ACTION;
        }
        $i += $iLookAhead;
        if ($i < 0 || $i >= self::YY_SZ_ACTTAB || self::$yy_lookahead[$i] != $iLookAhead) {
            if (count(self::$yyFallback) && $iLookAhead < count(self::$yyFallback) && ($iFallback = self::$yyFallback[$iLookAhead]) != 0) {
                if ($this->yyTraceFILE) {
                    fwrite($this->yyTraceFILE, $this->yyTracePrompt . "FALLBACK " . $this->yyTokenName[$iLookAhead] . " => " . $this->yyTokenName[$iFallback] . "\n");
                }

                return $this->yy_find_shift_action($iFallback);
            }

            return self::$yy_default[$stateno];
        } else {
            return self::$yy_action[$i];
        }
    }

    public function yy_find_reduce_action($stateno, $iLookAhead)
    {
        /* $stateno = $this->yystack[$this->yyidx]->stateno; */

        if (!isset(self::$yy_reduce_ofst[$stateno])) {
            return self::$yy_default[$stateno];
        }
        $i = self::$yy_reduce_ofst[$stateno];
        if ($i == self::YY_REDUCE_USE_DFLT) {
            return self::$yy_default[$stateno];
        }
        if ($iLookAhead == self::YYNOCODE) {
            return self::YY_NO_ACTION;
        }
        $i += $iLookAhead;
        if ($i < 0 || $i >= self::YY_SZ_ACTTAB || self::$yy_lookahead[$i] != $iLookAhead) {
            return self::$yy_default[$stateno];
        } else {
            return self::$yy_action[$i];
        }
    }

    public function yy_shift($yyNewState, $yyMajor, $yypMinor)
    {
        $this->yyidx ++;
        if ($this->yyidx >= self::YYSTACKDEPTH) {
            $this->yyidx --;
            if ($this->yyTraceFILE) {
                fprintf($this->yyTraceFILE, "%sStack Overflow!\n", $this->yyTracePrompt);
            }
            while ($this->yyidx >= 0) {
                $this->yy_pop_parser_stack();
            }
            #line 255 "../smarty/lexer/smarty_internal_configfileparser.y"

            $this->internalError = true;
            $this->compiler->trigger_config_file_error("Stack overflow in configfile parser");

            return;
        }
        $yytos = new TPC_yyStackEntry;
        $yytos->stateno = $yyNewState;
        $yytos->major = $yyMajor;
        $yytos->minor = $yypMinor;
        $this->yystack[] = $yytos;
        if ($this->yyTraceFILE && $this->yyidx > 0) {
            fprintf($this->yyTraceFILE, "%sShift %d\n", $this->yyTracePrompt, $yyNewState);
            fprintf($this->yyTraceFILE, "%sStack:", $this->yyTracePrompt);
            for ($i = 1; $i <= $this->yyidx; $i ++) {
                fprintf($this->yyTraceFILE, " %s", $this->yyTokenName[$this->yystack[$i]->major]);
            }
            fwrite($this->yyTraceFILE, "\n");
        }
    }

    public static $yyRuleInfo = array(array(0 => 20, 1 => 2), array(0 => 21, 1 => 1), array(0 => 22, 1 => 2),
        array(0 => 22, 1 => 0), array(0 => 24, 1 => 5), array(0 => 24, 1 => 6), array(0 => 23, 1 => 2),
        array(0 => 23, 1 => 2), array(0 => 23, 1 => 0), array(0 => 26, 1 => 3), array(0 => 27, 1 => 1),
        array(0 => 27, 1 => 1), array(0 => 27, 1 => 1), array(0 => 27, 1 => 1), array(0 => 27, 1 => 1),
        array(0 => 27, 1 => 3), array(0 => 27, 1 => 2), array(0 => 27, 1 => 1), array(0 => 27, 1 => 1),
        array(0 => 25, 1 => 1), array(0 => 25, 1 => 2), array(0 => 25, 1 => 3),);

    public static $yyReduceMap = array(0  => 0, 2 => 0, 3 => 0, 19 => 0, 20 => 0, 21 => 0, 1 => 1, 4 => 4, 5 => 5,
                                       6  => 6, 7 => 7, 8 => 8, 9 => 9, 10 => 10, 11 => 11, 12 => 12, 13 => 13,
                                       14 => 14, 15 => 15, 16 => 16, 17 => 17, 18 => 17,);

    #line 261 "../smarty/lexer/smarty_internal_configfileparser.y"
    function yy_r0()
    {
        $this->_retvalue = null;
    }

    #line 266 "../smarty/lexer/smarty_internal_configfileparser.y"
    function yy_r1()
    {
        $this->add_global_vars($this->yystack[$this->yyidx + 0]->minor);
        $this->_retvalue = null;
    }

    #line 280 "../smarty/lexer/smarty_internal_configfileparser.y"
    function yy_r4()
    {
        $this->add_section_vars($this->yystack[$this->yyidx + - 3]->minor, $this->yystack[$this->yyidx + 0]->minor);
        $this->_retvalue = null;
    }

    #line 285 "../smarty/lexer/smarty_internal_configfileparser.y"
    function yy_r5()
    {
        if ($this->configReadHidden) {
            $this->add_section_vars($this->yystack[$this->yyidx + - 3]->minor, $this->yystack[$this->yyidx + 0]->minor);
        }
        $this->_retvalue = null;
    }

    #line 293 "../smarty/lexer/smarty_internal_configfileparser.y"
    function yy_r6()
    {
        $this->_retvalue = $this->yystack[$this->yyidx + - 1]->minor;
    }

    #line 297 "../smarty/lexer/smarty_internal_configfileparser.y"
    function yy_r7()
    {
        $this->_retvalue = array_merge($this->yystack[$this->yyidx + - 1]->minor, Array($this->yystack[$this->yyidx + 0]->minor));
    }

    #line 301 "../smarty/lexer/smarty_internal_configfileparser.y"
    function yy_r8()
    {
        $this->_retvalue = Array();
    }

    #line 307 "../smarty/lexer/smarty_internal_configfileparser.y"
    function yy_r9()
    {
        $this->_retvalue = Array("key"   => $this->yystack[$this->yyidx + - 2]->minor,
                                 "value" => $this->yystack[$this->yyidx + 0]->minor);
    }

    #line 312 "../smarty/lexer/smarty_internal_configfileparser.y"
    function yy_r10()
    {
        $this->_retvalue = (float) $this->yystack[$this->yyidx + 0]->minor;
    }

    #line 316 "../smarty/lexer/smarty_internal_configfileparser.y"
    function yy_r11()
    {
        $this->_retvalue = (int) $this->yystack[$this->yyidx + 0]->minor;
    }

    #line 320 "../smarty/lexer/smarty_internal_configfileparser.y"
    function yy_r12()
    {
        $this->_retvalue = $this->parse_bool($this->yystack[$this->yyidx + 0]->minor);
    }

    #line 324 "../smarty/lexer/smarty_internal_configfileparser.y"
    function yy_r13()
    {
        $this->_retvalue = self::parse_single_quoted_string($this->yystack[$this->yyidx + 0]->minor);
    }

    #line 328 "../smarty/lexer/smarty_internal_configfileparser.y"
    function yy_r14()
    {
        $this->_retvalue = self::parse_double_quoted_string($this->yystack[$this->yyidx + 0]->minor);
    }

    #line 332 "../smarty/lexer/smarty_internal_configfileparser.y"
    function yy_r15()
    {
        $this->_retvalue = self::parse_tripple_double_quoted_string($this->yystack[$this->yyidx + - 1]->minor);
    }

    #line 336 "../smarty/lexer/smarty_internal_configfileparser.y"
    function yy_r16()
    {
        $this->_retvalue = '';
    }

    #line 340 "../smarty/lexer/smarty_internal_configfileparser.y"
    function yy_r17()
    {
        $this->_retvalue = $this->yystack[$this->yyidx + 0]->minor;
    }

    private $_retvalue;

    public function yy_reduce($yyruleno)
    {
        if ($this->yyTraceFILE && $yyruleno >= 0 && $yyruleno < count(self::$yyRuleName)) {
            fprintf($this->yyTraceFILE, "%sReduce (%d) [%s].\n", $this->yyTracePrompt, $yyruleno, self::$yyRuleName[$yyruleno]);
        }

        $this->_retvalue = $yy_lefthand_side = null;
        if (isset(self::$yyReduceMap[$yyruleno])) {
            // call the action
            $this->_retvalue = null;
            $this->{'yy_r' . self::$yyReduceMap[$yyruleno]}();
            $yy_lefthand_side = $this->_retvalue;
        }
        $yygoto = self::$yyRuleInfo[$yyruleno][0];
        $yysize = self::$yyRuleInfo[$yyruleno][1];
        $this->yyidx -= $yysize;
        for ($i = $yysize; $i; $i --) {
            // pop all of the right-hand side parameters
            array_pop($this->yystack);
        }
        $yyact = $this->yy_find_reduce_action($this->yystack[$this->yyidx]->stateno, $yygoto);
        if ($yyact < self::YYNSTATE) {
            if (!$this->yyTraceFILE && $yysize) {
                $this->yyidx ++;
                $x = new TPC_yyStackEntry;
                $x->stateno = $yyact;
                $x->major = $yygoto;
                $x->minor = $yy_lefthand_side;
                $this->yystack[$this->yyidx] = $x;
            } else {
                $this->yy_shift($yyact, $yygoto, $yy_lefthand_side);
            }
        } elseif ($yyact == self::YYNSTATE + self::YYNRULE + 1) {
            $this->yy_accept();
        }
    }

    public function yy_parse_failed()
    {
        if ($this->yyTraceFILE) {
            fprintf($this->yyTraceFILE, "%sFail!\n", $this->yyTracePrompt);
        }
        while ($this->yyidx >= 0) {
            $this->yy_pop_parser_stack();
        }
    }

    public function yy_syntax_error($yymajor, $TOKEN)
    {
        #line 248 "../smarty/lexer/smarty_internal_configfileparser.y"

        $this->internalError = true;
        $this->yymajor = $yymajor;
        $this->compiler->trigger_config_file_error();
    }

    public function yy_accept()
    {
        if ($this->yyTraceFILE) {
            fprintf($this->yyTraceFILE, "%sAccept!\n", $this->yyTracePrompt);
        }
        while ($this->yyidx >= 0) {
            $this->yy_pop_parser_stack();
        }
        #line 241 "../smarty/lexer/smarty_internal_configfileparser.y"

        $this->successful = !$this->internalError;
        $this->internalError = false;
        $this->retvalue = $this->_retvalue;
    }

    public function doParse($yymajor, $yytokenvalue)
    {
        $yyerrorhit = 0;   /* True if yymajor has invoked an error */

        if ($this->yyidx === null || $this->yyidx < 0) {
            $this->yyidx = 0;
            $this->yyerrcnt = - 1;
            $x = new TPC_yyStackEntry;
            $x->stateno = 0;
            $x->major = 0;
            $this->yystack = array();
            $this->yystack[] = $x;
        }
        $yyendofinput = ($yymajor == 0);

        if ($this->yyTraceFILE) {
            fprintf($this->yyTraceFILE, "%sInput %s\n", $this->yyTracePrompt, $this->yyTokenName[$yymajor]);
        }

        do {
            $yyact = $this->yy_find_shift_action($yymajor);
            if ($yymajor < self::YYERRORSYMBOL && !$this->yy_is_expected_token($yymajor)) {
                // force a syntax error
                $yyact = self::YY_ERROR_ACTION;
            }
            if ($yyact < self::YYNSTATE) {
                $this->yy_shift($yyact, $yymajor, $yytokenvalue);
                $this->yyerrcnt --;
                if ($yyendofinput && $this->yyidx >= 0) {
                    $yymajor = 0;
                } else {
                    $yymajor = self::YYNOCODE;
                }
            } elseif ($yyact < self::YYNSTATE + self::YYNRULE) {
                $this->yy_reduce($yyact - self::YYNSTATE);
            } elseif ($yyact == self::YY_ERROR_ACTION) {
                if ($this->yyTraceFILE) {
                    fprintf($this->yyTraceFILE, "%sSyntax Error!\n", $this->yyTracePrompt);
                }
                if (self::YYERRORSYMBOL) {
                    if ($this->yyerrcnt < 0) {
                        $this->yy_syntax_error($yymajor, $yytokenvalue);
                    }
                    $yymx = $this->yystack[$this->yyidx]->major;
                    if ($yymx == self::YYERRORSYMBOL || $yyerrorhit) {
                        if ($this->yyTraceFILE) {
                            fprintf($this->yyTraceFILE, "%sDiscard input token %s\n", $this->yyTracePrompt, $this->yyTokenName[$yymajor]);
                        }
                        $this->yy_destructor($yymajor, $yytokenvalue);
                        $yymajor = self::YYNOCODE;
                    } else {
                        while ($this->yyidx >= 0 && $yymx != self::YYERRORSYMBOL && ($yyact = $this->yy_find_shift_action(self::YYERRORSYMBOL)) >= self::YYNSTATE) {
                            $this->yy_pop_parser_stack();
                        }
                        if ($this->yyidx < 0 || $yymajor == 0) {
                            $this->yy_destructor($yymajor, $yytokenvalue);
                            $this->yy_parse_failed();
                            $yymajor = self::YYNOCODE;
                        } elseif ($yymx != self::YYERRORSYMBOL) {
                            $u2 = 0;
                            $this->yy_shift($yyact, self::YYERRORSYMBOL, $u2);
                        }
                    }
                    $this->yyerrcnt = 3;
                    $yyerrorhit = 1;
                } else {
                    if ($this->yyerrcnt <= 0) {
                        $this->yy_syntax_error($yymajor, $yytokenvalue);
                    }
                    $this->yyerrcnt = 3;
                    $this->yy_destructor($yymajor, $yytokenvalue);
                    if ($yyendofinput) {
                        $this->yy_parse_failed();
                    }
                    $yymajor = self::YYNOCODE;
                }
            } else {
                $this->yy_accept();
                $yymajor = self::YYNOCODE;
            }
        } while ($yymajor != self::YYNOCODE && $this->yyidx >= 0);
    }
}

