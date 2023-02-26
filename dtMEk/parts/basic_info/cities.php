<?php
    global $db, $session, $lang, $url;
    
    $title = HM_Cities;
    $table = 'cities';
    $table_id = 'city_id';
    $newedit_page = '/basic_info/edit/city';
    
    if (isset($_GET['del']) && is_numeric($_GET['del'])) {
        
        $id = $_GET['del'];
        $sqldel1 = $db->query("DELETE FROM $table WHERE $table_id = $id");
        $sqldel2 = $db->query("DELETE FROM cities_staff WHERE $table_id = $id");
        
    }

?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?= $title ?>
            <a href="<?= $newedit_page ?>" class="btn btn-success pull-<?= DIR_AFTER ?>"><i
                        class="fa fa-star"></i> <?= BTN_AddNew ?></a>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <!-- left column -->
            <div class="col-md-12">
                <?= $msg ?? '' ?>

                <div class="box">
                    <div class="box-body">
                        <table class="datatable-table4 table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th><?= LBL_Title ?></th>
                                <th><?= HM_Supervisors ?></th>
                                <th><?= LBL_Status ?></th>
                                <th><?= LBL_Order ?></th>
                                <th><?= LBL_Actions ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                                $sql = $db->query("SELECT * FROM $table ORDER BY city_order");
                                while ($row = $sql->fetch()) {
                                    ?>
                                    <tr>
                                        <td><?= $row['city_title_ar'] ?> </td>
                                        <td>
                                            <?php
                                                // Supervisors
                                                $sql2 = $db->query("SELECT staff_name FROM staff WHERE staff_type = 2 AND staff_active = 1 AND staff_id IN (SELECT staff_id FROM cities_staff WHERE city_id = " . $row['city_id'] . ")");
                                                if ($sql2->rowCount() > 0) {
                                                    while ($row2 = $sql2->fetch(PDO::FETCH_ASSOC)) {
                                                        ?>
                                                        <span class="label label-default"><?= $row2['staff_name'] ?></span>
                                                        <?php
                                                    }
                                                } else {
                                                    ?>
                                                    <i><?= LBL_NotFound ?></i>
                                                    <?php
                                                }
                                            ?>
                                        </td>
                                        <td>
                                            <span class="label label-<?= ($row['city_active'] === 1) ? 'success' : 'danger' ?>"><?= ($row['city_active'] === 1) ? LBL_Active : LBL_Inactive ?></span>
                                        </td>
                                        <td> <?= $row['city_order'] ?></td>
                                        <td>

                                            <a href="<?= $newedit_page . '?id=' . $row[$table_id] ?>"
                                               class="label label-info"><i class="fa fa-edit"></i><?= LBL_Modify ?></a>
                                            <a href="<?= $url . '?del=' . $row[$table_id] ?>" class="label label-danger"
                                               onclick="return confirm('<?= LBL_DeleteConfirm ?>');">
                                                <i class="fa fa-trash"></i><?= LBL_Delete ?>
                                            </a>

                                        </td>
                                    </tr>
                                    <?php
                                }
                            ?>

                            </tbody>
                        </table>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div><!--/.col (right) -->

        </div>   <!-- /.row -->
    </section><!-- /.content -->

</div>
