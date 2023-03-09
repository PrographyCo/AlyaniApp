<?php include 'layout/header.php';
    //error_reporting(E_ALL);
    //ini_set('display_errors', 1);
    if ($_POST) {
        
        $chk = $db->query("SELECT user_id FROM _users WHERE email = '" . $_POST['email'] . "' AND user_id != '" . $_POST['user_id'] . "'")->fetchColumn();
        if ($chk > 0) {
            
            $msg = '<div class="alert alert-danger alert-dismissable"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button><h4><i class="icon fa fa-check"></i>Error</h4>' . LBL_EmailAlreadyExists . '</div>';
            
        } else {
            
            $sql = $db->prepare("REPLACE INTO _users VALUES (
		:user_id,
		:email,
		:password,
		:name,
		:phone,
		1,
		:role,
		:timestamp
		)");
            
            if ($_POST['user_id'] > 0) $user_id = $_POST['user_id'];
            else $user_id = '';
            
            if ($_POST['password']) $newpass = password_hash($_POST['password'], PASSWORD_DEFAULT);
            else $newpass = $_POST['oldpass'];
            
            
            $sql->bindValue("user_id", $user_id);
            $sql->bindValue("email", $_POST['email']);
            $sql->bindValue("password", $newpass);
            $sql->bindValue("name", $_POST['name']);
            $sql->bindValue("phone", $_POST['phone']);
            $sql->bindValue("role", $_POST['role']);
            $sql->bindValue("timestamp", time());
            
            if ($sql->execute()) {
                
                $user_id = $db->lastInsertId();
                if ($_POST['role'] == 8) {
                    
                    // Delete old services
                    $sqldel = $db->exec("DELETE FROM _users_perms WHERE user_id = $user_id");
                    
                    // Put permissions
                    
                    $perms_array = $_POST['perms'];
                    $perm = '';
                    $sqlins = $db->prepare("INSERT INTO _users_perms VALUES(:user_id, :perm)");
                    $sqlins->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                    $sqlins->bindParam(':perm', $perm, PDO::PARAM_INT);
                    foreach ($perms_array as $perm) {
                        $sqlins->execute();
                    }
                    
                    
                }
                
                $label = $_GET['id'] ? LBL_Update : LBL_Added;
                
                $msg = '<div class="alert alert-success alert-dismissable"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button><h4><i class="icon fa fa-check"></i>' . $label . '!</h4>' . LBL_User . ' ' . $label . ' ' . LBL_Successfully . ' </div>';
                unset($_POST);
                
            } else {
                
                $msg = '<div class="alert alert-danger alert-dismissable"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button><h4><i class="icon fa fa-check"></i>Error</h4>Error Updating / Inserting Record</div>';
                
            }
            
            
        }
        
    }
    
    if (is_numeric($_GET['id'])) {
        
        $row = $db->query("SELECT * FROM _users WHERE user_id = " . $_GET['id'])->fetch(PDO::FETCH_ASSOC);
        
    }

?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?= HM_SystemUsers; ?>
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
                        <form role="form" method="post">

                            <div class="form-group">
                                <label><?= LBL_Name; ?></label>
                                <input type="text" class="form-control" name="name"
                                       value="<?php echo $_POST['name'] ?: $row['name'] ?>"/>
                            </div>

                            <div class="form-group">
                                <label><?= LBL_Email; ?></label>
                                <input type="email" class="form-control" name="email"
                                       value="<?php echo $_POST['email'] ?: $row['email'] ?>"/>
                            </div>

                            <div class="form-group">
                                <label><?= LBL_Password; ?></label>
                                <input type="password" class="form-control" name="password" autocomplete="off"/>
                            </div>

                            <div class="form-group">
                                <label><?= LBL_Phone; ?></label>
                                <input type="text" class="form-control" name="phone"
                                       value="<?php echo $_POST['phone'] ? $_POST['phone'] : $row['phone'] ?>"/>
                            </div>

                            <div class="form-group">
                                <label><?= LBL_Role; ?></label>
                                <select class="form-control select2" name="role" id="role"
                                        onchange="showperm(this.value);">
                                    <option value="8" <?php if ($row['userlevel'] == 8 || $_POST['role'] == 8) echo 'selected="selected"'; ?>><?= LBL_Moderator; ?></option>
                                    <option value="9" <?php if ($row['userlevel'] == 9 || $_POST['role'] == 9) echo 'selected="selected"'; ?>><?= LBL_Administrator; ?></option>
                                </select>
                            </div>

                            <span id="permdiv" <?php if ($row['userlevel'] == 9) echo 'style="display:none"'; ?>>

                    <div class="form-group">
                        <label><?= LBL_Permissions; ?></label>
                            <select name="perms[]" id="perms[]" class="form-control select2"
                                    onchange="showperm(this.value);" multiple placeholder="<?= LBL_Choose; ?>">

                                <?
                                    // Select current permissions
                                    $list_perms = array();
                                    if ($_GET['id']) {
        
                                        $sql3 = $db->prepare("SELECT permid FROM _users_perms WHERE user_id = :user_id");
                                        $sql3->bindValue("user_id", $_GET['id'], PDO::PARAM_INT);
                                        $sql3->execute();
                                        while ($row3 = $sql3->fetch()) {
            
                                            $list_perms[] = $row3['permid'];
            
                                        }
        
                                    } elseif ($_POST['perms']) {
        
        
                                        $list_perms = $_POST['perms'];
        
        
                                    }
                                ?>
                                <option value="1" <?php if (in_array(1, $list_perms)) echo 'selected="selected"'; ?>><?= HM_Dashboard; ?></option>
                                <option value="2" <?php if (in_array(2, $list_perms)) echo 'selected="selected"'; ?>><?= HM_OurLocations; ?></option>
                                <option value="3" <?php if (in_array(3, $list_perms)) echo 'selected="selected"'; ?>><?= HM_OurCompanies; ?></option>
                                <option value="4" <?php if (in_array(4, $list_perms)) echo 'selected="selected"'; ?>><?= HM_Promos; ?></option>
                                <option value="5" <?php if (in_array(5, $list_perms)) echo 'selected="selected"'; ?>><?= HM_Cities; ?></option>
                                <option value="6" <?php if (in_array(6, $list_perms)) echo 'selected="selected"'; ?>><?= HM_Suites; ?></option>
                                <option value="7" <?php if (in_array(7, $list_perms)) echo 'selected="selected"'; ?>><?= HM_Buildings; ?></option>
                                <option value="8" <?php if (in_array(8, $list_perms)) echo 'selected="selected"'; ?>><?= HM_Tents; ?></option>
                                <option value="9" <?php if (in_array(9, $list_perms)) echo 'selected="selected"'; ?>><?= HM_PilgrimsBuses; ?></option>
                                <option value="10" <?php if (in_array(10, $list_perms)) echo 'selected="selected"'; ?>><?= HM_Managers; ?></option>
																<option value="11" <?php if (in_array(11, $list_perms)) echo 'selected="selected"'; ?>><?= HM_Supervisors; ?></option>
																<option value="12" <?php if (in_array(12, $list_perms)) echo 'selected="selected"'; ?>><?= HM_Muftis; ?></option>
																<option value="13" <?php if (in_array(13, $list_perms)) echo 'selected="selected"'; ?>><?= HM_Pilgrims; ?></option>
																<option value="14" <?php if (in_array(14, $list_perms)) echo 'selected="selected"'; ?>><?= HM_SuitesAccomodations; ?></option>
																<option value="15" <?php if (in_array(15, $list_perms)) echo 'selected="selected"'; ?>><?= HM_BuildingsAccomodations; ?></option>
																<option value="16" <?php if (in_array(16, $list_perms)) echo 'selected="selected"'; ?>><?= HM_TentsAccomodations; ?></option>
																<option value="17" <?php if (in_array(17, $list_perms)) echo 'selected="selected"'; ?>><?= HM_BusesAccomodations; ?></option>
																<option value="18" <?php if (in_array(18, $list_perms)) echo 'selected="selected"'; ?>><?= HM_SendNewPushNotification; ?></option>
																<option value="19" <?php if (in_array(19, $list_perms)) echo 'selected="selected"'; ?>><?= HM_NotificationsHistory; ?></option>
																<option value="20" <?php if (in_array(20, $list_perms)) echo 'selected="selected"'; ?>><?= HM_News; ?></option>
																<option value="21" <?php if (in_array(21, $list_perms)) echo 'selected="selected"'; ?>><?= HM_Aboutus; ?></option>
																<option value="22" <?php if (in_array(22, $list_perms)) echo 'selected="selected"'; ?>><?= HM_ContactInfo; ?></option>
																<option value="23" <?php if (in_array(23, $list_perms)) echo 'selected="selected"'; ?>><?= HM_Social; ?></option>
																<option value="24" <?php if (in_array(24, $list_perms)) echo 'selected="selected"'; ?>><?= HM_FAQ; ?></option>
																<option value="25" <?php if (in_array(25, $list_perms)) echo 'selected="selected"'; ?>><?= HM_HajAlbum; ?></option>
																<option value="26" <?php if (in_array(26, $list_perms)) echo 'selected="selected"'; ?>><?= HM_HajGuideCats; ?></option>
																<option value="27" <?php if (in_array(27, $list_perms)) echo 'selected="selected"'; ?>><?= HM_HajGuideArticles; ?></option>
																<option value="28" <?php if (in_array(28, $list_perms)) echo 'selected="selected"'; ?>><?= HM_Feedback; ?></option>
																<option value="29" <?php if (in_array(29, $list_perms)) echo 'selected="selected"'; ?>><?= HM_Settings; ?></option>
																<option value="30" <?php if (in_array(30, $list_perms)) echo 'selected="selected"'; ?>><?= HM_SystemUsers; ?></option>
																<option value="31" <?php if (in_array(31, $list_perms)) echo 'selected="selected"'; ?>><?= HM_GENERALGUIDE; ?></option>
																<option value="32" <?php if (in_array(32, $list_perms)) echo 'selected="selected"'; ?>><?= HM_AutoAccomo; ?></option>
																<option value="33" <?php if (in_array(33, $list_perms)) echo 'selected="selected"'; ?>><?= HM_Employees; ?></option>
																<option value="34" <?php if (in_array(34, $list_perms)) echo 'selected="selected"'; ?>><?= HM_Employees_Designs; ?></option>
																<option value="35" <?php if (in_array(35, $list_perms)) echo 'selected="selected"'; ?>><?= HM_UpdatePilsPhotos; ?></option>
																<option value="36" <?php if (in_array(36, $list_perms)) echo 'selected="selected"'; ?>><?= HM_UpdateEmpsPhotos; ?></option>
																<option value="37" <?php if (in_array(37, $list_perms)) echo 'selected="selected"'; ?>><?= HM_bulkaccomosms; ?></option>

                            </select>
		                    </div>
									</span>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->

            </div><!--/.col (left) -->
            <div class="col-md-12">
                <input type="hidden" name="user_id" id="user_id" value="<?= $row['user_id']; ?>"/>
                <input type="hidden" name="oldpass" id="oldpass" value="<?= $row['password']; ?>"/>
                <input type="submit" class="col-md-12 btn btn-success"
                       value="<?php echo $row['user_id'] ? LBL_Update : LBL_Add; ?>"/>
                </form>
            </div>
        </div>   <!-- /.row -->
    </section><!-- /.content -->

</div>

<?php include 'layout/footer.php'; ?>
<script>
    function showperm(userlevel) {

        if (userlevel == 9) {

            $('#permdiv').fadeOut();
            $('select').select2();
        } else {

            $('#permdiv').fadeIn();
            $('select').select2();
        }

    }
</script>
