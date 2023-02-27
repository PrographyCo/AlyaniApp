<?php
    global $db, $url, $lang, $session;
    
    $edit = false;
    $title = HM_question;
    $table = 'competition_questions';
    $table_id = 'id';
    
    if (isset($_REQUEST['id']) && is_numeric($_REQUEST['id'])) $id = $_REQUEST['id'];
    else $id = '';
    
    if (isset($_POST['send'])) {
        
        try {
            $sql = $db->prepare("REPLACE INTO $table VALUES (
	:id,
	:question_ar,
	:question_en,
	:question_ur,
	:competition_id
	)");
            
            $sql->bindValue("id", $id);
            $sql->bindValue("question_ar", $_POST['question_ar']);
            $sql->bindValue("question_en", $_POST['question_en']);
            $sql->bindValue("question_ur", $_POST['question_ur']);
            $sql->bindValue("competition_id", $_POST['competition_id']);
            
            
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
    
    if (is_numeric($id)) {
        
        $edit = true;
        $row = $db->query("SELECT * FROM $table WHERE $table_id = " . $id)->fetch();
        
    }

?>
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

                            <div class="row">

                                <div class="form-group col-sm-4">
                                    <label><?= LBL_TitleAr ?></label>
                                    <textarea name="question_ar" required
                                              class="form-control"><?= $_POST['question_ar'] ?? $row['question_ar'] ?? '' ?></textarea>
                                </div>
                                <div class="form-group col-sm-4">
                                    <label><?= LBL_TitleEn ?></label>
                                    <textarea name="question_en" required
                                              class="form-control"><?= $_POST['question_en'] ?? $row['question_en'] ?? '' ?></textarea>
                                </div>
                                <div class="form-group col-sm-4">
                                    <label><?= LBL_TitleUr ?></label>
                                    <textarea name="question_ur" required
                                              class="form-control"><?= $_POST['question_ur'] ?? $row['question_ur'] ?? '' ?></textarea>
                                </div>

                                <div class="form-group col-sm-4">
                                    <label><?= HM_competitions ?> </label>
                                    <select class="form-control" name="competition_id">
                                        <?php
                                            $stmt = $db->prepare("SELECT  * FROM competitions ");
                                            $stmt->execute();
                                            $competitions = $stmt->fetchAll();
                                            foreach ($competitions as $competition) {
                                                ?>
                                                <option <?php if ((isset($_POST['competition_id']) && $_POST['competition_id'] == $competition['id']) || (isset($row['competition_id']) && $row['competition_id'] == $competition['id'])) {
                                                    echo "selected";
                                                } ?> value="<?= $competition['id'] ?>"><?= $competition['name_' . $_COOKIE['lang']] ?></option>
                                            <?php } ?>
                                    </select>

                                </div>


                            </div>


                            <input type="submit" name="send" class="col-md-12 btn btn-success"
                                   value="<?= $edit ? LBL_Update : LBL_Add ?>"/>
                            <input type="hidden" name="id" id="id" value="<?= $id ?>"/>

                        </form>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div><!--/.col (left) -->
        </div>   <!-- /.row -->
    </section><!-- /.content -->

</div>

<?php include 'layout/footer.php'; ?>
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
												<label><?=LBL_question?></label>
												<input type="text" class="form-control" name="question[]" required />
											</div>
										
											<div id="choices` + i + `">
											    <div class="row">
    										    	<div class="form-group col-sm-4">
        												<label><?=LBL_choice?></label>
        												<input type="text" class="form-control" name="choice` + i + `[]" required />
    										    	</div>
										    	</div>
										
										    </div>
										    
								
										</div>`);

    });

    $("#addnewchoice").click(function () {
        $("#choices" + i).append(`<div class="row">
           
											<div class="form-group col-sm-4">
												<label><?=LBL_choice?></label>
												<input type="text" class="form-control" name="choice` + i + `[]" />
											</div>
											<div class="form-group col-sm-4">
												<button type="button" class="btn btn-danger deletebtn" style="margin-top: 26px;"><?=LBL_Delete?></button>
											</div>
										
								
										</div>`);

    });

    $(document).on('click', '.deletebtn', function () {
        $(this).parent().parent().remove();
    });
</script>
