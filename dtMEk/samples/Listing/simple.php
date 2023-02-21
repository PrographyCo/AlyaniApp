<? include 'header.php';
	
$title = 'Sample';
$table = 'sample';
$table_id = 'sample_id';
$newedit_page = 'e_sample.php';

	if (is_numeric($_GET['del'])) {

		$id = $_GET['del'];

		$sqldel1 = $db->query("DELETE FROM $table WHERE $table_id = $id");

	}

?>
<!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            <?=$title;?> Listing
            <small>List</small>
            <a href="<?=$newedit_page;?>" class="btn btn-success pull-right"><i class="fa fa-star"></i> Add New</a>
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

                  <h3 class="box-title">All Listing</h3>
                </div><!-- /.box-header -->
                <div class="box-body">
                  <table class="datatable-table4 table table-bordered table-striped">
                    <thead>
                      <tr>
                        <th>Title</th>
                        <th>Title2</th>
                        <th>Title3</th>
                        <th>Title4</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody>
	                    <?
		                    $sql = $db->query("SELECT * FROM $table");
		                    while ($row = $sql->fetch()) {

			                    echo '<tr>';
			                    echo '<td>';
								echo $row['title'];
			                    echo '</td>';
			                    echo '<td>';
								echo $row['title2'];
			                    echo '</td>';
			                    echo '<td>';
								echo $row['title3'];
			                    echo '</td>';
			                    echo '<td>';
								echo $row['title4'];
			                    echo '</td>';


		                        echo '<td>

		                        <a href="'.$newedit_page.'?id='.$row[$table_id].'" class="label label-info"><i class="fa fa-edit"></i> Modify</a>
								<a href="'.basename($_SERVER['PHP_SELF']).'?del='.$row[$table_id].'" class="label label-danger" onclick="return confirm(\'Are you sure you want to delete this item?\');"><i class="fa fa-trash"></i> Delete</a>

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