<?php include 'header.php';
    
    $title = HM_Hall;
    $table = 'suites_halls';
    $table_id = 'hall_id';
    
    if ($_POST) {
        
        // check if new or update
        if (is_numeric($_GET['id'])) $id = $_GET['id'];
        else $id = '';
        
        $chk = $db->prepare("SELECT $table_id FROM $table WHERE hall_title = :title AND hall_suite_id = :hall_suite_id AND $table_id != '$id' LIMIT 1");
        $chk->bindValue("title", $_POST['hall_title']);
        $chk->bindValue("hall_suite_id", $_POST['hall_suite_id']);
        $chk->execute();
        
        if ($chk->rowCount() > 0) {
            
            $msg = '<div class="alert alert-danger alert-dismissable"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button><h4><i class="icon fa fa-check"></i>' . LBL_Error . '</h4>' . LBL_Title . ' ' . LBL_AlreadyExists . '</div>';
            
        } else {
            
            try {
                
                $sql = $db->prepare("REPLACE INTO $table VALUES (
			:id,
			:hall_suite_id,
			:hall_title,
			:hall_capacity,
			:hall_order,
			:hall_active,
			:hall_dateadded,
			:hall_lastupdated
			)");
                
                $sql->bindValue("id", $id);
                $sql->bindValue("hall_suite_id", $_POST['hall_suite_id']);
                $sql->bindValue("hall_title", $_POST['hall_title']);
                $sql->bindValue("hall_capacity", $_POST['hall_capacity']);
                $sql->bindValue("hall_order", $_POST['hall_order']);
                $sql->bindValue("hall_active", ($_POST['hall_active'] ? 1 : 0));
                $sql->bindValue("hall_dateadded", ($_POST['hall_dateadded'] ?: time()));
                $sql->bindValue("hall_lastupdated", time());
                
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
                                <label><?= HM_Suite; ?></label>
                                <select name="hall_suite_id" class="form-control select2">
                                    <?
                                        $sqls = $db->query("SELECT suite_id, suite_title FROM suites WHERE suite_active = 1 ORDER BY suite_order");
                                        while ($rows = $sqls->fetch(PDO::FETCH_ASSOC)) {
                                            
                                            echo '<option value="' . $rows['suite_id'] . '" ';
                                            if ($row['hall_suite_id'] == $rows['suite_id'] || $_GET['suite_id'] == $rows['suite_id']) echo 'selected="selected"';
                                            echo '>' . $rows['suite_title'] . '</option>';
                                            
                                        }
                                    ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label><?= LBL_Title; ?></label>
                                <input type="text" class="form-control" name="hall_title" required="required"
                                       value="<?php echo $_POST['hall_title'] ?: $row['hall_title'] ?>"/>
                            </div>

                            <div class="form-group">
                                <label><?= LBL_Capacity; ?></label>
                                <input type="number" class="form-control" name="hall_capacity"
                                       value="<?php echo $_POST['hall_capacity'] ?: $row['hall_capacity'] ?>"/>
                            </div>

                            <div class="form-group">
                                <label><?= LBL_Order; ?></label>
                                <input type="text" class="form-control" name="hall_order"
                                       value="<?php echo $_POST['hall_order'] ?: $row['hall_order'] ?>"/>
                            </div>

                            <div class="form-group">
                                <label><input type="checkbox"
                                              name="hall_active" <?php if (!$row || $row['hall_active'] == 1) echo 'checked="checked"'; ?> /> <?= LBL_Active; ?>
                                </label>
                            </div>

                            <input type="submit" class="col-md-12 btn btn-success"
                                   value="<?php echo $edit ? LBL_Update : LBL_Add; ?>"/>
                            <input type="hidden" name="id" id="id" value="<?= $_GET['id']; ?>"/>
                            <input type="hidden" name="hall_dateadded" id="hall_dateadded"
                                   value="<?= $row['hall_dateadded']; ?>"/>
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
