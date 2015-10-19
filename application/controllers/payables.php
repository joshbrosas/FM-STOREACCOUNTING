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
		$today=date("Y-m-d",strtotime('-1 day'));
		$nwdate=$this->fdate($today);

		$store['00001']="R1000001";
		$store['00002']="R1000002";
		$store['00003']="R1000003";
		$store['00004']="R1000004";
		$store['00005']="R1000005";
		$store['00006']="R1000006";
		$store['00007']="R1000007";
		$store['00008']="R1000008";
		$store['00009']="R1000009";
		$store['00010']="R1000010";
		$store['20']="R2211022";
		$store['9005']="R1009005";
		
		$datex = '151001';
		$len=strlen($datex);
		if($len < 4 or $len == 0 or $datex == "")
			return 0;
		$yy=substr($datex,$len-4,4);
		$mo=substr($datex,$len-6,2);
		$day=substr($datex,$len-8,2);

		$datetrn="$yy$mo$day";

		$datetrn=$this->fdate($datex);
		$datetrn=str_replace("-","",$datetrn);

		$output_dir="csv.docs\\";
		// open a datafile
		$filename = "SAP_INV"."$datetrn".".csv";
		$dataFile = fopen($output_dir.$filename,'w');
		$datetrnx=str_replace("-","",$datex);


		fputs($dataFile,"Indicator,Document Date,Document Type,Company Code,Posting Date,Fiscal Period,Currency Key,Exchange Rate,Reference Document Number,Document Header Text,Calculate tax,Posting Key,Account, Amount in document currency ,Amount in local currency,Profit Center,Assignment Number,House Bank,Account ID ,Tax Code,Value Date,Item Text,Cost Center,WBS Element,Special GL\n");
		$this->dbh = new PDO($this->connectionString(),"","");

	 	$query = "select poladg,pomrcv from MMFMSLIB.POMRCH where pordat={$datex} and postat=6";
		
		$statement = $this->dbh->prepare($query);
		$statement->execute();
		$result  = $statement->fetchAll();
		
		foreach ($result as $key => $value) {
			
			$invoice = $value['POLADG'];
			$porcv  = $value['POMRCV'];

		fputs($dataFile,"1,$datetrn,KR,R200,$datetrn,$fiscalx,PHP,,$filename,Invoices for the Day - $invoice ,X\n");


		$this->dbh = new PDO($this->connectionString(),"","");

	 	$query = "select povnum,poladg,porvcs,poloc,ponumb from MMFMSLIB.POMRCH where pomrcv=$porcv";
		
		$statement = $this->dbh->prepare($query);
		$statement->execute();
		$result2  = $statement->fetchAll();

			foreach ($result2 as $key => $valuex) {
					
					$venfsp = $valuex['POVNUM'];


			
				$this->dbh = new PDO($this->connectionString(),"","");

			 	$query = "select asnum,asname,astaxc from MMFMSLIB.APSUPP where asnum=$venfsp";
				
				$statement = $this->dbh->prepare($query);
				$statement->execute();
				$result3  = $statement->fetchAll();		


				foreach ($result3 as $key => $valuexx) {

					$vatflag = $valuexx['ASTAXC'];
					$venname = $valuexx['ASNAME'];
					
					$vendormap = $this->db->query("SELECT * FROM vendormap where fspvencode= {$venfsp} ");
					foreach ($vendormap->result_array() as $row)
					{
					   $sapven = $row['sapvencode'];
					   $merch= $valuex['PORVCS'];
					   $storecd=$valuex['POLOC'];
					   $ponumber=$valuex['PONUMB'];
					   
					   $cstcenter=$store["$storecd"];
						//$datetrnz=str_replace("-","",$row["mydate"]);

						//$len=strlen($datetrnz);
						//if($len < 4 or $len == 0 or $datetrnz == "")
						//	return 0;
						//$yy=substr($datetrnz,$len-4,4);
						//$mo=substr($datetrnz,$len-6,2);
						//$day=substr($datetrnz,$len-8,2);

				$datetrnx="20$datex";

				if ($vatflag =='N') {

						$suppamt=0;
						$vatamt=$merch * .12;
						$totpo=$merch;
						$totpox=$merch;

				}else{

						$suppamt=0;
						$totpo=$merch / 1.12;
						$vatamt=$merch - $totpo;
						$totpox=$merch;
				}

				//insert all 40  - DR

				            fputs($dataFile,"2,,,,,,,,,,,40,51012101,$merch,$merch,$cstcenter,$ponumber,,,P1,,,$cstcenter,,\n");
				            fputs($dataFile,"2,,,,,,,,,,,40,11954101,$vatamt,$vatamt,$cstcenter,$ponumber,,,,,,,,\n");
				//            fputs($dataFile,"2,,,,,,,,,,,40,11401102,$suppamt,$suppamt,$cstcenter,$datetrnx,,,,,,,,\n");

				//insert all 31  - CR

							fputs($dataFile,"2,,,,,,,,,,,31,$sapven,$totpo,$totpo,$cstcenter,$ponumber,,,P1,,,$cstcenter,,\n");

					}

				}
				}	

		}
		
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
		$date = new DateTime($this->input->post('datefilter1'));
		$format_date_from = $date->format("ymd");
		$frmt_date_from = "$format_date_from"; 
		$datefrom =  $frmt_date_from;

		$date = new DateTime($this->input->post('datefilter2'));
		$format_date_from = $date->format("ymd");
		$frmt_date_from = "$format_date_from"; 
		$dateto =  $frmt_date_from;

		$data['result']	   = $this->search_model->consignment($datefrom, $dateto);
		$data['pagetitle'] = 'Consignment Sales';
		$this->load->view('templates/consignment', $data);
	}

	public function fdate()
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

}