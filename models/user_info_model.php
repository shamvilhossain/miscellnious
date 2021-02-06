<?php
class user_info_model extends CI_Model {

    function __construct()
    {
        parent::__construct();
	}
	
	function get_grid_data($filterscount,$sortdatafield,$sortorder,$limit, $offset)
	{
	   	$i=0;
		
	   	if (isset($filterscount) && $filterscount > 0)
		{		
			$where = "(";
			
			$tmpdatafield = "";
			$tmpfilteroperator = "";
			for ($i=0; $i < $filterscount; $i++)
			{//$where2.="(".$this->input->get('filterdatafield'.$i)." like '%".$this->input->get('filtervalue'.$i)."%')";
			
				// get the filter's value.
				$filtervalue = $this->input->get('filtervalue'.$i);
				// get the filter's condition.
				$filtercondition = $this->input->get('filtercondition'.$i);
				// get the filter's column.
				$filterdatafield = $this->input->get('filterdatafield'.$i);
				// get the filter's operator.
				$filteroperator = $this->input->get('filteroperator'.$i);
				
				if($filterdatafield=='group_name')
				{
					$filterdatafield='j1.name';
				}				
				else if($filterdatafield=='branch_name')
				{
					$filterdatafield='j3.name';
				}
				else if($filterdatafield=='division_name')
				{
					$filterdatafield='j4.name';
				}
				else if($filterdatafield=='designtion_name')
				{
					$filterdatafield='j5.name';
				}
				else if($filterdatafield=='fun_designtion_name')
				{
					$filterdatafield='j6.name';
				}				
				else{$filterdatafield='j0.'.$filterdatafield;}
				
				
				if ($tmpdatafield == "")
				{
					$tmpdatafield = $filterdatafield;			
				}
				else if ($tmpdatafield <> $filterdatafield)
				{
					$where .= ")AND(";					
				}
				else if ($tmpdatafield == $filterdatafield)
				{
					if ($tmpfilteroperator == 0)
					{
						$where .= " AND ";
					}
					else $where .= " OR ";	
				}
				
				// build the "WHERE" clause depending on the filter's condition, value and datafield.
				switch($filtercondition)
				{
					case "CONTAINS":
						$where .= " ".$filterdatafield . " LIKE '%" . $filtervalue ."%'";						
						break;
					case "DOES_NOT_CONTAIN":
						$where .= " " . $filterdatafield . " NOT LIKE '%" . $filtervalue ."%'";
						break;
					case "EQUAL":
						$where .= " " . $filterdatafield . " = '" . $filtervalue ."'";
						break;
					case "NOT_EQUAL":
						$where .= " " . $filterdatafield . " <> '" . $filtervalue ."'";
						break;
					case "GREATER_THAN":
						$where .= " " . $filterdatafield . " > '" . $filtervalue ."'";
						break;
					case "LESS_THAN":
						$where .= " " . $filterdatafield . " < '" . $filtervalue ."'";
						break;
					case "GREATER_THAN_OR_EQUAL":
						$where .= " " . $filterdatafield . " >= '" . $filtervalue ."'";
						break;
					case "LESS_THAN_OR_EQUAL":
						$where .= " " . $filterdatafield . " <= '" . $filtervalue ."'";
						break;
					case "STARTS_WITH":
						$where .= " " . $filterdatafield . " LIKE '" . $filtervalue ."%'";
						break;
					case "ENDS_WITH":
						$where .= " " . $filterdatafield . " LIKE '%" . $filtervalue ."'";
						break;
				}
								
				if ($i == $filterscount - 1)
				{
					$where .= ")";
					
				}
				
				$tmpfilteroperator = $filteroperator;
				$tmpdatafield = $filterdatafield;	
						
			}
			// build the query.			
		}else{$where=array();}
		
		if ($sortorder == '')
		{
			$sortdatafield="j0.employee_ID";
			$order = "asc";				
		}
		
		$admin_on_off='';
		if($this->session->userdata['user']['user_system_admin_sts']!=2){
			$admin_on_off=" and j0.system_admin_sts in (0,1)";
		}
		
		$this->db
			->select("SQL_CALC_FOUND_ROWS j0.lock_sts,j0.block_sts, j0.block_sts as unblock_sts, j0.id, j0.employee_ID, j0.name, j0.location, j0.phone, j0.email, j1.name as group_name, j3.name as branch_name, j4.name as division_name, j5.name as designtion_name, j6.name as fun_designtion_name", FALSE)
			->from('b2_user_info as j0')
			->join('b2_working_group as j1', 'j0.work_group_id=j1.id', 'left')
			->join("b2_branch as j3", "j0.branch_id=j3.id", "left")
			->join("b2_division as j4", "j0.division_id=j4.id", "left")
			->join("b2_designation as j5", "j0.designtion_id=j5.id", "left")
			->join("b2_functional_des as j6", "j0.fun_designtion_id=j6.id", "left")
			->where("j0.sts=1 ".$admin_on_off." ", NULL, FALSE)
			->where($where)
			->order_by($sortdatafield,$sortorder)
			->limit($limit, $offset);
		$q=$this->db->get();
		
		$query = $this->db->query('SELECT FOUND_ROWS() AS `Count`');
		$objCount = $query->result_array();		
		$result["TotalRows"] = $objCount[0]['Count'];
		

		if ($q->num_rows() > 0){        
			$result["Rows"] = $q->result();
		} else {
			$result["Rows"] = array();
		}  		
		return $result;
	}
	
	function duplicate_name($field,$val,$edit_id=NULL)
	{
		$where="sts=1 and (upper(".$field.")='".strtoupper($val)."')";
		if($edit_id!=''){$where.=" and id!='".$edit_id."'";}
		$this->db->where($where, NULL, FALSE);
		$this->db->from('b2_user_info');
		$q=$this->db->get(); 
		return $q->num_rows();
	}
	
	
	
	function get_add_action_data($id)
	{
		$this->db
			->select("j0.lock_sts,j0.block_sts, j0.work_group_id, j0.block_sts as unblock_sts,j0.id, j0.employee_ID, j0.name, j0.location, j0.phone, j0.email, j1.name as group_name, j3.name as branch_name, j4.name as division_name, j5.name as designtion_name, j6.name as fun_designtion_name", FALSE)
			->from('b2_user_info as j0')
			->join('b2_working_group as j1', 'j0.work_group_id=j1.id', 'left')
			->join("b2_branch as j3", "j0.branch_id=j3.id", "left")
			->join("b2_division as j4", "j0.division_id=j4.id", "left")
			->join("b2_designation as j5", "j0.designtion_id=j5.id", "left")
			->join("b2_functional_des as j6", "j0.fun_designtion_id=j6.id", "left")
			->where('j0.sts', '1')
			->where("j0.id='".$id."'",NULL,FALSE)
			->limit(1);
		return  $this->db->get()->row();
	}
	
	function login_mail($email,$employee_ID,$pass,$name) 
	{
			$ContactPerson="Treasury Management System (eDeal)";
			$admineMail='treasury_ops@nrbbankbd.com';
			//$admineMail='shamvil@mmtvbd.com';
			
			$subject="Your Password at eDeal (Treasury Operations)";
			$msg="Dear ".$name." ,<br><br>Your login details at edeal are given below.<br><br>User ID:&nbsp;&nbsp;";
			$msg.=$employee_ID;	
			$msg.="<br><br>Password:&nbsp;&nbsp;";
			$msg.=$pass;	
			$msg.="<br><br>Click on the link to login now <a href='".base_url()."'>".base_url()."</a>";
			$msg.="<br><br>Regards<br>Treasury Management System (eDeal)<br>NRB Bank limited";	
			
			if($email!="") {
				$this->send_email($ContactPerson,$admineMail,$email,$subject,$msg);
			}
	}
	function deactivate_mail($email,$employee_ID,$pass,$name,$type_txt) 
	{
			$ContactPerson="Treasury Management System (eDeal)";
			$admineMail='treasury_ops@nrbbankbd.com';
			
			$subject=$type_txt." User Id ".$employee_ID." at Treasury Management System (eDeal)";
			$msg="Dear ".$name." ,<br><br>Your User Id have been ".$type_txt." at Treasury Management System (eDeal)";
			if($type_txt=='Reset Password')
			{
				$msg.="<br><br>Your login details at BMS are given below.<br><br>User ID:&nbsp;&nbsp;";
				$msg.=$employee_ID;	
				$msg.="<br><br>Password:&nbsp;&nbsp;";
				$msg.=$pass;	
			}
			
			
			$msg.="<br><br>Click on the link to login now <a href='".base_url()."'>".base_url()."</a>";
			$msg.="<br><br>Regards<br>Treasury Management System (eDeal)<br>NRB Bank limited";	
			
			if($email!="") {
				$this->send_email($ContactPerson,$admineMail,$email,$subject,$msg);
			}
	}
	function send_email_old($fromPerson, $fromEmail, $to, $subject, $message) {
       	$headers = "From: ". strip_tags($fromPerson)."<". strip_tags($fromEmail)."> \n";
		$headers .= "Reply-To: $to \r\n";
		$headers .= "X-Mailer: PHP/". phpversion();
		$headers .= "X-Priority: 3 \n";
		
		$headers  .= 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$msg=$message;
		@mail($to, $subject, $msg, $headers);
	}
// 24 may 2018
	function send_email($fromName, $fromEmail, $toemail, $subject, $message,$ccemail = '') {
        
            require_once 'PHPMailer/PHPMailerAutoload.php';
            require_once 'PHPMailer/class.phpmailer.php';

           // $message='mkikk'; 
           // echo $fromName;
           // echo $fromEmail;
           // echo $toemail;
           // echo $subject;
           // echo $message;
           // echo $ccemail;

            $mail = new PHPMailer();
            //$mail->SMTPDebug = 3;  

            $mail->isSMTP();
            
            $mail->Subject = $subject;
            
            $toA=explode(",", $toemail);
            for($i=0; $i<count($toA);$i++)
            {
                $mail->addAddress($toA[$i], '');
            }
            
           
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
            // $m='';
       
            // if(!$mail->Send())
            // {
            //     $m = "Error sending: " . $mail->ErrorInfo;
            // }
            // else
            // {
            //     $m= "Sent";
            // }
            // echo $m;exit;
            $mail->clearAddresses();
            //return $m;
    }
	
	function add_edit_action($add_edit=NULL,$edit_id=NULL)
	{
		$data = array(
			'employee_ID' => $this->input->post('employee_ID'),
			'name' => $this->input->post('msgArea'),
			'work_group_id' => $this->input->post('work_group_id'),			
			'branch_id' => $this->input->post('branch_id'),
			'division_id' => $this->input->post('division_id'),
			'location' => $this->input->post('location'),			
			'designtion_id' => $this->input->post('designtion_id'),			
			'fun_designtion_id' => $this->input->post('fun_designtion_id'),
			'phone' => $this->input->post('phone'),
			'email' => $this->input->post('email')
		);
		
		if($this->session->userdata['user']['user_system_admin_sts']!=2){			
			$data['system_admin_sts']=0;
		}
		
		
		
		if($add_edit=="add"){
			$upr_config=$this->upr_config_row();
			if($upr_config->default_password_type=='User ID'){ $pass=$this->input->post('employee_ID');}
			else if($upr_config->default_password_type=='Dot'){ $pass=".";}
			else if($upr_config->default_password_type=='Random'){ $pass=$this->randomPassword();}
			
			
			$data['pass']=sha1($pass);
			$data['pass_expiry_date']=$upr_config->expiry_dt;
			$data['SESSION_idle_time']=$upr_config->global_si_time;
			
			$data['limit_verify_sts']='2';
			$data['e_by']=$this->session->userdata['user']['user_id'];
			$data['e_dt']=date('Y-m-d, H:i:s');
			$this->db->insert('b2_user_info', $data);
			$insert_idss=$this->db->insert_id();
			
			$data1 = array('UserId'=>$insert_idss, 'ChangeBy'=>$this->session->userdata['user']['user_id'], 'ChangeDt'=>date('Y-m-d, H:i:s'), 'Pass' => sha1($pass));
			$this->db->insert('upr_pass_histry', $data1);
			
			$data2 = array('Activities_Id'=>1, 'Activities_by'=>$this->session->userdata['user']['user_id'], 'Activities_dt'=>date('Y-m-d, H:i:s'), 'IP' => $this->input->ip_address(), 'Operate_user_Id'=>$this->input->post('employee_ID'), 'Description'=>'Entry User');
			$this->db->insert('upr_activities_histry', $data2);
			
			
			
			if($this->input->post('acceptterms')==true)
			{
				$this->login_mail($this->input->post('email'),$this->input->post('employee_ID'),$pass,$this->input->post('msgArea'));
			}
			
			return $insert_idss;
		}else{
			$data['u_by']=$this->session->userdata['user']['user_id'];
			$data['u_dt']=date('Y-m-d, H:i:s');
			$this->db->where('id', $edit_id);
			$this->db->update('b2_user_info', $data);
			
			$data2 = array('Activities_Id'=>2, 'Activities_by'=>$this->session->userdata['user']['user_id'], 'Activities_dt'=>date('Y-m-d, H:i:s'), 'IP' => $this->input->ip_address(), 'Operate_user_Id'=>$pass=$this->input->post('employee_ID'), 'Description'=>'Edit User');
			$this->db->insert('upr_activities_histry', $data2);
			
			return $edit_id;
		}
		
	}
	
	function set_default_group_rights($id,$gid)
	{
		$str="select sys_user_rightId from user_group_right where user_groupId='".$gid."'";
		$query=$this->db->query($str);
		$result=$query->result();
		$data=array();	
		if(count($result)>0)
		{
			foreach($result as $row)
			{
				$data[]=array(
					'user_info_id'=>$id,
					'sys_link_id'=>$row->sys_user_rightId,
					'm_by'=>$this->session->userdata['user']['user_id'],
					'm_dt'=>date('Y-m-d, H:i:s')
				);		
			}
			$this->db->insert_batch('b2_user_rights', $data); 	
						
						
			$data2 = array('Activities_Id'=>4, 'Activities_by'=>$this->session->userdata['user']['user_id'], 'Activities_dt'=>date('Y-m-d, H:i:s'), 'IP' => $this->input->ip_address(), 'Operate_user_Id'=>$id, 'Description'=>'Set User Privilege');
			$this->db->insert('upr_activities_histry', $data2);
			return 'success';
		}else{
			return 'No group rights found';
		}
	}
	
	function randomPassword() {
		$alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
		$pass = array(); //remember to declare $pass as an array
		$alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
		for ($i = 0; $i <= 8; $i++) {
			$n = rand(0, $alphaLength);
			$pass[] = $alphabet[$n];
		}
		return implode($pass); //turn the array into a string
	}
	function delete_action(){
		$ary=explode(',',$this->input->post('deleteEventId'));
		for($k=0; $k<count($ary); $k++)
		{
			if($this->input->post('type')=='delete'){
				$data = array('sts' => 0, 'u_by'=> $this->session->userdata['user']['user_id'], 'u_dt'=>date('Y-m-d, H:i:s'));
				$this->db->where('id', $ary[$k]);
				$this->db->where('id!=1',NULL, FALSE);
				$this->db->update('b2_user_info', $data);
				
				if($ary[$k]!=1){				
					$this->db
					->select("employee_ID", FALSE)
					->from('b2_user_info');
					$this->db->where(" id='".$ary[$k]."' ", NULL, FALSE);
					$q=$this->db->get()->row();
					
					$data2 = array('Activities_Id'=>3, 'Activities_by'=>$this->session->userdata['user']['user_id'], 'Activities_dt'=>date('Y-m-d, H:i:s'), 'IP' => $this->input->ip_address(), 'Operate_user_Id'=>$q->employee_ID, 'Description'=>'Delete User');
					$this->db->insert('upr_activities_histry', $data2);
				}
			}
			
		}
	}
	function reset_pass(){
		if($this->input->post('verify_type')=='Reset Password'){
				$upr_config=$this->upr_config_row();
				if($upr_config->default_password_type=='User ID'){ $pass=$this->input->post('employee_ID');}
				else if($upr_config->default_password_type=='Dot'){ $pass=".";}
				else if($upr_config->default_password_type=='Random'){ $pass=$this->randomPassword();}
			
				$data = array(	'pass' => sha1($pass), 'default_change_sts' => '0', 'pass_expiry_date'=>$upr_config->expiry_dt );
				$this->db->where('id', $this->input->post('verifyEventId'));
				$this->db->update('b2_user_info', $data);
				
				$this->db
					->select("employee_ID,email,name", FALSE)
					->from('b2_user_info');
					$this->db->where(" id='".$this->input->post('verifyEventId')."' ", NULL, FALSE);
					$q=$this->db->get()->row();
				if($q->email!='')
				{
					$this->deactivate_mail($q->email,$q->employee_ID,$pass,$q->name,$this->input->post('verify_type'));
				}
					
					$data2 = array('Activities_Id'=>5, 'Activities_by'=>$this->session->userdata['user']['user_id'], 'Activities_dt'=>date('Y-m-d, H:i:s'), 'IP' => $this->input->ip_address(), 'Operate_user_Id'=>$this->input->post('verify_EmplyId'), 'Description'=>'Reset Password');
					$this->db->insert('upr_activities_histry', $data2);
		}
		else if($this->input->post('verify_type')=='Unlock'){
				$pass='';				
				$data = array(	'lock_sts'=>0 );
				$this->db->where('id', $this->input->post('verifyEventId'));
				$this->db->update('b2_user_info', $data);
				
				$this->db
					->select("employee_ID,email,name", FALSE)
					->from('b2_user_info');
					$this->db->where(" id='".$this->input->post('verifyEventId')."' ", NULL, FALSE);
					$q=$this->db->get()->row();
				if($q->email!='')
				{
					$this->deactivate_mail($q->email,$q->employee_ID,$pass,$q->name,$this->input->post('verify_type'));
				}
					
					$data2 = array('Activities_Id'=>10, 'Activities_by'=>$this->session->userdata['user']['user_id'], 'Activities_dt'=>date('Y-m-d, H:i:s'), 'IP' => $this->input->ip_address(), 'Operate_user_Id'=>$this->input->post('verify_EmplyId'), 'Description'=>'Unlock Wrong Password Lock');
					$this->db->insert('upr_activities_histry', $data2);
		}
		else if($this->input->post('verify_type')=='Deactivate'){
				$pass='';				
				$data = array(	'block_sts'=>1 );
				$this->db->where('id', $this->input->post('verifyEventId'));
				$this->db->update('b2_user_info', $data);
				
					
					$data2 = array('Activities_Id'=>7, 'Activities_by'=>$this->session->userdata['user']['user_id'], 'Activities_dt'=>date('Y-m-d, H:i:s'), 'IP' => $this->input->ip_address(), 'Operate_user_Id'=>$this->input->post('verify_EmplyId'), 'Description'=>'Deactivate User Id');
					$this->db->insert('upr_activities_histry', $data2);
		}
		else if($this->input->post('verify_type')=='Activate'){				
				$pass='';				
				$data = array(	'block_sts'=>0 );
				$this->db->where('id', $this->input->post('verifyEventId'));
				$this->db->update('b2_user_info', $data);
				
				$this->db
					->select("employee_ID,email,name", FALSE)
					->from('b2_user_info');
					$this->db->where(" id='".$this->input->post('verifyEventId')."' ", NULL, FALSE);
					$q=$this->db->get()->row();
				if($q->email!='')
				{
					$this->deactivate_mail($q->email,$q->employee_ID,$pass,$q->name,$this->input->post('verify_type'));
				}
					
					$data2 = array('Activities_Id'=>8, 'Activities_by'=>$this->session->userdata['user']['user_id'], 'Activities_dt'=>date('Y-m-d, H:i:s'), 'IP' => $this->input->ip_address(), 'Operate_user_Id'=>$this->input->post('verify_EmplyId'), 'Description'=>'Activate User Id');
					$this->db->insert('upr_activities_histry', $data2);
		}
	}
	
	
	
	function get_info($add_edit,$id)
	{
		if($id!=''){
			$this->db->limit(1);
			$data = $this->db->get_where('b2_user_info', array('id' => $id));
			return $data->row();
		}else{return array();}
	}
	
	function get_parameter_data($table,$orderby,$where=NULL,$stock_sts=NULL)
	{
	     if(!empty($stock_sts) && $stock_sts!='Sub-Zone'){return array();}
		 
		 $this->db->select('*',FALSE);
		 $this->db->from($table);
		 if(!empty($where)) $this->db->where($where);		 
		 $this->db->order_by($orderby);
		 $q=$this->db->get();
		 return $q->result();
		// return $this->db->last_query();
	}
	
	
	//set right
	function getUGdateByID($insert_id=NULL)
	{
		$str = "select *,  date_format(u_dt, '%d %b %Y') as Updated, 
				DATE_FORMAT(e_dt, '%d %b %Y') AS EntryDateTime
				from b2_user_info where sts='1' and id='".$insert_id."'"; 				
		$query=$this->db->query($str);
		return $query->result();
	}
	function total_sys_user_right_category()
	{
		$str = "select * from b2_sys_link_cat where sts='1' order by name ASC";
		$query=$this->db->query($str);
		return $query->result();	
	}
	function userstatusnewold($insert_id=NULL)
	{
		$str = "SELECT * FROM b2_user_rights WHERE sts='1' AND user_info_id='".$insert_id."'";
		$query=$this->db->query($str);
		return $query->result();	
	}
	function total_sys_user_right_category_user($insert_id=NULL)
	{
		$str = "SELECT SURC.name,SURC.id 
				FROM b2_user_rights UR 
				LEFT OUTER JOIN b2_sys_links SUR ON(UR.sys_link_id=SUR.id) 
				LEFT OUTER JOIN b2_sys_link_cat SURC ON(SUR.sys_link_cat_id=SURC.id)
				WHERE UR.user_info_id='".$insert_id."' AND UR.sts='1' AND SUR.sts='1' AND SURC.sts='1'
				GROUP BY SURC.id order by SURC.sys_link_group_id";// SURC.name ASC";
		$query=$this->db->query($str);
		return $query->result();	
	}
	function sys_user_order($surcid=NULL)
	{
		$str = "select tr.name as rname,tr.id as rid, tcr.* 
				from b2_sys_links tr 
				left outer join b2_sys_link_cat tcr on (tr.sys_link_cat_id=tcr.id) 
				where tr.sys_link_cat_id='".$surcid."' 	and tcr.sts='1' and tr.sts='1'";
		$query=$this->db->query($str);
		return $query->result();	
	}
	function sys_user_order_user($insert_id=NULL,$cat_id=NULL)
	{
		$str = "SELECT SUR.name as rname,SUR.id as rid, SURC.name as cname,SURC.id 
				FROM b2_user_rights UR 
				LEFT OUTER JOIN b2_sys_links SUR ON(UR.sys_link_id=SUR.id) 
				LEFT OUTER JOIN b2_sys_link_cat SURC ON(SUR.sys_link_cat_id=SURC.id)
				WHERE UR.user_info_id='".$insert_id."' AND SUR.sys_link_cat_id='".$cat_id."' and UR.sts='1' AND SUR.sts='1' AND SURC.sts='1'";
		$query=$this->db->query($str);
		return $query->result();	
	}
	function total_cate_right_chak($surcid=NULL,$chake=NULL)
	{
		$str = "select tr.id as srid,tr.name as srname,tcr.name as crname 
		from b2_sys_links tr 
		left outer join b2_sys_link_cat tcr on(tr.sys_link_cat_id=tcr.id) 
		join user_group_right grc on (tr.id=grc.sys_user_rightId ) 
		where tr.sys_link_cat_id='".$chake."' and tcr.sts='1' 
		and tr.sts='1' and grc.Status='1' and grc.user_groupId='".$surcid."'";
		$query=$this->db->query($str);
		return $query->result();	
	}
	function sys_user_order_chak($surcid=NULL,$chake=NULL)
	{
		$str = "SELECT * FROM user_group_right where user_groupId ='".$surcid."' AND sys_user_rightId='".$chake."' AND Status='1'";
		$query=$this->db->query($str);
		return $query->result();	
	}
	function sys_user_order_chak_user($surcid=NULL,$chake=NULL)
	{
		$str = "SELECT * FROM b2_user_rights WHERE user_info_id ='".$surcid."' AND sys_link_id='".$chake."' AND sts='1'";
		$query=$this->db->query($str);
		return $query->result();	
	}
	function delete_user_right_id($surcid=NULL)
	{
		$str = "Delete From b2_user_rights WHERE user_info_id ='".$surcid."'";
		$query=$this->db->query($str);
		if (!empty($query)){
			return 1;
		}else{return 0;}		
	}	
	// function set_right_update($eid)
	// {		
	// 	extract($_POST);
	// 	$expcount = count($chkBoxSelect);		
	// 	$EntryDateTime=date('Y-m-d, H:i:s');
	// 			$data=array();	
	// 	   	$quaryReport=$this->user_info_model->delete_user_right_id($eid);
	// 		if($quaryReport>0){
	// 			for($cc=0; $cc<$expcount; $cc++){
	// 				if ($chkBoxSelect[$cc]!=0){
	// 					$data[]=array(
	// 						'user_info_id'=>$eid,
	// 						'sys_link_id'=>$chkBoxSelect[$cc],
	// 						'm_by'=>$this->session->userdata['user']['user_id'],
	// 						'm_dt'=>$EntryDateTime
	// 					);
	// 				}
	// 			}	
	// 			$this->db->insert_batch('b2_user_rights', $data); 	
				
								
	// 				$this->db
	// 				->select("employee_ID", FALSE)
	// 				->from('b2_user_info');
	// 				$this->db->where(" id='".$eid."' ", NULL, FALSE);
	// 				$q=$this->db->get()->row();
					
	// 				$data2 = array('Activities_Id'=>4, 'Activities_by'=>$this->session->userdata['user']['user_id'], 'Activities_dt'=>date('Y-m-d, H:i:s'), 'IP' => $this->input->ip_address(), 'Operate_user_Id'=>$q->employee_ID, 'Description'=>'Set User Privilege');
	// 				$this->db->insert('upr_activities_histry', $data2);
				
													   
	// 	  }
		
	// 	return 1;
	// }

// jan 11
	function set_right_update($eid)
	{		
		extract($_POST);
		//$expcount = count($chkBoxSelect);		
		$EntryDateTime=date('Y-m-d, H:i:s');
				$data=array();	
		   	$quaryReport=$this->user_info_model->delete_user_right_id($eid);
		if($quaryReport>0){
				

		$group_counter = $_POST['group_counter'];
		for($i=1;$i<=$group_counter;$i++)
		{
			$categ_counter = $_POST['group'.$i.'categ_counter'];
			for($j=1;$j<=$categ_counter;$j++)
			{
				$input_counter = $_POST['group'.$i.'categ'.$j.'input_counter'];
				for($k=1;$k<=$input_counter;$k++)
				{
					$id = $_POST['group'.$i.'categ'.$j.'id'.$k];
					$value = isset($_POST['group'.$i.'categ'.$j.'input'.$k]);
					if($value)
					{
						$data[]=array(
							'user_info_id'=>$eid,
							'sys_link_id'=>$id,
							'm_by'=>$this->session->userdata['user']['user_id'],
							'm_dt'=>$EntryDateTime,
							'sts'=>1
						);
					}
				}
			}
		}
		if(!empty($data)){
			$this->db->insert_batch('b2_user_rights', $data);
		}
		
					$this->db
					->select("employee_ID", FALSE)
					->from('b2_user_info');
					$this->db->where(" id='".$eid."' ", NULL, FALSE);
					$q=$this->db->get()->row();
					
					$data2 = array('Activities_Id'=>4, 'Activities_by'=>$this->session->userdata['user']['user_id'], 'Activities_dt'=>date('Y-m-d, H:i:s'), 'IP' => $this->input->ip_address(), 'Operate_user_Id'=>$q->employee_ID, 'Description'=>'Set User Privilege');
					$this->db->insert('upr_activities_histry', $data2);
													   
		  }
		
		return 1;
	}
	
	function old_pass_check($insert_id,$pass)
	{
		$str = "select * from b2_user_info where sts='1' and id='".$insert_id."' and pass='".sha1($pass)."'"; 				
		$query=$this->db->query($str);
		return $query->num_rows();
	}
	
	function change_pass_action($user_id)
	{		
		if($this->old_pass_check($user_id,$this->input->post('employee_ID'))>0){
				
				$str = "select * from upr_pass_histry where  UserId='".$user_id."' order by Id DESC limit 4"; 	
				//and Pass='".sha1($this->input->post('pass'))."' 			
				$query=$this->db->query($str);
				$count=0;
				foreach($query->result() as $row){
					if($row->Pass==sha1($this->input->post('pass'))){$count++;}					
				}
				if($count>0){
					return 2;										
				}else{
					$data1 = array('UserId'=>$user_id, 'ChangeBy'=>$user_id, 'ChangeDt'=>date('Y-m-d, H:i:s'), 'Pass' => sha1($this->input->post('pass')));
					$this->db->insert('upr_pass_histry', $data1);
					
					$data2 = array('Activities_Id'=>6, 'Activities_by'=>$user_id, 'Activities_dt'=>date('Y-m-d, H:i:s'), 'IP' => $this->input->ip_address(), 'Operate_user_Id'=>$this->session->userdata['user']['user_full_id'], 'Description'=>'Change Password');
					$this->db->insert('upr_activities_histry', $data2);
					
					$upr_config=$this->upr_config_row();
					
					$data = array(	'pass' => sha1($this->input->post('pass')), 'pass_expiry_date'=>$upr_config->expiry_dt);
					$this->db->where('id', $user_id);
					$this->db->update('b2_user_info', $data);
					return 1;
				}
		}else{return 0;}
	}
	
	function upr_config_row()
	{		
		$this->db
			->select("*, DATE_ADD(CURRENT_DATE(), INTERVAL Password_validity_period DAY) expiry_dt", FALSE)
			->from('upr_config');
		$data = $this->db->get()->row();
		return $data;	
	}
	function get_user_info($employee_ID=NULL){
		$where1 = ' AND u.sts=1';

           if($employee_ID !=NULL){
            $where1.=" AND u.employee_ID = '" . $employee_ID . "'";
           }
 
		$str = "SELECT u.employee_ID AS UserID,
					 u.name AS UserName,
					 wg.name AS WorkGroup,
					 slg.name AS GroupName,
					 slc.name AS Category,
					 sl.name AS Rights
				FROM  b2_sys_link_group AS slg 
				LEFT OUTER JOIN b2_sys_link_cat AS slc ON slc.sys_link_group_id=slg.id 
				LEFT OUTER JOIN b2_sys_links AS sl ON sl.sys_link_cat_id=slc.id
				LEFT OUTER JOIN b2_user_rights AS ur ON ur.sys_link_id=sl.id
				LEFT OUTER JOIN b2_user_info AS u ON ur.user_info_id=u.id
				LEFT OUTER JOIN b2_working_group AS wg ON u.work_group_id=wg.id
				WHERE slg.sts='1' AND slc.sts='1' AND sl.sts='1' AND ur.sts='1'  AND wg.sts='1' " . $where1 . "
				GROUP BY u.id,slg.id,slc.id,wg.id,sl.id";
  
		$query=$this->db->query($str);
		return $query->result();
	}

	// jan 11
	function system_link_list()
	{
		$sts=0;
		if($this->session->userdata['user']['user_system_admin_sts']==2){
			$sts=1;
		}
		// 23 jan 2018
		if($this->session->userdata['user']['user_work_group_id']==6){
			$sts=1;
		}


		if($sts==0){
		$str = "
				select tr.*,tr.name right_name,tr.sys_link_cat_id categ_id, tr.sys_link_group_id group_id, 
				cat.name categ_name,gr.name group_name 
				from

				(select sys_link_id from b2_user_rights where user_info_id='".$this->session->userdata['user']['user_id']."') s1
				left outer join b2_sys_links tr on(tr.id=s1.sys_link_id) 

				left outer join b2_sys_link_cat cat on(cat.id=tr.sys_link_cat_id) 
				left outer join b2_sys_link_group gr on(gr.id=tr.sys_link_group_id) 
				where cat.sts='1' and gr.sts='1' and tr.sts='1'
				order by tr.sys_link_group_id,tr.sys_link_cat_id

				";
		}else if($sts==1){
		$str = "select tr.*,tr.name right_name,tr.sys_link_cat_id categ_id, tr.sys_link_group_id group_id, 
				cat.name categ_name,gr.name group_name 	from b2_sys_links tr 
				left outer join b2_sys_link_cat cat on(cat.id=tr.sys_link_cat_id) 
				left outer join b2_sys_link_group gr on(gr.id=tr.sys_link_group_id) 
				where cat.sts='1' and gr.sts='1' and tr.sts='1'
				order by tr.sys_link_group_id,tr.sys_link_cat_id";
		}
		
		$query=$this->db->query($str);
		return $query->result_array();	
	}

	function get_user_info_rights($Id=NULL)
    {
		if($Id==NULL){ return ''; }
		$str = "select GROUP_CONCAT(gr.sys_link_id) sys_user_rightId from b2_user_rights gr where gr.user_info_id='".$Id."' and gr.sts='1' GROUP BY gr.user_info_id";
		$query=$this->db->query($str);
		$result=$query->row();
		if($query->num_rows()>0){
			return $result->sys_user_rightId;
		}
		return '';
	}

	function get_working_group_info($add_edit,$id)
	{
		if($id!=''){
			$this->db->limit(1);
			$data = $this->db->get_where('b2_working_group', array('id' => $id));
			return $data->row();
		}else{return array();}
	}

	function get_single_user_info($Id=NULL){
		if($Id==NULL){ return ''; }
		$this->db
				->select("j0.employee_ID,j0.name", FALSE)
				->from('b2_user_info as j0')
				->where("j0.sts=1 AND j0.id=".$Id, NULL, FALSE)
				->limit(1);
		$q = $this->db->get();
		if($q->num_rows()>0){
			return $q->row();
		} else { return ''; }
	}

	function get_working_group_rights($Id=NULL)
	{
		if($Id==NULL){ return ''; }
		$str = "select GROUP_CONCAT(gr.sys_user_rightId) sys_user_rightId from user_group_right gr where gr.user_groupId='".$Id."' and gr.Status='1' GROUP BY gr.user_groupId";
		$query=$this->db->query($str);
		$result=$query->row();
		if($query->num_rows()>0){
			return $result->sys_user_rightId;
		}
		return '';
	}
	
}
?>