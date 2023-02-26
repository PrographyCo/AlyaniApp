<?php
  global $db;
  

  $buses = $_REQUEST['buses'];
  $gender = $_REQUEST['gender'];

  $output['availcount'] = AvailableToAccomoBuses($buses, $gender);

  echo json_encode($output);
