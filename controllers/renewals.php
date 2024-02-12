<?php
/**
 * @author kodetraveller 
 * @copyright 2021
 */
// Class Renewals
class renewals
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
			$this->controler_name = 'renewals';
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
            $this->renewals_main($params);
		}
        
        function renewals_main(){	
            global $db; 
            $check_login = checkLoggedIn();	
            if(!($check_login && $check_login['user_type']==1)){
                urlredirect(THE_URL."auth/login");	
            exit;
            }
            require(COMMON_TEMPLATES.'user.header.tpl.php');
            require(TEMPLATE_STORE.$this->controler_name.'/renewals_main.tpl.php');	
            require(COMMON_TEMPLATES.'user.footer.tpl.php');
	    }

         function renewals_policy($params = array()){
            global $db, $policy_info;
            $policy_id = $params[0];
            $policy_info = get_policy_data_by_policyid($policy_id);
            //print_r($policy_info);
            $check_login = checkLoggedIn(); 
            if(!($check_login && $check_login['user_type']==1)){
                urlredirect(THE_URL."auth/login");  
            exit;
            }
            require(COMMON_TEMPLATES.'user.header.tpl.php');
            require(TEMPLATE_STORE.$this->controler_name.'/renewals_policy.tpl.php');    
            require(COMMON_TEMPLATES.'user.footer.tpl.php');
        }

         function add_renewals($params = array()){
           global $db; 
           $check_login = checkLoggedIn();  
            if(!($check_login && $check_login['user_type']==1)){
                urlredirect(THE_URL."auth/login");  
            exit;
            }
            $policy_id = $params[0];
    
            $policyInfo = getSinglePolicy($policy_id);

            require(COMMON_TEMPLATES.'user.header.tpl.php');
            require(TEMPLATE_STORE.$this->controler_name.'/renewals_open.tpl.php');  
            require(COMMON_TEMPLATES.'user.footer.tpl.php');
        }


        function edit_renewals($params = array()){
           global $db; 
           $check_login = checkLoggedIn();  
            if(!($check_login && $check_login['user_type']==1)){
                urlredirect(THE_URL."auth/login");  
            exit;
            }
            $policy_id = $params[0];
            $renewal_id = $params[1];

            $policyInfo = getSinglePolicy($policy_id);
            $renewalInfo = getRenewalInfoByID($renewal_id);

            require(COMMON_TEMPLATES.'user.header.tpl.php');
            require(TEMPLATE_STORE.$this->controler_name.'/renewals_open.tpl.php');  
            require(COMMON_TEMPLATES.'user.footer.tpl.php');
        }

        function printIndividualRenewal($params = array()){
            global $db;
            $renewalID = $_GET['renewal_id'];
            $policyID = $_GET['policy_id'];
            $check_login = checkLoggedIn(); 
            if(!($check_login && $check_login['user_type']==1)){
                urlredirect(THE_URL."auth/login");  
            exit;
            }

            //$claimInfo = getClaimInfoByID($claimID);
            $policyInfo = getSinglePolicy($policyID);
            $renewalInfo = getRenewalInfoByID($renewalID);

            $deductible_amount = '';
            $coverage_amount = '';

            $deductibleInfo = getDeductiblebyid($policyInfo['iddeductible']);
            if(isset($deductibleInfo)){
              $deductible_amount = $deductibleInfo[0]['deductible'];
            }
            $coverageInfo = getCoveragebyid($policyInfo['idcoverage']);
            if(isset($coverageInfo)){
              $coverage_amount = $coverageInfo[0]['coverage'];
            }
            $payCycleInfo = getPayCyclebyid($policyInfo['idpaycycle']);
            $insuredLists = getHealthInsuredLists($policyInfo['id']);
            $countryInfo = get_country_nameby_id($policyInfo['idcountry']);

            // Agent Info
            $agent_1 = getAgentdetbyID($policyInfo['idagent']);
            $agent_2 = getAgentdetbyID($policyInfo['idagent2']);
            $agent_3 = getAgentdetbyID($policyInfo['idagent3']);
            $agent_4 = getAgentdetbyID($policyInfo['idagent4']);
            
            require(TEMPLATE_STORE.$this->controler_name.'/print_renewal.tpl.php');    
        }


        function print_renewal_add($params = array()){
            global $db;
            $renewalID = $_GET['renewal_id'];
            $policyID = $_GET['policy_id'];
            $check_login = checkLoggedIn(); 
            if(!($check_login && $check_login['user_type']==1)){
                urlredirect(THE_URL."auth/login");  
            exit;
            }

            $policyInfo = getSinglePolicy($policyID);
            $renewalInfo = getRenewalInfoByID($renewalID);

            $deductible_amount = '';
            $coverage_amount = '';

            $deductibleInfo = getDeductiblebyid($policyInfo['iddeductible']);
            if(isset($deductibleInfo)){
              $deductible_amount = $deductibleInfo[0]['deductible'];
            }
            $coverageInfo = getCoveragebyid($policyInfo['idcoverage']);
            if(isset($coverageInfo)){
              $coverage_amount = $coverageInfo[0]['coverage'];
            }
            $payCycleInfo = getPayCyclebyid($policyInfo['idpaycycle']);
            $insuredLists = getHealthInsuredLists($policyInfo['id']);
           
            
            require(TEMPLATE_STORE.$this->controler_name.'/print_add.tpl.php');    
        }


        function renewals_queues(){
            global $db;
            $month = $_GET['month'];
            $rate_year = $_GET['rate_year'];
            if($rate_year){
                $year['year'] = $rate_year;
            }
            $check_login = checkLoggedIn(); 
            if(!($check_login && $check_login['user_type']==1)){
                urlredirect(THE_URL."auth/login");  
            exit;
            }

            require(COMMON_TEMPLATES.'user.header.tpl.php');
            require(TEMPLATE_STORE.$this->controler_name.'/renewals_queues.tpl.php'); 
            require(COMMON_TEMPLATES.'user.footer.tpl.php');
        }


        function allRenewalsReports(){  
            global $db;
            $check_login = checkLoggedIn(); 
            if(!($check_login && $check_login['user_type']==1)){
                urlredirect(THE_URL."auth/login");  
            exit;
            }
            require(COMMON_TEMPLATES.'user.header.tpl.php');
            require(TEMPLATE_STORE.$this->controler_name.'/all_renewals_report.tpl.php'); 
            require(COMMON_TEMPLATES.'user.footer.tpl.php');
        }
        function status_report_setup(){  
            global $db;
            $check_login = checkLoggedIn(); 
            if(!($check_login && $check_login['user_type']==1)){
                urlredirect(THE_URL."auth/login");  
            exit;
            }
            require(COMMON_TEMPLATES.'user.header.tpl.php');
            require(TEMPLATE_STORE.$this->controler_name.'/status_report_stp.tpl.php'); 
            require(COMMON_TEMPLATES.'user.footer.tpl.php');
        }

        function renewal_status_report($params = array()){  
            global $db;
            $check_login = checkLoggedIn(); 
            if(!($check_login && $check_login['user_type']==1)){
                urlredirect(THE_URL."auth/login");  
            exit;
            }
            $month = $params[0];
            $rate_year = $params[1];
            $statusRenewalList = RenewalStatusReport($month,$rate_year);
            require(COMMON_TEMPLATES.'user.header.tpl.php');
            require(TEMPLATE_STORE.$this->controler_name.'/renewal_status_report.tpl.php'); 
            require(COMMON_TEMPLATES.'user.footer.tpl.php');
        }


        function renewals_eng_specific($params = array()){
            global $db;
            $check_login = checkLoggedIn(); 
            if(!($check_login && $check_login['user_type']==1)){
                urlredirect(THE_URL."auth/login");  
            exit;
            }

            $month = $params[0];
            $year_id = $params[1];
            $agent1 = $params[2];
            $get_year = get_rate_year_by_id($year_id);
            $year = $get_year['year'];
            require(TEMPLATE_STORE.$this->controler_name.'/renewal_eng_specific.tpl.php');    
        }

        function renewals_span_specific($params = array()){
            global $db;
            $check_login = checkLoggedIn(); 
            if(!($check_login && $check_login['user_type']==1)){
                urlredirect(THE_URL."auth/login");  
            exit;
            }

            $month = $params[0];
            $year_id = $params[1];
            $agent1 = $params[2];
            $get_year = get_rate_year_by_id($year_id);
            $year = $get_year['year'];
            require(TEMPLATE_STORE.$this->controler_name.'/renewal_span_specific.tpl.php');    
        }


        function add_renewals_eng_specific($params = array()){
            global $db;
            $check_login = checkLoggedIn(); 
            if(!($check_login && $check_login['user_type']==1)){
                urlredirect(THE_URL."auth/login");  
            exit;
            }

            $month = $params[0];
            $year_id = $params[1];
            $agent1 = $params[2];
            $get_year = get_rate_year_by_id($year_id);
            $year = $get_year['year'];
            require(TEMPLATE_STORE.$this->controler_name.'/add_renewal_specific_eng.tpl.php');    
        }

        function add_renewals_span_specific($params = array()){
            global $db;
            $check_login = checkLoggedIn(); 
            if(!($check_login && $check_login['user_type']==1)){
                urlredirect(THE_URL."auth/login");  
            exit;
            }

            $month = $params[0];
            $year_id = $params[1];
            $agent1 = $params[2];
            $get_year = get_rate_year_by_id($year_id);
            $year = $get_year['year'];
            require(TEMPLATE_STORE.$this->controler_name.'/add_renewal_specific_span.tpl.php');    
        }

        function over24($params = array()){
            global $db;
            $check_login = checkLoggedIn(); 
            if(!($check_login && $check_login['user_type']==1)){
                urlredirect(THE_URL."auth/login");  
            exit;
            }
            $month = $params[0];
            $year_id = $params[1];
            $agent1 = $params[2];
            $get_year = get_rate_year_by_id($year_id);
            $year = $get_year['year'];
            require(TEMPLATE_STORE.$this->controler_name.'/over24.tpl.php');    
        }

        function over24_span($params = array()){
            global $db;
            $check_login = checkLoggedIn(); 
            if(!($check_login && $check_login['user_type']==1)){
                urlredirect(THE_URL."auth/login");  
            exit;
            }
            $month = $params[0];
            $year_id = $params[1];
            $agent1 = $params[2];
            $get_year = get_rate_year_by_id($year_id);
            $year = $get_year['year'];
            require(TEMPLATE_STORE.$this->controler_name.'/over24_span.tpl.php');    
        }

        function student($params = array()){
            global $db;
            $check_login = checkLoggedIn(); 
            if(!($check_login && $check_login['user_type']==1)){
                urlredirect(THE_URL."auth/login");  
            exit;
            }
            $month = $params[0];
            $year_id = $params[1];
            $agent1 = $params[2];
            $get_year = get_rate_year_by_id($year_id);
            $year = $get_year['year'];
            require(TEMPLATE_STORE.$this->controler_name.'/student.tpl.php');    
        }

        function student_span($params = array()){
            global $db;
            $check_login = checkLoggedIn(); 
            if(!($check_login && $check_login['user_type']==1)){
                urlredirect(THE_URL."auth/login");  
            exit;
            }
            $month = $params[0];
            $year_id = $params[1];
            $agent1 = $params[2];
            $get_year = get_rate_year_by_id($year_id);
            $year = $get_year['year'];
            require(TEMPLATE_STORE.$this->controler_name.'/student_span.tpl.php');    
        }

        function delivery_requirement($params = array()){
            global $db;
            $check_login = checkLoggedIn(); 
            if(!($check_login && $check_login['user_type']==1)){
                urlredirect(THE_URL."auth/login");  
            exit;
            }
            $month = $params[0];
            $year_id = $params[1];
            $agent1 = $params[2];
            $get_year = get_rate_year_by_id($year_id);
            $year = $get_year['year'];
            require(TEMPLATE_STORE.$this->controler_name.'/delivery_requirement.tpl.php');    
        }

         function delivery_requirement_span($params = array()){
            global $db;
            $check_login = checkLoggedIn(); 
            if(!($check_login && $check_login['user_type']==1)){
                urlredirect(THE_URL."auth/login");  
            exit;
            }
            $month = $params[0];
            $year_id = $params[1];
            $agent1 = $params[2];
            $get_year = get_rate_year_by_id($year_id);
            $year = $get_year['year'];
            require(TEMPLATE_STORE.$this->controler_name.'/delivery_requirement_span.tpl.php');    
        }

         function print_delivery_request($params = array()){
            global $db;
            $check_login = checkLoggedIn(); 
            if(!($check_login && $check_login['user_type']==1)){
                urlredirect(THE_URL."auth/login");  
            exit;
            }
            $month = $params[0];
            $year_id = $params[1];
            $agent1 = $params[2];
            $get_year = get_rate_year_by_id($year_id);
            $year = $get_year['year'];
            require(TEMPLATE_STORE.$this->controler_name.'/print_delivery_request.tpl.php');    
        }

        function delivery_request($params = array()){  
            global $db;
            $check_login = checkLoggedIn(); 
            if(!($check_login && $check_login['user_type']==1)){
                urlredirect(THE_URL."auth/login");  
            exit;
            }
            $month = $params[0];
            $year = $params[1];
            $agent1 = $params[2];
            require(COMMON_TEMPLATES.'user.header.tpl.php');
            require(TEMPLATE_STORE.$this->controler_name.'/delivery_request.tpl.php'); 
            require(COMMON_TEMPLATES.'user.footer.tpl.php');
        }
        
        function claria_express($params = array()){  
            global $db;
            $check_login = checkLoggedIn(); 
            if(!($check_login && $check_login['user_type']==1)){
                urlredirect(THE_URL."auth/login");  
            exit;
            }
            $month = $params[0];
            $year_id = $params[1];
            $agent1 = $params[2];
            $get_year = get_rate_year_by_id($year_id);
            $year = $get_year['year'];
            require(COMMON_TEMPLATES.'user.header.tpl.php');
            require(TEMPLATE_STORE.$this->controler_name.'/claria_express.tpl.php'); 
            require(COMMON_TEMPLATES.'user.footer.tpl.php');
        }

         function pending_renewals_list($params = array()){  
             global $db;
            $check_login = checkLoggedIn(); 
            if(!($check_login && $check_login['user_type']==1)){
                urlredirect(THE_URL."auth/login");  
            exit;
            }
            $month = $params[0];
            $year = $params[1];
           
            require(COMMON_TEMPLATES.'user.header.tpl.php');
            require(TEMPLATE_STORE.$this->controler_name.'/pending_renewals_list.tpl.php'); 
            require(COMMON_TEMPLATES.'user.footer.tpl.php');
        }

        function renewal_all_interim($params = array()){  
             global $db;
            $check_login = checkLoggedIn(); 
            if(!($check_login && $check_login['user_type']==1)){
                urlredirect(THE_URL."auth/login");  
            exit;
            }

            $month = $params[0];
            $year_id = $params[1];
            $agent1 = $params[2];
            $get_year = get_rate_year_by_id($year_id);
            $year = $get_year['year'];
          
            require(TEMPLATE_STORE.$this->controler_name.'/renewal_all_interim.tpl.php');
        }

        function renewal_all_interim_span($params = array()){  
             global $db;
            $check_login = checkLoggedIn(); 
            if(!($check_login && $check_login['user_type']==1)){
                urlredirect(THE_URL."auth/login");  
            exit;
            }

            $month = $params[0];
            $year_id = $params[1];
            $agent1 = $params[2];
            $get_year = get_rate_year_by_id($year_id);
            $year = $get_year['year'];
          
            require(TEMPLATE_STORE.$this->controler_name.'/renewal_all_interim_span.tpl.php');
        }

         function pending_renewals_rateup($params = array()){  
             global $db;
            $check_login = checkLoggedIn(); 
            if(!($check_login && $check_login['user_type']==1)){
                urlredirect(THE_URL."auth/login");  
            exit;
            }
            $month = $params[0];
            $year = $params[1];
    
            require(COMMON_TEMPLATES.'user.header.tpl.php');
            require(TEMPLATE_STORE.$this->controler_name.'/pending_renewals_rateup.tpl.php'); 
            require(COMMON_TEMPLATES.'user.footer.tpl.php');
        }

        function agentl1_renewals_queues($params = array()){
            global $db;
            $month = $params[0];
            $year = $params[1];
            $agent1 = $params[2];
    
            $check_login = checkLoggedIn(); 
            if(!($check_login && $check_login['user_type']==1)){
                urlredirect(THE_URL."auth/login");  
            exit;
            }
            require(COMMON_TEMPLATES.'user.header.tpl.php');
            require(TEMPLATE_STORE.$this->controler_name.'/agentl1_renewals_queues.tpl.php'); 
            require(COMMON_TEMPLATES.'user.footer.tpl.php');
        }
} //end of Renewals Class
?>