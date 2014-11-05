<?php
require 'config.php';

$input = file_get_contents('php://input');
$json = json_decode($input, true);

if(isset($json['accessToken']) && isset($json['clientToken'])) {
	$mysql = new mysqli($CONFIG['host'], $CONFIG['user'], $CONFIG['pass'], $CONFIG['database']);

	$result = $mysql->query('SELECT * FROM ' . $CONFIG['table'] . ' WHERE access_token="' . $mysql->real_escape_string($json['accessToken']) . '" AND client_token="' . $mysql->real_escape_string($json['clientToken']) . '"');
	if($result !== FALSE) {
		$array = $result->fetch_array(MYSQLI_ASSOC);

		do {
			$result->close();
			$access_token = dechex(rand(268435456, 4294967295));
			$result = $mysql->query('SELECT * FROM ' . $CONFIG['table'] . ' WHERE access_token="' . $access_token . '"');
		}
		while($result !== FALSE);
		$result->close();

		$mysql->query('UPDATE ' . $CONFIG['table'] . ' SET access_token="' . $access_token . '" WHERE id=' . $array['id']);

		echo json_encode(array(
			'accessToken' => $access_token,
			'clientToken' => $array['clientToken'],
			'selectedProfile' => array(
				'id' => $array['id'],
				'name' => $array['username']
			)
		));
	}
	else if($CONFIG['onlineauth']) {
		echo file_get_contents('https://authserver.mojang.com/refresh', false, stream_context_create(array(
			'http' => array(
				'method' => 'POST',
				'header' => 'Content-Type: application/json',
				'content' => $input
			)
		)));
	}
	else {
		echo json_encode(array(
			'error' => 'ForbiddenOperationException',
			'errorMessage' => 'Invalid token.'
		));
	}

	$mysql->close();
}
else {
	echo $CONFIG['message'];
}
?>
