<?php
    include 'header.php';
    
    $title = HM_competitions;
    $table = 'competitions';
    $table_id = 'competition_id';
    $newedit_page = 'e_competition.php';
    
    if (is_numeric($_GET['del'])) {
        
        $id = $_GET['del'];
        
        $sqldel1 = $db->query("DELETE FROM $table WHERE id = $id");
        
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
                                <th>ID</th>
                                <th>
                                    <?= LBL_Title; ?>
                                </th>
                                <th>
                                    <?= LBL_about; ?>
                                </th>
                                <th><?= LBL_Actions; ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?
                                $sql = $db->query("SELECT * FROM $table ");
                                while ($row = $sql->fetch()) {
                                    
                                    echo '<tr>';
                                    echo '<td>';
                                    echo $row['id'];
                                    echo '</td>';
                                    
                                    echo '<td>';
                                    echo $row['name_' . $_COOKIE['lang']];
                                    echo '</td>';
                                    echo '<td>';
                                    echo $row['about_' . $_COOKIE['lang']];
                                    echo '</td>';
                                    
                                    
                                    echo '<td>

	                        <a href="' . $newedit_page . '?id=' . $row['id'] . '" class="label label-info"><i class="fa fa-edit"></i> ' . LBL_Modify . '</a>
							 <a href="' . basename($_SERVER['PHP_SELF']) . '?del=' . $row['id'] . '" class="label label-danger" onclick="return confirm(\'' . LBL_DeleteConfirm . '\');"><i class="fa fa-trash"></i> ' . LBL_Delete . '</a>

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
