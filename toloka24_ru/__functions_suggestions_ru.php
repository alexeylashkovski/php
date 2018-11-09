<?php
//---------------------------------------------
function api_check_telephone() {
	$tel_num = filter_input(INPUT_POST, 'tel_num');
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "https://dadata.ru/api/v2/clean/phone");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, "[ \"$tel_num\" ]");
	curl_setopt($ch, CURLOPT_POST, 1);
	$headers = array();
	$headers[] = "Content-Type: application/json";
	$headers[] = "Authorization: Token ***";
	$headers[] = "X-Secret: ***";
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	$result = curl_exec($ch);
	if (curl_errno($ch)) {
		$result = 'Error:' . curl_error($ch);
	}
	curl_close ($ch);

	return $result;
}
//---------------------------------------------
function api_check_address() {
	$full_adress_string = filter_input(INPUT_POST, 'full_adress_string');
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "https://dadata.ru/api/v2/clean/address");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, "[ \"$full_adress_string\" ]");
	curl_setopt($ch, CURLOPT_POST, 1);
	$headers = array();
	$headers[] = "Content-Type: application/json";
	$headers[] = "Authorization: Token ***";
	$headers[] = "X-Secret: ***";
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	$result = curl_exec($ch);
	if (curl_errno($ch)) {
		$result = 'Error:' . curl_error($ch);
	}
	curl_close ($ch);

	return $result;
}
//---------------------------------------------
?>