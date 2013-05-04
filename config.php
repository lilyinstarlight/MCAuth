<?php
$CONFIG = array(
	//Database information

	//MySQL host address, use localhost if unsure
	'host'		=> 'localhost',

	//Username for database
	'user'		=> 'root',

	//Password for database
	'pass'		=> 'letmein',

	//MySQL database to connect to
	'database'	=> 'database',

	//Table containing the login information
	//Table format:   id (int, AI)   username (varchar, 31)   password (char, 64)   session (char, 8)   server (varchar, 41)
	'table'		=> 'minecraft',

	//Folder where the player's .dat files are stored
	//The script will not move the .dat file after someone renames themselves if this field is blank
	//Supports FTP (ftp://username:password@address) and SFTP (ssh2.sftp://username:password@address:22) connections assuming they are enabled on the php server
	'playerdata'	=> '/home/minecraft/world/players',

	//Message to display when not accessed as an authenticator; can use HTML
	'message'	=> 'Welcome',

	//Whether or not to contact minecraft.net to get an unknown skin
	'getskin'	=> true,

	//Whether or not to contact minecraft.net to get an unknown cape
	'getcape'	=> true,

	//Whether or not to contact minecraft.net to authenticate an unknown user
	'onlineauth'	=> false,

	//Whether or not to allow users to change their username
	'changeuser'	=> false,

	//Fallback Minecraft release date
	//The script will not contact mcupdate.tumblr.com to check the current minecraft version if this field is set
	'version'	=> ''
);
?>
