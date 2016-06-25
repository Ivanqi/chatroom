<?php

	class CommentController extends Controller{

		public function __construct(){
			parent::__construct();
			$this->checkUp();
		}

		// 拒绝，跳过登陆访问
		public function checkUp(){
			if(!isset($_SESSION['home_user'])){ //这个记录用户是否登陆
				if(isset($_COOKIE['home_user_id'])){ //这个只是记录用户信息
					header ('Location: /index.php/login/autoLogin');
					//redirect(U('login/autoLogin'),1);
				}else{
					if(CONTROLLER !='Login')
						header ('Location: /index.php/login/index');
						//redirect(U('Login/index'));
				}
			}else{
				//if(CONTROLLER !='Login' && ACTION !='logout')
				//	redirect(U('Admin/index'),1);
			}
		}
	}


?>