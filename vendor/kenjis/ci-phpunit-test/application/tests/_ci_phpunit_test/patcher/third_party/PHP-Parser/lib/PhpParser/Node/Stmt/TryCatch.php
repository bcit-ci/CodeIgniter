<?php

namespace PhpParser\Node\Stmt;

use PhpParser\Node;
use PhpParser\Error;

class TryCatch extends Node\Stmt
{
    /** @var Node[] Statements */
    public $stmts;
    /** @var Catch_[] Catches */
    public $catches;
    /** @var null|Node[] Finally statements */
    public $finallyStmts;

    /**
     * Constructs a try catch node.
     *
     * @param Node[]      $stmts        Statements
     * @param Catch_[]    $catches      Catches
     * @param null|Node[] $finallyStmts Finally statements (null means no finally clause)
     * @param array|null  $attributes   Additional attributes
     */
    public function __construct(array $stmts, array $catches, array $finallyStmts = null, array $attributes = array()) {
        if (empty($catches) && null === $finallyStmts) {
            throw new Error('Cannot use try without catch or finally');
        }

        parent::__construct($attributes);
        $this->stmts = $stmts;
        $this->catches = $catches;
        $this->finallyStmts = $finallyStmts;
    }

    public function getSubNodeNames() {
        return array('stmts', 'catches', 'finallyStmts');
    }
}
