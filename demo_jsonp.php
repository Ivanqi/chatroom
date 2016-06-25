<?php
	$mysqli  = new mysqli('localhost','root','123456','wether');
	if($mysqli->connect_error){
		die( 'Connect Error ('.$mysqli->connect_errno.') '.$mysqli->connect_error);
	}
	$sql 	=	'select code,name from tiqi limit 10';
	$res 	=	$mysqli->query($sql);
	$json	=	[];
	while($row = $res->fetch_assoc()){
		$json[] = $row;
	}
	$json 	=	json_encode($json);
	$fn  = $_GET['fn'];
	echo $fn."($json)";
?>