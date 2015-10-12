<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Payables extends CI_Controller {

	public function __construct()
	{
		error_reporting(E_ALL & ~E_NOTICE);
		parent::__construct();
		ini_set('memory_limit','30000M'); // mem
		ini_set('max_execution_time', 3000); // time
		$this->load->library('session');
	}

	public function login()
	{
		$data['pagetitle'] = 'Login Page';
		$this->load->view('templates/login',$data);

		if($_SERVER['REQUEST_METHOD'] == 'POST')
		{
			$jda_username = $this->security->xss_clean($this->input->post('username'));
			$jda_password = $this->security->xss_clean($this->input->post('password'));

			$this->db->select('username, password');
			$this->db->where('username', $jda_username);
			$this->db->where('password', $jda_password);  
			$query = $this->db->get('payables_login');

			if($query->num_rows() == 1)
			{
				$this->session->set_userdata('fm_username',$jda_username);
				$this->session->set_userdata('fm_password',$jda_password);
				redirect('payables/index');	
			}
			else
			{
				$this->session->set_flashdata("message", "Incorrect Username/Password");
				redirect('payables/login');	
			}
		}
	}

	public function logout()
	{
		$this->session->sess_destroy();
		redirect('payables/login');
	}


	public function index()
	{
		if (!$this->session->userdata('fm_username'))
		{
			redirect('payables/login');
		}
		# Load the view for home
		$data['pagetitle'] = 'Home';
		$this->load->view('templates/home',$data);
	}



	public function ShowPay()
	{

		if (!$this->session->userdata('fm_username'))
		{
			redirect('payables/login');
		}
		set_time_limit(0);

		$query = $this->db->query("SELECT po_no from payables_status");
		$res = $query->result();
		$po = array();
		foreach ($res as $key => $ponumb) {
			$po[] =  $ponumb->po_no;
		}
		$cnString = "odbc:DRIVER={iSeries Access ODBC Driver}; ".
					"SYSTEM=172.16.1.9; ".
					"DATABASE=MMFMSLIB; ".
					"UID=DCLACAP; ".
					"PWD=PASSWORD";
		$this->dbh = new PDO($cnString,"","");
		$date = $this->session->userdata("date");

		if(count($res) != 0 && $date != "")
		{
			$query = 'select ponumb,poloc,pordat,pomrcv,porvcs,poladg,poshpr,asname,astrms
                      from MMFMSLIB.POMRCH inner join  MMFMSLIB.APSUPP on povnum=asnum
                      where pordat = '.$date.' and ponumb not in('.implode(',', $po).')
                      order by ponumb desc';	
		}else
		{
			$query = 'select ponumb,poloc,pordat,pomrcv,porvcs,poladg,poshpr,asname,astrms
                      from MMFMSLIB.POMRCH inner join  MMFMSLIB.APSUPP on povnum=asnum
                      where pordat = 151008
                      order by ponumb desc';	
		}
		  
		$statement = $this->dbh->prepare($query);
		$statement->execute();
		$result  = $statement->fetchAll();

		
		# Load the view for home
		if($date != ""){
			$data['payables'] = $result;
		}
		$data['pagetitle'] = 'Store Accounting';
		$this->load->view('templates/payables',$data);

	if($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		$action = $this->input->post('btnfilter');

		if($action == 'Filter')
		{
			$date = new DateTime($this->input->post('datefrom'));
			$format_date_from = $date->format("ymd");
			$frmt_date_from = "$format_date_from"; 
			$datefrom =  $frmt_date_from;
			$this->session->set_userdata("date", $datefrom);
			redirect('payables/showpay');
		}
		$po_no = $this->input->post('selector');
		$rcr_no = $this->input->post('rcr_no');
		$count_po = count($po_no);

		for($i = 0; $i < $count_po; $i++)
		{
			if($this->input->post('txt_'.$po_no[$i]) == '')
			{
				$payables_data = array(
				'po_no'      => $po_no[$i],
				'rcr_no'     => $this->input->post('hdn_'.$po_no[$i]),
				'new_amount' => '',
				'status'	 => '2');
			}else{
				$payables_data = array(
				'po_no'      => $po_no[$i],
				'rcr_no'     => $this->input->post('hdn_'.$po_no[$i]),
				'new_amount' => $this->input->post('txt_'.$po_no[$i]),
				'status'	 => '1');
			}

			$this->db->insert('payables_status', $payables_data);	
		}

		$this->session->set_flashdata('message', 'Changes successfully saved!');
		redirect('payables/showpay');
	}
	}

	public function salesaudit()
	{
		if (!$this->session->userdata('fm_username'))
		{
			redirect('payables/login');
		}
		# Load the view for home
		$data['pagetitle'] = 'Sales Audit';
		$this->load->view('templates/salesaudit',$data);
	}


	public function process()
	{
		if (!$this->session->userdata('fm_username'))
		{
			redirect('payables/login');
		}


		$query = $this->db->query("SELECT po_no from payables_status  where status = 2");
		$res = $query->result();
		$po = array();
		foreach ($res as $key => $ponumb) {
			$po[] =  $ponumb->po_no;
		}

		$cnString = "odbc:DRIVER={iSeries Access ODBC Driver}; ".
					"SYSTEM=172.16.1.9; ".
					"DATABASE=MMFMSLIB; ".
					"UID=DCLACAP; ".
					"PWD=PASSWORD";
		$this->dbh = new PDO($cnString,"","");

		
			$query = 'select ponumb,poloc,pordat,pomrcv,porvcs,poladg,poshpr,asname,astrms
                      from MMFMSLIB.POMRCH inner join  MMFMSLIB.APSUPP on povnum=asnum
                      where ponumb in('.implode(',', $po).')
                      order by ponumb desc ';	
		
			$statement = $this->dbh->prepare($query);
			$statement->execute();
			$result  = $statement->fetchAll();
		

		
		# Load the view for home
		$data['process'] = $result;
		$data['pagetitle'] = 'All Process';
		$this->load->view('templates/allprocess',$data);

		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			
		$query = $this->db->query("SELECT po_no from payables_status  where status = 2");
		$res = $query->result();
		$po = array();
		foreach ($res as $key => $ponumb) {
			$po[] =  $ponumb->po_no;
		}

		$cnString = "odbc:DRIVER={iSeries Access ODBC Driver}; ".
					"SYSTEM=172.16.1.9; ".
					"DATABASE=MMFMSLIB; ".
					"UID=DCLACAP; ".
					"PWD=PASSWORD";
		$this->dbh = new PDO($cnString,"","");


			$query = 'select ponumb,poloc,pordat,pomrcv,porvcs,poladg,poshpr,asname,astrms
                      from MMFMSLIB.POMRCH inner join  MMFMSLIB.APSUPP on povnum=asnum
                      where  ponumb in('.implode(',', $po).')
                      order by ponumb desc';	
		

		$statement = $this->dbh->prepare($query);
		$statement->execute();
		$result  = $statement->fetchAll();

		$output_dir="csv.docs\\";
		$todayz=date("mdY",strtotime('+8 hours'));
		$filename = "ALLPROCESS_"."$todayz".".csv";
		$dataFile = fopen($output_dir.$filename,'w');
		fputs($dataFile,"\"PO NO.\",\"RCR NO.\",\"LOCATION\",\"VENDOR\",\"PAYMENT TERM\",\"REC DATE\",\"INVOICE #\",\"RCR AMT\",\"INVOICE AMT\"\n");

		foreach ($result as $value) {
			$po_no   = $value['PONUMB'];
			$po_loc  = $value['POLOC'];
			$po_date = $value['PORDAT'];
			$po_mrcv = $value['POMRCV'];
			$po_rvcs = $value['PORVCS'];
			$po_ladg = trim($value['POLADG']);
			$po_shpr = trim($value['POSHPR']);
			$as_name = $value['ASNAME'];
			$as_trms = $value['ASTRMS'];

			fputs($dataFile,"\"$po_no\",\"$po_mrcv\",\"$po_loc\",\"$as_name\",\"$as_trms\",\"$po_date\",\"$po_ladg\",\"$po_shpr\",\"$po_rvcs\"\n");
		}

		$this->session->set_flashdata("message", "Successfully exported!");
		redirect('payables/process');
		}
	}




}