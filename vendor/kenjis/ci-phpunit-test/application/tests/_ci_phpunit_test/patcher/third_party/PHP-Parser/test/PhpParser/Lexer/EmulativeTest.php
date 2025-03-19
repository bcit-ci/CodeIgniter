<?php

namespace PhpParser\Lexer;

use PhpParser\LexerTest;
use PhpParser\Parser\Tokens;

require_once __DIR__ . '/../LexerTest.php';

class EmulativeTest extends LexerTest
{
    protected function getLexer(array $options = array()) {
        return new Emulative($options);
    }

    /**
     * @dataProvider provideTestReplaceKeywords
     */
    public function testReplaceKeywords($keyword, $expectedToken) {
        $lexer = $this->getLexer();
        $lexer->startLexing('<?php ' . $keyword);

        $this->assertSame($expectedToken, $lexer->getNextToken());
        $this->assertSame(0, $lexer->getNextToken());
    }

    /**
     * @dataProvider provideTestReplaceKeywords
     */
    public function testNoReplaceKeywordsAfterObjectOperator($keyword) {
        $lexer = $this->getLexer();
        $lexer->startLexing('<?php ->' . $keyword);

        $this->assertSame(Tokens::T_OBJECT_OPERATOR, $lexer->getNextToken());
        $this->assertSame(Tokens::T_STRING, $lexer->getNextToken());
        $this->assertSame(0, $lexer->getNextToken());
    }

    public function provideTestReplaceKeywords() {
        return array(
            // PHP 5.5
            array('finally',       Tokens::T_FINALLY),
            array('yield',         Tokens::T_YIELD),

            // PHP 5.4
            array('callable',      Tokens::T_CALLABLE),
            array('insteadof',     Tokens::T_INSTEADOF),
            array('trait',         Tokens::T_TRAIT),
            array('__TRAIT__',     Tokens::T_TRAIT_C),

            // PHP 5.3
            array('__DIR__',       Tokens::T_DIR),
            array('goto',          Tokens::T_GOTO),
            array('namespace',     Tokens::T_NAMESPACE),
            array('__NAMESPACE__', Tokens::T_NS_C),
        );
    }

    /**
     * @dataProvider provideTestLexNewFeatures
     */
    public function testLexNewFeatures($code, array $expectedTokens) {
        $lexer = $this->getLexer();
        $lexer->startLexing('<?php ' . $code);

        foreach ($expectedTokens as $expectedToken) {
            list($expectedTokenType, $expectedTokenText) = $expectedToken;
            $this->assertSame($expectedTokenType, $lexer->getNextToken($text));
            $this->assertSame($expectedTokenText, $text);
        }
        $this->assertSame(0, $lexer->getNextToken());
    }

    /**
     * @dataProvider provideTestLexNewFeatures
     */
    public function testLeaveStuffAloneInStrings($code) {
        $stringifiedToken = '"' . addcslashes($code, '"\\') . '"';

        $lexer = $this->getLexer();
        $lexer->startLexing('<?php ' . $stringifiedToken);

        $this->assertSame(Tokens::T_CONSTANT_ENCAPSED_STRING, $lexer->getNextToken($text));
        $this->assertSame($stringifiedToken, $text);
        $this->assertSame(0, $lexer->getNextToken());
    }

    public function provideTestLexNewFeatures() {
        return array(
            array('yield from', array(
                array(Tokens::T_YIELD_FROM, 'yield from'),
            )),
            array("yield\r\nfrom", array(
                array(Tokens::T_YIELD_FROM, "yield\r\nfrom"),
            )),
            array('...', array(
                array(Tokens::T_ELLIPSIS, '...'),
            )),
            array('**', array(
                array(Tokens::T_POW, '**'),
            )),
            array('**=', array(
                array(Tokens::T_POW_EQUAL, '**='),
            )),
            array('??', array(
                array(Tokens::T_COALESCE, '??'),
            )),
            array('<=>', array(
                array(Tokens::T_SPACESHIP, '<=>'),
            )),
            array('0b1010110', array(
                array(Tokens::T_LNUMBER, '0b1010110'),
            )),
            array('0b1011010101001010110101010010101011010101010101101011001110111100', array(
                array(Tokens::T_DNUMBER, '0b1011010101001010110101010010101011010101010101101011001110111100'),
            )),
            array('\\', array(
                array(Tokens::T_NS_SEPARATOR, '\\'),
            )),
            array("<<<'NOWDOC'\nNOWDOC;\n", array(
                array(Tokens::T_START_HEREDOC, "<<<'NOWDOC'\n"),
                array(Tokens::T_END_HEREDOC, 'NOWDOC'),
                array(ord(';'), ';'),
            )),
            array("<<<'NOWDOC'\nFoobar\nNOWDOC;\n", array(
                array(Tokens::T_START_HEREDOC, "<<<'NOWDOC'\n"),
                array(Tokens::T_ENCAPSED_AND_WHITESPACE, "Foobar\n"),
                array(Tokens::T_END_HEREDOC, 'NOWDOC'),
                array(ord(';'), ';'),
            )),
        );
    }
}
