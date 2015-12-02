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
				$userinfo = $this->db->query("SELECT * FROM payables_login WHERE username = '{$jda_username}' AND password = md5($jda_password)");
				$user_result = $userinfo->result();
				$jda_roles = $user_result[0]->roles;

				$this->session->set_userdata('fm_username',$jda_username);
				$this->session->set_userdata('fm_password',$jda_password);
				$this->session->set_userdata('fm_roles',   $jda_roles);
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
		$data['pagetitle'] = 'Payables';
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
			$query = $this->db->query("SELECT * FROM payables_status");

			$p_status = $query->result();

			$query = $this->db->query("SELECT po_no from payables_status  where status = 2");
			$res = $query->result();
			$po = array();
			foreach ($res as $key => $ponumb) {
			$po[] =  $ponumb->po_no;
			}

			$this->dbh = new PDO($this->ConnectionString(),"","");

			#check if the po is empty
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
			$data['count_status'] = $p_status;

		}
		$data['count_status'] = $p_status;
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
		#redirect to a model with a function mod_matched
		$this->search_model->mod_matched();
		$this->session->set_flashdata("message", "Successfully exported!");
		redirect('payables/process');
	}

	public function transaction()
	{
		try {
		#check if the payable_status have a value
		$query = $this->db->query("SELECT * FROM payables_status");
		$p_status = $query->result();


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

			#Check if the query is empty
			if(!empty($po))
			{
				$query = 'select ponumb,poloc,pordat,pomrcv,porvcs,poladg,poshpr,asname,astrms
                      from MMFMSLIB.POMRCH inner join  MMFMSLIB.APSUPP on povnum=asnum
                      where ponumb in('.implode(',', $po).')
                      order by ponumb desc ';	
		
				$statement = $this->dbh->prepare($query);
				$statement->execute();
				$result  = $statement->fetchAll();
				$data['count_status'] = $p_status;
				$data['amount'] = $amt;
				$data['transaction'] = $result;	
			}
				$data['count_status'] = $p_status;
				$data['pagetitle'] = 'Exception';
				$this->load->view('templates/transaction', $data);	
		} catch (Exception $e) {
			echo $e->getMessage();	
		}
	}

	public function postException()
	{
		#redirect to model with a function mod_exception
		$this->search_model->mod_exception();
		$this->session->set_flashdata("message", "Exported Successfully!");
		redirect('payables/transaction');
	}

	public function salesaudit()
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

	public function consignment()
	{
		$data['pagetitle'] = 'Consignment Sales';
		$this->load->view('templates/consignment', $data);
	}

	public function filter_consignment()
	{
		
		$action = $this->input->post('btnexport');

		$date = new DateTime($this->input->post('datefilter1'));
		$format_date_from = $date->format("ymd");
		$frmt_date_from = "$format_date_from"; 
		$datefrom =  $frmt_date_from;

		$date = new DateTime($this->input->post('datefilter2'));
		$format_date_from = $date->format("ymd");
		$frmt_date_from = "$format_date_from"; 
		$dateto =  $frmt_date_from;

		if($action == 'export')
		{
			$this->search_model->exportConsignment($datefrom, $dateto);
			$data['pagetitle'] = 'Consignment Sales';
			$this->session->set_flashdata('message', 'Exported Successfully!');
			redirect('payables/consignment');
		}

		#convert date to YY/MM/DD
		
		$data['result']	   = $this->search_model->consignment($datefrom, $dateto);
		$data['pagetitle'] = 'Consignment Sales';
		$this->load->view('templates/consignment', $data);


	}

	public function fdate($date1)
	{
		$len=strlen($date1);
		if($len < 4 or $len == 0 or $date1 == "")
			return 0;
		$day=substr($date1,$len-2,2);
		$mo=substr($date1,$len-4,2);
		if($len==5)
			$yr="0" . substr($date1,0,1);
		elseif($len==4)
			$yr="00";
		else
			$yr=substr($date1,0,2);
			if($yr >= 80){
			$yr=$yr+1900;
			}else{
			$yr=$yr+2000;
			}
		$ret_str="$yr-$mo-$day";
		return $ret_str;
	}

	public function fmonth($date){
			$len=strlen($date1);
		if($len < 4 or $len == 0 or $date1 == "")
			return 0;
		$day=substr($date1,$len-2,2);
		$mo=substr($date1,$len-4,2);
		if($len==5)
			$yr="0" . substr($date1,0,1);
		elseif($len==4)
			$yr="00";
		else
			$yr=substr($date1,0,2);
			if($yr >= 80){
			$yr=$yr+1900;
			}else{
			$yr=$yr+2000;
			}
		$ret_str="$mo";
		return $ret_str;
	}

}