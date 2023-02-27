<?php
    global $db, $session, $url, $lang;
    
    $title = HM_Rooms;
    $table = 'buildings_rooms';
    $table_id = 'room_id';
    $newedit_page = CP_PATH.'/basic_info/edit/room';
    
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
                                <th><?= LBL_RoomNumber ?></th>
                                <th><?= HM_Building ?></th>
                                <th><?= HM_Floor ?></th>
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
                                $sqlmore1 = $sqlmore2 = '';
                                if (isset($_GET['bld_id']) && is_numeric($_GET['bld_id'])) $sqlmore1 = " AND r.room_bld_id = " . $_GET['bld_id'];
                                if (isset($_GET['floor_id']) && is_numeric($_GET['floor_id'])) $sqlmore2 = " AND r.room_floor_id = " . $_GET['floor_id'];
                                
                                $sql = $db->query("SELECT r.*, b.bld_title, f.floor_title FROM $table r
                                        LEFT OUTER JOIN buildings b ON r.room_bld_id = b.bld_id
                                        LEFT OUTER JOIN buildings_floors f ON r.room_floor_id = f.floor_id
                                        WHERE 1 $sqlmore1 $sqlmore2 ORDER BY r.room_order");
                                
                                while ($row = $sql->fetch()) {
                                    $occu = $db->query("SELECT COUNT(pil_code) FROM pils_accomo WHERE room_id = " . $row['room_id'])->fetchColumn();
                                    $remaining = $row['room_capacity'] - $occu;
                                    ?>
                                    <tr>
                                        <td>
                                            <?= $row['room_title'] ?>
                                        </td>
                                        <td>
                                            <?= $row['bld_title'] ?>
                                        </td>
                                        <td>
                                            <?= $row['floor_title'] ?>
                                        </td>
                                        <td>
                                            <?= ($row['room_gender'] === 'm') ? LBL_Male : LBL_Female ?>
                                        </td>
                                        <td>
                                            <?= number_format($row['room_capacity']) ?>
                                        </td>
                                        <td>
                                            <?= number_format($remaining) ?>
                                        </td>
                                        <td>
                                            <span class="label label-<?= ($row['room_active'] === 1) ? 'success' : 'danger' ?>"><?= ($row['room_active'] === 1) ? LBL_Active : LBL_Inactive ?></span>
                                        </td>
                                        <td>
                                            <?= $row['room_order'] ?>
                                        </td>
                                        <td>

                                            <a href="<?= $newedit_page . '?id=' . $row[$table_id] ?>"
                                               class="label label-info"><i class="fa fa-edit"></i><?= LBL_Modify ?></a>
                                            <a href="<?= $url . '?del=' . $row[$table_id] ?>"
                                               class="label label-danger"
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
