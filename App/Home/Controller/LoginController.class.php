<?php
	class LoginController extends Controller{
		public function index(){
			$this->view->display('login');
		}

		public function reg(){
			$this->view->display('reg');
		}

		public function loginAct(){
			$user['username'] 		=	trim($_POST['username']);
			$user['password']		=	trim($_POST['password']);

			$reg 		=	new LoginModel();
			$user 		= 	$reg->_vaildata($user);
			if(!$user){
				$this->redirect($reg->error,'login/index');
			}

			$users = $reg->checkUser($user['username'],$user['password']);

			if($users){
				$_SESSION['home_user']	=	$users;
				$data['m_stauts']	=	1;
				$data['m_sessId']	=	session_id();
				$data['m_ip']		=	!empty($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:'unknow';
				$data['m_last']		=	time();
				$reg->where("m_id={$users['m_id']}")->save($data);

				$this->success('登陆成功','index/index');
			}
		}

		public function regAct(){

			$data['m_name'] 		=	trim($_POST['username']);
			$data['m_password']		=	trim($_POST['password']);
			$data['m_ip']			=	!empty($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:'unknow';
			$data['m_last']			=	time();
			$data['m_sessId']		=	session_id();
			$data['m_stauts']		=	1;

			$reg 		=	new LoginModel();

			$data 		= 	$reg->_vaildata($data);

			if(!$data){
				$this->redirect($reg->error,'login/reg');
			}

			$data 		=	$reg->_automatic($data);
			$username 	=	$reg->where("m_name='{$data['m_name']}'")->find();

			if(!empty($username)){
				$this->redirect('该用户已经存在，请重新输入','login/reg');
			}
			$mIp 		=	$reg->where("m_ip='{$data['m_ip']}'")->find();
			if(!empty($mIp)){
				$this->redirect('你已经注册过了，请登录','login/index');
			}

			if($reg->add($data)){
				$this->success('注册成功,跳转到登陆页进行登陆','login/index');
			}

		}

		//用户退出
		public function loginout(){
			session_destroy();
			setcookie('home_user_id','',time()-1);
			$this->success('退出登陆','login/index');
		}

		public function test(){
			$this->view->display('test');
		}

		public function test2(){
			$this->view->display('test2');
		}
	}
?>