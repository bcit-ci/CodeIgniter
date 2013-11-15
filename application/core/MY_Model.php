<?php

class MY_Model extends CI_Model
{
	protected $database_table_name;
	protected $database_keys;
	
	protected $relationships;
	
	protected $hidden_properties;
	
	public $updated;
	public $deleted;
	
	public function __construct(){
		parent::__construct();
		
		$this->database_table_name = static::database_table_name();
		$this->database_keys = static::database_keys();
		
		$this->relationships = static::relationships();
		
		$this->hidden_properties = static::hidden_properties();
		
		$this->deleted = 0;
	}
	
	public static function database_table_name()
	{
		return '';
	}
	
	public static function database_keys()
	{
		return array();
	}
	
	public static function relationships()
	{
		return array();
	}
	
	public static function hidden_properties()
	{
		return array();
	}
	
	public static function create()
	{
		return new static;
	}
	
	public static function get($filters = NULL, $modified_since = NULL, $exclude_deleted = TRUE)
	{
		$CI =& get_instance();
		if($filters)
		{
			foreach(static::database_keys() as $k)
			{
				$CI->db->select($k);
			}
			$CI->db->select('updated');
			if(!$exclude_deleted)
			{
				$CI->db->select('deleted');
			}
			$CI->db->from($CI->database_table_name);
			foreach($filters as $value)
			{
				$CI->db->where($value);
			}
			if($exclude_deleted)
			{
				$CI->db->where(static::database_table_name().'.deleted', '0');
			}
			if($modified_since)
			{
				$CI->db->where(static::database_table_name().'.updated >=', $modified_since);
			}
			$q = $CI->db->get();
			if ($q->num_rows() === 1)
			{
				$x = new static;
				foreach($q->result() as $object)
				{	
					foreach($object as $k => $v)
					{
						$x->$k = $v;
					}
				}
				return $x;
			}
		}
		return NULL;
	}
	
	public static function get_many($filters = NULL, $modified_since = NULL, $limit = 0, $offset = 0, $exclude_deleted = TRUE){
		$CI =& get_instance();
		$objects = array();
		foreach(static::database_keys() as $k)
		{
			$CI->db->select($k);
		}
		$CI->db->select('updated');
		if(!$exclude_deleted)
		{
			$CI->db->select('deleted');
		}
		$CI->db->from(static::database_table_name());
		if($filters)
		{
			foreach($filters as $value)
			{
				$CI->db->where($value);
			}
		}
		if($exclude_deleted)
		{
			$CI->db->where(static::database_table_name().'.deleted', '0');
		}
		if($modified_since)
		{
			$CI->db->where(static::database_table_name().'.updated >=', $modified_since);
		}
		if($limit)
		{
			if($offset)
			{
				$CI->db->limit($limit, $offset);
			} 
			else 
			{
				$CI->db->limit($limit);
			}
		}
		$q = $CI->db->get();
		if ($q->num_rows() > 0)
		{
			foreach($q->result() as $object)
			{
				$x = new static;
				foreach($object as $k => $v)
				{
					$x->$k = $v;
				}
				$objects[] = $x;
			}
		}
		return $objects;
	}
	
	public function populate($indata)
	{
		if(is_array($indata))
		{
			foreach ($this->database_keys as $key) 
			{
				if(isset($indata[$key]))
				{
					$this->$key = $indata[$key];
				}
			}
		} 
		else if(is_object($indata))
		{
			foreach ($this->database_keys as $key) 
			{
				if(isset($indata->$key))
				{
					$this->$key = $indata->$key;
				}
			}
		}
	}
	
	public function save()
	{
		$this->updated = date("Y-m-d H:i:s");
		if($this->id)
		{
			$this->db->where('id', $this->id);
			$this->db->update($this->database_table_name, $this);
		} 
		else 
		{
			$this->db->insert($this->database_table_name, $this);
			$this->id = $this->db->insert_id();
		}
	}
	
	public function delete()
	{
		$this->deleted = 1;
		$this->save();
	}
	
	public function create_relationship($relationship_key, $object)
	{
		if(isset($this->relationships[$relationship_key])){
			$relationship_model = static::_relationship_class($this, $this->relationships[$relationship_key]);
			$this->load->model($relationship_model);
			$relationship_model::create($this, $object);
		}
	}
	
	public function remove_relationship($relationship_key, $object)
	{
		if(isset($this->relationships[$relationship_key])){
			$relationship_model = static::_relationship_class($this, $this->relationships[$relationship_key]);
			$this->load->model($relationship_model);
			$relationship_model::delete($this, $object);
		}
	}
	
	public function get_related($key)
	{
		$relationship_key = $key;
		$is_count = FALSE;
		if (strpos($str, "count_") === 0)
		{
			$relationship_key = substr($str, strlen('count_'));
			$is_count = TRUE;
		}
		
		if(in_array($relationship_key, $this->relationships))
		{
			$relationship_model = static::_relationship_class($this, $this->relationships[$relationship_key]);
			$this->load->model($relationship_model);
			if(!$is_count)
			{
				$this->$key = $relationship_model::get_related($this, $filters, $modified_since, $exclude_deleted);
			}
			else
			{
				$this->$key = $relationship_model::count_related($this, $filters, $modified_since, $exclude_deleted);
			}
			return $this->$key;
		}
	}
	
	public function hide_properties()
	{
		foreach ($this->hidden_properties as $property)
		{
			if(isset($this->$property))
			{
				unset($this->$property);
			}
		}
	}
	
	private static function _relationship_class($left, $right)
	{
		return 'relationships/' . tolower(get_class($left)) . tolower($right);
	}
}