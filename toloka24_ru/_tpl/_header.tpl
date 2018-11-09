<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title><?php echo remove_br($page_title); ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1">    	
	<meta name="description" content="<?php echo htmlspecialchars($page_description); ?>" />
	<meta name="keywords" content="<?php echo $page_keywords; ?>" />
	<link rel="shortcut icon" type="image/vnd.microsoft.icon" href="/_img/favicons/favicon.ico" />
	<link rel="apple-touch-icon" href="/_img/favicons/touch-icon-iphone.png">
	<link rel="apple-touch-icon" sizes="76x76" href="/_img/favicons/touch-icon-ipad.png">
	<link rel="apple-touch-icon" sizes="120x120" href="/_img/favicons/touch-icon-iphone-retina.png">
	<link rel="apple-touch-icon" sizes="152x152" href="/_img/favicons/touch-icon-ipad-retina.png">
	<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700|Roboto:300,400,500,700|Open+Sans:400,600,700&subset=latin,cyrillic" rel="stylesheet" type="text/css" />
	<link href="https://fonts.googleapis.com/css?family=Ubuntu:300,300i,400,400i,500,500i,700,700i&amp;subset=cyrillic-ext" rel="stylesheet">
	<link href="/_css/bootstrap.css" rel="stylesheet">
	<link href="/_css/hamburgers.css" rel="stylesheet">
	<link href="/_css/animate.css" rel="stylesheet">

	<script src="/_js/jquery-3.1.1.min.js" type="text/javascript"></script>	

    <link href="/_js/alertify/css/alertify.core.css" rel="stylesheet">
    <link href="/_js/alertify/css/alertify.default.css" rel="stylesheet">
	<script src="/_js/alertify/alertify.min.js" type='text/javascript'></script>

	<link href="/_js/Swiper-4.2.2/dist/css/swiper.min.css" rel="stylesheet">
	<script src="/_js/Swiper-4.2.2/dist/js/swiper.min.js"></script>

	<link href="https://cdn.jsdelivr.net/npm/suggestions-jquery@17.12.0/dist/css/suggestions.min.css" type="text/css" rel="stylesheet" />
	<!--[if lt IE 10]>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-ajaxtransport-xdomainrequest/1.0.1/jquery.xdomainrequest.min.js"></script>
	<![endif]-->
	<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/suggestions-jquery@17.12.0/dist/js/jquery.suggestions.min.js"></script>

	<link href="/_css/style.css?ver=2018_10_11_03_50" rel="stylesheet">
	<link href="/_css/_<?= $config_DomainEnd ?>.css?ver=2018_10_11_03_50" rel="stylesheet">
	<link href="/_css/responsive.css?ver=2018_10_11_03_50" rel="stylesheet">	
	<link href="/_css/_<?= $config_DomainEnd ?>_responsive.css?ver=2018_10_11_03_50" rel="stylesheet">
	<script src="/_js/js.js?ver=2018_10_11_03_50"></script>
	<script src="/_js/jsSuggestions_<?= $config_DomainEnd ?>.js?ver=2018_10_11_03_50"></script>
	
	<link rel="canonical" href="<?php echo $current_url; ?>" />
	<meta property="og:url" content="<?php echo $current_url; ?>" />
	<meta property="og:type" content="website" />
	<meta property="og:site_name" content="toloka24.<?= $config_DomainEnd ?>" />
	<meta property="og:title" content="<?php echo remove_br($og_page_title); ?>" />
	<meta property="og:description" content="<?php echo htmlspecialchars($og_page_description); ?>" />
	<meta property="og:image" content="<?php echo $og_page_picture; ?>" />

	<?= $config_google_site_verification ?>

</head>
<body class="<?= $config_DomainEnd ?>">
<div class='preloader'>
	<div class="loader"><!--span>Загружаем...</span--></div>
</div>
<div class="wrapper">
	<header>
		<div class="container-fluid">
			<div class="row">	
				<div class="col-md-12">
					<button class="mob_burger hamburger hamburger--spin" type="button">
						<span class="hamburger-box">
							<span class="hamburger-inner"></span>
						</span>									
					</button>
					<div class="hamburger-menu">
						<div class="close_icon"></div>
						<a href="/" class="hamburger_logo_wrapper"><img class="hamburger_logo" src="/_img/logo.png" /></a>
						<ul>
							<?php if ($config_DomainEnd == "by") { ?>
							<li><a href="/subscribe-info/">Оформление подписки</a></li>
							<li><a href="/payment-delivery/">Оплата и доставка</a></li>
							<?php } ?>
							<li><a href="/all-magazines/">Все журналы</a></li>
							<li><a href="/contacts/">Контакты</a></li>
						</ul>
					</div>
					<a href="/" class="logo_wrapper"><img class="logo" src="/_img/logo.png" /></a>
					<?php if (($flagUserLoggedIn && !$flagAgentLoggedIn) || ($flagUserLoggedIn && $flagAgentLoggedIn && strpos($_SERVER["REQUEST_URI"], 'admin/'))) { ?> 
						<div class="logged_user_header_info">
							Здравствуйте, <b><?= $nameUserLoggedIn ?></b>
							&nbsp;&nbsp;&nbsp;&nbsp;
							<a href="/admin/">Админка</a>
							&nbsp;&nbsp;&nbsp;&nbsp;
							<a href="/admin/?act=logout">Выйти</a>
						</div>
					<?php } else if ($flagAgentLoggedIn) { ?> 
						<div class="logged_user_header_info">
							Здравствуйте, <b><?= $nameAgentLoggedIn ?></b>
							&nbsp;&nbsp;&nbsp;&nbsp;
							<a href="/agent/">Панель агента</a>
							&nbsp;&nbsp;&nbsp;&nbsp;
							<a href="/agent/?act=logout">Выйти</a>
						</div>
					<?php } ?> 
					<ul class="header_menu">
						<li><a href="/all-magazines/">Все журналы</a></li>
						<li><a href="/contacts/">Контакты</a></li>
					</ul>
				</div>
			</div>
		</div>
	</header>
	