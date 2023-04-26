<?php
    global $db, $url, $lang, $session;
    $title = LBL_Accomodation;
    
    $pil_id = $_REQUEST['pil_id'] ?? die('pil_id must be provided');
    $type = strtolower($_REQUEST['type'] ?? 'pil');
    $pil_code = ($type === 'emp') ? $pil_id : $db->query("SELECT pil_code FROM pils WHERE pil_id = $pil_id")->fetchColumn();
    $pilc_id = $db->query("SELECT pil_pilc_id FROM pils WHERE pil_id = $pil_id")->fetchColumn();
    
    $gender = 'm';
    if ($type === 'pil') {
        $sql1 = $db->query("SELECT pil_gender FROM pils WHERE pil_id = $pil_id");
        if ($sql1->rowCount() > 0) {
            $gender = $sql1->fetchColumn();
        }
    }
    
    if (!empty($_POST)) {
        $pilsaccomo = 0;
        $count = array();
        
        // Accomodation notification message
        $noti_message = "أخي الحاج- أختي الحاجة
بامكانك معرفة تفاصيل سكنك بمنى وإمكانية إصدار بطاقة إلكترونية
عبر تطبيق شركة العلياني لحجاج الداخل .
";
        
        if (isset($_POST['suite_id']) && is_array($_POST['suite_id']) && count($_POST['suite_id']) > 0) {
            
            // accomodate to suites
            $accomodated = AccomoSuites($_POST['suite_id'], $_POST['hall_id'], $_POST['extratype_id'], $pil_code, $gender, $type, $_POST['stuff_ids']);
            
            if ($accomodated) {
                sendPushNotification(0, null, $noti_message, 2, $pil_id, 0, 'silent', false, false);
            }
            
        }
        
        if (isset($_POST['building_id']) && is_array($_POST['building_id']) && count($_POST['building_id']) > 0) {
            
            // accomodate to buildings
            $accomodated = AccomoBuildings($_POST['building_id'], $_POST['floor_id'], $_POST['room_id'], $pil_code, $_POST['gender'] ?? $gender, $type);
            if ($accomodated) {
                sendPushNotification(0, null, $noti_message, 2, $pil_id, 0, 'silent', false, false);
            }
        }
        
        
        if (isset($_POST['tent_id']) && is_array($_POST['tent_id']) && count($_POST['tent_id']) > 0) {
            
            // accomodate to tents
            $accomodated = AccomoTents($pilc_id,$_POST['tent_id'], $pil_code, $_POST['gender'] ?? $gender, $type);
            if ($accomodated) {
                sendPushNotification(0, null, $noti_message, 2, $pil_id, 0, 'silent', false, false);
            }
        }
        
        Accomohallsarfa([$_POST['pil_accomo_type_arafa']], $pil_code ,$gender ,$_POST['seat']);
        
        $msg = LBL_SuccessAccomo;
        $_POST = [];
    }
    
    
    $sql = $db->query("SELECT * FROM pils_accomo WHERE pil_code = '$pil_code' AND type = '$type'");
    $accomo = $sql->fetch(PDO::FETCH_ASSOC);

?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?= $title ?>
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
                        <form method="post" enctype="multipart/form-data">
                            
                            <div class="panel well">
                                <?= ($type === 'emp') ? HM_STAFF : LBL_Pilgrim ?>: <span
                                    id="countpils"> <?= $pil_code ?> </span>
                                - <?= LBL_AvailableForAccomo ?>: <span id="availcount">0</span>
                            </div>
                            
                            <div class="row">
                                <div class="form-group col-sm-6">
                                    <label><?= LBL_Gender ?></label>
                                    <select name="gender" id="gender" class="form-control select2"
                                            onchange=" calcAvailAccomo(); getAccomosGender(this.value);" readonly
                                            disabled>
                                        <option
                                            value="m" <?php if (($_REQUEST['gender'] ?? $gender) === 'm') echo 'selected="selected"'; ?>>
                                            <?= LBL_Male ?>
                                        </option>
                                        <option
                                            value="f" <?php if (($_REQUEST['gender'] ?? $gender) === 'f') echo 'selected="selected"'; ?>>
                                            <?= LBL_Female ?>
                                        </option>
                                    </select>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label><?= LBL_Class ?></label>
                                    <select name="pilc_id" id="pilc_id" class="form-control select2"
                                            onchange="showClassFields(this);" disabled>
                                        <option value="" disabled readonly selected>
                                            <?= LBL_All ?>
                                        </option>
                                        <?php
                                            $sqlpilc = $db->query("SELECT * FROM pils_classes WHERE pilc_active = 1 ORDER BY pilc_title_" . $lang);
                                            while ($rowpc = $sqlpilc->fetch(PDO::FETCH_ASSOC)) {
                                                echo '<option value="' . $rowpc['pilc_id'] . '" >' . $rowpc['pilc_title_' . $lang] . '</option>';
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row data">
                            
                            </div>
                            
                            <div class="row">
                                <div class="form-group col-sm-6">
                                    <label><?= LBL_TentNumber_halls ?> ( <?= arafa ?> )</label>
                                    <select name="pil_accomo_type_arafa" class="form-control select2">
                                        <option value=""><?= LBL_TentNumber_halls ?></option>
                                        <?php
                                            $sqltents = $db->query("SELECT * FROM tents WHERE tent_active = 1 AND type = 2 AND  tent_gender = '$gender'  ORDER BY tent_title");
                                            while ($rowt = $sqltents->fetch(PDO::FETCH_ASSOC)) {
                                                echo '<option value="' . $rowt['tent_id'] . '"';
                                                echo (($accomo && $accomo['halls_id'] == $rowt['tent_id'])?'selected=selected':'') . '>' . $rowt['tent_title'] . '
                                                    </option>';
                                            }
                                        ?>
                                    
                                    </select>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label><?= with_seat ?>  </label>
                                    <div>
                                        <select name="seat" class="form-control select2">
                                            
                                            <option value="1" <?= ($accomo && $accomo['seats'] == 1)?'selected=selected':'' ?>><?= with_seat ?></option>
                                            <option value="0" <?= ($accomo && $accomo['seats'] == 0)?'selected=selected':'' ?>><?= without_seat ?></option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <input type="submit" class="btn btn-primary col-xs-12" value="<?= LBL_ApplyAccomo ?>" />
                        
                        </form>
                    </div>
                </div>
                
                <?php
                    if (!empty($_POST)) { ?>
                        <div class="box">
                            <div class="box-body">
                                
                                <?= $msg ?? '' ?>
                            
                            </div><!-- /.box-body -->
                        </div><!-- /.box -->
                    <?php } ?>
            </div><!--/.col (right) -->
        
        </div>   <!-- /.row -->
    </section><!-- /.content -->

</div>

<script>
    $(document).ready(function () {
        $('select').select2();
        $('#pilc_id').val(["<?= $pilc_id ?>"]).trigger('change');
    });

    function suites_selected() {
        $('#hallsarea').html('<?=LBL_Loading?>');

        var data = {
            suites: [$('#suite_id\\[\\]').val()],
            selectedhalls: $('#hall_id\\[\\]').val(),
            gender: $('#gender').val(),
            selected: [<?= $accomo ? $accomo['hall_id'] : '' ?>],
            extratype_id: <?= $accomo ? $accomo['extratype_id'] : '0' ?>
        };

        $.post('<?= CP_PATH ?>/post/accomo_suites_selected', data, function (response) {
            
            $('#hallsarea').html(response.html.replace('multiple',''));
            $('select').select2();

            <?php
                if ($accomo && $accomo['hall_id'])
                    echo '$("#hall_id\\\\[\\\\]").val(["'.$accomo['hall_id'].'"]);
                    $("#extratype_id").val(["'.$accomo['extratype_id'].'"]).trigger("change");';
            ?>

        }, 'json');

    }
    
    function hall_selected() {
        $('#extratype_id').val([0]).trigger('change');
    }

    function extratype_selected() {

        $('#type_select').html('');
        
        var data = {
            suites: [$('#suite_id\\[\\]').val()],
            selectedhalls: [$('#hall_id\\[\\]').val()],
            extratype_id: $('#extratype_id').val(),
            selected: [<?= $accomo ? $accomo['stuff_id'] : "" ?>]
        }

        if ($('#extratype_id').val() > 0) {
            $('#type_select').html('<?= LBL_Loading ?>');
            
            $.post('<?= CP_PATH ?>/post/accomo_suites_halls_type_selected', data, function (response) {
                $('#type_select').html(response.html.replace('multiple',''));

                $('select').select2();
            });
        }
    }

    function bldtype_selected(bld_type) {

        $('#buildingsarea').html('<?=LBL_Loading?>');

        var data = {
            bld_type: $('#bld_type'),
            gender: $('#gender').val(),

        };

        $.post('<?= CP_PATH ?>/post/accomo_bldtype_selected', data, function (response) {
            $('#buildingsarea').html(response);
            $('select').select2();
        
            $('#building_id\\[\\]').val(["<?= $accomo ? $accomo['bld_id'] : '' ?>"]).trigger('change');
        });

    }

    function bld_selected() {

        $('#floorsarea').html('<?=LBL_Loading?>');

        var data = {
            blds: [$('#building_id\\[\\]').val()],
            gender: $('#gender').val()
        };

        $.post('<?= CP_PATH ?>/post/accomo_buildings_selected', data, function (response) {

            $('#floorsarea').html(response);
            $('select').select2();

            $('#floor_id\\[\\]').val(["<?= $accomo ? $accomo['floor_id'] : '' ?>"]).trigger('change');
        });

    }

    function floors_selected() {

        $('#roomsarea').html('<?=LBL_Loading?>');

        var data = {
            floors: [$('#floor_id\\[\\]').val()],
            gender: $('#gender').val()
        };

        $.post('<?= CP_PATH ?>/post/accomo_floors_selected', data, function (response) {

            $('#roomsarea').html(response);
            $('select').select2();

            $('#room_id\\[\\]').val(["<?= $accomo ? $accomo['room_id'] : '' ?>"]).trigger('change');
        });

    }

    function calcAvailAccomo() {

        $('#availcount').html('<?=LBL_Loading?>');

        var data = {
            suites: [$('#suite_id\\[\\]').val()],
            halls: [$('#hall_id\\[\\]').val()],
            bld_type: $('#bld_type').val(),
            buildings: [$('#bld_id\\[\\]').val()],
            floors: [$('#floor_id\\[\\]').val()],
            rooms: [$('#room_id\\[\\]').val()],
            tents: [$('#tent_id\\[\\]').val()],
            gender: $('#gender').val(),
            halls_arfa: $('#halls_arfa').val(),
            extratype_id: $('#extratype_id').val()
        };

        $.post('<?= CP_PATH ?>/post/calcAvailAccomo', data, function (response) {
            let availCount = (response.availcount) ? response.availcount : 0;

            $('#availcount').html(availCount);

        }, 'json');

    }

    function getAccomosGender(gender) {

        $('#SuitesArea').html('<?=LBL_Loading?>');
        $('#BuildingsTypeArea').html('<?=LBL_Loading?>');
        $('#TentsArea').html('<?=LBL_Loading?>');

        var data = {
            gender
        };

        $.post('<?= CP_PATH ?>/post/getAccomosGender', data, function (response) {
            $('#SuitesArea').html(response.SuitesArea);
            $('#BuildingsTypeArea').html(response.BuildingsTypeArea);
            $('#TentsArea').html(response.TentsArea);
            $('select').select2();
        }, 'json');

    }

    function showClassFields(el) {
        let html = '';
        switch (el.value) {
            case '1':
            case '10':
                html = generate_type_1();
                break;
            case '2':
                html = generate_type_2();
                break;
            case '0':
                html = '';
                break;
            default:
                html = generate_type_3();
                break;
        }
        document.querySelector('.row.data').innerHTML = html;
        $('select').select2()
        
        <?php
        if ($accomo) {
        
            if ($accomo['suite_id'])
                echo '$("#suite_id\\\\[\\\\]").val(["'. $accomo['suite_id'] .'"]).trigger("change");';
            
            $bld_type = $db->query('SELECT bld_type FROM buildings WHERE bld_id = ' . $accomo['bld_id'])->fetchColumn() ?: '0';
            if ($bld_type !== "0")
                echo "$('#bld_type').val('$bld_type').trigger('change')";
            
            if ($accomo['tent_id'])
                echo '$("#tent_id\\\\[\\\\]").val(["'. $accomo['tent_id'] .'"]).trigger("change");';
            
        }
        ?>
        
    }

    function generate_type_1() {
        return `<div class="form-group col-sm-12">
                    <label><?= LBL_SuiteNumber ?></label>
                    <div id="SuitesArea">
                        
                        <select name="suite_id[]" id="suite_id[]" class="form-control select2" onchange="suites_selected(); calcAvailAccomo();">
                            <?php
                            $sqlsuites = $db->query("SELECT * FROM suites WHERE suite_active = 1 ORDER BY suite_title");
                            while ($rows = $sqlsuites->fetch(PDO::FETCH_ASSOC)) {
                                echo '<option value="' . $rows['suite_id'] . '">' . $rows['suite_title'] . '</option>';
                            }
                            ?>
                        </select>
                        <div id="hallsarea">
                        
                        </div>
                    
                    </div>
                
                </div>`;
    }

    function generate_type_2() {
        return `<div class="form-group col-sm-6 type_2">
                    <label><?= HM_Building . ' ' . LBL_Type ?></label>
                    <div id="BuildingsTypeArea">
                        <select name="bld_type" id="bld_type" class="form-control select2"
                                onchange="bldtype_selected(this.value); calcAvailAccomo();">
                            <option value="0">
                                <?= LBL_All ?>
                            </option>
                            <option value="1">
                                <?= HM_Building ?>
                            </option>
                            <option value="2">
                                <?= LBL_Premises ?>
                            </option>
                        </select>

                        <div id="buildingsarea">

                        </div>
                    </div>
                </div>
                <div class="form-group col-sm-6">
                    <label><?= LBL_TentNumber ?>  </label>
                    <div id="TentsArea">
                        <select name="tent_id[]" id="tent_id[]" class="form-control select2"
                                onchange="calcAvailAccomo();">
                            <option value="" selected disabled><?= LBL_Choose ?></option>
                            <?php
                            $sqltents = $db->query("SELECT * FROM tents WHERE tent_active = 1 AND type = 1 ORDER BY tent_title");
                            while ($rowt = $sqltents->fetch(PDO::FETCH_ASSOC)) {
                                echo '<option value="' . $rowt['tent_id'] . '" ';
                                echo '>' . $rowt['tent_title'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>`;
    }

    function generate_type_3() {
        return `<div class="form-group col-sm-12">
                    <label><?= LBL_TentNumber ?>  </label>
                    <div id="TentsArea">
                        <select name="tent_id[]" id="tent_id[]" class="form-control select2"
                                onchange="calcAvailAccomo();">
                            <option value="" selected disabled><?= LBL_Choose ?></option>
                            <?php
                            $sqltents = $db->query("SELECT * FROM tents WHERE tent_active = 1 AND type = 1 ORDER BY tent_title");
                            while ($rowt = $sqltents->fetch(PDO::FETCH_ASSOC)) {
                                echo '<option value="' . $rowt['tent_id'] . '" ';
                                echo '>' . $rowt['tent_title'] . '</option>';
                            }
                            ?>
                        
                        </select>
                    </div>
                </div>`;
    }
</script>
