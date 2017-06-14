<?php
function change($stem) {
	return 0;
}

header('Access-Control-Allow-Origin: https://hageveld.github.io');
header('Access-Control-Allow-Credentials: true');
date_default_timezone_set('Europe/Amsterdam');
session_start();
$db = mysqli_connect();
if (isset($_SESSION['auth']) && isset($_GET['stem']) && is_numeric($_GET['stem']) && intval($_GET['stem']) >= 1 && intval($_GET['stem']) <= 5) {
	$stem = change($_GET['stem']);
	$identiteit = sha1($seed . $_SESSION['auth']['volledigenaam'] . $_SESSION['auth']['geboortedatum']);
	if (mysqli_num_rows(mysqli_query($db, "SELECT * FROM mrstemmen WHERE Identiteit='$identiteit'")) == 0) {
		mysqli_query($db, "INSERT INTO mrstemmen VALUES ('','$identiteit'," . $stem . ")");
	}
}
elseif (!isset($_GET['stem'])) {
	header('Location: https://luithollander.nl');
}

session_destroy();
mysqli_close($db);