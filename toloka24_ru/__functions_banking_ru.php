<?php
//---------------------------------------------
function rest_RegisterOrder($id_order, $pay_try_num = '') {
	global $config_Domain;
	$api_base_url = 'https://securepayments.sberbank.ru/payment/rest/';
	$action = 'register.do';
	$expirationDate = date('Y-m-d', strtotime('+20 minutes')) . 'T' . date('H:i:s', strtotime('+20 minutes'));
	$db_link = @connecDB();
	$mysqli_result = mysqli_query($db_link, "SELECT * FROM subscribe WHERE id_order='$id_order'");
	while ($tmp_row = mysqli_fetch_assoc($mysqli_result)) {
		$created_datetime = $tmp_row["created_datetime"];
		$created_datetime = str_replace(" ", "T", $created_datetime); //переводим время из вида 2018-05-24 16:45:14 в 2018-05-24T16:45:14
		$email = $tmp_row["email"];
		$tel = $tmp_row["tel"];
		$tel = preg_replace("/[^0-9]/", '', $tel); //оставляем в телефоне только цифры
		$fio = $tmp_row["fio"];
		$summaNDS = $tmp_row["summaNDS"];
		$price_of_1_magazine = $tmp_row["price_of_1_magazine"];
		$monthes_num = $tmp_row["monthes"];
		$magazines_num = $tmp_row["month_releases_num"] * $monthes_num;
		
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
		
		break;
	}
	if (@$mysqli_result) mysqli_free_result($mysqli_result);
	if (@$db_link) mysqli_close($db_link);


	if ($pay_try_num) {
		$id_order = $id_order . $pay_try_num;
	}

	$params = array(
		'orderNumber' => $id_order,
		'userName' => '***',
		'password' => '***',
		'amount' => $price_to_pay,
		'pageView' => 'DESKTOP',
		'expirationDate' => $expirationDate,
		'returnUrl' => $config_Domain . '/finish/',
		'failUrl' => $config_Domain . '/fail/',
		'language' => 'ru',
		'taxSystem' => '0',
		'orderBundle' => json_encode(
			array(
				'orderCreationDate' => $created_datetime,
				'customerDetails' => array(
					'email' => $email,
					'phone' => $tel,
					'contact' => $fio
				),
				'cartItems' => array(
					'items' => array(
						array(
							'positionId' => "1",
							'name' => "Подписка на " . $monthes_num . " месяцев на «" . $magazine_name . "»",
							'itemAmount' => $price_to_pay,
							'quantity' =>  array(
								'value' => 1,
								'measure' => "подписка"
							),
							'itemCode' => $magazine_id,
							'tax' => array(
								'taxType' => 2,
								'taxSum' => $taxSum
							),
							'itemPrice' => $price_to_pay
						)
					)
				)
			))
	);
	$url = $api_base_url . $action . "?" . http_build_query($params);
	$registerOrderResult = @json_decode(file_get_contents($url), true);

	return $registerOrderResult;
}
//---------------------------------------------
function rest_getOrderStatusExtended() {
	$orderStatusResult = array();
	$id_order = filter_input(INPUT_GET, 'orderId');
	if ($id_order != '') {
		$api_base_url = 'https://securepayments.sberbank.ru/payment/rest/';

		$action = 'getOrderStatusExtended.do';
		$params = array(
			'orderId' => $id_order,
			'userName' => 'toloka24-api',
			'password' => 'Njkjrf24!'
		);
		$url = $api_base_url . $action . "?" . http_build_query($params);
		$orderStatusResult = @json_decode(file_get_contents($url), true);
	}
	return $orderStatusResult;
}
//---------------------------------------------
?>