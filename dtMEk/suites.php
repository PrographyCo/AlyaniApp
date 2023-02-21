<?php include 'header.php';
    
    $title = HM_Suites;
    $table = 'suites';
    $table_id = 'suite_id';
    $newedit_page = 'e_suite.php';
    
    if (is_numeric($_GET['del'])) {
        
        $id = $_GET['del'];
        $sqldel1 = $db->query("DELETE FROM $table WHERE $table_id = $id");
        
    }

?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?= $title; ?>
            <a href="<?= $newedit_page; ?>" class="btn btn-success pull-<?= DIR_AFTER; ?>"><i
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
                                <th><?= LBL_SuiteNumber; ?></th>
                                <th><?= LBL_Gender; ?></th>
                                <th><?= HM_Halls; ?></th>
                                <th><?= LBL_TotalCapacity; ?></th>
                                <th><?= LBL_ٍRemaining; ?></th>
                                <th><?= LBL_Status; ?></th>
                                <th><?= LBL_Order; ?></th>
                                <th><?= LBL_Actions; ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?
                                $sql = $db->query("SELECT * FROM $table ORDER BY suite_order");
                                while ($row = $sql->fetch()) {
                                    
                                    echo '<tr>';
                                    echo '<td>';
                                    echo $row['suite_title'];
                                    echo '</td>';
                                    echo '<td>';
                                    if ($row['suite_gender'] == 'm') echo LBL_Male;
                                    elseif ($row['suite_gender'] == 'f') echo LBL_Female;
                                    echo '</td>';
                                    
                                    echo '<td>';
                                    $counthalls = $db->query("SELECT COUNT(hall_id) FROM suites_halls WHERE hall_suite_id = " . $row['suite_id'])->fetchColumn();
                                    echo '<a href="halls.php?suite_id=' . $row['suite_id'] . '">' . $counthalls . ' ' . HM_Halls . '</a>';
                                    echo '</td>';
                                    
                                    echo '<td>';
                                    $totalcapacity = $db->query("SELECT SUM(hall_capacity) FROM suites_halls WHERE hall_suite_id = " . $row['suite_id'])->fetchColumn();
                                    echo number_format($totalcapacity);
                                    echo '</td>';
                                    
                                    echo '<td>';
                                    $occu = $db->query("SELECT COUNT(pil_code) FROM pils_accomo WHERE suite_id = " . $row['suite_id'])->fetchColumn();
                                    $remaining = $totalcapacity - $occu;
                                    echo number_format($remaining);
                                    echo '</td>';
                                    
                                    echo '<td>';
                                    if ($row['suite_active'] == 1) echo '<span class="label label-success">' . LBL_Active . '</span>';
                                    else echo '<span class="label label-danger">' . LBL_Inactive . '</span>';
                                    echo '</td>';
                                    echo '<td>';
                                    echo $row['suite_order'];
                                    echo '</td>';
                                    
                                    echo '<td>

	                        <a href="' . $newedit_page . '?id=' . $row[$table_id] . '" class="label label-info"><i class="fa fa-edit"></i> ' . LBL_Modify . '</a>
													<a href="' . basename($_SERVER['PHP_SELF']) . '?del=' . $row[$table_id] . '" class="label label-danger" onclick="return confirm(\'' . LBL_DeleteConfirm . '\');"><i class="fa fa-trash"></i> ' . LBL_Delete . '</a>

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

<?php include 'footer.php'; ?>
