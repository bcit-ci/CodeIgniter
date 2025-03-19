<?php

namespace PhpParser\Node\Stmt;

use PhpParser\Node;
use PhpParser\Error;

class Interface_ extends ClassLike
{
    /** @var Node\Name[] Extended interfaces */
    public $extends;

    protected static $specialNames = array(
        'self'   => true,
        'parent' => true,
        'static' => true,
    );

    /**
     * Constructs a class node.
     *
     * @param string $name       Name
     * @param array  $subNodes   Array of the following optional subnodes:
     *                           'extends' => array(): Name of extended interfaces
     *                           'stmts'   => array(): Statements
     * @param array  $attributes Additional attributes
     */
    public function __construct($name, array $subNodes = array(), array $attributes = array()) {
        parent::__construct($attributes);
        $this->name = $name;
        $this->extends = isset($subNodes['extends']) ? $subNodes['extends'] : array();
        $this->stmts = isset($subNodes['stmts']) ? $subNodes['stmts'] : array();

        if (isset(self::$specialNames[strtolower($this->name)])) {
            throw new Error(sprintf('Cannot use \'%s\' as class name as it is reserved', $this->name));
        }

        foreach ($this->extends as $interface) {
            if (isset(self::$specialNames[strtolower($interface)])) {
                throw new Error(
                    sprintf('Cannot use \'%s\' as interface name as it is reserved', $interface),
                    $interface->getAttributes()
                );
            }
        }
    }

    public function getSubNodeNames() {
        return array('name', 'extends', 'stmts');
    }
}
