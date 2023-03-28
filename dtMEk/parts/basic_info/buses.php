<?php
    global $db, $session, $lang, $url;
    
    $title = HM_PilgrimsBuses;
    $table = 'buses';
    $table_id = 'bus_id';
    $newedit_page = CP_PATH.'/basic_info/edit/bus';
    
    if (isset($_GET['del']) && is_numeric($_GET['del'])) {
        $id = $_GET['del'];
        $sqldel1 = $db->query("DELETE FROM $table WHERE $table_id = $id");
    }
    
    $sql = $db->query("SELECT b.*, c.city_title_ar FROM $table b LEFT OUTER JOIN cities c ON b.bus_city_id = c.city_id ORDER BY b.bus_order");
    
    if (isset($_POST['filter'])) {
        $bus_city_id = $_POST['bus_city_id'];
        
        if ($bus_city_id != 0) {
            $sql = $db->query("SELECT b.*, c.city_title_ar FROM $table b LEFT OUTER JOIN cities c ON b.bus_city_id = c.city_id WHERE c.city_id = $bus_city_id ORDER BY b.bus_order");
        } else {
            $sql = $db->query("SELECT b.*, c.city_title_ar FROM $table b LEFT OUTER JOIN cities c ON b.bus_city_id = c.city_id ORDER BY b.bus_order");
        }
        
        
    }
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?= $title ?>
            <a href="<?= $newedit_page ?>" class="btn btn-success pull-<?= DIR_AFTER ?>"><i
                        class="fa fa-star"></i> <?= BTN_AddNew ?></a>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <!-- left column -->
            <div class="col-md-12">
                <?= $msg??'' ?>

                <div class="box">

                    <form method="post" style="margin: 40px;">
                        <div class="row">
                            <div class="col-lg-2">
                                <label><?= LBL_City ?></label>
                                <select name="bus_city_id" class="form-control select2">
                                    <option value="0"><?= LBL_Choose ?></option>
                                    <option value="0"><?= HM_ListAll ?></option>
                                    <?php
                                        $sqlcities = $db->query("SELECT city_id, city_title_en, city_title_ar FROM cities ORDER BY city_title_" . $lang);
                                        while ($rowcities = $sqlcities->fetch(PDO::FETCH_ASSOC)) {
                                            
                                            echo '<option value="' . $rowcities['city_id'] . '" ';
                                            if (isset($_POST['bus_city_id']) && $_POST['bus_city_id'] == $rowcities['city_id']) echo 'selected="selected"';
                                            echo '>' . $rowcities['city_title_' . $lang] . '</option>';
                                            
                                        }
                                    ?>
                                </select>
                            </div>

                            <div class="col-lg-2" style="margin-top: 25px;">
                                <button class="btn btn-success" name="filter"><?= HM_show ?></button>
                            </div>
                        </div>
                    </form>

                    <div class="box-body">
                        <table class="datatable-table4 table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th><?= LBL_BusNumber ?></th>
                                <th><?= LBL_City ?></th>
                                <th><?= LBL_BusSeats ?></th>
                                <th><?= LBL_ٍRemaining ?></th>
                                <th><?= HM_staff_name ?></th>
                                <th><?= LBL_Status ?></th>
                                <th><?= LBL_Order ?></th>
                                <th><?= LBL_Actions ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                                
                                while ($row = $sql->fetch()) {
                                    $occu = $db->query("SELECT COUNT(pil_id) FROM pils WHERE pil_bus_id = " . $row['bus_id'])->fetchColumn();
                                    $staff = $db->query("SELECT staff_name FROM staff WHERE staff_id = " . $row['bus_staff_id'])->fetchColumn();
    
                                    $remaining = $row['bus_seats'] - $occu;
    
                                    ?>
                            <tr>
                                <td>
                                    <?= $row['bus_title'] ?>
                                </td>
                                <td>
                                    <?= $row['city_title_ar'] ?>
                                </td>
                                <td>
                                    <?= number_format($row['bus_seats']) ?>
                                </td>
                                <td>
                                    <?= number_format($remaining) ?>
                                </td>
                                <td>
                                    <?= $staff ?>
                                </td>
                                <td>
                                    <span class="label label-<?= ($row['bus_active'] == 1)?"success":"danger" ?>"><?= ($row['bus_active'] == 1)?LBL_Active:LBL_Inactive ?></span>
                                </td>
                                <td>
                                    <?= $row['bus_order'] ?>
                                </td><td>

                                    <a href="<?= $newedit_page . '?id=' . $row[$table_id] ?>" class="label label-info"><i class="fa fa-edit"></i><?= LBL_Modify ?></a>
                                    <a href="<?= CP_PATH.$url . '?del=' . $row[$table_id] ?>" class="label label-danger" onclick="return confirm('<?= LBL_DeleteConfirm ?>');"><i class="fa fa-trash"></i><?= LBL_Delete ?></a>

                                </td>
                            </tr>
                                    <?php
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
