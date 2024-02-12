<?php
/**
 * @author :)
 * @copyright 2019
 */
class main
	{

		var $controler_name;
		var $action;
		var $my_db;
		var $my_session;
		var $user_id;
		var $user_type;
		var $page_title;
		var $meta_keywords;
		var $meta_description;
        var $error;
		var $message;
		var $history;
		var $newMail;
		var $gerbage;

				
		function __construct()

		{
			global $db,$session;
			$this->my_db = $db;
			$this->my_session = $session;								
			$this->controler_name = 'main';
			$this->user_id = state('user_id');

            $this->error = state('err');
            $this->message = state('msg');
            $this->history = state('hst');

            state('err' , '');
            state('msg' , '');
            state('hst' , '');

            $this->action = 'default';

		}

		function default_func($params = array()){	
         /*$check_login = checkLoggedIn();	
         if(!($check_login && $check_login['user_type']==1)){
            urlredirect(THE_URL."auth/login");	
            exit;
         }
         require(COMMON_TEMPLATES.'header.tpl.php');
		 require(TEMPLATE_STORE.$this->controler_name.'/home.tpl.php');	
		 require(COMMON_TEMPLATES.'footer.tpl.php');
         $this->footer = 0;*/
         $this->health($params);
		}
        
        function health($params = array()){	 
                
         $check_login = checkLoggedIn();	
         if(!($check_login && $check_login['user_type']==1)){
            urlredirect(THE_URL."auth/login");	
            exit;
         }
         require(COMMON_TEMPLATES.'user.header.tpl.php');
		 require(TEMPLATE_STORE.$this->controler_name.'/health.tpl.php');	
		 require(COMMON_TEMPLATES.'user.footer.tpl.php');
		}
        
        function health_new($params = array()){	 
            
         $check_login = checkLoggedIn();	
         if(!($check_login && $check_login['user_type']==1)){
            urlredirect(THE_URL."auth/login");	
            exit;
         }
         
         $checkPermission = checkAccessPermission('Policies');
         
         
         require(COMMON_TEMPLATES.'user.header.tpl.php');
		 require(TEMPLATE_STORE.$this->controler_name.'/health.new.tpl.php');	
		 require(COMMON_TEMPLATES.'user.footer.tpl.php');
		}
        
        function health_edit($params = array()){	 
         global $policyInfo,$userSideBar;
         $check_login = checkLoggedIn();	
         if(!($check_login && $check_login['user_type']==1)){
            urlredirect(THE_URL."auth/login");	
            exit;
         }
         
         
         //print_r($params);
         $policyInfo = getSinglePolicy($params[0]);
         //print_r($policyInfo);
         state('c_premium',0);
         
         $userSideBar = "health.left.tpl.php";
         require(COMMON_TEMPLATES.'user.header.tpl.php');
		 require(TEMPLATE_STORE.$this->controler_name.'/health.edit.tpl.php');	
		 require(COMMON_TEMPLATES.'user.footer.tpl.php');
		}
        
        function rate_up($params = array()){
        global $insuredInfo,$insuredLists;
         
        $check_login = checkLoggedIn();	
         if(!($check_login && $check_login['user_type']==1)){
            urlredirect(THE_URL."auth/login");	
            exit;
         }
         
         $insured_id = $params[0];
         
         $insuredInfo = getHealthSingleInsured($insured_id);
         if($insuredInfo){
          $policy_id = $insuredInfo['idpolicy']; 
          //if($policy_id)
          //$insuredLists = getHealthInsuredLists($policy_id);
          if($policy_id){
                 if($params[1]=='insured'){
                 $auditName = getAuditName($check_login);
                 addAudit(array("uid"=>$check_login['user_id'],"idpolicy"=>$policy_id,"action"=>$auditName." Clicked add Rate Up"));
                 }
          }
          
         }
         
         require(COMMON_TEMPLATES.'user.header.tpl.php');
		 require(TEMPLATE_STORE.$this->controler_name.'/rate.up.tpl.php');	
		 require(COMMON_TEMPLATES.'user.footer.tpl.php');
		}
        
       
       function cancel_notice_claria($params = array()){
        global $policyInfo;
        
        $check_login = checkLoggedIn();	
         if(!($check_login && $check_login['user_type']==1)){
            urlredirect(THE_URL."auth/login");	
            exit;
         }
         
        $policy_id = $params[0];
        if($policy_id){
            $policyInfo = getSinglePolicy($policy_id);
            $auditName = getAuditName($check_login);
            addAudit(array("uid"=>$check_login['user_id'],"idpolicy"=>$policy_id,"action"=>$auditName." Opened Cancellation Notice"));
            require(TEMPLATE_STORE.$this->controler_name.'/cancel-notice-claria.tpl.php');
            
        }
       }
       
       function approval_sheet_claria($params = array()){
        global $policyInfo;
        
        $check_login = checkLoggedIn();	
         if(!($check_login && $check_login['user_type']==1)){
            urlredirect(THE_URL."auth/login");	
            exit;
         }
         
        $policy_id = $params[0];
        if($policy_id){
            $policyInfo = getSinglePolicy($policy_id);
             $auditName = getAuditName($check_login);
             addAudit(array("uid"=>$check_login['user_id'],"idpolicy"=>$policy_id,"action"=>$auditName." Opened approval sheet"));
            require(TEMPLATE_STORE.$this->controler_name.'/approval-sheet-claria.tpl.php');
            
        }
       }
       
       function reinstatement_sheet_claria($params = array()){
        global $policyInfo;
        
        $check_login = checkLoggedIn();	
         if(!($check_login && $check_login['user_type']==1)){
            urlredirect(THE_URL."auth/login");	
            exit;
         }
         
        $policy_id = $params[0];
        if($policy_id){
            $policyInfo = getSinglePolicy($policy_id);
            
            $auditName = getAuditName($check_login);
            addAudit(array("uid"=>$check_login['user_id'],"idpolicy"=>$policy_id,"action"=>$auditName." Opened Reinstatement Sheet"));
            
            
            require(TEMPLATE_STORE.$this->controler_name.'/reinstatement-sheet-claria.tpl.php');
            
        }
       }
       
       
       function ninety_day_waiver_print_claria($params = array()){
        global $policyInfo;
        
        $check_login = checkLoggedIn();	
         if(!($check_login && $check_login['user_type']==1)){
            urlredirect(THE_URL."auth/login");	
            exit;
         }
         
        $policy_id = $params[0];
        if($policy_id){
            $policyInfo = getSinglePolicy($policy_id);
            $auditName = getAuditName($check_login);
            addAudit(array("uid"=>$check_login['user_id'],"idpolicy"=>$policy_id,"action"=>$auditName." Opened 90 day waiver"));
            require(TEMPLATE_STORE.$this->controler_name.'/ninety-day-waiver-print-claria.tpl.php');
            
        }
       }
        
     function  print_policy($params = array()){
        global $policyInfo,$insuredInfo,$insuredLists;
        $check_login = checkLoggedIn();	
         if(!($check_login && $check_login['user_type']==1)){
            urlredirect(THE_URL."auth/login");	
            exit;
         }
         
        $policy_id = $params[0];
        if($policy_id){
            $policyInfo = getSinglePolicy($policy_id);
        }
        require(COMMON_TEMPLATES.'user.header.tpl.php');
	    require(TEMPLATE_STORE.$this->controler_name.'/print-policy.tpl.php');	
	    require(COMMON_TEMPLATES.'user.footer.tpl.php');
     }
     
     function policy_print($params = array()){
        global $policyInfo,$insuredInfo,$insuredLists;
         
        $check_login = checkLoggedIn();	
         if(!($check_login && $check_login['user_type']==1)){
            urlredirect(THE_URL."auth/login");	
            exit;
         }
         
         $policy_id = $params[0];
         
         if($policy_id){
            
             $auditName = getAuditName($check_login);
             addAudit(array("uid"=>$check_login['user_id'],"idpolicy"=>$policy_id,"action"=>$auditName." Printed Policy Booklet"));
            
            
            $policyInfo = getSinglePolicy($policy_id);
            require(TEMPLATE_STORE.$this->controler_name.'/policy-print.tpl.php');
         }
         
		 
     }
     
     function policy_single_print($params = array()){
        global $policyInfo,$insuredInfo,$insuredLists;
         
        $check_login = checkLoggedIn();	
         if(!($check_login && $check_login['user_type']==1)){
            urlredirect(THE_URL."auth/login");	
            exit;
         }
         
         $insured_id = $params[0];
         
         if($insured_id){
            
             $auditName = getAuditName($check_login);
             addAudit(array("uid"=>$check_login['user_id'],"idpolicy"=>$policy_id,"action"=>$auditName." Printed Policy Booklet"));
            
            
            $insuredInfo = getHealthSingleInsured($insured_id);
            require(TEMPLATE_STORE.$this->controler_name.'/policy-single-print.tpl.php');
         }
         
		 
     }
     
     function policy_all_print($params = array()){
        global $policyInfo,$insuredInfo,$insuredLists;
         
        $check_login = checkLoggedIn();	
         if(!($check_login && $check_login['user_type']==1)){
            urlredirect(THE_URL."auth/login");	
            exit;
         }
         
         $policy_id = $params[0];
         
         if($policy_id){
            
             $auditName = getAuditName($check_login);
             addAudit(array("uid"=>$check_login['user_id'],"idpolicy"=>$policy_id,"action"=>$auditName." Printed Policy Booklet"));
            
            
            $policyInfo = getSinglePolicy($policy_id);
            require(TEMPLATE_STORE.$this->controler_name.'/policy-all-print.tpl.php');
         }
         
		 
     }
     
     function pdf_insured($params = array()){
        global $policyInfo,$insuredInfo,$insuredLists;
         
        $check_login = checkLoggedIn();	
         if(!($check_login && $check_login['user_type']==1)){
            urlredirect(THE_URL."auth/login");	
            exit;
         }
         
         $policy_id = $params[0];
         if($policy_id){            
            $policyInfo = getSinglePolicy($policy_id);
            require(TEMPLATE_STORE.$this->controler_name.'/pdf-insured.tpl.php');
         }         
        //require(TEMPLATE_STORE.$this->controler_name.'/pdf-insured.tpl.php');
         
		 
     }
     
     function pdf_single_insured($params = array()){
        global $policyInfo,$insuredInfo,$insuredLists;
         
        $check_login = checkLoggedIn();	
         if(!($check_login && $check_login['user_type']==1)){
            urlredirect(THE_URL."auth/login");	
            exit;
         }
         
         $insured_id = $params[0];
        
        if($insured_id){
            
             $auditName = getAuditName($check_login);
             addAudit(array("uid"=>$check_login['user_id'],"idpolicy"=>$policy_id,"action"=>$auditName." Printed Policy Booklet"));
            
            
            $insuredInfo = getHealthSingleInsured($insured_id);
            require(TEMPLATE_STORE.$this->controler_name.'/pdf-single-insured.tpl.php');
         }
         
		 
     }
     
     function pdf_all_insured($params = array()){
        global $policyInfo,$insuredInfo,$insuredLists;
         
        $check_login = checkLoggedIn();	
         if(!($check_login && $check_login['user_type']==1)){
            urlredirect(THE_URL."auth/login");	
            exit;
         }
         
         $policy_id = $params[0];
         
         if($policy_id){
            
             $auditName = getAuditName($check_login);
             addAudit(array("uid"=>$check_login['user_id'],"idpolicy"=>$policy_id,"action"=>$auditName." Printed Policy Booklet"));
            
            
            $policyInfo = getSinglePolicy($policy_id);
            require(TEMPLATE_STORE.$this->controler_name.'/pdf-all-insured.tpl.php');
         }
         
		 
     }
     
     function claria_express($params = array()){
        global $policyInfo,$insuredInfo,$insuredLists;
         
        $check_login = checkLoggedIn();	
         if(!($check_login && $check_login['user_type']==1)){
            urlredirect(THE_URL."auth/login");	
            exit;
         }
         
         $policy_id = $params[0];
         
         if($policy_id){
            $policyInfo = getSinglePolicy($policy_id);
            $auditName = getAuditName($check_login);
            addAudit(array("uid"=>$check_login['user_id'],"idpolicy"=>$policy_id,"action"=>$auditName." Opened Claria Express"));
            
            require(TEMPLATE_STORE.$this->controler_name.'/claria_express.tpl.php');
         }
         
		 
     }
     
     
     function delivery_request_main($params = array()){
        global $policyInfo;
        
        $check_login = checkLoggedIn();	
        if(!($check_login && $check_login['user_type']==1)){
            urlredirect(THE_URL."auth/login");	
            exit;
        }
         
        $policy_id = $params[0];
        if($policy_id){
            $policyInfo = getSinglePolicy($policy_id);
            $auditName = getAuditName($check_login);
            addAudit(array("uid"=>$check_login['user_id'],"idpolicy"=>$policy_id,"action"=>$auditName." Opened Delivery Request"));
            
        }
        require(COMMON_TEMPLATES.'user.header.tpl.php');
	    require(TEMPLATE_STORE.$this->controler_name.'/delivery-request-main.tpl.php');	
	    require(COMMON_TEMPLATES.'user.footer.tpl.php');
       }
       
       
       function new_delivery_request($params = array()){	 
         global $policyInfo,$delivery_req_number,$dreq_id; 
         $check_login = checkLoggedIn();	
         if(!($check_login && $check_login['user_type']==1)){
            urlredirect(THE_URL."auth/login");	
            exit;
         }
         
         $policy_id = $params[0];
         
         $checkPermission = checkUserAccessRole('Policies');
         if(!$checkPermission){
              urlredirect(THE_URL."main/delivery-request-main/".$policy_id."/?pr=1");	
              exit;  
         }
         
         
         $dreq_submit = $this->my_db->post('dreq_submit');
         if($dreq_submit){
         $delivery_req_number = $formData['dreqnumber'] = $this->my_db->post('delivery_req_number');
         $formData['idpolicy'] = $this->my_db->post('policy_num');
         $formData['datesent'] = date("Y-m-d",strtotime($this->my_db->post('drq_date_sent')));
         $formData['detail'] = $this->my_db->post('dreq_details');
         $formData['status'] = $this->my_db->post('dreq_status');
         $dreq_id = $this->my_db->post('dreq_num');
         saveDeliveryReq($dreq_id,$formData);
         urlredirect(THE_URL."main/delivery-request-main/".$policy_id);	
         exit;
         }
         
        
         if($policy_id){
            $policyInfo = getSinglePolicy($policy_id);  
         }
        
         if($policyInfo && !$delivery_req_number){
           $dreq_id = createNewDeliveryReq($policyInfo['id']); 
           if($dreq_id)
           $delivery_req_number = generateDreqNumber($dreq_id,$policyInfo['policynumber']);
         }
        
         require(COMMON_TEMPLATES.'user.header.tpl.php');
		 require(TEMPLATE_STORE.$this->controler_name.'/new-delivery-request.tpl.php');	
		 require(COMMON_TEMPLATES.'user.footer.tpl.php');
		}
        
        function delivery_request_edit($params = array()){	 
         global $policyInfo,$delivery_req_info; 
         $check_login = checkLoggedIn();	
         if(!($check_login && $check_login['user_type']==1)){
            urlredirect(THE_URL."auth/login");	
            exit;
         }
         
         $policy_id = $params[0];
         $dreq_id = $params[1];
         
         $dreq_submit = $this->my_db->post('dreq_submit');
         if($dreq_submit){
         $delivery_req_number = $formData['dreqnumber'] = $this->my_db->post('delivery_req_number');
         $formData['idpolicy'] = $this->my_db->post('policy_num');
         $formData['datesent'] = date("Y-m-d",strtotime($this->my_db->post('drq_date_sent')));
         $formData['detail'] = $this->my_db->post('dreq_details');
         $formData['status'] = $this->my_db->post('dreq_status');
         $dreq_id = $this->my_db->post('dreq_num');
         saveDeliveryReq($dreq_id,$formData);
         urlredirect(THE_URL."main/delivery-request-main/".$policy_id);	
         exit;
         }
         
        
         if($policy_id){
            $policyInfo = getSinglePolicy($policy_id);  
         }
        
         if($policyInfo){
           $delivery_req_info = getDeliveryRequest($dreq_id);
         }
        
         require(COMMON_TEMPLATES.'user.header.tpl.php');
		 require(TEMPLATE_STORE.$this->controler_name.'/delivery-request-edit.tpl.php');	
		 require(COMMON_TEMPLATES.'user.footer.tpl.php');
		}
        
        
   function delivery_request_delete($params = array()){
     $dreq_id = $params[0];
     $policy_id = $params[1];
     if($dreq_id){
        deleteDeliveryReq($dreq_id);
     }
     
     urlredirect(THE_URL."main/delivery-request-main/".$policy_id);	
     exit; 
    } 
    
    function delivery_request_print_claria($params = array()){
        global $policyInfo;
        
        $check_login = checkLoggedIn();	
         if(!($check_login && $check_login['user_type']==1)){
            urlredirect(THE_URL."auth/login");	
            exit;
         }
         
        $policy_id = $params[0];
        if($policy_id){
            $policyInfo = getSinglePolicy($policy_id);
            require(TEMPLATE_STORE.$this->controler_name.'/delivery-request-print-claria.tpl.php');
            
        }
     }
     
     function delivery_request_print_claria_sp($params = array()){
        global $policyInfo;
        
        $check_login = checkLoggedIn();	
         if(!($check_login && $check_login['user_type']==1)){
            urlredirect(THE_URL."auth/login");	
            exit;
         }
         
        $policy_id = $params[0];
        if($policy_id){
            $policyInfo = getSinglePolicy($policy_id);
            require(TEMPLATE_STORE.$this->controler_name.'/delivery-request-print-claria-sp.tpl.php');
            
        }
     }
     
     function rate_up_print($params = array()){
        global $insuredInfo,$insuredLists;
         
        $check_login = checkLoggedIn();	
         if(!($check_login && $check_login['user_type']==1)){
            urlredirect(THE_URL."auth/login");	
            exit;
         }
         
         $insured_id = $params[0];
         
         $insuredInfo = getHealthSingleInsured($insured_id);
         if($insuredInfo){
          $policy_id = $insuredInfo['idpolicy']; 
          //if($policy_id)
          //$insuredLists = getHealthInsuredLists($policy_id);
          require(TEMPLATE_STORE.$this->controler_name.'/rate-up-print.tpl.php');
         }
         
		 
     }
     
     function rate_up_add_print($params = array()){
        global $insuredInfo,$insuredLists,$rateupinfo,$db,$rateup_info;
         
        $check_login = checkLoggedIn();	
         if(!($check_login && $check_login['user_type']==1)){
            urlredirect(THE_URL."auth/login");	
            exit;
         }
         
         $rateup_id = $params[0];
         
         $rateupinfo = getRateUpById($rateup_id);
         $rateupTypeId = $rateupinfo['idrateuptype'];
         if($rateupinfo){
          $sql="SELECT * FROM rateuptypes WHERE id='$rateupTypeId'";
          $rateup_info = $db->select_single($sql);
          //if($policy_id)
          //$insuredLists = getHealthInsuredLists($policy_id);
          require(TEMPLATE_STORE.$this->controler_name.'/rate-up-add-print.tpl.php');
         }
         
		 
     }
     function rate_up_ad_print($params = array()){
        global $insuredInfo,$policyinfo,$db;
         
        $check_login = checkLoggedIn();	
         if(!($check_login && $check_login['user_type']==1)){
            urlredirect(THE_URL."auth/login");	
            exit;
         }
         
         $insured_id = $params[0];
         
         $insuredInfo = getHealthSingleInsured($insured_id);
         if($insuredInfo){
            $policyinfo = getSinglePolicy($insuredInfo['idpolicy']); 
            require(TEMPLATE_STORE.$this->controler_name.'/rate-up-add-print-blank.tpl.php');
         }
         
		 
     }
     
     function rider_print($params = array()){
        global $insuredInfo,$insuredLists,$riderinfo,$db;
         
        $check_login = checkLoggedIn();	
         if(!($check_login && $check_login['user_type']==1)){
            urlredirect(THE_URL."auth/login");	
            exit;
         }
         
         $rider_id = $params[0];
         
         $sql="SELECT * FROM rider WHERE id='$rider_id'";
         $riderinfo = $db->select_single($sql);
         $insuredId = $riderinfo['insured_id'];
         if($riderinfo){
          $sql_ins="SELECT * FROM insured WHERE id='$insuredId'";
          $insuredInfo = $db->select_single($sql_ins);
          //if($policy_id)
          //$insuredLists = getHealthInsuredLists($policy_id);
          require(TEMPLATE_STORE.$this->controler_name.'/rider-print.tpl.php');
         }
         
		 
     }
     
     function rider_print_no_footer($params = array()){
        global $insuredInfo,$insuredLists,$riderinfo,$db;
         
        $check_login = checkLoggedIn();	
         if(!($check_login && $check_login['user_type']==1)){
            urlredirect(THE_URL."auth/login");	
            exit;
         }
         
         $rider_id = $params[0];
         
         $sql="SELECT * FROM rider WHERE id='$rider_id'";
         $riderinfo = $db->select_single($sql);
         $insuredId = $riderinfo['insured_id'];
         if($riderinfo){
          $sql_ins="SELECT * FROM insured WHERE id='$insuredId'";
          $insuredInfo = $db->select_single($sql_ins);
          //if($policy_id)
          //$insuredLists = getHealthInsuredLists($policy_id);
          require(TEMPLATE_STORE.$this->controler_name.'/rider-print-no-footer.tpl.php');
         }
         
		 
     }
     
     function rider_maternity($params = array()){
        global $insuredInfo,$insuredLists,$riderinfo,$db,$policyInfo;
         
        $check_login = checkLoggedIn();	
         if(!($check_login && $check_login['user_type']==1)){
            urlredirect(THE_URL."auth/login");	
            exit;
         }
         
         $policy_id = $params[0];
         
         if($policy_id){
            $policyInfo = getSinglePolicy($policy_id);
            require(TEMPLATE_STORE.$this->controler_name.'/rider-maternity.tpl.php');
            
         }
         
		 
     }
     
     function rider_commate($params = array()){
        global $insuredInfo,$insuredLists,$riderinfo,$db,$policyInfo;
         
        $check_login = checkLoggedIn();	
         if(!($check_login && $check_login['user_type']==1)){
            urlredirect(THE_URL."auth/login");	
            exit;
         }
         
         $policy_id = $params[0];
         
         if($policy_id){
            $policyInfo = getSinglePolicy($policy_id);
            require(TEMPLATE_STORE.$this->controler_name.'/rider-cornmate.tpl.php');
            
         }
         
		 
     }
    

        ####################### Faroque's code starts here ########################
    
        function rider($params = array())
        {
            global $insuredInfo,$insuredLists , $riderList;
            $check_login = checkLoggedIn();	
            if(!($check_login && $check_login['user_type']==1))
            {
                urlredirect(THE_URL."auth/login");	
                exit;
            }
            $insured_id = $params[0];
            
            $insuredInfo = getHealthSingleInsured($insured_id);
            if($insuredInfo)
            {
                $policy_id = $insuredInfo['idpolicy']; 
                //if($policy_id)
                //$insuredLists = getHealthInsuredLists($policy_id);
                if($policy_id){
                 if($params[1]=='insured'){
                 $auditName = getAuditName($check_login);
                 addAudit(array("uid"=>$check_login['user_id'],"idpolicy"=>$policy_id,"action"=>$auditName." Clicked add Rider"));
                 }
                }
            }
            
            
            if($insured_id > 0)
            {
                $riderList = $this->my_db->getRowsArray('rider' , "insured_id=$insured_id");                
            }
           
            require(COMMON_TEMPLATES.'user.header.tpl.php');
            require(TEMPLATE_STORE.$this->controler_name.'/rider.tpl.php');	
            require(COMMON_TEMPLATES.'user.footer.tpl.php');
		}
        
        function rider_new($params = array())
        {
            global $insuredInfo,$insuredLists , $riderInfo;
            
            $check_login = checkLoggedIn();	
            if(!($check_login && $check_login['user_type']==1))
            {
                urlredirect(THE_URL."auth/login");	
                exit;
            }
            
            $insured_id = $params[0];
            $rider_id = $params[1];
            
            
            $checkPermission = checkUserAccessRole('Policies');
            if(!$checkPermission){
              urlredirect(THE_URL."main/rider/".$insured_id."/?pr=1");	
              exit;  
            }
            
            
            
            $insuredInfo = getHealthSingleInsured($insured_id);
            if($insuredInfo)
            {
                $policy_id = $insuredInfo['idpolicy'];
            }
            
            if($rider_id)
            {
                $riderInfo = $this->my_db->pickRow('rider' , 'id' , $rider_id , 1);                
            }
           
            require(COMMON_TEMPLATES.'user.header.tpl.php');
            require(TEMPLATE_STORE.$this->controler_name.'/rider_new.tpl.php');	
            require(COMMON_TEMPLATES.'user.footer.tpl.php');
        }
        
        function rider_save($params = array())
        {
            global $insuredInfo,$insuredLists;
            
            
            $insured_id = $this->my_db->post('insured_id');
            $id = $this->my_db->post('id');
            
            
            $formData['rider_number'] = $this->my_db->post('rider_number');
            $formData['insured_id'] = $this->my_db->post('insured_id');
            $formData['name'] = $this->my_db->post('name');
            $formData['title'] = $this->my_db->post('title');
            
            $formData['status'] = $this->my_db->post('status');
            $formData['details'] = $this->my_db->post('details');            
            
            $formData['date_sent'] = date("Y-m-d",strtotime($this->my_db->post('date_sent')));
            
            
            //print_r($formData);
            
            if($id)
                $this->my_db->doUpdate($formData , 'rider' , "id = " . $id , 1);
            else
                $id = $this->my_db->doInsert($formData , 'rider' , 1);
            
            //urlredirect(THE_URL."main/rider_new/$insured_id/$id");
            
            urlredirect(THE_URL."main/rider/$insured_id");            
            exit;
        }
        
        function rider_refresh($params = array())
        {
            
        }
        
        function manual_rate($params = array())
        {
            global $rateInfo ;            
            $check_login = checkLoggedIn();	
            if(!($check_login && $check_login['user_type']==1))
            {
                urlredirect(THE_URL."auth/login");	
                exit;
            }
            
            $insured_id = $params[0];            
            if($insured_id)
            {
                $rateInfo = $this->my_db->pickRow('manual_rate' , 'insured_id' , $insured_id );                
                if(!$rateInfo)
                    $rateInfo['insured_id'] = $insured_id;
            }
            require(COMMON_TEMPLATES.'user.header.tpl.php');
            require(TEMPLATE_STORE.$this->controler_name.'/manualrate.tpl.php');	
            require(COMMON_TEMPLATES.'user.footer.tpl.php');
        }
        
        function manual_rate_save($params = array())
        {
            global $insuredInfo,$insuredLists,$db;  
            
            $check_login = checkLoggedIn();	
            if(!($check_login && $check_login['user_type']==1))
            {
                urlredirect(THE_URL."auth/login");	
                exit;
            }         
            
            $insured_id = $this->my_db->post('insured_id');
            $id = $this->my_db->post('id');
            
            
            $checkPermission = checkUserAccessRole('Policies');
            if(!$checkPermission){
              urlredirect(THE_URL."main/manual_rate/".$insured_id."/?pr=1");	
              exit;  
            }
               
            $formData['insured_id'] = $this->my_db->post('insured_id');         
            $formData['first_name'] = $this->my_db->post('first_name');
            $formData['last_name'] = $this->my_db->post('last_name');
            $formData['base_premium'] = $this->my_db->post('base_premium');
            $formData['total_premium'] = $this->my_db->post('total_premium');
            
            $sql='UPDATE insured SET basepremium="'.$formData['base_premium'].'", premium = "'.$formData['total_premium'].'" WHERE id="'.$formData['insured_id'].'"'; 
            $stats= $db->update($sql);          
            
            if($id)
                $this->my_db->doUpdate($formData , 'manual_rate' , "id = " . $id);
            else
                $id = $this->my_db->doInsert($formData , 'manual_rate',1);
            
            
            $insuredInfo = getHealthSingleInsured($insured_id);
            if($insuredInfo)
            {
                $policy_id = $insuredInfo['idpolicy'];
            }            
            
            urlredirect(THE_URL."main/health-edit/$policy_id");            
            exit;
        }
        
        
        function duplicate_policy($params = array()){
         global $policyInfo;
         
        $check_login = checkLoggedIn();	
         if(!($check_login && $check_login['user_type']==1)){
            urlredirect(THE_URL."auth/login");	
            exit;
         }
         
         $policy_id = $params[0];
         
      
         if($policy_id){
          $policyInfo = getSinglePolicy($policy_id);
          $auditName = getAuditName($check_login);
          addAudit(array("uid"=>$check_login['user_id'],"idpolicy"=>$policy_id,"action"=>$auditName." Opened Duplicate Policy"));
          //if($policy_id)
          //$insuredLists = getHealthInsuredLists($policy_id);
         }
         
         require(COMMON_TEMPLATES.'user.header.tpl.php');
		 require(TEMPLATE_STORE.$this->controler_name.'/duplicate.policy.tpl.php');	
		 require(COMMON_TEMPLATES.'user.footer.tpl.php');
		}


        ###########################################################################
        
        function add_rate($params = array()){
        global $insuredInfo,$insuredLists,$policyInfo;
         
        $check_login = checkLoggedIn();	
         if(!($check_login && $check_login['user_type']==1)){
            urlredirect(THE_URL."auth/login");	
            exit;
         }
         
         $insured_id = $params[0];
         
         $insuredInfo = getHealthSingleInsured($insured_id);
         if($insuredInfo){
          $policy_id = $insuredInfo['idpolicy']; 
          //if($policy_id)
          //$insuredLists = getHealthInsuredLists($policy_id);
         }
         
         require(COMMON_TEMPLATES.'user.header.tpl.php');
		 require(TEMPLATE_STORE.$this->controler_name.'/add-rate.tpl.php');	
		 require(COMMON_TEMPLATES.'user.footer.tpl.php');
		}
        
        function  calculate_premium($params = array()){
        global $policyInfo,$insuredInfo,$insuredLists;
        $check_login = checkLoggedIn();	
         if(!($check_login && $check_login['user_type']==1)){
            urlredirect(THE_URL."auth/login");	
            exit;
         }
         
        $policy_id = $params[0];
        if($policy_id){
            
            $auditName = getAuditName($check_login);
            addAudit(array("uid"=>$check_login['user_id'],"idpolicy"=>$policy_id,"action"=>$auditName." Clicked Calculate Premiums"));
            
            $policyInfo = getSinglePolicy($policy_id);
        }
        require(COMMON_TEMPLATES.'user.header.tpl.php');
	    require(TEMPLATE_STORE.$this->controler_name.'/calculate-premium.tpl.php');	
	    require(COMMON_TEMPLATES.'user.footer.tpl.php');
     }
     
     function mexico_rate_save($params = array())
        {
            global $insuredInfo,$insuredLists;           
            
            //$id = $this->my_db->post('id');
               
            $formData['plan'] = $this->my_db->post('plan');         
            $formData['coverage'] = $this->my_db->post('coverage');
            $formData['deductible'] = $this->my_db->post('deductible');
            $formData['deductiblearea'] = $this->my_db->post('deductiblearea');
            $formData['age'] = $this->my_db->post('age');
            $formData['premium'] = $this->my_db->post('premium');            
            $formData['rate_country'] = $this->my_db->post('rate_country');            
            $formData['rate_year'] = $this->my_db->post('rate_year');            
            
            
            $id = $this->my_db->doInsert($formData , 'rate_table_mundial',1);            
            
            urlredirect(THE_URL."main/add-rate");            
            exit;
        }
        
  function delete_policy($params = array()){
        
         
         $check_login = checkLoggedIn();	
         if(!($check_login && $check_login['user_type']==1)){
            urlredirect(THE_URL."auth/login");	
            exit;
         }
         
        $delete_submit = $this->my_db->get_post('delete_submit');
        $policy_id = $this->my_db->get_post('policy_uid');
         
        if($delete_submit){
          $checkPermission = checkAccessPermission('Policies');
         if($policy_id){
          $policyInfo = getSinglePolicy($policy_id);
          if($policyInfo){
            $status = deleteHealthPolicy($policy_id);
            if($status){
            state('msg' , 'Policy successfully deleted.'); 
            $auditName = getAuditName($check_login);
            addAudit(array("uid"=>$check_login['user_id'],"idpolicy"=>$policy_id,"action"=>$auditName." Deleted Policy ".$policyInfo['policynumber'],"audit_area"=>"delete_policy"));
            }
            else
            state('err' , 'Failed to delete policy. Please try again.');
          }
         }
         }
         require(COMMON_TEMPLATES.'user.header.tpl.php');
		 require(TEMPLATE_STORE.$this->controler_name.'/delete.policy.tpl.php');	
		 require(COMMON_TEMPLATES.'user.footer.tpl.php');
		}

        //start code for payment receipt by omar farook from 24/10/2019 at 12:27 pm//

       function  payment_receipt($params = array()){
        global $policyInfo;
        $check_login = checkLoggedIn(); 
        if(!($check_login && $check_login['user_type']==1)){
            urlredirect(THE_URL."auth/login");  
            exit;
        }

        
        require(TEMPLATE_STORE.$this->controler_name.'/payment-receipt.tpl.php'); 
        
    } //end payment receipt

    function  payment_approval_notice($params = array()){
        global $policyInfo;
        $check_login = checkLoggedIn(); 
        if(!($check_login && $check_login['user_type']==1)){
            urlredirect(THE_URL."auth/login");  
            exit;
        }
        require(TEMPLATE_STORE.$this->controler_name.'/payment-approval-notice.tpl.php'); 
        
    } //end payment receipt

    function  payments_form($params = array()){
     global $policyInfo;
     $check_login = checkLoggedIn();
     if(!($check_login && $check_login['user_type']==1)){
         urlredirect(THE_URL."auth/login");
         exit;
     }
     
     $policy_id = $params[0];
     if($policy_id){
         $policyInfo = getSinglePolicy($policy_id);
     }
     require(COMMON_TEMPLATES.'user.header.tpl.php');
     require(TEMPLATE_STORE.$this->controler_name.'/payments-form.tpl.php');
     require(COMMON_TEMPLATES.'user.footer.tpl.php');
 }

 function  agent_notes($params = array()){
   global $policyInfo;
   $check_login = checkLoggedIn();
   if(!($check_login && $check_login['user_type']==1)){
       urlredirect(THE_URL."auth/login");
       exit;
   }
   
   $policy_id = $params[0];
   if($policy_id){
       $policyInfo = getSinglePolicy($policy_id);
   }
   require(COMMON_TEMPLATES.'user.header.tpl.php');
   require(TEMPLATE_STORE.$this->controler_name.'/agent-notes.tpl.php');
   require(COMMON_TEMPLATES.'user.footer.tpl.php');
}

 function  file_upload($params = array()){
   global $policyInfo;
   $check_login = checkLoggedIn();
   if(!($check_login && $check_login['user_type']==1)){
       urlredirect(THE_URL."auth/login");
       exit;
   }
   
   $policy_id = $params[0];
   if($policy_id){
       $policyInfo = getSinglePolicy($policy_id);
   }
   require(COMMON_TEMPLATES.'user.header.tpl.php');
   require(TEMPLATE_STORE.$this->controler_name.'/file-upload.tpl.php');
   require(COMMON_TEMPLATES.'user.footer.tpl.php');
}

 function  upload($params = array()){
   global $policyInfo;
   $check_login = checkLoggedIn();
   if(!($check_login && $check_login['user_type']==1)){
       urlredirect(THE_URL."auth/login");
       exit;
   }
   
   require(TEMPLATE_STORE.$this->controler_name.'/upload.php');

}

 function  receipts($params = array()){

   $check_login = checkLoggedIn();
   if(!($check_login && $check_login['user_type']==1)){
       urlredirect(THE_URL."auth/login");
       exit;
   }
   
   require(COMMON_TEMPLATES.'user.header.tpl.php');
   require(TEMPLATE_STORE.$this->controler_name.'/receipts.tpl.php');
   require(COMMON_TEMPLATES.'user.footer.tpl.php');
}

	function  payment_report_rcv($params = array()){

	   $check_login = checkLoggedIn();
	   if(!($check_login && $check_login['user_type']==1)){
		   urlredirect(THE_URL."auth/login");
		   exit;
	   }

	   require(TEMPLATE_STORE.$this->controler_name.'/payment-report-rcv.tpl.php');
  
	}

	function  pending_authorize_report($params = array()){
		global $policyInfo;
		$check_login = checkLoggedIn();

		$policy_id = $params[0];
		if($policy_id){
			$policyInfo = getSinglePolicy($policy_id);
		}

	   if(!($check_login && $check_login['user_type']==1)){
		   urlredirect(THE_URL."auth/login");
		   exit;
	   }
	   require(COMMON_TEMPLATES.'user.header.tpl.php');
	   require(TEMPLATE_STORE.$this->controler_name.'/pending_authorize_report.tpl.php');
	   require(COMMON_TEMPLATES.'user.footer.tpl.php');
  
	}

	function  pending_report_heartland($params = array()){
		global $policyInfo;
		$check_login = checkLoggedIn();
	   if(!($check_login && $check_login['user_type']==1)){
		   urlredirect(THE_URL."auth/login");
		   exit;
	   }

		$policy_id = $params[0];
		if($policy_id){
			$policyInfo = getSinglePolicy($policy_id);
		}
	   require(COMMON_TEMPLATES.'user.header.tpl.php');
	   require(TEMPLATE_STORE.$this->controler_name.'/pending_report_heartland.tpl.php');
	   require(COMMON_TEMPLATES.'user.footer.tpl.php');
  
	}


	function  report_pending_payments($params = array()){
		global $policyInfo;
		$check_login = checkLoggedIn();
	   if(!($check_login && $check_login['user_type']==1)){
		   urlredirect(THE_URL."auth/login");
		   exit;
	   }

		$policy_id = $params[0];
		if($policy_id){
			$policyInfo = getSinglePolicy($policy_id);
		}
	   require(COMMON_TEMPLATES.'user.header.tpl.php');
	   require(TEMPLATE_STORE.$this->controler_name.'/report_pending_payments.tpl.php');
	   require(COMMON_TEMPLATES.'user.footer.tpl.php');
	  
	}

	function  report_void_payments($params = array()){
		global $policyInfo;
		$check_login = checkLoggedIn();
	   if(!($check_login && $check_login['user_type']==1)){
		   urlredirect(THE_URL."auth/login");
		   exit;
	   }

		$policy_id = $params[0];
		if($policy_id){
			$policyInfo = getSinglePolicy($policy_id);
		}
	   require(COMMON_TEMPLATES.'user.header.tpl.php');
	   require(TEMPLATE_STORE.$this->controler_name.'/report_void_payments.tpl.php');
	   require(COMMON_TEMPLATES.'user.footer.tpl.php');
	  
	}

	function  control_payments_auth($params = array()){
		global $policyInfo;
		$check_login = checkLoggedIn();
	   if(!($check_login && $check_login['user_type']==1)){
		   urlredirect(THE_URL."auth/login");
		   exit;
	   }

		$policy_id = $params[0];
		if($policy_id){
			$policyInfo = getSinglePolicy($policy_id);
		}
	   require(COMMON_TEMPLATES.'user.header.tpl.php');
	   require(TEMPLATE_STORE.$this->controler_name.'/control_payments_authorize.tpl.php');
	   require(COMMON_TEMPLATES.'user.footer.tpl.php');
	  
	}

	function  control_payments_heartland($params = array()){
		global $policyInfo;
		$check_login = checkLoggedIn();
	   if(!($check_login && $check_login['user_type']==1)){
		   urlredirect(THE_URL."auth/login");
		   exit;
	   }

		$policy_id = $params[0];
		if($policy_id){
			$policyInfo = getSinglePolicy($policy_id);
		}
	   require(COMMON_TEMPLATES.'user.header.tpl.php');
	   require(TEMPLATE_STORE.$this->controler_name.'/control_payments_heartland.tpl.php');
	   require(COMMON_TEMPLATES.'user.footer.tpl.php');
	  
	}

	function  temp_page_for_chk_commission($params = array()){
		global $policyInfo;
		$check_login = checkLoggedIn();
	   if(!($check_login && $check_login['user_type']==1)){
		   urlredirect(THE_URL."auth/login");
		   exit;
	   }

		$policy_id = $params[0];
		if($policy_id){
			$policyInfo = getSinglePolicy($policy_id);
		}
	   require(COMMON_TEMPLATES.'user.header.tpl.php');
	   require(TEMPLATE_STORE.$this->controler_name.'/temp_page_for_chk_commission.tpl.php');
	   require(COMMON_TEMPLATES.'user.footer.tpl.php');
	  
	}

	function  pending_commissions($params = array()){
		global $policyInfo;
		$check_login = checkLoggedIn();
	   if(!($check_login && $check_login['user_type']==1)){
		   urlredirect(THE_URL."auth/login");
		   exit;
	   }

		$policy_id = $params[0];
		if($policy_id){
			$policyInfo = getSinglePolicy($policy_id);
		}
	   require(COMMON_TEMPLATES.'user.header.tpl.php');
	   require(TEMPLATE_STORE.$this->controler_name.'/pending_commissions.tpl.php');
	   require(COMMON_TEMPLATES.'user.footer.tpl.php');
	  
	}

	function  commissions_printing($params = array()){
		global $policyInfo;
		$check_login = checkLoggedIn();
	   if(!($check_login && $check_login['user_type']==1)){
		   urlredirect(THE_URL."auth/login");
		   exit;
	   }

		$policy_id = $params[0];
		if($policy_id){
			$policyInfo = getSinglePolicy($policy_id);
		}
	   require(COMMON_TEMPLATES.'user.header.tpl.php');
	   require(TEMPLATE_STORE.$this->controler_name.'/commissions_printing.tpl.php');
	   require(COMMON_TEMPLATES.'user.footer.tpl.php');
	  
	}

	function  direct_deposit_form($params = array()){
		global $policyInfo;
		$check_login = checkLoggedIn();
	   if(!($check_login && $check_login['user_type']==1)){
		   urlredirect(THE_URL."auth/login");
		   exit;
	   }

		$policy_id = $params[0];
		if($policy_id){
			$policyInfo = getSinglePolicy($policy_id);
		}
	   require(COMMON_TEMPLATES.'user.header.tpl.php');
	   require(TEMPLATE_STORE.$this->controler_name.'/direct_deposit_form.tpl.php');
	   require(COMMON_TEMPLATES.'user.footer.tpl.php');
	  
	}
	function  get_agent_commission_report($params = array()){
			global $policyInfo;
			$check_login = checkLoggedIn(); 
			if(!($check_login && $check_login['user_type']==1)){
				urlredirect(THE_URL."auth/login");  
				exit;
			}
			require(TEMPLATE_STORE.$this->controler_name.'/get_agent_commission_report.tpl.php'); 
			
	}
	function  wt_form($params = array()){
		global $policyInfo;
		$check_login = checkLoggedIn();
	   if(!($check_login && $check_login['user_type']==1)){
		   urlredirect(THE_URL."auth/login");
		   exit;
	   }

		$policy_id = $params[0];
		if($policy_id){
			$policyInfo = getSinglePolicy($policy_id);
		}
	   require(COMMON_TEMPLATES.'user.header.tpl.php');
	   require(TEMPLATE_STORE.$this->controler_name.'/wt_form.tpl.php');
	   require(COMMON_TEMPLATES.'user.footer.tpl.php');
	  
	}
	function  check_commissions_form($params = array()){
		global $policyInfo;
		$check_login = checkLoggedIn();
	   if(!($check_login && $check_login['user_type']==1)){
		   urlredirect(THE_URL."auth/login");
		   exit;
	   }

		$policy_id = $params[0];
		if($policy_id){
			$policyInfo = getSinglePolicy($policy_id);
		}
	   require(COMMON_TEMPLATES.'user.header.tpl.php');
	   require(TEMPLATE_STORE.$this->controler_name.'/check_commissions_form.tpl.php');
	   require(COMMON_TEMPLATES.'user.footer.tpl.php');
	  
	}
	function  pending_report_cardknox($params = array()){
		global $policyInfo;
		$check_login = checkLoggedIn();
	   if(!($check_login && $check_login['user_type']==1)){
		   urlredirect(THE_URL."auth/login");
		   exit;
	   }

		$policy_id = $params[0];
		if($policy_id){
			$policyInfo = getSinglePolicy($policy_id);
		}
	   require(COMMON_TEMPLATES.'user.header.tpl.php');
	   require(TEMPLATE_STORE.$this->controler_name.'/pending_report_cardknox.tpl.php');
	   require(COMMON_TEMPLATES.'user.footer.tpl.php');
  
	}

	function  control_payments_cardknox($params = array()){
		global $policyInfo;
		$check_login = checkLoggedIn();
	   if(!($check_login && $check_login['user_type']==1)){
		   urlredirect(THE_URL."auth/login");
		   exit;
	   }

		$policy_id = $params[0];
		if($policy_id){
			$policyInfo = getSinglePolicy($policy_id);
		}
	   require(COMMON_TEMPLATES.'user.header.tpl.php');
	   require(TEMPLATE_STORE.$this->controler_name.'/control_payments_cardknox.tpl.php');
	   require(COMMON_TEMPLATES.'user.footer.tpl.php');
	  
	}
	function  report_commissions($params = array()){
		global $policyInfo;
		$check_login = checkLoggedIn();
	   if(!($check_login && $check_login['user_type']==1)){
		   urlredirect(THE_URL."auth/login");
		   exit;
	   }

		$policy_id = $params[0];
		if($policy_id){
			$policyInfo = getSinglePolicy($policy_id);
		}
	   require(COMMON_TEMPLATES.'user.header.tpl.php');
	   require(TEMPLATE_STORE.$this->controler_name.'/report_commissions.tpl.php');
	   require(COMMON_TEMPLATES.'user.footer.tpl.php');
	  
	}
	function  get_report_commissions($params = array()){
		global $policyInfo;
		$check_login = checkLoggedIn();
	   if(!($check_login && $check_login['user_type']==1)){
		   urlredirect(THE_URL."auth/login");
		   exit;
	   }

		$policy_id = $params[0];
		if($policy_id){
			$policyInfo = getSinglePolicy($policy_id);
		}
	   require(COMMON_TEMPLATES.'user.header.tpl.php');
	   require(TEMPLATE_STORE.$this->controler_name.'/get_report_commissions.tpl.php');
	   require(COMMON_TEget_agent_commission_report_by_group_ddMPLATES.'user.footer.tpl.php');
	  
	}
	function  report_paid_commissions($params = array()){
		global $policyInfo;
		$check_login = checkLoggedIn();
	   if(!($check_login && $check_login['user_type']==1)){
		   urlredirect(THE_URL."auth/login");
		   exit;
	   }

		$policy_id = $params[0];
		if($policy_id){
			$policyInfo = getSinglePolicy($policy_id);
		}
	   require(COMMON_TEMPLATES.'user.header.tpl.php');
	   require(TEMPLATE_STORE.$this->controler_name.'/report_paid_commissions.tpl.php');
	   require(COMMON_TEMPLATES.'user.footer.tpl.php');
	  
	}
	function  get_agent_commission_report_by_group_dd($params = array()){
			global $policyInfo;
			$check_login = checkLoggedIn(); 
			if(!($check_login && $check_login['user_type']==1)){
				urlredirect(THE_URL."auth/login");  
				exit;
			}
			require(TEMPLATE_STORE.$this->controler_name.'/get_agent_commission_report_by_group_dd.tpl.php'); 
			
	}
	function  get_agent_commission_report_by_group_wt($params = array()){
			global $policyInfo;
			$check_login = checkLoggedIn(); 
			if(!($check_login && $check_login['user_type']==1)){
				urlredirect(THE_URL."auth/login");  
				exit;
			}
			require(TEMPLATE_STORE.$this->controler_name.'/get_agent_commission_report_by_group_wt.tpl.php'); 
			
	}
	function  get_agent_commission_report_by_group_ck($params = array()){
			global $policyInfo;
			$check_login = checkLoggedIn(); 
			if(!($check_login && $check_login['user_type']==1)){
				urlredirect(THE_URL."auth/login");  
				exit;
			}
			require(TEMPLATE_STORE.$this->controler_name.'/get_agent_commission_report_by_group_ck.tpl.php'); 
			
	}
	 function  commissions($params = array()){
	   global $policyInfo;
	   $check_login = checkLoggedIn();
	   if(!($check_login && $check_login['user_type']==1)){
		   urlredirect(THE_URL."auth/login");
		   exit;
	   }
	   
	   $policy_id = $params[0];
	   if($policy_id){
		   $policyInfo = getSinglePolicy($policy_id);
	   }
	   require(COMMON_TEMPLATES.'user.header.tpl.php');
	   require(TEMPLATE_STORE.$this->controler_name.'/commissions.tpl.php');
	   require(COMMON_TEMPLATES.'user.footer.tpl.php');
	}
} //end main function
?>