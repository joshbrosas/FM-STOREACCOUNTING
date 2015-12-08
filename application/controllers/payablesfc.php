<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Payablesfc extends CI_Controller {

	function __construct()
	{
		error_reporting(E_ALL & ~E_NOTICE);
		parent::__construct();
		ini_set('memory_limit','30000M'); // mem
		ini_set('max_execution_time', 3000); // time
		set_time_limit(0);
		$this->load->library('session');
		#$payables = new Payables();	
	}

	public function showPayables()
	{
		$data['pagetitle'] = 'Payables FC';

		$this->load->view('templates/payables_fc/payablesfc', $data);

	}

	public function postpayables()
	{
		echo $this->formatdate($this->input->post('date'));
	}	

	#format date to  Y/M/D
	public function formatdate($input)
	{
		$date = new DateTime($input);
		$format_date_from = $date->format("ymd");
		$frmt_date_from = "$format_date_from"; 
		$datefrom =  $frmt_date_from;
		return $datefrom;
	}
}