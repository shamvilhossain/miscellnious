<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class mmd_deal extends CI_Controller {

	function __construct()
    {
        parent::__construct();	
		
		$this->output->set_header('Last-Modified:'.gmdate('D, d M Y H:i:s').'GMT');
		$this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate');
		$this->output->set_header('Cache-Control: post-check=0, pre-check=0',false);
		$this->output->set_header('Pragma: no-cache');		
		$this->load->model('mmd_deal_model', '', TRUE);
	}
	
	function view ($menu_group,$menu_cat)
	{		
		$data = array( 	
					'menu_group'=> $menu_group,
					'menu_cat'=> $menu_cat,
					'pages'=> 'mmd_deal/pages/grid',			   			   
				   	'per_page' => $this->config->item('per_pagess')
				   );
		$this->load->view('grid_layout',$data);
	}
	function grid()
	{		
		$this->load->model('mmd_deal_model', '', TRUE);
		$pagenum = $this->input->get('pagenum');
		$pagesize = $this->input->get('pagesize');
		$start = $pagenum * $pagesize;
		
		$result=$this->mmd_deal_model->get_grid_data($this->input->get('filterscount'), $this->input->get('sortdatafield'), $this->input->get('sortorder'),$pagesize, $start);
		//print_r($result); exit;		
		$data[] = array(
		   'TotalRows' => $result['TotalRows'],		   
		   'Rows' => $result['Rows']
		);		
		echo json_encode($data);		
	}
		
	function from($add_edit='add',$id=NULL,$editrow=NULL)
	{
		$result=$this->mmd_deal_model->get_info($add_edit,$id);
		if($add_edit != 'add')
			$ssi_info=$this->mmd_deal_model->get_ssi_info($id);
		else
			$ssi_info='';
		$where='';
		$str_order = $this->db->query("SELECT * FROM b2_currency WHERE sts = '1' 
		ORDER BY CASE WHEN id IN (2) THEN 0 ELSE 1 END, name");
		$cur_result = $str_order->result();
		
		if(is_object($result))
		{
			$where=$result->BankNature;
			if($result->MaturityDate=='1999-11-11'){$result->MaturityDate='';}
		}else{$where='Bank';}
		
		$v_cond=" and limit_verify_sts='1'";
		
		$time = $this->mmd_deal_model->get_eod();
		$current_time = date("H:i:s");
		$pre_time = "08:00:00";
		if(strtotime($time)<strtotime($current_time) || strtotime($pre_time)>strtotime($current_time)) {
			 $msg = 'The time is '.date('h:i:s a', strtotime($current_time)).'. A deal has to be made between '
			 .date('h:i:s a', strtotime($pre_time)).' and '.date('h:i:s a', strtotime($time)).'.';
			//$msg = 'ujafa';
			} else {
			 $msg = '';
			}
		$data = array( 	
				   'option' => '',
				   'add_edit' => $add_edit,
				   'rslt_dType' => $this->mmd_deal_model->get_parameter_data('b2_mmdealtype','name',"sts = '1'"),
				   'rslt_dPurpose' => array('Funding'=>'Funding', 'Trading'=>'Trading'),
				   'rslt_cParty' => $this->mmd_deal_model->get_parameter_data('b2_counterparty','name', "sts = '1' and BankNature='".$where."' ".$v_cond),
				   'rslt_Cur' => $cur_result,
				   'result' => $result,
				   'ssi_info' => $ssi_info,
				   'id' => $id,
				   'msg' => $msg,
				   'pages'=> 'mmd_deal/pages/form',
				   'editrow' => $editrow			   
				   );
		$this->load->view('user_info/form_layout',$data);
	}
	

	function add_edit_action($add_edit=NULL,$edit_id=NULL)
	{
		$Message=''; 
		$Message_1=''; 
		$id = 0;
		$dealer_limit_sts=0;
		$cp_limit_sts=0;
		$limit_sts='';
		if ($this->session->userdata['user']['login_status'])
		{
			if($edit_id==NULL){$edit_id = 0;}
			
			$v_date = date('Y-m-d',strtotime(str_replace('/', '-', $this->input->post('MaturityDate'))));						
			$dr_msg=$this->user_model->nostro_holiday(30,$v_date);
			if($dr_msg!='OK'){
				$Message=$dr_msg." , Maturity Date";
			}
			
			
			// dealer limit check
			$dealer_limit_check = $this->user_model->dealer_limit_check('mm',$this->input->post('DealTypeId'),$this->input->post('Amount'),$this->input->post('CurrencyId'),$edit_id,'','');
			
			
			if($dealer_limit_check == 3)
			{
				$Message="Sorry, Your Limit should be verified\n";
			}
			else if($dealer_limit_check == 1)
			{
				$Message_1=" Your Deal Amount Limit Crossed\n";
				$dealer_limit_sts=1;
			}
			else if($dealer_limit_check == 2)
			{
				$Message_1=" Your Today's Total Deal Amount Limit Crossed\n";
				$dealer_limit_sts=1;
			}
			
			//echo $this->input->post('CounterPartyId');exit;
			// counter party limit check
			$cp_limit_check='';
			if($this->input->post('DealTypeId')==1 || $this->input->post('DealTypeId')==3)
			{
				$cp_limit_check = $this->user_model->cp_limit_check($this->input->post('CounterPartyId'),'mm',$this->input->post('DealTypeId'),$this->input->post('Amount'),$this->input->post('CurrencyId'),NULL,NULL,$edit_id);
				// no#Bank#on-10000000#set-10000000#bill-10000000#exceed-700000
				// yes#Bank#on-10000000#set-15000000#bill-5000000
				//yes#Bank#on-140000000
				//echo $cp_limit_check;exit;
				if(current(explode('#',$cp_limit_check)) == 'no')
				{
					$Message="Counterparty Limit Crossed";
					$cp_limit_sts=1;
					$id=$this->mmd_deal_model->add_edit_action($add_edit,$edit_id,$cp_limit_check,$dealer_limit_sts,$cp_limit_sts);

				}
				else
				{
					$arr_limit = explode('#',$cp_limit_check);
					if(isset($arr_limit[4])){$limit_sts = current(explode('-',$arr_limit[4]));}
					if(isset($arr_limit[3]) || isset($arr_limit[4])){
						$cp_limit_sts=1;
						$Message='Limit from Other Line Utilized';
					}
				    
					
					$id=$this->mmd_deal_model->add_edit_action($add_edit,$edit_id,$cp_limit_check,$dealer_limit_sts,$cp_limit_sts);
					
				}
			}
			else{
			
				if($Message == ''){ 
				
					$id=$this->mmd_deal_model->add_edit_action($add_edit,$edit_id,$cp_limit_check,$dealer_limit_sts,$cp_limit_sts);
				
				}
			}
			
		}
		else{
			$Message="Session out, login required";
		}	
		
		
		if($id > 0 && $Message == '')
		{
			$Message='OK';
			$row=$this->mmd_deal_model->get_add_action_data($id);
		}else if($id > 0 && $Message != ''){

			$row=$this->mmd_deal_model->get_add_action_data($id);
			$Message='OK';
			if(current(explode('#',$cp_limit_check)) == 'no')
			{
				$Message_1='Counterparty Limit Crossed';
			}else{
				$Message_1='Deal Successfully created by utilizing Other Line';
			}

			//echo $Message_1;exit;
		}
		else
		{
			$Message='Sorry,Could not Saved! ('.$Message.')';
			$row[]='';	
		}
		
		$var =array();  
		$var['Message']=$Message;
		$var['Message_1']=$Message_1;
		$var['limit_sts']=$limit_sts;
		$var['row_info']=$row;
		//print_r($var);exit;
		echo json_encode($var);
	}
	
	function delete_action()
	{
		$Message='OK';
		$row[]='';
		if ($this->session->userdata['user']['login_status'])
		{			
			$id=$this->mmd_deal_model->delete_action();
			if($this->input->post("type")=='delete'){$row[]='';	}
			else{$row = $this->mmd_deal_model->get_add_action_data($id);}
		}else{
			$Message='Session out, login required';
		}
			
			$var =array();  
			$var['Message']=$Message;
			$var['row_info']=$row;
			echo json_encode($var);
	}
	
	function ajax_comma()
	{
		if ($this->input->post('val') >0){
			echo $this->user_model->comma($this->input->post('val'));
		}else{
			echo 0.00;
		}		
	}
	
	function get_banknature()
	{
		if ($this->input->post('val') != ""){			
			$num_row=$this->mmd_deal_model->get_parameter_data('b2_counterparty','name',"sts = '1' and BankNature='".$this->input->post('val')."' and limit_verify_sts='1' ");				
			//print_r($num_row);
			$var =  
			array(
				"Message"=>$this->input->post('val'),
				"Status" =>$num_row
			);
			echo json_encode($var);
    	}
	}

	function get_ssi_list()
	{
		if ($this->input->post('val1') != "" || $this->input->post('val2') != ""){			
			$num_row=$this->mmd_deal_model->get_ssi_list("CurrencyId = '".$this->input->post('val2')."' ");				
			//print_r($num_row);
			$var =  
			array(
				"Message"=>$this->input->post('val1'),
				"Status" =>$num_row
			);
			echo json_encode($var);
    	}
	}
	
	
	function duplicate_field($field_name=NULL,$add_edit=NULL,$edit_id=NULL)
	{
		if ($this->input->post('val') != ""){
			$num_row=$this->mmd_deal_model->duplicate_name($field_name,$this->input->post('val'),$edit_id);
			$var =  
			array(
				"Message"=>"",
				"Status"=>$num_row>0?'duplicate':'ok'
			);
			echo json_encode($var);
    	}
	}
	
	function ajax_action() 
	{
		$postdata = json_decode($this->input->post('updateFields'),true);
		$amount = $postdata[0]['amount'];
		$rate = $postdata[0]['rate'];
		$hp = $postdata[0]['hp'];
		$repo_int = ($rate * $amount * $hp) / (100 * 365);
		$total_settle = round(($amount + $repo_int),2);
		//$total_settle = $amount + $repo_int;
		echo $total_settle;
	}
	
	function viewDetails($id=NULL,$editrow=NULL,$type)
	{
		if($type == 'repo')
		{
			$sec_result = $this->mmd_deal_model->get_repo_result($id);
			$type = 'Repo';
		}
		elseif($type == 'reverse')
		{
			$sec_result = $this->mmd_deal_model->get_reverse_result($id);
			$type = 'Re-Repo';
		}
		$data = array( 	
				   'id' => $id,
				   'pages'=> 'mmd_deal/pages/details',
				   'editrow' => $editrow,
				   'type' => $type,
				   'sec_rslt' => $sec_result
				   );
		$this->load->view('mmd_deal/form_layout',$data);
	}
	
	
	function load_reposecurities()
	{
		include('./application/Classes/PHPExcel.php');
		include('./application/Classes/PHPExcel/Calculation/Financial.php');
		$varified=0;
		$repoVdt= implode('-',array_reverse(explode('/',$this->input->post('mm_deal_vdt'))));
		$als = $this->input->post('mm_deal_als');
		$id = $this->input->post('mm_deal_id');
		$p_dec_yield = $this->input->post('dec_Y');
		$p_dec_per100 = $this->input->post('dec_p');
		$p_basis = intval($this->input->post('bsis'));
		$deal_result=array();
		//echo $id;exit;
		if($id>0)
		{
			$q1 = $this->db->query("SELECT ValueDate,MaturityDate,ALS, repo_parameters FROM b2_mmdeal where id=".$id."");
			$deal_result = $q1->row();
			$repoVdt= $deal_result->ValueDate;
			$als = $deal_result->ALS;
			if($deal_result->repo_parameters!=''){
				$arr_parameters = explode(',',$deal_result->repo_parameters);
				
				$p_dec_yield = $arr_parameters[0];
				$p_dec_per100 = $arr_parameters[1];
				//$p_dec_per100 = '';
				$p_basis = intval($arr_parameters[2]);	
			}						
		}
		$last_prov_dt = $this->db->query(" SELECT MAX(prov_dt) as last_prov_dt FROM b2_bond_bill_amortization_htm")->row();
		
		$str='	<div id="repo_width" style="overflow:auto; min-height::400px; height:auto; border:1px solid #50852c;width:100%">
				<table class="register-table" style="width:100%" id="proTab">
				<tr bgcolor="#eeeeee" style="font-weight:bold; font-size:9pt; color:#000000">
					<td style="width:2%;text-align:center"><input type="checkbox" name="checkAll" id="checkAll" onClick="CheckAll_2(this)" /></td>
					<td style="width:10%;text-align:center">ISIN Name</td>
					<td style="width:6%;text-align:center">Tenor</td>
					<td style="width:7%;text-align:center">Issue Date</td>
					<td style="width:7%;text-align:center">Purchase Date</td>
					<td style="width:7%;text-align:center">Maturity Date</td>
					<td style="width:10%;text-align:right">Face Value</td>
					<td style="width:10%;text-align:right">New<br/>Face Value</td>
					<td style="width:5%;text-align:center">M.Yield (%)</td>
					<td style="width:5%;text-align:center">Remain<br />Y/D</td>
					<td style="width:10%;text-align:right">Clean Price</td>
					<td style="width:10%;text-align:right">Dirty Price</td>
					<td style="width:5%;text-align:right">Purpose</td>
				</tr>
				<tbody style="overflow:auto; min-height::400px; height:auto;">';
								
					$co = 1;
					
					$result = $this->mmd_deal_model->get_hft_query($repoVdt,$als,$id);
					//print_r($result);
					foreach($result as $rowHFT) // for hft bond
					{
						$objPHPExcel = new PHPExcel_Calculation_Financial();
						$mdate = $rowHFT->maturity_dt;
						$sdate= $repoVdt;
						$cr = $rowHFT->cr;
						$fv = $rowHFT->repofv;
						$dtype = $rowHFT->type;
						$tnor = $rowHFT->tenor;
						
						$last_coupon_date2 = $objPHPExcel->COUPPCD($sdate,$mdate,2,$p_basis,1);
						$last_coupon_date= implode('-',array_reverse(explode('/',$last_coupon_date2)));
						$next_coupon_date2 = $objPHPExcel->COUPNCD($sdate,$mdate,2,$p_basis,1);
						$next_coupon_date= implode('-',array_reverse(explode('/',$next_coupon_date2)));
						$diffNcdPcd = $objPHPExcel->get_date_diff($next_coupon_date,$last_coupon_date);
						
						$hp = floor(strtotime($sdate)-strtotime($last_coupon_date))/86400;
						
						$hpi= $objPHPExcel->get_hp_interest($cr,$hp,$fv,$diffNcdPcd);
						$hpi=number_format($hpi,2,'.','');
				
						$sdate2 = date('d/m/Y',strtotime($sdate));
						$mdate2 = date('d/m/Y',strtotime($mdate));
						$yearFriction= $objPHPExcel->YEARFRAC($sdate2,$mdate2,$p_basis);
						
						$tnor = $objPHPExcel->b2_get_tenor_from_yearfraction($yearFriction,$dtype);
						$my= $this->user_model->forcast_ex($sdate,$tnor,$yearFriction); //forcast
						if($rowHFT->preyield != '') { $my = $rowHFT->preyield;}
						$my = number_format($my,$p_dec_yield,'.','');
						
						$price_100 = $objPHPExcel->PRICE($sdate,$mdate,($cr/100),($my/100),100,2,$p_basis);
						if($rowHFT->precp100 != '') {$price_100 = $rowHFT->precp100;}
						$price_100=number_format($price_100,$p_dec_per100,'.','');
					
						$MV=($price_100*$fv)/100;
						$MV=number_format($MV,2,'.','');						
						$dirtyValue = $MV+ $hpi;
						if($rowHFT->prebv != '') 
						{$MV = $rowHFT->prebv; $dirtyValue = ($rowHFT->preadjust + $rowHFT->preincome + $MV); }
												
					
					$str.='<tr valign="top" bgcolor="#A2ADD0" id="row'.$co.'"  style=" font-size:9pt; color:#000000;">
							<td style="width:40px">
							<input type="checkbox" name="chkBoxSelect'.$co.'" id="chkBoxSelect'.$co.'"  onClick="CheckChanged_2(),calculateTotalAmnt()" ';
								if($id != 0 && $id==$rowHFT->edit_ref_id){ $str.=' checked="checked" '; }
								$str.=' />
							</td> 
							<td>
							<input type="text" readonly="" name="ISIN_Name'.$co.'" id="ISIN_Name'.$co.'" style="width:95%;text-transform:uppercase" value="'.$rowHFT->isin_no.'" />
							<input type="hidden" name="Type_of_Deal'.$co.'" id="Type_of_Deal'.$co.'" value="'.$rowHFT->type.'" />
							</td>
							<td><input type="text" name="TenorId'.$co.'" readonly="" id="TenorId'.$co.'" style="width:95%" value="'.$rowHFT->tenor." ".$rowHFT->day_year.'" />
							<input type="hidden" name="tenor'.$co.'"  id="tenor'.$co.'" style="width:95%" value="'.$rowHFT->tenor.'" />
							</td>
							<td><input type="text" readonly="" name="Issue_Date'.$co.'" id="Issue_Date'.$co.'" style="width:95%" value="'.date("d/m/Y",strtotime($rowHFT->issue_dt)).'" />
							</td>	
							<td><input type="text" readonly="" name="purDt'.$co.'" id="purDt'.$co.'" style="width:95%" value="'.date("d/m/Y",strtotime($rowHFT->pur_dt)).'" />
							</td>	
							<td><input type="text" readonly="" name="matDt'.$co.'" id="matDt'.$co.'" style="width:95%" value="'.date("d/m/Y",strtotime($mdate)).'" />
							</td>
							<td><input type="text" readonly="" style="width:95%;text-align:right" name="old_fv'.$co.'" id="old_fv'.$co.'"  value="'.$rowHFT->totalfv.'" /></td>
							<td><input class="editable" type="text" name="Face_Value'.$co.'" id="Face_Value'.$co.'" onKeyUp="check_value('.$co.'),calculateTotalAmnt()" style="width:95%;text-align:right" value="'.$rowHFT->repofv.'" ';
							
							if($varified == 1) { $str.=' readonly="" '; } 
							
							$str.=' />
							<input type="hidden" name="Cupon_Rate'.$co.'" id="Cupon_Rate'.$co.'" value="'.$rowHFT->cr.'" />
							</td>
							<td><input type="text" readonly="" name="Market_Yield'.$co.'" id="Market_Yield'.$co.'" style="text-align:right" size ="5" value="'.$my.'" /></td>
				
							<td><input type="text" readonly="true" name="yearFriction'.$co.'" id="yearFriction'.$co.'" style="width:95%;text-align:right" value="'.number_format($yearFriction,4,'.','').'" />
							<input type="hidden" name="Holding_Period'.$co.'" id="Holding_Period'.$co.'"  value="'.$hp.'" />
							<input type="hidden" name="HP_Interest'.$co.'" id="HP_Interest'.$co.'" value="'.$hpi.'" />
							<input type="hidden" name="Cost_Price_100'.$co.'" id="Cost_Price_100'.$co.'"  value="'.$price_100.'" />
							</td>				
							<td><input class="editable" ';
								if($als == '1' || $varified == 1) {$str.=' readonly="" '; }
							$str.=' type="text" name="cleanprice'.$co.'" id="cleanprice'.$co.'" style="width:95%;text-align:right" value="'.$MV.'" /></td>
							<td><input class="editable" ';
							
							if($als == '1' || $varified == 1) { $str.='readonly="" '; }
							
							$str.=' type="text" name="dirtyprice'.$co.'" id="dirtyprice'.$co.'" style="width:95%;text-align:right" value="'.($dirtyValue).'" onKeyUp="calculateTotalAmnt()" /></td>
							<td><input type="text" readonly="" value="HFT" style="width:95%;text-align:center"/>
							<input type="hidden" name="valuedt'.$co.'" id="valuedt'.$co.'" value="'.$repoVdt.'" />
							<input type="hidden" name="reference'.$co.'" value="'.$rowHFT->referenceID.'" />
							<input type="hidden" name="old_profit'.$co.'"  value="'.$rowHFT->sumprofit.'" />
							<input type="hidden" name="old_loss'.$co.'"  value="'.$rowHFT->sumloss.'" />
							<input type="hidden" name="old_total_profit'.$co.'" value="'.$rowHFT->tprofit.'" />
							<input type="hidden" name="old_total_loss'.$co.'"  value="'.$rowHFT->tloss.'" />
							<input type="hidden" name="old_adjust'.$co.'"  value="'.$rowHFT->sumadjust.'" />
							<input type="hidden" name="old_income'.$co.'" value="'.$rowHFT->sumincome.'" />
							<input type="hidden" name="old_provision'.$co.'"  value="'.$rowHFT->sumprovision.'" />
							<input type="hidden" name="old_prev_yield'.$co.'"  value="'.$rowHFT->prev_yield.'" />
							<input type="hidden" name="old_prev_cp100'.$co.'"  value="'.$rowHFT->prev_cp100.'" />
							<input type="hidden" name="old_prev_bv'.$co.'"  value="'.$rowHFT->totaloldbv.'" />
							<input type="hidden" name="old_purchase_cost_price'.$co.'"  value="'.$rowHFT->totalpurchase_cost_price.'" />
							<input type="hidden" name="old_pres_yield'.$co.'"  value="'.$rowHFT->prest_yield.'" />
							<input type="hidden" name="old_pres_cp100'.$co.'"  value="'.$rowHFT->prest_cp100.'" />
							<input type="hidden" name="old_pres_bv'.$co.'"  value="'.$rowHFT->totalpbv.'" />
							<input type="hidden" name="old_matu_dt'.$co.'"  value="'.$rowHFT->matu_dt.'" />
							<input type="hidden" name="old_buyid'.$co.'"  value="'.$rowHFT->buy_id.'" />
							<input type="hidden" name="old_revdt'.$co.'"  value="'.$rowHFT->rev_dt.'" />
							<input type="hidden" name="old_rra'.$co.'"  value="'.$rowHFT->totalwash.'" />
							<input type="hidden" name="old_prov_dt'.$co.'"  value="'.$rowHFT->prov_dt.'" />
							<input type="hidden" name="old_last_sts'.$co.'"  value="'.$rowHFT->last_sts.'" />
							<input type="hidden" name="old_sts'.$co.'"  value="'.$rowHFT->sts.'" />
							<input type="hidden" name="old_mmd_repo'.$co.'"  value="'.$rowHFT->mmd_repo_id.'" />
							<input type="hidden" name="old_als'.$co.'"  value="'.$rowHFT->als.'" />
							<input type="hidden" name="old_rev_e_by'.$co.'"  value="'.$rowHFT->rev_e_by.'" />
							<input type="hidden" name="old_rev_e_dt'.$co.'"  value="'.$rowHFT->rev_e_dt.'" />
							<input type="hidden" name="old_prov_e_by'.$co.'"  value="'.$rowHFT->prov_e_by.'" />
							<input type="hidden" name="old_prov_e_dt'.$co.'"  value="'.$rowHFT->prov_e_dt.'" />
							<input type="hidden" name="old_referid'.$co.'"  value="'.$rowHFT->ref_id.'" />
							<input type="hidden" name="edit_ref_id'.$co.'"  value="'.$rowHFT->edit_ref_id.'" />
							</td>
						</tr>';
						
					$co++;  
					}
					
					$resultbill = $this->mmd_deal_model->get_bill_query($repoVdt,$als,$id);
					$bc =10000;
					foreach($resultbill as $rowBill) // for hft bill
					{
						$objPHPExcel = new PHPExcel_Calculation_Financial();
						$mdate = $rowBill->maturity_dt;
						$sdate= $repoVdt;
						$cr = $rowBill->cr;
						$fv = $rowBill->repofv;
						$dtype = $rowBill->type;
						$tnor = $rowBill->tenor;

						$hp = floor((strtotime($mdate)-strtotime($sdate))/86400);	
						$hpi=0;	
				
						$yearFriction= $hp / 1;
						$tnor = $objPHPExcel->b2_get_tenor_from_yearfraction($yearFriction,$dtype);
						
						$my= $this->user_model->forcast_ex($sdate,$tnor,$yearFriction); //forcast
						if($rowBill->preyield != '') {$my = $rowBill->preyield;}
						$my=number_format($my,$p_dec_yield,'.','');
						
						$price_100= 100/(1 + (($my*$hp)/(100*365)));	
						if($rowBill->precp100 != '') {$price_100 = $rowBill->precp100;}
						$price_100=number_format($price_100,$p_dec_per100,'.','');
						
						$MV=($price_100*$fv)/100;
						$dirtyValue = $MV;
						if($rowBill->prebv != '') 
						{$MV = $rowBill->prebv; $dirtyValue =  $MV; }
						$MV=number_format($MV,2,'.','');
						
					
						$str.='<tr bgcolor="#ACE5EE" valign="top" id="row'.$bc.'"  style=" font-size:9pt; color:#000000">
							<td style="width:40px">
							<input type="checkbox" name="chkBoxSelect'.$bc.'" id="chkBoxSelect'.$bc.'" ';
							
							if ($varified == 1) {$str.=' onclick="return false" '; } else { $str.=' onClick="CheckChanged_2(),calculateTotalAmnt()" '; } 
							if($id != 0 && $id==$rowBill->edit_ref_id){$str.=' checked="checked" '; } 
							
							$str.=' />
							</td> 
							<td>
							<input type="text" readonly="" name="ISIN_Name'.$bc.'" id="ISIN_Name'.$bc.'" style="width:95%;text-transform:uppercase" value="'.$rowBill->isin_no.'" />
							<input type="hidden" name="Type_of_Deal'.$bc.'" id="Type_of_Deal'.$bc.'" value="'.$rowBill->type.'" /></td>
							<td><input type="text" name="TenorId'.$bc.'" readonly="" id="TenorId'.$bc.'" style="width:95%" value="'.$rowBill->tenor." ".$rowBill->day_year.'" />
							<input type="hidden" name="tenor'.$bc.'"  id="tenor'.$bc.'" style="width:95%" value="'.$rowBill->tenor.'" />
							</td>
							<td><input type="text" readonly="" name="Issue_Date'.$bc.'" id="Issue_Date'.$bc.'" style="width:95%" value="'.date("d/m/Y",strtotime($rowBill->issue_dt)).'" />
							</td>	
							<td><input type="text" readonly="" name="purDt'.$bc.'" id="purDt'.$bc.'" style="width:95%" value="'.date("d/m/Y",strtotime($rowBill->pur_dt)).'" />
							</td>	
							<td><input type="text" readonly="" name="matDt'.$bc.'" id="matDt'.$bc.'" style="width:95%" value="'.date("d/m/Y",strtotime($mdate)).'" />
							</td>
							<td><input type="text" readonly="" style="width:95%;text-align:right" name="old_fv'.$bc.'" id="old_fv'.$bc.'"  value="'.$rowBill->totalfv.'" /></td>
							<td><input class="editable" type="text" name="Face_Value'.$bc.'" id="Face_Value'.$bc.'" onKeyUp="check_value('.$bc.'),calculateTotalAmnt()" style="width:95%;text-align:right" value="'.$rowBill->repofv.'" ';
								 if($varified == 1) {$str.=' readonly="" '; }
								 
							$str.=' />
							<input type="hidden" name="Cupon_Rate'.$bc.'" id="Cupon_Rate'.$bc.'" value="'.$rowBill->cr.'" />
							</td>
							<td><input type="text" readonly="" name="Market_Yield'.$bc.'" id="Market_Yield'.$bc.'" style="text-align:right" size ="5" value="'.$my.'" /></td>
				
							<td><input type="text" readonly="true" name="yearFriction'.$bc.'" id="yearFriction'.$bc.'" style="width:95%;text-align:right" value="'.number_format($yearFriction,4,'.','').'" />
							<input type="hidden" name="Holding_Period'.$bc.'" id="Holding_Period'.$bc.'"  value="'.$hp.'" />
							<input type="hidden" name="HP_Interest'.$bc.'" id="HP_Interest'.$bc.'" value="'.$hpi.'" />
							<input type="hidden" name="Cost_Price_100'.$bc.'" id="Cost_Price_100'.$bc.'"  value="'.$price_100.'" />
							</td>				
							<td><input class="editable"  ';
								if($als == '1' || $varified == 1) {$str.=' readonly="" '; } 
								
							$str.=' type="text" name="cleanprice'.$bc.'" id="cleanprice'.$bc.'" style="width:95%;text-align:right" value="'.$MV.'" /></td>
							<td><input class="editable" '; 
								if($als == '1' || $varified == 1) {$str.=' readonly="" '; } 
							
							$str.=' type="text" name="dirtyprice'.$bc.'" id="dirtyprice'.$bc.'" style="width:95%;text-align:right" value="'.($dirtyValue).'" onKeyUp="calculateTotalAmnt()" /></td>
							<td><input type="text" readonly="" value="HFT" style="width:95%;text-align:center"/>
							<input type="hidden" name="valuedt'.$bc.'" id="valuedt'.$bc.'" value="'.$repoVdt.'" />
							<input type="hidden" name="reference'.$bc.'" value="'.$rowBill->referenceID.'" />
							<input type="hidden" name="old_profit'.$bc.'"  value="'.$rowBill->sumprofit.'" />
							<input type="hidden" name="old_loss'.$bc.'"  value="'.$rowBill->sumloss.'" />
							<input type="hidden" name="old_prev_yield'.$bc.'"  value="'.$rowBill->prev_yield.'" />
							<input type="hidden" name="old_prev_cp100'.$bc.'"  value="'.$rowBill->prev_cp100.'" />
							<input type="hidden" name="old_prev_bv'.$bc.'"  value="'.$rowBill->totaloldbv.'" />
							<input type="hidden" name="old_pres_yield'.$bc.'"  value="'.$rowBill->prest_yield.'" />
							<input type="hidden" name="old_pres_cp100'.$bc.'"  value="'.$rowBill->prest_cp100.'" />
							<input type="hidden" name="old_pres_bv'.$bc.'"  value="'.$rowBill->totalpbv.'" />
							<input type="hidden" name="old_purchase_cost_price'.$bc.'"  value="'.$rowBill->totalpurchase_cost_price.'" />
							<input type="hidden" name="old_matu_dt'.$bc.'"  value="'.$rowBill->matu_dt.'" />
							<input type="hidden" name="old_buyid'.$bc.'"  value="'.$rowBill->buy_id.'" />
							<input type="hidden" name="old_revdt'.$bc.'"  value="'.$rowBill->rev_dt.'" />
							<input type="hidden" name="old_last_sts'.$bc.'"  value="'.$rowBill->last_sts.'" />
							<input type="hidden" name="old_sts'.$bc.'"  value="'.$rowBill->sts.'" />
							<input type="hidden" name="old_mmd_repo'.$bc.'"  value="'.$rowBill->mmd_repo_id.'" />
							<input type="hidden" name="old_als'.$bc.'"  value="'.$rowBill->als.'" />
							<input type="hidden" name="old_rev_e_by'.$bc.'"  value="'.$rowBill->rev_e_by.'" />
							<input type="hidden" name="old_rev_e_dt'.$bc.'"  value="'.$rowBill->rev_e_dt.'" />
							<input type="hidden" name="old_referid'.$bc.'"  value="'.$rowBill->ref_id.'" />
							<input type="hidden" name="old_pres_amt'.$bc.'"  value="'.$rowBill->totaloldamt.'" />
							<input type="hidden" name="old_prev_amt'.$bc.'"  value="'.$rowBill->totalamt.'" />
							<input type="hidden" name="old_reversal_prof'.$bc.'"  value="'.$rowBill->totalreversal_profit.'" />
							<input type="hidden" name="old_reversal_loss'.$bc.'"  value="'.$rowBill->totalreversal_loss.'" />
							<input type="hidden" name="old_IDA'.$bc.'"  value="'.$rowBill->totalIDA.'" />
                            <input type="hidden" name="orginal_yield'.$bc.'"  value="'.$rowBill->orginal_yield.'" />
                            <input type="hidden" name="orginal_cost'.$bc.'"  value="'.$rowBill->orginal_cost.'" />
							<input type="hidden" name="edit_ref_id'.$bc.'"  value="'.$rowBill->edit_ref_id.'" />
							</td>
						</tr>';
					$bc++; 
					}
					
					$resulthtm = $this->mmd_deal_model->get_htm_query($repoVdt,$als,$id);
					$ac = 20000;
					foreach($resulthtm as $rowHTM) // for htm bond bill
					{
						$objPHPExcel = new PHPExcel_Calculation_Financial();
						$mdate = $rowHTM->maturity_dt;
						$sdate= $repoVdt;
						$cr = $rowHTM->cr;
						$fv = $rowHTM->repofv;
						$dtype = $rowHTM->ttype;
						$tnor = $rowHTM->tenor;
						
						if($dtype=='T.Bond'){
						$last_coupon_date2 = $objPHPExcel->COUPPCD($sdate,$mdate,2,$p_basis,1);
						$last_coupon_date= implode('-',array_reverse(explode('/',$last_coupon_date2)));
						$next_coupon_date2 = $objPHPExcel->COUPNCD($sdate,$mdate,2,$p_basis,1);
						$next_coupon_date= implode('-',array_reverse(explode('/',$next_coupon_date2)));
						$diffNcdPcd = $objPHPExcel->get_date_diff($next_coupon_date,$last_coupon_date);
						
						$hp = floor(strtotime($sdate)-strtotime($last_coupon_date))/86400;
						
						$hpi= $objPHPExcel->get_hp_interest($cr,$hp,$fv,$diffNcdPcd);
						$hpi=number_format($hpi,2,'.','');
						}
						else{
							$hp = floor((strtotime($mdate)-strtotime($sdate))/86400);	
							$hpi=0;	
						}
						
						if($dtype=='T.Bond')
						{	
							$sdate2 = date('d/m/Y',strtotime($sdate));
							$mdate2 = date('d/m/Y',strtotime($mdate));
							$yearFriction= $objPHPExcel->YEARFRAC($sdate2,$mdate2,$p_basis);
							
							$tnor = $objPHPExcel->b2_get_tenor_from_yearfraction($yearFriction,$dtype);
							$my= $this->user_model->forcast_ex($sdate,$tnor,$yearFriction); //forcast
							$my = number_format($my,$p_dec_yield,'.','');
					
							$price_100 = $objPHPExcel->PRICE($sdate,$mdate,($cr/100),($my/100),100,2,$p_basis);
							$price_100=number_format($price_100,$p_dec_per100,'.','');
								
							$MV=($price_100*$fv)/100;
							$MV=number_format($MV,2,'.','');
							$dirtyValue = $MV + $hpi;
							
						}else{
							$yearFriction= $hp / 1;
							$tnor = $objPHPExcel->b2_get_tenor_from_yearfraction($yearFriction,$dtype);
							
							$my= $this->user_model->forcast_ex($sdate,$tnor,$yearFriction); //forcast
							$my=number_format($my,$p_dec_yield,'.','');
							
							$price_100= 100/(1 + (($my*$hp)/(100*365)));
							$price_100=number_format($price_100,$p_dec_per100,'.','');
							$MV=($price_100*$fv)/100;
							$MV=number_format($MV,2,'.','');
							$dirtyValue = $MV + $hpi;
						}
					
					$str.='<tr valign="top" bgcolor="#FFFF99" id="row'.$ac.'"  style=" font-size:9pt; color:#000000;">
							<td style="width:40px">
							<input type="checkbox" name="chkBoxSelect'.$ac.'" id="chkBoxSelect'.$ac.'" ';
							
								if ($varified == 1) {$str.=' onclick="return false" '; } else {$str.=' onClick="CheckChanged_2(),calculateTotalAmnt()" '; } 
								if($id != 0 && $id==$rowHTM->edit_ref_id){ $str.=' checked="checked" '; }
								
							$str.=' />
							</td> 
							<td>
							<input type="text" readonly="" name="ISIN_Name'.$ac.'" id="ISIN_Name'.$ac.'" style="width:95%;text-transform:uppercase" value="'.$rowHTM->isin_no.'" />
							<input type="hidden" name="Type_of_Deal'.$ac.'" id="Type_of_Deal'.$ac.'" value="'.$rowHTM->type.'" /></td>
							<td><input type="text" name="TenorId'.$ac.'" readonly="" id="TenorId'.$ac.'" style="width:95%" value="'.$rowHTM->tenor." ".$rowHTM->day_year.'" />
							<input type="hidden" name="tenor'.$ac.'"  id="tenor'.$ac.'" style="width:95%" value="'.$rowHTM->tenor.'" />
							</td>
							<td><input type="text" readonly="" name="Issue_Date'.$ac.'" id="Issue_Date'.$ac.'" style="width:95%" value="'.date("d/m/Y",strtotime($rowHTM->issue_dt)).'" />
							</td>	
							<td><input type="text" readonly="" name="purDt'.$ac.'" id="purDt'.$ac.'" style="width:95%" value="'.date("d/m/Y",strtotime($rowHTM->pur_dt)).'" />
							</td>	
							<td><input type="text" readonly="" name="matDt'.$ac.'" id="matDt'.$ac.'" style="width:95%" value="'.date("d/m/Y",strtotime($mdate)).'" />
							</td>
							<td><input type="text" readonly="" style="width:95%;text-align:right" name="old_fv'.$ac.'" id="old_fv'.$ac.'"  value="'.$rowHTM->totalfv.'" /></td>
							<td><input class="editable" type="text" name="Face_Value'.$ac.'" id="Face_Value'.$ac.'" onKeyUp="check_value('.$ac.'),calculateTotalAmnt()" style="width:95%;text-align:right" value="'.$rowHTM->repofv.'" ';
							
							if($varified == 1) {$str.=' readonly="" '; }
							
							$str.=' />
							<input type="hidden" name="Cupon_Rate'.$ac.'" id="Cupon_Rate'.$ac.'" value="'.$rowHTM->cr.'" />
							</td>
							<td><input type="text" readonly="" name="Market_Yield'.$ac.'" id="Market_Yield'.$ac.'"  size ="5" style="text-align:right" value="'.$my.'" /></td>
				
							<td><input type="text" readonly="true" name="yearFriction'.$ac.'" id="yearFriction'.$ac.'" style="width:95%;text-align:right" value="'.number_format($yearFriction,4,'.','').'" />
							<input type="hidden" name="Holding_Period'.$ac.'" id="Holding_Period'.$ac.'"  value="'.$hp.'" />
							<input type="hidden" name="HP_Interest'.$ac.'" id="HP_Interest'.$ac.'" value="'.$hpi.'" />
							<input type="hidden" name="Cost_Price_100'.$ac.'" id="Cost_Price_100'.$ac.'"  value="'.$price_100.'" />
							</td>				
							<td><input class="editable" ';
								if($als == '1' || $varified == 1) {$str.=' readonly="" '; } 
							$str.=' type="text" name="cleanprice'.$ac.'" id="cleanprice'.$ac.'" style="width:95%;text-align:right" value="'.$MV.'" /></td>
							<td><input class="editable" '; 
							if($als == '1' || $varified == 1) {$str.=' readonly="" '; }
							$str.=' type="text" name="dirtyprice'.$ac.'" id="dirtyprice'.$ac.'" style="width:95%;text-align:right" value="'.($dirtyValue).'" onKeyUp="calculateTotalAmnt()" /></td>';
							$htm_txt='';
							if($rowHTM->rm_sts==1){
								$htm_txt='HTM(RM)';
							}else{
								$htm_txt='HTM';
							}
							$str.='<td><input type="text" readonly="" value="'.$htm_txt.'" style="width:95%;text-align:center"/>
							<input type="hidden" name="valuedt'.$ac.'" id="valuedt'.$ac.'" value="'.$repoVdt.'" />
							<input type="hidden" name="reference'.$ac.'" value="'.$rowHTM->referenceID.'" />
							<input type="hidden" name="old_profit'.$ac.'"  value="'.$rowHTM->sumprofit.'" />
							<input type="hidden" name="old_loss'.$ac.'"  value="'.$rowHTM->sumloss.'" />
							<input type="hidden" name="old_total_profit'.$ac.'" value="'.$rowHTM->tprofit.'" />
							<input type="hidden" name="old_total_loss'.$ac.'"  value="'.$rowHTM->tloss.'" />
							<input type="hidden" name="old_adjust'.$ac.'"  value="'.$rowHTM->sumadjust.'" />
							<input type="hidden" name="old_income'.$ac.'" value="'.$rowHTM->sumincome.'" />
							<input type="hidden" name="old_provision'.$ac.'"  value="'.$rowHTM->sumprovision.'" />
							<input type="hidden" name="old_prev_yield'.$ac.'"  value="'.$rowHTM->prev_yield.'" />
							<input type="hidden" name="old_prev_cp100'.$ac.'"  value="'.$rowHTM->prev_cp100.'" />
							<input type="hidden" name="old_prev_bv'.$ac.'"  value="'.$rowHTM->totaloldbv.'" />
							<input type="hidden" name="old_purchase_cost_price'.$ac.'"  value="'.$rowHTM->totalpurchase_cost_price.'" />
							<input type="hidden" name="old_pres_yield'.$ac.'"  value="'.$rowHTM->prest_yield.'" />
							<input type="hidden" name="old_pres_cp100'.$ac.'"  value="'.$rowHTM->prest_cp100.'" />
							<input type="hidden" name="old_pres_bv'.$ac.'"  value="'.$rowHTM->totalpbv.'" />
							<input type="hidden" name="old_pres_amt'.$ac.'"  value="'.$rowHTM->total_pres_amt.'" />
							<input type="hidden" name="old_prev_amt'.$ac.'"  value="'.$rowHTM->total_prv_amt.'" />
							<input type="hidden" name="old_in_de'.$ac.'"  value="'.$rowHTM->total_in_de.'" />
							<input type="hidden" name="old_cipy'.$ac.'"  value="'.$rowHTM->total_cipy.'" />
							<input type="hidden" name="old_matu_dt'.$ac.'"  value="'.$rowHTM->matu_dt.'" />
							<input type="hidden" name="old_buyid'.$ac.'"  value="'.$rowHTM->buy_id.'" />
							<input type="hidden" name="old_amdt'.$ac.'"  value="'.$rowHTM->am_dt.'" />
							<input type="hidden" name="old_rra'.$ac.'"  value="'.$rowHTM->totalwash.'" />
							<input type="hidden" name="old_prov_dt'.$ac.'"  value="'.$rowHTM->prov_dt.'" />
							<input type="hidden" name="old_last_sts'.$ac.'"  value="'.$rowHTM->last_sts.'" />
							<input type="hidden" name="old_sts'.$ac.'"  value="'.$rowHTM->sts.'" />
							<input type="hidden" name="old_mmd_repo'.$ac.'"  value="'.$rowHTM->mmd_repo_id.'" />
							<input type="hidden" name="old_als'.$ac.'"  value="'.$rowHTM->als.'" />
							<input type="hidden" name="old_am_e_by'.$ac.'"  value="'.$rowHTM->am_e_by.'" />
							<input type="hidden" name="old_am_e_dt'.$ac.'"  value="'.$rowHTM->am_e_dt.'" />
							<input type="hidden" name="old_prov_e_by'.$ac.'"  value="'.$rowHTM->prov_e_by.'" />
							<input type="hidden" name="old_prov_e_dt'.$ac.'"  value="'.$rowHTM->prov_e_dt.'" />
							<input type="hidden" name="old_referid'.$ac.'"  value="'.$rowHTM->ref_id.'" />
							<input type="hidden" name="edit_ref_id'.$ac.'"  value="'.$rowHTM->edit_ref_id.'" />
							</td>
						</tr>';
					$ac++; 
					}
					
					$str.='<input type="hidden" name="TotalcounterBond" id="TotalcounterBond" value="'.$co.'" />
					<input type="hidden" name="TotalcounterBill" id="TotalcounterBill" value="'.$bc.'" />
					<input type="hidden" name="TotalcounterHTM" id="TotalcounterHTM" value="'.$ac.'" />
					<input type="hidden" name="last_prov_dt" id="last_prov_dt" value="'.$last_prov_dt->last_prov_dt.'" />
														
					</tbody>
				</table><br/>
				
			</div>';
		echo $str;	
		
	}
	
	function get_cp_ssi($deal_serial=NULL)
	{
		$ncc='';
		$ncc_table='';
		//$d_info=$this->fex_deal_model->get_parameter_data('b2_fex_deal','Serial'," sts>'1' and Serial='".$deal_serial."'");
		$sl='<select name="our_ssi" id="our_ssi"  style="width:450px">
                <option  selected="selected" value="">Select Bank SSI</option>';
		$str="	SELECT 
				  b2ssi.id,
				  b2ssi.Number,
				  b2ssi.BankName 
				FROM
				  b2_mmdeal AS b2 
				  LEFT OUTER JOIN b2_account b2ssi 
				    ON ( b2.CurrencyId = b2ssi.CurrencyId ) 
				WHERE b2.Serial = '".$deal_serial."' and b2ssi.CurrencyId = b2.CurrencyId ";
				$q=$this->db->query($str);
				//echo $this->db->last_query();
					foreach($q->result() as $r)
					{			
						$sl.='<option value="'.$r->id.'">'.$r->BankName.'AC#'.$r->Number.'</option>';	
					}
		$sl.='</select>';

		// 		if($row->DealTypeId==2 && $row->TheirSettltement=='B')
		// 		{
		// 			$str="	SELECT cur.ISO_4217Code AS CurIso, cpssi.BankName AS ReceiveAtBName, cpssi.AccountNumber AS AccountNumber, cpssi.id AS SSIid,cPty.Name as CPname  
		// 			FROM 
		// 			(
		// 				SELECT  IF( WeHave='Sold',OurCurrencyId,CounterPartyCurrency) d_curId
		// 				FROM b2_fex_deal WHERE SERIAL='".$deal_serial."' AND sts>1 
		// 			) fed 
		// 			LEFT OUTER JOIN b2_counterpartyssi cpssi ON(fed.d_curId =cpssi.CurrencyId) 
		// 			left outer join b2_counterparty cPty on(cpssi.cp_id =cPty.id) 
		// 			LEFT OUTER JOIN b2_currency cur ON(fed.d_curId =cur.id) WHERE cpssi.sts='1' ";
		// 			$q=$this->db->query($str);
		// 			foreach($q->result() as $r)
		// 			{			
		// 				$sl.='<option value="'.$r->SSIid.'">'.$r->CPname.' SSI: '.$r->ReceiveAtBName.'&nbsp;-&nbsp;'.$r->CurIso.'&nbsp;-&nbsp;A/C:&nbsp;'.$r->AccountNumber.'</option>';	
		// 			}
					
		// 		}else{
		// 			$str="	SELECT cur.ISO_4217Code AS CurIso, cpssi.BankName AS ReceiveAtBName, cpssi.AccountNumber AS AccountNumber, cpssi.id AS SSIid 
		// 			FROM 
		// 			(
		// 				SELECT  CounterPartyId, IF( WeHave='Sold',OurCurrencyId,CounterPartyCurrency) d_curId
		// 				FROM b2_fex_deal WHERE SERIAL='".$deal_serial."' AND sts>1 
		// 			) fed 
		// 			LEFT OUTER JOIN b2_counterpartyssi cpssi ON(cpssi.cp_id=fed.CounterPartyId AND fed.d_curId =cpssi.CurrencyId) 					
		// 			LEFT OUTER JOIN b2_currency cur ON(fed.d_curId =cur.id) WHERE cpssi.sts='1' ";
		// 			$q=$this->db->query($str);
		// 			foreach($q->result() as $r)
		// 			{			
		// 				$sl.='<option value="'.$r->SSIid.'">'.$r->ReceiveAtBName.'&nbsp;-&nbsp;'.$r->CurIso.'&nbsp;-&nbsp;A/C:&nbsp;'.$r->AccountNumber.'</option>';	
		// 			}
		// 		}			
		// }
		// $sl.='</select>';		
		$var =array();  
		// $var['ncc_table']=$ncc_table;
		// $var['ncc']=$ncc;
		$var['row_info']=$sl;
		echo json_encode($var);
	}
	/// below codes related to add securities
// ***************************************** for re-repo *************************************************************	

	function securityfrom($id=NULL,$editrow=NULL,$varified=NULL)
	{
		$result = $this->mmd_deal_model->get_mmd_deal_data($id);
		$data = array( 	
				   'dealtype_list' => $this->mmd_deal_model->get_enum_data('b2_bond_bill_tenor','DealType'),
				   'id' => $id,
				   'dealresult' => $result,
				   'pages'=> 'mmd_deal/pages/securityform',
				   'editrow' => $editrow,
				   'varified' => $varified			   
				   );
		$this->load->view('user_info/form_layout',$data);
	}
	
	function get_tenor($dealtypevalue,$count)
	{
		if($dealtypevalue=='0'){return array();}
		$str_query = "SELECT TenorName, CONCAT(TenorName,' ',YearDays) as tenor FROM b2_bond_bill_tenor WHERE DealType = '".$dealtypevalue."'";
		$query=$this->db->query($str_query);
		$resultrow =$query->result();
		$optionstr = '<option value="0">Select</option>';
		foreach($resultrow as $row)
		{
			$optionstr.='<option value="'.$row->tenor.'">'.$row->tenor.'</option>';
		}
		echo $optionstr;
	}
	function get_mdate($issuedt,$type,$tenor,$count)
	{
		include('./application/Classes/PHPExcel.php');
		include('./application/Classes/PHPExcel/Calculation/Financial.php');
		$objPHPExcel = new PHPExcel_Calculation_Financial();
		$issuedt = implode('-',array_reverse(explode('-',$issuedt)));
		$arr_tenor = explode('%2', $tenor);
		$tenor = $arr_tenor[0];
		echo $matdate = $objPHPExcel->get_maturity_dt($issuedt,$tenor,$type);
	}
	
	function calculation()
	{
		$postdata = json_decode($this->input->post('updateFields'),true);
		//print_r($postdata);
		$arr_tenor = explode(' ', $postdata[0]['tenor']);
		$dtype = $postdata[0]['dealType'];
		$tnor = $arr_tenor[0];
		$idate = $postdata[0]['issue_date'];
		$fv = $postdata[0]['face_value'];
		$cr = $postdata[0]['coupon_rate'];
		$basis_dl = intval($postdata[0]['BASIS']);
		$per_100_dl = $postdata[0]['per_100_dl'];
		$sdate= $postdata[0]['setteDat'];
		$yield_dl=$postdata[0]['yield_dl'];
		
		$calResult= $this->mmd_deal_model->rrCalculator($dtype,$tnor,$idate,$fv,$cr,$basis_dl,$per_100_dl,$sdate,$yield_dl);
		$arr_result = explode('#',$calResult);
		
		$arr_json = array();
		$arr_json['MV'] = $arr_result[0];
		$arr_json['MY'] = $arr_result[1];
		$arr_json['dirty'] = $arr_result[0] + $arr_result[2];
		$arr_json['hpi'] = $arr_result[2];
		$arr_json['cp100'] = $arr_result[3];
		
		echo json_encode($arr_json);
	}
	
	function exfileup()
	{
		$config['upload_path'] = "./excel/";
		//$config['allowed_types'] = 'xlsx|xls';
		$config['allowed_types'] = '*';
		$config['overwrite'] = TRUE;
		$config['max_size']	= '1000KB';

		$this->load->library('upload', $config);
		if ( ! $this->upload->do_upload('reversefile'))
		{
			$error = array('error' => $this->upload->display_errors());
			print_r($error);exit;
		}
		else
		{
			$data = $this->upload->data();
		}

		$qresult = $this->mmd_deal_model->fupload($data);
		$var =  
			array(
				"data"=>$qresult
			);
			echo json_encode($var);

	}
	
	function addRRepoSecuirities()
	{
		$text=array();
		if ($this->session->userdata['user']['login_status'])
		{
			$id=$this->mmd_deal_model->add_rreposecurities();
		}
		else{
			$text[]="Session out, login required";
		}	
		
		$Message='';
		if(count($text)<=0){
			$Message='OK';
		}
				
		$var =array();  
		$var['Message']=$Message;
		echo json_encode($var);
	}

// *********************************************** for repo *********************************************************	
	
	function reposecurityfrom($id=NULL,$editrow=NULL,$varified=NULL)
	{
		$get_hft_query = $this->mmd_deal_model->get_hft_query($id);
		$get_bill_query = $this->mmd_deal_model->get_bill_query($id);
		$get_htm_query = $this->mmd_deal_model->get_htm_query($id);
		$data = array( 	
				   'id' => $id,
				   'pages'=> 'mmd_deal/pages/reposecurityform',
				   'editrow' => $editrow,
				   'result' => $get_hft_query,
				   'resultbill' => $get_bill_query,
				   'resulthtm' => $get_htm_query,
				   'varified' => $varified
				   );
		$this->load->view('user_info/form_layout',$data);
	}
	
	function addRepoSecuirities()
	{
		$Message='';
		if ($this->session->userdata['user']['login_status'])
		{
			$Message='OK';
		}
		else{
			$Message="Session out, login required";
		}	
		
		if($Message=='OK')
		{
			$id=$this->mmd_deal_model->add_reposecurities();
			if($id == '0')
			{
				$Message="Sorry, could not updated";
			}
		}
				
		$var =array();  
		$var['Message']=$Message;
		echo json_encode($var);
	}
	
	function repoCalculation()
	{
		include('./application/Classes/PHPExcel.php');
		include('./application/Classes/PHPExcel/Calculation/Financial.php');
		
		$postdata = json_decode($this->input->post('updateFields'),true);
		$dtype = $postdata[0]['dealType'];
		$tnor = $postdata[0]['tenor'];
		$mdate = implode('-',array_reverse(explode('/',$postdata[0]['maturity_date'])));
		$fv = $postdata[0]['face_value'];
		$cr = $postdata[0]['coupon_rate'];
		$basis_dl = intval($postdata[0]['BASIS']);
		$per_100_dl = $postdata[0]['per_100_dl'];
		$sdate= $postdata[0]['value_date'];
		$yield_dl=$postdata[0]['yield_dl'];
		
		$objPHPExcel = new PHPExcel_Calculation_Financial();

		if($dtype=='T.Bond'){
			$last_coupon_date2 = $objPHPExcel->COUPPCD($sdate,$mdate,2,$basis_dl,1);
			$last_coupon_date= implode('-',array_reverse(explode('/',$last_coupon_date2)));
			$next_coupon_date2 = $objPHPExcel->COUPNCD($sdate,$mdate,2,$basis_dl,1);
			$next_coupon_date= implode('-',array_reverse(explode('/',$next_coupon_date2)));
			$diffNcdPcd = $objPHPExcel->get_date_diff($next_coupon_date,$last_coupon_date);
			
			$hp = floor(strtotime($sdate)-strtotime($last_coupon_date))/86400;
			
			$hpi= $objPHPExcel->get_hp_interest($cr,$hp,$fv,$diffNcdPcd);
			$hpi=number_format($hpi,2,'.','');
		}
		else{
			$hp = floor((strtotime($mdate)-strtotime($sdate))/86400);	
			$hpi=0;	
		}
		if($dtype=='T.Bond')
		{	
			$sdate2 = date('d/m/Y',strtotime($sdate));
			$mdate2 = date('d/m/Y',strtotime($mdate));
			$yearFriction= $objPHPExcel->YEARFRAC($sdate2,$mdate2,$basis_dl);
			
			$tnor = $objPHPExcel->b2_get_tenor_from_yearfraction($yearFriction,$dtype);
			$my= $this->user_model->forcast_ex($sdate,$tnor,$yearFriction); //forcast
			if($yield_dl!=''){$my = number_format($my,$yield_dl,'.','');}
	
			$price_100 = $objPHPExcel->PRICE($sdate,$mdate,($cr/100),($my/100),100,2,$basis_dl);
			if($per_100_dl!=''){$price_100=number_format($price_100,$per_100_dl,'.','');}
				
			$MV=($price_100*$fv)/100;
			$MV=number_format($MV,2,'.','');
			
		}else{
			$yearFriction= $hp / 1;
			$tnor = $objPHPExcel->b2_get_tenor_from_yearfraction($yearFriction,$dtype);
			
			$my= $this->user_model->forcast_ex($sdate,$tnor,$yearFriction); //forcast
			if($yield_dl!=''){$my=number_format($my,$yield_dl,'.','');}
			
			$price_100= 100/(1 + (($my*$hp)/(100*365)));
			if($per_100_dl!=''){$price_100=number_format($price_100,$per_100_dl,'.','');}
			$MV=($price_100*$fv)/100;
			$MV=number_format($MV,2,'.','');
		}
		
		$arr_json = array();
			$arr_json['MV'] = $MV;
			$arr_json['MY'] = $my;
			$arr_json['dirty'] = $MV + $hpi;
			$arr_json['yf'] = number_format($yearFriction,3,'.','');
			$arr_json['hp'] = $hp;
			$arr_json['hpi'] = $hpi;
			$arr_json['cp100'] = $price_100;
			
			echo json_encode($arr_json);		
			
	}
	
// *********************************************** for ALS *********************************************************	
	
	function viewAls($id=NULL,$editrow=NULL,$sts)
	{
		$data = array( 	
				   'id' => $id,
				   'pages'=> 'mmd_deal/pages/alsform',
				   'editrow' => $editrow,
				   'alsresult' => $this->mmd_deal_model->get_parameter_data('b2_als','id',"mmd_id = ".$id.""),
				   'sts' => $sts
				   );

		$this->load->view('user_info/form_layout',$data);
	}
	
	function addAls()
	{
		$Message='';
		if ($this->session->userdata['user']['login_status'])
		{
			$Message='OK';
		}
		else{
			$Message="Session out, login required";
		}	
		
		if($Message=='OK')
		{
			$id=$this->mmd_deal_model->add_alsInfo();
			if($id == '0')
			{
				$Message="Sorry, could not updated";
			}
		}
				
		$var =array();  
		$var['Message']=$Message;
		echo json_encode($var);
	}

	function checkHolyday(){
		$v_date = date('Y-m-d',strtotime(str_replace('/', '-', $this->input->post('MaturityDate'))));						
		$dr_msg=$this->user_model->nostro_holiday(30,$v_date);
		echo $dr_msg;
	}

	function frontVerifySelected(){
		$Message = $this->mmd_deal_model->frontVerifySelected();

		$data = array('Message' => $Message);
		echo json_encode($data);
	}
}
?>