<?php
  
  global $db;

  $pil_id = $_POST['pil_id'];
  $type = $_POST['type'] ?? 'pil';

  if (is_numeric($pil_id) && $pil_id > 0) {

    if ($type === 'pil')
      $pil_code = $db->query("SELECT pil_code FROM pils WHERE pil_id = $pil_id")->fetchColumn();
    else
      $pil_code = $pil_id;
    
    if ($pil_code) {

      $sqldel = $db->query("DELETE FROM pils_accomo WHERE pil_code = '$pil_code' AND type = '$type'");
      
    }
  }

?>
