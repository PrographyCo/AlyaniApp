<?php
require '../../../init.php';
require '../../../db.php';
if (!is_numeric($_GET['id'])) die();
$id = $_GET['id'];

$pilinfo = $db->query("SELECT p.*, c.country_title_ar, ci.city_title_ar, cl.pilc_title_ar, cl.pilc_text_id, b.bus_staff_id, b.bus_title FROM pils p
  LEFT OUTER JOIN countries c ON p.pil_country_id = c.country_id
  LEFT OUTER JOIN cities ci ON p.pil_city_id = ci.city_id
  LEFT OUTER JOIN pils_classes cl ON p.pil_pilc_id = cl.pilc_id
  LEFT OUTER JOIN buses b ON p.pil_bus_id = b.bus_id
  WHERE p.pil_id = $id")->fetch(PDO::FETCH_ASSOC);

$campno = $db->query("SELECT pilc_text_text FROM pils_classes WHERE pilc_id = ".$pilinfo['pil_pilc_id'])->fetchColumn();

if ($pilinfo['pilc_text_id'] == 1) $cardtext = 'مخيم';
else $cardtext = 'عماره';

$pilc_design_id = $db->query("SELECT pilc_design_id FROM pils_classes WHERE pilc_id = ".$pilinfo['pil_pilc_id'])->fetchColumn();
$pilc_phones = $db->query("SELECT pilc_phones FROM pils_classes WHERE pilc_id = ".$pilinfo['pil_pilc_id'])->fetchColumn();

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <style>
        @font-face {
            font-family: ArbFONTS;
            src: url(ArbFONTS-Mohammad-Bold-normal.ttf);
        }
    </style>
  </head>
  <body style="font-family:'ArbFONTS'; position: relative;width: 991px;  height: 1403px; margin: 0 auto; color: #555555;">
      <div class="main" style="background-image: url('<?=$pilc_design_id;?>.png');background-repeat: no-repeat;background-size: 100% 100%;display: block;width: 100%;height: 100%;max-height: 100%;">

    <header style="padding: 50px 0 40px;margin-bottom: 8px;display: block;float: right;width: 100%;">
      <div id="logo" style="display:block;width:100%; float: left;">
        <img src="../../../media/pils_qrcodes/<?=$pilinfo['pil_qrcode'];?>" style="display: block;width:250px;height: 250px;padding-left: 67px;">
      </div>
    </header>
      <div id="pic" style="display: block;float: right;width: 100%; margin-top: 5px">
          <img  src="../../../media/pils/<?=$pilinfo['pil_photo'];?>" style="display: inline-block; float:right;width:383px;margin-right: 60px;height:350px;background:#fff;" />
          <div style="display: inline-block; float:left;width:520px;height:140px;background: #fff;margin-top: 90px;">
              <p style="font-size: 36px;color:#37383a; display: block;width:100%;text-align: right;margin: 0; font-weight: bold;padding: 10px 0;">البرنامج : <?=$pilinfo['pilc_title_ar'];?></p>
              <p style="font-size: 36px;color:#37383a; display: block;width:100%;text-align: right;margin: 0; font-weight: bold;"><?=$cardtext;?> : <span style="; font-family: Verdana"><?=$campno;?></span></p>
          </div>
      </div>
        <div id="details" style="display: block;float: right;width: 100%; text-align: right;padding-top: 84px;">
            <h3 style="direction: rtl;font-size: 42px;color:#37383a;margin:40px 0;padding-right: 45px; font-weight: bold;">اسم الحاج:&nbsp;&nbsp;&nbsp;<span style="font-family: Verdana; font-weight:bold"><?=$pilinfo['pil_name'];?></span></h3>
            <h3 style="direction: rtl;font-size: 42px;color:#37383a;margin:40px 0;padding-right: 45px; ">رقم الهوية:&nbsp;&nbsp;&nbsp;<span style="font-family: Verdana"><?=$pilinfo['pil_nationalid'];?></span></h3>
            <h3 style="direction: rtl;font-size: 42px;color:#37383a;margin:40px 0;padding-right: 45px; ">رقم الحاج:&nbsp;&nbsp;&nbsp;<span style="font-family: Verdana"><?=$pilinfo['pil_code'];?></span></h3>


            <?
            $accomoinfo = $db->query("SELECT * FROM pils_accomo WHERE pil_code = '".$pilinfo['pil_code']."'")->fetch(PDO::FETCH_ASSOC);
            if ($accomoinfo) {

              if ($accomoinfo['pil_accomo_type'] == 1) {

                // Suites
                $suite_title = $db->query("SELECT suite_title FROM suites WHERE suite_id = ".$accomoinfo['suite_id'])->fetchColumn();
                $hall_title = $db->query("SELECT hall_title FROM suites_halls WHERE hall_id = ".$accomoinfo['hall_id'])->fetchColumn();

                if ($accomoinfo['extratype_text']) {
                  $paddingwidth = '30%';
                  if ($accomoinfo['extratype_id'] == 1) echo '<p style="direction: rtl;font-size: 36px;color:#37383a;display: inline-block;width: '.$paddingwidth.';font-weight: bold;">كرسي:&nbsp;&nbsp;&nbsp;<span style="font-size:36px; font-weight:bolder; font-family: Verdana">'.$accomoinfo['extratype_text'].'</span></p>';
                  elseif ($accomoinfo['extratype_id'] == 2) echo '<p style="direction: rtl;font-size: 36px;color:#37383a;display: inline-block;width: '.$paddingwidth.';font-weight: bold;">مقعد:&nbsp;&nbsp;&nbsp;<span style="font-size:36px; font-weight:bolder; font-family: Verdana">'.$accomoinfo['extratype_text'].'</span></p>';
                  elseif ($accomoinfo['extratype_id'] == 3) echo '<p style="direction: rtl;font-size: 36px;color:#37383a;display: inline-block;width: '.$paddingwidth.';font-weight: bold;">سرير:&nbsp;&nbsp;&nbsp;<span style="font-size:36px; font-weight:bolder; font-family: Verdana">'.$accomoinfo['extratype_text'].'</span></p>';
                } else $paddingwidth = '45%';
                echo '<p style="direction: rtl;font-size: 36px;color:#37383a;display: inline-block;width: '.$paddingwidth.';font-weight: bold;">الصالة:&nbsp;&nbsp;&nbsp;<span style="font-size:36px; font-weight:bolder; font-family: Verdana">'.$hall_title.'</span></p>
                <p style="direction: rtl;font-size: 36px;color:#37383a;display: inline-block;width: '.$paddingwidth.';font-weight: bold;margin:40px 0;padding-right: 45px;">رقم الجناح:&nbsp;&nbsp;&nbsp;<span style="font-size:36px; font-weight:bolder; font-family: Verdana">'.$suite_title.'</span></p>
                ';

              } elseif ($accomoinfo['pil_accomo_type'] == 2) {

                // building
                $bld_title = $db->query("SELECT bld_title FROM buildings WHERE bld_id = ".$accomoinfo['bld_id'])->fetchColumn();
                $floor_title = $db->query("SELECT floor_title FROM buildings_floors WHERE floor_id = ".$accomoinfo['floor_id'])->fetchColumn();
                $room_title = $db->query("SELECT room_title FROM buildings_rooms WHERE room_id = ".$accomoinfo['room_id'])->fetchColumn();

                echo '<p style="direction: rtl;font-size: 36px;color:#37383a;display: inline-block;width: 30%;font-weight: bold;">الغرفة:&nbsp;&nbsp;&nbsp;<span style="font-size:36px; font-weight:bolder; font-family: Verdana">'.$room_title.'</span></p>
                <p style="direction: rtl;font-size: 36px;color:#37383a;display: inline-block;width: 30%;font-weight: bold;margin: 0;">الدور:&nbsp;&nbsp;&nbsp;<span style="font-size:36px; font-weight:bolder; font-family: Verdana">'.$floor_title.'</span></p>
                <p style="direction: rtl;font-size: 36px;color:#37383a;display: inline-block;width: 30%;font-weight: bold;margin:40px 0;padding-right: 45px;">المبنى:&nbsp;&nbsp;&nbsp;<span style="font-size:36px; font-weight:bolder; font-family: Verdana">'.$bld_title.'</span></p>

                ';

              } elseif ($accomoinfo['pil_accomo_type'] == 3) {

                // tent
                $tent_title = $db->query("SELECT tent_title FROM tents WHERE tent_id = ".$accomoinfo['tent_id'])->fetchColumn();
                $tent_type = $db->query("SELECT tent_type FROM tents WHERE tent_id = ".$accomoinfo['tent_id'])->fetchColumn();

                if ($tent_type == 1) $tent_text = LBL_TentType1;
                elseif ($tent_type == 2) $tent_text = LBL_TentType2;
                echo '<p style="direction: rtl;font-size: 36px;color:#37383a;display: inline-block;width: 90%;font-weight: bold; margin:40px 0;padding-right: 45px;">'.$tent_text.':&nbsp;&nbsp;&nbsp;<span style="font-size:36px; font-weight:bolder; font-family: Verdana">'.$tent_title.'</span></p>';

              }

            }
            ?>

        </div>
        <div id="tel" style="position:absolute; left:0; right:0; display: block;width: 100%; text-align: center;bottom: 20px;">
            <span style="color: #fff;font-weight: bold;font-size: 46px; -webkit-text-stroke-width: 2px; -webkit-text-stroke-color: black;"><?=$pilc_phones;?></span>
        </div>
      </div>
  </body>
</html>
