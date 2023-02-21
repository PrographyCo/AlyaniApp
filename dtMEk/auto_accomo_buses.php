<?php
    include 'header.php';
    require 'pushfunctions.php';
    require 'msegat.php';
    
    $title = HM_AutoAccomoBuses;
    
    if ($_GET['removeall'] == 1) {
        
        $sqldel = $db->query("UPDATE pils SET pil_bus_id = 0");
        
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
                        <form method="post" action="auto_accomo_buses.php" enctype="multipart/form-data">

                            <div class="panel well">
                                <?= LBL_NotAccomoPilgrims; ?>: <span id="countpils">0</span>
                                - <?= LBL_AvailableForAccomo; ?>: <span id="availcount">0</span>
                            </div>


                            <div class="row">
                                <div class="form-group col-sm-4">
                                    <label><?= LBL_Cities; ?></label>
                                    <select name="city_id[]" id="city_id[]" class="form-control select2"
                                            multiple="multiple"
                                            onchange="cities_selected(); countCitiesPilsBuses(); calcAvailAccomo();">
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
                                            onchange="countCitiesPilsBuses(); calcAvailAccomo();">
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
                                            onchange="countCitiesPilsBuses();">
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

                            <div id="busesarea"></div>


                            <input type="submit" class="btn btn-primary col-xs-12" value="<?= LBL_ApplyAccomo; ?>"/>

                        </form>
                    </div>
                </div>
                
                <?
                    if ($_POST) { ?>
                        <div class="box">
                            <div class="box-body">
                                
                                <?
                                    //error_reporting(E_ALL);
                                    //ini_set('display_errors', 1);
                                    
                                    if (is_array($_POST['city_id']) && sizeof($_POST['city_id']) > 0) {
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
                                            
                                            if ($_POST['gender']) $sqlmore1 = " AND pil_gender='" . $_POST['gender'] . "'";
                                            if ($_POST['pilc_id']) $sqlmore2 = " AND pil_pilc_id='" . $_POST['pilc_id'] . "'";
                                            
                                            
                                            $sql1 = $db->query("SELECT pil_id, pil_code, pil_gender, pil_city_id FROM pils WHERE pil_city_id = $city_id AND pil_bus_id = 0 $sqlmore1 $sqlmore2 ORDER BY pil_reservation_number");
                                            if ($sql1->rowCount() > 0) {
                                                
                                                while ($row1 = $sql1->fetch(PDO::FETCH_ASSOC)) {
                                                    
                                                    $accomodated = false;
                                                    
                                                    $accomodated = AccomoBuses($_POST['bus_id'], $row1['pil_code'], $row1['pil_gender'], $row1['pil_city_id'], $city_id);
                                                    if ($accomodated) {
                                                        if (!in_array($accomodated, $buses_accomodated)) $buses_accomodated[] = $accomodated;
                                                        $count[$city_title]++;
                                                        sendPushNotification(0, null, $noti_message, 2, $row1['pil_id'], 0, 'silent', false, false);
                                                        //sendSMSPilGeneral($row1['pil_id'], $noti_message);
                                                        
                                                    }
                                                    
                                                }
                                                
                                                echo LBL_SuccessAccomo . ' <b>' . $count[$city_title] . '</b> ' . HM_Pilgrims . ' ' . LBL_In . ' ' . $city_title . ' <br />';
                                                
                                            } else {
                                                
                                                echo LBL_NoPilsFoundToAccomodate . ' ' . LBL_In . ' ' . $city_title . ' <br />';
                                                
                                            }
                                            
                                        }
                                        
                                        // Check if buses accomodated, send notis to supervisors
                                        if (is_array($buses_accomodated) && sizeof($buses_accomodated) > 0) {
                                            
                                            foreach ($buses_accomodated as $bus_id) {
                                                
                                                $noti_message_for_sv = "اخي المرشد
										 بإمكانك الان معرفة الحجاج التابعين لك ومعرفة عددهم وكل تفاصيل الحاج عبر صفحتك بتطبيق العلياني لحجاج الداخل
										 ليهم";
                                                $bus_sv = $db->query("SELECT bus_staff_id FROM buses WHERE bus_id = " . $bus_id)->fetchColumn();
                                                if ($bus_sv > 0) {
                                                    
                                                    sendPushNotification(0, null, $noti_message_for_sv, 6, 0, $bus_sv, 'silent', true, false);
                                                    //sendSMSStaffGeneral($bus_sv, $noti_message_for_sv);
                                                    
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

<?php include 'footer.php'; ?>
<script>
    $('select').select2();

    function cities_selected() {

        $('#busesarea').html('<?=LBL_Loading;?>');

        var data = {
            cities: $('#city_id\\[\\]').val()
        };

        $.post('post/accomo_buses_cities_selected.php', data, function (response) {

            $('#busesarea').html(response);
            $('select').select2();

        });

    }

    function countCitiesPilsBuses() {

        $('#countpils').html('<?=LBL_Loading;?>');

        var data = {
            cities: $('#city_id\\[\\]').val(),
            gender: $('#gender').val(),
            pilc_id: $('#pilc_id').val()
        };

        $.post('post/countCitiesPilsBuses.php', data, function (response) {

            $('#countpils').html(response);

        });

    }

    function calcAvailAccomo() {

        $('#availcount').html('<?=LBL_Loading;?>');

        var data = {
            buses: $('#bus_id\\[\\]').val(),
            gender: $('#gender').val()
        };

        $.post('post/calcAvailAccomoBuses.php', data, function (response) {

            if (response.availcount) $('#availcount').html(response.availcount);
            else $('#availcount').html('0');

        }, 'json');

    }


</script>
