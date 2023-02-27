<?php
    global $db, $url, $lang, $session;
    
    $title = HM_ContactInfo;
    $table = 'contactinfo';
    $table_id = 'ci_id';
    $newedit_page = CP_PATH.'/general/edit/ci';
    
    if (is_numeric($_GET['del']??'')) {
        
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
                                <th><?= LBL_Type ?></th>
                                <th><?= LBL_Name ?></th>
                                <th><?= LBL_Order ?></th>
                                <th><?= LBL_Actions ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                                $sql = $db->query("SELECT * FROM $table ORDER BY ci_type, ci_order");
                                while ($row = $sql->fetch()) {
                                    
                                    echo '<tr>';
                                    
                                    echo '<td>';
                                    if ($row['ci_type'] == 1) echo '<b>' . LBL_Phone . '</b>';
                                    elseif ($row['ci_type'] == 2) echo '<b>' . LBL_Email . '</b>';
                                    echo '</td>';
                                    
                                    echo '<td>';
                                    echo $row['ci_value'];
                                    echo '</td>';
                                    
                                    echo '<td>';
                                    echo $row['ci_order'];
                                    echo '</td>';
                                    
                                    echo '<td>

													<a href="' . $newedit_page . '?id=' . $row[$table_id] . '" class="label label-info"><i class="fa fa-edit"></i> ' . LBL_Modify . '</a>
													<a href="' . $url . '?del=' . $row[$table_id] . '" class="label label-danger" onclick="return confirm(\'' . LBL_DeleteConfirm . '\');"><i class="fa fa-trash"></i> ' . LBL_Delete . '</a>';
                                    
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
