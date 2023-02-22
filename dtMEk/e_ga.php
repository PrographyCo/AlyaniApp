<?php include 'layout/header.php';
    
    $title = HM_HajGuideArticles;
    $table = 'guide_articles';
    $table_id = 'ga_id';
    
    if ($_POST) {
        
        try {
            $sql = $db->prepare("REPLACE INTO $table VALUES (
	:id,
	:ga_gcat_id,
	:ga_type,
	:ga_file_ar,
	:ga_file_en,
	:ga_file_ur,
	:ga_title_ar,
	:ga_title_en,
	:ga_title_ur,
	:ga_body_ar,
	:ga_body_en,
	:ga_body_ur,
	:ga_order,
	:ga_active
	)");
            
            // check if new or update
            if (is_numeric($_GET['id'])) $id = $_GET['id'];
            else $id = '';
            
            $sql->bindValue("id", $id);
            $sql->bindValue("ga_gcat_id", $_POST['ga_gcat_id']);
            $sql->bindValue("ga_type", $_POST['ga_type']);
            $sql->bindValue("ga_file_ar", $_POST['ga_file_ar']);
            $sql->bindValue("ga_file_en", $_POST['ga_file_en']);
            $sql->bindValue("ga_file_ur", $_POST['ga_file_ur']);
            $sql->bindValue("ga_title_ar", $_POST['ga_title_ar']);
            $sql->bindValue("ga_title_en", $_POST['ga_title_en']);
            $sql->bindValue("ga_title_ur", $_POST['ga_title_ur']);
            $sql->bindValue("ga_body_ar", $_POST['ga_body_ar']);
            $sql->bindValue("ga_body_en", $_POST['ga_body_en']);
            $sql->bindValue("ga_body_ur", $_POST['ga_body_ur']);
            $sql->bindValue("ga_order", $_POST['ga_order']);
            $sql->bindValue("ga_active", ($_POST['ga_active'] ? 1 : 0));
            
            if ($sql->execute()) {
                
                $id = $db->lastInsertId();
                
                if ($_FILES['ga_ufile_ar']['tmp_name']) {
                    $ext = strtolower(pathinfo($_FILES['ga_ufile_ar']['name'], PATHINFO_EXTENSION));
                    if (copy($_FILES['ga_ufile_ar']['tmp_name'], 'media/ga_articles/' . $id . '_ar.' . $ext)) {
                        
                        $sql2 = $db->query("UPDATE $table SET ga_file_ar = '" . $id . "_ar." . $ext . "' WHERE $table_id = $id");
                        $result .= LBL_PDFFileUploaded . '<br />';
                        
                    }
                }
                
                if ($_FILES['ga_ufile_en']['tmp_name']) {
                    $ext = strtolower(pathinfo($_FILES['ga_ufile_en']['name'], PATHINFO_EXTENSION));
                    if (copy($_FILES['ga_ufile_en']['tmp_name'], 'media/ga_articles/' . $id . '_en.' . $ext)) {
                        
                        $sql2 = $db->query("UPDATE $table SET ga_file_en = '" . $id . "_en." . $ext . "' WHERE $table_id = $id");
                        $result .= LBL_PDFFileUploaded . '<br />';
                        
                    }
                }
                
                if ($_FILES['ga_ufile_ur']['tmp_name']) {
                    $ext = strtolower(pathinfo($_FILES['ga_ufile_ur']['name'], PATHINFO_EXTENSION));
                    if (copy($_FILES['ga_ufile_ur']['tmp_name'], 'media/ga_articles/' . $id . '_ur.' . $ext)) {
                        
                        $sql2 = $db->query("UPDATE $table SET ga_file_ur = '" . $id . "_ur." . $ext . "' WHERE $table_id = $id");
                        $result .= LBL_PDFFileUploaded . '<br />';
                        
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

                            <div class="form-group">
                                <label><?= LBL_ChooseParentCategory; ?></label>
                                <select name="ga_gcat_id" class="form-control">
                                    <?
                                        $sqlcats = $db->query("SELECT * FROM guide_categories WHERE gcat_active = 1 ORDER BY gcat_title_$lang");
                                        while ($rowcats = $sqlcats->fetch(PDO::FETCH_ASSOC)) {
                                            
                                            echo '<option value="' . $rowcats['gcat_id'] . '" ';
                                            if ($row['gcat_parent_id'] == $rowcats['gcat_id'] || $_POST['gcat_parent_id'] == $rowcats['gcat_id'] || $_GET['parent_id'] == $rowcats['gcat_id']) echo 'selected="selected"';
                                            echo '>' . $rowcats['gcat_title_' . $lang] . '</option>';
                                            
                                        }
                                    ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label><?= LBL_Type; ?></label>
                                <select name="ga_type" class="form-control"
                                        onchange="typechanged(this.value); return false;">
                                    <option value="1" <?php if ($row['ga_type'] == 1) echo 'selected="selected"'; ?>><?= LBL_Text; ?></option>
                                    <option value="2" <?php if ($row['ga_type'] == 2) echo 'selected="selected"'; ?>><?= LBL_PDFFILE; ?></option>
                                </select>
                            </div>


                            <div class="row">

                                <div class="form-group col-sm-4">
                                    <label><?= LBL_TitleAr; ?></label>
                                    <input type="text" class="form-control" name="ga_title_ar" required="required"
                                           value="<?php echo $_POST['ga_title_ar'] ?: $row['ga_title_ar'] ?>"/>
                                </div>

                                <div class="form-group col-sm-4">
                                    <label><?= LBL_TitleEn; ?></label>
                                    <input type="text" class="form-control" name="ga_title_en" required="required"
                                           value="<?php echo $_POST['ga_title_en'] ?: $row['ga_title_en'] ?>"/>
                                </div>

                                <div class="form-group col-sm-4">
                                    <label><?= LBL_TitleUr; ?></label>
                                    <input type="text" class="form-control" name="ga_title_ur" required="required"
                                           value="<?php echo $_POST['ga_title_ur'] ?: $row['ga_title_ur'] ?>"/>
                                </div>

                            </div>

                            <div id="div_text"
                                 style="<?php if ($row['ga_type'] == 1 || $_POST['ga_type'] == 1 || !$row) echo 'display:block'; else echo 'display:none'; ?>">

                                <div class="row">

                                    <div class="form-group col-sm-4">
                                        <label><?= LBL_DescrAr; ?></label>
                                        <textarea name="ga_body_ar"
                                                  class="form-control"><?php echo $_POST['ga_body_ar'] ?: $row['ga_body_ar'] ?></textarea>
                                    </div>

                                    <div class="form-group col-sm-4">
                                        <label><?= LBL_DescrEn; ?></label>
                                        <textarea name="ga_body_en"
                                                  class="form-control"><?php echo $_POST['ga_body_en'] ?: $row['ga_body_en'] ?></textarea>
                                    </div>

                                    <div class="form-group col-sm-4">
                                        <label><?= LBL_DescrUr; ?></label>
                                        <textarea name="ga_body_ur"
                                                  class="form-control"><?php echo $_POST['ga_body_ur'] ?: $row['ga_body_ur'] ?></textarea>
                                    </div>

                                </div>

                            </div>

                            <div id="div_file"
                                 style="<?php if ($row['ga_type'] == 2 || $_POST['ga_type'] == 2) echo 'display:block'; else echo 'display:none'; ?>">

                                <div class="row">

                                    <div class="form-group col-sm-4">
                                        <label><?= LBL_PDFFILE_AR; ?></label>
                                        <input type="file" name="ga_ufile_ar" id="ga_ufile_ar" class="form-control"/>
                                        <?
                                            if ($row['ga_file_ar']) {
                                                
                                                echo '<a href="media/ga_articles/' . $row['ga_file_ar'] . '" target="_blank">' . LBL_ViewFile . '</a>';
                                                
                                            }
                                        ?>
                                    </div>

                                    <div class="form-group col-sm-4">
                                        <label><?= LBL_PDFFILE_EN; ?></label>
                                        <input type="file" name="ga_ufile_en" id="ga_ufile_en" class="form-control"/>
                                        <?
                                            if ($row['ga_file_en']) {
                                                
                                                echo '<a href="media/ga_articles/' . $row['ga_file_en'] . '" target="_blank">' . LBL_ViewFile . '</a>';
                                                
                                            }
                                        ?>
                                    </div>

                                    <div class="form-group col-sm-4">
                                        <label><?= LBL_PDFFILE_UR; ?></label>
                                        <input type="file" name="ga_ufile_ur" id="ga_ufile_ur" class="form-control"/>
                                        <?
                                            if ($row['ga_file_ur']) {
                                                
                                                echo '<a href="media/ga_articles/' . $row['ga_file_ur'] . '" target="_blank">' . LBL_ViewFile . '</a>';
                                                
                                            }
                                        ?>
                                    </div>


                                </div>
                            </div>

                            <div class="form-group">
                                <label><?= LBL_Order; ?></label>
                                <input type="text" class="form-control" name="ga_order"
                                       value="<?php echo $_POST['ga_order'] ?: $row['ga_order'] ?>"/>
                            </div>

                            <div class="form-group">
                                <label><input type="checkbox"
                                              name="ga_active" <?php if (!$row || $row['ga_active'] == 1) echo 'checked="checked"'; ?> /> <?= LBL_Active; ?>
                                </label>
                            </div>

                            <input type="submit" class="col-md-12 btn btn-success"
                                   value="<?php echo $edit ? LBL_Update : LBL_Add; ?>"/>
                            <input type="hidden" name="id" id="id" value="<?= $_GET['id']; ?>"/>
                            <input type="hidden" name="ga_file_ar" value="<?= $row['ga_file_ar']; ?>"/>
                            <input type="hidden" name="ga_file_en" value="<?= $row['ga_file_en']; ?>"/>
                            <input type="hidden" name="ga_file_ur" value="<?= $row['ga_file_ur']; ?>"/>
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

    function typechanged(type_id) {

        if (type_id == 1) {

            $('#div_file').hide();
            $('#div_text').show();

        } else if (type_id == 2) {

            $('#div_text').hide();
            $('#div_file').show();


        }

    }


    $("#ga_ufile_ar").fileinput({
        showRemove: true,
        showUpload: false,
        showCancel: false,
        showPreview: true,
        minFileCount: 0,
        maxFileCount: 1,
        allowedFileTypes: ['pdf']
    });

    $("#ga_ufile_en").fileinput({
        showRemove: true,
        showUpload: false,
        showCancel: false,
        showPreview: true,
        minFileCount: 0,
        maxFileCount: 1,
        allowedFileTypes: ['pdf']
    });

    $("#ga_ufile_ur").fileinput({
        showRemove: true,
        showUpload: false,
        showCancel: false,
        showPreview: true,
        minFileCount: 0,
        maxFileCount: 1,
        allowedFileTypes: ['pdf']
    });

</script>
