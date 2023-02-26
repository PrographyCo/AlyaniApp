<?php
    
    $lang = 'ar';
    
    require_once 'init.php';
    require_once 'config/db.php';
    require_once 'functions.php';
    require_once 'constants.php';
    
    global $css1, $css2, $css3, $css4;
    
    
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
    <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="css/skins/<?= $css4; ?>" rel="stylesheet" type="text/css"/>
    <link href="plugins/iCheck/flat/blue.css" rel="stylesheet" type="text/css"/>
    <link href="plugins/select2/select2.min.css" rel="stylesheet" type="text/css"/>
    <link href="css/<?= $css1; ?>" rel="stylesheet" type="text/css"/>
    <link href="plugins/iCheck/all.css" rel="stylesheet" type="text/css"/>
    <link href="plugins/datepicker/datepicker3.css" rel="stylesheet" type="text/css"/>
    <link href="plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet" type="text/css"/>
    <link href="plugins/daterangepicker/daterangepicker-bs3.css" rel="stylesheet" type="text/css"/>
    <link href="plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" type="text/css" href="plugins/DataTables2/datatables.min.css"/>
    <link href="css/fileinput.min.css" media="all" rel="stylesheet" type="text/css"/>
    <link href="css/bootstrap-colorpicker.css" media="all" rel="stylesheet" type="text/css"/>
    <link href="css/bootstrap-datetimepicker.min.css" media="all" rel="stylesheet" type="text/css"/>
    <link href="css/font-awesome.css" rel="stylesheet" type="text/css"/>
    <?
        if ($css2) echo '<link href="css/' . $css2 . '" rel="stylesheet" type="text/css" />';
        if ($css3) echo '<link href="css/' . $css3 . '" rel="stylesheet" type="text/css" />';
    ?>
</head>
<body>

<div class="box" style="border:0; padding:0; margin:0; box-shadow:none">
    <div class="box-body">
        <table class="datatable-table4 table table-bordered table-striped">
            <thead>
            <tr>
                <th><?= LBL_Code; ?></th>
                <th><?= LBL_Name; ?></th>
                <th><?= LBL_NationalId; ?></th>
                <!-- <th><?= LBL_ReservationNumber; ?></th> -->
                <th><?= LBL_SuiteNumber; ?></th>
                <th><?= HM_Hall; ?></th>
                <!-- <th><?= LBL_Chair1; ?> / <?= LBL_Chair2; ?> / <?= LBL_Bed; ?></th> -->
            </tr>
            </thead>
            <tbody>
            <?
                
                $array_of_suites = array();
                $array_of_halls = array();
                
                if (is_numeric($_GET['suite_id']) && $_GET['suite_id'] > 0) $sqlmore1 = " AND pa.suite_id = " . $_GET['suite_id'];
                if (is_numeric($_GET['hall_id']) && $_GET['hall_id'] > 0) $sqlmore2 = " AND pa.hall_id = " . $_GET['hall_id'];
                
                $sql = $db->query("SELECT pa.*, p.pil_name, p.pil_nationalid, p.pil_reservation_number, s.suite_title, h.hall_title
					FROM pils_accomo pa
					INNER JOIN pils p ON pa.pil_code = p.pil_code
					LEFT OUTER JOIN suites s ON pa.suite_id = s.suite_id
					LEFT OUTER JOIN suites_halls h ON pa.hall_id = h.hall_id
					WHERE pa.suite_id > 0 $sqlmore1 $sqlmore2 ORDER BY pa.suite_id, pa.hall_id, pa.extratype_id, pa.extratype_text + 1");
                while ($row = $sql->fetch()) {
                    
                    if (count($array_of_suites) == 0) {
                        
                        $array_of_suites[] = $row['suite_id'];
                        $array_of_halls[] = $row['hall_id'];
                        
                    } else {
                        
                        if (!in_array($row['suite_id'], $array_of_suites) || !in_array($row['hall_id'], $array_of_halls)) {
                            
                            $array_of_suites[] = $row['suite_id'];
                            $array_of_halls[] = $row['hall_id'];
                            
                            echo '</tbody>
							</table>
						</div><!-- /.box-body -->
					</div>
								<p style="page-break-after: always;"></p>
											<p style="page-break-before: always;"></p>
											<div class="box">
												<div class="box-body">
													<table class="table table-bordered table-striped">
														<thead>
															<tr>
															<th>' . LBL_Code . '</th>
															<th>' . LBL_Name . '</th>
															<th>' . LBL_NationalId . '</th>
															<th>' . LBL_SuiteNumber . '</th>
															<th>' . HM_Hall . '</th>
															</tr>
														</thead>
														<tbody>
														';
                        
                        }
                        
                    }
                    
                    echo '<tr>';
                    echo '<td>';
                    echo $row['pil_code'];
                    echo '</td>';
                    echo '<td>';
                    echo $row['pil_name'];
                    echo '</td>';
                    echo '<td>';
                    echo $row['pil_nationalid'];
                    echo '</td>';
                    /*echo '<td>';
                    echo $row['pil_reservation_number'];
                    echo '</td>';*/
                    echo '<td>';
                    echo $row['suite_title'];
                    echo '</td>';
                    echo '<td>';
                    echo $row['hall_title'];
                    echo '</td>';
                    /*
                    echo '<td>';
                    if ($row['extratype_id'] == 1) echo LBL_Chair1.': '.$row['extratype_text'];
                    elseif ($row['extratype_id'] == 2) echo LBL_Chair2.': '.$row['extratype_text'];
                    elseif ($row['extratype_id'] == 3) echo LBL_Bed.': '.$row['extratype_text'];
                    else echo $row['extratype_text'];
                    echo '</td>';
                    */
                    echo '</tr>';
                    
                }
            ?>

            </tbody>
        </table>
    </div><!-- /.box-body -->
</div><!-- /.box -->

</body>
</html>
