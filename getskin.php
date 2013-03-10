<?php
require 'config.php';

if(isset($_REQUEST['user'])) {
	if(file_exists('skins/' . $_REQUEST['user'] . '.png')) {
		$filename = 'skins/' . $_REQUEST['user'] . '.png';
		$size = filesize($filename);
	}
	else if($CONFIG['getskin']) {
		$filename = 'http://s3.amazonaws.com/MinecraftSkins/' . $_REQUEST['user'] . '.png';
		$headers = get_headers($filename, 1);
		if(isset($headers['Content-Length'])) {
			$size = $headers['Content-Length'];
		}
		else {
			$filename = 'steve.png';
			$size = filesize($filename);
		}
	}
	else {
		$filename = 'steve.png';
		$size = filesize($filename);
	}

	header('Content-Type: image/png');
	header('Content-Length:' . $size);
	header('Content-Disposition: attachment;filename="' . $_REQUEST['user'] . '.png"');
	$file=fopen($filename, 'r');
	fpassthru($file);
	fclose($file);
}
?>
