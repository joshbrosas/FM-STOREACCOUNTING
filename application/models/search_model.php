<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Search_model extends CI_Model {

	public function connectionString()
	{
		return "odbc:DRIVER={iSeries Access ODBC Driver}; ".
				"SYSTEM=172.16.1.9; ".
				"DATABASE=MMFMSLIB; ".
				"UID=DCLACAP; ".
				"PWD=PASSWORD";
	}

	public function getResult($search)
	{	
		$this->dbh = new PDO($this->connectionString(),"","");

	 	$query = "select b.strnam,a.csreg,sum(case when csdtyp in ('00','ZZ') then csdamt else 0 end),sum(case when a.csdtyp='VE' then a.csdamt else 0 end),sum(	  case when a.csdtyp='10' then a.csdamt else 0 end)   
          	      from MMFMSLIB.CSHTND a inner join MMFMSLIB.TBLSTR b on a.csstor=b.strnum
          	      where a.csdate = {$search} group by b.strnam,a.csreg order by b.strnam";
		
		$statement = $this->dbh->prepare($query);
		$statement->execute();
		$result  = $statement->fetchAll();
		return $result;
	}

	public function mod_matched()
	{
		set_time_limit(0);
		$query = $this->db->query("SELECT po_no from payables_status");
		$res = $query->result();
		$getallpo = array();
		foreach ($res as $key => $ponumb) {
			$getallpo[] =  $ponumb->po_no;
		}

		$this->dbh = new PDO($this->connectionString(),"","");


			$query = 'select ponumb,poloc,pordat,pomrcv,porvcs,poladg,poshpr,asname,astrms
                      from MMFMSLIB.POMRCH inner join  MMFMSLIB.APSUPP on povnum=asnum
                      where  ponumb in('.implode(',', $getallpo).')
                      order by ponumb desc';	
		

		$statement = $this->dbh->prepare($query);
		$statement->execute();
		$result  = $statement->fetchAll();

		$output_dir="csv.docs\\";
		$todayz=date("mdY",strtotime('+8 hours'));
		$filename = "TWOWAYPROCESS"."$todayz".".csv";
		$dataFile = fopen($output_dir.$filename,'w');
		fputs($dataFile,"\"IND\",\"BLDAT\",\"BLART\",\"BUKRS\",\"BUDAT\",\"MONAT\",\"WAERS\",\"KURSF\",\"XBLNR\",\"SGTXT\",\"CTAX\",\"BSCHL\",\"HKONT\",\"DMBTR\",\"WMBTR\",\"PRCTR\",\"ZUONR\",\"HBNK\",\"ACCID\",\"MWSKZ\",\"VALDT\",\"ITTXT\",\"KOSTL\",\"WBSEL\",\"UMSKZ\"\n");

		foreach ($result as $value) {

			$ind   = "";
			$bldat = "";
			$blart = "";
			$bukrs = "";
			$budat = $value['PORDAT'];
			$monat = "";
			$waers = "";
			$kursf = "";
			$xblnr = "";
			$sgtxt = "";
			$ctax  = "";
			$bschl = "";
			$hkont = "";
			$dmbtr = "";
			$wmbtr = "";
			$prctr = "";
			$zuonr = "";
			$hbnk  = "";
			$accid = "";
			$mwskz = "";
			$valdt = "";
			$ittxt = "";
			$kostl = "";
			$wbsel = "";
			$umskz = "";
			
			fputs($dataFile,"\"$ind\",\"$bldat\",\"$blart\",\"$bukrs\",\"$budat\",\"$monat\",\"$waers\",\"$kursf\",\"$xblnr\",\"$sgtxt\",\"$ctax\",\"$bschl\",\"$hkont\",\"$dmbtr\",\"$wmbtr\",\"$prctr\",\"$zuonr\",\"$hbnk\",\"$accid\",\"$mwskz\",\"$valdt\",\"$ittxt\",\"$kostl\",\"$wbsel\",\"$umskz\"\n");
		}
		$poimplode = implode(',', $getallpo);
		$query = $this->db->query('UPDATE payables_status SET status = 3  where po_no in('.$poimplode.')');
		
	}

	public function mod_exception()
	{
		set_time_limit(0);
		$query = $this->db->query("SELECT po_no, new_amount from payables_status  where status = 1");
		$res = $query->result();

			$po = array();
			$amount = array();

			foreach ($res as $key => $ponumb) 
			{
				$po[] =  $ponumb->po_no;
				$amt[] =  $ponumb->new_amount;
			}

			$query = $this->db->query("SELECT po_no from payables_status");
			$res = $query->result();
			$getallpo = array();
		foreach ($res as $key => $ponumb)
		 	{
			$getallpo[] =  $ponumb->po_no;
			}

			$this->dbh = new PDO($this->connectionString(),"","");

		
			$query = 'select ponumb,poloc,pordat,pomrcv,porvcs,poladg,poshpr,asname,astrms
                      from MMFMSLIB.POMRCH inner join  MMFMSLIB.APSUPP on povnum=asnum
                      where ponumb in('.implode(',', $getallpo).')
                      order by ponumb desc ';	
		
			$statement = $this->dbh->prepare($query);
			$statement->execute();
			$result  = $statement->fetchAll();

			$output_dir="csv.docs\\";
			$todayz=date("mdY",strtotime('+8 hours'));
			$filename = "TWOWAYPROCESS"."$todayz".".csv";
			$dataFile = fopen($output_dir.$filename,'w');
			fputs($dataFile,"\"IND\",\"BLDAT\",\"BLART\",\"BUKRS\",\"BUDAT\",\"MONAT\",\"WAERS\",\"KURSF\",\"XBLNR\",\"SGTXT\",\"CTAX\",\"BSCHL\",\"HKONT\",\"DMBTR\",\"WMBTR\",\"PRCTR\",\"ZUONR\",\"HBNK\",\"ACCID\",\"MWSKZ\",\"VALDT\",\"ITTXT\",\"KOSTL\",\"WBSEL\",\"UMSKZ\"\n");

			foreach ($result as $value) {
				$ind   = "";
				$bldat = "";
				$blart = "";
				$bukrs = "";
				$budat = $value['PORDAT'];
				$monat = "";
				$waers = "";
				$kursf = "";
				$xblnr = "";
				$sgtxt = "";
				$ctax  = "";
				$bschl = "";
				$hkont = "";
				$dmbtr = "";
				$wmbtr = "";
				$prctr = "";
				$zuonr = "";
				$hbnk  = "";
				$accid = "";
				$mwskz = "";
				$valdt = "";
				$ittxt = "";
				$kostl = "";
				$wbsel = "";
				$umskz = "";
			
			fputs($dataFile,"\"$ind\",\"$bldat\",\"$blart\",\"$bukrs\",\"$budat\",\"$monat\",\"$waers\",\"$kursf\",\"$xblnr\",\"$sgtxt\",\"$ctax\",\"$bschl\",\"$hkont\",\"$dmbtr\",\"$wmbtr\",\"$prctr\",\"$zuonr\",\"$hbnk\",\"$accid\",\"$mwskz\",\"$valdt\",\"$ittxt\",\"$kostl\",\"$wbsel\",\"$umskz\"\n");
		}
			$poimplode = implode(',', $getallpo);
			$query = $this->db->query('UPDATE payables_status SET status = 3  where po_no in('.$poimplode.')');
	}


	public function mod_payables($date)
	{
		$query = $this->db->query("SELECT po_no from payables_status");
		$res = $query->result();
		
		$po = array();
		foreach ($res as $key => $ponumb) {
			$po[] =  $ponumb->po_no;
		}

		$this->dbh = new PDO($this->connectionString(),"","");

		$query = 'select ponumb,poloc,pordat,pomrcv,porvcs,poladg,poshpr,asname,astrms
                  from MMFMSLIB.POMRCH inner join  MMFMSLIB.APSUPP on povnum=asnum
                  where pordat = '.$date.' and ponumb not in('.implode(',', $po).')
                  order by ponumb desc';	
		$statement = $this->dbh->prepare($query);
		$statement->execute();
		$result  = $statement->fetchAll();
		return $result;
	}
}