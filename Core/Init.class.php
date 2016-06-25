<?php
	class Init{
		/**
		* 框架初始化
		* @access public
		* @return void
		*/
		public static function start(){

			self::setCharset();
			self::error_reporting();

			include_once 'Dispatcher.class.php';
			Dispatcher::dispatch();

			self::setDirConst();

			include_once CORE.'functions.php';
			C(include CONFIG."config.php");

			self::autoload();
			self::_addslashes();
			self::sessionSet();
			self::exec();
		}

		/**
		* 设置字符集
		* @access private
		* @return void
		*/
		private static function setCharset(){
			header('Content-type:text/html;charset=utf-8');
		}

		/**
		* 设置目录常量
		* @access private
		* @return void
		*/
		private static function setDirConst(){

			define('ROOT',str_replace('\\','/',dirname(dirname(__FILE__))).'/');
			define('PATH',substr($_SERVER['SCRIPT_NAME'],0,strrpos($_SERVER['SCRIPT_NAME'],'/')));
			define('_APP_',$_SERVER['SCRIPT_NAME']);
			define('ErrShow',ROOT.'redirect.php');
			define('TEMP','/App/'.'view/'.MODULE.'/');
			define('_PUBLIC_',PATH.'/App/'.'public/'.MODULE.'/');

			define('CORE',ROOT.'Core/');
			define('CONFIG',ROOT.'Config/');
			define('DATA',ROOT.'Data/');
			define('TOOL',ROOT.'Tool/');
			define('VIEW',ROOT.'View/');
			define('APP', ROOT.'App/');
			define('APP_CONTROLLER',APP.MODULE.'/Controller/');
			define('APP_MODEL',APP.MODULE.'/Model/');
			define('APP_VIWE',APP.'View/');
			define('APP_PUBLIC',APP.'Public/'.MODULE.'/');
			define('PUBLICS',ROOT.'Public');
		}

		/**
		*设置数组转义
		* @access private
		* @retrun void
		*/
		private static function _addslashes(){
			$_POST	=	_addslashes($_POST);
			$_GET	=	_addslashes($_GET);
			$_COOKIE=	_addslashes($_COOKIE);
		}


		/**
		* 自动加载函数
		* @access private
		* @return void
		*/
		private static function autoload(){
			spl_autoload_register('self::autoloadController');
			spl_autoload_register('self::autoloadModel');
			spl_autoload_register('self::autoloadCore');
			spl_autoload_register('self::autoloadTool');
		}

		/**
		* 自动加载App/模块/Controller文件夹
		* @access private
		* @param $className 类名
		* @return void
		*/
		private static function autoloadModel($className){
			$path = APP_MODEL.$className.'.class.php';
			if(is_file($path)){
				include $path;
			}
		}

		/**
		* 自动加载App/模块/model文件夹
		* @access private
		* @param $className 类名
		* @return void
		*/
		private static function autoloadController($className){
			$path = APP_CONTROLLER.$className.'.class.php';
			if(is_file($path)){
				include $path;
			}
		}

		//设置自动加载Core目录
		public static function autoloadCore($className){
			$path = CORE.$className.'.class.php';
			if(is_file($path)){
				include $path;
			}
		}

		//设置自动加载Tool目录
		public static function autoloadTool($className){
			$path = TOOL.$className.'.class.php';
			if(is_file($path)){
				include $path;
			}
		}

		/**
		* 设置报错处理机制
		* @access private
		* @return void
		*/
		private static function error_reporting(){
			@ini_set('error_reporting',E_ALL);
			@ini_set('display_errors',1);
		}

		/**
		* 设置自定义session(现在只针对后台部分)
		* @access private
		* @return void
		*/
		private static function sessionSet(){
			new Session();
		}

		/**
		* 实例化当前使用的控制器
		* @access private
		* @return void
		*/
		private static function exec(){
			$controll 	=	ucfirst(CONTROLLER).'Controller';
			$action 	=	ACTION;
			$controller =	new $controll();

			$method = new ReflectionMethod($controller,$action);

			if(!preg_match('/^[A-Za-z](\w)*$/',$action)){
				throw new ReflectionException();
			}

			if($method->isPublic() && !$method->isStatic()){
				//检测方法是否带参数
				if($method->getNumberOfParameters()){
					$vars = $_REQUEST;
					//得到形参列表
					$params = $method->getParameters();
					$args 	=	[];
					foreach($params as $param){
						$name 	= $param->getName();
						$args[] =	isset($vars[$name])?$vars[$name]:'';
					}
					$method->invokeArgs($controller,$args);
				}else{
					$method->invoke($controller);
				}
			}
		}


	}

?>