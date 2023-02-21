<?php
    require '_includes/init.php';
    newlog($_SERVER['REQUEST_URI'], $_GET, $_POST, $_FILES);
    $pilinfo = checkToken();
    $lang = checkLang();
    
    $data = $_POST;
    
    try {
        
        if (is_numeric($data['pil_id']) && $data['pil_id'] > 0) $pilinfo['pil_id'] = $data['pil_id'];
        
        if ($pilinfo['pil_id'] > 0) {
            
            $items['success'] = true;
            $items['message'] = '';
            $items['data']['pilinfo'] = getPilInfoExtended($pilinfo, $lang);
            
        } else {
            
            $items['success'] = false;
            $items['message'] = 'No pilgrims found';
            
        }
        
    } catch (PDOException $e) {
        
        headerBadRequest();
        $items['success'] = false;
        $items['message'] = SQLError($e);
    }
    
    outputJSON($items);
