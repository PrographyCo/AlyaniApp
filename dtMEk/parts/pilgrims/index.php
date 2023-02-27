<?php
    global $db, $session, $url, $lang;
    
    $title = HM_PILGRIMS;
    $table = 'pils';
    $table_id = 'pil_id';
    $newedit_page = CP_PATH.'/pilgrims/edit/pilgrim';
    
    $sqlmore1 = $sqlmore2 = $sqlmore3 = $sqlmore4 = $sqlmore5 = $sqlmore6 = '';
    
    if ((isset($_GET['del'])) && is_numeric($_GET['del'])) {
        
        $id = $_GET['del'];
        
        $pil_code = $db->query("SELECT pil_code FROM pils WHERE pil_id = $id")->fetchColumn();
        $photo = $db->query("SELECT pil_photo FROM $table WHERE $table_id = $id")->fetchColumn();
        $qr_photo = $db->query("SELECT pil_qrcode FROM $table WHERE $table_id = $id")->fetchColumn();
        
        if ($pil_code) $sqldel2 = $db->query("DELETE FROM pils_accomo WHERE pil_code = '$pil_code'");
        if ($photo && $photo !== 'default_photo.png' && $photo !== 'default_male.png' && $photo !== 'default_female.png' && is_file(ASSETS_PATH . 'media/pils/' . $photo)) @unlink(ASSETS_PATH . 'media/pils/' . $photo);
        if ($qr_photo && is_file(ASSETS_PATH . 'media/pils_qrcodes/' . $qr_photo)) @unlink(ASSETS_PATH . 'media/pils_qrcodes/' . $qr_photo);
        
        $sqldel1 = $db->query("DELETE FROM $table WHERE $table_id = $id");
        
    }
    
    if (isset($_GET['deleteall']) && (int)$_GET['deleteall'] === 1) {
        
        if (isset($_GET['city_id']) && is_numeric($_GET['city_id']) && $_GET['city_id'] > 0) $sqlmore1 = " AND pil_city_id = " . $_GET['city_id'];
        if (isset($_GET['gender']) && ($_GET['gender'] === 'm' || $_GET['gender'] === 'f')) $sqlmore2 = " AND pil_gender = '" . $_GET['gender'] . "'";
        if (isset($_GET['pilc_id']) && is_numeric($_GET['pilc_id']) && $_GET['pilc_id'] > 0) $sqlmore3 = " AND pil_pilc_id = " . $_GET['pilc_id'];
        if (isset($_GET['code']) && $_GET['code']) $sqlmore4 = " AND pil_code LIKE '%" . $_GET['code'] . "%'";
        if (isset($_GET['resno']) && $_GET['resno']) $sqlmore5 = " AND pil_reservation_number LIKE '%" . $_GET['resno'] . "%'";
        if (isset($_GET['accomo']) && $_GET['accomo'] === 1) $sqlmore6 = " AND pil_code IN (SELECT pil_code FROM pils_accomo)";
        if (isset($_GET['accomo']) && $_GET['accomo'] === 2) $sqlmore6 = " AND pil_code NOT IN (SELECT pil_code FROM pils_accomo)";
        
        
        $sqlallpils = $db->query("SELECT p.*, c.country_title_$lang, ci.city_title_$lang FROM $table p
		LEFT OUTER JOIN countries c ON p.pil_country_id = c.country_id
		LEFT OUTER JOIN cities ci ON p.pil_city_id = ci.city_id
		WHERE 1 $sqlmore1 $sqlmore2 $sqlmore3 $sqlmore4 $sqlmore5 $sqlmore6
		ORDER BY pil_name");
        
        while ($rowallpils = $sqlallpils->fetch(PDO::FETCH_ASSOC)) {
            
            $id = $rowallpils['pil_id'];
            
            $pil_code = $db->query("SELECT pil_code FROM pils WHERE pil_id = $id")->fetchColumn();
            $photo = $db->query("SELECT pil_photo FROM $table WHERE $table_id = $id")->fetchColumn();
            $qr_photo = $db->query("SELECT pil_qrcode FROM $table WHERE $table_id = $id")->fetchColumn();
            
            if ($pil_code) $sqldel2 = $db->query("DELETE FROM pils_accomo WHERE pil_code = '$pil_code'");
            if ($photo && $photo !== 'default_photo.png' && $photo !== 'default_male.png' && $photo !== 'default_female.png' && is_file(ASSETS_PATH . 'media/pils/' . $photo)) @unlink(ASSETS_PATH . 'media/pils/' . $photo);
            if ($qr_photo && is_file(ASSETS_PATH . 'media/pils_qrcodes/' . $qr_photo)) @unlink(ASSETS_PATH . 'media/pils_qrcodes/' . $qr_photo);
            
            $sqldel1 = $db->query("DELETE FROM $table WHERE $table_id = $id");
            
        }
        
    }

?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <!--        TODO: SEE THIS SECTION -->
        <h1>
            <?= $title ?>
            <a href="<?= $newedit_page ?>" class="btn btn-success pull-<?= DIR_AFTER ?>"><i
                        class="fa fa-star"></i> <?= BTN_AddNew ?></a>

            <a href="<?= CP_PATH ?>/cards/pils/generated/sticker?city_id=<?= $_GET['city_id'] ?? '' ?>&gender=<?= $_GET['gender'] ?? '' ?>&pilc_id=<?= $_GET['pilc_id'] ?? '' ?>&code=<?= $_GET['code'] ?? '' ?>&resno=<?= $_GET['resno'] ?? '' ?>&accomo=<?= $_GET['accomo'] ?? '' ?>"
               target="_blank" class="btn btn-success pull-<?= DIR_AFTER ?>" style="margin-<?= DIR_AFTER ?>: 10px"><i
                        class="fa fa-file-pdf-o"></i> <?= LBL_sticker ?></a>
            <a href="<?= CP_PATH ?>/cards/pils/generated/index?city_id=<?= $_GET['city_id'] ?? '' ?>&gender=<?= $_GET['gender'] ?? '' ?>&pilc_id=<?= $_GET['pilc_id'] ?? '' ?>&code=<?= $_GET['code'] ?? '' ?>&resno=<?= $_GET['resno'] ?? '' ?>&accomo=<?= $_GET['accomo'] ?? '' ?>"
               target="_blank" class="btn btn-success pull-<?= DIR_AFTER ?>" style="margin-<?= DIR_AFTER ?>: 10px"><i
                        class="fa fa-file-pdf-o"></i> <?= BTN_ExportPilsCards ?></a>


            <a href="<?= CP_PATH ?>/pilgrims/actions/export?city_id=<?= $_GET['city_id'] ?? '' ?>&gender=<?= $_GET['gender'] ?? '' ?>&pilc_id=<?= $_GET['pilc_id'] ?? '' ?>&code=<?= $_GET['code'] ?? '' ?>&resno=<?= $_GET['resno'] ?? '' ?>&accomo=<?= $_GET['accomo'] ?? '' ?>"
               class="btn btn-success pull-<?= DIR_AFTER ?>" style="margin-<?= DIR_AFTER ?>: 10px"><i
                        class="fa fa-file-excel-o"></i> <?= BTN_ExportToExcel ?></a>
            <a href="<?= CP_PATH ?>/pilgrims/actions/import" class="btn btn-success pull-<?= DIR_AFTER ?>"
               style="margin-<?= DIR_AFTER ?>: 10px"><i class="fa fa-file-excel-o"></i> <?= BTN_ImportFromExcel ?></a>
            <a href="<?= $url ?>?deleteall=1&city_id=<?= $_GET['city_id'] ?? '' ?>&gender=<?= $_GET['gender'] ?? '' ?>&pilc_id=<?= $_GET['pilc_id'] ?? '' ?>&code=<?= $_GET['code'] ?? '' ?>&resno=<?= $_GET['resno'] ?? '' ?>&accomo=<?= $_GET['accomo'] ?? '' ?>"
               onclick="return confirm('<?= LBL_DeleteConfirm ?>');" class="btn btn-danger pull-<?= DIR_AFTER ?>"
               style="margin-<?= DIR_AFTER ?>: 10px"><i class="fa fa-trash"></i> <?= BTN_DELETEALLPILS ?></a>
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
                        <form method="get">
                            <div class="row">
                                <div class="form-group col-sm-4">
                                    <label><?= LBL_City ?></label>
                                    <select name="city_id" id="city_id" class="form-control select2">
                                        <option value="0">
                                            <?= LBL_Choose ?>
                                        </option>
                                        <?php
                                            $sqlcities = $db->query("SELECT * FROM cities WHERE city_active = 1 ORDER BY city_title_" . $lang);
                                            while ($rowc = $sqlcities->fetch(PDO::FETCH_ASSOC)) {
                                                ?>
                                                <option value="<?= $rowc['city_id'] ?>" <?= (isset($_GET['city_id']) && $_GET['city_id'] === $rowc['city_id']) ? 'selected="selected"' : '' ?>>
                                                    <?= $rowc['city_title_' . $lang] ?>
                                                </option>
                                                <?php
                                            }
                                        ?>

                                    </select>
                                </div>
                                <div class="form-group col-sm-4">
                                    <label><?= LBL_Gender ?></label>
                                    <select name="gender" id="gender" class="form-control select2">
                                        <option value="0">
                                            <?= LBL_Choose ?>
                                        </option>
                                        <option value="m" <?php if (isset($_GET['gender']) && $_GET['gender'] === 'm') echo 'selected="selected"'; ?>>
                                            <?= LBL_Male ?>
                                        </option>
                                        <option value="f" <?php if (isset($_GET['gender']) && $_GET['gender'] === 'f') echo 'selected="selected"'; ?>>
                                            <?= LBL_Female ?>
                                        </option>
                                    </select>
                                </div>
                                <div class="form-group col-sm-4">
                                    <label><?= LBL_Class ?></label>
                                    <select name="pilc_id" id="pilc_id" class="form-control select2">
                                        <option value="0">
                                            <?= LBL_Choose ?>
                                        </option>
                                        <?php
                                            $sqlpilc = $db->query("SELECT * FROM pils_classes WHERE pilc_active = 1 ORDER BY pilc_title_" . $lang);
                                            while ($rowpc = $sqlpilc->fetch(PDO::FETCH_ASSOC)) {
                                                echo '<option value="' . $rowpc['pilc_id'] . '" ';
                                                if (isset($_GET['pilc_id']) && $_GET['pilc_id'] === $rowpc['pilc_id']) echo 'selected="selected"';
                                                echo '>' . $rowpc['pilc_title_' . $lang] . '</option>';
                                            }
                                        ?>

                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-sm-4">
                                    <label><?= LBL_Code ?></label>
                                    <input type="text" name="code" id="code" class="form-control"
                                           value="<?= $_GET['code'] ?? '' ?>"/>
                                </div>
                                <div class="form-group col-sm-4">
                                    <label><?= LBL_ReservationNumber ?></label>
                                    <input type="text" name="resno" id="resno" class="form-control"
                                           value="<?= $_GET['resno'] ?? '' ?>"/>
                                </div>
                                <div class="form-group col-sm-4">
                                    <label><?= LBL_Accomodation ?></label>
                                    <select name="accomo" id="accomo" class="form-control select2">
                                        <option value="0">
                                            <?= LBL_Choose ?>
                                        </option>
                                        <option value="1" <?php if (isset($_GET['accomo']) && $_GET['accomo'] === 1) echo 'selected="selected"'; ?>>
                                            <?= LBL_VALIDACCOM ?>
                                        </option>
                                        <option value="2" <?php if (isset($_GET['accomo']) && $_GET['accomo'] === 2) echo 'selected="selected"'; ?>>
                                            <?= LBL_NOACCOM ?>
                                        </option>
                                    </select>
                                </div>

                            </div>

                            <input type="submit" class="btn btn-primary col-xs-12" value="<?= LBL_SearchFilter ?>"/>

                        </form>
                    </div>
                </div>
                <div class="box">
                    <div class="box-body">
                        <?php
                            if (isset($_GET['city_id']) && is_numeric($_GET['city_id']) && $_GET['city_id'] > 0) {
                                echo '<a href="#" onclick="removeAccomoCity(' . $_GET['city_id'] . ')" class="btn btn-danger">' . LBL_RemoveAccomoForCity . '</a> <a href="#" onclick="removeAccomoCityBus(' . $_GET['city_id'] . ')" class="btn btn-danger">' . LBL_RemoveAccomoForCityBus . '</a> <span id="removecityaccomo_loading"></span>';
                            }
                        ?>
                        <table class="datatable-table4 table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th><?= LBL_Name ?></th>
                                <th><?= LBL_NationalId ?></th>
                                <th><?= LBL_ReservationNumber ?></th>
                                <th><?= LBL_Code ?></th>
                                <th><?= LBL_City ?></th>
                                <th><?= LBL_Status ?></th>
                                <th><?= LBL_Accomodation ?></th>
                                <th><?= LBL_BusAccomodation ?></th>
                                <th><?= LBL_Card ?></th>
                                <th><?= LBL_Actions ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                                $sqlmore1 = $sqlmore2 = $sqlmore3 = $sqlmore4 = $sqlmore5 = $sqlmore6 = $sqlmore6 = '';
                                if (isset($_GET['city_id']) && is_numeric($_GET['city_id']) && $_GET['city_id'] > 0) $sqlmore1 = " AND pil_city_id = " . $_GET['city_id'];
                                if (isset($_GET['gender']) && ($_GET['gender'] === 'm' || $_GET['gender'] === 'f')) $sqlmore2 = " AND pil_gender = '" . $_GET['gender'] . "'";
                                if (isset($_GET['pilc_id']) && is_numeric($_GET['pilc_id']) && $_GET['pilc_id'] > 0) $sqlmore3 = " AND pil_pilc_id = " . $_GET['pilc_id'];
                                if (isset($_GET['code'])) $sqlmore4 = " AND pil_code LIKE '%" . $_GET['code'] . "%'";
                                if (isset($_GET['resno'])) $sqlmore5 = " AND pil_reservation_number LIKE '%" . $_GET['resno'] . "%'";
                                if (isset($_GET['accomo']) && $_GET['accomo'] === 1) $sqlmore6 = " AND pil_code IN (SELECT pil_code FROM pils_accomo)";
                                if (isset($_GET['accomo']) && $_GET['accomo'] === 2) $sqlmore6 = " AND pil_code NOT IN (SELECT pil_code FROM pils_accomo)";
                                
                                $sql = $db->query("SELECT p.*, c.country_title_$lang, ci.city_title_$lang FROM $table p
                                                    LEFT OUTER JOIN countries c ON p.pil_country_id = c.country_id
                                                    LEFT OUTER JOIN cities ci ON p.pil_city_id = ci.city_id
                                                       WHERE 1 $sqlmore1 $sqlmore2 $sqlmore3 $sqlmore4 $sqlmore5 $sqlmore6
                                                       ORDER BY pil_name");
                                while ($row = $sql->fetch()) {
                                $accomo = $db->query("SELECT pil_code FROM pils_accomo WHERE pil_code = '" . $row['pil_code'] . "'")->fetchColumn();
                            
                            ?>
                            <tr>
                                <td>
                                    <?= $row['pil_name'] ?>
                                </td>
                                <td>
                                    <?= $row['pil_nationalid'] ?>
                                </td>
                                <td>
                                    <?= $row['pil_reservation_number'] ?>
                                </td>
                                <td>
                                    <?= $row['pil_code'] ?>
                                </td>
                                <td>
                                    <?= $row['city_title_' . $lang] ?>
                                </td>
                                <td>
                                    <span class="label label-<?= ($row['pil_active'] === 1) ? "success" : "danger" ?>"><?= ($row['pil_active'] === 1) ? LBL_Active : LBL_Inactive ?></span>
                                </td>
                                <td id="pilaccomo_<?= $row['pil_id'] ?>">
                                    <span class="label label-<?= ($accomo) ? "success" : "danger" ?>"><?= ($accomo) ? LBL_VALIDACCOM : LBL_NOACCOM ?></span>
                                    <?php
                                        if ($accomo) {
                                            ?>
                                            <a href="#"
                                               onclick="confirmRemoveAccomo(<?= $row['pil_id'] ?>);"
                                               class="label label-default"><?= LBL_RemoveAccomo ?></a>
                                            <?php
                                        }
                                    ?>
                                </td>
                                <td>
                                    <span class="label label-<?= ($row['pil_bus_id'] > 0) ? "success" : "danger" ?>"><?= ($row['pil_bus_id'] > 0) ? LBL_VALIDACCOM : LBL_NOACCOM ?></span>
                                </td>
                                <td>
                                    <a href="<?= CP_PATH ?>/pil_cards_designs/pils/common/index.php?id=<?= $row[$table_id] ?>"
                                       target="_blank"><?= LBL_Card ?></a>
                                </td>
                                <td>

                                    <a href="<?= $newedit_page . '?id=' . $row[$table_id] ?>" class="label label-info"><i
                                                class="fa fa-edit"></i> <?= LBL_Modify ?></a>
                                    <a href="<?= $url . '?del=' . $row[$table_id] ?>"
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
                pil_id: pil_id
            };

            $.post('/post/removePilAccomo', data, function (response) {
                $('#pilaccomo_' + pil_id).html('<span class="label label-danger"><?= LBL_NOACCOM ?></span>');
            });

        }

    }

    function removeAccomoCity(city_id) {

        var confirmed = confirm('<?=LBL_RemoveAccomoForCityConfirm?>');
        if (confirmed) {

            $('#removecityaccomo_loading').html('<?=LBL_Loading?>');
            var data = {
                city_id
            };

            $.post('/post/removeAccomoCity', data, function (response) {
                $('#removecityaccomo_loading').html(response);
                window.location.reload();
            });


        }

    }

    function removeAccomoCityBus(city_id) {

        var confirmed = confirm('<?=LBL_RemoveAccomoForCityConfirm?>');
        if (confirmed) {

            $('#removecityaccomo_loading').html('<?=LBL_Loading?>');
            var data = {
                city_id
            };

            $.post('/post/removeAccomoCityBus', data, function (response) {
                $('#removecityaccomo_loading').html(response);
                window.location.reload();
            });


        }

    }

</script>
