<?php
    global $db, $session, $url, $lang;
    
    $edit = false;
    $title = HM_Buildings;
    $table = 'buildings';
    $table_id = 'bld_id';
    
    if (isset($_REQUEST['id']) && is_numeric($_REQUEST['id'])) $id = $_REQUEST['id'];
    else $id = '';
    
    if ($_POST) {
        
        $chk = $db->prepare("SELECT $table_id FROM $table WHERE bld_title = :title AND $table_id != '$id' LIMIT 1");
        $chk->bindValue("title", $_POST['suite_title'] ?? '');
        $chk->execute();
        
        if ($chk->rowCount() > 0) {
            
            $msg = '<div class="alert alert-danger alert-dismissable"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button><h4><i class="icon fa fa-check"></i>' . LBL_Error . '</h4>' . LBL_Title . ' ' . LBL_AlreadyExists . '</div>';
            
        } else {
            
            try {
                
                $sql = $db->prepare("REPLACE INTO $table VALUES (
                           :id,
                           :bld_title,
                           :bld_type,
                           :bld_order,
                           :bld_active,
                           :bld_dateadded,
                           :bld_lastupdated
                       )");
                
                $sql->bindValue("id", $id);
                $sql->bindValue("bld_title", $_POST['bld_title']);
                $sql->bindValue("bld_type", $_POST['bld_type']);
                $sql->bindValue("bld_order", $_POST['bld_order']);
                $sql->bindValue("bld_active", (isset($_POST['bld_active']) ? 1 : 0));
                $sql->bindValue("bld_dateadded", ($_POST['bld_dateadded'] ?? time()));
                $sql->bindValue("bld_lastupdated", time());
                
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
                                <label><?= LBL_BuildingNumber ?></label>
                                <input type="text" class="form-control" name="bld_title" required="required"
                                       value="<?= $_POST['bld_title'] ?? $row['bld_title'] ?? '' ?>"/>
                            </div>

                            <div class="form-group">
                                <label><?= LBL_Type ?></label>
                                <select name="bld_type" class="form-control select2">
                                    <option value="1" <?php if (isset($row) && $row['bld_type'] == 1) echo 'selected="selected"'; ?>>
                                        <?= HM_Building ?>
                                    </option>
                                    <option value="2" <?php if (isset($row) && $row['bld_type'] == 2) echo 'selected="selected"'; ?>>
                                        <?= LBL_Premises ?>
                                    </option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label><?= LBL_Order ?></label>
                                <input type="text" class="form-control" name="bld_order"
                                       value="<?= $_POST['bld_order'] ?? $row['bld_order'] ?? '' ?>"/>
                            </div>

                            <div class="form-group">
                                <label><input type="checkbox"
                                              name="bld_active" <?php if (!isset($row) || $row['bld_active'] == 1) echo 'checked="checked"'; ?> /> <?= LBL_Active ?>
                                </label>
                            </div>

                            <input type="submit" class="col-md-12 btn btn-success"
                                   value="<?= $edit ? LBL_Update : LBL_Add ?>"/>
                            <input type="hidden" name="id" id="id" value="<?= $id ?>"/>
                            <input type="hidden" name="bld_dateadded" id="bld_dateadded"
                                   value="<?= $row['bld_dateadded'] ?? time() ?>"/>
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
