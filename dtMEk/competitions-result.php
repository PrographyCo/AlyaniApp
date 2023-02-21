<? include 'header.php';

$title = HM_competitions_results;
$table = 'competition_result';
$table_id = 'id';
$sql = $db->query("SELECT * FROM $table ORDER BY result DESC");

if(isset($_POST['filter'])){
    $competition_id = $_POST['competition_id'];
  


    if($competition_id != 0){
                        $sql = $db->query("SELECT * FROM $table WHERE competition_id = $competition_id ORDER BY result DESC");
    }else{
                          $sql = $db->query("SELECT * FROM $table ORDER BY result DESC");

    }

													
}

?>
<!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            <?=$title;?>
          </h1>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <!-- left column -->
            <div class="col-md-12">
				<? echo $msg; ?>

			<div class="box">
			    	    
						<form method="post" style="margin: 40px;">
				            <div class="row">
				                   <div class="col-lg-2">
				                       	<label><?=HM_competitions;?></label>
											<select name="competition_id" class="form-control select2">
												<option value="0"><?=LBL_Choose;?></option>
													<option value="0"><?=HM_ListAll;?></option>
												<?
													$competitions = $db->query("SELECT * FROM competitions");
													while ($rowcompetitions = $competitions->fetch(PDO::FETCH_ASSOC)) {?>
                                                            
										                		<option value="<?php echo $rowcompetitions['id'];?>"><?php echo $rowcompetitions['name_'.$_COOKIE['lang']];?></option>

												<?php	}
												?>
											</select>
				                    </div>
				                  
				                     <div class="col-lg-2" style="margin-top: 25px;">
                                           <button class="btn btn-success" name="filter"><?=HM_show;?></button>
				                    </div>
				            </div>
				    </form>
				    
                <div class="box-body">
                  <table class="datatable-table4 table table-bordered table-striped">
                    <thead>
                      <tr>
                        <th>#</th>
                        <th>
							<?=HM_competitions_name;?>
						</th>
						<th>
							<?=HM_competitions_user;?>
						</th>
						<th><?=LBL_total_degree;?></th>
                      </tr>
                    </thead>
                    <tbody>
	                    <?
		                   $i=1;
		                    while ($row = $sql->fetch()) {
		                        $competition_id = $row['competition_id'];
		                        $com = $db->query("SELECT * FROM competitions WHERE id = $competition_id ");
                                $competition_name = $com->fetch();
                                
                                $user_id = $row['user_id'];
		                        $user = $db->query("SELECT * FROM pils WHERE pil_id = $user_id ");
                                $user_name = $user->fetch();
                                
			                    echo '<tr>';
			                    echo '<td>';
												echo $i;
			                    echo '</td>';
			                    echo '<td>';
												echo $competition_name['name_'.$_COOKIE['lang']];
			                    echo '</td>';
			                                        
			                    echo '<td>';
												echo $user_name['pil_name'];
			                    echo '</td>';
			                    echo '<td>';
												 echo $row['result'].' / '. $row['total'];
			                    echo '</td>';


	                            echo ' </tr>';

		                    $i++;}
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
