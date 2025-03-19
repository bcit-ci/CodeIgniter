<?php

namespace PhpParser;

class ErrorTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct() {
        $attributes = array(
            'startLine' => 10,
            'endLine' => 11,
        );
        $error = new Error('Some error', $attributes);

        $this->assertSame('Some error', $error->getRawMessage());
        $this->assertSame($attributes, $error->getAttributes());
        $this->assertSame(10, $error->getStartLine());
        $this->assertSame(11, $error->getEndLine());
        $this->assertSame(10, $error->getRawLine());
        $this->assertSame('Some error on line 10', $error->getMessage());

        return $error;
    }

    /**
     * @depends testConstruct
     */
    public function testSetMessageAndLine(Error $error) {
        $error->setRawMessage('Some other error');
        $this->assertSame('Some other error', $error->getRawMessage());

        $error->setStartLine(15);
        $this->assertSame(15, $error->getStartLine());
        $this->assertSame('Some other error on line 15', $error->getMessage());

        $error->setRawLine(17);
        $this->assertSame(17, $error->getRawLine());
        $this->assertSame('Some other error on line 17', $error->getMessage());
    }

    public function testUnknownLine() {
        $error = new Error('Some error');

        $this->assertSame(-1, $error->getStartLine());
        $this->assertSame(-1, $error->getEndLine());
        $this->assertSame(-1, $error->getRawLine());
        $this->assertSame('Some error on unknown line', $error->getMessage());
    }

    /** @dataProvider provideTestColumnInfo */
    public function testColumnInfo($code, $startPos, $endPos, $startColumn, $endColumn) {
        $error = new Error('Some error', array(
            'startFilePos' => $startPos,
            'endFilePos' => $endPos,
        ));

        $this->assertSame(true, $error->hasColumnInfo());
        $this->assertSame($startColumn, $error->getStartColumn($code));
        $this->assertSame($endColumn, $error->getEndColumn($code));

    }

    public function provideTestColumnInfo() {
        return array(
            // Error at "bar"
            array("<?php foo bar baz", 10, 12, 11, 13),
            array("<?php\nfoo bar baz", 10, 12, 5, 7),
            array("<?php foo\nbar baz", 10, 12, 1, 3),
            array("<?php foo bar\nbaz", 10, 12, 11, 13),
            array("<?php\r\nfoo bar baz", 11, 13, 5, 7),
            // Error at "baz"
            array("<?php foo bar baz", 14, 16, 15, 17),
            array("<?php foo bar\nbaz", 14, 16, 1, 3),
            // Error at string literal
            array("<?php foo 'bar\nbaz' xyz", 10, 18, 11, 4),
            array("<?php\nfoo 'bar\nbaz' xyz", 10, 18, 5, 4),
            array("<?php foo\n'\nbarbaz\n'\nxyz", 10, 19, 1, 1),
            // Error over full string
            array("<?php", 0, 4, 1, 5),
            array("<?\nphp", 0, 5, 1, 3),
        );
    }

    public function testNoColumnInfo() {
        $error = new Error('Some error', 3);

        $this->assertSame(false, $error->hasColumnInfo());
        try {
            $error->getStartColumn('');
            $this->fail('Expected RuntimeException');
        } catch (\RuntimeException $e) {
            $this->assertSame('Error does not have column information', $e->getMessage());
        }
        try {
            $error->getEndColumn('');
            $this->fail('Expected RuntimeException');
        } catch (\RuntimeException $e) {
            $this->assertSame('Error does not have column information', $e->getMessage());
        }
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Invalid position information
     */
    public function testInvalidPosInfo() {
        $error = new Error('Some error', array(
            'startFilePos' => 10,
            'endFilePos' => 11,
        ));
        $error->getStartColumn('code');
    }
}
