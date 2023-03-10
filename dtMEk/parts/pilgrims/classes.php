<?php
    global $db, $session, $lang, $url;
    
    $title = HM_ManageClasses;
    $table = 'pils_classes';
    $table_id = 'pilc_id';
    $newedit_page = CP_PATH.'/pilgrims/edit/class';
    
    if (isset($_GET['del']) && is_numeric($_GET['del'])) {
        
        $id = $_GET['del'];
        $sqldel1 = $db->query("DELETE FROM $table WHERE $table_id = $id");
        $sqldel2 = $db->query("DELETE FROM pils_classes_sv WHERE $table_id = $id");
        $sqldel3 = $db->query("DELETE FROM pils_classes_muftis WHERE $table_id = $id");
        
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
                                <th><?= LBL_Mufti ?></th>
                                <th><?= LBL_Status ?></th>
                                <th><?= LBL_Order ?></th>
                                <th><?= LBL_Actions ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                                $sql = $db->query("SELECT * FROM $table ORDER BY pilc_order");
                                while ($row = $sql->fetch()) {
                                    
                                    echo '<tr>';
                                    echo '<td>';
                                    echo $row['pilc_title_ar'];
                                    echo '</td>';
                                    echo '<td>';
                                    // Muftis
                                    $sql1 = $db->query("SELECT staff_name FROM staff WHERE staff_type = 3 AND staff_active = 1 AND staff_id IN (SELECT staff_id FROM pils_classes_muftis WHERE pilc_id = " . $row['pilc_id'] . ")");
                                    if ($sql1->rowCount() > 0) {
                                        
                                        while ($row1 = $sql1->fetch(PDO::FETCH_ASSOC)) {
                                            
                                            echo '<span class="label label-default">' . $row1['staff_name'] . '</span> ';
                                            
                                        }
                                        
                                    } else {
                                        
                                        echo '<i>' . LBL_NotFound . '</i>';
                                        
                                    }
                                    echo '</td>';
                                    echo '<td>';
                                    if ($row['pilc_active'] == 1) echo '<span class="label label-success">' . LBL_Active . '</span>';
                                    else echo '<span class="label label-danger">' . LBL_Inactive . '</span>';
                                    echo '</td>';
                                    echo '<td>';
                                    echo $row['pilc_order'];
                                    echo '</td>';
                                    
                                    echo '<td>

	                        <a href="' . $newedit_page . '?id=' . $row[$table_id] . '" class="label label-info"><i class="fa fa-edit"></i> ' . LBL_Modify . '</a>
													<a href="' . CP_PATH.$url . '?del=' . $row[$table_id] . '" class="label label-danger" onclick="return confirm(\'' . LBL_DeleteConfirm . '\');"><i class="fa fa-trash"></i> ' . LBL_Delete . '</a>

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
