<?php
function gen_uniq($mysql, $table, $col) {
	do {
		$uniq = bin2hex(openssl_random_pseudo_bytes(16));
		$result = $mysql->query('SELECT * FROM ' . $table . ' WHERE ' . $col . '="' . $uniq . '"');
	}
	while($result->num_rows !== 0);
	$result->free();

	return $uniq;
}
?>
