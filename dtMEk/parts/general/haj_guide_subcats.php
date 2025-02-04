<?php
    global $db, $url, $lang, $session;
    
    $title = HM_HajGuideSubCats;
    $table = 'guide_categories';
    $table_id = 'gcat_id';
    $newedit_page = CP_PATH.'e_gcat.php';
    
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
                                <th><?= LBL_ParentCategory; ?></th>
                                <th><?= LBL_Title; ?></th>
                                <th><?= LBL_Articles; ?></th>
                                <th><?= LBL_Status; ?></th>
                                <th><?= LBL_Order; ?></th>
                                <th><?= LBL_Actions; ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?
                                
                                if (is_numeric($_GET['parent_id']) && $_GET['parent_id'] > 0) $sqlmore = " AND c.gcat_parent_id = " . $_GET['parent_id'];
                                $sql = $db->query("SELECT c.*, pc.gcat_title_ar as 'pgcat_title_ar', pc.gcat_title_en as 'pgcat_title_en', pc.gcat_title_ur as 'pgcat_title_ur'
													FROM $table c
													LEFT OUTER JOIN guide_categories pc ON c.gcat_parent_id = pc.gcat_id
													WHERE c.gcat_parent_id > 0 $sqlmore ORDER BY c.gcat_order");
                                while ($row = $sql->fetch()) {
                                    
                                    echo '<tr>';
                                    echo '<td>';
                                    echo $row['pgcat_title_ar'] . ' - ' . $row['pgcat_title_en'] . ' - ' . $row['pgcat_title_ur'];
                                    echo '</td>';
                                    echo '<td>';
                                    echo $row['gcat_title_ar'] . ' - ' . $row['gcat_title_en'] . ' - ' . $row['gcat_title_ur'];
                                    echo '</td>';
                                    
                                    echo '<td>';
                                    $countsubs = $db->query("SELECT COUNT(gcat_id) FROM guide_categories WHERE gcat_parent_id = " . $row['gcat_id'])->fetchColumn();
                                    echo $countsubs;
                                    echo '</td>';
                                    echo '<td>';
                                    if ($row['gcat_active'] == 1) echo '<span class="label label-success">' . LBL_Active . '</span>';
                                    else echo '<span class="label label-danger">' . LBL_Inactive . '</span>';
                                    echo '</td>';
                                    echo '<td>';
                                    echo $row['gcat_order'];
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
