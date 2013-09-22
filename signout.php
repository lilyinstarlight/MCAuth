<?php
require 'config.php';

$input = file_get_contents('php://input');
$json = json_decode($input, true);

if(isset($json['user']) && isset($json['password']) && isset($json['clientToken'])) {
	$mysql = new mysqli($CONFIG['host'], $CONFIG['user'], $CONFIG['pass'], $CONFIG['database']);
	$result = $mysql->query('SELECT * FROM ' . $CONFIG['table'] . ' WHERE username="' . $mysql->real_escape_string($json['user']) . '" AND password="' . $mysql->real_escape_string(hash('sha256', $json['password'])) . '"');
	if($result->num_rows === 1) {
		$array = $result->fetch_array(MYSQLI_ASSOC);
		$mysql->query('UPDATE ' . $CONFIG['table'] . ' SET accesstoken="" AND clienttoken="" WHERE id=' . $array['id']);

		echo json_encode();
	}
	else if($CONFIG['onlineauth']) {
		echo file_get_contents('https://authserver.mojang.com/authenticate', false, stream_context_create(array(
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
