<?php
  global $db;


  $cities = $_POST['cities'] ?: $_GET['cities'];

  $sms_message = $db->query("SELECT s_value FROM settings WHERE s_id = 14")->fetchColumn();

  $sentSMSs = 0;

  if (is_array($cities) && count($cities) && $sms_message) {

    $pils = $db->query("SELECT pil_id, pil_name, pil_code, pil_phone FROM pils WHERE pil_active = 1 AND pil_city_id IN (".implode(',', $cities).") AND pil_code IN (SELECT pil_code FROM pils_accomo)");
    while ($pilinfo = $pils->fetch(PDO::FETCH_ASSOC)) {

      $message = str_replace("{{name}}", $pilinfo['pil_name'], $sms_message);

      $accomoinfo = $db->query("SELECT * FROM pils_accomo WHERE pil_code = '".$pilinfo['pil_code']."'")->fetch(PDO::FETCH_ASSOC);
      if ($accomoinfo) {

        if ($accomoinfo['pil_accomo_type'] == 1) {

          // Suites
          $suite_title = $db->query("SELECT suite_title FROM suites WHERE suite_id = ".$accomoinfo['suite_id'])->fetchColumn();
          $hall_title = $db->query("SELECT hall_title FROM suites_halls WHERE hall_id = ".$accomoinfo['hall_id'])->fetchColumn();

          $to_replace = "( ";
          $to_replace .= "الجناح:  ";
          $to_replace .= $suite_title;
          $to_replace .= "، ";
          $to_replace .= "الصالة: ";
          $to_replace .= $hall_title;

          if ($accomoinfo['extratype_text']) {

            if ($accomoinfo['extratype_id'] == 1) $to_replace .= '، كرسي: '.$accomoinfo['extratype_text'];
            elseif ($accomoinfo['extratype_id'] == 2) $to_replace .= '، مقعد: '.$accomoinfo['extratype_text'];
            elseif ($accomoinfo['extratype_id'] == 3) $to_replace .= '، سرير: '.$accomoinfo['extratype_text'];

          }

          $to_replace .= " )";

        } elseif ($accomoinfo['pil_accomo_type'] == 2) {

          // building
          $bld_title = $db->query("SELECT bld_title FROM buildings WHERE bld_id = ".$accomoinfo['bld_id'])->fetchColumn();
          $floor_title = $db->query("SELECT floor_title FROM buildings_floors WHERE floor_id = ".$accomoinfo['floor_id'])->fetchColumn();
          $room_title = $db->query("SELECT room_title FROM buildings_rooms WHERE room_id = ".$accomoinfo['room_id'])->fetchColumn();

          $to_replace = "( ";
          $to_replace .= "المبنى:  ";
          $to_replace .= $bld_title;
          $to_replace .= "، ";
          $to_replace .= "الدور:  ";
          $to_replace .= $floor_title;
          $to_replace .= "، ";
          $to_replace .= "الغرفة:  ";
          $to_replace .= $room_title;
          $to_replace .= " )";

        } elseif ($accomoinfo['pil_accomo_type'] == 3) {

          // tent
          $tent_title = $db->query("SELECT tent_title FROM tents WHERE tent_id = ".$accomoinfo['tent_id'])->fetchColumn();

          $to_replace = "( ";
          $to_replace .= "الخيمة:  ";
          $to_replace .= $tent_title;
          $to_replace .= " )";

        }

      }

      $message = str_replace("{{accomo}}", $to_replace, $message);
      $SMS = sendSMSPilGeneral($pilinfo['pil_id'], $message).'<br />';
      if ($SMS == 1) $sentSMSs++;

    }

    echo '<br /><br />';
    if ($sentSMSs > 0) echo LBL_SMSSENT.' <b>'.$sentSMSs.'</b> '.LBL_Message;
    else echo LBL_NoPilsAccomodated;
    echo '<br /><br /><button class="btn btn-success" onclick="resend(); return false;">'.LBL_ResendSMSs.'</button>';

  } else {

    echo '<br /><br />';
    echo LBL_NoPilsAccomodated;
    echo '<br /><br /><button class="btn btn-success" onclick="resend(); return false;">'.LBL_ResendSMSs.'</button>';

  }
