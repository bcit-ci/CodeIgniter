<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Scaffolding Class
 *
 * Provides the Scaffolding framework
 *
 * @package		CodeIgniter
 * @subpackage	Scaffolding
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/general/scaffolding.html
 */
class Scaffolding {

	var $CI;
	var $current_table;
	var $base_url = '';
	var $lang = array();

	function Scaffolding($db_table)
	{
		$this->CI =& get_instance();
		
		$this->CI->load->database("", FALSE, TRUE);			
		$this->CI->load->library('pagination');
		
		// Turn off caching
		$this->CI->db->cache_off();
				
		/**
		 * Set the current table name
		 * This is done when initializing scaffolding:
		 * $this->load->scaffolding('table_name')
		 *
		 */
		$this->current_table = $db_table;
		
		/**
		 * Set the path to the "view" files
		 * We'll manually override the "view" path so that
		 * the load->view function knows where to look.
		 */
		
		$this->CI->load->_ci_view_path = BASEPATH.'scaffolding/views/';

		// Set the base URL
		$this->base_url = $this->CI->config->site_url().'/'.$this->CI->uri->segment(1).$this->CI->uri->slash_segment(2, 'both');
		$this->base_uri = $this->CI->uri->segment(1).$this->CI->uri->slash_segment(2, 'leading');

		// Set a few globals
		$data = array(
						'image_url'	=> $this->CI->config->system_url().'scaffolding/images/',
						'base_uri'  => $this->base_uri,
						'base_url'	=> $this->base_url,
						'title'		=> $this->current_table
					);
		
		$this->CI->load->vars($data);
		
		// Load the language file and create variables
		$this->lang = $this->CI->load->scaffold_language('scaffolding', '', TRUE);
		$this->CI->load->vars($this->lang);
				
		//  Load the helper files we plan to use
		$this->CI->load->helper(array('url', 'form'));
		
				
		log_message('debug', 'Scaffolding Class Initialized');
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * "Add" Page
	 *
	 * Shows a form representing the currently selected DB
	 * so that data can be inserted
	 *
	 * @access	public
	 * @return	string	the HTML "add" page
	 */
	function add()
	{	
		$data = array(
						'title'	=>  ( ! isset($this->lang['scaff_add'])) ? 'Add Data' : $this->lang['scaff_add'],
						'fields' => $this->CI->db->field_data($this->current_table),
						'action' => $this->base_uri.'/insert'
					);
	
		$this->CI->load->view('add', $data);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Insert the data
	 *
	 * @access	public
	 * @return	void	redirects to the view page
	 */
	function insert()
	{		
		if ($this->CI->db->insert($this->current_table, $_POST) === FALSE)
		{
			$this->add();
		}
		else
		{
			redirect($this->base_uri.'/view/');
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * "View" Page
	 *
	 * Shows a table containing the data in the currently
	 * selected DB
	 *
	 * @access	public
	 * @return	string	the HTML "view" page
	 */
	function view()
	{
		// Fetch the total number of DB rows
		$total_rows = $this->CI->db->count_all($this->current_table);
		
		if ($total_rows < 1)
		{
			return $this->CI->load->view('no_data');
		}
		
		// Set the query limit/offset
		$per_page = 20;
		$offset = $this->CI->uri->segment(4, 0);
		
		// Run the query
		$query = $this->CI->db->get($this->current_table, $per_page, $offset);

		// Now let's get the field names				
		$fields = $this->CI->db->list_fields($this->current_table);
		
		// We assume that the column in the first position is the primary field.
		$primary = current($fields);

		// Pagination!
		$this->CI->pagination->initialize(
							array(
									'base_url'		 => $this->base_url.'/view',
									'total_rows'	 => $total_rows,
									'per_page'		 => $per_page,
									'uri_segment'	 => 4,
									'full_tag_open'	 => '<p>',
									'full_tag_close' => '</p>'
									)
								);	

		$data = array(
						'title'	=>  ( ! isset($this->lang['scaff_view'])) ? 'View Data' : $this->lang['scaff_view'],
						'query'		=> $query,
						'fields'	=> $fields,
						'primary'	=> $primary,
						'paginate'	=> $this->CI->pagination->create_links()
					);
						
		$this->CI->load->view('view', $data);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * "Edit" Page
	 *
	 * Shows a form representing the currently selected DB
	 * so that data can be edited
	 *
	 * @access	public
	 * @return	string	the HTML "edit" page
	 */
	function edit()
	{
		if (FALSE === ($id = $this->CI->uri->segment(4)))
		{
			return $this->view();
		}

		// Fetch the primary field name
		$primary = $this->CI->db->primary($this->current_table);				

		// Run the query
		$query = $this->CI->db->get_where($this->current_table, array($primary => $id));

		$data = array(
						'title'	=>  ( ! isset($this->lang['scaff_edit'])) ? 'Edit Data' : $this->lang['scaff_edit'],
						'fields'	=> $query->field_data(),
						'query'		=> $query->row(),
						'action'	=> $this->base_uri.'/update/'.$this->CI->uri->segment(4)
					);
	
		$this->CI->load->view('edit', $data);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Update
	 *
	 * @access	public
	 * @return	void	redirects to the view page
	 */
	function update()
	{	
		// Fetch the primary key
		$primary = $this->CI->db->primary($this->current_table);				

		// Now do the query
		$this->CI->db->update($this->current_table, $_POST, array($primary => $this->CI->uri->segment(4)));
		
		redirect($this->base_uri.'/view/');
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Delete Confirmation
	 *
	 * @access	public
	 * @return	string	the HTML "delete confirm" page
	 */
	function delete()
	{
		if ( ! isset($this->lang['scaff_del_confirm']))
		{
			$message = 'Are you sure you want to delete the following row: '.$this->CI->uri->segment(4);
		}
		else
		{
			$message = $this->lang['scaff_del_confirm'].' '.$this->CI->uri->segment(4);
		}
		
		$data = array(
						'title'		=> ( ! isset($this->lang['scaff_delete'])) ? 'Delete Data' : $this->lang['scaff_delete'],
						'message'	=> $message,
						'no'		=> anchor(array($this->base_uri, 'view'), ( ! isset($this->lang['scaff_no'])) ? 'No' : $this->lang['scaff_no']),
						'yes'		=> anchor(array($this->base_uri, 'do_delete', $this->CI->uri->segment(4)), ( ! isset($this->lang['scaff_yes'])) ? 'Yes' : $this->lang['scaff_yes'])
					);
	
		$this->CI->load->view('delete', $data);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Delete
	 *
	 * @access	public
	 * @return	void	redirects to the view page
	 */
	function do_delete()
	{		
		// Fetch the primary key
		$primary = $this->CI->db->primary($this->current_table);				

		// Now do the query
		$this->CI->db->where($primary, $this->CI->uri->segment(4));
		$this->CI->db->delete($this->current_table);

		header("Refresh:0;url=".site_url(array($this->base_uri, 'view')));
		exit;
	}

}

/* End of file Scaffolding.php */
/* Location: ./system/scaffolding/Scaffolding.php */