<?php
/**
 * @author :)
 * @copyright 2019
 */
class report
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
         $this->reports($params);
		}
        
        function reports($params = array()){	 
                
         $check_login = checkLoggedIn();	
         if(!($check_login && $check_login['user_type']==1)){
            urlredirect(THE_URL."auth/login");	
            exit;
         }
         require(COMMON_TEMPLATES.'user.header.tpl.php');
		 require(TEMPLATE_STORE.$this->controler_name.'/reports.tpl.php');	
		 require(COMMON_TEMPLATES.'user.footer.tpl.php');
		}
           
	}
?>