<?php 
ini_set('max_execution_time', 0);  set_time_limit(0); ignore_user_abort(true);
include('../includes/config.php');
//exit("Action Not Allowed!!!!");	
?>
<?php
global $db;
$user_id = state('user_id');
if(!$user_id){
  $data_sucess['sucess'] = 0;  
  $data_sucess['message'] = "Authentication Required.";
  echo json_encode($data_sucess);
  die();  
}


$action = $_REQUEST['action'];

switch($action)
	{		
		
        case 'upload_policy_files':
        $policy_id = trim($_POST['policy_num']);
        $descriptions = $_POST['descriptions'];
        $remove_lists = $_POST['remove_lists'];
        if($policy_id){
            if($_FILES['file']['name']){
                foreach($_FILES['file']['name'] as $key => $value){
                  if(move_uploaded_file($_FILES['file']['tmp_name'][$key], RESOURCE_STORE.'uploads/policies/' . $_FILES['file']['name'][$key])){
                    $file_path = trim(str_replace(BASE_DIR,"",RESOURCE_STORE.'uploads/policies/' . $_FILES['file']['name'][$key]));
                    $temp_file['description'] = $descriptions[$key];
                    $temp_file['file_path'] = $file_path;
                    $uploadedFiles[] = $temp_file;
                  }
                //move_uploaded_file($_FILES['file1']['tmp_name'], 'uploads/' . $_FILES['file1']['name']);
                }
            }else{
               $success = 1;
            }
           
            if($remove_lists){
                removePolicyFiles($policy_id,$remove_lists);
                $success = 1;
            }
           
            if($uploadedFiles){
            addPolicyFiles($policy_id,$uploadedFiles);
            $success = 1;
            } 
            if($success){
                echo "success";
            }
        
        }
        break;           	
	}
die();	
?>