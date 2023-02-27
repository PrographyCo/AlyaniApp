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
                <th><?= LBL_BuildingNumber; ?></th>
                <th><?= HM_Floor; ?></th>
                <th><?= LBL_RoomNumber; ?></th>
            </tr>
            </thead>
            <tbody>
            <?php
                $sqlmore1 = $sqlmore2 = '';
                if (is_numeric($_GET['building_id']) && $_GET['building_id'] > 0) $sqlmore1 = " AND pa.bld_id = " . $_GET['building_id'];
                if (is_numeric($_GET['floor_id']) && $_GET['floor_id'] > 0) $sqlmore2 = " AND pa.floor_id = " . $_GET['floor_id'];
                
                $sql = $db->query("SELECT pa.*, p.pil_name, p.pil_nationalid, p.pil_reservation_number, b.bld_title, f.floor_title, r.room_title
					FROM pils_accomo pa
					INNER JOIN pils p ON pa.pil_code = p.pil_code
					LEFT OUTER JOIN buildings b ON pa.bld_id = b.bld_id
					LEFT OUTER JOIN buildings_floors f ON pa.floor_id = f.floor_id
					LEFT OUTER JOIN buildings_rooms r ON pa.room_id = r.room_id
					WHERE pa.bld_id > 0 $sqlmore1 $sqlmore2 ORDER BY pa.bld_id, pa.floor_id, pa.room_id");
                while ($row = $sql->fetch()) {
                    
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
                    echo $row['bld_title'];
                    echo '</td>';
                    echo '<td>';
                    echo $row['floor_title'];
                    echo '</td>';
                    echo '<td>';
                    echo LBL_RoomNumber . ': ' . $row['room_title'];
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
