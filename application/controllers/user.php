<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller {


	function __construct()
	 {
	 	error_reporting(E_ALL & ~E_NOTICE);
		parent::__construct();
		ini_set('memory_limit','30000M'); // mem
		ini_set('max_execution_time', 3000); // time
		set_time_limit(0);
		$this->load->library('session');
		$this->load->library('form_validation');
	 }

	 public function getCreateUser()
	 {
	 	$data['pagetitle'] = 'Create User';
	 	$this->load->view('templates/createuser', $data);
	 }

	 public function postuser()
	 {
	 	$username = $this->input->post('username');
	 	$password = $this->input->post('password');
	 	$cpassword = $this->input->post('cpassword');

	 	$this->form_validation->set_rules('username', 'Username', 'required|is_unique[payables_login.username]');
		$this->form_validation->set_rules('password', 'Password', 'required|matches[cpassword]');
		$this->form_validation->set_rules('cpassword', 'Password Confirmation', 'required');


		if ($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('errors', validation_errors());
			redirect('user/getcreateuser');
		}
		else
		{
			$data = array(
	 				'username' => $username,
	 	 			'password' => md5($password),
	 				'roles'	   => 2,
	 				'created_by' => $this->session->userdata('fm_username')
	 			);
			$this->db->insert('payables_login', $data);
			$this->session->set_flashdata('message', 'User Successfully registered');
			redirect('user/getcreateuser');
			
		}
	 	 		

	 }

}
