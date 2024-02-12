<?php



/**

 * @author :)

 * @copyright 2009

 */

	

	#class auth extends dbclass

	class auth

	{

		

		var $controler_name;

		var $global_data;		

		var $my_db;

		var $my_session;

		var $user_id;		

		var $page_title;

		var $meta_keywords;

		var $meta_description;

		var $error;

		var $message;

        var $history;

				

		function __construct()

		{

			global $lang,$db,$session;

			//load_page_lang('login');

			

			$this->my_db = $db;

			$this->my_session = $session;								

			$this->controler_name = 'auth';

			$this->user_id = state('user_id');

            

            $this->error = state('err');

            $this->message = state('msg');

            $this->history = state('hst');

            

            state('err' , '');

            state('msg' , '');

            state('hst' , '');

		}

		

		function default_func($params = array())

		{

			#$message = @trim($params[0]);

			if($this->user_id)

			{

				

				urlredirect(THE_URL);

			}

			else

				$this->login();

			

		}

		

		function login()

		{

			global $lang;
			if(isset($_POST['formSubmitted']))
			{
				$stats = checkMyUserAuth();
				if(is_array($stats))
				{	
					state('err' , $stats['reason']);
                    //urlredirect(THE_URL);	
				}									
				else

				{		
					urlredirect(THE_URL);
				}		

			}

			

			//pick_page_info('sign_in' , &$this , 1);

			require(COMMON_TEMPLATES.'header.tpl.php');

			require(TEMPLATE_STORE.$this->controler_name.'/signin.tpl.php');

            require(COMMON_TEMPLATES.'footer.tpl.php');

         	//$this->footer = 0;	

		}

		

		function forgetpass($params = array())

		{			

			

			if(isset($_POST['formSubmitted']))

			{

				$email = $this->my_db->db_input($_POST['email']);

				

				$sql = "SELECT * FROM users WHERE email = '".$email."'";

	  			$res = $this->my_db->select_single($sql);

	  			#fwrite($handle, $user_email);

			  	if($res){

				  $email = $res['email'];

				  $name = $res['first_name'] . ' ' . $res['last_name'];				  

				  $new_password = ce_create_random_value(6);

				  #$enc_password = _encode($new_password);

				  $enc_password = _encode($new_password);

					#fwrite($handle, $new_password);	

				  $upd_sql = "UPDATE users SET password = '$enc_password' WHERE email = '$email' ";

					

				  $subject = "Password Reset";

				  $msg = "Dear ".$name." \nYour Password has been reset. \n New Password is: ".$new_password ."\n".getSettings('EMAIL_SIGNATURE');

				  // setting the mailer

				  $mailer['to_name']= $name;

		          $mailer['to_email']= $email;

				  $mailer['subject']= $subject;

		          $mailer['message']= nl2br($msg);

				  $mailer['mailtype']= "html";

				  

				  if(send_emailer($mailer))

				  {				  	

				    $this->my_db->edit($upd_sql);

				    state('msg' , "Password Sent To Your Email");

				  }else{

				  	

                      state('err' , "Failed to send password"); 	

				  }

			  }else{

				

                state('err' , "Please Provide Valid Email Address");

			  }

			}

			

			#pick_page_info('sign_in' , &$this , 1);

			require(COMMON_TEMPLATES.'header.php');

			require(TEMPLATE_STORE.$this->controler_name.'/forgetpass.tpl.php');

		}

		

		function register($params = array())

		{

			

			global $lang;			

			#require_once(DIR_3RD_PARTY."vimage/vImage.php");

    		#$vImage = new vImage();

    		

			#$country_list = $this->my_db->dbQuery("*" , "country");

    		//pick_page_info('sign_up' , &$this , 1);

    		 

			if(isset($_POST['formSubmitted']))

			{		

				

				#$vImageCodP = $_POST['vImageCodP'];	

				

				#$vImage->loadCodes();	

				#$captcha = $vImage->sessionCode;

				#if($captcha == $vImageCodP)

				#{

				

					if($this->my_db->countTotal("users" , "user_name = '".$this->my_db->db_input($_POST['user_name'])."'") > 0)

					{

						#state('err' , "User Name Already Exist");

                        $this->error = "User Name Already Exist";

                        				

					}

					else if($this->my_db->countTotal("users" , "email = '".$this->my_db->db_input($_POST['email'])."'") > 0)

					{

						#state('err' , "Email Already Exist");

						$this->error = "Email Already Exist";

                        

					}

					else

					{

						

						$data = array();

						$data['user_name'] = $this->my_db->db_input($_POST['user_name']);

						$data['email'] = $this->my_db->db_input($_POST['email']);

						$data['name'] = $this->my_db->db_input($_POST['name']);

						$upass = $_POST['user_password'];

						//$data['password'] = _encode($_POST['user_password']);		

						$data['password'] = _encode($_POST['user_password']);		

						$data['user_created']  = mktime(0, 0, 0, date("m")  , date("d") , date("Y"));

						#$data['validate_code'] = $validate_code;

						#$data['user_ip'] = IP_TO_LONG($_SERVER['REMOTE_ADDR']);

						$data['user_ip'] = $_SERVER['REMOTE_ADDR'];

						#pre($data);exit;					

						$user_id = $this->my_db->doInsert($data , 'users',1);

						#exit;

						if($user_id)

						{	

							

							$mail_subject = "Registration at " . getSettings('SCRIPT_NAME');

							$mail_content = "You have sucessfully registered. \n\n";

							$mail_content .= "Account Information:\n\n";

							$mail_content .= "Username: ".$data['user_name']."\n\n";

							$mail_content .= "Password: ".$upass."\n\n";

							

							if(mailUser($user_id , $mail_subject , $mail_content))

							     state('msg' , "Registration Sucessfully Completed. A mail has been sent to you for account details.");

							else

                                state('err' , "Failed to send confirmation mail.");

							urlredirect(THE_URL);	

						}

						else

						{

							state('err' , "Failed To Register!!!");

							urlredirect(THE_URL);

						}

					}

				#}

				#else

				#	$err = "Human Verification Code Not Matched!";

				

								

			}

            

            

            

			require(COMMON_TEMPLATES.'header.php');	

            require(TEMPLATE_STORE.'home'.'/home.tpl.php');				

			#require(TEMPLATE_STORE.$this->controler_name.'/signup.tpl.php');

			

		}

		

		function profile()

		{

			global $lang;

			$user_id = state('user_id');
			$user_info = $this->my_db->querySingle("*" , "users" , "user_id = '" . $user_id . "'");
			require(COMMON_TEMPLATES.'header.php');
			require(TEMPLATE_STORE.$this->controler_name.'/profile.tpl.php');

			

		}

		

		function logout()

		{

			global $lang;

			

			if(isset($_COOKIE['user_name']) && isset($_COOKIE['password'])){

			    	setcookie("user_name", '', time()-60*60, "/");

			      	setcookie("password", '', time()-60*60, "/");

			      	setcookie("cookie_time", time(), time()-60*60, "/");

			}

			

			$this->my_session->sess_destroy();

			

            urlredirect(THE_URL);

		}

		

	}





?>