<?php
    include 'header.php';
    
    $title = HM_answer;
    $table = 'competition_questions_choices';
    $table_id = 'id';
    
    if (isset($_POST['send'])) {
        
        try {
            $sql = $db->prepare("REPLACE INTO $table VALUES (
	:id,
	:choice_ar,
	:choice_en,
	:choice_ur,
	:question_id,
	:correct
	)");
            
            // check if new or update
            if (is_numeric($_GET['id'])) $id = $_GET['id'];
            else $id = '';
            
            if ($_POST['answer'] == 1) {
                $_POST['answer'] = 1;
            } else {
                $_POST['answer'] = 0;
            }
            $sql->bindValue("id", $id);
            $sql->bindValue("choice_ar", $_POST['choice_ar']);
            $sql->bindValue("choice_en", $_POST['choice_en']);
            $sql->bindValue("choice_ur", $_POST['choice_ur']);
            $sql->bindValue("question_id", $_POST['question_id']);
            $sql->bindValue("correct", $_POST['answer']);
            
            
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
                                    <label><?= LBL_choice; ?> AR </label>
                                    <textarea name="choice_ar" required
                                              class="form-control"><?php echo $_POST['choice_ar'] ?: $row['choice_ar'] ?></textarea>
                                </div>
                                <div class="form-group col-sm-4">
                                    <label><?= LBL_choice; ?> EN</label>
                                    <textarea name="choice_en" required
                                              class="form-control"><?php echo $_POST['choice_en'] ?: $row['choice_en'] ?></textarea>
                                </div>
                                <div class="form-group col-sm-4">
                                    <label><?= LBL_choice; ?> UR </label>
                                    <textarea name="choice_ur" required
                                              class="form-control"><?php echo $_POST['choice_ur'] ?: $row['choice_ur'] ?></textarea>
                                </div>

                                <div class="form-group col-sm-4">
                                    <label><?= HM_question; ?> </label>
                                    <select class="form-control" name="question_id">
                                        <?php
                                            $stmt = $db->prepare("SELECT  * FROM competition_questions ");
                                            $stmt->execute();
                                            $questions = $stmt->fetchAll();
                                            foreach ($questions as $item) {
                                                ?>
                                                <option <?php if ((isset($_POST['question_id']) && $_POST['question_id'] == $item['id']) || $row['question_id'] == $item['id']) {
                                                    echo "selected";
                                                } ?> value="<?php echo $item['id']; ?>"><?php echo $item['question_' . $_COOKIE['lang']] ?></option>
                                            <?php } ?>
                                    </select>

                                </div>
                                <div class="form-group col-sm-4" style="margin-top: 21px;">
                                    <label><?= HM_modelanswer; ?></label>
                                    <input type="checkbox" name="answer"
                                           value="1" <?php if ((isset($_POST['answer']) && $_POST['answer'] == 1) || ($row['correct'] == 1)) {
                                        echo "checked";
                                    } ?> />
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
