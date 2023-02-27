<?php
    global $db, $lang, $url;
    
    if (!empty($_POST)) {
        
        try {
            
            $db->beginTransaction();
            if ($_POST['type'] == 4) $staff_id = $_POST['manager_staff_id'];
            elseif ($_POST['type'] == 6) $staff_id = $_POST['supervisor_staff_id'];
            else $staff_id = 0;
            $countdevices = sendPushNotification($_POST['platform'], $_POST['title'], $_POST['message'], $_POST['type'], $_POST['pil_id'], $staff_id, $_POST['sound'], true, true);
            
            $msg = '<div class="alert alert-success alert-dismissable"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button><h4><i class="icon fa fa-check"></i>' . LBL_NotificationSent . '</h4>' . LBL_Devices . ': ' . $countdevices . '</div>';
            
            $db->commit();
            
        } catch (PDOException $e) {
            
            $db->rollBack();
            $msg = '<div class="alert alert-danger alert-dismissable"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>' . $e->getMessage() . ' - Line:' . $e->getLine() . '</div>';
            
        }
    }

?>
<!-- Content Wrapper. Contains page content -->

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?= HM_SendNewPushNotification ?>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <!-- left column -->
            <div class="col-md-12">
                
                <?= $msg??'' ?>

                <!-- Input addon -->
                <div class="box box-info">
                    <div class="box-body">
                        <form role="form" method="post" enctype="multipart/form-data">

                            <div class="form-group">
                                <label><?= LBL_Sound ?></label>
                                <select name="sound" id="sound" class="form-control select2">
                                    <option value="default" <?php if (isset($_POST['sound']) && $_POST['sound'] == 0) echo 'selected="selected"'; ?>><?= LBL_Default ?></option>
                                    <option value=""><?= LBL_Silent ?></option>
                                </select>
                            </div>


                            <div class="form-group">
                                <label><?= LBL_Platforms ?></label>
                                <select name="platform" id="platform" class="form-control select2">
                                    <option value="0" <?php if (isset($_POST['platform']) && $_POST['platform'] == 0) echo 'selected="selected"'; ?>><?= LBL_Both ?></option>
                                    <option value="1" <?php if (isset($_POST['platform']) && $_POST['platform'] == 1) echo 'selected="selected"'; ?>><?= LBL_iPhone ?></option>
                                    <option value="2" <?php if (isset($_POST['platform']) && $_POST['platform'] == 2) echo 'selected="selected"'; ?>><?= LBL_Android ?></option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label><?= LBL_Type ?></label>
                                <select name="type" id="type" class="form-control select2"
                                        onchange="checktype(this.value);">
                                    <option value="0" <?php if (isset($_POST['type']) && $_POST['type'] == 0) echo 'selected="selected"'; ?>><?= LBL_All ?></option>
                                    <option value="1" <?php if (isset($_POST['type']) && $_POST['type'] == 1) echo 'selected="selected"'; ?>><?= LBL_AllPilgrims ?></option>
                                    <option value="2" <?php if (isset($_POST['type']) && $_POST['type'] == 2) echo 'selected="selected"'; ?>><?= LBL_SPECIFICPIL ?></option>
                                    <option value="3" <?php if (isset($_POST['type']) && $_POST['type'] == 3) echo 'selected="selected"'; ?>><?= LBL_AllManagers ?></option>
                                    <option value="4" <?php if (isset($_POST['type']) && $_POST['type'] == 4) echo 'selected="selected"'; ?>><?= LBL_SPECIFICMANAGER ?></option>
                                    <option value="5" <?php if (isset($_POST['type']) && $_POST['type'] == 5) echo 'selected="selected"'; ?>><?= LBL_AllSupervisors ?></option>
                                    <option value="6" <?php if (isset($_POST['type']) && $_POST['type'] == 6) echo 'selected="selected"'; ?>><?= LBL_SPECIFICSUPERVISOR ?></option>
                                </select>
                            </div>

                            <div class="form-group" id="pilarea" style="display:none">
                                <label><?= LBL_Pilgrim ?></label>
                                <select name="pil_id" id="pil_id" class="form-control select2">
                                    <option value="0">
                                        <?= LBL_Choose ?>
                                    </option>
                                    <?php
                                        $sqlu = $db->query("SELECT pil_id, pil_name FROM pils ORDER BY pil_name");
                                        while ($rowu = $sqlu->fetch(PDO::FETCH_ASSOC)) {
                                            
                                            echo '<option value="' . $rowu['pil_id'] . '">' . $rowu['pil_name'] . '</option>';
                                            
                                        }
                                    ?>

                                </select>

                            </div>

                            <div class="form-group" id="managersarea" style="display:none">
                                <label><?= HM_Managers ?></label>
                                <select name="manager_staff_id" id="manager_staff_id" class="form-control select2">
                                    <option value="0">
                                        <?= LBL_Choose ?>
                                    </option>
                                    <?php
                                        $sqlu = $db->query("SELECT staff_id, staff_name FROM staff WHERE staff_type = 1 ORDER BY staff_name");
                                        while ($rowu = $sqlu->fetch(PDO::FETCH_ASSOC)) {
                                            
                                            echo '<option value="' . $rowu['staff_id'] . '">' . $rowu['staff_name'] . '</option>';
                                            
                                        }
                                    ?>

                                </select>

                            </div>

                            <div class="form-group" id="supervisorsarea" style="display:none">
                                <label><?= HM_Supervisors ?></label>
                                <select name="supervisor_staff_id" id="supervisor_staff_id"
                                        class="form-control select2">
                                    <option value="0">
                                        <?= LBL_Choose ?>
                                    </option>
                                    <?php
                                        $sqlu = $db->query("SELECT staff_id, staff_name FROM staff WHERE staff_type = 2 ORDER BY staff_name");
                                        while ($rowu = $sqlu->fetch(PDO::FETCH_ASSOC)) {
                                            
                                            echo '<option value="' . $rowu['staff_id'] . '">' . $rowu['staff_name'] . '</option>';
                                            
                                        }
                                    ?>

                                </select>

                            </div>

                            <div class="form-group">
                                <label><?= LBL_Address ?></label>
                                <input type="text" name="title" class="form-control" value="<?= $_POST['title']??'' ?>"/>
                            </div>

                            <div class="form-group">
                                <label><?= LBL_Message ?></label>
                                <textarea name="message" id="message"
                                          class="form-control"><?= $_POST['message']??'' ?></textarea>
                            </div>

                    </div><!-- /.box-body -->
                </div><!-- /.box -->

            </div><!--/.col (left) -->
            <div class="col-md-12">

                <input type="submit" class="col-md-12 btn btn-success" value="<?= LBL_SendMessage ?>"/>
                </form>
            </div>
        </div>   <!-- /.row -->
    </section><!-- /.content -->

</div>
<script>

    function checktype(type_id) {

        $('#pil_id').val(0);
        $('#manager_staff_id').val(0);
        $('#supervisor_staff_id').val(0);

        if (type_id == 0) {

            // All
            $('#pilarea').fadeOut();
            $('#managersarea').fadeOut();
            $('#supervisorsarea').fadeOut();

        } else if (type_id == 1) {

            // All Pilgrims
            $('#pilarea').fadeOut();
            $('#managersarea').fadeOut();
            $('#supervisorsarea').fadeOut();

        } else if (type_id == 2) {

            // Specific Pilgrim
            $('#pilarea').fadeIn();
            $('#managersarea').fadeOut();
            $('#supervisorsarea').fadeOut();

        } else if (type_id == 3) {

            // All Managers
            $('#pilarea').fadeOut();
            $('#managersarea').fadeOut();
            $('#supervisorsarea').fadeOut();

        } else if (type_id == 4) {

            // Specific Manager
            $('#pilarea').fadeOut();
            $('#managersarea').fadeIn();
            $('#supervisorsarea').fadeOut();

        } else if (type_id == 5) {

            // All Supervisors
            $('#pilarea').fadeOut();
            $('#managersarea').fadeOut();
            $('#supervisorsarea').fadeOut();
        } else if (type_id == 6) {

            // Specific Supervisor
            $('#pilarea').fadeOut();
            $('#managersarea').fadeOut();
            $('#supervisorsarea').fadeIn();
        }
        $('select').select2();
        

    }

</script>
