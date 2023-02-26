<?php
  
  global $db;

  $city_id = $_POST['city_id'];

  if (is_numeric($city_id) && $city_id > 0) {

    $sqldel = $db->query("DELETE FROM pils_accomo WHERE pil_code IN (SELECT pil_code FROM pils WHERE pil_city_id = $city_id)");
    echo LBL_AccomoRemoved;

  }

?>
