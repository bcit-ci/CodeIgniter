/**
* Smarty Internal Plugin Configfileparser
*
* This is the config file parser
* 
* 
* @package Smarty
* @subpackage Config
* @author Uwe Tews
*/
%name TPC_
%declare_class {
/**
* Smarty Internal Plugin Configfileparse
*
* This is the config file parser.
* It is generated from the smarty_internal_configfileparser.y file
* @package Smarty
* @subpackage Compiler
* @author Uwe Tews
*/
class Smarty_Internal_Configfileparser
}
%include_class
{
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
    private static $escapes_single = Array('\\' => '\\',
                                           '\'' => '\'');

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
}

%token_prefix TPC_

%parse_accept
{
    $this->successful = !$this->internalError;
    $this->internalError = false;
    $this->retvalue = $this->_retvalue;
}

%syntax_error
{
    $this->internalError = true;
    $this->yymajor = $yymajor;
    $this->compiler->trigger_config_file_error();
}

%stack_overflow
{
    $this->internalError = true;
    $this->compiler->trigger_config_file_error("Stack overflow in configfile parser");
}

// Complete config file
start(res) ::= global_vars sections. {
    res = null;
}

// Global vars
global_vars(res) ::= var_list(vl). {
    $this->add_global_vars(vl);
    res = null;
}

// Sections
sections(res) ::= sections section. {
    res = null;
}

sections(res) ::= . {
    res = null;
}

section(res) ::= OPENB SECTION(i) CLOSEB newline var_list(vars). {
    $this->add_section_vars(i, vars);
    res = null;
}

section(res) ::= OPENB DOT SECTION(i) CLOSEB newline var_list(vars). {
    if ($this->configReadHidden) {
        $this->add_section_vars(i, vars);
    }
    res = null;
}

// Var list
var_list(res) ::= var_list(vl) newline. {
    res = vl;
}

var_list(res) ::= var_list(vl) var(v). {
    res = array_merge(vl, Array(v));
}

var_list(res) ::= . {
    res = Array();
}


// Var
var(res) ::= ID(id) EQUAL value(v). {
    res = Array("key" => id, "value" => v);
}


value(res) ::= FLOAT(i). {
    res = (float) i;
}

value(res) ::= INT(i). {
    res = (int) i;
}

value(res) ::= BOOL(i). {
    res = $this->parse_bool(i);
}

value(res) ::= SINGLE_QUOTED_STRING(i). {
    res = self::parse_single_quoted_string(i);
}

value(res) ::= DOUBLE_QUOTED_STRING(i). {
    res = self::parse_double_quoted_string(i);
}

value(res) ::= TRIPPLE_QUOTES(i) TRIPPLE_TEXT(c) TRIPPLE_QUOTES_END(ii). {
    res = self::parse_tripple_double_quoted_string(c);
}

value(res) ::= TRIPPLE_QUOTES(i) TRIPPLE_QUOTES_END(ii). {
    res = '';
}

value(res) ::= NAKED_STRING(i). {
    res = i;
}

// NOTE: this is not a valid rule
// It is added hier to produce a usefull error message on a missing '=';
value(res) ::= OTHER(i). {
    res = i;
}


// Newline and comments
newline(res) ::= NEWLINE. {
    res = null;
}

newline(res) ::= COMMENTSTART NEWLINE. {
    res = null;
}

newline(res) ::= COMMENTSTART NAKED_STRING NEWLINE. {
    res = null;
}
