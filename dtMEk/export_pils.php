<?php
    include 'XLSClasses/PHPExcel/IOFactory.php';
    require 'db.php';
    
    $objPHPExcel = new PHPExcel();
    $objPHPExcel->getActiveSheet()->setRightToLeft(true);
    $objPHPExcel->getProperties()
        ->setCreator("Alyani")
        ->setLastModifiedBy("Alyani")
        ->setTitle("Alyani Pilgrims")
        ->setSubject("Alyani Pilgrims")
        ->setDescription("Alyani Pilgrims")
        ->setKeywords("Alyani Pilgrims");
    $objPHPExcel->setActiveSheetIndex(0);
    
    $objPHPExcel->getActiveSheet()->SetCellValue('A1', "رقم الهوية");
    $objPHPExcel->getActiveSheet()->SetCellValue('B1', "اسم الحاج");
    $objPHPExcel->getActiveSheet()->SetCellValue('C1', "الجنسية");
    $objPHPExcel->getActiveSheet()->SetCellValue('D1', "الجنس");
    $objPHPExcel->getActiveSheet()->SetCellValue('E1', "رقم الجوال");
    $objPHPExcel->getActiveSheet()->SetCellValue('F1', "رقم الحجز");
    $objPHPExcel->getActiveSheet()->SetCellValue('G1', "المدينة");
    $objPHPExcel->getActiveSheet()->SetCellValue('H1', "الفئة");
    $objPHPExcel->getActiveSheet()->SetCellValue('I1', "الكود");
    
    $cnt = 2;
    
    if (is_numeric($_GET['city_id']) && $_GET['city_id'] > 0) $sqlmore1 = " AND pil_city_id = " . $_GET['city_id'];
    if ($_GET['gender'] == 'm' || $_GET['gender'] == 'f') $sqlmore2 = " AND pil_gender = '" . $_GET['gender'] . "'";
    if (is_numeric($_GET['pilc_id']) && $_GET['pilc_id'] > 0) $sqlmore3 = " AND pil_pilc_id = " . $_GET['pilc_id'];
    if ($_GET['code']) $sqlmore4 = " AND pil_code LIKE '%" . $_GET['code'] . "%'";
    if ($_GET['resno']) $sqlmore5 = " AND pil_reservation_number LIKE '%" . $_GET['resno'] . "%'";
    if (is_numeric($_GET['accomo']) && $_GET['accomo'] == 1) $sqlmore6 = " AND pil_code IN (SELECT pil_code FROM pils_accomo)";
    if (is_numeric($_GET['accomo']) && $_GET['accomo'] == 2) $sqlmore6 = " AND pil_code NOT IN (SELECT pil_code FROM pils_accomo)";
    
    
    $sql = $db->query("SELECT p.*, pilc.*, c.country_title_ar, ci.city_title_ar FROM pils p
  LEFT OUTER JOIN pils_classes pilc ON p.pil_pilc_id = pilc.pilc_id
  LEFT OUTER JOIN countries c ON p.pil_country_id = c.country_id
  LEFT OUTER JOIN cities ci ON p.pil_city_id = ci.city_id
  WHERE 1 $sqlmore1 $sqlmore2 $sqlmore3 $sqlmore4 $sqlmore5 $sqlmore6
  ORDER BY p.pil_name");
    while ($pilinfo = $sql->fetch(PDO::FETCH_ASSOC)) {
        
        if ($pilinfo['pil_gender'] == 'm') $gender = 'ذكر';
        else $gender = 'أنثى';
        
        $objPHPExcel->getActiveSheet()->setCellValueExplicit('A' . $cnt, $pilinfo['pil_nationalid'], PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit('B' . $cnt, $pilinfo['pil_name'], PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit('C' . $cnt, $pilinfo['country_title_ar'], PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit('D' . $cnt, $gender, PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit('E' . $cnt, $pilinfo['pil_phone'], PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit('F' . $cnt, $pilinfo['pil_reservation_number'], PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit('G' . $cnt, $pilinfo['city_title_ar'], PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit('H' . $cnt, $pilinfo['pilc_title_ar'], PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit('I' . $cnt, $pilinfo['pil_code'], PHPExcel_Cell_DataType::TYPE_STRING);
        $cnt++;
    }
    
    $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="Alyani-Pilgrims.xlsx"');
    header('Cache-Control: max-age=0');
    $writer->save('php://output');
