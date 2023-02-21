<?

  require '../init.php';
  require_once '../db.php';
  require_once '../functions.php';

  $cities = $_POST['cities'] ?: $_GET['cities'];

  if (is_array($cities) && sizeof($cities)) {

    $countpils = $db->query("SELECT COUNT(pil_id) FROM pils WHERE pil_active = 1 AND pil_city_id IN (".implode(',', $cities).") AND pil_bus_id IN (SELECT bus_id FROM buses WHERE bus_active = 1)")->fetchColumn();
    if ($countpils > 0) echo '<b>'.$countpils.'</b>';
    else echo '0';

  } else {

    echo '0';

  }

?>
