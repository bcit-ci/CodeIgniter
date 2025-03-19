<?php
/**
 * Part of ci-phpunit-test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2016 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

namespace Kenjis\MonkeyPatch\Patcher\ConstantPatcher;

use PhpParser\Node;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\NodeVisitorAbstract;

use Kenjis\MonkeyPatch\Patcher\ConstantPatcher;

class NodeVisitor extends NodeVisitorAbstract
{
	private $disable_const_rewrite_level = 0;

	public function enterNode(Node $node)
	{
		$callback = [$this, 'before' . ucfirst($node->getType())];
		if (is_callable($callback)) {
			call_user_func_array($callback, [$node]);
		}
	}

	public function leaveNode(Node $node)
	{
		if (! ($node instanceof ConstFetch))
		{
			$callback = [$this, 'rewrite' . ucfirst($node->getType())];
			if (is_callable($callback)) {
				call_user_func_array($callback, [$node]);
			}

			return;
		}

		if ($this->disable_const_rewrite_level > 0)
		{
			return;
		}

		if (! ($node->name instanceof Name))
		{
			return;
		}

		if (! $node->name->isUnqualified())
		{
			return;
		}

		if (! ConstantPatcher::isBlacklisted((string) $node->name))
		{
			$replacement = new FullyQualified(array());
			$replacement->set(
				'\__ConstProxy__::get(\'' . (string) $node->name . '\')'
			);

			$pos = $node->getAttribute('startTokenPos');
			ConstantPatcher::$replacement[$pos] = 
				'\__ConstProxy__::get(\'' . (string) $node->name .'\')';

			$node->name = $replacement;
		}
	}

	/**
	 * The following logic is from:
	 * <https://github.com/badoo/soft-mocks/blob/06fe26a2c9ab4cae17b88648439952ab0586438f/src/QA/SoftMocks.php#L1572>
	 * Thank you.
	 * 
	 * The MIT License (MIT)
	 * Copyright (c) 2016 Badoo Development
	 */
	// Cannot rewrite constants that are used as default values in function arguments
	public function beforeParam()
	{
		$this->disable_const_rewrite_level++;
	}

	public function rewriteParam()
	{
		$this->disable_const_rewrite_level--;
	}

	// Cannot rewrite constants that are used as default values in constant declarations
	public function beforeConst()
	{
		$this->disable_const_rewrite_level++;
	}

	public function rewriteConst()
	{
		$this->disable_const_rewrite_level--;
	}

	// Cannot rewrite constants that are used as default values in property declarations
	public function beforeStmt_PropertyProperty()
	{
		$this->disable_const_rewrite_level++;
	}

	public function rewriteStmt_PropertyProperty()
	{
		$this->disable_const_rewrite_level--;
	}

	// Cannot rewrite constants that are used as default values in static variable declarations
	public function beforeStmt_StaticVar()
	{
		$this->disable_const_rewrite_level++;
	}

	public function rewriteStmt_StaticVar()
	{
		$this->disable_const_rewrite_level--;
	}
}
