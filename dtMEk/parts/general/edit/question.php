<?php
    global $db, $url, $lang, $session;
    
    $edit = false;
    $title = HM_RatingQuestions;
    $table = 'questions';
    $table_id = 'q_id';
    
    if (isset($_REQUEST['id']) && is_numeric($_REQUEST['id'])) $id = $_REQUEST['id'];
    else $id = '';
    
    if ($_POST) {
        
        try {
            $sql = $db->prepare("REPLACE INTO $table VALUES (
	:id,
	:q_title_ar,
	:q_title_en,
	:q_title_ur,
	:q_order,
	:q_active,
	:q_dateadded,
	:q_lastupdated
	)");
            
            $sql->bindValue("id", $id);
            $sql->bindValue("q_title_ar", $_POST['q_title_ar']);
            $sql->bindValue("q_title_en", $_POST['q_title_en']);
            $sql->bindValue("q_title_ur", $_POST['q_title_ur']);
            $sql->bindValue("q_order", $_POST['q_order']);
            $sql->bindValue("q_active", (isset($_POST['q_active']) ? 1 : 0));
            $sql->bindValue("q_dateadded", ($_POST['q_dateadded'] ?? time()));
            $sql->bindValue("q_lastupdated", time());
            
            if ($sql->execute()) {
                
                $q_id = $db->lastInsertId();
                
                // add / modify answers
                if (is_array($_POST['answer_ar']) && count($_POST['answer_ar'] > 0)) {
                    
                    // delete old
                    $sqldel = $db->query("DELETE FROM questions_answers WHERE qa_q_id = $q_id");
                    
                    foreach ($_POST['answer_ar'] as $key => $answer) {
                        
                        $answer_ar = $answer;
                        $answer_en = $_POST['answer_en'][$key];
                        $answer_ur = $_POST['answer_ur'][$key];
                        $order = $_POST['order'][$key];
                        
                        if ($answer_ar) {
                            
                            $sql = $db->prepare("INSERT INTO questions_answers VALUES (
					'',
					:qa_q_id,
					:qa_answer_ar,
					:qa_answer_en,
					:qa_answer_ur,
					:qa_order
					)");
                            
                            $sql->bindValue("qa_q_id", $q_id);
                            $sql->bindValue("qa_answer_ar", $answer_ar);
                            $sql->bindValue("qa_answer_en", $answer_en);
                            $sql->bindValue("qa_answer_ur", $answer_ur);
                            $sql->bindValue("qa_order", $order);
                            $sql->execute();
                            
                        }
                        
                    }
                    
                }
                
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

                            <div class="row">

                                <div class="form-group col-sm-4">
                                    <label><?= LBL_QuestionAr ?></label>
                                    <input type="text" class="form-control" name="q_title_ar" required="required"
                                           value="<?= $_POST['q_title_ar'] ?? $row['q_title_ar'] ?? '' ?>"/>
                                </div>

                                <div class="form-group col-sm-4">
                                    <label><?= LBL_QuestionEn ?></label>
                                    <input type="text" class="form-control" name="q_title_en" required="required"
                                           value="<?= $_POST['q_title_en'] ?? $row['q_title_en'] ?? '' ?>"/>
                                </div>

                                <div class="form-group col-sm-4">
                                    <label><?= LBL_QuestionUr ?></label>
                                    <input type="text" class="form-control" name="q_title_ur" required="required"
                                           value="<?= $_POST['q_title_ur'] ?? $row['q_title_ur'] ?? '' ?>"/>
                                </div>

                            </div>

                            <div class="form-group">

                                <div id="answers_area">
    
    
                                    <?php
                                        
                                        if (isset($row)) {
                                            
                                            $oldanswers = $db->query("SELECT * FROM questions_answers WHERE qa_q_id = " . $row['q_id'] . " ORDER BY qa_order");
                                            if ($oldanswers->rowCount() > 0) {
                                                
                                                while ($rowoa = $oldanswers->fetch(PDO::FETCH_ASSOC)) {
                                                    
                                                    echo '
														<div class="row">
															<div class="col-sm-3">
																<label>' . LBL_AnswerAr . '</label>
																<input type="text" class="form-control" name="answer_ar[]" value="' . $rowoa['qa_answer_ar'] . '" />
															</div>
															<div class="col-sm-3">
															<label>' . LBL_AnswerEn . '</label>
																<input type="text" class="form-control" name="answer_en[]" value="' . $rowoa['qa_answer_en'] . '" />
															</div>
															<div class="col-sm-3">
															<label>' . LBL_AnswerUr . '</label>
																<input type="text" class="form-control" name="answer_ur[]" value="' . $rowoa['qa_answer_ur'] . '" />
															</div>
															<div class="col-sm-2">
															<label>' . LBL_Order . '</label>
																<input type="text" class="form-control" name="order[]" value="' . $rowoa['qa_order'] . '" />
															</div>
															<div class="col-sm-1">
																<a href="#" onclick="removeanswer(this); return false;">' . LBL_Delete . '</a>
															</div>
														</div><br />';
                                                
                                                }
                                                
                                            }
                                            
                                        }
                                    ?>

                                </div>
                                <br/>
                                <a href="#" onclick="addanswer(); return false;">+ <?= LBL_AddAnswer ?></a>
                            </div>

                            <div class="form-group">
                                <label><?= LBL_Order ?></label>
                                <input type="text" class="form-control" name="q_order"
                                       value="<?= $_POST['q_order'] ?? $row['q_order'] ?? '' ?>"/>
                            </div>

                            <div class="form-group">
                                <label><input type="checkbox"
                                              name="q_active" <?php if (!isset($row) || $row['q_active'] == 1) echo 'checked="checked"'; ?>/>
                                    Active</label>
                            </div>


                            <input type="submit" class="col-md-12 btn btn-success"
                                   value="<?= $edit ? "Update" : "Add" ?>"/>
                            <input type="hidden" name="id" id="id" value="<?= $id ?>"/>

                        </form>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div><!--/.col (left) -->
        </div>   <!-- /.row -->
    </section><!-- /.content -->

</div>

<script>
    $('select').select2();

    function addanswer() {

        var output = '<div class="row"><div class="col-sm-3"><label><?=LBL_AnswerAr?></label><input type="text" class="form-control" name="answer_ar[]" /></div><div class="col-sm-3"><label><?=LBL_AnswerEn?></label><input type="text" class="form-control" name="answer_en[]" /></div><div class="col-sm-3"><label><?=LBL_AnswerUr?></label><input type="text" class="form-control" name="answer_ur[]" /></div><div class="col-sm-2"><label><?=LBL_Order?></label><input type="text" class="form-control" name="order[]" /></div><div class="col-sm-1"><a href="#" onclick="removeanswer(this); return false;"><?=LBL_Delete?></a></div></div><br />';
        $('#answers_area').append(output);

    }

    function removeanswer(el) {

        var confirmed = confirm('<?=LBL_DeleteConfirm?>');
        if (confirmed) {

            $(el).parent().parent().remove();

        }

    }
</script>
