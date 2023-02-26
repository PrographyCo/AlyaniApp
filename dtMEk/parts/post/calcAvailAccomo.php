<?php
  global $db;

  $suites = $_POST['suites'] ?: $_GET['suites'];
  $halls = $_POST['halls'] ?: $_GET['halls'];
  $bld_type = $_POST['bld_type'] ?: $_GET['bld_type'];
  $buildings = $_POST['buildings'] ?: $_GET['buildings'];
  $floors = $_POST['floors'] ?: $_GET['floors'];
  $rooms = $_POST['rooms'] ?: $_GET['rooms'];
  $tents = $_POST['tents'] ?: $_GET['tents'];
  $gender = $_POST['gender'] ?: $_GET['gender'];
    $halls_arfa = $_POST['halls_arfa'] ?: $_GET['halls_arfa'];
  $output['availcount'] = AvailableToAccomo($suites, $halls, $bld_type, $buildings, $floors, $rooms, $tents, $gender , $halls_arfa);

  echo json_encode($output);
