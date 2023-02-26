<?php
  global $db;

  $cities = $_REQUEST['cities'];
  $gender = $_REQUEST['gender'];
  $pilc_id = $_REQUEST['pilc_id'];

  if (is_array($cities) && count($cities)) {

    if ($gender) $sqlmore1 = " AND pil_gender = '$gender'";
    if ($pilc_id > 0) $sqlmore2 = " AND pil_pilc_id = $pilc_id";

    $countpils = $db->query("SELECT COUNT(pil_id) FROM pils WHERE pil_active = 1 AND pil_city_id IN (".implode(',', $cities).") AND pil_bus_id = 0 $sqlmore1 $sqlmore2")->fetchColumn();
    if ($countpils > 0) echo '<b>'.$countpils.'</b>';
    else echo '0';

  } else {

    echo '0';

  }

?>
