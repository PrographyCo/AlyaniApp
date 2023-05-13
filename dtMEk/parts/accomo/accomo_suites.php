<?php
    global $db, $session, $lang, $url;
    
    $title = HM_SuitesAccomodations;
    
    if (isset($_GET['remove']) && $_GET['remove'] == 1) {
        $sqlmore1 = $sqlmore2 = '';
        if (is_numeric($_GET['suite_id']) && $_GET['suite_id'] > 0) $sqlmore1 = " AND suite_id = " . $_GET['suite_id'];
        if (is_numeric($_GET['hall_id']) && $_GET['hall_id'] > 0) $sqlmore2 = " AND hall_id = " . $_GET['hall_id'];
        
        $sqlremove = $db->query("DELETE FROM pils_accomo WHERE suite_id > 0 $sqlmore1 $sqlmore2");
    }
?>


<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?= $title ?>
            <a class="btn btn-success pull-<?= DIR_AFTER ?>" href="<?= CP_PATH ?>/accomo/export/export_accomo_suites_html?<?= http_build_query($_GET) ?>"
                    style="margin-<?= DIR_AFTER ?>: 10px" target="_blank"><i
                        class="fa fa-file-pdf-o"></i> <?= BTN_ExportToPDF ?></a>
            <a href="?remove=1&suite_id=<?= $_GET['suite_id']??'' ?>&hall_id=<?= $_GET['hall_id']??'' ?>"
               onclick="return confirm('<?= LBL_RemoveConfirm ?>');" class="btn btn-danger pull-<?= DIR_AFTER ?>"
               style="margin-<?= DIR_AFTER ?>: 10px"><i class="fa fa-trash"></i> <?= BTN_REMOVEACCOMO2 ?></a>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <!-- left column -->
            <div class="col-md-12">
                <?= $msg??'' ?>
                <div class="box">
                    <div class="box-body">
                        <form method="get">
                            <div class="row">
                                <div class="form-group col-sm-6">
                                    <label><?= LBL_SuiteNumber ?></label>
                                    <select name="suite_id" id="suite_id" class="form-control select2">
                                        <option value="0">
                                            <?= LBL_Choose ?>
                                        </option>
                                        <?php
                                            $sqlsuites = $db->query("SELECT * FROM suites WHERE suite_active = 1 ORDER BY suite_title");
                                            while ($rows = $sqlsuites->fetch(PDO::FETCH_ASSOC)) {
                                                echo '<option value="' . $rows['suite_id'] . '" ';
                                                if (isset($_GET['suite_id']) && $_GET['suite_id'] == $rows['suite_id']) echo 'selected="selected"';
                                                echo '>
															' . $rows['suite_title'] . '
															</option>';
                                            }
                                        ?>

                                    </select>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label><?= HM_Hall ?></label>
                                    <select name="hall_id" id="hall_id" class="form-control select2">
                                        <option value="0">
                                            <?= LBL_Choose ?>
                                        </option>
                                        <?php
                                            if (isset($_GET['suite_id']) && is_numeric($_GET['suite_id']) && $_GET['suite_id'] > 0) {
                                                
                                                $sqlhalls = $db->query("SELECT * FROM suites_halls WHERE hall_suite_id = " . $_GET['suite_id'] . " AND hall_active = 1 ORDER BY hall_title");
                                                while ($rowh = $sqlhalls->fetch(PDO::FETCH_ASSOC)) {
                                                    echo '<option value="' . $rowh['hall_id'] . '" ';
                                                    if (isset($_GET['hall_id']) && $_GET['hall_id'] == $rowh['hall_id']) echo 'selected="selected"';
                                                    echo '>
															' . $rowh['hall_title'] . '
															</option>';
                                                }
                                                
                                            }
                                        ?>

                                    </select>
                                </div>
                            </div>

                            <input type="submit" class="btn btn-primary col-xs-12" value="<?= LBL_SearchFilter ?>"/>

                        </form>
                    </div>
                </div>
                <div class="box html-content">

                    <div class="container-fluid">
                        <div>
                            <img src="<?= CP_PATH ?>/assets/images/logo.png" style="width:10%"/>
                        </div>
                        
                        <?php if (isset($_GET['hall_id']) && $_GET['hall_id']){
                            
                            $stmt = $db->prepare("SELECT * FROM suites WHERE suite_id = ?");
                            $stmt->execute(array($_GET['suite_id']));
                            $suite = $stmt->fetch();
                            
                            $stmt = $db->prepare("SELECT * FROM suites_halls WHERE hall_id = ?");
                            $stmt->execute(array($_GET['hall_id']));
                            $hall = $stmt->fetch();
                        
                        ?>

                        <div style="padding: 26px;font-size: 18px;">
                            <p><?= LBL_SuiteNumber . ': ' . $suite['suite_title'] . " - " . HM_Hall . ': ' . $hall['hall_title']; ?></p>

                        </div>
                    </div>
                    
                    <?php }
                    ?>

                    <div class="box-body">
                        <?php if (isset($_GET['suite_id'],$_GET['hall_id']) && is_numeric($_GET['suite_id']) && $_GET['hall_id'] > 0) { ?>
                            <table class=" table table-bordered table-striped" id="myTable2" cellspacing="0"
                                   cellpadding="0">
                                <thead>
                                <tr>
                                    <th class="text-center"><?= LBL_SuiteNumber . ': ' . $suite['suite_title'] ?></th>
                                    <th class="text-center" colspan="2"><?=  HM_Hall . ': ' . $hall['hall_title'] ?></th>
                                </tr>
                                <tr>
                                    <th><?= LBL_Name ?></th>
                                    <th><?= LBL_NationalId ?></th>
                                    <th><?= HM_Stuff ?></th>

                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                    $sqlmore1 = $sqlmore2 = '';
                                    if (is_numeric($_GET['suite_id']) && $_GET['suite_id'] > 0) $sqlmore1 = " AND pa.suite_id = " . $_GET['suite_id'];
                                    if (is_numeric($_GET['hall_id']) && $_GET['hall_id'] > 0) $sqlmore2 = " AND pa.hall_id = " . $_GET['hall_id'];
                                    
                                    $sql = $db->query("SELECT pa.*, p.pil_name, p.pil_nationalid, p.pil_reservation_number, s.suite_title , pa.hall_id ,pa.suite_id , pa.pil_code, h.hall_title, shs.stuff_type, shs.stuff_title, pc.pilc_title_$lang
												FROM pils_accomo pa
												INNER JOIN pils p ON pa.pil_code = p.pil_code
												LEFT OUTER JOIN suites s ON pa.suite_id = s.suite_id
												LEFT OUTER JOIN suites_halls h ON pa.hall_id = h.hall_id
                                                LEFT OUTER JOIN suites_halls_stuff shs ON pa.stuff_id = shs.stuff_id
                        LEFT OUTER JOIN pils_classes pc ON p.pil_pilc_id = pc.pilc_id
												WHERE pa.suite_id > 0 $sqlmore1 $sqlmore2 ORDER BY pa.suite_id, pa.hall_id, shs.stuff_type, shs.stuff_id");
                                    while ($row = $sql->fetch()) {
                                        
                                        echo '<tr>';
                                        
                                        echo '<td>';
                                        echo $row['pil_name'];
                                        echo '</td>';
                                        echo '<td>';
                                        echo $row['pil_nationalid'];
                                        echo '</td>';
                                        echo '<td>';
                                        echo (match($row['stuff_type']) {
                                            "bed"   => LBL_Bed,
                                            "chair" => LBL_Chair1,
                                            "bench" => LBL_Chair2
                                        } ) . ': ' . $row['stuff_title'];
                                        echo '</td>';
                                        echo '</tr>';
                                        
                                    }
                                ?>

                                </tbody>
                            </table>
                        <?php } else { ?>
                            <table class=" table table-bordered table-striped" id="myTable2">
                                <thead>
                                <tr>
                                    <th><?= LBL_Name ?></th>
                                    <th><?= LBL_NationalId ?></th>

                                    <th><?= LBL_SuiteNumber ?></th>
                                    <th><?= HM_Hall ?></th>
                                    <th><?= HM_Stuff ?></th>


                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                    $sqlmore1 = $sqlmore2 = '';
                                    if (isset($_GET['suite_id']) && is_numeric($_GET['suite_id']) && $_GET['suite_id'] > 0) $sqlmore1 = " AND pa.suite_id = " . $_GET['suite_id'];
                                    if (isset($_GET['hall_id']) && is_numeric($_GET['hall_id']) && $_GET['hall_id'] > 0) $sqlmore2 = " AND pa.hall_id = " . $_GET['hall_id'];
                                    
                                    $sql = $db->query("SELECT pa.*, p.pil_name, p.pil_nationalid, p.pil_reservation_number, s.suite_title , pa.hall_id ,pa.suite_id , pa.pil_code  , h.hall_title,shs.stuff_type, shs.stuff_title, pc.pilc_title_$lang
												FROM pils_accomo pa
												INNER JOIN pils p ON pa.pil_code = p.pil_code
												LEFT OUTER JOIN suites s ON pa.suite_id = s.suite_id
												LEFT OUTER JOIN suites_halls h ON pa.hall_id = h.hall_id
                                                LEFT OUTER JOIN suites_halls_stuff shs ON pa.stuff_id = shs.stuff_id
                        LEFT OUTER JOIN pils_classes pc ON p.pil_pilc_id = pc.pilc_id
												WHERE pa.suite_id > 0 $sqlmore1 $sqlmore2 ORDER BY pa.suite_id, pa.hall_id");
                                    while ($row = $sql->fetch()) {
                                        
                                        echo '<tr>';
                                        
                                        echo '<td>';
                                        echo $row['pil_name'];
                                        echo '</td>';
                                        echo '<td>';
                                        echo $row['pil_nationalid'];
                                        echo '</td>';
                                        
                                        echo '<td>';
                                        echo $row['suite_title'];
                                        echo '</td>';
                                        echo '<td>';
                                        echo $row['hall_title'];
                                        echo '</td>';
                                        echo '<td>';
                                        echo (match($row['stuff_type']) {
                                                "bed"   => LBL_Bed,
                                                "chair" => LBL_Chair1,
                                                "bench" => LBL_Chair2
                                            } ) . ': ' . $row['stuff_title'];
                                        echo '</td>';
                                        echo '</tr>';
                                        
                                    }
                                ?>

                                </tbody>
                            </table>
                        <?php } ?>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div><!--/.col (right) -->

        </div>   <!-- /.row -->
    </section><!-- /.content -->

</div>
