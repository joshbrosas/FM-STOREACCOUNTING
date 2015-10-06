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
			$jda_username = strtoupper($this->input->post('username'));
			$jda_password = $this->input->post('password');

			if($jda_username == 'DOLF' && $jda_password == 'admin123') //override account for local testing
			{
				$this->session->set_userdata('jda_username',$jda_username);
				$this->session->set_userdata('jda_password',$jda_password);
				redirect('payables/index');
			}
			else
			{
				$process_error = "Login Failed!";
				$data['process_error'] = $process_error;
				redirect('payables/login');
			}
		}
	}


	public function index()
	{
		if (!$this->session->userdata('jda_username'))
		{
			redirect('payables/login');
		}
		# Load the view for home
		$data['pagetitle'] = 'Home';
		$this->load->view('templates/home',$data);
	}

	public function ShowPay()
	{
		if (!$this->session->userdata('jda_username'))
		{
			redirect('payables/login');
		}

		$cnString = "odbc:DRIVER={iSeries Access ODBC Driver}; ".
					"SYSTEM=172.16.1.9; ".
					"DATABASE=MMFMSLIB; ".
					"UID=DCLACAP; ".
					"PWD=PASSWORD";
		$this->dbh = new PDO($cnString,"","");
		$query = "select ponumb,poloc,pordat,pomrcv,porvcs,poladg,poshpr,asname,astrms
                        from MMFMSLIB.POMRCH inner join  MMFMSLIB.APSUPP on povnum=asnum
                        where pordat between 151001 and 151002
                        order by ponumb desc";

		$statement = $this->dbh->prepare($query);
		$statement->execute();
		$result  = $statement->fetchAll();


		# Load the view for home
		$data['payables'] = $result;
		$data['pagetitle'] = 'Payables';
		$this->load->view('templates/payables',$data);

	if($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		$po_no = $this->input->post('selector');
		$rcr_no = $this->input->post('rcr_no');
		$count_po = count($po_no);

		for($i = 0; $i < $count_po; $i++)
		{
			$data = array(
					'po_no'      => $po_no[$i],
					'rcr_no'     => $this->input->post('hdn_'.$po_no[$i]),
					'new_amount' => $this->input->post('txt_'.$po_no[$i]),
					'status'	 => '1'
			);

			$this->db->insert('payables_status', $data);
			$this->session->set_flashdata('message', 'Changes successfully saved!');
			redirect('payables/showpay');

			}
	}

	}






}