<?php



/**

 * @author :)

 * @copyright 2019

 */

	class home

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

			$this->controler_name = 'home';

			$this->user_id = state('user_id');

			//$this->user_type = getusertype();

            #pre($this->gerbage); exit;

            $this->error = state('err');

            $this->message = state('msg');

            $this->history = state('hst');

            

            state('err' , '');

            state('msg' , '');

            state('hst' , '');

            

            $this->action = 'default';

		}

		

		function default_func($params = array()){	
         
         $check_login = checkLoggedIn();	

         if(!($check_login && $check_login['user_type']==1)){
            urlredirect(THE_URL."auth/login");	
            exit;
         }

         require(COMMON_TEMPLATES.'header.tpl.php');

		 require(TEMPLATE_STORE.$this->controler_name.'/home.tpl.php');	

		 require(COMMON_TEMPLATES.'footer.tpl.php');

         $this->footer = 0;

		}
        
        
		function home($params = array()){

            $this->action = 'home';	

			

			$meta_information = getMetaInformation(array("page_key" => 'main_home'));

				      

			$slider_images = getImagesByProperties('home_page_sliders');

			

			$link = SCRIPT_URL.$this->action.'/page/';

			require(COMMON_TEMPLATES.'page-num.php');

			

			$sql_list = "SELECT * FROM ".PRFX."resources WHERE res_id != '0' AND res_type = 'home_article' AND status ='1' "; 

			$sql_list .= " ORDER BY res_order asc,res_id desc";

            

            $pages = make_pagination($sql_list , $page , getSettings('PAGING_PER_PAGE'));

            $sql_list .= " LIMIT ".$pages['start_form'].",".$pages['per_page'];

			

		    $homeArticles = $this->my_db->simpleSelect($sql_list,0);

			//$homeArticles = getResource(array("res_type" => "home_article"));

			//pre($homeArticles);

			require(COMMON_TEMPLATES.'header.php');

			require(TEMPLATE_STORE.$this->controler_name.'/home.tpl.php');	

			$this->footer = 1;

			

		}

	

		

        function lists($params = array()){

            $this->action = 'lists';

            

            $check_login = checkLoggedIn();	

            if(!($check_login && $check_login['user_type']==1)){

                urlredirect(THE_URL."auth/signin");	

                exit;

            }

            $today = date("Ymd"); 

			$sql = "SELECT * FROM attendece_data WHERE date_added ='$today'";

            $data_lists = $this->my_db->select($sql);

            

	        require(COMMON_TEMPLATES.'header.tpl.php');

		    require(TEMPLATE_STORE.$this->controler_name.'/lists.tpl.php');	

		    require(COMMON_TEMPLATES.'footer.tpl.php');

         	$this->footer = 0;

		}
        
        
        function view_lists($params = array()){

            $this->action = 'view_lists';

            

            /*$check_login = checkLoggedIn();	

            if(!($check_login && $check_login['user_type']==1)){

                urlredirect(THE_URL."auth/signin");	

                exit;

            }*/

            $today = date("Ymd"); 

			$sql = "SELECT * FROM attendece_data WHERE date_added ='$today'";

            $data_lists = $this->my_db->select($sql);

            

	        require(COMMON_TEMPLATES.'header.tpl.php');

		    require(TEMPLATE_STORE.$this->controler_name.'/view-lists.tpl.php');	

		    require(COMMON_TEMPLATES.'footer.tpl.php');

         	$this->footer = 0;

		}

        
        function edit_list($params = array()){

            $this->action = 'edit_list';
            
            

            $check_login = checkLoggedIn();	

            if(!($check_login && $check_login['user_type']==1)){

                urlredirect(THE_URL."auth/signin");	

                exit;

            }
            $user_id =  $this->my_db->get_post("id");
            
            //print_r($_POST); exit; 
            if($_POST['update']){
                
                $row_id = $_POST['row_id'];
                $e_times = $this->my_db->get_post("e_times");
                $e_am_pm = $this->my_db->get_post("e_am_pm");
                $removed_lists = $this->my_db->get_post("removed_lists");
                if(empty($removed_lists))
                $removed_lists = array();
                $entry_times = "";
                $today = $_REQUEST['date']? $_REQUEST['date'] : date("Ymd");
                
                if($e_times){
                    foreach($e_times as $e_key => $e_val){
                        if(!in_array($e_key,$removed_lists)){
                            if($e_am_pm[$e_key]){
                                
                                if($_REQUEST['date'])
                                $entry_times[] = strtotime($today." ".$e_val." ".$e_am_pm[$e_key]);
                                else
                                $entry_times[] = strtotime(date("Y-m-d")." ".$e_val." ".$e_am_pm[$e_key]);
                            }
                        }
                    }
                    if($entry_times){
                        sort($entry_times);
                        if($row_id){
                            $this->my_db->update("UPDATE attendece_data SET data_input='".serialize($entry_times)."', last_modified =NOW() WHERE id='$row_id'");
                        }else{
                            $this->my_db->insert("INSERT INTO attendece_data SET emp_id='$user_id',date_added='$today',data_input='".serialize($entry_times)."'");
                        }
                    }
                }
            }
            
            if($_POST['remove_all']){
                 $today = $_REQUEST['date']? $_REQUEST['date'] : date("Ymd");
                 //echo "DELETE FROM attendece_data WHERE emp_id='$user_id' AND date_added='$today'"; exit;
                $this->my_db->delete("DELETE FROM attendece_data WHERE emp_id='$user_id' AND date_added='$today'");
               
            }
            //print_r($entry_times);

            $today = $_REQUEST['date']? $_REQUEST['date'] : date("Ymd"); 
            //$today = "20170116"; 

			$sql = "SELECT * FROM attendece_data WHERE date_added ='$today' AND emp_id='$user_id'";

            $data_lists = $this->my_db->select_single($sql);

            

	        require(COMMON_TEMPLATES.'header.tpl.php');

		    require(TEMPLATE_STORE.$this->controler_name.'/edit-list.tpl.php');	

		    require(COMMON_TEMPLATES.'footer.tpl.php');

         	$this->footer = 0;

		}

        

		function bio($params = array()){

            $this->action = 'bio';		      

			

			$meta_information = getMetaInformation(array("page_key" => 'main_bio'));

			

			$bioInfo = getSingleResource(array("res_type"=>'bio_content'));

		    $slider_images = getImagesByProperties('bio_page_sliders');

		    

			require(COMMON_TEMPLATES.'header.php');

			require(TEMPLATE_STORE.$this->controler_name.'/bio.tpl.php');	

			

		}

		

		

		

		function store($params = array()){

            $this->action = 'store';	

				      

		    $meta_information = getMetaInformation(array("page_key" => 'main_store'));

			

			$storeInfo = getSingleResource(array("res_type"=>'store_content'));

		    $slider_images = getImagesByProperties('store_page_sliders');

		    

			require(COMMON_TEMPLATES.'header.php');

			require(TEMPLATE_STORE.$this->controler_name.'/store.tpl.php');	

			

		}

		

		

		function media($params = array()){

            $this->action = 'media';	

			

			$meta_information = getMetaInformation(array("page_key" => 'main_media'));     

			

			$mediaInfo = getSingleResource(array("res_type"=>'media_page'));

		    $media_image = getSingleImage(array("image_properties" =>'media_top_image'));

		    $media_links = getImagesByProperties('media_page_sliders');

		    

			require(COMMON_TEMPLATES.'header.php');

			require(TEMPLATE_STORE.$this->controler_name.'/media.tpl.php');	

			

		}

        

        

        

        function team($params = array()){

            $this->action = 'team';		      

            

			$meta_information = getMetaInformation(array("page_key" => 'main_team'));  

			

			$teamInfo = getSingleResource(array("res_type"=>"team_page"));

			require(COMMON_TEMPLATES.'header.php');

			require(TEMPLATE_STORE.$this->controler_name.'/team.tpl.php');	

        }

        

        

        function stockists($params = array()){

            $this->action = 'stockists';	

				      

			$meta_information = getMetaInformation(array("page_key" => 'main_stockist'));

			

			$stockistInfo = getSingleResource(array("res_type"=>"stockist_page"));

			require(COMMON_TEMPLATES.'header.php');

			require(TEMPLATE_STORE.$this->controler_name.'/stockists.tpl.php');	

        }

        

       function boards($params = array()){
            $this->action = 'boards';	
			$board_title = $params[0];
			$board_info = getSingleResource(array("res_extra"=>$board_title));
			$is_board_menu = $board_info['res_extra'];
		    $board_image = getSingleImage(array("res_id" =>$board_info['res_id']));
			$meta_information = getMetaInformation(array("page_key" => $board_info['res_extra']));
			require(COMMON_TEMPLATES.'header.php');
			require(TEMPLATE_STORE.$this->controler_name.'/board.tpl.php');	
       }

        

        function contact($params = array()){

            $this->action = 'contact';	

			

			$meta_information = getMetaInformation(array("page_key" => 'main_contact'));	      

			

			$contactInfo = getSingleResource(array("res_type"=>'contact_page'));

		    

   

			require(COMMON_TEMPLATES.'header.php');

			require(TEMPLATE_STORE.$this->controler_name.'/contact.tpl.php');	

			

		}

        

        

		function page_not_found($params = array())

		{

		    $this->action = 'page_not_found';  

			

			

			require(COMMON_TEMPLATES.'header.php');

            

            

			#pre($params);

			require(COMMON_TEMPLATES.'page-not-found.php');

		}
        
 	function test_func($params = array()){
        //print_r($_SESSION);
        //echo $user_role = state("user_role");
    }  
      		

	}





?>