<?php
require 'config.php';

if(isset($_REQUEST['user']) && isset($_REQUEST['sessionId']) && isset($_REQUEST['serverId'])) {
	$mysql = new mysqli($CONFIG['host'], $CONFIG['user'], $CONFIG['pass'], $CONFIG['database']);
	$result = $mysql->query('SELECT * FROM ' . $CONFIG['table'] . ' WHERE username="' . $mysql->real_escape_string($_REQUEST['user']) . '" AND session="' . $mysql->real_escape_string($_REQUEST['sessionId']) . '"');
	if($result->num_rows === 1) {
		$mysql->query('UPDATE ' . $CONFIG['table'] . ' SET server="' . $mysql->real_escape_string($_REQUEST['serverId']) . '" WHERE username="' . $mysql->real_escape_string($_REQUEST['user']) . '" AND session="' . $mysql->real_escape_string($_REQUEST['sessionId']) . '"');
		echo 'OK';
	}
	else if($CONFIG['onlineauth']) {
		echo file_get_contents('http://session.minecraft.net/game/joinserver.jsp?user=' . $_REQUEST['user'] . '&sessionId=' . $_REQUEST['sessionId'] . '&serverId=' . $_REQUEST['serverId']);
	}
	else {
		echo 'Bad login';
	}

	$result->close();
	$mysql->close();
}
?>
