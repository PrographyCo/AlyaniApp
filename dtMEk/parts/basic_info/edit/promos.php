<?php
    global $db, $session, $lang, $url;
    
    $edit = false;
    $title = HM_Promos;
    $table = 'promos';
    $table_id = 'promo_id';
    
    if (isset($_REQUEST['id']) && is_numeric($_REQUEST['id'])) $id = $_REQUEST['id'];
    else $id = '';
    
    if ($_POST) {
        
        try {
            $sql = $db->prepare("REPLACE INTO $table VALUES (
	:id,
	:promo_title_ar,
	:promo_title_en,
	:promo_title_ur,
	:promo_body_ar,
	:promo_body_en,
	:promo_body_ur,
	:promo_photo,
	:promo_order,
	:promo_active,
	:promo_dateadded,
	:promo_lastupdated
	)");
            $sql->bindValue("id", $id);
            $sql->bindValue("promo_title_ar", $_POST['promo_title_ar']);
            $sql->bindValue("promo_title_en", $_POST['promo_title_en']);
            $sql->bindValue("promo_title_ur", $_POST['promo_title_ur']);
            $sql->bindValue("promo_body_ar", $_POST['promo_body_ar']);
            $sql->bindValue("promo_body_en", $_POST['promo_body_en']);
            $sql->bindValue("promo_body_ur", $_POST['promo_body_ur']);
            $sql->bindValue("promo_photo", $_POST['promo_photo']);
            $sql->bindValue("promo_order", $_POST['promo_order']);
            $sql->bindValue("promo_active", (isset($_POST['promo_active']) ? 1 : 0));
            $sql->bindValue("promo_dateadded", ($_POST['promo_dateadded'] ?? time()));
            $sql->bindValue("promo_lastupdated", time());
            
            if ($sql->execute()) {
                $result = '';
                $id = $db->lastInsertId();
                
                if ($_FILES['promo_uphoto']['tmp_name']) {
                    $ext = strtolower(pathinfo($_FILES['promo_uphoto']['name'], PATHINFO_EXTENSION));
                    if (copy($_FILES['promo_uphoto']['tmp_name'], 'assets/media/promos/' . $id . '.' . $ext)) {
                        
                        $sql2 = $db->query("UPDATE $table SET promo_photo = '" . $id . "." . $ext . "' WHERE $table_id = $id");
                        $result .= LBL_PhotoUploaded . '<br />';
                        
                    }
                } else if (!is_numeric($_REQUEST['id'])) {
                    
                    $sql2 = $db->query("UPDATE $table SET promo_photo = 'default_photo.png' WHERE $table_id = $id");
                    $result .= LBL_DefaultPhotoUploaded . '<br />';
                    
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
    
    if (is_numeric($id)) {
        
        $edit = true;
        $row = $db->query("SELECT * FROM $table WHERE $table_id = " . $id)->fetch();
        
    }

?>
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

                            <div class="row">

                                <div class="form-group col-sm-4">
                                    <label><?= LBL_TitleAr ?></label>
                                    <input type="text" class="form-control" name="promo_title_ar" required="required"
                                           value="<?= $_POST['promo_title_ar'] ?? $row['promo_title_ar'] ?? '' ?>"/>
                                </div>

                                <div class="form-group col-sm-4">
                                    <label><?= LBL_TitleEn ?></label>
                                    <input type="text" class="form-control" name="promo_title_en" required="required"
                                           value="<?= $_POST['promo_title_en'] ?? $row['promo_title_en'] ?? '' ?>"/>
                                </div>

                                <div class="form-group col-sm-4">
                                    <label><?= LBL_TitleUr ?></label>
                                    <input type="text" class="form-control" name="promo_title_ur" required="required"
                                           value="<?= $_POST['promo_title_ur'] ?? $row['promo_title_ur'] ?? '' ?>"/>
                                </div>

                            </div>

                            <div class="row">

                                <div class="form-group col-sm-4">
                                    <label><?= LBL_DescrAr ?></label>
                                    <textarea name="promo_body_ar"
                                              class="form-control"><?= $_POST['promo_body_ar'] ?? $row['promo_body_ar'] ?? '' ?></textarea>
                                </div>

                                <div class="form-group col-sm-4">
                                    <label><?= LBL_DescrEn ?></label>
                                    <textarea name="promo_body_en"
                                              class="form-control"><?= $_POST['promo_body_en'] ?? $row['promo_body_en'] ?? '' ?></textarea>
                                </div>

                                <div class="form-group col-sm-4">
                                    <label><?= LBL_DescrUr ?></label>
                                    <textarea name="promo_body_ur"
                                              class="form-control"><?= $_POST['promo_body_ur'] ?? $row['promo_body_ur'] ?? '' ?></textarea>
                                </div>

                            </div>

                            <div class="form-group">
                                <label><?= LBL_Photo ?></label><br/>
                                <?php if (isset($row['promo_photo'])) echo '<img src="'.ASSETS_PATH.'media/promos/' . $row['promo_photo'] . '" width="150"/>'; ?>
                                <input id="promo_uphoto" name="promo_uphoto" type="file" class="file">
                            </div>


                            <div class="form-group">
                                <label><?= LBL_Order ?></label>
                                <input type="text" class="form-control" name="promo_order"
                                       value="<?= $_POST['promo_order'] ?? $row['promo_order'] ?? '' ?>"/>
                            </div>

                            <div class="form-group">
                                <label><input type="checkbox"
                                              name="promo_active" <?php if (!isset($row) || $row['promo_active'] == 1) echo 'checked="checked"'; ?> /> <?= LBL_Active ?>
                                </label>
                            </div>

                            <input type="submit" class="col-md-12 btn btn-success"
                                   value="<?= $edit ? LBL_Update : LBL_Add ?>"/>
                            <input type="hidden" name="id" id="id" value="<?= $id ?>"/>
                            <input type="hidden" name="promo_photo" id="promo_photo"
                                   value="<?= $row['promo_photo'] ?? '' ?>"/>
                            <input type="hidden" name="promo_dateadded" id="promo_dateadded"
                                   value="<?= $row['promo_dateadded'] ?? '' ?>"/>
                        </form>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div><!--/.col (left) -->
        </div>   <!-- /.row -->
    </section><!-- /.content -->

</div>

<script>
    $('select').select2();
    $("#promo_uphoto").fileinput({
        showRemove: true,
        showUpload: false,
        showCancel: false,
        showPreview: true,
        minFileCount: 0,
        maxFileCount: 1,
        allowedFileTypes: ['image', 'pdf']
    });
</script>
