<?php
    include 'header.php';
    
    $title = HM_competitions;
    $table = 'competitions';
    $table_id = 'id';
    
    if (isset($_POST['send'])) {
        
        try {
            $sql = $db->prepare("REPLACE INTO $table VALUES (
                           :id,
                           :name_ar,
                           :name_en,
                           :name_ur,
                           :about_ar,
                           :about_en,
                           :about_ur
                       )");
            
            // check if new or update
            if (is_numeric($_GET['id'])) $id = $_GET['id'];
            else $id = '';
            
            $sql->bindValue("id", $id);
            $sql->bindValue("name_ar", $_POST['name_ar']);
            $sql->bindValue("name_en", $_POST['name_en']);
            $sql->bindValue("name_ur", $_POST['name_ur']);
            $sql->bindValue("about_ar", $_POST['about_ar']);
            $sql->bindValue("about_en", $_POST['about_en']);
            $sql->bindValue("about_ur", $_POST['about_ur']);
            
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
    
    if (is_numeric($_GET['id'])) {
        
        $edit = true;
        $row = $db->query("SELECT * FROM $table WHERE $table_id = " . $_GET['id'])->fetch();
        
    }

?>
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

                            <div class="row">

                                <div class="form-group col-sm-4">
                                    <label><?= LBL_TitleAr; ?></label>
                                    <textarea name="name_ar" required
                                              class="form-control"><?php echo $_POST['name_ar'] ?: $row['name_ar'] ?></textarea>
                                </div>
                                <div class="form-group col-sm-4">
                                    <label><?= LBL_TitleEn; ?></label>
                                    <textarea name="name_en" required
                                              class="form-control"><?php echo $_POST['name_en'] ?: $row['name_en'] ?></textarea>
                                </div>
                                <div class="form-group col-sm-4">
                                    <label><?= LBL_TitleUr; ?></label>
                                    <textarea name="name_ur" required
                                              class="form-control"><?php echo $_POST['name_ur'] ?: $row['name_ur'] ?></textarea>
                                </div>

                                <div class="form-group col-sm-4">
                                    <label><?= LBL_about; ?> AR</label>
                                    <textarea name="about_ar" class="form-control"
                                              required><?php echo $_POST['about_ar'] ?: $row['about_ar'] ?></textarea>
                                </div>
                                <div class="form-group col-sm-4">
                                    <label><?= LBL_about; ?> EN</label>
                                    <textarea name="about_en" class="form-control"
                                              required><?php echo $_POST['about_en'] ?: $row['about_en'] ?></textarea>
                                </div>
                                <div class="form-group col-sm-4">
                                    <label><?= LBL_about; ?> UR</label>
                                    <textarea name="about_ur" class="form-control"
                                              required><?php echo $_POST['about_ur'] ?: $row['about_ur'] ?></textarea>
                                </div>


                            </div>


                            <input type="submit" name="send" class="col-md-12 btn btn-success"
                                   value="<?php echo $edit ? LBL_Update : LBL_Add; ?>"/>
                            <input type="hidden" name="id" id="id" value="<?= $_GET['id']; ?>"/>

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
    $("#news_uphoto").fileinput({
        showRemove: true,
        showUpload: false,
        showCancel: false,
        showPreview: true,
        minFileCount: 0,
        maxFileCount: 1,
        allowedFileTypes: ['image', 'pdf']
    });
</script>

<script>
    var i = 1;
    $("#addnewquestion").click(function () {
        i++;
        $("#ques").append(`	<div class="row ">
                                            <div class="form-group col-sm-4">
												<label><?=LBL_question;?></label>
												<input type="text" class="form-control" name="question[]" required />
											</div>
										
											<div id="choices` + i + `">
											    <div class="row">
    										    	<div class="form-group col-sm-4">
        												<label><?=LBL_choice;?></label>
        												<input type="text" class="form-control" name="choice` + i + `[]" required />
    										    	</div>
										    	</div>
										
										    </div>
										    
								
										</div>`);

    });

    $("#addnewchoice").click(function () {
        $("#choices" + i).append(`<div class="row">
           
											<div class="form-group col-sm-4">
												<label><?=LBL_choice;?></label>
												<input type="text" class="form-control" name="choice` + i + `[]" />
											</div>
											<div class="form-group col-sm-4">
												<button type="button" class="btn btn-danger deletebtn" style="margin-top: 26px;"><?=LBL_Delete;?></button>
											</div>
										
								
										</div>`);

    });

    $(document).on('click', '.deletebtn', function () {
        $(this).parent().parent().remove();
    });
</script>
