<?php
require 'config.php';

if(isset($_REQUEST['user']) && isset($_REQUEST['sessionId']) && isset($_REQUEST['serverId'])) {
	header('Content-Type: text/plain');

	$mysql = new mysqli($CONFIG['host'], $CONFIG['user'], $CONFIG['pass'], $CONFIG['database']);

	$session = explode(':', $_REQUEST['sessionId'], 3);
	if(count($session) === 3 && $session[0] === 'token')
		$result = $mysql->query('SELECT * FROM ' . $CONFIG['table'] . ' WHERE username="' . $mysql->real_escape_string($_REQUEST['user']) . '" AND access_token="' . $mysql->real_escape_string($session[1]) . '" AND id="' . $mysql->real_escape_string($session[2]) . '"');
	else
		$result = $mysql->query('SELECT * FROM ' . $CONFIG['table'] . ' WHERE username="' . $mysql->real_escape_string($_REQUEST['user']) . '" AND session="' . $mysql->real_escape_string($_REQUEST['sessionId']) . '"');

	if($result !== FALSE) {
		$result->close();

		$mysql->query('UPDATE ' . $CONFIG['table'] . ' SET server="' . $mysql->real_escape_string($_REQUEST['serverId']) . '" WHERE username="' . $mysql->real_escape_string($_REQUEST['user']) . '"');

		echo 'OK';
	}
	else if($CONFIG['onlineauth']) {
		echo file_get_contents('http://session.minecraft.net/game/joinserver.jsp?user=' . urlencode($_REQUEST['user']) . '&sessionId=' . $_REQUEST['sessionId'] . '&serverId=' . $_REQUEST['serverId']);
	}
	else {
		echo 'Bad login';
	}

	$mysql->close();
}
?>
