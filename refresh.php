<?php
require 'config.php';

$input = file_get_contents('php://input');
$json = json_decode($input, true);

if(isset($json['accessToken'])) {
	$mysql = new mysqli($CONFIG['host'], $CONFIG['user'], $CONFIG['pass'], $CONFIG['database']);

	$result = $mysql->query('SELECT * FROM ' . $CONFIG['table'] . ' WHERE access_token="' . $mysql->real_escape_string($json['accessToken']) . '"');
	if($result !== FALSE) {
		$array = $result->fetch_array(MYSQLI_ASSOC);

		do {
			$result->close();
			$access_token = dechex(rand(268435456, 4294967295));
			$result = $mysql->query('SELECT * FROM ' . $CONFIG['table'] . ' WHERE access_token="' . $access_token . '"');
		}
		while($result !== FALSE);

		$mysql->query('UPDATE ' . $CONFIG['table'] . ' SET access_token="' . $access_token . '" WHERE id=' . $array['id']);

		$response = array(
			'accessToken' => $access_token,
			'clientToken' => $json['clientToken']
		);

		if(isset($json['requestUser']) && $json['requestUser'] === TRUE) {
			$response['user'] = array(
				'id' => $array['id']
			);
		}

		echo json_encode($response);
	}
	else if($CONFIG['onlineauth']) {
		$mojang = file_get_contents('https://authserver.mojang.com/refresh', false, stream_context_create(array(
			'http' => array(
				'ignore_errors' => TRUE,
				'method' => 'POST',
				'header' => 'Content-Type: application/json'    . "\r\n" .
				            'Content-Length: ' . strlen($input) . "\r\n",
				'content' => $input
			)
		)));

		http_response_code(intval($http_response_header.split(' ')[1]));
		echo $mojang;
	}
	else {
		http_response_code(403);
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
