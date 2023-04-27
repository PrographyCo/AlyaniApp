<?php
    global $db, $session, $lang, $url;
    
    $edit = false;
    $title = HM_STAFF;
    $table = 'staff';
    $table_id = 'staff_id';
    $error = false;
    
    if (isset($_REQUEST['id']) && is_numeric($_REQUEST['id'])) $id = $_REQUEST['id'];
    else $id = '';
    
    if (isset($_REQUEST['type']) && is_numeric($_REQUEST['type'])) $type = $_REQUEST['type'];
    else $type = '';
    
    if ($_POST) {
        
        if ($_POST['staff_type'] < 3) {
            $chk = $db->prepare("SELECT $table_id FROM $table WHERE staff_username = :username AND $table_id != '$id' LIMIT 1");
            $chk->bindValue("username", $_POST['staff_username']);
            $chk->execute();
            
            if ($chk->rowCount() > 0) {
                $msg = '<div class="alert alert-danger alert-dismissable"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button><h4><i class="icon fa fa-check"></i>' . LBL_Error . '</h4>' . LBL_Username . ' ' . LBL_AlreadyExists . '</div>';
                $error = true;
            }
        }
        
        if (!$error) {
            try {
                if ($_POST['staff_type'] == 3) {
                    $username = '';
                    $password = '';
                } else {
                    $username = $_POST['staff_username'];
                    $password = (isset($_POST['newpassword']) ? password_hash($_POST['newpassword'], PASSWORD_DEFAULT) : $_POST['oldpassword']);
                }
        
                $sql = $db->prepare("REPLACE INTO $table VALUES (
			:id,
			:staff_type,
			:staff_name,
			:staff_email,
			:staff_phones,
			:staff_photo,
			:staff_priv,
			:staff_username,
			:staff_password,
			:staff_active,
			:staff_dateadded,
			:staff_lastupdated
			)");
        
                $sql->bindValue("id", $id);
                $sql->bindValue("staff_type", $_POST['staff_type']);
                $sql->bindValue("staff_name", $_POST['staff_name']);
                $sql->bindValue("staff_email", $_POST['staff_email']);
                $sql->bindValue("staff_phones", $_POST['staff_phones']);
                $sql->bindValue("staff_photo", $_POST['staff_photo']);
                $sql->bindValue("staff_priv", $_POST['staff_priv']);
                $sql->bindValue("staff_username", $username);
                $sql->bindValue("staff_password", $password);
                $sql->bindValue("staff_active", (isset($_POST['staff_active']) ? 1 : 0));
                $sql->bindValue("staff_dateadded", ($_POST['staff_dateadded'] ?? time()));
                $sql->bindValue("staff_lastupdated", time());
        
                if ($sql->execute()) {
                    $result = '';
                    $id = $db->lastInsertId();

                    if ($_FILES['staff_uphoto']['tmp_name']) {
                        $ext = strtolower(pathinfo($_FILES['staff_uphoto']['name'], PATHINFO_EXTENSION));
                        if (copy($_FILES['staff_uphoto']['tmp_name'], 'assets/media/staff/' . $id . '.' . $ext)) {
                    
                            $sql2 = $db->query("UPDATE $table SET staff_photo = '" . $id . "." . $ext . "' WHERE $table_id = $id");
                            $result .= LBL_PhotoUploaded . '<br />';
                    
                        }
                    } else if (!is_numeric($_REQUEST['id'])) {
                
                        $sql2 = $db->query("UPDATE $table SET staff_photo = 'default_photo.png' WHERE $table_id = $id");
                        $result .= LBL_DefaultPhotoUploaded . '<br />';
                    }
            
                    if ($_REQUEST['id'] > 0) $label = LBL_Updated;
                    else $label = LBL_Added;
                    
                    if (!empty($_POST['newpassword'])) {
                        $message = 'تم تغيير كلمة المرور ل: ' . ($_POST['newpassword']);
                        sendSMSStaffGeneral($id,$message);
                    }
            
                    $msg = '<div class="alert alert-success alert-dismissable"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button><h4><i class="icon fa fa-check"></i>' . $label . '!</h4>' . LBL_Item . ' ' . $label . ' ' . LBL_Successfully . '<br />' . $result . '</div>';
    
                    $_POST = [];
            
                } else {
            
                    $msg = '<div class="alert alert-danger alert-dismissable"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button><h4><i class="icon fa fa-check"></i>' . LBL_Error . '</h4>' . LBL_ErrorUpdateAdd . '</div>';
            
                }
        
            } catch (PDOException $e) {
        
                $msg = '<div class="alert alert-danger alert-dismissable"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button><h4><i class="icon fa fa-check"></i>' . LBL_Error . '</h4>' . LBL_ErrorDB . ' ' . $e->getMessage() . '</div>';
        
            }
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


                            <div class="form-group">
                                <label><?= LBL_Type ?></label>
                                <select name="staff_type" class="form-control select2"
                                        onchange="typechanged(this.value);">
                                    <option value="1" <?php if ((isset($row) && $row['staff_type'] == 1) || $type == 1) echo 'selected="selected"'; ?>><?= HM_Managers ?></option>
                                    <option value="2" <?php if ((isset($row) && $row['staff_type'] == 2) || $type == 2) echo 'selected="selected"'; ?>><?= HM_Supervisors ?></option>
                                    <option value="3" <?php if ((isset($row) && $row['staff_type'] == 3) || $type == 3) echo 'selected="selected"'; ?>><?= HM_Muftis ?></option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label><?= LBL_Name ?></label>
                                <input type="text" class="form-control" name="staff_name" required="required"
                                       value="<?= $_POST['staff_name'] ?? $row['staff_name'] ?? '' ?>"/>
                            </div>

                            <div class="form-group">
                                <label><?= LBL_Email ?></label>
                                <input type="email" class="form-control" name="staff_email"
                                       value="<?= $_POST['staff_email'] ?? $row['staff_email'] ?? '' ?>"/>
                            </div>

                            <div class="form-group">
                                <label><?= LBL_ContactNumbers ?></label>
                                <input type="text" class="form-control" name="staff_phones"
                                       value="<?= $_POST['staff_phones'] ?? $row['staff_phones'] ?? '' ?>"/>
                            </div>

                            <div class="form-group">
                                <label><?= LBL_Photo ?></label><br/>
                                <?php if (isset($row) && $row['staff_photo']) echo '<img src="'.ASSETS_PATH.'media/staff/' . $row['staff_photo'] . '" width="150"/>'; ?>
                                <input id="staff_uphoto" name="staff_uphoto" type="file" class="file">
                            </div>

                            <div id="privaccess" <?php if ((isset($row) && ($row['staff_type'] == 1 || $row['staff_type'] == 3)) || $type == 1 || $type == 3) echo 'style="display: none"'; ?>>
                                <div class="form-group">
                                    <label><?= LBL_Permissions ?></label>
                                    <select name="staff_priv" class="form-control select2">
                                        <option value="1" <?php if ((isset($row) && $row['staff_priv'] == 1) || $type == 1) echo 'selected="selected"'; ?>><?= LBL_Locations ?></option>
                                        <option value="2" <?php if ((isset($row) && $row['staff_priv'] == 2) || $type == 2) echo 'selected="selected"'; ?>><?= LBL_Buses ?></option>
                                    </select>
                                </div>
                            </div>

                            <div id="appaccess" <?php if ((isset($row) && $row['staff_type'] == 3) || $type == 3) echo 'style="display: none"'; ?>>
                                <div class="form-group">
                                    <label><?= LBL_Username ?></label>
                                    <input type="text" class="form-control" name="staff_username"
                                           value="<?= $_POST['staff_username'] ?? $row['staff_username'] ?? '' ?>"/>
                                </div>

                                <div class="form-group">
                                    <label><?= !isset($row) ? LBL_Password : LBL_ApplyNewPassword ?> -
                                        <a href="#" onclick="generateRand(); return false;"><?= LBL_GenerateRandom ?></a> -
                                        <a href="#" onclick="togglepasstype(); return false;"><?= LBL_ViewHide ?></a>
                                    </label>
                                    <input type="password" class="form-control" name="newpassword" id="newpassword"
                                           value="" autocomplete="off"/>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>
                                    <input type="checkbox" name="staff_active" <?php if (!isset($row) || $row['staff_active'] == 1) echo 'checked="checked"'; ?> /> <?= LBL_Active ?>
                                </label>
                            </div>

                            <input type="submit" class="col-md-12 btn btn-success"
                                   value="<?= $edit ? LBL_Update : LBL_Add ?>"/>
                            <input type="hidden" name="id" id="id" value="<?= $id ?>"/>
                            <input type="hidden" name="oldpassword" id="oldpassword"
                                   value="<?= $row['staff_password']??'' ?>"/>
                            <input type="hidden" name="staff_photo" id="staff_photo"
                                   value="<?= $row['staff_photo']??'' ?>"/>
                            <input type="hidden" name="staff_dateadded" id="staff_dateadded"
                                   value="<?= $row['staff_dateadded']??time() ?>"/>
                        </form>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div><!--/.col (left) -->
        </div>   <!-- /.row -->
    </section><!-- /.content -->

</div>

<script>
    $('select').select2();
    $("#staff_uphoto").fileinput({
        showRemove: true,
        showUpload: false,
        showCancel: false,
        showPreview: true,
        minFileCount: 0,
        maxFileCount: 1,
        allowedFileTypes: ['image', 'pdf']
    });

    function typechanged(type_id) {

        if (type_id == 1) {

            $('#appaccess').show();
            $('#privaccess').hide();
            $('select').select2();

        } else if (type_id == 2) {

            $('#appaccess').show();
            $('#privaccess').show();
            $('select').select2();

        } else if (type_id == 3) {

            $('#appaccess').hide();
            $('#privaccess').hide();
            $('select').select2();

        }

    }

    function generateRand() {

        var data = {};

        $.post('<?= CP_PATH ?>/post/generateRand', data, function (response) {
            $('#newpassword').attr("type", 'text');
            $('#newpassword').val(response.trim());
        });

    }

    function togglepasstype() {

        if ($('#newpassword').attr("type") == 'text') $('#newpassword').attr("type", 'password');
        else $('#newpassword').attr("type", 'text');

    }
</script>
