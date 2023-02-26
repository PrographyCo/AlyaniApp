<?php
    global $db, $session, $lang, $url;
    
    $edit = false;
    $title = HM_PilgrimsBuses;
    $table = 'buses';
    $table_id = 'bus_id';
    
    if (isset($_REQUEST['id']) && is_numeric($_REQUEST['id'])) $id = $_REQUEST['id'];
    else $id = '';
    
    if ($_POST) {
        
        $chk = $db->prepare("SELECT $table_id FROM $table WHERE bus_title = :title AND bus_city_id = :city_id AND $table_id != '$id' LIMIT 1");
        $chk->bindValue("title", $_POST['tent_title']??'');
        $chk->bindValue("city_id", $_POST['bus_city_id']??'');
        $chk->execute();
        
        if ($chk->rowCount() > 0) {
            
            $msg = '<div class="alert alert-danger alert-dismissable"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button><h4><i class="icon fa fa-check"></i>' . LBL_Error . '</h4>' . LBL_Title . ' ' . LBL_AlreadyExists . '</div>';
            
        } else {
            
            try {
                
                $sendnoti = false;
                if ($id > 0) $old_staff_id = $db->query("SELECT bus_staff_id FROM $table WHERE $table_id = $id")->fetchColumn();
                else $old_staff_id = 0;
                
                if ($_POST['bus_staff_id'] > 0 && $old_staff_id != $_POST['bus_staff_id']) $sendnoti = true;
                
                $sql = $db->prepare("REPLACE INTO $table VALUES (
			:id,
			:bus_title,
			:bus_city_id,
			:bus_seats,
			:bus_staff_id,
			:bus_order,
			:bus_active,
			:bus_dateadded,
			:bus_lastupdated
			)");
                
                $sql->bindValue("id", $id);
                $sql->bindValue("bus_title", $_POST['bus_title']);
                $sql->bindValue("bus_city_id", $_POST['bus_city_id']);
                $sql->bindValue("bus_seats", $_POST['bus_seats']);
                $sql->bindValue("bus_staff_id", $_POST['bus_staff_id']);
                $sql->bindValue("bus_order", $_POST['bus_order']);
                $sql->bindValue("bus_active", (isset($_POST['bus_active']) ? 1 : 0));
                $sql->bindValue("bus_dateadded", ($_POST['bus_dateadded'] ?? time()));
                $sql->bindValue("bus_lastupdated", time());
                
                if ($sql->execute()) {
                    $result = '';
                    $id = $db->lastInsertId();
                    
                    if ($sendnoti) {
                        // Accomodation notification message
                        $noti_message = "اخي المرشد
بإمكانك الان معرفة الحجاج التابعين لك ومعرفة عددهم وكل تفاصيل الحاج عبر صفحتك بتطبيق العلياني لحجاج الداخل
ليهم";
                        sendPushNotification(0, null, $noti_message, 6, 0, $_POST['bus_staff_id'], 'silent', true, false);
                        sendSMSStaffGeneral($_POST['bus_staff_id'], $noti_message);
                        
                    }
                    
                    if ($_REQUEST['id'] > 0) $label = LBL_Updated;
                    else $label = LBL_Added;
                    
                    $msg = '<div class="alert alert-success alert-dismissable"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button><h4><i class="icon fa fa-check"></i>' . $label . '!</h4>' . LBL_Item . ' ' . $label . ' ' . LBL_Successfully . '<br />' . $result . '</div>';
                    
                    $_POST = [];
                    
                } else {
                    
                    $msg = '<div class="alert alert-danger alert-dismissable"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button><h4><i class="icon fa fa-check"></i>' . LBL_Error . '</h4>' . LBL_ErrorUpdateAdd . '</div>';
                    
                }
                
            } catch (PDOException $e) {
                
                $msg = '<div class="alert alert-danger alert-dismissable"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button><h4><i class="icon fa fa-check"></i>' . LBL_Error . '</h4>' . LBL_ErrorDB . ' ' . $e->getMessage() . '</div>';
                
            }
            
        }
        
    }
    
    if (is_numeric($id)) {
        
        $edit = true;
        $row = $db->query("SELECT * FROM $table WHERE $table_id = " . $id)->fetch();
        
    }

?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?= $title ?>
            <small><?= $edit ? LBL_Edit : LBL_New ?></small>
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
                                <label><?= LBL_BusNumber ?></label>
                                <input type="text" class="form-control" name="bus_title" required="required"
                                       value="<?= $_POST['bus_title'] ?? $row['bus_title'] ?? '' ?>"/>
                            </div>

                            <div class="form-group">
                                <label><?= LBL_City ?></label>
                                <select name="bus_city_id" class="form-control select2">
                                    <option value="0"><?= LBL_Choose ?></option>
                                    <?php
                                        $sqlcities = $db->query("SELECT city_id, city_title_en, city_title_ar FROM cities ORDER BY city_title_" . $lang);
                                        while ($rowcities = $sqlcities->fetch(PDO::FETCH_ASSOC)) {
                                            
                                            echo '<option value="' . $rowcities['city_id'] . '" ';
                                            if ((isset($row) && $row['bus_city_id'] == $rowcities['city_id']) || (isset($_POST['bus_city_id']) && $_POST['bus_city_id'] == $rowcities['city_id'])) echo 'selected="selected"';
                                            echo '>' . $rowcities['city_title_' . $lang] . '</option>';
                                            
                                        }
                                    ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label><?= LBL_BusSeats ?></label>
                                <input type="number" class="form-control" name="bus_seats"
                                       value="<?= $_POST['bus_seats'] ?? $row['bus_seats'] ?? '' ?>"/>
                            </div>

                            <div class="form-group">
                                <label><?= HM_Supervisors ?></label>
                                <select name="bus_staff_id" class="form-control select2">
                                    <option value="0"><?= LBL_Choose ?></option>
                                    <?php
                                        $sql2 = $db->query("SELECT staff_id, staff_name FROM staff WHERE staff_type = 2 AND staff_active = 1 AND staff_priv = 2 ORDER BY staff_name");
                                        while ($row2 = $sql2->fetch(PDO::FETCH_ASSOC)) {
                                            
                                            echo '<option value="' . $row2['staff_id'] . '" ';
                                            if (isset($row) && $row['bus_staff_id'] == $row2['staff_id']) echo 'selected="selected"';
                                            echo '>' . $row2['staff_name'] . '</option>';
                                            
                                        }
                                    ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label><?= LBL_Order ?></label>
                                <input type="text" class="form-control" name="bus_order"
                                       value="<?= $_POST['bus_order'] ?? $row['bus_order'] ?? '' ?>"/>
                            </div>

                            <div class="form-group">
                                <label><input type="checkbox"
                                              name="bus_active" <?php if (!isset($row) || $row['bus_active'] == 1) echo 'checked="checked"'; ?> /> <?= LBL_Active ?>
                                </label>
                            </div>

                            <input type="submit" class="col-md-12 btn btn-success"
                                   value="<?= $edit ? LBL_Update : LBL_Add ?>"/>
                            <input type="hidden" name="id" id="id" value="<?= $id ?>"/>
                            <input type="hidden" name="bus_dateadded" id="bus_dateadded"
                                   value="<?= $row['bus_dateadded'] ?? time() ?>"/>
                        </form>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div><!--/.col (left) -->
        </div>   <!-- /.row -->
    </section><!-- /.content -->

</div>

<script>
    $('select').select2();
</script>
