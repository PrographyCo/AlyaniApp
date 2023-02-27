<?php
    
    $cardurl = 'media/pils_cards/GENERATED_1.pdf';
    
    $command = "wkhtmltopdf -T 0 -B 0 -L 0 -R 0 --page-size A4 --dpi 300 \"http://alyaniapp.com/v2/dtMEk/pil_cards_designs/pils/generated/index.php?city_id=" . $_GET['city_id'] . "&gender=" . $_GET['gender'] . "&pilc_id=" . $_GET['pilc_id'] . "&code=" . $_GET['code'] . "&resno=" . $_GET['resno'] . "&accomo=" . $_GET['accomo'] . "\" /home3/alshamelads/public_html/v2/dtMEk/media/pils_cards/GENERATED_1.pdf";
    $result = exec($command);
    
    $content = file_get_contents($cardurl);
    
    header('Content-Type: application/pdf');
    header('Content-Length: ' . strlen($content));
    header('Content-Disposition: inline; filename="GENERATED_1.pdf"');
    header('Cache-Control: private, max-age=0, must-revalidate');
    header('Pragma: public');
    
    die($content);
