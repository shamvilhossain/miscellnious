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

.security_deposit_style {
    font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
    width: 100%;
    border-collapse: collapse;
    margin: 0 0px;
}

.security_deposit_style td, .security_deposit_style th {
    font-size: 1em;
    border: 1px solid #4197c7;
    padding: 3px 7px 2px 7px;
}
.bill_details td, .bill_details th {
	border:none !important;
}

.security_deposit_style th {
    font-size: 1.1em;
    text-align: left;
    padding-top: 5px;
    padding-bottom: 4px;
    background-color: #4197c7;
    color: #ffffff;
}

.security_deposit_style tr.alt td {
    color: #000000;
    background-color: #4197c7;
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

         jQuery(document).ready(function($) {
            // prepare the data
            var theme = theme;		
            var source =
            {
                 datatype: "json",
                 datafields: [
					 { name: 'id', type: 'int'},
					 { name: 'sl', type: 'int'},
					 { name: 'rent_agree_ref', type: 'string'},
					 { name: 'fin_ref_no', type: 'string'},
					 { name: 'location_name', type: 'string'},
					 { name: 'schedule_strat_dt', type: 'string'},
					 { name: 'paid_dt', type: 'string'},
					 { name: 'agreement_id', type: 'int'},
					 { name: 'monthly_amount', type: 'number'}, 
					 { name: 'total_others_amount', type: 'number'}, 
					 { name: 'arear_adjust_amount', type: 'number'}, 
					 { name: 'rent_amount', type: 'number'}, 
					 { name: 'adv_adjustment_amt', type: 'number'},
					 { name: 'sd_adjust_amt', type: 'number'},
					 { name: 'tax_amount', type: 'number'},
					 //{ name: 'provision_adjust_amt', type: 'number'},
					 { name: 'sts', type: 'number'},
					 { name: 'fin_v_by', type: 'number'},
					 { name: 'approve_by', type: 'number'},
					 { name: 'journal_add_sts', type: 'number'},
					 { name: 'journal_verify_sts', type: 'number'}
					
					
					// { name: 'particulars', type: 'string'},
					
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
                 //     var dateToChange = JSON.stringify(rowdata.date_added);
               		// rowdata.date_added = dateToChange.replace(/"/g, '');
               		// var dateToChange = JSON.stringify(rowdata.date_modified);
               		// rowdata.date_modified = dateToChange.replace(/"/g, '');
                    commit(true);
                },
			    url: '<?=base_url()?>index.php/rent_schedule_payment/grid',
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
			
			 
			
			var columnCheckBox = null;
            var updatingCheckState = false;
            // initialize jqxGrid. Disable the built-in selection.
            
			
			var cellsrenderer = function (row, column, value) {
				return '<div style="text-align: center; margin-top: 5px;">' + value + '</div>';
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
			    source: dataadapter,
                theme: theme,
				filterable: true,
				sortable: true,
				//autoheight: true,
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
			    	  { text: 'ID', datafield: 'id',hidden:true,  editable: false,  width: '45' },
			    	 
			    	  // new code start
			    	  { text: '',
				          menu: false,
				          sortable: false,
				          datafield: 'available',
				          columntype: 'checkbox',  threestatecheckbox : false,
				          width: 38,
				          renderer: function () {
				              return '<div style="margin-left: 7px; margin-top: 3px;"></div>';
				          },
				          rendered: function (element) {
				              var checkbox = jQuery(element).last();
				              jQuery(checkbox).jqxCheckBox({
				                  theme: theme,
				                  width: 16,
				                  height: 16,
				                  animationShowDelay: 0,
				                  animationHideDelay: 0
				              });
				              columnCheckBox = jQuery(checkbox);
				              jQuery(checkbox).on('change', function (event) {
				                  var checked = event.args.checked;
				                  var pageinfo = jQuery("#jqxgrid").jqxGrid('getpaginginformation');
				                  var pagenum = pageinfo.pagenum;
				                  var pagesize = pageinfo.pagesize;
				                  if (checked == null || updatingCheckState) return;
				                  jQuery("#jqxgrid").jqxGrid('beginupdate');

				                  // select all rows when the column's checkbox is checked.
				                  if (checked) {
				                      jQuery("#jqxgrid").jqxGrid('selectallrows');
				                  }
				                  // unselect all rows when the column's checkbox is checked.
				                  else if (checked == false) {
				                      jQuery("#jqxgrid").jqxGrid('clearselection');
				                  }

				                  // update cells values.
				                  var startrow = pagenum * pagesize;
				                  for (var i = startrow; i < startrow + pagesize; i++) {
				                      // The bound index represents the row's unique index.
				                      // Ex: If you have rows A, B and C with bound indexes 0, 1 and 2, afer sorting, the Grid will display C, B, A i.e the C's bound index will be 2, but its visible index will be 0.
				                      // The code below gets the bound index of the displayed row and updates the value of the row's available column.
				                      var boundindex = jQuery("#jqxgrid").jqxGrid('getrowboundindex', i);
				                      jQuery("#jqxgrid").jqxGrid('setcellvalue', boundindex, 'available', event.args.checked);
				                  }

				                  jQuery("#jqxgrid").jqxGrid('endupdate');
				                  for (var i = 0; i < disabled.length; i++) {
				                      var row = disabled[i];
				                      jQuery("#jqxgrid").jqxGrid('setcellvalue', row, "available", false);
				                      jQuery('#jqxgrid').jqxGrid('unselectrow', row);
				                  }
				              });
				              return true;
				          },
				      },

				      // new code end
			    	 
					  <? if(DELETE==1){?>
						  { text: 'DELETE', menu: false,renderer: columnsrenderer, columntype: 'number',sortable: false, width: 60, 
						  	cellsrenderer: function (row) {							
								editrow = row;
								var dataRecord = jQuery("#jqxgrid").jqxGrid('getrowdata', editrow);
								if(dataRecord.sts==1 && dataRecord.approve_by ==0)	
								{						
							 		return '<div style="text-align:center;  cursor:pointer" onclick="delete_action('+dataRecord.id+','+editrow+')" ><img align="center" src="<?=base_url()?>images/delete.png"></div>';
							  	}else{
							  		return '<div style="text-align:center;margin-top:11%"><strong>V</strong></div>';
							  	}
							  }
						  },
					  <? }?>
					  <? if(EDIT==10){?>			 
					  { text: 'EDIT', menu: false,renderer: columnsrenderer, columntype: 'number',   sortable: false, width: 55, 
					  	cellsrenderer: function (row) {							
							editrow = row;
							var dataRecord = jQuery("#jqxgrid").jqxGrid('getrowdata', editrow);						
						 	//alert(dataRecord.v_sts);
						 	if(dataRecord.fin_v_by==0) 	
							{			
						 		return '<div style="text-align:center;  cursor:pointer" onclick="editt('+dataRecord.id+','+editrow+')" ><img align="center" src="<?=base_url()?>images/edit.png"></div>';
							}
							else{ 
								return '<div style="text-align:center;margin-top:11%"><strong>V</strong></div>';
								}
						  }
					  },
					    
					  <? }?>

					  // new 
					  <? if(FIN_VERIFY==1){?>
							 { text: 'FV', menu: false,   align:'center', columntype: 'number',  sortable: true, width: 60, 
								cellsrenderer: function (row) {					
									editrow = row;
									var dataRecord = jQuery("#jqxgrid").jqxGrid('getrowdata', editrow);
									if(dataRecord.fin_v_by ==0)
									//if(dataRecord.approve_by==0 && dataRecord.fin_v_by !=0)
									{
										return '<div style="text-align:center;  cursor:pointer" onclick="finance_verify(\''+dataRecord.id+'\','+editrow+',\'fin\');" ><img align="center" src="<?=base_url()?>images/confirm.png"></div>';
									}
									else
									{						
										return '<div style="text-align:center;margin-top:11%"><strong>N/A</strong></div>';
									}
								  }
							  },
							    <? }?>

					  <? if(APPROVE==1){?>
							 { text: 'Approve', menu: false,   align:'center', columntype: 'number',  sortable: true, width: 80, 
								cellsrenderer: function (row) {					
									editrow = row;
									var dataRecord = jQuery("#jqxgrid").jqxGrid('getrowdata', editrow);
									//if(dataRecord.fin_v_by ==0)
									if(dataRecord.approve_by==0 && dataRecord.fin_v_by !=0)
									{
										return '<div style="text-align:center;  cursor:pointer" onclick="approve_verify(\''+dataRecord.id+'\','+editrow+',\'dept\');" ><img align="center" width="24px" src="<?=base_url()?>images/approve.png"></div>';
									}else if(dataRecord.approve_by !=0)
									{						
										return '<div style="text-align:center;margin-top:11%"><strong>Approved</strong></div>';
									}
									else
									{						
										return '<div style="text-align:center;margin-top:11%"><strong>N/A</strong></div>';
									}
								  }
							  },
							    <? }?>
					<? if(RESET==1){?>		    
					{ text: 'Reset', menu: false, datafield: 'reset',  align:'center', columntype: 'number',  sortable: true, width: 60, 
								cellsrenderer: function (row) {					
									editrow = row;
									var dataRecord = jQuery("#jqxgrid").jqxGrid('getrowdata', editrow);
									// && dataRecord.journal_add_sts==1
									if(dataRecord.approve_by!=0 && dataRecord.journal_verify_sts==0 && dataRecord.journal_add_sts==0)
									{	
										return '<div style="text-align:center;  cursor:pointer" onclick="verify(\''+dataRecord.id+'\','+editrow+',\'reset\');" ><img align="center" width="24px" src="<?=base_url()?>images/reset.png"></div>';
									}else if(dataRecord.journal_add_sts !=0)
									{						
										return '<div style="text-align:center;margin-top:11%"><strong>Added</strong></div>';
									}else
									{						
										return '<div style="text-align:center;margin-top:11%"><strong>N/A</strong></div>';
									}
								}
					},
					<? }?>
					{ text: 'VIEW', menu: false, datafield: 'view',  align:'center', columntype: 'number',  sortable: true, width: 60, 
								cellsrenderer: function (row) {					
									editrow = row;
									var dataRecord = jQuery("#jqxgrid").jqxGrid('getrowdata', editrow);
										
											return '<div style="text-align:center;  cursor:pointer" onclick="approve_verify(\''+dataRecord.id+'\','+editrow+',\'view\');" ><img align="center" src="<?=base_url()?>images/view_detail.png"></div>';
									
								  }
					},
							  
                      { text: 'Agreement REF#', datafield: 'rent_agree_ref', editable: false, width: '130' },
                      { text: 'Finance REF#', datafield: 'fin_ref_no', editable: false, width: '120' },
                      { text: 'Location Name', datafield: 'location_name', editable: false, width: '200' },
                      { text: 'Payment Month', datafield: 'schedule_strat_dt', editable: false, width: '150' },
                      { text: 'Paid date', datafield: 'paid_dt', editable: false, width: '90' },
                      { text: 'Monthly Rent', datafield: 'monthly_amount', editable: false, width: '100' },
                      { text: 'Others Amount', datafield: 'total_others_amount', editable: false, width: '100' },
                      { text: 'Arear', datafield: 'arear_adjust_amount', editable: false, width: '65' },                      
                      { text: 'Adjust Amount', datafield: 'adv_adjustment_amt', editable: false, width: '100' }, 
                      { text: 'SD Adjust', datafield: 'sd_adjust_amt', editable: false, width: '80' }, 
                      { text: 'Tax', datafield: 'tax_amount', editable: false, width: '65' }, 
                     // { text: 'Provision Adjust Amount', datafield: 'provision_adjust_amt', editable: false, width: '100' }, 
                      { text: 'FIN', datafield: 'fin_v_by',hidden:true,  editable: false,  width: '45' },
                      { text: 'agreement_id', datafield: 'agreement_id',hidden:true,  editable: false,  width: '45' },
					  { text: 'Net Amount', datafield: 'rent_amount', editable: false, width: '100' },
                      { text: 'sts', datafield: 'sts',hidden:true,  editable: false,  width: '45' }
                   
                      // { text: 'Date Modified', datafield: 'date_modified',  editable: false, width: '120' }
                    
                  ]
            });			
			jQuery("#popupWindow").jqxWindow({
				height:450,width: 650, resizable: false,  isModal: true, autoOpen: false, cancelButton: jQuery("#hideWindowButton"), modalOpacity: 0.90           
			});
	         // open the popup window when the user clicks a button.
	         var offset = jQuery("#jqxgrid").offset();
            jQuery("#popupWindow").jqxWindow({ position: { x: parseInt(offset.left) + 260, y: parseInt(offset.top) + 10 } });

			
            jQuery("#Cancel").jqxButton({ theme: theme });
			
			//End check box start 
			jQuery("#jqxgrid").on('cellbeginedit', function (event) {
                var column = args.datafield;
                var row = args.rowindex;
                var value = args.value;
                var rowindexes = jQuery('#jqxgrid').jqxGrid('getselectedrowindexes');
            });
            // select or unselect rows when the checkbox is checked or unchecked.
            jQuery("#jqxgrid").on('cellendedit', function (event) {
                 var dataRecord = jQuery("#jqxgrid").jqxGrid('getrowdata', event.args.rowindex);
			   
					if (event.args.value) {
						jQuery("#jqxgrid").jqxGrid('selectrow', event.args.rowindex);
					}
					else {
						jQuery("#jqxgrid").jqxGrid('unselectrow', event.args.rowindex);
					}
			
                if (columnCheckBox) {
					
                    var selectedRowsCount = jQuery("#jqxgrid").jqxGrid('getselectedrowindexes').length;
                    var rowscount = jQuery("#jqxgrid").jqxGrid('getdatainformation').rowscount;
                    updatingCheckState = true;
					var newc=0;
					for(var i=0;i<rowscount;i++)
					{
						var dataRecord = jQuery("#jqxgrid").jqxGrid('getrowdata', i);
						if(dataRecord.VerificationStatus==0)
			   			{
							newc++;	
							//alert(newc)
						}
					}					
                    if ((selectedRowsCount+newc) == rowscount && selectedRowsCount>0) {
                        jQuery(columnCheckBox).jqxCheckBox('check')
                    }                    
                    else {
                        jQuery(columnCheckBox).jqxCheckBox('uncheck');
                    }
					/*
                    if (selectedRowsCount == rowscount) {
                        jQuery(columnCheckBox).jqxCheckBox('check')
                    }
                    else if (selectedRowsCount > 0) {
                        jQuery(columnCheckBox).jqxCheckBox('indeterminate');
                    }
                    else {
                        jQuery(columnCheckBox).jqxCheckBox('uncheck');
                    }*/
                    updatingCheckState = false;
                }
            });
			//End check box update 	
						
        });
		
		function delete_action(val,row){
			jQuery("#val").val(val);
			jQuery("#row").val(row);
			jQuery("#Saction").text(' delete ');
			var type='delete';
			if (!deleteMessageDialog) {

				initDeleteMessageDialog(val,type);
			}

			deleteMessageDialog.show();
			

			return true;
		}
	function approve_verify(val,indx,type)
		{		
		//	alert(val+indx+type)

			jQuery("#jqxgrid").jqxGrid('clearselection');			
			EOL.messageBoard.open('<?=base_url()?>index.php/rent_schedule_payment/from/finance_verify/'+val+'/'+indx+'/'+type, (jQuery(window).width()-70), jQuery(window).height(), 'yes'); 			
			return false;			
		}

		// 23 april 2018
		function finance_verify(val,indx,type)
		{		
		
			jQuery("#jqxgrid").jqxGrid('clearselection');			
			EOL.messageBoard.open('<?=base_url()?>index.php/rent_schedule_payment/from/fin/'+val+'/'+indx+'/'+type, (jQuery(window).width()-70), jQuery(window).height(), 'yes'); 			
			return false;			
		}

		function verify(val,row,type){

			$('verifyMessageErrorMsg').innerHTML='';
			$('verifyMessageDialogConfirm').style.display = '';
			$('verifyMessageDialogCancel').style.display = '';
			$('loadingVerify').style.display = 'none';
			jQuery("#verifyno").val(val);
			jQuery("#verifyrow").val(row);
			if (!verifyMessageDialog) {
				initVerifyMessageDialog();
			}
			verifyMessageDialog.show();
		}
		
		
		function editt(val,indx)
		{				
			jQuery("#jqxgrid").jqxGrid('clearselection');			
			EOL.messageBoard.open('<?=base_url()?>index.php/rent_schedule_payment/from/edit/'+val+'/'+indx, (jQuery(window).width()-70), jQuery(window).height(), 'yes'); 			
			return false;			
		}
		function right(id,indx)
		{				
			jQuery("#jqxgrid").jqxGrid('clearselection');			
			EOL.messageBoard.open('<?=base_url()?>index.php/agreement/set_right/'+id+'/'+indx, (jQuery(window).width()-70), jQuery(window).height(), 'yes'); 			
			return false;			
		}
			
		
       	var verifyMessageDialog;
		function initVerifyMessageDialog() {	

			// Define various event handlers for Dialog
			var handleCancel = function() {
				this.hide();
			}; 
			var handleVerifyMessageSuccess = function(req) {
				var response = eval('(' + req + ')');
				
					if( response.status == 'success') {	
						jQuery("#error").show();
						jQuery("#error").fadeOut(11500);
						jQuery("#error").html('<img align="absmiddle" src="'+baseurl+'images/drag.png" border="0" /> &nbsp;Reset Successfully ');	
						verifyMessageDialog.hide();		
						var row =jQuery("#verifyrow").val();	
						jQuery("#jqxgrid").jqxGrid('clearselection');
						jQuery("#jqxgrid").jqxGrid('setcellvalue',row,'sts',1);

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
		  		//alert(val);
		  		//exit;
			  	var request =  new Request({
			  									url: '<?=base_url()?>index.php/rent_schedule_payment/reset_action', 
												method: 'post',
												data: {id:val},
												onSuccess: function(req) {handleVerifyMessageSuccess(req);},
												onFailure: function(req) {handleVerifyMessageFailure(req);}
											});
				request.send();
				$('verifyMessageDialogConfirm').style.display = 'none';
				$('verifyMessageDialogCancel').style.display = 'none';
				$('loadingVerify').style.display = '';	
				jQuery("#jqxgrid").jqxGrid('updatebounddata');
				//window.location.reload();
		  };
			//alert(id)
			verifyMessageDialog = new EOL.dialog($('verifyMessageDialogContent'), {position: 'fixed', modal:true, width:470, close:true, id: 'verifyMessageDialog' });
			verifyMessageDialog.afterShow = function() {
				$$('#verifyMessageDialog #verifyMessageDialogConfirm').addEvent('click',handleSubmit);
				$$('#verifyMessageDialog #verifyMessageDialogCancel').addEvent('click',function() {verifyMessageDialog.hide();});
			};		
			verifyMessageDialog.show();

		}
	
		
		
		
		/* message delete */
		var deleteMessageDialog;
		
		function initDeleteMessageDialog(objId,type) {
			
			// Define various event handlers for Dialog
			var handleCancel = function() {
				this.hide();
			};
		  var handleDeleteMessageSuccess = function(req) {
			var response = eval('(' + req + ')');
			
			if( response.status == 'success') {		
				jQuery("#error").show();
						jQuery("#error").fadeOut(11500);
						jQuery("#error").html('<img align="absmiddle" src="'+baseurl+'images/drag.png" border="0" /> &nbsp;Successfully Done');	
						//verifyMessageDialog.hide();			
				deleteMessageDialog.hide();
				
				reloadCuragreementMessages();
			} else if( response.status == 'failed') {		
				jQuery("#error").show();
						jQuery("#error").fadeOut(11500);
						jQuery("#error").html('<font color="red"> Failed</font>!');	
						//verifyMessageDialog.hide();			
				deleteMessageDialog.hide();

				//reload results
				reloadCuragreementMessages();
			} else {
				$('deleteMessageErrorMsg').innerHTML = response.errorMsgs[0];
				$('deleteMessageErrorMsg').style.display = '';
			}
		  };
		  var handleDeleteMessageFailure = function(o) {    	
				showInfoDialog( 'deleteMessagefailuretitle', 'deleteMessagefailurebody', 'WARN' );
		  };
		  
		  var handleSubmit = function() {
		  	var val=jQuery("#val").val();
		  	//alert($("deleteEventId").value);exit;
		  	var postData = $('deleteMessageform').toQueryString();
		  	// if(type=='Verification' || type=='Approval'){
		  	// 	var request =  new Request({	url: '<?=base_url()?>index.php/rent_schedule_payment/bulk_fin_verify_action', 
					// 						method: 'post',
					// 						data: postData,
					// 						onSuccess: function(req) {handleDeleteMessageSuccess(req);},
					// 						onFailure: function(req) {handleDeleteMessageFailure(req);}
					// 					});
		  	// }

		  	if(type=='Verification' || type=='Approval'){
				  		jQuery.ajax({
							type: "POST",
							cache: false,
							url: "<?=base_url()?>index.php/rent_schedule_payment/bulk_fin_verify_action",
							data: postData,
							
							datatype: "json",
							success: function(response){
								var json = jQuery.parseJSON(response);
								//alert(json.Message);
								if(json.Message!='OK')
								{								
									
									alert(json.Message);
									//handleDeleteMessageFailure(response);
									jQuery("#error").show();
									jQuery("#error").fadeOut(11500);
									jQuery("#error").html('<font color="red"> Failed !</font>');	
									//verifyMessageDialog.hide();			
									deleteMessageDialog.hide();
									reloadCuragreementMessages();
									return false;
								}
								else{
									jQuery("#error").show();
									jQuery("#error").fadeOut(11500);
									jQuery("#error").html('<img align="absmiddle" src="'+baseurl+'images/drag.png" border="0" /> &nbsp;Successfully Done');	
									//verifyMessageDialog.hide();			
									deleteMessageDialog.hide();
									reloadCuragreementMessages();
								}				
							}
						});
		  	}

		  	else{
		  		var request =  new Request({	url: '<?=base_url()?>index.php/rent_schedule_payment/delete_action', 
											method: 'post',
											data: {id:val},
											onSuccess: function(req) {handleDeleteMessageSuccess(req);},
											onFailure: function(req) {handleDeleteMessageFailure(req);}
										});
		  		request.send();
		  	}
			
			
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

		
		function reloadCuragreementMessages()
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

		function delete_records(objId,stype)
		{

			//alert(stype)
			jQuery('#cdiv').hide();
			jQuery('#cpetty tbody').empty();
				if(stype == 'Verification'){
					jQuery("#Saction").text(' verify');
					jQuery("#stype").val(stype);
				}else if(stype == 'Approval'){
					jQuery("#Saction").text(' Approve');
					jQuery("#stype").val(stype);
				}else if(stype == 'Acknowledge'){
					jQuery("#Saction").text(' Acknowledge these entry(s)');
					jQuery("#stype").val(stype);
				}
				if (!deleteMessageDialog) {
					initDeleteMessageDialog(objId,stype);
				}
				if(objId == 'bulk') {
					var selectedrowindexes = jQuery("#jqxgrid").jqxGrid('getselectedrowindexes');
					// alert(selectedrowindexes)
					var selectedRowsCount = jQuery("#jqxgrid").jqxGrid('getselectedrowindexes').length;
					var rowscount = jQuery("#jqxgrid").jqxGrid('getdatainformation').rowscount;
					jQuery("#jqxgrid").jqxGrid('beginupdate');
					selectedrowindexes.sort();
					selectedrowindexes.reverse();
					var eventIds = '';
    				var first = true;
					// alert(selectedrowindexes)
					var eventIds = '';var sls = ''; var str='';var eventmemoref = '';var temp = '';var ack = 0;var acked = 0;var ackindex='';var ackedindex='';var stfindex='';
					var hlt = 0;var lock = 0;var pay = 0;var fin = 0;var stf = 0;var lockindex='';var hltindex='';var rhlt = 0;var rhltindex='';var rtn = 0;var k=0;
					var rtnindex='';var verify = 0;var verifyindex='';var finindex='';var payindex='';
					var first = true;var ackbool = true;var ackedbool = true;var paybool = true;var lockbool = true;var verifybool = true;var hltbool = true;var rhltbool = true;var rtnbool = true;var stfbool = true;var finbool = true;
						for (var m = 0; m < selectedrowindexes.length; m++) {
							var selectedrowindex = selectedrowindexes[selectedrowindexes.length - m - 1];
							if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
												var id = jQuery("#jqxgrid").jqxGrid('getcellvalue', selectedrowindex, 'id');
													if(first) {
														first = false;
													}
													else {
														eventIds += ',';
													}
													eventIds += id;
								//jQuery("#jqxgrid").jqxGrid('deleterow', id);
							}
						}

					if(stype=='Return'){
						   jQuery.ajax({
					            url: '<?php echo base_url(); ?>index.php/billprocess/return_action',
					            type: "post",
					            data: { memo_ref_no : eventmemoref },
					            datatype: 'html',
					            success: function(response){
						             jQuery('#return_detail').empty();
						             jQuery('#return_detail').append(response);
									 jQuery("deleteMessageDialog").attr("width", "650");
									 jQuery('#con_back').text('Continue');
									 jQuery('#return_detail').show();
									 jQuery('#cpetty tbody').empty();
									 jQuery('#cpetty tbody').append(str);
									 	jQuery('.rtn_reason').multipleSelect({ placeholder: "Select Reason" });
									 jQuery("#cc").val(k);
									   jQuery('#cpetty').hide();
					             },
					            error:   function(model, xhr, options){
					                alert('failed');
					            },
					        });
						}
						jQuery("#jqxgrid").jqxGrid('endupdate');
					if(stype=='Halt' || stype=='Verification' || stype=='Approval'){
						jQuery("#return_detail").empty();
						jQuery('#cpetty tbody').empty();
						if(stype=='Verification'){
							jQuery('#remark_reason').text('Finance Remarks');
						}
						if(stype=='Approval'){
							jQuery('#remark_reason').text('Finance Remarks');
						}
						// alert(str)
						jQuery('#cpetty tbody').append(str);
						jQuery("#cc").val(k);
						jQuery('#cdiv').show();
						 jQuery('#cpetty').show();
					}else{
						jQuery('#cdiv').hide();
					}
					if(!eventIds){
						jQuery("#errhead").text('We are Sorry ');
						jQuery("#errdetail").text('Please Select at least one entry For '+stype+' !!!!');
						deleteMessageDialog.hide();
						showInfoDialog( 'SelctOnetitle', 'SelctOnebody', 'WARN' )
						return;
					}

					$('deleteEventId').value = eventIds;
					//$('sl_ids').value = sls;
					//$('memoref').value = eventmemoref;
				}else {
					$('deleteEventId').value = objId;
					$('memoref').value = objId;
				}
				resetDeleteMessageErrors();
				deleteMessageDialog.show();
		}
		
    </script>

    <div id="jqxgrid"  style=" margin: 10px auto"></div>    
    <div style="float:left">
   
		<? if(ADD==1){?>
			<a style="text-decoration:none" onclick="javascript:EOL.messageBoard.open(this.href, (jQuery(window).width()-70), jQuery(window).height(), 'yes'); return false;" 
		    href="<?=base_url()?>index.php/rent_schedule_payment/from/add" title=""><input type="button" class="buttonStyle"  value="Add New" id="sendButton" /></a>
	    <? }?>

	    <? if(FIN_VERIFY==1){?>
		
			<input type="button" id="delete5" name="delete5" class="buttonStyle" onclick="delete_records('bulk','Verification');" value="Finance Verify" style="width:114px !important;" />
			
	    <? }?>
	    <? if(APPROVE==1){?>
	    	<input type="button" id="delete6" name="delete6" class="buttonStyle" onclick="delete_records('bulk','Approval');" value="Approval" style="width:114px !important;" />
	    <? }?>
	</div>

  

	
  <!-- Delete information-->
<style>
* { padding:0px; margin:0px; } 
</style>
			<div id="popupWindow">
                <div id="windowHeader">
                   <h3>Bill Details</h3>
                </div>
                <div  id="windowContent">
                    <!-- <table class="security_deposit_style">
					    <tr>
					        <th>Security_deposit Name</th>
					        <th>Price</th>
					        <th>Quantity</th>
					        <th>Total</th>
					    </tr>
					     <tbody id="load_bill_security_deposits">
					    	
			    		</tbody>
		  			</table> -->
		  			 
                </div>
            </div>
	  
     <input type="hidden" id="val" name="val" value="" />
     <input type="hidden" id="row" name="row" value="" />
<input name="verifyno" id="verifyno" value="" type="hidden">
	<input name="verifyrow" id="verifyrow" value="" type="hidden">
        <div id="deleteMessageDialogContent"  style="display:none">
          <div class="hd"><h2 class="conf">Are you sure you want to <span id="Saction"></span> these Sechedule Payment info(s)?</h2></div>
          <form method="POST" name="deleteMessageform" id="deleteMessageform"  style="margin:0px;">
            <input name="deleteEventId" id="deleteEventId" value="" type="hidden">
            <input name="action" value="DeleteMessage" type="hidden">
            <input name="stype"  id="stype" value="delete" type="hidden">
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
		<div style="display: none;" id="SelctOnebody">Please select at least One Entry.</div>

 	
		<!-- Delete information-->
    	<div id="verifyMessageDialogContent"  style="display:none">
          <div class="hd"><h3 class="conf" id="conf_msg">Are you sure you want to reset this entry?</h3></div>
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