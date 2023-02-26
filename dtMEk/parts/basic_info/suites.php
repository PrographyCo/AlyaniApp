<?php
    global $db, $session, $url, $lang;
    
    $title = HM_Suites;
    $table = 'suites';
    $table_id = 'suite_id';
    $newedit_page = '/basic_info/edit/suite';
    
    if (isset($_GET['del']) && is_numeric($_GET['del'])) {
        
        $id = $_GET['del'];
        $sqldel1 = $db->query("DELETE FROM $table WHERE $table_id = $id");
        
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
                                <th><?= LBL_SuiteNumber ?></th>
                                <th><?= LBL_Gender ?></th>
                                <th><?= HM_Halls ?></th>
                                <th><?= LBL_TotalCapacity ?></th>
                                <th><?= LBL_ٍRemaining ?></th>
                                <th><?= LBL_Status ?></th>
                                <th><?= LBL_Order ?></th>
                                <th><?= LBL_Actions ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                                $sql = $db->query("SELECT * FROM $table ORDER BY suite_order");
                                while ($row = $sql->fetch()) {
                                    $counthalls = $db->query("SELECT COUNT(hall_id) FROM suites_halls WHERE hall_suite_id = " . $row['suite_id'])->fetchColumn();
                                    
                                    $totalcapacity = $db->query("SELECT SUM(hall_capacity) FROM suites_halls WHERE hall_suite_id = " . $row['suite_id'])->fetchColumn();
                                    $occu = $db->query("SELECT COUNT(pil_code) FROM pils_accomo WHERE suite_id = " . $row['suite_id'])->fetchColumn();
                                    $remaining = $totalcapacity - $occu;
                                    ?>

                                    <tr>
                                        <td><?= $row["suite_title"] ?>
                                        <td><?= ($row['suite_gender'] === 'm') ? LBL_Male : LBL_Female ?></td>
                                        <td>
                                            <a href="/basic_info/halls?suite_id=<?= $row['suite_id'] ?>"><?= $counthalls . ' ' . HM_Halls ?></a>
                                        </td>
                                        <td><?= number_format($totalcapacity) ?></td>
                                        <td><?= number_format($remaining) ?></td>
                                        <td>
                                            <span class="label label-<?= ($row['suite_active'] === 1) ? 'success' : 'danger' ?>"><?= ($row['suite_active'] === 1) ? LBL_Active : LBL_Inactive ?></span>
                                        </td>
                                        <td><?= $row['suite_order'] ?>
                                        </td>
                                        <td>

                                            <a href="<?= $newedit_page . '?id=' . $row[$table_id] ?>"
                                               class="label label-info"><i class="fa fa-edit"></i><?= LBL_Modify ?></a>
                                            <a href="<?= $url . '?del=' . $row[$table_id] ?>" class="label label-danger"
                                               onclick="return confirm('<?= LBL_DeleteConfirm ?>');"><i
                                                        class="fa fa-trash"></i><?= LBL_Delete ?></a>

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
