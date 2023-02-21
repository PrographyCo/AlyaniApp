<?php
    require '_includes/init.php';
    newlog($_SERVER['REQUEST_URI'], $_GET, $_POST, $_FILES);
    $lang = checkLang();
    
    $data = $_POST;
    
    if ($data['email']) {
        
        $sql1 = $db->prepare("SELECT user_id, user_email FROM users WHERE user_email = :email LIMIT 1");
        $sql1->bindValue("email", $data['email']);
        $sql1->execute();
        
        if ($sql1->rowCount() > 0) {
            
            $userinfo = $sql1->fetch(PDO::FETCH_ASSOC);
            
            $token = rand_string(40);
            $sqlins = $db->query("INSERT INTO users_forgot_tokens VALUES (" . $userinfo['user_id'] . ", '" . $token . "', " . time() . ") ON DUPLICATE KEY UPDATE token = '" . $token . "', dateadded = " . time());
            // send email reset instructions to player email
            
            $emailto = $userinfo['user_email'];
            $subject = "Sooms App | Reset your password";
            $body = "Hello,<br /><br />"
                . "You have requested to reset your password on Sooms App, please follow the below link to reset your password:<br /><br />"
                . "<a href=\"https://www.soomsapp.com/users_reset.php?token=" . $token . "\">https://www.soomsapp.com/users_reset.php?token=" . $token . "</a>"
                . "<br /><br />"
                . "- Sooms App Team";
            
            $headers = "From: Sooms App <no-reply@soomsapp.com>\r\n" .
                "Reply-To: info@soomsapp.com\r\n" .
                'Content-Type: text/html; charset=UTF-8' . "\r\n" .
                'X-Mailer: PHP/' . phpversion();
            
            @mail($emailto, $subject, $body, $headers);
            
            
            $items['success'] = true;
            if ($lang == 'ar') $items['message'] = 'تم إرسال رسالة إلى البريد الالكتروني لكيفية تغيير كلمة السر';
            else $items['message'] = 'Password reset instructions have been sent to your email';
            headerSuccess();
            
        } else {
            
            $items['success'] = false;
            if ($lang == 'ar') $items['message'] = 'البريد الالكتروني   غير مسجل';
            else $items['message'] = 'Email not found';
            headerSuccess();
            
        }
        
    } else {
        
        $items['success'] = false;
        if ($lang == 'ar') $items['message'] = 'البريد الالكتروني غير موجود';
        else $items['message'] = 'Missing email';
        headerBadRequest();
    }
    
    outputJSON($items);
