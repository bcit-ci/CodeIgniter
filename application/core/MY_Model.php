<?php

/**
 * Exploding Phone Extension to CI_Model Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		Exploding Phone
 * @link		https://github.com/explodingphone/CodeIgniter
 */

class MY_Model extends CI_Model
{
	/**
	 * The name of the table in the database which should be used to
	 * store the objects of this class.
	 *
	 * @var string
	 * @access protected
	 */
	protected $database_table_name;
	
	/**
	 * An array of strings which describes the column titles of the
	 * table in the database.
	 *
	 * @var array
	 * @access protected
	 */
	protected $database_keys;
	
	/**
	 * An associative array of classes which form a relationship with this
	 * class, keyed by the property name you wish to use. 
	 * 
	 * e.g. If this class represents an Author, array('comments' => 'Comment')
	 * would make a to-many relationship with the Comment class and would be 
	 * accessed by $author->comments.
	 *
	 * @var array
	 * @access protected
	 */
	protected $relationships;
	
	/**
	 * An array of properties which should be removed when the appropriate method
	 * is called. Should be used to remove sensitve data before it leaves the
	 * confines of the secure server.
	 *
	 * @var array
	 * @access protected
	 */
	protected $hidden_properties;
	
	/**
	 * This property contains the time at which the database record was last updated.
	 * 
	 * It is standard for every subclass and every table should contain a DateTime
	 * column named 'updated'. The property is updated everytime the property is saved.
	 *
	 * @var datetime
	 * @access public
	 */
	public $updated;
	
	/**
	 * This property contains a boolean to indicate if the object has been deleted.
	 * 
	 * It is standard for every subclass and every table should contain a boolean
	 * column named 'deleted'. If the flag is set to TRUE it will be excluded from
	 * all Object methods unless the method is passed the appropriate parameter to 
	 * include them.
	 *
	 * @var boolean
	 * @access public
	 */
	public $deleted;
	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	public function __construct(){
		parent::__construct();
		
		$this->database_table_name = static::database_table_name();
		$this->database_keys = static::database_keys();
		
		$this->relationships = static::relationships();
		
		$this->hidden_properties = static::hidden_properties();
		
		$this->deleted = 0;
	}
	
	/**
	 * database_table_name()
	 *
	 * A utility function to populate the $database_table_name property.
	 * 
	 * Subclasses should override this method and return the name of the
	 * specific table which contains the data for the objects described by
	 * this class.
	 *
	 * @return 	string	The name of the table in the database.
	 */
	public static function database_table_name()
	{
		return '';
	}
	
	/**
	 * database_keys()
	 *
	 * A utility function to populate the $database_keys property.
	 * 
	 * Subclasses should override this method and return an array containing the
	 * column headings for the table returned in 'database_table_name()'.
	 *
	 * @return 	array	A list of column headings.
	 */
	public static function database_keys()
	{
		return array();
	}
	
	/**
	 * relationships()
	 *
	 * A utility function to populate the $relationships property.
	 * 
	 * Subclasses should override this method and return an associative array 
	 * of classes which form a relationship with this class, keyed by the property 
	 * name you wish to use. 
	 *
	 * @return 	array	An associative array of relationships for these objects.
	 */
	public static function relationships()
	{
		return array();
	}
	
	/**
	 * hidden_properties()
	 *
	 * A utility function to populate the $hidden_properties property.
	 * 
	 * Subclasses should override this method and return an array of the properties
	 * which should be hidden when 'hide_properties()' is called.
	 *
	 * @return 	array	An array of sensitive properties.
	 */
	public static function hidden_properties()
	{
		return array();
	}
	
	/**
	 * create()
	 * 
	 * Creates a new instance of the calling class.
	 *
	 * @return 	Object	An empty object with the template of the calling class.
	 */
	public static function create()
	{
		return new static;
	}
	
	/**
	 * get()
	 * 
	 * Searches for an object matching the criteria provided. It will return the object
	 * if the criteria are enough to pinpoint an object uniquely, otherwise will
	 * return NULL.
	 * 
	 * The $filters parameter should be an array of SQL snippets which the returned object
	 * should match. It can be simple and just match a given primary key: 
	 * 
	 * e.g. array("id = '858'").
	 * 
	 * Or it can be very complex using lots of SQL functions such as find a unique object within
	 * 25km of a given location: 
	 * 
	 * e.g.
	 *		$distance_sql = '(((acos(sin(('.$latitude.'*pi()/180))*sin((latitude*pi()/180))
	 * 							+cos(('.$latitude.'*pi()/180))*cos((latitude*pi()/180))
	 * 							*cos((('.$longitude.'-longitude)*pi()/180))))*180/pi())*60*1.1515*1.609344*1000)';
	 *		array("(name LIKE '%".$this->get('search')."%' OR ".$distance_sql." <= 25000))";  
	 *
	 * 
	 * 
	 * @param	$filters			An array of criteria which the returned object should match.
	 * @param	$modified_since		A date which, if provided, will return objects updated since that time.
	 * @param	$exclude_deleted	A flag to indicated that deleted objects should be included.
	 * @return 	Object				An object matching the criteria provided.
	 */
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
			$CI->db->from(static::database_table_name());
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
	
	/**
	 * get_many()
	 * 
	 * Searches for objects matching the criteria provided. It will return an array of objects
	 * of type of the calling class which match the given criteria or an empty array if no
	 * objects can be found.
	 * 
	 * The $filters parameter should be an array of SQL snippets which the returned objects
	 * should match. It can be simple and just match a given primary key: 
	 * 
	 * e.g. array("id = '858'").
	 * 
	 * Or it can be very complex using lots of SQL functions such as find all objects within
	 * 25km of a given location: 
	 * 
	 * e.g.
	 *		$distance_sql = '(((acos(sin(('.$latitude.'*pi()/180))*sin((latitude*pi()/180))
	 * 							+cos(('.$latitude.'*pi()/180))*cos((latitude*pi()/180))
	 * 							*cos((('.$longitude.'-longitude)*pi()/180))))*180/pi())*60*1.1515*1.609344*1000)';
	 *		array("(name LIKE '%".$this->get('search')."%' OR ".$distance_sql." <= 25000))";  
	 *
	 * 
	 * 
	 * @param	$filters			An array of criteria which the returned object should match.
	 * @param	$modified_since		A date which, if provided, will return objects updated since that time.
	 * @param	$exclude_deleted	A flag to indicated that deleted objects should be included.
	 * @return 	array				An array of objects matching the criteria provided.
	 */
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
	
	/**
	 * populate()
	 * 
	 * Sets the properties of the calling instance to match those of the $indata variable.
	 * $indata can be an array or an object.
	 * 
	 * 
	 * @param	$indata		An object or an array containing the details of the object.
	 */
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
	
	/**
	 * save()
	 * 
	 * Saves the data of the calling instance to the database, creating a new record if one does not
	 * already exist.
	 * 
	 */
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
	
	/**
	 * delete()
	 * 
	 * Deletes the object from the database.
	 * 
	 */
	public function delete()
	{
		$this->deleted = 1;
		$this->save();
	}
	
	public function create_relationship($relationship_key, $object)
	{
		if(isset($this->relationships[$relationship_key])){
			$relationship_class = static::_relationship_class($this, $this->relationships[$relationship_key]);
			$relationship_path = static::_relationship_path($this, $this->relationships[$relationship_key]);
			$this->load->model($relationship_path);
			$relationship_class::create($this, $object);
		}
	}
	
	public function remove_relationship($relationship_key, $object)
	{
		if(isset($this->relationships[$relationship_key])){
			$relationship_class = static::_relationship_class($this, $this->relationships[$relationship_key]);
			$relationship_path = static::_relationship_path($this, $this->relationships[$relationship_key]);
			$this->load->model($relationship_path);
			$relationship_class::delete($this, $object);
		}
	}
	
	public function get_related($key, $filters = NULL, $modified_since = NULL, $exclude_deleted = TRUE)
	{
		$relationship_key = $key;
		$is_count = FALSE;
		if (strpos($key, "count_") === 0)
		{
			$relationship_key = substr($key, strlen('count_'));
			$is_count = TRUE;
		}
		if(isset($relationship_key, $this->relationships))
		{
			$relationship_class = static::_relationship_class($this, $this->relationships[$relationship_key]);
			$relationship_path = static::_relationship_path($this, $this->relationships[$relationship_key]);
			$this->load->model($relationship_path);
			if(!$is_count)
			{
				$this->$key = $relationship_class::get_related($this, $filters, $modified_since, $exclude_deleted);
			}
			else
			{
				$this->$key = $relationship_class::count_related($this, $filters, $modified_since, $exclude_deleted);
			}
			return $this->$key;
		}
	}
	
	/**
	 * hide_properties()
	 * 
	 * Removes all of the properties contained in the calling instance's $hidden_properties
	 * property.
	 * 
	 */
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
		return strtolower(get_class($left)) . strtolower($right);
	}
	
	private static function _relationship_path($left, $right)
	{
		return 'relationships/' . static::_relationship_class($left, $right);
	}
}