<?php
    
    global $db;
    
    $gender = $_POST['gender'];
    $type = $_POST['type'] ?? 1;
    
    $items['SuitesArea'] = '';
    $items['BuildingsTypeArea'] = '';
    $items['TentsArea'] = '';
    
    
    $items['SuitesArea'] = '<select name="suite_id[]" id="suite_id[]" class="form-control select2" multiple="multiple" onchange="suites_selected(); calcAvailAccomo();">';
    if ($gender) $sqlsuites = $db->query("SELECT * FROM suites WHERE suite_active = 1 AND suite_gender = '$gender' ORDER BY suite_title");
    else $sqlsuites = $db->query("SELECT * FROM suites WHERE suite_active = 1 ORDER BY suite_title");
    
    while ($rows = $sqlsuites->fetch(PDO::FETCH_ASSOC)) {
        $items['SuitesArea'] .= '<option value="' . $rows['suite_id'] . '" ';
        $items['SuitesArea'] .= '>
			' . $rows['suite_title'] . '
			</option>';
    }
    
    $items['SuitesArea'] .= '</select>
	<div id="hallsarea">

	</div>';
    
    
    $items['BuildingsTypeArea'] = '<select name="bld_type" id="bld_type" class="form-control select2" onchange="bldtype_selected(this.value); calcAvailAccomo();">';
    $items['BuildingsTypeArea'] .= '<option value="0">
			' . LBL_All . '
		</option>
		<option value="1">
			' . HM_Building . '
		</option>
		<option value="2">
			' . LBL_Premises . '
		</option>
	</select>

	<div id="buildingsarea">

	</div>';
    
    
    if ($type == 1) {
        
        $items['TentsArea'] = '<select name="tent_id[]" id="tent_id[]" class="form-control select2" multiple="multiple" onchange="calcAvailAccomo();">';
        if ($gender) $sqltents = $db->query("SELECT * FROM tents WHERE tent_active = 1 AND tent_gender = '$gender' AND type = 1 ORDER BY tent_title");
        else $sqltents = $db->query("SELECT * FROM tents WHERE tent_active = 1  AND type = 1 ORDER BY tent_title");
        while ($rowt = $sqltents->fetch(PDO::FETCH_ASSOC)) {
            $items['TentsArea'] .= '<option value="' . $rowt['tent_id'] . '" ';
            $items['TentsArea'] .= '>
    				' . $rowt['tent_title'] . '
    				</option>';
        }
        
        
        $items['TentsArea'] .= '</select>';
        
    } elseif ($type == 2) {
        $items['TentsArea'] = '<select name="hallsarfa_id[]" id="halls_arfa[]" class="form-control select2" multiple="multiple" onchange="calcAvailAccomo();">';
        if ($gender) $sqltents = $db->query("SELECT * FROM tents WHERE tent_active = 1 AND tent_gender = '$gender' AND type = 2 ORDER BY tent_title");
        else $sqltents = $db->query("SELECT * FROM tents WHERE tent_active = 1  AND type = 2 ORDER BY tent_title");
        while ($rowt = $sqltents->fetch(PDO::FETCH_ASSOC)) {
            $items['TentsArea'] .= '<option value="' . $rowt['tent_id'] . '" ';
            $items['TentsArea'] .= '>
    				' . $rowt['tent_title'] . '
    				</option>';
        }
        
        
        $items['TentsArea'] .= '</select>';
        
    }
    
    
    echo json_encode($items, JSON_UNESCAPED_UNICODE);
