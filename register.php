<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Register</title>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<link href="common.css" rel="stylesheet" type="text/css" />
</head>
<body>
<p>
<?php
require 'config.php';

if(isset($_REQUEST['user']) && isset($_REQUEST['password']) && isset($_REQUEST['passwordconfirm'])) {
	$error = false;

	if($_REQUEST['user'] === '') {
		$error = true;
		echo '<span class="failure">Error: Enter a username</span><br />';
	}
	if($_REQUEST['password'] === '') {
		$error = true;
		echo '<span class="failure">Error: Enter a password</span><br />';
	}
	else if($_REQUEST['password'] !== $_REQUEST['passwordconfirm']) {
		$error = true;
		echo '<span class="failure">Error: Passwords do not match</span><br />';
	}
	if(!$error && file_exists($_FILES['skin']['tmp_name'])) {
		if($_FILES['skin']['size'] > 2097152) {
			$error = true;
			echo '<span class="failure">Error: Skin size is too big: must be less than 2 MB</span><br />';
		}
		if($_FILES['skin']['type'] !== 'image/png') {
			$error = true;
			echo '<span class="failure">Error: Skin file must be in PNG format</span><br />';
		}
		list($width, $height) = getimagesize($_FILES['skin']['tmp_name']);
		if($width !== 64 || $height !== 32) {
			$error = true;
			echo '<span class="failure">Error: Skin file must be 64px by 32px</span><br />';
		}
	}

	$mysql = new mysqli($CONFIG['host'], $CONFIG['user'], $CONFIG['pass'], $CONFIG['database']);

	$result = $mysql->query('SELECT * FROM ' . $CONFIG['table'] . ' WHERE username="' . $mysql->real_escape_string($_REQUEST['user']) . '"');
	if($result->num_rows === 1) {
		$error = true;
		echo '<span class="failure">Error: Username already registered</span><br />';
	}
	$result->free();

	if(!$error) {
		$result = $mysql->query('INSERT INTO ' . $CONFIG['table'] . ' (username, password) VALUES("' . $mysql->real_escape_string($_REQUEST['user']) . '", "' . $mysql->real_escape_string(hash('sha256', $_REQUEST['password'])) . '")');
		if($result === true) {
			if(file_exists($_FILES['skin']['tmp_name'])) {
				move_uploaded_file($_FILES['skin']['tmp_name'], 'skins/' . addslashes($_REQUEST['user']) . '.png');
			}
			unset($_REQUEST['user']);

			echo '<span class="success">Successfully registered</span><br />';
		}
		else {
			echo '<span class="failure">Error: Could not add user to database</span><br />';
		}
	}

	$mysql->close();
}
?>
</p>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
<table>
<tr>
<td><label for="user">Username: </label></td>
<td><input id="user" name="user" type="text" value="<?php echo isset($_REQUEST['user']) ? $_REQUEST['user'] : ''; ?>" /></td>
</tr>
<tr>
<td><label for="password">Password: </label></td>
<td><input id="password" name="password" type="password" /></td>
</tr>
<tr>
<td><label for="passwordconfirm">Confirm Password: </label></td>
<td><input id="passwordconfirm" name="passwordconfirm" type="password" /></td>
</tr>
<tr>
<td><label for="skin">Skin File: </label><input name="MAX_FILE_SIZE" type="hidden" value="2097152" /></td>
<td><input id="skin" name="skin" type="file" /></td>
</tr>
<tr>
<td></td>
<td><br /><input name="submit" type="submit" value="Submit"/></td>
</tr>
</table>
</form>
</body>
</html>
