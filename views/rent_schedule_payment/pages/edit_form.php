<body style="height:96%">
<style type="text/css">
	.jqx-rc-all {
    	border-radius:0 !important;
	}
	.ms-parent{ width: 320px !important;}
	ul.security_deposit_checklist{
		padding: 10px 40px 20px 0;
		text-align:left;
		list-style-type:none;
		float: left;
	}
	ul.security_deposit_checklist li{
		list-style-type:none;
		display:block;
	}
 
	h3 {
	    font-size: 12px;
	    margin: 18px 0 3px;
	    padding:0px 0px 0px 5px;
	    text-align:justify;
	    text-decoration: underline;
	}

	.error_msg{
		color: red;
		font-size: 16px;
		font-weight: bold;
		margin: 0 22px;
		padding-bottom: 5px;
		text-align: left;width: 55%;
	}
.register-table2 {
    margin: 25px;
    width: 45%;
    font-family: Arial;
    font-size: 13px;
}
.service_style {
    font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
    border-collapse: collapse;
    margin: 1px 6px;
}

.service_style td, .service_style th {
    font-size: 1.0em;
    border: 1px solid #3f8787;
    /*border: 1px solid #4197c7;*/
    padding: 3px 7px 2px 7px;
}

.service_style th {
    font-size: 1.0em !important;
    text-align: left;
    padding-top: 5px;
    padding-bottom: 4px;
    background-color: #4197c7;
    color: #ffffff;
}



</style> 



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
<script type="text/javascript">
// jQuery(document).on("cut copy paste","body",function(e) {
//         e.preventDefault();
//     });	
	jQuery(document).on("keypress",".number",function (evt) {
	 	var charCode = (evt.which) ? evt.which : evt.keyCode;
	 	if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46) 
	       return false;
	
		return true;	    	
   });
	jQuery(document).ready(function () {

		
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

	jQuery('#window').jqxWindow({height: 400, maxWidth: 1200, width: 1150,autoOpen: false});	
		 jQuery('#window2').jqxWindow({height: 400, maxWidth: 1200, width: 900,autoOpen: false,zIndex: 99999});


	
		

		var edit_agree_id= jQuery('#agreement_id_edit').val();
		jQuery('#agreement_idc').val(edit_agree_id);

	
		// initialize validator.
		jQuery('#form').jqxValidator({
			rules: [
		    	
				<? if($add_edit!="finance_verify"){?>
				
				
				<? } ?>
			]
		});

//function set_vendor_ac(){}

jQuery("#sd_closeButton").on('click',function(){
		    var i=parseInt(jQuery('#row_count').val(), 10);
		    var tax_percentage =parseFloat(jQuery('#tax_percentage').val());

            var sche_row_count = parseInt(jQuery('#sche_row_count').val(), 10);
        //var row_serial = jQuery('#row_serial').val();  
            var row_serial = 1;  
            var rent_id = jQuery('#rent_id').val();   
            jQuery('#total_row').val(i);
            if(check_adjust_amount(i) && check_bill_select(i)){
        //alert(jQuery('#checked_adjustment_amt').val()); 
            
           

           var checked_sche_net_amt = per_sche_adj_on_sd_close(sche_row_count,row_serial);
          // alert(checked_sche_net_amt);//805
           var comma_seperated_sche_chk_id = jQuery('#chk_sche_id_on_sd_close'+row_serial).val(); // comma seperated sche id
           if(comma_seperated_sche_chk_id==''){
                  alert('Select only Matured Schedule');
                  return false;
               }
            var sum = set_adjust_amount(i,rent_id);   
           var single_sche_chk_id = comma_seperated_sche_chk_id.split(',');
           var avg_adj= sum/ single_sche_chk_id.length;


           // 25 sep start
           		var sum_for_prov= 0;
                var prov_sche_ids= '';
                var non_prov_sche_ids= '';
                for(var k=0;k<single_sche_chk_id.length;k++){
                      
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

//test

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
	                         // jQuery('#prev_sd_amount'+rent_id).val(sum_for_prov);
	                         // jQuery('#new_sd_amount'+rent_id).val(sum_for_prov);
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
            	jQuery('#prev_sd_amount').val(sum);
                jQuery('#window2').jqxWindow('close');
            } 
            else{}
         
    });


jQuery("#br_list").on('select',function(event){
   	    	    var args = event.args; 
   	    	   
    	 if(args){
    	 	
            	var  output = '';
            	
            	var number=args.item.value;
            	var sNumber = number.toString();
            	for (var i = 0;i < 3; i += 1) {
    					output += sNumber.charAt(i);
					}
					//jQuery(".br_code").show();
						jQuery("#pay_br_code").val(output);
           
		}
   	    	});

   	    val=0;
		

		jQuery("#particulars").val('<? if(isset($result->particulars)){ echo $result->particulars; } else { echo ''; } ?>');
		// validate form.
		

	

		//jQuery("#payment_mode").change(function () {});
		<? if($add_edit=='edit'){ ?> 
           //alert(test);
          
        <? } else { ?>
            
            
            
         <? } ?>
		// jQuery("#exp_description").val('Security deposit GL Account for Security Deposit');
		 jQuery("#particulars").val('');

jQuery("#closeButton").click(function(){  

			//var agree_landlord_names = jQuery('#agree_landlord_names').val();
			var agree_landlord_names = jQuery('#agree_landlord_names').val().split(',');
			var agree_landlord_ids = jQuery('#agree_landlord_ids').val().split(',');
			jQuery('#ll_count').val(agree_landlord_names.length);
			jQuery('#is_changed').val(1);
			//alert( agree_landlord_names.length);

			jQuery(".sd_info_table1").html('<thead><tr><th><input type="hidden" name="landlord_counter" value="'+agree_landlord_names.length+'">Landlord</th><th>Amount</th><th>Payment Mode</th><th>Account no</th><th>Branch</th></tr><thead>');
			for(i=0;i<agree_landlord_names.length;i++){

			var single_ll_id= agree_landlord_ids[i];
			//alert( agree_landlord_ids[i]);
			jQuery(".sd_info_table1").append("<tr><td><input type='hidden' value='"+single_ll_id+"' name='single_ll_id"+i+"'><input type='hidden' value='"+agree_landlord_ids[i]+"' name='landlord_id"+i+"'><input type='text' value='"+agree_landlord_names[i]+"' name='landlord"+i+"'></td><td><input type='text' value='' name='sd_amount"+i+"'></td><td><div><select name='payment_mode"+i+"' id='payment_mode"+i+"' class='pay_mode'><option value=''>Select One</option><option value='ac_transfer'>Account Transfer</option><option value='pay_order'>Pay Order</option><option value='cash_payment'>Cash Payment</option></select></div></td><td><center><div name='"+i+"' id='"+i+"' ></div><div name='"+i+"' id='"+i+"'></div><input name='ac_no"+i+"' type='text' id='ac_no"+i+"' readonly value=''  class='text-input-small' /></center></td><td><input name='sd_branch_code"+i+"' readonly maxlength='3' id='sd_branch_code"+i+"' value='' style='width:50px;'/></td></tr>");
			//jQuery('#br_list'+i).hide();
				jQuery('#ac_gl'+i).hide();
				jQuery('#ac_no'+i).hide();
			sd_pay_mode_change(i,single_ll_id);

			}


		jQuery('.edit_table').html('');
		jQuery('#window').jqxWindow('close');

		});

jQuery("#sendButton").on('click', function(e){
//alert('sdfnn');
	 var row_serial = 1; 
     var i=parseInt(jQuery('#schedule_row_count').val(), 10);    
     var rent_id= jQuery('#rent_id').val();    
     var new_total_payment1 = set_total_payment(i,1);

    if(agree_set_adjust_amount(i)){

	    	var new_sd_amount = jQuery('#new_sd_amount'+rent_id).val();
	      	var updated_sd_amt_int = jQuery('#checked_updated_sd_int').val();
	 
	      	var difference = foo(parseFloat(new_sd_amount),parseFloat(updated_sd_amt_int));
	      	//alert(difference);
		    if(difference > 1){
		        alert('Security Deposit amount is not fully adjusted !');
		        return false;
		    }
			
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
				        }else{
				               
						        var row = {};
						       
						            
						                row["id"] = json['row_info'].id;
						                row["rent_agree_ref"] = json['row_info'].rent_agree_ref;          
						                row["fin_ref_no"] = json['row_info'].fin_ref_no;          
						                row["paid_dt"] = json['row_info'].paid_dt;          
						                row["agreement_id"] = json['row_info'].agreement_id;
						                 row["monthly_amount"] = json['row_info'].monthly_amount;
		                                row["total_others_amount"] = json['row_info'].total_others_amount;
		                                row["arear_adjust_amount"] = json['row_info'].arear_adjust_amount;
						                row["rent_amount"] = json['row_info'].rent_amount;
						                row["adv_adjustment_amt"] = json['row_info'].adv_adjustment_amt;
						                row["sd_adjust_amt"] = json['row_info'].sd_adjust_amt;
						                row["provision_adjust_amt"] = json['row_info'].provision_adjust_amt;
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
						                        //alert('key '+key+"val "+val);
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


	function emptyCheckCombo(field){
		if(jQuery("#"+field).val()==''){ return false; }
		var item = jQuery("#"+field).jqxComboBox('getSelectedItem');
		if(!item){return false;} else {return true};

	}


function agree_set_adjust_amount(counter)
{
            var i=1;
            var row_serial=1;
            var updated_sd_text='';
             var updated_sd_float=0.0;
             var stop_payment_amount=0.0;
            var stop_payment_adj_amount=0.0;
          
            var str = '';
            var str1 = '';
            var str_paid_sts = '';
            var chk_adj_amt='';
            var sum_f=0.0;
            var m=1;
            var n=1;
            var diff=0;
            var flag = 0;
            for(i=1;i<=counter;i++){
                if (jQuery('#'+row_serial+'sche_check'+i).is(':checked')) {

                    if(str!=''){

                    	str +=',';
                	}
                	if(n!=1){updated_sd_text +=','; str_paid_sts +=',';}
                    str +=i;
                      updated_sd_text +=parseFloat(jQuery('#new_avg_sd_payment'+i).val());
                      updated_sd_float +=parseFloat(jQuery('#new_avg_sd_payment'+i).val());
                    str_paid_sts +=jQuery('#new_paid_sts'+i).val();  
                    if(jQuery('#new_paid_sts'+i).val()=='stop_payment' || jQuery('#new_paid_sts'+i).val()=='stop_cost_center'){
                        stop_payment_amount +=parseFloat(jQuery('#net_payment_before_tax'+i).val());
                        stop_payment_adj_amount +=parseFloat(jQuery('#new_sche_adjustment_adv'+i).val());
                    }
                    
                    n++;
                    flag++;

                    
                }
                else{
        //         alert
                }
            }
             updated_sd_int=  Math.ceil(updated_sd_float);
			//alert(updated_sd_int);
            jQuery('#checked_schedule_sd_amt').val(updated_sd_text);
            jQuery('#checked_updated_sd_int').val(updated_sd_int);
			//alert(stop_payment_amount);
            jQuery('#checked_stop_cost_center_amt').val(stop_payment_amount);     
            jQuery('#checked_stop_cost_center_adj_amt').val(stop_payment_adj_amount);     
            arr =  jQuery.unique(str_paid_sts.split(','));
            str_paid_sts = arr.join(",");

            if (jQuery.inArray('unpaid_payment', arr)!='-1' && jQuery.inArray('stop_payment', arr)!='-1') {
                
                jQuery('#sche_payment_sts').val('stop_unpaid_payment');

            }else{

                jQuery('#sche_payment_sts').val(str_paid_sts);
            }

            if (jQuery.inArray('advance_rent_payment', arr)!='-1' && jQuery.inArray('unpaid_payment', arr)!='-1') {
                
               alert('Advance And Mature Schedule Payment Should be Seperate');
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
// 28 sep
function foo(num1, num2){
  if (num1 > num2)
    {return num1-num2;}
  else
    {
    	
    	return num2-num1;
    }
}

function sche_set_checked(counter,row_serial)
{
   
             var i=1;
                var arr = jQuery('#sche_counter'+row_serial).val().split(',');
              
    setTimeout(function() {
                for(i=1;i<=counter;i++){
                    var n = i.toString();
                   // alert(jQuery.inArray( n, arr ));
                        if(arr!=''){
                                if(jQuery.inArray( n, arr )!= -1)
                                {
                                    jQuery('#'+row_serial+'sche_check'+i).attr('checked','checked');
                                }else{
                                    jQuery('#'+row_serial+'sche_check'+i).attr('checked', false);
                                }
                        }
                }
    }, 500); 
}

function set_total_payment(counter1,row_serial){
    var counter = jQuery('#schedule_row_count').val();
    var tax_percentage =parseFloat(jQuery('#tax_percentage').val());
    var total_payment = 0;
    var n=1;
    var str1='';
    var total_monthly_rent=0.00;
    var total_arear=0.00;
    var total_others_rent=0.00;
    var total_adjustment_adv=0.00;
    var total_sche_prov=0.00;
    var total_sche_tax=0.00;

    var sum_for_prov= 0;
    var prov_sche_ids= '';
    var non_prov_sche_ids= '';
    var non_prov_net_payment= 0;
    var updated_sd_text='';

    for(i=1;i<=counter;i++){
    //  alert(jQuery('#check'+i).is(':checked'))
            if (jQuery('#'+row_serial+'sche_check'+i).is(':checked')) {
                
                
                            if(n!=1){str1 +=','; updated_sd_text +=',';}
                            //if(i!=1){chk_adj_amt +=',';}
                            str1 +=parseInt(jQuery('#id'+i).val());
                            updated_sd_text +=parseFloat(jQuery('#new_avg_sd_payment'+i).val());
                        
                            
                           total_payment += parseFloat(jQuery('.hidden_final_net_payment'+i).val());

                           total_monthly_rent += parseFloat(jQuery('#new_sche_monthly_rent'+i).val());
                           total_arear += parseFloat(jQuery('#new_sche_arear'+i).val());
                           total_others_rent += parseFloat(jQuery('#new_sche_others_rent'+i).val());
                           total_adjustment_adv += parseFloat(jQuery('#new_sche_adjustment_adv'+i).val());
                           total_sche_prov += parseFloat(jQuery('#new_sche_prov'+i).val());
                           total_sche_tax += parseFloat(jQuery('#new_sche_tax'+i).val());
                            
                            n++;
                        }
                        else{
                //         alert
                        }
       
        }

//alert(updated_sd_text);
// 26 sep
 var single_sche_chk_id = str1.split(',');
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
                          non_prov_net_payment += parseFloat(jQuery('#final_net_payment'+single_sche_chk_id[k]).text().trim());
                          
                      }
                
}

		if(non_prov_sche_ids==''){
		               
		                jQuery('#rent_amount').val(0);
		            }else{
		             
		                jQuery('#rent_amount').val(non_prov_net_payment);
		}
// 26 sep end
        //alert(str1);
        jQuery('#per_sche_monthly_rent').val(total_monthly_rent);
        jQuery('#per_sche_arear').val(total_arear);
        jQuery('#per_sche_others').val(total_others_rent);
        jQuery('#per_sche_adjust').val(total_adjustment_adv);
        jQuery('#per_sche_prov').val(total_sche_prov);
        jQuery('#per_sche_tax').val(total_sche_tax);
        jQuery('#checked_schedule_id').val(str1);
        jQuery('#checked_schedule_sd_amt').val(updated_sd_text);
		//alert(str1);
		return total_payment;
      // alert(parseFloat(total_payment));
}


	  function set_checked_bkp(counter,agree_id)
        {
                var i=1;
                var c=0;
                var arr = jQuery('#sd_counter'+agree_id).val().split(',');
                //alert(arr);
                for(i=1;i<=arr.length;i++){
                    var n = i.toString();
                    jQuery('#sd_check'+arr[c]).attr('checked','checked');
                    
                    c++;
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

	
	 function set_val(agree_id)
        {
                var k=0;
                //alert(agree_id);
                //var arr = jQuery('#checked_adjustment').val().split(',');
                var arr = jQuery('#sd_checked_adjustment_with_hash'+agree_id).val().split(',');
                //alert(arr);
                for(i=0;i<arr.length;i++){
                var n = arr[i].split('#'); 
            
                    k=jQuery('#'+n[0]).val();
                    jQuery('#new_sd_adjust_amount'+k).val(n[1]);  
                    jQuery('#old_adj'+k).val(n[1]);  
                    jQuery("#hidden_amount"+n[0]).val(n[1]);
                
                }

        }

        // 20 sep
function sum_net_amount_edit(counter)
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
            var row_serial=1;
                var n=1;
                var diff=0;
            for(i=1;i<=counter;i++){
                if (jQuery('#'+row_serial+'sche_check'+i).is(':checked')) {
                
                    //new
                    var t=jQuery('#id'+i).val();
                    //alert(t);
               
                    sum += parseFloat(jQuery('.old_hidden_final_net_payment'+i).val());
                  
                    n++;
                    //jQuery('#check'+i).attr('checked', 'checked');
                }
                else{
        //          if(i!=1){str2 +=',';}
                    // str2 +=parseInt(jQuery('#id'+i).val());

                    var t=jQuery('#id'+i).val();
                    if(parseInt(jQuery('#hidden_amount'+t).val())!=0)
                    {
                        if(m!=1){str2 +=',';}
                        str2 +=parseInt(jQuery('#id'+i).val())+'#'+parseInt(jQuery('#hidden_amount'+t).val());
                    m++;
                    } 
                }
            }
            //alert(updated_sd_text);
            
            return sum;
}

   	 
function check_adjust_amount(counter)
{
        //alert('sdf');
        <?php if ($add_edit=='edit'){ ?>
        var i=1;
        //var arr = jQuery('#checked_adjustment').val().split(',');
        var i=parseInt(jQuery('#sche_row_count').val(), 10);
        //     var sche_row_id = jQuery('#sche_row_id').val();   
        //     var row_serial = jQuery('#row_serial').val(); 
        var checked_total_net_sum =  sum_net_amount_edit(i);
		//alert(checked_total_net_sum);
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
                      if(sum_a>checked_total_net_sum)
                    {
                      
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
        <?php }else{ ?>
        var i=1;
        //var arr = jQuery('#checked_adjustment').val().split(',');
        for(i=1;i<=counter;i++){
                if (jQuery('#sd_check'+i).is(':checked')) {
                    if(parseFloat(jQuery('#new_sd_adjust_amount'+i).val())==0){
                        jQuery('#sd_check'+i).prop('checked', false);;

                    }
                    sum_a=parseFloat(jQuery('#new_sd_adjust_amount'+i).val());

                    sum_t=parseFloat(jQuery('#sche_sd_rest'+i).val());

                    if(i==1){
                        if(sum_a!=sum_t){
                            alert("Adjust amount must equal to Total adjust amount in row "+i);
                            jQuery('#new_sd_adjust_amount'+i).focus();
                            return false; 
                        }
                      
                    }
                
                    if(sum_a>sum_t)
                    {
                        alert("Adjust amount exceed Rest amount in row "+i);
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
    //  alert(jQuery('#check'+i).is(':checked'))
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

function set_adjust_amount(counter,rent_id)
            {
            var i=1;
            var sum=0.0;
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
                
                    //new
                    var t=jQuery('#sd_id'+i).val();
                    //alert(t);
                if(parseInt(jQuery('#hidden_amount'+t).val())!=0)
                    {
 

                        //sum_f=parseInt(jQuery('#adj_amount'+i).val())-parseInt(jQuery('#hidden_amount'+t).val());
                        //sum_f=parseInt(jQuery('#old_hidden_amount'+i).val())-parseInt(jQuery('#adj_amount'+i).val());
                        sum_f=parseInt(jQuery('#new_sd_adjust_amount'+i).val())- parseInt(jQuery('#old_hidden_amount'+i).val());
                        //sum_f=parseInt(jQuery('#adj_amount'+i).val())- parseInt(jQuery('#hidden_amount'+t).val());
                        //diff = diff+sum_f;
                        //alert(jQuery('#hidden_amount'+t).val());
                    }else{
                        sum_f=parseInt(jQuery('#new_sd_adjust_amount'+i).val());

                    }
                    //old
                    if(i!=1){str +=',';}
                    str +=i;
                    if(n!=1){str1 +=','; str_h +=',';str_o +=',';}
                    if(n!=1){chk_adj_amt +=',';}
                    str1 +=parseInt(jQuery('#sd_id'+i).val());
                    str_h +=parseInt(jQuery('#sd_id'+i).val())+'#'+parseInt(jQuery('#new_sd_adjust_amount'+i).val())+"#"+sum_f;
                    str_o +=parseInt(jQuery('#sd_id'+i).val())+'#'+parseInt(jQuery('#new_sd_adjust_amount'+i).val())+"#"+parseInt(jQuery('#new_sd_adjust_amount'+i).val());
                    //str1 +=parseInt(jQuery('#id'+i).val())+'#'+parseInt(jQuery('#amount_a'+i).val());
                    //jQuery('#set_adjusted_by_id'+i).val(jQuery('#id'+i).val());
                //  sum += parseFloat(jQuery('#payable'+i).val());
                    sum += parseFloat(jQuery('#new_sd_adjust_amount'+i).val());
                    chk_adj_amt += parseFloat(jQuery('#new_sd_adjust_amount'+i).val());
                    n++;
                    //jQuery('#check'+i).attr('checked', 'checked');
                }
                else{
        //          if(i!=1){str2 +=',';}
                    // str2 +=parseInt(jQuery('#id'+i).val());

                    var t=jQuery('#sd_id'+i).val();
                    if(parseInt(jQuery('#hidden_amount'+t).val())!=0)
                    {
                        if(m!=1){str2 +=',';}
                        str2 +=parseInt(jQuery('#sd_id'+i).val())+'#'+parseInt(jQuery('#hidden_amount'+t).val());
                    m++;
                    } 
                }
            }
            
            jQuery('#sd_checked_adjustment').val(str1);
            //jQuery('#checked_adjustment_with_hash').val(str_h);
            //jQuery('#checked_adjustment_with_hash'+sche_row_id).val(str_h);
            jQuery('#sd_checked_adjustment_with_hash'+rent_id).val(str_h);
            jQuery('#sd_checked_adjustment_amt'+rent_id).val(chk_adj_amt);
            //jQuery('#test_old').val(str_o);
            jQuery('#sd_prev_adj_value_hash'+rent_id).val(str_o);
            //jQuery('#old_value').val(chk_adj_amt);
            jQuery('#sd_unchecked_adjustment'+rent_id).val(str2);
            //alert(jQuery('#checked_adjustment').val())
            //alert(jQuery('#unchecked_adjustment').val())
            jQuery('#sd_counter'+rent_id).val(str);
            // alert(str);
            // alert(str_h);
            // alert(chk_adj_amt);
            return sum;
}

	function select_child()
	{
      var item = jQuery("#parent_gl").jqxComboBox('getSelectedItem'); 
    	 
		 if(item){
	    	 	//alert(args.item.value)
			jQuery.ajax({
	            url: '<?php echo base_url(); ?>index.php/security_deposit/get_child_list',
	            type: "post",
	            data: { parent_gl: item.value },
	            datatype: 'json',
	            async: false,
	            success: function(response){
	            	
	            	var json = jQuery.parseJSON(response); 

	            	jQuery("#child_gl").jqxComboBox({ source: json});
	            },
	            error:   function(model, xhr, options){
	                alert('failed');
	            }
	       	 });
			}
    

	}
	function select_ac_child()
	{
      var item = jQuery("#ac_parent_gl").jqxComboBox('getSelectedItem'); 
    	 
		 if(item){
	    	 	//alert(args.item.value)
			jQuery.ajax({
	            url: '<?php echo base_url(); ?>index.php/security_deposit/get_ac_child_list',
	            type: "post",
	            data: { ac_parent_gl: item.value },
	            datatype: 'json',
	            async: false,
	            success: function(response){
	            	
	            	var json = jQuery.parseJSON(response); 

	            	jQuery("#ac_child_gl").jqxComboBox({ source: json});
	            },
	            error:   function(model, xhr, options){
	                alert('failed');
	            }
	       	 });
			}
    

	}

function per_sche_adj_on_sd_close(sche_row_counter,row_serial){
		var i=1;
		var chk_adj_amt='';
		var str1 = '';
		var n=1;
		            for(i=1;i<=sche_row_counter;i++){
		                if (jQuery('#'+row_serial+'sche_check'+i).is(':checked')){

		                	if(jQuery('#new_paid_sts'+i).val()=='unpaid_payment' || jQuery('#new_paid_sts'+i).val()=='stop_payment'){  // 7 dec 2017
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

function sd_preview_item(agree_id){
      
        jQuery.ajax({
            url: '<?php echo base_url(); ?>index.php/rent_schedule_payment/get_sd_info_edit',
            type: "post",
            data: {agree_id : agree_id },
            //datatype: 'json',
          datatype: 'html',
          success: function(response){
    
                    if(response !=""){

                            jQuery("#data_table2").html(response).show();
                            var i=parseInt(jQuery('#row_count').val());
                  
                               //alert(i);
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




	
	
	
		var cdrMessageDialog;		
		function initCdrMessageDialog(id,gid) {		
			// Define various event handlers for Dialog
			var handleCancel = function() {
				this.hide();
			};
		 var handleCdrMessageSuccess = function(req) {
			var response = eval('(' + req + ')');
			//alert(response.errorMsgs+eval('(' + req + ')')+req)
			if( response.status == 'success') {				
				cdrMessageDialog.hide();
				//reload results
				//reloadCurrentMessages();
				jQuery("#msgArea").val('');
				window.parent.jQuery("#error").show();
				window.parent.jQuery("#error").fadeOut(11500);
				window.parent.jQuery("#error").html('<img align="absmiddle" src="'+baseurl+'images/drag.png" border="0" /> &nbsp;Successfully Saved');	
				window.top.EOL.messageBoard.close();
				
			} else {
				$('cdrMessageErrorMsg').innerHTML = response.errorMsgs;
				$('cdrMessageErrorMsg').style.display = '';
				jQuery("#msgArea").val('');
				window.parent.jQuery("#error").show();
				window.parent.jQuery("#error").fadeOut(11500);
				window.parent.jQuery("#error").html('<img align="absmiddle" src="'+baseurl+'images/drag.png" border="0" /> &nbsp;'+response.errorMsgs);	
				window.top.EOL.messageBoard.close();	
			}
		  };240
		 var handleCdrMessageFailure = function(o) {    	
				showInfoDialog( 'cdrMessagefailuretitle', 'cdrMessagefailurebody', 'WARN' );
		  };
		  
		  var handleSubmit = function() {			
			// var request =  new Request({	
			// 					url: '<?=base_url()?>index.php/user_info/set_default_group_rights', 
			// 					method: 'post',
			// 					data: {'id':id,'gid':gid},
			// 					onSuccess: function(req) {handleCdrMessageSuccess(req);},
			// 					onFailure: function(req) {handleCdrMessageFailure(req);}
			// 				});
			// request.send();

			cdrMessageDialog.hide();
			jQuery("#msgArea").val('');
			window.parent.jQuery("#error").show();
			window.parent.jQuery("#error").fadeOut(11500);
			window.parent.jQuery("#error").html('<img align="absmiddle" src="'+baseurl+'images/drag.png" border="0" /> &nbsp;Successfully Saved');	
			window.top.EOL.messageBoard.close();
	
			$('cdrMessageDialogConfirm').style.display = 'none';
			// $('cdrMessageDialogCancel').style.display = 'none';
			$('loading').style.display = '';
			
		  };
		  var handleSubmit_go_right_page = function() {						
				//window.location.href = "<?=base_url()?>index.php/user_info/set_right/"+id+"/0";

			
				$('cdrMessageDialogConfirm').style.display = 'none';
				$('cdrMessageDialogCancel').style.display = 'none';
				$('loading').style.display = '';
		  };
			
			cdrMessageDialog = new EOL.dialog($('cdrMessageDialogContent'), {position: 'fixed', modal:true, width:470, close:true, id: 'cdrMessageDialog' });
			
			cdrMessageDialog.afterShow = function() {
			
				$$('#cdrMessageDialog #cdrMessageDialogConfirm').addEvent('click',handleSubmit);
				$$('#cdrMessageDialog #cdrMessageDialogCancel').addEvent('click',handleSubmit_go_right_page);
			};
		   
		
			cdrMessageDialog.show();
		}

		
		function confirm_default_rights(id,gid)
		{
			if (!cdrMessageDialog) {
				initCdrMessageDialog(id,gid);
			}			
			cdrMessageDialog.show();
		}
		function reloadCurrentMessages()
		{			
			window.location.reload();
		}
    </script>

    <div  style=" width:100%; margin:auto">
       <form class="form" id="form" method="post" action="#">
       	<input name="id" type="hidden" id="id" value="<?=isset($result->id)?$result->id:''?>"  class="text-input-small" />
		       	
		       	<input name="paid_id" type="hidden" id="paid_id" value="<?=isset($schedule_paid_info->id)?$schedule_paid_info->id:''?>"  class="text-input-small" />
		       	<input name="rent_id" type="hidden" id="rent_id" value="<?=isset($result_agree_info->id)?$result_agree_info->id:''?>"  class="text-input-small" />
		       	<input name="rent_agree_ref" type="hidden" id="rent_agree_ref" value="<?=isset($result_agree_info->agreement_ref_no)?$result_agree_info->agreement_ref_no:''?>"  class="text-input-small" />
		       	<input name="fin_ref_no" type="hidden" id="fin_ref_no" value="<?=isset($result_agree_info->fin_ref_no)?$result_agree_info->fin_ref_no:''?>"  class="text-input-small" />
		       	<input name="rent_amount" type="hidden" id="rent_amount" value="<?=isset($schedule_paid_info->rent_amount)?$schedule_paid_info->rent_amount:''?>"  class="text-input-small" />
		       	<input name="schedule_row_count" type="hidden" id="schedule_row_count" value="<?=isset($schedule_row_count)?$schedule_row_count:''?>"  class="text-input-small" />
			    <input name="agree_counter" type="hidden" id="agree_counter" value=""  class="text-input-small" />
                <input name="tax_percentage" type="hidden" id="tax_percentage" value="<?=isset($tax_percentage->tax_amount)?$tax_percentage->tax_amount:''?>"  class="text-input-small" />
                      

			  	<input id="per_sche_monthly_rent" name="per_sche_monthly_rent" class="per_sche_monthly_rent_cls" type="hidden" value=""> 
                <input id="per_sche_others" name="per_sche_others" class="per_sche_others_cls" type="hidden" value=""> 
                <input id="per_sche_arear" name="per_sche_arear" class="per_sche_arear_cls" type="hidden" value=""> 
                <input id="per_sche_adjust" name="per_sche_adjust" class="per_sche_adjust_cls" type="hidden" value=""> 
                <input id="per_sche_provision" name="per_sche_provision" class="per_sche_provision_cls" type="hidden" value=""> 
                <input id="per_sche_sd" name="per_sche_sd" class="per_sche_sd_cls" type="hidden" value=""> 
                <input id="per_sche_prov" name="per_sche_prov" class="per_sche_tax_cls" type="hidden" value=""> 
                <input id="per_sche_tax" name="per_sche_tax" class="per_sche_tax_cls" type="hidden" value=""> 
                <input id="per_sche_net_payment" name="per_sche_net_payment" class="per_sche_net_payment_cls" type="hidden" value=""> 
                    
                 <!--  7 dec  -->
 					<input id="sche_payment_sts" name="sche_payment_sts" class="sche_payment_sts" type="hidden" value="<?=isset($schedule_paid_info->sche_payment_sts)?$schedule_paid_info->sche_payment_sts:''?>"> 
                    <input id="checked_stop_cost_center_amt" name="checked_stop_cost_center_amt" class="checked_stop_cost_center_amt" type="hidden" value="<?=isset($schedule_paid_info->stop_cost_center_amt)?$schedule_paid_info->stop_cost_center_amt:''?>"> 
                    <input id="checked_stop_cost_center_adj_amt" name="checked_stop_cost_center_adj_amt" class="checked_stop_cost_center_adj_amt" type="hidden" value=""> 
                   


			<!--   sd popup data-->
                    <input name="sd_checked_adjustment_with_hash" type="hidden" id="sd_checked_adjustment_with_hash<?php echo $result_agree_info->id; ?>" value="<?=isset($schedule_paid_info->sd_ids_hash)?$schedule_paid_info->sd_ids_hash:'' ?>"  class="text-input-small" />
                    <input name="sd_checked_adjustment" type="hidden" id="sd_checked_adjustment" value="<?=isset($schedule_paid_info->checked_schedule_sd_ids)?$schedule_paid_info->checked_schedule_sd_ids:''?>"  class="text-input-small" />
                     <!-- <input name="checked_adjustment_with_hash" type="hidden" id="checked_adjustment_with_hash" value="<?=isset($result->bill_ids_hash)?$result->bill_ids_hash:''?>"  class="text-input-small" /> -->
                    <input name="sd_checked_adjustment_amt" type="hidden" id="sd_checked_adjustment_amt<?php echo $result_agree_info->id; ?>" value="<?=isset($schedule_paid_info->sd_checked_adjustment_amt)?$schedule_paid_info->sd_checked_adjustment_amt:''?>"  class="text-input-small" />
                    <input name="sd_unchecked_adjustment1" type="hidden" id="sd_unchecked_adjustment<?php echo $result_agree_info->id; ?>" value=""  class="text-input-small" />
                    <input name="sd_prev_adj_value1" type="hidden" id="sd_prev_adj_value<?php echo $result_agree_info->id; ?>" value="<?=isset($result_agree_info->id)?$result_agree_info->id:''?>"  class="text-input-small" />
                    <input name="sd_prev_adj_value_hash1" type="hidden" id="sd_prev_adj_value_hash<?php echo $result_agree_info->id; ?>" value="<?=isset($result_agree_info->id)?$result_agree_info->id:''?>"  class="text-input-small" />
                    <input name="sd_counter" type="hidden" id="sd_counter<?php echo $result_agree_info->id; ?>" value="<?php echo $schedule_paid_info->checked_sd_id_serial; ?>"  class="text-input-small" />
                    <input name="prev_sd_amount" type="hidden" id="prev_sd_amount" value="<?=isset($schedule_paid_info->sd_adjust_amt)?$schedule_paid_info->sd_adjust_amt:''?>"  class="text-input-small" />
                    
<!--  sd popup data  -->

<!--                        schedule popup data-->
<!--                      <input name="sche_total_row<?php echo $i; ?>" type="hidden" id="sche_total_row<?php echo $i; ?>" value=""  class="text-input-small" />
                     <input name="sche_counter<?php echo $i; ?>" type="hidden" id="sche_counter<?php echo $i; ?>" value=""  class="text-input-small" />
                     
                     <input id="checked_schedule_adjustment_amt<?php echo $i; ?>" name="checked_schedule_adjustment_amt<?php echo $i; ?>" class="checked_schedule_adjustment_amt" type="hidden" value="<?php echo $new_net_payment; ?>"> 
                     <input id="checked_schedule_net_amt<?php echo $i; ?>" name="checked_schedule_net_amt<?php echo $i; ?>" class="checked_schedule_net_amt" type="hidden" value="<?php echo $new_net_payment; ?>">  -->
                     <input id="chk_sche_id_on_sd_close1" name="chk_sche_id_on_sd_close1" class="chk_sche_id_on_sd_close" type="hidden" value=""> 
                   <input id="checked_schedule_id" name="checked_schedule_id" class="checked_schedule_id" type="hidden" value="<?php echo $schedule_paid_info->checked_schedule_ids; ?>"> 
                   <input id="old_checked_schedule_id" name="old_checked_schedule_id" class="old_checked_schedule_id" type="hidden" value="<?php echo $schedule_paid_info->checked_schedule_ids; ?>"> 
                   <!--    schedule popup data end -->  


              <? if($add_edit=='add' || $add_edit=='edit' ){ ?>  
           
      

<!-- 12 july -->
           

<?php 
$i=1;
$html ='';
	$incr_type= '';
	    $incr_type_val= $result_agree_info->increment_type;
	    if($incr_type_val==1){$incr_type='No Increment';}
	    elseif($incr_type_val==2){$incr_type='Every Year Basis';}
	    elseif($incr_type_val==3){$incr_type='Only One Time';}
	    elseif($incr_type_val==4){$incr_type='Fixed Increment setup';}

$html.= '<div style="padding-left:10px;">';
$html.= '<p class="summery_class" ><b>Payment Summery</b></p>'; 
		//$html.= '<p class="summery_class"><b>Period :</b> '.date_format($start_date,"d/m/Y").' to '.date_format($end_date,"d/m/Y").' ('.$date_diff.'), '.$pp_str.' Basis</p>'; 
	    
	    $html.= '<p class="summery_class"><b>Reference No: </b>'.$result_agree_info->agreement_ref_no.'</p>';
        $html.= '<p class="summery_class"><b>Cost Center: </b>'.$cost_center_name->name.'</p>';
        $html.= '<p class="summery_class"><b>Location : </b>'.$result_agree_info->location_name.'</p>';
        $html.= '<p class="summery_class"><b>Landlords : </b>'.$result_agree_info->landlord_names.'</p>';
        $html.= '<p class="summery_class"><b>Increment Type : </b>'.$incr_type.'</p>';
		$html.= '<p class="summery_class"><b>Monthly Rent :</b> '.$result_agree_info->monthly_rent.'</p>'; 
		$html.= '<p class="summery_class"><b>Advance payment : </b>'.$result_agree_info->total_advance.'</p>'; 
		//$html.= '<p class="summery_class" style="display:none;"><b>Monthly Adjustment :</b> '.$rent_adjust_data->percent_dir_val.'</p>'; 

$html.= '</div>';

$calculated_total=0;
$row_serial = 1;
$rent_id = $result_agree_info->id;
$prov_amount= 0.00;

$html .='<div style="text-align:center;  cursor:pointer" onclick="sd_preview_item('.$rent_id.')" ><img align="center" src="'.base_url().'images/view_detail.png">    <input name="new_sd_amount'.$rent_id.'" type="text"  id="new_sd_amount'.$rent_id.'" value="'.$schedule_paid_info->sd_adjust_amt.'" class="text-input-small " placeholder="SD Adjust" readonly /></div><br >';


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
		
		<th style="text-align:center;"> Provision Adjust</th>
		<th style="text-align:center;"> Tax </th>
		<th style="text-align:center;"> Net Payment </th>
		<th style="text-align:center; display:none;"> Unadjusted Advance rent</th>
		<th style="text-align:center;"> Remarks</th>';

		$html .='<tbody id="">';

$updated_sd_text = '';
$updated_sd_int=0;
$tax_slab_rate = $this->agreement_model->get_tax_slab_rate();
$slab_count= count($tax_slab_rate);

foreach($rent_schedule_info as $row){ 

		
	//$new_net_payment= ($row->monthly_rent_amount + $row->total_others_amount + $row->area_amount) - ($row->adjustment_adv + $row->tax_amount);
	// 20 sep
   // $net_payment_before_tax = ($row->monthly_rent_amount + $row->total_others_amount + $row->area_amount) - ($row->adjustment_adv);
    $net_payment_before_tax = ($row->monthly_rent_amount + $row->total_others_amount + $row->area_amount);
    for($si=0;$si<$slab_count;$si++){
                if($net_payment_before_tax >= $tax_slab_rate[$si]->min_amt && $net_payment_before_tax <= $tax_slab_rate[$si]->max_amt){
                    $tax_rate=$tax_slab_rate[$si]->tax_percent;
                }
            }
            
    $tax_amount = ($net_payment_before_tax * $tax_rate)/100;
    $new_net_payment = ($net_payment_before_tax - $tax_amount - $row->adjustment_adv) - $row->adjust_sec_deposit;

	$old_new_net_payment = $new_net_payment;
		if(in_array($row->id,$sche_checked_schedule_ids)){
	

		// 26 sep	
			$new_net_payment= $new_net_payment- $row->temp_sec_deposit;
			$checked='checked';
			$sd_amt= $row->temp_sec_deposit;
			if($updated_sd_text !=''){
				$updated_sd_text .= ',';
			}
			$updated_sd_text .= $row->temp_sec_deposit;
			$updated_sd_int += $row->temp_sec_deposit;

		}else{
			$checked='';
			$sd_amt= 0;
		}
		
        $prov_amount=0;
		//$unadjust = $unadjust - $row->adjustment_adv ; 

		$date=date_create("$row->schedule_strat_dt");
		$d=  date_format($date,"d-M-y");

		if($row->remarks !=''){$style_tr='background-color: lightgreen !important;'; }else{$style_tr='';} 
		$sche_payment_type='';
		if($row->paid_sts !='paid'){

					if(date("Y-m-d") > $row->maturity_dt) {  // Matured
						if($row->paid_sts =='advance')
							{ $paid_sts='Matured (Advance)';
							  $sche_payment_type='advance_rent_payment'; }
						else if($row->paid_sts =='unpaid' && $result_agree_info->agree_current_sts_id=='6')
							{ $paid_sts='Matured (Stop and Unpaid)';
							  $sche_payment_type='stop_cost_center'; }
						else if($row->paid_sts =='stop' && $result_agree_info->agree_current_sts_id=='6')
							{ $paid_sts='Matured (Stoped)'; }
						else if($row->paid_sts =='stop' && $result_agree_info->agree_current_sts_id=='5')
							{ $paid_sts='Matured (Released and Unpaid)';
							  $sche_payment_type='stop_payment'; }
					   else if($row->paid_sts =='unpaid' && $result_agree_info->agree_current_sts_id=='5')
							{ $paid_sts='Matured';
							  $sche_payment_type='unpaid_payment'; } 
						else{$paid_sts=''; $sche_payment_type='unknown';} 
						 
						$prov_amount = 0;
						
					}else{   // not matured
						if($row->paid_sts =='advance'){ $paid_sts='Not Matured (Advance)'; }else{$paid_sts='Not Matured';}

					}

				
					if($row->paid_sts =='closed'){
						$paid_sts='Closed';
					}

			
			 
		}else{

			$paid_sts='Paid';
		}

				$html .='<tr style="border: 1px solid black ; '.$style_tr.'" >
				<input name="id'.$i.'" type="hidden" id="id'.$i.'" value="'.$row->id.'"  class="text-input-small" />';   
				$html .='<td style="text-align:center;">'; 
			// 	if($paid_sts=='Paid'){
			// 	$html .='<img align="center"  title="Paid"  src="'.base_url().'images/paid1.png" style="width:25px; hight:20px; ">';
			// }else if($paid_sts=='Not Matured'){
			// 	$html .='<img align="center"  title="Paid"  src="'.base_url().'images/not_applicable.png" style="width:25px; hight:20px; ">';
			// }else{
   			//     $html .='<input type="checkbox" id="'.$row_serial.'sche_check'.$i.'" name="'.$row_serial.'sche_check'.$i.'" value="'.$row_serial.'sche_check'.$i.'" '.$checked.' ></td>';
			// 	}
				if($paid_sts=='Paid'){
					if($checked==''){
						$html .='<img align="center"  title="Paid"  src="'.base_url().'images/not_applicable.png" style="width:25px; hight:20px; ">';	
					}else{
						$html .='<img align="center"  title="Paid"  src="'.base_url().'images/paid1.png" style="width:25px; hight:20px; ">';
					}
				}
				else if($paid_sts=='Matured' || $paid_sts=='Matured (Advance)' || $paid_sts=='Matured (Stop and Unpaid)' || $paid_sts=='Matured (Released and Unpaid)'){
					if($checked==''){
						$html .='<img align="center"  title="Paid"  src="'.base_url().'images/not_applicable.png" style="width:25px; hight:20px; ">';	
					}else{
						$html .='<input type="checkbox" id="'.$row_serial.'sche_check'.$i.'" name="'.$row_serial.'sche_check'.$i.'" value="'.$row_serial.'sche_check'.$i.'"  '.$checked.' ></td>';
					}
				}

				else if($paid_sts=='Closed'){
					$html .='<img align="center"  title="Paid"  src="'.base_url().'images/not_applicable.png" style="width:25px; hight:20px; ">';
				}
				else if($paid_sts=='Not Matured' || $paid_sts=='Not Matured (Advance)' || $paid_sts=='Matured (Stoped)' || $paid_sts==''){ 
					if($checked==''){
						$html .='<img align="center"  title="Paid"  src="'.base_url().'images/not_applicable.png" style="width:25px; hight:20px; ">';	
					}else{
						$html .='<img align="center"  title="Paid"  src="'.base_url().'images/not_applicable.png" style="width:25px; hight:20px; ">';
					}
				}
				else{
					if($checked==''){
						$html .='<img align="center"  title="Paid"  src="'.base_url().'images/not_applicable.png" style="width:25px; hight:20px; ">';	
					}else{
	                	$html .='<input type="checkbox" id="'.$row_serial.'sche_check'.$i.'" name="'.$row_serial.'sche_check'.$i.'" value="'.$row_serial.'sche_check'.$i.'" '.$checked.' ></td>';
					}
				}
				$html .='<td style="text-align:center;">'.$paid_sts.'</td>';
				$html .='<td style="text-align:center;">'.$d.'</td>';
				$html .='<td style="text-align:center;">'.$row->monthly_rent_amount.'<input type="hidden" name="new_sche_monthly_rent'.$i.'"  id="new_sche_monthly_rent'.$i.'" value="'.$row->monthly_rent_amount.'"></td>';
				$html .='<td style="text-align:center;">'.$row->total_others_amount.'<input type="hidden" name="new_sche_others_rent'.$i.'"  id="new_sche_others_rent'.$i.'" value="'.$row->total_others_amount.'"></td>';
				$html .='<td style="text-align:center;">'.$row->area_amount.'<input type="hidden" name="new_sche_arear'.$i.'"  id="new_sche_arear'.$i.'" value="'.$row->area_amount.'"></td>';
				$html .='<td style="text-align:center;" id="final_adj_payment'.$row->id.'">'.$row->adjustment_adv.'</td>
						<input type="hidden" name="new_sche_adjustment_adv'.$i.'"  id="new_sche_adjustment_adv'.$i.'" value="'.$row->adjustment_adv.'">';
				$html .='<td style="text-align:center;" id="avg_sd_payment'.$row->id.'" class="avg_sd_payment'.$i.'">'.$sd_amt.'</td>
						<input type="hidden" name="new_sche_sec_dep'.$i.'"  id="new_sche_sec_dep'.$i.'" value="'.$sd_amt.'">
						<input type="hidden" class="new_avg_sd_payment'.$row->id.'" name="new_avg_sd_payment'.$i.'"  id="new_avg_sd_payment'.$i.'" value="'.$sd_amt.'">';
				
				//$html .='<td style="text-align:center;"></td>';
				
				$html .='<td style="text-align:center;" class="final_prov_payment'.$i.'" id="final_prov_payment'.$row->id.'">'.$prov_amount.'</td>
						<input type="hidden" name="new_sche_prov'.$i.'"  id="new_sche_prov'.$i.'" value="'.$prov_amount.'">
						<input type="hidden" name="old_hidden_final_prov_payment'.$row->id.'"  id="old_hidden_final_prov_payment'.$row->id.'" value="'.$prov_amount.'">';
				
				$html .='<td style="text-align:center;">'.$tax_amount.'</td>
						<input type="hidden" name="new_sche_tax'.$i.'"  id="new_sche_tax'.$i.'" value="'.$tax_amount.'">';
				//$html .='<td style="text-align:center;" id="final_net_payment'.$row->id.'">'.number_format("$new_net_payment",2).' </td>
				$html .='<td style="text-align:center;" id="final_net_payment'.$row->id.'" class="final_net_payment'.$i.'" >'.$new_net_payment.' </td>

						 <input type="hidden" name="hidden_final_net_payment'.$row->id.'"  id="hidden_final_net_payment'.$row->id.'" class="hidden_final_net_payment'.$i.'" value="'.$new_net_payment.'">
						 <input type="hidden" name="old_hidden_final_net_payment'.$row->id.'"  class="old_hidden_final_net_payment'.$i.'" id="old_hidden_final_net_payment'.$row->id.'" value="'.$old_new_net_payment.'">
						 <input type="hidden" name="new_sche_adjust_amount'.$i.'"  id="new_sche_adjust_amount'.$i.'" value="'.$old_new_net_payment.'">
						 <input type="hidden" name="net_payment_before_tax'.$i.'"  id="net_payment_before_tax'.$i.'" value="'.$net_payment_before_tax.'">
						 <input type="hidden" name="new_paid_sts'.$i.'"  id="new_paid_sts'.$i.'" value="'.$sche_payment_type.'">
						 <input type="hidden" class="new_sche_net_amount'.$row->id.'" name="new_sche_net_amount'.$i.'"  id="new_sche_net_amount'.$i.'" value="'.$old_new_net_payment.'">';
				//$html .='<td style="text-align:center;">'.$unadjust.'</td>';
				$html .='<td style="text-align:center; display:none;">'.$row->unadjusted_adv_rent.'</td>';
				$html .='<td style="text-align:center;">'.$row->remarks.'</td>';
			
				
				
				//$html .='<td style="text-align:center;"><input type="text"  name="" id="others_total'.$i.'" value="" class="incr_input"  readonly/></td>';
				//$html .='<td style="text-align:center;"><input type="text"  name="end_date'.$i.'" id="rent_end_date" value="'.$sch_end_date.'" class="incr_input"  readonly/></td>';
				$html .='</tr>';
		$i++;	
	}



		$html .='</tbody></table>';
		$html .='<input name="checked_schedule_sd_amt" type="hidden" id="checked_schedule_sd_amt" value="'.$updated_sd_text.'"  class="text-input-small" />';
		$html .='<input name="checked_updated_sd_int" type="hidden" id="checked_updated_sd_int" value="'.$updated_sd_int.'"  class="text-input-small" />';
                   
		//$html .='<div style="text-align:center;  cursor:pointer" onclick="sd_preview_item('.$rent_id.')" ><img align="center" src="'.base_url().'images/view_detail.png"></div>    <input name="sd_adjust_amount'.$row->id.'" type="text"  id="sd_adjust_amount'.$row->id.'" value="" class="text-input-small amount" readonly />';
		
		//$html .='Total Amount : <input id="" class="" type="text" value="">'; 
        $row_count=$i-1;
		$html .= '<input name="sche_row_count" type="hidden" id="sche_row_count" value="'.$row_count.'"  class="text-input-small" />';
		$html .='<center><input id="sendButton" class="buttonStyle" type="button" value="Update"></center>';
		$html .='<br />';
		$html .='<br />';
		echo $html;

 ?>
                 
      
	</div>




			  <? } else { }	 ?>
				  <? if($type!='view'){} ?>
           
        </form>
       
    </div>
    
     	
     	<div id="window"  style=" margin: 10px auto">
     	<div id="windowHeader">
                    <span>
                     
                    </span>
       </div> 
     		<div style="">
     			
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
                    <center><input type="button" value="Close" id="sd_closeButton"  class="buttonStyle" /></center>
                </div> 
    </div>

    
    <div id="cdrMessageDialogContent"  style="display:none">
          <div class="hd"><h2 class="conf">Security Deposit successfully created.<br>
		  </div>
          	<div class="bd">
              <div class="inlineError" id="cdrMessageErrorMsg" style="display:none"></div>
            </div>
            <a class="btn-small btn-small-normal" id="cdrMessageDialogConfirm"><span>Ok</span></a> 
          <!--   <a class="btn-small btn-small-secondary" id="cdrMessageDialogCancel"><span>Cancel</span></a>  -->
            <span id="loading" style="display:none">Please wait... <img src="<?=base_url()?>images/loader.gif" align="bottom"></span>
   </div>