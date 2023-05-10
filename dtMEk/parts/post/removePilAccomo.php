<?php
  
  global $db;

  $pil_id = $_POST['pil_id'];
  $type = $_POST['type'] ?? 'pil';
  
  $arafa = $_POST['arafa'] ?? false;
  
  if (is_numeric($pil_id) && $pil_id > 0) {

    if ($type == 'pil')
      $pil_code = $db->query("SELECT pil_code FROM pils WHERE pil_id = $pil_id")->fetchColumn();
    else
      $pil_code = $pil_id;
    
    if ($pil_code) {

      if ($arafa) {
        $sqldel = $db->query("UPDATE pils_accomo SET halls_id=0,seats=0 WHERE pil_code = '$pil_code' AND type = '$type'");
      } else {
        $sqldel = $db->query("UPDATE pils_accomo SET pil_accomo_type=0,suite_id=0,suite_id=0,stuff_id=0,bld_id=0,floor_id=0,floor_id=0,tent_id=0 WHERE pil_code = '$pil_code' AND type = '$type'");
      }
      
    }
  }

?>
