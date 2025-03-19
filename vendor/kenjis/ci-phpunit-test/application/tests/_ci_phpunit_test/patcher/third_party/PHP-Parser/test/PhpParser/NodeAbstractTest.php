<?php

namespace PhpParser;

class DummyNode extends NodeAbstract {
    public $subNode1;
    public $subNode2;

    public function __construct($subNode1, $subNode2, $attributes) {
        parent::__construct($attributes);
        $this->subNode1 = $subNode1;
        $this->subNode2 = $subNode2;
    }

    public function getSubNodeNames() {
        return array('subNode1', 'subNode2');
    }

    // This method is only overwritten because the node is located in an unusual namespace
    public function getType() {
        return 'Dummy';
    }
}

class NodeAbstractTest extends \PHPUnit_Framework_TestCase
{
    public function provideNodes() {
        $attributes = array(
            'startLine' => 10,
            'comments'  => array(
                new Comment('// Comment' . "\n"),
                new Comment\Doc('/** doc comment */'),
            ),
        );

        $node = new DummyNode('value1', 'value2', $attributes);
        $node->notSubNode = 'value3';

        return array(
            array($attributes, $node),
        );
    }

    /**
     * @dataProvider provideNodes
     */
    public function testConstruct(array $attributes, Node $node) {
        $this->assertSame('Dummy', $node->getType());
        $this->assertSame(array('subNode1', 'subNode2'), $node->getSubNodeNames());
        $this->assertSame(10, $node->getLine());
        $this->assertSame('/** doc comment */', $node->getDocComment()->getText());
        $this->assertSame('value1', $node->subNode1);
        $this->assertSame('value2', $node->subNode2);
        $this->assertTrue(isset($node->subNode1));
        $this->assertTrue(isset($node->subNode2));
        $this->assertFalse(isset($node->subNode3));
        $this->assertSame($attributes, $node->getAttributes());

        return $node;
    }

    /**
     * @dataProvider provideNodes
     */
    public function testGetDocComment(array $attributes, Node $node) {
        $this->assertSame('/** doc comment */', $node->getDocComment()->getText());
        array_pop($node->getAttribute('comments')); // remove doc comment
        $this->assertNull($node->getDocComment());
        array_pop($node->getAttribute('comments')); // remove comment
        $this->assertNull($node->getDocComment());
    }

    /**
     * @dataProvider provideNodes
     */
    public function testChange(array $attributes, Node $node) {
        // change of line
        $node->setLine(15);
        $this->assertSame(15, $node->getLine());

        // direct modification
        $node->subNode = 'newValue';
        $this->assertSame('newValue', $node->subNode);

        // indirect modification
        $subNode =& $node->subNode;
        $subNode = 'newNewValue';
        $this->assertSame('newNewValue', $node->subNode);

        // removal
        unset($node->subNode);
        $this->assertFalse(isset($node->subNode));
    }

    /**
     * @dataProvider provideNodes
     */
    public function testIteration(array $attributes, Node $node) {
        // Iteration is simple object iteration over properties,
        // not over subnodes
        $i = 0;
        foreach ($node as $key => $value) {
            if ($i === 0) {
                $this->assertSame('subNode1', $key);
                $this->assertSame('value1', $value);
            } else if ($i === 1) {
                $this->assertSame('subNode2', $key);
                $this->assertSame('value2', $value);
            } else if ($i === 2) {
                $this->assertSame('notSubNode', $key);
                $this->assertSame('value3', $value);
            } else {
                throw new \Exception;
            }
            $i++;
        }
        $this->assertSame(3, $i);
    }

    public function testAttributes() {
        /** @var $node Node */
        $node = $this->getMockForAbstractClass('PhpParser\NodeAbstract');

        $this->assertEmpty($node->getAttributes());

        $node->setAttribute('key', 'value');
        $this->assertTrue($node->hasAttribute('key'));
        $this->assertSame('value', $node->getAttribute('key'));

        $this->assertFalse($node->hasAttribute('doesNotExist'));
        $this->assertNull($node->getAttribute('doesNotExist'));
        $this->assertSame('default', $node->getAttribute('doesNotExist', 'default'));

        $node->setAttribute('null', null);
        $this->assertTrue($node->hasAttribute('null'));
        $this->assertNull($node->getAttribute('null'));
        $this->assertNull($node->getAttribute('null', 'default'));

        $this->assertSame(
            array(
                'key'  => 'value',
                'null' => null,
            ),
            $node->getAttributes()
        );
    }
}
