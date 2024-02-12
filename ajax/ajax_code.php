<?php include('../includes/config.php');
//exit("Action Not Allowed!!!!");	
?>
<?php
global $db;

$action = $_REQUEST['action'];

switch($action)
	{		
		
        case 'test_action':
    
        $data_sucess['sucess'] = $status  ? 1 : 0;
		echo json_encode($data_sucess);
	    break;
        	
	}
die();	
?>