<?php
	require_once('../__functions.php');
	$arr_acts = array(
		"login",
		"logout",

		"edit_agent",
		"new_agent",
		"delete_agent",

		"request_stats",
		"request_calculation"
	);
	$breadcrumb = "";
	$api_action = filter_input(INPUT_POST, 'act');
	if (!$api_action) $api_action = filter_input(INPUT_GET, 'act');
	if ($api_action != "login" && $api_action != "logout" && $api_action != "delete_agent") {
		require "../_tpl/_header.tpl";
	}
	
	if ($flagAgentLoggedIn || $api_action == "login") {
		if ($api_action && in_array($api_action, $arr_acts)) {
			if ($_SERVER['REQUEST_METHOD'] == "POST") {
				if ($api_action == "edit_agent") {
					$breadcrumb = "Редактирование агента";
					$agent_id = set_agent_info();
					if ($agent_id) {
						$post_success_info = "Изменения сохранены успешно.";
						$arr_agent_info = get_agent_info($agent_id);
						require "../_tpl/agent/agent_02_agent_info.tpl";
					}
				}
				if ($api_action == "delete_agent") {
					echo delete_agent();
				}
				if ($api_action == "login") {
					echo api_agent_login();
					exit;
				}
			} else {
				if ($api_action == "logout") {
					echo api_agent_logout($_SERVER['HTTP_REFERER']);
					exit;
				}
			}			
		} else {
			if ($flagAgentLoggedIn) {
				require "../_tpl/agent/agent_01_start.tpl";
			}
		}
	} else {
		if ($_SERVER['REQUEST_METHOD'] == "POST") {
			if ($api_action == "new_agent") {
				$breadcrumb = "Добавление нового агента";
				$agent_id = set_agent_info();
				if ($agent_id) {
					$post_success_info = "Новый агент успешно добавлен.";
					$arr_agent_info = get_agent_info($agent_id);
					require "../_tpl/agent/agent_02_registration_success.tpl";
				} else {
					require "../_tpl/agent/agent_00_register.tpl";
				}
			} else if ($api_action == "request_stats") {
				$agent_id = send_xml_request('stats');
				if ($agent_id > 0) {
					$post_request_stats_success_info = "Ваш запрос отчетности успешно отправлен. Ожидайте в скором времени ответ на e-mail.";
				} else {
					if ($agent_id == -1) {
						$post_request_stats_error_info = "Вы уже отправили запрос отчетности! Ожидайте ответ на e-mail!";
					} else {
						$post_request_stats_error_info = "Такого кода не существует!";
					}
				}
				require "../_tpl/agent/agent_00_auth.tpl";
			} else if ($api_action == "request_calculation") {
				$agent_id = send_xml_request('calculation');
				if ($agent_id > 0) {
					$post_request_calculation_success_info = "Ваш запрос расчета успешно отправлен. Ожидайте в скором времени ответ на e-mail.";
				} else {
					if ($agent_id == -1) {
						$post_request_calculation_error_info = "Вы уже отправили запрос расчета! Ожидайте ответ на e-mail!";
					} else {
						$post_request_calculation_error_info = "Такого кода не существует!";
					}
				}
				require "../_tpl/agent/agent_00_auth.tpl";
			} else {
				require "../_tpl/agent/agent_00_auth.tpl";
			}
		} else {
			if ($api_action == "new_agent") {
				require "../_tpl/agent/agent_00_register.tpl";
			} else {
				require "../_tpl/agent/agent_00_auth.tpl";
			}
		}
	}

	if ($api_action != "login" && $api_action != "logout" && $api_action != "delete_agent") {
		require "../_tpl/_footer.tpl";
	}

	if (@$mysql_result) mysql_free_result($mysql_result);
	if (@$db_link) mysql_close($db_link);
?>