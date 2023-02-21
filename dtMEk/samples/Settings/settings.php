<? include 'header.php';

if ($_POST) {

	$sqlupd1 = $db->query("UPDATE settings SET s_value = '".$_POST['maxkm']."' WHERE s_id = 1");
	$sqlupd2 = $db->query("UPDATE settings SET s_value = '".$_POST['nlname']."' WHERE s_id = 2");
	$sqlupd3 = $db->query("UPDATE settings SET s_value = '".$_POST['nlemail']."' WHERE s_id = 3");
	$sqlupd4 = $db->query("UPDATE settings SET s_value = '".$_POST['kmsranges']."' WHERE s_id = 4");

	$label = 'Updated';

		$msg = '<div class="alert alert-success alert-dismissable"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button><h4><i class="icon fa fa-check"></i>'.$label.'!</h4>Settings '.$label.' successfully<br />'.$result.'</div>';

		unset($_POST);

}

$maxkm = $db->query("SELECT s_value FROM settings WHERE s_id = 1")->fetchColumn();
$nlname = $db->query("SELECT s_value FROM settings WHERE s_id = 2")->fetchColumn();
$nlemail = $db->query("SELECT s_value FROM settings WHERE s_id = 3")->fetchColumn();
$kmsranges = $db->query("SELECT s_value FROM settings WHERE s_id = 4")->fetchColumn();
?>
      <div class="content-wrapper">
        <!-- Main content -->
        <section class="content">
          <div class="row">
            <!-- left column -->
            <div class="col-md-12">
				<? echo $msg; ?>

              <!-- Input addon -->
              <div class="box box-info">
                <div class="box-header with-border">
                  <h3 class="box-title">Settings</h3>
                </div>
                <div class="box-body">
					<form role="form" method="post" enctype="multipart/form-data">



				  <div class="form-group">
                      <label>Max KMs</label>
                      <input type="text" class="form-control" name="maxkm" value="<?=$maxkm;?>" />
                  </div>

                  <div class="form-group">
                      <label>Newsletter From "Name"</label>
                      <input type="text" class="form-control" name="nlname" value="<?=$nlname;?>" />
                  </div>

                  <div class="form-group">
                      <label>Newsletter From "Email"</label>
                      <input type="text" class="form-control" name="nlemail" value="<?=$nlemail;?>" />
                  </div>


                </div><!-- /.box-body -->
				</div><!-- /.box -->



				<div class="box box-info">
                <div class="box-header with-border">
                  <h3 class="box-title">Search KM Range</h3>
                </div>
                <div class="box-body">

				  <div class="form-group">
                      <label>Ranges (Comma Separated)</label>
                      <input type="text" class="form-control" name="kmsranges" value="<?=$kmsranges;?>" />
                  </div>


                </div><!-- /.box-body -->
				</div><!-- /.box -->



            </div><!--/.col (left) -->
            <div class="col-md-12">

				<input type="submit" class="col-md-12 btn btn-success" value="Update" />
				</form>
            </div>
          </div>   <!-- /.row -->
        </section><!-- /.content -->

      </div>

<? include 'footer.php'; ?>
<script>
	$('select').select2();
</script>