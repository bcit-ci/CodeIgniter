<?php

class Model_Relationship 
{
	protected $database_table_name;
	protected $database_key_left_to_center;
	protected $database_key_center_to_left;
	protected $database_key_right_to_center;
	protected $database_key_center_to_right;
	
	protected $model_left;
	protected $model_right;
	
	protected $to_many;
	
	public static function database_table_name()
	{
		return '';
	}
	
	public static function database_key_left_to_center()
	{
		return '';
	}
	
	public static function database_key_center_to_left()
	{
		return '';
	}
	
	public static function database_key_right_to_center()
	{
		return '';
	}
	
	public static function database_key_center_to_right()
	{
		return '';
	}
	
	public static function left_model()
	{
		return '';
	}
	
	public static function right_model()
	{
		return '';
	}
	
	public static function to_many()
	{
		return 0;
	}
	
	public static function create($left_object, $right_object)
	{
		$CI =& get_instance();
		
		$CI->load->model(static::model_left());	
		$CI->load->model(static::model_right());
		
		$database_key_left_to_center = static::database_key_left_to_center();
		$database_key_right_to_center = static::database_key_right_to_center();
		
		$left_key = $left_object->$database_key_left_to_center;
		$right_key = $right_object->$database_key_right_to_center;
		
		$indata = array();
		$indata['updated'] = date("Y-m-d H:i:s");
		$indata['deleted'] = '0';
		
		$CI->db->where(static::database_key_center_to_left(), $left_key);
		$CI->db->where(static::database_key_center_to_right(), $right_key);
	    if ($CI->db->count_all_results(static::database_table_name()) == 0) 
	    {
	    	$indata[static::database_key_center_to_left()] = $left_key;
			$indata[static::database_key_center_to_right()] = $right_key;	
	    	$CI->db->insert(static::database_table_name(), $indata);
	    } 
	    else 
	    {
	    	$CI->db->where(static::database_key_center_to_left(), $left_key);
			$CI->db->where(static::database_key_center_to_right(), $right_key);
	    	$CI->db->update(static::database_table_name(), $indata);
	    }
	}


	public static function delete($left_object, $right_object)
	{
		$CI =& get_instance();
		
		$CI->load->model(static::model_left());	
		$CI->load->model(static::model_right());
		
		$database_key_left_to_center = static::database_key_left_to_center();
		$database_key_right_to_center = static::database_key_right_to_center();
		
		$left_key = $left_object->$database_key_left_to_center;
		$right_key = $right_object->$database_key_right_to_center;
		
		$indata = array();
		$indata['updated'] = date("Y-m-d H:i:s");
		$indata['deleted'] = '1';
		
		$CI->db->where(static::database_key_center_to_left(), $left_key);
		$CI->db->where(static::database_key_center_to_right(), $right_key);
	    if ($CI->db->count_all_results(static::database_table_name()) > 0)
	    {
	    	$CI->db->where(static::database_key_center_to_left(), $left_key);
			$CI->db->where(static::database_key_center_to_right(), $right_key);
	    	$CI->db->update(static::database_table_name(), $indata);
	    }
	}
	
	public static function count_related($left_object, $filters = NULL, $modified_since = NULL, $exclude_deleted = TRUE){
		return static::get($left_object, $filters, $modified_since, $exclude_deleted, TRUE);
	}
	
	public static function get_related($left_object, $filters = NULL, $modified_since = NULL, $exclude_deleted = TRUE, $count_only = FALSE)
	{
		$CI =& get_instance();
			
		$CI->load->model(static::left_model());	
		$CI->load->model(static::right_model());
		
		$model_left = static::left_model();
		$model_right = static::right_model();
		
		$left_table = $model_left::GetDatabaseTableName();
		$center_table = static::GetDatabaseTableName();
		$right_table = $model_right::GetDatabaseTableName();
		
		$database_key_left_to_center = static::database_key_left_to_center();
		$left_key = $left_object->$database_key_left_to_center;
		
		foreach($model_right::GetDatabaseKeys() as $key)
		{
			$CI->db->select($right_table.'.'.$key);
		}
		$CI->db->select($right_table.'.updated');
		if(!$exclude_deleted){
			$CI->db->select($right_table.'.deleted');
		}
		$CI->db->from($left_table);
		$CI->db->join($center_table, $left_table.'.'.static::database_key_left_to_center().' = '.$center_table.'.'.static::database_key_center_to_left());
		$CI->db->join($right_table, $center_table.'.'.static::database_key_center_to_right().' = '.$right_table.'.'.static::database_key_right_to_center());
		$CI->db->where($left_table.'.'.static::database_key_left_to_center(), $left_key);
		$CI->db->where($center_table.'.deleted', '0');
		if($filters)
		{
			foreach($filters as $value){
				$this->db->where($value);
			}
		}
		if($exclude_deleted)
		{
			$CI->db->where($right_table.'.deleted', '0');
		}
		if($modified_since)
		{
			$CI->db->where($center_table.'.updated >=', $modified_since);
		}
		
		if(!$count_only)
		{
			if($this->to_many)
			{
				$q = $CI->db->get();
				$results = array();
				if ($q->num_rows() > 0)
				{
					foreach($q->result() as $object)
					{
						$x = new $model_right;
						foreach($object as $key => $value)
						{
							$x->$key = $value;
						}
						$results[] = $x;
					}
				}
				return $results;
			} 
			else 
			{
				$q = $CI->db->get();
				if ($q->num_rows() === 1)
				{
					$x = new $model_right;
					foreach($q->result() as $object)
					{		
						foreach($object as $key => $value)
						{
							$x->$key = $value;
						}
					}
					return $x;
				}
				return NULL;
			}
		} 
		else 
		{
			return $CI->db->count_all_results();
		}
	}
}