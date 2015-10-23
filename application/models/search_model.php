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
		$as400 = odbc_connect("Driver={iSeries Access ODBC Driver};SYSTEM=172.16.1.9;DATABASE=MMFMSLIB;", 'DCLACAP', 'M@nager3971') or die('error');

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
			$result_get_po  = $statement->fetchAll();

			$poimplode = implode(',', $getallpo);
			$query = $this->db->query('UPDATE payables_status SET status = 3  where po_no in('.$poimplode.')');
			

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
		$i = $i + 1;
		$filename = "$today-".$i.".csv";
		$dataFile = fopen($output_dir.$filename,'w');
		$datetrnx=str_replace("-","",$datex);

		fputs($dataFile,"IND, BLDAT, BLART, BUKRS,BUDAT,MONAT,WAERS,KURSF,XBLNR,SGTXT,CTAX,BSCHL,HKONT,DMBTR,WMBTR,PRCTR,ZUONR,HBNK,ACCID,MWSKZ,VALDT,ITTXT,KOSTL,WBSEL,UMSKZ\n");
		#fputs($dataFile,"Indicator,Document Date,Document Type,Company Code,Posting Date,Fiscal Period,Currency Key,Exchange Rate,Reference Document Number,Document Header Text,Calculate tax,Posting Key,Account, Amount in document currency ,Amount in local currency,Profit Center,Assignment Number,House Bank,Account ID ,Tax Code,Value Date,Item Text,Cost Center,WBS Element,Special GL\n");
		foreach ($result_get_po as $value) {

			$budat = $value['PORDAT'];
			$timestamp = strtotime($this->fdate($budat));
			$month = date('n', $timestamp);

			$timestamp = strtotime($this->fdate($budat));
			$year = date('Ymd', $timestamp);
			
		$this->dbh = new PDO($this->connectionString(),"","");
	 	$query = "select poladg,pomrcv from MMFMSLIB.POMRCH where pordat={$budat} and postat=6";
		$statement = $this->dbh->prepare($query);
		$statement->execute();
		$result  = $statement->fetchAll();

		$counter = 0;
		foreach ($result as $key => $value) {
			
			$counter++;

			$invoice = $value['POLADG'];
			$porcv  = $value['POMRCV'];
			$fiscalx = $month;
			$datetrn = $year;
			$xblnr = substr($filename,0, -6);
			$sgtxt = substr($filename,0, -6);
			$xxx = $counter;
		fputs($dataFile,"1,$datetrn,KR,R400,$datetrn,$fiscalx,PHP,,Invoice-$xblnr,$sgtxt-$xxx,X\n");


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

				            fputs($dataFile,"2,,,,,,,,,,,40,51012101,$merch,$merch,$cstcenter,$ponumber,,,,,,$cstcenter,,\n");
				            fputs($dataFile,"2,,,,,,,,,,,40,11954101,$vatamt,$vatamt,$cstcenter,$ponumber,,,,,,,,\n");
				//            fputs($dataFile,"2,,,,,,,,,,,40,11401102,$suppamt,$suppamt,$cstcenter,$datetrnx,,,,,,,,\n");

				//insert all 31  - CR

							fputs($dataFile,"2,,,,,,,,,,,31,$sapven,$totpo,$totpo,$cstcenter,$ponumber,,,,,,$cstcenter,,\n");

					}

				}
				}	

		}
		}
		
		
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
			$result_get_po  = $statement->fetchAll();

			$poimplode = implode(',', $getallpo);
			$query = $this->db->query('UPDATE payables_status SET status = 3  where po_no in('.$poimplode.')');
			

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
		$store['20']   ="R2211022";
		$store['9005'] ="R1009005";

	
		$today=date("Ymd");
			
		$output_dir="csv.docs\\";
		// open a datafile
		$i = $i + 1;
		$filename = "$today-".$i.".csv";
		$dataFile = fopen($output_dir.$filename,'w');
		$datetrnx=str_replace("-","",$datex);

		fputs($dataFile,"IND, BLDAT, BLART, BUKRS,BUDAT,MONAT,WAERS,KURSF,XBLNR,SGTXT,CTAX,BSCHL,HKONT,DMBTR,WMBTR,PRCTR,ZUONR,HBNK,ACCID,MWSKZ,VALDT,ITTXT,KOSTL,WBSEL,UMSKZ\n");
		#fputs($dataFile,"Indicator,Document Date,Document Type,Company Code,Posting Date,Fiscal Period,Currency Key,Exchange Rate,Reference Document Number,Document Header Text,Calculate tax,Posting Key,Account, Amount in document currency ,Amount in local currency,Profit Center,Assignment Number,House Bank,Account ID ,Tax Code,Value Date,Item Text,Cost Center,WBS Element,Special GL\n");
		foreach ($result_get_po as $value) {

			#convert to numeric month
			$budat = $value['PORDAT'];
			$timestamp = strtotime($this->fdate($budat));
			$month = date('n', $timestamp);

			#convert to YYYY/mm/dd
			$timestamp = strtotime($this->fdate($budat));
			$year = date('Ymd', $timestamp);
			
		$this->dbh = new PDO($this->connectionString(),"","");
	 	$query = "select poladg,pomrcv from MMFMSLIB.POMRCH where pordat={$budat} and postat=6";
		$statement = $this->dbh->prepare($query);
		$statement->execute();
		$result  = $statement->fetchAll();

		foreach ($result as $key => $value) {
			
			$invoice = $value['POLADG'];
			$porcv  = $value['POMRCV'];
			$fiscalx = $month;
			$datetrn = $year;
			fputs($dataFile,"1,$datetrn,KR,R400,$datetrn,$fiscalx,PHP,,$filename,Invoices for the Day - $invoice ,X\n");


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
			
	}

	public function mod_payables($date)
	{
		$query = $this->db->query("SELECT po_no from payables_status");
		$res = $query->result();
		
		$po = array();
		foreach ($res as $key => $ponumb) {
			$po[] =  $ponumb->po_no;
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
		$dataFile = fopen($output_dir.$filename,'w');

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