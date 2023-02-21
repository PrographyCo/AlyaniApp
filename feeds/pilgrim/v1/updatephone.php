<?
	require '_includes/init.php';
	newlog($_SERVER['REQUEST_URI'], $_GET, $_POST, $_FILES);
	$pilinfo = checkToken(1);
	$lang = checkLang();

	$data = $_POST;

	$expectedParams[]['string'] = 'phone';

	$requiredParams[]['string'] = 'phone';

	if (checkParams($data, $expectedParams) && missingValues($data, $requiredParams)) {

		try {

			// insert newphone
			$sql = $db->prepare("INSERT INTO pils_newphones VALUES (:pil_id, :newphone) ON DUPLICATE KEY UPDATE newphone = :newphone");
			$sql->bindValue("newphone", $data['phone']);
			$sql->bindValue("pil_id", $pilinfo['pil_id']);
			$sql->execute();

			$code = rand(1000,9999);
			$sqlupdcode = $db->query("INSERT INTO pils_codes VALUES (".$pilinfo['pil_id'].", $code) ON DUPLICATE KEY UPDATE code = $code");
			$smsresult = sendSMSPilNewPhone($pilinfo['pil_id'], $code);

			$items['success'] = true;
			if ($lang == 'ar') $items['message'] = 'Phone added, pending verify';
			else $items['message'] = 'تم إضافة رقم الجوال الجديد، في انتظار تأكيده';

		} catch (PDOException $e) {

			headerBadRequest();
			$items['success'] = false;
			$items['message'] = SQLError($e);
		}

	} else {

		$items['success'] = false;
		$items['message'] = 'Missing Photo';

	}

	outputJSON($items);
?>
