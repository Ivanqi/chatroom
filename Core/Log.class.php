<?php
	//SQL 记录类
	class Log{
		const Log = 'curr.log';
		static public function write($cont){
			$cont .=	'            '.date("Y-m-d H:i:s")."\r\n\r\n";
			$path = self::backups();
			$fw = fopen($path,'ab');
			fwrite($fw,$cont);
			fclose($fw);
		}

		protected static function isbak($path){
			$newname = $path.'_'.date('Y-m-d').mt_rand(10000,99999).'.bak';
			rename($path,$newname);
		}

		protected static function backups(){
			$dir = ROOT.'/data/';
			if(!is_dir($dir)){
				mkdir($dir,0777,true);
			}
			$path = $dir.self::Log;
			if(!file_exists($path)){
				touch($path);
			}
			clearstatcache($path,true);
			if(filesize($path) > (pow(1024,2)*2)){
				self::isbak($path);
			}
			return $path;
		}
	}
?>