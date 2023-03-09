<?php
    require '_includes/init.php';
    newlog($_SERVER['REQUEST_URI'], $_GET, $_POST, $_FILES);
    $pilinfo = checkIfToken();
    $lang = checkLang();
    
    $data = $_POST;
    requiredInputs(['device_uuid','platform']);
    
    if ($pilinfo['pil_id'] > 0) {
        
        // this is a pilgrim, lets get the last noti_id and insert in notifications_read table
        $lastnoti_id = $db->query("SELECT noti_id FROM notifications WHERE noti_user_type = 1 AND noti_user_id = " . $pilinfo['pil_id'] . " ORDER BY noti_id DESC LIMIT 1")->fetchColumn() ?: 0;
        $upd = $db->query("INSERT INTO notifications_read VALUES (1, " . $pilinfo['pil_id'] . ", $lastnoti_id) ON DUPLICATE KEY UPDATE noti_id = $lastnoti_id");
        
    } elseif ($pilinfo['staff_id'] > 0) {
        
        // this is a staff, lets get the last noti_id and insert in notifications_read table
        $lastnoti_id = $db->query("SELECT noti_id FROM notifications WHERE noti_user_type = 2 AND noti_user_id = " . $pilinfo['staff_id'] . " ORDER BY noti_id DESC LIMIT 1")->fetchColumn() ?: 0;
        $upd = $db->query("INSERT INTO notifications_read VALUES (2, " . $pilinfo['staff_id'] . ", $lastnoti_id) ON DUPLICATE KEY UPDATE noti_id = $lastnoti_id");
        
    } else {
        
        // this is a guest, lets get the last noti_id and insert in notifications_read_guest table
        $lastnoti_id = $db->query("SELECT noti_id FROM notifications WHERE noti_user_type = 0 AND noti_user_id = 0 ORDER BY noti_id DESC LIMIT 1")->fetchColumn() ?: 0;
        $upd = $db->query("INSERT INTO notifications_read_guest VALUES ('" . $data['device_uuid'] . "', " . $data['platform'] . ", $lastnoti_id) ON DUPLICATE KEY UPDATE noti_id = $lastnoti_id");
        
    }
    
    $items['success'] = true;
    $items['message'] = 'updated';
    
    outputJSON($items);
