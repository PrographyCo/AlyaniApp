<?php
  
  global $db;
  
  $cities = $_REQUEST['cities'];

  if (is_array($cities) && count($cities)) {

    $countpils = $db->query("SELECT COUNT(pil_id) FROM pils WHERE pil_active = 1 AND pil_city_id IN (".implode(',', $cities).") AND pil_bus_id IN (SELECT bus_id FROM buses WHERE bus_active = 1)")->fetchColumn();
    if ($countpils > 0) echo '<b>'.$countpils.'</b>';
    else echo '0';

  } else {

    echo '0';

  }

?>
