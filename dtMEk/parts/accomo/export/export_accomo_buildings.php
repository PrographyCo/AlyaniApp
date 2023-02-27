<?php
    
    $cardurl = ASSETS_PATH.'media/accomo_PDF/ACCOMO_2.pdf';
    
    $command = "wkhtmltopdf -T 10 -B 10 -L 10 -R 10 --page-size A4 --dpi 300 \"".$_SERVER['HTTP_HOST'].CP_PATH."/accomo/export/export_accomo_buildings_html?building_id=" . $_GET['building_id'] . "&floor_id=" . $_GET['floor_id'] . "\" /home1/atipg2142020/public_html/v2/dtMEk/media/accomo_PDF/ACCOMO_2.pdf";
    $result = exec($command);
    
    $content = file_get_contents($cardurl);
    
    header('Content-Type: application/pdf');
    header('Content-Length: ' . strlen($content));
    header('Content-Disposition: inline; filename="ACCOMO_2.pdf"');
    header('Cache-Control: private, max-age=0, must-revalidate');
    header('Pragma: public');
    
    die($content);
