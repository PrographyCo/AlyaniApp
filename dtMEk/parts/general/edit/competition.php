<?php
    global $db, $url, $lang, $session;
    
    $edit = false;
    $title = HM_competitions;
    $table = 'competitions';
    $table_id = 'id';
    
    if (isset($_REQUEST['id']) && is_numeric($_REQUEST['id'])) $id = $_REQUEST['id'];
    else $id = '';
    
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
            
            $sql->bindValue("id", $id);
            $sql->bindValue("name_ar", $_POST['name_ar']);
            $sql->bindValue("name_en", $_POST['name_ar']);
            $sql->bindValue("name_ur", $_POST['name_ar']);
            $sql->bindValue("about_ar", $_POST['about_ar']);
            $sql->bindValue("about_en", $_POST['about_ar']);
            $sql->bindValue("about_ur", $_POST['about_ar']);
            
            if ($sql->execute()) {
                $result = '';
                
                $id = $db->lastInsertId();
                
                // add questions
                $db->exec('DELETE FROM competition_questions_choices WHERE question_id IN (SELECT id FROM competition_questions WHERE competition_id = ' . $id .')');
                $db->exec('DELETE FROM competition_questions WHERE competition_id = ' . $id);
                if (!empty($_POST['question'])) {
                    foreach ($_POST['question'] as $question) {
                        $QSql = $db->prepare("REPLACE INTO competition_questions VALUES (
                           :id,
                           :question_ar,
                           :question_en,
                           :question_ur,
                           :competition_id
                        )");
        
                        $QSql->bindValue("id", $question['id']);
                        $QSql->bindValue("question_ar", $question['question_ar']);
                        $QSql->bindValue("question_en", $question['question_ar']);
                        $QSql->bindValue("question_ur", $question['question_ar']);
                        $QSql->bindValue("competition_id", $id);
                        if ($QSql->execute()) {
                            $QId = $db->lastInsertId();
    
                            if (!empty($question['choice'])) {
                                $correct = $question['choice']['correct'];
                
                                unset($question['choice']['correct']);
                                foreach ($question['choice'] as $key => $choice) {
                                    $Csql = $db->prepare("REPLACE INTO competition_questions_choices VALUES (
                                                   :id,
                                                   :choice_ar,
                                                   :choice_en,
                                                   :choice_ur,
                                                   :question_id,
                                                   :correct
                                   )");
                    
                                    $Csql->bindValue("id", $choice['id'] ?? '');
                                    $Csql->bindValue("choice_ar", $choice['choice_ar']);
                                    $Csql->bindValue("choice_en", $choice['choice_ar']);
                                    $Csql->bindValue("choice_ur", $choice['choice_ar']);
                                    $Csql->bindValue("question_id", $QId);
                                    $Csql->bindValue("correct", $key == $correct ? 1 : 0);
                    
                    
                                    $Csql->execute();
                                }
                            }
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
        $questions = $db->query('SELECT * FROM competition_questions WHERE competition_id = '.$id);

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

                                <div class="form-group col-sm-3">
                                    <label><?= LBL_TitleAr ?></label>
                                    <textarea name="name_ar" required
                                              class="form-control"><?= $_POST['name_ar'] ?? $row['name_ar'] ?? '' ?></textarea>
                                </div>

                                <div class="form-group col-sm-9">
                                    <label><?= LBL_about ?> AR</label>
                                    <textarea name="about_ar" class="form-control"
                                              required><?= $_POST['about_ar'] ?? $row['about_ar'] ?? '' ?></textarea>
                                </div>
                            </div>

                            <label><?= HM_question ?></label>
                            <div class="questions margin-bottom">
                                
                                <div class="col-12 margin-bottom">
                                    <div class="w-100 add-question flex-center btn-blue">
                                        <i class="fa fa-plus fa-3x flex-center"></i>
                                    </div>
                                </div>
                                
                                <?php
                                
                                    while ($q = $questions->fetch(PDO::FETCH_OBJ)){
                                        $choices = $db->query('SELECT * FROM competition_questions_choices WHERE question_id = '.$q->id);
                                        ?>
                                        <div class="row question margin-bottom">
                                            <div class="form-group col-sm-12">
                                                <label><?=LBL_question?> <small><a onclick="this.parentElement.parentElement.parentElement.parentElement.remove()"><?= LBL_Delete ?></a></small></label>
                                                <input type="hidden" class="form-control" name="question[<?= $q->id ?>][id]" value="<?= $q->id ?>" required />
                                                <input type="text" class="form-control" name="question[<?= $q->id ?>][question_ar]" value="<?= $q->question_ar ?>" required />
                                            </div>

                                            <div id="choices_<?= $q->id ?>" style="padding: 10px 50px">
                                                <label><?=LBL_choice?></label>

                                                <div class="form-group col-sm-12 ">
                                                    <div class="w-100 flex-center btn-red margin-bottom" onclick="addChoice(<?= $q->id ?>)">
                                                        <i class="fa fa-plus fa-3x flex-center"></i>
                                                    </div>
                                                </div>
    
                                                <?php
                                                    while ($c = $choices->fetch(PDO::FETCH_OBJ))
                                                    {
                                                        ?>
                                                        <div class="margin-bottom choice">
                                                            <input type="hidden" class="form-control" name="question[<?= $q->id ?>][choice][<?= $c->id ?>][id]" value="<?= $c->id ?>" required />
                                                            <input type="text" class="col-sm-8 form-control" name="question[<?= $q->id ?>][choice][<?= $c->id ?>][choice_ar]" value="<?= $c->choice_ar ?>" required />
                                                            <input type="radio" class="col-sm-2" name="question[<?= $q->id ?>][choice][correct]" value="<?= $c->id ?>" <?= ($c->correct==1)?'checked=checked':'' ?> required />
                                                            <div class="col-sm-2" style="cursor: pointer" onclick="this.parentElement.remove()">
                                                                <i class="fa fa-trash text-danger fa-2x"></i>
                                                            </div>
                                                        </div>
                                                        <?php
                                                    }
                                                ?>
                                            </div>

                                        </div>
                                        <?php
                                    }
                                ?>
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

<script>
    $(function () {
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

        var q = 0;
        
        $(".add-question").click(function () {
            $(".questions").append(`
                <div class="row question margin-bottom">
                    <div class="form-group col-sm-12">
                        <label><?=LBL_question?> <small><a onclick="this.parentElement.parentElement.parentElement.parentElement.remove()"><?= LBL_Delete ?></a></small></label>
                        <input type="hidden" class="form-control" name="question[${q}][id]" value="" />
                        <input type="text" class="form-control" name="question[${q}][question_ar]" required />
                    </div>
                
                    <div id="choices_${q}" style="padding: 10px 50px">
                        <label><?=LBL_choice?></label>
                    
                        <div class="form-group col-sm-12">
                            <div class="w-100 flex-center btn-red" onclick="addChoice(${q})">
                                <i class="fa fa-plus fa-3x flex-center"></i>
                            </div>
                        </div>
                    </div>
                    
                </div>
            `);
            q++;
        });
    });
    
    function addChoice(question_num) {
        let c = document.querySelectorAll('#choices_'+question_num+' .choice').length;
        $('#choices_'+question_num).append(`
            <div class="margin-bottom choice">
                <input type="hidden" class="form-control" name="question[${question_num}][choice][${c}][id]" value="" required />
                <input type="text" class="col-sm-8 form-control" name="question[${question_num}][choice][${c}][choice_ar]" required />
                <input type="radio" class="col-sm-2" name="question[${question_num}][choice][correct]" value="${c}" required />
                <div class="col-sm-2" style="cursor: pointer" onclick="this.parentElement.remove()">
                    <i class="fa fa-trash text-danger fa-2x"></i>
                </div>
            </div>
        `);
    }

    $(document).on('click', '.deletebtn', function () {
        $(this).parent().parent().remove();
    });
</script>
