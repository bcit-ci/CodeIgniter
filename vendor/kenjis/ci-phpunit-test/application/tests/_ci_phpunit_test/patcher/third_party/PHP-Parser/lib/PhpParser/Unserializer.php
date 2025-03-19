<?php

namespace PhpParser;

interface Unserializer
{
    /**
     * Unserializes a string in some format into a node tree.
     *
     * @param string $string Serialized string
     *
     * @return mixed Node tree
     */
    public function unserialize($string);
}
