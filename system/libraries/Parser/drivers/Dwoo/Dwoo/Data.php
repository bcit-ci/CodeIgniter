<?php

/**
 * dwoo data object, use it for complex data assignments or if you want to easily pass it
 * around multiple functions to avoid passing an array by reference
 *
 * This software is provided 'as-is', without any express or implied warranty.
 * In no event will the authors be held liable for any damages arising from the use of this software.
 *
 * @author     Jordi Boggiano <j.boggiano@seld.be>
 * @copyright  Copyright (c) 2008, Jordi Boggiano
 * @license    http://dwoo.org/LICENSE   Modified BSD License
 * @link       http://dwoo.org/
 * @version    1.0.0
 * @date       2008-10-23
 * @package    Dwoo
 */
class Dwoo_Data implements Dwoo_IDataProvider
{
	/**
	 * data array
	 *
	 * @var array
	 */
	protected $data = array();

	/**
	 * returns the data array
	 *
	 * @return array
	 */
	public function getData()
	{
		return $this->data;
	}

	/**
	 * clears a the entire data or only the given key
	 *
	 * @param array|string $name clears only one value if you give a name, multiple values if
	 * 							  you give an array of names, or the entire data if left null
	 */
	public function clear($name = null)
	{
		if ($name === null) {
			$this->data = array();
		} elseif (is_array($name)) {
			foreach ($name as $index)
				unset($this->data[$index]);
		} else {
			unset($this->data[$name]);
		}
	}

	/**
	 * overwrites the entire data with the given array
	 *
	 * @param array $data the new data array to use
	 */
	public function setData(array $data)
	{
		$this->data = $data;
	}

	/**
	 * merges the given array(s) with the current data with array_merge
	 *
	 * @param array $data the array to merge
	 * @param array $data2 $data3 ... other arrays to merge, optional, etc.
	 */
	public function mergeData(array $data)
	{
		$args = func_get_args();
		while (list(,$v) = each($args)) {
			if (is_array($v)) {
				$this->data = array_merge($this->data, $v);
			}
		}
	}

	/**
	 * assigns a value or an array of values to the data object
	 *
	 * @param array|string $name an associative array of multiple (index=>value) or a string
	 * 					   that is the index to use, i.e. a value assigned to "foo" will be
	 * 					   accessible in the template through {$foo}
	 * @param mixed $val the value to assign, or null if $name was an array
	 */
	public function assign($name, $val = null)
	{
		if (is_array($name)) {
			reset($name);
			while (list($k,$v) = each($name))
				$this->data[$k] = $v;
		} else {
			$this->data[$name] = $val;
		}
	}
   	
   	/**
   	 * allows to assign variables using the object syntax
   	 * 
   	 * @param string $name the variable name
   	 * @param string $value the value to assign to it
   	 */
   	public function __set($name, $value)
   	{
   		$this->assign($name, $value);
   	}

	/**
	 * assigns a value by reference to the data object
	 *
	 * @param string $name the index to use, i.e. a value assigned to "foo" will be
	 * 					   accessible in the template through {$foo}
	 * @param mixed $val the value to assign by reference
	 */
	public function assignByRef($name, &$val)
	{
		$this->data[$name] =& $val;
	}

	/**
	 * appends values or an array of values to the data object
	 *
	 * @param array|string $name an associative array of multiple (index=>value) or a string
	 * 					   that is the index to use, i.e. a value assigned to "foo" will be
	 * 					   accessible in the template through {$foo}
	 * @param mixed $val the value to assign, or null if $name was an array
	 * @param bool $merge true to merge data or false to append, defaults to false
	 */
   	public function append($name, $val = null, $merge = false)
   	{
   		if (is_array($name)) {
			foreach ($name as $key=>$val) {
				if (isset($this->data[$key]) && !is_array($this->data[$key])) {
					settype($this->data[$key], 'array');
				}

				if ($merge === true && is_array($val)) {
					$this->data[$key] = $val + $this->data[$key];
				} else {
					$this->data[$key][] = $val;
				}
			}
   		} elseif ($val !== null) {
			if (isset($this->data[$name]) && !is_array($this->data[$name])) {
				settype($this->data[$name], 'array');
			} elseif (!isset($this->data[$name])) {
				$this->data[$name] = array();
			}

			if ($merge === true && is_array($val)) {
				$this->data[$name] = $val + $this->data[$name];
			} else {
				$this->data[$name][] = $val;
			}
   		}
   	}

	/**
	 * appends a value by reference to the data object
	 *
	 * @param string $name the index to use, i.e. a value assigned to "foo" will be
	 * 					   accessible in the template through {$foo}
	 * @param mixed $val the value to append by reference
	 * @param bool $merge true to merge data or false to append, defaults to false
	 */
   	public function appendByRef($name, &$val, $merge = false)
   	{
   		if (isset($this->data[$name]) && !is_array($this->data[$name])) {
			settype($this->data[$name], 'array');
		}

   		if ($merge === true && is_array($val)) {
   			foreach ($val as $key => &$val) {
   				$this->data[$name][$key] =& $val;
   			}
   		} else {
   			$this->data[$name][] =& $val;
   		}
   	}
   	
   	/**
   	 * returns true if the variable has been assigned already, false otherwise
   	 * 
   	 * @param string $name the variable name
   	 * @return bool 
   	 */
   	public function isAssigned($name)
   	{
   		return isset($this->data[$name]);
   	}
   	
   	/**
   	 * supports calls to isset($dwooData->var)
   	 * 
   	 * @param string $name the variable name
   	 */
   	public function __isset($name)
   	{
   		return isset($this->data[$name]);
   	}
   	
   	/**
   	 * unassigns/removes a variable
   	 * 
   	 * @param string $name the variable name
   	 */
   	public function unassign($name)
   	{
   		unset($this->data[$name]);
   	}
   	
   	/**
   	 * supports unsetting variables using the object syntax
   	 * 
   	 * @param string $name the variable name
   	 */
   	public function __unset($name)
   	{
   		unset($this->data[$name]);
   	}
   	
   	/**
   	 * returns a variable if it was assigned
   	 * 
   	 * @param string $name the variable name
   	 * @return mixed
   	 */
   	public function get($name)
   	{
   		return $this->__get($name);
   	}

   	/**
   	 * allows to read variables using the object syntax
   	 * 
   	 * @param string $name the variable name
   	 * @return mixed
   	 */
   	public function __get($name)
   	{
   		if (isset($this->data[$name])) {
   			return $this->data[$name];
   		} else {
   			throw new Dwoo_Exception('Tried to read a value that was not assigned yet : "'.$name.'"');
   		}
   	}
}
