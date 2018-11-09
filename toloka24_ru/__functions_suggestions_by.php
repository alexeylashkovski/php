<?php
function api_suggest_address_streets() {
	$arr_suggestions = array();
	$re = filter_input(INPUT_POST, 're');
	$di = filter_input(INPUT_POST, 'di');
	$tn = filter_input(INPUT_POST, 'tn');
	$tt = filter_input(INPUT_POST, 'tt');
	$status = "error";
	$error_text = "";
	$discount_percent = 0;
	if ($tn != "" && $tt != "") {
		$db_link = @connecDB();
		$mysqli_result = mysqli_query($db_link, "SELECT * FROM by_addr_full WHERE region='" . mysqli_real_escape_string($db_link, $re) . "' AND district='" . mysqli_real_escape_string($db_link, $di) . "' AND town_name='" . mysqli_real_escape_string($db_link, $tn) . "' AND town_type='" . mysqli_real_escape_string($db_link, $tt) . "' ORDER BY street_name");
		while ($tmp_row = mysqli_fetch_assoc($mysqli_result)) {
			$tmp_arr = array(
				'id' => $tmp_row["id"],
				'street_name' => $tmp_row["street_name"],
				'street_type' => $tmp_row["street_type"],
				'buildings' => $tmp_row["buildings"],
				'postcode' => $tmp_row["postcode"]
			);
			$arr_suggestions[] = $tmp_arr;
		}
		if (@$db_link) mysqli_close($db_link);
		if ($arr_suggestions) {
			$status = "ok_suggestions";
		}
	}
	$result = array(
		'status' => $status,
		'arr_suggestions' => $arr_suggestions,
		"error_text" => $error_text
	);
	return json_encode($result);
}
//---------------------------------------------
function api_suggest_address_houses() {
	$arr_suggestions = array();
	$arr_zip = array();
	$all_buildings = '';
	$re = filter_input(INPUT_POST, 're');
	$di = filter_input(INPUT_POST, 'di');
	$tn = filter_input(INPUT_POST, 'tn');
	$tt = filter_input(INPUT_POST, 'tt');
	$sn = filter_input(INPUT_POST, 'sn');
	$st = filter_input(INPUT_POST, 'st');
	$status = "error";
	$error_text = "";
	$discount_percent = 0;
	if ($tn != "" && $tt != "" && $sn != "" && $st != "") {
		$db_link = @connecDB();
		$mysqli_result = mysqli_query($db_link, "SELECT * FROM by_addr_full WHERE region='" . mysqli_real_escape_string($db_link, $re) . "' AND district='" . mysqli_real_escape_string($db_link, $di) . "' AND town_name='" . mysqli_real_escape_string($db_link, $tn) . "' AND town_type='" . mysqli_real_escape_string($db_link, $tt) . "' AND street_name='" . mysqli_real_escape_string($db_link, $sn) . "' AND street_type='" . mysqli_real_escape_string($db_link, $st) . "' ORDER BY postcode");
		while ($tmp_row = mysqli_fetch_assoc($mysqli_result)) {
			$postcode = $tmp_row["postcode"];
			if (in_array($postcode, $arr_zip)) {
				//если уже есть такой postcode то просто в записи собираем номера домов
				for ($i = 0; $i < count($arr_zip); $i++)  {
					if ($postcode == $arr_zip[$i]) {
						$arr_suggestions[$i]['buildings'] .= $tmp_row["buildings"];
					}
				}
			} else {
				$arr_zip[] = $postcode;
				$tmp_arr = array(
					'buildings' => $tmp_row["buildings"],
					'postcode' => $postcode
				);
				$arr_suggestions[] = $tmp_arr;
			}
		}
		if (@$db_link) mysqli_close($db_link);
		if ($arr_suggestions) {
			$status = "ok_suggestions";
		}
	}

	for ($i = 0; $i < count($arr_zip); $i++)  {
		if ($arr_suggestions[$i]['buildings'] != '') {
			$all_buildings .= $arr_suggestions[$i]['buildings'];
			$tmp = $arr_suggestions[$i]['buildings'];
			$tmp = order_houses_asc($tmp);
			$tmp = remove_commas($tmp);
			$arr_suggestions[$i]['buildings'] = $tmp;
		}
	}

	$all_buildings = order_houses_asc($all_buildings);
	$all_buildings = remove_commas($all_buildings);

	$result = array(
		'status' => $status,
		'all_buildings' => $all_buildings,
		'arr_suggestions' => $arr_suggestions,
		"error_text" => $error_text
	);
	return json_encode($result);
}
//---------------------------------------------
function order_houses_asc($text) {
	$arr_values = explode(",", $text);
	if (!asort($arr_values)) {
		die("Не могу отсортировать массив домов по возрастанию!");
	}
	return implode(',', $arr_values) . ',';
}
//---------------------------------------------
function remove_commas($text) {
	$text = add_commas($text);
	while(strpos($text, ',,') !== FALSE) {
		$text = str_replace(",,", ",", $text); //убираем двойные запятые
	}
	return $text;
}
//---------------------------------------------
function add_commas($text) {
	if ($text != "") {
		if (substr($text, 0, 1) != ",") {
			$text =  ',' . $text;
		}		
		if (substr($text, strlen($text) - 1) != ",") {
			$text =  $text . ',';
		}		
	}
	if ($text == ',') {
		$text = '';
	}
	return $text;
}
//---------------------------------------------
?>