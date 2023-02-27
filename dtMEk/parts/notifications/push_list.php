<?php
    global $db, $lang, $session, $url;
    
    if (isset($_GET['del']) && is_numeric($_GET['del'])) {
        
        $id = $_GET['del'];
        $sqldel1 = $db->query("DELETE FROM pushmsgs WHERE msg_id = $id");
        
    }

?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?= HM_NotificationsHistory ?>
            <a href="<?= CP_PATH ?>/notifications/push_new" class="btn btn-success pull-<?= DIR_AFTER ?>"><i
                        class="fa fa-star"></i> <?= LBL_SendNewNotification ?></a>
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
                                <th><?= LBL_Message ?></th>
                                <th><?= LBL_Platforms ?></th>
                                <th><?= LBL_Type ?></th>
                                <th><?= LBL_Devices ?></th>
                                <th><?= LBL_DateAdded ?></th>
                                <th><?= LBL_Actions ?></th>

                            </tr>
                            </thead>
                            <tbody>
                            <?php
                                $sql = $db->query("SELECT * FROM pushmsgs ORDER BY msg_date DESC");
                                while ($row = $sql->fetch()) {
                                    
                                    echo '<tr>
                        <td>' . $row['msg_body'] . '</td>';
                                    echo '<td>';
                                    if ($row['msg_platform'] == 0) echo 'Both';
                                    elseif ($row['msg_platform'] == 1) echo 'iOS';
                                    elseif ($row['msg_platform'] == 2) echo 'Android';
                                    echo '</td>';
                                    
                                    echo '<td>';
                                    if ($row['msg_type'] == 0) echo 'All';
                                    elseif ($row['msg_type'] == 1) echo 'Users';
                                    elseif ($row['msg_type'] == 2) echo 'Salon Owners';
                                    elseif ($row['msg_type'] == 3) echo 'Specific User';
                                    elseif ($row['msg_type'] == 4) echo 'Specific Salon Owner';
                                    echo '</td>';
                                    
                                    echo '<td>' . $row['msg_count'] . '</td>
                        <td>' . date("j F Y g:i:s a", $row['msg_date']) . '</td>';
                                    echo '<td>';
                                    echo '
									<a href="' . $url . '?del=' . $row['msg_id'] . '" class="label label-danger" onclick="return confirm(\'' . LBL_DeleteConfirm . '\');"><i class="fa fa-trash"></i> ' . LBL_Delete . '</a>';
                                    echo '</td>';
                                    echo '
                      </tr>';
                                
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
