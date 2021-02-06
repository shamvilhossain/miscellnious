<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class user_info extends CI_Controller {

	function __construct()
    {
        parent::__construct();	
		
		$this->output->set_header('Last-Modified:'.gmdate('D, d M Y H:i:s').'GMT');
		$this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate');
		$this->output->set_header('Cache-Control: post-check=0, pre-check=0',false);
		$this->output->set_header('Pragma: no-cache');		
		$this->load->model('user_info_model', '', TRUE);
	}
	
	function view ($menu_group,$menu_cat)
	{		
		$data = array( 	
					'menu_group'=> $menu_group,
					'menu_cat'=> $menu_cat,
					'pages'=> 'user_info/pages/grid',			   			   
				   	'per_page' => $this->config->item('per_pagess')
				   );
		$this->load->view('grid_layout',$data);
	}
	function grid()
	{		
		$this->load->model('user_info_model', '', TRUE);
		$pagenum = $this->input->get('pagenum');
		$pagesize = $this->input->get('pagesize');
		$start = $pagenum * $pagesize;
		
		$result=$this->user_info_model->get_grid_data($this->input->get('filterscount'), $this->input->get('sortdatafield'), $this->input->get('sortorder'),$pagesize, $start);
				
		$data[] = array(
		   'TotalRows' => $result['TotalRows'],		   
		   'Rows' => $result['Rows']
		);		
		echo json_encode($data);		
	}
		
	function from($add_edit='add',$id=NULL,$editrow=NULL)
	{
		$result=$this->user_info_model->get_info($add_edit,$id);
		$data = array( 	
				   'option' => '',
				   'add_edit' => $add_edit,
				   'upr_config'=> $this->user_info_model->upr_config_row(),
				   'working_group_list' => $this->user_info_model->get_parameter_data('b2_working_group','name',"sts = '1'"),
				   'branch_list' => $this->user_info_model->get_parameter_data('b2_branch','name',"sts = '1'"),
				   'division_list'	=> $this->user_info_model->get_parameter_data('b2_division','name',"sts = '1'"),
				   'designation_list'	=> $this->user_info_model->get_parameter_data('b2_designation','name',"sts = '1'"),
				   'fun_designation_list'	=> $this->user_info_model->get_parameter_data('b2_functional_des','name',"sts = '1'"),
				   'result' => $result,
				   'id' => $id,
				   'pages'=> 'user_info/pages/form',
				   'editrow' => $editrow			   
				   );
		$this->load->view('user_info/form_layout',$data);
	}
	
	function set_default_group_rights($d_v=NULL)
	{
		$msg=$this->user_info_model->set_default_group_rights($this->input->post('id'),$this->input->post('gid'));
		$jTableResult = array();
		$jTableResult['status'] = $msg;
		$jTableResult['errorMsgs'] = $msg;
		echo json_encode($jTableResult);
	}

	// ---Modified by Raihan
	function get_working_group_rights($Id=NULL)
	{	
			$data=array();
			$result=$this->user_info_model->get_working_group_rights($Id);
			if($result!=''){
				$data = explode(',',$result);
			}
			echo json_encode($data);
	}
	
	function set_right($Id=NULL,$group_id=NULL,$editrow=NULL)
	{
			
			$UserGroName="";	
			$employee_ID="";			
			$Updated="";
			$viewstring="";
			$options="";
			$Query_getUGdateByID = $this->user_info_model->getUGdateByID($Id);
			foreach($Query_getUGdateByID as $rfE){
					$UserName=$rfE->name;	
					$employee_ID=$rfE->employee_ID;					
					$Updated=$rfE->Updated;	
					$adminstatus=$rfE->system_admin_sts;
					$groupid=$rfE->work_group_id;
					if($Updated==NULL){$Updated=$rfE->EntryDateTime;}
					$options=$rfE->name;
			}
			$reugr=0;
			$resurc=0;
			$inputche="";
			$maindocumentsid=$Id;	
			$userstatusnewold=$this->user_info_model->userstatusnewold($Id);
			if ($adminstatus==2)
			{
				$sys_user_right_category_sort=$this->user_info_model->total_sys_user_right_category();
				if (count($userstatusnewold)>0)
				{
					$oldstate=1;
				}
				else
				{
					$oldstate=0;				
				}				
			}
			else
			{
				$sys_user_right_category_sort=$this->user_info_model->total_sys_user_right_category_user($this->session->userdata['user']['user_id']);
				
				if (count($userstatusnewold)>0)
					{
						$oldstate=1;
					
					}
					else
					{
						$oldstate=0;
						
					}
			
			}
			
			$counter1=0; 
			$counter = 0;
			$cuy_hosan=count($sys_user_right_category_sort);
			foreach ($sys_user_right_category_sort as $row)
				{
						$surcid=$row->id;
						
							
						if ($adminstatus==2)
						{
							$groupre=$this->user_info_model->sys_user_order($surcid);
																								
						}
						else
						{
							$groupre=$this->user_info_model->sys_user_order_user($this->session->userdata['user']['user_id'],$surcid);																	
						
						}																	
						if ($adminstatus==2 && $oldstate==1)
						{																	
							$totalgrcheak=$this->user_info_model->sys_user_order_user($maindocumentsid,$surcid);
																								
						}																
						if ($adminstatus!=2 && $oldstate==1)
						{																	
							$totalgrcheak=$this->user_info_model->sys_user_order_user($maindocumentsid,$surcid);	
																							
						}																
						if ($oldstate==0)
						{																	
							$totalgrcheak=$this->user_info_model->total_cate_right_chak($groupid,$surcid);
																							
						}																
						$chakcountcate=count($groupre);																
						$YYY=count($totalgrcheak);
						if ($chakcountcate>0){													
					$counter1++;
					$sucname=explode(" ",$row->name);
					$surnamenu=count($sucname);
					$chakcname='';
					for ($i=0;$i<$surnamenu;$i++)
					{
					$chakcname=($chakcname.$sucname[$i]);
					}															
					if (($counter%3)==0)
					{
						$viewstring.='<tr  style="vertical-align:top" align="left"><td align="left" style="BORDER-LEFT: #85C2C1 1px solid; BORDER-TOP: #85C2C1 1px solid'; if($counter==($cuy_hosan-3) || $counter==($cuy_hosan-1) || $counter==($cuy_hosan-2)){$viewstring.=';BORDER-BOTTOM: #85C2C1 1px solid;';} $viewstring.= '">';
					 }
					else if (($counter%3)==1) 
					{
						$viewstring.= '<td align="left" valign="top" style="BORDER-LEFT: #85C2C1 1px solid; BORDER-TOP: #85C2C1 1px solid'; if($counter==($cuy_hosan-2) || $counter==($cuy_hosan-1)){$viewstring.= ';BORDER-BOTTOM: #85C2C1 1px solid;';} $viewstring.= '">';
					}
					else if (($counter%3)==2) 
					{
						$viewstring.= '<td align="left" valign="top" style="BORDER-LEFT: #85C2C1 1px solid; BORDER-TOP: #85C2C1 1px solid; BORDER-RIGHT: #85C2C1 1px solid'; if($counter==($cuy_hosan-1)){$viewstring.= ';BORDER-BOTTOM: #85C2C1 1px solid;';} $viewstring.= '">';
					}																		
					$viewstring.= '<table border="0" width="100%">
						<tr  style="vertical-align:top">
						<td style="vertical-align:top" valign="top">																																			
						<input name="chkBoxSelect[]" ';  if ($YYY!=0){if($chakcountcate==$YYY ||$chakcountcate<$YYY){ $viewstring.= 'checked="checked"';}} $viewstring.= ' id="'.$chakcname.'" onclick="Cheakallsub(this,\''.$chakcname.'\')" type="checkbox" value="0">													</td>
						<td align="left" width="85%">
						<font color="#235CDB" face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>'.$row->name.'</b></font>
					</td></tr>';															
						
						$cou=1;															
					foreach ($groupre as $row2)
					{	
							
							
							$viewstring.='<tr  style="vertical-align:top">
							<td width="15%">';
							
							$counter1++;
							$chhk=$row2->rid;
							if ($oldstate==0)
							{																	
								$groupre1=$this->user_info_model->sys_user_order_chak($groupid,$chhk);																																			
																								
							}
							else
							{
								$groupre1=$this->user_info_model->sys_user_order_chak_user($maindocumentsid,$chhk);
							}							
							
							$nrow=count($groupre1);
							if($nrow!=0)
							{
							$reugr++;
							$resurc++;
							$inputche="onclick=\"Cheaksubhead(this,'".$chakcname."')\"";	
							$viewstring.='<input  '.$inputche.' type="checkbox"  checked="checked" name="chkBoxSelect[]" id="'.$chakcname.$cou.'"  value="'.$row2->rid .'" />';
							
							}
							else
							{
							$inputche="onclick=\"Cheaksubhead(this,'".$chakcname."')\"";
							$viewstring.='<input  '.$inputche.' type="checkbox"   name="chkBoxSelect[]" id="'.$chakcname.$cou.'"  value="'.$row2->rid.'" />';
							$resurc++;
							}
							$viewstring.='</td>
							<td align="left" width="85%">																
							<div style="font-size:10pt; margin-left:auto" align="left">'.$row2->rname.'</div>
							</td>
							</tr>';
							
							$cou++;
					}
					
					if(($counter%3)==0){		
					$viewstring.='</table></td>';
						if($counter==($cuy_hosan-1)){$viewstring.= '<td style="BORDER-BOTTOM: #85C2C1 1px solid; BORDER-LEFT: #85C2C1 1px solid; BORDER-RIGHT: #85C2C1 1px solid; BORDER-TOP: #85C2C1 1px solid">&nbsp;</td><td style="BORDER-BOTTOM: #85C2C1 1px solid; BORDER-RIGHT: #85C2C1 1px solid; BORDER-TOP: #85C2C1 1px solid">&nbsp;</td>';
				
						}
						
					}
					else if(($counter%3)==1){		
					$viewstring.= '</table></td>';
						if($counter==($cuy_hosan-1)){$viewstring.='<td style="BORDER-BOTTOM: #85C2C1 1px solid; BORDER-LEFT: #85C2C1 1px solid; BORDER-RIGHT: #85C2C1 1px solid; BORDER-TOP: #85C2C1 1px solid">&nbsp;</td>';
				
						}
					}
					else if(($counter%3)==2){		
					$viewstring.= '</table></td>';
						
					$viewstring.= '</tr>';
					}
					
					$viewstring.='<input type="hidden" id="gco'.$chakcname.'" value="'.$cou.'" />';
					$viewstring.='<input type="hidden" id="gcona'.$counter.'" value="'.$chakcname.'" />';
				
				if(($counter%3)==2){$viewstring.='<tr><td colspan="3" style="BORDER-LEFT: #85C2C1 1px solid; BORDER-RIGHT: #85C2C1 1px solid; BORDER-TOP: #85C2C1 1px solid; line-height:9px">&nbsp;</td></tr>';}
				$counter++;
				}
				else
				{
				$cuy_hosan--;
				}
				}
			
								
			$succmsg=0;
			$succmsg1=0;

			// jan 11
			$data='';
			$data=$this->user_info_model->get_user_info_rights($Id);

			$name = '';
			$user_info=$this->user_info_model->get_single_user_info($Id);		
			if($user_info!=''){
				$employee_ID = $user_info->employee_ID;
				$name = $user_info->name;
			}

			$group_name = '';
			$wg_info=$this->user_info_model->get_working_group_info("",$group_id);		
			if(!empty($wg_info)){
				$group_name = $wg_info->name;
			}

			$data = array(
				   'EId' => $Id,
				   'UserGroName' => $UserName,				   	
				   'Updated' => $Updated,				   
				   'succmsg'=> $succmsg,
				   'succmsg1'=> $succmsg1,
				   'viewstring'=>$viewstring,
				   'counter1'=>$counter1, 
				   'counter'=>$counter,
				   			   
				   'reugr' =>$reugr,
				   'resurc' =>$resurc,
				   
				   'maindocumentsid' => $maindocumentsid,
				   'employee_ID'=>$employee_ID,

				   'wg_Id' => $Id,
				   'name' => $name,
				   'group_id' => $group_id,
				   'group_name' => $group_name,
				   'data' => $data,	
				   'result'=> $this->user_info_model->system_link_list(),
				   	
				   //'pages'=> 'user_info/pages/right',
				   'pages'=> 'user_info/pages/right_other',
				   'editrow' => $editrow			
			  );	
		$this->load->view('user_info/form_layout',$data);
		
		
	}
	function set_right_update($eid)
	{
		$text=array();
		if ($this->session->userdata['user']['login_status'])
		{
			$id=$this->user_info_model->set_right_update($eid);
		}
		else{
			$text[]="Session out, login required";
		}	
		
		$Message='';
		if(count($text)<=0){
			$Message='OK';
			$row=$this->user_info_model->get_add_action_data($eid);
		}else{
			for($i=0; $i<count($text); $i++)
			{
				if($i>0){$Message.=',';}
				$Message.=$text[$i];				
			}	
			$row[]='';	
		}
		
		$var =array();  
		$var['Message']=$Message;
		$var['row_info']=$row;
		echo json_encode($var);    	
	}
	
	
	function duplicate_field($field_name=NULL,$add_edit=NULL,$edit_id=NULL)
	{
		if ($this->input->post('val') != ""){
			$num_row=$this->user_info_model->duplicate_name($field_name,$this->input->post('val'),$edit_id);
			$var =  
			array(
				"Message"=>"",
				"Status"=>$num_row>0?'duplicate':'ok'
			);
			echo json_encode($var);
    	}
	}
		
	function add_edit_action($add_edit=NULL,$edit_id=NULL)
	{
		$text=array();
		if ($this->session->userdata['user']['login_status'])
		{
			$id=$this->user_info_model->add_edit_action($add_edit,$edit_id);
		}
		else{
			$text[]="Session out, login required";
		}	
		
		$Message='';
		if(count($text)<=0){
			$Message='OK';
			$row=$this->user_info_model->get_add_action_data($id);
		}else{
			for($i=0; $i<count($text); $i++)
			{
				if($i>0){$Message.=',';}
				$Message.=$text[$i];				
			}	
			$row[]='';	
		}
		
		$var =array();  
		$var['Message']=$Message;
		$var['row_info']=$row;
		echo json_encode($var);
	}
	function reset_pass()
	{
		$b=$this->user_info_model->reset_pass();
		$jTableResult = array();
		$jTableResult['row_info']=$this->user_info_model->get_add_action_data($this->input->post('verifyEventId'));
		$jTableResult['Message'] = "OK";
		
		echo json_encode($jTableResult);
	}
	function delete_action($d_v=NULL)
	{
		$id=$this->user_info_model->delete_action();
		$jTableResult = array();
		$jTableResult['status'] = "success";
		$jTableResult['errorMsgs'] = 0;
		echo json_encode($jTableResult);
	}
	
	
	
	function change_pass($menu_group=1,$menu_cat=NULL)
	{	
		if ($this->session->userdata['user']['login_status'])
		{
			$user_id=$this->session->userdata['user']['user_id'];
			
			$data = array(
					'menu_group'=> $menu_group,
					'menu_cat'=> $menu_cat,
					'upr_config'=> $this->user_info_model->upr_config_row(),			   
				    'get_user_info' => $this->user_info_model->getUGdateByID($user_id),	
				    'pages'=> 'user_info/pages/change_pass',
				    'user_id' => $user_id			
			  );	
			$this->load->view('grid_layout',$data);		
		}
		else{
			redirect('/home');
		}
	}
	function old_pass_check($user_id)
	{
		$b=$this->user_info_model->old_pass_check($user_id,$this->input->post('val'));
		
		$jTableResult = array();
		$jTableResult['Status'] = $b>0?"OK":"Internal Server Error";
		
		echo json_encode($jTableResult);
	}
	function change_pass_action($user_id)
	{
		$b=$this->user_info_model->change_pass_action($user_id);
		
		if($b==0){$msg="Wrong Old Password";}
		else if($b==1){$msg="OK";}
		else{$msg="System must not allow users to reuse immediate pervious 4 passwords.";}
		
		$jTableResult = array();
		$jTableResult['Message'] = $msg;
		echo json_encode($jTableResult);
	}
	
	function change_zone($menu_group=NULL, $menu_cat=NULL)
	{	
		if ($this->session->userdata['user']['login_status'])
		{
			//$user_id=$this->session->userdata['user']['user_id'];
			$res=$this->user_model->get_parameter_data('b2_zone','name',"sts = '1' and  id in (".$this->session->userdata['user']['operation_zone'].") and  id!='".$this->session->userdata['user']['user_zone_id']."' ");
			$data = array(
					'menu_group'=> $menu_group,
					'menu_cat'=> $menu_cat,
					'zone_list'	=> $res,
				    'pages'=> 'user_info/pages/change_zone'
			  );	
			$this->load->view('grid_layout',$data);		
		}
		else{
			redirect('/home');
		}
	}	
	
	function change_zone_action()
	{
		if ($this->session->userdata['user']['login_status'])
		{
					$op_zone=$this->user_model->get_parameter_name_data_operation_zone("b2_zone","sts='1' and id='".$this->input->post('zonelist')."'");
					
					$this->session->userdata['user']['user_zone_id']=$op_zone->id;
					$this->session->userdata['user']['user_zone_name']=$op_zone->name;
					$this->session->userdata['user']['user_opr_zone_name']=$op_zone->name;
					$this->session->userdata['user']['user_opr_zone_code']=$op_zone->code;
					
					$data = array( 	
						'menu_group'=> 1,
						'menu_cat'=> 41,
						'status_msg'=> 's',
						'pages'=> 'home/pages/grid'
				   	);
					$this->load->view('grid_layout',$data);
					//redirect("/home/home_wc/1/41/s");	
		}
		else{
			redirect('/home');
		}
	}
	function user_right_xls($employee_ID=NULL){

        $report_name = "User Information";
        error_reporting(E_ALL);
        date_default_timezone_set('Asia/Dhaka');
        include_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'Classes'.DIRECTORY_SEPARATOR.'PHPExcel.php');
        global $objPHPExcel;
        $objPHPExcel = new PHPExcel();

        $styleArray = array(
            'font' => array(
                'underline' => PHPExcel_Style_Font::UNDERLINE_SINGLE
            )
        );
        $styleArray_border = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );

        $sheet = 0;
        $objPHPExcel->setActiveSheetIndex($sheet);
        $sheet++;
        $user_Id_array= array();
        $user_Name_array= array();
      	$user_array_group= array();
        $UserId='';
        $UserName='';
        $WorkGroup='';
        $GroupName='';
        $msg_wrk_grp=false;
        $rowNumber = 1;

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(23);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(28);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(23);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(23);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(23);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(40);
      
    $objPHPExcel->getActiveSheet()->setCellValue('A'.$rowNumber, 'NRB Bank limited');
    $objPHPExcel->getActiveSheet()->mergeCells('A'.$rowNumber.':F'.$rowNumber);
    $objPHPExcel->getActiveSheet()->getStyle('A'.$rowNumber.':F'.$rowNumber)->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('A'.$rowNumber.':F'.$rowNumber)->getFont()->setSize(14);
    $objPHPExcel->getActiveSheet()->getStyle('A'.$rowNumber.':F'.$rowNumber)->getAlignment()->setWrapText(true);
    $objPHPExcel->getActiveSheet()->getStyle('A'.$rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $rowNumber++;
    $objPHPExcel->getActiveSheet()->setCellValue('A'.$rowNumber, 'User Privilege');
    $objPHPExcel->getActiveSheet()->mergeCells('A'.$rowNumber.':F'.$rowNumber);
    $objPHPExcel->getActiveSheet()->getStyle('A'.$rowNumber.':F'.$rowNumber)->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('A'.$rowNumber.':F'.$rowNumber)->getAlignment()->setWrapText(true);
    $objPHPExcel->getActiveSheet()->getStyle('A'.$rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    
    // $rowNumber++;
    // $objPHPExcel->getActiveSheet()->setCellValue('A'.$rowNumber, 'User Information');
    // $objPHPExcel->getActiveSheet()->mergeCells('A'.$rowNumber.':F'.$rowNumber);
    // $objPHPExcel->getActiveSheet()->getStyle('A'.$rowNumber.':F'.$rowNumber)->getFont()->setBold(true);
    // $objPHPExcel->getActiveSheet()->getStyle('A'.$rowNumber.':F'.$rowNumber)->getAlignment()->setWrapText(true);
    // $objPHPExcel->getActiveSheet()->getStyle('A'.$rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
      $rowNumber++;
      $rowNumber++;
      $headings = array('User Id','User Name ','User Working Group','Menu Group Name','Menu Category Name','Rights');

        $objPHPExcel->getActiveSheet()->fromArray(array($headings),NULL,'A'.$rowNumber);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$rowNumber.':F'.$rowNumber)->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$rowNumber.':F'.$rowNumber)->getFont()->setSize(10);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$rowNumber.':F'.$rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$rowNumber.':F'.$rowNumber)->applyFromArray($styleArray_border);
        $objPHPExcel->getActiveSheet()->getRowDimension($rowNumber)->setRowHeight(23);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$rowNumber.':F'.$rowNumber)->applyFromArray($styleArray_border);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$rowNumber.':F'.$rowNumber)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->cellColor('A'.$rowNumber.':F'.$rowNumber,'C0C0C0');

     	$result=$this->user_info_model->get_user_info($employee_ID);
		foreach($result as $row)
		{	

			if(in_array($row->UserID,$user_Id_array)){
                    $UserId='';
               } else {
                    $UserId=!empty($row->UserID)?$row->UserID:'';
                   array_push($user_Id_array,$row->UserID);
                }

                if(in_array($row->UserName,$user_Name_array)){
                    $UserName='';
               } else {
                    $UserName=!empty($row->UserName)?$row->UserName:'';
                   array_push($user_Name_array,$row->UserName);
                }
                //  if(in_array($row->WorkGroup,$working_group_array)){
               //      $WorkGroup='';
               // } else {
               //      $WorkGroup=!empty($row->WorkGroup)?$row->WorkGroup:'';
               //      array_push($working_group_array,$row->WorkGroup);
               //  }
				if(in_array($row->UserID.'_'.$row->UserName,$user_array_group))
				{
		              $msg_wrk_grp=false;
				} 
                else 
                {
	               $msg_wrk_grp=true;
	               array_push($user_array_group,$row->UserID.'_'.$row->UserName); 
				}

               
			
		//$UserId=$row->UserID;
		//$UserName=$row->UserName;

		$WorkGroup=($msg_wrk_grp==true)?$row->WorkGroup:'';
		$GroupName=$row->GroupName;
		$Category=$row->Category;
		$Rights=$row->Rights;

		$rowNumber++;
        $headings2 = array($UserId,$UserName,$WorkGroup,$GroupName,$Category,$Rights);
        $objPHPExcel->getActiveSheet()->fromArray(array($headings2),NULL,'A'.$rowNumber);
     	$objPHPExcel->getActiveSheet()->getStyle('A'.$rowNumber.':F'.$rowNumber)->getFont()->setSize(9);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$rowNumber.':F'.$rowNumber)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$rowNumber.':F'.$rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$rowNumber.':F'.$rowNumber)->applyFromArray($styleArray_border);

		}   

      
        $objPHPExcel->getActiveSheet()->setTitle($report_name);
		include_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'Classes'.DIRECTORY_SEPARATOR.'PHPExcel'.DIRECTORY_SEPARATOR.'IOFactory.php');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');//Excel2007
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.str_replace(' ','_',$report_name).'.xls"'); //.xlsx
        header('Cache-Control: max-age=0');
        $objWriter->save('php://output');
        exit();
    
 
	}
	function cellColor($cells,$color)
    {
        global $objPHPExcel;
        $objPHPExcel->getActiveSheet()->getStyle($cells)->getFill()
            ->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID,
                'startcolor' => array('rgb' => $color)
        ));
    }
	
}
?>