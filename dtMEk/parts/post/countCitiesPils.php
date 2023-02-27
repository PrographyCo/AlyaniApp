<?php
    global $db;
    $cities = $_REQUEST['cities'];
    $gender = $_REQUEST['gender'];
    $pilc_id = $_REQUEST['pilc_id'];
    $type = $_REQUEST['type'];
    $sqlmore1 = $sqlmore2 = '';
    
    $extra = $type == 'arafa' ? 'halls_id = 0' : 'suite_id =0 AND hall_id =0 AND bld_id =0 AND floor_id =0 AND room_id =0 AND tent_id =0';
    
    if (is_array($cities) && count($cities)) {
        
        if ($gender) $sqlmore1 = " AND pil_gender = '$gender'";
        if ($pilc_id > 0) $sqlmore2 = " AND pil_pilc_id = $pilc_id";
        
        $countpils = $db->query("SELECT COUNT(pil_id) FROM pils WHERE pil_active = 1 AND pil_city_id IN (" . implode(',', $cities) . ") AND (  ( pil_code NOT IN (SELECT pil_code FROM pils_accomo))  OR (pil_code  IN (SELECT pil_code FROM pils_accomo WHERE $extra))) $sqlmore1 $sqlmore2 ORDER BY pil_reservation_number")->fetchColumn();
        if ($countpils > 0) {
            echo '<b>' . $countpils . '</b>';
        } else {
            echo '0';
        }
        
    } else {
        
        echo '0';
        
    }

?>
