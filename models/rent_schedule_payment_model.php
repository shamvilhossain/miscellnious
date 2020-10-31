<?php
class rent_schedule_payment_model extends CI_Model {
    function __construct()
    {
        parent::__construct();
	}
	function dtf($date,$expect_sign)
	{
		if (strpos($date,'-') !== false) {
    		$org_sign='-';
		}
		else if (strpos($date,'/') !== false) {
    		$org_sign='/';
		}
		else if (strpos($date,'.') !== false) {
    		$org_sign='.';
		}
		else
			$org_sign='-';
		//if($expect_sign=='-'){$org_sign='-';}else{$org_sign='/';}
			if(!empty($date)){
			$var=explode($org_sign,$date);
			if(count($var)==3){
				//echo $var[2].$expect_sign.$var[1].$expect_sign.$var[0];exit;
				return $var[2].$expect_sign.$var[1].$expect_sign.$var[0];
			}
			else if(count($var)==2){
				//echo $var[0];exit;
				return $var[1].$expect_sign.$var[0];
			}else if(count($var)==1){
				//echo $var[0];exit;
				return $var[0];
			}
		}
	}
	function get_grid_data($filterscount,$sortdatafield,$sortorder,$limit, $offset)
	{
	   	$i=0;
		
	   	if (isset($filterscount) && $filterscount > 0)
		{		
			$where = "( ";
			
			$tmpdatafield = "";
			$tmpfilteroperator = "";
			for ($i=0; $i < $filterscount; $i++)
			{//$where2.="(".$this->input->get('filterdatafield'.$i)." like '%".$this->input->get('filtervalue'.$i)."%')";
			
				// get the filter's value.
				$filtervalue = str_replace('"', '\"', str_replace("'", "\'", $this->input->get('filtervalue'.$i)));
				// get the filter's condition.
				$filtercondition = $this->input->get('filtercondition'.$i);
				// get the filter's column.
				$filterdatafield = $this->input->get('filterdatafield'.$i);
				// get the filter's operator.
				$filteroperator = $this->input->get('filteroperator'.$i);
				
				if($filterdatafield=='date_added' || $filterdatafield=='date_modified')
				{
					$filtervalue=$this->dtf($filtervalue,'-');
				}

				if($filterdatafield =='rent_agree_ref')
				{
					$filterdatafield='j1.rent_agree_ref';
				}	
				else if($filterdatafield=='fin_ref_no')
				{
					$filterdatafield="j1.fin_ref_no";
				}	
				else if($filterdatafield=='location_name')
				{
					$filterdatafield="location_name";
				}	
				else if($filterdatafield=='date_modified')
				{
					$filterdatafield="DATE_FORMAT(j0.u_dt,'%Y-%m-%d')";
				}			
				else if($filterdatafield=='rent_amount')
				{
					$filterdatafield="rent_amount";
				}	
				else if($filterdatafield=='adv_adjustment_amt')
				{
					$filterdatafield="adv_adjustment_amt";
				}
				else if($filterdatafield=='sd_adjust_amt')
				{
					$filterdatafield="sd_adjust_amt";
				}
				else if($filterdatafield=='provision_adjust_amt')
				{
					$filterdatafield="provision_adjust_amt";
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
		}else{$where="()";}

		 if($where =="()")
            {   
            	//$bill_sts_cond=" AND approve_by=0";
            	$bill_sts_cond=" AND journal_verify_sts=0 ";
    		}
            else{
                $bill_sts_cond=" AND  ".$where;
            }
		
		if ($sortorder == '')
		{
			$sortdatafield="j1.id";
			$sortorder = "desc";				
		}

		// MONTH(columnName) = MONTH(CURRENT_DATE())
		// AND YEAR(columnName) = YEAR(CURRENT_DATE())

		$sql="SELECT SQL_CALC_FOUND_ROWS j1.*,DATE_FORMAT(j2.schedule_strat_dt, '%M, %Y') AS schedule_strat_dt,
			j3.location_name
			FROM rent_paid_history AS j1 
			left join rent_ind_schedule j2 on (j1.checked_schedule_ids=j2.id) 
			left join rent_agreement j3 on (j1.agreement_id=j3.id) 
			where j1.sts=1 
			AND MONTH(j1.paid_dt) = MONTH(CURRENT_DATE())
			AND YEAR(j1.paid_dt) = YEAR(CURRENT_DATE()) ".$bill_sts_cond;
		$sql .=' ORDER BY '.$sortdatafield.' '.$sortorder;
		$sql .=' LIMIT '.$offset.','.$limit;
			
		
		$q=$this->db->query($sql);


		//$q=$this->db->get();
		$query = $this->db->query('SELECT FOUND_ROWS() AS `Count`');
		$objCount = $query->result_array();		
		$result["TotalRows"] = $objCount[0]['Count'];
		$result["TotalRows"]=$objCount['0']['Count'];

		if ($q->num_rows() > 0){        
			$result["Rows"] = $q->result();
		} else {
			$result["Rows"] = array();
		}  		
		return $result;
	}

function get_rent_data(){
	// old where ---maturity_dt < CURDATE() AND ---30 april 2018
	// old where ---IF(maturity_dt != schedule_strat_dt then fm otherwise cm)---14 aug 2018
	$sql="SELECT sche.*, agr.point_of_payment, agr.location_name, agr.fin_ref_no, agr.agree_cost_center, agr.agree_current_sts_id, agr.agree_pervious_sts_id FROM 
		(
			SELECT 
				  id,
				  rent_agree_id, GROUP_CONCAT(id SEPARATOR ', ' ) matured_sche_id,
				  GROUP_CONCAT(paid_sts SEPARATOR ', ' ) paid_sts_list,
				  rent_agree_ref,
				  schedule_strat_dt,
				  SUM(monthly_rent_amount) as total_monthly_rent,
				  SUM(total_others_amount) as total_others_rent,
				  SUM(adjustment_adv) as total_adjustment_amount,
				  SUM(area_amount) as total_area_amount,
				  SUM(adjust_sec_deposit) as total_sd_adjust_amount
				FROM
				  rent_ind_schedule 
				WHERE  (sche_add_sts=0 or sche_add_sts=2)
				AND IF(maturity_dt != schedule_strat_dt, schedule_strat_dt < CONCAT(YEAR(CURDATE()),'-',MONTH(CURDATE()),'-28'), maturity_dt <= CURDATE())  
				and (paid_sts='unpaid' or paid_sts='advance' or paid_sts='stop')
			

				GROUP BY rent_agree_id
		) sche
				LEFT OUTER JOIN rent_agreement agr ON (agr.id=sche.rent_agree_id)  
				where agr.agree_current_sts_id in (5,6)
				and agr.sts=1
				 
			";
			$query=$this->db->query($sql);
			//print_r($query->result());
			return $query->result();
}

	
	
	function get_add_action_data($id)
	{
		
		$sql="SELECT SQL_CALC_FOUND_ROWS j1.*,DATE_FORMAT(j2.schedule_strat_dt, '%M, %Y') AS schedule_strat_dt,
		j3.location_name
		FROM rent_paid_history 	AS j1 
		left join rent_ind_schedule j2 on (j1.checked_schedule_ids=j2.id) 
		left join rent_agreement j3 on (j1.agreement_id=j3.id) 
		where j1.id='".$id."' limit 1  ";
		$query=$this->db->query($sql);
		return $query->row();
	}
	function get_ac()
	{	
		$id=$this->input->post('vendor');
		
		$sql="SELECT j0.account_no
			FROM vendor AS j0
			
			 ";
		$sql .="WHERE j0.sts=1 AND j0.vendor_id='".$id."' limit 1";
		$query=$this->db->query($sql);
		//echo $this->db->last_query();exit;
		return $query->row();
	}
	
	function add_edit_action($add_edit=NULL,$edit_id=NULL)
	{
		
		if($add_edit!='finance_verify'){
		
		
		//$this->db->trans_begin(); // transaction start
		if($add_edit=="add")
		{	
			$agree_counter=$this->input->post('agree_counter');
			$agree_counter_arr= explode(",",$agree_counter);
			$adv_adjustment_amt=0;
			$adv_adjustment_amt_arr=array();
			$tax_amount=0;
			$sche_tax_arr=array();
			
			//exit;
			foreach($agree_counter_arr as $single_agree_counter){
				if($this->input->post('sche_payment_sts'.$single_agree_counter)=='stop_cost_center' || $this->input->post('sche_payment_sts'.$single_agree_counter)=='stop_cost_center_pm'){
					$adv_adjustment_amt=0;
					$tax_amount=0;
				}else{
					$adv_adjustment_amt=$this->input->post('per_sche_adjust'.$single_agree_counter);
					$adv_adjustment_amt_arr = explode("@",$this->input->post('per_sche_adjust_sep'.$single_agree_counter));
					$tax_amount=$this->input->post('per_sche_tax'.$single_agree_counter);
					$sche_tax_arr = explode("@",$this->input->post('per_sche_tax_sep'.$single_agree_counter));
				}

				$schedule_arr = explode(",",$this->input->post('checked_schedule_id'.$single_agree_counter));
				$net_payment_arr = explode("@",$this->input->post('per_sche_net_payment_sep'.$single_agree_counter));	
				$monthly_rent_arr = explode("@",$this->input->post('per_sche_monthly_rent_sep'.$single_agree_counter));
				$sd_amount_arr = explode("@",$this->input->post('prev_sd_amount_sep'.$single_agree_counter));
					
					
				$sche_arear_arr = explode("@",$this->input->post('per_sche_arear_sep'.$single_agree_counter));	
				$sche_others_arr = explode("@",$this->input->post('per_sche_others_sep'.$single_agree_counter));

				$checked_schedule_id_arr = explode(",",$this->input->post('checked_schedule_id'.$single_agree_counter));	
				$checked_schedule_sd_ids_arr = explode(",",$this->input->post('sd_checked_adjustment'.$single_agree_counter));	
				$checked_schedule_sd_amt_arr = explode(",",$this->input->post('checked_schedule_sd_amt'.$single_agree_counter));	
				
				// 57475@57475----165266.66@309875----
				$sd_amount_arr_val=0;
				$checked_schedule_sd_ids_val='';
				$checked_schedule_sd_amt_arr_val='';
				
				$sche_index=0;
				foreach($schedule_arr as $single_schedule){
					if($this->input->post('prev_sd_amount_sep'.$single_agree_counter)!=''){
					
						//$sd_amount_arr_val= $sd_amount_arr[$sche_index];
					}

					if($this->input->post('sd_checked_adjustment'.$single_agree_counter)!=''){
					
						//$checked_schedule_sd_ids_val= $checked_schedule_sd_ids_arr[$sche_index];
					}

					if($this->input->post('checked_schedule_sd_amt'.$single_agree_counter)!=''){
					
						$checked_schedule_sd_amt_arr_val= $checked_schedule_sd_amt_arr[$sche_index];
					}

					if($this->input->post('sche_payment_sts'.$single_agree_counter)=='stop_cost_center' || $this->input->post('sche_payment_sts'.$single_agree_counter)=='stop_cost_center_pm'){
						$adv_adjustment_amt_arr[$sche_index]='';
						$sche_tax_arr[$sche_index]='';
					}
					

					$rent_paid_data = array(

						'paid_dt' => date('Y-m-d')
						,'agreement_id' => $this->input->post('rent_agree_id'.$single_agree_counter)
						,'rent_agree_ref' => $this->input->post('rent_agree_ref'.$single_agree_counter)
						,'fin_ref_no' => $this->input->post('fin_ref_no'.$single_agree_counter)
						,'rent_amount' => $net_payment_arr[$sche_index]
						,'monthly_amount' => $monthly_rent_arr[$sche_index]
						,'adv_adjustment_amt' => $adv_adjustment_amt_arr[$sche_index]
						//,'sd_adjust_amt' => $sd_amount_arr_val
						,'sd_adjust_amt' => $checked_schedule_sd_amt_arr_val
						,'arear_adjust_amount' => $sche_arear_arr[$sche_index]
						,'tax_amount' => $sche_tax_arr[$sche_index]

						,'total_others_amount' =>$sche_others_arr[$sche_index]
						,'sche_payment_sts' =>$this->input->post('sche_payment_sts'.$single_agree_counter)
						,'stop_cost_center_amt' =>$this->input->post('checked_stop_cost_center_amt'.$single_agree_counter)	
						,'checked_schedule_ids' =>$checked_schedule_id_arr[$sche_index]
						//,'checked_schedule_sd_ids' =>$checked_schedule_sd_ids_val // may not be like that
						
					 
						,'checked_schedule_sd_ids' =>$this->input->post('sd_checked_adjustment'.$single_agree_counter)
						,'checked_sd_id_serial' =>$this->input->post('sd_counter'.$single_agree_counter)
						,'sd_checked_adjustment_amt' =>$this->input->post('sd_checked_adjustment_amt'.$single_agree_counter)
						,'sd_ids_hash' => $this->input->post('sd_checked_adjustment_with_hash'.$single_agree_counter)
						,'checked_sd_amount' => $this->input->post('checked_schedule_sd_amt'.$single_agree_counter)
						,'test' => $this->input->post('sd_checked_adjustment_with_hash'.$single_agree_counter)
					
						,'sts' => 1
						,'e_by' => $this->session->userdata['user']['user_id']
						,'e_dt' => date('Y-m-d H:i:s')
					
					);

					
						//$final_str = $this->rent_schedule_payment_model->approve_data_generate($rent_paid_data);
						// echo $final_str;exit;
						// 001#001#001#001#020++1043####++450100001#170300001#240500007##0201440028290++
						// Rent Office#Advance Rent#Tax-Rent Office#S.D A/C#Mohammad Alamgir Alam & Mohammad Jahangir Alam++
						// 13000#0#0#0#0++0#3900#650##8450++ 
						// Rent of ATM at ATM At Pahartoli,Ctg for Jan 1970 # Adjustment of advance rent from Rent of ATM At Pahartoli,Ctg for Jan 1970 #5.00 pc TAX from Mohammad Alamgir Alam & Mohammad Jahangir Alam for Rent of ATM At Pahartoli,Ctg for Jan 1970 # Adjustment of Security Deposit for Jan 1970 # Credited rent of ATM At Pahartoli,Ctg for Jan 1970 with ATM,
						//$single_paid_data['schedule_strat_dt']
						$sche_index++;
						$this->db->insert('rent_paid_history', $rent_paid_data);
						$insert_id =$this->db->insert_id();
						//$this->rent_schedule_payment_model->approve_data_insert($rent_paid_data,$insert_id);
				}


				 $insert_idss =$this->db->insert_id();
                 
	                $sche_list = $this->input->post('checked_schedule_id'.$single_agree_counter);
	                $sche_list_arr = explode(",",$sche_list);
	                $checked_schedule_sd_amt_arr = explode(",",$this->input->post('checked_schedule_sd_amt'.$single_agree_counter));
					

                            foreach($sche_list_arr as $key=>$single_sche_id){

                                        $rent_ind_schedule_prov_v_sts_update=array(
                                                        'sche_add_sts'=>1,
                                                        'temp_sec_deposit'=>$checked_schedule_sd_amt_arr[$key]

                                                );
                                                $this->db->where('id',$single_sche_id);
                                                $this->db->update('rent_ind_schedule',$rent_ind_schedule_prov_v_sts_update);

                            } 


			}
      
		}else if($add_edit=='edit')
		{ 
			$agree_counter=$this->input->post('agree_counter');
			$agree_counter_arr= explode(",",$agree_counter);

				$rent_paid_data = array(
					'paid_dt' => date('Y-m-d, H:i:s')
					,'agreement_id' => $this->input->post('rent_id')
					,'rent_agree_ref' => $this->input->post('rent_agree_ref')
					,'fin_ref_no' => $this->input->post('fin_ref_no')
					,'rent_amount' => $this->input->post('rent_amount')
					,'monthly_amount' => $this->input->post('per_sche_monthly_rent')
					,'adv_adjustment_amt' => $this->input->post('per_sche_adjust')
					,'sd_adjust_amt' => $this->input->post('prev_sd_amount') //
					//,'provision_adjust_amt' => $this->input->post('per_sche_prov')
					,'arear_adjust_amount' => $this->input->post('per_sche_arear')
					,'tax_amount' => $this->input->post('per_sche_tax')
					//,'vat_amount' =>0.00
					,'others_name' =>''
					,'others_amount' =>''
					,'total_others_amount' =>$this->input->post('per_sche_others')
					,'sche_payment_sts' =>$this->input->post('sche_payment_sts')
					,'stop_cost_center_amt' =>$this->input->post('checked_stop_cost_center_amt')
					,'checked_schedule_ids' =>$this->input->post('checked_schedule_id')
					,'checked_schedule_sd_ids' =>$this->input->post('sd_checked_adjustment')
					,'checked_sd_id_serial' =>$this->input->post('sd_counter')
					,'sd_checked_adjustment_amt' =>$this->input->post('sd_checked_adjustment_amt')
					,'sd_ids_hash' => $this->input->post('sd_checked_adjustment_with_hash')
					,'test' => $this->input->post('sd_checked_adjustment_with_hash')
					//,'counter' => $this->input->post('counter')
					,'sts' => 1
					,'e_by' => 0
					,'e_dt' => date('Y-m-d, H:i:s')
				
				);

                                  
                                $this->db->where('id',$this->input->post('paid_id'));
                                $this->db->update('rent_paid_history',$rent_paid_data);
                                $insert_idss =$this->input->post('paid_id');
                                    
                                    
               // sche table update (old should be zero)

                    $old_sche_list = $this->input->post('old_checked_schedule_id');
	                $old_sche_list_arr = explode(",",$old_sche_list);
	               

                            foreach($old_sche_list_arr as $single_sche_id){

                                        $old_rent_ind_schedule_prov_v_sts_update=array(
                                                        'sche_add_sts'=>0,
                                                        'temp_sec_deposit'=>0

                                                );
                                                $this->db->where('id',$single_sche_id);
                                                $this->db->update('rent_ind_schedule',$old_rent_ind_schedule_prov_v_sts_update);

                            } 
                                    
	                $sche_list = $this->input->post('checked_schedule_id');
	                $sche_list_arr = explode(",",$sche_list);
	                $checked_schedule_sd_amt_arr = explode(",",$this->input->post('checked_schedule_sd_amt'));

                            foreach($sche_list_arr as $key=>$single_sche_id){

                                        $rent_ind_schedule_prov_v_sts_update=array(
                                                        'sche_add_sts'=>1,
                                                        'temp_sec_deposit'=>$checked_schedule_sd_amt_arr[$key]

                                                );
                                                $this->db->where('id',$single_sche_id);
                                                $this->db->update('rent_ind_schedule',$rent_ind_schedule_prov_v_sts_update);

                                } 


		}if($add_edit=="fin"){ // finance verify // 23 april 2018
		
			$paid_update_data = array(
				
				'fin_v_by' =>$this->session->userdata['user']['user_id']
				,'fin_v_dt' => date('Y-m-d, H:i:s')
				
			);
			
			$this->db->where('id',$this->input->post('rent_paid_history_id'));
			$this->db->update('rent_paid_history',$paid_update_data);
			$insert_idss =$this->input->post('rent_paid_history_id');


		}
	}
		else if($add_edit=='finance_verify'){ // approve


		$others_string='';
		$tax_wived = $this->input->post('tax_wived');
		$loc_names = $this->input->post('loc_names');
		$others_loc_names = $this->input->post('others_loc_names');
		

		$rent_paid_history_id = $this->input->post('rent_paid_history_id');
		$sche_payment_sts = $this->input->post('sche_payment_sts');
		$vat_applicable = $this->input->post('vat_applicable');
		$ledger_type='';
		$ledger_type_info='';
		if($sche_payment_sts=='stop_cost_center'){ $ledger_type_info='Rent Stop'; }
		if($sche_payment_sts=='stop_cost_center_pm'){ $ledger_type_info='Rent Stop Accrual Pm'; }
		if($sche_payment_sts=='advance_rent_payment'){ $ledger_type_info='Rent for Rent in Advance'; }
		if($sche_payment_sts=='stop_payment'){ $ledger_type_info='Rent for Rent Stop';  }
		if($sche_payment_sts=='stop_payment_pm'){ $ledger_type_info='Rent for Rent Accrual Pm';  }
		if($sche_payment_sts=='stop_unpaid_payment'){ $ledger_type_info='Rent';  }
		if($sche_payment_sts=='unpaid_payment'){ $ledger_type_info='Rent';  }
		

		$location_type_data_count = $this->input->post('location_type_data_count');
		$others_location_type_data_count = $this->input->post('others_location_type_data_count');
		$rent_led_ref = $this->user_model->get_bill_refno_rent('rent_ledger', 'ledger_ref_counter', 'ledger_ref_no', 'transaction_dt', 13, '');
		if($others_location_type_data_count==0 && $others_loc_names!=''){
			$others_string= ' and '.$others_loc_names;
		}
		if($others_location_type_data_count==0 && $others_loc_names==''){
			$others_string= '';
		}
		$location_name =  $this->input->post('agree_location_name');
		$landlord_names =  $this->input->post('agree_landlord_names');
		$phpdate =  $this->input->post('paid_month');
        $mysqldate = date( 'M Y ', $phpdate );
		$ref_table_ref='SCH-RENT-'.$this->input->post('paid_date_ref');
		$rent_ledger_data = array();
//ll location dr   //UP
    if($sche_payment_sts!='stop_payment' && $sche_payment_sts!='stop_payment_pm'){    
		for($i=1;$i<=$location_type_data_count;$i++){
		
			  $debit_ledger_data = array(
                 	'ledger_ref_no' => $rent_led_ref[1] 
                 	,'ledger_ref_counter' => $rent_led_ref[0] 
					,'ref_table_ref' => $ref_table_ref
                    ,'section_type' => 'RENT'
                    , 'ledger_type' => 'Rent'
                    , 'Ledger_type_info' => $ledger_type_info
                    , 'ref_id' => $rent_paid_history_id
                    , 'rent_agre_id' => $this->input->post('rent_ref_id')
                    , 'landlord_id' => ''
                    , 'payment_type' => 'GL'
                    , 'cost_center' => $this->input->post('location_type_data_cost_center'.$i)
                    , 'mis_code' => $this->input->post('location_type_data_mis'.$i)
                    , 'description' => 'Rent of '.$this->input->post('location_type_data_loc_name'.$i).' '.$others_string.' at '.$location_name.'  for '.$mysqldate
                    //, 'description' => 'Rent of '.$location_name.' '.$others_string.' for '.$mysqldate
                    , 'account_description' => $this->input->post('location_type_data_acc_des'.$i)
                    , 'gl_account' => $this->input->post('location_type_data_rent_gl'.$i)
                    , 'db_cr_sts' => 'Debit'
                    , 'amount' => $this->input->post('location_type_data_dr_amount'.$i)
                    , 'transaction_by' => $this->session->userdata['user']['user_id']
                    , 'transaction_dt' => date('Y-m-d H:i:s')
                    , 'disburse_dt' => null
                    , 'journal_ref' => null
                    , 'sts' => 1
                    , 'gefo_ref_no' => null
                );
               	$rent_ledger_data[] = $debit_ledger_data;  

		}
	}	


	// others ll location dr frb 5   //Unpaid payment(UP)
    if($sche_payment_sts!='stop_payment' && $sche_payment_sts!='stop_payment_pm'){    
		for($i=1;$i<=$others_location_type_data_count;$i++){
		
			  $debit_ledger_data = array(
                 	'ledger_ref_no' => $rent_led_ref[1] 
                 	,'ledger_ref_counter' => $rent_led_ref[0] 
					,'ref_table_ref' => $ref_table_ref
                    ,'section_type' => 'RENT'
                    , 'ledger_type' => 'Rent'
                    , 'Ledger_type_info' => $ledger_type_info
                    , 'ref_id' => $rent_paid_history_id
                    , 'rent_agre_id' => $this->input->post('rent_ref_id')
                    , 'landlord_id' => ''
                    , 'payment_type' => 'GL'
                    , 'cost_center' => $this->input->post('others_location_type_data_cost_center'.$i)
                    , 'mis_code' => $this->input->post('others_location_type_data_mis'.$i)
                    , 'description' => 'Rent of '.$this->input->post('others_location_type_data_loc_name'.$i).' at  '.$location_name .'  for '.$mysqldate
                    , 'account_description' => $this->input->post('others_location_type_data_acc_desc'.$i)
                    , 'gl_account' => $this->input->post('others_location_type_data_rent_gl'.$i)
                    , 'db_cr_sts' => 'Debit'
                    , 'amount' => $this->input->post('others_location_type_data_dr_amount'.$i)
                    , 'transaction_by' => $this->session->userdata['user']['user_id']
                    , 'transaction_dt' => date('Y-m-d H:i:s')
                    , 'disburse_dt' => null
                    , 'journal_ref' => null
                    , 'sts' => 1
                    , 'gefo_ref_no' => null
                );
               	$rent_ledger_data[] = $debit_ledger_data;  

		}
	}	

	// arrear debit 3 oct 2018
	if($sche_payment_sts!='stop_payment' && $sche_payment_sts!='stop_payment_pm'){    
			if($this->input->post('arrear_dr_amount') > 0){
			  $debit_ledger_data = array(
                 	'ledger_ref_no' => $rent_led_ref[1] 
                 	,'ledger_ref_counter' => $rent_led_ref[0] 
					,'ref_table_ref' => $ref_table_ref
                    ,'section_type' => 'RENT'
                    , 'ledger_type' => 'Rent'
                    , 'Ledger_type_info' => $ledger_type_info
                    , 'ref_id' => $rent_paid_history_id
                    , 'rent_agre_id' => $this->input->post('rent_ref_id')
                    , 'landlord_id' => ''
                    , 'payment_type' => 'GL'
                    , 'cost_center' => $this->input->post('arrear_cost_center')
                    , 'mis_code' => $this->input->post('arrear_mis_code')
                    , 'description' => $this->input->post('arrear_remarks').' for the month of  '.$mysqldate.'  for '.$location_name
                    , 'account_description' => $this->input->post('arrear_loc_name')
                    , 'gl_account' => $this->input->post('arrear_rent_gl')
                    , 'db_cr_sts' => 'Debit'
                    , 'amount' => $this->input->post('arrear_dr_amount')
                    , 'transaction_by' => $this->session->userdata['user']['user_id']
                    , 'transaction_dt' => date('Y-m-d H:i:s')
                    , 'disburse_dt' => null
                    , 'journal_ref' => null
                    , 'sts' => 1
                    , 'gefo_ref_no' => null
                );
               	$rent_ledger_data[] = $debit_ledger_data;  
        }       	
	}

// vat debit	//UP
	if($sche_payment_sts!='stop_payment' && $vat_applicable=='yes' && $sche_payment_sts!='stop_payment_pm'){	
		for($i=1;$i<=$location_type_data_count;$i++){
			if($this->input->post('location_vat_dr_amount'.$i) > 0 ){
					$acc_des= $this->input->post('location_vat_acc_des'.$i);
					  $vat_debit_ledger_data = array(
		                 	'ledger_ref_no' => $rent_led_ref[1] 
		                 	,'ledger_ref_counter' => $rent_led_ref[0]
							,'ref_table_ref' => $ref_table_ref
		                    ,'section_type' => 'RENT'
		                    , 'ledger_type' => 'VAT'
		                    , 'Ledger_type_info' => $ledger_type_info
		                    , 'ref_id' => $rent_paid_history_id
		                    , 'rent_agre_id' => $this->input->post('rent_ref_id')
		                    , 'landlord_id' => ''
		                    , 'payment_type' => 'GL'
		                    , 'cost_center' => $this->input->post('location_vat_cost_center'.$i)
		                    , 'mis_code' => $this->input->post('location_vat_mis'.$i)
		                   // , 'description' => $this->input->post('location_vat_dr_amount'.$i).' taka Vat Debit for '.$this->input->post('location_vat_loc_name'.$i)
		                    , 'description' => 'Rent of '.$this->input->post('location_type_data_loc_name'.$i).' '.$others_string.' at '.$location_name.'  for '.$mysqldate.' VAT'
		                    , 'account_description' => $acc_des
		                    , 'gl_account' => $this->input->post('location_vat_rent_gl'.$i)
		                    , 'db_cr_sts' => 'Debit'
		                    , 'amount' => $this->input->post('location_vat_dr_amount'.$i)
		                    , 'transaction_by' => $this->session->userdata['user']['user_id']
		                    , 'transaction_dt' => date('Y-m-d H:i:s')
		                    , 'disburse_dt' => null
		                    , 'journal_ref' => null
		                    , 'sts' => 1
		                    , 'gefo_ref_no' => null
		                );		               
						$rent_ledger_data[] = $vat_debit_ledger_data; 
	            }    
		}

	}


	// others vat debit 5 feb	//UP
	if($sche_payment_sts!='stop_payment' && $vat_applicable=='yes' && $sche_payment_sts!='stop_payment_pm'){	
		for($i=1;$i<=$others_location_type_data_count;$i++){
			if($this->input->post('others_location_vat_dr_amount'.$i) > 0 && $this->input->post('others_location_vat_sts'.$i)=='yes'){

					  $others_vat_debit_ledger_data = array(
		                 	'ledger_ref_no' => $rent_led_ref[1] 
		                 	,'ledger_ref_counter' => $rent_led_ref[0]
							,'ref_table_ref' => $ref_table_ref
		                    ,'section_type' => 'RENT'
		                    , 'ledger_type' => 'VAT'
		                    , 'Ledger_type_info' => $ledger_type_info
		                    , 'ref_id' => $rent_paid_history_id
		                    , 'rent_agre_id' => $this->input->post('rent_ref_id')
		                    , 'landlord_id' => ''
		                    , 'payment_type' => 'GL'
		                    , 'cost_center' => $this->input->post('others_location_vat_cost_center'.$i)
		                    , 'mis_code' => $this->input->post('others_location_vat_mis'.$i)
		                    //, 'description' => $this->input->post('others_location_vat_dr_amount'.$i).' taka Vat Debit for '.$this->input->post('others_location_vat_loc_name'.$i)
		                    , 'description' => 'Rent of '.$location_name .' '.$this->input->post('others_location_vat_loc_name'.$i).' for '.$mysqldate.' VAT'
		                    , 'account_description' => $this->input->post('others_location_type_data_acc_descr'.$i)
		                    , 'gl_account' => $this->input->post('others_location_vat_rent_gl'.$i)
		                    , 'db_cr_sts' => 'Debit'
		                    , 'amount' => $this->input->post('others_location_vat_dr_amount'.$i)
		                    , 'transaction_by' => $this->session->userdata['user']['user_id']
		                    , 'transaction_dt' => date('Y-m-d H:i:s')
		                    , 'disburse_dt' => null
		                    , 'journal_ref' => null
		                    , 'sts' => 1
		                    , 'gefo_ref_no' => null
		                );		               
						$rent_ledger_data[] = $others_vat_debit_ledger_data; 
	            }    
		}

	}

// VAT    //UP
   	if($sche_payment_sts!='stop_unpaid_payment' && $sche_payment_sts!='stop_payment' && $sche_payment_sts!='stop_cost_center_pm' && $sche_payment_sts!='stop_payment_pm'){
   		if($sche_payment_sts=='stop_cost_center'){
   			$acc_name = 'Expense Payable -Rent Office';
   		
   			$acc_str= "Rent of $loc_names $others_string at $location_name for $mysqldate VAT";
   		}else{
   			$acc_name = 'VAT on Rent';
   			$vat_percent = $this->input->post('vat_percentage');
   			$acc_str= " $vat_percent pc VAT from $landlord_names for rent of $location_name for $mysqldate";
   		}
   		if($vat_applicable=='yes' &&  $this->input->post('location_vat_cr_amount') > 0 ){	
			  $vat_credit_ledger_data = array(
                 	'ledger_ref_no' => $rent_led_ref[1] 
                 	,'ledger_ref_counter' => $rent_led_ref[0]
					,'ref_table_ref' => $ref_table_ref 
                    ,'section_type' => 'RENT'
                    , 'ledger_type' => 'VAT'
                    , 'Ledger_type_info' => $ledger_type_info
                    , 'ref_id' => $rent_paid_history_id
                    , 'rent_agre_id' => $this->input->post('rent_ref_id')
                    , 'landlord_id' => ''
                    , 'payment_type' => 'GL'
                    , 'cost_center' => $this->input->post('location_vat_cost_center_cr')
                    , 'mis_code' => ''
                    //, 'description' => $this->input->post('vat_percentage').' pc VAT '.$landlord_names.' for '.$mysqldate

                    , 'description' => $acc_str
                    , 'account_description' => $acc_name
                    , 'gl_account' => $this->input->post('location_vat_rent_gl_cr')
                    , 'db_cr_sts' => 'Credit'
                    , 'amount' => $this->input->post('location_vat_cr_amount')
                    , 'transaction_by' => $this->session->userdata['user']['user_id']
                    , 'transaction_dt' => date('Y-m-d H:i:s')
                    , 'disburse_dt' => null
                    , 'journal_ref' => null
                    , 'sts' => 1
                    , 'gefo_ref_no' => null
                );
                $rent_ledger_data[] = $vat_credit_ledger_data; 
        	}  

        }  

    if($sche_payment_sts=='stop_unpaid_payment' || $sche_payment_sts=='stop_payment' || $sche_payment_sts=='stop_payment_pm'){
  	// stop receive paid dr only for both stop_unpaid_payment and stop_payment

	  	if($sche_payment_sts=='stop_payment_pm'){$ledger_type_str='Rent for Rent Accrual Pm';}else{$ledger_type_str='Rent for Rent Stop';}
	  	if($sche_payment_sts=='stop_payment' || $sche_payment_sts=='stop_payment_pm'){$account_description='Expense Payable -Rent Office';}else{$account_description='Rent Office';}
	  		  

        $debit_ledger_data = array(
                 	'ledger_ref_no' => $rent_led_ref[1] 
                 	,'ledger_ref_counter' => $rent_led_ref[0]
					,'ref_table_ref' => $ref_table_ref 
                    ,'section_type' => 'RENT'
                    , 'ledger_type' => 'Rent'
                    , 'Ledger_type_info' => $ledger_type_str
                    , 'ref_id' => $rent_paid_history_id
                    , 'rent_agre_id' => $this->input->post('rent_ref_id')
                    , 'landlord_id' => ''
                    , 'payment_type' => ''
                    , 'cost_center' => $this->input->post('scc_cost_center')
                    , 'mis_code' => null
                    , 'description' => $this->input->post('scc_data_narr')
                    , 'account_description' => $account_description
                    , 'gl_account' => $this->input->post('scc_prov_gl')
                    , 'db_cr_sts' => 'Debit'
                    , 'amount' => $this->input->post('scc_data_dr_amount')
                    , 'transaction_by' => $this->session->userdata['user']['user_id']
                    , 'transaction_dt' => date('Y-m-d H:i:s')
                    , 'disburse_dt' => null
                    , 'journal_ref' => null
                    , 'sts' => 1
                    , 'gefo_ref_no' => null
                );
                $rent_ledger_data[] = $debit_ledger_data; 

	// others dr 10 oct 2018
            if($this->input->post('other_scc_data_dr_amount') > 0 ){     
                 $debit_ledger_data = array(
                 	'ledger_ref_no' => $rent_led_ref[1] 
                 	,'ledger_ref_counter' => $rent_led_ref[0]
					,'ref_table_ref' => $ref_table_ref 
                    ,'section_type' => 'RENT'
                    , 'ledger_type' => 'Rent'
                    , 'Ledger_type_info' => $ledger_type_str
                    , 'ref_id' => $rent_paid_history_id
                    , 'rent_agre_id' => $this->input->post('rent_ref_id')
                    , 'landlord_id' => ''
                    , 'payment_type' => ''
                    , 'cost_center' => $this->input->post('other_scc_cost_center')
                    , 'mis_code' => null
                    , 'description' => $this->input->post('other_scc_data_narr')
                    , 'account_description' => $account_description
                    , 'gl_account' => $this->input->post('other_scc_prov_gl')
                    , 'db_cr_sts' => 'Debit'
                    , 'amount' => $this->input->post('other_scc_data_dr_amount')
                    , 'transaction_by' => $this->session->userdata['user']['user_id']
                    , 'transaction_dt' => date('Y-m-d H:i:s')
                    , 'disburse_dt' => null
                    , 'journal_ref' => null
                    , 'sts' => 1
                    , 'gefo_ref_no' => null
                );
                $rent_ledger_data[] = $debit_ledger_data; 
            }    

// need to add col for stop_payment 
        if($sche_payment_sts=='stop_payment' || $sche_payment_sts=='stop_payment_pm'){
        	if($this->input->post('scc_cost_center_vat_sts')=='yes'){
       			$account_description='Expense Payable -Rent Office';
       			$debit_ledger_data = array(
                 	'ledger_ref_no' => $rent_led_ref[1] 
                 	,'ledger_ref_counter' => $rent_led_ref[0]
					,'ref_table_ref' => $ref_table_ref 
                    ,'section_type' => 'RENT'
                    , 'ledger_type' => 'Rent'
                    , 'Ledger_type_info' => $ledger_type_str
                    , 'ref_id' => $rent_paid_history_id
                    , 'rent_agre_id' => $this->input->post('rent_ref_id')
                    , 'landlord_id' => ''
                    , 'payment_type' => ''
                    , 'cost_center' => $this->input->post('scc_cost_center_acc_vat')
                    , 'mis_code' => null
                    , 'description' => $this->input->post('scc_data_narr_acc_vat')
                    , 'account_description' => $account_description
                    , 'gl_account' => $this->input->post('scc_prov_gl_acc_vat')
                    , 'db_cr_sts' => 'Debit'
                    , 'amount' => $this->input->post('scc_data_dr_amount_acc_vat')
                    , 'transaction_by' => $this->session->userdata['user']['user_id']
                    , 'transaction_dt' => date('Y-m-d H:i:s')
                    , 'disburse_dt' => null
                    , 'journal_ref' => null
                    , 'sts' => 1
                    , 'gefo_ref_no' => null
                );
                $rent_ledger_data[] = $debit_ledger_data; 
            }    
       }
    }    

        if($sche_payment_sts!='stop_cost_center' && $sche_payment_sts!='advance_rent_payment' && $sche_payment_sts!='stop_cost_center_pm'){	
	$landlords_count = $this->input->post('landlords_count');
	$ledger_type='Rent Payment Credit for Advance';
	if($sche_payment_sts=='stop_unpaid_payment'){ $ledger_type='Advance Receive';  }
	if($sche_payment_sts=='stop_payment_pm'){ $ledger_type='Advance Rent';  }
	if($sche_payment_sts=='stop_payment' || $sche_payment_sts=='unpaid_payment'){ $ledger_type='Advance Receive';  }

	for($i=1;$i<=$landlords_count;$i++){
//$rent_led_ref = $this->user_model->get_bill_refno_rent('rent_ledger', 'ledger_ref_counter', 'ledger_ref_no', 'transaction_dt', 13, '');
   		if($this->input->post('landlord_adv_adj_amount_cr'.$i) > 0 ){

			  $debit_ledger_data = array(
                 	'ledger_ref_no' => $rent_led_ref[1] 
                 	,'ledger_ref_counter' => $rent_led_ref[0] 
					,'ref_table_ref' => $ref_table_ref
                    ,'section_type' => 'RENT'
                    , 'ledger_type' => $ledger_type
                    , 'Ledger_type_info' => $ledger_type_info
                    , 'ref_id' => $rent_paid_history_id
                    , 'rent_agre_id' => $this->input->post('rent_ref_id')
                    , 'landlord_id' => ''
					, 'payment_type' => ''
                    , 'cost_center' => $this->input->post('landlord_adv_cost_center'.$i)
                    , 'mis_code' => ''
                    , 'description' => 'Adjustment of advance rent of '.$location_name.' for '.$mysqldate
                    //, 'description' => 'Adjustment of advance rent from Rent of '.$this->input->post('landlord_adv_ll_name'.$i).' for '.$mysqldate
                    , 'account_description' => 'Advance Rent'
                    , 'gl_account' => $this->input->post('landlord_adv_rent_gl'.$i)
                    , 'db_cr_sts' => 'Credit'
                    , 'amount' => $this->input->post('landlord_adv_adj_amount_cr'.$i)
                    , 'transaction_by' => $this->session->userdata['user']['user_id']
                    , 'transaction_dt' => date('Y-m-d H:i:s')
                    , 'disburse_dt' => null
                    , 'journal_ref' => null
                    , 'sts' => 1
                    , 'gefo_ref_no' => null
                );
                
				$rent_ledger_data[] = $debit_ledger_data;
			}	
		}
	}  

  //SUP SP 
  	if($sche_payment_sts=='stop_unpaid_payment' || $sche_payment_sts=='stop_payment' || $sche_payment_sts=='stop_payment_pm'){
  	// stop receive paid dr only for both stop_unpaid_payment and stop_payment

  	if($sche_payment_sts=='stop_payment_pm'){$ledger_type_str='Rent for Rent Accrual Pm';}else{$ledger_type_str='Rent for Rent Stop';}
  	if($sche_payment_sts=='stop_payment' || $sche_payment_sts=='stop_payment_pm'){$account_description='Expense Payable -Rent Office';}else{$account_description='Rent Office';}
  		  

       // ----------------------------------- change in 12 sep 2018
	      

  	// location_type wise vat credit only for stop_unpaid_payment
         if($sche_payment_sts=='stop_unpaid_payment'){    

		  		for($i=1;$i<=$location_type_data_count;$i++){
		  			$location_name = $this->input->post('location_type_data_loc_name'.$i);
		  			$vat_credit_ledger_data = array(
		                 	'ledger_ref_no' => $rent_led_ref[1] 
		                 	,'ledger_ref_counter' => $rent_led_ref[0] 
							,'ref_table_ref' => $ref_table_ref
		                    ,'section_type' => 'RENT'
		                    , 'ledger_type' => 'VAT'
		                    , 'Ledger_type_info' => $ledger_type_info
		                    , 'ref_id' => $rent_paid_history_id
		                    , 'rent_agre_id' => $this->input->post('rent_ref_id') 
		                    , 'landlord_id' => ''
		                    , 'payment_type' => 'GL'
		                    , 'cost_center' => $this->input->post('location_vat_cost_center_cr'.$i)
		                    , 'mis_code' => ''
		                    , 'description' => $this->input->post('vat_percentage'.$i).' pc VAT '.$landlord_names.' for '.$mysqldate
		                    , 'account_description' => 'VAT on Rent'
		                    , 'gl_account' => $this->input->post('location_vat_rent_gl_cr'.$i)
		                    , 'db_cr_sts' => 'Credit'
		                    , 'amount' => $this->input->post('location_vat_cr_amount'.$i)
		                    , 'transaction_by' => $this->session->userdata['user']['user_id']
		                    , 'transaction_dt' => date('Y-m-d H:i:s')
		                    , 'disburse_dt' => null
		                    , 'journal_ref' => null
		                    , 'sts' => 1
		                    , 'gefo_ref_no' => null
		                );
		                $rent_ledger_data[] = $vat_credit_ledger_data;         
				}
			}   	
	}



	if($sche_payment_sts=='stop_payment_pm' || $sche_payment_sts=='stop_payment'){
		if($this->input->post('pm_location_vat_cr_amount') > 0){
				$vat_credit_ledger_data = array(
		                 	'ledger_ref_no' => $rent_led_ref[1] 
		                 	,'ledger_ref_counter' => $rent_led_ref[0] 
							,'ref_table_ref' => $ref_table_ref
		                    ,'section_type' => 'RENT'
		                    , 'ledger_type' => 'VAT'
		                    , 'Ledger_type_info' => $ledger_type_info
		                    , 'ref_id' => $rent_paid_history_id
		                    , 'rent_agre_id' => $this->input->post('rent_ref_id') 
		                    , 'landlord_id' => ''
		                    , 'payment_type' => 'GL'
		                    , 'cost_center' => $this->input->post('pm_location_vat_cost_center_cr')
		                    , 'mis_code' => ''
		                    , 'description' => $this->input->post('pm_vat_narration')
		                
		                    , 'account_description' => 'VAT on Rent'
		                    , 'gl_account' => $this->input->post('pm_location_vat_rent_gl_cr')
		                    , 'db_cr_sts' => 'Credit'
		                    , 'amount' => $this->input->post('pm_location_vat_cr_amount')
		                    , 'transaction_by' => $this->session->userdata['user']['user_id']
		                    , 'transaction_dt' => date('Y-m-d H:i:s')
		                    , 'disburse_dt' => null
		                    , 'journal_ref' => null
		                    , 'sts' => 1
		                    , 'gefo_ref_no' => null
		                );
		                $rent_ledger_data[] = $vat_credit_ledger_data; 
		}            
	}

//  tax credit   // UP
	$landlords_count = $this->input->post('landlords_count');
	if($sche_payment_sts!='stop_cost_center' && $sche_payment_sts!='advance_rent_payment' && $sche_payment_sts!='stop_cost_center_pm' && $tax_wived!='wived_yes'){	
		for($i=1;$i<=$landlords_count;$i++){
//$rent_led_ref = $this->user_model->get_bill_refno_rent('rent_ledger', 'ledger_ref_counter', 'ledger_ref_no', 'transaction_dt', 13, '');
   			    $tax_rate= number_format($this->input->post('landlord_tax_amount_percent'.$i),2);
			    $debit_ledger_data = array(
                 	'ledger_ref_no' => $rent_led_ref[1] 
                 	,'ledger_ref_counter' => $rent_led_ref[0] 
					,'ref_table_ref' => $ref_table_ref
                    ,'section_type' => 'RENT'
                    , 'ledger_type' => 'TAX'
                    , 'Ledger_type_info' => $ledger_type_info
                    , 'ref_id' => $rent_paid_history_id
                    , 'rent_agre_id' => $this->input->post('rent_ref_id')
                    , 'landlord_id' => $this->input->post('single_landlord_id_tax'.$i)
					, 'payment_type' => 'GL'
                    , 'cost_center' => $this->input->post('landlord_tax_cost_center'.$i)
                    , 'mis_code' => ''
                    
                    //, 'description' => $this->input->post('landlord_tax_amount_percent'.$i).'pc Tax '.$this->input->post('landlord_tax_ll_name'.$i).' for '.$mysqldate
                    , 'description' => $tax_rate.' pc Tax from'.$this->input->post('landlord_tax_ll_name'.$i).' from rent of '.$loc_names.' '.$others_string.' at '.$location_name.'  for '.$mysqldate
                    					//5.00 pc Tax from Delux fashion Ltd. from rent of ATM Bayzid Bostami Ctg for Aug 2018 	
                    , 'account_description' => 'Tax-Rent office'
                    , 'gl_account' => $this->input->post('landlord_tax_account_gl'.$i)
                    , 'db_cr_sts' => 'Credit'
                    , 'amount' => $this->input->post('landlord_tax_amount'.$i)
                    , 'transaction_by' => $this->session->userdata['user']['user_id']
                    , 'transaction_dt' => date('Y-m-d H:i:s')
                    , 'disburse_dt' => null
                    , 'journal_ref' => null
                    , 'sts' => 1
                    , 'gefo_ref_no' => null
                );
                $rent_ledger_data[] = $debit_ledger_data; 


		}
	}

// bank account sd credit   // UP
   if($sche_payment_sts!='stop_cost_center' && $sche_payment_sts!='advance_rent_payment' && $sche_payment_sts!='stop_cost_center_pm'){	
   		if($this->input->post('bank_sd_cr_amount') > 0 ){
			  $sd_credit_ledger_data = array(
                 	'ledger_ref_no' => $rent_led_ref[1] 
                 	,'ledger_ref_counter' => $rent_led_ref[0] 
					,'ref_table_ref' => $ref_table_ref
                    ,'section_type' => 'RENT'
                    , 'ledger_type' => 'SD Receive'
                    , 'Ledger_type_info' => $ledger_type_info
                    , 'ref_id' => $rent_paid_history_id
                    , 'rent_agre_id' => $this->input->post('rent_ref_id')
                    , 'landlord_id' => ''
                    , 'payment_type' => 'GL'
                    , 'cost_center' => $this->input->post('bank_cost_center_cr')
                    , 'mis_code' => ''
                    //, 'description' => $this->input->post('bank_sd_cr_amount').' taka Credit for Bank'
                    , 'description' => 'Adjustment of Security Deposit '.$location_name.' for '.$mysqldate
                    , 'account_description' => 'Advance Rent'
                    , 'gl_account' => $this->input->post('bank_sd_gl_cr')
                    , 'db_cr_sts' => 'Credit'
                    , 'amount' => $this->input->post('bank_sd_cr_amount')
                    , 'transaction_by' => $this->session->userdata['user']['user_id']
                    , 'transaction_dt' => date('Y-m-d H:i:s')
                    , 'disburse_dt' => null
                    , 'journal_ref' => null
                    , 'sts' => 1
                    , 'gefo_ref_no' => null
                );
			$rent_ledger_data[] = $sd_credit_ledger_data; 

		}
	}	
	
// ll credit amt final // UP // credit_account in 19 march 2019
$credit_account = $this->input->post('credit_account');
if($credit_account=='landlord'){	
	$landlords_count = $this->input->post('landlords_count');
	$acc_des='';
	if($sche_payment_sts!='advance_rent_payment' && $sche_payment_sts!='stop_cost_center' && $sche_payment_sts!='stop_cost_center_pm'){ 
			for($i=1;$i<=$landlords_count;$i++){
				$ll_name= $this->input->post('per_landlord_amt_name_tot_cr'.$i);
				$landlord= $this->input->post('per_landlord_name'.$i);
			   
			   if($sche_payment_sts=='stop_cost_center' || $sche_payment_sts=='stop_cost_center_pm'){ 
			   	//$description = "Credited Rent to Provision  for $mysqldate"; 
			   	//$description = "Rent of $loc_names $others_string at $location_name for $mysqldate"; 
			   	$description = "Credited Rent of $location_name for $mysqldate"; 
			   }
		
			   //else if($sche_payment_sts=='stop_payment'){ $description = "Credited Rent to Provision  for $mysqldate"; }
			   else{ 
				   	if($this->input->post('per_landlord_payment_mode_tot_cr'.$i)=='Pay Order'){
				   		$description = "PO favouring  $ll_name ";
				   		$acc_des = '- Payorder Suspense A/c ';
				   	}else{
				   		$description = "Credited Rent of $location_name for $mysqldate with $loc_names, $others_loc_names";
				   	}
			   	 
			   }
			   		if($this->input->post('per_landlord_final_amt_tot_cr'.$i) > 0 ){
						  $credit_ledger_data_final = array(
								'ledger_ref_no' => $rent_led_ref[1] 
								,'ledger_ref_counter' => $rent_led_ref[0] 
								,'ref_table_ref' => $ref_table_ref
								,'section_type' => 'RENT'
								, 'ledger_type' => 'Rent'
								, 'Ledger_type_info' => $ledger_type_info
								, 'ref_id' => $rent_paid_history_id
								, 'rent_agre_id' => $this->input->post('rent_ref_id')
								, 'landlord_id' => $this->input->post('single_landlord_id_tot_cr'.$i)
								, 'payment_type' => $this->input->post('per_landlord_payment_mode_tot_cr'.$i)
								, 'cost_center' => $this->input->post('loc_cost_center_code_tot_cr'.$i)
								, 'mis_code' => ''
								, 'description' => $description
								, 'account_description' => $this->input->post('per_landlord_name'.$i).$acc_des
								, 'gl_account' => $this->input->post('per_landlord_acc_no_tot_cr'.$i)
								, 'db_cr_sts' => 'Credit'
								, 'amount' => $this->input->post('per_landlord_final_amt_tot_cr'.$i)
								, 'transaction_by' => $this->session->userdata['user']['user_id']
								, 'transaction_dt' => date('Y-m-d H:i:s')
								, 'disburse_dt' => null
								, 'journal_ref' => null
								, 'sts' => 1
								, 'gefo_ref_no' => null
							);
						$rent_ledger_data[] = $credit_ledger_data_final;	
					}	
	
			}
	}
}	

if($credit_account!='landlord'){	
	$i=1;
	$acc_des='';
	if($sche_payment_sts!='advance_rent_payment' && $sche_payment_sts!='stop_cost_center' && $sche_payment_sts!='stop_cost_center_pm'){ 
			
				$ll_name= $this->input->post('per_landlord_amt_name_tot_cr'.$i);
				$landlord= $this->input->post('per_landlord_name'.$i);
			   
			   if($sche_payment_sts=='stop_cost_center' || $sche_payment_sts=='stop_cost_center_pm'){ 
			   	//$description = "Credited Rent to Provision  for $mysqldate"; 
			   	//$description = "Rent of $loc_names $others_string at $location_name for $mysqldate"; 
			   	$description = "Credited Rent of $location_name for $mysqldate"; 
			   }
		
			   //else if($sche_payment_sts=='stop_payment'){ $description = "Credited Rent to Provision  for $mysqldate"; }
			   else{ 
				  
				   	$description = "Credited Rent of $location_name for $mysqldate with $loc_names, $others_loc_names";
				
			   	 
			   }

			   
						  $credit_ledger_data_final = array(
								'ledger_ref_no' => $rent_led_ref[1] 
								,'ledger_ref_counter' => $rent_led_ref[0] 
								,'ref_table_ref' => $ref_table_ref
								,'section_type' => 'RENT'
								, 'ledger_type' => 'Rent'
								, 'Ledger_type_info' => $ledger_type_info
								, 'ref_id' => $rent_paid_history_id
								, 'rent_agre_id' => $this->input->post('rent_ref_id')
								, 'landlord_id' => $this->input->post('single_landlord_id_tot_cr'.$i)
								, 'payment_type' => $this->input->post('per_landlord_payment_mode_tot_cr'.$i)
								, 'cost_center' => $this->input->post('loc_cost_center_code_tot_cr'.$i)
								, 'mis_code' => ''
								, 'description' => $description
								, 'account_description' => $this->input->post('per_landlord_name'.$i).$acc_des
								, 'gl_account' => $this->input->post('per_landlord_acc_no_tot_cr'.$i)
								, 'db_cr_sts' => 'Credit'
								, 'amount' => $this->input->post('per_landlord_final_amt_tot_cr'.$i)
								, 'transaction_by' => $this->session->userdata['user']['user_id']
								, 'transaction_dt' => date('Y-m-d H:i:s')
								, 'disburse_dt' => null
								, 'journal_ref' => null
								, 'sts' => 1
								, 'gefo_ref_no' => null
							);
						$rent_ledger_data[] = $credit_ledger_data_final;	
	
			
	}
}	
// only for rent in adv in schedule   // ARP
	if($sche_payment_sts=='advance_rent_payment'){ 
	$description = "Credited to Advance rent  for $mysqldate"; 

		$rent_in_adv_credit_ledger_data = array(
                 	'ledger_ref_no' => $rent_led_ref[1] 
                 	,'ledger_ref_counter' => $rent_led_ref[0]
					,'ref_table_ref' => $ref_table_ref
                    ,'section_type' => 'RENT'
                    , 'ledger_type' => 'Advance Receive'
                 
                    , 'Ledger_type_info' => $ledger_type_info
                    , 'ref_id' => $rent_paid_history_id
                    , 'rent_agre_id' => $this->input->post('rent_ref_id')
                    , 'landlord_id' => null
					, 'payment_type' => 'GL'
                    , 'cost_center' => $this->input->post('ria_br_code')
                    , 'mis_code' => ''
                    , 'description' => $description
                    , 'account_description' => 'Advance Gl'
                    , 'gl_account' => $this->input->post('ria_adv_gl')
                    , 'db_cr_sts' => 'Credit'
                    , 'amount' => $this->input->post('ria_adv_amount')
                    , 'transaction_by' => $this->session->userdata['user']['user_id']
                    , 'transaction_dt' => date('Y-m-d H:i:s')
                    , 'disburse_dt' => null
                    , 'journal_ref' => null
                    , 'sts' => 1
                    , 'gefo_ref_no' => null
                );
                $rent_ledger_data[] = $rent_in_adv_credit_ledger_data;	
    }            


// only for rent in adv in schedule
	if($sche_payment_sts=='stop_cost_center' || $sche_payment_sts=='stop_cost_center_pm'){ 

		
		$description = "Rent of $loc_names $others_string at $location_name for $mysqldate"; 

		$rent_in_adv_credit_ledger_data = array(
                 	'ledger_ref_no' => $rent_led_ref[1] 
                 	,'ledger_ref_counter' => $rent_led_ref[0] 
					,'ref_table_ref' => $ref_table_ref
                    ,'section_type' => 'RENT'
                    , 'ledger_type' => 'Rent'
                 	
                    , 'Ledger_type_info' => $ledger_type_info
                    , 'ref_id' => $rent_paid_history_id
                    , 'rent_agre_id' => $this->input->post('rent_ref_id')
                    , 'landlord_id' => null
					, 'payment_type' => 'GL'
                    , 'cost_center' => $this->input->post('scc_br_code')
                    , 'mis_code' => ''
                    , 'description' => $description
                    , 'account_description' => 'Expense Payable -Rent Office'
                    , 'gl_account' => $this->input->post('scc_adv_gl')
                    , 'db_cr_sts' => 'Credit'
                    , 'amount' => $this->input->post('scc_adv_amount')
                    , 'transaction_by' => $this->session->userdata['user']['user_id']
                    , 'transaction_dt' => date('Y-m-d H:i:s')
                    , 'disburse_dt' => null
                    , 'journal_ref' => null
                    , 'sts' => 1
                    , 'gefo_ref_no' => null
                );
               
				$rent_ledger_data[] = $rent_in_adv_credit_ledger_data;
    }


		$this->db->insert_batch('rent_ledger', $rent_ledger_data);
		
		 $checked_schedule_sd_id=$this->input->post('checked_schedule_sd_ids');
		 $checked_schedule_sd_id_array= explode(",",$checked_schedule_sd_id);

		 $sd_checked_adjustment_amt=$this->input->post('sd_checked_adjustment_amt');
		 $sd_checked_adjustment_amt_array= explode(",",$sd_checked_adjustment_amt);

		 foreach($checked_schedule_sd_id_array as  $key => $single_schedule_sd_id){

		 	$schedule_sd_update_data = array(

		 		'adjust_amount' => $sd_checked_adjustment_amt_array[$key]
		 	);
		 	$this->db->where('id',$single_schedule_sd_id);
			$this->db->update('rent_security_deposit',$schedule_sd_update_data);
		 
		 }

		 $paid_sts= 'paid';
		 if($sche_payment_sts=='advance_rent_payment'){ $paid_sts= 'paid'; }
		 if($sche_payment_sts=='stop_cost_center' || $sche_payment_sts=='stop_cost_center_pm'){ $paid_sts= 'stop'; }
 
		 $checked_schedule_id=$this->input->post('checked_schedule_ids');
		 $sd_adjust_amt =$this->input->post('sd_adjust_amt');
		 $checked_schedule_id_array= explode(",",$checked_schedule_id);
         $sche_id_counter = count($checked_schedule_id_array);
                
         $sd_amt_per_sche = (float)($sd_adjust_amt/$sche_id_counter);


         
		 foreach($checked_schedule_id_array as $single_schedule_id){

		 	$schedule_update_data = array(

		 		'paid_sts' => $paid_sts
		 		,'paid_history_id' => $this->input->post('rent_paid_history_id')
		 		,'adjust_sec_deposit' => $sd_amt_per_sche
		 		,'sche_add_sts' => 2
		 	);
		 	$this->db->where('id',$single_schedule_id);
			$this->db->update('rent_ind_schedule',$schedule_update_data);
		 
		 }

            

			//if($sche_payment_sts=='stop_cost_center_pm' || $sche_payment_sts=='stop_payment_pm'){ 
			if($sche_payment_sts=='stop_cost_center_pm'){ 

				$paid_update_data = array(
					'stop_cost_center_amt' =>$this->input->post('scc_adv_amount')
					,'approve_by' =>$this->session->userdata['user']['user_id']
					,'approve_dt' => date('Y-m-d, H:i:s')
					
				);
			 }else{

			 	$paid_update_data = array(
				
					'approve_by' =>$this->session->userdata['user']['user_id']
					,'approve_dt' => date('Y-m-d, H:i:s')
				
				);
			 }
			
			$this->db->where('id',$this->input->post('rent_paid_history_id'));
			$this->db->update('rent_paid_history',$paid_update_data);

			// 20 may 2019
			// $ledger_update_data = array(
			// 		'pay_approve_sts' =>1
			// );
			// $this->db->where('ref_id',$rent_paid_history_id);
			// $this->db->update('rent_ledger',$ledger_update_data);

			$insert_idss =$this->input->post('rent_paid_history_id');


	}
		
			return $insert_idss;

		
}
	
function check_status($id){

		$r=$this->db->get_where('rent_paid_history',array('id' => $id,'sts' =>1));
		$num=$r->num_rows();
		return $num;

	}


	function get_single_paid_data($id){

		//$r=$this->db->get_where('rent_paid_history',array('id' => $id,'sts' =>1));
		$sql= "SELECT rph.*, ris.maturity_dt, ris.schedule_strat_dt, ris.rent_fraction_day FROM rent_paid_history rph
				left join rent_ind_schedule ris on ris.id=rph.checked_schedule_ids 
				WHERE 
				rph.id =$id 
				AND rph.sts=1
				
				";
		$r=$this->db->query($sql);
		
		return $r->row();

	}


	function get_prov_ids($rent_id){

		$sql= "SELECT GROUP_CONCAT( provision_id SEPARATOR ',') all_provision_id FROM rent_ind_schedule 
				WHERE 
				rent_agree_id =$rent_id 
				AND paid_sts='unpaid'
				
				";
		$query=$this->db->query($sql);
		// return $num;
		return $query->row();

	}

	function get_prov_ids_for_verify($rent_id,$sche_ids){

		$sql= "SELECT GROUP_CONCAT( provision_id SEPARATOR ',') all_provision_id FROM rent_ind_schedule 
				WHERE 
				rent_agree_id =$rent_id 
				AND paid_sts='unpaid'
				AND id in($sche_ids)
				";
		$query=$this->db->query($sql);
		// return $num;
		return $query->row();

	}

	function get_location_type_data($id){

		$sql="SELECT j0.*,j1.name,j1.code FROM rent_agr_loc_type_and_cost_center AS j0
		left join ref_location_type j1 on j1.id=j0.location_type_id 
		WHERE j0.sts=1 AND j0.rent_agree_id='".$id."'";

		$query=$this->db->query($sql);
		return $query->result();

	}

	function get_non_prov_paid_data($rent_id,$checked_schedule_ids){

		$sql="SELECT j0.* ,
				GROUP_CONCAT(j0.id SEPARATOR ',' ) sche_id_list,
				SUM(j0.monthly_rent_amount) AS tot_monthly_rent,
				SUM(j0.total_others_amount) AS tot_others_amount,
				SUM(j0.area_amount) AS tot_area_amount,
				SUM(j0.adjustment_adv) AS tot_adjustment_adv,
				SUM(j0.temp_sec_deposit) AS tot_sd_amt,
				SUM(j0.others_car) AS tot_others_car,
				SUM(j0.others_gas) AS tot_others_gas,
				SUM(j0.others_generator) AS tot_others_generator,
				SUM(j0.others_service) AS tot_others_service,
				SUM(j0.others_water) AS tot_others_water

				FROM rent_ind_schedule AS j0
						
						WHERE 
						j0.rent_agree_id=$rent_id 
						
						AND j0.id IN ($checked_schedule_ids) ";

		$query=$this->db->query($sql);
		return $query->row();

	}

	

	function get_others_type_data($id){

		// $sql="SELECT j0.* FROM rent_agr_other_locations AS j0 
		// WHERE j0.sts=1 AND j0.rent_agree_id='".$id."' and other_loc_type_id!='' and j0.other_sq_ft > 0";

		$sql="SELECT j0.*,j1.name,j1.account FROM rent_agr_other_locations AS j0 
		left join ref_others_rent_type j1 on j1.name=j0.other_loc_type_id 
		WHERE j0.sts=1 AND j0.rent_agree_id='".$id."' and other_loc_type_id!=''";

		$query=$this->db->query($sql);
		return $query->result();

	}

	function get_others_type_names($id){

		$sql="
		SELECT GROUP_CONCAT(DISTINCT rlt.name) AS loc_names, GROUP_CONCAT(DISTINCT j0.other_loc_type_id) AS others_loc_names
		FROM rent_agr_loc_type_and_cost_center racc 
		LEFT JOIN rent_agr_other_locations  j0 ON racc.rent_agree_id=j0.rent_agree_id 
		LEFT JOIN ref_location_type rlt ON rlt.id=racc.location_type_id
		WHERE racc.rent_agree_id='".$id."'";

		$query=$this->db->query($sql);
		return $query->row();

	}

function check_v_status($id){

		$r=$this->db->get_where('rent_security_deposit',array('id' => $id,'sts' =>1,'v_sts' =>1));
		$num=$r->num_rows();
		return $num;

	}

	function delete_action()
	{
            

		$check=0;
		//$r=$this->db->get_where('rent_paid_history',array('id' => $this->input->post('id'),'v_sts' =>1));
		//paid_sts = 'unpaid',paid_history_id = NULL,
		$r=$this->db->get_where('rent_paid_history',array('id' => $this->input->post('id')))->row();
        $paid_sts='unpaid';       
        if($r->sche_payment_sts=='stop_payment_pm' || $r->sche_payment_sts=='stop_cost_center_pm'){
        	$paid_sts='stop'; 
        }elseif ($r->sche_payment_sts=='stop_cost_center') {
        	$paid_sts='unpaid';
        }elseif ($r->sche_payment_sts=='stop_payment') {
        	$paid_sts='stop';
        }elseif ($r->sche_payment_sts=='advance_rent_payment') {
        	$paid_sts='advance';
        }       

                $check = 1;

                $sql = "update rent_ind_schedule
                		set
                		sche_add_sts=0,
                		paid_sts = '$paid_sts',
                		paid_history_id = NULL, 
                		temp_sec_deposit=temp_sec_deposit-$r->sd_adjust_amt,
                		adjust_sec_deposit=adjust_sec_deposit-$r->sd_adjust_amt
                		where id=$r->checked_schedule_ids";
                $this->db->query($sql);	
// update security_deposit data
                $checked_schedule_sd_id=$r->checked_schedule_sd_ids;
		 		
                $sd_checked_adjustment_amt=$r->sd_checked_adjustment_amt;
                if($checked_schedule_sd_id!='' && $sd_checked_adjustment_amt!=''){

                	$checked_schedule_sd_id_array= explode(",",$checked_schedule_sd_id);
                	$sd_checked_adjustment_amt_array= explode(",",$sd_checked_adjustment_amt);

					 foreach($checked_schedule_sd_id_array as  $key => $single_schedule_sd_id){
					 	$sd_amt= $sd_checked_adjustment_amt_array[$key];
					 	$sql_1 = "update rent_security_deposit
	                		set
	                		adjust_amount= adjust_amount - $sd_amt
	                		where id=$single_schedule_sd_id";
	                	$this->db->query($sql_1);	
					 
					 }
                }
               


                $this->db->where('id', $this->input->post('id'));
                $this->db->delete('rent_paid_history');

                $this->db->where('ref_id', $this->input->post('id'));
                $this->db->delete('rent_ledger');	

		return $check;
	}

	
  function rent_landlords_get_info($id) {

  //       $sql = "select j0.*,j1.name, j1.account_no as landlord_acc_no, j1.payment_mode as landlord_payment_mode,
  //       j2.name AS br_name, j2.`account_no` AS br_acc  from 
		// rent_agr_landlords j0
  //       left join vendor j1 on j0.vendor_id = j1.vendor_id 
  //       LEFT JOIN ref_branch j2 ON j1.branch_code = j2.code 
		// where j0.rent_agre_id=$id and j0.sts=1";

		$sql = "select j0.*,j1.name as ll_name, j1.payment_mode as landlord_payment_mode,j1.branch_code,
        IF(j1.`payment_mode`='Pay Order',j2.account_no,j1.account_no) AS landlord_acc_no,
        IF(j1.`payment_mode`='Pay Order',j2.name,j1.name) AS name  from 
		rent_agr_landlords j0
        left join vendor j1 on j0.vendor_id = j1.vendor_id 
        LEFT JOIN ref_branch j2 ON j1.branch_code = j2.code 
		where j0.rent_agre_id=$id and j0.sts=1";

        $q = $this->db->query($sql);
        return $q->result();
    }


	// single data
	function get_single_row_info($tbl_name,$id)
	{
		
		$sql="SELECT * from $tbl_name where id=$id limit 1";
			
		$query=$this->db->query($sql);
		return $query->row();
	}
	function get_info($add_edit,$id)
	{
		if($id!=''){
			$this->db->limit(1);
			$data = $this->db->get_where('rent_paid_history', array('id' => $id));
			return $data->row();
		}
		return array();
	}	

	function rent_sd_info_by_agree_id($agree_id)
	{
		
			$data = $this->db->get_where('rent_security_deposit', array('rent_agre_id' => $agree_id,'sts'=> 1,'sd_current_sts_id' => 5));
		
			return $data->result();
		
	}
	function get_parameter_data_where_in($table,$orderby,$where=NULL){
		 $this->db->select('*',FALSE);
		 $this->db->from($table);
		 if(!empty($where)) $this->db->where_in('id',$where);		 
		 $this->db->order_by($orderby);
		 $q=$this->db->get();
		 return $q->result();
	}
	function get_parameter_data_single($table,$orderby,$where=NULL)
	{
	     $this->db->select('*',FALSE);
		 $this->db->from($table);
		 if(!empty($where)) $this->db->where($where);		 
		 $this->db->order_by($orderby);
		 $this->db->limit(1);
		 $q=$this->db->get();
		 return $q->row();
	}
	function get_parameter_data($table,$orderby,$where=NULL)
	{
	     $this->db->select('*',FALSE);
		 $this->db->from($table);
		 if(!empty($where)) $this->db->where($where);		 
		 $this->db->order_by($orderby);
		 $q=$this->db->get();
		 return $q->result();
	}

	

	function get_search_data()
	{
		
			$cost_center_id=$this->input->post('cost_center_id');
			//echo $cost_center_id;
			// exit();
			$location_name=$this->input->post('location_name');
			if($cost_center_id!='0'){
				$where_str= "AND agree_cost_center= '".$cost_center_id."' ";
			}else{
				$where_str='';
			}
			if($location_name!=''){
				$location_str= "AND location_name LIKE '%$location_name%' ";
			}else{
				$location_str= '';
			}
		$query=$this->db->query("SELECT * FROM rent_agreement WHERE location_owner='rented'  ".$where_str." ".$location_str."" );

	
		return $query->result();
		 
	}

	function get_agree_amt_percent($rent_agre_id){
		$query=$this->db->query("SELECT * FROM rent_agr_landlords WHERE rent_agre_id='".$rent_agre_id."'" );
		return $query->result();
	}

	 function get_others_no_tax_amount($id) {

        $sql = "SELECT 
                COALESCE(SUM(other_cost_in_percent),0)
                AS tax_not_apply 
                FROM rent_agr_other_locations
                WHERE tax_sts='no'
                AND rent_agree_id='" . $id . "' ";
     
        $query = $this->db->query($sql);
        return $query->row();
    }

    function get_loc_type_no_tax_percent($id) {

        $sql = "SELECT 
                ROUND(SUM(cost_in_percent),4)
                AS tax_not_apply_percent 
                FROM rent_agr_loc_type_and_cost_center
                WHERE loc_tax_sts='no'
                AND rent_agree_id='" . $id . "' ";
     
        $query = $this->db->query($sql);
        return $query->row();
    }

	function approve_data_insert($single_paid_data,$paid_id){
		   
		$location_type_data=$this->rent_schedule_payment_model->get_location_type_data($single_paid_data['agreement_id']);		   
		$others_type_data=$this->rent_schedule_payment_model->get_others_type_data($single_paid_data['agreement_id']);
		$others_type_names=$this->rent_schedule_payment_model->get_others_type_names($single_paid_data['agreement_id']);
		$landlords_result = $this->rent_schedule_payment_model->rent_landlords_get_info($single_paid_data['agreement_id']);
        $result_agree_info = $this->rent_schedule_payment_model->get_single_row_info('rent_agreement',$single_paid_data['agreement_id']);  
        $single_sche_info = $this->rent_schedule_payment_model->get_single_row_info('rent_ind_schedule',$single_paid_data['checked_schedule_ids']);  
        $non_prov_paid_data=$this->rent_schedule_payment_model->get_non_prov_paid_data($single_paid_data['agreement_id'], $single_paid_data['checked_schedule_ids']);
		$others_no_tax_amount = $this->rent_schedule_payment_model->get_others_no_tax_amount($single_paid_data['agreement_id']);
		$vat_percentage = $this->rent_security_deposit_model->get_parameter_data_single('ref_rent_vat','id',''); 
		$tax_percentage = $this->rent_security_deposit_model->get_parameter_data_single('ref_rent_tax','id',''); 
		
		$rent_gl = $this->rent_security_deposit_model->get_parameter_data_where_in('ref_rent_single_gl','name',4);
		$advance_gl =$this->rent_security_deposit_model->get_parameter_data_where_in('ref_rent_single_gl','name',1);
		$provision_gl =$this->rent_security_deposit_model->get_parameter_data_where_in('ref_rent_single_gl','name',3);
		$vat_gl =$this->rent_security_deposit_model->get_parameter_data_where_in('ref_rent_single_gl','name',5);
		$tax_gl =$this->rent_security_deposit_model->get_parameter_data_where_in('ref_rent_single_gl','name',6); 

		//code start
		$loc_narr_str=''; 
	    if($others_type_names->others_loc_names==''){
	        $loc_narr_str=$others_type_names->loc_names;     
	    }else{
	        $loc_narr_str=$others_type_names->loc_names .','.$others_type_names->others_loc_names;
	    }

		if( $result_agree_info->total_square_ft < $vat_percentage->minm_sft ){ $vat_applicable = 'no'; }else{ $vat_applicable = 'yes';} 
		$others_string='';
		$tax_wived = $result_agree_info->tax_wived;
		$loc_names = $others_type_names->loc_names;
		$others_loc_names = $others_type_names->others_loc_names;
		$vat_percent = $vat_percentage->vat_percentage;

		$rent_paid_history_id = $paid_id;
		$sche_payment_sts = $single_paid_data['sche_payment_sts'];
		$vat_applicable = $vat_applicable;
		$total_vat = 0;

		$ledger_type='';
		$ledger_type_info='';
		if($sche_payment_sts=='stop_cost_center'){ $ledger_type_info='Rent Stop'; }
		if($sche_payment_sts=='stop_cost_center_pm'){ $ledger_type_info='Rent Stop Accrual Pm'; }
		if($sche_payment_sts=='advance_rent_payment'){ $ledger_type_info='Rent for Rent in Advance'; }
		if($sche_payment_sts=='stop_payment'){ $ledger_type_info='Rent for Rent Stop';  }
		if($sche_payment_sts=='stop_payment_pm'){ $ledger_type_info='Rent for Rent Accrual Pm';  }
		if($sche_payment_sts=='stop_unpaid_payment'){ $ledger_type_info='Rent';  }
		if($sche_payment_sts=='unpaid_payment'){ $ledger_type_info='Rent';  }

		$monthly_rent_with_all_for_stop_cost = $single_paid_data['stop_cost_center_amt'];
    	$vat_for_accrual_pm = $monthly_rent_with_all_for_stop_cost * ($vat_percent / 100);
    	// tax calculation - 30 april 2018
		$tax_slab_rate = $this->agreement_model->get_tax_slab_rate();
		$slab_count= count($tax_slab_rate);
		$monthly_rent_with_others_and_arear = $non_prov_paid_data->tot_monthly_rent + $non_prov_paid_data->tot_others_amount + $non_prov_paid_data->tot_area_amount ;
		for($si=0;$si<$slab_count;$si++){
		    if($monthly_rent_with_others_and_arear >= $tax_slab_rate[$si]->min_amt && $monthly_rent_with_others_and_arear <= $tax_slab_rate[$si]->max_amt){
		        $tax_rate=$tax_slab_rate[$si]->tax_percent;
		    }
		}
		
		$location_type_data_count = count($location_type_data);
		$others_location_type_data_count = count($others_type_data);
		$rent_led_ref = $this->user_model->get_bill_refno_rent('rent_ledger', 'ledger_ref_counter', 'ledger_ref_no', 'transaction_dt', 13, '');
		if($others_location_type_data_count==0 && $others_loc_names!=''){
			$others_string= ' and '.$others_loc_names;
		}
		if($others_location_type_data_count==0 && $others_loc_names==''){
			$others_string= '';
		}
		$location_name =  $result_agree_info->location_name;
		$landlord_names =  $result_agree_info->landlord_names;
		$paid_month = strtotime( $single_sche_info->schedule_strat_dt);
		//$phpdate =  $paid_month;
        $mysqldate = date( 'M Y ', $paid_month );
		$ref_table_ref='SCH-RENT-'.$single_paid_data['paid_dt'];
		$rent_ledger_data = array();

		// ll location dr   //UP
	    if($sche_payment_sts!='stop_payment' && $sche_payment_sts!='stop_payment_pm'){    
			//for($i=1;$i<=$location_type_data_count;$i++){
			foreach ($location_type_data as $location_type_data_row) { 
				
				if($location_type_data_row->code==0){
                    if($location_type_data_row->name=='Godown'){ $account_no= $godown_gl[0]->gl_account_no; $acc_des='Rent Godown ';}
                    else{ $account_no= $rent_gl[0]->gl_account_no;}
                    $acc_des='Rent Office';
                }else{
                    $account_no=$location_type_data_row->code;
                    $acc_des=$location_type_data_row->name;
                }

                $vat_percent = $vat_percentage->vat_percentage;
                $location_type_percent = $location_type_data_row->cost_in_percent;
                $monthly_rent_with_others_and_arear = $non_prov_paid_data->tot_monthly_rent;
                $location_type_dr_amount = $monthly_rent_with_others_and_arear * ($location_type_percent / 100);
                $location_type_vat_amount = $location_type_dr_amount * ($vat_percent / 100);
                $location_type_dr_amount_without_vat = $location_type_dr_amount ;

				  $debit_ledger_data = array(
	                 	'ledger_ref_no' => $rent_led_ref[1] 
	                 	,'ledger_ref_counter' => $rent_led_ref[0] 
						,'ref_table_ref' => $ref_table_ref
	                    ,'section_type' => 'RENT'
	                    , 'ledger_type' => 'Rent'
	                    , 'Ledger_type_info' => $ledger_type_info
	                    , 'ref_id' => $rent_paid_history_id
	                    , 'rent_agre_id' => $single_paid_data['agreement_id']
	                    , 'landlord_id' => ''
	                    , 'payment_type' => 'GL'
	                    , 'cost_center' => $result_agree_info->agree_cost_center
	                    , 'mis_code' => $location_type_data_row->location_mis_id
	                    , 'description' => 'Rent of '.$location_type_data_row->name.' '.$others_string.' at '.$location_name.'  for '.$mysqldate
	                    //, 'description' => 'Rent of '.$location_name.' '.$others_string.' for '.$mysqldate
	                    , 'account_description' => $acc_des
	                    , 'gl_account' => $account_no
	                    , 'db_cr_sts' => 'Debit'
	                    , 'amount' => $location_type_dr_amount_without_vat
	                    , 'transaction_by' => $this->session->userdata['user']['user_id']
	                    , 'transaction_dt' => date('Y-m-d H:i:s')
	                    , 'disburse_dt' => null
	                    , 'journal_ref' => null
	                    , 'sts' => 1
	                    , 'gefo_ref_no' => null
	                );
	               	$rent_ledger_data[] = $debit_ledger_data;  

			}
		}

		// others ll location dr frb 5   //Unpaid payment(UP)
	    if($sche_payment_sts!='stop_payment' && $sche_payment_sts!='stop_payment_pm'){    
			//for($i=1;$i<=$others_location_type_data_count;$i++){
			foreach ($others_type_data as $others_type_data_row) { 

				if($others_type_data_row->account==0){
                 $acc_des='Rent Office';
                 $oth_account_no=$rent_gl[0]->gl_account_no;
	            }else{
	                 $acc_des=$others_type_data_row->name;
	                 $oth_account_no=$others_type_data_row->account;
	            }

				$oth_amt=0;
                $oth_amt=$non_prov_paid_data->tot_others_amount * ($others_type_data_row->others_type_percentage / 100);
                    
                $only_others_rent =$oth_amt;
                $others_location_type_dr_amount = $only_others_rent * ($single_sche_info->rent_fraction_day / 100);
                $location_type_dr_amount_without_vat = $others_location_type_dr_amount ;

				  $debit_ledger_data = array(
	                 	'ledger_ref_no' => $rent_led_ref[1] 
	                 	,'ledger_ref_counter' => $rent_led_ref[0] 
						,'ref_table_ref' => $ref_table_ref
	                    ,'section_type' => 'RENT'
	                    , 'ledger_type' => 'Rent'
	                    , 'Ledger_type_info' => $ledger_type_info
	                    , 'ref_id' => $rent_paid_history_id
	                    , 'rent_agre_id' => $single_paid_data['agreement_id']
	                    , 'landlord_id' => ''
	                    , 'payment_type' => 'GL'
	                    , 'cost_center' => $result_agree_info->agree_cost_center
	                    , 'mis_code' => $others_type_data_row->other_loc_mis_id
	                    , 'description' => 'Rent of '.$others_type_data_row->other_loc_type_id.' at  '.$location_name .'  for '.$mysqldate
	                    , 'account_description' => $acc_des // need change like Loc type-- done
	                    , 'gl_account' => $oth_account_no // need change like Loc type -- done
	                    , 'db_cr_sts' => 'Debit'
	                    , 'amount' => $location_type_dr_amount_without_vat
	                    , 'transaction_by' => $this->session->userdata['user']['user_id']
	                    , 'transaction_dt' => date('Y-m-d H:i:s')
	                    , 'disburse_dt' => null
	                    , 'journal_ref' => null
	                    , 'sts' => 1
	                    , 'gefo_ref_no' => null
	                );
	               	$rent_ledger_data[] = $debit_ledger_data;  

			}
		}

		// arrear debit 3 oct 2018
		if($sche_payment_sts!='stop_payment' && $sche_payment_sts!='stop_payment_pm'){    
				if($non_prov_paid_data->tot_area_amount > 0){
					$narration_1 = " $result_agree_info->arear_remarks for the month of $mysqldate for $result_agree_info->location_name ";
				    $only_arrear =$non_prov_paid_data->tot_area_amount;
                	$arrear_dr_amount = $only_arrear * ($single_sche_info->rent_fraction_day / 100);

				    $debit_ledger_data = array(
	                 	'ledger_ref_no' => $rent_led_ref[1] 
	                 	,'ledger_ref_counter' => $rent_led_ref[0] 
						,'ref_table_ref' => $ref_table_ref
	                    ,'section_type' => 'RENT'
	                    , 'ledger_type' => 'Rent'
	                    , 'Ledger_type_info' => $ledger_type_info
	                    , 'ref_id' => $rent_paid_history_id
	                    , 'rent_agre_id' => $single_paid_data['agreement_id']
	                    , 'landlord_id' => ''
	                    , 'payment_type' => 'GL'
	                    , 'cost_center' => $result_agree_info->agree_cost_center
	                    , 'mis_code' => $location_type_data[0]->location_mis_id
	                    , 'description' => $narration_1
	                    , 'account_description' => 'Rent Office'
	                    , 'gl_account' => $rent_gl[0]->gl_account_no
	                    , 'db_cr_sts' => 'Debit'
	                    , 'amount' => $arrear_dr_amount
	                    , 'transaction_by' => $this->session->userdata['user']['user_id']
	                    , 'transaction_dt' => date('Y-m-d H:i:s')
	                    , 'disburse_dt' => null
	                    , 'journal_ref' => null
	                    , 'sts' => 1
	                    , 'gefo_ref_no' => null
	                );
	               	$rent_ledger_data[] = $debit_ledger_data;  
	        }       	
		}

		// vat debit	//UP
		if($sche_payment_sts!='stop_payment' && $vat_applicable=='yes' && $sche_payment_sts!='stop_payment_pm'){	
			//for($i=1;$i<=$location_type_data_count;$i++){
			foreach ($location_type_data as $location_type_data_row) {

				$mis_code=$location_type_data_row->location_mis_id;
				if($location_type_data_row->code==0){
                    if($location_type_data_row->name=='Godown'){ $account_no= $godown_gl[0]->gl_account_no; $acc_des='Rent Godown ';}
                    else{ $account_no= $rent_gl[0]->gl_account_no;}
                    $acc_des='Rent Office';
                }else{
                    $account_no=$location_type_data_row->code;
                    $acc_des=$location_type_data_row->name;
                }

				$vat_percent = $vat_percentage->vat_percentage;
                $location_type_percent = $location_type_data_row->cost_in_percent;
                $monthly_rent_with_arear = $non_prov_paid_data->tot_monthly_rent +  $non_prov_paid_data->tot_area_amount ;
                $location_type_dr_amount = $monthly_rent_with_arear * ($location_type_percent / 100);
                $location_type_vat_amount = $location_type_dr_amount * ($vat_percent / 100);
                $total_vat = $total_vat +  $location_type_vat_amount;

				if($location_type_vat_amount > 0 ){
						//$acc_des= $this->input->post('location_vat_acc_des'.$i);
						  $vat_debit_ledger_data = array(
			                 	'ledger_ref_no' => $rent_led_ref[1] 
			                 	,'ledger_ref_counter' => $rent_led_ref[0]
								,'ref_table_ref' => $ref_table_ref
			                    ,'section_type' => 'RENT'
			                    , 'ledger_type' => 'VAT'
			                    , 'Ledger_type_info' => $ledger_type_info
			                    , 'ref_id' => $rent_paid_history_id
			                    , 'rent_agre_id' => $single_paid_data['agreement_id']
			                    , 'landlord_id' => ''
			                    , 'payment_type' => 'GL'
			                    , 'cost_center' => $result_agree_info->agree_cost_center
			                    , 'mis_code' => $mis_code
			                   // , 'description' => $this->input->post('location_vat_dr_amount'.$i).' taka Vat Debit for '.$this->input->post('location_vat_loc_name'.$i)
			                    , 'description' => 'Rent of '.$location_type_data_row->name.' '.$others_string.' at '.$location_name.'  for '.$mysqldate.' VAT'
			                    , 'account_description' => $acc_des
			                    , 'gl_account' => $account_no
			                    , 'db_cr_sts' => 'Debit'
			                    , 'amount' => $location_type_vat_amount
			                    , 'transaction_by' => $this->session->userdata['user']['user_id']
			                    , 'transaction_dt' => date('Y-m-d H:i:s')
			                    , 'disburse_dt' => null
			                    , 'journal_ref' => null
			                    , 'sts' => 1
			                    , 'gefo_ref_no' => null
			                );		               
							$rent_ledger_data[] = $vat_debit_ledger_data; 
		        }    
			}

		}

		// others vat debit 5 feb	//UP
		if($sche_payment_sts!='stop_payment' && $vat_applicable=='yes' && $sche_payment_sts!='stop_payment_pm'){	
			//for($i=1;$i<=$others_location_type_data_count;$i++){
			foreach ($others_type_data as $others_type_data_row){
				if($others_type_data_row->account==0){
	                 $acc_des='Rent Office';
	                 $oth_account_no=$rent_gl[0]->gl_account_no;
	            }else{
	                 $acc_des=$others_type_data_row->name;
	                 $oth_account_no=$others_type_data_row->account;
	            }
				$vat_percent = $vat_percentage->vat_percentage;
                $location_type_percent = $location_type_data_row->cost_in_percent;
		        
		        $oth_amt=0;
                $oth_amt=$non_prov_paid_data->tot_others_amount * ($others_type_data_row->others_type_percentage / 100);    
                $only_others_rent =$oth_amt;
                $others_location_type_dr_amount = $only_others_rent * ($single_sche_info->rent_fraction_day / 100);
                $location_type_vat_amount = $others_location_type_dr_amount * ($vat_percent / 100);
                
				if($location_type_vat_amount > 0 && $others_type_data_row->vat_sts=='yes'){
					$total_vat =  $total_vat + $location_type_vat_amount; 

					  $others_vat_debit_ledger_data = array(
		                 	'ledger_ref_no' => $rent_led_ref[1] 
		                 	,'ledger_ref_counter' => $rent_led_ref[0]
							,'ref_table_ref' => $ref_table_ref
		                    ,'section_type' => 'RENT'
		                    , 'ledger_type' => 'VAT'
		                    , 'Ledger_type_info' => $ledger_type_info
		                    , 'ref_id' => $rent_paid_history_id
		                    , 'rent_agre_id' => $single_paid_data['agreement_id']
		                    , 'landlord_id' => ''
		                    , 'payment_type' => 'GL'
		                    , 'cost_center' => $result_agree_info->agree_cost_center
		                    , 'mis_code' => $others_type_data_row->other_loc_mis_id
		                    , 'description' => 'Rent of '.$location_name .' '.$others_type_data_row->other_loc_type_id.' for '.$mysqldate.' VAT'
		                    , 'account_description' => $acc_des	// need change like Loc type--done
		                    , 'gl_account' => $oth_account_no	// need change like Loc type--done
		                    , 'db_cr_sts' => 'Debit'
		                    , 'amount' => $location_type_vat_amount
		                    , 'transaction_by' => $this->session->userdata['user']['user_id']
		                    , 'transaction_dt' => date('Y-m-d H:i:s')
		                    , 'disburse_dt' => null
		                    , 'journal_ref' => null
		                    , 'sts' => 1
		                    , 'gefo_ref_no' => null
		                );		               
						$rent_ledger_data[] = $others_vat_debit_ledger_data; 
		        }    
			}

		}

		// VAT    //UP
		$hidden_vat=$total_vat;
	   	if($sche_payment_sts!='stop_unpaid_payment' && $sche_payment_sts!='stop_payment' && $sche_payment_sts!='stop_cost_center_pm' && $sche_payment_sts!='stop_payment_pm'){
	   		if($sche_payment_sts=='stop_cost_center'){
	   			$acc_name = 'Expense Payable -Rent Office';
	   		
	   			$acc_str= "Rent of $loc_names $others_string at $location_name for $mysqldate VAT";
	   		}else{
	   			$acc_name = 'VAT on Rent';
	   			$vat_percent = $vat_percentage->vat_percentage;
	   			$acc_str= " $vat_percent pc VAT from $landlord_names for rent of $location_name for $mysqldate";
	   		}
	   		if($vat_applicable=='yes' &&  $total_vat > 0 ){	
				  $vat_credit_ledger_data = array(
	                 	'ledger_ref_no' => $rent_led_ref[1] 
	                 	,'ledger_ref_counter' => $rent_led_ref[0]
						,'ref_table_ref' => $ref_table_ref 
	                    ,'section_type' => 'RENT'
	                    , 'ledger_type' => 'VAT'
	                    , 'Ledger_type_info' => $ledger_type_info
	                    , 'ref_id' => $rent_paid_history_id
	                    , 'rent_agre_id' => $single_paid_data['agreement_id']
	                    , 'landlord_id' => ''
	                    , 'payment_type' => 'GL'
	                    , 'cost_center' => $result_agree_info->agree_cost_center
	                    , 'mis_code' => ''
	                    //, 'description' => $this->input->post('vat_percentage').' pc VAT '.$landlord_names.' for '.$mysqldate

	                    , 'description' => $acc_str
	                    , 'account_description' => $acc_name
	                    , 'gl_account' => $vat_gl[0]->gl_account_no
	                    , 'db_cr_sts' => 'Credit'
	                    , 'amount' => $total_vat
	                    , 'transaction_by' => $this->session->userdata['user']['user_id']
	                    , 'transaction_dt' => date('Y-m-d H:i:s')
	                    , 'disburse_dt' => null
	                    , 'journal_ref' => null
	                    , 'sts' => 1
	                    , 'gefo_ref_no' => null
	                );
	                $rent_ledger_data[] = $vat_credit_ledger_data; 
	        	}  
	    }

	    if($sche_payment_sts=='stop_unpaid_payment' || $sche_payment_sts=='stop_payment' || $sche_payment_sts=='stop_payment_pm'){
	  	// stop receive paid dr only for both stop_unpaid_payment and stop_payment

		  	if($sche_payment_sts=='stop_payment_pm'){$ledger_type_str='Rent for Rent Accrual Pm';}else{$ledger_type_str='Rent for Rent Stop';}
		  	if($sche_payment_sts=='stop_payment' || $sche_payment_sts=='stop_payment_pm'){$account_description='Expense Payable -Rent Office';}else{$account_description='Rent Office';}
		  	$narration_1 = " Rent of $loc_narr_str at $result_agree_info->location_name for $mysqldate";
		  	$accrual_amt=0;
            $vat_cr_amount_new=0;
            $monthly_rent_without_others_for_stop_cost = $single_paid_data['stop_cost_center_amt'] - $non_prov_paid_data->tot_others_amount;
            $only_others_for_stop_cost = $non_prov_paid_data->tot_others_amount;
            if($sche_payment_sts=='stop_payment_pm'){
                //$vat_cr_amount_new = $vat_for_accrual_pm ;
                if($vat_applicable=='no'){ $vat_cr_amount_new=0;}
                $accrual_amt = $monthly_rent_without_others_for_stop_cost ;
            }else{
                $accrual_amt=$monthly_rent_without_others_for_stop_cost;
            }

	        $debit_ledger_data = array(
	                 	'ledger_ref_no' => $rent_led_ref[1] 
	                 	,'ledger_ref_counter' => $rent_led_ref[0]
						,'ref_table_ref' => $ref_table_ref 
	                    ,'section_type' => 'RENT'
	                    , 'ledger_type' => 'Rent'
	                    , 'Ledger_type_info' => $ledger_type_str
	                    , 'ref_id' => $rent_paid_history_id
	                    , 'rent_agre_id' => $single_paid_data['agreement_id']
	                    , 'landlord_id' => ''
	                    , 'payment_type' => ''
	                    , 'cost_center' => $result_agree_info->agree_cost_center
	                    , 'mis_code' => null
	                    , 'description' => $narration_1
	                    , 'account_description' => $account_description
	                    , 'gl_account' => $provision_gl[0]->gl_account_no
	                    , 'db_cr_sts' => 'Debit'
	                    , 'amount' => $accrual_amt
	                    , 'transaction_by' => $this->session->userdata['user']['user_id']
	                    , 'transaction_dt' => date('Y-m-d H:i:s')
	                    , 'disburse_dt' => null
	                    , 'journal_ref' => null
	                    , 'sts' => 1
	                    , 'gefo_ref_no' => null
	                );
	                $rent_ledger_data[] = $debit_ledger_data; 

	// others dr 10 oct 2018
	            if($only_others_for_stop_cost > 0 ){    
	                $narration_1 = " Rent of $others_type_names->others_loc_names at $result_agree_info->location_name for $mysqldate";
	                 $debit_ledger_data = array(
	                 	'ledger_ref_no' => $rent_led_ref[1] 
	                 	,'ledger_ref_counter' => $rent_led_ref[0]
						,'ref_table_ref' => $ref_table_ref 
	                    ,'section_type' => 'RENT'
	                    , 'ledger_type' => 'Rent'
	                    , 'Ledger_type_info' => $ledger_type_str
	                    , 'ref_id' => $rent_paid_history_id
	                    , 'rent_agre_id' => $single_paid_data['agreement_id']
	                    , 'landlord_id' => ''
	                    , 'payment_type' => ''
	                    , 'cost_center' => $result_agree_info->agree_cost_center
	                    , 'mis_code' => null
	                    , 'description' => $narration_1 
	                    , 'account_description' => $account_description
	                    , 'gl_account' => $provision_gl[0]->gl_account_no
	                    , 'db_cr_sts' => 'Debit'
	                    , 'amount' => $only_others_for_stop_cost
	                    , 'transaction_by' => $this->session->userdata['user']['user_id']
	                    , 'transaction_dt' => date('Y-m-d H:i:s')
	                    , 'disburse_dt' => null
	                    , 'journal_ref' => null
	                    , 'sts' => 1
	                    , 'gefo_ref_no' => null
	                );
	                $rent_ledger_data[] = $debit_ledger_data; 
	            }    

	// need to add col for stop_payment 

	        if($sche_payment_sts=='stop_payment' || $sche_payment_sts=='stop_payment_pm'){
	        	if($vat_applicable=='yes'){
	       			$account_description='Expense Payable -Rent Office';
	       			$narration_1 = " Rent of $loc_narr_str  at $result_agree_info->location_name for $mysqldate VAT";

	       			$debit_ledger_data = array(
	                 	'ledger_ref_no' => $rent_led_ref[1] 
	                 	,'ledger_ref_counter' => $rent_led_ref[0]
						,'ref_table_ref' => $ref_table_ref 
	                    ,'section_type' => 'RENT'
	                    , 'ledger_type' => 'Rent'
	                    , 'Ledger_type_info' => $ledger_type_str
	                    , 'ref_id' => $rent_paid_history_id
	                    , 'rent_agre_id' => $single_paid_data['agreement_id']
	                    , 'landlord_id' => ''
	                    , 'payment_type' => ''
	                    , 'cost_center' => $result_agree_info->agree_cost_center
	                    , 'mis_code' => null
	                    , 'description' => $narration_1
	                    , 'account_description' => $account_description
	                    , 'gl_account' => $provision_gl[0]->gl_account_no
	                    , 'db_cr_sts' => 'Debit'
	                    , 'amount' => $vat_for_accrual_pm
	                    , 'transaction_by' => $this->session->userdata['user']['user_id']
	                    , 'transaction_dt' => date('Y-m-d H:i:s')
	                    , 'disburse_dt' => null
	                    , 'journal_ref' => null
	                    , 'sts' => 1
	                    , 'gefo_ref_no' => null
	                );
	                $rent_ledger_data[] = $debit_ledger_data; 
	            }    
	       }
	    }

	    if($sche_payment_sts!='stop_cost_center' && $sche_payment_sts!='advance_rent_payment' && $sche_payment_sts!='stop_cost_center_pm'){	
			//$landlords_count = $this->input->post('landlords_count');
			$ledger_type='Rent Payment Credit for Advance';
			if($sche_payment_sts=='stop_unpaid_payment'){ $ledger_type='Advance Receive';  }
			if($sche_payment_sts=='stop_payment_pm'){ $ledger_type='Advance Rent';  }
			if($sche_payment_sts=='stop_payment' || $sche_payment_sts=='unpaid_payment'){ $ledger_type='Advance Receive';  }

			//for($i=1;$i<=$landlords_count;$i++){
			foreach ($landlords_result as $landlord_row) {

				$landlord_percent = $landlord_row->adv_amount_percent;
                $advance_adj_amount = $non_prov_paid_data->tot_adjustment_adv ;
                $landlord_adj_amount = $advance_adj_amount * ($landlord_percent / 100);
		
		   		if($landlord_adj_amount > 0 ){

					  $debit_ledger_data = array(
		                 	'ledger_ref_no' => $rent_led_ref[1] 
		                 	,'ledger_ref_counter' => $rent_led_ref[0] 
							,'ref_table_ref' => $ref_table_ref
		                    ,'section_type' => 'RENT'
		                    , 'ledger_type' => $ledger_type
		                    , 'Ledger_type_info' => $ledger_type_info
		                    , 'ref_id' => $rent_paid_history_id
		                    , 'rent_agre_id' => $single_paid_data['agreement_id']
		                    , 'landlord_id' => ''
							, 'payment_type' => ''
		                    , 'cost_center' => $result_agree_info->agree_cost_center
		                    , 'mis_code' => ''
		                    , 'description' => 'Adjustment of advance rent of '.$result_agree_info->location_name.' for '.$mysqldate
		                    //, 'description' => 'Adjustment of advance rent from Rent of '.$this->input->post('landlord_adv_ll_name'.$i).' for '.$mysqldate
		                    , 'account_description' => 'Advance Rent'
		                    , 'gl_account' => $advance_gl[0]->gl_account_no
		                    , 'db_cr_sts' => 'Credit'
		                    , 'amount' => $landlord_adj_amount
		                    , 'transaction_by' => $this->session->userdata['user']['user_id']
		                    , 'transaction_dt' => date('Y-m-d H:i:s')
		                    , 'disburse_dt' => null
		                    , 'journal_ref' => null
		                    , 'sts' => 1
		                    , 'gefo_ref_no' => null
		                );
		                
						$rent_ledger_data[] = $debit_ledger_data;
					}	
				}
		}

		//SUP SP 
	  	if($sche_payment_sts=='stop_unpaid_payment' || $sche_payment_sts=='stop_payment' || $sche_payment_sts=='stop_payment_pm'){
	  	// stop receive paid dr only for both stop_unpaid_payment and stop_payment

	  	if($sche_payment_sts=='stop_payment_pm'){$ledger_type_str='Rent for Rent Accrual Pm';}else{$ledger_type_str='Rent for Rent Stop';}
	  	if($sche_payment_sts=='stop_payment' || $sche_payment_sts=='stop_payment_pm'){$account_description='Expense Payable -Rent Office';}else{$account_description='Rent Office';}
	  	
	  	// location_type wise vat credit only for stop_unpaid_payment
	  		if($vat_applicable=='no'){ $vat_dr_amount=0;  $total_vat=0;}
	        if($sche_payment_sts=='stop_unpaid_payment' && $vat_applicable=='yes'){    

			  		//for($i=1;$i<=$location_type_data_count;$i++){
	         		foreach ($location_type_data as $location_type_data_row) {
			  			$location_name = $location_type_data_row->name;
			  			$location_type_percent = $location_type_data_row->cost_in_percent;
                        $vat_dr_amount = $total_vat * ($location_type_percent / 100);
                        $acc_desc = " $vat_percentage->vat_percentage pc VAT $landlord_names  for $mysqldate ";
			  			$vat_credit_ledger_data = array(
			                 	'ledger_ref_no' => $rent_led_ref[1] 
			                 	,'ledger_ref_counter' => $rent_led_ref[0] 
								,'ref_table_ref' => $ref_table_ref
			                    ,'section_type' => 'RENT'
			                    , 'ledger_type' => 'VAT'
			                    , 'Ledger_type_info' => $ledger_type_info
			                    , 'ref_id' => $rent_paid_history_id
			                    , 'rent_agre_id' => $single_paid_data['agreement_id']
			                    , 'landlord_id' => ''
			                    , 'payment_type' => 'GL'
			                    , 'cost_center' => $result_agree_info->agree_cost_center
			                    , 'mis_code' => ''
			                    , 'description' => $acc_desc
			                    , 'account_description' => 'VAT on Rent'
			                    , 'gl_account' => $vat_gl[0]->gl_account_no
			                    , 'db_cr_sts' => 'Credit'
			                    , 'amount' => $vat_dr_amount
			                    , 'transaction_by' => $this->session->userdata['user']['user_id']
			                    , 'transaction_dt' => date('Y-m-d H:i:s')
			                    , 'disburse_dt' => null
			                    , 'journal_ref' => null
			                    , 'sts' => 1
			                    , 'gefo_ref_no' => null
			                );
			                $rent_ledger_data[] = $vat_credit_ledger_data;         
					}
				}   	
		} 

		if($sche_payment_sts=='stop_payment_pm' || $sche_payment_sts=='stop_payment'){
			$vat_cr_amount = $vat_for_accrual_pm ;
			$narration_1 = " $vat_percent % VAT from Rent of $loc_narr_str  at $result_agree_info->location_name for $mysqldate";
			if($vat_cr_amount > 0 && $vat_applicable=='yes'){
				$vat_credit_ledger_data = array(
		                 	'ledger_ref_no' => $rent_led_ref[1] 
		                 	,'ledger_ref_counter' => $rent_led_ref[0] 
							,'ref_table_ref' => $ref_table_ref
		                    ,'section_type' => 'RENT'
		                    , 'ledger_type' => 'VAT'
		                    , 'Ledger_type_info' => $ledger_type_info
		                    , 'ref_id' => $rent_paid_history_id
		                    , 'rent_agre_id' => $single_paid_data['agreement_id']
		                    , 'landlord_id' => ''
		                    , 'payment_type' => 'GL'
		                    , 'cost_center' => $result_agree_info->agree_cost_center
		                    , 'mis_code' => ''
		                    , 'description' => $narration_1
		                    , 'account_description' => 'VAT on Rent'
		                    , 'gl_account' => $vat_gl[0]->gl_account_no
		                    , 'db_cr_sts' => 'Credit'
		                    , 'amount' => $vat_cr_amount
		                    , 'transaction_by' => $this->session->userdata['user']['user_id']
		                    , 'transaction_dt' => date('Y-m-d H:i:s')
		                    , 'disburse_dt' => null
		                    , 'journal_ref' => null
		                    , 'sts' => 1
		                    , 'gefo_ref_no' => null
		                );
		        $rent_ledger_data[] = $vat_credit_ledger_data; 
			}            
		}

		//  tax credit   // UP
		if($sche_payment_sts!='stop_cost_center' && $sche_payment_sts!='advance_rent_payment' && $sche_payment_sts!='stop_cost_center_pm' && $tax_wived!='wived_yes'){	
			foreach($landlords_result as $landlord_row) {
				if($sche_payment_sts=='unpaid_payment'){
					$landlord_percent = $landlord_row->credit_amount_percent;
                    $adv_landlord_percent = $landlord_row->adv_amount_percent;
                    $advance_adj_amount = $non_prov_paid_data->tot_adjustment_adv;
                    $monthly_rent_with_others_and_arear = $non_prov_paid_data->tot_monthly_rent + $non_prov_paid_data->tot_others_amount  + $non_prov_paid_data->tot_area_amount ;
                    $tax_applicable_amt= $monthly_rent_with_others_and_arear - $others_no_tax_amount->tax_not_apply;
                    $landlord_adj_amount = $tax_applicable_amt * ($landlord_percent / 100);
                    
                    $landlord_tax_amount = $landlord_adj_amount * ($tax_rate / 100);

                    $landlord_adj_amount_adv = $advance_adj_amount * ($adv_landlord_percent / 100);
                    $landlord_tax_amount_for_adv = $landlord_adj_amount_adv * ($tax_rate / 100);
				}else{
					$landlord_percent = $landlord_row->credit_amount_percent;
                    $adv_landlord_percent = $landlord_row->adv_amount_percent;
                    $advance_adj_amount = $non_prov_paid_data->tot_adjustment_adv;
                    $monthly_rent_with_others_and_arear = $non_prov_paid_data->tot_monthly_rent + $non_prov_paid_data->tot_others_amount  + $non_prov_paid_data->tot_area_amount ;
               
                    $tax_applicable_amt= $monthly_rent_with_others_and_arear - $others_no_tax_amount->tax_not_apply;
                    $landlord_adj_amount = $tax_applicable_amt * ($landlord_percent / 100);
                    $landlord_tax_amount = $landlord_adj_amount * ($tax_rate / 100);

                    $landlord_adj_amount_adv = $advance_adj_amount * ($landlord_percent / 100);
                    $landlord_tax_amount_for_adv = $landlord_adj_amount_adv * ($tax_rate / 100);
				}
					

					//$tax_rate= number_format($this->input->post('landlord_tax_amount_percent'.$i),2);
				    $debit_ledger_data = array(
	                 	'ledger_ref_no' => $rent_led_ref[1] 
	                 	,'ledger_ref_counter' => $rent_led_ref[0] 
						,'ref_table_ref' => $ref_table_ref
	                    ,'section_type' => 'RENT'
	                    , 'ledger_type' => 'TAX'
	                    , 'Ledger_type_info' => $ledger_type_info
	                    , 'ref_id' => $rent_paid_history_id
	                    , 'rent_agre_id' => $single_paid_data['agreement_id']
	                    , 'landlord_id' => $landlord_row->vendor_id
						, 'payment_type' => 'GL'
	                    , 'cost_center' => $location_type_data[0]->cost_center_code
	                    , 'mis_code' => ''
	                    , 'description' => $tax_rate.' pc Tax from'.$landlord_row->name.' from rent of '.$loc_names.' '.$others_string.' at '.$location_name.'  for '.$mysqldate
	                    					//5.00 pc Tax from Delux fashion Ltd. from rent of ATM Bayzid Bostami Ctg for Aug 2018 	
	                    , 'account_description' => 'Tax-Rent office'
	                    , 'gl_account' => $tax_gl[0]->gl_account_no
	                    , 'db_cr_sts' => 'Credit'
	                    , 'amount' => $landlord_tax_amount
	                    , 'transaction_by' => $this->session->userdata['user']['user_id']
	                    , 'transaction_dt' => date('Y-m-d H:i:s')
	                    , 'disburse_dt' => null
	                    , 'journal_ref' => null
	                    , 'sts' => 1
	                    , 'gefo_ref_no' => null
	                );
	                $rent_ledger_data[] = $debit_ledger_data; 

			}
		}

		// bank account sd credit   // UP
	    if($sche_payment_sts!='stop_cost_center' && $sche_payment_sts!='advance_rent_payment' && $sche_payment_sts!='stop_cost_center_pm'){	
	   		
	   		if($single_paid_data['sd_adjust_amt'] > 0 ){
				  $sd_credit_ledger_data = array(
	                 	'ledger_ref_no' => $rent_led_ref[1] 
	                 	,'ledger_ref_counter' => $rent_led_ref[0] 
						,'ref_table_ref' => $ref_table_ref
	                    ,'section_type' => 'RENT'
	                    , 'ledger_type' => 'SD Receive'
	                    , 'Ledger_type_info' => $ledger_type_info
	                    , 'ref_id' => $rent_paid_history_id
	                    , 'rent_agre_id' => $single_paid_data['agreement_id']
	                    , 'landlord_id' => ''
	                    , 'payment_type' => 'GL'
	                    , 'cost_center' => $location_type_data[0]->cost_center_code
	                    , 'mis_code' => ''
	                    //, 'description' => $this->input->post('bank_sd_cr_amount').' taka Credit for Bank'
	                    , 'description' => 'Adjustment of Security Deposit '.$location_name.' for '.$mysqldate
	                    , 'account_description' => 'Advance Rent'
	                    , 'gl_account' => $sd_gl[0]->gl_account_no
	                    , 'db_cr_sts' => 'Credit'
	                    , 'amount' => $single_paid_data['sd_adjust_amt']
	                    , 'transaction_by' => $this->session->userdata['user']['user_id']
	                    , 'transaction_dt' => date('Y-m-d H:i:s')
	                    , 'disburse_dt' => null
	                    , 'journal_ref' => null
	                    , 'sts' => 1
	                    , 'gefo_ref_no' => null
	                );
				$rent_ledger_data[] = $sd_credit_ledger_data; 

			}
		}

		// ll credit amt final // UP // credit_account in 19 march 2019
		$credit_account = $result_agree_info->credit_account;
		if($credit_account=='landlord'){	
			
			$acc_des='';
			if($sche_payment_sts!='advance_rent_payment' && $sche_payment_sts!='stop_cost_center' && $sche_payment_sts!='stop_cost_center_pm'){ 
				//for($i=1;$i<=$landlords_count;$i++){
				foreach ($landlords_result as $landlord_row) {
						$ll_name= $landlord_row->ll_name;
						$landlord= $landlord_row->name;
					   
					    
						   	if($landlord_row->landlord_payment_mode=='Pay Order'){
						   		$description = "PO favouring  $ll_name ";
						   		$acc_des = '- Payorder Suspense A/c ';
						   	}else{
						   		$description = "Credited Rent of $location_name for $mysqldate with $loc_names, $others_loc_names";
						   	}
						   	// checked for both unpaid_payment and lower part
					   			$landlord_percent = $landlord_row->credit_amount_percent;
					            $location_type_dr_amount = $final_cal_amount * ($landlord_percent / 100);

					            $monthly_rent_with_others_and_arear = $non_prov_paid_data->tot_monthly_rent + $non_prov_paid_data->tot_others_amount + $non_prov_paid_data->tot_area_amount ;
					            $adv_landlord_percent = $landlord_row->adv_amount_percent;
					            $advance_adj_amount = $non_prov_paid_data->tot_adjustment_adv;
					            $tax_applicable_amt= $monthly_rent_with_others_and_arear - $others_no_tax_amount->tax_not_apply;
					            $tax_amt = $tax_applicable_amt * ($landlord_percent / 100);
					            $landlord_rent_amt = $monthly_rent_with_others_and_arear * ($landlord_percent / 100);
					            $landlord_adj_amount = $advance_adj_amount * ($adv_landlord_percent / 100);
					            
					            $landlord_tax_amount_for_adv = $landlord_adj_amount * ($tax_rate / 100);
					            $landlord_tax_amount = $tax_amt * ($tax_rate / 100);
					            
					            //$tax_cal = $landlord_tax_amount - $landlord_tax_amount_for_adv;
					            $tax_cal = $landlord_tax_amount ;

					            //sd
					            $total_sd_adjust_amt = $single_paid_data['sd_adjust_amt'] - $prov_sd; 
					            $landlord_sd_adjust_amt = $total_sd_adjust_amt * ($landlord_percent / 100);

					            $final_amount = $landlord_rent_amt - $landlord_adj_amount - $tax_cal - $landlord_sd_adjust_amt; 
					           
					            if($result_agree_info->tax_wived=='wived_yes')
					            { $final_amount = $landlord_rent_amt - $landlord_adj_amount - $landlord_sd_adjust_amt; }

					            $ll_final_amount =  $final_amount;
					   		
					   
								  $credit_ledger_data_final = array(
										'ledger_ref_no' => $rent_led_ref[1] 
										,'ledger_ref_counter' => $rent_led_ref[0] 
										,'ref_table_ref' => $ref_table_ref
										,'section_type' => 'RENT'
										, 'ledger_type' => 'Rent'
										, 'Ledger_type_info' => $ledger_type_info
										, 'ref_id' => $rent_paid_history_id
										, 'rent_agre_id' => $single_paid_data['agreement_id']
										, 'landlord_id' => $landlord_row->vendor_id
										, 'payment_type' => $landlord_row->landlord_payment_mode
										, 'cost_center' => $landlord_row->branch_code
										, 'mis_code' => ''
										, 'description' => $description
										, 'account_description' => $landlord_row->name.' '.$acc_des
										, 'gl_account' => $landlord_row->landlord_acc_no
										, 'db_cr_sts' => 'Credit'
										, 'amount' => $ll_final_amount
										, 'transaction_by' => $this->session->userdata['user']['user_id']
										, 'transaction_dt' => date('Y-m-d H:i:s')
										, 'disburse_dt' => null
										, 'journal_ref' => null
										, 'sts' => 1
										, 'gefo_ref_no' => null
									);
								$rent_ledger_data[] = $credit_ledger_data_final;	
			
					}
			}
		}

		if($credit_account!='landlord'){	
			$i=1;
			$acc_des='';
			if($result_agree_info->credit_account=='advance_gl'){
		        $cr_gl=$advance_gl[0]->gl_account_no;
		        $cr_ac_name ='Advance GL';
		    }elseif ($result_agree_info->credit_account=='provision_gl') {
		        $cr_gl=$provision_gl[0]->gl_account_no;
		        $cr_ac_name ='Provision GL';
		    }else{
		       $cr_gl=$rent_gl[0]->gl_account_no;
		       $cr_ac_name ='Expense GL';
		    }
			if($sche_payment_sts!='advance_rent_payment' && $sche_payment_sts!='stop_cost_center' && $sche_payment_sts!='stop_cost_center_pm'){ 
					
						$ll_name= $landlord_row->ll_name;
						$landlord= $landlord_row->name;
					   
					    $description = "Credited Rent of $location_name for $mysqldate with $loc_names, $others_loc_names";
						
						$location_type_dr_amount = $final_cal_amount ;
			            $monthly_rent_with_others_and_arear = $non_prov_paid_data->tot_monthly_rent + $non_prov_paid_data->tot_others_amount + $non_prov_paid_data->tot_area_amount ;
			            
			            $advance_adj_amount = $non_prov_paid_data->tot_adjustment_adv;
			            $tax_applicable_amt= $monthly_rent_with_others_and_arear - $others_no_tax_amount->tax_not_apply;
			            $tax_amt = $tax_applicable_amt ;
			            $landlord_rent_amt = $monthly_rent_with_others_and_arear ;
			            $landlord_adj_amount = $advance_adj_amount ;
			            
			            $landlord_tax_amount_for_adv = $landlord_adj_amount * ($tax_rate / 100);
			            $landlord_tax_amount = $tax_amt * ($tax_rate / 100);
			            
			            //$tax_cal = $landlord_tax_amount - $landlord_tax_amount_for_adv;
			            $tax_cal = $landlord_tax_amount ;

			            //sd
			            $total_sd_adjust_amt = $single_paid_data['sd_adjust_amt'] - $prov_sd;
			            $landlord_sd_adjust_amt = $total_sd_adjust_amt ;

			            $final_amount = $landlord_rent_amt - $landlord_adj_amount - $tax_cal - $landlord_sd_adjust_amt; 


			            if($result_agree_info->tax_wived=='wived_yes')
			            { $final_amount = $landlord_rent_amt - $landlord_adj_amount - $landlord_sd_adjust_amt; }

			            $ll_final_amount =  $final_amount;
						  $credit_ledger_data_final = array(
								'ledger_ref_no' => $rent_led_ref[1] 
								,'ledger_ref_counter' => $rent_led_ref[0] 
								,'ref_table_ref' => $ref_table_ref
								,'section_type' => 'RENT'
								, 'ledger_type' => 'Rent'
								, 'Ledger_type_info' => $ledger_type_info
								, 'ref_id' => $rent_paid_history_id
								, 'rent_agre_id' => $single_paid_data['agreement_id']
								, 'landlord_id' => 0
								, 'payment_type' => 'GL'
								, 'cost_center' => $location_type_data[0]->cost_center_code
								, 'mis_code' => ''
								, 'description' => $description
								, 'account_description' => $cr_ac_name.' '.$acc_des
								, 'gl_account' => $cr_gl
								, 'db_cr_sts' => 'Credit'
								, 'amount' => $ll_final_amount
								, 'transaction_by' => $this->session->userdata['user']['user_id']
								, 'transaction_dt' => date('Y-m-d H:i:s')
								, 'disburse_dt' => null
								, 'journal_ref' => null
								, 'sts' => 1
								, 'gefo_ref_no' => null
							);
						$rent_ledger_data[] = $credit_ledger_data_final;	
			
					
			}
		}
		
		// only for rent in adv in schedule   // ARP
		if($sche_payment_sts=='advance_rent_payment'){ 
		$description = "Credited to Advance rent  for $mysqldate"; 
		$monthly_rent_with_others_and_arear = $non_prov_paid_data->tot_monthly_rent + $non_prov_paid_data->tot_others_amount + $non_prov_paid_data->tot_area_amount ;
		$final_amount = $monthly_rent_with_others_and_arear;
		$rent_in_adv_credit_ledger_data = array(
                 	'ledger_ref_no' => $rent_led_ref[1] 
                 	,'ledger_ref_counter' => $rent_led_ref[0]
					,'ref_table_ref' => $ref_table_ref
                    ,'section_type' => 'RENT'
                    , 'ledger_type' => 'Advance Receive'
                    , 'Ledger_type_info' => $ledger_type_info
                    , 'ref_id' => $rent_paid_history_id
                    , 'rent_agre_id' => $single_paid_data['agreement_id']
                    , 'landlord_id' => null
					, 'payment_type' => 'GL'
                    , 'cost_center' => $advance_gl[0]->gl_branch_code
                    , 'mis_code' => ''
                    , 'description' => $description
                    , 'account_description' => 'Advance Gl'
                    , 'gl_account' => $advance_gl[0]->gl_account_no
                    , 'db_cr_sts' => 'Credit'
                    , 'amount' => $final_amount
                    , 'transaction_by' => $this->session->userdata['user']['user_id']
                    , 'transaction_dt' => date('Y-m-d H:i:s')
                    , 'disburse_dt' => null
                    , 'journal_ref' => null 
                    , 'sts' => 1
                    , 'gefo_ref_no' => null
                );
                $rent_ledger_data[] = $rent_in_adv_credit_ledger_data;	
	    }

	    // only for rent in adv in schedule
	    $scc_adv_amount=0;
		if($sche_payment_sts=='stop_cost_center' || $sche_payment_sts=='stop_cost_center_pm'){ 

			$description = "Rent of $loc_names $others_string at $location_name for $mysqldate"; 
			$monthly_rent_with_others_and_arear = $non_prov_paid_data->tot_monthly_rent + $non_prov_paid_data->tot_others_amount + $non_prov_paid_data->tot_area_amount ;
			if($sche_payment_sts=='stop_cost_center_pm'){
                $final_amount = $monthly_rent_with_others_and_arear + $hidden_vat; 
            }else{
                $final_amount = $monthly_rent_with_others_and_arear;
            }
            $scc_adv_amount=$final_amount;
			$rent_in_adv_credit_ledger_data = array(
                 	'ledger_ref_no' => $rent_led_ref[1] 
                 	,'ledger_ref_counter' => $rent_led_ref[0] 
					,'ref_table_ref' => $ref_table_ref
                    ,'section_type' => 'RENT'
                    , 'ledger_type' => 'Rent'
                 	
                    , 'Ledger_type_info' => $ledger_type_info
                    , 'ref_id' => $rent_paid_history_id
                    , 'rent_agre_id' => $single_paid_data['agreement_id']
                    , 'landlord_id' => null
					, 'payment_type' => 'GL'
                    , 'cost_center' => $location_type_data[0]->cost_center_code
                    , 'mis_code' => ''
                    , 'description' => $description
                    , 'account_description' => 'Expense Payable -Rent Office'
                    , 'gl_account' => $provision_gl[0]->gl_account_no
                    , 'db_cr_sts' => 'Credit'
                    , 'amount' => $final_amount
                    , 'transaction_by' => $this->session->userdata['user']['user_id']
                    , 'transaction_dt' => date('Y-m-d H:i:s')
                    , 'disburse_dt' => null
                    , 'journal_ref' => null
                    , 'sts' => 1
                    , 'gefo_ref_no' => null
                );
               
				$rent_ledger_data[] = $rent_in_adv_credit_ledger_data;
				$this->db->query("update rent_paid_history set final_amount=$final_amount WHERE id =$rent_paid_history_id ");
	    }
	    $this->db->insert_batch('rent_ledger', $rent_ledger_data); 

	    
		

	    // resume from 1240
	    
	 //    $checked_schedule_sd_id=$single_paid_data['checked_schedule_sd_ids'];
		// $checked_schedule_sd_id_array= explode(",",$checked_schedule_sd_id);

		// $sd_checked_adjustment_amt=$single_paid_data['sd_checked_adjustment_amt'];
		// $sd_checked_adjustment_amt_array= explode(",",$sd_checked_adjustment_amt);
		// foreach($checked_schedule_sd_id_array as  $key => $single_schedule_sd_id){

		//  	$schedule_sd_update_data = array(

		//  		'adjust_amount' => $sd_checked_adjustment_amt_array[$key]
		//  	);
		//  	$this->db->where('id',$single_schedule_sd_id);
		// 	$this->db->update('rent_security_deposit',$schedule_sd_update_data);
		 
		// }
		// $paid_sts= 'paid';
		//  if($sche_payment_sts=='advance_rent_payment'){ $paid_sts= 'paid'; }
		//  if($sche_payment_sts=='stop_cost_center' || $sche_payment_sts=='stop_cost_center_pm'){ $paid_sts= 'stop'; }
 
		//  $checked_schedule_id=$single_paid_data['checked_schedule_ids'];
		//  $sd_adjust_amt =$single_paid_data['sd_adjust_amt'];
		//  $checked_schedule_id_array= explode(",",$checked_schedule_id);
  //        $sche_id_counter = count($checked_schedule_id_array);
                
  //        $sd_amt_per_sche = (float)($sd_adjust_amt/$sche_id_counter);
  //       foreach($checked_schedule_id_array as $single_schedule_id){

		//  	$schedule_update_data = array(

		//  		'paid_sts' => $paid_sts
		//  		,'paid_history_id' => $rent_paid_history_id
		//  		,'adjust_sec_deposit' => $sd_amt_per_sche
		//  		,'sche_add_sts' => 2
		//  	);
		//  	$this->db->where('id',$single_schedule_id);
		// 	$this->db->update('rent_ind_schedule',$schedule_update_data);
		// } 
		// if($sche_payment_sts=='stop_cost_center_pm' || $sche_payment_sts=='stop_payment_pm'){ 

		// 		$paid_update_data = array(
		// 			'stop_cost_center_amt' =>$scc_adv_amount
		// 			,'approve_by' =>$this->session->userdata['user']['user_id']
		// 			,'approve_dt' => date('Y-m-d, H:i:s')
		// 		);
		// }else{

		// 	 	$paid_update_data = array(
				
		// 			'approve_by' =>$this->session->userdata['user']['user_id']
		// 			,'approve_dt' => date('Y-m-d, H:i:s')
				
		// 		);

		// }
		// $this->db->where('id',$rent_paid_history_id);
		// $this->db->update('rent_paid_history',$paid_update_data);
  
	}

	function bulk_fin_verify_action()
	{
            
		$ary=explode(',',$this->input->post('deleteEventId'));
		$check=0;
		for($k=0; $k<count($ary); $k++)
		{	
			$rent_paid_history_id = $ary[$k];
			$single_paid_data=$this->rent_schedule_payment_model->get_single_paid_data($rent_paid_history_id);
			if($this->input->post('stype')=='Verification'){
				if($single_paid_data->fin_v_by!=0){
					return $single_paid_data->rent_agree_ref.' Already Verified ' ;
				}
				$paid_update_data = array(
					
					'fin_v_by' =>$this->session->userdata['user']['user_id']
					,'fin_v_dt' => date('Y-m-d, H:i:s')
					
				);
				
				$this->db->where('id',$ary[$k]);
				$this->db->update('rent_paid_history',$paid_update_data);
			}
// 19 may
			if($this->input->post('stype')=='Approval'){  
				//$non_prov_paid_data=$this->rent_schedule_payment_model->get_non_prov_paid_data($single_paid_data->agreement_id, $single_paid_data->checked_schedule_ids);
				if($single_paid_data->fin_v_by==0){
					return $single_paid_data->rent_agree_ref.' not Finance Verified !' ;
				}
				if($single_paid_data->approve_by!=0){
					return $single_paid_data->rent_agree_ref.' Already Approved ' ;
				}
// 21 may				
				$rent_paid_data = array(

						'paid_dt' => $single_paid_data->paid_dt
						,'agreement_id' => $single_paid_data->agreement_id
						,'rent_agree_ref' => $single_paid_data->rent_agree_ref
						,'fin_ref_no' => $single_paid_data->fin_ref_no
						,'rent_amount' => $single_paid_data->rent_amount
						,'monthly_amount' => $single_paid_data->monthly_amount
						,'adv_adjustment_amt' => $single_paid_data->adv_adjustment_amt
						//,'sd_adjust_amt' => $sd_amount_arr_val
						,'sd_adjust_amt' => $single_paid_data->sd_adjust_amt
						,'arear_adjust_amount' => $single_paid_data->arear_adjust_amount
						,'tax_amount' => $single_paid_data->tax_amount

						,'total_others_amount' =>$single_paid_data->total_others_amount
						,'sche_payment_sts' =>$single_paid_data->sche_payment_sts
						,'stop_cost_center_amt' =>$single_paid_data->stop_cost_center_amt
						,'checked_schedule_ids' =>$single_paid_data->checked_schedule_ids
						//,'checked_schedule_sd_ids' =>$checked_schedule_sd_ids_val // may not be like that
						
						,'checked_schedule_sd_ids' =>$single_paid_data->checked_schedule_sd_ids
						,'checked_sd_id_serial' =>$single_paid_data->checked_sd_id_serial
						,'sd_checked_adjustment_amt' =>$single_paid_data->sd_checked_adjustment_amt
						,'sd_ids_hash' => $single_paid_data->sd_ids_hash
						,'checked_sd_amount' => $single_paid_data->checked_sd_amount
						,'test' => $single_paid_data->test
					
						,'sts' => 1
						,'e_by' => $single_paid_data->e_by
						,'e_dt' => $single_paid_data->e_dt
					
					);
				$this->rent_schedule_payment_model->approve_data_insert($rent_paid_data,$rent_paid_history_id);
// 21 may
				$checked_schedule_sd_id=$single_paid_data->checked_schedule_sd_ids;
				$checked_schedule_sd_id_array= explode(",",$checked_schedule_sd_id);

				$sd_checked_adjustment_amt=$single_paid_data->sd_checked_adjustment_amt;
				$sd_checked_adjustment_amt_array= explode(",",$sd_checked_adjustment_amt);

				 foreach($checked_schedule_sd_id_array as  $key => $single_schedule_sd_id){

				 	$schedule_sd_update_data = array(

				 		'adjust_amount' => $sd_checked_adjustment_amt_array[$key]
				 	);
				 	$this->db->where('id',$single_schedule_sd_id);
					$this->db->update('rent_security_deposit',$schedule_sd_update_data);
				 
				 }

				$paid_sts= 'paid';
				$sche_payment_sts = $single_paid_data->sche_payment_sts;
				if($sche_payment_sts=='advance_rent_payment'){ $paid_sts= 'paid'; }
				if($sche_payment_sts=='stop_cost_center' || $sche_payment_sts=='stop_cost_center_pm'){ $paid_sts= 'stop'; }
		 
				$checked_schedule_id=$single_paid_data->checked_schedule_ids;
				$sd_adjust_amt =$single_paid_data->sd_adjust_amt;
				$checked_schedule_id_array= explode(",",$checked_schedule_id);
		        $sche_id_counter = count($checked_schedule_id_array);
		                
		        $sd_amt_per_sche = (float)($sd_adjust_amt/$sche_id_counter);

				foreach($checked_schedule_id_array as $single_schedule_id){

				 	$schedule_update_data = array(

				 		'paid_sts' => $paid_sts
				 		,'paid_history_id' => $rent_paid_history_id
				 		,'adjust_sec_deposit' => $sd_amt_per_sche
				 		,'sche_add_sts' => 2
				 	);
				 	$this->db->where('id',$single_schedule_id);
					$this->db->update('rent_ind_schedule',$schedule_update_data);
				 
				}

				//if($sche_payment_sts=='stop_cost_center_pm' || $sche_payment_sts=='stop_payment_pm'){
				if($sche_payment_sts=='stop_cost_center_pm'){

                    $final_amount = $single_paid_data->final_amount;
                    
					$paid_update_data = array(
						'stop_cost_center_amt' =>$final_amount
						,'approve_by' =>$this->session->userdata['user']['user_id']
						,'approve_dt' => date('Y-m-d, H:i:s')
						
					);
				 }else{

				 	$paid_update_data = array(
					
						'approve_by' =>$this->session->userdata['user']['user_id']
						,'approve_dt' => date('Y-m-d, H:i:s')
					
					);
				 }
				
				$this->db->where('id',$rent_paid_history_id);
				$this->db->update('rent_paid_history',$paid_update_data);

				// 20 may 2019
				// $ledger_update_data = array(
				// 	'pay_approve_sts' =>1
				// );
				// $this->db->where('ref_id',$rent_paid_history_id);
				// $this->db->update('rent_ledger',$ledger_update_data);
			}
			
		}	
		return $check;
	}

	function reset_action()
	{
		$check=0;
		$paid_id=$this->input->post('id');
                $check = 1;

                $sql = "update rent_paid_history
                		set
                		
                		fin_v_by = NULL,
                		fin_v_dt = NULL,
                		approve_by = NULL,
                		approve_dt = NULL,
                		journal_add_sts=0,
                		journal_verify_sts=0

                		where id=$paid_id";
                		
                $this->db->query($sql);

                $sql = "update rent_ledger
                		set
                		sts=0
                		where ref_id=$paid_id and ledger_sts=0";
                $this->db->query($sql);		

		return $check;
	}
}
?>