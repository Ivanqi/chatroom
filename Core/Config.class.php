<?php
	class Config{
		private static $ins	=	null;
		private $COF 	=	null;

		protected function __construct(){
			$this->COF  = C('DBINFO');
		}

		protected function __clone(){}

		public static function getIns(){
			if(!self::$ins instanceof self){
				self::$ins = new self;
			}
			return self::$ins;
		}
		public function __get($name){
			if(array_key_exists($name,$this->COF)){
				return $this->COF[$name];
			}else{
				return null;
			}
		}
		public function __set($key,$value){
			return $this->COF[$key] = $value;
		}
	}
	//$config = Config::getIns();

?>