<?php
    include 'header.php';
    
    if ($_POST) {
        
        try {
            
            $sql = $db->prepare("UPDATE aboutus SET
    au_type = :au_type,
    au_body_ar = :au_body_ar,
    au_body_en = :au_body_en,
    au_body_ur = :au_body_ur,
    au_youtube_id = :au_youtube_id,
    au_videofile = :au_videofile,
    au_lastupdated = " . time() . "
    WHERE au_id = 1
    ");
            $sql->bindValue("au_type", $_POST['au_type']);
            $sql->bindValue("au_body_ar", $_POST['au_body_ar']);
            $sql->bindValue("au_body_en", $_POST['au_body_en']);
            $sql->bindValue("au_body_ur", $_POST['au_body_ur']);
            $sql->bindValue("au_youtube_id", $_POST['au_youtube_id']);
            $sql->bindValue("au_videofile", $_POST['au_videofile']);
            $sql->execute();
            
            if ($_FILES['au_uvideofile']['tmp_name']) {
                $ext = strtolower(pathinfo($_FILES['au_uvideofile']['name'], PATHINFO_EXTENSION));
                if (copy($_FILES['au_uvideofile']['tmp_name'], 'media/aboutusvid/1.' . $ext)) {
                    
                    $sql2 = $db->query("UPDATE aboutus SET au_videofile = '1." . $ext . "' WHERE au_id = 1");
                    $result .= LBL_VideoUploaded . '<br />';
                    
                }
            }
            
            
            $msg = '<div class="alert alert-success alert-dismissable"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button><h4><i class="icon fa fa-check"></i>' . LBL_Updated . '!</h4>' . LBL_Item . ' ' . LBL_Updated . ' ' . LBL_Successfully . '<br />' . $result . '</div>';
            
            unset($_POST);
            
        } catch (PDOException $e) {
            
            $msg = '<div class="alert alert-danger alert-dismissable"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button><h4><i class="icon fa fa-check"></i>' . LBL_Error . '</h4>' . LBL_ErrorDB . ' ' . $e->getMessage() . '</div>';
            
        }
        
        
    }
    
    $auinfo = $db->query("SELECT * FROM aboutus WHERE au_id = 1 LIMIT 1")->fetch(PDO::FETCH_ASSOC);
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

                        <h3 class="box-title"><?= HM_Aboutus; ?></h3>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <form method="post" enctype="multipart/form-data">

                            <div class="form-group">
                                <label><?= LBL_Type; ?></label>
                                <select name="au_type" class="form-control select2" onchange="typechanged(this.value)">
                                    <option value="1" <?php if ($auinfo['au_type'] == 1) echo 'selected="selected"'; ?>><?= LBL_Text; ?></option>
                                    <option value="2" <?php if ($auinfo['au_type'] == 2) echo 'selected="selected"'; ?>><?= LBL_Video; ?></option>
                                    <option value="3" <?php if ($auinfo['au_type'] == 3) echo 'selected="selected"'; ?>><?= LBL_YTVideo; ?></option>
                                </select>


                            </div>

                            <hr/>

                            <div id="div_text"
                                 style="<?php if ($auinfo['au_type'] == 1) echo 'display:block'; else echo 'display:none'; ?>">
                                <div class="form-group">
                                    <label><?= LBL_DescrAr; ?></label>
                                    <textarea name="au_body_ar"
                                              class="form-control"><?= $auinfo['au_body_ar']; ?></textarea>
                                </div>

                                <div class="form-group">
                                    <label><?= LBL_DescrEn; ?></label>
                                    <textarea name="au_body_en"
                                              class="form-control"><?= $auinfo['au_body_en']; ?></textarea>
                                </div>

                                <div class="form-group">
                                    <label><?= LBL_DescrUr; ?></label>
                                    <textarea name="au_body_ur"
                                              class="form-control"><?= $auinfo['au_body_ur']; ?></textarea>
                                </div>

                            </div>

                            <div id="div_video"
                                 style="<?php if ($auinfo['au_type'] == 2) echo 'display:block'; else echo 'display:none'; ?>">
                                <div class="form-group">
                                    <label><?= LBL_Video; ?></label>
                                    <input type="file" name="au_uvideofile" id="au_uvideofile" class="form-control"/>
                                    <?
                                        if ($auinfo['au_videofile']) {
                                            
                                            $vidext = strtolower(pathinfo($auinfo['au_videofile'], PATHINFO_EXTENSION));
                                            echo ' <video width="320" height="240" controls>
                              <source src="media/aboutusvid/' . $auinfo['au_videofile'] . '" type="video/' . $vidext . '">
                            Your browser does not support the video tag.
                            </video> ';
                                        
                                        }
                                    ?>
                                </div>
                            </div>

                            <div id="div_ytvideo"
                                 style="<?php if ($auinfo['au_type'] == 3) echo 'display:block'; else echo 'display:none'; ?>">
                                <div class="form-group">
                                    <label><?= LBL_YTVideoID; ?></label>
                                    <input type="text" name="au_youtube_id" id="au_youtube_id" class="form-control"
                                           value="<?= $auinfo['au_youtube_id']; ?>"/>
                                    <?php if ($auinfo['au_youtube_id']) {
                                        
                                        echo '<iframe width="560" height="315" src="https://www.youtube.com/embed/' . $auinfo['au_youtube_id'] . '" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
                                        
                                    } ?>
                                </div>
                            </div>
                            <input type="hidden" name="au_videofile" value="<?= $auinfo['au_videofile']; ?>"/>
                            <input type="submit" class="col-md-12 btn btn-success" value="<?php echo LBL_Update; ?>"/>

                        </form>

                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div><!--/.col (right) -->

        </div>   <!-- /.row -->
    </section><!-- /.content -->

</div>

<?php include 'footer.php'; ?>
<script>
    function typechanged(type_id) {

        if (type_id == 1) {

            $('#div_video').hide();
            $('#div_ytvideo').hide();
            $('#div_text').show();

        } else if (type_id == 2) {

            $('#div_text').hide();
            $('#div_ytvideo').hide();
            $('#div_video').show();


        } else if (type_id == 3) {

            $('#div_video').hide();
            $('#div_text').hide();
            $('#div_ytvideo').show();

        }

    }

    $("#au_uvideofile").fileinput({
        showRemove: true,
        showUpload: false,
        showCancel: false,
        showPreview: true,
        minFileCount: 0,
        maxFileCount: 1,
        allowedFileTypes: ['video']
    });
</script>
