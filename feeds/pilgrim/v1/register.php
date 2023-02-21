<?php
    require '_includes/init.php';
    newlog($_SERVER['REQUEST_URI'], $_GET, $_POST, $_FILES);
    $lang = checkLang();
    
    $data = $_POST;
    $expectedParams[]['string'] = 'firstname';
    $expectedParams[]['string'] = 'lastname';
    $expectedParams[]['string'] = 'email';
    $expectedParams[]['string'] = 'password';
    $expectedParams[]['string'] = 'phone';
    $expectedParams[]['string'] = 'dob';
    $expectedParams[]['string'] = 'fbid';
    
    $requiredParams[]['string'] = 'firstname';
    $requiredParams[]['string'] = 'lastname';
    $requiredParams[]['string'] = 'email';
    $requiredParams[]['string'] = 'password';
    $requiredParams[]['string'] = 'phone';
    $requiredParams[]['string'] = 'dob';
    
    if (checkParams($data, $expectedParams) && missingValues($data, $requiredParams)) {
        
        $chk1 = $db->prepare("SELECT user_id FROM users WHERE user_email = ?");
        $chk1->execute(array($data['email']));
        
        if ($chk1->rowCount() > 0) {
            
            $items['success'] = false;
            if ($lang == 'ar') $items['message'] = 'البريد الالكتروني موجود بالفعل';
            else $items['message'] = 'Email already exists';
            outputJSON($items);
        }
        
        // All validations passed, lets create user
        try {
            
            $db->beginTransaction();
            $sql = $db->prepare("INSERT INTO users VALUES (
    		'',
    		:user_firstname,
				:user_lastname,
				:user_phone,
    		:user_email,
    		:user_password,
    		:user_dob,
				:user_fbid,
				1,
    		:user_regdate,
    		:user_lastactive
    		)");
            
            $sql->bindValue("user_firstname", $data['firstname']);
            $sql->bindValue("user_lastname", $data['lastname']);
            $sql->bindValue("user_phone", $data['phone']);
            $sql->bindValue("user_email", $data['email']);
            $sql->bindValue("user_password", password_hash($data['password'], PASSWORD_DEFAULT));
            $sql->bindValue("user_dob", date("Y-m-d", strtotime($data['dob'])));
            $sql->bindValue("user_fbid", $data['fbid']);
            $sql->bindValue("user_regdate", time());
            $sql->bindValue("user_lastactive", time());
            
            if ($sql->execute()) {
                
                $user_id = $db->lastInsertId();
                $items['success'] = true;
                $items['data']['user'] = getUserInfo($user_id);
                $items['data']['token'] = newAccessToken($user_id);
                
                if ($lang == 'ar') $items['message'] = 'تم التسجيل بنجاح';
                else $items['message'] = 'Registration successful';
                
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
        outputJSON($items);
    }
