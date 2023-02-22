<?php include '../../layout/header.php'; ?>
<!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Push Notifications History
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

                  <h3 class="box-title">Current Messages Sent</h3>
                  <a href="push_new.php" class="btn btn-primary pull-right"><i class="fa fa-star"></i> Send New Message</a>
                </div><!-- /.box-header -->
                <div class="box-body">
                  <table class="datatable-table4 table table-bordered table-striped">
                    <thead>
                      <tr>
                        <th>Body</th>
                        <th>Platforms</th>
                        <th>Year</th>
                        <th>Devices</th>
                        <th>Date</th>

                      </tr>
                    </thead>
                    <tbody>
	                    <?
		                    $sql = $db->query("SELECT * FROM pushmsgs ORDER BY msg_date DESC");
		                    while ($row = $sql->fetch()) {

			                    echo '<tr>
                        <td>'.$row['msg_body'].'</td>';
                        echo '<td>';
                        if ($row['msg_platform'] == 0) echo 'Both';
                        elseif ($row['msg_platform'] == 1) echo 'iOS';
                        elseif ($row['msg_platform'] == 2) echo 'Android';
                        echo '</td>
                        <td>'.$row['msg_year'].'</td>
                        <td>'.$row['msg_count'].'</td>
                        <td>'.date("j F Y g:i:s a", $row['msg_date']).'</td>';

                        echo '
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

<?php include '../../layout/footer.php'; ?>
