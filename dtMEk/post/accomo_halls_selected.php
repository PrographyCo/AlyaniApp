<?

  require '../init.php';
  require_once '../db.php';
  require_once '../functions.php';

  $suites = $_POST['suites'] ?: $_GET['suites'];
  $halls = $_POST['halls'] ?: $_GET['halls'];

  $output['availcount'] = AvailableToAccomoSuites($suites, $halls, $gender);

  echo json_encode($output);

?>
