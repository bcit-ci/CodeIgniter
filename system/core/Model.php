<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2017, British Columbia Institute of Technology
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package	CodeIgniter
 * @author	EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (https://ellislab.com/)
 * @copyright	Copyright (c) 2014 - 2017, British Columbia Institute of Technology (http://bcit.ca/)
 * @license	http://opensource.org/licenses/MIT	MIT License
 * @link	https://codeigniter.com
 * @since	Version 1.0.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/user_guide/libraries/config.html
 */
 class CI_Model {
 /*
 *var $_table stores the database table name that
 * be want to use in the Model
 *Important table must have id column as primary key.
 */
 	private $_table;

 	/**
 	 * Class constructor
 	 *
 	 * @return	void
 	 */
 	public function __construct($table=NULL)
 	{
 		/*
 		*checking if table name is given convert it to string
 		* and store it in private variable
 		*/
 		if($this->_table !=NULL)
 		{
 			$this->_table = (string)$table;
 			$this->load->database();
 			log_message('info', 'Model Class Initialized with table mapping.');
 		}
 		else
 		{
 			log_message('info', 'Model Class Initialized without table mapping.');
 		}
 	}

 	// --------------------------------------------------------------------

 	/**
 	 * __get magic
 	 *
 	 * Allows models to access CI's loaded classes using the same
 	 * syntax as controllers.
 	 *
 	 * @param	string	$key
 	 */
 	public function __get($key)
 	{
 		// Debugging note:
 		//	If you're here because you're getting an error message
 		//	saying 'Undefined Property: system/core/Model.php', it's
 		//	most likely a typo in your model code.
 		return get_instance()->$key;
 	}

 	/*method all()
 	*Returns all the rows in table
 	* return type mixed
 	*/

 		public function all()
 		{
 			return $this->db->get($this->_table)->result();
 		}

 		/*Method find($id)
 		*Returns only one row or first row in case if condition is true
 		*for more than one row
 		*
 		*return type mixed
 		*/

 		public function find($id)
 		{
 			return $this->db->get_where($this->_table,['id'=>$id])->row();
 		}

 		/*Method find_where($column_name,$value)
 		*takes two  parameter first column name and second value
 		*Returns all the rows matches the condition
 		*for more than one row
 		*
 		*return type mixed
 		*/
 		public function find_where($column_name,$value)
 		{
 			return $this->db->get_where($this->_table,[$column_name=>$value])->result();
 		}

 		/*Method insert($data)
 		*takes associate array as parameter
 		*e.g.
 		* $data = array(
 		*'column_name' => 'value'
 		*)
 		*Returns true if row inserted successfully or false if not.
 		*for more than one row
 		*
 		*return type bool
 		*/
 		public function insert($data)
 		{
 			return $this->db->insert($this->_table,$data);
 		}

 		/*
 		*method delete($id)
 		*takes one parameter the id of table
 		*return type mixed
 		*/
 		public function delete($id)
 		{
 			$data = $this->db->get_where($this->_table,['id'=>$id])->row();
 			if($this->db->delete($this->_table,['id'=>$id]))
 			{
 				return $data;
 			}
 			else
 			{
 				return FALSE;
 			}
 		}

 		/*
 		*method delete_where($column_name,$value)
 		*takes two  parameter first column name and second value
 		*return type mixed
 		*/

 		public function delete_where($column_name,$value)
 		{
 			$data = $this->db->get_where($this->_table,[$column_name=>$value])->result();
 			if($this->db->delete($this->_table,[$column_name=>$value]))
 			{
 				return $data;
 			}
 			else
 			{
 				return FALSE;
 			}
 		}

 }
