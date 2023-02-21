<?php
    require '_includes/init.php';
    newlog($_SERVER['REQUEST_URI'], $_GET, $_POST, $_FILES);
    $lang = checkLang();
    
    $data = $_POST;
    $expectedParams[]['integer'] = 'type';
    $expectedParams[]['string'] = 'name';
    $expectedParams[]['string'] = 'phone';
    $expectedParams[]['string'] = 'email';
    $expectedParams[]['string'] = 'message';
    
    $requiredParams[]['integer'] = 'type';
    $requiredParams[]['string'] = 'name';
    $requiredParams[]['string'] = 'phone';
    $requiredParams[]['string'] = 'email';
    $requiredParams[]['string'] = 'message';
    
    if (checkParams($data, $expectedParams) && missingValues($data, $requiredParams)) {
        
        try {
            
            $sql = $db->prepare("INSERT INTO feedback VALUES (
				'',
				:type,
				:name,
				:phone,
				:email,
				:message,
				:dateadded
			)");
            
            $sql->bindValue("type", $data['type']);
            $sql->bindValue("name", $data['name']);
            $sql->bindValue("phone", $data['phone']);
            $sql->bindValue("email", $data['email']);
            $sql->bindValue("message", $data['message']);
            $sql->bindValue("dateadded", time());
            $sql->execute();
            
            $items['success'] = true;
            $items['message'] = '';
            
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
