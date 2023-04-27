<?php
    global $db, $session, $lang, $url;
    
    $title = HM_Promos;
    $table = 'promos';
    $table_id = 'promo_id';
    $newedit_page = CP_PATH.'/basic_info/edit/promos';
    
    if (isset($_GET['del']) && is_numeric($_GET['del'])) {
        
        $id = $_GET['del'];
        $photo = $db->query("SELECT promo_photo FROM $table WHERE $table_id = $id")->fetchColumn();
        if ($photo && $photo !== 'default_photo.png' && is_file('/assets/media/promos/' . $photo)) @unlink('/assets/media/promos/' . $photo);
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
                                <th><?= LBL_Photo ?></th>
                                <th><?= LBL_Title ?></th>
                                <th><?= LBL_Status ?></th>
                                <th><?= LBL_Order ?></th>
                                <th><?= LBL_Actions ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                                $sql = $db->query("SELECT * FROM $table ORDER BY promo_order");
                                while ($row = $sql->fetch()) {
                                    ?>
                                    <tr>
                                        <td><img src="<?= CP_PATH ?>/assets/media/promos/<?= $row['promo_photo'] ?>" style="width:100px;" alt="image"/>
                                        </td>
                                        <td><?= $row['promo_title_ar'] ?></td>
                                        <td>
                                            <span class="label label-<?= ($row['promo_active'] == 1) ? 'success' : 'danger' ?>"><?= ($row['promo_active'] == 1) ? LBL_Active : LBL_Inactive ?></span>
                                        </td>
                                        <td><?= $row['promo_order'] ?></td>
                                        <td>

                                            <a href="<?= $newedit_page . '?id=' . $row[$table_id] ?>"
                                               class="label label-info"><i class="fa fa-edit"></i><?= LBL_Modify ?></a>
                                            <a href="<?= CP_PATH.$url . '?del=' . $row[$table_id] ?>" class="label label-danger"
                                               onclick="return confirm('<?= LBL_DeleteConfirm ?>');"><i
                                                        class="fa fa-trash"></i><?= LBL_Delete ?></a>

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
