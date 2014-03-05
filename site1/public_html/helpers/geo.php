<?php
include_once("../db.inc");

$input = encode($_GET['input']);
$all = encode($_GET['all']);

if(isset($input) && $input!='')
{
	start_mysql();
	# Установление соединения с MySQL-сервером

	if($input!='')
	{
		if($all==1) {
			$result=mysql_query("SELECT * FROM ".$prefix."geography WHERE parent=".$input." order by name");
		}
		else {
			$result=mysql_query("SELECT * FROM ".$prefix."geography WHERE parent=".$input." and parent!=2562 and id!=2562 order by name");
		}
		while($a = mysql_fetch_array($result))
		{
			$return_arr[]=Array('id'=>$a["id"],'value'=>$a["name"]);
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

function printOutcity($id)
{
	global
		$prefix;

	$result=mysql_query("SELECT * FROM ".$prefix."geography WHERE id=".$id);
	while($a = mysql_fetch_array($result))
	{
		if($a["parent2"]>0)
		{
			$result2=mysql_query("SELECT * FROM ".$prefix."objects WHERE id=".$a["parent2"]);
			$b = mysql_fetch_array($result2);
			$content.=decode($a["name"]).' ('.decode($b["name"]).')';
		}
		else
		{
			$content.=decode($a["name"]);
		}
	}

	return ($content);
}
?>