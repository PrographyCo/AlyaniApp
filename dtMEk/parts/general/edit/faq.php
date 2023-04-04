<?php
    global $db, $url, $lang, $session;
    
    $edit = false;
    $title = HM_FAQ;
    $table = 'faqs';
    $table_id = 'faq_id';
    
    if (isset($_REQUEST['id']) && is_numeric($_REQUEST['id'])) $id = $_REQUEST['id'];
    else $id = '';
    
    if (!empty($_POST)) {
        
        try {
            $sql = $db->prepare("REPLACE INTO $table VALUES (
                           :id,
                           :faq_title_ar,
                           :faq_title_en,
                           :faq_title_ur,
                           :faq_answer_ar,
                           :faq_answer_en,
                           :faq_answer_ur,
                           :faq_order,
                           :faq_active,
                           :faq_dateadded,
                           :faq_lastupdated
                       )");

            $sql->bindValue("id", $id);
            $sql->bindValue("faq_title_ar", $_POST['faq_title_ar']);
            $sql->bindValue("faq_title_en", $_POST['faq_title_en']);
            $sql->bindValue("faq_title_ur", $_POST['faq_title_ur']);
            $sql->bindValue("faq_answer_ar", $_POST['faq_answer_ar']);
            $sql->bindValue("faq_answer_en", $_POST['faq_answer_en']);
            $sql->bindValue("faq_answer_ur", $_POST['faq_answer_ur']);
            $sql->bindValue("faq_order", $_POST['faq_order']);
            $sql->bindValue("faq_active", (isset($_POST['faq_active']) ? 1 : 0));
            $sql->bindValue("faq_dateadded", ($_POST['faq_dateadded'] ?? time()));
            $sql->bindValue("faq_lastupdated", time());
            
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
                                    <input type="text" class="form-control" name="faq_title_ar" required="required"
                                           value="<?= $_POST['faq_title_ar'] ?? $row['faq_title_ar'] ?? '' ?>"/>
                                </div>

                                <div class="form-group col-sm-4">
                                    <label><?= LBL_TitleEn ?></label>
                                    <input type="text" class="form-control" name="faq_title_en" required="required"
                                           value="<?= $_POST['faq_title_en'] ?? $row['faq_title_en'] ?? '' ?>"/>
                                </div>

                                <div class="form-group col-sm-4">
                                    <label><?= LBL_TitleUr ?></label>
                                    <input type="text" class="form-control" name="faq_title_ur" required="required"
                                           value="<?= $_POST['faq_title_ur'] ?? $row['faq_title_ur'] ?? '' ?>"/>
                                </div>

                            </div>

                            <div class="row">

                                <div class="form-group col-sm-4">
                                    <label><?= LBL_AnswerAr ?></label>
                                    <textarea name="faq_answer_ar"
                                              class="form-control"><?= $_POST['faq_answer_ar'] ?? $row['faq_answer_ar'] ?? '' ?></textarea>
                                </div>

                                <div class="form-group col-sm-4">
                                    <label><?= LBL_AnswerEn ?></label>
                                    <textarea name="faq_answer_en"
                                              class="form-control"><?= $_POST['faq_answer_en'] ?? $row['faq_answer_en'] ?? '' ?></textarea>
                                </div>

                                <div class="form-group col-sm-4">
                                    <label><?= LBL_AnswerUr ?></label>
                                    <textarea name="faq_answer_ur"
                                              class="form-control"><?= $_POST['faq_answer_ur'] ?? $row['faq_answer_ur'] ?? '' ?></textarea>
                                </div>

                            </div>

                            <div class="form-group">
                                <label><?= LBL_Order ?></label>
                                <input type="text" class="form-control" name="faq_order"
                                       value="<?= $_POST['faq_order'] ?? $row['faq_order'] ?? '' ?>"/>
                            </div>

                            <div class="form-group">
                                <label><input type="checkbox"
                                              name="faq_active" <?php if (!isset($row) || $row['faq_active'] == 1) echo 'checked="checked"'; ?> /> <?= LBL_Active ?>
                                </label>
                            </div>

                            <input type="submit" class="col-md-12 btn btn-success"
                                   value="<?= $edit ? LBL_Update : LBL_Add ?>"/>
                            <input type="hidden" name="id" id="id" value="<?= $id ?>"/>
                            <input type="hidden" name="faq_dateadded" id="faq_dateadded"
                                   value="<?= $row['faq_dateadded'] ?? time() ?>"/>
                        </form>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div><!--/.col (left) -->
        </div>   <!-- /.row -->
    </section><!-- /.content -->

</div>

<script>
    $('select').select2();
</script>
