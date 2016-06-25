<?php

	//控制器基类
	class Controller {
		protected $view;		//视图基类

		public function __construct(){
			$this->view 	=	new View();
		}

		/**
		*错误信息提示方法
		* @access public
		* @param $message 	错误信息
		* @param $jumpUrl	跳转连接
		* @param $waitSecond等待时间
		*/
		public function redirect($message,$jumpUrl='',$waitSecond=3){
			$jumpUrl = U($jumpUrl);
			include_once PUBLICS.'/tips.html';
			exit;
		}

		/**
		*成功信息提示方法
		* @access public
		* @param $message 	错误信息
		* @param $jumpUrl	跳转连接
		* @param $Waitsecond等待时间
		*/
		public function success($message,$jumpUrl='',$waitSecond=1){
			$jumpUrl = U($jumpUrl);
			include_once PUBLICS.'/tips.html';
			exit;
		}
	}

?>