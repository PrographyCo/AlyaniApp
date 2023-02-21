<?

  require '../init.php';
  require_once '../db.php';
  require_once '../functions.php';

  $city_id = $_POST['city_id'];
?>

      <label><?=LBL_BusNumber;?></label>
      <select name="bus_id" id="bus_id" class="form-control select2">
        <option value="0">
          <?=LBL_Choose;?>
        </option>
        <?
          if ($city_id > 0) $sqlmore = " AND bus_city_id = $city_id";
          $sqlbuses = $db->query("SELECT * FROM buses WHERE bus_active = 1 $sqlmore ORDER BY bus_title");
          while ($rowb = $sqlbuses->fetch(PDO::FETCH_ASSOC)){
            echo '<option value="'.$rowb['bus_id'].'" ';
            echo '>
            '.$rowb['bus_title'].'
            </option>';
          }
        ?>

      </select>
