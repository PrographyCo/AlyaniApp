<?php
    global $db, $session, $lang, $url;
    
    $title = HM_OurCompanies;
    $table = 'ourcompanies';
    $table_id = 'comp_id';
    $newedit_page = CP_PATH.'/basic_info/edit/companies';
    
    if (isset($_GET['del']) && is_numeric($_GET['del'])) {
        
        $id = $_GET['del'];
        $photo = $db->query("SELECT comp_photo FROM $table WHERE $table_id = $id")->fetchColumn();
        if ($photo && $photo !== 'default_photo.png' && is_file(ASSETS_PATH.'media/ourcompanies/' . $photo)) @unlink(ASSETS_PATH.'media/ourcompanies/' . $photo);
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
                <?= $msg ?? '' ?>

                <div class="box">
                    <div class="box-body">
                        <table class="datatable-table4 table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th><?= LBL_Title ?></th>
                                <th><?= LBL_ContactNumbers ?></th>
                                <th><?= LBL_Status ?></th>
                                <th><?= LBL_Order ?></th>
                                <th><?= LBL_Actions ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                                $sql = $db->query("SELECT * FROM $table ORDER BY comp_order");
                                while ($row = $sql->fetch()) {
                                    ?>
                                    <tr>
                                        <td> <?= $row['comp_title_ar'] ?></td>
                                        <td><?= $row['comp_phone'] ?></td>
                                        <td>
                                            <span class="label label-<?= ($row['comp_active'] === 1) ? 'success' : 'danger' ?>"><?= ($row['comp_active'] === 1) ? LBL_Active : LBL_Inactive ?></span>
                                        </td>
                                        <td><?= $row['comp_order'] ?></td>
                                        <td>

                                            <a href="<?= $newedit_page . '?id=' . $row[$table_id] ?>"
                                               class="label label-info"><i class="fa fa-edit"></i> <?= LBL_Modify ?></a>
                                            <a href="<?= CP_PATH.$url . '?del=' . $row[$table_id] ?>"
                                               class="label label-danger"
                                               onclick="return confirm('<?= LBL_DeleteConfirm ?>');"><i
                                                        class="fa fa-trash"></i> <?= LBL_Delete ?></a>

                                        </td>
                                    </tr>
                                    <?php
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
