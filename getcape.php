<?php
if(isset($_REQUEST['user'])) {
	if(file_exists('capes/' . $_REQUEST['user'] . '.png')) {
		header('Content-Type: image/png');
		header('Content-Length:' . filesize('capes/' . $_REQUEST['user'] . '.png'));
		header('Content-Disposition: attachment;filename="capes/' . $_REQUEST['user'] . '.png"');
		$file=fopen('capes/' . $_REQUEST['user'] . '.png', 'r');
		fpassthru($file);
		fclose($file);
	}
	else {
		
	}
}
?>
