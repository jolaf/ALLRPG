<?php
include_once("../db.inc");
include_once("../classes_objects_allrpg.php");

//$value = iconv('utf-8','cp1251',encode($_REQUEST['value']));
$value = encode($_REQUEST['value']);

header('Access-Control-Allow-Origin: *');

if(isset($value) && $value!='')
{
	start_mysql();
	# Установление соединения с MySQL-сервером

	if($value!='')
	{
		$result=mysql_query("SELECT * FROM ".$prefix."roleslinks WHERE id=".$value);
		$a=mysql_fetch_array($result);
		$values=substr($a["vacancies"],1,strlen($a["vacancies"])-2);
		$values=explode('-',$values);
		$i=0;
		foreach($values as $v) {
			$return_arr[$i]['id']='all'.$v;
	  		if($v!=0) {
		  		$result2=mysql_query("SELECT * FROM ".$prefix."rolevacancy WHERE id=".$v);
		  		$b=mysql_fetch_array($result2);
		  		$return_arr[$i]['value']='все принятые на роль «'.decode3($b["name"]).'» заявки';
		  		$i++;
				$result2=mysql_query("SELECT * FROM ".$prefix."roles WHERE vacancy=".$v." AND todelete2!=1 ORDER BY sorter asc");
				while($b=mysql_fetch_array($result2)) {
		  			$return_arr[$i]['id']=$b["id"];
					$result3=mysql_query("SELECT * FROM ".$prefix."users WHERE id=".$b["player_id"]);
					$c=mysql_fetch_array($result3);
					$return_arr[$i]['value']=str_replace('&#39','`',decode3($b["sorter"]).' ('.decode3(usname($c,true)).')');
		  			$i++;
				}
			}
			else {
				$return_arr[$i]['value']='<i>глобальный сюжет</i>';
				$i++;
			}
		}
	}

//	foreach($return_arr as $key=>$val) {
//		$return_arr[$key]['value'] = iconv('cp1251','utf-8',$return_arr[$key]['value']);
//	}

	print(json_encode($return_arr));
	# Вывод основного содержания страницы

	stop_mysql();
	# Разрыв соединения с MySQL-сервером
}
?>