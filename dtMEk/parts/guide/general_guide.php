<?php
    global $db, $url, $lang;
    
    $title = HM_GENERALGUIDE;
    $table = 'general_guide';
    $table_id = 'gg_id';
    $newedit_page = CP_PATH.'/guide/edit/e_gg';
    
    if (isset($_GET['del']) && is_numeric($_GET['del'])) {
        
        $id = $_GET['del'];
        $sqldel1 = $db->query("DELETE FROM $table WHERE $table_id = $id");
        $sqldel2 = $db->query("DELETE FROM gg_staff WHERE $table_id = $id");
        
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
                                <th><?= LBL_Title ?></th>
                                <th><?= HM_Supervisors ?></th>
                                <th><?= LBL_Status ?></th>
                                <th><?= LBL_Order ?></th>
                                <th><?= LBL_Actions ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                                $sql = $db->query("SELECT * FROM $table ORDER BY gg_order");
                                while ($row = $sql->fetch()) {
                                    
                                    echo '<tr>';
                                    echo '<td>';
                                    echo $row['gg_title_ar'] . ' - ' . $row['gg_title_en'] . ' - ' . $row['gg_title_ur'];
                                    echo '</td>';
                                    echo '<td>';
                                    // Supervisors
                                    $sql2 = $db->query("SELECT ggs_name FROM general_guide_staff WHERE ggs_active = 1 AND ggs_id IN (SELECT ggs_id FROM gg_staff WHERE gg_id = " . $row['gg_id'] . ")");
                                    if ($sql2->rowCount() > 0) {
                                        
                                        while ($row2 = $sql2->fetch(PDO::FETCH_ASSOC)) {
                                            
                                            echo '<span class="label label-default">' . $row2['ggs_name'] . '</span> ';
                                            
                                        }
                                        
                                    } else {
                                        
                                        echo '<i>' . LBL_NotFound . '</i>';
                                        
                                    }
                                    echo '</td>';
                                    echo '<td>';
                                    if ($row['gg_active'] == 1) echo '<span class="label label-success">' . LBL_Active . '</span>';
                                    else echo '<span class="label label-danger">' . LBL_Inactive . '</span>';
                                    echo '</td>';
                                    echo '<td>';
                                    echo $row['gg_order'];
                                    echo '</td>';
                                    
                                    
                                    echo '<td>

	                        <a href="' . $newedit_page . '?id=' . $row[$table_id] . '" class="label label-info"><i class="fa fa-edit"></i> ' . LBL_Modify . '</a>
													<a href="' . $url . '?del=' . $row[$table_id] . '" class="label label-danger" onclick="return confirm(\'' . LBL_DeleteConfirm . '\');"><i class="fa fa-trash"></i> ' . LBL_Delete . '</a>

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
