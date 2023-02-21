<?

  require '../init.php';
  require_once '../db.php';
  require_once '../functions.php';

  $bld_id = $_POST['bld_id'] ?: $_GET['bld_id'];
  $gender = $_POST['gender'] ?: $_GET['gender'];

  if (is_numeric($bld_id) && $bld_id > 0) {

    // get all floors for this building which have occu
    ?>
    <div class="form-group">
    <label><?=HM_Floor;?></label>
    <select name="pacc_floor_id" id="pacc_floor_id" class="form-control select2" required="required" onchange="floorselected(this.value, '<?=$gender;?>');">
      <option value=""><?=LBL_Choose;?></option>
      <?

      $floors = floorsToAcc($bld_id, $gender);
      if (is_array($floors) && sizeof($floors) > 0) {

        foreach ($floors as $floor) {

          echo '<option value="'.$floor['floor_id'].'">'.$floor['floor_title'].'</option>';

        }

      }

      ?>
    </select>
  </div>
  <div id="roomsarea"></div>
    <?
      }
    ?>
