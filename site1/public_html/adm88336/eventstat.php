<?php
include_once("../db.inc");
//error_reporting(E_ALL);

start_mysql();
# Установление соединения с MySQL-сервером

$content='';

$content.='<html>
<head>
<style>
td {padding: 3px;}
</style>
</head>
<body>
<table border=1 style="border: 1px solid black; border-collapse: collapse;"><tr style="font-weight: bold;"><td>год</td><td>игр</td><td>прочего</td><td>итого</td></tr>';

$result=mysql_query("SELECT * FROM ".$prefix."allgames WHERE parent=0 order by datestart asc limit 0,1");
$a = mysql_fetch_array($result);
$start=date("Y",strtotime($a["datestart"]));

$result=mysql_query("SELECT * FROM ".$prefix."allgames WHERE parent=0 order by datestart desc limit 0,1");
$a = mysql_fetch_array($result);
$finish=date("Y",strtotime($a["datestart"]));

$total=0;
$totalgames=0;
$totalothers=0;
for($j=$start;$j<=$finish;$j++) {
	$content.='<tr><td>'.$j.'</td><td>';

    $result=mysql_query("SELECT COUNT(id) FROM ".$prefix."allgames WHERE parent=0 and datestart>='".$j."-01-01' and datestart<='".$j."-12-31' and (gametype2='-2-' OR gametype2='-13-' OR gametype2='-14-' OR gametype2='-15-')");
	$a = mysql_fetch_array($result);

	$content.=$a[0].'</td><td>';
	$totalgames+=$a[0];

	$result2=mysql_query("SELECT COUNT(id) FROM ".$prefix."allgames WHERE parent=0 and datestart>='".$j."-01-01' and datestart<='".$j."-12-31' and (gametype2='-33-' OR gametype2='-37-' OR gametype2='-4-' OR gametype2='-32-' OR gametype2='-34-' OR gametype2='-16-')");
	$b = mysql_fetch_array($result2);

	$content.=$b[0].'</td><td>'.($a[0]+$b[0]).'</td></tr>';

	$totalothers+=$b[0];
	$total=$total+$a[0]+$b[0];
}

$content.='<tr><td></td><td><b>'.$totalgames.'</b></td><td><b>'.$totalothers.'</b></td><td><b>'.$total.'</b></td></tr></table>
</body>
</html>';

print($content);
# Вывод основного содержания страницы

stop_mysql();
# Разрыв соединения с MySQL-сервером

?>