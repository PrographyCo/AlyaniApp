<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>AlyaniApp Pilgrim Today Logs</title>
<style>
* {
	margin:0;
	padding:0px
}
body{
	font-family: "Verdana";
	font-size:11px;
}
</style>
</head>
<body>
	<?
	$myfile = date("d-m-Y")."-log.txt";
	if (file_exists($myfile)) {

		$data = file_get_contents($myfile);
		echo nl2br($data);

	} else echo 'No logs found for today';
	?>
</body>
</html>
