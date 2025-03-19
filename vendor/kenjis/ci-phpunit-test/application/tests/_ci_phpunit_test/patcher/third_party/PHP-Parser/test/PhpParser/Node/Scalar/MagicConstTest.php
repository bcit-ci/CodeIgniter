<?php

namespace PhpParser\Node\Scalar;

class MagicConstTest extends \PHPUnit_Framework_TestCase {
    /**
     * @dataProvider provideTestGetName
     */
    public function testGetName(MagicConst $magicConst, $name) {
        $this->assertSame($name, $magicConst->getName());
    }

    public function provideTestGetName() {
        return array(
            array(new MagicConst\Class_, '__CLASS__'),
            array(new MagicConst\Dir, '__DIR__'),
            array(new MagicConst\File, '__FILE__'),
            array(new MagicConst\Function_, '__FUNCTION__'),
            array(new MagicConst\Line, '__LINE__'),
            array(new MagicConst\Method, '__METHOD__'),
            array(new MagicConst\Namespace_, '__NAMESPACE__'),
            array(new MagicConst\Trait_, '__TRAIT__'),
        );
    }
}