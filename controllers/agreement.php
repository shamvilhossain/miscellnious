<?php
error_reporting(0);
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class agreement extends CI_Controller {

    function __construct() {
        parent::__construct();

        $this->output->set_header('Last-Modified:' . gmdate('D, d M Y H:i:s') . 'GMT');
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate');
        $this->output->set_header('Cache-Control: post-check=0, pre-check=0', false);
        $this->output->set_header('Pragma: no-cache');
        $this->load->model('agreement_model', '', TRUE);
    }

     function from($add_edit = 'add', $id = NULL, $editrow = NULL) {
        
        $agreement = array();
        $code = array();
        $this->load->model('agreement_model', '', TRUE);
        $agreement = $this->agreement_model->get_info($add_edit, $id);
        //echo $id;exit;
        if ($add_edit == 'edit' || $add_edit == 'modify' || $add_edit == 'extend'){

            $result = '';
            $pages = '';

            $landlords_result = $this->agreement_model->rent_landlords_get_info($add_edit, $id);
            $location_type_cc_result = $this->agreement_model->rent_location_type_cost_center_get_info($add_edit, $id);
            $others_rent_location_result = $this->agreement_model->others_rent_location_info($add_edit, $id);
            $rent_adjustment_result = $this->agreement_model->rent_adjustment_get_info($add_edit, $id);

            // $rent_adjustment_result=$this->agreement_model->rent_adjustment_get_info_result($add_edit,$id);
            $rent_inc_result = $this->agreement_model->rent_inc_adj_get_info($add_edit, $id);
            $file_upload_list = $this->agreement_model->rent_file_upload_get_info($add_edit, $id);
            $rent_inc_tbl = $this->increment_ajax_edit($rent_inc_result,$id,$agreement->rent_start_dt,$agreement->agree_exp_dt,$agreement->location_owner);

            $rent_adjustment_tbl = $this->yearly_adjust_amt_edit($rent_adjustment_result, $agreement);
            if($add_edit == 'edit'){$pages='agreement/pages/edit_form';}
            else if($add_edit == 'modify'){ $pages='agreement/pages/modify_form';}
            else if($add_edit == 'extend'){ $pages='agreement/pages/extend_form';}

            
            $data1 = array(
                'pages' => $pages,
                'result' => $result,
                'rent_inc_tbl' => $rent_inc_tbl,
               // 'rent_inc_start_date' => $rent_inc_result[0]->start_dt,
                'landlords_result' => $landlords_result,
                'location_type_cc_result' => $location_type_cc_result,
                'others_rent_location_result' => $others_rent_location_result,
                'rent_adjustment_result' => $rent_adjustment_result,
                'rent_adjustment_tbl' => $rent_adjustment_tbl,
                'file_upload_list' => $file_upload_list,
            );
        }else{
            $file_list = $this->agreement_model->rent_file_list();

            $data1 = array(
                'pages' => 'agreement/pages/form',
                'file_list' => $file_list,
            );
        }


        $cost_center = $this->agreement_model->get_parameter_data('cost_center', 'id');

        foreach ($cost_center as $row) {
            $nvalue = explode(",", $row->mis_codes);

            foreach ($nvalue as $value) {
                $code[$row->id][] = array(
                    'value' => $value,
                    'label' => $value
                );
            }
        }

        $data = array(
            'option' => '',
            'add_edit' => $add_edit,
            'id' => $id,
            'code' => $code,
            'agreement' => $agreement,
            'cost_center' => $this->agreement_model->get_parameter_data('cost_center', 'name', ''),
            'vendor_list' => $this->agreement_model->get_parameter_data('vendor', 'name', 'sts = 1 AND FIND_IN_SET(5, vendor_type)'),
            'location_type_list' => $this->agreement_model->get_parameter_data('ref_location_type', 'name', array('sts' => 1)),
            'branch_list' => $this->agreement_model->get_parameter_data('ref_branch', 'name', ''),

            'dept_list' => $this->agreement_model->get_parameter_data('ref_department', 'name', ''),
            'rent_document_list' => $this->agreement_model->get_parameter_data('ref_rent_document_type', 'name', ''),
            'rent_others_list' => $this->agreement_model->get_parameter_data('ref_others_rent_type', 'name', ''),
            
            'editrow' => $editrow
        );
        //$data1=array('pages'=> 'agreement/pages/form',);

        $data = array_merge($data, $data1);
        
        $this->load->view('agreement/form_layout', $data);
    }

    public function _do_upload($field_name) {
        $config['upload_path'] = './uploads/';
        $config['allowed_types'] = 'gif|jpg|png|pdf|jpeg';
        $config['encrypt_name'] = true;

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload($field_name)) {
            return false;
        }else{
            return $this->upload->data();
        }
    }

    function view($menu_group, $menu_cat) {
        $data = array(
            'menu_group' => $menu_group,
            'menu_cat' => $menu_cat,
            'pages' => 'agreement/pages/grid',
            'per_page' => $this->config->item('per_pagess')
        );
        $this->load->view('grid_layout', $data);
    }

    function grid() {
        $this->load->model('agreement_model', '', TRUE);
        $pagenum = $this->input->get('pagenum');
        $pagesize = $this->input->get('pagesize');
        $start = $pagenum * $pagesize;
        $result = $this->agreement_model->get_grid_data($this->input->get('filterscount'), $this->input->get('sortdatafield'), $this->input->get('sortorder'), $pagesize, $start);
    

        $data[] = array(
            'TotalRows' => $result['TotalRows'],
            'Rows' => $result['Rows']
        );
        echo json_encode($data);
    }

    function grid2() {
        $this->load->model('billreceive_model', '', TRUE);

        $pagenum = $this->input->get('pagenum');
        $pagesize = $this->config->item('per_pagess');
        $start = $pagenum * $pagesize;

        $result = $this->billreceive_model->get_grid_data2($this->input->get('filterscount'), $this->input->get('sortdatafield'), $this->input->get('sortorder'), $pagesize, $start);

        echo "{\"total\":" . json_encode($result['TotalRows']) . ",\"data\":" . json_encode($result['Rows']) . "}";
    }

    function yearly_adjust_amt() {

        $start = $this->input->post('start');
        $exp = $this->input->post('exp');
        $yearly_seleted_option = $this->input->post('yearly_seleted_option');
        $a = explode('/', $start);
     
        $date_day = $a[0];
        $date_month = $a[1];
        $date_year = $a[2];


        if ($this->input->post('start')){
            $start_modify = $start= $rent_start_dt = date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('start'))));
        }
        if ($this->input->post('exp')){
           $exp_modify = $exp =  $agree_exp_dt = date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('exp'))));
        }

// 8 sep start

        $start_modify_last = new DateTime($start_modify);
        $interval = new DateInterval('P1M');
        $exp_modify_last = new DateTime($exp_modify);
        $period = new DatePeriod($start_modify_last, $interval, $exp_modify_last);
        $last_iteration = iterator_count($period);
       
// 8 sep end

        $d1 = new DateTime($start);
        $d2 = new DateTime($exp);
        $diff = $d2->diff($d1);
        $year_diff = $diff->y;
        $html = '';
         //print_r($year_diff);
        // exit();

     
        $html .='<input name="last_iteration" type="hidden" value="'.$last_iteration.'" />';
        if($yearly_seleted_option=='yearly_adj_percent'){
        //$html = '';
            $html .='<table class="register-table" style="border:solid 1px black; text-align: center; width:97%; margin:20px;">';
            $html .='<tr class="headrow" style="text-align:center;font-weight:bold;background-color:#C5C5C5; border: 1px solid black;"><th>Year</th><th id="type_head">(%) / Month</th><th>Calculated Monthly Adjustment</th><th>Yearly Adjustment</th></tr>';
            $html .='<tbody id="register-table">';

            for ($i = 0; $i <= $year_diff; $i++) {


                $html.= '<script>
                jQuery("#yrly_adj_amt'.$i.'").on("blur",function(){
                    var iteration = '.$last_iteration.';
                    //alert(iteration);
                        var yearly_adj_type = jQuery("#yearly_adj_type :selected").val();
                        if(yearly_adj_type=="yearly_adj_percent"){
                             var monthly_rent = jQuery("#monthly_rent").val();
                             var total_advance = jQuery("#total_advance").val();
                             var yrly_adj_percent_amt = jQuery(this).val();

                            var calculated_monthly_adj_per = total_advance * (yrly_adj_percent_amt)/100;
                            jQuery("#cal_month_adj_amt'.$i.'").val(parseFloat(calculated_monthly_adj_per));
                            if(iteration > 12){
                                  jQuery("#cal_year_adj_amt'.$i.'").val(parseFloat(jQuery("#cal_month_adj_amt'.$i.'").val() * 12).toFixed(2));
                              }else{
                                  jQuery("#cal_year_adj_amt'.$i.'").val(parseFloat(jQuery("#cal_month_adj_amt'.$i.'").val() * iteration).toFixed(2));
                              }
                          

                        }else if(yearly_adj_type=="yearly_adj_fixed"){
                            var monthly_rent = jQuery("#monthly_rent").val();
                             var total_advance = jQuery("#total_advance").val();
                             var yrly_adj_fixed_amt = jQuery(this).val();

                            jQuery("#cal_month_adj_amt'.$i.'").val(parseFloat(yrly_adj_fixed_amt));


                        }
                    
                     
                    });

                 </script>';

                    

                if($last_iteration/12 > 1){
                    $last_iteration = $last_iteration - 12 ;
                }else{
                    $last_iteration = $last_iteration ;
                }

                            $yr_sl= $i+1;
                            $temp_data_year = $date_year;
                            $sch_start_date = $date_day . '-' . $date_month . '-' . $date_year++;
                            $sch_start_date2 = $date_day . '-' . $date_month . '-' . $date_year;
                            $sch_end_date = date('d-m-Y', strtotime($sch_start_date2 . ' -1 day'));
                            //  $exp_date= date('d-m-Y', strtotime($exp .' +0 day'));

                            $sch_end_date_t = strtotime($sch_end_date);
                            $exp_date = strtotime($exp);
                            if ($exp_date < $sch_end_date_t) {
                                $sch_end_date = date('d-m-Y', $exp_date);
                            }


                            $html .='<input name="count_year" type="hidden" value="' . $date_year . '" />'; 
                            $html .='<input name="adj_year_sl" type="hidden" value="' . $i . '" />';
                            $html .='<input name="adjust_change_sts" type="hidden" value="1" />';
                            //$html .='<input name="hide_rent" type="hidden" value="'.$monthly_rent.'" />';
                            $html .='<tr style="text-align:center;">';
                           // $html .='<td style="text-align:center;"><input type="text"  name="adj_year' . $i . '" id="adj_year" value="' . $temp_data_year . '" class="text-input-small"  readonly /> </td>';
                            $html .='<td style="text-align:center;"><input type="hidden"  name="adj_year' . $i . '" id="adj_year" value="' . $temp_data_year . '" class="text-input-small"  readonly /> <input style="text-align:center;" type="text"  name="" id="" value="Year ' .$yr_sl . ' " class="text-input-small"  readonly /> </td>';
                           // $html .='<td style="text-align:center;"><input type="text"  name="adj_year' . $i . '" id="adj_year" value="Year ' .$yr_sl . ' " class="text-input-small"  readonly /> </td>';

                            $html .='<td style="text-align:center;"><input type="text"  name="yrly_adj_amt' . $i . '" id="yrly_adj_amt'.$i.'"  class="text-input-small number" value=""  /></td>';
                            
                            $html .='<td class="abc" style="text-align:center;"><input style="text-align:right;" type="text"  name="cal_month_adj_amt' . $i . '" id="cal_month_adj_amt'.$i.'"  class="text-input-small number" value=""  readonly/></td>';
                            $html .='<td class="abc" style="text-align:center;"><input style="text-align:right;" type="text"  name="cal_year_adj_amt' . $i . '" id="cal_year_adj_amt'.$i.'"  class="text-input-small number" value=""  readonly/></td>';

                            $html .='</tr>';
            }

                        $html.= '<input type="hidden"  name="" id="yr_row_count" value="'.$yr_sl.'" class="text-input-small"  readonly /> ';
                        $html .='</tbody></table>';

        }elseif($yearly_seleted_option=='yearly_adj_fixed'){

                $html = '';
                $html .='<table class="register-table" style="border:solid 1px black; text-align: center; width:97%; margin:20px;">';
                $html .='<tr class="headrow" style="text-align:center;font-weight:bold;background-color:#C5C5C5; border: 1px solid black;"><th>Year</th><th id="type_head">Amount / Month</th><th>Yearly Adjustment</th></tr>';
                $html .='<tbody id="register-table">';

            for ($i = 0; $i <= $year_diff; $i++) {

                $html.= '<script>
                            jQuery("#yrly_adj_amt'.$i.'").on("blur",function(){
                                var iteration = '.$last_iteration.';
                                //alert(iteration);
                               var yearly_adj_type = jQuery("#yearly_adj_type :selected").val();
                               if(yearly_adj_type=="yearly_adj_fixed"){
                                    var monthly_rent = jQuery("#monthly_rent").val();
                                    var total_advance = jQuery("#total_advance").val();
                                    var yrly_adj_fixed_amt = jQuery(this).val();

                                    if(iteration > 12){
                                          jQuery("#cal_yrly_adj_amt'.$i.'").val(parseFloat(jQuery("#yrly_adj_amt'.$i.'").val() * 12).toFixed(2));
                                    }else{
                                          jQuery("#cal_yrly_adj_amt'.$i.'").val(parseFloat(jQuery("#yrly_adj_amt'.$i.'").val() * iteration).toFixed(2));
                                    }

                                }
                            
                            });

                        </script>';

                        if($last_iteration/12 > 1){
                            $last_iteration = $last_iteration - 12 ;
                        }else{
                            $last_iteration = $last_iteration ;
                        }
               
                        $yr_sl= $i+1;
                        $temp_data_year = $date_year;
                        $sch_start_date = $date_day . '-' . $date_month . '-' . $date_year++;
                        $sch_start_date2 = $date_day . '-' . $date_month . '-' . $date_year;
                        //  $sch_start_date1= $date_year.'-'.$date_month.'-'.$date_day;

                        $sch_end_date = date('d-m-Y', strtotime($sch_start_date2 . ' -1 day'));
                        //  $exp_date= date('d-m-Y', strtotime($exp .' +0 day'));

                        $sch_end_date_t = strtotime($sch_end_date);
                        $exp_date = strtotime($exp);
                        if ($exp_date < $sch_end_date_t) {
                            $sch_end_date = date('d-m-Y', $exp_date);
                        }


                        $html.= '';
                        $html .='<input name="count_year" type="hidden" value="' . $date_year . '" />'; 
                        $html .='<input name="adj_year_sl" type="hidden" value="' . $i . '" />';
                        $html .='<input name="adjust_change_sts" type="hidden" value="1" />';
                        //$html .='<input name="hide_rent" type="hidden" value="'.$monthly_rent.'" />';
                        $html .='<tr style="text-align:center;">';
                       // $html .='<td style="text-align:center;"><input type="text"  name="adj_year' . $i . '" id="adj_year" value="' . $temp_data_year . '" class="text-input-small"  readonly /> </td>';
                        $html .='<td style="text-align:center;"><input type="hidden"  name="adj_year' . $i . '" id="adj_year" value="' . $temp_data_year . '" class="text-input-small"  readonly /> <input style="text-align:center;" type="text"  name="" id="" value="Year ' .$yr_sl . ' " class="text-input-small"  readonly /> </td>';
                        $html .='<td style="text-align:center;"><input type="text"  name="yrly_adj_amt' . $i . '" id="yrly_adj_amt'.$i.'"  class="text-input-small number" value=""  /></td>';
                        $html .='<td style="text-align:center;"><input style="text-align:right;" type="text"  name="cal_yrly_adj_amt' . $i . '" id="cal_yrly_adj_amt'.$i.'"  class="text-input-small number" value=""  readonly/></td>';
                        
                       // $html .='<td class="abc" style="text-align:center;"><input type="text"  name="cal_month_adj_amt' . $i . '" id="cal_month_adj_amt'.$i.'"  class="text-input-small number" value=""  readonly/></td>';

                        $html .='</tr>';
            }

                    $html.= '<input type="hidden"  name="" id="yr_row_count" value="'.$yr_sl.'" class="text-input-small"  readonly /> ';
                    $html .='</tbody></table>';

        }

        echo $html;
        //  exit();
}



function yearly_adjust_amt_edit($rent_adjustment_result, $agreement){

        $html = '';
        if($agreement->location_owner=='rented'){

        $total_advance = $agreement->total_advance;
        $start = $agreement->rent_start_dt;
        $exp =  $agreement->agree_exp_dt;
        $yearly_seleted_option = $rent_adjustment_result[0]->percent_dir_type;
        //  $monthly_rent= $this->input->post('monthly_rent');
        $a = explode('-', $start);
       

        $date_day = $a[2];
        $date_month = $a[1];
        $date_year = $a[0];

    if ($this->input->post('start')) {
            $start= $rent_start_dt = date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('start'))));
        }
        if ($this->input->post('exp')) {
           $exp=  $agree_exp_dt = date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('exp'))));
        }

        $start_modify_last = new DateTime($start);
        $interval = new DateInterval('P1M');
        $exp_modify_last = new DateTime($exp);
        $period = new DatePeriod($start_modify_last, $interval, $exp_modify_last);
        $last_iteration = iterator_count($period);


        $d1 = new DateTime($start);
        $d2 = new DateTime($exp);
        $diff = $d2->diff($d1);
        $year_diff = $diff->y;
        
        // print_r($year_diff);
        // exit();
if($yearly_seleted_option=='yearly_adj_percent'){
        $html = '';
        $html .='<table class="register-table" style="border:solid 1px black; text-align: center; width:97%; margin:20px;">';
        $html .='<tr class="headrow" style="text-align:center;font-weight:bold;background-color:#C5C5C5; border: 1px solid black;"><th>Year</th><th id="type_head">(%) / Month</th><th>Calculated Monthly Adjustment</th><th>Yearly Adjustment</th></tr>';
        $html .='<tbody id="register-table">';

//for ($i = 0; $i <= $year_diff; $i++) {
 //  foreach ($rent_adjustment_result as $single_rent_adjustment_info) {
 $i = 0;  
 $cal_yrly_adj_amt_val=0;     
foreach ($rent_adjustment_result as $single_rent_adjustment_info) {

            $iteration= $last_iteration;
            if($last_iteration/12 > 1){
                $last_iteration = $last_iteration - 12 ;
            }else{
                $last_iteration = $last_iteration ;
            }

            $cal_month_adj_amt =   ($total_advance * $single_rent_adjustment_info->adv_incr_year_val)/100;


            $yr_sl= $i+1;
            $temp_data_year = $date_year;
            $sch_start_date = $date_day . '-' . $date_month . '-' . $date_year++;

            $sch_start_date2 = $date_day . '-' . $date_month . '-' . $date_year;
            //  $sch_start_date1= $date_year.'-'.$date_month.'-'.$date_day;

            $sch_end_date = date('d-m-Y', strtotime($sch_start_date2 . ' -1 day'));
            //  $exp_date= date('d-m-Y', strtotime($exp .' +0 day'));

            $sch_end_date_t = strtotime($sch_end_date);
            $exp_date = strtotime($exp);
            if ($exp_date < $sch_end_date_t) {
                $sch_end_date = date('d-m-Y', $exp_date);
            }

            // 20 oct 
            if($iteration > 12){
                  //jQuery("#cal_yrly_adj_amt"+i).val(parseFloat(jQuery("#yrly_adj_amt"+i).val() * 12).toFixed(2));
                  $cal_yrly_adj_amt_val= $cal_month_adj_amt * 12;
            }else{
                  //jQuery("#cal_yrly_adj_amt"+i).val(parseFloat(jQuery("#yrly_adj_amt"+i).val() * iteration).toFixed(2));
                  $cal_yrly_adj_amt_val= $cal_month_adj_amt * $iteration;
            }


            $html.= '';
            $html .='<input name="count_year" type="hidden" value="' . $date_year . '" />'; 
            $html .='<input name="adj_year_sl" type="hidden" value="' . $i . '" />';
            $html .='<input name="adjust_change_sts" type="hidden" value="1" />';
            //$html .='<input name="hide_rent" type="hidden" value="'.$monthly_rent.'" />';
            $html .='<tr style="text-align:center;">';
           // $html .='<td style="text-align:center;"><input type="text"  name="adj_year' . $i . '" id="adj_year" value="' . $temp_data_year . '" class="text-input-small"  readonly /> </td>';
            $html .='<td style="text-align:center;"><input type="hidden"  name="adj_year'.$i.'" id="adj_year" value="' . $temp_data_year . '" class="text-input-small"  readonly /> <input type="text"  name="" id="" value="Year ' .$yr_sl . ' " class="text-input-small"  readonly /> </td>';
           // $html .='<td style="text-align:center;"><input type="text"  name="adj_year' . $i . '" id="adj_year" value="Year ' .$yr_sl . ' " class="text-input-small"  readonly /> </td>';
            $html .='<td style="text-align:center;"><input type="text"  name="yrly_adj_amt'.$i.'" id="yrly_adj_amt'.$i.'"  class="text-input-small number" value=" '.$single_rent_adjustment_info->adv_incr_year_val.' "  /></td>';
            
            $html .='<td class="abc" style="text-align:center;"><input type="text"  name="cal_month_adj_amt' . $i . '" id="cal_month_adj_amt'.$i.'"  class="text-input-small number" value="'.$cal_month_adj_amt .'"  readonly/></td>';
            $html .='<td class="abc" style="text-align:center;"><input style="text-align:right;" type="text"  name="cal_year_adj_amt' . $i . '" id="cal_year_adj_amt'.$i.'"  class="text-input-small number" value="'.$cal_yrly_adj_amt_val.'"  readonly/></td>';

            $html .='</tr>';

			$html.= '<script>
			       
			         adj_edit_jquery('.$i.');
			         adj_edit_jquery_for_percent_yr('.$i.','.$iteration.');
			        </script>';

            $i++;

        }
        $html.= '<input type="hidden"  name="" id="yr_row_count" value="'.$yr_sl.'" class="text-input-small"  readonly /> ';
        $html .='</tbody></table>';

}elseif($yearly_seleted_option=='yearly_adj_fixed'){

        $html = '';
        $html .='<table class="register-table" style="border:solid 1px black; text-align: center; width:97%; margin:20px;">';
        $html .='<tr class="headrow" style="text-align:center;font-weight:bold;background-color:#C5C5C5; border: 1px solid black;"><th>Year</th><th id="type_head">Amount / Month</th><th>Yearly Adjustment</th></tr>';
        $html .='<tbody id="register-table">';

//for ($i = 0; $i <= $year_diff; $i++) {
 $i = 0;       
 $cal_yrly_adj_amt_val = 0;       
foreach ($rent_adjustment_result as $single_rent_adjustment_info) {

   // 20 oct start

            $iteration= $last_iteration;
            if($last_iteration/12 > 1){
                $last_iteration = $last_iteration - 12 ;
            }else{
                $last_iteration = $last_iteration ;
            }
   
   // 20 oct end
            $yr_sl= $i+1;
            $temp_data_year = $date_year;
            $sch_start_date = $date_day . '-' . $date_month . '-' . $date_year++;

            $sch_start_date2 = $date_day . '-' . $date_month . '-' . $date_year;
            $sch_end_date = date('d-m-Y', strtotime($sch_start_date2 . ' -1 day'));
            //  $exp_date= date('d-m-Y', strtotime($exp .' +0 day'));

            $sch_end_date_t = strtotime($sch_end_date);
            $exp_date = strtotime($exp);
            if ($exp_date < $sch_end_date_t) {
                $sch_end_date = date('d-m-Y', $exp_date);
            }
// 20 oct 
            if($iteration > 12){
                  //jQuery("#cal_yrly_adj_amt"+i).val(parseFloat(jQuery("#yrly_adj_amt"+i).val() * 12).toFixed(2));
                  $cal_yrly_adj_amt_val= $single_rent_adjustment_info->adv_incr_year_val * 12;
            }else{
                  //jQuery("#cal_yrly_adj_amt"+i).val(parseFloat(jQuery("#yrly_adj_amt"+i).val() * iteration).toFixed(2));
                  $cal_yrly_adj_amt_val= $single_rent_adjustment_info->adv_incr_year_val * $iteration;
            }


            $html.= '';
            $html .='<input name="count_year" type="hidden" value="' . $date_year . '" />'; 
            $html .='<input name="adj_year_sl" type="hidden" value="' . $i . '" />';
            $html .='<input name="adjust_change_sts" type="hidden" value="1" />';
            //$html .='<input name="hide_rent" type="hidden" value="'.$monthly_rent.'" />';
            $html .='<tr style="text-align:center;">';
           // $html .='<td style="text-align:center;"><input type="text"  name="adj_year' . $i . '" id="adj_year" value="' . $temp_data_year . '" class="text-input-small"  readonly /> </td>';
            $html .='<td style="text-align:center;"><input type="hidden"  name="adj_year' . $i . '" id="adj_year" value="' . $temp_data_year . '" class="text-input-small"  readonly /> <input type="text"  name="" id="" value="Year ' .$yr_sl . ' " class="text-input-small"  readonly /> </td>';
            $html .='<td style="text-align:center;"><input type="text"  name="yrly_adj_amt' . $i . '" id="yrly_adj_amt'.$i.'"  class="text-input-small number" value=" '.$single_rent_adjustment_info->adv_incr_year_val.'"  /></td>';
            $html .='<td style="text-align:center;"><input style="text-align:right;" type="text"  name="cal_yrly_adj_amt'.$i.'" id="cal_yrly_adj_amt'.$i.'"  class="text-input-small number" value="'.$cal_yrly_adj_amt_val.'"  readonly/></td>';
            
           // $html .='<td class="abc" style="text-align:center;"><input type="text"  name="cal_month_adj_amt' . $i . '" id="cal_month_adj_amt'.$i.'"  class="text-input-small number" value=""  readonly/></td>';

            $html .='</tr>';
            $html.= '<script>
       
                adj_edit_jquery_for_fixed_yr('.$i.','.$iteration.');
          

            </script>';
             $i++;
        }
        $html.= '<input type="hidden"  name="" id="yr_row_count" value="'.$yr_sl.'" class="text-input-small"  readonly /> ';
        $html .='</tbody></table>';
}


// $html.= '<input type="hidden"  name="" id="yr_row_count" value="'.$yr_sl.'" class="text-input-small"  readonly /> ';
//         $html .='</tbody></table>';

        return $html;
        }
    }



        function yearly_adjust_amt_edit_old($rent_adjustment_result, $agreement) {


        $start = $agreement->rent_start_dt;
        $exp = $agreement->agree_exp_dt;
        //  $monthly_rent= $this->input->post('monthly_rent');
        $a = explode('-', $start);
        // print_r($a);
        //       exit();

        $date_day = $a[2];
        $date_month = $a[1];
        $date_year = $a[0];

        $d1 = new DateTime($start);
        $d2 = new DateTime($exp);
        $diff = $d2->diff($d1);
        $year_diff = $diff->y;
        // print_r($year_diff);
        // exit();

        $html = '';
        $html .='<table class="register-table" style="border:solid 1px black; text-align: center; width:97%; margin:20px;">';
        $html .='<tr><th>Year</th><th id="type_head">Amount</th></tr>';
        $html .='<tbody id="register-table">';
        $i = 0;
        //for($i=0;$i<=$year_diff;$i++){
        foreach ($rent_adjustment_result as $single_rent_adjustment_info) {
            $temp_data_year = $date_year;
            $sch_start_date = $date_day . '-' . $date_month . '-' . $date_year++;

            $sch_start_date2 = $date_day . '-' . $date_month . '-' . $date_year;
            //$sch_start_date1= $date_year.'-'.$date_month.'-'.$date_day;

            $sch_end_date = date('d-m-Y', strtotime($sch_start_date2 . ' -1 day'));
            //  $exp_date= date('d-m-Y', strtotime($exp .' +0 day'));

            $sch_end_date_t = strtotime($sch_end_date);
            $exp_date = strtotime($exp);
            if ($exp_date < $sch_end_date_t) {
                $sch_end_date = date('d-m-Y', $exp_date);
            }


            $html.= '';
            $html .='<input name="count_year" type="hidden" value="' . $date_year . '" />';
            $html .='<input name="adj_year_sl" type="hidden" value="' . $i . '" />';
            $html .='<input name="adjust_change_sts" type="hidden" value="0" />';
            //$html .='<input name="hide_rent" type="hidden" value="'.$monthly_rent.'" />';
            $html .='<tr>';
            $html .='<td style="text-align:center;"><input type="text"  name="adj_year' . $i . '" id="adj_year" value="' . $temp_data_year . '" class="text-input-small"  readonly /> </td>';

            $html .='<td style="text-align:center;"><input type="text"  name="yrly_adj_amt' . $i . '" id="yrly_adj_amt"  class="text-input-small number" value="' . $single_rent_adjustment_info->adv_incr_year_val . '"  /></td>';


            $html .='</tr>';
            $i++;
        }

        $html .='</tbody></table>';

        return $html;
        //  exit();
    }

    

    function generate_schedule_data($rent_agree_id,$add_edit) {
    	//$add_edit='add';
        if($add_edit!='modify'){
            $this->db->query("Delete from rent_ind_schedule where rent_agree_id = $rent_agree_id ");
        }else{
        	$paid_count=  $this->agreement_model->count_paid_schedule($rent_agree_id);  
        	$unpaid_id_list=  $this->agreement_model->get_sche_unpaid_id_list($rent_agree_id);  
            $id_list = explode(",",$unpaid_id_list->unpaid_id_list);
        }
        
        $rent_agreement_row_data = $this->agreement_model->get_add_action_data_new($rent_agree_id);
            if($rent_agreement_row_data->adjust_adv_type !=4){
                $rent_adjust_data = $this->agreement_model->single_rent_adjustment_get_info('', $rent_agree_id);
            }else{
                $rent_adjust_data = $this->agreement_model->rent_adjustment_get_info('', $rent_agree_id);
            }
        $tax_info = $this->agreement_model->single_rent_tax_info('', 1);
        $vat_info = $this->agreement_model->single_rent_vat_info('', 1);
        $frac = $start1 = $rent_agreement_row_data->rent_start_dt;
        $frac_for_last  = $rent_agreement_row_data->agree_exp_dt;
        $exp1 = $rent_agreement_row_data->agree_exp_dt;
 
        $start = $month = strtotime($start1);
        $end = strtotime($exp1);
        $start = new DateTime($start1);
        $interval = new DateInterval('P1M');
        $end = new DateTime($exp1);
        $end1 = new DateTime($exp1);
        $end1->modify('last day of this month');
        $period = new DatePeriod($start, $interval, $end1);
        $last_iteration = iterator_count($period);
        //print_r($period);exit;
       


        $a = explode('-', $start1);
        $date_year = $a[0];
       
        // checking if it is own or rented 
        $indx=0;
        if($rent_agreement_row_data->location_owner=='rented'){ 
            
            //print_r($id_list);exit;
            $rent_agree_ref = $rent_agreement_row_data->agreement_ref_no;
            $advance_amount = $rent_agreement_row_data->total_advance;
            $paid_advance_amount = $rent_agreement_row_data->total_advance_paid;
            $monthly_rent = $rent_agreement_row_data->monthly_rent;
            $others_rent = 0;
            
            $vat_amount = $vat_info->vat_percentage;
            $tax_amount = $tax_info->tax_amount;
            $calculated_tax = $monthly_rent * ($tax_amount / 100);
        
            $unadjust = $paid_advance_amount;
        
            $i = 0;
            $start_year = $date_year - 1;
        

            foreach ($period as $key => $dt) {
            //echo $dt->format('d-m-Y').'<br/>';
                //$schedule_strat_dt = $payment_date = $dt->format('d-M-y') . PHP_EOL;
                $schedule_strat_dt = $payment_date = $dt->format('Y-m-d') . PHP_EOL;
                $total_day_in_month = $dt->format('t');
                $adjustment_amount =0;

                if ($i == 0) {
                    $fraction_day = date('t', strtotime($frac)) - date('j', strtotime($frac));
                    $fraction_day_percent = (($fraction_day + 1) * 100) / $total_day_in_month;
                    $first_fraction_day_percent = (($fraction_day + 1) * 100) / $total_day_in_month;
                } 
                elseif($i == $last_iteration -1){
                    $fraction_day = date('t', strtotime($frac_for_last)) - date('j', strtotime($frac_for_last));
                    $fraction_day_percent = (($total_day_in_month - $fraction_day) * 100) / $total_day_in_month;

                }else {
                    $frac = $dt->format('Y-m-01') . PHP_EOL;
                    $fraction_day = date('t', strtotime($frac)) - date('j', strtotime($frac));
                    $fraction_day_percent = (($fraction_day + 1) * 100) / $total_day_in_month;
                }
                
                if($rent_agreement_row_data->point_of_payment=='cm'){
                    $maturity_dt = $dt->format('Y-m-01');
                    $Schedule_end_dt = $dt->format('Y-m-t');
            
                }else{

                    $first_day_of_next_month = $maturity_dt = date('Y-m-d', strtotime($dt->format('Y-m-01'). ' +1 month'));
                    $data4 = $Schedule_end_dt = $dt->format('Y-m-t');
                    //$date4 = $Schedule_end_dt = date('Y-m-t', strtotime($dt->format('Y-m-01'). ' +1 month'));

                }
            
                if ($i % 12 == 0) {
						
					$year_sts='';
                	if($i == $last_iteration - 1 ){  // last month
                		$start_year;
                		$year_sts='last';
                		
                	}else{
                		$start_year++;
                		//echo 'not last';
                	}

                    //$start_year++;
                    $new_monthly_rent = $this->agreement_model->get_monthly_rent_per_year($start_year, $rent_agree_id);
    				
	    				if($year_sts=='last'){
	    					$remarks = '';
	    				}else{
		                    if ($new_monthly_rent->rent_amount_type == '') {
		                        $remarks = '';
		                    } else if ($new_monthly_rent->rent_amount_type == 'per_rent') {
		                        $remarks = $new_monthly_rent->rent_amount_val . ' % increment';
		                    } else {
		                        $remarks = $new_monthly_rent->rent_amount_val . ' Taka increment';
		                    }
	                    }

                     // 11 march start
                        if($rent_agreement_row_data->increment_type==5){

							$mature_ts = strtotime($maturity_dt);
						  	$incr_start = strtotime($new_monthly_rent->start_dt);
						  	$incr_end = strtotime($new_monthly_rent->end_dt);

						  	if(($mature_ts >= $incr_start) && ($mature_ts <= $incr_end)){
						  		$calculated_monthly_rent = $new_monthly_rent->cal_rent_val * ($fraction_day_percent / 100);
						  	}
						  	else{
						  		$remarks = '';
						  		if ($new_monthly_rent->rent_amount_type == 'dir_rent') {
						  			$calculated_monthly_rent = ($new_monthly_rent->cal_rent_val * ($fraction_day_percent / 100))- $new_monthly_rent->rent_amount_val;
						  		}else if ($new_monthly_rent->rent_amount_type == 'per_rent') {
						  			$temp_amt = (($new_monthly_rent->cal_rent_val * ($fraction_day_percent / 100))* 100)/ (100+ $new_monthly_rent->rent_amount_val)  ;
						  			$calculated_monthly_rent =  $temp_amt;
						  		}else{
						  			$calculated_monthly_rent = $new_monthly_rent->cal_rent_val * ($fraction_day_percent / 100);
						  		} 	
						  	}
						}else{  // normal increment (1-4)

							$calculated_monthly_rent = $new_monthly_rent->cal_rent_val * ($fraction_day_percent / 100);
						}  	
                        // 11 march end
                    //$calculated_monthly_rent = $new_monthly_rent->cal_rent_val * ($fraction_day_percent / 100);
                 

                    if($first_fraction_day_percent < 100){
                        if($new_monthly_rent->rent_amount_type=='per_rent'){
                            $temp_rent_amount= ($new_monthly_rent->cal_rent_val * 100)/($new_monthly_rent->rent_amount_val + 100);
                           
                            $new_per=$new_monthly_rent->rent_amount_val * ($first_fraction_day_percent / 100);
                            $calculated_monthly_rent=$temp_rent_amount + ($temp_rent_amount * ($new_per / 100));

                        }else if($new_monthly_rent->rent_amount_type=='dir_rent'){
                             $temp_rent_amount= ($new_monthly_rent->cal_rent_val - $new_monthly_rent->rent_amount_val);
                             $new_dir= $new_monthly_rent->rent_amount_val * ($first_fraction_day_percent / 100);
                             $calculated_monthly_rent= $temp_rent_amount + $new_dir;

                        }else{
                            $calculated_monthly_rent = $new_monthly_rent->cal_rent_val * ($fraction_day_percent / 100);
                        }
                    }

            // adjust data
                    if($rent_agreement_row_data->adjust_adv_type ==4){
                       $new_monthly_adjustment_info = $this->agreement_model->get_adjustment_amount_per_year($start_year, $rent_agree_id); 
                        
                        if ($new_monthly_adjustment_info->percent_dir_type == 'yearly_adj_fixed') {
                            $adjustment_amount_1 = $new_monthly_adjustment_info->adv_incr_year_val;
                            $adjustment_amount = $adjustment_amount_1 * ($fraction_day_percent / 100);
                        } else {
                            $adjustment_amount_1 = $advance_amount * ($new_monthly_adjustment_info->adv_incr_year_val/100);
                            $adjustment_amount = $adjustment_amount_1 * ($fraction_day_percent / 100);
                            
                        }

                    }
     				$remarks_count = 0;
                    }else{

                        $new_monthly_rent = $this->agreement_model->get_monthly_rent_per_year($start_year, $rent_agree_id);
                        $remarks = '';
                        
                        // 11 march start
                        if($rent_agreement_row_data->increment_type==5){

	                        $date1 = new DateTime($maturity_dt);
							$date1->modify('last day of this month');
							$last_day_of_month =  $date1->format('Y-m-d');

							$mature_ts = strtotime($maturity_dt);
						  	$end_ts = strtotime($last_day_of_month);
						  	$incr_start = strtotime($new_monthly_rent->start_dt);
						  	$incr_end = strtotime($new_monthly_rent->end_dt);

						  	if(($mature_ts >= $incr_start) && ($mature_ts <= $incr_end)){
						  		if($remarks_count < 1 ){ 
						  			if ($new_monthly_rent->rent_amount_type == '') {
	                        		$remarks = '';
				                    } else if ($new_monthly_rent->rent_amount_type == 'per_rent') {
				                        $remarks = $new_monthly_rent->rent_amount_val . ' % increment';
				                    } else {
				                        $remarks = $new_monthly_rent->rent_amount_val . ' tk increment';
				                    }
						  		}else{ $remarks = ''; }
						  		
			                    ++$remarks_count;

						  		$calculated_monthly_rent = $new_monthly_rent->cal_rent_val * ($fraction_day_percent / 100);
						  	}
						  	else{
						  		// here
						  		if ($new_monthly_rent->rent_amount_type == 'dir_rent') {
						  			$calculated_monthly_rent = ($new_monthly_rent->cal_rent_val * ($fraction_day_percent / 100))- $new_monthly_rent->rent_amount_val;
						  		}else if ($new_monthly_rent->rent_amount_type == 'per_rent') {
						  			$temp_amt = (($new_monthly_rent->cal_rent_val * ($fraction_day_percent / 100))* 100)/ (100+ $new_monthly_rent->rent_amount_val)  ;
						  			$calculated_monthly_rent =  $temp_amt;
						  		}else{
						  			$calculated_monthly_rent = $new_monthly_rent->cal_rent_val * ($fraction_day_percent / 100);
						  		} 	
						  	}
						}else{  // normal increment (1-4)

							$calculated_monthly_rent = $new_monthly_rent->cal_rent_val * ($fraction_day_percent / 100);
						}  	
                        // 11 march end


                        if($rent_agreement_row_data->adjust_adv_type ==4){
                           $new_monthly_adjustment_info = $this->agreement_model->get_adjustment_amount_per_year($start_year, $rent_agree_id); 
                            // print_r($new_monthly_adjustment_info);
                            // exit();
                            if ($new_monthly_adjustment_info->percent_dir_type == 'yearly_adj_fixed') {
                                $adjustment_amount_1 = $new_monthly_adjustment_info->adv_incr_year_val;
                                $adjustment_amount = $adjustment_amount_1 * ($fraction_day_percent / 100);
                            } else {
                              $adjustment_amount_1 = $advance_amount * ($new_monthly_adjustment_info->adv_incr_year_val/100);
                              $adjustment_amount = $adjustment_amount_1 * ($fraction_day_percent / 100);
                            }

                        }
                    }

                    if($rent_agreement_row_data->adjust_adv_type !=4){
                

                        if($rent_agreement_row_data->adjust_adv_type ==3){
                            // % of total advance amount
                            if($rent_adjust_data->percent_dir_type=='percent_total_amt'){
                               $adjustment_amount_1 = $rent_agreement_row_data->total_advance * ($rent_adjust_data->percent_dir_val/100);
                               $adjustment_amount = $adjustment_amount_1 * ($fraction_day_percent / 100);

                            }else{
                                // % of paid amount
                                if($rent_agreement_row_data->total_advance_paid !=0.00){
                                    $adjustment_amount_1 = $rent_agreement_row_data->total_advance_paid * ($rent_adjust_data->percent_dir_val/100);
                                    $adjustment_amount = $adjustment_amount_1 * ($fraction_day_percent / 100);
                                 }
                                 else{
                                     $adjustment_amount = 0;
                                 }
                            }

                        }else{

                            $adjustment_amount_1 = $rent_adjust_data->percent_dir_val;
                            $adjustment_amount = $adjustment_amount_1 * ($fraction_day_percent / 100);
                        }
                    }      
  // ..............
                    $net_payment = ($monthly_rent + $others_rent) - ($adjustment_amount + $calculated_tax );
                    $unadjusted_adv_rent = ($monthly_rent + $others_rent) - ($adjustment_amount + $calculated_tax );

                    $others_id = $new_monthly_rent->others_id_list;
                    $others_amount = $new_monthly_rent->cal_others_val;

                    $others_id_array = explode(',', $others_id);
                    $others_amount_array = explode(',', $others_amount);

                    $others_car = '';
                    $others_generator = '';
                    $others_water = '';
                    $others_gas = '';
                    $others_service = '';

                    for ($j = 0; $j < count($others_id_array); $j++) {
                        if ($others_id_array[$j] == 'Car Parking') {
                            $others_car = $others_amount_array[$j];
                        }
                        if ($others_id_array[$j] == 'Generator Space') {
                            $others_generator = $others_amount_array[$j];
                        }
                        if ($others_id_array[$j] == 'Water Supply') {
                            $others_water = $others_amount_array[$j];
                        }
                        if ($others_id_array[$j] == 'Gas Bill') {
                            $others_gas = $others_amount_array[$j];
                        }
                        if ($others_id_array[$j] == 'Service Charge') {
                            $others_service = $others_amount_array[$j];
                        }
                    }

                    $others = $new_monthly_rent->cal_others_val;
                    // print_r($others);
                    // echo '<br />';
                    $total_others = 0;
                    $strings_array = explode(',', $others);
                    foreach ($strings_array as $each_number) {
                        $total_others += $each_number;
                    }

                    $total_others = $total_others * ($fraction_day_percent / 100);
                    //echo $total_others;
                    $new_calculated_vat = ($calculated_monthly_rent + $total_others )* ($vat_amount / 100);
                    $new_calculated_tax = ($calculated_monthly_rent + $total_others )* ($tax_amount / 100);
                    $new_net_payment = ($new_monthly_rent->cal_rent_val + $total_others) - ($adjustment_amount + $new_calculated_tax );
                    $unadjust = $unadjust - $adjustment_amount;
            
                    if($unadjust < 0 ){$unadjust=0;}

                    // 31 may
                    $adjustment_amount_after_agree = 0;
                    $unadjust_after_agree = 0;

                    $schedule_data = array(
                        'rent_agree_id' => $rent_agree_id
                        , 'rent_agree_ref' => $rent_agree_ref
                        , 'maturity_dt' => $maturity_dt
                        , 'schedule_strat_dt' => $schedule_strat_dt
                        , 'Schedule_end_dt' => $Schedule_end_dt
                        , 'rent_fraction_day' => $fraction_day_percent
                        // , 'advence_rent_amount' => $paid_advance_amount
                        // , 'hidden_adjustment_adv' => $adjustment_amount
                        // , 'adjustment_adv' => $adjustment_amount_after_agree
                        // , 'adjust_sec_deposit' => 0
                        , 'monthly_rent_amount' => $calculated_monthly_rent
                        , 'others_car' => $others_car
                        , 'others_gas' => $others_gas
                        , 'others_generator' => $others_generator
                        , 'others_service' => $others_service
                        , 'others_water' => $others_water
                        , 'total_others_amount' => $total_others
              			//,'unadjusted_adv_rent' => $unadjust_after_agree               
                        , 'remarks' => $remarks
                        //, 'paid_sts' => 'unpaid'
                    );
                if($add_edit!='modify'){
                    $schedule_data_1 = array(
	                    	 'advence_rent_amount' => $paid_advance_amount
	                        ,'hidden_adjustment_adv' => $adjustment_amount
	                        ,'adjustment_adv' => $adjustment_amount_after_agree
	                        ,'adjust_sec_deposit' => 0
                            ,'unadjusted_adv_rent' => $unadjust_after_agree  
	                        ,'paid_sts' => 'unpaid'
                        );
                    $this->db->insert('rent_ind_schedule', array_merge($schedule_data,$schedule_data_1));
                    //echo $this->db->last_query();
                }else{
                    // 16 april
                    if($i < $paid_count->paid_count){

                    }else{
                        // $schedule_data_2 = array(
                        //      'advence_rent_amount' => $paid_advance_amount
                        //     ,'hidden_adjustment_adv' => $adjustment_amount
                        //     ,'adjustment_adv' => $adjustment_amount_after_agree
                        //     ,'adjust_sec_deposit' => 0
                        //     ,'unadjusted_adv_rent' => $unadjust_after_agree
                        // );

                        $this->db->where('id', $id_list[$indx]);
                        $this->db->update('rent_ind_schedule', $schedule_data);
                        
	                    $indx++;
	                   // echo $this->db->last_query();
	                    
                    }
                   
                }
                

                $i++;

            }
            if($add_edit=='modify' && $rent_agreement_row_data->adjust_adv_type ==2){
            	$this->calculate_advance($rent_agree_id);
            }
            
           // exit;
        }
    }

    function calculate_advance($rent_agre_id) {

    	$sql = "select * from rent_agr_adv_adjustment_history where rent_agre_id=$rent_agre_id and adjustment_type=2 and sts=1";
                    $q = $this->db->query($sql);
                    $percent_paid_data = $q->row();
    	$this->db->query("update rent_ind_schedule set hidden_adjustment_adv = $percent_paid_data->percent_dir_val,
    												adjustment_adv = 0,
    												unadjusted_adv_rent=0
		                                            
		                                           WHERE rent_agree_id = $rent_agre_id and paid_sts ='unpaid' ");


    	$sql = "select * from rent_ind_schedule where rent_agree_id=$rent_agre_id and paid_sts='unpaid' ";

                    $q = $this->db->query($sql);
                    $unpaid_rent = $q->result();
                    $unpaid_rent_count = count($unpaid_rent);

                    $paid_adjustment_adv = $this->db->query("SELECT SUM(adjustment_adv) AS paid_adjustment_adv FROM rent_ind_schedule WHERE rent_agree_id=$rent_agre_id  AND paid_sts='paid' ");
                    $paid_adjustment_adv_amount = $paid_adjustment_adv->row(); //0

                    $total_advance_paid_res = $this->db->query("select location_name,agree_cost_center,total_advance_paid,adjust_adv_type from rent_agreement where id=$rent_agre_id");
                    $total_advance_paid = $total_advance_paid_res->row();
                    $total_advance_paid_already = $total_advance_paid->total_advance_paid; //4000
                    $temp= $total_advance_paid_already;
            

                    $calculated_adv_amount = number_format((float) $advance_amount / $unpaid_rent_count, 2, '.', '');
                    $this->db->query("update rent_ind_schedule set advence_rent_amount=$total_advance_paid->total_advance_paid
                                WHERE rent_agree_id=$rent_agre_id ");
                                
                    $counter = 0;
                    $temp2=0;
                    $temp1=0;
                    // check if there any entry paid or not       
                    if ($paid_adjustment_adv_amount->paid_adjustment_adv != ''){ //paid ase
                    
                        $total_paid_adjustment_amount = $paid_adjustment_adv_amount->paid_adjustment_adv;
                        $rest_advance_amount = $total_advance_paid_already - $total_paid_adjustment_amount;
                        $rest_advance_unpaid = $total_advance_paid_already - $total_paid_adjustment_amount;
                        
                        foreach ($unpaid_rent as $single_unpaid_rent) {
                           if($total_advance_paid->adjust_adv_type==0 ||  $total_advance_paid->adjust_adv_type==1){
                                $partial_amount= 0;
                           }else{
                                $partial_amount= ($rest_advance_amount % $single_unpaid_rent->hidden_adjustment_adv);
                           }
                            

                            $temp1= $rest_advance_amount;
                            $rest_advance_amount = $rest_advance_amount - $single_unpaid_rent->hidden_adjustment_adv;
                            

                            if ($rest_advance_amount < 0) {
                                $this->db->query("update rent_ind_schedule set adjustment_adv = $temp1
                                           
                                             WHERE id = $single_unpaid_rent->id and paid_sts ='unpaid' ");
                                break;
                            }

                            $this->db->query("update rent_ind_schedule set adjustment_adv = $single_unpaid_rent->hidden_adjustment_adv,
                                             unadjusted_adv_rent = $rest_advance_unpaid
                                             WHERE id = $single_unpaid_rent->id and paid_sts ='unpaid' ");
                            $counter++;
                        }
                    } else { // paid nai

                        foreach ($unpaid_rent as $single_unpaid_rent){

                            if($total_advance_paid->adjust_adv_type==0 ||  $total_advance_paid->adjust_adv_type==1){
                                $partial_amount= 0;
                            }else{
                                $partial_amount= ($temp % $single_unpaid_rent->hidden_adjustment_adv);
                            }

                            $temp2= $total_advance_paid_already;
                            $total_advance_paid_already = $total_advance_paid_already - $single_unpaid_rent->hidden_adjustment_adv;

                            if ($total_advance_paid_already < 0) {
                                  $this->db->query("update rent_ind_schedule set adjustment_adv = $temp2
                                           
                                             WHERE id = $single_unpaid_rent->id and paid_sts ='unpaid' ");

                                break;
                            }

                            $this->db->query("update rent_ind_schedule set adjustment_adv = $single_unpaid_rent->hidden_adjustment_adv,
                                             unadjusted_adv_rent = $total_advance_paid->total_advance_paid
                                             WHERE id = $single_unpaid_rent->id and paid_sts ='unpaid' ");

                            $counter++;
                        }

                        //$unadjust = $total_advance_paid->total_advance_paid;
                    }
    }

    function dateDiff($time1, $time2, $precision = 6) {
        // If not numeric then convert texts to unix timestamps
        //echo $time2;exit;
        $time1 = date('Y-m-d', strtotime($time1 . ' +1 day')); // exp date 1 increse for period showing
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
        $intervals = array('year', 'month', 'day', 'hour', 'minute', 'second');
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

// 8 may + 4 august

    function update_sche_info() {
        $add_edit='';
        $rent_agree_id = $this->input->post('rent_agree_id');
        $count_row = $this->input->post('count_row');
        $click_sts = $this->input->post('click_sts');
        $arear_remarks = $this->input->post('arear_remarks');
        //$debit_account = $this->input->post('debit_account');
        $credit_account = $this->input->post('credit_account');
        

        $rent_inc_result = $this->agreement_model->rent_inc_adj_get_info($add_edit, $rent_agree_id);
        $incr_tbl_row = count($rent_inc_result);
        $remarks_str='';
        
        if($click_sts!=0){
            // incr updated data
            $updated_inc_select_arr = explode(",",$this->input->post('updated_inc_select'));
            $updated_rent_amount_val_arr = explode(",",$this->input->post('updated_rent_amount_val'));
            $updated_cal_rent_val_arr = explode(",",$this->input->post('updated_cal_rent_val'));
            $updated_others_select_str_arr = explode(",",$this->input->post('updated_others_select_str'));
            $updated_others_amount_val_str_arr = explode(",",$this->input->post('updated_others_amount_val_str'));
            $updated_others_rent_val_str_arr = explode(",",$this->input->post('updated_others_rent_val_str')); 

                for ($i = 0; $i < $incr_tbl_row; $i++) {

                     $this->db->query("update rent_agr_increment_history 
                        set 
                        rent_amount_type = '".$updated_inc_select_arr[$i]."',
                        rent_amount_val =  '".$updated_rent_amount_val_arr[$i]."',
                        cal_rent_val = '".$updated_cal_rent_val_arr[$i]."',
                        others_amount_type = '".$updated_others_select_str_arr[$i]."',
                        others_amount_val =  '".$updated_others_amount_val_str_arr[$i]."',
                        cal_others_val = '".$updated_others_rent_val_str_arr[$i]."'

                        where id = '".$rent_inc_result[$i]->id."'
                     ");
                }

        }

        $count=0;
        $remarks_str= '';
            for ($i = 0; $i < $count_row; $i++) {

                if($click_sts!=0){
                    if($i%12==0){
                    	if(!empty($updated_inc_select_arr[$count])){
                          if($updated_inc_select_arr[$count]=='per_rent'){
                                $remarks_str= $updated_rent_amount_val_arr[$count].' % increment ';

                               }elseif($updated_inc_select_arr[$count]=='dir_rent'){
                                 $remarks_str= $updated_rent_amount_val_arr[$count].' tk increment ';
                               }else{
                                $remarks_str= '';
                               }
                               $count++;
                    	}
                        
                    }else{
                        $remarks_str= '';
                    }
                }

                if($this->input->post("payment_sts_tr".$i)=='unpaid'){ 
                    // modified in 3 oct 2018
                    
                    $this->db->set('hidden_adjustment_adv', $this->input->post("rent_adv".$i), FALSE);
                    $this->db->set('adjustment_adv', $this->input->post("rent_adv".$i), FALSE);
                    $this->db->set('area_amount', $this->input->post("area_amount".$i), FALSE);
                    $this->db->set('monthly_rent_amount', $this->input->post("monthly_rent_val".$i), FALSE);
                    // $this->db->set('total_others_amount', $this->input->post("others_rent_val".$i), FALSE);
                    $this->db->set('remarks',$remarks_str);
                    $this->db->where('id', $this->input->post("sche_id".$i));
                    $this->db->update('rent_ind_schedule');
                }
            }

            $this->db->set('arear_sts',1);
            $this->db->set('arear_remarks',$arear_remarks);
            //$this->db->set('debit_account',$debit_account);
            $this->db->set('credit_account',$credit_account);
            
            $this->db->where('id', $rent_agree_id);
            $this->db->update('rent_agreement');


            $var = array();
            $var['Message'] = 'OK';
            echo json_encode($var);
    }


    // 3 august

     function get_schedule_info_for_arear(){
        $rent_agree_id = $this->input->post('rent_id');
        $result = $this->agreement_model->rent_schedule_info($this->input->post('rent_id'));
        $advance_result = $this->agreement_model->single_rent_schedule_info($this->input->post('rent_id'));
        $rent_agreement_row_data = $this->agreement_model->get_add_action_data_new($this->input->post('rent_id'));
        $rent_adjust_data = $this->agreement_model->single_rent_adjustment_get_info('', $this->input->post('rent_id'));
        $others_no_tax_amount = $this->agreement_model->get_others_no_tax_amount($this->input->post('rent_id'));
        $ref_single_gl_list = $this->agreement_model->get_parameter_data('ref_rent_single_gl', 'id', '');
        $date_diff = $this->dateDiff($rent_agreement_row_data->agree_exp_dt, $rent_agreement_row_data->rent_start_dt);
        $start_date = date_create($rent_agreement_row_dataa->rent_start_dt);
        $end_date = date_create($rent_agreement_row_data->agree_exp_dt);
        $point_of_payment = $rent_agreement_row_data->point_of_payment;
        //$tax_rate = $this->agreement_model->get_parameter_data_single('ref_rent_tax','name','');
        $tax_slab_rate = $this->agreement_model->get_tax_slab_rate();
        $slab_count= count($tax_slab_rate);
        //$unadjust = $rent_agreement_row_data->total_advance;
        $unadjust = $rent_agreement_row_data->total_advance_paid;
        $count_row = count($result);

        if($point_of_payment == 'cm'){
            $pp_str = 'Current Month';
        }else{
            $pp_str = 'Following Month';
        }
        $html = '';
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
            background-color: #E5AAAA;
        }
        table#t01 tr:nth-child(odd) {
           background-color:#D2C9C9;
        }
        table#t01 th    {
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

        if($rent_agreement_row_data->location_owner=='rented'){

                $html.= '<p class="summery_class" ><b>Payment Summery</b></p>';
                $html.= '<p class="summery_class"><b>Period :</b> ' . date_format($start_date, "d/m/Y") . ' to ' . date_format($end_date, "d/m/Y") . ' (' . $date_diff . '), ' . $pp_str . ' Basis</p>';
                //  $html.= '<p class="summery_class"><b>10% increment every 02 years</b></p>'; 
                $html.= '<p class="summery_class"><b>Initial Rent :</b> ' . $rent_agreement_row_data->monthly_rent . '</p>';
                $html.= '<p class="summery_class"><b>Advance payment : </b>' . $rent_agreement_row_data->total_advance . '</p>';
                $html.= '<p class="summery_class"><b>Paid Advance Amount : </b>' . $advance_result->advence_rent_amount. '</p>';
                //$html.= '<p class="summery_class"><b>Monthly Adjustment :</b> ' . $rent_adjust_data->percent_dir_val . '</p>';
                $html.= '<br />Arrear Remarks: <textarea class="textarea" style="width:377px;height:20px;" name="arear_remarks"  id="arear_remarks" placeholder="Arrear Remarks">'.$rent_agreement_row_data->arear_remarks.'</textarea>';

                $html.='<br /><br /><div style="text-align:center;display:none">  
                            <p class=""><b>Debit Account : </b>  
                                <select name="debit_account" style="width: 200px;">';
                                        foreach($ref_single_gl_list  as $single_gl){    
                                            $html.='<option '; 
                                            $html.=(isset($rent_agreement_row_data->debit_account) && $rent_agreement_row_data->debit_account == $single_gl->id) ? 'selected' : '';
                                            $html.=' value="'.$single_gl->id.'">'.$single_gl->name.'</option>'; 
                                            
                                        }    
                                $html.='</select> </p></div>';

                $html.='<br /><div style="text-align:center">  
                            <p class=""><b>Credit Account : </b>  
                                <select name="credit_account" style="width: 200px;">
                                               
                                            <option '; 
                                            $html.=(isset($rent_agreement_row_data->credit_account) && $rent_agreement_row_data->credit_account == 'advance_gl') ? 'selected' : '';
                                            $html.=' value="advance_gl">Advance GL</option> 
                                            <option ';
                                            $html.=(isset($rent_agreement_row_data->credit_account) && $rent_agreement_row_data->credit_account == 'provision_gl') ? 'selected' : '';
                                            $html.=' value="provision_gl">Provision GL</option>
                                            <option ';
                                            $html.=(isset($rent_agreement_row_data->credit_account) && $rent_agreement_row_data->credit_account == 'rent_gl') ? 'selected' : '';
                                            $html.=' value="rent_gl">Expense GL</option>
                                            <option ';
                                            $html.=(isset($rent_agreement_row_data->credit_account) && $rent_agreement_row_data->credit_account == 'landlord') ? 'selected' : '';
                                            $html.=' value="landlord">Landlord Account</option>
                                </select> </p></div>';                
           
                $html.= '<input type="hidden" id="rent_agree_id" name="rent_agree_id" value="'.$rent_agree_id.'">';
                $html.= '<input type="hidden" id="rent_start_dt" name="rent_start_dt" value="'.$rent_agreement_row_data->rent_start_dt.'">';
                $html.= '<input type="hidden" id="agree_exp_dt" name="agree_exp_dt" value="'.$rent_agreement_row_data->agree_exp_dt.'">';
                $html.= '<input type="hidden" id="location_owner" name="location_owner" value="'.$rent_agreement_row_data->location_owner.'">';
                $html.= '<input type="hidden" id="monthly_rent_amt" name="monthly_rent_amt" value="'.$rent_agreement_row_data->monthly_rent.'">';
                $html.= '<input type="hidden" name="count_row" value="' . $count_row . '">';
                $html.= '<input type="hidden" name="click_sts" id="click_sts" value="0">';
                $html.= '<input type="hidden" name="arear_sts" id="arear_sts" value="'.$rent_agreement_row_data->arear_sts.'">';
                $html.= '<input type="hidden" name="tax_rate_for_arear" id="tax_rate_for_arear" value="5">';
        // updated hidden data start

                $html.= '<input type="hidden" name="updated_inc_select" id="updated_inc_select" value="">';
                $html.= '<input type="hidden" name="updated_rent_amount_val" id="updated_rent_amount_val" value="">';
                $html.= '<input type="hidden" name="updated_cal_rent_val" id="updated_cal_rent_val" value="">';
                
                $html.= '<input type="hidden" name="updated_others_select_str" id="updated_others_select_str" value="">';
                $html.= '<input type="hidden" name="updated_others_amount_val_str" id="updated_others_amount_val_str" value="">';
                $html.= '<input type="hidden" name="updated_others_rent_val_str" id="updated_others_rent_val_str" value="">';
                $html.= '<input type="hidden" name="updated_others_total_str" id="updated_others_total_str" value="">';
                // 2 oct 2018
                $html.= '<input type="hidden" name="paid_adv_amt" id="paid_adv_amt" value="'.$rent_agreement_row_data->total_advance_paid.'">';

        // updated hidden data end

                //$html.= ' <img align="center"  title="Increment Setting" id="incr_icon"  src="'.base_url().'images/incr.png" style="width:30px; hight:20px; cursor: pointer;">';

                $html .='<table class="" id="t01" style="border:solid 1px black; text-align: center; width:97%; margin:20px;">';


                $html .='<tr class="headrow" style="text-align:center;font-weight:bold;background-color:#C5C5C5; border: 1px solid black;">
                
                
                <th style="text-align:center;">SL</th>
                <th style="text-align:center;">Status</th>
                <th style="text-align:center;">Expected payment date</th>
                <th style="text-align:center;"> Monthly Rent</th>
                <th style="text-align:center;"> Others </th> 
                <th style="text-align:center;"> Arrear Amount </th>
                <th style="text-align:center; display:none;"> Diff </th>
                <th style="text-align:center;"> Adjustment</th>
                <th style="text-align:center;"> S.D Adjust</th>
                <th style="text-align:center; display:none;"> Provision Adjust</th>
                <th style="text-align:center;"> Tax </th>
                <th style="text-align:center;"> Net Payment </th>
                <th style="text-align:center; display:none;"> Unadjusted Advance rent</th>
                <th style="text-align:center;"> Remarks</th>';

        } 
        //  jQuery("input.number_a").on("keypress", function(e) {
		//     var caret = e.target.selectionStart;
		//     var nowStr = jQuery(this).val().substr(0, caret) + String.fromCharCode(e.which) + jQuery(this).val().substr(caret);
		//     if (!jQuery.isNumeric(nowStr)) e.preventDefault();
		// });

        $html .='<tbody id="">';
        $i = 0; $sl=1;
        foreach ($result as $row) {


        $html.= '<script>

			jQuery(document).on("keypress",".number_a",function (event) {
                var $this = jQuery(this);
                if (
                	(event.which != 46 || $this.val().indexOf(".") != -1) &&
                	(event.which != 45 || $this.val().indexOf("-") != -1) &&
                   ((event.which < 48 || event.which > 57) &&
                   (event.which != 0 && event.which != 8 ))) {
                       event.preventDefault();
                }

                var text = jQuery(this).val();
                if ((event.which == 46) && (text.indexOf(".") == -1)) {
                    setTimeout(function() {
                        if ($this.val().substring($this.val().indexOf(".")).length > 3) {
                            $this.val($this.val().substring(0, $this.val().indexOf(".") + 3));
                        }
                    }, 1);
                }

                if ((text.indexOf(".") != -1) &&
                    (text.substring(text.indexOf(".")).length > 2) &&
                    (event.which != 0 && event.which != 8 ) &&
                    (jQuery(this)[0].selectionStart >= text.length - 2)) {
                        event.preventDefault();
                } 


            });

		

        jQuery("#rent_adv'.$i.'").on("change",function(){

             var new_value = jQuery(this).val();
            

             var old_value =  parseFloat(jQuery(\'#old_rent_adv' . $i . ' \').val());
             var old_net_payment = parseFloat(jQuery(\'#old_net_payment' . $i . ' \').val());
             var  difference = old_value - new_value;  
             
            if(difference > 0){

                  var new_net_payment = old_net_payment + difference;
                  jQuery(\'#net_payment' . $i . ' \').val(new_net_payment);
                  jQuery(\'#net_pay_txt'.$i.' \').text(new_net_payment);
            
                  var j=' . $i . ';
                  var k=' . $count_row . ';
                  var reamining_row= k - (j+1);
                  var proportinal_amt = difference / reamining_row;
                  var proportinal_amt = parseFloat(proportinal_amt);
                 
                   for(var a=j+1;a<=k;a++){
                       
                    }

            } 
            else if(difference < 0){
              
                var new_net_payment = old_net_payment - Math.abs(difference);
                jQuery(\'#net_payment' . $i . ' \').val(new_net_payment);
                jQuery(\'#net_pay_txt'.$i.' \').text(new_net_payment);

            } 
        });
            

        </script>';

           // $new_net_payment = ($row->monthly_rent_amount + $row->total_others_amount + $row->area_amount) - ( $row->adjustment_adv + $row->tax_amount + $row->adjust_sec_deposit );
            // 20 sep 2017
            //$net_payment_before_tax = ($row->monthly_rent_amount + $row->total_others_amount + $row->area_amount) - ($row->adjustment_adv);
            $net_payment_before_tax = ($row->monthly_rent_amount + $row->total_others_amount + $row->area_amount ); // 9 nov 2017           
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
            $new_net_payment = ($net_payment_before_tax -$tax_amount - $row->adjustment_adv) - $row->adjust_sec_deposit; // 9 nov 2017

            $unadjust = $unadjust - $row->adjustment_adv;
            // if($point_of_payment == 'cm'){ $date = date_create("$row->schedule_strat_dt"); }
            // else{ $date = date_create("$row->maturity_dt");}
           
            $date = date_create("$row->maturity_dt");

            $d = date_format($date, "d-M-y");

            if ($row->remarks != '') {
                $style_tr = 'background-color: lightgreen !important;';
            } else {
                $style_tr = '';
            }
            if ($row->maturity_dt < date('Y-m-d') && $row->paid_sts == 'unpaid') {
                $paid_sts = 'Pending';
            }elseif ($row->maturity_dt < date('Y-m-d') && $row->paid_sts == 'paid') {
                  $paid_sts = 'Paid';
            } elseif ( $row->paid_sts == 'paid') {
                  $paid_sts = 'Paid';
            }
            else {
                $paid_sts = 'Not Matured';
            }


            $html .='<input type="hidden" name="payment_sts_tr'.$i.'"  id="payment_sts_tr'.$i.'" value="'.$row->paid_sts.'" readonly>';
            
            $html .='<input type="hidden" name="others_rent_val'.$i.'"  id="others_rent_val'.$i.'" value="'.$row->total_others_amount.'" readonly>';
            
            
            $html .='<tr style="border: 1px solid black ; '.$style_tr.'" id="sche_arear_tr'.$i.'">';

            $html .='<td style="text-align:center;">'.$sl.'</td>';
            $html .='<td style="text-align:center;">'.$paid_sts.'</td>';
            $html .='<td style="text-align:center;">' . $d . '</td>';
            $html .='<td style="text-align:center;display:none;" id="monthly_rent_tr'.$i.'">'.$row->monthly_rent_amount.'</td>';
            $html .='<td style="text-align:center;" id="">';
            if($row->paid_sts=='unpaid'){
                $html .='<input type="text" name="monthly_rent_val'.$i.'"  id="monthly_rent_val'.$i.'" value="'.$row->monthly_rent_amount.'">';
            }else{
                $html .=''.$row->monthly_rent_amount.'';
            }
            $html .=' </td>';
            $html .='<td style="text-align:center;" id="others_rent_tr'.$i.'">'.$row->total_others_amount.'</td>';
           // $html .='<td style="text-align:center;">' . $row->vat_amount . '</td>';
            $html .='<td style="text-align:center;" id="arear_tr'.$i.'">';
            if($row->paid_sts=='unpaid'){
               //$html .=' <input type="text" class="number" style="width: 52px" name="area_amount'.$i.'"  id="area_amount'.$i.'" value="'.$row->area_amount.'" >';
               $html .=' <input type="text" class="number_a" style="width: 52px" name="area_amount'.$i.'"  id="area_amount'.$i.'" value="'.$row->area_amount.'" >';
            }else{
                 $html .=''.$row->area_amount.'';
            }
               $html .=' </td>';
               $html .='<td style="display:none;"><input type="text" name="diff'.$i.'"  style="width: 52px"  id="diff'.$i.'" value="" readonly></td>';
            $html .='<input type="hidden" name="tax_amt'.$i.'"  id="tax_amt'.$i.'" value="'.$tax_amount.'" >';
          

            $html .='<td style="text-align:center;">
                <input type="hidden" name="sche_id' . $i . '" value="' . $row->id . '">
                <input type="hidden" name="hidden_paid_sts' . $i . '" id="hidden_paid_sts'. $i .'" value="' . $paid_sts . '">
                <input type="hidden" id="old_rent_adv' . $i . '" value="' .$row->adjustment_adv . '">';
            if($paid_sts=='Pending' ){
                
                  $html .='<input type="text" name="rent_adv'.$i.'"  style="width: 72px"  id="rent_adv'.$i.'" class="number_a"  value="'.$row->adjustment_adv.'" >';
               
            }else{
                  $html .='<input type="text" name="rent_adv'.$i.'"  style="width: 72px"  id="rent_adv'.$i.'" class="number_a"  value="' . $row->adjustment_adv . '" readonly>';    
            }
                $html .='</td>';
           // $html .='<td style="text-align:center;"><div style="text-align:center;  cursor:pointer" onclick="sd_preview_item(' . $row->id . ',' . $row->rent_agree_id . ')" ><img align="center" src="' . base_url() . 'images/view_detail.png"></div></td>';
            //$html .='<td style="text-align:center;">'.$row->adjust_sec_deposit.'</td>';
            $html .='<td style="text-align:center;">
                        <input type="hidden" name="adjust_sec_deposit'.$i.'" id="adjust_sec_deposit'.$i.'"  value="'.$row->adjust_sec_deposit.'">'.$row->adjust_sec_deposit.'';
            $html .='</td>';
            $html .='<td style="text-align:center; display:none;">0.00</td>';

            $html .='<td style="text-align:center;" id="tax_tr'.$i.'">'.$tax_amount.'';

            $html .='<input type="hidden" id="net_payment' . $i . '" value="' . $new_net_payment . '" readonly>';
            $html .='</td>';
            $html .='<td style="text-align:center;" id="net_pay_txt'.$i.'">' . $new_net_payment . '</td>
                         <input type="hidden" id="old_net_payment' . $i . '" value="' . $new_net_payment . '">
                         <input type="hidden" id="updated_net_payment'.$i.'" value="'.$new_net_payment.'">';
            //$html .='<td style="text-align:center;">' . $unadjust . '</td>';
            $html .='<td style="text-align:center; display:none">' . $row->unadjusted_adv_rent . '</td>';
            $html .='<td style="text-align:center;" id="remarks_tr'.$i.'">' . $row->remarks . '</td>';


            //$html .='<td style="text-align:center;"><input type="text"  name="" id="others_total'.$i.'" value="" class="incr_input"  readonly/></td>';
            //$html .='<td style="text-align:center;"><input type="text"  name="end_date'.$i.'" id="rent_end_date" value="'.$sch_end_date.'" class="incr_input"  readonly/></td>';
            $html .='</tr>';
            $i++;
            $sl++;
        }

        $html .='</tbody></table>';

        $html .='<input type="hidden" name="total_sche_count" id="total_sche_count" value="'.$i.'"  readonly/>';
        $html .='<input type="hidden"  name="paid_total_diff" id="paid_total_diff" value=""  readonly/>';


                $html.= '<script>

                     incr_func();
                            

                        </script>';

        echo $html;
    }


// 24 august

    function show_info_for_verify() {
        $add_edit='verify';
        $rent_agree_id = $this->input->post('rent_id');
        $verify_type = $this->input->post('verify_type');
        $r = $this->agreement_model->admin_check_status($rent_agree_id);
        // 18 sep
        $location_type_cc_result = $this->agreement_model->rent_location_type_cost_center_get_info_for_verify($add_edit, $rent_agree_id);
        $landlords_result = $this->agreement_model->rent_landlords_get_info_for_verify($add_edit, $rent_agree_id);
        $rent_inc_result = $this->agreement_model->rent_inc_adj_get_info($add_edit, $rent_agree_id);
         $agreement = $this->agreement_model->get_info($add_edit, $rent_agree_id);
        $rent_adjustment_result = $this->agreement_model->rent_adjustment_get_info($add_edit, $rent_agree_id);
        $html = '';
        if($r==0){
            //$html = 'Deleted Entry Cannot Verify !!!';
            $html = '';
        }
        else{

        $result = $this->agreement_model->rent_schedule_info($this->input->post('rent_id'));
        $advance_result = $this->agreement_model->single_rent_schedule_info($this->input->post('rent_id'));
        $rent_agreement_row_data = $this->agreement_model->get_add_action_data_new($this->input->post('rent_id'));
        $rent_adjust_data = $this->agreement_model->single_rent_adjustment_get_info('', $this->input->post('rent_id'));
        $date_diff = $this->dateDiff($rent_agreement_row_data->agree_exp_dt, $rent_agreement_row_data->rent_start_dt);
        $start_date = date_create($rent_agreement_row_data->rent_start_dt);
        $end_date = date_create($rent_agreement_row_data->agree_exp_dt);
        $point_of_payment = $rent_agreement_row_data->point_of_payment;
        //$unadjust = $rent_agreement_row_data->total_advance;
        $unadjust = $rent_agreement_row_data->total_advance_paid;
        $count_row = count($result);

        if ($point_of_payment == 'cm') {
            $pp_str = 'Current Month';
        } else {
            $pp_str = 'Following Month';
        }
        

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
            background-color: #E5AAAA;
        }
        table#t01 tr:nth-child(odd) {
           background-color:#D2C9C9;
        }
        table#t01 th    {
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

//if($rent_agreement_row_data->location_owner=='rented'){

    $incr_type= '';
    $incr_type_val= $rent_agreement_row_data->increment_type;
    if($incr_type_val==1){$incr_type='No Increment';}
    elseif($incr_type_val==2){$incr_type='Every '.$rent_agreement_row_data->increment_type_val.' Yearly Basis';}
    elseif($incr_type_val==3){$incr_type='Only One Time';}
    elseif($incr_type_val==4){$incr_type='Fixed Increment setup';}

    $cost_center_name= $this->agreement_model->get_single_cost_center_info($rent_agreement_row_data->agree_cost_center);

        //$html.= '<p class="summery_class" ><b>'.$verify_type.'</b></p><br />';
        $html.= '<p class="summery_class" ><b>Rent Agreement Summery</b></p><br />';
        $html.= '<p class="summery_class"><b>Reference No: '.$rent_agreement_row_data->agreement_ref_no.'</b></p>';
        $html.= '<p class="summery_class"><b>Duration : </b> ' . date_format($start_date, "d/m/Y") . ' to ' . date_format($end_date, "d/m/Y") . ' (' . $date_diff . ') </p>';
        
        $html.= '<p class="summery_class"><b>Payment Type: </b>' . $pp_str . ' Basis</p>';
        $html.= '<p class="summery_class"><b>Cost Center: </b>'.$cost_center_name->name.'</p>';
        $html.= '<p class="summery_class"><b>Location : </b>'.$rent_agreement_row_data->location_name.'</p>';
        $html.= '<p class="summery_class"><b>Landlord(s) : </b>'.$rent_agreement_row_data->landlord_names.'</p>';
        $html.= '<p class="summery_class"><b>Increment Type : </b>'.$incr_type.'</p>';
        //  $html.= '<p class="summery_class"><b>10% increment every 02 years</b></p>'; 
        $html.= '<p class="summery_class"><b>Initial Rent : </b> ' . $rent_agreement_row_data->monthly_rent . '</p>';
        $html.= '<p class="summery_class"><b>Advance payment : </b>' . $rent_agreement_row_data->total_advance . '</p>';
        $html.= '<input type="hidden" id="rent_agree_id" name="rent_agree_id" value="'.$rent_agree_id.'">';
        $html.= '<input type="hidden" name="agr_verify_type" id="agr_verify_type" value="'.$verify_type.'">';

        if($rent_agreement_row_data->location_owner=='rented'){
                $html.= '<p class="summery_class"><b>Paid Advance Amount : </b>' . $advance_result->advence_rent_amount. '</p>';
               // $html.= '<p class="summery_class"><b>Monthly Adjustment :</b> ' . $rent_adjust_data->percent_dir_val . '</p>';

                $html.= '<br />';

                if($verify_type=='fin_verify'){

                 $html.= '<p class="summery_class"><b>Last Paid Month : </b>';	
                 $html.= '<select name="last_paid_month" style="width: 100px;">
                 			<option value="">Select One</option>';	
	                 foreach ($result as $row){
	                 	$date = date_create("$row->maturity_dt");
	            		$d = date_format($date, "d-M-y");
	            		$html.='<option value="'.$row->maturity_dt.'">'.$d.'</option>';

	                 }	
                 $html.= '</select> </p>';

                }
                
                $html.= '<br />';
               
                $html.= '<input type="hidden" id="rent_start_dt" name="rent_start_dt" value="'.$rent_agreement_row_data->rent_start_dt.'">';
                $html.= '<input type="hidden" id="agree_exp_dt" name="agree_exp_dt" value="'.$rent_agreement_row_data->agree_exp_dt.'">';
                $html.= '<input type="hidden" id="location_owner" name="location_owner" value="'.$rent_agreement_row_data->location_owner.'">';
                $html.= '<input type="hidden" id="monthly_rent_amt" name="monthly_rent_amt" value="'.$rent_agreement_row_data->monthly_rent.'">';
                $html.= '<input type="hidden" name="count_row" value="'.$count_row.'">';
                $html.= '<input type="hidden" name="click_sts" id="click_sts" value="440">';
                $html.= '<input type="hidden" name="arear_sts" id="arear_sts" value="'.$rent_agreement_row_data->arear_sts.'">';
        // updated hidden data start

                $html.= '<input type="hidden" name="updated_inc_select" id="updated_inc_select" value="">';
                $html.= '<input type="hidden" name="updated_rent_amount_val" id="updated_rent_amount_val" value="">';
                $html.= '<input type="hidden" name="updated_cal_rent_val" id="updated_cal_rent_val" value="">';
                
                $html.= '<input type="hidden" name="updated_others_select_str" id="updated_others_select_str" value="">';
                $html.= '<input type="hidden" name="updated_others_amount_val_str" id="updated_others_amount_val_str" value="">';
                $html.= '<input type="hidden" name="updated_others_rent_val_str" id="updated_others_rent_val_str" value="">';
                $html.= '<input type="hidden" name="updated_others_total_str" id="updated_others_total_str" value="">';
                
                
             // updated hidden data end
             // 18 sep

// adjustment table

        $html.= '<b>Adjustment</b>';
        if($agreement->adjust_adv_type==1){ // no adjustment

        }else if($agreement->adjust_adv_type==2){ // fixed amount
            
            $html .='<input name="adj_table_id" type="hidden" id="adj_table_id" value="'.$rent_adjustment_result[0]->id.'" />'; 
            
            $html.='<table class="register-table" >';
            $html.='    <tr id="fixed_amt_tr">
                                <td style=" width:30%;">Monthly rent </td>
                                <td>
                                    <input type="text"  name="agree_month_rent" id="agree_month_rent" value="'.$agreement->monthly_rent.'" class="text-input-small"  readonly /> 
                                </td>
                              
                        </tr>';
            $html.='    <tr id="fixed_amt_tr">
                                <td style=" width:30%;">Adjustment Type </td>
                                <td>
                                    Fixed adjustment amount
                                </td>
                              
                        </tr>'; 
            $html.='    <tr id="fixed_amt_tr">
                                <td style=" width:30%;"> Amount (Fixed) </td>
                                <td>
                                    <input name="fixed_amt"  class="text-input-small number" id="fixed_amt" value="'.$rent_adjustment_result[0]->percent_dir_val.'"  class="text-input-small" readonly/> / Month
                                </td>
                        </tr>';
            $html.='</table>';    
        }else if($agreement->adjust_adv_type==3){

            $html .='<input name="adj_table_id" type="hidden" id="adj_table_id" value="'.$rent_adjustment_result[0]->id.'" />'; 
            $html .='<input name="agree_total_advance" type="hidden" id="agree_total_advance" value="'.$agreement->total_advance.'" />'; 
            $html.='<table class="register-table">';
            $html.='    <tr id="fixed_amt_tr">
                                <td style=" width:30%;">Monthly rent </td>
                                <td>
                                     <input type="text"  name="agree_month_rent" id="agree_month_rent" value="'.$agreement->monthly_rent.'" class="text-input-small"  readonly /> 
                                </td>
                              
                        </tr>'; 
            $html.='    <tr id="fixed_amt_tr">
                                <td style=" width:30%;">Adjustment Type </td>
                                <td>
                                   Percentage (%) basis
                                </td>
                              
                        </tr>'; 
            $html.='     <tr id="percent_amt_tr">
                                <td style=" width:30%;"> Amount (%) </td>
                                <td>
                                    <input name="percent_amt" class="text-input-small number" id="percent_amt" value="'.$rent_adjustment_result[0]->percent_dir_val.'"  class="text-input-small" readonly/> / Month
                                </td>
                            </tr>';

            $html.='     <tr id="calculated_percent_amt_tr">
                                <td style=" width:30%;"> Calculated Amount </td>
                                <td>
                                    <input name="calculated_percent_amt" class="text-input-small number" id="calculated_percent_amt" value="'.($rent_adjustment_result[0]->percent_dir_val/100) * $agreement->total_advance.'"  class="text-input-small" readonly/> / Month
                                </td>
                         </tr>';
            $html.='</table>';
    }else if($agreement->adjust_adv_type==4){

        

            $total_advance = $agreement->total_advance;
            $start = $agreement->rent_start_dt;
            $exp =  $agreement->agree_exp_dt;
            $yearly_seleted_option = $rent_adjustment_result[0]->percent_dir_type;
            //  $monthly_rent= $this->input->post('monthly_rent');
            $a = explode('-', $start);
            
            $date_day = $a[2];
            $date_month = $a[1];
            $date_year = $a[0];

            if ($this->input->post('start')) {
                    $start= $rent_start_dt = date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('start'))));
                }
                if ($this->input->post('exp')) {
                   $exp=  $agree_exp_dt = date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('exp'))));
                }

            $d1 = new DateTime($start);
            $d2 = new DateTime($exp);
            $diff = $d2->diff($d1);
            $year_diff = $diff->y;

           // $html = '';
          //  $html.='<input type="hidden"  name="adjustment_result_row_count" id="adjustment_result_row_count" value="'.$adjustment_result_row_count.'" class="text-input-small"  readonly />';
            $html.='<input type="hidden"  name="yearly_seleted_option" id="yearly_seleted_option" value="'.$yearly_seleted_option.'" class="text-input-small"  readonly />';
       
            $html.='<table class="register-table" >';
            $html.='    <tr id="fixed_amt_tr">
                                <td style=" width:30%;">Monthly rent </td>
                                <td>
                                     <input type="text"  name="agree_month_rent" id="agree_month_rent" value="'.$agreement->monthly_rent.'" class="text-input-small"  readonly /> 
                                     <input name="agree_total_advance" type="hidden" id="agree_total_advance" value="'.$agreement->total_advance.'" />
                                </td>
                              
                        </tr>'; 
            $html.='    <tr id="fixed_amt_tr">
                                <td style=" width:30%;">Adjustment Type </td>
                                <td>
                                   Single Year Basis
                                </td>
                              
                        </tr>'; 
            $html.='</table>';       
        
        if($yearly_seleted_option=='yearly_adj_percent'){
            
                   // $html = '';
                    $html .='<table class="register-table" style="border:solid 1px black; text-align: center; width:97%; margin:20px;">';
                    $html .='<tr class="headrow" style="text-align:center;font-weight:bold;background-color:#C5C5C5; border: 1px solid black;"><th>Year</th><th id="type_head">(%) / Month</th><th>Calculated Monthly Adjustment</th></tr>';
                    $html .='<tbody id="register-table">';

//for ($i = 0; $i <= $year_diff; $i++) {
 //  foreach ($rent_adjustment_result as $single_rent_adjustment_info) {
                    $i = 0;       
            foreach ($rent_adjustment_result as $single_rent_adjustment_info) {

               

                    $cal_month_adj_amt =   ($total_advance * $single_rent_adjustment_info->adv_incr_year_val)/100;


                    $yr_sl= $i+1;
                    $temp_data_year = $date_year;
                    $sch_start_date = $date_day . '-' . $date_month . '-' . $date_year++;

                    $sch_start_date2 = $date_day . '-' . $date_month . '-' . $date_year;
                    //  $sch_start_date1= $date_year.'-'.$date_month.'-'.$date_day;

                    $sch_end_date = date('d-m-Y', strtotime($sch_start_date2 . ' -1 day'));
                    //  $exp_date= date('d-m-Y', strtotime($exp .' +0 day'));

                    $sch_end_date_t = strtotime($sch_end_date);
                    $exp_date = strtotime($exp);
                    if ($exp_date < $sch_end_date_t) {
                        $sch_end_date = date('d-m-Y', $exp_date);
                    }


                    //$html.= '';
                    $html .='<input name="adj_table_id'.$i.'" type="hidden" id="adj_table_id'.$i.'" value="'.$single_rent_adjustment_info->id.'" />'; 
                 
                    $html .='<input name="count_year" type="hidden" value="' . $date_year . '" />'; 
                    $html .='<input name="adj_year_sl" type="hidden" value="' . $i . '" />';
                    $html .='<input name="adjust_change_sts" type="hidden" value="1" />';
                    $html .='<tr style="text-align:center;">';
                   // $html .='<td style="text-align:center;"><input type="text"  name="adj_year' . $i . '" id="adj_year" value="' . $temp_data_year . '" class="text-input-small"  readonly /> </td>';
                    $html .='<td style="text-align:center;"><input type="hidden"  name="adj_year' . $i . '" id="adj_year" value="' . $temp_data_year . '" class="text-input-small"  readonly /> <input type="text"  name="" id="" value="Year ' .$yr_sl . ' " class="text-input-small"  readonly /> </td>';
                   // $html .='<td style="text-align:center;"><input type="text"  name="adj_year' . $i . '" id="adj_year" value="Year ' .$yr_sl . ' " class="text-input-small"  readonly /> </td>';

                    $html .='<td style="text-align:center;"><input type="text"  name="yrly_adj_amt' . $i . '" id="yrly_adj_amt'.$i.'"  class="text-input-small number" value="'.$single_rent_adjustment_info->adv_incr_year_val.'"  readonly /></td>';
                    
                    $html .='<td class="abc" style="text-align:center;"><input type="text"  name="cal_month_adj_amt' . $i . '" id="cal_month_adj_amt'.$i.'"  class="text-input-small number" value="'.$cal_month_adj_amt .'"  readonly /></td>';
                    $html .='</tr>';

                   

                 $i++;

            }

            $html.= '<input type="hidden"  name="" id="yr_row_count" value="'.$yr_sl.'" class="text-input-small"  readonly /> ';
            $html .='</tbody></table>';

        }elseif($yearly_seleted_option=='yearly_adj_fixed'){

       // $html = '';
        $html .='<table class="register-table" style="border:solid 1px black; text-align: center; width:97%; margin:20px;">';
        $html .='<tr class="headrow" style="text-align:center;font-weight:bold;background-color:#C5C5C5; border: 1px solid black;"><th>Year</th><th id="type_head">Amount / Month</th></tr>';
        $html .='<tbody id="register-table">';

//for ($i = 0; $i <= $year_diff; $i++) {
        $i = 0;       
            foreach ($rent_adjustment_result as $single_rent_adjustment_info) {
   
                    $yr_sl= $i+1;
                    $temp_data_year = $date_year;
                    $sch_start_date = $date_day . '-' . $date_month . '-' . $date_year++;

                    $sch_start_date2 = $date_day . '-' . $date_month . '-' . $date_year;
                    $sch_end_date = date('d-m-Y', strtotime($sch_start_date2 . ' -1 day'));
                    $sch_end_date_t = strtotime($sch_end_date);
                    $exp_date = strtotime($exp);
                    if ($exp_date < $sch_end_date_t) {
                        $sch_end_date = date('d-m-Y', $exp_date);
                    }

                    //$html.= '';
                    $html .='<input name="adj_table_id'.$i.'" type="hidden" id="adj_table_id'.$i.'" value="'.$single_rent_adjustment_info->id.'" />'; 
                    $html .='<input name="count_year" type="hidden" value="' . $date_year . '" />'; 
                    $html .='<input name="adj_year_sl" type="hidden" value="' . $i . '" />';
                    $html .='<input name="adjust_change_sts" type="hidden" value="1" />';
                    $html .='<tr style="text-align:center;">';
                  
                    $html .='<td style="text-align:center;"><input type="hidden"  name="adj_year' . $i . '" id="adj_year" value="' . $temp_data_year . '" class="text-input-small"  readonly /> <input type="text"  name="" id="" value="Year ' .$yr_sl . ' " class="text-input-small"  readonly /> </td>';
                    $html .='<td style="text-align:center;"><input type="text"  name="yrly_adj_amt' . $i . '" id="yrly_adj_amt'.$i.'"  class="text-input-small number" value=" '.$single_rent_adjustment_info->adv_incr_year_val.'"  readonly /></td>';
                    
                    $html .='</tr>';
                    $i++;
            }
                $html.= '<input type="hidden"  name="" id="yr_row_count" value="'.$yr_sl.'" class="text-input-small"  readonly /> ';
                $html .='</tbody></table>';
        }
    }


 // location types table
                $html.= '<b>Locations</b>';
                $html .='<table class="" id="" style="border:solid 1px black; text-align: center; width:97%; margin:20px;">';
                $html .='<tr class="headrow" style="text-align:center;font-weight:bold;background-color:#C5C5C5; border: 1px solid black;">
        
                <th style="text-align:center;">Location</th>
                <th style="text-align:center;">MIS</th>
                <th style="text-align:center;">Square feat</th>
                <th style="text-align:center;">Amount(%)</th>';
                $html .='<tbody id="">';

                foreach($location_type_cc_result as $row){
                    $html .='<tr style="border: 1px solid black ;" >';

                    $html .='<td style="text-align:center;">'.$row->name.'</td>';
                    $html .='<td style="text-align:center;">'.$row->location_mis_id.'</td>';
                    $html .='<td style="text-align:center;">'.$row->sq_ft.'</td>';
                    $html .='<td style="text-align:center;">'.$row->cost_in_percent.'</td>';
                  

                    $html .='</tr>';
                }

                $html .='</tbody></table>';



    // landlords table
                $html.= '<b>Landlords</b>';
                $html .='<table class="" id="" style="border:solid 1px black; text-align: center; width:97%; margin:20px;">';
                $html .='<tr class="headrow" style="text-align:center;font-weight:bold;background-color:#C5C5C5; border: 1px solid black;">
                
                
                <th style="text-align:center;">Landlord Name</th>
                <th style="text-align:center;"> Account No</th>
             
                <th style="text-align:center;">Credit Status</th>
                <th style="text-align:center;">Adjust Amount(%)</th>
                <th style="text-align:center;">Rent Amount(%)</th>';
                $html .='<tbody id="">';

                foreach($landlords_result as $row){
                    $html .='<tr style="border: 1px solid black ;" >';

                    $html .='<td style="text-align:center;">'.$row->name.'</td>';
                    $html .='<td style="text-align:center;">'.$row->account_no.'</td>';
                    $html .='<td style="text-align:center;">'.$row->credit_sts.'</td>';
                    $html .='<td style="text-align:center;">'.$row->adv_amount_percent.'</td>';
                    $html .='<td style="text-align:center;">'.$row->credit_amount_percent.'</td>';
                  

                    $html .='</tr>';


                }

                $html .='</tbody></table>';

            // increment table  
                $html.= '<b>Increment</b>';
                $counter_others = $rent_inc_result[0]->others_id_list;
                $arr_other_name = explode(",",$counter_others);
                $element_number = count($arr_other_name);
                $incr_row_number = count($rent_inc_result);

                $html .='<table class="register-table" id="increment_tbl"  style="border:solid 1px black; text-align: center; width:97%; margin:20px;">';

                $html .='<tr class="headrow" style="text-align:center;font-weight:bold;background-color:#C5C5C5; border: 1px solid black;"><th>Year</th><th> Dir/ % </th><th>Monthly Rent</th>';
                $j = 0;
       
                    if($counter_others!=''){
                        foreach ($arr_other_name as $single_arr_other_name) {
                            if($single_arr_other_name!=''){
                                $html.='<th>Dir / % </th>';
                                $html.='<th>' . $single_arr_other_name . '</th>';
                                $j++;
                            }    
                        }
                    }
                $html.='</tr>';
                $number =1;
                $ends = array('th','st','nd','rd','th','th','th','th','th','th');
                $i=0;

                foreach ($rent_inc_result as $row) {
            
                      if (($number %100) >= 11 && ($number%100) <= 13)
                        $abbreviation = $number. 'th';
                      else
                        $abbreviation = $number. $ends[$number % 10];      
     
            //$html .='<input id="count_year" name="count_year" type="hidden" value="' . $year_diff . '" />';
                    $html .='<input id="element_number" name="element_number" type="hidden" value="'.$element_number.'" />';
                    $html .='<input id="counter_others" name="counter_others" type="hidden" value="'.$counter_others.'" />';
                    //$html .='<input name="year_sl" type="hidden" value="'.$i.'" />';
                    $html .='<input name="year_sl' . $i . '" type="hidden" value="' . $row->rent_incr_yr . '" />';
                    $html .='<input name="incr_change_sts" type="hidden" value="0" />';

                    $html .='<tr id="increment_tr'.$i.'" class="incre_tr_cls"   style="border: 1px solid black ;">';
                    $html .='<td style="text-align:center;">'.$abbreviation.' Year </td>';
                    $html .='<td >';
                     
                    $html .=' <input type="text"  name="rent_amount_val' . $i . '" id="rent_amount_val' . $i . '"  class=" incr_input number " value="'.$row->rent_amount_val.'"  readonly/>';
                    if( $row->rent_amount_type == "dir_rent") { $html .=' (Fixed) ';  } 
                     elseif($row->rent_amount_type == "per_rent"){ $html .=' (%) '; } 
                     else{$html .=''; }
                    $html .= '</td>';
                    $html .='<td style="text-align:center;"><input type="text"  name="cal_rent_val' . $i . '" id="cal_rent_val' . $i . '"  class="inc_area_cal inc_cls  tot_val_' . $i . ' number mon_val"  value="'.$row->cal_rent_val.'"  readonly/></td>';
                    $j = 0;
                    $k = 1;
                    // foreach ($arr_other_name as $single_arr_other_name) {
                    // if($single_arr_other_name!=''){
                    $cal_others_val_arr = explode(",",$row->cal_others_val);
                    $others_amount_val_arr = explode(",",$row->others_amount_val);
                    $others_amount_type_arr = explode(",",$row->others_amount_type);
                    $others_count = count($cal_others_val_arr);
                    if($others_count > 1){
                        $others_style_str = 'width: 97px; margin-right:5px;';
                    }else{
                        $others_style_str = 'width: 110px; margin-right:5px;';
                    }
                    //exit();

                        if($row->cal_others_val!=''){
                            foreach ($cal_others_val_arr as $key=> $single_cal_others_val) {
                           // if($single_arr_other_name!=''){

                                        $html .='<td style=" padding: 5px;">'; 
                                           

                                        $html .= '<input type="text"  name="others_amount_val' . $i . $j . '" id="others_amount_val' . $i . $j . '"  class="incr_input number  others_amount_val'.$i.$j.'"  value="'.$others_amount_val_arr[$key].'"    readonly/>';
                                         if( $others_amount_type_arr[$key] == "dir_otr") { $html .=' (Fixed) ';  } 
                                            elseif( $others_amount_type_arr[$key] == "per_otr"){ $html .=' (%) '; } 
                                            else{$html .=''; }
                                        $html .= '</td>';
                                        $html .='<td style="text-align:center;"><input type="text"  name="cal_others_val' . $i . $j . '" id="others_rent"  class="inc_area_cal others_input_value'.$j.' number  other_tot_val_' . $i . $j . ' others_input_style" value="'.$single_cal_others_val.'"   readonly/></td>';
                                        
                                    
                                        $j++;
                                        $k++;
                                    
                               // }
                            }
                        }

                        $html .='</tr>';
                    
                        $number++;
                        $i++;
                }
                
                $html .='</tbody></table>';

        } 

      
        $html .='<input type="hidden"  name="paid_total_diff" id="paid_total_diff" value=""  readonly/>';

    }
               
        echo $html;
}



    function get_schedule_info() {
        $rent_agree_id = $this->input->post('rent_id');
        $result = $this->agreement_model->rent_schedule_info($this->input->post('rent_id'));
        $advance_result = $this->agreement_model->single_rent_schedule_info($this->input->post('rent_id'));
        $rent_agreement_row_data = $this->agreement_model->get_add_action_data_new($this->input->post('rent_id'));
        $rent_adjust_data = $this->agreement_model->single_rent_adjustment_get_info('', $this->input->post('rent_id'));
        $others_no_tax_amount = $this->agreement_model->get_others_no_tax_amount($this->input->post('rent_id'));
        $date_diff = $this->dateDiff($rent_agreement_row_data->agree_exp_dt, $rent_agreement_row_data->rent_start_dt);
        $start_date = date_create($rent_agreement_row_data->rent_start_dt);
        $end_date = date_create($rent_agreement_row_data->agree_exp_dt);
        $point_of_payment = $rent_agreement_row_data->point_of_payment;
        //$tax_rate = $this->agreement_model->get_parameter_data_single('ref_rent_tax','name','');
        $tax_slab_rate = $this->agreement_model->get_tax_slab_rate();
        $slab_count= count($tax_slab_rate);
        //$unadjust = $rent_agreement_row_data->total_advance;
        $unadjust = $rent_agreement_row_data->total_advance_paid;
        $count_row = count($result);


        $file_details = $this->agreement_model->get_files_details($this->input->post('rent_id'));
        // print_r($file_details);exit();
        if ($point_of_payment == 'cm') {
            $pp_str = 'Current Month';
        } else {
            $pp_str = 'Following Month';
        }
        $html = '';


        $html.= '

            <style>
            table#t01 {
                width:100%;
            }
            table#t01, table#t01 th, table#t01 td {
                border: 1px solid black;
                border-collapse: collapse;
            }
            table.extra {
                width:100%;
            }
            table.extra, table.extra th, table.extra td {
                border: 0px solid black;
                border-collapse: collapse;
            }
            table.extra_files {
                width:60%;
                align: center;
                float: center;
            }
            table.extra_files, table.extra_files th, table.extra_files td {
                border: 1px solid #ccc;
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
            table#t01 th    {
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

        if($rent_agreement_row_data->location_owner=='rented'){

            $incr_type= '';
                $incr_type_val= $rent_agreement_row_data->increment_type;
                if($incr_type_val==1){$incr_type='No Increment';}
                elseif($incr_type_val==2){$incr_type='Every Year Basis';}
                elseif($incr_type_val==3){$incr_type='Only One Time';}
                elseif($incr_type_val==4){$incr_type='Fixed Increment setup';}
                elseif($incr_type_val==5){$incr_type='Manual Increment setup';}

                $cost_center_name= $this->agreement_model->get_single_cost_center_info($rent_agreement_row_data->agree_cost_center);


                $html.= '<p class="summery_class" ><b>Payment Summery</b></p>';

                $html.= '<table border="0" width="100%" class="extra">
                            <tr>
                                <td align="left" width="70%">';

                $html.= '<p class="summery_class"><b>Period :</b> ' . date_format($start_date, "d/m/Y") . ' to ' . date_format($end_date, "d/m/Y") . ' (' . $date_diff . '), ' . $pp_str . ' Basis</p>';
                //  $html.= '<p class="summery_class"><b>10% increment every 02 years</b></p>'; 
                $html.= '<p class="summery_class"><b>Reference No: </b>'.$rent_agreement_row_data->agreement_ref_no.'</p>';
                $html.= '<p class="summery_class"><b>Cost Center: </b>'.$cost_center_name->name.'</p>';
                $html.= '<p class="summery_class"><b>Location : </b>'.$rent_agreement_row_data->location_name.'</p>';
                $html.= '<p class="summery_class"><b>Landlords : </b>'.$rent_agreement_row_data->landlord_names.'</p>';

                $html.= '</td><td align="right" width="30%">';

                $html.= '<p class="summery_class"><b>Increment Type : </b>'.$incr_type.'</p>';

                $html.= '<p class="summery_class"><b>Initial Rent :</b> ' . $rent_agreement_row_data->monthly_rent . '</p>';
                $html.= '<p class="summery_class"><b>Advance payment : </b>' . $rent_agreement_row_data->total_advance . '</p>';
                if (isset($advance_result->advence_rent_amount)) {
                    $html.= '<p class="summery_class"><b>Paid Advance Amount : </b>' . $advance_result->advence_rent_amount. '</p>';
                }
               // $html.= '<p class="summery_class"><b>Monthly Adjustment :</b> ' . $rent_adjust_data->percent_dir_val . '</p>';


                $html.= '</td>
                            </tr>
                        </table>';

                
                $html.= '<br />';
                $html.= '<h3>Rent Schedule Summery</h3>';
                $html.= '<br />';
                $html.= '<input type="hidden" name="rent_agree_id" value="' . $rent_agree_id . '">';
                $html.= '<input type="hidden" name="count_row" value="' . $count_row . '">';

                $html .='<table class="" id="t01" style="border:solid 1px black; text-align: center; width:97%; margin:20px;">';


                $html .='<tr class="headrow" style="text-align:center;font-weight:bold;background-color:#C5C5C5; border: 1px solid black;">
                
                
                <th style="text-align:center;">SL</th>
                <th style="text-align:center;">Status</th>
                <th style="text-align:center;">Expected payment date</th>
                <th style="text-align:center;"> Monthly Rent</th>
                <th style="text-align:center;"> Others </th>
                
                <th style="text-align:center;"> Arear Amount </th>
                <th style="text-align:center;"> Adjustment</th>
                <th style="text-align:center;"> S.D Adjust</th>
                <th style="text-align:center; display:none;"> Provision Adjust</th>
                <th style="text-align:center;"> Tax </th>
                <th style="text-align:center;"> Net Payment </th>
                <th style="text-align:center; "> Unadjusted Advance rent</th>
                <th style="text-align:center;"> Remarks</th>';
                //$unadjust_amount= $result[0]->advence_rent_amount;
                 $unadjust_amount= $rent_agreement_row_data->total_advance_paid;

        }else{
            $cost_center_name= $this->agreement_model->get_single_cost_center_info($rent_agreement_row_data->agree_cost_center);

            $html.= '<p class="summery_class" ><b>Payment Summery</b></p>';
            $html.= '<p class="summery_class"><b>Period :</b> ' . date_format($start_date, "d/m/Y") . ' to ' . date_format($end_date, "d/m/Y") . ' (' . $date_diff . '), ' . $pp_str . ' Basis</p>';
            //  $html.= '<p class="summery_class"><b>10% increment every 02 years</b></p>'; 
            $html.= '<p class="summery_class"><b>Reference No: </b>'.$rent_agreement_row_data->agreement_ref_no.'</p>';
            $html.= '<p class="summery_class"><b>Cost Center: </b>'.$cost_center_name->name.'</p>';
            $html.= '<p class="summery_class"><b>Location : </b>'.$rent_agreement_row_data->location_name.'</p>';
           // $html.= '<p class="summery_class"><b>Landlords : </b>'.$rent_agreement_row_data->landlord_names.'</p>';
            //$html.= '<p class="summery_class"><b>Increment Type : </b>'.$incr_type.'</p>';

            $html.= '<p class="summery_class"><b>Initial Rent :</b> ' . $rent_agreement_row_data->monthly_rent . '</p>';
            $html.= '<p class="summery_class"><b>Advance payment : </b>' . $rent_agreement_row_data->total_advance . '</p>';
           // $html.= '<p class="summery_class"><b>Paid Advance Amount : </b>' . $advance_result->advence_rent_amount. '</p>';
    
        } 

        $html .='<a target="_blank" href="'.base_url().'index.php/agreement/excel_schedule_info/'.$rent_agree_id.'"><img src="'.base_url().'/images/icon_xls.gif"></a>
        <tbody id="">';
        $i = 0; $sl=1;

        
        foreach ($result as $row){

           //$net_payment_before_tax = ($row->monthly_rent_amount + $row->total_others_amount + $row->area_amount) - ($row->adjustment_adv);
            $net_payment_before_tax = ($row->monthly_rent_amount + $row->total_others_amount + $row->area_amount ); // 9 nov 2017
            $tax_applicable_amt= $net_payment_before_tax - $others_no_tax_amount->tax_not_apply;
            $tax_rate=0;
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
            $new_net_payment = ($net_payment_before_tax -$tax_amount - $row->adjustment_adv) - $row->adjust_sec_deposit; // 9 nov 2017
           
          
           $unadjust_amount= $unadjust_amount - $row->adjustment_adv;
           
           if($row->unadjusted_adv_rent > 0 ){
           	$calculated_unadjust =$unadjust_amount;
           }else{
           	$calculated_unadjust=0;
           }
            

            $unadjust = $unadjust - $row->adjustment_adv;
            $prov_amount = 0;
            // if($point_of_payment == 'cm'){ $date = date_create("$row->schedule_strat_dt"); }
            // else{ $date = date_create("$row->maturity_dt");}

            $date = date_create("$row->maturity_dt");
            $d = date_format($date, "d-M-y");

            if ($row->remarks != '') {
                $style_tr = 'background-color: lightgreen !important;';
            } else {
                $style_tr = '';
            }

        if($row->paid_sts !='paid'){
            if(date("Y-m-d") > $row->maturity_dt) {
            	if($row->paid_sts =='advance')
							{ $paid_sts='Matured (Advance)';}
						else if($row->paid_sts =='unpaid' && $rent_agreement_row_data->agree_current_sts_id=='6')
							{ $paid_sts='Matured (Stop and Unpaid)';}
						else if($row->paid_sts =='stop' && $rent_agreement_row_data->agree_current_sts_id=='6')
							{ $paid_sts='Matured (Stoped)'; }
						else if($row->paid_sts =='stop' && $rent_agreement_row_data->agree_current_sts_id=='5' && $rent_agreement_row_data->point_of_payment=='cm')
							{ $paid_sts='Matured (Released and Unpaid)';}
					    else if($row->paid_sts =='unpaid' && $rent_agreement_row_data->agree_current_sts_id=='5')
							{ $paid_sts='Pending'; } 
					    else if($row->paid_sts =='stop' && $rent_agreement_row_data->agree_current_sts_id=='5' && $rent_agreement_row_data->point_of_payment=='pm')
							{ $paid_sts='Matured (Unpaid)';}
						else{$paid_sts='Pending'; } 
						 
                //$paid_sts='Pending'; 
                $prov_amount = 0.00;
                
                
            }else{
            	if(date("Y-m-d") > $row->schedule_strat_dt && $row->paid_sts !='stop' && $rent_agreement_row_data->point_of_payment=='pm'){
							$paid_sts='Not Matured (Accrual)';}
					
						else{
					    // not matured
						if($row->paid_sts =='advance'){ $paid_sts='Not Matured (Advance)'; }else{$paid_sts='Not Matured';}
						} 
                //$paid_sts='Not Matured';
                $prov_amount = 0.00;
            }
             
        }else{

            $paid_sts='Paid';
        }

            
            if($paid_sts=='Provisioned'){$prov_style_tr='background-color: #F5A9A9 !important;'; }else{$prov_style_tr='';} 
            $html .='<tr style="border: 1px solid black ; '.$style_tr.' '.$prov_style_tr.'" >';

            $html .='<td style="text-align:center;">'.$sl.'</td>';
            //$html .='<td style="text-align:center;">'.$tax_slab_rate[1]->name.'</td>';
            $html .='<td style="text-align:center;">'.$paid_sts.'</td>';
            $html .='<td style="text-align:center;">' . $d . '</td>';
            $html .='<td style="text-align:center;">' . $row->monthly_rent_amount . '</td>';
            $html .='<td style="text-align:center;">' . $row->total_others_amount . '</td>';
           // $html .='<td style="text-align:center;">' . $row->vat_amount . '</td>';
            $html .='<td style="text-align:center;">'.$row->area_amount.'</td>';
          
            $html .='<td style="text-align:center;">
                <input type="hidden" name="sche_id' . $i . '" value="' . $row->id . '">
                <input type="hidden" name="hidden_paid_sts' . $i . '" id="hidden_paid_sts'. $i .'" value="' . $paid_sts . '">
                <input type="hidden" id="old_rent_adv' . $i . '" value="' .$row->adjustment_adv . '">';
          
            $html .=$row->adjustment_adv.'</td>';
           // $html .='<td style="text-align:center;"><div style="text-align:center;  cursor:pointer" onclick="sd_preview_item(' . $row->id . ',' . $row->rent_agree_id . ')" ><img align="center" src="' . base_url() . 'images/view_detail.png"></div></td>';
            $html .='<td style="text-align:center;">' . $row->adjust_sec_deposit . '</td>';
            $html .='<td style="text-align:center;display:none;">'.$prov_amount.'</td>';
            $html .='<td style="text-align:center;">' .number_format($tax_amount,2). '<input type="hidden" id="net_payment' . $i . '" value="' . $new_net_payment . '" readonly></td>';
            $html .='<td style="text-align:center;" id="net_pay_txt'.$i.'">' .number_format($new_net_payment,2). '</td>
                         <input type="hidden" id="old_net_payment' . $i . '" value="' . $new_net_payment . '">';
            //$html .='<td style="text-align:center;">' . $unadjust . '</td>';
           // $html .='<td style="text-align:center; ">' . $row->unadjusted_adv_rent . '</td>';
            $html .='<td style="text-align:center; ">' .number_format($calculated_unadjust,2). '</td>'; // 9 nov 2017
            $html .='<td style="text-align:center;">' . $row->remarks . '</td>';

            $html .='</tr>';
            $i++;
            $sl++;
        }

        $html .='</tbody></table>';

        if (count($file_details) > 0) {
            $html.= '<table border="0" align="center" width="60%" class="extra_files">
                    <tr>
                        <th align="left" width="60%">File Name</th>
                        <th align="center" width="40%">Download</th>
                    </tr>';
            foreach ($file_details as $file_row) {
                $html .= '<tr>
                              <td align="left">'.$file_row->name.'</td>
                              <td align="center">';
                $multi_file = explode(',', $file_row->files);
                foreach ($multi_file as  $single_file) {
                    if ($single_file != '') {
                        $html .= '<a href="#" onClick="window.open(\''.base_url().'/uploads/'.$single_file.'\')"><img src="'.base_url().'/images/file_icon.png"></a>';
                    // http://192.168.3.253:85/rent//uploads/ebl_edeal+requirements+21_09_2017=1557978087.pdf
                    }
                }

                $html .= '    </td>
                          </tr>';
            }
            $html.= '</table>';
        }
        echo $html;
    }

    function excel_schedule_info($rent_agree_id){

        $result = $this->agreement_model->rent_schedule_info($rent_agree_id);
        //$advance_result = $this->agreement_model->single_rent_schedule_info($rent_agree_id);
        $incr_result = $this->agreement_model->rent_inc_adj_get_info('report',$rent_agree_id);
        $rent_agreement_row_data = $this->agreement_model->get_add_action_data_new($rent_agree_id);
        $rent_adjust_data = $this->agreement_model->single_rent_adjustment_get_info('', $rent_agree_id);
        $date_diff = $this->dateDiff($rent_agreement_row_data->agree_exp_dt, $rent_agreement_row_data->rent_start_dt);
        $start_date = date_create($rent_agreement_row_data->rent_start_dt);
        $end_date = date_create($rent_agreement_row_data->agree_exp_dt);
        $point_of_payment = $rent_agreement_row_data->point_of_payment;
        $tax_rate = $this->agreement_model->get_parameter_data_single('ref_rent_tax','name','');
        $tax_slab_rate = $this->agreement_model->get_tax_slab_rate();
        $slab_count= count($tax_slab_rate);

        //$unadjust = $rent_agreement_row_data->total_advance;
        $unadjust = $rent_agreement_row_data->total_advance_paid;
        $count_row = count($result);
        if ($point_of_payment == 'cm') {
            $pp_str = 'Current Month';
        } else {
            $pp_str = 'Following Month';
        }

        error_reporting(E_ALL);
        date_default_timezone_set('Asia/Dhaka');        
        require_once './application/Classes/PHPExcel.php';  
        $objPHPExcel = new PHPExcel();
        
        $objPHPExcel->setActiveSheetIndex(0);           
        $rowNumber = 1;
        $rowNumber++;
        $rowNumber++;
        $styleBorderOutline = array(
                                    'borders' => array(                                                     
                                            'top'     => array(
                                                'style' => PHPExcel_Style_Border::BORDER_THIN
                                            ),                                                      
                                            'bottom'     => array(
                                                'style' => PHPExcel_Style_Border::BORDER_THIN
                                            )                                                   
                                        ),
                                    );
        $styleArray = array(
              'borders' => array(
                  'allborders' => array(
                      'style' => PHPExcel_Style_Border::BORDER_THIN
                  )
              )
          );
        $incr_type= '';
                $incr_type_val= $rent_agreement_row_data->increment_type;
               
                if($incr_type_val==1){$incr_type='No Increment';}
                elseif($incr_type_val==2){
                $rent_amount_type= $incr_result[0]->rent_amount_type;
                $rent_amount_val= $incr_result[0]->rent_amount_val;
                    if($rent_amount_type=='per_rent'){
                        $incr_type=$rent_amount_val.'% increment after every '.$rent_agreement_row_data->increment_type_val.' Years';
                    }else{
                         $incr_type=$rent_amount_val.' taka increment after every '.$rent_agreement_row_data->increment_type_val.' Years';
                    }
                    
                }
                elseif($incr_type_val==3){$incr_type='Only One Time';}
                elseif($incr_type_val==4){$incr_type='Fixed Increment setup';}
                elseif($incr_type_val==5){$incr_type='Manual Increment setup';}

        // heading
        $objPHPExcel->getActiveSheet()->setCellValue('B'.$rowNumber, 'Payment Schedule');            
        $objPHPExcel->getActiveSheet()->mergeCells('B'.$rowNumber.':M'.$rowNumber);
        $objPHPExcel->getActiveSheet()->getStyle('B'.$rowNumber)->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('B'.$rowNumber.':T'.$rowNumber)->getFont()->setSize(16);
        $objPHPExcel->getActiveSheet()->getStyle('B'.$rowNumber.':T'.$rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

        $rowNumber++;
        $rowNumber++;
        $objPHPExcel->getActiveSheet()->setCellValue('B'.$rowNumber, 'Reference:' );
        $objPHPExcel->getActiveSheet()->setCellValue('C'.$rowNumber, $rent_agreement_row_data->agreement_ref_no );
        $rowNumber++;
        $objPHPExcel->getActiveSheet()->setCellValue('B'.$rowNumber, $rent_agreement_row_data->location_name );
        $objPHPExcel->getActiveSheet()->mergeCells('B'.$rowNumber.':C'.$rowNumber); 
        $rowNumber++;
        $objPHPExcel->getActiveSheet()->setCellValue('B'.$rowNumber, date_format($start_date, "d/m/Y") . ' to ' . date_format($end_date, "d/m/Y") . ' (' . $date_diff . ') ' . $pp_str . ' Basis' );
        $objPHPExcel->getActiveSheet()->mergeCells('B'.$rowNumber.':E'.$rowNumber);

        $rowNumber++;
        $objPHPExcel->getActiveSheet()->setCellValue('B'.$rowNumber, 'Total Area:' );
        $objPHPExcel->getActiveSheet()->setCellValue('C'.$rowNumber, $rent_agreement_row_data->total_square_ft.' sft' );
        $rowNumber++;
        $objPHPExcel->getActiveSheet()->setCellValue('B'.$rowNumber, 'Increment:' );
        $objPHPExcel->getActiveSheet()->setCellValue('C'.$rowNumber, $incr_type );
        $rowNumber++;
        $objPHPExcel->getActiveSheet()->setCellValue('B'.$rowNumber, 'Office Rent:' );
        $objPHPExcel->getActiveSheet()->setCellValue('C'.$rowNumber, $rent_agreement_row_data->monthly_rent );
        $rowNumber++;
        $objPHPExcel->getActiveSheet()->setCellValue('B'.$rowNumber, 'Others:' );
        $objPHPExcel->getActiveSheet()->setCellValue('C'.$rowNumber, $rent_agreement_row_data->others_rent );
        $rowNumber++;
        $objPHPExcel->getActiveSheet()->setCellValue('B'.$rowNumber, 'Advance payment:' );
        $objPHPExcel->getActiveSheet()->setCellValue('C'.$rowNumber, $rent_agreement_row_data->total_advance );
        $rowNumber++;
        $objPHPExcel->getActiveSheet()->getStyle('B1:B'.$rowNumber)->getFont()->setBold(true);
         
      

         $rowNumber++; 
         $objPHPExcel->getActiveSheet()->setCellValue('H'.$rowNumber, 'Deductions' );
         $objPHPExcel->getActiveSheet()->mergeCells('H'.$rowNumber.':J'.$rowNumber);
         $objPHPExcel->getActiveSheet()->getStyle('H'.$rowNumber.':J'.$rowNumber)->applyFromArray($styleArray);
         $objPHPExcel->getActiveSheet()->getStyle('H'.$rowNumber.':J'.$rowNumber)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
         $objPHPExcel->getActiveSheet()->getStyle('H'.$rowNumber.':J'.$rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
         $objPHPExcel->getActiveSheet()->getStyle('H'.$rowNumber.':J'.$rowNumber)->getFont()->setBold(true);
         $rowNumber++;


        $objPHPExcel->getActiveSheet()->getStyle('B'.$rowNumber.':T'.$rowNumber)->getFont()->setBold(true);
        $start_row= $rowNumber;
        if($rent_agreement_row_data->location_owner=='rented'){ 
        
        $unadjust_amount= $rent_agreement_row_data->total_advance_paid;
        

        // table header
        $objPHPExcel->getActiveSheet()->setCellValue('B'.$rowNumber, 'SL');
        $objPHPExcel->getActiveSheet()->setCellValue('C'.$rowNumber, 'Status');
        $objPHPExcel->getActiveSheet()->setCellValue('D'.$rowNumber, 'Expected payment date');
        $objPHPExcel->getActiveSheet()->setCellValue('E'.$rowNumber, 'Monthly Rent');
        $objPHPExcel->getActiveSheet()->setCellValue('F'.$rowNumber, 'Others');
        $objPHPExcel->getActiveSheet()->setCellValue('G'.$rowNumber, 'Arear Amount');
        $objPHPExcel->getActiveSheet()->setCellValue('H'.$rowNumber, 'Adjustment');
        $objPHPExcel->getActiveSheet()->setCellValue('I'.$rowNumber, 'S.D Adjust');

        $objPHPExcel->getActiveSheet()->setCellValue('J'.$rowNumber, 'Tax');
        $objPHPExcel->getActiveSheet()->setCellValue('K'.$rowNumber, 'Net Payment');
        $objPHPExcel->getActiveSheet()->setCellValue('L'.$rowNumber, 'Unadjusted Advance rent');
        $objPHPExcel->getActiveSheet()->setCellValue('M'.$rowNumber, 'Remarks');
        

        $objPHPExcel->getActiveSheet()->getStyle('A'.$rowNumber.':M'.$rowNumber)->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$rowNumber.':T'.$rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->getActiveSheet()->getStyle('J'.$rowNumber.':P'.$rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

        $rowNumber++;

        // table data
            $counter = 0;
            $sl = 1;
            $i = 0;
            foreach($result as $row){

            $net_payment_before_tax = ($row->monthly_rent_amount + $row->total_others_amount + $row->area_amount); // 9 nov 2017
            
            // tax calculation - 30 april 2018
            //old --- $tax_amount = ($net_payment_before_tax * $tax_rate->tax_amount)/100;
            for($si=0;$si<$slab_count;$si++){
                if($net_payment_before_tax >= $tax_slab_rate[$si]->min_amt && $net_payment_before_tax <= $tax_slab_rate[$si]->max_amt){
                    $tax_rate=$tax_slab_rate[$si]->tax_percent;
                }
            }
            $tax_amount = ($net_payment_before_tax * $tax_rate)/100;
            if($rent_agreement_row_data->tax_wived=='wived_yes'){
                    $tax_amount =0;
            }
            $new_net_payment = ($net_payment_before_tax -$tax_amount - $row->adjustment_adv) - $row->adjust_sec_deposit; // 9 nov 2017
            $unadjust_amount= $unadjust_amount - $row->adjustment_adv;
           
           if($row->unadjusted_adv_rent > 0 ){
            $calculated_unadjust =$unadjust_amount;
           }else{
            $calculated_unadjust=0;
           }
            

            $unadjust = $unadjust - $row->adjustment_adv;
            $prov_amount = 0;
           

            $date = date_create("$row->maturity_dt");
            $d = date_format($date, "d-M-y");

            if ($row->remarks != '') {
                $style_tr = 'background-color: lightgreen !important;';
            } else {
                $style_tr = '';
            }

            if($row->paid_sts !='paid'){
            	if(date("Y-m-d") > $row->maturity_dt) {
            	if($row->paid_sts =='advance')
							{ $paid_sts='Matured (Advance)';}
						else if($row->paid_sts =='unpaid' && $rent_agreement_row_data->agree_current_sts_id=='6')
							{ $paid_sts='Matured (Stop and Unpaid)';}
						else if($row->paid_sts =='stop' && $rent_agreement_row_data->agree_current_sts_id=='6')
							{ $paid_sts='Matured (Stoped)'; }
						else if($row->paid_sts =='stop' && $rent_agreement_row_data->agree_current_sts_id=='5' && $rent_agreement_row_data->point_of_payment=='cm')
							{ $paid_sts='Matured (Released and Unpaid)';}
					    else if($row->paid_sts =='unpaid' && $rent_agreement_row_data->agree_current_sts_id=='5')
							{ $paid_sts='Pending'; } 
					    else if($row->paid_sts =='stop' && $rent_agreement_row_data->agree_current_sts_id=='5' && $rent_agreement_row_data->point_of_payment=='pm')
							{ $paid_sts='Matured (Unpaid)';}
						else{$paid_sts=''; $sche_payment_type='unknown';} 
						  
                $prov_amount = 0.00;
                
                
	            }else{
	            	if(date("Y-m-d") > $row->schedule_strat_dt && $row->paid_sts !='stop' && $rent_agreement_row_data->point_of_payment=='pm'){
								$paid_sts='Not Matured (Accrual)';}
						
							else{
						    // not matured
							if($row->paid_sts =='advance'){ $paid_sts='Not Matured (Advance)'; }else{$paid_sts='Not Matured';}
							} 
	                //$paid_sts='Not Matured';
	                $prov_amount = 0.00;
	            }
                // if(date("Y-m-d") > $row->maturity_dt) {

                //     $paid_sts='Pending'; 
                //     $prov_amount = 0.00;
                    
                // }else{
                //     $paid_sts='Not Matured';
                //     $prov_amount = 0.00;
                // }
             
            }else{
                $paid_sts='Paid';
            }

                $objPHPExcel->getActiveSheet()->setCellValue('B'.$rowNumber, $sl);
                $objPHPExcel->getActiveSheet()->setCellValue('C'.$rowNumber, $paid_sts);
                $objPHPExcel->getActiveSheet()->setCellValue('D'.$rowNumber, $d);
                $objPHPExcel->getActiveSheet()->setCellValue('E'.$rowNumber, number_format($row->monthly_rent_amount,2));
                $objPHPExcel->getActiveSheet()->setCellValue('F'.$rowNumber, number_format($row->total_others_amount,2));
                $objPHPExcel->getActiveSheet()->setCellValue('G'.$rowNumber, number_format($row->area_amount,2));
                $objPHPExcel->getActiveSheet()->setCellValue('H'.$rowNumber, number_format($row->adjustment_adv,2));
                $objPHPExcel->getActiveSheet()->setCellValue('I'.$rowNumber, number_format($row->adjust_sec_deposit,2));

                $objPHPExcel->getActiveSheet()->setCellValue('J'.$rowNumber, number_format($tax_amount,2));
                $objPHPExcel->getActiveSheet()->setCellValue('K'.$rowNumber, number_format($new_net_payment,2));

                $objPHPExcel->getActiveSheet()->setCellValue('L'.$rowNumber, number_format($calculated_unadjust,2));
                $objPHPExcel->getActiveSheet()->setCellValue('M'.$rowNumber, $row->remarks);

                $objPHPExcel->getActiveSheet()->getStyle('A'.$rowNumber.':T'.$rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $objPHPExcel->getActiveSheet()->getStyle('J'.$rowNumber.':P'.$rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

                $rowNumber++;
                $i++;
                $sl++;

            }
          
        } 

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(8); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(16);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(14);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(14);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(12);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(16);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(12);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(12);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(16);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(16);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(16);
  
        
        $objPHPExcel->getActiveSheet()->getStyle('A1:Z'.$rowNumber)->getAlignment()->setWrapText(true); 
        $objPHPExcel->getActiveSheet()->getStyle('A1:Z'.$rowNumber)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

        foreach(range('D','M') as $columnID) {
            $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)
                ->setAutoSize(true);
        }
        //$objPHPExcel->getDefaultStyle()->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('B'.$start_row.':M'.$rowNumber)->applyFromArray($styleArray);

        $rowNumber++;
        $rowNumber++;
        $rowNumber++;
        $rowNumber++;
        $rowNumber++;
        $objPHPExcel->getActiveSheet()->setCellValue('B'.$rowNumber, '_______________________________');
        $objPHPExcel->getActiveSheet()->setCellValue('M'.$rowNumber, '_______________________________');
        $rowNumber++;
        $objPHPExcel->getActiveSheet()->setCellValue('B'.$rowNumber, 'Prepared by:');
        $objPHPExcel->getActiveSheet()->setCellValue('M'.$rowNumber, 'Checked & Confirmed by:');
        $rowNumber++;
        $objPHPExcel->getActiveSheet()->setCellValue('B'.$rowNumber, 'Finance');
        $objPHPExcel->getActiveSheet()->setCellValue('M'.$rowNumber, 'Finance');

        $objPHPExcel->getActiveSheet()->setTitle('Rent Scedule Live');

        $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
    
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);
        
        /** PHPExcel_IOFactory */
        require_once './application/Classes/PHPExcel/IOFactory.php';
        
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        header('Content-Type: application/vnd.ms-excel');  
        header('Content-Disposition: attachment;filename="rent_scedule_live'.date('Y-m-d h-i-s').'.xls"');   
        header('Cache-Control: max-age=0');
        $objWriter->save('php://output');   
        exit();
    }

    // old code may 8

    function old_get_schedule_info() {

        $result = $this->agreement_model->rent_schedule_info($this->input->post('rent_id'));
        $rent_agreement_row_data = $this->agreement_model->get_add_action_data_new($this->input->post('rent_id'));
        $rent_adjust_data = $this->agreement_model->single_rent_adjustment_get_info('', $this->input->post('rent_id'));
        $date_diff = $this->dateDiff($rent_agreement_row_data->agree_exp_dt, $rent_agreement_row_data->rent_start_dt);
        $start_date = date_create($rent_agreement_row_data->rent_start_dt);
        $end_date = date_create($rent_agreement_row_data->agree_exp_dt);
        $point_of_payment = $rent_agreement_row_data->point_of_payment;
        if ($point_of_payment == 'cm') {
            $pp_str = 'Current Month';
        } else {
            $pp_str = 'Following Month';
        }
        $html = '';


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
            table#t01 th    {
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
        $html.= '<p class="summery_class"><b>Period :</b> ' . date_format($start_date, "d/m/Y") . ' to ' . date_format($end_date, "d/m/Y") . ' (' . $date_diff . '), ' . $pp_str . ' Basis</p>';
        //  $html.= '<p class="summery_class"><b>10% increment every 02 years</b></p>'; 
        $html.= '<p class="summery_class"><b>Initial Rent :</b> ' . $rent_agreement_row_data->monthly_rent . '</p>';
        $html.= '<p class="summery_class"><b>Advance payment : </b>' . $rent_agreement_row_data->total_advance . '</p>';
        $html.= '<p class="summery_class"><b>Monthly Adjustment :</b> ' . $rent_adjust_data->percent_dir_val . '</p>';

        $html.= '<br />';
        $html .='<table class="" id="t01" style="border:solid 1px black; text-align: center; width:97%; margin:20px;">
    ';

        $html .='<tr class="headrow" style="text-align:center;font-weight:bold;background-color:#C5C5C5; border: 1px solid black;">
        <th style="text-align:center;">Expected payment date</th>
        <th style="text-align:center;"> Monthly Rent</th>
        <th style="text-align:center;"> Others </th>
        <th style="text-align:center;"> Adjustment</th>
        <th style="text-align:center;"> Tax </th>
        <th style="text-align:center;"> Net Payment </th>
        <th style="text-align:center;">Unadjusted Advance rent</th>
        <th style="text-align:center;">Remarks</th>';

        $html .='<tbody id="">';

        foreach ($result as $row) {

            if ($row->remarks != '') {
                $style_tr = 'background-color: lightgreen !important;';
            } else {
                $style_tr = '';
            }
            $html .='<tr style="border: 1px solid black ; ' . $style_tr . '" >';
            $html .='<td style="text-align:center;">' . $row->payment_date . '</td>';
            $html .='<td style="text-align:center;">' . $row->monthly_rent . '</td>';
            $html .='<td style="text-align:center;">' . $row->others_rent . '</td>';
            $html .='<td style="text-align:center;">' . $row->adjustment_amount . '</td>';
            $html .='<td style="text-align:center;">' . $row->tax_amount . '</td>';
            $html .='<td style="text-align:center;">' . $row->net_payment . '</td>';
            $html .='<td style="text-align:center;">' . $row->unadjusted_adv_rent . '</td>';
            $html .='<td style="text-align:center;">' . $row->remarks . '</td>';


            $html .='</tr>';
        }

        $html .='</tbody></table>';
        echo $html;
    }

    function increment_ajax(){
        $counter_others = $this->input->post('counter_others_rent_type');
        $arr_other_name = array();
        $arr_other_id = array();
        $id_list = array();
        for ($i = 1; $i <= $counter_others; $i++) {

            // unset($id_list);
            if ($this->input->post('delete_others' . $i) != '1') {

                $arr_other_id[] = $i;
                $id_list[] = $arr_other_name[] = $this->input->post('rent_others_id' .$i);
                $id_list_final = implode(',', $id_list);
            }
        }
        
        $element_number = count($arr_other_name);

        // table 
        if($this->input->post('incr_start_dt') !='' ){  $start = $this->input->post('incr_start_dt'); }
        else{ 
            $start = $this->input->post('rent_start_dt');
        }
        
        // $start = '01/07/2018';
        // echo $start;
        $exp = $this->input->post('agree_exp_dt');
        $monthly_rent = $this->input->post('monthly_rent');
        $a = explode('/', $start);

        $date_day = $a[0];
        $date_month = $a[1];
        $date_year = $a[2];
        $start_date_year = $a[2];
		//print_r($a) ;
        if ($this->input->post('rent_start_dt')) {
            $start= $rent_start_dt = date('Y-m-d', strtotime(str_replace('/', '-', $start)));
        }
        if ($this->input->post('agree_exp_dt')) {
           $exp=  $agree_exp_dt = date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('agree_exp_dt'))));
        }

        // $start = $month = strtotime($start);
        // $end = strtotime($exp);

        $d1 = new DateTime($start);
        //$d1 = new DateTime('2018-02-01');
        $d2 = new DateTime($exp);
        $diff = $d2->diff($d1);
        $year_diff = $diff->y;
        $month_no = $diff->m;
        if($month_no==0){  $year_diff= $year_diff -1;}


        $interval = new DateInterval('P1M');
        $d2->modify('last day of this month');
        $period = new DatePeriod($d1, $interval, $d2);
        $last_iteration = iterator_count($period);
        

        $html = '';
        //    $html.= 'Select an option: '; 
        $html.= '

        <style>
      
        table, th, td {
            
            border-collapse: collapse;
        }

        table#increment_tbl th, td {
            padding-bottom: 3px;
            padding-top: 3px;
           
        }
       
        table#increment_tbl tr:nth-child(even) {
            background-color: #E5AAAA;
        }
        table#increment_tbl tr:nth-child(odd) {
           background-color:#BDBDBD;
        }
        table#increment_tbl th    {
            background-color: #F2F5A9;
            //color: white;
        }

       table#increment_tbl p {
        margin: 0;
        padding: 0;
        }
       
        </style>

        ';

        $html.= '<br />';
        $html .='<table class="register-table" id="increment_tbl"  style="border:solid 1px black; text-align: center; width:97%; margin:20px;">';

        $html .='<tr class="headrow" style="text-align:center; height: 35px; font-weight:bold;background-color:#C5C5C5; border: 1px solid black;"><th>Start From</th> <th>Year</th><th> <p>Incremented </p>Dir/ % </th><th>Monthly Rent</th>';

        $j = 0;
        // print_r($arr_other_name);
        // exit();
        foreach ($arr_other_name as $single_arr_other_name) {
            if($single_arr_other_name!=''){
                $html.='<th> <p>Incremented</p> Dir / % </th>';
                $html.='<th>' . $single_arr_other_name . '</th>';
                $j++;
            }    
        }
        $html.='</tr>';

        $html .='<tbody id="register-table">';
        $html .='<input id="count_year" name="count_year" type="hidden" value="'.$year_diff.'" />';
        $number =1;
        $ends = array('th','st','nd','rd','th','th','th','th','th','th');
    for ($i = 0; $i <= $year_diff; $i++) {
            
	          if (($number %100) >= 11 && ($number%100) <= 13)
	            $abbreviation = $number. 'th';
	          else
	            $abbreviation = $number. $ends[$number % 10];      
            

            $sch_start_date = $date_day . '-' . $date_month . '-' . $date_year++;
            $sch_start_date2 = $date_day . '-' . $date_month . '-' . $date_year;
            //$sch_start_date1= $date_year.'-'.$date_month.'-'.$date_day;

            $sch_end_date = date('d-m-Y', strtotime($sch_start_date2 . ' -1 day'));
            //  $exp_date= date('d-m-Y', strtotime($exp .' +0 day'));

            $sch_end_date_t = strtotime($sch_end_date);
            $exp_date = strtotime($exp);
            if ($exp_date < $sch_end_date_t) {
                $sch_end_date = date('d-m-Y', $exp_date);
            }

    $html.= '<script>


    jQuery("#increment_tr'.$i.'").hide();
    //jQuery(".incre_tr_cls").hide();
    jQuery("#monthly_rent").val();
    var monthly_rent= jQuery("#monthly_rent").val();
    jQuery(\'.mon_val \').val(monthly_rent);
    
    jQuery(".inc_select'.$i.'").on(\'change\',function() {  

            var incr_type_read= jQuery(".inc_select' . $i . ' option:selected").val();
            if (incr_type_read==\'per_rent\' || incr_type_read==\'dir_rent\'){ 
            jQuery("#rent_amount_val' . $i . '").attr("readonly", false);
            }else if(incr_type_read==\'\'){

               
                jQuery("#rent_amount_val' . $i . '").attr("readonly", true);
            }
            var j =  '.$i.';
            for(var i=0; i<j;i++){
                jQuery("#rent_amount_val"+i).attr("readonly", true);
                jQuery(".inc_select"+i).prop(\'disabled\', \'disabled\');
            }


     });

            var incr_row_numbe = '.$year_diff.';
            var incr_row_number = parseInt(incr_row_numbe) +  1;
            
              jQuery(".inc_select'.$i.'").on(\'focus\', function () {
                    
                    previous = this.value;
                }).change(function() { 

                        //jQuery("#rent_amount_val'.$i.'").on("change",function(){

                        var i =  '.$i.';
                     
                        var incr_type= jQuery(".inc_select'.$i.' option:selected").val();
                          
                        if (incr_type==\'per_rent\' || incr_type=="dir_rent"){

                            var t=0;
                                if(i>0){
                                    t = i-1;
                                    var cal_val=  jQuery("#cal_rent_val"+t).val();

                                }else{
                                    t=0;
                                    var cal_val=  monthly_rent;
                                }
                              //alert(t);
                                var old_val = parseFloat(jQuery("#rent_amount_val"+i).val());
                                var deducted_val_for_percent = parseFloat(jQuery("#cal_rent_val"+i).val()) - cal_val;
                                
                                    for(var start=i; start<incr_row_number; start++){
                            
         
                                                if(previous==\'dir_rent\'){
                                                    jQuery("#rent_amount_val"+i).val("0.00");
                                                    var deducted_val = parseFloat(jQuery("#cal_rent_val"+start).val()) - old_val;
                                                    jQuery("#cal_rent_val"+start).val(parseFloat(deducted_val));
                                                    //jQuery("#rent_amount_val"+start).val("0.00");
                                                    jQuery("#rent_amount_val"+i).attr("readonly", true);

                                                }else{ // per_rent

                                                    jQuery("#rent_amount_val"+i).val("0.00");
                                                    var deducted_val = parseFloat(jQuery("#cal_rent_val"+start).val()) - deducted_val_for_percent;
                                                    jQuery("#cal_rent_val"+start).val(parseFloat(deducted_val));
                                                    jQuery("#rent_amount_val"+i).attr("readonly", true);

                                               }
                                    }

                                    jQuery("#rent_amount_val"+i).attr("readonly", false);
                                    jQuery("#rent_amount_val"+i).val("0.00");
                                   

                        }else if(incr_type==""){
                
                            var t=0;
                                if(i>0){
                                    t = i-1;
                                    var cal_val=  parseFloat(jQuery("#cal_rent_val"+t).val());

                                }else{
                                    t=0;
                                    var cal_val=  parseFloat(monthly_rent);

                                }
                             
                                var old_val = parseFloat(jQuery("#rent_amount_val'.$i.'").val());
                            
                                var deducted_val_for_percent = parseFloat(jQuery("#cal_rent_val"+i).val()) - cal_val;


                                for(var start=i; start<incr_row_number; start++){
                  
                                    
                                        var deducted_val = parseFloat(jQuery("#cal_rent_val"+start).val()) - parseFloat(old_val);
                                        
                                       if(previous==\'dir_rent\'){
                                        jQuery("#cal_rent_val"+start).val(parseFloat(deducted_val));
                                        //jQuery("#rent_amount_val"+start).val("0.00");
                                        jQuery("#rent_amount_val"+i).attr("readonly", true);
                                        jQuery("#rent_amount_val"+i).val("0.00");

                                       }else{ // per_rent
            
                                        jQuery("#rent_amount_val"+i).val("0.00");
                                        var deducted_val = parseFloat(jQuery("#cal_rent_val"+start).val()) - deducted_val_for_percent;
                                        jQuery("#cal_rent_val"+start).val(parseFloat(deducted_val));
                                        jQuery("#rent_amount_val"+i).attr("readonly", true);

                                       }

                                }

                        }

                //});

            });


             jQuery("#rent_amount_val'.$i.'").on("change",function(){
                var i='.$i.';
               var incr_type= jQuery(".inc_select"+i+" option:selected").val();
           
               if (incr_type=="per_rent"){
                    var t=0;
                    if(i>0){
                        t = i-1;
                        var mid_val=  jQuery(".tot_val_"+t).val();

                    }else{
                        t=0;
                        var mid_val=  monthly_rent;

                    }
              
                      var incr_per = jQuery(this).val();
                      total_monthly_rent_amount= +mid_val + +mid_val *(incr_per/100);
                      var j= i;
                      var k= incr_row_number;
                      for(var a=j;a<=k;a++){
                            jQuery(".tot_val_"+a).val(parseFloat(total_monthly_rent_amount).toFixed(2));
                     }

                }

                else if(incr_type=="dir_rent"){
                        var t=0;
                    if(i>0){
                        t = i-1;
                        var mid_val=  jQuery(".tot_val_"+t).val();

                    }else{
                        t=0;
                        var mid_val=  monthly_rent;

                    }
              
                      var incr_per = jQuery(this).val();
                      total_monthly_rent_amount= +mid_val + +incr_per;
                      
                      var j= i;
                      var k= incr_row_number;
                      jQuery(".tot_val_"+j).val(parseFloat(total_monthly_rent_amount).toFixed(2));
                    for(var a=j+1;a<k;a++){

                        var incr_type= jQuery(".inc_select"+a+ " option:selected").val();
                        var dir_amt= parseFloat(total_monthly_rent_amount) + +jQuery("#rent_amount_val"+a).val();
                            
                        if(incr_type==\'dir_rent\'){
                            var dir_amt= parseFloat(total_monthly_rent_amount) + +jQuery("#rent_amount_val"+a).val();
                           
                            jQuery(".tot_val_"+a).val(parseFloat(dir_amt).toFixed(2));
                        
                        }else{
                            jQuery(".tot_val_"+a).val(parseFloat(dir_amt).toFixed(2));
                            
                        }

                    }

                }
     });

 
            </script>';

            //$html .='<input id="count_year" name="count_year" type="hidden" value="' . $year_diff . '" />';
            $html .='<input id="element_number" name="element_number" type="hidden" value="' . $element_number . '" />';
           
            $html .='<input name="date_day" type="hidden" value="'.$date_day.'" />';
            $html .='<input name="date_month" type="hidden" value="'. $date_month.'" />';
            $html .='<input name="year_sl' . $i . '" type="hidden" value="' . $start_date_year++ . '" />';
            $html .='<input name="year_sl_end' . $i . '" type="hidden" value="' .date_format(date_create("$sch_end_date"),"Y-m-d "). '" />';
           
            $html .='<input name="incr_change_sts" type="hidden" value="1" />';

            $html .='<tr id="increment_tr'.$i.'" class="incre_tr_cls"   style="border: 1px solid black ;">';
            //$html .='<td style="text-align:center;">'.date_format(date_create("$sch_start_date"),"d-M-Y").' to '.date_format(date_create("$sch_end_date"),"d-M-Y ").'  </td>';
            $html .='<td style="text-align:center;">'.date_format(date_create("$sch_start_date"),"d-M-Y").'  </td>';
            $html .='<td style="text-align:center;">' .$abbreviation. ' Year </td>';
            $html .='<td style="text-align:center;">
                                <select name="rent_amount_type'.$i.'" class="inc_select'.$i.'  common_inc_cls" id="">
                                        <option value="">No Increment</option>
                                        <option  value="per_rent">Percentage (%)</option>
                                        <option  value="dir_rent">Direct</option>

                                    </select>
                                    <input type="text"  name="rent_amount_val'.$i.'" id="rent_amount_val'.$i.'"  class="incr_input number" value="0.00"  readonly/>

                    </td>';
            $html .='<td style="text-align:center;"><input type="text" style="text-align:right;" name="cal_rent_val'.$i.'" id="cal_rent_val'.$i.'"  class="inc_cls  tot_val_' . $i . ' number mon_val"  value="" readonly/></td>';
            $j = 0;
            $k = 1;
            foreach ($arr_other_name as $single_arr_other_name) {
                    if($single_arr_other_name!=''){

                        $html.= '<script>
                        var others_amount_val= jQuery("#others_type_amount_percentage'.$k.'").val();
                            if(others_amount_val==\'\') { 
                                jQuery(".others_input_value'.$j.'").val(0.00); 
                            }else{
                                jQuery(".others_input_value'.$j.'").val(others_amount_val);
                            }

                        jQuery(".others_amount_type'.$i.$j.'").on(\'focus\', function () {
                    
                            previous = this.value;
                        }).change(function() { 
                            var others_amount_val= jQuery("#others_type_amount_percentage' . $k . '").val();
                            var incr_type= jQuery(".others_amount_type'.$i.$j.' option:selected").val();
                            if(incr_type==""){
                                var previous_val = previous;
                                var i_otr='.$i.';
                                var j_otr='.$j.';

                                
                                var t=0;
                                if(i_otr>0){
                                    t = i_otr-1;
                                    var cal_val=  parseFloat(jQuery(".other_tot_val_"+t+j_otr).val());

                                }else{
                                    t=0;
                                    var cal_val=  parseFloat(others_amount_val);

                                }
                                 var old_val = parseFloat(jQuery("#others_amount_val'.$i.$j.'").val());
                                 var deducted_val_for_percent = parseFloat(jQuery(".other_tot_val_'.$i.$j.'").val()) - cal_val;
                                 
    
                                          var j='.$i.';
                                          var k='.$year_diff.';
                                          for(var a=j;a<=k;a++){
                                                var deducted_val = parseFloat(jQuery(".other_tot_val_"+a+'.$j.').val()) - parseFloat(old_val);
                                              
                                                if(previous_val==\'dir_otr\'){
                                                    
                                                    jQuery(".other_tot_val_"+a+'.$j.').val(parseFloat(deducted_val));

                                                }else{
                                                    
                                                    var deducted_val_percent = parseFloat(jQuery(".other_tot_val_"+a+'.$j.').val()) - deducted_val_for_percent;
                                                    jQuery(".other_tot_val_"+a+'.$j.').val(parseFloat(deducted_val_percent));

                                                }
                                                  
                                           }
                            }else{

                            }

                            var others_amount_val= jQuery("#others_type_amount_percentage' . $k . '").val();
                            var others_incr_type_read = jQuery(".others_amount_type' . $i . $j . ' option:selected").val();

                            if (others_incr_type_read==\'per_otr\' || others_incr_type_read==\'dir_otr\'){ 
                            jQuery("#others_amount_val' . $i . $j . '").attr("readonly", false);
                            }else if(others_incr_type_read==\'\'){

                                jQuery("#others_amount_val' . $i . $j . '").val("0.00");
                                jQuery("#others_amount_val' . $i . $j . '").attr("readonly", true);
                            }
                            
                            var e =  '.$i.';
                            var f =  '.$j.';
                            for(var i=0; i<e;i++){
                                jQuery("#others_amount_val"+i+f).attr("readonly", true);
                                jQuery("#others_amount_type"+i+f).prop(\'disabled\', \'disabled\');
                            }

                        }); 


                        jQuery(".others_amount_val'.$i.$j.'").on("change",function(){
                           

                               var incr_type= jQuery(".others_amount_type'.$i.$j.' option:selected").val();
                          
                                if (incr_type==\'per_otr\'){

                                    var i='.$i.';
                                    var j='.$j.';
                                    var t=0;
                                    if(i>0){
                                        t = i-1;
                                        var mid_val=  jQuery(".other_tot_val_"+t+j).val();

                                    }else{
                                        t=0;
                                        var mid_val=  jQuery(".other_tot_val_"+t+j).val();

                                    }
                                
                                   // var mid_val=  jQuery(\'.other_tot_val_' . $i . $j . ' \').val();
                                    var incr_per = jQuery(this).val();
                                    total_monthly_rent_amount= +mid_val + +mid_val *(incr_per/100);

                                    if(jQuery("#increment_type option:selected").val()==\'3\'){
                                       jQuery(\'.other_tot_val_' . $i . $j . ' \').val(total_monthly_rent_amount.toFixed(2));
                                    }else{  
                                                  var j=' . $i . ';
                                                  var k=' . $year_diff . ';
                                                  for(var a=j;a<=k;a++){
                                                      jQuery(".other_tot_val_"+a+' . $j . ').val(total_monthly_rent_amount.toFixed(2));
                                                      
                                                      var after_clc= jQuery(".other_tot_val_"+a+' . $j . ').val();
                                                      var total_others= jQuery("#others_total' . $i . '").val();
                                                      total_others = +total_others + +after_clc;
                                                      jQuery("#others_total' . $i . '").val(total_others);
                                                   }
                                        }        

                                }else if(incr_type==\'dir_otr\'){

                                    var i='.$i.';
                                    var j='.$j.';
                                    var t=0;
                                    if(i>0){
                                        t = i-1;
                                        var mid_val=  jQuery(".other_tot_val_"+t+j).val();

                                    }else{
                                        t=0;
                                        var mid_val=  jQuery(".other_tot_val_"+t+j).val();

                                    }

                                    var incr_per = jQuery(this).val();
                                    total_monthly_rent_amount= +mid_val + +incr_per;
                                     

                                      var j=' . $i . ';
                                      var k=' . $year_diff . ';
                                      for(var a=j;a<=k;a++){
                                        jQuery(".other_tot_val_"+a+'.$j.').val(total_monthly_rent_amount.toFixed(2));
                                      }


                                      var after_clc= jQuery(".other_tot_val_' . $i . $j . '").val();
                                      var total_others= jQuery("#others_total' . $i . '").val();
                                      
                                      total_others = +total_others + +after_clc;
                                      jQuery("#others_total' . $i . '").val(total_others);

                                }

                        });

                 
                            </script>';


                                $html .='<td style=" padding: 5px;"> 
                                                <select name="others_amount_type' . $i . $j . '" class="others_amount_type'.$i.$j.'  common_other_cls" id="others_amount_type' . $i . $j . '" style="width: 110px; margin-right:5px;">
                                                        <option value="">No Increment</option>
                                                        <option value="per_otr">Percentage (%)</option>
                                                        <option value="dir_otr">Direct</option>
                                                </select>

                                <input type="text"  name="others_amount_val'.$i.$j.'" id="others_amount_val' . $i . $j . '"  class="incr_input number  others_amount_val' . $i . $j . '"  value="0.00"    readonly/>
                                </td>';
                                $html .='<td style="text-align:center;"><input type="text" style="text-align:right;" name="cal_others_val' . $i . $j . '" id="others_rent"  class="others_input_value' . $j . ' number  other_tot_val_' . $i . $j . ' others_input_style"   readonly /></td>';
                                $j++;
                                $k++;
                            
                        }
    }
            // others_rent
            $html .='<td style="text-align:center;"><input type="hidden"  name="" id="" value="" class="incr_input"/><input type="hidden"  name="id_list_final" id="id_list_final" value="' . $id_list_final . '" class=""/></td>';
            //$html .='<td style="text-align:center;"><input type="text"  name="end_date'.$i.'" id="rent_end_date" value="'.$sch_end_date.'" class="incr_input"  readonly/></td>';
            $html .='</tr>';
      $number++;
            }

        $html .='</tbody></table>';
        echo $html;
    }


function increment_ajax_for_area() {
    $add_edit='edit';
    $id= $this->input->post('rent_id');
    $arear_sts= $this->input->post('arear_sts');
    $rent_start_dt= $this->input->post('rent_start_dt');
    $agree_exp_dt=  $this->input->post('agree_exp_dt');
    $location_owner= $this->input->post('location_owner');
    $monthly_rent_amt= $this->input->post('monthly_rent_amt');

    $rent_inc_result = $this->agreement_model->rent_inc_adj_get_info($add_edit, $id);

    if($location_owner!='own'){

        $counter_others = $rent_inc_result[0]->others_id_list;
        $arr_other_name = explode(",",$counter_others);
        
        $element_number = count($arr_other_name);
        $incr_row_number = count($rent_inc_result);

         $d1 = new DateTime($rent_start_dt);
         $d2 = new DateTime($agree_exp_dt);
         $diff = $d2->diff($d1);
         $year_diff = $diff->y;
         $month_no = $diff->m;
        if($month_no==0){  $year_diff= $year_diff -1;}
    
        $html = '';
     

        $html.= '<br />';
        $html .='<table class="register-table" id="increment_tbl"  style="border:solid 1px black; text-align: center; width:97%; margin:20px;">';

        $html .='<tr class="headrow" style="text-align:center;font-weight:bold;background-color:#C5C5C5; border: 1px solid black;"><th>Year</th><th> Dir/ % </th><th>Monthly Rent</th>';

        $j = 0;
       
            if($counter_others!=''){
                foreach ($arr_other_name as $single_arr_other_name) {
                    if($single_arr_other_name!=''){
                        $html.='<th>Dir / % </th>';
                        $html.='<th>' . $single_arr_other_name . '</th>';
                        $j++;
                    }    
                }
            }
        $html.='</tr>';

        $html .='<tbody id="register-table">';
        $html .='<input id="count_year" name="count_year" type="hidden" value="' . $year_diff . '" />';
        $html .='<input id="incr_row_number" name="incr_row_number" type="hidden" value="'.$incr_row_number.'" />';
        $html .='<input id="monthly_rent_amt" name="monthly_rent_amt" type="hidden" value="'.$monthly_rent_amt.'" />';
     
		$number =1;
		$ends = array('th','st','nd','rd','th','th','th','th','th','th');
		$i=0;
                foreach ($rent_inc_result as $row) {
            
                      if (($number %100) >= 11 && ($number%100) <= 13)
                        $abbreviation = $number. 'th';
                      else
                        $abbreviation = $number. $ends[$number % 10]; 
						$html .='<input id="element_number" name="element_number" type="hidden" value="'.$element_number.'" />';
						$html .='<input id="counter_others" name="counter_others" type="hidden" value="'.$counter_others.'" />';
					   
						$html .='<input name="year_sl' . $i . '" type="hidden" value="' . $row->rent_incr_yr . '" />';
						$html .='<input name="incr_change_sts" type="hidden" value="0" />';
	
						$html .='<tr id="increment_tr'.$i.'" class="incre_tr_cls"   style="border: 1px solid black ;">';
						$html .='<td style="text-align:center;">'.$abbreviation.' Year </td>';
						$html .='<td >
                                        <select name="rent_amount_type' . $i . '" class="inc_select'.$i.' common_inc_cls" id="" style="width: 97px; margin-right:5px;">';

                                        $html .='<option ';  if( $row->rent_amount_type == "") { $html .=' selected="selected"';  } else { $html .=''; }   $html .=' value="">No Increment</option>';
                                        $html .='<option ';  if( $row->rent_amount_type == "per_rent") { $html .=' selected="selected"';  } else { $html .=''; }  $html .='  value="per_rent" >Percentage (%)</option>';
                                        $html .='<option ';  if( $row->rent_amount_type == "dir_rent") { $html .=' selected="selected"';  } else { $html .=''; }  $html .='  value="dir_rent" >Direct</option>';

                                $html .='</select>';
                                $html .=' <input type="text"  name="rent_amount_val' . $i . '" id="rent_amount_val' . $i . '"  class=" incr_input number " value="'.$row->rent_amount_val.'"  readonly/>

                            </td>';
                    $html .='<td style="text-align:center;"><input type="text"  name="cal_rent_val' . $i . '" id="cal_rent_val' . $i . '"  class="inc_area_cal inc_cls  tot_val_' . $i . ' number mon_val"  value="'.$row->cal_rent_val.'"  readonly/></td>';
                    $j = 0;
                    $k = 1;
                    if($arear_sts==0){
                        $cal_others_val_arr = explode(",",$row->cal_others_val);
                        $others_amount_val_arr = explode(",",$row->others_amount_val);
                        $others_amount_type_arr = explode(",",$row->others_amount_type);
                    }else{

                        // if arear=1
                        $cal_others_val_arr = explode("#",$row->cal_others_val);
                        $others_amount_val_arr = explode("#",$row->others_amount_val);
                        $others_amount_type_arr = explode("#",$row->others_amount_type);
                    }
                    


                    $others_count = count($cal_others_val_arr);
                    if($others_count > 1){
                        $others_style_str = 'width: 97px; margin-right:5px;';
                    }else{
                        $others_style_str = 'width: 110px; margin-right:5px;';
                    }
                    
                        if($row->cal_others_val!=''){
                            foreach ($cal_others_val_arr as $key=> $single_cal_others_val) {
								$html .='<td style=" padding: 5px;"> 
                                                    <select name="others_amount_type' . $i . $j . '" class="others_amount_type'.$i.$j.' common_other_cls" id="others_amount_type' . $i . $j . '" style="'.$others_style_str.'">';
                                                             
                                                                $html .= '<option '; if( $others_amount_type_arr[$key] == "") { $html .=' selected="selected"';  } else { $html .=''; }   $html .=' value="">No Increment</option>';
                                                                $html .= '<option '; if( $others_amount_type_arr[$key] == "per_otr") { $html .=' selected="selected"';  } else { $html .=''; } $html .= ' value="per_otr">Percentage (%)</option>';
                                                                $html .= '<option '; if( $others_amount_type_arr[$key] == "dir_otr") { $html .=' selected="selected"';  } else { $html .=''; } $html .= ' value="dir_otr">Direct</option>';
                                        $html .= '</select>';

                                        $html .= '<input type="text"  name="others_amount_val' . $i . $j . '" id="others_amount_val' . $i . $j . '"  class="incr_input number  others_amount_val'.$i.$j.'"  value="'.$others_amount_val_arr[$key].'"    readonly/>
                                        </td>';
                                        $html .='<td style="text-align:center;"><input type="text"  name="cal_others_val' . $i . $j . '" id="others_rent"  class="inc_area_cal others_input_value'.$j.' number  other_tot_val_' . $i . $j . ' others_input_style" value="'.$single_cal_others_val.'"   readonly/></td>';
                                        
                                        $html.= '<script>

                                        incr_edit_jquery_3('.$i.','.$j.','.$k.','.$year_diff.');
                                        var prev_val= jQuery(\'.others_amount_val' . $i . $j . ' \').val();
                                        jQuery(".others_amount_type'.$i.$j.'").on(\'focus\', function () {
        
                                            previous = this.value;
                                            }).change(function() { 
                                                     var t=0;
                                                     var i= '.$i.';
                                                  
                                                     if(i >0){
                                                         t = i-1;
                                                         t = parseInt(t);
                                                          var cal_val=  jQuery(".other_tot_val_"+t+"'.$j.'").val();
                                                     }else{
                                                         t=0;
                                                         var cal_val=    jQuery(".other_tot_val_'.$i.$j.'").val();
                                                     }
                                                    var old_val = (jQuery("#others_amount_val'.$i.$j.'").val());
                                                    var deducted_val_for_percent = parseFloat(jQuery(".other_tot_val_'.$i.$j.'").val()) - cal_val;
                                                    var others_amount_val= jQuery("#others_type_amount_percentage'.$k.'").val();
                                                    var others_incr_type_read = jQuery(".others_amount_type'.$i.$j.' option:selected").val();

                                                    if (others_incr_type_read==\'per_otr\' || others_incr_type_read==\'dir_otr\'){ 

                                                            jQuery("#others_amount_val' . $i . $j . '").attr("readonly", false);
                                                            //jQuery(".others_input_value' . $j . '").val(others_amount_val);
                                                            jQuery("#others_amount_val' . $i . $j . '").val("0.00");
                                                                          var j=' . $i . ';
                                                                          var k=' . $year_diff . ';
                                                                          for(var a=j;a<=k;a++){
                                                                                if(previous==\'dir_otr\'){


                                                                                  var deducted_val = parseFloat(jQuery(".other_tot_val_"+a+'.$j.').val()) - old_val;
                                                                                  jQuery(".other_tot_val_"+a+'.$j.').val(parseFloat(deducted_val));
                                                                                 

                                                                                }else if(previous==\'per_otr\'){
                                                                                    var deducted_val =  parseFloat(jQuery(".other_tot_val_"+a+"'.$j.'").val())- deducted_val_for_percent;
                                                                                       // alert(j);
                                                                                        //alert(k);
                                                                                    jQuery(".other_tot_val_"+a+'.$j.').val(parseFloat(deducted_val));
                                                                                }


                                                                          
                                                                      }


                                                    }else if(others_incr_type_read==\'\'){
                                                        

                                                            var previous_val = previous;
                                                            var i_otr='.$i.';
                                                            var j_otr='.$j.';

                                                            
                                                            var t=0;
                                                            if(i_otr>0){
                                                                t = i_otr-1;
                                                                var cal_val=  parseFloat(jQuery(".other_tot_val_"+t+j_otr).val());

                                                            }else{
                                                                t=0;
                                                                var cal_val=  parseFloat(others_amount_val);

                                                            }
                                                             var old_val = parseFloat(jQuery("#others_amount_val'.$i.$j.'").val());
                                                             var deducted_val_for_percent = parseFloat(jQuery(".other_tot_val_'.$i.$j.'").val()) - cal_val;
                                                             
                                
                                                                      var j='.$i.';
                                                                      var k='.$year_diff.';
                                                                      for(var a=j;a<=k;a++){
                                                                            var deducted_val = parseFloat(jQuery(".other_tot_val_"+a+'.$j.').val()) - parseFloat(old_val);
                                                                           
                                                                            if(previous_val==\'dir_otr\'){
                                                                                
                                                                                jQuery(".other_tot_val_"+a+'.$j.').val(parseFloat(deducted_val));

                                                                            }else{
                                                                                
                                                                                var deducted_val_percent = parseFloat(jQuery(".other_tot_val_"+a+'.$j.').val()) - deducted_val_for_percent;
                                                                                jQuery(".other_tot_val_"+a+'.$j.').val(parseFloat(deducted_val_percent));

                                                                            }
                                                                              
                                                                       }

                                                        jQuery(".others_input_value' . $j . '").val(others_amount_val);
                                                        jQuery("#others_amount_val' . $i . $j . '").val("0.00");
                                                        jQuery("#others_amount_val' . $i . $j . '").attr("readonly", true);
                                                    }

                                        }); 

                                        jQuery(".others_amount_val' . $i . $j . ' ").on("focusin", function(){
                                            
                                           jQuery(this).data("val", jQuery(this).val());
                                        });

                                                    jQuery(".others_amount_val' . $i . $j . ' ").on("change",function(){

                                                       var incr_type= jQuery(".others_amount_type' . $i . $j . ' option:selected").val();
                                                       var prev_given_value = jQuery(this).data("val");
                                                       var current_given_value = jQuery(this).val();

                                                    if (incr_type==\'per_otr\'){
                                                       var mid_val=  jQuery(\'.other_tot_val_' . $i . $j . ' \').val();
                                                       mid_val = mid_val - +mid_val *(prev_given_value/100);
                                                       var incr_per = jQuery(this).val();
                                                       total_monthly_rent_amount= +mid_val + +mid_val *(incr_per/100);

                                                          if(jQuery("#increment_type option:selected").val()==\'3\'){
                                                               jQuery(\'.other_tot_val_' . $i . $j . ' \').val(total_monthly_rent_amount.toFixed(2));
                                                          } else{  


                                                                          var j=' . $i . ';
                                                                          var k=' . $year_diff . ';
                                                                          for(var a=j;a<=k;a++){
                                                                          jQuery(".other_tot_val_"+a+' . $j . ').val(total_monthly_rent_amount.toFixed(2));
                                                                          
                                                                          var after_clc= jQuery(".other_tot_val_"+a+' . $j . ').val();
                                                                          var total_others= jQuery("#others_total' . $i . '").val();
                                                                          total_others = +total_others + +after_clc;
                                                                          jQuery("#others_total' . $i . '").val(total_others);
                                                                    }
                                                            }        

                                            }
													else if(incr_type==\'dir_otr\'){
														var mid_val=  jQuery(\'.other_tot_val_' . $i . $j . ' \').val();
														mid_val = mid_val- prev_given_value;
														var incr_per = jQuery(this).val();
														total_monthly_rent_amount= +mid_val + +incr_per;
													   	if(jQuery("#increment_type option:selected").val()==\'3\'){
														   jQuery(\'.other_tot_val_' . $i . $j . ' \').val(total_monthly_rent_amount.toFixed(2));
													  	} 
													  	else{ 
														  var j=' . $i . ';
														  var k=' . $year_diff . ';
														  for(var a=j;a<=k;a++){
															jQuery(".other_tot_val_"+a+' . $j . ').val(total_monthly_rent_amount.toFixed(2));
														  }
													  	}

													  var after_clc= jQuery(".other_tot_val_' . $i . $j . '").val();
													  var total_others= jQuery("#others_total' . $i . '").val();
													  total_others = +total_others + +after_clc;
													  jQuery("#others_total' . $i . '").val(total_others);
                                                }
                                            });
                                                </script>';
                                        $j++;
                                        $k++;
                            }
                        }

                        $html .='</tr>';
                        $html.= '<script>
                                 incr_edit_jquery_1('.$i.','.$incr_row_number.');
                                </script>';
                        $number++;
                        $i++;
                }

                $html .='</tbody></table>';
                echo $html;

    }else{
            $html=''; 
            echo $html;
    }

}



	function increment_ajax_edit($rent_inc_result, $id, $rent_start_dt,$agree_exp_dt,$location_owner ) {
    if($location_owner!='own'){

         $counter_others = $rent_inc_result[0]->others_id_list;
         $arr_other_name = explode(",",$counter_others);
        

        $element_number = count($arr_other_name);
        $incr_row_number = count($rent_inc_result);

         $d1 = new DateTime($rent_start_dt);
         $d2 = new DateTime($agree_exp_dt);
// 20 may 2018
        $a = explode('-', $rent_start_dt);
        $date_day = $a[2];
        $date_month = $a[1];
        $date_year = $a[0];
        $start_date_year = $a[0];


// 20 may 2018 end
         $diff = $d2->diff($d1);
         $year_diff = $diff->y;
         $month_no = $diff->m;
         if($month_no==0){  $year_diff= $year_diff -1;}
     
        $html = '';
        //    $html.= 'Select an option: '; 

        $html.= '<br />';
        $html .='<table class="register-table" id="increment_tbl"  style="border:solid 1px black; text-align: center; width:97%; margin:20px;">';

        $html .='<tr class="headrow" style="text-align:center;font-weight:bold;background-color:#C5C5C5; border: 1px solid black;"><th>Start From</th><th>Year</th><th> Dir/ % </th><th>Monthly Rent</th>';

        $j = 0;
       
    if($counter_others!=''){
        foreach ($arr_other_name as $single_arr_other_name) {
            if($single_arr_other_name!=''){
                $html.='<th>Dir / % </th>';
                $html.='<th>' . $single_arr_other_name . '</th>';
                $j++;
            }    
        }
    }
        $html.='</tr>';



        $html .='<tbody id="register-table">';
        $html .='<input id="count_year" name="count_year" type="hidden" value="' . $year_diff . '" />';
        //$html .='<input id="count_year" name="count_year" type="hidden" value="' . $incr_row_number . '" />';
            $number =1;
            $ends = array('th','st','nd','rd','th','th','th','th','th','th');
            $i=0;
    foreach ($rent_inc_result as $row) {
            
                      if (($number %100) >= 11 && ($number%100) <= 13)
                        $abbreviation = $number. 'th';
                      else
                        $abbreviation = $number. $ends[$number % 10]; 
// 20 may 2018 start
            $sch_start_date = $date_day . '-' . $date_month . '-' . $date_year++;
            $sch_start_date2 = $date_day . '-' . $date_month . '-' . $date_year;

            $sch_end_date = date('d-m-Y', strtotime($sch_start_date2 . ' -1 day'));
            $exp = date('d-m-Y', strtotime($agree_exp_dt));

            $sch_end_date_t = strtotime($sch_end_date);
            $exp_date = strtotime($exp);
            if ($exp_date < $sch_end_date_t) {
                $sch_end_date = date('d-m-Y', $exp_date);
            }               
            
// 20 may 2018 end
            //$html .='<input id="count_year" name="count_year" type="hidden" value="' . $year_diff . '" />';
            $html .='<input id="element_number" name="element_number" type="hidden" value="' . $element_number . '" />';
            //$html .='<input name="year_sl" type="hidden" value="'.$i.'" />';
            //$html .='<input name="year_sl' . $i . '" type="hidden" value="'.$row->rent_incr_yr.'" />';
            $html .='<input name="date_day" type="hidden" value="'.$date_day.'" />';
            $html .='<input name="date_month" type="hidden" value="'. $date_month.'" />';
            $html .='<input name="year_sl' . $i . '" type="hidden" value="'.$start_date_year++ .'" />';
            $html .='<input name="year_sl_end' . $i . '" type="hidden" value="' .date_format(date_create("$sch_end_date"),"Y-m-d "). '" />';
           

            $html .='<input name="incr_change_sts" type="hidden" value="0" />';

            $html .='<tr id="increment_tr'.$i.'" class="incre_tr_cls"   style="border: 1px solid black ;">';
            //$html .='<td style="text-align:center;">'.date_format(date_create("$row->start_dt"),"d-M-Y").' to '.date_format(date_create("$row->end_dt"),"d-M-Y ").'  </td>';
            $html .='<td style="text-align:center;">'.date_format(date_create("$row->start_dt"),"d-M-Y").'  </td>';
            $html .='<td style="text-align:center;">' . $abbreviation  . ' Year </td>';

            $html .='<td style="text-align:center;">
                            <select name="rent_amount_type'.$i.'" class="inc_select'.$i.' common_inc_cls" id="" disabled="true">';

                                    $html .='<option ';  if( $row->rent_amount_type == "") { $html .=' selected="selected"';  } else { $html .=''; }   $html .=' value="">No Increment</option>';
                                    $html .='<option ';  if( $row->rent_amount_type == "per_rent") { $html .=' selected="selected"';  } else { $html .=''; }  $html .='  value="per_rent" >Percentage (%)</option>';
                                    $html .='<option ';  if( $row->rent_amount_type == "dir_rent") { $html .=' selected="selected"';  } else { $html .=''; }  $html .='  value="dir_rent" >Direct</option>';

                            $html .='</select>';
                            $html .=' <input type="text"  name="rent_amount_val' . $i . '" id="rent_amount_val' . $i . '"  class=" incr_input number " value="'.$row->rent_amount_val.'"  readonly/>

                    </td>';
            $html .='<td style="text-align:center;"><input type="text" style="text-align:right;" name="cal_rent_val' . $i . '" id="cal_rent_val' . $i . '"  class="inc_cls  tot_val_' . $i . ' number mon_val"  value="'.$row->cal_rent_val.'"  readonly/></td>';
            $j = 0;
            $k = 1;
    // foreach ($arr_other_name as $single_arr_other_name) {
    // if($single_arr_other_name!=''){
    $cal_others_val_arr = explode(",",$row->cal_others_val);
    $others_amount_val_arr = explode(",",$row->others_amount_val);
    $others_amount_type_arr = explode(",",$row->others_amount_type);
   
	if($row->cal_others_val!=''){
    	foreach ($cal_others_val_arr as $key=> $single_cal_others_val){
   		// if($single_arr_other_name!=''){

                $html .='<td style=" padding: 5px;"> 
                                <select name="others_amount_type'.$i.$j.'" class="others_amount_type'.$i.$j.' common_other_cls" id="others_amount_type' . $i . $j . '" style="width: 110px; margin-right:5px;" disabled="true">';
                                     
                                        $html .= '<option '; if( $others_amount_type_arr[$key] == "") { $html .=' selected="selected"';  } else { $html .=''; }   $html .=' value="">No Increment</option>';
                                        $html .= '<option '; if( $others_amount_type_arr[$key] == "per_otr") { $html .=' selected="selected"';  } else { $html .=''; } $html .= ' value="per_otr">Percentage (%)</option>';
                                        $html .= '<option '; if( $others_amount_type_arr[$key] == "dir_otr") { $html .=' selected="selected"';  } else { $html .=''; } $html .= ' value="dir_otr">Direct</option>';
                                $html .= '</select>';

                $html .= '<input type="text"  name="others_amount_val' . $i . $j . '" id="others_amount_val' . $i . $j . '"  class="incr_input number  others_amount_val'.$i.$j.'"  value="'.$others_amount_val_arr[$key].'"    readonly/>
                </td>';
                $html .='<td style="text-align:center;"><input type="text" style="text-align:right;" name="cal_others_val' . $i . $j . '" id="others_rent"  class="others_input_value' . $j . ' number  other_tot_val_' . $i . $j . ' others_input_style" value="'.$single_cal_others_val.'"   readonly/></td>';
                
				$html.= '<script>

      
              incr_edit_jquery_3('.$i.','.$j.','.$k.','.$year_diff.');
                jQuery(".others_amount_type'.$i.$j.'").on(\'focus\', function () {
                    
                        previous = this.value;
                        }).change(function() { 
                            var others_amount_val= jQuery("#others_type_amount_percentage'.$k.'").val();
                            var incr_type= jQuery(".others_amount_type'.$i.$j.' option:selected").val();
                            if(incr_type==""){
                                var previous_val = previous;
                                var i_otr='.$i.';
                                var j_otr='.$j.';

                                var t=0;
                                if(i_otr>0){
                                    t = i_otr-1;
                                    var cal_val=  parseFloat(jQuery(".other_tot_val_"+t+j_otr).val());

                                }else{
                                    t=0;
                                    var cal_val=  parseFloat(others_amount_val);

                                }
                                 var old_val = parseFloat(jQuery("#others_amount_val'.$i.$j.'").val());
                                 var deducted_val_for_percent = parseFloat(jQuery(".other_tot_val_'.$i.$j.'").val()) - cal_val;
                                 
    
                                          var j='.$i.';
                                          var k='.$year_diff.';
                                          for(var a=j;a<=k;a++){
                                                var deducted_val = parseFloat(jQuery(".other_tot_val_"+a+'.$j.').val()) - parseFloat(old_val);
                                                
                                                if(previous_val==\'dir_otr\'){
                                                    
                                                    jQuery(".other_tot_val_"+a+'.$j.').val(parseFloat(deducted_val));

                                                }else{
                                                    
                                                    var deducted_val_percent = parseFloat(jQuery(".other_tot_val_"+a+'.$j.').val()) - deducted_val_for_percent;
                                                    jQuery(".other_tot_val_"+a+'.$j.').val(parseFloat(deducted_val_percent));

                                                }
                                               
                                           }

                            }else{

                            }

                            var others_amount_val= jQuery("#others_type_amount_percentage' . $k . '").val();
                            var others_incr_type_read = jQuery(".others_amount_type' . $i . $j . ' option:selected").val();

                            if (others_incr_type_read==\'per_otr\' || others_incr_type_read==\'dir_otr\'){ 
                            //jQuery("#others_amount_val' . $i . $j . '").attr("readonly", false);
                            }else if(others_incr_type_read==\'\'){

                               
                                jQuery("#others_amount_val' . $i . $j . '").val("0.00");
                                jQuery("#others_amount_val' . $i . $j . '").attr("readonly", true);
                            }

                        }); 
   
                        jQuery(".others_amount_val'.$i.$j.'").on("change",function(){
                           

                               var incr_type= jQuery(".others_amount_type' . $i . $j . ' option:selected").val();
                          
                                if (incr_type==\'per_otr\'){
                                
                                    var mid_val=  jQuery(\'.other_tot_val_' . $i . $j . ' \').val();
                                    var incr_per = jQuery(this).val();
                                    total_monthly_rent_amount= +mid_val + +mid_val *(incr_per/100);

                                    if(jQuery("#increment_type option:selected").val()==\'3\'){
                                       jQuery(\'.other_tot_val_' . $i . $j . ' \').val(total_monthly_rent_amount.toFixed(2));
                                    }else{  
                                                  var j=' . $i . ';
                                                  var k=' . $year_diff . ';
                                                  for(var a=j;a<=k;a++){
                                                      jQuery(".other_tot_val_"+a+' . $j . ').val(total_monthly_rent_amount.toFixed(2));
                                                      
                                                      var after_clc= jQuery(".other_tot_val_"+a+' . $j . ').val();
                                                      var total_others= jQuery("#others_total' . $i . '").val();
                                                      total_others = +total_others + +after_clc;
                                                      jQuery("#others_total' . $i . '").val(total_others);
                                                   }
                                        }        

                                }else if(incr_type==\'dir_otr\'){

                                    var mid_val=  jQuery(\'.other_tot_val_' . $i . $j . ' \').val();
                             
                                    var incr_per = jQuery(this).val();
                                    total_monthly_rent_amount= +mid_val + +incr_per;
                                    if(jQuery("#increment_type option:selected").val()==\'3\'){
                                       jQuery(\'.other_tot_val_' . $i . $j . ' \').val(total_monthly_rent_amount.toFixed(2));
                                    }else{   

                                          var j=' . $i . ';
                                          var k=' . $year_diff . ';
                                          for(var a=j;a<=k;a++){
                                            jQuery(".other_tot_val_"+a+' . $j . ').val(total_monthly_rent_amount.toFixed(2));
                                          }

                                    }

                                      var after_clc= jQuery(".other_tot_val_' . $i . $j . '").val();
                                
                                      var total_others= jQuery("#others_total' . $i . '").val();
                                      
                                      total_others = +total_others + +after_clc;
                                      jQuery("#others_total' . $i . '").val(total_others);

                                }

                });

        </script>';


                $j++;
                $k++;
       
    }
}
            // others_rent
            $html .='<td style="text-align:center;"><input type="hidden"  name="" id="" value="" class="incr_input"/><input type="hidden"  name="id_list_final" id="id_list_final" value="' . $counter_others . '" class=""/></td>';
            //$html .='<td style="text-align:center;"><input type="text"  name="end_date'.$i.'" id="rent_end_date" value="'.$sch_end_date.'" class="incr_input"  readonly/></td>';
            $html .='</tr>';
  $html.= '<script>


    incr_edit_jquery_1('.$i.','.$incr_row_number.');
 
 
            </script>';


      $number++;
      $i++;
            }

        $html .='</tbody></table>';
        return $html;

}else{
   $html=''; 
 return $html;
}


    }

    function increment_sch() {
        $start = $this->input->post('start');
        $exp = $this->input->post('exp');
        $monthly_rent = $this->input->post('monthly_rent');
        $a = explode('-', $start);
        // print_r($exp);
        //       exit();

        $date_day = $a[0];
        $date_month = $a[1];
        $date_year = $a[2];
        $start_date_year = $a[2];

        $d1 = new DateTime($start);
        $d2 = new DateTime($exp);
        $diff = $d2->diff($d1);
        $year_diff = $diff->y;
        // print_r($year_diff);
        // exit();

        $html = '';
        //    $html.= 'Select an option: '; 
        $html .='<center>Increment Type:  <select name="percentage_basis_inc" class="inc_select" id="inc_select">
                                        <option value="">Select an Option</option>
                                        <option  value="incr_percent_amt">Percentage (%) amount</option>
                                        <option  value="incr_fixed_amt">Direct amount</option>

                                    </select></center>';
        $html.= '<br />';
        $html .='<table class="register-table" style="border:solid 1px black; text-align: center; width:97%; margin:20px;">
    ';

        $html .='<tr><th>Year</th><th>Increment </th><th>Monthly Rent Amount</th><th>Others Rent</th><th>Start Date</th><th>End Date</th></tr>';
        $html .='<tbody id="register-table">';

        for ($i = 0; $i <= $year_diff; $i++) {

            $sch_start_date = $date_day . '-' . $date_month . '-' . $date_year++;
            $sch_start_date2 = $date_day . '-' . $date_month . '-' . $date_year;
            //$sch_start_date1= $date_year.'-'.$date_month.'-'.$date_day;

            $sch_end_date = date('d-m-Y', strtotime($sch_start_date2 . ' -1 day'));
            //  $exp_date= date('d-m-Y', strtotime($exp .' +0 day'));

            $sch_end_date_t = strtotime($sch_end_date);
            $exp_date = strtotime($exp);
            if ($exp_date < $sch_end_date_t) {
                $sch_end_date = date('d-m-Y', $exp_date);
            }


// 27 march old code
// $html.= '<script>jQuery("#inc_select").change(function() {
//       // alert("sdfsdf");
//        var incr_type= jQuery("#inc_select :selected").val();
//        var monthly_rent= jQuery("#monthly_rent").val();
//       if (incr_type==\'incr_percent_amt\'){
//           jQuery(".incr_per_val").prop(\'disabled\', false);
//       jQuery(".increment_percentage_'.$i.' ").blur(function(){
//       var incr_per = jQuery(this).val();
//       total_monthly_rent_amount= +monthly_rent + +monthly_rent *(incr_per/100);
//       jQuery(\'#tot_val_'.$i. ' \').val(total_monthly_rent_amount);
//          });
// }else if(incr_type==\'incr_fixed_amt\'){
//  jQuery(".incr_per_val").prop(\'disabled\', false);
//        jQuery(".increment_percentage_'.$i.' ").blur(function(){
//       var incr_per = jQuery(this).val();
//       total_monthly_rent_amount= +monthly_rent + +incr_per;
//       jQuery(\'#tot_val_'.$i. ' \').val(total_monthly_rent_amount);
//   });
// }else{
//   jQuery(".incr_per_val").prop(\'disabled\', true);
// }
//     });</script>'; 
// 27 march new code

            $html.= '<script>
            jQuery("#inc_select").on(\'change\',function() { 
            var monthly_rent= jQuery("#monthly_rent").val();
             jQuery(\'.mon_val \').val(monthly_rent);
             jQuery(\'.incr_per_val \').val(0.00);

            });
            jQuery(".inc_select").one(\'change\',function() {
                  
                   var monthly_rent= jQuery("#monthly_rent").val();

               //jQuery(\'#tot_val_' . $i . ' \').val(monthly_rent);
               jQuery(\'.mon_val \').val(\'\');
               jQuery(\'.mon_val \').val(monthly_rent);
               jQuery(".incr_per_val").prop(\'disabled\', false);   
               
                     jQuery(".increment_percentage_' . $i . ' ").blur(function(){
                           var incr_type= jQuery(".inc_select option:selected").val();
                        
                           if (incr_type==\'incr_percent_amt\'){
                           var mid_val=  jQuery(\'#tot_val_' . $i . ' \').val();
                              
                        
                      var incr_per = jQuery(this).val();
                      total_monthly_rent_amount= +mid_val + +mid_val *(incr_per/100);
                      var j=' . $i . ';
                      var k=' . $year_diff . ';
                      for(var a=j;a<=k;a++){
                      jQuery(\'#tot_val_\'+a).val(total_monthly_rent_amount.toFixed(2));
                     }

                }

                    else if(incr_type==\'incr_fixed_amt\'){
                    var mid_val=  jQuery(\'#tot_val_' . $i . ' \').val();
                      
                          var incr_per = jQuery(this).val();
                          total_monthly_rent_amount= +mid_val + +incr_per;
                          var j=' . $i . ';
                          var k=' . $year_diff . ';
                          for(var a=j;a<=k;a++){
                      
                          jQuery(\'#tot_val_\'+a).val(total_monthly_rent_amount.toFixed(2));
                          }

                    }
                     });
             
                });</script>';



            $html .='<input id="count_year" name="count_year" type="hidden" value="' . $year_diff . '" />';
            //$html .='<input name="year_sl" type="hidden" value="'.$i.'" />';
            $html .='<input name="year_sl' . $i . '" type="hidden" value="' . $start_date_year++ . '" />';
            $html .='<input name="change_sts" type="hidden" value="1" />';

            $html .='<tr>';
            $html .='<td style="text-align:center;">' . $i . ' Year </td>';
            $html .='<td style="text-align:center;"><input type="text"  name="increment_percentage' . $i . '" id="increment_percentage' . $i . '"  class="text-input-small incr_per_val increment_percentage_' . $i . ' number"  value="0.00" /></td>';
            $html .='<td style="text-align:center;"><input type="text"  name="monthly_rent_with_increment' . $i . '" id="tot_val_' . $i . '"  class="inc_cls text-input-small tot_val_' . $i . ' number mon_val"  value="" /></td>';
            $html .='<td style="text-align:center;"><input type="text"  name="others_rent' . $i . '" id="others_rent"  class="text-input-small number"   required/></td>';
            $html .='<td style="text-align:center;"><input type="text"  name="start_date' . $i . '" id="rent_start_date" value="' . $sch_start_date . '" class="text-input-small"  readonly/></td>';
            $html .='<td style="text-align:center;"><input type="text"  name="end_date' . $i . '" id="rent_end_date" value="' . $sch_end_date . '" class="text-input-small"  readonly/></td>';
            $html .='</tr>';
        }

        $html .='</tbody></table>';

        echo $html;
        //  exit();
    }

    function increment_sch_edit($rent_inc_result) {


        $percent_dir_type = $rent_inc_result[0]->percent_dir_type;


        $total_row = count($rent_inc_result);
        $new_row = $total_row - 1;
        ;


        $html = '';
        $html .='<center>Increment Type:  <select name="percentage_basis_inc" class="inc_select" id="inc_select">
                                        <option value="">Select an Option</option>';
        if (isset($percent_dir_type) && $percent_dir_type == "incr_percent_amt") {
            $html .= '<option selected="selected" value="incr_percent_amt" >Percentage (%) amount</option>
                                                    <option  value="incr_fixed_amt">Direct amount</option>';
        } else if (isset($percent_dir_type) && $percent_dir_type == "incr_fixed_amt") {
            $html .= '<option selected="selected" value="incr_fixed_amt">Direct amount</option>
                                                        <option value="incr_percent_amt" >Percentage (%) amount</option>';
        }
        $html .= ' </select></center>';
        $html.= '<br />';
        $html .='<table class="register-table" style="border:solid 1px black; text-align: center; width:97%; margin:20px;">';
        $html .='<tr><th>Year</th><th>Increment </th><th>Monthly Rent Amount</th><th>Others Rent</th><th>Start Date</th><th>End Date</th></tr>';
        $html .='<tbody id="register-table">';
        $i = 0;
        foreach ($rent_inc_result as $row) {

            $start = $row->start_dt;
            $exp = $row->end_dt;
         

            $d1 = new DateTime($start);
            $d2 = new DateTime($exp);
            $diff = $d2->diff($d1);
            $year_diff = $diff->y;
            //print_r($year_diff);
            // 28 march old code    

            $html.= '<script>
                jQuery("#inc_select").on(\'change\',function() { 
                var monthly_rent= jQuery("#monthly_rent").val();
                 jQuery(\'.mon_val \').val(monthly_rent);
                 jQuery(\'.incr_per_val \').val(0.00);
                 
                });
                jQuery(".inc_select").one(\'change\',function() {
                      
                       var monthly_rent= jQuery("#monthly_rent").val();

                   
                   jQuery(\'.mon_val \').val(\'\');
                   jQuery(\'.mon_val \').val(monthly_rent);
                   jQuery(".incr_per_val").prop(\'disabled\', false);   
                   
                         jQuery(".increment_percentage_' . $i . ' ").blur(function(){
                               var incr_type= jQuery(".inc_select option:selected").val();
                            
                               if (incr_type==\'incr_percent_amt\'){
                               var mid_val=  jQuery(\'#tot_val_' . $i . ' \').val();
                                  
                            
                          var incr_per = jQuery(this).val();
                          total_monthly_rent_amount= +mid_val + +mid_val *(incr_per/100);
                          var j=' . $i . ';
                          var k=' . $new_row . ';
                          for(var a=j;a<=k;a++){
                          jQuery(\'#tot_val_\'+a).val(total_monthly_rent_amount.toFixed(2));
                         }

                    }

                        else if(incr_type==\'incr_fixed_amt\'){
                        var mid_val=  jQuery(\'#tot_val_' . $i . ' \').val();
                             
                              var incr_per = jQuery(this).val();
                              total_monthly_rent_amount= +mid_val + +incr_per;
                              var j=' . $i . ';
                              var k=' . $new_row . ';
                              for(var a=j;a<=k;a++){
                          
                              jQuery(\'#tot_val_\'+a).val(total_monthly_rent_amount.toFixed(2));
                              }

                        }
                         });
                 
                    });</script>';



            $html .='<input name="rent_inc_id' . $i . '" type="hidden" value="' . $row->id . '" />';
            $html .='<input name="change_sts" type="hidden" value="0" />';
            $html .='<input name="year_sl' . $i . '" type="hidden" value="' . $row->rent_incr_yr . '" />';
            $html .='<input name="count_year" type="hidden" value="' . $total_row . '" />';
            $html .='<tr>';
            $html .='<td style="text-align:center;">' . $i . ' Year </td>';
            $html .='<td style="text-align:center;"><input type="text"  name="increment_percentage' . $i . '" id="increment_percentage' . $i . '"  class="text-input-small increment_percentage_' . $i . ' number in_amt incr_per_val"  value="' . $row->percent_dir_val . '" /></td>';
            $html .='<td style="text-align:center;"><input type="text"  name="monthly_rent_with_increment' . $i . '" id="tot_val_' . $i . '"  class="inc_cls text-input-small tot_val_' . $i . ' number mon_val"  value="' . $row->monthly_rent_with_increment . ' " readonly/></td>';
            $html .='<td style="text-align:center;"><input type="text"  name="others_rent' . $i . '" id="others_rent"  class="text-input-small number"   value="' . $row->others_rent_amount . '"/></td>';
            $html .='<td style="text-align:center;"><input type="text"  name="start_date' . $i . '" id="rent_start_date" value="' . $row->start_dt . '" class="text-input-small"  readonly/></td>';
            $html .='<td style="text-align:center;"><input type="text"  name="end_date' . $i . '" id="rent_end_date" value="' . $row->end_dt . '" class="text-input-small"  readonly/></td>';
            $html .='</tr>';

            $i++;
        }

        $html .='</tbody></table>';

        return $html;
        //exit();
    }

   

    function get_vendor_list()
    {
        $var=array();
        $vid = $this->input->post('id');
        $result = $this->agreement_model->get_vendor_code_list('vendor','name','sts = 1 AND FIND_IN_SET(5, vendor_type)');
        $result_1 = $this->agreement_model->get_parameter_data_single('vendor','name','sts = 1 AND FIND_IN_SET(5, vendor_type) and vendor_id='.$vid);
        //echo $result_1->name;
        if($result_1){
            foreach($result as $value){
                $var[] = array(
                    'value'=>$value->vendor_id,
                    'label'=>$value->name
                    );
            }
        }else{
            $var='';
        }

        echo json_encode($var);
    }

    function ajaxFileUpload_edit($custid, $view) {
        $data = array(
            'custid' => $custid,
           
            'view' => $view
        );
        $this->load->view('agreement/pages/upload_file_view', $data);
    }

    function ajaxFileUpload_edit_new($agree_id, $custid, $view) {
        $data = array(
            'custid' => $custid,
            'locksts' => 2,
            'agree_id' => $agree_id,
            // 'enqsl' => $enqsl,
            'view' => $view
        );
        $this->load->view('agreement/pages/upload_file_view_new', $data);
    }

    //function upload_by_ajax_edit($enqyr,$enqsl)
    function upload_by_ajax_edit() {

        $custid = $this->input->post('hidden_cust_id');
        $agree_id = $this->input->post('agree_id');
        // print_r($custid);
        // exit();
        //$q = $this->db->SELECT('customer_name',FALSE)->from('app_customer')->where(array('id' => $custid))->get()->row();     
        $destination_path = getcwd() . DIRECTORY_SEPARATOR;
        $result = 0;
        $file_limit = (2048576 * 10);
//print_r($_FILES['myfile']);
        $myFile = $_FILES['myfile'];
        $fileCount = count($myFile["name"]);
        for ($i = 0; $i < $fileCount; $i++) {
            $size1 = basename($myFile['size'][$i]);
            $size = $size1 / 2048576;
            $file_name_without_ext = current(explode(".", basename($myFile['name'][$i])));
            $ext = explode(".", basename($myFile['name'][$i]));

            //$New_file_name = 'cib='.str_replace(' ','+',$file_name_without_ext).'='.time().'.pdf';        
            $New_file_name = 'ebl_' . str_replace(' ', '+', $file_name_without_ext) . '=' . time() . '.' . $ext[1];
            //  $New_file_name = 'cib='.str_replace(' ','+',$file_name_without_ext).'=''.pdf';      
            $target_path = $destination_path . '/uploads/' . $New_file_name;
            //$caption =  $q->customer_name.'_'.$file_name_without_ext;
            $caption = $file_name_without_ext;


            $data = array(
                'e_by' => $this->session->userdata['user']['user_id'],
                'sessionid' => $this->session->userdata['user']['sessionId'],
                'userid' => $this->session->userdata['user']['user_id'],
                'rent_agree_id' => $agree_id,
                'doc_type_id' => $custid,
                'file_size' => $size,
                'file_path' => $New_file_name,
                'window_close_sts' => 0
            );
            $str = $this->db->insert_string('rent_upload_temp', $data);

            if ($size1 > $file_limit) {
                $result = 2;
            } else {

                if (@move_uploaded_file($myFile['tmp_name'][$i], $target_path)) {
                    $result = $this->db->query($str);
                }
            }
        }
         echo $result;
        // exit();
    }


    function uploaded_file_exists_check() {
        $res = $this->agreement_model->rent_file_upload_get_info_edit();
        echo $res;
        
    }


    function remove_file($sessionid, $filename, $type) {
        $res = $this->agreement_model->remove_file($sessionid, $filename, $type);
        echo 3;
    }

    function remove_file_edit($sessionid, $filename, $type) {
        $res = $this->agreement_model->remove_file_edit($sessionid, $filename, $type);
        echo 3;
    }

    function ajaxloadfile_edit_new($locksts, $custid, $agree_id) {
        
        $s = '<table cellspacing="0" cellpadding="0" border="0" style="width: 100%;" class="grid"><thead>
            <tr>
            <td nowrap="" class="gridColumn" width="75%">File</td>
            
            <td nowrap="" class="gridColumn delcolumn" style="text-align: right;" width="6%"></td></tr></thead>
            <tbody>';
        $c = 0;

   $v = '<table cellspacing="0" cellpadding="0" border="0" style="width: 100%;" class="grid"><thead>
            </thead>
            <tbody><tr><td class="gridCell" >';


        if ($locksts == "2") {
            $res1 = $this->agreement_model->ajaxloadfile_existing_edit_new($custid, $agree_id);
            foreach ($res1 as $row) {
//                  if($row->cib_file !='')
//                  {
                $arr_file_name = explode(',', $row->file_name);
                //$arr_file_caption = explode(',',$row->cib_file_caption);

                for ($i = 0; $i < count($arr_file_name); $i++) {
                    $file_name = $arr_file_name[$i];
                    //$org_name = explode('=', $file_name);
                    //$show_file_name_original='<a download="'.str_replace('+', ' ', $org_name[1]).'.pdf" href="'.base_url().'cib_files/'.$arr_file_name[$i].'" target="_blank" ><img height="16" width="16" src="'.base_url().'images/pdf_icon.gif" style="vertical-align: middle; margin-right: 2px;" /> '.$arr_file_caption[$i].'</a>';

                    $s.='<tr>
                            <td class="gridCell" ><a download="' . $row->file_name . '" href="' . base_url() . 'uploads/' . $row->file_name . '"  target="_blank"><img height="16" width="16" src="' . base_url() . 'images/file_icon.png" style="vertical-align: middle; margin-right: 2px;" />' . $row->file_name . '</a>
                            </td> 
                            
                            <td  class="gridCell delcolumn" style="text-align: center; ">
                            <span><img height="16" style="cursor:pointer;vertical-align: middle;" onClick=remove_file_edit("' . $custid . '","' . $arr_file_name[$i] . '",1) width="16" src="' . base_url() . 'images/del.png" /></span>
                            </td>
                            </tr>';
                    $c++;
                }

               $v.=' <a download="' . $row->file_name . '" href="' . base_url() . 'uploads/' . $row->file_name . '"  target="_blank"><img height="16" width="16" src="' . base_url() . 'images/pdf_icon.gif" style="vertical-align: middle; margin-right: 2px;" />  </a>  ';
                
                 
              
                //}
            }
        }

        $res = $this->agreement_model->ajaxloadfile($custid);

        foreach ($res as $row) {
            $s.='<tr>
                <td class="gridCell" ><a download="' . $row->file_path . '" href="' . base_url() . 'uploads/' . $row->file_path . '"  target="_blank"><img height="16" width="16" src="' . base_url() . 'images/file_icon.png" style="vertical-align: middle; margin-right: 2px;" />' . $row->file_path . '</a>
                </td>
                
                <td  class="gridCell" style="text-align: center; "><span><img height="16" style="cursor:pointer;vertical-align: middle;" onClick=remove_file_edit("' . $row->sessionid . '","' . $row->file_path . '",0) width="16" src="' . base_url() . 'images/del.png" /></span>
                </td>
                </tr>';

                 $v.='
                <a download="' . $row->file_path . '" href="' . base_url() . 'uploads/' . $row->file_path . '"  target="_blank"><img height="16" width="16" src="' . base_url() . 'images/pdf_icon.gif" style="vertical-align: middle; margin-right: 2px;" />  </a>
                ';

            $c++;
        }


        if ($c == 0) {
            $s.='<tr><td class="gridCell" colspan="7" style="border-top: 1px solid white; border-bottom: 1px solid white; border-left: 1px solid white; text-align: center">Empty</td>
                </tr>';
        }
        $s.='</tbody></table>';
        // echo $s . "####" . $c;
        // exit();
        $v.='</td></tr></tbody></table>';
        echo $s . "####" . $c . "####" . $v;
        //exit();
    }

    function ajaxloadfile_edit($custid) {
        // print_r($add_edit);
        // print_r($custid);
        // exit();
        $locksts = 0;
        $s = '<table cellspacing="0" cellpadding="0" border="0" style="width: 100%;" class="grid"><thead>
            <tr>
            <td nowrap="" class="gridColumn" width="25%">File</td>
            
            <td nowrap="" class="gridColumn delcolumn" style="text-align: right;" width="6%"></td></tr></thead>
            <tbody>';

        $v = '<table cellspacing="0" cellpadding="0" border="0" style="width: 100%;" class="grid"><thead>
            </thead>
            <tbody><tr><td class="gridCell" >';
        $c = 0;
        if ($locksts == "2") {
            $res1 = $this->agreement_model->ajaxloadfile_existing_edit($custid);
            foreach ($res1 as $row) {

                $arr_file_name = explode(',', $row->file_name);
                //$arr_file_caption = explode(',',$row->cib_file_caption);

                for ($i = 0; $i < count($arr_file_name); $i++) {
                    $file_name = $arr_file_name[$i];
                    //$org_name = explode('=', $file_name);
                    //$show_file_name_original='<a download="'.str_replace('+', ' ', $org_name[1]).'.pdf" href="'.base_url().'cib_files/'.$arr_file_name[$i].'" target="_blank" ><img height="16" width="16" src="'.base_url().'images/pdf_icon.gif" style="vertical-align: middle; margin-right: 2px;" /> '.$arr_file_caption[$i].'</a>';

                    $s.='<tr>
                            <td class="gridCell" >' . $file_name . '</td>
                            <td  class="gridCell" ></td>
                            <td  class="gridCell delcolumn" style="text-align: center; ">
                            <span><img height="16" style="cursor:pointer;vertical-align: middle;" onClick=remove_file("' . $custid . '","' . $arr_file_name[$i] . '",1) width="16" src="' . base_url() . 'images/del.png" /></span>
                            </td>
                            </tr>';
                    $c++;
                }
                
            }
        } else {
            $res = $this->agreement_model->ajaxloadfile($custid);

            foreach ($res as $row) {
                $s.='<tr>
                <td class="gridCell" ><a download="' . $row->file_path . '" href="' . base_url() . 'uploads/' . $row->file_path . '"  target="_blank"><img height="16" width="16" src="' . base_url() . 'images/pdf_icon.gif" style="vertical-align: middle; margin-right: 2px;" />' . $row->file_path . '</a>
                </td>
                
                <td  class="gridCell" style="text-align: center; "><span><img height="16" style="cursor:pointer;vertical-align: middle;" onClick=remove_file("' . $row->sessionid . '","' . $row->file_path . '",0) width="16" src="' . base_url() . 'images/del.png" /></span>
                </td>
                </tr>';

                $v.='
                <a download="' . $row->file_path . '" href="' . base_url() . 'uploads/' . $row->file_path . '"  target="_blank"><img height="16" width="16" src="' . base_url() . 'images/pdf_icon.gif" style="vertical-align: middle; margin-right: 2px;" />  </a>
                
                 
                ';
                $c++;
            }
        }
        if ($c == 0) {
            $s.='<tr><td class="gridCell" colspan="7" style="border-top: 1px solid white; border-bottom: 1px solid white; border-left: 1px solid white; text-align: center">Empty</td>
                </tr>';
        }
        $s.='</tbody></table>';
        $v.='</td></tr></tbody></table>';
        echo $s . "####" . $c . "####" . $v;
    }

    function rent_file_upload_action($add_edit = NULL, $edit_id = NULL) {
        //echo $this->input->post('file_count');
        $file_list = $this->agreement_model->get_file_list_from_temp();
        $file_count = count($file_list);


        foreach ($file_list as $single_file) {
            $id = $this->agreement_model->upload_file_action($add_edit, $edit_id, $single_file);
        }



        $var = array();
        // $var['Message']=$Message;
        // $var['row_info']=$row;
        //$var['file_check']=$file_check;

        echo json_encode($var);
    }

    function get_child_list() {

        $var = array();
        $service = array();
        $nvalue = array();
        $this->load->model('agreement_model', '', TRUE);
        $result = $this->agreement_model->get_child_list('cost_center', 'id');

        // print_r($result);
        // exit();
        if ($result->mis_codes) {
            $nvalue = explode(",", $result->mis_codes);
        }
        foreach ($nvalue as $value) {
            $var[] = array(
                'value' => $value,
                'label' => $value
            );
        }
        echo json_encode($var);
    }

    function duplicate_field($field_name = NULL) {
        if ($this->input->post('val') != "") {
            $num_row = $this->agreement_model->duplicate_name($field_name, $this->input->post('val'));
            $var =
                    array(
                        "Message" => "",
                        "Status" => $num_row > 0 ? 'duplicate' : 'ok'
            );
            echo json_encode($var);
        }
    }

    function duplicate_field2($field_name = NULL) {
        if ($this->input->post('agreement_ref_no') != "" && $this->input->post('val') != "" && $this->input->post('rent_agreement_id') != "") {
            $num_row = $this->agreement_model->duplicate_name3($field_name, $this->input->post('val'), $this->input->post('rent_agreement_id'), $this->input->post('agreement_ref_no'));
            $var =
                    array(
                        "Message" => "",
                        "Status" => $num_row > 0 ? 'duplicate' : 'ok'
            );
            echo json_encode($var);
        } else if ($this->input->post('val') != "" && $this->input->post('rent_agreement_id') != "") {
            $num_row = $this->agreement_model->duplicate_name2($field_name, $this->input->post('val'), $this->input->post('rent_agreement_id'));
            $var =
                    array(
                        "Message" => "",
                        "Status" => $num_row > 0 ? 'duplicate' : 'ok'
            );
            echo json_encode($var);
        }
    }

    function add_edit_action($add_edit = NULL, $edit_id = NULL) {
      
        $file_path = '';
        $file_type = '';
        $file_name = '';
        $file_check = 0;
        // $this->load->database();
        // echo $this->input->post('increment_type');
		// echo $this->input->post('agree_exp_dt');

        $text = array();
        if ($this->session->userdata['user']['login_status']) {
            $file_name = isset($upload_data['file_name']) ? $upload_data['file_name'] : NULL;
            $id = $this->agreement_model->add_edit_action($add_edit, $edit_id, $file_name, $file_path, $file_type);
            $rent_agreement_id = $id;
          
            if($add_edit!='modify'){
    	        if($this->input->post('increment_type')==5){
    	            $this->generate_schedule_data_manual_incr($rent_agreement_id,$add_edit);
    	        }else{
    	        	$this->generate_schedule_data($rent_agreement_id,$add_edit);
    	        }
            }else{  // modify

                if($this->input->post('increment_type')==5){
                    $this->generate_schedule_data_manual_incr($rent_agreement_id,$add_edit);
                }else{
                    $this->generate_schedule_data($rent_agreement_id,$add_edit);
                }
            }
          
        } else {
            $text[] = "Session out, login required";
        }


        // file upload code
        $file_list = $this->agreement_model->get_file_list_from_temp();
        $file_count = count($file_list);

        foreach ($file_list as $single_file) {
            $id = $this->agreement_model->upload_file_action($add_edit, $edit_id, $single_file, $rent_agreement_id);
        }


        $Message = '';
        if (count($text) <= 0) {
            $Message = 'OK';
            $row = $this->agreement_model->get_add_action_data_for_action($rent_agreement_id);

        } else { 
            for ($i = 0; $i < count($text); $i++) {
                if ($i > 0) {
                    $Message.=',';
                }
                $Message.=$text[$i];
            }
            $row[] = '';
        }

        $var = array();
        $var['Message'] = $Message;
        $var['row_info'] = $row; 
        // $var['Message'] = 'sdfs';
        // $var['row_info'] = 'bbb';
        //print_r($var);exit;
      
        echo json_encode($var);
    }
	
    function delete_action($d_v = NULL) {
        $msg = $this->agreement_model->delete_action();
        $jTableResult = array();
        if($msg == '1'){
            $jTableResult['status'] = "success";
            $jTableResult['errorMsgs'] = 0;
        }
        else{
            $jTableResult['status'] = "error";
            $jTableResult['errorMsgs'] = $msg;
        }
        
        echo json_encode($jTableResult);
    }

    function admin_check_verify() {
        $this->load->model('agreement_model', '', TRUE);
		$r = $this->agreement_model->admin_check_status($this->input->post('id'));
        echo $r;
    }

    function verify() {
        $this->load->model('agreement_model', '', TRUE);
        //echo $this->input->post('type');
        //exit();

        $return = $this->agreement_model->verify_action();
        $jTableResult = array();
        if ($return == 2)
            $jTableResult['status'] = "success";
        else {
            $jTableResult['status'] = "error";
            $jTableResult['errorMsgs'] = "Problem Occured during verify the memo entry";
        }
        echo json_encode($jTableResult);
    }

    function new_agr_verify() {
        $this->load->model('agreement_model', '', TRUE);
        

        $return = $this->agreement_model->new_agr_verify_action();
        $jTableResult = array();
        //echo $return;exit;
        if ($return == 2)
            $jTableResult['status'] = "success";
        else {
            $jTableResult['status'] = "error";
            $jTableResult['errorMsgs'] = "Problem Occured during verify ";
        }
        echo json_encode($jTableResult);
    }

    function ack_action() {
        $jTableResult = array();
        $agree_id = $this->input->post('id');


        $result = $this->agreement_model->get_agree_info($agree_id); 
     

        $id = $this->agreement_model->ack_halt_action($result);
        if ($id) {

            $jTableResult['status'] = "success";
        } else {
            $jTableResult['errorMsgs'] = 'error';
        }
        echo json_encode($jTableResult);
    }

    function tt() {
            $tbl_array= array('rent_agreement','rent_agr_landlords','rent_agr_increment_history','rent_agr_doc',
                'rent_agr_loc_type_and_cost_center','rent_agr_adv_adjustment_history','rent_agr_other_locations',
                'rent_advance_payment_history','rent_advance_payment_history_landlords_trxn','rent_security_deposit','rent_security_deposit_txrn',
                'rent_ind_schedule','rent_ledger','rent_data_operation_history','rent_paid_history','rent_journal_entry_and_gefo','rent_upload_temp',
				'rent_vat_tax_ref_counter','rent_stop_release_history'

            );

     
        $i=0;
        foreach($tbl_array as $single_tbl){
            $this->db->truncate($tbl_array[$i]);
            //echo $single_tbl[2];
            $i++;
        }
		
		echo $i.' tables successfully truncated';

    }


    function checkPaidStatus($id){
        $result = $this->agreement_model->checkPaidStatus($id);
        echo json_encode($result);
    }

    function check_agree_rent_in_adv($id){
        $result = $this->agreement_model->check_agree_rent_in_adv($id);
        echo json_encode($result);
    }

    function ajax_comma()
    {
        if ($this->input->post('monthly_rent') > 0 || $this->input->post('others_rent') >0){
            $total_amount= $this->input->post('monthly_rent') + $this->input->post('others_rent');
            echo $this->user_model->comma($total_amount);
        }else{
            echo 0.00;
        }       
    }

    function generate_schedule_data_manual_incr($rent_agree_id,$add_edit) {
    	if($add_edit!='modify'){
            $this->db->query("Delete from rent_ind_schedule where rent_agree_id = $rent_agree_id ");
        }else{
        	$paid_count=  $this->agreement_model->count_paid_schedule($rent_agree_id);  
        	$unpaid_id_list=  $this->agreement_model->get_sche_unpaid_id_list($rent_agree_id);  
            $id_list = explode(",",$unpaid_id_list->unpaid_id_list);
        }

    
        $rent_agreement_row_data = $this->agreement_model->get_add_action_data_new($rent_agree_id);

            if($rent_agreement_row_data->adjust_adv_type !=4){
                $rent_adjust_data = $this->agreement_model->single_rent_adjustment_get_info('', $rent_agree_id);
            }else{
                $rent_adjust_data = $this->agreement_model->rent_adjustment_get_info('', $rent_agree_id);
            }
        $tax_info = $this->agreement_model->single_rent_tax_info('', 1);
        $vat_info = $this->agreement_model->single_rent_vat_info('', 1);
        $frac = $start1 = $rent_agreement_row_data->rent_start_dt;
        $frac_for_last  = $rent_agreement_row_data->agree_exp_dt;
        $exp1 = $rent_agreement_row_data->agree_exp_dt;
 
        $start = $month = strtotime($start1);
        $end = strtotime($exp1);
        $start = new DateTime($start1);
        $interval = new DateInterval('P1M');
        $end = new DateTime($exp1);
        $end1 = new DateTime($exp1);
        $end1->modify('last day of this month');
        $period = new DatePeriod($start, $interval, $end1);
        $last_iteration = iterator_count($period);
        
        $a = explode('-', $start1);
        $date_year = $a[0];
       
        // checking if it is own or rented 

        if($rent_agreement_row_data->location_owner=='rented'){    
            $rent_agree_ref = $rent_agreement_row_data->agreement_ref_no;
            $advance_amount = $rent_agreement_row_data->total_advance;
            $paid_advance_amount = $rent_agreement_row_data->total_advance_paid;
            $monthly_rent = $rent_agreement_row_data->monthly_rent;
            $others_rent = 0;
            
            $vat_amount = $vat_info->vat_percentage;
            $tax_amount = $tax_info->tax_amount;
            $calculated_tax = $monthly_rent * ($tax_amount / 100);
        
            $unadjust = $paid_advance_amount;
        
            $i = 0;
            $count = 0;
            $start_year = $date_year - 1;
            $start_year =  date('Y', strtotime($rent_agreement_row_data->incr_start_date));
        
            $indx=0;
            foreach ($period as $key => $dt) {
            //echo $dt->format('d-m-Y').'<br/>';
                //$schedule_strat_dt = $payment_date = $dt->format('d-M-y') . PHP_EOL;
                $schedule_strat_dt = $payment_date = $dt->format('Y-m-d') . PHP_EOL;
                $total_day_in_month = $dt->format('t');
                $adjustment_amount =0;

                if ($i == 0) {
                    $fraction_day = date('t', strtotime($frac)) - date('j', strtotime($frac));
                    $fraction_day_percent = (($fraction_day + 1) * 100) / $total_day_in_month;
                    $first_fraction_day_percent = (($fraction_day + 1) * 100) / $total_day_in_month;
                } 
                elseif($i == $last_iteration -1){
                    $fraction_day = date('t', strtotime($frac_for_last)) - date('j', strtotime($frac_for_last));
                    $fraction_day_percent = (($total_day_in_month - $fraction_day) * 100) / $total_day_in_month;

                }else {
                    $frac = $dt->format('Y-m-01') . PHP_EOL;
                    $fraction_day = date('t', strtotime($frac)) - date('j', strtotime($frac));
                    $fraction_day_percent = (($fraction_day + 1) * 100) / $total_day_in_month;
                }
                
                if($rent_agreement_row_data->point_of_payment=='cm'){
                    $maturity_dt = $dt->format('Y-m-01');
                    $Schedule_end_dt = $dt->format('Y-m-t');
            
                }else{

                    $first_day_of_next_month = $maturity_dt = date('Y-m-d', strtotime($dt->format('Y-m-01'). ' +1 month'));
                    $date4 = $Schedule_end_dt = $dt->format('Y-m-t');
                    //$date4 = $Schedule_end_dt = date('Y-m-t', strtotime($dt->format('Y-m-01'). ' +1 month'));

                }
//echo $i.'-'.$start_year.'<br />';

                 if ($count % 12 == 0) {
                                
                            $year_sts='';
                            if($i == $last_iteration - 1 ){  // last month
                                $start_year;
                                $year_sts='last';
                                
                            }else{
                                //$start_year =  date('Y', strtotime($rent_agreement_row_data->incr_start_date));
                                if($count>0)
                                $start_year++;
                                //echo 'not last';
                            }
                            $remarks_count = 0;
                        }else{
                        	//$start_year =  date('Y', strtotime($rent_agreement_row_data->incr_start_date));
                        } 
            //echo $count.'-----'.$start_year.'<br />';
               if($maturity_dt < $rent_agreement_row_data->incr_start_date ){
                $incr_start_year =  date('Y', strtotime($rent_agreement_row_data->incr_start_date));

                $new_monthly_rent_before_incr = $this->agreement_model->get_monthly_rent_per_year($incr_start_year, $rent_agree_id);
                $new_others_rent_before_incr = $this->agreement_model->get_others_rent_per_year($rent_agree_id);
                $arr = array(   'rent_amount_type'=>'',
                                'rent_amount_val'=>0,
                                'cal_rent_val'=>$rent_agreement_row_data->monthly_rent,
                                'others_id_list'=>$new_monthly_rent_before_incr->others_id_list,
                                'cal_others_val'=>$new_others_rent_before_incr->total_others_amount
                    );
                $new_monthly_rent = (object) $arr;
                $calculated_monthly_rent = $new_monthly_rent->cal_rent_val * ($fraction_day_percent / 100);
                $remarks = '';
                $remarks_count = 0;
                $total_others = 0;

                $total_others = $new_monthly_rent->cal_others_val * ($fraction_day_percent / 100);

               }else{

                       // $incr_start_year =  date('Y', strtotime($rent_agreement_row_data->incr_start_date));  
                        $new_monthly_rent = $this->agreement_model->get_monthly_rent_per_year($start_year, $rent_agree_id);
                        $remarks = '';
                       // echo $i.'-'.$start_year.'<br />';
                        //print_r($new_monthly_rent);
                        
                        // 11 march start
                        if($rent_agreement_row_data->increment_type==5){

                            $date1 = new DateTime($maturity_dt);
                            $date1->modify('last day of this month');
                            $last_day_of_month =  $date1->format('Y-m-d');

                            $mature_ts = strtotime($maturity_dt);
                            $end_ts = strtotime($last_day_of_month);
                            $incr_start = strtotime($new_monthly_rent->start_dt);
                            $incr_end = strtotime($new_monthly_rent->end_dt);

                            if(($mature_ts >= $incr_start) && ($mature_ts <= $incr_end)){
                                if($remarks_count < 1 ){ 
                                    if ($new_monthly_rent->rent_amount_type == '') {
                                    $remarks = '';
                                    } else if ($new_monthly_rent->rent_amount_type == 'per_rent') {
                                        $remarks = $new_monthly_rent->rent_amount_val . ' % increment';
                                    } else {
                                        $remarks = $new_monthly_rent->rent_amount_val.' Taka increment';
                                    }
                                }else{ $remarks = ''; }
                                
                                ++$remarks_count;

                                $calculated_monthly_rent = $new_monthly_rent->cal_rent_val * ($fraction_day_percent / 100);
                            }
                            else{
                                // here
                                if ($new_monthly_rent->rent_amount_type == 'dir_rent') {
                                    $calculated_monthly_rent = ($new_monthly_rent->cal_rent_val * ($fraction_day_percent / 100))- $new_monthly_rent->rent_amount_val;
                                }else if ($new_monthly_rent->rent_amount_type == 'per_rent') {
                                    $temp_amt = (($new_monthly_rent->cal_rent_val * ($fraction_day_percent / 100))* 100)/ (100+ $new_monthly_rent->rent_amount_val)  ;
                                    $calculated_monthly_rent =  $temp_amt;
                                }else{
                                    $calculated_monthly_rent = $new_monthly_rent->cal_rent_val * ($fraction_day_percent / 100);
                                }   
                            }
                        }


                        if($rent_agreement_row_data->adjust_adv_type ==4){
                           $new_monthly_adjustment_info = $this->agreement_model->get_adjustment_amount_per_year($start_year, $rent_agree_id); 
                      
                            if ($new_monthly_adjustment_info->percent_dir_type == 'yearly_adj_fixed') {
                                $adjustment_amount_1 = $new_monthly_adjustment_info->adv_incr_year_val;
                                $adjustment_amount = $adjustment_amount_1 * ($fraction_day_percent / 100);
                            } else {
                              $adjustment_amount_1 = $advance_amount * ($new_monthly_adjustment_info->adv_incr_year_val/100);
                              $adjustment_amount = $adjustment_amount_1 * ($fraction_day_percent / 100);
                            }

                        }

                        // 3 april 2018
                        $others = $new_monthly_rent->cal_others_val;
	                    $total_others = 0;
	                    $strings_array = explode(',', $others);
	                    foreach ($strings_array as $each_number) {
	                        $total_others += $each_number;
	                    }

	                    $total_others = $total_others * ($fraction_day_percent / 100);
	                    $count++;	
                    }

                    if($rent_agreement_row_data->adjust_adv_type !=4){
                

                        if($rent_agreement_row_data->adjust_adv_type ==3){
                            // % of total advance amount
                            if($rent_adjust_data->percent_dir_type=='percent_total_amt'){
                               $adjustment_amount_1 = $rent_agreement_row_data->total_advance * ($rent_adjust_data->percent_dir_val/100);
                               $adjustment_amount = $adjustment_amount_1 * ($fraction_day_percent / 100);

                            }else{
                                // % of paid amount
                                if($rent_agreement_row_data->total_advance_paid !=0.00){
                                    $adjustment_amount_1 = $rent_agreement_row_data->total_advance_paid * ($rent_adjust_data->percent_dir_val/100);
                                    $adjustment_amount = $adjustment_amount_1 * ($fraction_day_percent / 100);
                                 }
                                 else{
                                     $adjustment_amount = 0;
                                 }
                            }

                        }else{

                            $adjustment_amount_1 = $rent_adjust_data->percent_dir_val;
                            $adjustment_amount = $adjustment_amount_1 * ($fraction_day_percent / 100);
                        }
                    }      
  // ..............
                    $net_payment = ($monthly_rent + $others_rent) - ($adjustment_amount + $calculated_tax );
                    $unadjusted_adv_rent = ($monthly_rent + $others_rent) - ($adjustment_amount + $calculated_tax );

                    $others_id = $new_monthly_rent->others_id_list;
                    $others_amount = $new_monthly_rent->cal_others_val;

                    $others_id_array = explode(',', $others_id);
                    $others_amount_array = explode(',', $others_amount);

                    $others_car = '';
                    $others_generator = '';
                    $others_water = '';
                    $others_gas = '';
                    $others_service = '';

                    for ($j = 0; $j < count($others_id_array); $j++) {
                        if ($others_id_array[$j] == 'Car Parking') {
                            $others_car = $others_amount_array[$j];
                        }
                        if ($others_id_array[$j] == 'Generator Space') {
                            $others_generator = $others_amount_array[$j];
                        }
                        if ($others_id_array[$j] == 'Water Supply') {
                            $others_water = $others_amount_array[$j];
                        }
                        if ($others_id_array[$j] == 'Gas Bill') {
                            $others_gas = $others_amount_array[$j];
                        }
                        if ($others_id_array[$j] == 'Service Charge') {
                            $others_service = $others_amount_array[$j];
                        }
                    }

                    // $others = $new_monthly_rent->cal_others_val;

                    // $total_others = 0;
                    // $strings_array = explode(',', $others);
                    // foreach ($strings_array as $each_number) {
                    //     $total_others += $each_number;
                    // }

                    // $total_others = $total_others * ($fraction_day_percent / 100);

                    $new_calculated_vat = ($calculated_monthly_rent + $total_others )* ($vat_amount / 100);
                    $new_calculated_tax = ($calculated_monthly_rent + $total_others )* ($tax_amount / 100);
                    $new_net_payment = ($new_monthly_rent->cal_rent_val + $total_others) - ($adjustment_amount + $new_calculated_tax );
                    $unadjust = $unadjust - $adjustment_amount;
            
                    if($unadjust < 0 ){$unadjust=0;}

                    // 31 may
                    $adjustment_amount_after_agree = 0;
                    $unadjust_after_agree = 0;

                    $schedule_data = array(
                        'rent_agree_id' => $rent_agree_id
                        , 'rent_agree_ref' => $rent_agree_ref
                        , 'maturity_dt' => $maturity_dt
                        , 'schedule_strat_dt' => $schedule_strat_dt
                        , 'Schedule_end_dt' => $Schedule_end_dt
                        , 'rent_fraction_day' => $fraction_day_percent
                        // , 'advence_rent_amount' => $paid_advance_amount
                        // , 'hidden_adjustment_adv' => $adjustment_amount
                        // , 'adjustment_adv' => $adjustment_amount_after_agree
                        // , 'adjust_sec_deposit' => 0
                        , 'monthly_rent_amount' => $calculated_monthly_rent
                        , 'others_car' => $others_car
                        , 'others_gas' => $others_gas
                        , 'others_generator' => $others_generator
                        , 'others_service' => $others_service
                        , 'others_water' => $others_water
                        , 'total_others_amount' => $total_others
                        //,'unadjusted_adv_rent' => $unadjust_after_agree               
                        , 'remarks' => $remarks
                        //, 'paid_sts' => 'unpaid'
                    );

                if($add_edit!='modify'){
                    $schedule_data_1 = array(
	                    	 'advence_rent_amount' => $paid_advance_amount
	                        , 'hidden_adjustment_adv' => $adjustment_amount
	                        , 'adjustment_adv' => $adjustment_amount_after_agree
	                        , 'adjust_sec_deposit' => 0
                            ,'unadjusted_adv_rent' => $unadjust_after_agree 
	                        ,'paid_sts' => 'unpaid'
                        );
                    $this->db->insert('rent_ind_schedule', array_merge($schedule_data,$schedule_data_1));
                   
                }else{
                    // 16 april
                    if($i < $paid_count->paid_count){

                    }else{

                    	$this->db->where('id', $id_list[$indx]);
	                    $this->db->update('rent_ind_schedule', $schedule_data);
	                    $indx++;
	                
                    }
                   
                }

                $i++;

            }
        }
    
    }

}

?>