
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
         #location_type_table, #location_type_table tr,#location_type_table td{
            border: 1px solid black;
        }
        #others_type_table, #others_type_table tr,#others_type_table td{
            border: 1px solid black;
        }
        #cost_center_table th, #location_type_table th, #others_type_table th{
            border-bottom: 1px solid black !important;
            border-top: 1px solid black;
            border-left: 1px solid black;
            border-right: 1px solid black;
        }
        .custom_css{width:100%;font-size: 15px; padding-top: 5px; font-weight: bold; }
        .incr_input{width:40px !important;}
        .others_input_style{width:60px !important;}
/*.browse_btn{
   background: url("<?=base_url()?>images/arrow_up.png") no-repeat 0 0;
  
   }*/
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
        .basket div
        {
            position: relative;
        }
        .nextButton
        {
            float: right;
            margin-left: 0px;
            /*background: #3e94c4;*/
        }
        .schedule_btn
        {
          
            margin-left: 0px;
        }
        .backButton
        {
            float: left;
            margin-left: 10px;
        }
        #basketButtonsWrapper
        {
            float: right;
            margin-top: 30px;
            margin-right: 10px;
            width: 115px;
        } 
        #basketButtonsWrapper_rent
        {
            float: right;
            margin-top: 30px;
            margin-right: 10px;
            width: 115px;
        }
        #selectedProductsHeader
        {
            margin-left: 20px;
            float: left;
            width: 200px;
        }
        #selectedProductsButtonsWrapper
        {
            float: right;
            margin-right: 10px;
            width: 115px;
            margin-top: 160px;
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
    <script type="text/javascript" src="<?= base_url() ?>js/jquery.ajaxupload.js"></script>
    <script>

        var allcodes = <? if (empty($code)) {
    echo '';
} else {
    echo json_encode($code);
} ?>;
        jQuery(function() {

            jQuery("#agree_sign_dt").datepicker();
            jQuery("#agree_sign_dt").datepicker("option", "dateFormat", "dd-mm-yy");
            <?php if (isset($agreement->agree_sign_dt)) { ?>
                            jQuery("#agree_sign_dt").datepicker("setDate", new Date("<?php echo isset($agreement->agree_sign_dt) ? date('m/d/Y', strtotime($agreement->agree_sign_dt)) : ''; ?>"));
            <?php } ?>

                        jQuery("#rent_start_dt").datepicker();
                        jQuery("#rent_start_dt").datepicker("option", "dateFormat", "dd/mm/yy");
            <?php if (isset($agreement->rent_start_dt)) { ?>
                            jQuery("#rent_start_dt").datepicker("setDate", new Date("<?php echo isset($agreement->rent_start_dt) ? date('m/d/Y', strtotime($agreement->rent_start_dt)) : ''; ?>"));
            <?php } ?>
            // dd/mm/yy || dd-mm-yy
                        jQuery("#agree_exp_dt").datepicker();

                        jQuery("#agree_exp_dt").datepicker("option", "dateFormat", "dd/mm/yy");
            <?php if (isset($agreement->agree_exp_dt)) { ?>
                            jQuery("#agree_exp_dt").datepicker("setDate", new Date("<?php echo isset($agreement->agree_exp_dt) ? date('m/d/Y', strtotime($agreement->agree_exp_dt)) : ''; ?>"));
            <?php } ?>

                        jQuery("#increment_start_dt_value").datepicker();
                        jQuery("#increment_start_dt_value").datepicker("option", "dateFormat", "dd/mm/yy");
            <?php if (isset($agreement->increment_date)) { ?>
                            jQuery("#increment_start_dt_value").datepicker("setDate", new Date("<?php echo isset($agreement->increment_start_dt_value) ? date('m/d/Y', strtotime($agreement->increment_start_dt_value)) : ''; ?>"));
            <?php } ?>

                        jQuery("#point_of_payment").datepicker();
                        jQuery("#point_of_payment").datepicker("option", "dateFormat", "dd/mm/yy");
            <?php if (isset($agreement->increment_date)) { ?>
                            jQuery("#point_of_payment").datepicker("setDate", new Date("<?php echo isset($agreement->point_of_payment) ? date('m/d/Y', strtotime($agreement->point_of_payment)) : ''; ?>"));
            <?php } ?>
        });
    </script>
    <script>
        jQuery(document).ready(function() {
            jQuery('#percentage_basis_adj').hide();
            jQuery('#year_basis_adj').hide();
            jQuery('#percent_amt_tr').hide();
            jQuery('#calculated_percent_amt_tr').hide();
            jQuery("#yearly_adj_type_tr").hide();
            jQuery("#fixed_amt_tr").hide();
            jQuery("#month_no_tr").hide();
            jQuery("#branch_id").hide();
            jQuery("#atm_id").hide();
            jQuery("#sme_id").hide();
            jQuery("#godown_id").hide();
            jQuery("#dept_id").hide();
         
             addmore_percent();
            //addmore_location_type_percent();
             //location_type_option_chk(1);
             //others_location_type_option_chk(1);
             landlords_option_chk(1);
             jQuery("#at1").hide(); 
             // increment data   
            jQuery("#one_time_increment_tr").hide();
            jQuery("#every_yr_increment_tr").hide();


            jQuery('#nextButtonInfo').jqxButton({width: 50});
            jQuery('#nextButtonBasket').jqxButton({width: 50});
            jQuery('#backButtonBasket').jqxButton({width: 50});
            jQuery('#increment_reset').jqxButton({width: 50});
            jQuery('#nextButtonBasket_rent').jqxButton({width: 50});
            jQuery('#backButtonBasket_rent').jqxButton({width: 50});
             jQuery('#nextButtonBasket_adj').jqxButton({width: 50});
            jQuery('#backButtonBasket_adj').jqxButton({width: 50});
            jQuery('#backButtonReview').jqxButton({width: 50});
            jQuery('#schedule_btn').jqxButton({width: 150});

           // jQuery(".browse_btn").hide();
            // Create jqxTabs.
            jQuery('#jqxTabs').jqxTabs({width: '99%', height: '100%', position: 'top'});
            // jQuery('#settings div').css('margin-top', '10px');
            jQuery('#animation').jqxCheckBox({theme: theme});
            jQuery('#contentAnimation').jqxCheckBox({theme: theme});

            jQuery('#animation').on('change', function(event) {
                var checked = event.args.checked;
                jQuery('#jqxTabs').jqxTabs({selectionTracker: checked});
            });


            jQuery('#contentAnimation').on('change', function(event) {
                var checked = event.args.checked;
                if (checked) {
                    jQuery('#jqxTabs').jqxTabs({animationType: 'fade'});
                }
                else {
                    jQuery('#jqxTabs').jqxTabs({animationType: 'none'});
                }
            });
      
            jQuery(".rent_docv").click(function() {

                if (jQuery(this).prop('checked')) {
                    // alert(jQuery(this).val());
                    jQuery(this).closest('td').next('td').find('.browse_btn').show();
                    jQuery("#file_check").val('0');


                } else {
                    jQuery(this).closest('td').next('td').find('.browse_btn').hide();
                   
                }

            });


            jQuery("#increment_reset").click(function() {
                increment_ajax(); 
            
            });


            jQuery("#form").ajaxForm(options);
            // validate form.
            jQuery("#sendButton").click(function() {

                if(form_validate()){
                    call_ajax_submit();
                }
                
            });

            var cost_centerlist = [<? $i = 1;
                foreach ($cost_center as $value) {
                    if ($i != 1) {
                        echo ',';
                    } echo '{value:"'.$value->code.'", label:"' . $value->name . '"}';
                    $i++;
                } ?>];
            var vendor_list = [<? $i = 1;
                foreach ($vendor_list as $value) {
                    if ($i != 1) {
                        echo ',';
                    } echo '{value:"' . $value->vendor_id . '", label:"' . $value->name . '"}';
                    $i++;
                } ?>];
            var credit_sts = [{value: "yes", label: "Yes"}, {value: "no", label: "No"}];
            var vat_sts = [{value: "yes", label: "Yes"}, {value: "no", label: "No"}];
            var tax_sts = [{value: "yes", label: "Yes"}, {value: "no", label: "No"}];
            var loc_vat_sts = [{value: "yes", label: "Yes"}, {value: "no", label: "No"}];
            var loc_tax_sts = [{value: "yes", label: "Yes"}, {value: "no", label: "No"}];
              // var location_type = [{value: "branch", label: "Branch"}, 
              // {value: "atm", label: "ATM"},
              // {value: "sme", label: "SME"},
              // {value: "godown", label: "Godown"},
              // {value: "others", label: "Others"},
              // ];

              var location_type = [<? $i = 1;
                foreach ($location_type_list as $value) {
                    if ($i != 1) {
                        echo ',';
                    } echo '{value:"' . $value->id . '", label:"' . $value->name . '"}';
                    $i++;
                } ?>];
   

            var miscodelist = [];
            jQuery("#cost_center1").jqxComboBox({source: cost_centerlist, placeHolder: "Select Cost Center", width: '180', height: '20'});
            jQuery('#cost_center1').keyup(function(){
                commbobox_check(jQuery(this).attr('id'));
            });
            jQuery("#cost_center1").val('999');
            jQuery("#mis_code1").jqxComboBox({source: miscodelist, placeHolder: "Select MIS Code", width: '180', height: '20'});
            jQuery("#others_mis_code1").jqxComboBox({source: miscodelist, placeHolder: "Select MIS Code", width: '150', height: '20'});
            jQuery("#vendor_id1").jqxComboBox({source: vendor_list, placeHolder: "Select Land Lord", width: '280', height: '20'});
            jQuery("#credit_sts1").jqxComboBox({source: credit_sts, placeHolder: "Select Credit Status", width: '180', height: '20'});
            jQuery("#location_type1").jqxComboBox({source: location_type, placeHolder: "Select Location Type", width: '180', height: '20'});
            
            jQuery("#vat_sts1").jqxComboBox({source: vat_sts, placeHolder: "Select Vat Status", width: '100', height: '20'});
            jQuery("#vat_sts1").jqxComboBox('selectItem', 'yes');
            jQuery("#tax_sts1").jqxComboBox({source: tax_sts, placeHolder: "Select Tax Status", width: '100', height: '20'});
            jQuery("#tax_sts1").jqxComboBox('selectItem', 'yes');

            jQuery("#loc_vat_sts1").jqxComboBox({source: loc_vat_sts, placeHolder: "Select Vat Status", width: '100', height: '20'});
            jQuery("#loc_vat_sts1").jqxComboBox('selectItem', 'yes');
            jQuery("#loc_tax_sts1").jqxComboBox({source: loc_tax_sts, placeHolder: "Select Tax Status", width: '100', height: '20'});
            jQuery("#loc_tax_sts1").jqxComboBox('selectItem', 'yes');

            jQuery("#credit_sts1").jqxComboBox('selectItem', 'yes');
            
             jQuery('#mis_code1').keyup(function(){
                        commbobox_check(jQuery(this).attr('id'));
                    });
             jQuery('#others_mis_code1').keyup(function(){
                        commbobox_check(jQuery(this).attr('id'));
                    });
             jQuery('#vendor_id1').keyup(function(){
                        commbobox_check(jQuery(this).attr('id'));
                    });
             jQuery('#credit_sts1').keyup(function(){
                        commbobox_check(jQuery(this).attr('id'));
                    });
             jQuery('#vat_sts1').keyup(function(){
                        commbobox_check(jQuery(this).attr('id'));
                    }); 
             jQuery('#tax_sts1').keyup(function(){
                        commbobox_check(jQuery(this).attr('id'));
                    });
             jQuery('#location_type1').keyup(function(){
                        commbobox_check(jQuery(this).attr('id'));
                    });

            cost_center_change('cost_center1', 1);
            cost_center_select();
            jQuery('.year_basis').hide();
            jQuery('.monthly_rent_based').hide();
            jQuery('.rent_wise_change_vat').hide();
            jQuery('.rent_wise_change').hide();
            jQuery('.adj_year_basis').hide();

            jQuery("#agree_exp_dtm").on("change", function() {
                var exp = jQuery(this).val();
                var start = jQuery('#rent_start_dt').val();
                var monthly_rent = jQuery('#monthly_rent').val();
                //alert(start);
                var postdata = {
                    'start': start, 'exp': exp, 'monthly_rent': monthly_rent
                };
                jQuery.ajax({
                    type: "POST",
                    // async:false,
                    cache: false,
                    url: "<?= base_url() ?>index.php/agreement/increment_sch",
                    data: postdata,
                    datatype: "html",  
                    success: function(response) {
                        jQuery("#show_data").show();
                        if (response != "") {

                            jQuery("#data_table").html(response).show();
                            jQuery(".incr_per_val").prop('disabled', true);
                        }
                    }

                });
            });
            jQuery("#percentage_basis_adj").change(function() {

                if (jQuery('#percentage_basis_adj :selected').val() != '') {
                    jQuery('#percent_amt_tr').show();
                    jQuery('#calculated_percent_amt_tr').show();

                     if(jQuery('#percentage_basis_adj :selected').val() == 'percent_paid_amt'){
                        jQuery("#percent_amt").val('');
                        jQuery("#calculated_percent_amt").val('');
                    }

                } else {
                    jQuery('#percent_amt_tr').hide();
                    jQuery('#calculated_percent_amt_tr').hide();
                   
                }

            });

            jQuery("#percent_amt").on("blur",function(){

                if ( jQuery('#total_advance').val()=='' || jQuery('#total_advance').val()==0 ){
                    alert('Total Advance Amount is Required');
                    jQuery('#percent_amt').val('');
                    return false;
                }

                if (jQuery('#percentage_basis_adj :selected').val() == 'percent_total_amt') {

                  var percent_amt_for_tot = jQuery("#percent_amt").val();
                  var monthly_rent_for_tot = jQuery("#monthly_rent").val();
                  var total_advance_for_tot = jQuery("#total_advance").val();
                  var calculated_monthly_adjust_for_tot = total_advance_for_tot * (percent_amt_for_tot)/100;
                  jQuery('#calculated_percent_amt').val(parseFloat(calculated_monthly_adjust_for_tot));

                }else if(jQuery('#percentage_basis_adj :selected').val() == 'percent_paid_amt'){

                    //jQuery("#percent_amt").val('');
                }

            });

// increment options change
        jQuery("#increment_type").change(function() {
              
                if (jQuery('#increment_type :selected').val() == '1') {
                     
                     jQuery('#one_time_increment_tr').hide();
                     jQuery('#every_yr_increment_tr').hide();
                     jQuery('#increment_start_tr').hide();
                     jQuery("#increment_tbl").hide();
                     jQuery('#incr_start_dt').val('');
                     var count_year = jQuery("#count_year").val();
                       
                     for (var i = 0; i <= count_year; i++) {

                            jQuery("#increment_tr"+i).hide();                   
                     }
                     jQuery("#incr_type").val(1);

                }
                else if (jQuery('#increment_type :selected').val() == '2') {
                        
                    jQuery("#increment_tbl").hide();
                    jQuery('#every_yr_increment_tr').show();
                    jQuery('#one_time_increment_tr').hide();
                    jQuery('#increment_start_tr').hide();
                    jQuery('#increment_every_yr_value').val('');
                    jQuery('#incr_start_dt').val('');
                    jQuery("#incr_type").val(2);

                }
                else if (jQuery('#increment_type :selected').val() == '3') {

                    jQuery("#increment_tbl").hide();
                    jQuery('#every_yr_increment_tr').hide();
                    jQuery('#increment_start_tr').hide();
                    jQuery('#one_time_increment_tr').show();
                    jQuery('#one_time_increment_yr_no').val('');
                    jQuery('#incr_start_dt').val('');
                    jQuery("#incr_type").val(3);
                    
                }
                else if (jQuery('#increment_type :selected').val() == '4') {
                    
                     jQuery('#one_time_increment_tr').hide();
                     jQuery('#every_yr_increment_tr').hide();
                     jQuery('#increment_start_tr').hide();
                     jQuery('#incr_start_dt').val('');
                    // show all rows
                    jQuery("#increment_tbl").show();
                     var count_year = jQuery("#count_year").val();
                       
                     for (var i = 0; i <= count_year; i++) {

                            jQuery("#increment_tr"+i).show();                   
                     }
                     jQuery("#incr_type").val(4);

                    

                } else if (jQuery('#increment_type :selected').val() == '5') {

                     jQuery("#increment_tbl").hide();
                     
                     jQuery('#every_yr_increment_tr').hide();
                     jQuery('#one_time_increment_tr').hide();
                     jQuery("#increment_start_tr").show();
                     jQuery("#incr_type").val(5);

                }else {
                     jQuery("#increment_tbl").hide();
                     jQuery("#increment_start_tr").hide();
                     jQuery('#every_yr_increment_tr').hide();
                     jQuery('#one_time_increment_tr').hide();
                     jQuery('#incr_start_dt').val('');

                }
        });


        jQuery("#one_time_increment_yr_no").on("blur",function(){ 

            var one_time_increment_yr_no_val = jQuery(this).val();
            if(one_time_increment_yr_no_val!=''){
                    
                    var one_time_increment_yr_no = jQuery(this).val(); 
                    var count_year = jQuery("#count_year").val();

                  if(parseInt(count_year) >= parseInt(one_time_increment_yr_no_val)){  
                      jQuery("#increment_tbl").show();
                     for (var i = 0; i <= count_year; i++) {
                        if(i==one_time_increment_yr_no){

                            jQuery("#increment_tr"+i).show();
                        }else{

                            jQuery("#increment_tr"+i).hide();
                        }

                     }
                  }else{

                     alert('Agreenemt length exceeded');
                     jQuery("#increment_tbl").hide();
                  }

            } 
                    
        });

// every yr tr
        jQuery("#increment_every_yr_value").on("blur",function(){

            var increment_every_yr_value_val = jQuery(this).val();
            
            if(increment_every_yr_value_val!=''){
                     var count_year = jQuery("#count_year").val();
                     if(parseInt(count_year) >= parseInt(increment_every_yr_value_val)){
                            jQuery("#increment_tbl").show();
                            var increment_every_yr_value = jQuery(this).val(); 
                           
                            for (var i = 0; i <= count_year; i++) {

                                if(i==0){ jQuery("#increment_tr"+i).hide(); }
                                    else {
                                        if(i % increment_every_yr_value == 0){

                                            jQuery("#increment_tr"+i).show();
                                        }else{

                                            jQuery("#increment_tr"+i).hide();

                                        }
                                    }    
                            }
                    }else{
                           alert('This value exceeds The number of Year');
                           jQuery("#increment_tbl").hide();
                    }        
                } 
                    
        });

        jQuery("#adjust_adv_type").change(function() {

                if (jQuery('#adjust_adv_type :selected').val() != '1' && (jQuery('#total_advance').val()=='' || jQuery('#total_advance').val()==0) ){
                    alert('Total Advance Amount is Required');
                    jQuery('#adjust_adv_type').val('');
                    return false;
                }
                           
                if (jQuery('#adjust_adv_type :selected').val() == '2') {
                        
                    jQuery('#percentage_basis_adj').hide();
                    jQuery('#percent_amt_tr').hide();
                    
                    jQuery('#calculated_percent_amt_tr').hide();
                    jQuery('#year_basis_adj').hide();
                    jQuery('#yearly_adj_type_tr').hide();
                    jQuery("#yearly_adj_data_table").html('');
                    jQuery("#fixed_amt_tr").show();
                    jQuery("#fixed_amt").val('');
                    jQuery("#month_no_tr").show();
                    jQuery("#fixed_month").val('');
                }
                else if (jQuery('#adjust_adv_type :selected').val() == '3') {
                    jQuery('#percentage_basis_adj').show();
                    jQuery('#percentage_basis_adj').prop('selectedIndex',0);
                    jQuery('#percent_amt').val('');
                    jQuery("#yearly_adj_data_table").html('');

                    jQuery('#year_basis_adj').hide();
                    jQuery('#yearly_adj_type_tr').hide();
                    jQuery("#fixed_amt_tr").hide();
                    jQuery("#month_no_tr").hide();
                }
                else if (jQuery('#adjust_adv_type :selected').val() == '4') {
                    jQuery("#yearly_adj_data_table").html('');
                    jQuery('#yearly_adj_type_tr').show();
                    jQuery('#year_basis_adj').show();
                    jQuery('#percent_amt_tr').hide();
                    jQuery('#calculated_percent_amt_tr').hide();
                    jQuery('#percentage_basis_adj').hide();
                    jQuery("#fixed_amt_tr").hide();
                    jQuery("#month_no_tr").hide();
                    jQuery('#yearly_adj_type').prop('selectedIndex',0);
                    

                } else {  // no adjustment
                    jQuery('#total_advance').val(0);
                    jQuery('#percentage_basis_adj').hide();
                    jQuery('#percent_amt_tr').hide();
                    jQuery('#calculated_percent_amt_tr').hide();
                    jQuery('#year_basis_adj').hide();
                    jQuery("#yearly_adj_data_table").html('');
                    jQuery("#fixed_amt_tr").hide();
                    jQuery("#month_no_tr").hide();
                    jQuery("#yearly_adj_type_tr").hide();
                }
        });

        jQuery("#location_owner").change(function() {
                 
                if (jQuery('#location_owner option:selected').val() == 'own') {

                   jQuery('#jqxTabs').jqxTabs('disableAt', 1);
                   jQuery('#jqxTabs').jqxTabs('disableAt', 2);
                   jQuery('#jqxTabs').jqxTabs('disableAt', 3);

                    jQuery('#total_advance_tr').hide();
                    jQuery('#monthly_rent_tr').hide();
                    jQuery('#adjust_tbl').hide();
                    jQuery('#yearly_adj_data_table').hide();
                    jQuery('#form1').jqxValidator('hideHint', '#total_advance');
                    jQuery('#form1').jqxValidator('hideHint', '#adjust_adv_type');
                }
                else { 

                        jQuery('#total_advance_tr').show();
                        jQuery('#monthly_rent_tr').show();
                        jQuery('#adjust_tbl').show();
                        jQuery('#yearly_adj_data_table').show();
                }
                 
        }); 

     

        jQuery("#monthly_rent").keyup(function () {           
            jQuery.ajax({
                type: "POST",
                url: "<?=base_url()?>index.php/agreement/ajax_comma",
                data : {'monthly_rent': jQuery(this).val(), 'others_rent':jQuery('#others_rent').val()},
                success: function(response) {
                    jQuery("#Amount_Span").html(response);
                },
                error: function(xhr, textStatus, errorThrown) {
                    alert("error");
                }
            });
        }); 

        jQuery("#others_rent").keyup(function () {           
            jQuery.ajax({
                type: "POST",
                url: "<?=base_url()?>index.php/agreement/ajax_comma",
                data : {'others_rent': jQuery(this).val(), 'monthly_rent':jQuery('#monthly_rent').val()},
                success: function(response) {
                    jQuery("#Amount_Span").html(response);
                },
                error: function(xhr, textStatus, errorThrown) {
                    alert("error");
                }
            });
        });
            
        jQuery("#yearly_adj_type").change(function() {
            
                if (jQuery('#yearly_adj_type :selected').val() == 'yearly_adj_percent') {

                    var yearly_seleted_option = jQuery(this).val();
                    var exp = jQuery('#agree_exp_dt').val();
                    var start = jQuery('#rent_start_dt').val();

                    if (exp != '' && start != '') {

                        var postdata = {
                            'start': start, 'exp': exp, 'yearly_seleted_option':yearly_seleted_option
                        };
                        jQuery.ajax({
                            type: "POST",
                            // async:false,
                            cache: false,
                            url: "<?= base_url() ?>index.php/agreement/yearly_adjust_amt",
                            data: postdata,
                            datatype: "html",
                            success: function(response) {
                                jQuery("#yearly_adj_show_data").show();
                                if (response != "") {

                                    jQuery("#yearly_adj_data_table").html(response).show();
                                }
                            }

                        });
                    } else {
                        alert('Agreement Start and expiry date is needed !');
                    }

                  
                }else if(jQuery('#yearly_adj_type :selected').val() == 'yearly_adj_fixed'){

                    var yearly_seleted_option = jQuery(this).val();
                    var exp = jQuery('#agree_exp_dt').val();
                    var start = jQuery('#rent_start_dt').val();
 
                    if (exp != '' && start != '') {

                        var postdata = {
                            'start': start, 'exp': exp, 'yearly_seleted_option':yearly_seleted_option
                        };
                        jQuery.ajax({
                            type: "POST",
                            // async:false,
                            cache: false,
                            url: "<?= base_url() ?>index.php/agreement/yearly_adjust_amt",
                            data: postdata,
                            datatype: "html",
                            success: function(response) {
                                jQuery("#yearly_adj_show_data").show();
                                if (response != "") {

                                    jQuery("#yearly_adj_data_table").html(response).show();
                                }
                            }

                        });
                    } else {
                        alert('Agreement Start and expiry date is needed !');
                    }
                     
                }
        });

    // jqxtab march 12
    validToggle = 0;
    jQuery('#form1').jqxValidator({ onError: function () { validToggle = 0;} });
    jQuery('#form1').jqxValidator({ onSuccess: function () { validToggle = 1;} });

     jQuery('#form2').jqxValidator({ onError: function () { validToggle = 0;} });
     jQuery('#form2').jqxValidator({ onSuccess: function () { validToggle = 1;} });

    // jQuery('#form3').jqxValidator({ onError: function () { validToggle = 0;} });
    // jQuery('#form3').jqxValidator({ onSuccess: function () { validToggle = 1;} });

    jQuery('#form4').jqxValidator({ onError: function () { validToggle = 0;} });
    jQuery('#form4').jqxValidator({ onSuccess: function () { validToggle = 1;} });

    jQuery('#form5').jqxValidator({ onError: function () { validToggle = 0;} });
    jQuery('#form5').jqxValidator({ onSuccess: function () { validToggle = 1;} });
 var wizard = (function () {

                //Adding event listeners
                var _addHandlers = function () {

                    jQuery('.nextButton').click(function () {wizard.validate(true);   jQuery('#jqxTabs').jqxTabs('next');});
                    jQuery('.backButton').click(function () {wizard.validate(true);jQuery('#jqxTabs').jqxTabs('previous');});

                };
                return {  

                    //Initializing the wizzard - creating all elements, adding event handlers and starting the validation
                    init: function () {
                        jQuery('#jqxTabs').jqxTabs({  width: '99.8%', height: 1000, keyboardNavigation: false });

                        _addHandlers();
                        <? if($add_edit=='add') {?>
                       // jQuery('#jqxTabs').jqxTabs('disableAt', 1);
                        jQuery('#jqxTabs').jqxTabs('disableAt', 2);
                        jQuery('#jqxTabs').jqxTabs('disableAt', 3);
                     

                        <? } else { ?>
                            wizard.validate(true);
                        <? } ?>
                       
                    },

                    //Validating all wizard tabs
                    validate: function (notify) {
                        if (!this.firstTab(notify)) {
                            jQuery('#jqxTabs').jqxTabs('disableAt', 1);
                            jQuery('#jqxTabs').jqxTabs('disableAt', 2);
                            jQuery('#jqxTabs').jqxTabs('disableAt', 3);
                       
       


                            return;
                        } else {
                            jQuery('#jqxTabs').jqxTabs('enableAt', 1);
                            //  jQuery('#jqxTabs').jqxTabs('disableAt', 2);
                            // jQuery('#jqxTabs').jqxTabs('disableAt', 3);
                            // jQuery('#jqxTabs').jqxTabs('disableAt', 4);
                           // jQuery('#jqxTabs').jqxTabs('enableAt', 3);
                           //alert('tab1-else');
                           //return;
                        }
                        if (!this.secondTab(notify)) {

                           jQuery('#jqxTabs').jqxTabs('disableAt', 2);
                           // jQuery('#jqxTabs').jqxTabs('disableAt', 3);

                            if( jQuery('#location_owner :selected').val()=='own'){ 
                             alert('No landlord is applicable for own location');  
                             jQuery('#jqxTabs').jqxTabs('disableAt', 1);   
                             jQuery('#jqxTabs').jqxTabs('enableAt', 3);
                            }else{
                                jQuery('#jqxTabs').jqxTabs('disableAt', 3);
                            }


                            return;
                        }else {
                          
                            jQuery('#jqxTabs').jqxTabs('enableAt', 2); 

                            // jQuery('#jqxTabs').jqxTabs('disableAt', 3);
                            // jQuery('#jqxTabs').jqxTabs('disableAt', 4); 
                           
                            //return;
                        }
                         if (!this.thirdTab(notify)) {

                           jQuery('#jqxTabs').jqxTabs('disableAt', 3);
                         //  jQuery('#jqxTabs').jqxTabs('disableAt', 4);
                         
                            return;
                        }else {
                            jQuery('#jqxTabs').jqxTabs('enableAt', 3); 
                             // jQuery('#jqxTabs').jqxTabs('disableAt', 4); 
                          // return;
                        }
                        
                    },

                    //Displaying message to the user
                    showHint: function (message, selector) {
                        if (typeof selector === 'undefined') {
                            selector = '.hint';
                        }
                        if (message === '') {
                            message = 'You can continue.';
                        }
                        jQuery(selector).html('<strong>' + message + '</strong>');
                    },

                    //Validating the first tab
                    firstTab: function (notify) {

                        jQuery('#form1').jqxValidator('validate');

                        if(validToggle){
                             //return tab_1_business_address_check();

                            if(tab_1_business_address_check()){
                                return tab_1_owner_check();
                            }
                            
                        }else {return false}
                    },

                    secondTab: function (notify) {

                        jQuery('#form2').jqxValidator('validate');
                       
                        if(validToggle){
                         
                                return business_address_check();
                               
                        } else {return false}
                    },


                    thirdTab: function (notify) { 

                        jQuery('#form4').jqxValidator('validate');

                        if(validToggle){
                            return tab_4_business_address_check();
                           // return true;
                        } else {return false}
                    },

                    //   forthTab: function (notify) {

                    //     jQuery('#form4').jqxValidator('validate');
                    //     //alert(validToggle);
                    //     if(validToggle){
                    //          return tab_4_business_address_check();
                    //            //  return incr_adjust_check();
                    //        // return true;
                    //     } else {return false}
                    // },

                   
                }
            } ());

            //Initializing the wizard
        wizard.init();
 

jQuery('#credit_sts1').bind('select', function(event) {
   
            var item = jQuery("#credit_sts1").jqxComboBox('getSelectedItem');
           
            if(item!=null){
                if (item.value == 'no') {
                  
                     jQuery("#amount_percentage1").prop('disabled', true);
                 
                } 
                else {
                 jQuery("#amount_percentage1").prop('disabled', false);
                }
            }
                   
});

    jQuery('#location_type1').bind('select', function(event) {
   
            var item = jQuery("#location_type1").jqxComboBox('getSelectedItem');
            if(item){ 
               // alert(item.value);
                if (item.value == '1') {
                    jQuery("#branch_id").show();
                    jQuery("#atm_id").hide();
                    jQuery("#sme_id").hide();
                    jQuery("#godown_id").hide();
                      jQuery("#dept_id").hide();
                    
                    // jQuery("input[name='vendor_id']").val(item.value);
                    // jQuery("#vendor_id").val(item.value);
                }else if (item.value == '2'){
                     jQuery("#atm_id").show();
                     jQuery("#branch_id").hide();
                     jQuery("#sme_id").hide();
                     jQuery("#godown_id").hide();
                     jQuery("#dept_id").hide();
                    // jQuery("#division_id").hide();
                } else if (item.value == '3'){
                     jQuery("#atm_id").hide();
                     jQuery("#branch_id").hide();
                     jQuery("#godown_id").hide();
                     jQuery("#sme_id").show();
                      jQuery("#dept_id").hide();
                     //jQuery("#division_id").hide();
                } else if (item.value == '4'){
                     jQuery("#atm_id").hide();
                     jQuery("#branch_id").hide();
                     jQuery("#godown_id").show();
                     jQuery("#sme_id").hide();
                      jQuery("#dept_id").hide();
                     //jQuery("#division_id").hide();
                }  
                else if (item.value == '5'){
                     jQuery("#atm_id").hide();
                     jQuery("#branch_id").hide();
                     jQuery("#godown_id").hide();
                     jQuery("#sme_id").hide();
                      jQuery("#dept_id").show();
                     //jQuery("#division_id").show();
                }  
                else {
                   jQuery("#atm_id").hide();
                     jQuery("#branch_id").hide();
                     jQuery("#sme_id").hide();
                     jQuery("#godown_id").hide();
                      jQuery("#dept_id").hide();
                     //jQuery("#division_id").hide();
                }
                //  last_five_vendor_disbursded_bills();
            }    
    });
    
    jQuery('#monthly_rent').on('blur', function() {
        var total_advance =  jQuery('#total_advance').val();
        var monthly_rent = jQuery('#monthly_rent').val();
            if( jQuery('#location_owner :selected').val()=='rented'){ 
                  if( parseFloat(monthly_rent) > parseFloat(total_advance)){
                        // alert("Monthly rent can not exceed Total amount");
                        // jQuery('#monthly_rent').focus();
                  }
            }

    });

    jQuery('#total_advance').on('blur', function() {
        jQuery('#fixed_month').val('');
        jQuery('#fixed_amt').val('');
    });

    //var $j = jQuery.noConflict();
            jQuery('#window').jqxWindow({height: 150, width: 500, autoOpen: false, cancelButton: jQuery('#closeButton')});
            val = 0;
            jQuery("#schedule").click(function() {
                var advance_amount_sch = $("#advance_amount").val();
                //alert(advance_amount_sch);
                $("#advance_amount_sch").append('<div>' + advance_amount_sch + '</div>');
                
                jQuery('#window').jqxWindow('open');

            });

            var vendor_id = jQuery("input[name='vendor_id']").val();
            var theme = 'classic';



            // var vendor_list = [<? $i = 1;
            foreach ($vendor_list as $row) {
                if ($i != 1) {
                    echo ',';
                } echo '{value:"' . $row->vendor_id . '", label:"' . $row->name . '"}';
                $i++;
            } ?>];
         

            jQuery("#adjustment_type").change(function() {
             
                if (jQuery('#adjustment_type :selected').val() == 'adj_year_basis') {
                    jQuery('.adj_year_basis').show();
                }
                else {
                    jQuery('.adj_year_basis').hide();
                }
            });



    // 31st dec
            jQuery("#tax_type").change(function() {
               
                if (jQuery('#tax_type :selected').val() == 'rent_wise_change') {
                    jQuery('.rent_wise_change').show();
                }
                else {
                    jQuery('.rent_wise_change').hide();
                }
            });

            jQuery("#rent_start_dt").change(function() {
                var today = new Date();
             
                var start_dt = jQuery("#rent_start_dt").datepicker('getDate');
                var ref_dt = jQuery("#rent_start_dt").datepicker('getDate', '+1d');
                var end_dt = jQuery("#agree_exp_dt").datepicker('getDate');
                ref_dt.setMonth(ref_dt.getMonth() + 1);
                if(end_dt!=null){
                    if(end_dt < start_dt){ 
                        alert('Expiry date should be larger than Start date'); 
                        jQuery("#rent_start_dt").val('');
                        //jQuery(this).focus();
                    }else if(ref_dt!=null && end_dt < ref_dt){
                        alert('Rent duration should be Minimum 1 Month'); 
                        jQuery("#rent_start_dt").val('');
                    }
                }
                adjustment_on_exp_date_change();
                increment_ajax(); 
                 
            });



              jQuery("#agree_exp_dt").change(function() {
                 var today = new Date();
             
                var start_dt = jQuery("#rent_start_dt").datepicker('getDate');
                var ref_dt = jQuery("#rent_start_dt").datepicker('getDate', '+1d');
                var end_dt = jQuery("#agree_exp_dt").datepicker('getDate');
                ref_dt.setMonth(ref_dt.getMonth() + 1);
              
                //end_dt.setDate(end_dt.getDate()+30); 
                //alert(ref_dt);
                // var day  = end_dt.getDate();  
                // var month = end_dt.getMonth() + 1;             
                // var year =  end_dt.getFullYear();
                // alert(day + '-' + month + '-' + year);
                

                  if(end_dt.setHours(0,0,0,0) < today.setHours(0,0,0,0))
                    {
                        alert('Expiry date should be larger than Today'); 
                        jQuery("#agree_exp_dt").val('');

                    }else if(end_dt < start_dt){ 
                        alert('Expiry date should be larger than Start date'); 
                        jQuery("#agree_exp_dt").val('');
                        //jQuery(this).focus();
                    }else if(ref_dt!=null && end_dt < ref_dt){
                        alert('Rent duration should be Minimum 1 Month'); 
                        jQuery("#agree_exp_dt").val('');
                    }
                 adjustment_on_exp_date_change();
                 increment_ajax(); 
                 
            });

              jQuery("#increment_start_dt_value").change(function() {
                 var today = new Date();
             
                 var incr_start_dt = jQuery("#increment_start_dt_value").datepicker('getDate');
                 var incr_start_dt_val = jQuery("#increment_start_dt_value").val();
                 var end_dt = jQuery("#agree_exp_dt").datepicker('getDate');
                  if(end_dt < incr_start_dt){ 
                    alert('Expiry date should be larger than Increment Start date'); 
                    jQuery("#increment_start_dt_value").val('');
                    
                    //jQuery(this).focus();
                }
                jQuery("#incr_start_dt").val(incr_start_dt_val);
                //alert(incr_start_dt_val);
                // adjustment_on_exp_date_change();
                 increment_ajax(); 
                 
            });
 
  // 24 july

        <?php for($i=1;$i<=6;$i++){ ?>

            jQuery('#enlisted<?= $i ?>').click(function() { 
                var doc_type_id = jQuery('#enlisted<?= $i ?>').val();

                if(jQuery(this).is(':checked')){ 
                     
                }else{

                        if(jQuery('#filescount<?= $i ?>').html()!=''){ 
                            alert('Please remove the file first');
                            return false;

                        }else{

                        }

                }    
            });  
                
                
        <? } ?>

            jQuery('#vendor_id').keyup(function() {
                commbobox_check(jQuery(this).attr('id'));
            });

            jQuery('#vendor_id').bind('select', function(event) {
                var item = jQuery("#vendor_id").jqxComboBox('getSelectedItem');
                if (item != null) {
                    jQuery("input[name='vendor_id']").val(item.value);
                    jQuery("#vendor_id").val(item.value);
                } else {
                    jQuery("#vendor_id").jqxComboBox('getItemByValue', "");
                    jQuery("#vendor_id").val("");
                }
                //  last_five_vendor_disbursded_bills();    
            });

            var branch_list = [<? $i = 1;
            foreach ($branch_list as $value) {
                if ($i != 1) {
                    echo ',';
                } echo '{value:"' . $value->id . '", label:"' . $value->name . '-' . $value->code . '"}';
                $i++;
            } ?>];

              

            var dept_list = [<? $i = 1;
            foreach ($dept_list as $value) {
                if ($i != 1) {
                    echo ',';
                } echo '{value:"' . $value->id . '", label:"' . $value->name . '"}';
                $i++;
            } ?>];

           

               var rent_others_list = [<? $i = 1;
            foreach ($rent_others_list as $value) {
                if ($i != 1) {
                    echo ',';
                } echo '{value:"' . $value->name . '", label:"' . $value->name . '"}';
                $i++;
            } ?>];

            jQuery("#branch_id").jqxComboBox({source: branch_list, width: '180', height: '21', promptText: "Select Branch"});
            jQuery("#dept_id").jqxComboBox({source: dept_list, width: '180', height: '21', promptText: "Select Department"});
          // jQuery("#division_id").jqxComboBox({source: division_list, width: '180', height: '21', promptText: "Select Division"});
            jQuery("#rent_others_id1").jqxComboBox({source: rent_others_list, width: '180', height: '21', promptText: "Select Other Rent"});

            jQuery("#branch_id").keyup(function() {
                commbobox_check(jQuery(this).attr('id'));
            });

            jQuery("#atm_id").keyup(function() {
                commbobox_check(jQuery(this).attr('id'));
            });
             jQuery("#sme_id").keyup(function() {
                commbobox_check(jQuery(this).attr('id'));
            });

             jQuery("#godown_id").keyup(function() {
                commbobox_check(jQuery(this).attr('id'));
            });
             jQuery("#dept_id").keyup(function() {
                commbobox_check(jQuery(this).attr('id'));
            });
            //  jQuery("#division_id").keyup(function() {
            //     commbobox_check(jQuery(this).attr('id'));
            // });
             jQuery("#rent_others_id1").keyup(function() {
                commbobox_check(jQuery(this).attr('id'));
            });
    // for branch list end

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
            jQuery(document).on("keypress",".number1",function (evt) {
                var charCode = (evt.which) ? evt.which : evt.keyCode;
                if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46) 
                   return false;
           
                return true;            
           });

            //jQuery('.number').keypress(function(event) {
            jQuery(document).on("keypress",".number",function (event) {
                var $this = jQuery(this);
                if ((event.which != 46 || $this.val().indexOf('.') != -1) &&
                   ((event.which < 48 || event.which > 57) &&
                   (event.which != 0 && event.which != 8))) {
                       event.preventDefault();
                }

                var text = jQuery(this).val();
                if ((event.which == 46) && (text.indexOf('.') == -1)) {
                    setTimeout(function() {
                        if ($this.val().substring($this.val().indexOf('.')).length > 3) {
                            $this.val($this.val().substring(0, $this.val().indexOf('.') + 3));
                        }
                    }, 1);
                }

                if ((text.indexOf('.') != -1) &&
                    (text.substring(text.indexOf('.')).length > 2) &&
                    (event.which != 0 && event.which != 8) &&
                    (jQuery(this)[0].selectionStart >= text.length - 2)) {
                        event.preventDefault();
                }      
            });
            
            jQuery('#form1').jqxValidator({
                rules: [
                    { input: '#rent_start_dt', message: 'Rent start date is required!', action: 'change,blur', rule: 'required'}
                    ,{ input: '#agree_exp_dt', message: 'Agreement expire date is required!', action: 'change,blur', rule: 'required'}
                    ,{ input: '#location_name', message: 'Location Name is required!', action: 'keyup,blur', rule: 'required'}
                    ,{ input: '#location_owner', message: 'Location owner is required!', action: 'select,blur', rule:function(input,commit){
                   
                       
                       if(input.val()==''){ return false; }else { return true ;} 
                  
                       }
                   }
                 ,{ input: '#total_square_ft', message: 'Total Square feet is required!', action: 'keyup,blur', rule: 'required'}
                 ,{ input: '#point_of_payment', message: 'Point Of payment is required!', action: 'select,blur', rule:function(input,commit){
                   
                       if(input.val()==''){ return false; }else { return true ;} 
                  
                       }
                   }

                   ,{ input: '#total_advance', message: 'Advance amount is required!', action: 'keyup,blur', rule:function(input,commit){

                        if( jQuery('#location_owner :selected').val()=='own'){ 
                             return true ;
                        }else if (jQuery('#adjust_adv_type :selected').val()=='') {
                            return true ;
                        }else{
                            //alert(jQuery('#adjust_adv_type :selected').val());   // || jQuery('#adjust_adv_type :selected').val()!=''
                            if(input.val()=='' && jQuery('#adjust_adv_type :selected').val()!=1  ){ 
                          
                                return false; 
                            }
                            else { 
                           
                                return true ;
                            }

                        }

                       }
                   }
                   ,{ input: '#monthly_rent', message: 'Monthly rent is required!', action: 'change,blur', rule: function(input, commit){
                        if( jQuery('#location_owner :selected').val()=='own'){ 
                             return true ;
                        }
                        else{
                            if (jQuery('#monthly_rent').val() == '' || jQuery('#monthly_rent').val() <= 0) {
                                return false;
                            }
                            else return true;
                        }
                   
                        }
                   }

                   ,{ input: '#adjust_adv_type', message: 'Adjustment Type is required!', action: 'change,blur', rule:function(input,commit){
                    
                        //return vendor_combo_required('vendor_id');

                        if( jQuery('#location_owner :selected').val()=='own'){ 
                             return true ;
                        }else{
                            return emptyCheckSelect('adjust_adv_type') ;
                            }
                   
                        }
                    }
                    
                    <? if ($add_edit == 'add') { ?>
                                           
                    <?php } ?>
                ]
            });

        jQuery('#form2').jqxValidator({
                        rules: [
                            { input: '#amount_percentage1', message: 'Credit Status is required!', action: 'keyup,blur', rule: 'required'}
                        ,{ input: '#vendor_id1', message: 'Landlord is required!', action: 'change,blur', rule:function(input,commit){
                            
                                return emptyCheckCombo('vendor_id1') ;
                           
                        }
                    }
                    ,{ input: '#credit_sts1', message: 'Credit Status is required!', action: 'change,blur', rule:function(input,commit){
                            
                                return emptyCheckCombo('credit_sts1') ;
                           
                        }
                    }
                         
                        <? if ($add_edit == 'add') { ?>
                                                
                        <?php } ?>
                        ]
         });



    jQuery('#form4').jqxValidator({
                    rules: [

                    { input: '#increment_type', message: 'Increment Type is required!', action: 'select,blur', rule:function(input,commit){
                        
                            if(input.val()==''){ return false; }else { return true ;} 
                       
                        }
                    }
                  
                <? if ($add_edit == 'add') { ?>
                                        
                <?php } ?>
                                ]
    });

    jQuery('#form5').jqxValidator({
                    rules: [
                    //    { input: '#adjust_adv_type', message: 'Adjustment Type is required!', action: 'change,blur', rule: 'required'}
                    
                     
                    <? if ($add_edit == 'add') { ?>
                                            
                    <?php } ?>
                                    ]
    });



});

        var options = {
            complete: function(response)
            {

                jQuery("#msgArea").val('');
                window.parent.jQuery("#error").show();
                window.parent.jQuery("#error").fadeOut(11500);
                window.parent.jQuery("#error").html('<img align="absmiddle" src="' + baseurl + 'images/drag.png" border="0" /> &nbsp;Successfully Saved');
                window.top.EOL.messageBoard.close();

            }

        }



        function addmore() {

            var count = (jQuery("#counter").val());
            count++;
            var miscodelist = [];
            var cost_centerlist = [<? $i = 1;
        foreach ($cost_center as $value) {
            if ($i != 1) {
                echo ',';
            } echo '{value:"' . $value->code . '", label:"' . $value->name . '"}';
            $i++;
        } ?>];

            var str = '<tr id="tr' + count + '">' +
                    '<td align="center" width="2%" ><img src="<?= base_url() ?>images/del.png" onclick="delete_row(' + count + ')"><input type="hidden" name="delete' + count + '" id="delete' + count + '" value="0"><input type="hidden" name="existing' + count + '" id="existing' + count + '" value="0"><input type="hidden" name="id' + count + '" id="id' + count + '" value=""></td>' +
                    
                    //'<td><input name="cost_center_name'+count+'" type="text" class="number" id="cost_center_name'+count+'" value="" align="right"  style="text-align:right;width:110px;"  /></td>'+
                    '<td><div class="cost_center" id="cost_center' + count + '" name="cost_center' + count + '"></div></td>' +
                    //'<td><input name="mis_code'+count+'" type="text" class="number" id="mis_code'+count+'" value="" align="right"  style="text-align:right;width:110px;"  /></td>'+
                    '<td><div id="mis_code' + count + '" name="mis_code' + count + '"></div></td>' +
                    '<td><input name="cost_center_amount' + count + '" type="text" class="number" id="cost_center_amount' + count + '" value="" align="right"  style="text-align:right;width:110px;"  /></td>' +
                    '</tr>';


            //  jQuery('#pettyTable tbody').append(str);
            jQuery('#cost_center_table tbody').append(str);
            jQuery("#cost_center" + count).jqxComboBox({source: cost_centerlist, placeHolder: "Select Cost Center", width: '180', height: '20'});
           
            jQuery("#mis_code" + count).jqxComboBox({source: miscodelist, placeHolder: "Select MIS Code", width: '180', height: '20'});

            jQuery("#cost_center_amount" + count).jqxInput({minLength: 3});

            // 23 january

            jQuery("#cost_center" + count + ",#mis_code" + count + "").keyup(function() {
                commbobox_check(jQuery(this).attr('id'));
            });
           
            cost_center_change('cost_center1', '' + count);

            jQuery("#counter").val(count);
        }


        function addmore_landlord() {
            
   
            var count_landlord = (jQuery("#counter_landlord").val());
            count_landlord++;
            //var miscodelist =[];
            //   var cost_centerlist = [<? $i = 1;
                foreach ($cost_center as $value) {
                    if ($i != 1) {
                        echo ',';
                    } echo '{value:"' . $value->id . '", label:"' . $value->name . '"}';
                    $i++;
                } ?>];
                            var vendor_list = [<? $i = 1;
                foreach ($vendor_list as $value) {
                    if ($i != 1) {
                        echo ',';
                    } echo '{value:"' . $value->vendor_id . '", label:"' . $value->name . '"}';
                    $i++;
                } ?>];
            var credit_sts = [{value: "yes", label: "Yes"}, {value: "no", label: "No"}];
  
            var str = '<tr id="tr_landlord' + count_landlord + '" style="background-color:#E1C8C2;">' +
                  //  '<td align="center" width="2%" ><img src="<?= base_url() ?>images/del.png" onclick="delete_row_landlord(' + count_landlord + ')"><input type="hidden" name="delete' + count_landlord + '" id="delete_landlord' + count_landlord + '" value="0"><input type="hidden" name="existing' + count_landlord + '" id="existing' + count_landlord + '" value="0"><input type="hidden" name="id' + count_landlord + '" id="id' + count_landlord + '" value=""></td>' +
                   
                    '<td align="center" width="2%" ><img src="<?= base_url() ?>images/del.png" onclick="delete_row_landlord(' + count_landlord + ')"><input type="hidden" name="delete_landlord' + count_landlord + '" id="delete_landlord' + count_landlord + '" value="0"><input type="hidden" name="existing' + count_landlord + '" id="existing' + count_landlord + '" value="0"><input type="hidden" name="id' + count_landlord + '" id="id' + count_landlord + '" value=""></td>' +
                    '<td align="center" width="5%" ><img src="<?= base_url() ?>images/plus.gif" onclick="preview_window(' + count_landlord + ')"> &nbsp;&nbsp; <img src="<?= base_url() ?>images/edit.png" onclick="edit_preview_window(' + count_landlord + ')" id="at'+ count_landlord +'" serial=""></td>' +
                    '<td><div class="vendor_id1" id="vendor_id' + count_landlord + '" name="vendor_id' + count_landlord + '"></div></td>' +
                    '<td><div id="credit_sts' + count_landlord + '" name="credit_sts' + count_landlord + '"></div></td>' +
                    '<td><input name="advance_amount_percentage' + count_landlord + '" type="text" class="adv_ll_amount number flags" id="advance_amount_percentage' + count_landlord + '" value="" align="left"  style="text-align:left;width:130px;"  /></td>' +
                    '<td><input name="amount_percentage' + count_landlord + '" type="text" class="ll_amount number flags" id="amount_percentage' + count_landlord + '" value="" align="left"  style="text-align:left;width:130px;"  /></td>' +
                    '</tr>';

            jQuery('#land_lord_table tbody').append(str);
            jQuery("#at"+count_landlord).hide(); 
            jQuery("#vendor_id" + count_landlord).jqxComboBox({source: vendor_list, placeHolder: "Select Landlord", width: '280', height: '20'});
            jQuery("#credit_sts" + count_landlord).jqxComboBox({source: credit_sts, placeHolder: "Select Credit Status", width: '180', height: '20'});
            jQuery("#counter_landlord").val(count_landlord);

            jQuery("#credit_sts" + count_landlord).jqxComboBox('selectItem', 'yes');

            jQuery("#vendor_id" + count_landlord).keyup(function(){
                        commbobox_check(jQuery(this).attr('id'));
                    });
            jQuery("#credit_sts" + count_landlord).keyup(function(){
                commbobox_check(jQuery(this).attr('id'));
            });
           change_credit_sts('credit_sts' + count_landlord, '' + count_landlord);
           landlords_option_chk(count_landlord);
          
        }

      function  addmore_percent(){

                jQuery('.ll_amount').keyup(function(){
                    var sum = 0;
                    jQuery('.ll_amount').each(function() {
                        sum += Number(jQuery(this).val());
                        
                    });
                    if(sum>100) {
                        alert('Percent amount can not be exceed to 100%');
                       
                    }
                
                });

        }


      
    function emptyCheckCombo(field){
        //alert(jQuery("#"+field).val());   
        if(jQuery("#"+field).val()==''){ return false; }

        var item = jQuery("#"+field).jqxComboBox('getSelectedItem');
        if(!item){return false;} else {return true};

    }

    function emptyCheckSelect(field){
    //alert((jQuery("#"+field).val()=='')&&(jQuery("#bill_type").val()=='Vendor Advance') ||(jQuery("#bill_type").val()=='Bill With Adjustment') ||(jQuery("#bill_type").val()=='Bill Without Adjustment'))
        if(jQuery("#"+field).val()=='' && jQuery('#location_owner :selected').val()=='rented' ){ return false; }

        var item = jQuery( "#"+field+ " option:selected" ).text();
        if(!item && jQuery('#location_owner :selected').val()=='rented'){return false;} else {return true};

    }


        function addmore_location_type() {

            var count_location_type = (jQuery("#counter_location_type").val());
            count_location_type++;
            var miscodelist = [];
            var loc_vat_sts = [{value: "yes", label: "Yes"}, {value: "no", label: "No"}];
            var loc_tax_sts = [{value: "yes", label: "Yes"}, {value: "no", label: "No"}];

            var branch_list = [<? $i = 1;
                            foreach ($branch_list as $value) {
                                if ($i != 1) {
                                    echo ',';
                                } echo '{value:"' . $value->id . '", label:"' . $value->name . '-' . $value->code . '"}';
                                $i++;
                            } ?>];
           
                            var dept_list = [<? $i = 1;
                            foreach ($dept_list as $value) {
                                if ($i != 1) {
                                    echo ',';
                                } echo '{value:"' . $value->id . '", label:"' . $value->name . '"}';
                                $i++;
                            } ?>];

                            
            var vendor_list = [<? $i = 1;
                                foreach ($vendor_list as $value) {
                                    if ($i != 1) {
                                        echo ',';
                                    } echo '{value:"' . $value->vendor_id . '", label:"' . $value->name . '"}';
                                    $i++;
                                } ?>];

            var credit_sts = [{value: "yes", label: "Yes"}, {value: "no", label: "No"}];
                 

               var location_type = [<? $i = 1;
                foreach ($location_type_list as $value) {
                    if ($i != 1) {
                        echo ',';
                    } echo '{value:"' . $value->id . '", label:"' . $value->name . '"}';
                    $i++;
                } ?>];

   

            var str = '<tr id="tr_landlord_type' + count_location_type + '">' +
                    '<td align="center" width="2%" ><img src="<?= base_url() ?>images/del.png" onclick="delete_row_landlord_type(' + count_location_type + ')"><input type="hidden" name="delete' + count_location_type + '" id="delete_landlord_type' + count_location_type + '" value="0"><input type="hidden" name="existing' + count_location_type + '" id="existing' + count_location_type + '" value="0"><input type="hidden" name="id' + count_location_type + '" id="id' + count_location_type + '" value=""></td>' +
                    '<td ><div class="location_type1 location_type_cls" id="location_type' + count_location_type + '" name="location_type' + count_location_type + '" style="margin-left:30px;"></div></td>' +
                    '<td style="display: none;"><div id="branch_id' + count_location_type + '" name="branch_id' + count_location_type + '"></div><div id="atm_id' + count_location_type + '" name="atm_id' + count_location_type + '"></div><div id="sme_id' + count_location_type + '" name="sme_id' + count_location_type + '"></div><div id="godown_id' + count_location_type + '" name="godown_id' + count_location_type + '"></div><div id="dept_id' + count_location_type + '" name="dept_id' + count_location_type + '"></div><br /><div id="' + count_location_type + '" name="' + count_location_type + '"></div></td>';
            
       
            str +=  '<td><div id="mis_code' + count_location_type + '" name="mis_code' + count_location_type + '" class="mis_code" style="margin-left:30px;"></div></td>';
            str +=  '<td><div id="loc_vat_sts' + count_location_type + '" name="loc_vat_sts' + count_location_type + '" class="loc_vat_sts" style="margin-left:5px;"></div></td>' + 
                    '<td><div id="loc_tax_sts' + count_location_type + '" name="loc_tax_sts' + count_location_type + '" class="loc_tax_sts" style="margin-left:5px;"></div></td>'; 
                    
           
            str +=  '<td style="text-align:center"><input name="square_ft' + count_location_type + '" type="text" class="number flags" id="square_ft' + count_location_type + '" value="" align="left"  style="text-align:left;width:130px;" onblur="cost_sft('+ count_location_type +')" onkeyup="cal_total_sft('+ count_location_type +')"/></td>' +
                    '<td style="text-align:center"><input name="cost_sft' + count_location_type + '" type="text" class="number flags" id="cost_sft' + count_location_type + '" value="" align="left"  style="text-align:left;width:130px;"  readonly/></td>' +
                    '<td style="text-align:center"><input name="location_type_amount_percentage' + count_location_type + '" type="text" class="number1 flags addmore_location_type ll_type_amount" id="location_type_amount_percentage' + count_location_type + '" value="" align="left"  style="text-align:left;width:130px;"  onblur="calculate_cost_by_percent('+ count_location_type +')" onkeyup="cal_total_percent('+ count_location_type +')"/></td>' +
                    '</tr>';
 
            jQuery('#location_type_table tbody').append(str);
            jQuery("#vendor_id" + count_location_type).jqxComboBox({source: vendor_list, placeHolder: "Select Landlord", width: '280', height: '20'});
            //jQuery("#credit_sts" + count_location_type).jqxComboBox({source: credit_sts, placeHolder: "Select Credit Status", width: '180', height: '20'});
            jQuery("#location_type" + count_location_type).jqxComboBox({source: location_type, placeHolder: "Select Location Type", width: '180', height: '20'});
            jQuery("#counter_location_type").val(count_location_type);
            jQuery("#branch_id" + count_location_type).jqxComboBox({source: branch_list, width: '180', height: '21', promptText: "Select Branch"});
            jQuery("#dept_id" + count_location_type).jqxComboBox({source: dept_list, width: '180', height: '21', promptText: "Select Department"});
           
            jQuery("#loc_vat_sts" + count_location_type).jqxComboBox({source: loc_vat_sts, placeHolder: "Select Vat Status", width: '100', height: '20'});
            jQuery("#loc_vat_sts"+ count_location_type).jqxComboBox('selectItem', 'yes'); 
            jQuery("#loc_tax_sts" + count_location_type).jqxComboBox({source: loc_tax_sts, placeHolder: "Select Tax Status", width: '100', height: '20'});
            jQuery("#loc_tax_sts"+ count_location_type).jqxComboBox('selectItem', 'yes');

            jQuery("#location_type" + count_location_type).keyup(function() {
                commbobox_check(jQuery(this).attr('id'));
            });

            jQuery("#branch_id" + count_location_type).keyup(function() {
                commbobox_check(jQuery(this).attr('id'));
            });
            

              jQuery("#dept_id" + count_location_type).keyup(function() {
                commbobox_check(jQuery(this).attr('id'));
            });
          
             jQuery("#mis_code" + count_location_type).jqxComboBox({source: miscodelist, placeHolder: "Select MIS Code", width: '180', height: '20'});

            jQuery("#mis_code" + count_location_type).keyup(function(){
                    commbobox_check(jQuery(this).attr('id'));
            });
            jQuery("#loc_vat_sts" + count_location_type).keyup(function(){
                        commbobox_check(jQuery(this).attr('id'));
                    });  
            jQuery("#loc_tax_sts" + count_location_type).keyup(function(){
                        commbobox_check(jQuery(this).attr('id'));
                    });

            jQuery("#branch_id" + count_location_type).hide();
          
            jQuery("#dept_id" + count_location_type).hide();
            //jQuery("#division_id" + count_location_type).hide();
            change_location_type('location_type' + count_location_type, '' + count_location_type);
            cost_center_change_edit('location_type' + count_location_type, '' + count_location_type);
            //location_type_option_chk(count_location_type);
            duplicate_mis_check(count_location_type);
            
        }


function cost_sft(count){
    var total_sft =0;
    var count_location_type = jQuery("#counter_location_type").val();
    var count_others_rent_type = (jQuery("#counter_others_rent_type").val());

    var only_monthly_rent = jQuery("#monthly_rent").val();
    var others_rent = jQuery("#others_rent").val();

    if (jQuery("#monthly_rent").val().trim().length === 0) {
            //jQuery("#monthly_rent").val(0);
            only_monthly_rent=0;
        }
    if (jQuery("#others_rent").val().trim().length === 0) {
            others_rent=0;
        }    

    //var monthly_rent = parseFloat(only_monthly_rent) + parseFloat(others_rent);
    var monthly_rent = parseFloat(only_monthly_rent);
    
    if(monthly_rent > 0){

        for(var i=1;i<=count_location_type;i++){
            if( jQuery("#delete_landlord_type"+i).val()==0)
            {
                total_sft= total_sft + +jQuery("#square_ft"+i).val();
            }
        }

        // for(var i=1;i<=count_others_rent_type;i++){
        //     if( jQuery("#delete_others"+i).val()==0)
        //     {
        //         total_sft= total_sft + +jQuery("#others_square_ft"+i).val();
        //     }
        // }

     
        if(total_sft > 0){
            for(var i=1;i<=count_location_type;i++){
                if( jQuery("#delete_landlord_type"+i).val()==0)
                {
                    var per_sft_cost = monthly_rent / total_sft;
                    var cost_sft= jQuery("#square_ft"+i).val() * per_sft_cost;
                    jQuery("#cost_sft"+i).val(cost_sft.toFixed(2));
                }
            }

            // for(var i=1;i<=count_others_rent_type;i++){
            //     if( jQuery("#delete_others"+i).val()==0)
            //     {
            //         var per_sft_cost = monthly_rent / total_sft;
            //         var cost_sft= jQuery("#others_square_ft"+i).val() * per_sft_cost;
            //         jQuery("#others_cost_sft"+i).val(cost_sft.toFixed(2));
            //     }
            // }
             location_type_percent_calculation('main',monthly_rent,count_location_type,count_others_rent_type);
        }
        
    }   

    cal_total_percent(1);
    
}

// 13 sep 2018
function amount_percent_others(count){
    var total_dir_amt =0;
    var count_location_type = jQuery("#counter_location_type").val();
    var count_others_rent_type = (jQuery("#counter_others_rent_type").val());

    var others_rent = jQuery("#others_rent").val();

   
    if (jQuery("#others_rent").val().trim().length === 0) {
            others_rent=0;
        }    

    var others_rent = parseFloat(others_rent);
    
    if(others_rent > 0){

        // for(var i=1;i<=count_location_type;i++){
        //     if( jQuery("#delete_landlord_type"+i).val()==0)
        //     {
        //         total_sft= total_sft + +jQuery("#square_ft"+i).val();
        //     }
        // }

        for(var i=1;i<=count_others_rent_type;i++){
            if( jQuery("#delete_others"+i).val()==0)
            {
                total_dir_amt= total_dir_amt + +jQuery("#others_type_amount_percentage"+i).val();
            }
        }

     
        if(total_dir_amt > 0){
            // for(var i=1;i<=count_location_type;i++){
            //     if( jQuery("#delete_landlord_type"+i).val()==0)
            //     {
            //         var per_sft_cost = monthly_rent / total_sft;
            //         var cost_sft= jQuery("#square_ft"+i).val() * per_sft_cost;
            //         jQuery("#cost_sft"+i).val(cost_sft.toFixed(2));
            //     }
            // }

            for(var i=1;i<=count_others_rent_type;i++){
                if( jQuery("#delete_others"+i).val()==0)
                {
                    var per_amount = jQuery("#others_type_amount_percentage"+i).val();
                    if(parseFloat(per_amount) > 0){
                         jQuery("#others_cost_sft"+i).val(parseFloat(per_amount).toFixed(2));
                    }
                     
                     //alert(per_amount);
                    // var per_sft_cost = others_rent / total_sft;
                    // var cost_sft= jQuery("#others_square_ft"+i).val() * per_sft_cost;
                   
                }
            }
             location_type_percent_calculation('others',others_rent,count_location_type,count_others_rent_type);
             calculate_cost_by_others_percent(1);
        }
        
    }   

    cal_total_percent(1);
    
}

// 27 march
function cal_total_sft(count){
    var total_sft =0;
    var count_location_type = jQuery("#counter_location_type").val();
    var count_others_rent_type = (jQuery("#counter_others_rent_type").val());


        for(var i=1;i<=count_location_type;i++){
            if( jQuery("#delete_landlord_type"+i).val()==0)
            {
                total_sft= total_sft + +jQuery("#square_ft"+i).val();
            }
        }

        for(var i=1;i<=count_others_rent_type;i++){
            if( jQuery("#delete_others"+i).val()==0)
            {
                total_sft= total_sft + +jQuery("#others_square_ft"+i).val();
            }
        }

    jQuery("#Sq_Amount_Span").html(parseFloat(total_sft).toFixed(2));
}

//  17 jul 2018
function cal_per_month_adv(){

    var total_advance = jQuery("#total_advance").val();
    var per_month_amt=0;
    var month_val  = jQuery('#fixed_month').val()
    if(parseFloat(total_advance) > 0 && parseFloat(month_val) > 0){
        per_month_amt = total_advance / month_val;
        jQuery("#fixed_amt").val(parseFloat(per_month_amt).toFixed(2));
    }

}

function cal_month_no(){

    var total_advance = jQuery("#total_advance").val();
    var per_month_amt=0;
    var fixed_amt  = jQuery('#fixed_amt').val()
    if(parseFloat(total_advance) > 0 && parseFloat(fixed_amt) > 0){
        per_month_amt = total_advance / fixed_amt;
        jQuery("#fixed_month").val(parseFloat(per_month_amt).toFixed(2));
    }

}

function cal_total_percent(count){
    var total_sft =0;
    var count_location_type = jQuery("#counter_location_type").val();
    var count_others_rent_type = (jQuery("#counter_others_rent_type").val());


        for(var i=1;i<=count_location_type;i++){
            if( jQuery("#delete_landlord_type"+i).val()==0)
            {
                total_sft= total_sft + +jQuery("#location_type_amount_percentage"+i).val();
            }
        }

        // for(var i=1;i<=count_others_rent_type;i++){
        //     if( jQuery("#delete_others"+i).val()==0)
        //     {
        //         total_sft= total_sft + +jQuery("#others_type_percentage"+i).val();
        //     }
        // }
         
    jQuery("#percent_Amount_Span").html(total_sft.toFixed(4));
}
// 3 april 2018


    function amount_percent_per_cost(count){
        var total_sft =0;

        var only_monthly_rent = jQuery("#monthly_rent").val();
        var others_rent = jQuery("#others_rent").val();

        if (jQuery("#monthly_rent").val().trim().length === 0) {
                //jQuery("#monthly_rent").val(0);
                only_monthly_rent=0;
            }
        if (jQuery("#others_rent").val().trim().length === 0) {
                others_rent=0;
            }    

        var monthly_rent = parseFloat(only_monthly_rent) + parseFloat(others_rent);
        
        if(monthly_rent > 0){

                    if( jQuery("#delete_landlord_type"+count).val()==0)
                    {
                        jQuery("#location_type_amount_percentage"+count).val(((jQuery("#cost_sft"+count).val() * 100 )/monthly_rent).toFixed(4));
                    }
           
        }   
        
    }


    function others_amount_percent_per_cost(count){
        var total_sft =0;

        var only_monthly_rent = jQuery("#monthly_rent").val();
        var others_rent = jQuery("#others_rent").val();

        if (jQuery("#monthly_rent").val().trim().length === 0) {
                only_monthly_rent=0;
            }
        if (jQuery("#others_rent").val().trim().length === 0) {
                others_rent=0;
            }    

        var monthly_rent = parseFloat(only_monthly_rent) + parseFloat(others_rent);
        
        if(monthly_rent > 0){

                if( jQuery("#delete_others"+count).val()==0)
                {
                    jQuery("#others_type_percentage"+count).val(((jQuery("#others_cost_sft"+count).val() * 100 )/monthly_rent).toFixed(4));
                }
              
        }   
        
    }

    // 4 april 

    
    function calculate_cost_by_percent(count){
        var total_sft =0;

        var total_square_ft = jQuery("#total_square_ft").val();
        var only_monthly_rent = jQuery("#monthly_rent").val();
        var others_rent = jQuery("#others_rent").val();

        if (jQuery("#monthly_rent").val().trim().length === 0) {
                //jQuery("#monthly_rent").val(0);
                only_monthly_rent=0;
            }
        if (jQuery("#others_rent").val().trim().length === 0) {
                others_rent=0;
            }    

        //var monthly_rent = parseFloat(only_monthly_rent) + parseFloat(others_rent);
        var monthly_rent = parseFloat(only_monthly_rent);
        
        if(monthly_rent > 0){

            if( jQuery("#delete_landlord_type"+count).val()==0)
            {
                jQuery("#cost_sft"+count).val(((jQuery("#location_type_amount_percentage"+count).val() * monthly_rent )/100).toFixed(4));
                jQuery("#square_ft"+count).val(((jQuery("#location_type_amount_percentage"+count).val() * total_square_ft )/100).toFixed(2));
                cal_total_sft(1);
            }
        
        }   
        
    }

    
    function calculate_cost_by_others_percent(count){
        var total_sft =0;

        var total_square_ft = jQuery("#total_square_ft").val();
        var only_monthly_rent = jQuery("#monthly_rent").val();
        var others_rent = jQuery("#others_rent").val();

        if (jQuery("#monthly_rent").val().trim().length === 0) {
                only_monthly_rent=0;
            }
        if (jQuery("#others_rent").val().trim().length === 0) {
                others_rent=0;
            }    

        //var monthly_rent = parseFloat(only_monthly_rent) + parseFloat(others_rent);
        var others_rent = parseFloat(others_rent);
        
        if(others_rent > 0){

                if( jQuery("#delete_others"+count).val()==0)
                {
                    jQuery("#others_cost_sft"+count).val(((jQuery("#others_type_percentage"+count).val() * others_rent )/100).toFixed(4));
                    jQuery("#others_type_amount_percentage"+count).val(((jQuery("#others_type_percentage"+count).val() * others_rent )/100).toFixed(4));
                    //jQuery("#others_square_ft"+count).val(((jQuery("#others_type_percentage"+count).val() * total_square_ft )/100).toFixed(2));
                    cal_total_sft(1);
                }
              
        }  
        
    }

// 29 jan

 // function others_cost_sft(count){ }
 
 function location_type_percent_calculation(type,monthly_rent,count_location_type,count_others_rent_type){

    if(type!='others'){
        for(var i=1;i<=count_location_type;i++){
            if( jQuery("#delete_landlord_type"+i).val()==0)
            {
                var calculated_amount = (jQuery("#cost_sft"+i).val()*100)/monthly_rent;
               
                jQuery("#location_type_amount_percentage"+i).val(calculated_amount.toFixed(4));
            }
            
        }
    }
    if(type=='others'){

        for(var i=1;i<=count_others_rent_type;i++){
            if( jQuery("#delete_others"+i).val()==0)
            {
                var calculated_amount = (jQuery("#others_cost_sft"+i).val()*100)/monthly_rent;
               
                jQuery("#others_type_percentage"+i).val(calculated_amount.toFixed(4));
            }
            
        }
    }
    

}


// others rent type addmore

function addmore_others_rent_type() {

           
            var count_others_rent_type = (jQuery("#counter_others_rent_type").val());
            count_others_rent_type++;
            var vat_sts = [{value: "yes", label: "Yes"}, {value: "no", label: "No"}];
            var tax_sts = [{value: "yes", label: "Yes"}, {value: "no", label: "No"}];
            var miscodelist = [];
            var rent_others_list = [<? $i = 1;
                foreach ($rent_others_list as $value) {
                    if ($i != 1) {
                        echo ',';
                    } echo '{value:"' . $value->name . '", label:"' . $value->name . '"}';
                    $i++;
                } ?>];

            var str = '<tr id="others_rent_tr' + count_others_rent_type + '">' +
                    '<td align="center" width="2%" ><img src="<?= base_url() ?>images/del.png" onclick="delete_others_rent_type(' + count_others_rent_type + ')"><input type="hidden" name="delete_others' + count_others_rent_type + '" id="delete_others' + count_others_rent_type + '" value="0"><input type="hidden" name="existing_others' + count_others_rent_type + '" id="existing_others' + count_others_rent_type + '" value="0"><input type="hidden" name="id_others' + count_others_rent_type + '" id="id_others' + count_others_rent_type + '" value=""></td>' +
                    '<td ><div class="rent_others_id" id="rent_others_id' + count_others_rent_type + '" name="rent_others_id' + count_others_rent_type + '"  style="margin-left:5px;"></div></td>' +
                    '<td><div id="others_mis_code' + count_others_rent_type + '" name="others_mis_code' + count_others_rent_type + '" class="others_mis_code" style="margin-left:5px;" ></div></td>' + 
                    '<td><div id="vat_sts' + count_others_rent_type + '" name="vat_sts' + count_others_rent_type + '" class="vat_sts" style="margin-left:5px;"></div></td>' + 
                    '<td><div id="tax_sts' + count_others_rent_type + '" name="tax_sts' + count_others_rent_type + '" class="tax_sts" style="margin-left:5px;"></div></td>' + 
                    '<td style="text-align:center"><input name="others_type_amount_percentage' + count_others_rent_type + '" type="text" class="number flags addmore_location_type" id="others_type_amount_percentage' + count_others_rent_type + '" value="" align="left"  onblur="amount_percent_others('+ count_others_rent_type +')" style="text-align:left;width:120px;"  /></td>' +
                    '<td style="text-align:center"><input name="others_square_ft' + count_others_rent_type + '" type="text" class="number flags" id="others_square_ft' + count_others_rent_type + '" value="" align="left"  style="text-align:left;width:120px;"   onkeyup="cal_total_sft('+ count_others_rent_type +')" /></td>' +
                    
                    '<td style="text-align:center"><input name="others_cost_sft' + count_others_rent_type + '" type="text" class="number flags addmore_location_type" id="others_cost_sft' + count_others_rent_type + '" value="" align="left"  style="text-align:left;width:120px;"  readonly/></td>' +
                    '<td style="text-align:center"><input name="others_type_percentage' + count_others_rent_type + '" type="text" class="number flags addmore_location_type" id="others_type_percentage' + count_others_rent_type + '" value="" align="left"  style="text-align:left;width:120px;"  onblur="calculate_cost_by_others_percent('+ count_others_rent_type +')"  onkeyup="cal_total_percent('+ count_others_rent_type +')"/></td>' +
                    '</tr>';
 
            jQuery('#others_type_table tbody').append(str);
            //jQuery("#credit_sts" + count_location_type).jqxComboBox({source: credit_sts, placeHolder: "Select Credit Status", width: '180', height: '20'});
           jQuery("#counter_others_rent_type").val(count_others_rent_type);
             
            jQuery("#rent_others_id" + count_others_rent_type).jqxComboBox({source: rent_others_list, placeHolder: "Select Others Rent", width: '180', height: '20'});
            jQuery("#others_mis_code" + count_others_rent_type).jqxComboBox({source: miscodelist, placeHolder: "Select MIS Code", width: '150', height: '20'});
            jQuery("#vat_sts" + count_others_rent_type).jqxComboBox({source: vat_sts, placeHolder: "Select Vat Status", width: '100', height: '20'});
            jQuery("#vat_sts"+ count_others_rent_type).jqxComboBox('selectItem', 'yes'); 
            jQuery("#tax_sts" + count_others_rent_type).jqxComboBox({source: tax_sts, placeHolder: "Select Tax Status", width: '100', height: '20'});
            jQuery("#tax_sts"+ count_others_rent_type).jqxComboBox('selectItem', 'yes');
            jQuery("#rent_others_id" + count_others_rent_type).keyup(function(){
                        commbobox_check(jQuery(this).attr('id'));
                    });
            jQuery("#others_mis_code" + count_others_rent_type).keyup(function(){
                        commbobox_check(jQuery(this).attr('id'));
                    });
            jQuery("#vat_sts" + count_others_rent_type).keyup(function(){
                        commbobox_check(jQuery(this).attr('id'));
                    });  
            jQuery("#tax_sts" + count_others_rent_type).keyup(function(){
                        commbobox_check(jQuery(this).attr('id'));
                    });
            

     
            cost_center_change_edit_for_others('rent_others_id' + count_others_rent_type, '' + count_others_rent_type);
            others_location_type_option_chk(count_others_rent_type);
            others_location_duplicate_mis_chk(count_others_rent_type);
        }


function  addmore_location_type_percent(){

                jQuery('.ll_type_amount').keyup(function () {
                    var addmore_location_type_sum = 0;
                    jQuery('.ll_type_amount').each(function() {
                        addmore_location_type_sum += Number(jQuery(this).val());
                         
                        
                    });
                    if(addmore_location_type_sum>100) {
                        alert('Percent amount can not be exceed to 100%');
                        jQuery(this).val('');
                   // jQuery('.ll_amount').attr('disabled','disabled'); 
                }
                 
                 });

        }
        // march 12

function call_ajax_submit()
{
    jQuery("#sendButton").hide();
    jQuery("#loading").show();

     jQuery(".common_inc_cls").prop('disabled',false);
     jQuery(".common_other_cls").prop('disabled',false);

    var postdata = jQuery('#form1,#form2,#form4,#form5').serialize();
    jQuery.ajax({
            type: "POST",
            cache: false,
            url: "<?=base_url()?>index.php/agreement/add_edit_action/<?=$add_edit?>/<?=$id?>",
            data : postdata,
            async : false,
            datatype: "json",
            success: function(response){
                //console.log(response);
                var json = jQuery.parseJSON(response);

                if(json.Message!='OK')
                {
                    jQuery("#sendButton").show();
                    jQuery("#loading").hide();
                    alert(json.Message);
                    return false
                }else{
                    //alert(json['row_info'].agree_current_sts_id); 
                                var row = {};  
                                row["id"] = json['row_info'].id;
                                row["agreement_ref_no"] = json['row_info'].agreement_ref_no;
                                row["fin_ref_no"] = json['row_info'].fin_ref_no;
                                row["landlord_names"] = json['row_info'].landlord_names;
                                row["location_name"] = json['row_info'].location_name;
                               // row["agree_cost_center"] = json['row_info'].agree_cost_center;
                                row["location_owner"] = json['row_info'].location_owner;
                                row["cost_center"] = json['row_info'].cost_center;
                                row["rent_start_dt"] = json['row_info'].rent_start_dt;
                                row["agree_exp_dt"] = json['row_info'].agree_exp_dt;
                                row["point_of_payment"] = json['row_info'].point_of_payment;
                                row["monthly_rent"] = json['row_info'].monthly_rent;
                                row["total_advance"] = json['row_info'].total_advance;
                                row["total_advance_paid"] = json['row_info'].total_advance_paid;
                                
                                row["sts"] = json['row_info'].sts;
                                
                                row["dept_v_by"] = json['row_info'].dept_v_by;
                                row["fin_v_by"] = json['row_info'].fin_v_by;
                                row["stf_by"] = json['row_info'].stf_by;
                                row["ack_by"] = json['row_info'].ack_by;
                                row["halt_by"] = json['row_info'].halt_by;
                                row["rhalt_by"] = json['row_info'].rhalt_by;
                                row["close_by"] = json['row_info'].close_by;
                                row["close_release_by"] = json['row_info'].close_release_by;
                                row["agree_current_sts_id"] = json['row_info'].agree_current_sts_id;
                                row["agree_pervious_sts_id"] = json['row_info'].agree_pervious_sts_id;
                                row["agr_current_sts"] = json['row_info'].agr_current_sts;
                               // row["dept_v_sts"] = json['row_info'].dept_v_sts; 



                                window.parent.jQuery("#jqxgrid").jqxGrid('clearselection');
                                <? if($add_edit=='add'){?>                          
                                //alert('test');
                                var paginginformation = window.parent.jQuery("#jqxgrid").jqxGrid('getpaginginformation');                           
                                var insert_index=paginginformation.pagenum*paginginformation.pagesize;                           
                                var commit = window.parent.jQuery("#jqxgrid").jqxGrid('addrow', null, row,insert_index);                            
                                window.parent.jQuery("#jqxgrid").jqxGrid('selectrow', insert_index);                            
                                <? }else{?>
                                                            
                                jQuery.each(row, function(key,val){

                                    window.parent.jQuery("#jqxgrid").jqxGrid('setcellvalue', <?=$editrow?>, key, row[key]);                         
                                });
                                window.parent.jQuery("#jqxgrid").jqxGrid('selectrow', <?=$editrow?>);                           
                                <? }?>


                    window.parent.jQuery("#error").show();
                        window.parent.jQuery("#error").fadeOut(11500);
                        window.parent.jQuery("#error").html('<img align="absmiddle" src="'+baseurl+'images/drag.png" border="0" /> &nbsp; Saved successfully');
                        window.top.EOL.messageBoard.close();

                }
            }
        });
}

function preview_window(counter)
        {       
            // alert(counter);
            // jQuery("#vendor_counter"+count).val(counter);
            //window.open('<?=base_url()?>index.php/vendor/from/add/NULL/NULL/'+counter,'_blank','resizable=yes, top=100, left=200, width=1150, scrollbars=yes, height=800');
            window.open('<?=base_url()?>index.php/vendor/from/add/null/null/'+counter,'_blank','resizable=yes, top=100, left=200, width=1150, scrollbars=yes, height=800');
            //javascript:win=window.open('<?=base_url()?>index.php/vendor/from/add','_blank','resizable=yes, top=100, left=200, width=1150, scrollbars=yes, height=800');
            
            return false;  
            // EOL.messageBoard.open('<?=base_url()?>index.php/rent_add_advance/from/advance_verify/'+id+'/'+row, (jQuery(window).width()-70), jQuery(window).height(), 'yes');            
            // return false;           
        }

function edit_preview_window(counter)
        {  

          var id = jQuery("#at"+counter).attr('serial'); 
          if(id==''){
                alert('Please Add New Landlord');
                return false;
          }else{
            window.open('<?=base_url()?>index.php/vendor/from/edit/'+id+'/null/'+counter,'_blank','resizable=yes, top=100, left=200, width=1150, scrollbars=yes, height=800');
            return false;
          }
             
                    
        }

function load_vendor_code(id,counter){

        
        jQuery.ajax({
                url: '<?php echo base_url(); ?>index.php/agreement/get_vendor_list',
                type: "post",
                data : {id: id},
                datatype: 'json',
                async: false,
                success: function(response){
                  var json = jQuery.parseJSON(response); 
                  //alert(json);
                    if(json!=''){
                         //jQuery(".vendor_cls").jqxComboBox({ source: json, width: 180, height: 28,autoDropDownHeight: false})
                         jQuery("#vendor_id"+counter).jqxComboBox({ source: json, width: 280, height: 28,autoDropDownHeight: false})
                         jQuery("#at"+counter).show();
                         jQuery("#at"+counter).attr('serial',id);
                         
                         jQuery("#vendor_id"+counter).val(id);
                         jQuery('#form2').jqxValidator('validateInput', '#vendor_id1');
                    }else{
                        alert('Vendor Type Should be Rent');
                    }
              
                },
                error:   function(model, xhr, options){
                    alert('failed');
                }
             }); 
        windowonclose();
       
    }
//change_credit_sts('credit_sts' + count_landlord, '' + count_landlord);

 function change_credit_sts(id, count) {

        jQuery("#" + id).bind('select', function(event) {
            var item = jQuery("#" + id).jqxComboBox('getSelectedItem');
          //  alert(item);

          if (item.value == 'no') {
                  
                  jQuery("#amount_percentage"+count).val('0.00');
                     jQuery("#amount_percentage"+count).prop('disabled', true);
                 
                } 
                else {
                 jQuery("#amount_percentage"+count).prop('disabled', false);
                }
              
             });

        }

         function change_location_type(id, count) {

            jQuery("#" + id).bind('select', function(event) {
                var item = jQuery("#" + id).jqxComboBox('getSelectedItem');
        
          if(item){
                  if (item.value == '1') {
                        jQuery("#branch_id"+count).show();
                         jQuery("#atm_id"+count).hide();
                         jQuery("#sme_id"+count).hide();
                         jQuery("#godown_id"+count).hide();
                         jQuery("#dept_id"+count).hide();
                        
                    } else if(item.value == '2'){
                     
                        jQuery("#branch_id"+count).hide();
                       
                        jQuery("#dept_id"+count).hide();
                        
                    }
                    else if(item.value == '3'){
                       
                        jQuery("#branch_id"+count).hide();
                     
                        jQuery("#dept_id"+count).hide();
                        //jQuery("#division_id"+count).hide();
                    }
                    else if(item.value == '4'){
                        jQuery("#sme_id"+count).hide(); 
                        jQuery("#branch_id"+count).hide();
                        jQuery("#atm_id"+count).hide();
                        jQuery("#godown_id"+count).show();
                        jQuery("#dept_id"+count).hide();
                         //jQuery("#division_id"+count).hide();
                    }
                    else if(item.value == '5'){
                        jQuery("#sme_id"+count).hide(); 
                        jQuery("#branch_id"+count).hide();
                        jQuery("#atm_id"+count).hide();
                        jQuery("#godown_id"+count).hide();
                        jQuery("#dept_id"+count).show();
                        // jQuery("#division_id"+count).show();
                    }
                    else {
                        jQuery("#branch_id"+count).hide();
                        jQuery("#atm_id"+count).hide();
                        jQuery("#sme_id"+count).hide();
                        jQuery("#godown_id"+count).hide();
                        jQuery("#dept_id"+count).hide();
                        //jQuery("#division_id"+count).hide();
                    }
                }

             });

        }

        function delete_row(i)
        {
            jQuery('#delete' + i).val('1');
            jQuery('#tr' + i).hide();

        }
        function delete_row_landlord(i)
        {
     
            jQuery('#delete_landlord' + i).val('1');
            jQuery('#tr_landlord' + i).hide();

        }
        
        function delete_row_landlord_type(i)
        {
           
            jQuery('#delete_landlord_type' + i).val('1');
            jQuery('#tr_landlord_type' + i).hide();

        }      

        function delete_others_rent_type(i)
        {
            
            jQuery('#delete_others' + i).val('1');
            jQuery('#others_rent_tr' + i).hide();

        }

        function file_check()
        {
            <? if ($add_edit == 'add') { ?>

                    if (jQuery("#file_check").val() == 0)
                    {
                        alert('File seleciton is mandatory !!');
                        return '1';
                        jQuery("#file_check").focus();
                    }
                    else {
                        return '0';
                    }
            <? } else { ?>
                            
                    if (jQuery("#enlisted").prop('checked') && jQuery("#file_check").val() == 0)
                    {
                        alert('Select File Please !!');
                        return '1';
                        jQuery("#file_check").focus();
                    }
                    else {
                        return '0';
                    }
            <? } ?>


        }


        //23 january

        function cost_center_change_edit(id, count) {


            var item = jQuery("#cost_center1" ).jqxComboBox('getSelectedItem');
            var count_location_type = (jQuery("#counter_location_type").val());

            if (item) {

                jQuery.ajax({
                    url: '<?php echo base_url(); ?>index.php/agreement/get_child_list',
                    type: "post",
                    data: {cost_center: item.value},
                    datatype: 'json',
                    async: false,
                    success: function(response) {

                        var json = jQuery.parseJSON(response);

                        jQuery("#mis_code" + count).jqxComboBox({source: json});
                    },
                    error: function(model, xhr, options) {
                        alert('failed');
                    }
                });
            }

        }


// 4 jun
    function adjustment_on_exp_date_change() {
                if (jQuery('#adjust_adv_type :selected').val() == '2') {
                        
                    jQuery('#percentage_basis_adj').hide();
                    jQuery('#percent_amt_tr').hide();
                    jQuery('#calculated_percent_amt_tr').hide();
                    jQuery('#year_basis_adj').hide();
                    jQuery('#yearly_adj_type_tr').hide();
                    jQuery("#yearly_adj_data_table").html('');
                    jQuery("#fixed_amt_tr").show();
                }
                else if (jQuery('#adjust_adv_type :selected').val() == '3') {
                    jQuery('#percentage_basis_adj').show();
                  // jQuery('#percentage_basis_adj').prop('selectedIndex',0);
                    jQuery("#yearly_adj_data_table").html('');

                    jQuery('#year_basis_adj').hide();
                    jQuery('#yearly_adj_type_tr').hide();
                    jQuery("#fixed_amt_tr").hide();
                }
                else if (jQuery('#adjust_adv_type :selected').val() == '4') {
                            jQuery("#yearly_adj_data_table").html('');
                            jQuery('#yearly_adj_type_tr').show();
                            jQuery('#year_basis_adj').show();
                            jQuery('#percent_amt_tr').hide();
                            jQuery('#calculated_percent_amt_tr').hide();
                            jQuery('#percentage_basis_adj').hide();
                            jQuery("#fixed_amt_tr").hide();
                        if(jQuery('#yearly_adj_type').val() == ''){

                        }else if(jQuery('#yearly_adj_type :selected').val()!=''){

         
                            var yearly_seleted_option = jQuery('#yearly_adj_type :selected').val();
                            var exp = jQuery('#agree_exp_dt').val();
                            var start = jQuery('#rent_start_dt').val();
         
                            if (exp != '' && start != '') {

                                var postdata = {
                                    'start': start, 'exp': exp, 'yearly_seleted_option':yearly_seleted_option
                                };
                                jQuery.ajax({
                                    type: "POST",
                                    // async:false,
                                    cache: false,
                                    url: "<?= base_url() ?>index.php/agreement/yearly_adjust_amt",
                                    data: postdata,
                                    datatype: "html",
                                    success: function(response) {
                                        jQuery("#yearly_adj_show_data").show();
                                        if (response != "") {

                                            jQuery("#yearly_adj_data_table").html(response).show();
                                        }
                                    }

                                });
                            } else {
                                alert('Agreement Start and expiry date is needed !');
                            }
          

                            }else{

                            }
            
                } else {
                    jQuery('#percentage_basis_adj').hide();
                    jQuery('#percent_amt_tr').hide();
                    jQuery('#calculated_percent_amt_tr').hide();
                    jQuery('#year_basis_adj').hide();
                    jQuery("#yearly_adj_data_table").html('');
                    jQuery("#fixed_amt_tr").hide();
                    jQuery("#yearly_adj_type_tr").hide();
                }
    }

    function cost_center_change_edit_for_others(id, count) {


            var item = jQuery("#cost_center1" ).jqxComboBox('getSelectedItem');
            var count_location_type = (jQuery("#counter_others_rent_type").val());

            if (item) {

                jQuery.ajax({
                    url: '<?php echo base_url(); ?>index.php/agreement/get_child_list',
                    type: "post",
                    data: {cost_center: item.value},
                    datatype: 'json',
                    async: false,
                    success: function(response) {

                        var json = jQuery.parseJSON(response);
                        jQuery("#others_mis_code" + count).jqxComboBox({source: json});
                    },
                    error: function(model, xhr, options) {
                        alert('failed');
                    }
                });
            }

    }
 


    function cost_center_change(id, count) {

            jQuery("#" + id).on('select', function(event) {
                
                var args = event.args;

                if (args) {
                    
                    var item = args.item;
                    if (item) {
                        jQuery.ajax({
                            url: '<?php echo base_url(); ?>index.php/agreement/get_child_list',
                            type: "post",
                            data: {cost_center: item.value},
                            datatype: 'json',
                            success: function(response) {

                                var json = jQuery.parseJSON(response);
                                jQuery(".mis_code").jqxComboBox('clearSelection');
                                jQuery(".mis_code").jqxComboBox({source: json});

                                jQuery(".others_mis_code").jqxComboBox('clearSelection');
                                jQuery(".others_mis_code").jqxComboBox({source: json});
                            },
                            error: function(model, xhr, options) {
                                alert('failed');
                            }
                        });
                    }

                }
            });
    }


    function cost_center_select() {

            
                var item = jQuery("#cost_center1" ).jqxComboBox('getSelectedItem');
            
                    if (item) {
                        jQuery.ajax({
                            url: '<?php echo base_url(); ?>index.php/agreement/get_child_list',
                            type: "post",
                            data: {cost_center: item.value},
                            datatype: 'json',
                            success: function(response) {

                                var json = jQuery.parseJSON(response);
                                jQuery(".mis_code").jqxComboBox('clearSelection');
                                jQuery(".mis_code").jqxComboBox({source: json});

                                jQuery(".others_mis_code").jqxComboBox('clearSelection');
                                jQuery(".others_mis_code").jqxComboBox({source: json});
                            },
                            error: function(model, xhr, options) {
                                alert('failed');
                            }
                        });
                    }
          
    }
        
          // march 12

    function form_validate(){


        var counter = jQuery('#counter_landlord').val();
        var i=0;
        if( jQuery('#location_owner :selected').val()=='rented'){     
            for(var k=1;k<=counter; k++){
              
                    if( jQuery("#delete_landlord"+k).val()==0){

                            if(jQuery('#vendor_id'+k).val()==''){
                                alert('Landlord is empty');
                                return false;
                            }else 
                            {   
                                   //return true;
                            }

                            if(jQuery('#credit_sts'+k).val()=='' ){
                                alert('Credit is empty row '+k);
                                return false;
                            }else 
                            {   
                                   //return true;
                            }

                              if(jQuery('#amount_percentage'+k).val()==''){
                         
                                return false;
                            }else 
                            {   
                                   //return true;
                            }
                    }
                    
                 i++;

                }

                if( jQuery('#increment_type :selected').val()==''){

                    alert('No Increment Type is selected');
                    return false;

                }

                if( jQuery('#adjust_adv_type :selected').val()==''){

                    alert('No Adjustment Type is selected');
                    return false;

                }
                if(jQuery('#total_advance').val()=='' && jQuery('#adjust_adv_type :selected').val()!=1){
                    alert('Total Advance Amount is needed!');
                    return false;
                }

            
        }
        return true;
    }

   
    function own_chack(){

            if( jQuery('#location_owner :selected').val()=='own'){ 
            jQuery('#jqxTabs').jqxTabs('disableAt', 2);
            jQuery('#jqxTabs').jqxTabs('disableAt', 3);

                return false;}
            else{ return true;}
    }

    function location_owner_check()
    {
        var location_owner = jQuery('#location_owner').val();
        if (jQuery('#location_owner').val() == 'own') {
                           jQuery('#jqxTabs').jqxTabs('disableAt', 2);
                           jQuery('#jqxTabs').jqxTabs('disableAt', 3);
                        }
                        else {
                          jQuery('#jqxTabs').jqxTabs('enableAt', 2);
                        jQuery('#jqxTabs').jqxTabs('enableAt', 3);
                        }
        return true;
    }

// jun 12

    function landlords_option_chk(cur_id)
    {

        var counter = jQuery('#counter_landlord').val();
        var sum1 = 0;

        jQuery("#vendor_id"+cur_id).change(function() {
            
            var new_select_option =  jQuery(this).val();
       
           
                for(var i=1;i<=jQuery('#counter_landlord').val();i++)
                {

                    if( jQuery("#delete_landlord"+i).val()==0)
                    {
                        if(i!=cur_id) {
                            if(jQuery("#vendor_id"+i).val()== new_select_option && jQuery("#vendor_id"+i).val()!='')
                            {
                                     alert('Duplicate Landlord!');
                                     jQuery(this).val('');
                                         
                            } 
                            else 
                            { 
                             
                            }
                       }                     
                         
                    }
                }
        }); 
                      
     //   return true
    }


     function duplicate_mis_check(cur_id)
    {

        var counter = jQuery('#counter_location_type').val();
        var sum1 = 0;

        jQuery("#mis_code"+cur_id).change(function() {
            var new_select_option =  jQuery(this).val();
                        
                for(var i=1;i<=jQuery('#counter_location_type').val();i++)
                {

                    if( jQuery("#delete_landlord_type"+i).val()==0)
                    {
                        
                        if(i!=cur_id) {
                            if(jQuery("#mis_code"+i).val()== new_select_option)
                            {
                                 alert('Duplicate MIS Code !');
                                 jQuery(this).val('');
                                 jQuery("#mis_code"+cur_id).jqxComboBox({ selectedIndex: -1 });
                                 return false;
                                         
                            } 
                            else 
                            { 
                            }
                       }                     
                        
                    }
                }
        }); 
                      
     //   return true
    }

    function location_type_option_chk(cur_id)
    {

        var counter = jQuery('#counter_location_type').val();
        var sum1 = 0;

        jQuery("#location_type"+cur_id).change(function() {
            var new_select_option =  jQuery(this).val();
                        
                for(var i=1;i<=jQuery('#counter_location_type').val();i++)
                {

                    if( jQuery("#delete_landlord_type"+i).val()==0)
                    {
                        
                        if(i!=cur_id) {
                            if(jQuery("#location_type"+i).val()== new_select_option)
                            {
                                                 alert('Duplicate Location Type !');
                                                 jQuery(this).val('');
                                         
                            } 
                            else 
                            { 
                            }
                       }                     
                        
                    }
                }
        }); 
                      
     //   return true
    }

    function others_location_type_option_chk(cur_id)
    {
        var counter = jQuery('#counter_others_rent_type').val();
           // alert(counter);

        jQuery("#rent_others_id"+cur_id).change(function() {
        var new_select_option =  jQuery(this).val();
       
            for(var i=1;i<=jQuery('#counter_others_rent_type').val();i++)
            {
                  
                if( jQuery("#delete_others"+i).val()==0)
                {

                    if(i!=cur_id) {
                        // if(jQuery("#rent_others_id"+i).val()== new_select_option)
                        // {
                        //      alert('Duplicate Others Location Type !');
                        //      jQuery(this).val('');
                        //      jQuery("#rent_others_id"+cur_id).jqxComboBox({ selectedIndex: -1 });
                        //      return false;
                                     
                        // } 
                        
                    }                     
                    
                }
            }
        }); 
                      
     //   return true
    }

    function others_location_duplicate_mis_chk(cur_id)
    {
        var counter = jQuery('#counter_others_rent_type').val();
         
        jQuery("#others_mis_code"+cur_id).change(function() {
        var new_select_option =  jQuery(this).val();
       
            for(var i=1;i<=jQuery('#counter_others_rent_type').val();i++)
            {
                  
                if( jQuery("#delete_others"+i).val()==0)
                {

                    if(i!=cur_id) {
                        if(jQuery("#others_mis_code"+i).val()== new_select_option)
                        {
                             alert('Duplicate MIS Code!');
                             jQuery(this).val('');
                             jQuery("#others_mis_code"+cur_id).jqxComboBox({ selectedIndex: -1 });
                             return false; 
                        } 
                        
                    }                     
                    
                }
            }
        }); 
                   
    }

// jun 15

    function tab_4_business_address_check()
    {

        if (jQuery('#location_owner :selected').val()=='rented' && jQuery('#increment_type').val() == '') {
            return false;
        }
        
                if (jQuery('#increment_type :selected').val() == '') {
                     
                     alert('Select Increment Type');
                     return false;

                }
                else if (jQuery('#increment_type :selected').val() == '1') {
                     
                     

                }
                else if (jQuery('#increment_type :selected').val() == '2') { 
                     // 
                
                    if(jQuery('#increment_every_yr_value').val() == ''){
                        alert('Year interval value is required');
                        return false;
                    }else{

                          var increment_every_yr_value_val = jQuery('#increment_every_yr_value').val();
                          var count_year = jQuery('#count_year').val();
                          if(parseInt(count_year) >= parseInt(increment_every_yr_value_val)){

                          }else{
                            alert('This value exceeds The number of Year');
                            return false;
                          }

                    }
                }
                else if (jQuery('#increment_type :selected').val() == '3') {

                    if(jQuery('#one_time_increment_yr_no').val() == ''){
                        alert('Single Year value is required');
                        return false;
                    }else{

                          var one_time_increment_yr_no_val = jQuery('#one_time_increment_yr_no').val();
                          var count_year = jQuery('#count_year').val();
                          if(parseInt(count_year) >= parseInt(one_time_increment_yr_no_val)){

                          }else{
                            alert('This value exceeds The number of Year');
                            return false;
                          }

                    }
                    
                    
                }else if (jQuery('#increment_type :selected').val() == '4') {
                        

                }else if (jQuery('#increment_type :selected').val() == '5') {
                    if(jQuery('#increment_start_dt_value').val() == ''){
                        alert('Increment Start Date is required');
                        return false;
                    }

                }

        // old
        var count_year = jQuery('#count_year').val();
        var element_number = jQuery('#element_number').val();
        var count = 1;
           for (var i = 0; i <= count_year; i++) {
                if(jQuery('#rent_amount_val'+i).val()==''){
                    alert('Monthly rent increment value is required');
                    jQuery("#rent_amount_val"+i).focus();
                    return false;
                    
                }
                    for (var j = 0; j < element_number; j++) {
                        
                           if(jQuery('#others_amount_val'+i+j).val()==''){
                           
                               alert('Others increment value is required ');
                               jQuery("#others_amount_val"+i+j).focus();
                               return false;
                           }

                    }
                    count++;
           }
         
        return true;
    }

// jun 13

    function tab_3_business_address_check()
    {
     
          if (jQuery('#adjust_adv_type :selected').val() == '2') {
              var fixed_amt_2 = jQuery("#fixed_amt").val();
              var monthly_rent = jQuery("#monthly_rent").val();

               if(jQuery("#fixed_amt").val()==''){
                   alert('Fixed Amount is required ');
                                 jQuery(this).focus();
                                  return false;
               }else if(parseFloat(fixed_amt_2) > parseFloat(monthly_rent)){
                 alert('Monthly Adjust Amount Can not Greater than Initial Monthly Rent ');
                                 jQuery("#fixed_amt").focus();
                                  return false;

               }
          } else if (jQuery('#adjust_adv_type :selected').val() == '3') {
               if (jQuery('#percentage_basis_adj').val() == '') {
                   
                   alert('Please select an option ');
                     return false;
               }else if(jQuery('#percentage_basis_adj :selected').val()=='percent_total_amt'){
                      var percent_amt_3 = jQuery("#percent_amt").val();
                      var monthly_rent = jQuery("#monthly_rent").val();
                      var total_advance = jQuery("#total_advance").val();
                      var calculated_monthly_adjust = total_advance * (percent_amt_3)/100;
                    if(parseFloat(calculated_monthly_adjust) > parseFloat(monthly_rent)){
                        alert('Monthly Adjust Amount Can not Greater than Initial Monthly Rent ');
                                 jQuery("#fixed_amt").focus();
                                 return false;

                    }


               }else{

                var percent_amt_3 = jQuery("#percent_amt").val();
                var monthly_rent = jQuery("#monthly_rent").val();

                   if(jQuery("#percent_amt").val()==''){
                       alert('Percent Amount is required ');
                          return false;
                   }else if(jQuery("#percent_amt").val() > 100){
                        alert('Percent Amount Can not exceed 100% ');
                          return false;
                   }
               }
          }else if (jQuery('#adjust_adv_type :selected').val() == '4') {
               if (jQuery('#yearly_adj_type').val() == '') {
                   alert('Please select an option ');
                     return false;
               }else{
                    if (jQuery('#yearly_adj_type :selected').val() == 'yearly_adj_percent') {
                         var monthly_rent = jQuery("#monthly_rent").val();
                        var yr_row_counter = jQuery('#yr_row_count').val() ;
                        var j=1;
                        for(var i=0;i<yr_row_counter;i++){
                            
                            if(jQuery('#yrly_adj_amt'+i).val()==''){
                                alert('Percent Amount is required at row '+j);
                                jQuery('#yrly_adj_amt'+i).focus();
                                return false;
                            }else if(jQuery('#yrly_adj_amt'+i).val() > 100){
                                 alert('Percent Amount Can not exceed 100% at row '+j);
                                 jQuery('#yrly_adj_amt'+i).focus();
                                 return false;
                            }else if(parseFloat(jQuery('#cal_month_adj_amt'+i).val()) >= parseFloat(monthly_rent)){
                                alert('Monthly Adjust Amount should be less than Monthly Rent in row '+j);
                                 jQuery('#yrly_adj_amt'+i).focus();
                                 return false;
                            }
                            j++;
                        }
                    }else{
                        //yearly fixed amt
                        var monthly_rent = jQuery("#monthly_rent").val();
                        var yr_row_counter = jQuery('#yr_row_count').val() ;
                        var j=1;
                        for(var i=0;i<yr_row_counter;i++){
                            
                            if(jQuery('#yrly_adj_amt'+i).val()==''){
                                alert('Fixed Adjustment Amount is required at row '+j);
                                jQuery('#yrly_adj_amt'+i).focus();
                                return false;
                            }else if(parseFloat(jQuery('#yrly_adj_amt'+i).val()) >= parseFloat(monthly_rent)){
                                alert('Monthly Adjust Amount should be less than Monthly Rent in row '+j);
                                 jQuery('#yrly_adj_amt'+i).focus();
                                 return false;
                            }

                            else{
                                
                            }
                            j++;
                        }
                    }
               }
          }

        return true
    }

// 29 aug

    function tab_1_owner_check()
    {

            if( jQuery('#location_owner :selected').val()=='own'){ 
            //alert('No  Increment applicable for Own Location !');
                jQuery('#jqxTabs').jqxTabs('disableAt', 1);
                jQuery('#jqxTabs').jqxTabs('disableAt', 2);
               // jQuery('#jqxTabs').jqxTabs('disableAt', 3);
                //jQuery('#jqxTabs').jqxTabs('enableAt', 3);

                jQuery('#total_advance_tr').hide();
                jQuery('#adjust_tbl').hide();
                jQuery('#yearly_adj_data_table').hide();

                return true;
            }
            else{ 
                jQuery('#total_advance_tr').show();
                jQuery('#adjust_tbl').show();
                jQuery('#yearly_adj_data_table').show();
                return true;
              
            }

    }

// jun 4

    function tab_1_business_address_check()
    { 
        
        var total_advance =  jQuery('#total_advance').val();
        var monthly_rent = jQuery('#monthly_rent').val();
        var others_rent = jQuery('#others_rent').val();
        if( jQuery('#location_owner :selected').val()=='rented'){ 

          if(jQuery('#adjust_adv_type :selected').val() !=1 && (jQuery('#total_advance').val()=='' || jQuery('#total_advance').val()==0)){
                alert("Total Advance Amount is required");
                jQuery('#total_advance').focus();
                return false;
          }
          if( parseFloat(monthly_rent) > parseFloat(total_advance) && jQuery('#adjust_adv_type :selected').val() !=1){
                alert("Monthly rent can not exceed Total Advance amount");
                //jQuery(this).val('');
                jQuery('#monthly_rent').focus();
                return false;
          }
        }

        var counter = jQuery('#counter_location_type').val();
        var count_others_rent_type = (jQuery("#counter_others_rent_type").val());
        var total_square_ft = jQuery('#total_square_ft').val();
        var sum1 = 0;
        var others_sum1 = 0;
        var tot_others_amount =0;
        var total_sft =0;
        var count_others =0;
        var loc_sum1=0;
        var loc_total_sft=0;
           
        for(var i=1;i<=counter;i++)
        {

            if( jQuery("#delete_landlord_type"+i).val()==0)
            {
                          
                if(jQuery("#location_type"+i).val()=='')
                {
                                     alert('Location Name is required at row '+i);
                                     jQuery('#jqxTabs').jqxTabs('disableAt', 2);
                                     jQuery('#jqxTabs').jqxTabs('disableAt', 3);
                                     jQuery('#jqxTabs').jqxTabs('disableAt', 4);
                                     jQuery(this).focus();

                                   return false;
                                   
                } 
                else 
                {
                                    
                  // if(jQuery("#mis_code"+i).val()=='')
                  //   {
                  //       alert('MIS Code is required at row ' +i);
                  //        jQuery('#jqxTabs').jqxTabs('disableAt', 2);
                  //        jQuery('#jqxTabs').jqxTabs('disableAt', 3);
                  //        jQuery('#jqxTabs').jqxTabs('disableAt', 4);
                        
                  //       jQuery("#mis_code"+i).focus();
                  //       return false
                  //   }   

                  if(jQuery("#square_ft"+i).val()=='')
                    {
                        alert('Sq.ft is required at row ' +i);
                         jQuery('#jqxTabs').jqxTabs('disableAt', 2);
                         jQuery('#jqxTabs').jqxTabs('disableAt', 3);
                         jQuery('#jqxTabs').jqxTabs('disableAt', 4);
                        
                        jQuery("#square_ft"+i).focus();
                        return false;
                    }        
                  
                  if(jQuery("#location_type_amount_percentage"+i).val()=='')
                    {
                        //alert('m');
                        alert('Amount is required at row '+i);
                        jQuery('#jqxTabs').jqxTabs('disableAt', 1);
                        jQuery('#jqxTabs').jqxTabs('disableAt', 2);
                        jQuery('#jqxTabs').jqxTabs('disableAt', 3);
                        jQuery('#jqxTabs').jqxTabs('disableAt', 4);
                        
                        //jQuery("#addrs_country"+i).jqxComboBox('focus');
                        return false;
                    }else{
                        
                            sum1 += Number(jQuery("#location_type_amount_percentage"+i).val());      
                            loc_sum1 += Number(jQuery("#location_type_amount_percentage"+i).val());      
                    }

                        total_sft= total_sft + +jQuery("#square_ft"+i).val();
                        loc_total_sft= loc_total_sft + +jQuery("#square_ft"+i).val();
                    
                }
            }
        }

       // others location
        for(var i=1;i<=count_others_rent_type;i++){
            if( jQuery("#delete_others"+i).val()==0)
            {

                if(jQuery("#rent_others_id"+i).val()!='')
                {
                    count_others++;
                    if(jQuery("#others_square_ft"+i).val()=='')
                    {
                        alert('Sq.ft is required at row '+i);
                        jQuery('#jqxTabs').jqxTabs('disableAt', 1);
                        jQuery('#jqxTabs').jqxTabs('disableAt', 2);
                        jQuery('#jqxTabs').jqxTabs('disableAt', 3);
                        jQuery('#jqxTabs').jqxTabs('disableAt', 4);
                        return false;
                    }

                    if(jQuery("#others_type_amount_percentage"+i).val()=='')
                    {
                        alert('Direct Amount is required at row '+i);
                        jQuery('#jqxTabs').jqxTabs('disableAt', 1);
                        jQuery('#jqxTabs').jqxTabs('disableAt', 2);
                        jQuery('#jqxTabs').jqxTabs('disableAt', 3);
                        jQuery('#jqxTabs').jqxTabs('disableAt', 4);
                        return false;
                    }

                         
                }else{ }

                tot_others_amount += Number(jQuery("#others_type_amount_percentage"+i).val()); 
                sum1 += Number(jQuery("#others_type_percentage"+i).val()); 
                others_sum1 += Number(jQuery("#others_type_percentage"+i).val()); 
                total_sft= total_sft + +jQuery("#others_square_ft"+i).val();
            }
        }
         total_square_ft = parseFloat(total_square_ft).toFixed(2);
         total_sft = parseFloat(total_sft).toFixed(2);
         loc_total_sft = parseFloat(loc_total_sft).toFixed(2);
    
       // alert(loc_sum1);
       // alert(sum1);
        // alert(total_square_ft);
        // alert(loc_total_sft);
        // alert(total_sft); 

       if(count_others > 0){

            if(parseFloat(total_sft)!=parseFloat(total_square_ft)){
                alert('Total Square Feet should be equal to Total Area');
                return false;
            }

            // if(Math.round(sum1)!=100) {
            //     alert('Location and Other Location Percent amount should be 100%');
            //     return false;  
            // } 

            if(Math.round(loc_sum1)!=100) {
                alert('Location Type Percent amount should be 100%');
                return false;  
            } 

            if(Math.round(others_sum1)!=100) {
                alert('Other Location Percent amount should be 100%');
                return false;  
            } 

       }else{
//change here

            if(parseFloat(loc_total_sft)!=parseFloat(total_square_ft)){
                alert('Total Square Feet should be equal to Total Area');
                return false;
            }

            if(Math.round(loc_sum1)!=100) {
                alert('Location Percent amount should be 100%');
                return false;
                 
            }

       }
       

        if (jQuery("#others_rent").val().trim().length === 0) {
            others_rent=0;
        }   

        if(count_others > 0 && parseFloat(tot_others_amount)!=parseFloat(others_rent)){
            alert('Total Others Rent should be equal to Total Direct Amount');
            return false;
       }

        // new 20 jun
        if(jQuery('#location_owner :selected').val() == 'rented'){

                  if (jQuery('#adjust_adv_type :selected').val() == '2') {
                  var fixed_amt_2 = jQuery("#fixed_amt").val();
                  var monthly_rent = jQuery("#monthly_rent").val();

                   if(jQuery("#fixed_amt").val()==''){
                       alert('Fixed Adjustment Amount is required ');
                                     jQuery(this).focus();
                                      return false;
                   }else if(parseFloat(fixed_amt_2) > parseFloat(monthly_rent)){
                     alert('Monthly Adjust Amount Can not Greater than Initial Monthly Rent ');
                                     jQuery("#fixed_amt").focus();
                                      return false;

                   }
              } else if (jQuery('#adjust_adv_type :selected').val() == '3'){
                   if (jQuery('#percentage_basis_adj').val() == '') {
                       
                        alert('Please select an option ');
                         return false;
                   }else if(jQuery('#percentage_basis_adj :selected').val()=='percent_total_amt'){
                          var percent_amt_3 = jQuery("#percent_amt").val();
                          var monthly_rent = jQuery("#monthly_rent").val();
                          var total_advance = jQuery("#total_advance").val();
                          var calculated_monthly_adjust = total_advance * (percent_amt_3)/100;

                          if(jQuery("#percent_amt").val()==''){
                           alert('Percent Amount is required ');
                            jQuery("#percent_amt").focus();
                              return false;
                       }else if(parseFloat(calculated_monthly_adjust) > parseFloat(monthly_rent)){
                            alert('Monthly Adjust Amount Can not Greater than Initial Monthly Rent ');
                                     jQuery("#fixed_amt").focus();
                                     return false;

                        }else{

                        }


                   }else{

                    var percent_amt_3 = jQuery("#percent_amt").val();
                    var monthly_rent = jQuery("#monthly_rent").val();

                       if(jQuery("#percent_amt").val()==''){
                           alert('Percent Amount is required ');
                              return false;
                       }else if(jQuery("#percent_amt").val() > 100){
                            alert('Percent Amount Can not exceed 100% ');
                              return false;
                       }
                   }
              }else if (jQuery('#adjust_adv_type :selected').val() == '4'){
                   if (jQuery('#yearly_adj_type').val() == '') {
                        alert('Please select an option ');
                        return false;
                   }else{
                        if (jQuery('#yearly_adj_type :selected').val() == 'yearly_adj_percent') {
                            var monthly_rent = jQuery("#monthly_rent").val();
                            var yr_row_counter = jQuery('#yr_row_count').val() ;
                            var total_advance = jQuery('#total_advance').val();
                            var j=1;
                            var sum_2=0;
                            for(var i=0;i<yr_row_counter;i++){
                                sum_2 += Number(jQuery("#cal_year_adj_amt"+i).val());

                                if(jQuery('#yrly_adj_amt'+i).val()==''){
                                    alert('Percent Amount is required at row '+j);
                                    jQuery('#yrly_adj_amt'+i).focus();
                                    return false;
                                }else if(jQuery('#yrly_adj_amt'+i).val() > 100){
                                    alert('Percent Amount Can not exceed 100% at row '+j);
                                    jQuery('#yrly_adj_amt'+i).focus();
                                    return false;
                                }else if(parseFloat(jQuery('#cal_month_adj_amt'+i).val()) >= parseFloat(monthly_rent)){
                                    alert('Monthly Adjust Amount should be less than Monthly Rent in row '+j);
                                    jQuery('#yrly_adj_amt'+i).focus();
                                    return false;
                                }else if( parseFloat(sum_2) >= parseFloat(total_advance)){
                                 
                                    alert('Total Yearly Adjust Amount should be less than Advance Amount');
                                 
                                    return false;
                                }
                                j++;
                            }
                        }else{
                            //yearly fixed amt
                            var monthly_rent = jQuery("#monthly_rent").val();
                            var yr_row_counter = jQuery('#yr_row_count').val();
                            var month_counter = jQuery('#last_iteration').val();
                            var total_advance = jQuery('#total_advance').val();
                            var final_adj_amount=0;
                            //sum_2 += Number(jQuery("#cal_yrly_adj_amt"+i).val());

                            var j=1;
                            for(var i=0;i<yr_row_counter;i++){
                                sum_2 += Number(jQuery("#cal_yrly_adj_amt"+i).val());
                                
                                if(jQuery('#yrly_adj_amt'+i).val()==''){
                                    alert('Fixed Adjustment Amount is required at row '+j);
                                    jQuery('#yrly_adj_amt'+i).focus();
                                    return false;
                                }else if(parseFloat(jQuery('#yrly_adj_amt'+i).val()) >= parseFloat(monthly_rent)){
                                    alert('Monthly Adjust Amount should be less than Monthly Rent in row '+j);
                                     jQuery('#yrly_adj_amt'+i).focus();
                                     return false;
                                }else if( parseFloat(sum_2) >= parseFloat(total_advance)){
                                 
                                    alert('Total Yearly Adjust Amount should be less than Advance Amount');
                                 
                                    return false;
                                }

                                else{
                                    
                                    
                                }
                                j++;
                            }
                        }
                   }
              }          

        }
        
            return true;
    }

// march 13
    function business_address_check()
    {
        //alert('sdd');
        var counter = jQuery('#counter_landlord').val();
        var sum1 = 0;
        var sum_adv = 0;
            for(var i=1;i<=counter;i++)
            {

                if( jQuery("#delete_landlord"+i).val()==0)
                {
                            
                    if(jQuery("#vendor_id"+i).val()=='')
                    {
                         alert('Landlord Name is required at row '+i);
                         jQuery('#jqxTabs').jqxTabs('disableAt', 2);
                         jQuery('#jqxTabs').jqxTabs('disableAt', 3);
                         jQuery('#jqxTabs').jqxTabs('disableAt', 4);
                         return false;
                              
                    } 
                    else 
                    {
                                        
                                     
                        if(jQuery("#credit_sts"+i).val()=='')
                        {
                            alert('Credit Status is required at row ' +i);
                             jQuery('#jqxTabs').jqxTabs('disableAt', 2);
                             jQuery('#jqxTabs').jqxTabs('disableAt', 3);
                             jQuery('#jqxTabs').jqxTabs('disableAt', 4);
                            
                            jQuery("#credit_sts"+i).focus();
                            return false
                        }
                        else if(jQuery("#amount_percentage"+i).val()=='')
                        {
                            alert('Rent Percentage Amount is required at row '+i);
                            jQuery('#jqxTabs').jqxTabs('disableAt', 2);
                            jQuery('#jqxTabs').jqxTabs('disableAt', 3);
                            jQuery('#jqxTabs').jqxTabs('disableAt', 4);
                            return false;
                        }else if(jQuery("#advance_amount_percentage"+i).val()=='')
                        {
                            alert('Advance Percentage Amount is required at row '+i);
                            jQuery('#jqxTabs').jqxTabs('disableAt', 2);
                            jQuery('#jqxTabs').jqxTabs('disableAt', 3);
                            jQuery('#jqxTabs').jqxTabs('disableAt', 4);
                            return false;
                        }else{
                                if(parseInt(jQuery("#amount_percentage"+i).val())==0 && jQuery("#credit_sts"+i).val()!='No'){
                                    alert('Rent Percentage is Zero at row '+i);
                                    return false;
                                }else{

                                      sum1 += Number(jQuery("#amount_percentage"+i).val());
                                      sum_adv += Number(jQuery("#advance_amount_percentage"+i).val());
                                }
                            
                            }
                        
                    }
                }
            }

                        if(sum1!=100){  
                         alert('Rent Percent amount should be 100%');
                         return false;
                          
                        } 

                        if(sum_adv!=100){ 
                         alert('Advance Percent amount should be 100%');
                         return false;
                          
                        } 

                        if( jQuery('#location_owner :selected').val()=='own'){ 
                            alert('No Increment applicable for Own Location !');
                            jQuery('#jqxTabs').jqxTabs('disableAt', 1);
                            jQuery('#jqxTabs').jqxTabs('disableAt', 2);
                                // jQuery('#jqxTabs').jqxTabs('disableAt', 2);
                                // jQuery('#jqxTabs').jqxTabs('disableAt', 3);
                            jQuery('#jqxTabs').jqxTabs('enableAt', 3);

                            return false;
                        }
                        else{ 
                            
                        }

        //}
        return true
    }


    function increment_ajax(){


        var postdata = jQuery('#form1').serialize();
        jQuery.ajax({
                    type: "POST",
                    // async:false,
                    cache: false,
                    url: "<?= base_url() ?>index.php/agreement/increment_ajax",
                    data: postdata,
                    datatype: "html",  
                    success: function(response) {
                        jQuery("#show_data").show();
                        if (response != "") {

                            jQuery("#data_table").html(response).show();
                            jQuery(".incr_per_val").prop('disabled', true);
                            jQuery("#increment_tbl").hide();

                              increment_selected_option();
                           
                        }
                    }

                });
                 return true;
    }

    function increment_selected_option(){

            if (jQuery('#increment_type :selected').val() == '1') {
                 
                 jQuery('#one_time_increment_tr').hide();
                 jQuery('#every_yr_increment_tr').hide();
                 jQuery('#increment_start_tr').hide();
                 jQuery("#increment_tbl").hide();
                 var count_year = jQuery("#count_year").val();
                   
                 for (var i = 0; i <= count_year; i++) {

                        jQuery("#increment_tr"+i).hide();                   
                 }
                 jQuery("#incr_start_dt").val('');

            }
            else if (jQuery('#increment_type :selected').val() == '2') {
                    
                jQuery("#increment_tbl").hide();
                jQuery('#every_yr_increment_tr').show();
                jQuery('#one_time_increment_tr').hide();
                jQuery('#increment_start_tr').hide();
                jQuery('#increment_every_yr_value').val('');
                jQuery("#incr_start_dt").val('');
            }
            else if (jQuery('#increment_type :selected').val() == '3') {

                jQuery("#increment_tbl").hide();
                jQuery('#every_yr_increment_tr').hide();
                jQuery('#increment_start_tr').hide();
                jQuery('#one_time_increment_tr').show();
                jQuery('#one_time_increment_yr_no').val('');
                jQuery("#incr_start_dt").val('');
                
            }
            else if (jQuery('#increment_type :selected').val() == '4') {
                
                 jQuery('#one_time_increment_tr').hide();
                 jQuery('#every_yr_increment_tr').hide();
                 jQuery('#increment_start_tr').hide();
                 jQuery("#incr_start_dt").val('');
                // show all rows
                jQuery("#increment_tbl").show();
                 var count_year = jQuery("#count_year").val();
                   
                 for (var i = 0; i <= count_year; i++) {

                        jQuery("#increment_tr"+i).show();                   
                 }

            }else if(jQuery('#increment_type :selected').val() == '5'){

                 
                 jQuery('#every_yr_increment_tr').hide();
                 jQuery('#increment_start_tr').show();
                 jQuery('#one_time_increment_tr').hide();
                 jQuery("#increment_tbl").show();
                 jQuery("#increment_start_dt_value").show();
                 var count_year = jQuery("#count_year").val();
                   
                 for (var i = 0; i <= count_year; i++) {

                        jQuery("#increment_tr"+i).show();                   
                 }

            }else {
                 jQuery("#increment_tbl").hide();
                 jQuery('#every_yr_increment_tr').hide();
                 jQuery('#increment_start_tr').hide();
                 jQuery('#one_time_increment_tr').hide();
                 jQuery("#incr_start_dt").val('');

            }

    }

//march 14

    function incr_adjust_check()
    {
   
            var counter = jQuery('#count_year').val();
            for(var i=0;i<=counter;i++)
            {
                var j=1;
                
                    if(jQuery("#increment_percentage"+i).val()=='')
                    {
                        jQuery('#jqxTabs').jqxTabs('disableAt', 4);
                         alert('Increment value is required at row '+j);
                         
                       return false;
                                       
                    } 
                   
                j++;
            }
       
        return true
    }
        // 29 feb

    function openCIBfiles(custid)
    {
        if(jQuery('.check_checkbox' + custid).is(":checked")){
        
        newwindow = window.open("<?= base_url() ?>index.php/agreement/ajaxFileUpload_edit/" + custid + '/1', "Upload", "width=520,height=500,resizable=0,scrollbars=0,location=no,menubar=no,toolbar=no,minimizable=no,status=no,top=140,left=300");
        if (window.focus) {
            newwindow.focus()
        }
        return false;
        }else {
            alert('Please check the Checkbox');
            return false;
        }
    }
        // 26 january
        function file_type_check()
        {
          
            for (i = 0; i < 5; i++) {
                var file = jQuery("#file" + i).val();

                if (file != '')
                {
                    var fileType = file.substr(file.lastIndexOf('.') + 1).toUpperCase();
                    if (fileType == "DOC" || fileType == "XLS" || fileType == "PDF" || fileType == "XLSX" || fileType == "DOCX") {
                        return true;
                    } else {
                        alert("Invalid File type (Only doc/xls/pdf allowed) !!!")
                        jQuery("#agreement_upload").focus();
                        return false;
                    }
                } else {
                    return true;
                }

            }
        }

   function windowonclose(){
      //window.parent.windowonclose(win);
      window.close();
    }
    </script>

    <!--   <form name="form" id="form" class="form" action="#" method="post" > -->
   <!--  <form class="form" id="form" method="post" action="<?= base_url() ?>index.php/agreement/add_edit_action/<?= $add_edit ?>/<?= $id ?>" enctype='multipart/form-data'> -->
    <form class="form" id="form1" method="post" action="#" >
        <div  style=" width:100%; margin:auto">

            <div id='jqxWidget' style="width:100% !important;">
                <div id='jqxTabs' style="height:auto !important;">
                    <ul>
                        <li style="margin-left: 30px;">Basic info</li>
                        <li>Landlords</li>
                        <!-- <li>Advance Adjustment</li> -->
                        <li>Rent and Increment</li>
                        <li>Document Upload</li>
                    </ul>
                    <!-- <form name="form" id="form" class="form" action="#" method="post" > -->
                    <div>
                        <!--  Basic info -->

                        <table class="register-table"  width="60% !important;">
                            <!-- <tr>
                                            <td><input name="id" type="hidden" id="id" value="<?php echo isset($agreement->id) ? $agreement->id : NULL; ?>"  class="text-input-small" /></td>
                                            </tr>  -->
                            <tr>
                                <td style=" width:30%;">Agreement Reference Number<span style="color:#FF0000">*</span></td>
                              
                                <td>  <? if($add_edit=='edit'){ echo $agreement->agreement_ref_no;} else { echo ' Auto Generated'; }?> </td>
<!--                                <td><input type="text"  name="agreement_ref_no" id="agreement_ref_no" class="text-input-small" value="<?php echo isset($agreement->agreement_ref_no) ? $agreement->agreement_ref_no : ''; ?>" /></td>-->
                            </tr> 
                    <?php if($this->session->userdata['user']['user_department_id']==4 || $this->session->userdata['user']['user_id']==1){ ?>
                            <tr>
                                <td style=" width:30%;">Finance Reference Number</td>
                                <td><? if($add_edit=='edit'){ echo $agreement->fin_ref_no;} else { echo ' Auto Generated'; }?> </td>
                            </tr>
                    <?php } ?>

                            <tr>
                                <td style=" width:30%;">Location Name<span style="color:#FF0000">*</span></td>
                                <td><input type="text"  name="location_name" id="location_name" class="text-input-small" value="<?php echo isset($agreement->location_name) ? $agreement->location_name : ''; ?>" /></td>
                            </tr>

                            <tr>

                                <td>Total Area<span style="color:#FF0000">*</span></td>
                                <td><input type="text" name="total_square_ft" id="total_square_ft" class="text-input-small number" value="<?php echo isset($agreement->total_square_ft) ? $agreement->total_square_ft : ''; ?>" /> (Sq. ft)
                                <input type="hidden"  name="incr_start_dt" id="incr_start_dt" class="text-input-small" value="<?php echo isset($agreement->incr_start_date) ? $agreement->incr_start_date : ''; ?>" /></td>
                                <input type="hidden"  name="incr_type" id="incr_type" class="text-input-small" value="" /></td>
                            </tr>


                            <tr>
                                <td>Address</td>
                                <td><textarea class="textarea" style="width:177px;" name="location_address"  id="location_address"><?php echo isset($agreement->location_address) ? $agreement->location_address : ''; ?></textarea></td>
                                
                            </tr>

                            <tr>
                             <td>Division </td>
                             <td>   
                                    <select name="location_division" class="select-input-small" id="location_division">
                                        <option value="">Select One</option>
                                        <option <?php echo (isset($agreement->location_division) && $agreement->location_division == "Dhaka") ? 'selected="selected"' : ''; ?> value="Dhaka">Dhaka</option>
                                        <option <?php echo (isset($agreement->location_division) && $agreement->location_division == "Chittagong") ? 'selected="selected"' : ''; ?> value="Chittagong">Chittagong</option>
                                        <option <?php echo (isset($agreement->location_division) && $agreement->location_division == "Sylhet") ? 'selected="selected"' : ''; ?> value="Sylhet">Sylhet</option>
                                        <option <?php echo (isset($agreement->location_division) && $agreement->location_division == "Khulna") ? 'selected="selected"' : ''; ?> value="Khulna">Khulna</option>
                                        <option <?php echo (isset($agreement->location_division) && $agreement->location_division == "Barisal") ? 'selected="selected"' : ''; ?> value="Barisal">Barisal</option>
                                        <option <?php echo (isset($agreement->location_division) && $agreement->location_division == "Rajshahi") ? 'selected="selected"' : ''; ?> value="Rajshahi">Rajshahi</option>
                                    </select>
                                </td>
                             </tr>

                            <tr>
                                <td>Rent Start Date<span style="color:#FF0000">*</span></td>
                                <td><input type="text" name="rent_start_dt" id="rent_start_dt" class="text-input-small"   value="<?php echo isset($agreement->rent_start_dt) ? $agreement->rent_start_dt : ''; ?>" /></td>
                            </tr> 

                            <tr>
                                <td>Agreement Expired Date<span style="color:#FF0000">*</span></td>
                                <td><input type="text"  name="agree_exp_dt" id="agree_exp_dt"  class="text-input-small" value="<?php echo isset($agreement->agree_exp_dt) ? $agreement->agree_exp_dt : ''; ?>" /></td>
                                <td>
                               <!--  <input type="button" value="Test" class="schedule_btn" id="schedule_btn" onClick="generate_schedule_data()" />  -->
                                </td>
                            </tr>

                            <tr>
                                <td>Point of Payment <span style="color:#FF0000">*</span></td>
                                <td>
                                   <select name="point_of_payment" class="select-input-small" id="point_of_payment">
                                        <option value="">Select One</option>
                                        <option <?php echo (isset($agreement->point_of_payment) && $agreement->point_of_payment == "pm") ? 'selected="selected"' : ''; ?> value="pm">Following Month</option>
                                        <option <?php echo (isset($agreement->point_of_payment) && $agreement->point_of_payment == "cm") ? 'selected="selected"' : ''; ?> value="cm">Current Month</option>
                                    </select>
                                </td>
                            </tr>


                            <tr>
                                <td>Location Ownership (Own/Rented) <span style="color:#FF0000">*</span></td>
                                <td>
                                    <select name="location_owner" class="select-input-small" id="location_owner">
                                        <option value="">Select One</option>
                                        <option <?php echo (isset($agreement->location_owner) && $agreement->location_owner == "own") ? 'selected="selected"' : ''; ?> value="own">Own</option>
                                        <option  selected="selected" value="rented">Rented</option>
                                    </select>
                                </td>
                            </tr>

                      
                            <tr>

                                <td>Cost Center <span style="color:#FF0000">*</span></td>
                                <td ><div id="cost_center1" name="cost_center1"></div></td>
                                
                            </tr>
                 

                            <tr id="monthly_rent_tr">
                                <td>Initial Monthly Rent <span style="color:#FF0000">*</span></td>
                                <td><input type="text" name="monthly_rent" id="monthly_rent" class="text-input-small number" value="<?php echo isset($agreement->monthly_rent) ? $agreement->monthly_rent : ''; ?>" onblur="cost_sft(1)"/></td>
                          
                            </tr>

                            <tr id="others_rent_tr">
                                <td>Total Others Rent </td>
                                <td><input type="text" name="others_rent" id="others_rent" class="text-input-small number" value="<?php echo isset($agreement->others_rent) ? $agreement->others_rent : ''; ?>" onblur="amount_percent_others(1)"/></td>
                            </tr>
                    
                            <tr id="total_tr">
                                <td>Total Amount</td>
                                <td>&nbsp;&nbsp;<div id="Amount_Span" style="
                                        box-sizing: content-box;    
                                        width: 170px;
                                        height: 16px;
                                        padding: 2px;    
                                        border: 2px solid gray;
                                        color:#060; font-weight:bold">0</div></td>
                          
                            </tr>

                        </table>

<!-- new  -->
                

                        <div class="custom_css" style="margin: 15px 0; width:100% ;background-color: Gainsboro ; height:25px; color: blue;"><center>Location Types</center></div>
                        <center><div style="color:#060; font-weight:bold; margin-right: 25px; font-size: 14px;">Total Sq.ft: <span id="Sq_Amount_Span">0</span>   <span style="color:red">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Location Amount (%):</span> <span id="percent_Amount_Span" style="color:red">0</span></div></center>

                        <div style="margin:10px 20px;" >
                            <input name="counter_location_type" type="hidden" id="counter_location_type" value="1" />
                            <table class="innerTable" id="location_type_table" cellspacing="0" cellpadding="5" border="0" style="border-collapse:collapse">
                                <thead>
                                    <tr class="headrow">
                                        <th width="2%"  valign="top" >Delete</th>
                                        <th width="15%" valign="top"  style="display: none;">Location Type <span style="color:#FF0000">*</span>:</th>
                                        <th width="5%" valign="top" >Location List<span style="color:#FF0000">*</span></th>
                                   
                                        <th width="5%" valign="top" >MIS</th>
                                        <th width="5%" valign="top" >Vat </th>
                                        <th width="5%" valign="top" >Tax </th>
                                        
                                        <th width="5%" valign="top" > Sq.Ft<span style="color:#FF0000">*</span></th>
                                        <th width="5%" valign="top" > Cost/Month</th>
                                        <th width="5%" valign="top" > Amount(%)<span style="color:#FF0000">*</span></th>
                                        

                                    </tr>
                                </thead>
                                <tbody>
                                    <tr id="tr_landlord_type1" style="border-top: 1px solid black !important; text-align:center;">
<!-- <td align="center" width="2%" ><img src="<?= base_url() ?>images/del.png" onclick="delete_row_landlord_type(' + count_location_type + ')"><input type="hidden" name="delete' + count_location_type + '" id="delete_landlord_type' + count_location_type + '" value="0"><input type="hidden" name="existing' + count_location_type + '" id="existing' + count_location_type + '" value="0"><input type="hidden" name="id' + count_location_type + '" id="id' + count_location_type + '" value=""></td> -->
                                        <td width="2%" align="center">

                                         <!-- <img src="<?= base_url() ?>images/del.png" onclick="delete_row_landlord_type(1)"> -->
                                            <input type="hidden" name="delete1" id="delete_landlord_type1" value="0"><input type="hidden" name="existing1" id="existing1" value="0"><input type="hidden" name="id1" id="id1" value="">
                                        
                                        </td>
                                        <!-- <td width="15%"><input name="cost_center1"  id="cost_center1"   /></td> 
                                        <td width="15%"><input name="mis_code1" type="text" class="name" id="mis_code1" value=""  style="width:130px"  /></td>-->
                                        <td width="15%">
                                            <div id="location_type1" name="location_type1" style="margin-left:30px;" class="location_type_cls"></div>


                                        </td>
                                        <td width="15%" style="display: none;">
                                            <div id="branch_id" name="branch_id1"></div>
                                            <div id="atm_id" name="atm_id1"></div>
                                            <div id="sme_id" name="sme_id1"></div>
                                            <div id="godown_id" name="godown_id1"></div>
                                            <div id="dept_id" name="dept_id1"></div> <br />
                                            <div id="division_id" name="division_id1"></div>

                                        </td>

                                        <td width="15%"><div id="mis_code1" name="mis_code1" class="mis_code" style="margin-left:30px;"></div></td>
                                        <td width="5%"><div id="loc_vat_sts1" name="loc_vat_sts1" class="loc_vat_sts1 " style="margin-left:5px;"></div></td>
                                        <td width="5%"><div id="loc_tax_sts1" name="loc_tax_sts1" class="loc_tax_sts1 " style="margin-left:5px;"></div></td>
                         
                                        <td width="15%"><input name="square_ft1" type="text" class="name flags number" id="square_ft1" value=""  style="width:130px"  onblur="cost_sft(1)" onkeyup="cal_total_sft(1)" /></td>
                                        <td width="15%"><input name="cost_sft1" type="text" class="name flags number" id="cost_sft1" value=""  style="width:130px"  readonly/></td>
                                        <!-- onblur="amount_percent_per_cost(1)" -->
                                        <td width="15%"><input name="location_type_amount_percentage1" type="text" class="name flags number1 ll_type_amount" id="location_type_amount_percentage1" value=""  style="width:130px"  onblur="calculate_cost_by_percent(1)" onkeyup="cal_total_percent(1)"/></td>
                                        
                                    </tr>

                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="10" align="right"><img src="<?= base_url() ?>images/addmore.png" onclick="addmore_location_type()"></td>
                                    </tr>
                                </tfoot>
                            </table>

                        </div>


                      <!--   Location Others Details -->
                      <div class="custom_css" style="margin: 10px 0; width:100% ;background-color: white ; height:16px; color: blue;"><center>Others Location Types</center></div>
                        <div style="margin:10px 20px;" >
                            <input name="counter_others_rent_type" type="hidden" id="counter_others_rent_type" value="1" />
                            <table id="others_type_table" class="innerTable" cellspacing="0" cellpadding="5" border="0" style="border-collapse:collapse">
                                <thead>
                                    <tr class="headrow">
                                        <th width="2%"  valign="top" >Delete</th>
                                        <th width="10%" valign="top" >Others Location Type </th>
                                        <th width="5%" valign="top" >MIS </th>
                                        <th width="5%" valign="top" >Vat </th>
                                        <th width="5%" valign="top" >Tax </th>
                                        <th width="5%" valign="top" > Direct Amount</th>
                                        <th width="5%" valign="top" > Sq.Ft</th>
                                        <th width="5%" valign="top" > Cost/Month</th>
                                        <th width="5%" valign="top" > Amount(%)<span style="color:#FF0000">*</span></th>
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr id="others_rent_tr1" style="border-top: 1px solid black !important; text-align:center;">
                                        
                                        <td width="2%" align="center">&nbsp;<input type="hidden" name="delete_others1" id="delete_others1" value="0"><input type="hidden" name="existing_others1" id="existing_others1" value="0"><input type="hidden" name="id1" id="id1" value=""></td>
                                        <td width="10%">
                                            <div id="rent_others_id1" name="rent_others_id1" style="margin-left:10px;"></div>

                                        </td>
                    
                             
                                        <td width="5%"><div id="others_mis_code1" name="others_mis_code1" class="mis_code others_mis_code" style="margin-left:5px;"></div></td>
                                        <td width="5%"><div id="vat_sts1" name="vat_sts1" class="vat_sts1 " style="margin-left:5px;"></div></td>
                                        <td width="5%"><div id="tax_sts1" name="tax_sts1" class="tax_sts1 " style="margin-left:5px;"></div></td>
                         
                                        <td width="5%"><input name="others_type_amount_percentage1" type="text" class="name flags number " id="others_type_amount_percentage1" value=""   onblur="amount_percent_others(1)" style="width:120px"  /></td>
                                        <td width="5%"><input name="others_square_ft1" type="text" class="name flags number" id="others_square_ft1" value=""  style="width:120px"   onkeyup="cal_total_sft(1)"/></td>
                                        
                                        <td width="5%"><input name="others_cost_sft1" type="text" class="name flags number " id="others_cost_sft1" value=""  style="width:120px"  readonly/></td>
                                        <!-- onblur="others_amount_percent_per_cost(1)" -->
                                        <td width="5%"><input name="others_type_percentage1" type="text" class="name flags number " id="others_type_percentage1" value=""  style="width:120px"  onblur="calculate_cost_by_others_percent(1)"   onkeyup="cal_total_percent(1)"/></td>
                                        
                                    </tr>

                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="10" align="right"><img src="<?= base_url() ?>images/addmore.png" onclick="addmore_others_rent_type()"></td>
                                    </tr>
                                </tfoot>
                            </table>

                        </div>
                    <div class="custom_css" style="margin: 15px 0; width:100% ;background-color: Gainsboro ; height:25px; color: blue;"><center>Advance Adjustment Setup</center></div>
                        <table class="register-table"  width="60% !important;" id="adjust_tbl">

                            <tr id="total_advance_tr">

                                <td>Total Advance Amount </td>
                                <td><input type="text" name="total_advance" id="total_advance" class="text-input-small number" value="<?php echo isset($agreement->total_advance) ? $agreement->total_advance : ''; ?>" /></td>
                            </tr>

                            <tr>
                                <td style=" width:30%;">Advance Adjustment Method <span style="color:#FF0000">*</span></td>
                                <td>
                                    <select name="adjust_adv_type" class="" id="adjust_adv_type">
                                        <option value="">Select an Adjustment Type</option>
                                        <option <?php echo (isset($agreement->adjust_adv_type) && $agreement->adjust_adv_type == "1") ? 'selected="selected"' : ''; ?> value="1">No Adjustment</option>
                                        <option <?php echo (isset($agreement->adjust_adv_type) && $agreement->adjust_adv_type == "2") ? 'selected="selected"' : ''; ?> value="2">Fixed adjustment amount</option>
                                        <option <?php echo (isset($agreement->adjust_adv_type) && $agreement->adjust_adv_type == "3") ? 'selected="selected"' : ''; ?> value="3">Percentage (%) basis</option>
                                        <option <?php echo (isset($agreement->adjust_adv_type) && $agreement->adjust_adv_type == "4") ? 'selected="selected"' : ''; ?> value="4">Single Year Basis</option>
                                       

                                    </select>
                                </td>
                            </tr>

                            <tr>
                                <td style=" width:30%;"> </td>
                                <td>
                                    <select name="percentage_basis_adj" class="select-input-small" id="percentage_basis_adj">
                                        <option value="">Select an Option</option>
                                       <!--  <option <?php echo (isset($agreement->percentage_basis_adj) && $agreement->percentage_basis_adj == "percent_paid_amt") ? 'selected="selected"' : ''; ?> value="percent_paid_amt">% of present advance amount that is paid already (For entire agreement period) </option> -->
                                        <option <?php echo (isset($agreement->percentage_basis_adj) && $agreement->percentage_basis_adj == "percent_total_amt") ? 'selected="selected"' : ''; ?> value="percent_total_amt">% of total advance amount (For entire agreement period)</option>

                                    </select>
                                </td>
                            </tr>


                            <tr id="percent_amt_tr">
                                <td style=" width:30%;"> Enter Amount (%) </td>
                                <td>
                                    <input name="percent_amt" class="text-input-small number" id="percent_amt" value="<?= isset($result->ref_no) ? $result->ref_no : '' ?>"  class="text-input-small" /> / Month
                                </td>
                            </tr>

                             <tr id="calculated_percent_amt_tr">
                                <td style=" width:30%;"> Calculated Amount </td>
                                <td>
                                    <input name="calculated_percent_amt" class="text-input-small number" id="calculated_percent_amt" value="<?= isset($result->ref_no) ? $result->ref_no : '' ?>"  class="text-input-small" readonly/> / Month
                                </td>
                             </tr>

                             <tr id="fixed_amt_tr">
                                <td style=" width:30%;"> Enter Amount (Fixed) </td>
                                <td>
                                    <input name="fixed_amt"  class="text-input-small number" id="fixed_amt" value=""  onblur="cal_month_no()" class="text-input-small" /> / Month
                                </td>
                            </tr> 

                            <tr id="month_no_tr">
                                <td style=" width:30%;"> Enter Month No </td>
                                <td>
                                    <input name="fixed_month"  class="text-input-small number" id="fixed_month" value=""  onblur="cal_per_month_adv()" class="text-input-small" /> 
                                </td>
                            </tr>
                            
                            

                            <tr id="yearly_adj_type_tr">
                                <td  style=" width:30%;"> </td>
                                <td>
                                    <select name="yearly_adj_type" class="select-input-small" id="yearly_adj_type">
                                        <option value="">Select an Option</option>
                                        <option <?php echo (isset($agreement->yearly_adj_type) && $agreement->yearly_adj_type == "yearly_adj_percent") ? 'selected="selected"' : ''; ?> value="yearly_adj_percent"> Percentage (%) </option>
                                        <option <?php echo (isset($agreement->yearly_adj_type) && $agreement->yearly_adj_type == "yearly_adj_fixed") ? 'selected="selected"' : ''; ?> value="yearly_adj_fixed">Fixed Amount</option>


                                    </select>
                                </td>
                            </tr>

                </table>


                        <div style="width:100%;" id="yearly_adj_data_table">


                        </div>

                        <table class=""  width="98% !important;">
                           
                            <tr>
                                <td> </td>

                                <td>
                                    <div id="sectionButtonsWrapper">
                                        <input type="button" value="Next" class="nextButton" id="nextButtonInfo" onClick="increment_ajax()"  />
                                        <!-- <center>  <input type="button" value="Increment Schedule" class="schedule_btn" id="schedule_btn" onClick="increment_ajax()" /></center> -->
                                    </div>
                                </td>
                            </tr>


                        </table>

</form>

                    </div>



                    <div>
  
                        <!-- Cost Center Segregation -->
                        

                        <!-- Land Lords -->
                        <div class="custom_css" style="margin: 15px 0; width:100% ;background-color: Gainsboro ; height:25px; color: blue;"><center>Land Lords</center></div>
                        <div style="margin:20px;" >
                <form class="form" id="form2" method="post" action="#" >
                            <input name="counter_landlord" type="hidden" id="counter_landlord" value="1" />
                            <table id="land_lord_table" cellspacing="0" cellpadding="5" border="0" style="border-collapse:collapse">
                                <thead>
                                    <tr style="border-bottom: 1px solid black !important;background-color:#C7BFBB;">
                                        <th width="5%"  valign="top" align="left">Delete</th>
                                        <th width="2%"  valign="top" align="center">Add | Edit</th>
                                        <th width="15%" valign="top" align="left">Land Lord <span style="color:#FF0000">*</span>:</th>
                                        <th width="5%" valign="top" align="left">Credit Status <span style="color:#FF0000">*</span>:</th>
                                        <th width="15%" valign="top" align="left">Advance Percentage Amount<span style="color:#FF0000">*</span>:</th>
                                        <th width="15%" valign="top" align="left">Rent Percentage Amount<span style="color:#FF0000">*</span>:</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <tr id="tr_landlord1" style="border-top: 1px solid black !important;background-color:#E1C8C2;">
                                        
                                        <td width="2%" align="center">
                                           <!--  <img src="<?= base_url() ?>images/del.png" onclick="delete_row_landlord(1)"> -->
                                        &nbsp;<input type="hidden" name="delete_landlord1" id="delete_landlord1" value="0">

                                        <input type="hidden" name="existing1" id="existing1" value="0"><input type="hidden" name="id1" id="id1" value=""></td>
                                        <!-- <td width="5%" align="center"><a style="text-decoration: none; cursor:pointer;" onclick="javascript:win=window.open('<?=base_url()?>index.php/vendor/from/add','_blank','resizable=yes, top=100, left=200, width=1150, scrollbars=yes, height=800');windowonclose(win);return false;"><img src="<?= base_url() ?>images/plus.gif"></a></td> -->
                                        <td width="5%" align="center">
                                            <a style="text-decoration: none; cursor:pointer;" onclick="preview_window(1)"><img src="<?= base_url() ?>images/plus.gif"></a>
                                             &nbsp;&nbsp; <a style="text-decoration: none; cursor:pointer;" onclick="edit_preview_window(1)" id="at1" serial=""><img src="<?= base_url() ?>images/edit.png"></a>
                                        </td>
                                        <!-- <td width="15%"><input name="cost_center1"  id="cost_center1"   /></td> 
                                        <td width="15%"><input name="mis_code1" type="text" class="name" id="mis_code1" value=""  style="width:130px"  /></td>-->
                                        <td width="15%"><div id="vendor_id1" class="vendor_cls" name="vendor_id1"></div></td>
                                        <td width="15%"><div id="credit_sts1" name="credit_sts1"></div></td>

                                        <td width="15%">
                                            <input name="advance_amount_percentage1" type="text" class="name flags number adv_ll_amount" id="advance_amount_percentage1" value=""  style="width:130px"  />
                             
                                        </td>
                                        <td width="15%">
                                            <input name="amount_percentage1" type="text" class="name flags number ll_amount" id="amount_percentage1" value=""  style="width:130px"  />
                             
                                        </td>

                                    </tr>

                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="10" align="right"><img src="<?= base_url() ?>images/addmore.png" onclick="addmore_landlord()"></td>
                                    </tr>
                                </tfoot>
                            </table>

                            <table  cellspacing="0" cellpadding="5" border="0" >
                                <td>Tax Wived :</td>
                                <td>
                                   <select name="tax_wived" class="select-input-small" id="tax_wived">
                                        <option value="wived_yes">Yes</option>
                                        <option selected="selected" value="wived_no">No</option>
                                    </select>
                                </td>
                            </table>
                </form>
                        </div>
                        <div id="basketButtonsWrapper">
                            <input type="button" value="Back" class="backButton" id="backButtonBasket" />
                            <input type="button" value="Next" class="nextButton" id="nextButtonBasket" />
                        </div>

                    </div>



                    <div>
                        <!-- Rent Tab -->

                        <div class="custom_css" style="margin: 15px 0; width:100% ;background-color: Gainsboro ; height:25px; color: blue;"><center>Monthly Rent and Increment Setup</center></div>
                <form class="form" id="form4" method="post" action="#" >

                    <table class="register-table"  width="60% !important;">
                            <tr>
                                <td style=" width:25%;">Increment Type <span style="color:#FF0000">*</span></td>
                                <td>
                                    <select name="increment_type" class="" id="increment_type">
                                        <option value="">Select an Increment Type</option>
                                        <option <?php echo (isset($agreement->increment_type) && $agreement->increment_type == "1") ? 'selected="selected"' : ''; ?> value="1">No Increment</option>
                                        <option <?php echo (isset($agreement->increment_type) && $agreement->increment_type == "2") ? 'selected="selected"' : ''; ?> value="2">Every Year Basis</option>
                                        <option <?php echo (isset($agreement->increment_type) && $agreement->increment_type == "3") ? 'selected="selected"' : ''; ?> value="3">Only One Time</option>
                                        <option <?php echo (isset($agreement->increment_type) && $agreement->increment_type == "4") ? 'selected="selected"' : ''; ?> value="4">Fixed Increment Setup (Per Year Basis)</option>
                                        <option <?php echo (isset($agreement->increment_type) && $agreement->increment_type == "5") ? 'selected="selected"' : ''; ?> value="5">Manual Increment Setup </option>
                                       

                                    </select>
                                </td>
                                <td style=" width:25%;"><input type="button" value="Reset" class="" id="increment_reset" /></td>
                            </tr>


                            <tr id="one_time_increment_tr">
                                <td style=" width:25%;"> Only After </td>
                                <td>
                                    <input name="one_time_increment_yr_no" class="number" id="one_time_increment_yr_no" value=""  class="text-input-small" /> Year
                                </td>
                            </tr> 


                            <tr id="every_yr_increment_tr">
                                <td style=" width:25%;"> After Every </td>
                                <td>
                                    <input name="increment_every_yr_value" class="number" id="increment_every_yr_value" value=""  class="text-input-small" /> Year
                                </td>
                            </tr>

                            <tr id="increment_start_tr">
                                <td style=" width:25%;"> Increment Start Date</td>
                                <td>
                                    <input name="increment_start_dt_value" class="" id="increment_start_dt_value" value=""  class="text-input-small" />
                                </td>
                            </tr>


                    </table>

                        <div style="width:100%;" id="data_table">



                        </div>
                </form>
                        <div id="basketButtonsWrapper_rent">
                            <input type="button" value="Back" class="backButton" id="backButtonBasket_rent" />
                            <input type="button" value="Next" class="nextButton" id="nextButtonBasket_rent" />
                        </div>

                    </div>



                    <div>
                        <!--  Upload Document -->
                        <!--    <form class="form" id="form" method="post" action="<?= base_url() ?>index.php/agreement/rent_file_upload_action/<?= $add_edit ?>/<?= $id ?>" enctype='multipart/form-data'> -->
                        <td><input name="file_check" type="hidden" id="file_check" value="0" class="text-input-small" /></td>
                        <!-- <form class="form" id="form" method="post" action="#"> -->
                        <!-- <div class="formHeader">Document Upload Information </div> -->


<!-- old -->
                        <table class="register-table" width="60%">
                         <form class="form" id="form5" method="post" action="#" >

                           <tbody>
                            <tr>
                                <td>
                                  <table class="innerTable" border="1" cellpadding="5" cellspacing="0" style="width:99% !important; border-collapse:collapse;">
                           
                                    <tr class="headrow">
                                        <td width="30%">File Name</td>
                                       
                                        <td width="40%">Upload Files</td>
                                    </tr>
                            <tbody>

                             


                            <?php echo form_open_multipart('doc_upload/do_upload'); ?>
                            <?php $i = 0;
                            foreach ($rent_document_list as $row) { ?>
                                    <tr> <td>
                                            <input name="id" type="hidden" id="id" value="<?= isset($result->id) ? $result->id : '' ?>"  class="text-input-small" />
                                            <input type="checkbox" id="enlisted<?= $row->id ?>" class="rent_doc check_checkbox<?= $row->id ?>" value="<?php echo $row->id; ?>" name="enlisted" />
                                                 <?php echo $row->name; ?>
                                         </td>
                                        <td align="center" style="text-align: center;"><input type="hidden" value="<?php echo $row->id; ?>" name="doc_type_id<?= $i ?>">
                                        <!-- <input type="file" name="file<?= $i ?>" id="file<?= $i ?>" class="browse_btn" size="20" accept=""/> --> 
                                           <!--  <a href="#" id="check_checkbox"><img height="16" width="16" src="<?=base_url()?>images/arrow_up.png" style="vertical-align: middle; margin-right: 2px;" /></a> -->
                                            <input type="button" name="file<?= $i ?>" value="Upload" id="upButton" class="buttonStyle browse_btn" onClick="openCIBfiles('<?= $row->id ?>')" />
                                            <!-- <input type="button" value="Files" id="upButton" class="buttonStyle" onClick="openCIBfiles('<?= $cust_id ?>','<?= $lock_sts ?>','<?= $enqyr ?>','<?= $enqsl ?>')" /> -->
                                            <input type="hidden" value="" name="cust_cib_files" id="cust_cib_files" /><span id="file_list_view"><span id="filescount<?= $row->id ?>" style="font-size:13px; color:#006699" ></span></span>
                                            <input type="hidden" value="" id="lock_sts" name="lock_sts" />


                                        </td>
                                        <?php echo form_open_multipart('doc_upload/do_upload'); ?>
                                        <input type="hidden" value="<?= $i ?>" name="file_count">
                                      
                                    </tr> 

                            <?php $i++;
                            }
                            ?>

                            
            </tbody>
         
                            
        </form>
                        </table> 
                        </tbody>

                            </tr>
                                </td> 
                                <tr>
                                <td colspan="2" style="text-align: center"><br><input type="button" value="Save" id="sendButton" class="buttonStyle" /> <span id="loading" style="display:none">Please wait... <img src="<?= base_url() ?>images/loader.gif" align="bottom"></span></td>
                                <!-- <input type="submit" value="Save" id="sendButton" class="buttonStyle" /> <span id="loading" style="display:none">Please wait... <img src="<?= base_url() ?>images/loader.gif" align="bottom"></span></td> -->
                            </tr>
                        </table>  


                        <div id="selectedProductsButtonsWrapper">
                            <input type="button" value="Back" id="backButtonReview" class="backButton" />
                        </div>


                    </div> 
                    <!-- </form>   -->    
                </div>   
            

            </div>



        </div>
    </form>     

    <div id="window" >
        <div id="windowHeader">
            <span>
                Payment History
            </span>
        </div> 
        <div style="overflow: hidden;">
            <table border="1" id="data_table">
                <tr>
                    <td id="advance_amount_sch"> </td>
                    <td>Devling</td>
                    <td>Espresso Truffle</td>
                    <td>$1.75</td>
                    <td>8</td>
                </tr>
                <tr>
                    <td>Nancy</td>
                    <td>Wilson</td>
                    <td>Cappuccino</td>
                    <td>$5.00</td>
                    <td>3</td>
                </tr>
            </table>
            <tr><td colspan="2" style="text-align: center"><br><input type="button" value="Close" id="closeButton" class="buttonStyle" /></td></tr>

        </div>
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