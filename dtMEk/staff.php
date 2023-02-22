<?php include 'layout/header.php';
    
    if (!is_numeric($_GET['type'])) die();
    $type = $_GET['type'];
    
    if ($type == 1) $title = HM_Managers;
    elseif ($type == 2) $title = HM_Supervisors;
    elseif ($type == 3) $title = HM_Muftis;
    $table = 'staff';
    $table_id = 'staff_id';
    $newedit_page = 'e_staff.php';
    
    if (is_numeric($_GET['del'])) {
        
        $id = $_GET['del'];
        $photo = $db->query("SELECT staff_photo FROM $table WHERE $table_id = $id")->fetchColumn();
        if ($photo && $photo != 'default_photo.png' && is_file('media/staff/' . $photo)) @unlink('media/staff/' . $photo);
        $sqldel1 = $db->query("DELETE FROM $table WHERE $table_id = $id");
        
    }

?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?= $title; ?>
            <a href="<?= $newedit_page; ?>?type=<?= $type; ?>" class="btn btn-success pull-<?= DIR_AFTER; ?>"><i
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
                                <th><?= LBL_Type; ?></th>
                                <th><?= LBL_Name; ?></th>
                                <th><?= LBL_ContactNumbers; ?></th>
                                <th><?= LBL_Status; ?></th>
                                <th><?= LBL_Actions; ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?
                                $sql = $db->query("SELECT * FROM $table WHERE staff_type = $type ORDER BY staff_name");
                                while ($row = $sql->fetch()) {
                                    
                                    if ($row['staff_type'] == 1) $typelbl = HM_Managers;
                                    elseif ($row['staff_type'] == 2) $typelbl = HM_Supervisors;
                                    elseif ($row['staff_type'] == 3) $typelbl = HM_Muftis;
                                    
                                    echo '<tr>';
                                    echo '<td>';
                                    echo $typelbl;
                                    echo '</td>';
                                    echo '<td>';
                                    echo $row['staff_name'];
                                    echo '</td>';
                                    echo '<td>';
                                    echo $row['staff_phones'];
                                    echo '</td>';
                                    echo '<td>';
                                    if ($row['staff_active'] == 1) echo '<span class="label label-success">' . LBL_Active . '</span>';
                                    else echo '<span class="label label-danger">' . LBL_Inactive . '</span>';
                                    echo '</td>';
                                    
                                    echo '<td>

	                        <a href="' . $newedit_page . '?id=' . $row[$table_id] . '" class="label label-info"><i class="fa fa-edit"></i> ' . LBL_Modify . '</a>
	                        <a href="staff-msg.php?id=' . $row[$table_id] . '" class="label label-info"><i class="fa fa-edit"></i> ' . LBL_SendMessages . '</a>
							<a href="' . basename($_SERVER['PHP_SELF']) . '?type=' . $_GET['type'] . '&del=' . $row[$table_id] . '" class="label label-danger" onclick="return confirm(\'' . LBL_DeleteConfirm . '\');"><i class="fa fa-trash"></i> ' . LBL_Delete . '</a>

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
