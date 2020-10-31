<?php $user_group_id= $this->session->userdata['user']['user_work_group_id']; ?>
<style type="text/css">
.buttonStyle {
    width: 80px !important; 
}
.closeButton{
	bottom: 2px;
    float: right;
    position: absolute;
    right: 2px;
    width: 60px !important;
}
</style>
<style>
.jqx-window-header {
	border-bottom:none !important;
	}

.service_style {
    font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
    width: 100%;
    border-collapse: collapse;
    margin: 0 0px;
}

.service_style td, .service_style th {
    font-size: 1em;
    border: 1px solid #4197c7;
    padding: 3px 7px 2px 7px;
}
.bill_details td, .bill_details th {
	border:none !important;
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
.incr_input{width:40px !important;}
.inc_area_cal{width:65px !important;}
.innerTable .headrow{
text-align:center;
font-weight:bold;
background-color:#C5C5C5;
}
</style>
<div id="container">	
	<div id="body"  >
    <!--Customization Start-->
	<script type="text/javascript">
	 	// initialize the popup window and buttons.
	 	jQuery(document).on("click",'#hideWindowButton',function () {
                jQuery('#popupWindow').jqxWindow('close');
        });
     


        var count=0; var maxrow = 0; var displayrow= 0; inc = 0; decr = 0;
		function clearCount() { count=0; maxrow = 0;displayrow= 0;}
		


         jQuery(document).ready(function($) {
            // prepare the data

			     jQuery(document).on("keypress",".number",function (evt) {
			        var charCode = (evt.which) ? evt.which : evt.keyCode;
			        if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46) 
			           return false;
			   
			        return true;            
			   });

            var theme = theme;		
            var source =
            {
                 datatype: "json",
                 datafields: [
					 { name: 'id', type: 'int'},
					 //{ name: 'vendor_id', type: 'string'},
					 { name: 'agreement_ref_no', type: 'string'},
					 { name: 'fin_ref_no', type: 'string'},
					 { name: 'landlord_names', type: 'string'},
					 { name: 'location_name', type: 'string'},
					 { name: 'location_owner', type: 'string'},
					 { name: 'cost_center', type: 'string'},
					 { name: 'ddif', type: 'string'},
					 { name: 'rent_start_dt', type: 'string'},
					 { name: 'agree_exp_dt', type: 'string'},
					 { name: 'point_of_payment', type: 'string'},
					 { name: 'monthly_rent', type: 'number'},
					 { name: 'total_advance', type: 'number'},
					 { name: 'total_advance_paid', type: 'number'},
					  
					 { name: 'sts', type: 'number'}, 
					 { name: 'dept_v_by', type: 'number'}, 
					 { name: 'fin_v_by', type: 'number'}, 
					 { name: 'stf_by', type: 'number'}, 
					 { name: 'ack_by', type: 'number'}, 
					 { name: 'halt_by', type: 'number'}, 
					 { name: 'rhalt_by', type: 'number'}, 
					 { name: 'close_by', type: 'number'}, 
					 { name: 'close_release_by', type: 'number'}, 
					 { name: 'agree_current_sts_id', type: 'number'}, 
					 { name: 'agree_pervious_sts_id', type: 'number'}, 
					 { name: 'agr_current_sts', type: 'string'}, 
					 // { name: 'v_sts', type: 'number'},
					 // { name: 'stf_sts', type: 'number'},
					 // { name: 'fin_v_sts', type: 'number'},
					 // { name: 'current_agree_sts', type: 'number'},
					 // { name: 'halt_sts', type: 'number'},
					 // { name: 'rhalt_sts', type: 'number'},
				
				
					
                ],
				addrow: function (rowid, rowdata, position, commit) {
                    // synchronize with the server - send insert command
                    // call commit with parameter true if the synchronization with the server is successful 
                    //and with parameter false if the synchronization failed.
                    // you can pass additional argument to the commit callback which represents the new ID if it is generated from a DB.
                    commit(true);
                },
                deleterow: function (rowid, commit) {
                	
                    // synchronize with the server - send delete command
                    // call commit with parameter true if the synchronization with the server is successful 
                    //and with parameter false if the synchronization failed.
                    commit(true);
                },
                updaterow: function (rowid, newdata, commit) {
                    // synchronize with the server - send update command
                    // call commit with parameter true if the synchronization with the server is successful 
                    // and with parameter false if the synchronization failed.
                    commit(true);
                },
			    // url: '<?=base_url()?>index.php/billreceive/grid',
			    url: '<?=base_url()?>index.php/agreement/grid',
				cache: false,
				timeout:100,
				filter: function()
				{
					// update the grid and send a request to the server.
					jQuery("#jqxgrid").jqxGrid('updatebounddata', 'filter');
				},
				sort: function()
				{
					// update the grid and send a request to the server.
					jQuery("#jqxgrid").jqxGrid('updatebounddata', 'sort');
				},
				root: 'Rows',
				beforeprocessing: function(data)
				{		
					if (data != null)
					{
						//alert(data[0].TotalRows)
						source.totalrecords = data[0].TotalRows;					
					}
				}
				
            };		
			
			
		    var dataadapter = new jQuery.jqx.dataAdapter(source, {
					loadError: function(xhr, status, error)
					{	
						
						alert("Server not found");
						jQuery("body").prepend("<div class=\"overlay\"></div>");

						jQuery(".overlay").css({
						    "position": "absolute", 
						    "width": jQuery(document).width(), 
						    "height": jQuery(document).height(),
						    "z-index": 99999
						}).fadeTo(0, 0.30);
						return false;
					}
				}
			);
			
			 
			var user_group_id ='<?=$user_group_id ?>';
			var columnCheckBox = null;
            var updatingCheckState = false;
            // initialize jqxGrid. Disable the built-in selection.
            var celledit = function (row, datafield, columntype) {
                var checked = jQuery('#jqxgrid').jqxGrid('getcellvalue', row, "available");
                if (checked == false) {
                    return false;
                };
            };
			
			var cellsrenderer = function (row, column, value, defaultHtml, columnSettings, rowData) {
				var agree_current_sts_id = rowData.agree_current_sts_id;
				if(agree_current_sts_id < 5){
					return '<div style="text-align: center; margin-top: 5px; background-color:red;">' + value + '</div>';
				}else{
					return '<div style="text-align: center; margin-top: 5px;">' + value + '</div>';
				}
				
			}
			var columnsrenderer = function (value) {
				return '<div style="text-align: center; margin-top: 5px;">' + value + '</div>';
			}
			var amount = function (value) {
				return '<div style="text-align: right;margin-top: 5px;">' + value + '</div>';
			}
			// initialize jqxGrid
            jQuery("#jqxgrid").jqxGrid(
            {		
                width:'99%',
				height:350,
				rowsheight: 27,
			    source: dataadapter,
                theme: theme,
				filterable: true,
				sortable: true,
				//autoheight: true,
				pagesize:10,
				pageable: true,
				virtualmode: true,
				editable: true,
				enablehover: true,
                enablebrowserselection: true,
                selectionmode: 'none',
				rendergridrows: function(obj)
				{
					 return obj.data;    
				},
				
			    columns: [
			    	  { text: 'Bill ID', datafield: 'id',hidden:true,  editable: false,  width: '45' },			    	  
					  <? if(DELETE==1){?>
						  { text: 'D', menu: false,renderer: columnsrenderer, columntype: 'number',sortable: false, width: 40, 
						  	cellsrenderer: function (row) {							
								editrow = row;
								var dataRecord = jQuery("#jqxgrid").jqxGrid('getrowdata', editrow);
								if(dataRecord.agree_current_sts_id >= 5 && user_group_id!=1){	
									return '<div style="text-align:center;margin-top:11%"><strong>M</strong></div>';
								}
								else if(dataRecord.agree_current_sts_id >1 && user_group_id==2){	
										return '<div style="text-align:center;margin-top:11%"><strong>M</strong></div>';
								}
								else if(dataRecord.agree_current_sts_id >2 && user_group_id==4){	
										return '<div style="text-align:center;margin-top:11%"><strong>M</strong></div>';
								}else{
									
										return '<div style="text-align:center;cursor:pointer" onclick="delete_action('+dataRecord.id+','+editrow+')" ><img align="center" src="<?=base_url()?>images/delete.png"></div>';
								}
								
								
							}
						  },
					  <? }?>

				

					  <? if(EDIT==1){?>	
					  { text: 'EDIT', menu: false,renderer: columnsrenderer, columntype: 'number',   sortable: false, width:45, 
					  	cellsrenderer: function (row) {							
							editrow = row;
							var dataRecord = jQuery("#jqxgrid").jqxGrid('getrowdata', editrow);
							
										
							if(dataRecord.agree_current_sts_id >= 5){	
									return '<div style="text-align:center;margin-top:11%"><strong>M</strong></div>';
							}
							else if(dataRecord.agree_current_sts_id >1 && user_group_id==2){	
									return '<div style="text-align:center;margin-top:11%"><strong>M</strong></div>';
							}
							else if(dataRecord.agree_current_sts_id >2 && user_group_id==4){	
									return '<div style="text-align:center;margin-top:11%"><strong>M</strong></div>';
							}else{
								
									return '<div style="text-align:center;cursor:pointer" onclick="editt('+dataRecord.id+','+editrow+')" ><img align="center" src="<?=base_url()?>images/edit.png"></div>';
							}
						}
					  },
					  <? }?>



				<? if(VERIFY==1){ ?> // admin verify
							  { text: 'V', menu: false, datafield: 'admin_verify',  align:'center', columntype: 'number',  sortable: true, width: 45, 
								cellsrenderer: function (row) {	  				
									editrow = row;

									var dataRecord = jQuery("#jqxgrid").jqxGrid('getrowdata', editrow);
									if(dataRecord.agree_current_sts_id ==1 && (user_group_id==4 || user_group_id==1)){	
											return '<div style="text-align:center;  cursor:pointer" onclick="verify_popup('+dataRecord.id+','+editrow+',\'admin_verify\')" ><img align="center" src="<?=base_url()?>images/confirm.png"></div>';
									}
									else if(dataRecord.agree_current_sts_id >=2){	
											return '<div style="text-align:center;margin-top:11%">V</div>';
									}
									else{	
											return '<div style="text-align:center;margin-top:11%"><strong>NA</strong></div>';
									}									
								  }
							  },{ text: 'STF', menu: false, datafield: 'stf',  align:'center', columntype: 'number',  sortable: true, width: 45, 
								cellsrenderer: function (row) {					
									editrow = row;
									var dataRecord = jQuery("#jqxgrid").jqxGrid('getrowdata', editrow);
									if(dataRecord.agree_current_sts_id ==2 && (user_group_id==4 || user_group_id==1)){	
											return '<div style="text-align:center;  cursor:pointer" onclick="verify('+dataRecord.id+','+editrow+',\'stf\');" ><img align="center" src="<?=base_url()?>images/assign.jpg"></div>';
									}
									else if(dataRecord.agree_current_sts_id >=3){	
											return '<div style="text-align:center;margin-top:11%">STF</div>';
									}
									else{	
											return '<div style="text-align:center;margin-top:11%"><strong>NA</strong></div>';
									}
									
								  }
							  },
							<? } ?>


							    <? if(ACKNOWLEDGE==1){?>
								  { 
					                    text: 'ACK',menu: false,datafield: 'ack',renderer: columnsrenderer,columntype: 'number',sortable: false, width: 34, cellsrenderer: function (row) {
					      					editrow = row;
											var dataRecord = jQuery("#jqxgrid").jqxGrid('getrowdata', editrow);	
											if(dataRecord.agree_current_sts_id ==3 && (user_group_id==3 || user_group_id==1)){	
													return '<div style="text-align:center; cursor:pointer" onclick="action(\''+dataRecord.id+'\','+editrow+',\'ack\')" ><img align="center" src="<?=base_url()?>images/approved.png"></div>';
											}
											else if(dataRecord.agree_current_sts_id >=4){	
													return '<div style="text-align:center;margin-top:11%">ACK</div>';
											}
											else{	
													return '<div style="text-align:center;margin-top:11%"><strong>NA</strong></div>';
											}
					               		}
					               },
						 <? }?>
						 

						 <? if(FIN_VERIFY==1){ ?>
							  { text: 'FV', menu: false, datafield: 'fin_verify',  align:'center', columntype: 'number',  sortable: true, width: 45, 
								cellsrenderer: function (row) {	 	 			
									editrow = row;
									var dataRecord = jQuery("#jqxgrid").jqxGrid('getrowdata', editrow);
									if(dataRecord.agree_current_sts_id ==4 && (user_group_id==7 || user_group_id==1)){	
											return '<div style="text-align:center;  cursor:pointer" onclick="verify_popup('+dataRecord.id+','+editrow+',\'fin_verify\')" ><img align="center" src="<?=base_url()?>images/confirm.png"></div>';
									}
									else if(dataRecord.agree_current_sts_id >=5){	
											return '<div style="text-align:center;margin-top:11%"> FV</div>';
									}
									else{	
											return '<div style="text-align:center;margin-top:11%"><strong>NA</strong></div>';
									}
									
									
								  }
							  },

							  { text: 'STM', menu: false,renderer: columnsrenderer, sortable: false, width: 60, 
					  				cellsrenderer: function (row){	 	 			
									editrow = row;
									var dataRecord = jQuery("#jqxgrid").jqxGrid('getrowdata', editrow);
									if(dataRecord.agree_current_sts_id ==4 && (user_group_id==7 || user_group_id==1)){	
											return '<div style="text-align:center;  cursor:pointer" onclick="action(\''+dataRecord.id+'\','+editrow+',\'stm\')" ><img align="center" src="<?=base_url()?>images/sendcib.png"></div>';
									}
									else if(dataRecord.agree_current_sts_id >=5){	
											return '<div style="text-align:center;margin-top:11%"> NA</div>';
									}
									else{	
											return '<div style="text-align:center;margin-top:11%"><strong>NA</strong></div>';
									}
									
									
								  }
							  },

					<? } ?>


					<? if(HALT==1){?>
								  { 
					                    text: 'Stop', menu: false,renderer: columnsrenderer,columntype: 'number',datafield: 'halt_sts',sortable: false, width: 50, cellsrenderer: function (row) {
					      					editrow = row;
											var dataRecord = jQuery("#jqxgrid").jqxGrid('getrowdata', editrow);
											if(dataRecord.agree_current_sts_id ==5 && (user_group_id==7 || user_group_id==1) && dataRecord.ddif<0){	
													return '<div title="Stop" style="text-align:center;  cursor:pointer" onclick="action(\''+dataRecord.id+'\','+editrow+',\'stop\')" ><img align="center" src="<?=base_url()?>images/Block.png"></div>';
											}
											else if(dataRecord.agree_current_sts_id ==6){	
													return '<div style="text-align:center;margin-top:11%"> HLT</div>';
											}
											else{	
													return '<div style="text-align:center;margin-top:11%"><strong>NA</strong></div>';
											}											
					               		}
					               },
					               { 
					                    text: 'Release', menu: false,renderer: columnsrenderer,columntype: 'number',sortable: false, width: 60, cellsrenderer: function (row) {
					      					editrow = row;
											var dataRecord = jQuery("#jqxgrid").jqxGrid('getrowdata', editrow);
											if(dataRecord.agree_current_sts_id ==6 && (user_group_id==7 || user_group_id==1) && dataRecord.ddif<0){	
													return '<div title="Stop" style="text-align:center;  cursor:pointer" onclick="action(\''+dataRecord.id+'\','+editrow+',\'release\')" ><img align="center" src="<?=base_url()?>images/UnBlock.png"></div>';
											}											
											else{	
													return '<div style="text-align:center;margin-top:11%"><strong>NA</strong></div>';
											}
											
					               		}
					               },
					                <? }?>


					        <? if(CLOSE==1){?>
								  { 
					                    text: 'Early Trn.', menu: false,renderer: columnsrenderer,columntype: 'number',datafield: 'close',sortable: false, width: 55, cellsrenderer: function (row) {
					      					editrow = row;
											var dataRecord = jQuery("#jqxgrid").jqxGrid('getrowdata', editrow);
											
											if(dataRecord.agree_current_sts_id ==5 && (user_group_id==7 || user_group_id==1) && dataRecord.ddif<0 ){	
													return '<div style="text-align:center;  cursor:pointer" onclick="action(\''+dataRecord.id+'\','+editrow+',\'close\')" ><img align="center" src="<?=base_url()?>images/link_red.png"></div>';
											}											
											else{	
													return '<div style="text-align:center;margin-top:6%"><strong>NA</strong></div>';
											}
											
					               		}
					               },
					      //          
					   <? }?>
					

                 		{ text: 'VIEW', menu: false,renderer: columnsrenderer, sortable: false, width: 50, 
					  	cellsrenderer: function (row) {							
							editrow = row;
							var dataRecord = jQuery("#jqxgrid").jqxGrid('getrowdata', editrow);						
						 	return '<div style="text-align:center;  cursor:pointer" onclick="preview_item('+dataRecord.id+','+editrow+')" ><img align="center" src="<?=base_url()?>images/view_detail.png"></div>';
						  }
					  },

 					<? if(FIN_VERIFY==1){?>
					  { text: 'Arrear', menu: false,renderer: columnsrenderer, sortable: false, width: 60, 
					  	cellsrenderer: function (row){							
							editrow = row;
							var dataRecord = jQuery("#jqxgrid").jqxGrid('getrowdata', editrow);	
							var location_owner = dataRecord.location_owner;
							var agree_current_sts_id = dataRecord.agree_current_sts_id;
							if (dataRecord.agree_current_sts_id == 5  && (user_group_id==7 || user_group_id==1) && dataRecord.ddif<0) {
								return '<div style="text-align:center;  cursor:pointer" onclick="arear_preview_item('+dataRecord.id+','+editrow+',\''+location_owner+'\','+agree_current_sts_id+')" ><img align="center" src="<?=base_url()?>images/area2.png"></div>';
							}
							else{
								return '<div style="text-align:center;margin-top:11%"><strong>NA</strong></div>';
							}
						 	
						}
					  },
	             	<? }?> 	


	             	<? if(FIN_VERIFY==1){?>
					  { text: 'Modify', menu: false,renderer: columnsrenderer, sortable: false, width: 60, 
					  	cellsrenderer: function (row){							
							editrow = row;
							var dataRecord = jQuery("#jqxgrid").jqxGrid('getrowdata', editrow);	
							var location_owner = dataRecord.location_owner;
							var agree_current_sts_id = dataRecord.agree_current_sts_id;
							if (dataRecord.agree_current_sts_id == 5  && (user_group_id==7 || user_group_id==1) && dataRecord.ddif<0) {
								//return '<div style="text-align:center;  cursor:pointer" onclick="arear_preview_item('+dataRecord.id+','+editrow+',\''+location_owner+'\','+agree_current_sts_id+')" ><img align="center" src="<?=base_url()?>images/modify.png"></div>';
								return '<div style="text-align:center;cursor:pointer" onclick="modify('+dataRecord.id+','+editrow+')" ><img align="center" src="<?=base_url()?>images/modify.png"></div>';
							}
							else{
								return '<div style="text-align:center;margin-top:11%"><strong>NA</strong></div>';
							}
						 	
						}
					  },
	             	<? }?>  

	             	<? if(FIN_VERIFY==1){?>
					  { text: 'Extend', menu: false,renderer: columnsrenderer, sortable: false, width: 50, 
					  	cellsrenderer: function (row){							
							editrow = row;
							var dataRecord = jQuery("#jqxgrid").jqxGrid('getrowdata', editrow);	
							var location_owner = dataRecord.location_owner;
							var agree_current_sts_id = dataRecord.agree_current_sts_id;
							if (dataRecord.agree_current_sts_id == 5  && (user_group_id==7 || user_group_id==1) && dataRecord.ddif<0) {
								//return '<div style="text-align:center;  cursor:pointer" onclick="arear_preview_item('+dataRecord.id+','+editrow+',\''+location_owner+'\','+agree_current_sts_id+')" ><img align="center" src="<?=base_url()?>images/modify.png"></div>';
								return '<div style="text-align:center;cursor:pointer" onclick="extend('+dataRecord.id+','+editrow+')" ><img align="center" src="<?=base_url()?>images/extend.png"></div>';
							}
							else{
								return '<div style="text-align:center;margin-top:11%"><strong>NA</strong></div>';
							}
						 	
						}
					  },
	             	<? }?>

					  { text: 'Reference No', datafield: 'agreement_ref_no', editable: false, width: '150', cellsrenderer: cellsrenderer },
					  { text: 'Finance Reference', datafield: 'fin_ref_no', editable: false, width: '120' },
					  
                     
                      { text: 'Location Name', datafield: 'location_name', editable: false, width: '210' },
					  { text: 'Cost Center', datafield: 'cost_center', editable: false, width: '200' },
					  { text: 'Land Lord', datafield: 'landlord_names', editable: false, width: '200' }, 
                      { text: 'Point of Payment', datafield: 'point_of_payment', editable: false, width: '110' },                      
                      { text: 'Start Date', datafield: 'rent_start_dt',align:'right',renderer: columnsrenderer,columntype: 'number', editable: false,width: '111',cellsalign: 'right'},
                      { text: 'Expiry Date', datafield: 'agree_exp_dt',align:'right',renderer: columnsrenderer,columntype: 'number', editable: false,width: '111',cellsalign: 'right'},
                      { text: 'Monthly Rent', datafield: 'monthly_rent', editable: false, width: '110', cellsalign: 'right' },
		              
                      { text: 'Total Advance', datafield: 'total_advance', editable: false, width: '150', cellsalign: 'right' },
                      { text: 'Paid Amount', datafield: 'total_advance_paid', editable: false, width: '150', cellsalign: 'right' },
                      { text: 'Status', datafield: 'agr_current_sts', editable: false, width: '110' }, 	
                     // { text: 'Tax', datafield: 'tax_rate', editable: false, width: '130' },
                           
                //       { text: 'Vat', datafield: 'vat_rate',renderer: columnsrenderer,columntype: 'number', editable: false, width: '130'},
                //       { text: 'Remarks', menu: false,datafield: 'remarks',align:'right',renderer: columnsrenderer,columntype: 'number', editable: false,width: '100',cellsalign: 'right'},
                      
           
                       
                      
                  ]
            });

// 3 august

jQuery("#increment_closeButton").click(function(){

		jQuery('#click_sts').val(1);

	     jQuery(".common_inc_cls").prop('disabled',false);
	     jQuery(".common_other_cls").prop('disabled',false);
      
        var incr_row_number = jQuery('#incr_row_number').val();
        if(jQuery('#counter_others').val()!=''){
        	var counter_others_arr = jQuery('#counter_others').val().split(',');
        	var counter_others = counter_others_arr.length;
        }else{
        	counter_others=0;
        }

        
        var total_sche_count = jQuery('#total_sche_count').val();

        set_incr_value(incr_row_number,counter_others);
        update_monthly_val(total_sche_count);
        //alert(counter_others);  
        jQuery('#jqxwindow3').jqxWindow('close');
});


jQuery("#sendButton_sche").click(function() {
	
	var arear_sts= parseInt(jQuery('#arear_sts').val());
      				// if(arear_sts==1){
      				// 	alert('Area amount already provided for this Agreement!');
      				// 	return false;
      				// }

	var rent_agree_id = jQuery('#rent_agree_id').val();
	var data = check_agree_rent_in_adv(rent_agree_id);
	if(parseInt(data) > 0){
			alert('Rent in Advance Exists for this Agreement!');
		    return false;
		}

	var total_sche_count = jQuery('#total_sche_count').val();
	var paid_total_diff = jQuery('#paid_total_diff').val();
	var total_area_amount = 0;
	var total_adv_amount = 0;
	var counter=0;
	var n=0;
	//alert(parseInt(paid_total_diff));
	for(var i=1; i<=total_sche_count; i++){
		if(jQuery('#payment_sts_tr'+n).val()=='unpaid'){ 

			var area_amount =  parseFloat(jQuery("#area_amount"+n).val());
			var monthly_rent_chk = parseFloat(jQuery("#monthly_rent_tr"+n).text());
			var others_rent_chk = parseFloat(jQuery("#others_rent_tr"+n).text());
			var rent_adv_chk =  parseFloat(jQuery("#rent_adv"+n).val());
			var tax_tr_chk = parseFloat(jQuery("#tax_tr"+n).text());
			var sd_chk =  parseFloat(jQuery("#adjust_sec_deposit"+n).val());

			var net_chk= (monthly_rent_chk + others_rent_chk + area_amount)-(rent_adv_chk + sd_chk + tax_tr_chk);

			if(!jQuery.isNumeric(area_amount)) {
			    alert('Invalid arear value in row '+i);
				return false;
			}
			if(parseInt(net_chk) < 0){
				//alert(parseInt(net_chk));
				alert('Net amount is Negative in row '+i);
					return false;

			}

			total_area_amount = total_area_amount + parseFloat(jQuery("#area_amount"+n).val());
			
		}
		total_adv_amount = total_adv_amount + parseFloat(jQuery("#rent_adv"+n).val());
		n++;
	}
	// alert(jQuery("#paid_adv_amt").val());
	// alert(total_adv_amount);
	  // commented on 11 march 2019
		// if(parseInt(jQuery("#paid_adv_amt").val())!=parseInt(total_adv_amount)){
		// 		alert('Total Adjustment Amount is not equal to Paid Advance Amount ');
		// 		return false;
  //       }
    

    // commented on 2 oct 2018

  //       if(parseInt(total_area_amount)==0){
		// 	alert('Total Area amount can not be zero ');
		// 	return false;
		// }else{
            
  //            if(parseInt(paid_total_diff)==0){
  //             }
            
  //           if(parseInt(paid_total_diff) != parseInt(total_area_amount)){
  //           }
  //       }

	    // alert(jQuery('#click_sts').val() );       
    	var postdata = jQuery('#form2').serialize();
    	jQuery("#sendButton_sche").hide();
    	jQuery("#loading_arrear").show();
    	jQuery.ajax({
            type: "POST",
            cache: false,
            url: "<?=base_url()?>index.php/agreement/update_sche_info",
            data : postdata,
            async : false,
            datatype: "json",
            success: function(response){
                var json = jQuery.parseJSON(response);
                if(json.Message!='OK')
                {
                    jQuery("#sendButton_sche").show();
                    jQuery("#loading_arrear").hide();
                    alert(json.Message);
                    return false
                }else{

					jQuery('#jqxwindow2').jqxWindow('close'); 
					jQuery("#sendButton_sche").show();
                    jQuery("#loading_arrear").hide();
					jQuery("#jqxgrid").jqxGrid('updatebounddata');
                }
            }
        });
         
                
    });


// 24 august

jQuery("#verify_btn").click(function() {

	var verify_type = jQuery('#agr_verify_type').val(); // admin_verify

	jQuery("#verify_btn").hide();
    jQuery("#loading_img").show();
	        
    var postdata = jQuery('#form4').serialize();
    jQuery.ajax({
            type: "POST",
            cache: false,
            url: "<?=base_url()?>index.php/agreement/new_agr_verify",
            data : postdata,
            async : false,
            datatype: "json",
            success: function(response){
                var json = jQuery.parseJSON(response);


    			// onSuccess: function(req) {v_handleVerifyMessageSuccess(req);},
				// onFailure: function(req) {handleVerifyMessageFailure(req);}
                if(json.status!='success')
                {
                    jQuery("#verify_btn").show();
                    jQuery("#loading_img").hide();
                    alert(json.errorMsgs);
                    return false
                }else{

					jQuery('#jqxwindow4').jqxWindow('close'); 
					// alert(baseurl);
					// alert(row);
					//jQuery("#error").html('<img align="absmiddle" src="'+baseurl+'images/drag.png" border="0" /> &nbsp;Successfully Verified');	
					// jQuery("#error").html('<img align="absmiddle" src="'+baseurl+'images/drag.png" border="0" /> &nbsp;Successfully Acknowledged');	
					// jQuery("#jqxgrid").jqxGrid('setcellvalue',row,'fin_verify',5);
					   jQuery("#jqxgrid").jqxGrid('updatebounddata');
                }
            }
        });
         
});

			jQuery("#popupWindow").jqxWindow({   
				height:450,width: 650, resizable: false,  isModal: true, autoOpen: false, cancelButton: jQuery("#hideWindowButton"), modalOpacity: 0.90           
			});
	         // open the popup window when the user clicks a button.
	         var offset = jQuery("#jqxgrid").offset();
            jQuery("#popupWindow").jqxWindow({ position: { x: parseInt(offset.left) + 260, y: parseInt(offset.top) + 10 } });
            jQuery("#Cancel").jqxButton({ theme: theme });

				jQuery('#jqxwindow').jqxWindow({height: 550, width: 900, maxWidth:1000, maxHeight:1000, autoOpen: false,cancelButton: jQuery('#cancelButton')});					
				jQuery('#jqxwindow2').jqxWindow({height: 550, width: 900, maxWidth:1000, maxHeight:1000, autoOpen: false,cancelButton: jQuery('#cancelButton')});					
				jQuery('#jqxwindow3').jqxWindow({height: 550, width: 900, maxWidth:1000, maxHeight:1000, autoOpen: false,cancelButton: jQuery('#cancelButton')});					
				jQuery('#jqxwindow4').jqxWindow({height: 550, width: 900, maxWidth:1000, maxHeight:1000, autoOpen: false,cancelButton: jQuery('#cancelButton')});					
        });


//  3 august

function incr_func() {
	
  jQuery("#incr_icon").click(function(){
	 
      				var arear_sts= parseInt(jQuery('#arear_sts').val());
      				// if(arear_sts==1){
      				// 	alert('Area amount already provided for this Agreement!');
      				// 	return false;
      				// }


                    var agreement_id = jQuery('#rent_agree_id').val();
                    var rent_start_dt = jQuery('#rent_start_dt').val();
                    var agree_exp_dt = jQuery('#agree_exp_dt').val();
                    var location_owner = jQuery('#location_owner').val();
                    var monthly_rent_amt = jQuery('#monthly_rent_amt').val();
                    var click_sts = parseInt(jQuery('#click_sts').val(), 10);
                 
                    if (agreement_id){

                        jQuery.ajax({
                            url: '<?php echo base_url(); ?>index.php/agreement/increment_ajax_for_area',
                                    type: "post",
                                    data: { rent_id:agreement_id, rent_start_dt:rent_start_dt, agree_exp_dt:agree_exp_dt, location_owner:location_owner, monthly_rent_amt:monthly_rent_amt,arear_sts:arear_sts  },
                                    // datatype: 'json',
                                    datatype: "html",
                                    success: function(response){

                                        if (response != ""){

                                                jQuery("#data_table3").html(response).show();
                                                //  var i = parseInt(jQuery('#row_count').val());
                                                //alert(click_sts);
                                                 if(click_sts==1){
                                                   //set_adjusted_val(adjust_adv_type);
                                                   set_incremented_val();
                                                
                                                 }
                                        }
                                    }

                        });
                    }

                   // jQuery('#click_sts').val(1);
                    jQuery('#jqxwindow3').jqxWindow('open');
					jQuery('#jqxwindow3').jqxWindow('bringToFront');
          

  });
}


/*verify action message */
        		var verifyMessageDialog;
		function initVerifyMessageDialog() {	

			// Define various event handlers for Dialog
			var handleCancel = function() {
				this.hide();
			}; 
			var v_handleVerifyMessageSuccess = function(req) {
				var response = eval('(' + req + ')');
				
					if( response.status == 'success') {	
						verifyMessageDialog.hide();		
						var row =jQuery("#verifyrow").val();	
						var type =jQuery("#type").val();	
						jQuery("#error").show();
						jQuery("#error").fadeOut(11500);
						jQuery("#jqxgrid").jqxGrid('clearselection');
						if(type=='admin_verify'){
							jQuery("#error").html('<img align="absmiddle" src="'+baseurl+'images/drag.png" border="0" /> &nbsp;Successfully verifyed');	
							jQuery("#jqxgrid").jqxGrid('setcellvalue',row,'admin_verify',2);
							jQuery("#jqxgrid").jqxGrid('updatebounddata');

							//<div style="text-align:center;margin-top:11%">V</div>
							 
						} else if(type=='stf'){
							jQuery("#error").html('<img align="absmiddle" src="'+baseurl+'images/drag.png" border="0" /> &nbsp;Sent To Finance');	
							jQuery("#jqxgrid").jqxGrid('setcellvalue',row,'stf',3);
							jQuery("#jqxgrid").jqxGrid('updatebounddata');
							 
						}
						else if(type=='fin_verify'){
							jQuery("#error").html('<img align="absmiddle" src="'+baseurl+'images/drag.png" border="0" /> &nbsp;Successfully verifyed');	
							jQuery("#jqxgrid").jqxGrid('setcellvalue',row,'fin_verify',5);
							jQuery("#jqxgrid").jqxGrid('updatebounddata');
							 
						}
						else {
							jQuery("#error").html('<img align="absmiddle" src="'+baseurl+'images/drag.png" border="0" /> &nbsp;Successfully sent');	
							jQuery("#jqxgrid").jqxGrid('setcellvalue',row,'dept_v_sts',0);
							 
						}

						$('verifyMessageDialogConfirm').style.display = '';
						$('verifyMessageDialogCancel').style.display = '';
						$('loadingVerify').style.display = 'none';

					} else {


						$('verifyMessageErrorMsg').innerHTML = response.errorMsgs;
						$('verifyMessageErrorMsg').style.display = '';
						$('loadingVerify').style.display = 'none';
					}
		  	};
			var handleVerifyMessageFailure = function(o) {    	
					showInfoDialog( 'deleteMessagefailuretitle', 'deleteMessagefailurebody', 'WARN' );
			};
		  
		  	var handleSubmit = function() {
		  		var val=jQuery("#verifyno").val();
		  		var type=jQuery("#type").val();
		  		var memo_ref=jQuery("#memo_ref").val();
			  	var request =  new Request({
			  									url: '<?=base_url()?>index.php/agreement/verify', 
												method: 'post',
												data: {id:val,type:type,memo_ref:memo_ref},
												onSuccess: function(req) {v_handleVerifyMessageSuccess(req);},
												onFailure: function(req) {handleVerifyMessageFailure(req);}
											});
				request.send();
				$('verifyMessageDialogConfirm').style.display = 'none';
				$('verifyMessageDialogCancel').style.display = 'none';
				$('loadingVerify').style.display = '';	
		  };
			//alert(id)
			verifyMessageDialog = new EOL.dialog($('verifyMessageDialogContent'), {position: 'fixed', modal:true, width:470, close:true, id: 'verifyMessageDialog' });
			verifyMessageDialog.afterShow = function() {
				$$('#verifyMessageDialog #verifyMessageDialogConfirm').addEvent('click',handleSubmit);
				$$('#verifyMessageDialog #verifyMessageDialogCancel').addEvent('click',function() {verifyMessageDialog.hide();});
			};		
			verifyMessageDialog.show();
		}

		// ack + halt + unhalt data msg

			var verifyMessageDialog1;
		function initVerifyMessageDialog_ack_halt_unhalt() {	

			// Define various event handlers for Dialog
			var handleCancel = function() {
				this.hide();
			}; 
			var handleVerifyMessageSuccess = function(req) {
				var response = eval('(' + req + ')');
				
					if( response.status == 'success') {	
						verifyMessageDialog1.hide();		
						var row =jQuery("#verifyrow").val();	
						var type =jQuery("#type").val();	
						jQuery("#error").show();
						jQuery("#error").fadeOut(11500);
						jQuery("#jqxgrid").jqxGrid('clearselection');
						if(type=='ack'){
							jQuery("#error").html('<img align="absmiddle" src="'+baseurl+'images/drag.png" border="0" /> &nbsp;Successfully Acknowledged');	
							jQuery("#jqxgrid").jqxGrid('setcellvalue',row,'ack',4);
							jQuery("#jqxgrid").jqxGrid('updatebounddata');

						} else if(type=='segregate'){
							jQuery("#error").html('<img align="absmiddle" src="'+baseurl+'images/drag.png" border="0" /> &nbsp;Successfully Sent');	
							jQuery("#jqxgrid").jqxGrid('setcellvalue',row,'memo_sts',7);
						} else if(type=='unhalt'){
							jQuery("#error").html('<img align="absmiddle" src="'+baseurl+'images/drag.png" border="0" /> &nbsp;Successfully released');	
							jQuery("#jqxgrid").jqxGrid('setcellvalue',row,'halt_sts',0);
							jQuery("#jqxgrid").jqxGrid('updatebounddata');
						}else if(type=='close'){
							jQuery("#error").html('<img align="absmiddle" src="'+baseurl+'images/drag.png" border="0" /> &nbsp;Agreement Closed');	
							jQuery("#jqxgrid").jqxGrid('setcellvalue',row,'close',0);
							jQuery("#jqxgrid").jqxGrid('updatebounddata');
						}else if(type=='unclose'){
							jQuery("#error").html('<img align="absmiddle" src="'+baseurl+'images/drag.png" border="0" /> &nbsp;Agreement Released');	
							jQuery("#jqxgrid").jqxGrid('setcellvalue',row,'close',1);
							jQuery("#jqxgrid").jqxGrid('updatebounddata');
						}else if(type=='stm'){
							// jQuery("#error").html('<img align="absmiddle" src="'+baseurl+'images/drag.png" border="0" /> &nbsp;Agreement Sent to Maker');	
							// jQuery("#jqxgrid").jqxGrid('setcellvalue',row,'close',1);
							jQuery("#jqxgrid").jqxGrid('updatebounddata');
						}
						else {
							jQuery("#error").html('<img align="absmiddle" src="'+baseurl+'images/drag.png" border="0" /> &nbsp;Successfully halted');	
							jQuery("#jqxgrid").jqxGrid('setcellvalue',row,'halt_sts',7);
							jQuery("#jqxgrid").jqxGrid('updatebounddata');
						}

						$('verifyMessageDialogConfirm').style.display = '';
						$('verifyMessageDialogCancel').style.display = '';
						$('loadingVerify').style.display = 'none';

					} else {


						$('verifyMessageErrorMsg').innerHTML = response.errorMsgs;
						$('verifyMessageErrorMsg').style.display = '';
						$('loadingVerify').style.display = 'none';
					}
		  	};
			var handleVerifyMessageFailure = function(o) {    	
					showInfoDialog( 'deleteMessagefailuretitle', 'deleteMessagefailurebody', 'WARN' );
			};
		  
		  	var handleSubmit = function() {
		  		//var val=jQuery("#verifyno").val();
		  		var type=jQuery("#type").val();
		  		var id=jQuery("#id").val();
		  		var comments=jQuery("#send_comments").val();

		  		if (type == 'stop' && comments.trim() == '') {
		  			alert("Please write stop reason on comment box.");
		  			return false;
		  		}
		  		
			  	var request =  new Request({
			  									url: '<?=base_url()?>index.php/agreement/ack_action', 
												method: 'post',
												data: {id:id,type:type,comments:comments},
												onSuccess: function(req) {
													
													handleVerifyMessageSuccess(req);},
												onFailure: function(req) {handleVerifyMessageFailure(req);}
											});
				request.send();
				$('verifyMessageDialogConfirm').style.display = 'none';
				$('verifyMessageDialogCancel').style.display = 'none';
				$('loadingVerify').style.display = '';
				
		  };
			//alert(id)
			verifyMessageDialog1 = new EOL.dialog($('verifyMessageDialogContent'), {position: 'fixed', modal:true, width:470, close:true, id: 'verifyMessageDialog1' });
			verifyMessageDialog1.afterShow = function() {

				$$('#verifyMessageDialog1 #verifyMessageDialogConfirm').addEvent('click',handleSubmit);
				$$('#verifyMessageDialog1 #verifyMessageDialogCancel').addEvent('click',function() {verifyMessageDialog1.hide();});
			};		
			verifyMessageDialog1.show();
		}
		
	function preview_item(val,row){
	  //alert(val);
	   jQuery.ajax({
            url: '<?php echo base_url(); ?>index.php/agreement/get_schedule_info',
            type: "post",
            data: { rent_id : val },
            //datatype: 'json',
            datatype: 'html',
            success: function(response){
            	if (response != "") { 

				jQuery('#jqxwindow').jqxWindow({height: 550, width: 1000, maxWidth:1000, maxHeight:1000, autoOpen: false,cancelButton: jQuery('#cancelButton')});					    
 				jQuery('#jqxwindow').jqxWindow('open');
                jQuery("#item_info").html(response).show();
                            //jQuery(".incr_per_val").prop('disabled', true);
                           
                        }
                
            },
            error:   function(model, xhr, options){
                alert('failed');
            },
        });

		}


// 24 august

function verify_popup(val,row,type){
	 //alert(type);
	   jQuery.ajax({
            url: '<?php echo base_url(); ?>index.php/agreement/show_info_for_verify',
            type: "post",
            data: { rent_id : val, verify_type : type },
            //datatype: 'json',
            datatype: 'html',
            success: function(response){
            	if (response != "") { 

			       jQuery('#jqxwindow4').jqxWindow({height: 550, width: 1000, maxWidth:1000, maxHeight:1000, autoOpen: false,cancelButton: jQuery('#cancelButton')});					    
 				
	 			   jQuery('#jqxwindow4').jqxWindow('open');
	 			   jQuery("#verify_btn").show();
                   jQuery("#loading_img").hide();
	               jQuery("#item_info4").html(response).show();
                    //jQuery(".incr_per_val").prop('disabled', true);verify_btn
                           
                }else{
                	alert('This Entry Cannot Verify !!!');
                }
                
            },
            error:   function(model, xhr, options){
                alert('failed');
            },
        });

}


// 2 august

function arear_preview_item(val,row,location_owner,agree_current_sts_id){
	 
	 if(location_owner=='own'){
	 	alert('Arear Not applicable for Own Location');
	 	return false;
	 }
	 // if(agree_current_sts_id!=5){
	 // 	alert('Aregeemrnt is not verified !');
	 // 	return false;
	 // }
	   jQuery.ajax({
            url: '<?php echo base_url(); ?>index.php/agreement/get_schedule_info_for_arear',
            type: "post",
            data: { rent_id : val },
            //datatype: 'json',
            datatype: 'html',
            success: function(response){
            	if (response != "") { 

			       jQuery('#jqxwindow2').jqxWindow({height: 550, width: 1000, maxWidth:1000, maxHeight:1000, autoOpen: false,cancelButton: jQuery('#cancelButton')});					    
 				//jQuery('#jqxwindow').jqxWindow('open');
	 			   jQuery('#jqxwindow2').jqxWindow('open');
	               jQuery("#item_info1").html(response).show();

                }
                
            },
            error:   function(model, xhr, options){
                alert('failed');
            },
        });

}

function incr_edit_jquery_1(i,incr_row_number)
{



// initial 

    var incr_type_read= jQuery(".inc_select"+i+" option:selected").val();
                if (incr_type_read=="per_rent" || incr_type_read=="dir_rent"){ 
                jQuery("#rent_amount_val"+i).attr("readonly", false);
               // jQuery("#rent_amount_val"+i).val("0.00");
                }else if(incr_type_read==""){

                    jQuery("#cal_rent_val"+i).val(monthly_rent);
                    jQuery("#rent_amount_val"+i).val("0.00");
                    jQuery("#rent_amount_val"+i).attr("readonly", true);
    }
    //jQuery(".incre_tr_cls").hide();
    //jQuery("#monthly_rent").val();
  var monthly_rent= jQuery("#monthly_rent_amt").val();


  jQuery(".inc_select"+i).on('focus', function () {
        
        previous = this.value;
    }).change(function() { 

    	
    	var incr_type_read= jQuery(".inc_select"+i+" option:selected").val();
    	// alert(incr_type_read);
            if (incr_type_read=="per_rent" || incr_type_read=="dir_rent"){ // if dir or per are selected
            	var t=0;
                	if(i>0){
                		t = i-1;
                		var cal_val=  jQuery("#cal_rent_val"+t).val();

                	}else{
                		t=0;
                		var cal_val=  monthly_rent;
                	}
                	var old_val = parseFloat(jQuery("#rent_amount_val"+i).val());
                	var deducted_val_for_percent = parseFloat(jQuery("#cal_rent_val"+i).val()) - cal_val;

            for(var start=i; start<incr_row_number; start++){
            	//jQuery("#cal_rent_val"+start).val(cal_val);

            	if(previous=='dir_rent'){
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

            }else if(incr_type_read==""){
            	var t=0;
                	if(i>0){
                		t = i-1;
                		var cal_val=  parseFloat(jQuery("#cal_rent_val"+t).val());

                	}else{
                		t=0;
                		var cal_val=  parseFloat(monthly_rent);

                	}
                	var old_val = parseFloat(jQuery("#rent_amount_val"+i).val());
                	var deducted_val_for_percent = parseFloat(jQuery("#cal_rent_val"+i).val()) - cal_val;


                	for(var start=i; start<incr_row_number; start++){

                		if(previous=='dir_rent'){
                			jQuery("#rent_amount_val"+i).val("0.00");
                			var deducted_val = parseFloat(jQuery("#cal_rent_val"+start).val()) - old_val;
                			jQuery("#cal_rent_val"+start).val(parseFloat(deducted_val));
			            
			               	jQuery("#rent_amount_val"+i).attr("readonly", true);

			               }else{ // per_rent

			               	jQuery("#rent_amount_val"+i).val("0.00");
                			var deducted_val = parseFloat(jQuery("#cal_rent_val"+start).val()) - deducted_val_for_percent;
                		    jQuery("#cal_rent_val"+start).val(parseFloat(deducted_val));
			                jQuery("#rent_amount_val"+i).attr("readonly", true);

			               }

                	}

            }




    });


            jQuery("#rent_amount_val"+i).on("change",function(){
               var incr_type= jQuery(".inc_select"+i+" option:selected").val();
               //alert(incr_type);
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
                            jQuery(".tot_val_"+a).val(total_monthly_rent_amount.toFixed(2));
                     }

                     for(var e=0; e<i;e++){
		                	jQuery("#rent_amount_val"+e).attr("readonly", true);
		                	jQuery(".inc_select"+e).prop('disabled', 'disabled');
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
                      for(var a=j;a<=k;a++){
             
                   		 var each_val = jQuery(".tot_val_"+a).val();	
                   		  var each_final = +mid_val + +incr_per;
                         jQuery(".tot_val_"+a).val(each_final.toFixed(2));
                         //jQuery(".tot_val_"+a).val(total_monthly_rent_amount.toFixed(2));
                      }
                     
                      for(var e=0; e<i;e++){
		                	jQuery("#rent_amount_val"+e).attr("readonly", true);
		                	jQuery(".inc_select"+e).prop('disabled', 'disabled');
            			}

                }

            // var jj =  i;
            // alert(jj);
            // for(var i=0; i<j;i++){
            //     jQuery("#rent_amount_val"+i).attr("readonly", true);
            //     jQuery(".inc_select"+i).prop('disabled', 'disabled');
            // }

     });

// });


}


function incr_edit_jquery_3(i,j,k,year_diff)
{

        var others_amount_val= jQuery("#others_type_amount_percentage"+k).val();
		var others_incr_type_read = jQuery(".others_amount_type"+i+j+" option:selected").val();
		// alert(others_incr_type_read);
        if (others_incr_type_read=="per_otr" || others_incr_type_read=="dir_otr"){ 
            jQuery("#others_amount_val"+i+j).attr("readonly", false);
        }else if(others_incr_type_read==""){
                jQuery("#others_amount_val"+i+j).attr("readonly", true);
        }

}

function incr_edit_jquery_5(i,j,k,year_diff)
{
var monthly_rent=1000;
			var ii= i;
            var jj= j;
            var kk= k;
            var year_diff= year_diff;

        jQuery(".others_amount_type"+i+j).on('focus', function () {
        // alert(i);
        // alert(j);
        // alert(k);
        // alert(year_diff);
            
            previous = this.value;
            }).change(function() { 
            	var i = ii;
            	var j = jj;
            	var k = kk;
            	//var year_diff = year_diff;
	
                    	 alert(i);
				         alert(j);
				         alert(k);
				         alert(year_diff);
				var others_amount_val= jQuery("#others_type_amount_percentage"+k).val();
                var incr_type= jQuery(".others_amount_type+i+j option:selected").val(); 
                if(incr_type==""){

                	var previous_val = previous;
                    var i_otr=i;
                    var j_otr=j;

                    var t=0;
                                if(i_otr>0){
                                    t = i_otr-1;
                                    var cal_val=  parseFloat(jQuery(".other_tot_val_"+t+j_otr).val());

                                }else{
                                    t=0;
                                    var cal_val=  parseFloat(others_amount_val);

                                }
                    var old_val = parseFloat(jQuery("#others_amount_val"+i+j).val());
                    var deducted_val_for_percent = parseFloat(jQuery(".other_tot_val_"+i+j).val()) - cal_val;
                    var jj=i;
                    var kk=year_diff;
                    for(var a=jj;a<=kk;a++){
                                var deducted_val = parseFloat(jQuery(".other_tot_val_"+a+jj).val()) - parseFloat(old_val);
                                //alert(deducted_val);
                                //alert(deducted_val_for_percent);
                                if(previous_val=='dir_otr'){
                                    
                                    jQuery(".other_tot_val_"+a+jj).val(parseFloat(deducted_val));

                                }else{
                                    
                                    var deducted_val_percent = parseFloat(jQuery(".other_tot_val_"+a+jj).val()) - deducted_val_for_percent;
                                    jQuery(".other_tot_val_"+a+jj).val(parseFloat(deducted_val_percent));

                                }
                              
                    }
                                     
                }else{

                }        
               
                 var others_amount_val= jQuery("#others_type_amount_percentage"+k).val();
                            var others_incr_type_read = jQuery(".others_amount_type+i+j option:selected").val();

                            if (others_incr_type_read=='per_otr' || others_incr_type_read=='dir_otr'){ 
                            jQuery("#others_amount_val"+i+j).attr("readonly", false);
                            }else if(others_incr_type_read==''){

                                //jQuery(".others_input_value' . $j . '").val(others_amount_val);
                                jQuery("#others_amount_val"+i+j).val("0.00");
                                jQuery("#others_amount_val"+i+j).attr("readonly", true);
                            }

                            
                           //  var old_val = (jQuery("#others_amount_val"+i+j).val());
                          
                           // var others_amount_val= jQuery("#others_type_amount_percentage"+k).val();
                           // var others_incr_type_read = jQuery(".others_amount_type+i+j option:selected").val();
                           // if (others_incr_type_read=='per_otr' || others_incr_type_read=='dir_otr'){ 

                           //  jQuery("#others_amount_val"+i+j).attr("readonly", false);
                           //  //jQuery(".others_input_value' . $j . '").val(others_amount_val);
                           //  jQuery("#others_amount_val"+i+j).val("0.00");

                           //  }else if(others_incr_type_read==''){

                           //      jQuery(".others_input_value"+j).val(others_amount_val);
                           //      jQuery("#others_amount_val"+i+j).val("0.00");
                           //      jQuery("#others_amount_val"+i+j).attr("readonly", true);
                           //  }

        	}); 

}

function incr_edit_jquery_3_bkp(i,j,k,year_diff)
{

           var others_amount_val= jQuery("#others_type_amount_percentage"+k).val();
		   var others_incr_type_read = jQuery(".others_amount_type"+i+j+" option:selected").val();

            if (others_incr_type_read=="per_otr" || others_incr_type_read=="dir_otr"){ 
            jQuery("#others_amount_val"+i+j).attr("readonly", false);
            }else if(others_incr_type_read==""){

                jQuery("#others_amount_val"+i+j).attr("readonly", true);
            }

}

function set_incr_value(incr_row_number,counter_others){

	    var n=1; 
        var inc_select_str='';
        var rent_amount_val_str='';
        var cal_rent_val_str='';

        // others data
        var others_select_str='';
        var others_amount_val_str='';
        var others_rent_val_str='';
        var others_total_str='';
        
//alert(counter_others);
        for(var i=0; i<incr_row_number; i++){

        	if (n != 1){
                inc_select_str += ',';  
                rent_amount_val_str += ',';  
                cal_rent_val_str += ','; 
                others_select_str += ',';  
                others_amount_val_str += ',';  
                others_rent_val_str += ','; 
                others_total_str += ','; 

            }
                                     
            inc_select_str += jQuery('.inc_select'+i).val();
            rent_amount_val_str += parseFloat(jQuery('#rent_amount_val'+i).val());
            cal_rent_val_str += parseFloat(jQuery('#cal_rent_val'+i).val());

            // others val
            	var others=1;
            	var others_total=0;

            	
            	for(var j=0; j<counter_others; j++){

            	 		if (others != 1){
			       
			                others_select_str += '#';  
			                others_amount_val_str += '#';  
			                others_rent_val_str += '#'; 

            			}
            			// alert(others_select_str);
            			// return false;
            	 	others_select_str += jQuery('.others_amount_type'+i+j).val();
            		others_amount_val_str += parseFloat(jQuery('#others_amount_val'+i+j).val());
            		others_rent_val_str += parseFloat(jQuery('.other_tot_val_'+i+j).val());
            		others_total = others_total + parseFloat(jQuery('.other_tot_val_'+i+j).val());
            		others++;

            	}
            	others_total_str +=others_total;
            	//alert(others_total);


            n++;
        }

         jQuery('#updated_inc_select').val(inc_select_str);
         jQuery('#updated_rent_amount_val').val(rent_amount_val_str);
         jQuery('#updated_cal_rent_val').val(cal_rent_val_str);

         //others 
         jQuery('#updated_others_select_str').val(others_select_str);
         jQuery('#updated_others_amount_val_str').val(others_amount_val_str);
         jQuery('#updated_others_rent_val_str').val(others_rent_val_str);
         jQuery('#updated_others_total_str').val(others_total_str);

}

// 7 august

 function set_incremented_val()
        {
                var rent_amount_val_arr = jQuery('#updated_rent_amount_val').val().split(',');
                   for (i = 0; i < rent_amount_val_arr.length; i++){   
                    jQuery('#rent_amount_val'+i).val(parseFloat(rent_amount_val_arr[i]));    
                   }
        
                var cal_rent_val_arr = jQuery('#updated_cal_rent_val').val().split(',');
                   for (i = 0; i < cal_rent_val_arr.length; i++){   
                    jQuery('#cal_rent_val'+i).val(parseFloat(cal_rent_val_arr[i]));    
                   }

                var inc_select_arr = jQuery('#updated_inc_select').val().split(',');
                   for (i = 0; i < inc_select_arr.length; i++){   
                    jQuery('.inc_select'+i).val(inc_select_arr[i]);    
                   }   

            // for others
            	var k = 0;
            	var i = 0;

            	var others_select_str_arr = jQuery('#updated_others_select_str').val().split(',');
		               for (i = 0; i < others_select_str_arr.length; i++){
		                   var  select_str_sml_arr = others_select_str_arr[i].split('#');
		                   		for (k = 0; k < select_str_sml_arr.length; k++){
		                   			jQuery('#others_amount_type'+i+k).val(select_str_sml_arr[k]);
		                   		}
		                }


            	var others_amount_val_str_arr = jQuery('#updated_others_amount_val_str').val().split(',');
		               for (i = 0; i < others_amount_val_str_arr.length; i++){
		                   var  amount_val_sml_arr = others_amount_val_str_arr[i].split('#');
		                   		for (k = 0; k < amount_val_sml_arr.length; k++){
		                   			jQuery('#others_amount_val'+i+k).val(amount_val_sml_arr[k]);

		                   		}
		                }

            	 var others_rent_val_str_arr = jQuery('#updated_others_rent_val_str').val().split(',');
		               for (i = 0; i < others_rent_val_str_arr.length; i++){
		                   var  rent_val_sml_arr = others_rent_val_str_arr[i].split('#');
		                   		for (k = 0; k < rent_val_sml_arr.length; k++){
		                   			jQuery('.other_tot_val_'+i+k).val(rent_val_sml_arr[k]);
		                   		}
		                }
                   

 }

 

function update_monthly_val(total_sche_count){
	var updated_rent_values_arr= jQuery('#updated_cal_rent_val').val().split(',');
	var updated_others_total_str_arr= jQuery('#updated_others_total_str').val().split(',');
	var tax_rate_for_arear= jQuery('#tax_rate_for_arear').val();
	//alert(updated_others_total_str_arr);
	// 8 aug
	var updated_rent_amount_val_arr= jQuery('#updated_rent_amount_val').val().split(',');

	var counter=0;
	var n=0;
	var identy = 0;
	var diff=0;
	var diff_counter=0;
	var paid_total_diff=0;

	for(var i=1; i<=total_sche_count; i++){

		// for diff calculation
			if(i%12==0){
				diff= parseFloat(updated_rent_values_arr[diff_counter]) - parseFloat(jQuery('#monthly_rent_tr'+n).text()) ;
				jQuery('#diff'+n).val(diff);
				diff_counter++;

			}else{
				diff= parseFloat(updated_rent_values_arr[diff_counter]) - parseFloat(jQuery('#monthly_rent_tr'+n).text()) ;
				jQuery('#diff'+n).val(diff);

			}
			if(jQuery('#payment_sts_tr'+n).val()=='paid'){ 
				paid_total_diff = paid_total_diff + parseFloat(jQuery('#diff'+n).val());

			}

		if(jQuery('#payment_sts_tr'+n).val()=='unpaid'){ // check if it is paid or unpaid

			if(i%12==0){
		
				jQuery('#monthly_rent_tr'+n).text(updated_rent_values_arr[counter]);
				jQuery('#others_rent_tr'+n).text(updated_others_total_str_arr[counter]);
				jQuery('#monthly_rent_val'+n).val(updated_rent_values_arr[counter]);
				jQuery('#others_rent_val'+n).val(updated_others_total_str_arr[counter]);
				
				counter++;
			}else{
				if(parseInt(i)==parseInt(total_sche_count) && parseInt(i)%12==1 ){
					counter--;
				}
				if(i%12==1 && parseFloat(updated_rent_amount_val_arr[counter]).toFixed(2)!=0.00){

					jQuery('#sche_arear_tr'+n).css('background-color', '#FFFF99');
					jQuery('#remarks_tr'+n).text('Incremented');
				}else{
					jQuery('#sche_arear_tr'+n).css('background-color', '');
					jQuery('#remarks_tr'+n).text('');
				}

				
				jQuery('#monthly_rent_tr'+n).text(updated_rent_values_arr[counter]);
				jQuery('#others_rent_tr'+n).text(updated_others_total_str_arr[counter]);
				jQuery('#monthly_rent_val'+n).val(updated_rent_values_arr[counter]);
				jQuery('#others_rent_val'+n).val(updated_others_total_str_arr[counter]);
			}

		}else{
			identy++;
		}

		jQuery("#area_amount"+n).attr("readonly", false);

		if(jQuery('#payment_sts_tr'+n).val()=='unpaid'){ 
			// net payment calculation
			var net_monthly = parseFloat(jQuery('#monthly_rent_val'+n).val());
			var net_others =  parseFloat(jQuery('#others_rent_val'+n).val());
			var net_arear =   parseFloat(jQuery('#area_amount'+n).val());
			var net_adv_adj = parseFloat(jQuery('#rent_adv'+n).val());
			var net_sec_dep = parseFloat(jQuery('#adjust_sec_deposit'+n).val());
			
			// 20 sep
			var net_payment_before_tax = (net_monthly + net_others + net_arear) ;
		    var net_tax =    ((parseFloat(net_payment_before_tax))* tax_rate_for_arear)/100;
		    var new_net_payment =  (net_payment_before_tax -  net_tax - net_adv_adj) - net_sec_dep;

			jQuery('#tax_tr'+n).text(net_tax);
			jQuery('#tax_amt'+n).val(parseFloat(net_tax.toFixed(2)));
			var net_final_val = (net_monthly + net_others + net_arear)-( net_adv_adj + net_sec_dep + net_tax);
			jQuery('#net_pay_txt'+n).text(new_net_payment);
			jQuery('#updated_net_payment'+n).val(new_net_payment);

		}



		n++;

	}
	jQuery("#paid_total_diff").val(paid_total_diff);
	jQuery("#area_amount"+identy).val(paid_total_diff.toFixed(2));
	//alert(counter);


}

		function delete_action(val,row){
// alert(val);
			jQuery("#deleteno").val(val);
			jQuery("#deleterow").val(row);
			if (!deleteMessageDialog) {
				initDeleteMessageDialog();
			}

			deleteMessageDialog.show();
		
			return true;
		}
	
		
		function view(val,indx)
		{	

			jQuery("#jqxgrid").jqxGrid('clearselection');	
			jQuery("#popupWindow").jqxWindow({
				height:450,width: 650,  resizable: false,  isModal: true, autoOpen: false, cancelButton: jQuery("#hideWindowButton"), modalOpacity: 0.90           
			});
	         // open the popup window when the user clicks a button.
	         var offset = jQuery("#jqxgrid").offset();
            jQuery("#popupWindow").jqxWindow({ position: { x: parseInt(offset.left) + 260, y: parseInt(offset.top) + 10 } });
			// show the popup window.
            jQuery("#popupWindow").jqxWindow('open');	
				
			jQuery.ajax({
				type: "POST",
				async:false,
				cache: false,
				url: "<?=base_url()?>index.php/billreceive/get_bill_service",
				data :{id:val},
				datatype: "html",
				success: function(html){
					if(html =="")
						jQuery("#windowContent").html('<tr><td colspan="4">No data to display</td></tr>');
					else 
						//jQuery("#load_bill_services").html(html).show();
						jQuery("#windowContent").html(html).show();
				}
			});
	
			return false;			
		}
		function windowonclose(win)
	{
		jQuery(".eol-dialog-close").click(function(){
		win.close();
		});
	}
	
		function editt(val,indx)
		{				
			jQuery("#jqxgrid").jqxGrid('clearselection');			
			EOL.messageBoard.open('<?=base_url()?>index.php/agreement/from/edit/'+val+'/'+indx, (jQuery(window).width()-70), jQuery(window).height(), 'yes'); 			
			return false;			
		}


		function modify(val,indx)
		{				
			jQuery("#jqxgrid").jqxGrid('clearselection');			
			EOL.messageBoard.open('<?=base_url()?>index.php/agreement/from/modify/'+val+'/'+indx, (jQuery(window).width()-70), jQuery(window).height(), 'yes'); 			
			return false;			
		}

		function extend(val,indx)
		{				
			jQuery("#jqxgrid").jqxGrid('clearselection');			
			EOL.messageBoard.open('<?=base_url()?>index.php/agreement/from/extend/'+val+'/'+indx, (jQuery(window).width()-70), jQuery(window).height(), 'yes'); 			
			return false;			
		}

		function verify(val,row,type){
			//alert(memo_ref)
			$('verifyMessageErrorMsg').innerHTML='';
			$('verifyMessageDialogConfirm').style.display = '';
			$('verifyMessageDialogCancel').style.display = '';
			$('loadingVerify').style.display = 'none';
			jQuery("#verifyno").val(val);
			jQuery("#verifyrow").val(row);
			jQuery("#type").val(type);
			//jQuery("#memo_ref").val(memo_ref);

			jQuery.ajax({
	            url: '<?php echo base_url(); ?>index.php/agreement/admin_check_verify',
	            type: "post",
	            data: { id : val },
	            datatype: 'text',
	            success: function(response){
	            	 
	             if(response=='0')   
	             {
	             	alert('Deleted Entry Cannot Verify !!!');
	             	  jQuery("#jqxgrid").jqxGrid('updatebounddata');
	             	return false;
	             }else{
	             	if(type == 'admin_verify'){
							 jQuery("#verifySTF").text(' you want to verify this Agreement entry');
							  jQuery("#ctable").hide();
						} else if(type == 'fin_verify'){
							jQuery("#verifySTF").text(' you want to verify this Agreement entry');
							 jQuery("#ctable").hide();
						}
						else {
							 jQuery("#verifySTF").text(' you want to send this Agreement to Finance');
							  jQuery("#ctable").hide();
						}
						if (!verifyMessageDialog) {
							 initVerifyMessageDialog();
						}
						verifyMessageDialog.show();
	             }
	 			
	            },
	            error:   function(model, xhr, options){
	                alert('failed');
	            },
      	  });

		}

		function checkPaidStatus(id){
			var data = [];
			jQuery.ajax({
				type: "POST",
				cache: false,
				url: "<?=base_url()?>index.php/agreement/checkPaidStatus/"+id,
				data : {'id':id},
				async : false,
				success : function(response){
					data = jQuery.parseJSON(response);
				}
			});
			return data;
		}
		
		// ack + halt + unhalt by finance

		function action(id,row,type){
			$('verifyMessageErrorMsg').innerHTML='';
			$('verifyMessageDialogConfirm').style.display = '';
			$('verifyMessageDialogCancel').style.display = '';
			$('loadingVerify').style.display = 'none';
			//jQuery("#verifyno").val(val);
			jQuery("#verifyrow").val(row);
			jQuery("#type").val(type);
			jQuery("#id").val(id);
			if(type == 'ack'){
				 jQuery("#verifySTF").text(' you want to Acknowledge this Agreement entry');
				 jQuery("#ctable").hide();
			} 
			else if (type == 'stop'){

				var data = check_agree_rent_in_adv(id);
				if(parseInt(data) == 0){
					jQuery("#verifySTF").text(' to Stop this Agreement entry');
					jQuery("#ctable").show();
						
					}else{
						alert('Rent in Advance Exists!');
						return false;
					}
				 
			} 
			else if (type == 'release'){
				jQuery("#verifySTF").text(' to Release this Agreement entry');
				 jQuery("#ctable").hide();
			}else if (type == 'close'){
				var data = checkPaidStatus(id);
				if (data != '') {
					if (data.stop_count > 0 || data.advance_count > 0) {
						alert("This agreement can't be closed, this agreement has unpaid or advance payment.");
						return false;
					}
				}

				jQuery("#verifySTF").text(' You want to Close this Agreement entry');
				 jQuery("#ctable").show();
			}
			else if (type == 'unclose'){
				jQuery("#verifySTF").text(' You want to Release this Closed Agreement entry');
				 jQuery("#ctable").hide();
			}else if (type == 'stm'){
				jQuery("#verifySTF").text(' you want to Sent this Agreement to Maker');
				jQuery("#ctable").show();
			}
			else {
				 jQuery("#verifySTF").text(' you want to Halt this Agreement');
				  jQuery("#ctable").show();
			}
			if (!verifyMessageDialog1) {
				initVerifyMessageDialog_ack_halt_unhalt();
			}
			verifyMessageDialog1.show();
		}


		/* message delete */
		var deleteMessageDialog;

		function check_agree_rent_in_adv(id){
			var data = [];
			jQuery.ajax({
				type: "POST",
				cache: false,
				url: "<?=base_url()?>index.php/agreement/check_agree_rent_in_adv/"+id,
				data : {'id':id},
				async : false,
				success : function(response){
					data = jQuery.parseJSON(response);
					
				}
			});
			return data;
		}
		
		function initDeleteMessageDialog() {
			
			// Define various event handlers for Dialog
			var handleCancel = function() {
				this.hide();
			};
		  var handleDeleteMessageSuccess = function(req) {
			var response = eval('(' + req + ')');
			
			if( response.status == 'success') {
				jQuery("#error").show();
				jQuery("#error").fadeOut(11500);
				jQuery("#error").html('<img align="absmiddle" src="'+baseurl+'images/drag.png" border="0" /> &nbsp;Successfully deleted');					
				deleteMessageDialog.hide();
				$('deleteMessageDialogConfirm').style.display = '';
				$('deleteMessageDialogCancel').style.display = '';
				$('loading').style.display = 'none';
				var row =jQuery("#deleterow").val();
				jQuery("#jqxgrid").jqxGrid('clearselection');
				jQuery("#jqxgrid").jqxGrid('setcellvalue',row,'bill_sts',0);		
				if(typeof row !="undefined"){
					jQuery("#jqxgrid").jqxGrid('hiderow',row);
					//submitonclick(0,2);
					clearCount();
				}
				else
					reloadCurrentMessages();
			} else {
				$('deleteMessageErrorMsg').style.display = '';
			}
		  };
		  var handleDeleteMessageFailure = function(o) {    	
				showInfoDialog( 'deleteMessagefailuretitle', 'deleteMessagefailurebody', 'WARN' );
		  };
		  
		  var handleSubmit = function() {
		  	var val=jQuery("#deleteno").val();
// alert(val);

			var request =  new Request({	url: '<?=base_url()?>index.php/agreement/delete_action', 
											method: 'post',
											data: {id:val},
											onSuccess: function(req) {handleDeleteMessageSuccess(req);},
											onFailure: function(req) {handleDeleteMessageFailure(req);}
										});
			request.send();
			$('deleteMessageDialogConfirm').style.display = 'none';
			$('deleteMessageDialogCancel').style.display = 'none';
			$('loading').style.display = '';
			
		  };
			
			deleteMessageDialog = new EOL.dialog($('deleteMessageDialogContent'), {position: 'fixed', modal:true, width:470, close:true, id: 'deleteMessageDialog' });
			
			deleteMessageDialog.afterShow = function() {
				$$('#deleteMessageDialog #deleteMessageDialogConfirm').addEvent('click',handleSubmit);
				$$('#deleteMessageDialog #deleteMessageDialogCancel').addEvent('click',function() {deleteMessageDialog.hide();});
			};
		
			deleteMessageDialog.show();
		}

		
		function reloadCurrentMessages()
		{			
			window.location.reload();
		}
		
		//Reset the dialog fields
		
		function resetDeleteMessageDialog() {
			resetDeleteMessageErrors();
			$("deleteEventId").value = '';
		}
		
		//Reset the error messages on the dialog.
		 
		function resetDeleteMessageErrors() {
			$('deleteMessageErrorMsg').innerHTML = '';
			$('deleteMessageErrorMsg').style.display = 'none';
		}

		function getToday(){
			var today = new Date();
			var dd = today.getDate();
			var mm = today.getMonth()+1; //January is 0!

			var yyyy = today.getFullYear();
			if(dd<10){
			    dd='0'+dd;
			} 
			if(mm<10){
			    mm='0'+mm;
			} 
			var today = dd+'/'+mm+'/'+yyyy;
			return today;
		}
		
    </script>

    <div id="jqxgrid"  style=" margin: 10px auto"></div>    
    <div style="float:left">
   
    	
	<? if(ADD==1){?>
	<a style="text-decoration:none" onclick="javascript:EOL.messageBoard.open(this.href, (jQuery(window).width()-70), jQuery(window).height(), 'yes'); return false;" 
    href="<?=base_url()?>index.php/agreement/from/add" title=""><input type="button" class="buttonStyle"  value="Add New" id="sendButton" /></a>
    <? }?>
	</div>


	<div style="font-family: Calibri; margin-top: 15px;margin-left: 200px"><strong> E=</strong> Edit, <strong> D=</strong> Delete,<strong> V=</strong> Admin Verify,<strong> STF=</strong> Sent To Finance,<strong> STM=</strong> Sent To Maker,<strong> ACK=</strong> Acknowledge By Finance, <strong> FV=</strong> Finance Verify,<strong> HLT=</strong> Halt,<strong> RHLT=</strong> Release Halt</div>
  <!-- Delete information-->
<style>
* { padding:0px; margin:0px; } 
</style>
			<div id="popupWindow">
                <div id="windowHeader">
                   <h3>Bill Details</h3>
                </div>
                <div  id="windowContent">
                   
		  			 
                </div>
            </div>
	    <input type="hidden" id="deleteno" name="deleteno" value="" />
	    <input name="verifyno" id="verifyno" value="" type="hidden">
	 <input name="verifyrow" id="verifyrow" value="" type="hidden">
	 <input name="id" id="id" value="" type="hidden">
	 <input name="type" id="type" value="" type="hidden">
	 <input name="memo_ref" id="memo_ref" value="" type="hidden">
     <input type="hidden" id="val" name="val" value="" />
     <input type="hidden" id="row" name="row" value="" />
	   <!--  <input type="hidden" id="deleterow" name="deleterow" value="" /> -->

        <div id="deleteMessageDialogContent"  style="display:none">
          <div class="hd"><h2 class="conf">Are you sure you want to delete these agreement info(s)?</h2></div>
          <form method="POST" name="deleteMessageform" id="deleteMessageform"  style="margin:0px;">
            <input name="deleteEventId" id="deleteEventId" value="" type="hidden">
            <input name="action" value="DeleteMessage" type="hidden">
            <input name="type"  id="type" value="delete" type="hidden">
          	<div class="bd">
              <div class="inlineError" id="deleteMessageErrorMsg" style="display:none"></div>
              <div class="instrText" style="margin-bottom: 20px">
               This action is permanent.
              </div>
            </div>
            <a class="btn-small btn-small-normal" id="deleteMessageDialogConfirm"><span>Yes</span></a> 
            <a class="btn-small btn-small-secondary" id="deleteMessageDialogCancel"><span>Cancel</span></a> 
            <span id="loading" style="display:none">Please wait... <img src="<?=base_url()?>images/loader.gif" align="bottom"></span>
            </form>
        </div>
        
        <div style="display: none; padding:0px; margin:0px;" id="SelctOnetitle">We're Sorry</div>
		<div style="display: none;" id="SelctOnebody">Please select at least one bill info.</div>

 	
<!-- Verify information-->
		<div id="verifyMessageDialogContent"  style="display:none">
          <div class="hd"><h3 class="conf">Are you sure<span id="verifySTF"></span>?</h3></div>
          <form method="POST" name="verifyMessageform" id="verifyMessageform"  style="margin:0px;">
      <table id="ctable" class="register-table2" width="100%;">   
				<tr>
					<td class="label">Send Comments:</td>
					<td><textarea id="send_comments" name="send_comments"></textarea></td>
				</tr>
		  	</table>
          	<div class="bd">
              <div class="inlineError" id="verifyMessageErrorMsg" style="display:none"></div>
              
            </div>
            <a class="btn-small btn-small-normal" id="verifyMessageDialogConfirm"><span>Yes</span></a> 
            <a class="btn-small btn-small-secondary" id="verifyMessageDialogCancel"><span>Cancel</span></a> 
            <span id="loadingVerify" style="display:none">Please wait... <img src="<?=base_url()?>images/loader.gif" align="bottom"></span>
            </form>
        </div>

		<!-- Delete information-->
    	<div id="verifyMessageDialogContent"  style="display:none">
          <div class="hd"><h3 class="conf" id="conf_msg">Are you sure you want to verify the bill info(s)?</h3></div>
          <form method="POST" name="verifyMessageform" id="verifyMessageform"  style="margin:0px;">
            <input name="verifyEventId" id="verifyEventId" value="" type="hidden">
            <input name="verifyIndexId" id="verifyIndexId" value="" type="hidden">
            <input name="verify_EmplyId" id="verify_EmplyId" value="" type="hidden">
            <input name="verify_type"  id="verify_type" value="" type="hidden">
          	<div class="bd">
              <div class="inlineError" id="verifyMessageErrorMsg" style="display:none"></div>
            </div>
            <a class="btn-small btn-small-normal" id="verifyMessageDialogConfirm"><span>Yes</span></a> 
            <a class="btn-small btn-small-secondary" id="verifyMessageDialogCancel"><span>Cancel</span></a> 
            <span id="loadingVerify" style="display:none">Please wait... <img src="<?=base_url()?>images/loader.gif" align="bottom"></span>
          </form>
        </div>	

	 	<!--Customization End-->
	</div>	
</div>


	<div id='jqxwindow' style="width:900px !important">
		<div id="windowHeader"><span>Rent Schedule</span></div>
			<div style="align:center;"> 
				<form class="form" id="form1" method="post" action="#" >
				  	<div id="item_info" align="center">
				 	

				
		        	</div>
		    	<!-- <input type="button" value="Save" id="sendButton_sche" class="buttonStyle" /> -->
		    	</form>
		    </div>
	</div>

	<div id='jqxwindow2' style="width:900px !important">
		<div id="windowHeader"><span>Rent Schedule</span></div>
			<div style="align:center;"> 
				<form class="form" id="form2" method="post" action="#" >
				  	<div id="item_info1" align="center">
				 	

				
		        	</div>
		        	<center>
		        			<input type="button" value="Save" id="sendButton_sche" class="buttonStyle" />
		        			<span id="loading_arrear" style="display:none">Please wait... <img src="<?=base_url()?>images/loader.gif" align="bottom"></span>
        
		        	</center>

		        	<br />
		    	</form>
		    </div>
	</div>

	<div id="jqxwindow3" style="width:900px !important" style=" margin: 10px auto">
        <div id="windowHeader">
            <span>
                Increment Setup
            </span>
        </div> 
        <div style="">
            <table>

                <tr>
                <div style="" id="data_table3">



                </div>
                </tr>
                <center><input type="button" value="Update" id="increment_closeButton" class="buttonStyle" /></center>

            </table>
        </div>
    </div> 

    <div id='jqxwindow4' style="width:900px !important">
		<div id="windowHeader"><span>Rent Information</span></div>
			<div style="align:center;"> 
				<form class="form" id="form4" method="post" action="#" >
				  	<div id="item_info4" align="center">
				 	

				
		        	</div>
		        	<center>
		        	    <input type="button" value="Verify" id="verify_btn" class="buttonStyle" />
		        		<span id="loading_img" style="display:none">Please wait... <img src="<?= base_url() ?>images/loader.gif" align="bottom"></span>
		        	</center>
		    	</form>
		    	<br />
		    	<br />
		    </div>
	</div>