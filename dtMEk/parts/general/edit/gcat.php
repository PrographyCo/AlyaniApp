<?php
    global $db, $url, $lang, $session;
    
    $edit = false;
    $title = HM_HajGuideCats;
    $table = 'guide_categories';
    $table_id = 'gcat_id';
    
    if (isset($_REQUEST['id']) && is_numeric($_REQUEST['id'])) $id = $_REQUEST['id'];
    else $id = '';
    
    
    if ($_POST) {
        
        try {
            $sql = $db->prepare("REPLACE INTO $table VALUES (
	:id,
	:gcat_title_ar,
	:gcat_title_en,
	:gcat_title_ur,
	:gcat_order,
	:gcat_active
	)");
            
            $sql->bindValue("id", $id);
            $sql->bindValue("gcat_title_ar", $_POST['gcat_title_ar']);
            $sql->bindValue("gcat_title_en", $_POST['gcat_title_en']);
            $sql->bindValue("gcat_title_ur", $_POST['gcat_title_ur']);
            $sql->bindValue("gcat_order", $_POST['gcat_order']);
            $sql->bindValue("gcat_active", (isset($_POST['gcat_active']) ? 1 : 0));
            
            if ($sql->execute()) {
                $result = '';
                $id = $db->lastInsertId();
                
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
                                    <input type="text" class="form-control" name="gcat_title_ar" required="required"
                                           value="<?= $_POST['gcat_title_ar'] ?? $row['gcat_title_ar'] ?? '' ?>"/>
                                </div>

                                <div class="form-group col-sm-4">
                                    <label><?= LBL_TitleEn ?></label>
                                    <input type="text" class="form-control" name="gcat_title_en" required="required"
                                           value="<?= $_POST['gcat_title_en'] ?? $row['gcat_title_en'] ?? '' ?>"/>
                                </div>

                                <div class="form-group col-sm-4">
                                    <label><?= LBL_TitleUr ?></label>
                                    <input type="text" class="form-control" name="gcat_title_ur" required="required"
                                           value="<?= $_POST['gcat_title_ur'] ?? $row['gcat_title_ur'] ?? '' ?>"/>
                                </div>

                            </div>

                            <div class="form-group">
                                <label><?= LBL_Order ?></label>
                                <input type="text" class="form-control" name="gcat_order"
                                       value="<?= $_POST['gcat_order'] ?? $row['gcat_order'] ?? '' ?>"/>
                            </div>

                            <div class="form-group">
                                <label><input type="checkbox"
                                              name="gcat_active" <?php if (!isset($row) || $row['gcat_active'] == 1) echo 'checked="checked"'; ?> /> <?= LBL_Active ?>
                                </label>
                            </div>

                            <input type="submit" class="col-md-12 btn btn-success"
                                   value="<?= $edit ? LBL_Update : LBL_Add ?>"/>
                            <input type="hidden" name="id" id="id" value="<?= $id ?>"/>
                        </form>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div><!--/.col (left) -->
        </div>   <!-- /.row -->
    </section><!-- /.content -->

</div>

<script>
    $('select').select2();
    $("#news_uphoto").fileinput({
        showRemove: true,
        showUpload: false,
        showCancel: false,
        showPreview: true,
        minFileCount: 0,
        maxFileCount: 1,
        allowedFileTypes: ['image', 'pdf']
    });
</script>
