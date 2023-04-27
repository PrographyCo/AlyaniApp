<?php
    global $db, $session, $lang, $url;
    
    $title = HM_Halls;
    $table = 'suites_halls_stuff';
    $table_id = 'stuff_id';
    $newedit_page = CP_PATH.'/basic_info/edit/stuff';
    
    if (isset($_GET['hall_id']) && $_GET['hall_id'] > 0) $newedit_page .= '?hall_id=' . $_GET['hall_id'];
    
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
                                <th><?= Type ?></th>
                                <th><?= HM_Hall ?></th>
                                <th><?= LBL_Status ?></th>
                                <th><?= LBL_Order ?></th>
                                <th><?= LBL_Actions ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                                $sqlmore = '';
                                if (isset($_GET['hall_id']) && is_numeric($_GET['hall_id'])) $sqlmore = "WHERE h.hall_id = " . $_GET['hall_id'];
                                
                                $sql = $db->query("SELECT s.*, h.hall_title FROM $table s LEFT OUTER JOIN suites_halls h ON h.hall_id = s.hall_id $sqlmore ORDER BY s.stuff_order");
                                while ($row = $sql->fetch()) {
                                    ?>
                                    <tr>
                                        <td><?= $row['stuff_title'] ?></td>
                                        <td>
                                            <?= match ($row['stuff_type']) {
                                                'bed' => LBL_Bed,
                                                'chair' => LBL_Chair1,
                                                'bench' => LBL_Chair2
                                            }?>
                                        </td>
                                        <td><?= $row['hall_title'] ?></td>
                                        <td>
                                            <span class="label label-<?= ($row['stuff_active'] == 1) ? 'success' : 'danger' ?>"><?= ($row['stuff_active'] == 1) ? LBL_Active : LBL_Inactive ?></span>
                                        </td>
                                        <td><?= $row['stuff_order'] ?></td>
                                        <td>

                                            <a href="<?= $newedit_page . (isset($_GET['hall_id']) ? '&' : '?') . 'id=' . $row[$table_id] ?>"
                                               class="label label-info">
                                                <i class="fa fa-edit"></i><?= LBL_Modify ?>
                                            </a>
                                            <a href="<?= $url . (isset($_GET['hall_id']) ? '?hall_id=' . $_GET['hall_id'] . '&' : '?') . 'del=' . $row[$table_id] ?>"
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
