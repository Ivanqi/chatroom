<?php
	class IndexController  extends CommentController{
		public function index(){

			$user 			=	new LoginModel();
			$firend 		=	new UserFirendModel();

			$userLists 		=	$user->allNearBy();
			$firendList		=  	$firend->allFirend();

			$this->view->assign('firendlist',$firendList);
			$this->view->assign('userlist',$userLists);
			$this->view->assign('user',$_SESSION['home_user']);
			$this->view->display('index');
		}

		//信息提取
		public function ChatSelect(){
			set_time_limit(0);
			$jsonPath 	=	DATA.'save/jsonData.txt';
			while(true){
				$str = file_get_contents($jsonPath);
				if(!$str){
				}else{
					echo $str;
					break;
				}
				sleep(1);
			}
		}

		//信息写入
		public function ChatInsert(){
			$data['c_ip']		=	trim($_POST['ip']);
			$data['c_name'] 	=	trim($_POST['name']);
			$data['c_content']  = 	trim($_POST['content']);
			$data['c_time']		=	date('Y-m-d H:i:s',time());

			$jsonPath 	=	DATA.'save/jsonData.txt';
			$fh			=	fopen($jsonPath,'ab');
			$jsonstr 	= 	json_encode($data).",";
			fwrite($fh,$jsonstr);
			fclose($fh);
		}

		//增加朋友
		public function addFirend(){

			$data['m_id']		=	trim($_POST['id']);
			$data['m_name']		=	trim($_POST['name']);
			$data['uf_name']	=	trim($_POST['firend']);
			$data['uf_time']	=	time();

			if(empty($data['m_id']) || empty($data['m_name']) || empty($data['uf_name'])){
				echo ' ';
				return false;
			}

			$firend 	=	new UserFirendModel();
			$row = $firend->where("m_name='{$data['m_name']}' and uf_name='{$data['uf_name']}'")->find();
			if(!empty($row)){
				if($row['uf_isfriend'] == 0){
					echo '请求在等待中，请耐心等待';
					return false;
				}else if($row['uf_isfriend'] == 2){
					echo '你的请求等待中';
					return false;
				}
			}else{
				$row2 = $firend->where("m_name='{$data['uf_name']}' and uf_name='{$data['m_name']}'")->find();
				if(!empty($row2)){
					if($row['uf_isfriend'] == 0){
						echo '请求在等待中，请耐心等待';
						return false;
					}else if($row['uf_isfriend'] == 2){
						echo '你的请求等待中';
						return false;
					}
				}
			}
			echo $firend->add($data)?'请求发送中，请等待':' ';
		}

		//查看是否有好友请求
		public function  checkFirendRequest(){
			set_time_limit(0);
			$firend 	=	new UserFirendModel();
			$name = isset($_POST['ask'])?$_POST['ask']:'';

			while(true){
				$msg = $firend->field('count(*) as n')->where("uf_name='{$name}' and uf_isfriend=0 and uf_index=0")->find();
				if($msg['n'] == 0){
					//break;
				}else{
					echo $msg['n'];
					break;
				}
				sleep(1);
			}
		}

		//好友请求页面
		public function addFirendShow(){
			$username =  isset($_SESSION['home_user']['m_name']) ? $_SESSION['home_user']['m_name'] : 0;

			if(!$username)$this->redirect('该用户不存在','login/index');

			$firend 	=	new UserFirendModel();
			$firends 	=	$firend->field('m_name,uf_time')->where("uf_name='{$username}' and uf_isfriend=0 ")->select();

			$this->view->assign('firends',$firends);
			$this->view->display('addFirendShow');
		}

		//好友请求处理
		public function requestDispose(){

			$firend 	=	new UserFirendModel();
			$model 		= 	isset($_POST['mode'])		?	$_POST['mode']:'';
			$name  		= 	isset($_POST['name'])		?	$_POST['name']:'';
			if(empty($name)){
				echo ' ';
			}
			if(empty($model)){
				echo ' ';
			}
			if($model == 'add'){
				echo $firend->where("m_name='{$name}'")->save(array('uf_isfriend' => '1'))?'好友添加成功':' ';
				die;
			}else if($model == 'refuse'){
				echo $firend->where("m_name='{$name}'")->save(array('uf_isfriend' =>'3'))?'好友请求已拒绝':' ';
				die;
			}else{
				echo ' ';
				die;
			}
		}

		//请求响应
		public function Reresponse(){
			set_time_limit(0);
			$ask 	=	isset($_POST['ask']) ? trim($_POST['ask']) : '';

			$firend 	=	new UserFirendModel();
			while(true){
				$row = $firend->field('m_name,uf_isfriend,uf_id,m_id')->where("uf_name ='{$ask}' and uf_isfriend in (2,3)")->limit(1)->find();
				if(empty($row)){
				}else{
					if($row['uf_isfriend'] == 2){
						$row['tips'] 	= 	$row['m_name'].' 通过了你的请求';
						$row['model']	=	200;
						$firend->where("uf_id ={$row['uf_id']}")->save(array('uf_isfriend' =>1));
						echo json_encode($row);
						break;
					}else if($row['uf_isfriend'] == 3){
						$row['tips']	=	$row['m_name'].' 拒绝了你的请求';
						$row['model']	=	404;
						$firend->where("uf_id ={$row['uf_id']}")->save(array('uf_isfriend' =>4));
						echo json_encode($row);
						break;
					}
				}
				sleep(1);
			}
		}

		//附近的人是否在线
		public function nearbyExists(){
			set_time_limit(0);
			$user 		=	new LoginModel();

			while(true){
				$userLists 	=	$user->allNearByForAjax();

				if(empty($userLists)){
					echo '';
					break;
				}else{
					echo rtrim($userLists,',');
					break;
				}
				sleep(1);
			}
		}

		//好友删除
		public function firendDelte(){
			$firends	=	isset($_POST['rec']) ? $_POST['rec'] :'';
			$self		=	isset($_POST['self'])	? $_POST['self']   :'';

			$firend 	=	new UserFirendModel();
			$mName 		=	$firend->where("m_name='{$firends}' and uf_name='{$self}' and uf_isfriend=1")->find();
			if(empty($mName)){
				$self 	=	$firend->where("m_name='{$self}' and uf_name='{$firends}' and uf_isfriend=1")->find();
				if(!empty($self)){
					echo $firend->where("m_name='{$self}' and uf_name='{$firends}'")->delete() ? 'ok' :'fail';
				}else{
					echo 'fail';
				}
			}else{
				echo $firend->where("m_name='{$firends}' and uf_name='{$self}'")->delete() ? 'ok' :'fail';
			}
		}

		//用户编辑
		public function userEdit(){
			$this->view->assign('index');
		}
	}

?>