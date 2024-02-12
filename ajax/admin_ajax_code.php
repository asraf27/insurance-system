<?php
ini_set('max_execution_time', 0);  set_time_limit(0); ignore_user_abort(true);
include('../includes/config.php');

require_once('../spreadsheet-plugin/php-excel-reader/excel_reader2.php');
require_once('../spreadsheet-plugin/SpreadsheetReader.php');
//exit("Action Not Allowed!!!!");	
?>
<?php
global $db;
$user_id = state('user_id');
$user_name = state("user_name");
if(!$user_id){
  $data_sucess['sucess'] = 0;  
  $data_sucess['message'] = "Authentication Required.";
  echo json_encode($data_sucess);
  die();  
}


$action = $_REQUEST['action'];

switch($action)
	{
        case 'load_agent':
        $agentType = trim($_POST['agent_type']);
        $agentID = trim($_POST['agent_num']);
        $agentLevel = trim($_POST['agent_level']);
        $policy_id = trim($_POST['policy_id']);
        $agentLevelSub = $agentLevel + 1;

        //echo $agentID;        
        $checkAgentEditData=getSingleAgentCommission($agentID,$agentLevel,$policy_id);
        //print_r($checkAgentEditData);
        if ($checkAgentEditData) {

            $agentInfo = $checkAgentEditData;

        }else{
          $agentInfo = getSingleAgent($agentID);
       }
       
        //$agentSub = getAgentLists($agentType,$agentLevelSub,$agentID);
        $agentSub = getSubAgentLists($agentType,$agentLevelSub,$agentID);
        
        if($agentInfo){
          $data_sucess['sucess'] = 1;
          $data_sucess['agent_data'] = $agentInfo;
          $data_sucess['agent_sub'] = $agentSub;
        }else{
           $data_sucess['sucess'] = 0; 
        }
        echo json_encode($data_sucess);
        break;
        
        case 'load_coverage':
        $planID = trim($_POST['plan_num']);
        $coverageLists = getPolicyCoverages($planID);
        if($coverageLists){
           $data_sucess['sucess'] = 1;
           $data_sucess['coverage_data'] = $coverageLists; 
        }else{
           $data_sucess['sucess'] = 0; 
        }
        echo json_encode($data_sucess);
        break;
        
        case 'load_deductible':
        $coverageID = trim($_POST['cov_num']);
        $deductibleLists = getPolicyDeductibles($coverageID);
        if($deductibleLists){
           $data_sucess['sucess'] = 1;
           $data_sucess['deductible_data'] = $deductibleLists; 
        }else{
           $data_sucess['sucess'] = 0; 
           //$data_sucess['message'] = $coverageID; 
           
        }
        echo json_encode($data_sucess);
        break;  
        
        case 'create_policy_number':
        
        $year = trim($_POST['year_fval']);
        $plan = trim($_POST['plan_fval']);
        $policy_id = trim($_POST['policy_num']);
        
        if(!$policy_id)
        //$policy_id = createNewPolicy();
        $policy_id = getLastPolicy();
        
        
        if($policy_id && $plan && $year){
           $year_last_two = substr( $year, -2);
           $plan_code = getPolicyPlanCode($plan);
           if($year_last_two && $plan_code){
            $policy_number = "1".$plan_code.$year_last_two."-";
            $policy_id_len = strlen($policy_id);
            if($policy_id_len<5){
            $policy_id_code =  $policy_id;
            while(strlen($policy_id_code)<5){
              $policy_id_code = "0". $policy_id_code; 
            }
             
            }else{
                $policy_id_code = $policy_id;
            }
            $policy_number = $policy_number.$policy_id_code;
            $data_sucess['sucess'] = 1;
            $data_sucess['policy_nu'] = $policy_id; 
            $data_sucess['policy_number'] = $policy_number;
           }
           
           
        }else{
           $data_sucess['sucess'] = 0; 
           //$data_sucess['message'] = $coverageID; 
           
        }
        echo json_encode($data_sucess);
        break; 
        
        
        
        case 'save_insured':
        
        $checkPermission = checkUserAccessRole('Policies');
        if(!$checkPermission){
            $data_sucess = array("sucess"=>0,"pr"=>1,"message"=>"Permission error");
            echo json_encode($data_sucess);
            break;
        }
        
        $insured_id = trim($_POST['insured_num']);
        $policy_id = trim($_POST['policy_num']);
        $process_filter = trim($_POST['process_filter']);
        
        if($policy_id){
        $interview = $db_data['interview'] = trim($_POST['interview']);
        $order = $db_data['ins_order'] = trim($_POST['order']);
        $first_name = $db_data['first_name'] = trim($_POST['first_name']);
        $last_name = $db_data['last_name'] = trim($_POST['last_name']);
        if(trim($_POST['dob']))
        $dob = $db_data['dob'] = date("Y-m-d",strtotime(trim($_POST['dob'])));
        $relation = $db_data['idrelation'] = trim($_POST['relation']);
        if(trim($_POST['effective']))
        $effective = $db_data['effectivedate'] = date("Y-m-d",strtotime(trim($_POST['effective'])));
        $age = $db_data['age'] = trim($_POST['age']);
        $gender = $db_data['gender'] = trim($_POST['gender']);
        $ninety_day_waiver = $db_data['ninety_day_waiver'] = trim($_POST['ninety_day_waiver']);
        if(trim($_POST['effective_ninety_day']))
        $effective_ninety_day = $db_data['effective_ninety_day'] = date("Y-m-d",strtotime(trim($_POST['effective_ninety_day'])));
        $ridermater = $db_data['ridermat'] = trim($_POST['ridermater']);
       	$ridercomp = $db_data['ridercommat'] = trim($_POST['ridercomp']);
        $activelab = $db_data['active'] = trim($_POST['activelab']); 
        if(trim($_POST['ins_inactivate_date'])) 
        $ins_inactivate_date = $db_data['dateinactive'] = date("Y-m-d",strtotime(trim($_POST['ins_inactivate_date'])));
        $ins_email = $db_data['email'] = trim($_POST['ins_email']); 
        $db_data['idpolicy'] = $policy_id;
        
        if($process_filter){
            if($insured_id)
            $insuredOldData = getHealthSingleInsured($insured_id);
            //else
            //$insuredOldData = array('idpolicy'=>$policy_id);
        }
        
        $insured_id = saveHealthInsured($policy_id,$db_data,$insured_id);
        if($insured_id){
          if($process_filter && $insuredOldData) 
          addAuditsPolicyInsuredData($insuredOldData,$db_data);
          
          $data_sucess['insured_number'] = $insured_id;
          $data_sucess['data_row'] = trim($_POST['data_row_id']);
          
          $data_sucess['sucess'] = 1;  
        }else{
          $data_sucess['sucess'] = 0; 
          $data_sucess['message'] = 'Failed to save insured.';  
        }
        }else{
           $data_sucess['sucess'] = 0; 
           $data_sucess['message'] = 'Policy number not found.';  
        }
		echo json_encode($data_sucess);
	    break;
        
        
        case 'remove_insured':
        
        $checkPermission = checkUserAccessRole('Policies');
        if(!$checkPermission){
            $data_sucess = array("sucess"=>0,"pr"=>1,"message"=>"Permission error");
            echo json_encode($data_sucess);
            break;
        }
        
        $insured_id = trim($_POST['insured_num']);
        
        $delete_insured = removeHealthInsured($insured_id);
        if($delete_insured){
          $data_sucess['insured_number'] = $insured_id;
          $data_sucess['data_row'] = trim($_POST['data_row_id']);
          
          $data_sucess['sucess'] = 1;  
        }else{
          $data_sucess['sucess'] = 0; 
          $data_sucess['message'] = 'Failed to remove insured.';  
        }
        
        echo json_encode($data_sucess);
	    break;
        
        case 'load_rateups':
        $rate_up_type = trim($_POST['rate_up_type']);
        if($rate_up_type){
          $rateUPSingle = getSingleRateUpType($rate_up_type);
          //print_r($rateUPSingle);
          if($rateUPSingle){ 
             $data_sucess['sucess'] = 1;
             $data_sucess['rateuppercent'] = $rateUPSingle['rateuppercent'];
             $data_sucess['rateupamount'] = $rateUPSingle['rateupamount'];
          }
        }else{
           $data_sucess['sucess'] = 0; 
        }
        
        echo json_encode($data_sucess);
	    break;
        
        case 'save_rateups':
        
        $checkPermission = checkUserAccessRole('Policies');
        if(!$checkPermission){
            $data_sucess = array("sucess"=>0,"pr"=>1,"message"=>"Permission error");
            echo json_encode($data_sucess);
            break;
        }
        
        $rate_up_type = trim($_POST['rate_up_type']);
        $insured_number = trim($_POST['insured_number']);
        $rate_up_amount = trim($_POST['rate_up_amount']);
        if(($rate_up_type || $rate_up_amount) && $insured_number){
          //$rateUPSingle = getRateUpByInsured($insured_number);
          //print_r($rateUPSingle);
          //if($rateUPSingle)
          //$rateUpData['id'] = $rateUPSingle['id'];
          
          $rateUpData['idinsured'] = $rateUPSingle['id'];
          $rateUpData['rate_up_id'] = $rate_up_type;
          $rateUpData['amount'] = $rate_up_amount;
          
          $rateUPId = addRateUps($insured_number,$rateUpData);
          
          //print_r($rateUPSingle);
          if($rateUPId){ 
             $data_sucess['sucess'] = 1;
             $data_sucess['rate_id'] = $rateUPId;
             $data_sucess['insurednumber'] = $insured_number;
             
          }
        }else{
           $data_sucess['sucess'] = 0; 
        }
        
        
        echo json_encode($data_sucess);
	    break;
        
        case 'rate_up_changes_insured': 
        
        $insured_number = trim($_POST['insured_num']);
        
        if($insured_number){
          $rateUPSingle = getRateUpByInsured($insured_number);
         
          if($rateUPSingle){ 
            $rateUpType = getSingleRateUpType($rateUPSingle['idrateuptype']);
            //print_r($rateUpType);
            if($rateUpType){
               $data_sucess['sucess'] = 1;
               $data_sucess['rateuppercent'] = $rateUpType['rateuppercent'];
               $data_sucess['rateupamount'] = $rateUpType['rateupamount']; 
               $data_sucess['insurednumber'] = $insured_number; 
               $data_sucess['data_row'] = trim($_POST['data_row_id']);
               //print_r($data_sucess);
            }
             
             
          }
        }else{
           $data_sucess['sucess'] = 0; 
        }
        
        
        echo json_encode($data_sucess);
	    break;
        
        case 'delete_rateup': 
        
        $rate_id = trim($_POST['rateup_id']);
        
        if($rate_id){         
            $rateUpDelete = DeleteSingleRateUp($rate_id);            
            $data_sucess['sucess'] = 1;
        }else{
           $data_sucess['sucess'] = 0; 
        }
        
        echo json_encode($data_sucess);
	    break;
        
        case 'update_rateup': 
        
        $rate_id = trim($_POST['rateup_id']);
        $rateuptype_id = trim($_POST['data_rateuptype_id']);
        
        if($rate_id){         
            $rateupUpdate = UpdateSingleRateUp($rate_id,$rateuptype_id);            
            $data_sucess['sucess'] = 1;
        }else{
           $data_sucess['sucess'] = 0; 
        }
        
        echo json_encode($data_sucess);
	    break;
        
        
        case 'update_insured_premium':
        
        $premium['idinsured'] = trim($_POST['insured_number']);
        $premium['premiumBase'] = trim($_POST['premiumBase']);
        $premium['premiumCalculate'] = trim($_POST['premiumCalculate']);
        
        $auditCalculatePremium = state('c_premium');
        
        if(!$auditCalculatePremium){
         $policy_id = getPolicyIDByInsured($premium['idinsured']);
         addAudit(array("uid"=>$user_id,"idpolicy"=>$policy_id,"action"=>$user_name." Clicked Calculate Premiums"));
         state('c_premium',1);
        }
        
        if($premium['idinsured']){          
          $premiumId = updateInsuredPremium($premium);
          if($premiumId){ 
             $data_sucess['sucess'] = 1;             
          }else{
            $data_sucess['sucess'] = 0;
          }
        }else{
           $data_sucess['sucess'] = 0; 
        }
        
        
        echo json_encode($data_sucess);
	    break;
        
        case 'save_health_policy':
        
        
        $checkPermission = checkUserAccessRole('Policies');
        if(!$checkPermission){
            $data_sucess = array("sucess"=>0,"pr"=>1,"message"=>"Permission error");
            echo json_encode($data_sucess);
            break;
        }
        
        $policy_id = trim($_POST['policy_num']);
        $policy_number = $db_data['policynumber'] = trim($_POST['policy_number']);
        $date_cancelled = trim($_POST['date_cancelled']);
        if($date_cancelled)
        $db_data['datecancel']  = date("Y-m-d",strtotime($date_cancelled));
        $policy_carrier = $db_data['carrier'] = trim($_POST['policy_carrier']);
        $effective_date =  trim($_POST['effective_date']);
        if($effective_date)
        $db_data['effectivedate'] = date("Y-m-d",strtotime($effective_date));
        $plan = $db_data['idplan'] = trim($_POST['plan']);
        $coverage = $db_data['idcoverage'] = trim($_POST['coverage']);
        $deductible = $db_data['iddeductible'] = trim($_POST['deductible']);
        
        
        $group_id = $db_data['idgroup'] = trim($_POST['group_id']);
        $policy_rfid = $db_data['rfid'] = trim($_POST['policy_rfid']);
        $rfid_clams = $db_data['rfidclams'] = trim($_POST['rfid_clams']);
        $policy_status = $db_data['idstatus'] = trim($_POST['policy_status']);
        $cancel_reason = $db_data['idnotecancel'] = trim($_POST['cancel_reason']);
        $address_l1 = $db_data['addressl1'] = trim($_POST['address_l1']);
        $address_l2 = $db_data['addressl2'] = trim($_POST['address_l2']);
        $contact_city = $db_data['city'] = trim($_POST['contact_city']);
        $contact_country = $db_data['idcountry'] = trim($_POST['contact_country']);
        $contact_phone = $db_data['phone'] = trim($_POST['contact_phone']);
        $contact_work_phone = $db_data['workphone'] = trim($_POST['contact_work_phone']);
        $contact_cell_phone = $db_data['cellphone'] = trim($_POST['contact_cell_phone']);
        $contact_email = $db_data['email'] = trim($_POST['contact_email']);
        $rate_year = $db_data['idrateyear'] = trim($_POST['rate_year']);
        $payment_start = trim($_POST['payment_start']);
        if($payment_start)
        $db_data['paymentstart'] = date("Y-m-d",strtotime($payment_start));
        $payment_end = trim($_POST['payment_end']);
        if($payment_end)
        $db_data['paymentend'] = date("Y-m-d",strtotime($payment_end));
        $payment_cycle = $db_data['idpaycycle'] = trim($_POST['payment_cycle']);
        $date_due =  trim($_POST['date_due']);
        if($date_due)
        $db_data['paymentduedate'] = date("Y-m-d",strtotime($date_due));
        $group_discount = $db_data['groupdiscount'] = trim($_POST['group_discount']);
        $policy_discount = $db_data['policydiscount'] = trim($_POST['policy_discount']);
        $policy_fee = $db_data['fee'] = trim($_POST['policy_fee']);
        $doctor = $db_data['iddoctor'] = trim($_POST['doctor']);
        $date_received = trim($_POST['date_received']);
        if($date_received)
        $db_data['datereceived'] = date("Y-m-d",strtotime($date_received));
        $date_approved = trim($_POST['date_approved']);
        if($date_approved)
        $db_data['dateapproved'] = date("Y-m-d",strtotime($date_approved));
        
        $dominicana = $db_data['dominicana'] = trim($_POST['dominicana'])? 1: 0;
        $approved_standard = $db_data['approvedstandrad'] = trim($_POST['approved_standard'])? 1: 0;
        $death_main_insured = $db_data['deathmaininsured'] = trim($_POST['death_main_insured'])? 1: 0;
        $is_spanish = $db_data['spanish'] = trim($_POST['is_spanish'])? 1: 0;
        $claria_express = $db_data['clariaexpress'] = trim($_POST['claria_express'])? 1: 0;
        $add_percent = $db_data['add_25_percent'] = trim($_POST['add_percent'])? 1: 0;
        $premium_zone = $db_data['premium_zone'] = trim($_POST['premium_zone']);
        
        $policy_form_edit = trim($_POST['policy_form_edit']);
        
        
        
        $notes = $_POST['notes'];
        if($notes)
        $notes = array_reverse($notes);
        
        //echo json_encode($_FILES);
        //break;
        
        
        $notesids = $_POST['notesids'];
        if($notesids)
        $notesids = array_reverse($notesids);
        
         
        if($_POST['agent_level5']>0){
          $agent_id5 = $_POST['agent_level5']; 
        }
        if($_POST['agent_level4']>0){
           $agent_id4 = $_POST['agent_level4'];
        }
        if($_POST['agent_level3']>0){
            $agent_id3 = $_POST['agent_level3'];
        }
        if($_POST['agent_level2']>0){
          $agent_id2 = $_POST['agent_level2']; 
        }
        if($_POST['agent_level1']>0){
           $agent_id = $_POST['agent_level1']; 
        }
        	
        
        $db_data['idagent'] = $agent_id;
        $db_data['idagent2'] = $agent_id2;
        $db_data['idagent3'] = $agent_id3;
        $db_data['idagent4'] = $agent_id4;
        $db_data['idagent5'] = $agent_id5;
        $db_data['policytype'] = "health"; 
        $db_data['last_update'] = time(); 
        
        
        
        
        if($policy_id){
            $policyOldData = getSinglePolicy($policy_id);
            if(!$policyOldData){
                $db_data['effectivedate_first'] = date("Y-m-d",strtotime($effective_date));
                $policy_id = createNewPolicy();
                $policyOldData = getSinglePolicy($policy_id);
            }
            $db_data['active'] = 1;
            $updStats = saveHealthPolicy($policy_id,$db_data); 
        }
  
        
        if($updStats){  
        addAuditsPolicyFormData($policyOldData,$db_data);
        $data_sucess['sucess'] = 1;
        if($notes){
          addPolicyNotes($policy_id,$notes,$notesids);  
        }
        }else{
           $data_sucess['sucess'] = 0; 
           //$data_sucess['message'] = $coverageID; 
           
        }
        
        echo json_encode($data_sucess);
        break; 
        
        
        case 'duplicate_policy':
        
        $checkPermission = checkUserAccessRole('Policies');
        if(!$checkPermission){
            $data_sucess = array("sucess"=>0,"pr"=>1,"message"=>"Permission error");
            echo json_encode($data_sucess);
            break;
        }
        
        $policy_id = trim($_POST['policy_num']);
        $new_policy_numer = trim($_POST['policy_new_number']);
        
        if($policy_id && $new_policy_numer){
         $new_policy_id = duplicateHealthPolicy($policy_id,$new_policy_numer); 
         if($new_policy_id){
          $data_sucess['sucess'] = 1;
          $data_sucess['new_policy_number'] = $new_policy_id;
             
         }
        }else{
           $data_sucess['sucess'] = 0; 
           //$data_sucess['message'] = $coverageID; 
           
        }
        echo json_encode($data_sucess);
        break; 
        
        ## Payment Codes 
        case 'save_agent_notes':
          $checkPermission = checkUserAccessRole('Policies');
          if(!$checkPermission){
            $data_sucess = array("sucess"=>0,"pr"=>1,"message"=>"Permission error");
            echo json_encode($data_sucess);
            break;
          }
          $policy_id =trim($_POST['policy_id']);
          $note_1 = trim($_POST['note_1']);
          $note_2 = trim($_POST['note_2']);
          $note_3 = trim($_POST['note_3']);
          $note_4 = trim($_POST['note_4']);
          $note_5 = trim($_POST['note_5']);
          $created_date = date("Y-m-d");
          $updated_date = date("Y-m-d");

          if($policy_id){
            $new_agent_notes = createNewAgentNotes($policy_id,$note_1,$note_2,$note_3,$note_4,$note_5,$created_date,$updated_date);
            if($new_agent_notes){
            $data_sucess['sucess'] = 1;
          }
        }else{
         $data_sucess['sucess'] = 0; 
       }
       echo json_encode($data_sucess);

        break;
        //for update payments
        case 'update_payment':
            $checkPermission = checkUserAccessRole('Policies');
            if(!$checkPermission){
                $data_sucess = array("sucess"=>0,"pr"=>1,"message"=>"Permission error");
                echo json_encode($data_sucess);
                break;
            }
            
            $p_id = trim($_POST['policy_id']);
            $payments_id = trim($_POST['payment_id']);
            $pay_mode = trim($_POST['value']);
            
            $update_payments = update_payment_table_paymode($payments_id,$pay_mode);
            if($update_payments){
                $data_sucess['sucess'] = 1;
            }else{
                $data_sucess['sucess'] = 0;
            }
            echo json_encode($data_sucess);
            
        break;
        
        //for save payments
         case 'save_payments':

           $checkPermission = checkUserAccessRole('Policies');
           if(!$checkPermission){
            $data_sucess = array("sucess"=>0,"pr"=>1,"message"=>"Permission error");
            echo json_encode($data_sucess);
            break;
          }

          $policy_id = trim($_POST['policy_id']);
          $payments_id = $db_data['id'] = trim($_POST['payments_id']);

          $p_id = $db_data['id_policy'] = trim($_POST['policy_id']);
          $receipt_pay = $db_data['receipt_pay'] = trim($_POST['receipt_pay']);
          $receipt_type = $db_data['receipt_type'] = trim($_POST['receipt_type']);
          $receipt_note = $db_data['receipt_note'] = trim($_POST['receipt_note']);
          $paymenType = $db_data['type'] = trim($_POST['paymenType']);
          $id_pay_cycle = $db_data['id_pay_cycle'] = trim($_POST['id_pay_cycle']);
          $paymentamount = $db_data['amount'] = trim($_POST['paymentamount']);
          $paymentpolicyfee = $db_data['fee'] = trim($_POST['paymentpolicyfee']);
          $paymentdetails = $db_data['details'] = trim($_POST['paymentdetails']);

          $discount_agent_1 = $db_data['agent_1_discount'] = trim($_POST['discount_agent_1']);
          $discount_agent_2 = $db_data['agent_2_discount'] = trim($_POST['discount_agent_2']);
          $discount_agent_3 = $db_data['agent_3_discount'] = trim($_POST['discount_agent_3']);
          $discount_agent_4 = $db_data['agent_4_discount'] = trim($_POST['discount_agent_4']);
          $discount_agent_5 = $db_data['agent_5_discount'] = trim($_POST['discount_agent_5']);

          $paymentMethod = $db_data['id_pay_type'] = trim($_POST['paymentMethod']);
          $id_user = $db_data['id_user'] = trim($_POST['payment_user_id']);


          if(isset($_POST['locked'])){
            $locked = $db_data['locked'] = 1;
          }else{
            $locked = $db_data['locked'] = 0;
          }
          //$locked = $db_data['locked'] = trim($_POST['locked']);
          //$paidcheck = $db_data['paid'] = trim($_POST['paidcheck']);
          if(isset($_POST['paid'])){
            $paidcheck = $db_data['paid'] = 1;
          }else{
            $paidcheck = $db_data['paid'] = 0;
          }
          
          $paymentpaid = trim($_POST['paymentpaid']);
          if($paymentpaid)
            $db_data['date_paid']  = date("Y-m-d",strtotime($paymentpaid));
          $paymentduedate = trim($_POST['paymentduedate']);
          if($paymentduedate)
            $db_data['date_due']  = date("Y-m-d",strtotime($paymentduedate));
          //fro agent note for each policy
          $notes = trim($_POST['notes']);
          $notesids = trim($_POST['notesids']);
          $db_data['payment_inserted_by']  = 'user';
          
          
          
          $paymentAction = $db_data['action'] = trim($_POST['paymentAction']);
          $now = date("Y-m-d H:i:s");
          $db_data['date_created'] = $now;
          
          
          $agent_commission_one = $db_data['com_agent1'] = trim($_POST['agent_commission_one']);
          $agent_commission_two = $db_data['com_agent2'] = trim($_POST['agent_commission_two']);
          $agent_commission_three = $db_data['com_agent3'] = trim($_POST['agent_commission_three']);
          $agent_commission_four = $db_data['com_agent4'] = trim($_POST['agent_commission_four']);
          $agent_commission_five = $db_data['com_agent5'] = trim($_POST['agent_commission_five']);
          
          if($policy_id){  
            if($paymentAction=='Recieved'){
            
            // echo 'yes';
            
              $NonComAm =0;
              $NonComPer =0;
              $nonPayable =0;
              $payable =0;
              $agent_1_discount = 0;
              $agent_2_discount = 0;
              $agent_3_discount = 0;
              $agent_4_discount = 0;
              $RateUpper=0;

              $PremiumsHealth = getPremiumsHealth($policy_id);

              $sumofpre = $PremiumsHealth['sumofpre'];
              $sumofbasepre = $PremiumsHealth['sumofbasepre'];

              $NonComAm = $sumofpre-$sumofbasepre;
              $NonComPer = ($NonComAm)/($sumofpre/100);

              $RateUpper = round($NonComPer,2);
      

             $payments_detail = getSinglePolicy($policy_id);

             
              $policy_number=$payments_detail['policynumber'];
              $pay_cycle= $id_pay_cycle;
              $policy_fee = $paymentpolicyfee;
              $policy_amount = $paymentamount;
              $pay_form =$paymentMethod;
              $date_due =$paymentduedate;
              $date_paid=$paymentpaid;
              $type =$paymenType;
              $agent_1_discount = $discount_agent_1;
              $agent_2_discount = $discount_agent_2;
              $agent_3_discount = $discount_agent_3;
              $agent_4_discount = $discount_agent_4;
              $agent_5_discount = $discount_agent_5;
              $effectivedate = $payments_detail['effectivedate'];
              $details =$paymentdetails;
              $city = $payments_detail['city'];
              $carrier =  $payments_detail['carrier'];
             

              $PaymenMinusFee=round($policy_amount - $policy_fee,2);
              $PayableMinusFee = round($PaymenMinusFee*($NonComPer/100),2);

              $idagent1 = $payments_detail['idagent']; 
              $idagent2 = $payments_detail['idagent2']; 
              $idagent3 = $payments_detail['idagent3']; 
              $idagent4 = $payments_detail['idagent4']; 
              $idagent5 = $payments_detail['idagent5']; 
              $now = date("Y-m-d H:i:s");
             // echo $idagent5 = $payments_detail['idagent5']; 
              

                if($idagent1){
                    $agent_data = getAgentCommissionByPolicyid($policy_id,$idagent1);
                    $agent_details = getAgentdetbyID($idagent1);

                    //$comission = $agent_data['commission'];
                    $comission = $agent_commission_one;

                    $pay_by = $agent_details['pay_by'];
                    $pay_to = $agent_details['payto'];
                    $level = $agent_details['level'];
                    
                    //$first_name = $agent_details['name'];
                    //$last_name = $agent_details['lastname'];
                   // $main_insured =$first_name.' '.$last_name;
                    $agent_code =$agent_details['number'];
                    $get_last_id_of_payment =get_last_id_payments();
                    $last_id = $get_last_id_of_payment['id'];
                    $payments_id = $last_id+1;
                    $transactionID = $payments_id.'A';
                    $agent_id =$agent_details['id']; 
                    $country_id = $agent_details['idcountry'];
                    $country=get_country_nameby_id($country_id);
                    $country_name = $country['country'];
                    $agent_city=$country_name; 
                    
                    $main_insured_data = getmaininsured($policy_id);
                    $main_insured_fname = $main_insured_data['first_name'];
                    $main_insured_lname = $main_insured_data['last_name'];
                    $main_insured = $main_insured_fname.' '.$main_insured_lname;

                    if($agent_1_discount>0){

                        $nonPayable= round((($policy_amount-$policy_fee)*($NonComPer/100))+$policy_fee,2);
                        $payable =round($policy_amount-$nonPayable,2);
                        $com_posted = (($comission/100)*$payable)-$agent_1_discount;
                        $com_posted = round($com_posted,2);
                        $discount = $agent_1_discount;
                                      
                    }
                    else {
                        $agent_1_discount = 0;

                     if($pay_cycle==1){
                          $nonPayable = round($NonComAm+$policy_fee,2);
                          $payable =   round($policy_amount-$nonPayable,2);
                          $com_posted = round((($comission/100)*$payable)-$agent_1_discount,2);
                          $discount = $agent_1_discount;                       
                      }
                      else if($pay_cycle==2){

                          $nonPayable = round(($NonComAm*0.55)+$policy_fee,2);
                          $payable =   round($policy_amount-$nonPayable,2);
                          $com_posted = round((($comission/100)*$payable)-$agent_1_discount,2);  
                          $discount = $agent_1_discount;                      
                      }

                     else if($pay_cycle==3){
                          $nonPayable = round(($NonComAm*0.28)+$policy_fee,2);
                          $payable =   round($policy_amount-$nonPayable,2);
                          $com_posted = round((($comission/100)*$payable)-$agent_1_discount,2);
                          $discount = $agent_1_discount;                         
                      }
                      else if($pay_cycle==4){
                          $nonPayable = round(($NonComAm*0.1)+$policy_fee,2);
                          $payable =   round($policy_amount-$nonPayable,2);
                          $com_posted = round((($comission/100)*$payable)-$agent_1_discount,2);
                          $discount = $agent_1_discount;
                      }

                    }

              


                     $formdata = array(
                                    'policy_id'=>$policy_id,
                                    'agent_id'=> $agent_id,
                                    'level_id'=> $level,
                                    'commission'=> $comission,
                                    'pay_by' => $pay_by,
                                    'payment_details' => $paymentdetails,
                                    'transactionID'=> $transactionID,
                                    'policy_number'=> $policy_number,
                                    'main_insured'=> $main_insured,
                                    'pay_mode'=> $pay_cycle,
                                    'effectivedate'=> $effectivedate,
                                    'payment_amount'=> $policy_amount,
                                    'rate_up'=> $RateUpper,
                                    'nonPayable'=> $nonPayable,
                                    'payable_amount'=> $payable,
                                    'pay_form'=> $pay_form,
                                    'date_due'=> $date_due,
                                    'date_paid'=> $date_paid,
                                    'agent_code'=> $agent_code,
                                    'agent_city'=> $agent_city,
                                    'payments_type'=> $type,
                                    'com_posted'=> $com_posted,
                                    'discount'=> $discount,
                                    'PaymenMinusFee'=> $PaymenMinusFee,
                                    'PayableMinusFee'=> $PayableMinusFee,
                                    'pay_to' =>$pay_to,
                                    'Date_Created'=> $now,
                                    'XC4' =>$now,
                                    'carrier'=> $carrier,
									'approved' => 0,
									'void'=> 0,
                                    
                                  );
                                    
                                  // print_r($formdata);
                                   $insert=insert_table_data($formdata);
                                  

                  }

                  if($idagent2){

                    $agent_data2 = getAgentCommissionByPolicyid($policy_id,$idagent2);
                    //$comission = $agent_data2['commission'];
                    $comission = $agent_commission_two;
                    $agent_details2 = getAgentdetbyID($idagent2);
                    $pay_by = $agent_details2['pay_by'];
                    $pay_to = $agent_details2['payto'];
                    $level = $agent_details2['level'];
                    
                    $main_insured_data = getmaininsured($policy_id);
                    $main_insured_fname = $main_insured_data['first_name'];
                    $main_insured_lname = $main_insured_data['last_name'];
                    $main_insured = $main_insured_fname.' '.$main_insured_lname;
                    
                    $agent_code =$agent_details2['number'];
                    $get_last_id_of_payment =get_last_id_payments();
                    $last_id = $get_last_id_of_payment['id'];
                    $payments_id = $last_id+1;
                    $transactionID = $payments_id.'B';
                    $agent_id =$agent_details2['id']; 
                    $country_id = $agent_details['idcountry'];
                    $country=get_country_nameby_id($country_id);
                    $country_name = $country['country'];
                    $agent_city=$country_name;

                    if($agent_2_discount>0){
                        $com_posted = (($comission/100)*$payable)-$agent_2_discount;
                        $com_posted = round($com_posted,2);
                        $discount = $agent_2_discount;
                    }else{
                        $agent_2_discount = 0;

                     if($pay_cycle==1){ 

                          $com_posted = round((($comission/100)*$payable)-$agent_2_discount,2);  
                          $discount = $agent_2_discount;                    
                      }
                      else if($pay_cycle==2){ 
                          $com_posted = round((($comission/100)*$payable)-$agent_2_discount,2);  
                          $discount = $agent_2_discount;
                      }
                      else if($pay_cycle==3){
                         $com_posted = round((($comission/100)*$payable)-$agent_2_discount,2);   
                         $discount = $agent_2_discount;                       
                      }
                      else if($pay_cycle==4){
                        $com_posted = round((($comission/100)*$payable)-$agent_2_discount,2);  
                        $discount = $agent_2_discount;
                      }
                    }

                     $formdata = array(

                                    'policy_id'=>$policy_id,
                                    'agent_id'=> $agent_id,
                                    'level_id'=> $level,
                                    'commission'=> $comission,
                                    'pay_by' => $pay_by,
                                    'payment_details' => $paymentdetails,
                                    'transactionID'=> $transactionID,
                                    'policy_number'=> $policy_number,
                                    'main_insured'=> $main_insured,
                                    'pay_mode'=> $pay_cycle,
                                    'effectivedate'=> $effectivedate,
                                    'payment_amount'=> $policy_amount,
                                    'rate_up'=> $RateUpper,
                                    'nonPayable'=> $nonPayable,
                                    'payable_amount'=> $payable,
                                    'pay_form'=> $pay_form,
                                    'date_due'=> $date_due,
                                    'date_paid'=> $date_paid,
                                    'agent_code'=> $agent_code,
                                    'agent_city'=> $agent_city,
                                    'payments_type'=> $type,
                                    'com_posted'=> $com_posted,
                                    'discount'=> $discount,
                                    'PaymenMinusFee'=> $PaymenMinusFee,
                                    'PayableMinusFee'=> $PayableMinusFee,
                                    'pay_to' =>$pay_to,
                                    'Date_Created'=> $now,
                                    'XC4' =>$now,
                                    'carrier'=> $carrier,
									'approved' => 0,
									'void'=> 0,
                                  );
                                    
                                   //print_r($formdata);
                                  $insert=insert_table_data($formdata);
                                 
                                   
                  }

                  if($idagent3){

                    $agent_data3 = getAgentCommissionByPolicyid($policy_id,$idagent3);
                    //$comission = $agent_data3['commission'];
                    $comission = $agent_commission_three;
                    $agent_details3 = getAgentdetbyID($idagent3);
                    $pay_by = $agent_details3['pay_by'];
                    $pay_to = $agent_details3['payto'];
                    $level = $agent_details3['level'];
                    $main_insured_data = getmaininsured($policy_id);
                    $main_insured_fname = $main_insured_data['first_name'];
                    $main_insured_lname = $main_insured_data['last_name'];
                    $main_insured = $main_insured_fname.' '.$main_insured_lname;
                    $agent_code =$agent_details3['number'];
                    $get_last_id_of_payment =get_last_id_payments();
                    $last_id = $get_last_id_of_payment['id'];
                    $payments_id = $last_id+1;
                    $transactionID = $payments_id.'C'; 
                    $agent_id =$agent_details3['id'];
                    $country_id = $agent_details['idcountry'];
                    $country=get_country_nameby_id($country_id);
                    $country_name = $country['country'];
                    $agent_city=$country_name;

                    if($agent_3_discount>0){
                        $com_posted = (($comission/100)*$payable)-$agent_3_discount;
                        $com_posted = round($com_posted,2); 
                        $discount = $agent_3_discount;
                    }else{
                        $agent_3_discount = 0;
                       if($pay_cycle==1){  
                            $com_posted = round((($comission/100)*$payable)-$agent_3_discount,2);     
                            $discount = $agent_3_discount;                   
                        }
                        else if($pay_cycle==2){ 
                            $com_posted = round((($comission/100)*$payable)-$agent_3_discount,2);  
                            $discount = $agent_3_discount;
                        }
                       else if($pay_cycle==3){
                          $com_posted = round((($comission/100)*$payable)-$agent_3_discount,2);  
                           $discount = $agent_3_discount;                        
                        }
                      else if($pay_cycle==4){
                          $com_posted = round((($comission/100)*$payable)-$agent_3_discount,2);  
                          $discount = $agent_3_discount;
                        }
                      }

                      $formdata = array(

                                    'policy_id'=>$policy_id,
                                    'agent_id'=> $agent_id,
                                    'level_id'=> $level,
                                    'commission'=> $comission,
                                    'pay_by' => $pay_by,
                                    'payment_details' => $paymentdetails,
                                    'transactionID'=> $transactionID,
                                    'policy_number'=> $policy_number,
                                    'main_insured'=> $main_insured,
                                    'pay_mode'=> $pay_cycle,
                                    'effectivedate'=> $effectivedate,
                                    'payment_amount'=> $policy_amount,
                                    'rate_up'=> $RateUpper,
                                    'nonPayable'=> $nonPayable,
                                    'payable_amount'=> $payable,
                                    'pay_form'=> $pay_form,
                                    'date_due'=> $date_due,
                                    'date_paid'=> $date_paid,
                                    'agent_code'=> $agent_code,
                                    'agent_city'=> $agent_city,
                                    'payments_type'=> $type,
                                    'com_posted'=> $com_posted,
                                    'discount'=> $discount,
                                    'PaymenMinusFee'=> $PaymenMinusFee,
                                    'PayableMinusFee'=> $PayableMinusFee,
                                    'pay_to' =>$pay_to,
                                    'Date_Created'=> $now,
                                    'XC4' =>$now,
                                    'carrier'=> $carrier,
									'approved' => 0,
									'void'=> 0,
                                  );
                                    
                                   //print_r($formdata);
                                   $insert=insert_table_data($formdata);
                                   
                                   
                    }
                    if($idagent4){

                      $agent_data4 = getAgentCommissionByPolicyid($policy_id,$idagent4);
                      //$comission = $agent_data4['commission'];
                      $comission = $agent_commission_four;
                      $agent_details4 = getAgentdetbyID($idagent4);
                      $pay_by = $agent_details4['pay_by'];
                      $pay_to = $agent_details4['payto'];
                      $level = $agent_details4['level'];
                      $main_insured_data = getmaininsured($policy_id);
                      $main_insured_fname = $main_insured_data['first_name'];
                      $main_insured_lname = $main_insured_data['last_name'];
                      $main_insured = $main_insured_fname.' '.$main_insured_lname;
                      $agent_code =$agent_details4['number'];
                      $get_last_id_of_payment =get_last_id_payments();
                      $last_id = $get_last_id_of_payment['id'];
                      $payments_id = $last_id+1;
                      $transactionID = $payments_id.'D'; 
                      $agent_id =$agent_details4['id']; 
                      $country_id = $agent_details['idcountry'];
                      $country=get_country_nameby_id($country_id);
                      $country_name = $country['country'];
                      $agent_city=$country_name;

                      if($agent_4_discount>0){
                          $com_posted = (($comission/100)*$payable)-$agent_4_discount;
                          $com_posted = round($com_posted,2);  ;
                          $discount = $agent_4_discount;
                      }else{

                        $agent_4_discount = 0;
                         if($pay_cycle==1){  
                              $com_posted = round((($comission/100)*$payable)-$agent_4_discount,2);  
                              $discount = $agent_4_discount;                      
                          }
                          else if($pay_cycle==2){ 
                              $com_posted = round((($comission/100)*$payable)-$agent_4_discount,2);  
                              $discount = $agent_4_discount;
                          }
                         else if($pay_cycle==3){
                             $com_posted = round((($comission/100)*$payable)-$agent_4_discount,2);   
                             $discount = $agent_4_discount;                       
                          }
                        else if($pay_cycle==4){
                            $com_posted = round((($comission/100)*$payable)-$agent_4_discount,2);
                            $discount = $agent_4_discount;
                          }
                      }

                      $formdata = array(

                                    'policy_id'=>$policy_id,
                                    'agent_id'=> $agent_id,
                                    'level_id'=> $level,
                                    'commission'=> $comission,
                                    'pay_by' => $pay_by,
                                    'payment_details' => $paymentdetails,
                                    'transactionID'=> $transactionID,
                                    'policy_number'=> $policy_number,
                                    'main_insured'=> $main_insured,
                                    'pay_mode'=> $pay_cycle,
                                    'effectivedate'=> $effectivedate,
                                    'payment_amount'=> $policy_amount,
                                    'rate_up'=> $RateUpper,
                                    'nonPayable'=> $nonPayable,
                                    'payable_amount'=> $payable,
                                    'pay_form'=> $pay_form,
                                    'date_due'=> $date_due,
                                    'date_paid'=> $date_paid,
                                    'agent_code'=> $agent_code,
                                    'agent_city'=> $agent_city,
                                    'payments_type'=> $type,
                                    'com_posted'=> $com_posted,
                                    'discount'=> $discount,
                                    'PaymenMinusFee'=> $PaymenMinusFee,
                                    'PayableMinusFee'=> $PayableMinusFee,
                                    'pay_to' =>$pay_to,
                                    'Date_Created'=> $now,
                                    'XC4' =>$now,
                                    'carrier'=> $carrier,
									'approved' => 0,
									'void'=> 0,
                                  );
                                   
                                   //print_r($formdata);
                                  $insert=insert_table_data($formdata);                   

                    }
                    if($idagent5){

                      $agent_data5 = getAgentCommissionByPolicyid($policy_id,$idagent5);
                      //$comission = $agent_data4['commission'];
                      $comission = $agent_commission_five;
                      $agent_details5 = getAgentdetbyID($idagent5);
                      $pay_by = $agent_details5['pay_by'];
                      $pay_to = $agent_details5['payto'];
                      $level = $agent_details5['level'];
                      $main_insured_data = getmaininsured($policy_id);
                      $main_insured_fname = $main_insured_data['first_name'];
                      $main_insured_lname = $main_insured_data['last_name'];
                      $main_insured = $main_insured_fname.' '.$main_insured_lname;
                      $agent_code =$agent_details5['number'];
                      $transactionID = $payment_id.'D'; 
                      $agent_id =$agent_details5['id']; 
                      $country_id = $agent_details['idcountry'];
                      $country=get_country_nameby_id($country_id);
                      $country_name = $country['country'];
                      $agent_city=$country_name;

                      if($agent_5_discount>0){
                          $com_posted = (($comission/100)*$payable)-$agent_5_discount;
                          $com_posted = round($com_posted,2);
                          $discount = $agent_5_discount; 
                      }else{

                        $agent_5_discount = 0;
                         if($pay_cycle==1){  
                              $com_posted = round((($comission/100)*$payable)-$agent_5_discount,2);  
                              $discount = $agent_5_discount;                      
                          }
                          else if($pay_cycle==2){ 
                              $com_posted = round((($comission/100)*$payable)-$agent_5_discount,2);  
                              $discount = $agent_5_discount;
                          }
                         else if($pay_cycle==3){
                             $com_posted = round((($comission/100)*$payable)-$agent_5_discount,2);   
                             $discount = $agent_5_discount;                       
                          }
                        else if($pay_cycle==4){
                            $com_posted = round((($comission/100)*$payable)-$agent_5_discount,2);
                            $discount = $agent_5_discount;
                          }
                      }

                      $formdata = array(

                                    'policy_id'=>$policy_id,
                                    'agent_id'=> $agent_id,
                                    'level_id'=> $level,
                                    'commission'=> $comission,
                                    'pay_by' => $pay_by,
                                    'payment_details' => $details,
                                    'transactionID'=> $transactionID,
                                    'policy_number'=> $policy_number,
                                    'main_insured'=> $main_insured,
                                    'pay_mode'=> $pay_cycle,
                                    'effectivedate'=> $effectivedate,
                                    'payment_amount'=> $policy_amount,
                                    'rate_up'=> $RateUpper,
                                    'nonPayable'=> $nonPayable,
                                    'payable_amount'=> $payable,
                                    'pay_form'=> $pay_form,
                                    'date_due'=> $date_due,
                                    'date_paid'=> $date_paid,
                                    'agent_code'=> $agent_code,
                                    'agent_city'=> $agent_city,
                                    'payments_type'=> $type,
                                    'com_posted'=> $com_posted,
                                    'discount'=> $discount,
                                    'PaymenMinusFee'=> $PaymenMinusFee,
                                    'PayableMinusFee'=> $PayableMinusFee,
                                    'pay_to' =>$pay_to,
                                    'Date_Created'=> $now,
                                    'XC4' =>$now,
                                    'carrier'=> $carrier,
									'approved' => 0,
                                    'void'=> 0,
                                  );
                                   
                                   //print_r($formdata);
                                  $insert=insert_table_data($formdata);                   

                    }
                            
                             
          
          }
          
         
          
          //endfor agent note for each policy
          
              if ($paymenType !="" && $id_pay_cycle !="" && $paymentpaid !="" ) { 
                $new_payments = createNewPayments($db_data);
                if($new_payments){
                  addAuditsCreatePayment($policy_id,$paymenType);
                   if($insert){
                        $data_sucess['sucess'] = 2;
                   }else{
                    
                    if ($paymentduedate !="") {
                        $db_upstatus_data['paymentduedate'] = date("Y-m-d",strtotime($paymentduedate));
                        $updStats = saveHealthPolicy($policy_id,$db_upstatus_data);
                        if($updStats){
                           $data_sucess['sucess'] = 1; 
                        }
                        
                    }
                    $data_sucess['sucess'] = 1;
                   }
                  
                }
              } //end payment update
            // start policy status due date and notes update
            if ($_POST['policy_status'] !="") {
                
                $db_upstatus_data['idstatus'] = trim($_POST['policy_status']);
                $updStats = saveHealthPolicy($policy_id,$db_upstatus_data);
                
                if($updStats){      
                  addAuditsChangeStatus($policy_id,$db_upstatus_data['idstatus']);
                  $data_sucess['sucess'] = 1;
                }
                
            }
            


            if($notes){
                $sql='INSERT INTO notespolicy SET note="'.$notes.'", idpolicy="'.$policy_id.'"'; 
                $note_id = $db->insert($sql); 

                if($note_id){
                      addAudit(array("uid"=>$user_id,"idpolicy"=>$policy_id,"action"=>$user_name." added a note"));
                      $data_sucess['sucess'] = 1;
                } 
                
            } //end policy status due date and notes update

            if ($receipt_pay && $receipt_type && $receipt_note) {

                $p_id = $r_db_data['id_policy'] = trim($_POST['policy_id']);
                $receipt_pay = $r_db_data['receipt_pay'] = trim($_POST['receipt_pay']);
                $receipt_type = $r_db_data['receipt_type'] = trim($_POST['receipt_type']);
                $receipt_note = $r_db_data['receipt_note'] = trim($_POST['receipt_note']);

                $sql="SELECT * FROM `policy_info_receipt` WHERE `id_policy`='$policy_id'";
                $checkdata=$db->select_single($sql);

                if ($checkdata !=null){
                    $sql = 'UPDATE policy_info_receipt SET ';
                }
                else {
                    $sql = 'INSERT INTO policy_info_receipt SET ';
                }
                foreach($r_db_data as $key => $value){

                   $sql .=  $key.'="'.$value.'",';
               }
               $sql = rtrim($sql,",");

               if ($checkdata !=null){
                $sql .= 'WHERE  id_policy="'.$policy_id.'" ';
            }
            if ($checkdata !=null){
                $stats = $db->edit($sql);
                if($stats){
                  addAudit(array("uid"=>$user_id,"idpolicy"=>$policy_id,"action"=>$user_name." edited info receipt "));
                  $data_sucess['sucess'] = 1;
                }
            }
            else{

                $checkdata = $db->insert($sql);
                if($checkdata){
                  addAudit(array("uid"=>$user_id,"idpolicy"=>$policy_id,"action"=>$user_name." inserted info receipt "));
                  $data_sucess['sucess'] = 1;
                }
            } 
             
        }
       
        }else{
           $data_sucess['sucess'] = 0; 
         }

         echo json_encode($data_sucess);
        
        
         break;
        //end notes add end
         case 'save_agent_label':

        $checkPermission = checkUserAccessRole('Policies');
            if(!$checkPermission){
                $data_sucess = array("sucess"=>0,"pr"=>1,"message"=>"Permission error");
                echo json_encode($data_sucess);
                break;
            }

            $policy_id = trim($_POST['policy_id']);
            $data_id = trim($_POST['data_id']);
            $agent_id = trim($_POST['agent_id']);
            $notes = trim($_POST['notes']);
            $parent_data_id = trim($_POST['parent_data_id']);
                
            $db_data['agent_id'] =  $agent_id;
            $db_data['policy_id'] = $policy_id;
            $db_data['level_id'] = $data_id;
            $db_data['commission'] = trim($_POST['agent_level'.$data_id.'_commission']);
            $db_data['sys_nb'] = trim($_POST['agent_level'.$data_id.'_sys_nb']);
            $db_data['nb'] = trim($_POST['agent_level'.$data_id.'_nb']);
            $db_data['sys_rn'] = trim($_POST['agent_level'.$data_id.'_sys_rn']);
            $db_data['rn'] = trim($_POST['agent_level'.$data_id.'_rn']);
            $db_data['pay_by'] = trim($_POST['agent_level'.$data_id.'_pay_by']);
            $db_data['notes'] = $notes;

            $get_level = getAgentdetbyID($agent_id);
            $level_id = $get_level['level'];

            if($policy_id && $agent_id){

                $new_payments = createNewAgentLabel($db_data,$agent_id,$policy_id,$data_id); 

                if($new_payments){
                   if(!$parent_data_id) {
                      addAudit(array("uid"=>$user_id,"idpolicy"=>$policy_id,"action"=>$user_name." updated notes for level ".$level_id));
                   }
                
                  if($data_id==1){
                    $data_id='';
                  }
                  $dbp_data['idagent'.$data_id]=$agent_id;
                  saveHealthPolicy($policy_id,$dbp_data); 
                 
                  
                  $data_sucess['sucess'] = 1;
                }

          }else{

            $data_sucess['sucess'] = 0; 
         }
         echo json_encode($data_sucess);
         break;
        //end agent commission 

        //rafia////
          case 'create_commission':

          $payment_id = trim($_POST['payment_id']);
          $policy_id = trim($_POST['policy_id']);
          $value = trim($_POST['value']);
          $agent_commission_one = trim($_POST['agent_commission_one']);
          $agent_commission_two = trim($_POST['agent_commission_two']);
          $agent_commission_three = trim($_POST['agent_commission_three']);
          $agent_commission_four = trim($_POST['agent_commission_four']);
          $agent_commission_five = trim($_POST['agent_commission_five']);
         // echo  $policy_id;
          if($value=='Recieved')
          {

              $NonComAm =0;
              $NonComPer =0;
              $nonPayable =0;
              $payable =0;
              $agent_1_discount = 0;
              $agent_2_discount = 0;
              $agent_3_discount = 0;
              $agent_4_discount = 0;
              $RateUpper=0;

              $PremiumsHealth =getPremiumsHealth($policy_id);

              $sumofpre = $PremiumsHealth['sumofpre'];
              $sumofbasepre = $PremiumsHealth['sumofbasepre'];

              $NonComAm = $sumofpre-$sumofbasepre;
              $NonComPer = ($NonComAm)/($sumofpre/100);

              $RateUpper = round($NonComPer,2);
      

             $payments_detail = getPolicynpaymentsdata($policy_id,$payment_id);

              $paid = $payments_detail['paid']; 
              $policy_number=$payments_detail['policynumber'];
              $pay_cycle=$payments_detail['idpaycycle'];
              $policy_fee = $payments_detail['fee'];
              $policy_amount =$payments_detail['amount'];
              $pay_form =$payments_detail['id_pay_type'];
              $date_due =$payments_detail['date_due'];
              $date_paid=$payments_detail['date_paid'];
              $type =$payments_detail['type'];
              $agent_1_discount = $payments_detail['agent_1_discount'];
              $agent_2_discount = $payments_detail['agent_2_discount'];
              $agent_3_discount = $payments_detail['agent_3_discount'];
              $agent_4_discount = $payments_detail['agent_4_discount'];
              $agent_5_discount = $payments_detail['agent_5_discount'];
              $effectivedate = $payments_detail['effectivedate'];
              $details =$payments_detail['details'];
              $city = $payments_detail['city'];
              $carrier =  $payments_detail['carrier'];

              $PaymenMinusFee=round($policy_amount - $policy_fee,2);
              $PayableMinusFee = round($PaymenMinusFee*($NonComPer/100),2);

              $idagent1 = $payments_detail['idagent']; 
              $idagent2 = $payments_detail['idagent2']; 
              $idagent3 = $payments_detail['idagent3']; 
              $idagent4 = $payments_detail['idagent4']; 
              $idagent5 = $payments_detail['idagent5'];
              $now = date("Y-m-d H:i:s");
             // echo $idagent5 = $payments_detail['idagent5']; 
              if($paid==1)
              {
                $data_sucess['message'] = 'The commission already exists!';        
              }
              else
              {

                if($idagent1){
                    $agent_data = getAgentCommissionByPolicyid($policy_id,$idagent1);
                    $agent_details = getAgentdetbyID($idagent1);

                    //$comission = $agent_data['commission'];
                    $comission = $agent_commission_one;

                    $pay_by = $agent_details['pay_by'];
                    $pay_to = $agent_details['payto'];
                    $level = $agent_details['level'];
                    
                    //$first_name = $agent_details['name'];
                    //$last_name = $agent_details['lastname'];
                   // $main_insured =$first_name.' '.$last_name;
                    $agent_code =$agent_details['number'];
                    $transactionID = $payment_id.'A';
                    $agent_id =$agent_details['id']; 
                    $country_id = $agent_details['idcountry'];
                    $country=get_country_nameby_id($country_id);
                    $country_name = $country['country'];
                    $agent_city=$country_name; 
                    
                    $main_insured_data = getmaininsured($policy_id);
                    $main_insured_fname = $main_insured_data['first_name'];
                    $main_insured_lname = $main_insured_data['last_name'];
                    $main_insured = $main_insured_fname.' '.$main_insured_lname;

                    if($agent_1_discount>0){

                        $nonPayable= round((($policy_amount-$policy_fee)*($NonComPer/100))+$policy_fee,2);
                        $payable =round($policy_amount-$nonPayable,2);
                        $com_posted = (($comission/100)*$payable)-$agent_1_discount;
                        $com_posted = round($com_posted,2);
                        $discount = $agent_1_discount;
                                      
                    }
                    else {
                     $agent_1_discount = 0;
                     if($pay_cycle==1){
                          $nonPayable = round($NonComAm+$policy_fee,2);
                          $payable =   round($policy_amount-$nonPayable,2);
                          $com_posted = round((($comission/100)*$payable)-$agent_1_discount,2);
                          $discount = $agent_1_discount;                       
                      }
                      else if($pay_cycle==2){

                          $nonPayable = round(($NonComAm*0.55)+$policy_fee,2);
                          $payable =   round($policy_amount-$nonPayable,2);
                          $com_posted = round((($comission/100)*$payable)-$agent_1_discount,2);  
                          $discount = $agent_1_discount;                      
                      }

                     else if($pay_cycle==3){
                          $nonPayable = round(($NonComAm*0.28)+$policy_fee,2);
                          $payable =   round($policy_amount-$nonPayable,2);
                          $com_posted = round((($comission/100)*$payable)-$agent_1_discount,2);
                          $discount = $agent_1_discount;                         
                      }
                      else if($pay_cycle==4){
                          $nonPayable = round(($NonComAm*0.1)+$policy_fee,2);
                          $payable =   round($policy_amount-$nonPayable,2);
                          $com_posted = round((($comission/100)*$payable)-$agent_1_discount,2);
                          $discount = $agent_1_discount;
                      }

                    }

              


                     $formdata = array(
                                    'policy_id'=>$policy_id,
                                    'agent_id'=> $agent_id,
                                    'level_id'=> $level,
                                    'commission'=> $comission,
                                    'pay_by' => $pay_by,
                                    'payment_details' => $details,
                                    'transactionID'=> $transactionID,
                                    'policy_number'=> $policy_number,
                                    'main_insured'=> $main_insured,
                                    'pay_mode'=> $pay_cycle,
                                    'effectivedate'=> $effectivedate,
                                    'payment_amount'=> $policy_amount,
                                    'rate_up'=> $RateUpper,
                                    'nonPayable'=> $nonPayable,
                                    'payable_amount'=> $payable,
                                    'pay_form'=> $pay_form,
                                    'date_due'=> $date_due,
                                    'date_paid'=> $date_paid,
                                    'agent_code'=> $agent_code,
                                    'agent_city'=> $agent_city,
                                    'payments_type'=> $type,
                                    'com_posted'=> $com_posted,
                                    'discount'=> $discount,
                                    'PaymenMinusFee'=> $PaymenMinusFee,
                                    'PayableMinusFee'=> $PayableMinusFee,
                                    'pay_to' =>$pay_to,
                                    'Date_Created'=> $now,
                                    'XC4' =>$now,
                                    'carrier'=> $carrier,
									'approved' => 0,
                                    'void'=> 0,
                                    
                                  );
                                    
                                  // print_r($formdata);
                                   $insert=insert_table_data($formdata);
                                  

                  }

                  if($idagent2){

                    $agent_data2 = getAgentCommissionByPolicyid($policy_id,$idagent2);
                    //$comission = $agent_data2['commission'];
                    $comission = $agent_commission_two;
                    $agent_details2 = getAgentdetbyID($idagent2);
                    $pay_by = $agent_details2['pay_by'];
                    $pay_to = $agent_details2['payto'];
                    $level = $agent_details2['level'];
                    
                    $main_insured_data = getmaininsured($policy_id);
                    $main_insured_fname = $main_insured_data['first_name'];
                    $main_insured_lname = $main_insured_data['last_name'];
                    $main_insured = $main_insured_fname.' '.$main_insured_lname;
                    
                    $agent_code =$agent_details2['number'];
                    $transactionID = $payment_id.'B';
                    $agent_id =$agent_details2['id']; 
                    $country_id = $agent_details['idcountry'];
                    $country=get_country_nameby_id($country_id);
                    $country_name = $country['country'];
                    $agent_city=$country_name;

                    if($agent_2_discount>0){
                        $com_posted = (($comission/100)*$payable)-$agent_2_discount;
                        $com_posted = round($com_posted,2);
                        $discount = $agent_2_discount;              
                    }else{
                        $agent_2_discount = 0;

                     if($pay_cycle==1){ 

                          $com_posted = round((($comission/100)*$payable)-$agent_2_discount,2);  
                          $discount = $agent_2_discount;                    
                      }
                      else if($pay_cycle==2){ 
                          $com_posted = round((($comission/100)*$payable)-$agent_2_discount,2);  
                          $discount = $agent_2_discount;
                      }
                      else if($pay_cycle==3){
                         $com_posted = round((($comission/100)*$payable)-$agent_2_discount,2);   
                         $discount = $agent_2_discount;                       
                      }
                      else if($pay_cycle==4){
                        $com_posted = round((($comission/100)*$payable)-$agent_2_discount,2);  
                        $discount = $agent_2_discount;
                      }
                    }

                     $formdata = array(

                                    'policy_id'=>$policy_id,
                                    'agent_id'=> $agent_id,
                                    'level_id'=> $level,
                                    'commission'=> $comission,
                                    'pay_by' => $pay_by,
                                    'payment_details' => $details,
                                    'transactionID'=> $transactionID,
                                    'policy_number'=> $policy_number,
                                    'main_insured'=> $main_insured,
                                    'pay_mode'=> $pay_cycle,
                                    'effectivedate'=> $effectivedate,
                                    'payment_amount'=> $policy_amount,
                                    'rate_up'=> $RateUpper,
                                    'nonPayable'=> $nonPayable,
                                    'payable_amount'=> $payable,
                                    'pay_form'=> $pay_form,
                                    'date_due'=> $date_due,
                                    'date_paid'=> $date_paid,
                                    'agent_code'=> $agent_code,
                                    'agent_city'=> $agent_city,
                                    'payments_type'=> $type,
                                    'com_posted'=> $com_posted,
                                    'discount'=> $discount,
                                    'PaymenMinusFee'=> $PaymenMinusFee,
                                    'PayableMinusFee'=> $PayableMinusFee,
                                    'pay_to' =>$pay_to,
                                    'Date_Created'=> $now,
                                    'XC4' =>$now,
                                    'carrier'=> $carrier,
									'approved' => 0,
                                    'void'=> 0,
                                  );
                                    
                                   //print_r($formdata);
                                  $insert=insert_table_data($formdata);
                                 
                                   
                  }

                  if($idagent3){

                    $agent_data3 = getAgentCommissionByPolicyid($policy_id,$idagent3);
                    //$comission = $agent_data3['commission'];
                    $comission = $agent_commission_three;
                    $agent_details3 = getAgentdetbyID($idagent3);
                    $pay_by = $agent_details3['pay_by'];
                    $pay_to = $agent_details3['payto'];
                    $level = $agent_details3['level'];
                    $main_insured_data = getmaininsured($policy_id);
                    $main_insured_fname = $main_insured_data['first_name'];
                    $main_insured_lname = $main_insured_data['last_name'];
                    $main_insured = $main_insured_fname.' '.$main_insured_lname;
                    $agent_code =$agent_details3['number'];
                    $transactionID = $payment_id.'C'; 
                    $agent_id =$agent_details3['id'];
                    $country_id = $agent_details['idcountry'];
                    $country=get_country_nameby_id($country_id);
                    $country_name = $country['country'];
                    $agent_city=$country_name;

                    if($agent_3_discount>0){
                        $com_posted = (($comission/100)*$payable)-$agent_3_discount;
                        $com_posted = round($com_posted,2); 
                        $discount = $agent_3_discount;              
                    }else{
                        $agent_3_discount = 0;
                       if($pay_cycle==1){  
                            $com_posted = round((($comission/100)*$payable)-$agent_3_discount,2);     
                            $discount = $agent_3_discount;                   
                        }
                        else if($pay_cycle==2){ 
                            $com_posted = round((($comission/100)*$payable)-$agent_3_discount,2);  
                            $discount = $agent_3_discount;
                        }
                       else if($pay_cycle==3){
                          $com_posted = round((($comission/100)*$payable)-$agent_3_discount,2);  
                           $discount = $agent_3_discount;                        
                        }
                      else if($pay_cycle==4){
                          $com_posted = round((($comission/100)*$payable)-$agent_3_discount,2);  
                          $discount = $agent_3_discount;
                        }
                      }

                      $formdata = array(

                                    'policy_id'=>$policy_id,
                                    'agent_id'=> $agent_id,
                                    'level_id'=> $level,
                                    'commission'=> $comission,
                                    'pay_by' => $pay_by,
                                    'payment_details' => $details,
                                    'transactionID'=> $transactionID,
                                    'policy_number'=> $policy_number,
                                    'main_insured'=> $main_insured,
                                    'pay_mode'=> $pay_cycle,
                                    'effectivedate'=> $effectivedate,
                                    'payment_amount'=> $policy_amount,
                                    'rate_up'=> $RateUpper,
                                    'nonPayable'=> $nonPayable,
                                    'payable_amount'=> $payable,
                                    'pay_form'=> $pay_form,
                                    'date_due'=> $date_due,
                                    'date_paid'=> $date_paid,
                                    'agent_code'=> $agent_code,
                                    'agent_city'=> $agent_city,
                                    'payments_type'=> $type,
                                    'com_posted'=> $com_posted,
                                    'discount'=> $discount,
                                    'PaymenMinusFee'=> $PaymenMinusFee,
                                    'PayableMinusFee'=> $PayableMinusFee,
                                    'pay_to' =>$pay_to,
                                    'Date_Created'=> $now,
                                    'XC4' =>$now,
                                    'carrier'=> $carrier,
									'approved' => 0,
                                    'void'=> 0,
                                  );
                                    
                                   //print_r($formdata);
                                   $insert=insert_table_data($formdata);
                                   
                                   
                    }
                    if($idagent4){

                      $agent_data4 = getAgentCommissionByPolicyid($policy_id,$idagent4);
                      //$comission = $agent_data4['commission'];
                      $comission = $agent_commission_four;
                      $agent_details4 = getAgentdetbyID($idagent4);
                      $pay_by = $agent_details4['pay_by'];
                      $pay_to = $agent_details4['payto'];
                      $level = $agent_details4['level'];
                      $main_insured_data = getmaininsured($policy_id);
                      $main_insured_fname = $main_insured_data['first_name'];
                      $main_insured_lname = $main_insured_data['last_name'];
                      $main_insured = $main_insured_fname.' '.$main_insured_lname;
                      $agent_code =$agent_details4['number'];
                      $transactionID = $payment_id.'D'; 
                      $agent_id =$agent_details4['id']; 
                      $country_id = $agent_details['idcountry'];
                      $country=get_country_nameby_id($country_id);
                      $country_name = $country['country'];
                      $agent_city=$country_name;

                      if($agent_4_discount>0){
                          $com_posted = (($comission/100)*$payable)-$agent_4_discount;
                          $com_posted = round($com_posted,2);  ;
                          $discount = $agent_4_discount;              
                      }else{

                            $agent_4_discount = 0;
                         if($pay_cycle==1){  
                              $com_posted = round((($comission/100)*$payable)-$agent_4_discount,2);  
                              $discount = $agent_4_discount;                      
                          }
                          else if($pay_cycle==2){ 
                              $com_posted = round((($comission/100)*$payable)-$agent_4_discount,2);  
                              $discount = $agent_4_discount;
                          }
                         else if($pay_cycle==3){
                             $com_posted = round((($comission/100)*$payable)-$agent_4_discount,2);   
                             $discount = $agent_4_discount;                       
                          }
                        else if($pay_cycle==4){
                            $com_posted = round((($comission/100)*$payable)-$agent_4_discount,2);
                            $discount = $agent_4_discount;
                          }
                      }

                      $formdata = array(

                                    'policy_id'=>$policy_id,
                                    'agent_id'=> $agent_id,
                                    'level_id'=> $level,
                                    'commission'=> $comission,
                                    'pay_by' => $pay_by,
                                    'payment_details' => $details,
                                    'transactionID'=> $transactionID,
                                    'policy_number'=> $policy_number,
                                    'main_insured'=> $main_insured,
                                    'pay_mode'=> $pay_cycle,
                                    'effectivedate'=> $effectivedate,
                                    'payment_amount'=> $policy_amount,
                                    'rate_up'=> $RateUpper,
                                    'nonPayable'=> $nonPayable,
                                    'payable_amount'=> $payable,
                                    'pay_form'=> $pay_form,
                                    'date_due'=> $date_due,
                                    'date_paid'=> $date_paid,
                                    'agent_code'=> $agent_code,
                                    'agent_city'=> $agent_city,
                                    'payments_type'=> $type,
                                    'com_posted'=> $com_posted,
                                    'discount'=> $discount,
                                    'PaymenMinusFee'=> $PaymenMinusFee,
                                    'PayableMinusFee'=> $PayableMinusFee,
                                    'pay_to' =>$pay_to,
                                    'Date_Created'=> $now,
                                    'XC4' =>$now,
                                    'carrier'=> $carrier,
									'approved' => 0,
                                    'void'=> 0,
                                  );
                                   
                                   //print_r($formdata);
                                  $insert=insert_table_data($formdata);                   

                            }
                            
                    if($idagent5){

                      $agent_data5 = getAgentCommissionByPolicyid($policy_id,$idagent5);
                      //$comission = $agent_data4['commission'];
                      $comission = $agent_commission_five;
                      $agent_details5 = getAgentdetbyID($idagent5);
                      $pay_by = $agent_details5['pay_by'];
                      $pay_to = $agent_details5['payto'];
                      $level = $agent_details5['level'];
                      $main_insured_data = getmaininsured($policy_id);
                      $main_insured_fname = $main_insured_data['first_name'];
                      $main_insured_lname = $main_insured_data['last_name'];
                      $main_insured = $main_insured_fname.' '.$main_insured_lname;
                      $agent_code =$agent_details5['number'];
                      $transactionID = $payment_id.'D'; 
                      $agent_id =$agent_details5['id']; 
                      $country_id = $agent_details['idcountry'];
                      $country=get_country_nameby_id($country_id);
                      $country_name = $country['country'];
                      $agent_city=$country_name;

                      if($agent_5_discount>0){
                          $com_posted = (($comission/100)*$payable)-$agent_5_discount;
                          $com_posted = round($com_posted,2);
                          $discount = $agent_5_discount;              
                      }else{

                        $agent_5_discount = 0;
                         if($pay_cycle==1){  
                              $com_posted = round((($comission/100)*$payable)-$agent_5_discount,2);  
                              $discount = $agent_5_discount;                      
                          }
                          else if($pay_cycle==2){ 
                              $com_posted = round((($comission/100)*$payable)-$agent_5_discount,2);  
                              $discount = $agent_5_discount;
                          }
                         else if($pay_cycle==3){
                             $com_posted = round((($comission/100)*$payable)-$agent_5_discount,2);   
                             $discount = $agent_5_discount;                       
                          }
                        else if($pay_cycle==4){
                            $com_posted = round((($comission/100)*$payable)-$agent_5_discount,2);
                            $discount = $agent_5_discount;
                          }
                      }

                      $formdata = array(

                                    'policy_id'=>$policy_id,
                                    'agent_id'=> $agent_id,
                                    'level_id'=> $level,
                                    'commission'=> $comission,
                                    'pay_by' => $pay_by,
                                    'payment_details' => $details,
                                    'transactionID'=> $transactionID,
                                    'policy_number'=> $policy_number,
                                    'main_insured'=> $main_insured,
                                    'pay_mode'=> $pay_cycle,
                                    'effectivedate'=> $effectivedate,
                                    'payment_amount'=> $policy_amount,
                                    'rate_up'=> $RateUpper,
                                    'nonPayable'=> $nonPayable,
                                    'payable_amount'=> $payable,
                                    'pay_form'=> $pay_form,
                                    'date_due'=> $date_due,
                                    'date_paid'=> $date_paid,
                                    'agent_code'=> $agent_code,
                                    'agent_city'=> $agent_city,
                                    'payments_type'=> $type,
                                    'com_posted'=> $com_posted,
                                    'discount'=> $discount,
                                    'PaymenMinusFee'=> $PaymenMinusFee,
                                    'PayableMinusFee'=> $PayableMinusFee,
                                    'pay_to' =>$pay_to,
                                    'Date_Created'=> $now,
                                    'XC4' =>$now,
                                    'carrier'=> $carrier,
									'approved' => 0,
                                    'void'=> 0,
                                  );
                                   
                                   //print_r($formdata);
                                  $insert=insert_table_data($formdata);                   

                            }
                              if($insert){
                               
                                addAudit(array("uid"=>$user_id,"idpolicy"=>$policy_id,"action"=>$user_name." changed Action on payment form to ".$value)); 
                                $data_sucess['sucess'] = 1; 
                                $update_payment_table = update_payment_table($payment_id,$value); 

                              }
                              else{
                                
                               $data_sucess['sucess'] = 0; 
                              }

                  }
                }
                else{
                   $update_payment_table = update_payment_table($payment_id,$value);
                   addAudit(array("uid"=>$user_id,"idpolicy"=>$policy_id,"action"=>$user_name." changed Action on payment form to ".$value)); 
                   if($update_payment_table){
                    $data_sucess['sucess'] = 2;
                    
                   }
                }
                echo json_encode($data_sucess);

           break;

           case 'create_annual_commission':

            
            $policy_id = trim($_POST['policy_id']);
            $pay_form = trim($_POST['pay_form']);
            $commission = trim($_POST['annual_commission']);
            $level_id = trim($_POST['level']);
             
            $PremiumsHealth =getPremiumsHealth($policy_id);

            $sumofpre = round($PremiumsHealth['sumofpre'],2);
            $sumofbasepre = $PremiumsHealth['sumofbasepre'];
            $NonComAm = $sumofpre-$sumofbasepre;
            $NonComPer = ($NonComAm)/($sumofpre/100);
            $RateUpper = round($NonComPer,2);

            $policy_data = get_policy_data_by_policyid($policy_id);
            $policy_fee = $policy_data['fee'];
            $policy_number = $policy_data['policynumber'];
            $agent_id = $policy_data['idagent'];
            $idagent2 = $policy_data['idagent2']; 
            $idagent3 = $policy_data['idagent3']; 
            $idagent4 = $policy_data['idagent4']; 
            $city = $policy_data['city'];
            $pay_cycle=$policy_data['idpaycycle'];
            $effectivedate = $policy_data['effectivedate'];
            $effdate=date_create($effectivedate);
            $date_due = date_format($effdate,"m/d/Y");
            $type =$policy_data['type'];
            $carrier =  $policy_data['carrier'];
            $now = date("m/d/Y");

            if($level_id==1){
              $get_agent_id = $agent_id;
              $transactionID = $policy_id.'A-An '.$now;
              $transid = $policy_id.'A-An';
            }
            else if ($level_id==2) {
              $get_agent_id = $idagent2;
              $transactionID = $policy_id.'B-An '.$now;             
            }
            else if ($level_id==3) {
              $get_agent_id = $idagent3;
              $transactionID = $policy_id.'C-An '.$now;           
            }
             else if ($level_id==4) {
              $get_agent_id = $idagent4;
              $transactionID = $policy_id.'D-An '.$now;   
            }

            $agent_details = getAgentdetbyID($get_agent_id);
            $pay_by = $agent_details['pay_by']; 
            $pay_to = $agent_details['payto'];
            $first_name = $agent_details['name'];
            $last_name = $agent_details['lastname'];
            $main_insured =$first_name.' '.$last_name;
            $agent_code =$agent_details['number'];
            $country_id = $agent_details['idcountry'];
            $level = $agent_details['level'];

            $country=get_country_nameby_id($country_id);
            $country_name = $country['country'];
            $agent_city=$country_name;
            
            $nonpayable = round($NonComAm+$policy_fee,2);
            $payable_amount =round($sumofpre-$nonpayable,2);
            $com_posted = round((($commission/100)*$payable_amount),2);
            $PaymenMinusFee = round($sumofpre-$policy_fee,2);
            $PayableMinusFee =  round($PaymenMinusFee*($NonComPer/100),2);
            
            $date_paid = $now;
            $details = 'Annual Commission process on '.$now;

            $formdata = array(

                        'policy_id'=>$policy_id,
                        'agent_id'=> $get_agent_id,
                        'level_id'=> $level,
                        'commission'=> $commission,
                        'pay_by' => $pay_by,
                        'payment_details' => $details,
                        'transactionID'=> $transactionID,
                        'policy_number'=> $policy_number,
                        'main_insured'=> $main_insured,
                        'pay_mode'=> $pay_cycle,
                        'effectivedate'=> $effectivedate,
                        'payment_amount'=> $sumofpre,
                        'rate_up'=> $RateUpper,
                        'nonPayable'=> $nonpayable,
                        'payable_amount'=> $payable_amount,
                        'pay_form'=> $pay_form,
                        'date_due'=> $date_due,
                        'date_paid'=> $date_paid,
                        'agent_code'=> $agent_code,
                        'agent_city'=> $agent_city,
                        'payments_type'=> $type,
                        'com_posted'=> $com_posted,
                        'PaymenMinusFee'=> $PaymenMinusFee,
                        'PayableMinusFee'=> $PayableMinusFee,
                        'pay_to' =>$pay_to,
                        'Date_Created'=> $now,
                        'XC4' =>$now,
                        'carrier'=> $carrier,
						'approved' => 0,
                        'void'=> 0,

                     );                   
                        //print_r($formdata);
                $insert=insert_table_data($formdata); 

                if($insert){
                 addAudit(array("uid"=>$user_id,"idpolicy"=>$policy_id,"action"=>$user_name." Created Annual Commission for Level ".$level)); 
                 $data_sucess['sucess'] = 1;

                }
                else{
                   
                    $data_sucess['sucess'] = 0; 
                }

                echo json_encode($data_sucess);

            break;
            
            case 'get_payments':
            $policy_id = trim($_POST['policy_id']);
            
            $get_payments=getPaymentsLists($policy_id);
            
            if($get_payments){
                echo '<option value="0">&nbsp;</option>';
                foreach($get_payments as $key => $value){
                    echo '<option value="'.$value['id'].'">'.$value['id'].'</option>';
                   //echo '<a href="'.$value['id'].'">'.$value['id'].'</a><br>';
                }
            }
            
            break;
            
            case 'get_commissions_result':
            $policy_id = trim($_POST['policy_id']);
            $payment_id = trim($_POST['payment_id']);
            
            $get_ann_commissions=get_annual_commission_temp($policy_id);
            echo '<h4>Annual Commissions</h4>';
            echo '<table style="border:1px solid">';
            echo '<tr>
            <th style="border:1px solid">Transaction ID</th>
            <th style="border:1px solid">Agent Level</th>
            <th style="border:1px solid">Commission</th>
            <th style="border:1px solid">Rate Up</th>
            <th style="border:1px solid">Non Payable</th>
            <th style="border:1px solid">Payable Amount</th>
            <th style="border:1px solid">Com Posted</th>
            <th style="border:1px solid">Payment minus fee</th>
            <th style="border:1px solid">payable minus fee</th>
            
            </tr>';
            
             if($get_ann_commissions){
                foreach($get_ann_commissions as $key => $value){
                   echo '<tr>
                   
                   <td style="border:1px solid">'.$value['transactionID'].'</td>
                   <td style="border:1px solid">'.$value['level_id'].'</td>
                   <td style="border:1px solid">'.$value['commission'].'</td>
                   <td style="border:1px solid">'.$value['rate_up'].'</td>
                   <td style="border:1px solid">'.$value['nonPayable'].'</td>
                   <td style="border:1px solid">'.$value['payable_amount'].'</td>
                   <td style="border:1px solid">'.$value['com_posted'].'</td>
                   <td style="border:1px solid">'.$value['PaymenMinusFee'].'</td>
                   <td style="border:1px solid">'.$value['PayableMinusFee'].'</td>
                   
                   </tr>';
                }
            }
            echo '</table>';
            
            echo '<br><br>';
            $get_commissions=get_commissionresult_temp($policy_id,$payment_id);
            echo '<h4>Commissions</h4>';
            echo '<table style="border:1px solid">';
            echo '<tr>
            <th style="border:1px solid">Transaction ID</th>
            <th style="border:1px solid">Agent Level</th>
            <th style="border:1px solid">Commission</th>
            <th style="border:1px solid">Rate Up</th>
            <th style="border:1px solid">Non Payable</th>
            <th style="border:1px solid">Payable Amount</th>
            <th style="border:1px solid">Com Posted</th>
            <th style="border:1px solid">Payment minus fee</th>
            <th style="border:1px solid">payable minus fee</th>
            
            </tr>';
            
             if($get_commissions){
                foreach($get_commissions as $key => $value){
                   echo '<tr>
                   <td style="border:1px solid">'.$value['transactionID'].'</td>
                   <td style="border:1px solid">'.$value['level_id'].'</td>
                   <td style="border:1px solid">'.$value['commission'].'</td>
                   <td style="border:1px solid">'.$value['rate_up'].'</td>
                   <td style="border:1px solid">'.$value['nonPayable'].'</td>
                   <td style="border:1px solid">'.$value['payable_amount'].'</td>
                   <td style="border:1px solid">'.$value['com_posted'].'</td>
                   <td style="border:1px solid">'.$value['PaymenMinusFee'].'</td>
                   <td style="border:1px solid">'.$value['PayableMinusFee'].'</td>
                   
                   </tr>';
                }
            }
            echo '</table>';
            
            break;
            
            
            case 'save_agent_commission':
            $agent_level_commission = trim($_POST['agent_commission_level']);
            $agent_level = trim($_POST['agent_level']);
            $policy_id = trim($_POST['policy_id']);
            
            
            $update_data = update_agent_commission($agent_level_commission,$agent_level,$policy_id);
            
            if($update_data){
                $data_sucess['sucess'] = 1;
            }
            else{
                $data_sucess['sucess'] = 0;
            }
            
            echo json_encode($data_sucess);
           
            break;
            
            case 'save_lock_commission':
            
            $policy_id = trim($_POST['policy_id']);
            $lockcommission = trim($_POST['lockcommission']);
            $update_data = update_policy_table($lockcommission,$policy_id);
            if($update_data){
                $data_sucess['sucess'] = 1;
            }
            else{
                $data_sucess['sucess'] = 0;
            }
            
             echo json_encode($data_sucess);
            break;
            
            case 'get_annual_commission':
            $policy_id = trim($_POST['policy_id']);
            
            
            $get_ann_commissions=get_annual_commission_temp($policy_id);
            echo '<h4>Annual Commissions</h4>';
            echo '<table style="border:1px solid">';
            echo '<tr>
            <th style="border:1px solid">Transaction ID</th>
            <th style="border:1px solid">Agent Level</th>
            <th style="border:1px solid">Commission</th>
            <th style="border:1px solid">Rate Up</th>
            <th style="border:1px solid">Non Payable</th>
            <th style="border:1px solid">Payable Amount</th>
            <th style="border:1px solid">Com Posted</th>
            <th style="border:1px solid">Payment minus fee</th>
            <th style="border:1px solid">payable minus fee</th>
            
            </tr>';
            
             if($get_ann_commissions){
                foreach($get_ann_commissions as $key => $value){
                   echo '<tr>
                   
                   <td style="border:1px solid">'.$value['transactionID'].'</td>
                   <td style="border:1px solid">'.$value['level_id'].'</td>
                   <td style="border:1px solid">'.$value['commission'].'</td>
                   <td style="border:1px solid">'.$value['rate_up'].'</td>
                   <td style="border:1px solid">'.$value['nonPayable'].'</td>
                   <td style="border:1px solid">'.$value['payable_amount'].'</td>
                   <td style="border:1px solid">'.$value['com_posted'].'</td>
                   <td style="border:1px solid">'.$value['PaymenMinusFee'].'</td>
                   <td style="border:1px solid">'.$value['PayableMinusFee'].'</td>
                   
                   </tr>';
                }
            }
            echo '</table>';
            break;
       // Print Agent Commissions from Here
       case 'print_agent_commission':
        $transaction_id = trim($_POST['transaction_id']);
        $payment_type = trim($_POST['payment_type']);
        $carrier = trim($_POST['carrier']);
        $policy_number = trim($_POST['policy_number']);
        $main_insured = trim($_POST['main_insured']);
        $pay_cycle = trim($_POST['pay_cycle']);
        $effective_date = trim($_POST['effective_date']);
        $agent_name = trim($_POST['agent_name']);
        $pay_to = trim($_POST['pay_to']);
        $level_id = trim($_POST['level_id']);
        $agent_code = trim($_POST['agent_code']);
        $pay_form = trim($_POST['pay_form']);
        $payment_amount = trim($_POST['payment_amount']);
        $discount = trim($_POST['discount']);
        $rate_up = trim($_POST['rate_up']);
        $non_payable = trim($_POST['non_payable']);
        $payable_amount = trim($_POST['payable_amount']);
        $commission = trim($_POST['commission']);
        $com_posted = trim($_POST['com_posted']);
        $payment_details = trim($_POST['payment_details']);
        $due_date = trim($_POST['due_date']);
        $paid_date = trim($_POST['paid_date']);

        $formdata = array(
          'transaction_id'  => $transaction_id,
          'payment_type'    => $payment_type,
          'carrier'         => $carrier,
          'policy_number'   => $policy_number,
          'main_insured'    => $main_insured,
          'pay_cycle'       => $pay_cycle,
          'effective_date'  => $effective_date,
          'agent_name'      => $agent_name,
          'pay_to'          => $pay_to,
          'level_id'        => $level_id,
          'agent_code'      => $agent_code,
          'pay_form'        => $pay_form,
          'payment_amount'  => $payment_amount,
          'discount'        => $discount,
          'rate_up'         => $rate_up,
          'non_payable'     => $non_payable,
          'payable_amount'  => $payable_amount,
          'commission'      => $commission,
          'com_posted'      => $com_posted,
          'payment_details' => $payment_details,
          'due_date'        => $due_date,
          'paid_date'       => $paid_date
        );

        $data_insert = createPrintingData($formdata);
        if($data_insert){
          $data_success['success'] = 1;
        } else {
          $data_success['success'] = 0;
        }
        echo json_encode($data_success);
       break;
       
       // Print BDX Report from Here
       case 'print_bdx_report':
        $bdx_month = trim($_POST['bdx_month']);
        $bdx_plan = trim($_POST['bdx_plan']);
        $bdx_policynumber = trim($_POST['bdx_policynumber']);
        $bdx_lob = trim($_POST['bdx_lob']);
        $bdx_grouped_insured = trim($_POST['bdx_grouped_insured']);
        $bdx_country = trim($_POST['bdx_country']);
        $bdx_renewal_status = trim($_POST['bdx_renewal_status']);
        $bdx_paycycle = trim($_POST['bdx_paycycle']);
        $bdx_underwriting_year = trim($_POST['bdx_underwriting_year']);
        $bdx_join_date = trim($_POST['bdx_join_date']);
        $bdx_renewal_date = trim($_POST['bdx_renewal_date']);
        $bdx_datecancel = trim($_POST['bdx_datecancel']);
        $bdx_currency = trim($_POST['bdx_currency']);
        $bdx_relation = trim($_POST['bdx_relation']);
        $bdx_premium = trim($_POST['bdx_premium']);
        $bdx_basepremium = trim($_POST['bdx_basepremium']);
        $bdx_policy_status = trim($_POST['bdx_policy_status']);

        $formdata = array(
          'transaction_id'  => $bdx_month,
          'payment_type'    => $bdx_plan,
          'carrier'         => $bdx_policynumber,
          'payment_details'         => $bdx_lob,
          'policy_number'   => $bdx_grouped_insured,
          'main_insured'    => $bdx_country,
          'pay_cycle'       => $bdx_renewal_status,
          'effective_date'  => $bdx_paycycle,
          'agent_name'      => $bdx_underwriting_year,
          'pay_to'          => $bdx_join_date,
          'level_id'        => $bdx_renewal_date,
          'agent_code'      => $bdx_datecancel,
          'pay_form'        => $bdx_currency,
          'payment_amount'  => $bdx_relation,
          'discount'        => $bdx_premium,
          'rate_up'         => $bdx_basepremium,
          'non_payable'     => $bdx_policy_status
        );

        $data_insert = createPrintingData($formdata);
        if($data_insert){
          $data_success['success'] = 1;
        } else {
          $data_success['success'] = 0;
        }
        echo json_encode($data_success);
       break;
      
      case 'print_policy_status_commission':

        $transaction_id = trim($_POST['transaction_id']);
        $agent_name = trim($_POST['agent_name']);
        $agent_code = trim($_POST['agent_code']);
        $agent_level = trim($_POST['agent_level']);
        $main_insured = trim($_POST['main_insured']);   
        $commission = trim($_POST['commission']);
        $payment_amount = trim($_POST['payment_amount']);
        $non_payable = trim($_POST['non_payable']);
        $payable_amount = trim($_POST['payable_amount']);
        $com_posted = trim($_POST['com_posted']);
        $effectivedate = trim($_POST['effectivedate']);
        $date_created = trim($_POST['date_created']);
        $date_due = trim($_POST['date_due']);
        $date_paid = trim($_POST['date_paid']);
        $date_printed = trim($_POST['date_printed']);
        $payment_details = trim($_POST['payment_details']);
        

        $formdata = array(
          'transaction_id'  => $transaction_id,
          'agent_name'      => $agent_name,
          'agent_code'      => $agent_code,
          'level_id'        => $agent_level,
          
          'main_insured'    => $main_insured,
          'commission'      => $commission,
          'payment_amount'  => $payment_amount,
          'com_posted'      => $com_posted,
          'non_payable'     => $non_payable,
          'payable_amount'  => $payable_amount,
          'effective_date'  => $effectivedate,
          'approved_date'   => $date_created,
          'printed_date'    => $date_printed,
          'due_date'        => $date_due,
          'paid_date'       => $date_paid,
          'payment_details' => $payment_details
        );

        $data_insert = createPrintingData($formdata);
        if($data_insert){
          $data_success['success'] = 1;
        } else {
          $data_success['success'] = 0;
        }
        echo json_encode($data_success);
      break;

      case 'print_report_rpt':
        $policy_number = trim($_POST['policy_number']);
        $policy_plan = trim($_POST['policy_plan']);
        $policy_status = trim($_POST['policy_status']);
        $pay_mode = trim($_POST['pay_mode']);
        $action = trim($_POST['action']);
        $date_due = trim($_POST['date_due']);
        $date_paid = trim($_POST['date_paid']);
        $details = trim($_POST['details']);
        $payment_amount = trim($_POST['payment_amount']);
        $payment_fee = trim($_POST['payment_fee']);
        $annual_premium = trim($_POST['annual_premium']);
        $coverage = trim($_POST['coverage']);
        $deductible = trim($_POST['deductible']);
        $date_created = trim($_POST['date_created']);
        $effectivedate = trim($_POST['effectivedate']);

        $formdata = array(
          'policy_number'     => $policy_number,
          'payment_type'      => $policy_plan,
          'transaction_id'    => $policy_status,
          'pay_cycle'         => $pay_mode,
          'pay_form'          => $action,
          'due_date'          => $date_due,
          'paid_date'         => $date_paid,
          'payment_details'   => $details,
          'payment_amount'    => $payment_amount,
          'discount'          => $payment_fee,
          'rate_up'           => $annual_premium,
          'non_payable'       => $coverage,
          'payable_amount'    => $deductible,
          'approved_date'     => $date_created,
          'effective_date'    => $effectivedate
        );  

        $data_insert = createPrintingData($formdata);
        if($data_insert){
          $data_success['success'] = 1;
        } else {
          $data_success['success'] = 0;
        }
        echo json_encode($data_success);

      break;

      case 'print_report_commissions':
        $transaction_id = trim($_POST['transaction_id']);
        $payment_type = trim($_POST['payments_type']);
        $policy_number = trim($_POST['policy_number']);
        $pay_mode = trim($_POST['pay_mode']);
        $effectivedate = trim($_POST['effectivedate']);
        $main_insured = trim($_POST['main_insured']);
        $agent_name = trim($_POST['agent_name']);
        $agent_level = trim($_POST['agent_level']);
        $agent_city = trim($_POST['agent_city']);
        $agent_code = trim($_POST['agent_code']);
        $commission = trim($_POST['commission']);
        $pay_form = trim($_POST['pay_form']);
        $payment_amount = trim($_POST['payment_amount']);
        $discount = trim($_POST['discount']);
        $rate_up = trim($_POST['rate_up']);
        $non_payable = trim($_POST['non_payable']);
        $payable_amount = trim($_POST['payable_amount']);
        $com_posted = trim($_POST['com_posted']);
        $payment_details = trim($_POST['payment_details']);
        $date_due = trim($_POST['date_due']);
        $date_paid = trim($_POST['date_paid']);
        $pay_by = trim($_POST['pay_by']);
        $date_approved = trim($_POST['date_approved']);
        $date_printed = trim($_POST['date_printed']);

        $formdata = array(
          'transaction_id'  => $transaction_id,
          'payment_type'    => $payment_type,
          'policy_number'   => $policy_number,
          'pay_cycle'       => $pay_mode,
          'effective_date'  => $effectivedate,
          'main_insured'    => $main_insured,
          'agent_name'      => $agent_name,
          'agent_city'      => $agent_city,
          'level_id'        => $agent_level,
          'agent_code'      => $agent_code,
          'pay_form'        => $pay_form,
          'payment_amount'  => $payment_amount,
          'discount'        => $discount,
          'rate_up'         => $rate_up,
          'non_payable'     => $non_payable,
          'payable_amount'  => $payable_amount,
          'commission'      => $commission,
          'com_posted'      => $com_posted,
          'payment_details' => $payment_details,
          'due_date'        => $date_due,
          'paid_date'       => $date_paid,
          'pay_by'          => $pay_by,
          'approved_date'   => $date_approved,
          'printed_date'    => $date_printed
        );

        $data_insert = createPrintingData($formdata);
        if($data_insert){
          $data_success['success'] = 1;
        } else {
          $data_success['success'] = 0;
        }
        echo json_encode($data_success);
        
      break;

      case 'get_status_commission_by_policy_id':
        $policy_id = trim($_POST['policy_id']);
        $policy_info = getCommissionsByPolicyid($policy_id);
        
        if($policy_info){
          $data_success['success'] = 1;
        }
        else {
          $data_success['success'] = 0;
        }
        echo json_encode($data_success);
      break;
      
      case 'get_approve_commission':
			$comission_id = trim($_POST['comission_id']);
			$update_data = update_commission_table($comission_id);
			if($update_data){
                $data_sucess['sucess'] = 1;
            }else{
                $data_sucess['sucess'] = 0;
            }
			echo json_encode($data_sucess);
			 break;
             
             case 'update_commission_bulk':
			// echo 'hello';
			$comission_id = trim($_POST['comission_id']);
			$payment_details = trim($_POST['payment_details']);
			$update_data = update_commission_table_bulk($comission_id,$payment_details);
			if($update_data){
                $data_sucess['sucess'] = 1;
            }else{
                $data_sucess['sucess'] = 0;
            }
			echo json_encode($data_sucess);
			 break;
			 
			 case 'get_void_commission':
			// echo 'hello';
			$comission_id = trim($_POST['comission_id']);
			$update_data = update_commission_table_to_void($comission_id);
			if($update_data){
                $data_sucess['sucess'] = 1;
            }else{
                $data_sucess['sucess'] = 0;
            }
			echo json_encode($data_sucess);
			 break;
			 
			  case 'edit_agent_commission':
			 //echo 'hello';
			  $commission_id = trim($_POST['commission_id']);
			  $amount_deducted = trim($_POST['amount_deducted']);
			  $amount_credited = trim($_POST['amount_credited']);
              $date_printed = trim($_POST['date_printed']);
			  $details = trim($_POST['details']);
			  
			  $get_payment_amount = get_all_from_agent_aommission_by_id($commission_id);
			  $payment_amount = $get_payment_amount['payment_amount'];
				if($amount_deducted=="" && $amount_credited=="" && $details==""){
					$data = array(
						'paid_DD'=>1,
                        'date_printed'=>$date_printed,
                     );
				}
				else{
					if($amount_deducted!=""){
						  $new_payment_amount = round(($payment_amount - $amount_deducted),2) ;
					}
					
					if($amount_credited!=""){
						 $new_payment_amount = round(($payment_amount + $amount_credited),2) ;
					}
					if($details!=""){
						 
						 $data = array(
							'payment_amount'=>$new_payment_amount,
							'notes'=> $details,
							'paid_DD'=>1,
                            'date_printed'=>$date_printed,
						 );
					}
					else{
						 $data = array(
							'payment_amount'=>$new_payment_amount,
							'paid_DD'=>1,
                            'date_printed'=>$date_printed,
						 );
					}
				}
			$update_data = update_data('agent_commissions',$commission_id,$data);
			if($update_data){
                $data_sucess['sucess'] = 1;
            }else{
                $data_sucess['sucess'] = 0;
            }
			echo json_encode($data_sucess);
			 break;
			 
			case 'edit_agent_commission_pay_by_group_dd':
			// echo 'hello';
			$comission_id = trim($_POST['agent_id']);
            //$sql_flag="UPDATE agent_commissions SET flag='0'"; 
            //$stats_flag= $db->update($sql_flag);
			$update_data = update_agent_commission_dd($comission_id);
			if($update_data){
                $data_sucess['sucess'] = 1;
            }else{
                $data_sucess['sucess'] = 0;
            }
			echo json_encode($data_sucess);
			 break;
			 
       case 'edit_agent_commission_wt':

       $user_id = trim($_POST['user_id']);
       $db_data['carrier'] = getSingleAdmin($user_id);
       $agent_id = $db_data['agent_id'] = trim($_POST['agent_id']);
       $agent_info = getSingleAgent($agent_id);
       
       $db_data['level_id']= $agent_info['level'];
       $db_data['agent_code'] = $agent_info['number'];
       $agent_city = get_country_nameby_id($agent_info['idcountry']);
       $db_data['agent_city'] = $agent_city['country'];
       $agent_name = $agent_info['name'].' '.$agent_info['lastname'];
       $db_data['pay_to'] = $agent_name;
       //$db_data['carrier'] = 'Claria';
       //$date_printed =  $db_data['date_printed'] = trim ($_POST['date_printed']);
       $details_notes =  $db_data['notes'] = trim($_POST['details']);
       $details =  $db_data['payment_details'] = trim($_POST['details']);
       $pay_by =  $db_data['pay_by'] = "WT";
       $date_paid = $db_data['date_paid'] = date ('Y-m-d');
       $loan_balance = (double)trim($_POST['loan_balance']);
       $pay_form = $db_data['pay_form'] = "Direct Deposit";
       $approved = $db_data['approved'] = 1;

       $amount_deducted = trim($_POST['amount_deducted']);
       $amount_credited = trim($_POST['amount_credited']);
       if($amount_deducted){
         $db_data['com_posted'] = $amount_deducted;
         $loan_amount = $loan_balance - $amount_deducted;
         //Other Info
         $payments_type = $db_data['payments_type'] = "Deduct";
         $db_data['policy_number'] = 'Deduct';
       } else if($amount_credited) {
         $db_data['com_posted'] = $amount_credited;
         $loan_amount = $loan_balance + $amount_credited;
         // Other Info
         $payments_type = $db_data['payments_type'] = "Credited";
         $db_data['policy_number'] = 'Credited';
       }
       
      
       
       // User Info
       //$db_data['carrier'] = getSingleAdmin($user_id);
       //$db_data['carrier'] = "Claria";

       $db_data['date_approved'] = date('Y-m-d'); 

       $insert_data = saveAgentCommissionWT($db_data);
       if($loan_balance != '0'){
          if($amount_deducted){
            $update_data = updateAgentLoanDeductWT($agent_id, $db_data, $loan_amount);
          }
          else if($amount_credited){
            $update_data = updateAgentLoanCreditWT($agent_id, $db_data, $loan_amount);
          }
       } else {
         if($amount_deducted){
          $update_data = insertAgentLoanDeductWT($db_data, $loan_amount);
         } else if($amount_credited) {
          $update_data = insertAgentLoanCreditWT($db_data, $loan_amount);
         }
        
       }
       

       if($insert_data){
          $data_sucess['sucess'] = 1;
       }else{
          $data_sucess['sucess'] = 0;
       }
      echo json_encode($data_sucess);

    break;
    
			 
			case 'edit_agent_commission_pay_by_group_wt':
			// echo 'hello';
			$agent_id = trim($_POST['agent_id']);
            //$sql_flag="UPDATE agent_commissions SET flag='0'"; 
            //$stats_flag= $db->update($sql_flag);
            //print_r($stats_flag);
            //if($stats_flag)
			$update_data = update_agent_commission_wt($agent_id);
			if($update_data){
                $data_sucess['sucess'] = 1;
            }else{
                $data_sucess['sucess'] = 0;
            }
			echo json_encode($data_sucess);
       break;

    case 'edit_agent_commission_dd':
     
       $user_id = trim($_POST['user_id']);
       $db_data['carrier'] = getSingleAdmin($user_id);
       $agent_id = $db_data['agent_id'] = trim($_POST['agent_id']);
       $agent_info = getSingleAgent($agent_id);
       
       $db_data['level_id']= $agent_info['level'];
       $db_data['agent_code'] = $agent_info['number'];
       $agent_city = get_country_nameby_id($agent_info['idcountry']);
       $db_data['agent_city'] = $agent_city['country'];
       $agent_name = $agent_info['name'].' '.$agent_info['lastname'];
       $db_data['pay_to'] = $agent_name;
       //$db_data['carrier'] = 'Claria';
       //$date_printed =  $db_data['date_printed'] = trim ($_POST['date_printed']);
       $details_notes =  $db_data['notes'] = trim($_POST['details']);
       $details =  $db_data['payment_details'] = trim($_POST['details']);
       $pay_by =  $db_data['pay_by'] = "DD";
       $date_paid = $db_data['date_paid'] = date ('Y-m-d');
       $loan_balance = (double)trim($_POST['loan_balance']);
       $pay_form = $db_data['pay_form'] = "Direct Deposit";
       $approved = $db_data['approved'] = 1;

       $amount_deducted = trim($_POST['amount_deducted']);
       $amount_credited = trim($_POST['amount_credited']);
       if($amount_deducted){
         $db_data['com_posted'] = $amount_deducted;
         $loan_amount = $loan_balance - $amount_deducted;
         //Other Info
         $payments_type = $db_data['payments_type'] = "Deduct";
         $db_data['policy_number'] = 'Deduct';
       } else if($amount_credited) {
         $db_data['com_posted'] = $amount_credited;
         $loan_amount = $loan_balance + $amount_credited;
         // Other Info
         $payments_type = $db_data['payments_type'] = "Credited";
         $db_data['policy_number'] = 'Credited';
       }
       
      
       
       // User Info
       //$db_data['carrier'] = getSingleAdmin($user_id);
       //$db_data['carrier'] = "Claria";

       $db_data['date_approved'] = date('Y-m-d'); 

       $insert_data = saveAgentCommissionWT($db_data);
       if($loan_balance != '0'){
          if($amount_deducted){
            $update_data = updateAgentLoanDeductWT($agent_id, $db_data, $loan_amount);
          }
          else if($amount_credited){
            $update_data = updateAgentLoanCreditWT($agent_id, $db_data, $loan_amount);
          }
       } else {
         if($amount_deducted){
          $update_data = insertAgentLoanDeductWT($db_data, $loan_amount);
         } else if($amount_credited) {
          $update_data = insertAgentLoanCreditWT($db_data, $loan_amount);
         }
        
       }
       

       if($insert_data){
          $data_sucess['sucess'] = 1;
       }else{
          $data_sucess['sucess'] = 0;
       }
      echo json_encode($data_sucess);
      break;

      
       
    case 'edit_agent_commission_ck':

      
       $user_id = $db_data['carrier'] = trim($_POST['user_id']);
       $agent_id = $db_data['agent_id'] = trim($_POST['agent_id']);
       $agent_info = getSingleAgent($agent_id);
       
       $db_data['level_id']= $agent_info['level'];
       $db_data['agent_code'] = $agent_info['number'];
       $agent_city = get_country_nameby_id($agent_info['idcountry']);
       $db_data['agent_city'] = $agent_city['country'];
       $agent_name = $agent_info['name'].' '.$agent_info['lastname'];
       $db_data['pay_to'] = $agent_name;
       //$db_data['carrier'] = 'Claria';
       //$date_printed =  $db_data['date_printed'] = trim ($_POST['date_printed']);
       $details_notes =  $db_data['notes'] = trim($_POST['details']);
       $details =  $db_data['payment_details'] = trim($_POST['details']);
       $pay_by =  $db_data['pay_by'] = "CK";
       $date_paid = $db_data['date_paid'] = date ('Y-m-d');
       $loan_balance = (double)trim($_POST['loan_balance']);
       $pay_form = $db_data['pay_form'] = "Direct Deposit";
       $approved = $db_data['approved'] = 1;

       $amount_deducted = trim($_POST['amount_deducted']);
       $amount_credited = trim($_POST['amount_credited']);
       if($amount_deducted){
         $db_data['com_posted'] = $amount_deducted;
         $loan_amount = $loan_balance - $amount_deducted;
         //Other Info
         $payments_type = $db_data['payments_type'] = "Deduct";
         $db_data['policy_number'] = 'Deduct';
       } else if($amount_credited) {
         $db_data['com_posted'] = $amount_credited;
         $loan_amount = $loan_balance + $amount_credited;
         // Other Info
         $payments_type = $db_data['payments_type'] = "Credited";
         $db_data['policy_number'] = 'Credited';
       }
       
      
       
       // User Info
       //$db_data['carrier'] = getSingleAdmin($user_id);
       //$db_data['carrier'] = "Claria";

       $db_data['date_approved'] = date('Y-m-d'); 

       $insert_data = saveAgentCommissionWT($db_data);
       if($loan_balance != '0'){
          if($amount_deducted){
            $update_data = updateAgentLoanDeductWT($agent_id, $db_data, $loan_amount);
          }
          else if($amount_credited){
            $update_data = updateAgentLoanCreditWT($agent_id, $db_data, $loan_amount);
          }
       } else {
         if($amount_deducted){
          $update_data = insertAgentLoanDeductWT($db_data, $loan_amount);
         } else if($amount_credited) {
          $update_data = insertAgentLoanCreditWT($db_data, $loan_amount);
         }
        
       }
       

       if($insert_data){
          $data_sucess['sucess'] = 1;
       }else{
          $data_sucess['sucess'] = 0;
       }
      echo json_encode($data_sucess);

   break;
      
      case 'edit_agent_commission_pay_by_group_ck':
         //echo 'hello';
        $agent_id = trim($_POST['agent_id']);
        //$sql_flag="UPDATE agent_commissions SET flag='0'"; 
        //$stats_flag= $db->update($sql_flag);
         //echo '</br>';
       $update_data = update_agent_commission_ck($agent_id);
          //print_r($update_data);
        if($update_data){
                  $data_sucess['sucess'] = 1;
              }else{
                  $data_sucess['sucess'] = 0;
              }
        echo json_encode($data_sucess);
         break;
        
       case 'edit_agent_commission_pay_by_group_flag':
         $update_data = update_agent_commission_flag();
         if(is_numeric($update_data)){
              $data_sucess['sucess'] = 1;
          }else{
              $data_sucess['sucess'] = 0;
          }
         echo json_encode($data_sucess);
         break;
		
			case 'edit_agent_pay_by_dd':
				// echo 'hello';
				$comission_id = trim($_POST['comission_id']);
				$data = array(
							'pay_by'=>'DD',
						);
				$update_data = update_data('agent_commissions',$comission_id,$data);
				if($update_data){
					$data_sucess['sucess'] = 1;
				}else{
                    $data_sucess['sucess'] = 0;
                }
				echo json_encode($data_sucess);
			break;
			case 'edit_agent_pay_by_ck':
				// echo 'hello';
				$comission_id = trim($_POST['comission_id']);
				$data = array(
							'pay_by'=>'CK',
						);
				$update_data = update_data('agent_commissions',$comission_id,$data);
				if($update_data){
					$data_sucess['sucess'] = 1;
				}else{
                    $data_sucess['sucess'] = 0;
                }
				echo json_encode($data_sucess);
			break;
			case 'edit_agent_pay_by_wt':
				// echo 'hello';
				$comission_id = trim($_POST['comission_id']);
				$data = array(
							'pay_by'=>'WT',
						);
				$update_data = update_data('agent_commissions',$comission_id,$data);
				if($update_data){
					$data_sucess['sucess'] = 1;
				}else{
                    $data_sucess['sucess'] = 0;
                }
				echo json_encode($data_sucess);
      break;

      case 'get_agent_loan_wt': 
        $agent_id = trim($_POST['agent_id']);
        //echo $agent_id;
        if($agent_id){
          $agent_loan = get_agent_loan_WT($agent_id);
            if($agent_loan){
              $data_sucess['sucess'] = 1;
              $data_sucess['loan'] = $agent_loan[0];
            }
            else {
              $data_sucess['sucess'] = 0;
            }
        } else {
            echo "No loan is found in database";
        }
        echo json_encode($data_sucess);
      break;

      case 'get_agent_loan_dd': 
        $agent_id = trim($_POST['agent_id']);
        //echo $agent_id;
        if($agent_id){
          $agent_loan = get_agent_loan_WT($agent_id);
            if($agent_loan){
              $data_sucess['sucess'] = 1;
              $data_sucess['loan'] = $agent_loan[0];
            }
            else {
              $data_sucess['sucess'] = 0;
            }
        } else {
            echo "No loan is found in database";
        }
        echo json_encode($data_sucess);
      break;

      case 'get_agent_loan_ck': 
        $agent_id = trim($_POST['agent_id']);
        //echo $agent_id;
        if($agent_id){
          $agent_loan = get_agent_loan_CK($agent_id);
            if($agent_loan){
              $data_sucess['sucess'] = 1;
              $data_sucess['loan'] = $agent_loan[0];
            }
            else {
              $data_sucess['sucess'] = 0;
            }
        } else {
            echo "No loan is found in database";
        }
        echo json_encode($data_sucess);
      break;
      
      //Added on 15 January 2021 {Shounok}
      case 'save_letter_of_benefit':
        
        $db_data['patient_idinsured']= $patient_idinsured = trim($_POST['patient_insured_id']);
        $db_data['primary_idinsured']= $primary_idinsured = trim($_POST['primary_insured_id']);
        $db_data['idpolicy']= $idpolicy = trim($_POST['policy_id_input']);
        $db_data['provider_from']= $provider_from = trim($_POST['provider_from']);
        $db_data['provider_to']= $provider_to = trim($_POST['provider_to']);
        $db_data['name']= $provider_name = trim($_POST['provider_name']);
        $db_data['tax_id']= $provider_tax_id = trim($_POST['provider_tax_id']);
        $db_data['email']= $provider_email = trim($_POST['provider_email']);
        $db_data['telephone']= $provider_telephone = trim($_POST['provider_telephone']);
        $db_data['date_sent']= $provider_date_sent = trim($_POST['provider_date_sent']);
        $db_data['details']= $details = trim($_POST['claim_details']);
        
        $insert_data = saveLetterofBenefits($db_data, $idpolicy, $patient_idinsured);
          
        if($insert_data){
          $data_success['success'] = 1;
        } else {
          $data_success['success'] = 0;
        }
        
        echo json_encode($data_success);
      break;
      // Added on 19 January 2021
      case 'insured_letter_of_benefit': 
        $insured_id = trim($_POST['insured_id']);
        $insured_info = getHealthSingleInsured($insured_id);
        //print_r($insured_info);
        if($insured_info){
          $data_success['success'] = 1;
          $data_success['full_name'] = $insured_info['first_name'].' '.$insured_info['last_name'];
          $data_success['date_of_birth'] = dateFormFormat($insured_info['dob']);
        } else {
          $data_success['success'] = 0;
        }
        echo json_encode($data_success);
        
      break;

    // By Faroque on 21 January 2021
    case 'save_claim': // faroque

        try{
          
          $formData = array();
          $id = $_REQUEST['id'];
          
          $formData['idpolicy '] = $_REQUEST['idpolicy'];
          $formData['idclaimant '] = $_REQUEST['idclaimant'];
          $formData['idinsured '] = $_REQUEST['idinsured'];
          $formData['idstatusclaim '] = $_REQUEST['idstatusclaim'];
          // Claim Number
          $formData['clnum '] = $_REQUEST['clnum'];
          // Diagnosis
          $formData['id_diagnosis_1 '] = $_REQUEST['diagnosis1'];
          $formData['id_diagnosis_2 '] = $_REQUEST['diagnosis2'];
          $formData['id_diagnosis_3 '] = $_REQUEST['diagnosis3'];
          $formData['id_diagnosis_4 '] = $_REQUEST['diagnosis4'];
          // Treatment not done yet
          $formData['id_treatment_1 '] = $_REQUEST['treatment1'];
          $formData['id_treatment_2 '] = $_REQUEST['treatment2'];
          // User Info
          $formData['iduser '] = $_REQUEST['iduser'];
          
          if($_REQUEST['claimIn'] != '')$_REQUEST['claimIn'] = date('Y-m-d', strtotime($_REQUEST['claimIn']));
          if($_REQUEST['date_prenotify'] != '')$_REQUEST['date_prenotify'] = date('Y-m-d', strtotime($_REQUEST['date_prenotify']));
          
          if($_REQUEST['valid_start'] != '')$_REQUEST['valid_start'] = date('Y-m-d', strtotime($_REQUEST['valid_start']));
          if($_REQUEST['valid_end'] != '')$_REQUEST['valid_end'] = date('Y-m-d', strtotime($_REQUEST['valid_end']));
          if($_REQUEST['hosp_date'] != '')$_REQUEST['hosp_date'] = date('Y-m-d', strtotime($_REQUEST['hosp_date']));

          $formData['claimIn'] = $_REQUEST['claimIn'];
          if(isset($_REQUEST['claimin_us'])){
            $formData['claimin_us '] = 1;
          }else{
            $formData['claimin_us '] = 0;
          }
          $formData['notes '] = $_REQUEST['notes'];
          $formData['idclaimcause '] = $_REQUEST['idclaimcause'];
          $formData['estimate '] = $_REQUEST['estimate'];
          if(isset($_REQUEST['prenotify'])){
            $formData['prenotify '] = 1;
          }else{
            $formData['prenotify '] = 0;
          }

          if(isset($_REQUEST['approved'])){
            $formData['id_mat'] = 1;
          } else{
            $formData['id_mat '] = 0;
          }
          //$formData['prenotify '] = $_REQUEST['prenotify'];
          $formData['date_prenotify '] = $_REQUEST['date_prenotify'];
          $formData['idclaimanalyst '] = $_REQUEST['idclaimanalyst'];
          $formData['idclaimtype '] = $_REQUEST['idclaimtype'];
          $formData['estimate_hosp '] = $_REQUEST['estimate_hosp'];
          $formData['estiame_doctor '] = $_REQUEST['estiame_doctor'];
          if(isset($_REQUEST['in_progress'])){
            $formData['in_progress '] = 1;
          }else{
            $formData['in_progress '] = 0;
          }
          $formData['claria_year '] = $_REQUEST['claria_year'];
          $formData['ben_ytd '] = $_REQUEST['ben_ytd'];
          $formData['mat_ytd '] = $_REQUEST['mat_ytd'];
          $formData['ben_ltd '] = $_REQUEST['ben_ltd'];
          $formData['deductible_ytd '] = $_REQUEST['deductible_ytd'];
          $formData['valid_start '] = $_REQUEST['valid_start'];
          $formData['valid_end '] = $_REQUEST['valid_end'];
          $formData['dental_ytd '] = $_REQUEST['dental_ytd'];
          $formData['idcountry '] = $_REQUEST['idcountry'];
          $formData['maternity_id '] = $_REQUEST['maternity_id'];
          $formData['hosp_date'] = $_REQUEST['hosp_date'];
          $formData['claim_updated'] = $_REQUEST['claim_updated'];
         
          if($id)
            $db->doUpdate($formData , 'claims' , "id = " . $id );
          else
          {
            $formData['datecreated '] = date('Y-m-d');
            $id = $db->doInsert($formData , 'claims');
            /*
            $policyInfo = getSinglePolicy($_REQUEST['idpolicy']);
            $clnum = $policyInfo['policynumber'] . 'CLM' . $id;
            //echo $claim_number;
            $db->doUpdate(array('clnum' => $clnum) , 'claims' , "id = " . $id );
            */
          }            

          $data_sucess['id'] = $id;
          $data_sucess['flg'] = 1;
          $data_sucess['msg'] = 'Successfully saved!';

        }
        catch(exception $e)
        {
          $data_sucess['flg'] = 0;
          $data_sucess['msg'] = 'Error occured';
        }       


        echo json_encode($data_sucess);

      break;

      // Added on 22 January 2021 {Shounok}
      case 'savePendingClaim':
        if(isset($_POST['claim_id'])){
          $claim_id = trim($_POST['claim_id']);
        }
        if(isset($_POST['pending_detail'])){
          $pending_detail = trim($_POST['pending_detail']);
        }
        $pending_dateRequested = trim($_POST['pending_dt_req']);
        
        $pending_dateRecieved = trim($_POST['pending_dt_recv']);
        if(isset($_POST['receivedLabel'])){
          $pending_is_received = 1;
        } else {
          $pending_is_received = 0;
        }
        if($claim_id == '' || $claim_id == null){
          $claim_id = getLastClaimID();
          $claim_id = $claim_id['id'];
        }
        // Declaration of DB Data
        $db_data['id_claim'] = $claim_id;
        $db_data['pending_detail'] = $pending_detail;
        $db_data['date_requested'] = dateDBFormat($pending_dateRequested) ;
        $db_data['date_received'] = dateDBFormat($pending_dateRecieved);
        $db_data['is_received'] = $pending_is_received;

        $insert_data = insertPendingClaim($db_data);
        if($insert_data){
          $data_success['success'] = 1;
        } else {
          $data_success['success'] = 0;
        }
        echo json_encode($data_success);
      break;
      // Added on 22 January 2021 {Shounok}  
      case 'updateSinglePendingClaim':
        if(isset($_POST['claim_id'])){
          $claim_id = trim($_POST['claim_id']);
        }
        if(isset($_POST['pending_id'])){
          $pending_id = trim($_POST['pending_id']);
        }
        if(isset($_POST['detail'])){
          $pending_detail = trim($_POST['detail']);
        }
        $date_requested = trim($_POST['d_req']);
        $date_received = trim($_POST['d_rcv']);
        if($_POST['rcvChkbox'] == 'true'){
          $pending_is_received = 1;
        }
        if($_POST['notrcvChkbox'] == 'true'){ 
          $pending_is_received = 0;
        }
        //echo $_POST['rcvChkbox'].' and '.$pending_is_received;
        // Declaration of DB Data
        $db_data['id_claim'] = $claim_id;
        $db_data['pending_detail'] = $pending_detail;
        $db_data['date_requested'] = dateDBFormat($date_requested) ;
        $db_data['date_received'] = dateDBFormat($date_received);
        $db_data['is_received'] = $pending_is_received;
        $update_pendingClaim_data = updateSinglePendingClaim($db_data, $pending_id);
        if($update_pendingClaim_data){
          $data_success['success'] = 1;
        } else {
          $data_success['success'] = 0;
        }
        echo json_encode($data_success);
      break;

      // Added on 26 January 2021
      case 'insertSingleEOB':

      $policy_id = trim($_POST['policy_id']);
      $claim_id = trim($_POST['claim_id']);
      $insured_id = trim($_POST['insured_id']);
      $numOfService = trim($_POST['eob_numOfService']);
      $remarksCode = trim($_POST['eob_remarksCode']);
      $remarksCode_2 = trim($_POST['eob_remarksCode_2']);
      $remarksCode_3 = trim($_POST['eob_remarksCode_3']);
      $dateServiceStart = trim($_POST['eob_dateServiceStart']);
      $dateServiceFinish = trim($_POST['eob_dateServiceFinish']);
      $symbol = trim($_POST['eob_symbol']);
      $foreignCurrency = trim($_POST['eob_foreignCurrency']);
      $amountOfBill = trim($_POST['eob_amountOfBill']);
      $notPayable = trim($_POST['eob_notPayable']);
      $discount = trim($_POST['eob_discount']);
      $deductible = trim($_POST['eob_deductible']);
      $dentalDeductible = trim($_POST['eob_dentalDeductible']);
      $coIns = trim($_POST['eob_coIns']);
      $payableAmount = trim($_POST['eob_payableAmount']);
      $isforeignProvider = trim($_POST['foreign_provider']);
      if($isforeignProvider == 'true'){
        $foreignProvider = 1;
      } else {
        $foreignProvider = 0;
      }
      $benefit = trim($_POST['claim_benefit_id']);
      
      if($claim_id == '' || $claim_id == null){
        $claim_id = getLastClaimID();
        $claim_id = $claim_id['id'];
      }
      // Declaration of DB Data
      $db_data = array(
        'id_policy' => $policy_id,
        'id_claims' => $claim_id,
        'id_insured'  => $insured_id,
        'num_of_service' => $numOfService,
        'remarks_code'  => $remarksCode,
        'remarks_code_2'  => $remarksCode_2,
        'remarks_code_3'  => $remarksCode_3,
        'date_of_service_start' => dateDBFormat($dateServiceStart),
        'date_of_service_finish' => dateDBFormat($dateServiceFinish),
        'symbol'  => $symbol,
        'foreign_currency'  => $foreignCurrency,
        'amount_of_bill'  => $amountOfBill,
        'not_payable'   => $notPayable,
        'discount'      => $discount,
        'deductible'    => $deductible,
        'dental_deductible' => $dentalDeductible,
        'co_ins'        => $coIns,
        'payble_amount' => $payableAmount,
        'foreign_provide' => $foreignProvider,
        'id_benefit'  => $benefit
      );

      $insertEOBdata = insertNewEOB($db_data);
      if($insertEOBdata){
        $data_success['success'] = 1;
      } else {
        $data_success['success'] = 0;
      }

      echo json_encode($data_success);
      break;
      // Added on 26 January 2021
      case 'deleteSingleEOB':
        $eob_id = trim($_POST['eob_detail_id']);
        $data_row_id = trim($_POST['data_row_id']);
        $delete_data = deleteSingleEOBRow($eob_id);
        if($delete_data) {
          $data_success['success'] = 1;
          $data_success['eob_id'] = $eob_id;
          $data_success['data_row_id'] = $data_row_id;
        } else {
          $data_success['success'] = 0;
        }
        echo json_encode($data_success);
      break;
      // Added on 27 January 2021
      case 'calculate_claim':
        // Get the values
        $insured_id = trim($_POST['insured_id']);
        $valid_start = dateDBFormat(trim($_POST['valid_start'])) ;
        $valid_end = dateDBFormat(trim($_POST['valid_end']));
        $deduct30in = trim($_POST['deduct30']);
        $maternity_start = $_POST['maternity_start'];
        $maternity_finish = $_POST['maternity_finish'];
        // Processing of the data
        if($insured_id){
          $eob_pay = processedEOBPAY($insured_id);
          $eob_payadj = processedEOBPayAdj($insured_id);
          $fee_144 = getFee144($insured_id);

        }
        if($insured_id && $valid_start && $valid_end){
          $fee_139 = getFee139($insured_id, $valid_start, $valid_end);
          $fee_141 = getFee141($insured_id, $valid_start, $valid_end);
          $benefits_ytd = getBenefitsYTD($insured_id, $valid_start, $valid_end);
          $dental_ytd = getDentalYTD($insured_id, $valid_start, $valid_end);
          if($deduct30in == 0 || $deduct30in == null){
            $deductible_ytd = getDeductibleYTD($insured_id, $valid_start, $valid_end);
          } else {
            $deductible_ytd = getDeductibleYTD($insured_id, $valid_start, $valid_end);
            $deductible_ytd += $deduct30in;
          }
          $benefits_ltd = getBenefitsLTD($insured_id, $valid_start, $valid_end);
        }
        if($insured_id && $maternity_start && $maternity_finish){        
            $maternity_ytd = getMaternityYTD($insured_id,$maternity_start,$maternity_finish);
        }else{
            $maternity_ytd = 0;
        }
        // Data Output
        if($benefits_ytd || $dental_ytd || $benefits_ltd || $deductible_ytd || $maternity_ytd){
            if(!$benefits_ytd){$benefits_ytd = 0;}
            if(!$dental_ytd){$dental_ytd = 0;}
            if(!$benefits_ltd){$benefits_ltd = 0;}
            if(!$deductible_ytd){$deductible_ytd = 0;}
            //if(!$maternity_ytd){$maternity_ytd = 0;}
          $data_success = array(
            'success'       => 1,
            'benefits_ytd'  => $benefits_ytd,
            'deductible_ytd'=> $deductible_ytd,
            'dental_ytd'    => $dental_ytd,
            'benefits_ltd'  => $benefits_ltd,
            'maternity_ytd' => $maternity_ytd                       
          );
        } else {
          $data_success['success']  = 0;
        }
        echo json_encode($data_success);  
      break;

      // Added on 29 January 2021
      // Which was discarded by Git
      case 'apply_benefit':
        $is_maternity_claim = trim($_POST['maternity_claim']);
        if(isset($_POST['insured_id'])){
          $insured_id = trim($_POST['insured_id']);
        }
        if(isset($_POST['deductibleInput'])){
          $deductible30 = trim($_POST['deductibleInput']);
        }
        if(!$is_maternity_claim){
          $deductible30days = getDeductible30days($insured_id);
        }

        $applyBenefit = applyBenefit($insured_id);
        if($applyBenefit) {
          $data_success = array(
            'success'   => 1,
            'benefit'   => $applyBenefit
          );
        } else {
          $data_success['success'] = 0;
        }
        echo json_encode($data_success);
      break;

      // Added on 28 January 2021
      case 'insertMaternityCycle':
        $date_start = dateDBFormat(trim($_POST['date_start'])) ;
        $date_finish = dateDBFormat(trim($_POST['date_finish'])) ;
        $maternity_num = trim($_POST['mat_number']);
        $policy_id = trim($_POST['policy_id']);
        $db_data = array(
          'mat_start'     => $date_start,
          'mat_finish'    => $date_finish,
          'maternity_number'=> $maternity_num,
          'id_policy'     => $policy_id
        );

        if($maternity_num && $policy_id) {
          $insertData = saveMaternityCycle($db_data);
        }
        if($insertData){
          $data_success['success'] =1;
        } else {
          $data_success['success'] =0;
        }
        echo json_encode($data_success);
      break;
      
      // Added on 28 January 2021
      case 'getMaternityCycle':
        $maternity_id = trim($_POST['mat_id']);
        $maternityInfo = getMaternityInfobyID($maternity_id);
        //print_r($maternityInfo);
        if($maternityInfo) {
          $data_success = array(
            'success'     => 1,
            'date_start'  => dateFormFormat($maternityInfo['mat_start']),
            'date_finish'  => dateFormFormat($maternityInfo['mat_finish'])
          );
        } else {
          $data_success ['success'] = 0;
        }
        echo json_encode($data_success);
      break;
      // Sohel Sir
      case 'delete_claim':
        // Get the values
        $claim_id = trim($_POST['claim_id']);
         
        if($claim_id){
          $delete_claim = delete_claim($claim_id);
        }
        if($delete_claim){
          $data_success['success']  = 1;
        } else {
          $data_success['success']  = 0;
        }
        echo json_encode($data_success);

      break;
     
      case 'delete_policy_file':
        // Get the values
        $file_id = trim($_POST['file_id']);
         
        if($file_id){
          $delete_policy_file = delete_policy_file($file_id);
        }
        if($delete_policy_file){
          $data_success['success']  = 1;
        } else {
          $data_success['success']  = 0;
        }
        echo json_encode($data_success);

      break;

      /* Added on Server 02 February, 2021 */
      // Added on 30 January 2021
      case 'insertClaimAnalist':
        $analist_name = trim($_POST['claim_analist']);
        if($analist_name != ''){
          $insertAnalist = insertSingleAnalist($analist_name);
        }
        if($insertAnalist){
          $data_success['success'] = 1;
        } else {
          $data_success['success'] = 0;
        }
        echo json_encode($data_success);
      break;
      // Added on 30 January 2021
      case 'insertClaimCause':
        $cause_name = trim($_POST['claim_cause']);
        if($cause_name != ''){
          $insertClaimCause = insertSingleClaimCause($cause_name);
        }
        if($insertClaimCause){
          $data_success['success'] = 1;
        } else {
          $data_success['success'] = 0;
        }
        echo json_encode($data_success);
      break;
      // Added on 30 January 2021
      case 'insertClaimCondition':
        $condition = trim($_POST['claim_condition']);
        if($condition != ''){
          $insertClaimCondition = insertSingleClaimCondition($condition);
        }
        if($insertClaimCondition){
          $data_success['success'] = 1;
        } else {
          $data_success['success'] = 0;
        }
        echo json_encode($data_success);
      break;
      
      // Added on 30 January 2021
      case 'insertClaimLocation':
        $location = trim($_POST['claim_location']);
        if($location != ''){
          $insertClaimLocation = insertSingleClaimLocation($location);
        }
        if($insertClaimLocation){
          $data_success['success'] = 1;
        } else {
          $data_success['success'] = 0;
        }
        echo json_encode($data_success);
      break;
      
      // Added on 30 January 2021
      case 'insertClaimStatus':
        $claimStatus = trim($_POST['claim_status']);
        if($claimStatus != ''){
          $insertClaimStatus = insertSingleClaimStatus($claimStatus);
        }
        if($insertClaimStatus){
          $data_success['success'] = 1;
        } else {
          $data_success['success'] = 0;
        }
        echo json_encode($data_success);
      break;
      
      // Added on 30 January 2021
      case 'insertClaimTreatment':
        $claimTreatment = trim($_POST['claim_treatment']);
        if($claimTreatment != ''){
          $insertClaimTreatment = insertSingleClaimTreatment($claimTreatment);
        }
        if($insertClaimTreatment){
          $data_success['success'] = 1;
        } else {
          $data_success['success'] = 0;
        }
        echo json_encode($data_success);
      break;
        // Added on 30 January 2021
      case 'deleteSingleAnalyst':
        $id = trim($_POST['id']);
        if($id){
          $deleteAnalist = deleteSingleAnalist($id);
        }
        if($deleteAnalist){
          $data_success['success'] = 1;
        } else {
          $data_success['success'] = 0;
        }
        echo json_encode($data_success);
      break;
        // Added on 30 January 2021
      case 'updateSingleAnalyst':
        $id = trim($_POST['id']);
        $name = trim($_POST['name']);
        if($id){
          $updateAnalist = updateSingleAnalist($id, $name);
        }
        if($updateAnalist){
          $data_success['success'] = 1;
        } else {
          $data_success['success'] = 0;
        }
        echo json_encode($data_success);
      break;
        // Added on 30 January 2021
      case 'deleteSingleClaimCause':
        $id = trim($_POST['id']);
        if($id){
          $deleteCause = deleteSingleClaimCause($id);
        }
        if($deleteCause){
          $data_success['success'] = 1;
        } else {
          $data_success['success'] = 0;
        }
        echo json_encode($data_success);
      break;
        // Added on 30 January 2021
      case 'updateSingleClaimCause':
        $id = trim($_POST['id']);
        $name = trim($_POST['name']);
        if($id){
          $updateClaimCause = updateSingleClaimCause($id, $name);
        }
        if($updateClaimCause){
          $data_success['success'] = 1;
        } else {
          $data_success['success'] = 0;
        }
        echo json_encode($data_success);
      break;
        // Added on 30 January 2021
      case 'deleteSingleClaimCondition':
        $id = trim($_POST['id']);
        if($id){
          $deleteCondition = deleteSingleClaimCondition($id);
        }
        if($deleteCondition){
          $data_success['success'] = 1;
        } else {
          $data_success['success'] = 0;
        }
        echo json_encode($data_success);
      break;
        // Added on 30 January 2021
      case 'updateSingleClaimCondition':
        $id = trim($_POST['id']);
        $name = trim($_POST['name']);
        if($id){
          $updateClaimCondition = updateSingleClaimCondition($id, $name);
        }
        if($updateClaimCondition){
          $data_success['success'] = 1;
        } else {
          $data_success['success'] = 0;
        }
        echo json_encode($data_success);
      break;

      case 'deleteSingleClaimLocation':
        $id = trim($_POST['id']);
        if($id){
          $deleteLocation = deleteSingleClaimLocation($id);
        }
        if($deleteLocation){
          $data_success['success'] = 1;
        } else {
          $data_success['success'] = 0;
        }
        echo json_encode($data_success);
      break;
      // Added on 30 January 2021
      case 'updateSingleClaimLocation':
        $id = trim($_POST['id']);
        $name = trim($_POST['name']);
        if($id){
          $updateClaimLocation = updateSingleClaimLocation($id, $name);
        }
        if($updateClaimLocation){
          $data_success['success'] = 1;
        } else {
          $data_success['success'] = 0;
        }
        echo json_encode($data_success);
      break;
      
      // Added on 15 February 2021
      case 'updateSingleClaimLocationStatus':
        $id = trim($_POST['id']);
        $location_status = trim($_POST['location_status']);
        $table_location = trim($_POST['table_location']);
        if($id){
          $updateSingleClaimLocationStatus = updateSingleClaimLocationStatus($id, $location_status, $table_location);
        }
        if(updateSingleClaimLocationStatus){
          $data_success['success'] = 1;
        } else {
          $data_success['success'] = 0;
        }
        echo json_encode($data_success);
      break;
      
      case 'deleteSingleClaimTreatment':
        $id = trim($_POST['id']);
        if($id){
          $deleteTreatment = deleteSingleClaimTreatment($id);
        }
        if($deleteTreatment){
          $data_success['success'] = 1;
        } else {
          $data_success['success'] = 0;
        }
        echo json_encode($data_success);
      break;
      
      // Added on 30 January 2021
      case 'updateSingleClaimTreatment':
        $id = trim($_POST['id']);
        $name = trim($_POST['name']);
        if($id){
          $updateClaimTreatment = updateSingleClaimTreatment($id, $name);
        }
        if($updateClaimTreatment){
          $data_success['success'] = 1;
        } else {
          $data_success['success'] = 0;
        }
        echo json_encode($data_success);
      break;
      
      case 'deleteSingleClaimStatus':
        $id = trim($_POST['id']);
        if($id){
          $deleteStatus = deleteSingleClaimStatus($id);
        }
        if($deleteStatus){
          $data_success['success'] = 1;
        } else {
          $data_success['success'] = 0;
        }
        echo json_encode($data_success);
      break;
      // Added on 30 January 2021
      case 'updateSingleClaimStatus':
        $id = trim($_POST['id']);
        $name = trim($_POST['name']);
        if($id){
          $updateClaimStatus = updateSingleClaimStatus($id, $name);
        }
        if($updateClaimStatus){
          $data_success['success'] = 1;
        } else {
          $data_success['success'] = 0;
        }
        echo json_encode($data_success);
      break;
      /* End of change in server, 02 February 2021*/
      
      case 'update_payment_locked':
        $id = trim($_POST['payment_id']);
        $locked = trim($_POST['locked']);
        if($id){
          $updatePayment = updatePaymentLocked($id,$locked);
        }
        if($updatePayment){
          $data_success['success'] = 1;
        } else {
          $data_success['success'] = 0;
        }
        echo json_encode($data_success);
      break;
      // Added on 11 February 2021
      case 'update_eob_benefit':
        $eob_id = trim($_POST['eob_id']);
        $benefit_id = trim($_POST['benefit_id']);
        $update_eob_benefit = update_eob_benefit($eob_id,$benefit_id);
        if($update_eob_benefit) {
          $data_success['success'] = 1;
        } else {
          $data_success['success'] = 0;
        }
        echo json_encode($data_success);
      break;
      
      // Added on 11 February 2021
      case 'load_diagnosis':
        //$update_eob_benefit = update_eob_benefit($eob_id,$benefit_id);
        $load_diagnosis = load_diagnosis($_REQUEST['term']);
        if($load_diagnosis) {
          //$data_success['success'] = 1;
          $data_success[0]['id'] = '';
          $data_success[0]['text'] = '';
          $data_count = 1;
          foreach($load_diagnosis as $load_d_key => $load_d_val){
            $data_success[$data_count]['id'] = $load_d_val['id'];
            $data_success[$data_count]['text'] = $load_d_val['diagnosis_id'].' - '.$load_d_val['code'].' - '.$load_d_val['short_text'];
            $data_count++;
          }
        } else {
          $data_success['success'] = 0;
        }
        echo json_encode($data_success);
      break;
      
      case 'update_policy_location':
        $policy_id = trim($_POST['policy_id']);
        $location_id = trim($_POST['location_id']);
        $update_policy_location = update_policy_location($policy_id,$location_id);
        if($update_policy_location) {
          $data_success['success'] = 1;
        } else {
          $data_success['success'] = 0;
        }
        echo json_encode($data_success);
      break;
      /** Added on Server on 16 February 2021 **/
      // Added on 03 January 2021
      case 'saveProviderInfo':
        $provider_id = trim($_POST['provider_id']);
        $name = trim($_POST['provider_name']);
        $address_l1 = trim($_POST['address_l1']);
        $address_l2 = trim($_POST['address_l2']);
        $city = trim($_POST['city']);
        $phone = trim($_POST['phone']);
        $zip_code = trim($_POST['zip_code']);
        $email = trim($_POST['email']);
        $state = trim($_POST['provider_state']);
        $note = trim($_POST['notes']);
        $countryID = trim($_POST['id_country']);
        $payTypeID = trim($_POST['id_pay_type']);

        $db_data = array(
          'name'          => $name,
          'address_L1'    => $address_l1,
          'address_L2'    => $address_l2,
          'city'          => $city,
          'state'         => $state,
          'id_country'    => $countryID,
          'zip_code'      => $zip_code,
          'phone'         => $phone,
          'email'         => $email,
          'notes'         => $note,
          'id_pay_type'   => $payTypeID
        );
        if($provider_id){
          $dataSave = updateProviderInfo($db_data, $provider_id);
        } else {
          $dataSave = insertProviderInfo($db_data);
        }
        

        if($dataSave){
          $data_success['success'] = 1;
        } else {
          $data_success['success'] = 0;
        }
        echo json_encode($data_success);
      break;

      // Added on 03 January 2021
      case 'deleteSingleProvider':
        $id = trim($_POST['id']);
        if($id){
          $deleteProvider = deleteSingleProvider($id);
        }
        if($deleteProvider){
          $data_success['success'] = 1;
        } else {
          $data_success['success'] = 0;
        }
        echo json_encode($data_success);
      break;
      // Added on 05 January 2021
      case 'updateAChWireStatus':
        $eob_id = trim($_POST['eob_id']);
        $current_date = date('Y-m-d');
        $data['id'] = $eob_id;
        $data['date_updated'] = $current_date;
        if($eob_id){
          $updateEOB = updateEOBTablePaymentStatusByID($eob_id, $data);
          //$updateEOB = updateEOBStatusbyID($eob_id, $data);
        }
        if($updateEOB){
          $data_success['success'] = 1;
          $data_success['processedDate'] = dateFormFormat($current_date) ;
          $data_success['status'] = 2;
        } else {
          $data_success['success'] = 0;
        }

        echo json_encode($data_success);
      break;
        // Added on 08 January 2021
      case 'updateCheckPrintedStatus':
        $cheque_id = trim($_POST['cheque_id']);
        if($cheque_id){
          // Get EOB Info
          $cheque_info = getChequesInfobyID($cheque_id);
          // Update Info
          $updateCheque = updateChequePrintStatus($cheque_id);
          $updteEOBStatus = updateEOBStatusToReissuedByID($cheque_info[0]['id_eob']);
        }
        if($updateCheque && $updteEOBStatus){
          $data_success['success'] = 1;
        } else {
          $data_success['success'] = 0;
        }
        echo json_encode($data_success);
      break;
           // Added on 16 June 2021
      case 'getCheckNumber':
      $start_date = $_REQUEST['start_date'];

      $getCheckNum = getFreeCheckNumber($start_date);
      
      if($getCheckNum === null){
          $timestamp = strtotime($start_date);
          $php_date = getdate($timestamp);   
          $month = $php_date['mon'];
          $year = $php_date['year'];
          $claria_year = $year;
          //$getLastCheckNum = getLastNumber();
          $getLastCheckNum = getFreeCheckNumberForClariaYear($start_date);
          if($getLastCheckNum['cheque_number'] == ""){
            
              $CheckNum = 10001;
          }else{
              $CheckNum = $getLastCheckNum['cheque_number']+1;
          }
      }else{
          $CheckNum = $getCheckNum['cheque_number'];
          $CheckID = $getCheckNum['id'];
          $claria_year = $getCheckNum['claria_year'];
      }
      if($CheckNum){
        $data_success['success'] = 1;
        $data_success['check_no'] = $CheckNum; //aboni 30-12-21
        if($claria_year){
            $data_success['claria_year'] = $claria_year;
        }
        if($CheckID){
            $data_success['check_id'] = $CheckID;
        }

      } else {
        $data_success['success'] = 0;
      }
      echo json_encode($data_success);

      break;
        
        // Added on 08 January 2021
      case 'deleteACheque':
        $eob_id = trim($_POST['cheque_id']);
        if($eob_id){
          //$deleteCheque = updateEOBPayTypeToNoneByID($eob_id); //keep paytype as it is 31-12-21
          $freeCheque = freeCheckByEOBID($eob_id);
        }
        if($freeCheque){
          $data_success['success'] = 1;
        } else {
          $data_success['success'] = 0;
        }
        echo json_encode($data_success);

        break;
        // Added on 12 January 2021
        case 'getInsuredbyPolicyNumber':
          $policy_id = trim($_POST['policy_number']);
          if($policy_id){
            $insured = getAllInsuredByPolicyID($policy_id);
            //print_r($insured);
          }
          if($insured){
            $success_flg = 1;
            foreach($insured as $inskey => $insval){
              $data_success[$inskey] = array(
                'full_name' => $insval['first_name'].' '.$insval['last_name'],
                'id'        => $insval['id']
              );
            }
          } else {
            $success_flg = 0;
          }

          //echo json_encode($success_flg);
          echo json_encode($data_success);
        break;

          // Added on 12 January 2021
        case 'getProviderInfoEOBForm':
          $provider_id = trim($_POST['provider_id']);
          if($provider_id){
            $providerInfo = getProviderByID($provider_id);
          }
          if($providerInfo){
            $provider_country = get_country_nameby_id($providerInfo['id_country']);
            $providerInfo['country_name'] = $provider_country['country'];
            $data_success['success'] = 1;
            $data_success['providerInfo'] = $providerInfo;
          } else {
            $data_success['success'] = 0;
          }
          echo json_encode($data_success);
        break;
          // Added on 12 January 2021
        case 'save_eob_form':

          

          // Check if EOB ID is there
          if($_REQUEST['eob_id']){
            $eob_id = trim($_POST['eob_id']);
          }

          /** Grabbing EOB Info  **/
          $db_data['id_policy'] = $policy_id = trim($_POST['policy_id']);
          $db_data['id_claims'] = $claim_id = trim($_POST['claim_id']);
          $db_data['eob_number'] = $eob_number = trim($_POST['eob_number']);
          $db_data['id_insured'] = $id_insured = trim($_POST['id_insured']);
          $db_data['id_eob_status'] = $id_eob_status = trim($_POST['id_eob_status']);
          $db_data['id_pay_type'] = $id_eob_pay_type = trim($_POST['id_eob_pay_type']);
          $db_data['id_provider'] = $id_eob_provider = trim($_POST['id_eob_provider']);
          $db_data['date_of_service_start'] = $DOS_start = dateDBFormat(trim($_POST['DOS_start']));
          $db_data['date_of_service_finish'] = $DOS_finish = dateDBFormat(trim($_POST['DOS_finish']));
          
          $db_data['date_effective'] = dateDBFormat($_REQUEST['date_effective']);
        
          $db_data['repricing'] = $repricing = trim($_POST['eob_repricing']);
          $db_data['payable_benefit'] = trim($_POST['payable_benefit']);
          $db_data['patient_responsibility'] = trim($_POST['patient_responsibility']);
          $db_data['deductible_ytd'] = trim($_POST['deductible_ytd']);
          $db_data['co_ins_ytd'] = trim($_POST['co_ins_ytd']);
          $db_data['benefits_ytd'] = trim($_POST['benefits_ytd']);
          $db_data['maternity_td'] = trim($_POST['maternity_td']);
          $db_data['dental_deductible_ytd'] = trim($_POST['dental_deductible_ytd']);
          $db_data['benefits_ltd'] = trim($_POST['benefits_ltd']);
          $is_two_deduct = trim($_POST['is_two_deduct']);
          $has_rider = trim($_POST['has_rider']);
          if($is_two_deduct == 'Yes'){
            $db_data['is_two_deduct'] = 1;
          } else {
            $db_data['is_two_deduct'] = 0;
          }
          if($has_rider == 'Yes'){
            $db_data['has_rider'] = 1;
          } else {
            $db_data['has_rider'] = 0;
          }

          /** Grabbing and generating  Cheque Info  **/
          $is_reissued = trim($_POST['is_reissued']);
          $cheque_data['cheque_number'] = trim($_POST['cheque_number']);
          $cheque_data['cheque_id'] = trim($_POST['cheque_id']);
          $cheque_data['claria_year'] = trim($_POST['claria_year']);
          $cheque_data['date_created']= date("Y-m-d");
          if($is_reissued != 'true'){
            $cheque_data['is_reissued'] = 0;
          } else {
            $cheque_data['is_reissued'] = 1;
            $cheque_data['date_reissued'] = date("Y-m-d");
          }
          if($cheque_data['cheque_number']){
            // Claim Info for cheque
            $claimInfo = getClaimInfoByID($claim_id);
            $main_insured = get_insured_names_by_id($claimInfo['idinsured']);
            $main_insured_full_name = $main_insured['first_name'].' '.$main_insured['last_name'];
            // Provider Info for Cheque
            $providerInfo = getProviderByID($id_eob_provider);
            // DB Cheque Info
            $cheque_data['is_active'] = 1;
            $cheque_data['is_printed'] = 0;
            $cheque_data['id_policy'] = $policy_id;
            $cheque_data['id_claim'] = $claim_id;
            $cheque_data['cheque_from'] = $providerInfo['name'];
            $cheque_data['cheque_to'] = $main_insured_full_name;
          }

          /**  Grabbing New EOB Row Inputs **/
          $eob_data['id_policy'] = $policy_id;
          $eob_data['id_claims'] = $claim_id;
          $eob_data['id_insured'] = $id_insured;
          $eob_data['num_of_service'] = $num_of_service = trim($_POST['eob_numOfService']);
          $eob_data['remarks_code'] = $remarks_code_1 = trim($_POST['eob_remarksCode']);
          $eob_data['remarks_code_2'] = $remarks_code_2 = trim($_POST['eob_remarksCode_2']);
          $eob_data['remarks_code_3'] = $remarks_code_3 = trim($_POST['eob_remarksCode_3']);
          $eob_data['amount_of_bill'] = $amount_of_bill = trim($_POST['eob_amountOfBill']);
          $eob_data['not_payable'] = $not_payable = trim($_POST['eob_notPayable']);
          $eob_data['discount'] = $discount = trim($_POST['eob_discount']);
          $eob_data['deductible'] = $deductible = trim($_POST['eob_deductible']);
          $eob_data['dental_deductible'] = $dental_deductible = trim($_POST['eob_dentalDeductible']);
          $eob_data['co_ins'] = $coIns = trim($_POST['eob_coIns']);
          $eob_data['patient_responsibility'] = $patientsResponsibility = trim($_POST['eob_patientResponsibility']);
          $eob_data['payble_amount'] = $payableAmount = trim($_POST['eob_payableAmount']);
          $eob_data['date_of_service_start'] = $DOS_start;
          $eob_data['date_of_service_finish'] = $DOS_finish;

          /** Saving Benefit Info **/
          $benefit_data['benefits'] = $benefitName = trim($_POST['eob_benefit_name']);
          $benefit_data['ltd'] = $benefitPayable = trim($_POST['eob_benefit_payable_ytd']);
          $benefit_data['lim'] = $benefitLimit = trim($_POST['eob_benefit_limit']);
          $benefit_data['lim_Percent'] = $benefitOverLimit = trim($_POST['eob_benefit_over_limit']);
          if($benefitName){
            $policyInfo = getSinglePolicy($policy_id);
            $policyPlanInfo = getPolicyPlanwithId($policyInfo['idplan']);
            $policyCoverage = getCoveragebyid($policyInfo['idcoverage']);
            $policyDeductibleInfo = getDeductiblebyid($policyInfo['iddeductible']);
            $deductible = str_replace(['$',','], '', $policyDeductibleInfo[0]['deductible']);
            $benefit_data['plan'] = strtoupper($policyPlanInfo[0]['plan']);
            $benefit_data['coverage'] = $policyCoverage[0]['coverage'] ;
            $benefit_data['deductible'] = $deductible;
            $benefit_data['maternity'] = 0;
            // Saving Benefit Information
            $saveBenefit = insertNewBenefit($benefit_data);
            if($saveBenefit){
              $new_benefit_info = getLastBenefitID();
              $new_benefit_id = $new_benefit_info['id'];
              if($new_benefit_id){
                $eob_data['id_benefit'] =  $new_benefit_id;
              }
            }
          }
          // Insert or Update EOB Table
          if($eob_id){
            $db_data['date_updated']= date("Y-m-d");
            $updateEOBForm = updateEOBFormData($eob_id, $db_data);
          } else {
            $db_data['date_created']= date("Y-m-d");
            $saveEOBForm = saveEOBFormData($db_data);
            if($saveEOBForm){
              $newEOB = getLastEOBID();
              $eob_data['id_eob_table'] = $newEOB['id'];
            }
          }
          // Saving Cheque Information
          if($cheque_data){
            if($eob_id){
              $checkInfo = getChequesInfobyPolicyandEOBID($policy_id, $eob_id);
              if($checkInfo['id']){
                  
              }else{
                 $cheque_data['id_eob'] = $eob_id;
                 $saveChequeInfo = saveChequeInfo($cheque_data); 
              }
              
            } else {
              $cheque_data['id_eob'] = $newEOB['id'];
              $saveChequeInfo = saveChequeInfo($cheque_data);
            }
          }

          // Insert or Updating EOB Detail
          /*if($eob_data && is_array($eob_data)){
            if($eob_id){
              $eob_data['id_eob_table'] = $eob_id;
              $updateEOBRow = updateEOBDetailByEOBTableID($eob_id, $eob_data);
            } else {
              $eob_data['id_eob_table'] = $newEOB['id'];
              $insertEOBRow = insertEOBRow($eob_data);
            }
          } aboni comment*/

          if($saveEOBForm || $updateEOBForm){
            if($saveEOBForm){
              $data_success['eob_id'] = $newEOB['id'];
            }
            if($updateEOBForm){
              $data_success['eob_id'] = $eob_id;
            }
            $data_success["success"] = 1;
            $data_success["policy_id"] = $policy_id;
          } else {
            $data_success["success"] = 0;
            $data_success["policy_id"] = $policy_id;
            if($eob_id){
              $data_success["eob_id"] = $eob_id;
            } else {
              $data_success['eob_id'] = $newEOB['id'];
            }
          }

          echo json_encode($data_success);
        break;

        case 'updateChequeToVoid':
          $chequeNumber = trim($_POST['cheque_number']);
          $eob_id = trim($_POST['eob_id']);
          $updateChequeStatus = setChequeStatustoVoid($chequeNumber);
          if($eob_id){
            $updateEOB = updateEOBStatusToVoidByID($eob_id);
          }
          if($updateChequeStatus){
            $data_success['success'] = 1;
          } else {
            $data_success['success'] = 0;
          }
          echo json_encode($data_success);
        break;

        case 'processNewEOB':
          $policy_id = trim($_POST['policy_id']);
          $claim_id = trim($_POST['claim_id']);

          $policyInfo = getSinglePolicy($policy_id);
          $claimInfo = getClaimInfoByID($claim_id);
          // Generate New EOB Number
          $lastEOBID = getLastEOBID(); 
          $eob_number = $claimInfo['clnum'].'EOB'.($lastEOBID['id']+1);
          $data = array(
            'id_policy'     => $policy_id,
            'id_claims'     => $claim_id,
            'eob_number'    => $eob_number
          );
          if($policy_id && $claim_id){
            $newEOB = generateNewEOB($data, '', $policy_id , $claim_id);
          }
          if($newEOB){
            $data_success['data_success'] = 1;
          } else {
            $data_success['data_success'] = 0;
          }
          echo json_encode($data_success);
        break;

        case 'calculateEOB':
         
          $claim_id = trim($_POST['claim_id']);
          $policy_id = trim($_POST['policy_id']);
          $eob_id = trim($_POST['eob_id']);
          //$patient_responsibility = trim($_POST['eob_patientResponsibility']);
          //$payable_amount = trim($_POST['eob_payableAmount']);
          //$co_ins = trim($_POST['eob_coIns']);
          //$dental_deductible = trim($_POST['eob_dentalDeductible']);
          //$amount_of_bill = trim($_POST['eob_amountOfBill']);
          //$not_payable = trim($_POST['eob_notPayable']);
          //$eob_deductible = trim($_POST['eob_deductible']);

          //$payable_benefit = $amount_of_bill - $not_payable - $patient_responsibility;

          $claimInfo = getClaimInfoByID($claim_id);
        
          $insured_id = $claimInfo['idclaimant'];
          $valid_start = dateDBFormat($claimInfo['valid_start']) ;
          $valid_end = dateDBFormat($claimInfo['valid_end']);
          
          $get_maternity_details = get_maternity_details($claimInfo['maternity_id']);
        
          if($get_maternity_details[0]['id']>0){
              $maternity_start = dateDBFormat($get_maternity_details[0]['mat_start']);
              $maternity_finish = dateDBFormat($get_maternity_details[0]['mat_finish']);
          }else{
              $maternity_ytd = 0;
          }
         
        
        
          // Processing of the data
          if($insured_id){
            $eob_pay = processedEOBPAY($insured_id);
            $eob_payadj = processedEOBPayAdj($insured_id);
            $fee_144 = getFee144($insured_id);

          }
          if($claim_id){
            $payable_benefit = getPayableBenefit($claim_id,$eob_id);
            $patient_responsibility = getPatientResponsability($claim_id,$eob_id);
            $co_ins = getCoinsYTD($claim_id, $valid_start, $valid_end,$eob_id);
          }
          
          if($insured_id && $valid_start && $valid_end){
            $fee_139 = getFee139($insured_id, $valid_start, $valid_end);
            $fee_141 = getFee141($insured_id, $valid_start, $valid_end);
            $benefits_ytd = getBenefitsYTD($insured_id, $valid_start, $valid_end);
            $dental_ytd = getDentalYTD($insured_id, $valid_start, $valid_end);
          
            $deductible_ytd = getDeductibleYTD($insured_id, $valid_start, $valid_end);
            
            $benefits_ltd = getBenefitsLTD($insured_id, $valid_start, $valid_end);
          }
          if($insured_id && $maternity_start && $maternity_finish){
            $maternity_ytd = getMaternityYTD($insured_id, $maternity_start, $maternity_finish, $valid_start, $valid_end);
          }
        
          // Data Output
       
            if(!$benefits_ytd){$benefits_ytd = 0;}
            if(!$dental_ytd){$dental_ytd = 0;}
            if(!$benefits_ltd){$benefits_ltd = 0;}
            if(!$deductible_ytd){$deductible_ytd = 0;}
            if(!$maternity_ytd){$maternity_ytd = 0;}
            
          $rider_info = getRiderbyInsuredId($insured_id);
          
          if($deductible_ytd != '' && $eob_deductible != ''){
            $two_deduct = 1;
          } else {
            $two_deduct = 0;
          }
          if(is_array($rider_info)){
            $has_rider = 1;
          } else {
            $has_rider = 0;
          }
          $data = array(
            'patient_responsibility'  => $patient_responsibility,
            'payable_amount'          => $payable_amount,
            'co_ins'                  => $co_ins,
            'dental_deductible'       => $dental_ytd,
            'amount_of_bill'          => $amount_of_bill,
            'payable_benefit'         => $payable_benefit,
            'ben_ytd'                 => $benefits_ytd,
            'ben_ltd'                 => $benefits_ltd,
            'deductible_ytd'          => $deductible_ytd,
            'mat_ytd'                 => $maternity_ytd,
            'two_deduct'              => $two_deduct,
            'has_rider'               => $has_rider,
              'cald'               => $cald 
          );
    
          echo json_encode($data);
        break;
        // 16 February 2021
        case 'eobRemarksDetail':
          $remarks_code_1 = trim($_POST['remarks_code_1']);
          $remarks_code_2 = trim($_POST['remarks_code_2']);
          $remarks_code_3 = trim($_POST['remarks_code_3']);
          if($remarks_code_1 || $remarks_code_2 || $remarks_code_3){
            if($remarks_code_1){
              $remarks_detail_1 = getRemarksCodeByCode($remarks_code_1);
              $data['remarks_detail_1_code'] = $remarks_detail_1['code'];
              $data['remarks_detail_1_content'] = $remarks_detail_1['content'];
            }
            if($remarks_code_2){
              $remarks_detail_2 = getRemarksCodeByCode($remarks_code_2);
              $data['remarks_detail_2_code'] = $remarks_detail_2['code'];
              $data['remarks_detail_2_content'] = $remarks_detail_2['content'];
            }
            if($remarks_code_3){
              $remarks_detail_3 = getRemarksCodeByCode($remarks_code_3);
              $data['remarks_detail_3_code'] = $remarks_detail_3['code'];
              $data['remarks_detail_3_content'] = $remarks_detail_3['content'];
            }
          }
          if($data && is_array($data)){
            echo json_encode($data);
          } else {
            $data['success'] = 0;
          }
          
        break;

        case 'delete_eob':
        // Get the values
        $eob_id = trim($_POST['eob_id']);
         
        if($eob_id){
          $delete_eob = delete_eob($eob_id);
        }
        if($delete_eob){
          $data_success['success']  = 1;
        } else {
          $data_success['success']  = 0;
        }
        echo json_encode($data_success);

      break;
      /** End of Add on Server on 16 February 2021**/

      case 'update_policy_file':
        // Get the values
        $file_id = trim($_POST['file_id']);
        $description = trim($_POST['description']);
         
        if($file_id){
          $update_policy_description = update_policy_description($file_id, $description);
        }
        if($update_policy_description){
          $data_success['success']  = 1;
        } else {
          $data_success['success']  = 0;
        }
        echo json_encode($data_success);

      break;

      case 'processQueueEOB':
        $claim_id = $_POST['claim_id'];
        $eob_detail_id = $_POST['eob_detail_id'];
        $provider_id = $_POST['provider_id'];
        // Claim and Policy Info
        $providerInfo = getProviderByID($provider_id);
        $claimInfo = getClaimInfoByID($claim_id);
        $policy_info = getSinglePolicy($claimInfo['idpolicy']);
        if($providerInfo['id_pay_type']){
          $id_pay_type = $providerInfo['id_pay_type'];
        }else{
          $id_pay_type = 0;
        }
        //
        $lastEOBID = getLastEOBID();
        if($lastEOBID){
          $eob_number = $claimInfo['clnum'].'EOB'.($lastEOBID['id']+1);
        } else {
          $eob_number = $claimInfo['clnum'].'EOB'.'01';
        }
        
          if($eob_number){
            $data = array(
              'eob_number'    => $eob_number,
              'id_claims'     => $claim_id,
              'id_policy'     => $claimInfo['idpolicy'],
              'id_insured'    => $claimInfo['idinsured'],
              'id_pay_type'   => $id_pay_type,
              'id_eob_detail' => $eob_detail_id,
              'id_provider'   => $provider_id,
              'date_created'  => date('Y-m-d'),
              'date_of_service_start' => date('Y-m-d')
            );
            $generatedEOB = generateNewEOB($data, '', $claimInfo['idpolicy'], $claim_id);
          }
          if($generatedEOB){
            $newEOB = getLastEOBID();
            $eob_detail_data = array(
              'id_eob_table'    => $newEOB['id']
            );
            if($provider_id){
              $eob_detail_data['foreign_provide'] = 1;
              $eob_detail_data['id_insured'] = $claimInfo['idinsured'];
            }
            $updateEOBDetail = updateEOBDetailbyID($eob_detail_id, $eob_detail_data);
            if($updateEOBDetail) {
            } else {
              print("Could not be updated");
            }
            $data_success['data_success'] = 1;
            $data_success['eob_number'] = $eob_number;
            $data_success['policy_number']  = $policy_info['policynumber'];
          } else {
            $data_success['data_success'] = 0;
          }
          echo json_encode($data_success);
      break;

      case 'getDiagnosisText':
        $diagnosis_id = trim($_POST['id']);
        $diagnosis_info = getDiagnosisInfoByID($diagnosis_id);
        //print_r($diagnosis_info);
        if($diagnosis_info){
          echo json_encode($diagnosis_info);
        }
      break;

      case 'update_eob_provider':
        $eob_id = trim($_REQUEST['eob_id']);
        if($eob_id){
          $eobInfo = getEOBDetailbyID($eob_id);
          if($eobInfo['foreign_provide'] == 0){
            $newVal = 1;
          } elseif($eobInfo['foreign_provide'] == 1){
            $newVal = 0;
          }
          $updateEOB = updateEOBDetailProviderByID($eob_id, $newVal);

          if($updateEOB){
            $data_success['success'] = 1;
            $data_success['eob_id'] = $eob_id;
          } else {
            $data_success['success'] = 0;
          }

          echo json_encode($data_success);
        }
      break;

        // Added on 05 March 2021 {Biplob}
      case 'save_renewal_form':
        
        
        $checkPermission = checkUserAccessRole('Policies');
        if(!$checkPermission){
            $data_sucess = array("sucess"=>0,"pr"=>1,"message"=>"Permission error");
            echo json_encode($data_sucess);
            break;
        }
        
      
        $renewal_number     = $db_data['id']                  = trim($_POST['renewal_number']);
        $id_policy          = $db_data['id_policy']           = trim($_POST['policy_id']);
        $policynumber       = $db_data['policynumber']        = trim($_POST['policynumber']);
        $renewal_status     = $db_data['id_status']           = trim($_POST['renewal_status']);
        $id_deductible      = $db_data['id_deductible']       = trim($_POST['id_deductible']);
        $id_coverage        = $db_data['id_coverage']         = $_POST['id_coverage'];
        $date_expire        = $db_data['date_expire']         = $_POST['date_expire'];
        $id_pay_cycle       = $db_data['id_pay_cycle']        = trim($_POST['id_pay_cycle']);
        
        $main_insured       = $db_data['main_insured']        = trim($_POST['main_insured']);
        $addressl1          = $db_data['addressl1']           = trim($_POST['addressl1']);
        $addressl2          = $db_data['addressl2']           = trim($_POST['addressl2']);
        $city               = $db_data['city']                = trim($_POST['city']);
        $country            = $db_data['country']             = trim($_POST['country_id']);
        $agentl1            = $db_data['agentl1']             = trim($_POST['agent_level1']);
        $agentl2            = $db_data['agentl2']             = trim($_POST['agent_level2']);
        $agentl3            = $db_data['agentl3']             = trim($_POST['agent_level3']);
        $agentl4            = $db_data['agentl4']             = trim($_POST['agent_level4']);
        $rate_year          = $db_data['rate_year']           = trim($_POST['rate_year']);
        $fronting_name      = $db_data['fronting_name']       = trim($_POST['fronting']);
        $service_center     = $db_data['service_center']      = trim($_POST['service_center']);
        $is_dominicana      = $db_data['is_dominicana']       = trim($_POST['dominicana'])? 1: 0;
        $premium_zone       = trim($_POST['premium_zone']);

        if($premium_zone == 'srilank'){
          $sri             = $db_data['sri']            = 1;
          $mexico          = $db_data['is_mexcio']   = 0;
          $world          = $db_data['world']   = 0;
        }elseif($premium_zone == 'mexico'){
          $sri             = $db_data['sri']            = 0;
          $mexico          = $db_data['is_mexcio']   = 1;
          $world          = $db_data['world']   = 0;
        }else{
          $sri             = $db_data['sri']            = 0;
          $mexico          = $db_data['is_mexcio']   = 0;
          $world          = $db_data['world']   = 1;
        }
        $date_effective     = $db_data['date_effective']      = trim($_POST['date_effective']);
        $policy_fee         = $db_data['policy_fee']          = trim($_POST['policy_fee'],'$');
        $spanish            = $db_data['is_spanish']          = trim($_POST['spanish'])? 1: 0;;
        $add                = $db_data['is_ADD']              = trim($_POST['add'])? 1: 0;;
        $interim            = $db_data['is_interim']          = trim($_POST['interim'])? 1: 0;;
        $amount_annual      = $db_data['amount_annual']       = trim($_POST['amount_annual'],'$');
        $amount_semi_annual = $db_data['amount_semi_annual']  = trim($_POST['amount_semi_annual'],'$');
        $amount_quarterly   = $db_data['amount_quarterly']    = trim($_POST['amount_quarterly'],'$');
        $amount_monthly     = $db_data['amount_monthly']      = trim($_POST['amount_monthly'],'$');

        $renewal_edit       =  trim($_POST['renewal_edit']);

        
        if($renewal_number !='' ){
            $db_data['date_updated']  = date("Y-m-d"); 
            $updRenewal = updateRenewalInfo($renewal_number,$db_data);
            $data_sucess['add'] = 0; 
        }else{
            $db_data['date_created']   = date("Y-m-d");
            $updRenewal = addRenewalInfo($id_policy,$db_data);
            $data_sucess['add'] = 1;
        }
  
        if($updRenewal){  
        
          $data_sucess['sucess'] = 1;
        
        }else{
           $data_sucess['sucess'] = 0;  
        }
        
        echo json_encode($data_sucess);
        break; 

    // Added on 05 March 2021 {Biplob}
      case 'delete_renewal': 
        
        $renewal_id = trim($_POST['renewal_id']);
        
        if($renewal_id){         
            $renewalDelete = DeleteSingleRenewal($renewal_id);            
            $data_sucess['sucess'] = 1;
        }else{
           $data_sucess['sucess'] = 0; 
        }
        
        echo json_encode($data_sucess);
      break;

       // Added on 08 March 2021 {Biplob}
      case 'changeRenewalStatus': 
        
        $renewal_id = trim($_POST['renewal_id']);
        $status_id = trim($_POST['status_id']);
        
        if($renewal_id){     
            $renenalUpdate = UpdateRenewalStatus($renewal_id, $status_id);    
        }
        
        if($renenalUpdate){
          $data_sucess['sucess'] = 1;
        }else{
           $data_sucess['sucess'] = 0; 
        }

        echo json_encode($data_sucess);
      break;



      // 19 March 2021
      case 'saveSingleEOB':

      
        // Grabbing Data
        $policy_id = trim($_REQUEST['policy_id']);
        $claim_id = trim($_REQUEST['claim_id']);
        $insured_id = trim($_REQUEST['insured_id']);
        $main_insured_id = trim($_REQUEST['main_insured_id']);
        $eob_id = trim($_REQUEST['eob_id']);
        $eob_row_id = trim($_REQUEST['eob_row_id']);
        $eob_row_id_fp = trim($_REQUEST['eob_row_id_fp']);
        $eob_detail_id = trim($_REQUEST['eob_detail_id']);
        // Grabbing EOB Detail Info
        $num_of_service = trim($_REQUEST['num_of_service']);
        $remarks_code = trim($_REQUEST['remarks_code']);
        $remarks_code_2 = trim($_REQUEST['remarks_code_2']);
        $remarks_code_3 = trim($_REQUEST['remarks_code_3']);
        $date_of_service_start = trim($_REQUEST['date_of_service_start']);
        $date_of_service_finish = trim($_REQUEST['date_of_service_finish']);
        $symbol = trim($_REQUEST['symbol']);
        $foreign_currency = trim($_REQUEST['foreign_currency']);
        $amount_of_bill = trim($_REQUEST['amount_of_bill']);
        $not_payable = trim($_REQUEST['not_payable']);
        $discount = trim($_REQUEST['discount']);
        $deductible = trim($_REQUEST['deductible']);
        $dental_deductible = trim($_REQUEST['dental_deductible']);
        $co_ins = trim($_REQUEST['co_ins']);
        $eob_patientRes = trim($_REQUEST['eob_patientRes']);
        $payable_amount = trim($_REQUEST['payable_amount']);
        $foreignProvider = trim($_REQUEST['foreignProvider']);
        $benefit_id = trim($_REQUEST['benefit_id']);
        
        if($claim_id == '' || $claim_id == null){
          $claim_id = getLastClaimID();
          $claim_id = $claim_id['id'];
        }

        $db_data = array(
          'id_policy' => $policy_id,
          'id_claims' => $claim_id,
          'id_insured'  => $insured_id,
          'id_eob_table'  => $eob_id,
          'num_of_service' => $num_of_service,
          'remarks_code'  => $remarks_code,
          'remarks_code_2'  => $remarks_code_2,
          'remarks_code_3'  => $remarks_code_3,
          'date_of_service_start' => dateDBFormat($date_of_service_start),
          'date_of_service_finish' => dateDBFormat($date_of_service_finish),
          'symbol'  => $symbol,
          'foreign_currency'  => $foreign_currency,
          'amount_of_bill'  => $amount_of_bill,
          'not_payable'   => $not_payable,
          'discount'      => $discount,
          'deductible'    => $deductible,
          'dental_deductible' => $dental_deductible,
          'co_ins'        => $co_ins,
          'payble_amount' => $payable_amount,
          'patient_responsibility' => $eob_patientRes,
          'foreign_provide' => $foreignProvider,
          'id_benefit'  => $benefit_id
        );

        if($eob_detail_id){
          $updateEOBdata = updateEOBDetailbyID($eob_detail_id, $db_data);
        } else {
          $db_data['date_created'] = date('Y-m-d');
          $insertEOBdata = insertNewEOB($db_data);
          if($insertEOBdata){
            $new_eob_detail = getLastEOBDetailID();
            $eob_detail_id = $new_eob_detail['id'];
          }
          
        }
        

        if($claim_id){
          $claim_info = getClaimInfoByID($claim_id);
          if($claim_info['idstatusclaim'] == 2 || $claim_info['idstatusclaim'] == 3){
            if($foreignProvider == 1 ){ 
              $lastEOBID = getLastEOBID();

             

              if($lastEOBID){
                $eob_number = $claim_info['clnum'].'EOB'.($lastEOBID['id']+1);
              } else {
                $eob_number = $claim_info['clnum'].'EOB'.'01';
              }

              
              
              $provider_id = getSingleProviderByInsured($main_insured_id);
              if(!$provider_id){
                $policy_id = getPolicyIDByInsured($main_insured_id);
                if($policy_id){
                    $policy_info = getSinglePolicy($policy_id);
                }
                $name = getHealthPrimaryInsuredText($policy_id);
                $provider_db_data = array(
                  'name'        => $name,
                  'insured_id'  => $main_insured_id,
                  'address_L1'  => $policy_info['addressl1'],
                  'address_L2'  => $policy_info['addressl2'],
                  'city'        => $policy_info['city'],
                  'state'       => '',
                  'id_country'  => $policy_info['idcountry'],
                  'zip_code'    => '',
                  'phone'       => $policy_info['phone'],
                  'email'       => $policy_info['email'],
                  'notes'       => ''
                );
                $dataSave = insertProviderInfo($provider_db_data);
                if($dataSave){
                    $provider_id = getSingleProviderByInsured($main_insured_id);
                }
              }
               

              if($eob_number){
                $eob_table_data = array(
                  'eob_number'    => $eob_number,
                  'id_claims'     => $claim_id,
                  'id_policy'     => $policy_id,
                  'id_insured'    => $main_insured_id,
                  'id_eob_detail' => $eob_detail_id,
                  'id_provider' => $provider_id,
                  'date_created'  => date('Y-m-d'),
                  'date_of_service_start' => dateDBFormat($date_of_service_start),
                  'date_of_service_finish'  => dateDBFormat($date_of_service_finish)
                );
                $generatedEOB = generateNewEOB($eob_table_data,$eob_row_id_fp,$policy_id,$claim_id);
                if($generatedEOB){
                  $newEOB = getLastEOBID();
                 
                  $data_success['eob_id'] = $newEOB['id'];
                  $eob_detail_data = array(
                    'id_eob_table'    => $newEOB['id']
                  );
                  //$updateEOBDetail = updateEOBDetailbyID($eob_detail_id, $eob_detail_data);
                }
              }



            }
            
          }
        }

        if($updateEOBdata || $insertEOBdata){
          $data_success['success'] = 1;
          if($updateEOBdata){
            $data_success['process'] = 'Updated';
          } else {
            $data_success['process'] = 'Inserted';
          }
        } else {
          $data_success['success'] = 0;
        }
        echo json_encode($data_success);
      break;


      case 'saveMultipleEOB': //added by abooni 23-11-21

       

      
        // Grabbing Data
        $policy_id = trim($_REQUEST['policy_id']);
        $claim_id = trim($_REQUEST['claim_id']);
        $insured_id = trim($_REQUEST['insured_id']);
        $main_insured_id = trim($_REQUEST['main_insured_id']);
        $eob_id = trim($_REQUEST['eob_id']);
        $eob_row_id = trim($_REQUEST['eob_row_id']);
        $eob_row_id_fp = trim($_REQUEST['eob_row_id_fp']);
        $eob_detail_id = trim($_REQUEST['eob_detail_id']);
        // Grabbing EOB Detail Info
        $num_of_service = trim($_REQUEST['num_of_service']);
        $remarks_code = trim($_REQUEST['remarks_code']);
        $remarks_code_2 = trim($_REQUEST['remarks_code_2']);
        $remarks_code_3 = trim($_REQUEST['remarks_code_3']);
        $date_of_service_start = trim($_REQUEST['date_of_service_start']);
        $date_of_service_finish = trim($_REQUEST['date_of_service_finish']);
        $symbol = trim($_REQUEST['symbol']);
        $foreign_currency = trim($_REQUEST['foreign_currency']);
        $amount_of_bill = trim($_REQUEST['amount_of_bill']);
        $not_payable = trim($_REQUEST['not_payable']);
        $discount = trim($_REQUEST['discount']);
        $deductible = trim($_REQUEST['deductible']);
        $dental_deductible = trim($_REQUEST['dental_deductible']);
        $co_ins = trim($_REQUEST['co_ins']);
        $eob_patientRes = trim($_REQUEST['eob_patientRes']);
        $payable_amount = trim($_REQUEST['payable_amount']);
        $foreignProvider = trim($_REQUEST['foreignProvider']);
        $benefit_id = trim($_REQUEST['benefit_id']);
        $provider_id = trim($_REQUEST['provider_id']);

        
        if($claim_id == '' || $claim_id == null){
          $claim_id = getLastClaimID();
          $claim_id = $claim_id['id'];
        }

        /** added in 3-12-21*/
        if($provider_id ==""){
          $provider_id = getSingleProviderByInsured($main_insured_id);
          if(!$provider_id){
            $policy_id = getPolicyIDByInsured($main_insured_id);
            if($policy_id){
                $policy_info = getSinglePolicy($policy_id);
            }
            $name = getHealthPrimaryInsuredText($policy_id);
            $provider_db_data = array(
              'name'        => $name,
              'insured_id'  => $main_insured_id,
              'address_L1'  => $policy_info['addressl1'],
              'address_L2'  => $policy_info['addressl2'],
              'city'        => $policy_info['city'],
              'state'       => '',
              'id_country'  => $policy_info['idcountry'],
              'zip_code'    => '',
              'phone'       => $policy_info['phone'],
              'email'       => $policy_info['email'],
              'notes'       => ''
            );
            $dataSave = insertProviderInfo($provider_db_data);
            if($dataSave){
                $provider_id = getSingleProviderByInsured($main_insured_id);
            }
          }
        }        

        /*****/

        

        if($benefit_id && $insured_id){
          $db_data = array(
            'id_policy' => $policy_id,
            'id_claims' => $claim_id,
            'id_insured'  => $insured_id,
            'id_eob_table'  => $eob_id,
            'num_of_service' => $num_of_service,
            'remarks_code'  => $remarks_code,
            'remarks_code_2'  => $remarks_code_2,
            'remarks_code_3'  => $remarks_code_3,
            'date_of_service_start' => dateDBFormat($date_of_service_start),
            'date_of_service_finish' => dateDBFormat($date_of_service_finish),
            'symbol'  => $symbol,
            'foreign_currency'  => $foreign_currency,
            'amount_of_bill'  => $amount_of_bill,
            'not_payable'   => $not_payable,
            'discount'      => $discount,
            'deductible'    => $deductible,
            'dental_deductible' => $dental_deductible,
            'co_ins'        => $co_ins,
            'payble_amount' => $payable_amount,
            'patient_responsibility' => $eob_patientRes,
            'foreign_provide' => $foreignProvider,
            'id_benefit'  => $benefit_id,
            'id_provider'  => $provider_id,
          );

        }else{
          $db_data = array(
            'id_policy' => $policy_id,
            'id_claims' => $claim_id,
            'id_eob_table'  => $eob_id,
            'num_of_service' => $num_of_service,
            'remarks_code'  => $remarks_code,
            'remarks_code_2'  => $remarks_code_2,
            'remarks_code_3'  => $remarks_code_3,
            'date_of_service_start' => dateDBFormat($date_of_service_start),
            'date_of_service_finish' => dateDBFormat($date_of_service_finish),
            'symbol'  => $symbol,
            'foreign_currency'  => $foreign_currency,
            'amount_of_bill'  => $amount_of_bill,
            'not_payable'   => $not_payable,
            'discount'      => $discount,
            'deductible'    => $deductible,
            'dental_deductible' => $dental_deductible,
            'co_ins'        => $co_ins,
            'payble_amount' => $payable_amount,
            'patient_responsibility' => $eob_patientRes,
            'foreign_provide' => $foreignProvider,
            'id_provider'  => $provider_id,
          );

        }
        

        if($eob_detail_id){
          $updateEOBdata = updateEOBDetailbyID($eob_detail_id, $db_data);
        } else {
          $db_data['date_created'] = date('Y-m-d');
          $insertEOBdata = insertNewEOB($db_data);
          if($insertEOBdata){
            $new_eob_detail = getLastEOBDetailID();
            $eob_detail_id = $new_eob_detail['id'];
          }
          
        }
        $return_array=array();
        $return_array['eob_detail_id'] = $eob_detail_id;
        $return_array['provider_id'] = $provider_id;
        $return_array['foreign_provide'] = $foreignProvider;
        $return_array['eob_id'] = $eob_id;
        $return_array['request'] = $_REQUEST; ////

        echo json_encode($return_array);
        

      break;
      case 'generateMultipleEOB': //added by aboni 23-11-21

        if(isset($_REQUEST['eob_id']) ){ //for add_eob from eob
            if($_REQUEST['eob_id'] ==""){
              $eob_id = getLastEOBID();
              $eob_id = $eob_id['id'];
              $eob_detail_id_arr=$_REQUEST["eob_detail_id"];
              $eob_detail_id = $_REQUEST['eob_detail_id'][0];
             
              $eob_details = eobDetailsById($eob_detail_id);
              $claim_id = $eob_details[0]['id_claims'];
              $claim_info = getClaimInfoByID($claim_id);

              $eob_number = $claim_info['clnum'].'EOB'.($eob_id);
              $data_success['eob_id'] =$eob_id;
              $data_success['eob_detail_ids'] = $eob_detail_id_arr;

              foreach ($eob_detail_id_arr as $eob_detail_id) {     
                $updateEOBDetail = trz_update_all_eob_details($eob_detail_id,$eob_id);
                $data_success[$eob_detail_id] = $eob_id;            
              }              
              $policy_id = $eob_details[0]['id_policy'];
              $data_success['data_success'] = 1;
              $data_success['eob_number'] = $eob_number;
              $data_success['policy_id'] = $policy_id;
             

            
            echo json_encode($data_success);
            break;
          }
        } 

       if(isset($_REQUEST['eob_id']) ){ //for edit_eob from eob
        if($_REQUEST['eob_id'] !=""){
          $eob_id =$_REQUEST['eob_id'];
        }       
       }

        $eob_detail_id_arr=$_REQUEST["eob_detail_id"];
        $eob_detail_id = $_REQUEST['eob_detail_id'][0];
        $provider_id = $_REQUEST['provider_id'];

        $eob_details = eobDetailsById($eob_detail_id);
        $claim_id = $eob_details[0]['id_claims'];
        $policy_id = $eob_details[0]['id_policy'];
        $main_insured_id = $eob_details[0]['id_insured'];

        $claim_info = getClaimInfoByID($claim_id);
        $providerInfo = getProviderByID($provider_id);
        $policy_info = getSinglePolicy($claimInfo['idpolicy']);

        if($providerInfo['id_pay_type']){
          $id_pay_type = $providerInfo['id_pay_type'];
        }else{
          $id_pay_type = 0;
        }
        
        $flg=0;

        if(isset($_REQUEST['eob_id']) ) { //for edit_eob from eob
          if($_REQUEST['eob_id'] !=""){
            //update eob_id
            $eob_number = $claim_info['clnum'].'EOB'.($eob_id);
            $data_success['eob_id'] =$eob_id;
                $data_success['eob_detail_ids'] = $eob_detail_id_arr;

                foreach ($eob_detail_id_arr as $eob_detail_id) {     
                  $updateEOBDetail = trz_update_all_eob_details($eob_detail_id,$eob_id);
                  $data_success[$eob_detail_id] = $eob_id;            
                }
                
                
                $data_success['data_success'] = 1;
                $data_success['eob_number'] = $eob_number;
                $data_success['policy_id'] = $policy_id;
                

          }else{
            $flg=1; 
          }
          

        }
        else{
          $flg=1;
        }

        if($flg==1){  //for create eob from claim approve

          if($claim_info['idstatusclaim'] == 2 || $claim_info['idstatusclaim'] == 3){

            $lastEOBID = getLastEOBID(); 

            if($lastEOBID){
              $eob_number = $claim_info['clnum'].'EOB'.($lastEOBID['id']+1);
            } else {
              $eob_number = $claim_info['clnum'].'EOB'.'01';
            }


            if($eob_number){
              $data = array(
                'eob_number'    => $eob_number,
                'id_claims'     => $claim_id,
                'id_policy'     => $policy_id,
                'id_insured'    => $main_insured_id,
                'id_pay_type'   => $id_pay_type, 
                'id_eob_detail' => $eob_detail_id,
                'id_provider'   => $provider_id,
                'date_created'  => date('Y-m-d'),
                'date_of_service_start'  => $claim_info['valid_start'],
                'date_of_service_finish' => $claim_info['valid_end'],
                'id_eob_status' => 3 //status pending 20-12-21
              );
    
              $generatedEOB = trz_generate_New_EOB($data);
            }
            $data_success['generated_eob'] = $generatedEOB;
            
            if($generatedEOB){

              $data_success['eob_id'] = $generatedEOB;
              $data_success['eob_detail_ids'] = $eob_detail_id_arr;

              foreach ($eob_detail_id_arr as $eob_detail_id) {     
                $updateEOBDetail = trz_update_all_eob_details($eob_detail_id,$generatedEOB);
                $data_success[$eob_detail_id] = $generatedEOB;            
              }
              
              
              $data_success['data_success'] = 1;
              $data_success['eob_number'] = $eob_number;    
              $data_success['policy_id'] = $policy_id;          
            } 
            else {
              $data_success['data_success'] = 0;
              
            }
          }else {
            $data_success['success'] = 0;
          }
        }

        echo json_encode($data_success);
        

      break;

     
      // Added on 1 April 2021
      case 'load_policies_by_name':
       function unique_multidim_array($array, $key) {
            $temp_array = array();
            $i = 0;
            $key_array = array();
        
            foreach ($array as $val) {
                if (!in_array($val[$key], $key_array)) {
                    $key_array[$i] = $val[$key];
                    $temp_array[$i] = $val;
                }
                $i++;
            }
            return $temp_array;
        }
        $load_insured_by_name = load_insured_by_name($_REQUEST['term']);
        if($load_insured_by_name) {
          //$data_success['success'] = 1;
          $data_count = 0;
          foreach($load_insured_by_name as $load_d_key => $load_d_val){
            $policy_id = getPolicyIDByInsured($load_d_val['id']);
            $policy_data = getSinglePolicy($policy_id);
            if($policy_data['policynumber']){
                $data_success[$data_count]['id'] = THE_URL . '/module-insurance-system/main/claim_policy/' . $policy_data['id'];
                $data_success[$data_count]['text'] = $policy_data['policynumber'].' - '.$load_d_val['first_name'].' - '.$load_d_val['last_name'];
                $data_count++;
            }
          }
        } else {
          $data_success['success'] = 0;
        }
        $data_success = unique_multidim_array($data_success, 'id');
        echo json_encode($data_success);
      break;
      
      // Added on 15 April 2021
      case 'update_all_eob_details':
        $policy_id = trim($_REQUEST['policy_id']);
        $insured_id = trim($_REQUEST['insured_id']);
        $claim_id = trim($_REQUEST['claim_id']);
        $update_all_eob_details = update_all_eob_details($policy_id,$claim_id);
        if($update_all_eob_details){
            $data_success['success'] = 1;
        }else{
            $data_success['success'] = 0;
        }
        echo json_encode($data_success);
      break;
      
      // Added on 15 April 2021
      case 'get_maternity_details':
        $mat_id = trim($_REQUEST['mat_id']);
        $get_maternity_details = get_maternity_details($mat_id);
        if($get_maternity_details){
            $data_success['success'] = 1;
            $data_success['mat_start'] = $get_maternity_details[0]['mat_start'];
            $data_success['mat_finish'] = $get_maternity_details[0]['mat_finish'];
        }else{
            $data_success['success'] = 0;
        }
        echo json_encode($data_success);
      break;

      case 'EOBUpdatePolicyLocation':
        $location_id = $_REQUEST['loc_id'];
        $policy_id = $_REQUEST['policy_id'];

        if($policy_id && $location_id) {
          $updateData = updatePolicyLocation($policy_id, $location_id);
        }
        if($updateData){
          $success['success'] = 1;
          $policyLocation = getClaimLocationByID($location_id);
          $success['location'] = $policyLocation['name'];
        } else {
          $success['success'] = 0;
        }

        echo json_encode($success);
      break;

      case 'deleteSingleRemarksCode':
        $id = trim($_POST['id']);
        if($id){
          $deleteRemarksCode = deleteRemarksCode($id);
        }
        if($deleteRemarksCode){
          $data_success['success'] = 1;
        } else {
          $data_success['success'] = 0;
        }
        echo json_encode($data_success);
      break;
        // 03 June 2021

      case 'updateSingleRemarksCode':
      $id = trim($_POST['id']);
      $remarksCode = trim($_POST['remarksCode']);
      $remarksContent = trim($_POST['remarksContent']);
      if($id){
        $updateRemarksCode = updateRemarksCode($id, $remarksCode, $remarksContent);
      }
      if($updateRemarksCode === null){
        $data_success['success'] = 0;
      } else {
        $data_success['success'] = 1;
      }
      echo json_encode($data_success);
    break;

    case 'insertNewRemarksCode':
        $new_code = trim($_POST['new_code']);
        $new_content = trim($_POST['new_content']);
        
        if($new_code != ''){
          $insertRemarksCode = insertRemarksCode($new_code, $new_content);
        }
        if($insertRemarksCode){
          $data_success['success'] = 1;
        } else {
          $data_success['success'] = 0;
        }
        echo json_encode($data_success);
    break;

    case 'deleteSingleServiceCode':
        $id = trim($_POST['id']);
        if($id){
          $deleteServiceCode = deleteServiceCode($id);
        }
        if($deleteServiceCode){
          $data_success['success'] = 1;
        } else {
          $data_success['success'] = 0;
        }
        echo json_encode($data_success);
      break;

      case 'updateSingleServiceCode':
      $id = trim($_POST['id']);
      $serviceCode = trim($_POST['serviceCode']);
      $serviceContent = trim($_POST['serviceContent']);
      if($id){
        $updateServiceCode = updateServiceCode($id, $serviceCode, $serviceContent);
      }
      if($updateServiceCode === null){
        $data_success['success'] = 0;
      } else {
        $data_success['success'] = 1;
      }
      echo json_encode($data_success);
    break;

    case 'insertServiceCode':
        $new_code = trim($_POST['new_code']);
        $new_content = trim($_POST['new_content']);
        
        if($new_code != ''){
          $insertServiceCode = insertServiceCode($new_code, $new_content);
        }
        if($insertServiceCode){
          $data_success['success'] = 1;
        } else {
          $data_success['success'] = 0;
        }
        echo json_encode($data_success);
    break;
      
    case 'loadRemarksCode':
      $qq = $_REQUEST['datastr'];
      if($qq){
        $remarksContent = getRemarksContentByCode($qq);
      }
      if($remarksContent){
        $data['success'] = 1;
        $data['content'] = $remarksContent['content'];
        $data['id'] = $remarksContent['id'];
        $data['code'] = $remarksContent['code'];
      } else {
        $data['success'] = 0;
        
      }
      echo json_encode($data);
    break;
        
    case 'get_all_eob_details':
     $claim_id = $_REQUEST['claim_id'];
      if($claim_id){
        $eob_details = getAllEOBforSingleClaim($claim_id);
      }
      if($eob_details){
        $data['success'] = 1;
        $data['eob_details'] = $eob_details;
      } else {
        $data['success'] = 0;
        
      }
      echo json_encode($data);
    break;

    case 'check_same_patient_and_claria_year':
      $eob_ids = $_REQUEST['eob_ids'];
      foreach($eob_ids as $eob_id){
        $eobInfo = getEOBTableInfobyID($eob_id);
        $claimInfo = getClaimInfoByID($eobInfo[0]['id_claims']);  
        
        $patient_data[] = $claimInfo['idclaimant'];

        $start_date = $claimInfo['valid_start'];  
        if ($start_date != '0000-00-00' && ($timestamp = strtotime($start_date)) !== false)
        {
            $php_date = getdate($timestamp);   
            $month = $php_date['mon'];
            $year = $php_date['year'];     
            
            if($month <3){
              $year = $year -1;
            }
            $yrs[] = $year;
        } 

      }

      $data1 = (count(array_unique($patient_data)) === 1);
      $data2 = (count(array_unique($yrs)) === 1);

      if($data1 == true && $data2 == true){
        $data = true;
      }else{
        $data = false;
      }

      echo json_encode($data);


    break;

    case 'print_EOBs':

      $policy_id = $_REQUEST['policy_id'];
      $insured_id = $_REQUEST['insured_id'];
      $eob_start = dateDBFormat($_REQUEST['eobPeriodStart']);
      $eob_end = dateDBFormat($_REQUEST['eobPeriodEnd']);

      $params= array(
          'id_policy'     => $policy_id,
          'id_insured'    => $insured_id,
          'period_start'  => $eob_start,
          'period_end'    => $eob_end    
      );

      $policyInfo = getSinglePolicy($policy_id);
      $eobInfo = getEOBsInDateRange($params);

      echo json_encode($eobInfo);

    break;

    case 'EOB_Process':

      $eob_ids = $_REQUEST['eob_ids'];
      $eob_statuss = $_REQUEST['eob_statuss'];
      
      $c = count($eob_ids);

      for($i=0 ; $i<$c ; $i++){
        $stats[] = updateEOBprocess($eob_ids[$i],$eob_statuss[$i]);

      }

      echo json_encode($stats);
    break;
    

	}
die();	
?>