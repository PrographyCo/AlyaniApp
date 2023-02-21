<?

	header("Accept: */*");
	header("Accept-Encoding: gzip, deflate");
	header("User-Agent: runscope/0.1");

	require '_includes/init.php';
	newlog($_SERVER['REQUEST_URI'], $_GET, $_POST, $_FILES);
	$staffinfo = checkToken();
	$lang = checkLang();

	$data = $_POST;

	$expectedParams[]['integer'] = 'from';
	$expectedParams[]['integer'] = 'maxresults';

	if (checkParams($data, $expectedParams)) {

		try {

			if ($staffinfo['staff_id'] && $staffinfo['staff_id'] > 0) {

				$items['success'] = true;
				$items['message'] = '';

				// Staff Pilgrims
				$items['data']['pilgrims'] = getStaffPilgrims($data['staff_id'], $data['search'], $data['city_id'], $staffinfo, $lang, $data['from'], $data['maxresults']);


			} else {

				$items['success'] = false;
				$items['message'] = 'Access denied';

			}



		} catch (PDOException $e) {

			headerBadRequest();
			$items['success'] = false;
			$items['message'] = SQLError($e);
		}

	}

	outputJSON($items);

?>
