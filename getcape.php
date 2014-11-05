<?php
require 'config.php';

if(isset($_REQUEST['user'])) {
	if(file_exists('capes/' . $_REQUEST['user'] . '.png')) {
		$filename = 'capes/' . $_REQUEST['user'] . '.png';
		$size = filesize($filename);
	}
	else if($CONFIG['getcape']) {
		$filename = 'http://s3.amazonaws.com/MinecraftCloaks/' . $_REQUEST['user'] . '.png';
		$headers = get_headers($filename, 1);
		if(isset($headers['Content-Length'])) {
			$size = $headers['Content-Length'];
		}
		else {
			return;
		}
	}
	else {
		return;
	}

	header('Content-Type: application/octet-stream');
	header('Content-Length: ' . $size);
	$file = fopen($filename, 'r');
	fpassthru($file);
	fclose($file);
}
?>
