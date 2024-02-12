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
        
        function claim($params = array()){   
               
      $check_login = checkLoggedIn();   
      if(!($check_login && $check_login['user_type']==1)){
         urlredirect(THE_URL."auth/login"); 
         exit;
      }
      require(COMMON_TEMPLATES.'user.header.tpl.php');
      require(TEMPLATE_STORE.'claim/claim_health.tpl.php'); 
      require(COMMON_TEMPLATES.'user.footer.tpl.php');
}
        
       // by Faroque on 21 January 2021
function claim_policy($params = array())
{   
     global $policyInfo,$userSideBar , $db , $claim_list;
     $check_login = checkLoggedIn();  
     if(!($check_login && $check_login['user_type']==1)){
        urlredirect(THE_URL."auth/login");  
        exit;
     }

     //print_r($params);
     $policyInfo = getSinglePolicy($params[0]);

     #$insureds = $db->getDataByColumns('insured' , 'id' , array('first_name' , 'last_name') , 'idpolicy = ' . $params[0] . ' AND idrelation=1' );
     #pre($insureds);exit;

     $claim_list = $db->getRowsArray('claims' , 'idpolicy = ' . $params[0] );
     #pre($claim_list);exit;
     

     $userSideBar = "";
     require(COMMON_TEMPLATES.'user.header.tpl.php');
     #require(TEMPLATE_STORE.$this->controler_name.'/policy-claim-form.tpl.php');
     require(TEMPLATE_STORE.'claim/open-policy.tpl.php');
     require(COMMON_TEMPLATES.'user.footer.tpl.php');
}
        
    
function add_claim($params = array())
{   
     global $policyInfo , $db , $data;
     $check_login = checkLoggedIn();  
     if(!($check_login && $check_login['user_type']==1))
     {
        urlredirect(THE_URL."auth/login");  
        exit;
     }

     //print_r($params);
     $policyInfo = getSinglePolicy($params[0]);
           
     $insured_id = $db->getRowArray('insured' , 'idpolicy='.$params[0] , 'id');
     if($insured_id)
        $policyInfo['insured_id'] = $insured_id['id'];

     $claim_no = $policyInfo['policynumber'] . 'CLM' . 'xxx';

     $primary_data = $db->getRowArray('insured' , array('idrelation'=>'1' , 'idpolicy'=>$params[0]) , array('first_name' , 'last_name'));
     $primary_name = $primary_data['first_name'] . ' ' . $primary_data['last_name'];
     $data['primary_name'] = $primary_name;

     $claimant = array();
     $claimant_data = $db->getRowsArray('insured' , array('idpolicy'=>$params[0]) , array('id' , 'first_name' , 'last_name'));
     if($claimant_data)
     {
        foreach($claimant_data as $k=>$v):
           $claimant[$v['id']] = $v['first_name'] . ' ' . $v['last_name'];
        endforeach;
     }
     
     $primary_name = $primary_data['first_name'] . ' ' . $primary_data['last_name'];
     $data['primary_name'] = $primary_name;
     $data['claimant'] = $claimant;

     $idrateyear = $policyInfo['idrateyear'];
     $year = $db->get_variable('SELECT year FROM rateyear WHERE id = ' . $idrateyear);
     $data['year'] = $year;
     
     $userSideBar = "";
     require(COMMON_TEMPLATES.'user.header.tpl.php');
     require(TEMPLATE_STORE.'claim/add-claim-form.tpl.php');     
     require(COMMON_TEMPLATES.'user.footer.tpl.php');
}

    // By Faroque on 21 January 2021
      function edit_claim($params = array())
      {   
         global $policyInfo , $db , $data;
         $check_login = checkLoggedIn();  
         if(!($check_login && $check_login['user_type']==1))
         {
            urlredirect(THE_URL."auth/login");  
            exit;
         }

         $claimInfo = $db->getRowArray('claims' , array('id'=>$params[0]));
         $policyInfo = getSinglePolicy($claimInfo['idpolicy']);
               
         $insured_id = $db->getRowArray('insured' , 'idpolicy='.$params[0] , 'id');
         if($insured_id)
            $policyInfo['insured_id'] = $insured_id['id'];
         
         $primary_data = $db->getRowArray('insured' , array('idrelation'=>'1' , 'idpolicy'=>$claimInfo['idpolicy']) , array('first_name' , 'last_name'));
         $primary_name = $primary_data['first_name'] . ' ' . $primary_data['last_name'];
         $data['primary_name'] = $primary_name;

         $claimant = array();
         $claimant_data = $db->getRowsArray('insured' , array('idpolicy'=>$claimInfo['idpolicy']) , array('id' , 'first_name' , 'last_name'));
         if($claimant_data)
         {
            foreach($claimant_data as $k=>$v):
               $claimant[$v['id']] = $v['first_name'] . ' ' . $v['last_name'];
            endforeach;
         }
         
         $data['claimant'] = $claimant;
         $data['claimInfo'] = $claimInfo;

         $idrateyear = $policyInfo['idrateyear'];
         $year = $db->get_variable('SELECT year FROM rateyear WHERE id = ' . $idrateyear);
         $data['year'] = $year;
         
         $userSideBar = "";
         require(COMMON_TEMPLATES.'user.header.tpl.php');
         require(TEMPLATE_STORE.'claim/add-claim-form.tpl.php');     
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

   function status_commission($params = array()){
      global $policyInfo;
      $check_login = checkLoggedIn();
      if(!($check_login && $check_login['user_type']==1)){
         urlredirect(THE_URL."auth/login");
         exit();
      }
      /*
      $policy_id = $params[0];
      if($policy_id){
         $policyInfo = getSinglePolicy($policy_id);
      }
      */
      require(COMMON_TEMPLATES.'user.header.tpl.php');
      require(TEMPLATE_STORE.$this->controler_name.'/status_commission.tpl.php');
      require(COMMON_TEMPLATES.'user.footer.tpl.php');
   }
   
   function get_status_commission($params = array()){
      $check_login = checkLoggedIn();
      if(!($check_login && $check_login['user_type']==1))
      {
         urlredirect(THE_URL."auth/login");
         exit();
      }
      require(COMMON_TEMPLATES.'user.header.tpl.php');
      require(TEMPLATE_STORE.$this->controler_name.'/get_status_commission.tpl.php');
      require(COMMON_TEMPLATES.'user.footer.tpl.php');
   }

   function report_rpt($params = array()){
		$check_login = checkLoggedIn();
	   if(!($check_login && $check_login['user_type']==1)){
		   urlredirect(THE_URL."auth/login");
		   exit;
      }
      
      require(COMMON_TEMPLATES.'user.header.tpl.php');
	   require(TEMPLATE_STORE.$this->controler_name.'/report_rpt.tpl.php');
	   require(COMMON_TEMPLATES.'user.footer.tpl.php');
   }

   function report_bdx($params = array()){
      $check_login = checkLoggedIn();
	   if(!($check_login && $check_login['user_type']==1)){
		   urlredirect(THE_URL."auth/login");
		   exit;
      }
      require(COMMON_TEMPLATES.'user.header.tpl.php');
	   require(TEMPLATE_STORE.$this->controler_name.'/report_bdx.tpl.php');
	   require(COMMON_TEMPLATES.'user.footer.tpl.php');
   }

   function get_bdx_report($params=array()){
      $check_login = checkLoggedIn();
	   if(!($check_login && $check_login['user_type']==1)){
		   urlredirect(THE_URL."auth/login");
		   exit;
      }
      require(COMMON_TEMPLATES.'user.header.tpl.php');
	   require(TEMPLATE_STORE.$this->controler_name.'/get_bdx_report.tpl.php');
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
	   require(COMMON_TEMPLATES.'user.footer.tpl.php');
	  
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
   function rep_commissions_report(){
      $check_login = checkLoggedIn();
	   if(!($check_login && $check_login['user_type']==1)){
		   urlredirect(THE_URL."auth/login");
		   exit;
      }
      require(COMMON_TEMPLATES.'user.header.tpl.php');
      require(TEMPLATE_STORE.$this->controler_name.'/rep_commissions_report.tpl.php');
      require(COMMON_TEMPLATES.'user.footer.tpl.php');
   }
   
   function pending_payment_control(){
	   $check_login = checkLoggedIn();
	   if(!($check_login && $check_login['user_type']==1)){
		   urlredirect(THE_URL."auth/login");
		   exit;
      }
      require(COMMON_TEMPLATES.'user.header.tpl.php');
      require(TEMPLATE_STORE.$this->controler_name.'/pending_payment_control.tpl.php');
      require(COMMON_TEMPLATES.'user.footer.tpl.php');
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

   function show_commission_report_within_date(){
      global $policyInfo;
	   $check_login = checkLoggedIn();
	   if(!($check_login && $check_login['user_type']==1)){
		   urlredirect(THE_URL."auth/login");
		   exit;
      }
      require(COMMON_TEMPLATES.'user.header.tpl.php');
	   require(TEMPLATE_STORE.$this->controler_name.'/show_commission_report_within_date.tpl.php');
	   require(COMMON_TEMPLATES.'user.footer.tpl.php');
   }

   function info_agents(){
      global $policyInfo;
	   $check_login = checkLoggedIn();
	   if(!($check_login && $check_login['user_type']==1)){
		   urlredirect(THE_URL."auth/login");
		   exit;
      }
      require(COMMON_TEMPLATES.'user.header.tpl.php');
	   require(TEMPLATE_STORE.$this->controler_name.'/info_agents.tpl.php');
	   require(COMMON_TEMPLATES.'user.footer.tpl.php');
   }

   function export_info_agents_report(){
      global $policyInfo;
	   $check_login = checkLoggedIn();
	   if(!($check_login && $check_login['user_type']==1)){
		   urlredirect(THE_URL."auth/login");
		   exit;
      }
	   require(TEMPLATE_STORE.$this->controler_name.'/export_info_agents_report.tpl.php');
   }

   function inclusion_report(){
	   $check_login = checkLoggedIn();
	   if(!($check_login && $check_login['user_type']==1)){
		   urlredirect(THE_URL."auth/login");
		   exit;
      }
	   require(COMMON_TEMPLATES.'user.header.tpl.php');
	   require(TEMPLATE_STORE.$this->controler_name.'/inclusion_report.tpl.php');
	   require(COMMON_TEMPLATES.'user.footer.tpl.php');
   }

   function get_inclusion_report($params=array()){
      global $policyInfo;
	   $check_login = checkLoggedIn();
	   if(!($check_login && $check_login['user_type']==1)){
		   urlredirect(THE_URL."auth/login");
		   exit;
      }
      require(COMMON_TEMPLATES.'user.header.tpl.php');
	   require(TEMPLATE_STORE.$this->controler_name.'/get_inclusion_report.tpl.php');
	   require(COMMON_TEMPLATES.'user.footer.tpl.php');
   }

   function new_business(){
	   $check_login = checkLoggedIn();
	   if(!($check_login && $check_login['user_type']==1)){
		   urlredirect(THE_URL."auth/login");
		   exit;
      }
	   require(COMMON_TEMPLATES.'user.header.tpl.php');
	   require(TEMPLATE_STORE.$this->controler_name.'/new_business.tpl.php');
	   require(COMMON_TEMPLATES.'user.footer.tpl.php');
   }

   function get_new_business($params=array()){
      global $policyInfo;
	   $check_login = checkLoggedIn();
	   if(!($check_login && $check_login['user_type']==1)){
		   urlredirect(THE_URL."auth/login");
		   exit;
      }
      require(COMMON_TEMPLATES.'user.header.tpl.php');
	   require(TEMPLATE_STORE.$this->controler_name.'/get_new_business.tpl.php');
	   require(COMMON_TEMPLATES.'user.footer.tpl.php');
   }
   
   function get_new_business_report_by_agent($params=array()){
      global $policyInfo;
	   $check_login = checkLoggedIn();
	   if(!($check_login && $check_login['user_type']==1)){
		   urlredirect(THE_URL."auth/login");
		   exit;
      }
      require(COMMON_TEMPLATES.'user.header.tpl.php');
	   require(TEMPLATE_STORE.$this->controler_name.'/get_new_business_report_by_agent.tpl.php');
	   require(COMMON_TEMPLATES.'user.footer.tpl.php');
   }

   function policies_excluding_insured(){
      global $policyInfo;
	   $check_login = checkLoggedIn();
	   if(!($check_login && $check_login['user_type']==1)){
		   urlredirect(THE_URL."auth/login");
		   exit;
      }
      require(COMMON_TEMPLATES.'user.header.tpl.php');
	   require(TEMPLATE_STORE.$this->controler_name.'/policies_excluding_insured.tpl.php');
	   require(COMMON_TEMPLATES.'user.footer.tpl.php');
   }

   function cancelled_policies(){
      global $policyInfo;
	   $check_login = checkLoggedIn();
	   if(!($check_login && $check_login['user_type']==1)){
		   urlredirect(THE_URL."auth/login");
		   exit;
      }
      require(COMMON_TEMPLATES.'user.header.tpl.php');
	   require(TEMPLATE_STORE.$this->controler_name.'/cancelled_policies.tpl.php');
	   require(COMMON_TEMPLATES.'user.footer.tpl.php');
   }

   function get_cancelled_policies(){
      global $policyInfo;
	   $check_login = checkLoggedIn();
	   if(!($check_login && $check_login['user_type']==1)){
		   urlredirect(THE_URL."auth/login");
		   exit;
      }
      require(COMMON_TEMPLATES.'user.header.tpl.php');
	   require(TEMPLATE_STORE.$this->controler_name.'/get_cancelled_policies.tpl.php');
	   require(COMMON_TEMPLATES.'user.footer.tpl.php');
   }

   function payment_by_policy(){
      global $policyInfo;
	   $check_login = checkLoggedIn();
	   if(!($check_login && $check_login['user_type']==1)){
		   urlredirect(THE_URL."auth/login");
		   exit;
      }
      require(COMMON_TEMPLATES.'user.header.tpl.php');
	   require(TEMPLATE_STORE.$this->controler_name.'/payment_by_policy.tpl.php');
	   require(COMMON_TEMPLATES.'user.footer.tpl.php');
   }

   function get_payment_by_policy(){
      global $policyInfo;
	   $check_login = checkLoggedIn();
	   if(!($check_login && $check_login['user_type']==1)){
		   urlredirect(THE_URL."auth/login");
		   exit;
      }
      require(COMMON_TEMPLATES.'user.header.tpl.php');
	   require(TEMPLATE_STORE.$this->controler_name.'/get_payment_by_policy.tpl.php');
	   require(COMMON_TEMPLATES.'user.footer.tpl.php');
   }

   function active_business_report(){
      global $policyInfo;
	   $check_login = checkLoggedIn();
	   if(!($check_login && $check_login['user_type']==1)){
		   urlredirect(THE_URL."auth/login");
		   exit;
      }
      require(COMMON_TEMPLATES.'user.header.tpl.php');
	   require(TEMPLATE_STORE.$this->controler_name.'/active_business_report.tpl.php');
	   require(COMMON_TEMPLATES.'user.footer.tpl.php');
   }

   function get_active_business_report(){
      global $policyInfo;
	   $check_login = checkLoggedIn();
	   if(!($check_login && $check_login['user_type']==1)){
		   urlredirect(THE_URL."auth/login");
		   exit;
      }
      require(COMMON_TEMPLATES.'user.header.tpl.php');
	   require(TEMPLATE_STORE.$this->controler_name.'/get_active_business_report.tpl.php');
	   require(COMMON_TEMPLATES.'user.footer.tpl.php');
   }

   function rp_payment_report(){
      global $policyInfo;
	   $check_login = checkLoggedIn();
	   if(!($check_login && $check_login['user_type']==1)){
		   urlredirect(THE_URL."auth/login");
		   exit;
      }
      require(COMMON_TEMPLATES.'user.header.tpl.php');
	   require(TEMPLATE_STORE.$this->controler_name.'/rp_payment_report.tpl.php');
	   require(COMMON_TEMPLATES.'user.footer.tpl.php');
   }

   function get_payment_report_by_created(){
      global $policyInfo;
	   $check_login = checkLoggedIn();
	   if(!($check_login && $check_login['user_type']==1)){
		   urlredirect(THE_URL."auth/login");
		   exit;
      }
      require(COMMON_TEMPLATES.'user.header.tpl.php');
	   require(TEMPLATE_STORE.$this->controler_name.'/get_payment_report_by_created.tpl.php');
	   require(COMMON_TEMPLATES.'user.footer.tpl.php');
   }

   function get_payment_report_by_paid(){
      global $policyInfo;
	   $check_login = checkLoggedIn();
	   if(!($check_login && $check_login['user_type']==1)){
		   urlredirect(THE_URL."auth/login");
		   exit;
      }
      require(COMMON_TEMPLATES.'user.header.tpl.php');
	   require(TEMPLATE_STORE.$this->controler_name.'/get_payment_report_by_paid.tpl.php');
	   require(COMMON_TEMPLATES.'user.footer.tpl.php');
   }

   function pending_underwriting_control(){
      global $policyInfo;
      $check_login = checkLoggedIn();
	   if(!($check_login && $check_login['user_type']==1)){
		   urlredirect(THE_URL."auth/login");
		   exit;
      }
      require(COMMON_TEMPLATES.'user.header.tpl.php');
	   require(TEMPLATE_STORE.$this->controler_name.'/pending_underwriting_control.tpl.php');
	   require(COMMON_TEMPLATES.'user.footer.tpl.php');
   }

   function pending_payment_by_agent(){
      global $policyInfo;
	   $check_login = checkLoggedIn();
	   if(!($check_login && $check_login['user_type']==1)){
		   urlredirect(THE_URL."auth/login");
		   exit;
      }
      require(COMMON_TEMPLATES.'user.header.tpl.php');
	   require(TEMPLATE_STORE.$this->controler_name.'/pending_payment_by_agent.tpl.php');
	   require(COMMON_TEMPLATES.'user.footer.tpl.php');
   }

  function get_pending_renewals(){
     global $policyInfo;
	   $check_login = checkLoggedIn();
	   if(!($check_login && $check_login['user_type']==1)){
		   urlredirect(THE_URL."auth/login");
		   exit;
      }
      require(COMMON_TEMPLATES.'user.header.tpl.php');
	   require(TEMPLATE_STORE.$this->controler_name.'/get_pending_renewals.tpl.php');
	   require(COMMON_TEMPLATES.'user.footer.tpl.php');
  }

   function get_pending_payment_by_agent(){
      global $policyInfo;
	   $check_login = checkLoggedIn();
	   if(!($check_login && $check_login['user_type']==1)){
		   urlredirect(THE_URL."auth/login");
		   exit;
      }
      require(COMMON_TEMPLATES.'user.header.tpl.php');
	   require(TEMPLATE_STORE.$this->controler_name.'/get_pending_payment_by_agent.tpl.php');
	   require(COMMON_TEMPLATES.'user.footer.tpl.php');
   }

   // Printing Commission from 'commissions_printing'
   function export_agent_commission_report($params = array()) {
      global $policyInfo;
	   $check_login = checkLoggedIn();
	   if(!($check_login && $check_login['user_type']==1)){
		   urlredirect(THE_URL."auth/login");
		   exit;
      }
      require(TEMPLATE_STORE.$this->controler_name.'/export_agent_commission_report.tpl.php'); 
   }

   function export_report_commission_print($params = array()){
      global $policyInfo;
	   $check_login = checkLoggedIn();
	   if(!($check_login && $check_login['user_type']==1)){
		   urlredirect(THE_URL."auth/login");
		   exit;
      }
      require(TEMPLATE_STORE.$this->controler_name.'/export_report_commission_print.tpl.php'); 
   }

   function export_policy_status_commission($params = array()){
      global $policyInfo;
	   $check_login = checkLoggedIn();
	   if(!($check_login && $check_login['user_type']==1)){
		   urlredirect(THE_URL."auth/login");
		   exit;
      }
      require(TEMPLATE_STORE.$this->controler_name.'/export_policy_status_commission.tpl.php'); 
   }

   function export_report_rpt_print($params = array()){
      global $policyInfo;
	   $check_login = checkLoggedIn();
	   if(!($check_login && $check_login['user_type']==1)){
		   urlredirect(THE_URL."auth/login");
		   exit;
      }
      require(TEMPLATE_STORE.$this->controler_name.'/export_report_rpt_print.tpl.php'); 
   }
   
   function export_bdx_report($params = array()){
      global $policyInfo;
	   $check_login = checkLoggedIn();
	   if(!($check_login && $check_login['user_type']==1)){
		   urlredirect(THE_URL."auth/login");
		   exit;
      }
      require(TEMPLATE_STORE.$this->controler_name.'/export_bdx_report.tpl.php'); 
   }
   
   function export_new_business_by_agent($params = array()){
      global $policyInfo;
	   $check_login = checkLoggedIn();
	   if(!($check_login && $check_login['user_type']==1)){
		   urlredirect(THE_URL."auth/login");
		   exit;
      }
      require(TEMPLATE_STORE.$this->controler_name.'/export_new_business_by_agent.tpl.php'); 
   }

   // Added on 15 January 2021 {Shounok}
   function get_pending_payment_by_agent_monthly(){
       $check_login = checkLoggedIn();
       if(!($check_login && $check_login['user_type']==1)){
           urlredirect(THE_URL."auth/login");
           exit;
      }
      require(COMMON_TEMPLATES.'user.header.tpl.php');
       require(TEMPLATE_STORE.$this->controler_name.'/get_pending_payment_by_agent_monthly.tpl.php'); 
       require(COMMON_TEMPLATES.'user.footer.tpl.php');
   }
   // Added on 15 January 2021 {Shounok}
   function get_pending_payment_by_agent_quarterly(){
      $check_login = checkLoggedIn();
       if(!($check_login && $check_login['user_type']==1)){
           urlredirect(THE_URL."auth/login");
           exit;
      }
      require(COMMON_TEMPLATES.'user.header.tpl.php');
       require(TEMPLATE_STORE.$this->controler_name.'/get_pending_payment_by_agent_quarterly.tpl.php'); 
       require(COMMON_TEMPLATES.'user.footer.tpl.php');
   }
   // Added on 15 January 2021 {Shounok}
   function get_pending_payment_by_agent_semiannually(){
      $check_login = checkLoggedIn();
       if(!($check_login && $check_login['user_type']==1)){
           urlredirect(THE_URL."auth/login");
           exit;
      }
      require(COMMON_TEMPLATES.'user.header.tpl.php');
       require(TEMPLATE_STORE.$this->controler_name.'/get_pending_payment_by_agent_semiannually.tpl.php'); 
       require(COMMON_TEMPLATES.'user.footer.tpl.php');
   }
   
   function report_claria_express(){
      $check_login = checkLoggedIn();
       if(!($check_login && $check_login['user_type']==1)){
           urlredirect(THE_URL."auth/login");
           exit;
      }
      require(COMMON_TEMPLATES.'user.header.tpl.php');
      require(TEMPLATE_STORE.$this->controler_name.'/report-claria-express.tpl.php'); 
      require(COMMON_TEMPLATES.'user.footer.tpl.php');
   }
   
   // Added on 19 January 2021 {Shounok}
   function claim_controls($params = array()){   
         $check_login = checkLoggedIn();    
         if(!($check_login && $check_login['user_type']==1)){
            urlredirect(THE_URL."auth/login");  
            exit;
         }
         
       require(COMMON_TEMPLATES.'user.header.tpl.php');
         require(TEMPLATE_STORE.'claim/claims_control.tpl.php');    
         require(COMMON_TEMPLATES.'user.footer.tpl.php');
   }
   
   // Added on 19 January 2021 {Shounok}
   function in_progress_control($params = array()){  
         $check_login = checkLoggedIn();    
         if(!($check_login && $check_login['user_type']==1)){
            urlredirect(THE_URL."auth/login");  
            exit;
         }
         
       require(COMMON_TEMPLATES.'user.header.tpl.php');
         require(TEMPLATE_STORE.'claim/in_progress_control.tpl.php');   
         require(COMMON_TEMPLATES.'user.footer.tpl.php');
   }
   
   // Added on 19 January 2021 {Shounok}
   function claim_administration($params = array()){     
         $check_login = checkLoggedIn();    
         if(!($check_login && $check_login['user_type']==1)){
            urlredirect(THE_URL."auth/login");  
            exit;
         }
         
       require(COMMON_TEMPLATES.'user.header.tpl.php');
         require(TEMPLATE_STORE.'claim/claim_administration.tpl.php');  
         require(COMMON_TEMPLATES.'user.footer.tpl.php');
   }
   
   // Added on 19 January 2021 {Shounok}
   function claim_search_by_name($params = array()){     
         $check_login = checkLoggedIn();    
         if(!($check_login && $check_login['user_type']==1)){
            urlredirect(THE_URL."auth/login");  
            exit;
         }
         
       require(COMMON_TEMPLATES.'user.header.tpl.php');
         require(TEMPLATE_STORE.'claim/claim_search_by_name.tpl.php');  
         require(COMMON_TEMPLATES.'user.footer.tpl.php');
   }
   
   // Added on 19 January 2021 {Shounok}
   function claim_reports_from_dashboard($params = array()){     
         $check_login = checkLoggedIn();    
         if(!($check_login && $check_login['user_type']==1)){
            urlredirect(THE_URL."auth/login");  
            exit;
         }
         
       require(COMMON_TEMPLATES.'user.header.tpl.php');
         require(TEMPLATE_STORE.'claim/claim_reports_from_dashboard.tpl.php');  
         require(COMMON_TEMPLATES.'user.footer.tpl.php');
   }

   // Added on 19 January 2021 {Shounok}
   function printSpanishLetterofBenefit($params = array()){  
         $check_login = checkLoggedIn();    
         if(!($check_login && $check_login['user_type']==1)){
            urlredirect(THE_URL."auth/login");  
            exit;
         }
         require(TEMPLATE_STORE.'claim/printSpanishLetterofBenefit.tpl.php');   
   }

   // Added on 20 January 2021 {Shounok}
   function printEnglishLetterofBenefit($params = array()){  
         $check_login = checkLoggedIn();    
         if(!($check_login && $check_login['user_type']==1)){
            urlredirect(THE_URL."auth/login");  
            exit;
         }
         require(TEMPLATE_STORE.'claim/printEnglishLetterofBenefit.tpl.php');   
   }

   // Added on 20 January 2021 {Shounok}
   function pendingClaimsDashboard($params = array()){   
         $check_login = checkLoggedIn();    
         if(!($check_login && $check_login['user_type']==1)){
            urlredirect(THE_URL."auth/login");  
            exit;
         }
         require(COMMON_TEMPLATES.'user.header.tpl.php');
         require(TEMPLATE_STORE.'claim/pendingClaimsDashboard.tpl.php');    
         require(COMMON_TEMPLATES.'user.footer.tpl.php');   
   }
   // Added on 20 January 2021 {Shounok}
   function getPendingClaimsByAnalist(){
     $check_login = checkLoggedIn();    
      if(!($check_login && $check_login['user_type']==1)){
         urlredirect(THE_URL."auth/login"); 
         exit;
      }
      require(TEMPLATE_STORE.'claim/getPendingClaimsByAnalist.tpl.php'); 
   }
   // Added on 21 January 2021 {Shounok}
   function getPendingClaimsByDateRange(){
     $check_login = checkLoggedIn();    
      if(!($check_login && $check_login['user_type']==1)){
         urlredirect(THE_URL."auth/login"); 
         exit;
      }
      require(TEMPLATE_STORE.'claim/getPendingClaimsByDateRange.tpl.php'); 
   }
   
   //Added on 13 January 2021 {Shounok}
   function new_letter_of_benefits($params = array()){
      global $policyInfo;
       $check_login = checkLoggedIn();
       if(!($check_login && $check_login['user_type']==1)){
           urlredirect(THE_URL."auth/login");
           exit;
      }
      $policyInfo = getSinglePolicy($params[0]);
      
      require(COMMON_TEMPLATES.'user.header.tpl.php');
       require(TEMPLATE_STORE.'claim/new_letter_of_benefits.tpl.php');
       require(COMMON_TEMPLATES.'user.footer.tpl.php');
   }

   //Added on 22 January 2021 {Shounok}
   function edit_letter_of_benefits($params = array()){
      global $policyInfo, $ltrOfBenefitID;
       $check_login = checkLoggedIn();
       if(!($check_login && $check_login['user_type']==1)){
           urlredirect(THE_URL."auth/login");
           exit;
      }
      $policyInfo = getSinglePolicy($params[0]);
      $ltrOfBenefitID = $params[1];
      require(COMMON_TEMPLATES.'user.header.tpl.php');
       require(TEMPLATE_STORE.'claim/new_letter_of_benefits.tpl.php');
       require(COMMON_TEMPLATES.'user.footer.tpl.php');
   }

   // Added on 28 January 2021
   function all_pending_claims(){
      global $db;
       $check_login = checkLoggedIn();
       if(!($check_login && $check_login['user_type']==1)){
           urlredirect(THE_URL."auth/login");
           exit;
      }
       require(TEMPLATE_STORE.'claim/all_pending_claims.tpl.php');
   }
   
   // Added on 15 February 2021
   function pending_claims(){
      global $db;
       $check_login = checkLoggedIn();
       if(!($check_login && $check_login['user_type']==1)){
           urlredirect(THE_URL."auth/login");
           exit;
      }
       require(TEMPLATE_STORE.'claim/pending_claims.tpl.php');
   }
   
   // Added on 28 January 2021
   function printEOBDetail($params = array()){
      global $db, $policyInfo, $claimInfo, $data;
       $check_login = checkLoggedIn();
       if(!($check_login && $check_login['user_type']==1)){
           urlredirect(THE_URL."auth/login");
           exit;
      }
      $claimInfo = $db->getRowArray('claims' , array('id'=>$params[0]));
      $policyInfo = getSinglePolicy($claimInfo['idpolicy']);
       $insured_id = $db->getRowArray('insured' , 'idpolicy='.$params[0] , 'id');
         if($insured_id)
            $policyInfo['insured_id'] = $insured_id['id'];
         
         $primary_data = $db->getRowArray('insured' , array('idrelation'=>'1' , 'idpolicy'=>$claimInfo['idpolicy']) , array('first_name' , 'last_name'));
         $primary_name = $primary_data['first_name'] . ' ' . $primary_data['last_name'];
         $data['primary_name'] = $primary_name;

         $claimant = array();
         $claimant_data = $db->getRowsArray('insured' , array('idpolicy'=>$claimInfo['idpolicy']) , array('id' , 'first_name' , 'last_name'));
         if($claimant_data)
         {
            foreach($claimant_data as $k=>$v):
               $claimant[$v['id']] = $v['first_name'] . ' ' . $v['last_name'];
            endforeach;
         }
         
         $data['claimant'] = $claimant;
         $data['claimInfo'] = $claimInfo;

         $idrateyear = $policyInfo['idrateyear'];
         $year = $db->get_variable('SELECT year FROM rateyear WHERE id = ' . $idrateyear);
         $data['year'] = $year;

         $idCountry = $policyInfo['idcountry'];
         $country_name = get_country_nameby_id($idCountry);
         $data['country'] = $country_name['country'];

       require(TEMPLATE_STORE.'claim/printEOBDetail.tpl.php');
   }
   // Added on 28 January 2021
   function add_maternity_cycle($params = array()){
      global $db, $policyID;
      $check_login = checkLoggedIn();
       if(!($check_login && $check_login['user_type']==1)){
           urlredirect(THE_URL."auth/login");
           exit;
      }
      $policyID = $params[0];
      require(COMMON_TEMPLATES.'user.header.tpl.php');
       require(TEMPLATE_STORE.'claim/add_maternity_cycle.tpl.php');
       require(COMMON_TEMPLATES.'user.footer.tpl.php');
   }
   // Added on 28 January 2021
   function in_progress_report(){
     global $db;
     $check_login = checkLoggedIn();
       if(!($check_login && $check_login['user_type']==1)){
           urlredirect(THE_URL."auth/login");
           exit;
      }
       require(TEMPLATE_STORE.'claim/in_progress_report.tpl.php');
   }
} //end main function
?>