<?php
	require_once('../__functions.php');
	$arr_acts = array(
		"login",
		"logout",
		"reg",

		"choose_magazine",
		"edit_magazine",
		"new_magazine",
		"delete_magazine",

		"choose_group",
		"edit_group",
		"new_group",
		"delete_group",

		"edit_promocode",
		"new_promocode",
		"delete_promocode",
		"all_promocodes"
	);
	$breadcrumb = "";
	$api_action = filter_input(INPUT_POST, 'act');
	if (!$api_action) $api_action = filter_input(INPUT_GET, 'act');
//$flagUserLoggedIn = true; //!!!!!
//echo "api_action=$api_action";exit;
	if ($api_action != "login" && $api_action != "logout" && $api_action != "delete_magazine" && $api_action != "delete_group" && $api_action != "delete_promocode") {
		require "../_tpl/_header.tpl";
	}

	if ($flagUserLoggedIn || $api_action == "login") {
		if ($api_action && in_array($api_action, $arr_acts)) {
			if ($_SERVER['REQUEST_METHOD'] == "POST") {
				if ($api_action == "choose_magazine") {
					$breadcrumb = "Редактирование журнала";
					$arr_magazine_info = get_magazine_info();
					require "../_tpl/admin/admin_02_magazine_info.tpl";
				}
				if ($api_action == "edit_magazine") {
					$breadcrumb = "Редактирование журнала";
					$magazine_id = set_magazine_info();
					if ($magazine_id) {
						$post_success_info = "Изменения сохранены успешно.";
						$arr_magazine_info = get_magazine_info($magazine_id);
						require "../_tpl/admin/admin_02_magazine_info.tpl";
					}
				}
				if ($api_action == "new_magazine") {
					$breadcrumb = "Добавление нового журнала";
					$magazine_id = set_magazine_info();
					if ($magazine_id) {
						$post_success_info = "Новый журнал успешно добавлен.";
						$arr_magazine_info = get_magazine_info($magazine_id);
						require "../_tpl/admin/admin_02_magazine_info.tpl";
					}
				}
				if ($api_action == "choose_group") {
					$breadcrumb = "Редактирование комплекта";
					$arr_group_info = get_group_info();
					require "../_tpl/admin/admin_05_group_info.tpl";
				}
				if ($api_action == "edit_group") {
					$breadcrumb = "Редактирование комплекта";
					$group_id = set_group_info();
					if ($group_id) {
						$post_success_info = "Изменения сохранены успешно.";
						$arr_group_info = get_group_info($group_id);
						require "../_tpl/admin/admin_05_group_info.tpl";
					}
				}
				if ($api_action == "new_group") {
					$breadcrumb = "Добавление нового комплекта";
					$group_id = set_group_info();
					if ($group_id) {
						$post_success_info = "Новый комплект успешно добавлен.";
						$arr_group_info = get_group_info($group_id);
						require "../_tpl/admin/admin_05_group_info.tpl";
					}
				}
				if ($api_action == "edit_promocode") {
					$breadcrumb = "Редактирование промокода";
					$promocode_id = set_promocode_info();
					if ($promocode_id) {
						$post_success_info = "Изменения сохранены успешно.";
						$arr_promocode_info = get_promocode_info($promocode_id);
						require "../_tpl/admin/admin_03_promocode_info.tpl";
					}
				}
				if ($api_action == "new_promocode") {
					$breadcrumb = "Добавление нового промокода";
					$promocode_id = set_promocode_info();
					if ($promocode_id) {
						$post_success_info = "Изменения сохранены успешно.";
						$arr_promocode_info = get_promocode_info($promocode_id);
						require "../_tpl/admin/admin_03_promocode_info.tpl";
					}
				}
				if ($api_action == "delete_magazine") {
					echo delete_magazine();
				}
				if ($api_action == "delete_group") {
					echo delete_group();
				}
				if ($api_action == "delete_promocode") {
					echo delete_promocode();
				}
				if ($api_action == "login") {
					echo api_login();
					exit;
				}
			} else {
				if ($api_action == "logout") {
					echo api_logout($_SERVER['HTTP_REFERER']);
					exit;
				}
				if ($api_action == "new_magazine") {
					$breadcrumb = "Добавление нового журнала";
					$flagNewMagazine = 1;
					$arr_magazine_info = get_magazine_categories();
					require "../_tpl/admin/admin_02_magazine_info.tpl";
				}
				if ($api_action == "new_group") {
					$breadcrumb = "Добавление нового комплекта";
					$flagNewGroup = 1;
					$arr_group_info = get_group_info('init');
					require "../_tpl/admin/admin_05_group_info.tpl";
				}
				if ($api_action == "new_promocode") {
					$breadcrumb = "Добавление нового промокода";
					$flagNewPromocode = 1;
					$arr_promocode_info = get_promocode_info();
					require "../_tpl/admin/admin_03_promocode_info.tpl";
				}
				if ($api_action == "edit_promocode") {
					$breadcrumb = "Редактирование промокода";
					$arr_promocode_info = get_promocode_info();
					require "../_tpl/admin/admin_03_promocode_info.tpl";
				}
				if ($api_action == "all_promocodes") {
					$breadcrumb = "Все промокоды";
					$flagAllPromocodes = 1;
					$arr_promocodes = get_all_promocodes_info();
					require "../_tpl/admin/admin_04_all_promocodes_info.tpl";
				}
			}			
		} else {
			if ($flagUserLoggedIn) {
				$arr_magazines = get_all_magazine_info();
				$arr_promocodes = get_all_actual_promocodes_info();
				$arr_groups = get_all_group_info();
				require "../_tpl/admin/admin_01_start.tpl";
			}
		}
	}

	if (!$flagUserLoggedIn) {
		require "../_tpl/admin/admin_00_auth.tpl";
	}

	if ($api_action != "login" && $api_action != "logout" && $api_action != "delete_magazine" && $api_action != "delete_group" && $api_action != "delete_promocode") {
		require "../_tpl/_footer.tpl";
	}

	if (@$mysql_result) mysql_free_result($mysql_result);
	if (@$db_link) mysql_close($db_link);
?>