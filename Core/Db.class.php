<?php
abstract class Db{
	abstract protected function connect($h,$u,$p);
	abstract public function query($sql);
	abstract public function getOne($sql);
	abstract public function getAll($sql);
	abstract public function autoExecute($table,$data,$act='insert',$where='');
}

?>