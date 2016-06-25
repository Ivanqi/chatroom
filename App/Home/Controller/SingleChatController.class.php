<?php
	class SingleChatController extends CommentController{
		public function index(){

			$selfname	= $_SESSION['home_user']['m_name'];
			$firendname = isset($_REQUEST['firendname']) ? $_REQUEST['firendname'] :'';

			$this->view->assign('selfname',$selfname);
			$this->view->assign('firendname',$firendname);
			$this->view->display('index');
		}

		//聊天信息写入
		public function SingleChatSendMes(){
			$data['m_rec'] 			=	isset($_POST['selfname']) ? $_POST['selfname'] :'';
			$data['m_pos'] 			= 	isset($_POST['firendname']) ? $_POST['firendname'] :'';
			$data['m_content']		=	isset($_POST['cont'])	?	htmlspecialchars($_POST['cont']): '';
			$data['m_time']			=	time();

			if(empty($data['m_rec']) || empty($data['m_pos'])){
				echo 'fail';
				return false;
			}

			$single = new SingleChatModel();
			echo $single->add($data) ? 'ok' :'fail';
		}

		//聊天信息提取
		public function SingleChatGetMes(){
			set_time_limit(0);
			$single = new SingleChatModel();
			$pos = isset($_POST['pos']) ?	$_POST['pos']: '';
			$rec = isset($_POST['rec']) ?	$_POST['rec']: '';

			while(true){
				$row = $single->where("ms_rec ='{$rec}' and ms_pos='{$pos}' and ms_isread=0")->limit(1)->find();
				if(empty($row)){

				}else{
					$single->where("ms_id={$row['ms_id']}")->save(array('ms_isread' => 1));
					$row['ms_content']	=	htmlspecialchars_decode($row['ms_content']);
					echo json_encode($row);
					break;
				}
				sleep(1);
			}
		}

		//查看是否有信息推送(来信提醒功能)
		public function mesPush(){

			$single 	= 	new SingleChatModel();
			$user 		=	new LoginModel();

			$rec = isset($_POST['pos'])?$_POST['pos']:'';
			$pos = isset($_POST['rec'])?$_POST['rec']:'';

			$posArr = explode(',',$pos);
			while(true){
				$html = '';
				foreach($posArr as $k=>$v){
					$row = $user->field('m_id')->where("m_name='{$v}'")->find();
					if(!empty($row)){
						$msg	=	$single->where("ms_rec='{$v}' and ms_pos='{$rec}' and ms_isread=0")->limit(1)->find();
						if(empty($msg)){
							$msg['stauts']	 =  'fail';
							$msg['mId']		 =	$row['m_id'];
							$html 			.=  json_encode($msg).',';
						}else{
							$msg['stauts']	 =  'ok';
							$msg['mId']		 =	$row['m_id'];
							$html 			.=  json_encode($msg).',';
						}
					}
				}
				echo rtrim($html,',');
				break;
				sleep(1);
			}
		}

		//在线好友显示
		public function onlineFirend(){
			set_time_limit(0);
			$firend 	=	new UserFirendModel();
			while(true){
				$firendList		=  	$firend->allFirendForAjax();
				if(empty($firendList)){

				}else{
					echo $firendList;
					break;
				}
				sleep(1);
			}
		}
	}
?>