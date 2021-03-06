<?php
require 'config.php';
require 'common.php';

$input = file_get_contents('php://input');
$json = json_decode($input, true);

if($json === null) {
	http_response_code(400);
	header('Content-Type: application/json');
	echo json_encode(array(
		'error' => 'JsonParseException',
		'errorMessage' => 'Error parsing JSON.'
	));
}
else if(isset($json['accessToken'])) {
	$mysql = new mysqli($CONFIG['host'], $CONFIG['user'], $CONFIG['pass'], $CONFIG['database']);

	$result = $mysql->query('SELECT * FROM ' . $CONFIG['table'] . ' WHERE access_token="' . $mysql->real_escape_string($json['accessToken']) . '"');
	if($result->num_rows === 1) {
		$array = $result->fetch_array(MYSQLI_ASSOC);

		$access_token = gen_uniq($mysql, $CONFIG['table'], 'access_token');

		$mysql->query('UPDATE ' . $CONFIG['table'] . ' SET access_token="' . $access_token . '" WHERE id="' . $array['id'] . '"');

		$response = array(
			'accessToken' => $access_token,
			'clientToken' => $array['client_token']
		);

		if(isset($json['requestUser']) && $json['requestUser'] === true) {
			$response['selectedProfile'] = array(
				'id' => $array['id'],
				'name' => $array['username']
			);
			$response['user'] = array(
				'id' => $array['id']
			);
		}

		header('Content-Type: application/json');
		echo json_encode($response);
	}
	else if($CONFIG['onlineauth']) {
		$mojang = file_get_contents('https://authserver.mojang.com/refresh', false, stream_context_create(array(
			'http' => array(
				'ignore_errors' => true,
				'method' => 'POST',
				'header' => 'Content-Type: application/json'    . "\r\n" .
				            'Content-Length: ' . strlen($input) . "\r\n",
				'content' => $input
			)
		)));

		http_response_code(intval(explode(' ', $http_response_header[0], 3)[1]));
		header('Content-Type: application/json');
		echo $mojang;
	}
	else {
		http_response_code(403);
		header('Content-Type: application/json');
		echo json_encode(array(
			'error' => 'ForbiddenOperationException',
			'errorMessage' => 'Invalid token.'
		));
	}
	$result->free();

	$mysql->close();
}
else {
	http_response_code(400);
	header('Content-Type: application/json');
	echo json_encode(array(
		'error' => 'IllegalArgumentException',
		'errorMessage' => 'Access Token can not be null or empty.'
	));
}
?>
