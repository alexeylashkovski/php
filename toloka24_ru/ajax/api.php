<?php
	require_once('../__functions.php');
	$arr_acts = array(
		"check_promocode",
		"check_telephone",
		"check_address"
	);
	$api_action = filter_input(INPUT_POST, 'act');
	if (!$api_action) $api_action = filter_input(INPUT_GET, 'act');
	if ($api_action && in_array($api_action, $arr_acts)) {
		if ($_SERVER['REQUEST_METHOD'] == "POST") {
			if ($api_action == "check_promocode") {
				echo api_check_promocode();
				exit;
			}
			if ($api_action == "check_telephone") {
				echo api_check_telephone();
				exit;
			}
			if ($api_action == "check_address") {
				echo api_check_address();
				exit;
			}
		} else {
			if ($api_action == "check_telephone") {
				echo api_check_telephone();
				exit;
			}
		}
	}
	if (@$mysql_result) mysql_free_result($mysql_result);
	if (@$db_link) mysql_close($db_link);
?>