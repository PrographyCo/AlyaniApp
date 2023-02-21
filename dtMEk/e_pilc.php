<?php include 'header.php';
    
    $title = HM_ManageClasses;
    $table = 'pils_classes';
    $table_id = 'pilc_id';
    
    if ($_POST) {
        
        try {
            $sql = $db->prepare("REPLACE INTO $table VALUES (
	:id,
	:pilc_title_ar,
	:pilc_title_en,
	:pilc_title_ur,
	:pilc_city1_staff_id,
	:pilc_city2_staff_id,
	:pilc_city3_staff_id,
	:pilc_design_id,
	:pilc_text_id,
	:pilc_text_text,
	:pilc_phones,
	:pilc_order,
	:pilc_active,
	:pilc_dateadded,
	:pilc_lastupdated
	)");
            
            // check if new or update
            if (is_numeric($_GET['id'])) $id = $_GET['id'];
            else $id = '';
            
            $sql->bindValue("id", $id);
            $sql->bindValue("pilc_title_ar", $_POST['pilc_title_ar']);
            $sql->bindValue("pilc_title_en", $_POST['pilc_title_en']);
            $sql->bindValue("pilc_title_ur", $_POST['pilc_title_ur']);
            $sql->bindValue("pilc_city1_staff_id", $_POST['pilc_city1_staff_id']);
            $sql->bindValue("pilc_city2_staff_id", $_POST['pilc_city2_staff_id']);
            $sql->bindValue("pilc_city3_staff_id", $_POST['pilc_city3_staff_id']);
            $sql->bindValue("pilc_design_id", $_POST['pilc_design_id']);
            $sql->bindValue("pilc_text_id", $_POST['pilc_text_id']);
            $sql->bindValue("pilc_text_text", $_POST['pilc_text_text']);
            $sql->bindValue("pilc_phones", $_POST['pilc_phones']);
            $sql->bindValue("pilc_order", $_POST['pilc_order']);
            $sql->bindValue("pilc_active", ($_POST['pilc_active'] ? 1 : 0));
            $sql->bindValue("pilc_dateadded", ($_POST['pilc_dateadded'] ?: time()));
            $sql->bindValue("pilc_lastupdated", time());
            
            if ($sql->execute()) {
                
                $id = $db->lastInsertId();
                
                // Delete pils_classes_muftis records of this pilc id
                $sqldel = $db->query("DELETE FROM pils_classes_muftis WHERE pilc_id = $id");
                
                // Handling Muftis
                if (is_array($_POST['pilc_muftis']) && sizeof($_POST['pilc_muftis']) > 0) {
                    
                    foreach ($_POST['pilc_muftis'] as $staff_id) {
                        
                        $sqlins1 = $db->query("INSERT INTO pils_classes_muftis VALUES ($id, $staff_id)");
                        
                    }
                    
                }
                
                // Delete pils_classes_sv records of this pilc id
                $sqldel = $db->query("DELETE FROM pils_classes_sv WHERE pilc_id = $id");
                
                // Handling Location Supervisros
                if (is_array($_POST['pilc_sv']) && sizeof($_POST['pilc_sv']) > 0) {
                    
                    foreach ($_POST['pilc_sv'] as $city_id => $staff_id) {
                        
                        $sqlins1 = $db->query("INSERT INTO pils_classes_sv VALUES ($id, $city_id, $staff_id)");
                        
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
                                    <input type="text" class="form-control" name="pilc_title_ar" required="required"
                                           value="<?php echo $_POST['pilc_title_ar'] ?: $row['pilc_title_ar'] ?>"/>
                                </div>

                                <div class="form-group col-sm-4">
                                    <label><?= LBL_TitleEn; ?></label>
                                    <input type="text" class="form-control" name="pilc_title_en" required="required"
                                           value="<?php echo $_POST['pilc_title_en'] ?: $row['pilc_title_en'] ?>"/>
                                </div>

                                <div class="form-group col-sm-4">
                                    <label><?= LBL_TitleUr; ?></label>
                                    <input type="text" class="form-control" name="pilc_title_ur" required="required"
                                           value="<?php echo $_POST['pilc_title_ur'] ?: $row['pilc_title_ur'] ?>"/>
                                </div>

                            </div>

                            <div class="form-group">
                                <label><?= LBL_Order; ?></label>
                                <input type="text" class="form-control" name="pilc_order"
                                       value="<?php echo $_POST['pilc_order'] ?: $row['pilc_order'] ?>"/>
                            </div>


                            <div class="form-group">
                                <label><?= HM_Muftis; ?></label>
                                <select name="pilc_muftis[]" class="form-control select2" multiple="multiple">
                                    <?
                                        if ($row) $muftis_array = $db->query("SELECT staff_id FROM pils_classes_muftis WHERE pilc_id = " . $row['pilc_id'] . " AND staff_id IN (SELECT staff_id FROM staff WHERE staff_type = 3 AND staff_active = 1)")->fetchAll(PDO::FETCH_COLUMN);
                                        else $muftis_array = array();
                                        $sql1 = $db->query("SELECT staff_id, staff_name FROM staff WHERE staff_type = 3 AND staff_active = 1 ORDER BY staff_name");
                                        while ($row1 = $sql1->fetch(PDO::FETCH_ASSOC)) {
                                            
                                            echo '<option value="' . $row1['staff_id'] . '" ';
                                            if (in_array($row1['staff_id'], $muftis_array) || (is_array($_POST['pilc_muftis']) && in_array($row1['staff_id'], $_POST['pilc_muftis']))) echo 'selected="selected"';
                                            echo '>' . $row1['staff_name'] . '</option>';
                                            
                                        }
                                    ?>
                                </select>
                            </div>


                            <div class="form-group">
                                <label><?= LBL_MENA_SUPERVISOR; ?></label>
                                <select name="pilc_city1_staff_id" class="form-control select2">
                                    <option value="0"><?= LBL_Choose; ?></option>
                                    <?
                                        $sql1 = $db->query("SELECT staff_id, staff_name FROM staff WHERE staff_type = 2 AND staff_active = 1 ORDER BY staff_name");
                                        while ($row1 = $sql1->fetch(PDO::FETCH_ASSOC)) {
                                            
                                            echo '<option value="' . $row1['staff_id'] . '" ';
                                            if ($row['pilc_city1_staff_id'] == $row1['staff_id']) echo 'selected="selected"';
                                            echo '>' . $row1['staff_name'] . '</option>';
                                            
                                        }
                                    ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label><?= LBL_MOZDALEFA_SUPERVISOR; ?></label>
                                <select name="pilc_city2_staff_id" class="form-control select2">
                                    <option value="0"><?= LBL_Choose; ?></option>
                                    <?
                                        $sql1 = $db->query("SELECT staff_id, staff_name FROM staff WHERE staff_type = 2 AND staff_active = 1 ORDER BY staff_name");
                                        while ($row1 = $sql1->fetch(PDO::FETCH_ASSOC)) {
                                            
                                            echo '<option value="' . $row1['staff_id'] . '" ';
                                            if ($row['pilc_city2_staff_id'] == $row1['staff_id']) echo 'selected="selected"';
                                            echo '>' . $row1['staff_name'] . '</option>';
                                            
                                        }
                                    ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label><?= LBL_ARAFA_SUPERVISOR; ?></label>
                                <select name="pilc_city3_staff_id" class="form-control select2">
                                    <option value="0"><?= LBL_Choose; ?></option>
                                    <?
                                        $sql1 = $db->query("SELECT staff_id, staff_name FROM staff WHERE staff_type = 2 AND staff_active = 1 ORDER BY staff_name");
                                        while ($row1 = $sql1->fetch(PDO::FETCH_ASSOC)) {
                                            
                                            echo '<option value="' . $row1['staff_id'] . '" ';
                                            if ($row['pilc_city3_staff_id'] == $row1['staff_id']) echo 'selected="selected"';
                                            echo '>' . $row1['staff_name'] . '</option>';
                                            
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-6">
                                    <label><?= LBL_TextType; ?></label>
                                    <select name="pilc_text_id" id="pilc_text_id" class="form-control select2">
                                        <option value="1" <?php if ($row['pilc_text_id'] == 1) echo 'selected="selected"'; ?>><?= LBL_Mokhayam; ?></option>
                                        <option value="2" <?php if ($row['pilc_text_id'] == 2) echo 'selected="selected"'; ?>><?= LBL_Omara; ?></option>
                                    </select>
                                </div>

                                <div class="form-group col-sm-6">
                                    <label><?= LBL_Number; ?></label>
                                    <input type="text" class="form-control" name="pilc_text_text" required="required"
                                           value="<?php echo $_POST['pilc_text_text'] ?: $row['pilc_text_text'] ?>"/>
                                </div>
                            </div>

                            <div class="form-group">
                                <label><?= LBL_ContactNumbers; ?></label>
                                <input type="text" class="form-control" name="pilc_phones"
                                       value="<?php echo $_POST['pilc_phones'] ?: $row['pilc_phones'] ?>"/>
                            </div>


                            <div class="form-group">
                                <div class="row">
                                    <?
                                        for ($i = 1; $i <= 10; $i++) {
                                            
                                            echo '<div class="col-sm-4" style="margin-bottom:20px">
														<label><input type="radio" name="pilc_design_id" value="' . $i . '" ';
                                            if ($row['pilc_design_id'] == $i) echo 'checked="checked"';
                                            echo '/> <img src="pil_cards_designs/pils/common/' . $i . '.png" style="height:300px;" /></label>
														</div>';
                                        
                                        }
                                    ?>
                                </div>
                            </div>
                            <div class="cleafix">

                            </div>
                            <div class="form-group">
                                <label><input type="checkbox"
                                              name="pilc_active" <?php if (!$row || $row['pilc_active'] == 1) echo 'checked="checked"'; ?> /> <?= LBL_Active; ?>
                                </label>
                            </div>

                            <input type="submit" class="col-md-12 btn btn-success"
                                   value="<?php echo $edit ? LBL_Update : LBL_Add; ?>"/>
                            <input type="hidden" name="id" id="id" value="<?= $_GET['id']; ?>"/>
                            <input type="hidden" name="pilc_dateadded" id="pilc_dateadded"
                                   value="<?= $row['pilc_dateadded']; ?>"/>
                        </form>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div><!--/.col (left) -->
        </div>   <!-- /.row -->
    </section><!-- /.content -->

</div>

<?php include 'footer.php'; ?>
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
