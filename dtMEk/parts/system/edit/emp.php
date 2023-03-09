<?php
    global $db, $url, $lang;
    
    $edit = false;
    $title = HM_Employees;
    $table = 'employees';
    $table_id = 'emp_id';
    
    if (isset($_REQUEST['id']) && is_numeric($_REQUEST['id'])) $id = $_REQUEST['id'];
    else $id = '';
    
    if (!empty($_POST)) {
        
        try {
            $sql = $db->prepare("REPLACE INTO $table VALUES (
                           :id,
                           :emp_name,
                           :emp_jobtitle,
                           :emp_jobid,
                           :emp_photo,
                           :emp_dateadded,
                           " . time() . "
                       )");
            
            $sql->bindValue("id", $id);
            $sql->bindValue("emp_name", $_POST['emp_name']);
            $sql->bindValue("emp_jobtitle", $_POST['emp_jobtitle']);
            $sql->bindValue("emp_jobid", $_POST['emp_jobid']);
            $sql->bindValue("emp_photo", $_POST['emp_photo']);
            $sql->bindValue("emp_dateadded", ($_POST['emp_dateadded'] ?? time()));
            
            if ($sql->execute()) {
                $result = '';
                $id = $db->lastInsertId();
                
                if ($_FILES['emp_uphoto']['tmp_name']) {
                    $ext = strtolower(pathinfo($_FILES['emp_uphoto']['name'], PATHINFO_EXTENSION));
                    if (copy($_FILES['emp_uphoto']['tmp_name'], ASSETS_PATH.' media/emps_photos/' . $id . '.' . $ext)) {
                        
                        $sql2 = $db->query("UPDATE $table SET emp_photo = '" . $id . "." . $ext . "' WHERE $table_id = $id");
                        $result .= LBL_PhotoUploaded . '<br />';
                    
                    }
                } else {
                    if (!is_numeric($_REQUEST['id'])) {
                        
                        $sql2 = $db->query("UPDATE $table SET emp_photo = 'default.png' WHERE $table_id = $id");
                        $result .= LBL_DefaultPhotoUploaded . '<br />';
                        
                    }
                }
                
                if ($_REQUEST['id'] > 0) $label = LBL_Updated;
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
                                <input type="text" class="form-control" name="emp_name" required="required"
                                       value="<?= $_POST['emp_name'] ?? $row['emp_name'] ?? '' ?>"/>
                            </div>

                            <div class="form-group">
                                <label><?= LBL_JobTitle ?></label>
                                <input type="text" class="form-control" name="emp_jobtitle" required="required"
                                       value="<?= $_POST['emp_jobtitle'] ?? $row['emp_jobtitle'] ?? '' ?>"/>
                            </div>

                            <div class="form-group">
                                <label><?= LBL_JobID ?></label>
                                <input type="text" class="form-control" name="emp_jobid" required="required"
                                       value="<?= $_POST['emp_jobid'] ?? $row['emp_jobid'] ?? '' ?>"/>
                            </div>

                            <div class="form-group">
                                <label><?= LBL_Photo ?></label><br/>
                                <?php if (isset($row['emp_photo'])) echo '<img src="'.CP_PATH.'/assets/media/emps_photos/' . $row['emp_photo'] . '" width="150"/>'; ?>
                                <input id="emp_uphoto" name="emp_uphoto" type="file" class="file">
                            </div>

                            <input type="submit" class="col-md-12 btn btn-success"
                                   value="<?= $edit ? LBL_Update : LBL_Add ?>"/>
                            <input type="hidden" name="id" id="id" value="<?= $id ?>"/>
                            <input type="hidden" name="emp_photo" id="emp_photo" value="<?= $row['emp_photo']??'' ?>"/>
                            <input type="hidden" name="emp_dateadded" id="emp_dateadded"
                                   value="<?= $row['emp_dateadded']??time() ?>"/>

                        </form>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div><!--/.col (left) -->
        </div>   <!-- /.row -->
    </section><!-- /.content -->

</div>

<script>
    $('select').select2();
    $("#emp_uphoto").fileinput({
        showRemove: true,
        showUpload: false,
        showCancel: false,
        showPreview: true,
        minFileCount: 0,
        maxFileCount: 1,
        allowedFileTypes: ['image', 'pdf']
    });

</script>
