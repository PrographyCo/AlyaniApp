<?php
    global $db;
    
    $bld_type = $_REQUEST['bld_type'];
    $gender = $_REQUEST['gender'];
    
    if (is_numeric($bld_type) && $bld_type > 0) {
        
        if ($bld_type == 1) $placeholder = LBL_ChooseBuildings;
        elseif ($bld_type == 2) $placeholder = LBL_ChoosePremises;
        
        echo '<select name="building_id[]" id="building_id[]" class="form-control select2" multiple="multiple" data-placeholder="' . $placeholder . '" onchange="bld_selected(); calcAvailAccomo();">';
        if ($gender) $sqlblds = $db->query("SELECT * FROM buildings WHERE bld_active = 1 AND bld_type = $bld_type AND bld_id IN (SELECT room_bld_id FROM buildings_rooms WHERE room_gender = '$gender') ORDER BY bld_title");
        else $sqlblds = $db->query("SELECT * FROM buildings WHERE bld_active = 1 AND bld_type = $bld_type AND bld_id IN (SELECT room_bld_id FROM buildings_rooms WHERE room_gender = '$gender') ORDER BY bld_title");
        while ($rowb = $sqlblds->fetch(PDO::FETCH_ASSOC)) {
            echo '<option value="' . $rowb['bld_id'] . '" ';
            echo '>
          ' . $rowb['bld_title'] . '
          </option>';
        }
        echo '</select>
    <div id="floorsarea">

    </div>';
    
    }
