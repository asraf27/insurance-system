<?php



/**

 * @author :)

 * @copyright 2009

 */

	

	class admin

	{		

		var $controler_name;

		var $action;

		var $my_db;

		var $my_session;

		var $user_id;

		var $user_type;

		var $page_title;		

		var $search_key;		

		var $hst_c;		

		var $hst_a;		

		var $page;		

        var $error;

		var $message;

		var $alert;

		

				

		function __construct()

		{

			global $lang,$db,$session;

			

			$this->my_db = $db;

			$this->my_session = $session;								

			$this->controler_name = 'admin';

			$this->user_id = state('user_id');

			

            $this->search_key = state('search_key');

            $this->hst_c = state('hst_c');

            $this->hst_a = state('hst_a');

            

            $this->page = state('page');

            

            $this->error = state('err');

            $this->message = state('msg');

            $this->alert = state('alr');

            

            state('err' , '');

            state('msg' , '');

            state('alr' , '');

            

            

            $this->user_type = getUserType($this->user_id);

            

            if(!$this->user_id)

            {

                $this->alert = 'Authentication Required.';

                $this->login();

                exit;

            }

            

		}

		

		function default_func($params = array())

		{

            #$this->checkAdmin();           

            $this->action = '';

            //require(ADMIN_TEMPLATES.'header.php');

            //require(TEMPLATE_STORE.$this->controler_name.'/dashboard.tpl.php');

            //$this->footer = 2;
            echo "Admin Dashboard";

		}

        

        

        function checkHistory($params = array())

        {

            if(($this->controler_name != $this->hst_c) or ($this->action != $this->hst_a))

            {

                $this->search_key = '';

                $this->page = '';

                state('search_key' , '');

                state('page' , '');

            }

        }

        

        function checkAdmin($flag = 1)

        {

            if($flag == 1)

            {

                if(state('guru') == 1)

                    return true;

                else

                    $this->alert = "Only accessible by Super Admin.";

            }

            else if($flag == 2)

            {

                if((state('guru') == 1) or (state('sub_guru') == 1))

                    return true;

                else

                    $this->alert = "Only accessible by Super Admin or Department Admin.";

                    

            }

            else

                $this->alert = "Illegal termination occured!.";

            

            require(ADMIN_TEMPLATES.'header.php');

            require(TEMPLATE_STORE.$this->controler_name.'/no-entry.tpl.php'); 

            $this->footer = 2;

            exit;

        }

        

        function login($params = array())

        {    
           urlredirect(THE_URL."auth/login");	
           exit;
        }

        

        function logout($params = array())

        {

            if(isset($_COOKIE['user_name']) && isset($_COOKIE['password']))

            {

		    	setcookie("user_name", '', time()-60*60, "/");

		      	setcookie("password", '', time()-60*60, "/");

		      	setcookie("cookie_time", time(), time()-60*60, "/");

			}

			

			$this->my_session->sess_destroy();

			

            urlredirect(ADMIN_URL); 

        }

        

        function showall($params = array())

        {

            state('search_key' , '');

            state('page' , '');

            

            urlredirect(ADMIN_URL.$this->hst_a);

        }

        

		function settings($params = array())

        {

                  

            $this->action = 'settings';

            

            $search_key = 'Search..'; 

            if(isset($_POST['searchSubmitted']))

            {

                $search_key = strtolower(trim($this->my_db->db_input($_POST['search_key'])));

                if(($search_key!='') and ($search_key != 'search..'))

                    $allSettings = $this->my_db->dbQuery("*" , PRFX."settings" , "option_title LIKE '%".$search_key."%' OR option_name LIKE '%".$search_key."%'" , "id","",0);

                  

            }

            else

            {

                $allSettings = $this->my_db->dbQuery("*" , PRFX."settings" , "" , "id");    

            }

                        

            require(ADMIN_TEMPLATES.'header.php');

            require(TEMPLATE_STORE.$this->controler_name.'/settings.tpl.php'); 

            $this->footer = 2;                       

        }

        

		        

        

        function page_not_found($params = array())

        {

            $this->alert = "The requested url can't be executed!";

            

            require(ADMIN_TEMPLATES.'header.php');

            require(TEMPLATE_STORE.$this->controler_name.'/page-not-found.tpl.php');

            

            $this->footer = 2;

        }

        

        function testUploader($params = array())

        {

            $this->action = 'testUploader';

            if(isset($_POST['formSubmitted']))

            {

             

                #pre($_FILES);exit;

                

                $uploaded_result = simpleUploader('file_content',false,"audio");

                pre($uploaded_result);

                

                exit;

                echo $sucess.' files upoaded.';

                pre($uploaded_result);

            }

            

            require(ADMIN_TEMPLATES.'header.php');

            require(TEMPLATE_STORE.$this->controler_name.'/test-uploader.tpl.php');

            

            $this->footer = 2;

        }

        

        

        

        function password($params = array())

		  {

            

			$this->action = 'password';

            

            

            

            if(isset($_POST['formSubmitted']))

            {   

            	

            	$current_pass = $this->my_db->post('current_pass');

            	$new_pass = $this->my_db->post('new_pass');

            	$re_pass = $this->my_db->post('re_pass');

            	

            	if($re_pass != $new_pass){

            		$this->error = "Password and Re-Password doesn't match";

            	}elseif(!$current_pass){

            	 	$this->error = "Enter Current Password";

            	}elseif(!$new_pass){

           		    $this->error = "Enter New Password";

			    }else{

			    	$new_pass = _encode($new_pass);

			    	$current_pass = _encode($current_pass);

			    	$sql = "UPDATE ".PRFX."users SET password = '$new_pass' WHERE user_name ='admin' AND password ='$current_pass' LIMIT 1";

			    	$stats = $this->my_db->edit($sql);

			    	if($stats)

			    	 $this->message = "Password Successfully Changed";

			    	else

			    	 $this->error = "Failed to change Password";

			    }

            	 

            

            }

          

            require(ADMIN_TEMPLATES.'header.php');

            require(TEMPLATE_STORE.$this->controler_name.'/change-password.tpl.php');

            

            $this->footer = 2;

		} 	

       

       

       function myaccount($params = array())

		  {

            

			$this->action = 'myaccount';

            

            

            

            if(isset($_POST['formSubmitted']))

            {   

            	

            	$admin_email = $this->my_db->post('admin_email');

            	$name = $this->my_db->post('name');

            	

            	

            	if($admin_email && $name){

            		$sql = "UPDATE ".PRFX."users SET email = '$admin_email',name='$name' WHERE user_name ='admin' LIMIT 1";

			    	$stats = $this->my_db->edit($sql);

			    	if($stats)

			    	 $this->message = "Information Successfully Saved";

			    	else

			    	 $this->error = "Failed to save Admin Infos";

            	}else{

			    	

			    	$this->error = "Failed to save Admin Infos";

			    }

            	 

            

            }

            

			$admin_info = $this->my_db->select_single("SELECT * FROM ".PRFX."users WHERE user_name 	='admin' LIMIT 1");

          

            require(ADMIN_TEMPLATES.'header.php');

            require(TEMPLATE_STORE.$this->controler_name.'/my-account.tpl.php');

            

            $this->footer = 2;

		} 	
        
        
   function manageusers($params = array())

		{ 

            
            global $userLists;
            
            $this->action = 'manageusers';
            $checkPermission = checkAccessPermission('Admin');
			//$this->checkHistory();
			$res_id = $params[0];

            if(isset($_POST['formSubmitted']))

            {                

            }

            $userLists = getUserLists(20);
            //pre($userLists);
            require(COMMON_TEMPLATES.'user.header.tpl.php');
            require(TEMPLATE_STORE.$this->controler_name.'/user-list.tpl.php');
            require(COMMON_TEMPLATES.'user.footer.tpl.php');

    }
    
    
    
    function add_user($params = array()){
        
            global $userRoles;
        
            $this->action = 'add-user';
		
            if(isset($_POST['formSubmitted']))
            {                
              $user_name = $dbData['name'] = $this->my_db->post('user_name');
              $first_name = $dbData['firstname'] = $this->my_db->post('first_name');
              $last_name = $dbData['lastname'] = $this->my_db->post('last_name');
              $email = $dbData['email'] =  $this->my_db->post('email');
              $password = $this->my_db->post('password');
              $status =   $dbData['active'] = $this->my_db->post('status');
              $user_role = $this->my_db->post('user_role');
              
              $dbData['displayname'] = $first_name ." ".$last_name;
              $dbData['pwd'] = md5($password);
              
              if($user_name && $email && $user_role && $password){
               $duplicate_check = checkUserDuplicate($email,$user_name);
               if(empty($duplicate_check)){
               $userAdd = addUser($dbData);
               if($userAdd){
                addUserRole($userAdd,$user_role,1);
                
                urlredirect(THE_URL."admin/manageusers");	
                exit;
               }
               }else{
                state('err' , $duplicate_check);
               }
              }else{
                  state('err' , "Fill all required field currectly and try again.");
              }

            }

            $userRoles = getUserRoleLists();
            //pre($userRoles);
            require(COMMON_TEMPLATES.'user.header.tpl.php');
            require(TEMPLATE_STORE.$this->controler_name.'/adduser.tpl.php');
            require(COMMON_TEMPLATES.'user.footer.tpl.php'); 
    }
    
    
    function edit_user($params = array()){
       global $userInfo,$userRoles; 
       $this->action = 'edit-user';
			//$this->checkHistory();
			$user_id = $params[0];

            if(isset($_POST['formSubmitted']))
            {                
              $user_name = $dbData['name'] = $this->my_db->post('user_name');
              $first_name = $dbData['firstname'] = $this->my_db->post('first_name');
              $last_name = $dbData['lastname'] = $this->my_db->post('last_name');
              $email = $dbData['email'] =  $this->my_db->post('email');
              $password = $this->my_db->post('password');
              $status =   $dbData['active'] = $this->my_db->post('status');
              $user_role = $this->my_db->post('user_role');
              
              $dbData['displayname'] = $first_name ." ".$last_name;
              if($password)
              $dbData['pwd'] = md5($password);
              
              if($user_name && $email && $user_role){
               $userAdd = addUser($dbData,$user_id);
               if($userAdd){
                addUserRole($user_id,$user_role,1);
                
                urlredirect(THE_URL."admin/manageusers");	
                exit;
               }
                
              }else{
                  state('err' , "Failed to edit user check all required field and try again.");
              }

            }
 
            $userRoles = getUserRoleLists();
            $userInfo = getSingleUser($user_id);
            //pre($userInfo);
            require(COMMON_TEMPLATES.'user.header.tpl.php');
            require(TEMPLATE_STORE.$this->controler_name.'/edituser.tpl.php');
            require(COMMON_TEMPLATES.'user.footer.tpl.php');
    }
    
    
    function delete_user($params = array()){
     $user_id = $params[0];
     if($user_id){
        $stats = removeUser($user_id);
        if($stats)
        state('msg' , 'User successfully removed');
        else
        state('err' , 'Failed to remove user');
     }
     
     urlredirect(THE_URL."admin/manageusers");	
     exit; 
    }   
  
     function intro_groups($params = array())

		{ 

            $this->action = 'intro_groups';

            

			$this->checkHistory();

			

            if(isset($_POST['formSubmitted']))

            {                

                $admin_action = $_POST['admin_action'];

                $res_id_arr = $_POST['res_id_arr']; 

               

				               

                if($res_id_arr)

                {

                    foreach($res_id_arr as $key=>$value)

                    {

                       switch($admin_action){

                  	    case 'enable':

                  	                   updateResourceStatus($value,1);

                  	                   break;

                       	case 'disable':

                       	  			   updateResourceStatus($value,0);

                  	                   break;

                       	case 'delete': 

                       	               removeResourecs($value);

                       	               break;

                       	

                       }

                    }

                   

                }

                else

                    $this->alert = "Nothing Selected.";

                

            }

            

            require(ADMIN_TEMPLATES.'page-num.php');

           

            $sql_list = "SELECT * FROM ".PRFX."resources WHERE res_id != '0' AND res_type ='intro_group'";   

                     

            $temp_search_key = strtolower(trim($this->my_db->db_input($_POST['search_key'])));            

            if($temp_search_key == 'all')

            {

                state('search_key' , '');

                $this->search_key = '';

            }

            else if(($temp_search_key != 'search..') AND ($temp_search_key != ''))

            {

                state('search_key' , $temp_search_key);

                $this->search_key = $temp_search_key;

            }

              

            if($this->search_key != '')

                $sql_list .= "AND (res_type LIKE '%".$this->search_key."%' OR  res_title LIKE '%".$this->search_key."%')";

            else

                $this->search_key = 'Search..';

                        

			$link = ADMIN_URL.$this->action.'/';     

            

            $sql_list .= " ORDER BY res_id";

            

            $pages = make_pagination($sql_list , $page , getSettings('PAGING_PER_PAGE'));

            $sql_list .= " LIMIT ".$pages['start_form'].",".$pages['per_page'];

            #pre($pages);

            #echo $sql_list;exit; 

            $groupList = $this->my_db->simpleSelect($sql_list,0);

            

            

            require(ADMIN_TEMPLATES.'header.php');

            require(TEMPLATE_STORE.$this->controler_name.'/intro-group-list.tpl.php');

            

            $this->footer = 2;

		}

  

  function intro_add_group($params = array())

		{

            

			$this->action = 'intro_add_group';

            

            $res_id = $params[0];

            

            if(isset($_POST['formSubmitted']))

            {   

            	$post_info['res_type'] = "intro_group";

                $post_info['res_id'] = $_POST['res_id'];

                $post_info['res_title'] = $_POST['res_title'];

                $post_info['status'] = $_POST['status'];

                

                $check_group = checkResourceTitleExists($post_info['res_title'],$post_info['res_type'],$post_info['res_id']);

                

				if($check_group){

                  $err = "Group Already Exists";

                  state('err' , $err);

                }

                if($err == ''){

                 $status = manageResources($post_info);

                 

				 if($status)

				   state('msg' , "Group Information Sucessfully Saved");	

		         else

		           state('err' , "Failed to Save Project Information");

		           urlredirect(ADMIN_URL."intro_groups");

			    }	       

                    

            }

            if($res_id)

            {

			  $groupInfo = $this->my_db->querySingle("*" , PRFX."resources" , "res_id = '".$res_id."'");

            }

             

            require(ADMIN_TEMPLATES.'header.php');

            require(TEMPLATE_STORE.$this->controler_name.'/intro-group.tpl.php');

            

            $this->footer = 2;

		}

		

		

		

		

		function intro_manage_group($params = array())

		{ 

            $this->action = 'intro_manage_group';

            

			$this->checkHistory();

			

			$res_id = $params[0];

			

            if(isset($_POST['formSubmitted']))

            {                

                 

                $res_id = $_POST['res_id'];

                $ChangeImage = $this->my_db->post('ChangeImage');

                if($ChangeImage && $_FILES){

                	$stats = manageIntroFormFiles($_FILES,$res_id);

                	if($stats['success']==1)

                	 $this->message = $stats['message'];

                	elseif($stats['error']==1)

                	 $this->error = $stats['error'];

                }

            }

            

            $group_lists = getIntroGroupList();

            

            $res_id = $res_id ? $res_id : $group_lists[0]['res_id'];

            $groupInfo = getIntroGroup($res_id);

            

            $introImages = getIntroGroupImages($res_id);

            

            require(ADMIN_TEMPLATES.'header.php');

            require(TEMPLATE_STORE.$this->controler_name.'/intro-manage-group.tpl.php');

            

            $this->footer = 2;

		}

		

		

		

		function intro_manage_icon($params = array())

		{ 

            $this->action = 'intro_manage_icon';

            

			$this->checkHistory();

			

            if(isset($_POST['formSubmitted']))

            {                

                $ChangeImage = $this->my_db->post('ChangeImage');

                if($ChangeImage && $_FILES){

                	$stats = manageIntroIcons($_FILES);

                	if($stats['success']==1)

                	 $this->message = $stats['message'];

                	elseif($stats['error']==1)

                	 $this->error = $stats['error'];

                }

            }

            

            $introIcons = introGetIcons();

            require(ADMIN_TEMPLATES.'header.php');

            require(TEMPLATE_STORE.$this->controler_name.'/intro-manage-icon.tpl.php');

            

            $this->footer = 2;

		}

		

	    function home_manage_sliders($params = array()){

		    $this->action = 'home_manage_sliders';

			$this->checkHistory();

			

            if(isset($_POST['formSubmitted']))

            {                

               

			    $admin_action = $this->my_db->post('admin_action');

			    $img_id_arr = $this->my_db->post('img_id_arr');

			   

			    $addNewImage = $this->my_db->post('addNewImage');

                if($addNewImage && $_FILES){

                	$stats = addImageRow('home_slide_image','images/sliders/',array("image_properties"=>'home_page_sliders'));

                	if($stats)

                	 $this->message = "Image Successfully Added";

                	elseif($stats['error']==1)

                	 $this->error = "Falied To Add Image";

                }

                

                

                if($admin_action == 'delete' && $img_id_arr){

                	foreach($img_id_arr as $key => $value){

                		deleteImageRow($value);

                	}

                }

            }

            

            

            $slider_images = getImagesByProperties('home_page_sliders');

            

            

            require(ADMIN_TEMPLATES.'header.php');

            require(TEMPLATE_STORE.$this->controler_name.'/home-sliders.tpl.php');

            

            $this->footer = 2;

	    }

	    

	    

     function home_manage_articles($params = array())

		{ 

            $this->action = 'home_manage_articles';

            

			$this->checkHistory();

			

            if(isset($_POST['formSubmitted']))

            {                

                $admin_action = $_POST['admin_action'];

                $res_id_arr = $_POST['res_id_arr']; 

               

				               

                if($res_id_arr)

                {

                    foreach($res_id_arr as $key=>$value)

                    {

                       switch($admin_action){

                  	    case 'enable':

                  	                   updateResourceStatus($value,1);

                  	                   break;

                       	case 'disable':

                       	  			   updateResourceStatus($value,0);

                  	                   break;

                       	case 'delete': 

                       	               removeResourecs($value);

                       	               break;

                       	

                       }

                    }

                   

                }

                else

                    $this->alert = "Nothing Selected.";

                

            }

            

            require(ADMIN_TEMPLATES.'page-num.php');

           

            $sql_list = "SELECT * FROM ".PRFX."resources WHERE res_id != '0' AND res_type = 'home_article'";   

                     

            $temp_search_key = strtolower(trim($this->my_db->db_input($_POST['search_key'])));            

            if($temp_search_key == 'all')

            {

                state('search_key' , '');

                $this->search_key = '';

            }

            else if(($temp_search_key != 'search..') AND ($temp_search_key != ''))

            {

                state('search_key' , $temp_search_key);

                $this->search_key = $temp_search_key;

            }

              

            if($this->search_key != '')

                $sql_list .= "AND (res_type LIKE '%".$this->search_key."%' OR  res_title LIKE '%".$this->search_key."%' OR res_desc LIKE '%".$this->search_key."%')";

            else

                $this->search_key = 'Search..';

                        

			$link = ADMIN_URL.$this->action.'/';     

            

            $sql_list .= " ORDER BY res_order asc,res_id desc";

            

            $pages = make_pagination($sql_list , $page , getSettings('PAGING_PER_PAGE'));

            $sql_list .= " LIMIT ".$pages['start_form'].",".$pages['per_page'];

            #pre($pages);

            #echo $sql_list;exit; 

            $articleLists = $this->my_db->simpleSelect($sql_list,0);

            

            //pre($articleLists);

            require(ADMIN_TEMPLATES.'header.php');

            require(TEMPLATE_STORE.$this->controler_name.'/manage-articles.tpl.php');

            

            $this->footer = 2;

		}

	    

	     function home_add_article($params = array())

		  {

            

			$this->action = 'home_add_article';

            

            $res_id = $params[0];

            

            if(isset($_POST['formSubmitted'])){ 

            	

            	$res_id = $this->my_db->post('res_id');

            	$post_info['res_title'] = $this->my_db->post('res_title');

            	$post_info['res_desc'] = $this->my_db->post('res_desc');

            	$post_info['content_type'] = $this->my_db->post('content_type');

            	$post_info['res_file_type'] = $this->my_db->post('res_file_type');

            	$post_info['res_order'] = $this->my_db->post('res_order');

            	$post_info['status'] = $this->my_db->post('status');

            	$post_info['res_type'] = 'home_article';

            	

            	if($_FILES['target_file']['name']){



            	 switch($post_info['res_file_type']){

            		case 'video':

            		        $article_file_info = manageResourceFormFiles('target_file','video/',$res_id,'video');

            		        break;

    		        case 'flash':

            		        $article_file_info = manageResourceFormFiles('target_file','',$res_id,'falsh');

            		        break;

    		        case 'file':

            		        $article_file_info = manageResourceFormFiles('target_file','images/news/',$res_id,'falsh');

            		        break;

            		      

            	  }

            	  

            	}

            	

            	$post_info['file_path'] = $article_file_info['file_path'];

            	$post_info['file_name'] = $article_file_info['file_name'];

            	

            	if($post_info['res_file_type']=='url'){

            	$post_info['target_url'] = $this->my_db->post('target_url');

            	$post_info['target_window'] = $this->my_db->post('target_window');

                }

               

               $article_stats = addResource($post_info,$res_id);

               if($_FILES['article_image']['name']){

	               if($article_stats || $res_id){

	               	 $article_stats = $article_stats ?$article_stats :  $res_id;

	               	 addImageRow('article_image','images/news/',array("res_id"=>$article_stats, "image_properties" =>'home_article'),0,$res_id);

              	   }

               }

               

               if($_FILES['article_thumb']['name']){

	               if($article_stats || $res_id){

	               	 $article_stats = $article_stats ?$article_stats :  $res_id;

	               	 addImageRow('article_thumb','images/news/',array("res_id"=>$article_stats, "image_properties" =>'home_article_thumb'),0,$res_id);

              	   }

               }

               if($article_stats){

               	 $msg = $res_id ?"Article Successfully Modified" : "Article Successfully Added";

               	 state('msg' , $msg);

               	 

          	 	  

               }else{

               	

               	$err = $res_id ?"Failed to modify Article" : "Failed to Add Article";

               	 state('err' , $err);

               }

               

		           

	           urlredirect(ADMIN_URL."home_manage_articles");

			  

            }

            

            if($res_id)

            {

			  $articleInfo = getSingleResource(array("res_id"=>$res_id));

            }

             

            require(ADMIN_TEMPLATES.'header.php');

            require(TEMPLATE_STORE.$this->controler_name.'/add-article.tpl.php');

            

            $this->footer = 2;

		}

		

		

		

		function manage_newsletters($params = array())

		{ 

            $this->action = 'manage_newsletters';

            

			$this->checkHistory();

			

            if(isset($_POST['formSubmitted']))

            {                

                $admin_action = $_POST['admin_action'];

                $res_id_arr = $_POST['res_id_arr']; 

               

				               

                if($res_id_arr)

                {

                    foreach($res_id_arr as $key=>$value)

                    {

                       switch($admin_action){

                  	    case 'enable':

                  	                   updateResourceStatus($value,1);

                  	                   break;

                       	case 'disable':

                       	  			   updateResourceStatus($value,0);

                  	                   break;

                       	case 'delete': 

                       	               removeResourecs($value);

                       	               break;

                       	

                       }

                    }

                   

                }

                else

                    $this->alert = "Nothing Selected.";

                

            }

            

            require(ADMIN_TEMPLATES.'page-num.php');

           

            $sql_list = "SELECT * FROM ".PRFX."resources WHERE res_id != '0' AND res_type ='news_letter'";   

                     

            $temp_search_key = strtolower(trim($this->my_db->db_input($_POST['search_key'])));            

            if($temp_search_key == 'all')

            {

                state('search_key' , '');

                $this->search_key = '';

            }

            else if(($temp_search_key != 'search..') AND ($temp_search_key != ''))

            {

                state('search_key' , $temp_search_key);

                $this->search_key = $temp_search_key;

            }

              

            if($this->search_key != '')

                $sql_list .= "AND (res_type LIKE '%".$this->search_key."%' OR  res_title LIKE '%".$this->search_key."%' OR 	res_desc LIKE '%".$this->search_key."%')";

            else

                $this->search_key = 'Search..';

                        

			$link = ADMIN_URL.$this->action.'/';     

            

            $sql_list .= " ORDER BY res_id";

            

            $pages = make_pagination($sql_list , $page , getSettings('PAGING_PER_PAGE'));

            $sql_list .= " LIMIT ".$pages['start_form'].",".$pages['per_page'];

            #pre($pages);

            #echo $sql_list;exit; 

            $newsletterList = $this->my_db->simpleSelect($sql_list,0);

            

            

            require(ADMIN_TEMPLATES.'header.php');

            require(TEMPLATE_STORE.$this->controler_name.'/manage-news-letters.tpl.php');

            

            $this->footer = 2;

		}

		

		

		function add_newsletter($params = array())

		  {

            

			$this->action = 'add_newsletter';

            

            $res_id = $params[0];

            

            if(isset($_POST['formSubmitted']))

            {   

            	$res_id = $this->my_db->post('res_id');

            	$post_info['res_title'] = $this->my_db->post('res_title');

            	$post_info['res_desc'] = $this->my_db->post('res_desc');

            	$post_info['status'] = $this->my_db->post('status');

            	$post_info['res_type'] = 'news_letter';

                $newsletter_stats = addResource($post_info,$res_id);

                if($newsletter_stats){

               	 $msg = $res_id ?"News Letter Successfully Modified" : "News Letter Successfully Added";

               	 state('msg' , $msg); 

               }else{

               	

               	$err = $res_id ?"Failed to modify News Letter" : "Failed to Add Letter";

               	 state('err' , $err);

               }

               

		           

	           urlredirect(ADMIN_URL."manage_newsletters");

			  

            }

            

            if($res_id)

            {

			  $newsletterInfo = getSingleResource(array("res_id"=>$res_id));

            }

             

            require(ADMIN_TEMPLATES.'header.php');

            require(TEMPLATE_STORE.$this->controler_name.'/add-newsletter.tpl.php');

            

            $this->footer = 2;

		} 

		

		function send_newsletter($params = array())

		  {

			$this->action = 'send_newsletter';

            

            $res_id = $params[0];

            

            if(isset($_POST['formSubmitted']))

            {   

            	$res_id = $this->my_db->post('sl_res_id');

            	$post_info['res_desc'] = $this->my_db->post('res_desc');

            	if($this->my_db->post('sendNewsLetter')){

            	

           		   $subject = getSettings('NEWS_LETTER_SUBJECT');

            	   $send_stats = sendNewsLetters($post_info['res_desc'],$subject);	

            	   if($send_stats['error'])

            	    $this->error = $send_stats['message'];

            	   else

            	    $this->message = $send_stats['message'];

            	}

            }

            

            if(!$res_id){

            	$newletter = getSingleResource(array("res_type" =>"news_letter"));

            	$res_id = $newletter['res_id'];

            }

            

            if($res_id)

            {

			  $newsletterInfo = getSingleResource(array("res_id"=>$res_id));

            }else{

            	state('err' , "No News Letter Found");

            	urlredirect(ADMIN_URL."manage_newsletters");

            }

             

            $active_lists = getNewsLetterLists(1);

             

            require(ADMIN_TEMPLATES.'header.php');

            require(TEMPLATE_STORE.$this->controler_name.'/send-newsletter.tpl.php');

            

            $this->footer = 2;

		}

		

		function contact_manage($params = array())

		  {

            

			$this->action = 'contact_manage';

            

            $res_id = $params[0];

            

            if(isset($_POST['formSubmitted']))

            {   

            	$res_id = $this->my_db->post('res_id');

            	$post_info['res_title'] = $this->my_db->post('res_title');

            	$post_info['res_desc'] = $this->my_db->post('res_desc');

            	$post_info['res_extra'] = $this->my_db->post('res_extra');

            	$post_info['content_type'] = $this->my_db->post('content_type');

            	if($res_id)

                $contact_stats = addResource($post_info,$res_id);

                

                if($contact_stats){

               	 $msg = "Cotact Information Successfully Changed";

               	 $this->message = $msg;

               	

               }else{

               	$err = "Failed to modify Cotact Info";

           	    $this->error = $err;

               }

			  

            }

            

           

	        $contactInfo = getSingleResource(array("res_type"=>'contact_page'));

	        if(!$contactInfo){

	        	$res_id = addResource(array("res_type"=>'contact_page'));

	        	$contactInfo = getSingleResource(array("res_id"=>$res_id));

	        }

           

            require(ADMIN_TEMPLATES.'header.php');

            require(TEMPLATE_STORE.$this->controler_name.'/manage_contact_page.tpl.php');

            

            $this->footer = 2;

		} 

		

		function contacts($params = array()){

		 $this->action = 'contacts';

            

			$this->checkHistory();

			

            if(isset($_POST['formSubmitted']))

            {                

                $admin_action = $_POST['admin_action'];

                $c_id_arr = $_POST['c_id_arr']; 

               

				               

                if($c_id_arr)

                {

                    foreach($c_id_arr as $key=>$value)

                    {

                       switch($admin_action){

                  	    case 'read':

                  	                   updateContactStatus($value,1);

                  	                   break;

                       	case 'unread':

                       	  			   updateContactStatus($value,0);

                  	                   break;

                       	case 'delete': 

                       	               removeContactMessage($value);

                       	               break;

                       	

                       }

                    }

                   

                }

                else

                    $this->alert = "Nothing Selected.";

                

            }

            

            require(ADMIN_TEMPLATES.'page-num.php');

           

            $sql_list = "SELECT * FROM ".PRFX."contacts WHERE  c_id != '0' ";   

                     

            $temp_search_key = strtolower(trim($this->my_db->db_input($_POST['search_key'])));            

            if($temp_search_key == 'all')

            {

                state('search_key' , '');

                $this->search_key = '';

            }

            else if(($temp_search_key != 'search..') AND ($temp_search_key != ''))

            {

                state('search_key' , $temp_search_key);

                $this->search_key = $temp_search_key;

            }

              

            if($this->search_key != '')

                $sql_list .= "AND (from_name LIKE '%".$this->search_key."%' OR  from_email LIKE '%".$this->search_key."%' OR 	country  LIKE '%".$this->search_key."%' OR message LIKE '%".$this->search_key."%')";

            else

                $this->search_key = 'Search..';

                        

			$link = ADMIN_URL.$this->action.'/';     

            

            $sql_list .= " ORDER BY c_id DESC";

            

            $pages = make_pagination($sql_list , $page , getSettings('PAGING_PER_PAGE'));

            $sql_list .= " LIMIT ".$pages['start_form'].",".$pages['per_page'];

            #pre($pages);

            #echo $sql_list;exit; 

            $contactList = $this->my_db->simpleSelect($sql_list,0);

            

            

            require(ADMIN_TEMPLATES.'header.php');

            require(TEMPLATE_STORE.$this->controler_name.'/manage-contact-lists.tpl.php');

            

            $this->footer = 2;	

		}

	    function contact_reply($params = array())

		  {

		  	$this->action = 'contact_reply';

		  	$c_id = $params[0];

		  	

		  	if(isset($_POST['formSubmitted']))

            {

            

          

		    }

		  	

		  	$contact_info = getSingleContact(array("c_id"=>$c_id));

		  	

		  	

		    require(ADMIN_TEMPLATES.'html_header.php');

            require(TEMPLATE_STORE.$this->controler_name.'/contact-reply.tpl.php');

		  	

		  	$this->footer = 0;

	  	  }

  function bio_content($params = array())

		  {

            

			$this->action = 'bio_content';

            

            $res_id = $params[0];

            

            if(isset($_POST['formSubmitted']))

            {   

            	$res_id = $this->my_db->post('res_id');

            	$post_info['res_title'] = $this->my_db->post('res_title');

            	$post_info['res_desc'] = $this->my_db->post('res_desc');

            	if($res_id)

                $bio_stats = addResource($post_info,$res_id);

                

                if($bio_stats){

               	 $msg = "Bio Content Successfully Changed";

               	 $this->message = $msg;

               	

               }else{

               	$err = "Failed to modify Bio Content";

           	    $this->error = $err;

               }

			  

            }

            

           

	        $bioInfo = getSingleResource(array("res_type"=>'bio_content'));

	        if(!$bioInfo){

	        	$res_id = addResource(array("res_type"=>'bio_content'));

	        	$bioInfo = getSingleResource(array("res_id"=>$res_id));

	        }

           

            require(ADMIN_TEMPLATES.'header.php');

            require(TEMPLATE_STORE.$this->controler_name.'/manage_bio_page.tpl.php');

            

            $this->footer = 2;

		}

		

	function bio_slides($params = array()){

		    $this->action = 'bio_slides';

			$this->checkHistory();

			

            if(isset($_POST['formSubmitted']))

            {                

               

			    $admin_action = $this->my_db->post('admin_action');

			    $img_id_arr = $this->my_db->post('img_id_arr');

			   

			    $addNewImage = $this->my_db->post('addNewImage');

                if($addNewImage && $_FILES){

                	$stats = addImageRow('bio_slide_image','images/sliders/',array("image_properties"=>'bio_page_sliders'));

                	if($stats)

                	 $this->message = "Image Successfully Added";

                	elseif($stats['error']==1)

                	 $this->error = "Falied To Add Image";

                }

                

                

                if($admin_action == 'delete' && $img_id_arr){

                	foreach($img_id_arr as $key => $value){

                		deleteImageRow($value);

                	}

                }

            }

            

            

            $slider_images = getImagesByProperties('bio_page_sliders');

            

            

            require(ADMIN_TEMPLATES.'header.php');

            require(TEMPLATE_STORE.$this->controler_name.'/bio-sliders.tpl.php');

            

            $this->footer = 2;

	    }

	    

	    function manage_media_page($params = array())

		  {

            

			$this->action = 'manage_media_page';

            

            $res_id = $params[0];

            

            if(isset($_POST['formSubmitted'])){   

            	$res_id = $this->my_db->post('res_id');

            	$post_info['res_title'] = $this->my_db->post('res_title');

            	$post_info['res_desc'] = $this->my_db->post('res_desc');

            	

            	$post_info['file_path'] = $article_file_info['file_path'];

            	$post_info['file_name'] = $article_file_info['file_name'];

            	

            	if($res_id){

            	  if($_FILES['media_image']['name']){

               	 	addImageRow('media_image','images/news/',array("res_id"=>$res_id, "image_properties" =>'media_top_image'),0,$res_id);

               	 }

               	 

           	    $media_stats = addResource($post_info,$res_id);

                if($media_stats){

               	   $msg = "Media Page Successfully Modified";

               	   state('msg' , $msg);

                 }else{

               	   $err = "Failed to modify Media Page";

           	       state('err' , $err);

                 }

						

     	      }

            	

            }

            

            $mediaInfo = getSingleResource(array("res_type"=>'media_page'));

            

	        if(!$mediaInfo){

	        	$res_id = addResource(array("res_type"=>'media_page'));

	        	$mediaInfo = getSingleResource(array("res_id"=>$res_id));

	        }

             

            require(ADMIN_TEMPLATES.'header.php');

            require(TEMPLATE_STORE.$this->controler_name.'/manage-media.tpl.php');

            

            $this->footer = 2;

		}

		

		

		

         function media_manage_links($params = array()){

		    $this->action = 'media_manage_links';

			$this->checkHistory();

			

            if(isset($_POST['formSubmitted']))

            {                

               

			    $admin_action = $this->my_db->post('admin_action');

			    $img_id_arr = $this->my_db->post('img_id_arr');

			    $link_title = $this->my_db->post('link_title');

			   

			    $addNewImage = $this->my_db->post('addNewImage');

                if($addNewImage && $_FILES){

                	$stats = addImageRow('media_slide_image','images/media/',array("image_properties"=>'media_page_sliders',"img_extra" => $link_title));

                	if($stats)

                	 $this->message = "Links Successfully Added";

                	elseif($stats['error']==1)

                	 $this->error = "Falied To Add Link";

                }

                

                

                if($admin_action == 'delete' && $img_id_arr){

                	foreach($img_id_arr as $key => $value){

                		deleteImageRow($value);

                	}

                }

            }

            

            

            $slider_images = getImagesByProperties('media_page_sliders');

            

            

            require(ADMIN_TEMPLATES.'header.php');

            require(TEMPLATE_STORE.$this->controler_name.'/media-sliders.tpl.php');

            

            $this->footer = 2;

	    }

	    

	    

	    

	    

	    function manage_board_page($params = array())

		{ 

            $this->action = 'manage_board_page';

            

			$this->checkHistory();

			

            if(isset($_POST['formSubmitted']))

            {                

                $admin_action = $_POST['admin_action'];

                $res_id_arr = $_POST['res_id_arr']; 

               

				               

                if($res_id_arr)

                {

                    foreach($res_id_arr as $key=>$value)

                    {

                       switch($admin_action){

                  	    case 'enable':

                  	                   updateResourceStatus($value,1);

                  	                   break;

                       	case 'disable':

                       	  			   updateResourceStatus($value,0);

                  	                   break;

                       	case 'delete': 

                       	               removeResourecs($value);

                       	               break;

                       	

                       }

                    }

                   

                }

                else

                    $this->alert = "Nothing Selected.";

                

            }

            

            require(ADMIN_TEMPLATES.'page-num.php');

           

            $sql_list = "SELECT * FROM ".PRFX."resources WHERE res_id != '0' AND res_type = 'board_page'";   

                     

            $temp_search_key = strtolower(trim($this->my_db->db_input($_POST['search_key'])));            

            if($temp_search_key == 'all')

            {

                state('search_key' , '');

                $this->search_key = '';

            }

            else if(($temp_search_key != 'search..') AND ($temp_search_key != ''))

            {

                state('search_key' , $temp_search_key);

                $this->search_key = $temp_search_key;

            }

              

            if($this->search_key != '')

                $sql_list .= "AND (res_type LIKE '%".$this->search_key."%' OR  res_title LIKE '%".$this->search_key."%' OR res_desc LIKE '%".$this->search_key."%')";

            else

                $this->search_key = 'Search..';

                        

			$link = ADMIN_URL.$this->action.'/';     

            

            $sql_list .= " ORDER BY res_order asc, res_id desc";

            

            $pages = make_pagination($sql_list , $page , getSettings('PAGING_PER_PAGE'));

            $sql_list .= " LIMIT ".$pages['start_form'].",".$pages['per_page'];

            #pre($pages);

            #echo $sql_list;exit; 

            $boardLists = $this->my_db->simpleSelect($sql_list,0);

            

            

            require(ADMIN_TEMPLATES.'header.php');

            require(TEMPLATE_STORE.$this->controler_name.'/manage-board-page.tpl.php');

            

            $this->footer = 2;

		}



      function add_board_page($params = array())

		  {

            

			$this->action = 'add_board_page';

            

            $res_id = $params[0];

            

            if(isset($_POST['formSubmitted']))

            {   

            	$res_id = $this->my_db->post('res_id');

                $post_info['res_title'] = $this->my_db->post('res_title');

                $post_info['res_order'] = $this->my_db->post('res_order');

            	$post_info['res_type'] = 'board_page';

            	$post_info['status'] = $this->my_db->post('status');

            	$post_info['content_type'] = $this->my_db->post('content_type')?$this->my_db->post('content_type'): '';

            	

            	$post_info['res_extra'] = getBoardSeoTitle($post_info['res_title'],$res_id);

            	

            	

                $board_stats = addResource($post_info,$res_id);

                

                if($_FILES['board_image']['name']){

                   if($board_stats || $res_id){

               	   $board_stats = $board_stats ?$board_stats :  $res_id;

               	   addImageRow('board_image','images/board/',array("res_id"=>$board_stats, "image_properties" =>'board_page_image'),0,$res_id);

               	 }

               }

                

             if($board_stats){

               	 $msg = $res_id ?"Board Page Information Successfully Modified" : "Board Page Successfully Added";

               	 state('msg' , $msg);

               	 

          	 	  

               }else{

               	

               	$err = $res_id ?"Failed to modify Board Page Info" : "Failed to add Board Page";

               	 state('err' , $err);

               }

               

		           

	           urlredirect(ADMIN_URL."manage_board_page");

			  

            }

            

            if($res_id)

            {

			  $boardInfo = getSingleResource(array("res_id"=>$res_id));

			  $boardImage = getSingleImage(array('res_id'=>$res_id,'image_properties'=>"board_page_image"));

            }

             

            require(ADMIN_TEMPLATES.'header.php');

            require(TEMPLATE_STORE.$this->controler_name.'/add-board-page.tpl.php');

            

            $this->footer = 2;

		}

		

		

		

		function manage_team_page($params = array())

		  {

            

			$this->action = 'manage_team_page';

            

            $res_id = $params[0];

            

            if(isset($_POST['formSubmitted'])){   

            	$res_id = $this->my_db->post('res_id');

            	$post_info['res_desc'] = $this->my_db->post('res_desc');

            	$post_info['res_title'] = $this->my_db->post('res_title');

            	$post_info['status'] = $this->my_db->post('status');

            	$post_info['res_type'] = "team_page";

            	

            	if(!$post_info['status'])

            	$post_info['res_extra'] = $this->my_db->post('res_extra');

            	

            

               	 

           	    $team_stats = addResource($post_info,$res_id);

                if($team_stats){

               	   $this->message = "Team Page Successfully Modified";

               	   //state('msg' , $msg);

                 }else{

               	   $this->error = "Failed to modify Team Page";

           	      // state('err' , $err);

                 }

						

     	      }

            	

           

            

            $teamInfo = getSingleResource(array("res_type"=>"team_page"));

            

	        if(!$teamInfo){

	        	$res_id = addResource(array("res_type"=>'team_page'));

	        	$teamInfo = getSingleResource(array("res_id"=>$res_id));

	        }

             

            require(ADMIN_TEMPLATES.'header.php');

            require(TEMPLATE_STORE.$this->controler_name.'/manage-team-page.tpl.php');

            

            $this->footer = 2;

		}

		

		function manage_stockist_page($params = array())

		  {

            

			$this->action = 'manage_stockist_page';

            

            $res_id = $params[0];

            

            if(isset($_POST['formSubmitted'])){   

            	$res_id = $this->my_db->post('res_id');

            	$post_info['res_desc'] = $this->my_db->post('res_desc');

            	$post_info['res_title'] = $this->my_db->post('res_title');

            	$post_info['status'] = $this->my_db->post('status');

            	$post_info['res_type'] = "stockist_page";

            	

            	if(!$post_info['status'])

            	$post_info['res_extra'] = $this->my_db->post('res_extra');

            	

            

               	 

           	    $team_stats = addResource($post_info,$res_id);

                if($team_stats){

               	   $this->message = "Stockist Page Successfully Modified";

               	   //state('msg' , $msg);

                 }else{

               	   $this->error = "Failed to modify Stockist Page";

           	      // state('err' , $err);

                 }

						

     	      }

            	

           

            

            $stockistInfo = getSingleResource(array("res_type"=>"stockist_page"));

            

	        if(!$stockistInfo){

	        	$res_id = addResource(array("res_type"=>'stockist_page'));

	        	$stockistInfo = getSingleResource(array("res_id"=>$res_id));

	        } 

            require(ADMIN_TEMPLATES.'header.php');

            require(TEMPLATE_STORE.$this->controler_name.'/manage-stockist-page.tpl.php');

            

            $this->footer = 2;

		}

		

		

    function store_content($params = array())

		  {

            

			$this->action = 'store_content';

            

            $res_id = $params[0];

            

            if(isset($_POST['formSubmitted']))

            {   

            	$res_id = $this->my_db->post('res_id');

            	$post_info['res_title'] = $this->my_db->post('res_title');

            	$post_info['res_desc'] = $this->my_db->post('res_desc');

            	if($res_id)

                $store_stats = addResource($post_info,$res_id);

                

                if($store_stats){

               	 $msg = "Store Content Successfully Changed";

               	 $this->message = $msg;

               	

               }else{

               	$err = "Failed to modify Store Content";

           	    $this->error = $err;

               }

			  

            }

            

           

	        $storeInfo = getSingleResource(array("res_type"=>'store_content'));

	        if(!$storeInfo){

	        	$res_id = addResource(array("res_type"=>'store_content'));

	        	$storeInfo = getSingleResource(array("res_id"=>$res_id));

	        }

           

            require(ADMIN_TEMPLATES.'header.php');

            require(TEMPLATE_STORE.$this->controler_name.'/manage_store_page.tpl.php');

            

            $this->footer = 2;

		}

		

	function store_slides($params = array()){

		    $this->action = 'store_slides';

			$this->checkHistory();

			

            if(isset($_POST['formSubmitted']))

            {                

               

			    $admin_action = $this->my_db->post('admin_action');

			    $img_id_arr = $this->my_db->post('img_id_arr');

			   

			    $addNewImage = $this->my_db->post('addNewImage');

                if($addNewImage && $_FILES){

                	$stats = addImageRow('store_slide_image','images/sliders/',array("image_properties"=>'store_page_sliders'));

                	if($stats)

                	 $this->message = "Image Successfully Added";

                	elseif($stats['error']==1)

                	 $this->error = "Falied To Add Image";

                }

                

                

                if($admin_action == 'delete' && $img_id_arr){

                	foreach($img_id_arr as $key => $value){

                		deleteImageRow($value);

                	}

                }

            }

            

            

            $slider_images = getImagesByProperties('store_page_sliders');

            

            

            require(ADMIN_TEMPLATES.'header.php');

            require(TEMPLATE_STORE.$this->controler_name.'/store-sliders.tpl.php');

            

            $this->footer = 2;

	    }

	    

	    function banner($params = array())

		  {

            

			$this->action = 'banner';

            

            $res_id = $params[0];

            

            if(isset($_POST['formSubmitted']))

            {   

            	$res_id = $this->my_db->post('res_id');

            	$post_info['res_desc'] = $this->my_db->post('res_desc');

            	$post_info['content_type'] = $this->my_db->post('content_type');

            	$post_info['status'] = $this->my_db->post('status');

            	$post_info['res_type'] = 'left_banner';

            

            	

            	if($post_info['content_type']=='url'){

            	$post_info['target_url'] = $this->my_db->post('target_url');

            	$post_info['target_window'] = $this->my_db->post('target_window');

                }

               

                $banner_stats = addResource($post_info,$res_id);

                if($banner_stats){

               	 $msg = $res_id ?"Banner Successfully Modified" : "Banner Successfully Added";

                 $this->message = $msg; 

               	 

               	 if($_FILES['banner_image']['name'] && $post_info['content_type']=='url'){

               	 	addImageRow('banner_image','images/banner/',array("res_id"=>$banner_stats, "image_properties" =>'banner_left_image'),0,$res_id);

               	  }

          	 	  

                 }else{

               	

               	 $err = $res_id ?"Failed to modify Banner" : "Failed to Add Banner";

               	 $this->error = $err;

                } 

	           //urlredirect(ADMIN_URL."home_manage_articles");

			  

            }

            

            

           	$bannerInfo = getSingleResource(array("res_type"=>"left_banner"));

           	$res_id = $bannerInfo['res_id'];

           	

            require(ADMIN_TEMPLATES.'header.php');

            require(TEMPLATE_STORE.$this->controler_name.'/banner.tpl.php');

            

            $this->footer = 2;

		}

	    

	 	function news_subscriber($params = array())

		{ 

            $this->action = 'manage_subscriber';

            

			$this->checkHistory();

			

            if(isset($_POST['formSubmitted']))

            {                

                $admin_action = $_POST['admin_action'];

                $user_id_arr = $_POST['user_id_arr']; 

                              

                if($user_id_arr)

                {

                    foreach($user_id_arr as $key=>$value)

                    {

                       switch($admin_action){

                  	    case 'enable':

                  	                   changeUserStatus($value,1);

                  	                   break;

                       	case 'disable':

                       	  			   changeUserStatus($value,0);

                  	                   break;

                       	case 'delete': 

                       	               removeUser($value);

                       	               break;

                       	

                       }

                    }

                   

                }

                else

                    $this->alert = "Nothing Selected.";

                

            }

            

            require(ADMIN_TEMPLATES.'page-num.php');

           

            $sql_list = "SELECT * FROM ".PRFX."users WHERE user_id != '0' AND news_letter_subscribed ='1'";   

                     

            $temp_search_key = strtolower(trim($this->my_db->db_input($_POST['search_key'])));            

            if($temp_search_key == 'all')

            {

                state('search_key' , '');

                $this->search_key = '';

            }

            else if(($temp_search_key != 'search..') AND ($temp_search_key != ''))

            {

                state('search_key' , $temp_search_key);

                $this->search_key = $temp_search_key;

            }

              

            if($this->search_key != '')

                $sql_list .= "AND (email LIKE '%".$this->search_key."%' OR  user_name LIKE '%".$this->search_key."%')";

            else

                $this->search_key = 'Search..';

                        

			$link = ADMIN_URL.$this->action.'/';     

            

            $sql_list .= " ORDER BY user_id";

            

            $pages = make_pagination($sql_list , $page , getSettings('PAGING_PER_PAGE'));

            $sql_list .= " LIMIT ".$pages['start_form'].",".$pages['per_page'];

            #pre($pages);

            #echo $sql_list;exit; 

            $newssubscriberList = $this->my_db->simpleSelect($sql_list,0);

            

            

            require(ADMIN_TEMPLATES.'header.php');

            require(TEMPLATE_STORE.$this->controler_name.'/manage-news-subscriber.tpl.php');

            

            $this->footer = 2;

		}

		

	function add_subscriber($params = array()){

            

			$this->action = 'add_subscriber';

            

            $res_id = $params[0];

            

            if(isset($_POST['formSubmitted']))

            {   

            	

            	$post_info['email'] = $this->my_db->post('user_email');

            	

            	$post_info['access'] = $this->my_db->post('status');

            	$post_info['news_letter_subscribed'] = 1;

            	$user_stats = addUser($post_info);

            

                if($user_stats){

               	 $msg = $user_id ?"Subscriber Successfully Modified" : "NewsLetter Subscriber Successfully Added";

               	 state('msg' , $msg); 

               }else{

               	

               	$err = $res_id ?"Failed to modify Subscriber" : "Failed to Add NewsLetter Subscriber";

               	 state('err' , $err);

               }

               

		           

	           urlredirect(ADMIN_URL."news_subscriber");

			  

            }

          

            require(ADMIN_TEMPLATES.'header.php');

            require(TEMPLATE_STORE.$this->controler_name.'/add_subscriber.tpl.php');

            

            $this->footer = 2;

		}

		

		function intro_manage($params = array())

		  {

            

			$this->action = 'intro_manage';

            

            $res_id = $params[0];

            

            if(isset($_POST['formSubmitted']))

            {   

            	$res_id = $this->my_db->post('res_id');

            	$post_info['res_file_type'] = $this->my_db->post('res_file_type');

            	$post_info['res_type'] = 'intro_page';

            	

            	if($_FILES['target_file']['name']){

            	switch($post_info['res_file_type']){

            		case 'video':

            		        $article_file_info = manageResourceFormFiles('target_file','video/',$res_id,'video');

            		        break;

    		        case 'flash':

            		        $article_file_info = manageResourceFormFiles('target_file','',$res_id,'falsh');

            		        break;

    		           

            	 }

            	}

            	

            	$post_info['file_path'] = $article_file_info['file_path'];

            	$post_info['file_name'] = $article_file_info['file_name'];

            	

            

               

               $intro_stats = addResource($post_info,$res_id);

               

               if($intro_stats){

               	 $msg = $res_id ?"Splash Infos Successfully Modified" : "Splash Infos Successfully Added";

               	 state('msg' , $msg); 

               }else{

               	

               	$err = $res_id ?"Failed to modify Splash Information" : "Failed to Add Splash Information";

               	 state('err' , $err);

               }

               

            }

            

            if(!$res_id){

            	$introPageInfo = getSingleResource(array("res_type" =>"intro_page"));

            	$res_id = $newletter['res_id'];

            }else{

            	$introPageInfo = getSingleResource(array("res_id"=>$res_id));

            }

           

            

            require(ADMIN_TEMPLATES.'header.php');

            require(TEMPLATE_STORE.$this->controler_name.'/intro-manage-page.tpl.php');

            

            $this->footer = 2;

		}

		

		

    function manage_seo($params = array())

		{ 

            $this->action = 'home_manage_articles';

            

			$this->checkHistory();

			

            if(isset($_POST['formSubmitted']))

            {                

                $admin_action = $_POST['admin_action'];

                $res_id_arr = $_POST['res_id_arr']; 

               

				               

                if($res_id_arr)

                {

                    foreach($res_id_arr as $key=>$value)

                    {

                       switch($admin_action){

                  	    case 'enable':

                  	                   updateResourceStatus($value,1);

                  	                   break;

                       	case 'disable':

                       	  			   updateResourceStatus($value,0);

                  	                   break;

                       	case 'delete': 

                       	               removeResourecs($value);

                       	               break;

                       	

                       }

                    }

                   

                }

                else

                    $this->alert = "Nothing Selected.";

                

            }

            

           

            

            $seoLists = getSeoLists();

            

            //pre($seoLists);

            require(ADMIN_TEMPLATES.'header.php');

            require(TEMPLATE_STORE.$this->controler_name.'/manage-seo.tpl.php');

            

            $this->footer = 2;

		}

		

	 function modify_seo($params = array())

		  {

            

			$this->action = 'modify_seo';

            $s_id = $params[0];

            

            if(isset($_POST['formSubmitted'])){   

            	$s_id = $this->my_db->post('s_id');

            	$post_info['meta_title'] = $this->my_db->post('meta_title');

            	$post_info['meta_key'] = $this->my_db->post('meta_key');

            	$post_info['meta_description'] = $this->my_db->post('meta_description');

				$seo_stats = addSeo($post_info,$s_id);

				

                if($seo_stats){

               	 $msg = "Meta Information Successfully Updated";

               	 state('msg' , $msg); 

                }else{

               	

               	$err = "Failed to Update meta Information";

               	 state('err' , $err);

               }

               

		           

	           urlredirect(ADMIN_URL."manage_seo");

            }

            

            if($s_id)

			{ 

				$metaInfo = getSingleSeo(array("s_id"=>$s_id));

			}

             

            require(ADMIN_TEMPLATES.'header.php');

            require(TEMPLATE_STORE.$this->controler_name.'/add-seo.tpl.php');

            

            $this->footer = 2;

		}

		

		

      function manage_logo($params = array())

		  {

            

			$this->action = 'manage_logo';

            

            if(isset($_POST['formSubmitted']))

            {   

            

              if($_FILES['logo_image']['name']){

                 $supload_logo = simpleUploader("logo_image","includes/css/images",'image','banner.jpg');

               }

                

             if($supload_logo[0]['success']==0){

               	  $msg = "Logo Successfully Changed";

               	  state('msg' , $msg);

               }else{

               	

               	$err = "Failed to Change The Logo";

               	 state('err' , $err);

               }

 

			  

            }

            

            

             

            require(ADMIN_TEMPLATES.'header.php');

            require(TEMPLATE_STORE.$this->controler_name.'/manage-logo.tpl.php');

            

            $this->footer = 2;

		}

	}

?>