<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
$this->load->database();
$this->db->select('*');
$this->db->join('User', 'Article.userid = User.id', 'left');//natural RIGHT outer');
$this->db->get_where('Article');
print_r($this->db->queries);

$this->db->join('User', 'Article.userid = User.id', 'natural RIGHT outer');
$this->db->get_where('Article');
print_r($this->db->queries);
/*
        $this->db->select('*,worktypes.Name as WorkTypeName
			,departments.Name as DepartmentName
			,u1.Realname as CreateUserRealname
			,u2.Realname as ToUserRealname');
        $this->db->where('orders.CreateDatetime >=', $startdt);
        $this->db->where('orders.CreateDatetime <', $enddt);
        $this->db->where('orders.HospitalId', $HospitalId);
        $this->db->join('orders', 'todos.OrderId = orders.OrderId', 'inner');
        $this->db->join('worktypes', 'worktypes.Mode = orders.WorkTypeMode', 'inner');
        $this->db->join('users as u1', 'u1.UserId = orders.CreateUserId', 'inner');
        $this->db->join('users_ext_hospital', 'users_ext_hospital.UserId = u1.UserId', 'inner');
        $this->db->join('departments', 'departments.DepartmentId = users_ext_hospital.DepartmentId', 'inner');
        $this->db->join('users as u2', 'u2.UserId = todos.ToUserId', 'inner');
        return parent::showMyAll();
*/
		$this->load->view('welcome_message');
	}
}
