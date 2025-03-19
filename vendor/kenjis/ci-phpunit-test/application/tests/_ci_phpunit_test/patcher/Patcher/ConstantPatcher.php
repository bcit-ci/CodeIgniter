<?php
/**
 * Part of ci-phpunit-test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2016 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

namespace Kenjis\MonkeyPatch\Patcher;

require __DIR__ . '/ConstantPatcher/NodeVisitor.php';
require __DIR__ . '/ConstantPatcher/Proxy.php';

use LogicException;

use Kenjis\MonkeyPatch\Patcher\ConstantPatcher\NodeVisitor;

class ConstantPatcher extends AbstractPatcher
{
	/**
	 * @var special constant names which we don't patch
	 */
	private static $blacklist = [
		'true',
		'false',
		'null',
	];

	public static $replacement;

	public function __construct()
	{
		$this->node_visitor = new NodeVisitor();
	}

	/**
	 * @param string $name constant name
	 * @return boolean
	 */
	public static function isBlacklisted($name)
	{
		if (in_array(strtolower($name), self::$blacklist))
		{
			return true;
		}

		return false;
	}

	protected static function generateNewSource($source)
	{
		$tokens = token_get_all($source);
		$new_source = '';
		$i = -1;

		ksort(self::$replacement);
		reset(self::$replacement);
		$replacement = each(self::$replacement);

		foreach ($tokens as $token)
		{
			$i++;

			if (is_string($token))
			{
				$new_source .= $token;
			}
			elseif ($i == $replacement['key'])
			{
				$new_source .= $replacement['value'];
				$replacement = each(self::$replacement);
			}
			else
			{
				$new_source .= $token[1];
			}
		}

		if ($replacement !== false)
		{
			throw new LogicException('Replacement data still remain');
		}

		return $new_source;
	}
}
