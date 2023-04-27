<?php
    global $db,$session,$url, $lang;
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?= HM_UpdatePilsPhotos ?>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <!-- left column -->
            <div class="col-md-12">

                <div class="box">
                    <div class="box-body">

                        <form method="post" enctype="multipart/form-data">
                            <div class="form-group">
                                <label><?= LBL_File ?></label>
                                <input type="file" class="form-control" name="zipfile" id="zipfile" accept=".zip"/>
                            </div>
                            <input type="submit" class="col-md-12 btn btn-success" value="<?= LBL_Upload ?>"/>
                        </form>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
                
                
                <?php
                    if (!empty($_FILES)) {
                        
                        if ($_FILES['zipfile']['name']) {
                            
                            $zip = new ZipArchive;
                            $res = $zip->open($_FILES['zipfile']['tmp_name']);
                            if ($res == TRUE) {
                                $zip->extractTo(ASSETS_PATH.'media/zip_photos/');
                                $zip->close();
                                
                                foreach (new DirectoryIterator(ASSETS_PATH.'media/zip_photos') as $fileInfo) {
                                    
                                    if ($fileInfo->isDot()) continue;
                                    
                                    $path_parts = pathinfo($fileInfo->getFilename());
                                    $nationalid = $path_parts['filename'];
                                    $extension = $path_parts['extension'];
                                    
                                    echo LBL_NationalId . ": " . $nationalid . " ";
                                    
                                    if (is_numeric($nationalid)) {
                                        
                                        // Check if this is an image
                                        if (@is_array(getimagesize(ASSETS_PATH.'media/zip_photos/' . $fileInfo->getFilename()))) {
                                            
                                            // Lets see if this nationalid exists
                                            $pil_id = $db->query("SELECT pil_id FROM pils WHERE pil_nationalid = $nationalid")->fetchColumn();
                                            if ($pil_id > 0) {
                                                
                                                // lets copy the image
                                                $newname = GUID();
                                                if (copy(ASSETS_PATH.'media/zip_photos/' . $fileInfo->getFilename(), ASSETS_PATH.'media/pils/' . $newname . '.' . $extension)) {
                                                    $sqlupd = $db->query("UPDATE pils SET pil_photo = '$newname.$extension', pil_lastupdated = " . time() . " WHERE pil_id = $pil_id");
                                                    echo LBL_Updated . " " . LBL_Successfully;
                                                } else {
                                                    echo LBL_Error;
                                                }
                                                
                                            } else {
                                                
                                                echo LBL_NotFound;
                                                
                                            }
                                            
                                        } else {
                                            
                                            echo LBL_NotAnImage;
                                        }
                                        
                                    } else {
                                        
                                        echo LBL_InvalidNationalID;
                                        
                                    }
                                    
                                    if (is_file(ASSETS_PATH.'media/zip_photos/' . $fileInfo->getFilename())) @unlink(ASSETS_PATH.'media/zip_photos/' . $fileInfo->getFilename());
                                    echo '<br />';
                                    
                                }
                                
                                
                            } else {
                                echo 'Error';
                            }
                            
                        }
                        
                    }
                
                ?>

            </div><!--/.col (right) -->

        </div>   <!-- /.row -->
    </section><!-- /.content -->

</div>
<script>
    $("#zipfile").fileinput({
        showRemove: true,
        showUpload: false,
        showCancel: true,
        showPreview: true,
        minFileCount: 0,
        maxFileCount: 1,
        allowedFileTypes: ['zip']
    });
</script>
