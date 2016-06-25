<?php

	class Session extends Model{
		protected  $ip			= null;				//客户端ip
		protected  $time 		= null;				//当前时间
		protected  $lifetime 	= null;				//生命周期
		protected  $table 		= 'session';
		protected  $pk 			=	's_id';

		public function __construct(){
			parent::__construct();
			$this->ip 		= !empty($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:'unknow';
			$this->time =  time();

			session_set_save_handler(
				array($this,'open'),array($this,'close'),
				array($this,'read'),array($this,'write'),
				array($this,'destroy'),array($this,'gc')
			);
			session_start();
		}


		public function open(){
			return true;
		}

		public function close(){
			return true;
		}

		public function read($id){
			$lifetime	=	ini_get('seesion.gc_maxlifetime');
			$row = $this->where("{$this->pk}='{$id}'")->find();

			if($this->ip !=$row['s_ip']){
				$this->destroy($id);
				return ;
			}

			/*if(($row['s_last'] + $lifetime) < $this->time){
				$this->destroy($id);
				return ;
			}*/
			return @$row['s_content'];
		}

		public function write($id,$content){
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
				$this->add($data);
			}
			return true;
		}

		public function destroy($id){
			if($this->where("{$this->pk} = '{$id}'")->delete()){
				$this->where("m_sessId='{$id}'")->table('member')->save(array('m_stauts' =>0));
			}
			return true;
		}

		public function gc(){
			//$lifetime	=	ini_get('seesion.gc_maxlifetime');
			$lifetime	=	100;

			$row = $this->where("s_last <".($this->time - $lifetime))->select();
			if($this->where("s_last <".($this->time - $lifetime))->delete()){
				foreach($row as $v){
					$this->where("m_sessId='{$v['s_id']}'")->table('member')->save(array('m_stauts'=>0));
				}
			}
			return true;
		}

	}

?>