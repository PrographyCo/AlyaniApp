<?

require_once 'db.php';

function sendPushNotification($platform, $title = null, $message, $type = 0, $pil_id, $staff_id, $sound = null, $saveToNotis, $saveToPushMsgs) {
	global $db;

	if ($platform > 0) $sqlmore1 = " AND device_platform = $platform";

	if ($type == 1 || $type == 2) $sqlmore2 = " AND device_user_type = 1";
	elseif ($type == 3 || $type == 4) $sqlmore2 = " AND device_user_type = 3";
	elseif ($type == 5 || $type == 6) $sqlmore2 = " AND device_user_type = 2";

	if ($type == 2) $sqlmore3 = " AND device_user_id = $pil_id";
	if ($type == 4 || $type == 6) $sqlmore3 = " AND device_user_id = $staff_id";

	$query = "SELECT device_token FROM devices WHERE device_token != '' $sqlmore1 $sqlmore2 $sqlmore3 GROUP BY device_token";
	//echo $query;
	$sql = $db->query($query);
	$key = 'AAAA5bTl9pw:APA91bF5IQv3ZIPcDzqDgZM1tWAbHFWmdktvshSnPgarPNfActwyuAbuyZ0VoVuDccQNMQeEpM4c3CPnUG0v-2K3XkWMHU-M9eaVSUWftcguWJknSOpNIl-EQojr1InOU8t4A7U1Crh_';

	if ($sql->rowCount() > 0) {
		while ($row = $sql->fetch()) {
			$tokens[] = $row['device_token'];
		}
	}

	if (sizeof($tokens) > 0) {

		$path_to_firebase_cm = 'https://fcm.googleapis.com/fcm/send';

		$title = $title ?: 'Notification from Alyani';

		$fields = array(
            'registration_ids' => $tokens,
            'notification' => array("title" => $title, "body" => $message, "sound" => $sound),
						'data' => array("title" => $title, "message" => $message)
        );


        $headers = array(
            'Authorization:key='.$key,
            'Content-Type:application/json'
        );
		$ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $path_to_firebase_cm);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        $result = curl_exec($ch);

				//print_r($result);

		curl_close($ch);


	} else {

		//echo 'no tokens found';
	}

	if ($saveToNotis) addNoti($platform, $type, $pil_id, $staff_id, $title, $message);
	if ($saveToPushMsgs) addPushMsgs($platform, $type, $title, $message, $sound, $pil_id, $staff_id, sizeof($tokens));

	return sizeof($tokens);

}

function addNoti($platform, $type, $pil_id, $staff_id, $title, $message) {

	global $db;

	if ($type == 0 || $type == 1) {

		// ALL or all pilgrims
		// all pilgrims
		if ($platform > 0) $sqlmore = " AND pil_id IN (SELECT device_user_id FROM devices WHERE device_platform = $platform AND device_user_type = 1)";

		$sql = $db->query("SELECT pil_id FROM pils WHERE pil_active = 1 $sqlmore");
		while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {

			// insert data
			$sqlins = $db->prepare("INSERT INTO notifications VALUES (
			'',
			1,
			:pil_id,
			:message_ar,
			:message_en,
			".time()."
			)");
			$sqlins->bindValue("pil_id", $row['pil_id']);
			$sqlins->bindValue("message_ar", $message);
			$sqlins->bindValue("message_en", $message);
			$sqlins->execute();

		}
	}

	if ($type == 0 || $type == 3) {

		if ($platform > 0) $sqlmore = " AND staff_id IN (SELECT device_user_id FROM devices WHERE device_platform = $platform AND device_user_type > 1)";

		// ALL or all managers
		// all managers
		$sql = $db->query("SELECT staff_id FROM staff WHERE staff_type = 1 AND staff_active = 1 $sqlmore");
		while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {

			// insert data
			$sqlins = $db->prepare("INSERT INTO notifications VALUES (
			'',
			2,
			:staff_id,
			:message_ar,
			:message_en,
			".time()."
			)");
			$sqlins->bindValue("staff_id", $row['staff_id']);
			$sqlins->bindValue("message_ar", $message);
			$sqlins->bindValue("message_en", $message);
			$sqlins->execute();

		}
	}

	if ($type == 0 || $type == 5) {

		if ($platform > 0) $sqlmore = " AND staff_id IN (SELECT device_user_id FROM devices WHERE device_platform = $platform AND device_user_type > 1)";

		// ALL or all supervisors
		// all managers
		$sql = $db->query("SELECT staff_id FROM staff WHERE staff_type = 2 AND staff_active = 1 $sqlmore");
		while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {

			// insert data
			$sqlins = $db->prepare("INSERT INTO notifications VALUES (
			'',
			2,
			:staff_id,
			:message_ar,
			:message_en,
			".time()."
			)");
			$sqlins->bindValue("staff_id", $row['staff_id']);
			$sqlins->bindValue("message_ar", $message);
			$sqlins->bindValue("message_en", $message);
			$sqlins->execute();

		}
	}

	if ($type == 2) {

		if ($platform > 0) $sqlmore = " AND pil_id IN (SELECT device_user_id FROM devices WHERE device_platform = $platform AND device_user_type = 1)";

		// specific pilgrim
		$sql = $db->query("SELECT pil_id FROM pils WHERE pil_id = $pil_id AND pil_active = 1 $sqlmore");
		while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {

			// insert data
			$sqlins = $db->prepare("INSERT INTO notifications VALUES (
			'',
			1,
			:pil_id,
			:message_ar,
			:message_en,
			".time()."
			)");
			$sqlins->bindValue("pil_id", $row['pil_id']);
			$sqlins->bindValue("message_ar", $message);
			$sqlins->bindValue("message_en", $message);
			$sqlins->execute();

		}
	}

	if ($type == 4 || $type == 6) {

		if ($platform > 0) $sqlmore = " AND staff_id IN (SELECT device_user_id FROM devices WHERE device_platform = $platform AND device_user_type > 1)";

		// specific staff
		$sql = $db->query("SELECT staff_id FROM staff WHERE staff_id = $staff_id AND staff_active = 1 $sqlmore");
		while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {

			// insert data
			$sqlins = $db->prepare("INSERT INTO notifications VALUES (
			'',
			1,
			:staff_id,
			:message_ar,
			:message_en,
			".time()."
			)");
			$sqlins->bindValue("staff_id", $row['staff_id']);
			$sqlins->bindValue("message_ar", $message);
			$sqlins->bindValue("message_en", $message);
			$sqlins->execute();

		}
	}

	if ($type == 0) {

		// everyone
		// insert data
		$sqlins = $db->prepare("INSERT INTO notifications VALUES (
		'',
		0,
		0,
		:message_ar,
		:message_en,
		".time()."
		)");
		$sqlins->bindValue("message_ar", $message);
		$sqlins->bindValue("message_en", $message);
		$sqlins->execute();

	}

}

function addPushMsgs($platform, $type, $title, $message, $sound, $pil_id, $staff_id, $countdevices) {

	global $db;

	$sql1 = $db->prepare("INSERT INTO pushmsgs VALUES ('', ".time().", :platform, :type, :title, :message, :sound, '".$countdevices."', :pil_id, :staff_id)");
	$sql1->bindValue("platform", $platform);
	$sql1->bindValue("type", $type);
	$sql1->bindValue("title", $title);
	$sql1->bindValue("message", $message);
	$sql1->bindValue("sound", $sound);
	$sql1->bindValue("pil_id", $pil_id);
	$sql1->bindValue("staff_id", $staff_id);
	$sql1->execute();

}

?>
