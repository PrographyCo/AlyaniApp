<?
	require '_includes/init.php';
	newlog($_SERVER['REQUEST_URI'], $_GET, $_POST, $_FILES);
	$pilinfo = checkToken(3);
	$lang = checkLang();

	$data = $_POST;

	$expectedParams[]['integer'] = 'type';
	$expectedParams[]['array'] = 'cities';
	$expectedParams[]['string'] = 'message';

	$requiredParams[]['integer'] = 'type';
	$requiredParams[]['string'] = 'message';

	if (checkParams($data, $expectedParams) && missingValues($data, $requiredParams)) {

		try {

			if ($data['type'] == 1) {

				// send to pilgrims
				if (is_array($data['cities']) && sizeof($data['cities']) > 0) $sqlmore = " AND pil_city_id IN (".implode(',', $data['cities']).")";
				$sql = $db->query("SELECT pil_id FROM pils WHERE pil_active = 1 $sqlmore");
				while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {

					sendSMSPilGeneral($row['pil_id'], $data['message']);

				}

			} elseif ($data['type'] == 2) {

				// send to supervisors
				if (is_array($data['cities']) && sizeof($data['cities']) > 0) $sqlmore = " AND staff_id IN (SELECT staff_id FROM cities_staff WHERE city_id IN (".implode(',', $data['cities'])."))";
				$sql = $db->query("SELECT staff_id FROM staff WHERE staff_type = 2 AND staff_active = 1 $sqlmore");
				while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {

					sendSMSStaffGeneral($row['staff_id'], $data['message']);

				}

			}

			$items['success'] = true;
			if ($lang == 'ar') $items['message'] = 'SMSs sent successfully';
			else $items['message'] = 'تم إرسال الرسائل القصيرة بنجاح';

		} catch (PDOException $e) {

			headerBadRequest();
			$items['success'] = false;
			$items['message'] = SQLError($e);
		}

	}
	outputJSON($items);

?>
