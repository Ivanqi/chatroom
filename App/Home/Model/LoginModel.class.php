<?php
	class LoginModel extends Model{
		protected $table =	'member';
		protected $pk	 =	'm_id';

		protected $_auto	=	array(
			array('m_password','function','md5'),
		);

		protected $_vaild	=	array(
			array('m_name',0,'注册名不能为空','require'),
			array('m_password',0,'密码不能为空','require'),
		);


		/**
		* 检测用户名是否存在
		* @param $username 表单提交的用户名
		* @param $passwd   表单提交的密码
		* @param bool/array
		*/
		public function checkUser($username,$password){
			$row 	= 	$this->where("m_name='{$username}'")->find();

			if(empty($row)){
				return false;
			}
			if($row['m_password']	!=	md5($password)){
				return false;
			}
			return $row;
		}

		/**
		*获取所有附近的人
		* @access public
		* @return array 所有附近的人
		*/
		public function allNearBy(){

			$lifttime	=	1440;
			$last 		=	time() - $lifttime;

			$userList 	=	$this->where("m_name !='{$_SESSION['home_user']['m_name']}' and m_stauts=1 and m_last > {$last} ")->select();
			$userLists	=	array();
			$firend 	=	new UserFirendModel();

			foreach($userList as $k=>$v){
				$row = $firend->where("m_name = '{$v['m_name']}' and uf_isfriend in(1,2,3,4)")->find();
				if(is_null($row)){
					$row2 = $firend->where("uf_name = '{$v['m_name']}' and uf_isfriend in(1,2,3,4)")->find();
					if(is_null($row2)){
						$userLists[] = $v;
					}else{
						unset($v);
					}
				}else{
					unset($v);
				}
			}
			unset($userlist);
			return $userLists;
		}

		/**
		* 获取附近所有的人ajax
		* @access public
		* @return string 返回附近所有的人
		*/
		public function allNearByForAjax(){

			$lifttime	=	1440;
			$last 		=	time() - $lifttime;
			$userLists	=	'';
			$firend 	=	new UserFirendModel();

			$userList 	=	$this->where("m_name != '{$_SESSION['home_user']['m_name']}' and m_stauts=1 and m_last > {$last}")->select();
			foreach($userList as $k=>$v){
				$row = $firend->where("m_name = '{$v['m_name']}' and uf_isfriend in(1,2,3,4)")->find();
				if(is_null($row)){
					$row2 = $firend->where("uf_name = '{$v['m_name']}' and uf_isfriend in(1,2,3,4)")->find();
					if(is_null($row2)){
						$userLists .= json_encode($v).',';
					}else{
						unset($v);
					}
				}else{
					unset($v);
				}
			}
			unset($userList);
			return $userLists;
		}
	}

?>