<?php

	//视图类
	class View{
		private $data	= array();

		/**
		* 设置模版全局变量
		* @access public
		* @param string $name 	属性名
		* @param string $value 	属性值
		* @return void
		*/
		public function assign($name,$value){
			$this->data[$name]	=	$value;
		}

		/**
		* 用于显示模版
		* @access public
		* @param string $template 模板名称
		* @return void
		*/
		public function display($template){
			include_once APP_VIWE.MODULE."/".ucfirst(CONTROLLER).'/'.$template.'.html';
		}
	}

?>