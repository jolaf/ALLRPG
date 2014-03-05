<?php
$globaltimer=microtime(true);
require_once("../db.inc");

start_mysql();
# Установление соединения с MySQL-сервером

session_start();

auth($prefix."users");
# Авторизация

require_once("kind_list.php");

// Подключение библиотеки объектов, полей (hidden, timestamp, h1) и прав
require_once($server_inner_path.$direct."/classes/classes_objects.php");
require_once($server_inner_path.$direct."/classes/classes_rights.php");
require_once($server_inner_path."classes_objects_allrpg.php");

require_once("kind_".$kind.".php");
if($content2=='')
{
	echo("<html><body><script>document.location='".$server_absolute_path."error404.php';</script></body></html>");
	exit;
}

$result=mysql_query("SELECT * FROM ".$prefix."dif where name='Title'");
$a=mysql_fetch_array($result);

$content='<!doctype html public \'-//w3c//dtd html 4.01//en\' \'http://www.w3.org/tr/html4/strict.dtd\'>
<html>
<head>
<title>Управление сайтом: '.decode2($a["content"]).'</title>
<meta content="text/html; charset=UTF-8" http-equiv=Content-Type>
<link rel="stylesheet" type="text/css" href="'.$server_absolute_path.$direct.'/jquery/plugins/jquery-ui-1.10.0.custom/css/smoothness/jquery-ui-1.10.0.custom.min.css">
<script type="text/javascript" src="'.$server_absolute_path.$direct.'/jquery/jquery-1.9.0.min.js"></script>
<script type="text/javascript" src="'.$server_absolute_path.$direct.'/jquery/plugins/jquery-ui-1.10.0.custom/js/jquery-ui-1.10.0.custom.min.js"></script>
<script type="text/javascript" src="'.$server_absolute_path.$direct.'/jquery/plugins/jquery-ui-1.10.0.custom/js/jquery-ui-i18n.js"></script>
<script type="text/javascript" src="'.$server_absolute_path.$direct.'/jquery/plugins/jquery-ui-1.10.0.custom/js/jquery.ui.datepicker-ru.js"></script>
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
<link rel="stylesheet" type="text/css" href="'.$server_absolute_path.$admin.'/main.css">
<link rel="stylesheet" media="screen" type="text/css" href="'.$server_absolute_path.$direct.'/jquery/plugins/colorpicker/jquery.colorpicker.css" />
</head>

<body>
<a name="top"></a>
<div class="topmenu">
	<div>
	<!--menu-->
	</div>
</div>
<br>
<div class="maincontent">
<!--maincontent-->
</div>';

for($i=0;$i<count($kinds);$i++)
{
	if($kinds[$i][0]=='hr')
	{
		if($theright[$kinds[$i][0]])
		{
			$menu.='
</div>
<hr>
<div>
';
			$seen=false;
		}
	}
	elseif($theright[$kinds[$i][0]])
	{
		if($seen)
		{
			$menu.=' | ';
		}
		if($kinds[$i][0]!=$kind)
		{
			$menu.='<a href="'.$server_absolute_path.$admin.'/'.$kinds[$i][0].'/">'.$kinds[$i][1].'</a>';
		}
		else
		{
			$menu.='<a href="'.$server_absolute_path.$admin.'/'.$kinds[$i][0].'/" class="selected">'.$kinds[$i][1].'</a>';
		}
		$seen=true;
	}
}

$content.='
<!--error-->
</body>
</html>';

$content=preg_replace('#<!--maincontent-->#',$content2,$content);
$content=preg_replace('#<!--menu-->#',$menu,$content);
$error_array='<script>
errors=[];';
foreach($_SESSION['errors'] as $error) {
	$error_array.='errors.push(Array("'.$error[0].'","'.str_replace('"','\"',$error[1]).'"));';
}
unset($_SESSION['errors']);
$error_array.='</script>';
$content=preg_replace('#<!--error-->#',$error_array,$content);

print($content);
# Вывод основного содержания страницы

stop_mysql();
# Разрыв соединения с MySQL-сервером

$globaltimer=microtime(true)-$globaltimer;
echo('
<!-- execution time: '.$globaltimer.'s-->');
?>