<?php
    global $db, $session, $lang, $url;
    set_time_limit(0);
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <!-- left column -->
            <div class="col-md-12">
                <?= $msg ?? '' ?>

                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title"><?= BTN_ImportFromExcel ?></h3>
                    </div><!-- /.box-header -->
                    <div class="box-body">

                        <form method="post" enctype="multipart/form-data">

                            <div class="form-group">
                                <label><?= HM_ManageClasses ?></label>
                                <select name="pilc_id" id="pilc_id" class="form-control select2" required="required">
                                    <option value=""><?= LBL_Choose ?></option>
                                    <?php
                                        $sqlc = $db->query("SELECT * FROM pils_classes ORDER BY pilc_title_" . $lang);
                                        while ($rowc = $sqlc->fetch(PDO::FETCH_ASSOC)) {
                                            
                                            echo '<option value="' . $rowc['pilc_id'] . '" ';
                                            if (isset($_POST['pilc_id']) && $_POST['pilc_id'] === $rowc['pilc_id']) echo 'selected="selected"';
                                            echo '>' . $rowc['pilc_title_' . $lang] . '</option>';
                                            
                                        }
                                    ?>

                                </select>
                            </div>
                            <div class="form-group">
                                <label><?= LBL_File ?></label>
                                <input type="file" class="form-control" name="excelfile" accept=".xls,.xlsx"/>
                            </div>
                            <input type="submit" class="col-md-12 btn btn-success" value="<?= LBL_Upload ?>"/>
                        </form>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
                
                
                <?php
                    if ($_FILES) {
                    
                    if ($_FILES['excelfile']['name']) {
                    
                    $filenameext = pathinfo($_FILES['excelfile']['name'], PATHINFO_EXTENSION);
                    $newname = 'Import_' . date("Y_m_d_H_i_s") . '.' . $filenameext;
                    move_uploaded_file($_FILES['excelfile']['tmp_name'], ASSETS_PATH.'media/imports/' . $newname);
                
                ?>
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title"><?= LBL_FileInformation ?></h3>
                    </div>
                    <div class="box-body">
                        <?php
                            
                            $inputFileName = ASSETS_PATH."media/imports/" . $newname;
                            $objPHPExcel = PHPExcel_IOFactory::load($inputFileName);
                            $sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
                            
                            foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
                                $arrayData[$worksheet->getTitle()] = $worksheet->toArray();
                            }
                            
                            //var_dump($arrayData);
                            
                            $count = count($sheetData) - 1;
                        
                        ?>
                        <h2><?= LBL_PilsInSheet ?>: <b><?= $count ?></b></h2><br/>
                        <form method="post">
                            <table class="table datatable-table4">
                                <thead>
                                <th><?= LBL_NationalId ?></th>
                                <th><?= LBL_Name ?></th>
                                <th><?= LBL_Nationality ?></th>
                                <th><?= LBL_Gender ?></th>
                                <th><?= LBL_Phone ?></th>
                                <th><?= LBL_ReservationNumber ?></th>
                                <th><?= LBL_City ?></th>
                                <th><?= LBL_Actions ?></th>
                                </thead>
                                <tbody>
                                <?php
                                    $notFoundCities = array();
                                    $cnt = 0;
                                    foreach ($sheetData as $data) {
                                        
                                        if ($cnt > 0) {
                                            
                                            $nationalid = trim($data['A']);
                                            $name = trim($data['B']);
                                            $country = trim($data['C']);
                                            $gender = trim($data['D']);
                                            $phone = trim($data['E']);
                                            $reservation = trim($data['F']);
                                            $city = trim($data['G']);
                                            
                                            ?>
                                            <tr>
                                                <td>
                                                    <?= $nationalid ?>
                                                </td>
                                                <td>
                                                    <?= $name ?>
                                                </td>
                                                <td>
                                                    <?= $country ?>
                                                </td>
                                                <td>
                                                    <?php
                                                        if (strtolower($gender) === 'm' || strtolower($gender) === 'ذكر' || strtolower($gender) === 'male') echo 'ذكر';
                                                        if (strtolower($gender) === 'f' || strtolower($gender) === 'أنثى' || strtolower($gender) === 'female' || strtolower($gender) === 'انثى' || strtolower($gender) === 'أنثي' || strtolower($gender) === 'انثي') echo 'انثى';
                                                        
                                                        echo '</td>';
                                                        
                                                        echo '<td>';
                                                        echo $phone;
                                                        echo '</td>';
                                                        
                                                        echo '<td>';
                                                        echo $reservation;
                                                        echo '</td>';
                                                        
                                                        echo '<td>';
                                                        echo $city;
                                                        echo '</td>';
                                                        
                                                        $city_id = $db->query("SELECT city_id FROM cities WHERE city_title_en = '$city' OR city_title_ar = '$city' OR city_title_ur = '$city'")->fetchColumn();
                                                        if (!$city_id) $notFoundCities[] = $city;
                                                        
                                                        if ($nationalid) {
                                                            
                                                            $chk1 = $db->prepare("SELECT pil_id FROM pils WHERE pil_nationalid = :nationalid");
                                                            $chk1->bindValue("nationalid", $nationalid);
                                                            $chk1->execute();
                                                            
                                                            if ($chk1->rowCount() > 0) echo '<td>' . LBL_Update . '</td>';
                                                            else echo '<td>' . LBL_Add . '</td>';
                                                            
                                                        }
                                                    ?>
                                            </tr>
                                            <?php
                                        }
                                        $cnt++;
                                    }
                                    
                                    echo '</tbody></table><br /><br />';
                                    
                                    echo '<input type="hidden" name="confirm" value="1" />';
                                    echo '<input type="hidden" name="fileimportned" value="' . $newname . '" />';
                                    echo '<input type="hidden" name="pilc_id" value="' . $_POST['pilc_id'] . '" />';
                                    
                                    if (count($notFoundCities) > 0) {
                                        echo '<h3><center>
								' . LBL_CITIESNOTFOUND . '<br /><br />';
                                        
                                        foreach ($notFoundCities as $notFoundCity) {
                                            echo $notFoundCity . '<br />';
                                        }
                                        
                                        echo '
								</center></h3>';
                                        echo '<br />';
                                        echo '<input type="submit" class="btn btn-primary col-sm-12" value="' . LBL_Confirm . '" disabled="disabled"/>';
                                        
                                    } else {
                                        echo '<input type="submit" class="btn btn-primary col-sm-12" value="' . LBL_Confirm . '" />';
                                    }
                                    
                                    echo '</form></div></div>';
                                    }
                                    
                                    }
                                ?>
                                
                                <?php
                                    
                                    if (isset($_POST['confirm'])) {
                                        echo '<div class="box box-info">
			                <div class="box-header with-border">
			                  <h3 class="box-title">' . LBL_ApplyingFileInfo . '</h3>
			                </div>
			                <div class="box-body">';
                                        
                                        
                                        if (is_file(ASSETS_PATH."media/imports/" . $_POST['fileimportned'])) {
                                            
                                            $inputFileName = ASSETS_PATH."media/imports/" . $_POST['fileimportned'];
                                            $objPHPExcel = PHPExcel_IOFactory::load($inputFileName);
                                            $sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
                                            $count = count($sheetData) - 1;
                                            
                                            echo '<h2>' . LBL_PilsInSheet . ': <b>' . $count . '</b></h2><br />';
                                            
                                            echo '<table class="table datatable-table4"><thead>
							<th>' . LBL_NationalId . '</th>
							<th>' . LBL_Name . '</th>
							<th>' . LBL_Nationality . '</th>
							<th>' . LBL_Gender . '</th>
							<th>' . LBL_Phone . '</th>
							<th>' . LBL_ReservationNumber . '</th>
							<th>' . LBL_City . '</th>
							<th>' . LBL_Actions . '</th>
							</thead><tbody>';
                                            
                                            try {
                                                
                                                $db->beginTransaction();
                                                $added = 0;
                                                $updated = 0;
                                                $cnt = 0;
                                                
                                                foreach ($sheetData as $data) {
                                                    if ($cnt > 0) {
                                                        
                                                        echo '<tr>';
                                                        
                                                        $nationalid = trim($data['A']);
                                                        $name = trim($data['B']);
                                                        $country = trim($data['C']);
                                                        $gender = trim($data['D']);
                                                        $phone = trim($data['E']);
                                                        $reservation = trim($data['F']);
                                                        $city = trim($data['G']);
                                                        
                                                        echo '<td>';
                                                        echo $nationalid;
                                                        echo '</td>';
                                                        
                                                        echo '<td>';
                                                        echo $name;
                                                        echo '</td>';
                                                        
                                                        echo '<td>';
                                                        echo $country;
                                                        echo '</td>';
                                                        
                                                        echo '<td>';
                                                        
                                                        if (strtolower($gender) == 'm' || strtolower($gender) == 'ذكر' || strtolower($gender) == 'male') echo 'ذكر';
                                                        if (strtolower($gender) == 'f' || strtolower($gender) == 'أنثى' || strtolower($gender) == 'female' || strtolower($gender) == 'انثى' || strtolower($gender) == 'أنثي' || strtolower($gender) == 'انثي') echo 'انثى';
                                                        
                                                        echo '</td>';
                                                        
                                                        echo '<td>';
                                                        echo $phone;
                                                        echo '</td>';
                                                        
                                                        echo '<td>';
                                                        echo $reservation;
                                                        echo '</td>';
                                                        
                                                        echo '<td>';
                                                        echo $city;
                                                        echo '</td>';
                                                        
                                                        
                                                        if ($nationalid) {
                                                            
                                                            $chk1 = $db->prepare("SELECT pil_id FROM pils WHERE pil_nationalid = :nationalid");
                                                            $chk1->bindValue("nationalid", $nationalid);
                                                            $chk1->execute();
                                                            
                                                            if ($chk1->rowCount() > 0) echo '<td>' . LBL_Update . '</td>';
                                                            else echo '<td>' . LBL_Add . '</td>';
                                                            
                                                        }
                                                        
                                                        echo '</tr>';
                                                        
                                                        // Get country_id
                                                        $country_id = $db->query("SELECT country_id FROM countries WHERE country_title_en = '$country' OR country_title_ar = '$country'")->fetchColumn();
                                                        if (!$country_id) {
                                                            $sqlins1 = $db->prepare("INSERT INTO countries VALUES ('', :country_title_en, :country_title_ar)");
                                                            $sqlins1->bindValue("country_title_en", $country);
                                                            $sqlins1->bindValue("country_title_ar", $country);
                                                            $sqlins1->execute();
                                                            $country_id = $db->lastInsertId();
                                                        }
                                                        
                                                        // Get city_id
                                                        $city_id = $db->query("SELECT city_id FROM cities WHERE city_title_en = '$city' OR city_title_ar = '$city' OR city_title_ur = '$city'")->fetchColumn();
                                                        
                                                        // Manage Gender
                                                        if (strtolower($gender) == 'm' || strtolower($gender) == 'ذكر' || strtolower($gender) == 'male') $new_gender = 'm';
                                                        if (strtolower($gender) == 'f' || strtolower($gender) == 'أنثى' || strtolower($gender) == 'female' || strtolower($gender) == 'انثى' || strtolower($gender) == 'أنثي' || strtolower($gender) == 'انثي') $new_gender = 'f';
                                                        
                                                        // Manage pil_pilc_id
                                                        $pil_pilc_id = $_POST['pilc_id'];
                                                        
                                                        if ($chk1->rowCount() > 0) {
                                                            
                                                            // update
                                                            $sql1 = $db->prepare("UPDATE pils SET
										pil_name = :pil_name,
										pil_country_id = :pil_country_id,
										pil_gender = :pil_gender,
										pil_phone = :pil_phone,
										pil_reservation_number = :pil_reservation_number,
										pil_city_id = :pil_city_id,
										pil_pilc_id = :pil_pilc_id

										WHERE pil_nationalid = :pil_nationalid");
                                                            
                                                            $sql1->bindValue("pil_name", $name);
                                                            $sql1->bindValue("pil_country_id", $country_id);
                                                            $sql1->bindValue("pil_gender", $new_gender);
                                                            $sql1->bindValue("pil_phone", $phone);
                                                            $sql1->bindValue("pil_reservation_number", $reservation);
                                                            $sql1->bindValue("pil_city_id", $city_id);
                                                            $sql1->bindValue("pil_pilc_id", $pil_pilc_id);
                                                            $sql1->bindValue("pil_nationalid", $nationalid);
                                                            $sql1->execute();
                                                            $updated++;
                                                            
                                                        } else {
                                                            
                                                            $sql = $db->prepare("INSERT INTO pils VALUES (
										:id,
										:pil_name,
										:pil_pilc_id,
										:pil_country_id,
										:pil_phone,
										:pil_nationalid,
										:pil_reservation_number,
										:pil_code,
										:pil_city_id,
										:pil_bus_id,
										:pil_photo,
										:pil_gender,
										:pil_qrcode,
										:pil_card,
										:pil_verified,
										:pil_active,
										:pil_dateadded,
										:pil_lastupdated
										)");
                                                            
                                                            $sql->bindValue("id", '');
                                                            $sql->bindValue("pil_name", $name);
                                                            $sql->bindValue("pil_pilc_id", $pil_pilc_id);
                                                            $sql->bindValue("pil_country_id", $country_id);
                                                            $sql->bindValue("pil_phone", $phone);
                                                            $sql->bindValue("pil_nationalid", $nationalid);
                                                            $sql->bindValue("pil_reservation_number", $reservation);
                                                            $sql->bindValue("pil_code", "");
                                                            $sql->bindValue("pil_city_id", $city_id);
                                                            $sql->bindValue("pil_bus_id", 0);
                                                            $sql->bindValue("pil_photo", "");
                                                            $sql->bindValue("pil_gender", $new_gender);
                                                            $sql->bindValue("pil_qrcode", "");
                                                            $sql->bindValue("pil_card", "");
                                                            $sql->bindValue("pil_verified", 1);
                                                            $sql->bindValue("pil_active", 1);
                                                            $sql->bindValue("pil_dateadded", time());
                                                            $sql->bindValue("pil_lastupdated", time());
                                                            
                                                            if ($sql->execute()) {
                                                                
                                                                $id = $db->lastInsertId();
                                                                
                                                                sendWelcomeMessagePil($id);
                                                                
                                                                if ($new_gender == 'm') $sql2 = $db->query("UPDATE pils SET pil_photo = 'default_male.png' WHERE pil_id = $id");
                                                                elseif ($new_gender == 'f') $sql2 = $db->query("UPDATE pils SET pil_photo = 'default_female.png' WHERE pil_id = $id");
                                                                
                                                                // Generate Code
                                                                $city_prefix = $db->query("SELECT city_prefix FROM cities WHERE city_id = " . $city_id)->fetchColumn();
                                                                $pad_length = 5;
                                                                $pad_char = 0;
                                                                $str_type = 'd'; // treats input as integer, and outputs as a (signed) decimal number
                                                                $format = "%{$pad_char}{$pad_length}{$str_type}"; // or "%04d"
                                                                $formatted_str = sprintf($format, $id);
                                                                $pil_code = $city_prefix . $formatted_str;
                                                                $sqlupdcode = $db->query("UPDATE pils SET pil_code = '$pil_code' WHERE pil_id = $id");
                                                                
                                                                
                                                                // Manage QR Code
                                                                
                                                                $Dir = ASSETS_PATH.'media/pils_qrcodes/';
                                                                $codeContents = $id;
                                                                QRcode::png($codeContents, $Dir . $id . '.png', QR_ECLEVEL_L, 10);
                                                                $sqlupdcode = $db->query("UPDATE pils SET pil_qrcode = '$id.png' WHERE pil_id = $id");
                                                                
                                                                $added++;
                                                            }
                                                        }
                                                        
                                                    }
                                                    $cnt++;
                                                }
                                                
                                                echo '</tbody></table><br /><br />';
                                                
                                                $db->commit();
                                                
                                                echo '<h1>';
                                                echo LBL_PilgrimsAdded . ': ' . $added . '<br />';
                                                echo LBL_PilgrimsUpdated . ': ' . $updated . '<br />';
                                                echo '</h1>';
                                                
                                            } catch (PDOException $e) {
                                                
                                                $db->rollBack();
                                                echo '<span style="color:red"><b>DATABASE ERROR: ' . $e->getMessage() . ' - Line:' . $e->getLine() . '</b></span>';
                                                
                                            }
                                            
                                        } else {
                                            
                                            echo '<span style="color:red"><b>File Already Imported or Not Found</b></span>';
                                            
                                        }
                                        echo '</div></div>';
                                    }
                                ?>


                    </div><!--/.col (right) -->

                </div>   <!-- /.row -->
    </section><!-- /.content -->

</div>
