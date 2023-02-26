<?php
    global $db;
    
    
    $suite_id = $_REQUEST['suite_id'];
    
    if (is_numeric($suite_id) && $suite_id > 0) {
        
        // get all halls for this suite which have occu
        ?>
        <label><?= HM_Hall; ?></label>
        <select name="pacc_hall_id" id="pacc_hall_id" class="form-control select2" onchange="hallselected();"
                required="required">
            <option value=""><?= LBL_Choose; ?></option>
            <?php
                
                $halls = hallsToAcc($suite_id);
                if (is_array($halls) && count($halls) > 0) {
                    
                    foreach ($halls as $hall) {
                        
                        echo '<option value="' . $hall['hall_id'] . '">' . $hall['hall_title'] . '</option>';
                        
                    }
                    
                }
            
            ?>
        </select>
        <?php
    }
?>
