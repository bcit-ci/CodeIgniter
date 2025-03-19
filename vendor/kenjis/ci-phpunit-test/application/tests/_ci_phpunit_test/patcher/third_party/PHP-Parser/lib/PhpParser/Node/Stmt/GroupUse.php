<?php

namespace PhpParser\Node\Stmt;

use PhpParser\Node\Stmt;
use PhpParser\Node\Name;

class GroupUse extends Stmt
{
    /** @var int Type of group use */
    public $type;
    /** @var Name Prefix for uses */
    public $prefix;
    /** @var UseUse[] Uses */
    public $uses;

    /**
     * Constructs a group use node.
     *
     * @param Name     $prefix     Prefix for uses
     * @param UseUse[] $uses       Uses
     * @param int      $type       Type of group use
     * @param array    $attributes Additional attributes
     */
    public function __construct(Name $prefix, array $uses, $type = Use_::TYPE_NORMAL, array $attributes = array()) {
        parent::__construct($attributes);
        $this->type = $type;
        $this->prefix = $prefix;
        $this->uses = $uses;
    }

    public function getSubNodeNames() {
        return array('type', 'prefix', 'uses');
    }
}
