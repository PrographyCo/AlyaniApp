<?php
    require '_includes/init.php';
    newlog($_SERVER['REQUEST_URI'], $_GET, $_POST, $_FILES);
    $pilinfo = checkToken();
    $lang = checkLang();
    
    $data = $_POST;
    $expectedParams[]['integer'] = 'code';
    
    $requiredParams[]['integer'] = 'code';
    
    if (checkParams($data, $expectedParams) && missingValues($data, $requiredParams)) {
        
        try {
            
            if (checkCodePil($pilinfo['pil_id'], $data['code'])) {
                
                $items['success'] = true;
                $items['message'] = '';
                $items['data']['pilinfo']['pil_verified'] = 1;
                $items['data']['pilgrim'] = getPilInfo($pilinfo['pil_id']);
                
                
            } else {
                
                $items['success'] = false;
                $items['message'] = '';
                $items['data']['pilinfo']['pil_verified'] = 0;
                
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
