<?
if ($add_edit == 'edit') {

    //$arr_vendor = explode(',',$result->vendor_id);
    $rent_ids = explode(',', $agreement->others_rent_types_ids);
}
if ($add_edit == 'edit') {
    //$arr_branch = explode(',',$result->branch_id);
}
?>

<body style="height:96%">
    <style type="text/css">
        .jqx-rc-all {
            border-radius: 0 !important;
        }
        .ms-parent{ width: 320px !important;}
        #cost_center_table, #cost_center_table tr,#cost_center_table td,
        #land_lord_table, #land_lord_table tr,#land_lord_table td{
            border: 1px solid black;
        }
        #cost_center_table th, #land_lord_table th{
            border-bottom: 1px solid black !important;
            border-top: 1px solid black;
            border-left: 1px solid black;
            border-right: 1px solid black;
        }
        .custom_css{width:100%;}

    </style>
    <style>
        .mark{
            border:1px solid red !important;
        }
        .unmark{
            border:none !important;
        }
        .error_msg{
            color: red;
            font-size: 16px;
            font-weight: bold;
            margin: 0 22px;
            padding-bottom: 5px;
            text-align: left;width: 55%;
        }
        .account_status{
            color: red;
            float: left !important;
            font-size: 12px;
            font-weight: bold;
            margin-left: 0;
            padding-left: 0;
            width: 300px;
        }
        .service_style {
            font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
            width: 55%;
            border-collapse: collapse;
            margin: 0 22px;
        }

        .service_style td, .service_style th {
            font-size: 1em;
            border: 1px solid #4197c7;
            padding: 3px 7px 2px 7px;
        }

        .service_style th {
            font-size: 1.1em;
            text-align: left;
            padding-top: 5px;
            padding-bottom: 4px;
            background-color: #4197c7;
            color: #ffffff;
        }

        .service_style tr.alt td {
            color: #000000;
            background-color: #4197c7;
        }
    </style>

    <style type="text/css">
        .innerTable{
            border-color:#AAAAAA;
            border-top-left-radius: 3px;
            border-top-right-radius: 3px;
            -moz-border-top-left-radius: 3px;
            -moz-border-top-right-radius: 3px;
            -webkit-border-top-left-radius: 3px;
            -webkit-border-top-right-radius: 3px;
        }
        .innerTable .headrow{
            text-align:center;
            font-weight:bold;
            background-color:#C5C5C5;
        }
        .innerTable td{
            border-color:#AAAAAA;
        }

        .dateSpan{
            font-weight: normal;
            font-size: 12px;
            color: #808080;
        }
    </style> 

    <style>
        table#t01 {
            width:100%;
            margin: 10px;

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
            background-color: gray;
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
    
    <script>

        jQuery(document).on("keypress", ".number", function(evt) {
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46)
                return false;

            return true;
        });

        jQuery(document).ready(function() {


            jQuery("#check_upload").hide();
            jQuery("#enlisted").click(function() {

                if (jQuery("#enlisted").prop('checked')) {

                    jQuery("#check_upload").show();
                    
                } else {
                    jQuery("#check_upload").hide();
                    
                }

            });
          
            jQuery('#rent_advance_data').hide();
            jQuery('.year_basis').hide();
            jQuery('.monthly_rent_based').hide();
            jQuery('.rent_wise_change_vat').hide();
            jQuery('.rent_wise_change').hide();
            jQuery('.adj_year_basis').hide();
           
            
            var $j = jQuery.noConflict();
            // jQuery('#window').jqxWindow({height: 150, width: 500, autoOpen: false, cancelButton: jQuery('#closeButton')});
            jQuery('#window').jqxWindow({height: 600, maxWidth: 1200, width: 1150, autoOpen: false, zIndex: 10000});
            val = 0;
            
            var theme = 'classic';
 
 
    jQuery("#sendButton").on('click', function(e) {

          if (business_address_check()) {
                var form = jQuery('#form').serialize();
                jQuery("#sendButton").hide();
                jQuery("#loading").show();
                  
                    jQuery.ajax({
                        url: "<?= base_url() ?>index.php/rent_schedule_payment/add_edit_action/<?= $add_edit ?>/<?= $id ?>",
                        type: "POST",
                        data: form,
                        // contentType: false,
                        cache: false,
                        //processData:false,
                        datatype: "json",
                        success: function(response)
                        {
                            var json = jQuery.parseJSON(response);
                            if (json.Message != 'OK')
                            {
                                alert(json.Message);
                                jQuery("#sendButton").show();
                                jQuery("#loading").hide();
                                alert(json.Message);
                                return false;
                            } else {

                                var row = {};

                                row["id"] = json['row_info'].id;
                                row["paid_dt"] = json['row_info'].paid_dt;
                                // row["location_address"] = json['row_info'].location_name;
                                row["rent_amount"] = json['row_info'].rent_amount;
                                row["adv_adjustment_amt"] = json['row_info'].adv_adjustment_amt;
                                row["sd_adjust_amt"] = json['row_info'].sd_adjust_amt;
                                row["provision_adjust_amt"] = json['row_info'].provision_adjust_amt;
                                row["fin_v_by"] = json['row_info'].fin_v_by;



                                window.parent.jQuery("#jqxgrid").jqxGrid('clearselection');

                        <? if ($add_edit == 'add') { ?>
                                    var paginginformation = window.parent.jQuery("#jqxgrid").jqxGrid('getpaginginformation');
                                    var insert_index = paginginformation.pagenum * paginginformation.pagesize;
                                    var commit = window.parent.jQuery("#jqxgrid").jqxGrid('addrow', null, row, insert_index);
                                    window.parent.jQuery("#jqxgrid").jqxGrid('selectrow', insert_index);
                        <? } else { ?>
                                    jQuery.each(row, function(key, val) {
                                        //alert('key '+key+"val "+val);
                                        window.parent.jQuery("#jqxgrid").jqxGrid('setcellvalue',<?= $editrow ?>, key, row[key]);
                                    });
                                    window.parent.jQuery("#jqxgrid").jqxGrid('selectrow',<?= $editrow ?>);
                        <? } ?>



                                jQuery("#msgArea").val('');
                                window.parent.jQuery("#error").show();
                                window.parent.jQuery("#error").fadeOut(11500);
                                window.parent.jQuery("#error").html('<img align="absmiddle" src="' + baseurl + 'images/drag.png" border="0" /> &nbsp;Successfully Verified');
                                window.top.EOL.messageBoard.close();
                                window.parent.jQuery("#jqxgrid").jqxGrid('updatebounddata');
                            }

                        },
                        error: function(data) {

                        }
                    });

                }
               
        });

            val = 0;


            var theme = 'classic';
            jQuery('#acceptInput').jqxCheckBox({width: 130, theme: theme});
            jQuery('.text-input-small').addClass('jqx-input');
            jQuery('.text-input-small').addClass('jqx-rc-all');
            jQuery('.text-input-big').addClass('jqx-input');
            jQuery('.text-input-big').addClass('jqx-rc-all');
            if (theme.length > 0) {
                jQuery('.text-input-small').addClass('jqx-input-' + theme);
                jQuery('.text-input-small').addClass('jqx-widget-content-' + theme);
                jQuery('.text-input-small').addClass('jqx-rc-all-' + theme);
                jQuery('.text-input-big').addClass('jqx-input-' + theme);
                jQuery('.text-input-big').addClass('jqx-widget-content-' + theme);
                jQuery('.text-input-big').addClass('jqx-rc-all-' + theme);
            }
            jQuery('#form').jqxValidator({
                rules: [
                    
                    <? if ($add_edit == 'add') { ?>
                                           
                    <?php } ?>
                ]
            });
    });
      

        function business_address_check()
        {

            var counter = jQuery('#landlords_count').val();
            var sum1 = 0;
           

            return true
        }

        

    </script>


    <div  style=" width:100%; margin:auto">  

        <form name="form" id="form" class="form" action="#" method="post" >



     
            <input name="id" type="hidden" id="id" value="<?= isset($result->id) ? $result->id : '' ?>"  class="text-input-small" />
            <input name="paid_date_ref" type="hidden" id="paid_date_ref" value="<?=$single_paid_data->paid_dt?>"  class="text-input-small" />
            <input name="checked_adjustment" type="hidden" id="checked_adjustment" value="<?= isset($result->bill_adjusted_ids) ? $result->bill_adjusted_ids : '' ?>"  class="text-input-small" />
            <!-- <input name="checked_adjustment_with_hash" type="hidden" id="checked_adjustment_with_hash" value="<?= isset($result->bill_ids_hash) ? $result->bill_ids_hash : '' ?>"  class="text-input-small" /> -->
            <input name="checked_adjustment_amt" type="hidden" id="checked_adjustment_amt" value="<?= isset($result->bill_adjusted_amts) ? $result->bill_adjusted_amts : '' ?>"  class="text-input-small" />
            <input name="unchecked_adjustment" type="hidden" id="unchecked_adjustment" value=""  class="text-input-small" />


            <input name="prev_adj_value" type="hidden" id="prev_adj_value" value="<?= isset($result->bill_adj_amt) ? $result->bill_adj_amt : '' ?>"  class="text-input-small" />
            <input name="prev_adj_value_hash" type="hidden" id="prev_adj_value_hash" value="<?= isset($result->bill_ids_hash) ? $result->bill_ids_hash : '' ?>"  class="text-input-small" />
            <input name="test_old" type="hidden" id="test_old" value="<?= isset($result->test) ? $result->test : '' ?>"  class="text-input-small" />
            <input name="tot_diff" type="hidden" id="tot_diff" value=""  class="text-input-small" />
            <input name="total_row" type="hidden" id="total_row" value=""  class="text-input-small" />

            <!--    main agreement data   -->
            <input name="agree_total_row" type="hidden" id="agree_total_row" value=""  class="text-input-small" />
            <input name="agree_counter" type="hidden" id="agree_counter" value=""  class="text-input-small" />
            <input type="hidden" value="<?php echo $single_paid_data->id; ?>" name="rent_paid_history_id" >
            <input type="hidden" value="<?php echo $single_paid_data->checked_schedule_ids; ?>" name="checked_schedule_ids" >


            <input type="hidden" value="<?php echo $single_paid_data->checked_schedule_sd_ids; ?>" name="checked_schedule_sd_ids" >
            <input type="hidden" value="<?php echo $single_paid_data->sd_checked_adjustment_amt; ?>" name="sd_checked_adjustment_amt" >
            <input type="hidden" value="<?php echo $single_paid_data->sd_adjust_amt; ?>" name="sd_adjust_amt" >
            <input type="hidden" value="<?php echo $result_agree_info->location_name; ?>" name="agree_location_name" >
            <input type="hidden" value="<?php echo $result_agree_info->landlord_names; ?>" name="agree_landlord_names" >
            <input type="hidden" value="<?php echo $result_agree_info->total_square_ft; ?>" name="total_square_ft" >
            
            <input type="hidden" value="<?php echo $single_paid_data->sche_payment_sts; ?>" name="sche_payment_sts" >
            <input type="hidden" value="<?php echo $single_paid_data->stop_cost_center_amt; ?>" name="stop_cost_center_amt" >
            
            <input type="hidden" value="<?php echo $others_type_names->loc_names; ?>" name="loc_names" >
            <input type="hidden" value="<?php echo $others_type_names->others_loc_names; ?>" name="others_loc_names" >
            <input type="hidden" value="<?php echo $result_agree_info->tax_wived; ?>" name="tax_wived" >
            <input type="hidden" value="<?php echo $result_agree_info->credit_account ?>" name="credit_account" >

            <?php if($verify_type!='view'){ ?>
                    <?php if($add_edit=='fin'){ ?>
                        <div class="formHeader">Rent Schedule Payment Verify</div>
                    <?php }else{ ?>
                        <div class="formHeader">Rent Schedule Payment Approval</div>
                    <?php } ?>

            <?php }else{ ?>
                <div class="formHeader">Rent Schedule Payment Summery</div>
            <?php } ?>

                <?php
                $html = '';
                $incr_type = '';
                $incr_type_val = $result_agree_info->increment_type;
                if ($incr_type_val == 1) {
                    $incr_type = 'No Increment';
                } elseif ($incr_type_val == 2) {
                    $incr_type='Every '.$result_agree_info->increment_type_val.' Yearly Basis';
                } elseif ($incr_type_val == 3) {
                    $incr_type = 'Only One Time';
                } elseif ($incr_type_val == 4) {
                    $incr_type = 'Fixed Increment setup';
                }

                $html.= '<div style="padding-left:10px;">';
                $html.= '<p class="summery_class" ><b>Payment Summery</b></p>';
                //$html.= '<p class="summery_class"><b>Period :</b> '.date_format($start_date,"d/m/Y").' to '.date_format($end_date,"d/m/Y").' ('.$date_diff.'), '.$pp_str.' Basis</p>'; 

                $html.= '<p class="summery_class"><b>Rent Reference No: </b>' . $result_agree_info->agreement_ref_no . '</p>';
                $html.= '<p class="summery_class"><b>Cost Center: </b>'.$cost_center_name->name.'</p>';
                $html.= '<p class="summery_class"><b>Total Area: </b>'.$result_agree_info->total_square_ft.' sqft</p>';
                $html.= '<p class="summery_class"><b>Location : </b>' . $result_agree_info->location_name . '</p>';
                $html.= '<p class="summery_class"><b>Landlord(s) : </b>' . $result_agree_info->landlord_names . '</p>';
                $html.= '<p class="summery_class"><b>Increment Type : </b>' . $incr_type . '</p>';
                $html.= '<p class="summery_class"><b>Monthly Rent :</b> ' . $result_agree_info->monthly_rent . '</p>';
                $html.= '<p class="summery_class"><b>Advance payment : </b>' . $result_agree_info->total_advance . '</p>';
                //$html.= '<p class="summery_class" style="display:none;"><b>Monthly Adjustment :</b> '.$rent_adjust_data->percent_dir_val.'</p>'; 

                $html.= '</div>';
                echo $html;
                ?>

            <table class="" id="t01" style="width:99%">
                <tr class="headrow">

                    <th style="text-align:center;">Cost Center</th>
                    <th style="text-align:center;"> MIS</th>
                    <th style="text-align:center;"> Account No </th>
                    <th style="text-align:center;"> Account Name</th>
                    <th style="text-align:center;">Dr </th>
                    <th style="text-align:center;">Cr </th>
                    <th style="text-align:center;">Narration</th>

                </tr> 

<?php

$total_dr = 0;
$total_cr = 0;
$vat_applicable = '';
$others_string ='';

// tax calculation - 30 april 2018
$tax_slab_rate = $this->agreement_model->get_tax_slab_rate();
$slab_count= count($tax_slab_rate);
$monthly_rent_with_others_and_arear = $non_prov_paid_data->tot_monthly_rent + $non_prov_paid_data->tot_others_amount + $non_prov_paid_data->tot_area_amount ;
for($si=0;$si<$slab_count;$si++){
    if($monthly_rent_with_others_and_arear >= $tax_slab_rate[$si]->min_amt && $monthly_rent_with_others_and_arear <= $tax_slab_rate[$si]->max_amt){
        $tax_rate=$tax_slab_rate[$si]->tax_percent;
    }
}

if(count($others_type_data)==0 && $others_type_names->others_loc_names!=''){$others_string = ' and '.$others_type_names->others_loc_names;}
if(count($others_type_data)==0 && $others_type_names->others_loc_names==''){$others_string = '';}
if( $result_agree_info->total_square_ft < $vat_percentage->minm_sft ){ $vat_applicable = 'no'; }else{ $vat_applicable = 'yes';} 
//$phpdate = strtotime( $single_paid_data->maturity_dt );
$phpdate = strtotime( $single_paid_data->schedule_strat_dt );
?>
 <input type="hidden" value="<?php echo $vat_applicable; ?>" name="vat_applicable" >
 <input type="hidden" value="<?php echo $phpdate; ?>" name="paid_month" >


<?php
if($single_paid_data->sche_payment_sts!='stop_unpaid_payment' && $single_paid_data->sche_payment_sts!='stop_payment' && $single_paid_data->sche_payment_sts!='stop_payment_pm'){
   

    $i = 1;
    $location_type_data_count = count($location_type_data);
    $others_location_type_data_count = count($others_type_data);
?>
                <input type="hidden" value="<?php echo $location_type_data_count; ?>" name="location_type_data_count" >
                <input type="hidden" value="<?php echo $others_location_type_data_count; ?>" name="others_location_type_data_count" >
                <input type="hidden" value="<?php echo $single_paid_data->agreement_id; ?>" name="rent_ref_id" >
        <?php 
        $account_no='';
        $cost_center_code='';
        $acc_des='Rent Office';
        foreach ($location_type_data as $location_type_data_row) { 
                if($location_type_data_row->code=='0'){
                    if($location_type_data_row->name=='Godown'){ $account_no= $godown_gl[0]->gl_account_no; $cost_center_code=$result_agree_info->agree_cost_center; $acc_des='Rent Godown ';}
                    else{ $account_no= $rent_gl[0]->gl_account_no; $cost_center_code=$result_agree_info->agree_cost_center; }
                    $acc_des='Rent Office';
                }else{
                    $account_no=$location_type_data_row->code;
                    $cost_center_code=$result_agree_info->agree_cost_center;
                    $acc_des=$location_type_data_row->name;
                }
                    
            ?>

                    <tr> 
                        <td> 
                            <input type="hidden" value="<?php echo $cost_center_code; ?>" name="location_type_data_cost_center<?php echo $i; ?>" >
                            <?php echo $cost_center_code; ?> 
                        </td>

                        <td>      
                            <input type="hidden" value="<?php echo $location_type_data_row->location_mis_id; ?>" name="location_type_data_mis<?php echo $i; ?>" >
                            <?php echo $location_type_data_row->location_mis_id; ?>  
                        </td>

                        <td>  
                            <input type="hidden" value="<?php echo $account_no; ?>" name="location_type_data_rent_gl<?php echo $i; ?>" >
                            <?php echo $account_no; ?>  
                        </td>

                        <td> <?=$acc_des;?>
                            <input type="hidden" value="<?php echo $acc_des; ?>" name="location_type_data_acc_des<?php echo $i; ?>" >
                            <input type="hidden" value="<?php echo $location_type_data_row->name; ?>" name="location_type_data_loc_name<?php echo $i; ?>" >
                            <?php //echo $location_type_data_row->name;  ?> 
                        </td>
                        <td style="text-align:right;">  
                            <?php
                                $vat_percent = $vat_percentage->vat_percentage;
                                $location_type_percent = $location_type_data_row->cost_in_percent;
                               
                                //$monthly_rent_with_others_and_arear = $non_prov_paid_data->tot_monthly_rent + $non_prov_paid_data->tot_others_amount + $non_prov_paid_data->tot_area_amount ;
                                $monthly_rent_with_others_and_arear = $non_prov_paid_data->tot_monthly_rent;
                                //$monthly_rent_with_arear = $non_prov_paid_data->tot_monthly_rent + $non_prov_paid_data->tot_area_amount ;
                                $location_type_dr_amount = $monthly_rent_with_others_and_arear * ($location_type_percent / 100);
                                $location_type_vat_amount = $location_type_dr_amount * ($vat_percent / 100);
                                //$location_type_dr_amount_with_vat = $location_type_dr_amount +  $location_type_vat_amount;
                                $location_type_dr_amount_without_vat = $location_type_dr_amount ;
                                echo number_format($location_type_dr_amount_without_vat,2);
                                $total_dr = $total_dr + $location_type_dr_amount_without_vat;
                            ?>
                            <input type="hidden" value="<?php echo $location_type_dr_amount_without_vat; ?>" name="location_type_data_dr_amount<?php echo $i; ?>" >
                        </td>
                        <td> </td>
                        <td> 
                            <?php 
                           
                            $mysqldate = date( 'M Y ', $phpdate );

                            $narration_1 = " Rent of $location_type_data_row->name $others_string at $result_agree_info->location_name for $mysqldate";
                                echo $narration_1 ;
                            ?>
                            
                        </td>

                    </tr>


            <?php $i++;
        } ?>

<!-- others start 30 jan -->
<?php $i = 1;
         foreach ($others_type_data as $others_type_data_row) {  
            if($others_type_data_row->account==0){
                 $acc_des='Rent Office';
                 $oth_account_no=$account_no;
            }else{
                 $acc_des=$others_type_data_row->name;
                 $oth_account_no=$others_type_data_row->account;
            }
?>

                <tr> 
                        <td> 
                            <input type="hidden" value="<?php echo $result_agree_info->agree_cost_center; ?>" name="others_location_type_data_cost_center<?php echo $i; ?>" >
                            <?php echo $result_agree_info->agree_cost_center; ?> 
                        </td>

                        <td>      
                            <input type="hidden" value="<?php echo $others_type_data_row->other_loc_mis_id; ?>" name="others_location_type_data_mis<?php echo $i; ?>" >
                            <?php echo $others_type_data_row->other_loc_mis_id; ?>  
                        </td>

                        <td>  
                            <input type="hidden" value="<?php echo $oth_account_no; ?>" name="others_location_type_data_rent_gl<?php echo $i; ?>" >
                            <?php echo $oth_account_no; ?>  
                        </td>

                        <td> <?=$acc_des;?>
                            <input type="hidden" value="<?php echo $others_type_data_row->other_loc_type_id; ?>" name="others_location_type_data_loc_name<?php echo $i; ?>" >
                            <input type="hidden" value="<?php echo $acc_des; ?>" name="others_location_type_data_acc_desc<?php echo $i; ?>" >
                            <?php //echo $others_type_data_row->other_loc_type_id; ?> 
                        </td>
                        <td style="text-align:right;">  
                            <?php
                               

                                $oth_amt=0;
                                // changed in 3 march 2019

                                // if($others_type_data_row->other_loc_type_id=='Car Parking'){
                                //     $oth_amt=$non_prov_paid_data->tot_others_car;
                                // }
                                // else if($others_type_data_row->other_loc_type_id=='Generator Space'){
                                //     $oth_amt=$non_prov_paid_data->tot_others_generator;
                                // }
                                // else if($others_type_data_row->other_loc_type_id=='Water Supply'){
                                //     $oth_amt=$non_prov_paid_data->tot_others_water;
                                // }
                                // else if($others_type_data_row->other_loc_type_id=='Gas Bill'){
                                //     $oth_amt=$non_prov_paid_data->tot_others_gas;
                                // }
                                // else if($others_type_data_row->other_loc_type_id=='Service Charge'){
                                //     $oth_amt=$non_prov_paid_data->tot_others_service;
                                // }

                                $oth_amt=$non_prov_paid_data->tot_others_amount * ($others_type_data_row->others_type_percentage / 100);
                                    
                                $only_others_rent =$oth_amt;
                                //$monthly_others = $others_type_data_row->other_cost_in_percent * ($single_paid_data->rent_fraction_day / 100);
                                //$monthly_rent_with_others_and_arear = $non_prov_paid_data->tot_monthly_rent + $non_prov_paid_data->tot_others_amount + $non_prov_paid_data->tot_area_amount ;
                                //$others_location_type_dr_amount = $only_others_rent * ($others_type_data_row->others_type_percentage / 100);

                                $others_location_type_dr_amount = $only_others_rent * ($single_paid_data->rent_fraction_day / 100);
                                $location_type_dr_amount_without_vat = $others_location_type_dr_amount ;
                                echo number_format($location_type_dr_amount_without_vat,2);
                                $total_dr = $total_dr + $location_type_dr_amount_without_vat;
                            ?>
                            <input type="hidden" value="<?php echo $location_type_dr_amount_without_vat; ?>" name="others_location_type_data_dr_amount<?php echo $i; ?>" >
                        </td>
                        <td> </td>
                        <td> 
                            <?php 
                          
                            $mysqldate = date( 'M Y ', $phpdate );
                            $narration_1 = " Rent of $others_type_data_row->other_loc_type_id at $result_agree_info->location_name for $mysqldate";
                                echo $narration_1 ;
                            ?>
                            
                        </td>

                </tr>
        <?php $i++; 
    } ?>
<!-- others end -->
<!-- arrear in seperate row start 3 oct 2018 -->
<?php 
$arrear_display='';
if($non_prov_paid_data->tot_area_amount <= 0){ $arrear_display='display:none'; }
?>
        <tr style="<?=$arrear_display?>"> 
                <td> 
                    <input type="hidden" value="<?php echo $cost_center_name->code; ?>" name="arrear_cost_center" >
                    <?php echo $cost_center_name->code; ?> 
                </td>

                <td>      
                    <input type="hidden" value="<?php echo $location_type_data[0]->location_mis_id; ?>" name="arrear_mis_code" > 
                    <?php echo $location_type_data[0]->location_mis_id; ?>  
                </td>

                <td>  
                    <input type="hidden" value="<?php echo $account_no; ?>" name="arrear_rent_gl" >
                    <?php echo $account_no; ?>  
                </td>

                <td> <?=$acc_des;?>
                    <input type="hidden" value="<?php echo $acc_des; ?>" name="arrear_loc_name" >
         
                </td>
                <td style="text-align:right;">  
                    <?php
                       
                        $only_arrear =$non_prov_paid_data->tot_area_amount;
                        $arrear_dr_amount = $only_arrear * ($single_paid_data->rent_fraction_day / 100);

                        echo number_format($arrear_dr_amount,2);
                        $total_dr = $total_dr + $arrear_dr_amount;
                    ?>
                    <input type="hidden" value="<?php echo $arrear_dr_amount; ?>" name="arrear_dr_amount" >
                    <input type="hidden" value="<?php echo $result_agree_info->arear_remarks; ?>" name="arrear_remarks" >
                </td>
                <td> </td>
                <td> 
                    <?php 
                 
                    $mysqldate = date( 'M Y ', $phpdate );
                    $narration_1 = " $result_agree_info->arear_remarks for the month of $mysqldate for $result_agree_info->location_name ";
                        echo $narration_1 ;
                    ?>
                    
                </td>

        </tr>

<!-- arrear in seperate row end 3 oct 2018 -->


<?php
$total_vat = 0;
$i = 1;
$vat_display='';
$account_no='';
$cost_center_code='';
$others_loc_names='';
$acc_des='Rent Office';
if($vat_applicable=='no'){ $vat_display='display:none'; }
foreach ($location_type_data as $location_type_data_row) {
    if($location_type_data_row->loc_vat_sts=='yes'){
	            if($location_type_data_row->code==0){
                    if($location_type_data_row->name=='Godown'){ $account_no= $godown_gl[0]->gl_account_no; $cost_center_code=$location_type_data_row->cost_center_code; $acc_des='Rent Godown';}
                    else{ $account_no= $rent_gl[0]->gl_account_no; $cost_center_code=$location_type_data_row->cost_center_code; }
                    $acc_des='Rent Office';
                }else{
                    $account_no=$location_type_data_row->code;
                    $cost_center_code=$result_agree_info->agree_cost_center;
                    $acc_des=$location_type_data_row->name;
                }   
    ?>

                    <tr style="<?=$vat_display?>"> 
                        <td> 
                            <input type="hidden" value="<?php echo $cost_center_code; ?>" name="location_vat_cost_center<?php echo $i; ?>" >
                            <?php echo $cost_center_code; ?> 
                        </td>
                        <td> 
                            <input type="hidden" value="<?php echo $location_type_data_row->location_mis_id; ?>" name="location_vat_mis<?php echo $i; ?>" >
                            <?php echo $location_type_data_row->location_mis_id; ?>  
                        </td>
                        <td>
                            <input type="hidden" value="<?php echo $account_no; ?>" name="location_vat_rent_gl<?php echo $i; ?>" >
                            <?php echo $account_no; ?>   
                        </td>
                        <td> <?=$acc_des;?>
                            <input type="hidden" value="<?php echo $acc_des; ?>" name="location_vat_acc_des<?php echo $i; ?>" >
                            <input type="hidden" value="<?php echo $location_type_data_row->name; ?>" name="location_vat_loc_name<?php echo $i; ?>" >
                            <?php //echo $location_type_data_row->name; ?> 
                        </td>
                        <td style="text-align:right;">  
                                <?php
                                $vat_percent = $vat_percentage->vat_percentage;
                                $location_type_percent = $location_type_data_row->cost_in_percent;
                                //$monthly_rent_with_arear = $non_prov_paid_data->tot_monthly_rent + $non_prov_paid_data->tot_others_amount + $non_prov_paid_data->tot_area_amount ;
                                $monthly_rent_with_arear = $non_prov_paid_data->tot_monthly_rent +  $non_prov_paid_data->tot_area_amount ;
                                $location_type_dr_amount = $monthly_rent_with_arear * ($location_type_percent / 100);
                                $location_type_vat_amount = $location_type_dr_amount * ($vat_percent / 100);
                                $total_vat = $total_vat +  $location_type_vat_amount;
                                if($vat_applicable=='no'){ $location_type_vat_amount=0;  $total_vat=0;}
                                echo number_format($location_type_vat_amount,2);
                                if($vat_applicable!='no'){
                                    $total_dr = $total_dr + $location_type_vat_amount;
                                }
                                ?>
                            <input type="hidden" value="<?php echo $location_type_vat_amount; ?>" name="location_vat_dr_amount<?php echo $i; ?>" >
                        </td>
                        <td> </td>
                        <td>

                         <?php 
                         
                            $mysqldate = date( 'M Y ', $phpdate );
                            //$narration_1 = "  Rent of  at $result_agree_info->location_name for $mysqldate VAT";
                            $narration_1 = " Rent of $location_type_data_row->name $others_string at $result_agree_info->location_name for $mysqldate VAT";
                               
                                echo $narration_1 ;
                            ?>

                        </td>

                    </tr>


                            <?php $i++;
    }
} ?>
                    
<!-- others vat 30 jan -->
<?php   
        //$total_vat = 0;
        $i = 1;
        $vat_display='';
        $vat_display_1='';
        //$others_loc_names='';
        //$account_no='';
        //$cost_center_code='';
        
    foreach ($others_type_data as $others_type_data_row) {
            if($others_type_data_row->account==0){
                 $acc_des='Rent Office';
                 $oth_account_no=$account_no;
            }else{
                 $acc_des=$others_type_data_row->name;
                 $oth_account_no=$others_type_data_row->account;
            }  
            
            if($vat_applicable=='no'){ $vat_display='display:none'; $vat_display_1='display:none';}else{$vat_display=''; $vat_display_1='';}  
            if($others_type_data_row->vat_sts=='no'){ $vat_display='display:none'; }else{ }  
             ?>
            <tr style="<?=$vat_display?>"> 
                        <td> 
                            <input type="hidden" value="<?php echo $others_type_data_row->vat_sts; ?>" name="others_location_vat_sts<?php echo $i; ?>" >
                            <input type="hidden" value="<?php echo $others_type_data_row->other_cost_center_code; ?>" name="others_location_vat_cost_center<?php echo $i; ?>" >
                            <?php echo $others_type_data_row->other_cost_center_code; ?> 
                        </td>
                        <td> 
                            <input type="hidden" value="<?php echo $others_type_data_row->other_loc_mis_id; ?>" name="others_location_vat_mis<?php echo $i; ?>" >
                            <?php echo $others_type_data_row->other_loc_mis_id; ?>  
                        </td>
                        <td>
                            <input type="hidden" value="<?php echo $oth_account_no; ?>" name="others_location_vat_rent_gl<?php echo $i; ?>" >
                            <?php echo $oth_account_no; ?>   
                        </td>
                        <td> <?=$acc_des;?>
                            <input type="hidden" value="<?php echo $others_type_data_row->other_loc_type_id; ?>" name="others_location_vat_loc_name<?php echo $i; ?>" >
                            <input type="hidden" value="<?php echo $acc_des; ?>" name="others_location_type_data_acc_descr<?php echo $i; ?>" >
                            <?php //echo $others_type_data_row->other_loc_type_id; ?> 
                        </td>
                        <td style="text-align:right;">  
                                <?php
                                $vat_percent = $vat_percentage->vat_percentage;
                                $location_type_percent = $location_type_data_row->cost_in_percent;
                                //$monthly_others = $others_type_data_row->other_cost_in_percent * ($single_paid_data->rent_fraction_day / 100);
                                $oth_amt=0;
                                // if($others_type_data_row->other_loc_type_id=='Car Parking'){
                                //     $oth_amt=$non_prov_paid_data->tot_others_car;
                                // }
                                // else if($others_type_data_row->other_loc_type_id=='Generator Space'){
                                //     $oth_amt=$non_prov_paid_data->tot_others_generator;
                                // }
                                // else if($others_type_data_row->other_loc_type_id=='Water Supply'){
                                //     $oth_amt=$non_prov_paid_data->tot_others_water;
                                // }
                                // else if($others_type_data_row->other_loc_type_id=='Gas Bill'){
                                //     $oth_amt=$non_prov_paid_data->tot_others_gas;
                                // }
                                // else if($others_type_data_row->other_loc_type_id=='Service Charge'){
                                //     $oth_amt=$non_prov_paid_data->tot_others_service; 
                                // }

                                // changed in march 3, 2019
                                $oth_amt=$non_prov_paid_data->tot_others_amount * ($others_type_data_row->others_type_percentage / 100);    
                                $only_others_rent =$oth_amt;

								// $monthly_rent_with_others_and_arear = $non_prov_paid_data->tot_monthly_rent + $non_prov_paid_data->tot_others_amount + $non_prov_paid_data->tot_area_amount ;
                                // $others_location_type_dr_amount = $monthly_rent_with_others_and_arear * ($others_type_data_row->others_type_percentage / 100);
                                $others_location_type_dr_amount = $only_others_rent * ($single_paid_data->rent_fraction_day / 100);
                                $location_type_vat_amount = $others_location_type_dr_amount * ($vat_percent / 100);
                                $total_vat =  $total_vat + $location_type_vat_amount;
                                if($vat_applicable=='no' || $others_type_data_row->vat_sts=='no'){ $location_type_vat_amount=0;  $total_vat=0;}
                                echo number_format($location_type_vat_amount,2);
                                 $total_dr = $total_dr + $location_type_vat_amount;
                                
                                ?>
                            <input type="hidden" value="<?php echo $location_type_vat_amount; ?>" name="others_location_vat_dr_amount<?php echo $i; ?>" >
                        </td>
                        <td> </td>
                        <td>

                         <?php 
                        
                            $mysqldate = date( 'M Y ', $phpdate );
                            $narration_1 = "  Rent of  $others_type_data_row->other_loc_type_id at $result_agree_info->location_name for $mysqldate VAT";
                                echo $narration_1 ;
                            ?>

                        </td>

            </tr>

        <?php $i++; 
    } ?>
<!-- others vat end -->


                    <!--  7 sep -->
                    <?php
                    if ($prov_result != '') {
                             $prov_result_count = count($prov_result); 

                             ?>
                            
                    <?php 
                    $i=1;
                        foreach ($prov_result as $result_data_row) {} 


                     }?>

<!-- Advance for per landlord  -->
                        <?php $landlords_count = count($landlords_result); ?>
                <input type="hidden" value="<?php echo $landlords_count; ?>" name="landlords_count" id="landlords_count" >
<?php
$i = 1;
$display='';
if($single_paid_data->sche_payment_sts=='stop_cost_center' || $single_paid_data->sche_payment_sts=='advance_rent_payment' || $single_paid_data->sche_payment_sts=='stop_cost_center_pm'){ $display='display:none'; }
if($non_prov_paid_data->tot_adjustment_adv <= 0){ $display='display:none'; }
foreach ($landlords_result as $landlord_row) {
    ?>

                  <tr style="<?=$display;?>">  
                        <td> 
                            <input type="hidden" value="<?php echo $location_type_data[0]->cost_center_code; ?>" name="landlord_adv_cost_center<?php echo $i; ?>" >
                            <input type="hidden" value="<?php echo $landlord_row->vendor_id; ?>" name="single_landlord_id<?php echo $i; ?>" >
                            <?php echo $location_type_data[0]->cost_center_code; ?>  
                        </td>
                        <td>   </td>
                        <td> 
                            <input type="hidden" value="<?php echo $advance_gl[0]->gl_account_no; ?>" name="landlord_adv_rent_gl<?php echo $i; ?>" >
                            <?php echo $advance_gl[0]->gl_account_no; ?>  
                        </td>
                        <td> Advance Rent </td>
                        <td> </td>  

                        <td style="text-align:right;"> 
                            <?php
                            $landlord_percent = $landlord_row->adv_amount_percent;
                            $advance_adj_amount = $non_prov_paid_data->tot_adjustment_adv ;
                            $landlord_adj_amount = $advance_adj_amount * ($landlord_percent / 100);
                            // $location_type_vat_amount = $location_type_dr_amount * ($vat_percent/100) ;
                            echo number_format($landlord_adj_amount,2);
                            if($single_paid_data->sche_payment_sts=='stop_cost_center' || $single_paid_data->sche_payment_sts=='advance_rent_payment' || $single_paid_data->sche_payment_sts=='stop_cost_center_pm'){
                                //$total_cr = $total_cr + $landlord_adj_amount;
                            }else{
                                $total_cr = $total_cr + $landlord_adj_amount;
                            }
                             
                             //echo '-'.$total_cr;
                            ?>
                            <input type="hidden" value="<?php echo $landlord_adj_amount; ?>" name="landlord_adv_adj_amount_cr<?php echo $i; ?>" >
                        </td>
                        <td> 
                            <?php 
                          
                                $mysqldate = date( 'M Y ', $phpdate );
                        
                                $narration_1 = " Adjustment of advance rent from Rent of  $result_agree_info->location_name for $mysqldate";
                                echo $narration_1 ;
                            ?>

                            <input type="hidden" value="<?php echo $landlord_row->name; ?>" name="landlord_adv_ll_name<?php echo $i; ?>" >
                        </td>
                        </td>

                    </tr>


    <?php $i++;
  
} ?>                 
<?php if($single_paid_data->sche_payment_sts=='stop_cost_center_pm' || $total_vat==0 ){ $vat_display_1='display:none'; $hidden_vat=$total_vat; $total_vat=0; } ?>
<?php 
    $gl_account = $vat_gl[0]->gl_account_no;
    $acc_name = 'VAT On Rent';
    
    if($single_paid_data->sche_payment_sts=='stop_cost_center'){ 
        $gl_account = $provision_gl[0]->gl_account_no;
        $acc_name = 'Expense Payable -Rent Office';

    } 

    $accrual_str=''; 
    if($others_type_names->others_loc_names==''){
        $accrual_str=$others_type_names->loc_names;     
    }else{
        $accrual_str=$others_type_names->loc_names .','.$others_type_names->others_loc_names;
    }

?>

                <tr style="<?=$vat_display_1?>"> 
                    <td> 
                        <input type="hidden" value="<?php echo $result_agree_info->agree_cost_center; ?>" name="location_vat_cost_center_cr" >
                        <?php echo $result_agree_info->agree_cost_center; ?>  
                        <?php  //$vat_gl[0]->gl_branch_code; 
                        ?>  
                    </td>
                    <td>  </td>
                    <td>  
                        <input type="hidden" value="<?php echo $vat_gl[0]->gl_account_no; ?>" name="location_vat_rent_gl_cr" >
                        <?php echo $vat_gl[0]->gl_account_no; ?>
                    </td>
                    <td> <?=$acc_name;?> </td>
                    <td> </td> 


                    <td style="text-align:right;"> 
                         <?php 
                             if($vat_applicable=='no'){ $location_type_vat_amount=0;  $total_vat=0;}
                             echo number_format($total_vat,2); 
                              $total_cr = $total_cr + round($total_vat,2);
                             // echo '-'.$total_cr;
                         ?>
                        
                        <input type="hidden" value="<?php echo $total_vat; ?>" name="location_vat_cr_amount" >
                        <input type="hidden" value="<?php echo $vat_percentage->vat_percentage; ?>" name="vat_percentage" >
                        
                       
                    </td>

                    <td> 
                        <? if($single_paid_data->sche_payment_sts=='stop_cost_center'){  ?>
                         
                             <?php echo "Rent of $accrual_str at $result_agree_info->location_name for $mysqldate  VAT";?>
                        <? }else{ ?>   
                            <?php echo $vat_percentage->vat_percentage; ?> pc VAT from <?php echo $result_agree_info->landlord_names;?> for rent of <?=$result_agree_info->location_name;?> for <?=$mysqldate;?>
                        <? } ?>
                    </td>
                </tr>


                <?php
                $i = 1;
                $display='';
                if($single_paid_data->sche_payment_sts=='stop_cost_center' || $single_paid_data->sche_payment_sts=='advance_rent_payment' || $single_paid_data->sche_payment_sts=='stop_cost_center_pm' || $result_agree_info->tax_wived=='wived_yes'){ $display='display:none'; }
                foreach ($landlords_result as $landlord_row) {
                    ?>

                    <tr style="<?=$display;?>"> 
                        <td>
                            <input type="hidden" value="<?php echo $location_type_data[0]->cost_center_code; ?>" name="landlord_tax_cost_center<?php echo $i; ?>" >
                            <input type="hidden" value="<?php echo $landlord_row->vendor_id; ?>" name="single_landlord_id_tax<?php echo $i; ?>" >
                            <?php echo $location_type_data[0]->cost_center_code; ?> 
                        </td>
                        <td>   </td>
                        <td>
                            <input type="hidden" value="<?php echo $tax_gl[0]->gl_account_no; ?>" name="landlord_tax_account_gl<?php echo $i; ?>" >
                            <?php echo $tax_gl[0]->gl_account_no; ?> 
                        </td>
                        <td> Tax-Rent Office
                            <input type="hidden" value="<?php echo $landlord_row->name; ?>" name="landlord_tax_ll_name<?php echo $i; ?>" >
                        </td>
                        <td> </td>  

                        <td style="text-align:right;"> 
                            <?php
                                $landlord_percent = $landlord_row->credit_amount_percent;
                                $adv_landlord_percent = $landlord_row->adv_amount_percent;
                                $advance_adj_amount = $non_prov_paid_data->tot_adjustment_adv;
                                //$monthly_rent_with_others_and_arear = $non_prov_paid_data->tot_monthly_rent + $non_prov_paid_data->tot_others_amount + $non_prov_paid_data->total_others_amount + $non_prov_paid_data->tot_area_amount ;
                                $monthly_rent_with_others_and_arear = $non_prov_paid_data->tot_monthly_rent + $non_prov_paid_data->tot_others_amount  + $non_prov_paid_data->tot_area_amount ;
                                $tax_applicable_amt= $monthly_rent_with_others_and_arear - $others_no_tax_amount->tax_not_apply;
                                //$tax_applicable_amt= $monthly_rent_with_others_and_arear - $others_no_tax_amount->tax_not_apply - (($loc_type_no_tax_percent->tax_not_apply_percent*$non_prov_paid_data->tot_monthly_rent)/100); -- 24 aug 2019
                                $landlord_adj_amount = $tax_applicable_amt * ($landlord_percent / 100);
                                
                                $landlord_tax_amount = $landlord_adj_amount * ($tax_rate / 100);

                                $landlord_adj_amount_adv = $advance_adj_amount * ($adv_landlord_percent / 100);
                                $landlord_tax_amount_for_adv = $landlord_adj_amount_adv * ($tax_rate / 100);
                            
                                $tax_cal = $landlord_tax_amount - $landlord_tax_amount_for_adv; // off in 3 dec 2017
                          
                                echo number_format($landlord_tax_amount,2);
                                if($single_paid_data->sche_payment_sts=='stop_cost_center' || $single_paid_data->sche_payment_sts=='advance_rent_payment' || $single_paid_data->sche_payment_sts=='stop_cost_center_pm' ||  $result_agree_info->tax_wived=='wived_yes'){

                                }else{
                                    $total_cr = $total_cr + $landlord_tax_amount;
                                }
                                
                                //echo '-'.$total_cr;
                            ?>
                            <input type="hidden" value="<?php echo $landlord_tax_amount; ?>" name="landlord_tax_amount<?php echo $i; ?>" >
                        </td>
                        <td> 

                            <input type="hidden" value="<?php echo $tax_rate; ?>" name="landlord_tax_amount_percent<?php echo $i; ?>" >
                            <?php 
                           
                            $mysqldate = date( 'M Y ', $phpdate );
                            $narration_1 = number_format($tax_rate,2) . " pc TAX from $landlord_row->name  for Rent of $result_agree_info->location_name for $mysqldate";

                            echo $narration_1 ;
        
                            ?>

                        </td>

                    </tr>


    <?php $i++;
} ?>

<?php if ($single_paid_data->sd_adjust_amt > 0) { ?>

<!-- sd vat -->

                <tr style="display:none"> 
                        <td> 
                            <input type="hidden" value="<?php echo $sd_gl[0]->gl_branch_code; ?>" name="bank_cost_center_vat_cr" >
                            <?php echo $sd_gl[0]->gl_branch_code; ?>  
                        </td>
                        <td>  </td>
                        <td>  
                            <input type="hidden" value="<?php echo $vat_gl[0]->gl_account_no; ?>" name="bank_sd_gl_vat_cr" >
                            <?php echo $vat_gl[0]->gl_account_no; ?> 
                        </td>
                        <td> Vat on S.D </td>
                        <td> </td> 


                        <td style="text-align:right;"> 
                            <?php 
                                $vat_percent = $vat_percentage->vat_percentage;
                                //$sd_vat_amount = $single_paid_data->sd_adjust_amt * ($vat_percent / 100);
                                $sd_vat_amount = 0;
                                echo number_format($sd_vat_amount,2); 
                                 

                            ?>
                            <input type="hidden" value="<?php echo $sd_vat_amount; ?>" name="bank_sd_cr_vat_amount" >
   
                        </td>


                        <td> <?php echo $vat_percent; ?>% Vat on S.D</td>

                </tr>


<!-- sd tax -->

                <tr style="display:none"> 
                        <td> 
                            <input type="hidden" value="<?php echo $sd_gl[0]->gl_branch_code; ?>" name="bank_cost_center_tax_cr" >
                            <?php echo $sd_gl[0]->gl_branch_code; ?>  
                        </td>
                        <td>  </td>
                        <td>  
                            <input type="hidden" value="<?php echo $tax_gl[0]->gl_account_no; ?>" name="bank_sd_gl_tax_cr" >
                            <?php echo $tax_gl[0]->gl_account_no; ?> 
                        </td>
                        <td> Tax on S.D </td>
                        <td> </td> 


                        <td style="text-align:right;"> 
                            <?php 
                               // $sd_tax_amount = $single_paid_data->sd_adjust_amt * ($tax_percentage->tax_amount / 100);
                                $sd_tax_amount = 0;
                                echo $sd_tax_amount; 

                            ?>
                            <input type="hidden" value="<?php echo $sd_tax_amount; ?>" name="bank_sd_cr_tax_amount" >
   
                        </td>


                        <td> <?php echo $tax_rate; ?>% Tax for S.D </td>

                    </tr>

<!-- sd rest amount -->
<?php
$display='';
if($single_paid_data->sche_payment_sts=='stop_cost_center' || $single_paid_data->sche_payment_sts=='advance_rent_payment' || $single_paid_data->sche_payment_sts=='stop_cost_center_pm'){ $display='display:none'; }
                

?>
            <tr <?=$display;?>> 
                        <td> 
                            <input type="hidden" value="<?php echo $location_type_data[0]->cost_center_code; ?>" name="bank_cost_center_cr" >
                            <?php echo $location_type_data[0]->cost_center_code; ?>  
                        </td>
                        <td>  </td>
                        <td>  
                            <input type="hidden" value="<?php echo $sd_gl[0]->gl_account_no; ?>" name="bank_sd_gl_cr" >
                    <?php echo $sd_gl[0]->gl_account_no; ?>
                        </td>
                        <td> S.D A/C </td>
                        <td> </td> 


                        <td style="text-align:right;"> 
                            <?php 

                            //$final_sd_amount = $single_paid_data->sd_adjust_amt - $sd_tax_amount - $sd_vat_amount;
                            $final_sd_amount = $single_paid_data->sd_adjust_amt;
                            echo number_format($final_sd_amount,2); 
                            if($single_paid_data->sche_payment_sts=='stop_cost_center' || $single_paid_data->sche_payment_sts=='advance_rent_payment' || $single_paid_data->sche_payment_sts=='stop_cost_center_pm'){

                            }else{
                                $total_cr = $total_cr +$final_sd_amount;
                            }
                            
                            //echo '-'.$total_cr;


                            ?>
                            <input type="hidden" value="<?php echo $final_sd_amount; ?>" name="bank_sd_cr_amount" >
   
                        </td>


                        <td> 

                            <?php 
                               
                                $mysqldate = date( 'M Y ', $phpdate );
                                $narration_1 = "  Adjustment of Security Deposit for $mysqldate";
                                echo $narration_1 ;
                            ?>


                        </td>

            </tr>


<?php } ?>
            
            <?php $agree_row_count = $i - 1; ?>   
            <input id="agree_row_count" class="" type="hidden" value="<?php echo $agree_row_count; ?>">


 <!--   <input type="hidden" value="<?php echo $loc_data_count; ?>" name="loc_data_count<?php echo $i; ?>" > -->

<?php if($result_agree_info->credit_account=='landlord'  ){  ?>
    <?php $i = 1;
    $landlords_count = count($landlords_result);
     // $single_paid_data->rent_amount > 0 [ for full adjust (17/8/19) ]
    if($single_paid_data->rent_amount > 0){
        $display='';
    }else{
        $display='display:none'; 
    }
    
    // only for unpaid payment
    if($single_paid_data->sche_payment_sts=='advance_rent_payment' || $single_paid_data->sche_payment_sts=='stop_cost_center' || $single_paid_data->sche_payment_sts=='stop_cost_center_pm'){ $display='display:none'; }
    foreach ($landlords_result as $landlord_row) {
                    $single_ll_id = $landlord_row->vendor_id;
        ?>

                            <tr style="<?=$display;?>"> 
                                <td>
                                    <?php echo 
                                   // $location_type_data[0]->cost_center_code;
                                    $landlord_row->branch_code; ?>
                                    <input type="hidden" value="<?php echo $landlord_row->branch_code; ?>" name="loc_cost_center_code_tot_cr<?php echo $i; ?>" >
                                    <input type="hidden" value="<?php echo $landlord_row->vendor_id; ?>" name="single_landlord_id_tot_cr<?php echo $i; ?>" >
                                </td>
                                <td>
                                
                                 </td>
                                <td>
                                    <?php echo $landlord_row->landlord_acc_no; ?> 
                                     <input type="hidden" value="<?php echo $landlord_row->landlord_acc_no; ?>" name="per_landlord_acc_no_tot_cr<?php echo $i; ?>" >
                                    <input type="hidden" value="<?php echo $landlord_row->landlord_payment_mode; ?>" name="per_landlord_payment_mode_tot_cr<?php echo $i; ?>" >
                                </td>
                                <td> 
                                <?php 
                                    if($landlord_row->landlord_payment_mode=='Pay Order'){
                                        echo $landlord_row->name.'- Payorder Suspense A/c ';
                                    }else{
                                        echo $landlord_row->name;
                                    }
                                ?> 
                                    <input type="hidden" value="<?php echo $landlord_row->name; ?>" name="per_landlord_name<?php echo $i; ?>" >
                                </td>
                                <td> </td>

                                <td style="text-align:right;"> 

            <?php


    if($prov_result!=''){

        }else{

            $prov_tax_amount = 0;
            $new_cal_amount = 0 - $prov_tax_amount; 
            $prov_vat_amount = 0 ;
            $final_cal_amount = $new_cal_amount - $prov_vat_amount;
            $prov_sd = 0;

        }
          
            $landlord_percent = $landlord_row->credit_amount_percent;
            $location_type_dr_amount = $final_cal_amount * ($landlord_percent / 100);

// 6 sep

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
            $total_sd_adjust_amt = $single_paid_data->sd_adjust_amt - $prov_sd;
            $landlord_sd_adjust_amt = $total_sd_adjust_amt * ($landlord_percent / 100);

            $final_amount = $landlord_rent_amt - $landlord_adj_amount - $tax_cal - $landlord_sd_adjust_amt; 
            if($single_paid_data->sche_payment_sts=='stop_cost_center' || $single_paid_data->sche_payment_sts=='stop_cost_center_pm')
            { $final_amount = $landlord_rent_amt; }

            if($result_agree_info->tax_wived=='wived_yes')
            { $final_amount = $landlord_rent_amt - $landlord_adj_amount - $landlord_sd_adjust_amt; }

            $ll_final_amount =  $final_amount;


            echo number_format($ll_final_amount,2);
            if($single_paid_data->sche_payment_sts=='advance_rent_payment' || $single_paid_data->sche_payment_sts=='stop_cost_center' || $single_paid_data->sche_payment_sts=='stop_cost_center_pm'){

            }else{
                 $total_cr = $total_cr + $ll_final_amount;
            }
            
             //echo '-'.$total_cr;
            ?>
                        <input type="hidden" value="<?php echo $ll_final_amount; ?>" name="per_landlord_final_amt_tot_cr<?php echo $i; ?>" >

            </td>
                        <td> 

                        <?php 

                            
                            $mysqldate = date( 'M Y ', $phpdate );
                            $with=' with ';
                           
                            if($landlord_row->landlord_payment_mode=='Pay Order'){

                                $narration_1 = " PO favouring $landlord_row->ll_name  ";
                            }else{
                                 //$narration_1 = " Credited rent for $mysqldate  $with $others_type_names->loc_names, $others_type_names->others_loc_names";
                                 //changed in 9 oct 2018
                                 $narration_1 = " Credited rent of $result_agree_info->location_name for $mysqldate  $with $others_type_names->loc_names, $others_type_names->others_loc_names";
                               
                            } 
                            echo $narration_1 ;
                            //Credited rent for December 17 with generator space
                        ?>

                             <input type="hidden" value="<?php echo $landlord_row->ll_name; ?>" name="per_landlord_amt_name_tot_cr<?php echo $i; ?>" >
                        </td>
                    </tr> 

        <?php
            $i++;
         } ?>
<?php } ?>

<!-- 19 march 2019 start for another acc accept ll account -->
<?php if($result_agree_info->credit_account!='landlord'){  ?>
    <?php 
    $i = 1;
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
    
    $display='';
    // only for unpaid payment
    if($single_paid_data->sche_payment_sts=='advance_rent_payment' || $single_paid_data->sche_payment_sts=='stop_cost_center' || $single_paid_data->sche_payment_sts=='stop_cost_center_pm'){ $display='display:none'; }
  
           
        ?>

                            <tr style="<?=$display;?>"> 
                                <td>
                                    <?php echo $location_type_data[0]->cost_center_code; ?>
                                    <input type="hidden" value="<?php echo $location_type_data[0]->cost_center_code; ?>" name="loc_cost_center_code_tot_cr<?php echo $i; ?>" >
                                    <input type="hidden" value="0" name="single_landlord_id_tot_cr<?php echo $i; ?>" >
                                </td>
                                <td>
                                
                                 </td>
                                <td>
                                    <?php echo $cr_gl; ?> 
                                     <input type="hidden" value="<?php echo $cr_gl; ?>" name="per_landlord_acc_no_tot_cr<?php echo $i; ?>" >
                                    <input type="hidden" value="GL" name="per_landlord_payment_mode_tot_cr<?php echo $i; ?>" >
                                </td>
                                <td> 
                                    <?php echo $cr_ac_name; ?> 
                                    <input type="hidden" value="<?php echo $cr_ac_name; ?>" name="per_landlord_name<?php echo $i; ?>" >
                                </td>
                                <td> </td>

                                <td style="text-align:right;"> 

            <?php


    if($prov_result!=''){

        }else{

            $prov_tax_amount = 0;
            $new_cal_amount = 0 - $prov_tax_amount; 
            $prov_vat_amount = 0 ;
            $final_cal_amount = $new_cal_amount - $prov_vat_amount;
            $prov_sd = 0;

        }
          
          
            $location_type_dr_amount = $final_cal_amount ;

// 6 sep

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
            $total_sd_adjust_amt = $single_paid_data->sd_adjust_amt - $prov_sd;
            $landlord_sd_adjust_amt = $total_sd_adjust_amt ;

            $final_amount = $landlord_rent_amt - $landlord_adj_amount - $tax_cal - $landlord_sd_adjust_amt; 
            if($single_paid_data->sche_payment_sts=='stop_cost_center' || $single_paid_data->sche_payment_sts=='stop_cost_center_pm')
            { $final_amount = $landlord_rent_amt; }

            if($result_agree_info->tax_wived=='wived_yes')
            { $final_amount = $landlord_rent_amt - $landlord_adj_amount - $landlord_sd_adjust_amt; }

            $ll_final_amount =  $final_amount;


            echo number_format($ll_final_amount,2);
            if($single_paid_data->sche_payment_sts=='advance_rent_payment' || $single_paid_data->sche_payment_sts=='stop_cost_center' || $single_paid_data->sche_payment_sts=='stop_cost_center_pm'){

            }else{
                 $total_cr = $total_cr + $ll_final_amount;
            }
            
             //echo '-'.$total_cr;
            ?>
                        <input type="hidden" value="<?php echo $ll_final_amount; ?>" name="per_landlord_final_amt_tot_cr<?php echo $i; ?>" >

            </td>
                        <td> 

                        <?php 

                            
                            $mysqldate = date( 'M Y ', $phpdate );
                            $with=' with ';
                           
                            
                            $narration_1 = " Credited rent of $result_agree_info->location_name for $mysqldate  $with $others_type_names->loc_names, $others_type_names->others_loc_names";
                            echo $narration_1 ;
                            //Credited rent for December 17 with generator space
                        ?>

                             <input type="hidden" value="<?php echo $cr_ac_name; ?>" name="per_landlord_amt_name_tot_cr<?php echo $i; ?>" >
                        </td>
                    </tr> 

        
<?php } ?>
<!-- 19 march 2019 end -->
<?php if($single_paid_data->sche_payment_sts=='advance_rent_payment'){ ?>

            <tr> 
                    <td> 
                        <?php echo $advance_gl[0]->gl_branch_code; ?>  
                    </td>
                    <td>  </td>
                    <td>  
                        <input type="hidden" value="<?php echo $advance_gl[0]->gl_account_no; ?>" name="ria_adv_gl" >
                        <input type="hidden" value="<?php echo $advance_gl[0]->gl_branch_code; ?>" name="ria_br_code" >
                        <?php echo $advance_gl[0]->gl_account_no; ?>
                    </td>
                    <td> Rent for Rent in Advance</td>
                    <td> </td> 


                    <td style="text-align:right;"> 
                        
                        <?php 
                            $monthly_rent_with_others_and_arear = $non_prov_paid_data->tot_monthly_rent + $non_prov_paid_data->tot_others_amount + $non_prov_paid_data->tot_area_amount ;
                        
                            $mysqldate = date( 'M Y ', $phpdate );
                            $final_amount = $monthly_rent_with_others_and_arear;
                        echo number_format($final_amount,2); 
                        $total_cr = $total_cr + round($final_amount,2);
                        //echo '-'.$total_cr;

                        ?>
                        <input type="hidden" value="<?php echo $final_amount; ?>" name="ria_adv_amount" >
                    </td>


                    <td> Credit to Advance rent for <?=$mysqldate?></td>

            </tr>

<?php } 

if($single_paid_data->sche_payment_sts=='stop_cost_center' || $single_paid_data->sche_payment_sts=='stop_cost_center_pm'){
 $accrual_str=''; 
    if($others_type_names->others_loc_names==''){
        $accrual_str=$others_type_names->loc_names;     
    }else{
        $accrual_str=$others_type_names->loc_names .','.$others_type_names->others_loc_names;
    }

?>

            <tr> 
                    <td> 
                        <?php echo $location_type_data[0]->cost_center_code; ?>  
                    </td>
                    <td>  </td>
                    <td>  
                        <input type="hidden" value="<?php echo $provision_gl[0]->gl_account_no; ?>" name="scc_adv_gl" >
                        <input type="hidden" value="<?php echo $location_type_data[0]->cost_center_code; ?>" name="scc_br_code" >
                        <?php echo $provision_gl[0]->gl_account_no; ?>
                    </td>
                    <td> Expense Payable -Rent Office</td>
                    <td> </td> 


                    <td style="text-align:right;"> 
                        
                        <?php 
                            $monthly_rent_with_others_and_arear = $non_prov_paid_data->tot_monthly_rent + $non_prov_paid_data->tot_others_amount + $non_prov_paid_data->tot_area_amount ;
                        
                            $mysqldate = date( 'M Y ', $phpdate );
                            if($single_paid_data->sche_payment_sts=='stop_cost_center_pm'){
                                $final_amount = $monthly_rent_with_others_and_arear + $hidden_vat; 
                            }else{
                                $final_amount = $monthly_rent_with_others_and_arear;
                            }
                        echo number_format($final_amount,2); 
                         $total_cr = $total_cr + round($final_amount,2);
                         //echo '-'.$total_cr;

                        ?>
                        <input type="hidden" value="<?php echo $final_amount; ?>" name="scc_adv_amount" >
                    </td>


                    <td> Rent of <?=$accrual_str;?> at <?=$result_agree_info->location_name?> for <?=$mysqldate?></td>

            </tr>

<?php } 



}    //  not for stop_unpaid_payment

?>

<!-- only for stop unpaid payment -->

<?php
if($single_paid_data->sche_payment_sts=='stop_unpaid_payment' || $single_paid_data->sche_payment_sts=='stop_payment' || $single_paid_data->sche_payment_sts=='stop_payment_pm'){
    
    $narr_str=''; 
    $loc_narr_str=''; 
    $other_narr_str=''; 
        if($others_type_names->others_loc_names==''){
            $loc_narr_str = $narr_str=$others_type_names->loc_names;     
        }else{
            $narr_str=$others_type_names->loc_names .','.$others_type_names->others_loc_names;
            $other_narr_str= $others_type_names->others_loc_names;
            $loc_narr_str = $others_type_names->loc_names;     
        }

    $total_dr = 0;
    $total_cr = 0;
    $vat_percent = $vat_percentage->vat_percentage;

    $mysqldate = date( 'M Y ', $phpdate );

    //$monthly_rent_with_others_and_arear = $non_prov_paid_data->tot_monthly_rent + $non_prov_paid_data->tot_others_amount + $non_prov_paid_data->tot_area_amount ;
    $monthly_rent_with_others_and_arear = $non_prov_paid_data->tot_monthly_rent + $non_prov_paid_data->tot_area_amount ;
    $monthly_rent_with_all_for_unpaid = $monthly_rent_with_others_and_arear - $single_paid_data->stop_cost_center_amt;
    
    $monthly_rent_with_all_for_stop_cost = $single_paid_data->stop_cost_center_amt;
    $vat_for_accrual_pm = $monthly_rent_with_all_for_stop_cost * ($vat_percent / 100);

    // 10 oct 2018
    $monthly_rent_without_others_for_stop_cost = $single_paid_data->stop_cost_center_amt - $non_prov_paid_data->tot_others_amount;
    $only_others_for_stop_cost = $non_prov_paid_data->tot_others_amount;
    
    $total_vat = $monthly_rent_with_all_for_unpaid * ($vat_percent / 100);

    $i = 1;
    $account_no='';
    $cost_center_code='';
    $location_type_data_count = count($location_type_data);
    $others_location_type_data_count = count($others_type_data);
    $acc_des='Rent Office';
    ?>
                <input type="hidden" value="<?php echo $location_type_data_count; ?>" name="location_type_data_count" >
                <input type="hidden" value="<?php echo $others_location_type_data_count; ?>" name="others_location_type_data_count" >
                <input type="hidden" value="<?php echo $single_paid_data->agreement_id; ?>" name="rent_ref_id" >
        <?php 
            if($single_paid_data->sche_payment_sts!='stop_payment' && $single_paid_data->sche_payment_sts!='stop_payment_pm'){   // only for stop_unpaid_payment
                foreach ($location_type_data as $location_type_data_row) { 
                   if($location_type_data_row->code==0){ 
                        if($location_type_data_row->name=='Godown'){ $account_no= $godown_gl[0]->gl_account_no; $cost_center_code=$godown_gl[0]->gl_branch_code; $acc_des='Rent Godown ';}
                        else{ $account_no= $rent_gl[0]->gl_account_no; $cost_center_code=$location_type_data_row->cost_center_code; }
                        $acc_des='Rent Office';
                    }else{
                        $account_no=$location_type_data_row->code;
                        $cost_center_code=$result_agree_info->agree_cost_center;
                        $acc_des=$location_type_data_row->name;
                    }  
            ?>

                    <tr> 
                        <td> 
                            <input type="hidden" value="<?php echo $cost_center_code; ?>" name="location_type_data_cost_center<?php echo $i; ?>" >
                            <?php echo $cost_center_code; ?> 
                        </td>

                        <td>      
                            <input type="hidden" value="<?php echo $location_type_data_row->location_mis_id; ?>" name="location_type_data_mis<?php echo $i; ?>" >
                            <?php echo $location_type_data_row->location_mis_id; ?>  
                        </td>

                        <td>  
                            <input type="hidden" value="<?php echo $account_no; ?>" name="location_type_data_rent_gl<?php echo $i; ?>" >
                            <?php echo $account_no; ?>  
                        </td>

                        <td> <?=$acc_des;?>
                            <input type="hidden" value="<?php echo $acc_des; ?>" name="location_type_data_acc_des<?php echo $i; ?>" >
                            <input type="hidden" value="<?php echo $location_type_data_row->name; ?>" name="location_type_data_loc_name<?php echo $i; ?>" >
                            <?php //echo $location_type_data_row->name; ?> 
                        </td>
                        <td style="text-align:right;">  
                                <?php

                                $location_type_percent = $location_type_data_row->cost_in_percent;
                                //$monthly_rent_with_others_and_arear = $non_prov_paid_data->tot_monthly_rent + $non_prov_paid_data->total_others_amount + $non_prov_paid_data->total_others_amount + $non_prov_paid_data->tot_area_amount ;
                                
                                $location_type_dr_amount = $monthly_rent_with_all_for_unpaid * ($location_type_percent / 100);
                                $location_type_vat_amount = $location_type_dr_amount * ($vat_percent / 100);
                                $location_type_dr_amount_with_vat = $location_type_dr_amount +  $location_type_vat_amount;
                                $location_type_dr_amount_without_vat = $location_type_dr_amount;
                                echo number_format($location_type_dr_amount_without_vat,2);
                                $total_dr = $total_dr + $location_type_dr_amount_without_vat;

                                ?>
                            <input type="hidden" value="<?php echo round($location_type_dr_amount_without_vat,2); ?>" name="location_type_data_dr_amount<?php echo $i; ?>" >
                        </td>
                        <td> </td>
                        <td> 
                            <?php 
                           
                            $narration_1 = " Rent of $location_type_data_row->name at $result_agree_info->location_name $others_string for $mysqldate";
                                echo $narration_1 ;
                            ?>
                            
                        </td>

                    </tr>


        <?php $i++;
        } 

    }?>

 <!-- others start 30 jan -->
<?php $i = 1;
if($single_paid_data->sche_payment_sts!='stop_payment' && $single_paid_data->sche_payment_sts!='stop_payment_pm'){   // only for stop_unpaid_payment
         foreach ($others_type_data as $others_type_data_row) {  
            if($others_type_data_row->account==0){
                 $acc_des='Rent Office';
                 $oth_account_no=$account_no;
            }else{
                 $acc_des=$others_type_data_row->name;
                 $oth_account_no=$others_type_data_row->account;
            }
?>

                <tr> 
                        <td> 
                            <input type="hidden" value="<?php echo $others_type_data_row->other_cost_center_code; ?>" name="others_location_type_data_cost_center<?php echo $i; ?>" >
                            <?php echo $others_type_data_row->other_cost_center_code; ?> 
                        </td>

                        <td>      
                            <input type="hidden" value="<?php echo $others_type_data_row->other_loc_mis_id; ?>" name="others_location_type_data_mis<?php echo $i; ?>" >
                            <?php echo $others_type_data_row->other_loc_mis_id; ?>  
                        </td>

                        <td>  
                            <input type="hidden" value="<?php echo $oth_account_no; ?>" name="others_location_type_data_rent_gl<?php echo $i; ?>" >
                            <?php echo $oth_account_no; ?>  
                        </td>

                        <td> <?=$acc_des;?>
                            <input type="hidden" value="<?php echo $others_type_data_row->other_loc_type_id; ?>" name="others_location_type_data_loc_name<?php echo $i; ?>" >
                            <input type="hidden" value="<?php echo $acc_des; ?>" name="others_location_type_data_acc_desc<?php echo $i; ?>" >
                            <?php //echo $others_type_data_row->other_loc_type_id; ?> 
                        </td>
                        <td style="text-align:right;">  
                            <?php
                                $vat_percent = $vat_percentage->vat_percentage;
                                

                                // 13 sep 2018
                                $oth_amt=0;
                                // if($others_type_data_row->other_loc_type_id=='Car Parking'){
                                //     $oth_amt=$non_prov_paid_data->tot_others_car;
                                // }
                                // else if($others_type_data_row->other_loc_type_id=='Generator Space'){
                                //     $oth_amt=$non_prov_paid_data->tot_others_generator;
                                // }
                                // else if($others_type_data_row->other_loc_type_id=='Water Supply'){
                                //     $oth_amt=$non_prov_paid_data->tot_others_water;
                                // }
                                // else if($others_type_data_row->other_loc_type_id=='Gas Bill'){
                                //     $oth_amt=$non_prov_paid_data->tot_others_gas;
                                // }
                                // else if($others_type_data_row->other_loc_type_id=='Service Charge'){
                                //     $oth_amt=$non_prov_paid_data->tot_others_service;
                                // }

                                // 3 march 2019
                                    
                                $oth_amt=$non_prov_paid_data->tot_others_amount * ($others_type_data_row->others_type_percentage / 100);    
                                $only_others_rent =$oth_amt;

                                // $monthly_rent_with_others_and_arear = $non_prov_paid_data->tot_monthly_rent + $non_prov_paid_data->tot_others_amount + $non_prov_paid_data->tot_area_amount ;
                                // $others_location_type_dr_amount = $monthly_rent_with_others_and_arear * ($others_type_data_row->others_type_percentage / 100);
                                 $others_location_type_dr_amount = $only_others_rent * ($single_paid_data->rent_fraction_day / 100);
        
        
                                $location_type_dr_amount_without_vat = $others_location_type_dr_amount ;
                                echo number_format($location_type_dr_amount_without_vat,2);
                                $total_dr = $total_dr + $location_type_dr_amount_without_vat;
                            ?>
                            <input type="hidden" value="<?php echo $location_type_dr_amount_without_vat; ?>" name="others_location_type_data_dr_amount<?php echo $i; ?>" >
                        </td>
                        <td> </td>
                        <td> 
                            <?php 
                          
                            $mysqldate = date( 'M Y ', $phpdate );
                            $narration_1 = " Rent of $others_type_data_row->other_loc_type_id at $result_agree_info->location_name for $mysqldate";
                                echo $narration_1 ;
                            ?>
                            
                        </td>

                </tr>
        <?php $i++; 
    }
}     ?>
<!-- others end -->   

<?php
$total_vat = 0;
$i = 1;
$vat_display='';
$acc_des='Rent Office';
$account_no='';
$cost_center_code='';
$others_loc_names='';
if($vat_applicable=='no'){ $vat_display='display:none'; }
if($single_paid_data->sche_payment_sts!='stop_payment' && $single_paid_data->sche_payment_sts!='stop_payment_pm'){   // only for stop_unpaid_payment
    foreach ($location_type_data as $location_type_data_row) {
    		if($location_type_data_row->code==0){ 
                if($location_type_data_row->name=='Godown'){ $account_no= $godown_gl[0]->gl_account_no; $cost_center_code=$godown_gl[0]->gl_branch_code; $acc_des='Rent Godown';}
                else{ $account_no= $rent_gl[0]->gl_account_no; $cost_center_code=$location_type_data_row->cost_center_code; }
                $acc_des='Rent Office';
            }else{
                $account_no=$location_type_data_row->code;
                $cost_center_code=$result_agree_info->agree_cost_center;
                $acc_des=$location_type_data_row->name;
            }  
    ?>

                    <tr style="<?=$vat_display?>"> 
                        <td> 
                            <input type="hidden" value="<?php echo $cost_center_code; ?>" name="location_vat_cost_center<?php echo $i; ?>" >
                            <?php echo $cost_center_code; ?> 
                        </td>
                        <td> 
                            <input type="hidden" value="<?php echo $location_type_data_row->location_mis_id; ?>" name="location_vat_mis<?php echo $i; ?>" >
                            <?php echo $location_type_data_row->location_mis_id; ?>  
                        </td>
                        <td>
                            <input type="hidden" value="<?php echo $account_no; ?>" name="location_vat_rent_gl<?php echo $i; ?>" >
                            <?php echo $account_no; ?>   
                        </td>
                        <td> <?=$acc_des;?>
                            <input type="hidden" value="<?php echo $location_type_data_row->name; ?>" name="location_vat_loc_name<?php echo $i; ?>" >
                            <?php //echo $location_type_data_row->name; ?> 
                        </td>
                        <td style="text-align:right;">  
                                <?php
                                $vat_percent = $vat_percentage->vat_percentage;
                                $location_type_percent = $location_type_data_row->cost_in_percent;
                               // $monthly_rent_with_others_and_arear = $non_prov_paid_data->tot_monthly_rent + $non_prov_paid_data->tot_others_amount + $non_prov_paid_data->tot_area_amount ;
                                $monthly_rent_with_others_and_arear = $non_prov_paid_data->tot_monthly_rent + $non_prov_paid_data->tot_area_amount ;
                                $location_type_dr_amount = $monthly_rent_with_others_and_arear * ($location_type_percent / 100);
                                $location_type_vat_amount = $location_type_dr_amount * ($vat_percent / 100);
                                $total_vat = $total_vat +  $location_type_vat_amount;
                                if($vat_applicable=='no'){ $location_type_vat_amount=0;  $total_vat=0;}
                                echo number_format($location_type_vat_amount,2);
                                if($vat_applicable!='no'){
                                    $total_dr = $total_dr + $location_type_vat_amount;
                                }
                                ?>
                            <input type="hidden" value="<?php echo $location_type_vat_amount; ?>" name="location_vat_dr_amount<?php echo $i; ?>" >
                        </td>
                        <td> </td>
                        <td>

                         <?php 
                            
                            $mysqldate = date( 'M Y ', $phpdate );
                            $narration_1 = "  Rent of  $location_type_data_row->name at $result_agree_info->location_name for $mysqldate  VAT";
                                
                                echo $narration_1 ;

                            ?>

                        </td>

                    </tr>


                            <?php $i++;
        } 

    }?>

    <!-- others vat 30 jan -->
<?php   
        //$total_vat = 0;
        $i = 1;
        $vat_display='';
        //$account_no='';
        //$others_loc_names ='';
if($single_paid_data->sche_payment_sts!='stop_payment' && $single_paid_data->sche_payment_sts!='stop_payment_pm'){         
    foreach ($others_type_data as $others_type_data_row) {  
            if($others_type_data_row->account==0){
                 $acc_des='Rent Office';
                 $oth_account_no=$account_no;
            }else{
                 $acc_des=$others_type_data_row->name;
                 $oth_account_no=$others_type_data_row->account;
            }
           
            if($vat_applicable=='no'){ $vat_display='display:none'; }else{$vat_display='';}   
            if($others_type_data_row->vat_sts=='no'){ $vat_display='display:none'; }else{$vat_display='';} 
            ?>
            <tr style="<?=$vat_display?>"> 
                        <td> 
                            <input type="hidden" value="<?php echo $result_agree_info->agree_cost_center; ?>" name="others_location_vat_cost_center<?php echo $i; ?>" >
                            <input type="hidden" value="<?php echo $others_type_data_row->vat_sts; ?>" name="others_location_vat_sts<?php echo $i; ?>" >
                            <?php echo $result_agree_info->agree_cost_center; ?> 
                        </td>
                        <td> 
                            <input type="hidden" value="<?php echo $others_type_data_row->other_loc_mis_id; ?>" name="others_location_vat_mis<?php echo $i; ?>" >
                            <?php echo $others_type_data_row->other_loc_mis_id; ?>  
                        </td>
                        <td>
                            <input type="hidden" value="<?php echo $oth_account_no; ?>" name="others_location_vat_rent_gl<?php echo $i; ?>" >
                            <?php echo $oth_account_no; ?>   
                        </td>
                        <td> <?=$acc_des;?>
                            <input type="hidden" value="<?php echo $others_type_data_row->other_loc_type_id; ?>" name="others_location_vat_loc_name<?php echo $i; ?>" >
                            <input type="hidden" value="<?php echo $acc_des; ?>" name="others_location_type_data_acc_descr<?php echo $i; ?>" >
                            <?php //echo $others_type_data_row->other_loc_type_id; ?> 
                        </td>
                        <td style="text-align:right;">  
                                <?php
                                $vat_percent = $vat_percentage->vat_percentage;
                                $location_type_percent = $others_type_data_row->other_cost_in_percent;
                                //$monthly_others = $non_prov_paid_data->tot_others_amount ;
                                //$monthly_others = $others_type_data_row->other_cost_in_percent * ($single_paid_data->rent_fraction_day / 100);
                                //$monthly_rent_with_others_and_arear = $non_prov_paid_data->tot_monthly_rent + $non_prov_paid_data->tot_others_amount + $non_prov_paid_data->tot_area_amount ;
                                //13 sep 2018
                                $oth_amt=0;
                                // if($others_type_data_row->other_loc_type_id=='Car Parking'){
                                //     $oth_amt=$non_prov_paid_data->tot_others_car;
                                // }
                                // else if($others_type_data_row->other_loc_type_id=='Generator Space'){
                                //     $oth_amt=$non_prov_paid_data->tot_others_generator;
                                // }
                                // else if($others_type_data_row->other_loc_type_id=='Water Supply'){
                                //     $oth_amt=$non_prov_paid_data->tot_others_water;
                                // }
                                // else if($others_type_data_row->other_loc_type_id=='Gas Bill'){
                                //     $oth_amt=$non_prov_paid_data->tot_others_gas;
                                // }
                                // else if($others_type_data_row->other_loc_type_id=='Service Charge'){
                                //     $oth_amt=$non_prov_paid_data->tot_others_service;
                                // }
                               
                                $oth_amt=$non_prov_paid_data->tot_others_amount * ($others_type_data_row->others_type_percentage / 100);    
                                $only_others_rent =$oth_amt;
                                $others_location_type_dr_amount = $only_others_rent * ($single_paid_data->rent_fraction_day / 100);

                                $location_type_vat_amount = $others_location_type_dr_amount * ($vat_percent / 100);
                                $total_vat =  $total_vat + $location_type_vat_amount;
                               // if($vat_applicable=='no'){ $location_type_vat_amount=0;  $total_vat=0;}
                                echo number_format($location_type_vat_amount,2);
                                if($others_type_data_row->vat_sts!='no'){ 
                                     $total_dr = $total_dr + $location_type_vat_amount;
                                }
                               
                                
                                ?>
                            <input type="hidden" value="<?php echo $location_type_vat_amount; ?>" name="others_location_vat_dr_amount<?php echo $i; ?>" >
                        </td>
                        <td> </td>
                        <td>

                         <?php 
                        
                            $mysqldate = date( 'M Y ', $phpdate );
                            $narration_1 = "  Rent of  $others_type_data_row->other_loc_type_id at $result_agree_info->location_name for $mysqldate";
                                echo $narration_1 ;
                            ?>

                        </td>

            </tr>

        <?php $i++; 
    }
}     ?>
<!-- others vat end -->

<!-- stop receive paid amount (M.R + arrear) to provision gl -->

            <tr> 
                        <td> 
                            <input type="hidden" value="<?php echo $result_agree_info->agree_cost_center; ?>" name="scc_cost_center" >
                            <?php echo $result_agree_info->agree_cost_center; ?> 
                        </td>

                        <td>      
                            <input type="hidden" value="" name="scc_data_mis" >
                            <?php echo ''; ?>  
                        </td>

                        <td>  
                            <input type="hidden" value="<?php echo $provision_gl[0]->gl_account_no; ?>" name="scc_prov_gl" >
                            <?php echo $provision_gl[0]->gl_account_no; ?>  
                        </td>

                        <td> Expense Payable -Rent Office  </td>
                      
                        <td style="text-align:right;">  
                                <?php
                                $accrual_amt=0;
                                $vat_cr_amount_new=0;
                                if($single_paid_data->sche_payment_sts=='stop_payment_pm'){
                                    $vat_cr_amount_new = $vat_for_accrual_pm ;
                                    if($vat_applicable=='no'){ $vat_cr_amount_new=0;}
                                   
                                    //$accrual_amt = $monthly_rent_with_all_for_stop_cost ;
                                    $accrual_amt = $monthly_rent_without_others_for_stop_cost ;
                                }else{
                                    $accrual_amt = $monthly_rent_without_others_for_stop_cost;
                                }
                                    echo number_format($accrual_amt,2);
                                    $total_dr = $total_dr + $accrual_amt;
                                ?>
                            <input type="hidden" value="<?php echo $accrual_amt; ?>" name="scc_data_dr_amount" >
                        </td>
                        <td> </td>
                        <td> 
                            <?php 
                            
                            $narration_1 = " Rent of $loc_narr_str at $result_agree_info->location_name for $mysqldate";
                               echo $narration_1 ;
                            ?>
                            <input type="hidden" value="<?php echo $narration_1; ?>" name="scc_data_narr" >
                        </td>

            </tr>


<!-- stop receive paid amount (only others) to provision gl -->
        <?php 
        $others_display='';
        if($only_others_for_stop_cost > 0 ){ $others_display='';}else{$others_display='display:none';}?>
            <tr style="<?=$others_display?>"> 
                        <td> 
                            <input type="hidden" value="<?php echo $result_agree_info->agree_cost_center; ?>" name="other_scc_cost_center" >
                            <?php echo $result_agree_info->agree_cost_center; ?> 
                        </td>

                        <td>      
                           
                        </td>

                        <td>  
                            <input type="hidden" value="<?php echo $provision_gl[0]->gl_account_no; ?>" name="other_scc_prov_gl" >
                            <?php echo $provision_gl[0]->gl_account_no; ?>  
                        </td>

                        <td> Expense Payable -Rent Office  </td>
                      
                        <td style="text-align:right;">  
                                <?php
                                $accrual_amt=0;
                              
                                $accrual_amt = $only_others_for_stop_cost ;
                                
                                    echo number_format($accrual_amt,2);
                                    $total_dr = $total_dr + $accrual_amt;
                                ?>
                            <input type="hidden" value="<?php echo $accrual_amt; ?>" name="other_scc_data_dr_amount" >
                        </td>
                        <td> </td>
                        <td> 
                            <?php 
                            
                            $narration_1 = " Rent of $other_narr_str at $result_agree_info->location_name for $mysqldate";
                               echo $narration_1 ;
                            ?>
                            <input type="hidden" value="<?php echo $narration_1; ?>" name="other_scc_data_narr" >
                        </td>

            </tr>
     
<!-- vat for stop_payment 24 july 2018--> 
<?php if($single_paid_data->sche_payment_sts=='stop_payment' || $single_paid_data->sche_payment_sts=='stop_payment_pm'){ 
            if($vat_applicable=='no'){ $vat_display='display:none';}else{$vat_display='';}  ?>
            
        <tr style="<?=$vat_display?>"> 
                        <td> 
                            <input type="hidden" value="<?php echo $vat_applicable; ?>" name="scc_cost_center_vat_sts" >
                            <input type="hidden" value="<?php echo $result_agree_info->agree_cost_center; ?>" name="scc_cost_center_acc_vat" >
                            <?php echo $result_agree_info->agree_cost_center; ?> 
                        </td>

                        <td>      
                            <input type="hidden" value="" name="scc_data_mis_acc_vat" >
                            <?php echo ''; ?>  
                        </td>

                        <td>  
                            <input type="hidden" value="<?php echo $provision_gl[0]->gl_account_no; ?>" name="scc_prov_gl_acc_vat" >
                            <?php echo $provision_gl[0]->gl_account_no; ?>  
                        </td>

                        <td> Expense Payable -Rent Office  </td>
                      
                        <td style="text-align:right;">  
                                <?php
                                
                                    $accrual_amt=$vat_for_accrual_pm;
                                    if($vat_applicable=='no'){ $accrual_amt=0;}else{} 
                                    echo number_format($accrual_amt,2);
                                    $total_dr = $total_dr + $accrual_amt;
                                ?>
                            <input type="hidden" value="<?php echo $accrual_amt; ?>" name="scc_data_dr_amount_acc_vat" >
                        </td>
                        <td> </td>
                        <td> 
                            <?php 
                            
                            $narration_1 = " Rent of $narr_str  at $result_agree_info->location_name for $mysqldate VAT";
                                echo $narration_1 ;
                            ?>
                            <input type="hidden" value="<?php echo $narration_1; ?>" name="scc_data_narr_acc_vat" >
                            
                        </td>

            </tr>
<?php } ?>



 <!-- Advance for per landlord  -->
    <?php $landlords_count = count($landlords_result); ?>
            <input type="hidden" value="<?php echo $landlords_count; ?>" name="landlords_count" id="landlords_count" >
        <?php
        $i = 1;
        $display='';
        if($non_prov_paid_data->tot_adjustment_adv < 1){ $display='display:none'; }
        foreach ($landlords_result as $landlord_row) {
            ?>

                    <tr style="<?=$display;?>"> 
                        <td> 
                            <input type="hidden" value="<?php echo $location_type_data[0]->cost_center_code; ?>" name="landlord_adv_cost_center<?php echo $i; ?>" >
                            <input type="hidden" value="<?php echo $landlord_row->vendor_id; ?>" name="single_landlord_id<?php echo $i; ?>" >
                            <?php echo $location_type_data[0]->cost_center_code; ?>  
                        </td>
                        <td>   </td>
                        <td> 
                            <input type="hidden" value="<?php echo $advance_gl[0]->gl_account_no; ?>" name="landlord_adv_rent_gl<?php echo $i; ?>" >
                            <?php echo $advance_gl[0]->gl_account_no; ?>  
                        </td>
                        <td> Advance Rent  </td>
                        <td> </td>  

                        <td style="text-align:right;"> 
                            <?php
                            $landlord_percent = $landlord_row->adv_amount_percent;
                            $advance_adj_amount = $non_prov_paid_data->tot_adjustment_adv ;
                            $landlord_adj_amount = $advance_adj_amount * ($landlord_percent / 100);
                            // $location_type_vat_amount = $location_type_dr_amount * ($vat_percent/100) ;
                            echo number_format($landlord_adj_amount,2);
                            $total_cr = $total_cr + $landlord_adj_amount;
                            ?>
                            <input type="hidden" value="<?php echo $landlord_adj_amount; ?>" name="landlord_adv_adj_amount_cr<?php echo $i; ?>" >
                        </td>
                        <td> 
                            <?php 
                            
                                $mysqldate = date( 'M Y ', $phpdate );
                                //$narration_1 = "  Adjustment of advance rent of  $landlord_row->ll_name for $mysqldate";
                                $narration_1 = "  Adjustment of advance rent of  $result_agree_info->location_name for $mysqldate";
                                echo $narration_1 ;
                            ?>

                            <input type="hidden" value="<?php echo $landlord_row->ll_name; ?>" name="landlord_adv_ll_name<?php echo $i; ?>" >
                        </td>
                        </td>

                    </tr>


    <?php $i++;
} ?>
     

<!--  vat for accrual following month         -->   
<?php 
        $i=1;
        $vat_display='';
        if($vat_applicable=='no'){ $vat_display='display:none'; }
        if($single_paid_data->sche_payment_sts=='stop_payment_pm' || $single_paid_data->sche_payment_sts=='stop_payment'){ ?>
            
            

                <tr style="<?=$vat_display?>"> 
                    <td> 
                        <input type="hidden" value="<?php echo $result_agree_info->agree_cost_center; ?>" name="pm_location_vat_cost_center_cr" >
                        <?php echo $result_agree_info->agree_cost_center;
                        ?>  
                    </td>
                    <td>  </td>
                    <td>  
                        <input type="hidden" value="<?php echo $vat_gl[0]->gl_account_no; ?>" name="pm_location_vat_rent_gl_cr" >
                        <?php echo $vat_gl[0]->gl_account_no; ?>
                    </td>
                    <td> Vat On Rent </td>
                    <td> </td> 

                    <td style="text-align:right;"> 
                         <?php 

                        $vat_cr_amount = $vat_for_accrual_pm ;
                        if($vat_applicable=='no'){ $vat_cr_amount=0;  $total_vat=0;}
                        echo number_format($vat_cr_amount,2); 
                        if($vat_applicable!='no'){
                            $total_cr = $total_cr + $vat_cr_amount;
                        }

                        ?>
                        <input type="hidden" value="<?php echo $vat_cr_amount; ?>" name="pm_location_vat_cr_amount" >
                        <input type="hidden" value="<?php echo $vat_percentage->vat_percentage; ?>" name="pm_vat_percentage" >
                       
                    </td>

                <td> <?php $narration_1 = " $vat_percent % VAT from Rent of $narr_str  at $result_agree_info->location_name for $mysqldate";
                                echo $narration_1; 
                         ?>
                    <input type="hidden" value="<?php echo $narration_1; ?>" name="pm_vat_narration" >     
                </td>
                </tr>

        <?php $i++;  
    
    } $vat_display='';   ?>

        

<!-- location wise vat cr -->

        <?php 
        $i=1;
        $vat_display='';
        if($vat_applicable=='no'){ $vat_display='display:none'; }
        if($single_paid_data->sche_payment_sts!='stop_payment' && $single_paid_data->sche_payment_sts!='stop_payment_pm'){  
            foreach ($location_type_data as $location_type_data_row) { ?>

                <tr style="<?=$vat_display?>"> 
                    <td> 
                        <input type="hidden" value="<?php echo $result_agree_info->agree_cost_center; ?>" name="location_vat_cost_center_cr<?php echo $i; ?>" >
                        <?php echo $result_agree_info->agree_cost_center;
                        //$vat_gl[0]->gl_branch_code; 
                        ?>  
                    </td>
                    <td>  </td>
                    <td>  
                        <input type="hidden" value="<?php echo $vat_gl[0]->gl_account_no; ?>" name="location_vat_rent_gl_cr<?php echo $i; ?>" >
                        <?php echo $vat_gl[0]->gl_account_no; ?>
                    </td>
                    <td> Vat On Rent </td>
                    <td> </td> 

                    <td style="text-align:right;"> 
                         <?php 

                        $location_type_percent = $location_type_data_row->cost_in_percent;

                        $vat_dr_amount = $total_vat * ($location_type_percent / 100);
                        if($vat_applicable=='no'){ $vat_dr_amount=0;  $total_vat=0;}
                        echo number_format($vat_dr_amount,2); 
                        if($vat_applicable!='no'){
                            $total_cr = $total_cr + $vat_dr_amount;
                        }

                        ?>
                        <input type="hidden" value="<?php echo $vat_dr_amount; ?>" name="location_vat_cr_amount<?php echo $i; ?>" >
                        <input type="hidden" value="<?php echo $vat_percentage->vat_percentage; ?>" name="vat_percentage<?php echo $i; ?>" >
                       
                    </td>


                    <td> <?php echo $vat_percentage->vat_percentage; ?>pc Vat on Unpaid Rent for <?=$mysqldate?></td>

                </tr>

        <?php $i++;  } 

    }    ?>

 

                <?php
                $i = 1;
                $display='';
                if($result_agree_info->tax_wived=='wived_yes'){ $display='display:none'; }
                foreach ($landlords_result as $landlord_row) {
                    ?>

                    <tr style="<?=$display;?>"> 
                        <td>
                            <input type="hidden" value="<?php echo $location_type_data[0]->cost_center_code; ?>" name="landlord_tax_cost_center<?php echo $i; ?>" >
                            <input type="hidden" value="<?php echo $landlord_row->vendor_id; ?>" name="single_landlord_id_tax<?php echo $i; ?>" >
                            <?php echo $location_type_data[0]->cost_center_code; ?> 
                        </td>
                        <td>   </td>
                        <td>
                            <input type="hidden" value="<?php echo $tax_gl[0]->gl_account_no; ?>" name="landlord_tax_account_gl<?php echo $i; ?>" >
                            <?php echo $tax_gl[0]->gl_account_no; ?> 
                        </td>
                        <td> Tax  
                            <input type="hidden" value="<?php echo $landlord_row->ll_name; ?>" name="landlord_tax_ll_name<?php echo $i; ?>" >
                        </td>
                        <td> </td>  

                        <td style="text-align:right;"> 
                            <?php
                            $landlord_percent = $landlord_row->credit_amount_percent;
                            $adv_landlord_percent = $landlord_row->adv_amount_percent;
                            $advance_adj_amount = $non_prov_paid_data->tot_adjustment_adv;
                            //$monthly_rent_with_others_and_arear = $non_prov_paid_data->tot_monthly_rent + $non_prov_paid_data->tot_others_amount + $non_prov_paid_data->total_others_amount + $non_prov_paid_data->tot_area_amount ;
                            $monthly_rent_with_others_and_arear = $non_prov_paid_data->tot_monthly_rent + $non_prov_paid_data->tot_others_amount  + $non_prov_paid_data->tot_area_amount ;
                            // $landlord_adj_amount = $advance_adj_amount * ($landlord_percent/100) ;
                            $tax_applicable_amt= $monthly_rent_with_others_and_arear - $others_no_tax_amount->tax_not_apply;
                            $landlord_adj_amount = $tax_applicable_amt * ($landlord_percent / 100);
                            $landlord_tax_amount = $landlord_adj_amount * ($tax_rate / 100);

                            $landlord_adj_amount_adv = $advance_adj_amount * ($landlord_percent / 100);
                            $landlord_tax_amount_for_adv = $landlord_adj_amount_adv * ($tax_rate / 100);
                        
                            $tax_cal = $landlord_tax_amount - $landlord_tax_amount_for_adv; // off in 3 dec 2017
                      
                            echo number_format($landlord_tax_amount,2);
                            if($result_agree_info->tax_wived=='wived_yes'){ $total_cr = $total_cr;}
                            else{ $total_cr = $total_cr + $landlord_tax_amount; }
                            
                            ?>
                            <input type="hidden" value="<?php echo $landlord_tax_amount; ?>" name="landlord_tax_amount<?php echo $i; ?>" >
                        </td>
                        <td> 

                            <input type="hidden" value="<?php echo $tax_rate; ?>" name="landlord_tax_amount_percent<?php echo $i; ?>" >
                            <?php 
                           
                            $mysqldate = date( 'M Y ', $phpdate );
                            $tax_rate = number_format($tax_rate,2);
                            $narration_1 = " $tax_rate pc Tax from $landlord_row->ll_name from rent of $narr_str at $result_agree_info->location_name for $mysqldate";
                            echo $narration_1 ;

                            ?>
       
                        </td>

                    </tr>


    <?php $i++;
} ?>

<?php if ($single_paid_data->sd_adjust_amt > 0) { ?>

<!-- sd vat =0 -->
<input type="hidden" value="0" name="bank_sd_cr_vat_amount" >

<!-- sd tax=0  -->
<input type="hidden" value="0" name="bank_sd_cr_tax_amount" >


<!-- sd rest amount -->

            <tr> 
                        <td> 
                            <input type="hidden" value="<?php echo $location_type_data[0]->cost_center_code; ?>" name="bank_cost_center_cr" >
                            <?php echo $location_type_data[0]->cost_center_code; ?>  
                        </td>
                        <td>  </td>
                        <td>  
                            <input type="hidden" value="<?php echo $sd_gl[0]->gl_account_no; ?>" name="bank_sd_gl_cr" >
                    <?php echo $sd_gl[0]->gl_account_no; ?>
                        </td>
                        <td> S.D A/C </td>
                        <td> </td> 


                        <td style="text-align:right;"> 
                            <?php 

                            //$final_sd_amount = $single_paid_data->sd_adjust_amt - $sd_tax_amount - $sd_vat_amount;
                            $final_sd_amount = $single_paid_data->sd_adjust_amt;
                            echo number_format($final_sd_amount,2); 
                            $total_cr = $total_cr + $final_sd_amount;


                            ?>
                            <input type="hidden" value="<?php echo $final_sd_amount; ?>" name="bank_sd_cr_amount" >
   
                        </td>


                        <td> 

                            <?php 
                            
                                $mysqldate = date( 'M Y ', $phpdate );
                                $narration_1 = "  Adj of Secu Deposit rent for $mysqldate";
                                echo $narration_1 ;
                            ?>


                        </td>

                    </tr>


    <?php } ?>


            
            <?php $agree_row_count = $i - 1; ?>   
            <input id="agree_row_count" class="" type="hidden" value="<?php echo $agree_row_count; ?>">


            <?php $i = 1;
            $landlords_count = count($landlords_result);
            $display='';
            // only for rent in adv in schedule
            if($single_paid_data->sche_payment_sts=='advance_rent_payment'){ $display='display:none'; }
            foreach ($landlords_result as $landlord_row) {
                    $single_ll_id = $landlord_row->vendor_id;
        ?>

                            <tr style="<?=$display;?>"> 
                                <td>
                                    <?php echo 
                                   // $location_type_data[0]->cost_center_code;
                                    $landlord_row->branch_code; ?>
                                    <input type="hidden" value="<?php echo $landlord_row->branch_code; ?>" name="loc_cost_center_code_tot_cr<?php echo $i; ?>" >
                                    <input type="hidden" value="<?php echo $landlord_row->vendor_id; ?>" name="single_landlord_id_tot_cr<?php echo $i; ?>" >
                                </td>
                                <td>
                                
                                 </td>
                                <td>
                                    <?php echo $landlord_row->landlord_acc_no; ?> 
                                     <input type="hidden" value="<?php echo $landlord_row->landlord_acc_no; ?>" name="per_landlord_acc_no_tot_cr<?php echo $i; ?>" >
                                    <input type="hidden" value="<?php echo $landlord_row->landlord_payment_mode; ?>" name="per_landlord_payment_mode_tot_cr<?php echo $i; ?>" >
                                </td>
                                <td > <?php 

                                        if($landlord_row->landlord_payment_mode=='Pay Order'){
                                            echo $landlord_row->name.'- Payorder Suspense A/c ';
                                        }else{
                                            echo $landlord_row->name;
                                        } 
                                    ?>  
                                        <input type="hidden" value="<?php echo $landlord_row->name; ?>" name="per_landlord_name<?php echo $i; ?>" >
                                </td>
                                <td> </td>

                                <td style="text-align:right;"> 

            <?php


 if($prov_result!=''){

        }else{

            $prov_tax_amount = 0;
            $new_cal_amount = 0 - $prov_tax_amount; 
            $prov_vat_amount = 0 ;
            $final_cal_amount = $new_cal_amount - $prov_vat_amount;
            $prov_sd = 0;

        }
          
            $landlord_percent = $landlord_row->credit_amount_percent;
            $location_type_dr_amount = $final_cal_amount * ($landlord_percent / 100);

// 6 sep

            $monthly_rent_with_others_and_arear = $non_prov_paid_data->tot_monthly_rent + $non_prov_paid_data->tot_others_amount + $non_prov_paid_data->tot_area_amount ;
            $adv_landlord_percent = $landlord_row->adv_amount_percent;
            $advance_adj_amount = $non_prov_paid_data->tot_adjustment_adv;
            $landlord_rent_amt = $monthly_rent_with_others_and_arear * ($landlord_percent / 100);
            $landlord_adj_amount = $advance_adj_amount * ($adv_landlord_percent / 100);
            
            $landlord_tax_amount_for_adv = $landlord_adj_amount * ($tax_rate/ 100);
            $tax_applicable_amt= $monthly_rent_with_others_and_arear - $others_no_tax_amount->tax_not_apply;
            $tax_amt = $tax_applicable_amt * ($landlord_percent / 100);
            $landlord_tax_amount = $tax_amt * ($tax_rate / 100);
            
            //$tax_cal = $landlord_tax_amount - $landlord_tax_amount_for_adv;
            $tax_cal = $landlord_tax_amount ;

            //sd
            $total_sd_adjust_amt = $single_paid_data->sd_adjust_amt - $prov_sd;
            $landlord_sd_adjust_amt = $total_sd_adjust_amt * ($landlord_percent / 100);

            $final_amount = $landlord_rent_amt - $landlord_adj_amount - $tax_cal - $landlord_sd_adjust_amt; 
            if($single_paid_data->sche_payment_sts=='stop_cost_center' || $single_paid_data->sche_payment_sts=='stop_cost_center_pm')
                { $final_amount = $landlord_rent_amt; }
            if($result_agree_info->tax_wived=='wived_yes'){ 
                $final_amount = $landlord_rent_amt - $landlord_adj_amount - $landlord_sd_adjust_amt; 
            }
            $ll_final_amount =  $final_amount;


            echo number_format($ll_final_amount,2);
                if($single_paid_data->sche_payment_sts!='advance_rent_payment'){
                     $total_cr = $total_cr + $ll_final_amount;
                }
           
            ?>
                                   <input type="hidden" value="<?php echo $ll_final_amount; ?>" name="per_landlord_final_amt_tot_cr<?php echo $i; ?>" >

                                </td>
                                <td> 

                                <?php 
                                //$others_loc_names;

                                    $mysqldate = date( 'M Y ', $phpdate );
                                    $with='with';
                                   
                                    //$narration_1 = " Credited rent for $mysqldate  $with  $narr_str";
                                    if($landlord_row->landlord_payment_mode=='Pay Order'){
                                        
                                        $narration_1 = "PO favouring  $landlord_row->ll_name  ";
                                    }else{
                                        //$narration_1 = " Credited rent for $mysqldate  $with  $narr_str"; 
                                        //changed in 9 oct 2018
                                        $narration_1 = " Credited rent of $result_agree_info->location_name for $mysqldate $with  $narr_str";   
                                    }
                                    echo $narration_1 ;
                                ?>

                                     <input type="hidden" value="<?php echo $landlord_row->ll_name; ?>" name="per_landlord_amt_name_tot_cr<?php echo $i; ?>" >
                                </td>
                            </tr> 

        <?php
            $i++;
         } ?>

<?php 


}    //  for stop_unpaid_payment

?>

    <tr style="background:skyblue;">
        <td colspan="4" style="text-align:center"> <b>Total</b> </td>
        
        <td style="text-align:right;"><b><?php echo number_format($total_dr,2); ?></b> </td>
        <td style="text-align:right;"><b> <?php echo number_format($total_cr,2); ?></b> </td>
        <td> </td>
    </tr>

</table>
            <!-- 4 sep end-->


            <div style="" id="sd_info_table">

            </div>

            <!-- Land Lords -->

                    <?php $button_name=''; 
                        if($verify_type!='view'){ ?>
                         <?php if($add_edit=='fin'){ $button_name='Verify';}else{ $button_name='Approve'; }  ?>

                             <div class="" style="text-align:center;padding-top:40px;">
                                <center><input type="button" value="<?php echo $button_name; ?>" id="sendButton" class="buttonStyle" /> <span id="loading" style="display:none">Please wait... <img src="<?= base_url() ?>images/loader.gif" align="bottom"></span> <center>
                                    <br />
                            </div>
                    <?php } ?>

                        </form>


                        </div>

                       

                        <div id="cdrMessageDialogContent"  style="display:none">
                            <div class="hd"><h2 class="conf">Bill Successfully Created.<br>
                                    </div>
                                    <div class="bd">
                                        <div class="inlineError" id="cdrMessageErrorMsg" style="display:none"></div>
                                    </div>
                                    <a class="btn-small btn-small-normal" id="cdrMessageDialogConfirm"><span>Ok</span></a> 
                                  <!--   <a class="btn-small btn-small-secondary" id="cdrMessageDialogCancel"><span>Cancel</span></a>  -->
                                    <span id="loading" style="display:none">Please Wait... <img src="<?= base_url() ?>images/loader.gif" align="bottom"></span>
                            </div>