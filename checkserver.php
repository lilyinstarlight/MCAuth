<?php
require 'config.php';

if(isset($_REQUEST['user']) && isset($_REQUEST['serverId'])) {
	$mysql = new mysqli($CONFIG['host'], $CONFIG['user'], $CONFIG['pass'], $CONFIG['database']);

	$result = $mysql->query('SELECT * FROM ' . $CONFIG['table'] . ' WHERE username="' . $mysql->real_escape_string($_REQUEST['user']) . '" AND server="' . $mysql->real_escape_string($_REQUEST['serverId']) . '"');
	if($result !== FALSE) {
		$result->close();

		echo 'YES';
	}
	else if($CONFIG['onlineauth']) {
		echo file_get_contents('http://session.minecraft.net/game/checkserver.jsp?user=' . urlencode($_REQUEST['user']) . '&serverId=' . $_REQUEST['serverId']);
	}
	else {
		echo 'NO';
	}

	$mysql->close();
}
?>
