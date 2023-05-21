<?php
    
    $lang = 'ar';
    
    global $css1, $css2, $css3, $css4, $db, $lang;
    
    
    $curpage = '/' . basename($_SERVER['REQUEST_URI']);
    $curpagewithquery = $_SERVER['REQUEST_URI'];
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>ِExported PDF File</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.4 -->
    <link href="<?= CP_PATH ?>/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="<?= CP_PATH ?>/assets/css/skins/<?= $css4 ?>" rel="stylesheet" type="text/css"/>
    <link href="<?= CP_PATH ?>/assets/plugins/iCheck/flat/blue.css" rel="stylesheet" type="text/css"/>
    <link href="<?= CP_PATH ?>/assets/plugins/select2/select2.min.css" rel="stylesheet" type="text/css"/>
    <link href="<?= CP_PATH ?>/assets/css/<?= $css1 ?>" rel="stylesheet" type="text/css"/>
    <link href="<?= CP_PATH ?>/assets/plugins/iCheck/all.css" rel="stylesheet" type="text/css"/>
    <link href="<?= CP_PATH ?>/assets/plugins/datepicker/datepicker3.css" rel="stylesheet" type="text/css"/>
    <link href="<?= CP_PATH ?>/assets/plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet"
          type="text/css"/>
    <link href="<?= CP_PATH ?>/assets/plugins/daterangepicker/daterangepicker-bs3.css" rel="stylesheet"
          type="text/css"/>
    <link href="<?= CP_PATH ?>/assets/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css" rel="stylesheet"
          type="text/css"/>
    <link rel="stylesheet" type="text/css" href="<?= CP_PATH ?>/assets/plugins/DataTables2/datatables.min.css"/>
    <link href="<?= CP_PATH ?>/assets/css/fileinput.min.css" media="all" rel="stylesheet" type="text/css"/>
    <link href="<?= CP_PATH ?>/assets/css/bootstrap-colorpicker.css" media="all" rel="stylesheet" type="text/css"/>
    <link href="<?= CP_PATH ?>/assets/css/bootstrap-datetimepicker.min.css" media="all" rel="stylesheet"
          type="text/css"/>
    <link href="<?= CP_PATH ?>/assets/css/font-awesome.css" rel="stylesheet" type="text/css"/>
    <?php
        if (isset($css2)) echo '<link href="' . CP_PATH . '/assets/css/' . $css2 . '" rel="stylesheet" type="text/css" />';
        if (isset($css3)) echo '<link href="' . CP_PATH . '/assets/css/' . $css3 . '" rel="stylesheet" type="text/css" />';
    ?>
</head>
<body>
<?php
    
    $array_of_suites = array();
    $array_of_halls = array();
    $sqlmore1 = $sqlmore2 = '';
    $i = 1;
    
    if (isset($_GET['suite_id']) && is_numeric($_GET['suite_id']) && $_GET['suite_id'] > 0) $sqlmore1 = " AND pa.suite_id = " . $_GET['suite_id'];
    if (isset($_GET['hall_id']) && is_numeric($_GET['hall_id']) && $_GET['hall_id'] > 0) $sqlmore2 = " AND pa.hall_id = " . $_GET['hall_id'];
    
    $sql = $db->query("SELECT pa.*, p.pil_name, p.pil_nationalid, p.pil_reservation_number, s.suite_title, h.hall_title, shs.stuff_title, shs.stuff_type
					FROM pils_accomo pa
					INNER JOIN pils p ON pa.pil_code = p.pil_code
					LEFT OUTER JOIN suites s ON pa.suite_id = s.suite_id
					LEFT OUTER JOIN suites_halls h ON pa.hall_id = h.hall_id
					LEFT OUTER JOIN suites_halls_stuff shs on pa.stuff_id = shs.stuff_id
					WHERE pa.suite_id > 0 $sqlmore1 $sqlmore2 ORDER BY s.suite_title, h.hall_title, shs.stuff_type, shs.stuff_title, p.pil_reservation_number");
    while ($row = $sql->fetch()) {
        if (!in_array($row['suite_id'], $array_of_suites) || !in_array($row['hall_id'], $array_of_halls)) {
            $i = 1;
            $array_of_suites[] = $row['suite_id'];
            $array_of_halls[] = $row['hall_id'];
            
            if (count($array_of_suites) > 0) {
                echo '</tbody>
							</table>
						</div><!-- /.box-body -->
					</div><p style="page-break-after: always;"></p>
											<p style="page-break-before: always;"></p>';
            }
            echo '<div class="box">
												<div class="box-body">
												<div class="box-header text-center">
												    <img src="' . CP_PATH . '/assets/images/logo.png" alt="logo" style="width: 20%">
												</div>
													<table class="table table-bordered table-striped">
														<thead>
														    <tr>
														        <th style="text-align: center;font-size: 20px;padding-bottom: 10px;" colspan="2">' . LBL_SuiteNumber . ': ' . $row['suite_title'] . '</th>
														        <th style="text-align: center;font-size: 20px;padding-bottom: 10px;" colspan="2">' . HM_Hall . ': ' . $row['hall_title'] . '</th>
                                                            </tr>
															<tr>
															<th>#</th>
															<th>' . LBL_Name . '</th>
															<th>' . LBL_NationalId . '</th>
															<th>' . HM_Stuff . '</th>
															</tr>
														</thead>
														<tbody>
														';
            
        }
        
        echo '<tr>';
        
        echo '<td>';
        echo $i++;
        echo '</td>';
        
        echo '<td>';
        echo $row['pil_name'];
        echo '</td>';
        
        echo '<td>';
        echo $row['pil_nationalid'];
        echo '</td>';
        
        echo '<td>';
        echo $row['stuff_title'];
        echo '</td>';
        
        echo '</tr>';
        
    }
?>

</body>
</html>
