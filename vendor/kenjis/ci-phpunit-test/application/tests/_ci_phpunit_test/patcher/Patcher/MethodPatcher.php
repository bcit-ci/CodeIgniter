<?php
/**
 * Part of ci-phpunit-test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

namespace Kenjis\MonkeyPatch\Patcher;

require __DIR__ . '/MethodPatcher/NodeVisitor.php';
require __DIR__ . '/MethodPatcher/PatchManager.php';

use LogicException;

use Kenjis\MonkeyPatch\Patcher\MethodPatcher\NodeVisitor;

class MethodPatcher extends AbstractPatcher
{
	const CODE = <<<'EOL'
if (($__ret__ = \__PatchManager__::getReturn(__CLASS__, __FUNCTION__, func_get_args())) !== __GO_TO_ORIG__) return $__ret__;
EOL;

	public static $replacement;

	public function __construct()
	{
		$this->node_visitor = new NodeVisitor();
	}

	protected static function generateNewSource($source)
	{
		$tokens = token_get_all($source);
		$new_source = '';
		$i = -1;

		ksort(self::$replacement);
		reset(self::$replacement);
		$replacement = each(self::$replacement);

		$start_method = false;

		foreach ($tokens as $token)
		{
			$i++;

			if ($i == $replacement['key'])
			{
				$start_method = true;
			}

			if (is_string($token))
			{
				if ($start_method && $token === '{')
				{
					$new_source .= '{ ' . self::CODE;
					$start_method = false;
					$replacement = each(self::$replacement);
				}
				else
				{
					$new_source .= $token;
				}
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
