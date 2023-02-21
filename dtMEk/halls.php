<? include 'header.php';

$title = HM_Halls;
$table = 'suites_halls';
$table_id = 'hall_id';
$newedit_page = 'e_hall.php';

if ($_GET['suite_id'] > 0) $newedit_page .= '?suite_id='.$_GET['suite_id'];

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
            <?=$title;?>
            <a href="<?=$newedit_page;?>" class="btn btn-success pull-<?=DIR_AFTER;?>"><i class="fa fa-star"></i> <?=BTN_AddNew;?></a>
          </h1>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <!-- left column -->
            <div class="col-md-12">
						<? echo $msg; ?>

							<div class="box">
                <div class="box-body">
                  <table class="datatable-table4 table table-bordered table-striped">
                    <thead>
                      <tr>
                        <th><?=LBL_Title;?></th>
												<th><?=HM_Suite;?></th>
												<th><?=LBL_Gender;?></th>
												<th><?=LBL_Capacity;?></th>
												<th><?=LBL_ٍRemaining;?></th>
												<th><?=LBL_Status;?></th>
                        <th><?=LBL_Order;?></th>
                        <th><?=LBL_Actions;?></th>
                      </tr>
                    </thead>
                    <tbody>
	                    <?

												if (is_numeric($_GET['suite_id'])) $sqlmore = " AND h.hall_suite_id = ".$_GET['suite_id'];

		                    $sql = $db->query("SELECT h.*, s.suite_title, s.suite_gender FROM $table h LEFT OUTER JOIN suites s ON h.hall_suite_id = s.suite_id WHERE 1 $sqlmore ORDER BY h.hall_order");
		                    while ($row = $sql->fetch()) {

			                    echo '<tr>';
			                    echo '<td>';
													echo $row['hall_title'];
			                    echo '</td>';
													echo '<td>';
													echo $row['suite_title'];
			                    echo '</td>';

													echo '<td>';
													if ($row['suite_gender'] == 'm') echo LBL_Male;
													elseif ($row['suite_gender'] == 'f') echo LBL_Female;
			                    echo '</td>';

													echo '<td>';
													echo number_format($row['hall_capacity']);
													echo '</td>';

													echo '<td>';
													$occu = $db->query("SELECT COUNT(pil_code) FROM pils_accomo WHERE hall_id = ".$row['hall_id'])->fetchColumn();
													$remaining = $row['hall_capacity'] - $occu;
													echo number_format($remaining);
													echo '</td>';

			                    echo '<td>';
													if ($row['hall_active'] == 1) echo '<span class="label label-success">'.LBL_Active.'</span>';
													else echo '<span class="label label-danger">'.LBL_Inactive.'</span>';
			                    echo '</td>';
			                    echo '<td>';
													echo $row['hall_order'];
			                    echo '</td>';

	                        echo '<td>

	                        <a href="'.$newedit_page.''.($_GET['suite_id'] ? '&' : '?').'id='.$row[$table_id].'" class="label label-info"><i class="fa fa-edit"></i> '.LBL_Modify.'</a>
													<a href="'.basename($_SERVER['PHP_SELF']).'?del='.$row[$table_id].'" class="label label-danger" onclick="return confirm(\''.LBL_DeleteConfirm.'\');"><i class="fa fa-trash"></i> '.LBL_Delete.'</a>

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
