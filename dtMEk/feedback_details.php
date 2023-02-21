<?php include 'header.php';
    
    $title = HM_Feedback;
    $table = '';
    $table_id = '';
    $newedit_page = '';
    $settings_fromname = $db->query("SELECT s_value FROM settings WHERE s_id = 2")->fetchColumn();
    $settings_fromemail = $db->query("SELECT s_value FROM settings WHERE s_id = 3")->fetchColumn();
    
    if ($_POST['message']) {
        
        if (is_numeric($_GET['id'])) {
            
            $feedb_id = $_GET['id'];
            $feedbinfo = $db->query("SELECT * FROM feedback WHERE feedb_id = $feedb_id")->fetch(PDO::FETCH_ASSOC);
            
            
            $emailto = $feedbinfo['feedb_email'];
            $subject = $_POST['subject'] ? $_POST['subject'] : "A new contact from " . $settings_fromname;
            $body = nl2br($_POST['message']);
            
            $headers = "From: " . $settings_fromname . " <$settings_fromemail>\r\n" .
                "Reply-To: Kottouf <$settings_fromemail>\r\n" .
                'Content-Type: text/html; charset=UTF-8' . "\r\n" .
                'X-Mailer: PHP/' . phpversion();
            
            if (mail($emailto, $subject, $body, $headers)) {
                
                $msg = '<div class="alert alert-success alert-dismissable"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>' . LBL_EmailSent . '</div>';
                
                $stmt = $db->prepare("UPDATE  feedback SET status=?  WHERE feedb_id=?");
                $stmt->execute(array(1, $feedb_id));
                
            } else {
                
                $msg = '<div class="alert alert-danger alert-dismissable"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button><h4><i class="icon fa fa-check"></i>' . LBL_Error . '</h4>' . LBL_ErrorEmailSent . '</div>';
                
                
            }
            
        }
        
        
    }
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
                    <div class="box-body">
                        
                        <?
                            if (is_numeric($_GET['id'])) {
                                
                                $feedb_id = $_GET['id'];
                                
                                $feedbinfo = $db->query("SELECT * FROM feedback WHERE feedb_id = $feedb_id")->fetch(PDO::FETCH_ASSOC);
                                
                                echo LBL_Name . '<br /><b>' . $feedbinfo['feedb_name'] . '</b><br /><br />';
                                echo LBL_Phone . '<br /><b>' . $feedbinfo['feedb_phone'] . '</b><br /><br />';
                                echo LBL_Email . '<br /><b>' . $feedbinfo['feedb_email'] . '</b><br /><br />';
                                echo LBL_Message . '<br /><b>' . nl2br($feedbinfo['feedb_message']) . '</b><br /><br />';
                                
                                echo '<hr />';
                                
                                echo '<h4>' . LBL_Reply . '</h4>';
                                echo '<form method="post">';
                                echo '<div class="form-group">';
                                echo '<label>' . LBL_Subject . '</label><input type="text" name="subject" class="form-control" />';
                                echo '</div>';
                                
                                
                                echo '<div class="form-group">';
                                echo '<textarea name="message" id="message" class="form-control" placeholder="' . LBL_YourReply . '" rows="10"></textarea>';
                                echo '</div>';
                                
                                echo '<div class="form-group">';
                                echo '<input type="submit" class="btn btn-success" value="' . LBL_SendReply . '" />';
                                echo '</div>';
                                echo '</form>';
                            }
                        ?>


                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div><!--/.col (right) -->
        </div>   <!-- /.row -->
    </section><!-- /.content -->
</div>

<?php include 'footer.php'; ?>
