<?php
    
    $cardurl = $root_absolute_path . trim(CP_PATH."/media/accomo_PDF/ACCOMO_pil.pdf",'/');
    
    $command = "wkhtmltopdf -T 10 -B 10 -L 10 -R 10 --page-size A4 --dpi 300 \"".$_SERVER['HTTP_HOST'].CP_PATH."/pilgrims/actions/export_accomo_pils_html\" ".$cardurl;
    $result = exec($command);
    
    $content = file_get_contents($cardurl);
    
    header('Content-Type: application/pdf');
    header('Content-Length: ' . strlen($content));
    header('Content-Disposition: inline; filename="ACCOMO_pils.pdf"');
    header('Cache-Control: private, max-age=0, must-revalidate');
    header('Pragma: public');
    
    die($content);
