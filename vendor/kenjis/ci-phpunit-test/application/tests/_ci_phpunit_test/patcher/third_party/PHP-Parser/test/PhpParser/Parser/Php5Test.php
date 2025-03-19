<?php

namespace PhpParser\Parser;

use PhpParser\Lexer;
use PhpParser\ParserTest;

require_once __DIR__ . '/../ParserTest.php';

class Php5Test extends ParserTest {
    protected function getParser(Lexer $lexer) {
        return new Php5($lexer);
    }
}
