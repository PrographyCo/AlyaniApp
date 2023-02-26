<?php
    
    global $db;
    
    
    $pil_id = $_POST['pil_id'] ?? $pil_id;
    $pil_gender = $_POST['pil_gender'] ?? $pil_gender;
    $pil_accomo_type = $_POST['pil_accomo_type'] ?? $pil_accomo_type;
    
    if ($pil_gender !== 'm' && $pil_gender !== 'f') {
        
        echo LBL_ChooseGenderFirst;
        
    } else {
        
        if ($pil_accomo_type == 1) {
            
            // get all suites for this gender
            ?>
            <div class="form-group">
                <label><?= HM_Suite; ?></label>
                
                <?php
                    
                    $suites = suitesToAcc($pil_gender);
                    if (is_array($suites) && count($suites) > 0) {
                        
                        ?>
                        <select name="pacc_suite_id" id="pacc_suite_id" class="form-control select2"
                                onchange="suiteselected(this.value, '<?= $pil_gender; ?>');" required="required">
                            <option value=""><?= LBL_Choose; ?></option>
                            
                            <?php
                                
                                foreach ($suites as $suite) {
                                    
                                    echo '<option value="' . $suite['suite_id'] . '">' . $suite['suite_title'] . '</option>';
                                    
                                }
                            
                            ?>
                        </select>
                        <?php
                        
                    } else {
                        
                        echo '<div style="color:red">' . LBL_NOACCOMAVAILABLE . '</div>';
                        
                    }
                
                ?>

            </div>
            <div id="hallsarea" class="form-group"></div>
            <div id="afterhallsarea" class="form-group"></div>
            <?php
            
        } elseif ($pil_accomo_type == 2 || $pil_accomo_type == 5) {
            
            // get all buildings and floors with rooms for this gender
            ?>
            <div class="form-group">
                <?php
                    if ($pil_accomo_type == 2) echo '<label>' . HM_Building . '</label>';
                    elseif ($pil_accomo_type == 5) echo '<label>' . LBL_Premises . '</label>';
                ?>
                
                
                <?php
                    
                    $buildings = buildingsToAcc($pil_gender, $pil_accomo_type);
                    if (is_array($buildings) && count($buildings) > 0) {
                        
                        ?>
                        <select name="pacc_bld_id" id="pacc_bld_id" class="form-control select2"
                                onchange="bldselected(this.value, '<?= $pil_gender; ?>');" required="required">
                            <option value=""><?= LBL_Choose; ?></option>
                            
                            <?php
                                
                                foreach ($buildings as $building) {
                                    
                                    echo '<option value="' . $building['bld_id'] . '">' . $building['bld_title'] . '</option>';
                                    
                                }
                            
                            ?>
                        </select>
                        <?php
                        
                    } else {
                        
                        echo '<div style="color:red">' . LBL_NOACCOMAVAILABLE . '</div>';
                        
                    }
                
                ?>
            </div>
            <div id="floorsarea" class="form-group"></div>
            <?php
        } elseif ($pil_accomo_type == 3) {
            
            // get tents
            ?>
            <div class="form-group">
                <label><?= HM_Tent; ?></label>
    
                <?php
                    
                    $tents = tentsToAcc($pil_gender);
                    if (is_array($tents) && count($tents) > 0) {
                        
                        ?>
                        <select name="pacc_tent_id" id="pacc_tent_id" class="form-control select2" required="required">
                            <option value=""><?= LBL_Choose; ?></option>
    
                            <?php
                                
                                foreach ($tents as $tent) {
                                    
                                    echo '<option value="' . $tent['tent_id'] . '">' . $tent['tent_title'] . '</option>';
                                    
                                }
                            
                            ?>
                        </select>
                        <?php
                        
                    } else {
                        
                        echo '<div style="color:red">' . LBL_NOACCOMAVAILABLE . '</div>';
                        
                    }
                
                ?>
            </div>
            <?php
            
        } elseif ($pil_accomo_type == 4) {
            
            // get buses
            ?>
            <div class="form-group">
                <label><?= HM_Bus; ?></label>
    
                <?php
                    
                    $buses = busesToAcc();
                    if (is_array($buses) && count($buses) > 0) {
                        
                        ?>
                        <select name="pacc_bus_id" id="pacc_bus_id" class="form-control select2" required="required">
                            <option value=""><?= LBL_Choose; ?></option>
    
                            <?php
                                
                                foreach ($buses as $bus) {
                                    
                                    echo '<option value="' . $bus['bus_id'] . '">' . $bus['bus_title'] . '</option>';
                                    
                                }
                            
                            ?>
                        </select>
                        <?php
                        
                    } else {
                        
                        echo '<div style="color:red">' . LBL_NOACCOMAVAILABLE . '</div>';
                        
                    }
                
                ?>
            </div>
            <?php
            
        }
        
    }


?>
