<?php include 'layout/header.php';
    
    $title = HM_HajAlbum;
    $table = 'haj_album';
    $table_id = 'hp_id';
    
    if ($_POST) {
        
        try {
            $sql = $db->prepare("REPLACE INTO $table VALUES (
	:id,
	:hp_title_ar,
	:hp_title_en,
	:hp_title_ur,
	:hp_photo,
	:hp_order,
	:hp_active,
	:hp_dateadded,
	:hp_lastupdated
	)");
            
            // check if new or update
            if (is_numeric($_GET['id'])) $id = $_GET['id'];
            else $id = '';
            
            $sql->bindValue("id", $id);
            $sql->bindValue("hp_title_ar", $_POST['hp_title_ar']);
            $sql->bindValue("hp_title_en", $_POST['hp_title_en']);
            $sql->bindValue("hp_title_ur", $_POST['hp_title_ur']);
            $sql->bindValue("hp_photo", $_POST['hp_photo']);
            $sql->bindValue("hp_order", $_POST['hp_order']);
            $sql->bindValue("hp_active", ($_POST['hp_active'] ? 1 : 0));
            $sql->bindValue("hp_dateadded", ($_POST['hp_dateadded'] ?: time()));
            $sql->bindValue("hp_lastupdated", time());
            
            if ($sql->execute()) {
                
                $id = $db->lastInsertId();
                
                if ($_FILES['hp_uphoto']['tmp_name']) {
                    $ext = strtolower(pathinfo($_FILES['hp_uphoto']['name'], PATHINFO_EXTENSION));
                    $newname = GUID();
                    if (copy($_FILES['hp_uphoto']['tmp_name'], 'media/hajalbum/' . $newname . '.' . $ext)) {
                        
                        $sql2 = $db->query("UPDATE $table SET hp_photo = '" . $newname . "." . $ext . "' WHERE $table_id = $id");
                        $result .= LBL_PhotoUploaded . '<br />';
                        
                    }
                } else {
                    if (!is_numeric($_GET['id'])) {
                        
                        $sql2 = $db->query("UPDATE $table SET hp_photo = 'default_photo.png' WHERE $table_id = $id");
                        $result .= LBL_DefaultPhotoUploaded . '<br />';
                        
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
                                    <input type="text" class="form-control" name="hp_title_ar" required="required"
                                           value="<?php echo $_POST['hp_title_ar'] ?: $row['hp_title_ar'] ?>"/>
                                </div>

                                <div class="form-group col-sm-4">
                                    <label><?= LBL_TitleEn; ?></label>
                                    <input type="text" class="form-control" name="hp_title_en" required="required"
                                           value="<?php echo $_POST['hp_title_en'] ?: $row['hp_title_en'] ?>"/>
                                </div>

                                <div class="form-group col-sm-4">
                                    <label><?= LBL_TitleUr; ?></label>
                                    <input type="text" class="form-control" name="hp_title_ur" required="required"
                                           value="<?php echo $_POST['hp_title_ur'] ?: $row['hp_title_ur'] ?>"/>
                                </div>

                            </div>

                            <div class="form-group">
                                <label><?= LBL_Photo; ?></label><br/>
                                <?php if ($row['hp_photo']) echo '<img src="media/hajalbum/' . $row['hp_photo'] . '" width="150"/>'; ?>
                                <input id="hp_uphoto" name="hp_uphoto" type="file" class="file">
                            </div>


                            <div class="form-group">
                                <label><?= LBL_Order; ?></label>
                                <input type="text" class="form-control" name="hp_order"
                                       value="<?php echo $_POST['hp_order'] ?: $row['hp_order'] ?>"/>
                            </div>

                            <div class="form-group">
                                <label><input type="checkbox"
                                              name="hp_active" <?php if (!$row || $row['hp_active'] == 1) echo 'checked="checked"'; ?> /> <?= LBL_Active; ?>
                                </label>
                            </div>

                            <input type="submit" class="col-md-12 btn btn-success"
                                   value="<?php echo $edit ? LBL_Update : LBL_Add; ?>"/>
                            <input type="hidden" name="id" id="id" value="<?= $_GET['id']; ?>"/>
                            <input type="hidden" name="hp_photo" id="hp_photo" value="<?= $row['hp_photo']; ?>"/>
                            <input type="hidden" name="hp_dateadded" id="hp_dateadded"
                                   value="<?= $row['hp_dateadded']; ?>"/>
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
    $("#hp_uphoto").fileinput({
        showRemove: true,
        showUpload: false,
        showCancel: false,
        showPreview: true,
        minFileCount: 0,
        maxFileCount: 1,
        allowedFileTypes: ['image', 'pdf']
    });
</script>
