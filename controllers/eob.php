<?php
/**
 * @author kodetraveller 
 * @copyright 2021
 */
// Class EOB
class eob
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
			$this->controler_name = 'eob';
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
            $this->eob_main($params);
		}
        // 03rd February 2021
        function eob_main($params = array()){	 
            $check_login = checkLoggedIn();	
            if(!($check_login && $check_login['user_type']==1)){
                urlredirect(THE_URL."auth/login");	
            exit;
            }
            require(COMMON_TEMPLATES.'user.header.tpl.php');
            require(TEMPLATE_STORE.$this->controler_name.'/eob_main.tpl.php');	
            require(COMMON_TEMPLATES.'user.footer.tpl.php');
	    }
        
        function providers($params = array()){
            global $db;	 
            $check_login = checkLoggedIn();	
            if(!($check_login && $check_login['user_type']==1)){
                urlredirect(THE_URL."auth/login");	
            exit;
            }
            require(COMMON_TEMPLATES.'user.header.tpl.php');
            require(TEMPLATE_STORE.$this->controler_name.'/providers.tpl.php');	
            require(COMMON_TEMPLATES.'user.footer.tpl.php');
        }
        // 03rd February 2021
        function addNewProvider($params = array()){
            global $db;	 
            $check_login = checkLoggedIn();	
            if(!($check_login && $check_login['user_type']==1)){
                urlredirect(THE_URL."auth/login");	
            exit;
            }
            require(COMMON_TEMPLATES.'user.header.tpl.php');
            require(TEMPLATE_STORE.$this->controler_name.'/providerInfo.tpl.php');	
            require(COMMON_TEMPLATES.'user.footer.tpl.php');
        }
        // 03rd February 2021
        function editProvider($params = array()){
            global $db, $provider_info;
            $provider_id = $params[0];
            $provider_info = $db->getRowsArray('providers', $provider_id);
            $provider_info = $provider_info[0];
            
            $check_login = checkLoggedIn();	
            if(!($check_login && $check_login['user_type']==1)){
                urlredirect(THE_URL."auth/login");	
            exit;
            }

            require(COMMON_TEMPLATES.'user.header.tpl.php');
            require(TEMPLATE_STORE.$this->controler_name.'/providerInfo.tpl.php');	
            require(COMMON_TEMPLATES.'user.footer.tpl.php');
        }
        // 03rd February 2021
        function remarksCode(){
            global $db;
            $check_login = checkLoggedIn();	
            if(!($check_login && $check_login['user_type']==1)){
                urlredirect(THE_URL."auth/login");	
            exit;
            }
            require(COMMON_TEMPLATES.'user.header.tpl.php');
            require(TEMPLATE_STORE.$this->controler_name.'/remarksCode.tpl.php');	
            require(COMMON_TEMPLATES.'user.footer.tpl.php');
        }
        // 03rd February 2021
        function serviceCode(){
            global $db;
            $check_login = checkLoggedIn();	
            if(!($check_login && $check_login['user_type']==1)){
                urlredirect(THE_URL."auth/login");	
            exit;
            }
            require(COMMON_TEMPLATES.'user.header.tpl.php');
            require(TEMPLATE_STORE.$this->controler_name.'/serviceCode.tpl.php');	
            require(COMMON_TEMPLATES.'user.footer.tpl.php');
        }
        // 04th February 2021
        function eob_control(){
            global $db;
            $check_login = checkLoggedIn();	
            if(!($check_login && $check_login['user_type']==1)){
                urlredirect(THE_URL."auth/login");	
            exit;
            }
            require(COMMON_TEMPLATES.'user.header.tpl.php');
            require(TEMPLATE_STORE.$this->controler_name.'/eob_control.tpl.php');	
            require(COMMON_TEMPLATES.'user.footer.tpl.php');
        }

        // 04th February 2021
        function eob_queue(){
            global $db;
            $check_login = checkLoggedIn();	
            if(!($check_login && $check_login['user_type']==1)){
                urlredirect(THE_URL."auth/login");	
            exit;
            }
            require(COMMON_TEMPLATES.'user.header.tpl.php');
            require(TEMPLATE_STORE.$this->controler_name.'/eob_queue.tpl.php');	
            require(COMMON_TEMPLATES.'user.footer.tpl.php');
        }

        // 04th February 2021
        function eob_check_printing(){
            global $db;
            $check_login = checkLoggedIn();	
            if(!($check_login && $check_login['user_type']==1)){
                urlredirect(THE_URL."auth/login");	
            exit;
            }
            require(COMMON_TEMPLATES.'user.header.tpl.php');
            require(TEMPLATE_STORE.$this->controler_name.'/eob_check_printing.tpl.php');	
            require(COMMON_TEMPLATES.'user.footer.tpl.php');
        }
        
        // 04th February 2021
        function change_ach_wire(){
            global $db;
            $check_login = checkLoggedIn();	
            if(!($check_login && $check_login['user_type']==1)){
                urlredirect(THE_URL."auth/login");	
            exit;
            }
            require(COMMON_TEMPLATES.'user.header.tpl.php');
            require(TEMPLATE_STORE.$this->controler_name.'/change_ach_wire.tpl.php');	
            require(COMMON_TEMPLATES.'user.footer.tpl.php');
        }
        
        // 04th February 2021
        function claim_total_dashboard(){
            global $db;
            $check_login = checkLoggedIn();	
            if(!($check_login && $check_login['user_type']==1)){
                urlredirect(THE_URL."auth/login");	
            exit;
            }
            require(COMMON_TEMPLATES.'user.header.tpl.php');
            require(TEMPLATE_STORE.$this->controler_name.'/claim_total_dashboard.tpl.php');	
            require(COMMON_TEMPLATES.'user.footer.tpl.php');
        }
        // Added on 05 February 2021
        function allEOBReports(){
            global $db;
            $check_login = checkLoggedIn();	
            if(!($check_login && $check_login['user_type']==1)){
                urlredirect(THE_URL."auth/login");	
            exit;
            }
            require(COMMON_TEMPLATES.'user.header.tpl.php');
            require(TEMPLATE_STORE.$this->controler_name.'/allEOBReports.tpl.php');	
            require(COMMON_TEMPLATES.'user.footer.tpl.php');
        }
        // Added on 05 February 2021
        function report_agent_intro(){
            global $db;
            $check_login = checkLoggedIn();	
            if(!($check_login && $check_login['user_type']==1)){
                urlredirect(THE_URL."auth/login");	
            exit;
            }
            require(COMMON_TEMPLATES.'user.header.tpl.php');
            require(TEMPLATE_STORE.$this->controler_name.'/report_agent_intro.tpl.php');	
            require(COMMON_TEMPLATES.'user.footer.tpl.php');
        }
        // Added on 05 February 2021
        function reportByAgentofPolicies(){
            global $db;
            $check_login = checkLoggedIn();	
            if(!($check_login && $check_login['user_type']==1)){
                urlredirect(THE_URL."auth/login");	
            exit;
            }
            require(COMMON_TEMPLATES.'user.header.tpl.php');
            require(TEMPLATE_STORE.$this->controler_name.'/reportByAgentofPolicies.tpl.php');	
            require(COMMON_TEMPLATES.'user.footer.tpl.php');
        }
        // Added on 05 February 2021
        function eob_policy($params = array()){
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
            require(TEMPLATE_STORE.$this->controler_name.'/eob_policy.tpl.php');	
            require(COMMON_TEMPLATES.'user.footer.tpl.php');
        }
        // Added on 05 February 2021
        function printCheques($params = array()){
            global $db, $chequeInfo;
            $cheque_id = $params[0];
            $chequeInfo = getChequesInfobyID($cheque_id);
            $check_login = checkLoggedIn();	
            if(!($check_login && $check_login['user_type']==1)){
                urlredirect(THE_URL."auth/login");	
            exit;
            }
            //require(COMMON_TEMPLATES.'user.header.tpl.php');
            require(TEMPLATE_STORE.$this->controler_name.'/printCheques.tpl.php');	
            //require(COMMON_TEMPLATES.'user.footer.tpl.php');
        }

        function reportByAgentofAllClaims(){
            global $db;
            $check_login = checkLoggedIn();	
            if(!($check_login && $check_login['user_type']==1)){
                urlredirect(THE_URL."auth/login");	
            exit;
            }
            require(COMMON_TEMPLATES.'user.header.tpl.php');
            require(TEMPLATE_STORE.$this->controler_name.'/reportByAgentofAllClaims.tpl.php');	
            require(COMMON_TEMPLATES.'user.footer.tpl.php');
        }

        function reportByAgentofAllPolicies(){
           global $db;
            $check_login = checkLoggedIn();	
            if(!($check_login && $check_login['user_type']==1)){
                urlredirect(THE_URL."auth/login");	
            exit;
            }
            require(COMMON_TEMPLATES.'user.header.tpl.php');
            require(TEMPLATE_STORE.$this->controler_name.'/reportByAgentofAllPolicies.tpl.php');	
            require(COMMON_TEMPLATES.'user.footer.tpl.php'); 
        }

        function report_country_intro(){
            global $db;
            $check_login = checkLoggedIn();	
            if(!($check_login && $check_login['user_type']==1)){
                urlredirect(THE_URL."auth/login");	
            exit;
            }
            require(COMMON_TEMPLATES.'user.header.tpl.php');
            require(TEMPLATE_STORE.$this->controler_name.'/report_country_intro.tpl.php');	
            require(COMMON_TEMPLATES.'user.footer.tpl.php');
        }

        function reportByCountryofPolicies(){
            global $db;
            $check_login = checkLoggedIn();	
            if(!($check_login && $check_login['user_type']==1)){
                urlredirect(THE_URL."auth/login");	
            exit;
            }
            require(COMMON_TEMPLATES.'user.header.tpl.php');
            require(TEMPLATE_STORE.$this->controler_name.'/reportByCountryofPolicies.tpl.php');	
            require(COMMON_TEMPLATES.'user.footer.tpl.php');
        }

        function reportByCountryofAllClaims(){
            global $db;
            $check_login = checkLoggedIn();	
            if(!($check_login && $check_login['user_type']==1)){
                urlredirect(THE_URL."auth/login");	
            exit;
            }
            require(COMMON_TEMPLATES.'user.header.tpl.php');
            require(TEMPLATE_STORE.$this->controler_name.'/reportByCountryofAllClaims.tpl.php');	
            require(COMMON_TEMPLATES.'user.footer.tpl.php');
        }

        function reportByCountryofAllPolicies(){
            global $db;
            $check_login = checkLoggedIn();	
            if(!($check_login && $check_login['user_type']==1)){
                urlredirect(THE_URL."auth/login");	
            exit;
            }
            require(COMMON_TEMPLATES.'user.header.tpl.php');
            require(TEMPLATE_STORE.$this->controler_name.'/reportByCountryofAllPolicies.tpl.php');	
            require(COMMON_TEMPLATES.'user.footer.tpl.php'); 
        }

        function deleteCheckbyPolicy($params = array()){
            global $db, $chequeInfo;
            $policy_id = $params[0];
            $chequeInfo = getEOBsByCheckPaytype(); 
            $check_login = checkLoggedIn();	
            if(!($check_login && $check_login['user_type']==1)){
                urlredirect(THE_URL."auth/login");	
            exit;
            }
            require(COMMON_TEMPLATES.'user.header.tpl.php');
            require(TEMPLATE_STORE.$this->controler_name.'/deleteCheckbyPolicy.tpl.php');	
            require(COMMON_TEMPLATES.'user.footer.tpl.php'); 
        }

        // 09 February 2021
        function printIndividualEOB($params = array()){
            global $db;
            $eob_id = $_GET['eob_id'];
            
            $check_login = checkLoggedIn();	
            if(!($check_login && $check_login['user_type']==1)){
                urlredirect(THE_URL."auth/login");	
            exit;
            }

            //$claimInfo = getClaimInfoByID($claimID);
            //$policyInfo = getSinglePolicy($policyID);
            $eobInfo = getEOBTableInfobyID($eob_id);
            $claimInfo = getClaimInfoByID($eobInfo[0]['id_claims']);
            $policyInfo = getSinglePolicy($eobInfo[0]['id_policy']);
            //print_r($policyInfo);
            
            require(TEMPLATE_STORE.$this->controler_name.'/printIndividualEOB.tpl.php');	
        }
        // 09 February 2021
        function printApprovalSheet($params = array()){
            global $db;
            $eob_id = $params[0];
            $eobTableInfo =getEOBTableInfobyID($eob_id);
            $eobInfo = getEOBDetailbyID($eobTableInfo[0]['id_eob_detail']);
            //print_r($eobInfo);
            require(TEMPLATE_STORE.$this->controler_name.'/printApprovalSheet.tpl.php');
        }

        function report_bdx_intro(){
             global $db;
            
            $check_login = checkLoggedIn();	
            if(!($check_login && $check_login['user_type']==1)){
                urlredirect(THE_URL."auth/login");	
            exit;
            }
            require(COMMON_TEMPLATES.'user.header.tpl.php');
            require(TEMPLATE_STORE.$this->controler_name.'/report_bdx_intro.tpl.php');	
            require(COMMON_TEMPLATES.'user.footer.tpl.php'); 
        }

        function eob_bdx_report($params = array()){
            global $db;
            $check_login = checkLoggedIn();	
            if(!($check_login && $check_login['user_type']==1)){
                urlredirect(THE_URL."auth/login");	
            exit;
            }
            $monthYear = $_GET['monthYear'];
            require(COMMON_TEMPLATES.'user.header.tpl.php');
            require(TEMPLATE_STORE.$this->controler_name.'/eob_bdx_report.tpl.php');	
            require(COMMON_TEMPLATES.'user.footer.tpl.php');
        }
        // 10 February 2021
        function printAllEOBIntro(){
            global $db;
            $check_login = checkLoggedIn();	
            if(!($check_login && $check_login['user_type']==1)){
                urlredirect(THE_URL."auth/login");	
            exit;
            }
            require(COMMON_TEMPLATES.'user.header.tpl.php');
            require(TEMPLATE_STORE.$this->controler_name.'/printAllEOBIntro.tpl.php');	
            require(COMMON_TEMPLATES.'user.footer.tpl.php');
        }
        // 12 Februaru 2021
        function printEOBs(){
            global $db;
            $check_login = checkLoggedIn();	
            if(!($check_login && $check_login['user_type']==1)){
                urlredirect(THE_URL."auth/login");	
            exit;
            }
            $policy_id = $_GET['policy_id'];
            $insured_id = $_GET['insured'];
            $eob_start = dateDBFormat($_GET['eob_start']);
            $eob_end = dateDBFormat($_GET['eob_end']);

            $params= array(
                'id_policy'     => $policy_id,
                'id_insured'    => $insured_id,
                'period_start'  => $eob_start,
                'period_end'    => $eob_end    
            );

            $policyInfo = getSinglePolicy($policy_id);
            $eobInfo = getEOBsInDateRange($params);

            require(TEMPLATE_STORE.$this->controler_name.'/printEOBs.tpl.php');
        }

        // 12 February 2021
        function add_eob($params = array()){
           global $db; 
           $check_login = checkLoggedIn();	
            if(!($check_login && $check_login['user_type']==1)){
                urlredirect(THE_URL."auth/login");	
            exit;
            }
            $policy_id = $params[0];
            $claim_id = $params[1];

            $policyInfo = getSinglePolicy($policy_id);
            $claimInfo = getClaimInfoByID($claim_id);

            require(COMMON_TEMPLATES.'user.header.tpl.php');
            require(TEMPLATE_STORE.$this->controler_name.'/eob_form.tpl.php');	
            require(COMMON_TEMPLATES.'user.footer.tpl.php');
        }

        // 15 February 2021
        function edit_eob($params = array()){
           global $db; 
           $check_login = checkLoggedIn();	
            if(!($check_login && $check_login['user_type']==1)){
                urlredirect(THE_URL."auth/login");	
            exit;
            }
            $policy_id = $params[0];
            $eob_id = $params[1];

            $policyInfo = getSinglePolicy($policy_id);
            $eobInfo = getEOBTableInfobyID($eob_id);
            if($eobInfo){
                $eobDetailInfo = getEOBInfobyID($eob_id);
            }
            //print_r($eobInfo); die();
            require(COMMON_TEMPLATES.'user.header.tpl.php');
            require(TEMPLATE_STORE.$this->controler_name.'/eob_form.tpl.php');	
            require(COMMON_TEMPLATES.'user.footer.tpl.php');
        }
    
    // 16th June 2021
        function eob_process(){
            global $db;
            $check_login = checkLoggedIn();	
            if(!($check_login && $check_login['user_type']==1)){
                urlredirect(THE_URL."auth/login");	
            exit;
            }
            $claim_id = $_GET['claim_id'];
           
            $claimInfo = getClaimInfoByID($claim_id);
            require(COMMON_TEMPLATES.'user.header.tpl.php');
            require(TEMPLATE_STORE.$this->controler_name.'/eob_process.tpl.php');	
            require(COMMON_TEMPLATES.'user.footer.tpl.php');
        }
        function print_all_selected_eob($params = array()){
			global $db;
            $check_login = checkLoggedIn();	
            if(!($check_login && $check_login['user_type']==1)){
                urlredirect(THE_URL."auth/login");	
            exit;
            }
			require(TEMPLATE_STORE.$this->controler_name.'/print_all_selected_eob.tpl.php'); 
			
	}
        
} //end of EOB Class
?>