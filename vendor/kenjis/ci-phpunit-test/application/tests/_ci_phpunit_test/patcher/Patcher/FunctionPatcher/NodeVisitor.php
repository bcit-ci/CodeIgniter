<?php
/**
 * Part of ci-phpunit-test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

/**
 * Copyright for Original Code
 * 
 * @author     Adrian Philipp
 * @copyright  2014 Adrian Philipp
 * @license    https://github.com/adri/monkey/blob/dfbb93ae09a2c0712f43eab7ced76d3f49989fbe/LICENSE
 * @link       https://github.com/adri/monkey
 * 
 * @see        https://github.com/adri/monkey/blob/dfbb93ae09a2c0712f43eab7ced76d3f49989fbe/testTest.php
 */

namespace Kenjis\MonkeyPatch\Patcher\FunctionPatcher;

use ReflectionFunction;
use ReflectionException;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\NodeVisitorAbstract;

use Kenjis\MonkeyPatch\Patcher\FunctionPatcher;

class NodeVisitor extends NodeVisitorAbstract
{
	public function leaveNode(Node $node)
	{
		if (! ($node instanceof FuncCall))
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

		if (
			FunctionPatcher::isWhitelisted((string) $node->name)
			&& ! FunctionPatcher::isBlacklisted((string) $node->name)
		) {
			$replacement = new FullyQualified(array());
			$replacement->set(
				'\__FuncProxy__::' . (string) $node->name
			);

			$pos = $node->getAttribute('startTokenPos');
			FunctionPatcher::$replacement[$pos] = 
				'\__FuncProxy__::' . (string) $node->name;

			$node->name = $replacement;
		}
	}
}
