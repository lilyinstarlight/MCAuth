<?php
require 'config.php';

if(isset($_REQUEST['user']) && isset($_REQUEST['sessionId']) && isset($_REQUEST['serverId'])) {
	$mysql = mysql_connect($CONFIG['server'], $CONFIG['user'], $CONFIG['pass']);
	mysql_select_db($CONFIG['database'], $mysql);
	$result = mysql_query('SELECT * FROM ' . $CONFIG['table'] . ' WHERE username="' . mysql_real_escape_string($_REQUEST['user']) . '" AND session="' . mysql_real_escape_string($_REQUEST['sessionId']) . '"');
	$array = mysql_fetch_array($result);
	if(mysql_num_rows($result) == 1) {
		$result = mysql_query('UPDATE ' . $CONFIG['table'] . ' SET server="' . mysql_real_escape_string($_REQUEST['serverId']) . '" WHERE username="' . mysql_real_escape_string($_REQUEST['user']) . '" AND session="' . mysql_real_escape_string($_REQUEST['sessionId']) . '"');
		echo 'OK';
	}
	else {
		echo 'Bad login';
	}
	mysql_close($mysql);
}
?>
