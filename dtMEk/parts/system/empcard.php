<?php

    if (!is_numeric($_GET['id'])) die();
    $id = $_GET['id'];
    
    $cardurl = $root_absolute_path . trim(CP_PATH.'/assets/media/pils_cards/EMP_' . $id . '_CARD.pdf','/');
    
    $command = "wkhtmltopdf -T 0 -B 0 -L 0 -R 0 --page-size A4 --dpi 300 ";
    $command .= (($_SERVER['SERVER_PORT']==443)?'https':'http')."://".str_replace(['http','https'],'',$_SERVER['HTTP_HOST']).CP_PATH."/cards/managers/common/index?id=$id ";
    $command .= $cardurl;
    
    $result = shell_exec($command);
    
    $content = file_get_contents($cardurl);
    
    header('Content-Type: application/pdf');
    header('Content-Length: ' . strlen($content));
    header('Content-Disposition: inline; filename="EMP_' . $id . '_CARD.pdf"');
    header('Cache-Control: private, max-age=0, must-revalidate');
    header('Pragma: public');
    ini_set('zlib.output_compression', '0');
    
    die($content);
