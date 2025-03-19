<?php

namespace PhpParser;

class CommentTest extends \PHPUnit_Framework_TestCase
{
    public function testGetSet() {
        $comment = new Comment('/* Some comment */', 1);

        $this->assertSame('/* Some comment */', $comment->getText());
        $this->assertSame('/* Some comment */', (string) $comment);
        $this->assertSame(1, $comment->getLine());

        $comment->setText('/* Some other comment */');
        $comment->setLine(10);

        $this->assertSame('/* Some other comment */', $comment->getText());
        $this->assertSame('/* Some other comment */', (string) $comment);
        $this->assertSame(10, $comment->getLine());
    }

    /**
     * @dataProvider provideTestReformatting
     */
    public function testReformatting($commentText, $reformattedText) {
        $comment = new Comment($commentText);
        $this->assertSame($reformattedText, $comment->getReformattedText());
    }

    public function provideTestReformatting() {
        return array(
            array('// Some text' . "\n", '// Some text'),
            array('/* Some text */', '/* Some text */'),
            array(
                '/**
     * Some text.
     * Some more text.
     */',
                '/**
 * Some text.
 * Some more text.
 */'
            ),
            array(
                '/*
        Some text.
        Some more text.
    */',
                '/*
    Some text.
    Some more text.
*/'
            ),
            array(
                '/* Some text.
       More text.
       Even more text. */',
                '/* Some text.
   More text.
   Even more text. */'
            ),
            // invalid comment -> no reformatting
            array(
                'hallo
    world',
                'hallo
    world',
            ),
        );
    }
}