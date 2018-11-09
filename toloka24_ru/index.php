<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require_once 'config_mapper.php';
require_once 'config.php';
require_once('__functions.php');
ob_start(); // Initiate the output buffer

$protocol = empty($_SERVER['HTTPS'])?'http://':'https://';
$current_url = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

$article="";
$page = $_SERVER['REQUEST_URI'];
$page = substr($page, strpos($page, '/') + 1);
if ($page == '' || strtolower($page) == 'index.php' || strtolower($page) == '_index.php' || strtolower($page) == 'index.html' || strtolower($page) == 'index.htm') {
	$menu_selected = "0";
	header($_SERVER["SERVER_PROTOCOL"] . " 200 OK\n");
	$page = "_front.php";
} elseif (strpos($page, '/')) {
	if (substr_count($page, '/') == 1) {
		$page = substr($page, 0, strpos($page, '/'));
	} else {
		$page = substr($page, 0, strrpos($page, '/'));
	}
	$article = '';
	if (array_key_exists($page, $arr_articles)) {
		if ($arr_articles[$page]["title"] != "") {
			$page_title = $arr_articles[$page]["title"];
		}
		if ($arr_articles[$page]["og_title"] != "") {
			$og_page_title = $arr_articles[$page]["og_title"];
		} else {
			$og_page_title = $page_title;
		}
		if ($arr_articles[$page]["description"] != "") {
			$page_description = $arr_articles[$page]["description"];
		}
		if ($arr_articles[$page]["og_description"] != "") {
			$og_page_description = $arr_articles[$page]["og_description"];
		} else {
			$og_page_description = $page_description;
		}
		if ($arr_articles[$page]["og_picture"] != "") {
			$og_page_picture = $arr_articles[$page]["og_picture"];
		}
		if ($arr_articles[$page]["keywords"] != "") {
			$page_keywords = $arr_articles[$page]["keywords"] . "," . $page_keywords;
		}
		if ($arr_articles[$page]["menu_selected"] != "") {
			$menu_selected = $arr_articles[$page]["menu_selected"];
		}
		$article = $page;
		header($_SERVER["SERVER_PROTOCOL"] . " 200 OK\n");
		$page = "_article.php";
	} else {
		if (strpos($page, 'agazine/') > 0) {
			if ($arr_magazine_info = check_magazine_url_exist($page)) {
				if ($arr_magazine_info["html_title"] != "") {
					$page_title = $arr_magazine_info["html_title"];
				} else {
					$page_title = $arr_magazine_info["name"];
				}
				$page_description = $arr_magazine_info["meta_description"];
				$page_keywords = $arr_magazine_info["meta_keywords"] . "," . $page_keywords;
				$og_page_title = 'Подписка на журнал ' . $arr_magazine_info["name"];
				$article = 'magazine/template';
				$page = "_article.php";
			}
		} else if (strpos($page, 'roup/') > 0) {
			if ($arr_group_info = check_group_url_exist($page)) {
				if ($arr_group_info["html_title"] != "") {
					$page_title = $arr_group_info["html_title"];
				} else {
					$page_title = $arr_group_info["name"];
				}
				$page_description = $arr_group_info["meta_description"];
				$page_keywords = $arr_group_info["meta_keywords"] . "," . $page_keywords;
				$og_page_title = 'Подписка на комплект ' . $arr_group_info["name"];
				$article = 'group/template';
				$page = "_article.php";
			}
		} else {
			$page = $page . '.php';
			if (!file_exists($page)) {
				header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
				$page = "_404.php";
			}
		}
	}
} else {
	header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
	$page = "_404.php";
}
require $page;
ob_end_flush(); // Flush the output from the buffer
?>