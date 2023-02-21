<?

  require_once '../init.php';
  require_once '../db.php';
  require_once '../functions.php';

  $city_id = $_POST['city_id'];

  if (is_numeric($city_id) && $city_id > 0) {

    $sqldel = $db->query("UPDATE pils SET pil_bus_id = 0 WHERE pil_city_id = $city_id");
    echo LBL_AccomoRemoved;

  }

?>
