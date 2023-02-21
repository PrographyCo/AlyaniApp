<?
	require '_includes/init.php';
	newlog($_SERVER['REQUEST_URI'], $_GET, $_POST, $_FILES);
	$userinfo = checkToken();
	$lang = checkLang();

	outputJSON($userinfo);

?>
