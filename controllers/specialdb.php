<?php
/**
 * @author kodetraveller 
 * @copyright 2021
 */
// Class Specialdb
class specialdb
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
			$this->controler_name = 'specialdb';
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
            $this->specialdb_main($params);
		}
        
        function specialdb_main(){
          global $db;  	 
            $check_login = checkLoggedIn();	
            if(!($check_login && $check_login['user_type']==1)){
                urlredirect(THE_URL."auth/login");	
            exit;
            }
            require(COMMON_TEMPLATES.'user.header.tpl.php');
            require(TEMPLATE_STORE.$this->controler_name.'/specialdb_main.tpl.php');	
            require(COMMON_TEMPLATES.'user.footer.tpl.php');
	    }

        function info_agents(){
         global $db;    
            $check_login = checkLoggedIn(); 
            if(!($check_login && $check_login['user_type']==1)){
                urlredirect(THE_URL."auth/login");  
            exit;
            }
            require(COMMON_TEMPLATES.'user.header.tpl.php');
            require(TEMPLATE_STORE.$this->controler_name.'/agents_info.tpl.php');    
            require(COMMON_TEMPLATES.'user.footer.tpl.php');
        }

         function policy_info(){
            global $db;    
            $check_login = checkLoggedIn(); 
            if(!($check_login && $check_login['user_type']==1)){
                urlredirect(THE_URL."auth/login");  
            exit;
            }
            require(COMMON_TEMPLATES.'user.header.tpl.php');
            require(TEMPLATE_STORE.$this->controler_name.'/policy_info.tpl.php');    
            require(COMMON_TEMPLATES.'user.footer.tpl.php');
        }

        function business_by_agent(){
            global $db;    
            $check_login = checkLoggedIn(); 
            if(!($check_login && $check_login['user_type']==1)){
                urlredirect(THE_URL."auth/login");  
            exit;
            }
            require(COMMON_TEMPLATES.'user.header.tpl.php');
            require(TEMPLATE_STORE.$this->controler_name.'/business_by_agent.tpl.php');    
            require(COMMON_TEMPLATES.'user.footer.tpl.php');
        }
        function business_report(){
            global $db;    
            $check_login = checkLoggedIn(); 
            if(!($check_login && $check_login['user_type']==1)){
                urlredirect(THE_URL."auth/login");  
            exit;
            }
            require(COMMON_TEMPLATES.'user.header.tpl.php');
            require(TEMPLATE_STORE.$this->controler_name.'/business_report.tpl.php');    
            require(COMMON_TEMPLATES.'user.footer.tpl.php');
        }

        function claims(){
            global $db;    
            $check_login = checkLoggedIn(); 
            if(!($check_login && $check_login['user_type']==1)){
                urlredirect(THE_URL."auth/login");  
            exit;
            }
            require(COMMON_TEMPLATES.'user.header.tpl.php');
            require(TEMPLATE_STORE.$this->controler_name.'/claims.tpl.php');    
            require(COMMON_TEMPLATES.'user.footer.tpl.php');
        }

         function claim_info($params = array()){
            global $db;    
            $check_login = checkLoggedIn(); 
            if(!($check_login && $check_login['user_type']==1)){
                urlredirect(THE_URL."auth/login");  
            exit;
            }

            require(COMMON_TEMPLATES.'user.header.tpl.php');
            require(TEMPLATE_STORE.$this->controler_name.'/claim_info.tpl.php');    
            require(COMMON_TEMPLATES.'user.footer.tpl.php');
        }
        function renewals(){
            global $db;    
            $check_login = checkLoggedIn(); 
            if(!($check_login && $check_login['user_type']==1)){
                urlredirect(THE_URL."auth/login");  
            exit;
            }

            require(COMMON_TEMPLATES.'user.header.tpl.php');
            require(TEMPLATE_STORE.$this->controler_name.'/renewals.tpl.php');    
            require(COMMON_TEMPLATES.'user.footer.tpl.php');
        }
        function renewal_print(){
            global $db;    
            $check_login = checkLoggedIn(); 
            if(!($check_login && $check_login['user_type']==1)){
                urlredirect(THE_URL."auth/login");  
            exit;
            }

            require(COMMON_TEMPLATES.'user.header.tpl.php');
            require(TEMPLATE_STORE.$this->controler_name.'/print_renewal.tpl.php');    
            require(COMMON_TEMPLATES.'user.footer.tpl.php');
        }
        function print_renewal(){
            global $db;    
        
            $renewalID = $_GET['renewal_id'];
            $check_login = checkLoggedIn(); 
            if(!($check_login && $check_login['user_type']==1)){
                urlredirect(THE_URL."auth/login");  
            exit;
            }

            //$claimInfo = getClaimInfoByID($claimID);
            $renewalInfo = getRenewalInfoByID($renewalID);

            $deductible_amount = '';
            $coverage_amount = '';

            $deductibleInfo = getDeductiblebyid($renewalInfo['id_deductible']);
            if(isset($deductibleInfo)){
              $deductible_amount = $deductibleInfo[0]['deductible'];
            }
            $coverageInfo = getCoveragebyid($renewalInfo['id_coverage']);
            if(isset($coverageInfo)){
              $coverage_amount = $coverageInfo[0]['coverage'];
            }
            $payCycleInfo = getPayCyclebyid($renewalInfo['id_pay_cycle']);
            $insuredLists = getHealthInsuredLists($renewalInfo['id_policy']);
            $countryInfo = get_country_nameby_id($renewalInfo['country']);

            // Agent Info
            $agent_1 = getAgentdetbyID($renewalInfo['agentl1']);
            $agent_2 = getAgentdetbyID($renewalInfo['agentl2']);
            $agent_3 = getAgentdetbyID($renewalInfo['agentl3']);
            $agent_4 = getAgentdetbyID($renewalInfo['agentl4']);
    
            require(TEMPLATE_STORE.$this->controler_name.'/print_single_renewal.tpl.php');    
            
        }

         function commissions(){
            global $db;    
            $check_login = checkLoggedIn(); 
            if(!($check_login && $check_login['user_type']==1)){
                urlredirect(THE_URL."auth/login");  
            exit;
            }

            require(COMMON_TEMPLATES.'user.header.tpl.php');
            require(TEMPLATE_STORE.$this->controler_name.'/commissions.tpl.php');    
            require(COMMON_TEMPLATES.'user.footer.tpl.php');
        }

        function commission_report(){
            global $db;    
            $check_login = checkLoggedIn(); 
            if(!($check_login && $check_login['user_type']==1)){
                urlredirect(THE_URL."auth/login");  
            exit;
            }

            require(COMMON_TEMPLATES.'user.header.tpl.php');
            require(TEMPLATE_STORE.$this->controler_name.'/commission_report.tpl.php');    
            require(COMMON_TEMPLATES.'user.footer.tpl.php');
        }

         function print_eob(){
            global $db;    
            $check_login = checkLoggedIn(); 
            if(!($check_login && $check_login['user_type']==1)){
                urlredirect(THE_URL."auth/login");  
            exit;
            }

            require(COMMON_TEMPLATES.'user.header.tpl.php');
            require(TEMPLATE_STORE.$this->controler_name.'/print_eob.tpl.php');    
            require(COMMON_TEMPLATES.'user.footer.tpl.php');
        }
        function print_eob_report(){
            global $db;    
            $check_login = checkLoggedIn(); 
            if(!($check_login && $check_login['user_type']==1)){
                urlredirect(THE_URL."auth/login");  
            exit;
            }

           
            require(TEMPLATE_STORE.$this->controler_name.'/print_eob_report.tpl.php');    
           
        }



      
} //end of Renewals Class
?>