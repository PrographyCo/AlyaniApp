<?

  require '../init.php';
  require_once '../db.php';
  require_once '../functions.php';

  $cities = $_POST['cities'] ?: $_GET['cities'];

  if (is_array($cities) && sizeof($cities)) {

    $countpils = $db->query("SELECT COUNT(pil_id) FROM pils WHERE pil_active = 1 AND pil_city_id IN (".implode(',', $cities).") AND pil_code IN (SELECT pil_code FROM pils_accomo)")->fetchColumn();
    if ($countpils > 0) echo '<b>'.$countpils.'</b>';
    else echo '0';

  } else {

    echo '0';

  }

?>
