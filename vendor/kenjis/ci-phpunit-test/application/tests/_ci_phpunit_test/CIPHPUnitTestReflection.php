<?php
/**
 * Part of ci-phpunit-test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

class CIPHPUnitTestReflection
{
	/**
	 * @param object|string $obj    object or class name
	 * @param string        $method method name
	 * @return closure
	 */
	public static function getPrivateMethodInvoker($obj, $method)
	{
		$ref_method = new ReflectionMethod($obj, $method);
		$ref_method->setAccessible(true);
		$obj = (gettype($obj) === 'object') ? $obj : null;

		return function () use ($obj, $ref_method) {
			$args = func_get_args();
			return $ref_method->invokeArgs($obj, $args);
		};
	}

	protected static function getAccessibleRefProperty($obj, $property)
	{
		if (is_object($obj)) {
			$ref_class = new ReflectionObject($obj);
		} else {
			$ref_class = new ReflectionClass($obj);
		}

		$ref_property = $ref_class->getProperty($property);
		$ref_property->setAccessible(true);

		return $ref_property;
	}

	/**
	 * @param object|string $obj      object or class name
	 * @param string        $property property name
	 * @param mixed         $value    value
	 */
	public static function setPrivateProperty($obj, $property, $value)
	{
		$ref_property = self::getAccessibleRefProperty($obj, $property);
		$ref_property->setValue($obj, $value);
	}

	/**
	 * @param object|string $obj      object or class name
	 * @param string        $property property name
	 * @return mixed value
	 */
	public static function getPrivateProperty($obj, $property)
	{
		$ref_property = self::getAccessibleRefProperty($obj, $property);
		return $ref_property->getValue($obj);
	}
}
