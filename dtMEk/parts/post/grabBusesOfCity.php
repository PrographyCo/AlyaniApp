<?php
    global $db;
	$city_id = $_REQUEST['city_id'];

	if (is_numeric($city_id)) {
		?>
		<select name="pil_bus_id" class="form-control select2">
			<option value=""><?=LBL_Choose;?></option>
            <?php
			$sql_bus = $db->query("SELECT bus_id, bus_title, bus_seats FROM buses WHERE bus_active = 1 AND bus_city_id = $city_id ORDER BY bus_order");
			while ($row_bus = $sql_bus->fetch(PDO::FETCH_ASSOC)) {

				$used = $db->query("SELECT COUNT(pil_id) FROM pils WHERE pil_bus_id = ".$row_bus['bus_id'])->fetchColumn();
				$remaining = $row_bus['bus_seats'] - $used;

				if ($remaining > 0) {
					echo '<option value="'.$row_bus['bus_id'].'" ';
					echo '>'.$row_bus['bus_title'].'</option>';
				}

			}
			?>
		</select>
        <?php
	}

?>
