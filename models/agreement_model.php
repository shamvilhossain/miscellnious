<?php
error_reporting(0);
class agreement_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function get_grid_data($filterscount, $sortdatafield, $sortorder, $limit, $offset) {
        $i = 0;

        if (isset($filterscount) && $filterscount > 0) {
            $where = "( ";

            $tmpdatafield = "";
            $tmpfilteroperator = "";
            for ($i = 0; $i < $filterscount; $i++) {//$where2.="(".$this->input->get('filterdatafield'.$i)." like '%".$this->input->get('filtervalue'.$i)."%')";
                // get the filter's value.
                $filtervalue = str_replace('"', '\"', str_replace("'", "\'", $this->input->get('filtervalue' . $i)));
                // get the filter's condition.
                $filtercondition = $this->input->get('filtercondition' . $i);
                // get the filter's column.
                $filterdatafield = $this->input->get('filterdatafield' . $i);
                // get the filter's operator.
                $filteroperator = $this->input->get('filteroperator' . $i);

                if($filterdatafield=='agreement_ref_no')
                {
                	$filterdatafield='agreement_ref_no';
                }else if($filterdatafield=='fin_ref_no')
                {
                    $filterdatafield='fin_ref_no';
                }else if($filterdatafield=='landlord_names')
                {
                	$filterdatafield='landlord_names';
                }
                else if($filterdatafield=='point_of_payment')
                {
                    $filterdatafield='point_of_payment';
                }
                else if($filterdatafield=='rent_start_dt')
                {
                    $filtervalue=$this->dtf($filtervalue,'-');
                }
                else if($filterdatafield=='agree_exp_dt')
                {
                    $filtervalue=$this->dtf($filtervalue,'-');
                }

                else if($filterdatafield=='total_advance')
                {
                    $filterdatafield='total_advance';
                }
                else if($filterdatafield=='total_advance_paid')
                {
                    $filterdatafield='total_advance_paid';
                }
                else if($filterdatafield=='monthly_rent')
                {
                    $filterdatafield='monthly_rent';
                } 
                // else if($filterdatafield=='cost_center')
                // {
                //     $filterdatafield='j1.name';
                // }


                if($filterdatafield =='cost_center')
                {
                	$filterdatafield='CONCAT(j1.name,"-",j1.code)';
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
                switch ($filtercondition) {
                    case "CONTAINS":
                        $where .= " " . $filterdatafield . " LIKE '%" . $filtervalue . "%'";
                        break;
                    case "DOES_NOT_CONTAIN":
                        $where .= " " . $filterdatafield . " NOT LIKE '%" . $filtervalue . "%'";
                        break;
                    case "EQUAL":
                        $where .= " " . $filterdatafield . " = '" . $filtervalue . "'";
                        break;
                    case "NOT_EQUAL":
                        $where .= " " . $filterdatafield . " <> '" . $filtervalue . "'";
                        break;
                    case "GREATER_THAN":
                        $where .= " " . $filterdatafield . " > '" . $filtervalue . "'";
                        break;
                    case "LESS_THAN":
                        $where .= " " . $filterdatafield . " < '" . $filtervalue . "'";
                        break;
                    case "GREATER_THAN_OR_EQUAL":
                        $where .= " " . $filterdatafield . " >= '" . $filtervalue . "'";
                        break;
                    case "LESS_THAN_OR_EQUAL":
                        $where .= " " . $filterdatafield . " <= '" . $filtervalue . "'";
                        break;
                    case "STARTS_WITH":
                        $where .= " " . $filterdatafield . " LIKE '" . $filtervalue . "%'";
                        break;
                    case "ENDS_WITH":
                        $where .= " " . $filterdatafield . " LIKE '%" . $filtervalue . "'";
                        break;
                }

                if ($i == $filterscount - 1) {
                    $where .= ")";
                }

                $tmpfilteroperator = $filteroperator;
                $tmpdatafield = $filterdatafield;
            }
            // build the query.			
        } else {
            $where = "()";
         
        }


        $right_where="";
        $user_group_id= $this->session->userdata['user']['user_work_group_id']; //1 superadmin, 2=Dept Maker, 4=Dept Checker
		$user_dept_id=$this->session->userdata['user']['user_department_id'];  //1 administrator, 9=Finance
		//user_system_admin_sts
        $right_where="";
		if($user_dept_id==9 && $user_group_id!=1){
                $right_where=" AND j0.agree_current_sts_id >= 3 ";
        }
		/*else{
             $right_where=" AND j0.agree_current_sts_id > 2 ";
        }*/

// 13 oct end

        if($where =="()")
            {   $bill_sts_cond="j0.sts =1 and CURRENT_DATE() <= j0.agree_exp_dt ".$right_where;}
            else{
                $bill_sts_cond="j0.sts =1  and CURRENT_DATE() <= j0.agree_exp_dt ".$right_where." AND  ".$where;
            }

        if ($sortorder == '') {
            $sortdatafield = "j0.id";
            $sortorder = "desc";
        }
        

        $this->db->select("SQL_CALC_FOUND_ROWS j0.id, j0.agreement_ref_no,j0.fin_ref_no, DATEDIFF(CURRENT_DATE(),j0.agree_exp_dt) as ddif,
            DATE_FORMAT(j0.rent_start_dt, '%d/%m/%Y') AS rent_start_dt,
            DATE_FORMAT(j0.agree_exp_dt, '%d/%m/%Y') AS agree_exp_dt,
            IF( j0.point_of_payment = 'pm', 'Following Month', 'Current Month' ) AS point_of_payment,
			j0.landlord_names, j0.location_owner,j0.location_name,
			j0.total_advance, j0.total_advance_paid,
			j0.dept_v_by, j0.fin_v_by, j0.stf_by, j0.ack_by, j0.halt_by, j0.rhalt_by, j0.close_by, j0.close_release_by,
			j0.monthly_rent, j0.agree_current_sts_id, j0.agree_pervious_sts_id,j2.name as agr_current_sts,
		
            CONCAT(j1.name,'-',j1.code) AS cost_center
            ", FALSE)
                ->from('rent_agreement as j0')
                ->join('cost_center as j1', 'j0.agree_cost_center=j1.code', 'left')
                ->join('rent_agreement_sts as j2', 'j0.agree_current_sts_id=j2.id', 'left')
              
                //->where("j0.sts =1")
                ->where($bill_sts_cond)
                ->order_by($sortdatafield,$sortorder)
                ->limit($limit, $offset);

        $q = $this->db->get();
        //print_r($this->db->last_query());
        //$q=$this->db->get();
        $query = $this->db->query('SELECT FOUND_ROWS() AS `Count`');
        $objCount = $query->result_array();
        $result["TotalRows"] = $objCount[0]['Count'];
        $result["TotalRows"] = $objCount['0']['Count'];

        if ($q->num_rows() > 0) {
            $result["Rows"] = $q->result();
        } else {
            $result["Rows"] = array();
        }
        return $result;
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

    function duplicate_name3($field, $val, $vendor_id, $id) {
        
        $where = "sts=1 and (upper(" . $field . ")='" . strtoupper($val) . "')";
        $this->db->where($where, NULL, FALSE);
        $this->db->where('id !=', $vendor_id);
        //$this->db->where('vendor_id',$vendor_id);
        $this->db->from('rent_agreement');
        // echo $this->db->last_query();
        // exit();
        $q = $this->db->get();
        return $q->num_rows();
    }

    function duplicate_name($field, $val) {
        $where = "(upper(" . $field . ")='" . strtoupper($val) . "')";
        $this->db->where($where, NULL, FALSE);
        $this->db->from('rent_agreement');
        $q = $this->db->get();
        return $q->num_rows();
    }

    function get_add_action_data_for_action($id) {


        //$sql = "select * from rent_agreement ";
        $sql = "SELECT 
              SQL_CALC_FOUND_ROWS j0.id,
              j0.agreement_ref_no,
              j0.fin_ref_no,
              DATE_FORMAT(j0.rent_start_dt, '%d/%m/%Y') AS rent_start_dt,
              DATE_FORMAT(j0.agree_exp_dt, '%d/%m/%Y') AS agree_exp_dt,
              IF( j0.point_of_payment = 'pm', 'Following Month', 'Current Month' ) AS point_of_payment,
              j0.landlord_names,
              j0.location_name,
              j0.location_owner,
              j0.total_advance,
              j0.total_advance_paid,
              j0.dept_v_by,
              j0.fin_v_by,
              j0.stf_by,
              j0.ack_by,
              j0.halt_by,
              j0.rhalt_by,
              j0.close_by,
              j0.close_release_by,
              j0.monthly_rent,
              j0.agree_current_sts_id,
              j0.agree_pervious_sts_id,
              CONCAT(j1.name,'-',j1.code) AS cost_center,
              j2.name as agr_current_sts 
            FROM
              (`rent_agreement` AS j0) 
              LEFT JOIN `cost_center` AS j1 
                ON `j0`.`agree_cost_center` = `j1`.`code` 
              LEFT JOIN `rent_agreement_sts` AS j2 
                ON `j0`.`agree_current_sts_id` = `j2`.`id`
            WHERE `j0`.`sts` = 1 and `j0`.`id`='" . $id . "' limit 1";
        //$sql .="WHERE id='" . $id . "' limit 1";
        $query = $this->db->query($sql);
        return $query->row();
    }

    function get_add_action_data_new($id) {

        $sql = "select * from rent_agreement ";
        $sql .="WHERE id='" . $id . "' limit 1";
        $query = $this->db->query($sql);
        return $query->row();
    }



    function get_add_action_data($id) {

        $sql = "select * from rent_agreement ";
        $sql .="WHERE id='" .$id. "' limit 1";
        $query = $this->db->query($sql);
        return $query->row();
    }

    function get_sche_unpaid_id_list($id) {

        $sql = "select GROUP_CONCAT(id) AS unpaid_id_list  from rent_ind_schedule ";
        $sql .="WHERE rent_agree_id='".$id."' and paid_sts not in ('paid','advance') and sche_add_sts=0";
        //echo $sql;exit;
        $query = $this->db->query($sql);
        return $query->row();
    }

    function count_paid_schedule($id) {

        $sql = "select count(*) as paid_count  from rent_ind_schedule ";
        $sql .="WHERE rent_agree_id='".$id."' and paid_sts in ('paid','advance') ";
        //echo $sql;exit;
        $query = $this->db->query($sql);
        return $query->row();
    }

    function admin_check_status($id) {

        $r = $this->db->get_where('rent_agreement', array('id' => $id, 'sts' => 1));
        $num = $r->num_rows();
        //echo $num;exit;
        return $num;
    }

    function get_agree_info($id) {
        
        $sql = "SELECT  j0.*
			FROM rent_agreement AS j0 
			";
        $sql .= " WHERE j0.sts=1  AND j0.id='" . $id . "' ";
        $q = $this->db->query($sql);


        return $q->row();
    }

    function rent_file_list() {

        $this->db->query("update  rent_upload_temp set window_close_sts=1 
		WHERE userid ='" . $this->session->userdata['user']['user_id'] . "'
		");


        $query = $this->db->query("SELECT * FROM rent_upload_temp WHERE userid ='" . $this->session->userdata['user']['user_id'] . "'
		AND sessionid='" . $this->session->userdata['user']['sessionId'] . "'
		AND window_close_sts=0"
        );

        return $query->result();
    }



    function ack_halt_action($result) {

        
        $comments = $this->input->post('comments');
        
        $type = $this->input->post('type');

        if ($type == 'stm') {
            //echo $comments;exit;
            $service = array(
                'agree_current_sts_id' => 1
                , 'resend_by' => $this->session->userdata['user']['user_id']
                , 'resend_reason' => $comments
            );
           
            $this->db->update('rent_agreement', $service, array('id' => $this->input->post('id')));

            $operation = array(
                'operation_id' => 11
                , 'operation_ref' => 'rent_agreement'
                , 'operation_ref_id' => $result->id
                , 'operation_by' => $this->session->userdata['user']['user_id']
                , 'operation_dt' => date('Y-m-d, H:i:s')
                , 'operation_ip' => $this->input->ip_address()
                , 'remarks_2' => 'Agreement resent to Maker'
            );
            $this->db->insert('rent_data_operation_history', $operation);
           
            return 1;
        }
        if ($type == 'stop') {
            //echo $comments;exit;
            $service = array(
                'agree_current_sts_id' => 6
                , 'halt_by' => $this->session->userdata['user']['user_id']
                , 'halt_dt' => date('Y-m-d, H:i:s')
                , 'halt_reason' => $comments
            );
            //$this->db->where('memo_ref_no',$this->input->post('id'));
            $this->db->update('rent_agreement', $service, array('id' => $this->input->post('id')));

            $operation = array(
                'operation_id' => 4
                , 'operation_ref' => 'rent_agreement'
                , 'operation_ref_id' => $result->id
                , 'operation_by' => $this->session->userdata['user']['user_id']
                , 'operation_dt' => date('Y-m-d, H:i:s')
                , 'operation_ip' => $this->input->ip_address()
                , 'remarks_2' => 'Agreement Stoped'
            );
            $this->db->insert('rent_data_operation_history', $operation);
			
			 $operation = array(               
                 'stop_release' => 'stop',
                 'rent_agree_id' => $result->id,
                 'operation_by' => $this->session->userdata['user']['user_id'],
                 'operation_dt' => date('Y-m-d, H:i:s'),                
                 'reasons' => $comments
            );
            $this->db->insert('rent_stop_release_history', $operation);
           
            return 1;
        }
        if ($type == 'release') {
            // agree_pervious_sts_id=6 added in 2 oct 2018 but removed
            $service = array(
                'agree_current_sts_id' => $result->agree_pervious_sts_id
                //, 'agree_pervious_sts_id' => 6
                , 'rhalt_by' => $this->session->userdata['user']['user_id']
                , 'rhalt_dt' => date('Y-m-d, H:i:s')
            );
           
            $this->db->update('rent_agreement', $service, array('id' => $this->input->post('id')));

            $operation = array(
                'operation_id' => 5
                , 'operation_ref' => 'rent_agreement'
                , 'operation_ref_id' => $result->id
                , 'operation_by' => $this->session->userdata['user']['user_id']
                , 'operation_dt' => date('Y-m-d, H:i:s')
                , 'operation_ip' => $this->input->ip_address()
                , 'remarks_2' => 'Agreement Released'
            );
            $this->db->insert('rent_data_operation_history', $operation);
			
			 $operation = array(               
                 'stop_release' => 'release',
                 'rent_agree_id' => $result->id,
                 'operation_by' => $this->session->userdata['user']['user_id'],
                 'operation_dt' => date('Y-m-d, H:i:s'),                
                 'reasons' => 'Released'
            );
            $this->db->insert('rent_stop_release_history', $operation);
            	
            return 1;
        }
        if ($type == 'ack') {
            $ack = array(
                'agree_current_sts_id' => 4
                , 'agree_pervious_sts_id' => 4
                , 'ack_by' => $this->session->userdata['user']['user_id']
                , 'ack_dt' => date('Y-m-d, H:i:s')
            );
            
            $this->db->update('rent_agreement', $ack, array('id' => $this->input->post('id')));
            $operation = array(
                'operation_id' => 8
                , 'operation_ref' => 'rent_agreement'
                , 'operation_ref_id' => $result->id
                , 'operation_by' => $this->session->userdata['user']['user_id']
                , 'operation_dt' => date('Y-m-d, H:i:s')
                , 'operation_ip' => $this->input->ip_address()
                , 'remarks_2' => 'Agreement Acknowledge by Finance'
            );
            $this->db->insert('rent_data_operation_history', $operation);

            return 1;
        }
        if ($type == 'close') {
            $close = array(
                'agree_current_sts_id' => 7
                , 'close_by' => $this->session->userdata['user']['user_id']
                , 'close_dt' => date('Y-m-d, H:i:s')
                , 'close_reason' => $comments
                , 'close_release_by' => null
                , 'close_release_dt' => null
            );
            
            $this->db->update('rent_agreement', $close, array('id' => $this->input->post('id')));

            $operation = array(
                'operation_id' => 10
                , 'operation_ref' => 'rent_agreement'
                , 'operation_ref_id' => $result->id
                , 'operation_by' => $this->session->userdata['user']['user_id']
                , 'operation_dt' => date('Y-m-d, H:i:s')
                , 'operation_ip' => $this->input->ip_address()
                , 'remarks_2' => 'Agreement Closed'
            );
            $this->db->insert('rent_data_operation_history', $operation);

            $dx = array('paid_sts' => 'closed');
            $this->db->where('rent_agree_id = '.$this->input->post('id').' AND paid_sts = "unpaid" ')
                     ->update('rent_ind_schedule', $dx);

            return 1;
        }if ($type == 'unclose') {
            $unclose = array(
                'agree_current_sts_id' => $result->agree_pervious_sts_id
                , 'close_by' => null
                , 'close_dt' => null
                , 'close_release_by' => $this->session->userdata['user']['user_id']
                , 'close_release_dt' => date('Y-m-d, H:i:s')
            );            
            $this->db->update('rent_agreement', $unclose, array('id' => $this->input->post('id')));

            $operation = array(
                'operation_id' => 12
                , 'operation_ref' => 'rent_agreement'
                , 'operation_ref_id' => $result->id
                , 'operation_by' => $this->session->userdata['user']['user_id']
                , 'operation_dt' => date('Y-m-d, H:i:s')
                , 'operation_ip' => $this->input->ip_address()
                , 'remarks_2' => 'Agreement Unclosed'
            );
            $this->db->insert('rent_data_operation_history', $operation);

            return 1;
        }
        return 0;
    }

    function get_file_list_from_temp() {
        $query = $this->db->query("SELECT * FROM rent_upload_temp WHERE userid ='" . $this->session->userdata['user']['user_id'] . "'
		AND sessionid='" . $this->session->userdata['user']['sessionId'] . "'
		AND window_close_sts=0"
        );
        return $query->result();
    }

    function ajaxloadfile($custid) {
        $this->db->query("DELETE FROM rent_upload_temp 
		WHERE userid ='" . $this->session->userdata['user']['user_id'] . "'
		AND sessionid !='" . $this->session->userdata['user']['sessionId'] . "'");

        $query = $this->db->query("SELECT * FROM rent_upload_temp WHERE userid ='" . $this->session->userdata['user']['user_id'] . "'
		AND sessionid='" . $this->session->userdata['user']['sessionId'] . "'
		AND doc_type_id='" . $custid . "' AND window_close_sts=0"
        );
        return $query->result();
    }

    function ajaxloadfile_existing_edit($custid) {

        $query = $this->db->query("SELECT * FROM rent_agr_doc WHERE rent_agree_id=98
		AND sts=1 ");

        return $query->result();
    }

    function ajaxloadfile_existing_edit_new($custid, $agree_id) {
        $query = $this->db->query("SELECT * FROM rent_agr_doc WHERE rent_agree_id=$agree_id and doc_type_id=$custid
		AND sts=1 ");
        return $query->result();
    }

    function remove_file_edit($sessionid, $filename, $type) {

        if ($type == 0) { // new file

            $this->db->query("DELETE FROM rent_upload_temp WHERE sessionid ='" . $sessionid . "' 
	   		AND userid ='" . $this->session->userdata['user']['user_id'] . "' AND file_path='" . $filename . "'");
         
            $path = "./uploads/" . $filename;
            unlink($path);
        } else {
            // old file
            $this->db->query("DELETE FROM rent_agr_doc WHERE doc_type_id ='" . $sessionid . "' 
	   		AND e_by ='" . $this->session->userdata['user']['user_id'] . "' AND file_name='" . $filename . "'");
         

            $path = "./uploads/" . $filename;
            unlink($path);
        }
    }

    function remove_file($sessionid, $filename, $type) {
        $this->db->query("DELETE FROM rent_upload_temp WHERE sessionid ='" . $sessionid . "' 
	   		AND userid ='" . $this->session->userdata['user']['user_id'] . "' AND file_path='" . $filename . "'");
        $path = "./uploads/" . $filename;
        unlink($path);
    }


    function upload_file_action($add_edit = NULL, $edit_id = NULL, $single_file = NULL, $rent_agreement_id = NULL) {
        if ($add_edit == "add") {

            $data = array(
                'rent_agree_id' => $rent_agreement_id,
                'doc_type_id' => $single_file->doc_type_id,
                'original_name' => $this->input->post('original_name'),
                'file_name' => $single_file->file_path,
                'sts' => 1,
                'd_sts' => 0,
                'e_by' => $this->session->userdata['user']['user_id'],
                'e_dt' => date('Y-m-d, H:i:s')
            );
            $this->db->insert('rent_agr_doc', $data);
            $insert_idss = $this->db->insert_id();
        } else {
            $data = array(
                'rent_agree_id' => $rent_agreement_id,
                'doc_type_id' => $single_file->doc_type_id,
                'original_name' => $this->input->post('original_name'),
                'file_name' => $single_file->file_path,
                'sts' => 1,
                'd_sts' => 0,
                'e_by' => $this->session->userdata['user']['user_id'],
                'e_dt' => date('Y-m-d, H:i:s')
            );
            $this->db->insert('rent_agr_doc', $data);
            $insert_idss = $this->db->insert_id();
        }
        return $insert_idss;       
    }

    function add_edit_action($add_edit = NULL, $edit_id = NULL, $file_name = NULL, $file_path = NULL, $file_type = NULL) {

        $db_debug = $this->db->db_debug;
        $this->db->db_debug = false; // off display of db error

        if ($this->input->post('rent_start_dt')) {
            $rent_start_dt = date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('rent_start_dt'))));
        }
        if ($this->input->post('agree_exp_dt')) {
            $agree_exp_dt = date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('agree_exp_dt'))));
        }
        $cost_center= $this->input->post('cost_center1');
        
        if ($add_edit == "add" || $add_edit == "extend") {
            $rent_agree_ref = $this->user_model->get_bill_refno_rent('rent_agreement', 'agree_ref_counter', 'agreement_ref_no', 'e_dt', 12, '');
            $fin_agree_ref = $this->user_model->get_bill_refno_rent('rent_agreement', 'fin_ref_counter', 'fin_ref_no', 'e_dt', 20, " and agree_cost_center= $cost_center ",$cost_center);
            //$rent_agree_ref = 'mm';
            // $fin_agree_ref = 'ff';
       
             
            // totalsqft data - 
            $square_ft = 0;
            for ($i = 1; $i <= $this->input->post('counter_location_type'); $i++) {
                $square_ft+=$this->input->post('square_ft' . $i);
            }

            $increment_type = $this->input->post('increment_type');
            if($increment_type ==1 || $increment_type ==4){
                $increment_type_val= 0;
                $increment_start_dt_value='';
            }elseif($increment_type ==2){

                $increment_type_val= $this->input->post('increment_every_yr_value');
                $increment_start_dt_value='';
            }elseif($increment_type ==3){
                $increment_type_val= $this->input->post('one_time_increment_yr_no');
                $increment_start_dt_value='';
            }else{
                $increment_type_val= 0;
                $increment_start_dt_value='';
                if ($this->input->post('increment_start_dt_value')) {
                    $increment_start_dt_value = date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('increment_start_dt_value'))));
                }
            }


            // rent lanlords data		
            $vendor_id = '';
            $rent_landlord_name = '';

            $new_vendor_id='';
            $new_rent_landlord_name = '';
            $n=1;
			if($this->input->post('location_owner')=='rented'){
            for ($i = 1; $i <= $this->input->post('counter_landlord'); $i++) {
                if ($this->input->post('delete_landlord' . $i) != '1') {
                        if ($i != 1) {
                            $vendor_id.=',';   
                        }

                        $vendor_id.=$this->input->post('vendor_id' . $i);

                        // landlord name select
                        $rent_landlord_id = $this->input->post('vendor_id' . $i);
                        $rent_landlord_str = "select name from vendor where vendor_id='" . $rent_landlord_id . "'";
                        $rent_landlord_query = $this->db->query($rent_landlord_str);
                        $rent_landlord_result = $rent_landlord_query->row();
                     
                        if($this->input->post('location_owner')=='rented'){
                            if ($i != 1) {
                                $rent_landlord_name.=',';  
                            }
                            $rent_landlord_name .= $rent_landlord_result->name;
                        }else{
                            
                        }
                        // 8 sep 
                        if($this->input->post('credit_sts'.$i)=='yes'){
                            if ($n != 1) {
                                $new_vendor_id.=',';
                                $new_rent_landlord_name.=',';
                            
                            }
                            $new_vendor_id.=$this->input->post('vendor_id'.$i);
                            $new_rent_landlord_name .= $rent_landlord_result->name;
                            $n++;
                        }
                }
            }
			}
            if($this->input->post('total_advance')==''){$total_advance=0;}else{$total_advance = $this->input->post('total_advance');}

            $data = array(
                'agreement_ref_no' => $rent_agree_ref[1]
                , 'agree_ref_counter' => $rent_agree_ref[0]
                , 'fin_ref_no' => $fin_agree_ref[1]
                , 'fin_ref_counter' => $fin_agree_ref[0]
                , 'location_name' => $this->input->post('location_name')
                , 'total_square_ft' => $this->input->post('total_square_ft')
                , 'location_address' => $this->input->post('location_address')
                , 'location_division' => $this->input->post('location_division')
                , 'rent_start_dt' => $rent_start_dt
                , 'agree_exp_dt' => $agree_exp_dt
                , 'point_of_payment' => $this->input->post('point_of_payment')
                , 'location_owner' => $this->input->post('location_owner')
                , 'agree_cost_center' => $this->input->post('cost_center1')
                , 'total_advance' => $total_advance
                , 'monthly_rent' => $this->input->post('monthly_rent')
                , 'others_rent' => $this->input->post('others_rent')    
                , 'landlord_ids' => $new_vendor_id
                , 'landlord_names' => $new_rent_landlord_name
                , 'tax_wived' => $this->input->post('tax_wived') 
                , 'adjust_adv_type' => $this->input->post('adjust_adv_type')
                , 'increment_type' => $this->input->post('increment_type')
                , 'increment_type_val' => $increment_type_val
                , 'incr_start_date' => $increment_start_dt_value
                , 'e_by' => $this->session->userdata['user']['user_id']
                , 'e_dt' => date('Y-m-d, H:i:s')
                , 'sts' => 1
                , 'agree_current_sts_id' => 1
                , 'agree_pervious_sts_id' => 1
            );
//print_r($data);exit;
            $this->db->insert('rent_agreement', $data);
           
          
            $insert_idss = $this->db->insert_id();

            if($add_edit == "extend"){

                $old_id = $edit_id;
                $update_data = array(
                            
                            'extend_by' => isset($this->session->userdata['user']['user_id']) ? $this->session->userdata['user']['user_id'] : NULL
                            , 'extend_dt' => date('Y-m-d, H:i:s')
                            , 'extend_agree_id' => $insert_idss
                            , 'agree_current_sts_id' => 7
                        );
                        $this->db->where('id', $old_id);
                        $this->db->update('rent_agreement', $update_data);
            }
            

            //print_r($data);exit;
    if ($insert_idss) {


                $others_amount_type = array();
                $others_amount_val = array();
                $cal_others_val = array();
// checking if it is own or rented 
    if($this->input->post('location_owner')=='rented'){
                for ($i = 0; $i <= $this->input->post('count_year'); $i++) {

                    unset($others_amount_type);
                    unset($others_amount_val);
                    unset($cal_others_val);

                    $others_amount_type_commaList = '';
                    for ($j = 0; $j < $this->input->post('element_number'); $j++) {
                        $others_amount_type[] = $this->input->post('others_amount_type' . $i . $j);
                        $others_amount_val[] = $this->input->post('others_amount_val' . $i . $j);
                        $cal_others_val[] = $this->input->post('cal_others_val' . $i . $j);

                        $others_amount_type_commaList = implode(',', $others_amount_type);
                        $others_amount_val_commaList = implode(',', $others_amount_val);
                        $cal_others_val_commaList = implode(',', $cal_others_val);
                    }
                    $incr_amount = 0;
                    $incr_amount = $this->input->post('monthly_rent_with_increment'. $i) - $this->input->post('monthly_rent');
                    $date_day =  $this->input->post('date_day');
                    $date_month =  $this->input->post('date_month');
                    $incr_start_date = $this->input->post('year_sl'.$i).'-'.$date_month.'-'.$date_day;
                    $sch_end_date = date('Y-m-d', strtotime($incr_start_date . '+1 year'));
                    $sch_end_date1 = date('Y-m-d', strtotime($sch_end_date . '-1 day'));
                    $rent_inc_adj_data = array(
                        'rent_agre_id' => $insert_idss
                        , 'rent_incr_yr' => $this->input->post('year_sl'.$i)
                        , 'rent_amount_type' => $this->input->post('rent_amount_type' . $i)
                        , 'rent_amount_val' => $this->input->post('rent_amount_val' . $i)
                        , 'cal_rent_val' => $this->input->post('cal_rent_val' . $i)
                        , 'others_id_list' => $this->input->post('id_list_final')
                        , 'others_amount_type' => $others_amount_type_commaList
                        , 'others_amount_val' => $others_amount_val_commaList
                        , 'cal_others_val' => $cal_others_val_commaList

                        , 'e_by' => $this->session->userdata['user']['user_id']
                        , 'e_dt' => date('Y-m-d, H:i:s')
                        //, 'start_dt' => date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('start_date' . $i))))
                        , 'start_dt' => $incr_start_date
                        //, 'end_dt' => $sch_end_date1
                        , 'end_dt' => $this->input->post('year_sl_end'.$i)
                        , 'sts' => 1
                    );

                    $this->db->insert('rent_agr_increment_history', $rent_inc_adj_data);
                }
        }

// rent adjustment data
                $adjustment_type = $this->input->post('adjust_adv_type');

                if ($adjustment_type == 4) {
                    $adj_amount_type = $this->input->post('yearly_adj_type');

                    for ($i = 0; $i <= $this->input->post('adj_year_sl'); $i++) {

                        $rent_adjustment_data = array(
                            'rent_agre_id' => $insert_idss
                            , 'adjustment_type' => $adjustment_type
                            , 'percent_dir_type' => $adj_amount_type
                            , 'percent_dir_val' => 0
                            , 'adv_incr_year_val' => $this->input->post('yrly_adj_amt' . $i)
                            , 'adv_incr_year' => $this->input->post('adj_year' . $i)
                            , 'e_by' => $this->session->userdata['user']['user_id']
                            , 'e_dt' => date('Y-m-d, H:i:s')
                            , 'sts' => 1
                        );
                        $this->db->insert('rent_agr_adv_adjustment_history', $rent_adjustment_data);
                    }
                } else if ($adjustment_type == 3) {
                    $adj_amount_type = $this->input->post('percentage_basis_adj');
                    $adjust_amount = $this->input->post('percent_amt');

                    $rent_adjustment_data = array(
                        'rent_agre_id' => $insert_idss
                        , 'adjustment_type' => $adjustment_type
                        , 'percent_dir_type' => $adj_amount_type
                        , 'percent_dir_val' => $adjust_amount
                        , 'adv_incr_year_val' => 0
                        , 'adv_incr_year' => 0
                        , 'e_by' => $this->session->userdata['user']['user_id']
                        , 'e_dt' => date('Y-m-d, H:i:s')
                        , 'sts' => 1
                    );
                    $this->db->insert('rent_agr_adv_adjustment_history', $rent_adjustment_data);
                } else if ($adjustment_type == 2) {
                    $adj_amount_type = 'fixed';
                    $adjust_amount = $this->input->post('fixed_amt');

                    $rent_adjustment_data = array(
                        'rent_agre_id' => $insert_idss
                        , 'adjustment_type' => $adjustment_type
                        , 'percent_dir_type' => $adj_amount_type
                        , 'percent_dir_val' => $adjust_amount
                        , 'adv_incr_year_val' => 0
                        , 'adv_incr_year' => 0
                        , 'e_by' => $this->session->userdata['user']['user_id']
                        , 'e_dt' => date('Y-m-d, H:i:s')
                        , 'sts' => 1
                    );
                    $this->db->insert('rent_agr_adv_adjustment_history', $rent_adjustment_data);
                } else if ($adjustment_type == 1) {
                    $adj_amount_type = 'none';
                    $adjust_amount = 0;

                    $rent_adjustment_data = array(
                        'rent_agre_id' => $insert_idss
                        , 'adjustment_type' => $adjustment_type
                        , 'percent_dir_type' => $adj_amount_type
                        , 'percent_dir_val' => $adjust_amount
                        , 'adv_incr_year_val' => 0
                        , 'adv_incr_year' => 0
                        , 'e_by' => $this->session->userdata['user']['user_id']
                        , 'e_dt' => date('Y-m-d, H:i:s')
                        , 'sts' => 1
                    );
                    $this->db->insert('rent_agr_adv_adjustment_history', $rent_adjustment_data);
                }

//location type data

                $counter_location_type = $this->input->post('counter_location_type');
				$location_types='0';
				
                for ($i = 1; $i <= $counter_location_type; $i++) {

                    if ($this->input->post('delete' . $i) != '1') {
                        $location_type_id = $this->input->post('location_type' . $i);
                        if ($location_type_id == 1) {
                            $location_ref_id = $this->input->post('branch_id' . $i);
                        } else if ($location_type_id == 2) {
                            $location_ref_id = $this->input->post('atm_id' . $i);
                        } else if ($location_type_id == 3) {
                            $location_ref_id = $this->input->post('sme_id' . $i);
                        } else if ($location_type_id == 4) {
                            $location_ref_id = $this->input->post('godown_id' . $i);
                        } else {
                            $location_ref_id = 0;
                        }
                        if($this->input->post('loc_vat_sts'.$i)==''){$loc_vat_sts='yes';}else{$loc_vat_sts = $this->input->post('loc_vat_sts'.$i);}
                        if($this->input->post('loc_tax_sts'.$i)==''){$loc_tax_sts='yes';}else{$loc_tax_sts = $this->input->post('loc_tax_sts'.$i);}
						$location_types.=','.$location_type_id;
                        $rent_location_type_data = array(
                            'rent_agree_id' => $insert_idss
                            , 'cost_center_code' => $this->input->post('cost_center1')
                            , 'location_type_id' => $location_type_id
                            , 'location_mis_id' => $this->input->post('mis_code' . $i)
                            , 'loc_vat_sts' => $loc_vat_sts
                            , 'loc_tax_sts' => $loc_tax_sts
                            , 'cost_per_month' => $this->input->post('cost_sft'.$i)
                            , 'sq_ft' => $this->input->post('square_ft' . $i)
                            , 'cost_in_percent' => $this->input->post('location_type_amount_percentage' . $i)
                            , 'e_by' => $this->session->userdata['user']['user_id']
                            , 'e_dt' => date('Y-m-d, H:i:s')
                            , 'sts' => 1
                        );
                        $this->db->insert('rent_agr_loc_type_and_cost_center', $rent_location_type_data);
                    }
                }
				$location_types_name='';
				$str_lt="SELECT GROUP_CONCAT(NAME) AS location_types_name FROM ref_location_type WHERE id IN (".$location_types.") AND sts=1";				
				$str_lt_query = $this->db->query($str_lt);
				$str_lt_query_row = $str_lt_query->row();
				if(is_object($str_lt_query_row)){$location_types_name=$str_lt_query_row->location_types_name;}
				$this->db->query("update rent_agreement set location_types='".$location_types_name."' where id='".$insert_idss."'");
				
// others loc type

            $counter_others_rent_type = $this->input->post('counter_others_rent_type');
                for ($i = 1; $i <= $counter_others_rent_type; $i++) {
if($this->input->post('others_mis_code'.$i)==''){$mis_code=null;}else{$mis_code = $this->input->post('others_mis_code'.$i);}
if($this->input->post('vat_sts'.$i)==''){$vat_sts=null;}else{$vat_sts = $this->input->post('vat_sts'.$i);}
if($this->input->post('tax_sts'.$i)==''){$tax_sts=null;}else{$tax_sts = $this->input->post('tax_sts'.$i);}
if($this->input->post('others_square_ft' . $i)==''){$others_square_ft=null;}else{$others_square_ft = $this->input->post('others_square_ft' . $i);}
if($this->input->post('others_type_amount_percentage'.$i)==''){$others_type_amount_percentage =null;}else{$others_type_amount_percentage = $this->input->post('others_type_amount_percentage'.$i);}
if($this->input->post('others_type_percentage'.$i)==''){$others_type_percentage =0;}else{$others_type_percentage = $this->input->post('others_type_percentage'.$i);}
if($this->input->post('others_cost_sft'.$i)==''){$others_cost_per_month =0;}else{$others_cost_per_month = $this->input->post('others_cost_sft'.$i);}

                if ($this->input->post('delete_others' . $i) != '1') {
                    $rent_other_location_data = array(
                        'rent_agree_id' => $insert_idss
                        , 'other_loc_type_id' => $this->input->post('rent_others_id' . $i)
                        , 'other_cost_center_code' => $this->input->post('cost_center1')
                        , 'other_loc_mis_id' => $mis_code
                        , 'vat_sts' => $vat_sts
                        , 'tax_sts' => $tax_sts
                        , 'other_sq_ft' => $others_square_ft
                        , 'other_cost_in_percent' => $others_type_amount_percentage
                        , 'others_type_percentage' => $others_type_percentage
                        , 'others_cost_per_month' => $others_cost_per_month
                        , 'e_by' => $this->session->userdata['user']['user_id']
                        , 'e_dt' => date('Y-m-d, H:i:s')
                        , 'sts' => 1
                    );
                    //print_r($rent_other_location_data);

                    $this->db->insert('rent_agr_other_locations', $rent_other_location_data);
                    //echo $this->db->last_query();
                }
            }

// landlord data

                for ($i = 1; $i <= $this->input->post('counter_landlord'); $i++) {
                    if ($this->input->post('delete_landlord' . $i) != '1') {
                       // if($this->input->post('credit_sts'.$i)=='yes'){
                            $rent_landlord_data = array(
                                'rent_agre_id' => $insert_idss
                                , 'vendor_id' => $this->input->post('vendor_id'.$i)
                                , 'credit_sts' => $this->input->post('credit_sts'.$i)
                                , 'credit_amount_percent' => $this->input->post('amount_percentage'.$i)
                                , 'adv_amount_percent' => $this->input->post('advance_amount_percentage'.$i)
                                , 'e_by' => $this->session->userdata['user']['user_id']
                                , 'e_dt' => date('Y-m-d, H:i:s')  
                                , 'sts' => 1
                            );

                            $this->db->insert('rent_agr_landlords', $rent_landlord_data);
                      //  }    
                    }
                }

                $remarks_1 = "rent_agr_landlords,rent_agr_other_locations,rent_agr_loc_type_and_cost_center,rent_agr_adv_adjustment_history,rent_agr_increment_history";
                $rent_data_operation_history = array(
                    'operation_id' => 1
                    , 'operation_ref' => 'rent_agreement'
                    , 'operation_ref_id' => $insert_idss
                    , 'operation_by' => $this->session->userdata['user']['user_id']
                    , 'operation_dt' => date('Y-m-d')
                    , 'operation_ip' => $this->input->ip_address()
                    , 'remarks_1' => $remarks_1
                    , 'remarks_2' => ''
                );
                $this->db->insert('rent_data_operation_history', $rent_data_operation_history);

                
            }

// add end
        } else {

            $insert_idss = $edit_id;

            // increment data

            $increment_type = $this->input->post('increment_type');
            //echo   $increment_type;exit; 
            if($increment_type ==1 || $increment_type ==4){
                $increment_type_val= 0;
                $increment_start_dt_value='';
            }elseif($increment_type ==2){

                $increment_type_val= $this->input->post('increment_every_yr_value');
                $increment_start_dt_value='';
            }elseif($increment_type ==3){
                $increment_type_val= $this->input->post('one_time_increment_yr_no');
                $increment_start_dt_value='';
            }else{
                $increment_type_val= 0;
                $increment_start_dt_value='';
                if ($this->input->post('increment_start_dt_value')) {
                    $increment_start_dt_value = date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('increment_start_dt_value'))));
                }
            }


// totalsqft data - 
            $square_ft = 0;
            for ($i = 1; $i <= $this->input->post('counter_location_type'); $i++) {
                if ($this->input->post('delete_landlord_type' . $i) != '1' && $this->input->post('existing_landlord_type' . $i) == '1') {
                    //update
                    $square_ft+=$this->input->post('square_ft' . $i);
                } else if ($this->input->post('delete_landlord_type' . $i) != '1' && $this->input->post('existing_landlord_type' . $i) == '0') {
// insert

                    $square_ft+=$this->input->post('square_ft' . $i);
                } else if ($this->input->post('delete_landlord_type' . $i) == '1') {
                    
                } else {
                    
                }
            }


// rent lanlords data		
            $vendor_id = '';
            $rent_landlord_name = '';

            $new_vendor_id='';
            $new_rent_landlord_name = '';
            $n=1;

            for ($i = 1; $i <= $this->input->post('counter_landlord'); $i++) {

                if ($this->input->post('delete_landlord' . $i) != '1' && $this->input->post('existing_landlord' . $i) == '1') {
// update
                    if ($i != 1) {
                        $vendor_id.=',';
                    }
                    $vendor_id.=$this->input->post('vendor_id' . $i);
// landlord name select
                    $rent_landlord_id = $this->input->post('vendor_id' . $i);
                    $rent_landlord_str = "select name from vendor where vendor_id='" . $rent_landlord_id . "'";
                    $rent_landlord_query = $this->db->query($rent_landlord_str);
                    $rent_landlord_result = $rent_landlord_query->row();
//print_r($rent_landlord_result);
                    if($this->input->post('location_owner')=='rented'){
                        if ($i != 1) {
                            $rent_landlord_name.=',';
                        }
                        $rent_landlord_name .= $rent_landlord_result->name;
                    }
                      // 8 sep 
                if($this->input->post('credit_sts'.$i)=='yes'){
                    if ($n != 1) {
                        $new_vendor_id.=',';
                        $new_rent_landlord_name.=',';
                    
                    }
                    $new_vendor_id.=$this->input->post('vendor_id'.$i);
                    $new_rent_landlord_name .= $rent_landlord_result->name;
                    $n++;
                }


                    $rent_landlord_data = array(
                        'rent_agre_id' => $insert_idss
                        , 'vendor_id' => $this->input->post('vendor_id' . $i)
                        , 'credit_sts' => $this->input->post('credit_sts' . $i)
                        , 'credit_amount_percent' => $this->input->post('amount_percentage' . $i)
                        , 'adv_amount_percent' => $this->input->post('advance_amount_percentage'.$i)
                        , 'u_by' => $this->session->userdata['user']['user_id']
                        , 'u_dt' => date('Y-m-d, H:i:s')
                        , 'sts' => 1
                    );

                    if ($this->input->post('rent_landlord_id' . $i) != 0 && $this->input->post('rent_landlord_id' . $i) != '') {
                        $this->db->where('id', $this->input->post('rent_landlord_id' . $i));
                        $this->db->update('rent_agr_landlords', $rent_landlord_data);
                    }
                } else if ($this->input->post('delete_landlord' . $i) != '1' && $this->input->post('existing_landlord' . $i) == '0') {
 //insert
                    if ($i != 1) {
                        $vendor_id.=',';
                    }
                    $vendor_id.=$this->input->post('vendor_id' . $i);
// landlord name select
                    $rent_landlord_id = $this->input->post('vendor_id' . $i);
                    $rent_landlord_str = "select name from vendor where vendor_id='" . $rent_landlord_id . "'";
                    $rent_landlord_query = $this->db->query($rent_landlord_str);
                    $rent_landlord_result = $rent_landlord_query->row();


                    if ($i != 1) {
                        $rent_landlord_name.=',';
                    }
                    $rent_landlord_name .= $rent_landlord_result->name;

                      // 8 sep 
                if($this->input->post('credit_sts'.$i)=='yes'){
                    if ($n != 1) {
                        $new_vendor_id.=',';
                        $new_rent_landlord_name.=',';
                    
                    }
                    $new_vendor_id.=$this->input->post('vendor_id'.$i);
                    $new_rent_landlord_name .= $rent_landlord_result->name;
                    $n++;
                }


                    $rent_landlord_data = array(
                        'rent_agre_id' => $insert_idss
                        , 'vendor_id' => $this->input->post('vendor_id' . $i)
                        , 'credit_sts' => $this->input->post('credit_sts' . $i)
                        , 'credit_amount_percent' => $this->input->post('amount_percentage' . $i)
                        , 'adv_amount_percent' => $this->input->post('advance_amount_percentage'.$i)
                        , 'e_by' => $this->session->userdata['user']['user_id']
                        , 'e_dt' => date('Y-m-d, H:i:s')
                        , 'sts' => 1
                    );
                    $this->db->insert('rent_agr_landlords', $rent_landlord_data);
                } else if ($this->input->post('delete_landlord' . $i) == '1' && $this->input->post('existing_landlord' . $i) == '1') {
                    // delete
                    $rent_landlord_name .='';
                    $vendor_id .='';

                    $new_vendor_id.='';
                    $new_rent_landlord_name .='';

                    $rent_landlord_data = array(
                        'sts' => 0
                        , 'd_by' => isset($this->session->userdata['user']['user_id']) ? $this->session->userdata['user']['user_id'] : NULL
                        , 'd_dt' => date('Y-m-d, H:i:s')
                    );
                    $this->db->where('id', $this->input->post('rent_landlord_id' . $i));
                    $this->db->update('rent_agr_landlords', $rent_landlord_data);
                } else {
                    
                }
            }

            // rent agreement 		
            $data = array(
               
                 'location_name' => $this->input->post('location_name')
                , 'total_square_ft' => $this->input->post('total_square_ft')
                , 'location_address' => $this->input->post('location_address')
                , 'location_division' => $this->input->post('location_division')
                , 'rent_start_dt' => $rent_start_dt
                , 'agree_exp_dt' => $agree_exp_dt
                , 'point_of_payment' => $this->input->post('point_of_payment')  
                , 'location_owner' => $this->input->post('location_owner')
                , 'agree_cost_center' => $this->input->post('cost_center1')
                , 'total_advance' => $this->input->post('total_advance')
                , 'monthly_rent' => $this->input->post('monthly_rent')
                , 'others_rent' => $this->input->post('others_rent')
                // , 'all_landlord_ids' => $vendor_id
                // , 'all_landlord_names' => $rent_landlord_name      

                , 'landlord_ids' => $new_vendor_id
                , 'landlord_names' => $new_rent_landlord_name
                , 'tax_wived' => $this->input->post('tax_wived')
                , 'adjust_adv_type' => $this->input->post('adjust_adv_type')
                , 'increment_type' => $this->input->post('increment_type')
                , 'increment_type_val' => $increment_type_val
                , 'incr_start_date' => $increment_start_dt_value
                
                , 'u_by' => $this->session->userdata['user']['user_id']
                , 'u_dt' => date('Y-m-d, H:i:s')
                , 'sts' => 1
               
               
            );


            $this->db->where('id', $insert_idss);
            $this->db->update('rent_agreement', $data);

            if($add_edit == "modify"){
                $update_data = array(
                            
                            'modify_by' => isset($this->session->userdata['user']['user_id']) ? $this->session->userdata['user']['user_id'] : NULL
                            , 'modify_dt' => date('Y-m-d, H:i:s')
                        );
                        $this->db->where('id', $insert_idss);
                        $this->db->update('rent_agreement', $update_data);
            }

//location type data
			$location_types='0';
            $counter_location_type = $this->input->post('counter_location_type');
            for ($i = 1; $i <= $counter_location_type; $i++) {

                $location_type_id = $this->input->post('location_type' . $i);


                if ($this->input->post('delete_landlord_type' . $i) != '1' && $this->input->post('existing_location_type' . $i) == '1') {
 // update
                    $rent_location_type_data = array(
                        'rent_agree_id' => $insert_idss
                        , 'cost_center_code' => $this->input->post('cost_center1')
                        , 'location_type_id' => $location_type_id
                        , 'location_mis_id' => $this->input->post('mis_code'. $i)
                        , 'loc_vat_sts' => $this->input->post('loc_vat_sts'.$i)
                        , 'loc_tax_sts' => $this->input->post('loc_tax_sts'.$i)
                        , 'sq_ft' => $this->input->post('square_ft' . $i)
                        , 'cost_in_percent' => $this->input->post('location_type_amount_percentage' . $i)
                        , 'u_by' => $this->session->userdata['user']['user_id']
                        , 'u_dt' => date('Y-m-d, H:i:s')
                        , 'sts' => 1
                    );

                    if ($this->input->post('rent_loc_type_id' . $i) != 0 && $this->input->post('rent_loc_type_id' . $i) != '') {
                        $this->db->where('id', $this->input->post('rent_loc_type_id' . $i));
                        $this->db->update('rent_agr_loc_type_and_cost_center', $rent_location_type_data);
						$location_types.=','.$location_type_id;
                    }
                } else if ($this->input->post('delete_landlord_type' . $i) != '1' && $this->input->post('existing_location_type' . $i) == '0') {
//insert
                    $rent_location_type_data = array(
                        'rent_agree_id' => $insert_idss
                        , 'cost_center_code' => $this->input->post('cost_center1')
                        , 'location_type_id' => $location_type_id
                        , 'location_mis_id' => $this->input->post('mis_code' . $i)
                        , 'loc_vat_sts' => $this->input->post('loc_vat_sts'.$i)
                        , 'loc_tax_sts' => $this->input->post('loc_tax_sts'.$i)
                        , 'sq_ft' => $this->input->post('square_ft' . $i)
                        , 'cost_in_percent' => $this->input->post('location_type_amount_percentage' . $i)
                        , 'e_by' => $this->session->userdata['user']['user_id']
                        , 'e_dt' => date('Y-m-d, H:i:s')
                        , 'sts' => 1
                    );
                    $this->db->insert('rent_agr_loc_type_and_cost_center', $rent_location_type_data);
					$location_types.=','.$location_type_id;
                } else if ($this->input->post('delete_landlord_type' . $i) == '1' && $this->input->post('existing_location_type' . $i) == '1') {
//delete
                    $rent_location_type_data = array(
                        'sts' => 0
                        , 'd_by' => isset($this->session->userdata['user']['user_id']) ? $this->session->userdata['user']['user_id'] : NULL
                        , 'd_dt' => date('Y-m-d, H:i:s')
                    );
                    $this->db->where('id', $this->input->post('rent_loc_type_id' . $i));
                    $this->db->update('rent_agr_loc_type_and_cost_center', $rent_location_type_data);
                } else {
                    
                }
            }
			
			$location_types_name='';
			$str_lt="SELECT GROUP_CONCAT(NAME) AS location_types_name FROM ref_location_type WHERE id IN (".$location_types.") AND sts=1";				
			$str_lt_query = $this->db->query($str_lt);
			$str_lt_query_row = $str_lt_query->row();
			if(is_object($str_lt_query_row)){$location_types_name=$str_lt_query_row->location_types_name;}
			$this->db->query("update rent_agreement set location_types='".$location_types_name."' where id='".$insert_idss."'");

// others location type data

  $counter_others_rent_type = $this->input->post('counter_others_rent_type');
            for ($i = 1; $i <= $counter_others_rent_type; $i++) {
                
                
if($this->input->post('others_mis_code'.$i)==''){$mis_code=null;}else{$mis_code = $this->input->post('others_mis_code'.$i);}
if($this->input->post('vat_sts'.$i)==''){$vat_sts=null;}else{$vat_sts = $this->input->post('vat_sts'.$i);}
if($this->input->post('tax_sts'.$i)==''){$tax_sts=null;}else{$tax_sts = $this->input->post('tax_sts'.$i);}
if($this->input->post('others_square_ft' . $i)==''){$others_square_ft=null;}else{$others_square_ft = $this->input->post('others_square_ft' . $i);}
if($this->input->post('others_type_amount_percentage'.$i)==''){$others_type_amount_percentage =null;}else{$others_type_amount_percentage = $this->input->post('others_type_amount_percentage'.$i);}
if($this->input->post('others_type_percentage'.$i)==''){$others_type_percentage =null;}else{$others_type_percentage = $this->input->post('others_type_percentage'.$i);}


                if ($this->input->post('delete_others' . $i) != '1' && $this->input->post('existing_others' . $i) == '1') {
 // update
                   $rent_other_location_data = array(
                        'rent_agree_id' => $insert_idss
                        , 'other_loc_type_id' => $this->input->post('rent_others_id' . $i)
                        , 'other_cost_center_code' => $this->input->post('cost_center1')
                        , 'other_loc_mis_id' => $mis_code
                         , 'vat_sts' => $vat_sts
                        , 'tax_sts' => $tax_sts
                        , 'other_sq_ft' => $others_square_ft
                        , 'other_cost_in_percent' => $others_type_amount_percentage
                        , 'others_type_percentage' => $others_type_percentage
                        , 'u_by' => $this->session->userdata['user']['user_id']
                        , 'u_dt' => date('Y-m-d, H:i:s')
                        , 'sts' => 1
                    );

                    if ($this->input->post('others_loc_type_id' . $i) != 0 && $this->input->post('others_loc_type_id' . $i) != '') {
                        $this->db->where('id', $this->input->post('others_loc_type_id' . $i));
                        $this->db->update('rent_agr_other_locations', $rent_other_location_data);
                    }
                } else if ($this->input->post('delete_others' . $i) != '1' && $this->input->post('existing_others' . $i) == '0') {
//insert
                     $rent_other_location_data = array(
                        'rent_agree_id' => $insert_idss
                        , 'other_loc_type_id' => $this->input->post('rent_others_id' . $i)
                        , 'other_cost_center_code' => $this->input->post('cost_center1')
                        , 'other_loc_mis_id' => $mis_code
                        , 'vat_sts' => $vat_sts
                        , 'tax_sts' => $tax_sts
                        , 'other_sq_ft' => $others_square_ft
                        , 'other_cost_in_percent' => $others_type_amount_percentage
                        , 'others_type_percentage' => $others_type_percentage
                        , 'u_by' => $this->session->userdata['user']['user_id']
                        , 'u_dt' => date('Y-m-d, H:i:s')
                        , 'sts' => 1
                    );
                    $this->db->insert('rent_agr_other_locations', $rent_other_location_data);
                } else if ($this->input->post('delete_others' . $i) == '1' && $this->input->post('existing_others' . $i) == '1') {
//delete
                    $rent_other_location_data = array(
                        'sts' => 0
                        , 'd_by' => isset($this->session->userdata['user']['user_id']) ? $this->session->userdata['user']['user_id'] : NULL
                        , 'd_dt' => date('Y-m-d, H:i:s')
                    );
                    $this->db->where('id', $this->input->post('others_loc_type_id' . $i));
                    $this->db->update('rent_agr_other_locations', $rent_other_location_data);
                } else {
                    
                }
            }


// if incr_change_sts = 0 then update, if 1 then insert
         //   if ($this->input->post('incr_change_sts') == '1') {

                $rent_inc_adj_data_old = array(
                    'sts' => 0
                );
                $this->db->where('rent_agre_id', $insert_idss);
                $this->db->update('rent_agr_increment_history', $rent_inc_adj_data_old);
          //  }

   if($this->input->post('location_owner')=='rented'){             
      for ($i = 0; $i <= $this->input->post('count_year'); $i++) {

                    unset($others_amount_type);
                    unset($others_amount_val);
                    unset($cal_others_val);

                    $others_amount_type_commaList = '';
                    for ($j = 0; $j < $this->input->post('element_number'); $j++){
                        $others_amount_type[] = $this->input->post('others_amount_type' . $i . $j);
                        $others_amount_val[] = $this->input->post('others_amount_val' . $i . $j);
                        $cal_others_val[] = $this->input->post('cal_others_val' . $i . $j);

                        $others_amount_type_commaList = implode(',', $others_amount_type);
                        $others_amount_val_commaList = implode(',', $others_amount_val);
                        $cal_others_val_commaList = implode(',', $cal_others_val);
                    }
                    $incr_amount = 0;
                    $incr_amount = $this->input->post('monthly_rent_with_increment' . $i) - $this->input->post('monthly_rent');
                    $date_day =  $this->input->post('date_day');
                    $date_month =  $this->input->post('date_month');
                    $incr_start_date = $this->input->post('year_sl'.$i).'-'.$date_month.'-'.$date_day;
                    $sch_end_date = date('Y-m-d', strtotime($incr_start_date . '+1 year'));
                    $sch_end_date1 = date('Y-m-d', strtotime($sch_end_date . '-1 day'));

                    $rent_inc_adj_data = array(
                        'rent_agre_id' => $insert_idss
                        , 'rent_incr_yr' => $this->input->post('year_sl' . $i)
                        , 'rent_amount_type' => $this->input->post('rent_amount_type'.$i)
                        , 'rent_amount_val' => $this->input->post('rent_amount_val' . $i)
                        , 'cal_rent_val' => $this->input->post('cal_rent_val' . $i)
                        , 'others_id_list' => $this->input->post('id_list_final')
                        , 'others_amount_type' => $others_amount_type_commaList
                        , 'others_amount_val' => $others_amount_val_commaList
                        , 'cal_others_val' => $cal_others_val_commaList
                        , 'e_by' => $this->session->userdata['user']['user_id']
                        , 'e_dt' => date('Y-m-d, H:i:s')
                        //, 'start_dt' => date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('start_date' . $i))))
                        , 'start_dt' => $incr_start_date
                        //, 'end_dt' => $sch_end_date1
                        , 'end_dt' => $this->input->post('year_sl_end'.$i)
                        , 'sts' => 1
                        , 'e_dt' => date('Y-m-d, H:i:s')
                    );

                    $this->db->insert('rent_agr_increment_history', $rent_inc_adj_data);
                }           
           }     
   
//rent adjustment data

            $adjustment_type = $this->input->post('adjust_adv_type');

//changed
            $rent_adjustment_data_old = array(
                'sts' => 0
            );
            $this->db->where('rent_agre_id', $insert_idss);
            $this->db->update('rent_agr_adv_adjustment_history', $rent_adjustment_data_old);


                if ($adjustment_type == 4) {
                    $adj_amount_type = $this->input->post('yearly_adj_type');

                    for ($i = 0; $i <= $this->input->post('adj_year_sl'); $i++) {

                        $rent_adjustment_data = array(
                            'rent_agre_id' => $insert_idss
                            , 'adjustment_type' => $adjustment_type
                            , 'percent_dir_type' => $adj_amount_type
                            , 'percent_dir_val' => 0
                            , 'adv_incr_year_val' => $this->input->post('yrly_adj_amt' . $i)
                            , 'adv_incr_year' => $this->input->post('adj_year' . $i)
                            , 'e_by' => $this->session->userdata['user']['user_id']
                            , 'e_dt' => date('Y-m-d, H:i:s')
                            , 'sts' => 1
                        );
                        $this->db->insert('rent_agr_adv_adjustment_history', $rent_adjustment_data);
                    }
                } else if ($adjustment_type == 3) {
                    $adj_amount_type = $this->input->post('percentage_basis_adj');
                    $adjust_amount = $this->input->post('percent_amt');

                    $rent_adjustment_data = array(
                        'rent_agre_id' => $insert_idss
                        , 'adjustment_type' => $adjustment_type
                        , 'percent_dir_type' => $adj_amount_type
                        , 'percent_dir_val' => $adjust_amount
                        , 'adv_incr_year_val' => 0
                        , 'adv_incr_year' => 0
                        , 'e_by' => $this->session->userdata['user']['user_id']
                        , 'e_dt' => date('Y-m-d, H:i:s')
                        , 'sts' => 1
                    );
                    $this->db->insert('rent_agr_adv_adjustment_history', $rent_adjustment_data);
                } else if ($adjustment_type == 2) {
                    $adj_amount_type = 'fixed';
                    $adjust_amount = $this->input->post('fixed_amt');

                    $rent_adjustment_data = array(
                        'rent_agre_id' => $insert_idss
                        , 'adjustment_type' => $adjustment_type
                        , 'percent_dir_type' => $adj_amount_type
                        , 'percent_dir_val' => $adjust_amount
                        , 'adv_incr_year_val' => 0
                        , 'adv_incr_year' => 0
                        , 'e_by' => $this->session->userdata['user']['user_id']
                        , 'e_dt' => date('Y-m-d, H:i:s')
                        , 'sts' => 1
                    );
                    $this->db->insert('rent_agr_adv_adjustment_history', $rent_adjustment_data);
                } else if ($adjustment_type == 1) {
                    $adj_amount_type = 'none';
                    $adjust_amount = 0;

                    $rent_adjustment_data = array(
                        'rent_agre_id' => $insert_idss
                        , 'adjustment_type' => $adjustment_type
                        , 'percent_dir_type' => $adj_amount_type
                        , 'percent_dir_val' => $adjust_amount
                        , 'adv_incr_year_val' => 0
                        , 'adv_incr_year' => 0
                        , 'e_by' => $this->session->userdata['user']['user_id']
                        , 'e_dt' => date('Y-m-d, H:i:s')
                        , 'sts' => 1
                    );
                    $this->db->insert('rent_agr_adv_adjustment_history', $rent_adjustment_data);
                }
 
        }

        return $insert_idss;
    }


    function verify_action() {
        $data = $this->db->get_where('rent_agreement', array('id' => $this->input->post('id')));

        $agree_data = $data->row();
      
       if ($this->input->post('type') == 'stf') {

            if ($agree_data->sts == 1 && $agree_data->dept_v_by != '' && ($agree_data->stf_by == '' || $agree_data->resend_by != '')) {
                $rent_agreement_data = array(
                    'agree_current_sts_id' => 3
                    , 'agree_pervious_sts_id' => 3
                    , 'stf_by' => isset($this->session->userdata['user']['user_id']) ? $this->session->userdata['user']['user_id'] : NULL
                    , 'stf_dt' => date("Y-m-d h:i:s", strtotime("now"))
                );

                $this->db->where('id', $this->input->post('id'));
                $this->db->update('rent_agreement', $rent_agreement_data);

                $operation = array(
                    'operation_id' => 9
                    , 'operation_ref' => 'rent_agreement'
                    , 'operation_ref_id' => $this->input->post('id')
                    , 'operation_by' => $this->session->userdata['user']['user_id']
                    , 'operation_dt' => date('Y-m-d, H:i:s')
                    , 'operation_ip' => $this->input->ip_address()
                    , 'remarks_2' => 'Agreement Sent to Finance'
                );
                $this->db->insert('rent_data_operation_history', $operation);
                return 2;
            } else {
                return 1;
            }
        } 
		else if ($this->input->post('type') == 'fin_verify') {

            if ($agree_data->agree_current_sts_id == 4 && $agree_data->fin_v_by == '') {
                $rent_agreement_data = array(
                    'agree_current_sts_id' => 5
                    , 'agree_pervious_sts_id' => 5
                    , 'fin_v_by' => isset($this->session->userdata['user']['user_id']) ? $this->session->userdata['user']['user_id'] : NULL
                    , 'fin_v_dt' => date("Y-m-d h:i:s", strtotime("now"))
                );

                $this->db->where('id', $this->input->post('id'));
                $this->db->update('rent_agreement', $rent_agreement_data);

                $operation = array(
                    'operation_id' => 7
                    , 'operation_ref' => 'rent_agreement'
                    , 'operation_ref_id' => $this->input->post('id')
                    , 'operation_by' => $this->session->userdata['user']['user_id']
                    , 'operation_dt' => date('Y-m-d, H:i:s')
                    , 'operation_ip' => $this->input->ip_address()
                    , 'remarks_2' => 'Agreement Verified by Finance'
                );
                $this->db->insert('rent_data_operation_history', $operation);
                return 2;
            } else {
                return 1;
            }
        }
    }


    function new_agr_verify_action() {
        $data = $this->db->get_where('rent_agreement', array('id' => $this->input->post('rent_agree_id')));

        $agree_data = $data->row();
     
        if ($this->input->post('agr_verify_type') == 'admin_verify') {
            if ($agree_data->sts == 1 && ($agree_data->dept_v_by == '' || $agree_data->resend_by != '')) {

                $rent_agreement_data = array(
                    //'dept_v_sts'=>1
                    'agree_current_sts_id' => 2
                    , 'agree_pervious_sts_id' => 2
                    , 'dept_v_by' => isset($this->session->userdata['user']['user_id']) ? $this->session->userdata['user']['user_id'] : NULL
                    , 'dept_v_dt' => date("Y-m-d h:i:s", strtotime("now"))
                );

                $this->db->where('id', $this->input->post('rent_agree_id'));
                $this->db->update('rent_agreement', $rent_agreement_data);


                $operation = array(
                    'operation_id' => 6
                    , 'operation_ref' => 'rent_agreement'
                    , 'operation_ref_id' => $this->input->post('rent_agree_id')
                    , 'operation_by' => $this->session->userdata['user']['user_id']
                    , 'operation_dt' => date('Y-m-d, H:i:s')
                    , 'operation_ip' => $this->input->ip_address()
                    , 'remarks_2' => 'Agreement Verified by Admin'
                );
                $this->db->insert('rent_data_operation_history', $operation);


                return 2;
            } else {
                return 1;
            }
        } 
		
		else if ($this->input->post('agr_verify_type') == 'fin_verify') {

            // 7 january 2018
            $last_paid_month = $this->input->post('last_paid_month');
            //echo $last_paid_month;exit;
            $rent_agree_id = $this->input->post('rent_agree_id');
            if($last_paid_month!=''){
                
                $sql = "UPDATE rent_ind_schedule
                        SET paid_sts='paid' 
                        WHERE  maturity_dt <= '$last_paid_month' AND rent_agree_id=$rent_agree_id";
                $q = $this->db->query($sql);
            }
            
            if ($agree_data->agree_current_sts_id == 4 && $agree_data->fin_v_by == '') {
                $rent_agreement_data = array(
                    'agree_current_sts_id' => 5
                    , 'agree_pervious_sts_id' => 5
                    , 'fin_v_by' => isset($this->session->userdata['user']['user_id']) ? $this->session->userdata['user']['user_id'] : NULL
                    , 'fin_v_dt' => date("Y-m-d h:i:s", strtotime("now"))
                );

                $this->db->where('id', $this->input->post('rent_agree_id'));
                $this->db->update('rent_agreement', $rent_agreement_data);
                $landlord_names = str_replace(",","|",$agree_data->landlord_names);
                $this->stf_mail('rashed.mosharef@ebl-bd.com',$agree_data->agreement_ref_no,$agree_data->location_name,$landlord_names);

                $operation = array(
                    'operation_id' => 7
                    , 'operation_ref' => 'rent_agreement'
                    , 'operation_ref_id' => $this->input->post('rent_agree_id')
                    , 'operation_by' => $this->session->userdata['user']['user_id']
                    , 'operation_dt' => date('Y-m-d, H:i:s')
                    , 'operation_ip' => $this->input->ip_address()
                    , 'remarks_2' => 'Agreement Verified by Finance'
                );
                $this->db->insert('rent_data_operation_history', $operation);
                return 2;
            } else {
                return 1;
            }

        }

    }

    function stf_mail($email,$agreement_ref_no,$location_name,$landlord_names) 
    {
            $ContactPerson="Rent Management System";
            $admineMail='khanmra@ebl-bd.com';
            $landlord_name = str_replace("|",",",$landlord_names);
            
            
            $subject=" Sent to Finance (STF) at Rent Management System";
            
            $msg="Dear Concern,<br><br>
            The following Rent Agreeement has been sent to Finance.<br><br>Agreement Ref:&nbsp;&nbsp;";
            $msg.=$agreement_ref_no; 
            $msg.="<br><br>Location Name:&nbsp;&nbsp;";
            $msg.=$location_name;    
            $msg.="<br><br>Landlords:&nbsp;&nbsp;";
            $msg.=$landlord_name;  
            $msg.="<br><br>Click on the link to login now <a href='".base_url()."'>".base_url()."</a>";
            $msg.="<br><br>Regards<br>Rent Management System <br>Eastern Bank Ltd.";
             
            if($email!="") {
                $this->send_email($ContactPerson,$admineMail,$email,$subject,$msg);
            }
    }
   
    
    function send_email($fromName, $fromEmail, $toemail, $subject, $message,$ccemail = '') {
        
            require_once 'PHPMailer/PHPMailerAutoload.php';
            
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
            $mail->clearAddresses();
            //return $m;
    }

    function delete_action() {

        $sts_row = $this->getAgreementStatus($this->input->post('id'));
        if (count($sts_row) <= 0) {
            return 'Something is wrong. Please try again';
        }

        if ($sts_row->sts == 0) {
            return 'Data already deleted.';
        }
        // else if ($sts_row->agree_current_sts_id >= 5) {
        //     return 'Verified data cannot be deleted.';
        // }

        if ($this->input->post('id')) {
            $agreement = array(
                'sts' => 0
                , 'd_by' => isset($this->session->userdata['user']['user_id']) ? $this->session->userdata['user']['user_id'] : NULL
                , 'd_dt' => date("Y-m-d h:i:s", strtotime("now"))
            );
            $this->db->where('id', $this->input->post('id'));
            $this->db->update('rent_agreement', $agreement);


            $agreement_landlord = array(
                'sts' => 0
                , 'd_by' => isset($this->session->userdata['user']['user_id']) ? $this->session->userdata['user']['user_id'] : NULL
                , 'd_dt' => date("Y-m-d h:i:s", strtotime("now"))
            );
            $this->db->where('rent_agre_id', $this->input->post('id'));
            $this->db->update('rent_agr_landlords', $agreement_landlord);
        }
        return '1';
        
    }

   

    function get_services() {
        $this->db
                ->select('j0.*,j1.name as service_name')
                ->from("bill_services as j0")
                ->join("ref_service as j1", 'j0.service_id=j1.id', 'left')
                ->where('j0.bill_id', $this->input->post('id'))
                ->where('j0.sts', 1)
                ->order_by("j0.id", "ASC");
        $q = $this->db->get();
        return $q->result();
    }

    function get_vendor_services($vendor_id = null) {
        $services = array();
        if (isset($vendor_id) && $vendor_id != "") {
            $this->db->limit(1);
            $data = $this->db->get_where('vendors', array('vendor_id' => $vendor_id));
            if ($data->num_rows() > 0) {
                $vendor = $data->row();
                if ($vendor->services) {
                    $vendor_services = explode(',', $vendor->services);
                    if ($vendor_services) {
                        $this->db
                                ->select('j0.id as service_id,j0.name as service_name')
                                ->from("ref_service as j0")
                                ->where_in('j0.id', $vendor_services)
                                ->order_by("j0.id", "ASC");
                        $q = $this->db->get();
                        $services = $q->result();
                    }
                }
            }
        }
        return $services;
    }


    function get_info($add_edit, $id) {
        if ($id != '') {
            $this->db->limit(1);
            $this->db
                    ->select("*")
                    ->from("rent_agreement as j0")
                    ->where('j0.id', $id);

            $q = $this->db->get();
            return $q->row();
        }
        return array();
    }

    function rent_cost_center_get_info($add_edit, $id) {
        $sql = "select * from rent_cost_center where rent_agre_id=$id and sts=1";
        $q = $this->db->query($sql);
        return $q->result();
    }

    function rent_landlords_get_info($add_edit, $id) {
        $sql = "select * from rent_agr_landlords where rent_agre_id=$id and sts=1";
        $q = $this->db->query($sql);
        return $q->result();
    }

    function rent_location_type_cost_center_get_info($add_edit, $id) {
        $sql = "select * from rent_agr_loc_type_and_cost_center where rent_agree_id=$id and sts=1";
        $q = $this->db->query($sql);
        return $q->result();
    }

    function rent_location_type_cost_center_get_info_for_verify($add_edit, $id) {

        $sql = "select agr_loc_type.*, ref_location_type.name 
        from rent_agr_loc_type_and_cost_center agr_loc_type
        join  ref_location_type on(agr_loc_type.location_type_id=ref_location_type.id)
        where agr_loc_type.rent_agree_id=$id and agr_loc_type.sts=1";
        $q = $this->db->query($sql);
        return $q->result();
    }

    function rent_landlords_get_info_for_verify($add_edit, $id) {

        $sql = "select rent_agr_landlords.*,vendor.name,vendor.account_no 
        from rent_agr_landlords 
        join vendor on(rent_agr_landlords.vendor_id=vendor.vendor_id)
        where rent_agr_landlords.rent_agre_id=$id and rent_agr_landlords.sts=1";
        $q = $this->db->query($sql);
        return $q->result();
    }

    function others_rent_location_info($add_edit, $id) {

        $sql = "select * from rent_agr_other_locations where rent_agree_id=$id and sts=1";
        $q = $this->db->query($sql);
        return $q->result();
    }

    function get_monthly_rent_per_year($current_year, $rent_agree_id) {

        $sql = "select * from rent_agr_increment_history where rent_agre_id=$rent_agree_id and rent_incr_yr=$current_year  and sts=1";
        $q = $this->db->query($sql);
       
        return $q->row();
    }

    function get_others_rent_per_year($rent_agree_id) {

        $sql = "SELECT SUM(other_cost_in_percent) AS total_others_amount 
                FROM rent_agr_other_locations
                WHERE rent_agree_id=$rent_agree_id";
        $q = $this->db->query($sql);
       
        return $q->row();
    }

    function get_adjustment_amount_per_year($current_year, $rent_agree_id) {

        $sql = "select * from rent_agr_adv_adjustment_history where rent_agre_id=$rent_agree_id and adv_incr_year=$current_year  and sts=1";
        $q = $this->db->query($sql);
      
        return $q->row();
    }

    function rent_adjustment_get_info($add_edit, $id) {

        $sql = "select * from rent_agr_adv_adjustment_history where rent_agre_id=$id and sts=1";
        $q = $this->db->query($sql);
     
        return $q->result();
    }

    function single_rent_adjustment_get_info($add_edit, $id) {

        $sql = "select * from rent_agr_adv_adjustment_history where rent_agre_id=$id and sts=1";
        $q = $this->db->query($sql);
      
        return $q->row();
    }

    function single_rent_tax_info($add_edit, $id) {

        $sql = "select * from ref_rent_tax where id=$id and sts=1";
        $q = $this->db->query($sql);
      
        return $q->row();
    }

     function single_rent_vat_info($add_edit, $id) {

        $sql = "select * from ref_rent_vat where id=$id and sts=1";
        $q = $this->db->query($sql);
        return $q->row();
    }

    function rent_schedule_info($id) {

        $sql = "SELECT *  FROM rent_ind_schedule 
		WHERE rent_agree_id=$id";
        $q = $this->db->query($sql);
        return $q->result();
    }

    function single_rent_schedule_info($id) {

        $sql = "SELECT *  FROM rent_ind_schedule 
        WHERE rent_agree_id=$id";
        $q = $this->db->query($sql);
        return $q->row();
    }

    function rent_payment_schedule_info($id) {

        $sql = "select * from rent_ind_schedule where rent_agree_id=$id ";
        $q = $this->db->query($sql);
        return $q->result();
    }

    function rent_matured_payment_schedule_info($id) {

        $sql = "SELECT *  FROM rent_ind_schedule 
		WHERE maturity_dt < CURDATE() and rent_agree_id=$id";
        $q = $this->db->query($sql);
        return $q->result();
    }

     function rent_matured_unpaid_payment_schedule_info($id) {

        $sql = "SELECT *  FROM rent_ind_schedule 
        WHERE maturity_dt < CURDATE() and rent_agree_id=$id and paid_sts='unpaid' ";
        $q = $this->db->query($sql);
        return $q->result();
    }

      function rent_matured_unpaid_schedule_info_for_provision($id) {

        $sql = "SELECT *  FROM rent_ind_schedule 
        WHERE maturity_dt < CURDATE() and rent_agree_id=$id and paid_sts='unpaid' and provision_sts=0  and sche_add_sts=0";
        $q = $this->db->query($sql);
        return $q->result();
    }

     function rent_matured_unpaid_schedule_info_for_provision_edit($id,$provision_ref_counter){

        $sql = "SELECT * FROM rent_ind_schedule 
        WHERE maturity_dt < CURDATE() and rent_agree_id=$id and paid_sts='unpaid'  and sche_add_sts=0
        and ((provision_ref=$provision_ref_counter  and provision_sts=1) or provision_sts=0)";
        $q = $this->db->query($sql);
        return $q->result();

    }


     function rent_matured_unpaid_payment_schedule_info_for_payment($id){

        $sql = "SELECT *  FROM rent_ind_schedule 
        WHERE  rent_agree_id=$id  and sche_add_sts!=1"; 
        $q = $this->db->query($sql);
        return $q->result();
    }

    function rent_matured_unpaid_payment_schedule_info_for_payment_edit($id) {

        $sql = "SELECT *  FROM rent_ind_schedule 
        WHERE  rent_agree_id=$id "; 
      
        $q = $this->db->query($sql);
        return $q->result();
    }

    function get_single_cost_center_info($cc_id) {

        $sql = "SELECT * FROM cost_center WHERE code='$cc_id' and sts=1";
        $q = $this->db->query($sql);
        return $q->row();
    }



    function rent_inc_adj_get_info($add_edit, $id) {

        if($add_edit=='report'){
            $sql = "select * from rent_agr_increment_history where rent_agre_id=$id  AND rent_amount_val > 0 and sts=1";
        }else{
            $sql = "select * from rent_agr_increment_history where rent_agre_id=$id and sts=1";
        }
        
        $q = $this->db->query($sql);
     
        return $q->result();
    }

    function rent_file_upload_get_info($add_edit, $id) {
        // 27 march
        $this->db->query("update rent_upload_temp set window_close_sts=1 
		WHERE userid ='" . $this->session->userdata['user']['user_id'] . "'
		");

        // 27 january
        $sql = "select * from rent_agr_doc where rent_agree_id=$id and sts=1";
        $q = $this->db->query($sql);
        //echo $this->db->last_query();exit;
        return $q->result();
    }


    function rent_file_upload_get_info_edit() {
        // 12 july
         $doc_type_id = $this->input->post('doc_type_id');
         $agreement_id_edit = $this->input->post('agreement_id_edit');

        $sql = "select * from rent_upload_temp where rent_agree_id=$agreement_id_edit and doc_type_id=$doc_type_id ";
        $q = $this->db->query($sql);
      
        return $q->num_rows();
    }

    function get_parameter_data_where_in($table, $orderby, $where = NULL) {
        $this->db->select('*', FALSE);
        $this->db->from($table);
        if (!empty($where))
            $this->db->where_in('id', $where);
        $this->db->order_by($orderby);
        $q = $this->db->get();
        return $q->result();
    }

    function get_parameter_data_single($table, $orderby, $where = NULL) {
        $this->db->select('*', FALSE);
        $this->db->from($table);
        if (!empty($where))
            $this->db->where($where);
        $this->db->order_by($orderby);
        $this->db->limit(1);
        $q = $this->db->get();
        //echo $this->db->last_query();exit;
        return $q->row();
    }

    function get_parameter_data($table, $orderby, $where = NULL) {
        $this->db->select('*', FALSE);
        $this->db->from($table);
        if (!empty($where))
            $this->db->where($where);
        $this->db->order_by($orderby);
        $q = $this->db->get();
        return $q->result();
    }

    // 23 january 
    function get_child_list($table, $orderby) {

        $cost_center = $this->input->post("cost_center");
       
        $this->db->select('*', FALSE);
        $this->db->from($table);
        $this->db->where('code', $cost_center);
       		 
        $this->db->order_by($orderby);
        $this->db->limit(1);
        $q = $this->db->get();
        // echo $this->db->last_query();exit;
        return $q->row();
    }

    function checkPaidStatus($id = 0){
        if ($id == 0) {
            return array();
        }

        $q= $this->db->select('SUM(IF(sh.paid_sts = "paid", 1, 0)) as paid_count,
                               SUM(IF(sh.paid_sts = "unpaid", 1, 0)) as unpaid_count,
                               SUM(IF(sh.paid_sts = "advance", 1, 0)) as advance_count,
                               SUM(IF(sh.paid_sts = "closed", 1, 0)) as closed_count,
                               SUM(IF(sh.paid_sts = "stop", 1, 0)) as stop_count', false)
                     ->from('rent_ind_schedule sh', false)
                     ->where('rent_agree_id = '.$id)
                     ->get();
        return $q->row();
    }

    function check_agree_rent_in_adv($id){
        $sql = "select * from 
        rent_ind_schedule 
        where 
        rent_agree_id=$id 
        and paid_sts='advance' ";
        $q = $this->db->query($sql);
      
        return $q->num_rows();
    }

    function get_files_details($agree_id){
        $query = $this->db->select('t.name, d.doc_type_id, GROUP_CONCAT(d.file_name) AS files')
                          ->from('rent_agr_doc d')
                          ->join('ref_rent_document_type t', 't.id = d.doc_type_id', 'LEFT OUTER')
                          ->where('d.rent_agree_id = '.$agree_id)
                          ->group_by('d.doc_type_id')
                          ->get();
        return $query->result();
    }

    function getAgreementStatus($id = 0){
        $res = $this->db->select('id, sts, agree_current_sts_id, agree_pervious_sts_id, arear_sts')
                        ->from('rent_agreement')
                        ->where('id = '.$id)
                        ->get()
                        ->row();
        return $res;
    }

    function get_vendor_code_list($table,$orderby,$where = NULL)
    {
        $this->db->select('*',FALSE);
        $this->db->from($table);
        //$this->db->where('sts',1);
        if (!empty($where))
            $this->db->where($where);
        $this->db->order_by($orderby);

        $q=$this->db->get();
        return $q->result();
    }

    function get_tax_slab_rate(){
     
         $sql = "select * from 
        ref_rent_tax_slab where sts=1 ";
        $q = $this->db->query($sql);

        return $q->result();
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

  
}

?>