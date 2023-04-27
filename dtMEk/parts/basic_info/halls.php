<?php
    global $db, $session, $lang, $url;
    
    $title = HM_Halls;
    $table = 'suites_halls';
    $table_id = 'hall_id';
    $newedit_page = CP_PATH.'/basic_info/edit/hall';
    
    if (isset($_GET['suite_id']) && $_GET['suite_id'] > 0) $newedit_page .= '?suite_id=' . $_GET['suite_id'];
    
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
                                <th><?= LBL_Title ?></th>
                                <th><?= HM_Suite ?></th>
                                <th><?= LBL_Gender ?></th>
                                <th><?= LBL_Capacity ?></th>
                                <th><?= LBL_ٍRemaining ?></th>
                                <th><?= LBL_Status ?></th>
                                <th><?= LBL_Order ?></th>
                                <th><?= LBL_Actions ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                                $sqlmore = '';
                                if (isset($_GET['suite_id']) && is_numeric($_GET['suite_id'])) $sqlmore = " AND h.hall_suite_id = " . $_GET['suite_id'];
                                
                                $sql = $db->query("SELECT h.*, s.suite_title, s.suite_gender FROM $table h LEFT OUTER JOIN suites s ON h.hall_suite_id = s.suite_id WHERE 1 $sqlmore ORDER BY h.hall_order");
                                while ($row = $sql->fetch()) {
                                    
                                    $capacity = $db->query("SELECT COUNT(*) FROM suites_halls_stuff WHERE stuff_active = 1 AND hall_id = " . $row['hall_id'])->fetchColumn();
                                    $occu = $db->query("SELECT COUNT(pil_code) FROM pils_accomo WHERE hall_id = " . $row['hall_id'])->fetchColumn();
                                    $remaining = $capacity - $occu;
                                    
                                    ?>
                                    <tr>
                                        <td><?= $row['hall_title'] ?></td>
                                        <td><?= $row['suite_title'] ?></td>
                                        <td><?= ($row['suite_gender'] == 'm') ? LBL_Male : LBL_Female ?></td>
                                        <td>
                                            <a href="<?= CP_PATH ?>/basic_info/stuffs?hall_id=<?= $row['hall_id'] ?>"><?= number_format($capacity) . ' ' . HM_Stuff ?></a>
                                        </td>
                                        <td>
                                            <?= number_format($remaining) ?></td>
                                        <td>
                                            <span class="label label-<?= ($row['hall_active'] == 1) ? 'success' : 'danger' ?>"><?= ($row['hall_active'] == 1) ? LBL_Active : LBL_Inactive ?></span>
                                        </td>
                                        <td><?= $row['hall_order'] ?></td>
                                        <td>

                                            <a href="<?= $newedit_page . (isset($_GET['suite_id']) ? '&' : '?') . 'id=' . $row[$table_id] ?>"
                                               class="label label-info">
                                                <i class="fa fa-edit"></i><?= LBL_Modify ?>
                                            </a>
                                            <a href="<?= $url . (isset($_GET['suite_id']) ? '?suite_id=' . $_GET['suite_id'] . '&' : '?') . 'del=' . $row[$table_id] ?>"
                                               class="label label-danger"
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
