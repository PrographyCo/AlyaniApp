<?php
    global $db;


  $floor_id = $_REQUEST['floor_id'];
  $gender = $_REQUEST['gender'];

  if (is_numeric($floor_id) && $floor_id > 0) {

    // get all floors for this building which have occu
    ?>
    <div class="form-group">
    <label><?=HM_Room;?></label>
    <select name="pacc_room_id" id="pacc_room_id" class="form-control select2" required="required">
      <option value=""><?=LBL_Choose;?></option>
      <?php

      $rooms = roomsToAcc($floor_id, $gender);
      if (is_array($rooms) && count($rooms) > 0) {

        foreach ($rooms as $room) {

          echo '<option value="'.$room['room_id'].'">'.$room['room_title'].'</option>';

        }

      }

      ?>
    </select>
  </div>
    <?
      }
    ?>
