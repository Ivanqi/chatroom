<?php
	/**
	* url 重定向
	* @param string $url 	重定向url
	* @param int 	$time 	重定向跳转时间
	* @param string $msg 	重定向提示信息
	* @return void
	*/
	function redirect($url,$time = 0,$msg=''){
		//多行url地址支持
		if(empty($msg)){
			$msg = "系统将在{$time}秒之后自动跳转到{$url}";
		}
		if(!headers_sent()){
			if(0 === $time){
				//echo 'Loaction: '.$url;
				header('Loaction: '.$url);
			}else{
				header("refresh:{$time};url={$url}");
				echo $msg;
			}
			exit;
		}else{
			$str = "<meta http-equiv='Refresh' content='{$time}';URL={$url}>";
			if($time != 0)
				$str .=  $msg;
			exit($str);
		}
	}

	/**
	* 反斜线转义
	* @param array $arr 要转义的数组
	* @return array 被转义后的数组
	*/
	function _addslashes($arr){
		foreach($arr as $k =>$v){
			if(is_string($v)){
				$arr[$k] = addslashes($v);
			}else if(is_array($v)){
				$arr[$k] = _addslashes($v);
			}
		}
		return $arr;
	}

	/**
	* thinkphp风格 URL组装
	* @param string $url 要解析的url
	* @param mixed(string/array) 传入的参数，支持字符串和数组
	* @return string 解析后的url
	*/
	function U($url='',$vars=''){
		//解析url
		$info 	=	parse_url($url);
		$url 	=	!empty($info['path'])?$info['path']:ACTION;

		//解析参数
		if(is_string($vars)){
			parse_str($vars,$vars);
		}elseif(!is_array($vars)){
			$vars = [];
		}

		//解析地址里的参数合并到 vars里
		if(isset($info['query'])){
			parse_str($info['query'],$params);
			$vars = array_merge($vars,$params);
		}

		$depr	=	'/';					//分割符

		//url 组装
		if($url){
			$url 	=	trim($url,$depr);
			$path	=	explode($depr,$url);
			$var 	=	array();

			$var['a']	=	!empty($path)?array_pop($path):ACTION;			//操作方法
			$var['c']	=	!empty($path)?array_pop($path):CONTROLLER;		//控制器

			$module	=	'';

			/*if(!empty($path)){
				$var['m']	=	explode($depr,$path);
			}else{
				$var['m']	=	MODULE;										//当前使用的模块
			}

			if(isset($var['m'])){
				$module = $var['m'];
				unset($var['m']);
			}*/

		}

		//$url  = _APP_.'/'.($module?$module.'/':'').implode($depr,array_reverse($var));
		$url  = _APP_.'/'.implode($depr,array_reverse($var));
		$url  = strtolower($url);
		if(!empty($vars)){  //添加参数
			foreach($vars as $var =>$val){
				if('' !== trim($val)) $url .= $depr.$var.$depr.urlencode($val);
			}
		}
		return $url;
	}


	/**
	* thinkphp C 函数
	* @param string $name 要进行查找的名字
	* @param string $value 要动态添加的属性值
	* @param string $default 默认值
	*/

	function C($name = null,$value =null,$default = null){
		static $_config = array();
		if(empty($name)){
			return false;
		}

		if(is_string($name)){
			if(!strpos($name,'.')){
				if(is_null($value))
					return isset($_config[$name])?$_config[$name]:$default;
				$_config[$name]	=	$value;
			}

			$name = explode('.',$name);
			if(is_null($value))
				return isset($_config[$name[0]])?$_config[$name[0]]:$default;
			$_config[$name[0]][$name[1]]	=	$value;
		}


		if(is_array($name)){
			$_config = array_merge($_config,$name);
			return ;
		}
	}
?>