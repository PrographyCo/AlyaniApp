<?php
    require 'config/db.php';
    
    $cardurl = 'media/accomo_PDF/ACCOMO_1.pdf';
    
    $command = "wkhtmltopdf -T 10 -B 10 -L 10 -R 10 --page-size A4 --dpi 300 \"http://alyaniapp.com/v2/dtMEk/export_accomo_suites_html.php?suite_id=" . $_GET['suite_id'] . "&hall_id=" . $_GET['hall_id'] . "\" /home1/atipg2142020/public_html/v2/dtMEk/media/accomo_PDF/ACCOMO_1.pdf";
    $result = exec($command);
    
    $content = file_get_contents($cardurl);
    
    header('Content-Type: application/pdf');
    header('Content-Length: ' . strlen($content));
    header('Content-Disposition: inline; filename="ACCOMO_1.pdf"');
    header('Cache-Control: private, max-age=0, must-revalidate');
    header('Pragma: public');
    
    die($content);
