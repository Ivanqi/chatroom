<?php
	class Session extends Model{
		protected  $ip 			= null;				//客户端ip
		protected  $time 		= null;				//当前时间
		protected  $lifetime 	= null;				//生命周期
		protected  $table 		= 'session';
		protected  $pk 			=	's_id';

		/**
		* 初始化
		* @access public
		* @return void
		*/
		public  function __construct(){
			parent::__construct();
			$this->ip 		= !empty($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:'unknow';
			$this->time  	=	time();

			session_set_save_handler(
				array($this,'open'),array($this,'close'),
				array($this,'read'),array($this,'write'),
				array($this,'destroy'),array($this,'gc')
			);

			session_start();
		}

		/**
		* 初始化session
		* @access public
		* @param string $path session 存储文件的路径
		* @param string $name sessionID sessionID 的名称
		*/
		public  function open($path,$name){
			return true;
		}

		/**
		* session要要销毁的时候触发
		* @access public
		*/
		public  function close(){
			return true;
		}

		/**
		* 进行session 数据查询 读取session数据到 $_SESSION数组中
		* @access public
		* @param string $id sessionID
		* @return string 把数据库保存信息，读取到$_SESSION数据中
		*/
		public  function read($id){
			$lifetime	=	ini_get('seesion.gc_maxlifetime');
			$row = $this->field("s_content")->where(" {$this->pk} = '{$id}' and  s_last <= ".($this->time - $lifetime))->find();
			return $row?$row['s_content']:'';
		}

		/**
		* 进行session数据写入 强制提交SESSION数据
		* @access public
		* @param string $id sessionID
		* @param string $content $_SESSION数组存储的数据
		* @param boolean true
		*/
		public  function write($id,$content){
			$lifetime	=	ini_get('seesion.gc_maxlifetime');
			$row = $this->field("s_content")->where(" {$this->pk} = '{$id}' and  s_last <= ".($this->time - $lifetime))->find();
			if(!empty($row)){
				$sql = "replace  into ".$this->table."({$this->pk},s_ip,s_last,s_content) values('{$id}','{$this->ip}',{$this->time},'{$content}')";
				$this->query($sql);
			}else{
				$data = array(
					's_id' =>$id,
					's_ip'=>$this->ip,
					's_last'=>$this->time,
					's_content'=>$content
				);
				$this->data($data)->add();
			}
			return true;
		}

		/**
		* 删除数据库中的session数据
		* @access public
		* @param string $id sessionID
		* @return boolean
		*/
		public  function destroy($id){
			$this->where("{$this->pk} = '{$id}'")->delete();
			return true;
		}

		/**
		* 删除数据库过期的session数据
		* @access public
		* @param int $lifttime session 生命周期
 		*/
		public  function gc($lifetime){
			$this->where("s_last <".($this->time - $lifetime))->delete();
			return true;
		}

	}
?>