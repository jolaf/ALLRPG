<?php
include_once("db.inc");
include_once("classes_objects_allrpg.php");
//error_reporting(E_ALL);

start_mysql();
# Установление соединения с MySQL-сервером

$content='';

$from=encode($_GET["from"]);
if($from=='' || !is_Numeric($from)) {
	$from=0;
}
$to=encode($_GET["to"]);
if($to=='' || !is_Numeric($to)) {
	$to=2000;
}

$content.='<table border=1 style="border: 1px solid black; border-collapse: collapse;"><tr style="font-weight: bold;"><td>№</td><td>регион</td><td>полигон</td><td>тип</td></tr>';
$i=1;
$newpolygons=Array();
for($id=$from;$id<=$to;$id++) {
	$stres=implode(file("http://kogda-igra.ru/api/game/".$id));
	unset($info);
	$info=json_decode($stres,true);

	$region=$info["sub_region_name"];
	$gametype=$info["game_type_name"];
	$polygon=$info["polygon_name"];

//	$region=utf8_to_cp1251($info["sub_region_name"]);
//	$gametype=utf8_to_cp1251($info["game_type_name"]);
//	$polygon=utf8_to_cp1251($info["polygon_name"]);

	$polygon_id=$info["polygon"];
	unset($allrpg_id);
	$allrpg_id=$info["allrpg_info_id"];
	if(($name!='' || $allrpg_id>0) && $info["deleted_flag"]!='1' && $polygon_id>0 && $polygon_id!=29) {
		$newpolygon='';
		$content.='<tr><td>'.$i.'</td><td>';
		$result=mysql_query("SELECT * FROM ".$prefix."geography WHERE name LIKE '%".$region."%'");
		$a = mysql_fetch_array($result);
		if($a["id"]!='') {
			$content.=$region;
			$polygon_region=$a["id"];
		}
		elseif($region=="Пермский край") {
			$content.=$region; //761
			$polygon_region=761;
		}
		else {
			$polygon_region=0;
		}
		$content.='</td><td>';
		$result=mysql_query("SELECT * FROM ".$prefix."areas WHERE name LIKE '%".$polygon."%' OR kogdaigra_id=".$polygon_id);
		$a = mysql_fetch_array($result);
		if($a["id"]!='' || strpos($polygon,'г.')!==false) {
			$content.=$polygon;
		}
		else {
			$content.='<font color="red">'.$polygon.'</font>';
			$newpolygon=$polygon;
		}
		$content.='</td><td>';
		if($info["type"]==3 || $info["type"]==1) {
			$content.='лесной';
			$polygon_type=2;
		}
		else {
			$content.='городской';
			$polygon_type=1;
		}
		$content.='</td></tr>';
		if($newpolygon!='') {
			$newpolygons[]=Array($newpolygon,$polygon_id,$polygon_type,$polygon_region);
		}
		$i++;
	}
}
$content.='</table><br>';

foreach ($newpolygons as $key => $row) {
	$allmasterssort[$key]  = strtolower($row[0]);
}
array_multisort($allmasterssort, SORT_ASC, $newpolygons);

for($i=0;$i<count($newpolygons);$i++) {
	if($newpolygons[$i][0]!=$newpolygons[$i-1][0]) {
		$content.=$newpolygons[$i][0].' - '.$newpolygons[$i][1].' - '.$newpolygons[$i][2].' - '.$newpolygons[$i][3].'<br>';
		$city=$newpolygons[$i][3];
		$result=mysql_query("SELECT * FROM ".$prefix."geography WHERE parent=".$city." order by RAND() limit 1");
		$a = mysql_fetch_array($result);
		$city=$a["id"];
		mysql_query("INSERT into ".$prefix."areas (tipe,name,city,date,tomoderate,kogdaigra_id) VALUES (".$newpolygons[$i][2].",'".$newpolygons[$i][0]."',".$city.",".time().",'1',".$newpolygons[$i][1].")");
	}
}

print($content);
# Вывод основного содержания страницы

stop_mysql();
# Разрыв соединения с MySQL-сервером

function utf8_to_cp1251($s) {
    return iconv("utf-8","windows-1251",$s);
}

?>