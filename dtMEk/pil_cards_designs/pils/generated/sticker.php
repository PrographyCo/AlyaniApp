<?
require '../../../init.php';
require '../../../db.php';

?><!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <style>
        @font-face {
            font-family: ArbFONTS;
            src: url(ArbFONTS-Mohammad-Bold-normal.ttf);
        }
        .font-26{
            font-size: 10px !important;
        }
    </style>
  </head>
  <body style="font-family:ArbFONTS; position: relative; margin: 0 auto; color: #555555; box-sizing: border-box">

    <?

    if (is_numeric($_GET['city_id']) && $_GET['city_id'] > 0) $sqlmore1 = " AND pil_city_id = ".$_GET['city_id'];
    if ($_GET['gender'] == 'm' || $_GET['gender'] == 'f') $sqlmore2 = " AND pil_gender = '".$_GET['gender']."'";
    if (is_numeric($_GET['pilc_id']) && $_GET['pilc_id'] > 0) $sqlmore3 = " AND pil_pilc_id = ".$_GET['pilc_id'];
    if ($_GET['code']) $sqlmore4 = " AND pil_code LIKE '%".$_GET['code']."%'";
    if ($_GET['resno']) $sqlmore5 = " AND pil_reservation_number LIKE '%".$_GET['resno']."%'";
    if (is_numeric($_GET['accomo']) && $_GET['accomo'] == 1) $sqlmore6 = " AND pil_code IN (SELECT pil_code FROM pils_accomo)";
    if (is_numeric($_GET['accomo']) && $_GET['accomo'] == 2) $sqlmore6 = " AND pil_code NOT IN (SELECT pil_code FROM pils_accomo)";

    $sql = $db->query("SELECT p.*, cl.pilc_title_ar, cl.pilc_text_id, c.country_title_ar, ci.city_title_ar FROM pils p
      LEFT OUTER JOIN countries c ON p.pil_country_id = c.country_id
      LEFT OUTER JOIN cities ci ON p.pil_city_id = ci.city_id
      LEFT OUTER JOIN pils_classes cl ON p.pil_pilc_id = cl.pilc_id
      WHERE 1 $sqlmore1 $sqlmore2 $sqlmore3 $sqlmore4 $sqlmore5 $sqlmore6
      ORDER BY pil_name");
    while ($pilinfo = $sql->fetch()) {

    if ($pilinfo['pilc_text_id'] == 1) $cardtext = 'مخيم';
    else $cardtext = 'عماره';

    $pilc_design_id = $db->query("SELECT pilc_design_id FROM pils_classes WHERE pilc_id = ".$pilinfo['pil_pilc_id'])->fetchColumn();
    $pilc_phones = $db->query("SELECT pilc_phones FROM pils_classes WHERE pilc_id = ".$pilinfo['pil_pilc_id'])->fetchColumn();
    $campno = $db->query("SELECT pilc_text_text FROM pils_classes WHERE pilc_id = ".$pilinfo['pil_pilc_id'])->fetchColumn();

    ?>

    <div style="font-family:ArbFONTS; position: relative; margin: 0 auto; color: #555555;">
      <div class="main" style="background-repeat: no-repeat;background-size: 100% 100%;display: block;width: 100%;height: 100%;max-height: 100%;">

    <!--<header style="padding: 50px 0 40px;margin-bottom: 8px;display: block;float: right;width: 100%;">-->
    <!--  <div id="logo" style="display:block;width:100%; float: left;">-->
    <!--    <img src="../../../media/pils_qrcodes/<?=$pilinfo['pil_qrcode'];?>" style="display: block;width:250px;height: 250px;padding-left: 67px;">-->
    <!--  </div>-->
    <!--</header>-->
   
        <div id="details" style="width: 6in;height: 9.5in;">
                <div style="position: absolute;top: 18%;text-align: center;right: 0;left: 0;width: 100%;">
                      <h3 style="direction: rtl;font-size:1em;color:#37383a;margin:20px 0 0 0;padding-right: 15px; font-weight: bold;"> 
                          <span style="font-family: Verdana; font-weight:bold"><?=$pilinfo['pil_name'] .' - '.$pilc_phones;?></span></h3>
                                <h3 style="direction: rtl;font-size:1em;color:#37383a;margin: 0;padding-right: 10px; "><span style="; font-family: Verdana"><?=$pilinfo['pil_nationalid'];?></span></h3>


            <?
            $accomoinfo = $db->query("SELECT * FROM pils_accomo WHERE pil_code = '".$pilinfo['pil_code']."'")->fetch(PDO::FETCH_ASSOC);
            if ($accomoinfo) {

              if ($accomoinfo['suite_id'] != 0) {

                // Suites
                $suite_title = $db->query("SELECT suite_title FROM suites WHERE suite_id = ".$accomoinfo['suite_id'])->fetchColumn();
                $hall_title = $db->query("SELECT hall_title FROM suites_halls WHERE hall_id = ".$accomoinfo['hall_id'])->fetchColumn();

                if ($accomoinfo['extratype_text']) {
                  $paddingwidth = '30%';
                  if ($accomoinfo['extratype_id'] == 1) echo '<p style="margin:0 18px;direction: rtl;font-size: 12px;color:#37383a;display: inline-block;font-weight: bold;">كرسي:&nbsp;&nbsp;&nbsp;<span style="font-size:16px; font-weight:bolder; font-family: Verdana">'.$accomoinfo['extratype_text'].'</span></p>';
                  elseif ($accomoinfo['extratype_id'] == 2) echo '<p style="margin:0 18px;direction: rtl;font-size: 12px;color:#37383a;display: inline-block;font-weight: bold;">مقعد:&nbsp;&nbsp;&nbsp;<span style="font-size:16px; font-weight:bolder; font-family: Verdana">'.$accomoinfo['extratype_text'].'</span></p>';
                  elseif ($accomoinfo['extratype_id'] == 3) echo '<p style="margin:0 18px;direction: rtl;font-size: 12px;color:#37383a;display: inline-block;font-weight: bold;">سرير:&nbsp;&nbsp;&nbsp;<span style="font-size:16px; font-weight:bolder; font-family: Verdana">'.$accomoinfo['extratype_text'].'</span></p>';
                } else $paddingwidth = '45%';
                echo '<p style="margin:0 18px;direction: rtl;font-size: 12px;color:#37383a;display: inline-block;font-weight: bold;">الصالة:&nbsp;&nbsp;&nbsp;<span style="font-size:16px; font-weight:bolder; font-family: Verdana">'.$hall_title.'</span></p>
                <p style="direction: rtl;font-size: 12px;color:#37383a;display: inline-block;font-weight: bold;margin:0 18px;padding-right: 12px;"> الجناح:&nbsp;&nbsp;&nbsp;<span style="font-size:16px; font-weight:bolder; font-family: Verdana">'.$suite_title.'</span></p>
                ';

              } 
              if ($accomoinfo['bld_id'] != 0) {

                // building
                $bld_title = $db->query("SELECT bld_title FROM buildings WHERE bld_id = ".$accomoinfo['bld_id'])->fetchColumn();
                $floor_title = $db->query("SELECT floor_title FROM buildings_floors WHERE floor_id = ".$accomoinfo['floor_id'])->fetchColumn();
                $room_title = $db->query("SELECT room_title FROM buildings_rooms WHERE room_id = ".$accomoinfo['room_id'])->fetchColumn();

                echo '<p style="direction: rtl;font-size: 36px;color:#37383a;display: inline-block;font-weight: bold;">الغرفة:&nbsp;&nbsp;&nbsp;<span style="font-size:16px; font-weight:bolder; font-family: Verdana">'.$room_title.'</span></p>
                <p style="direction: rtl;font-size: 26px;color:#37383a;display: inline-block;font-weight: bold;margin: 0 18px;">الدور:&nbsp;&nbsp;&nbsp;<span style="font-size:16px; font-weight:bolder; font-family: Verdana">'.$floor_title.'</span></p>
                <p style="direction: rtl;font-size: 26px;color:#37383a;display: inline-block;font-weight: bold;margin: 0 18px;padding-right: 12px;">المبنى:&nbsp;&nbsp;&nbsp;<span style="font-size:16px; font-weight:bolder; font-family: Verdana">'.$bld_title.'</span></p>';

              } 
              if ($accomoinfo['tent_id'] != 0) {

                // tent
                $tent_title = $db->query("SELECT tent_title FROM tents WHERE tent_id = ".$accomoinfo['tent_id'])->fetchColumn();
                $tent_type = $db->query("SELECT tent_type FROM tents WHERE tent_id = ".$accomoinfo['tent_id'])->fetchColumn();

                if ($tent_type == 1) $tent_text = LBL_TentType1;
                elseif ($tent_type == 2) $tent_text = LBL_TentType2;

                echo '<p style="direction: rtl;font-size: 36px;color:#37383a;display: inline-block;font-weight: bold; margin:0 18px;padding-right: 45px;">'.$tent_text.':&nbsp;&nbsp;&nbsp;<span style="font-size:16px; font-weight:bolder; font-family: Verdana">'.$tent_title.'</span></p>';

              }
            
              

            }
            ?>
                </div>

        </div>
       <hr>
      </div>
    </div>
    <p style="page-break-after: always;"></p>
    <p style="page-break-before: always;"></p>
   
    <? } ?>
  </body>
</html>
