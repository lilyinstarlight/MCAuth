<?php
$CONFIG = array(
	//Database information

	//Server address, use localhost if unsure
	'server'	=> 'localhost',

	//Username for database
	'user'		=> 'root',

	//Password for database
	'pass'		=> 'letmein',

	//MySQL database to connect to
	'database'	=> 'database',

	//Table containing the login information
	'table'		=> 'minecraft',
	/*Table format:
		id (int, AI)	username (varchar, 31)	password (char, 64)	session (char, 8)	server (varchar, 16)
	*/

	//Message to display when not accessed as an authenticator, can use HTML
	'message'	=> 'Welcome',

	//Whether or not to contact minecraft.net to get an unknown skin
	'getskin'	=> true,

	//Whether or not to contact minecraft.net to get an unknown cape
	'getcape'	=> true

	//Whether or not to contact minecraft.net to authenticate an unknown user
	'onlineauth'	=> false
);
?>
