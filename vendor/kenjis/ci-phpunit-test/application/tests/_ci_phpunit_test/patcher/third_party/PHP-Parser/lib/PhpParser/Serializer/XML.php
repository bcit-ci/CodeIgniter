<?php

namespace PhpParser\Serializer;

use XMLWriter;
use PhpParser\Node;
use PhpParser\Comment;
use PhpParser\Serializer;

class XML implements Serializer
{
    protected $writer;

    /**
     * Constructs a XML serializer.
     */
    public function __construct() {
        $this->writer = new XMLWriter;
        $this->writer->openMemory();
        $this->writer->setIndent(true);
    }

    public function serialize(array $nodes) {
        $this->writer->flush();
        $this->writer->startDocument('1.0', 'UTF-8');

        $this->writer->startElement('AST');
        $this->writer->writeAttribute('xmlns:node',      'http://nikic.github.com/PHPParser/XML/node');
        $this->writer->writeAttribute('xmlns:subNode',   'http://nikic.github.com/PHPParser/XML/subNode');
        $this->writer->writeAttribute('xmlns:attribute', 'http://nikic.github.com/PHPParser/XML/attribute');
        $this->writer->writeAttribute('xmlns:scalar',    'http://nikic.github.com/PHPParser/XML/scalar');

        $this->_serialize($nodes);

        $this->writer->endElement();

        return $this->writer->outputMemory();
    }

    protected function _serialize($node) {
        if ($node instanceof Node) {
            $this->writer->startElement('node:' . $node->getType());

            foreach ($node->getAttributes() as $name => $value) {
                $this->writer->startElement('attribute:' . $name);
                $this->_serialize($value);
                $this->writer->endElement();
            }

            foreach ($node as $name => $subNode) {
                $this->writer->startElement('subNode:' . $name);
                $this->_serialize($subNode);
                $this->writer->endElement();
            }

            $this->writer->endElement();
        } elseif ($node instanceof Comment) {
            $this->writer->startElement('comment');
            $this->writer->writeAttribute('isDocComment', $node instanceof Comment\Doc ? 'true' : 'false');
            $this->writer->writeAttribute('line', (string) $node->getLine());
            $this->writer->text($node->getText());
            $this->writer->endElement();
        } elseif (is_array($node)) {
            $this->writer->startElement('scalar:array');
            foreach ($node as $subNode) {
                $this->_serialize($subNode);
            }
            $this->writer->endElement();
        } elseif (is_string($node)) {
            $this->writer->writeElement('scalar:string', $node);
        } elseif (is_int($node)) {
            $this->writer->writeElement('scalar:int', (string) $node);
        } elseif (is_float($node)) {
            // TODO Higher precision conversion?
            $this->writer->writeElement('scalar:float', (string) $node);
        } elseif (true === $node) {
            $this->writer->writeElement('scalar:true');
        } elseif (false === $node) {
            $this->writer->writeElement('scalar:false');
        } elseif (null === $node) {
            $this->writer->writeElement('scalar:null');
        } else {
            throw new \InvalidArgumentException('Unexpected node type');
        }
    }
}
