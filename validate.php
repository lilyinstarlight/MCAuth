<?php
require 'config.php';

$input = file_get_contents('php://input');
$json = json_decode($input, true);

if($json === NULL) {
	http_response_code(400);
	echo json_encode(array(
		'error' => 'JsonParseException',
		'errorMessage' => 'Error parsing JSON.'
	));
}
else if(isset($json['accessToken'])) {
	$mysql = new mysqli($CONFIG['host'], $CONFIG['user'], $CONFIG['pass'], $CONFIG['database']);

	$result = $mysql->query('SELECT * FROM ' . $CONFIG['table'] . ' WHERE access_token="' . $mysql->real_escape_string($json['accessToken']) . '"');
	if($result !== FALSE) {
		$result->close();

		http_response_code(204);
	}
	else if($CONFIG['onlineauth']) {
		$mojang = file_get_contents('https://authserver.mojang.com/validate', false, stream_context_create(array(
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
			'errorMessage' => 'Invalid token'
		));
	}

	$mysql->close();
}
else {
	http_response_code(400);
	echo json_encode(array(
		'error' => 'IllegalArgumentException',
		'errorMessage' => 'Access Token can not be null or empty.'
	));
}
?>
