<?php
//---------------------------------------------
function rest_RegisterOrder($id_order, $pay_try_num = '') {
	global $config_Domain;
	global $config_bepaid_by_customer_id, $config_bepaid_by_private_key;
	$api_base_url = 'https://' . $config_bepaid_by_customer_id . ':' . $config_bepaid_by_private_key . '@' . 'checkout.bepaid.by/ctp/api/checkouts';
	$expirationDate = date('Y-m-d', strtotime('+20 minutes')) . 'T' . date('H:i:s', strtotime('+20 minutes'));

	$db_link = @connecDB();
	$mysqli_result = mysqli_query($db_link, "SELECT * FROM subscribe WHERE id_order='$id_order'");
	while ($tmp_row = mysqli_fetch_assoc($mysqli_result)) {
		$region = $tmp_row["region"];
		$created_datetime = $tmp_row["created_datetime"];
		$created_datetime = str_replace(" ", "T", $created_datetime); //переводим время из вида 2018-05-24 16:45:14 в 2018-05-24T16:45:14
		$email = $tmp_row["email"];
		$tel = $tmp_row["tel"];
		$fio = $tmp_row["fio"];
		$first_name = substr($fio, 0, strpos($fio, ' '));
		$last_name = substr($fio, strpos($fio, ' ') + 1);
		$last_name = substr($last_name, 0, strpos($last_name, ' '));
		$summaNDS = $tmp_row["summaNDS"];
		$price_of_1_magazine = $tmp_row["price_of_1_magazine"];
		$monthes_num = $tmp_row["monthes"];
		$magazines_num = $tmp_row["month_releases_num"] * $monthes_num;
		$magazines_num_text = $magazines_num . ' номер';
		if ($magazines_num < 5) {
			$magazines_num_text .= "а";
		} else {
			$magazines_num_text .= "ов";
		}
		
		$price = $tmp_row["price"];
		$price_discounted = $tmp_row["price_discounted"];
		if ($price_discounted) {
			$price_to_pay = $price_discounted;
		} else {
			$price_to_pay = $price;
		}
		$price_to_pay = ($price_to_pay * 100);

		$taxSum = (float)$summaNDS*100;

		$magazine_id = $tmp_row["magazine_id"];
		$arr_magazine_info = get_magazine_info($magazine_id);
		$magazine_name = remove_br($arr_magazine_info["name"]);

		$city = $tmp_row["city"];
		$street = $tmp_row["street"];
		$house = $tmp_row["house"];
		if ($region != '') {
			$address_text = capitalize($region) . ', ';
		} else {
			$address_text = '';
		}
		$address_text .= $tmp_row['street'];
		$address_text .= ', д. ' . $tmp_row['house'];
		if ($tmp_row['korpus']) {
			$address_text .= ' к. ' . $tmp_row['korpus'];
		}
		if ($tmp_row['flat']) {
			$address_text .= ', кв. ' . $tmp_row['flat'];
		}
		$zip = $tmp_row["zip"];
		
		break;
	}
	if (@$mysqli_result) mysqli_free_result($mysqli_result);
	if (@$db_link) mysqli_close($db_link);


	if ($pay_try_num) {
		$id_order = $id_order . $pay_try_num;
	}

	$params = array(
		"checkout" => array(		
			"version" => "2.1",
			"transaction_type" => "payment",
			"settings" => array(
				"success_url" => 'http://toloka24.by/finish/',
				"fail_url" => 'http://toloka24.by/fail/',
				"decline_url" => 'http://toloka24.by/decline/',
				"language" => 'ru',
				"customer_fields" => array(
					"visible" => array("first_name", "last_name"),
					"read_only" => array("email")
				),
			),
			"order" => array(
				"currency" => 'BYN',
				"amount" => $price_to_pay,
				"description" => "Подписка на " . $monthes_num . " месяцев ($magazines_num_text) на «" . $magazine_name . "»",
				"expired_at" => $expirationDate,
				"tracking_id" => $id_order
			),
			"customer" => array(
				"phone" => $tel,
				"email" => $email,
				"first_name" => $first_name,
				"last_name" => $last_name,
				"zip" => $zip,
				"country" => "BY",
				"city" => $city,
				"address" => $address_text
			)
		)
	);	

	$myCurl = curl_init();
	curl_setopt_array($myCurl, array(
		CURLOPT_URL => $api_base_url,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_POST => true,
		CURLOPT_HTTPHEADER => array("Content-Type: application/json", "Accept: application/json"),
		CURLOPT_POSTFIELDS => json_encode($params)
	));

	$registerOrderResult = @json_decode(curl_exec($myCurl), true);
	curl_close($myCurl);
	return $registerOrderResult;
}
//---------------------------------------------
function rest_getOrderStatusExtended() {
	global $config_bepaid_by_customer_id, $config_bepaid_by_private_key;
	$id_order = filter_input(INPUT_GET, 'token');
	$orderStatusResult = array();
	if ($id_order != '') {
		$api_base_url = 'https://' . $config_bepaid_by_customer_id . ':' . $config_bepaid_by_private_key . '@' . 'checkout.bepaid.by/ctp/api/checkouts/';
		$url = $api_base_url . $id_order;
		$orderStatusResult = @json_decode(file_get_contents($url), true);
	}
	return $orderStatusResult;
}
//---------------------------------------------
function rest_getOrderStatusExtended2($uid_order) {
	global $config_bepaid_by_customer_id, $config_bepaid_by_private_key;
	$orderStatusResult = array();
	$credit_card_num = "";
	if ($uid_order != '') {
		$api_base_url = 'https://' . $config_bepaid_by_customer_id . ':' . $config_bepaid_by_private_key . '@' . 'gateway.bepaid.by/transactions/';
		$url = $api_base_url . $uid_order;
		$orderStatusResult = @json_decode(file_get_contents($url), true);
		$credit_card_num = $orderStatusResult["transaction"]["credit_card"]["bin"] . '_' .  $orderStatusResult["transaction"]["credit_card"]["last_4"];
	}
	return $credit_card_num;
}
//---------------------------------------------
?>