<?php include 'header.php';
    
    // $title = HM_Tents;
    $table = 'tents';
    $table_id = 'tent_id';
    if (isset($_GET['type']) && $_GET['type'] == 2) {
        $title = HM_Halls . ' - ' . arafa;
    } else {
        $title = HM_Tents . ' - ' . mozdalifa;
    }
    
    if ($_POST) {
        
        // check if new or update
        if (is_numeric($_GET['id'])) $id = $_GET['id'];
        else $id = '';
        
        if (isset($_GET['type']) && $_GET['type'] == 2) {
            $type = 2;
        } else {
            $type = 1;
        }
        
        $chk = $db->prepare("SELECT $table_id FROM $table WHERE tent_title = :title AND type = :type AND $table_id != '$id' LIMIT 1");
        $chk->bindValue("title", $_POST['tent_title']);
        $chk->bindValue("type", $type);
        $chk->execute();
        
        if ($chk->rowCount() > 0) {
            
            $msg = '<div class="alert alert-danger alert-dismissable"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button><h4><i class="icon fa fa-check"></i>' . LBL_Error . '</h4>' . LBL_Title . ' ' . LBL_AlreadyExists . '</div>';
            
        } else {
            
            try {
                
                $sql = $db->prepare("REPLACE INTO $table VALUES (
			:id,
			:tent_title,
			:tent_type,
			:tent_gender,
			:tent_capacity,
			:tent_order,
			:tent_active,
			:tent_dateadded,
			:tent_lastupdated,
			:type
			)");
                
                
                $sql->bindValue("id", $id);
                $sql->bindValue("tent_title", $_POST['tent_title']);
                $sql->bindValue("tent_type", $_POST['tent_type']);
                $sql->bindValue("tent_gender", $_POST['tent_gender']);
                $sql->bindValue("tent_capacity", $_POST['tent_capacity']);
                $sql->bindValue("tent_order", $_POST['tent_order']);
                $sql->bindValue("tent_active", ($_POST['tent_active'] ? 1 : 0));
                $sql->bindValue("tent_dateadded", ($_POST['tent_dateadded'] ?: time()));
                $sql->bindValue("tent_lastupdated", time());
                $sql->bindValue("type", $type);
                
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
                                <label><?php if (isset($_GET['type']) && $_GET['type'] == 2) {
                                        echo LBL_TentNumber_halls;
                                    } else {
                                        echo LBL_TentNumber;
                                    } ?></label>
                                <input type="text" class="form-control" name="tent_title" required="required"
                                       value="<?php echo $_POST['tent_title'] ?: $row['tent_title'] ?>"/>
                            </div>

                            <div class="form-group">
                                <label><?= LBL_Type; ?></label>
                                <select name="tent_type" class="form-control select2">
                                    <option value="1" <?php if ($row['tent_type'] == 1) echo 'selected="selected"'; ?>><?= LBL_TentType1; ?></option>
                                    <option value="2" <?php if ($row['tent_type'] == 2) echo 'selected="selected"'; ?>><?= LBL_TentType2; ?></option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label><?= LBL_Gender; ?></label>
                                <select name="tent_gender" class="form-control select2">
                                    <option value="m" <?php if ($row['tent_gender'] == 'm') echo 'selected="selected"'; ?>><?= LBL_Male; ?></option>
                                    <option value="f" <?php if ($row['tent_gender'] == 'f') echo 'selected="selected"'; ?>><?= LBL_Female; ?></option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label><?= LBL_Capacity; ?></label>
                                <input type="number" class="form-control" name="tent_capacity"
                                       value="<?php echo $_POST['tent_capacity'] ?: $row['tent_capacity'] ?>"/>
                            </div>

                            <div class="form-group">
                                <label><?= LBL_Order; ?></label>
                                <input type="text" class="form-control" name="tent_order"
                                       value="<?php echo $_POST['tent_order'] ?: $row['tent_order'] ?>"/>
                            </div>

                            <div class="form-group">
                                <label><input type="checkbox"
                                              name="tent_active" <?php if (!$row || $row['tent_active'] == 1) echo 'checked="checked"'; ?> /> <?= LBL_Active; ?>
                                </label>
                            </div>

                            <input type="submit" class="col-md-12 btn btn-success"
                                   value="<?php echo $edit ? LBL_Update : LBL_Add; ?>"/>
                            <input type="hidden" name="id" id="id" value="<?= $_GET['id']; ?>"/>
                            <input type="hidden" name="tent_dateadded" id="tent_dateadded"
                                   value="<?= $row['tent_dateadded']; ?>"/>
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
