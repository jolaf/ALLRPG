<?php
include_once("../db.inc");
include_once("../classes_objects_allrpg.php");

$input = encode($_GET['input']);
$futurepast = encode($_GET['futurepast']);

if(isset($input) && $input!='')
{
	start_mysql();
	# Установление соединения с MySQL-сервером

	if($input!='') {
		if($futurepast==1) {
			$result=mysql_query("SELECT * FROM ".$prefix."allgames WHERE master='{menu}' and datestart>='".date("Y-m-d")."' and name LIKE \"%".$input."%\" order by name");
		}
		elseif($futurepast==2) {
			$result=mysql_query("SELECT * FROM ".$prefix."allgames WHERE master='{menu}' and datestart<'".date("Y-m-d")."' and name LIKE \"%".$input."%\" order by name");
		}
		elseif($futurepast==0) {
			$result=mysql_query("SELECT * FROM ".$prefix."allgames WHERE master='{menu}' and name LIKE \"%".$input."%\" order by name");
		}
		while($a = mysql_fetch_array($result)) {
			$return_arr[]=Array('id'=>$a["id"],'value'=>decode3($a["name"]).' ('.preg_replace('#&nbsp;#',' ',datesfmake($a["datestart"],$a["datefinish"])).')');
			$i++;
		}
	}

//	foreach($return_arr as $key=>$val) {
//		$return_arr[$key]['value'] = iconv('cp1251','utf-8',$return_arr[$key]['value']);
//	}

    header('Access-Control-Allow-Origin: *');
	print(json_encode($return_arr));
	# Вывод основного содержания страницы

	stop_mysql();
	# Разрыв соединения с MySQL-сервером
}

function printOutgame($id)
{
	$result=mysql_query("SELECT * FROM ".$prefix."allgames WHERE id=".$id);
	while($a = mysql_fetch_array($result)) {
		$content.=decode($a["name"]);
	}

	return ($content);
}
?>