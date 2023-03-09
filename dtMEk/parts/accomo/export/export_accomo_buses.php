<?php
    $cardurl = $root_absolute_path . trim(CP_PATH."/media/accomo_PDF/ACCOMO_4.pdf",'/');
    
    $command = $command = "wkhtmltopdf -T 0 -B 0 -L 0 -R 0 --page-size A4 --dpi 300 \"";
    $command .= (($_SERVER['SERVER_PORT']==443)?'https':'http')."://".str_replace(['http','https'],'',$_SERVER['HTTP_HOST']).CP_PATH."/accomo/export/export_accomo_buses_html?bus_id=" . $_GET['bus_id'] . "&city_id=" . $_GET['city_id'] . "\" ";
    $command .= $cardurl;

    $result = exec($command);
    
    $content = file_get_contents($cardurl);
    
    header('Content-Type: application/pdf');
    header('Content-Length: ' . strlen($content));
    header('Content-Disposition: inline; filename="ACCOMO_4.pdf"');
    header('Cache-Control: private, max-age=0, must-revalidate');
    header('Pragma: public');
    
    die($content);
