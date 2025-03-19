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

namespace Doctrine\ORM\Query;

/**
 * Scans a DQL query for tokens.
 *
 * @author Guilherme Blanco <guilhermeblanco@hotmail.com>
 * @author Janne Vanhala <jpvanhal@cc.hut.fi>
 * @author Roman Borschel <roman@code-factory.org>
 * @since 2.0
 */
class Lexer extends \Doctrine\Common\Lexer
{
    // All tokens that are not valid identifiers must be < 100
    const T_NONE                = 1;
    const T_INTEGER             = 2;
    const T_STRING              = 3;
    const T_INPUT_PARAMETER     = 4;
    const T_FLOAT               = 5;
    const T_CLOSE_PARENTHESIS   = 6;
    const T_OPEN_PARENTHESIS    = 7;
    const T_COMMA               = 8;
    const T_DIVIDE              = 9;
    const T_DOT                 = 10;
    const T_EQUALS              = 11;
    const T_GREATER_THAN        = 12;
    const T_LOWER_THAN          = 13;
    const T_MINUS               = 14;
    const T_MULTIPLY            = 15;
    const T_NEGATE              = 16;
    const T_PLUS                = 17;
    const T_OPEN_CURLY_BRACE    = 18;
    const T_CLOSE_CURLY_BRACE   = 19;

    // All tokens that are also identifiers should be >= 100
    const T_IDENTIFIER          = 100;
    const T_ALL                 = 101;
    const T_AND                 = 102;
    const T_ANY                 = 103;
    const T_AS                  = 104;
    const T_ASC                 = 105;
    const T_AVG                 = 106;
    const T_BETWEEN             = 107;
    const T_BOTH                = 108;
    const T_BY                  = 109;
    const T_CASE                = 110;
    const T_COALESCE            = 111;
    const T_COUNT               = 112;
    const T_DELETE              = 113;
    const T_DESC                = 114;
    const T_DISTINCT            = 115;
    const T_ELSE                = 116;
    const T_EMPTY               = 117;
    const T_END                 = 118;
    const T_ESCAPE              = 119;
    const T_EXISTS              = 120;
    const T_FALSE               = 121;
    const T_FROM                = 122;
    const T_GROUP               = 123;
    const T_HAVING              = 124;
    const T_HIDDEN              = 125;
    const T_IN                  = 126;
    const T_INDEX               = 127;
    const T_INNER               = 128;
    const T_INSTANCE            = 129;
    const T_IS                  = 130;
    const T_JOIN                = 131;
    const T_LEADING             = 132;
    const T_LEFT                = 133;
    const T_LIKE                = 134;
    const T_MAX                 = 135;
    const T_MEMBER              = 136;
    const T_MIN                 = 137;
    const T_NOT                 = 138;
    const T_NULL                = 139;
    const T_NULLIF              = 140;
    const T_OF                  = 141;
    const T_OR                  = 142;
    const T_ORDER               = 143;
    const T_OUTER               = 144;
    const T_SELECT              = 145;
    const T_SET                 = 146;
    const T_SOME                = 147;
    const T_SUM                 = 148;
    const T_THEN                = 149;
    const T_TRAILING            = 150;
    const T_TRUE                = 151;
    const T_UPDATE              = 152;
    const T_WHEN                = 153;
    const T_WHERE               = 154;
    const T_WITH                = 155;
    const T_PARTIAL             = 156;
    const T_NEW                 = 157;

    /**
     * Creates a new query scanner object.
     *
     * @param string $input A query string.
     */
    public function __construct($input)
    {
        $this->setInput($input);
    }

    /**
     * @inheritdoc
     */
    protected function getCatchablePatterns()
    {
        return array(
            '[a-z_\\\][a-z0-9_\:\\\]*[a-z0-9_]{1}',
            '(?:[0-9]+(?:[\.][0-9]+)*)(?:e[+-]?[0-9]+)?',
            "'(?:[^']|'')*'",
            '\?[0-9]*|:[a-z_][a-z0-9_]*'
        );
    }

    /**
     * @inheritdoc
     */
    protected function getNonCatchablePatterns()
    {
        return array('\s+', '(.)');
    }

    /**
     * @inheritdoc
     */
    protected function getType(&$value)
    {
        $type = self::T_NONE;

        switch (true) {
            // Recognize numeric values
            case (is_numeric($value)):
                if (strpos($value, '.') !== false || stripos($value, 'e') !== false) {
                    return self::T_FLOAT;
                }

                return self::T_INTEGER;

            // Recognize quoted strings
            case ($value[0] === "'"):
                $value = str_replace("''", "'", substr($value, 1, strlen($value) - 2));

                return self::T_STRING;

            // Recognize identifiers
            case (ctype_alpha($value[0]) || $value[0] === '_'):
                $name = 'Doctrine\ORM\Query\Lexer::T_' . strtoupper($value);

                if (defined($name)) {
                    $type = constant($name);

                    if ($type > 100) {
                        return $type;
                    }
                }

                return self::T_IDENTIFIER;

            // Recognize input parameters
            case ($value[0] === '?' || $value[0] === ':'):
                return self::T_INPUT_PARAMETER;

            // Recognize symbols
            case ($value === '.'): return self::T_DOT;
            case ($value === ','): return self::T_COMMA;
            case ($value === '('): return self::T_OPEN_PARENTHESIS;
            case ($value === ')'): return self::T_CLOSE_PARENTHESIS;
            case ($value === '='): return self::T_EQUALS;
            case ($value === '>'): return self::T_GREATER_THAN;
            case ($value === '<'): return self::T_LOWER_THAN;
            case ($value === '+'): return self::T_PLUS;
            case ($value === '-'): return self::T_MINUS;
            case ($value === '*'): return self::T_MULTIPLY;
            case ($value === '/'): return self::T_DIVIDE;
            case ($value === '!'): return self::T_NEGATE;
            case ($value === '{'): return self::T_OPEN_CURLY_BRACE;
            case ($value === '}'): return self::T_CLOSE_CURLY_BRACE;

            // Default
            default:
                // Do nothing
        }

        return $type;
    }
}
