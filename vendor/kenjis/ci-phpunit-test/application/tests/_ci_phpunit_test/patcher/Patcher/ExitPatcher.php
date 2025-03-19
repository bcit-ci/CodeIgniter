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

require __DIR__ . '/../Exception/ExitException.php';

class ExitPatcher
{
	public static function patch($source)
	{
		$tokens = token_get_all($source);

		$patched = false;
		$new_source = '';
		$i = -1;

		foreach ($tokens as $token) {
			$i++;
			if (is_string($token))
			{
				$new_source .= $token;
			}
			elseif ($token[0] === T_EXIT)
			{
				if ($tokens[$i+1] === ';')
				{
					$new_source .= 'exit__()';
				}
				else
				{
					$new_source .= 'exit__';
				}
				$patched = true;
			}
			else
			{
				$new_source .= $token[1];
			}
		}

		return [
			$new_source,
			$patched,
		];
	}
}
