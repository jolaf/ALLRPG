<?php
include_once("db.inc");
include_once("classes_objects_allrpg.php");
//error_reporting(E_ALL);

start_mysql();
# Установление соединения с MySQL-сервером

$content='';

$from=encode($_GET["from"]);
if($from=='' || !is_Numeric($from)) {
	$from=1500;
}
$to=encode($_GET["to"]);
if($to=='' || !is_Numeric($to)) {
	$to=2000;
}

$content.='<table border=1 style="border: 1px solid black; border-collapse: collapse;"><tr style="font-weight: bold;"><td>№</td><td>kogda-igra</td><td>allrpg</td><td>регион</td><td>полигон</td><td>тип</td><td>МГ</td><td>сайт</td><td>дата начала</td><td>дата окончания</td><td>участников</td><td>отменена</td></tr>';
$i=1;
$types[1]=15;
$types[2]=2;
$types[3]=14;
$types[4]=14;
$types[5]=4;
$types[6]=33;
$types[7]=34;
$types[8]=2;
$types[9]=2;
$types[10]=34;
$inserts=0;
$updates=0;
$deletes=0;
for($id=$from;$id<=$to;$id++) {
	$stres=implode(file("http://kogda-igra.ru/api/game/".$id));
	unset($info);
	$info=json_decode($stres,true);

	$name=$info["name"];
	$region=$info["sub_region_name"];
	$gametype=$info["game_type_name"];
	$polygon=$info["polygon_name"];
	$mg=$info["mg"];

//	$name=utf8_to_cp1251($info["name"]);
//	$region=utf8_to_cp1251($info["sub_region_name"]);
//	$gametype=utf8_to_cp1251($info["game_type_name"]);
//	$polygon=utf8_to_cp	($info["polygon_name"]);
//	$mg=utf8_to_cp1251($info["mg"]);

	unset($allrpg_id);
	$allrpg_id=$info["allrpg_info_id"];
	if(($name!='' || $allrpg_id>0) && $info["deleted_flag"]!='1') {
		$query='';
		$theid=0;
		$datearrival='';
		$result=mysql_query("SELECT * FROM ".$prefix."allgames WHERE parent=0 and kogdaigra_id=".$id);
		$a = mysql_fetch_array($result);
		if($allrpg_id>0 && $a["id"]=='') {
			$result=mysql_query("SELECT * FROM ".$prefix."allgames WHERE parent=0 and id=".$allrpg_id);
		}
		elseif($a["id"]=='') {
			//$result=mysql_query("SELECT * FROM ".$prefix."allgames WHERE parent=0 and ((LOWER(name) LIKE '%".strtolower(encode($name))."%' and datestart='".$info["begin"]."') OR LOWER(name)='".strtolower(encode($name))."')");
			$result=mysql_query("SELECT * FROM ".$prefix."allgames WHERE parent=0 and LOWER(name)='".strtolower(encode($name))."' and datestart='".$info["begin"]."'");
		}
		if($a["id"]=='') {
			$a = mysql_fetch_array($result);
		}
		$content.='<tr><td>'.$i.'</td>';
		$i++;
		$content.='<td><a href="http://kogda-igra.ru/game/'.$id.'">'.$name.'</a></td><td>';
		if($a["id"]!='') {
			$updates++;
			$content.='<a href="http://inf.allrpg.info/events/'.$a["id"].'/">'.decode($a["name"]).'</a>';
			$query="UPDATE allgames SET kogdaigra_id=".$id.", name='".encode($name)."', ";
			$theid=$a["id"];
			$datearrival=$a["datearrival"];
		}
		else {
			$inserts++;
			$content.='<font color="red">соответствия не найдено</font>';
			$query="INSERT into allgames (kogdaigra_id,parent,name,region,area,gametype2,gametype3,mg,site,datestart,datefinish,datearrival,playernum,date,master,sid,wascancelled,moved) VALUES (".$id.",0,'".encode($name)."',";
		}
		$content.='</td><td>';
		$result=mysql_query("SELECT * FROM ".$prefix."geography WHERE name LIKE '%".$region."%'");
		$a = mysql_fetch_array($result);
		if($a["id"]!='') {
			$content.=$a["id"];
			if($region=="") {
				$a["id"]=2563;
			}
			if($theid>0) {
				$query.='region='.$a["id"].',';
			}
			else {
				$query.=$a["id"].',';
			}
		}
		elseif($region=="Пермский край") {
			$content.='761';
			if($theid>0) {
				$query.='region=761,';
			}
			else {
				$query.=$a["id"].',';
			}
		}
		else {
			$content.='<font color="red">'.$region.'</font>';
			if($theid>0) {
				$query.='';
			}
			else {
				$query.='0,';
			}
		}
		$content.='</td><td>';
		$result=mysql_query("SELECT * FROM ".$prefix."areas WHERE name='".$polygon."' OR kogdaigra_id=".$info["polygon"]);
		$a = mysql_fetch_array($result);
		if($a["id"]!='') {
			$content.=$a["id"];
			if($theid>0) {
				$query.='area='.$a["id"].',';
			}
			else {
				$query.=$a["id"].',';
			}
		}
		elseif($polygon=="Выбран" || $polygon=="Неизвестен") {
			$content.='110';
			if($theid>0) {
				$query.='area=110,';
			}
			else {
				$query.='110,';
			}
		}
		else {
			$content.='<font color="red">'.$polygon.' (110)</font>';
			if($theid>0) {
				$query.='area=110,';
			}
			else {
				$query.='110,';
			}
		}
		$content.='</td><td>';
		if($types[$info["type"]]!='') {
			$content.=$types[$info["type"]];
			if($theid>0) {
				$query.="gametype2='-".$types[$info["type"]]."-',";
			}
			else {
				$query.="'-".$types[$info["type"]]."-',";
			}
		}
		else {
			$content.='<font color="red">'.$info["type"].'</font>';
			if($theid>0) {
				$query.="gametype2='-0-',";
			}
			else {
				$query.="'-0-',";
			}
		}
		if($theid==0) {
			$query.="67,";
		}
		$content.='</td><td>';
		$mg=str_replace('«','',$mg);
		$mg=str_replace('»','',$mg);
		$content.=$mg;
		if($theid>0) {
			$query.="mg='".encode($mg)."',";
		}
		else {
			$query.="'".encode($mg)."',";
		}
		$content.='</td><td>';
		$content.=$info["uri"];
		if($info["uri"]!='') {
			if($theid>0) {
				$query.="site='".encode($info["uri"])."',";
//				$query.="site='".iconv('UTF-8','windows-1251',encode($info["uri"]))."',";
			}
			else {
				$query.="'".encode($info["uri"])."',";
//				$query.="'".iconv('UTF-8','windows-1251',encode($info["uri"]))."',";
			}
		}
		elseif($theid==0) {
			$query.="'',";
		}
		$content.='</td><td>';
		$content.=$info["begin"];
		if($theid>0) {
			$query.="datestart='".$info["begin"]."',";
		}
		else {
			$query.="'".$info["begin"]."',";
		}
		$content.='</td><td>';
		$content.=date("Y-m-d",strtotime($info["begin"])+60*60*24*($info["time"]-1));
		if($theid>0) {
			$query.="datefinish='".date("Y-m-d",strtotime($info["begin"])+60*60*24*($info["time"]-1))."',";
			if(strtotime($datearrival)>strtotime($info["begin"]) || strtotime($datearrival)<strtotime($info["begin"])-(60*60*24*7)) {
				$query.="datearrival='".$info["begin"]."',";
			}
		}
		else {
			$query.="'".date("Y-m-d",strtotime($info["begin"])+60*60*24*($info["time"]-1))."','".$info["begin"]."',";
		}
		$content.='</td><td>';
		$content.=$info["players_count"];
		if($theid>0) {
			if($info["players_count"]!='') {
				$query.="playernum='".$info["players_count"]."',";
			}
			$query.="date=".time();
		}
		else {
			$query.="'".$info["players_count"]."',".time().",'{menu}',0,";
		}
		$content.='</td><td>';
		if($info["status"]==5) {
			$content.='отменена';
			if($theid>0) {
				$query.=",wascancelled='1'";
			}
			else {
				$query.="'1','0')";
			}
		}
		elseif($info["status"]==3) {
			$content.='отложена';
			if($theid>0) {
				$query.=",moved='1'";
			}
			else {
				$query.="'0','1')";
			}
		}
		elseif($theid==0) {
			$query.="'0','0')";
		}
		if($theid>0) {
			$query.=" WHERE id=".$theid;
		}
		mysql_query($query);
		$content.='</td></tr><tr><td colspan=12>'.$query;
        if(mysql_affected_rows($link)>0) {
        	$content.=' <font color="green">ОК</font>';
        }
        else {
        	$content.=' <font color="red">NOT ОК</font>';
        }
		$content.='</td></tr>';
	}
	elseif($info["deleted_flag"]=='1') {
		if($allrpg_id>0) {
        	mysql_query("DELETE FROM ".$prefix."allgames WHERE parent=0 and id=".$allrpg_id);
		}
		else {
			mysql_query("DELETE FROM ".$prefix."allgames WHERE parent=0 and kogdaigra_id=".$id);
		}
		if(mysql_affected_rows($link)>0) {
			$deletes++;
		}
	}
}
$content.='</table><br>
Updates: '.$updates.'<br>
Inserts: '.$inserts.'<br>
Deletes: '.$deletes;

print($content);
# Вывод основного содержания страницы

stop_mysql();
# Разрыв соединения с MySQL-сервером

function utf8_to_cp1251($s) {
    return iconv("utf-8","windows-1251",$s);
}

?>