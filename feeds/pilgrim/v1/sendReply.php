<?php
    require '_includes/init.php';
    newlog($_SERVER['REQUEST_URI'], $_GET, $_POST, $_FILES);
    $pilinfo = checkToken(3);
    $lang = checkLang();
    
    $data = $_POST;
    $expectedParams[]['integer'] = 'feedback_id';
    $expectedParams[]['string'] = 'reply';
    
    $requiredParams[]['integer'] = 'feedback_id';
    $requiredParams[]['string'] = 'reply';
    
    if (checkParams($data, $expectedParams) && missingValues($data, $requiredParams)) {
        
        try {
            
            $settings_fromname = $db->query("SELECT s_value FROM settings WHERE s_id = 2")->fetchColumn();
            $settings_fromemail = $db->query("SELECT s_value FROM settings WHERE s_id = 3")->fetchColumn();
            
            $feedbinfo = $db->query("SELECT * FROM feedback WHERE feedb_id = " . $data['feedback_id'])->fetch(PDO::FETCH_ASSOC);
            
            $originalMessage = 'Name<br /><b>' . $feedbinfo['feedb_name'] . '</b><br /><br />';
            $originalMessage .= 'Phone<br /><b>' . $feedbinfo['feedb_phone'] . '</b><br /><br />';
            $originalMessage .= 'Email<br /><b>' . $feedbinfo['feedb_email'] . '</b><br /><br />';
            $originalMessage .= 'Message<br /><b>' . nl2br($feedbinfo['feedb_message']) . '</b><br /><br />';
            
            $emailto = $feedbinfo['feedb_email'];
            $subject = "A new reply from " . $settings_fromname;
            $body = nl2br($data['reply']) . "<br /><br />In reply to:<br /><br />" . $originalMessage;
            
            $headers = "From: " . $settings_fromname . " <$settings_fromemail>\r\n" .
                "Reply-To: Kottouf <$settings_fromemail>\r\n" .
                'Content-Type: text/html; charset=UTF-8' . "\r\n" .
                'X-Mailer: PHP/' . phpversion();
            
            if (mail($emailto, $subject, $body, $headers)) {
                
                $items['success'] = true;
                if ($lang == 'ar') $items['message'] = 'تم إرسال الرد بنجاح';
                else $items['message'] = 'Reply sent successfully';
                
            } else {
                
                $items['success'] = false;
                if ($lang == 'ar') $items['message'] = 'خطأ في إرسال الرد';
                else $items['message'] = 'Error sending reply';
                
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
