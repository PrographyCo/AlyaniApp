<?php include 'header.php';
    
    $title = HM_Suites;
    $table = 'suites';
    $table_id = 'suite_id';
    
    if ($_POST) {
        
        // check if new or update
        if (is_numeric($_GET['id'])) $id = $_GET['id'];
        else $id = '';
        
        $chk = $db->prepare("SELECT $table_id FROM $table WHERE suite_title = :title AND $table_id != '$id' LIMIT 1");
        $chk->bindValue("title", $_POST['suite_title']);
        $chk->execute();
        
        if ($chk->rowCount() > 0) {
            
            $msg = '<div class="alert alert-danger alert-dismissable"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button><h4><i class="icon fa fa-check"></i>' . LBL_Error . '</h4>' . LBL_Title . ' ' . LBL_AlreadyExists . '</div>';
            
        } else {
            
            try {
                
                $sql = $db->prepare("REPLACE INTO $table VALUES (
			:id,
			:suite_title,
			:suite_gender,
			:suite_order,
			:suite_active,
			:suite_dateadded,
			:suite_lastupdated
			)");
                
                $sql->bindValue("id", $id);
                $sql->bindValue("suite_title", $_POST['suite_title']);
                $sql->bindValue("suite_gender", $_POST['suite_gender']);
                $sql->bindValue("suite_order", $_POST['suite_order']);
                $sql->bindValue("suite_active", ($_POST['suite_active'] ? 1 : 0));
                $sql->bindValue("suite_dateadded", ($_POST['suite_dateadded'] ?: time()));
                $sql->bindValue("suite_lastupdated", time());
                
                if ($sql->execute()) {
                    
                    $id = $db->lastInsertId();
                    
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


                            <div class="form-group">
                                <label><?= LBL_SuiteNumber; ?></label>
                                <input type="text" class="form-control" name="suite_title" required="required"
                                       value="<?php echo $_POST['suite_title'] ?: $row['suite_title'] ?>"/>
                            </div>

                            <div class="form-group">
                                <label><?= LBL_Gender; ?></label>
                                <select name="suite_gender" class="form-control select2">
                                    <option value="m" <?php if ($row['suite_gender'] == 'm') echo 'selected="selected"'; ?>><?= LBL_Male; ?></option>
                                    <option value="f" <?php if ($row['suite_gender'] == 'f') echo 'selected="selected"'; ?>><?= LBL_Female; ?></option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label><?= LBL_Order; ?></label>
                                <input type="text" class="form-control" name="suite_order"
                                       value="<?php echo $_POST['suite_order'] ?: $row['suite_order'] ?>"/>
                            </div>

                            <div class="form-group">
                                <label><input type="checkbox"
                                              name="suite_active" <?php if (!$row || $row['suite_active'] == 1) echo 'checked="checked"'; ?> /> <?= LBL_Active; ?>
                                </label>
                            </div>

                            <input type="submit" class="col-md-12 btn btn-success"
                                   value="<?php echo $edit ? LBL_Update : LBL_Add; ?>"/>
                            <input type="hidden" name="id" id="id" value="<?= $_GET['id']; ?>"/>
                            <input type="hidden" name="suite_dateadded" id="suite_dateadded"
                                   value="<?= $row['suite_dateadded']; ?>"/>
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
</script>
