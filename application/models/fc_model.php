<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Fc_model extends CI_Model {

	public function connectionString()
	{
		return "odbc:DRIVER={iSeries Access ODBC Driver}; ".
				"SYSTEM=172.16.1.9; ".
				"DATABASE=MMFMSLIB; ".
				"UID=DCLACAP; ".
				"PWD=PASSWORD";
	}

	public function mod_fcmatched()
	{
		set_time_limit(0);

		#get all the records from payables fc table

		$query = $this->db->query("SELECT PONO from sa_pfcstat where STATUS  IN (1,2)");
		$result = $query->result();
				
		$getallpo = array();

		foreach ($result as $key => $value) {
			$getallpo[] = $value->PONO;
		}

		$implode_po = implode(',', $getallpo);

		$this->dbh = new PDO($this->connectionString(),"","");
		
			$query = "select ponumb,poloc,pordat,pomrcv,porvcs,poladg,poshpr,asname,astrms
                      from MMFMSLIB.POMRCH inner join  MMFMSLIB.APSUPP on povnum=asnum
                      where ponumb in($implode_po)
                      order by ponumb desc";	
			
			$statement = $this->dbh->prepare($query);
			$statement->execute();
			$result_get_po = $statement->fetchAll();

			foreach ($result_get_po as $key => $value) {
				echo $value['ponumb'];
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