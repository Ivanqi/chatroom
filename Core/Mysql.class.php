<?php
	class Mysql extends Db{
		protected static $ins = null;
		protected $data	= null;
		protected $conn = null;

		protected function __construct(){
			$this->data = Config::getIns();
			$this->connect($this->data->host,$this->data->user,$this->data->password);
			$this->setChar($this->data->char);
			$this->selectDb($this->data->database);
		}
		protected function __clone(){}

		public static function getIns(){
			if(!self::$ins instanceof self){
				self::$ins = new self;
			}
			return self::$ins;
		}

		protected function connect($h,$u,$p){
			$this->conn = mysql_connect($h,$u,$p);
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

		public function query($sql){
			$result = mysql_query($sql);
			Log::write($sql);
			return $result;
		}

		public function getAll($sql){
			$arr = array();
			$rs  = $this->query($sql);
			while($row = mysql_fetch_assoc($rs)){
				$arr[] = $row;
			}
			return $arr;
		}

		public function getRow($sql){
			$rs  = $this->query($sql);
			return mysql_fetch_array($rs);
		}
		public function getOne($sql){
			$rs  = $this->query($sql);
			return mysql_fetch_row($rs);
		}

		public function autoExecute($table,$data,$act='insert',$where=''){
			if(!is_array($data)){
				return false;
			}
			if($act =='update'){
				$sql = 'update '.$table.' set ';
				foreach($data as $k=>$v){
					$sql .= $k.'="'.$v.'",';
				}
				$sql =	rtrim($sql,',').' '.$where;
				return $this->query($sql);
			}
			$sql = 'insert into '.$table.'(';
			$sql .= implode(',',array_keys($data));
			$sql .= ') values("';
			$sql .= implode('","',array_values($data));
			$sql .= '")';
			return $this->query($sql);
		}

		public function affect_row(){
			return mysql_affected_rows($this->conn);
		}

		public function insert_id(){
			return mysql_insert_id($this->conn);
		}

		/**
		* @param resource $res 得到一个结果集
		* @return int  返回所有记录数
		*/
		public function num_rows($res){
			return  mysql_num_rows($res);
		}
	}

?>