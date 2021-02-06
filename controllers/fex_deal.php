<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class fex_deal extends CI_Controller {

	function __construct()
    {
        parent::__construct();	
		
		$this->output->set_header('Last-Modified:'.gmdate('D, d M Y H:i:s').'GMT');
		$this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate');
		$this->output->set_header('Cache-Control: post-check=0, pre-check=0',false);
		$this->output->set_header('Pragma: no-cache');		
		$this->load->model('fex_deal_model', '', TRUE);
	}
	
	function view ($menu_group,$menu_cat)
	{		
		$data = array( 	
					'menu_group'=> $menu_group,
					'menu_cat'=> $menu_cat,
					'pages'=> 'fex_deal/pages/grid',			   			   
				   	'per_page' => $this->config->item('per_pagess')
				   );
		$this->load->view('grid_layout',$data);
	}
	function grid()
	{		
		$this->load->model('fex_deal_model', '', TRUE);
		$pagenum = $this->input->get('pagenum');
		$pagesize = $this->input->get('pagesize');
		$start = $pagenum * $pagesize;
		
		$result=$this->fex_deal_model->get_grid_data($this->input->get('filterscount'), $this->input->get('sortdatafield'), $this->input->get('sortorder'),$pagesize, $start);
				
		$data[] = array(
		   'TotalRows' => $result['TotalRows'],		   
		   'Rows' => $result['Rows']
		);		
		echo json_encode($data);		
	}
		
	function from($add_edit='add',$id=NULL,$editrow=NULL)
	{
		$result=$this->fex_deal_model->get_info($add_edit,$id);
		$BankNature=is_object($result)? $result->BankNature!=''?" and BankNature='".$result->BankNature."' ":'':'';
		
		$currencyid1=is_object($result)?$result->OurCurrencyId:'0';
		$currencyid2=is_object($result)?$result->CounterPartyCurrency:'0';
		
		$v_cond=is_object($result)? $result->DealTypeId=='1'?" and limit_verify_sts=1 ":'':'';
		$time = $this->fex_deal_model->get_eod();
		$current_time = date("H:i:s");
		$pre_time = "08:00:00";
		if(strtotime($time)<strtotime($current_time) || strtotime($pre_time)>strtotime($current_time)) {
			 $msg = 'The time is '.date('h:i:s a', strtotime($current_time)).'. A deal has to be made between '
			 .date('h:i:s a', strtotime($pre_time)).' and '.date('h:i:s a', strtotime($time)).'.';
			
			} else {
			 $msg = '';
			}
		$data = array( 	
				   'option' => '',
				   'add_edit' => $add_edit,				  
				   'deal_type' => $this->fex_deal_model->get_parameter_data('b2_dealtype','name',"sts = '1' AND id<>3 "),
				   'deal_nature' => $this->fex_deal_model->get_parameter_data('b2_dealnature','id',"sts = '1' "),
				   'deal_purpose' =>array('Funding'=>'Funding','Trading'=>'Trading'),
				   'counter_party' => $this->fex_deal_model->get_parameter_data('b2_counterparty','name',"sts = '1' ".$BankNature." ".$v_cond),
				   'PaymentAgent' => $this->fex_deal_model->get_parameter_data('b2_account','BankName',"sts = '1' and CurrencyId='".$currencyid1."' "),
				   'ReceivingAgent' => $this->fex_deal_model->get_parameter_data('b2_account','BankName',"sts = '1' and CurrencyId='".$currencyid2."' "),
				   'counterparty_currency' => $this->fex_deal_model->get_parameter_data('b2_currency','name',"sts = '1' and id<>2 "),
				   'counterpartyother_currency' => $this->fex_deal_model->get_parameter_data('b2_currency','name',"sts = '1' "),
				   'result' => $result,
				   'msg' => $msg,
				   'id' => $id,
				   'pages'=> 'fex_deal/pages/form',
				   'editrow' => $editrow			   
				   );
		$this->load->view('fex_deal/form_layout',$data);
	}
	
	function ajax_comma()
	{
		if ($this->input->post('val') >0){
			echo $this->user_model->commausd($this->input->post('val'));
		}else{
			echo 0.00;
		}		
	}
		
	function add_edit_action($add_edit=NULL,$edit_id=NULL)
	{
		$dealer_limit_sts=0;
		$cp_limit_sts=0;
		$Message_1=''; 
		$swap=$this->input->post('hiddenswap');
		$text=array();
		if ($this->session->userdata['user']['login_status'])
		{
			$amount = $this->input->post('OurAmount');
			$currency = $this->input->post('counterpartycurrency');
			$recv_agent = $this->input->post('ReceivingAgent');
			$fex_deal_type = $this->input->post('jqxdeal_type');
			$fex_deal_purpose = $this->input->post('deal_purpose');
		
			$v_date = date('Y-m-d',strtotime(str_replace('/', '-', $this->input->post('ValueDate'))));
			
			$dr_msg=$this->user_model->nostro_holiday($this->input->post('ReceivingAgent'),$v_date);
			
			
			if($dr_msg!='OK'){
				$text[]=$dr_msg." , Receiving Agent";
			}
			$cr_msg=$this->user_model->nostro_holiday($this->input->post('PaymentAgent'),$v_date);
			if($cr_msg!='OK'){
				$text[]=$cr_msg." , Payment Agent";
			}
			
			if($edit_id==NULL){$edit_id = 0;}
			// dealer limit check
			
			if($this->input->post('hiddenswapdealid')<=0)
			{
				$dealer_limit_check = $this->user_model->dealer_limit_check('fex',$this->input->post('hiddenswap'),$amount,$currency,$edit_id,$fex_deal_type,$fex_deal_purpose);
				if($dealer_limit_check == 3)
				{
					$text[]="Sorry, Your Limit should be verified\n";
				}
				else if($dealer_limit_check == 1)
				{
					$Message_1="Sorry, Your Deal Amount Limit Crossed\n";
					$dealer_limit_sts=1;
				}
				else if($dealer_limit_check == 2)
				{
					$Message_1="Sorry, Your Today's Total Deal Amount Limit Crossed\n";
					$dealer_limit_sts=1;
				}
			}
			
			// counter party limit check
			if($this->input->post('counterpartycurrencyother')==2)
			{
				$amount=($this->input->post('hiddenmultidivi')=='m'?($this->input->post('OurAmount')*$this->input->post('Rate')):($this->input->post('OurAmount')/$this->input->post('Rate')));
				$currency=2;
			}
			//echo 'lll';exit;
			if( $swap==0 || ($swap==1 && $this->input->post('hiddenswapdealid')!=0 )){  // not swap or swap 2nd leg
				
				$cp_limit_check = $this->user_model->cp_limit_check($this->input->post('counterparty'),'fex',$this->input->post('jqxdeal_nature'),$amount,$currency,$recv_agent,$this->input->post('hiddenswap'),$edit_id);
				// echo $recv_agent.'<br />';
				// echo $cp_limit_check;exit;
			}else{
				$cp_limit_check='';
			}
		
			if(current(explode('#',$cp_limit_check)) == 'no')
			{
				$text[]="Sorry,Counterparty Limit Crossed ";
				$cp_limit_sts=1;
				$id=$this->fex_deal_model->add_edit_action($add_edit,$edit_id,$cp_limit_check,$dealer_limit_sts,$cp_limit_sts);
			}
			else
			{
				if(count($text)<=0){
					$arr_limit = explode('#',$cp_limit_check);
					if( isset($arr_limit[3]) || isset($arr_limit[4])){ 
						
						$Message_1='Deal Successfully created by utilizing Other Line'; 
						$cp_limit_sts=1;
					}
					$id=$this->fex_deal_model->add_edit_action($add_edit,$edit_id,$cp_limit_check,$dealer_limit_sts,$cp_limit_sts);
				}
			}
		}
		else{
			$text[]="Session out, login required";
		}	
		
		$Message='';
		if($id > 0 && count($text)<=0){
			$Message='OK';
			$row=$this->fex_deal_model->get_add_action_data($id);
		}else if($id > 0 && count($text) > 0){

			$row=$this->fex_deal_model->get_add_action_data($id);
			$Message='OK';
			$Message_1='Counterparty Limit Crossed';
		}else{
			for($i=0; $i<count($text); $i++)
			{
				if($i>0){$Message.=" \n ";}
				$Message.=$text[$i];				
			}	
			$row[]='';	
		}
		
		$var =array();  
		$var['Message']=$Message;
		$var['Message_1']=$Message_1;
		$var['row_info']=$row;
		echo json_encode($var);
	}
	
	function delete_action()
	{
		$Message='OK';
		$row[]='';
		if ($this->session->userdata['user']['login_status'])
		{			
			$id=$this->fex_deal_model->delete_action();
			if($this->input->post("type")=='delete'){$row[]='';	}
			else{$row = $this->fex_deal_model->get_add_action_data($id);}
		}else{
			$Message='Session out, login required';
		}
			
			$var =array();  
			$var['Message']=$Message;
			$var['row_info']=$row;
			echo json_encode($var);
	}
	
	function get_banknature($add_edit=NULL,$edit_id=NULL)
	{
		$banknatu='';
		if ($this->input->post('val') != ""){		
			if($this->input->post('val')=='2'){$banknatu='Corporate';}else{$banknatu='Bank';}
			
			$v_cond=($banknatu=='Bank')?" and limit_verify_sts='1' ":'';		
			$num_row=$this->fex_deal_model->get_parameter_data('b2_counterparty','name',"sts='1' and BankNature='".$banknatu."' ".$v_cond);				
			$var =  
			array(
				"Message"=>$this->input->post('val'),
				"Status" =>$num_row
			);
			echo json_encode($var);
    	}
	}
	function get_valudate($add_edit=NULL,$edit_id=NULL)
	{
		$str='';$tomorrow ='';
		if ($this->input->post('val') != ""){		
			if($this->input->post('val') == '1')
			{
			$str='<input  type="text" value="'.date("d/m/Y").'" readonly="true" name="ValueDate"  id="ValueDate" class="text-input jqx-input jqx-rc-all" />';
			}
			else if($this->input->post('val') == '2')
			{
			$tomorrow = mktime(0, 0, 0, date("m") , date("d")+1, date("Y"));
		
			$str='<input  type="text" value="'.date("d/m/Y",$tomorrow).'"  readonly="true" name="ValueDate"  id="ValueDate" class="text-input jqx-input jqx-rc-all"/><script type="text/javascript" charset="utf-8">datePicker("ValueDate");</script>';
			}
			else if($this->input->post('val') == '3')
			{
			$tomorrow = mktime(0, 0, 0, date("m") , date("d")+2, date("Y"));
			$str='<input  type="text" value="'.date("d/m/Y",$tomorrow).'"  readonly="true" name="ValueDate"  id="ValueDate" class="text-input jqx-input jqx-rc-all"/><script type="text/javascript" charset="utf-8">datePicker("ValueDate");</script>';
			}
			else if($this->input->post('val') == '4')
			{
			$tomorrow = mktime(0, 0, 0, date("m") , date("d")+3, date("Y"));
			$str='<input  type="text" value="'.date("d/m/Y",$tomorrow).'"  readonly="true" name="ValueDate"  id="ValueDate" class="text-input jqx-input jqx-rc-all"/><script type="text/javascript" charset="utf-8">datePicker("ValueDate");</script>';
			}
			else if($this->input->post('val') == '5')
			{
			$tomorrow = mktime(0, 0, 0, date("m") , date("d")+3, date("Y"));
			$str= '<input  type="text" value="'.date("d/m/Y",$tomorrow).'"  readonly="true" name="ValueDate"  id="ValueDate" class="text-input jqx-input jqx-rc-all"/><script type="text/javascript" charset="utf-8">datePicker("ValueDate");</script>';
			}
			else
			{
			$str='';
			}				
			$var =  
			array(
				"Message"=>$this->input->post('val'),
				"Status" =>$str
			);
			echo json_encode($var);
    	}
	}
	function get_payrecagent()
	{	
		if ($this->input->post('val') != ""){					
			$num_row=$this->fex_deal_model->get_parameter_data('b2_account','BankName',"sts='1' and CurrencyId='".$this->input->post('val')."'");				
			$var =  
			array(
				"Message"=>$this->input->post('val'),
				"Status" =>$num_row
			);
			echo json_encode($var);
    	}
	}
	
	function get_cp_re($deal_serial=NULL)
	{
		$ncc='';
		$ncc_table='';
		$d_info=$this->fex_deal_model->get_parameter_data('b2_fex_deal','Serial'," sts>'1' and Serial='".$deal_serial."'");
		$sl='<select name="TheirReceiveAt" id="TheirReceiveAt"  style="width:650px">
                <option  selected="selected" value="">Select Bank SSI</option>';
		foreach($d_info as $row)
		{				
				$ncc=$row->cashcurrecy;
				if($row->cashcurrecy=='ncc'){
					$sl.='<option value="00">Principal Office</option>';	
					$ncc_table='<br /><br /><strong>Cash currency:</strong>
                <table width="60%"  border="1" cellpadding="2" cellspacing="1" style="border-collapse:collapse" align="center" id="ncc_proTab">								
                    <tr valign="top">
                        <td width="6%" ><img src="'.base_url().'images/delete.png" border="0" />
                        </td>
                        <td width="47%" ><strong>Denominations</strong></td>
                        <td width="47%" ><strong>Bank Notes</strong></td>
                        </tr>
                        <tr valign="top" id="ncc_row1">
                        <td >&nbsp;<input type="hidden" id="ncc_deleted1" name="ncc_deleted1" value="0"></td>
                        <td ><input class="text"  style="width:99%" maxlength="10" type="text" name="deno_1" id="deno_1" ></td>
                        <td ><input class="text" style="width:99%" type="text" name="bnote_1" id="bnote_1" ></td>		
                   </tr>							  			   
                </table>					
                <table width="59%"  border="0" align="center" cellpadding="3" cellspacing="0" >							   
                   <tr>
                    <td  colspan="3" style="text-align:right">
                            <input type="hidden" id="ncc_lastCountervalue" name="ncc_lastCountervalue" value="1" />
                            <a  onclick="fnAddItemRow(\'ncc_\');"><span><img src="'.base_url().'images/btnL3Add.gif" border="0" /></span></a> 									
                    </td>								
                   </tr>							   
                </table>';
					
				}
				
				if($row->DealTypeId==2 && $row->TheirSettltement=='B')
				{
					$str="	SELECT cur.ISO_4217Code AS CurIso, cpssi.BankName AS ReceiveAtBName, cpssi.AccountNumber AS AccountNumber, cpssi.id AS SSIid,cPty.Name as CPname  
					FROM 
					(
						SELECT  IF( WeHave='Sold',OurCurrencyId,CounterPartyCurrency) d_curId
						FROM b2_fex_deal WHERE SERIAL='".$deal_serial."' AND sts>1 
					) fed 
					LEFT OUTER JOIN b2_counterpartyssi cpssi ON(fed.d_curId =cpssi.CurrencyId) 
					left outer join b2_counterparty cPty on(cpssi.cp_id =cPty.id) 
					LEFT OUTER JOIN b2_currency cur ON(fed.d_curId =cur.id) WHERE cpssi.sts='1' ";
					$q=$this->db->query($str);
					foreach($q->result() as $r)
					{			
						$sl.='<option value="'.$r->SSIid.'">'.$r->CPname.' SSI: '.$r->ReceiveAtBName.'&nbsp;-&nbsp;'.$r->CurIso.'&nbsp;-&nbsp;A/C:&nbsp;'.$r->AccountNumber.'</option>';	
					}
					
				}else{
					$str="	SELECT cur.ISO_4217Code AS CurIso, cpssi.BankName AS ReceiveAtBName, cpssi.AccountNumber AS AccountNumber, cpssi.id AS SSIid 
					FROM 
					(
						SELECT  CounterPartyId, IF( WeHave='Sold',OurCurrencyId,CounterPartyCurrency) d_curId
						FROM b2_fex_deal WHERE SERIAL='".$deal_serial."' AND sts>1 
					) fed 
					LEFT OUTER JOIN b2_counterpartyssi cpssi ON(cpssi.cp_id=fed.CounterPartyId AND fed.d_curId =cpssi.CurrencyId) 					
					LEFT OUTER JOIN b2_currency cur ON(fed.d_curId =cur.id) WHERE cpssi.sts='1' ";
					$q=$this->db->query($str);
					foreach($q->result() as $r)
					{			
						$sl.='<option value="'.$r->SSIid.'">'.$r->ReceiveAtBName.'&nbsp;-&nbsp;'.$r->CurIso.'&nbsp;-&nbsp;A/C:&nbsp;'.$r->AccountNumber.'</option>';	
					}
				}			
		}
		$sl.='</select>';
		
		
		
		
		
		$var =array();  
		$var['ncc_table']=$ncc_table;
		$var['ncc']=$ncc;
		$var['row_info']=$sl;
		echo json_encode($var);
	}

	function frontVerifySelected(){
		$Message = $this->fex_deal_model->frontVerifySelected();

		$data = array('Message' => $Message);
		echo json_encode($data);
	}
	
}
?>