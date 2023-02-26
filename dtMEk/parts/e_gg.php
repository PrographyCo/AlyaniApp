<?php include 'layout/header.php';
    
    $title = HM_GENERALGUIDE;
    $table = 'general_guide';
    $table_id = 'gg_id';
    
    if ($_POST) {
        
        try {
            $sql = $db->prepare("REPLACE INTO $table VALUES (
	:id,
	:gg_title_ar,
	:gg_title_en,
	:gg_title_ur,
	:gg_descr_ar,
	:gg_descr_en,
	:gg_descr_ur,
	:gg_photo,
	:gg_order,
	:gg_active,
	:gg_dateadded,
	:gg_lastupdated
	)");
            
            // check if new or update
            if (is_numeric($_GET['id'])) $id = $_GET['id'];
            else $id = '';
            
            $sql->bindValue("id", $id);
            $sql->bindValue("gg_title_ar", $_POST['gg_title_ar']);
            $sql->bindValue("gg_title_en", $_POST['gg_title_en']);
            $sql->bindValue("gg_title_ur", $_POST['gg_title_ur']);
            $sql->bindValue("gg_descr_ar", $_POST['gg_descr_ar']);
            $sql->bindValue("gg_descr_en", $_POST['gg_descr_en']);
            $sql->bindValue("gg_descr_ur", $_POST['gg_descr_ur']);
            $sql->bindValue("gg_photo", $_POST['gg_photo']);
            $sql->bindValue("gg_order", $_POST['gg_order']);
            $sql->bindValue("gg_active", ($_POST['gg_active'] ? 1 : 0));
            $sql->bindValue("gg_dateadded", ($_POST['gg_dateadded'] ?: time()));
            $sql->bindValue("gg_lastupdated", time());
            
            if ($sql->execute()) {
                
                $id = $db->lastInsertId();
                
                if ($_FILES['gg_uphoto']['tmp_name']) {
                    $ext = strtolower(pathinfo($_FILES['gg_uphoto']['name'], PATHINFO_EXTENSION));
                    if (copy($_FILES['gg_uphoto']['tmp_name'], 'media/gg/' . $id . '.' . $ext)) {
                        
                        $sql2 = $db->query("UPDATE $table SET gg_photo = '" . $id . "." . $ext . "' WHERE $table_id = $id");
                        $result .= LBL_PhotoUploaded . '<br />';
                        
                    }
                } else {
                    if (!is_numeric($_GET['id'])) {
                        
                        $sql2 = $db->query("UPDATE $table SET gg_photo = 'default_photo.png' WHERE $table_id = $id");
                        $result .= LBL_DefaultPhotoUploaded . '<br />';
                        
                    }
                }
                
                
                // Delete gg_staff records of this gg id
                $sqldel = $db->query("DELETE FROM gg_staff WHERE gg_id = $id");
                
                // Handling Supervisors
                if (is_array($_POST['gg_sv']) && count($_POST['gg_sv']) > 0) {
                    
                    foreach ($_POST['gg_sv'] as $ggs_id) {
                        
                        $sqlins2 = $db->query("INSERT INTO gg_staff VALUES ($id, $ggs_id)");
                        
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
                                    <input type="text" class="form-control" name="gg_title_ar" required="required"
                                           value="<?php echo $_POST['gg_title_ar'] ?: $row['gg_title_ar'] ?>"/>
                                </div>

                                <div class="form-group col-sm-4">
                                    <label><?= LBL_TitleEn; ?></label>
                                    <input type="text" class="form-control" name="gg_title_en" required="required"
                                           value="<?php echo $_POST['gg_title_en'] ?: $row['gg_title_en'] ?>"/>
                                </div>

                                <div class="form-group col-sm-4">
                                    <label><?= LBL_TitleUr; ?></label>
                                    <input type="text" class="form-control" name="gg_title_ur" required="required"
                                           value="<?php echo $_POST['gg_title_ur'] ?: $row['gg_title_ur'] ?>"/>
                                </div>

                            </div>

                            <div class="row">

                                <div class="form-group col-sm-4">
                                    <label><?= LBL_DescrAr; ?></label>
                                    <textarea name="gg_descr_ar"
                                              class="form-control"><?php echo $_POST['gg_descr_ar'] ?: $row['gg_descr_ar'] ?></textarea>
                                </div>

                                <div class="form-group col-sm-4">
                                    <label><?= LBL_DescrEn; ?></label>
                                    <textarea name="gg_descr_en"
                                              class="form-control"><?php echo $_POST['gg_descr_en'] ?: $row['gg_descr_en'] ?></textarea>
                                </div>

                                <div class="form-group col-sm-4">
                                    <label><?= LBL_DescrUr; ?></label>
                                    <textarea name="gg_descr_ur"
                                              class="form-control"><?php echo $_POST['gg_descr_ur'] ?: $row['gg_descr_ur'] ?></textarea>
                                </div>

                            </div>

                            <div class="form-group">
                                <label><?= LBL_Order; ?></label>
                                <input type="text" class="form-control" name="gg_order"
                                       value="<?php echo $_POST['gg_order'] ?: $row['gg_order'] ?>"/>
                            </div>

                            <div class="form-group">
                                <label><?= HM_Supervisors; ?></label>
                                <select name="gg_sv[]" class="form-control select2" multiple="multiple">
                                    <?
                                        if ($row) $sv_array = $db->query("SELECT ggs_id FROM gg_staff WHERE gg_id = " . $row['gg_id'] . " AND ggs_id IN (SELECT ggs_id FROM general_guide_staff WHERE ggs_active = 1)")->fetchAll(PDO::FETCH_COLUMN);
                                        else $sv_array = array();
                                        
                                        $sql2 = $db->query("SELECT ggs_id, ggs_name FROM general_guide_staff WHERE ggs_active = 1 ORDER BY ggs_name");
                                        while ($row2 = $sql2->fetch(PDO::FETCH_ASSOC)) {
                                            
                                            echo '<option value="' . $row2['ggs_id'] . '" ';
                                            if (in_array($row2['ggs_id'], $sv_array) || (is_array($_POST['gg_sv']) && in_array($row2['ggs_id'], $_POST['gg_sv']))) echo 'selected="selected"';
                                            echo '>' . $row2['ggs_name'] . '</option>';
                                            
                                        }
                                    ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label><?= LBL_Photo; ?></label><br/>
                                <?php if ($row['gg_photo']) echo '<img src="media/gg/' . $row['gg_photo'] . '" width="150"/>'; ?>
                                <input id="gg_uphoto" name="gg_uphoto" type="file" class="file">
                            </div>


                            <div class="form-group">
                                <label><input type="checkbox"
                                              name="gg_active" <?php if (!$row || $row['gg_active'] == 1) echo 'checked="checked"'; ?> /> <?= LBL_Active; ?>
                                </label>
                            </div>

                            <input type="submit" class="col-md-12 btn btn-success"
                                   value="<?php echo $edit ? LBL_Update : LBL_Add; ?>"/>
                            <input type="hidden" name="id" id="id" value="<?= $_GET['id']; ?>"/>
                            <input type="hidden" name="gg_photo" id="gg_photo" value="<?= $row['gg_photo']; ?>"/>
                            <input type="hidden" name="gg_dateadded" id="gg_dateadded"
                                   value="<?= $row['gg_dateadded']; ?>"/>
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
    $("#gg_uphoto").fileinput({
        showRemove: true,
        showUpload: false,
        showCancel: false,
        showPreview: true,
        minFileCount: 0,
        maxFileCount: 1,
        allowedFileTypes: ['image', 'pdf']
    });

</script>
