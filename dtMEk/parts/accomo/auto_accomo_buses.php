<?php
    global $db, $session, $lang, $url;
    $title = HM_AutoAccomoBuses;
    
    if (isset($_GET['removeall']) && $_GET['removeall'] == 1) {
        
        $sqldel = $db->query("UPDATE pils SET pil_bus_id = 0");
        
    }

?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?= $title ?>
            <a href="?removeall=1" onclick="return confirm('<?= LBL_RemoveAccomoConfirm ?>');"
               class="btn btn-danger pull-<?= DIR_AFTER ?>" style="margin-<?= DIR_AFTER ?>: 10px"><i
                        class="fa fa-trash"></i> <?= BTN_REMOVEACCOMO ?></a>
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
                        <form method="post" enctype="multipart/form-data">

                            <div class="panel well">
                                <?= LBL_NotAccomoPilgrims ?>: <span id="countpils">0</span>
                                - <?= LBL_AvailableForAccomo ?>: <span id="availcount">0</span>
                            </div>


                            <div class="row">
                                <div class="form-group col-sm-4">
                                    <label><?= LBL_Cities ?></label>
                                    <select name="city_id[]" id="city_id[]" class="form-control select2"
                                            multiple="multiple"
                                            onchange="cities_selected(); countCitiesPilsBuses(); calcAvailAccomo();">
                                        <?php
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
                                    <label><?= LBL_Gender ?></label>
                                    <select name="gender" id="gender" class="form-control select2"
                                            onchange="countCitiesPilsBuses(); calcAvailAccomo();">
                                        <option value="0">
                                            <?= LBL_All ?>
                                        </option>
                                        <option value="m" <?php if (isset($_GET['gender']) && $_GET['gender'] == 'm') echo 'selected="selected"'; ?>>
                                            <?= LBL_Male ?>
                                        </option>
                                        <option value="f" <?php if (isset($_GET['gender']) && $_GET['gender'] == 'f') echo 'selected="selected"'; ?>>
                                            <?= LBL_Female ?>
                                        </option>
                                    </select>
                                </div>
                                <div class="form-group col-sm-4">
                                    <label><?= LBL_Class ?></label>
                                    <select name="pilc_id" id="pilc_id" class="form-control select2"
                                            onchange="countCitiesPilsBuses();">
                                        <option value="0">
                                            <?= LBL_All ?>
                                        </option>
                                        <?php
                                            $sqlpilc = $db->query("SELECT * FROM pils_classes WHERE pilc_active = 1 ORDER BY pilc_title_" . $lang);
                                            while ($rowpc = $sqlpilc->fetch(PDO::FETCH_ASSOC)) {
                                                echo '<option value="' . $rowpc['pilc_id'] . '" ';
                                                if (isset($_GET['pilc_id']) && $_GET['pilc_id'] == $rowpc['pilc_id']) echo 'selected="selected"';
                                                echo '>
															' . $rowpc['pilc_title_' . $lang] . '
															</option>';
                                            }
                                        ?>

                                    </select>
                                </div>
                            </div>

                            <div id="busesarea"></div>


                            <input type="submit" class="btn btn-primary col-xs-12" value="<?= LBL_ApplyAccomo ?>"/>

                        </form>
                    </div>
                </div>
    
                <?php
                    if ($_POST) { ?>
                        <div class="box">
                            <div class="box-body">
    
                                <?php
                                    if (isset($_POST['city_id']) && is_array($_POST['city_id']) && count($_POST['city_id']) > 0) {
                                        $pilsaccomo = 0;
                                        $count = array();
                                        
                                        // Accomodation notification message
                                        $noti_message = "أخي الحاج- أختي الحاجة
بإمكانك معرفة  الباص والمرشد الخاص بك وإصدار بطاقتك الإلكترونية
عبر تطبيق شركة العلياني لحجاج الداخل .";
                                        
                                        $buses_accomodated = array();
                                        foreach ($_POST['city_id'] as $city_id) {
                                            
                                            $city_title = $db->query("SELECT city_title_$lang FROM cities WHERE city_id = $city_id")->fetchColumn();
                                            $count[$city_title] = 0;
                                            $sqlmore1 = $sqlmore2 = '';
                                            
                                            if ($_POST['gender']) $sqlmore1 = " AND pil_gender='" . $_POST['gender'] . "'";
                                            if ($_POST['pilc_id']) $sqlmore2 = " AND pil_pilc_id='" . $_POST['pilc_id'] . "'";
                                            
                                            
                                            $sql1 = $db->query("SELECT pil_id, pil_code, pil_gender, pil_city_id, pil_reservation_number FROM pils WHERE pil_city_id = $city_id AND pil_bus_id = 0 $sqlmore1 $sqlmore2 ORDER BY pil_reservation_number");
                                            
                                            if ($sql1->rowCount() > 0) {
                                                $families = sortPilsFamilies($sql1->fetchAll(PDO::FETCH_ASSOC));
                                                $c = 0;
                                                foreach ($families as $res=>$family) {
                                                    
                                                    $accomodated = AccomoBuses($_POST['bus_id'], getPilCodeForFamily($family), '', $family[0]['pil_city_id'], $city_id);
                                                    
                                                    if ($accomodated) {
                                                        if (!in_array($accomodated, $buses_accomodated)) $buses_accomodated[] = $accomodated;
                                                        
                                                        $count[$city_title] += count($family);
                                                        
                                                        foreach ($family as $pil) {
                                                            sendPushNotification(0, null, $noti_message, 2, $pil['pil_id'], 0, 'silent', false, false);;
                                                        }
                                                    }
                                                }
                                                
                                                echo LBL_SuccessAccomo . ' <b>' . $count[$city_title] . '</b> ' . HM_Pilgrims . ' ' . LBL_In . ' ' . $city_title . ' <br />';
                                                
                                            } else {
                                                
                                                echo LBL_NoPilsFoundToAccomodate . ' ' . LBL_In . ' ' . $city_title . ' <br />';
                                                
                                            }
                                            
                                        }
                                        
                                        // Check if buses accomodated, send notis to supervisors
                                        if (is_array($buses_accomodated) && count($buses_accomodated) > 0) {
                                            
                                            foreach ($buses_accomodated as $bus_id) {
                                                
                                                $noti_message_for_sv = "اخي المرشد
										 بإمكانك الان معرفة الحجاج التابعين لك ومعرفة عددهم وكل تفاصيل الحاج عبر صفحتك بتطبيق العلياني لحجاج الداخل
										 ليهم";
                                                $bus_sv = $db->query("SELECT bus_staff_id FROM buses WHERE bus_id = " . $bus_id)->fetchColumn();
                                                if ($bus_sv > 0) {
                                                    
                                                    sendPushNotification(0, null, $noti_message_for_sv, 6, 0, $bus_sv, 'silent', true, false);
                                                    
                                                }
                                                
                                                
                                            }
                                            
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

<script>
    $('select').select2();

    function cities_selected() {

        $('#busesarea').html('<?=LBL_Loading?>');

        var data = {
            cities: $('#city_id\\[\\]').val()
        };

        $.post('<?= CP_PATH ?>/post/accomo_buses_cities_selected', data, function (response) {

            $('#busesarea').html(response);
            $('select').select2();

        });

    }

    function countCitiesPilsBuses() {

        $('#countpils').html('<?=LBL_Loading?>');

        var data = {
            cities: $('#city_id\\[\\]').val(),
            gender: $('#gender').val(),
            pilc_id: $('#pilc_id').val()
        };

        $.post('<?= CP_PATH ?>/post/countCitiesPilsBuses', data, function (response) {

            $('#countpils').html(response);

        });

    }

    function calcAvailAccomo() {

        $('#availcount').html('<?=LBL_Loading?>');

        var data = {
            buses: $('#bus_id\\[\\]').val(),
            gender: $('#gender').val()
        };

        $.post('<?= CP_PATH ?>/post/calcAvailAccomoBuses', data, function (response) {

            if (response.availcount) $('#availcount').html(response.availcount);
            else $('#availcount').html('0');

        }, 'json');

    }


</script>
