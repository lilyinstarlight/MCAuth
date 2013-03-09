<?php
$CONFIG = array(
	//Database information
	//Server address, use localhost if unsure
	'server'	=> 'localhost',
	//Username for database
	'user'		=> 'root',
	//Password for database
	'pass'		=> 'toor',
	//MySQL database to connect to
	'database'	=> 'root',
	//Table containing the login information
	'table'		=> 'minecraft',
	/*Table format:
		id (int, AI)	username (varchar, 31)	password (char, 64)	session (char, 8)	server (varchar, 16)
	*/
	//Message to display when not accessed as an authenticator, can use HTML
	'message'	=> 'Welcome'
);
?>
