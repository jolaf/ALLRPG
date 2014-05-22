<?php
	$path='../../site1/public_html/';
	
include_once("$path/db.inc");
include_once("$path/classes_objects_allrpg.php");
require_once("$path/appcode/data/common.php");

$datestart = encode($_GET['datestart']);
$datefinish = encode($_GET['datefinish']);
$game_id = encode($_GET['game_id']);
$open_list = encode($_GET['open_list']);

start_mysql();
# Установление соединения с MySQL-сервером

if(isset($datestart) && $datestart!='' && isset($datefinish) && $datefinish!='')
{
  $datestart = mysql_real_escape_string($datestart);
  $datefinish = mysql_real_escape_string($datefinish);
	$result=db_query("SELECT id, name, kogdaigra_id FROM {$prefix}allgames WHERE ((datestart BETWEEN '$datestart' AND '$datefinish') OR (datefinish BETWEEN '$datestart' AND '$datefinish')) AND parent=0 order by name");
	$games = array();
	while($a = mysql_fetch_array($result)) {
		$games [] = array('allrpg_info_id' => $a['id'],  "allrpg_info_name" => str_replace('"','\\"',decode($a["name"])), 'kogdaigra_id' => $a['kogdaigra_id']);
	}
	$content = json_encode ($games);
}
elseif(isset($game_id) && $game_id!='') {
	$content='{';
	$result=mysql_query("SELECT * FROM ".$prefix."allgames WHERE id=".$game_id);
	$a = mysql_fetch_array($result);
	$result2=mysql_query("SELECT * FROM ".$prefix."users WHERE sid=".$a["sid"]);
	$b = mysql_fetch_array($result2);
	$content.='"info":{"name":"'.(str_replace('"','\\"',decode($a["name"]))).'","site":"'.(str_replace('"','\\"',decode($a["site"]))).'","mg":"'.(str_replace('"','\\"',decode($a["mg"]))).'","playernum":"'.(str_replace('"','\\"',decode($a["playernum"]))).'","datestart":"'.$a["datestart"].'","datefinish":"'.$a["datefinish"].'","datearrival":"'.$a["datearrival"].'","author_mail":"'.$b["em"].'","author_name":"'.(str_replace('"','\\"',usname($b))).'"},"masters":[';
	$result=mysql_query("SELECT * FROM ".$prefix."allgames WHERE parent=".$game_id." and user_id!=0 order by name");
	while($a = mysql_fetch_array($result)) {
		$result2=mysql_query("SELECT * FROM ".$prefix."users WHERE sid=".$a["user_id"]);
		$b = mysql_fetch_array($result2);
		$content.='{"email":"'.decode($b["em"]).'","name":"'.(str_replace('"','\\"',decode(usname($b,true,false)))).'","duty":[';
        $master='';
        $result2=mysql_query("SELECT * FROM ".$prefix."specializ WHERE gr=2 OR gr=3");
		while($b = mysql_fetch_array($result2)) {
         	if(strpos($a["master"],"-".$b["id"]."-")!==false) {
         		$master.='"'.(str_replace('"','\\"',decode($b["name"]))).'",';
         	}
		}
		if(strlen($master)>1) {
			$master=substr($master,0,strlen($master)-1);
			$content.=$master;
		}
		$content.=']},';
	}
	if(strlen($master)>1) {
		$content=substr($content,0,strlen($content)-1);
	}
	$content.=']}';
}

elseif(isset($open_list) && $open_list!='') {
	$content='[';
	$result=mysql_query("SELECT * FROM ".$prefix."sites where status2=2 and testing!='1' and datefinish>='".date("Y-m-d")."' order by title asc");
	while($a = mysql_fetch_array($result)) {
		$content.='{"allrpg_id":"'.$a["id"].'","name":"'.(str_replace('"','\\"',decode($a["title"]))).'"},';
	}
	if(strlen($content)>1) {
		$content=substr($content,0,strlen($content)-1);
	}
	$content.=']';
}

  header('Access-Control-Allow-Origin: *');
	header("Content-Type: text/html;charset=UTF-8");

	print($content);
	# Вывод основного содержания страницы

	stop_mysql();
	# Разрыв соединения с MySQL-сервером
?>