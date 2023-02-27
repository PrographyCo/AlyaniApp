<?php include 'layout/header.php';
    
    if (is_numeric($_GET['del'])) {
        $sql = $db->query("DELETE FROM _users WHERE user_id = " . $_GET['del']);
    }


?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?= HM_SystemUsers; ?>
            <a href="userreg.php" class="btn btn-primary pull-<?= DIR_AFTER; ?>"><i
                        class="fa fa-star"></i> <?= BTN_AddNew; ?></a>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <!-- left column -->
            <div class="col-md-12">
                <?php echo $msg; ?>

                <div class="box">
                    <div class="box-body">
                        <table class="datatable-table4 table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th><?= LBL_Name; ?></th>
                                <th><?= LBL_Email; ?></th>
                                <th><?= LBL_Phone; ?></th>
                                <th><?= LBL_Role; ?></th>
                                <th><?= LBL_Actions; ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?
                                $sql = $db->query("SELECT * FROM _users ORDER BY name");
                                while ($row = $sql->fetch()) {
                                    
                                    echo '<tr>
                        <td>' . $row['name'] . '</td>
                        <td>' . $row['email'] . '</td>
                        <td>' . $row['phone'] . '</td>';
                                    
                                    if ($row['userlevel'] == 9) echo '<td>' . LBL_Administrator . '</td>';
                                    elseif ($row['userlevel'] == 8) echo '<td>' . LBL_Moderator . '</td>';
                                    else echo '<td>Unknown</td>';
                                    
                                    echo '<td>

                        <a href="userreg.php?id=' . $row['user_id'] . '" class="label label-info"><i class="fa fa-edit"></i> ' . LBL_Modify . '</a>
                        <a href="users_all.php?del=' . $row['user_id'] . '" onclick="return confirm(\'Are you sure you want to delete this user?\');" class="label label-danger"><i class="fa fa-trash"></i> ' . LBL_Delete . '</a>

                        </td>
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

<?php include 'layout/footer.php'; ?>
