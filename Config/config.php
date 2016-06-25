<?php
	return array(
		'DBTYPE'		=>	'Mysqlis',
		'DBPREFIX' 		=> 	'chat_',
		'DBINFO'		=>	 array(
			'host' 		=>	'localhost',
			'user' 		=>	'root',
			'password' 	=>	'123456',
			'db'		=>	'chatroom',
			'char'		=>	'utf8',
			'port'		=> 	3307
		),
		'CAPTCHATYPE'	=>	'POETRY', //CH|ZH|OPER|POETRY
		'CAPTCHAINFO'	=>	array(
			'width'		=>	185,
			'height'	=>	30,
			'length'	=>	5,
			'dots'		=>	70,
			'lines'		=>	4,
			'ttfPath'	=>	'captchattf/kai.ttf'
		),
	);

?>