<?php
	class Mysqlis extends Db{
		protected static $ins = null;
		protected $data	= null;
		protected $conn = null;

		protected function __construct(){
			$this->data = Config::getIns();
			$this->connect($this->data->host,$this->data->user,$this->data->password);
			$this->setChar($this->data->char);
		}
		protected function __clone(){}

		public static function getIns(){
			if(!self::$ins instanceof self){
				self::$ins = new self;
			}
			return self::$ins;
		}

		protected function connect($h,$u,$p){
			$this->conn = new mysqli($h,$u,$p,$this->data->db,$this->data->port);
			if(!$this->conn){
				$err = new Exception('连接失败');
				throw $err;
			}
		}

		protected function setChar($char){
			$sql = 'set names '.$char;
			$this->query($sql);
		}

		protected function selectDb($dbname){
			$sql = 'use '.$dbname;
			$this->query($sql);
		}

		/**
		* 设置query 函数
		* @return mixed 返回一个结果集
		*/
		public function query($sql){
			$res = mysqli_query($this->conn,$sql);
			if(!$res){
				die(mysqli_error($this->conn));
			}
			//Log::write($sql);
			return $res;
		}

		public function getAll($sql){
			$arr = array();
			$rs  = $this->query($sql);
			while($row = mysqli_fetch_assoc($rs)){
				$arr[] = $row;
			}
			return $arr;
		}

		/**
		* 得到单条mysql数组
		* @return array 一维数组
		*/
		public function getOne($sql){
			$res = $this->query($sql);
			return mysqli_fetch_assoc($res);
		}

		public function autoExecute($table,$data,$act='insert',$where=''){
			if(!is_array($data)){
				return false;
			}
			if($act =='update'){
				$sql = 'UPDATE '.$table.' SET ';
				foreach($data as $k=>$v){
					$sql .= $k.'="'.$v.'",';
				}
				$sql =	rtrim($sql,',').' '.$where;
				return $this->query($sql);
			}
			$sql = 'INSERT INTO '.$table.'(';
			$sql .= implode(',',array_keys($data));
			$sql .= ') VALUES ("';
			$sql .= implode('","',array_values($data));
			$sql .= '")';
			return $this->query($sql);
		}

		public function affect_row(){
			return mysqli_affected_rows($this->conn);
		}

		public function insert_id(){
			return mysqli_insert_id($this->conn);
		}

		/**
		* @param resource $res 得到一个结果集
		* @return int  返回所有记录数
		*/
		public function num_rows($res){
			return  mysqli_num_rows($res);
		}
	}

?>