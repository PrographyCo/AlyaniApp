<?php
  global $db;

  $suites = $_REQUEST['suites']??[];
  $selectedhalls = $_REQUEST['selectedhalls']??die(400);
  $extratype_id = $_REQUEST['extratype_id']??die(400);
  $selected = $_REQUEST['selected']??[];

  $output['html'] = '';
  if (is_array($suites) && count($suites)>0) {
    $sql = "SELECT stuff_id, stuff_title FROM suites_halls_stuff WHERE hall_id IN (
                ".implode(',', $selectedhalls)."
            ) AND stuff_type = '". ( match ($extratype_id) {
            "1",1 => 'chair',
            "2",2 => 'bench',
            "3",3 => 'bed',
        } )."' AND stuff_id NOT IN (SELECT stuff_id FROM pils_accomo WHERE stuff_id != 0 AND stuff_id NOT IN ('". implode(',',$selected) ."'))";

    $sqlstuffs = $db->query($sql);
    
    $output['html'] .= '<select name="stuff_ids[]" id="stuff_ids[]" class="form-control select2" multiple onchange="calcAvailAccomo();">';
    $output['html'] .= '<option value="" readonly disabled>'. LBL_Choose .'</option>';
    
    while ($row = $sqlstuffs->fetch(PDO::FETCH_ASSOC))
    {
        $output['html'] .= '<option value="'.$row['stuff_id'].'" '. ((in_array($row['stuff_id'],$selected))?'selected=selected':'') .'>'.$row['stuff_title'].'</option>';
    }
    
    $output['html'] .= '</select>';

  }
header('Content-Type: application/json');
  echo json_encode($output);
