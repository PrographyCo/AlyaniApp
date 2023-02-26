<?php
  
  global $db;

  $pil_id = $_POST['pil_id'] ?? $pil_id;

  if (is_numeric($pil_id) && $pil_id > 0) {

    $pil_code = $db->query("SELECT pil_code FROM pils WHERE pil_id = $pil_id")->fetchColumn();
    if ($pil_code) {

      $sqldel = $db->query("DELETE FROM pils_accomo WHERE pil_code = '$pil_code'");
      
    }
  }

?>
