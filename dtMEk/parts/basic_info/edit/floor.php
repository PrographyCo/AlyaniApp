<?php
    global $db, $session, $url, $lang;
    
    $edit = false;
    $title = HM_Floor;
    $table = 'buildings_floors';
    $table_id = 'floor_id';
    
    if (isset($_REQUEST['id']) && is_numeric($_REQUEST['id'])) $id = $_REQUEST['id'];
    else $id = '';
    
    if ($_POST) {
        
        $chk = $db->prepare("SELECT $table_id FROM $table WHERE floor_title = :title AND floor_bld_id = :floor_bld_id AND $table_id != '$id' LIMIT 1");
        $chk->bindValue("title", $_POST['floor_title']);
        $chk->bindValue("floor_bld_id", $_POST['floor_bld_id']);
        $chk->execute();
        
        if ($chk->rowCount() > 0) {
            
            $msg = '<div class="alert alert-danger alert-dismissable"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button><h4><i class="icon fa fa-check"></i>' . LBL_Error . '</h4>' . LBL_Title . ' ' . LBL_AlreadyExists . '</div>';
            
        } else {
            
            try {
                
                $sql = $db->prepare("REPLACE INTO $table VALUES (
                           :id,
                           :floor_bld_id,
                           :floor_title,
                           :floor_order,
                           :floor_active,
                           :floor_dateadded,
                           :floor_lastupdated
                       )");
                
                $sql->bindValue("id", $id);
                $sql->bindValue("floor_bld_id", $_POST['floor_bld_id']);
                $sql->bindValue("floor_title", $_POST['floor_title']);
                $sql->bindValue("floor_order", $_POST['floor_order']);
                $sql->bindValue("floor_active", (isset($_POST['floor_active']) ? 1 : 0));
                $sql->bindValue("floor_dateadded", ($_POST['floor_dateadded'] ?? time()));
                $sql->bindValue("floor_lastupdated", time());
                
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
                                <label><?= HM_Building ?></label>
                                <select name="floor_bld_id" class="form-control select2">
                                    <?php
                                        $sqlb = $db->query("SELECT bld_id, bld_title FROM buildings WHERE bld_active = 1 ORDER BY bld_order");
                                        while ($rowb = $sqlb->fetch(PDO::FETCH_ASSOC)) {
                                            
                                            echo '<option value="' . $rowb['bld_id'] . '" ';
                                            if (isset($row) && $row['floor_bld_id'] == $rowb['bld_id']) echo 'selected="selected"';
                                            echo '>' . $rowb['bld_title'] . '</option>';
                                            
                                        }
                                    ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label><?= LBL_Title ?></label>
                                <input type="text" class="form-control" name="floor_title" required="required"
                                       value="<?= $_POST['floor_title'] ?? $row['floor_title'] ?? '' ?>"/>
                            </div>

                            <div class="form-group">
                                <label><?= LBL_Order ?></label>
                                <input type="text" class="form-control" name="floor_order"
                                       value="<?= $_POST['floor_order'] ?? $row['floor_order'] ?? '' ?>"/>
                            </div>

                            <div class="form-group">
                                <label><input type="checkbox"
                                              name="floor_active" <?php if (!isset($row) || $row['floor_active'] == 1) echo 'checked="checked"'; ?> /> <?= LBL_Active ?>
                                </label>
                            </div>

                            <input type="submit" class="col-md-12 btn btn-success"
                                   value="<?= $edit ? LBL_Update : LBL_Add ?>"/>
                            <input type="hidden" name="id" id="id" value="<?= $id ?>"/>
                            <input type="hidden" name="floor_dateadded" id="floor_dateadded"
                                   value="<?= $row['floor_dateadded'] ?? time() ?>"/>
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
