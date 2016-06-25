<?php
	class Dispatcher{

		/**
		* url 的调度函数
		* @access public
		* @return void
		*/
		public static function dispatch(){
			if(isset($_SERVER['PATH_INFO']) && $_SERVER['PATH_INFO'] != '/'){
				$pathinfo 	=	trim($_SERVER['PATH_INFO'],'/');
				$paths 		=	explode('/',$pathinfo);

				$_REQUEST['c']	=	array_shift($paths);
				$_REQUEST['a']	=	empty($paths)?'index':array_shift($paths);

				$var 		= 	array();
				preg_replace_callback('/(\w+)\/([^\/]+)/', function($match) use(&$var){ $var[$match[1]] = strip_tags($match[2]);}, implode('/',$paths));

				$_REQUEST   	=  	array_merge($var,$_REQUEST);
			}
			//$_REQUEST = array_merge($_POST,$_GET);
			define('MODULE',self::getModule('m'));
			define('CONTROLLER',self::getControll('c'));
			define('ACTION', self::getAction('a'));
		}

		/**
		* 得到操作函数
		* @access private
		* @param string  $var 操作函数的键名
		* @return string 操作函数名字
		*/
		private static function getAction($var){
			$action = !empty($_REQUEST[$var])?$_REQUEST[$var]:'index';
			unset($_REQUEST[$var]);
			return strtolower($action);
		}

		/**
		* 得到控制器
		* @access private
		* @param string  $var 操作控制器的键名
		* @return 控制器名字
		*/
		private static function getControll($var){
			$controll = !empty($_REQUEST[$var])?$_REQUEST[$var]:'index';
			unset($_REQUEST[$var]);
			return strip_tags(ucfirst($controll));
		}

		/**
		* 得到模块
		* @access private
		* @param string  $var 操作模块的键名
		* @return 模块名字
		*/
		private static function getModule($var){
			$module = !empty($_REQUEST[$var])?$_REQUEST[$var]:'home';
			unset($_REQUEST[$var]);
			return ucfirst(strtolower($module));
		}
	}
?>