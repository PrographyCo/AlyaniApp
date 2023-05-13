<?php
    global $db, $session, $lang, $url;
    $sqlmore = [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <style>
        @font-face {
            font-family: ArbFONTS;
            src: url("<?= CP_PATH ?>/assets/fonts/ArbFONTS-Mohammad-Bold-normal.ttf");
        }

        .font-26 {
            font-size: 10px !important;
        }
    </style>
</head>
<body style="font-family:ArbFONTS; position: relative; margin: 0 auto; color: #555555; box-sizing: border-box">

<?php
    if (!empty($_GET['city_id']) && is_numeric($_GET['city_id']) && $_GET['city_id'] > 0) $sqlmore[] = "pil_city_id = " . $_GET['city_id'];
    if (!empty($_GET['gender']) && ($_GET['gender'] == 'm' || $_GET['gender'] == 'f')) $sqlmore[] = "pil_gender = '" . $_GET['gender'] . "'";
    if (!empty($_GET['pilc_id']) && is_numeric($_GET['pilc_id']) && $_GET['pilc_id'] > 0) $sqlmore[] = "pil_pilc_id = " . $_GET['pilc_id'];
    if (!empty($_GET['code'])) $sqlmore[] = "pil_code LIKE '%" . $_GET['code'] . "%'";
    if (!empty($_GET['resno'])) $sqlmore[] = "pil_reservation_number LIKE '%" . $_GET['resno'] . "%'";
    if (!empty($_GET['accomo']) && $_GET['accomo'] == 1) $sqlmore[] = "pil_code IN (SELECT pil_code FROM pils_accomo)";
    if (!empty($_GET['accomo']) && $_GET['accomo'] == 2) $sqlmore[] = "pil_code NOT IN (SELECT pil_code FROM pils_accomo)";

    $sql = $db->query("SELECT p.*, cl.pilc_title_ar, cl.pilc_text_id, c.country_title_ar, ci.city_title_ar FROM pils p
        LEFT OUTER JOIN countries c ON p.pil_country_id = c.country_id
        LEFT OUTER JOIN cities ci ON p.pil_city_id = ci.city_id
        LEFT OUTER JOIN pils_classes cl ON p.pil_pilc_id = cl.pilc_id
        ".((!empty($sqlmore))?"WHERE ".implode(' AND ', $sqlmore):"")."
        ORDER BY pil_name");

    while ($pilinfo = $sql->fetch()) {
        
        if ($pilinfo['pilc_text_id'] == 1) $cardtext = 'مخيم';
        else $cardtext = 'عماره';
        
        $pilc_design_id = $db->query("SELECT pilc_design_id FROM pils_classes WHERE pilc_id = " . $pilinfo['pil_pilc_id'])->fetchColumn();
        $pilc_phones = $db->query("SELECT pilc_phones FROM pils_classes WHERE pilc_id = " . $pilinfo['pil_pilc_id'])->fetchColumn();
        $campno = $db->query("SELECT pilc_text_text FROM pils_classes WHERE pilc_id = " . $pilinfo['pil_pilc_id'])->fetchColumn();
        ?>

        <div style="font-family:ArbFONTS; position: relative; margin: 10px 20px; color: #555555;display: inline-block;">
            <div class="main"
                 style="background-repeat: no-repeat;background-size: 100% 100%;display: inline-block;height: 2.5cm;width: 9.5cm;margin: auto;">

                <div id="details">
                    <div style="text-align: center;">
                        <h3 style="direction: rtl;font-size:1em;color:#37383a;margin:0;padding-right: 15px; font-weight: bold;">
                            <span style="font-family: Verdana; font-weight:bold"><?= $pilinfo['pil_name'] . ' - ' . $pilc_phones ?></span>
                        </h3>
                        <h3 style="direction: rtl;font-size:1em;color:#37383a;margin: 0;padding-right: 10px; "><span
                                    style="; font-family: Verdana"><?= $pilinfo['pil_nationalid'] ?></span></h3>
                        
                        
                        <?php
                            $accomoinfo = $db->query("SELECT * FROM pils_accomo WHERE pil_code = '" . $pilinfo['pil_code'] . "'")->fetch(PDO::FETCH_ASSOC);
                            if ($accomoinfo) {
                                
                                if ($accomoinfo['suite_id'] != 0) {
                                    
                                    // Suites
                                    $suite_title = $db->query("SELECT suite_title FROM suites WHERE suite_id = " . $accomoinfo['suite_id'])->fetchColumn();
                                    $hall_title = $db->query("SELECT hall_title FROM suites_halls WHERE hall_id = " . $accomoinfo['hall_id'])->fetchColumn();
                                    $stuff_title = $db->query("SELECT stuff_title FROM suites_halls_stuff WHERE stuff_id = ".$accomoinfo['stuff_id'])->fetchColumn();
                                    $stuff_type = $db->query("SELECT stuff_type FROM suites_halls_stuff WHERE stuff_id = ".$accomoinfo['stuff_id'])->fetchColumn();
                                    
                                    echo '<p style="direction: rtl;font-size: 18px;color:#37383a;display: inline-block;font-weight: bold;margin:0 18px;padding-right: 12px;"> الجناح:&nbsp;&nbsp;&nbsp;<span style="font-size:16px; font-weight:bolder; font-family: Verdana">' . $suite_title . '</span></p>';
                                    echo '<p style="margin:0 18px;direction: rtl;font-size: 18px;color:#37383a;display: inline-block;font-weight: bold;">الصالة:&nbsp;&nbsp;&nbsp;<span style="font-size:16px; font-weight:bolder; font-family: Verdana">' . $hall_title . '</span></p>';
                                    echo '<p style="margin:0 18px;direction: rtl;font-size: 18px;color:#37383a;display: inline-block;font-weight: bold;">'. (match($stuff_type) {
                                            "bed"   => LBL_Bed,
                                            "chair" => LBL_Chair1,
                                            "bench" => LBL_Chair2
                                        } ) .':&nbsp;&nbsp;&nbsp;<span style="font-size:16px; font-weight:bolder; font-family: Verdana">' . $stuff_title . '</span></p>';
                                
                                }
                                if ($accomoinfo['bld_id'] != 0) {
                                    
                                    // building
                                    $bld_title = $db->query("SELECT bld_title FROM buildings WHERE bld_id = " . $accomoinfo['bld_id'])->fetchColumn();
                                    $floor_title = $db->query("SELECT floor_title FROM buildings_floors WHERE floor_id = " . $accomoinfo['floor_id'])->fetchColumn();
                                    $room_title = $db->query("SELECT room_title FROM buildings_rooms WHERE room_id = " . $accomoinfo['room_id'])->fetchColumn();
                                    
                                    echo '<p style="direction: rtl;font-size: 18px;color:#37383a;display: inline-block;font-weight: bold;">الغرفة:&nbsp;&nbsp;&nbsp;<span style="font-size:16px; font-weight:bolder; font-family: Verdana">' . $room_title . '</span></p>
                <p style="direction: rtl;font-size: 18px;color:#37383a;display: inline-block;font-weight: bold;margin: 0 18px;">الدور:&nbsp;&nbsp;&nbsp;<span style="font-size:16px; font-weight:bolder; font-family: Verdana">' . $floor_title . '</span></p>
                <p style="direction: rtl;font-size: 18px;color:#37383a;display: inline-block;font-weight: bold;margin: 0 18px;padding-right: 12px;">المبنى:&nbsp;&nbsp;&nbsp;<span style="font-size:16px; font-weight:bolder; font-family: Verdana">' . $bld_title . '</span></p>';
                                
                                }
                                if ($accomoinfo['tent_id'] != 0) {
                                    
                                    // tent
                                    $tent_title = $db->query("SELECT tent_title FROM tents WHERE tent_id = " . $accomoinfo['tent_id'])->fetchColumn();
                                    $tent_type = $db->query("SELECT tent_type FROM tents WHERE tent_id = " . $accomoinfo['tent_id'])->fetchColumn();
                                    
                                    if ($tent_type == 1) $tent_text = LBL_TentType1;
                                    elseif ($tent_type == 2) $tent_text = LBL_TentType2;
                                    
                                    echo '<p style="direction: rtl;font-size: 18px;color:#37383a;display: inline-block;font-weight: bold; margin:0 18px;padding-right: 45px;">' . $tent_text . ':&nbsp;&nbsp;&nbsp;<span style="font-size:16px; font-weight:bolder; font-family: Verdana">' . $tent_title . '</span></p>';
                                    
                                }
                                
                                
                            }
                        ?>
                    </div>

                </div>
                <hr>
            </div>
        </div>
    <?php } ?>
</body>
</html>
