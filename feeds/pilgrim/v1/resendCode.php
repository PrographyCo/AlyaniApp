<?
	require '_includes/init.php';
	newlog($_SERVER['REQUEST_URI'], $_GET, $_POST, $_FILES);
	$pilinfo = checkToken(1);
	$lang = checkLang();

	$data = $_POST;

	try {

		$code = rand(1000,9999);
		$sqlupdcode = $db->query("INSERT INTO pils_codes VALUES (".$pilinfo['pil_id'].", $code) ON DUPLICATE KEY UPDATE code = $code");

		$smsresult = sendSMSPil($pilinfo['pil_id'], $code);

		if ($smsresult['code'] == '1') {
			$items['success'] = true;
			$items['sentSMS'] = true;
		} else {
			$items['success'] = false;
			$items['sentSMS'] = false;
			$items['SMSErrorCode'] = $smsresult['code'];
		}

	} catch (PDOException $e) {

		headerBadRequest();
		$items['success'] = false;
		$items['message'] = SQLError($e);
	}

	outputJSON($items);

?>
