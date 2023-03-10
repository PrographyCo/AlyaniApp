<?php include 'layout/header.php';
    
    $title = HM_PILGRIMS;
    $table = 'pils';
    $table_id = 'pil_id';
    $newedit_page = CP_PATH.'/pilgrims/edit/pil';
    
    if (is_numeric($_GET['del'])) {
        
        $id = $_GET['del'];
        
        $pil_code = $db->query("SELECT pil_code FROM pils WHERE pil_id = $id")->fetchColumn();
        $photo = $db->query("SELECT pil_photo FROM $table WHERE $table_id = $id")->fetchColumn();
        $qr_photo = $db->query("SELECT pil_qrcode FROM $table WHERE $table_id = $id")->fetchColumn();
        
        if ($pil_code) $sqldel2 = $db->query("DELETE FROM pils_accomo WHERE pil_code = '$pil_code'");
        if ($photo && $photo !== 'default_photo.png' && $photo != 'default_male.png' && $photo != 'default_female.png' && is_file(ASSETS_PATH.'media/pils/' . $photo)) @unlink(ASSETS_PATH.'media/pils/' . $photo);
        if ($qr_photo && is_file(ASSETS_PATH.'media/pils_qrcodes/' . $qr_photo)) @unlink(ASSETS_PATH.'media/pils_qrcodes/' . $qr_photo);
        
        $sqldel1 = $db->query("DELETE FROM $table WHERE $table_id = $id");
        
    }
    
    if ($_GET['deleteall'] == 1) {
        
        if (is_numeric($_GET['city_id']) && $_GET['city_id'] > 0) $sqlmore1 = " AND pil_city_id = " . $_GET['city_id'];
        if ($_GET['gender'] == 'm' || $_GET['gender'] == 'f') $sqlmore2 = " AND pil_gender = '" . $_GET['gender'] . "'";
        if (is_numeric($_GET['pilc_id']) && $_GET['pilc_id'] > 0) $sqlmore3 = " AND pil_pilc_id = " . $_GET['pilc_id'];
        if ($_GET['code']) $sqlmore4 = " AND pil_code LIKE '%" . $_GET['code'] . "%'";
        if ($_GET['resno']) $sqlmore5 = " AND pil_reservation_number LIKE '%" . $_GET['resno'] . "%'";
        if (is_numeric($_GET['accomo']) && $_GET['accomo'] == 1) $sqlmore6 = " AND pil_code IN (SELECT pil_code FROM pils_accomo)";
        if (is_numeric($_GET['accomo']) && $_GET['accomo'] == 2) $sqlmore6 = " AND pil_code NOT IN (SELECT pil_code FROM pils_accomo)";
        
        
        $sqlallpils = $db->query("SELECT p.*, c.country_title_$lang, ci.city_title_$lang FROM $table p
		LEFT OUTER JOIN countries c ON p.pil_country_id = c.country_id
		LEFT OUTER JOIN cities ci ON p.pil_city_id = ci.city_id
		WHERE 1 $sqlmore1 $sqlmore2 $sqlmore3 $sqlmore4 $sqlmore5 $sqlmore6
		ORDER BY pil_name");
        
        while ($rowallpils = $sqlallpils->fetch(PDO::FETCH_ASSOC)) {
            
            $id = $rowallpils['pil_id'];
            
            $pil_code = $db->query("SELECT pil_code FROM pils WHERE pil_id = $id")->fetchColumn();
            $photo = $db->query("SELECT pil_photo FROM $table WHERE $table_id = $id")->fetchColumn();
            $qr_photo = $db->query("SELECT pil_qrcode FROM $table WHERE $table_id = $id")->fetchColumn();
            
            if ($pil_code) $sqldel2 = $db->query("DELETE FROM pils_accomo WHERE pil_code = '$pil_code'");
            if ($photo && $photo != 'default_photo.png' && $photo != 'default_male.png' && $photo != 'default_female.png' && is_file('media/pils/' . $photo)) @unlink('media/pils/' . $photo);
            if ($qr_photo && is_file('media/pils_qrcodes/' . $qr_photo)) @unlink('media/pils_qrcodes/' . $qr_photo);
            
            $sqldel1 = $db->query("DELETE FROM $table WHERE $table_id = $id");
            
        }
        
    }

?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?= $title; ?>
            <!--<img src="images/logo.png" style="width:10%"/>-->
            <button onclick="convertpdf()" class="btn btn-success pull-<?= DIR_AFTER; ?>"
                    style="margin-<?= DIR_AFTER; ?>: 10px" target="_blank"><i
                        class="fa fa-file-pdf-o"></i> <?= BTN_ExportToPDF; ?></button>


        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <!-- left column -->
            <div class="col-md-12">
                <?php echo $msg; ?>

                <div class="box">
                    <form method="get">
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label><?= HM_Hall; ?></label>
                                <select name="tent_id" id="tent_id" class="form-control select2">
                                    <option value="0">
                                        <?= HM_ListAll; ?>
                                    </option>
                                    <?php
                                        
                                        $sqlhalls = $db->query("SELECT * FROM tents WHERE tent_type = 2 AND tent_active = 1");
                                        while ($rowh = $sqlhalls->fetch(PDO::FETCH_ASSOC)) {
                                            echo '<option value="' . $rowh['tent_id'] . '" ';
                                            if ($_GET['tent_id'] == $rowh['tent_id']) echo 'selected="selected"';
                                            echo '>
															' . $rowh['tent_title'] . '
															</option>';
                                        }
                                    
                                    
                                    ?>

                                </select>
                            </div>
                        </div>

                        <input type="submit" class="btn btn-primary col-xs-12" value="<?= LBL_SearchFilter; ?>"/>

                    </form>

                    <div class="box-body html-content">

                        <table class=" table table-bordered table-striped text-center" style="font-size:37px">
                            <thead>
                            <tr>
                                <img src="images/logo.png"
                                     style="width: 17%;position: relative;right: 30%;margin-bottom: 24px;"/>
                                <?php
                                    if (is_numeric($_GET['tent_id'])) {
                                        $id = $_GET['tent_id'];
                                        $sqlhalls = $db->query("SELECT * FROM tents WHERE tent_id =$id");
                                        $rowh = $sqlhalls->fetch()
                                        ?>
                                        <h2><?= HM_Hall . ': ' . $rowh['tent_title'] ?></h2>
                                    <?php } ?>
                            </tr>
                            <tr>
                                <th><?= LBL_Name; ?></th>
                                <th><?= seat; ?></th>
                                <th><?= LBL_Name; ?></th>
                                <th><?= seat; ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                                
                                
                                if (is_numeric($_GET['tent_id'])) {
                                    $id = $_GET['tent_id'];
                                    if ($id == 0) {
                                        $sql = $db->query("SELECT * FROM $table  ORDER BY pil_name");
                                        $items = $sql->fetchAll();
                                    } else {
                                        $sql = $db->query("SELECT * FROM $table  WHERE pil_code IN  (SELECT pil_code FROM pils_accomo WHERE  halls_id = $id) ORDER BY pil_name");
                                        $items = $sql->fetchAll();
                                    }
                                } else {
                                    
                                    $sql = $db->query("SELECT * FROM $table  ORDER BY pil_name");
                                    $items = $sql->fetchAll();
                                }
                                
                                $i = 0;
                                
                                for (; $i < count($items); $i++) {
                                    $set = 0;
                                    
                                    $stmt = $db->prepare("SELECT * FROM pils_accomo WHERE  pil_code = ?");
                                    $stmt->execute(array($items[$i]['pil_code']));
                                    $accomo = $stmt->fetch();
                                    if (!empty($accomo)) {
                                        
                                        
                                        if ($accomo['seats'] != 0) {
                                            
                                            
                                            $stmt = $db->prepare("SELECT * FROM pils_accomo WHERE  halls_id = ? AND seats = ?");
                                            $stmt->execute(array($accomo['halls_id'], 1));
                                            $seats = $stmt->fetchAll();
                                            $check = $stmt->rowCount();
                                            if ($check > 0) {
                                                foreach ($seats as $seat) {
                                                    $set++;
                                                    if ($seat['pil_code'] == $items[$i]['pil_code']) {
                                                        
                                                        break;
                                                    }
                                                }
                                            }
                                        } else {
                                            $set = without_seat;
                                        }
                                    }
                                    
                                    
                                    echo '<tr>';
                                    echo '<td>';
                                    echo $items[$i]['pil_name'];
                                    echo '</td>';
                                    echo '<td>';
                                    echo $set;
                                    echo '</td>';
                                    
                                    echo '<td>';
                                    echo $items[++$i]['pil_name'];
                                    echo '</td>';
                                    $set = 0;
                                    $stmt = $db->prepare("SELECT * FROM pils_accomo WHERE  pil_code = ?");
                                    $stmt->execute(array($items[$i]['pil_code']));
                                    $accomo = $stmt->fetch();
                                    if (!empty($accomo)) {
                                        
                                        if ($accomo['seats'] != 0) {
                                            
                                            
                                            $stmt = $db->prepare("SELECT * FROM pils_accomo WHERE  halls_id = ? AND seats = ?");
                                            $stmt->execute(array($accomo['halls_id'], 1));
                                            $seats = $stmt->fetchAll();
                                            $check = $stmt->rowCount();
                                            if ($check > 0) {
                                                foreach ($seats as $seat) {
                                                    $set++;
                                                    if ($seat['pil_code'] == $items[$i]['pil_code']) {
                                                        
                                                        break;
                                                    }
                                                }
                                            }
                                        } else {
                                            $set = without_seat;
                                        }
                                        
                                    }
                                    
                                    echo '<td>';
                                    echo $set;
                                    echo '</td>';
                                    '</tr>';
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
