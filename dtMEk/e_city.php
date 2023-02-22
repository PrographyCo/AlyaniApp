<?php
    include 'layout/header.php';
    
    $title = HM_Cities;
    $table = 'cities';
    $table_id = 'city_id';
    
    if ($_POST) {
        
        try {
            $sql = $db->prepare("REPLACE INTO $table VALUES (
                           :id,
                           :city_title_ar,
                           :city_title_en,
                           :city_title_ur,
                           :city_prefix,
                           :city_order,
                           :city_active,
                           :city_dateadded,
                           :city_lastupdated
                       )");
            
            // check if new or update
            if (is_numeric($_GET['id'])) $id = $_GET['id'];
            else $id = '';
            
            $sql->bindValue("id", $id);
            $sql->bindValue("city_title_ar", $_POST['city_title_ar']);
            $sql->bindValue("city_title_en", $_POST['city_title_en']);
            $sql->bindValue("city_title_ur", $_POST['city_title_ur']);
            $sql->bindValue("city_prefix", $_POST['city_prefix']);
            $sql->bindValue("city_order", $_POST['city_order']);
            $sql->bindValue("city_active", ($_POST['city_active'] ? 1 : 0));
            $sql->bindValue("city_dateadded", ($_POST['city_dateadded'] ?: time()));
            $sql->bindValue("city_lastupdated", time());
            
            if ($sql->execute()) {
                
                $id = $db->lastInsertId();
                
                // Delete cities_staff records of this city id
                $sqldel = $db->query("DELETE FROM cities_staff WHERE city_id = $id");
                
                // Handling Supervisors
                if (is_array($_POST['city_sv']) && sizeof($_POST['city_sv']) > 0) {
                    
                    foreach ($_POST['city_sv'] as $staff_id) {
                        
                        $sqlins2 = $db->query("INSERT INTO cities_staff VALUES ($id, $staff_id)");
                        
                    }
                    
                }
                
                
                if ($_GET['id'] > 0) $label = LBL_Updated;
                else $label = LBL_Added;
                
                $msg = '<div class="alert alert-success alert-dismissable"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button><h4><i class="icon fa fa-check"></i>' . $label . '!</h4>' . LBL_Item . ' ' . $label . ' ' . LBL_Successfully . '<br />' . $result . '</div>';
                
                unset($_POST);
                
            } else {
                
                $msg = '<div class="alert alert-danger alert-dismissable"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button><h4><i class="icon fa fa-check"></i>' . LBL_Error . '</h4>' . LBL_ErrorUpdateAdd . '</div>';
                
            }
            
        } catch (PDOException $e) {
            
            $msg = '<div class="alert alert-danger alert-dismissable"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button><h4><i class="icon fa fa-check"></i>' . LBL_Error . '</h4>' . LBL_ErrorDB . ' ' . $e->getMessage() . '</div>';
            
        }
        
    }
    
    if (is_numeric($_GET['id'])) {
        
        $edit = true;
        $row = $db->query("SELECT * FROM $table WHERE $table_id = " . $_GET['id'])->fetch();
        
    }

?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?= $title; ?>
            <small><?php echo $edit ? LBL_Edit : LBL_New; ?></small>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <!-- left column -->
            <div class="col-md-12">
                
                <?php echo $msg; ?>
                <!-- Input addon -->
                <div class="box box-info">

                    <div class="box-body">
                        <form role="form" method="post" enctype="multipart/form-data">

                            <div class="row">

                                <div class="form-group col-sm-4">
                                    <label><?= LBL_TitleAr; ?></label>
                                    <input type="text" class="form-control" name="city_title_ar" required="required"
                                           value="<?php echo $_POST['city_title_ar'] ?: $row['city_title_ar'] ?>"/>
                                </div>

                                <div class="form-group col-sm-4">
                                    <label><?= LBL_TitleEn; ?></label>
                                    <input type="text" class="form-control" name="city_title_en" required="required"
                                           value="<?php echo $_POST['city_title_en'] ?: $row['city_title_en'] ?>"/>
                                </div>

                                <div class="form-group col-sm-4">
                                    <label><?= LBL_TitleUr; ?></label>
                                    <input type="text" class="form-control" name="city_title_ur" required="required"
                                           value="<?php echo $_POST['city_title_ur'] ?: $row['city_title_ur'] ?>"/>
                                </div>

                            </div>

                            <div class="form-group">
                                <label><?= LBL_Prefix; ?></label>
                                <input type="text" class="form-control" name="city_prefix" require="required"
                                       value="<?php echo $_POST['city_prefix'] ?: $row['city_prefix'] ?>"/>
                            </div>

                            <div class="form-group">
                                <label><?= LBL_Order; ?></label>
                                <input type="text" class="form-control" name="city_order"
                                       value="<?php echo $_POST['city_order'] ?: $row['city_order'] ?>"/>
                            </div>

                            <div class="form-group">
                                <label><?= HM_Supervisors; ?></label>
                                <select name="city_sv[]" class="form-control select2" multiple="multiple">
                                    <?
                                        if ($row) $sv_array = $db->query("SELECT staff_id FROM cities_staff WHERE city_id = " . $row['city_id'] . " AND staff_id IN (SELECT staff_id FROM staff WHERE staff_type = 2 AND staff_active = 1)")->fetchAll(PDO::FETCH_COLUMN);
                                        else $sv_array = array();
                                        $sql2 = $db->query("SELECT staff_id, staff_name FROM staff WHERE staff_type = 2 AND staff_active = 1 AND staff_priv = 1 ORDER BY staff_name");
                                        while ($row2 = $sql2->fetch(PDO::FETCH_ASSOC)) {
                                            
                                            echo '<option value="' . $row2['staff_id'] . '" ';
                                            if (in_array($row2['staff_id'], $sv_array) || (is_array($_POST['city_sv']) && in_array($row2['staff_id'], $_POST['city_sv']))) echo 'selected="selected"';
                                            echo '>' . $row2['staff_name'] . '</option>';
                                            
                                        }
                                    ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label><input type="checkbox"
                                              name="city_active" <?php if (!$row || $row['city_active'] == 1) echo 'checked="checked"'; ?> /> <?= LBL_Active; ?>
                                </label>
                            </div>

                            <input type="submit" class="col-md-12 btn btn-success"
                                   value="<?php echo $edit ? LBL_Update : LBL_Add; ?>"/>
                            <input type="hidden" name="id" id="id" value="<?= $_GET['id']; ?>"/>
                            <input type="hidden" name="oldpassword" id="oldpassword"
                                   value="<?= $row['staff_password']; ?>"/>
                            <input type="hidden" name="staff_dateadded" id="staff_dateadded"
                                   value="<?= $row['staff_dateadded']; ?>"/>
                        </form>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div><!--/.col (left) -->
        </div>   <!-- /.row -->
    </section><!-- /.content -->

</div>

<?php include 'layout/footer.php'; ?>
<script>
    $('select').select2();
    $("#staff_uphoto").fileinput({
        showRemove: true,
        showUpload: false,
        showCancel: false,
        showPreview: true,
        minFileCount: 0,
        maxFileCount: 1,
        allowedFileTypes: ['image', 'pdf']
    });
</script>
