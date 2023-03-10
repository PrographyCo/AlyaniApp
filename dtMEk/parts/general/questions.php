<?php
    global $db, $url, $lang, $session;
    
    $title = HM_RatingQuestions;
    $table = 'questions';
    $table_id = 'q_id';
    $newedit_page = CP_PATH.'/general/question';
    
    if (isset($_GET['del']) && is_numeric($_GET['del'])) {
        
        $id = $_GET['del'];
        
        $sqldel2 = $db->query("DELETE FROM questions_answers WHERE qa_q_id = $id");
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
                                <th><?= LBL_TitleAr ?></th>
                                <th><?= LBL_TitleEn ?></th>
                                <th><?= LBL_TitleUr ?></th>
                                <th><?= LBL_Answers ?></th>
                                <th><?= LBL_Status ?></th>
                                <th><?= LBL_Actions ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                                $sql = $db->query("SELECT * FROM $table ORDER BY q_order");
                                while ($row = $sql->fetch()) {
                                    
                                    echo '<tr>';
                                    echo '<td>';
                                    echo $row['q_title_ar'];
                                    echo '</td>';
                                    echo '<td>';
                                    echo $row['q_title_en'];
                                    echo '</td>';
                                    echo '<td>';
                                    echo $row['q_title_ur'];
                                    echo '</td>';
                                    echo '<td>';
                                    $count_answers = $db->query("SELECT COUNT(qa_id) FROM questions_answers WHERE qa_q_id = " . $row['q_id'])->fetchColumn();
                                    echo number_format($count_answers);
                                    echo '</td>';
                                    
                                    echo '<td>';
                                    if ($row['q_active'] == 1) echo '<span class="label label-success">' . LBL_Active . '</span>';
                                    else echo '<span class="label label-danger">' . LBL_Inactive . '</span>';
                                    echo '</td>';
                                    
                                    
                                    echo '</td>';
                                    echo '<td>';
                                    
                                    echo '<a href="' . $newedit_page . '?id=' . $row[$table_id] . '" class="label label-info"><i class="fa fa-edit"></i> ' . LBL_Modify . '</a> ';
                                    echo '<a href="' . CP_PATH.$url . '?del=' . $row[$table_id] . '" class="label label-danger" onclick="return confirm(\'' . LBL_DeleteConfirm . '\');"><i class="fa fa-trash"></i> ' . LBL_Delete . '</a>';
                                    
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
