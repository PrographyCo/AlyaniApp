<?php
  global $db;

  $cities = $_POST['cities'] ?: $_GET['cities'];

  $sms_message = $db->query("SELECT s_value FROM settings WHERE s_id = 15")->fetchColumn();

  $sentSMSs = 0;

  if (is_array($cities) && count($cities) && $sms_message) {

    $pils = $db->query("SELECT pil_id, pil_name, pil_bus_id FROM pils WHERE pil_active = 1 AND pil_city_id IN (".implode(',', $cities).") AND pil_bus_id IN (SELECT bus_id FROM buses WHERE bus_active = 1)");
    while ($pilinfo = $pils->fetch(PDO::FETCH_ASSOC)) {

      $message = str_replace("{{name}}", $pilinfo['pil_name'], $sms_message);
      $bus_title = $db->query("SELECT bus_title FROM buses WHERE bus_id = ".$pilinfo['pil_bus_id'])->fetchColumn();

      $to_replace = "( ";
      $to_replace .= "الحافلة رقم:  ";
      $to_replace .= $bus_title;
      $to_replace .= " )";

      $message = str_replace("{{accomo}}", $to_replace, $message);
      $SMS = sendSMSPilGeneral($pilinfo['pil_id'], $message).'<br />';
      if ($SMS == 1) $sentSMSs++;

    }

    echo '<br /><br />';
    if ($sentSMSs > 0) echo LBL_SMSSENT.' <b>'.$sentSMSs.'</b> '.LBL_Message;
    else echo LBL_NoPilsAccomodatedBuses;
    echo '<br /><br /><button class="btn btn-success" onclick="resend(); return false;">'.LBL_ResendSMSs.'</button>';

  } else {

    echo '<br /><br />';
    echo LBL_NoPilsAccomodatedBuses;
    echo '<br /><br /><button class="btn btn-success" onclick="resend(); return false;">'.LBL_ResendSMSs.'</button>';

  }
