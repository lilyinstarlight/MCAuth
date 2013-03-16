<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Account Settings</title>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<link href="common.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?php
require 'config.php';

session_start();
if(!isset($_SESSION['user']) && isset($_REQUEST['user'])) {
	$mysql = mysql_connect($CONFIG['server'], $CONFIG['user'], $CONFIG['pass']);
	mysql_select_db($CONFIG['database'], $mysql);
	$result = mysql_query('SELECT * FROM ' . $CONFIG['table'] . ' WHERE username="' . mysql_real_escape_string($_REQUEST['user']) . '" AND password="' . mysql_real_escape_string(hash('sha256', $_REQUEST['password'])) . '"');
	if(mysql_num_rows($result) == 1) {
		$_SESSION['user'] = $_REQUEST['user'];
	}
	else {
		echo '<span class="failure">Error: Wrong password.</span><br />';
	}
	mysql_close($mysql);
}
if(isset($_REQUEST['logout'])) {
	unset($_SESSION['user']);
}
if(isset($_SESSION['user'])) {
?>
<div class="logout">
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
<div>
<input name="logout" type="hidden" value="logout" />
<input name="submit_logout" type="submit" value="Logout" />
</div>
</form>
</div>
<p>
<?php
	if(($CONFIG['changeuser'] && !empty($_REQUEST['newuser'])) || !empty($_REQUEST['newpassword'])) {
		$mysql = mysql_connect($CONFIG['server'], $CONFIG['user'], $CONFIG['pass']);
		mysql_select_db($CONFIG['database'], $mysql);

		$error = false;
		$query = '';
		if($CONFIG['changeuser'] && !empty($_REQUEST['newuser'])) {
			if(!isset($_REQUEST['password']) || $_REQUEST['password'] == '') {
				$error = true;
				echo '<span class="failure">Error: Please enter your current password.</span><br />';
			}

			$result = mysql_query('SELECT * FROM ' . $CONFIG['table'] . ' WHERE username="' . mysql_real_escape_string($_REQUEST['newuser']) . '"');
			if(mysql_num_rows($result) != 0) {
				$error=true;
				echo '<span class="failure">Error: Username already registered.</span><br />';
			}
			$query = 'username="' . mysql_real_escape_string($_REQUEST['newuser']) . '"';
		}
		if(!empty($_REQUEST['newpassword'])) {
			if(!isset($_REQUEST['password']) || $_REQUEST['password'] == '') {
				$error = true;
				echo '<span class="failure">Error: Please enter your current password.</span><br />';
			}
			if($_REQUEST['newpassword'] != $_REQUEST['newpasswordConfirm']) {
				$error = true;
				echo '<span class="failure">Error: Passwords did not match.</span><br />';
			}
			if(isset($_REQUEST['password']) && $_REQUEST['password'] == $_REQUEST['newpassword']) {
				$error = true;
				echo '<span class="failure">Error: Password unchanged</span><br />';
			}
			$query = (empty($query) ? '' : $query . ' AND ') . 'password="' . mysql_real_escape_string(hash('sha256', $_REQUEST['newpassword'])) . '"';
		}
		if(!$error) {
			$result = mysql_query('UPDATE ' . $CONFIG['table'] . ' SET ' . $query . ' WHERE username="' . mysql_real_escape_string($_SESSION['user']) . '" AND password="' . mysql_real_escape_string(hash('sha256', $_REQUEST['password'])) . '"');
			if(mysql_affected_rows() == 1) {
				echo '<span class="success">Successfully updated.</span><br />';
				if(isset($_REQUEST['newuser'])) {
					if(file_exists('skins/' . addslashes($_SESSION['user']) . '.png'))
						rename('skins/' . addslashes($_SESSION['user']) . '.png', 'skins/' . addslashes($_REQUEST['newuser']) . '.png');
					if(file_exists('capes/' . addslashes($_SESSION['user']) . '.png'))
						rename('capes/' . addslashes($_SESSION['user']) . '.png', 'capes/' . addslashes($_REQUEST['newuser']) . '.png');
					if($CONFIG['playerdata'] != '' && file_exists($CONFIG['playerdata'] . '/' . addslashes($_SESSION['user']) . '.dat'))
						rename($CONFIG['playerdata'] . '/' . addslashes($_SESSION['user']) . '.dat', $CONFIG['playerdata'] . '/' . addslashes($_REQUEST['newuser']) . '.dat');
					$_SESSION['user'] = $_REQUEST['newuser'];
				}
			}
			else {
				echo '<span class="failure">Error: Wrong password.</span><br />';
			}
		}
		mysql_close($mysql);
	}
	if(isset($_FILES['skin']) && file_exists($_FILES['skin']['tmp_name'])) {
		$error = false;
		if($_FILES['skin']['size'] > 2097152) {
			$error=true;
			echo '<span class="failure">Error: Skin size is too big.  Must be less than 2 MB.</span><br />';
		}
		if($_FILES['skin']['type'] != 'image/png') {
			$error=true;
			echo '<span class="failure">Error: Skin file must be in PNG format.</span><br />';
		}
		list($width, $height) = getimagesize($_FILES['skin']['tmp_name']);
		if($width != 64 || $height != 32) {
			$error=true;
			echo '<span class="failure">Error: Skin file must be 64px by 32px.</span><br />';
		}
		if(!$error) {
			move_uploaded_file($_FILES['skin']['tmp_name'], 'skins/' . addslashes($_SESSION['user']) . '.png');
			echo '<span class="success">Skin successfully updated.</span><br />';
		}
	}
	if(isset($_REQUEST['removeskin'])) {
		if(file_exists('skins/' . addslashes($_SESSION['user']) . '.png')) {
			unlink('skins/' . addslashes($_SESSION['user']) . '.png');
			echo '<span class="success">Skin successfully removed.</span><br />';
		}
		else {
			echo '<span class="failure">Error: You must have a skin to remove it.</span><br />';
		}
	}
?>
</p>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
<table>
<tr>
<td><label for="newuser">Username: </label></td>
<td><input id="newuser" name="newuser" type="text" value="<?php echo $_SESSION['user']; ?>"<?php echo $CONFIG['changeuser'] ? '' : ' disabled="disabled"'; ?> /></td>
</tr>
<tr>
<td><label for="password">Current Password: </label><br />Only if changing<?php echo $CONFIG['changeuser'] ? ' username or' : ''; ?> password.</td>
<td><input id="password" name="password" type="password" /></td>
</tr>
<tr>
<td><label for="newpassword">Password: </label></td>
<td><input id="newpassword" name="newpassword" type="password" /></td>
</tr>
<tr>
<td><label for="newpasswordConfirm">Confirm Password: </label></td>
<td><input id="newpasswordConfirm" name="newpasswordConfirm" type="password" /></td>
</tr>
<tr>
<td><label for="skin">Skin File: </label><input name="MAX_FILE_SIZE" type="hidden" value="2097152" /></td>
<td>
<?php if(file_exists('skins/' . addslashes($_SESSION['user']) . '.png')) echo '<input id="removeskin" name="removeskin" type="checkbox" /><label for="removeskin" style="font-weight: normal;">Remove skin</label><br />'; ?>
<input id="skin" name="skin" type="file" />
</td>
</tr>
<tr>
<td></td>
<td><br /><input name="submit" type="submit" value="Submit"/></td>
</tr>
</table>
</form>
<?php
}
else {
?>
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
<td></td>
<td><br /><input name="submit" type="submit" value="Submit"/></td>
</tr>
</table>
</form>
<?php
}
?>
</body>
</html>
