<?php
    global $db, $url, $lang;
    $sql_more = [];
    
    if (isset($_GET['del']) && is_numeric($_GET['del'])) {
        
        $id = $_GET['del'];
        $sqldel1 = $db->query("DELETE FROM employees WHERE emp_id = $id");
        
    }
    
    if (!empty($_POST))
    {
        if (!empty($_POST['name'])) $sql_more[] = 'emp_name LIKE "%'.$_POST['name'].'%"';
        if (!empty($_POST['job_title'])) $sql_more[] = 'emp_jobtitle LIKE "%'.$_POST['job_title'].'%"';
    }
    $sqlemps = $db->query(
            "SELECT * FROM employees".(($sql_more!==[])?' WHERE '.implode(' AND ', $sql_more):'')
    );

?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?= HM_Employees ?>
            <a href="<?= CP_PATH ?>/system/edit/emp" class="btn btn-success pull-<?= DIR_AFTER ?>"><i
                        class="fa fa-star"></i> <?= BTN_AddNew ?></a>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <!-- left column -->
            <div class="col-md-12">

                <div class="box" style="padding: 0 40px 40px 40px">
                    <h3 class="box-title"><?= LBL_SearchFilter ?></h3>
                    <form method="post">
                        <div class="row">
                            <div class="col-lg-3">
                                <label for="exampleFormControlSelect2"><?= LBL_Name ?></label>
                                <input type="text" class="form-control" name="name" />
                            </div>
                            <div class="col-lg-3">
                                <label for="exampleFormControlSelect2"><?= LBL_JobTitle ?></label>
                                <input type="text" class="form-control" name="job_title" />
                            </div>
                            <div class="col-lg-3" style="margin-top: 25px;">
                                <button class="btn btn-success" name="filter"><?= HM_show ?></button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title"><?= BTN_ImportFromExcel ?></h3>
                    </div><!-- /.box-header -->
                    <div class="box-body">

                        <form method="post" enctype="multipart/form-data">
                            <div class="form-group">
                                <label><?= LBL_File ?></label>
                                <input type="file" class="form-control" name="excelfile" accept=".xls,.xlsx"/>
                            </div>
                            <input type="submit" class="col-md-12 btn btn-success" value="<?= LBL_Upload ?>"/>
                        </form>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
                
                
                <?php
                    if (!empty($_FILES)) {
                        
                        if (isset($_FILES['excelfile']['name'])) {
                            
                            $filenameext = pathinfo($_FILES['excelfile']['name'], PATHINFO_EXTENSION);
                            $newname = 'Import_' . date("Y_m_d_H_i_s") . '.' . $filenameext;
                            move_uploaded_file($_FILES['excelfile']['tmp_name'], 'media/imports/' . $newname);
                            
                            $inputFileName = "media/imports/" . $newname;
                            $objPHPExcel = PHPExcel_IOFactory::load($inputFileName);
                            $sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
                            
                            foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
                                $arrayData[$worksheet->getTitle()] = $worksheet->toArray();
                            }
                            
                            $count = count($sheetData) - 1;
                            
                            foreach ($sheetData as $data) {
                                
                                try {
                                    
                                    $db->beginTransaction();
                                    
                                    if ($cnt > 0) {
                                        
                                        $jobid = trim($data['A']);
                                        $name = trim($data['B']);
                                        $jobtitle = trim($data['C']);
                                        
                                        
                                        $sqlins = $db->prepare("INSERT INTO employees VALUES (
																'',
																:emp_name,
																:emp_jobtitle,
																:emp_jobid,
																'default.png',
																" . time() . ",
																" . time() . "
															)");
                                        
                                        $sqlins->bindValue("emp_name", $name);
                                        $sqlins->bindValue("emp_jobtitle", $jobtitle);
                                        $sqlins->bindValue("emp_jobid", $jobid);
                                        $sqlins->execute();
                                        
                                    }
                                    
                                    $db->commit();
                                    
                                } catch (PDOException $e) {
                                    
                                    $db->rollBack();
                                    echo '<span style="color:red"><b>DATABASE ERROR: ' . $e->getMessage() . $e->getLine() . '</b></span>';
                                    
                                }
                                
                                $cnt++;
                            }
                            
                        }
                        
                    }
                    
                    
                    echo '<div class="box box-info">
															<div class="box-header with-border">
																<h3 class="box-title">' . HM_Employees . '</h3>
															</div>
															<div class="box-body">

															<table class="table datatable-table4"><thead>
											<th>' . LBL_JobID . '</th>
											<th>' . LBL_Name . '</th>
											<th>' . LBL_JobTitle . '</th>
											<th>' . LBL_EmpCard . '</th>
											<th>
											' . LBL_Actions . '
											</th>
											</thead><tbody>';
                    
                    while ($row = $sqlemps->fetch(PDO::FETCH_ASSOC)) {
                        
                        echo '<tr>
												<td>
												' . $row['emp_jobid'] . '
												</td>
												<td>
												' . $row['emp_name'] . '
												</td>
												<td>
												' . $row['emp_jobtitle'] . '
												</td>
												<td>
												<a href="' . CP_PATH . '/system/empcard?id=' . $row['emp_id'] . '" target="_blank">' . LBL_EmpCard . '</a>
												</td>
												<td>

												<a href="' . CP_PATH . '/system/edit/emp?id=' . $row['emp_id'] . '" class="label label-info"><i class="fa fa-edit"></i> ' . LBL_Modify . '</a>
												<a href="' . CP_PATH . '/system/employees?del=' . $row['emp_id'] . '" class="label label-danger" onclick="return confirm(\'' . LBL_DeleteConfirm . '\');"><i class="fa fa-trash"></i> ' . LBL_Delete . '</a>

												</td>
												</tr>';
                    
                    }
                    
                    echo '</tbody></table></div></div>';
                
                
                ?>

            </div><!--/.col (right) -->

        </div>   <!-- /.row -->
    </section><!-- /.content -->

</div>
