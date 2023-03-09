<?php
  global $db;

  $suites = $_REQUEST['suites']??[];
  $selected = $_REQUEST['selected']??[];

  $output['html'] = '';
  if (is_array($suites) && count($suites)>0) {

    $sqlhalls = $db->query("SELECT hall_id, hall_title FROM suites_halls WHERE hall_suite_id IN (".implode(',', $suites).")");
    $output['html'] .= '<select name="hall_id[]" id="hall_id[]" class="form-control select2" multiple="multiple" data-placeholder="'.LBL_ChooseHalls.'" onchange="calcAvailAccomo();">';
    while ($row1 = $sqlhalls->fetch(PDO::FETCH_ASSOC)) {

      $output['html'] .= '<option value="'.$row1['hall_id'].'" ';
      if (is_array($selected) && count($selected) > 0 && in_array($row1['hall_id'], $selected)) $output['html'] .= 'selected="selected"';
      $output['html'] .= '>
      '.$row1['hall_title'].'
      </option>';

    }
    $output['html'] .= '</select>';
    $output['html'] .= '<select name="extratype_id" id="extratype_id" class="form-control select2">
      <option value="0">
        '.LBL_Choose.'
      </option>
      <option value="1">
        '.LBL_Chair1.'
      </option>
      <option value="2">
        '.LBL_Chair2.'
      </option>
      <option value="3">
        '.LBL_Bed.'
      </option>
    </select>';

  }

  echo json_encode($output);
