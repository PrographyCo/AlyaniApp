<? include 'header.php';

	if (is_numeric($_GET['del'])) {

		$noti_id = $_GET['del'];
		$db->query("DELETE FROM notifications WHERE noti_id = $noti_id");

	}
?>
<!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Notifications
            <small>List</small>
          </h1>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <!-- left column -->
            <div class="col-md-12">
				<? echo $msg; ?>

				<div class="box">
                <div class="box-header">

                  <h3 class="box-title">Current Notifications</h3>
                </div><!-- /.box-header -->
                <div class="box-body">
                  <table class="datatable-table4 table table-bordered table-striped">
                    <thead>
                      <tr>
                        <th>Body</th>
                        <th>Platform</th>
                        <th>Date</th>
						<th>Actions</th>
                      </tr>
                    </thead>
                    <tbody>
	                    <?
		                    $sql = $db->query("SELECT * FROM notifications ORDER BY noti_date DESC");
		                    while ($row = $sql->fetch()) {

			                    echo '<tr>
                        <td>'.$row['noti_body'].'</td>';

                        echo '
                        <td>'.$row['noti_year'].'</td>
                        <td>'.date("j F Y g:i:s a", $row['noti_date']).'</td>';

                        echo '<td>
                        <a href="notifications.php?del='.$row['noti_id'].'" onclick="return confirm(\'Are you sure you want to delete this notification?\');" class="label label-danger"><i class="fa fa-trash"></i> Delete</a>
                        </td>
                      </tr>';

		                    }
		                    ?>

                    </tbody>
                  </table>
                </div><!-- /.box-body -->
              </div><!-- /.box -->
            </div><!--/.col (right) -->

          </div>   <!-- /.row -->
        </section><!-- /.content -->

      </div>

<? include 'footer.php'; ?>