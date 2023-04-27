<?php
    global $db,$session,$lang,$url;
    
    $edit = false;
    $title = HM_Stuff;
    $table = 'suites_halls_stuff';
    $table_id = 'stuff_id';
    
    if (isset($_REQUEST['id']) && is_numeric($_REQUEST['id'])) $id = $_REQUEST['id'];
    else $id = '';
    
    if ($_REQUEST['id'] > 0) {
        $label = LBL_Updated;
        $edit = true;
    }else {
        $label = LBL_Added;
        $edit = false;
    }
    
    if ($_POST) {
        
        $chk = $db->prepare("SELECT $table_id FROM $table WHERE stuff_title = :title AND hall_id = :hall_id AND $table_id != '$id' LIMIT 1");
        $chk->bindValue("title", $_POST['stuff_title']);
        $chk->bindValue("hall_id", $_POST['hall_id']);
        $chk->execute();
        
        if (!$edit && $chk->rowCount() > 0) {
            
            $msg = '<div class="alert alert-danger alert-dismissable"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button><h4><i class="icon fa fa-check"></i>' . LBL_Error . '</h4>' . LBL_Title . ' ' . LBL_AlreadyExists . '</div>';
            
        } else {
            
            try {
                
                $sql = $db->prepare("REPLACE INTO $table VALUES (
			:id,
			:hall_id,
			:stuff_title,
			:stuff_type,
			:stuff_order,
			:stuff_active,
			:stuff_dateadded,
			:stuff_lastupdated
			)");
                
                $sql->bindValue("id", $id);
                $sql->bindValue("hall_id", $_POST['hall_id']);
                $sql->bindValue("stuff_title", $_POST['stuff_title']);
                $sql->bindValue("stuff_type", $_POST['stuff_type']);
                $sql->bindValue("stuff_order", $_POST['stuff_order']);
                $sql->bindValue("stuff_active", (isset($_POST['stuff_active']) ? 1 : 0));
                $sql->bindValue("stuff_dateadded", ($_POST['stuff_dateadded'] ?? time()));
                $sql->bindValue("stuff_lastupdated", time());
                
                if ($sql->execute()) {
                    $result = '';
                    $id = $db->lastInsertId();
                    
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
    
    if (is_numeric($id)) {
        
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
                                <label><?= HM_Hall ?></label>
                                <select name="hall_id" class="form-control select2">
                                    <?php
                                        $sqls = $db->query("SELECT hall_id, hall_title FROM suites_halls WHERE hall_active = 1 ORDER BY hall_order");
                                        while ($rows = $sqls->fetch(PDO::FETCH_ASSOC)) {
                                            
                                            echo '<option value="' . $rows['hall_id'] . '" ';
                                            if ((isset($row) && $row['hall_id'] == $rows['hall_id']) || (isset($_GET['hall_id']) && ((int)$_GET['hall_id']) == (int)$rows['hall_id'])) echo 'selected="selected"';
                                            echo '>' . $rows['hall_title'] . '</option>';
                                            
                                        }
                                    ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label><?= LBL_Title ?></label>
                                <input type="text" class="form-control" name="stuff_title" required="required"
                                       value="<?= $_POST['stuff_title'] ?? $row['stuff_title'] ?? '' ?>"/>
                            </div>
                            
                            <div class="form-group">
                                <label><?= Type ?></label>
                                <select name="stuff_type" class="form-control select2">
                                    <option value="bed" <?= (isset($row) && $row['stuff_type'] == 'bed')? 'selected=selected':'' ?>><?= LBL_Bed ?></option>
                                    <option value="chair" <?= (isset($row) && $row['stuff_type'] == 'chair')? 'selected=selected':'' ?>><?= LBL_Chair1 ?></option>
                                    <option value="bench" <?= (isset($row) && $row['stuff_type'] == 'bench')? 'selected=selected':'' ?>><?= LBL_Chair2 ?></option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label><?= LBL_Order ?></label>
                                <input type="text" class="form-control" name="stuff_order"
                                       value="<?= $_POST['stuff_order'] ?? $row['stuff_order'] ?? '' ?>"/>
                            </div>

                            <div class="form-group">
                                <label><input type="checkbox"
                                              name="stuff_active" <?php if (!isset($row) || $row['stuff_active']==1) echo 'checked="checked"'; ?> /> <?= LBL_Active ?>
                                </label>
                            </div>

                            <input type="submit" class="col-md-12 btn btn-success"
                                   value="<?= $edit ? LBL_Update : LBL_Add ?>"/>
                            <input type="hidden" name="id" id="id" value="<?= $id ?>"/>
                            <input type="hidden" name="stuff_dateadded" id="stuff_dateadded"
                                   value="<?= $row['stuff_dateadded']??time() ?>"/>
                        </form>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div><!--/.col (left) -->
        </div>   <!-- /.row -->
    </section><!-- /.content -->

</div>

<script>
    $('select').select2();
</script>
