<?php
$CONFIG = array(
	//MySQL host address, use localhost if unsure
	'host'		=> 'localhost',

	//Username for database
	'user'		=> 'root',

	//Password for database
	'pass'		=> 'letmein',

	//MySQL database to connect to
	'database'	=> 'database',

	//Table containing the login information
	//Table format: id (int, AI)   username (varchar, 31)   password (char, 64)   access_token (char, 8)   client_token (varchar, 36)   session (char, 8)   server (varchar, 41)
	'table'		=> 'minecraft',

	//Message to display when not accessed as an authenticator; can use HTML
	'message'	=> '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><title>MCAuth</title><meta http-equiv="Content-Type" content="text/html;charset=utf-8" /><link href="common.css" rel="stylesheet" type="text/css" /></head><body><p>Welcome to the MCAuth authentication server. To register yourself, go to <a href="register.php">Register</a>. To change your skin or information, go to <a href="login.php">Login</a>.</p></body></html>',

	//Whether or not to contact minecraft.net to get an unknown skin
	'getskin'	=> true,

	//Whether or not to contact minecraft.net to get an unknown cape
	'getcape'	=> true,

	//Whether or not to contact authserver.mojang.com to authenticate an unknown user
	'onlineauth'	=> false,

	//Whether or not to allow users to change their username
	'changeuser'	=> false,

	//Folder where the player's .dat files are stored
	//If changeuser is true and this field is set, it will move the player's .dat files to keep their items and location when they change names
	//Supports FTP (ftp://username:password@address) and SFTP (ssh2.sftp://username:password@address:22) connections assuming they are enabled on the php server
	'playerdata'	=> '/home/minecraft/world/players',

	//Only for pre-1.6 clients
	//Fallback Minecraft release date
	//The script will not contact mcupdate.tumblr.com to check the current minecraft version if this field is set
	'version'	=> ''
);
?>
