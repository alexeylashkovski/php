<?php
$tpl_article = "_tpl/articles/$article.tpl";
if (file_exists($tpl_article)) {
	include "_tpl/_header.tpl";
	include "$tpl_article";
	include "_tpl/_footer.tpl";
} else {
	include "_404.php";
}
?>