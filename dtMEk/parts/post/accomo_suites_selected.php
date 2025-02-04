<?php
  global $db;

  $suites = $_REQUEST['suites']??[];
  $selected = $_REQUEST['selected']??[];
  $extratype_id = $_REQUEST['extratype_id']??'';

  $output['html'] = '';
  if (is_array($suites) && count($suites)>0) {

    $sqlhalls = $db->query("SELECT hall_id, hall_title FROM suites_halls WHERE hall_suite_id IN (".implode(',', $suites).")");
    $output['html'] .= '<select name="hall_id[]" id="hall_id[]" class="form-control select2" multiple="multiple" data-placeholder="'.LBL_ChooseHalls.'" onchange="calcAvailAccomo();hall_selected();">';
    while ($row1 = $sqlhalls->fetch(PDO::FETCH_ASSOC)) {

      $output['html'] .= '<option value="'.$row1['hall_id'].'" ';
      if (is_array($selected) && count($selected) > 0 && in_array($row1['hall_id'], $selected)) $output['html'] .= 'selected="selected"';
      $output['html'] .= '>
      '.$row1['hall_title'].'
      </option>';

    }
    $output['html'] .= '</select>';
    $output['html'] .= '<select name="extratype_id" id="extratype_id" class="form-control select2" onchange="extratype_selected(); calcAvailAccomo();">
      <option value="0" '. (($extratype_id=="0")?'selected=selected':'') .'>
        '.LBL_Choose.'
      </option>
      <option value="1" '. (($extratype_id=="1")?'selected=selected':'') .'>
        '.LBL_Chair1.'
      </option>
      <option value="2" '. (($extratype_id=="2")?'selected=selected':'') .'>
        '.LBL_Chair2.'
      </option>
      <option value="3" '. (($extratype_id=="3")?'selected=selected':'') .'>
        '.LBL_Bed.'
      </option>
    </select>
    
    <div id="type_select"></div>';

  }

  echo json_encode($output);
