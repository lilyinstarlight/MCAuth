<?php
require 'config.php';

if(isset($_REQUEST['user']) && isset($_REQUEST['serverId'])) {
	$mysql = mysql_connect($CONFIG['server'], $CONFIG['user'], $CONFIG['pass']);
	mysql_select_db($CONFIG['database'], $mysql);
	$result = mysql_query('SELECT * FROM ' . $CONFIG['table'] . ' WHERE username="' . mysql_real_escape_string($_REQUEST['user']) . '" AND server="' . mysql_real_escape_string($_REQUEST['serverId']) . '"');
	$array = mysql_fetch_array($result);
	if(mysql_num_rows($result) == 1)
		echo "YES";
	else if($CONFIG['onlineauth'])
		echo file_get_contents('http://session.minecraft.net/game/checkserver.jsp?user=' . $_REQUEST['user'] . '&serverId=' . $_REQUEST['serverId']);
	else
		echo "NO";
	mysql_close($mysql);
}
?>
