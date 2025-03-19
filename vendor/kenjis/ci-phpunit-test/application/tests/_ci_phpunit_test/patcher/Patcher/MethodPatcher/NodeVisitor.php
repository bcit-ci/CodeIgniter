<?php
/**
 * Part of ci-phpunit-test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

namespace Kenjis\MonkeyPatch\Patcher\MethodPatcher;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeVisitorAbstract;

use Kenjis\MonkeyPatch\Patcher\MethodPatcher;

class NodeVisitor extends NodeVisitorAbstract
{
	public function leaveNode(Node $node)
	{
		if (! ($node instanceof ClassMethod))
		{
			return;
		}

		$pos = $node->getAttribute('startTokenPos');
		MethodPatcher::$replacement[$pos] = true;
	}
}
