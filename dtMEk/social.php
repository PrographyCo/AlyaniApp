<?php include 'header.php';
    
    if ($_POST) {
        
        try {
            
            $sql1 = $db->prepare("UPDATE socials SET ws_url = :ws_facebook WHERE ws_id = 1");
            $sql1->bindValue("ws_facebook", $_POST['ws_facebook']);
            $sql1->execute();
            
            $sql2 = $db->prepare("UPDATE socials SET ws_url = :ws_twitter WHERE ws_id = 2");
            $sql2->bindValue("ws_twitter", $_POST['ws_twitter']);
            $sql2->execute();
            
            $sql3 = $db->prepare("UPDATE socials SET ws_url = :ws_whatsapp WHERE ws_id = 3");
            $sql3->bindValue("ws_whatsapp", $_POST['ws_whatsapp']);
            $sql3->execute();
            
            $sql4 = $db->prepare("UPDATE socials SET ws_url = :ws_snapchat WHERE ws_id = 4");
            $sql4->bindValue("ws_snapchat", $_POST['ws_snapchat']);
            $sql4->execute();
            
            $sql5 = $db->prepare("UPDATE socials SET ws_url = :ws_instagram WHERE ws_id = 5");
            $sql5->bindValue("ws_instagram", $_POST['ws_instagram']);
            $sql5->execute();
            
            $label = LBL_Updated;
            
            $msg = '<div class="alert alert-success alert-dismissable"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button><h4><i class="icon fa fa-check"></i>' . $label . '!</h4>' . LBL_Item . ' ' . $label . ' ' . LBL_Successfully . '<br />' . $result . '</div>';
            
            unset($_POST);
            
        } catch (PDOException $e) {
            
            $msg = '<div class="alert alert-danger alert-dismissable"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button><h4><i class="icon fa fa-check"></i>Error</h4>Error Updating / Inserting Record, Database Error: ' . $e->getMessage() . '</div>';
            
        }
        
    }
    
    $ws_facebook = $db->query("SELECT ws_url FROM socials WHERE ws_id = 1")->fetchColumn();
    $ws_twitter = $db->query("SELECT ws_url FROM socials WHERE ws_id = 2")->fetchColumn();
    $ws_whatsapp = $db->query("SELECT ws_url FROM socials WHERE ws_id = 3")->fetchColumn();
    $ws_snapchat = $db->query("SELECT ws_url FROM socials WHERE ws_id = 4")->fetchColumn();
    $ws_instagram = $db->query("SELECT ws_url FROM socials WHERE ws_id = 5")->fetchColumn();

?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <!-- left column -->
            <div class="col-md-12">
                <?php echo $msg; ?>

                <div class="box">
                    <div class="box-header">

                        <h3 class="box-title"><?= HM_Social; ?></h3>
                    </div><!-- /.box-header -->
                    <div class="box-body">

                        <form role="form" method="post" enctype="multipart/form-data">

                            <div class="form-group">
                                <label><?= LBL_Facebook; ?></label>
                                <input type="text" class="form-control" name="ws_facebook"
                                       value="<?php echo $_POST['ws_facebook'] ?: $ws_facebook; ?>"/>
                            </div>

                            <div class="form-group">
                                <label><?= LBL_Twitter; ?></label>
                                <input type="text" class="form-control" name="ws_twitter"
                                       value="<?php echo $_POST['ws_twitter'] ?: $ws_twitter; ?>"/>
                            </div>

                            <div class="form-group">
                                <label><?= LBL_Whatsapp; ?></label>
                                <input type="text" class="form-control" name="ws_whatsapp"
                                       value="<?php echo $_POST['ws_whatsapp'] ?: $ws_whatsapp; ?>"/>
                            </div>

                            <div class="form-group">
                                <label><?= LBL_Snapchat; ?></label>
                                <input type="text" class="form-control" name="ws_snapchat"
                                       value="<?php echo $_POST['ws_snapchat'] ?: $ws_snapchat; ?>"/>
                            </div>

                            <div class="form-group">
                                <label><?= LBL_Instagram; ?></label>
                                <input type="text" class="form-control" name="ws_instagram"
                                       value="<?php echo $_POST['ws_instagram'] ?: $ws_instagram; ?>"/>
                            </div>


                            <input type="submit" class="col-md-12 btn btn-success" value="<?= LBL_Update; ?>"/>

                        </form>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div><!--/.col (right) -->
        </div>   <!-- /.row -->
    </section><!-- /.content -->
</div>

<?php include 'footer.php'; ?>
