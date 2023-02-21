<?
	require '_includes/init.php';
	newlog($_SERVER['REQUEST_URI'], $_GET, $_POST, $_FILES);
	$lang = checkLang();

	$data = $_POST;
	$expectedParams[]['integer'] = 'type';
	$expectedParams[]['integer'] = 'user_id';
	$expectedParams[]['string'] = 'uuid';
	$expectedParams[]['string'] = 'token';
	$expectedParams[]['integer'] = 'platform';

	$requiredParams[]['integer'] = 'type';
	$requiredParams[]['string'] = 'uuid';
	$requiredParams[]['string'] = 'token';
	$requiredParams[]['integer'] = 'platform';

	if (checkParams($data, $expectedParams) && missingValues($data, $requiredParams)) {

		try {
			// insert / update record based on device platform and uuid
			$sql = $db->prepare("INSERT INTO devices VALUES (
			'',
			:platform,
			:type,
			:user_id,
			:uuid,
			:token,
			:dateadded,
			:lastupdated
			) ON DUPLICATE KEY UPDATE
			device_user_id = :user_id,
			device_user_type = :type,
			device_token = :token,
			device_lastupdated = :lastupdated");

			$sql->bindValue("platform", $data['platform']);
			$sql->bindValue("type", $data['type']);
			$sql->bindValue("user_id", $data['user_id']);
			$sql->bindValue("uuid", $data['uuid']);
			$sql->bindValue("token", $data['token']);
			$sql->bindValue("dateadded", time());
			$sql->bindValue("lastupdated", time());
			$sql->execute();

			$items['success'] = true;
			if ($lang == 'ar') $items['message'] = 'تم إضافة / تحديث بيانات الجهاز بنجاح';
			else $items['message'] = 'Device added / updated successfully';

		} catch (PDOException $e) {

			headerBadRequest();
			$items['message'] = SQLError($e);

		}

	}

	outputJSON($items);

?>
