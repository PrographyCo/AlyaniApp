<?

	require '../config.inc.php';
	require '../db.php';

	$p_id = $_POST['p_id'] ? $_POST['p_id'] : $_GET['p_id'];
	$filetitle = $_POST['filetitle'] ? $_POST['filetitle'] : $_GET['filetitle'];

	if (is_numeric($p_id) && $filetitle) {

		// chk if p_id belongs to logged in broker
		$chk = $db->query("SELECT p_id FROM projects WHERE p_id = $p_id AND p_broker_id = ".$_SESSION['userinfo']['user_id'])->fetchColumn();
		if ($chk) {

			// Ok grant access
			if (is_file('../media/gallery/'.$p_id.'/'.$filetitle)) {

				@unlink('../media/gallery/'.$p_id.'/'.$filetitle);

			}

		}


	}

	echo '1';

?>