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

	public function connectionString()
	{
		return "odbc:DRIVER={iSeries Access ODBC Driver}; ".
				"SYSTEM=172.16.1.9; ".
				"DATABASE=MMFMSLIB; ".
				"UID=DCLACAP; ".
				"PWD=PASSWORD";
	}

	public function salesaudit_pos()
	{
		if (!$this->session->userdata('fm_username'))
		{
			redirect('payables/login');
		}

		# Load the view for home
		$data['pagetitle'] = 'Sales Audit | Per POS';
		$this->load->view('templates/salesaudit/salesaudit_pos',$data);
	}

	public function salesaudit_total()
	{
		if (!$this->session->userdata('fm_username'))
		{
			redirect('payables/login');
		}

		# Load the view for home
		$data['pagetitle'] = 'Sales Audit | Total Sales';
		$this->load->view('templates/salesaudit/salesaudit_total',$data);
	}

	public function salesaudit_report()
	{
		if (!$this->session->userdata('fm_username'))
		{
			redirect('payables/login');
		}

		$query  = $this->db->query("SELECT * FROM sa_salesaudit");
		$get_result  = $query->result();
		
		# Load the view for home
		$data['pagetitle'] = 'Sales Report';
		$data['salesreport'] = $get_result;
		$this->load->view('templates/salesaudit/salesaudit_report',$data);
	}

	public function salesaudit_remove($id)
	{
		$this->db->where('id', $id);
		$this->db->delete('sa_salesaudit'); 

		redirect('salesaudit/salesaudit_report');
	}

	public function filter_salesaudit_pos()
	{
		#convert date to YY/MM/DD
		$date = $this->input->post('datefilter');
		$date = new DateTime($this->input->post('datefilter'));
		$format_date_from = $date->format("ymd");
		$frmt_date_from = "$format_date_from"; 
		$datefrom =  $frmt_date_from;
		
		$data['pagetitle'] = 'Sales Audit | Per POS';
		$data['salesaudit'] = $this->search_model->getResult($datefrom);
		$this->load->view('templates/salesaudit/salesaudit_pos',$data);

		$action = $this->input->post('btnfilter');
	}

	public function filter_salesaudit_total()
	{
		$action = $this->input->post('btnfilter');
		#convert date to YY/MM/DD
		$date = $this->input->post('datefilter');

		$date = new DateTime($this->input->post('datefilter'));
		$format_date_from = $date->format("ymd");
		$frmt_date_from = "$format_date_from"; 
		$datefrom =  $frmt_date_from;
		
		$data['pagetitle'] = 'Sales Audit | Total Sales';
		$data['salesaudit'] = $this->search_model->getTotalResult($datefrom);

		$this->load->view('templates/salesaudit/salesaudit_total',$data);

		if($action != 'Filter')
		{
			$location = $this->input->post('selector');
			$totalsales = $this->input->post('ts');
			$count_location = count($location);

			for ($i=0; $i < $count_location; $i++)
			 { 
				
				$sa_data = array(
					'sa_location' => $this->input->post('loc_'.$location[$i]),
					'sa_totalsales' => $this->input->post('ts_'.$location[$i]),
					'sa_vatexmt'	=> $this->input->post('vatexmt_'.$location[$i]),
					'sa_creditcard' => $this->input->post('cc_'.$location[$i]),
					'sa_curr_date' => $datefrom	
					);
				
				$this->db->insert('sa_salesaudit', $sa_data);
				
			}
			
		//redirect('salesaudit/salesaudit_total');
		
		}
	}

	public function exportreport()
	{
			$today=date("Ymd");
			$output_dir="csv.docs\\";
			$filename = "$today.csv";
			$dataFile = fopen($_SERVER['DOCUMENT_ROOT'] . "/storeaccounting/csv.docs/".$filename,'w');
			$bldat = date('Ymd');
			fputs($dataFile,"IND, BLDAT_, BLART, BUKRS_,BUDAT_,MONAT,WAERS,KURSF,XBLNR,SGTXT,CTAX,BSCHL,HKONT,DMBTR,WMBTR,PRCTR,ZUONR,HBNK,ACCID,MWSKZ,VALDT,ITTXT,KOSTL,WBSEL,UMSKZ\n");

				$query  = $this->db->query("SELECT * FROM sa_salesaudit");
				$get_result  = $query->result();         
			
			 
			  foreach ($get_result as $key => $value) {
			  $cash = $value->sa_totalsales;
			  $vat  = $value->sa_vatexmt;
				fputs($dataFile,"1,$bldat,SA,P001,$bldat,10,PHP,,P001000120151001,P001000120151001,X\n");

			 fputs($dataFile,"2, ,, ,$bldat,,,,,,,1,IC0TR400,\"$cash\",\"$cash\",P0010001,$bldat,,,,,Cash,,,, ,, ,, ,, ,,\n");
			 fputs($dataFile,"2, ,, ,$bldat,,,,,,,1,IC0TR400,0,0,P0010001,$bldat,,,,,SNAP Payments,,,, ,, ,, ,, ,,\n");
			 fputs($dataFile,"2, ,, ,$bldat,,,,,,,40,IC0TR400,0,0,P0010001,$bldat,,,,,Senior Discount and PWD,,,, ,, ,, ,, ,,\n");
			 fputs($dataFile,"2, ,, ,$bldat,,,,,,,50,IC0TR400,0,0,P0010001,$bldat,,,,,Sales with VAT RTE,,,, ,, ,, ,, ,,\n");
			 fputs($dataFile,"2, ,, ,$bldat,,,,,,,50,IC0TR400,0,0,P0010001,$bldat,,,,,Sales with VAT FF,,,, ,, ,, ,, ,,\n");
			 fputs($dataFile,"2, ,, ,$bldat,,,,,,,50,IC0TR400,0,0,P0010001,$bldat,,,,,Sales with VAT CVS,,,, ,, ,, ,, ,,\n");
			 fputs($dataFile,"2, ,, ,$bldat,,,,,,,50,IC0TR400,0,0,P0010001,$bldat,,,,,Sales VAT Exempt RTE,,,, ,, ,, ,, ,,\n");
			 fputs($dataFile,"2, ,, ,$bldat,,,,,,,50,IC0TR400,0,0,P0010001,$bldat,,,,,Sales VAT Exempt FF,,,, ,, ,, ,, ,,\n");
			 fputs($dataFile,"2, ,, ,$bldat,,,,,,,50,IC0TR400,0,0,P0010001,$bldat,,,,,Sales VAT Exempt CVS,,,, ,, ,, ,, ,,\n");
			 fputs($dataFile,"2, ,, ,$bldat,,,,,,,50,IC0TR400,\"$vat\",\"$vat\",P0010001,$bldat,,,,,VAT Amount,,,, ,, ,, ,, ,,\n");
			  }

			$this->session->set_flashdata('message', 'Data exported successfully');
			 redirect('salesaudit/salesaudit_report');	  
			
	}
}


