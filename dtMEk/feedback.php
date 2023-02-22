<?php include 'layout/header.php';
    
    $title = HM_Feedback;
    $table = 'feedback';
    $table_id = 'feedb_id';
    
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

                                <th>ID</th>
                                <th><?= LBL_Type; ?></th>
                                <th><?= LBL_Name; ?></th>
                                <th><?= LBL_Message; ?></th>
                                <th><?= LBL_DateAdded; ?></th>
                                <th><?= LBL_Actions; ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?
                                
                                if (is_numeric($_GET['feedb_type']) && $_GET['feedb_type'] == 1) $sqlmore1 = " AND feedb_type = 1";
                                if (is_numeric($_GET['feedb_type']) && $_GET['feedb_type'] == 2) $sqlmore1 = " AND feedb_type = 2";
                                
                                
                                $sql = $db->query("SELECT * FROM $table
		                    WHERE 1 $sqlmore1
		                    ORDER BY feedb_dateadded DESC");
                                while ($row = $sql->fetch()) {
                                    if ($row['status'] == 0) {
                                        $color = "color:#f00";
                                    } else {
                                        $color = "color:#000";
                                    }
                                    echo "<tr style='$color'>";
                                    echo '<td>';
                                    echo $row['feedb_id'];
                                    echo '</td>';
                                    echo '<td>';
                                    if ($row['feedb_type'] == 1) echo LBL_Enquiry;
                                    elseif ($row['feedb_type'] == 2) echo LBL_Complaint;
                                    elseif ($row['feedb_type'] == 3) echo LBL_Suggestion;
                                    echo '</td>';
                                    
                                    echo '<td>';
                                    echo $row['feedb_name'];
                                    echo '</td>';
                                    
                                    echo '<td>';
                                    echo nl2br($row['feedb_message']);
                                    echo '</td>';
                                    
                                    echo '<td style="display:inline-block; direction:ltr">';
                                    echo date("j F Y g:i:s a", $row['feedb_dateadded']);
                                    echo '</td>';
                                    
                                    echo '<td>';
                                    echo '
													<a href="feedback_details.php?id=' . $row[$table_id] . '" class="label label-success"><i class="fa fa-info"></i> ' . LBL_Details . '</a>
													<a href="' . basename($_SERVER['PHP_SELF']) . '?feedb_type=' . $_GET['feedb_type'] . '&del=' . $row[$table_id] . '" class="label label-danger" onclick="return confirm(\'' . LBL_DeleteConfirm . '\');"><i class="fa fa-trash"></i> ' . LBL_Delete . '</a>';
                                    echo '</td>
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
