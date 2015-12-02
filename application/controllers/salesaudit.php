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
		$data['pagetitle'] = 'Sales Audit <small><i>(Per POS)</i></small>';
		$this->load->view('templates/salesaudit_pos',$data);
	}

	public function salesaudit_total()
	{
		if (!$this->session->userdata('fm_username'))
		{
			redirect('payables/login');
		}

		# Load the view for home
		$data['pagetitle'] = 'Sales Audit <small><i>(Total Sales)</i></small>';
		$this->load->view('templates/salesaudit_total',$data);
	}

	public function filter_salesaudit_pos()
	{
		#convert date to YY/MM/DD
		$date = $this->input->post('datefilter');
		$date = new DateTime($this->input->post('datefilter'));
		$format_date_from = $date->format("ymd");
		$frmt_date_from = "$format_date_from"; 
		$datefrom =  $frmt_date_from;
		
		$data['pagetitle'] = 'Sales Audit <small><i>(Per POS)</i></small>';
		$data['salesaudit'] = $this->search_model->getResult($datefrom);
		$this->load->view('templates/salesaudit_pos',$data);

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
		
		$data['pagetitle'] = 'Sales Audit <small><i>(Total Sales)</i></small>';
		$data['salesaudit'] = $this->search_model->getTotalResult($datefrom);

		$this->load->view('templates/salesaudit_total',$data);

		if($action != 'Filter')
		{
			$location = $this->input->post('selector');
			$count_location = count($location);

			if($count_location == 0)
			{
				$this->session->set_flashdata("message", "Please select location.");
				redirect('salesaudit/salesaudit_total');

			}

			$today=date("Ymd");
			$output_dir="csv.docs\\";
			$filename = "$today.csv";
			$dataFile = fopen("csv.docs\\".$filename,'w');
			$bldat = date('Ymd');
			fputs($dataFile,"IND, BLDAT_, BLART, BUKRS_,BUDAT_,MONAT,WAERS,KURSF,XBLNR,SGTXT,CTAX,BSCHL,HKONT,DMBTR,WMBTR,PRCTR,ZUONR,HBNK,ACCID,MWSKZ,VALDT,ITTXT,KOSTL,WBSEL,UMSKZ\n");
			for($i = 0; $i < $count_location; $i++)
			{
				$this->dbh = new PDO($this->connectionString(),"","");

			$query = "select b.strnam,sum(case when csdtyp in ('00','ZZ') then csdamt else 0 end),sum(case when a.csdtyp='VE' then a.csdamt else 0 end),sum(case when a.csdtyp='10' then a.csdamt else 0 end) 
                      from MMFMSLIB.CSHTND a inner join MMFMSLIB.TBLSTR b on a.csstor=b.strnum
                      where b.strnam = '".$location[$i]."' AND a.csdate = {$datefrom} group by b.strnam order by b.strnam ";

            $statement = $this->dbh->prepare($query);
			$statement->execute();
			$result  = $statement->fetchAll();          

			 fputs($dataFile,"1,$bldat,SA,P001,$bldat,10,PHP,,P001000120151001,P001000120151001,X\n");

			  foreach ($result as $key => $value) {
			  $cash = number_format($value['00002'], 0);
			  $vat  = number_format($value['00003'], 0);

			 fputs($dataFile,"2, ,, ,$bldat,10,PHP,,,,,1,IC0TR400,\"$cash\",\"$cash\",P0010001,$bldat,,,,,Cash,,,, ,, ,, ,, ,,\n");
			 fputs($dataFile,"2, ,, ,$bldat,10,PHP,,,,,1,IC0TR400,0,0,P0010001,$bldat,,,,,SNAP Payments,,,, ,, ,, ,, ,,\n");
			 fputs($dataFile,"2, ,, ,$bldat,10,PHP,,,,,40,IC0TR400,0,0,P0010001,$bldat,,,,,Senior Discount and PWD,,,, ,, ,, ,, ,,\n");
			 fputs($dataFile,"2, ,, ,$bldat,10,PHP,,,,,50,IC0TR400,0,0,P0010001,$bldat,,,,,Sales with VAT RTE,,,, ,, ,, ,, ,,\n");
			 fputs($dataFile,"2, ,, ,$bldat,10,PHP,,,,,50,IC0TR400,0,0,P0010001,$bldat,,,,,Sales with VAT FF,,,, ,, ,, ,, ,,\n");
			 fputs($dataFile,"2, ,, ,$bldat,10,PHP,,,,,50,IC0TR400,0,0,P0010001,$bldat,,,,,Sales with VAT CVS,,,, ,, ,, ,, ,,\n");
			 fputs($dataFile,"2, ,, ,$bldat,10,PHP,,,,,50,IC0TR400,0,0,P0010001,$bldat,,,,,Sales VAT Exempt RTE,,,, ,, ,, ,, ,,\n");
			 fputs($dataFile,"2, ,, ,$bldat,10,PHP,,,,,50,IC0TR400,0,0,P0010001,$bldat,,,,,Sales VAT Exempt FF,,,, ,, ,, ,, ,,\n");
			 fputs($dataFile,"2, ,, ,$bldat,10,PHP,,,,,50,IC0TR400,0,0,P0010001,$bldat,,,,,Sales VAT Exempt CVS,,,, ,, ,, ,, ,,\n");
			 fputs($dataFile,"2, ,, ,$bldat,10,PHP,,,,,50,IC0TR400,\"$vat\",\"$vat\",P0010001,$bldat,,,,,VAT Amount,,,, ,, ,, ,, ,,\n");
			  }
			}
		}
	}
}


