<?php

namespace PhpParser;

use PhpParser\Comment;

require_once __DIR__ . '/CodeTestAbstract.php';

class CodeParsingTest extends CodeTestAbstract
{
    /**
     * @dataProvider provideTestParse
     */
    public function testParse($name, $code, $expected, $mode) {
        $lexer = new Lexer\Emulative(array('usedAttributes' => array(
            'startLine', 'endLine', 'startFilePos', 'endFilePos'
        )));
        $parser5 = new Parser\Php5($lexer, array(
            'throwOnError' => false,
        ));
        $parser7 = new Parser\Php7($lexer, array(
            'throwOnError' => false,
        ));

        $output5 = $this->getParseOutput($parser5, $code);
        $output7 = $this->getParseOutput($parser7, $code);

        if ($mode === 'php5') {
            $this->assertSame($expected, $output5, $name);
            $this->assertNotSame($expected, $output7, $name);
        } else if ($mode === 'php7') {
            $this->assertNotSame($expected, $output5, $name);
            $this->assertSame($expected, $output7, $name);
        } else {
            $this->assertSame($expected, $output5, $name);
            $this->assertSame($expected, $output7, $name);
        }
    }

    private function getParseOutput(Parser $parser, $code) {
        $stmts = $parser->parse($code);
        $errors = $parser->getErrors();

        $output = '';
        foreach ($errors as $error) {
            $output .= $this->formatErrorMessage($error, $code) . "\n";
        }

        if (null !== $stmts) {
            $dumper = new NodeDumper;
            $output .= $dumper->dump($stmts);
        }

        return canonicalize($output);
    }

    public function provideTestParse() {
        return $this->getTests(__DIR__ . '/../code/parser', 'test');
    }

    private function formatErrorMessage(Error $e, $code) {
        if ($e->hasColumnInfo()) {
            return $e->getRawMessage() . ' from ' . $e->getStartLine() . ':' . $e->getStartColumn($code)
                . ' to ' . $e->getEndLine() . ':' . $e->getEndColumn($code);
        } else {
            return $e->getMessage();
        }
    }
}
