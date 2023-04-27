<?php
    global $db, $session, $url, $lang;
    
    $title = HM_Buildings;
    $table = 'buildings';
    $table_id = 'bld_id';
    $newedit_page = CP_PATH.'/basic_info/edit/building';
    
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
                <?= $msg??'' ?>

                <div class="box">
                    <div class="box-body">
                        <table class="datatable-table4 table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th><?= LBL_BuildingNumber ?></th>
                                <th><?= LBL_Type ?></th>
                                <th><?= HM_Floors ?></th>
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
                                $sql = $db->query("SELECT * FROM $table ORDER BY bld_order");
                                while ($row = $sql->fetch()) {
                                    $countfloors = $db->query("SELECT COUNT(floor_id) FROM buildings_floors WHERE floor_bld_id = " . $row['bld_id'])->fetchColumn();
    
                                    $countrooms = $db->query("SELECT COUNT(room_id) FROM buildings_rooms WHERE room_bld_id = " . $row['bld_id'])->fetchColumn();
    
                                    $totalcapacity = $db->query("SELECT SUM(room_capacity) FROM buildings_rooms WHERE room_bld_id = " . $row['bld_id'])->fetchColumn();
    
                                    $occu = $db->query("SELECT COUNT(pil_code) FROM pils_accomo WHERE bld_id = " . $row['bld_id'])->fetchColumn();
                                    $remaining = $totalcapacity - $occu;
                                    
                                    ?>
                            <tr><td><?= $row['bld_title'] ?></td><td><?= ($row['bld_type'] == 1)?HM_Building:LBL_Premises ?></td><td>
                                    <a href="<?= CP_PATH ?>/basic_info/floors?bld_id=<?= $row['bld_id'] ?>"><?= $countfloors . ' ' . HM_Floors ?></a>
                                </td>
                                <td><a href="<?= CP_PATH ?>/basic_info/rooms?bld_id=<?= $row['bld_id'] ?>"><?= $countrooms . ' ' . HM_Rooms ?></a></td>
                                <td>
                                    <?= number_format($totalcapacity) ?>
                                </td>
                                <td>
                                    <?= number_format($remaining) ?>
                                </td>
                                <td>
                                    
                                    <span class="label label-<?= ($row['bld_active']==1)?'success':'danger' ?>"><?= ($row['bld_active']==1)?LBL_Active:LBL_Inactive ?></span>
                                </td>
                                <td><?= $row['bld_order'] ?></td>
                                <td>

                                    <a href="<?= $newedit_page . '?id=' . $row[$table_id] ?>" class="label label-info"><i class="fa fa-edit"></i><?= LBL_Modify ?></a>
                                    <a href="<?= CP_PATH.$url . '?del=' . $row[$table_id] ?>" class="label label-danger" onclick="return confirm('<?= LBL_DeleteConfirm ?>');"><i class="fa fa-trash"></i><?= LBL_Delete ?></a>

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
