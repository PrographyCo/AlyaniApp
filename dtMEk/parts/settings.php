<?php include 'layout/header.php';
    
    if ($_POST) {
        
        if (is_array($_POST['s']) && count($_POST['s']) > 0) {
            
            if ($_FILES['s']['tmp_name'][13]) {
                $ext = strtolower(pathinfo($_FILES['s']['name'][13], PATHINFO_EXTENSION));
                if (copy($_FILES['s']['tmp_name'][13], 'media/frontimg/frontimg.' . $ext)) {
                    
                    $_POST['s']['13'] = 'frontimg.' . $ext;
                    
                }
            }
            
            foreach ($_POST['s'] as $key => $value) {
                
                //if ($key == 1) $value = implode(",", $value);
                //if ($key == 2) $value = implode(",", $value);
                //if ($key == 5) $value = implode(",", $value);
                
                $sqlupd = $db->prepare("UPDATE settings SET s_value = :value WHERE s_id = :id");
                $sqlupd->bindValue("value", $value);
                $sqlupd->bindValue("id", $key);
                $sqlupd->execute();
                
            }
            
            $msg = '<div class="alert alert-success alert-dismissable"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button><h4><i class="icon fa fa-check"></i>' . LBL_Updated . '!</h4>' . LBL_Item . ' ' . LBL_Updated . ' ' . LBL_Successfully . '<br />' . $result . '</div>';
            unset($_POST);
            
            
        }
        
        
    }
?>
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <!-- left column -->
            <div class="col-md-12">
                <?php echo $msg; ?>
                <!-- Input addon -->
                <form role="form" method="post" enctype="multipart/form-data">
                    <div class="box box-info">
                        <div class="box-header with-border">
                            <h3 class="box-title"><?= HM_Settings; ?></h3>
                        </div>
                        <div class="box-body">
                            
                            <?
                                
                                $sqls = $db->query("SELECT * FROM settings ORDER BY s_id");
                                while ($rows = $sqls->fetch(PDO::FETCH_ASSOC)) {
                                    
                                    if ($rows['s_id'] == 4 || $rows['s_id'] == 5 || $rows['s_id'] == 10) {
                                        
                                        echo '<div class="form-group">
										<label>' . $rows['s_title_' . $lang] . '</label>
										<select name="s[' . $rows['s_id'] . ']" class="form-control">
										<option value="0">' . LBL_NO . '</option>
										<option value="1" ';
                                        if ($rows['s_value'] == 1) echo 'selected="selected"';
                                        echo '>' . LBL_YES . '</option>
										</select></div>';
                                    
                                    } elseif ($rows['s_id'] == 12) {
                                        
                                        echo '<div class="form-group">
										<label>' . $rows['s_title_' . $lang] . ' - {{name}}</label>
										<textarea name="s[' . $rows['s_id'] . ']" class="form-control">' . $rows['s_value'] . '</textarea>
										</div>';
                                    
                                    } elseif ($rows['s_id'] == 14 || $rows['s_id'] == 15) {
                                        
                                        echo '<div class="form-group">
										<label>' . $rows['s_title_' . $lang] . ' - {{name}} / {{accomo}}</label>
										<textarea name="s[' . $rows['s_id'] . ']" class="form-control" rows="10">' . $rows['s_value'] . '</textarea>
										</div>';
                                    
                                    } elseif ($rows['s_id'] == 13) {
                                        
                                        echo '<div class="form-group">
										<label>' . $rows['s_title_' . $lang] . '</label><br />';
                                        
                                        if ($rows['s_value']) echo '<img src="media/frontimg/' . $rows['s_value'] . '?v=' . time() . '" width="150"/><br />';
                                        else echo '<img src="media/frontimg/default.png" width="150"/><br />';
                                        echo '<input name="s[' . $rows['s_id'] . ']" type="file" class="file">
										</div>';
                                    
                                    } else {
                                        
                                        echo '<div class="form-group">
										<label>' . $rows['s_title_' . $lang] . '</label>
										<input type="text" class="form-control" name="s[' . $rows['s_id'] . ']" value="' . $rows['s_value'] . '" />
										</div>';
                                    
                                    }
                                    
                                }
                            
                            ?>

                        </div><!-- /.box-body -->
                    </div><!-- /.box -->
            </div><!--/.col (left) -->
            <div class="col-md-12">
                <input type="submit" class="col-md-12 btn btn-success" value="<?= LBL_Update; ?>"/>
            </div>
            </form>
        </div>   <!-- /.row -->
    </section><!-- /.content -->
</div>

<?php include 'layout/footer.php'; ?>
<script>
    $('select').select2();
    $("file").fileinput({
        showRemove: true,
        showUpload: false,
        showCancel: false,
        showPreview: true,
        minFileCount: 0,
        maxFileCount: 1,
        allowedFileTypes: ['image', 'pdf']
    });

</script>
