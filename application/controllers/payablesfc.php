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

	public function ConnectionString()
	{
		return "odbc:DRIVER={iSeries Access ODBC Driver}; ".
					"SYSTEM=172.16.1.9; ".
					"DATABASE=MMFMSLIB; ".
					"UID=DCLACAP; ".
					"PWD=PASSWORD";
	}

	public function showPayables()
	{
		$data['pagetitle'] = 'Payables FC';

		$this->load->view('templates/payables_fc/payablesfc', $data);

	}

	public function postpayables()
	{
		$action = $this->input->post('btnfilter');

		if($action == 'Filter')
		{
			$filterdate =  $this->formatdate($this->input->post('date'));

			$this->dbh = new PDO($this->connectionString(),"","");

		 	$query = "select POEDAT,POSDAT,PONUMB,POVNUM,POCOST from MMFMSLIB.POMHDR where POEDAT =$filterdate";
			
			$statement = $this->dbh->prepare($query);
			$statement->execute();
			$result  = $statement->fetchAll();
			
		 	$data['records'] = $result;
			$data['pagetitle'] = 'Payables FC';
			$this->load->view('templates/payables_fc/payablesfc',$data);
		}
		else
		{
			$po_no = $this->input->post('selector');
			$rcr_no = $this->input->post('rcr_no');
			$count_po = count($po_no);

			for($i = 0; $i < $count_po; $i++)
			{
				if($this->input->post('txt_'.$po_no[$i]) == '')
				{
					$payablesfc_data = array(
					'PONO'      => $po_no[$i],
					'RCRNO'     => $this->input->post('hdn_'.$po_no[$i]),
					'LOCATION'	 => $this->input->post('branch_'.$po_no[$i]),
					'VENDOR'	 => $this->input->post('spname_'.$po_no[$i]),
					'PTERM'	 	 => $this->input->post('payterm_'.$po_no[$i]),
					'RECDATE'	 => $this->input->post('recdate_'.$po_no[$i]),
					'INVNO'	 	 => $this->input->post('ansipo_'.$po_no[$i]),
					'RCRAMT'	 => $this->input->post('rcr_'.$po_no[$i]),
					'INVAMT'	 => $this->input->post('invoice_'.$po_no[$i]),
					'NEWAMT' 	 => '',
					'STATUS'	 => '2');
				}else{
					$payablesfc_data = array(
					'PONO'      => $po_no[$i],
					'RCRNO'     => $this->input->post('hdn_'.$po_no[$i]),
					'LOCATION'	 => $this->input->post('branch_'.$po_no[$i]),
					'VENDOR'	 => $this->input->post('spname_'.$po_no[$i]),
					'PTERM'	 	 => $this->input->post('payterm_'.$po_no[$i]),
					'RECDATE'	 => $this->input->post('recdate_'.$po_no[$i]),
					'INVNO'	  	 => $this->input->post('ansipo_'.$po_no[$i]),
					'RCRAMT'	 => $this->input->post('rcr_'.$po_no[$i]),
					'INVAMT'	 => $this->input->post('invoice_'.$po_no[$i]),
					'NEWAMT' 	 => $this->input->post('txt_'.$po_no[$i]),
					'STATUS'	 => '1');
				}
				$this->db->insert('sa_pfcstat', $payablesfc_data);
			}
				$this->session->set_flashdata('message', 'Successfully saved!');
				redirect('payablesfc/showPayables');
		}
	}


	public function fcmatch()
	{
		$query = $this->db->query("SELECT * FROM sa_pfcstat where STATUS = 2 order by RECDATE DESC");
		$result = $query->result();

		$data['process'] = $result;
		$data['pagetitle'] = 'Payables FC | Two way Matched';
		$this->load->view('templates/payables_fc/fcmatch', $data);
	}

	public function fcexception()
	{

		$query = $this->db->query("SELECT * FROM sa_pfcstat where STATUS = 1 order by RECDATE DESC");
		$result = $query->result();

		$data['process'] = $result;
		$data['pagetitle'] = 'Payables FC | Exception';
		$this->load->view('templates/payables_fc/fcexception', $data);

	}



	
	public function formatdate($input)
	{
		#format date to  Y/M/D
		$date = new DateTime($input);
		$format_date_from = $date->format("ymd");
		$frmt_date_from = "$format_date_from"; 
		$datefrom =  $frmt_date_from;
		return $datefrom;
	}
}