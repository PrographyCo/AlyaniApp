<?php
    include 'layout/header.php';
    
    if ($_POST['design_id']) {
        
        $sqlupd = $db->query("UPDATE chosen_designs SET design_id = " . $_POST['design_id'] . " WHERE design_type = 1");
        
    }
    
    $design_id = $db->query("SELECT design_id FROM chosen_designs WHERE design_type = 1")->fetchColumn();
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
                        <h3 class="box-title"><?= HM_Designs_Pils; ?></h3>
                    </div><!-- /.box-header -->
                    <div class="box-body">

                        <form method="post">

                            <div class="row">
                                
                                <?
                                    for ($i = 1; $i <= 10; $i++) {
                                        
                                        echo '<div class="col-sm-4" style="margin-bottom:20px">
													<label><input type="radio" name="design_id" value="' . $i . '" ';
                                        if ($design_id == $i) echo 'checked="checked"';
                                        echo '/> <img src="pil_cards_designs/pils/' . $i . '/' . $i . '.png" style="height:300px;" /></label>
													</div>';
                                    
                                    }
                                ?>
                            </div>

                            <input type="submit" class="form-control btn btn-primary" value="<?= LBL_Update; ?>"/>

                        </form>

                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div><!--/.col (right) -->

        </div>   <!-- /.row -->
    </section><!-- /.content -->

</div>

<?php include 'layout/footer.php'; ?>
