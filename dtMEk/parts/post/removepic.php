<?php
    
    global $db;
    
    
    $p_id = $_REQUEST['p_id'];
    $filetitle = $_REQUEST['filetitle'];
    
    if (is_numeric($p_id) && $filetitle) {
        
        // chk if p_id belongs to logged in broker
        $chk = $db->query("SELECT p_id FROM projects WHERE p_id = $p_id AND p_broker_id = " . $_SESSION['userinfo']['user_id'])->fetchColumn();
        if ($chk) {
            
            // Ok grant access
            if (is_file('/assets/media/gallery/' . $p_id . '/' . $filetitle)) {
                
                @unlink('/assets/media/gallery/' . $p_id . '/' . $filetitle);
                
            }
            
        }
        
        
    }
    
    echo '1';
