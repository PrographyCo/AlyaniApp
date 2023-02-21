<?
	require '_includes/init.php';
	newlog($_SERVER['REQUEST_URI'], $_GET, $_POST, $_FILES);
	$pilinfo = checkToken(1);
	$lang = checkLang();
	$photo = $_FILES['photo'];

	if ($photo) {

		if ($_FILES["photo"]["tmp_name"]) {

			$ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
			$newname = GUID();

			
                $file = $_FILES['photo']['name'];
                $filesize = $_FILES['photo']['size'];
                $filetmp = $_FILES['photo']['tmp_name'];
                
                $allowedExts = array("pdf","docx","doc","odt","pptx","ppt","odp","jpeg","jpg","png");
                $exp = explode('.' , $file);
                $imageExtension = strtolower(end($exp));
                
                $Mimage = rand(0,100000) . '_' .$file ;
               
			if (move_uploaded_file($filetmp, "../../../../v3//dtMEk/media/pils/". $Mimage)) {

				$sqlupd = $db->query("UPDATE pils SET pil_photo ='$Mimage' WHERE pil_id = ".$pilinfo['pil_id']);
				$items['success'] = true;
				if ($lang == 'ar') $items['message'] = 'تم تحديث الصورة بنجاح';
				else $items['message'] = 'Profile photo updated successfully';
				$items['data']['pilgrim'] = getPilInfo($pilinfo['pil_id']);

			} else {

				$items['success'] = false;
				if ($lang == 'ar') $items['message'] = 'لم يتم تحديث الصورة';
				else $items['message'] = 'Profile photo not updated';

			}

		} else {

			$items['success'] = false;
			$items['message'] = 'Missing Photo';

		}

	} else {

		$items['success'] = false;
		$items['message'] = 'Missing Photo';

	}

	outputJSON($items);
?>
