<?php
session_start();
include_once("../db.inc");
//$value = iconv('utf-8','cp1251',encode($_GET['term']));
$value = encode($_GET['term']);

if(isset($value)) {
	start_mysql();

	$i=0;

	$value=str_replace(array(':',',','.','-'),'',$value);

	$result=mysql_query("SELECT * FROM ".$prefix."allgames WHERE name LIKE '%".$value."%' and parent=0");
	while($a = mysql_fetch_array($result)) {
		if(!in_array(decode($a["name"]),$return_arr)) {
			$return_arr[]=decode($a["name"]);
		}
	}

	$result=mysql_query("SELECT * FROM ".$prefix."sites where testing!='1' and title LIKE '%".$value."%' order by title asc");
	while($a = mysql_fetch_array($result)) {
		if(!in_array(decode($a["title"]),$return_arr)) {
			$return_arr[]=decode($a["title"]);
		}
	}

	$result=mysql_query("SELECT * FROM ".$prefix."users WHERE nick LIKE '%".$value."%' and hidesome NOT LIKE '%-0-%'");
	while($a = mysql_fetch_array($result)) {
		if(!in_array(decode($a["nick"]),$return_arr)) {
			$return_arr[]=decode($a["nick"]);
		}
	}
	$result=mysql_query("SELECT * FROM ".$prefix."users WHERE fio LIKE '%".$value."%' and hidesome NOT LIKE '%-10-%'");
	while($a = mysql_fetch_array($result)) {
		if(!in_array(decode($a["fio"]),$return_arr)) {
			$return_arr[]=decode($a["fio"]);
		}
	}

	$result=mysql_query("SELECT * FROM ".$prefix."users WHERE ingroup LIKE '%".$value."%'");
	while($a = mysql_fetch_array($result)) {
		$should=true;
		$rightmg='';
		$hisgroups=explode(',',$a["ingroup"]);
		for($j=0;$j<count($hisgroups);$j++) {
			if(substr($hisgroups[$j],0,1)==' ') {
				$hisgroups[$j]=substr($hisgroups[$j],1,strlen($hisgroups[$j]));
			}
			if(stripos($hisgroups[$j],$value)!==false) {
				$rightmg=$hisgroups[$j];
			}
			$rightmg=str_ireplace('&quot;','',$rightmg);
			$rightmg=str_ireplace('МГ ','',$rightmg);
			$rightmg=str_ireplace('МО ','',$rightmg);
			$rightmg=str_ireplace('ТГ ','',$rightmg);
			$rightmg=str_ireplace('ТО ','',$rightmg);
			$rightmg=str_ireplace('ТК ','',$rightmg);
			$rightmg=str_ireplace('ТМ ','',$rightmg);
		}
		if($rightmg!='') {
			for($j=0;$j<count($foss);$j++)
			{
				if(strtolower($foss[$j])==strtolower($rightmg))
				{
					$should=false;
				}
			}
			if($should) {
				if(!in_array(decode($rightmg),$return_arr)) {
					$return_arr[]=decode($rightmg);
				}
				$foss[]=$rightmg;
			}
		}
	}

	$result=mysql_query("SELECT * FROM ".$prefix."allgames WHERE mg LIKE '%".$value."%'");
	while($a = mysql_fetch_array($result)) {
		$should=true;
		$rightmg='';
		$hisgroups=explode(',',$a["mg"]);
		for($j=0;$j<count($hisgroups);$j++) {
			if(substr($hisgroups[$j],0,1)==' ') {
				$hisgroups[$j]=substr($hisgroups[$j],1,strlen($hisgroups[$j]));
			}
			if(stripos($hisgroups[$j],$value)!==false) {
				$rightmg=$hisgroups[$j];
			}
			$rightmg=str_ireplace('&quot;','',$rightmg);
			$rightmg=str_ireplace('МГ ','',$rightmg);
			$rightmg=str_ireplace('МО ','',$rightmg);
			$rightmg=str_ireplace('ТГ ','',$rightmg);
			$rightmg=str_ireplace('ТО ','',$rightmg);
			$rightmg=str_ireplace('ТК ','',$rightmg);
			$rightmg=str_ireplace('ТМ ','',$rightmg);
		}
		if($rightmg!='') {
			for($j=0;$j<count($foss);$j++)
			{
				if(strtolower($foss[$j])==strtolower($rightmg))
				{
					$should=false;
				}
			}
			if($should) {
				if(!in_array(decode($rightmg),$return_arr)) {
					$return_arr[]=decode($rightmg);
				}
				$foss[]=$rightmg;
			}
		}
	}

	$result=mysql_query("SELECT * FROM ".$prefix."areas WHERE name LIKE '%".$value."%'");
	while($a = mysql_fetch_array($result)) {
		if(!in_array(decode($a["name"]),$return_arr)) {
			$return_arr[]=decode($a["name"]);
		}
	}

	$result=mysql_query("SELECT * FROM ".$prefix."reports WHERE name LIKE '%".$value."%'");
	while($a = mysql_fetch_array($result)) {
		if(!in_array(decode($a["name"]),$return_arr)) {
			$return_arr[]=decode($a["name"]);
		}
	}

	$result=mysql_query("SELECT * FROM ".$prefix."articles WHERE name LIKE '%".$value."%' order by date desc");
	while($a = mysql_fetch_array($result)) {
		if(!in_array(decode($a["name"]),$return_arr)) {
			$return_arr[]=decode($a["name"]);
		}
	}

//	foreach($return_arr as $key=>$val) {
//		$return_arr[$key] = iconv('cp1251','utf-8',$val);
//	}
	sort($return_arr);

	header('Access-Control-Allow-Origin: *');
	print(json_encode($return_arr));
	# Вывод основного содержания страницы

	stop_mysql();
	# Разрыв соединения с MySQL-сервером
}
?>