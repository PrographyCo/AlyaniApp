<? include 'header.php';
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
require 'pushfunctions.php';

if ($_POST) {

	if ($_POST['platform'] == 1 || $_POST['platform'] == 0) $countios = sendpush_ios($_POST['message'], $_POST['year']);
	if ($_POST['platform'] == 2 || $_POST['platform'] == 0) $countandroid = sendpush_android($_POST['message'], $_POST['year']);

	$msg = '<div class="alert alert-success alert-dismissable"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button><h4><i class="icon fa fa-check"></i>Message Sent!</h4>iOS Devices: '.$countios.', Android Devices: '.$countandroid.'</div>';

	$totaldevices = $countios + $countandroid;

	$sql1 = $db->query("INSERT INTO pushmsgs VALUES ('', ".time().", '".$_POST['platform']."', '".$_POST['year']."', '".$_POST['message']."', '".$totaldevices."')");
	$sql2 = $db->query("INSERT INTO notifications VALUES ('', ".time().", '".$_POST['year']."', '".$_POST['message']."')");

}

?>
<!-- Content Wrapper. Contains page content -->

      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            New Push Notification
          </h1>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <!-- left column -->
            <div class="col-md-12">

				<? echo $msg; ?>

              <!-- Input addon -->
              <div class="box box-info">
                <div class="box-header with-border">
                  <h3 class="box-title">Notification Information</h3>
                </div>
                <div class="box-body">
					<form role="form" method="post" enctype="multipart/form-data">


				  <div class="form-group">
                      <label>Platforms</label>
                      <select name="platform" id="platform" class="form-control">
	                      <option value="0" <? if ($_POST['platform'] == 0) echo 'selected="selected"';?>>Both</option>
	                      <option value="1" <? if ($_POST['platform'] == 1) echo 'selected="selected"';?>>iOS</option>
	                      <option value="2" <? if ($_POST['platform'] == 2) echo 'selected="selected"';?>>Android</option>
                      </select>
                  </div>


                  <div class="form-group">
                      <label>Message</label>
                      <textarea name="message" id="message" class="form-control"><?=$_POST['message'];?></textarea>
                  </div>

                </div><!-- /.box-body -->
              </div><!-- /.box -->





            </div><!--/.col (left) -->
            <div class="col-md-12">

				<input type="submit" class="col-md-12 btn btn-success" value="Send Message" />
				</form>
            </div>
          </div>   <!-- /.row -->
        </section><!-- /.content -->

      </div>

<? include 'footer.php'; ?>