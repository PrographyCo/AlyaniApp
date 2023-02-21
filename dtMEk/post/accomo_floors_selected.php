<?

  require '../init.php';
  require_once '../db.php';
  require_once '../functions.php';

  $floors = $_POST['floors'] ?: $_GET['floors'];
  $gender = $_POST['gender'] ?: $_GET['gender'];

  if (is_array($floors) && sizeof($floors)) {

    echo '<select name="room_id[]" id="room_id[]" class="form-control select2" multiple="multiple" data-placeholder="'.LBL_ChooseRooms.'" onchange="calcAvailAccomo();">';
        if ($gender) $sqlrooms = $db->query("SELECT * FROM buildings_rooms WHERE room_active = 1 AND room_floor_id IN (".implode(",", $floors).") AND room_gender = '$gender' ORDER BY room_title");
        else $sqlrooms = $db->query("SELECT * FROM buildings_rooms WHERE room_active = 1 AND room_floor_id IN (".implode(",", $floors).") ORDER BY room_title");

        while ($rowr = $sqlrooms->fetch(PDO::FETCH_ASSOC)){
          echo '<option value="'.$rowr['room_id'].'" ';
          echo '>
          '.$rowr['room_title'].'
          </option>';
        }
    echo '</select>';

  }
?>
