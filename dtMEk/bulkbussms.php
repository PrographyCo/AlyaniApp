<?php
    include 'header.php';
    require 'pushfunctions.php';
    require 'msegat.php';
    $title = HM_bulkbussms;
    
    //error_reporting(E_ALL);
    //ini_set('display_errors', 1);

?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?= $title; ?>
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
                        <form method="post" enctype="multipart/form-data" onsubmit="sendbulkSMSBuses(); return false;">


                            <div class="panel well">
                                <?= LBL_AccomoPilgrims; ?>: <span id="countpils">0</span>
                            </div>

                            <div class="row">
                                <div class="form-group col-xs-12">
                                    <label><?= LBL_Cities; ?></label>
                                    <select name="city_id[]" id="city_id[]" class="form-control select2"
                                            multiple="multiple" onchange="countCitiesPils();">
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
                            </div>

                            <input type="submit" id="submitbutton" class="btn btn-primary col-xs-12"
                                   value="<?= LBL_SendSMSs; ?>"/>

                        </form>

                        <div id="bulksmsresult" class="panel well" style="display:none">

                        </div>
                    </div>
                </div>

            </div><!--/.col (right) -->

        </div>   <!-- /.row -->
    </section><!-- /.content -->

</div>

<?php include 'footer.php'; ?>
<script>
    $('select').select2();

    function countCitiesPils() {

        $('#countpils').html('<?=LBL_Loading;?>');

        var data = {
            cities: $('#city_id\\[\\]').val()
        };

        $.post('post/countCitiesPilsBusesAccomo.php', data, function (response) {

            $('#countpils').html(response);

        });

    }

    function sendbulkSMSBuses() {

        var cities = $('#city_id\\[\\]').val();
        $('#bulksmsresult').html('<br /><br /><?=LBL_Loading;?>');
        $('#bulksmsresult').show();
        $('#submitbutton').attr('disabled', true);
        $('#city_id\\[\\]').attr('disabled', true);

        var data = {
            cities
        };

        $.post('post/sendbulkSMSBuses.php', data, function (response) {

            $('#bulksmsresult').html(response);

        });

    }

    function resend() {
        $('#bulksmsresult').html('');
        $('#bulksmsresult').hide();
        $('#submitbutton').attr('disabled', false);
        $('#city_id\\[\\]').attr('disabled', false);
    }
</script>
