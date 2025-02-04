<?php
    global $db;
    
    $stmt = $db->prepare("SELECT * FROM feedback WHERE is_read=?");
    $stmt->execute(array(0));
    $feedback = $stmt->rowCount();
?>
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">

        <center>
            <img src="<?= CP_PATH ?>/assets/images/logo-card-header.png" style="width:50%; min-width:200px; padding-top:2%; margin-bottom:30px"/>
        </center>


    </section><!-- /.content -->
    
    <?php if ($feedback > 0) { ?>
        <div class="alert alert-danger alert-dismissible fade show" style="opacity:1;" role="alert">
            <?= msg_feedback_not_read; ?> <strong>( <?php echo $feedback . ' ' . msg; ?> ) </strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php } ?>
</div><!-- /.content-wrapper -->
