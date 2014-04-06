<?php
$content='<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<meta name="Description" Content="Ролевые игры, хостинг, система заявок, статьи">
<meta name="Keywords" Content="ролевые игры, хостинг, система заявок, статьи, allrpg, allrpg.info, allrpg.ru, ролевая игра, ролевики, РИ, lrpg, rpg">
<meta name="author" content="©еть">
<title>Ролевые игры, хостинг и система заявок</title>
<link rel="stylesheet" type="text/css" href="'.$server_absolute_path.$direct.'/jquery/plugins/jquery-ui-1.10.0.custom/css/smoothness/jquery-ui-1.10.0.custom.min.css">
<script type="text/javascript" src="http://yandex.st/jquery/1.9.0/jquery.min.js"></script>
<script type="text/javascript" src="http://yandex.st/jquery-ui/1.10.0/jquery-ui.min.js"></script>
<script type="text/javascript" src="http://yandex.st/jquery-ui/1.10.0/i18n/jquery.ui.datepicker-ru.min.js"></script>
<script type="text/javascript" src="'.$server_absolute_path.$direct.'/jquery/plugins/noty/jquery.noty.js"></script>
<script type="text/javascript" src="'.$server_absolute_path.$direct.'/jquery/plugins/noty/layouts/center.js"></script>
<script type="text/javascript" src="'.$server_absolute_path.$direct.'/jquery/plugins/noty/layouts/bottomRight.js"></script>
<script type="text/javascript" src="'.$server_absolute_path.$direct.'/jquery/plugins/noty/themes/default.js"></script>
<script type="text/javascript" src="'.$server_absolute_path.$direct.'/jquery/plugins/simplemodal/jquery.simplemodal.1.4.4.min.js"></script>
<link rel="stylesheet" href="'.$server_absolute_path.$direct.'/jquery/plugins/wysihtml5/stylesheet.css">
<script src="'.$server_absolute_path.$direct.'/jquery/plugins/wysihtml5/advanced.js"></script>
<script src="'.$server_absolute_path.$direct.'/jquery/plugins/wysihtml5/wysihtml5-0.3.0.min.js"></script>
<script src="'.$server_absolute_path.$direct.'/jquery/plugins/formstyler/jquery.formstyler.min.js"></script>
<script type="text/javascript" src="'.$server_absolute_path.$direct.'/jquery/plugins/colorpicker/jquery.colorpicker.js"></script>
<script type="text/javascript" src="'.$server_absolute_path.$direct.'/main.js"></script>
<link rel="stylesheet" type="text/css" href="'.$server_absolute_path.$direct.'/jquery/plugins/formstyler/jquery.formstyler.css">
<link rel="stylesheet" type="text/css" href="'.$server_absolute_path.'main_new.css">
<link rel="stylesheet" type="text/css" href="'.$server_absolute_path.'tiles.css">
<link rel="stylesheet" media="screen" type="text/css" href="'.$server_absolute_path.$direct.'/jquery/plugins/colorpicker/jquery.colorpicker.css" />
';
if($itsthemainpage) {
	$content.='<link rel="stylesheet" type="text/css" href="'.$server_absolute_path.'mainpage_fix.css">';
}
$content.='
</head>

<body>
<div class="fullpage">
<div class="header">

<div class="header_left">
	<div class="logo"><a href="'.$server_absolute_path.'"><img src="'.$server_absolute_path.'images/design/logo.png"></a></div>
	СИСТЕМА ЗАЯВОК И ХОСТИНГ<br>
	<div class="pagetitle_wrap">
		<div class="pagetitle"><!--pagetitle--></div>
	</div>
</div>

<div class="header_right">
	<!--login-->
	<div class="clear"></div>
	<div class="qwerty_space">
		<form action="'.$server_absolute_path_info.'#mainsearchlink" method="post" enctype="multipart/form-data" autocomplete="off" id="qwerty_form">
			<input type="hidden" name="action" value="mainsearch" />
			<input type="text" name="qwerty" id="qwerty" class="qwerty" placehold="Поиск" />
			<div id="qwerty-helper">введите 3+ символа</div>
			<div id="qwerty-empty-message">ничего не найдено</div>
		</form>
		<div id="qwerty-container"></div>
	</div>
	<div class="menu_wrapper"><!--additional_commands--></div>
</div>

</div>
<div class="clear"></div>
<div class="content">
<div class="maincontent">
<div class="maincontent_wrapper">
<!--maincontent-->
</div>
</div>
</div>

<div class="footer"><div id="cetb_logo"><a href="http://www.cetb.ru"><img src="'.$server_absolute_path.'images/design/cetb.png"/></a></div><div id="be_logo"><a href="http://www.be.net/aleser"><img src="'.$server_absolute_path.'images/design/be.png"/></a></div>
</div>

<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src=\'" + gaJsHost + "google-analytics.com/ga.js\' type=\'text/javascript\'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("UA-2587146-1");
pageTracker._trackPageview();
} catch(err) {}
</script>
<!--error-->
</div>
</body>
</html>';
?>