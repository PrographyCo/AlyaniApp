<?

  require '../init.php';
  require_once '../db.php';
  require_once '../functions.php';

  $cities = $_POST['cities'] ?: $_GET['cities'];

  if (is_array($cities) && sizeof($cities)) {

    ?>

    <div class="form-group">
      <label><?=LBL_BusNumber;?></label>
      <select name="bus_id[]" id="bus_id[]" class="form-control select2" multiple="multiple" onchange="calcAvailAccomo();">
        <?
          $sqlbuses = $db->query("SELECT * FROM buses WHERE bus_active = 1 AND bus_city_id IN (".implode(',', $cities).") ORDER BY bus_title");
          while ($rowb = $sqlbuses->fetch(PDO::FETCH_ASSOC)){
            echo '<option value="'.$rowb['bus_id'].'" ';
            echo '>
            '.$rowb['bus_title'].'
            </option>';
          }
        ?>

      </select>
    </div>

    <? } ?>
