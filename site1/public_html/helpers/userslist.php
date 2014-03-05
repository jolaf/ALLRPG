<?php
include_once("../db.inc");
include_once("../classes_objects_allrpg.php");

$input = encode($_GET['input']);

if(isset($input) && $input!='')
{
	start_mysql();
	# Установление соединения с MySQL-сервером

	if($input!='') {
		if(is_numeric($input)) {
			$result=mysql_query("SELECT * FROM ".$prefix."users WHERE sid=".$input);
		}
		else {
			$result=mysql_query("SELECT * FROM ".$prefix."users WHERE (fio LIKE '%".$input."%' and hidesome NOT LIKE '%-10-%') OR (nick LIKE '%".$input."%' and hidesome NOT LIKE '%-0-%')");
		}
		while($a = mysql_fetch_array($result)) {
			$sidadd='';
			if((!preg_match('#-10-#',$a["hidesome"]) && decode($a["fio"])!='') || (!preg_match('#-0-#',$a["hidesome"]) && decode($a["nick"])!='')) {
				$sidadd=' (ИНП '.$a["sid"].')';
			}
			$allusers[]=Array($a["sid"],(usname($a,true)).$sidadd);
		}
        foreach ($allusers as $key => $row) {
			$allusers_sort[$key]  = strtolower($row[1]);
		}
		array_multisort($allusers_sort, SORT_ASC, $allusers);
		for($j=0;$j<count($allusers);$j++) {
			$return_arr[]=Array('id'=>$allusers[$j][0],'value'=>$allusers[$j][1]);
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

function printOutuser_id($id)
{
	$result=mysql_query("SELECT * FROM ".$prefix."users WHERE id=".$id);
	while($a = mysql_fetch_array($result)) {
		$sidadd='';
		if((!preg_match('#-10-#',$a["hidesome"]) && decode($a["fio"])!='') || (!preg_match('#-0-#',$a["hidesome"]) && decode($a["nick"])!='')) {
			$sidadd=' (ИНП '.$a["sid"].')';
		}
		$content.=(usname($a,true)).$sidadd;
	}

	return ($content);
}
?>