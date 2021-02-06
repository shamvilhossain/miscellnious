<?php
class user_model extends CI_Model {

    function __construct()
    {
        parent::__construct();
	}
	function login($userid,$pass)
	{	
		$this->db
			->select("u.*, DATEDIFF(u.pass_expiry_date,CURRENT_DATE()) as expiry_days, b.IP_3octet, d.name as dename, b.name as branch_name, w.name as  working_group_name", FALSE)
			->from('b2_user_info as u')
			->join("b2_branch as b", "u.branch_id=b.id", "left")			
			->join("b2_designation as d", "u.designtion_id=d.id", "left")
			->join("b2_working_group as w", "u.work_group_id=w.id", "left")
			->where(array('employee_ID' => $userid,'pass' => $pass))
			->where(" (u.sts='1' or (u.sts='0' and u.id='1'))  ", NULL, FALSE);
		$data = $this->db->get()->row();
		return $data;	
	}
	function get_user_info($userid,$email)
	{	
		$data = $this->db->get_where('b2_user_info',array('employee_ID' => $userid,'email' => $email,'sts' =>1));		
		return $data->result();	
	}
	function get_parameter_name_data_operation_zone($table,$where=NULL)
	{
	     $this->db->select('*',FALSE);
		 $this->db->from($table);
		 if(!empty($where)) $this->db->where($where, NULL, FALSE);
		 $q=$this->db->get();
		 return $q->row();
	}
	function get_user_actual_id($userid)
	{		
		$this->db
			->select("* ", FALSE)
			->from('b2_user_info')
			->where(array('employee_ID' => $userid));
		$data = $this->db->get()->row();
		return $data;	
	}

	// 16 nov

	//function get_deal_refno($table=NULL,$field_counter=NULL,$field=NULL,$e_dt=NULL,$ref_id=NULL,$where=" ",$u=NULL)
	function get_deal_refno_old($table=NULL,$field_counter=NULL,$field=NULL,$id=NULL)
	{

		$prffix='';
		$unit='';
		$maxlength=0;
		$str1 = "select * from b2_dealtype where id='".$id."' limit 1";
		$query=$this->db->query($str1);
		foreach( $query->result() as $row){
				$prffix=$row->code;
			}

		// $str1 = "select * from ref_unit where id='".$u."' limit 1";
		// $query=$this->db->query($str1);
		// foreach( $query->result() as $row){
		// 		$unit=$row->name;
		// 	}

		$maxc=$this->random_sleep();
 		$max_counter=1;
 		
			$str = "SELECT MAX($field_counter) as max_counter FROM $table
				where id='".$id."' and sts='1' ";
				
		
		$max_counter=$this->db->query($str)->row()->max_counter;
	
		//echo $this->db->last_query();exit;
		$max_counter=$max_counter+1;
		//echo $max_counter;exit;
		

		$MaxrefNo=strtoupper($prffix.'-'.date('Y').'-'.date('m').'-'.$max_counter);
		
		// $str = "SELECT * FROM $table
		// 		where $field='".$MaxrefNo."' ".$where." and sts='1' ";
		// $no=$this->db->query($str)->num_rows();

		// if($no>0){ $this->get_bill_refno($table,$field_counter,$field,$e_dt,$ref_id,$where);}

		$ref[0]=$max_counter;
		$ref[1]=$MaxrefNo;
		//echo $ref[1];exit;
		return $ref;


	}

	function get_deal_refno_dec($dealtype_id=NULL, $year=NULL)
	{
		$max = 1; 
		$min = 0.015;
		$time = $min + mt_rand() / mt_getrandmax() * ($max - $min);
		sleep($time);
		
		if(empty($year)){$year=date("Y");}

		$prffix='';
		$MaxrefNo='';
		$unit='';
		$maxlength=0;
		$str1 = "select * from b2_dealtype where id='".$dealtype_id."' limit 1";
		$query=$this->db->query($str1);
		foreach( $query->result() as $row){
				$prffix=$row->code;
			}

		$max_counter=1;
		$max_counter_str='';
 		
			$str = "SELECT MAX(Serial) as max_counter FROM b2_fex_deal
				where DealTypeId='".$dealtype_id."' and YEAR(e_dt)='".$year."' and (sts>=1 || (sts=0 and fv_by!='')) ";
				
		$max_counter=$this->db->query($str)->row()->max_counter;

		if($max_counter==''){
			$max_counter=1;
			$max_counter_str='0001';
		}else{
			$max_counter= $max_counter+1;
			if(strlen($max_counter)==1){$max_counter='000'.$max_counter;}else if(strlen($max_counter)==2){$max_counter='00'.$max_counter;}else if(strlen($max_counter)==3){$max_counter='0'.$max_counter;}else{$max_counter=$max_counter;}
			$max_counter_str=$max_counter;
		}
		
		$MaxrefNo=strtoupper($prffix.'-'.date('Y').'-'.date('m').'-'.$max_counter_str);	
		
		//$prefix=$code.$year.'/';
		
		
		// $str = "SELECT MAX(SUBSTR(RegistrationNumber,1)) AS maxReqNo  
		// 		FROM b2_spbuy WHERE LENGTH(SUBSTR(RegistrationNumber,1))=(SELECT MAX(LENGTH(SUBSTR(RegistrationNumber,1))) 
		// 		FROM b2_spbuy WHERE RegistrationNumber LIKE '".$prefix."%' and sp_categoryId='".$cat."' 
		// 		and StockId='".$this->session->userdata['user']['user_zone_id']."'  and Status='1') 
		// 		and StockId='".$this->session->userdata['user']['user_zone_id']."'  and Status='1'
		// 		AND RegistrationNumber LIKE '".$prefix."%' and sp_categoryId='".$cat."'"; 
				
		// $query=$this->db->query($str);
		// $result1=$query->result();	
		
		
		// foreach ($result1 as $row_1)
		// {
		// 	$max_reg_no=$row_1->maxReqNo;
		// 	if($max_reg_no==""){$reg_no_unique=$code.$year.'/'.'0001';}
		// 	else{			
		// 		$a=explode("/",$max_reg_no); 
		// 		$aa=$a[(count($a)-1)]+1;  //1
		// 		if(strlen($aa)==1){$aa='000'.$aa;}else if(strlen($aa)==2){$aa='00'.$aa;}else if(strlen($aa)==3){$aa='0'.$aa;}else{$aa=$aa;}
		// 		$reg_no_unique=$code.$year.'/'.($aa);			
		// 	}
		// }
		
		$str = "SELECT * FROM  b2_fex_deal 
				where  fex_ref_no = '".$MaxrefNo."' and DealTypeId='".$dealtype_id."' ";
		$no=$this->db->query($str)->num_rows();
		
		if($no>0){ $this->max_reg_no_product_wise($code,$cat,$year);}
			
		return $MaxrefNo;
			 
	}

// 4 dec
	function get_deal_refno($dealtype_id=NULL, $year=NULL)
	{
		$max = 1; 
		$min = 0.015;
		$time = $min + mt_rand() / mt_getrandmax() * ($max - $min);
		sleep($time);
		
		if(empty($year)){$year=date("Y");}

		$prffix='';
		$MaxrefNo='';
		$unit='';
		$maxlength=0;
		$str1 = "select * from b2_dealtype where id='".$dealtype_id."' limit 1";
		$query=$this->db->query($str1);
		foreach( $query->result() as $row){
				$prffix=$row->code;
			}

		$max_counter=1;
		$max_counter_str='';

		// $str1 = "update b2_dealtype set ref_counter=ref_counter+1 where id='".$dealtype_id."' and sts=1";
		// $this->db->query($str1);

		
 		
			// $str = "SELECT MAX(Serial) as max_counter FROM b2_fex_deal
			// 	where DealTypeId='".$dealtype_id."' and YEAR(e_dt)='".$year."' and (sts>=1 || (sts=0 and fv_by!='')) ";
			
			$str = "SELECT count(Serial) as max_counter FROM b2_fex_deal
				where DealTypeId='".$dealtype_id."' and YEAR(e_dt)='".$year."'  ";	
		$max_counter=$this->db->query($str)->row()->max_counter;

		//if($max_counter==''){
		if($max_counter==0){
			$max_counter=1;
			$max_counter_str='0001';
		}else{
			$max_counter= $max_counter+1;
			if(strlen($max_counter)==1){$max_counter='000'.$max_counter;}else if(strlen($max_counter)==2){$max_counter='00'.$max_counter;}else if(strlen($max_counter)==3){$max_counter='0'.$max_counter;}else{$max_counter=$max_counter;}
			$max_counter_str=$max_counter;
		}
		
		//$MaxrefNo=strtoupper($prffix.'-'.date('Y').'-'.date('m').'-'.$max_counter_str);	
		$MaxrefNo=strtoupper($prffix.'-'.date('Y').'-'.$max_counter_str);	
		
		//echo $MaxrefNo;exit;
		$str = "SELECT * FROM  b2_fex_deal 
				where  fex_ref_no = '".$MaxrefNo."' and DealTypeId='".$dealtype_id."' ";
		$no=$this->db->query($str)->num_rows();
		
		if($no>0){ $this->max_reg_no_product_wise($code,$cat,$year);}
			
		return $MaxrefNo;
			 
	}

	function random_sleep()
	{
		$max = 1; 
		$min = 0.015;
		$time = $min + mt_rand() / mt_getrandmax() * ($max - $min);
		sleep($time);	
	}
	
		
	function get_link_group($user_id)
	{
		$this->db->query('SET @@group_concat_max_len = 204800');
		$str="SELECT slg.id, slg.name,slg.has_child,slg.url_prefix,
			GROUP_CONCAT(sub1.sys_link_cat_id ORDER BY sub1.s_order ASC SEPARATOR '#') AS sub1_slc_ids,
			GROUP_CONCAT(sub1.slc_Name ORDER BY sub1.s_order ASC SEPARATOR '#') AS sub1_slc_Names,
			GROUP_CONCAT(sub1.slc_has_child ORDER BY sub1.s_order ASC SEPARATOR '#') AS sub1_slc_has_childs,
			GROUP_CONCAT(sub1.slc_url_prefix ORDER BY sub1.s_order ASC SEPARATOR '#') AS sub1_slc_url_prefixs,
			
			GROUP_CONCAT(sub1.sl_ids ORDER BY sub1.s_order ASC SEPARATOR '#') AS sub1_sl_ids,
			GROUP_CONCAT(sub1.sl_operations ORDER BY sub1.s_order ASC SEPARATOR '#') AS sub1_sl_operations,
			GROUP_CONCAT(sub1.sl_url_prefixs ORDER BY sub1.s_order ASC SEPARATOR '#') AS sub1_sl_url_prefixs,
			GROUP_CONCAT(sub1.sl_names ORDER BY sub1.s_order ASC SEPARATOR '#') AS sub1_sl_names,
			GROUP_CONCAT(sub1.sl_orders ORDER BY sub1.s_order ASC SEPARATOR '#') AS sub1_sl_orders
			FROM 
				(
				SELECT slc.sys_link_group_id, slc.s_order,sl.sys_link_cat_id,
				slc.name AS slc_Name,slc.url_prefix AS slc_url_prefix,slc.has_child AS slc_has_child,
				GROUP_CONCAT( sl.id ORDER BY sl.id ASC SEPARATOR '|') AS sl_ids,
				GROUP_CONCAT( sl.operations ORDER BY sl.id ASC SEPARATOR '|') AS sl_operations,
				GROUP_CONCAT(sl.url_prefix ORDER BY sl.id ASC SEPARATOR '|') AS sl_url_prefixs,
				GROUP_CONCAT( sl.name ORDER BY sl.id ASC SEPARATOR '|') AS sl_names,
				GROUP_CONCAT( sl.s_order ORDER BY sl.id ASC SEPARATOR '|') AS sl_orders
				FROM b2_user_rights ur 
				LEFT JOIN b2_sys_links sl ON ur.sys_link_id=sl.id
				LEFT JOIN b2_sys_link_cat slc ON sl.sys_link_cat_id=slc.id
				WHERE ur.user_info_id='".$user_id."' AND sl.sts='1' AND slc.sts='1'  AND ur.sts='1' GROUP BY sl.sys_link_cat_id 
				ORDER BY slc.s_order
				) sub1 
			LEFT OUTER JOIN b2_sys_link_group slg ON sub1.sys_link_group_id=slg.id
			WHERE slg.sts='1' GROUP BY sub1.sys_link_group_id ORDER BY slg.s_order";
			$data = $this->db->query($str);
			return $data->result();
		
	}
	function get_parameter_data($table,$orderby,$where=NULL,$stock_sts=NULL)
	{
	     $this->db->select('*',FALSE);
		 $this->db->from($table);
		 if(!empty($where)) $this->db->where($where);		 
		 $this->db->order_by($orderby);
		 $q=$this->db->get();
		 return $q->result();
		 //return $this->db->last_query();
	}
	function get_parameter_name_data($table,$where=NULL)
	{
	     $this->db->select('*',FALSE);
		 $this->db->from($table);
		 if(!empty($where)) $this->db->where($where, NULL, FALSE);
		 $q=$this->db->get();
		 foreach($q->result() as $row)
		 {
		 	$name=$row->name;
		 }
		 return $name;
	}
	
	
	function get_serial($table,$field,$condition)// common funtion with delay to get serial number from table
	{
		// sleep for random seconds
		$max = 1; 
		$min = 0.015;
		$time = $min + mt_rand() / mt_getrandmax() * ($max - $min);
		//sleep($time);
		// wake up !
		$this->db->select("MAX(".$field.") as SL",FALSE);
		$this->db->from($table);
		if(!empty($condition)) $this->db->where($condition,NULL,FALSE);	 
		$q=$this->db->get();
		$result = $q->result();
		// echo $this->db->last_query();
		// exit;
		isset($result[0]->SL)?($serial = $result[0]->SL+1):($serial=1);
		//echo $serial;exit;
		return $serial;
	}
	
	function get_client_info()
	{
		$str = "select * from b2_client_info where sts='1' limit 1 "; 
		$query=$this->db->query($str);
		return $query->row();
	}
	
	function dtf($date,$expect_sign)
	 {
		if($expect_sign=='-'){$org_sign='/';}else{$org_sign='-';}
		if(!empty($date)){
			$var=explode($org_sign,$date);
			if(count($var)==3){
				return $var[2].$expect_sign.$var[1].$expect_sign.$var[0];
			}
		}
	 }
	 function dtf2($date,$expect_sign)
	 {
		if($expect_sign=='-'){$org_sign='/';}else{$org_sign='-';}
		if(!empty($date)){
			$var=explode($org_sign,$date);
			if(count($var)==3){
				// if(strlen($var[2]!=4 && strlen($var[1]!=2 && strlen($var[0]!=2))
				// 	return;
				return $var[2].$expect_sign.$var[1].$expect_sign.$var[0];
			}
			if(count($var)==2){
				// if(strlen($var[2]!=4 && strlen($var[1]!=2 && strlen($var[0]!=2))
				// 	return;
				return $var[1].$expect_sign.$var[0];
			}
			if(count($var)==1){
				// if(strlen($var[2]!=4 && strlen($var[1]!=2 && strlen($var[0]!=2))
				// 	return;
				return $var[0];
			}
		}
	 }


function trxn_code($vou_type,$debit_credit,$deal_type, $deal_nature, $CounterPartyId){
		if($vou_type=='Contingent'){
			if($deal_type==2){
				if($debit_credit=='debit'){
						return '037';
				}else{
						return '038';
				}
			}else{

				if($deal_nature==2 || $deal_nature==3){
					if($debit_credit=='debit'){
						return '031';
					}else{
						return '032';
					}
				}else{
					if($debit_credit=='debit'){
						return '033';
					}else{
						return '034';
					}
				}

					
			}
		}

}
	function mm_trxn_code($debit_credit){
					if($debit_credit=='debit'){
						return '021';
					}else{
						return '022';
					}
				}

	function ft_trxn_code($debit_credit){
		if($debit_credit=='debit'){
			return '023';
		}else{
			return '024';
		}
	}
	function obu_trxn_code($debit_credit,$type){
		if($type=="Onshore Placement"){
			if($debit_credit=='debit'){
				return '023';
			}else{
				return '024';
			}
		}
		else if($type=="Placement Refund"){ 
			if($debit_credit=='debit'){
				return '024';
			}else{
				return '023';
			}
		}
		else{
			if($debit_credit=='debit'){
							return '029';
						}else{
							return '030';
						}
		}
	}
	function send_email($fromEmail,$fromName, $toemail, $ccemail,$subject,$message)
	 {
		require_once 'PHPMailer/PHPMailerAutoload.php';
		$mail = new PHPMailer();
		$mail->isSMTP();
		
		$mail->Subject =$subject;
		
		$toA=explode(",", $toemail);
		for($i=0; $i<count($toA);$i++)
		{
			$mail->addAddress($toA[$i], '');
		}
		
		//$mail->addAddress($toemail, 'to');	
		//$mail->addAddress('sysadmin@thecitybank.com', 'to');	
		//$mail->addAddress('hosain@mmtvbd.com', '');
		//$mail->addAddress('hosainkuet@yahoo.co.uk', '');
		
		if($ccemail!='' && strlen($ccemail)>5){
			$ccA=explode(",", $ccemail);
			for($i=0; $i<count($ccA);$i++)
			{
				$mail->AddCC($ccA[$i], '');
			}
		}
		
		$mail->setFrom($fromEmail, $fromName);
		$mail->msgHTML($message);					
		$mail->send();
		$mail->clearAddresses();
		
		/*$this->load->library('email');
		$config['mailtype'] = "html";		
		$this->email->initialize($config);
		$this->email->clear(TRUE); 
		$this->email->from($fromEmail, $fromName); 
		 
		$this->email->to($toemail);
		if($ccemail!='' && strlen($ccemail)>5){$this->email->cc($ccemail);}
		
		$this->email->subject($subject);
		$this->email->message($message);
		
		$this->email->send();*/
	 }
	 
	function coma2($amout,$len2)
	{
		$out2r=$amout;
		$out22r='';
		$len2h=$len2;
		$count=1;
		for($i=1; $i<$len2; $i=$i+2)
		{				
			$outNext='';
			$out22r=substr($out2r, $len2h-2).','.$out22r;			
			$outNext=substr($out2r, 0, $len2h-2);
			if(strlen($outNext)<=2 || $count==2){
			 $out22r=$outNext.','.$out22r;
			 break;
			}
			$len2h=strlen($outNext);				
			$out2r=$outNext;		
			$count++;	
		}
		return $out22r;
	}
	
	function comma($amout)
	{
		$minSign='';
		$len3=0;
		if($amout<0){	
			$removeminus=str_replace("-","",$amout);
			$amout=$removeminus;
			$minSign='-';
		}
		$removeDot=explode(".",$amout);
		if (count($removeDot)==1){$amountf=$amout; $spart='00';}else{$amountf=$removeDot[0]; $spart=$removeDot[1];}	
		
		$len3=strlen($amountf);
		if($len3 > 3)
		{
			$out3=substr($amountf, $len3-3);
			$out2=substr($amountf, 0, $len3-3);
			$len2=strlen($out2);
			$out2r=$out2;
			
			if($len2 > 2){
				$out22=$this->coma2($out2,$len2);
				$result=$out22.$out3.'.'.$spart;		
			}
			else{
			$result=$out2.','.$out3.'.'.$spart;
			}		
		}
		else
		{
		$result=$removeDot[0].'.'.$spart;
		}
		return $minSign.$result;
	}
	function commausd($val)
	{
		if($val!=''){return number_format($val,2,'.',',');}		
	}	
	
	function get_yield_for_forcast($y,$set_rev_date)
	{
		$Auc_Q="SELECT d.AuctionRate FROM b2_bond_bill_auction_rate d where d.sts=1 and d.Tenor='".$y."'  
		and d.AuctionDate <='".$set_rev_date."' order by d.AuctionDate DESC limit 1";		
		$Auc_Q_reslt = $this->db->query($Auc_Q);	
		$Arr_auc = $Auc_Q_reslt->result();
		if($Auc_Q_reslt->num_rows() == 0)
		{
			return 0;
		}
		else 
		{
			return $Arr_auc[0]->AuctionRate;
		}
		
	}
	
	function forcast_ex($set_rev_date,$tenor, $yf) 
	{
		if($tenor==2 || $tenor==5){$y1=2; $y2=5;}
		else if($tenor==10){$y1=5; $y2=10;}
		else if($tenor==15){$y1=10; $y2=15;}
		else if($tenor==20){$y1=15; $y2=20;}
		else if($tenor==30){$y1=14; $y2=30;}
		else if($tenor==7 || $tenor==14){$y1=7; $y2=14;}
		else if($tenor==91 || $tenor==182){$y1=91; $y2=182;}
		else if($tenor==364){$y1=182; $y2=364;}
		else{$y1=91; $y2=182;}
	
	//else if($tenor==30){$y1=91; $y2=182;}
		
		$x1=$this->user_model->get_yield_for_forcast($y1,$set_rev_date); //by query db $y1
		$x2=$this->user_model->get_yield_for_forcast($y2,$set_rev_date); //by query db $y2
		//if bond year fraction in years, if bill then in days
		if($y1>0){
			$forcast=($x2 + (($yf-$y2)*($x1-$x2))/($y1-$y2));
		}else{$forcast=0;}
		return $forcast;	
	}
	// Counter party limit check **************************************************
	
	function cp_limit_check($cpid,$section,$type,$amount,$currencyId,$nostroId=NULL,$swap=NULL,$edit_id)
	{
		error_reporting(0);
		//(cp.spot_forward_usd *(0.1) * 1000000 * ".$usd_bdtRate.") spot_forward_usd,
		$usd_bdtRate=79.67;
		$cp_query = $this->db->query("SELECT cp.code,cp.name,cp.BankNature,(cp.overnight_bdt * 1000000) overnight_bdt, 
		(cp.term_lending_bdt * 1000000) term_lending_bdt,(cp.settlement_usd * 1000000 ) settlement_usd,
		(cp.spot_forward_usd * 1000000 * ".$usd_bdtRate.") spot_forward_usd,
		(cp.th_10p_ler_enhancement_dir * 1000000) th_10p_ler_enhancement_dir, 
		(cp.md_50p_ler_enhancement_dir * 1000000) md_50p_ler_enhancement_dir,
		(cp.bill_discounting_bdt * 1000000) bill_discounting_bdt, (cp.acceptance_bdt * 1000000) acceptance_bdt,
		(cp.fdr_bdt * 1000000) fdr_bdt, (cp.th_10p_ler_enhancement_ind * 1000000) th_10p_ler_enhancement_ind,
		(cp.md_50p_ler_enhancement_ind * 1000000) md_50p_ler_enhancement_ind,
		cp.th_enhancement_expdt_dir, cp.md_enhancement_expdt_dir, cp.th_enhancement_expdt_ind, cp.md_enhancement_expdt_ind		
		FROM b2_counterparty cp 
		WHERE cp.id=".$cpid."");
		$cprow = $cp_query->row();
		$str_check=''; $temp = 0;
		//$million = 1000000;

		$oth_query = $this->db->query("SELECT IF(SUM(bill)<>'',SUM(bill),0) sumBill, 
				IF(SUM(acceptance)<>'',SUM(acceptance),0) sumAcpt, IF(SUM(fdr)<>'',SUM(fdr),0) sumFdr,
				IF(SUM(on_mmdeal_line)<>'',SUM(on_mmdeal_line),0) sumONline, 
				IF(SUM(set_line)<>'',SUM(set_line),0) sumSETline,
				IF(SUM(thE)<>'',SUM(thE),0) sumTHe, IF(SUM(mdE)<>'',SUM(mdE),0) sumMDe
				FROM b2_branch_purchasing_bill_req WHERE Counterparty=".$cpid." AND sts<>0 
				AND Settlement=0 AND id<>".$edit_id." GROUP BY Counterparty");
		$oth_row = $oth_query->row();

		$ind_other_usage = $this->get_other_ind_usage($cpid);
		$total_on_used = 0; $total_term_used= 0; 
		$total_set_used=0; $total_spot_used_own=0; $total_spot_used_other=0; 
		$total_oth_used=0;
	
		if($section == 'mm')
		{
			
			//$amount = ($amount * $currow->usd_bdtRate); ///$million;
			// 7 may
			$currency_query = $this->db->get_where('b2_currency', array('id' => $currencyId));
			$currow = $currency_query->row();
			$amount = ($amount * $currow->usd_bdtRate); // amount in bdt
			// 7 may end

			$mm_row1 = $this->user_model->mm_on_query($cpid,$edit_id);// from mmdeal table for call lending deal
			
			if(count($mm_row1) > 0)
			{
				$total_on_used+= $mm_row1->sumONused;
				$total_oth_used+=$mm_row1->sumtradeused;
				$total_set_used+= $mm_row1->sumSetused;
				//$total_spot_used_other+= $mm_row1->sumSpotused;
			}
			
			$mm_row2 = $this->user_model->mm_term_query($cpid,$edit_id);// from mmdeal table for term lending deal
			if(count($mm_row2) > 0)
			{
				$total_on_used+= $mm_row2->sumONused;
				$total_oth_used+=$mm_row2->sumtradeused;
				$total_set_used+= $mm_row2->sumSetused;
				//$total_spot_used_other+= $mm_row2->sumSpotused;
			}
			
			$fex_row = $this->user_model->fex_query($cpid,0); // from fex deal table   // trade will be calculated later
			if(count($fex_row) > 0)
			{
					$total_on_used+= $fex_row->sumONused;
					$total_oth_used+=$fex_row->sumtradeused;
					$total_set_used+= $fex_row->sumSetused;
					//$total_spot_used_own+=$fex_row->sumSpotusedOwn;
					//$total_spot_used_other+= $fex_row->sumSpotusedOther;
			}

			$on_remain = $cprow->overnight_bdt - $total_on_used - $oth_row->sumONline;
			
			//$term_remain = $cprow->term_lending_bdt - $total_term_used;
			$set_remain = $cprow->settlement_usd - $total_set_used - $oth_row->sumSETline;
			$bill_remain = $cprow->bill_discounting_bdt - $total_oth_used - $oth_row->sumBill;
			
			// ---------------------- for  enhancement used 
			$th_dir_remain=0; $md_dir_remain=0;
			//echo $cprow->BankNature;exit;
			if($cprow->BankNature == 'Bank') // ----------- for bank type counter party ---------------------
			{
				
				$total_remain = $on_remain +  $set_remain + $bill_remain ;
				// echo $on_remain.'---'.$set_remain.'---'.$bill_remain;
				// exit;
				if($total_remain < $amount)
				{
					//$str_check.='no';
					$str_check.='no#Bank#';
					if($on_remain < $amount) 
					{ 
						if($on_remain<0){
							$str_check.="on-0";
							$temp = $amount;
						}
						else{
							$str_check.="on-".$on_remain;
							$temp = $amount-$on_remain;
						}

						if($set_remain < $temp) 
						{ 
							
							if($set_remain<0){
								$str_check.="#set-0";
								$temp = $temp;
							}
							else{
								$str_check.="#set-".$set_remain;
								$temp = $temp-$set_remain;
							}
							
							// 29 apr 2019
							if($bill_remain < $temp) 
							{ 
								
								if($bill_remain<0){
									$str_check.="#bill-0";
									$temp = $temp;
								}
								else{
									$str_check.="#bill-".$bill_remain;
									$temp = $temp-$bill_remain;
									$str_check.="#exceed-".$temp;
								}
								
							}
							else
							{
								$str_check.="#bill-".$temp;
							}
						}
						else
						{
							$str_check.="#set-".$temp;
						}
					}
					else
					{
						$str_check.="on-".$amount;
					}	
				}
				else
				{
					$str_check.="yes#Bank#";
					
					if($type == '1' || $type == '3') // for o/n lending
					{
						if($on_remain < $amount) 
						{ 
							if($on_remain<0){
								$str_check.="on-0";
								$temp = $amount;
							}
							else{
								$str_check.="on-".$on_remain;
								$temp = $amount-$on_remain;
							}			
														
							if($set_remain < $temp) 
							{ 
								
								if($set_remain<0){
									$str_check.="#set-0";
									$temp = $temp;
								}
								else{
									$str_check.="#set-".$set_remain;
									$temp = $temp-$set_remain;
								}

								// 29 apr 2019
								if($bill_remain < $temp) 
								{ 
									
									if($bill_remain<0){
										$str_check.="#bill-0";
										$temp = $temp;
									}
									else{
										$str_check.="#bill-".$bill_remain;
										$temp = $temp-$bill_remain;
										$str_check.="#exceed-".$temp;
									}
									
								}
								else
								{
									$str_check.="#bill-".$temp;
								}
								
							}
							else
							{
								$str_check.="#set-".$temp;
							}
							
						}
						else
						{
							$str_check.="on-".$amount;
						}
					}
	
				}
			}
			else // ----------------------for NBFI type counter party----------------
			{
				
				//$total_remain = $on_remain +  $set_remain + $bill_remain ;
				$total_remain = $on_remain ;
				
				if($total_remain < $amount)
				{
					//$str_check.='no';
					$str_check.='no#NBFI#';
					if($on_remain < $amount) 
					{ 
						if($on_remain<0){
							$str_check.="on-0";
							$temp = $amount;
						}
						else{
							$str_check.="on-".$on_remain;
							$temp = $amount-$on_remain;
							$str_check.="#exceed-".$temp;
						}

					}
					else
					{
						$str_check.="on-".$amount;
					}
				}
				else
				{
					$str_check.="yes#NBFI#";
					
					if($type == '1' || $type == '3') // for o/n lending
					{
						if($on_remain < $amount) 
						{
						
							if($on_remain<0){
								$str_check.="on-0";
								$temp = $amount;
							}
							else{
								$str_check.="on-".$on_remain;
								$temp = $amount-$on_remain;
								$str_check.="#exceed-".$temp;
							}

						}
						else
						{
							$str_check.="on-".$amount;
						}
					}

				}
			}
			
		}
		
		//////////////////////////////////////////////////////////////////////////////////////////////////
		elseif($section == 'fex')
		{
			
			$Dif=(strtotime(str_replace('/', '-', $this->input->post('ValueDate')))-strtotime(date('Y-m-d')));
		    $day_dif=($Dif/86400);
		    

			if($swap==1 || $type>1){  // not cash
				//$amount=$amount*(0.1);

				if($type=='4' && $day_dif > 30 ){  // forward
					$amount = $amount*(0.15);
				}else{
					$amount=$amount*(0.1);
				}
			
			}
			
			
			$nost_query = $this->db->query("SELECT bbank FROM b2_account WHERE id= ".$nostroId."");
			$nostrow = $nost_query->row();
			if($cprow->BankNature == 'Corporate')
			{
				$str_check.='corpo';
			}
			elseif($type=='1' && $nostrow->bbank == '1')
			{
				$str_check.='nostro';
			}
			else
			{
				$currency_query = $this->db->get_where('b2_currency', array('id' => $currencyId));
				$currow = $currency_query->row();
				
				$amount = ($amount * $currow->usd_bdtRate); // amount in bdt

				$mm_row1 = $this->user_model->mm_on_query($cpid,0);// from mmdeal table for call lending deal
			
				if(is_object($mm_row1))
				{
					$total_on_used+= $mm_row1->sumONused;
					$total_oth_used+=$mm_row1->sumtradeused;
					$total_set_used+= $mm_row1->sumSetused;
			
				}
				
				$mm_row2 = $this->user_model->mm_term_query($cpid,0);// from mmdeal table for term lending deal
				if(is_object($mm_row2))
				{
					$total_on_used+= $mm_row2->sumONused;
					$total_oth_used+=$mm_row2->sumtradeused;
					$total_set_used+= $mm_row2->sumSetused;
				}
				
				$fex_row = $this->user_model->fex_query($cpid,$edit_id); // from fex deal table
				
				if(is_object($fex_row))
				{
						$total_on_used+= $fex_row->sumONused;
						$total_oth_used+=$fex_row->sumtradeused;
						$total_set_used+= $fex_row->sumSetused;
				}

				$on_remain = $cprow->overnight_bdt - $total_on_used - $oth_row->sumONline;
				
				$set_remain = $cprow->settlement_usd - $total_set_used - $oth_row->sumSETline;
				$bill_remain = $cprow->bill_discounting_bdt - $total_oth_used - $oth_row->sumBill;

				// ----------------------for enhancement used
				$th_dir_remain=0;$md_dir_remain=0;
				
				$total_remain = $on_remain + $set_remain + $bill_remain ;
				
				if($total_remain < $amount)
				{
					
					//$str_check.='no';
					$str_check.='no#';
					if($set_remain < $amount) 
						{
							if($set_remain<0){
								$str_check.="set-0";
								$temp = $amount;
							}
							else{
								$str_check.="set-".$set_remain;
								$temp = $amount-$set_remain;
							}
							if($on_remain < $temp) 
							{
								if($on_remain<0){
									$str_check.="#on-0";
									$temp = $temp;
								}
								else{
									$str_check.="#on-".$on_remain; 
									$temp = $temp-$on_remain;
								} 
								
								// 30 apr 2019
								if($bill_remain < $temp) 
								{ 
									
									if($bill_remain<0){
										$str_check.="#bill-0";
										$temp = $temp;
									}
									else{
										$str_check.="#bill-".$bill_remain;
										$temp = $temp-$bill_remain;
										$str_check.="#exceed-".$temp;
									}
									
								}
								else
								{
									$str_check.="#bill-".$temp;
								}
							}
							else
							{
								$str_check.="#on-".$temp;
							}
							
						}
						else
						{
							$str_check.="set-".$amount;
						}
				}
				else
				{
					$str_check.="yes#";
					
					if($swap == 1) // for swap deals (2nd leg)
					{
						if($set_remain < $amount) 
						{
							if($set_remain<0){
								$str_check.="set-0";
								$temp = $amount;
							}
							else{
								$str_check.="set-".$set_remain;
								$temp = $amount-$set_remain;
							}
							if($on_remain < $temp) 
							{
								if($on_remain<0){
									$str_check.="#on-0";
									$temp = $temp;
								}
								else{
									$str_check.="#on-".$on_remain; 
									$temp = $temp-$on_remain;
								} 
								
								// 30 apr 2019
								if($bill_remain < $temp) 
								{ 
									
									if($bill_remain<0){
										$str_check.="#bill-0";
										$temp = $temp;
									}
									else{
										$str_check.="#bill-".$bill_remain;
										$temp = $temp-$bill_remain;
										$str_check.="#exceed-".$temp;
									}
									
								}
								else
								{
									$str_check.="#bill-".$temp;
								}
							}
							else
							{
								$str_check.="#on-".$temp;
							}
							
						}
						else
						{
							$str_check.="set-".$amount;
						}
					}					
					else // not swap
					{
						if($set_remain < $amount) 
						{
							if($set_remain<0){
								$str_check.="set-0";
								$temp = $amount;
							}
							else{
								$str_check.="set-".$set_remain;
								$temp = $amount-$set_remain;
							}
							if($on_remain < $temp) 
							{
								if($on_remain<0){
									$str_check.="#on-0";
									$temp = $temp;
								}
								else{
									$str_check.="#on-".$on_remain; 
									$temp = $temp-$on_remain;
								} 
								
								// 30 apr 2019
								if($bill_remain < $temp) 
								{ 
									
									if($bill_remain<0){
										$str_check.="#bill-0";
										$temp = $temp;
									}
									else{
										$str_check.="#bill-".$bill_remain;
										$temp = $temp-$bill_remain;
										$str_check.="#exceed-".$temp;
									}
									
								}
								else
								{
									$str_check.="#bill-".$temp;
								}
							}
							else
							{
								$str_check.="#on-".$temp;
							}
							
						}
						else
						{
							$str_check.="set-".$amount;
						}
					}
				}
			}
		}
		
		///////////////////////////////////////////////////////////////////////////////////////////////////
		elseif($section == 'oth')
		{
			if($cprow->BankNature == 'NBFI')
			{
				$str_check.='NBFI';
			}
			else
			{
				$amount = $amount; /// $million;
				$total_on_used = 0; 
				$total_oth_used = 0;
				$total_set_used = 0;
	
				$oth_query = $this->db->query("SELECT IF(SUM(bill)<>'',SUM(bill),0) sumBill, 
				IF(SUM(acceptance)<>'',SUM(acceptance),0) sumAcpt, IF(SUM(fdr)<>'',SUM(fdr),0) sumFdr,
				IF(SUM(on_mmdeal_line)<>'',SUM(on_mmdeal_line),0) sumONline, 
				IF(SUM(set_line)<>'',SUM(set_line),0) sumSETline,
				IF(SUM(thE)<>'',SUM(thE),0) sumTHe, IF(SUM(mdE)<>'',SUM(mdE),0) sumMDe
				FROM b2_branch_purchasing_bill_req WHERE Counterparty=".$cpid." AND sts<>0 
				AND Settlement=0 AND id<>".$edit_id." GROUP BY Counterparty");
				$oth_row = $oth_query->row();
				
				$mm_row1 = $this->user_model->mm_on_query($cpid,0);// from mmdeal table for call lending deal
				if(count($mm_row1) > 0)
				{
					$total_on_used+= $mm_row1->sumONused;
					$total_oth_used+=$mm_row1->sumtradeused;
					$total_set_used+= $mm_row1->sumSetused;
				}
				
				$mm_row2 = $this->user_model->mm_term_query($cpid,0);// from mmdeal table for term lending deal
				if(count($mm_row2) > 0)
				{
					$total_on_used+= $mm_row2->sumONused;
					$total_oth_used+=$mm_row2->sumtradeused;
					$total_set_used+= $mm_row2->sumSetused;
				}
				
				$fex_row = $this->user_model->fex_query($cpid,0); // from fex deal table
				if(count($fex_row) > 0)
				{
						
						$total_on_used+= $fex_row->sumONused;
						$total_oth_used+=$fex_row->sumtradeused;
						$total_set_used+= $fex_row->sumSetused;
				}
				
				
				$on_remain = $cprow->overnight_bdt - $total_on_used;
				$set_remain = $cprow->settlement_usd - $total_set_used;
		
				if($oth_query->num_rows() == 0)
				{
					$bill_remain = $cprow->bill_discounting_bdt;
					
					$on_remain = $on_remain;
					$set_remain = $set_remain;
				}
				else
				{
					$bill_remain = $cprow->bill_discounting_bdt - $oth_row->sumBill;
				
					$on_remain = $on_remain - $oth_row->sumONline;
					$set_remain = $set_remain - $oth_row->sumSETline;
				}
				
				$total_remain = $bill_remain + $on_remain + $set_remain ;
				
				if($total_remain < $amount)
				{
					$str_check.='no#';
				}
				else
				{
					$str_check.="yes#";
				}	
				

				if($bill_remain < $amount) 
					{
						if($bill_remain<0){
							$str_check.="bill-0";
							$temp = $amount;
						}
						else{
							$str_check.="bill-".$bill_remain; 
							$temp = $amount-$bill_remain;
						} 
							
						if($on_remain< $temp)
						{
							if($on_remain<0){
								$str_check.="#on-0";
								$temp = $temp;
							}
							else{
								$str_check.="#on-".$on_remain; 
								$temp = $temp-$on_remain;
							} 
							
							// 30 apr 2019
							if($set_remain < $temp) 
							{ 
								
								if($set_remain<0){
									$str_check.="#set-0";
									$temp = $temp;
								}
								else{
									$str_check.="#set-".$set_remain;
									$temp = $temp-$set_remain;
									$str_check.="#exceed-".$temp;
								}
								
							}
							else
							{
								$str_check.="#set-".$temp;
							}
						}
						else
						{
							$str_check.="#on-".$temp; 
						}
					}
					else
					{
						$str_check.="bill-".$amount;
					}
					
				
			}
		}
		//1500000026
		//echo $str_check; exit;
		return $str_check;
	}
	
	function mm_on_query($cpid,$edit_id)
	{
		//sumTermused   sumSpotused baad
		//sumONused      sumSetused thakbe
		$mm_query_ON = $this->db->query("SELECT (SUM(s1.onused)) as sumONused, 
		SUM(s1.setused) sumSetused, 
		SUM(s1.tradeused) sumtradeused,
		SUM(s1.thEused) sumTHEused,
		SUM(s1.mdEused) sumMDEused 
		FROM
		(
			SELECT SUBSTRING_INDEX(GROUP_CONCAT(m.overnight_used ORDER BY DATE(m.e_dt) DESC SEPARATOR '##'),'##', 1) AS onused,
			SUBSTRING_INDEX(GROUP_CONCAT(m.trade_used ORDER BY DATE(m.e_dt) DESC SEPARATOR '##'),'##', 1) AS tradeused,
			SUBSTRING_INDEX(GROUP_CONCAT(m.settlement_used ORDER BY DATE(m.e_dt) DESC SEPARATOR '##'),'##', 1) AS setused,
			SUBSTRING_INDEX(GROUP_CONCAT(m.exceed_used ORDER BY DATE(m.e_dt) DESC SEPARATOR '##'),'##', 1) AS spotused,
			SUBSTRING_INDEX(GROUP_CONCAT(m.thE_used ORDER BY DATE(m.e_dt) DESC SEPARATOR '##'),'##', 1) AS thEused,
			SUBSTRING_INDEX(GROUP_CONCAT(m.mdE_used ORDER BY DATE(m.e_dt) DESC SEPARATOR '##'),'##', 1) AS mdEused,
			m.CounterPartyId,m.CrosspondingDeal
			FROM b2_mmdeal m
			WHERE m.FUL!='F' AND m.sts <> '0' AND m.DealTypeId = 1 AND m.CounterPartyId= ".$cpid." 
			AND m.id<>".$edit_id." GROUP BY m.CrosspondingDeal
		) s1 GROUP BY s1.CounterPartyId");
		return $mm_query_ON->row();
		//m.FUL!='F'
	}
	
	function mm_term_query($cpid,$edit_id)
	{
		// m.MaturityDate > CURDATE()
		$mm_query_term = $this->db->query("SELECT (SUM(m.overnight_used)) sumONused,
		SUM(m.settlement_used) sumSetused, 
		SUM(m.trade_used) sumtradeused,
		SUM(m.thE_used) sumTHEused, SUM(m.mdE_used) sumMDEused
		FROM b2_mmdeal m
		WHERE m.MaturityDate > CURDATE()
		AND m.sts <> '0' AND m.DealTypeId = 3 
		AND m.CounterPartyId= ".$cpid." AND m.id<>".$edit_id."
		GROUP BY m.CounterPartyId");
		return $mm_query_term->row();
	}
	
	function fex_query($cpid,$edit_id)
	{
		$fex_query = $this->db->query("SELECT SUM(m.overnight_used) sumONused,  
		SUM(m.settlement_used)  sumSetused, 
		SUM(m.trade_used) sumtradeused,
		SUM(m.thE_used) sumTHEused, SUM(m.mdE_used) sumMDEused
		FROM b2_fex_deal m
		WHERE m.ValueDate >= CURDATE() AND m.sts <> '0' AND m.CounterPartyId= ".$cpid." AND m.id<>".$edit_id." 
		GROUP BY m.CounterPartyId");
		return $fex_query->row();
	}
	
	function get_other_ind_usage($cpid)
	{
		$ind_oth_query = $this->db->query("SELECT SUM(t2.on_mmdeal_line) sum_on_line, SUM(t2.set_line) sum_set_line
		FROM b2_branch_purchasing_bill_req t2 WHERE t2.Counterparty=".$cpid." AND t2.sts<>0  AND t2.Settlement=0 
		GROUP BY t2.Counterparty");
		return $ind_oth_query->row();
	}
	
	
	//Nostro  holiday checking
	function nostro_holiday($nostro_id, $vdate)
	{		
		$vdate = implode('-',array_reverse(explode('/',$vdate)));
		
		$str = "select CountryId,BankName from  b2_account where id in ('".$nostro_id."')	limit 1";
		$query=$this->db->query($str);
		$obj=$query->row();
		
		$CountryId='';
		
		if(is_object($obj))
		{
			$CountryId=$obj->CountryId;
			
		}
		if($CountryId=='' || $CountryId<=0){return 'No country found under selected nostro';}
		 
		
		$str1 = "select id from  b2_account_holidays where country_id='".$CountryId."' and holiy_dt='".$vdate."' ";
		$query1=$this->db->query($str1);
		if(is_object($query1->row())){return 'Holiday exist under selected nostro';}
		else{ return 'OK'; }
	}
	
	// Dealer limit check **************************************************
	
	function dealer_limit_check($section,$type,$amount,$currencyId,$edit_id=NULL,$fex_deal_type=NULL,$fex_deal_purpose=NULL)
	{
		
		//error_reporting(0);
		$million = 1000000;
		$dealer_query = $this->db->query("SELECT * FROM b2_user_info 
		WHERE id=".$this->session->userdata['user']['user_id']."");
		$limitrow = $dealer_query->row();
		
		$check_result=0; 
		
		if($limitrow->limit_verify_sts==0){
			$check_result=3;
			return $check_result;
		}
		
		//$total_lcy_day_used = 0; $total_fcy_day_used=0; $total_fex_day_used=0; $total_bbb_day_used=0;
		$qqq1 = $this->db->query("SELECT BDTRate,usd_bdtRate FROM b2_currency WHERE id = 8"); 
		$cur_row_bdt = $qqq1->row();
		
		$qqq = $this->db->query("SELECT BDTRate,usd_bdtRate FROM b2_currency WHERE id = ".$currencyId.""); 
		$cur_row = $qqq->row();
	
		if($section == 'bbb')
		{
			if($type == 0) // not slr
			{
					$per_day = $limitrow->max_bill_bond_trade_day; 

					$bbb_used = $this->user_model->total_bbb_amount_day($this->session->userdata['user']['user_id'],$edit_id);
					$total_bbb_used =  $bbb_used + $amount;
					if($total_bbb_used > ($per_day * $million)) { $check_result=2; }
				
			}
		}
		
		//////////////////////////////////////////////////////////////////////////////////////////////////
		elseif($section == 'fex')
		{
			$we_have = $this->input->post('WeHave');
			$amount = $amount * $cur_row->usd_bdtRate;  // in bdt
			
			if($type == 0)  // not swap
			{
				// 23 sep 2018
				$per_day=0; $per_deal=0;
				if($fex_deal_type==1){  // inter-bank
					//if($fex_deal_purpose=='Funding'){}
					$per_day=$limitrow->max_ibank_trade_day; 
					$per_deal=$limitrow->max_ibank_trade_deal;
					
					if($amount > ($per_deal * $cur_row_bdt->usd_bdtRate*$million)){$check_result=1;}
					else
					{
						$buy_sale_diff = $this->user_model->total_fex_buy_sale_day($this->session->userdata['user']['user_id'],$edit_id,$type,$fex_deal_purpose,$we_have,$fex_deal_type,$amount);
						//$total_fex_used =  $buy_sale_diff + $amount;
						if($buy_sale_diff > ($per_day* $cur_row_bdt->usd_bdtRate*$million)) { $check_result=2; }
					}
				}else{  // corporate not swap -- 9 apr 2019

					$per_day=$limitrow->max_cor_trade_day; 
					$per_deal=$limitrow->max_cor_trade_deal;
					
					if($amount > ($per_deal * $cur_row_bdt->usd_bdtRate*$million)){$check_result=1;}
					else
					{
						$buy_sale_diff = $this->user_model->total_fex_buy_sale_day($this->session->userdata['user']['user_id'],$edit_id,$type,$fex_deal_purpose,$we_have,$fex_deal_type,$amount);
						//$total_fex_used =  $buy_sale_diff + $amount;
						if($buy_sale_diff > ($per_day* $cur_row_bdt->usd_bdtRate*$million)) { $check_result=2; }
					}

				}
			}
			else  // swap 1st leg only
			{
				$per_day=$limitrow->net_swap_fcy_per_day; 
				$per_deal=$limitrow->max_swap_fcy_per_deal;

				if($amount > ($per_deal * $cur_row_bdt->usd_bdtRate * $million)){$check_result=1;}
				else
				{
					$buy_sale_diff = $this->user_model->total_fex_buy_sale_day($this->session->userdata['user']['user_id'],$edit_id,$type,'',$we_have,$fex_deal_type,$amount);
					
					// $total_fex_used =  $fex_used + $mmd_used + $amount;
					//echo $fex_used .'--'. $mmd_used .'--'. $amount.'-'.$limitrow->max_fcy_per_day * $cur_row_bdt->usd_bdtRate*$million;
					if($buy_sale_diff > ($per_day * $cur_row_bdt->usd_bdtRate*$million)) { $check_result=2; }
				}
			}
		}
		
		///////////////////////////////////////////////////////////////////////////////////////////////////
		elseif($section == 'mm')
		{
			if($type > 0 && $type <= 4 )
			{
				if($currencyId == 2)  //lcy
				{	
					// 8 apr 2019
					$per_day=0; $per_deal=0;
					if($type==1 || $type==2){ 		//call
						$per_day=$limitrow->net_call_lcy_per_day; 
						$per_deal=$limitrow->max_call_lcy_per_deal;
					}else{ 		// term
						$per_day=$limitrow->net_term_lcy_per_day; 
						$per_deal=$limitrow->max_term_lcy_per_deal;
					}   
				
					if($amount > ($per_deal*$million)){$check_result=1;}
					else
					{   // old
						//$mmd_used = $this->user_model->total_mmd_amount_day($this->session->userdata['user']['user_id'],$edit_id,$type,$currencyId);
						//$total_mmd_used =  $mmd_used + $amount;
						//if($total_mmd_used > ($per_day*$million)) { $check_result=2; }

						// 8 apr 2019
						$diff = $this->user_model->total_mmd_lend_borr_diff_amount_day($this->session->userdata['user']['user_id'],$edit_id,$type,$currencyId,$amount);
						//echo $diff; exit;
						if(($diff) > ($per_day*$million)) { $check_result=2; }
						
					}
				}
				else  // fcy
				{ 
					$amount = $amount * $cur_row->BDTRate;  // converted in USD
					if($type==1 || $type==3){   	// lending
						$per_day=$limitrow->max_fcy_lend_per_day;  // in USD
						$per_deal = $limitrow->max_fcy_lend_per_deal;

					}else{  	// borrowing
						$per_day=$limitrow->max_fcy_borr_per_day;  // in USD
						$per_deal = $limitrow->max_fcy_borr_per_deal;
					}

					if($amount > ($per_deal *  $million)){$check_result=1;}  //ok
					else
					{
						$mmd_used_in_usd = $this->user_model->mmd_lend_borr_used_amount_day($this->session->userdata['user']['user_id'],$edit_id,$type,$currencyId);
						//$fex_used = $this->user_model->total_fex_amount_day($this->session->userdata['user']['user_id'],0,1,'');
						$total_mmd_used =  $mmd_used_in_usd + $amount;  // in USD
						if($total_mmd_used > ($per_day * $million)) { $check_result=2; }
					}



				}
			}
		}
		
		//echo $str_check;exit;
		return $check_result;
	}
	
	function total_bbb_amount_day($dealerid,$edit_id)
	{
		$sumresult = 0;
		$mm_query_ON = $this->db->query("SELECT SUM(fv) sumfv
			FROM b2_bond_bill_buy bbb 
			WHERE bbb.sts=1 AND bbb.slr_investment <> 0 AND bbb.e_by = ".$dealerid." 
			AND bbb.id<>".$edit_id." AND DATE(bbb.e_dt)=CURDATE() GROUP BY bbb.e_by");
		$result = $mm_query_ON->row();
		if(count($result) > 0)
		{ $sumresult = $result->sumfv; }
		return $sumresult;
	}
	
	function total_fex_amount_day($dealerid,$edit_id,$swap,$fex_deal_purpose)
	{
		$sumresult = 0;
		$str_purpose = '';
		$str_swap=" and swap ='".$swap."' ";
		if($swap==1){$str_swap.="  and swapdealid='0' ";}
		if($fex_deal_purpose!=''){$str_purpose="  and DealPurpose ='".$fex_deal_purpose."'  ";}
		
		$mm_query_ON = $this->db->query("SELECT sum(s1.OurAmount * cur.usd_bdtRate) sumamount FROM
		(SELECT OurCurrencyId, OurAmount FROM b2_fex_deal WHERE sts=1 ".$str_swap."  ".$str_purpose." AND e_by = ".$dealerid." 
		AND id<>".$edit_id." AND DATE(e_dt)=CURDATE()) s1
		LEFT OUTER JOIN b2_currency cur ON(cur.id = s1.OurCurrencyId)
		");
		//echo $this->db->last_query();exit;
		$result = $mm_query_ON->row();
		if(count($result) > 0)
		{ $sumresult = $result->sumamount; }
		return $sumresult;
	}

	// 9 apr 2019
	function total_fex_buy_sale_day($dealerid,$edit_id,$swap,$fex_deal_purpose,$we_have,$fex_deal_type,$amount)
	{
		$diff = 0;
		$buy_amt=0;
		$sold_amt=0;
		$str_purpose = '';
		$str_DealTypeId = '';
		$str_swap=" and swap ='".$swap."' ";
		if($swap==1){$str_swap.="  and swapdealid='0' ";}
		if($swap==0){$str_DealTypeId ="  and DealTypeId ='".$fex_deal_type."'  ";}
		if($fex_deal_purpose!=''){$str_purpose="  and DealPurpose ='".$fex_deal_purpose."'  ";}

		if($we_have=='Purchased'){ $buy_amt=0;}
		if($we_have=='Sold'){ $sold_amt=0; }
		
		$mm_query_ON = $this->db->query("SELECT SUM(s1.buy * cur.usd_bdtRate) sumbuy, 
		  SUM(s1.sale * cur.usd_bdtRate) sumsale,
		  ABS( (SUM(s1.buy * cur.usd_bdtRate)+$buy_amt) - (SUM(s1.sale * cur.usd_bdtRate)+$sold_amt) ) AS diff
		 FROM
			(SELECT 
			OurCurrencyId, OurAmount,
			IF(WeHave='Purchased', OurAmount, 0) buy,
	    	IF(WeHave='Sold', OurAmount, 0) sale
	     	FROM b2_fex_deal WHERE sts=1  ".$str_swap."  ".$str_purpose." 
	     	AND e_by = ".$dealerid." 
	        ".$str_DealTypeId ."  
			AND id<>".$edit_id." AND DATE(e_dt)=CURDATE()) s1
			LEFT OUTER JOIN b2_currency cur ON(cur.id = s1.OurCurrencyId)
		");
		//echo $this->db->last_query();exit;
		$result = $mm_query_ON->row();
		if(count($result) > 0)
		{ $diff = $result->diff; }
		return $diff;
	}
	
	function total_mmd_amount_day($dealerid,$edit_id,$type,$currencyid)
	{
		$where="";
		if($currencyid<>2)
		{
			$where=" and CurrencyId<>2 ";			
		}
		
		$sumresult = 0;
		$mm_query_ON = $this->db->query("SELECT sum(s1.Amount/cur.usd_bdtRate) sumamount 
		FROM
		(
			SELECT CurrencyId, Amount, DealTypeId 
			FROM b2_mmdeal 
			WHERE sts<>0 AND DealTypeId =".$type." AND e_by = ".$dealerid."
		    AND id<>".$edit_id."  ".$where."  and SettlementType!='FULL' AND DATE(e_dt)=CURDATE()) s1
			LEFT OUTER JOIN b2_currency cur ON(cur.id = s1.CurrencyId)
		");
		$result = $mm_query_ON->row();
		//echo $this->db->last_query(); exit;
		if(count($result) > 0)
		{ $sumresult = $result->sumamount; }
		return $sumresult;
	}
	
	// 8 apr 2019 lcy
	function total_mmd_lend_borr_diff_amount_day($dealerid,$edit_id,$type,$currencyid,$amount)
	{
		$where="";
		$lend_amt=0;
		$borr_amt=0;
		if($currencyid<>2)
		{
			$where=" and CurrencyId<>2 ";			
		}
		$diffresult = 0;
		if($type==1 || $type==2){ // call
			
			if($type==1){$lend_amt=$amount;}
			if($type==2){$borr_amt=$amount;}
			$mm_query_ON = $this->db->query(" SELECT SUM(s1.lending) sumlend, 
			  SUM(s1.borr) sumborr,
			  ABS( (SUM(s1.lending )+$lend_amt) - (SUM(s1.borr)+$borr_amt) ) AS diff
			FROM
			  (SELECT 
			    CurrencyId,
			    IF(DealTypeId=1, Amount, 0) lending,
			    IF(DealTypeId=2, Amount, 0) borr,
			    DealTypeId 
			  FROM
			    b2_mmdeal 
			  WHERE sts <> 0 
			    AND (DealTypeId = 1 || DealTypeId = 2)
			    AND e_by = ".$dealerid." 
			    AND id<>".$edit_id."   
			    AND CurrencyId = '2'
			    AND SettlementType != 'FULL' 
			    AND DATE(e_dt) = CURDATE()) s1 
			  LEFT OUTER JOIN b2_currency cur 
			    ON (cur.id = s1.CurrencyId)
			");

		}else{  // term

			if($type==3){$lend_amt=$amount;}
			if($type==4){$borr_amt=$amount;}
			$mm_query_ON = $this->db->query(" SELECT SUM(s1.lending) sumlend, 
				  SUM(s1.borr) sumborr,
				  ABS( (SUM(s1.lending)+$lend_amt) - (SUM(s1.borr)+$borr_amt) ) AS diff
				FROM
				  (SELECT 
				    CurrencyId,
				    IF(DealTypeId=3, Amount, 0) lending,
				    IF(DealTypeId=4, Amount, 0) borr,
				    DealTypeId 
				  FROM
				    b2_mmdeal 
				  WHERE sts <> 0 
				    AND (DealTypeId = 3 || DealTypeId = 4)
				    AND e_by = ".$dealerid." 
				    AND id<>".$edit_id." 
				    AND CurrencyId = '2' 
				    AND SettlementType != 'FULL' 
				    AND DATE(e_dt) = CURDATE()) s1 
				  LEFT OUTER JOIN b2_currency cur 
				    ON (cur.id = s1.CurrencyId)
			");

		}
		
		$result = $mm_query_ON->row();
		//echo $this->db->last_query(); exit;
		if(count($result) > 0)
		{ $diffresult = $result->diff; }
		return $diffresult;
	}

	// 8 apr 2019  fcy
	function mmd_lend_borr_used_amount_day($dealerid,$edit_id,$type,$currencyid)
	{
		$where="";
		if($currencyid<>2)
		{
			$where=" and CurrencyId<>2 ";			
		}
		$sumamount = 0;
		if($type==1 || $type==3){ // lending
			
			
			$mm_query_ON = $this->db->query(" SELECT 
			  (SUM(s1.ond * cur.BDTRate) + SUM(s1.term * cur.BDTRate)) AS sumamount
			FROM
			  (SELECT 
			    CurrencyId,
			    IF(DealTypeId=1, Amount, 0) ond,
			    IF(DealTypeId=3, Amount, 0) term,
			    DealTypeId 
			  FROM
			    b2_mmdeal 
			  WHERE sts <> 0 
			    AND (DealTypeId = 1 || DealTypeId = 3)
			    AND e_by = ".$dealerid." 
			    AND id<>".$edit_id."   ".$where."
			    AND SettlementType != 'FULL' 
			    AND DATE(e_dt) = CURDATE()) s1 
			  LEFT OUTER JOIN b2_currency cur 
			    ON (cur.id = s1.CurrencyId)
			");

		}else{  // borr

			
			$mm_query_ON = $this->db->query(" SELECT 
				  (SUM(s1.ond * cur.BDTRate) + SUM(s1.term * cur.BDTRate)) AS sumamount
				FROM
				  (SELECT 
				    CurrencyId,
				    IF(DealTypeId=2, Amount, 0) ond,
				    IF(DealTypeId=4, Amount, 0) term,
				    DealTypeId 
				  FROM
				    b2_mmdeal 
				  WHERE sts <> 0 
				    AND (DealTypeId = 2 || DealTypeId = 4)
				    AND e_by = ".$dealerid." 
				    AND id<>".$edit_id."   ".$where."
				    AND SettlementType != 'FULL' 
				    AND DATE(e_dt) = CURDATE()) s1 
				  LEFT OUTER JOIN b2_currency cur 
				    ON (cur.id = s1.CurrencyId)
			");

		}
		
		$result = $mm_query_ON->row();
		//echo $this->db->last_query(); exit;
		if(count($result) > 0)
		{ $sumamount = $result->sumamount; }
		return $sumamount;
	}

	function unverified_mm_deal(){
		$group_id= $this->session->userdata['user']['user_work_group_id'];
		$where ="";

		if($group_id==5){ // admin
			$where=" and mm.sts!=3 ";
		}elseif ($group_id==1) {  //FO
			$where=" and mm.sts=1 ";
		}elseif($group_id==2) { //BO
			$where=" and mm.sts=2 ";
		}else{
			$where=" and mm.sts!=3 ";
		}


		$mm_query= $this->db->query("
			SELECT mm.Serial,mm.Amount, cp.name AS cpname , mmtyp.name as deal_type,
			date_format(mm.e_dt,'%d/%m/%Y') as cr_date
			FROM b2_mmdeal mm
			LEFT OUTER JOIN  b2_counterparty cp ON(cp.id = mm.CounterPartyId)
			LEFT OUTER JOIN  b2_mmdealtype mmtyp ON(mmtyp.id = mm.DealTypeId)
			WHERE Date(mm.e_dt) <= CURDATE() 
			and mm.sts!=0
			".$where." 

			
		");
		//echo $this->db->last_query();
		return $mm_query->result();
	}

	function unverified_fex_deal(){
		$group_id= $this->session->userdata['user']['user_work_group_id'];
		$where ="";

		if($group_id==5){ // admin
			$where=" and fex.sts!=3 ";
		}elseif ($group_id==1) {  //FO
			$where=" and fex.sts=1 ";
		}elseif($group_id==2) { //BO
			$where=" and fex.sts=2 ";
		}else{
			$where=" and fex.sts!=3 ";
		}


		// $mm_query= $this->db->query("
		// 	SELECT fex.Serial,fex.OurAmount,fex.Rate, cp.name AS cpname , dn.name as deal_nature,
		// 	date_format(fex.e_dt,'%d/%m/%Y') as cr_date
		// 	FROM b2_fex_deal fex
		// 	LEFT OUTER JOIN  b2_counterparty cp ON(cp.id = fex.CounterPartyId)
		// 	LEFT OUTER JOIN  b2_dealnature dn ON(dn.id = fex.DealNatureId)
		// 	WHERE Date(fex.e_dt) <= CURDATE() 
		// 	and fex.sts!=0
		// 	".$where." 
		// ");

		$mm_query= $this->db->query("
					SELECT a.*,
					  cp.name AS cpname,
					  dn.name AS deal_nature,
					   ourcur.ISO_4217Code AS ourCurr,
					  countercur.ISO_4217Code AS countCurr
					FROM
					(SELECT 
					  fex.Serial,
					  fex.OurAmount,
					  fex.Rate,
					  fex.CounterPartyId,
					  fex.DealNatureId,
					  fex.OurCurrencyId,
					  fex.CounterPartyCurrency,
					  DATE_FORMAT(fex.e_dt, '%d/%m/%Y') AS cr_date 
					FROM
					  b2_fex_deal fex 

					WHERE DATE(fex.e_dt) <= CURDATE() 
					  AND fex.sts != 0 
					  ".$where." ) a
					  LEFT OUTER JOIN b2_counterparty cp 
					    ON (cp.id = a.CounterPartyId) 
					  LEFT OUTER JOIN b2_dealnature dn 
					    ON (dn.id = a.DealNatureId) 
					  LEFT OUTER JOIN b2_currency ourcur 
					    ON (ourcur.id = a.OurCurrencyId) 
					  LEFT OUTER JOIN b2_currency countercur 
					    ON (
					      countercur.id = a.CounterPartyCurrency
					    ) 
		");
		//echo $this->db->last_query();
		return $mm_query->result();
	}


	function unverified_remitance_deal(){
		$group_id= $this->session->userdata['user']['user_work_group_id'];
		$where ="";

		if($group_id==5){ // admin
			$where=" and fex.sts!=3 ";
		}elseif ($group_id==1) {  //FO
			$where=" and fex.sts=1 ";
		}elseif($group_id==2) { //BO
			$where=" and fex.sts=2 ";
		}else{
			$where=" and fex.sts!=3 ";
		}


		$mm_query= $this->db->query("
			SELECT fex.Serial, cp.name AS cpname , dn.name as deal_nature,
			date_format(fex.e_dt,'%d/%m/%Y') as cr_date
			FROM b2_remittance fex
			LEFT OUTER JOIN  b2_counterparty cp ON(cp.id = fex.CounterPartyId)
			LEFT OUTER JOIN  b2_dealnature dn ON(dn.id = fex.DealNatureId)
			WHERE Date(fex.e_dt) <= CURDATE() 
			and fex.sts!=0
			".$where." 
			
		");
		//echo $this->db->last_query();
		return $mm_query->result();
	}

	function unverified_placement_deal(){
		$group_id= $this->session->userdata['user']['user_work_group_id'];
		$where ="";

		if($group_id==5){ // admin
			$where=" and fex.VerifyStatus!=3 ";
		}elseif ($group_id==1) {  //FO
			$where=" and fex.VerifyStatus=1 ";
		}elseif($group_id==2) { //BO
			$where=" and fex.VerifyStatus=2 ";
		}else{
			$where=" and fex.VerifyStatus!=3 ";
		}


		$mm_query= $this->db->query("
			SELECT fex.SerialNo, fex.Amount, cp.name AS cpname,
			date_format(fex.e_dt,'%d/%m/%Y') as cr_date
			FROM b2_placement fex
			LEFT OUTER JOIN  b2_counterparty cp ON(cp.id = fex.CounterPartyId)
			
			WHERE Date(fex.e_dt) <= CURDATE() 
			and fex.sts!=0
			".$where." 
			
		");
		//echo $this->db->last_query();
		return $mm_query->result();
	}

	function unverified_bill_bond(){
		$group_id= $this->session->userdata['user']['user_work_group_id'];
		$where ="";

		if($group_id==5){ // admin
			$where=" and fex.v_sts!=2 ";
		}elseif ($group_id==1) {  //FO
			$where=" and fex.v_sts=0 ";
		}elseif($group_id==2) { //BO
			$where=" and fex.v_sts=1 ";
		}else{
			$where=" and fex.v_sts!=2 ";
		}


		$mm_query= $this->db->query("
			SELECT fex.isin_no, fex.type, fex.fv, cp.name AS cpname,
			date_format(fex.e_dt,'%d/%m/%Y') as cr_date
			FROM b2_bond_bill_buy fex
			LEFT OUTER JOIN  b2_counterparty cp ON(cp.id = fex.cp_id)
			
			WHERE Date(fex.e_dt) <= CURDATE() 
			and fex.sts!=0
			".$where." 
			
		");
		//echo $this->db->last_query();
		return $mm_query->result();
	}

	// 2 may 2019

	function mm_on_used($cpid)
	{
		
		$mm_query_ON = $this->db->query("SELECT (SUM(s1.onused)) as sumONused, 
		SUM(s1.setused) sumSetused, 
		SUM(s1.tradeused) sumtradeused,
		SUM(s1.exceed_used) sumexceedused
		FROM
		(
			SELECT SUBSTRING_INDEX(GROUP_CONCAT(m.overnight_used ORDER BY DATE(m.e_dt) DESC SEPARATOR '##'),'##', 1) AS onused,
			SUBSTRING_INDEX(GROUP_CONCAT(m.trade_used ORDER BY DATE(m.e_dt) DESC SEPARATOR '##'),'##', 1) AS tradeused,
			SUBSTRING_INDEX(GROUP_CONCAT(m.settlement_used ORDER BY DATE(m.e_dt) DESC SEPARATOR '##'),'##', 1) AS setused,
			SUBSTRING_INDEX(GROUP_CONCAT(m.exceed_used ORDER BY DATE(m.e_dt) DESC SEPARATOR '##'),'##', 1) AS exceed_used,
			
			m.CounterPartyId,m.CrosspondingDeal
			FROM b2_mmdeal m
			WHERE m.FUL!='F' AND m.sts <> '0' AND m.DealTypeId = 1 AND m.CounterPartyId= ".$cpid." 
			 GROUP BY m.CrosspondingDeal
		) s1 GROUP BY s1.CounterPartyId");
		return $mm_query_ON->row();
	}
	
	function mm_term_used($cpid)
	{
		
		$mm_query_term = $this->db->query("SELECT (SUM(m.overnight_used)) sumONused,
		SUM(m.settlement_used) sumSetused, 
		SUM(m.trade_used) sumtradeused,
		SUM(m.exceed_used) sumexceedused
		FROM b2_mmdeal m
		WHERE 
		m.MaturityDate > CURDATE()
		AND m.sts <> '0' AND m.DealTypeId = 3 
		AND m.CounterPartyId= ".$cpid." 
		GROUP BY m.CounterPartyId");
		return $mm_query_term->row();
	}
	
	function fex_used($cpid)
	{
		$fex_query = $this->db->query("SELECT SUM(m.overnight_used) sumONused,  
		SUM(m.settlement_used)  sumSetused, 
		SUM(m.trade_used) sumtradeused,
		SUM(m.exceed_used) sumexceedused
		FROM b2_fex_deal m
		WHERE m.ValueDate >= CURDATE() AND m.sts <> '0' AND m.CounterPartyId= ".$cpid."   
		GROUP BY m.CounterPartyId");
		return $fex_query->row();
	}

	function all_deal_data($cpid)
	{
		
		$mm_query_term = $this->db->query("SELECT 
			m.id,m.overnight_used, m.settlement_used, m.trade_used, m.exceed_used,'mm' as dtype
			FROM b2_mmdeal m
			WHERE m.MaturityDate > CURDATE() AND m.sts <> '0' AND (m.DealTypeId = 1 or m.DealTypeId = 3) 
			AND m.CounterPartyId= ".$cpid." 
			
			union

			SELECT 
			m.id,m.overnight_used, m.settlement_used, m.trade_used, m.exceed_used,'fex' as dtype
			FROM b2_fex_deal m
			WHERE m.ValueDate > CURDATE() AND m.sts <> '0' AND m.CounterPartyId= ".$cpid."  
			
			union

			SELECT 
			m.id,m.on_mmdeal_line overnight_used, m.set_line settlement_used, m.bill trade_used, m.exceed_used, 'others' as dtype
			FROM b2_branch_purchasing_bill_req m
			WHERE Counterparty=".$cpid." AND sts<>0 AND Settlement=0 

			");
			return $mm_query_term->result();
	}

	function cp_limit_reallocate($cpid = 0)
	{
		
		$cp_query = $this->db->query("SELECT cp.code,cp.name,cp.BankNature,
								(cp.overnight_bdt * 1000000) overnight_bdt, 
								(cp.settlement_usd * 1000000 ) settlement_usd,
								(cp.bill_discounting_bdt * 1000000) bill_discounting_bdt
								FROM b2_counterparty cp 
								WHERE cp.id=".$cpid."");

		$cp_limit_assigned_row = $cp_query->row();
		$query_live = "SELECT 
						  IFNULL(SUM(tt.sumONused), 0) AS sumONused,
						  IFNULL(SUM(tt.sumSetused), 0) AS sumSetused,
						  IFNULL(SUM(tt.sumtradeused), 0) AS sumtradeused,
						  IFNULL(SUM(tt.sumexceedused), 0) AS sumexceedused 
						FROM
						  (SELECT 
						    (SUM(s1.onused)) AS sumONused,
						    SUM(s1.setused) sumSetused,
						    SUM(s1.tradeused) sumtradeused,
						    SUM(s1.exceed_used) sumexceedused 
						  FROM
						    (SELECT 
						     m.overnight_used AS onused,
							  m.trade_used  AS tradeused,
							  m.settlement_used AS setused,
							  m.exceed_used AS exceed_used,
						      m.CounterPartyId,
						      m.CrosspondingDeal 
						    FROM
						      b2_mmdeal m 
						    WHERE m.FUL!='F'
						      AND m.sts <> '0' 
						      AND m.DealTypeId = 1 
						      AND m.CounterPartyId = ".$cpid." 
						    ) s1 
						  GROUP BY s1.CounterPartyId 
						  UNION
						  SELECT 
						    (SUM(m.overnight_used)) sumONused,
						    SUM(m.settlement_used) sumSetused,
						    SUM(m.trade_used) sumtradeused,
						    SUM(m.exceed_used) sumexceedused 
						  FROM
						    b2_mmdeal m 
						  WHERE m.MaturityDate > CURDATE()
						    AND m.sts <> '0' 
						    AND m.DealTypeId = 3 
						    AND m.CounterPartyId = ".$cpid." 
						  GROUP BY m.CounterPartyId 
						  UNION
						  SELECT 
						    SUM(m.overnight_used) sumONused,
						    SUM(m.settlement_used) sumSetused,
						    SUM(m.trade_used) sumtradeused,
						    SUM(m.exceed_used) sumexceedused 
						  FROM
						    b2_fex_deal m 
						  WHERE m.ValueDate >= CURDATE() 
						    AND m.sts <> '0' 
						    AND m.CounterPartyId = ".$cpid." 
						  GROUP BY m.CounterPartyId 
						  UNION
						  SELECT 
						  	IF(
						      SUM(on_mmdeal_line) <> '',
						      SUM(on_mmdeal_line),
						      0
						    ) sumONused,
						    IF(SUM(set_line) <> '', SUM(set_line), 0) sumSetused,
						    IF(SUM(bill) <> '', SUM(bill), 0) sumtradeused,
						    SUM(exceed_used) sumexceedused 
						  FROM
						    b2_branch_purchasing_bill_req 
						  WHERE Counterparty = ".$cpid." 
						    AND sts <> 0 
						    AND Settlement = 0 
						  GROUP BY Counterparty) AS tt ";
		$live_total = $this->db->query($query_live)->row();

		$free_ONused = $cp_limit_assigned_row->overnight_bdt - $live_total->sumONused;
		$free_Setused = $cp_limit_assigned_row->settlement_usd - $live_total->sumSetused;
		$free_tradeused = $cp_limit_assigned_row->bill_discounting_bdt - $live_total->sumtradeused;
		$total_exceedused = $live_total->sumexceedused;

		// echo "<br>overnight_bdt = ".$cp_limit_assigned_row->overnight_bdt;
		// echo "<br>settlement_usd = ".$cp_limit_assigned_row->settlement_usd;
		// echo "<br>bill_discounting_bdt = ".$cp_limit_assigned_row->bill_discounting_bdt;

		// echo "<br>free_ONused = ".$free_ONused;
		// echo "<br>free_Setused = ".$free_Setused;
		// echo "<br>free_tradeused = ".$free_tradeused;
		// echo "<br>total_exceedused = ".$total_exceedused;
		// exit();

		if ($free_ONused <= 0 && $free_Setused <= 0 && $free_tradeused <= 0 ) {
			return 0;
		}
		else {
			if ($total_exceedused > 0) {
				$query_exceed = "SELECT 
					  m.id,
					  m.overnight_used,
					  m.settlement_used,
					  m.trade_used,
					  m.exceed_used,
					  'b2_mmdeal' AS dtype 
					FROM
					  b2_mmdeal m 
					WHERE m.sts <> '0' 
					  AND ((m.DealTypeId = 1 AND m.FUL!='F')
					    OR (m.DealTypeId = 3 AND  m.MaturityDate > CURDATE()) )
					  AND m.CounterPartyId = ".$cpid." 
					  AND m.exceed_used >0
					UNION
					SELECT 
					  m.id,
					  m.overnight_used,
					  m.settlement_used,
					  m.trade_used,
					  m.exceed_used,
					  'b2_fex_deal' AS dtype 
					FROM
					  b2_fex_deal m 
					WHERE m.ValueDate >= CURDATE() 
					  AND m.sts <> '0' 
					  AND m.CounterPartyId = ".$cpid." 
					  AND m.exceed_used >0
					UNION
					SELECT 
					  m.id,
					  m.on_mmdeal_line overnight_used,
					  m.set_line settlement_used,
					  m.bill trade_used,
					  m.exceed_used,
					  'b2_branch_purchasing_bill_req' AS dtype 
					FROM
					  b2_branch_purchasing_bill_req m 
					WHERE Counterparty = ".$cpid." 
					  AND sts <> 0 
					  AND Settlement = 0 
					  AND m.exceed_used >0";
				$exceed_rows = $this->db->query($query_exceed)->result();
				foreach ($exceed_rows as $row) {
					if ($free_ONused <= 0 && $free_Setused <= 0 && $free_tradeused <= 0 ) {
						break;
					}
					$new_exceed = $row->exceed_used;
					$new_mm = $row->overnight_used;
					$new_fx = $row->settlement_used;
					$new_other = $row->trade_used;
					if($row->dtype == "b2_mmdeal"){
						// from money market
						if ($new_exceed > 0 && $free_ONused > 0 && $new_exceed <= $free_ONused) {
							$free_ONused -= $new_exceed;
							$new_mm += $new_exceed;
							$new_exceed = 0;
						}
						else if($new_exceed > 0 && $free_ONused > 0 && $new_exceed > $free_ONused){
							$new_exceed -= $free_ONused;
							$new_mm += $free_ONused;
							$free_ONused = 0; 
						}
						// from FX
						if ($new_exceed > 0 && $free_Setused > 0 && $new_exceed <= $free_Setused) {
							$free_Setused -= $new_exceed;
							$new_fx += $new_exceed; 
							$new_exceed = 0;
						}
						else if($new_exceed > 0 && $free_Setused > 0 && $new_exceed > $free_Setused){
							$new_exceed -= $free_Setused;
							$new_fx += $free_Setused;
							$free_Setused = 0;
						}
						// from others
						if ($new_exceed > 0 && $free_tradeused > 0 && $new_exceed <= $free_tradeused) {
							$free_tradeused -= $new_exceed;
							$new_other += $new_exceed; 
							$new_exceed = 0;
						}
						else if($new_exceed > 0 && $free_tradeused > 0 && $new_exceed > $free_tradeused){
							$new_exceed -= $free_tradeused;
							$new_other += $free_tradeused;
							$free_tradeused = 0; 
						}

						$new_data = array(
							'overnight_used' => $new_mm, 
							'settlement_used' => $new_fx, 
							'trade_used' => $new_other, 
							'exceed_used' => $new_exceed
						);
						//echo "<br>1xxxx".$free_tradeused;
						$this->db->where(array('id' => $row->id))
								 ->update("b2_mmdeal", $new_data);
					}
					else if($row->dtype == "b2_fex_deal"){
						// from FX
						if ($new_exceed > 0 && $free_Setused > 0 && $new_exceed <= $free_Setused) {
							$free_Setused -= $new_exceed;
							$new_fx += $new_exceed; 
							$new_exceed = 0;
						}
						else if($new_exceed > 0 && $free_Setused > 0 && $new_exceed > $free_Setused){
							$new_exceed -= $free_Setused;
							$new_fx += $free_Setused; 
							$free_Setused = 0;
						}
						// from others
						if ($new_exceed > 0 && $free_tradeused > 0 && $new_exceed <= $free_tradeused) {
							$free_tradeused -= $new_exceed;
							$new_other += $new_exceed; 
							$new_exceed = 0;
						}
						else if($new_exceed > 0 && $free_tradeused > 0 && $new_exceed > $free_tradeused){
							$new_exceed -= $free_tradeused;
							$new_other += $free_tradeused; 
							$free_tradeused = 0;
						}
						// from money market
						if ($new_exceed > 0 && $free_ONused > 0 && $new_exceed <= $free_ONused) {
							$free_ONused -= $new_exceed;
							$new_mm += $new_exceed; 
							$new_exceed = 0;
						}
						else if($new_exceed > 0 && $free_ONused > 0 && $new_exceed > $free_ONused){
							$new_exceed -= $free_ONused;
							$new_mm += $free_ONused; 
							$free_ONused = 0;
						}

						$new_data = array(
							'overnight_used' => $new_mm, 
							'settlement_used' => $new_fx, 
							'trade_used' => $new_other, 
							'exceed_used' => $new_exceed
						);
						//echo "<br>2xxxx".$free_tradeused;
						$this->db->where(array('id' => $row->id))
								 ->update("b2_fex_deal", $new_data);
					}
					else if($row->dtype == "b2_branch_purchasing_bill_req"){
						// from FX
						if ($new_exceed > 0 && $free_Setused > 0 && $new_exceed <= $free_Setused) {
							$free_Setused -= $new_exceed;
							$new_fx += $new_exceed; 
							$new_exceed = 0;
						}
						else if($new_exceed > 0 && $free_Setused > 0 && $new_exceed > $free_Setused){
							$new_exceed -= $free_Setused;
							$new_fx += $free_Setused; 
							$free_Setused = 0;
						}
						// from others
						if ($new_exceed > 0 && $free_tradeused > 0 && $new_exceed <= $free_tradeused) {
							$free_tradeused -= $new_exceed;
							$new_other += $new_exceed; 
							$new_exceed = 0;
						}
						else if($new_exceed > 0 && $free_tradeused > 0 && $new_exceed > $free_tradeused){
							$new_exceed -= $free_tradeused;
							$new_other += $free_tradeused; 
							$free_tradeused = 0;
						}
						// from money market
						if ($new_exceed > 0 && $free_ONused > 0 && $new_exceed <= $free_ONused) {
							$free_ONused -= $new_exceed;
							$new_mm += $new_exceed;
							$new_exceed = 0;
						}
						else if($new_exceed > 0 && $free_ONused > 0 && $new_exceed > $free_ONused){
							$new_exceed -= $free_ONused;
							$new_mm += $free_ONused; 
							$free_ONused = 0;
						}

						$new_data = array(
							'on_mmdeal_line' => $new_mm, 
							'set_line' => $new_fx, 
							'bill' => $new_other, 
							'exceed_used' => $new_exceed 
						);
						//echo "<br>3xxxx".$free_tradeused;
						$this->db->where(array('id' => $row->id))
								 ->update("b2_branch_purchasing_bill_req", $new_data);
					}
				}
			}
			$query_exchange = "SELECT 
								  m.id,
								  m.overnight_used,
								  m.settlement_used,
								  m.trade_used,
								  m.exceed_used,
								  'b2_mmdeal' AS dtype 
								FROM
								  b2_mmdeal m 
								WHERE m.sts <> '0' 
								  AND ((m.DealTypeId = 1 AND m.FUL!='F')
					    		  OR (m.DealTypeId = 3  AND m.MaturityDate > CURDATE() ))
								  AND m.CounterPartyId = ".$cpid." 
								  AND (m.settlement_used > 0 || m.trade_used > 0) 
								UNION
								SELECT 
								  m.id,
								  m.overnight_used,
								  m.settlement_used,
								  m.trade_used,
								  m.exceed_used,
								  'b2_fex_deal' AS dtype 
								FROM
								  b2_fex_deal m 
								WHERE m.ValueDate >= CURDATE() 
								  AND m.sts <> '0' 
								  AND m.CounterPartyId = ".$cpid." 
								  AND (m.overnight_used > 0 || m.trade_used > 0)  
								UNION
								SELECT 
								  m.id,
								  m.on_mmdeal_line overnight_used,
								  m.set_line settlement_used,
								  m.bill trade_used,
								  m.exceed_used,
								  'b2_branch_purchasing_bill_req' AS dtype 
								FROM
								  b2_branch_purchasing_bill_req m 
								WHERE Counterparty = ".$cpid." 
								  AND sts <> 0 
								  AND Settlement = 0 
								  AND (m.on_mmdeal_line > 0 || m.set_line > 0)";
			$exchange_rows = $this->db->query($query_exchange)->result();
			// print_r($exchange_rows);
			foreach ($exchange_rows as $row) {
				if ($free_ONused <= 0 && $free_Setused <= 0 && $free_tradeused <= 0 ) {
					break;
				}
				$new_mm = $row->overnight_used;
				$new_fx = $row->settlement_used;
				$new_other = $row->trade_used;
				if($row->dtype == "b2_mmdeal"){
					if ($free_ONused <= 0) {
						continue;
					}
					if ($new_fx > 0 && $free_ONused > 0 && $new_fx <= $free_ONused) {
						$free_ONused -= $new_fx;
						$new_mm += $new_fx; 
						$new_fx = 0;
					}
					else if($new_fx > 0 && $free_ONused > 0 && $new_fx > $free_ONused){
						$new_fx -= $free_ONused;
						$new_mm += $free_ONused; 
						$free_ONused = 0;
					}
					
					if ($new_other > 0 && $free_ONused > 0 && $new_other <= $free_ONused) {
						$free_ONused -= $new_other;
						$new_mm += $new_other; 
						$new_other = 0;
					}
					else if($new_other > 0 && $free_ONused > 0 && $new_other > $free_ONused){
						$new_other -= $free_ONused;
						$new_mm += $free_ONused;
						$free_ONused = 0;
					}

					$new_data = array(
						'overnight_used' => $new_mm, 
						'settlement_used' => $new_fx, 
						'trade_used' => $new_other
					);
					$this->db->where(array('id' => $row->id))
							 ->update("b2_mmdeal", $new_data);
				}
				else if($row->dtype == "b2_fex_deal"){
					if ($free_Setused <= 0) {
						continue;
					}
					if ($new_mm > 0 && $free_Setused > 0 && $new_mm <= $free_Setused) {
						$free_Setused -= $new_mm;
						$new_fx += $new_mm; 
						$new_mm = 0;
					}
					else if($new_mm > 0 && $free_Setused > 0 && $new_mm > $free_Setused){
						$new_mm -= $free_Setused;
						$new_fx += $free_Setused;
						$free_Setused = 0;
					}
					
					if ($new_other > 0 && $free_Setused > 0 && $new_other <= $free_Setused) {
						$free_Setused -= $new_other;
						$new_fx += $new_other; 
						$new_other = 0;
					}
					else if($new_other > 0 && $free_Setused > 0 && $new_other > $free_Setused){
						$new_other -= $free_Setused;
						$new_fx += $free_Setused;
						$free_Setused = 0;
					}

					$new_data = array(
						'overnight_used' => $new_mm, 
						'settlement_used' => $new_fx, 
						'trade_used' => $new_other
					);
					$this->db->where(array('id' => $row->id))
							 ->update("b2_fex_deal", $new_data);
				}
				else if($row->dtype == "b2_branch_purchasing_bill_req"){
					if ($free_tradeused <= 0) {
						continue;
					}
					if ($new_mm > 0 && $free_tradeused > 0 && $new_mm <= $free_tradeused) {
						$free_tradeused -= $new_mm;
						$new_other += $new_mm; 
						$new_mm = 0;
					}
					else if($new_mm > 0 && $free_tradeused > 0 && $new_mm > $free_tradeused){
						$new_mm -= $free_tradeused;
						$new_other += $free_tradeused; 
						$free_tradeused = 0;
					}
					if ($new_fx > 0 && $free_tradeused > 0 && $new_fx <= $free_tradeused) {
						$free_tradeused -= $new_fx;
						$new_other += $new_fx; 
						$new_fx = 0;
					}
					else if($new_fx > 0 && $free_tradeused > 0 && $new_fx > $free_tradeused){
						$new_fx -= $free_tradeused;
						$new_other += $free_tradeused; 
						$free_tradeused = 0;
					}

					$new_data = array(
						'on_mmdeal_line' => $new_mm, 
						'set_line' => $new_fx, 
						'bill' => $new_other 
					);
					$this->db->where(array('id' => $row->id))
							 ->update("b2_branch_purchasing_bill_req", $new_data);
				}
			}
		}
	}

}
?>