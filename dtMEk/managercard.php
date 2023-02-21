<?
	require 'db.php';

	if (!is_numeric($_GET['id'])) die();
	$id = $_GET['id'];

	//error_reporting(E_ALL);
	//ini_set('display_errors', 1);

	$cardurl = 'media/pils_cards/MANAGER_'.$id.'_CARD.pdf';
	$chosen_id = $db->query("SELECT design_id FROM chosen_designs WHERE design_type = 2")->fetchColumn();
	//if (!is_file($cardurl)) {

		$command = "wkhtmltopdf -T 0 -B 0 -L 0 -R 0 --page-size A4 --dpi 300 http://alyaniapp.com/v2/dtMEk/pil_cards_designs/managers/".$chosen_id."/index.php?id=$id /home/alyaniapp/public_html/v2/dtMEk/media/pils_cards/MANAGER_".$id."_CARD.pdf";
		$result = exec($command);

	//}

	$content = file_get_contents($cardurl);

	header('Content-Type: application/pdf');
	header('Content-Length: ' . strlen($content));
	header('Content-Disposition: inline; filename="MANAGER_'.$id.'_CARD.pdf"');
	header('Cache-Control: private, max-age=0, must-revalidate');
	header('Pragma: public');
	ini_set('zlib.output_compression','0');

	die($content);

?>
