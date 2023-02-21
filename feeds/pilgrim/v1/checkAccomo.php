<?
	require '_includes/init.php';
	newlog($_SERVER['REQUEST_URI'], $_GET, $_POST, $_FILES);
	$pilinfo = checkToken(1);
	$lang = checkLang();

	$data = $_POST;

	try {

		if ($pilinfo) {

			$pil_code = $db->query("SELECT pil_code FROM pils WHERE pil_id = ".$pilinfo['pil_id'])->fetchColumn();

			if ($pil_code) {

				$items['success'] = true;
				$items['message'] = '';
				$items['data']['accomodation'] = getPilAccomo($pil_code, $lang);

			} else {

				$items['success'] = false;
				$items['message'] = 'No code found';

			}

		} else {

			$items['success'] = false;
			if ($lang == 'ar') $items['message'] = 'Pilgrim does not exist';
			else $items['message'] = 'الحاج غير موجود';

		}


	} catch (PDOException $e) {

		headerBadRequest();
		$items['success'] = false;
		$items['message'] = SQLError($e);
	}

	outputJSON($items);

?>
