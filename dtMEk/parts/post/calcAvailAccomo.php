<?php
  global $db;

    $suites = $_REQUEST['suites'] ?? '';
    $halls = $_REQUEST['halls'] ?? '';
    $bld_type = $_REQUEST['bld_type'] ?? '';
    $buildings = $_REQUEST['buildings'] ?? '';
    $floors = $_REQUEST['floors'] ?? '';
    $rooms = $_REQUEST['rooms'] ?? '';
    $tents = $_REQUEST['tents'] ?? '';
    $gender = $_REQUEST['gender'] ?? '';
    $halls_arfa = $_REQUEST['halls_arfa'] ?? '';
    $extratype_id = $_REQUEST['extratype_id'] ?? 0;
    $output['availcount'] = AvailableToAccomo($suites, $halls, $bld_type, $buildings, $floors, $rooms, $tents, $gender , $halls_arfa, $extratype_id);

  echo json_encode($output);
