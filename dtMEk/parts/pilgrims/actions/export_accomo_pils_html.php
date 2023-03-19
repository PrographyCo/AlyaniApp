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
                <th>#</th>
                <th><?= LBL_Name ?></th>
                <th><?= LBL_NationalId ?></th>
                <th><?= LBL_ReservationNumber ?></th>
                <th><?= LBL_Code ?></th>
                <th><?= LBL_City ?></th>
                <th><?= LBL_Accomodation ?></th>
            </tr>
            </thead>
            <tbody>
            <?php
                $sql = $db->query("SELECT p.*, c.country_title_ar, ci.city_title_ar FROM pils p LEFT OUTER JOIN countries c ON p.pil_country_id = c.country_id LEFT OUTER JOIN cities ci ON p.pil_city_id = ci.city_id WHERE pil_code IN (SELECT pil_code FROM pils_accomo) ORDER BY pil_name");
                $i = 1;
                while ($row = $sql->fetch()) {
                    $accomo = $db->query("SELECT * FROM pils_accomo WHERE pil_code = '" . $row['pil_code'] . "'")->fetch(PDO::FETCH_ASSOC);
                    
                    ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td>
                            <?= $row['pil_name'] ?>
                        </td>
                        <td>
                            <?= $row['pil_nationalid'] ?>
                        </td>
                        <td>
                            <?= $row['pil_reservation_number'] ?>
                        </td>
                        <td>
                            <?= $row['pil_code'] ?>
                        </td>
                        <td>
                            <?= $row['city_title_' . $lang] ?>
                        </td>
                        <td id="pilaccomo_<?= $row['pil_id'] ?>">
                            <?= json_encode($accomo) ?>
                        </td>
                    </tr>
                    <?php
                    
                }
            ?>

            </tbody>
        </table>
    </div><!-- /.box-body -->
</div><!-- /.box -->
</body>
</html>
