<?php
    include 'layout/header.php';
    require 'pushfunctions.php';
    require 'msegat.php';
    $title = HM_AutoAccomo . " - " . arafa;
    
    //error_reporting(E_ALL);
    //ini_set('display_errors', 1);
    
    if ($_GET['removeall'] == 1) {
        
        $sqldel = $db->query("TRUNCATE TABLE pils_accomo");
        
    }

?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?= $title; ?>
            <a href="?removeall=1" onclick="return confirm('<?= LBL_RemoveAccomoConfirm; ?>');"
               class="btn btn-danger pull-<?= DIR_AFTER; ?>" style="margin-<?= DIR_AFTER; ?>: 10px"><i
                        class="fa fa-trash"></i> <?= BTN_REMOVEACCOMO; ?></a>
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
                        <form method="post" action="auto_accomo_arafa.php" enctype="multipart/form-data">


                            <div class="panel well">
                                <?= LBL_NotAccomoPilgrims; ?>: <span id="countpils"></span>
                                - <?= LBL_AvailableForAccomo; ?>: <span id="availcount">0</span>
                            </div>

                            <div class="row">
                                <div class="form-group col-sm-4">
                                    <label><?= LBL_Cities; ?></label>
                                    <select name="city_id[]" id="city_id[]" class="form-control select2"
                                            multiple="multiple" onchange="countCitiesPils(); calcAvailAccomo();">
                                        <?
                                            $sqlcities = $db->query("SELECT * FROM cities WHERE city_active = 1 ORDER BY city_title_" . $lang);
                                            while ($rowc = $sqlcities->fetch(PDO::FETCH_ASSOC)) {
                                                echo '<option value="' . $rowc['city_id'] . '" ';
                                                echo '>
															' . $rowc['city_title_' . $lang] . '
															</option>';
                                            }
                                        ?>

                                    </select>
                                </div>
                                <div class="form-group col-sm-4">
                                    <label><?= LBL_Gender; ?></label>
                                    <select name="gender" id="gender" class="form-control select2"
                                            onchange="countCitiesPils(); calcAvailAccomo(); getAccomosGender(this.value);">
                                        <option value="0">
                                            <?= LBL_All; ?>
                                        </option>
                                        <option value="m" <?php if ($_GET['gender'] == 'm') echo 'selected="selected"'; ?>>
                                            <?= LBL_Male; ?>
                                        </option>
                                        <option value="f" <?php if ($_GET['gender'] == 'f') echo 'selected="selected"'; ?>>
                                            <?= LBL_Female; ?>
                                        </option>
                                    </select>
                                </div>
                                <div class="form-group col-sm-4">
                                    <label><?= LBL_Class; ?></label>
                                    <select name="pilc_id" id="pilc_id" class="form-control select2"
                                            onchange="countCitiesPils();">
                                        <option value="0">
                                            <?= LBL_All; ?>
                                        </option>
                                        <?
                                            $sqlpilc = $db->query("SELECT * FROM pils_classes WHERE pilc_active = 1 ORDER BY pilc_title_" . $lang);
                                            while ($rowpc = $sqlpilc->fetch(PDO::FETCH_ASSOC)) {
                                                echo '<option value="' . $rowpc['pilc_id'] . '" ';
                                                if ($_GET['pilc_id'] == $rowpc['pilc_id']) echo 'selected="selected"';
                                                echo '>
															' . $rowpc['pilc_title_' . $lang] . '
															</option>';
                                            }
                                        ?>

                                    </select>
                                </div>
                            </div>

                            <div class="row">

                                <div class="form-group col-sm-4">
                                    <label><?= LBL_TentNumber_halls; ?> ( <?= arafa; ?> )</label>
                                    <div id="TentsArea">
                                        <select name="hallsarfa_id[]" id="halls_arfa[]" class="form-control select2"
                                                multiple="multiple" onchange="calcAvailAccomo();">
                                            <?
                                                $sqltents = $db->query("SELECT * FROM tents WHERE tent_active = 1 AND type = 2 ORDER BY tent_title");
                                                while ($rowt = $sqltents->fetch(PDO::FETCH_ASSOC)) {
                                                    echo '<option value="' . $rowt['tent_id'] . '" ';
                                                    echo '>
																' . $rowt['tent_title'] . '
																</option>';
                                                }
                                            ?>

                                        </select>
                                    </div>
                                </div>

                                <div class="form-group col-sm-4">
                                    <label><?= seat; ?>  </label>
                                    <div id="TentsArea">
                                        <select name="seat" class="form-control select2">

                                            <option value="1"><?= with_seat ?></option>
                                            <option value="0"><?= without_seat ?></option>
                                        </select>
                                    </div>
                                </div>


                            </div>

                            <input type="submit" class="btn btn-primary col-xs-12" value="<?= LBL_ApplyAccomo; ?>"/>

                        </form>
                    </div>
                </div>
                
                <?
                    if ($_POST) { ?>
                        <div class="box">
                            <div class="box-body">
                                
                                <?
                                    
                                    if (is_array($_POST['city_id']) && sizeof($_POST['city_id']) > 0) {
                                        $pilsaccomo = 0;
                                        $count = array();
                                        
                                        // Accomodation notification message
                                        $noti_message = "أخي الحاج- أختي الحاجة
بامكانك معرفة تفاصيل سكنك بمنى وإمكانية إصدار بطاقة إلكترونية
عبر تطبيق شركة العلياني لحجاج الداخل .
";
                                        
                                        
                                        $i = 0;
                                        foreach ($_POST['city_id'] as $city_id) {
                                            $city_title = $db->query("SELECT city_title_$lang FROM cities WHERE city_id = $city_id")->fetchColumn();
                                            $count[$city_title] = 0;
                                            
                                            if ($_POST['gender']) $sqlmore1 = " AND pil_gender='" . $_POST['gender'] . "'";
                                            if ($_POST['pilc_id']) $sqlmore2 = " AND pil_pilc_id='" . $_POST['pilc_id'] . "'";
                                            
                                            $sql1 = $db->query("SELECT pil_id, pil_code, pil_gender FROM pils WHERE pil_city_id = $city_id AND (pil_code NOT IN (SELECT pil_code FROM pils_accomo) OR pil_code IN (SELECT pil_code FROM pils_accomo WHERE halls_id = 0)) $sqlmore1 $sqlmore2 ORDER BY pil_reservation_number");
                                            if ($sql1->rowCount() > 0) {
                                                
                                                while ($row1 = $sql1->fetch(PDO::FETCH_ASSOC)) {
                                                    $accomodated = false;
                                                    
                                                    
                                                    if (!$accomodated && is_array($_POST['hallsarfa_id']) && sizeof($_POST['hallsarfa_id']) > 0) {
                                                        // accomodate to suites
                                                        $accomodated = Accomohallsarfa($_POST['hallsarfa_id'][$i], $row1['pil_code'], $row1['pil_gender'], $_POST['seat']);
                                                        if ($accomodated) {
                                                            
                                                            $count[$city_title]++;
                                                            sendPushNotification(0, null, $noti_message, 2, $row1['pil_id'], 0, 'silent', false, false);
                                                            //sendSMSPilGeneral($row1['pil_id'], $noti_message);
                                                            
                                                        }
                                                    }
                                                    
                                                    
                                                }
                                                
                                                echo LBL_SuccessAccomo . ' <b>' . $count[$city_title] . '</b> ' . HM_Pilgrims . ' ' . LBL_In . ' ' . $city_title . ' <br />';
                                                
                                            } else {
                                                
                                                echo LBL_NoPilsFoundToAccomodate . ' ' . LBL_In . ' ' . $city_title . ' <br />';
                                                
                                            }
                                            
                                            $i++;
                                        }
                                        
                                    } else {
                                        
                                        echo '<center>
											' . LBL_ChooseAtLeastOneCity . '
											</center>';
                                    
                                    }
                                ?>

                            </div><!-- /.box-body -->
                        </div><!-- /.box -->
                    <?php } ?>
            </div><!--/.col (right) -->

        </div>   <!-- /.row -->
    </section><!-- /.content -->

</div>

<?php include 'layout/footer.php'; ?>
<script>
    $('select').select2();

    function suites_selected() {

        $('#hallsarea').html('<?=LBL_Loading;?>');

        var data = {
            suites: $('#suite_id\\[\\]').val(),
            selectedhalls: $('#hall_id\\[\\]').val(),
            gender: $('#gender').val()
        };

        $.post('post/accomo_suites_selected.php', data, function (response) {

            $('#hallsarea').html(response.html);
            $('select').select2();

        }, 'json');

    }

    function bldtype_selected(bld_type) {

        $('#buildingsarea').html('<?=LBL_Loading;?>');

        var data = {
            bld_type: bld_type,
            gender: $('#gender').val()
        };

        $.post('post/accomo_bldtype_selected.php', data, function (response) {
            $('#buildingsarea').html(response);
            $('select').select2();

        });

    }

    function bld_selected() {

        $('#floorsarea').html('<?=LBL_Loading;?>');

        var data = {
            blds: $('#building_id\\[\\]').val(),
            gender: $('#gender').val()
        };

        $.post('post/accomo_buildings_selected.php', data, function (response) {

            $('#floorsarea').html(response);
            $('select').select2();

        });

    }

    function floors_selected() {

        $('#roomsarea').html('<?=LBL_Loading;?>');

        var data = {
            floors: $('#floor_id\\[\\]').val(),
            gender: $('#gender').val()
        };

        $.post('post/accomo_floors_selected.php', data, function (response) {

            $('#roomsarea').html(response);
            $('select').select2();

        });

    }

    function countCitiesPils() {

        $('#countpils').html('<?=LBL_Loading;?>');

        var data = {
            cities: $('#city_id\\[\\]').val(),
            gender: $('#gender').val(),
            pilc_id: $('#pilc_id').val(),
            type: 'arafa',
        };

        $.post('post/countCitiesPils.php', data, function (response) {

            $('#countpils').html(response);

        });

    }

    function calcAvailAccomo() {

        $('#availcount').html('<?=LBL_Loading;?>');

        var data = {
            suites: $('#suite_id\\[\\]').val(),
            halls: $('#hall_id\\[\\]').val(),
            bld_type: $('#bld_type').val(),
            buildings: $('#bld_id\\[\\]').val(),
            floors: $('#floor_id\\[\\]').val(),
            rooms: $('#room_id\\[\\]').val(),
            tents: $('#tent_id\\[\\]').val(),
            gender: $('#gender').val(),
            halls_arfa: $('#halls_arfa\\[\\]').val()
        };
        $.post('post/calcAvailAccomo.php', data, function (response) {

            if (response.availcount) $('#availcount').html(response.availcount);
            else $('#availcount').html('0');

        }, 'json');

    }

    function getAccomosGender(gender) {

        $('#SuitesArea').html('<?=LBL_Loading;?>');
        $('#BuildingsTypeArea').html('<?=LBL_Loading;?>');
        $('#TentsArea').html('<?=LBL_Loading;?>');

        var data = {
            gender,
            type: 2
        };

        $.post('post/getAccomosGender.php', data, function (response) {
            $('#SuitesArea').html(response.SuitesArea);
            $('#BuildingsTypeArea').html(response.BuildingsTypeArea);
            $('#TentsArea').html(response.TentsArea);
            $('select').select2();
        }, 'json');

    }
</script>
