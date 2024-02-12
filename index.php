<?php
/**
 * RAW PHP SCRIPT
 *
 * 
 * @package		RAWPHPSCRIPT
 * @author		:)
 * @copyright	Copyright (c) 2009.
 * @since		Version 1.0
 */
	error_reporting(E_ERROR);  // Wanna View Errors?
	//error_reporting(0);
	require('includes/router.php');
	require('includes/config.php');
    
 global $conn;
 $router = make_route();
 //print_r($router);
 /*if($router['controller'] != 'view_lists'){
 $auth_users = array('trz_admin' => 'vTg34%ss');
 if(!$_SESSION['auth_approved']){
 $u= stripcslashes($_GET['u']);
 $p= stripcslashes($_GET['p']);
 if(empty($u) || empty($p) || $auth_users[$u] != $p){
    die("Unauthorized Access...");
 }else{
    $_SESSION['auth_approved'] = 1;
    header('Location: '.SCRIPT_URL);
    exit;
 }
 
 }elseif($_GET['u'] && $_GET['p']){
    header('Location: '.SCRIPT_URL);
    exit; 
 }
} 
*/
/*    $realm = 'Restricted area';

//user => password



if (empty($_SERVER['PHP_AUTH_DIGEST'])) {
    header('HTTP/1.1 401 Unauthorized');
    header('WWW-Authenticate: Digest realm="'.$realm.
           '",qop="auth",nonce="'.uniqid().'",opaque="'.md5($realm).'"');

    die('Unauthrized Access Restricted...');
} */
	
	
	
	$query_part = $router['query_part'];
	
	$controller = $router['controller'];
	if(!$controller)$controller = DEFAULT_CONTROLER;
	
	$action = $router['action'];
	if(!$action)$action = DEFAULT_FUNCTION;
	
	$action = str_replace("-","_",$router['action']);
	
	$params = '';
	$args = $router['args'];
	
#pre($router); exit;	
	if($args)
	{
		$params = implode(',' , $args); 
	}
	
	#require(COMMON_TEMPLATES.'header.php');
	if(!file_exists(CONTROLLER_STORE.$controller.'.php'))
	{
		$action = $controller;
		$controller = DEFAULT_CONTROLER;				
			require(CONTROLLER_STORE.$controller.'.php');		
			$instance = new $controller;				
			$instance->footer = 1;
            $instance->gerbage = $router['gerbage'];
			$gerbage1 = $router['gerbage'];
			if($gerbage1)
			{
				$sz_gerbage = count($gerbage1);
				if(strlen($gerbage1[0])==2)$g = 1 ;
				else $g = 0;
					
				for( ; $g<$sz_gerbage ; $g++)$gerbage[] = $gerbage1[$g];				
			}
			
			#pre($gerbage);
			
			if(!method_exists ($instance , $action))				
			{
			
				if($gerbage)
				{
					$params = implode(',' , $gerbage);
					$sz_parms = count($gerbage);
									
					$action = 'page_not_found';
					$instance->$action($gerbage);
					
				}
							
			}	
			else
			{
				
				$sz_gerbage = count($gerbage);
				if($gerbage)
				{									
					for($g=1 ; $g<$sz_gerbage ; $g++)$gerbage2[] = $gerbage[$g];				
				}
				#pre($gerbage2);
				$instance->$action($gerbage2);
			}
		
	}// eof if controlar not exist
	else
	{
		
		require(CONTROLLER_STORE.$controller.'.php');
		$instance = new $controller;
		$instance->footer = 1;
        $instance->gerbage = $router['gerbage'];
		if(!method_exists ($instance , $action))
		{
			$action = DEFAULT_FUNCTION;
			$gerbage1 = $router['gerbage'];
			if($gerbage1)
			{
				$sz_gerbage = count($gerbage1);
				if(strlen($gerbage1[0])==2)$g = 2 ;
				else $g = 1;
					
				for( ; $g<$sz_gerbage ; $g++)$gerbage[] = $gerbage1[$g];				
			}
			$instance->$action($gerbage);
		}
		else
			$instance->$action($args);
	}
    
    #pre($instance->gerbage);
	if($instance->footer == 1)
		require(COMMON_TEMPLATES.'footer.php');
    else if($instance->footer == 2)
		require(ADMIN_TEMPLATES.'footer.php');

    state('hst_c' , $instance->controler_name);  
    state('hst_a' , $instance->action);  
?>