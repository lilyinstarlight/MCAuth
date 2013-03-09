<?php
if(isset($_REQUEST['user'])) {
	if(file_exists('skins/' . $_REQUEST['user'] . '.png')) {
		header('Content-Type: image/png');
		header('Content-Length:' . filesize('skins/' . $_REQUEST['user'] . '.png'));
		header('Content-Disposition: attachment;filename="skins/' . $_REQUEST['user'].'.png"');
		$file=fopen('skins/' . $_REQUEST['user'] . '.png', 'r');
		fpassthru($file);
		fclose($file);
	}
	else {
		
	}
}
?>
