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
    $i = 0;
    $sqlmore1 = $sqlmore2 = '';
    if (isset($_GET['building_id']) && is_numeric($_GET['building_id']) && $_GET['building_id'] > 0) $sqlmore1 = " AND pa.bld_id = " . $_GET['building_id'];
    if (isset($_GET['floor_id']) && is_numeric($_GET['floor_id']) && $_GET['floor_id'] > 0) $sqlmore2 = " AND pa.floor_id = " . $_GET['floor_id'];
    
    $array_of_buildings = array();
    
    $sql = $db->query("SELECT pa.*, b.bld_title, f.floor_title, r.room_title
					FROM pils_accomo pa
					LEFT OUTER JOIN buildings b ON pa.bld_id = b.bld_id
					LEFT OUTER JOIN buildings_floors f ON pa.floor_id = f.floor_id
					LEFT OUTER JOIN buildings_rooms r ON pa.room_id = r.room_id
					WHERE pa.bld_id > 0 $sqlmore1 $sqlmore2 ORDER BY pa.bld_id, pa.floor_id, pa.room_id, p.pil_reservation_number");
    
    while ($row = $sql->fetch()) {
        if ($row['type'] == 'pil') $pil = $db->query('SELECT pil_name, pil_nationalid, pil_reservation_number FROM pils where pil_code="' . $row['pil_code'] . '"')->fetch(PDO::FETCH_ASSOC);
        else $pil = $db->query('SELECT staff_name as pil_name FROM staff where staff_id="' . $row['pil_code'] . '"')->fetch(PDO::FETCH_ASSOC);
        
        if (!in_array($row['bld_title'], $array_of_buildings)) {
            $i = 1;
            
            if (count($array_of_buildings) > 0) {
                echo '</tbody>
							</table>
						</div><!-- /.box-body -->
					</div><p style="page-break-after: always;"></p>
											<p style="page-break-before: always;"></p>';
            }
            
            echo '<div class="box">
												<div class="box-header text-center">
												    <img src="' . CP_PATH . '/assets/images/logo.png" alt="logo" style="width: 20%">
												</div>
												<div class="box-body">
													<table class="table table-bordered table-striped">
														<thead>
														    <tr>
														        <th style="text-align: center;font-size: 20px;padding-bottom: 10px;" colspan="7">' . LBL_BuildingNumber . ': ' . $row['bld_title'] . '</th>
                                                            </tr>
															<tr>
                                                                <th>#</th>
                                                                <th>' . LBL_Code .'</th>
                                                                <th>' . LBL_Name .'</th>
                                                                <th>' . LBL_NationalId .'</th>
                                                                <th>' . LBL_BuildingNumber .'</th>
                                                                <th>' . HM_Floor .'</th>
                                                                <th>' . LBL_RoomNumber .'</th>
															</tr>
														</thead>
														<tbody>
														';
            $array_of_buildings[] = $row['bld_title'];
        }
        
        echo '<tr>';
        echo '<td>';
        echo $i++;
        echo '</td>';
        echo '<td>';
        echo $row['pil_code'];
        echo '</td>';
        echo '<td>';
        echo $pil['pil_name'] ?? '-';
        echo '</td>';
        echo '<td>';
        echo $pil['pil_nationalid'] ?? '-';
        echo '</td>';
        echo '<td>';
        echo $row['bld_title'];
        echo '</td>';
        echo '<td>';
        echo $row['floor_title'];
        echo '</td>';
        echo '<td>';
        echo $row['room_title'];
        echo '</td>';
        echo '</tr>';
        
    }
?>
</body>
</html>
