<?php
    
    $lang = 'ar';
    
    global $css1, $css2, $css3, $css4, $db;
    
    
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
    <link href="<?= CP_PATH ?>/assets/plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet" type="text/css"/>
    <link href="<?= CP_PATH ?>/assets/plugins/daterangepicker/daterangepicker-bs3.css" rel="stylesheet" type="text/css"/>
    <link href="<?= CP_PATH ?>/assets/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" type="text/css" href="<?= CP_PATH ?>/assets/plugins/DataTables2/datatables.min.css"/>
    <link href="<?= CP_PATH ?>/assets/css/fileinput.min.css" media="all" rel="stylesheet" type="text/css"/>
    <link href="<?= CP_PATH ?>/assets/css/bootstrap-colorpicker.css" media="all" rel="stylesheet" type="text/css"/>
    <link href="<?= CP_PATH ?>/assets/css/bootstrap-datetimepicker.min.css" media="all" rel="stylesheet" type="text/css"/>
    <link href="<?= CP_PATH ?>/assets/css/font-awesome.css" rel="stylesheet" type="text/css"/>
    <?php
        if (isset($css2)) echo '<link href="'.CP_PATH.'/assets/css/' . $css2 . '" rel="stylesheet" type="text/css" />';
        if (isset($css3)) echo '<link href="'.CP_PATH.'/assets/css/' . $css3 . '" rel="stylesheet" type="text/css" />';
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
                <th><?= LBL_BusNumber; ?></th>
            </tr>
            </thead>
            <tbody>
            <?php
                
                $array_of_buses = array();
                $sqlmore2 = $sqlmore1 = '';
                if (is_numeric($_GET['bus_id']) && $_GET['bus_id'] > 0) $sqlmore1 = " AND p.pil_bus_id = " . $_GET['bus_id'];
                if (is_numeric($_GET['city_id']) && $_GET['city_id'] > 0) $sqlmore2 = " AND p.pil_city_id = " . $_GET['city_id'];
                
                $sql = $db->query("SELECT p.pil_code, p.pil_name, p.pil_nationalid, p.pil_reservation_number, b.bus_id, b.bus_title
					FROM pils p
					LEFT OUTER JOIN buses b ON p.pil_bus_id = b.bus_id
					WHERE p.pil_bus_id > 0 $sqlmore1 $sqlmore2 ORDER BY p.pil_bus_id");
                while ($row = $sql->fetch()) {
                    
                    if (count($array_of_buses) == 0) {
                        
                        $array_of_buses[] = $row['bus_id'];
                        
                    } else {
                        
                        if (!in_array($row['bus_id'], $array_of_buses)) {
                            
                            $array_of_buses[] = $row['bus_id'];
                            
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
															<th>' . LBL_BusNumber . '</th>
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
                    echo $row['bus_title'];
                    echo '</td>';
                    echo '</tr>';
                    
                }
            ?>

            </tbody>
        </table>
    </div><!-- /.box-body -->
</div><!-- /.box -->
</body>
</html>
