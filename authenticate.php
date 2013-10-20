<?php
require 'config.php';

$input = file_get_contents('php://input');
$json = json_decode($input, true);

if(isset($json['username']) && isset($json['password']) && isset($json['clientToken'])) {
	$mysql = new mysqli($CONFIG['host'], $CONFIG['user'], $CONFIG['pass'], $CONFIG['database']);
	$result = $mysql->query('SELECT * FROM ' . $CONFIG['table'] . ' WHERE username="' . $mysql->real_escape_string($json['username']) . '" AND password="' . $mysql->real_escape_string(hash('sha256', $json['password'])) . '"');
	if($result->num_rows === 1) {
		$array = $result->fetch_array(MYSQLI_ASSOC);
		do {
			$result->close();
			$access_token = dechex(rand(268435456, 4294967295));
			$result = $mysql->query('SELECT * FROM ' . $CONFIG['table'] . ' WHERE access_token="' . $access_token . '"');
		}
		while($result->num_rows != 0);
		$mysql->query('UPDATE ' . $CONFIG['table'] . ' SET access_token="' . $access_token . '" AND client_token="' . $mysql->real_escape_string($json['clientToken']) . '" WHERE id=' . $array['id']);

		echo json_encode(array(
			'accessToken' => $access_token,
			'clientToken' => $json['clientToken'],
			'selectedProfile' => array(
				'id' => $array['id'],
				'name' => $array['username']
			),
			'availableProfiles' => array(
				array(
					'id' => $array['id'],
					'name' => $array['username']
				)
			)
		));
	}
	else if($CONFIG['onlineauth']) {
		echo file_get_contents('https://authserver.mojang.com/authenticate', false, stream_context_create(array(
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
			'errorMessage' => 'Invalid credentials. Invalid username or password.'
		));
	}

	$result->close();
	$mysql->close();
}
else {
	echo $CONFIG['message'];
}
?>
