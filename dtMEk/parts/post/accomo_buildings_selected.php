<?php
    global $db;
    
    $blds = $_REQUEST['blds'];
    $gender = $_REQUEST['gender'];
    
    if (is_array($blds) && count($blds)) {
        
        echo '<select name="floor_id[]" id="floor_id[]" class="form-control select2" multiple="multiple" data-placeholder="' . LBL_ChooseFloors . '" onchange="floors_selected(); calcAvailAccomo();">';
        if ($gender) $sqlfloors = $db->query("SELECT * FROM buildings_floors WHERE floor_active = 1 AND floor_bld_id IN (" . implode(",", $blds) . ") AND floor_id IN (SELECT room_floor_id FROM buildings_rooms WHERE room_gender = '$gender') ORDER BY floor_title");
        else $sqlfloors = $db->query("SELECT * FROM buildings_floors WHERE floor_active = 1 AND floor_bld_id IN (" . implode(",", $blds) . ") ORDER BY floor_title");
        
        while ($rowf = $sqlfloors->fetch(PDO::FETCH_ASSOC)) {
            echo '<option value="' . $rowf['floor_id'] . '" ';
            echo '>
          ' . $rowf['floor_title'] . '
          </option>';
        }
        echo '</select>
    <div id="roomsarea">

    </div>';
    
    }
?>
