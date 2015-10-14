<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Payables extends CI_Controller {

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

	public function ConnectionString()
	{
		return "odbc:DRIVER={iSeries Access ODBC Driver}; ".
					"SYSTEM=172.16.1.9; ".
					"DATABASE=MMFMSLIB; ".
					"UID=DCLACAP; ".
					"PWD=PASSWORD";
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
			$this->db->where('password', md5($jda_password));  
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
		$data['pagetitle'] = 'Store Accounting';
		$this->load->view('templates/payables',$data);
	}

	public function postPayables(){

		$action = $this->input->post('btnfilter');

		if($action != 'Filter')
		{
		
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
		$date = new DateTime($this->input->post('date'));
		$format_date_from = $date->format("ymd");
		$frmt_date_from = "$format_date_from"; 
		$datefrom =  $frmt_date_from;

		$data['payables'] = $this->search_model->mod_payables($datefrom);
		$data['pagetitle'] = 'Store Accounting';
		$this->load->view('templates/payables',$data);
	}

	public function process()
	{
		if (!$this->session->userdata('fm_username'))
		{
			redirect('payables/login');
		}


		try {
			$query = $this->db->query("SELECT po_no from payables_status  where status = 2");
			$res = $query->result();
			$po = array();
			foreach ($res as $key => $ponumb) {
			$po[] =  $ponumb->po_no;
			}

			$this->dbh = new PDO($this->ConnectionString(),"","");

		
			if(!empty($po)){
				$query = 'select ponumb,poloc,pordat,pomrcv,porvcs,poladg,poshpr,asname,astrms
                      from MMFMSLIB.POMRCH inner join  MMFMSLIB.APSUPP on povnum=asnum
                      where ponumb in('.implode(',', $po).')
                      order by ponumb desc ';	
		
			$statement = $this->dbh->prepare($query);
			$statement->execute();
			$result  = $statement->fetchAll();
			# Load the view for home
			$data['process'] = $result;
		}

		$data['pagetitle'] = 'Two Way Matched';
		$this->load->view('templates/allprocess',$data);

		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			
		
		$this->session->set_flashdata("message", "Successfully exported!");
		redirect('payables/process');
		}
		} catch (PDOException $e) {
			echo $e->getMessage();	
		}
	}

	public function postMatched()
	{
		$this->search_model->mod_matched();
		$this->session->set_flashdata("message", "Exported Successfully!");
		redirect('payables/process');
	}

	public function transaction()
	{
		try {
		$query = $this->db->query("SELECT po_no, new_amount from payables_status  where status = 1");
		$res = $query->result();
		$po = array();
		$amount = array();
		foreach ($res as $key => $ponumb) 
		{
			$po[]  =  $ponumb->po_no;
			$amt[] =  $ponumb->new_amount;
		}

		$this->dbh = new PDO($this->ConnectionString(),"","");

			if(!empty($po))
			{
				$query = 'select ponumb,poloc,pordat,pomrcv,porvcs,poladg,poshpr,asname,astrms
                      from MMFMSLIB.POMRCH inner join  MMFMSLIB.APSUPP on povnum=asnum
                      where ponumb in('.implode(',', $po).')
                      order by ponumb desc ';	
		
				$statement = $this->dbh->prepare($query);
				$statement->execute();
				$result  = $statement->fetchAll();

				$data['amount'] = $amt;
				$data['transaction'] = $result;	
			}
				$data['pagetitle'] = 'Exception';
				$this->load->view('templates/transaction', $data);	
		} catch (Exception $e) {
			echo $e->getMessage();	
		}
	}

	public function postException()
	{
		$this->search_model->mod_exception();
		$this->session->set_flashdata("message", "Exported Successfully!");
		redirect('payables/transaction');
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

	public function filter_salesaudit()
	{
		$date = $this->input->post('datefilter');
		$date = new DateTime($this->input->post('datefilter'));
		$format_date_from = $date->format("ymd");
		$frmt_date_from = "$format_date_from"; 
		$datefrom =  $frmt_date_from;

		$data['salesaudit'] = $this->search_model->getResult($datefrom);
		$this->load->view('templates/salesaudit',$data);
	}
}