<?php
/**
 * Smarty shared plugin
 *
 * @package    Smarty
 * @subpackage PluginsShared
 */

if (!function_exists('smarty_mb_wordwrap')) {

    /**
     * Wrap a string to a given number of characters
     *
     * @link   http://php.net/manual/en/function.wordwrap.php for similarity
     *
     * @param  string  $str   the string to wrap
     * @param  int     $width the width of the output
     * @param  string  $break the character used to break the line
     * @param  boolean $cut   ignored parameter, just for the sake of
     *
     * @return string  wrapped string
     * @author Rodney Rehm
     */
    function smarty_mb_wordwrap($str, $width = 75, $break = "\n", $cut = false)
    {
        // break words into tokens using white space as a delimiter
        $tokens = preg_split('!(\s)!S' . Smarty::$_UTF8_MODIFIER, $str, - 1, PREG_SPLIT_NO_EMPTY + PREG_SPLIT_DELIM_CAPTURE);
        $length = 0;
        $t = '';
        $_previous = false;
        $_space = false;

        foreach ($tokens as $_token) {
            $token_length = mb_strlen($_token, Smarty::$_CHARSET);
            $_tokens = array($_token);
            if ($token_length > $width) {
                 if ($cut) {
                    $_tokens = preg_split('!(.{' . $width . '})!S' . Smarty::$_UTF8_MODIFIER, $_token, - 1, PREG_SPLIT_NO_EMPTY + PREG_SPLIT_DELIM_CAPTURE);
                }
            }

            foreach ($_tokens as $token) {
                $_space = !!preg_match('!^\s$!S' . Smarty::$_UTF8_MODIFIER, $token);
                $token_length = mb_strlen($token, Smarty::$_CHARSET);
                $length += $token_length;

                if ($length > $width) {
                    // remove space before inserted break
                    if ($_previous) {
                        $t = mb_substr($t, 0, - 1, Smarty::$_CHARSET);
                    }

                    if (!$_space) {
                        // add the break before the token
                        if (!empty($t)) {
                            $t .= $break;
                        }
                        $length = $token_length;
                    }
                } elseif ($token == "\n") {
                    // hard break must reset counters
                    $_previous = 0;
                    $length = 0;
                }
                $_previous = $_space;
                // add the token
                $t .= $token;
            }
        }

        return $t;
    }
}
