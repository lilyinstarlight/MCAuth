<?php
require 'config.php';

$input = file_get_contents('php://input');
$json = json_decode($input, true);

if(isset($json['accessToken']) && isset($json['clientToken'])) {
	$mysql = new mysqli($CONFIG['host'], $CONFIG['user'], $CONFIG['pass'], $CONFIG['database']);
	$result = $mysql->query('SELECT * FROM ' . $CONFIG['table'] . ' WHERE accesstoken="' . $mysql->real_escape_string($json['accessToken']) . '" AND clientoken="' . $mysql->real_escape_string($json['clientToken']) . '"');
	if($result->num_rows === 1) {
		$array = $result->fetch_array(MYSQLI_ASSOC);
		do {
			$result->close();
			$accesstoken = dechex(rand(268435456, 4294967295));
			$result = $mysql->query('SELECT * FROM ' . $CONFIG['table'] . ' WHERE accesstoken="' . $accesstoken . '"');
		}
		while($result->num_rows != 0);
		$mysql->query('UPDATE ' . $CONFIG['table'] . ' SET accesstoken="' . $accesstoken . '" WHERE id=' . $array['id']);

		echo json_encode(array(
			'accessToken' => $accesstoken,
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
				'header' => 'Content-Type: application/json' + "\r\n",
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

	$result->close();
	$mysql->close();
}
else {
	echo $CONFIG['message'];
}
?>
