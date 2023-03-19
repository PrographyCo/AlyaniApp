<?php
    require '_includes/init.php';
    newlog($_SERVER['REQUEST_URI'], $_GET, $_POST, $_FILES);
    $lang = checkLang();
    
    $data = $_POST;
    $expectedParams[]['string'] = 'param1';
    $expectedParams[]['string'] = 'param2';
    $expectedParams[]['integer'] = 'type';
    
    $requiredParams[]['string'] = 'param1';
    $requiredParams[]['string'] = 'param2';
    $requiredParams[]['integer'] = 'type';
    
    if (checkParams($data, $expectedParams) && missingValues($data, $requiredParams)) {
        
        try {
            
            if ($data['type'] == 1) {
                
                $sql1 = $db->prepare("SELECT pil_id, pil_verified, pil_phone FROM pils WHERE pil_nationalid = :nationalid AND pil_active = 1 LIMIT 1");
                $sql1->bindValue("nationalid", $data['param1']);
                $sql1->execute();
                
                if ($sql1->rowCount() > 0) {
                    
                    $pilinfo = $sql1->fetch(PDO::FETCH_ASSOC);
                    $items['success'] = true;
                    $items['message'] = '';
                    
                    // check if it's a new phone number
                    $newphone = $data['param2'];
                    if ($newphone && $newphone != $pilinfo['pil_phone']) {
                        $pilinfo['pil_verified'] = 0;
                        $sqlupd = $db->prepare("UPDATE pils SET pil_phone = :phone, pil_verified = 0 WHERE pil_id = :pil_id");
                        $sqlupd->bindValue("phone", $data['param2']);
                        $sqlupd->bindValue("pil_id", $pilinfo['pil_id']);
                        $sqlupd->execute();
                    }
                    
                    
                    $items['data']['pilgrim'] = getPilInfo($pilinfo['pil_id']);
                    $items['data']['token'] = newAccessToken($pilinfo['pil_id'], 1);
                    
                    if ($pilinfo['pil_verified'] == 0) {
                        
                        // Lets generate code, update database, send SMS
                        $code = rand(1000, 9999);
                        $sqlupdcode = $db->query("INSERT INTO pils_codes VALUES (" . $pilinfo['pil_id'] . ", $code) ON DUPLICATE KEY UPDATE code = $code");
                        
                        $smsresult = sendSMSPil($pilinfo['pil_id'], $code);
                        
                        if ($smsresult['code'] == '1') {
                            $items['sentSMS'] = true;
                        } else {
                            $items['sentSMS'] = false;
                            $items['SMSErrorCode'] = $smsresult['code'];
                        }
                        
                    }
                    
                } else {
                    
                    $items['success'] = false;
                    if ($lang == 'ar') $items['message'] = 'حاج غير موجود';
                    else $items['message'] = 'Pilgrim not found';
                    
                }
                
            } elseif ($_POST['type'] == 2) {
                
                // Supervisor
                $sql2 = $db->prepare("SELECT staff_id, staff_password FROM staff WHERE staff_username = :username AND staff_type = 2 AND staff_active = 1 AND staff_id IN (SELECT bus_staff_id FROM buses) LIMIT 1");
                $sql2->bindValue("username", $data['param1']);
                $sql2->execute();
                
                if ($sql2->rowCount() > 0) {
                    
                    $staffinfo = $sql2->fetch(PDO::FETCH_ASSOC);
                    
                    if (password_verify($data['param2'], $staffinfo['staff_password'])) {
                        $accomo = $db->query("SELECT pa.*, bu.bld_title, f.floor_title, r.room_title, b.bus_title, s.suite_title, h.hall_title, t.tent_title
					FROM pils_accomo pa
					LEFT OUTER JOIN buildings bu ON pa.bld_id = bu.bld_id
					LEFT OUTER JOIN buildings_floors f ON pa.floor_id = f.floor_id
					LEFT OUTER JOIN buildings_rooms r ON pa.room_id = r.room_id
					LEFT OUTER JOIN buses b ON pa.bus_id = b.bus_id
					LEFT OUTER JOIN tents t ON pa.tent_id = t.tent_id
					LEFT OUTER JOIN suites s ON pa.suite_id = s.suite_id
					LEFT OUTER JOIN suites_halls h ON pa.hall_id = h.hall_id
					WHERE pa.pil_code = '". $staffinfo['staff_id'] ."' AND pa.type = 'emp'")->fetch(PDO::FETCH_ASSOC);
                        
                        $items['success'] = true;
                        $items['message'] = '';
                        $items['data']['staff'] = getStaffInfo($staffinfo['staff_id']);
                        $items['data']['token'] = newAccessToken($staffinfo['staff_id'], 2);
                        $items['data']['accomo'] = ;
                        
                    } else {
                        
                        $items['success'] = false;
                        if ($lang == 'ar') $items['message'] = 'مشرف غير موجود';
                        else $items['message'] = 'Supervisor not found';
                        
                    }
                    
                } else {
                    
                    $items['success'] = false;
                    if ($lang == 'ar') $items['message'] = 'مشرف غير موجود';
                    else $items['message'] = 'Supervisor not found';
                    
                }
                
            } elseif ($_POST['type'] == 3) {
                
                // Manager
                $sql2 = $db->prepare("SELECT staff_id, staff_password FROM staff WHERE staff_username = :username AND staff_type = 1 AND staff_active = 1 LIMIT 1");
                $sql2->bindValue("username", $data['param1']);
                $sql2->execute();
                
                if ($sql2->rowCount() > 0) {
                    
                    $staffinfo = $sql2->fetch(PDO::FETCH_ASSOC);
                    
                    if (password_verify($data['param2'], $staffinfo['staff_password'])) {
                        $accomo = $db->query("SELECT pa.*, bu.bld_title, f.floor_title, r.room_title, b.bus_title, s.suite_title, h.hall_title, t.tent_title
					FROM pils_accomo pa
					LEFT OUTER JOIN buildings bu ON pa.bld_id = bu.bld_id
					LEFT OUTER JOIN buildings_floors f ON pa.floor_id = f.floor_id
					LEFT OUTER JOIN buildings_rooms r ON pa.room_id = r.room_id
					LEFT OUTER JOIN buses b ON pa.bus_id = b.bus_id
					LEFT OUTER JOIN tents t ON pa.tent_id = t.tent_id
					LEFT OUTER JOIN suites s ON pa.suite_id = s.suite_id
					LEFT OUTER JOIN suites_halls h ON pa.hall_id = h.hall_id
					WHERE pa.pil_code = '". $staffinfo['staff_id'] ."' AND pa.type = 'emp'")->fetch(PDO::FETCH_ASSOC);
                        
                        $items['success'] = true;
                        $items['message'] = '';
                        $items['data']['staff'] = getStaffInfo($staffinfo['staff_id']);
                        $items['data']['token'] = newAccessToken($staffinfo['staff_id'], 3);
                        $items['data']['accomo'] = ;
                        
                    } else {
                        
                        $items['success'] = false;
                        if ($lang == 'ar') $items['message'] = 'مدير غير موجود';
                        else $items['message'] = 'Manager not found';
                        
                    }
                    
                } else {
                    
                    $items['success'] = false;
                    if ($lang == 'ar') $items['message'] = 'مدير غير موجود';
                    else $items['message'] = 'Manager not found';
                    
                }
                
            }
            
        } catch (PDOException $e) {
            
            headerBadRequest();
            $items['success'] = false;
            $items['message'] = SQLError($e);
        }
        
    } else {
        
        headerBadRequest();
        $items['success'] = false;
        if ($lang == 'ar') $items['message'] = 'ERROR 101';
        else $items['message'] = 'ERROR 101';
    }
    
    outputJSON($items);
