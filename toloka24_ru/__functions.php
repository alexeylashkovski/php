<?php
	require_once 'config.php';
	require_once '__functions_suggestions_' . $config_DomainEnd . '.php';
	require_once '__functions_banking_' . $config_DomainEnd . '.php';

	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;
	require_once '_libs/PHPMailer-master/src/Exception.php';
	require_once '_libs/PHPMailer-master/src/PHPMailer.php';
	require_once '_libs/PHPMailer-master/src/SMTP.php';
	require('_dp_dadata_client.php');
	use Dadata\DadataClient as DadataClient;
	saveOuterReffererInSession();

	date_default_timezone_set('Europe/Moscow');	
	@ob_end_flush();
	@ob_implicit_flush(1);
	@set_time_limit(0);

	$post_success_info = "";
	$error_description	= "";

	$flagUserLoggedIn = false;
	$logged_in = check_logged_in();
	if ( !empty($logged_in) ) {
		$flagUserLoggedIn = true;
		$nameUserLoggedIn = $logged_in["name"];
	}
	$flagAgentLoggedIn = false;
	$agent_logged_in = check_agent_logged_in();
	if ( !empty($agent_logged_in) ) {
		$flagAgentLoggedIn = true;
		$nameAgentLoggedIn = $agent_logged_in["name"];
	}
//---------------------------------------------
function ucfirst_utf8($str) {
	return mb_substr(mb_strtoupper($str, 'utf-8'), 0, 1, 'utf-8') . mb_substr($str, 1, mb_strlen($str)-1, 'utf-8');
}
//---------------------------------------------
function capitalize($str) {
	return ucfirst_utf8(mb_strtolower($str));
}
//---------------------------------------------
function saveOuterReffererInSession() {
	session_start();
	if (isset($_SERVER['HTTP_REFERER'])) {
		$our_domain = strtolower($_SERVER["SERVER_NAME"]);
		$our_domain = str_replace("www.", "", $our_domain);
		$referer_domain = strtolower(parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST));
		$referer_domain = str_replace("www.", "", $referer_domain);
		if ($referer_domain != $our_domain) {
			$_SESSION['referer'] = $_SERVER['HTTP_REFERER'];
		}
	}
}
//---------------------------------------------
function connecDB($new_link = "") {
	global $config_DBDomain, $config_DBName, $config_DBUser, $config_DBPassword;
	$db_link = @mysqli_connect($config_DBDomain, $config_DBUser, $config_DBPassword, $config_DBName) or die ("Couldn't connect to MySQL.");
	mysqli_set_charset($db_link, 'utf8');
	return $db_link;
}
//---------------------------------------------
function get_all_magazine_info() {
	$arr_magazines = array();
	$db_link = @connecDB();
	$mysqli_result = mysqli_query($db_link, "SELECT * FROM magazine WHERE deleted='0' ORDER BY list_order_value DESC");
	while ($tmp_row = mysqli_fetch_assoc($mysqli_result)) {
		$arr_categories = array();
		$magazine_id = $tmp_row["id"];
		$mysqli_result2 = mysqli_query($db_link, "SELECT category.id, magazines_categories.category_id FROM category LEFT JOIN magazines_categories ON magazines_categories.magazine_id='$magazine_id' AND magazines_categories.category_id=category.id AND category.deleted=0 AND magazines_categories.deleted=0");
		while ($tmp_row2 = mysqli_fetch_assoc($mysqli_result2)) {
			if ($tmp_row2["category_id"]) {
				$arr_categories[] = array("id"=>$tmp_row2["id"]);
			}
		}
		$tmp_arr = array("id"=>$tmp_row["id"], "name"=>$tmp_row["name"], "url"=>$tmp_row["url"], "post_index"=>$tmp_row["post_index"], "list_order_value"=>$tmp_row["list_order_value"], "not_show_on_popular"=>$tmp_row["not_show_on_popular"], "categories"=>$arr_categories);
		$arr_magazines[] = $tmp_arr;
	}

	if (@$mysqli_result) mysqli_free_result($mysqli_result);
	if (@$db_link) mysqli_close($db_link);
	return $arr_magazines;
}
//---------------------------------------------
function get_all_promocodes_info() {
	$arr_promocodes = array();
	$db_link = @connecDB();
	$only_actual_sql = "";
	$mysqli_result = mysqli_query($db_link, "SELECT * FROM promocode");
	$now = date('Y-m-d H:i:s');
	while ($tmp_row = mysqli_fetch_assoc($mysqli_result)) {
		$flagPromocodeCompleted = false;
		if ($tmp_row["datetime_end"] < $now) {
			$flagPromocodeCompleted = true;
		}
		$tmp_arr = array("id"=>$tmp_row["id"], "code_text"=>$tmp_row["code_text"], "code_name"=>$tmp_row["code_name"], "discount_percent"=>$tmp_row["discount_percent"], "for_all_magazines"=>$tmp_row["for_all_magazines"], "datetime_start"=>$tmp_row["datetime_start"], "datetime_end"=>$tmp_row["datetime_end"], "datetime_created"=>$tmp_row["datetime_created"], "deleted"=>$tmp_row["deleted"], "completed"=>$flagPromocodeCompleted);
		$arr_promocodes[] = $tmp_arr;
	}
	if (@$mysqli_result) mysqli_free_result($mysqli_result);
	if (@$db_link) mysqli_close($db_link);
	return $arr_promocodes;
}
//---------------------------------------------
function get_all_actual_promocodes_info() {
	$arr_promocodes = array();
	$db_link = @connecDB();
	$now = date('Y-m-d H:i:s');
	$mysqli_result = mysqli_query($db_link, "SELECT * FROM promocode WHERE datetime_start<='$now' AND datetime_end>='$now' AND deleted='0'");
	while ($tmp_row = mysqli_fetch_assoc($mysqli_result)) {
		$tmp_arr = array("id"=>$tmp_row["id"], "code_text"=>$tmp_row["code_text"], "code_name"=>$tmp_row["code_name"], "discount_percent"=>$tmp_row["discount_percent"], "for_all_magazines"=>$tmp_row["for_all_magazines"], "datetime_start"=>$tmp_row["datetime_start"], "datetime_end"=>$tmp_row["datetime_end"]);
		$arr_promocodes[] = $tmp_arr;
	}
	if (@$mysqli_result) mysqli_free_result($mysqli_result);
	if (@$db_link) mysqli_close($db_link);
	return $arr_promocodes;
}
//---------------------------------------------
function check_magazine_url_exist($magazine_url) {
	$arr_magazine_info = array();
	if ($magazine_url != '') {
		$magazine_url = str_replace("magazine/", "", $magazine_url);
		$arr_magazine_info = get_magazine_info('', $magazine_url);
	}
	return $arr_magazine_info;
}
//---------------------------------------------
function check_group_url_exist($group_url) {
	$arr_group_info = array();
	if ($group_url != '') {
		$group_url = str_replace("group/", "", $group_url);
		$arr_group_info = get_group_info('', $group_url);
	}
	return $arr_group_info;
}
//---------------------------------------------
function get_magazine_info($magazine_id = '', $magazine_url = '') {
	$arr_magazine_info = array();
	if ($magazine_id == '') {
		$magazine_id =  filter_input(INPUT_POST, 'magazine');
	}
	if ($magazine_id != '' || $magazine_url != '') {
		$db_link = @connecDB();
		if ($magazine_id != '') {
			$query = "SELECT * FROM magazine WHERE id='" . mysqli_real_escape_string($db_link, $magazine_id) ."' AND deleted='0'";
		} else {
			$query = "SELECT * FROM magazine WHERE url='" . mysqli_real_escape_string($db_link, $magazine_url) . "' AND deleted='0'";
		}
		$mysqli_result = mysqli_query($db_link, $query);
		while ($tmp_row = mysqli_fetch_assoc($mysqli_result)) {
			$magazine_id = $tmp_row["id"];
			$arr_magazine_info["id"] = $tmp_row["id"];
			$arr_magazine_info["url"] = $tmp_row["url"];
			$arr_magazine_info["post_index"] = $tmp_row["post_index"];
			$arr_magazine_info["name"] = $tmp_row["name"];
			$arr_magazine_info["subtitle"] = $tmp_row["subtitle"];
			$arr_magazine_info["description"] = $tmp_row["description"];
			$arr_magazine_info["month_releases_num"] = $tmp_row["month_releases_num"];
			$arr_magazine_info["page_size"] = $tmp_row["page_size"];
			$arr_magazine_info["price_6_monthes"] = $tmp_row["price_6_monthes"];
			$arr_magazine_info["price_12_monthes"] = $tmp_row["price_12_monthes"];
			$arr_magazine_info["stavka_nds"] = $tmp_row["stavka_nds"];
			$arr_magazine_info["list_order_value"] = $tmp_row["list_order_value"];
			$arr_magazine_info["not_show_on_popular"] = $tmp_row["not_show_on_popular"];
			$arr_magazine_info["html_title"] = $tmp_row["html_title"];
			$arr_magazine_info["meta_description"] = $tmp_row["meta_description"];
			$arr_magazine_info["meta_keywords"] = $tmp_row["meta_keywords"];
			break;
		}
		$arr_categories = array();
		$mysqli_result = mysqli_query($db_link, "SELECT category.name, category.id, magazines_categories.category_id FROM category LEFT JOIN magazines_categories ON magazines_categories.magazine_id='" . mysqli_real_escape_string($db_link, $magazine_id) . "' AND magazines_categories.category_id=category.id AND category.deleted=0 AND magazines_categories.deleted=0");
		while ($tmp_row = mysqli_fetch_assoc($mysqli_result)) {
			$tmp_arr[] = array("name"=>$tmp_row["name"], "id"=>$tmp_row["id"], "category_id"=>$tmp_row["category_id"]);
		}
		$arr_magazine_info["categories"] = $tmp_arr;
		if (@$mysqli_result) mysqli_free_result($mysqli_result);
		if (@$db_link) mysqli_close($db_link);
	}
	return $arr_magazine_info;
}
//---------------------------------------------
function get_magazine_categories() {
	$db_link = @connecDB();
	$arr_categories = array();
	$mysqli_result = mysqli_query($db_link, "SELECT name, id FROM category WHERE deleted=0");
	while ($tmp_row = mysqli_fetch_assoc($mysqli_result)) {
		$tmp_arr[] = array("name"=>$tmp_row["name"], "id"=>$tmp_row["id"]);
	}
	$arr_magazine_info["categories"] = $tmp_arr;
	return $arr_magazine_info;
}
//---------------------------------------------
function set_magazine_info() {
	global $error_description;
	$arr_magazine_info = array();
	$act = filter_input(INPUT_POST, 'act');
	if ($act == 'new_magazine' || $act == 'edit_magazine') {
		$name = filter_input(INPUT_POST, 'name');
		$url = filter_input(INPUT_POST, 'url');
		$post_index = filter_input(INPUT_POST, 'post_index');
		$subtitle = filter_input(INPUT_POST, 'subtitle');
		$description = filter_input(INPUT_POST, 'description');
		$page_size = filter_input(INPUT_POST, 'page_size');
		$month_releases_num = filter_input(INPUT_POST, 'month_releases_num');
		if ($month_releases_num == "") {
			$month_releases_num = 1;
		}
		$price_6_monthes = filter_input(INPUT_POST, 'price_6_monthes');
		if ($price_6_monthes == "") {
			$price_6_monthes = 0;
		}
		$price_12_monthes = filter_input(INPUT_POST, 'price_12_monthes');
		if ($price_12_monthes == "") {
			$price_12_monthes = 0;
		}
		$stavka_nds = filter_input(INPUT_POST, 'stavka_nds');
		if ($stavka_nds == "") {
			$stavka_nds = 10;
		}
		$list_order_value = filter_input(INPUT_POST, 'list_order_value');
		if ($list_order_value == "") {
			$list_order_value = 0;
		}
		$not_show_on_popular = filter_input(INPUT_POST, 'not_show_on_popular');
		if ($not_show_on_popular) {
			$not_show_on_popular = 1;
		} else {
			$not_show_on_popular = 0;
		}
		$html_title = filter_input(INPUT_POST, 'html_title');
		$meta_description = filter_input(INPUT_POST, 'meta_description');
		$meta_keywords = filter_input(INPUT_POST, 'meta_keywords');
		$arr_magazine_categories = $_POST['categories'];
		$db_link = @connecDB();
		if ($act == 'edit_magazine') {
			$id = filter_input(INPUT_POST, 'id');
			$tmp_query = "UPDATE magazine SET 	name='" . mysqli_real_escape_string($db_link, $name) . "', 
												url='" . mysqli_real_escape_string($db_link, $url) . "', 
												post_index='" . mysqli_real_escape_string($db_link, $post_index) . "', 
												subtitle='" . mysqli_real_escape_string($db_link, $subtitle) . "', 
												description='" . mysqli_real_escape_string($db_link, $description) . "', 
												month_releases_num='" . mysqli_real_escape_string($db_link, $month_releases_num) . "', 
												page_size='" . mysqli_real_escape_string($db_link, $page_size) . "', 
												price_6_monthes='" . mysqli_real_escape_string($db_link, $price_6_monthes) . "', 
												price_12_monthes='" . mysqli_real_escape_string($db_link, $price_12_monthes) . "', 
												stavka_nds='" . mysqli_real_escape_string($db_link, $stavka_nds) . "', 
												list_order_value='" . mysqli_real_escape_string($db_link, $list_order_value) . "', 
												not_show_on_popular='" . mysqli_real_escape_string($db_link, $not_show_on_popular) . "', 
												html_title='" . mysqli_real_escape_string($db_link, $html_title) . "', 
												meta_description='" . mysqli_real_escape_string($db_link, $meta_description) . "', 
												meta_keywords='" . mysqli_real_escape_string($db_link, $meta_keywords) . "' 
												WHERE id='" . mysqli_real_escape_string($db_link, $id) . "' AND deleted='0'";
		}
		if ($act == 'new_magazine') {
			$tmp_query = "INSERT INTO magazine (name, 
												url, 
												post_index, 
												subtitle, 
												description, 
												month_releases_num, 
												page_size, 
												price_6_monthes, 
												price_12_monthes, 
												stavka_nds, 
												list_order_value, 
												not_show_on_popular, 
												html_title, 
												meta_description, 
												meta_keywords) VALUES('" . 
												mysqli_real_escape_string($db_link, $name) . "', '" . 
												mysqli_real_escape_string($db_link, $url) . "', '" . 
												mysqli_real_escape_string($db_link, $post_index) . "', '" . 
												mysqli_real_escape_string($db_link, $subtitle) . "', '" . 
												mysqli_real_escape_string($db_link, $description) . "', '" . 
												mysqli_real_escape_string($db_link, $month_releases_num) . "', '" . 
												mysqli_real_escape_string($db_link, $page_size) . "', '" . 
												mysqli_real_escape_string($db_link, $price_6_monthes) . "', '" . 
												mysqli_real_escape_string($db_link, $price_12_monthes) . "', '" . 
												mysqli_real_escape_string($db_link, $stavka_nds) . "', '" . 
												mysqli_real_escape_string($db_link, $list_order_value) . "', '" . 
												mysqli_real_escape_string($db_link, $not_show_on_popular) . "', '" . 
												mysqli_real_escape_string($db_link, $html_title) . "', '" . 
												mysqli_real_escape_string($db_link, $meta_description) . "', '" . 
												mysqli_real_escape_string($db_link, $meta_keywords) . "')";
		}
		if ($tmp_query != '') {
			$mysqli_result = mysqli_query($db_link, $tmp_query);
			if (!$mysqli_result) {
				echo "DB error: " . mysqli_error($db_link);
			} else {
				if ($act == 'new_magazine') {
					$id = mysqli_insert_id($db_link);
				}
				save_magazine_cover_pic($id);
				if (update_magazine_categories($id, $arr_magazine_categories)) {
					return $id;
				} else {
					$error_description = "У журнала не выбрана ни одна категория!";
					return true;
				}
			}
		}
		if (@$db_link) mysqli_close($db_link);
	}
	return false;
}
//---------------------------------------------
function update_magazine_categories($magazine_id, $arr_magazine_categories) {
	$flagAllCategoriesEmpty = true;
	if ($arr_magazine_categories == NULL) {
		$arr_magazine_categories = array();
	}
	$db_link = @connecDB();
	$mysqli_result = mysqli_query($db_link, "SELECT id FROM category WHERE deleted=0");
	while ($tmp_row = mysqli_fetch_assoc($mysqli_result)) {
		$arr_categories[] = $tmp_row["id"];
	}
	foreach ($arr_categories as $category_id) {
		if (in_array($category_id, $arr_magazine_categories)) {
			$flagAllCategoriesEmpty = false;
			$mysqli_result = mysqli_query($db_link, "INSERT INTO magazines_categories (magazine_id, category_id, deleted, deleted_datetime) VALUES('$magazine_id', '$category_id', '0', NULL) ON DUPLICATE KEY UPDATE deleted='0', deleted_datetime=NULL");
		} else {
			$row_id = 0;
			$mysqli_result = mysqli_query($db_link, "SELECT * FROM magazines_categories WHERE magazine_id='$magazine_id' AND category_id='$category_id'");
			while ($tmp_row = mysqli_fetch_assoc($mysqli_result)) {
				$row_id = $tmp_row["id"];
				break;
			}
			if ($row_id) {
				$mysqli_result = mysqli_query($db_link, "UPDATE magazines_categories SET deleted='1', deleted_datetime='" . date('Y-m-d H:i:s') ."' WHERE id='$row_id'");
			}
		}
	}
	if (@$db_link) mysqli_close($db_link);
	if ($flagAllCategoriesEmpty) {
		return false;
	}
	return true;
}
//---------------------------------------------
function save_magazine_cover_pic($magazine_id) {
	global $error_description;
	if ( $_FILES['picture']['error'] != 4 && $_FILES['picture']['size'] != 0) {	
		if ( $_FILES['picture']['error'] != 0 && ($_FILES['picture']['error'] != 4 && $_FILES['picture']['size'] != 0) ) {
			$error_description = "Произошла ошибка при загрузке картинки на сервер";
		} elseif ( $_FILES['picture']['type'] != "image/jpeg" ) {
			$error_description = "Ошибка! Фотография должна быть формата jpg или jpeg!";
		} elseif ( $_FILES['picture']['size'] > 2*1048576 ) {
			$error_description = "Ошибка! Размер фотографии больше 2 Мб.";
		} else {
			$magazine_folder = check_or_create_magazine_dirs($magazine_id);
			if (!@copy($_FILES['picture']['tmp_name'], $magazine_folder . "/cover_ORIGINAL.jpg")) {
				$error_description = "Произошла ошибка при копировании временного файла на сервере!";
			} else {
				resize_magazin_cover_pic($magazine_id);
			}			
		}
	}
}
//---------------------------------------------
function save_group_cover_pic($group_id) {
	global $error_description;
	if ( $_FILES['picture']['error'] != 4 && $_FILES['picture']['size'] != 0) {	
		if ( $_FILES['picture']['error'] != 0 && ($_FILES['picture']['error'] != 4 && $_FILES['picture']['size'] != 0) ) {
			$error_description = "Произошла ошибка при загрузке картинки на сервер";
		} elseif ( $_FILES['picture']['type'] != "image/jpeg" ) {
			$error_description = "Ошибка! Фотография должна быть формата jpg или jpeg!";
		} elseif ( $_FILES['picture']['size'] > 2*1048576 ) {
			$error_description = "Ошибка! Размер фотографии больше 2 Мб.";
		} else {
			$group_folder = check_or_create_group_dirs($group_id);
			if (!@copy($_FILES['picture']['tmp_name'], $group_folder . "/cover_ORIGINAL.jpg")) {
				$error_description = "Произошла ошибка при копировании временного файла на сервере!";
			} else {
				resize_group_cover_pic($group_id);
			}			
		}
	}
}
//---------------------------------------------
function check_or_create_magazine_dirs($magazine_id) {
	$magazine_folder = '';
	if ($magazine_id != "") {
		$magazine_folder = path_magazine_dir_pics($magazine_id);
		if (!is_dir($magazine_folder)) {
			if (!mkdir($magazine_folder, 0777)) {
				die("Error create magazine dir");
			}
		}	
	}
	return $magazine_folder;
}
//---------------------------------------------
function check_or_create_group_dirs($group_id) {
	$group_folder = '';
	if ($group_id != "") {
		$group_folder = path_group_dir_pics($group_id);
		if (!is_dir($group_folder)) {
			if (!mkdir($group_folder, 0777)) {
				die("Error create group dir");
			}
		}	
	}
	return $group_folder;
}
//---------------------------------------------
function resize_magazin_cover_pic($magazine_id) {
	require_once '_libs/image-toolkit/AcImage.php';

	$original_pic = path_magazine_dir_pics($magazine_id) . "/cover_ORIGINAL.jpg";
	$resized_pic =  path_magazine_dir_pics($magazine_id) . "/cover.jpg";
	
	AcImage::setRewrite(true);
	AcImage::setQuality(90);
	$image = AcImage::createImage($original_pic);
	$image->resizeByHeight(600)->save($resized_pic);
}
//---------------------------------------------
function resize_group_cover_pic($group_id) {
	require_once '_libs/image-toolkit/AcImage.php';

	$original_pic = path_group_dir_pics($group_id) . "/cover_ORIGINAL.jpg";
	$resized_pic =  path_group_dir_pics($group_id) . "/cover.jpg";
	
	AcImage::setRewrite(true);
	AcImage::setQuality(90);
	$image = AcImage::createImage($original_pic);
	$image->resizeByHeight(600)->save($resized_pic);
}
//---------------------------------------------
function path_magazine_dir_pics($magazine_id) {
	return "../_img/magazine/$magazine_id";
}
//---------------------------------------------
function path_group_dir_pics($group_id) {
	return "../_img/group/$group_id";
}
//---------------------------------------------
function full_web_path_magazine_cover($magazine_id) {
	return 'http://' . $_SERVER['HTTP_HOST'] . "/_img/magazine/$magazine_id/cover.jpg";
}
//---------------------------------------------
function full_web_path_group_cover($group_id) {
	return 'http://' . $_SERVER['HTTP_HOST'] . "/_img/group/$group_id/cover.jpg";
}
//---------------------------------------------
function check_magazine_cover_exist($magazine_id) {
	$result = false;
	if ($magazine_id != "") {
		$file_cover = path_magazine_dir_pics($magazine_id) . "/cover.jpg";
		if (file_exists($file_cover)) {
			$result = true;
		}	
	}
	return $result;	
}
//---------------------------------------------
function check_group_cover_exist($group_id) {
	$result = false;
	if ($group_id != "") {
		$file_cover = path_group_dir_pics($group_id) . "/cover.jpg";
		if (file_exists($file_cover)) {
			$result = true;
		}	
	}
	return $result;	
}
//---------------------------------------------
function delete_magazine() {
	$errors = "";
	$magazine_id = filter_input(INPUT_POST, 'magazine_id');
	if ($magazine_id) {
		$db_link = @connecDB();
		$mysqli_result = mysqli_query($db_link, "UPDATE magazine SET deleted='1', deleted_datetime='" . date('Y-m-d H:i:s') ."' WHERE id='" . mysqli_real_escape_string($db_link, $magazine_id) . "' AND deleted='0'");
		if (!$mysqli_result) {
			$errors = "DB error: " . mysqli_error($db_link);
		} else {
			$status = "ok";
		}
		if (@$db_link) mysqli_close($db_link);
	}
	$result = array(
		'status' => $status,
		"errors" => $errors
	);
	return json_encode($result);
}
//---------------------------------------------
function delete_promocode() {
	$errors = "";
	$promocode_id = filter_input(INPUT_POST, 'promocode_id');
	if ($promocode_id) {
		$db_link = @connecDB();
		$mysqli_result = mysqli_query($db_link, "UPDATE promocode SET deleted='1', deleted_datetime='" . date('Y-m-d H:i:s') ."' WHERE id='" . mysqli_real_escape_string($db_link, $promocode_id) . "' AND deleted='0'");
		if (!$mysqli_result) {
			$errors = "DB error: " . mysqli_error($db_link);
		} else {
			$status = "ok";
		}
		if (@$db_link) mysqli_close($db_link);
	}
	$result = array(
		'status' => $status,
		"errors" => $errors
	);
	return json_encode($result);
}
//---------------------------------------------
function check_logged_in() {
	$cookie_login = '';
	$cookie_hash = '';
	if (isset($_COOKIE['login'])) {
		$cookie_login = $_COOKIE['login'];
	}
	if (isset($_COOKIE['hash'])) {
		$cookie_hash = $_COOKIE['hash'];
	}
	$tmp_result = array();
	if ($cookie_login != "" && $cookie_hash != "") {
		$db_link = @connecDB();
		$mysqli_result = mysqli_query($db_link, "SELECT * FROM users WHERE login='$cookie_login' AND active='1'");
		while ($tmp_row = mysqli_fetch_assoc($mysqli_result)) {
			if ( $tmp_row["logged_in_hash"] == $cookie_hash ) {
				$tmp_result['login'] = $tmp_row["login"];
				$tmp_result['name'] = $tmp_row["name"];
				break;
			}
		}
		if (@$db_link) mysqli_close($db_link);
	}
	return $tmp_result;
}
//---------------------------------------------
function check_agent_logged_in() {
	$cookie_login = '';
	$cookie_hash = '';
	if (isset($_COOKIE['agent_login'])) {
		$cookie_login = $_COOKIE['agent_login'];
	}
	if (isset($_COOKIE['agent_hash'])) {
		$cookie_hash = $_COOKIE['agent_hash'];
	}
	$tmp_result = array();
	if ($cookie_login != "" && $cookie_hash != "") {
		$db_link = @connecDB();
		$mysqli_result = mysqli_query($db_link, "SELECT * FROM agent WHERE login='$cookie_login' AND active='1'");
		while ($tmp_row = mysqli_fetch_assoc($mysqli_result)) {
			if ( $tmp_row["logged_in_hash"] == $cookie_hash ) {
				$tmp_result['login'] = $tmp_row["login"];
				$tmp_result['name'] = $tmp_row["name_last"] . ' ' . $tmp_row["name_first"] . ' ' . $tmp_row["name_middle"];
				break;
			}
		}
		if (@$db_link) mysqli_close($db_link);
	}
	return $tmp_result;
}
//---------------------------------------------
function api_login() {
	$errors = array();
	$status = "fields_error";
	$login = filter_input(INPUT_POST, 'login');
	$pass = filter_input(INPUT_POST, 'pass');
	$tmp_pass_hash = hash('md5', $pass);
	
	if ($login == "") {
		$errors["login"][] = "Необходимо заполнить поле «Логин»";
	}
	if ($pass == "") {
		$errors["pass"][] = "Необходимо заполнить поле «Пароль»";
	}
	if (!$errors) {
		$db_link = @connecDB();
		$mysqli_result = mysqli_query($db_link, "SELECT * FROM users WHERE login='" . mysqli_real_escape_string($db_link, $login) . "'");
		while ($tmp_row = mysqli_fetch_assoc($mysqli_result)) {
			if ($tmp_row["pass"] == $tmp_pass_hash || $tmp_row["pass"] == $pass) {
				if ($tmp_row["active"] == 1) {
					update_db_user_logged_in($login);
					$status = "ok_login";
					unset($errors);
				} else {
					$status = "fields_error";
					$errors["status_error"][] = "Этот пользователь не активирован";
				}
			} else {
				$status = "fields_error";
				$errors["status_error"][] = "Неправильное имя пользователя или пароль!";
			}
		}
		if (@$db_link) mysqli_close($db_link);
	} else {
		$status = 'fields_error';
	}	
	if ($status == "fields_error" && empty($errors)) {
		$errors["status_error"][] = "Неправильное имя пользователя или пароль!";
	}	
	$result = array(
		'status' => $status,
		"errors" => $errors
	);
	return json_encode($result);
}
//---------------------------------------------
function api_agent_login() {
	$errors = array();
	$status = "fields_error";
	$login = filter_input(INPUT_POST, 'login');
	$pass = filter_input(INPUT_POST, 'pass');
	$tmp_pass_hash = hash('md5', $pass);
	
	if ($login == "") {
		$errors["login"][] = "Необходимо заполнить поле «Логин»";
	}
	if ($pass == "") {
		$errors["pass"][] = "Необходимо заполнить поле «Пароль»";
	}
	if (!$errors) {
		$db_link = @connecDB();
		$mysqli_result = mysqli_query($db_link, "SELECT * FROM agent WHERE login='" . mysqli_real_escape_string($db_link, $login) . "'");
		while ($tmp_row = mysqli_fetch_assoc($mysqli_result)) {
			if ($tmp_row["pass"] == $tmp_pass_hash || $tmp_row["pass"] == $pass) {
				if ($tmp_row["active"] == 1) {
					update_db_agent_logged_in($login);
					$status = "ok_login";
					unset($errors);
				} else {
					$status = "fields_error";
					$errors["status_error"][] = "Этот агент не активирован";
				}
			} else {
				$status = "fields_error";
				$errors["status_error"][] = "Неправильное имя агента или пароль!";
			}
		}
		if (@$db_link) mysqli_close($db_link);
	} else {
		$status = 'fields_error';
	}	
	if ($status == "fields_error" && empty($errors)) {
		$errors["status_error"][] = "Неправильное имя агента или пароль!";
	}	
	$result = array(
		'status' => $status,
		"errors" => $errors
	);
	return json_encode($result);
}
//---------------------------------------------
function update_db_user_logged_in($login) {
	$tmp_hash = hash('md5', $login + time());
	$db_link = @connecDB();
	$mysql_result = mysqli_query($db_link, "SELECT logged_in_hash FROM users WHERE login='$login' AND active='1'");
	while ($tmp_row = mysqli_fetch_assoc($mysql_result)) {
		if ( $tmp_row["logged_in_hash"] != "" ) {
			$tmp_hash = $tmp_row["logged_in_hash"];
		}
		break;
	}
	setcookies_login_success($login, $tmp_hash);
	
	$mysql_result = false;
	$mysql_result = mysqli_query($db_link, "UPDATE users SET logged_in_hash='$tmp_hash', login_nums=login_nums+1, last_login='" . date('Y-m-d H:i:s') . "' WHERE login='$login'");
	if (@$db_link) mysqli_close($db_link);

	return $mysql_result;
}
//---------------------------------------------
function update_db_agent_logged_in($login) {
	$tmp_hash = hash('md5', $login + time());
	$db_link = @connecDB();
	$mysql_result = mysqli_query($db_link, "SELECT logged_in_hash FROM agent WHERE login='$login' AND active='1'");
	while ($tmp_row = mysqli_fetch_assoc($mysql_result)) {
		if ( $tmp_row["logged_in_hash"] != "" ) {
			$tmp_hash = $tmp_row["logged_in_hash"];
		}
		break;
	}
	setcookies_agent_login_success($login, $tmp_hash);
	
	$mysql_result = false;
	$mysql_result = mysqli_query($db_link, "UPDATE agent SET logged_in_hash='$tmp_hash', login_nums=login_nums+1, last_login='" . date('Y-m-d H:i:s') . "' WHERE login='$login'");
	if (@$db_link) mysqli_close($db_link);

	return $mysql_result;
}
//---------------------------------------------
function update_db_user_logged_out($login) {
	setcookies_login_success($login, "");
/*
	$mysql_result = false;
	$mysql_result = false;
	$mysql_result = mysqli_query($db_link, "UPDATE users SET logged_in_hash='', logout_nums=logout_nums+1, last_logout='" . date('Y-m-d H:i:s') . "' WHERE login='$login'");
	if (@$db_link) mysqli_close($db_link);
	return $mysql_result;
*/
	return true;
}
//---------------------------------------------
function update_db_user_agent_logged_out($login) {
	setcookies_agent_login_success($login, "");
	return true;
}
//---------------------------------------------
function setcookies_login_success($login, $pass) {
	$int = 60*60*24*30;
	setcookie("login", $login, time() + $int, '/');
	setcookie("hash", $pass, time() + $int, '/');
}
//---------------------------------------------
function setcookies_agent_login_success($login, $pass) {
	$int = 60*60*24*30;
	setcookie("agent_login", $login, time() + $int, '/');
	setcookie("agent_hash", $pass, time() + $int, '/');
}
//---------------------------------------------
function clearcookies_logout_success($login) {
	$int = 60*60*24*30;
	setcookie("login", $login, time() + $int, '/');
	setcookie("hash", "", time() + $int, '/');
}
//---------------------------------------------
function clearcookies_agent_logout_success($login) {
	$int = 60*60*24*30;
	setcookie("agent_login", $login, time() + $int, '/');
	setcookie("agent_hash", "", time() + $int, '/');
}
//---------------------------------------------
function api_logout($location) {
	$logged_in = check_logged_in();
	if ( !empty($logged_in) ) {
		$login = $logged_in['login'];
		update_db_user_logged_out($login);
	}
	if ($location) {
		header ('Location: ' . $location);
	} else {
		header ('Location: /admin/');
	}
	exit;
}
//---------------------------------------------
function api_agent_logout($location) {
	$logged_in_agent = check_agent_logged_in();
	if ( !empty($logged_in_agent) ) {
		$login_agent = $logged_in_agent['login_agent'];
		update_db_user_agent_logged_out($login_agent);
	}
	if ($location) {
		header ('Location: ' . $location);
	} else {
		header ('Location: /agent/');
	}
	exit;
}
//---------------------------------------------
function get_promocode_info($promocode_id = '') {
	$arr_promocode_info = array();
	if ($promocode_id == '') {
		$promocode_id = filter_input(INPUT_GET, 'id');
	}
	$db_link = @connecDB();
	if ($promocode_id != '') {
		$query = "SELECT * FROM promocode WHERE id='$promocode_id' AND deleted='0'";
		$mysqli_result = mysqli_query($db_link, $query);
		while ($tmp_row = mysqli_fetch_assoc($mysqli_result)) {
			$arr_promocode_info["id"] = $tmp_row["id"];
			$arr_promocode_info["code_text"] = $tmp_row["code_text"];
			$arr_promocode_info["code_name"] = $tmp_row["code_name"];
			$arr_promocode_info["discount_percent"] = $tmp_row["discount_percent"];
			$arr_promocode_info["for_all_magazines"] = $tmp_row["for_all_magazines"];
			$arr_promocode_info["datetime_start"] = $tmp_row["datetime_start"];
			$arr_promocode_info["datetime_end"] = $tmp_row["datetime_end"];			
			break;
		}
	}
	$mysqli_result = mysqli_query($db_link, "SELECT magazine.id AS id, magazine.name AS name, promocode_to_magazines.id_promocode AS magazine_checked FROM magazine LEFT JOIN promocode_to_magazines ON magazine.id=promocode_to_magazines.id_magazine AND promocode_to_magazines.id_promocode='$promocode_id' AND promocode_to_magazines.deleted=0 ORDER BY list_order_value DESC");
	while ($tmp_row = mysqli_fetch_assoc($mysqli_result)) {
		$tmp_arr = array("magazine_id"=>$tmp_row["id"], "magazine_name"=>$tmp_row["name"], "magazine_checked"=>$tmp_row["magazine_checked"]);
		$arr_magazines[] = $tmp_arr;
	}
	$arr_promocode_info["magazines"] = $arr_magazines;

	if (@$mysqli_result) mysqli_free_result($mysqli_result);
	if (@$db_link) mysqli_close($db_link);

	return $arr_promocode_info;
}
//---------------------------------------------
function set_promocode_info() {
	$act = filter_input(INPUT_POST, 'act');
	if ($act == 'new_promocode' || $act == 'edit_promocode') {
		$code_text = filter_input(INPUT_POST, 'code_text');
		$discount_percent = filter_input(INPUT_POST, 'discount_percent');
		$code_name = filter_input(INPUT_POST, 'code_name');
		$datetime_start = filter_input(INPUT_POST, 'datetime_start');
		if (strlen($datetime_start) == 10) {
			$datetime_start .=  " 00:00:00";
		}
		$datetime_end = filter_input(INPUT_POST, 'datetime_end');
		if (strlen($datetime_end) == 10) {
			$datetime_end .=  " 00:00:00";
		}
		$for_all_magazines = filter_input(INPUT_POST, 'radio_promocode_type');
		$db_link = @connecDB();
		if ($act == 'edit_promocode') {
			$id_promocode = filter_input(INPUT_POST, 'id');
			$tmp_query = "UPDATE promocode SET code_text='" . mysqli_real_escape_string($db_link, $code_text) . "', discount_percent='" . mysqli_real_escape_string($db_link, $discount_percent) . "', code_name='" . mysqli_real_escape_string($db_link, $code_name) . "', datetime_start='" . mysqli_real_escape_string($db_link, $datetime_start) . "', datetime_end='" . mysqli_real_escape_string($db_link, $datetime_end) . "', for_all_magazines='" . mysqli_real_escape_string($db_link, $for_all_magazines) . "' WHERE id='" . mysqli_real_escape_string($db_link, $id_promocode) . "' AND deleted='0'";
		}
		if ($act == 'new_promocode') {
			$tmp_query = "INSERT INTO promocode (code_text, discount_percent, code_name, datetime_start, datetime_end, for_all_magazines, datetime_created) VALUES('" . mysqli_real_escape_string($db_link, $code_text) . "', '" . mysqli_real_escape_string($db_link, $discount_percent) . "', '" . mysqli_real_escape_string($db_link, $code_name) . "', '" . mysqli_real_escape_string($db_link, $datetime_start) . "', '" . mysqli_real_escape_string($db_link, $datetime_end) . "', '" . mysqli_real_escape_string($db_link, $for_all_magazines) . "', '" . date('Y-m-d H:i:s') . "')";
		}
		if ($tmp_query != '') {
			$mysqli_result = mysqli_query($db_link, $tmp_query);
			if (!$mysqli_result) {
				echo "DB error: " . mysqli_error($db_link);
			} else {
				if ($act == 'new_promocode') {
					$id_promocode = mysqli_insert_id($db_link);
				}
			}
			if ($for_all_magazines != '1') {
				$for_all_magazines = 0;
				$arr_magazines = $_POST['magazines'];
				$now = date('Y-m-d H:i:s');
				$tmp_query = "UPDATE promocode_to_magazines SET deleted='1', deleted_datetime='$now' WHERE id_promocode='" . mysqli_real_escape_string($db_link, $id_promocode) . "'";
				$mysqli_result = mysqli_query($db_link, $tmp_query);
				foreach ($arr_magazines as $magazine_id) {
					$tmp_query = "INSERT INTO promocode_to_magazines (id_promocode, id_magazine, deleted, deleted_datetime) VALUES('" . mysqli_real_escape_string($db_link, $id_promocode) . "', '" . mysqli_real_escape_string($db_link, $magazine_id) . "', '0', NULL) ON DUPLICATE KEY UPDATE deleted='0', deleted_datetime=NULL";
					$mysqli_result = mysqli_query($db_link, $tmp_query);
				}
			}	
		}
		if (@$db_link) mysqli_close($db_link);
	}
	return $id_promocode;
}
//---------------------------------------------
function api_check_promocode() {
	$promocode = filter_input(INPUT_POST, 'promocode');
	$magazine_id = filter_input(INPUT_POST, 'magazine_id');
	$status = "error";
	$error_text = "";
	$id = 0;
	$discount_percent = 0;
	$price_6_monthes = 0;
	$price_6_monthes_discounted = 0;
	$price_12_monthes = 0;
	$price_12_monthes_discounted = 0;
	if ($promocode == "") {
		$error_text = "Необходимо заполнить поле «Промокод»";
	}
	if ($error_text == "") {
		$db_link = @connecDB();
		$mysqli_result = mysqli_query($db_link, "SELECT * FROM promocode WHERE code_text='" . mysqli_real_escape_string($db_link, $promocode) . "' AND deleted='0'");
		while ($tmp_row = mysqli_fetch_assoc($mysqli_result)) {
			$id = $tmp_row["id"];
			$datetime_start = $tmp_row["datetime_start"];
			$datetime_end = $tmp_row["datetime_end"];
			$discount_percent = $tmp_row["discount_percent"];			
			$for_all_magazines = $tmp_row["for_all_magazines"];
			if (!$for_all_magazines) {
				$flagPromocodeExist = false;
				$mysqli_result2 = mysqli_query($db_link, "SELECT * FROM promocode_to_magazines WHERE id_promocode='" . mysqli_real_escape_string($db_link, $id) . "' AND id_magazine='" . mysqli_real_escape_string($db_link, $magazine_id) . "' AND deleted='0'");
				while ($tmp_row2 = mysqli_fetch_assoc($mysqli_result2)) {
					$flagPromocodeExist = true;
					break;
				}
				if (!$flagPromocodeExist) {
					$error_text = "Такого промокода для этого журнала не существует.";
				}
			}
			if ($error_text == "") {
				$date_now = date('Y-m-d H:i:s');
				if ($date_now < $datetime_start) {
					$error_text = "Этот промокод еще не начал действовать.";
				} else if ($date_now > $datetime_end) {
					$error_text = "Действие этого промокода уже закончено.";
				} else {
					$status = "ok_promocode";
				}
			}
			break;
		}
		if ($status == "ok_promocode") {
			$mysqli_result = mysqli_query($db_link, "SELECT * FROM magazine WHERE id='" . mysqli_real_escape_string($db_link, $magazine_id) ."' AND deleted='0'");
			while ($tmp_row = mysqli_fetch_assoc($mysqli_result)) {
				$stavka_nds = $tmp_row["stavka_nds"];
				$price_6_monthes = $tmp_row["price_6_monthes"];
				$price_6_monthes_discounted = mega_nds_formula($stavka_nds, 6, $price_6_monthes, $discount_percent);
				$price_12_monthes = $tmp_row["price_12_monthes"];
				$price_12_monthes_discounted = mega_nds_formula($stavka_nds, 12, $price_12_monthes, $discount_percent);
				break;
			}
		} 		
		if (@$db_link) mysqli_close($db_link);
	}
	if ($status == "error" && $error_text == "") {
		$error_text = "Такого промокода не существует.";
	}
	$result = array(
		'status' => $status,
		'promocode_id' => $id,
		'promocode_code_text' => $promocode,
		'discount_percent' => $discount_percent,
		'price_6_monthes' => $price_6_monthes,
		'price_6_monthes_discounted' => $price_6_monthes_discounted,
		'price_12_monthes' => $price_12_monthes,
		'price_12_monthes_discounted' => $price_12_monthes_discounted,
		"error_text" => $error_text
	);
	return json_encode($result);
}
//---------------------------------------------
function mega_nds_formula_1_item($stavka_nds, $monthes, $price, $discount) {
	$price_1_item = ($price - ($price/100*$discount));
	$price_1_item = $price_1_item - summaNDS($price_1_item, $stavka_nds);
	$price_1_item = $price_1_item / $monthes;
	if ($discount) {
		$price_1_item = ceil($price_1_item * 100) / 100; //округляем в большую сторону с двумя знаками после запятой
	} else {
		$price_1_item = round($price_1_item * 100) / 100; //округляем с двумя знаками после запятой
	}
	return $price_1_item;
}
//---------------------------------------------
function mega_nds_formula($stavka_nds, $monthes, $price, $discount) {
	$discount_correct = ($price - ($price/100*$discount));
	$discount_correct = $discount_correct - summaNDS($discount_correct, $stavka_nds);
	$discount_correct = $discount_correct / $monthes;
	$discount_correct = ceil($discount_correct * 100) / 100; //округляем в большую сторону с двумя знаками после запятой
	$discount_correct = $discount_correct * $monthes;
	$discount_correct = $discount_correct + $discount_correct/100*10; //прибавляем станвку ндс в процентах от этого числа
	$discount_correct = ceil($discount_correct * 100) / 100; //округляем в большую сторону с двумя знаками после запятой
	return $discount_correct;
}
//---------------------------------------------
function get_unique_order_id($id_subscribe) {
	global $config_DomainEnd;
	$unique_order_id = "";
	$now = time();
	$date = date('Y-m-d', $now);
	$time = date('H:i:s', $now);
	$db_link = @connecDB();
	$tmp_query = "SELECT COUNT(*) AS count FROM subscribe_date_time_num WHERE date='$date'";
	$mysqli_result = mysqli_query($db_link, $tmp_query);
	$num = 0;
	while ($tmp_row = mysqli_fetch_assoc($mysqli_result)) {
		$num = $tmp_row["count"];
		break;
	}
	$num++;
	
	$tmp_query = "INSERT INTO subscribe_date_time_num (date, time, num, id_subscribe) VALUES('$date', '$time', '$num', '$id_subscribe')";
	$mysqli_result = mysqli_query($db_link, $tmp_query);
	if (!$mysqli_result) {
		echo "DB error: " . mysqli_error($db_link);
	} else {
		$unique_order_id = strtoupper($config_DomainEnd) . date('Ymd', $now) . "-" . str_pad($num, 4, '0', STR_PAD_LEFT);
	}
	if (@$db_link) mysqli_close($db_link);
	return $unique_order_id;
}
//---------------------------------------------
function checkErrorFIO($text) {
	$error = '';
	$result = preg_match('/^[А-ЯЁ][а-яё]{2,}([-][А-ЯЁ][а-яё]{2,})?\s[А-ЯЁ][а-яё]{2,}\s[А-ЯЁ][а-яё]{2,}$/u', $text);
	if (!$result) {
		$error = "Ошибка ввода Ф.И.О.";
	}
	return $error;
}
//---------------------------------------------
function checkErrorTel($text) {
	$text = str_replace(" ", "", $text); //убираем все пробелы
	$text = str_replace("-", "", $text); //убираем все минусы
	$error = '';
	$result = preg_match('/^([\+]){0,1}[\d]{5,}$/u', $text); //проверка моя примитивная, в начале может быть + и должно быть не менее 5 цифр
	if (!$result) {
		$error = 'Введен некорректный номер телефона';
	}
	return $error;
}
//---------------------------------------------
function checkErrorZip($text) {
	$text = str_replace(" ", "", $text); //убираем все пробелы
	$error = '';
	$result = preg_match('/^[\d]{6}$/u', $text); //проверка только 6 цифр
	if (!$result) {
		$error = 'Необходимо ввести почтовый индекс';
	}
	return $error;
}
//---------------------------------------------
function checkErrorRegion($text) {
	global $config_DomainEnd;
	$error = '';
	if ($text == '') {
		if ($config_DomainEnd == 'by') {
			$error = 'Необходимо ввести область';
		} else {
			$error = 'Необходимо ввести регион';
		}
	}
	return $error;
}
//---------------------------------------------
function checkErrorCity($text) {
	$error = '';
	if ($text == '') {
		$error = 'Необходимо ввести город';
	}
	return $error;
}
//---------------------------------------------
function checkErrorStreet($text) {
	$error = '';
	if ($text == '') {
		$error = 'Необходимо ввести улицу';
	}
	return $error;
}
//---------------------------------------------
function checkErrorHouse($text) {
	$error = '';
	if ($text == '') {
		$error = 'Необходимо ввести дом';
	}
	return $error;
}
//---------------------------------------------
function checkErrorKorpus($text) {
	$error = '';
	if ($text != '') {
		$text_only_digits = preg_replace("/[^0-9]/", '', $text); //оставляем только цифры
		if ($text != $text_only_digits) {		
			$error = 'В корпусе могут быть только цифры';
		}
	}
	return $error;
}
//---------------------------------------------
function checkErrorAgree($text) {
	$error = '';
	if ($text == '') {
		$error = 'Необходимо ознакомиться с Договором оферты и Политикой обработки персональных данных и принять их.';
	}
	return $error;
}
//---------------------------------------------
function checkPromocodeInfoCorrect($magazine_id, $promocode_id, $promocode_code_text, $promocode_discount_percent) {
	$result = false;
	$db_link = @connecDB();
	$mysqli_result = mysqli_query($db_link, "SELECT * FROM promocode WHERE id='$promocode_id' AND deleted='0'");
	while ($tmp_row = mysqli_fetch_assoc($mysqli_result)) {
		if ($promocode_code_text == $tmp_row["code_name"] || $promocode_discount_percent == $tmp_row["discount_percent"]) {
			if ($tmp_row["for_all_magazines"]) {
				$result = true;
				break;
			} else {
				$mysqli_result2 = mysqli_query($db_link, "SELECT * FROM promocode_to_magazines WHERE id_promocode='$promocode_id' AND id_magazine='$magazine_id' AND deleted='0'");
				while ($tmp_row2 = mysqli_fetch_assoc($mysqli_result2)) {
					$result = true;
					break;
				}
			}		
		}
		break;
	}
	if (@$mysqli_result) mysqli_free_result($mysqli_result);
	if (@$db_link) mysqli_close($db_link);
	return $result;
}
//---------------------------------------------
function checkPrices($magazine_id, $monthes, $price) {
	$result = true;
	$db_link = @connecDB();
	$mysqli_result = mysqli_query($db_link, "SELECT * FROM magazine WHERE id='$magazine_id' AND deleted='0'");
	while ($tmp_row = mysqli_fetch_assoc($mysqli_result)) {
		if ($monthes == 6) {
			if ($price != $tmp_row["price_6_monthes"]) {
				$result = false;
			}
		} else {
			if ($price != $tmp_row["price_12_monthes"]) {
				$result = false;
			}
		}
		break;
	}
	if (@$mysqli_result) mysqli_free_result($mysqli_result);
	if (@$db_link) mysqli_close($db_link);
	return $result;
}
//---------------------------------------------
function set_subscribe_info() {
	global $config_subscribe_day_num_to_get_this_month;
	global $config_DomainEnd;
	$act = filter_input(INPUT_POST, 'act');
	$subscribe_id = 0;
	$banking_pay_url = '';
	$error_text = '';
	if ($act == 'subscribe') {
		$arr_to_save_in_cookies = array("fio", "email", "tel", "region", "city", "street", "house", "korpus", "flat", "zip");
		save_post_values_in_cookies($arr_to_save_in_cookies);

		if ($error_text == "") {
			$fio = trim(filter_input(INPUT_POST, 'fio'));
			$error_text = checkErrorFIO($fio);
		}
		if ($error_text == "") {
			$email = trim(filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL));
			if ($email == "") {
				$error_text = "Введен некорректный e-mail";
			}
		}
		if ($error_text == "") {
			$tel = trim(filter_input(INPUT_POST, 'tel'));
			$error_text = checkErrorTel($tel);			
			if ($config_DomainEnd == 'ru') {
				$normalized_tel_phone = trim(filter_input(INPUT_POST, 'normalized_tel_phone'));
				$normalized_tel_type = trim(filter_input(INPUT_POST, 'normalized_tel_type'));
				$normalized_tel_country_code = trim(filter_input(INPUT_POST, 'normalized_tel_country_code'));
				$normalized_tel_city_code = trim(filter_input(INPUT_POST, 'normalized_tel_city_code'));
				$normalized_tel_number = trim(filter_input(INPUT_POST, 'normalized_tel_number'));
				$normalized_tel_extension = trim(filter_input(INPUT_POST, 'normalized_tel_extension'));
				$normalized_tel_provider = trim(filter_input(INPUT_POST, 'normalized_tel_provider'));
				$normalized_tel_region = trim(filter_input(INPUT_POST, 'normalized_tel_region'));
				$normalized_tel_timezone = trim(filter_input(INPUT_POST, 'normalized_tel_timezone'));
				$normalized_tel_qc_conflict = trim(filter_input(INPUT_POST, 'normalized_tel_qc_conflict'));
				$normalized_tel_qc = trim(filter_input(INPUT_POST, 'normalized_tel_qc'));
			} else {
				$normalized_tel_phone = '';
				$normalized_tel_type = '';
				$normalized_tel_country_code = '';
				$normalized_tel_city_code = '';
				$normalized_tel_number = '';
				$normalized_tel_extension = '';
				$normalized_tel_provider = '';
				$normalized_tel_region = '';
				$normalized_tel_timezone = '';
				$normalized_tel_qc_conflict = '';
				$normalized_tel_qc = '';
			}
		}
		if ($error_text == "") {
			$region_unnormalized = '';
			if ($config_DomainEnd == 'by') {
				$region_type = trim(filter_input(INPUT_POST, 'region_type'));
				if ($region_type != '') {
					 $region_type .= ' ОБЛАСТЬ';
				}
				$region = $region_type;
			} else {
				if(filter_input(INPUT_POST, 'normalized_region')) {
					$region_unnormalized = trim(filter_input(INPUT_POST, 'region'));
					$region = trim(filter_input(INPUT_POST, 'normalized_region'));
				} else {
					$region = trim(filter_input(INPUT_POST, 'region'));
				}				
				$error_text = checkErrorRegion($region);
			}
		}
		if ($error_text == "") {
			$city_unnormalized = '';
			if(filter_input(INPUT_POST, 'normalized_city')) {
				$city_unnormalized = trim(filter_input(INPUT_POST, 'city'));
				$city = trim(filter_input(INPUT_POST, 'normalized_city'));
			} else {
				$city = trim(filter_input(INPUT_POST, 'city'));
			}
			$error_text = checkErrorCity($city);
			if ($config_DomainEnd == 'by') {
				$city_type = trim(filter_input(INPUT_POST, 'city_type'));
			} else {
				if(filter_input(INPUT_POST, 'normalized_city_type')) {
					$city_type = trim(filter_input(INPUT_POST, 'normalized_city_type'));
				} else {
					$city_type = '';
				}
			}
		}
		if ($error_text == "") {
			$street_unnormalized = '';
			if(filter_input(INPUT_POST, 'normalized_street')) {
				$street_unnormalized = trim(filter_input(INPUT_POST, 'street'));
				$street = trim(filter_input(INPUT_POST, 'normalized_street'));
			} else {
				$street = trim(filter_input(INPUT_POST, 'street'));
			}
			$error_text = checkErrorStreet($street);
			if ($config_DomainEnd == 'by') {
				$street_type = trim(filter_input(INPUT_POST, 'street_type'));
			} else {
				if(filter_input(INPUT_POST, 'normalized_street_type')) {
					$street_type = trim(filter_input(INPUT_POST, 'normalized_street_type'));
				} else {
					$street_type = '';
				}
			}
		}
		if ($error_text == "") {
			$house_unnormalized = '';
			if(filter_input(INPUT_POST, 'normalized_house')) {
				$house_unnormalized = trim(filter_input(INPUT_POST, 'house'));
				$house = trim(filter_input(INPUT_POST, 'normalized_house'));
			} else {
				$house = trim(filter_input(INPUT_POST, 'house'));
			}
			$error_text = checkErrorHouse($house);
		}
		$house_type = '';
		$korpus_type = '';
		if ($config_DomainEnd == 'ru') {
			if(filter_input(INPUT_POST, 'normalized_house_type')) {
				$house_type = trim(filter_input(INPUT_POST, 'normalized_house_type'));
			}
			if(filter_input(INPUT_POST, 'normalized_korpus_type')) {
				$korpus_type = trim(filter_input(INPUT_POST, 'normalized_korpus_type'));
			}
		}
		if ($error_text == "") {
			if ($config_DomainEnd == 'by') {
				$korpus = trim(filter_input(INPUT_POST, 'korpus'));
				$error_text = checkErrorKorpus($korpus);
			} else {
				$korpus = trim(filter_input(INPUT_POST, 'korpus'));
			}
		}		
		if ($error_text == "") {
			$zip = trim(filter_input(INPUT_POST, 'zip'));
			$error_text = checkErrorZip($zip);
		}
		if ($error_text == "") {
			$agree = trim(filter_input(INPUT_POST, 'agree'));
			$error_text = checkErrorAgree($agree);
		}		

		$district_unnormalized = '';
		if ($config_DomainEnd == 'by') {
			$district = trim(filter_input(INPUT_POST, 'district'));
			$city_name = trim(filter_input(INPUT_POST, 'city_name'));
			if ($city_name == '') {
				$city_name = $city;
			}
			$street_name = trim(filter_input(INPUT_POST, 'street_name'));
			if ($street_name == '') {
				$street_name = $street;
			}
		} else {
			if(filter_input(INPUT_POST, 'normalized_district')) {
				$district = trim(filter_input(INPUT_POST, 'normalized_district'));
			} else {
				$district = '';
			}				
			$city_name = '';
			$street_name = '';
		}
		
		$flat_unnormalized = '';
		if(filter_input(INPUT_POST, 'normalized_flat')) {
			$flat_unnormalized = trim(filter_input(INPUT_POST, 'flat'));
			$flat = trim(filter_input(INPUT_POST, 'normalized_flat'));
		} else {
			$flat = trim(filter_input(INPUT_POST, 'flat'));
		}


		$comment = trim(filter_input(INPUT_POST, 'comment'));
		$monthes = trim(filter_input(INPUT_POST, 'monthes'));
		if ($monthes != 6 && $monthes != 12) {
			$error_text = "Срок подписки должен быть 6 или 12 месяцев!";
		}

		
		$magazine_id = trim(filter_input(INPUT_POST, 'magazine_id'));
		$arr_magazine_info = get_magazine_info($magazine_id);
		$month_releases_num = $arr_magazine_info["month_releases_num"];
		$post_index = $arr_magazine_info["post_index"];
		$price_6_monthes = $arr_magazine_info["price_6_monthes"];
		$price_12_monthes = $arr_magazine_info["price_12_monthes"];
		$stavka_nds = $arr_magazine_info["stavka_nds"];
		$magazine_name = remove_br($arr_magazine_info["name"]);


		$promocode_id = trim(filter_input(INPUT_POST, 'promocode_id'));
		if ($promocode_id == '') {
			$promocode_id = 0;
			$promocode_discount_percent = 0;
			$promocode_code_text = '';
		} else {
			$promocode_code_text = strtolower(trim(filter_input(INPUT_POST, 'promocode_code_text')));
			$promocode_discount_percent = trim(filter_input(INPUT_POST, 'promocode_discount_percent'));
			if ($promocode_discount_percent == '') {
				$promocode_discount_percent = 0;
			}
			if (!checkPromocodeInfoCorrect($magazine_id, $promocode_id, $promocode_code_text, $promocode_discount_percent)) {
				$error_text = "Данные по предоставляемой скидке не совпадают с данными из базы данных!";
			}
		}
		$price = trim(filter_input(INPUT_POST, 'price'));
		$price_discounted = trim(filter_input(INPUT_POST, 'price_discounted'));
		if ($price_discounted) {
			$price_to_pay = $price_discounted;
		} else {
			$price_discounted = 0;
			$price_to_pay = $price;
		}
		$summa_nds = round(summaNDS($price_to_pay, $stavka_nds), 2);

		if (!checkPrices($magazine_id, $monthes, $price)) {
			$error_text = "Данные по оплате не совадают с данными из базы данных!";
		} else {
			if ($promocode_discount_percent) {
				$price_discounted_check = mega_nds_formula($stavka_nds, $monthes, $price, $promocode_discount_percent);
				if ($price_discounted != $price_discounted_check) {
					$error_text = "Данные по цене со скидкой не совадают с данными из базы данных!";
					$error_text .= "<br>price_discounted_check=$price_discounted_check
									<br>price_discounted=$price_discounted
									<br>price=$price
									<br>promocode_discount_percent=$promocode_discount_percent";
				}
			}
		}

		$price_of_1_magazine = mega_nds_formula_1_item($stavka_nds, $monthes, $price, $promocode_discount_percent);

		$month_start_is_changed = trim(filter_input(INPUT_POST, 'change_subscribe_date'));
		if ($month_start_is_changed) {
			$month_start_is_changed = 1;
			$month_start = trim(filter_input(INPUT_POST, 'subscribe-date'));
		} else {
			$month_start_is_changed = 0;
			$month_start = date('n');
			$month_start++;
			if ($month_start > 12) {
				$month_start = 1;
			}
			$day_now = date('j');
			if ($day_now > $config_subscribe_day_num_to_get_this_month) {
				$month_start++;
			}
			if ($month_start > 12) {
				$month_start = 1;
			}
		}
		
		if ($error_text == '') {		
			$session_referer = "";
			if (isset($_SESSION['referer'])) {
				$session_referer = $_SESSION['referer'];
			}

			$db_link = @connecDB();
			$tmp_query = "INSERT INTO subscribe (fio, 
												email, 
												tel, 
												normalized_tel_phone, 
												normalized_tel_type, 
												normalized_tel_country_code, 
												normalized_tel_city_code, 
												normalized_tel_number, 
												normalized_tel_extension, 
												normalized_tel_provider, 
												normalized_tel_region, 
												normalized_tel_timezone, 
												normalized_tel_qc_conflict, 
												normalized_tel_qc, 
												region, 
												region_unnormalized, 
												district, 
												district_unnormalized, 
												city_type, 
												city, 
												city_unnormalized, 
												city_name, 
												street_type, 
												street, 
												street_unnormalized, 
												street_name, 
												house_type, 
												house, 
												house_unnormalized, 
												korpus_type, 
												korpus, 
												flat, 
												flat_unnormalized, 
												zip, 
												comment, 
												magazine_id, 
												magazine_name, 
												monthes, 
												month_start, 
												month_start_is_changed, 
												month_releases_num, 
												post_index, 
												promocode_id, 
												promocode_code_text, 
												promocode_discount_percent, 
												price, 
												price_discounted, 
												summaNDS, 
												price_of_1_magazine, 
												price_6_monthes, 
												price_12_monthes, 
												paid, 
												created_datetime, 
												referer, 
												deleted) VALUES('" . 
												mysqli_real_escape_string($db_link, $fio) . "', '" . 
												mysqli_real_escape_string($db_link, $email) . "', '" . 
												mysqli_real_escape_string($db_link, $tel) . "', '" . 
												mysqli_real_escape_string($db_link, $normalized_tel_phone) . "', '" . 
												mysqli_real_escape_string($db_link, $normalized_tel_type) . "', '" . 
												mysqli_real_escape_string($db_link, $normalized_tel_country_code) . "', '" . 
												mysqli_real_escape_string($db_link, $normalized_tel_city_code) . "', '" . 
												mysqli_real_escape_string($db_link, $normalized_tel_number) . "', '" . 
												mysqli_real_escape_string($db_link, $normalized_tel_extension) . "', '" . 
												mysqli_real_escape_string($db_link, $normalized_tel_provider) . "', '" . 
												mysqli_real_escape_string($db_link, $normalized_tel_region) . "', '" . 
												mysqli_real_escape_string($db_link, $normalized_tel_timezone) . "', '" . 
												mysqli_real_escape_string($db_link, $normalized_tel_qc_conflict) . "', '" . 
												mysqli_real_escape_string($db_link, $normalized_tel_qc) . "', '" . 
												mysqli_real_escape_string($db_link, $region) . "', '" . 
												mysqli_real_escape_string($db_link, $region_unnormalized) . "', '" . 
												mysqli_real_escape_string($db_link, $district) . "', '" . 
												mysqli_real_escape_string($db_link, $district_unnormalized) . "', '" . 
												mysqli_real_escape_string($db_link, $city_type) . "', '" . 
												mysqli_real_escape_string($db_link, $city) . "', '" . 
												mysqli_real_escape_string($db_link, $city_unnormalized) . "', '" . 
												mysqli_real_escape_string($db_link, $city_name) . "', '" . 
												mysqli_real_escape_string($db_link, $street_type) . "', '" . 
												mysqli_real_escape_string($db_link, $street) . "', '" . 
												mysqli_real_escape_string($db_link, $street_unnormalized) . "', '" . 
												mysqli_real_escape_string($db_link, $street_name) . "', '" . 
												mysqli_real_escape_string($db_link, $house_type) . "', '" . 
												mysqli_real_escape_string($db_link, $house) . "', '" . 
												mysqli_real_escape_string($db_link, $house_unnormalized) . "', '" . 
												mysqli_real_escape_string($db_link, $korpus_type) . "', '" . 
												mysqli_real_escape_string($db_link, $korpus) . "', '" . 
												mysqli_real_escape_string($db_link, $flat) . "', '" . 
												mysqli_real_escape_string($db_link, $flat_unnormalized) . "', '" . 
												mysqli_real_escape_string($db_link, $zip) . "', '" . 
												mysqli_real_escape_string($db_link, $comment) . "', '" . 
												mysqli_real_escape_string($db_link, $magazine_id) . "', '" . 
												mysqli_real_escape_string($db_link, $magazine_name) . "', '" . 
												mysqli_real_escape_string($db_link, $monthes) . "', '" . 
												mysqli_real_escape_string($db_link, $month_start) . "', '" . 
												mysqli_real_escape_string($db_link, $month_start_is_changed) . "', '" . 
												mysqli_real_escape_string($db_link, $month_releases_num) . "', '" . 
												mysqli_real_escape_string($db_link, $post_index) . "', '" . 
												mysqli_real_escape_string($db_link, $promocode_id) . "', '" . 
												mysqli_real_escape_string($db_link, $promocode_code_text) . "', '" . 
												mysqli_real_escape_string($db_link, $promocode_discount_percent) . "', '" . 
												mysqli_real_escape_string($db_link, $price) . "', '" . 
												mysqli_real_escape_string($db_link, $price_discounted) . "', '" . 
												mysqli_real_escape_string($db_link, $summa_nds) . "', '" . 
												mysqli_real_escape_string($db_link, $price_of_1_magazine) . "', '" . 
												mysqli_real_escape_string($db_link, $price_6_monthes) . "', '" . 
												mysqli_real_escape_string($db_link, $price_12_monthes) . "', 
												'0', '" . 
												date('Y-m-d H:i:s') . "', '" . 
												mysqli_real_escape_string($db_link, $session_referer) . "', 
												'0')";
			$mysqli_result = mysqli_query($db_link, $tmp_query);
			if (!$mysqli_result) {
				echo "DB error: " . mysqli_error($db_link);
			} else {
				$subscribe_id = mysqli_insert_id($db_link);
				$id_order = get_unique_order_id($subscribe_id);
				$mysqli_result = mysqli_query($db_link, "UPDATE subscribe SET id_order='$id_order' WHERE id='$subscribe_id'");
			}

			if ($subscribe_id) {
				$registerOrderResult = rest_RegisterOrder($id_order);
				if ($config_DomainEnd == 'by') {
					if (empty($registerOrderResult["errors"])) {
						if ($registerOrderResult["checkout"]["token"]) {
							$banking_pay_url = $registerOrderResult["checkout"]["redirect_url"];
							$mysqli_result = mysqli_query($db_link, "UPDATE subscribe SET banking_token='" . $registerOrderResult["checkout"]["token"] . "', banking_formUrl='$banking_pay_url' WHERE id='$subscribe_id' AND deleted='0'");
							if (!$mysqli_result) {
								$error_text = "DB error UPDATE orderId";
							}
						}
					} else {
						echo "Ошибка: " . $registerOrderResult["message"];
						return false;
					}		
				} else {
					if (empty($registerOrderResult["errorCode"])) {
						if ($registerOrderResult["orderId"]) {
							$banking_pay_url = $registerOrderResult["formUrl"];
							$mysqli_result = mysqli_query($db_link, "UPDATE subscribe SET banking_token='" . $registerOrderResult["orderId"] . "', banking_formUrl='$banking_pay_url' WHERE id='$subscribe_id' AND deleted='0'");
							if (!$mysqli_result) {
								$error_text = "DB error UPDATE orderId";
							}
						}
					} else {
						echo "Ошибка #" . $registerOrderResult["errorCode"] . ": " . $registerOrderResult["errorMessage"];
						return false;
					}		
				}	
			}	
		}	
	} else {
		$error_text = "Пустой заказ.";
	}

	if (@$db_link) mysqli_close($db_link);

	$result = array(
		'banking_pay_url' => $banking_pay_url,
		"error_text" => $error_text
	);

	return $result;
}
//---------------------------------------------
function createXML($id_order, $pay_try_num = "") {
	global $config_DomainEnd;
	$db_link = @connecDB();
	$magazine_name = "";

	$mysqli_result = mysqli_query($db_link, "SELECT * FROM subscribe WHERE id_order='$id_order' AND deleted='0'");
	while ($tmp_row = mysqli_fetch_assoc($mysqli_result)) {
		if ($config_DomainEnd == 'by') {
			$banking_token = $tmp_row["banking_token"];
			$banking_merch_id = "";
		} else {
			$banking_token = "";
			$banking_merch_id = $tmp_row["banking_token"];
		}
		$banking_transactionId = $tmp_row["banking_transactionId"];
		$banking_cardNumber = $tmp_row["banking_cardNumber"];
		$banking_approvalCode = $tmp_row["banking_approvalCode"];
		$email = $tmp_row["email"];
		$fio = $tmp_row["fio"];
		$tel = $tmp_row["tel"];

		$normalized_tel_phone = $tmp_row["normalized_tel_phone"];
		$normalized_tel_type = $tmp_row["normalized_tel_type"];
		$normalized_tel_country_code = $tmp_row["normalized_tel_country_code"];
		$normalized_tel_city_code = $tmp_row["normalized_tel_city_code"];
		$normalized_tel_number = $tmp_row["normalized_tel_number"];
		$normalized_tel_extension = $tmp_row["normalized_tel_extension"];
		$normalized_tel_provider = $tmp_row["normalized_tel_provider"];
		$normalized_tel_region = $tmp_row["normalized_tel_region"];
		$normalized_tel_timezone = $tmp_row["normalized_tel_timezone"];
		$normalized_tel_qc_conflict = $tmp_row["normalized_tel_qc_conflict"];
		$normalized_tel_qc = $tmp_row["normalized_tel_qc"];

		$price = $tmp_row["price"];
		$price_discounted = $tmp_row["price_discounted"];
		if ($price_discounted) {
			$price_to_pay = $price_discounted;
		} else {
			$price_discounted = 0;
			$price_to_pay = $price;
		}
		$summaNDS = $tmp_row["summaNDS"];
		$magazine_id = $tmp_row["magazine_id"];
		$zip = $tmp_row["zip"];
		$region = $tmp_row["region"];
		$district = $tmp_row["district"];
		if ($config_DomainEnd == 'by') {
			$city = $tmp_row["city_name"];
			$street = $tmp_row["street_name"];
		} else {
			$city = $tmp_row["city"];
			$street = $tmp_row["street"];
		}
		$city_type = $tmp_row["city_type"];
		$street_type = $tmp_row["street_type"];
		$house_type = $tmp_row["house_type"];
		$korpus_type = $tmp_row["korpus_type"];

		$region_unnormalized = $tmp_row["region_unnormalized"];
		$district_unnormalized = $tmp_row["district_unnormalized"];
		$city_unnormalized = $tmp_row["city_unnormalized"];
		$street_unnormalized = $tmp_row["street_unnormalized"];
		$house_unnormalized = $tmp_row["house_unnormalized"];
		$flat_unnormalized = $tmp_row["flat_unnormalized"];

		$street_name = $tmp_row["street_name"];
		$house = $tmp_row["house"];
		$korpus = $tmp_row["korpus"];
		$flat = $tmp_row["flat"];
		$monthes = $tmp_row["monthes"];
		$month_releases_num = $tmp_row["month_releases_num"];
		$month_start = $tmp_row["month_start"];
		$month_start_is_changed = $tmp_row["month_start_is_changed"];
		$promocode_discount_percent = $tmp_row["promocode_discount_percent"];
		$promocode_code_text = $tmp_row["promocode_code_text"];
		$price_of_1_magazine = $tmp_row["price_of_1_magazine"];
		$comment = $tmp_row["comment"];
		$session_referer = $tmp_row["referer"];

		$mysqli_result = mysqli_query($db_link, "SELECT * FROM magazine WHERE id='$magazine_id' AND deleted='0'");
		while ($tmp_row = mysqli_fetch_assoc($mysqli_result)) {
			$magazine_name = remove_br($tmp_row["name"]);
			$magazine_post_index = $tmp_row["post_index"];
			$stavka_nds = $tmp_row["stavka_nds"];
			break;
		}
		break;
	}

	if (@$mysqli_result) mysqli_free_result($mysqli_result);
	if (@$db_link) mysqli_close($db_link);

	if ($pay_try_num) {
		$id_order = $id_order . '-' . $pay_try_num;
	}

	if ($magazine_name)	{
		$magazines_num = $monthes*$month_releases_num;
		$xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		$xml .= "<data>\n";
		$xml .= "<SITE_ID>$id_order</SITE_ID>\n";
		$xml .= "<TOKEN>$banking_token</TOKEN>\n";
		$xml .= "<MERCH_ID>$banking_merch_id</MERCH_ID>\n";
		$xml .= "<TRANSACTION_ID>$banking_transactionId</TRANSACTION_ID>\n";
		$xml .= "<CARD_NUMBER>$banking_cardNumber</CARD_NUMBER>\n";
		$xml .= "<APPROVAL_CODE>$banking_approvalCode</APPROVAL_CODE>\n";
		$xml .= "<EMAIL>$email</EMAIL>\n";
		$xml .= "<CONTACT_PERSON>$fio</CONTACT_PERSON>\n";
		$xml .= "<PHONE_FIZ>$tel</PHONE_FIZ>\n";
		$xml .= "<PHONE_NORMALIZED>$normalized_tel_phone</PHONE_NORMALIZED>\n";
		$xml .= "<PHONE_NORMALIZED_TYPE>$normalized_tel_type</PHONE_NORMALIZED_TYPE>\n";
		$xml .= "<PHONE_NORMALIZED_COUNTRY_CODE>$normalized_tel_country_code</PHONE_NORMALIZED_COUNTRY_CODE>\n";
		$xml .= "<PHONE_NORMALIZED_CITY_CODE>$normalized_tel_city_code</PHONE_NORMALIZED_CITY_CODE>\n";
		$xml .= "<PHONE_NORMALIZED_NUMBER>$normalized_tel_number</PHONE_NORMALIZED_NUMBER>\n";
		$xml .= "<PHONE_NORMALIZED_EXTENSION>$normalized_tel_extension</PHONE_NORMALIZED_EXTENSION>\n";
		$xml .= "<PHONE_NORMALIZED_PROVIDER>$normalized_tel_provider</PHONE_NORMALIZED_PROVIDER>\n";
		$xml .= "<PHONE_NORMALIZED_REGION>$normalized_tel_region</PHONE_NORMALIZED_REGION>\n";
		$xml .= "<PHONE_NORMALIZED_TIMEZONE>$normalized_tel_timezone</PHONE_NORMALIZED_TIMEZONE>\n";
		$xml .= "<PHONE_NORMALIZED_QC_CONFLICT>$normalized_tel_qc_conflict</PHONE_NORMALIZED_QC_CONFLICT>\n";
		$xml .= "<PHONE_NORMALIZED_QC>$normalized_tel_qc</PHONE_NORMALIZED_QC>\n";
		$xml .= "<ADDRESS_ZIP>$zip</ADDRESS_ZIP>\n";
		$xml .= "<ADDRESS_REGION>$region</ADDRESS_REGION>\n";
		$xml .= "<ADDRESS_REGION_UNNORMALIZED>$region_unnormalized</ADDRESS_REGION_UNNORMALIZED>\n";
		$xml .= "<ADDRESS_DISTRICT>$district</ADDRESS_DISTRICT>\n";
		$xml .= "<ADDRESS_DISTRICT_UNNORMALIZED>$district_unnormalized</ADDRESS_DISTRICT_UNNORMALIZED>\n";
		$xml .= "<ADDRESS_CITY_TYPE>$city_type</ADDRESS_CITY_TYPE>\n";
		$xml .= "<ADDRESS_CITY>$city</ADDRESS_CITY>\n";
		$xml .= "<ADDRESS_CITY_UNNORMALIZED>$city_unnormalized</ADDRESS_CITY_UNNORMALIZED>\n";
		$xml .= "<ADDRESS_STREET_TYPE>$street_type</ADDRESS_STREET_TYPE>\n";
		$xml .= "<ADDRESS_STREET>$street</ADDRESS_STREET>\n";
		$xml .= "<ADDRESS_STREET_UNNORMALIZED>$street_unnormalized</ADDRESS_STREET_UNNORMALIZED>\n";
		$xml .= "<ADDRESS_HOUSE_TYPE>$house_type</ADDRESS_HOUSE_TYPE>\n";
		$xml .= "<ADDRESS_HOUSE>$house</ADDRESS_HOUSE>\n";
		$xml .= "<ADDRESS_HOUSE_UNNORMALIZED>$house_unnormalized</ADDRESS_HOUSE_UNNORMALIZED>\n";
		$xml .= "<ADDRESS_KORPUS_TYPE>$korpus_type</ADDRESS_KORPUS_TYPE>\n";
		$xml .= "<ADDRESS_KORPUS>$korpus</ADDRESS_KORPUS>\n";
		$xml .= "<ADDRESS_FLAT>$flat</ADDRESS_FLAT>\n";
		$xml .= "<ADDRESS_FLAT_UNNORMALIZED>$flat_unnormalized</ADDRESS_FLAT_UNNORMALIZED>\n";
		$xml .= "<MONTHES>$monthes</MONTHES>\n";
		$xml .= "<MONTH_START>$month_start</MONTH_START>\n";
		$xml .= "<MONTH_START_IS_CHANGED>$month_start_is_changed</MONTH_START_IS_CHANGED>\n";
		$xml .= "<DISCOUNT_CODE>$promocode_code_text</DISCOUNT_CODE>\n";
		$xml .= "<COMMENT>$comment</COMMENT>\n";
		$xml .= "<REFERER></REFERER>\n";
		$xml .= "<BASKET>\n";
		$xml .= "	<item0>\n";
		$xml .= "		<PRODUCT_NAME>$magazine_name</PRODUCT_NAME>\n";
		$xml .= "		<PRODUCT_CODE>$magazine_post_index</PRODUCT_CODE>\n";
		$xml .= "		<COUNT>1</COUNT>\n";
		$xml .= "		<PRICE>$price_to_pay</PRICE>\n";
		$xml .= "		<PRICE_1_ITEM>$price_of_1_magazine</PRICE_1_ITEM>\n";
		$xml .= "		<DISCOUNT>$promocode_discount_percent%</DISCOUNT>\n";
		$xml .= "		<STAVKA_NDS>$stavka_nds%</STAVKA_NDS>\n";
		$xml .= "		<SUMMA_NDS>$summaNDS</SUMMA_NDS>\n";
		$xml .= "		<MAGAZINES_NUM>$magazines_num</MAGAZINES_NUM>\n";
		$xml .= "	</item0>\n";
		$xml .= "</BASKET>\n";
		$xml .= "</data>";
	
		$file_name = dirname(__FILE__) . '/subscribes/' . $id_order . '.xml';
		$aaaa = file_put_contents($file_name, $xml);
		if(!$aaaa) {
			die("Error save XML");
		}
	}
}
//---------------------------------------------
function summaNDS($price, $stavka_nds) {
	$price_minus_nds = $price/(100 + $stavka_nds)*$stavka_nds;
	return $price_minus_nds;
}
//---------------------------------------------
function get_subscribe_info($subscribe_id) {
	$arr_subscribe_info = array();
	if ($subscribe_id) {
		$db_link = @connecDB();
		$query = "SELECT 
subscribe.id AS id, 
subscribe.id_order AS id_order, 
subscribe.pay_try_num AS pay_try_num, 
subscribe.banking_formUrl AS banking_formUrl, 
subscribe.banking_transactionId AS banking_transactionId, 
subscribe.fio AS fio, 
subscribe.email AS email, 
subscribe.region AS region, 
subscribe.city AS city, 
subscribe.street AS street, 
subscribe.house AS house, 
subscribe.korpus AS korpus, 
subscribe.flat AS flat, 
subscribe.zip AS zip, 
subscribe.magazine_id AS magazine_id, 
subscribe.monthes AS monthes, 
subscribe.month_start_is_changed AS month_start_is_changed, 
subscribe.promocode_discount_percent AS promocode_discount_percent, 
subscribe.price AS price, 
subscribe.price_discounted AS price_discounted, 
subscribe.price_6_monthes AS price_6_monthes, 
subscribe.price_12_monthes AS price_12_monthes, 
subscribe.created_datetime AS created_datetime, 
subscribe.paid_datetime AS paid_datetime, 
subscribe.month_start AS month_start, 
subscribe.comment AS comment, 
magazine.name AS magazine_name, 
magazine.month_releases_num AS month_releases_num 
FROM subscribe INNER JOIN magazine ON subscribe.id='$subscribe_id' AND subscribe.magazine_id=magazine.id";
		$mysqli_result = mysqli_query($db_link, $query);
		while ($tmp_row = mysqli_fetch_assoc($mysqli_result)) {
			$arr_subscribe_info["id"] = $tmp_row["id"];
			$arr_subscribe_info["id_order"] = $tmp_row["id_order"];
			$arr_subscribe_info["pay_try_num"] = $tmp_row["pay_try_num"];
			$arr_subscribe_info["banking_formUrl"] = $tmp_row["banking_formUrl"];
			$arr_subscribe_info["banking_transactionId"] = $tmp_row["banking_transactionId"];
			$arr_subscribe_info["fio"] = $tmp_row["fio"];
			$arr_subscribe_info["email"] = $tmp_row["email"];
			$arr_subscribe_info["region"] = $tmp_row["region"];
			$arr_subscribe_info["city"] = $tmp_row["city"];
			$arr_subscribe_info["street"] = $tmp_row["street"];
			$arr_subscribe_info["house"] = $tmp_row["house"];
			$arr_subscribe_info["korpus"] = $tmp_row["korpus"];
			$arr_subscribe_info["flat"] = $tmp_row["flat"];
			$arr_subscribe_info["zip"] = $tmp_row["zip"];
			$arr_subscribe_info["magazine_id"] = $tmp_row["magazine_id"];
			$arr_subscribe_info["monthes"] = $tmp_row["monthes"];
			$arr_subscribe_info["month_releases_num"] = $tmp_row["month_releases_num"];
			$arr_subscribe_info["promocode_discount_percent"] = $tmp_row["promocode_discount_percent"];
			$arr_subscribe_info["price"] = $tmp_row["price"];
			$arr_subscribe_info["price_discounted"] = $tmp_row["price_discounted"];
			$arr_subscribe_info["price_6_monthes"] = $tmp_row["price_6_monthes"];
			$arr_subscribe_info["price_12_monthes"] = $tmp_row["price_12_monthes"];
			$arr_subscribe_info["created_datetime"] = $tmp_row["created_datetime"];
			$arr_subscribe_info["paid_datetime"] = $tmp_row["paid_datetime"];
			$arr_subscribe_info["month_start"] = $tmp_row["month_start"];
			$arr_subscribe_info["month_start_is_changed"] = $tmp_row["month_start_is_changed"];
			$arr_subscribe_info["comment"] = $tmp_row["comment"];
			$arr_subscribe_info["magazine_id"] = $tmp_row["magazine_id"];
			$arr_subscribe_info["magazine_name"] = $tmp_row["magazine_name"];
			break;
		}
		if (@$mysqli_result) mysqli_free_result($mysqli_result);
		if (@$db_link) mysqli_close($db_link);
	}
	return $arr_subscribe_info;
}
//---------------------------------------------
function email_subscribe_info($subscribe_id) {
	global $config_DomainEnd;
	global $config_email_copy_subscribe;
	if ($subscribe_id) {
		$arr_subscribe_info = get_subscribe_info($subscribe_id);
		mb_language('ru');
		mb_internal_encoding("UTF-8");
		if ($arr_subscribe_info) {
			$mailheaders = "Content-Type: text/html; charset=utf-8\r\n";
			$mailheaders .= "From: Толока <info@toloka24.$config_DomainEnd>\r\n";
			$mailheaders .= "Reply-To: info@toloka24.$config_DomainEnd\r\n";

			$months = array( 1 => 'Январь' , 'Февраль' , 'Март' , 'Апрель' , 'Май' , 'Июнь' , 'Июль' , 'Август' , 'Сентябрь' , 'Октябрь' , 'Ноябрь' , 'Декабрь' );
			$month_start = $arr_subscribe_info['month_start'];
			$month_start_text = $months[$month_start];
			$month_end = $month_start + $arr_subscribe_info['monthes'] - 1;
			if ($month_end > 12) {
				$month_end = $month_end - 12;
			}
			$month_start_text .= '-' . $months[$month_end];

			$email_content = '<div style="width:540px;font-family:Arial,sans-serif;font-size:14px;line-height:21px;padding:15px 30px;color:#333;margin:15px auto; border:1px solid #ddd;border-radius:10px;box-shadow:0px 0px 5px rgba(0,0,0,0.1);">' . "\n";
			$email_content .= '<h1 style="font-size:18px;line-height:23px;padding:0;margin:5px 0 10px 0;font-style:normal;font-weight:normal;font-family:Arial,sans-serif;">Здравствуйте, ' . $arr_subscribe_info['fio'] . '</h1>' . "\n";
			$email_content .= '<img src="' . full_web_path_magazine_cover($arr_subscribe_info['magazine_id']) . '" width="100" style="width:100px;float:left;margin:8px 15px 10px 0;" />' . "\n";
			$email_content .= '<p>Благодарим Вас за подписку на журнал <b>«' . remove_br($arr_subscribe_info['magazine_name']) . '»</b></p>' . "\n";
			$email_content .= '<table border="0" cellspacing="0" cellpadding="0" width="425">' . "\n";

			if (($config_DomainEnd == 'by') && ($arr_subscribe_info["banking_transactionId"] != "")) {
				$email_content .= '<tr>' . "\n";
				$email_content .= '	<td nowrap valign="top" style="font-family:Arial,sans-serif;font-size:14px;line-height:19px;">Номер транзакции:</td>' . "\n";
				$email_content .= '	<td valign="top" align="right" style="font-family:Arial,sans-serif;font-size:14px;line-height:21px;"><b>' . $arr_subscribe_info['banking_transactionId'] . '</b></td>' . "\n";
				$email_content .= '</tr>' . "\n";
			}

			$email_content .= '<tr>' . "\n";
			$email_content .= '	<td nowrap valign="top" style="font-family:Arial,sans-serif;font-size:14px;line-height:19px;">Номер заказа:</td>' . "\n";
			$email_content .= '	<td valign="top" align="right" style="font-family:Arial,sans-serif;font-size:14px;line-height:21px;"><b>' . $arr_subscribe_info['id_order'] . '</b></td>' . "\n";
			$email_content .= '</tr>' . "\n";

			$email_content .= '<tr>' . "\n";
			$email_content .= '	<td nowrap valign="top" style="font-family:Arial,sans-serif;font-size:14px;line-height:19px;">Дата доставки:</td>' . "\n";
			$email_content .= '	<td valign="top" align="right" style="font-family:Arial,sans-serif;font-size:14px;line-height:21px;">' . $month_start_text . '</td>' . "\n";
			$email_content .= '</tr>' . "\n";

			$email_content .= '<tr>' . "\n";
			$email_content .= '	<td nowrap valign="top" style="font-family:Arial,sans-serif;font-size:14px;line-height:19px;">Количество месяцев:</td>' . "\n";
			$email_content .= '	<td valign="top" align="right" style="font-family:Arial,sans-serif;font-size:14px;line-height:21px;">' . $arr_subscribe_info['monthes'] .'</td>' . "\n";
			$email_content .= '</tr>' . "\n";

			$email_content .= '<tr>' . "\n";
			$email_content .= '	<td nowrap valign="top" style="font-family:Arial,sans-serif;font-size:14px;line-height:19px;">Количество номеров:</td>' . "\n";
			$email_content .= '	<td valign="top" align="right" style="font-family:Arial,sans-serif;font-size:14px;line-height:21px;">' . $arr_subscribe_info['monthes']*$arr_subscribe_info['month_releases_num'] .'</td>' . "\n";
			$email_content .= '</tr>' . "\n";

			$email_content .= '<tr>' . "\n";
			$email_content .= '	<td nowrap valign="top" style="font-family:Arial,sans-serif;font-size:14px;line-height:19px;">Месяц первого номера:</td>' . "\n";
			$email_content .= '	<td valign="top" align="right" style="font-family:Arial,sans-serif;font-size:14px;line-height:21px;">' . $months[$month_start] . '</td>' . "\n";
			$email_content .= '</tr>' . "\n";

			$email_content .= '<tr>' . "\n";
			$email_content .= '	<td nowrap valign="top" style="font-family:Arial,sans-serif;font-size:14px;line-height:19px;">Адрес доставки:</td>' . "\n";
			$email_content .= '	<td valign="top" align="right" style="font-family:Arial,sans-serif;font-size:14px;line-height:21px;">';
			$email_content .= $arr_subscribe_info['zip'];
			$email_content .= ', ' . $arr_subscribe_info['city'];			
			if ($arr_subscribe_info['region'] != '') {
				$email_content .= ', ' . capitalize($arr_subscribe_info['region']);
			}	
			$email_content .= ', ' . $arr_subscribe_info['street'];
			$email_content .= ', д. ' . $arr_subscribe_info['house'];
			if (($config_DomainEnd == 'by') && ($arr_subscribe_info['korpus'])) {
				$email_content .= ' к. ' . $arr_subscribe_info['korpus'];
			}
			if ($arr_subscribe_info['flat']) {
				$email_content .= ', кв. ' . $arr_subscribe_info['flat'];
			}
			$email_content .= '</td>' . "\n";
			$email_content .= '</tr>' . "\n";
			$email_content .= '<tr>' . "\n";
			$email_content .= '	<td nowrap valign="top" style="font-family:Arial,sans-serif;font-size:14px;line-height:19px;">Способ доставки:</td>' . "\n";
			$email_content .= '	<td valign="top" align="right" style="font-family:Arial,sans-serif;font-size:14px;line-height:21px;">Почтой</td>' . "\n";
			$email_content .= '</tr>' . "\n";
			$email_content .= '<tr>' . "\n";
			$email_content .= '	<td nowrap valign="top" style="font-family:Arial,sans-serif;font-size:14px;line-height:19px;">Оплачен:</td>' . "\n";
			$email_content .= '	<td valign="top" align="right" style="font-family:Arial,sans-serif;font-size:14px;line-height:21px;">' . $arr_subscribe_info['paid_datetime'] . '</td>' . "\n";
			$email_content .= '</tr>' . "\n";
			$email_content .= '<tr>' . "\n";
			if ($arr_subscribe_info['promocode_discount_percent']) {
				$email_content .= '	<td nowrap valign="top" style="font-family:Arial,sans-serif;font-size:14px;line-height:19px;">Стоимость без скидки:</td>' . "\n";
			} else {
				$email_content .= '	<td nowrap valign="top" style="font-family:Arial,sans-serif;font-size:14px;line-height:19px;">Стоимость:</td>' . "\n";
			}
			$email_content .= '	<td valign="top" align="right" style="font-family:Arial,sans-serif;font-size:14px;line-height:21px;"><b>' . $arr_subscribe_info['price'] . ' руб.</b></td>' . "\n";
			$email_content .= '</tr>' . "\n";
			if ($arr_subscribe_info['promocode_discount_percent']) {
				$email_content .= '<tr>' . "\n";
				$email_content .= '	<td nowrap valign="top" style="font-family:Arial,sans-serif;font-size:14px;line-height:19px;">Скидка:</td>' . "\n";
				$email_content .= '	<td valign="top" align="right" style="font-family:Arial,sans-serif;font-size:14px;line-height:21px;"><b>' . $arr_subscribe_info['promocode_discount_percent'] . '%</b></td>' . "\n";
				$email_content .= '</tr>' . "\n";
			}
			if ($arr_subscribe_info['price_discounted']) {
				$email_content .= '<tr>' . "\n";
				$email_content .= '	<td nowrap valign="top" style="font-family:Arial,sans-serif;font-size:14px;line-height:19px;">Итого:</td>' . "\n";
				$email_content .= '	<td valign="top" align="right" style="font-family:Arial,sans-serif;font-size:14px;line-height:21px;"><b>' . $arr_subscribe_info['price_discounted'] . ' руб.</b></td>' . "\n";
				$email_content .= '</tr>' . "\n";
			}
			if ($arr_subscribe_info['comment']) {
				$email_content .= '<tr>' . "\n";
				$email_content .= '	<td colspan="2" nowrap valign="top" style="font-family:Arial,sans-serif;font-size:14px;line-height:19px;"><hr></td>' . "\n";
				$email_content .= '</tr>' . "\n";
				$email_content .= '<tr>' . "\n";
				$email_content .= '	<td nowrap valign="top" style="font-family:Arial,sans-serif;font-size:14px;line-height:19px;">Комментарий к заказу:</td>' . "\n";
				$email_content .= '	<td valign="top" align="right" style="font-family:Arial,sans-serif;font-size:14px;line-height:21px;">' . $arr_subscribe_info['comment'] . '</td>' . "\n";
				$email_content .= '</tr>' . "\n";
			}

			$email_content .= '</table>' . "\n";
			$email_content .= '</div>' . "\n";
			if ( mail_PHPMailer($arr_subscribe_info['email'],  "Уведомление о подписке на журнал «" . remove_br($arr_subscribe_info['magazine_name']) . "»", $email_content) ) {
				if ($arr_subscribe_info['month_start_is_changed']) {
					$email_content .= '<div style="font-family:Arial,sans-serif;font-size:20px;font-weight:bold;line-height:29px;margin-top:10px;color:red;">ВНИМАНИЕ! Подписка с другого периода!</div>';
				}
				mail_PHPMailer($config_email_copy_subscribe,  "Подписка на «" . remove_br($arr_subscribe_info['magazine_name']) . "» от " . $arr_subscribe_info['fio'], $email_content);
				return true;
			} else {
				echo "Ошибка при отправке письма.";
			}
		}
	}
	return false;
}
//---------------------------------------------
function mail_PHPMailer($email, $email_subject, $email_content) {
	global $config_DomainEnd;

	$mail = new PHPMailer(true);								// Passing `true` enables exceptions
	try {
		//Server settings
		$mail->CharSet = 'UTF-8';
		$mail->isSMTP();										// Set mailer to use SMTP
		$mail->Host = 'relay.toloka.com';
		$mail->SMTPAuth = false;
		$mail->SMTPSecure = 'tls';								// Enable TLS encryption, `ssl` also accepted
		$mail->Port = 25;

		//Recipients
		$mail->setFrom("info@toloka24.$config_DomainEnd", 'Толока');
		$mail->addAddress($email);								// Name is optional
		$mail->addReplyTo("info@toloka24.$config_DomainEnd", 'Толока');

		//Content
		$mail->isHTML(true);									// Set email format to HTML
		$mail->Subject = $email_subject;
		$mail->Body    = $email_content;

		$mail->send();
		return true;
	} catch (Exception $e) {
		echo '<p>Message could not be sent. Mailer Error: ', $mail->ErrorInfo . '</p>';
	}	
	return false;
}
//---------------------------------------------
function remove_br($text) {
	$text = str_replace("<br>", "", $text);
	$text = str_replace("<br />", "", $text);
	$text = str_replace("<br/>", "", $text);
	return $text;
}
//---------------------------------------------
function set_subscribe_finish() {
	global $config_DomainEnd;
	$success_info = array();
	if ($config_DomainEnd == 'by') {
		$banking_token = filter_input(INPUT_GET, 'token');
	} else {
		$banking_token = filter_input(INPUT_GET, 'orderId');
	}
	$error_text = '';
	$magazine_name = '';
	$pay_again_url = '';
	if ($banking_token == "") {
		$error_text = "Пустой номер заказа";
	} else {
		$db_link = @connecDB();
		$mysqli_result = mysqli_query($db_link, "SELECT * FROM subscribe WHERE banking_token='" . mysqli_real_escape_string($db_link, $banking_token) . "'");
		$paid = "";
		$magazine_id = "";
		$subscribe_id = "";
		$email = "";
		while ($tmp_row = mysqli_fetch_assoc($mysqli_result)) {
			$paid = $tmp_row["paid"];
			$magazine_id = $tmp_row["magazine_id"];
			$subscribe_id = $tmp_row["id"];
			$email = $tmp_row["email"];
			$monthes = $tmp_row["monthes"];
			$comment = $tmp_row["comment"];
			break;
		}
		if ($magazine_id == '') {
			$error_text = 'Такого заказа не существует';		
		}
		if ($paid) {
			$error_text = "Эта подписка уже была успешно оплачена.";
		} else {
			if ($magazine_id) {
				$orderStatusResult = rest_getOrderStatusExtended();

				if (($config_DomainEnd == 'by') && ($orderStatusResult["checkout"]["status"] == 'error')) {
					$error_text = $orderStatusResult["checkout"]["message"];
				}
				if (($config_DomainEnd == 'ru') && $orderStatusResult['errorCode']) {
					$error_text = $orderStatusResult['errorMessage'] . ' (код ошибки ' . $orderStatusResult['errorCode'] . ')';
				}

				$flagSuccess = false;
				if (($config_DomainEnd == 'by') && ($orderStatusResult["checkout"]["status"] == 'successful')) {
					$flagSuccess = true;
				}
				if (($config_DomainEnd == 'ru') && ($orderStatusResult['paymentAmountInfo']['paymentState'] == 'DEPOSITED')) {
					$flagSuccess = true;
				}
				if ($flagSuccess) {
					$arr_magazine_info = get_magazine_info($magazine_id);
					$magazine_id = $arr_magazine_info["id"];
					$magazine_name = remove_br($arr_magazine_info["name"]);

					$payed_id_order = $tmp_row["id_order"];
					if ($tmp_row["pay_try_num"]) {
						$payed_id_order = $payed_id_order . '-' . $tmp_row["pay_try_num"];
					}

					$months = array( 1 => 'Январь' , 'Февраль' , 'Март' , 'Апрель' , 'Май' , 'Июнь' , 'Июль' , 'Август' , 'Сентябрь' , 'Октябрь' , 'Ноябрь' , 'Декабрь' );			
					$month_start = $tmp_row["month_start"];
					$month_start_text = $months[$month_start];
					$month_end = $month_start + $monthes - 1;
					if ($month_end > 12) {
						$month_end = $month_end - 12;
					}
					$month_start_text .= '-' . $months[$month_end];
					$month_releases_num = $tmp_row["month_releases_num"];		

					$address_text = $tmp_row['zip'];
					$address_text .= ', ' . $tmp_row['city'];
					$address_text .= ', ' . $tmp_row['street'];
					$address_text .= ', д. ' . $tmp_row['house'];
					if ($tmp_row['korpus']) {
						$address_text .= ' к. ' . $tmp_row['korpus'];
					}
					if ($tmp_row['flat']) {
						$address_text .= ', кв. ' . $tmp_row['flat'];
					}
					
					if ($config_DomainEnd == 'by') {
						$approvedAmount = $orderStatusResult["checkout"]["order"]["amount"] / 100;
						$banking_transactionId = $orderStatusResult["checkout"]["gateway_response"]["payment"]["uid"];
						$banking_cardNumber = rest_getOrderStatusExtended2($banking_transactionId);
						$banking_approvalCode = $orderStatusResult["checkout"]["gateway_response"]["payment"]["auth_code"];
					} else {
						$approvedAmount = $orderStatusResult['paymentAmountInfo']['approvedAmount'] / 100;
						$banking_transactionId = '';
						$banking_cardNumber = $orderStatusResult['cardAuthInfo']['pan'];
						$banking_approvalCode = $orderStatusResult['cardAuthInfo']['approvalCode'];
					}		
					$success_info = array( 'id_order' => $payed_id_order, 
											'monthes' => $monthes, 
											'month_releases_num' => $month_releases_num, 
											'month_start_text' => $month_start_text, 
											'address_text' => $address_text, 
											'fio' => $tmp_row["fio"], 
											'magazine_name' => $magazine_name, 
											'approvedAmount' => $approvedAmount, 
											'banking_transactionId' => $banking_transactionId,
											'comment' => $comment
									);
									
					$mysqli_result = mysqli_query($db_link, "UPDATE subscribe SET id_order='$payed_id_order', banking_transactionId='$banking_transactionId', banking_cardNumber='$banking_cardNumber', banking_approvalCode='$banking_approvalCode', paid='1', paid_datetime='" . date('Y-m-d H:i:s') . "' WHERE banking_token='" . mysqli_real_escape_string($db_link, $banking_token) . "'");

					if (!$mysqli_result) {
						$error_text = "DB error UPDATE paid";
					} else {
						createXML($payed_id_order);
						if (!email_subscribe_info($subscribe_id)) {
							$error_text = "Ошибка при отправке письма.";
						}
					}
				} else {
					if ($config_DomainEnd == 'by') {
						$error_text = "Состояние платежа - " . $orderStatusResult["checkout"]["message"];
					} else {
						$error_text = "Состояние платежа - " . $orderStatusResult['paymentAmountInfo']['paymentState'] . ' (' . $orderStatusResult['actionCodeDescription'] . ')';
					}
					$arr_subscribe_info = get_subscribe_info($subscribe_id);
					$pay_try_num = ++$arr_subscribe_info["pay_try_num"];
					$new_id_order = $arr_subscribe_info["id_order"] . '-' . $pay_try_num;
					$registerOrderResult = rest_RegisterOrder($arr_subscribe_info["id_order"], '-' . $pay_try_num);

					$new_banking_token = '';
					if (($config_DomainEnd == 'by') && empty($registerOrderResult["errors"])) {
						$new_banking_token = $registerOrderResult["checkout"]["token"];
						$pay_again_url = $registerOrderResult["checkout"]["redirect_url"];
					}
					if (($config_DomainEnd == 'ru') && empty($registerOrderResult["errorCode"])) {
						$new_banking_token = $registerOrderResult["orderId"];
						$pay_again_url = $registerOrderResult["formUrl"];
					}
					if ($new_banking_token) {
						$mysqli_result = mysqli_query($db_link, "UPDATE subscribe SET pay_try_num='$pay_try_num', banking_token='$new_banking_token', banking_formUrl='$pay_again_url' WHERE id='$subscribe_id' AND deleted='0'");
						if (!$mysqli_result) {
							$error_text = "DB error UPDATE orderId";
						}
					} else {
						if ($config_DomainEnd == 'by') {
							$error_text = "Ошибка: " . $registerOrderResult["message"];
						} else {
							$error_text = "Ошибка #" . $registerOrderResult["errorCode"] . ": " . $registerOrderResult["errorMessage"];
						}
					}
				}
			}
			if (@$db_link) mysqli_close($db_link);
		}
	}

	$result = array(
		'magazine_id' => $magazine_id,
		'magazine_name' => $magazine_name,
		'success_info' => $success_info,
		'email' => $email,
		'pay_again_url' => $pay_again_url,
		"error_text" => $error_text
	);
	return $result;
}
//---------------------------------------------
function get_all_group_info() {
	$arr_groups = array();
	$db_link = @connecDB();
	$mysqli_result = mysqli_query($db_link, "SELECT * FROM groups WHERE deleted='0' ORDER BY list_order_value DESC");
	while ($tmp_row = mysqli_fetch_assoc($mysqli_result)) {
		$arr_magazines = array();
		$group_id = $tmp_row["id"];
		$mysqli_result2 = mysqli_query($db_link, "SELECT magazine.id, magazine.name, magazine.url FROM groups_magazines LEFT JOIN magazine ON groups_magazines.magazine_group_id='$group_id' AND groups_magazines.magazine_id=magazine.id AND groups_magazines.deleted=0 AND magazine.deleted=0");
		while ($tmp_row2 = mysqli_fetch_assoc($mysqli_result2)) {
			if ($tmp_row2["id"]) {
				$arr_magazines[] = array("id"=>$tmp_row2["id"], "name"=>$tmp_row2["name"], "url"=>$tmp_row2["url"]);
			}
		}
		$tmp_arr = array("id"=>$tmp_row["id"], "name"=>$tmp_row["name"], "url"=>$tmp_row["url"], "list_order_value"=>$tmp_row["list_order_value"], "magazines"=>$arr_magazines);
		$arr_groups[] = $tmp_arr;
	}
	if (@$mysqli_result) mysqli_free_result($mysqli_result);
	if (@$db_link) mysqli_close($db_link);
	return $arr_groups;
}
//---------------------------------------------
function get_group_info($group_id = '', $group_url = '') {
	$arr_group_info = array();
	if ($group_id == '') {
		$group_id =  filter_input(INPUT_POST, 'group');
	}
	if ($group_id != '' || $group_url != '') {
		$db_link = @connecDB();
		if ($group_id != '') {
			$query = "SELECT * FROM groups WHERE id='" . mysqli_real_escape_string($db_link, $group_id) ."' AND deleted='0'";
		} else {
			$query = "SELECT * FROM groups WHERE url='" . mysqli_real_escape_string($db_link, $group_url) . "' AND deleted='0'";
		}
		$mysqli_result = mysqli_query($db_link, $query);
		while ($tmp_row = mysqli_fetch_assoc($mysqli_result)) {
			$group_id = $tmp_row["id"];
			$arr_group_info["id"] = $tmp_row["id"];
			$arr_group_info["url"] = $tmp_row["url"];
			$arr_group_info["name"] = $tmp_row["name"];
			$arr_group_info["subtitle"] = $tmp_row["subtitle"];
			$arr_group_info["description"] = $tmp_row["description"];
			$arr_group_info["list_order_value"] = $tmp_row["list_order_value"];
			$arr_group_info["html_title"] = $tmp_row["html_title"];
			$arr_group_info["meta_description"] = $tmp_row["meta_description"];
			$arr_group_info["meta_keywords"] = $tmp_row["meta_keywords"];
			break;
		}
		$arr_magazines = array();
		$sum_6_monthes = 0;
		$sum_12_monthes = 0;
		$group_sum_6_monthes = 0;
		$group_sum_12_monthes = 0;
		$mysqli_result = mysqli_query($db_link, "SELECT magazine.name, magazine.id, magazine.price_6_monthes, magazine.price_12_monthes, groups_magazines.magazine_group_id, groups_magazines.group_price_6_monthes, groups_magazines.group_price_12_monthes FROM magazine LEFT JOIN groups_magazines ON groups_magazines.magazine_group_id='" . mysqli_real_escape_string($db_link, $group_id) . "' AND groups_magazines.magazine_id=magazine.id AND magazine.deleted=0 AND groups_magazines.deleted=0 ORDER BY id");
		while ($tmp_row = mysqli_fetch_assoc($mysqli_result)) {
			$tmp_arr[] = array("name"=>$tmp_row["name"], "id"=>$tmp_row["id"], "price_6_monthes"=>$tmp_row["price_6_monthes"], "price_12_monthes"=>$tmp_row["price_12_monthes"], "magazine_group_id"=>$tmp_row["magazine_group_id"], "group_price_6_monthes"=>$tmp_row["group_price_6_monthes"], "group_price_12_monthes"=>$tmp_row["group_price_12_monthes"]);

			if ($tmp_row["magazine_group_id"]) {
				$sum_6_monthes += $tmp_row['price_6_monthes'];
				$sum_12_monthes += $tmp_row['price_12_monthes']; 
				if ($tmp_row["group_price_6_monthes"]) {
					$group_sum_6_monthes += $tmp_row["group_price_6_monthes"];
				} else {
					$group_sum_6_monthes += $tmp_row['price_6_monthes'];
				}
				if ($tmp_row["group_price_12_monthes"]) {
					$group_sum_12_monthes += $tmp_row["group_price_12_monthes"];
				} else {
					$group_sum_12_monthes += $tmp_row['price_12_monthes'];
				}
			}
		}
		if ($sum_6_monthes) {
			$discount_group_sum_6_monthes = round(($sum_6_monthes - $group_sum_6_monthes)/$sum_6_monthes*100, 2);
		}
		if ($sum_12_monthes) {
			$discount_group_sum_12_monthes = round(($sum_12_monthes - $group_sum_12_monthes)/$sum_12_monthes*100, 2);
		}

		$arr_group_info["magazines"] = $tmp_arr;
		$arr_group_info["sum_6_monthes"] = $sum_6_monthes;
		$arr_group_info["sum_12_monthes"] = $sum_12_monthes;
		$arr_group_info["group_sum_6_monthes"] = $group_sum_6_monthes;
		$arr_group_info["group_sum_12_monthes"] = $group_sum_12_monthes;
		$arr_group_info["discount_group_sum_6_monthes"] = $discount_group_sum_6_monthes;
		$arr_group_info["discount_group_sum_12_monthes"] = $discount_group_sum_12_monthes;

		$arr_group_info["price_6_monthes"] = $group_sum_6_monthes;
		$arr_group_info["price_12_monthes"] = $group_sum_12_monthes;

		if (@$mysqli_result) mysqli_free_result($mysqli_result);
		if (@$db_link) mysqli_close($db_link);
	}
	return $arr_group_info;
}
//---------------------------------------------
function set_group_info() {
	$arr_group_info = array();
	$act = filter_input(INPUT_POST, 'act');
	if ($act == 'new_group' || $act == 'edit_group') {
		$name = filter_input(INPUT_POST, 'name');
		$url = filter_input(INPUT_POST, 'url');
		$subtitle = filter_input(INPUT_POST, 'subtitle');
		$description = filter_input(INPUT_POST, 'description');
		$list_order_value = filter_input(INPUT_POST, 'list_order_value');
		if ($list_order_value == "") {
			$list_order_value = 0;
		}
		$html_title = filter_input(INPUT_POST, 'html_title');
		$meta_description = filter_input(INPUT_POST, 'meta_description');
		$meta_keywords = filter_input(INPUT_POST, 'meta_keywords');
		$arr_magazines = $_POST['magazines'];
		$arr_group_price_6_monthes = $_POST['group_price_6_monthes'];
		$arr_group_price_12_monthes = $_POST['group_price_12_monthes'];
		$db_link = @connecDB();
		if ($act == 'edit_group') {
			$id = filter_input(INPUT_POST, 'id');
			$tmp_query = "UPDATE groups SET		name='" . mysqli_real_escape_string($db_link, $name) . "', 
												url='" . mysqli_real_escape_string($db_link, $url) . "', 
												subtitle='" . mysqli_real_escape_string($db_link, $subtitle) . "', 
												description='" . mysqli_real_escape_string($db_link, $description) . "', 
												list_order_value='" . mysqli_real_escape_string($db_link, $list_order_value) . "', 
												html_title='" . mysqli_real_escape_string($db_link, $html_title) . "', 
												meta_description='" . mysqli_real_escape_string($db_link, $meta_description) . "', 
												meta_keywords='" . mysqli_real_escape_string($db_link, $meta_keywords) . "' 
												WHERE id='" . mysqli_real_escape_string($db_link, $id) . "' AND deleted='0'";
		}
		if ($act == 'new_group') {
			$tmp_query = "INSERT INTO groups 	(name, 
												url, 
												subtitle, 
												description, 
												list_order_value, 
												html_title, 
												meta_description, 
												meta_keywords) VALUES('" . 
												mysqli_real_escape_string($db_link, $name) . "', '" . 
												mysqli_real_escape_string($db_link, $url) . "', '" . 
												mysqli_real_escape_string($db_link, $subtitle) . "', '" . 
												mysqli_real_escape_string($db_link, $description) . "', '" . 
												mysqli_real_escape_string($db_link, $list_order_value) . "', '" . 
												mysqli_real_escape_string($db_link, $html_title) . "', '" . 
												mysqli_real_escape_string($db_link, $meta_description) . "', '" . 
												mysqli_real_escape_string($db_link, $meta_keywords) . "')";
		}
		if ($tmp_query != '') {
			$mysqli_result = mysqli_query($db_link, $tmp_query);
			if (!$mysqli_result) {
				echo "DB error: " . mysqli_error($db_link);
			} else {
				if ($act == 'new_group') {
					$id = mysqli_insert_id($db_link);
				}
				save_group_cover_pic($id);
				update_group_magazines($id, $arr_magazines, $arr_group_price_6_monthes, $arr_group_price_12_monthes);
				return $id;
			}
		}
		if (@$db_link) mysqli_close($db_link);
	}
	return false;
}
//---------------------------------------------
function delete_group() {
	$errors = "";
	$group_id = filter_input(INPUT_POST, 'group_id');
	if (group_id) {
		$db_link = @connecDB();
		$mysqli_result = mysqli_query($db_link, "UPDATE groups SET deleted='1', deleted_datetime='" . date('Y-m-d H:i:s') ."' WHERE id='" . mysqli_real_escape_string($db_link, $group_id) . "' AND deleted='0'");
		if (!$mysqli_result) {
			$errors = "DB error: " . mysqli_error($db_link);
		} else {
			$status = "ok";
		}
		if (@$db_link) mysqli_close($db_link);
	}
	$result = array(
		'status' => $status,
		"errors" => $errors
	);
	return json_encode($result);
}
//---------------------------------------------
function update_group_magazines($group_id, $arr_magazines, $arr_group_price_6_monthes, $arr_group_price_12_monthes) {
	if ($arr_magazines == NULL) {
		$arr_magazines = array();
	}
	$db_link = @connecDB();
	$mysqli_result = mysqli_query($db_link, "SELECT id FROM magazine WHERE deleted=0");
	while ($tmp_row = mysqli_fetch_assoc($mysqli_result)) {
		$arr_all_magazines[] = $tmp_row["id"];
	}
	$i = 0;
	foreach ($arr_all_magazines as $magazine_id) {
		if (in_array($magazine_id, $arr_magazines)) {
			$group_price_6_monthes = $arr_group_price_6_monthes[$i];
			if ($group_price_6_monthes == '') {
				$group_price_6_monthes = 0;
			}
			$group_price_12_monthes = $arr_group_price_12_monthes[$i];
			if ($group_price_12_monthes == '') {
				$group_price_12_monthes = 0;
			}
			$mysqli_result = mysqli_query($db_link, "INSERT INTO groups_magazines (magazine_group_id, magazine_id, group_price_6_monthes, group_price_12_monthes, deleted, deleted_datetime) VALUES('$group_id', '$magazine_id', '$group_price_6_monthes', '$group_price_12_monthes', '0', NULL) ON DUPLICATE KEY UPDATE group_price_6_monthes='$group_price_6_monthes', group_price_12_monthes='$group_price_12_monthes', deleted='0', deleted_datetime=NULL");
		} else {
			$row_id = 0;
			$mysqli_result = mysqli_query($db_link, "SELECT * FROM groups_magazines WHERE magazine_group_id='$group_id' AND magazine_id='$magazine_id'");
			while ($tmp_row = mysqli_fetch_assoc($mysqli_result)) {
				$row_id = $tmp_row["id"];
				break;
			}
			if ($row_id) {
				$mysqli_result = mysqli_query($db_link, "UPDATE groups_magazines SET deleted='1', group_price_6_monthes=0, group_price_12_monthes=0, deleted_datetime='" . date('Y-m-d H:i:s') ."' WHERE id='$row_id'");
			}
		}
		$i++;
	}
	if (@$db_link) mysqli_close($db_link);
	return true;
}
//---------------------------------------------
function get_agent_info($agent_id) {
	$arr_agent_info = array();
	if ($agent_id != '') {
		$db_link = @connecDB();
		$query = "SELECT * FROM agent WHERE id='" . mysqli_real_escape_string($db_link, $agent_id) ."' AND deleted='0'";
		$mysqli_result = mysqli_query($db_link, $query);
		while ($tmp_row = mysqli_fetch_assoc($mysqli_result)) {
			$arr_agent_info["id"] = $tmp_row["id"];
			$arr_agent_info["login"] = $tmp_row["login"];
			$arr_agent_info["pass"] = $tmp_row["pass"];
			$arr_agent_info["promocode"] = $tmp_row["promocode"];
			$arr_agent_info["promocode_discount_percent"] = $tmp_row["promocode_discount_percent"];
			$arr_agent_info["name_first"] = $tmp_row["name_first"];
			$arr_agent_info["name_last"] = $tmp_row["name_last"];
			$arr_agent_info["name_middle"] = $tmp_row["name_middle"];
			$arr_agent_info["email"] = $tmp_row["email"];
			$arr_agent_info["tel"] = $tmp_row["tel"];
			$arr_agent_info["account_num_rasschetni"] = $tmp_row["account_num_rasschetni"];
			$arr_agent_info["bank_name"] = $tmp_row["bank_name"];
			$arr_agent_info["account_num_correspondent"] = $tmp_row["account_num_correspondent"];
			$arr_agent_info["bik"] = $tmp_row["bik"];
			$arr_agent_info["credit_card_num"] = $tmp_row["credit_card_num"];
			$arr_agent_info["job"] = $tmp_row["job"];
			$arr_agent_info["job_position"] = $tmp_row["job_position"];
			$arr_agent_info["passport_series"] = $tmp_row["passport_series"];
			$arr_agent_info["passport_num"] = $tmp_row["passport_num"];
			$arr_agent_info["passport_issue_who"] = $tmp_row["passport_issue_who"];
			$arr_agent_info["passport_issue_code"] = $tmp_row["passport_issue_code"];
			$arr_agent_info["passport_issue_date"] = $tmp_row["passport_issue_date"];
			$arr_agent_info["inn"] = $tmp_row["inn"];
			$arr_agent_info["snils"] = $tmp_row["snils"];
			$arr_agent_info["region"] = $tmp_row["region"];
			$arr_agent_info["district"] = $tmp_row["district"];
			$arr_agent_info["city_type"] = $tmp_row["city_type"];
			$arr_agent_info["city"] = $tmp_row["city"];
			$arr_agent_info["city_name"] = $tmp_row["city_name"];
			$arr_agent_info["street_type"] = $tmp_row["street_type"];
			$arr_agent_info["street"] = $tmp_row["street"];
			$arr_agent_info["street_name"] = $tmp_row["street_name"];
			$arr_agent_info["house"] = $tmp_row["house"];
			$arr_agent_info["korpus"] = $tmp_row["korpus"];
			$arr_agent_info["flat"] = $tmp_row["flat"];
			$arr_agent_info["zip"] = $tmp_row["zip"];
			$arr_agent_info["region2"] = $tmp_row["region2"];
			$arr_agent_info["district2"] = $tmp_row["district2"];
			$arr_agent_info["city_type2"] = $tmp_row["city_type2"];
			$arr_agent_info["city2"] = $tmp_row["city2"];
			$arr_agent_info["city_name2"] = $tmp_row["city_name2"];
			$arr_agent_info["street_type2"] = $tmp_row["street_type2"];
			$arr_agent_info["street2"] = $tmp_row["street2"];
			$arr_agent_info["street_name2"] = $tmp_row["street_name2"];
			$arr_agent_info["house2"] = $tmp_row["house2"];
			$arr_agent_info["korpus2"] = $tmp_row["korpus2"];
			$arr_agent_info["flat2"] = $tmp_row["flat2"];
			$arr_agent_info["zip2"] = $tmp_row["zip2"];
			$arr_agent_info["comment"] = $tmp_row["comment"];
			break;
		}
		if (@$mysqli_result) mysqli_free_result($mysqli_result);
		if (@$db_link) mysqli_close($db_link);
	}
	return $arr_agent_info;
}
//---------------------------------------------
function add_br($text) {
	if ($text != '') {
		return '<li>' . $text . '</li>';
	}
}
//---------------------------------------------
function set_agent_info() {
	global $error_description, $config_DomainEnd;
	$arr_agent_info = array();
	$act = filter_input(INPUT_POST, 'act');
	if ($act == 'new_agent' || $act == 'edit_agent') {		
		$name_last = filter_input(INPUT_POST, 'name_last');
		$error_description .= add_br(checkErrorFIO_name_last($name_last));

		$name_first = filter_input(INPUT_POST, 'name_first');
		$error_description .= add_br(checkErrorFIO_name_first($name_first));

		$name_middle = filter_input(INPUT_POST, 'name_middle');
		$error_description .= add_br(checkErrorFIO_name_middle($name_middle));

		$passport_series = filter_input(INPUT_POST, 'passport_series');
		$error_description .= add_br(checkErrorPassportSeries($passport_series));

		$passport_num = filter_input(INPUT_POST, 'passport_num');
		$error_description .= add_br(checkErrorPassportNum($passport_num));

		$passport_issue_who = filter_input(INPUT_POST, 'passport_issue_who');
		$error_description .= add_br(checkErrorPassportIssueWho($passport_issue_who));

		$passport_issue_code = filter_input(INPUT_POST, 'passport_issue_code');
		$error_description .= add_br(checkErrorPassportIssueCode($passport_issue_code));

		$passport_issue_date = filter_input(INPUT_POST, 'passport_issue_date');
		$error_description .= add_br(checkErrorPassportIssueDate($passport_issue_date));

		$inn = filter_input(INPUT_POST, 'inn');
		$error_description .= add_br(checkErrorInn($inn));

		$snils = filter_input(INPUT_POST, 'snils');
		$error_description .= add_br(checkErrorSnils($snils));

		$region = filter_input(INPUT_POST, 'region');
		$error_description .= add_br(checkErrorRegion($region));

		$district = filter_input(INPUT_POST, 'district');

		$city_type = filter_input(INPUT_POST, 'city_type');

		$city = filter_input(INPUT_POST, 'city');
		$error_description .= add_br(checkErrorCity($city));

		$city_name = filter_input(INPUT_POST, 'city_name');

		$street_type = filter_input(INPUT_POST, 'street_type');

		$street = filter_input(INPUT_POST, 'street');
		$error_description .= add_br(checkErrorStreet($street));

		$street_name = filter_input(INPUT_POST, 'street_name');

		$house = filter_input(INPUT_POST, 'house');
		$error_description .= add_br(checkErrorHouse($house));
		
		$korpus = filter_input(INPUT_POST, 'korpus');

		$flat = filter_input(INPUT_POST, 'flat');

		$zip = filter_input(INPUT_POST, 'zip');
		$error_description .= add_br(checkErrorZip($zip));


		if(filter_input(INPUT_POST, 'address2')) {
			$region2 = filter_input(INPUT_POST, 'region2');
			$error_description .= add_br(checkErrorRegion($region2));
	
			$district2 = filter_input(INPUT_POST, 'district2');

			$city_type2 = filter_input(INPUT_POST, 'city_type2');

			$city2 = filter_input(INPUT_POST, 'city2');
			$error_description .= add_br(checkErrorCity($city2));

			$city_name2 = filter_input(INPUT_POST, 'city_name2');

			$street_type2 = filter_input(INPUT_POST, 'street_type2');

			$street2 = filter_input(INPUT_POST, 'street2');
			$error_description .= add_br(checkErrorStreet($street2));

			$street_name2 = filter_input(INPUT_POST, 'street_name2');

			$house2 = filter_input(INPUT_POST, 'house2');
			$error_description .= add_br(checkErrorHouse($house2));
		
			$korpus2 = filter_input(INPUT_POST, 'korpus2');
			
			$flat2 = filter_input(INPUT_POST, 'flat2');
		
			$zip2 = filter_input(INPUT_POST, 'zip2');
			$error_description .= add_br(checkErrorZip($zip2));
		}

		$account_num_rasschetni = filter_input(INPUT_POST, 'account_num_rasschetni');
		$error_description .= add_br(checkErrorBankAccountRasschetni($account_num_rasschetni));

		$bank_name = filter_input(INPUT_POST, 'bank_name');
		$error_description .= add_br(checkErrorBankName($bank_name));

		$account_num_correspondent = filter_input(INPUT_POST, 'account_num_correspondent');
		$error_description .= add_br(checkErrorBankAccountCorrespondent($account_num_correspondent));

		$bik = filter_input(INPUT_POST, 'bik');
		$error_description .= add_br(checkErrorBIK($bik));

		$credit_card_num = filter_input(INPUT_POST, 'credit_card_num');

		$job = filter_input(INPUT_POST, 'job');

		$job_position = filter_input(INPUT_POST, 'job_position');

		$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
		$error_description .= add_br(checkErrorEmail($email));

		$tel = filter_input(INPUT_POST, 'tel');
		$error_description .= add_br(checkErrorTel($tel));

		$comment = filter_input(INPUT_POST, 'comment');

		if(!filter_input(INPUT_POST, 'agree')) {
			$error_description .= add_br("Необходимо ознакомиться с Договором оферты и Политикой обработки персональных данных и принять их.");
		}

		$db_link = @connecDB();

		if ($error_description == '') {
			if ($config_DomainEnd == 'ru') {
				$normalized_tel_phone = trim(filter_input(INPUT_POST, 'normalized_tel_phone'));
				$normalized_tel_type = trim(filter_input(INPUT_POST, 'normalized_tel_type'));
				$normalized_tel_country_code = trim(filter_input(INPUT_POST, 'normalized_tel_country_code'));
				$normalized_tel_city_code = trim(filter_input(INPUT_POST, 'normalized_tel_city_code'));
				$normalized_tel_number = trim(filter_input(INPUT_POST, 'normalized_tel_number'));
				$normalized_tel_extension = trim(filter_input(INPUT_POST, 'normalized_tel_extension'));
				$normalized_tel_provider = trim(filter_input(INPUT_POST, 'normalized_tel_provider'));
				$normalized_tel_region = trim(filter_input(INPUT_POST, 'normalized_tel_region'));
				$normalized_tel_timezone = trim(filter_input(INPUT_POST, 'normalized_tel_timezone'));
				$normalized_tel_qc_conflict = trim(filter_input(INPUT_POST, 'normalized_tel_qc_conflict'));
				$normalized_tel_qc = trim(filter_input(INPUT_POST, 'normalized_tel_qc'));
			} else {
				$normalized_tel_phone = '';
				$normalized_tel_type = '';
				$normalized_tel_country_code = '';
				$normalized_tel_city_code = '';
				$normalized_tel_number = '';
				$normalized_tel_extension = '';
				$normalized_tel_provider = '';
				$normalized_tel_region = '';
				$normalized_tel_timezone = '';
				$normalized_tel_qc_conflict = '';
				$normalized_tel_qc = '';
			}

			if ($act == 'edit_agent') {
				$id = filter_input(INPUT_POST, 'id');
				$tmp_query = "UPDATE agent SET		login='" . mysqli_real_escape_string($db_link, $login) . "', 
													pass='" . mysqli_real_escape_string($db_link, $pass) . "', 
													promocode='" . mysqli_real_escape_string($db_link, $promocode) . "', 
													promocode_discount_percent='" . mysqli_real_escape_string($db_link, $promocode_discount_percent) . "', 
													name_first='" . mysqli_real_escape_string($db_link, $name_first) . "', 
													name_last='" . mysqli_real_escape_string($db_link, $name_last) . "', 
													name_middle='" . mysqli_real_escape_string($db_link, $name_middle) . "', 
													email='" . mysqli_real_escape_string($db_link, $email) . "', 
													tel='" . mysqli_real_escape_string($db_link, $tel) . "', 
													account_num_rasschetni='" . mysqli_real_escape_string($db_link, $account_num_rasschetni) . "', 
													bank_name='" . mysqli_real_escape_string($db_link, $bank_name) . "', 
													account_num_correspondent='" . mysqli_real_escape_string($db_link, $account_num_correspondent) . "', 
													bik='" . mysqli_real_escape_string($db_link, $bik) . "', 
													credit_card_num='" . mysqli_real_escape_string($db_link, $credit_card_num) . "', 
													job='" . mysqli_real_escape_string($db_link, $job) . "', 
													job_position='" . mysqli_real_escape_string($db_link, $job_position) . "', 
													passport_series='" . mysqli_real_escape_string($db_link, $passport_series) . "', 
													passport_num='" . mysqli_real_escape_string($db_link, $passport_num) . "', 
													passport_issue_who='" . mysqli_real_escape_string($db_link, $passport_issue_who) . "', 
													passport_issue_code='" . mysqli_real_escape_string($db_link, $passport_issue_code) . "', 
													passport_issue_date='" . mysqli_real_escape_string($db_link, $passport_issue_date) . "', 
													inn='" . mysqli_real_escape_string($db_link, $inn) . "', 
													snils='" . mysqli_real_escape_string($db_link, $snils) . "', 
													region='" . mysqli_real_escape_string($db_link, $region) . "', 
													district='" . mysqli_real_escape_string($db_link, $district) . "', 
													city_type='" . mysqli_real_escape_string($db_link, $city_type) . "', 
													city='" . mysqli_real_escape_string($db_link, $city) . "', 
													city_name='" . mysqli_real_escape_string($db_link, $city_name) . "', 
													street_type='" . mysqli_real_escape_string($db_link, $street_type) . "', 
													street='" . mysqli_real_escape_string($db_link, $street) . "', 
													street_name='" . mysqli_real_escape_string($db_link, $street_name) . "', 
													house='" . mysqli_real_escape_string($db_link, $house) . "', 
													korpus='" . mysqli_real_escape_string($db_link, $korpus) . "', 
													flat='" . mysqli_real_escape_string($db_link, $flat) . "', 
													zip='" . mysqli_real_escape_string($db_link, $zip) . "', 
													comment='" . mysqli_real_escape_string($db_link, $comment) . "', 
													WHERE id='" . mysqli_real_escape_string($db_link, $id) . "' AND deleted='0'";
			}
			if ($act == 'new_agent') {
				$region_code = filter_input(INPUT_POST, 'normalized_street_kladr_id');
				if ($region_code == '') {
					$region_code = '00';
				} else {
					$region_code = substr($region_code, 0, 2);
				}
				$promocode = generateAgentPromocode($name_last . ' ' . $name_first . ' ' . $name_middle, $region_code);
				$promocode_name = 'Промокод агента ' . $name_last . ' ' . $name_first . ' ' . $name_middle;
				$promocode_discount_percent = "5";

				$tmp_query = "INSERT INTO agent 	(login, 
													pass, 
													promocode, 
													promocode_name, 
													promocode_discount_percent, 
													name_first, 
													name_last, 
													name_middle, 
													email, 
													tel, 
													normalized_tel_phone, 
													normalized_tel_type, 
													normalized_tel_country_code, 
													normalized_tel_city_code, 
													normalized_tel_number, 
													normalized_tel_extension, 
													normalized_tel_provider, 
													normalized_tel_region, 
													normalized_tel_timezone, 
													normalized_tel_qc_conflict, 
													normalized_tel_qc, 
													account_num_rasschetni, 
													bank_name, 
													account_num_correspondent, 
													bik, 
													credit_card_num, 
													job, 
													job_position, 
													passport_series, 
													passport_num, 
													passport_issue_who, 
													passport_issue_code, 
													passport_issue_date, 
													inn, 
													snils, 
													region, 
													district, 
													city_type, 
													city, 
													city_name, 
													street_type, 
													street, 
													street_name, 
													house, 
													korpus, 
													flat, 
													zip, 
													region2, 
													district2, 
													city_type2, 
													city2, 
													city_name2, 
													street_type2, 
													street2, 
													street_name2, 
													house2, 
													korpus2, 
													flat2, 
													zip2, 
													comment) VALUES('" . 
													mysqli_real_escape_string($db_link, $login) . "', '" . 
													mysqli_real_escape_string($db_link, $pass) . "', '" . 
													mysqli_real_escape_string($db_link, $promocode) . "', '" . 
													mysqli_real_escape_string($db_link, $promocode_name) . "', '" . 
													mysqli_real_escape_string($db_link, $promocode_discount_percent) . "', '" . 
													mysqli_real_escape_string($db_link, $name_first) . "', '" . 
													mysqli_real_escape_string($db_link, $name_last) . "', '" . 
													mysqli_real_escape_string($db_link, $name_middle) . "', '" . 
													mysqli_real_escape_string($db_link, $email) . "', '" . 
													mysqli_real_escape_string($db_link, $tel) . "', '" . 
													mysqli_real_escape_string($db_link, $normalized_tel_phone) . "', '" . 
													mysqli_real_escape_string($db_link, $normalized_tel_type) . "', '" . 
													mysqli_real_escape_string($db_link, $normalized_tel_country_code) . "', '" . 
													mysqli_real_escape_string($db_link, $normalized_tel_city_code) . "', '" . 
													mysqli_real_escape_string($db_link, $normalized_tel_number) . "', '" . 
													mysqli_real_escape_string($db_link, $normalized_tel_extension) . "', '" . 
													mysqli_real_escape_string($db_link, $normalized_tel_provider) . "', '" . 
													mysqli_real_escape_string($db_link, $normalized_tel_region) . "', '" . 
													mysqli_real_escape_string($db_link, $normalized_tel_timezone) . "', '" . 
													mysqli_real_escape_string($db_link, $normalized_tel_qc_conflict) . "', '" . 
													mysqli_real_escape_string($db_link, $normalized_tel_qc) . "', '" . 
													mysqli_real_escape_string($db_link, $account_num_rasschetni) . "', '" . 
													mysqli_real_escape_string($db_link, $bank_name) . "', '" . 
													mysqli_real_escape_string($db_link, $account_num_correspondent) . "', '" . 
													mysqli_real_escape_string($db_link, $bik) . "', '" . 
													mysqli_real_escape_string($db_link, $credit_card_num) . "', '" . 
													mysqli_real_escape_string($db_link, $job) . "', '" . 
													mysqli_real_escape_string($db_link, $job_position) . "', '" . 
													mysqli_real_escape_string($db_link, $passport_series) . "', '" . 
													mysqli_real_escape_string($db_link, $passport_num) . "', '" . 
													mysqli_real_escape_string($db_link, $passport_issue_who) . "', '" . 
													mysqli_real_escape_string($db_link, $passport_issue_code) . "', '" . 
													mysqli_real_escape_string($db_link, $passport_issue_date) . "', '" . 
													mysqli_real_escape_string($db_link, $inn) . "', '" . 
													mysqli_real_escape_string($db_link, $snils) . "', '" . 
													mysqli_real_escape_string($db_link, $region) . "', '" . 
													mysqli_real_escape_string($db_link, $district) . "', '" . 
													mysqli_real_escape_string($db_link, $city_type) . "', '" . 
													mysqli_real_escape_string($db_link, $city) . "', '" . 
													mysqli_real_escape_string($db_link, $city_name) . "', '" . 
													mysqli_real_escape_string($db_link, $street_type) . "', '" . 
													mysqli_real_escape_string($db_link, $street) . "', '" . 
													mysqli_real_escape_string($db_link, $street_name) . "', '" . 
													mysqli_real_escape_string($db_link, $house) . "', '" . 
													mysqli_real_escape_string($db_link, $korpus) . "', '" . 
													mysqli_real_escape_string($db_link, $flat) . "', '" . 
													mysqli_real_escape_string($db_link, $zip) . "', '" . 
													mysqli_real_escape_string($db_link, $region2) . "', '" . 
													mysqli_real_escape_string($db_link, $district2) . "', '" . 
													mysqli_real_escape_string($db_link, $city_type2) . "', '" . 
													mysqli_real_escape_string($db_link, $city2) . "', '" . 
													mysqli_real_escape_string($db_link, $city_name2) . "', '" . 
													mysqli_real_escape_string($db_link, $street_type2) . "', '" . 
													mysqli_real_escape_string($db_link, $street2) . "', '" . 
													mysqli_real_escape_string($db_link, $street_name2) . "', '" . 
													mysqli_real_escape_string($db_link, $house2) . "', '" . 
													mysqli_real_escape_string($db_link, $korpus2) . "', '" . 
													mysqli_real_escape_string($db_link, $flat2) . "', '" . 
													mysqli_real_escape_string($db_link, $zip2) . "', '" . 
													mysqli_real_escape_string($db_link, $comment) . "')";
			}
			if ($tmp_query != '') {
				$mysqli_result = mysqli_query($db_link, $tmp_query);
				if (!$mysqli_result) {
					if (strpos(mysqli_error($db_link), 'ncorrect datetime value') && strpos(mysqli_error($db_link), 'passport_issue_date')) {
						$error_description .= add_br("Неверная дата в поле 'Кем выдан паспорт'");
					} else {
						$error_description .= add_br("DB error: " . mysqli_error($db_link));
					}
				} else {
					if ($act == 'new_agent') {
						$id = mysqli_insert_id($db_link);
						if (createAgentXML($id) && email_agent_info($id)) {
							deleteAgentInfo($id);
						}
					}
					return $id;
				}
			}
			if (@$db_link) mysqli_close($db_link);
		}
	}
	return false;
}
//---------------------------------------------
function checkErrorLogin($text) {
	$error = '';
	$result = preg_match('/^[a-zA-Z][a-zA-Z0-9-_\.]{2,120}$/u', $text);
	if (!$result) {
		$error = "В логине могут использоваться только английские буквы, цифры и символ подчеркивания. Первым симвлом должна быть буква.";
	}
	if (strlen($text) < 3) {
		$error = "Длина логина должна быть не менее 3-х символов";
	}
	$db_link = @connecDB();
	$mysqli_result = mysqli_query($db_link, "SELECT login FROM agent WHERE login='$text'");
	while ($tmp_row = mysqli_fetch_assoc($mysqli_result)) {
		$error = "Агент с логином '$text' уже существует";
		break;
	}
	if (@$db_link) mysqli_close($db_link);
	return $error;
}
//---------------------------------------------
function checkErrorPassword($text, $text2) {
	$error = '';
	if (($text != '') && ($text != $text2)) {
		$error = "Пароли не совпадают";
	}
	$result = preg_match('/^[a-zA-Z0-9-_\.]{3,120}$/u', $text);
	if (!$result) {
		$error = "В пароле могут использоваться только английские буквы, цифры и символ подчеркивания";
	}
	if (strlen($text) < 6) {
		$error = "Длина пароля должна быть не менее 6-х символов";
	}
	return $error;
}
//---------------------------------------------
function checkErrorFIO_name_last($text) {
	$error = '';
	$result = preg_match('/^[А-ЯЁ][а-яё]{2,}$/u', $text);
	if (!$result) {
		$error = "Фамилия должна начинаться с заглавной буквы и состоять из русских букв";
	}
	if ($text == '') {
		$error = "Введите фамилию";
	}
	return $error;
}
//---------------------------------------------
function checkErrorFIO_name_first($text) {
	$error = '';
	$result = preg_match('/^[А-ЯЁ][а-яё]{2,}$/u', $text);
	if (!$result) {
		$error = "Имя должно начинаться с заглавной буквы и состоять из русских букв";
	}
	if ($text == '') {
		$error = "Введите имя";
	}
	return $error;
}
//---------------------------------------------
function checkErrorFIO_name_middle($text) {
	$error = '';
	$result = preg_match('/^[А-ЯЁ][а-яё]{2,}$/u', $text);
	if (!$result) {
		$error = "Отчество должно начинаться с заглавной буквы и состоять из русских букв";
	}
	if ($text == '') {
		$error = "Введите отчество";
	}
	return $error;
}
//---------------------------------------------
function checkErrorEmail($text) {
	$error = '';
	if ($text == '') {
		$error = "Введен некорректный e-mail";
	}
	return $error;
}
//---------------------------------------------
function checkErrorBankAccountRasschetni($text) {
	$error = '';
	if (strlen($text) == 0) {
		$error = "Введите расчетный счет";
	}
	return $error;
}
//---------------------------------------------
function checkErrorBankAccountCorrespondent($text) {
	$error = '';
	if (strlen($text) == 0) {
		$error = "Введите корреспондентский счет";
	}
	return $error;
}
//---------------------------------------------
function checkErrorBankName($text) {
	$error = '';
	if (strlen($text) == 0) {
		$error = "Введите название банка";
	}
	return $error;
}
//---------------------------------------------
function checkErrorBIK($text) {
	$error = '';
	if (strlen($text) == 0) {
		$error = "Введите БИК";
	}
	return $error;
}
//---------------------------------------------
function checkErrorJob($text) {
	$error = '';
	if (strlen($text) == 0) {
		$error = "Введите место работы";
	}
	return $error;
}
//---------------------------------------------
function checkErrorJobPosition($text) {
	$error = '';
	if (strlen($text) == 0) {
		$error = "Введите должность";
	}
	return $error;
}
//---------------------------------------------
function checkErrorPassportSeries($text) {
	$error = '';
	if (strlen($text) == 0) {
		$error = "Введите серию паспорта";
	}
	return $error;
}
//---------------------------------------------
function checkErrorPassportNum($text) {
	$error = '';
	if (strlen($text) == 0) {
		$error = "Введите номер паспорта";
	}
	return $error;
}
//---------------------------------------------
function checkErrorPassportIssueWho($text) {
	$error = '';
	if (strlen($text) == 0) {
		$error = "Введите название органа выдавшего паспорт";
	}
	return $error;
}
//---------------------------------------------
function checkErrorPassportIssueCode($text) {
	$error = '';
	$result = preg_match('/^[А-ЯЁа-яёa-zA-Z0-9]{3}[-][А-ЯЁа-яёa-zA-Z0-9]{3}$/u', $text);
	if (!$result) {
		$error = "Неверный формат кода подразделения";
	}
	if ($text == '') {
		$error = "Введите код подразделения";
	}
	return $error;
}
//---------------------------------------------
function checkErrorPassportIssueDate($text) {
	$error = '';
	if (strlen($text) == 0) {
		$error = "Введите дату выдачи паспорта";
	}
	return $error;
}
//---------------------------------------------
function checkErrorInn($text) {
	$error = '';
	if (strlen($text) == 0) {
		$error = "Введите ИНН";
	}
	return $error;
}
//---------------------------------------------
function checkErrorSnils($text) {
	$error = '';
	$result = preg_match('/^[А-ЯЁа-яёa-zA-Z0-9]{3}[-][А-ЯЁа-яёa-zA-Z0-9]{3}[-][А-ЯЁа-яёa-zA-Z0-9]{3}[\s][А-ЯЁа-яёa-zA-Z0-9]{2}$/u', $text);
	if (!$result) {
		$error = "Неверный формат СНИЛС";
	}
	if ($text == '') {
		$error = "Введите СНИЛС";
	}
	return $error;
}
//---------------------------------------------
function createAgentXML($id_agent) {
	global $config_DomainEnd;
	$db_link = @connecDB();
	$magazine_name = "";
	
	$mysqli_result = mysqli_query($db_link, "SELECT * FROM agent WHERE id='$id_agent' AND deleted='0'");
	while ($tmp_row = mysqli_fetch_assoc($mysqli_result)) {
		$promocode = $tmp_row["promocode"];
		$promocode_name = $tmp_row["promocode_name"];
		$promocode_discount_percent = $tmp_row["promocode_discount_percent"];

		$name_first = $tmp_row["name_first"];
		$name_last = $tmp_row["name_last"];
		$name_middle = $tmp_row["name_middle"];
		$email = $tmp_row["email"];
		$tel = $tmp_row["tel"];

		$normalized_tel_phone = $tmp_row["normalized_tel_phone"];
		$normalized_tel_type = $tmp_row["normalized_tel_type"];
		$normalized_tel_country_code = $tmp_row["normalized_tel_country_code"];
		$normalized_tel_city_code = $tmp_row["normalized_tel_city_code"];
		$normalized_tel_number = $tmp_row["normalized_tel_number"];
		$normalized_tel_extension = $tmp_row["normalized_tel_extension"];
		$normalized_tel_provider = $tmp_row["normalized_tel_provider"];
		$normalized_tel_region = $tmp_row["normalized_tel_region"];
		$normalized_tel_timezone = $tmp_row["normalized_tel_timezone"];
		$normalized_tel_qc_conflict = $tmp_row["normalized_tel_qc_conflict"];
		$normalized_tel_qc = $tmp_row["normalized_tel_qc"];

		$account_num_rasschetni = $tmp_row["account_num_rasschetni"];
		$bank_name = $tmp_row["bank_name"];
		$account_num_correspondent = $tmp_row["account_num_correspondent"];
		$bik = $tmp_row["bik"];
		$credit_card_num = $tmp_row["credit_card_num"];
		$job = $tmp_row["job"];
		$job_position = $tmp_row["job_position"];
		$passport_series = $tmp_row["passport_series"];
		$passport_num = $tmp_row["passport_num"];
		$passport_issue_who = $tmp_row["passport_issue_who"];
		$passport_issue_code = $tmp_row["passport_issue_code"];
		$passport_issue_date = $tmp_row["passport_issue_date"];
		if (strpos($passport_issue_date, ' ')) {
			$passport_issue_date = substr($passport_issue_date, 0, strpos($passport_issue_date, ' '));
		}
		$passport_issue_date = str_replace("-", "", $passport_issue_date);
		$inn = $tmp_row["inn"];
		$snils = $tmp_row["snils"];
		$region = $tmp_row["region"];
		$region2 = $tmp_row["region2"];
		$district = $tmp_row["district"];
		$district2 = $tmp_row["district2"];
		$city_type = $tmp_row["city_type"];
		$city_type2 = $tmp_row["city_type2"];
		$city = $tmp_row["city"];
		$city2 = $tmp_row["city2"];
		$city_name = $tmp_row["city_name"];
		$city_name2 = $tmp_row["city_name2"];
		$street_type = $tmp_row["street_type"];
		$street_type2 = $tmp_row["street_type2"];
		$street = $tmp_row["street"];
		$street2 = $tmp_row["street2"];
		$street_name = $tmp_row["street_name"];
		$street_name2 = $tmp_row["street_name2"];
		$house = $tmp_row["house"];
		$house2 = $tmp_row["house2"];
		$korpus = $tmp_row["korpus"];
		$korpus2 = $tmp_row["korpus2"];
		$flat = $tmp_row["flat"];
		$flat2 = $tmp_row["flat2"];
		$zip = $tmp_row["zip"];
		$zip2 = $tmp_row["zip2"];
		$comment = $tmp_row["comment"];
		break;
	}

	if (@$mysqli_result) mysqli_free_result($mysqli_result);
	if (@$db_link) mysqli_close($db_link);

	if ($id_agent)	{
		$xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		$xml .= "<data>\n";
		$xml .= "<PROMOCODE>$promocode</PROMOCODE>\n";
		$xml .= "<PROMOCODE_NAME>$promocode_name</PROMOCODE_NAME>\n";
		$xml .= "<DISCOUNT>$promocode_discount_percent</DISCOUNT>\n";
		$xml .= "<NAME_FIRST>$name_first</NAME_FIRST>\n";
		$xml .= "<NAME_LAST>$name_last</NAME_LAST>\n";
		$xml .= "<NAME_MIDDLE>$name_middle</NAME_MIDDLE>\n";
		$xml .= "<EMAIL>$email</EMAIL>\n";
		$xml .= "<PHONE_FIZ>$tel</PHONE_FIZ>\n";
		$xml .= "<PHONE_NORMALIZED>$normalized_tel_phone</PHONE_NORMALIZED>\n";
		$xml .= "<PHONE_NORMALIZED_TYPE>$normalized_tel_type</PHONE_NORMALIZED_TYPE>\n";
		$xml .= "<PHONE_NORMALIZED_COUNTRY_CODE>$normalized_tel_country_code</PHONE_NORMALIZED_COUNTRY_CODE>\n";
		$xml .= "<PHONE_NORMALIZED_CITY_CODE>$normalized_tel_city_code</PHONE_NORMALIZED_CITY_CODE>\n";
		$xml .= "<PHONE_NORMALIZED_NUMBER>$normalized_tel_number</PHONE_NORMALIZED_NUMBER>\n";
		$xml .= "<PHONE_NORMALIZED_EXTENSION>$normalized_tel_extension</PHONE_NORMALIZED_EXTENSION>\n";
		$xml .= "<PHONE_NORMALIZED_PROVIDER>$normalized_tel_provider</PHONE_NORMALIZED_PROVIDER>\n";
		$xml .= "<PHONE_NORMALIZED_REGION>$normalized_tel_region</PHONE_NORMALIZED_REGION>\n";
		$xml .= "<PHONE_NORMALIZED_TIMEZONE>$normalized_tel_timezone</PHONE_NORMALIZED_TIMEZONE>\n";
		$xml .= "<PHONE_NORMALIZED_QC_CONFLICT>$normalized_tel_qc_conflict</PHONE_NORMALIZED_QC_CONFLICT>\n";
		$xml .= "<PHONE_NORMALIZED_QC>$normalized_tel_qc</PHONE_NORMALIZED_QC>\n";
		$xml .= "<ACCOUNT_NUM_RASSCHETNI>$account_num_rasschetni</ACCOUNT_NUM_RASSCHETNI>\n";
		$xml .= "<BANK_NAME>$bank_name</BANK_NAME>\n";
		$xml .= "<ACCOUNT_NUM_CORRESPONDENT>$account_num_correspondent</ACCOUNT_NUM_CORRESPONDENT>\n";
		$xml .= "<BIK>$bik</BIK>\n";
		$xml .= "<CREDIT_CARD_NUM>$credit_card_num</CREDIT_CARD_NUM>\n";
		$xml .= "<JOB>$job</JOB>\n";
		$xml .= "<JOB_POSITION>$job_position</JOB_POSITION>\n";
		$xml .= "<PASSPORT_SERIES>$passport_series</PASSPORT_SERIES>\n";
		$xml .= "<PASSPORT_NUM>$passport_num</PASSPORT_NUM>\n";
		$xml .= "<PASSPORT_ISSUE_WHO>$passport_issue_who</PASSPORT_ISSUE_WHO>\n";
		$xml .= "<PASSPORT_ISSUE_CODE>$passport_issue_code</PASSPORT_ISSUE_CODE>\n";
		$xml .= "<PASSPORT_ISSUE_DATE>$passport_issue_date</PASSPORT_ISSUE_DATE>\n";
		$xml .= "<INN>$inn</INN>\n";
		$xml .= "<SNILS>$snils</SNILS>\n";		
		$xml .= "<ADDRESS_REGION>$region</ADDRESS_REGION>\n";
		$xml .= "<ADDRESS_CITY>$city</ADDRESS_CITY>\n";
		$xml .= "<ADDRESS_STREET>$street</ADDRESS_STREET>\n";
		$xml .= "<ADDRESS_HOUSE>$house</ADDRESS_HOUSE>\n";
		$xml .= "<ADDRESS_FLAT>$flat</ADDRESS_FLAT>\n";
		$xml .= "<ADDRESS_ZIP>$zip</ADDRESS_ZIP>\n";		
		$xml .= "<ADDRESS_REGION2>$region2</ADDRESS_REGION2>\n";
		$xml .= "<ADDRESS_CITY2>$city2</ADDRESS_CITY2>\n";
		$xml .= "<ADDRESS_STREET2>$street2</ADDRESS_STREET2>\n";
		$xml .= "<ADDRESS_HOUSE2>$house2</ADDRESS_HOUSE2>\n";
		$xml .= "<ADDRESS_FLAT2>$flat2</ADDRESS_FLAT2>\n";
		$xml .= "<ADDRESS_ZIP2>$zip2</ADDRESS_ZIP2>\n";		
		$xml .= "<COMMENT>$comment</COMMENT>\n";
		$xml .= "</data>";
	
		$file_name = dirname(__FILE__) . '/agents_xml/' . $promocode . '.xml';
		$aaaa = file_put_contents($file_name, $xml);
		if(!$aaaa) {
			die("Error save XML");
		} else {
			return true;
		}
	}
}
//---------------------------------------------
function generateAgentPromocode($fio, $region_2_digits) {
	if ($region_2_digits =='') {
		$region_2_digits = '__';
	}
	$code_text = '';
	$discount_percent = "5";
	$code_name = "Промокод агента $fio";	
	$datetime_start = date('Y-m-d H:i:s');
	$datetime_end = '2100-01-01 00:00:00';
	$for_all_magazines = "1";
	
	$db_link = @connecDB();
	$tmp_query = "INSERT INTO promocode (code_text, discount_percent, code_name, datetime_start, datetime_end, for_all_magazines, datetime_created) VALUES('" . mysqli_real_escape_string($db_link, $code_text) . "', '" . mysqli_real_escape_string($db_link, $discount_percent) . "', '" . mysqli_real_escape_string($db_link, $code_name) . "', '" . mysqli_real_escape_string($db_link, $datetime_start) . "', '" . mysqli_real_escape_string($db_link, $datetime_end) . "', '" . mysqli_real_escape_string($db_link, $for_all_magazines) . "', '" . date('Y-m-d H:i:s') . "')";
	$mysqli_result = mysqli_query($db_link, $tmp_query);
	if (!$mysqli_result) {
		echo "DB error: " . mysqli_error($db_link);
	} else {
		$id_promocode = mysqli_insert_id($db_link);
		$code_text = $region_2_digits . str_pad($id_promocode, 3, '0', STR_PAD_LEFT);
		$mysqli_result = mysqli_query($db_link, "UPDATE promocode SET code_text='$code_text' WHERE id='" . mysqli_real_escape_string($db_link, $id_promocode) . "' AND deleted='0'");
	}
	if (@$db_link) mysqli_close($db_link);	
	return $code_text;
}
//---------------------------------------------
function email_agent_info($agent_id) {
	global $config_DomainEnd;
	global $config_email_copy_subscribe;
	if ($agent_id) {
		$arr_agent_info = get_agent_info($agent_id);
mb_language('ru');
mb_internal_encoding("UTF-8");
		if ($arr_agent_info) {
			$mailheaders = "Content-Type: text/html; charset=utf-8\r\n";
			$mailheaders .= "From: Толока <info@toloka24.$config_DomainEnd>\r\n";
			$mailheaders .= "Reply-To: info@toloka24.$config_DomainEnd\r\n";

			$email_content = '<div style="width:540px;font-family:Arial,sans-serif;font-size:14px;line-height:21px;padding:15px 30px;color:#333;margin:15px auto; border:1px solid #ddd;border-radius:10px;box-shadow:0px 0px 5px rgba(0,0,0,0.1);">' . "\n";
			$email_content .= '<h1 style="font-size:18px;line-height:23px;padding:0;margin:5px 0 10px 0;font-style:normal;font-weight:normal;font-family:Arial,sans-serif;">Здравствуйте, ' . $arr_agent_info['name_last'] . ' ' . $arr_agent_info['name_first'] . ' ' . $arr_agent_info['name_middle'] . '</h1>' . "\n";
			$email_content .= '<p>Благодарим Вас за регистрацию в качестве агента <b>«Толока»</b></p>' . "\n";
			$email_content .= '<table border="0" cellspacing="0" cellpadding="0" width="425">' . "\n";

			$email_content .= '<tr>' . "\n";
			$email_content .= '	<td nowrap valign="top" style="font-family:Arial,sans-serif;font-size:14px;line-height:19px;">Ваш промокод:</td>' . "\n";
			$email_content .= '	<td valign="top" align="right" style="font-family:Arial,sans-serif;font-size:14px;line-height:21px;"><b>' . $arr_agent_info['promocode'] . '</b></td>' . "\n";
			$email_content .= '</tr>' . "\n";

			$email_content .= '<tr>' . "\n";
			$email_content .= '	<td nowrap valign="top" style="font-family:Arial,sans-serif;font-size:14px;line-height:19px;">Ваш скидка:</td>' . "\n";
			$email_content .= '	<td valign="top" align="right" style="font-family:Arial,sans-serif;font-size:14px;line-height:21px;"><b>' . $arr_agent_info['promocode_discount_percent'] . '%</b></td>' . "\n";
			$email_content .= '</tr>' . "\n";

			$email_content .= '</table>' . "\n";
			$email_content .= '</div>' . "\n";
			if ( mail_PHPMailer($arr_agent_info['email'], "Уведомление о регистрации в качестве агента «Толока»", $email_content) ) {
				mail_PHPMailer($config_email_copy_subscribe, "Регистрация агента «Толока»: " . $arr_agent_info['name_last'] . ' ' . $arr_agent_info['name_first'] . ' ' . $arr_agent_info['name_middle'], $email_content);
				return true;
			} else {
				echo "Ошибка при отправке письма.";
			}
		}
	}
	return false;
}
//---------------------------------------------
function deleteAgentInfo($agent_id) {
	$tmp_query = "UPDATE agent SET		promocode_name='', 
										name_first='', 
										name_last='', 
										name_middle='', 
										email='', 
										tel='', 

										normalized_tel_phone='', 
										normalized_tel_type='', 
										normalized_tel_country_code='', 
										normalized_tel_city_code='', 
										normalized_tel_number='', 
										normalized_tel_extension='', 
										normalized_tel_provider='', 
										normalized_tel_region='', 
										normalized_tel_timezone='', 
										normalized_tel_qc_conflict='', 
										normalized_tel_qc='', 
										account_num_rasschetni='', 
										bank_name='', 
										account_num_correspondent='', 
										bik='', 
										credit_card_num='', 
										job='', 
										job_position='', 
										passport_series='', 
										passport_num='', 
										passport_issue_who='', 
										passport_issue_code='', 
										passport_issue_date='2000-01-01 00:00:00', 
										inn='', 
										snils='', 
										region='', 
										district='', 
										city_type='', 
										city='', 
										city_name='', 
										street_type='', 
										street='', 
										street_name='', 
										house='', 
										korpus='', 
										flat='', 
										zip='', 
										region2='', 
										district2='', 
										city_type2='', 
										city2='', 
										city_name2='', 
										street_type2='', 
										street2='', 
										street_name2='', 
										house2='', 
										korpus2='', 
										flat2='', 
										zip2='', 
										comment=''
										WHERE id='$agent_id' AND deleted='0'";
	if ($tmp_query != '') {
		$db_link = @connecDB();
		$mysqli_result = mysqli_query($db_link, $tmp_query);
		if (!$mysqli_result) {
			die("DB error: " . mysqli_error($db_link));
		}
	}
	if (@$db_link) mysqli_close($db_link);
}
//---------------------------------------------
function send_xml_request($type) {
	if ($type) {
		$result = 0;
		$promocode = filter_input(INPUT_POST, 'promocode');
		if ($promocode != '') {
			$db_link = @connecDB();
			$query = "SELECT * FROM promocode WHERE code_text='" . mysqli_real_escape_string($db_link, $promocode) ."' AND deleted='0' AND code_name LIKE 'Промокод агента %'";
			$mysqli_result = mysqli_query($db_link, $query);
			while ($tmp_row = mysqli_fetch_assoc($mysqli_result)) {
				$promocode = $tmp_row["code_text"];
				$promocode_name = $tmp_row["code_name"];
				$result = createAgentRequestXML($type, $promocode, $promocode_name);
				if ($result == 1) {
					email_agent_request_info($type, $promocode, $promocode_name);
				}
				break;
			}
			if (@$mysqli_result) mysqli_free_result($mysqli_result);
			if (@$db_link) mysqli_close($db_link);
			return $result;
		}
	}
	return false;
}
//---------------------------------------------
function createAgentRequestXML($type, $promocode, $promocode_name) {
	if ($type && $promocode && $promocode_name) {
		$xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		$xml .= "<data>\n";
		$xml .= "<PROMOCODE>$promocode</PROMOCODE>\n";
		$xml .= "<PROMOCODE_NAME>$promocode_name</PROMOCODE_NAME>\n";
		$xml .= "</data>";
	
		$file_name = dirname(__FILE__) . '/agents_xml/request_' . $type . '_' . $promocode . '.xml';
		if (file_exists($file_name)) {
			return -1;
		}
		$aaaa = file_put_contents($file_name, $xml);
		if(!$aaaa) {
			die("Error save XML");
		} else {
			return 1;
		}
	}
}
//---------------------------------------------
function email_agent_request_info($type, $promocode, $promocode_name) {
	global $config_DomainEnd;
	global $config_email_copy_subscribe;
	if (strpos($promocode_name, 'ромокод агента ')) {
		$agent_fio = str_replace("Промокод агента ", "", $promocode_name);
mb_language('ru');
mb_internal_encoding("UTF-8");
		if ($type == 'stats') {
			$type_ru = "отчетности";
		}
		if ($type == 'calculation') {
			$type_ru = "расчета";
		}
		$mailheaders = "Content-Type: text/html; charset=utf-8\r\n";
		$mailheaders .= "From: Толока <info@toloka24.$config_DomainEnd>\r\n";
		$mailheaders .= "Reply-To: info@toloka24.$config_DomainEnd\r\n";
		$email_content = '<div style="width:540px;font-family:Arial,sans-serif;font-size:14px;line-height:21px;padding:15px 30px;color:#333;margin:15px auto; border:1px solid #ddd;border-radius:10px;box-shadow:0px 0px 5px rgba(0,0,0,0.1);">' . "\n";
		$email_content .= "<p>Агент $agent_fio сделал запрос $type_ru</p>\n";
		$email_content .= '<table border="0" cellspacing="0" cellpadding="0" width="425">' . "\n";

		$email_content .= '<tr>' . "\n";
		$email_content .= '	<td nowrap valign="top" style="font-family:Arial,sans-serif;font-size:14px;line-height:19px;">Промокод агента:</td>' . "\n";
		$email_content .= '	<td valign="top" align="right" style="font-family:Arial,sans-serif;font-size:14px;line-height:21px;"><b>' . $promocode . '</b></td>' . "\n";
		$email_content .= '</tr>' . "\n";

		$email_content .= '</table>' . "\n";
		$email_content .= '</div>' . "\n";
		if ( mail_PHPMailer($config_email_copy_subscribe, "Запрос $type_ru агента «Толока»: $agent_fio", $email_content) ) {
//		if ( mail_PHPMailer('pixiesfanatic@yahoo.com', "Запрос $type_ru агента «Толока»: $agent_fio", $email_content) ) {
			return true;
		} else {
			echo "Ошибка при отправке письма.";
		}
	}
	return false;
}
//---------------------------------------------
function generate_possible_dates_of_subscribe_for_select() {
	global $config_subscribe_day_num_to_get_this_month;
	$months = array( 1 => 'Январь' , 'Февраль' , 'Март' , 'Апрель' , 'Май' , 'Июнь' , 'Июль' , 'Август' , 'Сентябрь' , 'Октябрь' , 'Ноябрь' , 'Декабрь' );

	$year_start = date('Y');

	$month_start = date('n');
	$month_start++;
	if ($month_start > 12) {
		$month_start = 1;
		$year_start++;
	}
	$day_now = date('j');
	if ($day_now > $config_subscribe_day_num_to_get_this_month) {
		$month_start++;
	}
	if ($month_start > 12) {
		$month_start = 1;
		$year_start++;
	}

	$result = '';
	for ($i = 0; $i < 6; $i++) {
		if ($month_start > 12) {
			$month_start = 1;
			$year_start++;
		}
		$result .= '<option value="' . $month_start . '">' . $months[$month_start] . ' ' . $year_start . '</option>';
		$month_start++;
	}
	
	return $result;
}
//---------------------------------------------
function save_post_values_in_cookies($arr) {
	foreach ($arr as $val) {
		if (filter_input(INPUT_POST, $val) != '') {
			set_cookie_value($val, filter_input(INPUT_POST, $val));
		}
	}
}
//---------------------------------------------
function get_cookie_value($cookie_name) {
	$cookie_value = "";
	if (isset($_COOKIE["subscriber_" . $cookie_name])) {
		$cookie_value = $_COOKIE["subscriber_" . $cookie_name];
	}
	return $cookie_value;
}
//---------------------------------------------
function set_cookie_value($cookie_name, $cookie_value) {
	if ($cookie_name != '') {
		$int = 60*60*24*30;
		setcookie("subscriber_" . $cookie_name, $cookie_value, time() + $int, '/');
	}
}
//---------------------------------------------
?>