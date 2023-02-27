<?php
    global $db, $url, $lang;
    
    $edit = false;
    $title = HM_GENERALGUIDESTAFF;
    $table = 'general_guide_staff';
    $table_id = 'ggs_id';
    
    if (isset($_REQUEST['id']) && is_numeric($_REQUEST['id'])) $id = $_REQUEST['id'];
    else $id = '';
    
    if ($_POST) {

        try {
            
            $sql = $db->prepare("REPLACE INTO $table VALUES (
		:id,
		:ggs_name,
		:ggs_email,
		:ggs_phones,
		:ggs_photo,
		:ggs_job_ar,
		:ggs_job_en,
		:ggs_job_ur,
		:ggs_active,
		:ggs_dateadded,
		:ggs_lastupdated
		)");
            
            $sql->bindValue("id", $id);
            $sql->bindValue("ggs_name", $_POST['ggs_name']);
            $sql->bindValue("ggs_email", $_POST['ggs_email']);
            $sql->bindValue("ggs_phones", $_POST['ggs_phones']);
            $sql->bindValue("ggs_photo", $_POST['ggs_photo']);
            $sql->bindValue("ggs_job_ar", $_POST['ggs_job_ar']);
            $sql->bindValue("ggs_job_en", $_POST['ggs_job_en']);
            $sql->bindValue("ggs_job_ur", $_POST['ggs_job_ur']);
            $sql->bindValue("ggs_active", (isset($_POST['ggs_active']) ? 1 : 0));
            $sql->bindValue("ggs_dateadded", ($_POST['ggs_dateadded'] ?? time()));
            $sql->bindValue("ggs_lastupdated", time());
            
            if ($sql->execute()) {
                $result = '';
                $id = $db->lastInsertId();
                
                if ($_FILES['ggs_uphoto']['tmp_name']) {
                    $ext = strtolower(pathinfo($_FILES['ggs_uphoto']['name'], PATHINFO_EXTENSION));
                    if (copy($_FILES['ggs_uphoto']['tmp_name'], ASSETS_PATH.'media/ggs_staff/' . $id . '.' . $ext)) {
                        
                        $sql2 = $db->query("UPDATE $table SET ggs_photo = '" . $id . "." . $ext . "' WHERE $table_id = $id");
                        $result .= LBL_PhotoUploaded . '<br />';
                        
                    }
                } else {
                    if (!is_numeric($_REQUEST['id'])) {
                        
                        $sql2 = $db->query("UPDATE $table SET ggs_photo = 'default_photo.png' WHERE $table_id = $id");
                        $result .= LBL_DefaultPhotoUploaded . '<br />';
                        
                    }
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
                                <label><?= LBL_Name ?></label>
                                <input type="text" class="form-control" name="ggs_name" required="required"
                                       value="<?= $_POST['ggs_name'] ?? $row['ggs_name'] ?? '' ?>"/>
                            </div>

                            <div class="row">

                                <div class="form-group col-sm-4">
                                    <label><?= LBL_JobTitle ?></label>
                                    <input type="text" class="form-control" name="ggs_job_ar"
                                           value="<?= $_POST['ggs_job_ar'] ?? $row['ggs_job_ar'] ?? '' ?>"/>
                                </div>

                                <div class="form-group col-sm-4">
                                    <label><?= LBL_JobTitle ?></label>
                                    <input type="text" class="form-control" name="ggs_job_en"
                                           value="<?= $_POST['ggs_job_en'] ?? $row['ggs_job_en'] ?? '' ?>"/>
                                </div>

                                <div class="form-group col-sm-4">
                                    <label><?= LBL_JobTitle ?></label>
                                    <input type="text" class="form-control" name="ggs_job_ur"
                                           value="<?= $_POST['ggs_job_ur'] ?? $row['ggs_job_ur'] ?? '' ?>"/>
                                </div>

                            </div>

                            <div class="form-group">
                                <label><?= LBL_Email ?></label>
                                <input type="email" class="form-control" name="ggs_email"
                                       value="<?= $_POST['ggs_email'] ?? $row['ggs_email'] ?? '' ?>"/>
                            </div>

                            <div class="form-group">
                                <label><?= LBL_ContactNumbers ?></label>
                                <input type="text" class="form-control" name="ggs_phones"
                                       value="<?= $_POST['ggs_phones'] ?? $row['ggs_phones'] ?? '' ?>"/>
                            </div>

                            <div class="form-group">
                                <label><?= LBL_Photo ?></label><br/>
                                <?php if (isset($row['ggs_photo']) && $row['ggs_photo']) echo '<img src="'.CP_PATH.'/assets/media/ggs_staff/' . $row['ggs_photo'] . '" width="150"/>'; ?>
                                <input id="ggs_uphoto" name="ggs_uphoto" type="file" class="file">
                            </div>


                            <div class="form-group">
                                <label><input type="checkbox"
                                              name="ggs_active" <?php if (!isset($row) || $row['ggs_active'] == 1) echo 'checked="checked"'; ?> /> <?= LBL_Active ?>
                                </label>
                            </div>

                            <input type="submit" class="col-md-12 btn btn-success"
                                   value="<?= $edit ? LBL_Update : LBL_Add ?>"/>
                            <input type="hidden" name="id" id="id" value="<?= $id ?>"/>
                            <input type="hidden" name="ggs_photo" id="ggs_photo" value="<?= $row['ggs_photo']??'' ?>"/>
                            <input type="hidden" name="ggs_dateadded" id="ggs_dateadded"
                                   value="<?= $row['ggs_dateadded']??time() ?>"/>
                        </form>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div><!--/.col (left) -->
        </div>   <!-- /.row -->
    </section><!-- /.content -->

</div>

<script>
    $('select').select2();
    $("#ggs_uphoto").fileinput({
        showRemove: true,
        showUpload: false,
        showCancel: false,
        showPreview: true,
        minFileCount: 0,
        maxFileCount: 1,
        allowedFileTypes: ['image', 'pdf']
    });
</script>
