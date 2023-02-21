<?

  require '../init.php';
  require_once '../db.php';
  require_once '../functions.php';

  $buses = $_POST['buses'] ?: $_GET['buses'];
  $gender = $_POST['gender'] ?: $_GET['gender'];

  $output['availcount'] = AvailableToAccomoBuses($buses, $gender);

  echo json_encode($output);

?>
