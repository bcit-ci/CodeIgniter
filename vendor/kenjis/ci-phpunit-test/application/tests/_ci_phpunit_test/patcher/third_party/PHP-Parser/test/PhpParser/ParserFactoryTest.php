<?php

namespace PhpParser;

/* This test is very weak, because PHPUnit's assertEquals assertion is way too slow dealing with the
 * large objects involved here. So we just do some basic instanceof tests instead. */
class ParserFactoryTest extends \PHPUnit_Framework_TestCase {
    /** @dataProvider provideTestCreate */
    public function testCreate($kind, $lexer, $expected) {
        $this->assertInstanceOf($expected, (new ParserFactory)->create($kind, $lexer));
    }

    public function provideTestCreate() {
        $lexer = new Lexer();
        return [
            [
                ParserFactory::PREFER_PHP7, $lexer,
                'PhpParser\Parser\Multiple'
            ],
            [
                ParserFactory::PREFER_PHP5, null,
                'PhpParser\Parser\Multiple'
            ],
            [
                ParserFactory::ONLY_PHP7, null,
                'PhpParser\Parser\Php7'
            ],
            [
                ParserFactory::ONLY_PHP5, $lexer,
                'PhpParser\Parser\Php5'
            ]
        ];
    }
}