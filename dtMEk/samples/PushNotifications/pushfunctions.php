<?php
//include 'nfc.inc.php';
require_once '../../config/db.php';

function sendpush_android($message, $year) {

	global $db;


	if ($year > 0) $sql = $db->query("SELECT DISTINCT(device_token) FROM devices WHERE device_platform = 2 AND device_user_id IN (SELECT user_id FROM app_users WHERE user_year = $year AND user_id NOT IN (SELECT user_id FROM banned_users) UNION SELECT user_id FROM docs)");

	else $sql = $db->query("SELECT device_token FROM devices WHERE device_platform = 2 AND device_user_id NOT IN (SELECT user_id FROM banned_users)");

	if ($sql->rowCount() > 0) {
		while ($row = $sql->fetch()) {

			$tokens[] = $row['device_token'];
		}
	}

	if (sizeof($tokens) > 0) {


		$path_to_firebase_cm = 'https://fcm.googleapis.com/fcm/send';

		/*
		$fields = array(
            'registration_ids' => $tokens,
            'data' => array('message' => $message, "title" => "Notification from Lorewing", "openKey" => "course_id", "openValue" => 3, "openPage" => "courses_details.html")
        );
		*/
		$fields = array(
            'registration_ids' => $tokens,
            'data' => array('message' => $message, "title" => "Notification from Lorewing", "badge" => 1, "image" => "www/wwwcommon/img/logo.jpg")
        );

        $headers = array(
            'Authorization:key=AIzaSyCazovzi3a0o1ABltuN4wHh3TL8ZhRWN38',
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
		//var_dump($result);


		try { $db->query("INSERT INTO pushlog VALUES ('', ".time().", '".$result."')"); }
		catch(PDOException $e) {}

		curl_close($ch);

	} else {

		//echo 'no tokens found';
	}

	return $sql->rowCount();

}

function sendpush_ios($message, $year) {
	global $db;

	try {


		$ckfile = 'dev.pem';
		$ckurl = 'ssl://gateway.push.apple.com:2195';

		$deviceToken = array();
		if (!is_file($ckfile)) {

			$sendnotiresult = 'No certificate found for ios';

		} else {

			$ctx = stream_context_create();
			stream_context_set_option($ctx, 'ssl', 'local_cert',$ckfile);
			stream_context_set_option($ctx, 'ssl', 'passphrase', "");

			$fp = stream_socket_client($ckurl, $err, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx);
			stream_set_blocking($fp, 0);
			if (!$fp) $finalmsg .= "Failed to connect to Apple servers ... $err $errstr ".PHP_EOL."<br />\n";
			else {

				$apple_expiry = time() + (90 * 24 * 60 * 60);
				$body = array();
				$body['aps'] = array('alert' => $message);
				$body['aps']['sound'] = 'default';
				$body['aps']['badge'] = 1;
				//$body['aps']['openKey'] = 'course_id';
				//$body['aps']['openValue'] = 3;
				//$body['aps']['openPage'] = 'courses_details.html';

				$apple_identifier = 1;
				$payload = json_encode($body);

				if ($year > 0) $sql_nots3 = $db->query("SELECT DISTINCT(device_token) FROM devices WHERE device_platform = 1 AND device_user_id IN (SELECT user_id FROM app_users WHERE user_year = $year AND user_id NOT IN (SELECT user_id FROM banned_users) UNION SELECT user_id FROM docs)");
				else $sql_nots3 = $db->query("SELECT device_token FROM devices WHERE device_platform = 1 AND device_user_id NOT IN (SELECT user_id FROM banned_users)");

				if ($sql_nots3->rowCount() > 0) {

					while ($rown3 = $sql_nots3->fetch()) $deviceToken[] = $rown3['device_token'];

					foreach ($deviceToken as $token) {

						$msg = pack("C", 1) . pack("N", $apple_identifier) . pack("N", $apple_expiry) . pack("n", 32) . pack('H*', str_replace(' ', '', $token)) . pack("n", strlen($payload)) . $payload; //Enhanced Notification
						$result = fwrite($fp, $msg, strlen($msg));

					}
				}
				$sendnotiresult = $sql_nots3->rowCount();
				fclose($fp);

			}

		}
	} catch(PDOException $e) {

		$sendnotiresult = 'ERROR '.$e->getMessage();
	}

	return $sendnotiresult;
}







function sendpush_android_new($message, $year, $openKey, $openValue, $openPage) {

	global $db;

	if ($year > 0) $sql = $db->query("SELECT DISTINCT(device_token) FROM devices WHERE device_platform = 2 AND device_user_id IN (SELECT user_id FROM app_users WHERE user_year = $year AND user_id NOT IN (SELECT user_id FROM banned_users) UNION SELECT user_id FROM docs)");
	else $sql = $db->query("SELECT device_token FROM devices WHERE device_platform = 2 AND device_user_id NOT IN (SELECT user_id FROM banned_users)");


	if ($sql->rowCount() > 0) {
		while ($row = $sql->fetch()) {

			$tokens[] = $row['device_token'];
		}
	}

	if (sizeof($tokens) > 0) {


		$path_to_firebase_cm = 'https://fcm.googleapis.com/fcm/send';


		$fields = array(
            'registration_ids' => $tokens,
            'data' => array('message' => $message, "title" => "Notification from Lorewing", "openKey" => $openKey, "openValue" => $openValue, "openPage" => $openPage, "badge" => 1, "image" => "www/wwwcommon/img/logo.jpg")
        );
		/*
		$fields = array(
            'registration_ids' => $tokens,
            'data' => array('message' => $message, "title" => "Notification from Lorewing")
        );
		*/
        $headers = array(
            'Authorization:key=AIzaSyCazovzi3a0o1ABltuN4wHh3TL8ZhRWN38',
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
		//var_dump($result);


        try { $db->query("INSERT INTO pushlog VALUES ('', ".time().", '".$result."')"); }
		catch(PDOException $e) {}

		curl_close($ch);


	} else {

		//echo 'no tokens found';
	}

	return $sql->rowCount();

}

function sendpush_ios_new($message, $year, $openKey, $openValue, $openPage) {
	global $db;

	try {


		$ckfile = 'dev.pem';
		$ckurl = 'ssl://gateway.push.apple.com:2195';

		$deviceToken = array();
		if (!is_file($ckfile)) {

			$sendnotiresult = 'No certificate found for ios';

		} else {

			$ctx = stream_context_create();
			stream_context_set_option($ctx, 'ssl', 'local_cert',$ckfile);
			stream_context_set_option($ctx, 'ssl', 'passphrase', "");

			$fp = stream_socket_client($ckurl, $err, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx);
			stream_set_blocking($fp, 0);
			if (!$fp) $finalmsg .= "Failed to connect to Apple servers ... $err $errstr ".PHP_EOL."<br />\n";
			else {

				$apple_expiry = time() + (90 * 24 * 60 * 60);
				$body = array();
				$body['aps'] = array('alert' => $message);
				$body['aps']['sound'] = 'default';
				$body['aps']['badge'] = 1;
				$body['aps']['openKey'] = $openKey;
				$body['aps']['openValue'] = $openValue;
				$body['aps']['openPage'] = $openPage;

				$apple_identifier = 1;
				$payload = json_encode($body);

				if ($year > 0) $sql_nots3 = $db->query("SELECT DISTINCT(device_token) FROM devices WHERE device_platform = 1 AND device_user_id IN (SELECT user_id FROM app_users WHERE user_year = $year AND user_id NOT IN (SELECT user_id FROM banned_users) UNION SELECT user_id FROM docs)");
				else $sql_nots3 = $db->query("SELECT device_token FROM devices WHERE device_platform = 1 AND device_user_id NOT IN (SELECT user_id FROM banned_users)");

				if ($sql_nots3->rowCount() > 0) {

					while ($rown3 = $sql_nots3->fetch()) $deviceToken[] = $rown3['device_token'];

					foreach ($deviceToken as $token) {

						$msg = pack("C", 1) . pack("N", $apple_identifier) . pack("N", $apple_expiry) . pack("n", 32) . pack('H*', str_replace(' ', '', $token)) . pack("n", strlen($payload)) . $payload; //Enhanced Notification
						$result = fwrite($fp, $msg, strlen($msg));

					}
				}
				$sendnotiresult = $sql_nots3->rowCount();
				fclose($fp);

			}

		}
	} catch(PDOException $e) {

		$sendnotiresult = 'ERROR '.$e->getMessage();
	}

	return $sendnotiresult;
}
