<?php

namespace PhpParser;

use PhpParser\Comment;
use PhpParser\Node\Expr;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt;
use PhpParser\PrettyPrinter\Standard;

require_once __DIR__ . '/CodeTestAbstract.php';

class PrettyPrinterTest extends CodeTestAbstract
{
    protected function doTestPrettyPrintMethod($method, $name, $code, $expected, $modeLine) {
        $lexer = new Lexer\Emulative;
        $parser5 = new Parser\Php5($lexer);
        $parser7 = new Parser\Php7($lexer);

        list($version, $options) = $this->parseModeLine($modeLine);
        $prettyPrinter = new Standard($options);

        try {
            $output5 = canonicalize($prettyPrinter->$method($parser5->parse($code)));
        } catch (Error $e) {
            $output5 = null;
            if ('php7' !== $version) {
                throw $e;
            }
        }

        try {
            $output7 = canonicalize($prettyPrinter->$method($parser7->parse($code)));
        } catch (Error $e) {
            $output7 = null;
            if ('php5' !== $version) {
                throw $e;
            }
        }

        if ('php5' === $version) {
            $this->assertSame($expected, $output5, $name);
            $this->assertNotSame($expected, $output7, $name);
        } else if ('php7' === $version) {
            $this->assertSame($expected, $output7, $name);
            $this->assertNotSame($expected, $output5, $name);
        } else {
            $this->assertSame($expected, $output5, $name);
            $this->assertSame($expected, $output7, $name);
        }
    }

    /**
     * @dataProvider provideTestPrettyPrint
     * @covers PhpParser\PrettyPrinter\Standard<extended>
     */
    public function testPrettyPrint($name, $code, $expected, $mode) {
        $this->doTestPrettyPrintMethod('prettyPrint', $name, $code, $expected, $mode);
    }

    /**
     * @dataProvider provideTestPrettyPrintFile
     * @covers PhpParser\PrettyPrinter\Standard<extended>
     */
    public function testPrettyPrintFile($name, $code, $expected, $mode) {
        $this->doTestPrettyPrintMethod('prettyPrintFile', $name, $code, $expected, $mode);
    }

    public function provideTestPrettyPrint() {
        return $this->getTests(__DIR__ . '/../code/prettyPrinter', 'test');
    }

    public function provideTestPrettyPrintFile() {
        return $this->getTests(__DIR__ . '/../code/prettyPrinter', 'file-test');
    }

    public function testPrettyPrintExpr() {
        $prettyPrinter = new Standard;
        $expr = new Expr\BinaryOp\Mul(
            new Expr\BinaryOp\Plus(new Expr\Variable('a'), new Expr\Variable('b')),
            new Expr\Variable('c')
        );
        $this->assertEquals('($a + $b) * $c', $prettyPrinter->prettyPrintExpr($expr));

        $expr = new Expr\Closure(array(
            'stmts' => array(new Stmt\Return_(new String_("a\nb")))
        ));
        $this->assertEquals("function () {\n    return 'a\nb';\n}", $prettyPrinter->prettyPrintExpr($expr));
    }

    public function testCommentBeforeInlineHTML() {
        $prettyPrinter = new PrettyPrinter\Standard;
        $comment = new Comment\Doc("/**\n * This is a comment\n */");
        $stmts = [new Stmt\InlineHTML('Hello World!', ['comments' => [$comment]])];
        $expected = "<?php\n\n/**\n * This is a comment\n */\n?>\nHello World!";
        $this->assertSame($expected, $prettyPrinter->prettyPrintFile($stmts));
    }

    private function parseModeLine($modeLine) {
        $parts = explode(' ', $modeLine, 2);
        $version = isset($parts[0]) ? $parts[0] : 'both';
        $options = isset($parts[1]) ? json_decode($parts[1], true) : [];
        return [$version, $options];
    }
}
