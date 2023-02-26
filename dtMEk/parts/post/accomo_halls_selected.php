<?php
  global $db;
  
  $suites = $_REQUEST['suites'];
  $halls = $_REQUEST['halls'];

  $output['availcount'] = AvailableToAccomoSuites($suites, $halls, $gender);

  echo json_encode($output);
