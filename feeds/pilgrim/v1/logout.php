<?
	require '_includes/init.php';
	newlog($_SERVER['REQUEST_URI'], $_GET, $_POST, $_FILES);
	$lang = checkLang();

	$data = $_POST;
	$expectedParams[]['string'] = 'uuid';
	$expectedParams[]['integer'] = 'platform';

	$requiredParams[]['string'] = 'uuid';
	$requiredParams[]['integer'] = 'platform';

	if (checkParams($data, $expectedParams) && missingValues($data, $requiredParams)) {

		try {

			// update devices, user logged out
			$sql = $db->prepare("UPDATE devices SET
				device_user_id = 0,
				device_user_type = 0,
				device_lastupdated = :lastupdated
				WHERE device_uuid = :uuid AND device_platform = :platform
			");

			$sql->bindValue("platform", $data['platform']);
			$sql->bindValue("uuid", $data['uuid']);
			$sql->bindValue("lastupdated", time());
			$sql->execute();

			$items['success'] = true;
			if ($lang == 'ar') $items['message'] = 'تم تحديث بيانات الجهاز بنجاح';
			else $items['message'] = 'Device updated successfully';

		} catch (PDOException $e) {

			headerBadRequest();
			$items['message'] = SQLError($e);

		}

	}

	outputJSON($items);

?>
