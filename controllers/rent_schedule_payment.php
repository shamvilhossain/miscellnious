<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class rent_schedule_payment extends CI_Controller {

	function __construct()
    {
        parent::__construct();			
		$this->output->set_header('Last-Modified:'.gmdate('D, d M Y H:i:s').'GMT');
		$this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate');
		$this->output->set_header('Cache-Control: post-check=0, pre-check=0',false);
		$this->output->set_header('Pragma: no-cache');		
		//$this->load->model('', '', TRUE);
		$this->load->model('agreement_model', '', TRUE);
		$this->load->model('rent_security_deposit_model', '', TRUE);
		$this->load->model('rent_schedule_payment_model', '', TRUE);
		
	}
	function view ($menu_group,$menu_cat)
	{		
		$data = array( 	
					'menu_group'=> $menu_group,
					'menu_cat'=> $menu_cat,
					'pages'=> 'rent_schedule_payment/pages/grid',			   			   
				   	'per_page' => $this->config->item('per_pagess')
				   );
		$this->load->view('grid_layout',$data);
	}
	function grid()
	{		
		$this->load->model('', '', TRUE);
		$pagenum = $this->input->get('pagenum');
		$pagesize = $this->input->get('pagesize');
		$start = $pagenum * $pagesize;
		
		$result=$this->rent_schedule_payment_model->get_grid_data($this->input->get('filterscount'), $this->input->get('sortdatafield'), $this->input->get('sortorder'),$pagesize, $start);
		
				
		$data[] = array(
		   'TotalRows' => $result['TotalRows'],		   
		   'Rows' => $result['Rows']
		);		
		echo json_encode($data);		
	}	
	function from($add_edit='add',$id=NULL,$editrow=NULL,$type=NULL)
	{

		$security_deposit=array();
		$str1='';
		$str2='';
		$str='';
		$sd_cat=$this->config->item('SD_gl_cat');
		$cash_cat=$this->config->item('cash_gl_cat');
		$this->load->model('rent_schedule_payment_model', '', TRUE);
		$this->load->model('agreement_model', '', TRUE);
		if($add_edit!='finance_verify' && $add_edit!='fin'){
		if($add_edit=='edit'){
			
		}

			$result=$this->rent_schedule_payment_model->get_info($add_edit,$id);
		
		} 
		else {  //finance_verify (approve) or fin
			$r=$this->rent_schedule_payment_model->check_status($id);
				if($r==0){
					$str .='<div align="center" ><h1 style="color:#ff0000;" >Sorry !!! </h1> <br> <h2>Entry Already Deleted... </h2></div>';
					echo $str ;
					die();
				}else{
					//$result=$this->rent_schedule_payment_model->get_verify_info($add_edit,$id);
					$single_paid_data=$this->rent_schedule_payment_model->get_single_paid_data($id);
					$location_type_data=$this->rent_schedule_payment_model->get_location_type_data($single_paid_data->agreement_id);
					$others_type_data=$this->rent_schedule_payment_model->get_others_type_data($single_paid_data->agreement_id);
					$others_type_names=$this->rent_schedule_payment_model->get_others_type_names($single_paid_data->agreement_id);
					$landlords_result = $this->rent_schedule_payment_model->rent_landlords_get_info($single_paid_data->agreement_id);
                    $result_agree_info = $this->rent_schedule_payment_model->get_single_row_info('rent_agreement',$single_paid_data->agreement_id);
                    $cost_center_name= $this->agreement_model->get_single_cost_center_info($result_agree_info->agree_cost_center);
                    $others_no_tax_amount = $this->agreement_model->get_others_no_tax_amount($single_paid_data->agreement_id);
                    $loc_type_no_tax_percent = $this->rent_schedule_payment_model->get_loc_type_no_tax_percent($single_paid_data->agreement_id);
                    
					$prov_info='';
					
					// 5 sep
					$non_prov_paid_data=$this->rent_schedule_payment_model->get_non_prov_paid_data($single_paid_data->agreement_id, $single_paid_data->checked_schedule_ids);
					//$prov_paid_data=$this->rent_schedule_payment_model->get_prov_paid_data($single_paid_data->agreement_id, $single_paid_data->checked_schedule_ids);
					
				}
		}

		if($add_edit=='add'){
			$data1=array(
				'pages'=> 'rent_schedule_payment/pages/form',
				'rent_data'=> $this->rent_schedule_payment_model->get_rent_data(),

				);

		}elseif ($add_edit=='edit') {
		
		if($result){
			$result_agree_info = $this->rent_schedule_payment_model->get_single_row_info('rent_agreement',$result->agreement_id);
			$schedule_paid_info = $this->rent_schedule_payment_model->get_single_row_info('rent_paid_history',$result->id);
			$sche_checked_schedule_ids =explode(',' , $schedule_paid_info->checked_schedule_ids);
			$schedule_id_count =count($sche_checked_schedule_ids);
			$schedule_sd_amt_per_id = $schedule_paid_info->sd_adjust_amt/$schedule_id_count ;
			$rent_schedule_info = $this->agreement_model->rent_matured_unpaid_payment_schedule_info_for_payment_edit($result->agreement_id); 
			$schedule_row_count =count($rent_schedule_info);
			$cost_center_name= $this->agreement_model->get_single_cost_center_info($result_agree_info->agree_cost_center);
			// echo'<pre>';
			
		}
			$data1=array(
	     	'pages'=> 'rent_schedule_payment/pages/edit_form',
	     	'result_agree_info'=>$result_agree_info,
	     	'rent_schedule_info'=>$rent_schedule_info,
	     	'schedule_paid_info'=>$schedule_paid_info,
	     	'sche_checked_schedule_ids'=>$sche_checked_schedule_ids,
	     	'paid_schedule_id_count'=>$schedule_id_count,
	     	'schedule_sd_amt_per_id'=>$schedule_sd_amt_per_id,
	     	'schedule_row_count'=>$schedule_row_count,
	     	'cost_center_name'=>$cost_center_name,

	     
	     	);
     	
		}elseif ($add_edit=='fin') {
		// verify

			$data1=array(
				//'pages'=> 'rent_schedule_payment/pages/verify_form1',
				//'pages'=> 'rent_schedule_payment/pages/verify_form',
				'pages'=> 'rent_schedule_payment/pages/approve_form',
				'rent_data'=> $this->rent_schedule_payment_model->get_rent_data(),
				'single_paid_data' => $single_paid_data,			   
				'location_type_data' => $location_type_data,			   
				'others_type_data' => $others_type_data,
				'others_type_names' => $others_type_names,
				'landlords_result' => $landlords_result,
                'result_agree_info'=>$result_agree_info,  
                'cost_center_name'=>$cost_center_name,
                'non_prov_paid_data'=>$non_prov_paid_data,
				'prov_result' => $prov_info,
				'others_no_tax_amount' => $others_no_tax_amount,
				'loc_type_no_tax_percent' => $loc_type_no_tax_percent,
				'verify_type'=>$type // fin_verify/view

				);

		}
		else{ // approve
		
			$data1=array(
				//'pages'=> 'rent_schedule_payment/pages/verify_form1',
				'pages'=> 'rent_schedule_payment/pages/approve_form',
				'rent_data'=> $this->rent_schedule_payment_model->get_rent_data(),
				'single_paid_data' => $single_paid_data,			   
				'location_type_data' => $location_type_data,			   
				'others_type_data' => $others_type_data,
				'others_type_names' => $others_type_names,
				'landlords_result' => $landlords_result,
                'result_agree_info'=>$result_agree_info,  
                'cost_center_name'=>$cost_center_name,
                'non_prov_paid_data'=>$non_prov_paid_data,
				'prov_result' => $prov_info,
				'others_no_tax_amount' => $others_no_tax_amount,
				'loc_type_no_tax_percent' => $loc_type_no_tax_percent,
				'verify_type'=>$type // fin_verify/view

				);
			
		}
	
		$data = array( 	
		   'option' => '',
		   'add_edit' => $add_edit,
		   'id' => $id,

		   'type'=>$type,		
		
		   'vat_percentage' =>$this->rent_security_deposit_model->get_parameter_data_single('ref_rent_vat','id',''), 
		   'tax_percentage' =>$this->rent_security_deposit_model->get_parameter_data_single('ref_rent_tax','id',''), 
		   'sd_gl' =>$this->rent_security_deposit_model->get_parameter_data_where_in('ref_rent_single_gl','name',2), 
		   'rent_gl' =>$this->rent_security_deposit_model->get_parameter_data_where_in('ref_rent_single_gl','name',4),
		   'provision_gl' =>$this->rent_security_deposit_model->get_parameter_data_where_in('ref_rent_single_gl','name',3),
		   'advance_gl' =>$this->rent_security_deposit_model->get_parameter_data_where_in('ref_rent_single_gl','name',1), 
		   'vat_gl' =>$this->rent_security_deposit_model->get_parameter_data_where_in('ref_rent_single_gl','name',5), 
		   'tax_gl' =>$this->rent_security_deposit_model->get_parameter_data_where_in('ref_rent_single_gl','name',6), 
		   'cash_gl' =>$this->rent_security_deposit_model->get_parameter_data_where_in('ref_rent_single_gl','name',7), 
		   'godown_gl' =>$this->rent_security_deposit_model->get_parameter_data_where_in('ref_rent_single_gl','name',8), 
		   //'debit_gl' =>$this->rent_security_deposit_model->get_parameter_data_where_in('ref_rent_single_gl','name',$result_agree_info->debit_account), 
		
		   //'pages'=> 'rent_security_deposit/pages/form',
		   'editrow' => $editrow
	   
		);
		
		$data=array_merge($data,$data1);
		$this->load->view('rent_schedule_payment/form_layout',$data);
	}

	function add_edit_action($add_edit=NULL,$edit_id=NULL)
	{
		
		$text=array();
		if ($this->session->userdata['user']['login_status'])
		{
			$id=$this->rent_schedule_payment_model->add_edit_action($add_edit,$edit_id);

		}
		else{
			$text[]="Session out, login required";
		}
	
		$Message='';
		$row=array();
		if(count($text)<=0){
			if($id=='0'){
					$Message='Failed';
			}else if($id=='-1'){
					$Message='Verified';
			}else{
			$Message='OK';
			$row=$this->rent_schedule_payment_model->get_add_action_data($id);
		}
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


	function adjust_preview(){
		$vendor= $this->input->post('vendor');
		$id= $this->input->post('id');
		$this->load->model('', '', TRUE);
		$preview=$this->rent_security_deposit_model->get_adjust_preview($id,$vendor);

		$str='';
		//$i=1;
       	$m=00;
		$str .='<div align="center"><h3 style="text-align:center;"><u>Adjusted Bill Information</u></h3></div>
				<br><table width="95%" cellspacing="0" class="service_style" border="1"  style="border-collapse:collapse;padding-left:5px;" >
							<thead>
								<tr>

								<th align="center"  width="5%">SL <span style="color:#FF0000"></span></th>
								<th align="left"  width="20%">SD Reference <span style="color:#FF0000"></span></th>
								<th align="right" width="15%">Adjust Amount <span style="color:#FF0000"></span></th>
								<th align="left" width="15%">Adjust by <span style="color:#FF0000"></span></th>
								<th align="left" width="15%">Adjust Date<span style="color:#FF0000"></span></th>
							
								</tr>
							</thead><tbody>';
		foreach ($preview as $row) { 
			$m++;
				$adjust=$row->checked_adjust_amount;
				$un_var=explode(',',$adjust);
				for($i=0;$i<count($un_var);$i++){
				
					$un_id_amount=explode('#',$un_var[$i]);
					
					$adjust_bill_id =$un_id_amount[0];
					$adjust_bill_amount =$un_id_amount[1];
					if($adjust_bill_id==$id){
						break;
					}
				}
				if($adjust_bill_id==$id){
					//if($add_edit=='add'){
			    
    			  $str .='<tr id="tr'.$i.'">
    				
						<td   width="5%" style="text-align:center;">'.$m .'</td>
						<td   width="20%" >'.$row->bill_ref_no .'</td>
						<td   width="15%" style="text-align:right;">'.number_format($adjust_bill_amount,2,'.','').'</td>
						<td   width="15%" style="text-align:left;">'.$row->name .'</td>
						<td   width="15%" style="text-align:left;">'.$row->date_added .'</td>
						
					</tr>';
				}
		$i++;
		}
		$str .='</tbody></table>';
		$row_count=$i-1;
		$str .= '<input name="row_count" type="hidden" id="row_count" value="'.$row_count.'"  class="text-input-small" />';
			echo $str;

	}
	function set_popup($add_edit=NULL,$editrow=NULL)
	{
	
		$this->load->model('', '', TRUE);
		$preview=$this->rent_security_deposit_model->get_preview($add_edit,$editrow);
		$base_url=$this->config->item('base_url');

		$str='<div align="center"><p style="text-align:center;font-size:17px;color:black;"><b><u>Security Deposit Refund</u></b></p></div>';
		$i=1;
       
		$str .='
				<table width="99%" cellspacing="0"  border="1"  style="border-collapse:collapse" >
							<thead>
								<tr style="background-color:#e0e0d1;">
									
								<th align="center" width="2%"> Select <span style="color:#FF0000"></span></th>
								<th align="center" width="15%">SD Reference <span style="color:#FF0000"></span></th>
								<th align="right"  width="15%">T. Amount <span style="color:#FF0000"></span></th>
								<th align="right"  width="15%">T. Adjust <span style="color:#FF0000"></span></th>
								<th align="right"  width="15%">Rest Amount <span style="color:#FF0000"></span></th>
								<th align="right"  width="15%">Adjust amount <span style="color:#FF0000"></span></th>
								<th align="center" width="30%">Particulars <span style="color:#FF0000"></span></th>
								<th align="center" width="30%">View <span style="color:#FF0000"></span></th>
								
									
								</tr>
							</thead>';
		foreach ($preview as $row) { 
			//if($add_edit=='add'){
			      $amount_f=$row->amount;
			      $amount_p=$row->adjust_amount;
			      $amount_d=$row->amount-$row->adjust_amount;
			  //   }
			   
			      $set=0;
    			  $str .='<tbody><tr id="tr'.$i.'">
    					<input name="id" type="hidden" id="'.$row->id.'" value="'.$i.'"  class="text-input-small" />
    					<input name="id" type="hidden" id="id'.$i.'" value="'.$row->id.'"  class="text-input-small" />
    					
						<td id="check_box'.$i.'" width="2%" ><input type="checkbox" id="check'.$i.'" name="check" value="check'.$i.'"></td>
						<td id="bill_ref_no'.$i.'"  width="15%" >'.$row->bill_ref_no.'</td>
						<input name="amount_f'.$i.'" type="hidden" id="amount_f'.$i.'" value="'.$amount_f.'"  class="text-input-small" />
						<input name="hidden_amount'.$row->id.'" type="hidden" id="hidden_amount'.$row->id.'" value="'.$set.'"  class="text-input-small" />
						
						<td id="amount_pop'.$row->id.'"  width="15%" style="text-align:right;"><div id="amount_pop'.$row->id.'" >'.number_format($amount_f,2,'.','').'</div></td>
						<td id="amount_adj'.$row->id.'"  width="15%" style="text-align:right;"><div id="amount_adj'.$row->id.'" >'.number_format($amount_p,2,'.','').'</div></td>
						<td id="amount_diff'.$row->id.'"  width="15%" style="text-align:right;"><div id="amount_diff'.$row->id.'" >'.number_format($amount_d,2,'.','').'</div></td>
						<td id="adjust_amount'.$i.'"  width="15%" style="text-align:right;"><input name="amount_a'.$i.'" type="text" id="amount_a'.$i.'" value="'.$set.'"  class="text-input-small" style="text-align:right;width:100px;"/></td>
						<td id="particulars'.$i.'"  width="30%" >'.$row->particulars.'</td>';
						if($amount_p==0.00){
				 $str .='<td>&nbsp;</td>';		
						}else{
				 $str .='<td><div style="text-align:center;  cursor:pointer" onclick="preview_item('.$row->vendor.','.$row->id.')" ><img align="center" src="'.$base_url.'images/view_detail.png"></div></td>';
						
						}
				$str .='<input name="count'.$i.'" type="hidden" id="count'.$i.'" value="'.$i.'"  class="text-input-small" />
					</tr></tbody>';
		$i++;
		}
		$str .='</table>';
		$row_count=$i-1;
		$str .= '<input name="row_count" type="hidden" id="row_count" value="'.$row_count.'"  class="text-input-small" />';
			echo $str;
	}
	function get_child_list()
	{
		$security_deposit=array();
		$this->load->model('', '', TRUE);
		$result = $this->rent_security_deposit_model->get_child_list('ref_child_gl','name');
		$var =array();  
		
		foreach($result as $value){ 
		 $var[] = array(
		 	'value'=>$value->accountnumber, 
		 	'label'=>$value->name
		 	);
		}	 
		echo json_encode($var);
	}




function get_matured_schedule_info_for_payment()
{
	
		$this->load->model('agreement_model', '', TRUE);

		$row_serial = $this->input->post('row_serial');
		$rent_id = $this->input->post('rent_id');
		//$result = $this->agreement_model->rent_matured_unpaid_payment_schedule_info($this->input->post('rent_id')); // 0
		$result = $this->agreement_model->rent_matured_unpaid_payment_schedule_info_for_payment($this->input->post('rent_id')); // 0
		$rent_agreement_row_data = $this->agreement_model->get_add_action_data($this->input->post('rent_id'));
		$rent_adjust_data = $this->agreement_model->single_rent_adjustment_get_info('',$this->input->post('rent_id'));
		$others_no_tax_amount = $this->agreement_model->get_others_no_tax_amount($this->input->post('rent_id'));
		$date_diff =  $this->dateDiff($rent_agreement_row_data->agree_exp_dt, $rent_agreement_row_data->rent_start_dt);
		$start_date=date_create($rent_agreement_row_data->rent_start_dt);
		$end_date=date_create($rent_agreement_row_data->agree_exp_dt);
		$point_of_payment=$rent_agreement_row_data->point_of_payment;
		$unadjust = $rent_agreement_row_data->total_advance;
		//$tax_rate = $this->agreement_model->get_parameter_data_single('ref_rent_tax','id','');
		$tax_slab_rate = $this->agreement_model->get_tax_slab_rate();
        $slab_count= count($tax_slab_rate);

		if($point_of_payment=='cm'){ $pp_str = 'Current Month';}
			else{ $pp_str = 'Following Month';}
		$html= ''; 
		
		
		$html.= '

		<style>
		table {
		    width:100%;
		}
		table, th, td {
		    border: 1px solid black;
		    border-collapse: collapse;
		}
		th, td {
		    padding: 5px;
		    text-align: left;
		}
		table#t01 tr:nth-child(even) {
		    background-color: #eee;
		}
		table#t01 tr:nth-child(odd) {
		   background-color:#fff;
		}
		table#t01 th	{
		    background-color: olive;
		    color: white;
		}
		.summery_class{
			margin-bottom: 3px !important;
		    text-align: left;
		}
		.border_none{
			border:none !important;

		}
		</style>

		'; 

		$incr_type= '';
	    $incr_type_val= $rent_agreement_row_data->increment_type;
	    if($incr_type_val==1){$incr_type='No Increment';}
	    elseif($incr_type_val==2){$incr_type='Every '.$rent_agreement_row_data->increment_type_val.' Yearly Basis';}
	    elseif($incr_type_val==3){$incr_type='Only One Time';}
	    elseif($incr_type_val==4){$incr_type='Fixed Increment setup';}

		 $cost_center_name= $this->agreement_model->get_single_cost_center_info($rent_agreement_row_data->agree_cost_center);

		$html.= '<p class="summery_class" ><b>Payment Summery</b></p>'; 
		$html.= '<p class="summery_class"><b>Rent Reference No: </b>'.$rent_agreement_row_data->agreement_ref_no.'</p>';
		$html.= '<p class="summery_class"><b>Rent Duration :</b> '.date_format($start_date,"d/m/Y").' to '.date_format($end_date,"d/m/Y").' ('.$date_diff.')</p>'; 
	    
	    $html.= '<p class="summery_class"><b>Payment Type: </b>'.$pp_str.' Basis</p>';
	    
        $html.= '<p class="summery_class"><b>Cost Center: </b>'.$cost_center_name->name.'</p>';
        $html.= '<p class="summery_class"><b>Location : </b>'.$rent_agreement_row_data->location_name.'</p>';
        $html.= '<p class="summery_class"><b>Landlord(s) : </b>'.$rent_agreement_row_data->landlord_names.'</p>';
        $html.= '<p class="summery_class"><b>Increment Type : </b>'.$incr_type.'</p>';
	//	$html.= '<p class="summery_class"><b>10% increment every 02 years</b></p>'; 
		$html.= '<p class="summery_class"><b>Monthly Rent :</b> '.$rent_agreement_row_data->monthly_rent.'</p>'; 
		$html.= '<p class="summery_class"><b>Advance payment : </b>'.$rent_agreement_row_data->total_advance.'</p>'; 
		$html.= '<p class="summery_class" style="display:none;"><b>Monthly Adjustment :</b> '.$rent_adjust_data->percent_dir_val.'</p>'; 
		$html.= '<input type="hidden" value="'.$row_serial.'" id="row_serial">'; 
		$html.= '<input type="hidden" value="'.$rent_id.'" id="rent_id">'; 
   		$html .='<div style="text-align:center;  cursor:pointer" onclick="sd_preview_item('.$rent_id.')" ><img align="center" src="'.base_url().'images/view_detail.png">    <input name="new_sd_amount'.$rent_id.'" type="text"  id="new_sd_amount'.$rent_id.'" value="0.00" class="text-input-small " placeholder="SD Adjust" readonly /></div><br >';
        $html.= '<br />'; 
		$html .='<table class="" id="t01" style="border:solid 1px black; text-align: center; width:97%; margin:20px;">';
	
	      
		$html .='<tr class="headrow" style="text-align:center;font-weight:bold;background-color:#C5C5C5; border: 1px solid black;">
		<th style="text-align:center;">Payment Select</th>
		<th style="text-align:center;">Payment Status</th>
		<th style="text-align:center;">Expected payment date</th>
		<th style="text-align:center;"> Monthly Rent</th>
		<th style="text-align:center;"> Others </th>
		<th style="text-align:center;"> Arear Amount </th>
		<th style="text-align:center;"> Adjustment</th>	
		<th style="text-align:center;"> SD</th>	
		<th style="text-align:center; display:none;" > Provision Adjust</th>
		<th style="text-align:center;"> Tax </th>
		<th style="text-align:center;"> Net Payment </th>
		<th style="text-align:center; display:none;"> Unadjusted Advance rent</th>
		<th style="text-align:center;"> Remarks</th>';

		$html .='<tbody id="">';
		$i=1;
		$prov_amount= 0.00;
		foreach($result as $row){ 

				// 20 sep
		        $net_payment_before_tax = ($row->monthly_rent_amount + $row->total_others_amount + $row->area_amount);
		     	$tax_applicable_amt= $net_payment_before_tax - $others_no_tax_amount->tax_not_apply;
		        // tax calculation - 30 april 2018
	            //old --- $tax_amount = ($net_payment_before_tax * $tax_rate->tax_amount)/100;
	            for($si=0;$si<$slab_count;$si++){
	                if($tax_applicable_amt >= $tax_slab_rate[$si]->min_amt && $tax_applicable_amt <= $tax_slab_rate[$si]->max_amt){
	                    $tax_rate=$tax_slab_rate[$si]->tax_percent;
	                }
	            }
	            $tax_amount = ($tax_applicable_amt * $tax_rate)/100;
	            if($rent_agreement_row_data->tax_wived=='wived_yes'){
	            	$tax_amount =0;
	            }
		        $new_net_payment = ($net_payment_before_tax - $tax_amount -  $row->adjustment_adv) - $row->adjust_sec_deposit;

				$unadjust = $unadjust - $row->adjustment_adv ; 

				//$date=date_create("$row->schedule_strat_dt");
				$date = date_create("$row->maturity_dt");
				$d=  date_format($date,"d-M-y");
				$sche_payment_type='';

				if($row->remarks !=''){$style_tr='background-color: lightgreen !important;'; }else{$style_tr='';} 
				
				if($row->paid_sts !='paid'){

					if(date("Y-m-d") > $row->maturity_dt) {  // Matured
					//if(date("Y-m-d") >= date("Y-m-d") ) {  // Matured
						if($row->paid_sts =='advance')
							{ $paid_sts='Matured (Advance)';
							  $sche_payment_type='advance_rent_payment'; }
						else if($row->paid_sts =='unpaid' && $rent_agreement_row_data->agree_current_sts_id=='6')
							{ 
							  $paid_sts='Matured (Stop and Unpaid)';
							  $sche_payment_type='stop_cost_center'; 
// here pm condition
							}
						else if($row->paid_sts =='stop' && $rent_agreement_row_data->agree_current_sts_id=='6')
							{ $paid_sts='Matured (Stoped)'; }
						else if($row->paid_sts =='stop' && $rent_agreement_row_data->agree_current_sts_id=='5' && $rent_agreement_row_data->point_of_payment=='cm')
							{ $paid_sts='Matured (Released and Unpaid)';
							  $sche_payment_type='stop_payment'; }
					   else if($row->paid_sts =='unpaid' && $rent_agreement_row_data->agree_current_sts_id=='5')
							{ $paid_sts='Pending';
							  $sche_payment_type='unpaid_payment'; } 
					    else if($row->paid_sts =='stop' && $rent_agreement_row_data->agree_current_sts_id=='5' && $rent_agreement_row_data->point_of_payment=='pm'){
							$paid_sts='Matured (Unpaid)';
							$sche_payment_type='stop_payment_pm'; 
						}
						else{$paid_sts=''; $sche_payment_type='unknown';} 
						 
						$prov_amount = 0;
						
					}else{
						if(date("Y-m-d") >= $row->schedule_strat_dt && $row->paid_sts !='stop' && $rent_agreement_row_data->point_of_payment=='pm'){
							$paid_sts='Not Matured (Accrual)';
							$sche_payment_type='stop_cost_center_pm'; 

						}
						// for testing purpose on 12 sep 2018 start
						else if( $row->paid_sts =='stop' && $rent_agreement_row_data->agree_current_sts_id=='5' && $rent_agreement_row_data->point_of_payment=='pm'){
							$paid_sts='Matured (Unpaid)';
							$sche_payment_type='stop_payment_pm'; 
						}
						// for testing purpose on 12 sep 2018 end
						else{

					    // not matured
						if($row->paid_sts =='advance'){ $paid_sts='Not Matured (Advance)'; }else{$paid_sts='Not Matured';}
						} 
					}

				
					if($row->paid_sts =='closed'){
						$paid_sts='Closed';
					}
					 
				}else{

					$paid_sts='Paid';
				}

				if($paid_sts=='Provisioned'){$prov_style_tr='background-color: #F5A9A9 !important;'; }else{$prov_style_tr='';} 

						$html .='<tr style="border: 1px solid black ; '.$style_tr.' '.$prov_style_tr.'" >
						<input name="id'.$i.'" type="hidden" id="id'.$i.'" value="'.$row->id.'"  class="text-input-small" />';   
						$html .='<td style="text-align:center;">'; 

						
						if($paid_sts=='Paid'){
							$html .='<img align="center"  title="Paid"  src="'.base_url().'images/paid1.png" style="width:25px; hight:20px; ">';
						}
						else if($paid_sts=='Pending' || $paid_sts=='Matured (Advance)' || $paid_sts=='Matured (Stop and Unpaid)' || $paid_sts=='Matured (Released and Unpaid)' || $paid_sts=='Not Matured (Accrual)'){
							$html .='<input type="checkbox" id="'.$row_serial.'sche_check'.$i.'" name="'.$row_serial.'sche_check'.$i.'" value="'.$row_serial.'sche_check'.$i.'"  ></td>';
						}

						else if($paid_sts=='Closed'){
							$html .='<img align="center"  title="Paid"  src="'.base_url().'images/not_applicable.png" style="width:25px; hight:20px; ">';
						}
						else if($paid_sts=='Not Matured' || $paid_sts=='Not Matured (Advance)' || $paid_sts=='Matured (Stoped)' || $paid_sts==''){ 
							$html .='<img align="center"  title="Paid"  src="'.base_url().'images/not_applicable.png" style="width:25px; hight:20px; ">';
							//$html .='<input type="checkbox" id="'.$row_serial.'sche_check'.$i.'" name="'.$row_serial.'sche_check'.$i.'" value="'.$row_serial.'sche_check'.$i.'"  ></td>';
						}
						else{
			                $html .='<input type="checkbox" id="'.$row_serial.'sche_check'.$i.'" name="'.$row_serial.'sche_check'.$i.'" value="'.$row_serial.'sche_check'.$i.'"  ></td>';
						}
						
						$html .='<td style="text-align:center;" id="'.$row_serial.'paid_txt'.$i.'">'.$paid_sts.'</td>';
						$html .='<td style="text-align:center;">'.$d.'</td>';
						$html .='<td style="text-align:center;">'.$row->monthly_rent_amount.'<input type="hidden" name="new_sche_monthly_rent'.$i.'"  id="new_sche_monthly_rent'.$i.'" value="'.$row->monthly_rent_amount.'"></td>';
						$html .='<td style="text-align:center;">'.$row->total_others_amount.'<input type="hidden" name="new_sche_others_rent'.$i.'"  id="new_sche_others_rent'.$i.'" value="'.$row->total_others_amount.'"></td>';
						$html .='<td style="text-align:center;">'.$row->area_amount.'<input type="hidden" name="new_sche_arear'.$i.'"  id="new_sche_arear'.$i.'" value="'.$row->area_amount.'"></td>';
						$html .='<td style="text-align:center;" id="final_adj_payment'.$row->id.'">'.$row->adjustment_adv.'<input type="hidden" name="new_sche_adjustment_adv'.$i.'"  id="new_sche_adjustment_adv'.$i.'" value="'.$row->adjustment_adv.'"></td>';
						$html .='<td style="text-align:center;" id="avg_sd_payment'.$row->id.'" class="avg_sd_payment'.$i.'">'.$row->adjust_sec_deposit.'<input type="hidden" name="new_sche_sec_dep'.$i.'"  id="new_sche_sec_dep'.$i.'" value="'.$row->adjust_sec_deposit.'"></td>
						 		<input type="hidden" class="new_avg_sd_payment'.$row->id.'" name="new_avg_sd_payment'.$i.'"  id="new_avg_sd_payment'.$i.'" value="'.$row->adjust_sec_deposit.'">
						 		<input type="hidden" name="prov_sd_amt'.$i.'"  id="prov_sd_amt'.$i.'" class="prov_sd_amt'.$row->id.'" value="'.$row->adjust_sec_deposit.'">';
						
						$html .='<td style="text-align:center; display:none;" class="final_prov_payment'.$i.'" id="final_prov_payment'.$row->id.'">'.$prov_amount.'</td>
									<input type="hidden" name="new_sche_prov'.$i.'"  id="new_sche_prov'.$i.'" value="'.$prov_amount.'">
									<input type="hidden" name="old_hidden_final_prov_payment'.$row->id.'"  id="old_hidden_final_prov_payment'.$row->id.'" value="'.$prov_amount.'">';

						$html .='<td style="text-align:center;">'.number_format($tax_amount,2,'.','').'<input type="hidden" name="new_sche_tax'.$i.'"  id="new_sche_tax'.$i.'" value="'.$tax_amount.'"></td>';
						
						$html .='<td style="text-align:center;" class="final_net_payment'.$i.'" id="final_net_payment'.$row->id.'">'.number_format($new_net_payment,2,'.','').' </td>

								 <input type="hidden" name="hidden_final_net_payment'.$row->id.'"  id="hidden_final_net_payment'.$row->id.'" value="'.$new_net_payment.'">
								 <input type="hidden" name="old_hidden_final_net_payment'.$row->id.'" class="old_hidden_final_net_payment'.$i.'" id="old_hidden_final_net_payment'.$row->id.'" value="'.$new_net_payment.'">
								 <input type="hidden" name="new_sche_adjust_amount'.$i.'"  id="new_sche_adjust_amount'.$i.'" value="'.$new_net_payment.'">
								 <input type="hidden" name="net_payment_before_tax'.$i.'"  id="net_payment_before_tax'.$i.'" value="'.$net_payment_before_tax.'">
								 <input type="hidden" name="new_paid_sts'.$i.'"  id="new_paid_sts'.$i.'" value="'.$sche_payment_type.'">
								 <input type="hidden" class="new_sche_net_amount'.$row->id.'" name="new_sche_net_amount'.$i.'"  id="new_sche_net_amount'.$i.'" value="'.$new_net_payment.'">
								 <input type="hidden" class="rent_fraction_day'.$row->id.'" name="rent_fraction_day'.$i.'"  id="rent_fraction_day'.$i.'" value="'.$row->rent_fraction_day.'">';
						//$html .='<td style="text-align:center;">'.$unadjust.'</td>';
						$html .='<td style="text-align:center; display:none;">'.$row->unadjusted_adv_rent.'</td>';
						$html .='<td style="text-align:center;">'.$row->remarks.'</td>';
					
						$html .='</tr>';
						$i++;	
		}
		
		$html .='</tbody></table>';
		 
        $row_count=$i-1;
		$html .= '<input name="sche_row_count" type="hidden" id="sche_row_count" value="'.$row_count.'"  class="text-input-small" />';
		$html.= '<br />'; 
	//	$html .='<center><input id="closeButton" class="buttonStyle" type="button" value="Close"></center>';
		echo $html;

}


function get_matured_schedule_info()
{
	
		$this->load->model('agreement_model', '', TRUE);

		$row_serial = $this->input->post('row_serial');
		$rent_id = $this->input->post('rent_id');
		$result = $this->agreement_model->rent_matured_unpaid_payment_schedule_info($this->input->post('rent_id')); // 0
		$rent_agreement_row_data = $this->agreement_model->get_add_action_data($this->input->post('rent_id'));
		$rent_adjust_data = $this->agreement_model->single_rent_adjustment_get_info('',$this->input->post('rent_id'));
		$date_diff =  $this->dateDiff($rent_agreement_row_data->agree_exp_dt, $rent_agreement_row_data->rent_start_dt);
		$start_date=date_create($rent_agreement_row_data->rent_start_dt);
		$end_date=date_create($rent_agreement_row_data->agree_exp_dt);
		$point_of_payment=$rent_agreement_row_data->point_of_payment;
		$unadjust = $rent_agreement_row_data ->total_advance;
		$tax_rate = $this->agreement_model->get_parameter_data_single('ref_rent_tax','id','');

		if($point_of_payment=='cm'){ $pp_str = 'Current Month';}
			else{ $pp_str = 'Following Month';}
		$html= ''; 
		
		
		$html.= '

		<style>
		table {
		    width:100%;
		}
		table, th, td {
		    border: 1px solid black;
		    border-collapse: collapse;
		}
		th, td {
		    padding: 5px;
		    text-align: left;
		}
		table#t01 tr:nth-child(even) {
		    background-color: #eee;
		}
		table#t01 tr:nth-child(odd) {
		   background-color:#fff;
		}
		table#t01 th	{
		    background-color: olive;
		    color: white;
		}
		.summery_class{
			margin-bottom: 3px !important;
		    text-align: left;
		}
		.border_none{
			border:none !important;

		}
		</style>

		'; 
		$html.= '<p class="summery_class" ><b>Payment Summery</b></p>'; 
		$html.= '<p class="summery_class"><b>Period :</b> '.date_format($start_date,"d/m/Y").' to '.date_format($end_date,"d/m/Y").' ('.$date_diff.'), '.$pp_str.' Basis</p>'; 
	//	$html.= '<p class="summery_class"><b>10% increment every 02 years</b></p>'; 
		$html.= '<p class="summery_class"><b>Monthly Rent :</b> '.$rent_agreement_row_data->monthly_rent.'</p>'; 
		$html.= '<p class="summery_class"><b>Advance payment : </b>'.$rent_agreement_row_data->total_advance.'</p>'; 
		$html.= '<p class="summery_class"><b>Monthly Adjustment :</b> '.$rent_adjust_data->percent_dir_val.'</p>'; 
		$html.= '<input type="hidden" value="'.$row_serial.'" id="row_serial">'; 
		$html.= '<input type="hidden" value="'.$rent_id.'" id="rent_id">'; 
   
        $html.= '<br />'; 
		$html .='<table class="" id="t01" style="border:solid 1px black; text-align: center; width:97%; margin:20px;">';
	
	      
		$html .='<tr class="headrow" style="text-align:center;font-weight:bold;background-color:#C5C5C5; border: 1px solid black;">
		<th style="text-align:center;">Payment Select</th>
		<th style="text-align:center;">Payment Status</th>
		<th style="text-align:center;">Expected payment date</th>
		<th style="text-align:center;"> Monthly Rent</th>
		<th style="text-align:center;"> Others </th>
		<th style="text-align:center;"> Area Amount </th>
		<th style="text-align:center;"> Adjustment</th>
		
		<th style="text-align:center;"> Provision Adjust</th>
		<th style="text-align:center;"> Tax </th>
		<th style="text-align:center;"> Net Payment </th>
		<th style="text-align:center;"> Unadjusted Advance rent</th>
		<th style="text-align:center;"> Remarks</th>';

		$html .='<tbody id="">';
		$i=1;
	foreach($result as $row){ 

		
		$new_net_payment= ($row->monthly_rent_amount + $row->total_others_amount) - ($row->adjustment_adv + $tax_rate->tax_amount);
		$unadjust = $unadjust - $row->adjustment_adv ; 

		$date=date_create("$row->schedule_strat_dt");
		$d=  date_format($date,"d-M-y");

		if($row->remarks !=''){$style_tr='background-color: lightgreen !important;'; }else{$style_tr='';} 
		if($row->paid_sts =='unpaid'){$paid_sts='Matured'; }else{$paid_sts='Paid';}

				$html .='<tr style="border: 1px solid black ; '.$style_tr.'" >
				<input name="id'.$i.'" type="hidden" id="id'.$i.'" value="'.$row->id.'"  class="text-input-small" />';   
				$html .='<td style="text-align:center;">

				'; 
                $html .='<input type="checkbox" id="'.$row_serial.'sche_check'.$i.'" name="'.$row_serial.'sche_check'.$i.'" value="'.$row_serial.'sche_check'.$i.'" checked></td>';
				$html .='<td style="text-align:center;">'.$paid_sts.'</td>';
				$html .='<td style="text-align:center;">'.$d.'</td>';
				$html .='<td style="text-align:center;">'.$row->monthly_rent_amount.'<input type="hidden" name="new_sche_monthly_rent'.$i.'"  id="new_sche_monthly_rent'.$i.'" value="'.$row->monthly_rent_amount.'"></td>';
				$html .='<td style="text-align:center;">'.$row->total_others_amount.'<input type="hidden" name="new_sche_others_rent'.$i.'"  id="new_sche_others_rent'.$i.'" value="'.$row->total_others_amount.'"></td>';
				$html .='<td style="text-align:center;">0.00</td>';
				$html .='<td style="text-align:center;">'.$row->adjustment_adv.'<input type="hidden" name="new_sche_adjustment_adv'.$i.'"  id="new_sche_adjustment_adv'.$i.'" value="'.$row->adjustment_adv.'"></td>';
			
				$html .='<td style="text-align:center;">0.00</td>';
				$html .='<td style="text-align:center;">'.$tax_rate->tax_amount.'<input type="hidden" name="new_sche_tax'.$i.'"  id="new_sche_tax'.$i.'" value="'.$tax_rate->tax_amount.'"></td>';
				$html .='<td style="text-align:center;" id="final_net_payment'.$row->id.'">'.$new_net_payment.' </td>

						 <input type="hidden" name="hidden_final_net_payment'.$row->id.'"  id="hidden_final_net_payment'.$row->id.'" value="'.$new_net_payment.'">
						 <input type="hidden" name="old_hidden_final_net_payment'.$row->id.'"  id="old_hidden_final_net_payment'.$row->id.'" value="'.$new_net_payment.'">
						 <input type="hidden" name="new_sche_adjust_amount'.$i.'"  id="new_sche_adjust_amount'.$i.'" value="'.$new_net_payment.'">';
				//$html .='<td style="text-align:center;">'.$unadjust.'</td>';
				$html .='<td style="text-align:center;">'.$row->unadjusted_adv_rent.'</td>';
				$html .='<td style="text-align:center;">'.$row->remarks.'</td>';
			

				$html .='</tr>';
		$i++;	
	}
		
		$html .='</tbody></table>';
		//$html .='<div style="text-align:center;  cursor:pointer" onclick="sd_preview_item('.$rent_id.')" ><img align="center" src="'.base_url().'images/view_detail.png"></div>    <input name="sd_adjust_amount'.$row->id.'" type="text"  id="sd_adjust_amount'.$row->id.'" value="" class="text-input-small amount" readonly />';
		$html .='<div style="text-align:center;  cursor:pointer" onclick="sd_preview_item('.$rent_id.')" ><img align="center" src="'.base_url().'images/view_detail.png">    <input name="new_sd_amount'.$rent_id.'" type="text"  id="new_sd_amount'.$rent_id.'" value="0.00" class="text-input-small " placeholder="SD Adjust" readonly /></div><br >';

        $row_count=$i-1;
		$html .= '<input name="sche_row_count" type="hidden" id="sche_row_count" value="'.$row_count.'"  class="text-input-small" />';

		echo $html;


}

function get_schedule_info()
{
		$this->load->model('rent_security_deposit_model', '', TRUE);
		$this->load->model('agreement_model', '', TRUE);

		$result = $this->agreement_model->rent_payment_schedule_info($this->input->post('rent_id'));
		$rent_agreement_row_data = $this->agreement_model->get_add_action_data($this->input->post('rent_id'));
		$rent_adjust_data = $this->agreement_model->single_rent_adjustment_get_info('',$this->input->post('rent_id'));
		$date_diff =  $this->dateDiff($rent_agreement_row_data->agree_exp_dt, $rent_agreement_row_data->rent_start_dt);
		$start_date=date_create($rent_agreement_row_data->rent_start_dt);
		$end_date=date_create($rent_agreement_row_data->agree_exp_dt);
		$point_of_payment=$rent_agreement_row_data->point_of_payment;
		$unadjust = $rent_agreement_row_data ->total_advance;
		$tax_rate = $this->agreement_model->get_parameter_data_single('ref_rent_tax','id','');

		if($point_of_payment=='cm'){ $pp_str = 'Current Month';}
			else{ $pp_str = 'Following Month';}
		$html= ''; 
		
		
		$html.= '

			<style>
			table {
			    width:100%;
			}
			table, th, td {
			    border: 1px solid black;
			    border-collapse: collapse;
			}
			th, td {
			    padding: 5px;
			    text-align: left;
			}
			table#t01 tr:nth-child(even) {
			    background-color: #eee;
			}
			table#t01 tr:nth-child(odd) {
			   background-color:#fff;
			}
			table#t01 th	{
			    background-color: olive;
			    color: white;
			}
			.summery_class{
				margin-bottom: 3px !important;
			    text-align: left;
			}
			.border_none{
				border:none !important;

			}
			</style>';

		 
		$html.= '<p class="summery_class" ><b>Payment Summery</b></p>'; 
		$html.= '<p class="summery_class"><b>Period :</b> '.date_format($start_date,"d/m/Y").' to '.date_format($end_date,"d/m/Y").' ('.$date_diff.'), '.$pp_str.' Basis</p>'; 
	//	$html.= '<p class="summery_class"><b>10% increment every 02 years</b></p>'; 
		$html.= '<p class="summery_class"><b>Initial Rent :</b> '.$rent_agreement_row_data->monthly_rent.'</p>'; 
		$html.= '<p class="summery_class"><b>Advance payment : </b>'.$rent_agreement_row_data->total_advance.'</p>'; 
		$html.= '<p class="summery_class"><b>Monthly Adjustment :</b> '.$rent_adjust_data->percent_dir_val.'</p>'; 
   
        $html.= '<br />'; 
		$html .='<table class="" id="t01" style="border:solid 1px black; text-align: center; width:97%; margin:20px;">';
	

	// </tr>';      
		$html .='<tr class="headrow" style="text-align:center;font-weight:bold;background-color:#C5C5C5; border: 1px solid black;">
		<th style="text-align:center;">Payment Status</th>
		<th style="text-align:center;">Expected payment date</th>
		<th style="text-align:center;"> Monthly Rent</th>
		<th style="text-align:center;"> Others </th>
		<th style="text-align:center;"> Area Amount </th>
		<th style="text-align:center;"> Adjustment</th>
		<th style="text-align:center;"> S.D Adjust</th>
		<th style="text-align:center;"> Provision Adjust</th>
		<th style="text-align:center;"> Tax </th>
		<th style="text-align:center;"> Net Payment </th>
		<th style="text-align:center;"> Unadjusted Advance rent</th>
		<th style="text-align:center;"> Remarks</th>';

		$html .='<tbody id="">';
	
		foreach($result as $row){ 

		
				$new_net_payment= ($row->monthly_rent_amount + $row->total_others_amount) - ($row->adjustment_adv + $tax_rate->tax_amount);
				$unadjust = $unadjust - $row->adjustment_adv ; 

				$date=date_create("$row->schedule_strat_dt");
				$d=  date_format($date,"d-M-y");

				if($row->remarks !=''){$style_tr='background-color: lightgreen !important;'; }else{$style_tr='';}
				if($row->paid_sts =='unpaid'){$paid_sts='Unpaid'; }else{$paid_sts='Paid';}

				$html .='<tr style="border: 1px solid black ; '.$style_tr.'" >';
				$html .='<td style="text-align:center;">'.$paid_sts.'</td>';
				$html .='<td style="text-align:center;">'.$d.'</td>';
				$html .='<td style="text-align:center;">'.$row->monthly_rent_amount.'</td>';
				$html .='<td style="text-align:center;">'.$row->total_others_amount.'</td>';
				$html .='<td style="text-align:center;">0.00</td>';
				$html .='<td style="text-align:center;">'.$row->adjustment_adv.'</td>';
				$html .='<td style="text-align:center;"><div style="text-align:center;  cursor:pointer" onclick="sd_preview_item('.$row->id.','.$row->rent_agree_id.')" ><img align="center" src="'.base_url().'images/view_detail.png"></div></td>';
				$html .='<td style="text-align:center;">0.00</td>';
				$html .='<td style="text-align:center;">'.$tax_rate->tax_amount.'</td>';
				$html .='<td style="text-align:center;">'.$new_net_payment.'</td>';
				$html .='<td style="text-align:center;">'.$unadjust.'</td>';
				$html .='<td style="text-align:center;">'.$row->remarks.'</td>';
			
				//$html .='<td style="text-align:center;"><input type="text"  name="" id="others_total'.$i.'" value="" class="incr_input"  readonly/></td>';
				//$html .='<td style="text-align:center;"><input type="text"  name="end_date'.$i.'" id="rent_end_date" value="'.$sch_end_date.'" class="incr_input"  readonly/></td>';
				$html .='</tr>';
			
		}
		
		$html .='</tbody></table>';
		echo $html;


}

function get_sd_info()
{
	
	//$sche_row_id = $this->input->post('sche_row_id');
	$agree_id = $this->input->post('agree_id');
	$sd_result = $this->rent_schedule_payment_model->rent_sd_info_by_agree_id($agree_id);

	$html= ''; 
	$html.= '
	<style>
			table#t02 tr:nth-child(even) {
			    background-color: #eee;
			}
			table#t02 tr:nth-child(odd) {
			   background-color:#fff;
			}
	</style>
	'; 
	$html .='<table class="" id="t02" style="border:solid 1px black; text-align: center; width:97%; margin:20px;">';
				$html .='<tr class="headrow" style="text-align:center;font-weight:bold;background-color:skyblue; border: 1px solid black;">
				<input name="sche_row_id" type="hidden" id="sche_row_id" value=""  class="text-input-small" />

					<th style="text-align:center;">Select</th> 
					<th style="text-align:center;">SD Reference</th>
					<th style="text-align:center;">T.Amount</th>
					<th style="text-align:center;">T.Adjust</th>
					<th style="text-align:center;">Rest Amount</th>
					<th style="text-align:center;">New Adjust Amount</th>';
				$html .='<tbody id="">';
	$i=1;	
        
	foreach($sd_result as $sd_row){ 
			if($sd_row->amount!=$sd_row->adjust_amount){
						$rest_amount=0;
						$rest_amount= $sd_row->amount - $sd_row->adjust_amount; 
						$set=0;

							$html .='<tr style="border: 1px solid black ;" >

							
							<input name="id" type="hidden" id="'.$sd_row->id.'" value="'.$i.'"  class="text-input-small" />
			    			<input name="sd_id'.$i.'" type="hidden" id="sd_id'.$i.'" value="'.$sd_row->id.'"  class="text-input-small" />
			    			<input name="bill_adjusted_by_id'.$i.'" type="hidden" id="bill_adjusted_by_id'.$i.'" value=""  class="text-input-small" />
							<input name="hidden_amount'.$sd_row->id.'" type="hidden" id="hidden_amount'.$sd_row->id.'" value="'.$set.'"  class="text-input-small" />
			    			<input name="old_hidden_amount'.$i.'" type="hidden" id="old_hidden_amount'.$i.'" value=""  class="text-input-small" />';

							$html .='<td style="text-align:center;"><input type="checkbox" id="sd_check'.$i.'" name="sd_check" value="sd_check'.$i.'" ></td>';
							$html .='<td style="text-align:center;">'.$sd_row->sd_ref_no_auto.'</td>';
							$html .='<td style="text-align:center;">'.$sd_row->amount.'</td>';
							$html .='<td style="text-align:center;">'.$sd_row->adjust_amount.'</td>';
							$html .='<td style="text-align:center;">'.$rest_amount.'</td>';
							$html .='<td style="text-align:center;"><input type="text" name="new_sd_adjust_amount'.$i.'"  id="new_sd_adjust_amount'.$i.'"  class="number"></td>';


							// new 
							$html .='<input name="payable'.$i.'" type="hidden" id="payable'.$i.'" value=""  class="text-input-small" />
									<input name="sche_sd_rest'.$i.'" type="hidden" id="sche_sd_rest'.$i.'" value="'.$rest_amount.'"  class="text-input-small" />
									<input name="count'.$i.'" type="hidden" id="count'.$i.'" value="'.$i.'"  class="text-input-small" />
									<input name="t_adjusted'.$i.'" type="hidden" id="t_adjusted'.$i.'" value=""  class="text-input-small" />
									<input name="old_adj'.$i.'" type="hidden" id="old_adj'.$i.'" value=""  class="text-input-small" />';
							$html .='</tr>';
							$i++;
			}           
	}
	$html .='</tbody></table>';
	$row_count=$i-1;
	$html .= '<input name="row_count" type="hidden" id="row_count" value="'.$row_count.'"  class="text-input-small" />';
	//$html .='<center><input id="closeButton" class="buttonStyle" type="button" value="Close"></center>';
	echo $html;

}


// 31 aug

function get_sd_info_edit()
{
	
	$agree_id = $this->input->post('agree_id');
	$sd_result = $this->rent_schedule_payment_model->rent_sd_info_by_agree_id($agree_id);

	$html= ''; 
	$html.= '
	<style>
			table#t02 tr:nth-child(even) {
			    background-color: #eee;
			}
			table#t02 tr:nth-child(odd) {
			   background-color:#fff;
			}
	</style>
	'; 
	$html .='<table class="" id="t02" style="border:solid 1px black; text-align: center; width:97%; margin:20px;">';
				$html .='<tr class="headrow" style="text-align:center;font-weight:bold;background-color:skyblue; border: 1px solid black;">
				<input name="sche_row_id" type="hidden" id="sche_row_id" value=""  class="text-input-small" />

					<th style="text-align:center;">Select</th> 
					<th style="text-align:center;">SD Reference</th>
					<th style="text-align:center;">T.Amount</th>
					<th style="text-align:center;">T.Adjust</th>
					<th style="text-align:center;">Rest Amount</th>
					<th style="text-align:center;">New Adjust Amount</th>';
				$html .='<tbody id="">';
	$i=1;	
        
	foreach($sd_result as $sd_row){ 
			if($sd_row->amount!=$sd_row->adjust_amount){
						$rest_amount=0;
						$rest_amount= $sd_row->amount - $sd_row->adjust_amount; 
						$set=0;

							$html .='<tr style="border: 1px solid black ;" >

							
							<input name="id" type="hidden" id="'.$sd_row->id.'" value="'.$i.'"  class="text-input-small" />
			    			<input name="sd_id'.$i.'" type="hidden" id="sd_id'.$i.'" value="'.$sd_row->id.'"  class="text-input-small" />
			    			<input name="bill_adjusted_by_id'.$i.'" type="hidden" id="bill_adjusted_by_id'.$i.'" value=""  class="text-input-small" />
							<input name="hidden_amount'.$sd_row->id.'" type="hidden" id="hidden_amount'.$sd_row->id.'" value="'.$set.'"  class="text-input-small" />
			    			<input name="old_hidden_amount'.$i.'" type="hidden" id="old_hidden_amount'.$i.'" value=""  class="text-input-small" />';

							$html .='<td style="text-align:center;"><input type="checkbox" id="sd_check'.$i.'" name="sd_check" value="sd_check'.$i.'" ></td>';
							//$html .='<td style="text-align:center;"><input type="checkbox" id="sd_check'.$sd_row->id.'" name="sd_check" value="sd_check'.$sd_row->id.'" ></td>';
							$html .='<td style="text-align:center;">'.$sd_row->sd_ref_no_auto.'</td>';
							$html .='<td style="text-align:center;">'.$sd_row->amount.'</td>';
							$html .='<td style="text-align:center;">'.$sd_row->adjust_amount.'</td>';
							$html .='<td style="text-align:center;">'.$rest_amount.'</td>';
							$html .='<td style="text-align:center;"><input type="text" name="new_sd_adjust_amount'.$i.'"  id="new_sd_adjust_amount'.$i.'"  class="number"></td>';


							// new 
							$html .='<input name="payable'.$i.'" type="hidden" id="payable'.$i.'" value=""  class="text-input-small" />
									<input name="sche_sd_rest'.$i.'" type="hidden" id="sche_sd_rest'.$i.'" value="'.$rest_amount.'"  class="text-input-small" />
									<input name="count'.$i.'" type="hidden" id="count'.$i.'" value="'.$i.'"  class="text-input-small" />
									<input name="t_adjusted'.$i.'" type="hidden" id="t_adjusted'.$i.'" value=""  class="text-input-small" />
									<input name="old_adj'.$i.'" type="hidden" id="old_adj'.$i.'" value=""  class="text-input-small" />';
							$html .='</tr>';
							$i++;
			}           
	}
	$html .='</tbody></table>';
	$row_count=$i-1;
	$html .= '<input name="row_count" type="hidden" id="row_count" value="'.$row_count.'"  class="text-input-small" />';
	//$html .='<center><input id="closeButton" class="buttonStyle" type="button" value="Close"></center>';
	echo $html;


}


function dateDiff($time1, $time2, $precision = 6) {
    // If not numeric then convert texts to unix timestamps
    if (!is_int($time1)) {
      $time1 = strtotime($time1);
    }
    if (!is_int($time2)) {
      $time2 = strtotime($time2);
    }
 
    // If time1 is bigger than time2
    // Then swap time1 and time2
    if ($time1 > $time2) {
      $ttime = $time1;
      $time1 = $time2;
      $time2 = $ttime;
    }
 
    // Set up intervals and diffs arrays
    $intervals = array('year','month','day','hour','minute','second');
    $diffs = array();
 
    // Loop thru all intervals
    foreach ($intervals as $interval) {
      // Create temp time from time1 and interval
      $ttime = strtotime('+1 ' . $interval, $time1);
      // Set initial values
      $add = 1;
      $looped = 0;
      // Loop until temp time is smaller than time2
      while ($time2 >= $ttime) {
        // Create new temp time from time1 and interval
        $add++;
        $ttime = strtotime("+" . $add . " " . $interval, $time1);
        $looped++;
      }
 
      $time1 = strtotime("+" . $looped . " " . $interval, $time1);
      $diffs[$interval] = $looped;
    }
    
    $count = 0;
    $times = array();
    // Loop thru all diffs
    foreach ($diffs as $interval => $value) {
      // Break if we have needed precission
      if ($count >= $precision) {
        break;
      }
      // Add value and interval 
      // if value is bigger than 0
      if ($value > 0) {
        // Add s if value is not 1
        if ($value != 1) {
          $interval .= "s";
        }
        // Add value and interval to times array
        $times[] = $value . " " . $interval;
        $count++;
      }
    }
 
    // Return string with times
    return implode(", ", $times);
  }

function get_search_data()
	{


		$security_deposit=array();
		$this->load->model('rent_security_deposit_model', '', TRUE);
		$result = $this->rent_security_deposit_model->get_search_data();


// new code table

		$html= ''; 
		$html .='<table class="register-table" style="border:solid 1px black; text-align: center; width:97%; margin:20px;">';
	
		$html .='<tr class="headrow" style="text-align:center;font-weight:bold;background-color:#C5C5C5; border: 1px solid black;"><th>Select</th><th>Agreement Reference</th><th>Landlords</th><th>Location Name</th></tr>';
		$html .='<tbody id="register-table" >';
		 
		$c=0;
			foreach ($result as $single_agree_data){

			$html.= '<script>jQuery(".select_it").click(function(){
				       jQuery(this).empty();
				       jQuery(this).html(\'Selected\');
				      jQuery(".select_it").not(this).html(\'<img src="http://localhost/bprms/images/drag.png" >\');
				       var agreement_id = \'\';
				       var agreement_name=\'\';
							 landlord_ids= jQuery(this).prev().prev().prev().prev().val(); 
							 landlord_names= jQuery(this).prev().prev().prev().val(); 
							 agreement_id= jQuery(this).prev().prev().val();
							
							
							 agreement_name= jQuery(this).prev().val();
							
							jQuery(\'#agreement_ref_no\').val(agreement_name);
							jQuery(\'#agree_landlord_names\').val(landlord_names);
							jQuery(\'#agree_landlord_ids\').val(landlord_ids);
							jQuery(\'#agreement_idc\').val(agreement_id);

					    	
					    	//var inc_val= parseFloat(jQuery(this).val());
					    	
				    });</script>'; 

				
				$html .='<tr style="border: 1px solid;">';
 
				$html .='<input name="landlord_ids" class="landlord_ids" type="hidden" value="'.$single_agree_data->landlord_ids.'" />';
				$html .='<input name="landlord_names" class="landlord_names" type="hidden" value="'.$single_agree_data->landlord_names.'" />';
				$html .='<input name="agreement_id" class="agreement_id" type="hidden" value="'.$single_agree_data->id.'" />';
			    $html .='<input name="rent_agreement_name" class="rent_agreement_name" type="hidden" value="'.$single_agree_data->agreement_ref_no.'" />';
			    
				$html .='<td style="text-align:center;border: 1px solid;" class="select_it"><img src="http://192.168.3.253:85/rent/images/drag.png" ></td>';
				$html .='<td style="text-align:center;border: 1px solid;">'.$single_agree_data->agreement_ref_no.'</td>';
				$html .='<td style="text-align:center;border: 1px solid;">'.$single_agree_data->landlord_names.'</td>';
				$html .='<td style="text-align:center;border: 1px solid;">'.$single_agree_data->location_name.'</td>';
				$html .='</tr>';
			}
		
		$html .='</tbody></table>';

		echo $html;

	$c++; 
}



	function get_search_data_edit($ll_edit_result,$ll_count)
	{

		$security_deposit=array();
		$this->load->model('rent_security_deposit_model', '', TRUE);
	
// new code jquery

		$html= ''; 
		$html .='<table class="edit_table" style="border:solid 1px black; text-align: center; width:97%; margin:20px;">';
	
		$html .='<tbody id="register-table" >';
		 $html .='<thead><tr><th>Landlord</th><th>Amount</th><th>Payment Mode</th><th>Account no</th><th>Branch</th></tr><thead>';
		
		$i=0; 
		$payment_mode='';
		foreach ($ll_edit_result as $single_agree_data){
			
			//html 
	 		if($single_agree_data->payment_type=='A/C Transfer')
				{	$payment_mode='ac_transfer';
					
				}else if($single_agree_data->payment_type=='Pay Order'){
					$payment_mode='pay_order';
				
				}else if($single_agree_data->payment_type=='Cash Payment'){
					$payment_mode='cash_payment';
					
				}
			$html.= '<tr>';		
			$html.= '<input type="hidden" id="agreement_id_edit" value="'.$single_agree_data->rent_agreement_id.'" name="agreement_id_edit">';		
			$html.= '<input type="hidden" id="rent_sd_txrn_id'.$i.'" value="'.$single_agree_data->id.'" name="rent_sd_txrn_id'.$i.'">';		
			$html.= '<td><input type="text" value="'.$single_agree_data->landlords_id.'" name="landlord'.$i.'"><input type="hidden" id="single_ll_id'.$i.'" value="'.$single_agree_data->single_ll_id.'" name="single_ll_id'.$i.'"></td>';		
			$html.= '<td><input type="text" value="'.$single_agree_data->txrn_amount.'" name="sd_amount'.$i.'"><input name="ac_noo'.$i.'"  id= "ac_noo'.$i.'"  type="hidden"  readonly value="'.$single_agree_data->ac_name.'"  class="text-input-small" />
			</td>';		
			$html.= '<td><div>';
			//$html.= '<select name="payment_mode'.$i.'" id="payment_mode'.$i.'" class="pay_mode"><option value="">Select One</option><option ';if( $payment_mode=='ac_transfer'){ echo 'selected="selected"'; }'  value="ac_transfer" >Account Transfer</option><option 'if( $payment_mode=='pay_order'){ echo 'selected="selected"'; }.' value="pay_order">Pay Order</option><option '.if( $payment_mode=='cash_payment'){ echo 'selected="selected"'; }.' value="cash_payment">Cash Payment</option></select></div></td>';		
			$html.= '<select name="payment_mode'.$i.'" id="payment_mode'.$i.'" style="height:25px;width:240px;">
										<option value="0">Select Payment Mode</option>
										<option '.($payment_mode == 'ac_transfer' ?  'selected="selected"': '').' value="ac_transfer">A/C Transfer</option>
										<option '.($payment_mode == 'pay_order' ?  'selected="selected"': '').' value="pay_order">Pay Order</option>
										<option '.($payment_mode == 'cash_payment' ?  'selected="selected"': '').' value="cash_payment">Cash Payment</option>
									</select></div></td>';		
			$html.= '<td><center>
			<div name="ac_gl'.$i.'" id="ac_gl'.$i.'" ></div>
			<div name="br_list'.$i.'" id="br_list'.$i.'"></div>

			<input name="ac_no'.$i.'"  id= "ac_no'.$i.'"  type="text" id="ac_no'.$i.'" readonly value=""  class="text-input-small" />
			
			</center></td>';		
			$html.= '<td><input name="sd_branch_code'.$i.'" readonly maxlength="3" id="sd_branch_code'.$i.'" value="'.$single_agree_data->ac_br_code.'" style="width:50px;"/></td>';		
			$html.= '</tr>';	
			$i++;	
	

 		}

		$html .='</tbody></table>';
		return $html;

	}

	function get_ac()
	{
		$security_deposit=array();
		$this->load->model('rent_security_deposit_model', '', TRUE);
		$result = $this->rent_security_deposit_model->get_ac();
		$var=$result->account_no;
		echo $var;
		exit;
	}


	function reset_action(){
		$return=$this->rent_schedule_payment_model->reset_action();
		$jTableResult = array();
		if($return==1)
			$jTableResult['status'] = "success";
		else{
			$jTableResult['status'] = "error";
			$jTableResult['errorMsgs'] = "Problem Occured during reset";
		}
		echo json_encode($jTableResult);
	}


	function delete_action($d_v=NULL)
	{
		$id=$this->rent_schedule_payment_model->delete_action();
		$jTableResult = array();
		if($id!='' &&  $id=='100'){
			$jTableResult['status'] = "failed";
		}else{
			
			$jTableResult['status'] = "success";
		}
		$jTableResult['errorMsgs'] = 0;
		echo json_encode($jTableResult);
	}

	function bulk_fin_verify_action($d_v=NULL)
	{
		
		$id=$this->rent_schedule_payment_model->bulk_fin_verify_action();
		//echo $id.'---------';
		 $jTableResult = array();
		 $Message='';
		 if($id!='0'){
		 	$Message =''.$id;
		 	$jTableResult['status'] = "failed";
		 }else{
		 	
		 	$Message='OK';
		 	$jTableResult['status'] = "success";
		 }
		// if($id!='' &&  $id=='100'){
		// 	$jTableResult['status'] = "failed";
		// }else{
			
		// 	$jTableResult['status'] = "success";
		// }
		 //echo $Message;exit;
		$jTableResult['Message']=$Message;
		
		$jTableResult['errorMsgs'] = 0;
		echo json_encode($jTableResult);
	}


}
?>