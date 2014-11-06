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
else if(isset($json['username']) && isset($json['password'])) {
	$mysql = new mysqli($CONFIG['host'], $CONFIG['user'], $CONFIG['pass'], $CONFIG['database']);

	$result = $mysql->query('SELECT * FROM ' . $CONFIG['table'] . ' WHERE username="' . $mysql->real_escape_string($json['username']) . '" AND password="' . $mysql->real_escape_string(hash('sha256', $json['password'])) . '"');
	if($result->num_rows === 1) {
		$array = $result->fetch_array(MYSQLI_ASSOC);

		$access_token = gen_uniq($mysql, $CONFIG['table'], 'access_token');

		if(isset($json['clientToken']))
			$client_token = $mysql->real_escape_string($json['clientToken']);
		else
			$client_token = gen_uniq($mysql, $CONFIG['table'], 'client_token');

		$mysql->query('UPDATE ' . $CONFIG['table'] . ' SET access_token="' . $access_token . '", client_token="' . $client_token . '" WHERE id="' . $array['id'] . '"');

		$response = array(
			'accessToken' => $access_token,
			'clientToken' => $client_token
		);

		if(isset($json['requestUser']) && $json['requestUser'] === true) {
			$response['selectedProfile'] = array(
				'id' => $array['id'],
				'name' => $array['username']
			);
			$response['availableProfiles'] = array(
				array(
					'id' => $array['id'],
					'name' => $array['username']
				)
			);
			$response['user'] = array(
				'id' => $array['id']
			);
		}

		header('Content-Type: application/json');
		echo json_encode($response);
	}
	else if($CONFIG['onlineauth']) {
		$mojang = file_get_contents('https://authserver.mojang.com/authenticate', false, stream_context_create(array(
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
			'errorMessage' => 'Invalid credentials. Invalid username or password.'
		));
	}
	$result->free();

	$mysql->close();
}
else {
	http_response_code(403);
	header('Content-Type: application/json');
	echo json_encode(array(
		'error' => 'ForbiddenOperationException',
		'errorMessage' => 'Invalid credentials. Invalid username or password.'
	));
}
?>
