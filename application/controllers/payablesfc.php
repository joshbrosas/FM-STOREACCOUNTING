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
	}

	public function index()
	{
		$this->dbh = new PDO("odbc:Driver={SQL Server};Server=54.215.2.91;Database='ansilive'; Uid=pfmadmin;Pwd=M@nager3971;");
		#$as400 = odbc_connect("Driver={iSeries Access ODBC Driver};SYSTEM=172.16.1.9;DATABASE=MMFMSLIB;", 'DCLACAP', 'PASSWORD') or die('error');
		

	}
}