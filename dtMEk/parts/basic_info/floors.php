<?php
    global $db, $session, $url, $lang;
    
    $title = HM_Floors;
    $table = 'buildings_floors';
    $table_id = 'floor_id';
    $newedit_page = CP_PATH.'/basic_info/edit/floor';
    
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
                                <th><?= HM_Building ?></th>
                                <th><?= HM_Rooms ?></th>
                                <th><?= LBL_TotalCapacity ?></th>
                                <th><?= LBL_ٍRemaining ?></th>
                                <th><?= LBL_Status ?></th>
                                <th><?= LBL_Order ?></th>
                                <th><?= LBL_Actions ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                                $sqlmore = '';
                                if (isset($_GET['bld_id']) && is_numeric($_GET['bld_id'])) $sqlmore = " AND f.floor_bld_id = " . $_GET['bld_id'];
                                
                                $sql = $db->query("SELECT f.*, b.bld_title FROM $table f LEFT OUTER JOIN buildings b ON f.floor_bld_id = b.bld_id WHERE 1 $sqlmore ORDER BY f.floor_order");
                                while ($row = $sql->fetch()) {
                                    $countrooms = $db->query("SELECT COUNT(room_id) FROM buildings_rooms WHERE room_floor_id = " . $row['floor_id'])->fetchColumn();
                                    
                                    $totalcapacity = $db->query("SELECT SUM(room_capacity) FROM buildings_rooms WHERE room_floor_id = " . $row['floor_id'])->fetchColumn();
                                    
                                    $occu = $db->query("SELECT COUNT(pil_code) FROM pils_accomo WHERE floor_id = " . $row['floor_id'])->fetchColumn();
                                    $remaining = $totalcapacity - $occu;
                                    
                                    ?>
                                    <tr>
                                        <td>
                                            <?= $row['floor_title'] ?>
                                        </td>
                                        <td>
                                            <?= $row['bld_title'] ?>
                                        </td>
                                        <td>
                                            <a href="<?= CP_PATH ?>/basic_info/rooms?floor_id=<?= $row['floor_id'] ?>"><?= $countrooms . ' ' . HM_Rooms ?></a>
                                        </td>
                                        <td>
                                            <?= number_format($totalcapacity) ?>
                                        </td>
                                        <td>
                                            <?= number_format($remaining) ?>
                                        </td>
                                        <td>
                                            <span class="label label-<?= ($row['floor_active'] == 1) ? 'success' : 'danger' ?>"><?= ($row['floor_active'] == 1) ? LBL_Active : LBL_Inactive ?></span>
                                        </td>
                                        <td>
                                            <?= $row['floor_order'] ?>
                                        </td>
                                        <td>

                                            <a href="<?= $newedit_page . '?id=' . $row[$table_id] ?>"
                                               class="label label-info"><i class="fa fa-edit"></i><?= LBL_Modify ?></a>
                                            <a href="<?= CP_PATH.$url . '?del=' . $row[$table_id] ?>" class="label label-danger"
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
