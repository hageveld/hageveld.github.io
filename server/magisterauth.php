<?php
header('Access-Control-Allow-Origin: https://hageveld.github.io');
header('Access-Control-Allow-Credentials: true');
try {
	$db = mysqli_connect();
	session_start();
	if (isset($_SESSION['auth']) && $_GET['username'] == $_SESSION['auth']['username']) {
		sleep(1);
		echo json_encode($_SESSION['auth']);
		mysqli_close($db);
		die();
	}

	if (isset($_GET['password']) && isset($_GET['username'])) {
		if (!isset($_GET['attempt'])) {
			$_GET['attempt'] = 1;
		}
		elseif ($_GET['attempt'] > 20) {
			error_log("ERROR: TEVEEL ATTEMPTS IN V3");
			echo json_encode(array(
				"success" => false,
				"error" => true
			));
			mysqli_close($db);
			die();
		}

		$userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
		putenv("MAGISTER_ENCRYPTION_KEY=" . md5($_SERVER['REMOTE_ADDR'] . $userAgent . time()));
		require 'lib/vendor/autoload.php';

		$auth = new Magister\Magister('hageveld', $_GET['username'], $_GET['password']);
		$persoon = json_decode(Magister\Models\User::get()->toJson() , true);
		$geboortedatum = $persoon[0]['Persoon']['Geboortedatum'];
		$naam = $persoon[0]['Persoon']['OfficieleVoornamen'] . " ";
		if ($persoon[0]['Persoon']['OfficieleTussenvoegsels'] != "") {
			$naam.= $persoon[0]['Persoon']['OfficieleTussenvoegsels'] . " ";
		}

		$naam.= $persoon[0]['Persoon']['OfficieleAchternaam'];
		if (trim($naam) != "") {
			if (mysqli_num_rows(mysqli_query($db, "SELECT * FROM mrstemmen WHERE Identiteit='" . sha1($naam . $geboortedatum) . "'")) == 0) {
				$_SESSION['auth'] = array(
					"success" => true,
					"error" => false,
					"gestemd" => false,
					"username" => $_GET['username'],
					"volledigenaam" => $naam,
					"geboortedatum" => $geboortedatum
				);
				echo json_encode(array(
					"success" => true,
					"error" => false,
					"gestemd" => false
				));
			}
			else {
				echo json_encode(array(
					"success" => false,
					"error" => false,
					"gestemd" => true
				));
				session_destroy();
			}
		}
		else {
			echo json_encode(array(
				"success" => false,
				"error" => false
			));
		}
	}
	else {
		echo json_encode(array(
			"success" => false,
			"error" => false
		));
	}

	mysqli_close($db);
}

catch(Exception $e) {
	echo json_encode(array(
		"success" => false,
		"error" => true
	));
}
