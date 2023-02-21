<?

	require '../init.php';
	require_once '../db.php';
	$bld_id = $_POST['bld_id'] ?: $_GET['bld_id'];

	if (is_numeric($bld_id)) {
		?>
		<select name="room_floor_id" class="form-control select2">
			<option value=""><?=LBL_Choose;?></option>
			<?
			$sqlf = $db->query("SELECT floor_id, floor_title FROM buildings_floors WHERE floor_active = 1 AND floor_bld_id = $bld_id ORDER BY floor_order");
			while ($rowf = $sqlf->fetch(PDO::FETCH_ASSOC)) {

				echo '<option value="'.$rowf['floor_id'].'" ';
				echo '>'.$rowf['floor_title'].'</option>';

			}
			?>
		</select>
		<?
	}

?>
