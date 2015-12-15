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
	public function odbcConnect()
	{
		return odbc_connect("Driver={iSeries Access ODBC Driver};SYSTEM=172.16.1.9;DATABASE=MMFMSLIB;", 'DCLACAP', 'PASSWORD') or die('error');
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

	public function getTotalResult($search)
	{
		# Select location from mysql database
		$query  = $this->db->query("SELECT * FROM sa_pcostat");
		$get_result  = $query->result();
		
		$location = array();
		$currdate = array();

		foreach($get_result as $key => $value)
			{
				$location[] = $value->sa_location; #sa_location
				$currdate[] = $value->sa_curr_date; #sa_current data
			}


		$this->dbh = new PDO($this->connectionString(),"","");

	 	$query = "select b.strnum,b.strnam,sum(case when csdtyp in ('00','ZZ') then csdamt else 0 end),sum(case when a.csdtyp='VE' then a.csdamt else 0 end),sum(case when a.csdtyp='10' then a.csdamt else 0 end) 
          	      from MMFMSLIB.CSHTND a inner join MMFMSLIB.TBLSTR b on a.csstor=b.strnum
          	      where a.csdate = $search group by b.strnam,b.strnum order by b.strnam ";
  

		$statement = $this->dbh->prepare($query);
		$statement->execute();
		$result  = $statement->fetchAll();
		return $result;
	}

	public function mod_matched()
	{
		
		set_time_limit(0);
		$query = $this->db->query("SELECT PONO, NEWAMT from sa_pcostat  where status = 1");
		$res = $query->result();

			$po = array();
			$amount = array();

			foreach ($res as $key => $ponumb) 
			{
				$po[] =  $ponumb->PONO;
				$amt[] =  $ponumb->NEWAMT;
			}

			$query = $this->db->query("SELECT PONO from sa_pcostat where STATUS != 3");
			$res = $query->result();
			$getallpo = array();

			foreach ($res as $key => $ponumb)
		 	 {
			   $getallpo[] =  $ponumb->PONO;
			 }

			$this->dbh = new PDO($this->connectionString(),"","");
		
			$query = 'select ponumb,poloc,pordat,pomrcv,porvcs,poladg,poshpr,asname,astrms
                      from MMFMSLIB.POMRCH inner join  MMFMSLIB.APSUPP on povnum=asnum
                      where ponumb in('.implode(',', $getallpo).')
                      order by ponumb desc ';	
		
			$statement = $this->dbh->prepare($query);
			$statement->execute();
			$result_get_po  = $statement->fetchAll();

			
			
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

	
		$today=date("Ymd");
			
		$output_dir="csv.docs\\";
		// open a datafile
		$i = '001';
		$filename = "$today-".$i.".csv";
		$dataFile = fopen($_SERVER['DOCUMENT_ROOT'] . "/storeaccounting/csv.docs/".$filename,'w');
		$DSN = "DRIVER=iSeries Access ODBC Driver;SYSTEM=172.16.1.9;DBQ=MMFMSLIB";
		$AS400 = odbc_connect($DSN, 'DCLACAP', 'PASSWORD');
		#$AS400 = odbc_connect("Driver={iSeries Access ODBC Driver};SYSTEM=172.16.1.9;DATABASE=MMFMSLIB;", 'DCLACAP', 'PASSWORD');
		fputs($dataFile,"IND, BLDAT, BLART, BUKRS,BUDAT,MONAT,WAERS,KURSF,XBLNR,SGTXT,CTAX,BSCHL,HKONT,DMBTR,WMBTR,PRCTR,ZUONR,HBNK,ACCID,MWSKZ,VALDT,ITTXT,KOSTL,WBSEL,UMSKZ\n");
		$counter = 0;
		foreach ($result_get_po as $key => $value) {
			echo $key;
			

			$budat = $value['PORDAT'];
			$timestamp = strtotime($this->fdate($budat));
			$month = date('n', $timestamp);

			$timestamp = strtotime($this->fdate($budat));
			$year = date('Ymd', $timestamp);
			
		$sql_str="select poladg,pomrcv from MMFMSLIB.POMRCH where pordat=$budat and postat=6";


	 	    $details = odbc_exec($AS400,$sql_str);
	 		while (odbc_fetch_row($details)) {

	 		$invoice= odbc_result($details,1);
	 		$porcv= odbc_result($details,2);

			$fiscalx = $month;
			$datetrn = $year;
			$bldat = date('Ymd');
			$xblnr = substr($filename,0, -8);
			$sgtxt = substr($filename,0, -8);
			$xxx = $counter;

		fputs($dataFile,"1,$bldat,KR,R400,$datetrn,$fiscalx,PHP,,R400000$xblnr,$sgtxt-$invoice,X\n");


		$sqlStr="select povnum,poladg,porvcs,poloc,ponumb from MMFMSLIB.POMRCH where pomrcv=$porcv";


	 	    $detailx= odbc_exec($AS400,$sqlStr);
	 		while (odbc_fetch_row($detailx)) {

				$venfsp=odbc_result($detailx,1);

				$sqlStrv="select asnum,asname,astaxc from MMFMSLIB.APSUPP where asnum=$venfsp";
	 	    	$detailv= odbc_exec($AS400,$sqlStrv);
	 			odbc_fetch_row($detailv);

				$vatflag=odbc_result($detailv,3);
				$venname=odbc_result($detailv,2);
		//$sapven=odbc_result($detailv,1)+ 60000000;


		$db1 = mysqli_connect('localhost', 'root', '', 'storeaccounting_db');

		$sql = "SELECT * FROM sa_vendormap where fspvencode=$venfsp";
		$results = mysqli_query($db1, $sql)or die("MySQL error: " . mysqli_error($db1) . "<hr>\nQuery: $sql");  ;

		$row_vlist = mysqli_fetch_array($results,MYSQLI_ASSOC);
		$sapven=$row_vlist['sapvencode'];

		$merch=odbc_result($detailx,3);
		$storecd=odbc_result($detailx,4);
		$ponumber=odbc_result($detailx,5);
		$cstcenter=$store["$storecd"];
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
//fputs($dataFile,"</ItemHierarchyView>\n");
		}
		
		
	}
}
			$poimplode = implode(',', $getallpo);
			$query = $this->db->query('UPDATE sa_pcostat SET STATUS = 3  where PONO in('.$poimplode.')');

	}

	public function mod_exception()
	{
		set_time_limit(0);

		$query = $this->db->query("SELECT PONO, NEWAMT from sa_pcostat  where status = 1");
		$res = $query->result();

			$po = array();
			$amount = array();

			foreach ($res as $key => $ponumb) 
			{
				$po[] =  $ponumb->PONO;
				$amt[] =  $ponumb->NEWAMT;
			}
			$query = $this->db->query("SELECT PONO from sa_pcostat WHERE STATUS != 3");
			$res = $query->result();
			$getallpo = array();

			foreach ($res as $key => $ponumb)
		 	 {
			   $getallpo[] =  $ponumb->PONO;
			 }

			$this->dbh = new PDO($this->connectionString(),"","");

		
			$query = 'select ponumb,poloc,pordat,pomrcv,porvcs,poladg,poshpr,asname,astrms
                      from MMFMSLIB.POMRCH inner join  MMFMSLIB.APSUPP on povnum=asnum
                      where ponumb in('.implode(',', $getallpo).')
                      order by ponumb desc ';	
		
			$statement = $this->dbh->prepare($query);
			$statement->execute();
			$result_get_po  = $statement->fetchAll();

	
			

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

	
		$today=date("Ymd");
			
		$output_dir="csv.docs\\";
		// open a datafile
		$i = '001';
		$filename = "$today-".$i.".csv";
		$dataFile = fopen($_SERVER['DOCUMENT_ROOT'] . "/storeaccounting/csv.docs/".$filename,'w');
		$DSN = "DRIVER=iSeries Access ODBC Driver;SYSTEM=172.16.1.9;DBQ=MMFMSLIB";
		$AS400 = odbc_connect($DSN,'DCLACAP','PASSWORD');
		fputs($dataFile,"IND, BLDAT, BLART, BUKRS,BUDAT,MONAT,WAERS,KURSF,XBLNR,SGTXT,CTAX,BSCHL,HKONT,DMBTR,WMBTR,PRCTR,ZUONR,HBNK,ACCID,MWSKZ,VALDT,ITTXT,KOSTL,WBSEL,UMSKZ\n");
		$counter = 0;
		foreach ($result_get_po as $value) {

			$budat = $value['PORDAT'];
			$timestamp = strtotime($this->fdate($budat));
			$month = date('n', $timestamp);

			$timestamp = strtotime($this->fdate($budat));
			$year = date('Ymd', $timestamp);
			
		$sql_str="select poladg,pomrcv from MMFMSLIB.POMRCH where pordat=$budat and postat=6";


	 	    $details = odbc_exec($AS400,$sql_str);
	 		while (odbc_fetch_row($details)) {

	 		$invoice= odbc_result($details,1);
	 		$porcv= odbc_result($details,2);

			$fiscalx = $month;
			$datetrn = $year;
			$bldat = date('Ymd');
			$xblnr = substr($filename,0, -8);
			$sgtxt = substr($filename,0, -8);
			$xxx = $counter;

		fputs($dataFile,"1,$bldat,KR,R400,$datetrn,$fiscalx,PHP,,R400000$xblnr,$sgtxt-$invoice,X\n");


		$sqlStr="select povnum,poladg,porvcs,poloc,ponumb from MMFMSLIB.POMRCH where pomrcv=$porcv";


	 	    $detailx= odbc_exec($AS400,$sqlStr);
	 		while (odbc_fetch_row($detailx)) {

				$venfsp=odbc_result($detailx,1);

				$sqlStrv="select asnum,asname,astaxc from MMFMSLIB.APSUPP where asnum=$venfsp";
	 	    	$detailv= odbc_exec($AS400,$sqlStrv);
	 			odbc_fetch_row($detailv);

				$vatflag=odbc_result($detailv,3);
				$venname=odbc_result($detailv,2);
		//$sapven=odbc_result($detailv,1)+ 60000000;


		$db1 = mysqli_connect('localhost', 'root', '', 'storeaccounting_db');

		$sql = "SELECT * FROM sa_vendormap where fspvencode=$venfsp";
		$results = mysqli_query($db1, $sql)or die("MySQL error: " . mysqli_error($db1) . "<hr>\nQuery: $sql");  ;

		$row_vlist = mysqli_fetch_array($results,MYSQLI_ASSOC);
		$sapven=$row_vlist['sapvencode'];

		$merch=odbc_result($detailx,3);
		$storecd=odbc_result($detailx,4);
		$ponumber=odbc_result($detailx,5);
		$cstcenter=$store["$storecd"];

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

//fputs($dataFile,"</ItemHierarchyView>\n");


		}
		
		
		}
	}
		$poimplode = implode(',', $getallpo);
		$query = $this->db->query('UPDATE sa_pcostat SET status = 3  where PONO in('.$poimplode.')');	
	}

	public function mod_payables($date)
	{
		$query = $this->db->query("SELECT PONO from sa_pcostat");
		$res = $query->result();
		
		$po = array();
		foreach ($res as $key => $ponumb) {
			$po[] =  $ponumb->PONO;
		}

		if(count($res)!= 0){
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
		else
		{
		$this->dbh = new PDO($this->connectionString(),"","");

		$query = 'select ponumb,poloc,pordat,pomrcv,porvcs,poladg,poshpr,asname,astrms
                  from MMFMSLIB.POMRCH inner join  MMFMSLIB.APSUPP on povnum=asnum
                  where pordat = '.$date.'
                  order by ponumb desc';	
		$statement = $this->dbh->prepare($query);
		$statement->execute();
		$result  = $statement->fetchAll();
		return $result;
	}	
	}

	public function consignment($datefrom, $dateto)
	{
		$this->dbh = new PDO($this->connectionString(),"","");

		$query = "select c.asname as vendor,sum(a.csexpr) as totalsales 
				 from MMFMSLIB.cshdet a inner join MMFMSLIB.invmst b
				  on a.cssku=b.inumbr inner join MMFMSLIB.apsupp c 
				  on c.asnum=b.asnum where a.cscen=1 and csdate 
				  between {$datefrom} and {$dateto} and b.istype='CC' 
				  group by c.asname";
				  
		$statement = $this->dbh->prepare($query);
		$statement->execute();
		$result  = $statement->fetchAll();
		return $result;		  
	}

	public function exportConsignment($datefrom, $dateto)
	{
		$this->dbh = new PDO($this->connectionString(),"","");
		$query = "select c.asname as vendor,sum(a.csexpr) as totalsales 
				 from MMFMSLIB.cshdet a inner join MMFMSLIB.invmst b
				  on a.cssku=b.inumbr inner join MMFMSLIB.apsupp c 
				  on c.asnum=b.asnum where a.cscen=1 and csdate 
				  between {$datefrom} and {$dateto} and b.istype='CC' 
				  group by c.asname";
				  
		$statement = $this->dbh->prepare($query);
		$statement->execute();
		$result  = $statement->fetchAll();

		$today=date("Ymd");
			
		$output_dir="csv.docs\\";
		// open a datafile
		$filename = "CONSIGNMENTSALES_"."$today".".csv";
		$dataFile = fopen($_SERVER['DOCUMENT_ROOT'] . "/storeaccounting/csv.docs/".$filename,'w');

		fputs($dataFile,"VENDOR,TOTALSALES\n");
		foreach ($result as $key => $value) {
			$vendor = trim($value['VENDOR']);
			$t_sales = $value['TOTALSALES'];
			fputs($dataFile,"\"$vendor\",\"$t_sales\"\n");
		}
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
}