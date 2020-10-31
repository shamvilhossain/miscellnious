<?php
$this->load->view('includes/header');
?>
<link rel="stylesheet" href="<?=base_url()?>css/style_upload.css">
<script type="text/javascript" src="<?=base_url()?>js/jquery.ajaxupload.js"></script>
<body class="dialog">
<style type="text/css"> img{border:none;} </style>
<script language="javascript" type="text/javascript">
	var fileCount=0;	
 jQuery(document).ready(function($){
 	
	var theme = 'classic';
 	uploadedfile("<?=$locksts?>","<?=$custid?>","<?=$agree_id?>");
		
	jQuery("#submitBtn").click(function() {
		if(startUpload('myfile'))
		{
			jQuery("#hidden_cust_id").val('<?=$custid?>');
			jQuery('#submitBtn').hide();
			jQuery('#f1_upload_process').show();
			jQuery("#newUpBox").submit();
		}
	
	});
	
});

var options = {
	success: function(responseText)
	{
		stopUpload(responseText,1);
		jQuery('#submitBtn').show();
		jQuery('#f1_upload_process').hide();
	}
};
jQuery("#newUpBox").ajaxForm(options);


function startUpload(fileField)
{
	flag=0;					
	var fileName = jQuery("#"+fileField).val().toLowerCase();
	
	var img_array=new Array();
	img_array=fileName.split(".");									
	var alen=img_array.length;
	if(alen>0){alen=parseInt(alen-1);}else{alen=0;}					
	if (img_array[alen] != 'pdf' && img_array[alen] != 'docx' && img_array[alen] != 'doc' && img_array[alen] != 'xls' && img_array[alen] != 'xlsx')
	{
		alert('Upload File should be pdf extension!');	
		flag=1;			
		return false;					
	}//end if not pdf
	var img_arrayaaa=new Array();
	img_arrayaaa=fileName.split("\\");									
	var alenaa=img_arrayaaa.length;
	if(alenaa>0){alenaa=parseInt(alenaa-1);}else{alenaa=0;}	
	
	if(/^[a-zA-Z.0-9-_& ]*$/.test(img_arrayaaa[alenaa]) == false)
	{
		alert('Your file name contains illegal characters!!');
		return false;
	}	
	 
	return true;
}

function uploadedfile(locksts,custid,agree_id)
{
 jQuery.post("<?=base_url()?>index.php/agreement/ajaxloadfile_edit_new/2/"+custid+'/'+agree_id,{}, function(data){	

		var ds=data.split('####');	

		jQuery("#divGrid").empty();
		jQuery("#divGrid").html(ds[0]);	
		jQuery("#hidden_count_val").val(ds[1]);	
		window.opener.jQuery("#uploaded_file_count"+custid).val(ds[1]);
		//alert(custid);
       // alert(jQuery("#uploaded_file_count"+custid).val());
		<? if($view == 1) {?>
		window.opener.document.getElementById('cust_cib_files').value = jQuery("#hidden_count_val").val();
		if(ds[1] > 0)
		{
			//alert(ds[2]);
			//window.opener.jQuery('#filescount').html('<img height="16" width="16" src="<?base_url()?>images/pdf_icon.gif" style="vertical-align: top; margin-left:5%" />');
			//window.opener.jQuery('#filescount'+custid).html(jQuery("#hidden_count_val").val()+' files uploaded');
			window.opener.jQuery('#filescount'+custid).html(ds[2]);
			//window.opener.jQuery("#uploaded_file_count"+custid).val(ds[1]);
			//alert(window.opener.jQuery("#uploaded_file_count"+custid).val());

		}
		else
		{
			window.opener.jQuery('#filescount'+custid).html('');
			jQuery('#enlisted'+custid).attr('checked', false);
		}
		<? }else{ ?>
			jQuery(".delcolumn").html('');
		<? } ?>
	});
}


function remove_file_edit(sessionid, filename, type)
{
	
	if(confirm('Are you sure want to remove?')==true)
	{	
		 jQuery.post("<?=base_url()?>index.php/agreement/remove_file_edit/"+sessionid+"/"+filename+"/"+type+"", {}, function(data){				 			
		 stopUpload(data);					
		});		
	}
}

function stopUpload(success,showFiled)
{
	var result = '';
	if (success==1){
	 result = '<span class="msg">The file was uploaded successfully!<\/span><br/><br/>';
	}
	else if (success == 2){
	 result = '<span class="emsg">The file size should be less than or equal to 20 MB!<\/span><br/><br/>';
	}
	else if (success == 3){
	 result = '<span class="msg">The file was removed successfully!<\/span><br/><br/>';
	}
	else {
	 result = '<span class="emsg">There was an error during file oparation!<\/span><br/><br/>';
	}
	
	
	//uploadedfile("<?=$custid?>");
	uploadedfile("<?=$locksts?>","<?=$custid?>","<?=$agree_id?>");

	jQuery('#f1_upload_process').hide();
	jQuery('#f1_upload_result').html(result);
	jQuery('#f1_upload_result').show();
	jQuery("#myfile").val('');
	
}

function popitup_w(url,name)
{
	var winl = (screen.width - 800) / 2;
	var wint = (screen.height - 540) / 2;
	//alert(url);
	newwindow=window.open(url,'jesy','height=400, width=920,top='+wint+', left='+winl+", ,toolbar=no,status=no,scrollbars=yes,resizable=yes,menubar=no,location=no,direction=no");

	return false;
}

</script>   		


<div style="width:100%">
	<table border="0" cellpadding="0" cellspacing="0" style="width:100%">
		<tr>
			<td width="50%" style="padding: 1%;font-weight:bold">EBL Rent Agreement Files Upload</td>
			<td width="50%" style="padding: 1%;text-align: right;font-size:12px">
			<img src="<?=base_url()?>images/Close_Box_Red.png" style="vertical-align:top;cursor:pointer;height:23px" onClick="window.close();" title="close window"/></td>
		</tr>
	</table>
	<div class="nonSelectableText" style="width: 100%;" id="divUpload">           
		<div style="width: 99%; height:210px;" id="divGrid"></div>	
	</div>
	<div style="width: 99.4%; height:5px; background-color:#3164ca"></div> 
	<? if($view == 1) {?>
	<div id="content" style="width: 99%; height:140px">
		<form action="<?=base_url()?>index.php/agreement/upload_by_ajax_edit"  method="post" enctype="multipart/form-data" id="newUpBox" style="min-height:125px !important"  >
			<p id="f1_upload_process" style="display:none">Loading...<img src="<?=base_url()?>images/loader.gif" /></p>
			<p id="f1_upload_result" style="display:none;text-align:center;"></p>
				<table cellpadding="2" cellspacing="0" border="0" width="90%" style="margin:0 0 0 20px;color:#000000;font-size:12px; font-weight:bold;">
					<tr>
						<td colspan="2"  align="center" style="font-size:15px;color:#343434">Upload New File</td>
					</tr>
					<tr>
						<td width="15%"  align="left">File:</td>
						<td width="85%">
						<input name="myfile[]" multiple id="myfile" type="file" style="width:98%" />
						<input name="agree_id" value="<?=$agree_id?>" id="agree_id" type="hidden" style="width:98%" />

						</td>
					</tr>
					<tr>
						<td></td>
						<td align="left" style="padding-top:20px;"><input type="button" name="submitBtn" id="submitBtn" class="sbtn" value="Upload" style="background-color:#374AB6" /></td>
					</tr>					
				</table>
			<input type="hidden" name="hidden_closed_value" id="hidden_closed_value" value=""  />
			<input type="hidden" name="hidden_count_val" id="hidden_count_val" value="0"  />
			<input type="hidden" name="hidden_cust_id" id="hidden_cust_id" value="<?=$custid?>"   />
		 </form>
	</div>
	<? } ?>
</div>
			
</body>
</html>