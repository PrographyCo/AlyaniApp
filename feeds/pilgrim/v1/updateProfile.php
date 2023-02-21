<?php
    require '_includes/init.php';
    newlog($_SERVER['REQUEST_URI'], $_GET, $_POST, $_FILES);
    $userinfo = checkToken();
    $lang = checkLang();
    
    $data = $_POST;
    $expectedParams[]['string'] = 'firstname';
    $expectedParams[]['string'] = 'lastname';
    $expectedParams[]['string'] = 'newpassword';
    $expectedParams[]['string'] = 'phone';
    $expectedParams[]['string'] = 'dob';
    
    $requiredParams[]['string'] = 'firstname';
    $requiredParams[]['string'] = 'lastname';
    $requiredParams[]['string'] = 'phone';
    $requiredParams[]['string'] = 'dob';
    
    if (checkParams($data, $expectedParams) && missingValues($data, $requiredParams)) {
        
        $chk1 = $db->prepare("SELECT user_id FROM users WHERE user_email = ? AND user_id != ?");
        $chk1->execute(array($data['email'], $userinfo['user_id']));
        
        if ($chk1->rowCount() > 0) {
            
            $items['success'] = false;
            if ($lang == 'ar') $items['message'] = 'البريد الالكتروني موجود لحساب آخر';
            else $items['message'] = 'Email exists to another account';
            outputJSON($items);
        }
        
        // All validations passed, lets create user
        try {
            
            $db->beginTransaction();
            $sql = $db->prepare("UPDATE users SET
    		user_firstname = :user_firstname,
				user_lastname = :user_lastname,
				user_phone = :user_phone,
    		user_dob = :user_dob

				WHERE user_id = :user_id
    		");
            
            $sql->bindValue("user_firstname", $data['firstname']);
            $sql->bindValue("user_lastname", $data['lastname']);
            $sql->bindValue("user_phone", $data['phone']);
            $sql->bindValue("user_dob", date("Y-m-d", strtotime($data['dob'])));
            $sql->bindValue("user_id", $userinfo['user_id']);
            if ($sql->execute()) {
                
                // Check if newpassword is sent
                if (!empty($data['newpassword'])) {
                    
                    $sqlnewpass = $db->prepare("UPDATE users SET user_password = :password WHERE user_id = :user_id");
                    $sqlnewpass->bindValue("password", password_hash($data['newpassword'], PASSWORD_DEFAULT));
                    $sqlnewpass->bindValue("user_id", $userinfo['user_id']);
                    $sqlnewpass->execute();
                    
                }
                
                $items['success'] = true;
                $items['data']['user'] = getUserInfo($userinfo['user_id']);
                
                if ($lang == 'ar') $items['message'] = 'تم تعديل البيانات بنجاح';
                else $items['message'] = 'Profile updated successfully';
                
                headerSuccess();
                $db->commit();
                outputJSON($items);
                
            } else {
                
                $items['success'] = false;
                $items['message'] = 'Error - Error Inserting Record';
                $db->commit();
                outputJSON($items);
            }
            
        } catch (PDOException $e) {
            
            $db->rollBack();
            headerBadRequest();
            $items['message'] = SQLError($e);
            
        }
        
    } else {
        
        if ($lang == 'ar') $items['message'] = 'ERROR 101';
        else $items['message'] = 'ERROR 101';
        headerBadRequest();
        
    }
    
    outputJSON($items);
