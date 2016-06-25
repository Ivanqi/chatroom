<?php
	class UserFirendModel extends Model{
		protected $table	=	'userfirend';
		protected $pk		=	'uf_id';

		/**
		* 获取所有的朋友
		* @access public
		* @return array 所有的朋友
		*/
		public function allFirend(){

			$firendList1 = $this->field('m_name,uf_name,m_id')->where("uf_name='{$_SESSION['home_user']['m_name']}' and uf_isfriend = 1")->select();
			$firendList2 = $this->field('m_name as uf_name,uf_name as m_name,m_id')->where("m_name='{$_SESSION['home_user']['m_name']}' and uf_isfriend = 1")->select();
			$firendLists = array_merge($firendList1,$firendList2);
			unset($firendList1);
			unset($firendList2);
			return $firendLists;
			//var_dump($firendList1,$firendList2,$firendLists);
		}

		/**
		* 获取所有的朋友ajax
		* @access public
		* @return string 所有的朋友
		*/
		public function allFirendForAjax(){
			$json = '';
			$firendList1 = $this->field('m_name,uf_name,m_id')->where("uf_name='{$_SESSION['home_user']['m_name']}' and uf_isfriend = 1")->select();
			$firendList2 = $this->field('m_name as uf_name,uf_name as m_name,m_id')->where("m_name='{$_SESSION['home_user']['m_name']}' and uf_isfriend = 1")->select();
			$firendLists = array_merge($firendList1,$firendList2);
			unset($firendList1);
			unset($firendList2);

			foreach($firendLists as $k=>$v){
				$json .= json_encode($v).',';
			}
			unset($firendLists);
			return rtrim($json,',');
		}

	}
?>