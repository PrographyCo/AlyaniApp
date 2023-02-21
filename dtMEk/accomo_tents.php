<?php
    include 'header.php';
    
    $title = HM_TentsAccomodations;
    
    if ($_GET['remove'] == 1) {
        
        if (is_numeric($_GET['tent_id']) && $_GET['tent_id'] > 0) $sqlmore1 = " AND tent_id = " . $_GET['tent_id'];
        $sqlremove = $db->query("DELETE FROM pils_accomo WHERE pil_accomo_type = 3 AND tent_id > 0 $sqlmore1");
        
    }

?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?= $title; ?>
            <img src="images/logo.png" style="width:10%"/>
            <a href="export_accomo_tents.php?tent_id=<?= $_GET['tent_id']; ?>"
               class="btn btn-success pull-<?= DIR_AFTER; ?>" style="margin-<?= DIR_AFTER; ?>: 10px" target="_blank"><i
                        class="fa fa-file-pdf-o"></i> <?= BTN_ExportToPDF; ?></a>
            <a href="?remove=1&tent_id=<?= $_GET['tent_id']; ?>" onclick="return confirm('<?= LBL_RemoveConfirm; ?>');"
               class="btn btn-danger pull-<?= DIR_AFTER; ?>" style="margin-<?= DIR_AFTER; ?>: 10px"><i
                        class="fa fa-trash"></i> <?= BTN_REMOVEACCOMO2; ?></a>

        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <!-- left column -->
            <div class="col-md-12">
                <?php echo $msg; ?>
                <div class="box">
                    <div class="box-body">
                        <form method="get">
                            <div class="row">
                                <div class="form-group col-sm-12">
                                    <label><?= LBL_TentNumber; ?></label>
                                    <select name="tent_id" id="tent_id" class="form-control select2">
                                        <option value="0">
                                            <?= LBL_Choose; ?>
                                        </option>
                                        <?
                                            $sqltents = $db->query("SELECT * FROM tents WHERE tent_active = 1  AND type = 1 ORDER BY tent_title");
                                            while ($rowt = $sqltents->fetch(PDO::FETCH_ASSOC)) {
                                                echo '<option value="' . $rowt['tent_id'] . '" ';
                                                if ($_GET['tent_id'] == $rowt['tent_id']) echo 'selected="selected"';
                                                echo '>
															' . $rowt['tent_title'] . '
															</option>';
                                            }
                                        ?>

                                    </select>
                                </div>
                            </div>

                            <input type="submit" class="btn btn-primary col-xs-12" value="<?= LBL_SearchFilter; ?>"/>

                        </form>
                    </div>
                </div>
                <div class="box">
                    
                    <?php if ($_GET['tent_id']) {
                        
                        $stmt = $db->prepare("SELECT * FROM tents WHERE tent_id = ?");
                        $stmt->execute(array($_GET['tent_id']));
                        $tent = $stmt->fetch();
                        
                        
                        ?>
                        <div style="padding: 26px;$tent-size: 18px;">
                            <p><?php echo $tent['tent_title'] . " - " . $tent['tent_order']; ?></p>

                        </div>
                    <?php }
                    ?>

                    <div class="box-body">
                        <table class=" table table-bordered table-striped" id="myTable2">
                            <thead>
                            <tr>
                                <th><?= LBL_Code; ?></th>
                                <th><?= LBL_Name; ?></th>
                                <th><?= LBL_NationalId; ?></th>

                            </tr>
                            </thead>
                            <tbody>
                            <?
                                
                                if (is_numeric($_GET['tent_id']) && $_GET['tent_id'] > 0) $sqlmore1 = " AND pa.tent_id = " . $_GET['tent_id'];
                                
                                $sql = $db->query("SELECT pa.*, p.pil_name, p.pil_nationalid, p.pil_reservation_number, t.tent_title
												FROM pils_accomo pa
												INNER JOIN pils p ON pa.pil_code = p.pil_code
												LEFT OUTER JOIN tents t ON pa.tent_id = t.tent_id
												WHERE pa.tent_id > 0 AND t.type = 1 $sqlmore1");
                                while ($row = $sql->fetch()) {
                                    
                                    echo '<tr>';
                                    echo '<td>';
                                    echo $row['pil_code'];
                                    echo '</td>';
                                    echo '<td>';
                                    echo $row['pil_name'];
                                    echo '</td>';
                                    echo '<td>';
                                    echo $row['pil_nationalid'];
                                    echo '</td>';
                                    
                                    echo '</tr>';
                                    
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

<?php include 'footer.php'; ?>
