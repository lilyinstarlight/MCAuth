<?php
require 'config.php';

if(isset($_REQUEST['user']) && isset($_REQUEST['password'])) {
	$mysql = mysql_connect($CONFIG['server'], $CONFIG['user'], $CONFIG['pass']);
	mysql_select_db($CONFIG['database'], $mysql);
	$result = mysql_query('SELECT * FROM ' . $CONFIG['table'] . ' WHERE username="' . mysql_real_escape_string($_REQUEST['user']) . '" AND password="' . mysql_real_escape_string(hash('sha256', $_REQUEST['password'])) . '"');
	$array = mysql_fetch_array($result);
	if(mysql_num_rows($result) == 1) {
		do {
			$id = dechex(rand(268435456, 4294967295));
			$result = mysql_query('SELECT * FROM ' . $CONFIG['table'] . ' WHERE session="' . $id . '"');
		} while(mysql_num_rows($result) != 0);
		mysql_query('UPDATE ' . $CONFIG['table'] . ' SET session="' . $id . '" WHERE id=' . $array['id']);
		echo '1357737036000' . ':' . 'deprecated' . ':' . $array['username'] . ':' . $id . ':';
	}
	else if($CONFIG['onlineauth'] && isset($_REQUEST['version'])) {
		echo file_get_contents('http://login.minecraft.net/?user=' . $_REQUEST['user'] . '&password=' . $_REQUEST['password'] . '&version=' . $_REQUEST['version']);
	}
	else {
		echo 'Bad login';
	}
	mysql_close($mysql);
}
else {
	echo $CONFIG['message'];
}
?>
