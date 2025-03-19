<?php

namespace PhpParser\Parser;

use PhpParser\Error;
use PhpParser\Lexer;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\ParserTest;
use PhpParser\Node\Expr;
use PhpParser\Node\Stmt;

require_once __DIR__ . '/../ParserTest.php';

class MultipleTest extends ParserTest {
    // This provider is for the generic parser tests, just pick an arbitrary order here
    protected function getParser(Lexer $lexer) {
        return new Multiple([new Php5($lexer), new Php7($lexer)]);
    }

    private function getPrefer7() {
        $lexer = new Lexer(['usedAttributes' => []]);
        return new Multiple([new Php7($lexer), new Php5($lexer)]);
    }

    private function getPrefer5() {
        $lexer = new Lexer(['usedAttributes' => []]);
        return new Multiple([new Php5($lexer), new Php7($lexer)]);
    }

    /** @dataProvider provideTestParse */
    public function testParse($code, Multiple $parser, $expected) {
        $this->assertEquals($expected, $parser->parse($code));
        $this->assertSame([], $parser->getErrors());
    }

    public function provideTestParse() {
        return [
            [
                // PHP 7 only code
                '<?php class Test { function function() {} }',
                $this->getPrefer5(),
                [
                    new Stmt\Class_('Test', ['stmts' => [
                        new Stmt\ClassMethod('function')
                    ]]),
                ]
            ],
            [
                // PHP 5 only code
                '<?php global $$a->b;',
                $this->getPrefer7(),
                [
                    new Stmt\Global_([
                        new Expr\Variable(new Expr\PropertyFetch(new Expr\Variable('a'), 'b'))
                    ])
                ]
            ],
            [
                // Different meaning (PHP 5)
                '<?php $$a[0];',
                $this->getPrefer5(),
                [
                    new Expr\Variable(
                        new Expr\ArrayDimFetch(new Expr\Variable('a'), new LNumber(0))
                    )
                ]
            ],
            [
                // Different meaning (PHP 7)
                '<?php $$a[0];',
                $this->getPrefer7(),
                [
                    new Expr\ArrayDimFetch(
                        new Expr\Variable(new Expr\Variable('a')), new LNumber(0)
                    )
                ]
            ],
        ];
    }

    public function testThrownError() {
        $this->setExpectedException('PhpParser\Error', 'FAIL A');

        $parserA = $this->getMockBuilder('PhpParser\Parser')->getMock();
        $parserA->expects($this->at(0))
            ->method('parse')->will($this->throwException(new Error('FAIL A')));

        $parserB = $this->getMockBuilder('PhpParser\Parser')->getMock();
        $parserB->expects($this->at(0))
            ->method('parse')->will($this->throwException(new Error('FAIL B')));

        $parser = new Multiple([$parserA, $parserB]);
        $parser->parse('dummy');
    }

    public function testGetErrors() {
        $errorsA = [new Error('A1'), new Error('A2')];
        $parserA = $this->getMockBuilder('PhpParser\Parser')->getMock();
        $parserA->expects($this->at(0))->method('parse');
        $parserA->expects($this->at(1))
            ->method('getErrors')->will($this->returnValue($errorsA));

        $errorsB = [new Error('B1'), new Error('B2')];
        $parserB = $this->getMockBuilder('PhpParser\Parser')->getMock();
        $parserB->expects($this->at(0))->method('parse');
        $parserB->expects($this->at(1))
            ->method('getErrors')->will($this->returnValue($errorsB));

        $parser = new Multiple([$parserA, $parserB]);
        $parser->parse('dummy');
        $this->assertSame($errorsA, $parser->getErrors());
    }
}