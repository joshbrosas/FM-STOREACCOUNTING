<?php

class Salesaudit extends CI_Controller{

	function __construct()
	{
		error_reporting(E_ALL & ~E_NOTICE);
		parent::__construct();
		ini_set('memory_limit','30000M'); // mem
		ini_set('max_execution_time', 3000); // time
		set_time_limit(0);
		$this->load->library('session');
		$this->load->model('search_model');
	}

	public function index()
	{
		#check if the user has logged in
		if (!$this->session->userdata('fm_username'))
		{
			redirect('payables/login');
		}

		# Load the view for home
		$data['pagetitle'] = 'Sales Audit';
		$this->load->view('templates/salesaudit',$data);
	}

	public function filter_salesaudit()
	{
		#convert date to YY/MM/DD
		$date = $this->input->post('datefilter');
		$date = new DateTime($this->input->post('datefilter'));
		$format_date_from = $date->format("ymd");
		$frmt_date_from = "$format_date_from"; 
		$datefrom =  $frmt_date_from;
		
		$data['pagetitle'] = 'Sales Audit';
		$data['salesaudit'] = $this->search_model->getResult($datefrom);
		$this->load->view('templates/salesaudit',$data);
	}

	public function exportSales()
	{
		
	}


}


