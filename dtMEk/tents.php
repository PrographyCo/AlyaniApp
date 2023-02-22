<?php include 'layout/header.php';
    
    $table = 'tents';
    $table_id = 'tent_id';
    
    if (isset($_GET['type'])) {
        $type = $_GET['type'];
        $sql = $db->query("SELECT * FROM $table  WHERE type = $type ORDER BY tent_order");
        
    }
    
    $title = $type == 1 ? HM_Tents . ' - ' . mozdalifa : HM_Halls . ' - ' . arafa;
    if ($type == 1) {
        $newedit_page = 'e_tent.php';
        
    } elseif ($type == 2) {
        $newedit_page = 'e_tent.php?type=2';
        
    }
    
    
    if (isset($_POST['filter'])) {
        $soothing = $_POST['tent_soothing'];
        $gender = $_POST['tent_gender'];
        $tent_type = $_POST['tent_type'];
        $arr = array();
        array_push($arr, "type = '$type' ");
        if ($tent_type != 0) {
            array_push($arr, "tent_type = " . $tent_type);
        }
        if ($gender != 'All') {
            array_push($arr, "tent_gender = '$gender' ");
        }
        if (!empty($arr)) {
            $option = implode(" AND ", $arr);
            //  echo $option;die;
            $sql = $db->query("SELECT * FROM $table WHERE $option ORDER BY tent_order");
            
            
        } else {
            $sql = $db->query("SELECT * FROM $table  WHERE type = $type  ORDER BY tent_order");
            
        }
        
        // $sql = $db->query("SELECT * FROM $table WHERE tent_gender = '$gender' ORDER BY tent_order");
        
        
    }
    if (is_numeric($_GET['del'])) {
        
        $id = $_GET['del'];
        $type = $_GET['type'];
        $sqldel1 = $db->query("DELETE FROM $table WHERE $table_id = $id");
        
        header("Location:tents.php?type=$type");
        exit();
        
    }

?>

<style>

</style>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?= $title; ?>
            <a href="<?= $newedit_page; ?>" class="btn btn-success pull-<?= DIR_AFTER; ?>"><i
                        class="fa fa-star"></i> <?= BTN_AddNew; ?></a>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <!-- left column -->
            <div class="col-md-12">
                <?php echo $msg; ?>

                <div class="box">
                    <form method="post" style="margin: 40px;">
                        <div class="row">
                            <div class="col-lg-3">
                                <label for="exampleFormControlSelect2"><?= LBL_Type; ?></label>
                                <select name="tent_type" class="form-control select2">
                                    <option value="0"><?= HM_ListAll; ?></option>
                                    <option value="1"><?= LBL_TentType1; ?></option>
                                    <option value="2"><?= LBL_TentType2; ?></option>
                                </select>
                            </div>
                            <div class="col-lg-3">
                                <label for="exampleFormControlSelect2"><?= LBL_Gender; ?></label>
                                <select name="tent_gender" class="form-control select2">
                                    <option value="All"><?= HM_ListAll; ?></option>
                                    <option value="m"><?= LBL_Male; ?></option>
                                    <option value="f"><?= LBL_Female; ?></option>
                                </select>
                            </div>
                            <div class="col-lg-3">
                                <label for="form-control"><?= HM_soothing; ?></label>
                                <select name="tent_soothing" class="form-control select2">
                                    <option value="0"><?= HM_ListAll; ?></option>
                                    <option value="1"><?= HM_soothing_true; ?></option>
                                    <option value="2"><?= HM_soothing_false; ?></option>
                                </select>
                            </div>
                            <div class="col-lg-3" style="margin-top: 25px;">
                                <button class="btn btn-success" name="filter"><?= HM_show; ?></button>
                            </div>
                        </div>
                    </form>
                    <div class="box-body">
                        <table class=" table table-bordered table-striped" id="myTable">
                            <thead>
                            <tr>
                                <th><?= LBL_Title; ?></th>
                                <th><?= LBL_Type; ?></th>
                                <th><?= LBL_Gender; ?></th>
                                <th><?= LBL_Capacity; ?></th>
                                <th><?= LBL_ٍRemaining; ?></th>
                                <th><?= LBL_Status; ?></th>
                                <th><?= LBL_Order; ?></th>
                                <th><?= LBL_Actions; ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?
                                while ($row = $sql->fetch()) {
                                    if (isset($_POST['filter'])) {
                                        $occu = $db->query("SELECT COUNT(pil_code) FROM pils_accomo WHERE tent_id = " . $row['tent_id'])->fetchColumn();
                                        $remaining = $row['tent_capacity'] - $occu;
                                        //  echo $soothing."<br>";
                                        // echo $occu."<br>";
                                        // echo $row['tent_capacity'];die;
                                        if ($soothing == 1 && $row['tent_capacity'] == $occu) {
                                            
                                            echo '<tr>';
                                            echo '<td>';
                                            echo $row['tent_title'];
                                            echo '</td>';
                                            echo '<td>';
                                            if ($row['tent_type'] == 1) echo LBL_TentType1;
                                            elseif ($row['tent_type'] == 2) echo LBL_TentType2;
                                            echo '</td>';
                                            echo '<td>';
                                            if ($row['tent_gender'] == 'm') echo LBL_Male;
                                            elseif ($row['tent_gender'] == 'f') echo LBL_Female;
                                            echo '</td>';
                                            echo '<td>';
                                            echo number_format($row['tent_capacity']);
                                            echo '</td>';
                                            echo '<td>';
                                            
                                            $occu = $db->query("SELECT COUNT(pil_code) FROM pils_accomo WHERE tent_id = " . $row['tent_id'])->fetchColumn();
                                            $remaining = $row['tent_capacity'] - $occu;
                                            echo number_format($remaining);
                                            echo '</td>';
                                            
                                            echo '<td>';
                                            if ($row['tent_active'] == 1) echo '<span class="label label-success">' . LBL_Active . '</span>';
                                            else echo '<span class="label label-danger">' . LBL_Inactive . '</span>';
                                            echo '</td>';
                                            
                                            echo '<td>';
                                            echo $row['tent_order'];
                                            echo '</td>';
                                            
                                            echo '<td>

	                        <a href="' . $newedit_page . '&id=' . $row[$table_id] . '" class="label label-info"><i class="fa fa-edit"></i> ' . LBL_Modify . '</a>
								<a href="' . basename($_SERVER['PHP_SELF']) . '?del=' . $row[$table_id] . '&type=' . $type . '" class="label label-danger" onclick="return confirm(\'' . LBL_DeleteConfirm . '\');"><i class="fa fa-trash"></i> ' . LBL_Delete . '</a>

	                        </td>
		                      </tr>';
                                        
                                        } elseif ($soothing == 2 && $row['tent_capacity'] != $occu) {
                                            
                                            echo '<tr>';
                                            echo '<td>';
                                            echo $row['tent_title'];
                                            echo '</td>';
                                            echo '<td>';
                                            if ($row['tent_type'] == 1) echo LBL_TentType1;
                                            elseif ($row['tent_type'] == 2) echo LBL_TentType2;
                                            echo '</td>';
                                            echo '<td>';
                                            if ($row['tent_gender'] == 'm') echo LBL_Male;
                                            elseif ($row['tent_gender'] == 'f') echo LBL_Female;
                                            echo '</td>';
                                            echo '<td>';
                                            echo number_format($row['tent_capacity']);
                                            echo '</td>';
                                            echo '<td>';
                                            
                                            $occu = $db->query("SELECT COUNT(pil_code) FROM pils_accomo WHERE tent_id = " . $row['tent_id'])->fetchColumn();
                                            $remaining = $row['tent_capacity'] - $occu;
                                            echo number_format($remaining);
                                            echo '</td>';
                                            
                                            echo '<td>';
                                            if ($row['tent_active'] == 1) echo '<span class="label label-success">' . LBL_Active . '</span>';
                                            else echo '<span class="label label-danger">' . LBL_Inactive . '</span>';
                                            echo '</td>';
                                            
                                            echo '<td>';
                                            echo $row['tent_order'];
                                            echo '</td>';
                                            
                                            echo '<td>

	                        <a href="' . $newedit_page . '&id=' . $row[$table_id] . '" class="label label-info"><i class="fa fa-edit"></i> ' . LBL_Modify . '</a>
								<a href="' . basename($_SERVER['PHP_SELF']) . '?del=' . $row[$table_id] . '&type=' . $type . '" class="label label-danger" onclick="return confirm(\'' . LBL_DeleteConfirm . '\');"><i class="fa fa-trash"></i> ' . LBL_Delete . '</a>

	                        </td>
		                      </tr>';
                                        
                                        } elseif ($soothing == 0) {
                                            
                                            
                                            echo '<tr>';
                                            echo '<td>';
                                            echo $row['tent_title'];
                                            echo '</td>';
                                            echo '<td>';
                                            if ($row['tent_type'] == 1) echo LBL_TentType1;
                                            elseif ($row['tent_type'] == 2) echo LBL_TentType2;
                                            echo '</td>';
                                            echo '<td>';
                                            if ($row['tent_gender'] == 'm') echo LBL_Male;
                                            elseif ($row['tent_gender'] == 'f') echo LBL_Female;
                                            echo '</td>';
                                            echo '<td>';
                                            echo number_format($row['tent_capacity']);
                                            echo '</td>';
                                            echo '<td>';
                                            
                                            $occu = $db->query("SELECT COUNT(pil_code) FROM pils_accomo WHERE tent_id = " . $row['tent_id'])->fetchColumn();
                                            $remaining = $row['tent_capacity'] - $occu;
                                            echo number_format($remaining);
                                            echo '</td>';
                                            
                                            echo '<td>';
                                            if ($row['tent_active'] == 1) echo '<span class="label label-success">' . LBL_Active . '</span>';
                                            else echo '<span class="label label-danger">' . LBL_Inactive . '</span>';
                                            echo '</td>';
                                            
                                            echo '<td>';
                                            echo $row['tent_order'];
                                            echo '</td>';
                                            
                                            echo '<td>

	                        <a href="' . $newedit_page . '&id=' . $row[$table_id] . '" class="label label-info"><i class="fa fa-edit"></i> ' . LBL_Modify . '</a>
								<a href="' . basename($_SERVER['PHP_SELF']) . '?del=' . $row[$table_id] . '&type=' . $type . '" class="label label-danger" onclick="return confirm(\'' . LBL_DeleteConfirm . '\');"><i class="fa fa-trash"></i> ' . LBL_Delete . '</a>

	                        </td>
		                      </tr>';
                                        
                                        }
                                    } else {
                                        
                                        echo '<tr>';
                                        echo '<td>';
                                        echo $row['tent_title'];
                                        echo '</td>';
                                        echo '<td>';
                                        if ($row['tent_type'] == 1) echo LBL_TentType1;
                                        elseif ($row['tent_type'] == 2) echo LBL_TentType2;
                                        echo '</td>';
                                        echo '<td>';
                                        if ($row['tent_gender'] == 'm') echo LBL_Male;
                                        elseif ($row['tent_gender'] == 'f') echo LBL_Female;
                                        echo '</td>';
                                        echo '<td>';
                                        echo number_format($row['tent_capacity']);
                                        echo '</td>';
                                        echo '<td>';
                                        
                                        $occu = $db->query("SELECT COUNT(pil_code) FROM pils_accomo WHERE tent_id = " . $row['tent_id'])->fetchColumn();
                                        $remaining = $row['tent_capacity'] - $occu;
                                        echo number_format($remaining);
                                        echo '</td>';
                                        
                                        echo '<td>';
                                        if ($row['tent_active'] == 1) echo '<span class="label label-success">' . LBL_Active . '</span>';
                                        else echo '<span class="label label-danger">' . LBL_Inactive . '</span>';
                                        echo '</td>';
                                        
                                        echo '<td>';
                                        echo $row['tent_order'];
                                        echo '</td>';
                                        
                                        echo '<td>

	                        <a href="' . $newedit_page . '&id=' . $row[$table_id] . '" class="label label-info"><i class="fa fa-edit"></i> ' . LBL_Modify . '</a>
								<a href="' . basename($_SERVER['PHP_SELF']) . '?del=' . $row[$table_id] . '&type=' . $type . '" class="label label-danger" onclick="return confirm(\'' . LBL_DeleteConfirm . '\');"><i class="fa fa-trash"></i> ' . LBL_Delete . '</a>

	                        </td>
		                      </tr>';
                                    
                                    }
                                    
                                    
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

<?php include 'layout/footer.php'; ?>


