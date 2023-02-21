<? include 'header.php';

$title = HM_HajGuideArticles;
$table = 'guide_articles';
$table_id = 'ga_id';
$newedit_page = 'e_ga.php';

if ($_GET['parent_id'] > 0) $newedit_page .= '?parent_id='.$_GET['parent_id'];

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
												<th><?=LBL_ParentCategory;?></th>
												<th><?=LBL_Type;?></th>
                        <th><?=LBL_Title;?></th>
                        <th><?=LBL_Status;?></th>
                        <th><?=LBL_Order;?></th>
                        <th><?=LBL_Actions;?></th>
                      </tr>
                    </thead>
                    <tbody>
	                    <?

												if (is_numeric($_GET['parent_id']) && $_GET['parent_id'] > 0) $sqlmore = " AND ga.ga_gcat_id = ".$_GET['parent_id'];
		                    $sql = $db->query("SELECT ga.*, gc.gcat_order, gc.gcat_title_ar, gc.gcat_title_en, gc.gcat_title_ur
													FROM $table ga
													LEFT OUTER JOIN guide_categories gc ON ga.ga_gcat_id = gc.gcat_id
													WHERE 1 $sqlmore ORDER BY gc.gcat_order, ga.ga_order");
		                    while ($row = $sql->fetch()) {

			                    echo '<tr>';
			                    echo '<td>';
													echo $row['gcat_title_'.$lang];
			                    echo '</td>';

													echo '<td>';
													if ($row['ga_type'] == 1) echo '<b>'.LBL_Text.'</b>';
													elseif ($row['ga_type'] == 2) echo '<b>'.LBL_PDFFILE.'</b>';
			                    echo '</td>';

													echo '<td>';
													echo $row['ga_title_'.$lang];
													echo '</td>';

			                    echo '<td>';
													if ($row['ga_active'] == 1) echo '<span class="label label-success">'.LBL_Active.'</span>';
													else echo '<span class="label label-danger">'.LBL_Inactive.'</span>';
			                    echo '</td>';
			                    echo '<td>';
													echo $row['ga_order'];
			                    echo '</td>';

	                        echo '<td>

	                        <a href="'.$newedit_page.''.($_GET['parent_id'] ? '&' : '?').'id='.$row[$table_id].'" class="label label-info"><i class="fa fa-edit"></i> '.LBL_Modify.'</a>
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
