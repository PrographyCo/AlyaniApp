<?php
    global $db, $session, $lang, $url;
    
    if (!isset($_GET['type']) || !is_numeric($_GET['type'])) die(400);
    $type = (int)$_GET['type'];
    
    if ($type === 1) $title = HM_Managers;
    elseif ($type === 2) $title = HM_Supervisors;
    elseif ($type === 3) $title = HM_Muftis;
    
    $table = 'staff';
    $table_id = 'staff_id';
    $newedit_page = CP_PATH . '/staff/edit';
    
    if (isset($_GET['del']) && is_numeric($_GET['del'])) {
        
        $id = $_GET['del'];
        $photo = $db->query("SELECT staff_photo FROM $table WHERE $table_id = $id")->fetchColumn();
        if ($photo && $photo !== 'default_photo.png' && is_file(ASSETS_PATH . 'media/staff/' . $photo)) @unlink(ASSETS_PATH . 'media/staff/' . $photo);
        $sqldel1 = $db->query("DELETE FROM $table WHERE $table_id = $id");
        
    }

?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?= $title ?>
            <a href="<?= $newedit_page ?>?type=<?= $type ?>" class="btn btn-success pull-<?= DIR_AFTER ?>"><i
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
                                <th><?= LBL_Type ?></th>
                                <th><?= LBL_Name ?></th>
                                <th><?= LBL_ContactNumbers ?></th>
                                <th><?= LBL_BusNumber ?></th>
                                <th><?= LBL_Accomodation ?></th>
                                <th><?= LBL_Status ?></th>
                                <th><?= LBL_Actions ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                                $sql = $db->query("SELECT * FROM $table WHERE staff_type = $type ORDER BY staff_name");
                                while ($row = $sql->fetch()) {
                                    $buses = $db->query('SELECT * FROM buses WHERE bus_staff_id=' . $row['staff_id']);
                                    $accomo = $db->query("SELECT pil_code FROM pils_accomo WHERE pil_code = '" . $row['staff_id'] . "' AND type = 'emp'")->fetchColumn();

                                    ?>
                                    <tr>
                                        <td>
                                            <?= match ($row['staff_type']) {
                                                "1" => HM_Managers,
                                                "2" => HM_Supervisors,
                                                "3" => HM_Muftis,
                                            } ?>
                                        </td>
                                        <td>
                                            <?= $row['staff_name'] ?>
                                        </td>
                                        <td>
                                            <?= $row['staff_phones'] ?>
                                        </td>
                                        <td>
                                            <?php
                                                $a = [];
                                                while ($b = $buses->fetch())
                                                    $a[]='<a href="'.CP_PATH.'/basic_info/edit/bus?id='.$b['bus_id'].'">'.$b['bus_title'].'</a>';
                                                echo implode(',',$a);
                                            ?>
                                        </td>
                                        <td id="pilaccomo_<?= $row['staff_id'] ?>">
                                            <span class="label label-<?= ($accomo) ? "success" : "danger" ?>"><?= ($accomo) ? LBL_VALIDACCOM : LBL_NOACCOM ?></span>
                                            <?php
                                                if ($accomo) {
                                                    ?>
                                                    <a href="#"
                                                       onclick="confirmRemoveAccomo(<?= $row['staff_id'] ?>);"
                                                       class="label label-default"><?= LBL_RemoveAccomo ?></a>
                                                    <?php
                                                } else {
                                                    ?>
                                                    <a href="<?= CP_PATH ?>/accomo/transfer?pil_id=<?= $row['staff_id'] ?>&type=emp"
                                                       class="label label-default"><?= LBL_Add ?></a>
                                                    <?php
                                                }
                                            ?>
                                        </td>
                                        <td>
                                    <span class="label label-<?= (((int)$row['staff_active']) === 1) ? 'success' : 'danger' ?>">
                                        <?= (((int)$row['staff_active']) === 1) ? LBL_Active : LBL_Inactive ?>
                                    </span>
                                        </td>
                                        <td>
                                            <a href="<?= $newedit_page . '?id=' . $row[$table_id] ?>"
                                               class="label label-info"><i class="fa fa-edit"></i><?= LBL_Modify ?></a>
                                            <a href="<?= CP_PATH ?>/staff/msg?id=<?= $row[$table_id] ?>"
                                               class="label label-info"><i
                                                        class="fa fa-edit"></i><?= LBL_SendMessages ?></a>
                                            <a href="<?= $url . '?type=' . $_GET['type'] . '&del=' . $row[$table_id] ?>"
                                               class="label label-danger"
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
<script>
    function confirmRemoveAccomo(pil_id) {

        var confirmed = confirm('<?= LBL_RemoveAccomoPilgrimConfirm ?>');
        if (confirmed) {

            $('#pilaccomo_' + pil_id).text('<?= LBL_Loading ?>');

            var data = {
                pil_id: pil_id,
                type: 'emp'
            };

            $.post('<?= CP_PATH ?>/post/removePilAccomo', data, function (response) {
                $('#pilaccomo_' + pil_id).html('<span class="label label-danger"><?= LBL_NOACCOM ?></span><a href="<?= CP_PATH ?>/accomo/transfer?pil_id='+ pil_id +'&type=emp" class="label label-default"><?= LBL_Add ?></a>');
            });

        }

    }
</script>
