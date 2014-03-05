<?php
include_once("..db.inc");

$input = encode($_GET['input']);
$moreparams = encode($_GET['moreparams']);
$moreparams = str_replace("&#39","'",$moreparams);

if(isset($input) && $input!='')
{
	session_start();

	start_mysql();
	# Установление соединения с MySQL-сервером

	$i=0;
	if($input!='')
	{
		$result=mysql_query("SELECT * FROM ".$prefix."allgames WHERE ((datestart <= '".$input."' AND datefinish >= '".$input."') OR (datestart <= '".$input."' AND datefinish >= '".$input."') OR (datestart > '".$input."' AND datefinish < '".$input."')) AND parent=0 ".$moreparams." order by name");
		while($a = mysql_fetch_array($result))
		{
			$result2=mysql_query("SELECT * FROM ".$prefix."played WHERE game=".$a["id"]." and user_id=".$_SESSION["user_id"]);
			$b = mysql_fetch_array($result2);
			$content.='<first>'.$a["id"].'</first>';
			if($b["specializ2"]!='' && $b["specializ2"]!='-')
			{
				$content.='<second> style="color: #0000CC"</second>';
				$i++;
			}
			elseif($b["specializ3"]!='' && $b["specializ3"]!='-')
			{
				$content.='<second> style="color: #009900"</second>';
				$i++;
			}
			elseif($b["specializ"]!='' && $b["specializ"]!='-')
			{
				$content.='<second> style="color: #CC0000"</second>';
				$i++;
			}
			elseif($b["id"]!='')
			{
				$content.='<second> style="color: #CC0000"</second>';
				$i++;
			}
			else
			{
				$content.='<second> style="color: #3A3A3A"</second>';
				$i++;
			}
			$content.='<third>'.decode3($a["name"]).'</third>';
			$content.='<four>game</four>';
		}

		if($_SESSION["user_id"]!='') {
			$result=mysql_query("SELECT * FROM ".$prefix."played WHERE ((datestart <= '".$input."' AND datefinish >= '".$input."') OR (datestart <= '".$input."' AND datefinish >= '".$input."') OR (datestart > '".$input."' AND datefinish < '".$input."')) AND event!='' and game=0 and user_id=".$_SESSION["user_id"]." order by event asc");
			while($a = mysql_fetch_array($result))
			{
				$content.='<first>'.$a["id"].'</first>';
				$content.='<second> style="color: #2375AF"</second>';
				$i++;
				$content.='<third>'.$a["event"].'</third>';
				$content.='<four>event</four>';
			}
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
?>