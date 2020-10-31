<?
if ($add_edit == 'edit') {

    //$arr_vendor = explode(',',$result->vendor_id);
    $rent_ids = explode(',', $agreement->others_rent_types_ids);
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

jQuery(document).on("keypress",".number",function (evt) {
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46) 
           return false;
    
        return true;            
   });

jQuery(document).ready(function () {

        jQuery("#check_upload").hide();

        jQuery("#enlisted").click(function(){

            if (jQuery("#enlisted").prop('checked')){

            jQuery("#check_upload").show();

            } else{
                jQuery("#check_upload").hide();
            }

        });

        var credit_sts = [{value:"yes", label:"Yes"}, {value:"no", label:"No"}];
        var miscodelist = [];
                

  

        jQuery('#rent_advance_data').hide();
                jQuery('.year_basis').hide();
                jQuery('.monthly_rent_based').hide();
                jQuery('.rent_wise_change_vat').hide();
                jQuery('.rent_wise_change').hide();
                jQuery('.adj_year_basis').hide();
               
                var $j = jQuery.noConflict();
              
                jQuery('#window').jqxWindow({height: 500, maxWidth: 1150, width: 1150,autoOpen: false, zIndex: 99999});
                jQuery('#window2').jqxWindow({height: 400, maxWidth: 1200, width: 900,autoOpen: false,zIndex: 99999});
                val = 0;
                jQuery("#schedule").click(function(){
                var advance_amount_sch = $("#advance_amount").val();
                alert(advance_amount_sch);
                jQuery("#advance_amount_sch").append('<div>' + advance_amount_sch + '</div>');
                   
                jQuery('#window').jqxWindow('open');
                jQuery('#window2').jqxWindow('open');
        });
        jQuery("#agree_sendButton").click(function(){
     
        jQuery("#parent_combo").jqxComboBox("clearSelection");
                jQuery("#child_combo").jqxComboBox("clearSelection");
                jQuery("#parent_combo").jqxComboBox({ source: parentGl_list, width: '240', height: '24', promptText: "Select Parent GL"});
                jQuery("#parent_combo").keyup(function() {
        commbobox_check(jQuery(this).attr('id'));
        });
                val = 1;
                set_input_value(val);
                jQuery('#window').jqxWindow('open');
                jQuery('#window2').jqxWindow('open');
        });
               

                var theme = 'classic';

         
// search module

        jQuery("#agree_check_all").on('change', function(){

        if(this.checked) // if changed state is "CHECKED"
                {
                    jQuery('.payment_popup_chk').prop('checked', this.checked); 

                    var i=parseInt(jQuery('#sche_row_count').val(), 10);
                   
                    var row_serial = jQuery('#row_serial').val();
                    var new_total_payment1 = set_total_payment_for_check_all(i,row_serial);
                    jQuery('#total_payment').val(new_total_payment1.toFixed(2));   

                }
                else{

                     jQuery('.payment_popup_chk').removeAttr('checked');
                     jQuery('#total_payment').val(0.00); 
                }
        }); 

  // 26 aug 2018 start

        jQuery("#agree_check_all_cm").on('change', function(){

            

            if(this.checked) // if changed state is "CHECKED"
                {
                    // jQuery('.cls_cm').each(function() {

                    //     if ( jQuery(this).is(':visible') && jQuery(this).prop('checked') ) {

                    //          jQuery(this).prop('checked', true);
                    //          jQuery(this).find('.tr_cls_cm').show();
                    //     }
                    // });

                    jQuery('.cls_cm').prop('checked', this.checked); 
                    jQuery('#agree_check_all_pm').removeAttr('checked');
                    jQuery('#agree_check_all_stop').removeAttr('checked');
                    jQuery('.cls_pm').removeAttr('checked');
                    jQuery('.cls_stop').removeAttr('checked');
                    jQuery('.tr_cls_cm').show(); 
                    jQuery('.tr_cls_pm').hide(); 
                    jQuery('.tr_cls_stop').hide(); 

                    var i=parseInt(jQuery('#sche_row_count').val(), 10);
                   
                    var row_serial = jQuery('#row_serial').val();

                    var new_total_payment1 = set_total_payment_for_check_cm_pm('cm',row_serial);
                    jQuery('#total_payment').val(new_total_payment1.toFixed(2));   

                }
                else{

                    var new_total_payment1 = set_total_payment_for_check_cm_pm('cm',row_serial);
                  
                    jQuery('.cls_cm').removeAttr('checked');
                    jQuery('.tr_cls_pm').show(); 
                    jQuery('.tr_cls_stop').show(); 
                    var rem =  new_total_payment1; 
                    jQuery('#total_payment').val(rem.toFixed(2));
                }
        }); 

        jQuery("#agree_check_all_pm").on('change', function(){

        if(this.checked) // if changed state is "CHECKED"
                {
                    jQuery('.cls_pm').prop('checked', this.checked); 
                    jQuery('#agree_check_all_cm').removeAttr('checked');
                    jQuery('#agree_check_all_stop').removeAttr('checked');
                    jQuery('.cls_cm').removeAttr('checked');
                    jQuery('.cls_stop').removeAttr('checked');
                    jQuery('.tr_cls_pm').show(); 
                    jQuery('.tr_cls_cm').hide(); 
                    jQuery('.tr_cls_stop').hide(); 

                    var i=parseInt(jQuery('#sche_row_count').val(), 10);
                   
                    var row_serial = jQuery('#row_serial').val();

                    var new_total_payment1 = set_total_payment_for_check_cm_pm('pm',row_serial);
                    jQuery('#total_payment').val(new_total_payment1.toFixed(2));   

                }
                else{
                    var new_total_payment1 = set_total_payment_for_check_cm_pm('pm',row_serial);
                     jQuery('.cls_pm').removeAttr('checked');
                     jQuery('.tr_cls_cm').show(); 
                    jQuery('.tr_cls_stop').show(); 
                    var rem = new_total_payment1; 
                    jQuery('#total_payment').val(rem.toFixed(2));
                }
        });
  // 26 aug 2018 end
  // 2 oct start
  jQuery("#agree_check_all_stop").on('change', function(){

        if(this.checked) // if changed state is "CHECKED"
                {
                    jQuery('.cls_stop').prop('checked', this.checked); 
                    jQuery('#agree_check_all_pm').removeAttr('checked');
                    jQuery('#agree_check_all_cm').removeAttr('checked');
                    jQuery('.cls_cm').removeAttr('checked');
                    jQuery('.cls_pm').removeAttr('checked');
                    jQuery('.tr_cls_stop').show(); 
                    jQuery('.tr_cls_cm').hide(); 
                    jQuery('.tr_cls_pm').hide();

                    var i=parseInt(jQuery('#sche_row_count').val(), 10);
                   
                    var row_serial = jQuery('#row_serial').val();

                    var new_total_payment1 = set_total_payment_for_check_cm_pm('cm',row_serial);
                    jQuery('#total_payment').val(new_total_payment1.toFixed(2));   

                }
                else{

                    var new_total_payment1 = set_total_payment_for_check_cm_pm('cm',row_serial);
                  
                    jQuery('.cls_stop').removeAttr('checked');
                    jQuery('.tr_cls_cm').show(); 
                    jQuery('.tr_cls_pm').show();
                    var rem =  new_total_payment1; 
                    jQuery('#total_payment').val(rem.toFixed(2));
                }
        }); 

        jQuery('#cc_list').change(function() { 
            
            var itemsFound = jQuery(this).val(); 
            itemsFound = parseInt(itemsFound, 10);
            //iterate through each option
            jQuery('#t01 tr').each(function() {
              currentItem = parseInt(jQuery(this).attr("data-cc"), 10);
            
              //alert(itemsFound);
              if (currentItem > itemsFound) {
                jQuery(this).find('.cc_cls').removeAttr('checked');
                jQuery(this).hide();
                
                
              }else{
                jQuery(this).show();
                jQuery(this).find('.cc_cls').prop('checked', true); 
              }

            });

        });

       //var $rows = $('#table tr');
        jQuery('#search_cc').keyup(function() {
            var val = jQuery.trim(jQuery(this).val()).replace(/ +/g, ' ').toLowerCase();
            //alert(val);
            jQuery('#t01 tr').not('.headrow').show().filter(function() {
                var text = jQuery(this).text().replace(/\s+/g, ' ').toLowerCase();
                //var text = jQuery(this).attr("data-loc");
                //alert(text);
                return !~text.indexOf(val);
            }).hide();
        });

        jQuery(".payment_popup_chk_link").on('click', function(){
            var checkbox_serial= jQuery(this).attr('serial');
            if(jQuery("#agree_check"+checkbox_serial).prop('checked') == true ){ 
           
            var rent_agree_id = jQuery(this).attr('value');
            var row_serial = jQuery(this).attr('serial');
            var sche_ids = jQuery('#checked_schedule_id'+row_serial).val();
            var single_sche_id = sche_ids.split(',');
                   

             var prev_sd_amt = jQuery('#prev_sd_amount'+rent_agree_id).val();
             var total_payment = parseFloat(jQuery('#total_payment').val());
             var per_sche_net_payment =   parseFloat(jQuery('#per_sche_net_payment'+row_serial).val());
             var new_calculation = total_payment+per_sche_net_payment;
          

                     jQuery.ajax({
                        url: '<?php echo base_url(); ?>index.php/rent_schedule_payment/get_matured_schedule_info_for_payment',
                        type: "post",
                        data: { rent_id:rent_agree_id, row_serial:row_serial }, 
                       // datatype: 'json',
                       datatype: "html",
                        success: function(response){
                
                                if(response !=""){

                                        jQuery("#data_table").html(response).show();
                                }
                        }
                       
                    });

                        val=1;
                        var i=parseInt(jQuery('#sche_row_count').val());
                        sche_set_checked(i,row_serial); 
                        jQuery('#window').jqxWindow('open');
                      
                    
             setTimeout(function() {
                     jQuery('#new_sd_amount'+rent_agree_id).val(prev_sd_amt);
             }, 500); 
                  
            }else{
                alert('Please Check the Checkbox!');
            }
        }); 

        val=0;
      
            jQuery(".payment_popup_chk").on('change', function(){

            if(this.checked) // if changed state is "CHECKED"

            {
               
                var rent_agree_id = jQuery(this).attr('value');
                var row_serial = jQuery(this).attr('serial');
                var sche_ids = jQuery('#checked_schedule_id'+row_serial).val();
                var single_sche_id = sche_ids.split(',');
                       
                
                 var prev_sd_amt = jQuery('#prev_sd_amount'+rent_agree_id).val();
                 var total_payment = parseFloat(jQuery('#total_payment').val());
                 var per_sche_net_payment =   parseFloat(jQuery('#per_sche_net_payment'+row_serial).val());
                 var new_calculation = total_payment+per_sche_net_payment;
                 jQuery('#total_payment').val(new_calculation.toFixed(2));

                        
                            setTimeout(function() {
                                 jQuery('#new_sd_amount'+rent_agree_id).val(prev_sd_amt);
                                 if(prev_sd_amt !=''){
                                 var avg_adj= prev_sd_amt/ single_sche_id.length;

                                 for(var k=0;k<single_sche_id.length;k++){
                                        
                                         var res=  parseFloat(jQuery('#old_hidden_final_net_payment'+single_sche_id[k]).val()) - parseFloat(avg_adj.toFixed(2));
                                         
                                         jQuery('#final_net_payment'+single_sche_id[k]).text(res);
                                          //  alert(single_sche_id[k]);
                                       }

                                }

                 }, 1000);
                      
                }else{
                 var row_serial = jQuery(this).attr('serial');
                 var total_payment = jQuery('#total_payment').val();
                 var per_sche_net_payment =   jQuery('#per_sche_net_payment'+row_serial).val();
                 var new_calculation = total_payment - per_sche_net_payment;
                 jQuery('#total_payment').val(new_calculation.toFixed(2));
                }

            }); 


        jQuery("#sd_closeButton").click(function(){
            
                var i=parseInt(jQuery('#row_count').val(), 10);
                var sche_row_count = parseInt(jQuery('#sche_row_count').val(), 10);
                var row_serial = jQuery('#row_serial').val(); 
                var tax_percentage =parseFloat(jQuery('#tax_percentage').val()); 
                //alert(row_serial); 
                var rent_id = jQuery('#rent_id').val();   

                jQuery('#total_row').val(i);
                if(check_adjust_amount(i) && check_bill_select(i)){
                    
               var checked_sche_net_amt = per_sche_adj_on_sd_close(sche_row_count,row_serial);
               var comma_seperated_sche_chk_id = jQuery('#chk_sche_id_on_sd_close'+row_serial).val(); // comma seperated sche id
               if(comma_seperated_sche_chk_id==''){
                  alert('Select only Matured Schedule');
                  return false;
               }
               var sum =  set_adjust_amount(i,rent_id);

               var single_sche_chk_id = comma_seperated_sche_chk_id.split(',');
               var avg_adj= sum/ single_sche_chk_id.length;
               //alert(comma_seperated_sche_chk_id);         

                var sum_for_prov= 0;
                var prov_sche_ids= '';
                var non_prov_sche_ids= '';
                for(var k=0;k<single_sche_chk_id.length;k++){
                      // 22 sep
                      var net_payment_text = jQuery('#final_net_payment'+single_sche_chk_id[k]).text().trim();

                      if(net_payment_text=='-'){

                          var prov_text_amt = jQuery('#final_prov_payment'+single_sche_chk_id[k]).text().trim();
                          var adj_text_amt = jQuery('#final_adj_payment'+single_sche_chk_id[k]).text().trim();
                          var rest_amt_before_tax =  parseFloat(prov_text_amt) - parseFloat(adj_text_amt);
                          var prov_tax = (rest_amt_before_tax * tax_percentage)/100; 
                          var rest_amt_after_tax = rest_amt_before_tax - prov_tax;
                          sum_for_prov = sum_for_prov + rest_amt_after_tax;
                          if(prov_sche_ids!=''){prov_sche_ids +=',';}
                          prov_sche_ids += single_sche_chk_id[k];

                      }else{
                       if(non_prov_sche_ids!=''){non_prov_sche_ids +=',';}
                          non_prov_sche_ids += single_sche_chk_id[k];
                      }
                
                }


                var prov_single_sche_chk_id = prov_sche_ids.split(',');
                var non_prov_single_sche_chk_id = non_prov_sche_ids.split(',');
                var sum_for_non_prov= parseFloat(sum) - parseFloat(sum_for_prov);

                var prov_avg_adj= sum_for_prov/ prov_single_sche_chk_id.length;
                var non_prov_avg_adj= sum_for_non_prov/ non_prov_single_sche_chk_id.length;


                    if(parseFloat(avg_adj) < parseFloat(prov_avg_adj)){
                        for(var k=0;k<single_sche_chk_id.length;k++){
                                          // 22 sep
                                        var net_payment_text = jQuery('#final_net_payment'+single_sche_chk_id[k]).text().trim();
                                          
                                        if(net_payment_text=='-'){
                                            // var res=  parseFloat(jQuery('#old_hidden_final_prov_payment'+single_sche_chk_id[k]).val()) - parseFloat(avg_adj.toFixed(2));
                                            // jQuery('#final_prov_payment'+single_sche_chk_id[k]).text(res);
                                        }else{
                                            var res=  parseFloat(jQuery('#old_hidden_final_net_payment'+single_sche_chk_id[k]).val()) - parseFloat(avg_adj.toFixed(2));            
                                            jQuery('#final_net_payment'+single_sche_chk_id[k]).text(res.toFixed(2));
                                            jQuery('#hidden_final_net_payment'+single_sche_chk_id[k]).val(parseFloat(res));
                                            jQuery('.new_sche_net_amount'+single_sche_chk_id[k]).val(parseFloat(res));
                                        }
                                      
                                        jQuery('#avg_sd_payment'+single_sche_chk_id[k]).text(avg_adj.toFixed(2));
                                        jQuery('.prov_sd_amt'+single_sche_chk_id[k]).val(parseFloat(avg_adj.toFixed(2)));
                                        jQuery('.new_avg_sd_payment'+single_sche_chk_id[k]).val(parseFloat(avg_adj.toFixed(2)));
                        }

                    }else{
                        if(non_prov_sche_ids=='' && sum > sum_for_prov){
                             alert('Total SD amount '+sum_for_prov +' is allowed for selected provision');
                             return false;
                             
                        }

                        for(var k=0;k<single_sche_chk_id.length;k++){
                             var net_payment_text = jQuery('#final_net_payment'+single_sche_chk_id[k]).text().trim();
                           if(net_payment_text!='-'){
                                var res=  parseFloat(jQuery('#old_hidden_final_net_payment'+single_sche_chk_id[k]).val()) - parseFloat(non_prov_avg_adj.toFixed(2));  
                                if(parseFloat(res) < 0){
                                    alert('Security Deposit Can not be Greater than Total Net Payment');
                                    return false;
                                } 
                           }
                        }

                        for(var k=0;k<single_sche_chk_id.length;k++){
                          // 22 sep
                          var net_payment_text = jQuery('#final_net_payment'+single_sche_chk_id[k]).text().trim();
                          
                          
                              if(net_payment_text=='-'){
                                var res=  parseFloat(jQuery('#old_hidden_final_net_payment'+single_sche_chk_id[k]).val()) - parseFloat(prov_avg_adj.toFixed(2));            
                                

                                jQuery('#avg_sd_payment'+single_sche_chk_id[k]).text(prov_avg_adj.toFixed(2));
                                jQuery('.prov_sd_amt'+single_sche_chk_id[k]).val(parseFloat(prov_avg_adj.toFixed(2)));
                                jQuery('.new_avg_sd_payment'+single_sche_chk_id[k]).val(parseFloat(prov_avg_adj.toFixed(2)));

                              }else{
                                var res=  parseFloat(jQuery('#old_hidden_final_net_payment'+single_sche_chk_id[k]).val()) - parseFloat(non_prov_avg_adj.toFixed(2));  
                                       
                                jQuery('#final_net_payment'+single_sche_chk_id[k]).text(res.toFixed(2));
                                jQuery('#hidden_final_net_payment'+single_sche_chk_id[k]).val(parseFloat(res));
                                jQuery('.new_sche_net_amount'+single_sche_chk_id[k]).val(parseFloat(res));

                                jQuery('#avg_sd_payment'+single_sche_chk_id[k]).text(non_prov_avg_adj.toFixed(2));
                                jQuery('.prov_sd_amt'+single_sche_chk_id[k]).val(parseFloat(non_prov_avg_adj.toFixed(2)));
                                jQuery('.new_avg_sd_payment'+single_sche_chk_id[k]).val(parseFloat(non_prov_avg_adj.toFixed(2)));
                              }

                       }

                    }

                    jQuery('#new_sd_amount'+rent_id).val(sum);
                    jQuery('#prev_sd_amount'+rent_id).val(sum);
                    jQuery('#window2').jqxWindow('close');
                } 
                else{

                    }
        });
    
    
    
        jQuery("#sche_closeButton").click(function(){
         
                var i=parseInt(jQuery('#sche_row_count').val(), 10);
                var tax_percentage =parseFloat(jQuery('#tax_percentage').val());
              
                var sche_row_id = jQuery('#sche_row_id').val();   
                var row_serial = jQuery('#row_serial').val();   
                var rent_id = jQuery('#rent_id').val();   
                var new_sd_amount = jQuery('#new_sd_amount'+rent_id).val();   
                
                //alert(row_serial); 
                jQuery('#sche_total_row'+row_serial).val(i);
                if(check_sche_select(i,row_serial)){

                    jQuery('#agree_sche_check'+row_serial).val(1);

                    var sum =  sche_set_adjust_amount(i,sche_row_id,row_serial);
                    if(parseInt(sum) < 0){ return false; }
                    var updated_sd_amt_int = jQuery('#checked_updated_sd_int'+row_serial).val();
                    var difference = foo(new_sd_amount,updated_sd_amt_int);
                    if(difference > 1){
                        alert('Security Deposit amount is not fully adjusted !');
                        return false;
                    }
                  
                    var comma_seperated_sche_chk_id = jQuery('#checked_schedule_id'+row_serial).val();
                    var single_sche_chk_id = comma_seperated_sche_chk_id.split(',');

                    var sum_for_prov= 0;
                    var prov_sche_ids= '';
                    var non_prov_sche_ids= '';
                    var non_prov_net_payment= 0;
                    var non_prov_net_payment_sep= '';
                    for(var k=0;k<single_sche_chk_id.length;k++){
                          // 22 sep
                          var net_payment_text = jQuery('#final_net_payment'+single_sche_chk_id[k]).text().trim();

                          if(net_payment_text=='-'){

                              var prov_text_amt = jQuery('#final_prov_payment'+single_sche_chk_id[k]).text().trim();
                              var adj_text_amt = jQuery('#final_adj_payment'+single_sche_chk_id[k]).text().trim();
                              var rest_amt_before_tax =  parseFloat(prov_text_amt) - parseFloat(adj_text_amt);
                              var prov_tax = (rest_amt_before_tax * tax_percentage)/100; 
                              var rest_amt_after_tax = rest_amt_before_tax - prov_tax;
                              sum_for_prov = sum_for_prov + rest_amt_after_tax;
                              if(prov_sche_ids!=''){prov_sche_ids +=',';}
                              prov_sche_ids += single_sche_chk_id[k];

                          }else{
                           if(non_prov_sche_ids!=''){non_prov_sche_ids +=','; non_prov_net_payment_sep +='@';}
                              non_prov_sche_ids += single_sche_chk_id[k];
                              non_prov_net_payment += parseFloat(jQuery('#final_net_payment'+single_sche_chk_id[k]).text().trim());
                              non_prov_net_payment_sep += parseFloat(jQuery('#final_net_payment'+single_sche_chk_id[k]).text().trim());
                              
                          }
                    
                    }

                    var prov_single_sche_chk_id = prov_sche_ids.split(',');
                    var non_prov_single_sche_chk_id = non_prov_sche_ids.split(',');
                    var sum_for_non_prov= parseFloat(sum) - parseFloat(sum_for_prov);

                    var prov_avg_adj= sum_for_prov/ prov_single_sche_chk_id.length;
                    var non_prov_avg_adj= sum_for_non_prov/ non_prov_single_sche_chk_id.length;
                    
                    if(parseFloat(sum)< new_sd_amount){
                       
                    }
                    var per_sche_prov= jQuery('#per_sche_prov'+row_serial).val();
                    //alert(row_serial);
                 
                    var row_perv_net_payment= jQuery('#per_sche_net_payment'+row_serial).val();
                    var prev_total_payment= jQuery('#total_payment').val();
                    var hidden_per_sche_net_payment= jQuery('#hidden_per_sche_net_payment'+row_serial).val();
                    var hidden_total_payment= jQuery('#hidden_total_payment').val();
                   
                    var difference = hidden_per_sche_net_payment - sum ; 
                            // check sd amt and net amt
                            if(parseFloat(new_sd_amount) > parseFloat(hidden_per_sche_net_payment)){
                                alert('Security Deposit Can not be Greater than Total Net Payment ');
                                return false;
                            }else{
                               
                                var net_payment_after_sd = hidden_per_sche_net_payment - new_sd_amount - difference;
                            }
        
                var new_total_payment = hidden_total_payment - new_sd_amount ; 

                if(non_prov_sche_ids==''){
                    jQuery('#per_sche_net_payment'+row_serial).val(0);
                    jQuery('#per_sche_net_payment_sep'+row_serial).val(0);
                    jQuery('#per_sche_net_payment_tr'+row_serial).text(0);
                }else{
                    
                    jQuery('#per_sche_net_payment'+row_serial).val(non_prov_net_payment);
                    jQuery('#per_sche_net_payment_sep'+row_serial).val(non_prov_net_payment_sep);
                    jQuery('#per_sche_net_payment_tr'+row_serial).text(non_prov_net_payment);
                }
                    
                    jQuery('#per_sche_sd'+row_serial).val(new_sd_amount);
                    jQuery('#per_sche_sd_tr'+row_serial).text(new_sd_amount);

                    var new_total_payment1 = set_total_payment(i,row_serial);
                    jQuery('#total_payment').val(new_total_payment1.toFixed(2));
                    var net_payment = jQuery('#new_net_payment'+sche_row_id).val();
                
                    var final_net_payment=  parseInt(net_payment) -  parseInt(sum);
                    if(final_net_payment < 0){  
                        alert('Net Amount can not be Negative');
                        return false; 
                    }
                    else {  
                        jQuery('#sd_adjust_amount'+sche_row_id).val(sum);
                        jQuery('#final_net_payment'+sche_row_id).text(final_net_payment); 
                    }
                    
                    jQuery('#window').jqxWindow('close');
                } 
                else{

                    }
        });


                var theme = 'classic';
                jQuery('#acceptInput').jqxCheckBox({ width: 130, theme: theme });
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
    
                ]
        });

        jQuery("#sendButton").on('click', function(e){

            var i=parseInt(jQuery('#agree_row_count').val(), 10);    
           
            if(agree_set_adjust_amount(i)){
           
                  var form = jQuery('#form').serialize();
                               
                    e.preventDefault();
                    jQuery("#sendButton").hide();
                    jQuery("#loading").show();
                 
                        jQuery.ajax({
                        url: "<?= base_url() ?>index.php/rent_schedule_payment/add_edit_action/<?= $add_edit ?>/<?= $id ?>",
                                type: "POST",
                                data:form,
                               // contentType: false,
                                cache: false,
                               // processData:false,
                               async : false,
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
                        } else{
                               
                        var row = {};
                       
                                
                                row["id"] = json['row_info'].id;
                                row["rent_agree_ref"] = json['row_info'].rent_agree_ref;          
                                row["fin_ref_no"] = json['row_info'].fin_ref_no;          
                                row["location_name"] = json['row_info'].location_name;          
                                row["schedule_strat_dt"] = json['row_info'].schedule_strat_dt;          
                                row["paid_dt"] = json['row_info'].paid_dt;          
                               // row["location_address"] = json['row_info'].location_name;
                                row["monthly_amount"] = json['row_info'].monthly_amount;
                                row["total_others_amount"] = json['row_info'].total_others_amount;
                                row["arear_adjust_amount"] = json['row_info'].arear_adjust_amount;
                                row["rent_amount"] = json['row_info'].rent_amount;
                                row["adv_adjustment_amt"] = json['row_info'].adv_adjustment_amt;
                                row["sd_adjust_amt"] = json['row_info'].sd_adjust_amt;
                                row["tax_amount"] = json['row_info'].tax_amount;
                                row["sts"] = json['row_info'].sts;
                                row["fin_v_by"] = json['row_info'].fin_v_by;
                                
                                                                 

                                window.parent.jQuery("#jqxgrid").jqxGrid('clearselection');
                                

                            <? if ($add_edit == 'add') { ?>
                                        var paginginformation = window.parent.jQuery("#jqxgrid").jqxGrid('getpaginginformation');
                                                var insert_index = paginginformation.pagenum * paginginformation.pagesize;
                                               
                                                var commit = window.parent.jQuery("#jqxgrid").jqxGrid('addrow', null, row, insert_index);
                                                window.parent.jQuery("#jqxgrid").jqxGrid('selectrow', insert_index);
                            <? } else { ?>
                                        jQuery.each(row, function(key, val){
                                     
                                        window.parent.jQuery("#jqxgrid").jqxGrid('setcellvalue',<?= $editrow ?>, key, row[key]);
                                        });
                                                window.parent.jQuery("#jqxgrid").jqxGrid('selectrow',<?= $editrow ?>);
                            <? } ?>
                            window.parent.jQuery("#jqxgrid").jqxGrid('updatebounddata');


                            jQuery("#msgArea").val('');
                                    window.parent.jQuery("#error").show();
                                    window.parent.jQuery("#error").fadeOut(11500);
                                    window.parent.jQuery("#error").html('<img align="absmiddle" src="' + baseurl + 'images/drag.png" border="0" /> &nbsp;Successfully Saved');
                                    window.top.EOL.messageBoard.close();
                                   
                            }

                        },
                                error: function(data){ 

                                }
                        });
            }            //    }
               
        });



});
                


    function check_adjust_amount(counter)
    {
            
            <?php if ($add_edit=='edit'){ ?>
            var i=1;
            
            return true;
            <?php }else{ ?>
            var i=1;
                var i=parseInt(jQuery('#sche_row_count').val(), 10);
                var sche_row_id = jQuery('#sche_row_id').val();   
                var row_serial = jQuery('#row_serial').val(); 
                var sum =  sche_set_adjust_amount_for_sd(i,sche_row_id,row_serial);
              
            for(i=1;i<=counter;i++){
                    if (jQuery('#sd_check'+i).is(':checked')) {
                        if(parseFloat(jQuery('#new_sd_adjust_amount'+i).val())==0){
                            jQuery('#sd_check'+i).prop('checked', false);

                        }
                        sum_a=parseFloat(jQuery('#new_sd_adjust_amount'+i).val());
                        sum_t=parseFloat(jQuery('#sche_sd_rest'+i).val());

                        if(i==1){
                            // if(sum_a!=sum_t){
                            //     alert("Adjust amount must equal to Total adjust amount in row "+i);
                            //     jQuery('#new_sd_adjust_amount'+i).focus();
                            //     return false; 
                            // }
                          
                        }else{
                                 given_amt_first_row=parseFloat(jQuery('#new_sd_adjust_amount1').val());
                                 rest_amt_first_row=parseFloat(jQuery('#sche_sd_rest1').val());
                                if(jQuery('#new_sd_adjust_amount'+i).val()!='' && given_amt_first_row!=rest_amt_first_row){
                                      alert("Security Deposit should be fully adjusted in First entry !");
                                      jQuery('#new_sd_adjust_amount1').focus();
                                      return false; 
                                }

                        }

                        if(sum_a>sum_t)
                        {
                            alert("Adjust amount exceed Rest amount in row "+i);
                             jQuery('#new_sd_adjust_amount'+i).focus();
                            return false;
                        }
                    
                        if(sum_a>sum)
                        {
                            alert("SD Adjust amount exceed Total net amount in row "+i);
                             jQuery('#new_sd_adjust_amount'+i).focus();
                            return false;
                        }
                    }else{
                           if(i==1){
                                if(jQuery('#new_sd_adjust_amount'+i).val()==''){
                                      alert("Adjust amount can not be empty at row "+i);
                                      jQuery('#new_sd_adjust_amount'+i).focus();
                                      return false; 
                                }
                            }
                    }
            }
            
            return true;
            <?php } ?>
    }

    function check_bill_select(counter){
        var flag = 0;
        for(i=1;i<=counter;i++){
      
                    if (jQuery('#sd_check'+i).is(':checked')) {

                        flag++;
                    }
            }
           //   alert(flag)
            if(flag==0){
                alert("Please Check at least one !!!")
                return false;
            }else{
                return true;
            }
    }

    function set_total_payment(counter1,row_serial){
        var counter = jQuery('#agree_row_count').val();
        var total_payment = 0;
        for(i=1;i<=counter;i++){
       
                if (jQuery('#agree_check'+i).is(':checked')) {
                            
                                
                               total_payment += parseFloat(jQuery('#per_sche_net_payment'+i).val());
                                
                }else{
                    
                }
             
            }
    return total_payment;
         
    }

    function set_total_payment_for_check_all(counter1,row_serial){
        var counter = jQuery('#agree_row_count').val();
        var total_payment = 0;
        for(i=1;i<=counter;i++){
        
        
                 total_payment += parseFloat(jQuery('#per_sche_net_payment'+i).val());
            }
        return total_payment;
     
    }

    function set_total_payment_for_check_cm_pm(pm_cm_sts,row_serial){
        //var counter = pm_cm_sts;
        var counter = jQuery('#agree_row_count').val();
        var total_payment = 0;
        for(i=1;i<=counter;i++){
        
                if (jQuery('#agree_check'+i).is(':checked')) {
                   total_payment += parseFloat(jQuery('#per_sche_net_payment'+i).val());
                }else{
                    
                }
            }
        return total_payment;
     
    }

    function deduct_total_payment_for_check_cm_pm(pm_cm_sts,row_serial){
        //var counter = pm_cm_sts;
        var counter = jQuery('#agree_row_count').val();
        var total_payment = 0;
        for(i=1;i<=counter;i++){
        
                if (jQuery('#agree_check'+i).is(':checked') && jQuery('#agree_point_of_payment'+i).val()==pm_cm_sts) {
                   total_payment += parseFloat(jQuery('#per_sche_net_payment'+i).val());
                }else{
                    
                }
            }
        return total_payment;
     
    }


    function toggleCheckbox(row_serial,i,rent_id){


        if (jQuery('#'+row_serial+'sche_check'+i).is(':checked')) {
           // alert('c');
        }else{
            // alert('u');
        }

    }

    function check_sche_select(counter,row_serial){
        var flag = 0;
        for(i=1;i<=counter;i++){
      
                    if (jQuery('#'+row_serial+'sche_check'+i).is(':checked')) {

                        flag++;
                    }
            }
          
            if(flag==0){
                jQuery('#agree_sche_check'+row_serial).val(0);
                alert("Please Check at least one !!!");

                return false;
            }else{
                return true;
            }
    }

    function per_sche_adj_on_sd_close(sche_row_counter,row_serial){
        var i=1;
        var chk_adj_amt='';
        var str1 = '';
        var n=1;
                    for(i=1;i<=sche_row_counter;i++){
                        if (jQuery('#'+row_serial+'sche_check'+i).is(':checked')) {

                            if(jQuery('#new_paid_sts'+i).val()=='unpaid_payment' || jQuery('#new_paid_sts'+i).val()=='stop_payment' || jQuery('#new_paid_sts'+i).val()=='stop_payment_pm'){  // 7 dec 2017
                              if(n!=1){str1 +=',';}
                              if(i!=1){chk_adj_amt +=',';}
                              str1 +=parseInt(jQuery('#id'+i).val());
                              chk_adj_amt += parseFloat(jQuery('#new_sche_adjust_amount'+i).val());
                              n++;
                            }  

                        }else{
                           var old_hidden_final_net_payment = jQuery('.old_hidden_final_net_payment'+i).val();
                            jQuery('.final_net_payment'+i).text(old_hidden_final_net_payment);
                            jQuery('#new_sche_net_amount'+i).val(old_hidden_final_net_payment);

                            jQuery('.avg_sd_payment'+i).text(0);
                            jQuery('#new_avg_sd_payment'+i).val(0);

                        }
                    }
        jQuery('#chk_sche_id_on_sd_close'+row_serial).val(str1);
        return chk_adj_amt;

    }

    function sche_set_adjust_amount(counter,sche_row_id,row_serial){
       
            var i=1;
            var sum=0.0;
            var updated_payment_text='';
            var updated_sd_text='';
            var stop_payment_amount=0.0;
            var stop_payment_adj_amount=0.0;
            var updated_sd_float=0.0;
            var sum_monthly_rent=0.0;
            var sum_monthly_rent_sep='';
            var sum_others_rent=0.0;
            var sum_others_rent_sep='';
            var sum_arear=0.0;
            var sum_arear_sep='';
            var sum_adjustment_adv=0.0;
            var sum_adjustment_adv_sep='';
            var sum_prov=0.0;
            var sum_prov_sd=0.0;
            var sum_tax=0.0;
            var sum_tax_sep='';
            var str = '';
            var str1 = '';
            var str2 = '';
            var str_h = '';
            var str_o = '';
            var str_paid_sts = '';
            var chk_adj_amt='';
            var sum_f=0.0;
            var m=1;
            var n=1;
            var diff=0;
            for(i=1;i<=counter;i++){
                if (jQuery('#'+row_serial+'sche_check'+i).is(':checked')) {
                
                    //new
                    var t=jQuery('#id'+i).val();
               
                if(parseInt(jQuery('#hidden_amount'+t).val())!=0)
                    {
 
                        //sum_f=parseInt(jQuery('#adj_amount'+i).val())-parseInt(jQuery('#hidden_amount'+t).val());
                        //sum_f=parseInt(jQuery('#old_hidden_amount'+i).val())-parseInt(jQuery('#adj_amount'+i).val());
                        sum_f=parseInt(jQuery('#new_sche_adjust_amount'+i).val())- parseInt(jQuery('#old_hidden_amount'+i).val());
                        
                    }else{
                        sum_f=parseInt(jQuery('#new_sche_adjust_amount'+i).val());

                    }
                    //old
                    if(i!=1){str +=','; }
                    str +=i;
                    
                    if(n!=1){str1 +=','; str_h +=',';str_o +=','; updated_sd_text +=',';
                     updated_payment_text +=','; str_paid_sts +=','; 
                     sum_monthly_rent_sep +='@'; sum_adjustment_adv_sep +='@'; 
                     sum_arear_sep +='@'; sum_tax_sep +='@';
                     sum_others_rent_sep +='@';
                      }
                    if(i!=1){chk_adj_amt +=',';}
                    str_paid_sts +=jQuery('#new_paid_sts'+i).val();
                    if(jQuery('#new_paid_sts'+i).val()=='stop_payment' || jQuery('#new_paid_sts'+i).val()=='stop_cost_center' || jQuery('#new_paid_sts'+i).val()=='stop_cost_center_pm' || jQuery('#new_paid_sts'+i).val()=='stop_payment_pm'){
                        stop_payment_amount +=parseFloat(jQuery('#net_payment_before_tax'+i).val());
                        stop_payment_adj_amount +=parseFloat(jQuery('#new_sche_adjustment_adv'+i).val());
                    }
                    updated_payment_text +=parseFloat(jQuery('#new_sche_net_amount'+i).val());
                    updated_sd_text +=parseFloat(jQuery('#new_avg_sd_payment'+i).val());
                    updated_sd_float +=parseFloat(jQuery('#new_avg_sd_payment'+i).val());
                    str1 +=parseInt(jQuery('#id'+i).val());
                    str_h +=parseInt(jQuery('#id'+i).val())+'#'+parseInt(jQuery('#new_sche_adjust_amount'+i).val())+"#"+sum_f;
                    str_o +=parseInt(jQuery('#id'+i).val())+'#'+parseInt(jQuery('#new_sche_adjust_amount'+i).val())+"#"+parseInt(jQuery('#new_sd_adjust_amount'+i).val());
                    
                    if(jQuery('#new_sche_adjust_amount'+i).val().trim()!='-'){
                        sum += parseFloat(jQuery('#new_sche_adjust_amount'+i).val());
                    }
                    
                    sum_monthly_rent += parseFloat(jQuery('#new_sche_monthly_rent'+i).val());
                    sum_monthly_rent_sep += parseFloat(jQuery('#new_sche_monthly_rent'+i).val());
                    sum_arear += parseFloat(jQuery('#new_sche_arear'+i).val());
                    sum_arear_sep += parseFloat(jQuery('#new_sche_arear'+i).val());
                    sum_others_rent += parseFloat(jQuery('#new_sche_others_rent'+i).val());
                    sum_others_rent_sep += parseFloat(jQuery('#new_sche_others_rent'+i).val());
                    sum_adjustment_adv += parseFloat(jQuery('#new_sche_adjustment_adv'+i).val());
                    sum_adjustment_adv_sep += parseFloat(jQuery('#new_sche_adjustment_adv'+i).val());
                    sum_prov += parseFloat(jQuery('#new_sche_prov'+i).val());
                    //sum_prov_sd += parseFloat(jQuery('#prov_sd_amt'+i).val());
                    sum_tax += parseFloat(jQuery('#new_sche_tax'+i).val());
                    sum_tax_sep += parseFloat(jQuery('#new_sche_tax'+i).val());
                    chk_adj_amt += parseFloat(jQuery('#new_sche_adjust_amount'+i).val());
                    n++;
                    //jQuery('#check'+i).attr('checked', 'checked');
                }
                else{
  
                    var t=jQuery('#id'+i).val();
                    if(parseInt(jQuery('#hidden_amount'+t).val())!=0)
                    {
                        if(m!=1){str2 +=',';}
                        str2 +=parseInt(jQuery('#id'+i).val())+'#'+parseInt(jQuery('#hidden_amount'+t).val());
                    m++;
                    } 
                }
            }
           updated_sd_int=  Math.ceil(updated_sd_float);
         
            jQuery('#checked_schedule_id'+row_serial).val(str1);
         
            jQuery('#checked_adjustment_with_hash'+sche_row_id).val(str_h);
            jQuery('#checked_adjustment_amt').val(chk_adj_amt);
            jQuery('#checked_schedule_adjustment_amt'+row_serial).val(chk_adj_amt);
            jQuery('#checked_schedule_net_amt'+row_serial).val(updated_payment_text);
            jQuery('#checked_schedule_sd_amt'+row_serial).val(updated_sd_text);
            jQuery('#checked_updated_sd_int'+row_serial).val(updated_sd_int);
           
            jQuery('#prev_adj_value_hash').val(str_o);
            //jQuery('#old_value').val(chk_adj_amt);
            jQuery('#unchecked_adjustment').val(str2);
          
            jQuery('#sche_counter'+row_serial).val(str);

            // update table
            jQuery('#per_sche_monthly_rent'+row_serial).val(sum_monthly_rent);
            jQuery('#per_sche_monthly_rent_sep'+row_serial).val(sum_monthly_rent_sep);
            jQuery('#per_sche_monthly_rent_tr'+row_serial).text(sum_monthly_rent);

            jQuery('#per_sche_arear'+row_serial).val(sum_arear);
            jQuery('#per_sche_arear_sep'+row_serial).val(sum_arear_sep);
            jQuery('#per_sche_arear_tr'+row_serial).text(sum_arear);

            jQuery('#per_sche_others'+row_serial).val(sum_others_rent);
            jQuery('#per_sche_others_sep'+row_serial).val(sum_others_rent_sep);
            jQuery('#per_sche_others_tr'+row_serial).text(sum_others_rent);

            jQuery('#per_sche_adjust'+row_serial).val(sum_adjustment_adv);
            jQuery('#per_sche_adjust_sep'+row_serial).val(sum_adjustment_adv_sep);
            jQuery('#per_sche_adjust_tr'+row_serial).text(sum_adjustment_adv);

            jQuery('#per_sche_prov'+row_serial).val(sum_prov);
            jQuery('#per_sche_prov_tr'+row_serial).text(sum_prov);
           // jQuery('#per_sche_prov_sd'+row_serial).val(sum_prov_sd);

            jQuery('#per_sche_tax'+row_serial).val(sum_tax);
            jQuery('#per_sche_tax_sep'+row_serial).val(sum_tax_sep);
            jQuery('#per_sche_tax_tr'+row_serial).text(sum_tax);
    
            jQuery('#checked_stop_cost_center_amt'+row_serial).val(stop_payment_amount);     
            jQuery('#checked_stop_cost_center_adj_amt'+row_serial).val(stop_payment_adj_amount);  
            //alert(str_paid_sts);
            arr =  jQuery.unique(str_paid_sts.split(','));
            str_paid_sts = arr.join(",");


            if (jQuery.inArray('unpaid_payment', arr)!='-1' && jQuery.inArray('stop_payment', arr)!='-1') {
                
                jQuery('#sche_payment_sts'+row_serial).val('stop_unpaid_payment');

            }else{

               
                if (jQuery.inArray('unpaid_payment', arr)!='-1' && jQuery.inArray('stop_cost_center_pm', arr)!='-1') {
                   alert("Please select only one Type of Payment");
                   sum=-2;
                   return sum;
                }else if(jQuery.inArray('unpaid_payment', arr)!='-1' && jQuery.inArray('stop_payment_pm', arr)!='-1'){
                  alert("Please select only one Type of Payment");
                  sum=-2;
                  return sum;

                }
                else{
                  jQuery('#sche_payment_sts'+row_serial).val(str_paid_sts);
                }
                
            }

            if (jQuery.inArray('advance_rent_payment', arr)!='-1' && jQuery.inArray('unpaid_payment', arr)!='-1') {
                
               alert('Advance And Mature Schedule Payment Should be Seperate');
               sum=-2;
               return sum;

            }
            
            //alert(jQuery('#sche_payment_sts'+row_serial).val());
           
            return sum;
    }
// 28 sep
            function foo(num1, num2){
              if (num1 > num2)
                {return num1-num2;}
              else
                {return num2-num1;}
            }
// 19 sep
    function sche_set_adjust_amount_for_sd(counter,sche_row_id,row_serial)
            {
                
            var i=1;
            var sum=0.0;
            var updated_payment_text='';
            var updated_sd_text='';
            var sum_monthly_rent=0.0;
            var sum_others_rent=0.0;
            var sum_adjustment_adv=0.0;
            var sum_prov=0.0;
            var sum_tax=0.0;
            var str = '';
            var str1 = '';
            var str2 = '';
            var str_h = '';
            var str_o = '';
            var chk_adj_amt='';
            var sum_f=0.0;
            var m=1;
                var n=1;
                var diff=0;
            for(i=1;i<=counter;i++){
                if (jQuery('#'+row_serial+'sche_check'+i).is(':checked')) {
                
                    //new
                    var t=jQuery('#id'+i).val();
                    
                    if(i!=1){str +=','; updated_payment_text +=','; updated_sd_text +=',';}
                    str +=i;
                    updated_payment_text +=parseFloat(jQuery('#new_sche_net_amount'+i).val());
                    updated_sd_text +=parseFloat(jQuery('#new_avg_sd_payment'+i).val());
                    
                    
                    sum += parseFloat(jQuery('#new_sche_adjust_amount'+i).val());
                  
                    n++;
                   
                }
                else{
        

                    var t=jQuery('#id'+i).val();
                    if(parseInt(jQuery('#hidden_amount'+t).val())!=0)
                    {
                        if(m!=1){str2 +=',';}
                        str2 +=parseInt(jQuery('#id'+i).val())+'#'+parseInt(jQuery('#hidden_amount'+t).val());
                    m++;
                    } 
                }
            }
            
            return sum;
    }

    function set_adjust_amount(counter,rent_id)
            {
            var i=1;
            var sum=0.0;
            var sd_sep='';
            var str = '';
            var str1 = '';
            var str2 = '';
            var str_h = '';
            var str_o = '';
            var chk_adj_amt='';
            var sum_f=0.0;
            var m=1;
                var n=1;
                var diff=0;
            for(i=1;i<=counter;i++){
                if (jQuery('#sd_check'+i).is(':checked')) {
                
                    var t=jQuery('#sd_id'+i).val();
                  
                if(parseInt(jQuery('#hidden_amount'+t).val())!=0)
                    {
 
                        sum_f=parseInt(jQuery('#new_sd_adjust_amount'+i).val())- parseInt(jQuery('#old_hidden_amount'+i).val());
                       
                    }else{
                        sum_f=parseInt(jQuery('#new_sd_adjust_amount'+i).val());

                    }
                    //old
                    if(i!=1){str +=',';}
                    str +=i;
                    if(n!=1){str1 +=','; str_h +=',';str_o +=','; sd_sep+='@';}
                    if(n!=1){chk_adj_amt +=',';}
                    str1 +=parseInt(jQuery('#sd_id'+i).val());
                    str_h +=parseInt(jQuery('#sd_id'+i).val())+'#'+parseInt(jQuery('#new_sd_adjust_amount'+i).val())+"#"+sum_f;
                    str_o +=parseInt(jQuery('#sd_id'+i).val())+'#'+parseInt(jQuery('#new_sd_adjust_amount'+i).val())+"#"+parseInt(jQuery('#new_sd_adjust_amount'+i).val());
                   
                    sum += parseFloat(jQuery('#new_sd_adjust_amount'+i).val());
                    sd_sep += parseFloat(jQuery('#new_sd_adjust_amount'+i).val());
                    chk_adj_amt += parseFloat(jQuery('#new_sd_adjust_amount'+i).val());
                    n++;
                    //jQuery('#check'+i).attr('checked', 'checked');
                }
                else{
     
                    var t=jQuery('#sd_id'+i).val();
                    if(parseInt(jQuery('#hidden_amount'+t).val())!=0)
                    {
                        if(m!=1){str2 +=',';}
                        str2 +=parseInt(jQuery('#sd_id'+i).val())+'#'+parseInt(jQuery('#hidden_amount'+t).val());
                    m++;
                    } 
                }
            }
           
            jQuery('#sd_checked_adjustment'+rent_id).val(str1);
            
            jQuery('#sd_checked_adjustment_with_hash'+rent_id).val(str_h);
            jQuery('#sd_checked_adjustment_amt'+rent_id).val(chk_adj_amt);
          
            jQuery('#sd_prev_adj_value_hash'+rent_id).val(str_o);
          
            jQuery('#sd_unchecked_adjustment'+rent_id).val(str2);
          
            jQuery('#sd_counter'+rent_id).val(str);
            jQuery('#prev_sd_amount_sep'+rent_id).val(sd_sep);
            return sum;
    }



    function schedule_popup(rent_agree_id){

            jQuery.ajax({
                url: '<?php echo base_url(); ?>index.php/rent_schedule_payment/get_matured_schedule_info',
                type: "post",
                data: { rent_id:rent_agree_id }, 
               // datatype: 'json',
               datatype: "html",
                success: function(response){
        
                        if(response !=""){

                                jQuery("#data_table").html(response).show();
                        }
                }
               
            });

                val=1; 
               
                jQuery('#window').jqxWindow('open');

    }   



    function sd_preview_item(agree_id){
     
        jQuery.ajax({
            url: '<?php echo base_url(); ?>index.php/rent_schedule_payment/get_sd_info',
            type: "post",
            data: {agree_id : agree_id },
            //datatype: 'json',
          datatype: 'html',
          success: function(response){
    
                    if(response !=""){

                            jQuery("#data_table2").html(response).show();
                             var i=parseInt(jQuery('#row_count').val());
                  
                                //set_val(sche_row_id);
                                set_val(agree_id);
                                set_checked(i,agree_id);
                    }
            },

            error:   function(model, xhr, options){
                alert('failed');
            },
        });

        jQuery('#window2').jqxWindow('open');
        jQuery('#window2').jqxWindow('bringToFront');

    }
    
    
  

    function set_val(agree_id)
        {
                var k=0;
            
                var arr = jQuery('#sd_checked_adjustment_with_hash'+agree_id).val().split(',');
          
                for(i=0;i<arr.length;i++){
                var n = arr[i].split('#'); 
            
                    k=jQuery('#'+n[0]).val();
                    jQuery('#new_sd_adjust_amount'+k).val(n[1]);  
                    jQuery('#old_adj'+k).val(n[1]);  
                    jQuery("#hidden_amount"+n[0]).val(n[1]);
                
                }


        }

            function set_checked(counter,agree_id)
        {
                var i=1;

                var arr = jQuery('#sd_counter'+agree_id).val().split(',');
                //alert(arr);
                for(i=1;i<=counter;i++){
                    var n = i.toString();
                    //alert(jQuery.inArray( n, arr )!= -1);

                    if(jQuery.inArray( n, arr )!= -1)
                    {
                        jQuery('#sd_check'+i).attr('checked','checked');
                    }
                }
        }
        
        function sche_set_checked(counter,row_serial)
         {
           
                    var k=0;
                        var arr = jQuery('#sche_counter'+row_serial).val().split(',');
                        var arr_net = jQuery('#checked_schedule_net_amt'+row_serial).val().split(',');
                        var arr_sd = jQuery('#checked_schedule_sd_amt'+row_serial).val().split(',');
                     
            setTimeout(function() {
                        for(i=1;i<=counter;i++){
                          
                            var n = i.toString();
                           // alert(jQuery.inArray( n, arr ));
                                if(arr!=''){
                                        if(jQuery.inArray( n, arr )!= -1)
                                        {

                                            jQuery('#'+row_serial+'sche_check'+i).attr('checked','checked');
                                            var net_payment_text = jQuery('.final_net_payment'+i).text().trim();
                                            if(net_payment_text=='-'){

                                            }else{
                                                jQuery('.final_net_payment'+i).text(arr_net[k]);
                                                jQuery('#new_sche_net_amount'+i).val(arr_net[k]);
                                            }
                                           
                                            jQuery('.avg_sd_payment'+i).text(arr_sd[k]);
                                            jQuery('#new_avg_sd_payment'+i).val(arr_sd[k]);
                                            k++;
                                        }else{
                                            jQuery('#'+row_serial+'sche_check'+i).attr('checked', false);
                                        }
                                }

                        }
             }, 500); 
        }


    function agree_set_adjust_amount(counter)
                {
                var i=1;
              
                var str = '';
                var str1 = '';
                var sr_list = '';
              
                var chk_adj_amt='';
                var sum_f=0.0;
                var m=1;
                    var n=1;
                    var diff=0;
                    var flag = 0;
                for(i=1;i<=counter;i++){
                    if (jQuery('#agree_check'+i).is(':checked')) {
                    
                        // alert(jQuery('#checked_schedule_id'+i).val());
                        // alert(jQuery('#checked_schedule_sts_list'+i).val());
                        // alert(jQuery('#agree_current_sts_id'+i).val());

                        if(jQuery('#agree_sche_check'+i).val()==0 && jQuery('#result_row_counter'+i).val() > 1){
                          
                          // alert(" Please Check at least one Monthly Schedule of Sr - "+i);
                          // return false;
                          if(sr_list!=''){sr_list +=',';}
                          sr_list += i;

                        }

                        // 19 feb start
                        if(jQuery('#agree_sche_check'+i).val()==0 && jQuery('#result_row_counter'+i).val() == 1){
                          var schedule_sts=jQuery('#checked_schedule_sts_list'+i).val();
                          if(schedule_sts=='stop' || schedule_sts=='advance'){

                            alert(" Please Check Monthly Schedule of Sr - "+i);
                            return false;
                          }
                          //else if(){} // here 
                          else{
                            // unpaid_payment or stop_cost_center depending on agree_current_sts_id
                              if(jQuery('#agree_current_sts_id'+i).val()==5){
                                
                                if(jQuery('#agree_point_of_payment'+i).val()=='pm'){
                                  jQuery('#sche_payment_sts'+i).val('stop_cost_center_pm');
                                }else{
                                  jQuery('#sche_payment_sts'+i).val('unpaid_payment');
                                }

                                //jQuery('#sche_payment_sts'+i).val('unpaid_payment');

                                
                              }else{
                                jQuery('#sche_payment_sts'+i).val('stop_cost_center');
                              }
                            
                          }
                          
                        }

                        // 19 feb end

                        if(str!=''){ str +=',';}
                        str +=i;
                        
                        n++;
                        flag++;
                        
                    }
                    else{
            //         alert
                    }
                }
                if(sr_list!=''){
                  alert(" Please Select Monthly Schedule Manually of Sr - "+sr_list);
                  return false;
                }

                 if(flag==0){
                alert("Please Check at least one !!!")
                return false;
            }else{
                jQuery('#agree_counter').val(str);
                return true;
            }
                
             
    }

 
    </script>


    <div  style=" width:100%; margin:auto">  
        <form name="form" id="form" class="form" action="#" method="post" >
                    <!-- <input name="counter" type="hidden" id="counter" value="<?=isset($result->counter)?$result->counter:''?>"  class="text-input-small" /> -->
                    <input name="id" type="hidden" id="id" value="<?=isset($result->id)?$result->id:''?>"  class="text-input-small" />
                    <input name="checked_adjustment" type="hidden" id="checked_adjustment" value="<?=isset($result->bill_adjusted_ids)?$result->bill_adjusted_ids:''?>"  class="text-input-small" />
                    <!-- <input name="checked_adjustment_with_hash" type="hidden" id="checked_adjustment_with_hash" value="<?=isset($result->bill_ids_hash)?$result->bill_ids_hash:''?>"  class="text-input-small" /> -->
                    <input name="checked_adjustment_amt" type="hidden" id="checked_adjustment_amt" value="<?=isset($result->bill_adjusted_amts)?$result->bill_adjusted_amts:''?>"  class="text-input-small" />
                    <input name="unchecked_adjustment" type="hidden" id="unchecked_adjustment" value=""  class="text-input-small" />
                    

                    <input name="prev_adj_value" type="hidden" id="prev_adj_value" value="<?=isset($result->bill_adj_amt)?$result->bill_adj_amt:''?>"  class="text-input-small" />
                    <input name="prev_adj_value_hash" type="hidden" id="prev_adj_value_hash" value="<?=isset($result->bill_ids_hash)?$result->bill_ids_hash:''?>"  class="text-input-small" />
                    <input name="test_old" type="hidden" id="test_old" value="<?=isset($result->test)?$result->test:''?>"  class="text-input-small" />
                    <input name="tot_diff" type="hidden" id="tot_diff" value=""  class="text-input-small" />
                    <input name="total_row" type="hidden" id="total_row" value=""  class="text-input-small" />

<!--    main agreement data   -->
                     <input name="agree_total_row" type="hidden" id="agree_total_row" value=""  class="text-input-small" />
                     <input name="agree_counter" type="hidden" id="agree_counter" value=""  class="text-input-small" />
                     <input name="tax_percentage" type="hidden" id="tax_percentage" value="<?=isset($tax_percentage->tax_amount)?$tax_percentage->tax_amount:''?>"  class="text-input-small" />
                    
                    
  <div class="formHeader">Schedule Payment</div>
            
            <div style="padding: 4px;width: 98%;height:40px;margin-left: 1px;margin-top: 2px;border: 1px solid #c0c0c0;font-family: Calibri;font-size: 14px;">
              <table width="99%">
                <tr>
                  <td width="18%" ><input type="checkbox" name="check_all_cm" id="agree_check_all_cm" value="" serial="" class="" > <b>Regular Current Month</b></td>
                  <td width="18%" ><input type="checkbox" name="check_all_pm" id="agree_check_all_pm" value="" serial="" class="" > <b>All Following Month</b></td>
                  <td width="18%" ><input type="checkbox" name="check_all_stop" id="agree_check_all_stop" value="" serial="" class="" > <b>All Stoped Payment</b></td>
                  <td width="18%" >Cost Center Upto 
                      <select id="cc_list" style="width:100px;">
                        <option value="001">001</option>
                        <option value="005">005</option>
                        <option value="010">010</option>
                        <option value="050">050</option>
                        <option value="100">100</option>
                        <option value="300">300</option>
                        <option value="400">400</option>
                        <option value="999">999</option>
                        
                      </select>
                  </td>
                  <td width="28%" ><b>Location Name:</b> <input type="text" name="search_cc" id="search_cc" > </td>
                  <!-- <td width="25%" ><input type="checkbox" name="check_all_released" id="agree_check_all_released" value="" serial="" class="" > <b>All Released Payment</b></td> -->
                </tr>
              </table>
            </div>

             <table class="" id="t01" style="width:99%">
                <tr class="headrow">
                    <th style="text-align:center;">
                        
                         <input type="checkbox" name="check_all" id="agree_check_all" value="" serial="" class="" style="display:none"> 
                    </th> 
                    <th style="text-align:center;">Sr.</th>
                    
                    <th style="text-align:center;">Reference</th>
                    <th style="text-align:center; display:none;">Fin Ref</th>
                    <th style="text-align:center;">Cost Center</th>
                    <!-- <th style="text-align:center;">Details</th> -->
                    <th style="text-align:center;"> Location Name</th>
                    <th style="text-align:center;"> Point of Payment </th>
                    <th style="text-align:center;"> Starting Date</th>
                  <!--   <th style="text-align:center;">Advance rent</th> -->
                    <th style="text-align:center;">Monthly Rent</th>
                    <th style="text-align:center;">Others</th>
                    <th style="text-align:center;">Arrear</th>
                    <th style="text-align:center;">Advance Adjustment</th>
                    
                    <th style="text-align:center;display:none;">Security Deposit</th>
                    <th style="text-align:center;">Tax</th>
                    <th style="text-align:center;">Net Rent</th>
                    <th style="text-align:center;">No of Pending Months</th>
                   
                </tr> 

<?php 
$i=1;
$calculated_total=0;
$cls_str='';
$agree_sts_style='';
$agree_sts_txt='';
foreach($rent_data as $row) { 

// 24 sep
    $net_payment_before_tax = ($row->total_monthly_rent + $row->total_others_rent + $row->total_area_amount) ;
    $tax_amount = ($net_payment_before_tax * $tax_percentage->tax_amount)/100;
    $new_net_payment = ($net_payment_before_tax - $tax_amount - ($row->total_adjustment_amount)) - $row->total_sd_adjust_amount; 

    // $schools_array = explode(",", $row->matured_sche_id);
    // $result = count($schools_array);
    $result_row_number = count(explode(',',$row->matured_sche_id));
    $agree_current_sts_id=$row->agree_current_sts_id;
    $agree_pervious_sts_id=$row->agree_pervious_sts_id;
    $point_of_payment=$row->point_of_payment;
    if($agree_current_sts_id==6){ $agree_sts_style = 'background-color:#ee8678'; $agree_sts_txt='(Stopped/Halted)';}
    elseif($point_of_payment=='pm' && $agree_current_sts_id==5){ $agree_sts_style = 'background-color:#94E6B5';$agree_sts_txt=''; }
    else{ $agree_sts_style = '';$agree_sts_txt='';}
    
    // 2 oct 2018  
    
    if($agree_current_sts_id==6){ $cls_str = 'cls_stop'; $tr_str = 'tr_cls_stop';}
    //else if($agree_current_sts_id==5 && $agree_pervious_sts_id==6){ $cls_str = 'cls_release';}
    else if($point_of_payment=='cm' && $agree_current_sts_id==5){ $cls_str = 'cls_cm'; $tr_str = 'tr_cls_cm';}
    else if($point_of_payment=='pm' && $agree_current_sts_id==5){ $cls_str = 'cls_pm'; $tr_str = 'tr_cls_pm';}
 
?>
                <tr  style="<?php echo $agree_sts_style;?>" class="<?php echo $tr_str;?>"  data-cc="<?php echo $row->agree_cost_center; ?>"  data-loc="<?php echo $row->location_name; ?>"> 
                    <td>
                        
<!--   sd popup data-->
                    <input name="sd_checked_adjustment_with_hash<?php echo $i; ?>" type="hidden" id="sd_checked_adjustment_with_hash<?php echo $row->rent_agree_id; ?>" value=""  class="text-input-small" />
                    <input name="sd_checked_adjustment<?php echo $i; ?>" type="hidden" id="sd_checked_adjustment<?php echo $row->rent_agree_id; ?>" value=""  class="text-input-small" />
                    <input name="checked_adjustment_with_hash<?php echo $i; ?>" type="hidden" id="checked_adjustment_with_hash<?php echo $row->rent_agree_id; ?>" value=""  class="text-input-small" />
                    <input name="sd_checked_adjustment_amt<?php echo $i; ?>" type="hidden" id="sd_checked_adjustment_amt<?php echo $row->rent_agree_id; ?>" value="<?=isset($result->bill_adjusted_amts)?$result->bill_adjusted_amts:''?>"  class="text-input-small" />
                    <input name="sd_unchecked_adjustment<?php echo $i; ?>" type="hidden" id="sd_unchecked_adjustment<?php echo $row->rent_agree_id; ?>" value=""  class="text-input-small" />
                    <input name="sd_prev_adj_value<?php echo $i; ?>" type="hidden" id="sd_prev_adj_value<?php echo $row->rent_agree_id; ?>" value="<?=isset($result->bill_adj_amt)?$result->bill_adj_amt:''?>"  class="text-input-small" />
                    <input name="sd_prev_adj_value_hash<?php echo $i; ?>" type="hidden" id="sd_prev_adj_value_hash<?php echo $row->rent_agree_id; ?>" value="<?=isset($result->bill_ids_hash)?$result->bill_ids_hash:''?>"  class="text-input-small" />
                    <input name="sd_counter<?php echo $i; ?>" type="hidden" id="sd_counter<?php echo $row->rent_agree_id; ?>" value=""  class="text-input-small" />
                    <input name="prev_sd_amount<?php echo $i; ?>" type="hidden" id="prev_sd_amount<?php echo $row->rent_agree_id; ?>" value=""  class="text-input-small" />
                    
<!--  sd popup data  -->


<!--   schedule popup data -->
                     <input name="sche_total_row<?php echo $i; ?>" type="hidden" id="sche_total_row<?php echo $i; ?>" value=""  class="text-input-small" />
                     <input name="sche_counter<?php echo $i; ?>" type="hidden" id="sche_counter<?php echo $i; ?>" value=""  class="text-input-small" />
                     <input id="checked_schedule_id<?php echo $i; ?>" name="checked_schedule_id<?php echo $i; ?>" class="checked_schedule_id" type="hidden" value="<?php echo $row->matured_sche_id; ?>"> 
                     <input id="checked_schedule_sts_list<?php echo $i; ?>" name="checked_schedule_sts_list<?php echo $i; ?>" class="checked_schedule_sts_list" type="hidden" value="<?php echo $row->paid_sts_list; ?>"> 
                     <input id="agree_current_sts_id<?php echo $i; ?>" name="agree_current_sts_id<?php echo $i; ?>" class="agree_current_sts_id" type="hidden" value="<?php echo $row->agree_current_sts_id; ?>"> 
                     <input id="agree_point_of_payment<?php echo $i; ?>" name="agree_point_of_payment<?php echo $i; ?>" class="agree_point_of_payment" type="hidden" value="<?php echo $row->point_of_payment; ?>"> 
                     <input id="checked_schedule_adjustment_amt<?php echo $i; ?>" name="checked_schedule_adjustment_amt<?php echo $i; ?>" class="checked_schedule_adjustment_amt" type="hidden" value="<?php echo $new_net_payment; ?>"> 
                     <input id="checked_schedule_net_amt<?php echo $i; ?>" name="checked_schedule_net_amt<?php echo $i; ?>" class="checked_schedule_net_amt" type="hidden" value="<?php echo $new_net_payment; ?>"> 
                     <input id="checked_schedule_sd_amt<?php echo $i; ?>" name="checked_schedule_sd_amt<?php echo $i; ?>" class="checked_schedule_sd_amt" type="hidden" value=""> 
                     <input id="checked_updated_sd_int<?php echo $i; ?>" name="checked_updated_sd_int<?php echo $i; ?>" class="checked_updated_sd_int" type="hidden" value=""> 
                     <input id="chk_sche_id_on_sd_close<?php echo $i; ?>" name="chk_sche_id_on_sd_close<?php echo $i; ?>" class="chk_sche_id_on_sd_close" type="hidden" value="<?php echo $row->matured_sche_id; ?>"> 
                     
                     <input id="sche_payment_sts<?php echo $i; ?>" name="sche_payment_sts<?php echo $i; ?>" class="sche_payment_sts" type="hidden" value=""> 
                     <input id="checked_stop_cost_center_amt<?php echo $i; ?>" name="checked_stop_cost_center_amt<?php echo $i; ?>" class="checked_stop_cost_center_amt" type="hidden" value=""> 
                     <input id="checked_stop_cost_center_adj_amt<?php echo $i; ?>" name="checked_stop_cost_center_adj_amt<?php echo $i; ?>" class="checked_stop_cost_center_adj_amt" type="hidden" value=""> 
                   
                   <!--    schedule popup data end -->     
                   
                    <input type="checkbox" name="check2" id="agree_check<?php echo $i; ?>" value="<?php echo $row->rent_agree_id; ?>" serial="<?php echo $i; ?>" class="payment_popup_chk cc_cls <?php echo $cls_str;?>" >
                    <input type="hidden" name="rent_agree_id<?php echo $i; ?>" value="<?php echo $row->rent_agree_id; ?>" class="">
                    <input type="hidden" name="rent_agree_ref<?php echo $i; ?>" value="<?php echo $row->rent_agree_ref; ?>" class="">
                    <input type="hidden" name="fin_ref_no<?php echo $i; ?>" value="<?php echo $row->fin_ref_no; ?>" class="">

                    <input type="hidden" name="agree_sche_check<?php echo $i; ?>"  id="agree_sche_check<?php echo $i; ?>"  value="0" >
                    <input type="hidden" name="result_row_counter<?php echo $i; ?>"  id="result_row_counter<?php echo $i; ?>"  value="<?php echo $result_row_number;?>" >
                  
                

                    </td>
                    <td><?php echo $i; ?></td>
                    
                    <td><div ><a href="#" class="payment_popup_chk_link" value="<?php echo $row->rent_agree_id; ?>" serial="<?php echo $i; ?>"><?php echo $row->rent_agree_ref; ?></a></div> </td>
                   
                    <td style="display:none;"><?php echo $row->fin_ref_no; ?> </td>
                    <td><?php echo $row->agree_cost_center; ?> </td>
                    <td><?php echo $row->location_name; ?> </td>
                  
                    <td ><?php 
                        $point_of_payment=$row->point_of_payment;
                        if($point_of_payment=='cm'){ $pp_str = 'Current Month';}
                        else{ $pp_str = 'Following Month';}
                        echo $pp_str.' '.$agree_sts_txt;
                            ?> </td>
                    <td>
                    <?php 
                        $start_date=date_create($row->schedule_strat_dt);
                        echo date_format($start_date,"d/m/Y");

                    ?> 
                    </td>
                    
                   
                    <td id="per_sche_monthly_rent_tr<?php echo $i; ?>"><?php echo number_format($row->total_monthly_rent,2); ?> </td>
                    <td id="per_sche_others_tr<?php echo $i; ?>"><?php echo number_format($row->total_others_rent,2); ?> </td>
                    <td id="per_sche_arear_tr<?php echo $i; ?>"><?php echo  number_format($row->total_area_amount,2); ?> </td>
                    <td id="per_sche_adjust_tr<?php echo $i; ?>"><?php echo number_format($row->total_adjustment_amount,2); ?></td>
                    
                    <td style="display:none;" id="per_sche_sd_tr<?php echo $i; ?>"><?php echo $row->total_sd_adjust_amount; ?></td>
                    <td id="per_sche_tax_tr<?php echo $i; ?>"><?php echo number_format($tax_amount,2); ?> </td>
                    <td id="per_sche_net_payment_tr<?php echo $i; ?>"><?php echo number_format($new_net_payment,2) ; ?>  </td>
                    <td style="text-align:center;" ><?php echo $result_row_number; ?></td>
                   
                     <input id="per_sche_monthly_rent<?php echo $i; ?>" name="per_sche_monthly_rent<?php echo $i; ?>" class="per_sche_monthly_rent_cls" type="hidden" value="<?php echo $row->total_monthly_rent; ?>"> 
                     <input id="per_sche_others<?php echo $i; ?>" name="per_sche_others<?php echo $i; ?>" class="per_sche_others_cls" type="hidden" value="<?php echo $row->total_others_rent; ?>"> 
                     <input id="per_sche_arear<?php echo $i; ?>" name="per_sche_arear<?php echo $i; ?>" class="per_sche_arear_cls" type="hidden" value="<?php echo $row->total_area_amount; ?>"> 
                     <input id="per_sche_adjust<?php echo $i; ?>" name="per_sche_adjust<?php echo $i; ?>" class="per_sche_adjust_cls" type="hidden" value="<?php echo $row->total_adjustment_amount; ?>"> 
                     <input id="per_sche_sd<?php echo $i; ?>" name="per_sche_sd<?php echo $i; ?>" class="per_sche_sd_cls" type="hidden" value="<?php echo $row->total_sd_adjust_amount; ?>"> 

                     <input id="per_sche_prov_sd<?php echo $i; ?>" name="per_sche_prov_sd<?php echo $i; ?>" class="per_sche_prov_cls" type="hidden" value="0"> 
                     <input id="per_sche_tax<?php echo $i; ?>" name="per_sche_tax<?php echo $i; ?>" class="per_sche_tax_cls" type="hidden" value="<?php echo $tax_amount; ?>"> 
                     <input id="per_sche_net_payment<?php echo $i; ?>" name="per_sche_net_payment<?php echo $i; ?>" class="per_sche_net_payment_cls" type="hidden" value="<?php echo $new_net_payment; ?>"> 
<!-- 1 feb -->
                     <input id="per_sche_net_payment_sep<?php echo $i; ?>" name="per_sche_net_payment_sep<?php echo $i; ?>" class="per_sche_net_payment_cls" type="hidden" value="<?php echo $new_net_payment; ?>"> 
                     <input id="per_sche_monthly_rent_sep<?php echo $i; ?>" name="per_sche_monthly_rent_sep<?php echo $i; ?>" class="per_sche_monthly_rent_cls" type="hidden" value="<?php echo $row->total_monthly_rent; ?>"> 
                     <input id="per_sche_adjust_sep<?php echo $i; ?>" name="per_sche_adjust_sep<?php echo $i; ?>" class="per_sche_adjust_cls" type="hidden" value="<?php echo $row->total_adjustment_amount; ?>"> 
                     <input id="prev_sd_amount_sep<?php echo $row->rent_agree_id; ?>" name="prev_sd_amount_sep<?php echo $i; ?>" class="prev_sd_amount_sep_cls" type="hidden" value=""> 
                     <input id="per_sche_arear_sep<?php echo $i; ?>" name="per_sche_arear_sep<?php echo $i; ?>" class="per_sche_arear_sep_cls" type="hidden" value="<?php echo $row->total_area_amount; ?>">
                     <input id="per_sche_tax_sep<?php echo $i; ?>" name="per_sche_tax_sep<?php echo $i; ?>" class="per_sche_tax_sep_cls" type="hidden" value="<?php echo $tax_amount; ?>"> 
                     <input id="per_sche_others_sep<?php echo $i; ?>" name="per_sche_others_sep<?php echo $i; ?>" class="per_sche_others_sep_cls" type="hidden" value="<?php echo $row->total_others_rent; ?>"> 

<!-- hidden -->

                     <input id="hidden_per_sche_net_payment<?php echo $i; ?>" name="hidden_per_sche_net_payment<?php echo $i; ?>" class="per_sche_net_payment_cls" type="hidden" value="<?php echo $new_net_payment; ?>"> 
                   
                  
                    
                </tr>
<?php 
$calculated_total= $calculated_total+$new_net_payment;
$i++ ; } ?>
                 

    </table>
        <?php $agree_row_count=$i-1; ?>   
            <input id="agree_row_count" class="" type="hidden" value="<?php echo $agree_row_count; ?>">
            <center> Grand Total Amount : 
            <!-- <input id="total_payment" class="" type="text" value="<?php echo $calculated_total; ?>" readonly> </center>  -->    
            <input id="total_payment" class="" type="text" value="0.00" readonly> </center>     
            <input id="hidden_total_payment" class="" type="hidden" value="<?php echo $calculated_total; ?>" readonly>

            
            <div class="" style="text-align:center;padding-top:40px;">
                <input type="button" value="Save" id="sendButton" class="buttonStyle" /> <span id="loading" style="display:none">Please wait... <img src="<?= base_url() ?>images/loader.gif" align="bottom"></span>
            </div>
            <br />

        </form>




    </div>

    <div id="window"  style=" margin: 10px auto">
            <div id="windowHeader">
                        <span>
                            Monthwise Rent Payment Schedule
                        </span>
           </div> 
                <div style="">
                
         
                    <div style="" id="data_table">
                        
                    </div>
                    
                   <center><input type="button" value="Save" id="sche_closeButton" class="buttonStyle" /></center>
                   <br />
                </div> 
    </div> 



    <div id="window2"  style=" margin: 10px auto">
            <div id="windowHeader">
                        <span>
                            Security Deposite Adjustment
                        </span>
           </div> 
                <div style="">
                
         
                    <div style="" id="data_table2">
                        


                    </div>
                    <center><input type="button" value="Close" id="sd_closeButton" class="buttonStyle" /></center>
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