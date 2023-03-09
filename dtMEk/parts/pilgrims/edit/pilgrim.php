<?php
    global $db, $session, $lang, $url;
    
    $edit = false;
    $title = HM_Pilgrims;
    $table = 'pils';
    $table_id = 'pil_id';
    
    if (isset($_REQUEST['id']) && is_numeric($_REQUEST['id'])) $id = $_REQUEST['id'];
    else $id = '';
    
    if ($_POST) {
        
        // check if new or update
        $continue = true;
        $errors = '';
        
        // check duplicate nationalid
        $chk2 = $db->prepare("SELECT $table_id FROM $table WHERE pil_nationalid = :pil_nationalid AND $table_id != '$id' AND pil_nationalid != '' LIMIT 1");
        $chk2->bindValue("pil_nationalid", $_POST['pil_nationalid']);
        $chk2->execute();
        
        if ($chk2->rowCount() > 0) {
            $continue = false;
            $errors .= LBL_NationalId . ' ' . LBL_AlreadyExists . '<br />';
        }
        
        if (!$continue) {
            
            $msg = '<div class="alert alert-danger alert-dismissable"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button><h4><i class="icon fa fa-check"></i>' . LBL_Error . '</h4>' . $errors . '</div>';
            
        } else {
            
            try {
                
                $db->beginTransaction();
                
                // Check if this is a new bus accomodation
                $sendnoti = false;
                $sendwelcome = false;
                
                if ($id > 0) $old_bus_id = $db->query("SELECT pil_bus_id FROM pils WHERE pil_id = $id")->fetchColumn();
                else {
                    $old_bus_id = 0;
                    $sendwelcome = true;
                }
                
                if ($_POST['pil_bus_id'] > 0 && $old_bus_id != $_POST['pil_bus_id']) $sendnoti = true;
                
                // Check if changed city
                if ($id > 0) $oldcity_id = $db->query("SELECT pil_city_id FROM pils WHERE pil_id = $id")->fetchColumn();
                else $oldcity_id = 0;
                
                $sql = $db->prepare("REPLACE INTO $table VALUES (
			:id,
			:pil_name,
			:pil_pilc_id,
			:pil_country_id,
			:pil_phone,
			:pil_nationalid,
			:pil_reservation_number,
			:pil_code,
			:pil_city_id,
			:pil_bus_id,
			:pil_photo,
			:pil_gender,
			:pil_qrcode,
			:pil_card,
			:pil_verified,
			:pil_active,
			:pil_dateadded,
			:pil_lastupdated
			)");
                
                $sql->bindValue("id", $id);
                $sql->bindValue("pil_name", $_POST['pil_name']);
                $sql->bindValue("pil_pilc_id", $_POST['pil_pilc_id']);
                $sql->bindValue("pil_country_id", $_POST['pil_country_id']);
                $sql->bindValue("pil_phone", $_POST['pil_phone']);
                $sql->bindValue("pil_nationalid", $_POST['pil_nationalid']);
                $sql->bindValue("pil_reservation_number", $_POST['pil_reservation_number']);
                $sql->bindValue("pil_code", $_POST['pil_code']);
                $sql->bindValue("pil_city_id", $_POST['pil_city_id']);
                $sql->bindValue("pil_bus_id", ($_POST['pil_bus_id'] ?? 0));
                $sql->bindValue("pil_photo", $_POST['pil_photo']);
                $sql->bindValue("pil_gender", $_POST['pil_gender']);
                $sql->bindValue("pil_qrcode", $_POST['pil_qrcode']);
                $sql->bindValue("pil_card", $_POST['pil_card']);
                $sql->bindValue("pil_verified", $_POST['pil_verified']);
                $sql->bindValue("pil_active", (isset($_POST['pil_active']) ? 1 : 0));
                $sql->bindValue("pil_dateadded", ($_POST['pil_dateadded'] ?? time()));
                $sql->bindValue("pil_lastupdated", time());
                
                if ($sql->execute()) {
                    $result = '';
                    $id = $db->lastInsertId();
                    
                    if ($sendwelcome) sendWelcomeMessagePil($id);
                    
                    if ($sendnoti) {
                        // Accomodation notification message
                        $noti_message = "أخي الحاج- أختي الحاجة
بإمكانك معرفة  الباص والمرشد الخاص بك وإصدار بطاقتك الإلكترونية
عبر تطبيق شركة العلياني لحجاج الداخل .
";
                        
                        sendPushNotification(0, null, $noti_message, 2, $id, 0, 'silent', false, false);
                        //sendSMSPilGeneral($id, $noti_message);
                        
                        $noti_message_for_sv = "اخي المرشد
بإمكانك الان معرفة الحجاج التابعين لك ومعرفة عددهم وكل تفاصيل الحاج عبر صفحتك بتطبيق العلياني لحجاج الداخل
ليهم";
                        $bus_sv = $db->query("SELECT bus_staff_id FROM buses WHERE bus_id = " . $_POST['pil_bus_id'])->fetchColumn();
                        if ($bus_sv > 0) {
                            
                            sendPushNotification(0, null, $noti_message_for_sv, 6, 0, $bus_sv, 'silent', true, false);
                            sendSMSStaffGeneral($bus_sv, $noti_message_for_sv);
                            
                        }
                        
                    }
                    
                    if ($_FILES['pil_uphoto']['tmp_name']) {
                        $ext = strtolower(pathinfo($_FILES['pil_uphoto']['name'], PATHINFO_EXTENSION));
                        $newname = GUID();
                        if (copy($_FILES['pil_uphoto']['tmp_name'], ASSETS_PATH. 'media/pils/' . $newname . '.' . $ext)) {
                            
                            $sql2 = $db->query("UPDATE $table SET pil_photo = '" . $newname . "." . $ext . "' WHERE $table_id = $id");
                            $result .= LBL_PhotoUploaded . '<br />';
                            
                        }
                    } else {
                        if (!is_numeric($_REQUEST['id'])) {
                            
                            if ($_POST['pil_gender'] == 'm') $def = 'default_male.png';
                            elseif ($_POST['pil_gender'] == 'f') $def = 'default_female.png';
                            
                            $sql2 = $db->query("UPDATE $table SET pil_photo = '$def' WHERE $table_id = $id");
                            $result .= LBL_DefaultPhotoUploaded . '<br />';
                            
                        }
                    }
                    
                    // Check Code
                    if ($oldcity_id != $_POST['pil_city_id'] || empty($_POST['pil_code'])) {
                        
                        $city_prefix = $db->query("SELECT city_prefix FROM cities WHERE city_id = " . $_POST['pil_city_id'])->fetchColumn();
                        $pad_length = 5;
                        $pad_char = 0;
                        $str_type = 'd'; // treats input as integer, and outputs as a (signed) decimal number
                        $format = "%{$pad_char}{$pad_length}{$str_type}"; // or "%04d"
                        $formatted_str = sprintf($format, $id);
                        $pil_code = $city_prefix . $formatted_str;
                        $oldcode = $db->query("SELECT pil_code FROM pils WHERE pil_id = $id")->fetchColumn();
                        $sqlupdcode = $db->query("UPDATE $table SET pil_code = '$pil_code' WHERE $table_id = $id");
                        
                        if ($oldcode && $oldcity_id > 0) {
                            // this pil changed his city and code changed, lets change all occurances in accomo
                            $sqlupdaccomo = $db->query("UPDATE pils_accomo SET pil_code = '$pil_code' WHERE pil_code = '$oldcode'");
                        }
                        
                    } else $pil_code = $_POST['pil_code'];
                    
                    // Manage QR Code
                    if (!is_file(ASSETS_PATH . 'media/pils_qrcodes/' . $id . '.png')) {
                        
                        // how to build raw content - QRCode with simple Business Card (VCard)
                        $Dir = ASSETS_PATH . 'media/pils_qrcodes/';
                        
                        // we building raw data
                        //$codeContents  = 'BEGIN:VCARD'."\n";
                        //$codeContents .= 'ID:'.$id."\n";
                        //$codeContents .= 'END:VCARD';
                        $codeContents = $id;
                        
                        // generating
                        QRcode::png($codeContents, $Dir . $id . '.png', QR_ECLEVEL_L, 10);
                        
                        $sqlupdcode = $db->query("UPDATE $table SET pil_qrcode = '$id.png' WHERE $table_id = $id");
                        
                    }
                    
                    // Accomodation notification message
                    $noti_message = "أخي الحاج- أختي الحاجة
بامكانك معرفة تفاصيل سكنك بمنى وإمكانية إصدار بطاقة إلكترونية
عبر تطبيق شركة العلياني لحجاج الداخل .
";
                    
                    
                    // Manage Accomodation
                    $countpils = $db->query("SELECT COUNT(pil_id) FROM pils")->fetchColumn();
                    if (isset($_POST['pil_accomo_type']) && $_POST['pil_accomo_type'] == '0') {
                        
                        // No accomodation
                        $sqldelaccomo = $db->query("DELETE FROM pils_accomo WHERE pil_code = '$pil_code'");
                        $result = LBL_ACCOMO_REMOVED;
                        
                    } elseif ($_POST['pil_accomo_type'] == 1) {
                        
                        if ($_POST['extratype_id']) {
                            
                            // Lets check if pil with same reservation number exists in same suite, hall, extratype_id limit by 1 order by extratype_text desc
                            $pil_res_no = $_POST['pil_reservation_number'];
                            $relative = $db->query("SELECT * FROM pils_accomo WHERE pil_code != '$pil_code' AND suite_id = " . $_POST['pacc_suite_id'] . " AND hall_id = " . $_POST['pacc_hall_id'] . " AND pil_code IN (SELECT pil_code FROM pils WHERE pil_code != '$pil_code' AND pil_reservation_number = '$pil_res_no') ORDER BY extratype_text DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
                            
                            if ($relative) {
                                
                                // we found him, lets get the extratype_text of the next pil, assign it to this pil, and move the other pil to foreach
                                $otherpil = $db->query("SELECT * FROM pils_accomo WHERE pil_code != '$pil_code' AND suite_id = " . $_POST['pacc_suite_id'] . " AND hall_id = " . $_POST['pacc_hall_id'] . " AND extratype_text > '" . $relative['extratype_text'] . "' ORDER BY extratype_text LIMIT 1")->fetch(PDO::FETCH_ASSOC);
                                $extratype_text = $otherpil['extratype_text'];
                                
                            } else {
                                
                                for ($i = 1; $i <= $countpils; $i++) {
                                    
                                    $reserved = $db->query("SELECT pil_code FROM pils_accomo WHERE pil_code != '$pil_code' AND suite_id > 0 AND hall_id > 0 /* AND extratype_id = " . $_POST['extratype_id'] . " */ AND extratype_text = '$i'")->fetchColumn();
                                    if (!$reserved) {
                                        
                                        $extratype_text = $i;
                                        break;
                                        
                                    }
                                }
                                
                            }
                            
                        } else $extratype_text = '';
                        
                        // Suite accomodation
                        
                        
                        $stmt = $db->prepare("SELECT * FROM pils_accomo WHERE pil_code = ? ");
                        $stmt->execute(array($pil_code));
                        $check = $stmt->rowCount();
                        
                        if ($check > 0) {
                            // Update
                            $sqlac = $db->prepare("UPDATE pils_accomo SET suite_id = ? , hall_id = ? , extratype_id = ?  , extratype_text = ? WHERE pil_code = ?");
                            $sqlac->execute(array($_POST['pacc_suite_id'], $_POST['pacc_hall_id'], $_POST['extratype_id'], $extratype_text, $pil_code));
                            
                            $result = LBL_ACCOMO_UPDATED;
                            
                            sendPushNotification(0, null, $noti_message, 2, $id, 0, 'silent', false, false);
                            //sendSMSPilGeneral($id, $noti_message);
                            
                        } else {
                            // Insert
                            
                            $sqlac = $db->prepare("INSERT INTO pils_accomo VALUES (
        						:pil_code,
        						1,
        						:suite_id,
        						:hall_id,
        						0,
        						0,
        						0,
        						0,
        						0,
        						0,
        						0,
        						:extratype_id,
        						:extratype_text
        					) ON DUPLICATE KEY UPDATE pil_accomo_type = 1, suite_id = :suite_id, hall_id = :hall_id, bld_id = 0, floor_id = 0, room_id = 0, tent_id = 0, halls_id = 0 , seats = 0 , bus_id = 0, extratype_id = :extratype_id, extratype_text = :extratype_text");
                            
                            $sqlac->bindValue("pil_code", $pil_code);
                            $sqlac->bindValue("suite_id", $_POST['pacc_suite_id']);
                            $sqlac->bindValue("hall_id", $_POST['pacc_hall_id']);
                            $sqlac->bindValue("extratype_id", $_POST['extratype_id']);
                            $sqlac->bindValue("extratype_text", $extratype_text);
                            $sqlac->execute();
                            
                            $result = LBL_ACCOMO_UPDATED;
                            
                            sendPushNotification(0, null, $noti_message, 2, $id, 0, 'silent', false, false);
                            //sendSMSPilGeneral($id, $noti_message);
                            
                        }
                        
                        
                        if ($otherpil) {
                            
                            for ($i = 1; $i <= $countpils; $i++) {
                                
                                $reserved = $db->query("SELECT pil_code FROM pils_accomo WHERE pil_code != '$pil_code' AND suite_id > 0 AND hall_id > 0 /* AND extratype_id = " . $_POST['extratype_id'] . " */ AND extratype_text = '$i'")->fetchColumn();
                                if (!$reserved) {
                                    
                                    $extratype_text_otherpil = $i;
                                    break;
                                    
                                }
                            }
                            
                            $sqlup_otherpil = $db->query("UPDATE pils_accomo SET extratype_text = $extratype_text_otherpil WHERE pil_code = '" . $otherpil['pil_code'] . "'");
                            
                        }
                        
                    } elseif ($_POST['pil_accomo_type'] == 2 || $_POST['pil_accomo_type'] == 5) {
                        
                        if ($_POST['pil_accomo_type'] == 2) $extratype_id = 1;
                        elseif ($_POST['pil_accomo_type'] == 5) $extratype_id = 2;
                        // Building accomodation
                        
                        
                        $stmt = $db->prepare("SELECT * FROM pils_accomo WHERE pil_code = ? ");
                        $stmt->execute(array($pil_code));
                        $check = $stmt->rowCount();
                        
                        if ($check > 0) {
                            // Update
                            $sqlac = $db->prepare("UPDATE pils_accomo SET bld_id = ? , floor_id = ? , room_id = ? WHERE pil_code = ?");
                            $sqlac->execute(array($_POST['pacc_bld_id'], $_POST['pacc_floor_id'], $_POST['pacc_room_id'], $pil_code));
                            
                            $result = LBL_ACCOMO_UPDATED;
                            
                            sendPushNotification(0, null, $noti_message, 2, $id, 0, 'silent', false, false);
                            //sendSMSPilGeneral($id, $noti_message);
                            
                        } else {
                            // Insert
                            
                            $sqlac = $db->prepare("INSERT INTO pils_accomo VALUES (
        						:pil_code,
        						2,
        						0,
        						0,
        						:bld_id,
        						:floor_id,
        						:room_id,
        						0,
        						0,
        						0,
        						0
        						:extratype_id,
        						''
        					) ON DUPLICATE KEY UPDATE pil_accomo_type = 2, suite_id = 0, hall_id = 0, bld_id = :bld_id, floor_id = :floor_id, room_id = :room_id, tent_id = 0, halls_id = 0 , seats = 0 ,bus_id = 0, extratype_id = :extratype_id, extratype_text = ''");
                            
                            $sqlac->bindValue("pil_code", $pil_code);
                            $sqlac->bindValue("bld_id", $_POST['pacc_bld_id']);
                            $sqlac->bindValue("floor_id", $_POST['pacc_floor_id']);
                            $sqlac->bindValue("room_id", $_POST['pacc_room_id']);
                            $sqlac->bindValue("extratype_id", $extratype_id);
                            
                            $sqlac->execute();
                            
                            $result = LBL_ACCOMO_UPDATED;
                            
                            sendPushNotification(0, null, $noti_message, 2, $id, 0, 'silent', false, false);
                            //sendSMSPilGeneral($id, $noti_message);
                            
                        }
                        
                        
                    } elseif ($_POST['pil_accomo_type'] == 3) {
                        
                        
                        // Tent accomodation
                        
                        
                        $stmt = $db->prepare("SELECT * FROM pils_accomo WHERE pil_code = ? ");
                        $stmt->execute(array($pil_code));
                        $check = $stmt->rowCount();
                        
                        if ($check > 0) {
                            // Update
                            $sqlac = $db->prepare("UPDATE pils_accomo SET tent_id = ? WHERE pil_code = ?");
                            $sqlac->execute(array($_POST['pacc_tent_id'], $pil_code));
                            
                            $result = LBL_ACCOMO_UPDATED;
                            
                            sendPushNotification(0, null, $noti_message, 2, $id, 0, 'silent', false, false);
                            //sendSMSPilGeneral($id, $noti_message);
                            
                        } else {
                            // Insert
                            
                            $sqlac = $db->prepare("INSERT INTO pils_accomo VALUES (
            						:pil_code,
            						3,
            						0,
            						0,
            						0,
            						0,
            						0,
            						:tent_id,
            						0,
            						0,
            						0,
            						'',
            						''
            					) ON DUPLICATE KEY UPDATE pil_accomo_type = 3, suite_id = 0, hall_id = 0, bld_id = 0, floor_id = 0, room_id = 0, tent_id = :tent_id,halls_id = 0 , seats = 0 , bus_id = 0, extratype_id = '', extratype_text = ''");
                            
                            $sqlac->bindValue("pil_code", $pil_code);
                            $sqlac->bindValue("tent_id", $_POST['pacc_tent_id']);
                            $sqlac->execute();
                            
                            $result = LBL_ACCOMO_UPDATED;
                            
                            sendPushNotification(0, null, $noti_message, 2, $id, 0, 'silent', false, false);
                            //sendSMSPilGeneral($id, $noti_message);
                            
                        }
                        
                        
                    } elseif ($_POST['pil_accomo_type'] == 4) {
                        
                        // Tent accomodation
                        
                        $stmt = $db->prepare("SELECT * FROM pils_accomo WHERE pil_code = ? ");
                        $stmt->execute(array($pil_code));
                        $check = $stmt->rowCount();
                        
                        if ($check > 0) {
                            // Update
                            $sqlac = $db->prepare("UPDATE pils_accomo SET bus_id = ? WHERE pil_code = ?");
                            $sqlac->execute(array($_POST['pacc_bus_id'], $pil_code));
                            
                            $result = LBL_ACCOMO_UPDATED;
                            
                            sendPushNotification(0, null, $noti_message, 2, $id, 0, 'silent', false, false);
                            //sendSMSPilGeneral($id, $noti_message);
                            
                        } else {
                            // Insert
                            
                            // Bus accomodation
                            $sqlac = $db->prepare("INSERT INTO pils_accomo VALUES (
            						:pil_code,
            						3,
            						0,
            						0,
            						0,
            						0,
            						0,
            						0,
            						0,
            						0,
            						:bus_id,
            						'',
            						''
            					) ON DUPLICATE KEY UPDATE pil_accomo_type = 4, suite_id = 0, hall_id = 0, bld_id = 0, floor_id = 0, room_id = 0, tent_id = 0, halls_id = 0 , seats = 0 , bus_id = :bus_id, extratype_id = :extratype_id, extratype_text = ''");
                            
                            $sqlac->bindValue("pil_code", $pil_code);
                            $sqlac->bindValue("bus_id", $_POST['pacc_bus_id']);
                            $sqlac->execute();
                            
                            $result = LBL_ACCOMO_UPDATED;
                            
                            sendPushNotification(0, null, $noti_message, 2, $id, 0, 'silent', false, false);
                            //sendSMSPilGeneral($id, $noti_message);
                            
                        }
                        
                        
                    }
                    
                    if (!empty($_POST['pil_accomo_type_arafa'])) {
                        
                        // Tent accomodation
                        
                        $stmt = $db->prepare("SELECT * FROM pils_accomo WHERE pil_code = ? ");
                        $stmt->execute(array($pil_code));
                        $check = $stmt->rowCount();
                        
                        if ($check > 0) {
                            // Update
                            $sqlac = $db->prepare("UPDATE pils_accomo SET halls_id = ?  , seats = ? WHERE pil_code = ?");
                            $sqlac->execute(array($_POST['pil_accomo_type_arafa'], $_POST['seat'], $pil_code));
                            
                            $result = LBL_ACCOMO_UPDATED;
                            
                            sendPushNotification(0, null, $noti_message, 2, $id, 0, 'silent', false, false);
                            //sendSMSPilGeneral($id, $noti_message);
                            
                        } else {
                            // Insert
                            
                            $sqlac = $db->prepare("INSERT INTO pils_accomo VALUES (
    						:pil_code,
    						3,
    						0,
    						0,
    						0,
    						0,
    						0,
    						0,
                            :halls_id,
                            :seats,
    						0,
    						'',
    						''
    					) ON DUPLICATE KEY UPDATE pil_accomo_type = 10, suite_id = 0, hall_id = 0, bld_id = 0, floor_id = 0, room_id = 0, tent_id = 0,halls_id = :halls_id , seats = :seats , bus_id = 0, extratype_id = '', extratype_text = ''");
                            
                            $sqlac->bindValue("pil_code", $pil_code);
                            $sqlac->bindValue("halls_id", $_POST['pil_accomo_type_arafa']);
                            $sqlac->bindValue("seats", $_POST['seat']);
                            $sqlac->execute();
                            
                            $result = LBL_ACCOMO_UPDATED;
                            
                            sendPushNotification(0, null, $noti_message, 2, $id, 0, 'silent', false, false);
                            //sendSMSPilGeneral($id, $noti_message);
                            
                        }
                        
                        
                    }
                    
                    
                    if ($_REQUEST['id'] > 0) $label = LBL_Updated;
                    else $label = LBL_Added;
                    
                    $msg = '<div class="alert alert-success alert-dismissable"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button><h4><i class="icon fa fa-check"></i>' . $label . '!</h4>' . LBL_Item . ' ' . $label . ' ' . LBL_Successfully . '<br />' . $result . '</div>';
                    
                    unset($_POST);
                    
                } else {
                    
                    $msg = '<div class="alert alert-danger alert-dismissable"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button><h4><i class="icon fa fa-check"></i>' . LBL_Error . '</h4>' . LBL_ErrorUpdateAdd . '</div>';
                    
                }
                
                $db->commit();
                
            } catch (PDOException $e) {
                
                $db->rollBack();
                $msg = '<div class="alert alert-danger alert-dismissable"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button><h4><i class="icon fa fa-check"></i>' . LBL_Error . '</h4>' . LBL_ErrorDB . ' ' . $e->getMessage() . '</div>';
                
            }
            
        }
        
    }
    
    if (is_numeric($id)) {
        
        $edit = true;
        $row = $db->query("SELECT * FROM $table WHERE $table_id = " . $id)->fetch();
        
    }

?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?= $title ?>
            <small><?= $edit ? LBL_Edit : LBL_New ?></small>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <!-- left column -->
            <div class="col-md-12">
                
                <?= $msg??'' ?>
                <!-- Input addon -->
                <form role="form" method="post" enctype="multipart/form-data">

                    <div class="box box-info">

                        <div class="box-body">

                            <div class="row">

                                <div class="form-group col-sm-6">
                                    <label><?= LBL_Name ?></label>
                                    <input type="text" class="form-control" name="pil_name" required="required"
                                           value="<?= $_POST['pil_name'] ?? $row['pil_name'] ?? '' ?>"/>
                                </div>

                                <div class="form-group col-sm-6">
                                    <label><?= LBL_Class ?></label>
                                    <select name="pil_pilc_id" class="form-control select2">
                                        <?php
                                            $sql_pilc = $db->query("SELECT * FROM pils_classes ORDER BY pilc_title_en");
                                            while ($row_pilc = $sql_pilc->fetch(PDO::FETCH_ASSOC)) {
                                                
                                                echo '<option value="' . $row_pilc['pilc_id'] . '" ';
                                                if ((isset($row['pil_pilc_id']) && $row['pil_pilc_id'] == $row_pilc['pilc_id']) || (isset($_POST['pil_pilc_id']) && $_POST['pil_pilc_id'] == $row_pilc['pilc_id'])) echo 'selected="selected"';
                                                echo '>' . $row_pilc['pilc_title_' . $lang] . '</option>';
                                                
                                            }
                                        ?>
                                    </select>
                                </div>

                            </div>
                            <div class="row">
                                <div class="form-group col-sm-6">
                                    <label><?= LBL_Nationality ?></label>
                                    <select name="pil_country_id" class="form-control select2">
                                        <?php
                                            $sql_c = $db->query("SELECT country_id, country_title_$lang FROM countries ORDER BY country_title_$lang");
                                            while ($row_c = $sql_c->fetch(PDO::FETCH_ASSOC)) {
                                                
                                                echo '<option value="' . $row_c['country_id'] . '" ';
                                                if ((isset($row['pil_country_id']) && $row['pil_country_id'] == $row_c['country_id']) || (isset($_POST['pil_country_id']) && $_POST['pil_country_id'] == $row_c['country_id'])) echo 'selected="selected"';
                                                echo '>' . $row_c['country_title_' . $lang] . '</option>';
                                                
                                            }
                                        ?>
                                    </select>
                                </div>

                                <div class="form-group col-sm-6">
                                    <label><?= LBL_Gender ?></label>
                                    <select name="pil_gender" id="pil_gender" class="form-control select2">
                                        <option value="m" <?php if (isset($row['pil_gender']) && $row['pil_gender'] == 'm') echo 'selected="selected"'; ?>><?= LBL_Male ?></option>
                                        <option value="f" <?php if (isset($row['pil_gender']) && $row['pil_gender'] == 'f') echo 'selected="selected"'; ?>><?= LBL_Female ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-6">
                                    <label><?= LBL_Phone ?></label>
                                    <input type="text" class="form-control" name="pil_phone" required="required"
                                           value="<?= $_POST['pil_phone'] ?? $row['pil_phone'] ?? '' ?>"/>
                                </div>

                                <div class="form-group col-sm-6">
                                    <label><?= LBL_NationalId ?></label>
                                    <input type="text" class="form-control" name="pil_nationalid" required="required"
                                           value="<?= $_POST['pil_nationalid'] ?? $row['pil_nationalid'] ?? '' ?>"/>
                                </div>
                            </div>


                            <div class="form-group">
                                <label><?= LBL_ReservationNumber ?></label>
                                <input type="text" class="form-control" name="pil_reservation_number"
                                       required="required"
                                       value="<?= $_POST['pil_reservation_number'] ?? $row['pil_reservation_number'] ?? '' ?>"/>
                            </div>

                            <div class="form-group">
                                <label><?= LBL_City ?></label>
                                <select name="pil_city_id" class="form-control select2"
                                        onchange="cityselected(this.value)" required="required">
                                    <option value=""><?= LBL_Choose ?></option>
                                    <?php
                                        $sql_ci = $db->query("SELECT city_id, city_title_$lang FROM cities ORDER BY city_title_$lang");
                                        while ($row_ci = $sql_ci->fetch(PDO::FETCH_ASSOC)) {
                                            
                                            echo '<option value="' . $row_ci['city_id'] . '" ';
                                            if ((isset($row['pil_city_id']) && $row['pil_city_id'] == $row_ci['city_id']) || (isset($_POST['pil_city_id']) && $_POST['pil_city_id'] == $row_ci['city_id'])) echo 'selected="selected"';
                                            echo '>' . $row_ci['city_title_' . $lang] . '</option>';
                                            
                                        }
                                    ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label><?= HM_Bus ?></label>
                                <span id="busarea">
												<?php if (isset($row)) { ?>
                                                    <select name="pil_bus_id" class="form-control select2">
													<option value=""><?= LBL_Choose ?></option>
													<?php
                                                        $sql_bus = $db->query("SELECT bus_id, bus_title, bus_seats FROM buses WHERE bus_active = 1 AND bus_city_id = " . $row['pil_city_id'] . " ORDER BY bus_order");
                                                        while ($row_bus = $sql_bus->fetch(PDO::FETCH_ASSOC)) {
                                                            
                                                            $used = $db->query("SELECT COUNT(pil_id) FROM pils WHERE pil_bus_id = " . $row_bus['bus_id'])->fetchColumn();
                                                            $remaining = $row_bus['bus_seats'] - $used;
                                                            
                                                            if ($row['pil_bus_id'] == $row_bus['bus_id'] || $remaining > 0) {
                                                                echo '<option value="' . $row_bus['bus_id'] . '" ';
                                                                if ($row['pil_bus_id'] == $row_bus['bus_id']) echo 'selected="selected"';
                                                                echo '>' . $row_bus['bus_title'] . '</option>';
                                                            }
                                                            
                                                        }
                                                    ?>
												</select>
                                                <?php } else echo '<br />' . LBL_ChooseCity ?>
											</span>
                            </div>

                            <div class="row">

                                <div class="form-group col-sm-6">
                                    <label><?= LBL_Photo ?></label><br/>
                                    <?php if (isset($row['pil_photo']) && $row['pil_photo']) echo '<img src="'.CP_PATH.'/assets/media/pils/' . $row['pil_photo'] . '" width="150"/>'; ?>
                                    <input id="pil_uphoto" name="pil_uphoto" type="file" class="file">
                                </div>

                                <div class="form-group col-sm-6">
                                    <label><?= LBL_QRCode ?></label><br/>
                                    <?php
                                        
                                        if (isset($row)) {
                                            
                                            echo '<img src="'.CP_PATH.'/assets/media/pils_qrcodes/' . $row['pil_qrcode'] . '" style="width:150px" />';
                                            
                                        } else {
                                            
                                            echo LBL_WILLAUTOGENERATE;
                                            
                                        }
                                    
                                    ?>

                                </div>

                            </div>


                            <div class="form-group">
                                <label><input type="checkbox"
                                              name="pil_active" <?php if (!isset($row) || $row['pil_active'] == 1) echo 'checked="checked"'; ?> /> <?= LBL_Active ?>
                                </label>
                            </div>


                        </div><!-- /.box-body -->
                    </div><!-- /.box -->


                    <div class="box box-info">

                        <div class="box-body">

                            <h2><?= LBL_Accomodation ?> ( <?= LBL_MENA ?> ) </h2>
                            <?php
                                $accomoinfo = $db->query("SELECT * FROM pils_accomo WHERE pil_code = '" . (isset($row)?$row['pil_code']:'NULL') . "'")->fetch(PDO::FETCH_ASSOC);
                                
                                if ($accomoinfo) {
                                    
                                    if ($accomoinfo['suite_id'] != 0 || $accomoinfo['hall_id'] != 0) {
                                        
                                        $suite_title = $db->query("SELECT suite_title FROM suites WHERE suite_id = " . $accomoinfo['suite_id'])->fetchColumn();
                                        $hall_title = $db->query("SELECT hall_title FROM suites_halls WHERE hall_id = " . $accomoinfo['hall_id'])->fetchColumn();
                                        
                                        echo '<div class="panel well">';
                                        echo '<div class="row">';
                                        
                                        if ($accomoinfo['extratype_id'] > 0) {
                                            
                                            echo '<div class="col-sm-3">';
                                            echo '<b>' . LBL_AccomodationType . '</b><br />' . HM_Suites;
                                            echo '</div>';
                                            echo '<div class="col-sm-3">';
                                            echo '<b>' . HM_Suite . '</b><br />' . $suite_title;
                                            echo '</div>';
                                            echo '<div class="col-sm-3">';
                                            echo '<b>' . HM_Hall . '</b><br />' . $hall_title;
                                            echo '</div>';
                                            echo '<div class="col-sm-3">';
                                            if ($accomoinfo['extratype_id'] == 1) echo '<b>' . LBL_Chair1 . '</b>';
                                            elseif ($accomoinfo['extratype_id'] == 2) echo '<b>' . LBL_Chair2 . '</b>';
                                            elseif ($accomoinfo['extratype_id'] == 3) echo '<b>' . LBL_Bed . '</b>';
                                            echo '<br />' . $accomoinfo['extratype_text'];
                                            echo '</div>';
                                            echo '</div>';
                                            echo '</div>';
                                            
                                        } else {
                                            
                                            echo '<div class="col-sm-4">';
                                            echo '<b>' . LBL_AccomodationType . '</b><br />' . HM_Suites;
                                            echo '</div>';
                                            echo '<div class="col-sm-4">';
                                            echo '<b>' . HM_Suite . '</b><br />' . $suite_title;
                                            echo '</div>';
                                            echo '<div class="col-sm-4">';
                                            echo '<b>' . HM_Hall . '</b><br />' . $hall_title;
                                            echo '</div>';
                                            echo '</div>';
                                            echo '</div>';
                                            
                                        }
                                        
                                        
                                    }
                                    
                                    if ($accomoinfo['bld_id'] != 0 || $accomoinfo['floor_id'] != 0 || $accomoinfo['room_id'] != 0) {
                                        
                                        $bld_title = $db->query("SELECT bld_title FROM buildings WHERE bld_id = " . $accomoinfo['bld_id'])->fetchColumn();
                                        $floor_title = $db->query("SELECT floor_title FROM buildings_floors WHERE floor_id = " . $accomoinfo['floor_id'])->fetchColumn();
                                        $room_title = $db->query("SELECT room_title FROM buildings_rooms WHERE room_id = " . $accomoinfo['room_id'])->fetchColumn();
                                        
                                        echo '<div class="panel well">';
                                        echo '<div class="row">';
                                        echo '<div class="col-sm-3">';
                                        echo '<b>' . LBL_AccomodationType . '</b><br />' . HM_Buildings . ' / ' . LBL_PremisesPlus;
                                        echo '</div>';
                                        echo '<div class="col-sm-3">';
                                        if ($accomoinfo['pil_accomo_type'] == 2) echo '<b>' . HM_Building . '</b>';
                                        elseif ($accomoinfo['pil_accomo_type'] == 5) echo '<b>' . LBL_Premises . '</b>';
                                        echo '<br />' . $bld_title;
                                        echo '</div>';
                                        echo '<div class="col-sm-3">';
                                        echo '<b>' . HM_Floor . '</b><br />' . $floor_title;
                                        echo '</div>';
                                        echo '<div class="col-sm-3">';
                                        echo '<b>' . HM_Room . '</b><br />' . $room_title;
                                        echo '</div>';
                                        echo '</div>';
                                        echo '</div>';
                                        
                                    }
                                    if ($accomoinfo['tent_id'] != 0) {
                                        
                                        $tent_title = $db->query("SELECT tent_title FROM tents WHERE type = 1 AND tent_id = " . $accomoinfo['tent_id'])->fetchColumn();
                                        $tent_type = $db->query("SELECT tent_type FROM tents WHERE type = 1 AND tent_id = " . $accomoinfo['tent_id'])->fetchColumn();
                                        
                                        if ($tent_type == 1) $tent_text = LBL_TentType1;
                                        elseif ($tent_type == 2) $tent_text = LBL_TentType2;
                                        
                                        echo '<div class="panel well">';
                                        echo '<div class="row">';
                                        echo '<div class="col-sm-6">';
                                        echo '<b>' . LBL_AccomodationType . '</b><br />' . HM_Tents;
                                        echo '</div>';
                                        echo '<div class="col-sm-6">';
                                        echo '<b>' . $tent_text . '</b><br />' . $tent_title;
                                        echo '</div>';
                                        echo '</div>';
                                        echo '</div>';
                                        
                                    }
                                    if ($accomoinfo['bus_id'] != 0) {
                                        
                                        $bus_title = $db->query("SELECT bus_title FROM buses WHERE bus_id = " . $accomoinfo['bus_id'])->fetchColumn();
                                        
                                        echo '<div class="panel well">';
                                        echo '<div class="row">';
                                        echo '<div class="col-sm-6">';
                                        echo '<b>' . LBL_AccomodationType . '</b><br />' . HM_PilgrimsBuses;
                                        echo '</div>';
                                        echo '<div class="col-sm-6">';
                                        echo '<b>' . HM_Bus . '</b><br />' . $bus_title;
                                        echo '</div>';
                                        echo '</div>';
                                        echo '</div>';
                                        
                                    }
                                    if ($accomoinfo['halls_id'] != 0) {
                                        echo "<h2>" . LBL_Accomodation . " ( " . arafa . " ) </h2>";
                                        $tent_title = $db->query("SELECT tent_title FROM tents WHERE type = 2 AND tent_id = " . $accomoinfo['halls_id'])->fetchColumn();
                                        $tent_type = $db->query("SELECT tent_type FROM tents WHERE type = 2 AND tent_id = " . $accomoinfo['halls_id'])->fetchColumn();
                                        
                                        if ($tent_type == 1) $tent_text = LBL_TentType1;
                                        elseif ($tent_type == 2) $tent_text = LBL_TentType2;
                                        
                                        echo '<div class="panel well">';
                                        echo '<div class="row">';
                                        echo '<div class="col-sm-6">';
                                        echo '<b>' . $tent_text . '</b><br />' . $tent_title;
                                        echo '</div>';
                                        echo '<div class="col-sm-6">';
                                        echo '<b>' . with_seat . '</b><br />' ?><?php if ($accomoinfo['seats'] == 1) {
                                            echo with_seat;
                                        } else {
                                            echo without_seat;
                                        }
                                        echo '</div>';
                                        echo '</div>';
                                        echo '</div>';
                                        
                                    }
                                    
                                }
                            
                            
                            ?>


                            <div class="form-group">
                                <label><?= LBL_AccomodationType ?></label>
                                <select name="pil_accomo_type" class="form-control select2"
                                        onchange="pilaccomoselect(this.value);">
                                    <option value=""><?= LBL_CHANGEACCOMODATION ?></option>
                                    <option value="0"><?= LBL_NOACCOMODATION ?></option>
                                    <option value="1"><?= HM_Suite ?></option>
                                    <option value="2"><?= HM_Building ?></option>
                                    <option value="5"><?= LBL_Premises ?></option>
                                    <option value="3"><?= HM_Tent ?></option>
                                    <!-- <option value="4"><?= HM_Bus ?></option> -->
                                </select>
                            </div>

                            <div id="accomoarea"></div>

                            <div class="form-group col-sm-6">
                                <label><?= LBL_TentNumber_halls ?> ( <?= arafa ?> )</label>
                                <select name="pil_accomo_type_arafa" class="form-control select2">
                                    <option value=""><?= LBL_TentNumber_halls ?></option>
                                    <?php
                                        $gender = $row['pil_gender'];
                                        $sqltents = $db->query("SELECT * FROM tents WHERE tent_active = 1 AND type = 2 AND  tent_gender = '$gender'  ORDER BY tent_title");
                                        while ($rowt = $sqltents->fetch(PDO::FETCH_ASSOC)) {
                                            echo '<option value="' . $rowt['tent_id'] . '" ';
                                            echo '>
														' . $rowt['tent_title'] . '
														</option>';
                                        }
                                    ?>

                                </select>
                            </div>
                            <div class="form-group col-sm-6">
                                <label><?= with_seat ?>  </label>
                                <div>
                                    <select name="seat" class="form-control select2">

                                        <option value="1"><?= with_seat ?></option>
                                        <option value="0"><?= without_seat ?></option>
                                    </select>
                                </div>
                            </div>


                        </div>

                    </div>

                    <input type="submit" class="col-md-12 btn btn-success"
                           value="<?= $edit ? LBL_Update : LBL_Add ?>"/>
                    <input type="hidden" name="id" id="id" value="<?= $id ?>"/>
                    <input type="hidden" name="pil_code" id="pil_code" value="<?= $row['pil_code']??'' ?>"/>
                    <input type="hidden" name="pil_photo" id="pil_photo" value="<?= $row['pil_photo']??'' ?>"/>
                    <input type="hidden" name="pil_qrcode" id="pil_qrcode" value="<?= $row['pil_qrcode']??'' ?>"/>
                    <input type="hidden" name="pil_card" id="pil_card" value="<?= $row['pil_card']??'' ?>"/>
                    <input type="hidden" name="pil_verified" id="pil_verified" value="<?= $row['pil_verified']??'' ?>"/>
                    <input type="hidden" name="pil_dateadded" id="pil_dateadded" value="<?= $row['pil_dateadded']??time() ?>"/>

                </form>

            </div><!--/.col (left) -->
        </div>   <!-- /.row -->
    </section><!-- /.content -->

</div>
<script>
    $('select').select2();
    $("#pil_uphoto").fileinput({
        showRemove: true,
        showUpload: false,
        showCancel: false,
        showPreview: true,
        minFileCount: 0,
        maxFileCount: 1,
        allowedFileTypes: ['image', 'pdf']
    });

    function pilaccomoselect(accomo_type) {

        if (accomo_type > 0) {

            $('#accomoarea').html('<?=LBL_Loading?>');

            var data = {
                pil_id: <?=$row['pil_id'] ?? 0?>,
                pil_gender: $('#pil_gender').val(),
                pil_accomo_type: accomo_type
            };

            $.post('<?= CP_PATH ?>/post/getAccomoInfo', data, function (response) {

                $('#accomoarea').html(response);
                $('select').select2();

            });

        } else {

            $('#accomoarea').empty();

        }

    }

    function suiteselected(suite_id, gender) {

        $('#hallsarea').html('<?=LBL_Loading?>');

        var data = {
            suite_id: suite_id,
            gender: gender
        };

        $.post('<?= CP_PATH ?>/post/getSuiteHalls', data, function (response) {
            $('#hallsarea').html(response);
            $('select').select2();
        });

    }

    function hallselected(hall_id) {

        //$('#afterhallsarea').html('<label><?=LBL_Chair1?> / <?=LBL_Chair2?> / <?=LBL_Bed?></label><select name="extratype_id" class="form-control select2"><option value="0"><?=LBL_Choose?></option><option value="1"><?=LBL_Chair1?></option><option value="2"><?=LBL_Chair2?></option><option value="3"><?=LBL_Bed?></option></select><br /><input type="text" class="form-control" name="extratype_text"  />');
        $('#afterhallsarea').html('<label><?=LBL_Chair1?> / <?=LBL_Chair2?> / <?=LBL_Bed?></label><select name="extratype_id" class="form-control select2"><option value="0"><?=LBL_Choose?></option><option value="1"><?=LBL_Chair1?></option><option value="2"><?=LBL_Chair2?></option><option value="3"><?=LBL_Bed?></option></select>');
        $('select').select2();
    }

    function bldselected(bld_id, gender) {

        $('#floorsarea').html('<?=LBL_Loading?>');

        var data = {
            bld_id: bld_id,
            gender: gender
        };

        $.post('<?= CP_PATH ?>/post/getBuildingFloors', data, function (response) {
            $('#floorsarea').html(response);
            $('select').select2();
        });

    }

    function floorselected(floor_id, gender) {

        $('#roomsarea').html('<?=LBL_Loading?>');

        var data = {
            floor_id: floor_id,
            gender: gender
        };

        $.post('<?= CP_PATH ?>/post/getFloorRooms', data, function (response) {
            $('#roomsarea').html(response);
            $('select').select2();
        });

    }

    function cityselected(city_id) {

        if (city_id > 0) {

            $('#busarea').html('<br /><?=LBL_Loading?>');

            var data = {
                city_id: city_id
            };

            $.post('<?= CP_PATH ?>/post/grabBusesOfCity', data, function (response) {
                $('#busarea').html(response);
                $('select').select2();
            });

        } else {

            $('#busarea').html('<br /><?=LBL_ChooseCity?>');

        }

    }
</script>
